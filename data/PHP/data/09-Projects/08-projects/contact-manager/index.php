<?php
// Contact Manager - Main Application

// Configuration
define('APP_NAME', 'Contact Manager');
define('APP_VERSION', '1.0.0');
define('DB_HOST', 'localhost');
define('DB_NAME', 'contact_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
define('CACHE_DIR', __DIR__ . '/cache');

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
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
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
                color VARCHAR(7) DEFAULT '#007bff',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create contacts table
            $sql = "CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255),
                phone VARCHAR(50),
                company VARCHAR(255),
                job_title VARCHAR(255),
                address TEXT,
                birthday DATE,
                notes TEXT,
                profile_picture VARCHAR(255),
                category_id INT,
                tags JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )";
            $this->pdo->exec($sql);
            
            // Create search history table
            $sql = "CREATE TABLE IF NOT EXISTS search_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                query VARCHAR(255) NOT NULL,
                filters JSON,
                results_count INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Insert default categories
            $this->pdo->exec("INSERT IGNORE INTO categories (name, description, color) VALUES 
                ('Family', 'Family members and relatives', '#28a745'),
                ('Friends', 'Friends and acquaintances', '#17a2b8'),
                ('Work', 'Work colleagues and business contacts', '#ffc107'),
                ('Clients', 'Client contacts', '#dc3545'),
                ('Other', 'Other contacts', '#6c757d')");
            
            // Insert sample contacts
            $contacts = [
                ['John', 'Doe', 'john@example.com', '555-0101', 'Tech Corp', 'Software Engineer', '123 Main St, City, State', '1990-05-15', 'Software developer specializing in PHP', null, 3, '["developer", "php", "javascript"]'],
                ['Jane', 'Smith', 'jane@example.com', '555-0102', 'Design Co', 'UI Designer', '456 Oak Ave, Town, State', '1992-08-22', 'Creative designer with 5 years experience', null, 3, '["designer", "ui", "ux"]'],
                ['Bob', 'Johnson', 'bob@example.com', '555-0103', 'Marketing Inc', 'Marketing Manager', '789 Pine Rd, Village, State', '1988-03-10', 'Marketing expert and team leader', null, 3, '["marketing", "manager", "strategy"]'],
                ['Alice', 'Brown', 'alice@example.com', '555-0104', '', 'Teacher', '321 Elm St, City, State', '1985-12-05', 'Elementary school teacher', null, 2, '["teacher", "education", "children"]'],
                ['Charlie', 'Wilson', 'charlie@example.com', '555-0105', 'StartupXYZ', 'CEO', '654 Maple Dr, Town, State', '1980-07-18', 'Entrepreneur and startup founder', null, 1, '["ceo", "entrepreneur", "startup"]']
            ];
            
            foreach ($contacts as $contact) {
                $this->pdo->exec("INSERT IGNORE INTO contacts (first_name, last_name, email, phone, company, job_title, address, birthday, notes, category_id, tags) VALUES 
                    ('{$contact[0]}', '{$contact[1]}', '{$contact[2]}', '{$contact[3]}', '{$contact[4]}', '{$contact[5]}', '{$contact[6]}', '{$contact[7]}', '{$contact[8]}', {$contact[10]}, '{$contact[11]}')");
            }
            
            echo "Database setup completed!<br>";
        } catch (PDOException $e) {
            echo "Database setup error: " . $e->getMessage() . "<br>";
        }
    }
}

// Contact class
class Contact {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return ['success' => false, 'message' => 'First name and last name are required'];
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if ($this->isDuplicate($data)) {
            return ['success' => false, 'message' => 'Contact with this email or phone already exists'];
        }
        
        $contactData = [
            'first_name' => htmlspecialchars(trim($data['first_name'])),
            'last_name' => htmlspecialchars(trim($data['last_name'])),
            'email' => !empty($data['email']) ? htmlspecialchars(trim($data['email'])) : null,
            'phone' => !empty($data['phone']) ? htmlspecialchars(trim($data['phone'])) : null,
            'company' => !empty($data['company']) ? htmlspecialchars(trim($data['company'])) : null,
            'job_title' => !empty($data['job_title']) ? htmlspecialchars(trim($data['job_title'])) : null,
            'address' => !empty($data['address']) ? htmlspecialchars(trim($data['address'])) : null,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'notes' => !empty($data['notes']) ? htmlspecialchars(trim($data['notes'])) : null,
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'tags' => !empty($data['tags']) ? json_encode($data['tags']) : null
        ];
        
