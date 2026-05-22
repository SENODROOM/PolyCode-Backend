<?php
/**
 * PHP Knowledge Assessment
 * 
 * Comprehensive evaluation of PHP knowledge covering all modules
 * from fundamentals to advanced topics.
 */

// Knowledge Assessment Framework
class PHPKnowledgeAssessment
{
    private array $questions = [];
    private array $userAnswers = [];
    private array $results = [];
    private array $categories = [];
    
    public function __construct()
    {
        $this->initializeQuestions();
        $this->initializeCategories();
    }
    
    /**
     * Initialize assessment questions
     */
    private function initializeQuestions(): void
    {
        $this->questions = [
            'php_fundamentals' => [
                [
                    'id' => 'pf_001',
                    'question' => 'What is the difference between == and === in PHP?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'easy',
                    'points' => 5,
                    'options' => [
                        'a' => 'No difference',
                        'b' => '== compares value only, === compares value and type',
                        'c' => '=== compares value only, == compares value and type',
                        'd' => 'Both compare value and type'
                    ],
                    'correct_answer' => 'b',
                    'explanation' => '== performs loose comparison with type juggling, while === performs strict comparison checking both value and type.'
                ],
                [
                    'id' => 'pf_002',
                    'question' => 'Which of the following is NOT a valid PHP variable name?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'easy',
                    'points' => 5,
                    'options' => [
                        'a' => '$myVariable',
                        'b' => '$variable_123',
                        'c' => '$123variable',
                        'd' => '$_variable'
                    ],
                    'correct_answer' => 'c',
                    'explanation' => 'PHP variables cannot start with numbers. They must start with a letter or underscore.'
                ],
                [
                    'id' => 'pf_003',
                    'question' => 'What is the purpose of the extract() function in PHP?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'medium',
                    'points' => 10,
                    'options' => [
                        'a' => 'Extract characters from a string',
                        'b' => 'Import variables into the current symbol table from an array',
                        'c' => 'Extract data from database',
                        'd' => 'Extract file contents'
                    ],
                    'correct_answer' => 'b',
                    'explanation' => 'extract() imports variables from an associative array into the current symbol table.'
                ]
            ],
            'oop_concepts' => [
                [
                    'id' => 'oop_001',
                    'question' => 'What is the main difference between abstract classes and interfaces in PHP?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'medium',
                    'points' => 10,
                    'options' => [
                        'a' => 'No difference',
                        'b' => 'Abstract classes can have properties, interfaces cannot',
                        'c' => 'Interfaces can have concrete methods, abstract classes cannot',
                        'd' => 'Classes can implement multiple interfaces but extend only one abstract class'
                    ],
                    'correct_answer' => 'd',
                    'explanation' => 'PHP supports multiple interface implementation but single class inheritance. Abstract classes can have both abstract and concrete methods, while interfaces can only have abstract methods (prior to PHP 8.0).'
                ],
                [
                    'id' => 'oop_002',
                    'question' => 'What are PHP traits and when should you use them?',
                    'type' => 'essay',
                    'difficulty' => 'medium',
                    'points' => 15,
                    'explanation' => 'Traits are a mechanism for code reuse in single inheritance languages. They allow you to reuse sets of methods in multiple independent classes. Use traits when you need to share functionality across unrelated classes without using inheritance.'
                ],
                [
                    'id' => 'oop_003',
                    'question' => 'Explain late static binding and provide an example.',
                    'type' => 'coding',
                    'difficulty' => 'hard',
                    'points' => 20,
                    'explanation' => 'Late static binding allows reference to the called class in context of static inheritance. Using static:: instead of self:: resolves at runtime rather than compile time.'
                ]
            ],
            'frameworks' => [
                [
                    'id' => 'fw_001',
                    'question' => 'What is dependency injection in the context of Laravel?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'medium',
                    'points' => 10,
                    'options' => [
                        'a' => 'Injecting dependencies directly into methods',
                        'b' => 'A design pattern where dependencies are injected into classes rather than created internally',
                        'c' => 'Database dependency management',
                        'd' => 'File dependency management'
                    ],
                    'correct_answer' => 'b',
                    'explanation' => 'Dependency injection is a design pattern where dependencies are injected into classes rather than created internally, improving testability and maintainability.'
                ],
                [
                    'id' => 'fw_002',
                    'question' => 'What is the purpose of middleware in Laravel?',
                    'type' => 'essay',
                    'difficulty' => 'medium',
                    'points' => 15,
                    'explanation' => 'Middleware are classes that filter HTTP requests entering your application. They provide a mechanism for filtering HTTP requests entering your application, useful for authentication, logging, CORS, rate limiting, etc.'
                ]
            ],
            'database' => [
                [
                    'id' => 'db_001',
                    'question' => 'What is the difference between INNER JOIN and LEFT JOIN in SQL?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'easy',
                    'points' => 5,
                    'options' => [
                        'a' => 'No difference',
                        'b' => 'INNER JOIN returns all records, LEFT JOIN returns only matching records',
                        'c' => 'INNER JOIN returns only matching records, LEFT JOIN returns all records from left table',
                        'd' => 'LEFT JOIN returns all records, INNER JOIN returns only records from left table'
                    ],
                    'correct_answer' => 'c',
                    'explanation' => 'INNER JOIN returns only matching records from both tables, while LEFT JOIN returns all records from the left table and matching records from the right table.'
                ],
                [
                    'id' => 'db_002',
                    'question' => 'Explain database indexing and its impact on performance.',
                    'type' => 'essay',
                    'difficulty' => 'medium',
                    'points' => 15,
                    'explanation' => 'Database indexes are data structures that improve the speed of data retrieval operations on database tables. They work like book indexes, allowing the database to find data without scanning the entire table. While they improve read performance, they can slow down write operations.'
                ]
            ],
            'security' => [
                [
                    'id' => 'sec_001',
                    'question' => 'What is SQL injection and how do you prevent it?',
                    'type' => 'essay',
                    'difficulty' => 'medium',
                    'points' => 15,
                    'explanation' => 'SQL injection is an attack where malicious SQL code is inserted into queries. Prevent it using prepared statements with parameterized queries, ORMs, input validation, and least privilege database access.'
                ],
                [
                    'id' => 'sec_002',
                    'question' => 'Explain Cross-Site Scripting (XSS) and prevention methods.',
                    'type' => 'coding',
                    'difficulty' => 'hard',
                    'points' => 20,
                    'explanation' => 'XSS injects malicious scripts into web pages. Prevent it using output encoding, Content Security Policy, input validation, and frameworks with built-in protection.'
                ]
            ],
            'advanced_topics' => [
                [
                    'id' => 'adv_001',
                    'question' => 'What are the key features introduced in PHP 8?',
                    'type' => 'multiple_choice',
                    'difficulty' => 'medium',
                    'points' => 10,
                    'options' => [
                        'a' => 'Union types, named arguments, match expression',
                        'b' => 'Only performance improvements',
                        'c' => 'Removed old features only',
                        'd' => 'No significant changes'
                    ],
                    'correct_answer' => 'a',
                    'explanation' => 'PHP 8 introduced union types, named arguments, match expression, nullsafe operator, constructor property promotion, enums, and more.'
                ],
                [
                    'id' => 'adv_002',
                    'question' => 'Explain the concept of PHP fibers and their use cases.',
                    'type' => 'essay',
                    'difficulty' => 'hard',
                    'points' => 20,
                    'explanation' => 'Fibers are a lightweight concurrency feature introduced in PHP 8.1. They allow for cooperative multitasking, enabling developers to write asynchronous code that looks synchronous, useful for I/O-bound operations.'
                ]
            ]
        ];
    }
    
