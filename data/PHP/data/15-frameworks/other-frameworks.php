<?php
/**
 * Other PHP Frameworks
 * 
 * This file demonstrates CodeIgniter, Slim, Phalcon, Yii,
 * and other popular PHP frameworks and their features.
 */

// CodeIgniter Framework Simulation
class CodeIgniterFramework
{
    private array $config = [];
    private array $routes = [];
    private array $libraries = [];
    private array $helpers = [];
    
    public function __construct()
    {
        $this->initializeConfig();
        $this->initializeRoutes();
        $this->initializeLibraries();
        $this->initializeHelpers();
    }
    
    /**
     * Initialize CodeIgniter configuration
     */
    private function initializeConfig(): void
    {
        $this->config = [
            'base_url' => 'http://localhost/codeigniter',
            'index_page' => 'index.php',
            'uri_protocol' => 'REQUEST_URI',
            'url_suffix' => '',
            'language' => 'english',
            'charset' => 'UTF-8',
            'enable_hooks' => true,
            'subclass_prefix' => 'MY_',
            'composer_autoload' => false,
            'permitted_uri_chars' => 'a-z 0-9~%.:_-',
            'allow_get_array' => true,
            'enable_query_strings' => true,
            'controller_trigger' => 'c',
            'function_trigger' => 'm',
            'directory_trigger' => 'd',
            'log_threshold' => 1,
            'log_path' => '/logs/',
            'log_file_extension' => '',
            'log_date_format' => 'Y-m-d H:i:s',
            'error_views_path' => 'errors/',
            'cache_path' => '/cache/',
            'cache_query_string' => false,
            'cache_encryption' => false,
            'sess_driver' => 'files',
            'sess_cookie_name' => 'ci_session',
            'sess_expiration' => 7200,
            'sess_save_path' => NULL,
            'sess_match_ip' => false,
            'sess_time_to_update' => 300,
            'sess_regenerate_destroy' => false,
            'cookie_prefix' => 'ci_',
            'cookie_domain' => '',
            'cookie_path' => '/',
            'cookie_secure' => false,
            'cookie_httponly' => false,
            'standardize_newlines' => false,
            'global_xss_filtering' => false,
            'csrf_protection' => false,
            'csrf_token_name' => 'csrf_test_name',
            'csrf_cookie_name' => 'csrf_cookie_name',
            'csrf_expire' => 7200,
            'csrf_regenerate' => true,
            'csrf_exclude_uris' => [],
            'proxy_ips' => '',
            'allow_auto_ip' => true,
            'compress_output' => false,
            'time_reference' => 'local',
            'rewrite_short_tags' => false,
            'reverse_proxy' => false,
            'proxy_servers' => '',
            'image_library' => 'gd2',
            'image_library_path' => '/usr/local/bin/',
            'image_library_driver' => 'gd2',
            'image_library_rotation' => 'auto',
            'image_library_rand_seed' => '',
            'image_library_sharpen' => 25,
            'image_library_channel' => 'auto',
            'image_library_nice_sharpen' => 'auto',
            'image_library_compression' => 85,
            'image_library_overlay_watermark' => '',
            'image_library_overlay_watermark_x' => 5,
            'image_library_overlay_watermark_y' => 5,
            'image_library_overlay_watermark_width' => 100,
            'image_library_overlay_watermark_height' => 50,
            'image_library_overlay_watermark_opacity' => 50,
            'image_library_overlay_watermark_y_align' => 'bottom',
            'image_library_overlay_watermark_x_align' => 'right',
            'image_library_master_dim' => 'auto',
            'image_library_auto_orient' => false,
            'image_library_rotate' => '',
            'image_library_resize' => '',
            'image_library_crop' => '',
            'image_library_crop_x' => '',
            'image_library_crop_y' => '',
            'image_library_quality' => '90',
            'image_library_preserve_aspect' => true,
            'image_library_master_dim' => '',
            'image_library_width' => '',
            'image_library_height' => '',
            'image_library_force_resize' => false,
            'image_library_force_master_dim' => false,
            'image_library_master_ratio' => 1,
            'image_library_strip_exif' => true,
            'image_library_type_filter' => '',
            'image_library_size_filter' => '',
            'image_library_colorspace' => '',
            'image_library_default_dtype' => 'auto',
            'image_library_order_by' => '',
            'image_library_random' => false,
            'image_library_use_exif_orientation' => true,
            'image_library_reflection' => false,
            'image_library_reflection_source' => '',
            'image_library_reflection_pt' => '',
            'image_library_reflection_distance' => 10,
            'image_library_reflection_opacity' => 50,
            'image_library_reflection_angle' => '',
            'image_library_reflection_bg_color' => '#ffffff',
            'image_library_watermark' => '',
            'image_library_watermark_vrt_alignment' => 'bottom',
            'image_library_watermark_hor_alignment' => 'right',
            'image_library_watermark_margin_vrt' => 5,
            'image_library_watermark_margin_hor' => 5,
            'image_library_watermark_padding' => 0,
            'image_library_watermark_x_axis' => 0,
            'image_library_watermark_y_axis' => 0,
            'image_library_watermark_no_text' => false,
            'image_library_watermark_font' => 'text-liberation-sans',
            'image_library_watermark_size' => 17,
            'image_library_watermark_color' => '#8B8B8B',
            'image_library_watermark_angle' => 0,
            'image_library_watermark_show_drop_shadow' => false,
            'image_library_watermark_shadow_color' => '#000000',
            'image_library_watermark_shadow_distance' => 3,
            'image_library_watermark_shadow_blur' => 4,
            'image_library_text_watermark' => '',
            'image_library_text_watermark_size' => 17,
            'image_library_text_watermark_color' => '#8B8B8B',
            'image_library_text_watermark_angle' => 0,
            'image_library_text_watermark_vrt_alignment' => 'bottom',
            'image_library_text_watermark_hor_alignment' => 'center',
            'image_library_text_watermark_padding' => 0,
            'image_library_text_watermark_x_align' => 'center',
            'image_library_text_watermark_y_align' => 'middle',
            'image_library_text_watermark_show_drop_shadow' => false,
            'image_library_text_watermark_drop_shadow_color' => '#000000',
            'image_library_text_watermark_drop_shadow_distance' => 3,
            'image_library_text_watermark_drop_shadow_blur' => 4
        ];
    }
    
    /**
     * Initialize CodeIgniter routes
     */
    private function initializeRoutes(): void
    {
        $this->routes = [
            'default_controller' => 'Welcome',
            'default_method' => 'index',
            'translate_uri_dashes' => false,
            'enable_query_strings' => false,
            'routes' => [
                'about' => 'pages/about',
                'contact' => 'pages/contact',
                'blog(/(.*)' => 'blog$1',
                'api/users' => 'api/users',
                'admin' => 'admin/dashboard'
            ],
            'method' => '_REQUEST',
            'auto_route' => true
        ];
    }
    
