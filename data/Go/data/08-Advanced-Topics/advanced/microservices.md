# Go Microservices

## Microservice Architecture

### Service Design Principles
```go
package main

import (
    "context"
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "time"
    
    "github.com/gorilla/mux"
    "google.golang.org/grpc"
    pb "google.golang.org/grpc/examples/helloworld/helloworld"
)

// Service interface
type UserService interface {
    CreateUser(ctx context.Context, user *User) (*User, error)
    GetUser(ctx context.Context, id string) (*User, error)
    UpdateUser(ctx context.Context, user *User) (*User, error)
    DeleteUser(ctx context.Context, id string) error)
    ListUsers(ctx context.Context, filter *UserFilter) ([]*User, error)
}

// User model
type User struct {
    ID        string    `json:"id" bson:"_id"`
    Name      string    `json:"name" bson:"name"`
    Email     string    `json:"email" bson:"email"`
    Age       int       `json:"age" bson:"age"`
    CreatedAt time.Time `json:"created_at" bson:"created_at"`
    UpdatedAt time.Time `json:"updated_at" bson:"updated_at"`
}

type UserFilter struct {
    Name   string `json:"name"`
    Email  string `json:"email"`
    Limit  int    `json:"limit"`
    Offset int    `json:"offset"`
}

// Service implementation
type userService struct {
    repository UserRepository
    events     EventPublisher
    cache      Cache
    logger     Logger
}

func NewUserService(repo UserRepository, events EventPublisher, cache Cache, logger Logger) UserService {
    return &userService{
        repository: repo,
        events:     events,
        cache:      cache,
        logger:     logger,
    }
}

func (s *userService) CreateUser(ctx context.Context, user *User) (*User, error) {
    // Validate input
    if err := s.validateUser(user); err != nil {
        s.logger.Error("User validation failed", "error", err)
        return nil, err
    }
    
    // Check for duplicate email
    existing, err := s.repository.FindByEmail(ctx, user.Email)
    if err != nil && err != ErrNotFound {
        return nil, fmt.Errorf("failed to check existing user: %w", err)
    }
    if existing != nil {
        return nil, fmt.Errorf("user with email %s already exists", user.Email)
    }
    
    // Create user
    user.ID = generateID()
    user.CreatedAt = time.Now()
    user.UpdatedAt = time.Now()
    
    if err := s.repository.Create(ctx, user); err != nil {
        s.logger.Error("Failed to create user", "error", err)
        return nil, fmt.Errorf("failed to create user: %w", err)
    }
    
    // Publish event
    event := &UserCreatedEvent{
        UserID:    user.ID,
        Name:      user.Name,
        Email:     user.Email,
        Timestamp: time.Now(),
    }
    
    if err := s.events.Publish(ctx, "user.created", event); err != nil {
        s.logger.Warn("Failed to publish user created event", "error", err)
        // Don't fail the operation if event publishing fails
    }
    
    // Invalidate cache
    s.cache.Delete(fmt.Sprintf("user:%s", user.ID))
    
    s.logger.Info("User created successfully", "user_id", user.ID)
    return user, nil
}

func (s *userService) GetUser(ctx context.Context, id string) (*User, error) {
    // Try cache first
    cacheKey := fmt.Sprintf("user:%s", id)
    if user, err := s.cache.Get(cacheKey); err == nil {
        return user.(*User), nil
    }
    
    // Get from repository
    user, err := s.repository.FindByID(ctx, id)
    if err != nil {
        if err == ErrNotFound {
            return nil, fmt.Errorf("user not found")
        }
        return nil, fmt.Errorf("failed to get user: %w", err)
    }
    
    // Cache the result
    s.cache.Set(cacheKey, user, 5*time.Minute)
    
    return user, nil
}

func (s *userService) UpdateUser(ctx context.Context, user *User) (*User, error) {
    // Validate input
    if err := s.validateUser(user); err != nil {
        return nil, err
    }
    
    // Check if user exists
    existing, err := s.repository.FindByID(ctx, user.ID)
    if err != nil {
        if err == ErrNotFound {
            return nil, fmt.Errorf("user not found")
        }
        return nil, fmt.Errorf("failed to get user: %w", err)
    }
    
    // Update fields
    if user.Name != "" {
        existing.Name = user.Name
    }
    if user.Email != "" {
        existing.Email = user.Email
    }
    if user.Age > 0 {
        existing.Age = user.Age
    }
    existing.UpdatedAt = time.Now()
    
    // Save to repository
    if err := s.repository.Update(ctx, existing); err != nil {
        return nil, fmt.Errorf("failed to update user: %w", err)
    }
    
    // Publish event
    event := &UserUpdatedEvent{
        UserID:    existing.ID,
        Name:      existing.Name,
        Email:     existing.Email,
        Timestamp: time.Now(),
    }
    
    if err := s.events.Publish(ctx, "user.updated", event); err != nil {
        s.logger.Warn("Failed to publish user updated event", "error", err)
    }
    
    // Invalidate cache
    s.cache.Delete(fmt.Sprintf("user:%s", existing.ID))
    
    s.logger.Info("User updated successfully", "user_id", existing.ID)
    return existing, nil
}

func (s *userService) DeleteUser(ctx context.Context, id string) error {
    // Check if user exists
    _, err := s.repository.FindByID(ctx, id)
    if err != nil {
        if err == ErrNotFound {
            return fmt.Errorf("user not found")
        }
        return fmt.Errorf("failed to get user: %w", err)
    }
    
    // Delete from repository
    if err := s.repository.Delete(ctx, id); err != nil {
        return fmt.Errorf("failed to delete user: %w", err)
    }
    
    // Publish event
    event := &UserDeletedEvent{
        UserID:    id,
        Timestamp: time.Now(),
    }
    
    if err := s.events.Publish(ctx, "user.deleted", event); err != nil {
        s.logger.Warn("Failed to publish user deleted event", "error", err)
    }
    
    // Invalidate cache
    s.cache.Delete(fmt.Sprintf("user:%s", id))
    
    s.logger.Info("User deleted successfully", "user_id", id)
    return nil
}

func (s *userService) ListUsers(ctx context.Context, filter *UserFilter) ([]*User, error) {
    users, err := s.repository.FindAll(ctx, filter)
    if err != nil {
        return nil, fmt.Errorf("failed to list users: %w", err)
    }
    
    return users, nil
}

func (s *userService) validateUser(user *User) error {
    if user.Name == "" {
        return fmt.Errorf("name is required")
    }
    if user.Email == "" {
        return fmt.Errorf("email is required")
    }
    if user.Age < 0 || user.Age > 120 {
        return fmt.Errorf("invalid age")
    }
    return nil
}

// Repository interface
type UserRepository interface {
    Create(ctx context.Context, user *User) error
    FindByID(ctx context.Context, id string) (*User, error)
    FindByEmail(ctx context.Context, email string) (*User, error)
    Update(ctx context.Context, user *User) error
    Delete(ctx context.Context, id string) error
    FindAll(ctx context.Context, filter *UserFilter) ([]*User, error)
}

// Event publisher interface
type EventPublisher interface {
    Publish(ctx context.Context, topic string, event interface{}) error
}

// Cache interface
type Cache interface {
    Get(key string) (interface{}, error)
    Set(key string, value interface{}, ttl time.Duration) error
    Delete(key string) error
}

// Logger interface
type Logger interface {
    Info(msg string, args ...interface{})
    Warn(msg string, args ...interface{})
    Error(msg string, args ...interface{})
    Debug(msg string, args ...interface{})
}

// Events
type UserCreatedEvent struct {
    UserID    string    `json:"user_id"`
    Name      string    `json:"name"`
    Email     string    `json:"email"`
    Timestamp time.Time `json:"timestamp"`
}

type UserUpdatedEvent struct {
    UserID    string    `json:"user_id"`
    Name      string    `json:"name"`
    Email     string    `json:"email"`
    Timestamp time.Time `json:"timestamp"`
}

type UserDeletedEvent struct {
    UserID    string    `json:"user_id"`
    Timestamp time.Time `json:"timestamp"`
}

// HTTP Handler
type UserHandler struct {
    service UserService
    logger  Logger
}

func NewUserHandler(service UserService, logger Logger) *UserHandler {
    return &UserHandler{
        service: service,
        logger:  logger,
    }
}

func (h *UserHandler) CreateUser(w http.ResponseWriter, r *http.Request) {
    var user User
    if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
        h.writeError(w, http.StatusBadRequest, "Invalid request body")
        return
    }
    
    ctx := r.Context()
    created, err := h.service.CreateUser(ctx, &user)
    if err != nil {
        h.writeError(w, http.StatusInternalServerError, err.Error())
        return
    }
    
    h.writeJSON(w, http.StatusCreated, created)
}

func (h *UserHandler) GetUser(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    id := vars["id"]
    
    ctx := r.Context()
    user, err := h.service.GetUser(ctx, id)
    if err != nil {
        h.writeError(w, http.StatusNotFound, err.Error())
        return
    }
    
    h.writeJSON(w, http.StatusOK, user)
}

func (h *UserHandler) UpdateUser(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    id := vars["id"]
    
    var user User
    if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
        h.writeError(w, http.StatusBadRequest, "Invalid request body")
        return
    }
    
    user.ID = id
    
    ctx := r.Context()
    updated, err := h.service.UpdateUser(ctx, &user)
    if err != nil {
        h.writeError(w, http.StatusInternalServerError, err.Error())
        return
    }
    
    h.writeJSON(w, http.StatusOK, updated)
}

func (h *UserHandler) DeleteUser(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    id := vars["id"]
    
    ctx := r.Context()
    if err := h.service.DeleteUser(ctx, id); err != nil {
        h.writeError(w, http.StatusInternalServerError, err.Error())
        return
    }
    
    w.WriteHeader(http.StatusNoContent)
}

func (h *UserHandler) ListUsers(w http.ResponseWriter, r *http.Request) {
    filter := &UserFilter{
        Name:   r.URL.Query().Get("name"),
        Email:  r.URL.Query().Get("email"),
        Limit:  getIntParam(r, "limit", 10),
        Offset: getIntParam(r, "offset", 0),
    }
    
    ctx := r.Context()
    users, err := h.service.ListUsers(ctx, filter)
    if err != nil {
        h.writeError(w, http.StatusInternalServerError, err.Error())
        return
    }
    
    h.writeJSON(w, http.StatusOK, users)
}

func (h *UserHandler) writeJSON(w http.ResponseWriter, status int, data interface{}) {
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(status)
    json.NewEncoder(w).Encode(data)
}

func (h *UserHandler) writeError(w http.ResponseWriter, status int, message string) {
    h.writeJSON(w, status, map[string]string{"error": message})
}

func getIntParam(r *http.Request, key string, defaultValue int) int {
    value := r.URL.Query().Get(key)
    if value == "" {
        return defaultValue
    }
    
    if intValue, err := strconv.Atoi(value); err == nil {
        return intValue
    }
    
    return defaultValue
}

// Service setup
func setupUserService() *UserHandler {
    // Initialize dependencies
    logger := NewConsoleLogger()
    cache := NewMemoryCache()
    repo := NewInMemoryUserRepository()
    events := NewInMemoryEventPublisher()
    
    // Create service
    service := NewUserService(repo, events, cache, logger)
    
    // Create handler
    handler := NewUserHandler(service, logger)
    
    return handler
}

// HTTP Server setup
func setupHTTPServer() *http.Server {
    handler := setupUserService()
    
    router := mux.NewRouter()
    
    // User routes
    router.HandleFunc("/users", handler.CreateUser).Methods("POST")
    router.HandleFunc("/users", handler.ListUsers).Methods("GET")
    router.HandleFunc("/users/{id}", handler.GetUser).Methods("GET")
    router.HandleFunc("/users/{id}", handler.UpdateUser).Methods("PUT")
    router.HandleFunc("/users/{id}", handler.DeleteUser).Methods("DELETE")
    
    // Health check
    router.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
        w.WriteHeader(http.StatusOK)
        w.Write([]byte("OK"))
    }).Methods("GET")
    
    // Metrics
    router.HandleFunc("/metrics", func(w http.ResponseWriter, r *http.Request) {
        w.WriteHeader(http.StatusOK)
        w.Write([]byte("# HELP go_goroutines Number of goroutines\ngo_goroutines " + fmt.Sprintf("%d", runtime.NumGoroutine())))
    }).Methods("GET")
    
    server := &http.Server{
        Addr:         ":8080",
        Handler:      router,
        ReadTimeout:  15 * time.Second,
        WriteTimeout: 15 * time.Second,
        IdleTimeout:  60 * time.Second,
    }
    
    return server
}
```

