<?php
/**
 * Laravel Framework Basics
 * 
 * This file demonstrates Laravel framework concepts,
 * components, and best practices for building applications.
 */

// Laravel Service Container (Dependency Injection)
class LaravelServiceContainer
{
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];
    
    /**
     * Bind a concrete implementation to an interface
     */
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }
    
    /**
     * Bind a singleton instance
     */
    public function singleton(string $abstract, callable $concrete): void
    {
        $this->singletons[$abstract] = $concrete;
    }
    
    /**
     * Resolve an instance from the container
     */
    public function make(string $abstract)
    {
        // Check if we already have an instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        // Check if it's a singleton
        if (isset($this->singletons[$abstract])) {
            $instance = $this->singletons[$abstract]($this);
            $this->instances[$abstract] = $instance;
            return $instance;
        }
        
        // Check if it's a regular binding
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]($this);
        }
        
        // Try to instantiate directly
        if (class_exists($abstract)) {
            return $this->build($abstract);
        }
        
        throw new \Exception("Unable to resolve dependency: {$abstract}");
    }
    
    /**
     * Build a class with automatic dependency injection
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
                $dependencies[] = $this->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Unable to resolve dependency: {$parameter->getName()}");
            }
        }
        
        return $reflection->newInstanceArgs($dependencies);
    }
}

// Laravel Eloquent ORM Simulation
class EloquentModel
{
    protected string $table;
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;
    protected static string $connection = 'default';
    
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }
    
    /**
     * Fill the model with an array of attributes
     */
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }
    
    /**
     * Set a given attribute on the model
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
        
        if ($this->exists) {
            $this->original[$key] = $value;
        }
    }
    
    /**
     * Get an attribute from the model
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
    
    /**
     * Save the model to the database
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        
        return $this->insert();
    }
    
    /**
     * Insert a new record
     */
    private function insert(): bool
    {
        // Simulate database insert
        $this->attributes['id'] = rand(1, 1000);
        $this->exists = true;
        $this->original = $this->attributes;
        
        return true;
    }
    
    /**
     * Update an existing record
     */
    private function update(): bool
    {
        // Simulate database update
        $this->original = $this->attributes;
        
        return true;
    }
    
    /**
     * Delete the model from the database
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        // Simulate database delete
        $this->exists = false;
        
        return true;
    }
    
    /**
     * Get the table name for the model
     */
    public function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }
        
        return strtolower(class_basename(static::class)) . 's';
    }
    
    /**
     * Magic method to get attributes
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }
    
    /**
     * Magic method to set attributes
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
}

// Laravel Query Builder Simulation
class QueryBuilder
{
    protected string $table;
    protected array $wheres = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    
    public function __construct(string $table)
    {
        $this->table = $table;
    }
    
    /**
     * Add a where clause
     */
    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        
        return $this;
    }
    
    /**
     * Add an orderBy clause
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => $direction
        ];
        
        return $this;
    }
    
    /**
     * Set the limit
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        
        return $this;
    }
    
    /**
     * Set the offset
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        
        return $this;
    }
    
    /**
     * Get the first result
     */
    public function first(): ?array
    {
        $this->limit = 1;
        $results = $this->get();
        
        return $results[0] ?? null;
    }
    
    /**
     * Get all results
     */
    public function get(): array
    {
        // Simulate database query
        $this->logQuery();
        
        return $this->simulateResults();
    }
    
    /**
     * Insert a record
     */
    public function insert(array $data): bool
    {
        $this->logQuery();
        
        return true;
    }
    
    /**
     * Update records
     */
    public function update(array $data): int
    {
        $this->logQuery();
        
        return rand(1, 10);
    }
    
    /**
     * Delete records
     */
    public function delete(): int
    {
        $this->logQuery();
        
        return rand(1, 10);
    }
    
    /**
     * Log the query (simulation)
     */
    private function logQuery(): void
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }
        
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . $this->buildOrderClause();
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        echo "Query: $sql\n";
    }
    
    /**
     * Build WHERE clause
     */
    private function buildWhereClause(): string
    {
        $clauses = [];
        
        foreach ($this->wheres as $where) {
            $clauses[] = "{$where['column']} {$where['operator']} '{$where['value']}'";
        }
        
        return implode(' AND ', $clauses);
    }
    
    /**
     * Build ORDER BY clause
     */
    private function buildOrderClause(): string
    {
        $clauses = [];
        
        foreach ($this->orders as $order) {
            $clauses[] = "{$order['column']} {$order['direction']}";
        }
        
        return implode(', ', $clauses);
    }
    
    /**
     * Simulate query results
     */
    private function simulateResults(): array
    {
        $results = [];
        $count = $this->limit ?? rand(5, 20);
        
        for ($i = 1; $i <= $count; $i++) {
            $results[] = [
                'id' => $i,
                'name' => "Item $i",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $results;
    }
}

// Laravel Blade Template Engine Simulation
class BladeEngine
{
    private array $data = [];
    private array $directives = [];
    
    public function __construct()
    {
        $this->registerDefaultDirectives();
    }
    
    /**
     * Register default Blade directives
     */
    private function registerDefaultDirectives(): void
    {
        $this->directives['if'] = function($expression) {
            return "<?php if($expression): ?>";
        };
        
        $this->directives['elseif'] = function($expression) {
            return "<?php elseif($expression): ?>";
        };
        
        $this->directives['else'] = function() {
            return "<?php else: ?>";
        };
        
        $this->directives['endif'] = function() {
            return "<?php endif; ?>";
        };
        
        $this->directives['foreach'] = function($expression) {
            return "<?php foreach($expression): ?>";
        };
        
        $this->directives['endforeach'] = function() {
            return "<?php endforeach; ?>";
        };
        
        $this->directives['csrf'] = function() {
            return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
        };
    }
    
    /**
     * Share data with all views
     */
    public function share(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Render a Blade template
     */
    public function render(string $template, array $data = []): string
    {
        $data = array_merge($this->data, $data);
        
        // Process directives
        $content = $this->processDirectives($template);
        
        // Process variables
        $content = $this->processVariables($content, $data);
        
        // Process includes
        $content = $this->processIncludes($content);
        
        return $content;
    }
    
    /**
     * Process Blade directives
     */
    private function processDirectives(string $template): string
    {
        foreach ($this->directives as $directive => $handler) {
            $pattern = "/@{$directive}\((.*?)\)/";
            
            if (preg_match_all($pattern, $template, $matches)) {
                foreach ($matches[0] as $match) {
                    $expression = str_replace(["@{$directive}(", ")"], "", $match);
                    $template = str_replace($match, $handler($expression), $template);
                }
            }
        }
        
        return $template;
    }
    
    /**
     * Process template variables
     */
    private function processVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $content = str_replace("{{ \$$key }}", $value, $content);
                $content = str_replace("{{ \$$key ?? '' }}", $value, $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Process template includes
     */
    private function processIncludes(string $content): string
    {
        // Simulate include processing
        return $content;
    }
    
    /**
     * Compile a Blade template to PHP
     */
    public function compile(string $template): string
    {
        // This would normally compile to PHP and cache
        return $this->render($template);
    }
}

// Laravel Routing System Simulation
class Router
{
    private array $routes = [];
    private array $middleware = [];
    
    /**
     * Register a GET route
     */
    public function get(string $uri, callable $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }
    
    /**
     * Register a POST route
     */
    public function post(string $uri, callable $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }
    
    /**
     * Register a PUT route
     */
    public function put(string $uri, callable $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }
    
    /**
     * Register a DELETE route
     */
    public function delete(string $uri, callable $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }
    
    /**
     * Add a route to the collection
     */
    private function addRoute(string $method, string $uri, callable $action): void
    {
        $this->routes[$method][$uri] = $action;
    }
    
    /**
     * Dispatch a request
     */
    public function dispatch(string $method, string $uri)
    {
        $uri = $this->normalizeUri($uri);
        
        if (!isset($this->routes[$method])) {
            return $this->handleNotFound();
        }
        
        foreach ($this->routes[$method] as $routeUri => $action) {
            if ($this->uriMatches($routeUri, $uri, $params)) {
                return $this->callAction($action, $params);
            }
        }
        
        return $this->handleNotFound();
    }
    
    /**
     * Normalize URI
     */
    private function normalizeUri(string $uri): string
    {
        return rtrim($uri, '/');
    }
    
    /**
     * Check if URI matches route pattern
     */
    private function uriMatches(string $routeUri, string $uri, array &$params): bool
    {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . str_replace('/', '\/', $routePattern) . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Extract parameter names
            preg_match_all('/\{([^}]+)\}/', $routeUri, $paramNames);
            
            foreach ($paramNames[1] as $index => $paramName) {
                $params[$paramName] = $matches[$index + 1];
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Call the route action
     */
    private function callAction(callable $action, array $params)
    {
        return call_user_func_array($action, $params);
    }
    
    /**
     * Handle 404 not found
     */
    private function handleNotFound()
    {
        return '404 Not Found';
    }
    
    /**
     * Register middleware
     */
    public function middleware(string $name, callable $middleware): void
    {
        $this->middleware[$name] = $middleware;
    }
    
    /**
     * Apply middleware to route
     */
    public function applyMiddleware(string $middlewareName, callable $action): callable
    {
        if (!isset($this->middleware[$middlewareName])) {
            throw new \Exception("Middleware not found: $middlewareName");
        }
        
        return function(...$params) use ($middlewareName, $action) {
            $result = $this->middleware[$middlewareName]();
            
            if ($result === true) {
                return call_user_func_array($action, $params);
            }
            
            return $result;
        };
    }
}

// Laravel Middleware Simulation
class Middleware
{
    public static function auth(): bool
    {
        // Simulate authentication check
        return isset($_SESSION['user']);
    }
    
    public static function guest(): bool
    {
        // Simulate guest check
        return !isset($_SESSION['user']);
    }
    
    public static function cors(): bool
    {
        // Simulate CORS handling
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        return true;
    }
    
    public static function throttle(int $limit = 60): bool
    {
        // Simulate rate limiting
        $key = 'rate_limit_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $count = $_SESSION[$key] ?? 0;
        
        if ($count >= $limit) {
            http_response_code(429);
            echo 'Too Many Requests';
            return false;
        }
        
        $_SESSION[$key] = $count + 1;
        return true;
    }
}

// Laravel Request Simulation
class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $headers;
    private string $method;
    private string $uri;
    
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->headers = $this->getHeaders();
    }
    
    /**
     * Get input from request
     */
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }
    
    /**
     * Get all input
     */
    public function all(): array
    {
        return array_merge($this->post, $this->get);
    }
    
    /**
     * Check if request has input
     */
    public function has(string $key): bool
    {
        return $this->input($key) !== null;
    }
    
    /**
     * Get JSON input
     */
    public function json(string $key = null, $default = null)
    {
        $json = json_decode(file_get_contents('php://input'), true);
        
        if ($key === null) {
            return $json;
        }
        
        return $json[$key] ?? $default;
    }
    
    /**
     * Get request method
     */
    public function method(): string
    {
        return $this->method;
    }
    
    /**
     * Get request URI
     */
    public function uri(): string
    {
        return $this->uri;
    }
    
    /**
     * Get request headers
     */
    private function getHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('HTTP_', '', $key);
                $header = str_replace('_', ' ', $header);
                $header = ucwords(strtolower($header));
                $header = str_replace(' ', '-', $header);
                
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }
    
    /**
     * Get header value
     */
    public function header(string $key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }
}

// Laravel Response Simulation
class Response
{
    private string $content;
    private int $status;
    private array $headers;
    
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }
    
    /**
     * Create a JSON response
     */
    public static function json($data, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json';
        
        return new self(json_encode($data), $status, $headers);
    }
    
    /**
     * Create a redirect response
     */
    public static function redirect(string $url, int $status = 302): self
    {
        $headers['Location'] = $url;
        
        return new self('', $status, $headers);
    }
    
    /**
     * Send the response
     */
    public function send(): void
    {
        http_response_code($this->status);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $this->content;
    }
    
    /**
     * Get response content
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * Get response status
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}

// Laravel Artisan Command Simulation
class ArtisanCommand
{
    protected string $name;
    protected string $description;
    protected array $arguments = [];
    protected array $options = [];
    
    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }
    
    /**
     * Add an argument
     */
    protected function addArgument(string $name, string $description, bool $required = false): void
    {
        $this->arguments[$name] = [
            'description' => $description,
            'required' => $required
        ];
    }
    
    /**
     * Add an option
     */
    protected function addOption(string $name, string $description, bool $required = false): void
    {
        $this->options[$name] = [
            'description' => $description,
            'required' => $required
        ];
    }
    
    /**
     * Handle the command
     */
    public function handle(array $arguments = [], array $options = []): string
    {
        return "Command '{$this->name}' executed successfully";
    }
    
    /**
     * Get command signature
     */
    public function getSignature(): string
    {
        $signature = $this->name;
        
        foreach ($this->arguments as $name => $arg) {
            $signature .= $arg['required'] ? " <$name>" : " [$name]";
        }
        
        foreach ($this->options as $name => $option) {
            $signature .= $option['required'] ? " --$name=" : " [--$name]";
        }
        
        return $signature;
    }
    
    /**
     * Get command description
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}

// Laravel Migration Simulation
class Migration
{
    protected string $name;
    protected array $schema = [];
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * Create a new table
     */
    protected function create(string $table, callable $callback): void
    {
        $this->schema[$table] = [
            'action' => 'create',
            'columns' => []
        ];
        
        $builder = new SchemaBuilder($this->schema[$table]);
        $callback($builder);
    }
    
    /**
     * Add columns to an existing table
     */
    protected function add(string $table, callable $callback): void
    {
        if (!isset($this->schema[$table])) {
            $this->schema[$table] = [
                'action' => 'add',
                'columns' => []
            ];
        }
        
        $builder = new SchemaBuilder($this->schema[$table]);
        $callback($builder);
    }
    
    /**
     * Get migration SQL
     */
    public function toSql(): array
    {
        $sql = [];
        
        foreach ($this->schema as $table => $definition) {
            if ($definition['action'] === 'create') {
                $sql[] = $this->buildCreateTableSql($table, $definition['columns']);
            } else {
                $sql[] = $this->buildAlterTableSql($table, $definition['columns']);
            }
        }
        
        return $sql;
    }
    
    /**
     * Build CREATE TABLE SQL
     */
    private function buildCreateTableSql(string $table, array $columns): string
    {
        $sql = "CREATE TABLE `$table` (\n";
        $columnDefs = [];
        
        foreach ($columns as $name => $definition) {
            $columnDefs[] = "  `$name` {$definition['type']}{$definition['extra']}";
        }
        
        $sql .= implode(",\n", $columnDefs);
        $sql .= "\n)";
        
        return $sql;
    }
    
    /**
     * Build ALTER TABLE SQL
     */
    private function buildAlterTableSql(string $table, array $columns): string
    {
        $sql = "ALTER TABLE `$table` ";
        $alterations = [];
        
        foreach ($columns as $name => $definition) {
            $alterations[] = "ADD COLUMN `$name` {$definition['type']}{$definition['extra']}";
        }
        
        $sql .= implode(", ", $alterations);
        
        return $sql;
    }
}

