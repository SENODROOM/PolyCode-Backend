<?php
/**
 * Framework Architecture and Design Patterns
 * 
 * This file demonstrates common framework architectural patterns,
 * design principles, and structural concepts used in PHP frameworks.
 */

// Front Controller Pattern
class FrontController
{
    private array $routes = [];
    private array $middleware = [];
    private array $services = [];
    
    public function __construct()
    {
        $this->initializeServices();
        $this->initializeRoutes();
        $this->initializeMiddleware();
    }
    
    /**
     * Initialize framework services
     */
    private function initializeServices(): void
    {
        $this->services['router'] = new Router();
        $this->services['dispatcher'] = new EventDispatcher();
        $this->services['request'] = new RequestHandler();
        $this->services['response'] = new ResponseHandler();
        $this->services['view'] = new ViewEngine();
    }
    
    /**
     * Initialize application routes
     */
    private function initializeRoutes(): void
    {
        $this->routes = [
            '/' => ['controller' => 'HomeController', 'action' => 'index'],
            '/users' => ['controller' => 'UserController', 'action' => 'index'],
            '/users/{id}' => ['controller' => 'UserController', 'action' => 'show'],
            '/api/users' => ['controller' => 'ApiController', 'action' => 'users'],
            '/admin' => ['controller' => 'AdminController', 'action' => 'dashboard']
        ];
    }
    
    /**
     * Initialize middleware stack
     */
    private function initializeMiddleware(): void
    {
        $this->middleware = [
            'auth' => new AuthenticationMiddleware(),
            'cors' => new CorsMiddleware(),
            'rate_limit' => new RateLimitMiddleware(),
            'logging' => new LoggingMiddleware()
        ];
    }
    
    /**
     * Handle incoming request
     */
    public function handle(HttpRequest $request): HttpResponse
    {
        // Dispatch request event
        $this->services['dispatcher']->dispatch('request.received', new RequestEvent($request));
        
        // Apply middleware
        $request = $this->applyMiddleware($request);
        
        // Route the request
        $route = $this->services['router']->match($request);
        
        if (!$route) {
            return $this->services['response']->notFound();
        }
        
        // Execute controller action
        $response = $this->executeController($route, $request);
        
        // Dispatch response event
        $this->services['dispatcher']->dispatch('response.created', new ResponseEvent($response));
        
        return $response;
    }
    
    /**
     * Apply middleware stack
     */
    private function applyMiddleware(HttpRequest $request): HttpRequest
    {
        foreach ($this->middleware as $middleware) {
            $request = $middleware->handle($request);
            
            if ($request->isRedirected()) {
                break;
            }
        }
        
        return $request;
    }
    
    /**
     * Execute controller action
     */
    private function executeController(array $route, HttpRequest $request): HttpResponse
    {
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        
        $controller = $this->createController($controllerName);
        
        if (!method_exists($controller, $actionName)) {
            return $this->services['response']->notFound();
        }
        
        return $controller->$actionName($request);
    }
    
    /**
     * Create controller instance
     */
    private function createController(string $className): object
    {
        $controllerClass = "App\\Controllers\\$className";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller not found: $controllerClass");
        }
        
        return new $controllerClass($this->services);
    }
}

// MVC Pattern Implementation
class Model
{
    protected array $attributes = [];
    protected bool $exists = false;
    
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }
    
    /**
     * Fill model with attributes
     */
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }
    
    /**
     * Set attribute
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get attribute
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
    
    /**
     * Save model to storage
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        
        return $this->insert();
    }
    
    /**
     * Insert new record
     */
    protected function insert(): bool
    {
        $this->attributes['id'] = $this->generateId();
        $this->exists = true;
        
        return true;
    }
    
    /**
     * Update existing record
     */
    protected function update(): bool
    {
        return true;
    }
    
    /**
     * Delete model from storage
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        $this->exists = false;
        return true;
    }
    
    /**
     * Generate unique ID
     */
    protected function generateId(): int
    {
        return rand(1, 1000);
    }
    
    /**
     * Magic getter
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }
    
    /**
     * Magic setter
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
}

class View
{
    private string $template;
    private array $data = [];
    private string $layout;
    
    public function __construct(string $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }
    
    /**
     * Set layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }
    
    /**
     * Set data
     */
    public function setData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
    
    /**
     * Render view
     */
    public function render(): string
    {
        $content = $this->renderTemplate($this->template, $this->data);
        
        if ($this->layout) {
            $layoutData = array_merge($this->data, ['content' => $content]);
            return $this->renderTemplate($this->layout, $layoutData);
        }
        
        return $content;
    }
    
    /**
     * Render template
     */
    private function renderTemplate(string $template, array $data): string
    {
        // Simple template rendering
        $content = file_get_contents($template);
        
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $content = str_replace("{{ $key }}", $value, $content);
            }
        }
        
        return $content;
    }
}

