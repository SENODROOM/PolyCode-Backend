<?php
// E-commerce Cart System - Main Application

// Configuration
define('APP_NAME', 'E-commerce Cart');
define('APP_VERSION', '1.0.0');
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_cart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start session
session_start();

// Database class
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function setupDatabase() {
        try {
            // Create categories table
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create products table
            $sql = "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                category_id INT,
                sku VARCHAR(100) UNIQUE NOT NULL,
                stock_quantity INT DEFAULT 0,
                image_url VARCHAR(255),
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )";
            $this->pdo->exec($sql);
            
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                role ENUM('user', 'admin') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create orders table
            $sql = "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                tax_amount DECIMAL(10,2) DEFAULT 0,
                shipping_amount DECIMAL(10,2) DEFAULT 0,
                status ENUM('pending', 'processing', 'shipped', 'delivered') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )";
            $this->pdo->exec($sql);
            
            // Create order items table
            $sql = "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                unit_price DECIMAL(10,2) NOT NULL,
                total_price DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id)
            )";
            $this->pdo->exec($sql);
            
            // Insert default categories
            $this->pdo->exec("INSERT IGNORE INTO categories (name, description) VALUES 
                ('Electronics', 'Electronic devices and gadgets'),
                ('Clothing', 'Fashion and apparel'),
                ('Books', 'Books and educational materials'),
                ('Home & Garden', 'Home improvement and garden supplies')");
            
            // Insert sample products
            $products = [
                ['Laptop Pro', 'High-performance laptop for professionals', 999.99, 1, 'LAPTOP-001', 50, 'https://via.placeholder.com/300x200'],
                ['Wireless Mouse', 'Ergonomic wireless mouse', 29.99, 1, 'MOUSE-001', 100, 'https://via.placeholder.com/300x200'],
                ['T-Shirt', 'Comfortable cotton t-shirt', 19.99, 2, 'SHIRT-001', 200, 'https://via.placeholder.com/300x200'],
                ['Jeans', 'Classic denim jeans', 49.99, 2, 'JEANS-001', 150, 'https://via.placeholder.com/300x200'],
                ['PHP Programming', 'Learn PHP from scratch', 29.99, 3, 'BOOK-001', 75, 'https://via.placeholder.com/300x200'],
                ['Garden Tools Set', 'Complete garden tool set', 79.99, 4, 'TOOLS-001', 30, 'https://via.placeholder.com/300x200']
            ];
            
            foreach ($products as $product) {
                $this->pdo->exec("INSERT IGNORE INTO products (name, description, price, category_id, sku, stock_quantity, image_url) VALUES 
                    ('{$product[0]}', '{$product[1]}', {$product[2]}, {$product[3]}, '{$product[4]}', {$product[5]}, '{$product[6]}')");
            }
            
            // Create admin user
            $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
            $this->pdo->exec("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, role) VALUES 
                ('admin', 'admin@example.com', '$adminHash', 'Admin', 'User', 'admin')");
            
            echo "Database setup completed!<br>";
        } catch (PDOException $e) {
            echo "Database setup error: " . $e->getMessage() . "<br>";
        }
    }
}

