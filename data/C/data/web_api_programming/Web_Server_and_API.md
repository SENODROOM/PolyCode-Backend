# Web Server and API Programming

This file contains comprehensive web server and API programming examples in C, including HTTP request/response handling, routing systems, REST API implementation, and basic web server architecture.

## 📚 Web Programming Overview

### 🌐 Web Concepts
- **HTTP Protocol**: Request-response communication
- **REST API**: Representational State Transfer
- **JSON**: JavaScript Object Notation for data exchange
- **Routing**: Mapping URLs to handler functions
- **Middleware**: Request/response processing pipeline

### 🏗️ Server Architecture
- **Socket Programming**: Network communication
- **Multi-threading**: Handling concurrent requests
- **Request Parsing**: Understanding HTTP requests
- **Response Generation**: Creating HTTP responses

## 🌐 HTTP Fundamentals

### HTTP Methods
```c
typedef enum {
    HTTP_GET,
    HTTP_POST,
    HTTP_PUT,
    HTTP_DELETE,
    HTTP_PATCH,
    HTTP_OPTIONS,
    HTTP_HEAD
} HttpMethod;
```

### HTTP Status Codes
```c
typedef enum {
    HTTP_OK = 200,
    HTTP_CREATED = 201,
    HTTP_BAD_REQUEST = 400,
    HTTP_UNAUTHORIZED = 401,
    HTTP_NOT_FOUND = 404,
    HTTP_METHOD_NOT_ALLOWED = 405,
    HTTP_INTERNAL_SERVER_ERROR = 500
} HttpStatus;
```

### HTTP Request Structure
```c
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
```

### HTTP Response Structure
```c
typedef struct {
    HttpStatus status;
    char headers[20][256];
    int header_count;
    char body[BUFFER_SIZE];
    int body_length;
    char version[16];
} HttpResponse;
```

## 🔧 HTTP Request/Response Handling

### Request Parsing
```c
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
```

### Response Generation
```c
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
```

### Response Builder Functions
```c
void setResponseStatus(HttpResponse* response, HttpStatus status) {
    response->status = status;
}

void setResponseHeader(HttpResponse* response, const char* name, const char* value) {
    if (response->header_count < 20) {
        char header[256];
        sprintf(header, "%s: %s", name, value);
        strcpy(response->headers[response->header_count++], header);
    }
}

void setResponseBody(HttpResponse* response, const char* content, const char* content_type) {
    response->body_length = strlen(content);
    if (response->body_length < BUFFER_SIZE) {
        strcpy(response->body, content);
        setResponseHeader(response, "Content-Type", content_type);
    }
}
```

## 🛣️ Routing System

### Route Structure
```c
typedef struct {
    HttpMethod method;
    char path[256];
    RouteHandler handler;
} Route;

typedef void (*RouteHandler)(HttpRequest* request, HttpResponse* response);
```

### Route Registration
```c
void addRoute(HttpMethod method, const char* path, RouteHandler handler) {
    if (server.route_count < MAX_ROUTES) {
        server.routes[server.route_count].method = method;
        strcpy(server.routes[server.route_count].path, path);
        server.routes[server.route_count].handler = handler;
        server.route_count++;
    }
}
```

### Route Matching
```c
Route* findRoute(HttpMethod method, const char* path) {
    for (int i = 0; i < server.route_count; i++) {
        if (server.routes[i].method == method && strcmp(server.routes[i].path, path) == 0) {
            return &server.routes[i];
        }
    }
    return NULL;
}
```

### Route Handler Example
```c
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
```

## 🏗️ Web Server Core

### Server Structure
```c
typedef struct {
    SOCKET server_socket;
    Route routes[MAX_ROUTES];
    int route_count;
    int is_running;
} WebServer;
```

### Server Initialization
```c
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
```

### Client Handling
```c
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
```