class Controller
{
    protected array $services;
    
    public function __construct(array $services)
    {
        $this->services = $services;
    }
    
    /**
     * Render view
     */
    protected function render(string $template, array $data = []): HttpResponse
    {
        $view = new View($template, $data);
        return new HttpResponse($view->render());
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $status = 302): HttpResponse
    {
        return new HttpResponse('', $status, ['Location' => $url]);
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, int $status = 200): HttpResponse
    {
        return new HttpResponse(json_encode($data), $status, ['Content-Type' => 'application/json']);
    }
}

// Dependency Injection Container
class DIContainer
{
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];
    
    /**
     * Bind a class to the container
     */
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }
    
    /**
     * Bind a singleton
     */
    public function singleton(string $abstract, callable $concrete): void
    {
        $this->singletons[$abstract] = $concrete;
    }
    
    /**
     * Resolve a dependency
     */
    public function resolve(string $abstract)
    {
        // Check for existing instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        // Check for singleton
        if (isset($this->singletons[$abstract])) {
            $instance = $this->singletons[$abstract]($this);
            $this->instances[$abstract] = $instance;
            return $instance;
        }
        
        // Check for regular binding
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]($this);
        }
        
        // Try to instantiate directly
        if (class_exists($abstract)) {
            return $this->build($abstract);
        }
        
        throw new \Exception("Unable to resolve dependency: $abstract");
    }
    
    /**
     * Build class with constructor injection
     */
    private function build(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return new $class();
        }
        
        $dependencies = [];
        
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->resolve($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Unable to resolve dependency: {$parameter->getName()}");
            }
        }
        
        return $reflection->newInstanceArgs($dependencies);
    }
}

// Event System
class EventDispatcher
{
    private array $listeners = [];
    
    /**
     * Add event listener
     */
    public function listen(string $event, callable $listener, int $priority = 0): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        
        $this->listeners[$event][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort($this->listeners[$event], function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }
    
    /**
     * Dispatch event
     */
    public function dispatch(string $event, array $data = []): void
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listenerInfo) {
                $listenerInfo['listener']($data);
            }
        }
    }
}

// Plugin Architecture
class PluginManager
{
    private array $plugins = [];
    private array $hooks = [];
    
    /**
     * Register a plugin
     */
    public function registerPlugin(PluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = $plugin;
        $plugin->register();
    }
    
    /**
     * Register a hook
     */
    public function registerHook(string $hook, callable $callback): void
    {
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = [];
        }
        
        $this->hooks[$hook][] = $callback;
    }
    
    /**
     * Execute a hook
     */
    public function executeHook(string $hook, array $args = []): array
    {
        $results = [];
        
        if (isset($this->hooks[$hook])) {
            foreach ($this->hooks[$hook] as $callback) {
                $results[] = call_user_func_array($callback, $args);
            }
        }
        
        return $results;
    }
    
    /**
     * Get all plugins
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }
    
    /**
     * Get plugin by name
     */
    public function getPlugin(string $name): ?PluginInterface
    {
        return $this->plugins[$name] ?? null;
    }
}

interface PluginInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function register(): void;
    public function boot(): void;
    public function shutdown(): void;
}

// Service Locator Pattern
class ServiceLocator
{
    private array $services = [];
    
    /**
     * Register a service
     */
    public function register(string $name, object $service): void
    {
        $this->services[$name] = $service;
    }
    
    /**
     * Get a service
     */
    public function get(string $name): object
    {
        if (!isset($this->services[$name])) {
            throw new \Exception("Service not found: $name");
        }
        
        return $this->services[$name];
    }
    