// User class
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function register($username, $email, $password, $firstName, $lastName) {
        if (strlen($username) < 3 || strlen($username) > 50) {
            return ['success' => false, 'message' => 'Username must be 3-50 characters'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }
        
        $stmt = $this->db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
            $this->db->query($sql, [$username, $email, $passwordHash, $firstName, $lastName]);
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
        $stmt = $this->db->query($sql, [$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return $user;
        }
        
        return false;
    }
    
    public function getById($id) {
        $sql = "SELECT id, username, email, first_name, last_name, role, created_at FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
}

// Product class
class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($categoryId = null, $limit = 20) {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? AND p.status = 'active'";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function search($query, $limit = 20) {
        $searchTerm = "%$query%";
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND 
                      (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                ORDER BY p.name ASC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

// Cart class
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    public function addItem($productId, $quantity = 1) {
        $product = $this->getProduct($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $newQuantity = $_SESSION['cart'][$cartKey]['quantity'] + $quantity;
            
            if ($product['stock_quantity'] < $newQuantity) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
            
            $_SESSION['cart'][$cartKey]['quantity'] = $newQuantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return ['success' => true, 'message' => 'Item added to cart'];
    }
    
    public function updateItem($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }
        
        $product = $this->getProduct($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Cart updated'];
        }
        
        return ['success' => false, 'message' => 'Item not in cart'];
    }
    
    public function removeItem($productId) {
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            return ['success' => true, 'message' => 'Item removed from cart'];
        }
        
        return ['success' => false, 'message' => 'Item not in cart'];
    }
    
    public function getItems() {
        $items = [];
        
        foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            $product = $this->getProduct($cartItem['product_id']);
            
            if ($product) {
                $items[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $product['price'] * $cartItem['quantity'],
                    'stock_quantity' => $product['stock_quantity']
                ];
            }
        }
        
        return $items;
    }
    
    public function getSubtotal() {
        $subtotal = 0;
        
        foreach ($this->getItems() as $item) {
            $subtotal += $item['subtotal'];
        }
        
        return $subtotal;
    }
    
    public function getTotal($taxRate = 0.08, $shippingCost = 0) {
        $subtotal = $this->getSubtotal();
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax + $shippingCost;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shippingCost,
            'total' => $total
        ];
    }
    
    public function getItemCount() {
        $count = 0;
        
        foreach ($_SESSION['cart'] as $cartItem) {
            $count += $cartItem['quantity'];
        }
        
        return $count;
    }
    
    public function clear() {
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    
    private function getProduct($productId) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM products WHERE id = ? AND status = 'active'";
        $stmt = $db->query($sql, [$productId]);
        return $stmt->fetch();
    }
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $user = new User();
    return $user->getById($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ?action=login');
        exit;
    }
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Initialize database
$db = Database::getInstance();
$db->setupDatabase();

// Initialize cart
$cart = new Cart();

// Handle requests
$action = $_GET['action'] ?? 'home';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['form_type'] ?? '') {
        case 'register':
            $user = new User();
            $result = $user->register(
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                $_POST['first_name'],
                $_POST['last_name']
            );
            
            if ($result['success']) {
                $message = $result['message'];
                $action = 'login';
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'login':
            $user = new User();
            $loginResult = $user->login($_POST['username'], $_POST['password']);
            
            if ($loginResult) {
                $message = 'Login successful!';
                $action = 'home';
            } else {
                $error = 'Invalid username or password';
            }
            break;
            
        case 'add_to_cart':
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1;
            $result = $cart->addItem($productId, $quantity);
            
            if ($result['success']) {
                $message = $result['message'];
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'update_cart':
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $result = $cart->updateItem($productId, $quantity);
            
            if ($result['success']) {
                $message = $result['message'];
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'remove_from_cart':
            $productId = $_POST['product_id'];
            $result = $cart->removeItem($productId);
            
            if ($result['success']) {
                $message = $result['message'];
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'checkout':
            requireLogin();
            // Simplified checkout - just clear cart
            $cart->clear();
            $message = 'Order placed successfully! (Demo)';
            $action = 'home';
            break;
    }
}

// Handle logout
if ($action === 'logout') {
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #007bff;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav a {
            color: #333;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: #f8f9fa;
        }

        .cart-icon {
            position: relative;
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #1e7e34;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .product-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
        }

        .cart-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .cart-summary h3 {
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: bold;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo"><?= APP_NAME ?></div>
            <nav>
                <ul>
                    <li><a href="?action=home">Home</a></li>
                    <li><a href="?action=products">Products</a></li>
                    <li><a href="?action=cart" class="cart-icon">
                        Cart (<?= $cart->getItemCount() ?>)
                        <?php if ($cart->getItemCount() > 0): ?>
                            <span class="cart-count"><?= $cart->getItemCount() ?></span>
                        <?php endif; ?>
                    </a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="?action=logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="?action=login">Login</a></li>
                        <li><a href="?action=register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <?php
        switch ($action) {
            case 'home':
                include 'views/home.php';
                break;
            case 'products':
                include 'views/products.php';
                break;
            case 'product_detail':
                include 'views/product_detail.php';
                break;
            case 'cart':
                include 'views/cart.php';
                break;
            case 'login':
                include 'views/login.php';
                break;
            case 'register':
                include 'views/register.php';
                break;
            default:
                include 'views/home.php';
        }
        ?>
    </div>
</body>
</html>