    /**
     * Initialize categories
     */
    private function initializeCategories(): void
    {
        $this->categories = [
            'php_fundamentals' => [
                'name' => 'PHP Fundamentals',
                'description' => 'Basic PHP syntax, variables, operators, and control structures',
                'weight' => 20,
                'required_score' => 70
            ],
            'oop_concepts' => [
                'name' => 'Object-Oriented Programming',
                'description' => 'Classes, objects, inheritance, polymorphism, and design patterns',
                'weight' => 25,
                'required_score' => 70
            ],
            'frameworks' => [
                'name' => 'PHP Frameworks',
                'description' => 'Laravel, Symfony, and other framework knowledge',
                'weight' => 15,
                'required_score' => 65
            ],
            'database' => [
                'name' => 'Database Management',
                'description' => 'SQL, database design, and optimization',
                'weight' => 15,
                'required_score' => 70
            ],
            'security' => [
                'name' => 'Security',
                'description' => 'Common vulnerabilities and security best practices',
                'weight' => 15,
                'required_score' => 75
            ],
            'advanced_topics' => [
                'name' => 'Advanced Topics',
                'description' => 'PHP 8+ features, performance optimization, and advanced concepts',
                'weight' => 10,
                'required_score' => 65
            ]
        ];
    }
    
    /**
     * Get questions by category
     */
    public function getQuestionsByCategory(string $category): array
    {
        return $this->questions[$category] ?? [];
    }
    
    /**
     * Get all questions
     */
    public function getAllQuestions(): array
    {
        return $this->questions;
    }
    
    /**
     * Get categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
    
    /**
     * Submit answer
     */
    public function submitAnswer(string $questionId, mixed $answer): void
    {
        $this->userAnswers[$questionId] = $answer;
    }
    
    /**
     * Calculate score
     */
    public function calculateScore(): array
    {
        $totalPoints = 0;
        $earnedPoints = 0;
        $categoryScores = [];
        
        foreach ($this->questions as $category => $questions) {
            $categoryTotal = 0;
            $categoryEarned = 0;
            
            foreach ($questions as $question) {
                $totalPoints += $question['points'];
                $categoryTotal += $question['points'];
                
                if (isset($this->userAnswers[$question['id']])) {
                    $userAnswer = $this->userAnswers[$question['id']];
                    $correctAnswer = $question['correct_answer'];
                    
                    if ($question['type'] === 'multiple_choice') {
                        if ($userAnswer === $correctAnswer) {
                            $earnedPoints += $question['points'];
                            $categoryEarned += $question['points'];
                        }
                    } elseif ($question['type'] === 'coding' || $question['type'] === 'essay') {
                        // For coding/essay questions, we'll simulate grading
                        $score = $this->gradeOpenEndedQuestion($question, $userAnswer);
                        $earnedPoints += $score * $question['points'];
                        $categoryEarned += $score * $question['points'];
                    }
                }
            }
            
            $categoryScores[$category] = [
                'total' => $categoryTotal,
                'earned' => $categoryEarned,
                'percentage' => $categoryTotal > 0 ? ($categoryEarned / $categoryTotal) * 100 : 0,
                'passed' => $categoryTotal > 0 ? ($categoryEarned / $categoryTotal) * 100 >= $this->categories[$category]['required_score'] : false
            ];
        }
        
        $overallScore = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $overallScore >= 70;
        
        // Check if all categories passed
        $allCategoriesPassed = true;
        foreach ($categoryScores as $score) {
            if (!$score['passed']) {
                $allCategoriesPassed = false;
                break;
            }
        }
        
        return [
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'overall_score' => $overallScore,
            'passed' => $passed && $allCategoriesPassed,
            'category_scores' => $categoryScores,
            'recommendations' => $this->generateRecommendations($categoryScores)
        ];
    }
    