    /**
     * Check if service exists
     */
    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }
}

// Registry Pattern
class Registry
{
    private static array $data = [];
    
    /**
     * Set a value
     */
    public static function set(string $key, $value): void
    {
        self::$data[$key] = $value;
    }
    
    /**
     * Get a value
     */
    public static function get(string $key, $default = null)
    {
        return self::$data[$key] ?? $default;
    }
    
    /**
     * Check if key exists
     */
    public static function has(string $key): bool
    {
        return isset(self::$data[$key]);
    }
    
    /**
     * Remove a value
     */
    public static function remove(string $key): void
    {
        unset(self::$data[$key]);
    }
    
    /**
     * Clear all data
     */
    public static function clear(): void
    {
        self::$data = [];
    }
}

// Factory Pattern
class ModelFactory
{
    private DIContainer $container;
    
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }
    
    /**
     * Create a model instance
     */
    public function create(string $modelClass, array $attributes = []): Model
    {
        if (!class_exists($modelClass)) {
            throw new \Exception("Model class not found: $modelClass");
        }
        
        $model = new $modelClass($attributes);
        
        // Inject dependencies if model uses them
        if (method_exists($model, 'setContainer')) {
            $model->setContainer($this->container);
        }
        
        return $model;
    }
    
    /**
     * Create multiple models
     */
    public function createMany(string $modelClass, array $attributesList): array
    {
        $models = [];
        
        foreach ($attributesList as $attributes) {
            $models[] = $this->create($modelClass, $attributes);
        }
        
        return $models;
    }
}

// Repository Pattern
abstract class Repository
{
    protected ModelFactory $factory;
    protected array $models = [];
    
    public function __construct(ModelFactory $factory)
    {
        $this->factory = $factory;
    }
    
    /**
     * Find model by ID
     */
    public function find(int $id): ?Model
    {
        return $this->models[$id] ?? null;
    }
    
    /**
     * Get all models
     */
    public function all(): array
    {
        return array_values($this->models);
    }
    
    /**
     * Save model
     */
    public function save(Model $model): bool
    {
        $model->save();
        
        if ($model->id) {
            $this->models[$model->id] = $model;
        }
        
        return true;
    }
    
    /**
     * Delete model
     */
    public function delete(Model $model): bool
    {
        if ($model->delete()) {
            unset($this->models[$model->id]);
            return true;
        }
        
        return false;
    }
}

// Active Record Pattern
class ActiveRecord extends Model
{
    private static $table;
    private static $connection;
    
    /**
     * Find record by ID
     */
    public static function find(int $id): ?self
    {
        $data = self::getDatabaseConnection()->select(self::$table, ['id' => $id]);
        
        if ($data) {
            return new static($data);
        }
        
        return null;
    }
    
    /**
     * Get all records
     */
    public static function all(): array
    {
        $data = self::getDatabaseConnection()->selectAll(self::$table);
        $models = [];
        
        foreach ($data as $row) {
            $models[] = new static($row);
        }
        
        return $models;
    }
    
    /**
     * Create new record
     */
    public static function create(array $attributes): self
    {
        $model = new static($attributes);
        $model->save();
        
        return $model;
    }
    
    /**
     * Get database connection
     */
    protected static function getDatabaseConnection()
    {
        if (!self::$connection) {
            self::$connection = new DatabaseConnection();
        }
        
        return self::$connection;
    }
    
    /**
     * Set table name
     */
    public static function setTable(string $table): void
    {
        self::$table = $table;
    }
    
    /**
     * Get table name
     */
    public static function getTable(): string
    {
        return self::$table ?? strtolower(class_basename(static::class)) . 's';
    }
}

// Observer Pattern
abstract class Observer
{
    protected Model $model;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    abstract public function created(): void;
    abstract public function updated(): void;
    abstract public function deleted(): void;
}

class ModelObserver
{
    protected static array $observers = [];
    
    /**
     * Register an observer
     */
    public static function observe(string $modelClass, string $observerClass): void
    {
        if (!isset(self::$observers[$modelClass])) {
            self::$observers[$modelClass] = [];
        }
        
        self::$observers[$modelClass][] = $observerClass;
    }
    
