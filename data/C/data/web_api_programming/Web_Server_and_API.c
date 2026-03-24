#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <winsock2.h>
#include <ws2tcpip.h>
#include <time.h>
#include <process.h>

#pragma comment(lib, "ws2_32.lib")

// =============================================================================
// WEB SERVER FUNDAMENTALS
// =============================================================================

#define PORT 8080
#define BUFFER_SIZE 4096
#define MAX_CONNECTIONS 10
#define MAX_ROUTES 50

// HTTP Methods
typedef enum {
    HTTP_GET,
    HTTP_POST,
    HTTP_PUT,
    HTTP_DELETE,
    HTTP_PATCH,
    HTTP_OPTIONS,
    HTTP_HEAD
} HttpMethod;

// HTTP Status Codes
typedef enum {
    HTTP_OK = 200,
    HTTP_CREATED = 201,
    HTTP_BAD_REQUEST = 400,
    HTTP_UNAUTHORIZED = 401,
    HTTP_NOT_FOUND = 404,
    HTTP_METHOD_NOT_ALLOWED = 405,
    HTTP_INTERNAL_SERVER_ERROR = 500
} HttpStatus;

// HTTP Request structure
typedef struct {
    HttpMethod method;
    char path[256];
    char query[256];
    char headers[20][256];
    int header_count;
    char body[BUFFER_SIZE];
    int body_length;
    char version[16];
} HttpRequest;

// HTTP Response structure
typedef struct {
    HttpStatus status;
    char headers[20][256];
    int header_count;
    char body[BUFFER_SIZE];
    int body_length;
    char version[16];
} HttpResponse;

// Route handler function type
typedef void (*RouteHandler)(HttpRequest* request, HttpResponse* response);

// Route structure
typedef struct {
    HttpMethod method;
    char path[256];
    RouteHandler handler;
} Route;

// Web server structure
typedef struct {
    SOCKET server_socket;
    Route routes[MAX_ROUTES];
    int route_count;
    int is_running;
} WebServer;

WebServer server;

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

// Get current timestamp
char* getCurrentTimestamp() {
    static char timestamp[64];
    time_t now = time(NULL);
    struct tm* tm_info = localtime(&now);
    strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", tm_info);
    return timestamp;
}

// URL decode function
void urlDecode(char* dst, const char* src) {
    char a, b;
    while (*src) {
        if (*src == '%' && ((a = src[1]) && (b = src[2])) && 
            (isxdigit(a) && isxdigit(b))) {
            if (a >= 'a')
                a -= 'a'-'A';
            if (b >= 'a')
                b -= 'a'-'A';
            *dst++ = 16 * (a - '0' + (a > '9' ? 10 : 0)) + 
                     (b - '0' + (b > '9' ? 10 : 0));
            src += 3;
        } else if (*src == '+') {
            *dst++ = ' ';
            src++;
        } else {
            *dst++ = *src++;
        }
    }
    *dst++ = '\0';
}

// Parse query string
void parseQueryString(const char* query, char params[10][256]) {
    char* query_copy = strdup(query);
    char* token = strtok(query_copy, "&");
    int param_count = 0;
    
    while (token != NULL && param_count < 10) {
        strcpy(params[param_count++], token);
        token = strtok(NULL, "&");
    }
    
    free(query_copy);
}

// Get MIME type for file extension
const char* getMimeType(const char* filename) {
    const char* ext = strrchr(filename, '.');
    if (!ext) return "text/plain";
    
    if (strcmp(ext, ".html") == 0) return "text/html";
    if (strcmp(ext, ".css") == 0) return "text/css";
    if (strcmp(ext, ".js") == 0) return "application/javascript";
    if (strcmp(ext, ".json") == 0) return "application/json";
    if (strcmp(ext, ".png") == 0) return "image/png";
    if (strcmp(ext, ".jpg") == 0 || strcmp(ext, ".jpeg") == 0) return "image/jpeg";
    if (strcmp(ext, ".gif") == 0) return "image/gif";
    if (strcmp(ext, ".svg") == 0) return "image/svg+xml";
    
    return "text/plain";
}

