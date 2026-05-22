<?php
    // Exercise 3: User Management System with Authentication
    
    class UserManagementSystem {
        private $pdo;
        
        public function __construct($host, $dbname, $username, $password) {
            try {
                $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                                     $username, $password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        
        public function createTables() {
            try {
                // Create users table
                $sql = "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    first_name VARCHAR(100),
                    last_name VARCHAR(100),
                    phone VARCHAR(20),
                    date_of_birth DATE,
                    bio TEXT,
                    avatar VARCHAR(255),
                    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
                    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
            email_verified BOOLEAN DEFAULT FALSE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
                $this->pdo->exec($sql);
                
                // Create user_sessions table for login tracking
                $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    session_token VARCHAR(255) UNIQUE NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    expires_at TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";
                $this->pdo->exec($sql);
                
                // Create password_resets table
                $sql = "CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(255) UNIQUE NOT NULL,
                    expires_at TIMESTAMP NOT NULL,
                    used BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";
                $this->pdo->exec($sql);
                
                echo "User management tables created successfully!<br>";
            } catch (PDOException $e) {
                echo "Error creating tables: " . $e->getMessage() . "<br>";
            }
        }
        
        // Register new user
        public function register($username, $email, $password, $firstName, $lastName, $role = 'user') {
            try {
                // Validate input
                $this->validateRegistrationData($username, $email, $password, $firstName, $lastName);
                
                // Check if user already exists
                if ($this->userExists($username, $email)) {
                    return ['success' => false, 'message' => 'Username or email already exists'];
                }
                
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, role) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$username, $email, $passwordHash, $firstName, $lastName, $role]);
                
                $userId = $this->pdo->lastInsertId();
                
                return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
                
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Registration failed'];
            }
        }
        
        // Login user
        public function login($usernameOrEmail, $password, $rememberMe = false) {
            try {
                // Find user by username or email
                $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($password, $user['password_hash'])) {
                    return ['success' => false, 'message' => 'Invalid credentials'];
                }
                
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Create session
                $sessionToken = $this->createUserSession($user['id'], $rememberMe);
                
                // Remove sensitive data
                unset($user['password_hash']);
                
                return [
                    'success' => true, 
                    'message' => 'Login successful',
                    'user' => $user,
                    'session_token' => $sessionToken
                ];
                
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Login failed'];
            }
        }
        
        // Update user profile
        public function updateProfile($userId, $data) {
            try {
                $allowedFields = ['first_name', 'last_name', 'phone', 'date_of_birth', 'bio'];
                $updates = [];
                $params = [];
                
                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        $updates[] = "$field = ?";
                        $params[] = $data[$field];
                    }
                }
                
                if (empty($updates)) {
                    return ['success' => false, 'message' => 'No valid fields to update'];
                }
                
                $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
                $params[] = $userId;
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                
                return ['success' => true, 'message' => 'Profile updated successfully'];
                
            } catch (PDOException $e) {
                error_log("Profile update error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Profile update failed'];
            }
        }
        
        // Change password
        public function changePassword($userId, $currentPassword, $newPassword) {
            try {
                // Get current password hash
                $sql = "SELECT password_hash FROM users WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                    return ['success' => false, 'message' => 'Current password is incorrect'];
                }
                
                // Validate new password
                if (strlen($newPassword) < 8) {
                    return ['success' => false, 'message' => 'New password must be at least 8 characters'];
                }
                
                // Hash new password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password
                $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$newPasswordHash, $userId]);
                
                // Invalidate all sessions for this user
                $this->invalidateUserSessions($userId);
                
                return ['success' => true, 'message' => 'Password changed successfully'];
                
            } catch (PDOException $e) {
                error_log("Password change error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Password change failed'];
            }
        }
        
        // Get user by ID
        public function getUser($userId) {
            try {
                $sql = "SELECT id, username, email, first_name, last_name, phone, date_of_birth, 
                        bio, avatar, status, role, email_verified, last_login, created_at 
                        FROM users WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$userId]);
                return $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error getting user: " . $e->getMessage());
                return null;
            }
        }
        
        // Get all users with pagination
        public function getAllUsers($page = 1, $limit = 10, $status = null, $role = null) {
            try {
                $offset = ($page - 1) * $limit;
                
                $sql = "SELECT id, username, email, first_name, last_name, status, role, 
                        email_verified, last_login, created_at FROM users";
                $params = [];
                $whereConditions = [];
                
                if ($status) {
                    $whereConditions[] = "status = ?";
                    $params[] = $status;
                }
                
                if ($role) {
                    $whereConditions[] = "role = ?";
                    $params[] = $role;
                }
                
                if (!empty($whereConditions)) {
                    $sql .= " WHERE " . implode(' AND ', $whereConditions);
                }
                
                $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll();
                
            } catch (PDOException $e) {
                error_log("Error getting users: " . $e->getMessage());
                return [];
            }
        }
        
        // Update user status
        public function updateUserStatus($userId, $status) {
            try {
                $validStatuses = ['active', 'inactive', 'suspended'];
                if (!in_array($status, $validStatuses)) {
                    return false;
                }
                
                $sql = "UPDATE users SET status = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$status, $userId]);
                
                // If suspending, invalidate all sessions
                if ($status === 'suspended') {
                    $this->invalidateUserSessions($userId);
                }
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error updating user status: " . $e->getMessage());
                return false;
            }
        }
        
        // Delete user
        public function deleteUser($userId) {
            try {
                $this->pdo->beginTransaction();
                
                // Delete user sessions
                $sql = "DELETE FROM user_sessions WHERE user_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$userId]);
                
                // Delete user
                $sql = "DELETE FROM users WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$userId]);
                
                $this->pdo->commit();
                return $stmt->rowCount() > 0;
                
            } catch (PDOException $e) {
                $this->pdo->rollBack();
                error_log("Error deleting user: " . $e->getMessage());
                return false;
            }
        }
        
        // Search users
        public function searchUsers($query, $page = 1, $limit = 10) {
            try {
                $offset = ($page - 1) * $limit;
                $searchTerm = "%$query%";
                
                $sql = "SELECT id, username, email, first_name, last_name, status, role, created_at 
                        FROM users 
                        WHERE username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?
                        ORDER BY username ASC LIMIT ? OFFSET ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
                return $stmt->fetchAll();
                
            } catch (PDOException $e) {
                error_log("Error searching users: " . $e->getMessage());
                return [];
            }
        }
        
        // Get user statistics
        public function getUserStatistics() {
            try {
                $stats = [];
                
                // Total users
                $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
                $stats['total_users'] = $stmt->fetch()['total'];
                
                // Users by status
                $stmt = $this->pdo->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
                $stats['by_status'] = $stmt->fetchAll();
                
                // Users by role
                $stmt = $this->pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
                $stats['by_role'] = $stmt->fetchAll();
                
                // Recent registrations (last 30 days)
                $stmt = $this->pdo->query("SELECT COUNT(*) as recent FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
                $stats['recent_registrations'] = $stmt->fetch()['recent'];
                
                // Active sessions
                $stmt = $this->pdo->query("SELECT COUNT(DISTINCT user_id) as active_sessions FROM user_sessions WHERE expires_at > NOW()");
                $stats['active_sessions'] = $stmt->fetch()['active_sessions'];
                
                return $stats;
            } catch (PDOException $e) {
                error_log("Error getting user statistics: " . $e->getMessage());
                return [];
            }
        }
        
        // Private helper methods
        private function validateRegistrationData($username, $email, $password, $firstName, $lastName) {
            if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
                throw new Exception('All fields are required');
            }
            
            if (strlen($username) < 3 || strlen($username) > 50) {
                throw new Exception('Username must be between 3 and 50 characters');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            if (strlen($password) < 8) {
                throw new Exception('Password must be at least 8 characters');
            }
            
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
                throw new Exception('Password must contain uppercase, lowercase, and number');
            }
        }
        
        private function userExists($username, $email) {
            $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $email]);
            return $stmt->fetch() !== false;
        }
        
        private function updateLastLogin($userId) {
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
        }
        
        private function createUserSession($userId, $rememberMe = false) {
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = $rememberMe ? 
                date('Y-m-d H:i:s', strtotime('+30 days')) : 
                date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $sql = "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $sessionToken, $ipAddress, $userAgent, $expiresAt]);
            
            return $sessionToken;
        }
        
        private function invalidateUserSessions($userId) {
            $sql = "DELETE FROM user_sessions WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
        }
    }
    
    // Demo
    $userSystem = new UserManagementSystem("localhost", "test_db", "root", "");
    
    echo "<h2>User Management System Demo</h2>";
    
    // Create tables
    $userSystem->createTables();
    echo "<br>";
    
    // Register users
    echo "<h3>Registering Users:</h3>";
    
    $users = [
        [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'user'
        ],
        [
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'role' => 'admin'
        ],
        [
            'username' => 'bobwilson',
            'email' => 'bob@example.com',
            'password' => 'Password123',
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'role' => 'moderator'
        ]
    ];
    
    $userIds = [];
    foreach ($users as $user) {
        $result = $userSystem->register(
            $user['username'], $user['email'], $user['password'],
            $user['first_name'], $user['last_name'], $user['role']
        );
        
        if ($result['success']) {
            echo "✅ Registered {$user['username']} (ID: {$result['user_id']})<br>";
            $userIds[] = $result['user_id'];
        } else {
            echo "❌ Failed to register {$user['username']}: {$result['message']}<br>";
        }
    }
    echo "<br>";
    
    // Test login
    echo "<h3>Testing Login:</h3>";
    $loginResult = $userSystem->login('johndoe', 'Password123');
    if ($loginResult['success']) {
        echo "✅ Login successful for {$loginResult['user']['username']}<br>";
        echo "Session token: " . substr($loginResult['session_token'], 0, 20) . "...<br>";
    } else {
        echo "❌ Login failed: {$loginResult['message']}<br>";
    }
    
    // Test invalid login
    $invalidLogin = $userSystem->login('johndoe', 'wrongpassword');
    echo "Invalid login test: " . ($invalidLogin['success'] ? "❌ Failed" : "✅ Passed") . "<br>";
    echo "<br>";
    
    // Display all users
    echo "<h3>All Users:</h3>";
    $allUsers = $userSystem->getAllUsers();
    if ($allUsers) {
        foreach ($allUsers as $user) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<strong>" . htmlspecialchars($user['username']) . "</strong> (" . htmlspecialchars($user['email']) . ")<br>";
            echo "Name: " . htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']) . "<br>";
            echo "Role: <span style='color: " . 
                 ($user['role'] == 'admin' ? 'red' : ($user['role'] == 'moderator' ? 'orange' : 'blue') . "'>" . 
                 ucfirst($user['role']) . "</span><br>";
            echo "Status: <span style='color: " . 
                 ($user['status'] == 'active' ? 'green' : 'red') . "'>" . 
                 ucfirst($user['status']) . "</span><br>";
            echo "Last Login: " . ($user['last_login'] ?? 'Never') . "<br>";
            echo "Registered: " . $user['created_at'] . "<br>";
            echo "</div>";
        }
    }
    echo "<br>";
    
    // Update user profile
    echo "<h3>Updating User Profile:</h3>";
    if (!empty($userIds)) {
        $profileData = [
            'phone' => '555-1234',
            'date_of_birth' => '1990-01-01',
            'bio' => 'Software developer with expertise in PHP and web technologies.'
        ];
        
        $updateResult = $userSystem->updateProfile($userIds[0], $profileData);
        echo "Profile update: " . ($updateResult['success'] ? "✅ Success" : "❌ Failed") . "<br>";
        
        // Display updated user
        $updatedUser = $userSystem->getUser($userIds[0]);
        if ($updatedUser) {
            echo "Updated profile for {$updatedUser['username']}:<br>";
            echo "- Phone: " . htmlspecialchars($updatedUser['phone'] ?? 'Not set') . "<br>";
            echo "- Bio: " . htmlspecialchars($updatedUser['bio'] ?? 'Not set') . "<br>";
        }
    }
    echo "<br>";
    
    // Test password change
    echo "<h3>Testing Password Change:</h3>";
    if (!empty($userIds)) {
        $passwordResult = $userSystem->changePassword($userIds[0], 'Password123', 'NewPassword123');
        echo "Password change: " . ($passwordResult['success'] ? "✅ Success" : "❌ Failed") . "<br>";
        
        // Test login with new password
        $newLoginResult = $userSystem->login('johndoe', 'NewPassword123');
        echo "Login with new password: " . ($newLoginResult['success'] ? "✅ Success" : "❌ Failed") . "<br>";
    }
    echo "<br>";
    
    // Search users
    echo "<h3>Searching Users:</h3>";
    $searchResults = $userSystem->searchUsers('john');
    echo "Found " . count($searchResults) . " users matching 'john':<br>";
    foreach ($searchResults as $result) {
        echo "- " . htmlspecialchars($result['username']) . " (" . htmlspecialchars($result['email']) . ")<br>";
    }
    echo "<br>";
    
    // Display statistics
    echo "<h3>User Management Statistics:</h3>";
    $stats = $userSystem->getUserStatistics();
    echo "Total Users: " . $stats['total_users'] . "<br>";
    echo "Recent Registrations (30 days): " . $stats['recent_registrations'] . "<br>";
    echo "Active Sessions: " . $stats['active_sessions'] . "<br>";
    
    echo "<br><strong>Users by Status:</strong><br>";
    foreach ($stats['by_status'] as $status) {
        $color = $status['status'] == 'active' ? 'green' : 'red';
        echo "- <span style='color: $color'>" . ucfirst($status['status']) . "</span>: " . $status['count'] . "<br>";
    }
    
    echo "<br><strong>Users by Role:</strong><br>";
    foreach ($stats['by_role'] as $role) {
        $color = $role['role'] == 'admin' ? 'red' : ($role['role'] == 'moderator' ? 'orange' : 'blue');
        echo "- <span style='color: $color'>" . ucfirst($role['role']) . "</span>: " . $role['count'] . "<br>";
    }
    echo "<br>";
    
    // Test user status update
    echo "<h3>Testing User Status Update:</h3>";
    if (!empty($userIds)) {
        $statusUpdated = $userSystem->updateUserStatus($userIds[1], 'inactive');
        echo "Status update to 'inactive': " . ($statusUpdated ? "✅ Success" : "❌ Failed") . "<br>";
        
        // Verify status change
        $updatedUser = $userSystem->getUser($userIds[1]);
        echo "Current status: " . ucfirst($updatedUser['status']) . "<br>";
    }
    echo "<br>";
    
    // Test validation
    echo "<h3>Testing Input Validation:</h3>";
    
    // Test weak password
    $result = $userSystem->register('testuser', 'test@example.com', 'weak', 'Test', 'User');
    echo "Weak password test: " . ($result['success'] ? "❌ Failed" : "✅ Passed") . "<br>";
    
    // Test invalid email
    $result = $userSystem->register('testuser2', 'invalid-email', 'Password123', 'Test', 'User');
    echo "Invalid email test: " . ($result['success'] ? "❌ Failed" : "✅ Passed") . "<br>";
    
    // Test short username
    $result = $userSystem->register('ab', 'test2@example.com', 'Password123', 'Test', 'User');
    echo "Short username test: " . ($result['success'] ? "❌ Failed" : "✅ Passed") . "<br>";
    echo "<br>";
    
    echo "<h3>User Management System Features Demonstrated:</h3>";
    echo "✅ Secure user registration with validation<br>";
    echo "✅ Password hashing and verification<br>";
    echo "✅ User authentication with session management<br>";
    echo "✅ Profile management<br>";
    echo "✅ Secure password change functionality<br>";
    echo "✅ User role and status management<br>";
    echo "✅ Search functionality<br>";
    echo "✅ User statistics and reporting<br>";
    echo "✅ Input validation and sanitization<br>";
    echo "✅ SQL injection prevention with prepared statements<br>";
    echo "✅ Session tracking and security<br>";
    echo "✅ Database transactions for data integrity<br>";
?>
