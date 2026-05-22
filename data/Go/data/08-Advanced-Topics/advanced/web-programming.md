# Go Web Programming

## HTTP Fundamentals

### Basic HTTP Server
```go
package main

import (
    "fmt"
    "net/http"
    "log"
    "time"
    "encoding/json"
    "io/ioutil"
    "strings"
    "strconv"
)

// Basic HTTP server
func basicServer() {
    // Simple handler
    helloHandler := func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "Hello, World! Request received at %s\n", time.Now().Format(time.RFC3339))
    }
    
    // Handler with path parameters
    userHandler := func(w http.ResponseWriter, r *http.Request) {
        // Extract user ID from URL path
        path := strings.TrimPrefix(r.URL.Path, "/users/")
        userID, err := strconv.Atoi(path)
        if err != nil {
            http.Error(w, "Invalid user ID", http.StatusBadRequest)
            return
        }
        
        fmt.Fprintf(w, "User ID: %d\n", userID)
        fmt.Fprintf(w, "Method: %s\n", r.Method)
        fmt.Fprintf(w, "Headers:\n")
        
        for name, values := range r.Header {
            for _, value := range values {
                fmt.Fprintf(w, "  %s: %s\n", name, value)
            }
        }
    }
    
    // JSON handler
    jsonHandler := func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")
        
        response := map[string]interface{}{
            "message": "Hello, JSON!",
            "time":    time.Now().Format(time.RFC3339),
            "method":  r.Method,
        }
        
        json.NewEncoder(w).Encode(response)
    }
    
    // Register handlers
    http.HandleFunc("/", helloHandler)
    http.HandleFunc("/users/", userHandler)
    http.HandleFunc("/api/hello", jsonHandler)
    
    // Start server
    fmt.Println("Server starting on :8080...")
    if err := http.ListenAndServe(":8080", nil); err != nil {
        log.Fatal("Server error:", err)
    }
}

// Advanced server with custom mux
func advancedServer() {
    // Custom multiplexer
    mux := http.NewServeMux()
    
    // Middleware for logging
    loggingMiddleware := func(next http.Handler) http.Handler {
        return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
            start := time.Now()
            
            // Call next handler
            next.ServeHTTP(w, r)
            
            // Log request
            fmt.Printf("[%s] %s %s %v\n", 
                time.Now().Format(time.RFC3339),
                r.Method,
                r.URL.Path,
                time.Since(start))
        })
    }
    
    // Middleware for CORS
    corsMiddleware := func(next http.Handler) http.Handler {
        return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
            w.Header().Set("Access-Control-Allow-Origin", "*")
            w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
            
            if r.Method == "OPTIONS" {
                w.WriteHeader(http.StatusOK)
                return
            }
            
            next.ServeHTTP(w, r)
        })
    }
    
    // Handlers
    homeHandler := func(w http.ResponseWriter, r *http.Request) {
        w.WriteHeader(http.StatusOK)
        fmt.Fprintf(w, "Welcome to the advanced Go server!")
    }
    
    apiHandler := func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")
        
        data := map[string]interface{}{
            "endpoint": r.URL.Path,
            "method":   r.Method,
            "query":    r.URL.Query(),
            "time":     time.Now().Unix(),
        }
        
        json.NewEncoder(w).Encode(data)
    }
    
    // Register handlers
    mux.HandleFunc("/", homeHandler)
    mux.HandleFunc("/api/", apiHandler)
    
    // Apply middleware
    handler := loggingMiddleware(corsMiddleware(mux))
    
    // Custom server configuration
    server := &http.Server{
        Addr:         ":8080",
        Handler:      handler,
        ReadTimeout:  10 * time.Second,
        WriteTimeout: 10 * time.Second,
        IdleTimeout:  120 * time.Second,
    }
    
    fmt.Println("Advanced server starting on :8080...")
    if err := server.ListenAndServe(); err != nil {
        log.Fatal("Server error:", err)
    }
}

// HTTP client examples
func httpClientExamples() {
    // Simple GET request
    resp, err := http.Get("https://httpbin.org/get")
    if err != nil {
        log.Fatal("GET error:", err)
    }
    defer resp.Body.Close()
    
    body, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        log.Fatal("Read error:", err)
    }
    
    fmt.Printf("GET Response: %s\n", string(body))
    
    // POST request with JSON
    data := map[string]interface{}{
        "name":  "John Doe",
        "email": "john@example.com",
    }
    
    jsonData, _ := json.Marshal(data)
    
    resp, err = http.Post("https://httpbin.org/post", 
        "application/json", 
        strings.NewReader(string(jsonData)))
    if err != nil {
        log.Fatal("POST error:", err)
    }
    defer resp.Body.Close()
    
    body, err = ioutil.ReadAll(resp.Body)
    if err != nil {
        log.Fatal("Read error:", err)
    }
    
    fmt.Printf("POST Response: %s\n", string(body))
    
    // Custom client with timeout
    client := &http.Client{
        Timeout: 30 * time.Second,
    }
    
    req, err := http.NewRequest("GET", "https://httpbin.org/delay/2", nil)
    if err != nil {
        log.Fatal("Request error:", err)
    }
    
    resp, err = client.Do(req)
    if err != nil {
        log.Fatal("Client error:", err)
    }
    defer resp.Body.Close()
    
    fmt.Printf("Delayed request completed with status: %s\n", resp.Status)
}
```

