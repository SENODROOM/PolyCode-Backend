<?php
/**
 * Code Review Challenge
 * 
 * Comprehensive code review exercises to test understanding
 * of best practices, security, and optimization.
 */

// Code Review Framework
class CodeReviewFramework
{
    private array $codeSnippets = [];
    private array $reviews = [];
    private array $issues = [];
    private array $solutions = [];
    
    public function __construct()
    {
        $this->initializeCodeSnippets();
    }
    
    /**
     * Initialize code snippets for review
     */
    private function initializeCodeSnippets(): void
    {
        $this->codeSnippets = [
            'security_vulnerabilities' => [
                'title' => 'Security Vulnerabilities Review',
                'description' => 'Review code for security issues and suggest improvements',
                'difficulty' => 'medium',
                'code' => '<?php
// Login script with security issues
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM users WHERE username = \'$username\' AND password = \'$password\'";
$result = mysql_query($sql);

if ($row = mysql_fetch_assoc($result)) {
    $_SESSION["user"] = $row;
    header("Location: dashboard.php");
} else {
    echo "Invalid login";
}',
                'issues' => [
                    'sql_injection' => 'Direct SQL injection vulnerability',
                    'plaintext_password' => 'Password stored in plain text',
                    'deprecated_mysql' => 'Using deprecated mysql_* functions',
                    'no_input_validation' => 'No input validation',
                    'no_output_escaping' => 'Potential XSS in output'
                ],
                'solutions' => [
                    'Use prepared statements',
                    'Hash passwords with password_hash()',
                    'Use PDO or MySQLi',
                    'Validate and sanitize input',
                    'Escape output properly'
                ]
            ],
            'code_quality' => [
                'title' => 'Code Quality Review',
                'description' => 'Review code for quality, maintainability, and best practices',
                'difficulty' => 'easy',
                'code' => '<?php
function getUserData($id) {
    $data = array();
    
    $conn = mysql_connect("localhost", "root", "");
    mysql_select_db("myapp", $conn);
    
    $result = mysql_query("SELECT * FROM users WHERE id = " . $id);
    
    while($row = mysql_fetch_array($result)) {
        $data[] = $row;
    }
    
    return $data;
}

function updateUser($id, $name, $email) {
    $conn = mysql_connect("localhost", "root", "");
    mysql_select_db("myapp", $conn);
    
    $sql = "UPDATE users SET name = \'$name\', email = \'$email\' WHERE id = $id";
    return mysql_query($sql);
}',
                'issues' => [
                    'deprecated_database' => 'Using deprecated mysql_* functions',
                    'no_error_handling' => 'No error handling for database operations',
                    'sql_injection_risk' => 'Potential SQL injection',
                    'no_input_validation' => 'No input validation',
                    'poor_naming' => 'Inconsistent naming conventions',
                    'no_documentation' => 'Missing function documentation'
                ],
                'solutions' => [
                    'Use PDO or MySQLi with prepared statements',
                    'Add proper error handling with try-catch',
                    'Validate all input parameters',
                    'Use consistent naming conventions',
                    'Add PHPDoc comments',
                    'Use dependency injection for database'
                ]
            ],
            'performance_optimization' => [
                'title' => 'Performance Optimization Review',
                'description' => 'Review code for performance issues and optimization opportunities',
                'difficulty' => 'hard',
                'code' => '<?php
// Inefficient data processing
function processLargeDataset($data) {
    $results = array();
    
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]["items"]); $j++) {
            $item = $data[$i]["items"][$j];
            
            // Expensive operation in nested loop
            $processed = expensiveOperation($item);
            
            if ($processed["status"] == "active") {
                $results[] = $processed;
            }
        }
    }
    
    return $results;
}

