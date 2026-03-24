<?php
/**
 * Symfony Framework Basics
 * 
 * This file demonstrates Symfony framework concepts,
 * components, bundles, and best practices.
 */

// Symfony Service Container (Dependency Injection)
class SymfonyServiceContainer
{
    private array $services = [];
    private array $parameters = [];
    private array $aliases = [];
    private array $instances = [];
    
    /**
     * Register a service definition
     */
    public function register(string $id, callable $definition): void
    {
        $this->services[$id] = $definition;
    }
    
    /**
     * Set a parameter
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }
    
    /**
     * Get a parameter
     */
    public function getParameter(string $name)
    {
        return $this->parameters[$name] ?? null;
    }
    
    /**
     * Create a service alias
     */
    public function setAlias(string $alias, string $id): void
    {
        $this->aliases[$alias] = $id;
    }
    
    /**
     * Get a service from the container
     */
    public function get(string $id)
    {
        // Check for aliases
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        
        // Return existing instance
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        
        // Create new instance
        if (isset($this->services[$id])) {
            $instance = $this->services[$id]($this);
            $this->instances[$id] = $instance;
            return $instance;
        }
        
        throw new \Exception("Service not found: $id");
    }
    
    /**
     * Check if a service is registered
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->aliases[$id]);
    }
}

// Symfony Bundle System
class Bundle
{
    protected string $name;
    protected string $namespace;
    protected string $path;
    
    public function __construct(string $name, string $namespace, string $path)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->path = $path;
    }
    
    /**
     * Boot the bundle
     */
    public function boot(): void
    {
        echo "Booting bundle: {$this->name}\n";
    }
    
    /**
     * Build the bundle
     */
    public function build(SymfonyServiceContainer $container): void
    {
        echo "Building bundle: {$this->name}\n";
        
        // Register bundle services
        $this->registerServices($container);
        
        // Register bundle configuration
        $this->registerConfiguration($container);
    }
    
    /**
     * Register bundle services
     */
    protected function registerServices(SymfonyServiceContainer $container): void
    {
        // Override in concrete bundles
    }
    
    /**
     * Register bundle configuration
     */
    protected function registerConfiguration(SymfonyServiceContainer $container): void
    {
        // Override in concrete bundles
    }
    
    /**
     * Get bundle name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get bundle namespace
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
    
    /**
     * Get bundle path
     */
    public function getPath(): string
    {
        return $this->path;
    }
}

// Symfony Framework Bundle
class FrameworkBundle extends Bundle
{
    public function __construct()
    {
        parent::__construct('FrameworkBundle', 'Symfony\\Bundle\\FrameworkBundle', '/vendor/symfony/framework-bundle');
    }
    
    protected function registerServices(SymfonyServiceContainer $container): void
    {
        // Register framework services
        $container->register('router', function($container) {
            return new Router($container);
        });
        
        $container->register('request_stack', function($container) {
            return new RequestStack();
        });
        
        $container->register('http_kernel', function($container) {
            return new HttpKernel($container);
        });
        
        $container->register('event_dispatcher', function($container) {
            return new EventDispatcher();
        });
        
        // Register parameters
        $container->setParameter('kernel.secret', 'mock_secret');
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.environment', 'dev');
    }
}

// Doctrine ORM Bundle
class DoctrineBundle extends Bundle
{
    public function __construct()
    {
        parent::__construct('DoctrineBundle', 'Doctrine\\Bundle\\DoctrineBundle', '/vendor/doctrine/doctrine-bundle');
    }
    
    protected function registerServices(SymfonyServiceContainer $container): void
    {
        // Register Doctrine services
        $container->register('doctrine.orm.entity_manager', function($container) {
            return new EntityManager($container);
        });
        
        $container->register('doctrine.dbal.connection', function($container) {
            return new DatabaseConnection($container);
        });
        
        // Set Doctrine parameters
        $container->setParameter('doctrine.dbal.driver', 'pdo_mysql');
        $container->setParameter('doctrine.dbal.host', 'localhost');
        $container->setParameter('doctrine.dbal.dbname', 'symfony');
        $container->setParameter('doctrine.dbal.user', 'root');
        $container->setParameter('doctrine.dbal.password', '');
    }
}