### Service Discovery and Registration
```go
package main

import (
    "context"
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "time"
    
    "github.com/go-chi/chi/v5"
    "github.com/hashicorp/consul/api"
)

// Service registry interface
type ServiceRegistry interface {
    Register(ctx context.Context, service *ServiceInfo) error
    Deregister(ctx context.Context, serviceID string) error
    Discover(ctx context.Context, serviceName string) ([]*ServiceInfo, error)
    HealthCheck(ctx context.Context, serviceID string) error
}

// Service information
type ServiceInfo struct {
    ID       string            `json:"id"`
    Name     string            `json:"name"`
    Address  string            `json:"address"`
    Port     int               `json:"port"`
    Tags     []string          `json:"tags"`
    Meta     map[string]string `json:"meta"`
    Health   string            `json:"health"`
    Priority int               `json:"priority"`
}

// Consul implementation
type ConsulRegistry struct {
    client *api.Client
    config *ConsulConfig
}

type ConsulConfig struct {
    Address    string
    Datacenter string
    Token      string
}

func NewConsulRegistry(config *ConsulConfig) (*ConsulRegistry, error) {
    consulConfig := api.DefaultConfig()
    if config.Address != "" {
        consulConfig.Address = config.Address
    }
    if config.Datacenter != "" {
        consulConfig.Datacenter = config.Datacenter
    }
    if config.Token != "" {
        consulConfig.Token = config.Token
    }
    
    client, err := api.NewClient(consulConfig)
    if err != nil {
        return nil, fmt.Errorf("failed to create consul client: %w", err)
    }
    
    return &ConsulRegistry{
        client: client,
        config: config,
    }, nil
}

func (r *ConsulRegistry) Register(ctx context.Context, service *ServiceInfo) error {
    registration := &api.AgentServiceRegistration{
        ID:      service.ID,
        Name:    service.Name,
        Address: service.Address,
        Port:    service.Port,
        Tags:    service.Tags,
        Meta:    service.Meta,
        Check: &api.AgentServiceCheck{
            HTTP:     fmt.Sprintf("http://%s:%d/health", service.Address, service.Port),
            Interval: "10s",
            Timeout:  "3s",
        },
    }
    
    if err := r.client.Agent().ServiceRegister(registration); err != nil {
        return fmt.Errorf("failed to register service: %w", err)
    }
    
    log.Printf("Service %s registered successfully", service.ID)
    return nil
}

func (r *ConsulRegistry) Deregister(ctx context.Context, serviceID string) error {
    if err := r.client.Agent().ServiceDeregister(serviceID); err != nil {
        return fmt.Errorf("failed to deregister service: %w", err)
    }
    
    log.Printf("Service %s deregistered successfully", serviceID)
    return nil
}

func (r *ConsulRegistry) Discover(ctx context.Context, serviceName string) ([]*ServiceInfo, error) {
    services, _, err := r.client.Health().Service(serviceName, "", true)
    if err != nil {
        return nil, fmt.Errorf("failed to discover services: %w", err)
    }
    
    var result []*ServiceInfo
    for _, service := range services {
        info := &ServiceInfo{
            ID:      service.Service.ID,
            Name:    service.Service.Service,
            Address: service.Service.Address,
            Port:    service.Service.Port,
            Tags:    service.Service.Tags,
            Meta:    service.Service.Meta,
            Health:  "healthy",
        }
        
        if service.Checks.AggregatedStatus() != "passing" {
            info.Health = "unhealthy"
        }
        
        result = append(result, info)
    }
    
    return result, nil
}

func (r *ConsulRegistry) HealthCheck(ctx context.Context, serviceID string) error {
    checks, _, err := r.client.Health().Checks(serviceID, nil)
    if err != nil {
        return fmt.Errorf("failed to get health checks: %w", err)
    }
    
    for _, check := range checks {
        if check.Status != "passing" {
            return fmt.Errorf("service %s is unhealthy: %s", serviceID, check.Output)
        }
    }
    
    return nil
}

// Service discovery client
type DiscoveryClient struct {
    registry ServiceRegistry
    cache    map[string][]*ServiceInfo
    ttl      time.Duration
    mu       sync.RWMutex
}

func NewDiscoveryClient(registry ServiceRegistry, ttl time.Duration) *DiscoveryClient {
    return &DiscoveryClient{
        registry: registry,
        cache:    make(map[string][]*ServiceInfo),
        ttl:      ttl,
    }
}

func (dc *DiscoveryClient) GetServices(ctx context.Context, serviceName string) ([]*ServiceInfo, error) {
    dc.mu.RLock()
    if cached, exists := dc.cache[serviceName]; exists {
        dc.mu.RUnlock()
        return cached, nil
    }
    dc.mu.RUnlock()
    
    services, err := dc.registry.Discover(ctx, serviceName)
    if err != nil {
        return nil, err
    }
    
    dc.mu.Lock()
    dc.cache[serviceName] = services
    dc.mu.Unlock()
    
    // Refresh cache in background
    go func() {
        time.Sleep(dc.ttl)
        dc.RefreshCache(ctx, serviceName)
    }()
    
    return services, nil
}

func (dc *DiscoveryClient) RefreshCache(ctx context.Context, serviceName string) {
    services, err := dc.registry.Discover(ctx, serviceName)
    if err != nil {
        log.Printf("Failed to refresh cache for %s: %v", serviceName, err)
        return
    }
    
    dc.mu.Lock()
    dc.cache[serviceName] = services
    dc.mu.Unlock()
}

func (dc *DiscoveryClient) GetHealthyService(ctx context.Context, serviceName string) (*ServiceInfo, error) {
    services, err := dc.GetServices(ctx, serviceName)
    if err != nil {
        return nil, err
    }
    
    for _, service := range services {
        if service.Health == "healthy" {
            return service, nil
        }
    }
    
    return nil, fmt.Errorf("no healthy services found for %s", serviceName)
}

// Load balancer
type LoadBalancer interface {
    NextService(services []*ServiceInfo) *ServiceInfo
}

type RoundRobinBalancer struct {
    current int
}

func NewRoundRobinBalancer() *RoundRobinBalancer {
    return &RoundRobinBalancer{current: 0}
}

func (rr *RoundRobinBalancer) NextService(services []*ServiceInfo) *ServiceInfo {
    if len(services) == 0 {
        return nil
    }
    
    service := services[rr.current%len(services)]
    rr.current++
    return service
}

type RandomBalancer struct{}

func NewRandomBalancer() *RandomBalancer {
    return &RandomBalancer{}
}

func (rb *RandomBalancer) NextService(services []*ServiceInfo) *ServiceInfo {
    if len(services) == 0 {
        return nil
    }
    
    return services[rand.Intn(len(services))]
}

// Service client with discovery
type ServiceClient struct {
    discovery *DiscoveryClient
    balancer  LoadBalancer
    client    *http.Client
    logger    Logger
}

func NewServiceClient(discovery *DiscoveryClient, balancer LoadBalancer, logger Logger) *ServiceClient {
    return &ServiceClient{
        discovery: discovery,
        balancer:  balancer,
        client: &http.Client{
            Timeout: 30 * time.Second,
        },
        logger: logger,
    }
}

func (sc *ServiceClient) Call(ctx context.Context, serviceName, method, path string, body interface{}) (*http.Response, error) {
    // Discover services
    services, err := sc.discovery.GetServices(ctx, serviceName)
    if err != nil {
        return nil, fmt.Errorf("failed to discover services: %w", err)
    }
    
    // Filter healthy services
    var healthyServices []*ServiceInfo
    for _, service := range services {
        if service.Health == "healthy" {
            healthyServices = append(healthyServices, service)
        }
    }
    
    if len(healthyServices) == 0 {
        return nil, fmt.Errorf("no healthy services available for %s", serviceName)
    }
    
    // Load balance
    service := sc.balancer.NextService(healthyServices)
    if service == nil {
        return nil, fmt.Errorf("failed to select service")
    }
    
    // Make request
    url := fmt.Sprintf("http://%s:%d%s", service.Address, service.Port, path)
    
    var req *http.Request
    if body != nil {
        jsonBody, err := json.Marshal(body)
        if err != nil {
            return nil, fmt.Errorf("failed to marshal request body: %w", err)
        }
        req, err = http.NewRequestWithContext(ctx, method, url, bytes.NewBuffer(jsonBody))
        if err != nil {
            return nil, fmt.Errorf("failed to create request: %w", err)
        }
        req.Header.Set("Content-Type", "application/json")
    } else {
        req, err = http.NewRequestWithContext(ctx, method, url, nil)
        if err != nil {
            return nil, fmt.Errorf("failed to create request: %w", err)
        }
    }
    
    resp, err := sc.client.Do(req)
    if err != nil {
        return nil, fmt.Errorf("failed to make request: %w", err)
    }
    
    sc.logger.Info("Service call completed",
        "service", serviceName,
        "method", method,
        "path", path,
        "target", fmt.Sprintf("%s:%d", service.Address, service.Port),
        "status", resp.StatusCode,
    )
    
    return resp, nil
}

// Service registration and discovery server
func setupServiceRegistryServer() *http.Server {
    // Initialize Consul registry
    consulConfig := &ConsulConfig{
        Address: "localhost:8500",
    }
    
    registry, err := NewConsulRegistry(consulConfig)
    if err != nil {
        log.Fatal("Failed to create service registry:", err)
    }
    
    // Create discovery client
    discovery := NewDiscoveryClient(registry, 30*time.Second)
    balancer := NewRoundRobinBalancer()
    logger := NewConsoleLogger()
    
    // Create service client
    client := NewServiceClient(discovery, balancer, logger)
    
    router := chi.NewRouter()
    
    // Registration endpoints
    router.Post("/register", func(w http.ResponseWriter, r *http.Request) {
        var service ServiceInfo
        if err := json.NewDecoder(r.Body).Decode(&service); err != nil {
            http.Error(w, "Invalid request body", http.StatusBadRequest)
            return
        }
        
        ctx := r.Context()
        if err := registry.Register(ctx, &service); err != nil {
            http.Error(w, err.Error(), http.StatusInternalServerError)
            return
        }
        
        w.WriteHeader(http.StatusCreated)
        json.NewEncoder(w).Encode(map[string]string{"status": "registered"})
    })
    
    router.Delete("/deregister/{id}", func(w http.ResponseWriter, r *http.Request) {
        serviceID := chi.URLParam(r, "id")
        
        ctx := r.Context()
        if err := registry.Deregister(ctx, serviceID); err != nil {
            http.Error(w, err.Error(), http.StatusInternalServerError)
            return
        }
        
        w.WriteHeader(http.StatusOK)
        json.NewEncoder(w).Encode(map[string]string{"status": "deregistered"})
    })
    
    // Discovery endpoints
    router.Get("/discover/{serviceName}", func(w http.ResponseWriter, r *http.Request) {
        serviceName := chi.URLParam(r, "serviceName")
        
        ctx := r.Context()
        services, err := registry.Discover(ctx, serviceName)
        if err != nil {
            http.Error(w, err.Error(), http.StatusInternalServerError)
            return
        }
        
        w.Header().Set("Content-Type", "application/json")
        json.NewEncoder(w).Encode(services)
    })
    
    // Proxy endpoint
    router.Route("/proxy/{serviceName}", func(r chi.Router) {
        r.Get("/*", func(w http.ResponseWriter, r *http.Request) {
            serviceName := chi.URLParam(r, "serviceName")
            path := chi.URLParam(r, "*")
            
            ctx := r.Context()
            resp, err := client.Call(ctx, serviceName, "GET", "/"+path, nil)
            if err != nil {
                http.Error(w, err.Error(), http.StatusBadGateway)
                return
            }
            defer resp.Body.Close()
            
            // Copy response
            for key, values := range resp.Header {
                for _, value := range values {
                    w.Header().Add(key, value)
                }
            }
            w.WriteHeader(resp.StatusCode)
            io.Copy(w, resp.Body)
        })
        
        r.Post("/*", func(w http.ResponseWriter, r *http.Request) {
            serviceName := chi.URLParam(r, "serviceName")
            path := chi.URLParam(r, "*")
            
            var body interface{}
            json.NewDecoder(r.Body).Decode(&body)
            
            ctx := r.Context()
            resp, err := client.Call(ctx, serviceName, "POST", "/"+path, body)
            if err != nil {
                http.Error(w, err.Error(), http.StatusBadGateway)
                return
            }
            defer resp.Body.Close()
            
            // Copy response
            for key, values := range resp.Header {
                for _, value := range values {
                    w.Header().Add(key, value)
                }
            }
            w.WriteHeader(resp.StatusCode)
            io.Copy(w, resp.Body)
        })
    })
    
    // Health check
    router.Get("/health", func(w http.ResponseWriter, r *http.Request) {
        w.WriteHeader(http.StatusOK)
        w.Write([]byte("OK"))
    })
    
    server := &http.Server{
        Addr:         ":8081",
        Handler:      router,
        ReadTimeout:  15 * time.Second,
        WriteTimeout: 15 * time.Second,
        IdleTimeout:  60 * time.Second,
    }
    
    return server
}

// Service auto-registration
type ServiceRegistrar struct {
    registry ServiceRegistry
    service  *ServiceInfo
    ticker   *time.Ticker
    done     chan bool
}

func NewServiceRegistrar(registry ServiceRegistry, service *ServiceInfo) *ServiceRegistrar {
    return &ServiceRegistrar{
        registry: registry,
        service:  service,
        done:     make(chan bool),
    }
}

func (sr *ServiceRegistrar) Start(ctx context.Context) error {
    // Initial registration
    if err := sr.registry.Register(ctx, sr.service); err != nil {
        return fmt.Errorf("failed to register service: %w", err)
    }
    
    // Periodic health check
    sr.ticker = time.NewTicker(30 * time.Second)
    
    go func() {
        for {
            select {
            case <-sr.ticker.C:
                if err := sr.registry.HealthCheck(ctx, sr.service.ID); err != nil {
                    log.Printf("Health check failed for service %s: %v", sr.service.ID, err)
                    // Try to re-register
                    if err := sr.registry.Register(ctx, sr.service); err != nil {
                        log.Printf("Failed to re-register service %s: %v", sr.service.ID, err)
                    }
                }
            case <-sr.done:
                return
            case <-ctx.Done():
                return
            }
        }
    }()
    
    return nil
}

func (sr *ServiceRegistrar) Stop(ctx context.Context) error {
    close(sr.done)
    if sr.ticker != nil {
        sr.ticker.Stop()
    }
    
    return sr.registry.Deregister(ctx, sr.service.ID)
}
```

