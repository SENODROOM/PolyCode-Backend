<?php
/**
 * Practical Projects Assessment
 * 
 * Comprehensive hands-on projects demonstrating PHP skills
 * and real-world application development.
 */

// Project Management System
class ProjectManager
{
    private array $projects = [];
    private array $requirements = [];
    private array $submissions = [];
    private array $evaluations = [];
    
    public function __construct()
    {
        $this->initializeProjects();
    }
    
    /**
     * Initialize project definitions
     */
    private function initializeProjects(): void
    {
        $this->projects = [
            'blog_system' => [
                'title' => 'Blog Management System',
                'description' => 'Create a complete blog system with CRUD operations, user authentication, and admin panel',
                'difficulty' => 'medium',
                'estimated_time' => '8-12 hours',
                'skills_tested' => ['PHP fundamentals', 'MySQL', 'Authentication', 'CRUD operations', 'Session management'],
                'requirements' => [
                    'User registration and login system',
                    'Create, read, update, delete blog posts',
                    'Category management',
                    'Comment system',
                    'Admin dashboard',
                    'Search functionality',
                    'Responsive design'
                ],
                'evaluation_criteria' => [
                    'functionality' => 30,
                    'code_quality' => 25,
                    'security' => 20,
                    'user_experience' => 15,
                    'database_design' => 10
                ]
            ],
            'ecommerce_api' => [
                'title' => 'E-commerce REST API',
                'description' => 'Build a RESTful API for an e-commerce platform with product management, orders, and payments',
                'difficulty' => 'hard',
                'estimated_time' => '12-16 hours',
                'skills_tested' => ['REST API design', 'Database integration', 'Authentication', 'JSON handling', 'Error handling'],
                'requirements' => [
                    'Product management (CRUD)',
                    'Category management',
                    'Shopping cart functionality',
                    'Order processing',
                    'User authentication',
                    'Payment integration simulation',
                    'API documentation',
                    'Error handling and validation'
                ],
                'evaluation_criteria' => [
                    'api_design' => 30,
                    'functionality' => 25,
                    'security' => 20,
                    'documentation' => 15,
                    'error_handling' => 10
                ]
            ],
            'task_management' => [
                'title' => 'Task Management Application',
                'description' => 'Develop a task management system with projects, tasks, users, and real-time updates',
                'difficulty' => 'medium',
                'estimated_time' => '10-14 hours',
                'skills_tested' => ['OOP', 'Database design', 'AJAX', 'JavaScript integration', 'Real-time updates'],
                'requirements' => [
                    'User management system',
                    'Project creation and management',
                    'Task creation and assignment',
                    'Status tracking',
                    'Due date management',
                    'Real-time notifications',
                    'File attachments',
                    'Reporting dashboard'
                ],
                'evaluation_criteria' => [
                    'functionality' => 30,
                    'code_organization' => 25,
                    'user_interface' => 20,
                    'database_design' => 15,
                    'features' => 10
                ]
            ],
            'social_network' => [
                'title' => 'Social Network Platform',
                'description' => 'Create a basic social networking platform with user profiles, posts, and interactions',
                'difficulty' => 'hard',
                'estimated_time' => '16-20 hours',
                'skills_tested' => ['Advanced PHP', 'Database design', 'Security', 'File uploads', 'Social features'],
                'requirements' => [
                    'User profiles with avatars',
                    'Post creation and sharing',
                    'Friend/follower system',
                    'Like and comment system',
                    'Privacy settings',
                    'News feed',
                    'Messaging system',
                    'Notification system'
                ],
                'evaluation_criteria' => [
                    'features' => 30,
                    'security' => 25,
                    'user_experience' => 20,
                    'database_design' => 15,
                    'code_quality' => 10
                ]
            ],
            'file_sharing' => [
                'title' => 'File Sharing System',
                'description' => 'Build a secure file sharing system with upload, download, and management features',
                'difficulty' => 'medium',
                'estimated_time' => '8-12 hours',
                'skills_tested' => ['File handling', 'Security', 'Database', 'User management', 'File organization'],
                'requirements' => [
                    'User authentication',
                    'File upload with validation',
                    'File organization and folders',
                    'File sharing with permissions',
                    'Download functionality',
                    'File preview',
                    'Search functionality',
                    'Storage management'
                ],
                'evaluation_criteria' => [
                    'security' => 30,
                    'functionality' => 25,
                    'user_interface' => 20,
                    'file_handling' => 15,
                    'performance' => 10
                ]
            ]
        ];
    }
    