    /**
     * Grade open-ended questions (simulated)
     */
    private function gradeOpenEndedQuestion(array $question, string $answer): float
    {
        // Simulate grading based on answer length and keywords
        $keywords = $this->getQuestionKeywords($question['id']);
        $answerLower = strtolower($answer);
        
        $keywordScore = 0;
        foreach ($keywords as $keyword) {
            if (strpos($answerLower, strtolower($keyword)) !== false) {
                $keywordScore++;
            }
        }
        
        $lengthScore = min(strlen($answer) / 200, 1); // Normalize to 0-1
        $keywordScore = $keywordScore / count($keywords);
        
        return ($lengthScore * 0.3) + ($keywordScore * 0.7); // 70% keywords, 30% length
    }
    
    /**
     * Get question keywords for grading
     */
    private function getQuestionKeywords(string $questionId): array
    {
        $keywords = [
            'oop_002' => ['code reuse', 'single inheritance', 'methods', 'classes', 'horizontal reuse'],
            'oop_003' => ['static', 'binding', 'runtime', 'compile time', 'late static binding'],
            'fw_002' => ['filter', 'http requests', 'authentication', 'logging', 'cors'],
            'db_002' => ['index', 'performance', 'read operations', 'write operations', 'data retrieval'],
            'sec_001' => ['prepared statements', 'parameterized', 'input validation', 'malicious', 'queries'],
            'sec_002' => ['output encoding', 'csp', 'input validation', 'malicious scripts', 'xss'],
            'adv_002' => ['concurrency', 'lightweight', 'cooperative', 'asynchronous', 'fibers']
        ];
        
        return $keywords[$questionId] ?? [];
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $categoryScores): array
    {
        $recommendations = [];
        
        foreach ($categoryScores as $category => $score) {
            if (!$score['passed']) {
                $recommendations[] = "Focus on {$this->categories[$category]['name']} - Current score: " . round($score['percentage'], 1) . "% (Required: {$this->categories[$category]['required_score']}%)";
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = "Excellent work! You've passed all categories.";
        }
        
        return $recommendations;
    }
    
    /**
     * Generate assessment report
     */
    public function generateReport(): string
    {
        $score = $this->calculateScore();
        
        $report = "PHP Knowledge Assessment Report\n";
        $report .= str_repeat("=", 35) . "\n\n";
        
        $report .= "Overall Score: " . round($score['overall_score'], 1) . "%\n";
        $report .= "Status: " . ($score['passed'] ? 'PASSED' : 'FAILED') . "\n";
        $report .= "Points Earned: {$score['earned_points']}/{$score['total_points']}\n\n";
        
        $report .= "Category Breakdown:\n";
        $report .= str_repeat("-", 18) . "\n";
        
        foreach ($score['category_scores'] as $category => $categoryScore) {
            $categoryName = $this->categories[$category]['name'];
            $status = $categoryScore['passed'] ? 'PASS' : 'FAIL';
            $report .= "$categoryName: " . round($categoryScore['percentage'], 1) . "% ($status)\n";
        }
        
        $report .= "\nRecommendations:\n";
        $report .= str_repeat("-", 16) . "\n";
        foreach ($score['recommendations'] as $recommendation) {
            $report .= "• $recommendation\n";
        }
        
        return $report;
    }
}

// Practical Coding Assessment
class PracticalCodingAssessment
{
    private array $challenges = [];
    private array $submissions = [];
    private array $results = [];
    
    public function __construct()
    {
        $this->initializeChallenges();
    }
    