    /**
     * Initialize CodeIgniter libraries
     */
    private function initializeLibraries(): void
    {
        $this->libraries = [
            'database' => 'Database Library',
            'session' => 'Session Library',
            'email' => 'Email Library',
            'form_validation' => 'Form Validation Library',
            'pagination' => 'Pagination Library',
            'upload' => 'Upload Library',
            'image_lib' => 'Image Manipulation Library',
            'cart' => 'Shopping Cart Library',
            'xmlrpc' => 'XML-RPC Library',
            'unit_test' => 'Unit Testing Library',
            'javascript' => 'JavaScript Library',
            'table' => 'HTML Table Library',
            'parser' => 'Template Parser Library',
            'encryption' => 'Encryption Library',
            'ftp' => 'FTP Library',
            'migration' => 'Database Migration Library',
            'zip' => 'Zip Compression Library'
        ];
    }
    
    /**
     * Initialize CodeIgniter helpers
     */
    private function initializeHelpers(): void
    {
        $this->helpers = [
            'array' => 'Array Helper',
            'captcha' => 'CAPTCHA Helper',
            'cookie' => 'Cookie Helper',
            'date' => 'Date Helper',
            'directory' => 'Directory Helper',
            'download' => 'Download Helper',
            'email' => 'Email Helper',
            'file' => 'File Helper',
            'form' => 'Form Helper',
            'html' => 'HTML Helper',
            'inflector' => 'Inflector Helper',
            'language' => 'Language Helper',
            'number' => 'Number Helper',
            'path' => 'Path Helper',
            'security' => 'Security Helper',
            'smiley' => 'Smiley Helper',
            'string' => 'String Helper',
            'text' => 'Text Helper',
            'typography' => 'Typography Helper',
            'url' => 'URL Helper',
            'xml' => 'XML Helper'
        ];
    }
    
    /**
     * Load a library
     */
    public function library(string $library): bool
    {
        if (isset($this->libraries[$library])) {
            echo "Loading library: {$this->libraries[$library]}\n";
            return true;
        }
        
        echo "Library not found: $library\n";
        return false;
    }
    
    /**
     * Load a helper
     */
    public function helper(string $helper): bool
    {
        if (isset($this->helpers[$helper])) {
            echo "Loading helper: {$this->helpers[$helper]}\n";
            return true;
        }
        
        echo "Helper not found: $helper\n";
        return false;
    }
    
    /**
     * Get configuration value
     */
    public function config(string $item, $default = null)
    {
        return $this->config[$item] ?? $default;
    }
    
    /**
     * Get routes configuration
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    /**
     * Create a controller
     */
    public function createController(string $name): string
    {
        $className = ucfirst($name) . '_controller';
        
        $controllerCode = "<?php\n";
        $controllerCode .= "defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
        $controllerCode .= "class $className extends CI_Controller {\n\n";
        $controllerCode .= "    public function index() {\n";
        $controllerCode .= "        echo 'Welcome to $className';\n";
        $controllerCode .= "    }\n\n";
        $controllerCode .= "    public function method(\$param = '') {\n";
        $controllerCode .= "        echo 'Method called with parameter: ' . \$param;\n";
        $controllerCode .= "    }\n";
        $controllerCode .= "}\n";
        
        return $controllerCode;
    }
    
    /**
     * Create a model
     */
    public function createModel(string $name): string
    {
        $className = ucfirst($name) . '_model';
        $tableName = strtolower($name);
        
        $modelCode = "<?php\n";
        $modelCode .= "defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
        $modelCode .= "class $className extends CI_Model {\n\n";
        $modelCode .= "    protected \$table = '$tableName';\n\n";
        $modelCode .= "    public function get_all() {\n";
        $modelCode .= "        \$query = \$this->db->get(\$this->table);\n";
        $modelCode .= "        return \$query->result();\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function get(\$id) {\n";
        $modelCode .= "        return \$this->db->get_where(\$this->table, ['id' => \$id])->row();\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function insert(\$data) {\n";
        $modelCode .= "        return \$this->db->insert(\$this->table, \$data);\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function update(\$id, \$data) {\n";
        $modelCode .= "        return \$this->db->update(\$this->table, \$data, ['id' => \$id]);\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function delete(\$id) {\n";
        $modelCode .= "        return \$this->db->delete(\$this->table, ['id' => \$id]);\n";
        $modelCode .= "    }\n";
        $modelCode .= "}\n";
        
        return $modelCode;
    }
    
    /**
     * Show framework information
     */
    public function showInfo(): void
    {
        echo "CodeIgniter Framework Information\n";
        echo str_repeat("-", 35) . "\n";
        echo "Base URL: " . $this->config['base_url'] . "\n";
        echo "Index Page: " . $this->config['index_page'] . "\n";
        echo "Language: " . $this->config['language'] . "\n";
        echo "Charset: " . $this->config['charset'] . "\n";
        echo "Hooks Enabled: " . ($this->config['enable_hooks'] ? 'Yes' : 'No') . "\n";
        
        echo "\nAvailable Libraries (" . count($this->libraries) . "):\n";
        foreach ($this->libraries as $name => $description) {
            echo "  - $name: $description\n";
        }
        
        echo "\nAvailable Helpers (" . count($this->helpers) . "):\n";
        foreach ($this->helpers as $name => $description) {
            echo "  - $name: $description\n";
        }
    }
}