    /**
     * Get all projects
     */
    public function getProjects(): array
    {
        return $this->projects;
    }
    
    /**
     * Get project by ID
     */
    public function getProject(string $id): ?array
    {
        return $this->projects[$id] ?? null;
    }
    
    /**
     * Submit project
     */
    public function submitProject(string $projectId, array $submission): void
    {
        $this->submissions[$projectId] = array_merge([
            'id' => uniqid('submission_'),
            'project_id' => $projectId,
            'submitted_at' => time(),
            'status' => 'pending'
        ], $submission);
        
        // Evaluate the submission
        $this->evaluateSubmission($projectId);
    }
    
    /**
     * Evaluate project submission
     */
    private function evaluateSubmission(string $projectId): void
    {
        $project = $this->projects[$projectId];
        $submission = $this->submissions[$projectId];
        
        $evaluation = [
            'project_id' => $projectId,
            'submission_id' => $submission['id'],
            'evaluated_at' => time(),
            'scores' => [],
            'feedback' => [],
            'total_score' => 0,
            'passed' => false
        ];
        
        // Simulate evaluation based on project criteria
        foreach ($project['evaluation_criteria'] as $criterion => $weight) {
            $score = $this->evaluateCriterion($criterion, $submission);
            $evaluation['scores'][$criterion] = [
                'score' => $score,
                'weight' => $weight,
                'weighted_score' => $score * ($weight / 100)
            ];
            
            $evaluation['total_score'] += $evaluation['scores'][$criterion]['weighted_score'];
        }
        
        $evaluation['passed'] = $evaluation['total_score'] >= 70;
        $evaluation['feedback'] = $this->generateFeedback($evaluation, $project);
        
        $this->evaluations[$projectId] = $evaluation;
        $this->submissions[$projectId]['status'] = 'evaluated';
        $this->submissions[$projectId]['evaluation'] = $evaluation;
    }
    
    /**
     * Evaluate individual criterion
     */
    private function evaluateCriterion(string $criterion, array $submission): int
    {
        // Simulate evaluation based on submission data
        $baseScore = 75; // Base score
        
        switch ($criterion) {
            case 'functionality':
                return $this->evaluateFunctionality($submission);
            case 'code_quality':
                return $this->evaluateCodeQuality($submission);
            case 'security':
                return $this->evaluateSecurity($submission);
            case 'user_experience':
                return $this->evaluateUserExperience($submission);
            case 'database_design':
                return $this->evaluateDatabaseDesign($submission);
            case 'api_design':
                return $this->evaluateApiDesign($submission);
            case 'documentation':
                return $this->evaluateDocumentation($submission);
            case 'error_handling':
                return $this->evaluateErrorHandling($submission);
            case 'features':
                return $this->evaluateFeatures($submission);
            case 'code_organization':
                return $this->evaluateCodeOrganization($submission);
            case 'file_handling':
                return $this->evaluateFileHandling($submission);
            case 'performance':
                return $this->evaluatePerformance($submission);
            default:
                return $baseScore;
        }
    }
    
    /**
     * Evaluation methods for different criteria
     */
    private function evaluateFunctionality(array $submission): int
    {
        // Check if required features are implemented
        $score = 80;
        
        if (isset($submission['features']) && is_array($submission['features'])) {
            $featureCount = count($submission['features']);
            $score = min(80 + ($featureCount * 5), 100);
        }
        
        return $score;
    }
    
