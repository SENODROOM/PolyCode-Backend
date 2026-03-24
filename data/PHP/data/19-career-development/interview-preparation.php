<?php
/**
 * Interview Preparation Guide
 * 
 * This file provides comprehensive interview preparation,
 * common questions, coding challenges, and practice strategies.
 */

// Interview Question Bank
class InterviewQuestionBank
{
    private array $technicalQuestions = [];
    private array $behavioralQuestions = [];
    private array $codingChallenges = [];
    private array $systemDesignQuestions = [];
    
    public function __construct()
    {
        $this->initializeQuestions();
    }
    
    /**
     * Initialize interview questions
     */
    private function initializeQuestions(): void
    {
        $this->technicalQuestions = [
            'php_fundamentals' => [
                [
                    'question' => 'What are the main differences between PHP 7 and PHP 8?',
                    'difficulty' => 'Medium',
                    'category' => 'Language Features',
                    'keywords' => ['php8', 'features', 'differences'],
                    'sample_answer' => 'PHP 8 introduced several major features: union types, named arguments, match expressions, nullsafe operator, constructor property promotion, enums, fibers, readonly properties, attributes, and JIT compilation. Union types allow variables to accept multiple types, named arguments improve code readability, match expressions provide more control than switch statements, and the nullsafe operator simplifies null checking chains.'
                ],
                [
                    'question' => 'Explain the difference between == and === in PHP',
                    'difficulty' => 'Easy',
                    'category' => 'Operators',
                    'keywords' => ['comparison', 'operators', 'type'],
                    'sample_answer' => '== performs loose comparison with type juggling, while === performs strict comparison without type conversion. For example, "5" == 5 returns true, but "5" === 5 returns false. Always use === when you need to check both value and type to avoid unexpected behavior.'
                ],
                [
                    'question' => 'What is autoloading in PHP and how does it work?',
                    'difficulty' => 'Medium',
                    'category' => 'OOP',
                    'keywords' => ['autoloading', 'spl_autoload_register', 'psr-4'],
                    'sample_answer' => 'Autoloading automatically loads class files when they\'re needed, eliminating manual require/include statements. It works by registering autoload functions using spl_autoload_register(). When a class is used, PHP calls registered autoload functions with the class name. PSR-4 is the standard autoloading standard that maps namespaces to directory structures.'
                ]
            ],
            'oop_concepts' => [
                [
                    'question' => 'Explain the difference between abstract classes and interfaces',
                    'difficulty' => 'Medium',
                    'category' => 'OOP',
                    'keywords' => ['abstract', 'interface', 'inheritance'],
                    'sample_answer' => 'Interfaces define a contract of methods that must be implemented, while abstract classes can provide both abstract and concrete methods. A class can implement multiple interfaces but extend only one abstract class. Use interfaces for defining capabilities and abstract classes for sharing common implementation.'
                ],
                [
                    'question' => 'What are PHP traits and when would you use them?',
                    'difficulty' => 'Medium',
                    'category' => 'OOP',
                    'keywords' => ['traits', 'code_reuse', 'horizontal_reuse'],
                    'sample_answer' => 'Traits are a mechanism for code reuse in single inheritance languages like PHP. They allow you to reuse sets of methods in multiple independent classes. Use traits when you need to share functionality across unrelated classes without using inheritance.'
                ],
                [
                    'question' => 'Explain late static binding and provide an example',
                    'difficulty' => 'Hard',
                    'category' => 'OOP',
                    'keywords' => ['static', 'binding', 'self', 'static'],
                    'sample_answer' => 'Late static binding allows reference to the called class in context of static inheritance. Using "static::" instead of "self::" resolves at runtime rather than compile time. Example: static::method() calls the method from the class that was actually called, not where it was defined.'
                ]
            ],
            'database' => [
                [
                    'question' => 'Explain the difference between INNER JOIN and LEFT JOIN',
                    'difficulty' => 'Easy',
                    'category' => 'Database',
                    'keywords' => ['sql', 'join', 'database'],
                    'sample_answer' => 'INNER JOIN returns only matching records from both tables, while LEFT JOIN returns all records from the left table and matching records from the right table. Use INNER JOIN when you need only matching data, and LEFT JOIN when you need all records from one table regardless of matches.'
                ],
                [
                    'question' => 'What are database indexes and how do they improve performance?',
                    'difficulty' => 'Medium',
                    'category' => 'Database',
                    'keywords' => ['index', 'performance', 'optimization'],
                    'sample_answer' => 'Indexes are data structures that improve query speed by allowing fast data retrieval without scanning entire tables. They work like book indexes - instead of reading the whole book, you can quickly find relevant pages. Common types include B-tree indexes for equality and range queries, and hash indexes for exact matches.'
                ],
                [
                    'question' => 'Explain database transactions and ACID properties',
                    'difficulty' => 'Hard',
                    'category' => 'Database',
                    'keywords' => ['transaction', 'acid', 'consistency'],
                    'sample_answer' => 'Transactions are sequences of operations executed as a single unit. ACID properties ensure reliability: Atomicity (all or nothing), Consistency (data remains valid), Isolation (concurrent transactions don\'t interfere), and Durability (changes persist). Use transactions for operations that must maintain data integrity.'
                ]
            ],
            'frameworks' => [
                [
                    'question' => 'What is the difference between Laravel and Symfony?',
                    'difficulty' => 'Medium',
                    'category' => 'Frameworks',
                    'keywords' => ['laravel', 'symfony', 'comparison'],
                    'sample_answer' => 'Laravel focuses on developer experience with elegant syntax and conventions, while Symfony provides more flexibility and modularity. Laravel includes Eloquent ORM and Blade templating, while Symfony uses Doctrine ORM and Twig. Laravel is great for rapid development, while Symfony excels in enterprise applications requiring customization.'
                ],
                [
                    'question' => 'Explain dependency injection in Laravel',
                    'difficulty' => 'Medium',
                    'category' => 'Frameworks',
                    'keywords' => ['dependency_injection', 'ioc', 'laravel'],
                    'sample_answer' => 'Dependency injection is a design pattern where dependencies are injected into classes rather than created internally. Laravel\'s IoC container manages dependency injection, allowing automatic resolution of class dependencies. This improves testability, maintainability, and follows SOLID principles.'
                ],
                [
                    'question' => 'What are Laravel middleware and how do they work?',
                    'difficulty' => 'Medium',
                    'category' => 'Frameworks',
                    'keywords' => ['middleware', 'laravel', 'http'],
                    'sample_answer' => 'Middleware are classes that filter HTTP requests entering your application. They form layers that requests pass through before reaching the controller. Use middleware for authentication, logging, CORS, rate limiting, and request/response modification. They provide a clean way to separate cross-cutting concerns.'
                ]
            ],
            'security' => [
                [
                    'question' => 'What is SQL injection and how do you prevent it?',
                    'difficulty' => 'Medium',
                    'category' => 'Security',
                    'keywords' => ['sql_injection', 'security', 'prepared_statements'],
                    'sample_answer' => 'SQL injection is an attack where malicious SQL code is inserted into queries. Prevent it using prepared statements with parameterized queries, ORMs like Eloquent, input validation, and least privilege database access. Never concatenate user input directly into SQL queries.'
                ],
                [
                    'question' => 'Explain XSS and how to prevent it in PHP',
                    'difficulty' => 'Medium',
                    'category' => 'Security',
                    'keywords' => ['xss', 'cross_site_scripting', 'security'],
                    'sample_answer' => 'Cross-Site Scripting (XSS) injects malicious scripts into web pages viewed by users. Prevent it by output encoding with htmlspecialchars(), using Content Security Policy headers, validating input, and using frameworks with built-in XSS protection like Laravel\'s Blade templating.'
                ],
                [
                    'question' => 'What is CSRF and how do you protect against it?',
                    'difficulty' => 'Medium',
                    'category' => 'Security',
                    'keywords' => ['csrf', 'security', 'tokens'],
                    'sample_answer' => 'Cross-Site Request Forgery tricks users into performing unwanted actions. Protect using CSRF tokens that validate requests originate from your application, SameSite cookie attributes, and checking referrer headers. Most frameworks like Laravel provide built-in CSRF protection.'
                ]
            ],
            'performance' => [
                [
                    'question' => 'How would you optimize a slow PHP application?',
                    'difficulty' => 'Hard',
                    'category' => 'Performance',
                    'keywords' => ['optimization', 'performance', 'profiling'],
                    'sample_answer' => 'Start with profiling to identify bottlenecks using tools like Xdebug or Blackfire. Common optimizations include: enable OPcache, use efficient algorithms, optimize database queries, implement caching, use lazy loading, minimize I/O operations, and consider asynchronous processing for long tasks.'
                ],
                [
                    'question' => 'What is OPcache and how does it improve performance?',
                    'difficulty' => 'Medium',
                    'category' => 'Performance',
                    'keywords' => ['opcache', 'performance', 'bytecode'],
                    'sample_answer' => 'OPcache is a PHP extension that improves performance by caching precompiled script bytecode in memory. This eliminates the need for PHP to parse and compile scripts on each request. It can significantly improve performance, especially for applications with frequent requests.'
                ],
                [
                    'question' => 'Explain different caching strategies in PHP',
                    'difficulty' => 'Medium',
                    'category' => 'Performance',
                    'keywords' => ['caching', 'performance', 'strategies'],
                    'sample_answer' => 'Common caching strategies include: application-level caching with APCu/Memcached, database query caching, HTTP caching with headers, CDN caching for static assets, and opcode caching with OPcache. Choose based on data volatility, access patterns, and infrastructure constraints.'
                ]
            ]
        ];
        
        $this->behavioralQuestions = [
            'teamwork' => [
                [
                    'question' => 'Tell me about a time you had a conflict with a team member',
                    'focus' => 'Conflict resolution, communication, collaboration',
                    'what_interviewer_wants' => 'How you handle disagreements professionally',
                    'sample_answer' => 'In a previous project, a colleague and I disagreed on the technical approach. I scheduled a meeting to understand their perspective, presented my reasoning with data, and we found a compromise that combined both approaches. We documented the decision and both were satisfied with the outcome.'
                ],
                [
                    'question' => 'Describe a situation where you had to work with a difficult team member',
                    'focus' => 'Interpersonal skills, patience, professionalism',
                    'what_interviewer_wants' => 'Your ability to handle challenging personalities',
                    'sample_answer' => 'I worked with a team member who was very critical of others\' work. I focused on finding common ground, acknowledged their expertise in specific areas, and established clear communication guidelines. Over time, we developed mutual respect and improved our collaboration.'
                ]
            ],
            'problem_solving' => [
                [
                    'question' => 'Tell me about a complex problem you solved',
                    'focus' => 'Analytical thinking, problem-solving approach',
                    'what_interviewer_wants' => 'Your problem-solving methodology',
                    'sample_answer' => 'We had a performance issue in our application. I systematically analyzed the codebase, used profiling tools to identify bottlenecks, discovered inefficient database queries, optimized them, and implemented caching. Performance improved by 60%. I documented the solution and shared it with the team.'
                ],
                [
                    'question' => 'Describe a time you made a mistake and how you handled it',
                    'focus' => 'Accountability, learning from mistakes',
                    'what_interviewer_wants' => 'Your honesty and growth mindset',
                    'sample_answer' => 'I once deployed code without proper testing that caused a production issue. I immediately took responsibility, rolled back the changes, fixed the issue, implemented better testing procedures, and shared lessons learned with the team. This led to improved our deployment process.'
                ]
            ],
            'leadership' => [
                [
                    'question' => 'Tell me about a time you led a project',
                    'focus' => 'Leadership, project management, delegation',
                    'what_interviewer_wants' => 'Your leadership potential',
                    'sample_answer' => 'I led a project to redesign our API. I defined requirements, assigned tasks based on team strengths, established clear milestones, facilitated daily standups, and ensured quality through code reviews. We delivered on time and the new API improved performance by 40%.'
                ],
                [
                    'question' => 'How do you mentor junior developers?',
                    'focus' => 'Mentoring, knowledge sharing, leadership',
                    'what_interviewer_wants' => 'Your ability to develop others',
                    'sample_answer' => 'I pair program with junior developers, conduct regular code reviews, provide constructive feedback, share learning resources, and create growth plans. I focus on building their confidence while ensuring code quality. Several junior developers I mentored have become senior developers.'
                ]
            ]
        ];
        
        $this->codingChallenges = [
            'arrays_strings' => [
                [
                    'title' => 'Find duplicate in array',
                    'difficulty' => 'Easy',
                    'description' => 'Find the first duplicate element in an array',
                    'example_input' => '[1, 2, 3, 4, 2, 5]',
                    'example_output' => '2',
                    'solution' => 'function findFirstDuplicate($arr) { $seen = []; foreach ($arr as $num) { if (in_array($num, $seen)) return $num; $seen[] = $num; } return null; }'
                ],
                [
                    'title' => 'Check if string is palindrome',
                    'difficulty' => 'Easy',
                    'description' => 'Check if a string reads the same forwards and backwards',
                    'example_input' => '"racecar"',
                    'example_output' => 'true',
                    'solution' => 'function isPalindrome($str) { $clean = preg_replace("/[^a-zA-Z0-9]/", "", strtolower($str)); return $clean === strrev($clean); }'
                ],
                [
                    'title' => 'Find missing number',
                    'difficulty' => 'Medium',
                    'description' => 'Find the missing number in an array of 1-n',
                    'example_input' => '[1, 2, 4, 5, 6]',
                    'example_output' => '3',
                    'solution' => 'function findMissing($arr) { $n = count($arr) + 1; $sum = array_sum($arr); $expected = $n * ($n + 1) / 2; return $expected - $sum; }'
                ]
            ],
            'algorithms' => [
                [
                    'title' => 'Binary search',
                    'difficulty' => 'Medium',
                    'description' => 'Implement binary search algorithm',
                    'example_input' => '[1, 3, 5, 7, 9], 5',
                    'example_output' => '2',
                    'solution' => 'function binarySearch($arr, $target) { $left = 0; $right = count($arr) - 1; while ($left <= $right) { $mid = floor(($left + $right) / 2); if ($arr[$mid] === $target) return $mid; if ($arr[$mid] < $target) $left = $mid + 1; else $right = $mid - 1; } return -1; }'
                ],
                [
                    'title' => 'Merge two sorted arrays',
                    'difficulty' => 'Medium',
                    'description' => 'Merge two sorted arrays into one sorted array',
                    'example_input' => '[1, 3, 5], [2, 4, 6]',
                    'example_output' => '[1, 2, 3, 4, 5, 6]',
                    'solution' => 'function mergeSorted($arr1, $arr2) { $result = []; $i = $j = 0; while ($i < count($arr1) && $j < count($arr2)) { if ($arr1[$i] <= $arr2[$j]) { $result[] = $arr1[$i++]; } else { $result[] = $arr2[$j++]; } } return array_merge($result, array_slice($arr1, $i), array_slice($arr2, $j)); }'
                ],
                [
                    'title' => 'Validate parentheses',
                    'difficulty' => 'Hard',
                    'description' => 'Check if parentheses are balanced',
                    'example_input' => '"({[]})"',
                    'example_output' => 'true',
                    'solution' => 'function isValidParentheses($s) { $stack = []; $pairs = [")" => "(", "}" => "{", "]" => "["]; for ($i = 0; $i < strlen($s); $i++) { $char = $s[$i]; if (in_array($char, $pairs)) { array_push($stack, $char); } else { if (empty($stack) || array_pop($stack) !== $pairs[$char]) return false; } } return empty($stack); }'
                ]
            ],
            'data_structures' => [
                [
                    'title' => 'Implement a stack',
                    'difficulty' => 'Medium',
                    'description' => 'Create a stack data structure with push, pop, and peek',
                    'solution' => 'class Stack { private $items = []; public function push($item) { array_push($this->items, $item); } public function pop() { return array_pop($this->items); } public function peek() { return end($this->items); } public function isEmpty() { return empty($this->items); } }'
                ],
                [
                    'title' => 'Implement a queue',
                    'difficulty' => 'Medium',
                    'description' => 'Create a queue data structure with enqueue, dequeue, and front',
                    'solution' => 'class Queue { private $items = []; public function enqueue($item) { array_push($this->items, $item); } public function dequeue() { return array_shift($this->items); } public function front() { return $this->items[0] ?? null; } public function isEmpty() { return empty($this->items); } }'
                ],
                [
                    'title' => 'Binary tree traversal',
                    'difficulty' => 'Hard',
                    'description' => 'Implement in-order traversal of a binary tree',
                    'solution' => 'function inOrderTraversal($node) { $result = []; if ($node) { $result = array_merge($result, inOrderTraversal($node->left)); $result[] = $node->value; $result = array_merge($result, inOrderTraversal($node->right)); } return $result; }'
                ]
            ]
        ];
        
        $this->systemDesignQuestions = [
            [
                'question' => 'Design a URL shortening service',
                'difficulty' => 'Medium',
                'components' => ['API server', 'Database', 'Cache', 'Analytics'],
                'considerations' => ['Scalability', 'Collision handling', 'Analytics', 'Rate limiting'],
                'solution_outline' => 'Use a hash function to generate short URLs, store mappings in database, implement caching for popular URLs, use CDN for redirection, and track analytics.'
            ],
            [
                'question' => 'Design a chat application',
                'difficulty' => 'Hard',
                'components' => ['WebSocket server', 'Message queue', 'Database', 'Push notifications'],
                'considerations' => ['Real-time communication', 'Message persistence', 'Scalability', 'Security'],
                'solution_outline' => 'Use WebSockets for real-time communication, implement message queuing for reliability, store chat history in database, use Redis for session management, and implement end-to-end encryption.'
            ],
            [
                'question' => 'Design an e-commerce platform',
                'difficulty' => 'Hard',
                'components' => ['Product catalog', 'Shopping cart', 'Payment gateway', 'Order management'],
                'considerations' => ['High availability', 'Transaction consistency', 'Inventory management', 'Security'],
                'solution_outline' => 'Use microservices architecture, implement distributed transactions, use message queues for order processing, implement caching for product data, and ensure PCI compliance for payments.'
            ]
        ];
    }
    