// =============================================================================
// HTTP REQUEST/RESPONSE HANDLING
// =============================================================================

// Initialize HTTP request
void initHttpRequest(HttpRequest* request) {
    request->method = HTTP_GET;
    strcpy(request->path, "/");
    strcpy(request->query, "");
    request->header_count = 0;
    request->body_length = 0;
    strcpy(request->version, "HTTP/1.1");
}

// Initialize HTTP response
void initHttpResponse(HttpResponse* response) {
    response->status = HTTP_OK;
    response->header_count = 0;
    response->body_length = 0;
    strcpy(response->version, "HTTP/1.1");
}

// Parse HTTP method
HttpMethod parseHttpMethod(const char* method) {
    if (strcmp(method, "GET") == 0) return HTTP_GET;
    if (strcmp(method, "POST") == 0) return HTTP_POST;
    if (strcmp(method, "PUT") == 0) return HTTP_PUT;
    if (strcmp(method, "DELETE") == 0) return HTTP_DELETE;
    if (strcmp(method, "PATCH") == 0) return HTTP_PATCH;
    if (strcmp(method, "OPTIONS") == 0) return HTTP_OPTIONS;
    if (strcmp(method, "HEAD") == 0) return HTTP_HEAD;
    
    return HTTP_GET; // Default
}

// Parse HTTP request
int parseHttpRequest(const char* raw_request, HttpRequest* request) {
    char request_copy[BUFFER_SIZE];
    strcpy(request_copy, raw_request);
    
    // Parse request line
    char* line = strtok(request_copy, "\r\n");
    if (!line) return 0;
    
    char method[16], path[256], version[16];
    if (sscanf(line, "%s %s %s", method, path, version) != 3) {
        return 0;
    }
    
    request->method = parseHttpMethod(method);
    strcpy(request->version, version);
    
    // Parse path and query
    char* query_start = strchr(path, '?');
    if (query_start) {
        *query_start = '\0';
        strcpy(request->path, path);
        strcpy(request->query, query_start + 1);
    } else {
        strcpy(request->path, path);
        strcpy(request->query, "");
    }
    
    // Parse headers
    request->header_count = 0;
    while ((line = strtok(NULL, "\r\n")) != NULL && strlen(line) > 0) {
        if (request->header_count < 20) {
            strcpy(request->headers[request->header_count++], line);
        }
    }
    
    // Parse body
    char* body_start = strstr(raw_request, "\r\n\r\n");
    if (body_start) {
        body_start += 4;
        request->body_length = strlen(body_start);
        if (request->body_length < BUFFER_SIZE) {
            strcpy(request->body, body_start);
        }
    }
    
    return 1;
}

// Set response status
void setResponseStatus(HttpResponse* response, HttpStatus status) {
    response->status = status;
}

// Set response header
void setResponseHeader(HttpResponse* response, const char* name, const char* value) {
    if (response->header_count < 20) {
        char header[256];
        sprintf(header, "%s: %s", name, value);
        strcpy(response->headers[response->header_count++], header);
    }
}

// Set response body
void setResponseBody(HttpResponse* response, const char* content, const char* content_type) {
    response->body_length = strlen(content);
    if (response->body_length < BUFFER_SIZE) {
        strcpy(response->body, content);
        setResponseHeader(response, "Content-Type", content_type);
    }
}

