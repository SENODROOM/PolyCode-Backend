/*
 * File: ftp_client.c
 * Description: Simple FTP client implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <sys/stat.h>
#include <fcntl.h>

#define FTP_PORT 21
#define BUFFER_SIZE 1024

// FTP response structure
typedef struct {
    int code;
    char message[BUFFER_SIZE];
} FTPResponse;

// FTP connection structure
typedef struct {
    int control_socket;
    int data_socket;
    int passive_mode;
    char server_ip[16];
    int server_port;
} FTPConnection;

// Connect to FTP server
int ftp_connect(FTPConnection* ftp, const char* hostname) {
    struct hostent* server = gethostbyname(hostname);
    if (server == NULL) {
        fprintf(stderr, "ERROR: No such host: %s\n", hostname);
        return -1;
    }
    
    struct sockaddr_in server_addr;
    memset(&server_addr, 0, sizeof(server_addr));
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(FTP_PORT);
    memcpy(&server_addr.sin_addr.s_addr, server->h_addr, server->h_length);
    
    // Create control socket
    ftp->control_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (ftp->control_socket < 0) {
        perror("ERROR opening control socket");
        return -1;
    }
    
    // Connect to server
    if (connect(ftp->control_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("ERROR connecting to FTP server");
        close(ftp->control_socket);
        return -1;
    }
    
    strcpy(ftp->server_ip, inet_ntoa(server_addr.sin_addr));
    ftp->server_port = FTP_PORT;
    ftp->passive_mode = 1;
    
    printf("Connected to FTP server: %s\n", hostname);
    return 0;
}

// Read FTP response
FTPResponse ftp_read_response(int socket) {
    FTPResponse response;
    char buffer[BUFFER_SIZE];
    int bytes_received;
    
    memset(&response, 0, sizeof(response));
    
    bytes_received = recv(socket, buffer, BUFFER_SIZE - 1, 0);
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        sscanf(buffer, "%d %255[^\r\n]", &response.code, response.message);
        printf("FTP Response: %d %s\n", response.code, response.message);
    }
    
    return response;
}

// Send FTP command
FTPResponse ftp_send_command(FTPConnection* ftp, const char* command) {
    char buffer[BUFFER_SIZE];
    FTPResponse response;
    
    printf("FTP Command: %s\n", command);
    
    sprintf(buffer, "%s\r\n", command);
    send(ftp->control_socket, buffer, strlen(buffer), 0);
    
    response = ftp_read_response(ftp->control_socket);
    return response;
}

// Login to FTP server
int ftp_login(FTPConnection* ftp, const char* username, const char* password) {
    FTPResponse response;
    
    // Send username
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "USER %s", username);
    response = ftp_send_command(ftp, cmd);
    
    if (response.code == 331) { // Need password
        // Send password
        sprintf(cmd, "PASS %s", password);
        response = ftp_send_command(ftp, cmd);
        
        if (response.code == 230) { // Login successful
            printf("Login successful\n");
            return 0;
        }
    }
    
    printf("Login failed: %d %s\n", response.code, response.message);
    return -1;
}

// Enter passive mode
int ftp_enter_passive_mode(FTPConnection* ftp) {
    FTPResponse response;
    
    response = ftp_send_command(ftp, "PASV");
    
    if (response.code == 227) { // Entering passive mode
        // Parse passive mode response
        int h1, h2, h3, h4, p1, p2;
        char* ptr = strstr(response.message, "(");
        if (ptr) {
            sscanf(ptr + 1, "%d,%d,%d,%d,%d,%d", &h1, &h2, &h3, &h4, &p1, &p2);
            
            sprintf(ftp->server_ip, "%d.%d.%d.%d", h1, h2, h3, h4);
            ftp->server_port = p1 * 256 + p2;
            
            printf("Passive mode: %s:%d\n", ftp->server_ip, ftp->server_port);
            return 0;
        }
    }
    
    printf("Failed to enter passive mode\n");
    return -1;
}

// Open data connection
int ftp_open_data_connection(FTPConnection* ftp) {
    if (ftp->passive_mode) {
        if (ftp_enter_passive_mode(ftp) != 0) {
            return -1;
        }
    }
    
    // Create data socket
    ftp->data_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (ftp->data_socket < 0) {
        perror("ERROR opening data socket");
        return -1;
    }
    
    // Connect to data port
    struct sockaddr_in data_addr;
    memset(&data_addr, 0, sizeof(data_addr));
    data_addr.sin_family = AF_INET;
    data_addr.sin_port = htons(ftp->server_port);
    inet_pton(AF_INET, ftp->server_ip, &data_addr.sin_addr);
    
    if (connect(ftp->data_socket, (struct sockaddr*)&data_addr, sizeof(data_addr)) < 0) {
        perror("ERROR connecting to data port");
        close(ftp->data_socket);
        return -1;
    }
    
    printf("Data connection established\n");
    return 0;
}

// Close data connection
void ftp_close_data_connection(FTPConnection* ftp) {
    if (ftp->data_socket > 0) {
        close(ftp->data_socket);
        ftp->data_socket = -1;
        printf("Data connection closed\n");
    }
}

// List directory
int ftp_list_directory(FTPConnection* ftp, const char* pathname) {
    FTPResponse response;
    
    if (ftp_open_data_connection(ftp) != 0) {
        return -1;
    }
    
    // Send LIST command
    char cmd[BUFFER_SIZE];
    if (pathname) {
        sprintf(cmd, "LIST %s", pathname);
    } else {
        strcpy(cmd, "LIST");
    }
    
    response = ftp_send_command(ftp, cmd);
    
    if (response.code == 150 || response.code == 125) {
        // Read directory listing
        char buffer[BUFFER_SIZE];
        int bytes_received;
        
        printf("Directory listing:\n");
        printf("================\n");
        
        while ((bytes_received = recv(ftp->data_socket, buffer, BUFFER_SIZE - 1, 0)) > 0) {
            buffer[bytes_received] = '\0';
            printf("%s", buffer);
        }
        
        ftp_close_data_connection(ftp);
        
        // Read final response
        response = ftp_read_response(ftp->control_socket);
        return 0;
    }
    
    ftp_close_data_connection(ftp);
    return -1;
}

// Download file
int ftp_download_file(FTPConnection* ftp, const char* remote_filename, const char* local_filename) {
    FTPResponse response;
    FILE* local_file;
    
    // Open local file for writing
    local_file = fopen(local_filename, "wb");
    if (!local_file) {
        perror("ERROR opening local file");
        return -1;
    }
    
    if (ftp_open_data_connection(ftp) != 0) {
        fclose(local_file);
        return -1;
    }
    
    // Send RETR command
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "RETR %s", remote_filename);
    response = ftp_send_command(ftp, cmd);
    
    if (response.code == 150 || response.code == 125) {
        // Download file
        char buffer[BUFFER_SIZE];
        int bytes_received;
        long total_bytes = 0;
        
        printf("Downloading %s...\n", remote_filename);
        
        while ((bytes_received = recv(ftp->data_socket, buffer, BUFFER_SIZE, 0)) > 0) {
            fwrite(buffer, 1, bytes_received, local_file);
            total_bytes += bytes_received;
            printf("\rDownloaded: %ld bytes", total_bytes);
            fflush(stdout);
        }
        
        printf("\nDownload complete: %ld bytes\n", total_bytes);
        
        ftp_close_data_connection(ftp);
        fclose(local_file);
        
        // Read final response
        response = ftp_read_response(ftp->control_socket);
        return 0;
    }
    
    ftp_close_data_connection(ftp);
    fclose(local_file);
    return -1;
}

// Upload file
int ftp_upload_file(FTPConnection* ftp, const char* local_filename, const char* remote_filename) {
    FTPResponse response;
    FILE* local_file;
    
    // Open local file for reading
    local_file = fopen(local_filename, "rb");
    if (!local_file) {
        perror("ERROR opening local file");
        return -1;
    }
    
    if (ftp_open_data_connection(ftp) != 0) {
        fclose(local_file);
        return -1;
    }
    
    // Send STOR command
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "STOR %s", remote_filename);
    response = ftp_send_command(ftp, cmd);
    
    if (response.code == 150 || response.code == 125) {
        // Upload file
        char buffer[BUFFER_SIZE];
        int bytes_read;
        long total_bytes = 0;
        
        printf("Uploading %s...\n", local_filename);
        
        while ((bytes_read = fread(buffer, 1, BUFFER_SIZE, local_file)) > 0) {
            send(ftp->data_socket, buffer, bytes_read, 0);
            total_bytes += bytes_read;
            printf("\rUploaded: %ld bytes", total_bytes);
            fflush(stdout);
        }
        
        printf("\nUpload complete: %ld bytes\n", total_bytes);
        
        ftp_close_data_connection(ftp);
        fclose(local_file);
        
        // Read final response
        response = ftp_read_response(ftp->control_socket);
        return 0;
    }
    
    ftp_close_data_connection(ftp);
    fclose(local_file);
    return -1;
}

// Change directory
int ftp_change_directory(FTPConnection* ftp, const char* pathname) {
    FTPResponse response;
    
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "CWD %s", pathname);
    response = ftp_send_command(ftp, cmd);
    
    return (response.code == 250) ? 0 : -1;
}

// Print working directory
int ftp_print_working_directory(FTPConnection* ftp) {
    FTPResponse response;
    
    response = ftp_send_command(ftp, "PWD");
    
    return (response.code == 257) ? 0 : -1;
}

// Create directory
int ftp_create_directory(FTPConnection* ftp, const char* pathname) {
    FTPResponse response;
    
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "MKD %s", pathname);
    response = ftp_send_command(ftp, cmd);
    
    return (response.code == 257) ? 0 : -1;
}

// Remove directory
int ftp_remove_directory(FTPConnection* ftp, const char* pathname) {
    FTPResponse response;
    
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "RMD %s", pathname);
    response = ftp_send_command(ftp, cmd);
    
    return (response.code == 250) ? 0 : -1;
}

// Delete file
int ftp_delete_file(FTPConnection* ftp, const char* filename) {
    FTPResponse response;
    
    char cmd[BUFFER_SIZE];
    sprintf(cmd, "DELE %s", filename);
    response = ftp_send_command(ftp, cmd);
    
    return (response.code == 250) ? 0 : -1;
}

// Quit FTP session
void ftp_quit(FTPConnection* ftp) {
    FTPResponse response;
    
    response = ftp_send_command(ftp, "QUIT");
    
    close(ftp->control_socket);
    if (ftp->data_socket > 0) {
        close(ftp->data_socket);
    }
    
    printf("FTP session closed\n");
}

// Interactive FTP client
void interactive_ftp_client(FTPConnection* ftp) {
    char command[BUFFER_SIZE];
    char arg1[BUFFER_SIZE];
    char arg2[BUFFER_SIZE];
    
    printf("\n=== Interactive FTP Client ===\n");
    printf("Available commands:\n");
    printf("  ls [directory]     - List directory contents\n");
    printf("  cd <directory>     - Change directory\n");
    printf("  pwd                - Print working directory\n");
    printf("  get <remote> <local> - Download file\n");
    printf("  put <local> <remote> - Upload file\n");
    printf("  mkdir <directory>  - Create directory\n");
    printf("  rmdir <directory>  - Remove directory\n");
    printf("  delete <file>      - Delete file\n");
    printf("  quit               - Exit FTP client\n");
    printf("===============================\n");
    
    while (1) {
        printf("ftp> ");
        fflush(stdout);
        
        if (fgets(command, sizeof(command), stdin) == NULL) {
            break;
        }
        
        // Parse command
        arg1[0] = arg2[0] = '\0';
        sscanf(command, "%s %s %s", command, arg1, arg2);
        
        if (strcmp(command, "ls") == 0) {
            ftp_list_directory(ftp, arg1[0] ? arg1 : NULL);
        } else if (strcmp(command, "cd") == 0) {
            if (arg1[0]) {
                if (ftp_change_directory(ftp, arg1) == 0) {
                    printf("Changed to directory: %s\n", arg1);
                } else {
                    printf("Failed to change directory\n");
                }
            } else {
                printf("Usage: cd <directory>\n");
            }
        } else if (strcmp(command, "pwd") == 0) {
            ftp_print_working_directory(ftp);
        } else if (strcmp(command, "get") == 0) {
            if (arg1[0] && arg2[0]) {
                if (ftp_download_file(ftp, arg1, arg2) == 0) {
                    printf("File downloaded successfully\n");
                } else {
                    printf("Failed to download file\n");
                }
            } else {
                printf("Usage: get <remote_file> <local_file>\n");
            }
        } else if (strcmp(command, "put") == 0) {
            if (arg1[0] && arg2[0]) {
                if (ftp_upload_file(ftp, arg1, arg2) == 0) {
                    printf("File uploaded successfully\n");
                } else {
                    printf("Failed to upload file\n");
                }
            } else {
                printf("Usage: put <local_file> <remote_file>\n");
            }
        } else if (strcmp(command, "mkdir") == 0) {
            if (arg1[0]) {
                if (ftp_create_directory(ftp, arg1) == 0) {
                    printf("Directory created successfully\n");
                } else {
                    printf("Failed to create directory\n");
                }
            } else {
                printf("Usage: mkdir <directory>\n");
            }
        } else if (strcmp(command, "rmdir") == 0) {
            if (arg1[0]) {
                if (ftp_remove_directory(ftp, arg1) == 0) {
                    printf("Directory removed successfully\n");
                } else {
                    printf("Failed to remove directory\n");
                }
            } else {
                printf("Usage: rmdir <directory>\n");
            }
        } else if (strcmp(command, "delete") == 0) {
            if (arg1[0]) {
                if (ftp_delete_file(ftp, arg1) == 0) {
                    printf("File deleted successfully\n");
                } else {
                    printf("Failed to delete file\n");
                }
            } else {
                printf("Usage: delete <file>\n");
            }
        } else if (strcmp(command, "quit") == 0) {
            break;
        } else if (command[0] != '\n' && command[0] != '\0') {
            printf("Unknown command: %s\n", command);
        }
    }
}

int main(int argc, char* argv[]) {
    FTPConnection ftp;
    char hostname[256];
    char username[256];
    char password[256];
    
    if (argc < 2) {
        printf("Usage: %s <hostname> [username] [password]\n", argv[0]);
        printf("Example: %s ftp.example.com anonymous anonymous@example.com\n", argv[0]);
        return 1;
    }
    
    strcpy(hostname, argv[1]);
    
    if (argc >= 3) {
        strcpy(username, argv[2]);
    } else {
        printf("Username: ");
        fgets(username, sizeof(username), stdin);
        username[strcspn(username, "\n")] = '\0';
    }
    
    if (argc >= 4) {
        strcpy(password, argv[3]);
    } else {
        printf("Password: ");
        fgets(password, sizeof(password), stdin);
        password[strcspn(password, "\n")] = '\0';
    }
    
    // Connect to FTP server
    if (ftp_connect(&ftp, hostname) != 0) {
        return 1;
    }
    
    // Read welcome message
    FTPResponse welcome = ftp_read_response(ftp.control_socket);
    
    // Login
    if (ftp_login(&ftp, username, password) != 0) {
        ftp_quit(&ftp);
        return 1;
    }
    
    // Interactive mode
    interactive_ftp_client(&ftp);
    
    // Quit
    ftp_quit(&ftp);
    
    return 0;
}