    /**
     * Get questions by category
     */
    public function getQuestionsByCategory(string $category, string $difficulty = 'all'): array
    {
        $questions = $this->technicalQuestions[$category] ?? [];
        
        if ($difficulty !== 'all') {
            $questions = array_filter($questions, fn($q) => $q['difficulty'] === $difficulty);
        }
        
        return array_values($questions);
    }
    
    /**
     * Get random questions
     */
    public function getRandomQuestions(int $count = 5, array $categories = []): array
    {
        $allQuestions = [];
        
        if (empty($categories)) {
            $categories = array_keys($this->technicalQuestions);
        }
        
        foreach ($categories as $category) {
            $allQuestions = array_merge($allQuestions, $this->technicalQuestions[$category] ?? []);
        }
        
        shuffle($allQuestions);
        return array_slice($allQuestions, 0, $count);
    }
    
    /**
     * Get behavioral questions
     */
    public function getBehavioralQuestions(string $focus = 'all'): array
    {
        if ($focus === 'all') {
            return array_merge(...array_values($this->behavioralQuestions));
        }
        
        return $this->behavioralQuestions[$focus] ?? [];
    }
    
    /**
     * Get coding challenges
     */
    public function getCodingChallenges(string $difficulty = 'all', string $category = 'all'): array
    {
        $challenges = [];
        
        if ($category === 'all') {
            $challenges = array_merge(...array_values($this->codingChallenges));
        } else {
            $challenges = $this->codingChallenges[$category] ?? [];
        }
        
        if ($difficulty !== 'all') {
            $challenges = array_filter($challenges, fn($c) => $c['difficulty'] === $difficulty);
        }
        
        return array_values($challenges);
    }
    