// Symfony Kernel
class Kernel
{
    private array $bundles = [];
    private SymfonyServiceContainer $container;
    private string $environment;
    private bool $debug;
    
    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;
        $this->container = new SymfonyServiceContainer();
        
        $this->initializeBundles();
        $this->initializeContainer();
    }
    
    /**
     * Initialize bundles
     */
    protected function initializeBundles(): void
    {
        $this->bundles = [
            new FrameworkBundle(),
            new DoctrineBundle()
        ];
        
        // Register bundles
        foreach ($this->bundles as $bundle) {
            $bundle->build($this->container);
        }
    }
    
    /**
     * Initialize container
     */
    protected function initializeContainer(): void
    {
        $this->container->setParameter('kernel.environment', $this->environment);
        $this->container->setParameter('kernel.debug', $this->debug);
        
        // Boot bundles
        foreach ($this->bundles as $bundle) {
            $bundle->boot();
        }
    }
    
    /**
     * Get the service container
     */
    public function getContainer(): SymfonyServiceContainer
    {
        return $this->container;
    }
    
    /**
     * Handle a request
     */
    public function handle(Request $request): Response
    {
        $httpKernel = $this->container->get('http_kernel');
        return $httpKernel->handle($request);
    }
    
    /**
     * Get bundles
     */
    public function getBundles(): array
    {
        return $this->bundles;
    }
}

// Symfony HTTP Kernel
class HttpKernel
{
    private SymfonyServiceContainer $container;
    private EventDispatcher $eventDispatcher;
    
    public function __construct(SymfonyServiceContainer $container)
    {
        $this->container = $container;
        $this->eventDispatcher = $container->get('event_dispatcher');
    }
    
    /**
     * Handle an HTTP request
     */
    public function handle(Request $request): Response
    {
        // Dispatch kernel.request event
        $event = new RequestEvent($request);
        $this->eventDispatcher->dispatch('kernel.request', $event);
        
        if ($event->hasResponse()) {
            return $event->getResponse();
        }
        
        // Resolve controller
        $controller = $this->resolveController($request);
        
        if ($controller === null) {
            return new Response('Not Found', 404);
        }
        
        // Execute controller
        $response = $this->executeController($controller, $request);
        
        // Dispatch kernel.response event
        $responseEvent = new ResponseEvent($request, $response);
        $this->eventDispatcher->dispatch('kernel.response', $responseEvent);
        
        return $responseEvent->getResponse();
    }
    
    /**
     * Resolve controller from request
     */
    private function resolveController(Request $request): ?callable
    {
        $router = $this->container->get('router');
        $route = $router->match($request->getPathInfo());
        
        if ($route === null) {
            return null;
        }
        
        return $route['_controller'];
    }
    
    /**
     * Execute controller
     */
    private function executeController(callable $controller, Request $request): Response
    {
        return $controller($request);
    }
}

// Symfony Router
class Router
{
    private SymfonyServiceContainer $container;
    private array $routes = [];
    
    public function __construct(SymfonyServiceContainer $container)
    {
        $this->container = $container;
        $this->loadRoutes();
    }
    
    /**
     * Load routes from configuration
     */
    private function loadRoutes(): void
    {
        $this->routes = [
            '/' => [
                '_controller' => function($request) {
                    return new Response('Welcome to Symfony!');
                }
            ],
            '/hello/{name}' => [
                '_controller' => function($request, $name) {
                    return new Response("Hello, $name!");
                }
            ],
            '/api/users' => [
                '_controller' => function($request) {
                    $users = [
                        ['id' => 1, 'name' => 'John Doe'],
                        ['id' => 2, 'name' => 'Jane Doe']
                    ];
                    return new JsonResponse($users);
                }
            ]
        ];
    }
    