### RESTful API Design
```go
package main

import (
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "strconv"
    "strings"
    "sync"
    "time"
)

// User model
type User struct {
    ID        int       `json:"id"`
    Name      string    `json:"name"`
    Email     string    `json:"email"`
    Age       int       `json:"age"`
    CreatedAt time.Time `json:"created_at"`
    UpdatedAt time.Time `json:"updated_at"`
}

// User service
type UserService struct {
    users  map[int]User
    nextID int
    mu     sync.RWMutex
}

func NewUserService() *UserService {
    return &UserService{
        users:  make(map[int]User),
        nextID: 1,
    }
}

func (us *UserService) CreateUser(name, email string, age int) (*User, error) {
    us.mu.Lock()
    defer us.mu.Unlock()
    
    if name == "" {
        return nil, fmt.Errorf("name cannot be empty")
    }
    
    if email == "" {
        return nil, fmt.Errorf("email cannot be empty")
    }
    
    if age < 0 || age > 120 {
        return nil, fmt.Errorf("invalid age")
    }
    
    user := User{
        ID:        us.nextID,
        Name:      name,
        Email:     email,
        Age:       age,
        CreatedAt: time.Now(),
        UpdatedAt: time.Now(),
    }
    
    us.users[user.ID] = user
    us.nextID++
    
    return &user, nil
}

func (us *UserService) GetUser(id int) (*User, error) {
    us.mu.RLock()
    defer us.mu.RUnlock()
    
    user, exists := us.users[id]
    if !exists {
        return nil, fmt.Errorf("user not found")
    }
    
    return &user, nil
}

func (us *UserService) UpdateUser(id int, name, email string, age int) (*User, error) {
    us.mu.Lock()
    defer us.mu.Unlock()
    
    user, exists := us.users[id]
    if !exists {
        return nil, fmt.Errorf("user not found")
    }
    
    if name != "" {
        user.Name = name
    }
    
    if email != "" {
        user.Email = email
    }
    
    if age >= 0 {
        user.Age = age
    }
    
    user.UpdatedAt = time.Now()
    us.users[id] = user
    
    return &user, nil
}

func (us *UserService) DeleteUser(id int) error {
    us.mu.Lock()
    defer us.mu.Unlock()
    
    if _, exists := us.users[id]; !exists {
        return fmt.Errorf("user not found")
    }
    
    delete(us.users, id)
    return nil
}

func (us *UserService) GetAllUsers() []User {
    us.mu.RLock()
    defer us.mu.RUnlock()
    
    users := make([]User, 0, len(us.users))
    for _, user := range us.users {
        users = append(users, user)
    }
    
    return users
}

// HTTP handlers
type UserHandler struct {
    service *UserService
}

func NewUserHandler(service *UserService) *UserHandler {
    return &UserHandler{service: service}
}

func (uh *UserHandler) CreateUser(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.CreateUser(req.Name, req.Email, req.Age)
    if err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusCreated)
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) GetUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.GetUser(id)
    if err != nil {
        if err.Error() == "user not found" {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusInternalServerError)
        }
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) UpdateUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.UpdateUser(id, req.Name, req.Email, req.Age)
    if err != nil {
        if err.Error() == "user not found" {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusBadRequest)
        }
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) DeleteUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    err = uh.service.DeleteUser(id)
    if err != nil {
        if err.Error() == "user not found" {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusInternalServerError)
        }
        return
    }
    
    w.WriteHeader(http.StatusNoContent)
}

func (uh *UserHandler) GetAllUsers(w http.ResponseWriter, r *http.Request) {
    users := uh.service.GetAllUsers()
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(users)
}

// Response helper
type APIResponse struct {
    Success bool        `json:"success"`
    Data    interface{} `json:"data,omitempty"`
    Error   string      `json:"error,omitempty"`
}

func writeJSONResponse(w http.ResponseWriter, statusCode int, response APIResponse) {
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(statusCode)
    json.NewEncoder(w).Encode(response)
}

// Router setup
func setupRouter() *http.ServeMux {
    userService := NewUserService()
    userHandler := NewUserHandler(userService)
    
    mux := http.NewServeMux()
    
    // User routes
    mux.HandleFunc("/users", func(w http.ResponseWriter, r *http.Request) {
        switch r.Method {
        case http.MethodGet:
            userHandler.GetAllUsers(w, r)
        case http.MethodPost:
            userHandler.CreateUser(w, r)
        default:
            http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
        }
    })
    
    mux.HandleFunc("/users/", func(w http.ResponseWriter, r *http.Request) {
        switch r.Method {
        case http.MethodGet:
            userHandler.GetUser(w, r)
        case http.MethodPut:
            userHandler.UpdateUser(w, r)
        case http.MethodDelete:
            userHandler.DeleteUser(w, r)
        default:
            http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
        }
    })
    
    // Health check
    mux.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
        response := APIResponse{
            Success: true,
            Data: map[string]interface{}{
                "status":    "healthy",
                "timestamp": time.Now().Unix(),
                "version":   "1.0.0",
            },
        }
        writeJSONResponse(w, http.StatusOK, response)
    })
    
    // API info
    mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        response := APIResponse{
            Success: true,
            Data: map[string]interface{}{
                "name":        "Go REST API",
                "version":     "1.0.0",
                "description": "A simple REST API built with Go",
                "endpoints": []string{
                    "GET    /users        - Get all users",
                    "POST   /users        - Create user",
                    "GET    /users/{id}   - Get user by ID",
                    "PUT    /users/{id}   - Update user",
                    "DELETE /users/{id}   - Delete user",
                    "GET    /health       - Health check",
                },
            },
        }
        writeJSONResponse(w, http.StatusOK, response)
    })
    
    return mux
}

// Middleware
func loggingMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        start := time.Now()
        
        // Create a response writer to capture status code
        wrapped := &responseWriter{ResponseWriter: w, statusCode: http.StatusOK}
        
        // Call next handler
        next.ServeHTTP(wrapped, r)
        
        // Log request
        duration := time.Since(start)
        log.Printf("%s %s %d %v", 
            r.Method,
            r.URL.Path,
            wrapped.statusCode,
            duration)
    })
}

type responseWriter struct {
    http.ResponseWriter
    statusCode int
}

func (rw *responseWriter) WriteHeader(code int) {
    rw.statusCode = code
    rw.ResponseWriter.WriteHeader(code)
}

func corsMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Access-Control-Allow-Origin", "*")
        w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
        w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
        
        if r.Method == "OPTIONS" {
            w.WriteHeader(http.StatusOK)
            return
        }
        
        next.ServeHTTP(w, r)
    })
}

func authMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        // Simple auth check - in production, use proper JWT validation
        authHeader := r.Header.Get("Authorization")
        if authHeader == "" {
            writeJSONResponse(w, http.StatusUnauthorized, APIResponse{
                Success: false,
                Error:   "Authorization header required",
            })
            return
        }
        
        // Extract token (simplified)
        token := strings.TrimPrefix(authHeader, "Bearer ")
        if token == "" {
            writeJSONResponse(w, http.StatusUnauthorized, APIResponse{
                Success: false,
                Error:   "Invalid authorization format",
            })
            return
        }
        
        // In production, validate JWT token here
        if token != "valid-token" {
            writeJSONResponse(w, http.StatusUnauthorized, APIResponse{
                Success: false,
                Error:   "Invalid token",
            })
            return
        }
        
        next.ServeHTTP(w, r)
    })
}

// Main server function
func runRESTServer() {
    router := setupRouter()
    
    // Apply middleware
    handler := loggingMiddleware(corsMiddleware(router))
    
    server := &http.Server{
        Addr:         ":8080",
        Handler:      handler,
        ReadTimeout:  15 * time.Second,
        WriteTimeout: 15 * time.Second,
        IdleTimeout:  60 * time.Second,
    }
    
    log.Println("REST API server starting on :8080...")
    log.Fatal(server.ListenAndServe())
}
```

## Web Frameworks