## Inter-Service Communication

### gRPC and REST Communication
```go
package main

import (
    "context"
    "fmt"
    "log"
    "net"
    "net/http"
    "time"
    
    "github.com/golang/protobuf/ptypes/empty"
    "google.golang.org/grpc"
    "google.golang.org/grpc/credentials/insecure"
    "google.golang.org/grpc/reflection"
)

// gRPC service definitions
type UserServiceServer struct {
    pb.UnimplementedUserServiceServer
    service UserService
    logger  Logger
}

func NewUserServiceServer(service UserService, logger Logger) *UserServiceServer {
    return &UserServiceServer{
        service: service,
        logger:  logger,
    }
}

func (s *UserServiceServer) CreateUser(ctx context.Context, req *pb.CreateUserRequest) (*pb.UserResponse, error) {
    user := &User{
        Name:  req.GetName(),
        Email: req.GetEmail(),
        Age:   int(req.GetAge()),
    }
    
    created, err := s.service.CreateUser(ctx, user)
    if err != nil {
        s.logger.Error("Failed to create user", "error", err)
        return nil, fmt.Errorf("failed to create user: %w", err)
    }
    
    return &pb.UserResponse{
        User: &pb.User{
            Id:        created.ID,
            Name:      created.Name,
            Email:     created.Email,
            Age:       int32(created.Age),
            CreatedAt: created.CreatedAt.Unix(),
            UpdatedAt: created.UpdatedAt.Unix(),
        },
    }, nil
}

func (s *UserServiceServer) GetUser(ctx context.Context, req *pb.GetUserRequest) (*pb.UserResponse, error) {
    user, err := s.service.GetUser(ctx, req.GetId())
    if err != nil {
        s.logger.Error("Failed to get user", "error", err)
        return nil, fmt.Errorf("failed to get user: %w", err)
    }
    
    return &pb.UserResponse{
        User: &pb.User{
            Id:        user.ID,
            Name:      user.Name,
            Email:     user.Email,
            Age:       int32(user.Age),
            CreatedAt: user.CreatedAt.Unix(),
            UpdatedAt: user.UpdatedAt.Unix(),
        },
    }, nil
}

func (s *UserServiceServer) UpdateUser(ctx context.Context, req *pb.UpdateUserRequest) (*pb.UserResponse, error) {
    user := &User{
        ID:    req.GetId(),
        Name:  req.GetName(),
        Email: req.GetEmail(),
        Age:   int(req.GetAge()),
    }
    
    updated, err := s.service.UpdateUser(ctx, user)
    if err != nil {
        s.logger.Error("Failed to update user", "error", err)
        return nil, fmt.Errorf("failed to update user: %w", err)
    }
    
    return &pb.UserResponse{
        User: &pb.User{
            Id:        updated.ID,
            Name:      updated.Name,
            Email:     updated.Email,
            Age:       int32(updated.Age),
            CreatedAt: updated.CreatedAt.Unix(),
            UpdatedAt: updated.UpdatedAt.Unix(),
        },
    }, nil
}

func (s *UserServiceServer) DeleteUser(ctx context.Context, req *pb.DeleteUserRequest) (*pb.DeleteUserResponse, error) {
    if err := s.service.DeleteUser(ctx, req.GetId()); err != nil {
        s.logger.Error("Failed to delete user", "error", err)
        return nil, fmt.Errorf("failed to delete user: %w", err)
    }
    
    return &pb.DeleteUserResponse{
        Success: true,
    }, nil
}

func (s *UserServiceServer) ListUsers(ctx context.Context, req *pb.ListUsersRequest) (*pb.ListUsersResponse, error) {
    filter := &UserFilter{
        Name:   req.GetFilter().GetName(),
        Email:  req.GetFilter().GetEmail(),
        Limit:  int(req.GetFilter().GetLimit()),
        Offset: int(req.GetFilter().GetOffset()),
    }
    
    users, err := s.service.ListUsers(ctx, filter)
    if err != nil {
        s.logger.Error("Failed to list users", "error", err)
        return nil, fmt.Errorf("failed to list users: %w", err)
    }
    
    var pbUsers []*pb.User
    for _, user := range users {
        pbUsers = append(pbUsers, &pb.User{
            Id:        user.ID,
            Name:      user.Name,
            Email:     user.Email,
            Age:       int32(user.Age),
            CreatedAt: user.CreatedAt.Unix(),
            UpdatedAt: user.UpdatedAt.Unix(),
        })
    }
    
    return &pb.ListUsersResponse{
        Users: pbUsers,
    }, nil
}

// gRPC client
type GRPCClient struct {
    conn   *grpc.ClientConn
    client pb.UserServiceClient
    logger Logger
}

func NewGRPCClient(address string, logger Logger) (*GRPCClient, error) {
    conn, err := grpc.Dial(address, grpc.WithTransportCredentials(insecure.NewCredentials()))
    if err != nil {
        return nil, fmt.Errorf("failed to connect to gRPC server: %w", err)
    }
    
    client := pb.NewUserServiceClient(conn)
    
    return &GRPCClient{
        conn:   conn,
        client: client,
        logger: logger,
    }, nil
}

func (c *GRPCClient) Close() error {
    return c.conn.Close()
}

func (c *GRPCClient) CreateUser(ctx context.Context, name, email string, age int) (*User, error) {
    req := &pb.CreateUserRequest{
        Name:  name,
        Email: email,
        Age:   int32(age),
    }
    
    resp, err := c.client.CreateUser(ctx, req)
    if err != nil {
        return nil, fmt.Errorf("failed to create user: %w", err)
    }
    
    user := resp.GetUser()
    return &User{
        ID:        user.GetId(),
        Name:      user.GetName(),
        Email:     user.GetEmail(),
        Age:       int(user.GetAge()),
        CreatedAt: time.Unix(user.GetCreatedAt(), 0),
        UpdatedAt: time.Unix(user.GetUpdatedAt(), 0),
    }, nil
}

func (c *GRPCClient) GetUser(ctx context.Context, id string) (*User, error) {
    req := &pb.GetUserRequest{
        Id: id,
    }
    
    resp, err := c.client.GetUser(ctx, req)
    if err != nil {
        return nil, fmt.Errorf("failed to get user: %w", err)
    }
    
    user := resp.GetUser()
    return &User{
        ID:        user.GetId(),
        Name:      user.GetName(),
        Email:     user.GetEmail(),
        Age:       int(user.GetAge()),
        CreatedAt: time.Unix(user.GetCreatedAt(), 0),
        UpdatedAt: time.Unix(user.GetUpdatedAt(), 0),
    }, nil
}

// REST Gateway
type RESTGateway struct {
    grpcClient *GRPCClient
    logger     Logger
}

func NewRESTGateway(grpcClient *GRPCClient, logger Logger) *RESTGateway {
    return &RESTGateway{
        grpcClient: grpcClient,
        logger:     logger,
    }
}

func (g *RESTGateway) CreateUser(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    ctx := r.Context()
    user, err := g.grpcClient.CreateUser(ctx, req.Name, req.Email, req.Age)
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (g *RESTGateway) GetUser(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    id := vars["id"]
    
    ctx := r.Context()
    user, err := g.grpcClient.GetUser(ctx, id)
    if err != nil {
        http.Error(w, err.Error(), http.StatusNotFound)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

// Message broker interface
type MessageBroker interface {
    Publish(ctx context.Context, topic string, message *Message) error
    Subscribe(ctx context.Context, topic string, handler MessageHandler) error
    Close() error
}

type Message struct {
    ID        string                 `json:"id"`
    Topic     string                 `json:"topic"`
    Payload   []byte                 `json:"payload"`
    Headers   map[string]string      `json:"headers"`
    Timestamp time.Time              `json:"timestamp"`
    Retry     int                    `json:"retry"`
}

type MessageHandler func(ctx context.Context, message *Message) error

// In-memory message broker (for testing)
type InMemoryBroker struct {
    topics   map[string][]chan *Message
    handlers map[string]MessageHandler
    mu       sync.RWMutex
}

func NewInMemoryBroker() *InMemoryBroker {
    return &InMemoryBroker{
        topics:   make(map[string][]chan *Message),
        handlers: make(map[string]MessageHandler),
    }
}

func (b *InMemoryBroker) Publish(ctx context.Context, topic string, message *Message) error {
    b.mu.RLock()
    subscribers, exists := b.topics[topic]
    b.mu.RUnlock()
    
    if !exists {
        return fmt.Errorf("no subscribers for topic %s", topic)
    }
    
    message.ID = generateID()
    message.Timestamp = time.Now()
    
    for _, ch := range subscribers {
        select {
        case ch <- message:
        case <-ctx.Done():
            return ctx.Err()
        default:
            // Channel is full, skip
        }
    }
    
    return nil
}

func (b *InMemoryBroker) Subscribe(ctx context.Context, topic string, handler MessageHandler) error {
    b.mu.Lock()
    defer b.mu.Unlock()
    
    ch := make(chan *Message, 100)
    b.topics[topic] = append(b.topics[topic], ch)
    b.handlers[topic] = handler
    
    go func() {
        for {
            select {
            case message := <-ch:
                if err := handler(ctx, message); err != nil {
                    log.Printf("Message handler error: %v", err)
                }
            case <-ctx.Done():
                return
            }
        }
    }()
    
    return nil
}

func (b *InMemoryBroker) Close() error {
    b.mu.Lock()
    defer b.mu.Unlock()
    
    for topic, subscribers := range b.topics {
        for _, ch := range subscribers {
            close(ch)
        }
        delete(b.topics, topic)
    }
    
    return nil
}

// Event-driven service
type EventDrivenService struct {
    broker  MessageBroker
    service UserService
    logger  Logger
}

func NewEventDrivenService(broker MessageBroker, service UserService, logger Logger) *EventDrivenService {
    return &EventDrivenService{
        broker:  broker,
        service: service,
        logger:  logger,
    }
}

func (s *EventDrivenService) Start(ctx context.Context) error {
    // Subscribe to events
    if err := s.broker.Subscribe(ctx, "user.created", s.handleUserCreated); err != nil {
        return fmt.Errorf("failed to subscribe to user.created events: %w", err)
    }
    
    if err := s.broker.Subscribe(ctx, "user.updated", s.handleUserUpdated); err != nil {
        return fmt.Errorf("failed to subscribe to user.updated events: %w", err)
    }
    
    if err := s.broker.Subscribe(ctx, "user.deleted", s.handleUserDeleted); err != nil {
        return fmt.Errorf("failed to subscribe to user.deleted events: %w", err)
    }
    
    s.logger.Info("Event-driven service started")
    return nil
}

func (s *EventDrivenService) handleUserCreated(ctx context.Context, message *Message) error {
    var event UserCreatedEvent
    if err := json.Unmarshal(message.Payload, &event); err != nil {
        return fmt.Errorf("failed to unmarshal user created event: %w", err)
    }
    
    s.logger.Info("User created event received", "user_id", event.UserID, "name", event.Name)
    
    // Handle event (e.g., send welcome email, update analytics, etc.)
    return nil
}

func (s *EventDrivenService) handleUserUpdated(ctx context.Context, message *Message) error {
    var event UserUpdatedEvent
    if err := json.Unmarshal(message.Payload, &event); err != nil {
        return fmt.Errorf("failed to unmarshal user updated event: %w", err)
    }
    
    s.logger.Info("User updated event received", "user_id", event.UserID, "name", event.Name)
    
    // Handle event
    return nil
}

func (s *EventDrivenService) handleUserDeleted(ctx context.Context, message *Message) error {
    var event UserDeletedEvent
    if err := json.Unmarshal(message.Payload, &event); err != nil {
        return fmt.Errorf("failed to unmarshal user deleted event: %w", err)
    }
    
    s.logger.Info("User deleted event received", "user_id", event.UserID)
    
    // Handle event (e.g., cleanup related data, update analytics, etc.)
    return nil
}

// Circuit breaker
type CircuitBreaker struct {
    maxFailures int
    timeout     time.Duration
    failures    int
    lastFail    time.Time
    state       CircuitState
    mu          sync.RWMutex
}

type CircuitState int

const (
    CircuitClosed CircuitState = iota
    CircuitOpen
    CircuitHalfOpen
)

func NewCircuitBreaker(maxFailures int, timeout time.Duration) *CircuitBreaker {
    return &CircuitBreaker{
        maxFailures: maxFailures,
        timeout:     timeout,
        state:       CircuitClosed,
    }
}

func (cb *CircuitBreaker) Call(fn func() error) error {
    cb.mu.Lock()
    defer cb.mu.Unlock()
    
    if cb.state == CircuitOpen {
        if time.Since(cb.lastFail) > cb.timeout {
            cb.state = CircuitHalfOpen
        } else {
            return fmt.Errorf("circuit breaker is open")
        }
    }
    
    err := fn()
    
    if err != nil {
        cb.failures++
        cb.lastFail = time.Now()
        
        if cb.failures >= cb.maxFailures {
            cb.state = CircuitOpen
        }
        
        return err
    }
    
    cb.failures = 0
    cb.state = CircuitClosed
    
    return nil
}

// Retry mechanism
type RetryMechanism struct {
    maxAttempts int
    delay       time.Duration
    backoff     BackoffStrategy
}

type BackoffStrategy int

const (
    BackoffFixed BackoffStrategy = iota
    BackoffExponential
    BackoffLinear
)

func NewRetryMechanism(maxAttempts int, delay time.Duration, backoff BackoffStrategy) *RetryMechanism {
    return &RetryMechanism{
        maxAttempts: maxAttempts,
        delay:       delay,
        backoff:     backoff,
    }
}

func (r *RetryMechanism) Execute(ctx context.Context, fn func() error) error {
    var lastErr error
    
    for attempt := 1; attempt <= r.maxAttempts; attempt++ {
        if err := fn(); err != nil {
            lastErr = err
            
            if attempt == r.maxAttempts {
                break
            }
            
            delay := r.calculateDelay(attempt)
            select {
            case <-time.After(delay):
            case <-ctx.Done():
                return ctx.Err()
            }
            
            continue
        }
        
        return nil
    }
    
    return fmt.Errorf("failed after %d attempts: %w", r.maxAttempts, lastErr)
}

func (r *RetryMechanism) calculateDelay(attempt int) time.Duration {
    switch r.backoff {
    case BackoffFixed:
        return r.delay
    case BackoffExponential:
        return r.delay * time.Duration(1<<uint(attempt-1))
    case BackoffLinear:
        return r.delay * time.Duration(attempt)
    default:
        return r.delay
    }
}
```