    /**
     * Fire created event
     */
    public static function fireCreated(Model $model): void
    {
        $modelClass = get_class($model);
        
        if (isset(self::$observers[$modelClass])) {
            foreach (self::$observers[$modelClass] as $observerClass) {
                $observer = new $observerClass($model);
                $observer->created();
            }
        }
    }
    
    /**
     * Fire updated event
     */
    public static function fireUpdated(Model $model): void
    {
        $modelClass = get_class($model);
        
        if (isset(self::$observers[$modelClass])) {
            foreach (self::$observers[$modelClass] as $observerClass) {
                $observer = new $observerClass($model);
                $observer->updated();
            }
        }
    }
    
    /**
     * Fire deleted event
     */
    public static function fireDeleted(Model $model): void
    {
        $modelClass = get_class($model);
        
        if (isset(self::$observers[$modelClass])) {
            foreach (self::$observers[$modelClass] as $observerClass) {
                $observer = new $observerClass($model);
                $observer->deleted();
            }
        }
    }
}

// Framework Architecture Examples
class FrameworkArchitectureExamples
{
    private FrontController $frontController;
    private DIContainer $container;
    private EventDispatcher $dispatcher;
    private PluginManager $pluginManager;
    
    public function __construct()
    {
        $this->frontController = new FrontController();
        $this->container = new DIContainer();
        $this->dispatcher = new EventDispatcher();
        $this->pluginManager = new PluginManager();
    }
    
    public function demonstrateFrontController(): void
    {
        echo "Front Controller Pattern Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create HTTP request
        $request = new HttpRequest('GET', '/');
        
        // Handle request
        $response = $this->frontController->handle($request);
        
        echo "Request: " . $request->getMethod() . " " . $request->getPath() . "\n";
        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Content: " . substr($response->getContent(), 0, 50) . "...\n";
        
        // Test different routes
        $routes = [
            new HttpRequest('GET', '/users'),
            new HttpRequest('GET', '/users/123'),
            new HttpRequest('GET', '/api/users'),
            new HttpRequest('GET', '/admin')
        ];
        
        foreach ($routes as $req) {
            echo "\nRequest: " . $req->getPath() . "\n";
            $resp = $this->frontController->handle($req);
            echo "Response: " . substr($resp->getContent(), 0, 30) . "...\n";
        }
    }
    
    public function demonstrateMVC(): void
    {
        echo "\nMVC Pattern Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create a model
        $user = new Model([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30
        ]);
        
        echo "Model created:\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Age: " . $user->age . "\n";
        
        // Create a view
        $view = new View('user_profile.html', [
            'user' => $user,
            'title' => 'User Profile'
        ]);
        
        echo "\nView created with template: user_profile.html\n";
        echo "Data passed: user, title\n";
        
        // Create a controller
        $services = ['view' => $view];
        $controller = new UserController($services);
        
        echo "\nController created: UserController\n";
        echo "Services injected: view\n";
    }
    
    public function demonstrateDependencyInjection(): void
    {
        echo "\nDependency Injection Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Register services
        $this->container->bind(DatabaseConnection::class, function() {
            return new DatabaseConnection();
        });
        
        $this->container->bind(Logger::class, function() {
            return new Logger();
        });
        
        $this->container->singleton(Cache::class, function() {
            return new Cache();
        });
        
        // Resolve services
        $db = $this->container->resolve(DatabaseConnection::class);
        $logger = $this->container->resolve(Logger::class);
        $cache = $this->container->resolve(Cache::class);
        
        echo "Services resolved:\n";
        echo "Database: " . get_class($db) . "\n";
        echo "Logger: " . get_class($logger) . "\n";
        echo "Cache: " . get_class($cache) . "\n";
        
        // Test singleton
        $cache2 = $this->container->resolve(Cache::class);
        echo "Cache is singleton: " . ($cache === $cache2 ? 'true' : 'false') . "\n";
        
        // Resolve service with dependencies
        $this->container->bind(UserService::class, function($container) {
            return new UserService(
                $container->resolve(DatabaseConnection::class),
                $container->resolve(Logger::class)
            );
        });
        
        $userService = $this->container->resolve(UserService::class);
        echo "UserService resolved with dependencies\n";
    }
    