// Slim Framework Simulation
class SlimFramework
{
    private array $container;
    private array $middleware = [];
    private array $routes = [];
    private array $settings;
    
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge($this->getDefaultSettings(), $settings);
        $this->container = new Container();
        $this->initializeContainer();
    }
    
    /**
     * Get default settings
     */
    private function getDefaultSettings(): array
    {
        return [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => true,
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
            'httpMethodOverride' => false,
            'cookies' => ['encrypt' => false, 'secure' => false, 'httponly' => false],
            'encryption' => ['key' => 'your-secret-key'],
            'logger' => [
                'name' => 'slim-app',
                'path' => 'logs/app.log',
                'level' => 0,
                'message_format' => '[{datetime}] {channel}.{level_name}: {message}',
                'timezone' => 'UTC'
            ]
        ];
    }
    
    /**
     * Initialize dependency container
     */
    private function initializeContainer(): void
    {
        // Register request
        $this->container['request'] = function($c) {
            return ServerRequest::fromGlobals();
        };
        
        // Register response
        $this->container['response'] = function($c) {
            return new Response();
        };
        
        // Register router
        $this->container['router'] = function($c) {
            return new Router();
        };
        
        // Register error handler
        $this->container['errorHandler'] = function($c) {
            return new ErrorHandler();
        };
        
        // Register view
        $this->container['view'] = function($c) {
            return new View();
        };
    }
    
    /**
     * Add GET route
     */
    public function get(string $pattern, callable $handler): Route
    {
        return $this->map(['GET'], $pattern, $handler);
    }
    
    /**
     * Add POST route
     */
    public function post(string $pattern, callable $handler): Route
    {
        return $this->map(['POST'], $pattern, $handler);
    }
    
    /**
     * Add PUT route
     */
    public function put(string $pattern, callable $handler): Route
    {
        return $this->map(['PUT'], $pattern, $handler);
    }
    
    /**
     * Add DELETE route
     */
    public function delete(string $pattern, callable $handler): Route
    {
        return $this->map(['DELETE'], $pattern, $handler);
    }
    
    /**
     * Add route for multiple HTTP methods
     */
    public function map(array $methods, string $pattern, callable $handler): Route
    {
        $route = new Route($methods, $pattern, $handler);
        $this->routes[] = $route;
        
        return $route;
    }
    
    /**
     * Add middleware
     */
    public function add(callable $middleware): self
    {
        $this->middleware[] = $middleware;
        
        return $this;
    }
    
    /**
     * Run the application
     */
    public function run(): void
    {
        // Get request
        $request = $this->container['request'];
        
        // Apply middleware
        foreach ($this->middleware as $middleware) {
            $response = $middleware($request, $this);
            
            if ($response instanceof Response) {
                $this->respond($response);
                return;
            }
        }
        
        // Route request
        $response = $this->route($request);
        
        // Send response
        $this->respond($response);
    }
    
    /**
     * Route the request
     */
    private function route(ServerRequest $request): Response
    {
        $router = $this->container['router'];
        $route = $router->dispatch($request);
        
        if ($route === null) {
            return $this->container['response']->withStatus(404)->withJson(['error' => 'Not Found']);
        }
        
        return $route($request);
    }
    
    /**
     * Send response
     */
    private function respond(Response $response): void
    {
        if ($response->hasHeader('Content-Type') === false) {
            $response = $response->withHeader('Content-Type', 'text/html');
        }
        
        if ($response->hasHeader('Content-Length') === false) {
            $response = $response->withHeader('Content-Length', (string) strlen($response->getBody()));
        }
        
        $this->sendHeaders($response->getHeaders());
        $this->sendStatus($response->getStatusCode());
        echo $response->getBody();
    }
    
    /**
     * Send HTTP headers
     */
    private function sendHeaders(array $headers): void
    {
        foreach ($headers as $name => $value) {
            header("$name: $value");
        }
    }
    
    /**
     * Send HTTP status
     */
    private function sendStatus(int $status): void
    {
        http_response_code($status);
    }
    
    /**
     * Get container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
    
    /**
     * Get settings
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
    
    /**
     * Create a Slim app
     */
    public static function create(array $settings = []): self
    {
        return new self($settings);
    }
}

// Phalcon Framework Simulation
class PhalconFramework
{
    private array $services = [];
    private array $modules = [];
    private array $events = [];
    private array $config = [];
    
    public function __construct()
    {
        $this->initializeServices();
        $this->initializeConfig();
    }
    
    /**
     * Initialize Phalcon services
     */
    private function initializeServices(): void
    {
        // Register services
        $this->services['router'] = new Router();
        $this->services['dispatcher'] = new Dispatcher();
        $this->services['view'] = new View();
        $this->services['request'] = new Request();
        $this->services['response'] = new Response();
        $this->services['flash'] = new Flash();
        $this->services['security'] = new Security();
        $this->services['cookies'] = new Cookies();
        $this->services['session'] = new Session();
        $this->services['cache'] = new Cache();
        $this->services['crypt'] = new Crypt();
        $this->services['tag'] = new Tag();
        $this->services['escaper'] => new Escaper();
        $this->services['logger'] = new Logger();
        $this->services['annotations'] = new Annotations();
        $this->services['assets'] = new Assets();
        $this->services['url'] = new Url();
        $this->services['di'] = new DI();
    }
    
    /**
     * Initialize Phalcon configuration
     */
    private function initializeConfig(): void
    {
        $this->config = [
            'database' => [
                'adapter' => 'Mysql',
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname' => 'phalcon',
                'charset' => 'utf8'
            ],
            'application' => [
                'baseUri' => '/',
                'controllersDir' => 'app/controllers/',
                'modelsDir' => 'app/models/',
                'viewsDir' => 'app/views/',
                'pluginsDir' => 'app/plugins/',
                'libraryDir' => 'app/library/',
                'formsDir' => 'app/forms/',
                'resourcesDir' => 'app/resources/',
                'voltDir' => 'app/volt/',
                'voltCacheDir' => 'app/cache/volt/'
            ],
            'metadata' => [
                'modelsDir' => 'app/models/metadata/',
                'modelsMetadata' => 'annotations'
            ],
            'response' => [
                'headers' => [
                    'Content-Type: text/html; charset=UTF-8',
                    'X-Frame-Options: SAMEORIGIN',
                    'X-XSS-Protection: 1; mode=block',
                    'X-Content-Type-Options: nosniff'
                ]
            ],
            'session' => [
                'files' => true,
                'uniqueId' => null,
                'lifetime' => 1440,
                'cookie' => [
                    'name' => 'PHPSESSID',
                    'path' => '/',
                    'domain' => null,
                    'secure' => false,
                    'httponly' => true
                ]
            ],
            'cache' => [
                'frontend' => [
                    'adapter' => 'File',
                    'cacheDir' => 'app/cache/frontend/'
                ],
                'models' => [
                    'adapter' => 'Memory',
                    'lifetime' => 3600
                ],
                'view' => [
                    'adapter' => 'File',
                    'cacheDir' => 'app/cache/view/'
                ]
            ],
            'logger' => [
                'adapter' => 'File',
                'path' => 'app/logs/',
                'format' => '[%date%][%type%] %message%',
                'date_format' => 'Y-m-d H:i:s',
                'logLevel' => 'debug'
            ]
        ];
    }
    
    /**
     * Register a service
     */
    public function registerService(string $name, callable $definition): void
    {
        $this->services[$name] = $definition;
    }
    
    /**
     * Get a service
     */
    public function getService(string $name)
    {
        if (!isset($this->services[$name])) {
            throw new \Exception("Service not found: $name");
        }
        
        return $this->services[$name];
    }
    
    /**
     * Register a module
     */
    public function registerModule(string $name, ModuleInterface $module): void
    {
        $this->modules[$name] = $module;
        $module->register($this);
    }
    
    /**
     * Get a module
     */
    public function getModule(string $name): ?ModuleInterface
    {
        return $this->modules[$name] ?? null;
    }
    
    /**
     * Add an event listener
     */
    public function addEventListener(string $type, callable $listener, int $priority = 100): void
    {
        if (!isset($this->events[$type])) {
            $this->events[$type] = [];
        }
        
        $this->events[$type][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort($this->events[$type], function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }
    
    /**
     * Fire an event
     */
    public function fireEvent(string $type, $data = null, bool $cancelable = false): Event
    {
        $event = new Event($type, $data, $cancelable);
        
        if (isset($this->events[$type])) {
            foreach ($this->events[$type] as $listenerInfo) {
                $listenerInfo['listener']($event, $this);
                
                if ($cancelable && $event->isStopped()) {
                    break;
                }
            }
        }
        
        return $event;
    }
    
    /**
     * Handle a request
     */
    public function handle(Request $request): Response
    {
        // Fire before handle event
        $this->fireEvent('beforeHandle', $request);
        
        $router = $this->getService('router');
        $dispatcher = $this->getService('dispatcher');
        
        // Route the request
        $router->handle($request);
        
        // Dispatch to controller
        $dispatcher->dispatch($request);
        
        // Get response
        $response = $dispatcher->getReturnedValue();
        
        // Fire after handle event
        $this->fireEvent('afterHandle', $response);
        
        return $response;
    }
    
    /**
     * Get configuration
     */
    public function getConfig(string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? null;
    }
    
    /**
     * Show framework information
     */
    public function showInfo(): void
    {
        echo "Phalcon Framework Information\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "Services Registered (" . count($this->services) . "):\n";
        foreach ($this->services as $name => $service) {
            echo "  - $name: " . get_class($service) . "\n";
        }
        
        echo "\nConfiguration:\n";
        foreach ($this->config as $section => $values) {
            echo "  [$section]\n";
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    echo "    $key: " . json_encode($value) . "\n";
                } else {
                    echo "    $key: $value\n";
                }
            }
            echo "\n";
        }
    }
}