// Format HTTP response
void formatHttpResponse(HttpResponse* response, char* output) {
    // Status line
    const char* status_text;
    switch (response->status) {
        case HTTP_OK: status_text = "OK"; break;
        case HTTP_CREATED: status_text = "Created"; break;
        case HTTP_BAD_REQUEST: status_text = "Bad Request"; break;
        case HTTP_UNAUTHORIZED: status_text = "Unauthorized"; break;
        case HTTP_NOT_FOUND: status_text = "Not Found"; break;
        case HTTP_METHOD_NOT_ALLOWED: status_text = "Method Not Allowed"; break;
        case HTTP_INTERNAL_SERVER_ERROR: status_text = "Internal Server Error"; break;
        default: status_text = "Unknown"; break;
    }
    
    sprintf(output, "%s %d %s\r\n", response->version, response->status, status_text);
    
    // Headers
    for (int i = 0; i < response->header_count; i++) {
        strcat(output, response->headers[i]);
        strcat(output, "\r\n");
    }
    
    // Content-Length header
    char content_length[32];
    sprintf(content_length, "Content-Length: %d", response->body_length);
    strcat(output, content_length);
    strcat(output, "\r\n");
    
    // Date header
    strcat(output, "Date: ");
    strcat(output, getCurrentTimestamp());
    strcat(output, "\r\n");
    
    // Server header
    strcat(output, "Server: C-WebServer/1.0\r\n");
    
    // End of headers
    strcat(output, "\r\n");
    
    // Body
    if (response->body_length > 0) {
        strcat(output, response->body);
    }
}

// =============================================================================
// ROUTING SYSTEM
// =============================================================================

// Add route to server
void addRoute(HttpMethod method, const char* path, RouteHandler handler) {
    if (server.route_count < MAX_ROUTES) {
        server.routes[server.route_count].method = method;
        strcpy(server.routes[server.route_count].path, path);
        server.routes[server.route_count].handler = handler;
        server.route_count++;
    }
}

// Find matching route
Route* findRoute(HttpMethod method, const char* path) {
    for (int i = 0; i < server.route_count; i++) {
        if (server.routes[i].method == method && strcmp(server.routes[i].path, path) == 0) {
            return &server.routes[i];
        }
    }
    return NULL;
}

// =============================================================================
// API ENDPOINTS
// =============================================================================

// Home page handler
void homeHandler(HttpRequest* request, HttpResponse* response) {
    const char* html = 
        "<!DOCTYPE html>"
        "<html>"
        "<head><title>C Web Server</title></head>"
        "<body>"
        "<h1>Welcome to C Web Server!</h1>"
        "<p>This is a simple web server implemented in C.</p>"
        "<ul>"
        "<li><a href='/api/time'>Current Time API</a></li>"
        "<li><a href='/api/hello'>Hello API</a></li>"
        "<li><a href='/api/users'>Users API</a></li>"
        "</ul>"
        "</body>"
        "</html>";
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, html, "text/html");
}

// Current time API handler
void timeHandler(HttpRequest* request, HttpResponse* response) {
    char json[256];
    sprintf(json, "{\"timestamp\":\"%s\",\"unix\":%ld}", getCurrentTimestamp(), time(NULL));
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}

// Hello API handler
void helloHandler(HttpRequest* request, HttpResponse* response) {
    char name[256] = "World";
    
    // Parse query parameters
    if (strlen(request->query) > 0) {
        char params[10][256];
        parseQueryString(request->query, params);
        
        for (int i = 0; i < 10 && strlen(params[i]) > 0; i++) {
            if (strncmp(params[i], "name=", 5) == 0) {
                urlDecode(name, params[i] + 5);
                break;
            }
        }
    }
    
    char json[256];
    sprintf(json, "{\"message\":\"Hello, %s!\",\"timestamp\":\"%s\"}", name, getCurrentTimestamp());
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}

// Users API handler
void usersHandler(HttpRequest* request, HttpResponse* response) {
    const char* users_json = 
        "{"
        "\"users\":["
        "{\"id\":1,\"name\":\"John Doe\",\"email\":\"john@example.com\"},"
        "{\"id\":2,\"name\":\"Jane Smith\",\"email\":\"jane@example.com\"},"
        "{\"id\":3,\"name\":\"Bob Johnson\",\"email\":\"bob@example.com\"}"
        "],"
        "\"total\":3"
        "}";
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, users_json, "application/json");
}

