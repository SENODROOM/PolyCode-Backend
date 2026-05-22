# Project 2: Blog System 📰

A full-featured blog system with user authentication, database integration, and content management capabilities.

## 🎯 Learning Objectives

After completing this project, you will:
- Build a complete CRUD application with database
- Implement user authentication and authorization
- Handle file uploads for images
- Create search and filtering functionality
- Build an admin panel for content management
- Understand MVC pattern basics
- Implement security best practices

## 🛠️ Features

### User Features
- ✅ User registration and login
- ✅ Password hashing and verification
- ✅ User profiles and account management
- ✅ Session-based authentication

### Blog Features
- ✅ Create, edit, delete blog posts
- ✅ Rich text editor for content
- ✅ Image upload for post thumbnails
- ✅ Categories and tags system
- ✅ Post scheduling and status management
- ✅ Search functionality
- ✅ Comment system

### Admin Features
- ✅ Admin dashboard
- ✅ User management
- ✅ Content moderation
- ✅ Statistics and analytics
- ✅ System settings

### Technical Features
- ✅ Database integration with PDO
- ✅ Prepared statements for security
- ✅ Input validation and sanitization
- ✅ File upload handling
- ✅ Pagination for large datasets
- ✅ Responsive design

## 📁 Project Structure

```
blog-system/
├── README.md           # This file
├── index.php          # Main entry point
├── config/
│   ├── database.php   # Database configuration
│   └── config.php     # Application config
├── includes/
│   ├── functions.php  # Helper functions
│   ├── auth.php       # Authentication functions
│   └── header.php     # Header template
├── classes/
│   ├── User.php       # User class
│   ├── Blog.php        # Blog post class
│   └── Database.php   # Database handler
├── admin/
│   ├── index.php      # Admin dashboard
│   ├── users.php      # User management
│   └── posts.php      # Post management
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   ├── js/
│   │   └── script.js  # JavaScript
│   └── uploads/       # Image uploads
└── database/
    └── setup.sql      # Database schema
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache, Nginx)
- GD extension for image processing

### Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE blog_system;
   ```

2. **Import Schema**
   Run the SQL commands from `database/setup.sql` or execute them manually.

### Configuration

1. **Database Configuration**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'blog_system');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

2. **File Permissions**
   Ensure the `assets/uploads/` directory is writable:
   ```bash
   chmod 755 assets/uploads/
   ```

### Running the Application

1. **Navigate to project directory**
   ```bash
   cd php-learning-guide/08-projects/blog-system
   ```

2. **Start PHP server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application**
   - Main site: `http://localhost:8000`
   - Admin panel: `http://localhost:8000/admin`

## 📖 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    bio TEXT,
    avatar VARCHAR(255),
    role ENUM('user', 'admin', 'editor') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Posts Table
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    author_id INT NOT NULL,
    category_id INT,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published', 'scheduled', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