// Yii Framework Simulation
class YiiFramework
{
    private array $components = [];
    private array $modules = [];
    private array $aliases = [];
    private array $config = [];
    
    public function __construct()
    {
        $this->initializeComponents();
        $this->initializeConfig();
        $this->initializeAliases();
    }
    
    /**
     * Initialize Yii components
     */
    private function initializeComponents(): void
    {
        $this->components = [
            'request' => 'yii\web\Request',
            'response' => 'yii\web\Response',
            'session' => 'yii\web\Session',
            'user' => 'yii\web\User',
            'security' => 'yii\base\Security',
            'cache' => 'yii\caching\Cache',
            'db' => 'yii\db\Connection',
            'urlManager' => 'yii\web\UrlManager',
            'view' => 'yii\web\View',
            'formatter' => 'yii\i18n\Formatter',
            'i18n' => 'yii\i18n\I18N',
            'log' => 'yii\log\Logger',
            'errorHandler' => 'yii\web\ErrorHandler',
            'assetManager' => 'yii\web\AssetManager',
            'themeManager' => 'yii\base\ThemeManager',
            'authManager' => 'yii\rbac\AuthManager',
            'mailer' => 'yii\swiftmailer\Mailer'
        ];
    }
    
    /**
     * Initialize Yii configuration
     */
    private function initializeConfig(): void
    {
        $this->config = [
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8'
                ],
                'cache' => [
                    'class' => 'yii\caching\FileCache',
                    'cachePath' => '@runtime/cache'
                ],
                'user' => [
                    'identityClass' => 'app\models\User',
                    'enableAutoLogin' => true,
                    'loginUrl' => ['site/login'],
                    'logoutUrl' => ['site/logout']
                ],
                'errorHandler' => [
                    'errorAction' => 'site/error',
                    'exceptionAction' => 'site/error'
                ],
                'log' => [
                    'traceLevel' => YII_TRACE_LEVEL,
                    'targets' => [
                        [
                            [
                                'class' => 'yii\log\FileTarget',
                                'levels' => ['error', 'warning'],
                                'logFile' => '@runtime/logs/app.log',
                                'maxFileSize' => 1024 * 1024,
                                'maxFiles' => 10
                            ]
                        ]
                    ]
                ],
                'urlManager' => [
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                    'enableStrictParsing' => true,
                    'rules' => [
                        '' => 'site/index',
                        'about' => 'site/about',
                        'contact' => 'site/contact',
                        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                    ]
                ],
                'request' => [
                    'cookieValidationKey' => 'some-secret-key',
                    'csrfParam' => '_csrf',
                    'enableCsrfCookie' => true,
                    'enableCsrfValidation' => true,
                    'csrfCookie' => ['httpOnly' => true],
                    'enableCookieValidation' => true,
                    'enableCsrfCookie' => true,
                    'csrfCookie' => ['httpOnly' => true]
                ],
                'assetManager' => [
                    'bundles' => [
                        'app\assets\AppAsset' => [
                            'basePath' => '@webroot',
                            'baseUrl' => '@web',
                            'css' => [
                                'css/site.css',
                                'css/bootstrap.css'
                            ],
                            'js' => [
                                'js/yii.js',
                                'js/bootstrap.js'
                            ]
                        ]
                    ]
                ]
            ],
            'params' => [
                'adminEmail' => 'admin@example.com',
                'supportEmail' => 'support@example.com',
                'user.passwordResetTokenExpire' => 3600,
                'user.passwordMinLength' => 6
            ],
            'id' => 'basic',
            'basePath' => dirname(__DIR__),
            'bootstrap' => ['log', 'errorHandler', 'request'],
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset'
            ],
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'timeZone' => 'Asia/Shanghai',
            'charset' => 'UTF-8',
            'language' => 'zh-CN'
        ];
    }
    
    /**
     * Initialize class aliases
     */
    private function initializeAliases(): void
    {
        $this->aliases = [
            'Yii' => 'Yii',
            'StringHelper' => 'yii\helpers\StringHelper',
            'ArrayHelper' => 'yii\helpers\ArrayHelper',
            'Html' => 'yii\helpers\Html',
            'Url' => 'yii\helpers\Url',
            'FileHelper' => 'yii\helpers\FileHelper',
            'Json' => 'yii\helpers\Json',
            'SecurityHelper' => 'yii\helpers\SecurityHelper',
            'VarDumper' => 'yii\helpers\VarDumper',
            'Inflector' => 'yii\helpers\Inflector'
        ];
    }
    
    /**
     * Create a component
     */
    public function createComponent(string $name, string $class, array $config = []): void
    {
        $this->components[$name] = [
            'class' => $class,
            'config' => $config
        ];
    }
    
    /**
     * Get a component
     */
    public function getComponent(string $name)
    {
        if (!isset($this->components[$name])) {
            throw new \Exception("Component not found: $name");
        }
        
        $component = $this->components[$name];
        
        if (is_string($component)) {
            $class = $component;
            $config = [];
        } else {
            $class = $component['class'];
            $config = $component['config'] ?? [];
        }
        
        return new $class($config);
    }
    
    /**
     * Create a module
     */
    public function createModule(string $name, string $class, array $config = []): void
    {
        $this->modules[$name] = [
            'class' => $class,
            'config' => $config
        ];
    }
    
    /**
     * Get a module
     */
    public function getModule(string $name)
    {
        if (!isset($this->modules[$name])) {
            throw new \Exception("Module not found: $name");
        }
        
        $module = $this->modules[$name];
        
        return new $module['class']($module['config']);
    }
    
    /**
     * Set an alias
     */
    public function setAlias(string $alias, string $class): void
    {
        $this->aliases[$alias] = $class;
    }
    
    /**
     * Get an alias
     */
    public function getAlias(string $alias): string
    {
        return $this->aliases[$alias] ?? $alias;
    }
    
    /**
     * Get configuration
     */
    public function getConfig(string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? null;
    }
    
    /**
     * Create an ActiveRecord model
     */
    public function createActiveRecord(string $className, string $tableName): string
    {
        $modelCode = "<?php\n";
        $modelCode .= "namespace app\\models;\n\n";
        $modelCode .= "use yii\\db\\ActiveRecord;\n\n";
        $modelCode .= "class $className extends ActiveRecord\n";
        $modelCode .= "{\n";
        $modelCode .= "    public static function tableName()\n";
        $modelCode .= "    {\n";
        $modelCode .= "        return '$tableName';\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function rules()\n";
        $modelCode .= "    {\n";
        $modelCode .= "        return [\n";
        $modelCode .= "            [['field1', 'field2'], 'required'],\n";
        $modelCode .= "            [['field1'], 'string', 'max' => 255],\n";
        $modelCode .= "            [['field2'], 'integer'],\n";
        $modelCode .= "        ];\n";
        $modelCode .= "    }\n\n";
        $modelCode .= "    public function attributeLabels()\n";
        $modelCode .= "    {\n";
        $modelCode .= "        return [\n";
        $modelCode .= "            'field1' => 'Field 1',\n";
        $modelCode .= "            'field2' => 'Field 2',\n";
        $modelCode .= "        ];\n";
        $modelCode .= "    }\n";
        $modelCode .= "}\n";
        
        return $modelCode;
    }
    
    /**
     * Create a controller
     */
    public function createController(string $className): string
    {
        $controllerCode = "<?php\n";
        $controllerCode .= "namespace app\\controllers;\n\n";
        $controllerCode .= "use yii\\web\\Controller;\n";
        $controllerCode .= "use yii\\web\\NotFoundHttpException;\n\n";
        $controllerCode .= "class $className extends Controller\n";
        $controllerCode .= "{\n";
        $controllerCode .= "    public function actionIndex()\n";
        $controllerCode .= "    {\n";
        $controllerCode .= "        return \$this->render('index');\n";
        $controllerCode .= "    }\n\n";
        $controllerCode .= "    public function actionView(\$id)\n";
        $controllerCode .= "    {\n";
        $controllerCode .= "        return \$this->render('view', ['id' => \$id]);\n";
        $controllerCode .= "    }\n";
        $controllerCode .= "}\n";
        
        return $controllerCode;
    }
    
    /**
     * Show framework information
     */
    public function showInfo(): void
    {
        echo "Yii Framework Information\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Components (" . count($this->components) . "):\n";
        foreach ($this->components as $name => $component) {
            $class = is_array($component) ? $component['class'] : $component;
            echo "  - $name: $class\n";
        }
        
        echo "\nAliases (" . count($this->aliases) . "):\n";
        foreach ($this->aliases as $alias => $class) {
            echo "  - $alias: $class\n";
        }
        
        echo "\nConfiguration:\n";
        echo "  Application ID: " . $this->config['id'] . "\n";
        echo "  Base Path: " . $this->config['basePath'] . "\n";
        echo "  Time Zone: " . $this->config['timeZone'] . "\n";
        echo "  Charset: " . $this->config['charset'] . "\n";
        echo "  Language: " . $this->config['language'] . "\n";
    }
}