### Using Popular Frameworks
```go
package main

import (
    "github.com/gin-gonic/gin"
    "github.com/gorilla/mux"
    "github.com/go-chi/chi/v5"
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "time"
)

// Gin framework example
func ginExample() {
    // Create Gin router
    r := gin.Default()
    
    // Middleware
    r.Use(gin.Logger())
    r.Use(gin.Recovery())
    
    // CORS middleware
    r.Use(func(c *gin.Context) {
        c.Header("Access-Control-Allow-Origin", "*")
        c.Header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
        c.Header("Access-Control-Allow-Headers", "Content-Type, Authorization")
        
        if c.Request.Method == "OPTIONS" {
            c.AbortWithStatus(http.StatusOK)
            return
        }
        
        c.Next()
    })
    
    // User model
    type User struct {
        ID   int    `json:"id"`
        Name string `json:"name"`
        Age  int    `json:"age"`
    }
    
    // In-memory storage
    users := []User{
        {ID: 1, Name: "John", Age: 30},
        {ID: 2, Name: "Jane", Age: 25},
    }
    
    // Routes
    r.GET("/users", func(c *gin.Context) {
        c.JSON(http.StatusOK, gin.H{
            "success": true,
            "data":    users,
        })
    })
    
    r.GET("/users/:id", func(c *gin.Context) {
        id := c.Param("id")
        
        // Find user
        for _, user := range users {
            if fmt.Sprintf("%d", user.ID) == id {
                c.JSON(http.StatusOK, gin.H{
                    "success": true,
                    "data":    user,
                })
                return
            }
        }
        
        c.JSON(http.StatusNotFound, gin.H{
            "success": false,
            "error":   "User not found",
        })
    })
    
    r.POST("/users", func(c *gin.Context) {
        var user User
        if err := c.ShouldBindJSON(&user); err != nil {
            c.JSON(http.StatusBadRequest, gin.H{
                "success": false,
                "error":   err.Error(),
            })
            return
        }
        
        user.ID = len(users) + 1
        users = append(users, user)
        
        c.JSON(http.StatusCreated, gin.H{
            "success": true,
            "data":    user,
        })
    })
    
    r.PUT("/users/:id", func(c *gin.Context) {
        id := c.Param("id")
        
        var updatedUser User
        if err := c.ShouldBindJSON(&updatedUser); err != nil {
            c.JSON(http.StatusBadRequest, gin.H{
                "success": false,
                "error":   err.Error(),
            })
            return
        }
        
        // Find and update user
        for i, user := range users {
            if fmt.Sprintf("%d", user.ID) == id {
                users[i].Name = updatedUser.Name
                users[i].Age = updatedUser.Age
                c.JSON(http.StatusOK, gin.H{
                    "success": true,
                    "data":    users[i],
                })
                return
            }
        }
        
        c.JSON(http.StatusNotFound, gin.H{
            "success": false,
            "error":   "User not found",
        })
    })
    
    r.DELETE("/users/:id", func(c *gin.Context) {
        id := c.Param("id")
        
        // Find and delete user
        for i, user := range users {
            if fmt.Sprintf("%d", user.ID) == id {
                users = append(users[:i], users[i+1:]...)
                c.JSON(http.StatusOK, gin.H{
                    "success": true,
                    "message": "User deleted",
                })
                return
            }
        }
        
        c.JSON(http.StatusNotFound, gin.H{
            "success": false,
            "error":   "User not found",
        })
    })
    
    // Health check
    r.GET("/health", func(c *gin.Context) {
        c.JSON(http.StatusOK, gin.H{
            "success": true,
            "data": gin.H{
                "status":    "healthy",
                "timestamp": time.Now().Unix(),
                "framework": "gin",
            },
        })
    })
    
    // Start server
    fmt.Println("Gin server starting on :8080...")
    r.Run(":8080")
}

// Gorilla Mux example
func gorillaMuxExample() {
    // Create router
    r := mux.NewRouter()
    
    // Middleware
    r.Use(mux.MiddlewareFunc(func(next http.Handler) http.Handler {
        return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
            start := time.Now()
            
            // CORS headers
            w.Header().Set("Access-Control-Allow-Origin", "*")
            w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
            
            if r.Method == "OPTIONS" {
                w.WriteHeader(http.StatusOK)
                return
            }
            
            next.ServeHTTP(w, r)
            
            log.Printf("%s %s %v", r.Method, r.URL.Path, time.Since(start))
        })
    }))
    
    // User model
    type User struct {
        ID   int    `json:"id"`
        Name string `json:"name"`
        Age  int    `json:"age"`
    }
    
    // In-memory storage
    users := []User{
        {ID: 1, Name: "John", Age: 30},
        {ID: 2, Name: "Jane", Age: 25},
    }
    
    // Helper functions
    writeJSON := func(w http.ResponseWriter, statusCode int, data interface{}) {
        w.Header().Set("Content-Type", "application/json")
        w.WriteHeader(statusCode)
        json.NewEncoder(w).Encode(data)
    }
    
    // Routes
    r.HandleFunc("/users", func(w http.ResponseWriter, r *http.Request) {
        switch r.Method {
        case http.MethodGet:
            writeJSON(w, http.StatusOK, map[string]interface{}{
                "success": true,
                "data":    users,
            })
        case http.MethodPost:
            var user User
            if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
                writeJSON(w, http.StatusBadRequest, map[string]interface{}{
                    "success": false,
                    "error":   err.Error(),
                })
                return
            }
            
            user.ID = len(users) + 1
            users = append(users, user)
            
            writeJSON(w, http.StatusCreated, map[string]interface{}{
                "success": true,
                "data":    user,
            })
        default:
            writeJSON(w, http.StatusMethodNotAllowed, map[string]interface{}{
                "success": false,
                "error":   "Method not allowed",
            })
        }
    }).Methods(http.MethodGet, http.MethodPost)
    
    r.HandleFunc("/users/{id}", func(w http.ResponseWriter, r *http.Request) {
        vars := mux.Vars(r)
        id := vars["id"]
        
        switch r.Method {
        case http.MethodGet:
            for _, user := range users {
                if fmt.Sprintf("%d", user.ID) == id {
                    writeJSON(w, http.StatusOK, map[string]interface{}{
                        "success": true,
                        "data":    user,
                    })
                    return
                }
            }
            
            writeJSON(w, http.StatusNotFound, map[string]interface{}{
                "success": false,
                "error":   "User not found",
            })
            
        case http.MethodPut:
            var updatedUser User
            if err := json.NewDecoder(r.Body).Decode(&updatedUser); err != nil {
                writeJSON(w, http.StatusBadRequest, map[string]interface{}{
                    "success": false,
                    "error":   err.Error(),
                })
                return
            }
            
            for i, user := range users {
                if fmt.Sprintf("%d", user.ID) == id {
                    users[i].Name = updatedUser.Name
                    users[i].Age = updatedUser.Age
                    writeJSON(w, http.StatusOK, map[string]interface{}{
                        "success": true,
                        "data":    users[i],
                    })
                    return
                }
            }
            
            writeJSON(w, http.StatusNotFound, map[string]interface{}{
                "success": false,
                "error":   "User not found",
            })
            
        case http.MethodDelete:
            for i, user := range users {
                if fmt.Sprintf("%d", user.ID) == id {
                    users = append(users[:i], users[i+1:]...)
                    writeJSON(w, http.StatusOK, map[string]interface{}{
                        "success": true,
                        "message": "User deleted",
                    })
                    return
                }
            }
            
            writeJSON(w, http.StatusNotFound, map[string]interface{}{
                "success": false,
                "error":   "User not found",
            })
            
        default:
            writeJSON(w, http.StatusMethodNotAllowed, map[string]interface{}{
                "success": false,
                "error":   "Method not allowed",
            })
        }
    }).Methods(http.MethodGet, http.MethodPut, http.MethodDelete)
    
    // Health check
    r.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
        writeJSON(w, http.StatusOK, map[string]interface{}{
            "success": true,
            "data": map[string]interface{}{
                "status":    "healthy",
                "timestamp": time.Now().Unix(),
                "framework": "gorilla-mux",
            },
        })
    })
    
    // Start server
    fmt.Println("Gorilla Mux server starting on :8080...")
    log.Fatal(http.ListenAndServe(":8080", r))
}

// Chi router example
func chiExample() {
    // Create router
    r := chi.NewRouter()
    
    // Middleware
    r.Use(func(next http.Handler) http.Handler {
        return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
            start := time.Now()
            
            // CORS headers
            w.Header().Set("Access-Control-Allow-Origin", "*")
            w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
            
            if r.Method == "OPTIONS" {
                w.WriteHeader(http.StatusOK)
                return
            }
            
            next.ServeHTTP(w, r)
            
            log.Printf("%s %s %v", r.Method, r.URL.Path, time.Since(start))
        })
    })
    
    // User model
    type User struct {
        ID   int    `json:"id"`
        Name string `json:"name"`
        Age  int    `json:"age"`
    }
    
    // In-memory storage
    users := []User{
        {ID: 1, Name: "John", Age: 30},
        {ID: 2, Name: "Jane", Age: 25},
    }
    
    // Helper functions
    writeJSON := func(w http.ResponseWriter, statusCode int, data interface{}) {
        w.Header().Set("Content-Type", "application/json")
        w.WriteHeader(statusCode)
        json.NewEncoder(w).Encode(data)
    }
    
    // Routes
    r.Route("/users", func(r chi.Router) {
        r.Get("/", func(w http.ResponseWriter, r *http.Request) {
            writeJSON(w, http.StatusOK, map[string]interface{}{
                "success": true,
                "data":    users,
            })
        })
        
        r.Post("/", func(w http.ResponseWriter, r *http.Request) {
            var user User
            if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
                writeJSON(w, http.StatusBadRequest, map[string]interface{}{
                    "success": false,
                    "error":   err.Error(),
                })
                return
            }
            
            user.ID = len(users) + 1
            users = append(users, user)
            
            writeJSON(w, http.StatusCreated, map[string]interface{}{
                "success": true,
                "data":    user,
            })
        })
        
        r.Route("/{id}", func(r chi.Router) {
            r.Get("/", func(w http.ResponseWriter, r *http.Request) {
                id := chi.URLParam(r, "id")
                
                for _, user := range users {
                    if fmt.Sprintf("%d", user.ID) == id {
                        writeJSON(w, http.StatusOK, map[string]interface{}{
                            "success": true,
                            "data":    user,
                        })
                        return
                    }
                }
                
                writeJSON(w, http.StatusNotFound, map[string]interface{}{
                    "success": false,
                    "error":   "User not found",
                })
            })
            
            r.Put("/", func(w http.ResponseWriter, r *http.Request) {
                id := chi.URLParam(r, "id")
                
                var updatedUser User
                if err := json.NewDecoder(r.Body).Decode(&updatedUser); err != nil {
                    writeJSON(w, http.StatusBadRequest, map[string]interface{}{
                        "success": false,
                        "error":   err.Error(),
                    })
                    return
                }
                
                for i, user := range users {
                    if fmt.Sprintf("%d", user.ID) == id {
                        users[i].Name = updatedUser.Name
                        users[i].Age = updatedUser.Age
                        writeJSON(w, http.StatusOK, map[string]interface{}{
                            "success": true,
                            "data":    users[i],
                        })
                        return
                    }
                }
                
                writeJSON(w, http.StatusNotFound, map[string]interface{}{
                    "success": false,
                    "error":   "User not found",
                })
            })
            
            r.Delete("/", func(w http.ResponseWriter, r *http.Request) {
                id := chi.URLParam(r, "id")
                
                for i, user := range users {
                    if fmt.Sprintf("%d", user.ID) == id {
                        users = append(users[:i], users[i+1:]...)
                        writeJSON(w, http.StatusOK, map[string]interface{}{
                            "success": true,
                            "message": "User deleted",
                        })
                        return
                    }
                }
                
                writeJSON(w, http.StatusNotFound, map[string]interface{}{
                    "success": false,
                    "error":   "User not found",
                })
            })
        })
    })
    
    // Health check
    r.Get("/health", func(w http.ResponseWriter, r *http.Request) {
        writeJSON(w, http.StatusOK, map[string]interface{}{
            "success": true,
            "data": map[string]interface{}{
                "status":    "healthy",
                "timestamp": time.Now().Unix(),
                "framework": "chi",
            },
        })
    })
    
    // Start server
    fmt.Println("Chi server starting on :8080...")
    log.Fatal(http.ListenAndServe(":8080", r))
}

// Framework comparison
func frameworkComparison() {
    fmt.Println("Go Web Framework Comparison:")
    fmt.Println("1. Gin - Fast, minimalist, good performance")
    fmt.Println("2. Gorilla Mux - Powerful router, good for complex APIs")
    fmt.Println("3. Chi - Lightweight, expressive, good middleware")
    fmt.Println("4. Echo - High performance, extensive features")
    fmt.Println("5. Fiber - Express.js inspired, very fast")
}
```

