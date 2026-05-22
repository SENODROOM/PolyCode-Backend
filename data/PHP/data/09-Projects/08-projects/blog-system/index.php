<?php
// Blog System - Main Application

// Configuration
define('APP_NAME', 'Blog System');
define('APP_VERSION', '2.0.0');
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_system');
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
    
    public function setupDatabase() {
        try {
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                role ENUM('user', 'admin') DEFAULT 'user',
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create posts table
            $sql = "CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content TEXT NOT NULL,
                excerpt TEXT,
                author_id INT NOT NULL,
                status ENUM('draft', 'published') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            $this->pdo->exec($sql);
            
            // Create categories table
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Insert default categories
            $this->pdo->exec("INSERT IGNORE INTO categories (name, slug) VALUES 
                ('Technology', 'technology'),
                ('Lifestyle', 'lifestyle'),
                ('Business', 'business')");
            
            // Create admin user if not exists
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
        
        // Check if user exists
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
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
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
    
    public function getAll() {
        $sql = "SELECT id, username, email, first_name, last_name, role, status, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

// BlogPost class
class BlogPost {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($title, $content, $authorId, $status = 'draft') {
        $slug = $this->generateSlug($title);
        $excerpt = $this->generateExcerpt($content);
        
        try {
            $sql = "INSERT INTO posts (title, slug, content, excerpt, author_id, status) VALUES (?, ?, ?, ?, ?, ?)";
            $this->db->query($sql, [$title, $slug, $content, $excerpt, $authorId, $status]);
            return $this->db->getConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getAll($status = 'published', $limit = 10) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = ?
                ORDER BY p.created_at DESC
                LIMIT ?";
        $stmt = $this->db->query($sql, [$status, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function update($id, $title, $content, $status) {
        $slug = $this->generateSlug($title);
        $excerpt = $this->generateExcerpt($content);
        
        $sql = "UPDATE posts SET title = ?, slug = ?, content = ?, excerpt = ?, status = ? WHERE id = ?";
        $stmt = $this->db->query($sql, [$title, $slug, $content, $excerpt, $status, $id]);
        return $stmt->rowCount() > 0;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->rowCount() > 0;
    }
    
    public function search($query) {
        $searchTerm = "%$query%";
        $sql = "SELECT p.*, u.username as author_name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published' AND 
                      (p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    private function generateExcerpt($content, $length = 150) {
        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        
        if (strlen($content) <= $length) {
            return $content;
        }
        
        return substr($content, 0, strrpos(substr($content, 0, $length), ' ')) . '...';
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

function requireAdmin() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied';
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
            
        case 'create_post':
            requireLogin();
            $post = new BlogPost();
            $postId = $post->create(
                $_POST['title'],
                $_POST['content'],
                $_SESSION['user_id'],
                $_POST['status']
            );
            
            if ($postId) {
                $message = 'Post created successfully!';
                $action = 'home';
            } else {
                $error = 'Failed to create post';
            }
            break;
            
        case 'edit_post':
            requireLogin();
            $post = new BlogPost();
            $updated = $post->update(
                $_POST['post_id'],
                $_POST['title'],
                $_POST['content'],
                $_POST['status']
            );
            
            if ($updated) {
                $message = 'Post updated successfully!';
                $action = 'home';
            } else {
                $error = 'Failed to update post';
            }
            break;
            
        case 'delete_post':
            requireLogin();
            $post = new BlogPost();
            $deleted = $post->delete($_POST['post_id']);
            
            if ($deleted) {
                $message = 'Post deleted successfully!';
                $action = 'home';
            } else {
                $error = 'Failed to delete post';
            }
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
            background: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: rgba(255,255,255,0.1);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-success {
            background: #27ae60;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .post-list {
            display: grid;
            gap: 20px;
        }

        .post-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .post-card:hover {
            transform: translateY(-2px);
        }

        .post-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .post-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .post-excerpt {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .post-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }

        .admin-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }

        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
        }

        .user-list {
            margin-top: 20px;
        }

        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            nav ul {
                flex-direction: column;
                text-align: center;
            }

            .post-actions {
                flex-direction: column;
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
                    <?php if (isLoggedIn()): ?>
                        <li><a href="?action=create_post">Write Post</a></li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="?action=admin">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="?action=logout">Logout (<?= $_SESSION['username'] ?>)</a></li>
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
            case 'login':
                include 'views/login.php';
                break;
            case 'register':
                include 'views/register.php';
                break;
            case 'create_post':
                requireLogin();
                include 'views/create_post.php';
                break;
            case 'edit_post':
                requireLogin();
                include 'views/edit_post.php';
                break;
            case 'view_post':
                include 'views/view_post.php';
                break;
            case 'admin':
                requireAdmin();
                include 'views/admin.php';
                break;
            default:
                include 'views/home.php';
        }
        ?>
    </div>
</body>
</html>