// Framework Comparison
class FrameworkComparison
{
    private array $frameworks = [];
    
    public function __construct()
    {
        $this->initializeFrameworks();
    }
    
    /**
     * Initialize framework data
     */
    private function initializeFrameworks(): void
    {
        $this->frameworks = [
            'laravel' => [
                'name' => 'Laravel',
                'version' => '9.x',
                'type' => 'Full-Stack',
                'license' => 'MIT',
                'learning_curve' => 'Medium',
                'performance' => 'Good',
                'documentation' => 'Excellent',
                'community' => 'Large',
                'features' => [
                    'Eloquent ORM',
                    'Blade Template Engine',
                    'Artisan CLI',
                    'Middleware System',
                    'Service Container',
                    'Queue System',
                    'Event System',
                    'Validation',
                    'Authentication',
                    'File Storage'
                ],
                'pros' => [
                    'Elegant syntax',
                    'Rich ecosystem',
                    'Excellent documentation',
                    'Active community',
                    'Powerful CLI tools',
                    'Built-in testing support'
                ],
                'cons' => [
                    'Performance overhead',
                    'Memory usage',
                    'Steep learning curve',
                    'Over-engineered for small projects'
                ]
            ],
            'symfony' => [
                'name' => 'Symfony',
                'version' => '6.x',
                'type' => 'Full-Stack',
                'license' => 'MIT',
                'learning_curve' => 'High',
                'performance' => 'Excellent',
                'documentation' => 'Excellent',
                'community' => 'Large',
                'features' => [
                    'Component System',
                    'Dependency Injection',
                    'Twig Template Engine',
                    'Doctrine ORM',
                    'Console Commands',
                    'Event Dispatcher',
                    'Form Component',
                    'Security Component',
                    'Translation',
                    'Validator'
                ],
                'pros' => [
                    'High performance',
                    'Flexible architecture',
                    'Enterprise-ready',
                    'Excellent documentation',
                    'Strong typing',
                    'Modular design'
                ],
                'cons' => [
                    'Complex configuration',
                    'Steep learning curve',
                    'Verbose code',
                    'Over-engineered for simple apps'
                ]
            ],
            'codeigniter' => [
                'name' => 'CodeIgniter',
                'version' => '4.x',
                'type' => 'Lightweight',
                'license' => 'MIT',
                'learning_curve' => 'Easy',
                'performance' => 'Excellent',
                'documentation' => 'Good',
                'community' => 'Medium',
                'features' => [
                    'Simple MVC',
                    'Database Library',
                    'Form Validation',
                    'Session Management',
                    'File Upload',
                    'Image Processing',
                    'Pagination',
                    'Security Library',
                    'Cache Library',
                    'Email Library'
                ],
                'pros' => [
                    'Fast performance',
                    'Easy to learn',
                    'Small footprint',
                    'Good documentation',
                    'Flexible configuration',
                    'No dependencies'
                ],
                'cons' => [
                    'Less modern features',
                    'Limited ecosystem',
                    'Manual testing setup',
                    'No built-in ORM',
                    'Less type safety'
                ]
            ],
            'slim' => [
                'name' => 'Slim',
                'version' => '4.x',
                'type' => 'Microframework',
                'license' => 'MIT',
                'learning_curve' => 'Easy',
                'performance' => 'Excellent',
                'documentation' => 'Good',
                'community' => 'Medium',
                'features' => [
                    'PSR-7 Support',
                    'Dependency Injection',
                    'Middleware',
                    'Routing',
                    'View Rendering',
                    'Error Handling',
                    'CSRF Protection',
                    'HTTP Caching'
                ],
                'pros' => [
                    'Minimal footprint',
                    'Fast performance',
                    'PSR compliant',
                    'Easy to extend',
                    'Good documentation',
                    'Flexible'
                ],
                'cons' => [
                    'Limited features',
                    'No built-in ORM',
                    'Manual setup for many things',
                    'Smaller community',
                    'Less opinionated'
                ]
            ],
            'phalcon' => [
                'name' => 'Phalcon',
                'version' => '5.x',
                'type' => 'Full-Stack',
                'license' => 'BSD-3',
                'learning_curve' => 'High',
                'performance' => 'Excellent',
                'documentation' => 'Good',
                'community' => 'Small',
                'features' => [
                    'C Extension',
                    'High Performance',
                    'ORM',
                    'MVC Architecture',
                    'DI Container',
                    'Event Manager',
                    'Caching',
                    'Security',
                    'CLI Tools',
                    'Volt Templates'
                ],
                'pros' => [
                    'Excellent performance',
                    'Low memory usage',
                    'C extension',
                    'Rich features',
                    'Type hints',
                    'Autoloader'
                ],
                'cons' => [
                    'Requires C knowledge',
                    'Complex setup',
                    'Smaller community',
                    'Extension dependencies',
                    'Debugging complexity'
                ]
            ],
            'yii' => [
                'name' => 'Yii',
                'version' => '2.x',
                'type' => 'Full-Stack',
                'license' => 'BSD-3',
                'learning_curve' => 'Medium',
                'performance' => 'Good',
                'documentation' => 'Good',
                'community' => 'Large',
                'features' => [
                    'Active Record',
                    'MVC Pattern',
                    'Component System',
                    'Caching',
                    'RBAC',
                    'I18N',
                    'Logging',
                    'Error Handling',
                    'CLI Tools',
                    'Assets Management'
                ],
                'pros' => [
                    'Easy to start',
                    'Good performance',
                    'Rich features',
                    'Good documentation',
                    'Large community',
                    'Component architecture'
                ],
                'cons' => [
                    'Convention heavy',
                    'Magic methods',
                    'Less type safety',
                    'Older architecture',
                    'Limited extensibility'
                ]
            ]
        ];
    }
    