// User by ID handler
void userByIdHandler(HttpRequest* request, HttpResponse* response) {
    // Extract user ID from path (simplified)
    int user_id = 1; // Default
    
    // In a real implementation, parse the path to get the ID
    char* id_start = strstr(request->path, "/users/");
    if (id_start) {
        user_id = atoi(id_start + 7);
    }
    
    char json[256];
    if (user_id >= 1 && user_id <= 3) {
        sprintf(json, "{\"id\":%d,\"name\":\"User %d\",\"email\":\"user%d@example.com\"}", 
                user_id, user_id, user_id);
        setResponseStatus(response, HTTP_OK);
    } else {
        sprintf(json, "{\"error\":\"User not found\"}");
        setResponseStatus(response, HTTP_NOT_FOUND);
    }
    
    setResponseBody(response, json, "application/json");
}

// Echo handler for testing
void echoHandler(HttpRequest* request, HttpResponse* response) {
    char json[BUFFER_SIZE];
    sprintf(json, 
        "{"
        "\"method\":\"%s\","
        "\"path\":\"%s\","
        "\"query\":\"%s\","
        "\"headers_count\":%d,"
        "\"body_length\":%d,"
        "\"body\":\"%s\""
        "}",
        (request->method == HTTP_GET) ? "GET" : "POST",
        request->path,
        request->query,
        request->header_count,
        request->body_length,
        request->body);
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}

// File serving handler
void fileHandler(HttpRequest* request, HttpResponse* response) {
    // Extract filename from path
    char filename[256];
    strcpy(filename, ".");
    strcat(filename, request->path);
    
    // Try to open file
    FILE* file = fopen(filename, "rb");
    if (!file) {
        const char* not_found = "<h1>404 - File Not Found</h1>";
        setResponseStatus(response, HTTP_NOT_FOUND);
        setResponseBody(response, not_found, "text/html");
        return;
    }
    
    // Read file content
    fseek(file, 0, SEEK_END);
    long file_size = ftell(file);
    fseek(file, 0, SEEK_SET);
    
    if (file_size < BUFFER_SIZE) {
        char* content = (char*)malloc(file_size + 1);
        fread(content, 1, file_size, file);
        content[file_size] = '\0';
        
        setResponseStatus(response, HTTP_OK);
        setResponseBody(response, content, getMimeType(filename));
        
        free(content);
    } else {
        const char* too_large = "<h1>File too large</h1>";
        setResponseStatus(response, HTTP_INTERNAL_SERVER_ERROR);
        setResponseBody(response, too_large, "text/html");
    }
    
    fclose(file);
}

// =============================================================================
// WEB SERVER CORE
// =============================================================================

// Initialize web server
int initWebServer() {
    WSADATA wsaData;
    if (WSAStartup(MAKEWORD(2, 2), &wsaData) != 0) {
        printf("WSAStartup failed\n");
        return 0;
    }
    
    server.server_socket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
    if (server.server_socket == INVALID_SOCKET) {
        printf("Socket creation failed\n");
        WSACleanup();
        return 0;
    }
    
    // Set socket options
    int opt = 1;
    setsockopt(server.server_socket, SOL_SOCKET, SO_REUSEADDR, (char*)&opt, sizeof(opt));
    
    // Bind socket
    struct sockaddr_in server_addr;
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(PORT);
    
    if (bind(server.server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) == SOCKET_ERROR) {
        printf("Bind failed\n");
        closesocket(server.server_socket);
        WSACleanup();
        return 0;
    }
    
    // Listen for connections
    if (listen(server.server_socket, MAX_CONNECTIONS) == SOCKET_ERROR) {
        printf("Listen failed\n");
        closesocket(server.server_socket);
        WSACleanup();
        return 0;
    }
    
    server.route_count = 0;
    server.is_running = 0;
    
    printf("Web server initialized on port %d\n", PORT);
    return 1;
}