function expensiveOperation($item) {
    // Simulate expensive processing
    sleep(0.1);
    
    $result = array();
    $result["id"] = $item["id"];
    $result["status"] = $item["status"];
    $result["processed_at"] = date("Y-m-d H:i:s");
    
    // Database query in loop
    $conn = mysql_connect("localhost", "root", "");
    mysql_select_db("myapp", $conn);
    
    $sql = "UPDATE items SET processed = 1 WHERE id = " . $item["id"];
    mysql_query($sql);
    
    return $result;
}',
                'issues' => [
                    'nested_loops' => 'O(n²) complexity with nested loops',
                    'database_in_loop' => 'Database queries inside loops',
                    'no_caching' => 'No caching for expensive operations',
                    'blocking_operations' => 'Synchronous expensive operations',
                    'memory_usage' => 'High memory usage with large arrays',
                    'no_batch_processing' => 'Processing items one by one'
                ],
                'solutions' => [
                    'Use array_map/filter for better performance',
                    'Batch database operations',
                    'Implement caching mechanism',
                    'Use asynchronous processing',
                    'Process data in chunks',
                    'Use generators for memory efficiency'
                ]
            ],
            'oop_principles' => [
                'title' => 'OOP Principles Review',
                'description' => 'Review code for object-oriented programming principles',
                'difficulty' => 'medium',
                'code' => '<?php
class UserManager {
    public $db;
    public $logger;
    
    public function __construct() {
        $this->db = new Database();
        $this->logger = new Logger();
    }
    
    public function createUser($userData) {
        $user = new User();
        $user->setName($userData["name"]);
        $user->setEmail($userData["email"]);
        $user->setPassword($userData["password"]);
        
        $userId = $this->db->insert("users", $user->toArray());
        
        // Direct dependency on email service
        $emailService = new EmailService();
        $emailService->sendWelcomeEmail($user->getEmail());
        
        // Direct dependency on notification service
        $notificationService = new NotificationService();
        $notificationService->sendNotification("User created: " . $user->getName());
        
        $this->logger->log("User created: " . $userId);
        
        return $userId;
    }
    
    public function deleteUser($userId) {
        $this->db->delete("users", "id = " . $userId);
        
        // More direct dependencies
        $emailService = new EmailService();
        $emailService->sendGoodbyeEmail($userId);
        
        $this->logger->log("User deleted: " . $userId);
    }
}',
                'issues' => [
                    'tight_coupling' => 'Direct dependency instantiation',
                    'single_responsibility' => 'Class doing too many things',
                    'dependency_injection' => 'No dependency injection',
                    'solid_principles' => 'Violates SOLID principles',
                    'hardcoded_dependencies' => 'Hardcoded class dependencies',
                    'no_abstraction' => 'No abstraction for external services'
                ],
                'solutions' => [
                    'Use dependency injection',
                    'Implement service container',
                    'Separate concerns into different classes',
                    'Use interfaces for abstraction',
                    'Apply SOLID principles',
                    'Use factory pattern for object creation'
                ]
            ],
            'api_design' => [
                'title' => 'API Design Review',
                'description' => 'Review API design for RESTful principles and best practices',
                'difficulty' => 'medium',
                'code' => '<?php
// API endpoints with design issues
class UserAPI {
    public function handleRequest() {
        $action = $_GET["action"];
        
        switch ($action) {
            case "create":
                return $this->createUser();
            case "update":
                return $this->updateUser();
            case "delete":
                return $this->deleteUser();
            case "get":
                return $this->getUser();
            default:
                return "Invalid action";
        }
    }
    
    public function createUser() {
        $name = $_POST["name"];
        $email = $_POST["email"];
        
        // Direct database access
        $db = new Database();
        $userId = $db->insert("users", [
            "name" => $name,
            "email" => $email
        ]);
        
        return [
            "success" => true,
            "user_id" => $userId,
            "message" => "User created successfully"
        ];
    }
    
    public function updateUser() {
        $id = $_POST["id"];
        $name = $_POST["name"];
        $email = $_POST["email"];
        
        $db = new Database();
        $db->update("users", [
            "name" => $name,
            "email" => $email
        ], "id = " . $id);
        
        return [
            "success" => true,
            "message" => "User updated successfully"
        ];
    }
    
    public function deleteUser() {
        $id = $_GET["id"];
        
        $db = new Database();
        $db->delete("users", "id = " . $id);
        
        return [
            "success" => true,
            "message" => "User deleted successfully"
        ];
    }
    
    public function getUser() {
        $id = $_GET["id"];
        
        $db = new Database();
        $user = $db->selectOne("users", "id = " . $id);
        
        return [
            "success" => true,
            "user" => $user
        ];
    }
}',
                'issues' => [
                    'not_restful' => 'Not following RESTful principles',
                    'wrong_http_methods' => 'Using GET for delete operations',
                    'no_http_status_codes' => 'Not using proper HTTP status codes',
                    'no_error_handling' => 'No proper error handling',
                    'no_input_validation' => 'No input validation',
                    'no_authentication' => 'No authentication/authorization',
                    'no_api_versioning' => 'No API versioning',
                    'inconsistent_response_format' => 'Inconsistent response format'
                ],
                'solutions' => [
                    'Use proper HTTP methods (GET, POST, PUT, DELETE)',
                    'Implement proper HTTP status codes',
                    'Add authentication and authorization',
                    'Use consistent response format',
                    'Add input validation',
                    'Implement error handling',
                    'Use API versioning',
                    'Follow RESTful principles'
                ]
            ]
        ];
    }
    
    /**
     * Get code snippet
     */
    public function getCodeSnippet(string $id): ?array
    {
        return $this->codeSnippets[$id] ?? null;
    }
    
    /**
     * Get all code snippets
     */
    public function getAllCodeSnippets(): array
    {
        return $this->codeSnippets;
    }
    
    /**
     * Review code snippet
     */
    public function reviewCode(string $snippetId, array $findings): array
    {
        $snippet = $this->codeSnippets[$snippetId];
        
        $review = [
            'snippet_id' => $snippetId,
            'title' => $snippet['title'],
            'reviewed_at' => time(),
            'findings' => $findings,
            'score' => $this->calculateReviewScore($findings, $snippet),
            'recommendations' => $this->generateRecommendations($findings, $snippet),
            'passed' => false
        ];
        
        $review['passed'] = $review['score'] >= 70;
        
        $this->reviews[$snippetId] = $review;
        
        return $review;
    }
    
    /**
     * Calculate review score
     */
    private function calculateReviewScore(array $findings, array $snippet): int
    {
        $totalIssues = count($snippet['issues']);
        $foundIssues = count($findings);
        
        // Base score starts at 100
        $score = 100;
        
        // Deduct points for missed issues
        $missedIssues = $totalIssues - $foundIssues;
        $score -= $missedIssues * 10;
        
        // Bonus for finding additional issues
        if ($foundIssues > $totalIssues) {
            $score += 5;
        }
        
        return max($score, 0);
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $findings, array $snippet): array
    {
        $recommendations = [];
        
        foreach ($findings as $issue) {
            if (isset($snippet['solutions'][$issue])) {
                $recommendations[] = $snippet['solutions'][$issue];
            }
        }
        
        return array_unique($recommendations);
    }
    
    /**
     * Get review
     */
    public function getReview(string $snippetId): ?array
    {
        return $this->reviews[$snippetId] ?? null;
    }
    
    /**
     * Get all reviews
     */
    public function getAllReviews(): array
    {
        return $this->reviews;
    }
    
    /**
     * Calculate overall score
     */
    public function calculateOverallScore(): array
    {
        $totalScore = 0;
        $passedReviews = 0;
        $totalReviews = count($this->reviews);
        
        foreach ($this->reviews as $review) {
            $totalScore += $review['score'];
            if ($review['passed']) {
                $passedReviews++;
            }
        }
        
        $averageScore = $totalReviews > 0 ? $totalScore / $totalReviews : 0;
        
        return [
            'average_score' => $averageScore,
            'passed_reviews' => $passedReviews,
            'total_reviews' => $totalReviews,
            'pass_rate' => $totalReviews > 0 ? ($passedReviews / $totalReviews) * 100 : 0,
            'overall_passed' => $passedReviews >= 3 // Require at least 3 passed reviews
        ];
    }
}