    /**
     * Get system design questions
     */
    public function getSystemDesignQuestions(string $difficulty = 'all'): array
    {
        $questions = $this->systemDesignQuestions;
        
        if ($difficulty !== 'all') {
            $questions = array_filter($questions, fn($q) => $q['difficulty'] === $difficulty);
        }
        
        return array_values($questions);
    }
    
    /**
     * Generate practice session
     */
    public function generatePracticeSession(array $options = []): array
    {
        $defaultOptions = [
            'technical_count' => 3,
            'behavioral_count' => 2,
            'coding_count' => 2,
            'system_design_count' => 1,
            'difficulty' => 'mixed'
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $session = [
            'technical_questions' => $this->getRandomQuestions($options['technical_count']),
            'behavioral_questions' => array_slice($this->getBehavioralQuestions(), 0, $options['behavioral_count']),
            'coding_challenges' => array_slice($this->getCodingChallenges($options['difficulty']), 0, $options['coding_count']),
            'system_design_questions' => array_slice($this->getSystemDesignQuestions($options['difficulty']), 0, $options['system_design_count'])
        ];
        
        return $session;
    }
}

// Mock Interview Simulator
class MockInterviewSimulator
{
    private InterviewQuestionBank $questionBank;
    private array $sessionData = [];
    private array $responses = [];
    