// Handle client request
void handleClient(SOCKET client_socket) {
    char buffer[BUFFER_SIZE];
    int bytes_received = recv(client_socket, buffer, BUFFER_SIZE - 1, 0);
    
    if (bytes_received > 0) {
        buffer[bytes_received] = '\0';
        
        HttpRequest request;
        HttpResponse response;
        
        initHttpRequest(&request);
        initHttpResponse(&response);
        
        // Parse request
        if (parseHttpRequest(buffer, &request)) {
            printf("[%s] %s %s\n", getCurrentTimestamp(), 
                   (request.method == HTTP_GET) ? "GET" : "POST", request.path);
            
            // Find matching route
            Route* route = findRoute(request.method, request.path);
            
            if (route) {
                route->handler(&request, &response);
            } else {
                // Try file serving
                if (request.method == HTTP_GET && strlen(request.path) > 1) {
                    fileHandler(&request, &response);
                } else {
                    // 404 Not Found
                    const char* not_found = "<h1>404 - Not Found</h1>";
                    setResponseStatus(&response, HTTP_NOT_FOUND);
                    setResponseBody(&response, not_found, "text/html");
                }
            }
        } else {
            // Bad request
            const char* bad_request = "<h1>400 - Bad Request</h1>";
            setResponseStatus(&response, HTTP_BAD_REQUEST);
            setResponseBody(&response, bad_request, "text/html");
        }
        
        // Format and send response
        char response_buffer[BUFFER_SIZE * 2];
        formatHttpResponse(&response, response_buffer);
        
        send(client_socket, response_buffer, strlen(response_buffer), 0);
    }
    
    closesocket(client_socket);
}

// Start web server
void startWebServer() {
    printf("Starting web server...\n");
    
    // Set up routes
    addRoute(HTTP_GET, "/", homeHandler);
    addRoute(HTTP_GET, "/api/time", timeHandler);
    addRoute(HTTP_GET, "/api/hello", helloHandler);
    addRoute(HTTP_GET, "/api/users", usersHandler);
    addRoute(HTTP_GET, "/api/users/", userByIdHandler);
    addRoute(HTTP_GET, "/echo", echoHandler);
    addRoute(HTTP_POST, "/echo", echoHandler);
    
    server.is_running = 1;
    
    printf("Web server running on http://localhost:%d\n", PORT);
    printf("Press Ctrl+C to stop\n\n");
    
    // Main server loop
    while (server.is_running) {
        struct sockaddr_in client_addr;
        int client_addr_size = sizeof(client_addr);
        
        SOCKET client_socket = accept(server.server_socket, (struct sockaddr*)&client_addr, &client_addr_size);
        
        if (client_socket != INVALID_SOCKET) {
            // Handle client in a new thread (simplified - in production, use thread pool)
            handleClient(client_socket);
        }
    }
}

// Stop web server
void stopWebServer() {
    server.is_running = 0;
    closesocket(server.server_socket);
    WSACleanup();
    printf("Web server stopped\n");
}

// =============================================================================
// REST API EXAMPLES
// =============================================================================

// User database (simplified in-memory)
typedef struct {
    int id;
    char name[100];
    char email[100];
    int active;
} User;

User users[100];
int user_count = 0;

// Initialize user database
void initUserDatabase() {
    // Add sample users
    users[0].id = 1;
    strcpy(users[0].name, "John Doe");
    strcpy(users[0].email, "john@example.com");
    users[0].active = 1;
    
    users[1].id = 2;
    strcpy(users[1].name, "Jane Smith");
    strcpy(users[1].email, "jane@example.com");
    users[1].active = 1;
    
    user_count = 2;
}

// GET all users
void getUsersApi(HttpRequest* request, HttpResponse* response) {
    char json[BUFFER_SIZE];
    strcpy(json, "{\"users\":[");
    
    for (int i = 0; i < user_count; i++) {
        if (i > 0) strcat(json, ",");
        char user_json[256];
        sprintf(user_json, "{\"id\":%d,\"name\":\"%s\",\"email\":\"%s\",\"active\":%s}",
                users[i].id, users[i].name, users[i].email, users[i].active ? "true" : "false");
        strcat(json, user_json);
    }
    
    strcat(json, "],\"total\":");
    char total_str[10];
    sprintf(total_str, "%d", user_count);
    strcat(json, total_str);
    strcat(json, "}");
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}

