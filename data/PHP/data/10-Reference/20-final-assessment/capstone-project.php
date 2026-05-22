<?php
/**
 * Capstone Project
 * 
 * Comprehensive project demonstrating all PHP skills learned
 * throughout the course. This is the final assessment project.
 */

// Capstone Project Manager
class CapstoneProjectManager
{
    private array $projectRequirements = [];
    private array $projectStructure = [];
    private array $evaluationCriteria = [];
    private array $submissions = [];
    private array $evaluations = [];
    
    public function __construct()
    {
        $this->initializeProjectRequirements();
        $this->initializeProjectStructure();
        $this->initializeEvaluationCriteria();
    }
    
    /**
     * Initialize project requirements
     */
    private function initializeProjectRequirements(): void
    {
        $this->projectRequirements = [
            'title' => 'Complete Web Application Platform',
            'description' => 'Build a comprehensive web application that demonstrates all PHP skills learned',
            'difficulty' => 'expert',
            'estimated_time' => '40-60 hours',
            'objectives' => [
                'Demonstrate mastery of PHP fundamentals',
                'Show understanding of OOP principles',
                'Implement database design and optimization',
                'Apply security best practices',
                'Create RESTful APIs',
                'Implement real-time features',
                'Design scalable architecture',
                'Follow coding standards'
            ],
            'core_features' => [
                'User Management System' => [
                    'User registration and authentication',
                    'Profile management',
                    'Role-based access control',
                    'Password recovery',
                    'User preferences'
                ],
                'Content Management' => [
                    'Content creation and editing',
                    'Media management',
                    'Content categorization',
                    'Search functionality',
                    'Content scheduling'
                ],
                'Social Features' => [
                    'User profiles and networking',
                    'Comment system',
                    'Like/rating system',
                    'Follow/friend system',
                    'Activity feed'
                ],
                'Admin Dashboard' => [
                    'User management',
                    'Content moderation',
                    'Analytics and reporting',
                    'System configuration',
                    'Backup and restore'
                ],
                'API Integration' => [
                    'RESTful API endpoints',
                    'API authentication',
                    'Rate limiting',
                    'API documentation',
                    'Third-party integrations'
                ]
            ],
            'technical_requirements' => [
                'backend_technologies' => [
                    'PHP 8+ with modern features',
                    'Object-oriented programming',
                    'MVC architecture pattern',
                    'Database integration (MySQL/PostgreSQL)',
                    'Caching implementation',
                    'Session management'
                ],
                'frontend_technologies' => [
                    'Responsive design',
                    'JavaScript integration',
                    'AJAX functionality',
                    'Form validation',
                    'Progressive enhancement'
                ],
                'security_requirements' => [
                    'Input validation and sanitization',
                    'SQL injection prevention',
                    'XSS protection',
                    'CSRF protection',
                    'Secure authentication',
                    'Data encryption'
                ],
                'performance_requirements' => [
                    'Optimized database queries',
                    'Caching strategies',
                    'Lazy loading',
                    'Minified assets',
                    'Performance monitoring'
                ]
            ],
            'deliverables' => [
                'Complete source code',
                'Database schema file',
                'Configuration files',
                'Documentation',
                'API documentation',
                'User manual',
                'Deployment guide',
                'Testing suite'
            ]
        ];
    }
    
    /**
     * Initialize project structure
     */
    private function initializeProjectStructure(): void
    {
        $this->projectStructure = [
            'root' => [
                'public/' => [
                    'index.php' => 'Application entry point',
                    'assets/' => [
                        'css/' => 'Stylesheets',
                        'js/' => 'JavaScript files',
                        'images/' => 'Image files',
                        'uploads/' => 'User uploaded files'
                    ]
                ],
                'src/' => [
                    'Controllers/' => 'Application controllers',
                    'Models/' => 'Data models',
                    'Views/' => 'View templates',
                    'Services/' => 'Business logic services',
                    'Middleware/' => 'Request/response middleware',
                    'Helpers/' => 'Utility functions',
                    'Config/' => 'Configuration files'
                ],
                'database/' => [
                    'migrations/' => 'Database migration files',
                    'seeds/' => 'Database seed data',
                    'schema.sql' => 'Database schema'
                ],
                'tests/' => [
                    'Unit/' => 'Unit tests',
                    'Integration/' => 'Integration tests',
                    'Feature/' => 'Feature tests'
                ],
                'docs/' => [
                    'api.md' => 'API documentation',
                    'README.md' => 'Project documentation',
                    'deployment.md' => 'Deployment guide',
                    'user-manual.md' => 'User manual'
                ],
                'vendor/' => 'Composer dependencies',
                '.env.example' => 'Environment variables example',
                '.gitignore' => 'Git ignore file',
                'composer.json' => 'Composer configuration',
                'README.md' => 'Project README'
            ]
        ];
    }
    