    public function __construct()
    {
        $this->questionBank = new InterviewQuestionBank();
    }
    
    /**
     * Start mock interview
     */
    public function startInterview(array $options = []): array
    {
        $session = $this->questionBank->generatePracticeSession($options);
        
        $this->sessionData = [
            'session_id' => uniqid('interview_'),
            'started_at' => time(),
            'questions' => $session,
            'current_question' => 0,
            'total_questions' => $this->countTotalQuestions($session),
            'duration' => 0
        ];
        
        return $this->sessionData;
    }
    
    /**
     * Count total questions
     */
    private function countTotalQuestions(array $session): int
    {
        return count($session['technical_questions']) +
               count($session['behavioral_questions']) +
               count($session['coding_challenges']) +
               count($session['system_design_questions']);
    }
    
    /**
     * Get next question
     */
    public function getNextQuestion(): ?array
    {
        if ($this->sessionData['current_question'] >= $this->sessionData['total_questions']) {
            return null;
        }
        
        $current = $this->sessionData['current_question'];
        $questions = $this->sessionData['questions'];
        
        // Find the current question type
        if ($current < count($questions['technical_questions'])) {
            return [
                'type' => 'technical',
                'question' => $questions['technical_questions'][$current],
                'number' => $current + 1
            ];
        } elseif ($current < count($questions['technical_questions']) + count($questions['behavioral_questions'])) {
            $index = $current - count($questions['technical_questions']);
            return [
                'type' => 'behavioral',
                'question' => $questions['behavioral_questions'][$index],
                'number' => $current + 1
            ];
        } elseif ($current < count($questions['technical_questions']) + count($questions['behavioral_questions']) + count($questions['coding_challenges'])) {
            $index = $current - count($questions['technical_questions']) - count($questions['behavioral_questions']);
            return [
                'type' => 'coding',
                'question' => $questions['coding_challenges'][$index],
                'number' => $current + 1
            ];
        } else {
            $index = $current - count($questions['technical_questions']) - count($questions['behavioral_questions']) - count($questions['coding_challenges']);
            return [
                'type' => 'system_design',
                'question' => $questions['system_design_questions'][$index],
                'number' => $current + 1
            ];
        }
    }
    
    /**
     * Submit response
     */
    public function submitResponse(string $response, array $metrics = []): array
    {
        $question = $this->getNextQuestion();
        
        if (!$question) {
            return ['error' => 'No more questions'];
        }
        
        $responseData = [
            'question_number' => $question['number'],
            'question_type' => $question['type'],
            'question_text' => $question['question']['question'] ?? $question['question']['title'],
            'response' => $response,
            'response_time' => $metrics['response_time'] ?? 0,
            'confidence' => $metrics['confidence'] ?? 0,
            'submitted_at' => time()
        ];
        
        $this->responses[] = $responseData;
        $this->sessionData['current_question']++;
        
        return [
            'success' => true,
            'question_answered' => $question['number'],
            'remaining_questions' => $this->sessionData['total_questions'] - $this->sessionData['current_question']
        ];
    }
    