// GET user by ID
void getUserByIdApi(HttpRequest* request, HttpResponse* response) {
    int user_id = 1; // Default
    
    // Parse ID from path (simplified)
    char* id_start = strstr(request->path, "/api/users/");
    if (id_start) {
        user_id = atoi(id_start + 11);
    }
    
    // Find user
    User* user = NULL;
    for (int i = 0; i < user_count; i++) {
        if (users[i].id == user_id && users[i].active) {
            user = &users[i];
            break;
        }
    }
    
    char json[512];
    if (user) {
        sprintf(json, "{\"id\":%d,\"name\":\"%s\",\"email\":\"%s\",\"active\":%s}",
                user->id, user->name, user->email, user->active ? "true" : "false");
        setResponseStatus(response, HTTP_OK);
    } else {
        sprintf(json, "{\"error\":\"User not found\"}");
        setResponseStatus(response, HTTP_NOT_FOUND);
    }
    
    setResponseBody(response, json, "application/json");
}

// POST create user
void createUserApi(HttpRequest* request, HttpResponse* response) {
    // Parse JSON body (simplified)
    char name[100] = "";
    char email[100] = "";
    
    // Simple JSON parsing (in production, use proper JSON library)
    char* name_start = strstr(request->body, "\"name\":\"");
    if (name_start) {
        name_start += 8;
        char* name_end = strstr(name_start, "\"");
        if (name_end) {
            int name_len = name_end - name_start;
            strncpy(name, name_start, name_len);
            name[name_len] = '\0';
        }
    }
    
    char* email_start = strstr(request->body, "\"email\":\"");
    if (email_start) {
        email_start += 9;
        char* email_end = strstr(email_start, "\"");
        if (email_end) {
            int email_len = email_end - email_start;
            strncpy(email, email_start, email_len);
            email[email_len] = '\0';
        }
    }
    
    // Validate input
    if (strlen(name) == 0 || strlen(email) == 0) {
        const char* error_json = "{\"error\":\"Name and email are required\"}";
        setResponseStatus(response, HTTP_BAD_REQUEST);
        setResponseBody(response, error_json, "application/json");
        return;
    }
    
    // Create new user
    if (user_count < 100) {
        User new_user;
        new_user.id = user_count > 0 ? users[user_count - 1].id + 1 : 1;
        strcpy(new_user.name, name);
        strcpy(new_user.email, email);
        new_user.active = 1;
        
        users[user_count++] = new_user;
        
        char json[512];
        sprintf(json, "{\"id\":%d,\"name\":\"%s\",\"email\":\"%s\",\"active\":true}",
                new_user.id, new_user.name, new_user.email);
        
        setResponseStatus(response, HTTP_CREATED);
        setResponseBody(response, json, "application/json");
    } else {
        const char* error_json = "{\"error\":\"User limit reached\"}";
        setResponseStatus(response, HTTP_INTERNAL_SERVER_ERROR);
        setResponseBody(response, error_json, "application/json");
    }
}

// PUT update user
void updateUserApi(HttpRequest* request, HttpResponse* response) {
    int user_id = 1; // Default
    
    // Parse ID from path
    char* id_start = strstr(request->path, "/api/users/");
    if (id_start) {
        user_id = atoi(id_start + 11);
    }
    
    // Find user
    User* user = NULL;
    for (int i = 0; i < user_count; i++) {
        if (users[i].id == user_id && users[i].active) {
            user = &users[i];
            break;
        }
    }
    
    if (!user) {
        const char* error_json = "{\"error\":\"User not found\"}";
        setResponseStatus(response, HTTP_NOT_FOUND);
        setResponseBody(response, error_json, "application/json");
        return;
    }
    
    // Parse JSON body for updates (simplified)
    char name[100] = "";
    char email[100] = "";
    
    char* name_start = strstr(request->body, "\"name\":\"");
    if (name_start) {
        name_start += 8;
        char* name_end = strstr(name_start, "\"");
        if (name_end) {
            int name_len = name_end - name_start;
            strncpy(name, name_start, name_len);
            name[name_len] = '\0';
            if (strlen(name) > 0) {
                strcpy(user->name, name);
            }
        }
    }
    
    char* email_start = strstr(request->body, "\"email\":\"");
    if (email_start) {
        email_start += 9;
        char* email_end = strstr(email_start, "\"");
        if (email_end) {
            int email_len = email_end - email_start;
            strncpy(email, email_start, email_len);
            email[email_len] = '\0';
            if (strlen(email) > 0) {
                strcpy(user->email, email);
            }
        }
    }
    
    char json[512];
    sprintf(json, "{\"id\":%d,\"name\":\"%s\",\"email\":\"%s\",\"active\":%s}",
            user->id, user->name, user->email, user->active ? "true" : "false");
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}