### Server Loop
```c
void startWebServer() {
    printf("Starting web server...\n");
    
    // Set up routes
    addRoute(HTTP_GET, "/", homeHandler);
    addRoute(HTTP_GET, "/api/time", timeHandler);
    addRoute(HTTP_GET, "/api/hello", helloHandler);
    
    server.is_running = 1;
    
    printf("Web server running on http://localhost:%d\n", PORT);
    printf("Press Ctrl+C to stop\n\n");
    
    // Main server loop
    while (server.is_running) {
        struct sockaddr_in client_addr;
        int client_addr_size = sizeof(client_addr);
        
        SOCKET client_socket = accept(server.server_socket, (struct sockaddr*)&client_addr, &client_addr_size);
        
        if (client_socket != INVALID_SOCKET) {
            // Handle client in a new thread (simplified)
            handleClient(client_socket);
        }
    }
}
```

## 🔄 REST API Implementation

### User Database Model
```c
typedef struct {
    int id;
    char name[100];
    char email[100];
    int active;
} User;

User users[100];
int user_count = 0;
```

### GET All Users
```c
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
```

### GET User by ID
```c
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
```

### POST Create User
```c
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
```

### PUT Update User
```c
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
```

### DELETE User
```c
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
```

## 🛠️ Utility Functions

### URL Decoding
```c
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
```

### Query String Parsing
```c
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
```

### MIME Type Detection
```c
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
```

### Timestamp Generation
```c
char* getCurrentTimestamp() {
    static char timestamp[64];
    time_t now = time(NULL);
    struct tm* tm_info = localtime(&now);
    strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", tm_info);
    return timestamp;
}
```

## 📁 File Serving

### Static File Handler
```c
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
```

## 📊 JSON Generation

### JSON Response Builder
```c
void buildJsonResponse(HttpResponse* response, const char* data) {
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, data, "application/json");
}

void buildErrorResponse(HttpResponse* response, const char* error, HttpStatus status) {
    char json[256];
    sprintf(json, "{\"error\":\"%s\"}", error);
    setResponseStatus(response, status);
    setResponseBody(response, json, "application/json");
}

void buildSuccessResponse(HttpResponse* response, const char* message) {
    char json[256];
    sprintf(json, "{\"message\":\"%s\"}", message);
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}
```

### JSON Array Generation
```c
void buildJsonArray(HttpResponse* response, const char* array_name, const char* items[], int count) {
    char json[BUFFER_SIZE];
    sprintf(json, "{\"%s\":[", array_name);
    
    for (int i = 0; i < count; i++) {
        if (i > 0) strcat(json, ",");
        strcat(json, "\"");
        strcat(json, items[i]);
        strcat(json, "\"");
    }
    
    strcat(json, "],\"total\":");
    char total_str[10];
    sprintf(total_str, "%d", count);
    strcat(json, total_str);
    strcat(json, "}");
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, json, "application/json");
}
```

## 🔐 Security Features

### Input Validation
```c
int validateInput(const char* input, int max_length) {
    if (!input || strlen(input) > max_length) {
        return 0;
    }
    
    // Check for dangerous characters
    const char* dangerous_chars = "<>\"'&";
    for (int i = 0; input[i]; i++) {
        if (strchr(dangerous_chars, input[i])) {
            return 0;
        }
    }
    
    return 1;
}

int validateEmail(const char* email) {
    if (!validateInput(email, 100)) return 0;
    
    // Simple email validation
    int at_count = 0;
    int dot_count = 0;
    
    for (int i = 0; email[i]; i++) {
        if (email[i] == '@') at_count++;
        if (email[i] == '.') dot_count++;
    }
    
    return at_count == 1 && dot_count >= 1;
}
```

### Rate Limiting
```c
typedef struct {
    char ip[16];
    int request_count;
    time_t last_request;
} RateLimitEntry;

RateLimitEntry rate_limit_table[100];
int rate_limit_count = 0;

int checkRateLimit(const char* ip, int max_requests, int time_window) {
    time_t now = time(NULL);
    
    // Find existing entry
    for (int i = 0; i < rate_limit_count; i++) {
        if (strcmp(rate_limit_table[i].ip, ip) == 0) {
            if (now - rate_limit_table[i].last_request > time_window) {
                // Reset counter
                rate_limit_table[i].request_count = 1;
                rate_limit_table[i].last_request = now;
                return 1;
            } else if (rate_limit_table[i].request_count >= max_requests) {
                return 0; // Rate limited
            } else {
                rate_limit_table[i].request_count++;
                return 1;
            }
        }
    }
    
    // Add new entry
    if (rate_limit_count < 100) {
        strcpy(rate_limit_table[rate_limit_count].ip, ip);
        rate_limit_table[rate_limit_count].request_count = 1;
        rate_limit_table[rate_limit_count].last_request = now;
        rate_limit_count++;
    }
    
    return 1;
}
```