    /**
     * End interview
     */
    public function endInterview(): array
    {
        $this->sessionData['ended_at'] = time();
        $this->sessionData['duration'] = $this->sessionData['ended_at'] - $this->sessionData['started_at'];
        $this->sessionData['responses'] = $this->responses;
        
        return [
            'session_summary' => $this->sessionData,
            'performance_analysis' => $this->analyzePerformance(),
            'recommendations' => $this->generateRecommendations()
        ];
    }
    
    /**
     * Analyze performance
     */
    private function analyzePerformance(): array
    {
        $analysis = [
            'total_questions' => count($this->responses),
            'average_response_time' => 0,
            'average_confidence' => 0,
            'performance_by_type' => [],
            'strengths' => [],
            'areas_for_improvement' => []
        ];
        
        if (empty($this->responses)) {
            return $analysis;
        }
        
        // Calculate averages
        $totalTime = array_sum(array_column($this->responses, 'response_time'));
        $totalConfidence = array_sum(array_column($this->responses, 'confidence'));
        
        $analysis['average_response_time'] = $totalTime / count($this->responses);
        $analysis['average_confidence'] = $totalConfidence / count($this->responses);
        
        // Analyze by question type
        $byType = [];
        foreach ($this->responses as $response) {
            $type = $response['question_type'];
            if (!isset($byType[$type])) {
                $byType[$type] = [
                    'count' => 0,
                    'total_time' => 0,
                    'total_confidence' => 0
                ];
            }
            
            $byType[$type]['count']++;
            $byType[$type]['total_time'] += $response['response_time'];
            $byType[$type]['total_confidence'] += $response['confidence'];
        }
        
        foreach ($byType as $type => $data) {
            $analysis['performance_by_type'][$type] = [
                'count' => $data['count'],
                'average_time' => $data['total_time'] / $data['count'],
                'average_confidence' => $data['total_confidence'] / $data['count']
            ];
        }
        
        // Identify strengths and areas for improvement
        foreach ($analysis['performance_by_type'] as $type => $performance) {
            if ($performance['average_confidence'] >= 4) {
                $analysis['strengths'][] = $type;
            } else {
                $analysis['areas_for_improvement'][] = $type;
            }
        }
        
        return $analysis;
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations(): array
    {
        $analysis = $this->analyzePerformance();
        $recommendations = [];
        
        // Response time recommendations
        if ($analysis['average_response_time'] > 60) {
            $recommendations[] = 'Practice responding more quickly to questions';
        }
        
        // Confidence recommendations
        if ($analysis['average_confidence'] < 3) {
            $recommendations[] = 'Build confidence through more practice and preparation';
        }
        
        // Type-specific recommendations
        foreach ($analysis['areas_for_improvement'] as $area) {
            switch ($area) {
                case 'technical':
                    $recommendations[] = 'Focus on studying PHP fundamentals and best practices';
                    break;
                case 'behavioral':
                    $recommendations[] = 'Practice STAR method for behavioral questions';
                    break;
                case 'coding':
                    $recommendations[] = 'Practice more coding challenges and algorithms';
                    break;
                case 'system_design':
                    $recommendations[] = 'Study system design patterns and architectures';
                    break;
            }
        }
        
        return $recommendations;
    }
}

// Interview Preparation Coach
class InterviewPreparationCoach
{
    private array $preparationPlan = [];
    private array $studyMaterials = [];
    private array $progressTracker = [];
    
    public function __construct()
    {
        $this->initializePreparationPlan();
    }
    
