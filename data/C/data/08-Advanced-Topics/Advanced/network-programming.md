# C Network Programming

## Socket Basics

### TCP Client
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>

int main() {
    int client_socket;
    struct sockaddr_in server_addr;
    char buffer[1024];
    
    // Create socket
    client_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (client_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(8080);
    server_addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    
    // Connect to server
    if (connect(client_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Connection failed");
        close(client_socket);
        return 1;
    }
    
    printf("Connected to server\n");
    
    // Send message
    char* message = "Hello, Server!";
    send(client_socket, message, strlen(message), 0);
    
    // Receive response
    int bytes_received = recv(client_socket, buffer, sizeof(buffer) - 1, 0);
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        printf("Server response: %s\n", buffer);
    }
    
    close(client_socket);
    return 0;
}
```

### TCP Server
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>

int main() {
    int server_socket, client_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    char buffer[1024];
    
    // Create socket
    server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    // Set socket option to reuse address
    int opt = 1;
    setsockopt(server_socket, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(8080);
    
    // Bind socket
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        return 1;
    }
    
    // Listen for connections
    if (listen(server_socket, 5) < 0) {
        perror("Listen failed");
        close(server_socket);
        return 1;
    }
    
    printf("Server listening on port 8080\n");
    
    // Accept connection
    client_socket = accept(server_socket, (struct sockaddr*)&client_addr, &client_len);
    if (client_socket < 0) {
        perror("Accept failed");
        close(server_socket);
        return 1;
    }
    
    printf("Client connected: %s:%d\n", 
           inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port));
    
    // Receive message
    int bytes_received = recv(client_socket, buffer, sizeof(buffer) - 1, 0);
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        printf("Client message: %s\n", buffer);
        
        // Send response
        char* response = "Hello, Client!";
        send(client_socket, response, strlen(response), 0);
    }
    
    close(client_socket);
    close(server_socket);
    return 0;
}
```

## UDP Programming

### UDP Client
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>

int main() {
    int client_socket;
    struct sockaddr_in server_addr;
    char buffer[1024];
    
    // Create UDP socket
    client_socket = socket(AF_INET, SOCK_DGRAM, 0);
    if (client_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(8080);
    server_addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    
    // Send message
    char* message = "Hello, UDP Server!";
    sendto(client_socket, message, strlen(message), 0,
           (struct sockaddr*)&server_addr, sizeof(server_addr));
    
    printf("Message sent to server\n");
    
    // Receive response
    socklen_t server_len = sizeof(server_addr);
    int bytes_received = recvfrom(client_socket, buffer, sizeof(buffer) - 1, 0,
                                  (struct sockaddr*)&server_addr, &server_len);
    
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        printf("Server response: %s\n", buffer);
    }
    
    close(client_socket);
    return 0;
}
```

### UDP Server
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>

int main() {
    int server_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    char buffer[1024];
    
    // Create UDP socket
    server_socket = socket(AF_INET, SOCK_DGRAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(8080);
    
    // Bind socket
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        return 1;
    }
    
    printf("UDP Server listening on port 8080\n");
    
    // Receive message
    int bytes_received = recvfrom(server_socket, buffer, sizeof(buffer) - 1, 0,
                                  (struct sockaddr*)&client_addr, &client_len);
    
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        printf("Received from %s:%d: %s\n",
               inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port), buffer);
        
        // Send response
        char* response = "Hello, UDP Client!";
        sendto(server_socket, response, strlen(response), 0,
               (struct sockaddr*)&client_addr, client_len);
    }
    
    close(server_socket);
    return 0;
}
```

## Non-blocking Sockets

### Non-blocking Server
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <fcntl.h>
#include <errno.h>

void set_nonblocking(int socket) {
    int flags = fcntl(socket, F_GETFL, 0);
    fcntl(socket, F_SETFL, flags | O_NONBLOCK);
}

int main() {
    int server_socket, client_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    char buffer[1024];
    
    server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    set_nonblocking(server_socket);
    
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(8080);
    
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        return 1;
    }
    
    if (listen(server_socket, 5) < 0) {
        perror("Listen failed");
        close(server_socket);
        return 1;
    }
    
    printf("Non-blocking server listening on port 8080\n");
    
    while (1) {
        // Accept connections (non-blocking)
        client_socket = accept(server_socket, (struct sockaddr*)&client_addr, &client_len);
        
        if (client_socket >= 0) {
            printf("Client connected: %s:%d\n",
                   inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port));
            
            set_nonblocking(client_socket);
            
            // Handle client communication
            while (1) {
                int bytes_received = recv(client_socket, buffer, sizeof(buffer) - 1, 0);
                
                if (bytes_received > 0) {
                    buffer[bytes_received] = '\0';
                    printf("Received: %s\n", buffer);
                    
                    char* response = "Message received";
                    send(client_socket, response, strlen(response), 0);
                } else if (bytes_received == 0) {
                    printf("Client disconnected\n");
                    close(client_socket);
                    break;
                } else if (errno != EAGAIN && errno != EWOULDBLOCK) {
                    perror("Receive error");
                    close(client_socket);
                    break;
                }
                
                usleep(10000); // Small delay to prevent busy waiting
            }
        } else if (errno != EAGAIN && errno != EWOULDBLOCK) {
            perror("Accept error");
            break;
        }
        
        usleep(10000); // Small delay
    }
    
    close(server_socket);
    return 0;
}
```

## Select System Call

### Using Select for Multiple Clients
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <fcntl.h>

#define MAX_CLIENTS 10

int main() {
    int server_socket, client_sockets[MAX_CLIENTS], max_sd, activity, new_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    char buffer[1024];
    fd_set readfds;
    
    // Initialize client sockets
    for (int i = 0; i < MAX_CLIENTS; i++) {
        client_sockets[i] = 0;
    }
    
    server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(8080);
    
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        return 1;
    }
    
    if (listen(server_socket, 5) < 0) {
        perror("Listen failed");
        close(server_socket);
        return 1;
    }
    
    printf("Select-based server listening on port 8080\n");
    
    while (1) {
        // Clear socket set
        FD_ZERO(&readfds);
        
        // Add server socket to set
        FD_SET(server_socket, &readfds);
        max_sd = server_socket;
        
        // Add client sockets to set
        for (int i = 0; i < MAX_CLIENTS; i++) {
            if (client_sockets[i] > 0) {
                FD_SET(client_sockets[i], &readfds);
            }
            if (client_sockets[i] > max_sd) {
                max_sd = client_sockets[i];
            }
        }
        
        // Wait for activity
        activity = select(max_sd + 1, &readfds, NULL, NULL, NULL);
        
        if (activity < 0) {
            perror("Select error");
            break;
        }
        
        // Check for new connections
        if (FD_ISSET(server_socket, &readfds)) {
            new_socket = accept(server_socket, (struct sockaddr*)&client_addr, &client_len);
            if (new_socket < 0) {
                perror("Accept failed");
                continue;
            }
            
            printf("New connection: %s:%d\n",
                   inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port));
            
            // Add new socket to array
            for (int i = 0; i < MAX_CLIENTS; i++) {
                if (client_sockets[i] == 0) {
                    client_sockets[i] = new_socket;
                    break;
                }
            }
        }
        
        // Check for client activity
        for (int i = 0; i < MAX_CLIENTS; i++) {
            int sd = client_sockets[i];
            
            if (FD_ISSET(sd, &readfds)) {
                int bytes_received = recv(sd, buffer, sizeof(buffer) - 1, 0);
                
                if (bytes_received > 0) {
                    buffer[bytes_received] = '\0';
                    printf("Client %d: %s\n", i, buffer);
                    
                    // Echo back to client
                    send(sd, buffer, bytes_received, 0);
                } else {
                    printf("Client %d disconnected\n", i);
                    close(sd);
                    client_sockets[i] = 0;
                }
            }
        }
    }
    
    close(server_socket);
    return 0;
}
```

