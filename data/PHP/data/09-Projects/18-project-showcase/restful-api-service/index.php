<?php
/**
 * RESTful API Service - Main Entry Point
 * 
 * A comprehensive RESTful API with authentication, rate limiting,
 * versioning, documentation, and best practices.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'API\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use API\Config\Database;
use API\Core\Router;
use API\Core\Request;
use API\Core\Response;
use API\Services\AuthService;
use API\Services\UserService;
use API\Services\PostService;
use API\Services\ProductService;
use API\Services\OrderService;
use API\Middleware\AuthMiddleware;
use API\Middleware\RateLimitMiddleware;
use API\Middleware\CorsMiddleware;
use API\Middleware\ApiKeyMiddleware;

// Initialize services
$db = new Database();
$router = new Router();
$request = new Request();
$response = new Response();

// Global middleware
$router->addMiddleware(new CorsMiddleware());
$router->addMiddleware(new RateLimitMiddleware());

// Services
$authService = new AuthService($db);
$userService = new UserService($db);
$postService = new PostService($db);
$productService = new ProductService($db);
$orderService = new OrderService($db);

// API Version 1
$router->group('/api/v1', function($router) use ($authService, $userService, $postService, $productService, $orderService) {
    
    // Public routes (no authentication required)
    $router->get('/health', function() {
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ]);
    });
    
    $router->get('/info', function() {
        echo json_encode([
            'name' => 'RESTful API Service',
            'version' => '1.0.0',
            'description' => 'A comprehensive RESTful API with authentication and best practices',
            'endpoints' => [
                'users' => '/api/v1/users',
                'posts' => '/api/v1/posts',
                'products' => '/api/v1/products',
                'orders' => '/api/v1/orders'
            ]
        ]);
    });
    
    // Authentication routes
    $router->post('/auth/login', function() use ($authService, $request) {
        $email = $request->post('email');
        $password = $request->post('password');
        
        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }
        
        $result = $authService->login($email, $password);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'token' => $result['token'],
                'user' => $result['user'],
                'expires_in' => 3600
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => $result['message']]);
        }
    });
    
    $router->post('/auth/register', function() use ($authService, $request) {
        $userData = [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => $request->post('password'),
            'role' => $request->post('role', 'user')
        ];
        
        $result = $authService->register($userData);
        
        if ($result['success']) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $result['user']
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $result['message']]);
        }
    });
    
    // Protected routes (authentication required)
    $router->group('/users', function($router) use ($userService, $authService) {
        $router->addMiddleware(new AuthMiddleware($authService));
        
        $router->get('', function() use ($userService, $request) {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $search = $request->get('search');
            
            $users = $userService->getUsers($page, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'data' => $users,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $users['total'] ?? 0
                ]
            ]);
        });
        
        $router->get('/{id}', function($id) use ($userService) {
            $user = $userService->getUser($id);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
        });
        
        $router->post('', function() use ($userService, $request) {
            $userData = [
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'password' => $request->post('password'),
                'role' => $request->post('role', 'user')
            ];
            
            $result = $userService->createUser($userData);
            
            if ($result['success']) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => $result['user']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
        
        $router->put('/{id}', function($id) use ($userService, $request) {
            $userData = [
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'role' => $request->post('role')
            ];
            
            $result = $userService->updateUser($id, $userData);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User updated successfully',
                    'data' => $result['user']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
        
        $router->delete('/{id}', function($id) use ($userService) {
            $result = $userService->deleteUser($id);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
    });
    
    // Posts endpoints
    $router->group('/posts', function($router) use ($postService, $authService) {
        $router->addMiddleware(new AuthMiddleware($authService));
        
        $router->get('', function() use ($postService, $request) {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $status = $request->get('status', 'published');
            
            $posts = $postService->getPosts($page, $limit, $status);
            
            echo json_encode([
                'success' => true,
                'data' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $posts['total'] ?? 0
                ]
            ]);
        });
        
        $router->get('/{id}', function($id) use ($postService) {
            $post = $postService->getPost($id);
            
            if (!$post) {
                http_response_code(404);
                echo json_encode(['error' => 'Post not found']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $post
            ]);
        });
        
        $router->post('', function() use ($postService, $request) {
            $postData = [
                'title' => $request->post('title'),
                'content' => $request->post('content'),
                'status' => $request->post('status', 'draft'),
                'author_id' => $request->user['id']
            ];
            
            $result = $postService->createPost($postData);
            
            if ($result['success']) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Post created successfully',
                    'data' => $result['post']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
        
        $router->put('/{id}', function($id) use ($postService, $request) {
            $postData = [
                'title' => $request->post('title'),
                'content' => $request->post('content'),
                'status' => $request->post('status')
            ];
            
            $result = $postService->updatePost($id, $postData);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Post updated successfully',
                    'data' => $result['post']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
        
        $router->delete('/{id}', function($id) use ($postService) {
            $result = $postService->deletePost($id);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Post deleted successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
    });
    
    // Products endpoints
    $router->group('/products', function($router) use ($productService) {
        $router->get('', function() use ($productService, $request) {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $category = $request->get('category');
            $search = $request->get('search');
            
            $products = $productService->getProducts($page, $limit, $category, $search);
            
            echo json_encode([
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $products['total'] ?? 0
                ]
            ]);
        });
        
        $router->get('/{id}', function($id) use ($productService) {
            $product = $productService->getProduct($id);
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);
        });
        
        $router->get('/categories', function() use ($productService) {
            $categories = $productService->getCategories();
            
            echo json_encode([
                'success' => true,
                'data' => $categories
            ]);
        });
        
        $router->post('', function() use ($productService, $request) {
            // This would require admin authentication
            $productData = [
                'name' => $request->post('name'),
                'description' => $request->post('description'),
                'price' => $request->post('price'),
                'category' => $request->post('category'),
                'stock' => $request->post('stock', 0)
            ];
            
            $result = $productService->createProduct($productData);
            
            if ($result['success']) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'data' => $result['product']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
    });
    
    // Orders endpoints
    $router->group('/orders', function($router) use ($orderService, $authService) {
        $router->addMiddleware(new AuthMiddleware($authService));
        
        $router->get('', function() use ($orderService, $request) {
            $userId = $request->user['id'];
            $page = $request->get('page', 1);
            $status = $request->get('status');
            
            $orders = $orderService->getUserOrders($userId, $page, 10, $status);
            
            echo json_encode([
                'success' => true,
                'data' => $orders,
                'pagination' => [
                    'page' => $page,
                    'limit' => 10,
                    'total' => $orders['total'] ?? 0
                ]
            ]);
        });
        
        $router->get('/{id}', function($id) use ($orderService, $request) {
            $userId = $request->user['id'];
            $order = $orderService->getOrder($id, $userId);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $order
            ]);
        });
        
        $router->post('', function() use ($orderService, $request) {
            $orderData = [
                'user_id' => $request->user['id'],
                'items' => $request->post('items', []),
                'shipping_address' => $request->post('shipping_address'),
                'payment_method' => $request->post('payment_method')
            ];
            
            $result = $orderService->createOrder($orderData);
            
            if ($result['success']) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => $result['order']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['message']]);
            }
        });
    });
});

// API Documentation endpoint
$router->get('/docs', function() {
    $documentation = [
        'title' => 'RESTful API Service Documentation',
        'version' => '1.0.0',
        'base_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/v1',
        'authentication' => [
            'type' => 'Bearer Token',
            'description' => 'Include token in Authorization header: Authorization: Bearer <token>'
        ],
        'endpoints' => [
            'Authentication' => [
                'POST /auth/login' => 'Login with email and password',
                'POST /auth/register' => 'Register new user'
            ],
            'Users' => [
                'GET /users' => 'Get all users (authenticated)',
                'GET /users/{id}' => 'Get user by ID (authenticated)',
                'POST /users' => 'Create user (authenticated)',
                'PUT /users/{id}' => 'Update user (authenticated)',
                'DELETE /users/{id}' => 'Delete user (authenticated)'
            ],
            'Posts' => [
                'GET /posts' => 'Get all posts (authenticated)',
                'GET /posts/{id}' => 'Get post by ID (authenticated)',
                'POST /posts' => 'Create post (authenticated)',
                'PUT /posts/{id}' => 'Update post (authenticated)',
                'DELETE /posts/{id}' => 'Delete post (authenticated)'
            ],
            'Products' => [
                'GET /products' => 'Get all products',
                'GET /products/{id}' => 'Get product by ID',
                'GET /products/categories' => 'Get product categories',
                'POST /products' => 'Create product (admin only)'
            ],
            'Orders' => [
                'GET /orders' => 'Get user orders (authenticated)',
                'GET /orders/{id}' => 'Get order by ID (authenticated)',
                'POST /orders' => 'Create order (authenticated)'
            ]
        ],
        'status_codes' => [
            '200' => 'Success',
            '201' => 'Created',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '429' => 'Too Many Requests',
            '500' => 'Internal Server Error'
        ],
        'examples' => [
            'login' => [
                'method' => 'POST',
                'url' => '/api/v1/auth/login',
                'body' => [
                    'email' => 'user@example.com',
                    'password' => 'password123'
                ]
            ],
            'get_users' => [
                'method' => 'GET',
                'url' => '/api/v1/users?page=1&limit=10',
                'headers' => [
                    'Authorization' => 'Bearer <token>'
                ]
            ]
        ]
    ];
    
    echo json_encode($documentation, JSON_PRETTY_PRINT);
});

// Error handling
$router->setErrorHandler(function($exception) {
    error_log($exception->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => 'An unexpected error occurred',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Handle the request
try {
    $router->dispatch($request, $response);
} catch (Exception $e) {
    error_log($e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => 'An unexpected error occurred',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