    /**
     * Initialize preparation plan
     */
    private function initializePreparationPlan(): void
    {
        $this->preparationPlan = [
            'technical_skills' => [
                'php_fundamentals' => [
                    'topics' => ['PHP syntax', 'Data types', 'Operators', 'Control structures'],
                    'resources' => ['PHP Manual', 'Online tutorials', 'Practice exercises'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'High'
                ],
                'oop_concepts' => [
                    'topics' => ['Classes', 'Inheritance', 'Interfaces', 'Design patterns'],
                    'resources' => ['Design Patterns book', 'OOP tutorials', 'Code examples'],
                    'time_estimate' => '3 weeks',
                    'priority' => 'High'
                ],
                'database_skills' => [
                    'topics' => ['SQL basics', 'Database design', 'Indexing', 'Transactions'],
                    'resources' => ['SQL tutorials', 'Database documentation', 'Practice problems'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'High'
                ],
                'framework_knowledge' => [
                    'topics' => ['Laravel basics', 'Symfony concepts', 'MVC patterns'],
                    'resources' => ['Framework documentation', 'Tutorial videos', 'Sample projects'],
                    'time_estimate' => '3 weeks',
                    'priority' => 'Medium'
                ],
                'security_practices' => [
                    'topics' => ['XSS prevention', 'SQL injection', 'Authentication', 'Authorization'],
                    'resources' => ['OWASP guidelines', 'Security tutorials', 'Best practices'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'High'
                ]
            ],
            'soft_skills' => [
                'communication' => [
                    'topics' => ['Technical explanations', 'Active listening', 'Clear articulation'],
                    'resources' => ['Communication courses', 'Practice sessions', 'Feedback'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'High'
                ],
                'problem_solving' => [
                    'topics' => ['Analytical thinking', 'Breakdown complex problems', 'Solution design'],
                    'resources' => ['Problem-solving frameworks', 'Practice problems', 'Case studies'],
                    'time_estimate' => '3 weeks',
                    'priority' => 'High'
                ],
                'teamwork' => [
                    'topics' => ['Collaboration', 'Conflict resolution', 'Leadership'],
                    'resources' => ['Team workshops', 'Role-playing', 'Real projects'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'Medium'
                ]
            ],
            'interview_techniques' => [
                'question_types' => [
                    'topics' => ['Technical questions', 'Behavioral questions', 'Coding challenges'],
                    'resources' => ['Question banks', 'Practice interviews', 'Answer frameworks'],
                    'time_estimate' => '2 weeks',
                    'priority' => 'High'
                ],
                'answer_frameworks' => [
                    'topics' => ['STAR method', 'Technical explanation structure', 'Problem-solving approach'],
                    'resources' => ['Framework guides', 'Examples', 'Practice templates'],
                    'time_estimate' => '1 week',
                    'priority' => 'High'
                ],
                'mock_interviews' => [
                    'topics' => ['Practice sessions', 'Feedback incorporation', 'Performance improvement'],
                    'resources' => ['Mock interview platforms', 'Peer practice', 'Professional coaching'],
                    'time_estimate' => 'Ongoing',
                    'priority' => 'High'
                ]
            ]
        ];
    }
    
    /**
     * Generate personalized study plan
     */
    public function generateStudyPlan(array $assessment): array
    {
        $plan = [
            'current_level' => $assessment['level'] ?? 'beginner',
            'target_role' => $assessment['target_role'] ?? 'php_developer',
            'time_available' => $assessment['time_available'] ?? 8, // hours per week
            'study_schedule' => [],
            'milestones' => [],
            'estimated_completion' => ''
        ];
        
        // Customize based on assessment
        $priorities = $this->determinePriorities($assessment);
        $schedule = $this->createStudySchedule($priorities, $plan['time_available']);
        
        $plan['study_schedule'] = $schedule;
        $plan['milestones'] = $this->createMilestones($schedule);
        $plan['estimated_completion'] = $this->estimateCompletionTime($schedule);
        
        return $plan;
    }
    
    /**
     * Determine priorities based on assessment
     */
    private function determinePriorities(array $assessment): array
    {
        $priorities = [];
        
        // Default priorities
        $priorities['technical_skills']['php_fundamentals'] = 'High';
        $priorities['technical_skills']['oop_concepts'] = 'High';
        $priorities['technical_skills']['database_skills'] = 'High';
        $priorities['soft_skills']['communication'] = 'High';
        $priorities['interview_techniques']['question_types'] = 'High';
        
        // Adjust based on assessment
        if (isset($assessment['weak_areas'])) {
            foreach ($assessment['weak_areas'] as $area) {
                if (isset($priorities['technical_skills'][$area])) {
                    $priorities['technical_skills'][$area] = 'Critical';
                }
            }
        }
        
        return $priorities;
    }
    
    /**
     * Create study schedule
     */
    private function createStudySchedule(array $priorities, int $hoursPerWeek): array
    {
        $schedule = [];
        $week = 1;
        
        foreach ($priorities as $category => $areas) {
            foreach ($areas as $area => $priority) {
                $areaData = $this->preparationPlan[$category][$area] ?? null;
                
                if (!$areaData) continue;
                
                $weeksNeeded = $this->calculateWeeksNeeded($areaData, $hoursPerWeek);
                
                for ($i = 0; $i < $weeksNeeded; $i++) {
                    if (!isset($schedule[$week])) {
                        $schedule[$week] = [];
                    }
                    
                    $schedule[$week][] = [
                        'category' => $category,
                        'area' => $area,
                        'topics' => $areaData['topics'],
                        'resources' => $areaData['resources'],
                        'priority' => $priority,
                        'hours' => min($hoursPerWeek / count($schedule[$week]), 8)
                    ];
                    
                    $week++;
                }
            }
        }
        
        return $schedule;
    }
    
    /**
     * Calculate weeks needed
     */
    private function calculateWeeksNeeded(array $areaData, int $hoursPerWeek): int
    {
        $timeEstimate = (int) filter_var($areaData['time_estimate'], FILTER_SANITIZE_NUMBER_INT);
        $hoursNeeded = $timeEstimate * 40; // Assuming 40 hours per week estimate
        
        return max(1, ceil($hoursNeeded / $hoursPerWeek));
    }
    
    /**
     * Create milestones
     */
    private function createMilestones(array $schedule): array
    {
        $milestones = [];
        $totalWeeks = count($schedule);
        
        $milestones[] = [
            'week' => 2,
            'title' => 'Complete PHP Fundamentals Review',
            'description' => 'Master basic PHP syntax and concepts'
        ];
        
        $milestones[] = [
            'week' => 4,
            'title' => 'Complete OOP Concepts Study',
            'description' => 'Understand object-oriented programming in PHP'
        ];
        
        $milestones[] = [
            'week' => 6,
            'title' => 'Complete Database Skills',
            'description' => 'Master SQL and database design basics'
        ];
        
        $milestones[] = [
            'week' => floor($totalWeeks / 2),
            'title' => 'Mid-Point Assessment',
            'description' => 'Evaluate progress and adjust plan'
        ];
        
        $milestones[] = [
            'week' => $totalWeeks - 2,
            'title' => 'Complete Mock Interviews',
            'description' => 'Practice and refine interview skills'
        ];
        
        $milestones[] = [
            'week' => $totalWeeks,
            'title' => 'Interview Ready',
            'description' => 'Fully prepared for technical interviews'
        ];
        
        return $milestones;
    }
    
    /**
     * Estimate completion time
     */
    private function estimateCompletionTime(array $schedule): string
    {
        $totalWeeks = count($schedule);
        
        if ($totalWeeks <= 4) {
            return $totalWeeks . ' weeks';
        } elseif ($totalWeeks <= 12) {
            return round($totalWeeks / 4, 1) . ' months';
        } else {
            return round($totalWeeks / 4, 1) . ' months';
        }
    }
    
    /**
     * Track progress
     */
    public function trackProgress(string $area, string $topic, bool $completed): void
    {
        if (!isset($this->progressTracker[$area])) {
            $this->progressTracker[$area] = [];
        }
        
        $this->progressTracker[$area][$topic] = [
            'completed' => $completed,
            'completed_at' => $completed ? time() : null
        ];
    }
    
    /**
     * Get progress report
     */
    public function getProgressReport(): array
    {
        $report = [
            'overall_progress' => 0,
            'progress_by_area' => [],
            'completed_topics' => 0,
            'total_topics' => 0,
            'next_recommendations' => []
        ];
        
        $totalTopics = 0;
        $completedTopics = 0;
        
        foreach ($this->preparationPlan as $category => $areas) {
            foreach ($areas as $area => $data) {
                $totalTopics += count($data['topics']);
                
                if (isset($this->progressTracker[$category][$area])) {
                    $completed = count(array_filter($this->progressTracker[$category][$area], fn($t) => $t['completed']));
                    $completedTopics += $completed;
                    
                    $report['progress_by_area'][$category][$area] = [
                        'completed' => $completed,
                        'total' => count($data['topics']),
                        'percentage' => ($completed / count($data['topics'])) * 100
                    ];
                }
            }
        }
        
        $report['overall_progress'] = $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0;
        $report['completed_topics'] = $completedTopics;
        $report['total_topics'] = $totalTopics;
        
        // Generate recommendations
        if ($report['overall_progress'] < 30) {
            $report['next_recommendations'][] = 'Focus on completing fundamental topics first';
        } elseif ($report['overall_progress'] < 60) {
            $report['next_recommendations'][] = 'Continue with intermediate topics and start mock interviews';
        } else {
            $report['next_recommendations'][] = 'Focus on advanced topics and intensive practice';
        }
        
        return $report;
    }
}

// Interview Preparation Examples
class InterviewPreparationExamples
{
    private InterviewQuestionBank $questionBank;
    private MockInterviewSimulator $simulator;
    private InterviewPreparationCoach $coach;
    
    public function __construct()
    {
        $this->questionBank = new InterviewQuestionBank();
        $this->simulator = new MockInterviewSimulator();
        $this->coach = new InterviewPreparationCoach();
    }
    
    public function demonstrateQuestionBank(): void
    {
        echo "Interview Question Bank Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Get questions by category
        $phpQuestions = $this->questionBank->getQuestionsByCategory('php_fundamentals');
        echo "PHP Fundamentals Questions:\n";
        foreach (array_slice($phpQuestions, 0, 2) as $question) {
            echo "  Q: {$question['question']}\n";
            echo "  Difficulty: {$question['difficulty']}\n";
            echo "  Sample Answer: " . substr($question['sample_answer'], 0, 100) . "...\n\n";
        }
        
        // Get behavioral questions
        $behavioralQuestions = $this->questionBank->getBehavioralQuestions('teamwork');
        echo "Behavioral Questions (Teamwork):\n";
        foreach ($behavioralQuestions as $question) {
            echo "  Q: {$question['question']}\n";
            echo "  Focus: {$question['focus']}\n";
            echo "  What interviewer wants: {$question['what_interviewer_wants']}\n\n";
        }
        
        // Get coding challenges
        $codingChallenges = $this->questionBank->getCodingChallenges('medium', 'algorithms');
        echo "Coding Challenges (Medium Algorithms):\n";
        foreach ($codingChallenges as $challenge) {
            echo "  Challenge: {$challenge['title']}\n";
            echo "  Description: {$challenge['description']}\n";
            echo "  Input: {$challenge['example_input']}\n";
            echo "  Output: {$challenge['example_output']}\n\n";
        }
        
        // Get system design questions
        $systemDesignQuestions = $this->questionBank->getSystemDesignQuestions();
        echo "System Design Questions:\n";
        foreach ($systemDesignQuestions as $question) {
            echo "  Q: {$question['question']}\n";
            echo "  Difficulty: {$question['difficulty']}\n";
            echo "  Components: " . implode(', ', $question['components']) . "\n";
            echo "  Considerations: " . implode(', ', $question['considerations']) . "\n\n";
        }
    }
    
    public function demonstrateMockInterview(): void
    {
        echo "\nMock Interview Simulator\n";
        echo str_repeat("-", 25) . "\n";
        
        // Start interview
        $session = $this->simulator->startInterview([
            'technical_count' => 2,
            'behavioral_count' => 1,
            'coding_count' => 1,
            'system_design_count' => 0
        ]);
        
        echo "Interview Started:\n";
        echo "Session ID: {$session['session_id']}\n";
        echo "Total Questions: {$session['total_questions']}\n";
        echo "Started At: " . date('Y-m-d H:i:s', $session['started_at']) . "\n\n";
        
        // Simulate answering questions
        echo "Answering Questions:\n";
        while ($question = $this->simulator->getNextQuestion()) {
            echo "Question {$question['number']} ({$question['type']}):\n";
            echo "  " . ($question['question']['question'] ?? $question['question']['title']) . "\n";
            
            // Simulate response
            $response = "This is a sample answer to the question...";
            $metrics = ['response_time' => rand(30, 120), 'confidence' => rand(3, 5)];
            
            $result = $this->simulator->submitResponse($response, $metrics);
            echo "  Response submitted (Time: {$metrics['response_time']}s, Confidence: {$metrics['confidence']})\n";
            echo "  Remaining: {$result['remaining_questions']} questions\n\n";
        }
        
        // End interview and get results
        $results = $this->simulator->endInterview();
        
        echo "Interview Results:\n";
        echo "Duration: {$results['session_summary']['duration']} seconds\n";
        echo "Questions Answered: {$results['performance_analysis']['total_questions']}\n";
        echo "Average Response Time: " . round($results['performance_analysis']['average_response_time'], 2) . " seconds\n";
        echo "Average Confidence: " . round($results['performance_analysis']['average_confidence'], 2) . "/5\n\n";
        
        echo "Performance by Type:\n";
        foreach ($results['performance_analysis']['performance_by_type'] as $type => $performance) {
            echo "  $type: {$performance['count']} questions, ";
            echo "Avg Time: " . round($performance['average_time'], 2) . "s, ";
            echo "Avg Confidence: " . round($performance['average_confidence'], 2) . "/5\n";
        }
        
        echo "\nRecommendations:\n";
        foreach ($results['recommendations'] as $recommendation) {
            echo "  • $recommendation\n";
        }
    }
    
    public function demonstrateStudyPlan(): void
    {
        echo "\nPersonalized Study Plan\n";
        echo str_repeat("-", 25) . "\n";
        
        // Sample assessment
        $assessment = [
            'level' => 'intermediate',
            'target_role' => 'senior_php_developer',
            'time_available' => 10,
            'weak_areas' => ['security_practices', 'system_design']
        ];
        
        // Generate study plan
        $plan = $this->coach->generateStudyPlan($assessment);
        
        echo "Study Plan Details:\n";
        echo "Current Level: {$plan['current_level']}\n";
        echo "Target Role: {$plan['target_role']}\n";
        echo "Time Available: {$plan['time_available']} hours/week\n";
        echo "Estimated Completion: {$plan['estimated_completion']}\n\n";
        
        echo "Sample Study Schedule (First 3 weeks):\n";
        foreach (array_slice($plan['study_schedule'], 0, 3, true) as $week => $topics) {
            echo "Week $week:\n";
            foreach ($topics as $topic) {
                echo "  • {$topic['area']} (Priority: {$topic['priority']})\n";
                echo "    Topics: " . implode(', ', array_slice($topic['topics'], 0, 2)) . "\n";
            }
            echo "\n";
        }
        
        echo "Milestones:\n";
        foreach (array_slice($plan['milestones'], 0, 3) as $milestone) {
            echo "  Week {$milestone['week']}: {$milestone['title']}\n";
            echo "    {$milestone['description']}\n";
        }
    }
    
    public function demonstrateProgressTracking(): void
    {
        echo "\nProgress Tracking\n";
        echo str_repeat("-", 18) . "\n";
        
        // Track some progress
        $this->coach->trackProgress('technical_skills', 'php_fundamentals', true);
        $this->coach->trackProgress('technical_skills', 'oop_concepts', true);
        $this->coach->trackProgress('technical_skills', 'database_skills', false);
        $this->coach->trackProgress('soft_skills', 'communication', true);
        
        // Get progress report
        $report = $this->coach->getProgressReport();
        
        echo "Progress Report:\n";
        echo "Overall Progress: " . round($report['overall_progress'], 1) . "%\n";
        echo "Completed: {$report['completed_topics']}/{$report['total_topics']} topics\n\n";
        
        echo "Progress by Area:\n";
        foreach ($report['progress_by_area'] as $category => $areas) {
            echo "  $category:\n";
            foreach ($areas as $area => $progress) {
                echo "    $area: {$progress['completed']}/{$progress['total']} ({$progress['percentage']}%)\n";
            }
        }
        
        echo "\nRecommendations:\n";
        foreach ($report['next_recommendations'] as $recommendation) {
            echo "  • $recommendation\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nInterview Preparation Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "Technical Preparation:\n";
        echo "  • Master PHP fundamentals and OOP concepts\n";
        echo "  • Practice coding challenges regularly\n";
        echo "  • Study system design patterns\n";
        echo "  • Review database concepts and SQL\n";
        echo "  • Understand security best practices\n";
        echo "  • Learn framework-specific knowledge\n\n";
        
        echo "Behavioral Preparation:\n";
        echo "  • Use STAR method for answers\n";
        echo "  • Prepare specific examples\n";
        echo "  • Practice storytelling\n";
        echo "  • Research the company\n";
        echo "  • Prepare questions to ask\n";
        echo "  • Practice with mock interviews\n\n";
        
        echo "Day of Interview:\n";
        echo "  • Get a good night\'s sleep\n";
        echo "  • Eat a healthy breakfast\n";
        echo "  • Arrive 10-15 minutes early\n";
        echo "  • Bring copies of your resume\n";
        echo "  • Dress professionally\n";
        echo "  • Stay calm and confident\n";
        echo "  • Listen carefully to questions\n\n";
        
        echo "Post-Interview:\n";
        echo "  • Send thank-you notes\n";
        echo "  • Reflect on performance\n";
        echo "  • Follow up appropriately\n";
        echo "  • Continue job searching\n";
        echo "  • Learn from experience\n";
        echo "  • Update your approach\n";
        echo "  • Stay positive";
    }
    
    public function runAllExamples(): void
    {
        echo "Interview Preparation Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateQuestionBank();
        $this->demonstrateMockInterview();
        $this->demonstrateStudyPlan();
        $this->demonstrateProgressTracking();
        $this->demonstrateBestPractices();
    }
}

// Interview Preparation Best Practices
function printInterviewPreparationBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Interview Preparation Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Technical Preparation:\n";
    echo "   • Master core PHP concepts\n";
    echo "   • Practice coding challenges daily\n";
    echo "   • Study system design patterns\n";
    echo "   • Review database fundamentals\n";
    echo "   • Understand security principles\n\n";
    
    echo "2. Behavioral Preparation:\n";
    echo "   • Use STAR method consistently\n";
    echo "   • Prepare specific examples\n";
    echo "   • Practice storytelling skills\n";
    echo "   • Research company culture\n";
    echo "   • Prepare thoughtful questions\n\n";
    
    echo "3. Mock Interviews:\n";
    echo "   • Practice regularly\n";
    echo "   • Record and review sessions\n";
    echo "   • Seek constructive feedback\n";
    echo "   • Simulate real conditions\n";
    echo "   • Practice time management\n\n";
    
    echo "4. Interview Day:\n";
    echo "   • Get adequate rest\n";
    echo "   • Arrive early\n";
    echo "   • Dress professionally\n";
    echo "   • Bring necessary materials\n";
    echo "   • Maintain positive attitude\n\n";
    
    echo "5. Follow-up:\n";
    echo "   • Send thank-you notes\n";
    echo "   • Reflect on performance\n";
    echo "   • Continue job search\n";
    echo "   • Learn from experience\n";
    echo "   • Stay persistent";
}

// Main execution
function runInterviewPreparationDemo(): void
{
    $examples = new InterviewPreparationExamples();
    $examples->runAllExamples();
    printInterviewPreparationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runInterviewPreparationDemo();
}
?>
