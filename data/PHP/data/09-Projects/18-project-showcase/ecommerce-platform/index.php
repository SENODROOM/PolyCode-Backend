<?php
/**
 * E-Commerce Platform - Main Entry Point
 * 
 * A complete e-commerce solution with product catalog,
 * shopping cart, payment processing, and admin dashboard.
 */

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'ECommerce\\';
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

use ECommerce\Config\Database;
use ECommerce\Core\Router;
use ECommerce\Core\Request;
use ECommerce\Core\Response;
use ECommerce\Services\AuthService;
use ECommerce\Services\CartService;
use ECommerce\Services\ProductService;
use ECommerce\Services\OrderService;
use ECommerce\Middleware\AuthMiddleware;
use ECommerce\Middleware\CorsMiddleware;

// Initialize services
$db = new Database();
$router = new Router();
$request = new Request();
$response = new Response();

// Middleware
$router->addMiddleware(new CorsMiddleware());
$router->addMiddleware(new AuthMiddleware());

// Services
$authService = new AuthService($db);
$cartService = new CartService($db);
$productService = new ProductService($db);
$orderService = new OrderService($db);

// Define routes
$router->get('/', function() use ($productService) {
    $featuredProducts = $productService->getFeaturedProducts(8);
    $categories = $productService->getCategories();
    
    include __DIR__ . '/views/home.php';
});

// Product routes
$router->get('/products', function() use ($productService, $request) {
    $page = $request->get('page', 1);
    $category = $request->get('category');
    $search = $request->get('search');
    $sort = $request->get('sort', 'name');
    
    $products = $productService->getProducts($page, 12, $category, $search, $sort);
    $categories = $productService->getCategories();
    
    include __DIR__ . '/views/products.php';
});

$router->get('/products/{id}', function($id) use ($productService) {
    $product = $productService->getProduct($id);
    $relatedProducts = $productService->getRelatedProducts($id, 4);
    
    if (!$product) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    include __DIR__ . '/views/product.php';
});

// Cart routes
$router->get('/cart', function() use ($cartService) {
    $cart = $cartService->getCart();
    include __DIR__ . '/views/cart.php';
});

$router->post('/cart/add', function() use ($cartService, $request) {
    $productId = $request->post('product_id');
    $quantity = $request->post('quantity', 1);
    
    $result = $cartService->addToCart($productId, $quantity);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->post('/cart/update', function() use ($cartService, $request) {
    $productId = $request->post('product_id');
    $quantity = $request->post('quantity');
    
    $result = $cartService->updateQuantity($productId, $quantity);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->post('/cart/remove', function() use ($cartService, $request) {
    $productId = $request->post('product_id');
    
    $result = $cartService->removeFromCart($productId);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Authentication routes
$router->get('/login', function() {
    if (isset($_SESSION['user'])) {
        header('Location: /dashboard');
        exit;
    }
    include __DIR__ . '/views/login.php';
});

$router->post('/login', function() use ($authService, $request) {
    $email = $request->post('email');
    $password = $request->post('password');
    
    $result = $authService->login($email, $password);
    
    if ($result['success']) {
        header('Location: /dashboard');
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /login');
    }
});

$router->get('/register', function() {
    if (isset($_SESSION['user'])) {
        header('Location: /dashboard');
        exit;
    }
    include __DIR__ . '/views/register.php';
});

$router->post('/register', function() use ($authService, $request) {
    $userData = [
        'name' => $request->post('name'),
        'email' => $request->post('email'),
        'password' => $request->post('password'),
        'phone' => $request->post('phone')
    ];
    
    $result = $authService->register($userData);
    
    if ($result['success']) {
        header('Location: /login?registered=true');
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /register');
    }
});

$router->get('/logout', function() use ($authService) {
    $authService->logout();
    header('Location: /');
});

// Checkout routes
$router->get('/checkout', function() use ($cartService, $authService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login?redirect=checkout');
        exit;
    }
    
    $cart = $cartService->getCart();
    if (empty($cart['items'])) {
        header('Location: /cart');
        exit;
    }
    
    include __DIR__ . '/views/checkout.php';
});

$router->post('/checkout', function() use ($orderService, $cartService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $orderData = [
        'user_id' => $_SESSION['user']['id'],
        'shipping_address' => $request->post('shipping_address'),
        'billing_address' => $request->post('billing_address'),
        'payment_method' => $request->post('payment_method'),
        'notes' => $request->post('notes')
    ];
    
    $result = $orderService->createOrder($orderData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// User dashboard
$router->get('/dashboard', function() use ($orderService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login?redirect=dashboard');
        exit;
    }
    
    $orders = $orderService->getUserOrders($_SESSION['user']['id']);
    include __DIR__ . '/views/dashboard.php';
});

// Admin routes
$router->get('/admin', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }
    
    include __DIR__ . '/views/admin/dashboard.php';
});

$router->get('/admin/products', function() use ($productService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }
    
    $page = $_GET['page'] ?? 1;
    $products = $productService->getAllProducts($page, 20);
    
    include __DIR__ . '/views/admin/products.php';
});

$router->get('/admin/orders', function() use ($orderService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }
    
    $page = $_GET['page'] ?? 1;
    $status = $_GET['status'] ?? '';
    $orders = $orderService->getAllOrders($page, 20, $status);
    
    include __DIR__ . '/views/admin/orders.php';
});

// API routes
$router->get('/api/products', function() use ($productService, $request) {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 10);
    $category = $request->get('category');
    
    $products = $productService->getProducts($page, $limit, $category);
    
    header('Content-Type: application/json');
    echo json_encode($products);
});

$router->get('/api/categories', function() use ($productService) {
    $categories = $productService->getCategories();
    
    header('Content-Type: application/json');
    echo json_encode($categories);
});

$router->get('/api/search', function() use ($productService, $request) {
    $query = $request->get('q');
    $results = $productService->search($query);
    
    header('Content-Type: application/json');
    echo json_encode($results);
});

// Handle the request
try {
    $router->dispatch($request, $response);
} catch (Exception $e) {
    error_log($e->getMessage());
    
    http_response_code(500);
    include __DIR__ . '/views/500.php';
}
?>