### CORS Headers
```c
void addCorsHeaders(HttpResponse* response) {
    setResponseHeader(response, "Access-Control-Allow-Origin", "*");
    setResponseHeader(response, "Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    setResponseHeader(response, "Access-Control-Allow-Headers", "Content-Type, Authorization");
}
```

## 🔄 Middleware System

### Middleware Function Type
```c
typedef void (*Middleware)(HttpRequest* request, HttpResponse* response, void (*next)(HttpRequest*, HttpResponse*));
```

### Logging Middleware
```c
void loggingMiddleware(HttpRequest* request, HttpResponse* response, void (*next)(HttpRequest*, HttpResponse*)) {
    printf("[%s] %s %s\n", getCurrentTimestamp(), 
           (request->method == HTTP_GET) ? "GET" : "POST", request.path);
    
    next(request, response);
    
    printf("[%s] Response: %d\n", getCurrentTimestamp(), response->status);
}
```

### Authentication Middleware
```c
void authMiddleware(HttpRequest* request, HttpResponse* response, void (*next)(HttpRequest*, HttpResponse*)) {
    // Check for Authorization header
    int auth_header_found = 0;
    for (int i = 0; i < request->header_count; i++) {
        if (strncmp(request->headers[i], "Authorization:", 13) == 0) {
            auth_header_found = 1;
            break;
        }
    }
    
    if (!auth_header_found) {
        buildErrorResponse(response, "Authorization required", HTTP_UNAUTHORIZED);
        return;
    }
    
    next(request, response);
}
```

## 💡 Advanced Features

### WebSocket Support (Simplified)
```c
typedef struct {
    char key[64];
    char protocol[32];
    int version;
} WebSocketHandshake;

int parseWebSocketHandshake(HttpRequest* request, WebSocketHandshake* handshake) {
    // Parse WebSocket handshake headers
    for (int i = 0; i < request->header_count; i++) {
        if (strncmp(request->headers[i], "Sec-WebSocket-Key:", 18) == 0) {
            strcpy(handshake->key, request->headers[i] + 19);
        }
        if (strncmp(request->headers[i], "Sec-WebSocket-Protocol:", 23) == 0) {
            strcpy(handshake->protocol, request->headers[i] + 24);
        }
        if (strncmp(request->headers[i], "Sec-WebSocket-Version:", 22) == 0) {
            handshake->version = atoi(request->headers[i] + 23);
        }
    }
    
    return strlen(handshake->key) > 0;
}
```

### Template Engine (Simplified)
```c
void renderTemplate(HttpResponse* response, const char* template_name, const char* variables[10][2]) {
    char filename[256];
    sprintf(filename, "templates/%s.html", template_name);
    
    FILE* file = fopen(filename, "r");
    if (!file) {
        buildErrorResponse(response, "Template not found", HTTP_NOT_FOUND);
        return;
    }
    
    char template_content[BUFFER_SIZE];
    int template_size = fread(template_content, 1, BUFFER_SIZE - 1, file);
    template_content[template_size] = '\0';
    fclose(file);
    
    // Simple variable substitution
    for (int i = 0; i < 10 && variables[i][0]; i++) {
        char placeholder[64];
        sprintf(placeholder, "{{%s}}", variables[i][0]);
        
        char* pos = strstr(template_content, placeholder);
        if (pos) {
            int placeholder_len = strlen(placeholder);
            int value_len = strlen(variables[i][1]);
            
            if (value_len <= placeholder_len) {
                strncpy(pos, variables[i][1], value_len);
                // Fill remaining space with spaces
                for (int j = value_len; j < placeholder_len; j++) {
                    pos[j] = ' ';
                }
            }
        }
    }
    
    setResponseStatus(response, HTTP_OK);
    setResponseBody(response, template_content, "text/html");
}
```

