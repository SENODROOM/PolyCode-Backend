/*
 * File: websocket_server.c
 * Description: Simple WebSocket server implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <time.h>
#include <openssl/sha.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>

#define PORT 8080
#define BUFFER_SIZE 4096
#define WS_MAGIC_STRING "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"

// WebSocket frame structure
typedef struct {
    unsigned char fin : 1;
    unsigned char rsv1 : 1;
    unsigned char rsv2 : 1;
    unsigned char rsv3 : 1;
    unsigned char opcode : 4;
    unsigned char masked : 1;
    unsigned char payload_len : 7;
    unsigned char extended_len[8];
    unsigned char masking_key[4];
    char* payload;
} WebSocketFrame;

// Client connection structure
typedef struct {
    int socket;
    char* key;
    int connected;
} WSClient;

// Base64 encoding
char* base64_encode(const unsigned char* data, size_t input_length) {
    BIO *b64, *bmem;
    BUF_MEM *bptr;
    
    b64 = BIO_new(BIO_f_base64());
    bmem = BIO_new(BIO_s_mem());
    b64 = BIO_push(b64, bmem);
    
    BIO_write(b64, data, input_length);
    BIO_flush(b64);
    
    BIO_get_mem_ptr(b64, &bptr);
    
    char* result = malloc(bptr->length);
    memcpy(result, bptr->data, bptr->length - 1);
    result[bptr->length - 1] = '\0';
    
    BIO_free_all(b64);
    
    return result;
}

// Generate WebSocket accept key
char* generate_accept_key(const char* client_key) {
    char concatenated[256];
    snprintf(concatenated, sizeof(concatenated), "%s%s", client_key, WS_MAGIC_STRING);
    
    unsigned char hash[SHA_DIGEST_LENGTH];
    SHA1((unsigned char*)concatenated, strlen(concatenated), hash);
    
    return base64_encode(hash, SHA_DIGEST_LENGTH);
}

// Parse WebSocket handshake
int parse_handshake(const char* request, char* sec_key, char* protocols) {
    char* key_start = strstr(request, "Sec-WebSocket-Key:");
    if (key_start) {
        key_start += 19; // Skip "Sec-WebSocket-Key:"
        while (*key_start == ' ') key_start++;
        
        char* key_end = strstr(key_start, "\r\n");
        if (key_end) {
            int key_len = key_end - key_start;
            strncpy(sec_key, key_start, key_len);
            sec_key[key_len] = '\0';
            return 1;
        }
    }
    return 0;
}

// Send WebSocket handshake response
void send_handshake_response(int client_socket, const char* accept_key) {
    char response[1024];
    snprintf(response, sizeof(response),
             "HTTP/1.1 101 Switching Protocols\r\n"
             "Upgrade: websocket\r\n"
             "Connection: Upgrade\r\n"
             "Sec-WebSocket-Accept: %s\r\n"
             "\r\n",
             accept_key);
    
    write(client_socket, response, strlen(response));
}

// Parse WebSocket frame
int parse_frame(const unsigned char* data, int len, WebSocketFrame* frame) {
    if (len < 2) return -1;
    
    // Parse first byte
    frame->fin = (data[0] & 0x80) >> 7;
    frame->rsv1 = (data[0] & 0x40) >> 6;
    frame->rsv2 = (data[0] & 0x20) >> 5;
    frame->rsv3 = (data[0] & 0x10) >> 4;
    frame->opcode = data[0] & 0x0f;
    
    // Parse second byte
    frame->masked = (data[1] & 0x80) >> 7;
    frame->payload_len = data[1] & 0x7f;
    
    int header_len = 2;
    
    // Extended payload length
    if (frame->payload_len == 126) {
        if (len < header_len + 2) return -1;
        frame->payload_len = (data[2] << 8) | data[3];
        header_len += 2;
    } else if (frame->payload_len == 127) {
        if (len < header_len + 8) return -1;
        for (int i = 0; i < 8; i++) {
            frame->extended_len[i] = data[2 + i];
        }
        header_len += 8;
        // Convert to 64-bit integer (simplified)
        frame->payload_len = 0;
        for (int i = 0; i < 8; i++) {
            frame->payload_len = (frame->payload_len << 8) | data[2 + i];
        }
    }
    
    // Masking key
    if (frame->masked) {
        if (len < header_len + 4) return -1;
        for (int i = 0; i < 4; i++) {
            frame->masking_key[i] = data[header_len + i];
        }
        header_len += 4;
    }
    
    // Payload
    if (len < header_len + frame->payload_len) return -1;
    frame->payload = malloc(frame->payload_len + 1);
    memcpy(frame->payload, data + header_len, frame->payload_len);
    frame->payload[frame->payload_len] = '\0';
    
    // Unmask payload if needed
    if (frame->masked) {
        for (int i = 0; i < frame->payload_len; i++) {
            frame->payload[i] ^= frame->masking_key[i % 4];
        }
    }
    
    return header_len + frame->payload_len;
}

// Create WebSocket frame
int create_frame(WebSocketFrame* frame, const char* message, int opcode) {
    int message_len = strlen(message);
    frame->fin = 1;
    frame->rsv1 = frame->rsv2 = frame->rsv3 = 0;
    frame->opcode = opcode;
    frame->masked = 0;
    
    if (message_len < 126) {
        frame->payload_len = message_len;
    } else if (message_len < 65536) {
        frame->payload_len = 126;
    } else {
        frame->payload_len = 127;
    }
    
    frame->payload = malloc(message_len + 1);
    strcpy(frame->payload, message);
    
    return message_len;
}

// Send WebSocket frame
void send_frame(int client_socket, WebSocketFrame* frame) {
    unsigned char buffer[BUFFER_SIZE];
    int pos = 0;
    
    // First byte
    buffer[pos++] = (frame->fin << 7) | (frame->opcode & 0x0f);
    
    // Payload length
    int message_len = strlen(frame->payload);
    if (message_len < 126) {
        buffer[pos++] = message_len;
    } else if (message_len < 65536) {
        buffer[pos++] = 126;
        buffer[pos++] = (message_len >> 8) & 0xff;
        buffer[pos++] = message_len & 0xff;
    } else {
        buffer[pos++] = 127;
        for (int i = 7; i >= 0; i--) {
            buffer[pos++] = (message_len >> (i * 8)) & 0xff;
        }
    }
    
    // Payload
    memcpy(buffer + pos, frame->payload, message_len);
    pos += message_len;
    
    write(client_socket, buffer, pos);
}

// Handle WebSocket message
void handle_websocket_message(int client_socket, const char* message) {
    printf("Received WebSocket message: %s\n", message);
    
    // Echo back the message
    char response[1024];
    snprintf(response, sizeof(response), "Echo: %s", message);
    
    WebSocketFrame frame;
    create_frame(&frame, response, 0x1); // Text frame
    send_frame(client_socket, &frame);
    
    free(frame.payload);
}

// Handle WebSocket connection
void handle_websocket_connection(int client_socket) {
    char buffer[BUFFER_SIZE];
    int bytes_received = recv(client_socket, buffer, BUFFER_SIZE - 1, 0);
    
    if (bytes_received <= 0) {
        close(client_socket);
        return;
    }
    
    buffer[bytes_received] = '\0';
    printf("Received handshake:\n%s\n", buffer);
    
    // Parse handshake
    char sec_key[256] = {0};
    if (parse_handshake(buffer, sec_key, NULL)) {
        printf("WebSocket key: %s\n", sec_key);
        
        // Generate accept key
        char* accept_key = generate_accept_key(sec_key);
        printf("Accept key: %s\n", accept_key);
        
        // Send handshake response
        send_handshake_response(client_socket, accept_key);
        free(accept_key);
        
        // Handle WebSocket messages
        while (1) {
            bytes_received = recv(client_socket, buffer, BUFFER_SIZE - 1, 0);
            if (bytes_received <= 0) break;
            
            WebSocketFrame frame;
            int frame_len = parse_frame((unsigned char*)buffer, bytes_received, &frame);
            
            if (frame_len > 0) {
                if (frame.opcode == 0x1) { // Text frame
                    handle_websocket_message(client_socket, frame.payload);
                } else if (frame.opcode == 0x8) { // Close frame
                    printf("Client requested close\n");
                    free(frame.payload);
                    break;
                } else if (frame.opcode == 0x9) { // Ping frame
                    // Send pong frame
                    WebSocketFrame pong_frame;
                    create_frame(&pong_frame, frame.payload, 0xA); // Pong frame
                    send_frame(client_socket, &pong_frame);
                    free(pong_frame.payload);
                }
                
                free(frame.payload);
            }
        }
    }
    
    close(client_socket);
}

// Send periodic messages to all connected clients
void* broadcast_messages(void* arg) {
    int* client_sockets = (int*)arg;
    int client_count = 0;
    
    while (1) {
        sleep(5); // Send message every 5 seconds
        
        time_t now = time(NULL);
        char message[256];
        strftime(message, sizeof(message), "Server time: %Y-%m-%d %H:%M:%S", localtime(&now));
        
        for (int i = 0; i < client_count; i++) {
            if (client_sockets[i] > 0) {
                WebSocketFrame frame;
                create_frame(&frame, message, 0x1); // Text frame
                send_frame(client_sockets[i], &frame);
                free(frame.payload);
            }
        }
    }
    
    return NULL;
}

// WebSocket server
int main() {
    int server_socket, client_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    
    // Create socket
    server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        exit(EXIT_FAILURE);
    }
    
    // Set socket options
    int opt = 1;
    setsockopt(server_socket, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(PORT);
    
    // Bind socket to address
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        exit(EXIT_FAILURE);
    }
    
    // Listen for connections
    if (listen(server_socket, 5) < 0) {
        perror("Listen failed");
        close(server_socket);
        exit(EXIT_FAILURE);
    }
    
    printf("WebSocket Server started on port %d\n", PORT);
    printf("Connect using WebSocket client to: ws://localhost:%d\n", PORT);
    printf("Press Ctrl+C to stop the server\n");
    
    // Accept connections
    while (1) {
        client_socket = accept(server_socket, (struct sockaddr*)&client_addr, &client_len);
        if (client_socket < 0) {
            perror("Accept failed");
            continue;
        }
        
        printf("Connection from %s:%d\n", 
               inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port));
        
        // Handle WebSocket connection
        handle_websocket_connection(client_socket);
    }
    
    close(server_socket);
    return 0;
}