## Database Integration

### Working with Databases
```go
package main

import (
    "database/sql"
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "time"
    
    _ "github.com/lib/pq"      // PostgreSQL
    _ "github.com/go-sql-driver/mysql" // MySQL
    _ "github.com/mattn/go-sqlite3" // SQLite
    
    "gorm.io/gorm"
    "gorm.io/driver/postgres"
    "gorm.io/driver/mysql"
    "gorm.io/driver/sqlite"
)

// Database models
type User struct {
    ID        uint      `gorm:"primaryKey" json:"id"`
    Name      string    `gorm:"size:100;not null" json:"name"`
    Email     string    `gorm:"size:100;uniqueIndex;not null" json:"email"`
    Age       int       `gorm:"default:0" json:"age"`
    CreatedAt time.Time `json:"created_at"`
    UpdatedAt time.Time `json:"updated_at"`
}

type Post struct {
    ID       uint      `gorm:"primaryKey" json:"id"`
    Title    string    `gorm:"size:200;not null" json:"title"`
    Content  string    `gorm:"type:text" json:"content"`
    UserID   uint      `gorm:"not null" json:"user_id"`
    User     User      `gorm:"foreignKey:UserID" json:"user,omitempty"`
    CreatedAt time.Time `json:"created_at"`
    UpdatedAt time.Time `json:"updated_at"`
}

// Raw SQL database operations
type RawSQLService struct {
    db *sql.DB
}

func NewRawSQLService(driver, dsn string) (*RawSQLService, error) {
    db, err := sql.Open(driver, dsn)
    if err != nil {
        return nil, err
    }
    
    if err := db.Ping(); err != nil {
        return nil, err
    }
    
    return &RawSQLService{db: db}, nil
}

func (rs *RawSQLService) CreateUser(name, email string, age int) (*User, error) {
    query := `INSERT INTO users (name, email, age, created_at, updated_at) 
              VALUES ($1, $2, $3, $4, $5) RETURNING id, name, email, age, created_at, updated_at`
    
    var user User
    err := rs.db.QueryRow(query, name, email, age, time.Now(), time.Now()).Scan(
        &user.ID, &user.Name, &user.Email, &user.Age, &user.CreatedAt, &user.UpdatedAt)
    
    if err != nil {
        return nil, err
    }
    
    return &user, nil
}

func (rs *RawSQLService) GetUser(id uint) (*User, error) {
    query := `SELECT id, name, email, age, created_at, updated_at FROM users WHERE id = $1`
    
    var user User
    err := rs.db.QueryRow(query, id).Scan(
        &user.ID, &user.Name, &user.Email, &user.Age, &user.CreatedAt, &user.UpdatedAt)
    
    if err != nil {
        return nil, err
    }
    
    return &user, nil
}

func (rs *RawSQLService) UpdateUser(id uint, name, email string, age int) (*User, error) {
    query := `UPDATE users SET name = $1, email = $2, age = $3, updated_at = $4 
              WHERE id = $5 RETURNING id, name, email, age, created_at, updated_at`
    
    var user User
    err := rs.db.QueryRow(query, name, email, age, time.Now(), id).Scan(
        &user.ID, &user.Name, &user.Email, &user.Age, &user.CreatedAt, &user.UpdatedAt)
    
    if err != nil {
        return nil, err
    }
    
    return &user, nil
}

func (rs *RawSQLService) DeleteUser(id uint) error {
    query := `DELETE FROM users WHERE id = $1`
    
    result, err := rs.db.Exec(query, id)
    if err != nil {
        return err
    }
    
    rowsAffected, err := result.RowsAffected()
    if err != nil {
        return err
    }
    
    if rowsAffected == 0 {
        return fmt.Errorf("user not found")
    }
    
    return nil
}

func (rs *RawSQLService) GetAllUsers() ([]User, error) {
    query := `SELECT id, name, email, age, created_at, updated_at FROM users ORDER BY created_at DESC`
    
    rows, err := rs.db.Query(query)
    if err != nil {
        return nil, err
    }
    defer rows.Close()
    
    var users []User
    for rows.Next() {
        var user User
        err := rows.Scan(&user.ID, &user.Name, &user.Email, &user.Age, &user.CreatedAt, &user.UpdatedAt)
        if err != nil {
            return nil, err
        }
        users = append(users, user)
    }
    
    return users, nil
}

func (rs *RawSQLService) Close() error {
    return rs.db.Close()
}

// GORM ORM operations
type GORMService struct {
    db *gorm.DB
}

func NewGORMService(driver, dsn string) (*GORMService, error) {
    var dialector gorm.Dialector
    
    switch driver {
    case "postgres":
        dialector = postgres.Open(dsn)
    case "mysql":
        dialector = mysql.Open(dsn)
    case "sqlite":
        dialector = sqlite.Open(dsn)
    default:
        return nil, fmt.Errorf("unsupported driver: %s", driver)
    }
    
    db, err := gorm.Open(dialector, &gorm.Config{})
    if err != nil {
        return nil, err
    }
    
    // Auto migrate
    err = db.AutoMigrate(&User{}, &Post{})
    if err != nil {
        return nil, err
    }
    
    return &GORMService{db: db}, nil
}

func (gs *GORMService) CreateUser(name, email string, age int) (*User, error) {
    user := User{
        Name:  name,
        Email: email,
        Age:   age,
    }
    
    result := gs.db.Create(&user)
    if result.Error != nil {
        return nil, result.Error
    }
    
    return &user, nil
}

func (gs *GORMService) GetUser(id uint) (*User, error) {
    var user User
    result := gs.db.First(&user, id)
    if result.Error != nil {
        return nil, result.Error
    }
    
    return &user, nil
}

func (gs *GORMService) GetUserByEmail(email string) (*User, error) {
    var user User
    result := gs.db.Where("email = ?", email).First(&user)
    if result.Error != nil {
        return nil, result.Error
    }
    
    return &user, nil
}

func (gs *GORMService) UpdateUser(id uint, name, email string, age int) (*User, error) {
    var user User
    result := gs.db.First(&user, id)
    if result.Error != nil {
        return nil, result.Error
    }
    
    updates := map[string]interface{}{}
    if name != "" {
        updates["name"] = name
    }
    if email != "" {
        updates["email"] = email
    }
    if age >= 0 {
        updates["age"] = age
    }
    
    result = gs.db.Model(&user).Updates(updates)
    if result.Error != nil {
        return nil, result.Error
    }
    
    // Refresh to get updated data
    result = gs.db.First(&user, id)
    if result.Error != nil {
        return nil, result.Error
    }
    
    return &user, nil
}

func (gs *GORMService) DeleteUser(id uint) error {
    result := gs.db.Delete(&User{}, id)
    return result.Error
}

func (gs *GORMService) GetAllUsers() ([]User, error) {
    var users []User
    result := gs.db.Find(&users)
    return users, result.Error
}

func (gs *GORMService) GetUsersWithPagination(page, limit int) ([]User, int64, error) {
    var users []User
    var total int64
    
    // Count total users
    result := gs.db.Model(&User{}).Count(&total)
    if result.Error != nil {
        return nil, 0, result.Error
    }
    
    // Get paginated users
    offset := (page - 1) * limit
    result = gs.db.Offset(offset).Limit(limit).Find(&users)
    if result.Error != nil {
        return nil, 0, result.Error
    }
    
    return users, total, nil
}

func (gs *GORMService) SearchUsers(query string) ([]User, error) {
    var users []User
    searchPattern := "%" + query + "%"
    
    result := gs.db.Where("name ILIKE ? OR email ILIKE ?", searchPattern, searchPattern).Find(&users)
    return users, result.Error
}

func (gs *GORMService) CreatePost(title, content string, userID uint) (*Post, error) {
    post := Post{
        Title:   title,
        Content: content,
        UserID:  userID,
    }
    
    result := gs.db.Create(&post)
    if result.Error != nil {
        return nil, result.Error
    }
    
    // Load user relationship
    result = gs.db.Preload("User").First(&post, post.ID)
    if result.Error != nil {
        return nil, result.Error
    }
    
    return &post, nil
}

func (gs *GORMService) GetPost(id uint) (*Post, error) {
    var post Post
    result := gs.db.Preload("User").First(&post, id)
    return &post, result.Error
}

func (gs *GORMService) GetUserPosts(userID uint) ([]Post, error) {
    var posts []Post
    result := gs.db.Preload("User").Where("user_id = ?", userID).Find(&posts)
    return posts, result.Error
}

func (gs *GORMService) Close() error {
    sqlDB, err := gs.db.DB()
    if err != nil {
        return err
    }
    return sqlDB.Close()
}

// HTTP handlers with database
type DatabaseHandler struct {
    service interface {
        CreateUser(name, email string, age int) (*User, error)
        GetUser(id uint) (*User, error)
        UpdateUser(id uint, name, email string, age int) (*User, error)
        DeleteUser(id uint) error
        GetAllUsers() ([]User, error)
        Close() error
    }
}

func NewDatabaseHandler(service interface{}) *DatabaseHandler {
    return &DatabaseHandler{service: service}
}

func (dh *DatabaseHandler) CreateUser(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    user, err := dh.service.CreateUser(req.Name, req.Email, req.Age)
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (dh *DatabaseHandler) GetUser(w http.ResponseWriter, r *http.Request) {
    idStr := r.URL.Query().Get("id")
    if idStr == "" {
        http.Error(w, "Missing id parameter", http.StatusBadRequest)
        return
    }
    
    var id uint
    _, err := fmt.Sscanf(idStr, "%d", &id)
    if err != nil {
        http.Error(w, "Invalid id parameter", http.StatusBadRequest)
        return
    }
    
    user, err := dh.service.GetUser(id)
    if err != nil {
        http.Error(w, err.Error(), http.StatusNotFound)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (dh *DatabaseHandler) GetAllUsers(w http.ResponseWriter, r *http.Request) {
    users, err := dh.service.GetAllUsers()
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(users)
}

// Database connection examples
func databaseExamples() {
    // SQLite example
    fmt.Println("SQLite example:")
    sqliteService, err := NewGORMService("sqlite", "test.db")
    if err != nil {
        log.Fatal("SQLite connection error:", err)
    }
    defer sqliteService.Close()
    
    user, err := sqliteService.CreateUser("John Doe", "john@example.com", 30)
    if err != nil {
        log.Fatal("Create user error:", err)
    }
    
    fmt.Printf("Created user: %+v\n", user)
    
    // PostgreSQL example (requires actual connection string)
    fmt.Println("PostgreSQL example:")
    // pgService, err := NewGORMService("postgres", "host=localhost user=postgres dbname=test password=password port=5432 sslmode=disable")
    
    // MySQL example (requires actual connection string)
    fmt.Println("MySQL example:")
    // mysqlService, err := NewGORMService("mysql", "user:password@tcp(localhost:3306)/dbname?charset=utf8mb4&parseTime=True&loc=Local")
}

// Database server
func runDatabaseServer() {
    // Use SQLite for demo
    service, err := NewGORMService("sqlite", "test.db")
    if err != nil {
        log.Fatal("Database connection error:", err)
    }
    defer service.Close()
    
    handler := NewDatabaseHandler(service)
    
    mux := http.NewServeMux()
    mux.HandleFunc("/users", func(w http.ResponseWriter, r *http.Request) {
        switch r.Method {
        case http.MethodGet:
            handler.GetAllUsers(w, r)
        case http.MethodPost:
            handler.CreateUser(w, r)
        default:
            http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
        }
    })
    
    mux.HandleFunc("/user", handler.GetUser)
    
    fmt.Println("Database server starting on :8080...")
    log.Fatal(http.ListenAndServe(":8080", mux))
}
```