### Session Management
```c
typedef struct {
    char session_id[32];
    char user_id[32];
    time_t created;
    time_t last_access;
    char data[256];
} Session;

Session sessions[100];
int session_count = 0;

char* createSession(const char* user_id) {
    if (session_count < 100) {
        Session* session = &sessions[session_count];
        
        // Generate session ID (simplified)
        sprintf(session->session_id, "session_%d", session_count + 1);
        strcpy(session->user_id, user_id);
        session->created = time(NULL);
        session->last_access = time(NULL);
        strcpy(session->data, "");
        
        session_count++;
        return session->session_id;
    }
    
    return NULL;
}

Session* getSession(const char* session_id) {
    for (int i = 0; i < session_count; i++) {
        if (strcmp(sessions[i].session_id, session_id) == 0) {
            sessions[i].last_access = time(NULL);
            return &sessions[i];
        }
    }
    
    return NULL;
}
```

## 📊 API Documentation

### Auto-Generated Documentation
```c
void generateApiDocumentation() {
    printf("API Endpoints:\n");
    printf("=============\n\n");
    
    for (int i = 0; i < server.route_count; i++) {
        const char* method_str;
        switch (server.routes[i].method) {
            case HTTP_GET: method_str = "GET"; break;
            case HTTP_POST: method_str = "POST"; break;
            case HTTP_PUT: method_str = "PUT"; break;
            case HTTP_DELETE: method_str = "DELETE"; break;
            default: method_str = "UNKNOWN"; break;
        }
        
        printf("%s %s\n", method_str, server.routes[i].path);
        printf("  Handler: %p\n", server.routes[i].handler);
        printf("\n");
    }
}
```

### OpenAPI Specification (Simplified)
```c
void generateOpenApiSpec() {
    printf("{\n");
    printf("  \"openapi\": \"3.0.0\",\n");
    printf("  \"info\": {\n");
    printf("    \"title\": \"C Web Server API\",\n");
    printf("    \"version\": \"1.0.0\"\n");
    printf("  },\n");
    printf("  \"paths\": {\n");
    
    for (int i = 0; i < server.route_count; i++) {
        printf("    \"%s\": {\n", server.routes[i].path);
        
        const char* method_str;
        switch (server.routes[i].method) {
            case HTTP_GET: method_str = "get"; break;
            case HTTP_POST: method_str = "post"; break;
            case HTTP_PUT: method_str = "put"; break;
            case HTTP_DELETE: method_str = "delete"; break;
            default: method_str = "unknown"; break;
        }
        
        printf("      \"%s\": {\n", method_str);
        printf("        \"summary\": \"%s %s\",\n", method_str, server.routes[i].path);
        printf("        \"responses\": {\n");
        printf("          \"200\": {\n");
        printf("            \"description\": \"Success\"\n");
        printf("          }\n");
        printf("        }\n");
        printf("      }\n");
        printf("    },\n");
    }
    
    printf("  }\n");
    printf("}\n");
}
```

## ⚠️ Common Pitfalls

### 1. Memory Management
```c
// Wrong - Not checking buffer sizes
void unsafeCopy(char* dest, const char* src) {
    strcpy(dest, src); // May overflow buffer
}

// Right - Check buffer sizes
void safeCopy(char* dest, const char* src, size_t dest_size) {
    strncpy(dest, src, dest_size - 1);
    dest[dest_size - 1] = '\0';
}
```

### 2. Socket Programming
```c
// Wrong - Not checking return values
void unsafeSocket() {
    SOCKET sock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
    bind(sock, (struct sockaddr*)&addr, sizeof(addr)); // May fail
    listen(sock, 10); // May fail
}

// Right - Check all return values
int safeSocket() {
    SOCKET sock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
    if (sock == INVALID_SOCKET) return 0;
    
    if (bind(sock, (struct sockaddr*)&addr, sizeof(addr)) == SOCKET_ERROR) {
        closesocket(sock);
        return 0;
    }
    
    if (listen(sock, 10) == SOCKET_ERROR) {
        closesocket(sock);
        return 0;
    }
    
    return 1;
}
```

### 3. String Handling
```c
// Wrong - Not validating input
void unsafeInput(char* input) {
    printf("Input: %s\n", input); // May be null or too long
}

// Right - Validate input
void safeInput(char* input) {
    if (!input) return;
    if (strlen(input) > 255) return;
    
    // Check for dangerous characters
    if (strchr(input, '<') || strchr(input, '>')) {
        printf("Invalid input\n");
        return;
    }
    
    printf("Input: %s\n", input);
}
```