// DELETE user
void deleteUserApi(HttpRequest* request, HttpResponse* response) {
    int user_id = 1; // Default
    
    // Parse ID from path
    char* id_start = strstr(request->path, "/api/users/");
    if (id_start) {
        user_id = atoi(id_start + 11);
    }
    
    // Find and deactivate user
    for (int i = 0; i < user_count; i++) {
        if (users[i].id == user_id && users[i].active) {
            users[i].active = 0;
            
            const char* success_json = "{\"message\":\"User deleted successfully\"}";
            setResponseStatus(response, HTTP_OK);
            setResponseBody(response, success_json, "application/json");
            return;
        }
    }
    
    const char* error_json = "{\"error\":\"User not found\"}";
    setResponseStatus(response, HTTP_NOT_FOUND);
    setResponseBody(response, error_json, "application/json");
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicServer() {
    printf("=== BASIC WEB SERVER DEMO ===\n");
    
    if (!initWebServer()) {
        printf("Failed to initialize web server\n");
        return;
    }
    
    // Add basic routes
    addRoute(HTTP_GET, "/", homeHandler);
    addRoute(HTTP_GET, "/api/time", timeHandler);
    addRoute(HTTP_GET, "/api/hello", helloHandler);
    
    printf("Basic web server configured with routes:\n");
    printf("- GET / - Home page\n");
    printf("- GET /api/time - Current time API\n");
    printf("- GET /api/hello - Hello API\n");
    printf("\n");
    
    // Note: In a real application, you would start the server here
    // startWebServer();
    
    // For demo purposes, just show the configuration
    printf("Web server is ready to start.\n");
    printf("In a real application, call startWebServer() to begin serving.\n\n");
}

void demonstrateRestAPI() {
    printf("=== REST API DEMO ===\n");
    
    // Initialize user database
    initUserDatabase();
    
    // Add REST API routes
    addRoute(HTTP_GET, "/api/users", getUsersApi);
    addRoute(HTTP_GET, "/api/users/", getUserByIdApi);
    addRoute(HTTP_POST, "/api/users", createUserApi);
    addRoute(HTTP_PUT, "/api/users/", updateUserApi);
    addRoute(HTTP_DELETE, "/api/users/", deleteUserApi);
    
    printf("REST API configured with endpoints:\n");
    printf("- GET /api/users - Get all users\n");
    printf("- GET /api/users/{id} - Get user by ID\n");
    printf("- POST /api/users - Create new user\n");
    printf("- PUT /api/users/{id} - Update user\n");
    printf("- DELETE /api/users/{id} - Delete user\n");
    printf("\n");
    
    printf("Sample API calls:\n");
    printf("GET http://localhost:8080/api/users\n");
    printf("POST http://localhost:8080/api/users\n");
    printf("PUT http://localhost:8080/api/users/1\n");
    printf("DELETE http://localhost:8080/api/users/1\n");
    printf("\n");
}

void demonstrateRequestHandling() {
    printf("=== REQUEST HANDLING DEMO ===\n");
    
    // Sample HTTP request
    const char* sample_request = 
        "GET /api/hello?name=World HTTP/1.1\r\n"
        "Host: localhost:8080\r\n"
        "User-Agent: curl/7.68.0\r\n"
        "Accept: application/json\r\n"
        "\r\n";
    
    printf("Sample HTTP Request:\n%s\n", sample_request);
    
    // Parse request
    HttpRequest request;
    HttpResponse response;
    
    initHttpRequest(&request);
    initHttpResponse(&response);
    
    if (parseHttpRequest(sample_request, &request)) {
        printf("Parsed Request:\n");
        printf("Method: %d\n", request.method);
        printf("Path: %s\n", request.path);
        printf("Query: %s\n", request.query);
        printf("Headers: %d\n", request.header_count);
        
        for (int i = 0; i < request.header_count; i++) {
            printf("  %s\n", request.headers[i]);
        }
        
        // Handle request
        helloHandler(&request, &response);
        
        // Format response
        char response_buffer[BUFFER_SIZE * 2];
        formatHttpResponse(&response, response_buffer);
        
        printf("\nGenerated Response:\n%s\n", response_buffer);
    } else {
        printf("Failed to parse request\n");
    }
    
    printf("\n");
}

void demonstrateJsonGeneration() {
    printf("=== JSON GENERATION DEMO ===\n");
    
    // Sample JSON responses
    printf("User JSON:\n");
    printf("{\"id\":1,\"name\":\"John Doe\",\"email\":\"john@example.com\",\"active\":true}\n\n");
    
    printf("Users List JSON:\n");
    printf("{\"users\":[{\"id\":1,\"name\":\"John\",\"email\":\"john@example.com\"},{\"id\":2,\"name\":\"Jane\",\"email\":\"jane@example.com\"}],\"total\":2}\n\n");
    
    printf("Error JSON:\n");
    printf("{\"error\":\"User not found\",\"code\":404}\n\n");
    
    printf("Success JSON:\n");
    printf("{\"message\":\"User created successfully\",\"user_id\":123}\n\n");
}

void demonstrateApiDocumentation() {
    printf("=== API DOCUMENTATION DEMO ===\n");
    
    printf("REST API Documentation:\n");
    printf("========================\n\n");
    
    printf("Users API:\n");
    printf("---------\n");
    printf("GET /api/users\n");
    printf("  Description: Get all users\n");
    printf("  Response: Array of user objects\n");
    printf("  Example: GET http://localhost:8080/api/users\n\n");
    
    printf("GET /api/users/{id}\n");
    printf("  Description: Get user by ID\n");
    printf("  Parameters: id (integer)\n");
    printf("  Response: User object\n");
    printf("  Example: GET http://localhost:8080/api/users/1\n\n");
    
    printf("POST /api/users\n");
    printf("  Description: Create new user\n");
    printf("  Body: JSON object with name and email\n");
    printf("  Response: Created user object\n");
    printf("  Example: POST http://localhost:8080/api/users\n");
    printf("           Body: {\"name\":\"John Doe\",\"email\":\"john@example.com\"}\n\n");
    
    printf("PUT /api/users/{id}\n");
    printf("  Description: Update user\n");
    printf("  Parameters: id (integer)\n");
    printf("  Body: JSON object with updated fields\n");
    printf("  Response: Updated user object\n");
    printf("  Example: PUT http://localhost:8080/api/users/1\n");
    printf("         Body: {\"name\":\"John Smith\"}\n\n");
    
    printf("DELETE /api/users/{id}\n");
    printf("  Description: Delete user\n");
    printf("  Parameters: id (integer)\n");
    printf("  Response: Success message\n");
    printf("  Example: DELETE http://localhost:8080/api/users/1\n\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Web Server and API Programming\n");
    printf("==============================\n\n");
    
    // Run demonstrations
    demonstrateBasicServer();
    demonstrateRestAPI();
    demonstrateRequestHandling();
    demonstrateJsonGeneration();
    demonstrateApiDocumentation();
    
    printf("All web server and API examples demonstrated!\n");
    printf("Note: This is a simplified web server for educational purposes.\n");
    printf("For production use, consider established libraries like libevent, libmicrohttpd, or nginx.\n");
    printf("\nTo actually run the server, uncomment the startWebServer() call in demonstrateBasicServer().\n");
    
    // Clean up
    // stopWebServer();
    
    return 0;
}