    /**
     * Initialize evaluation criteria
     */
    private function initializeEvaluationCriteria(): void
    {
        $this->evaluationCriteria = [
            'functionality' => [
                'weight' => 30,
                'description' => 'Implementation of required features',
                'subcriteria' => [
                    'core_features' => 40,
                    'additional_features' => 30,
                    'user_experience' => 30
                ]
            ],
            'code_quality' => [
                'weight' => 25,
                'description' => 'Code organization, readability, and maintainability',
                'subcriteria' => [
                    'structure' => 30,
                    'readability' => 25,
                    'documentation' => 20,
                    'error_handling' => 25
                ]
            ],
            'security' => [
                'weight' => 20,
                'description' => 'Security implementation and best practices',
                'subcriteria' => [
                    'authentication' => 30,
                    'input_validation' => 25,
                    'data_protection' => 25,
                    'vulnerability_prevention' => 20
                ]
            ],
            'database_design' => [
                'weight' => 15,
                'description' => 'Database structure and optimization',
                'subcriteria' => [
                    'schema_design' => 40,
                    'normalization' => 30,
                    'optimization' => 30
                ]
            ],
            'architecture' => [
                'weight' => 10,
                'description' => 'System architecture and design patterns',
                'subcriteria' => [
                    'mvc_implementation' => 40,
                    'design_patterns' => 30,
                    'scalability' => 30
                ]
            ]
        ];
    }
    
    /**
     * Get project requirements
     */
    public function getProjectRequirements(): array
    {
        return $this->projectRequirements;
    }
    
    /**
     * Get project structure
     */
    public function getProjectStructure(): array
    {
        return $this->projectStructure;
    }
    
    /**
     * Get evaluation criteria
     */
    public function getEvaluationCriteria(): array
    {
        return $this->evaluationCriteria;
    }
    
    /**
     * Submit capstone project
     */
    public function submitProject(array $submission): void
    {
        $this->submissions[] = array_merge([
            'id' => uniqid('capstone_'),
            'submitted_at' => time(),
            'status' => 'pending'
        ], $submission);
        
        // Evaluate the project
        $this->evaluateProject(end($this->submissions)['id']);
    }
    
    /**
     * Evaluate capstone project
     */
    private function evaluateProject(string $submissionId): void
    {
        $submission = null;
        foreach ($this->submissions as $sub) {
            if ($sub['id'] === $submissionId) {
                $submission = $sub;
                break;
            }
        }
        
        if (!$submission) {
            throw new Exception('Submission not found');
        }
        
        $evaluation = [
            'submission_id' => $submissionId,
            'evaluated_at' => time(),
            'scores' => [],
            'feedback' => [],
            'total_score' => 0,
            'passed' => false,
            'grade' => ''
        ];
        
        // Evaluate each criterion
        foreach ($this->evaluationCriteria as $criterion => $details) {
            $score = $this->evaluateCriterion($criterion, $submission);
            $evaluation['scores'][$criterion] = [
                'score' => $score,
                'weight' => $details['weight'],
                'weighted_score' => $score * ($details['weight'] / 100)
            ];
            
            $evaluation['total_score'] += $evaluation['scores'][$criterion]['weighted_score'];
        }
        
        $evaluation['passed'] = $evaluation['total_score'] >= 70;
        $evaluation['grade'] = $this->calculateGrade($evaluation['total_score']);
        $evaluation['feedback'] = $this->generateFeedback($evaluation);
        
        $this->evaluations[$submissionId] = $evaluation;
        
        // Update submission status
        foreach ($this->submissions as &$sub) {
            if ($sub['id'] === $submissionId) {
                $sub['status'] = 'evaluated';
                $sub['evaluation'] = $evaluation;
                break;
            }
        }
    }
    