## Container Orchestration

### Docker and Kubernetes
```go
package main

import (
    "context"
    "fmt"
    "log"
    "os"
    "os/signal"
    "syscall"
    "time"
)

// Docker configuration
type DockerConfig struct {
    Image       string            `json:"image"`
    Tag         string            `json:"tag"`
    Port        int               `json:"port"`
    Environment map[string]string `json:"environment"`
    Volumes     []Volume          `json:"volumes"`
    HealthCheck *HealthCheck      `json:"health_check"`
}

type Volume struct {
    HostPath  string `json:"host_path"`
    Container string `json:"container"`
    ReadOnly  bool   `json:"read_only"`
}

type HealthCheck struct {
    Test     []string `json:"test"`
    Interval string   `json:"interval"`
    Timeout  string   `json:"timeout"`
    Retries  int      `json:"retries"`
}

// Kubernetes configuration
type K8sConfig struct {
    Namespace     string            `json:"namespace"`
    ServiceName   string            `json:"service_name"`
    Replicas      int               `json:"replicas"`
    Image         string            `json:"image"`
    Port          int               `json:"port"`
    Resources     ResourceRequirements `json:"resources"`
    Environment   map[string]string `json:"environment"`
    Labels        map[string]string `json:"labels"`
    Annotations   map[string]string `json:"annotations"`
}

type ResourceRequirements struct {
    Requests ResourceList `json:"requests"`
    Limits   ResourceList `json:"limits"`
}

type ResourceList struct {
    CPU    string `json:"cpu"`
    Memory string `json:"memory"`
}

// Service configuration
type ServiceConfig struct {
    Name        string         `json:"name"`
    Version     string         `json:"version"`
    Port        int            `json:"port"`
    Docker      DockerConfig   `json:"docker"`
    Kubernetes  K8sConfig      `json:"kubernetes"`
    Health      HealthConfig   `json:"health"`
    Metrics     MetricsConfig  `json:"metrics"`
    Tracing     TracingConfig  `json:"tracing"`
}

type HealthConfig struct {
    Endpoint string        `json:"endpoint"`
    Interval time.Duration `json:"interval"`
    Timeout  time.Duration `json:"timeout"`
}

type MetricsConfig struct {
    Enabled bool   `json:"enabled"`
    Port    int    `json:"port"`
    Path    string `json:"path"`
}

type TracingConfig struct {
    Enabled  bool   `json:"enabled"`
    Endpoint string `json:"endpoint"`
    Service  string `json:"service"`
}

// Configuration loader
func LoadServiceConfig(configPath string) (*ServiceConfig, error) {
    data, err := os.ReadFile(configPath)
    if err != nil {
        return nil, fmt.Errorf("failed to read config file: %w", err)
    }
    
    var config ServiceConfig
    if err := json.Unmarshal(data, &config); err != nil {
        return nil, fmt.Errorf("failed to unmarshal config: %w", err)
    }
    
    return &config, nil
}

// Dockerfile generator
func GenerateDockerfile(config *ServiceConfig) string {
    dockerfile := fmt.Sprintf(`# Build stage
