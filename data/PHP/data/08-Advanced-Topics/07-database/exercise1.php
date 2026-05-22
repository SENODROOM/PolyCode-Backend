<?php
    // Exercise 1: Simple Blog System with Database
    
    class BlogSystem {
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
                // Create posts table
                $sql = "CREATE TABLE IF NOT EXISTS posts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT NOT NULL,
                    author VARCHAR(100) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    status ENUM('draft', 'published') DEFAULT 'draft'
                )";
                $this->pdo->exec($sql);
                
                // Create comments table
                $sql = "CREATE TABLE IF NOT EXISTS comments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT NOT NULL,
                    author VARCHAR(100) NOT NULL,
                    email VARCHAR(255),
                    content TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
                )";
                $this->pdo->exec($sql);
                
                echo "Tables created successfully!<br>";
            } catch (PDOException $e) {
                echo "Error creating tables: " . $e->getMessage() . "<br>";
            }
        }
        
        // CREATE: Add new post
        public function createPost($title, $content, $author, $status = 'draft') {
            try {
                $sql = "INSERT INTO posts (title, content, author, status) VALUES (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$title, $content, $author, $status]);
                
                return $this->pdo->lastInsertId();
            } catch (PDOException $e) {
                error_log("Error creating post: " . $e->getMessage());
                return false;
            }
        }
        
        // READ: Get all posts
        public function getAllPosts($status = 'published') {
            try {
                $sql = "SELECT * FROM posts WHERE status = ? ORDER BY created_at DESC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$status]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error getting posts: " . $e->getMessage());
                return [];
            }
        }
        
        // READ: Get single post with comments
        public function getPost($id) {
            try {
                // Get post
                $sql = "SELECT * FROM posts WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                $post = $stmt->fetch();
                
                if ($post) {
                    // Get comments
                    $sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$id]);
                    $post['comments'] = $stmt->fetchAll();
                }
                
                return $post;
            } catch (PDOException $e) {
                error_log("Error getting post: " . $e->getMessage());
                return null;
            }
        }
        
        // UPDATE: Update post
        public function updatePost($id, $title, $content, $status = null) {
            try {
                if ($status) {
                    $sql = "UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$title, $content, $status, $id]);
                } else {
                    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$title, $content, $id]);
                }
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error updating post: " . $e->getMessage());
                return false;
            }
        }
        
        // DELETE: Delete post
        public function deletePost($id) {
            try {
                $sql = "DELETE FROM posts WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error deleting post: " . $e->getMessage());
                return false;
            }
        }
        
        // CREATE: Add comment
        public function addComment($postId, $author, $email, $content) {
            try {
                $sql = "INSERT INTO comments (post_id, author, email, content) VALUES (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$postId, $author, $email, $content]);
                
                return $this->pdo->lastInsertId();
            } catch (PDOException $e) {
                error_log("Error adding comment: " . $e->getMessage());
                return false;
            }
        }
        
        // DELETE: Delete comment
        public function deleteComment($id) {
            try {
                $sql = "DELETE FROM comments WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error deleting comment: " . $e->getMessage());
                return false;
            }
        }
        
        // Search posts
        public function searchPosts($query) {
            try {
                $sql = "SELECT * FROM posts WHERE 
                        title LIKE ? OR content LIKE ? 
                        AND status = 'published' 
                        ORDER BY created_at DESC";
                $searchTerm = "%$query%";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$searchTerm, $searchTerm]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error searching posts: " . $e->getMessage());
                return [];
            }
        }
        
        // Get statistics
        public function getStats() {
            try {
                $stats = [];
                
                // Total posts
                $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM posts");
                $stats['total_posts'] = $stmt->fetch()['total'];
                
                // Published posts
                $stmt = $this->pdo->query("SELECT COUNT(*) as published FROM posts WHERE status = 'published'");
                $stats['published_posts'] = $stmt->fetch()['published'];
                
                // Draft posts
                $stmt = $this->pdo->query("SELECT COUNT(*) as drafts FROM posts WHERE status = 'draft'");
                $stats['draft_posts'] = $stmt->fetch()['drafts'];
                
                // Total comments
                $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM comments");
                $stats['total_comments'] = $stmt->fetch()['total'];
                
                return $stats;
            } catch (PDOException $e) {
                error_log("Error getting stats: " . $e->getMessage());
                return [];
            }
        }
    }
    
    // Demo
    $blog = new BlogSystem("localhost", "test_db", "root", "");
    
    echo "<h2>Blog System Demo</h2>";
    
    // Create tables
    $blog->createTables();
    echo "<br>";
    
    // Create sample posts
    echo "<h3>Creating Sample Posts:</h3>";
    $post1Id = $blog->createPost(
        "Welcome to My Blog",
        "This is my first blog post. I'm excited to share my thoughts and experiences with you!",
        "John Doe",
        "published"
    );
    echo "Created post 1 with ID: $post1Id<br>";
    
    $post2Id = $blog->createPost(
        "PHP Database Tutorial",
        "In this post, I'll explain how to connect PHP to databases and perform CRUD operations.",
        "Jane Smith",
        "published"
    );
    echo "Created post 2 with ID: $post2Id<br>";
    
    $post3Id = $blog->createPost(
        "Draft Post",
        "This is a draft post that hasn't been published yet.",
        "John Doe",
        "draft"
    );
    echo "Created post 3 with ID: $post3Id<br>";
    echo "<br>";
    
    // Display all published posts
    echo "<h3>All Published Posts:</h3>";
    $posts = $blog->getAllPosts();
    if ($posts) {
        foreach ($posts as $post) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h4>" . htmlspecialchars($post['title']) . "</h4>";
            echo "<p>" . htmlspecialchars(substr($post['content'], 0, 100)) . "...</p>";
            echo "<small>By " . htmlspecialchars($post['author']) . " on " . $post['created_at'] . "</small>";
            echo "</div>";
        }
    } else {
        echo "No published posts found.<br>";
    }
    echo "<br>";
    
    // Add comments to posts
    echo "<h3>Adding Comments:</h3>";
    $commentId1 = $blog->addComment($post1Id, "Alice", "alice@example.com", "Great first post! Looking forward to more.");
    echo "Added comment 1 with ID: $commentId1<br>";
    
    $commentId2 = $blog->addComment($post1Id, "Bob", "bob@example.com", "Welcome to the blogging world!");
    echo "Added comment 2 with ID: $commentId2<br>";
    
    $commentId3 = $blog->addComment($post2Id, "Charlie", "charlie@example.com", "This tutorial was very helpful. Thanks!");
    echo "Added comment 3 with ID: $commentId3<br>";
    echo "<br>";
    
    // Display post with comments
    echo "<h3>Post with Comments:</h3>";
    $post = $blog->getPost($post1Id);
    if ($post) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h4>" . htmlspecialchars($post['title']) . "</h4>";
        echo "<p>" . htmlspecialchars($post['content']) . "</p>";
        echo "<small>By " . htmlspecialchars($post['author']) . " on " . $post['created_at'] . "</small>";
        
        if (!empty($post['comments'])) {
            echo "<h5>Comments:</h5>";
            foreach ($post['comments'] as $comment) {
                echo "<div style='margin-left: 20px; padding: 5px; border-left: 2px solid #ddd;'>";
                echo "<p>" . htmlspecialchars($comment['content']) . "</p>";
                echo "<small>" . htmlspecialchars($comment['author']) . " on " . $comment['created_at'] . "</small>";
                echo "</div>";
            }
        }
        echo "</div>";
    }
    echo "<br>";
    
    // Update a post
    echo "<h3>Updating Post:</h3>";
    $updated = $blog->updatePost($post1Id, "Welcome to My Blog - Updated", 
                                "This is my updated first blog post with more content!");
    echo "Post updated: " . ($updated ? "Yes" : "No") . "<br><br>";
    
    // Search posts
    echo "<h3>Searching Posts:</h3>";
    $searchResults = $blog->searchPosts("PHP");
    echo "Found " . count($searchResults) . " posts matching 'PHP':<br>";
    foreach ($searchResults as $result) {
        echo "- " . htmlspecialchars($result['title']) . "<br>";
    }
    echo "<br>";
    
    // Display statistics
    echo "<h3>Blog Statistics:</h3>";
    $stats = $blog->getStats();
    echo "Total Posts: " . $stats['total_posts'] . "<br>";
    echo "Published Posts: " . $stats['published_posts'] . "<br>";
    echo "Draft Posts: " . $stats['draft_posts'] . "<br>";
    echo "Total Comments: " . $stats['total_comments'] . "<br>";
    echo "<br>";
    
    // Demonstrate deletion
    echo "<h3>Deleting Content:</h3>";
    $commentDeleted = $blog->deleteComment($commentId1);
    echo "Comment deleted: " . ($commentDeleted ? "Yes" : "No") . "<br>";
    
    $postDeleted = $blog->deletePost($post3Id);
    echo "Draft post deleted: " . ($postDeleted ? "Yes" : "No") . "<br>";
    echo "<br>";
    
    // Final statistics
    echo "<h3>Final Statistics:</h3>";
    $finalStats = $blog->getStats();
    echo "Total Posts: " . $finalStats['total_posts'] . "<br>";
    echo "Published Posts: " . $finalStats['published_posts'] . "<br>";
    echo "Draft Posts: " . $finalStats['draft_posts'] . "<br>";
    echo "Total Comments: " . $finalStats['total_comments'] . "<br>";
    
    echo "<br><h3>Blog System Features Demonstrated:</h3>";
    echo "✅ Database connection with PDO<br>";
    echo "✅ Table creation<br>";
    echo "✅ CREATE operations (posts and comments)<br>";
    echo "✅ READ operations (all posts, single post, search)<br>";
    echo "✅ UPDATE operations<br>";
    echo "✅ DELETE operations<br>";
    echo "✅ Prepared statements for security<br>";
    echo "✅ Error handling<br>";
    echo "✅ Foreign key relationships<br>";
    echo "✅ Data validation and sanitization<br>";
?>