// Laravel Schema Builder Simulation
class SchemaBuilder
{
    private array $schema;
    
    public function __construct(array &$schema)
    {
        $this->schema = &$schema;
    }
    
    /**
     * Add an integer column
     */
    public function integer(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'INT');
    }
    
    /**
     * Add a string column
     */
    public function string(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn($name, "VARCHAR($length)");
    }
    
    /**
     * Add a text column
     */
    public function text(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'TEXT');
    }
    
    /**
     * Add a timestamp column
     */
    public function timestamp(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'TIMESTAMP');
    }
    
    /**
     * Add timestamps
     */
    public function timestamps(): void
    {
        $this->addColumn('created_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('updated_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
    
    /**
     * Add a column definition
     */
    private function addColumn(string $name, string $type): ColumnDefinition
    {
        $definition = new ColumnDefinition($type);
        $this->schema['columns'][$name] = $definition;
        
        return $definition;
    }
}

// Laravel Column Definition Simulation
class ColumnDefinition
{
    public string $type;
    public string $extra = '';
    
    public function __construct(string $type)
    {
        $this->type = $type;
    }
    
    /**
     * Make column nullable
     */
    public function nullable(): self
    {
        $this->extra .= ' NULL';
        return $this;
    }
    
    /**
     * Add default value
     */
    public function default($value): self
    {
        if (is_string($value)) {
            $this->extra .= " DEFAULT '$value'";
        } else {
            $this->extra .= " DEFAULT $value";
        }
        
        return $this;
    }
    
    /**
     * Make column primary key
     */
    public function primary(): self
    {
        $this->extra .= ' PRIMARY KEY';
        return $this;
    }
    
    /**
     * Make column unique
     */
    public function unique(): self
    {
        $this->extra .= ' UNIQUE';
        return $this;
    }
    
    /**
     * Add auto increment
     */
    public function autoIncrement(): self
    {
        $this->extra .= ' AUTO_INCREMENT';
        return $this;
    }
}

// Laravel Examples
class LaravelExamples
{
    private LaravelServiceContainer $container;
    private Router $router;
    private BladeEngine $blade;
    
    public function __construct()
    {
        $this->container = new LaravelServiceContainer();
        $this->router = new Router();
        $this->blade = new BladeEngine();
    }
    
    public function demonstrateServiceContainer(): void
    {
        echo "Laravel Service Container Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Bind interfaces to implementations
        $this->container->bind(UserRepositoryInterface::class, function() {
            return new UserRepository();
        });
        
        $this->container->bind(EmailServiceInterface::class, function() {
            return new EmailService();
        });
        
        // Bind a singleton
        $this->container->singleton(LoggerInterface::class, function() {
            return new Logger();
        });
        
        // Resolve dependencies automatically
        $userService = $this->container->make(UserService::class);
        
        echo "UserService resolved successfully\n";
        echo "Logger is singleton: " . ($this->container->make(LoggerInterface::class) === $this->container->make(LoggerInterface::class) ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateEloquent(): void
    {
        echo "\nLaravel Eloquent Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create a new user
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        echo "Created user: " . $user->name . "\n";
        echo "User ID: " . $user->id . "\n";
        echo "User exists: " . ($user->exists ? 'true' : 'false') . "\n";
        
        // Update user
        $user->name = 'Jane Doe';
        $user->save();
        
        echo "Updated user name: " . $user->name . "\n";
        
        // Delete user
        $user->delete();
        echo "User deleted. Exists: " . ($user->exists ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateQueryBuilder(): void
    {
        echo "\nLaravel Query Builder Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $query = new QueryBuilder('users');
        
        // Build a complex query
        $results = $query->where('active', '=', 1)
            ->where('created_at', '>', '2023-01-01')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
        
        echo "Query executed. Results count: " . count($results) . "\n";
        
        // Get first result
        $first = $query->where('id', '=', 1)->first();
        echo "First result: " . ($first ? json_encode($first) : 'null') . "\n";
        
        // Update records
        $updated = $query->where('active', '=', 0)->update(['active' => 1]);
        echo "Updated records: $updated\n";
        
        // Delete records
        $deleted = $query->where('deleted_at', '!=', null)->delete();
        echo "Deleted records: $deleted\n";
    }
    
    public function demonstrateBlade(): void
    {
        echo "\nLaravel Blade Example\n";
        echo str_repeat("-", 25) . "\n";
        
        $template = <<<BLADE
            <h1>Welcome, {{ \$name }}!</h1>
            
            @if(\$isAdmin)
                <p>You are an administrator.</p>
            @else
                <p>You are a regular user.</p>
            @endif
            
            <ul>
            @foreach(\$users as \$user)
                <li>{{ \$user['name'] }} - {{ \$user['email'] }}</li>
            @endforeach
            </ul>
            
            @csrf
            BLADE;
        
        $data = [
            'name' => 'John Doe',
            'isAdmin' => true,
            'users' => [
                ['name' => 'Alice', 'email' => 'alice@example.com'],
                ['name' => 'Bob', 'email' => 'bob@example.com'],
                ['name' => 'Charlie', 'email' => 'charlie@example.com']
            ]
        ];
        
        $rendered = $this->blade->render($template, $data);
        
        echo "Rendered template:\n";
        echo $rendered . "\n";
    }
    
    public function demonstrateRouting(): void
    {
        echo "\nLaravel Routing Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Define routes
        $this->router->get('/', function() {
            return 'Welcome to the homepage!';
        });
        
        $this->router->get('/users/{id}', function($id) {
            return "User ID: $id";
        });
        
        $this->router->post('/users', function() {
            return 'User created!';
        });
        
        // Dispatch requests
        echo "GET /: " . $this->router->dispatch('GET', '/') . "\n";
        echo "GET /users/123: " . $this->router->dispatch('GET', '/users/123') . "\n";
        echo "POST /users: " . $this->router->dispatch('POST', '/users') . "\n";
        echo "GET /not-found: " . $this->router->dispatch('GET', '/not-found') . "\n";
    }
    
    public function demonstrateMiddleware(): void
    {
        echo "\nLaravel Middleware Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test middleware
        echo "Auth middleware: " . (Middleware::auth() ? 'Authenticated' : 'Not authenticated') . "\n";
        echo "Guest middleware: " . (Middleware::guest() ? 'Guest' : 'Not guest') . "\n";
        echo "CORS middleware: " . (Middleware::cors() ? 'CORS handled' : 'CORS failed') . "\n";
        echo "Throttle middleware: " . (Middleware::throttle(100) ? 'Request allowed' : 'Rate limited') . "\n";
    }
    
    public function demonstrateRequestResponse(): void
    {
        echo "\nLaravel Request/Response Example\n";
        echo str_repeat("-", 40) . "\n";
        
        // Create a request
        $_GET['name'] = 'John';
        $_POST['email'] = 'john@example.com';
        
        $request = new Request();
        
        echo "Request method: " . $request->method() . "\n";
        echo "Request URI: " . $request->uri() . "\n";
        echo "Input 'name': " . $request->input('name') . "\n";
        echo "Input 'email': " . $request->input('email') . "\n";
        echo "Has 'name': " . ($request->has('name') ? 'true' : 'false') . "\n";
        echo "All input: " . json_encode($request->all()) . "\n";
        
        // Create responses
        $htmlResponse = new Response('<h1>Hello World</h1>');
        echo "HTML response status: " . $htmlResponse->getStatus() . "\n";
        
        $jsonResponse = Response::json(['message' => 'Success'], 200);
        echo "JSON response content: " . $jsonResponse->getContent() . "\n";
        
        $redirectResponse = Response::redirect('/dashboard');
        echo "Redirect response status: " . $redirectResponse->getStatus() . "\n";
    }
    
    public function demonstrateArtisan(): void
    {
        echo "\nLaravel Artisan Commands Example\n";
        echo str_repeat("-", 40) . "\n";
        
        // Create commands
        $makeController = new ArtisanCommand('make:controller', 'Create a new controller');
        $makeModel = new ArtisanCommand('make:model', 'Create a new model');
        $migrate = new ArtisanCommand('migrate', 'Run database migrations');
        
        echo "Command: " . $makeController->getSignature() . "\n";
        echo "Description: " . $makeController->getDescription() . "\n";
        echo "Result: " . $makeController->handle() . "\n\n";
        
        echo "Command: " . $makeModel->getSignature() . "\n";
        echo "Description: " . $makeModel->getDescription() . "\n";
        echo "Result: " . $makeModel->handle() . "\n\n";
        
        echo "Command: " . $migrate->getSignature() . "\n";
        echo "Description: " . $migrate->getDescription() . "\n";
        echo "Result: " . $migrate->handle() . "\n";
    }
    
    public function demonstrateMigrations(): void
    {
        echo "\nLaravel Migrations Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create a migration
        $migration = new class extends Migration {
            public function __construct()
            {
                parent::__construct('create_users_table');
            }
            
            public function up()
            {
                $this->create('users', function($table) {
                    $table->integer('id')->primary()->autoIncrement();
                    $table->string('name')->nullable();
                    $table->string('email')->unique();
                    $table->timestamp('email_verified_at')->nullable();
                    $table->string('password');
                    $table->rememberToken();
                    $table->timestamps();
                });
            }
        };
        
        $migration->up();
        $sql = $migration->toSql();
        
        echo "Generated SQL:\n";
        foreach ($sql as $query) {
            echo "$query;\n";
        }
    }
    
    public function runAllExamples(): void
    {
        echo "Laravel Framework Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateServiceContainer();
        $this->demonstrateEloquent();
        $this->demonstrateQueryBuilder();
        $this->demonstrateBlade();
        $this->demonstrateRouting();
        $this->demonstrateMiddleware();
        $this->demonstrateRequestResponse();
        $this->demonstrateArtisan();
        $this->demonstrateMigrations();
    }
}

// Supporting classes for examples
interface UserRepositoryInterface {}
interface EmailServiceInterface {}
interface LoggerInterface {}

class UserRepository implements UserRepositoryInterface {}
class EmailService implements EmailServiceInterface {}
class Logger implements LoggerInterface {}

class UserService
{
    public function __construct(UserRepositoryInterface $repository, EmailServiceInterface $emailService, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }
}

class User extends EloquentModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'users';
    }
}

function csrf_token(): string
{
    return 'mock_csrf_token';
}

function class_basename($class): string
{
    return substr($class, strrpos($class, '\\') + 1);
}

// Laravel Best Practices
function printLaravelBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Laravel Framework Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Service Container:\n";
    echo "   • Use dependency injection\n";
    echo "   • Bind interfaces to implementations\n";
    echo "   • Use singletons for shared services\n";
    echo "   • Avoid service location pattern\n";
    echo "   • Leverage automatic resolution\n\n";
    
    echo "2. Eloquent ORM:\n";
    echo "   • Use relationships effectively\n";
    echo "   • Implement accessors and mutators\n";
    echo "   • Use eager loading to prevent N+1\n";
    echo "   • Implement proper validation\n";
    echo "   • Use mass assignment carefully\n\n";
    
    echo "3. Query Builder:\n";
    echo "   • Use parameterized queries\n";
    echo "   • Chain methods fluently\n";
    echo "   • Use scopes for complex queries\n";
    echo "   • Implement proper indexing\n";
    echo "   • Monitor query performance\n\n";
    
    echo "4. Blade Templates:\n";
    echo "   • Keep templates simple\n";
    echo "   • Use components and includes\n";
    echo "   • Implement proper escaping\n";
    echo "   • Use directives effectively\n";
    echo "   • Cache compiled views\n\n";
    
    echo "5. Routing:\n";
    echo "   • Use resource controllers\n";
    echo "   • Implement proper middleware\n";
    echo "   • Use route model binding\n";
    echo "   • Group related routes\n";
    echo "   • Implement rate limiting\n\n";
    
    echo "6. Security:\n";
    echo "   • Use CSRF protection\n";
    echo "   • Implement proper authentication\n";
    echo "   • Use authorization gates\n";
    echo "   • Validate all input\n";
    echo "   • Use HTTPS in production";
}

// Main execution
function runLaravelDemo(): void
{
    $examples = new LaravelExamples();
    $examples->runAllExamples();
    printLaravelBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runLaravelDemo();
}
?>
