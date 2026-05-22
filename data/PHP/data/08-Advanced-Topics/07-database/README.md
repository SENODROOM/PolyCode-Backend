# Module 7: Database Connectivity 🗄️

Database connectivity is essential for most web applications. PHP provides excellent support for connecting to various database systems, with MySQL/MariaDB being the most popular choice.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand database concepts and SQL basics
- Connect PHP to MySQL/MariaDB databases
- Perform CRUD operations (Create, Read, Update, Delete)
- Use prepared statements for security
- Handle database errors effectively
- Implement database best practices

## 📝 Topics Covered

1. [Database Basics](#database-basics)
2. [MySQLi Extension](#mysqli-extension)
3. [PDO (PHP Data Objects)]#pdo-php-data-objects)
4. [CRUD Operations](#crud-operations)
5. [Prepared Statements](#prepared-statements)
6. [Error Handling](#error-handling)
7. [Database Security](#database-security)
8. [Practical Examples](#practical-examples)
9. [Exercises](#exercises)

---

## Database Basics

### What is a Database?
A database is an organized collection of structured information, or data, typically stored electronically in a computer system.

### Common Database Types
- **MySQL/MariaDB**: Most popular with PHP
- **PostgreSQL**: Advanced open-source database
- **SQLite**: File-based database for small applications
- **SQL Server**: Microsoft's database system

### Basic SQL Concepts
```sql
-- Create table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data
INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com');

-- Select data
SELECT * FROM users WHERE email = 'john@example.com';

-- Update data
UPDATE users SET name = 'Jane Doe' WHERE id = 1;

-- Delete data
DELETE FROM users WHERE id = 1;
```

---

## MySQLi Extension

### Connecting to Database
```php
<?php
    // Connection parameters
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "test_db";
    
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully!";
    
    // Close connection
    $conn->close();
?>
```

### Procedural Style
```php
<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "test_db";
    
    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);
    
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    echo "Connected successfully!";
    
    // Close connection
    mysqli_close($conn);
?>
```

### Creating Tables
```php
<?php
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // SQL to create table
    $sql = "CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        age INT(3),
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table users created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $conn->close();
?>
```

---

## PDO (PHP Data Objects)

### PDO Connection
```php
<?php
    $host = "localhost";
    $dbname = "test_db";
    $username = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully!";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>
```

### PDO with Different Databases
```php
<?php
    // MySQL
    $pdo = new PDO("mysql:host=localhost;dbname=test", "user", "pass");
    
    // PostgreSQL
    $pdo = new PDO("pgsql:host=localhost;dbname=test", "user", "pass");
    
    // SQLite
    $pdo = new PDO("sqlite:database.sqlite");
    
    // SQL Server
    $pdo = new PDO("sqlsrv:Server=localhost;Database=test", "user", "pass");
?>
```

### PDO Configuration Options
```php
<?php
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ];
    
    $pdo = new PDO("mysql:host=localhost;dbname=test", "user", "pass", $options);
?>
```

---

## CRUD Operations

### Create (Insert)
```php
<?php
    // MySQLi - Object Oriented
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    $name = "John Doe";
    $email = "john@example.com";
    $age = 25;
    
    $sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $age);
    
    if ($stmt->execute()) {
        echo "New record created successfully. ID: " . $conn->insert_id;
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
?>
```

```php
<?php
    // PDO
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $name = "John Doe";
    $email = "john@example.com";
    $age = 25;
    
    $sql = "INSERT INTO users (name, email, age) VALUES (:name, :email, :age)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':age', $age);
    
    $stmt->execute();
    echo "New record created successfully. ID: " . $pdo->lastInsertId();
?>
```

### Read (Select)
```php
<?php
    // MySQLi - Object Oriented
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    $sql = "SELECT id, name, email, age FROM users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["email"] . "</td><td>" . $row["age"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    
    $conn->close();
?>
```

```php
<?php
    // PDO
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT id, name, email, age FROM users";
    $stmt = $pdo->query($sql);
    
    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th></tr>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["email"] . "</td><td>" . $row["age"] . "</td></tr>";
    }
    echo "</table>";
?>
```

### Update
```php
<?php
    // MySQLi
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    $id = 1;
    $newEmail = "newemail@example.com";
    
    $sql = "UPDATE users SET email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newEmail, $id);
    
    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
?>
```

```php
<?php
    // PDO
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = 1;
    $newEmail = "newemail@example.com";
    
    $sql = "UPDATE users SET email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $newEmail);
    $stmt->bindParam(':id', $id);
    
    $stmt->execute();
    echo "Record updated successfully";
?>
```

### Delete
```php
<?php
    // MySQLi
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    $id = 1;
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
?>
```

```php
<?php
    // PDO
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = 1;
    
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    
    $stmt->execute();
    echo "Record deleted successfully";
?>
```

---

## Prepared Statements

### Why Use Prepared Statements?
- **Security**: Prevents SQL injection attacks
- **Performance**: Better for repeated queries
- **Clarity**: Separates SQL logic from data

### MySQLi Prepared Statements
```php
<?php
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    // Insert multiple records
    $users = [
        ["Alice", "alice@example.com", 28],
        ["Bob", "bob@example.com", 32],
        ["Charlie", "charlie@example.com", 24]
    ];
    
    $sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($users as $user) {
        $stmt->bind_param("ssi", $user[0], $user[1], $user[2]);
        $stmt->execute();
    }
    
    echo "Records inserted successfully";
    
    $stmt->close();
    $conn->close();
?>
```

### PDO Prepared Statements
```php
<?php
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Search with parameters
    $searchTerm = "john";
    $minAge = 25;
    
    $sql = "SELECT * FROM users WHERE name LIKE :search AND age >= :minAge";
    $stmt = $pdo->prepare($sql);
    
    $searchPattern = "%$searchTerm%";
    $stmt->bindParam(':search', $searchPattern);
    $stmt->bindParam(':minAge', $minAge);
    
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        echo "Name: " . $row['name'] . ", Email: " . $row['email'] . "<br>";
    }
?>
```

### Batch Operations
```php
<?php
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Insert multiple records
        $sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $users = [
            ["David", "david@example.com", 30],
            ["Emma", "emma@example.com", 26],
            ["Frank", "frank@example.com", 35]
        ];
        
        foreach ($users as $user) {
            $stmt->execute($user);
        }
        
        // Commit transaction
        $pdo->commit();
        echo "All records inserted successfully";
        
    } catch (PDOException $e) {
        // Rollback on error
        $pdo->rollback();
        echo "Error: " . $e->getMessage();
    }
?>
```

---

## Error Handling

### MySQLi Error Handling
```php
<?php
    $conn = new mysqli("localhost", "root", "", "test_db");
    
    // Set error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $sql = "SELECT * FROM non_existent_table";
        $result = $conn->query($sql);
        
        while ($row = $result->fetch_assoc()) {
            echo $row['name'];
        }
        
    } catch (mysqli_sql_exception $e) {
        echo "Database error: " . $e->getMessage();
        // Log error for debugging
        error_log("Database error: " . $e->getMessage());
    }
    
    $conn->close();
?>
```

### PDO Error Handling
```php
<?php
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => 999]);
        
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        echo "User found: " . $user['name'];
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
        
    } catch (Exception $e) {
        echo "Application error: " . $e->getMessage();
        error_log("Application error: " . $e->getMessage());
    }
?>
```

### Custom Error Handler
```php
<?php
    class Database {
        private $pdo;
        
        public function __construct($host, $dbname, $username, $password) {
            try {
                $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }
        
        public function query($sql, $params = []) {
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt;
            } catch (PDOException $e) {
                $this->handleError($e);
                return false;
            }
        }
        
        private function handleError($exception) {
            // Log detailed error for debugging
            error_log("Database Error: " . $exception->getMessage());
            
            // Show user-friendly message
            echo "A database error occurred. Please try again later.";
            
            // In production, you might want to redirect to an error page
            // header("Location: /error.php");
            // exit();
        }
    }
    
    $db = new Database("localhost", "test_db", "root", "");
    $result = $db->query("SELECT * FROM users");
?>
```

---

## Database Security

### SQL Injection Prevention
```php
<?php
    // ❌ VULNERABLE - Don't do this!
    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'"; // DANGEROUS!
    
    // ✅ SECURE - Use prepared statements
    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
?>
```

### Input Validation
```php
<?php
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    // Usage
    $email = $_POST['email'] ?? '';
    
    if (!validateEmail($email)) {
        die("Invalid email format");
    }
    
    $cleanEmail = sanitizeInput($email);
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cleanEmail]);
?>
```

### Password Security
```php
<?php
    // Hash password before storing
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Store hashed password in database
    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $hashedPassword]);
    
    // Verify password during login
    $sql = "SELECT password FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        echo "Login successful";
    } else {
        echo "Invalid credentials";
    }
?>
```

### Database Connection Security
```php
<?php
    // Store credentials in environment variables or config file
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'test_db';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';
    
    // Use SSL if available
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/path/to/ca.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                      $username, $password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Don't expose detailed errors to users
        error_log("Connection failed: " . $e->getMessage());
        die("Database connection failed");
    }
?>
```

---

## Practical Examples

### Example 1: User Registration System
```php
<?php
    class UserSystem {
        private $pdo;
        
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function register($name, $email, $password) {
            // Validate input
            if (empty($name) || empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'All fields are required'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            if (strlen($password) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters'];
            }
            
            try {
                // Check if email already exists
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Email already registered'];
                }
                
                // Hash password and insert user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $this->pdo->prepare(
                    "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())"
                );
                $stmt->execute([$name, $email, $hashedPassword]);
                
                return ['success' => true, 'message' => 'Registration successful'];
                
            } catch (PDOException $e) {
                error_log("Registration error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Registration failed'];
            }
        }
        
        public function login($email, $password) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    unset($user['password']); // Remove password from result
                    return ['success' => true, 'user' => $user];
                }
                
                return ['success' => false, 'message' => 'Invalid credentials'];
                
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Login failed'];
            }
        }
    }
    
    // Usage
    $pdo = new PDO("mysql:host=localhost;dbname=test", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $userSystem = new UserSystem($pdo);
    
    // Register user
    $result = $userSystem->register("John Doe", "john@example.com", "password123");
    echo $result['message'];
?>
```

---

## Exercises

### Exercise 1: Simple Blog System
Create a PHP file that:
1. Sets up a database connection
2. Creates tables for posts and comments
3. Implements CRUD operations for blog posts
4. Uses prepared statements

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: Contact Form with Database
Create a PHP file that:
1. Saves contact form submissions to database
2. Displays submitted messages
3. Includes search functionality
4. Implements proper validation

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: User Management System
Create a PHP file that:
1. Implements user authentication
2. Manages user profiles
3. Handles password updates
4. Uses secure practices

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand database concepts and SQL basics
- [ ] I can connect PHP to databases using MySQLi and PDO
- [ ] I can perform CRUD operations
- [ ] I understand and use prepared statements
- [ ] I can handle database errors
- [ ] I understand database security best practices
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 8: Mini Projects](../08-projects/README.md)