    public function demonstrateEventSystem(): void
    {
        echo "\nEvent System Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Add event listeners
        $this->dispatcher->listen('user.created', function($data) {
            echo "User created event triggered for: {$data['name']}\n";
        });
        
        $this->dispatcher->listen('user.created', function($data) {
            echo "Sending welcome email to: {$data['email']}\n";
        }, 100); // Higher priority
        
        $this->dispatcher->listen('user.deleted', function($data) {
            echo "User deleted event triggered for: {$data['name']}\n";
        });
        
        // Dispatch events
        echo "Dispatching user.created event:\n";
        $this->dispatcher->dispatch('user.created', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        echo "\nDispatching user.deleted event:\n";
        $this->dispatcher->dispatch('user.deleted', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com'
        ]);
    }
    
    public function demonstratePluginArchitecture(): void
    {
        echo "\nPlugin Architecture Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create plugins
        $authPlugin = new AuthenticationPlugin();
        $cachePlugin = new CachingPlugin();
        $analyticsPlugin = new AnalyticsPlugin();
        
        // Register plugins
        $this->pluginManager->registerPlugin($authPlugin);
        $this->pluginManager->registerPlugin($cachePlugin);
        $this->pluginManager->registerPlugin($analyticsPlugin);
        
        echo "Registered plugins:\n";
        foreach ($this->pluginManager->getPlugins() as $plugin) {
            echo "- {$plugin->getName()} v{$plugin->getVersion()}\n";
        }
        
        // Register hooks
        $this->pluginManager->registerHook('user.login', function($user) {
            echo "Login hook: {$user['name']} logged in\n";
        });
        
        $this->pluginManager->registerHook('user.logout', function($user) {
            echo "Logout hook: {$user['name']} logged out\n";
        });
        
        // Execute hooks
        echo "\nExecuting user.login hook:\n";
        $this->pluginManager->executeHook('user.login', [
            ['name' => 'John Doe', 'email' => 'john@example.com']
        ]);
        
        echo "\nExecuting user.logout hook:\n";
        $this->pluginManager->executeHook('user.logout', [
            ['name' => 'John Doe', 'email' => 'john@example.com']
        ]);
    }
    
    public function demonstrateActiveRecord(): void
    {
        echo "\nActive Record Pattern Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Set up ActiveRecord
        User::setTable('users');
        
        // Create user
        $user = User::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'age' => 25
        ]);
        
        echo "Created user with ID: {$user->id}\n";
        echo "User name: {$user->name}\n";
        echo "User email: {$user->email}\n";
        
        // Find user
        $foundUser = User::find($user->id);
        
        echo "\nFound user: " . ($foundUser ? $foundUser->name : 'Not found') . "\n";
        
        // Update user
        $user->age = 26;
        $user->save();
        
        echo "Updated user age to: {$user->age}\n";
        
        // Get all users
        $allUsers = User::all();
        echo "Total users: " . count($allUsers) . "\n";
        
        // Delete user
        $user->delete();
        echo "User deleted\n";
    }
    
    public function demonstrateObserver(): void
    {
        echo "\nObserver Pattern Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Register observers
        ModelObserver::observe(User::class, UserObserver::class);
        
        // Create user (triggers created event)
        $user = new User([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com'
        ]);
        
        echo "Creating user...\n";
        ModelObserver::fireCreated($user);
        
        // Update user (triggers updated event)
        $user->name = 'Bob Smith';
        echo "\nUpdating user...\n";
        ModelObserver::fireUpdated($user);
        
        // Delete user (triggers deleted event)
        echo "\nDeleting user...\n";
        ModelObserver::fireDeleted($user);
    }
    
    public function demonstrateFactory(): void
    {
        echo "\nFactory Pattern Example\n";
        echo str_repeat("-", 30) . "\n";
        
        $factory = new ModelFactory($this->container);
        
        // Create single model
        $user = $factory->create(User::class, [
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com'
        ]);
        
        echo "Created user: {$user->name}\n";
        
        // Create multiple models
        $users = $factory->createMany(User::class, [
            ['name' => 'User 1', 'email' => 'user1@example.com'],
            ['name' => 'User 2', 'email' => 'user2@example.com'],
            ['name' => 'User 3', 'email' => 'user3@example.com']
        ]);
        
        echo "Created " . count($users) . " users\n";
        foreach ($users as $index => $user) {
            echo "  User " . ($index + 1) . ": {$user->name}\n";
        }
    }
    
    public function demonstrateRepository(): void
    {
        echo "\nRepository Pattern Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $factory = new ModelFactory($this->container);
        $repository = new UserRepository($factory);
        
        // Create and save users
        $user1 = $factory->create(User::class, [
            'name' => 'David Wilson',
            'email' => 'david@example.com'
        ]);
        
        $user2 = $factory->create(User::class, [
            'name' => 'Emma Davis',
            'email' => 'emma@example.com'
        ]);
        
        $repository->save($user1);
        $repository->save($user2);
        
        echo "Saved 2 users to repository\n";
        
        // Find user
        $foundUser = $repository->find($user1->id);
        echo "Found user: " . ($foundUser ? $foundUser->name : 'Not found') . "\n";
        
        // Get all users
        $allUsers = $repository->all();
        echo "Total users in repository: " . count($allUsers) . "\n";
        
        // Delete user
        $repository->delete($user2);
        echo "Deleted one user\n";
        
        $remainingUsers = $repository->all();
        echo "Remaining users: " . count($remainingUsers) . "\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Framework Architecture Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateFrontController();
        $this->demonstrateMVC();
        $this->demonstrateDependencyInjection();
        $this->demonstrateEventSystem();
        $this->demonstratePluginArchitecture();
        $this->demonstrateActiveRecord();
        $this->demonstrateObserver();
        $this->demonstrateFactory();
        $this->demonstrateRepository();
    }
}