FROM golang:1.21-alpine AS builder

WORKDIR /app

# Copy go mod files
COPY go.mod go.sum ./
RUN go mod download

# Copy source code
COPY . .

# Build the application
RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o main ./cmd/%s

# Final stage
FROM alpine:latest

RUN apk --no-cache add ca-certificates

WORKDIR /root/

# Copy the binary from builder stage
COPY --from=builder /app/main .

# Expose port
EXPOSE %d

# Health check
HEALTHCHECK --interval=%s --timeout=%s --retries=3 --start-period=5s CMD [ "wget", "--no-verbose", "--tries=1", "--spider", "http://localhost:%d%s" || exit 1 ]

# Run the binary
CMD ["./main"]
`, config.Name, config.Port, 
        config.Docker.HealthCheck.Interval, 
        config.Docker.HealthCheck.Timeout,
        config.Port, config.Health.Endpoint)

    return dockerfile
}

// Docker Compose generator
func GenerateDockerCompose(config *ServiceConfig) string {
    compose := fmt.Sprintf(`version: '3.8'

services:
  %s:
    build: .
    image: %s:%s
    ports:
      - "%d:%d"
    environment:
`, config.Name, config.Docker.Image, config.Docker.Tag, config.Docker.Port, config.Port)

    for key, value := range config.Docker.Environment {
        compose += fmt.Sprintf("      %s: %s\n", key, value)
    }

    if len(config.Docker.Volumes) > 0 {
        compose += "    volumes:\n"
        for _, volume := range config.Docker.Volumes {
            readOnly := ""
            if volume.ReadOnly {
                readOnly = ":ro"
            }
            compose += fmt.Sprintf("      - %s:%s%s\n", volume.HostPath, volume.Container, readOnly)
        }
    }

    if config.Metrics.Enabled {
        compose += fmt.Sprintf(`  %s-metrics:
    image: prom/prometheus:latest
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml

`, config.Name)
    }

    compose += fmt.Sprintf(`networks:
  default:
    name: %s-network
`, config.Name)

    return compose
}

// Kubernetes manifests generator
func GenerateKubernetesManifests(config *ServiceConfig) map[string]string {
    manifests := make(map[string]string)

    // Deployment manifest
    deployment := fmt.Sprintf(`apiVersion: apps/v1
kind: Deployment
metadata:
  name: %s
  namespace: %s
  labels:
    app: %s
    version: %s
`, config.Name, config.Kubernetes.Namespace, config.Name, config.Version)

    if len(config.Kubernetes.Annotations) > 0 {
        deployment += "  annotations:\n"
        for key, value := range config.Kubernetes.Annotations {
            deployment += fmt.Sprintf("    %s: %s\n", key, value)
        }
    }

    deployment += fmt.Sprintf(`spec:
  replicas: %d
  selector:
    matchLabels:
      app: %s
  template:
    metadata:
      labels:
        app: %s
        version: %s
`, config.Kubernetes.Replicas, config.Name, config.Name, config.Version)

    if len(config.Kubernetes.Labels) > 0 {
        deployment += "        "
        for key, value := range config.Kubernetes.Labels {
            if key != "app" && key != "version" {
                deployment += fmt.Sprintf("%s: %s\n", key, value)
            }
        }
    }

    deployment += "    spec:\n"
    deployment += "      containers:\n"
    deployment += fmt.Sprintf(`      - name: %s
        image: %s
        imagePullPolicy: IfNotPresent
        ports:
        - containerPort: %d