// Code Review Challenge Manager
class CodeReviewChallenge
{
    private CodeReviewFramework $framework;
    private array $challenges = [];
    private array $submissions = [];
    
    public function __construct()
    {
        $this->framework = new CodeReviewFramework();
        $this->initializeChallenges();
    }
    
    /**
     * Initialize challenges
     */
    private function initializeChallenges(): void
    {
        $this->challenges = [
            'security_focus' => [
                'title' => 'Security-Focused Review',
                'description' => 'Focus on identifying and fixing security vulnerabilities',
                'focus_areas' => ['sql_injection', 'xss', 'authentication', 'input_validation'],
                'time_limit' => 30, // minutes
                'required_findings' => 3
            ],
            'performance_focus' => [
                'title' => 'Performance-Focused Review',
                'description' => 'Focus on identifying performance bottlenecks and optimization opportunities',
                'focus_areas' => ['loops', 'database_queries', 'memory_usage', 'caching'],
                'time_limit' => 45,
                'required_findings' => 4
            ],
            'code_quality_focus' => [
                'title' => 'Code Quality Review',
                'description' => 'Focus on code quality, maintainability, and best practices',
                'focus_areas' => ['naming_conventions', 'documentation', 'error_handling', 'structure'],
                'time_limit' => 30,
                'required_findings' => 3
            ],
            'comprehensive_review' => [
                'title' => 'Comprehensive Review',
                'description' => 'Review all aspects of the code',
                'focus_areas' => ['all'],
                'time_limit' => 60,
                'required_findings' => 5
            ]
        ];
    }
    
