<?php
    // Exercise 2: Contact Form with Database Storage
    
    class ContactFormSystem {
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
        
        public function createTable() {
            try {
                $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    subject VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
                    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $this->pdo->exec($sql);
                echo "Contact messages table created successfully!<br>";
            } catch (PDOException $e) {
                echo "Error creating table: " . $e->getMessage() . "<br>";
            }
        }
        
        // Save contact form submission
        public function saveMessage($name, $email, $phone, $subject, $message, $priority = 'medium') {
            try {
                // Validate input
                if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                    return ['success' => false, 'message' => 'All required fields must be filled'];
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return ['success' => false, 'message' => 'Invalid email address'];
                }
                
                if (strlen($message) < 10) {
                    return ['success' => false, 'message' => 'Message must be at least 10 characters'];
                }
                
                // Sanitize input
                $name = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8');
                $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
                $phone = htmlspecialchars(trim($phone), ENT_QUOTES, 'UTF-8');
                $subject = htmlspecialchars(trim($subject), ENT_QUOTES, 'UTF-8');
                $message = htmlspecialchars(trim($message), ENT_QUOTES, 'UTF-8');
                
                // Get client info
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                
                $sql = "INSERT INTO contact_messages 
                        (name, email, phone, subject, message, priority, ip_address, user_agent) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$name, $email, $phone, $subject, $message, $priority, $ipAddress, $userAgent]);
                
                return ['success' => true, 'message' => 'Message saved successfully', 'id' => $this->pdo->lastInsertId()];
                
            } catch (PDOException $e) {
                error_log("Error saving message: " . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to save message'];
            }
        }
        
        // Get all messages with pagination
        public function getAllMessages($page = 1, $limit = 10, $status = null) {
            try {
                $offset = ($page - 1) * $limit;
                
                $sql = "SELECT * FROM contact_messages";
                $params = [];
                
                if ($status) {
                    $sql .= " WHERE status = ?";
                    $params[] = $status;
                }
                
                $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll();
                
            } catch (PDOException $e) {
                error_log("Error getting messages: " . $e->getMessage());
                return [];
            }
        }
        
        // Get single message
        public function getMessage($id) {
            try {
                $sql = "SELECT * FROM contact_messages WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                return $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error getting message: " . $e->getMessage());
                return null;
            }
        }
        
        // Update message status
        public function updateStatus($id, $status) {
            try {
                $validStatuses = ['new', 'read', 'replied', 'archived'];
                if (!in_array($status, $validStatuses)) {
                    return false;
                }
                
                $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$status, $id]);
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error updating status: " . $e->getMessage());
                return false;
            }
        }
        
        // Update message priority
        public function updatePriority($id, $priority) {
            try {
                $validPriorities = ['low', 'medium', 'high'];
                if (!in_array($priority, $validPriorities)) {
                    return false;
                }
                
                $sql = "UPDATE contact_messages SET priority = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$priority, $id]);
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error updating priority: " . $e->getMessage());
                return false;
            }
        }
        
        // Delete message
        public function deleteMessage($id) {
            try {
                $sql = "DELETE FROM contact_messages WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error deleting message: " . $e->getMessage());
                return false;
            }
        }
        
        // Search messages
        public function searchMessages($query, $page = 1, $limit = 10) {
            try {
                $offset = ($page - 1) * $limit;
                $searchTerm = "%$query%";
                
                $sql = "SELECT * FROM contact_messages 
                        WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?
                        ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
                return $stmt->fetchAll();
                
            } catch (PDOException $e) {
                error_log("Error searching messages: " . $e->getMessage());
                return [];
            }
        }
        
        // Get message count
        public function getMessageCount($status = null) {
            try {
                $sql = "SELECT COUNT(*) as count FROM contact_messages";
                $params = [];
                
                if ($status) {
                    $sql .= " WHERE status = ?";
                    $params[] = $status;
                }
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetch()['count'];
            } catch (PDOException $e) {
                error_log("Error getting message count: " . $e->getMessage());
                return 0;
            }
        }
        
        // Get statistics
        public function getStatistics() {
            try {
                $stats = [];
                
                // Total messages
                $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM contact_messages");
                $stats['total_messages'] = $stmt->fetch()['total'];
                
                // Messages by status
                $stmt = $this->pdo->query("SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status");
                $stats['by_status'] = $stmt->fetchAll();
                
                // Messages by priority
                $stmt = $this->pdo->query("SELECT priority, COUNT(*) as count FROM contact_messages GROUP BY priority");
                $stats['by_priority'] = $stmt->fetchAll();
                
                // Messages in last 7 days
                $stmt = $this->pdo->query("SELECT COUNT(*) as recent FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                $stats['recent_messages'] = $stmt->fetch()['recent'];
                
                return $stats;
            } catch (PDOException $e) {
                error_log("Error getting statistics: " . $e->getMessage());
                return [];
            }
        }
        
        // Get messages by date range
        public function getMessagesByDateRange($startDate, $endDate) {
            try {
                $sql = "SELECT * FROM contact_messages 
                        WHERE created_at BETWEEN ? AND ? 
                        ORDER BY created_at DESC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$startDate, $endDate]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error getting messages by date range: " . $e->getMessage());
                return [];
            }
        }
    }
    
    // Demo
    $contactSystem = new ContactFormSystem("localhost", "test_db", "root", "");
    
    echo "<h2>Contact Form System Demo</h2>";
    
    // Create table
    $contactSystem->createTable();
    echo "<br>";
    
    // Simulate form submissions
    echo "<h3>Simulating Contact Form Submissions:</h3>";
    
    $messages = [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
            'subject' => 'Product Inquiry',
            'message' => 'I am interested in your products. Can you send me more information?',
            'priority' => 'high'
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '555-5678',
            'subject' => 'Technical Support',
            'message' => 'I am having trouble with my account. The login page is not working properly.',
            'priority' => 'medium'
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'phone' => '',
            'subject' => 'General Question',
            'message' => 'What are your business hours?',
            'priority' => 'low'
        ],
        [
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'phone' => '555-9012',
            'subject' => 'Feedback',
            'message' => 'I love your service! It has been very helpful for my business.',
            'priority' => 'medium'
        ]
    ];
    
    $savedIds = [];
    foreach ($messages as $msg) {
        $result = $contactSystem->saveMessage(
            $msg['name'], $msg['email'], $msg['phone'], 
            $msg['subject'], $msg['message'], $msg['priority']
        );
        
        if ($result['success']) {
            echo "✅ Saved message from {$msg['name']} (ID: {$result['id']})<br>";
            $savedIds[] = $result['id'];
        } else {
            echo "❌ Failed to save message from {$msg['name']}: {$result['message']}<br>";
        }
    }
    echo "<br>";
    
    // Display all messages
    echo "<h3>All Messages:</h3>";
    $allMessages = $contactSystem->getAllMessages();
    if ($allMessages) {
        foreach ($allMessages as $msg) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<strong>From:</strong> " . htmlspecialchars($msg['name']) . " (" . htmlspecialchars($msg['email']) . ")<br>";
            echo "<strong>Subject:</strong> " . htmlspecialchars($msg['subject']) . "<br>";
            echo "<strong>Priority:</strong> <span style='color: " . 
                 ($msg['priority'] == 'high' ? 'red' : ($msg['priority'] == 'medium' ? 'orange' : 'green')) . "'>" . 
                 ucfirst($msg['priority']) . "</span><br>";
            echo "<strong>Status:</strong> " . ucfirst($msg['status']) . "<br>";
            echo "<strong>Message:</strong> " . htmlspecialchars(substr($msg['message'], 0, 100)) . "...<br>";
            echo "<small>Received: " . $msg['created_at'] . "</small>";
            echo "</div>";
        }
    } else {
        echo "No messages found.<br>";
    }
    echo "<br>";
    
    // Update message status
    echo "<h3>Updating Message Status:</h3>";
    if (!empty($savedIds)) {
        $firstId = $savedIds[0];
        $updated = $contactSystem->updateStatus($firstId, 'read');
        echo "Updated first message status to 'read': " . ($updated ? "✅ Success" : "❌ Failed") . "<br>";
        
        $secondId = $savedIds[1] ?? $firstId;
        $updated = $contactSystem->updateStatus($secondId, 'replied');
        echo "Updated second message status to 'replied': " . ($updated ? "✅ Success" : "❌ Failed") . "<br>";
    }
    echo "<br>";
    
    // Search functionality
    echo "<h3>Search Messages:</h3>";
    $searchResults = $contactSystem->searchMessages('product');
    echo "Found " . count($searchResults) . " messages matching 'product':<br>";
    foreach ($searchResults as $result) {
        echo "- " . htmlspecialchars($result['subject']) . " (from " . htmlspecialchars($result['name']) . ")<br>";
    }
    echo "<br>";
    
    // Display statistics
    echo "<h3>Contact Form Statistics:</h3>";
    $stats = $contactSystem->getStatistics();
    echo "Total Messages: " . $stats['total_messages'] . "<br>";
    echo "Messages in Last 7 Days: " . $stats['recent_messages'] . "<br>";
    
    echo "<br><strong>Messages by Status:</strong><br>";
    foreach ($stats['by_status'] as $status) {
        echo "- " . ucfirst($status['status']) . ": " . $status['count'] . "<br>";
    }
    
    echo "<br><strong>Messages by Priority:</strong><br>";
    foreach ($stats['by_priority'] as $priority) {
        $color = $priority['priority'] == 'high' ? 'red' : ($priority['priority'] == 'medium' ? 'orange' : 'green');
        echo "- <span style='color: $color'>" . ucfirst($priority['priority']) . "</span>: " . $priority['count'] . "<br>";
    }
    echo "<br>";
    
    // Test validation
    echo "<h3>Testing Input Validation:</h3>";
    
    // Test invalid email
    $result = $contactSystem->saveMessage(
        'Test User', 'invalid-email', '555-1234', 
        'Test Subject', 'Test message content here'
    );
    echo "Invalid email test: " . ($result['success'] ? "❌ Failed" : "✅ Passed - " . $result['message']) . "<br>";
    
    // Test empty required field
    $result = $contactSystem->saveMessage(
        '', 'test@example.com', '555-1234', 
        'Test Subject', 'Test message content here'
    );
    echo "Empty name test: " . ($result['success'] ? "❌ Failed" : "✅ Passed - " . $result['message']) . "<br>";
    
    // Test short message
    $result = $contactSystem->saveMessage(
        'Test User', 'test@example.com', '555-1234', 
        'Test Subject', 'Short'
    );
    echo "Short message test: " . ($result['success'] ? "❌ Failed" : "✅ Passed - " . $result['message']) . "<br>";
    echo "<br>";
    
    // Test successful message with proper data
    $result = $contactSystem->saveMessage(
        'Valid User', 'valid@example.com', '555-1234', 
        'Valid Subject', 'This is a valid message with sufficient length for testing purposes.'
    );
    echo "Valid message test: " . ($result['success'] ? "✅ Passed - " . $result['message'] : "❌ Failed") . "<br>";
    echo "<br>";
    
    // Demonstrate pagination
    echo "<h3>Pagination Demo:</h3>";
    $page1 = $contactSystem->getAllMessages(1, 2);
    echo "Page 1 (2 messages): " . count($page1) . " messages<br>";
    
    $page2 = $contactSystem->getAllMessages(2, 2);
    echo "Page 2 (2 messages): " . count($page2) . " messages<br>";
    
    $totalMessages = $contactSystem->getMessageCount();
    echo "Total messages: $totalMessages<br>";
    echo "<br>";
    
    echo "<h3>Contact Form System Features Demonstrated:</h3>";
    echo "✅ Database table creation<br>";
    echo "✅ Input validation and sanitization<br>";
    echo "✅ Message storage with prepared statements<br>";
    echo "✅ Message retrieval with pagination<br>";
    echo "✅ Search functionality<br>";
    echo "✅ Status and priority management<br>";
    echo "✅ Statistics and reporting<br>";
    echo "✅ Error handling<br>";
    echo "✅ Data security (SQL injection prevention)<br>";
    echo "✅ Client information tracking (IP, User Agent)<br>";
?>