`, config.Name, config.Kubernetes.Image, config.Port)

    if len(config.Kubernetes.Environment) > 0 {
        deployment += "        env:\n"
        for key, value := range config.Kubernetes.Environment {
            deployment += fmt.Sprintf("        - name: %s\n          value: %q\n", key, value)
        }
    }

    if config.Kubernetes.Resources.Requests.CPU != "" || config.Kubernetes.Resources.Requests.Memory != "" {
        deployment += "        resources:\n"
        deployment += "          requests:\n"
        if config.Kubernetes.Resources.Requests.CPU != "" {
            deployment += fmt.Sprintf("            cpu: %s\n", config.Kubernetes.Resources.Requests.CPU)
        }
        if config.Kubernetes.Resources.Requests.Memory != "" {
            deployment += fmt.Sprintf("            memory: %s\n", config.Kubernetes.Resources.Requests.Memory)
        }
    }

    if config.Kubernetes.Resources.Limits.CPU != "" || config.Kubernetes.Resources.Limits.Memory != "" {
        if config.Kubernetes.Resources.Requests.CPU == "" && config.Kubernetes.Resources.Requests.Memory == "" {
            deployment += "        resources:\n"
        }
        deployment += "          limits:\n"
        if config.Kubernetes.Resources.Limits.CPU != "" {
            deployment += fmt.Sprintf("            cpu: %s\n", config.Kubernetes.Resources.Limits.CPU)
        }
        if config.Kubernetes.Resources.Limits.Memory != "" {
            deployment += fmt.Sprintf("            memory: %s\n", config.Kubernetes.Resources.Limits.Memory)
        }
    }

    if config.Health.Endpoint != "" {
        deployment += fmt.Sprintf(`        livenessProbe:
          httpGet:
            path: %s
            port: %d
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: %s
            port: %d
          initialDelaySeconds: 5
          periodSeconds: 5
`, config.Health.Endpoint, config.Port, config.Health.Endpoint, config.Port)
    }

    deployment += "\n---\n"

    // Service manifest
    service := fmt.Sprintf(`apiVersion: v1
kind: Service
metadata:
  name: %s
  namespace: %s
  labels:
    app: %s
spec:
  selector:
    app: %s
  ports:
  - protocol: TCP
    port: 80
    targetPort: %d
  type: ClusterIP