    /**
     * Get challenges
     */
    public function getChallenges(): array
    {
        return $this->challenges;
    }
    
    /**
     * Start challenge
     */
    public function startChallenge(string $challengeId, string $snippetId): array
    {
        $challenge = $this->challenges[$challengeId];
        $snippet = $this->framework->getCodeSnippet($snippetId);
        
        if (!$challenge || !$snippet) {
            throw new Exception('Invalid challenge or snippet ID');
        }
        
        return [
            'challenge_id' => $challengeId,
            'snippet_id' => $snippetId,
            'title' => $challenge['title'],
            'description' => $challenge['description'],
            'code' => $snippet['code'],
            'focus_areas' => $challenge['focus_areas'],
            'time_limit' => $challenge['time_limit'],
            'required_findings' => $challenge['required_findings'],
            'started_at' => time()
        ];
    }
    
    /**
     * Submit challenge findings
     */
    public function submitChallenge(string $challengeId, string $snippetId, array $findings): array
    {
        $review = $this->framework->reviewCode($snippetId, $findings);
        
        $challenge = $this->challenges[$challengeId];
        
        $submission = [
            'challenge_id' => $challengeId,
            'snippet_id' => $snippetId,
            'findings' => $findings,
            'review' => $review,
            'submitted_at' => time(),
            'passed' => $review['passed'] && count($findings) >= $challenge['required_findings']
        ];
        
        $this->submissions[$challengeId . '_' . $snippetId] = $submission;
        
        return $submission;
    }
    
    /**
     * Get submission
     */
    public function getSubmission(string $challengeId, string $snippetId): ?array
    {
        return $this->submissions[$challengeId . '_' . $snippetId] ?? null;
    }
    
    /**
     * Get all submissions
     */
    public function getAllSubmissions(): array
    {
        return $this->submissions;
    }
    
    /**
     * Generate improvement suggestions
     */
    public function generateImprovementSuggestions(array $submission): array
    {
        $suggestions = [];
        
        if (!$submission['passed']) {
            $review = $submission['review'];
            
            if ($review['score'] < 70) {
                $suggestions[] = 'Study the code more carefully to identify all issues';
                $suggestions[] = 'Focus on the specific areas mentioned in the challenge';
                $suggestions[] = 'Review best practices for the identified issues';
            }
            
            if (count($submission['findings']) < $submission['review']['score'] / 10) {
                $suggestions[] = 'Look for more issues in the code';
                $suggestions[] = 'Consider edge cases and potential vulnerabilities';
            }
        }
        
        return $suggestions;
    }
}

// Code Review Examples
class CodeReviewExamples
{
    private CodeReviewFramework $framework;
    private CodeReviewChallenge $challenge;
    
    public function __construct()
    {
        $this->framework = new CodeReviewFramework();
        $this->challenge = new CodeReviewChallenge();
    }
    