    private function evaluateCodeQuality(array $submission): int
    {
        $score = 75;
        
        // Check for code quality indicators
        if (isset($submission['code_structure']) && $submission['code_structure'] === 'good') {
            $score += 10;
        }
        
        if (isset($submission['comments']) && $submission['comments'] === 'adequate') {
            $score += 10;
        }
        
        if (isset($submission['naming_conventions']) && $submission['naming_conventions'] === 'consistent') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateSecurity(array $submission): int
    {
        $score = 70;
        
        // Check for security measures
        if (isset($submission['authentication']) && $submission['authentication'] === 'implemented') {
            $score += 15;
        }
        
        if (isset($submission['input_validation']) && $submission['input_validation'] === 'implemented') {
            $score += 10;
        }
        
        if (isset($submission['sql_injection_protection']) && $submission['sql_injection_protection'] === 'implemented') {
            $score += 10;
        }
        
        if (isset($submission['xss_protection']) && $submission['xss_protection'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateUserExperience(array $submission): int
    {
        $score = 75;
        
        if (isset($submission['responsive_design']) && $submission['responsive_design'] === 'implemented') {
            $score += 10;
        }
        
        if (isset($submission['navigation']) && $submission['navigation'] === 'intuitive') {
            $score += 10;
        }
        
        if (isset($submission['error_messages']) && $submission['error_messages'] === 'clear') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateDatabaseDesign(array $submission): int
    {
        $score = 80;
        
        if (isset($submission['normalization']) && $submission['normalization'] === 'proper') {
            $score += 10;
        }
        
        if (isset($submission['indexes']) && $submission['indexes'] === 'optimized') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateApiDesign(array $submission): int
    {
        $score = 75;
        
        if (isset($submission['restful']) && $submission['restful'] === 'implemented') {
            $score += 15;
        }
        
        if (isset($submission['status_codes']) && $submission['status_codes'] === 'proper') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateDocumentation(array $submission): int
    {
        $score = 70;
        
        if (isset($submission['api_docs']) && $submission['api_docs'] === 'complete') {
            $score += 20;
        }
        
        if (isset($submission['code_comments']) && $submission['code_comments'] === 'adequate') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateErrorHandling(array $submission): int
    {
        $score = 75;
        
        if (isset($submission['try_catch']) && $submission['try_catch'] === 'implemented') {
            $score += 15;
        }
        
        if (isset($submission['validation']) && $submission['validation'] === 'comprehensive') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateFeatures(array $submission): int
    {
        $score = 70;
        
        if (isset($submission['advanced_features']) && is_array($submission['advanced_features'])) {
            $featureCount = count($submission['advanced_features']);
            $score = min(70 + ($featureCount * 6), 100);
        }
        
        return $score;
    }
    
    private function evaluateCodeOrganization(array $submission): int
    {
        $score = 75;
        
        if (isset($submission['mvc_pattern']) && $submission['mvc_pattern'] === 'implemented') {
            $score += 15;
        }
        
        if (isset($submission['separation_of_concerns']) && $submission['separation_of_concerns'] === 'good') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateFileHandling(array $submission): int
    {
        $score = 75;
        
        if (isset($submission['file_validation']) && $submission['file_validation'] === 'implemented') {
            $score += 15;
        }
        
        if (isset($submission['file_security']) && $submission['file_security'] === 'implemented') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluatePerformance(array $submission): int
    {
        $score = 80;
        
        if (isset($submission['caching']) && $submission['caching'] === 'implemented') {
            $score += 10;
        }
        
        if (isset($submission['optimization']) && $submission['optimization'] === 'good') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    /**
     * Generate feedback
     */
    private function generateFeedback(array $evaluation, array $project): array
    {
        $feedback = [];
        
        foreach ($evaluation['scores'] as $criterion => $scoreData) {
            if ($scoreData['score'] < 70) {
                $feedback[] = "Improve $criterion: Current score is " . round($scoreData['score'], 1) . "%";
            }
        }
        
        if ($evaluation['passed']) {
            $feedback[] = "Excellent work! Project meets all requirements.";
        } else {
            $feedback[] = "Project needs improvement to pass. Focus on areas with scores below 70%.";
        }
        
        return $feedback;
    }
    
    /**
     * Get submission
     */
    public function getSubmission(string $projectId): ?array
    {
        return $this->submissions[$projectId] ?? null;
    }
    
    /**
     * Get evaluation
     */
    public function getEvaluation(string $projectId): ?array
    {
        return $this->evaluations[$projectId] ?? null;
    }
    
    /**
     * Get all submissions
     */
    public function getAllSubmissions(): array
    {
        return $this->submissions;
    }
    
    /**
     * Get all evaluations
     */
    public function getAllEvaluations(): array
    {
        return $this->evaluations;
    }
    
    /**
     * Calculate overall score
     */
    public function calculateOverallScore(): array
    {
        $totalScore = 0;
        $passedProjects = 0;
        $totalProjects = count($this->evaluations);
        
        foreach ($this->evaluations as $evaluation) {
            $totalScore += $evaluation['total_score'];
            if ($evaluation['passed']) {
                $passedProjects++;
            }
        }
        
        $averageScore = $totalProjects > 0 ? $totalScore / $totalProjects : 0;
        
        return [
            'average_score' => $averageScore,
            'passed_projects' => $passedProjects,
            'total_projects' => $totalProjects,
            'pass_rate' => $totalProjects > 0 ? ($passedProjects / $totalProjects) * 100 : 0,
            'overall_passed' => $passedProjects >= 3 // Require at least 3 passed projects
        ];
    }
}

// Project Templates and Examples
class ProjectTemplates
{
    private array $templates = [];
    
    public function __construct()
    {
        $this->initializeTemplates();
    }
    
    /**
     * Initialize project templates
     */
    private function initializeTemplates(): void
    {
        $this->templates = [
            'blog_system' => [
                'database_schema' => [
                    'users' => [
                        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                        'username' => 'VARCHAR(50) UNIQUE NOT NULL',
                        'email' => 'VARCHAR(100) UNIQUE NOT NULL',
                        'password' => 'VARCHAR(255) NOT NULL',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                    ],
                    'posts' => [
                        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                        'title' => 'VARCHAR(255) NOT NULL',
                        'content' => 'TEXT NOT NULL',
                        'author_id' => 'INT NOT NULL',
                        'category_id' => 'INT',
                        'status' => 'ENUM("draft", "published") DEFAULT "draft"',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                        'FOREIGN KEY (author_id) REFERENCES users(id)',
                        'FOREIGN KEY (category_id) REFERENCES categories(id)'
                    ],
                    'categories' => [
                        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                        'name' => 'VARCHAR(100) NOT NULL',
                        'description' => 'TEXT',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                    ],
                    'comments' => [
                        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                        'post_id' => 'INT NOT NULL',
                        'author_id' => 'INT NOT NULL',
                        'content' => 'TEXT NOT NULL',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                        'FOREIGN KEY (post_id) REFERENCES posts(id)',
                        'FOREIGN KEY (author_id) REFERENCES users(id)'
                    ]
                ],
                'sample_code' => [
                    'user_registration' => '<?php
class User {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function register($username, $email, $password) {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            throw new InvalidArgumentException("All fields are required");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
        
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            throw new Exception("Email already exists");
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user["password"])) {
            throw new Exception("Invalid credentials");
        }
        
        return $user["id"];
    }
}',
                    'post_management' => '<?php
class Post {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function create($title, $content, $authorId, $categoryId = null) {
        $stmt = $this->db->prepare("INSERT INTO posts (title, content, author_id, category_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $content, $authorId, $categoryId]);
    }
    
    public function getAll($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as author, c.name as category 
            FROM posts p 
            JOIN users u ON p.author_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = "published" 
            ORDER BY p.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as author, c.name as category 
            FROM posts p 
            JOIN users u ON p.author_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $title, $content, $categoryId = null) {
        $stmt = $this->db->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$title, $content, $categoryId, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}'
                ],
                'requirements_checklist' => [
                    'User authentication system' => [
                        'User registration with validation',
                        'User login with session management',
                        'Password hashing',
                        'Logout functionality'
                    ],
                    'Blog post management' => [
                        'Create new posts',
                        'Edit existing posts',
                        'Delete posts',
                        'View single posts',
                        'List all posts with pagination'
                    ],
                    'Category system' => [
                        'Create categories',
                        'Assign posts to categories',
                        'Filter posts by category'
                    ],
                    'Comment system' => [
                        'Add comments to posts',
                        'View comments',
                        'Delete comments (author/admin)'
                    ],
                    'Admin features' => [
                        'Admin dashboard',
                        'Manage all posts',
                        'Manage users',
                        'Manage categories'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get template
     */
    public function getTemplate(string $projectId): ?array
    {
        return $this->templates[$projectId] ?? null;
    }
    
    /**
     * Get all templates
     */
    public function getAllTemplates(): array
    {
        return $this->templates;
    }
}

// Practical Projects Examples
class PracticalProjectsExamples
{
    private ProjectManager $projectManager;
    private ProjectTemplates $templates;
    
    public function __construct()
    {
        $this->projectManager = new ProjectManager();
        $this->templates = new ProjectTemplates();
    }
    
    public function demonstrateProjectOverview(): void
    {
        echo "Practical Projects Overview\n";
        echo str_repeat("-", 30) . "\n";
        
        $projects = $this->projectManager->getProjects();
        
        echo "Available Projects:\n\n";
        
        foreach ($projects as $id => $project) {
            echo "{$project['title']}\n";
            echo "  Difficulty: {$project['difficulty']}\n";
            echo "  Estimated Time: {$project['estimated_time']}\n";
            echo "  Description: {$project['description']}\n";
            echo "  Skills Tested: " . implode(', ', $project['skills_tested']) . "\n";
            echo "  Requirements:\n";
            
            foreach (array_slice($project['requirements'], 0, 3) as $requirement) {
                echo "    • $requirement\n";
            }
            
            if (count($project['requirements']) > 3) {
                echo "    • And " . (count($project['requirements']) - 3) . " more...\n";
            }
            
            echo "\n";
        }
        
        echo "Evaluation Criteria:\n";
        echo "  • Functionality: Implementation of required features\n";
        echo "  • Code Quality: Clean, maintainable, well-documented code\n";
        echo "  • Security: Proper authentication, validation, and protection\n";
        echo "  • User Experience: Intuitive interface and responsive design\n";
        echo "  • Database Design: Proper structure and optimization\n";
    }
    
    public function demonstrateProjectSubmission(): void
    {
        echo "\nProject Submission Example\n";
        echo str_repeat("-", 28) . "\n";
        
        // Simulate submitting a blog system project
        $submission = [
            'project_files' => [
                'index.php',
                'config/database.php',
                'classes/User.php',
                'classes/Post.php',
                'classes/Comment.php',
                'admin/dashboard.php',
                'css/style.css',
                'js/script.js'
            ],
            'features' => [
                'user_registration',
                'user_login',
                'post_crud',
                'category_management',
                'comment_system',
                'search_functionality',
                'admin_panel'
            ],
            'code_structure' => 'good',
            'comments' => 'adequate',
            'naming_conventions' => 'consistent',
            'authentication' => 'implemented',
            'input_validation' => 'implemented',
            'sql_injection_protection' => 'implemented',
            'xss_protection' => 'implemented',
            'responsive_design' => 'implemented',
            'navigation' => 'intuitive',
            'error_messages' => 'clear',
            'normalization' => 'proper',
            'indexes' => 'optimized'
        ];
        
        echo "Submitting Blog System Project...\n";
        $this->projectManager->submitProject('blog_system', $submission);
        
        // Get evaluation results
        $evaluation = $this->projectManager->getEvaluation('blog_system');
        
        echo "\nEvaluation Results:\n";
        echo "Total Score: " . round($evaluation['total_score'], 1) . "%\n";
        echo "Status: " . ($evaluation['passed'] ? 'PASSED' : 'FAILED') . "\n\n";
        
        echo "Detailed Scores:\n";
        foreach ($evaluation['scores'] as $criterion => $score) {
            echo "  $criterion: " . round($score['score'], 1) . "% (Weight: {$score['weight']}%)\n";
        }
        
        echo "\nFeedback:\n";
        foreach ($evaluation['feedback'] as $feedback) {
            echo "  • $feedback\n";
        }
    }
    
    public function demonstrateProjectTemplates(): void
    {
        echo "\nProject Templates and Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        $template = $this->templates->getTemplate('blog_system');
        
        echo "Blog System Template:\n";
        echo "Database Schema:\n";
        foreach ($template['database_schema'] as $table => $fields) {
            echo "  $table:\n";
            foreach ($fields as $field => $definition) {
                echo "    $field: $definition\n";
            }
            echo "\n";
        }
        
        echo "Sample Code (User Registration):\n";
        echo substr($template['sample_code']['user_registration'], 0, 500) . "...\n\n";
        
        echo "Requirements Checklist:\n";
        foreach ($template['requirements_checklist'] as $category => $items) {
            echo "$category:\n";
            foreach ($items as $item) {
                echo "  • $item\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateMultipleProjects(): void
    {
        echo "\nMultiple Projects Submission\n";
        echo str_repeat("-", 30) . "\n";
        
        // Simulate submitting multiple projects
        $projects = [
            'blog_system' => [
                'features' => ['user_auth', 'post_crud', 'comments'],
                'authentication' => 'implemented',
                'input_validation' => 'implemented',
                'responsive_design' => 'implemented'
            ],
            'ecommerce_api' => [
                'features' => ['product_crud', 'cart', 'orders'],
                'restful' => 'implemented',
                'status_codes' => 'proper',
                'api_docs' => 'complete'
            ],
            'task_management' => [
                'features' => ['projects', 'tasks', 'users'],
                'mvc_pattern' => 'implemented',
                'separation_of_concerns' => 'good',
                'navigation' => 'intuitive'
            ]
        ];
        
        foreach ($projects as $projectId => $submission) {
            echo "Submitting $projectId...\n";
            $this->projectManager->submitProject($projectId, $submission);
            
            $evaluation = $this->projectManager->getEvaluation($projectId);
            echo "Score: " . round($evaluation['total_score'], 1) . "% - " . ($evaluation['passed'] ? 'PASSED' : 'FAILED') . "\n";
        }
        
        $overallScore = $this->projectManager->calculateOverallScore();
        
        echo "\nOverall Results:\n";
        echo "Average Score: " . round($overallScore['average_score'], 1) . "%\n";
        echo "Passed Projects: {$overallScore['passed_projects']}/{$overallScore['total_projects']}\n";
        echo "Pass Rate: " . round($overallScore['pass_rate'], 1) . "%\n";
        echo "Overall Status: " . ($overallScore['overall_passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    public function demonstrateProjectGuidelines(): void
    {
        echo "\nProject Development Guidelines\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Development Process:\n";
        echo "1. Requirements Analysis\n";
        echo "   • Understand all project requirements\n";
        echo "   • Plan database schema\n";
        echo "   • Design application architecture\n\n";
        
        echo "2. Database Setup\n";
        echo "   • Create database and tables\n";
        echo "   • Define relationships\n";
        echo "   • Add sample data\n\n";
        
        echo "3. Core Development\n";
        echo "   • Implement authentication system\n";
        echo "   • Create CRUD operations\n";
        echo "   • Add business logic\n\n";
        
        echo "4. User Interface\n";
        echo "   • Design responsive layout\n";
        echo "   • Implement forms and validation\n";
        echo "   • Add navigation and user experience\n\n";
        
        echo "5. Testing and Refinement\n";
        echo "   • Test all functionality\n";
        echo "   • Fix bugs and issues\n";
        echo "   • Optimize performance\n\n";
        
        echo "Code Quality Standards:\n";
        echo "• Follow PSR coding standards\n";
        echo "• Use meaningful variable and function names\n";
        echo "• Add comments and documentation\n";
        echo "• Implement proper error handling\n";
        echo "• Use object-oriented programming\n";
        echo "• Separate concerns (MVC pattern)\n";
        echo "• Validate and sanitize all input\n\n";
        
        echo "Security Requirements:\n";
        echo "• Implement proper authentication\n";
        echo "• Use prepared statements for database queries\n";
        echo "• Validate all user input\n";
        echo "• Prevent XSS attacks\n";
        echo "• Use HTTPS for sensitive data\n";
        echo "• Implement proper session management\n\n";
        
        echo "Submission Requirements:\n";
        echo "• Complete source code\n";
        echo "• Database schema file\n";
        echo "• README with setup instructions\n";
        echo "• Working demo or screenshots\n";
        echo "• Brief project description\n";
        echo "• List of implemented features\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nProject Best Practices\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "1. Planning and Design:\n";
        echo "   • Read all requirements carefully\n";
        echo "   • Plan database structure first\n";
        echo "   • Design application architecture\n";
        echo "   • Create wireframes/mockups\n";
        echo "   • Break project into smaller tasks\n\n";
        
        echo "2. Development:\n";
        echo "   • Start with core functionality\n";
        echo "   • Build incrementally\n";
        echo "   • Test each component\n";
        echo "   • Use version control (Git)\n";
        echo "   • Write clean, maintainable code\n\n";
        
        echo "3. Security:\n";
        echo "   • Never trust user input\n";
        echo "   • Use prepared statements\n";
        echo "   • Implement proper authentication\n";
        echo "   • Validate and sanitize data\n";
        echo "   • Use HTTPS for sensitive operations\n\n";
        
        echo "4. Testing:\n";
        echo "   • Test all functionality\n";
        echo "   • Test edge cases\n";
        echo "   • Test security measures\n";
        echo "   • Test with different browsers\n";
        echo "   • Get feedback from others\n\n";
        
        echo "5. Documentation:\n";
        echo "   • Add code comments\n";
        echo "   • Create README file\n";
        echo "   • Document API endpoints\n";
        echo "   • Include setup instructions\n";
        echo "   • Explain architecture decisions\n\n";
        
        echo "Common Pitfalls to Avoid:\n";
        echo "• Starting without planning\n";
        echo "• Ignoring security requirements\n";
        echo "• Writing messy code\n";
        echo "• Not testing thoroughly\n";
        echo "• Missing documentation\n";
        echo "• Not following requirements";
    }
    
    public function runAllExamples(): void
    {
        echo "Practical Projects Assessment\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateProjectOverview();
        $this->demonstrateProjectSubmission();
        $this->demonstrateProjectTemplates();
        $this->demonstrateMultipleProjects();
        $this->demonstrateProjectGuidelines();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runPracticalProjectsDemo(): void
{
    $examples = new PracticalProjectsExamples();
    $examples->runAllExamples();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPracticalProjectsDemo();
}
?>