## Web Security

### Security Best Practices
```go
package main

import (
    "crypto/rand"
    "crypto/subtle"
    "encoding/base64"
    "encoding/json"
    "fmt"
    "html/template"
    "log"
    "net/http"
    "regexp"
    "strings"
    "time"
    
    "golang.org/x/crypto/bcrypt"
    "golang.org/x/crypto/argon2"
    "github.com/golang-jwt/jwt/v4"
)

// Security utilities
type SecurityUtils struct {
    jwtSecret []byte
}

func NewSecurityUtils() *SecurityUtils {
    return &SecurityUtils{
        jwtSecret: []byte("your-secret-key-change-in-production"),
    }
}

// Password hashing with bcrypt
func (su *SecurityUtils) HashPassword(password string) (string, error) {
    bytes, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
    return string(bytes), err
}

func (su *SecurityUtils) CheckPasswordHash(password, hash string) bool {
    err := bcrypt.CompareHashAndPassword([]byte(hash), []byte(password))
    return err == nil
}

// Password hashing with Argon2 (more secure)
func (su *SecurityUtils) HashPasswordArgon2(password string) (string, error) {
    salt := make([]byte, 16)
    if _, err := rand.Read(salt); err != nil {
        return "", err
    }
    
    hash := argon2.IDKey([]byte(password), salt, 1, 64*1024, 4, 32)
    
    // Combine salt and hash
    combined := append(salt, hash...)
    return base64.StdEncoding.EncodeToString(combined), nil
}

func (su *SecurityUtils) CheckPasswordArgon2(password, encodedHash string) bool {
    combined, err := base64.StdEncoding.DecodeString(encodedHash)
    if err != nil || len(combined) < 16 {
        return false
    }
    
    salt := combined[:16]
    hash := combined[16:]
    
    computedHash := argon2.IDKey([]byte(password), salt, 1, 64*1024, 4, 32)
    
    return subtle.ConstantTimeCompare(hash, computedHash) == 1
}

// JWT token handling
type Claims struct {
    UserID uint   `json:"user_id"`
    Email  string `json:"email"`
    jwt.RegisteredClaims
}

func (su *SecurityUtils) GenerateToken(userID uint, email string) (string, error) {
    claims := &Claims{
        UserID: userID,
        Email:  email,
        RegisteredClaims: jwt.RegisteredClaims{
            ExpiresAt: jwt.NewNumericDate(time.Now().Add(24 * time.Hour)),
            IssuedAt:  jwt.NewNumericDate(time.Now()),
            NotBefore: jwt.NewNumericDate(time.Now()),
            Issuer:    "go-web-app",
            Subject:   fmt.Sprintf("%d", userID),
        },
    }
    
    token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
    return token.SignedString(su.jwtSecret)
}

func (su *SecurityUtils) ValidateToken(tokenString string) (*Claims, error) {
    token, err := jwt.ParseWithClaims(tokenString, &Claims{}, func(token *jwt.Token) (interface{}, error) {
        return su.jwtSecret, nil
    })
    
    if err != nil {
        return nil, err
    }
    
    if claims, ok := token.Claims.(*Claims); ok && token.Valid {
        return claims, nil
    }
    
    return nil, fmt.Errorf("invalid token")
}

// CSRF protection
type CSRFProtection struct {
    tokens map[string]string
    mu     sync.RWMutex
}

func NewCSRFProtection() *CSRFProtection {
    return &CSRFProtection{
        tokens: make(map[string]string),
    }
}

func (cp *CSRFProtection) GenerateToken(sessionID string) string {
    cp.mu.Lock()
    defer cp.mu.Unlock()
    
    token := generateRandomToken(32)
    cp.tokens[sessionID] = token
    return token
}

func (cp *CSRFProtection) ValidateToken(sessionID, token string) bool {
    cp.mu.RLock()
    defer cp.mu.RUnlock()
    
    storedToken, exists := cp.tokens[sessionID]
    return exists && storedToken == token
}

func (cp *CSRFProtection) RemoveToken(sessionID string) {
    cp.mu.Lock()
    defer cp.mu.Unlock()
    
    delete(cp.tokens, sessionID)
}

func generateRandomToken(length int) string {
    bytes := make([]byte, length)
    rand.Read(bytes)
    return base64.URLEncoding.EncodeToString(bytes)
}

// Input validation
type Validator struct {
    emailRegex *regexp.Regexp
    nameRegex  *regexp.Regexp
}

func NewValidator() *Validator {
    return &Validator{
        emailRegex: regexp.MustCompile(`^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`),
        nameRegex:  regexp.MustCompile(`^[a-zA-Z\s]{2,50}$`),
    }
}

func (v *Validator) ValidateEmail(email string) bool {
    return v.emailRegex.MatchString(email)
}

func (v *Validator) ValidateName(name string) bool {
    return v.nameRegex.MatchString(name)
}

func (v *Validator) ValidatePassword(password string) error {
    if len(password) < 8 {
        return fmt.Errorf("password must be at least 8 characters long")
    }
    
    hasUpper := regexp.MustCompile(`[A-Z]`).MatchString(password)
    hasLower := regexp.MustCompile(`[a-z]`).MatchString(password)
    hasNumber := regexp.MustCompile(`[0-9]`).MatchString(password)
    hasSpecial := regexp.MustCompile(`[!@#$%^&*(),.?":{}|<>]`).MatchString(password)
    
    if !hasUpper {
        return fmt.Errorf("password must contain at least one uppercase letter")
    }
    
    if !hasLower {
        return fmt.Errorf("password must contain at least one lowercase letter")
    }
    
    if !hasNumber {
        return fmt.Errorf("password must contain at least one number")
    }
    
    if !hasSpecial {
        return fmt.Errorf("password must contain at least one special character")
    }
    
    return nil
}