    public function demonstrateCodeReview(): void
    {
        echo "Code Review Challenge Examples\n";
        echo str_repeat("-", 32) . "\n";
        
        // Show available code snippets
        $snippets = $this->framework->getAllCodeSnippets();
        
        echo "Available Code Snippets:\n\n";
        
        foreach ($snippets as $id => $snippet) {
            echo "$id: {$snippet['title']}\n";
            echo "  Difficulty: {$snippet['difficulty']}\n";
            echo "  Description: {$snippet['description']}\n";
            echo "  Issues to find: " . implode(', ', array_keys($snippet['issues'])) . "\n\n";
        }
        
        // Demonstrate reviewing a security snippet
        echo "Reviewing Security Vulnerabilities Example:\n";
        echo str_repeat("-", 40) . "\n";
        
        $snippet = $this->framework->getCodeSnippet('security_vulnerabilities');
        echo "Code to Review:\n";
        echo substr($snippet['code'], 0, 500) . "...\n\n";
        
        echo "Expected Issues:\n";
        foreach ($snippet['issues'] as $issue => $description) {
            echo "  • $issue: $description\n";
        }
        
        // Simulate finding issues
        $findings = ['sql_injection', 'plaintext_password', 'deprecated_mysql', 'no_input_validation'];
        
        echo "\nFound Issues:\n";
        foreach ($findings as $finding) {
            echo "  • $finding\n";
        }
        
        // Review the code
        $review = $this->framework->reviewCode('security_vulnerabilities', $findings);
        
        echo "\nReview Results:\n";
        echo "Score: {$review['score']}%\n";
        echo "Status: " . ($review['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        echo "\nRecommendations:\n";
        foreach ($review['recommendations'] as $recommendation) {
            echo "  • $recommendation\n";
        }
    }
    
    public function demonstrateChallengeMode(): void
    {
        echo "\nChallenge Mode Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $challenges = $this->challenge->getChallenges();
        
        echo "Available Challenges:\n\n";
        
        foreach ($challenges as $id => $challenge) {
            echo "$id: {$challenge['title']}\n";
            echo "  Description: {$challenge['description']}\n";
            echo "  Focus Areas: " . implode(', ', $challenge['focus_areas']) . "\n";
            echo "  Time Limit: {$challenge['time_limit']} minutes\n";
            echo "  Required Findings: {$challenge['required_findings']}\n\n";
        }
        
        // Simulate a security challenge
        echo "Starting Security-Focused Challenge...\n";
        
        $challengeData = $this->challenge->startChallenge('security_focus', 'security_vulnerabilities');
        
        echo "Challenge: {$challengeData['title']}\n";
        echo "Focus: " . implode(', ', $challengeData['focus_areas']) . "\n";
        echo "Time Limit: {$challengeData['time_limit']} minutes\n";
        echo "Required Findings: {$challengeData['required_findings']}\n\n";
        
        // Simulate submission
        $findings = ['sql_injection', 'plaintext_password', 'no_input_validation', 'xss_protection'];
        
        echo "Submitting Findings:\n";
        foreach ($findings as $finding) {
            echo "  • $finding\n";
        }
        
        $submission = $this->challenge->submitChallenge('security_focus', 'security_vulnerabilities', $findings);
        
        echo "\nChallenge Results:\n";
        echo "Score: {$submission['review']['score']}%\n";
        echo "Status: " . ($submission['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        if (!$submission['passed']) {
            $suggestions = $this->challenge->generateImprovementSuggestions($submission);
            echo "\nImprovement Suggestions:\n";
            foreach ($suggestions as $suggestion) {
                echo "  • $suggestion\n";
            }
        }
    }
    
    public function demonstrateMultipleReviews(): void
    {
        echo "\nMultiple Code Reviews\n";
        echo str_repeat("-", 23) . "\n";
        
        $snippets = ['security_vulnerabilities', 'code_quality', 'performance_optimization', 'oop_principles'];
        
        foreach ($snippets as $snippetId) {
            echo "\nReviewing: $snippetId\n";
            echo str_repeat("-", strlen($snippetId) + 10) . "\n";
            
            $snippet = $this->framework->getCodeSnippet($snippetId);
            
            // Simulate different findings for each snippet
            $findings = $this->simulateFindings($snippetId);
            
            echo "Found " . count($findings) . " issues: " . implode(', ', $findings) . "\n";
            
            $review = $this->framework->reviewCode($snippetId, $findings);
            echo "Score: {$review['score']}% - " . ($review['passed'] ? 'PASSED' : 'FAILED') . "\n";
        }
        
        // Calculate overall score
        $overallScore = $this->framework->calculateOverallScore();
        
        echo "\nOverall Review Results:\n";
        echo "Average Score: " . round($overallScore['average_score'], 1) . "%\n";
        echo "Passed Reviews: {$overallScore['passed_reviews']}/{$overallScore['total_reviews']}\n";
        echo "Pass Rate: " . round($overallScore['pass_rate'], 1) . "%\n";
        echo "Overall Status: " . ($overallScore['overall_passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    public function demonstrateReviewGuidelines(): void
    {
        echo "\nCode Review Guidelines\n";
        echo str_repeat("-", 22) . "\n";
        
        echo "What to Look For:\n";
        echo "1. Security Issues:\n";
        echo "   • SQL injection vulnerabilities\n";
        echo "   • XSS vulnerabilities\n";
        echo "   • Authentication flaws\n";
        echo "   • Input validation issues\n";
        echo "   • Authorization problems\n\n";
        
        echo "2. Performance Issues:\n";
        echo "   • Inefficient algorithms\n";
        echo "   • Database query optimization\n";
        echo "   • Memory usage problems\n";
        echo "   • Blocking operations\n";
        echo "   • Caching opportunities\n\n";
        
        echo "3. Code Quality Issues:\n";
        echo "   • Naming conventions\n";
        echo "   • Code organization\n";
        echo "   • Documentation\n";
        echo "   • Error handling\n";
        echo "   • Code duplication\n\n";
        
        echo "4. OOP Principles:\n";
        echo "   • SOLID principles violations\n";
        echo "   • Design pattern misuse\n";
        echo "   • Tight coupling\n";
        echo "   • Single responsibility violations\n";
        echo "   • Dependency injection issues\n\n";
        
        echo "5. API Design Issues:\n";
        echo "   • RESTful principles\n";
        echo "   • HTTP method usage\n";
        echo "   • Status code usage\n";
        echo "   • Response format\n";
        echo "   • Authentication/authorization\n\n";
        
        echo "Review Process:\n";
        echo "1. Read the code carefully\n";
        echo "2. Identify issues by category\n";
        echo "3. Prioritize critical issues\n";
        echo "4. Provide specific recommendations\n";
        echo "5. Suggest concrete solutions\n";
        echo "6. Explain the impact of issues\n\n";
        
        echo "Scoring Criteria:\n";
        echo "• Issue Identification (40%)\n";
        echo "• Accuracy of Findings (30%)\n";
        echo "• Quality of Recommendations (20%)\n";
        echo "• Completeness (10%)\n\n";
        
        echo "Best Practices:\n";
        echo "• Be thorough and systematic\n";
        echo "• Explain why something is an issue\n";
        echo "• Provide actionable recommendations\n";
        echo "• Consider security implications\n";
        echo "• Think about maintainability\n";
        echo "• Consider performance impact";
    }
    
    public function demonstrateCommonIssues(): void
    {
        echo "\nCommon Code Issues and Solutions\n";
        echo str_repeat("-", 35) . "\n";
        
        $commonIssues = [
            'sql_injection' => [
                'description' => 'Direct SQL queries with user input',
                'risk' => 'High',
                'solution' => 'Use prepared statements with parameterized queries',
                'example' => '$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");'
            ],
            'xss_vulnerability' => [
                'description' => 'Outputting user input without escaping',
                'risk' => 'High',
                'solution' => 'Use htmlspecialchars() or template engine auto-escaping',
                'example' => 'echo htmlspecialchars($userInput, ENT_QUOTES, "UTF-8");'
            ],
            'deprecated_functions' => [
                'description' => 'Using deprecated PHP functions',
                'risk' => 'Medium',
                'solution' => 'Use modern alternatives (PDO, MySQLi, password_hash)',
                'example' => 'Use PDO or MySQLi instead of mysql_* functions'
            ],
            'no_input_validation' => [
                'description' => 'Not validating or sanitizing user input',
                'risk' => 'High',
                'solution' => 'Validate all input using filter_var() and custom validation',
                'example' => '$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);'
            ],
            'poor_error_handling' => [
                'description' => 'Not handling errors properly',
                'risk' => 'Medium',
                'solution' => 'Use try-catch blocks and proper error reporting',
                'example' => 'try { $result = riskyOperation(); } catch (Exception $e) { handleError($e); }'
            ],
            'tight_coupling' => [
                'description' => 'Classes depending on concrete implementations',
                'risk' => 'Medium',
                'solution' => 'Use dependency injection and interfaces',
                'example' => 'class UserService { private $emailService; public function __construct(EmailService $emailService) { $this->emailService = $emailService; } }'
            ]
        ];
        
        foreach ($commonIssues as $issue => $details) {
            echo "$issue:\n";
            echo "  Description: {$details['description']}\n";
            echo "  Risk Level: {$details['risk']}\n";
            echo "  Solution: {$details['solution']}\n";
            echo "  Example: {$details['example']}\n\n";
        }
    }
    
    public function demonstrateReviewTools(): void
    {
        echo "\nCode Review Tools and Resources\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "Static Analysis Tools:\n";
        echo "• PHPStan - Static analysis tool for PHP\n";
        echo "• Psalm - Static analysis and type checking\n";
        echo "• PHP_CodeSniffer - Coding standard checker\n";
        echo "• PHPMD - Mess detector for PHP\n";
        echo "• Phan - Static analyzer for PHP\n\n";
        
        echo "\nSecurity Tools:\n";
        echo "• OWASP ZAP - Web application security scanner\n";
        echo "• Burp Suite - Web application security testing\n";
        echo "• Security Code Scanner - Security vulnerability scanner\n";
        echo "• SonarQube - Code quality and security analysis\n";
        echo "• Checkmarx - Application security testing\n\n";
        
        echo "Performance Tools:\n";
        echo "• Xdebug - Debugging and profiling\n";
        echo "• Blackfire - Performance profiling\n";
        echo "• PHP Profiler - Built-in PHP profiler\n";
        echo "• Tideways - Performance monitoring\n";
        echo "• New Relic - Application performance monitoring\n\n";
        
        echo "Code Review Platforms:\n";
        echo "• GitHub Pull Requests - Code review and discussion\n";
        echo "• GitLab Merge Requests - Code review workflow\n";
        echo "• Bitbucket Pull Requests - Code review and approval\n";
        echo "• Phabricator - Code review tool by Facebook\n";
        echo "• Review Board - Enterprise code review platform\n\n";
        
        echo "Best Practices:\n";
        echo "• Use automated tools for initial analysis\n";
        echo "• Combine automated and manual reviews\n";
        echo "• Create checklists for common issues\n";
        echo "• Document review findings and decisions\n";
        echo "• Track metrics and improvement over time";
    }
    
    public function runAllExamples(): void
    {
        echo "Code Review Challenge Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateCodeReview();
        $this->demonstrateChallengeMode();
        $this->demonstrateMultipleReviews();
        $this->demonstrateReviewGuidelines();
        $this->demonstrateCommonIssues();
        $this->demonstrateReviewTools();
    }
    
    /**
     * Simulate findings for demonstration
     */
    private function simulateFindings(string $snippetId): array
    {
        $findings = [
            'security_vulnerabilities' => ['sql_injection', 'plaintext_password', 'deprecated_mysql'],
            'code_quality' => ['deprecated_database', 'no_error_handling', 'poor_naming'],
            'performance_optimization' => ['nested_loops', 'database_in_loop', 'no_caching'],
            'oop_principles' => ['tight_coupling', 'single_responsibility', 'no_dependency_injection'],
            'api_design' => ['not_restful', 'wrong_http_methods', 'no_http_status_codes']
        ];
        
        return $findings[$snippetId] ?? [];
    }
}

// Main execution
function runCodeReviewChallengeDemo(): void
{
    $examples = new CodeReviewExamples();
    $examples->runAllExamples();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCodeReviewChallengeDemo();
}
?>