    /**
     * Evaluate individual criterion
     */
    private function evaluateCriterion(string $criterion, array $submission): int
    {
        switch ($criterion) {
            case 'functionality':
                return $this->evaluateFunctionality($submission);
            case 'code_quality':
                return $this->evaluateCodeQuality($submission);
            case 'security':
                return $this->evaluateSecurity($submission);
            case 'database_design':
                return $this->evaluateDatabaseDesign($submission);
            case 'architecture':
                return $this->evaluateArchitecture($submission);
            default:
                return 75; // Default score
        }
    }
    
    /**
     * Evaluation methods for different criteria
     */
    private function evaluateFunctionality(array $submission): int
    {
        $score = 75;
        
        // Check core features implementation
        if (isset($submission['core_features']) && count($submission['core_features']) >= 5) {
            $score += 10;
        }
        
        // Check additional features
        if (isset($submission['additional_features']) && count($submission['additional_features']) >= 3) {
            $score += 10;
        }
        
        // Check user experience
        if (isset($submission['user_experience']) && $submission['user_experience'] === 'excellent') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateCodeQuality(array $submission): int
    {
        $score = 75;
        
        // Check code structure
        if (isset($submission['code_structure']) && $submission['code_structure'] === 'well_organized') {
            $score += 10;
        }
        
        // Check documentation
        if (isset($submission['documentation']) && $submission['documentation'] === 'comprehensive') {
            $score += 10;
        }
        
        // Check error handling
        if (isset($submission['error_handling']) && $submission['error_handling'] === 'robust') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateSecurity(array $submission): int
    {
        $score = 70;
        
        // Check authentication
        if (isset($submission['authentication']) && $submission['authentication'] === 'secure') {
            $score += 10;
        }
        
        // Check input validation
        if (isset($submission['input_validation']) && $submission['input_validation'] === 'comprehensive') {
            $score += 10;
        }
        
        // Check data protection
        if (isset($submission['data_protection']) && $submission['data_protection'] === 'implemented') {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    private function evaluateDatabaseDesign(array $submission): int
    {
        $score = 75;
        
        // Check schema design
        if (isset($submission['schema_design']) && $submission['schema_design'] === 'normalized') {
            $score += 10;
        }
        
        // Check optimization
        if (isset($submission['optimization']) && $submission['optimization'] === 'optimized') {
            $score += 10;
        }
        
        // Check relationships
        if (isset($submission['relationships']) && $submission['relationships'] === 'properly_defined') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateArchitecture(array $submission): int
    {
        $score = 75;
        
        // Check MVC implementation
        if (isset($submission['mvc_implementation']) && $submission['mvc_implementation'] === 'proper') {
            $score += 10;
        }
        
        // Check design patterns
        if (isset($submission['design_patterns']) && count($submission['design_patterns']) >= 3) {
            $score += 10;
        }
        
        // Check scalability
        if (isset($submission['scalability']) && $submission['scalability'] === 'considered') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    /**
     * Calculate grade
     */
    private function calculateGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }
    
    /**
     * Generate feedback
     */
    private function generateFeedback(array $evaluation): array
    {
        $feedback = [];
        
        foreach ($evaluation['scores'] as $criterion => $scoreData) {
            if ($scoreData['score'] < 70) {
                $feedback[] = "Improve $criterion: Current score is " . round($scoreData['score'], 1) . "%";
            }
        }
        
        if ($evaluation['passed']) {
            $feedback[] = "Excellent work! Capstone project demonstrates mastery of PHP development.";
        } else {
            $feedback[] = "Project needs improvement to pass. Focus on areas with scores below 70%.";
        }
        
        return $feedback;
    }
    
    /**
     * Get submission
     */
    public function getSubmission(string $submissionId): ?array
    {
        foreach ($this->submissions as $submission) {
            if ($submission['id'] === $submissionId) {
                return $submission;
            }
        }
        return null;
    }
    
    /**
     * Get evaluation
     */
    public function getEvaluation(string $submissionId): ?array
    {
        return $this->evaluations[$submissionId] ?? null;
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
}

// Capstone Project Template Generator
class CapstoneTemplateGenerator
{
    private array $templates = [];
    
    public function __construct()
    {
        $this->initializeTemplates();
    }
    
    /**
     * Initialize templates
     */
    private function initializeTemplates(): void
    {
        $this->templates = [
            'mvc_structure' => [
                'controller_template' => '<?php
<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Middleware\AuthMiddleware;

class UserController extends BaseController
{
    private UserService $userService;
    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function index(): void
    {
        $users = $this->userService->getAllUsers();
        $this->view(\'users/index\', [\'users\' => $users]);
    }
    
    public function create(): void
    {
        if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
            $data = $this->validateUserInput($_POST);
            $user = $this->userService->createUser($data);
            $this->redirect(\'/users\');
        }
        
        $this->view(\'users/create\');
    }
    
    public function edit(int $id): void
    {
        $user = $this->userService->getUserById($id);
        
        if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
            $data = $this->validateUserInput($_POST);
            $this->userService->updateUser($id, $data);
            $this->redirect(\'/users\');
        }
        
        $this->view(\'users/edit\', [\'user\' => $user]);
    }
    
    public function delete(int $id): void
    {
        $this->userService->deleteUser($id);
        $this->redirect(\'/users\');
    }
    
    private function validateUserInput(array $data): array
    {
        $errors = [];
        
        if (empty($data[\'name\'])) {
            $errors[\'name\'] = \'Name is required\';
        }
        
        if (empty($data[\'email\'])) {
            $errors[\'email\'] = \'Email is required\';
        } elseif (!filter_var($data[\'email\'], FILTER_VALIDATE_EMAIL)) {
            $errors[\'email\'] = \'Invalid email format\';
        }
        
        if (!empty($errors)) {
            $_SESSION[\'errors\'] = $errors;
            $_SESSION[\'old_input\'] = $data;
            throw new ValidationException(\'Validation failed\');
        }
        
        return [
            \'name\' => htmlspecialchars(trim($data[\'name\'])),
            \'email\' => filter_var(trim($data[\'email\']), FILTER_SANITIZE_EMAIL),
            \'password\' => password_hash($data[\'password\'], PASSWORD_DEFAULT)
        ];
    }
}',
                'model_template' => '<?php
<?php
namespace App\Models;

use PDO;
use PDOException;

class User
{
    private PDO $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public function create(array $data): int
    {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data[\'name\'], $data[\'email\'], $data[\'password\']]);
        return (int) $this->db->lastInsertId();
    }
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }
    
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }
    
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        if (isset($data[\'name\'])) {
            $fields[] = \'name = ?\';
            $values[] = $data[\'name\'];
        }
        
        if (isset($data[\'email\'])) {
            $fields[] = \'email = ?\';
            $values[] = $data[\'email\'];
        }
        
        if (isset($data[\'password\'])) {
            $fields[] = \'password = ?\';
            $values[] = $data[\'password\'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = \'updated_at = NOW()\';
        $values[] = $id;
        
        $sql = "UPDATE users SET " . implode(\', \', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete(int $id): bool
    {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function all(): array
    {
        $sql = "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}',
                'service_template' => '<?php
<?php
namespace App\Services;

use App\Models\User;
use App\Validators\UserValidator;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;

class UserService
{
    private User $userModel;
    private UserValidator $validator;
    
    public function __construct(User $userModel, UserValidator $validator)
    {
        $this->userModel = $userModel;
        $this->validator = $validator;
    }
    
    public function createUser(array $data): array
    {
        $this->validator->validateCreate($data);
        
        // Check if email already exists
        if ($this->userModel->findByEmail($data[\'email\'])) {
            throw new \InvalidArgumentException(\'Email already exists\');
        }
        
        $userId = $this->userModel->create($data);
        $user = $this->userModel->findById($userId);
        
        // Fire event
        event(new UserCreated($user));
        
        return $user;
    }
    
    public function getUserById(int $id): array
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            throw new \InvalidArgumentException(\'User not found\');
        }
        
        return $user;
    }
    
    public function updateUser(int $id, array $data): array
    {
        $this->validator->validateUpdate($data);
        
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            throw new \InvalidArgumentException(\'User not found\');
        }
        
        // Check if email is being changed and if it already exists
        if (isset($data[\'email\']) && $data[\'email\'] !== $user[\'email\']) {
            if ($this->userModel->findByEmail($data[\'email\'])) {
                throw new \InvalidArgumentException(\'Email already exists\');
            }
        }
        
        $this->userModel->update($id, $data);
        $updatedUser = $this->userModel->findById($id);
        
        // Fire event
        event(new UserUpdated($updatedUser));
        
        return $updatedUser;
    }
    
    public function deleteUser(int $id): bool
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            throw new \InvalidArgumentException(\'User not found\');
        }
        
        $result = $this->userModel->delete($id);
        
        if ($result) {
            // Fire event
            event(new UserDeleted($user));
        }
        
        return $result;
    }
    
    public function getAllUsers(): array
    {
        return $this->userModel->all();
    }
}'
            ],
            'database_schema' => [
                'users_table' => 'CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(500) NULL,
    bio TEXT NULL,
    status ENUM("active", "inactive", "suspended") DEFAULT "active",
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at)
);',
                'posts_table' => 'CREATE TABLE posts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    featured_image VARCHAR(500) NULL,
    status ENUM("draft", "published", "archived") DEFAULT "draft",
    published_at TIMESTAMP NULL,
    view_count BIGINT DEFAULT 0,
    like_count INT DEFAULT 0,
    comment_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_slug (slug),
    INDEX idx_deleted_at (deleted_at),
    FULLTEXT idx_title_content (title, content)
);',
                'categories_table' => 'CREATE TABLE categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    parent_id BIGINT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_sort_order (sort_order)
);',
                'comments_table' => 'CREATE TABLE comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    parent_id BIGINT NULL,
    content TEXT NOT NULL,
    status ENUM("pending", "approved", "rejected") DEFAULT "pending",
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_user_id (user_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);'
            ],
            'api_endpoints' => [
                'authentication' => [
                    'POST /api/v1/auth/register' => 'User registration',
                    'POST /api/v1/auth/login' => 'User login',
                    'POST /api/v1/auth/logout' => 'User logout',
                    'POST /api/v1/auth/refresh' => 'Refresh token',
                    'POST /api/v1/auth/forgot-password' => 'Forgot password',
                    'POST /api/v1/auth/reset-password' => 'Reset password'
                ],
                'users' => [
                    'GET /api/v1/users' => 'Get all users (paginated)',
                    'GET /api/v1/users/{id}' => 'Get user by ID',
                    'PUT /api/v1/users/{id}' => 'Update user',
                    'DELETE /api/v1/users/{id}' => 'Delete user',
                    'GET /api/v1/users/me' => 'Get current user',
                    'POST /api/v1/users/{id}/follow' => 'Follow user',
                    'DELETE /api/v1/users/{id}/follow' => 'Unfollow user'
                ],
                'posts' => [
                    'GET /api/v1/posts' => 'Get all posts (paginated)',
                    'GET /api/v1/posts/{id}' => 'Get post by ID',
                    'POST /api/v1/posts' => 'Create post',
                    'PUT /api/v1/posts/{id}' => 'Update post',
                    'DELETE /api/v1/posts/{id}' => 'Delete post',
                    'POST /api/v1/posts/{id}/like' => 'Like post',
                    'DELETE /api/v1/posts/{id}/like' => 'Unlike post'
                ]
            ]
        ];
    }
    
    /**
     * Get template
     */
    public function getTemplate(string $category, string $template): ?string
    {
        return $this->templates[$category][$template] ?? null;
    }
    
    /**
     * Get all templates
     */
    public function getAllTemplates(): array
    {
        return $this->templates;
    }
}

// Capstone Project Examples
class CapstoneProjectExamples
{
    private CapstoneProjectManager $manager;
    private CapstoneTemplateGenerator $templates;
    
    public function __construct()
    {
        $this->manager = new CapstoneProjectManager();
        $this->templates = new CapstoneTemplateGenerator();
    }
    
    public function demonstrateProjectOverview(): void
    {
        echo "Capstone Project Overview\n";
        echo str_repeat("-", 28) . "\n";
        
        $requirements = $this->manager->getProjectRequirements();
        
        echo "Project: {$requirements['title']}\n";
        echo "Difficulty: {$requirements['difficulty']}\n";
        echo "Estimated Time: {$requirements['estimated_time']}\n";
        echo "Description: {$requirements['description']}\n\n";
        
        echo "Objectives:\n";
        foreach ($requirements['objectives'] as $objective) {
            echo "  • $objective\n";
        }
        
        echo "\nCore Features:\n";
        foreach ($requirements['core_features'] as $feature => $details) {
            echo "$feature:\n";
            foreach ($details as $item) {
                echo "  • $item\n";
            }
            echo "\n";
        }
        
        echo "Technical Requirements:\n";
        foreach ($requirements['technical_requirements'] as $category => $items) {
            echo ucfirst(str_replace('_', ' ', $category)) . ":\n";
            foreach ($items as $item) {
                echo "  • $item\n";
            }
            echo "\n";
        }
        
        echo "Deliverables:\n";
        foreach ($requirements['deliverables'] as $deliverable) {
            echo "  • $deliverable\n";
        }
    }
    
    public function demonstrateProjectStructure(): void
    {
        echo "\nProject Structure\n";
        echo str_repeat("-", 19) . "\n";
        
        $structure = $this->manager->getProjectStructure();
        
        $this->displayDirectoryStructure($structure['root']);
    }
    
    /**
     * Display directory structure recursively
     */
    private function displayDirectoryStructure(array $directory, int $level = 0): void
    {
        foreach ($directory as $name => $content) {
            $indent = str_repeat('  ', $level);
            
            if (is_array($content)) {
                echo "$indent$name/\n";
                $this->displayDirectoryStructure($content, $level + 1);
            } else {
                echo "$indent$name: $content\n";
            }
        }
    }
    
    public function demonstrateEvaluationCriteria(): void
    {
        echo "\nEvaluation Criteria\n";
        echo str_repeat("-", 20) . "\n";
        
        $criteria = $this->manager->getEvaluationCriteria();
        
        foreach ($criteria as $criterion => $details) {
            echo "$criterion ({$details['weight']}%):\n";
            echo "  Description: {$details['description']}\n";
            echo "  Subcriteria:\n";
            foreach ($details['subcriteria'] as $sub => $weight) {
                echo "    • $sub: $weight%\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateProjectSubmission(): void
    {
        echo "\nProject Submission Example\n";
        echo str_repeat("-", 28) . "\n";
        
        // Simulate submitting a capstone project
        $submission = [
            'student_name' => 'John Doe',
            'project_title' => 'Complete Blog Platform',
            'core_features' => [
                'user_authentication',
                'post_management',
                'comment_system',
                'admin_dashboard',
                'search_functionality',
                'media_uploads'
            ],
            'additional_features' => [
                'social_sharing',
                'email_notifications',
                'analytics_dashboard',
                'api_endpoints'
            ],
            'user_experience' => 'excellent',
            'code_structure' => 'well_organized',
            'documentation' => 'comprehensive',
            'error_handling' => 'robust',
            'authentication' => 'secure',
            'input_validation' => 'comprehensive',
            'data_protection' => 'implemented',
            'schema_design' => 'normalized',
            'optimization' => 'optimized',
            'relationships' => 'properly_defined',
            'mvc_implementation' => 'proper',
            'design_patterns' => ['singleton', 'factory', 'observer', 'repository'],
            'scalability' => 'considered',
            'files' => [
                'README.md',
                'composer.json',
                'database/schema.sql',
                'src/Controllers/UserController.php',
                'src/Models/User.php',
                'src/Services/UserService.php',
                'docs/api.md'
            ]
        ];
        
        echo "Submitting Capstone Project...\n";
        $this->manager->submitProject($submission);
        
        // Get evaluation results
        $evaluations = $this->manager->getAllEvaluations();
        $evaluation = end($evaluations);
        
        echo "\nEvaluation Results:\n";
        echo "Total Score: " . round($evaluation['total_score'], 1) . "%\n";
        echo "Grade: {$evaluation['grade']}\n";
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
    
    public function demonstrateTemplates(): void
    {
        echo "\nProject Templates\n";
        echo str_repeat("-", 18) . "\n";
        
        echo "MVC Controller Template:\n";
        echo substr($this->templates->getTemplate('mvc_structure', 'controller_template'), 0, 500) . "...\n\n";
        
        echo "Model Template:\n";
        echo substr($this->templates->getTemplate('mvc_structure', 'model_template'), 0, 500) . "...\n\n";
        
        echo "Service Template:\n";
        echo substr($this->templates->getTemplate('mvc_structure', 'service_template'), 0, 500) . "...\n\n";
        
        echo "Database Schema Sample:\n";
        echo substr($this->templates->getTemplate('database_schema', 'users_table'), 0, 300) . "...\n\n";
        
        echo "API Endpoints Sample:\n";
        foreach ($this->templates->getTemplate('api_endpoints', 'authentication') as $endpoint => $description) {
            echo "$endpoint: $description\n";
        }
    }
    
    public function demonstrateProjectGuidelines(): void
    {
        echo "\nProject Development Guidelines\n";
        echo str_repeat("-", 32) . "\n";
        
        echo "1. Planning Phase\n";
        echo "   • Read all requirements carefully\n";
        echo "   • Create project roadmap\n";
        echo "   • Design database schema\n";
        echo "   • Plan API endpoints\n";
        echo "   • Choose technology stack\n\n";
        
        echo "2. Development Phase\n";
        echo "   • Set up project structure\n";
        echo "   • Implement core functionality first\n";
        echo "   • Follow MVC pattern\n";
        echo "   • Use dependency injection\n";
        echo "   • Implement security measures\n";
        echo "   • Add error handling\n";
        echo "   • Write tests\n\n";
        
        echo "3. Testing Phase\n";
        echo "   • Unit testing\n";
        echo "   • Integration testing\n";
        echo "   • Security testing\n";
        echo "   • Performance testing\n";
        echo "   • User acceptance testing\n\n";
        
        echo "4. Documentation Phase\n";
        echo "   • Write comprehensive README\n";
        echo "   • Document API endpoints\n";
        echo "   • Create user manual\n";
        echo "   • Add code comments\n";
        echo "   • Document architecture decisions\n\n";
        
        echo "5. Deployment Phase\n";
        echo "   • Prepare deployment guide\n";
        echo "   • Set up environment\n";
        echo "   • Configure database\n";
        echo "   • Deploy application\n";
        echo "   • Test in production\n\n";
        
        echo "Quality Standards:\n";
        echo "• Code must be well-organized and maintainable\n";
        echo "• Follow PSR coding standards\n";
        echo "• Implement proper error handling\n";
        echo "• Use secure coding practices\n";
        echo "• Include comprehensive tests\n";
        echo "• Document all components\n";
        echo "• Optimize for performance\n";
        echo "• Design for scalability";
    }
    
    public function demonstrateSuccessCriteria(): void
    {
        echo "\nSuccess Criteria\n";
        echo str_repeat("-", 17) . "\n";
        
        echo "To successfully complete the capstone project:\n\n";
        
        echo "Functional Requirements (30%):\n";
        echo "• All core features implemented and working\n";
        echo "• Additional features add value\n";
        echo "• User experience is intuitive and responsive\n";
        echo "• No critical bugs in core functionality\n\n";
        
        echo "Code Quality (25%):\n";
        echo "• Well-organized and maintainable code\n";
        echo("• Follows PSR coding standards\n");
        echo "• Comprehensive documentation\n";
        echo "• Robust error handling\n\n";
        
        echo "Security (20%):\n";
        echo "• Secure authentication and authorization\n";
        echo "• Input validation and sanitization\n";
        echo "• Protection against common vulnerabilities\n";
        echo "• Data encryption and protection\n\n";
        
        echo "Database Design (15%):\n";
        echo "• Properly normalized schema\n";
        echo "• Optimized queries and indexes\n";
        echo "• Well-defined relationships\n";
        echo "• Efficient data access patterns\n\n";
        
        echo "Architecture (10%):\n";
        echo "• Proper MVC implementation\n";
        echo "• Use of appropriate design patterns\n";
        echo "• Consideration for scalability\n";
        echo "• Clean separation of concerns\n\n";
        
        echo "Minimum Passing Score: 70%\n";
        echo "Grade A+: 90%+ (Excellent)\n";
        echo "Grade A: 85-89% (Very Good)\n";
        echo "Grade B+: 80-84% (Good)\n";
        echo "Grade B: 75-79% (Satisfactory)\n";
        echo "Grade C+: 70-74% (Passing)\n";
        echo "Grade C: 65-69% (Needs Improvement)\n";
        echo "Grade D: 60-64% (Poor)\n";
        echo "Grade F: <60% (Failing)";
    }
    
    public function runAllExamples(): void
    {
        echo "Capstone Project Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateProjectOverview();
        $this->demonstrateProjectStructure();
        $this->demonstrateEvaluationCriteria();
        $this->demonstrateProjectSubmission();
        $this->demonstrateTemplates();
        $this->demonstrateProjectGuidelines();
        $this->demonstrateSuccessCriteria();
    }
}

// Main execution
function runCapstoneProjectDemo(): void
{
    $examples = new CapstoneProjectExamples();
    $examples->runAllExamples();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCapstoneProjectDemo();
}
?>