    /**
     * Match a request path to a route
     */
    public function match(string $path): ?array
    {
        foreach ($this->routes as $pattern => $route) {
            if ($this->pathMatches($pattern, $path, $params)) {
                return array_merge($route, $params);
            }
        }
        
        return null;
    }
    
    /**
     * Check if path matches route pattern
     */
    private function pathMatches(string $pattern, string $path, array &$params): bool
    {
        if ($pattern === $path) {
            return true;
        }
        
        $patternParts = explode('/', $pattern);
        $pathParts = explode('/', $path);
        
        if (count($patternParts) !== count($pathParts)) {
            return false;
        }
        
        foreach ($patternParts as $index => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $paramName = substr($part, 1, -1);
                $params[$paramName] = $pathParts[$index];
            } elseif ($part !== $pathParts[$index]) {
                return false;
            }
        }
        
        return true;
    }
}

// Symfony Event Dispatcher
class EventDispatcher
{
    private array $listeners = [];
    
    /**
     * Add an event listener
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort by priority (higher first)
        usort($this->listeners[$eventName], function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }
    
    /**
     * Dispatch an event
     */
    public function dispatch(string $eventName, Event $event): Event
    {
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listenerInfo) {
                $listenerInfo['listener']($event, $eventName, $this);
                
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
        
        return $event;
    }
}

// Symfony Event System
class Event
{
    private bool $propagationStopped = false;
    
    /**
     * Stop event propagation
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
    
    /**
     * Check if propagation is stopped
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}

class RequestEvent extends Event
{
    private Request $request;
    private ?Response $response = null;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    public function getResponse(): ?Response
    {
        return $this->response;
    }
    
    public function setResponse(Response $response): void
    {
        $this->response = $response;
        $this->stopPropagation();
    }
    
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
}

class ResponseEvent extends Event
{
    private Request $request;
    private Response $response;
    
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    public function getResponse(): Response
    {
        return $this->response;
    }
    
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}

// Symfony Request Object
class Request
{
    private array $query;
    private array $request;
    private array $attributes;
    private array $cookies;
    private array $files;
    private array $server;
    private string $method;
    private string $uri;
    
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = []
    ) {
        $this->query = $query;
        $this->request = $request;
        $this->attributes = $attributes;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->method = $server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $server['REQUEST_URI'] ?? '/';
    }
    
    /**
     * Create request from globals
     */
    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
    }
    
    /**
     * Get query parameter
     */
    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }
    
    /**
     * Get request parameter
     */
    public function request(string $key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }
    
    /**
     * Get attribute
     */
    public function get(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
    
    /**
     * Set attribute
     */
    public function set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * Get request URI
     */
    public function getPathInfo(): string
    {
        return parse_url($this->uri, PHP_URL_PATH) ?: '/';
    }
    
    /**
     * Get all parameters
     */
    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }
}

// Symfony Response Object
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
     * Create JSON response
     */
    public static function createJson($data, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json';
        
        return new self(json_encode($data), $status, $headers);
    }
    
    /**
     * Create redirect response
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
    public function getStatusCode(): int
    {
        return $this->status;
    }
    
    /**
     * Set response content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    /**
     * Set response status
     */
    public function setStatusCode(int $status): void
    {
        $this->status = $status;
    }
}

// Symfony JsonResponse
class JsonResponse extends Response
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        $headers['Content-Type'] = 'application/json';
        
        if ($json) {
            $content = $data;
        } else {
            $content = json_encode($data);
        }
        
        parent::__construct($content, $status, $headers);
    }
}

// Symfony Twig Template Engine
class TwigEngine
{
    private array $templates = [];
    private array $globals = [];
    
    /**
     * Add a template
     */
    public function addTemplate(string $name, string $content): void
    {
        $this->templates[$name] = $content;
    }
    
    /**
     * Set global variable
     */
    public function addGlobal(string $name, $value): void
    {
        $this->globals[$name] = $value;
    }
    