## HTTP Client

### Simple HTTP GET Request
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <netdb.h>

int main() {
    int client_socket;
    struct sockaddr_in server_addr;
    struct hostent *server;
    char buffer[4096];
    char *hostname = "www.example.com";
    char *request = "GET / HTTP/1.1\r\nHost: www.example.com\r\nConnection: close\r\n\r\n";
    
    // Resolve hostname
    server = gethostbyname(hostname);
    if (server == NULL) {
        fprintf(stderr, "ERROR, no such host\n");
        return 1;
    }
    
    // Create socket
    client_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (client_socket < 0) {
        perror("Socket creation failed");
        return 1;
    }
    
    // Configure server address
    memset(&server_addr, 0, sizeof(server_addr));
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(80);
    memcpy(&server_addr.sin_addr.s_addr, server->h_addr, server->h_length);
    
    // Connect to server
    if (connect(client_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Connection failed");
        close(client_socket);
        return 1;
    }
    
    // Send HTTP request
    if (send(client_socket, request, strlen(request), 0) < 0) {
        perror("Send failed");
        close(client_socket);
        return 1;
    }
    
    // Receive response
    printf("HTTP Response:\n");
    printf("=============\n");
    
    int bytes_received;
    while ((bytes_received = recv(client_socket, buffer, sizeof(buffer) - 1, 0)) > 0) {
        buffer[bytes_received] = '\0';
        printf("%s", buffer);
    }
    
    if (bytes_received < 0) {
        perror("Receive failed");
    }
    
    printf("\n=============\n");
    
    close(client_socket);
    return 0;
}
```

## Best Practices
- Always check return values of socket functions
- Handle network errors gracefully
- Use proper buffer sizes and bounds checking
- Implement timeout mechanisms
- Use select/poll for handling multiple connections
- Close sockets properly to avoid resource leaks
- Consider using non-blocking I/O for better performance
- Implement proper error handling and logging
- Use appropriate socket options
- Be aware of network byte order (htons, ntohs)
- Handle partial sends and receives
- Consider using libraries like libcurl for complex protocols
- Implement proper security measures (SSL/TLS)
- Test on different network conditions
- Consider IPv6 compatibility