// XSS protection
func escapeHTML(input string) string {
    // Simple HTML escaping (use html/template in production)
    input = strings.ReplaceAll(input, "&", "&amp;")
    input = strings.ReplaceAll(input, "<", "&lt;")
    input = strings.ReplaceAll(input, ">", "&gt;")
    input = strings.ReplaceAll(input, "\"", "&quot;")
    input = strings.ReplaceAll(input, "'", "&#39;")
    return input
}

// SQL injection protection
func sanitizeSQLInput(input string) string {
    // Basic sanitization (use prepared statements in production)
    input = strings.ReplaceAll(input, "'", "''")
    input = strings.ReplaceAll(input, ";", "")
    input = strings.ReplaceAll(input, "--", "")
    input = strings.ReplaceAll(input, "/*", "")
    input = strings.ReplaceAll(input, "*/", "")
    return input
}

// Rate limiting
type RateLimiter struct {
    requests map[string][]time.Time
    limit    int
    window   time.Duration
    mu       sync.RWMutex
}

func NewRateLimiter(limit int, window time.Duration) *RateLimiter {
    return &RateLimiter{
        requests: make(map[string][]time.Time),
        limit:    limit,
        window:   window,
    }
}

func (rl *RateLimiter) Allow(key string) bool {
    rl.mu.Lock()
    defer rl.mu.Unlock()
    
    now := time.Now()
    
    // Clean old requests
    if requests, exists := rl.requests[key]; exists {
        var validRequests []time.Time
        for _, req := range requests {
            if now.Sub(req) < rl.window {
                validRequests = append(validRequests, req)
            }
        }
        rl.requests[key] = validRequests
    }
    
    // Check limit
    if len(rl.requests[key]) >= rl.limit {
        return false
    }
    
    // Add new request
    rl.requests[key] = append(rl.requests[key], now)
    return true
}