// Supporting classes for examples
class HttpRequest
{
    private string $method;
    private string $path;
    private array $parameters;
    
    public function __construct(string $method, string $path, array $parameters = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->parameters = $parameters;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getPath(): string
    {
        return $this->path;
    }
    
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    public function isRedirected(): bool
    {
        return false;
    }
}

class HttpResponse
{
    private string $content;
    private int $statusCode;
    private array $headers;
    
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

class Router
{
    public function match(HttpRequest $request): ?array
    {
        $path = $request->getPath();
        
        $routes = [
            '/' => ['controller' => 'HomeController', 'action' => 'index'],
            '/users' => ['controller' => 'UserController', 'action' => 'index'],
            '/users/{id}' => ['controller' => 'UserController', 'action' => 'show'],
            '/api/users' => ['controller' => 'ApiController', 'action' => 'users'],
            '/admin' => ['controller' => 'AdminController', 'action' => 'dashboard']
        ];
        
        // Simple route matching
        if (isset($routes[$path])) {
            return $routes[$path];
        }
        
        // Check for parameterized routes
        if ($path === '/users/123') {
            return ['controller' => 'UserController', 'action' => 'show'];
        }
        
        return null;
    }
}

class RequestHandler {}
class ResponseHandler {}
class ViewEngine {}

class HomeController extends Controller
{
    public function index(HttpRequest $request): HttpResponse
    {
        return $this->render('home.html', ['title' => 'Welcome']);
    }
}

class UserController extends Controller
{
    public function index(HttpRequest $request): HttpResponse
    {
        return $this->json(['users' => ['John', 'Jane', 'Bob']]);
    }
    
    public function show(HttpRequest $request): HttpResponse
    {
        return $this->json(['user' => 'John Doe']);
    }
}

class ApiController extends Controller
{
    public function users(HttpRequest $request): HttpResponse
    {
        return $this->json(['users' => [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ]]);
    }
}

class AdminController extends Controller
{
    public function dashboard(HttpRequest $request): HttpResponse
    {
        return $this->render('admin/dashboard.html', ['title' => 'Admin Dashboard']);
    }
}

class AuthenticationMiddleware
{
    public function handle(HttpRequest $request): HttpRequest
    {
        echo "Authentication middleware applied\n";
        return $request;
    }
}

class CorsMiddleware
{
    public function handle(HttpRequest $request): HttpRequest
    {
        echo "CORS middleware applied\n";
        return $request;
    }
}

class RateLimitMiddleware
{
    public function handle(HttpRequest $request): HttpRequest
    {
        echo "Rate limiting middleware applied\n";
        return $request;
    }
}

class LoggingMiddleware
{
    public function handle(HttpRequest $request): HttpRequest
    {
        echo "Logging middleware applied\n";
        return $request;
    }
}

class DatabaseConnection {}
class Logger {}
class Cache {}
class UserService
{
    public function __construct(DatabaseConnection $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }
}

class AuthenticationPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Authentication';
    }
    