    /**
     * Compare frameworks
     */
    public function compare(array $frameworks): array
    {
        $comparison = [];
        
        foreach ($frameworks as $framework) {
            if (isset($this->frameworks[$framework])) {
                $comparison[$framework] = $this->frameworks[$framework];
            }
        }
        
        return $comparison;
    }
    
    /**
     * Recommend framework based on criteria
     */
    public function recommend(array $criteria): array
    {
        $recommendations = [];
        
        $scores = [];
        
        foreach ($this->frameworks as $name => $framework) {
            $score = 0;
            
            // Learning curve
            if (isset($criteria['learning_curve'])) {
                $learningScores = ['Easy' => 3, 'Medium' => 2, 'High' => 1];
                $score += $learningScores[$framework['learning_curve']] ?? 0;
            }
            
            // Performance
            if (isset($criteria['performance'])) {
                $perfScores = ['Excellent' => 3, 'Good' => 2, 'Fair' => 1];
                $score += $perfScores[$framework['performance']] ?? 0;
            }
            
            // Documentation
            if (isset($criteria['documentation'])) {
                $docScores = ['Excellent' => 3, 'Good' => 2, 'Fair' => 1];
                $score += $docScores[$framework['documentation']] ?? 0;
            }
            
            // Community
            if (isset($criteria['community'])) {
                $commScores = ['Large' => 3, 'Medium' => 2, 'Small' => 1];
                $score += $commScores[$framework['community']] ?? 0;
            }
            
            // Type preference
            if (isset($criteria['type'])) {
                $typeScores = ['Full-Stack' => 3, 'Lightweight' => 2, 'Microframework' => 1];
                $score += $typeScores[$framework['type']] ?? 0;
            }
            
            $scores[$name] = $score;
        }
        
        // Sort by score
        arsort($scores);
        
        foreach ($scores as $name => $score) {
            $recommendations[] = [
                'framework' => $name,
                'score' => $score,
                'details' => $this->frameworks[$name]
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Get framework information
     */
    public function getFramework(string $name): ?array
    {
        return $this->frameworks[$name] ?? null;
    }
    
    /**
     * Get all frameworks
     */
    public function getAllFrameworks(): array
    {
        return $this->frameworks;
    }
    
    /**
     * Generate comparison table
     */
    public function generateComparisonTable(): string
    {
        $table = "| Framework | Version | Type | Performance | Learning Curve | Documentation | Community |\n";
        $table .= "|----------|--------|------|------------|----------------|--------------|----------|\n";
        
        foreach ($this->frameworks as $name => $framework) {
            $table .= "| {$framework['name']} | {$framework['version']} | {$framework['type']} | {$framework['performance']} | {$framework['learning_curve']} | {$framework['documentation']} | {$framework['community']} |\n";
        }
        
        return $table;
    }
    
    /**
     * Show framework comparison
     */
    public function showComparison(): void
    {
        echo "PHP Framework Comparison\n";
        echo str_repeat("-", 25) . "\n";
        
        echo $this->generateComparisonTable() . "\n\n";
        
        echo "Detailed Comparison:\n\n";
        
        foreach ($this->frameworks as $name => $framework) {
            echo "$name ({$framework['version']})\n";
            echo str_repeat("-", strlen($name) + strlen($framework['version']) + 2) . "\n";
            echo "Type: {$framework['type']}\n";
            echo "License: {$framework['license']}\n";
            echo "Learning Curve: {$framework['learning_curve']}\n";
            echo "Performance: {$framework['performance']}\n";
            echo "Documentation: {$framework['documentation']}\n";
            echo "Community: {$framework['community']}\n";
            echo "\n";
            
            echo "Key Features:\n";
            foreach ($framework['features'] as $feature) {
                echo "  • $feature\n";
            }
            
            echo "\nPros:\n";
            foreach ($framework['pros'] as $pro) {
                echo "  • $pro\n";
            }
            
            echo "\nCons:\n";
            foreach ($framework['cons'] as $con) {
                echo "  • $con\n";
            }
            
            echo "\n" . str_repeat("=", 80) . "\n\n";
        }
    }
}

// Framework Examples
class FrameworkExamples
{
    private CodeIgniterFramework $codeigniter;
    private SlimFramework $slim;
    private PhalconFramework $phalcon;
    private YiiFramework $yii;
    private FrameworkComparison $comparison;
    
    public function __construct()
    {
        $this->codeigniter = new CodeIgniterFramework();
        $this->slim = new SlimFramework();
        $this->phalcon = new PhalconFramework();
        $this->yii = new YiiFramework();
        $this->comparison = new FrameworkComparison();
    }
    
    public function demonstrateCodeIgniter(): void
    {
        echo "CodeIgniter Framework Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show framework info
        $this->codeigniter->showInfo();
        
        // Load libraries
        echo "\nLoading libraries:\n";
        $this->codeigniter->library('database');
        $this->codeigniter->library('session');
        $this->codeigniter->library('form_validation');
        
        // Load helpers
        echo "\nLoading helpers:\n";
        $this->codeigniter->helper('url');
        $this->codeigniter->helper('form');
        $this->codeigniter->helper('html');
        
        // Create controller
        echo "\nCreating controller:\n";
        $controllerCode = $this->codeigniter->createController('Blog');
        echo substr($controllerCode, 0, 500) . "...\n";
        
        // Create model
        echo "\nCreating model:\n";
        $modelCode = $this->codeigniter->createModel('Post');
        echo substr($modelCode, 0, 500) . "...\n";
        
        // Show configuration
        echo "\nConfiguration:\n";
        echo "Base URL: " . $this->codeigniter->config('base_url') . "\n";
        echo "Language: " . $this->codeigniter->config('language') . "\n";
        echo "Charset: " . $this->codeigniter->config('charset') . "\n";
    }
    
    public function demonstrateSlim(): void
    {
        echo "\nSlim Framework Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create Slim app
        $app = new SlimFramework([
            'displayErrorDetails' => true,
            'debug' => true
        ]);
        
        // Add routes
        $app->get('/', function($request) {
            return $app->getContainer()->get('response')
                ->write('Welcome to Slim!')
                ->withHeader('Content-Type', 'text/html');
        });
        
        $app->get('/hello/{name}', function($request, $args) {
            $name = $args['name'];
            return $app->getContainer()->get('response')
                ->write("Hello, $name!")
                ->withHeader('Content-Type', 'text/html');
        });
        
        $app->post('/api/users', function($request) {
            $data = json_decode($request->getBody()->getContents(), true);
            return $app->getContainer()->get('response')
                ->withJson($data)
                ->withStatus(201);
        });
        
        echo "Slim app created with routes:\n";
        echo "  GET / - Welcome page\n";
        echo "  GET /hello/{name} - Greeting\n";
        echo "  POST /api/users - Create user\n";
        
        // Show settings
        echo "\nSettings:\n";
        $settings = $app->getSettings();
        echo "Debug: " . ($settings['debug'] ? 'Enabled' : 'Disabled') . "\n";
        echo "Error Details: " . ($settings['displayErrorDetails'] ? 'Enabled' : 'Disabled') . "\n";
        echo "HTTP Version: " . $settings['httpVersion'] . "\n";
        
        // Show container
        echo "\nContainer Services:\n";
        $container = $app->getContainer();
        echo "  request: " . get_class($container['request']) . "\n";
        echo "  response: " . get_class($container['response']) . "\n";
        echo "  router: " . get_class($container['router']) . "\n";
    }
    
    public function demonstratePhalcon(): void
    {
        echo "\nPhalcon Framework Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show framework info
        $this->phalcon->showInfo();
        
        // Register services
        echo "\nRegistering services:\n";
        $this->phalcon->registerService('customService', function() {
            return new CustomService();
        });
        
        // Register modules
        echo "Registering modules:\n";
        $this->phalcon->registerModule('frontend', new FrontendModule());
        $this->phalcon->registerModule('backend', new BackendModule());
        
        // Add event listeners
        echo "\nAdding event listeners:\n";
        $this->phalcon->addEventListener('beforeHandle', function($event) {
            echo "Before handle event triggered\n";
        });
        
        $this->phalcon->addEventListener('afterHandle', function($event) {
            echo "After handle event triggered\n";
        });
        
        // Show configuration
        echo "\nDatabase Configuration:\n";
        $dbConfig = $this->phalcon->getConfig('database');
        echo "Adapter: " . $dbConfig['adapter'] . "\n";
        echo "Host: " . $dbConfig['host'] . "\n";
        echo "Database: " . $dbConfig['dbname'] . "\n";
        echo "Charset: " . $dbConfig['charset'] . "\n";
    }
    
    public function demonstrateYii(): void
    {
        echo "\nYii Framework Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Show framework info
        $this->yii->showInfo();
        
        // Create components
        echo "\nCreating components:\n";
        $this->yii->createComponent('customService', 'app\\services\\CustomService', [
                'property1' => 'value1',
                'property2' => 'value2'
            ]);
        
        // Create modules
        echo "Creating modules:\n";
        $this->yii->createModule('api', 'app\\modules\\ApiModule', [
            'version' => '1.0',
            'routes' => [
                'GET /api/users' => 'user/index',
                'POST /api/users' => 'user/create'
            ]
        ]);
        
        // Set aliases
        echo "Setting aliases:\n";
        $this->yii->setAlias('MyHelper', 'app\\helpers\\MyHelper');
        $this->yii->setAlias('StringHelper', 'yii\\helpers\\StringHelper');
        
        // Create ActiveRecord
        echo "\nCreating ActiveRecord model:\n";
        $modelCode = $this->yii->createActiveRecord('User', 'users');
        echo substr($modelCode, 0, 500) . "...\n";
        
        // Create controller
        echo "\nCreating controller:\n";
        $controllerCode = $this->yii->createController('SiteController');
        echo substr($controllerCode, 0, 500) . "...\n";
        
        // Show configuration
        echo "\nConfiguration:\n";
        $components = $this->yii->getConfig('components');
        echo "Database: " . $components['db']['class'] . "\n";
        echo "Cache: " . $components['cache']['class'] . "\n";
        echo "User: " . $components['user']['identityClass'] . "\n";
        
        $params = $this->yii->getConfig('params');
        echo "Admin Email: " . $params['adminEmail'] . "\n";
        echo "Support Email: " . $params['supportEmail'] . "\n";
    }
    
    public function demonstrateComparison(): void
    {
        echo "\nFramework Comparison Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show comparison table
        echo $this->comparison->generateComparisonTable() . "\n";
        
        // Compare specific frameworks
        $frameworks = ['laravel', 'symfony', 'codeigniter'];
        $comparison = $this->comparison->compare($frameworks);
        
        echo "\nDetailed Comparison:\n";
        foreach ($comparison as $name => $framework) {
            echo "$name:\n";
            echo "  Score: {$framework['score']}/12\n";
            echo "  Type: {$framework['details']['type']}\n";
            echo "  Performance: {$framework['details']['performance']}\n";
            echo "  Learning Curve: {$framework['details']['learning_curve']}\n";
            echo "  Documentation: {$framework['details']['documentation']}\n";
            echo "  Community: {$framework['details']['community']}\n";
            echo "\n";
        }
        
        // Recommend frameworks
        echo "Recommendations:\n";
        
        // For beginners
        $beginnerRecommendations = $this->comparison->recommend([
            'learning_curve' => 'Easy',
            'documentation' => 'Good'
        ]);
        
        echo "For Beginners:\n";
        foreach ($beginnerRecommendations as $rec) {
            echo "  {$rec['framework']['details']['name']} (Score: {$rec['score']})\n";
        }
        
        // For performance
        $performanceRecommendations = $this->comparison->recommend([
            'performance' => 'Excellent'
        ]);
        
        echo "\nFor Performance:\n";
        foreach ($performanceRecommendations as $rec) {
            echo "  {$rec['framework']['details']['name']} (Score: {$rec['score']})\n";
        }
        
        // For enterprise
        $enterpriseRecommendations = $this->comparison->recommend([
            'type' => 'Full-Stack',
            'documentation' => 'Excellent'
        ]);
        
        echo "\nFor Enterprise:\n";
        foreach ($enterpriseRecommendations as $rec) {
            echo "  {$rec['framework']['details']['name']} (Score: {$rec['score']})\n";
        }
        
        // For microservices
        $microRecommendations = $this->comparison->recommend([
            'type' => 'Microframework',
            'performance' => 'Excellent'
        ]);
        
        echo "\nFor Microservices:\n";
        foreach ($microRecommendations as $rec) {
            echo "  {$rec['framework']['details']['name']} (Score: {$rec['score']})\n";
        }
    }
    
    public function runAllExamples(): void
    {
        echo "Other PHP Frameworks Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateCodeIgniter();
        $this->demonstrateSlim();
        $this->demonstratePhalcon();
        $this->demonstrateYii();
        $this->demonstrateComparison();
    }
}

// Supporting classes for examples
class Container
{
    private array $services = [];
    
    public function get(string $id)
    {
        return $this->services[$id] ?? null;
    }
    
    public function set(string $id, $service): void
    {
        $this->services[$id] = $service;
    }
}

class ServerRequest
{
    public static function fromGlobals(): self
    {
        return new self();
    }
    
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    }
    
    public function getBody(): Stream
    {
        return new Stream(fopen('php://input', 'r'));
    }
}

class Stream
{
    private $stream;
    
    public function __construct($stream)
    {
        $this->stream = $stream;
    }
    
    public function getContents(): string
    {
        return stream_get_contents($this->stream);
    }
}

class Response
{
    private $headers = [];
    private $body = '';
    private $statusCode = 200;
    
    public function write(string $content): self
    {
        $this->body .= $content;
        return $this;
    }
    
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function withJson($data, int $status = 200): self
    {
        $this->body = json_encode($data);
        $this->statusCode = $status;
        return $this->withHeader('Content-Type', 'application/json');
    }
    
    public function withStatus(int $status): self
    {
        $this->statusCode = $status;
        return $this;
    }
    
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function getBody(): string
    {
        return $this->body;
    }
}

class Route
{
    private array $methods;
    private string $pattern;
    private $handler;
    
    public function __construct(array $methods, string $pattern, callable $handler)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->handler = $handler;
    }
    
    public function getMethods(): array
    {
        return $this->methods;
    }
    
    public function getPattern(): string
    {
        return $this->pattern;
    }
    
    public function getHandler(): callable
    {
        return $this->handler;
    }
}

class Router
{
    private array $routes = [];
    