// Security middleware
type SecurityMiddleware struct {
    security   *SecurityUtils
    validator  *Validator
    csrf       *CSRFProtection
    rateLimiter *RateLimiter
}

func NewSecurityMiddleware() *SecurityMiddleware {
    return &SecurityMiddleware{
        security:    NewSecurityUtils(),
        validator:   NewValidator(),
        csrf:        NewCSRFProtection(),
        rateLimiter: NewRateLimiter(100, time.Minute),
    }
}

func (sm *SecurityMiddleware) SecurityHeaders(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        // Security headers
        w.Header().Set("X-Content-Type-Options", "nosniff")
        w.Header().Set("X-Frame-Options", "DENY")
        w.Header().Set("X-XSS-Protection", "1; mode=block")
        w.Header().Set("Strict-Transport-Security", "max-age=31536000; includeSubDomains")
        w.Header().Set("Content-Security-Policy", "default-src 'self'")
        
        next.ServeHTTP(w, r)
    })
}

func (sm *SecurityMiddleware) RateLimit(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        clientIP := r.RemoteAddr
        
        if !sm.rateLimiter.Allow(clientIP) {
            http.Error(w, "Rate limit exceeded", http.StatusTooManyRequests)
            return
        }
        
        next.ServeHTTP(w, r)
    })
}

func (sm *SecurityMiddleware) ValidateInput(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        // Validate query parameters
        for key, values := range r.URL.Query() {
            for _, value := range values {
                if strings.Contains(value, "<script>") || strings.Contains(value, "javascript:") {
                    http.Error(w, "Invalid input detected", http.StatusBadRequest)
                    return
                }
            }
        }
        
        next.ServeHTTP(w, r)
    })
}

// Authentication handlers
type AuthHandler struct {
    security  *SecurityUtils
    validator *Validator
    users     map[string]User // In-memory user store (use database in production)
}

type User struct {
    ID       uint   `json:"id"`
    Name     string `json:"name"`
    Email    string `json:"email"`
    Password string `json:"-"`
}

func NewAuthHandler() *AuthHandler {
    return &AuthHandler{
        security:  NewSecurityUtils(),
        validator: NewValidator(),
        users:     make(map[string]User),
    }
}