    public function getVersion(): string
    {
        return '1.0.0';
    }
    
    public function register(): void
    {
        echo "Authentication plugin registered\n";
    }
    
    public function boot(): void
    {
        echo "Authentication plugin booted\n";
    }
    
    public function shutdown(): void
    {
        echo "Authentication plugin shutdown\n";
    }
}

class CachingPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Caching';
    }
    
    public function getVersion(): string
    {
        return '2.1.0';
    }
    
    public function register(): void
    {
        echo "Caching plugin registered\n";
    }
    
    public function boot(): void
    {
        echo "Caching plugin booted\n";
    }
    
    public function shutdown(): void
    {
        echo "Caching plugin shutdown\n";
    }
}

class AnalyticsPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Analytics';
    }
    
    public function getVersion(): string
    {
        return '1.5.0';
    }
    
    public function register(): void
    {
        echo "Analytics plugin registered\n";
    }
    
    public function boot(): void
    {
        echo "Analytics plugin booted\n";
    }
    
    public function shutdown(): void
    {
        echo "Analytics plugin shutdown\n";
    }
}

class User extends ActiveRecord {}
class UserObserver extends Observer
{
    public function created(): void
    {
        echo "User created: {$this->model->name}\n";
    }
    
    public function updated(): void
    {
        echo "User updated: {$this->model->name}\n";
    }
    
    public function deleted(): void
    {
        echo "User deleted: {$this->model->name}\n";
    }
}

class UserRepository extends Repository
{
    public function __construct(ModelFactory $factory)
    {
        parent::__construct($factory);
    }
}

function class_basename($class): string
{
    return substr($class, strrpos($class, '\\') + 1);
}

// Framework Architecture Best Practices
function printFrameworkArchitectureBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Framework Architecture Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Front Controller Pattern:\n";
    echo "   • Centralize request handling\n";
    echo "   • Implement proper routing\n";
    echo "   • Use middleware pipeline\n";
    echo "   • Handle errors gracefully\n";
    echo "   • Support different request types\n\n";
    
    echo "2. MVC Pattern:\n";
    echo "   • Keep controllers thin\n";
    echo "   • Make models fat\n";
    echo "   • Use views for presentation\n";
    echo "   • Implement proper separation\n";
    echo "   • Avoid business logic in views\n\n";
    
    echo "3. Dependency Injection:\n";
    echo "   • Use constructor injection\n";
    echo "   • Avoid service locator\n";
    echo "   • Bind interfaces to implementations\n";
    echo "   • Use singletons wisely\n";
    echo "   • Implement proper scoping\n\n";
    
    echo "4. Event System:\n";
    echo "   • Use events for loose coupling\n";
    echo "   • Create meaningful events\n";
    echo "   • Avoid event chains\n";
    echo "   • Use event subscribers\n";
    echo "   • Keep event handlers simple\n\n";
    
    echo "5. Plugin Architecture:\n";
    echo "   • Design extensible plugins\n";
    echo "   • Use hook system\n";
    echo "   • Implement plugin discovery\n";
    echo "   • Handle plugin dependencies\n";
    echo "   • Provide plugin documentation\n\n";
    
    echo "6. Design Patterns:\n";
    echo "   • Use patterns appropriately\n";
    echo "   • Don't over-engineer\n";
    echo "   • Follow SOLID principles\n";
    echo "   • Keep code maintainable\n";
    echo "   • Document pattern usage";
}

// Main execution
function runFrameworkArchitectureDemo(): void
{
    $examples = new FrameworkArchitectureExamples();
    $examples->runAllExamples();
    printFrameworkArchitectureBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runFrameworkArchitectureDemo();
}
?>