    public function dispatch(ServerRequest $request): ?Route
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        foreach ($this->routes as $route) {
            if (in_array($method, $route->getMethods())) {
                if ($this->patternMatches($route->getPattern(), $path, $params)) {
                    return $route;
                }
            }
        }
        
        return null;
    }
    
    private function patternMatches(string $pattern, string $path, array &$params): bool
    {
        // Simple pattern matching
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        
        $pattern = "#^$pattern$#";
        
        if (preg_match($pattern, $path, $matches)) {
            if (count($matches) > 1) {
                $params = array_slice($matches, 1);
            }
            return true;
        }
        
        return false;
    }
    
    public function handle(ServerRequest $request): void
    {
        $route = $this->dispatch($request);
        
        if ($route) {
            $handler = $route->getHandler();
            $handler($request);
        }
    }
}

class Dispatcher
{
    private $returnValue;
    
    public function dispatch(ServerRequest $request): void
    {
        $router = $this->router;
        $router->handle($request);
    }
    
    public function getReturnedValue()
    {
        return $this->returnValue;
    }
    
    public function setReturnValue($value): void
    {
        $this->returnValue = $value;
    }
}

class Dispatcher
{
    private $router;
    
    public function __construct()
    {
        $this->router = new Router();
    }
    
    public function dispatch(ServerRequest $request): void
    {
        $route = $this->router->dispatch($request);
        
        if ($route) {
            $handler = $route->getHandler();
            $this->returnValue = $handler($request);
        }
    }
    