func (ah *AuthHandler) Register(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Name     string `json:"name"`
        Email    string `json:"email"`
        Password string `json:"password"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    // Validate input
    if !ah.validator.ValidateName(req.Name) {
        http.Error(w, "Invalid name", http.StatusBadRequest)
        return
    }
    
    if !ah.validator.ValidateEmail(req.Email) {
        http.Error(w, "Invalid email", http.StatusBadRequest)
        return
    }
    
    if err := ah.validator.ValidatePassword(req.Password); err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }
    
    // Check if user already exists
    if _, exists := ah.users[req.Email]; exists {
        http.Error(w, "User already exists", http.StatusConflict)
        return
    }
    
    // Hash password
    hashedPassword, err := ah.security.HashPassword(req.Password)
    if err != nil {
        http.Error(w, "Failed to hash password", http.StatusInternalServerError)
        return
    }
    
    // Create user
    user := User{
        ID:       uint(len(ah.users) + 1),
        Name:     req.Name,
        Email:    req.Email,
        Password: hashedPassword,
    }
    
    ah.users[req.Email] = user
    
    // Generate token
    token, err := ah.security.GenerateToken(user.ID, user.Email)
    if err != nil {
        http.Error(w, "Failed to generate token", http.StatusInternalServerError)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]interface{}{
        "success": true,
        "token":   token,
        "user": map[string]interface{}{
            "id":    user.ID,
            "name":  user.Name,
            "email": user.Email,
        },
    })
}

func (ah *AuthHandler) Login(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Email    string `json:"email"`
        Password string `json:"password"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    // Find user
    user, exists := ah.users[req.Email]
    if !exists {
        http.Error(w, "Invalid credentials", http.StatusUnauthorized)
        return
    }
    
    // Check password
    if !ah.security.CheckPasswordHash(req.Password, user.Password) {
        http.Error(w, "Invalid credentials", http.StatusUnauthorized)
        return
    }
    
    // Generate token
    token, err := ah.security.GenerateToken(user.ID, user.Email)
    if err != nil {
        http.Error(w, "Failed to generate token", http.StatusInternalServerError)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]interface{}{
        "success": true,
        "token":   token,
        "user": map[string]interface{}{
            "id":    user.ID,
            "name":  user.Name,
            "email": user.Email,
        },
    })
}

func (ah *AuthHandler) Protected(w http.ResponseWriter, r *http.Request) {
    // Extract token from Authorization header
    authHeader := r.Header.Get("Authorization")
    if authHeader == "" {
        http.Error(w, "Authorization header required", http.StatusUnauthorized)
        return
    }
    
    tokenString := strings.TrimPrefix(authHeader, "Bearer ")
    
    // Validate token
    claims, err := ah.security.ValidateToken(tokenString)
    if err != nil {
        http.Error(w, "Invalid token", http.StatusUnauthorized)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]interface{}{
        "success": true,
        "message": "Access granted",
        "user": map[string]interface{}{
            "id":    claims.UserID,
            "email": claims.Email,
        },
    })
}

// Security server setup
func runSecurityServer() {
    authHandler := NewAuthHandler()
    securityMiddleware := NewSecurityMiddleware()
    
    mux := http.NewServeMux()
    
    // Public routes
    mux.HandleFunc("/register", authHandler.Register)
    mux.HandleFunc("/login", authHandler.Login)
    
    // Protected routes
    mux.HandleFunc("/protected", authHandler.Protected)
    
    // Apply security middleware
    handler := securityMiddleware.SecurityHeaders(
        securityMiddleware.RateLimit(
            securityMiddleware.ValidateInput(mux)))
    
    fmt.Println("Security server starting on :8080...")
    log.Fatal(http.ListenAndServe(":8080", handler))
}

// Security examples
func securityExamples() {
    security := NewSecurityUtils()
    validator := NewValidator()
    
    // Password hashing
    password := "SecurePassword123!"
    hashed, err := security.HashPassword(password)
    if err != nil {
        log.Fatal("Password hashing error:", err)
    }
    
    fmt.Printf("Original password: %s\n", password)
    fmt.Printf("Hashed password: %s\n", hashed)
    
    // Password verification
    isValid := security.CheckPasswordHash(password, hashed)
    fmt.Printf("Password verification: %t\n", isValid)
    
    // JWT token
    token, err := security.GenerateToken(1, "user@example.com")
    if err != nil {
        log.Fatal("Token generation error:", err)
    }
    
    fmt.Printf("JWT token: %s\n", token)
    
    claims, err := security.ValidateToken(token)
    if err != nil {
        log.Fatal("Token validation error:", err)
    }
    
    fmt.Printf("Token claims: UserID=%d, Email=%s\n", claims.UserID, claims.Email)
    
    // Input validation
    testEmail := "user@example.com"
    fmt.Printf("Email validation (%s): %t\n", testEmail, validator.ValidateEmail(testEmail))
    
    testName := "John Doe"
    fmt.Printf("Name validation (%s): %t\n", testName, validator.ValidateName(testName))
    
    // Password validation
    err = validator.ValidatePassword("SecurePass123!")
    if err != nil {
        fmt.Printf("Password validation error: %v\n", err)
    } else {
        fmt.Println("Password is valid")
    }
    
    // XSS protection
    maliciousInput := "<script>alert('xss')</script>"
    escaped := escapeHTML(maliciousInput)
    fmt.Printf("XSS protection: %s -> %s\n", maliciousInput, escaped)
    
    // Rate limiting
    rateLimiter := NewRateLimiter(5, time.Minute)
    clientIP := "127.0.0.1"
    
    for i := 0; i < 7; i++ {
        allowed := rateLimiter.Allow(clientIP)
        fmt.Printf("Request %d allowed: %t\n", i+1, allowed)
    }
}
```

## Summary

Go web programming provides:

**HTTP Fundamentals:**
- Built-in net/http package
- Simple server setup
- Request/response handling
- Middleware support
- Client functionality

**RESTful APIs:**
- CRUD operations
- JSON handling
- Route parameters
- Status codes
- Error handling

**Web Frameworks:**
- Gin (fast, minimalist)
- Gorilla Mux (powerful)
- Chi (lightweight)
- Echo (feature-rich)
- Fiber (Express.js-like)

**Database Integration:**
- Raw SQL operations
- GORM ORM
- Connection pooling
- Migrations
- Relationships

**Security Features:**
- Password hashing
- JWT authentication
- CSRF protection
- Input validation
- Rate limiting
- Security headers

**Key Features:**
- High performance
- Concurrency support
- Standard library strength
- Rich ecosystem
- Type safety

**Best Practices:**
- Use proper middleware
- Validate all inputs
- Implement authentication
- Handle errors gracefully
- Use HTTPS in production

**Common Use Cases:**
- REST APIs
- Microservices
- Web applications
- Real-time services
- File servers

Go's web programming capabilities provide excellent performance, safety, and simplicity for building modern web applications and APIs.