    /**
     * Initialize coding challenges
     */
    private function initializeChallenges(): void
    {
        $this->challenges = [
            'array_manipulation' => [
                'title' => 'Array Manipulation Challenge',
                'description' => 'Create a function that processes arrays with various operations',
                'difficulty' => 'easy',
                'time_limit' => 30, // minutes
                'points' => 20,
                'requirements' => [
                    'Filter array elements based on callback',
                    'Map array elements to new values',
                    'Reduce array to single value',
                    'Handle edge cases and errors'
                ],
                'test_cases' => [
                    ['input' => [1, 2, 3, 4, 5], 'expected' => [2, 4, 6, 8, 10]],
                    ['input' => ['a', 'b', 'c'], 'expected' => ['A', 'B', 'C']],
                    ['input' => [], 'expected' => []]
                ]
            ],
            'oop_design' => [
                'title' => 'OOP Design Challenge',
                'description' => 'Design and implement a class hierarchy following OOP principles',
                'difficulty' => 'medium',
                'time_limit' => 45,
                'points' => 30,
                'requirements' => [
                    'Create abstract base class',
                    'Implement concrete classes',
                    'Use encapsulation properly',
                    'Implement polymorphism',
                    'Follow SOLID principles'
                ],
                'test_cases' => [
                    'Test inheritance',
                    'Test polymorphism',
                    'Test encapsulation'
                ]
            ],
            'api_development' => [
                'title' => 'REST API Development',
                'description' => 'Build a simple RESTful API with CRUD operations',
                'difficulty' => 'medium',
                'time_limit' => 60,
                'points' => 40,
                'requirements' => [
                    'Create proper HTTP endpoints',
                    'Handle different HTTP methods',
                    'Implement input validation',
                    'Return proper HTTP status codes',
                    'Handle errors gracefully'
                ],
                'test_cases' => [
                    'GET /users',
                    'POST /users',
                    'PUT /users/{id}',
                    'DELETE /users/{id}'
                ]
            ],
            'database_integration' => [
                'title' => 'Database Integration',
                'description' => 'Implement database operations with proper error handling',
                'difficulty' => 'hard',
                'time_limit' => 60,
                'points' => 50,
                'requirements' => [
                    'Connect to database',
                    'Implement CRUD operations',
                    'Use prepared statements',
                    'Handle database errors',
                    'Implement transactions'
                ],
                'test_cases' => [
                    'Test connection',
                    'Test CRUD operations',
                    'Test error handling',
                    'Test transactions'
                ]
            ],
            'security_implementation' => [
                'title' => 'Security Implementation',
                'description' => 'Implement security measures in a web application',
                'difficulty' => 'hard',
                'time_limit' => 90,
                'points' => 60,
                'requirements' => [
                    'Implement authentication',
                    'Prevent SQL injection',
                    'Prevent XSS attacks',
                    'Implement CSRF protection',
                    'Validate and sanitize input'
                ],
                'test_cases' => [
                    'Test authentication',
                    'Test SQL injection prevention',
                    'Test XSS prevention',
                    'Test CSRF protection'
                ]
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
     * Submit solution
     */
    public function submitSolution(string $challengeId, string $code): void
    {
        $this->submissions[$challengeId] = [
            'code' => $code,
            'submitted_at' => time(),
            'status' => 'pending'
        ];
        
        // Evaluate the solution
        $this->evaluateSolution($challengeId);
    }
    
    /**
     * Evaluate solution (simplified evaluation)
     */
    private function evaluateSolution(string $challengeId): void
    {
        $challenge = $this->challenges[$challengeId];
        $submission = $this->submissions[$challengeId];
        
        $score = $this->analyzeCode($submission['code'], $challenge);
        
        $this->submissions[$challengeId]['status'] = 'evaluated';
        $this->submissions[$challengeId]['score'] = $score;
        $this->submissions[$challengeId]['feedback'] = $this->generateFeedback($score, $challenge);
        
        $this->results[$challengeId] = $score;
    }
    
    /**
     * Analyze code (simplified analysis)
     */
    private function analyzeCode(string $code, array $challenge): array
    {
        $score = [
            'functionality' => 0,
            'code_quality' => 0,
            'security' => 0,
            'performance' => 0,
            'total' => 0
        ];
        
        // Check for required functionality
        $functionalityScore = $this->checkFunctionality($code, $challenge);
        $score['functionality'] = $functionalityScore;
        
        // Check code quality
        $qualityScore = $this->checkCodeQuality($code);
        $score['code_quality'] = $qualityScore;
        
        // Check security (if applicable)
        if ($challengeId === 'security_implementation') {
            $securityScore = $this->checkSecurity($code);
            $score['security'] = $securityScore;
        } else {
            $score['security'] = 80; // Default score for non-security challenges
        }
        
        // Check performance
        $performanceScore = $this->checkPerformance($code);
        $score['performance'] = $performanceScore;
        
        // Calculate total score
        $score['total'] = ($score['functionality'] * 0.4) + 
                          ($score['code_quality'] * 0.3) + 
                          ($score['security'] * 0.2) + 
                          ($score['performance'] * 0.1);
        
        return $score;
    }
    
    /**
     * Check functionality
     */
    private function checkFunctionality(string $code, array $challenge): int
    {
        // Simplified functionality check
        $requiredKeywords = $this->getRequiredKeywords($challenge['title']);
        $foundKeywords = 0;
        
        foreach ($requiredKeywords as $keyword) {
            if (strpos($code, $keyword) !== false) {
                $foundKeywords++;
            }
        }
        
        return ($foundKeywords / count($requiredKeywords)) * 100;
    }
    
    /**
     * Get required keywords for functionality check
     */
    private function getRequiredKeywords(string $title): array
    {
        $keywords = [
            'Array Manipulation Challenge' => ['function', 'array_filter', 'array_map', 'array_reduce'],
            'OOP Design Challenge' => ['class', 'abstract', 'extends', 'interface'],
            'REST API Development' => ['GET', 'POST', 'PUT', 'DELETE', 'header'],
            'Database Integration' => ['PDO', 'prepare', 'execute', 'beginTransaction'],
            'Security Implementation' => ['password_hash', 'htmlspecialchars', 'filter_var', 'session']
        ];
        
        return $keywords[$title] ?? [];
    }
    
    /**
     * Check code quality
     */
    private function checkCodeQuality(string $code): int
    {
        $score = 80; // Base score
        
        // Check for proper naming conventions
        if (preg_match('/\$[a-z][a-zA-Z0-9_]*/', $code)) {
            $score += 5;
        }
        
        // Check for comments
        if (strpos($code, '//') !== false || strpos($code, '/*') !== false) {
            $score += 5;
        }
        
        // Check for error handling
        if (strpos($code, 'try') !== false && strpos($code, 'catch') !== false) {
            $score += 5;
        }
        
        // Check for functions/methods
        if (strpos($code, 'function') !== false) {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    /**
     * Check security
     */
    private function checkSecurity(string $code): int
    {
        $score = 70; // Base score
        
        // Check for input validation
        if (strpos($code, 'filter_var') !== false || strpos($code, 'isset') !== false) {
            $score += 10;
        }
        
        // Check for output encoding
        if (strpos($code, 'htmlspecialchars') !== false) {
            $score += 10;
        }
        
        // Check for prepared statements
        if (strpos($code, 'prepare') !== false) {
            $score += 10;
        }
        
        return min($score, 100);
    }
    
    /**
     * Check performance
     */
    private function checkPerformance(string $code): int
    {
        $score = 80; // Base score
        
        // Check for efficient loops
        if (strpos($code, 'foreach') !== false) {
            $score += 5;
        }
        
        // Check for caching (simplified)
        if (strpos($code, 'cache') !== false) {
            $score += 10;
        }
        
        // Check for database optimization
        if (strpos($code, 'index') !== false || strpos($code, 'LIMIT') !== false) {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    /**
     * Generate feedback
     */
    private function generateFeedback(array $score, array $challenge): array
    {
        $feedback = [];
        
        if ($score['functionality'] < 80) {
            $feedback[] = 'Functionality needs improvement. Make sure all requirements are implemented.';
        }
        
        if ($score['code_quality'] < 80) {
            $feedback[] = 'Code quality can be improved. Add comments, use proper naming, and handle errors.';
        }
        
        if ($score['security'] < 80) {
            $feedback[] = 'Security measures need attention. Implement proper validation and protection.';
        }
        
        if ($score['performance'] < 80) {
            $feedback[] = 'Performance can be optimized. Consider efficient algorithms and caching.';
        }
        
        if (empty($feedback)) {
            $feedback[] = 'Excellent work! Your solution meets all requirements.';
        }
        
        return $feedback;
    }
    
    /**
     * Get results
     */
    public function getResults(): array
    {
        return $this->results;
    }
    
    /**
     * Get submissions
     */
    public function getSubmissions(): array
    {
        return $this->submissions;
    }
    
    /**
     * Calculate overall score
     */
    public function calculateOverallScore(): array
    {
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($this->challenges as $id => $challenge) {
            $totalPoints += $challenge['points'];
            
            if (isset($this->results[$id])) {
                $earnedPoints += ($this->results[$id]['total'] / 100) * $challenge['points'];
            }
        }
        
        $overallScore = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        
        return [
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'overall_score' => $overallScore,
            'passed' => $overallScore >= 70
        ];
    }
}

// Comprehensive Assessment Runner
class ComprehensiveAssessment
{
    private PHPKnowledgeAssessment $knowledgeAssessment;
    private PracticalCodingAssessment $codingAssessment;
    private array $finalResults = [];
    
    public function __construct()
    {
        $this->knowledgeAssessment = new PHPKnowledgeAssessment();
        $this->codingAssessment = new PracticalCodingAssessment();
    }
    
    /**
     * Run complete assessment
     */
    public function runCompleteAssessment(): array
    {
        echo "Starting Comprehensive PHP Assessment...\n\n";
        
        // Run knowledge assessment
        echo "1. Knowledge Assessment\n";
        echo str_repeat("-", 25) . "\n";
        
        $this->runKnowledgeAssessment();
        
        // Run coding assessment
        echo "\n2. Coding Assessment\n";
        echo str_repeat("-", 20) . "\n";
        
        $this->runCodingAssessment();
        
        // Calculate final results
        $this->calculateFinalResults();
        
        return $this->finalResults;
    }
    
    /**
     * Run knowledge assessment
     */
    private function runKnowledgeAssessment(): void
    {
        $questions = $this->knowledgeAssessment->getAllQuestions();
        $answered = 0;
        
        foreach ($questions as $category => $categoryQuestions) {
            echo "Category: {$this->knowledgeAssessment->getCategories()[$category]['name']}\n";
            
            foreach ($categoryQuestions as $question) {
                // Simulate answering questions
                $answer = $this->simulateAnswer($question);
                $this->knowledgeAssessment->submitAnswer($question['id'], $answer);
                $answered++;
                
                echo "  Q{$answered}: {$question['question']}\n";
                echo "    Answered: " . ($question['type'] === 'multiple_choice' ? $answer : '[Essay/Coding]') . "\n";
            }
        }
        
        $knowledgeScore = $this->knowledgeAssessment->calculateScore();
        echo "\nKnowledge Score: " . round($knowledgeScore['overall_score'], 1) . "%\n";
        echo "Status: " . ($knowledgeScore['passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    /**
     * Simulate answer for demonstration
     */
    private function simulateAnswer(array $question): string
    {
        if ($question['type'] === 'multiple_choice') {
            // Return correct answer for demonstration
            return $question['correct_answer'];
        } else {
            // Return a sample answer for essay/coding questions
            return "Sample answer demonstrating understanding of the concept.";
        }
    }
    
    /**
     * Run coding assessment
     */
    private function runCodingAssessment(): void
    {
        $challenges = $this->codingAssessment->getChallenges();
        $completed = 0;
        
        foreach ($challenges as $id => $challenge) {
            echo "Challenge " . ($completed + 1) . ": {$challenge['title']}\n";
            echo "Difficulty: {$challenge['difficulty']}\n";
            echo "Time Limit: {$challenge['time_limit']} minutes\n";
            echo "Points: {$challenge['points']}\n\n";
            
            // Simulate submitting a solution
            $sampleCode = $this->generateSampleCode($id);
            $this->codingAssessment->submitSolution($id, $sampleCode);
            
            $result = $this->codingAssessment->getResults()[$id];
            echo "Score: " . round($result['total'], 1) . "%\n";
            echo "Feedback: " . implode(', ', $this->codingAssessment->getSubmissions()[$id]['feedback']) . "\n\n";
            
            $completed++;
        }
        
        $codingScore = $this->codingAssessment->calculateOverallScore();
        echo "Coding Score: " . round($codingScore['overall_score'], 1) . "%\n";
        echo "Status: " . ($codingScore['passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    /**
     * Generate sample code for demonstration
     */
    private function generateSampleCode(string $challengeId): string
    {
        $samples = [
            'array_manipulation' => '<?php
function processArray($array, $callback) {
    return array_map($callback, $array);
}',
            'oop_design' => '<?php
abstract class Animal {
    abstract public function makeSound();
}

class Dog extends Animal {
    public function makeSound() {
        return "Woof!";
    }
}',
            'api_development' => '<?php
// Sample API endpoint
function getUsers() {
    header("Content-Type: application/json");
    echo json_encode(["users" => []]);
}',
            'database_integration' => '<?php
// Sample database operation
$pdo = new PDO("mysql:host=localhost;dbname=test", "user", "pass");
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();',
            'security_implementation' => '<?php
// Sample security implementation
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);'
        ];
        
        return $samples[$challengeId] ?? '<?php // Sample code';
    }
    
    /**
     * Calculate final results
     */
    private function calculateFinalResults(): void
    {
        $knowledgeScore = $this->knowledgeAssessment->calculateScore();
        $codingScore = $this->codingAssessment->calculateOverallScore();
        
        $this->finalResults = [
            'knowledge_assessment' => [
                'score' => $knowledgeScore['overall_score'],
                'passed' => $knowledgeScore['passed'],
                'category_scores' => $knowledgeScore['category_scores']
            ],
            'coding_assessment' => [
                'score' => $codingScore['overall_score'],
                'passed' => $codingScore['passed'],
                'challenge_results' => $this->codingAssessment->getResults()
            ],
            'overall' => [
                'total_score' => ($knowledgeScore['overall_score'] + $codingScore['overall_score']) / 2,
                'passed' => $knowledgeScore['passed'] && $codingScore['passed'],
                'grade' => $this->calculateGrade(($knowledgeScore['overall_score'] + $codingScore['overall_score']) / 2)
            ]
        ];
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
     * Generate final report
     */
    public function generateFinalReport(): string
    {
        if (empty($this->finalResults)) {
            $this->runCompleteAssessment();
        }
        
        $results = $this->finalResults;
        
        $report = "PHP Comprehensive Assessment Report\n";
        $report .= str_repeat("=", 40) . "\n\n";
        
        $report .= "Overall Results:\n";
        $report .= str_repeat("-", 16) . "\n";
        $report .= "Total Score: " . round($results['overall']['total_score'], 1) . "%\n";
        $report .= "Grade: {$results['overall']['grade']}\n";
        $report .= "Status: " . ($results['overall']['passed'] ? 'PASSED' : 'FAILED') . "\n\n";
        
        $report .= "Knowledge Assessment:\n";
        $report .= str_repeat("-", 20) . "\n";
        $report .= "Score: " . round($results['knowledge_assessment']['score'], 1) . "%\n";
        $report .= "Status: " . ($results['knowledge_assessment']['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        foreach ($results['knowledge_assessment']['category_scores'] as $category => $score) {
            $categoryName = $this->knowledgeAssessment->getCategories()[$category]['name'];
            $report .= "  $categoryName: " . round($score['percentage'], 1) . "%\n";
        }
        
        $report .= "\nCoding Assessment:\n";
        $report .= str_repeat("-", 18) . "\n";
        $report .= "Score: " . round($results['coding_assessment']['score'], 1) . "%\n";
        $report .= "Status: " . ($results['coding_assessment']['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        foreach ($results['coding_assessment']['challenge_results'] as $id => $result) {
            $challenge = $this->codingAssessment->getChallenges()[$id];
            $report .= "  {$challenge['title']}: " . round($result['total'], 1) . "%\n";
        }
        
        $report .= "\nCertification Status:\n";
        $report .= str_repeat("-", 20) . "\n";
        
        if ($results['overall']['passed']) {
            $report .= "🎉 Congratulations! You have successfully completed the PHP Comprehensive Assessment.\n";
            $report .= "You have earned the PHP Learning Certificate with a grade of {$results['overall']['grade']}.\n";
            $report .= "You are now ready for professional PHP development roles.\n";
        } else {
            $report .= "⏳ You have not yet met the requirements for certification.\n";
            $report .= "Continue studying and practicing to improve your skills.\n";
            $report .= "Focus on areas where you scored below 70%.\n";
        }
        
        return $report;
    }
}

// Assessment Examples
class AssessmentExamples
{
    private PHPKnowledgeAssessment $knowledgeAssessment;
    private PracticalCodingAssessment $codingAssessment;
    private ComprehensiveAssessment $comprehensiveAssessment;
    
    public function __construct()
    {
        $this->knowledgeAssessment = new PHPKnowledgeAssessment();
        $this->codingAssessment = new PracticalCodingAssessment();
        $this->comprehensiveAssessment = new ComprehensiveAssessment();
    }
    
    public function demonstrateKnowledgeAssessment(): void
    {
        echo "Knowledge Assessment Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Show categories
        $categories = $this->knowledgeAssessment->getCategories();
        echo "Assessment Categories:\n";
        foreach ($categories as $id => $category) {
            echo "  $id: {$category['name']} ({$category['weight']}% weight)\n";
            echo "    Required: {$category['required_score']}%\n";
            echo "    {$category['description']}\n";
        }
        
        echo "\nSample Questions:\n";
        
        // Show sample questions from each category
        $questions = $this->knowledgeAssessment->getAllQuestions();
        foreach ($questions as $category => $categoryQuestions) {
            echo "\n{$categories[$category]['name']}:\n";
            
            $sampleQuestion = $categoryQuestions[0];
            echo "  Q: {$sampleQuestion['question']}\n";
            echo "  Type: {$sampleQuestion['type']}\n";
            echo "  Difficulty: {$sampleQuestion['difficulty']}\n";
            echo "  Points: {$sampleQuestion['points']}\n";
            
            if ($sampleQuestion['type'] === 'multiple_choice') {
                echo "  Options:\n";
                foreach ($sampleQuestion['options'] as $key => $option) {
                    $marker = $key === $sampleQuestion['correct_answer'] ? '✓' : ' ';
                    echo "    $marker) $option\n";
                }
            }
            
            echo "  Explanation: {$sampleQuestion['explanation']}\n";
        }
        
        // Simulate taking the assessment
        echo "\nSimulating Assessment...\n";
        
        foreach ($questions as $category => $categoryQuestions) {
            foreach ($categoryQuestions as $question) {
                $answer = $this->simulateAnswer($question);
                $this->knowledgeAssessment->submitAnswer($question['id'], $answer);
            }
        }
        
        $score = $this->knowledgeAssessment->calculateScore();
        
        echo "\nResults:\n";
        echo "Overall Score: " . round($score['overall_score'], 1) . "%\n";
        echo "Status: " . ($score['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        echo "\nCategory Breakdown:\n";
        foreach ($score['category_scores'] as $category => $categoryScore) {
            echo "  {$categories[$category]['name']}: " . round($categoryScore['percentage'], 1) . "%";
            echo " (" . ($categoryScore['passed'] ? 'PASS' : 'FAIL') . ")\n";
        }
        
        echo "\nRecommendations:\n";
        foreach ($score['recommendations'] as $recommendation) {
            echo "  • $recommendation\n";
        }
    }
    
    public function demonstrateCodingAssessment(): void
    {
        echo "\nCoding Assessment Examples\n";
        echo str_repeat("-", 28) . "\n";
        
        $challenges = $this->codingAssessment->getChallenges();
        
        echo "Coding Challenges:\n";
        foreach ($challenges as $id => $challenge) {
            echo "\n{$challenge['title']}:\n";
            echo "  Difficulty: {$challenge['difficulty']}\n";
            echo "  Time Limit: {$challenge['time_limit']} minutes\n";
            echo "  Points: {$challenge['points']}\n";
            echo "  Description: {$challenge['description']}\n";
            echo "  Requirements:\n";
            foreach ($challenge['requirements'] as $requirement) {
                echo "    • $requirement\n";
            }
        }
        
        echo "\nSimulating Submissions...\n";
        
        foreach ($challenges as $id => $challenge) {
            $sampleCode = $this->generateSampleCode($id);
            $this->codingAssessment->submitSolution($id, $sampleCode);
            
            $result = $this->codingAssessment->getResults()[$id];
            $submission = $this->codingAssessment->getSubmissions()[$id];
            
            echo "\n{$challenge['title']} Results:\n";
            echo "  Overall Score: " . round($result['total'], 1) . "%\n";
            echo "  Functionality: " . round($result['functionality'], 1) . "%\n";
            echo "  Code Quality: " . round($result['code_quality'], 1) . "%\n";
            echo "  Security: " . round($result['security'], 1) . "%\n";
            echo "  Performance: " . round($result['performance'], 1) . "%\n";
            echo "  Feedback: " . implode(', ', $submission['feedback']) . "\n";
        }
        
        $overallScore = $this->codingAssessment->calculateOverallScore();
        
        echo "\nOverall Coding Results:\n";
        echo "Score: " . round($overallScore['overall_score'], 1) . "%\n";
        echo "Points: {$overallScore['earned_points']}/{$overallScore['total_points']}\n";
        echo "Status: " . ($overallScore['passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    public function demonstrateComprehensiveAssessment(): void
    {
        echo "\nComprehensive Assessment Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        $results = $this->comprehensiveAssessment->runCompleteAssessment();
        
        echo "\nFinal Assessment Report:\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "Overall Score: " . round($results['overall']['total_score'], 1) . "%\n";
        echo "Grade: {$results['overall']['grade']}\n";
        echo "Status: " . ($results['overall']['passed'] ? 'PASSED' : 'FAILED') . "\n";
        
        echo "\nDetailed Breakdown:\n";
        echo "Knowledge Assessment: " . round($results['knowledge_assessment']['score'], 1) . "%\n";
        echo "Coding Assessment: " . round($results['coding_assessment']['score'], 1) . "%\n";
        
        echo "\nCertification Status:\n";
        if ($results['overall']['passed']) {
            echo "🎉 CERTIFIED - PHP Learning Certificate Earned!\n";
            echo "Grade: {$results['overall']['grade']}\n";
            echo "Status: Ready for professional development\n";
        } else {
            echo "⏳ NOT CERTIFIED - Continue learning and practicing\n";
            echo "Focus on areas needing improvement\n";
        }
        
        // Generate full report
        $report = $this->comprehensiveAssessment->generateFinalReport();
        echo "\n" . substr($report, 0, 500) . "...\n";
    }
    
    public function demonstrateAssessmentFeatures(): void
    {
        echo "\nAssessment Features\n";
        echo str_repeat("-", 20) . "\n";
        
        echo "Knowledge Assessment Features:\n";
        echo "  • Multiple choice questions\n";
        echo "  • Essay questions\n";
        echo "  • Coding challenges\n";
        echo "  • Category-based scoring\n";
        echo "  • Detailed feedback\n";
        echo "  • Progress tracking\n\n";
        
        echo "Coding Assessment Features:\n";
        echo "  • Real-world challenges\n";
        echo "  • Code quality analysis\n";
        echo "  • Security evaluation\n";
        echo "  • Performance assessment\n";
        echo "  • Automated scoring\n";
        echo "  • Detailed feedback\n\n";
        
        echo "Comprehensive Assessment Features:\n";
        echo "  • Combined evaluation\n";
        echo "  • Grade calculation\n";
        echo "  • Certification system\n";
        echo "  • Detailed reporting\n";
        echo "  • Recommendations\n";
        echo "  • Progress tracking\n\n";
        
        echo "Assessment Benefits:\n";
        echo "  • Evaluate comprehensive knowledge\n";
        echo "  • Identify skill gaps\n";
        echo "  • Provide learning guidance\n";
        echo "  • Prepare for interviews\n";
        echo "  • Build confidence\n";
        echo "  • Earn certification";
    }
    
    public function runAllExamples(): void
    {
        echo "PHP Knowledge Assessment Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateKnowledgeAssessment();
        $this->demonstrateCodingAssessment();
        $this->demonstrateComprehensiveAssessment();
        $this->demonstrateAssessmentFeatures();
    }
    
    private function simulateAnswer(array $question): string
    {
        if ($question['type'] === 'multiple_choice') {
            return $question['correct_answer'];
        } else {
            return "Sample answer demonstrating understanding of the concept.";
        }
    }
    
    private function generateSampleCode(string $challengeId): string
    {
        $samples = [
            'array_manipulation' => '<?php
function processArray($array, $callback) {
    return array_filter(array_map($callback, $array));
}',
            'oop_design' => '<?php
abstract class Shape {
    abstract public function getArea();
    abstract public function getPerimeter();
}

class Circle extends Shape {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    public function getArea() {
        return pi() * $this->radius * $this->radius;
    }
    
    public function getPerimeter() {
        return 2 * pi() * $this->radius;
    }
}',
            'api_development' => '<?php
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getUsers();
        break;
    case "POST":
        createUser();
        break;
    case "PUT":
        updateUser();
        break;
    case "DELETE":
        deleteUser();
        break;
}

function getUsers() {
    $users = [
        ["id" => 1, "name" => "John"],
        ["id" => 2, "name" => "Jane"]
    ];
    echo json_encode($users);
}',
            'database_integration' => '<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([1]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($user);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}',
            'security_implementation' => '<?php
session_start();

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, "UTF-8");
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Usage
$email = sanitizeInput($_POST["email"] ?? "");
if (validateEmail($email)) {
    $password = $_POST["password"] ?? "";
    $hashedPassword = hashPassword($password);
    // Store in database
}'
        ];
        
        return $samples[$challengeId] ?? '<?php // Sample code';
    }
}

// Main execution
function runKnowledgeAssessmentDemo(): void
{
    $examples = new AssessmentExamples();
    $examples->runAllExamples();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runKnowledgeAssessmentDemo();
}
?>