    public function getReturnValue()
    {
        return $this->returnValue;
    }
}

class Event
{
    private $type;
    private $data;
    private $cancelable;
    
    public function __construct(string $type, $data = null, bool $cancelable = false)
    {
        $this->type = $type;
        $this->data = $data;
        $this->cancelable = $cancelable;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function isStopped(): bool
    {
        return $this->cancelable && $this->cancelable;
    }
    
    public function stop(): void
    {
        $this->cancelable = true;
    }
}

interface ModuleInterface
{
    public function register($framework): void;
    public function boot(): void;
    public function shutdown(): void;
}

class FrontendModule implements ModuleInterface
{
    public function register($framework): void
    {
        echo "Frontend module registered\n";
    }
    
    public function boot(): void
    {
        echo "Frontend module booted\n";
    }
    
    public function shutdown(): void
    {
        echo "Frontend module shutdown\n";
    }
    
    public function getName(): string
    {
        return 'FrontendModule';
    }
    
    public function getVersion(): string
    {
        return '1.0.0';
    }
}

class BackendModule implements ModuleInterface
{
    public function register($framework): void
    {
        echo "Backend module registered\n";
    }
    
    public function boot(): void
    {
        echo "Backend module booted\n";
    }
    
    public function shutdown(): void
    {
        echo "Backend module shutdown\n";
    }
    
    public function getName(): string
    {
        return 'BackendModule';
    }
    
    public function getVersion(): string
    {
        return '1.0.0';
    }
}

class CustomService
{
    public function __construct()
    {
        echo "Custom service created\n";
    }
}

// Framework Best Practices
function printFrameworkBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "PHP Framework Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Framework Selection:\n";
    echo "   • Choose based on project requirements\n";
    echo "   • Consider team expertise\n";
    echo "   • Evaluate performance needs\n";
    echo "   • Check community support\n";
    echo "   • Review documentation quality\n\n";
    
    echo "2. CodeIgniter:\n";
    echo "   • Use for small to medium projects\n";
    echo "   • Follow MVC conventions\n";
    • Use libraries appropriately\n";
    echo "   • Keep controllers thin\n";
    echo "   • Use models for business logic\n\n";
    
    echo "3. Slim:\n";
    echo "   • Use for microservices and APIs\n";
    echo "   • Follow PSR standards\n";
    echo "   • Use dependency injection\n";
    echo "   • Implement middleware\n";
    echo "   • Keep it minimal\n";
    echo "   • Use PSR-7 interfaces\n\n";
    
    echo "4. Phalcon:\n";
    echo "   • Use for high-performance apps\n";
    echo "   • Learn C basics\n";
    echo "   • Use DI container\n";
    echo "   • Leverage extensions\n";
    echo "   • Use Volt templates\n";
    echo "   • Monitor memory usage\n\n";
    
    echo "5. Yii:\n";
    echo "   • Use for enterprise apps\n";
    echo "   • Follow conventions\n";
    echo "   • Use components\n";
    echo "   • Use Active Record\n";
    echo "   • Implement caching\n";
    echo "   • Use RBAC for security\n\n";
    
    echo "6. General Practices:\n";
    echo "   • Keep up with updates\n";
    echo "   • Use version control\n";
    echo "   • Write tests\n";
    echo "   • Document code\n";
    echo "   • Follow security best practices\n";
    echo "   • Optimize performance\n";
}

// Main execution
function runOtherFrameworksDemo(): void
{
    $examples = new FrameworkExamples();
    $examples->runAllExamples();
    printFrameworkBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runOtherFrameworksDemo();
}
?>