### 4. Resource Cleanup
```c
// Wrong - Forgetting to close sockets
void handleConnection() {
    SOCKET client = accept(server_socket, NULL, NULL);
    // Process request...
    // Forgot to close client socket
}

// Right - Always close resources
void handleConnectionSafe() {
    SOCKET client = accept(server_socket, NULL, NULL);
    if (client != INVALID_SOCKET) {
        // Process request...
        closesocket(client);
    }
}
```

## 🔧 Real-World Applications

### 1. RESTful API Server
```c
void setupRestApi() {
    // User management endpoints
    addRoute(HTTP_GET, "/api/users", getUsersApi);
    addRoute(HTTP_POST, "/api/users", createUserApi);
    addRoute(HTTP_GET, "/api/users/", getUserByIdApi);
    addRoute(HTTP_PUT, "/api/users/", updateUserApi);
    addRoute(HTTP_DELETE, "/api/users/", deleteUserApi);
    
    // Product management endpoints
    addRoute(HTTP_GET, "/api/products", getProductsApi);
    addRoute(HTTP_POST, "/api/products", createProductApi);
    addRoute(HTTP_PUT, "/api/products/", updateProductApi);
    addRoute(HTTP_DELETE, "/api/products/", deleteProductApi);
    
    // Order management endpoints
    addRoute(HTTP_GET, "/api/orders", getOrdersApi);
    addRoute(HTTP_POST, "/api/orders", createOrderApi);
    addRoute(HTTP_GET, "/api/orders/", getOrderByIdApi);
}
```

### 2. Static File Server
```c
void setupStaticFileServer() {
    // Serve files from public directory
    addRoute(HTTP_GET, "/", serveIndexHandler);
    addRoute(HTTP_GET, "/css/", serveCssHandler);
    addRoute(HTTP_GET, "/js/", serveJsHandler);
    addRoute(HTTP_GET, "/images/", serveImageHandler);
    
    // Add file serving fallback
    addRoute(HTTP_GET, "*", fileHandler);
}
```

### 3. WebSocket Chat Server
```c
void setupWebSocketChat() {
    addRoute(HTTP_GET, "/ws", websocketUpgradeHandler);
    
    // Handle WebSocket messages
    // Broadcast messages to all connected clients
    // Maintain client connections
    // Handle disconnections
}
```

### 4. File Upload API
```c
void setupFileUpload() {
    addRoute(HTTP_POST, "/api/upload", fileUploadHandler);
    addRoute(HTTP_GET, "/api/files/", fileListHandler);
    addRoute(HTTP_DELETE, "/api/files/", deleteFileHandler);
}

void fileUploadHandler(HttpRequest* request, HttpResponse* response) {
    // Parse multipart/form-data
    // Save uploaded files
    // Return file information
}
```

## 🎓 Best Practices

### 1. Error Handling
```c
// Always check return values
if (parseHttpRequest(buffer, &request)) {
    // Process request
} else {
    buildErrorResponse(&response, "Invalid request", HTTP_BAD_REQUEST);
}
```

### 2. Security
```c
// Validate all inputs
if (!validateInput(request->path, 255)) {
    buildErrorResponse(response, "Invalid path", HTTP_BAD_REQUEST);
    return;
}

// Use HTTPS in production
// Implement rate limiting
// Add authentication
```

### 3. Performance
```c
// Use connection pooling
// Implement caching
// Optimize JSON generation
// Use asynchronous I/O
```

### 4. Logging
```c
// Log all requests
printf("[%s] %s %s %d\n", getCurrentTimestamp(), 
       method_str, request->path, response->status);

// Log errors
if (error_occurred) {
    printf("[%s] ERROR: %s\n", getCurrentTimestamp(), error_message);
}
```

### 5. Documentation
```c
// Document all API endpoints
// Provide examples
// Include error codes
// Add OpenAPI specification
```

Web server and API programming in C provides fundamental understanding of web protocols and network programming. While C may not be the first choice for web development, implementing these concepts helps understand the underlying mechanics of web applications. For production use, consider established libraries like libevent, libmicrohttpd, or frameworks like nginx!