```

### Categories Table
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Comments Table
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NULL,
    author_name VARCHAR(100),
    author_email VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

## 🔧 Core Classes

### Database Class
```php
<?php
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
        
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
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
}
?>
```

### User Class
```php
<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function register($username, $email, $password, $firstName, $lastName) {
        // Validate input
        if (!$this->validateRegistration($username, $email, $password)) {
            return false;
        }
        
        // Check if user exists
        if ($this->userExists($username, $email)) {
            return false;
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $data = [
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
        
        return $this->db->insert('users', $data);
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
        $stmt = $this->db->query($sql, [$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login
            $this->db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            return $user;
        }
        
        return false;
    }
    
    public function getById($id) {
        $sql = "SELECT id, username, email, first_name, last_name, bio, avatar, role, created_at 
                FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function updateProfile($id, $data) {
        $allowedFields = ['first_name', 'last_name', 'bio'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return false;
        }
        
        return $this->db->update('users', $updateData, 'id = ?', [$id]);
    }
    
    private function validateRegistration($username, $email, $password) {
        // Username validation
        if (strlen($username) < 3 || strlen($username) > 50) {
            return false;
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Password validation
        if (strlen($password) < 8) {
            return false;
        }
        
        return true;
    }
    
    private function userExists($username, $email) {
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->db->query($sql, [$username, $email]);
        return $stmt->fetch() !== false;
    }
}
?>
```

### Blog Post Class
```php
<?php
class BlogPost {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($title, $content, $authorId, $categoryId = null, $status = 'draft') {
        $slug = $this->generateSlug($title);
        $excerpt = $this->generateExcerpt($content);
        
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'author_id' => $authorId,
            'category_id' => $categoryId,
            'status' => $status
        ];
        
        if ($status === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert('posts', $data);
    }
    
    public function getAll($status = 'published', $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, u.username as author_name, c.name as category_name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = ?
                ORDER BY p.published_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$status, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getBySlug($slug) {
        $sql = "SELECT p.*, u.username as author_name, u.first_name, u.last_name, c.name as category_name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.status = 'published'";
        
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $allowedFields = ['title', 'content', 'category_id', 'status', 'featured_image'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (isset($data['title'])) {
            $updateData['slug'] = $this->generateSlug($data['title']);
        }
        
        if (isset($data['content'])) {
            $updateData['excerpt'] = $this->generateExcerpt($data['content']);
        }
        
        if (isset($data['status']) && $data['status'] === 'published') {
            $updateData['published_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update('posts', $updateData, 'id = ?', [$id]);
    }
    
    public function delete($id) {
        return $this->db->delete('posts', 'id = ?', [$id]);
    }
    
    public function search($query, $limit = 10) {
        $searchTerm = "%$query%";
        $sql = "SELECT p.*, u.username as author_name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published' AND 
                      (p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
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
    
    private function slugExists($slug) {
        $sql = "SELECT id FROM posts WHERE slug = ?";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt->fetch() !== false;
    }
}
?>
```

## 🔐 Authentication System

### Login Function
```php
<?php
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
        header('Location: login.php');
        exit;
    }
}

function requireRole($role) {
    if (!isLoggedIn() || $_SESSION['role'] !== $role) {
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
?>
```

## 📤 File Upload Handler

### Image Upload Function
```php
<?php
function uploadImage($file, $maxSize = 2097152, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $extension;
    $uploadPath = 'assets/uploads/' . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir('assets/uploads')) {
        mkdir('assets/uploads', 0755, true);
    }
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}
?>
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Add Comments**: Implement comment system
2. **Tag System**: Add tags to posts
3. **RSS Feed**: Create RSS feed for blog
4. **Contact Form**: Add contact page

### Intermediate Challenges
1. **Email Notifications**: Send email for new comments
2. **Social Sharing**: Add social media sharing buttons
3. **Related Posts**: Show related posts based on categories
4. **Analytics Dashboard**: Track page views and user engagement

### Advanced Challenges
1. **API Endpoints**: Create REST API for blog
2. **Caching System**: Implement caching for performance
3. **Multi-language Support**: Add internationalization
4. **Real-time Comments**: Use WebSockets for live comments

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] User registration and login
- [ ] Create, edit, delete posts
- [ ] Image upload functionality
- [ ] Search functionality
- [ ] Admin panel access
- [ ] Comment system
- [ ] Responsive design
- [ ] Security measures

### Security Testing
- [ ] SQL injection prevention
- [ ] XSS protection
- [ ] File upload security
- [ ] Session security
- [ ] Input validation

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ Database design and operations
- ✅ User authentication systems
- ✅ File upload handling
- ✅ MVC pattern implementation
- ✅ Security best practices
- ✅ Advanced PHP features
- ✅ Error handling and validation
- ✅ Session management
- ✅ Object-oriented programming

## 🚀 Next Steps

1. **Enhance Features**: Implement challenges above
2. **Add Framework**: Try Laravel or Symfony
3. **Create API**: Build REST API endpoints
4. **Deploy**: Deploy to production server
5. **Optimize**: Add caching and performance improvements

---

**Ready for the next project?** ➡️ [E-commerce Cart](../ecommerce-cart/README.md)