    /**
     * Render a template
     */
    public function render(string $template, array $context = []): string
    {
        if (!isset($this->templates[$template])) {
            throw new \Exception("Template not found: $template");
        }
        
        $content = $this->templates[$template];
        
        // Merge globals with context
        $context = array_merge($this->globals, $context);
        
        // Process Twig-like syntax
        $content = $this->processVariables($content, $context);
        $content = $this->processConditionals($content, $context);
        $content = $this->processLoops($content, $context);
        $content = $this->processIncludes($content, $context);
        
        return $content;
    }
    
    /**
     * Process variables {{ variable }}
     */
    private function processVariables(string $content, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                $content = str_replace("{{ $key }}", $value, $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Process conditionals {% if %} ... {% endif %}
     */
    private function processConditionals(string $content, array $context): string
    {
        // Simple if processing
        preg_match_all('/{% if (.*?) %}(.*?){% endif %}/s', $content, $matches);
        
        foreach ($matches[0] as $index => $match) {
            $condition = $matches[1][$index];
            $body = $matches[2][$index];
            
            if ($this->evaluateCondition($condition, $context)) {
                $content = str_replace($match, $body, $content);
            } else {
                $content = str_replace($match, '', $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Process loops {% for %} ... {% endfor %}
     */
    private function processLoops(string $content, array $context): string
    {
        // Simple for processing
        preg_match_all('/{% for (.*?) in (.*?) %}(.*?){% endfor %}/s', $content, $matches);
        
        foreach ($matches[0] as $index => $match) {
            $itemVar = $matches[1][$index];
            $arrayVar = $matches[2][$index];
            $body = $matches[3][$index];
            
            $array = $this->getVariableValue($arrayVar, $context);
            
            if (is_array($array)) {
                $loopContent = '';
                foreach ($array as $item) {
                    $loopContext = $context;
                    $loopContext[$itemVar] = $item;
                    $loopContent .= $this->processVariables($body, $loopContext);
                }
                
                $content = str_replace($match, $loopContent, $content);
            } else {
                $content = str_replace($match, '', $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Process includes {% include %}
     */
    private function processIncludes(string $content, array $context): string
    {
        // Simple include processing
        preg_match_all('/{% include (.*?) %}/', $content, $matches);
        
        foreach ($matches[0] as $index => $match) {
            $template = $matches[1][$index];
            
            if (isset($this->templates[$template])) {
                $includedContent = $this->render($template, $context);
                $content = str_replace($match, $includedContent, $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Evaluate a condition
     */
    private function evaluateCondition(string $condition, array $context): bool
    {
        // Simple condition evaluation
        if (preg_match('/(\w+)\s*==\s*(.+)/', $condition, $matches)) {
            $var = $matches[1];
            $value = $matches[2];
            
            $actualValue = $this->getVariableValue($var, $context);
            $expectedValue = trim($value, '"\'');
            
            return $actualValue == $expectedValue;
        }
        
        return false;
    }
    
    /**
     * Get variable value from context
     */
    private function getVariableValue(string $var, array $context)
    {
        if (strpos($var, '.') !== false) {
            $parts = explode('.', $var);
            $value = $context;
            
            foreach ($parts as $part) {
                if (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return null;
                }
            }
            
            return $value;
        }
        
        return $context[$var] ?? null;
    }
}

// Symfony Console Commands
class Command
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
     * Execute the command
     */
    public function execute(array $arguments = [], array $options = []): string
    {
        return "Command '{$this->name}' executed successfully";
    }
    
    /**
     * Get command name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get command description
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * Get command help
     */
    public function getHelp(): string
    {
        $help = "Command: {$this->name}\n";
        $help .= "Description: {$this->description}\n\n";
        
        if (!empty($this->arguments)) {
            $help .= "Arguments:\n";
            foreach ($this->arguments as $name => $arg) {
                $help .= "  {$name}: {$arg['description']} " . ($arg['required'] ? '(required)' : '(optional)') . "\n";
            }
            $help .= "\n";
        }
        
        if (!empty($this->options)) {
            $help .= "Options:\n";
            foreach ($this->options as $name => $option) {
                $help .= "  --{$name}: {$option['description']} " . ($option['required'] ? '(required)' : '(optional)') . "\n";
            }
        }
        
        return $help;
    }
}

// Symfony Examples
class SymfonyExamples
{
    private Kernel $kernel;
    private TwigEngine $twig;
    
    public function __construct()
    {
        $this->kernel = new Kernel('dev', true);
        $this->twig = new TwigEngine();
    }
    
    public function demonstrateServiceContainer(): void
    {
        echo "Symfony Service Container Example\n";
        echo str_repeat("-", 40) . "\n";
        
        $container = $this->kernel->getContainer();
        
        // Set parameters
        $container->setParameter('database.host', 'localhost');
        $container->setParameter('database.name', 'symfony');
        
        echo "Database host: " . $container->getParameter('database.host') . "\n";
        echo "Database name: " . $container->getParameter('database.name') . "\n";
        
        // Get services
        $router = $container->get('router');
        $httpKernel = $container->get('http_kernel');
        $eventDispatcher = $container->get('event_dispatcher');
        
        echo "Router: " . get_class($router) . "\n";
        echo "HTTP Kernel: " . get_class($httpKernel) . "\n";
        echo "Event Dispatcher: " . get_class($eventDispatcher) . "\n";
        
        // Check service existence
        echo "Has router: " . ($container->has('router') ? 'true' : 'false') . "\n";
        echo "Has unknown_service: " . ($container->has('unknown_service') ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateBundles(): void
    {
        echo "\nSymfony Bundle System Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $bundles = $this->kernel->getBundles();
        
        foreach ($bundles as $bundle) {
            echo "Bundle: " . $bundle->getName() . "\n";
            echo "  Namespace: " . $bundle->getNamespace() . "\n";
            echo "  Path: " . $bundle->getPath() . "\n\n";
        }
        
        // Show registered services
        $container = $this->kernel->getContainer();
        
        echo "FrameworkBundle services:\n";
        echo "  - router\n";
        echo "  - request_stack\n";
        echo "  - http_kernel\n";
        echo "  - event_dispatcher\n\n";
        
        echo "DoctrineBundle services:\n";
        echo "  - doctrine.orm.entity_manager\n";
        echo "  - doctrine.dbal.connection\n\n";
        
        echo "Doctrine parameters:\n";
        echo "  - driver: " . $container->getParameter('doctrine.dbal.driver') . "\n";
        echo "  - host: " . $container->getParameter('doctrine.dbal.host') . "\n";
        echo "  - database: " . $container->getParameter('doctrine.dbal.dbname') . "\n";
    }
    
    public function demonstrateEventSystem(): void
    {
        echo "\nSymfony Event System Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $container = $this->kernel->getContainer();
        $eventDispatcher = $container->get('event_dispatcher');
        
        // Add listeners
        $eventDispatcher->addListener('kernel.request', function($event) {
            echo "Kernel request event triggered\n";
        }, 100);
        
        $eventDispatcher->addListener('kernel.request', function($event) {
            echo "Second kernel request listener\n";
        }, 50);
        
        $eventDispatcher->addListener('kernel.response', function($event) {
            echo "Kernel response event triggered\n";
        });
        
        // Dispatch events
        $request = Request::createFromGlobals();
        $requestEvent = new RequestEvent($request);
        
        echo "Dispatching kernel.request event:\n";
        $eventDispatcher->dispatch('kernel.request', $requestEvent);
        
        $response = new Response('Hello World');
        $responseEvent = new ResponseEvent($request, $response);
        
        echo "\nDispatching kernel.response event:\n";
        $eventDispatcher->dispatch('kernel.response', $responseEvent);
    }
    
    public function demonstrateHttpKernel(): void
    {
        echo "\nSymfony HTTP Kernel Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $request = new Request();
        $request->set('name', 'Symfony');
        
        echo "Request method: " . $request->getMethod() . "\n";
        echo "Request path: " . $request->getPathInfo() . "\n";
        
        // Handle request
        $response = $this->kernel->handle($request);
        
        echo "Response status: " . $response->getStatusCode() . "\n";
        echo "Response content: " . $response->getContent() . "\n";
        
        // Test different routes
        $requests = [
            new Request([], [], [], [], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/hello/World']),
            new Request([], [], [], [], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/users'])
        ];
        
        foreach ($requests as $req) {
            echo "\nHandling: " . $req->getPathInfo() . "\n";
            $resp = $this->kernel->handle($req);
            echo "Response: " . $resp->getContent() . "\n";
        }
    }
    
    public function demonstrateTwig(): void
    {
        echo "\nSymfony Twig Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Add templates
        $this->twig->addTemplate('base.html.twig', <<<TWIG
            <!DOCTYPE html>
            <html>
            <head>
                <title>{{ title }}</title>
            </head>
            <body>
                <header>
                    <h1>{{ site_name }}</h1>
                </header>
                <main>
                    {% block content %}{% endblock %}
                </main>
            </body>
            </html>
            TWIG);
        
        $this->twig->addTemplate('hello.html.twig', <<<TWIG
            {% extends "base.html.twig" %}
            
            {% block content %}
                <h2>Hello, {{ name }}!</h2>
                
                {% if user.admin %}
                    <p>Welcome, administrator!</p>
                {% else %}
                    <p>Welcome, regular user!</p>
                {% endif %}
                
                <ul>
                {% for item in items %}
                    <li>{{ item }}</li>
                {% endfor %}
                </ul>
            {% endblock %}
            TWIG);
        
        // Set globals
        $this->twig->addGlobal('site_name', 'Symfony Demo');
        
        // Render template
        $context = [
            'title' => 'Hello Page',
            'name' => 'John Doe',
            'user' => ['admin' => true],
            'items' => ['Apple', 'Banana', 'Cherry']
        ];
        
        $rendered = $this->twig->render('hello.html.twig', $context);
        
        echo "Rendered template:\n";
        echo $rendered . "\n";
    }
    
    public function demonstrateConsole(): void
    {
        echo "\nSymfony Console Commands Example\n";
        echo str_repeat("-", 40) . "\n";
        
        // Create commands
        $cacheClear = new class extends Command {
            public function __construct()
            {
                parent::__construct('cache:clear', 'Clear the application cache');
            }
            
            public function execute(array $arguments = [], array $options = []): string
            {
                return 'Cache cleared successfully';
            }
        };
        
        $doctrineMigrate = new class extends Command {
            public function __construct()
            {
                parent::__construct('doctrine:migrate', 'Run database migrations');
                $this->addArgument('version', 'Target migration version', false);
                $this->addOption('force', 'Force migration execution', false);
            }
            
            public function execute(array $arguments = [], array $options = []): string
            {
                $version = $arguments['version'] ?? 'latest';
                $force = isset($options['force']) && $options['force'];
                
                return "Migration executed to version: $version" . ($force ? ' (forced)' : '');
            }
        };
        
        $serverStart = new class extends Command {
            public function __construct()
            {
                parent::__construct('server:start', 'Start the development server');
                $this->addOption('port', 'Server port', false);
                $this->addOption('host', 'Server host', false);
            }
            
            public function execute(array $arguments = [], array $options = []): string
            {
                $port = $options['port'] ?? 8000;
                $host = $options['host'] ?? '127.0.0.1';
                
                return "Server started on http://$host:$port";
            }
        };
        
        // Show command help
        echo $cacheClear->getHelp() . "\n";
        echo $doctrineMigrate->getHelp() . "\n";
        echo $serverStart->getHelp() . "\n";
        
        // Execute commands
        echo "Executing cache:clear:\n";
        echo $cacheClear->execute() . "\n\n";
        
        echo "Executing doctrine:migrate with version:\n";
        echo $doctrineMigrate->execute(['version' => '20230101000000']) . "\n\n";
        
        echo "Executing server:start with options:\n";
        echo $serverStart->execute([], ['port' => 8080, 'host' => '0.0.0.0']) . "\n";
    }
    
    public function demonstrateDependencyInjection(): void
    {
        echo "\nSymfony Dependency Injection Example\n";
        echo str_repeat("-", 45) . "\n";
        
        $container = new SymfonyServiceContainer();
        
        // Register services with dependencies
        $container->register('logger', function($container) {
            return new Logger();
        });
        
        $container->register('database', function($container) {
            return new Database($container->getParameter('database.dsn'));
        });
        
        $container->register('user_service', function($container) {
            return new UserService(
                $container->get('database'),
                $container->get('logger')
            );
        });
        
        // Set parameters
        $container->setParameter('database.dsn', 'mysql:host=localhost;dbname=symfony');
        
        // Resolve services
        $logger = $container->get('logger');
        $database = $container->get('database');
        $userService = $container->get('user_service');
        
        echo "Logger resolved: " . get_class($logger) . "\n";
        echo "Database resolved: " . get_class($database) . "\n";
        echo "UserService resolved: " . get_class($userService) . "\n";
        
        // Show dependency graph
        echo "\nDependency Graph:\n";
        echo "UserService -> Database, Logger\n";
        echo "Database -> database.dsn parameter\n";
        echo "Logger -> no dependencies\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Symfony Framework Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateServiceContainer();
        $this->demonstrateBundles();
        $this->demonstrateEventSystem();
        $this->demonstrateHttpKernel();
        $this->demonstrateTwig();
        $this->demonstrateConsole();
        $this->demonstrateDependencyInjection();
    }
}

// Supporting classes for examples
class Logger {}
class Database {}
class UserService
{
    public function __construct(Database $database, Logger $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }
}

class RequestStack {}
class EntityManager {}
class DatabaseConnection {}

// Symfony Best Practices
function printSymfonyBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Symfony Framework Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Service Container:\n";
    echo "   • Use dependency injection properly\n";
    echo "   • Prefer constructor injection\n";
    echo "   • Use interfaces for services\n";
    echo "   • Avoid service locator pattern\n";
    echo "   • Use autowiring when possible\n\n";
    
    echo "2. Bundle System:\n";
    echo "   • Create reusable bundles\n";
    echo "   • Follow bundle conventions\n";
    echo "   • Use proper dependency injection\n";
    echo "   • Implement proper configuration\n";
    echo "   • Keep bundles focused\n\n";
    
    echo "3. Event System:\n";
    echo "   • Use events for loose coupling\n";
    echo "   • Create custom event classes\n";
    echo "   • Use event subscribers\n";
    echo "   • Keep event handlers simple\n";
    echo "   • Avoid heavy processing in events\n\n";
    
    echo "4. HTTP Kernel:\n";
    echo "   • Use proper routing\n";
    echo "   • Implement controllers as services\n";
    echo "   • Use proper HTTP status codes\n";
    echo "   • Implement proper error handling\n";
    echo "   • Use middleware effectively\n\n";
    
    echo "5. Twig Templates:\n";
    echo "   • Keep templates simple\n";
    echo "   • Use template inheritance\n";
    echo "   • Create custom Twig functions\n";
    echo "   • Use proper escaping\n";
    echo "   • Cache compiled templates\n\n";
    
    echo "6. Console Commands:\n";
    echo "   • Follow command conventions\n";
    echo "   • Use proper argument validation\n";
    echo "   • Implement proper error handling\n";
    echo "   • Use services in commands\n";
    echo "   • Provide helpful output";
}

// Main execution
function runSymfonyDemo(): void
{
    $examples = new SymfonyExamples();
    $examples->runAllExamples();
    printSymfonyBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSymfonyDemo();
}
?>