`, config.Kubernetes.ServiceName, config.Kubernetes.Namespace, config.Name, config.Name, config.Port)

    deployment += service

    manifests["deployment.yaml"] = deployment

    // Service manifest (separate)
    manifests["service.yaml"] = service

    // ConfigMap manifest
    if len(config.Kubernetes.Environment) > 0 {
        configMap := fmt.Sprintf(`apiVersion: v1
kind: ConfigMap
metadata:
  name: %s-config
  namespace: %s
data:
`, config.Name, config.Kubernetes.Namespace)

        for key, value := range config.Kubernetes.Environment {
            configMap += fmt.Sprintf("  %s: %q\n", key, value)
        }

        manifests["configmap.yaml"] = configMap
    }

    // HorizontalPodAutoscaler manifest
    if config.Kubernetes.Resources.Requests.CPU != "" {
        hpa := fmt.Sprintf(`apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: %s-hpa
  namespace: %s
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: %s
  minReplicas: 1
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
`, config.Name, config.Kubernetes.Namespace, config.Name)

        manifests["hpa.yaml"] = hpa
    }

    return manifests
}

// Graceful shutdown
func GracefulShutdown(server *http.Server, timeout time.Duration) error {
    // Create a channel to receive OS signals
    sigChan := make(chan os.Signal, 1)
    signal.Notify(sigChan, syscall.SIGINT, syscall.SIGTERM)

    // Wait for signal
    sig := <-sigChan
    log.Printf("Received signal: %v", sig)

    // Create a context with timeout
    ctx, cancel := context.WithTimeout(context.Background(), timeout)
    defer cancel()

    // Shutdown the server
    if err := server.Shutdown(ctx); err != nil {
        return fmt.Errorf("server shutdown failed: %w", err)
    }

    log.Println("Server gracefully stopped")
    return nil
}

// Health check endpoint
func HealthCheckHandler(w http.ResponseWriter, r *http.Request) {
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusOK)
    w.Write([]byte(`{"status":"healthy","timestamp":"` + time.Now().Format(time.RFC3339) + `"}`))
}

// Readiness check endpoint
func ReadinessCheckHandler(w http.ResponseWriter, r *http.Request) {
    // Check dependencies here
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusOK)
    w.Write([]byte(`{"status":"ready","timestamp":"` + time.Now().Format(time.RFC3339) + `"}`))
}

// Liveness check endpoint
func LivenessCheckHandler(w http.ResponseWriter, r *http.Request) {
    // Check if the service is alive
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusOK)
    w.Write([]byte(`{"status":"alive","timestamp":"` + time.Now().Format(time.RFC3339) + `"}`))
}

// Metrics endpoint
func MetricsHandler(w http.ResponseWriter, r *http.Request) {
    // Return Prometheus metrics
    metrics := `# HELP go_goroutines Number of goroutines
# TYPE go_goroutines gauge
go_goroutines ` + fmt.Sprintf("%d", runtime.NumGoroutine()) + `

# HELP go_memstats_alloc_bytes Number of bytes allocated
# TYPE go_memstats_alloc_bytes gauge
go_memstats_alloc_bytes ` + fmt.Sprintf("%d", getMemoryUsage()) + `
`

    w.Header().Set("Content-Type", "text/plain")
    w.WriteHeader(http.StatusOK)
    w.Write([]byte(metrics))
}

func getMemoryUsage() uint64 {
    var m runtime.MemStats
    runtime.ReadMemStats(&m)
    return m.Alloc
}

// Service lifecycle management
type ServiceLifecycle struct {
    config     *ServiceConfig
    server     *http.Server
    logger     Logger
    shutdownCh chan os.Signal
}

func NewServiceLifecycle(config *ServiceConfig, logger Logger) *ServiceLifecycle {
    return &ServiceLifecycle{
        config:     config,
        logger:     logger,
        shutdownCh: make(chan os.Signal, 1),
    }
}

func (sl *ServiceLifecycle) Start() error {
    // Setup router
    router := mux.NewRouter()
    
    // Health endpoints
    router.HandleFunc("/health", HealthCheckHandler).Methods("GET")
    router.HandleFunc("/ready", ReadinessCheckHandler).Methods("GET")
    router.HandleFunc("/alive", LivenessCheckHandler).Methods("GET")
    
    // Metrics endpoint
    if sl.config.Metrics.Enabled {
        router.HandleFunc(sl.config.Metrics.Path, MetricsHandler).Methods("GET")
    }
    
    // Application routes would be added here
    
    // Create server
    sl.server = &http.Server{
        Addr:         fmt.Sprintf(":%d", sl.config.Port),
        Handler:      router,
        ReadTimeout:  30 * time.Second,
        WriteTimeout: 30 * time.Second,
        IdleTimeout:  120 * time.Second,
    }
    
    // Start server
    sl.logger.Info("Starting service", "port", sl.config.Port, "name", sl.config.Name)
    
    go func() {
        if err := sl.server.ListenAndServe(); err != nil && err != http.ErrServerClosed {
            sl.logger.Error("Server error", "error", err)
        }
    }()
    
    return nil
}

func (sl *ServiceLifecycle) Stop() error {
    sl.logger.Info("Shutting down service")
    
    // Create shutdown context
    ctx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
    defer cancel()
    
    // Shutdown server
    if err := sl.server.Shutdown(ctx); err != nil {
        sl.logger.Error("Server shutdown error", "error", err)
        return err
    }
    
    sl.logger.Info("Service stopped successfully")
    return nil
}

func (sl *ServiceLifecycle) WaitForShutdown() {
    signal.Notify(sl.shutdownCh, syscall.SIGINT, syscall.SIGTERM)
    <-sl.shutdownCh
}

// Main service function
func RunMicroservice(configPath string) error {
    // Load configuration
    config, err := LoadServiceConfig(configPath)
    if err != nil {
        return fmt.Errorf("failed to load configuration: %w", err)
    }
    
    // Create logger
    logger := NewConsoleLogger()
    
    // Create service lifecycle
    lifecycle := NewServiceLifecycle(config, logger)
    
    // Start service
    if err := lifecycle.Start(); err != nil {
        return fmt.Errorf("failed to start service: %w", err)
    }
    
    // Wait for shutdown signal
    lifecycle.WaitForShutdown()
    
    // Stop service
    return lifecycle.Stop()
}
```

## Summary

Go microservices provide:

**Service Architecture:**
- Service interfaces and implementations
- Dependency injection
- Event-driven architecture
- Circuit breakers and retries
- Graceful shutdown

**Service Discovery:**
- Service registration and deregistration
- Health checks
- Load balancing
- Service mesh integration
- Dynamic configuration

**Inter-Service Communication:**
- gRPC services
- REST APIs
- Message brokers
- Event streaming
- Protocol buffers

**Container Orchestration:**
- Docker configuration
- Kubernetes manifests
- Service deployment
- Auto-scaling
- Resource management

**Key Features:**
- High performance
- Strong typing
- Concurrency support
- Rich ecosystem
- Cloud-native

**Best Practices:**
- Service boundaries
- API design
- Error handling
- Monitoring and logging
- Security

**Common Patterns:**
- API gateway
- Service registry
- Circuit breaker
- Event sourcing
- CQRS

**Deployment:**
- Containerization
- Orchestration
- CI/CD pipelines
- Blue-green deployment
- Canary releases

Go provides excellent support for building microservices with its performance, concurrency model, and rich ecosystem of tools and libraries.