        try {
            $contactId = $this->db->insert('contacts', $contactData);
            return ['success' => true, 'contact_id' => $contactId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create contact'];
        }
    }
    
    public function getAll($limit = 50, $categoryId = null) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " WHERE c.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY c.last_name, c.first_name LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        $contacts = $stmt->fetchAll();
        
        foreach ($contacts as &$contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
        }
        
        return $contacts;
    }
    
    public function getById($id) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $contact = $stmt->fetch();
        
        if ($contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
        }
        
        return $contact;
    }
    
    public function update($id, $data) {
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        $allowedFields = [
            'first_name', 'last_name', 'email', 'phone', 'company', 
            'job_title', 'address', 'birthday', 'notes', 'category_id', 'tags'
        ];
        
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'tags') {
                    $updateData[$field] = json_encode($data[$field]);
                } elseif (in_array($field, ['first_name', 'last_name', 'email', 'phone', 'company', 'job_title', 'address', 'notes'])) {
                    $updateData[$field] = htmlspecialchars(trim($data[$field]));
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }
        
        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }
        
        try {
            $this->db->update('contacts', $updateData, 'id = ?', [$id]);
            return ['success' => true, 'message' => 'Contact updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update contact'];
        }
    }
    
    public function delete($id) {
        try {
            $this->db->delete('contacts', 'id = ?', [$id]);
            return ['success' => true, 'message' => 'Contact deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete contact'];
        }
    }
    
    public function search($query, $filters = []) {
        $searchTerm = "%$query%";
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE (c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR 
                       c.phone LIKE ? OR c.company LIKE ? OR c.notes LIKE ?)";
        
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND c.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        $sql .= " ORDER BY c.last_name, c.first_name";
        
        $stmt = $this->db->query($sql, $params);
        $contacts = $stmt->fetchAll();
        
        foreach ($contacts as &$contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
        }
        
        // Save search history
        $this->saveSearchHistory($query, $filters, count($contacts));
        
        return $contacts;
    }
    
    private function isDuplicate($data) {
        $sql = "SELECT id FROM contacts WHERE (email = ? OR phone = ?) AND (email IS NOT NULL OR phone IS NOT NULL)";
        $stmt = $this->db->query($sql, [$data['email'] ?? '', $data['phone'] ?? '']);
        return $stmt->fetch() !== false;
    }
    
    private function saveSearchHistory($query, $filters, $resultsCount) {
        $searchData = [
            'query' => $query,
            'filters' => json_encode($filters),
            'results_count' => $resultsCount
        ];
        
        $this->db->insert('search_history', $searchData);
    }
}

// Category class
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
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
    
    return ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username']];
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Initialize database
$db = Database::getInstance();
$db->setupDatabase();

// Initialize classes
$contact = new Contact();
$category = new Category();

// Handle requests
$action = $_GET['action'] ?? 'home';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['form_type'] ?? '') {
        case 'add_contact':
            $result = $contact->create($_POST);
            
            if ($result['success']) {
                $message = 'Contact added successfully!';
                $action = 'home';
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'edit_contact':
            $result = $contact->update($_POST['contact_id'], $_POST);
            
            if ($result['success']) {
                $message = $result['message'];
                $action = 'home';
            } else {
                $error = $result['message'];
            }
            break;
            
        case 'delete_contact':
            $result = $contact->delete($_POST['contact_id']);
            
            if ($result['success']) {
                $message = $result['message'];
                $action = 'home';
            } else {
                $error = $result['message'];
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .contact-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .contact-card:hover {
            transform: translateY(-2px);
        }

        .contact-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .contact-info {
            margin-bottom: 5px;
            color: #666;
        }

        .contact-category {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 10px;
        }

        .contact-tags {
            margin-top: 10px;
        }

        .tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
        }

        .contact-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
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

            .contact-grid {
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
                    <li><a href="?action=add">Add Contact</a></li>
                    <li><a href="?action=export">Export</a></li>
                    <li><a href="?action=api">API</a></li>
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
            case 'add':
                include 'views/add.php';
                break;
            case 'edit':
                include 'views/edit.php';
                break;
            case 'view':
                include 'views/view.php';
                break;
            case 'export':
                include 'views/export.php';
                break;
            case 'api':
                include 'views/api.php';
                break;
            default:
                include 'views/home.php';
        }
        ?>
    </div>
</body>
</html>
