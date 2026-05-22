<?php
/**
 * System Design Exercise
 * 
 * Comprehensive system design challenges to test understanding
 * of architecture, scalability, and design patterns.
 */

// System Design Framework
class SystemDesignFramework
{
    private array $designChallenges = [];
    private array $submissions = [];
    private array $evaluations = [];
    
    public function __construct()
    {
        $this->initializeDesignChallenges();
    }
    
    /**
     * Initialize design challenges
     */
    private function initializeDesignChallenges(): void
    {
        $this->designChallenges = [
            'url_shortener' => [
                'title' => 'URL Shortener Service',
                'description' => 'Design a URL shortening service like bit.ly',
                'difficulty' => 'medium',
                'estimated_time' => '45 minutes',
                'requirements' => [
                    'Generate short URLs for long URLs',
                    'Redirect short URLs to original URLs',
                    'Handle high volume of redirects',
                    'Track click statistics',
                    'Handle custom short URLs',
                    'API for creating and managing URLs'
                ],
                'constraints' => [
                    'Handle 1M+ URLs',
                    '100ms redirect time',
                    '99.99% availability',
                    'Support analytics'
                ],
                'evaluation_criteria' => [
                    'architecture' => 25,
                    'scalability' => 25,
                    'database_design' => 20,
                    'api_design' => 15,
                    'performance' => 15
                ]
            ],
            'chat_application' => [
                'title' => 'Real-time Chat Application',
                'description' => 'Design a real-time chat application like WhatsApp',
                'difficulty' => 'hard',
                'estimated_time' => '60 minutes',
                'requirements' => [
                    'Real-time messaging',
                    'User authentication',
                    'Contact management',
                    'Message history',
                    'Online status',
                    'Group chats',
                    'File sharing',
                    'Push notifications'
                ],
                'constraints' => [
                    '1M concurrent users',
                    '100ms message delivery',
                    'Message persistence',
                    'File upload support',
                    'Mobile compatibility'
                ],
                'evaluation_criteria' => [
                    'architecture' => 30,
                    'scalability' => 25,
                    'real_time_design' => 20,
                    'database_design' => 15,
                    'api_design' => 10
                ]
            ],
            'social_media' => [
                'title' => 'Social Media Platform',
                'description' => 'Design a social media platform like Twitter',
                'difficulty' => 'hard',
                'estimated_time' => '75 minutes',
                'requirements' => [
                    'User profiles and authentication',
                    'Posting and timeline',
                    'Follow/follower system',
                    'Like and comment system',
                    'Hashtag support',
                    'Search functionality',
                    'Trending topics',
                    'Analytics dashboard'
                ],
                'constraints' => [
                    '10M users',
                    '100K posts per second',
                    'Real-time feed updates',
                    'Media support',
                    'Global availability'
                ],
                'evaluation_criteria' => [
                    'architecture' => 30,
                    'scalability' => 25,
                    'database_design' => 20,
                    'real_time_design' => 15,
                    'performance' => 10
                ]
            ],
            'video_streaming' => [
                'title' => 'Video Streaming Service',
                'description' => 'Design a video streaming service like YouTube',
                'difficulty' => 'hard',
                'estimated_time' => '90 minutes',
                'requirements' => [
                    'Video upload and processing',
                    'Video streaming',
                    'User authentication',
                    'Video recommendations',
                    'Comments and likes',
                    'Live streaming',
                    'Video analytics',
                    'Monetization'
                ],
                'constraints' => [
                    '1M concurrent streams',
                    '4K video support',
                    'Low latency streaming',
                    'Ad insertion',
                    'Global CDN',
                    'DRM protection'
                ],
                'evaluation_criteria' => [
                    'architecture' => 30,
                    'scalability' => 30,
                    'media_handling' => 20,
                    'real_time_design' => 10,
                    'performance' => 10
                ]
            ],
            'ecommerce_platform' => [
                'title' => 'E-commerce Platform',
                'description' => 'Design an e-commerce platform like Amazon',
                'difficulty' => 'medium',
                'estimated_time' => '60 minutes',
                'requirements' => [
                    'Product catalog',
                    'Shopping cart',
                    'User authentication',
                    'Order processing',
                    'Payment integration',
                    'Inventory management',
                    'Search and filtering',
                    'Recommendations',
                    'Order tracking'
                ],
                'constraints' => [
                    '100K products',
                    '10K concurrent users',
                    '99.99% uptime',
                    'Payment security',
                    'Real-time inventory'
                ],
                'evaluation_criteria' => [
                    'architecture' => 25,
                    'scalability' => 25,
                    'database_design' => 20,
                    'security' => 15,
                    'performance' => 15
                ]
            ]
        ];
    }
    
    /**
     * Get design challenge
     */
    public function getDesignChallenge(string $id): ?array
    {
        return $this->designChallenges[$id] ?? null;
    }
    
    /**
     * Get all design challenges
     */
    public function getAllDesignChallenges(): array
    {
        return $this->designChallenges;
    }
    
    /**
     * Submit design solution
     */
    public function submitDesign(string $challengeId, array $solution): void
    {
        $this->submissions[$challengeId] = array_merge([
            'id' => uniqid('design_'),
            'challenge_id' => $challengeId,
            'submitted_at' => time(),
            'status' => 'pending'
        ], $solution);
        
        // Evaluate the design
        $this->evaluateDesign($challengeId);
    }
    
    /**
     * Evaluate design submission
     */
    private function evaluateDesign(string $challengeId): void
    {
        $challenge = $this->designChallenges[$challengeId];
        $submission = $this->submissions[$challengeId];
        
        $evaluation = [
            'challenge_id' => $challengeId,
            'submission_id' => $submission['id'],
            'evaluated_at' => time(),
            'scores' => [],
            'feedback' => [],
            'total_score' => 0,
            'passed' => false
        ];
        
        // Evaluate each criterion
        foreach ($challenge['evaluation_criteria'] as $criterion => $weight) {
            $score = $this->evaluateCriterion($criterion, $submission);
            $evaluation['scores'][$criterion] = [
                'score' => $score,
                'weight' => $weight,
                'weighted_score' => $score * ($weight / 100)
            ];
            
            $evaluation['total_score'] += $evaluation['scores'][$criterion]['weighted_score'];
        }
        
        $evaluation['passed'] = $evaluation['total_score'] >= 70;
        $evaluation['feedback'] = $this->generateFeedback($evaluation, $challenge);
        
        $this->evaluations[$challengeId] = $evaluation;
        $this->submissions[$challengeId]['status'] = 'evaluated';
        $this->submissions[$challengeId]['evaluation'] = $evaluation;
    }
    
    /**
     * Evaluate individual criterion
     */
    private function evaluateCriterion(string $criterion, array $submission): int
    {
        switch ($criterion) {
            case 'architecture':
                return $this->evaluateArchitecture($submission);
            case 'scalability':
                return $this->evaluateScalability($submission);
            case 'database_design':
                return $this->evaluateDatabaseDesign($submission);
            case 'api_design':
                return $this->evaluateApiDesign($submission);
            case 'performance':
                return $this->evaluatePerformance($submission);
            case 'real_time_design':
                return $this->evaluateRealTimeDesign($submission);
            case 'security':
                return $this->evaluateSecurity($submission);
            case 'media_handling':
                return $this->evaluateMediaHandling($submission);
            default:
                return 75; // Default score
        }
    }
    
    /**
     * Evaluation methods for different criteria
     */
    private function evaluateArchitecture(array $submission): int
    {
        $score = 75;
        
        // Check for microservices architecture
        if (isset($submission['architecture_pattern']) && $submission['architecture_pattern'] === 'microservices') {
            $score += 10;
        }
        
        // Check for proper separation of concerns
        if (isset($submission['separation_of_concerns']) && $submission['separation_of_concerns'] === 'good') {
            $score += 10;
        }
        
        // Check for load balancing
        if (isset($submission['load_balancing']) && $submission['load_balancing'] === 'implemented') {
            $score += 5;
        }
        
        // Check for caching strategy
        if (isset($submission['caching_strategy']) && $submission['caching_strategy'] === 'comprehensive') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateScalability(array $submission): int
    {
        $score = 70;
        
        // Check for horizontal scaling
        if (isset($submission['horizontal_scaling']) && $submission['horizontal_scaling'] === 'supported') {
            $score += 15;
        }
        
        // Check for database sharding
        if (isset($submission['database_sharding']) && $submission['database_sharding'] === 'planned') {
            $score += 10;
        }
        
        // Check for CDN usage
        if (isset($submission['cdn']) && $submission['cdn'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateDatabaseDesign(array $submission): int
    {
        $score = 75;
        
        // Check for proper normalization
        if (isset($submission['normalization']) && $submission['normalization'] === 'proper') {
            $score += 10;
        }
        
        // Check for indexing strategy
        if (isset($submission['indexing']) && $submission['indexing'] === 'optimized') {
            $score += 10;
        }
        
        // Check for connection pooling
        if (isset($submission['connection_pooling']) && $submission['connection_pooling'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateApiDesign(array $submission): int
    {
        $score = 70;
        
        // Check for RESTful design
        if (isset($submission['restful']) && $submission['restful'] === 'implemented') {
            $score += 15;
        }
        
        // Check for proper HTTP status codes
        if (isset($submission['status_codes']) && $submission['status_codes'] === 'proper') {
            $score += 10;
        }
        
        // Check for API versioning
        if (isset($submission['versioning']) && $submission['versioning'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluatePerformance(array $submission): int
    {
        $score = 75;
        
        // Check for caching implementation
        if (isset($submission['caching']) && $submission['caching'] === 'comprehensive') {
            $score += 10;
        }
        
        // Check for optimization techniques
        if (isset($submission['optimization']) && $submission['optimization'] === 'implemented') {
            $score += 10;
        }
        
        // Check for monitoring
        if (isset($submission['monitoring']) && $submission['monitoring'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateRealTimeDesign(array $submission): int
    {
        $score = 70;
        
        // Check for WebSocket implementation
        if (isset($submission['websockets']) && $submission['websockets'] === 'implemented') {
            $score += 15;
        }
        
        // Check for message queuing
        if (isset($submission['message_queue']) && $submission['message_queue'] === 'implemented') {
            $score += 10;
        }
        
        // Check for event-driven architecture
        if (isset($submission['event_driven']) && $submission['event_driven'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateSecurity(array $submission): int
    {
        $score = 75;
        
        // Check for authentication
        if (isset($submission['authentication']) && $submission['authentication'] === 'comprehensive') {
            $score += 10;
        }
        
        // Check for authorization
        if (isset($submission['authorization']) && $submission['authorization'] === 'implemented') {
            $score += 10;
        }
        
        // Check for encryption
        if (isset($submission['encryption']) && $submission['encryption'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    private function evaluateMediaHandling(array $submission): int
    {
        $score = 75;
        
        // Check for CDN usage
        if (isset($submission['cdn']) && $submission['cdn'] === 'implemented') {
            $score += 15;
        }
        
        // Check for transcoding
        if (isset($submission['transcoding']) && $submission['transcoding'] === 'implemented') {
            $score += 10;
        }
        
        // Check for storage optimization
        if (isset($submission['storage_optimization']) && $submission['storage_optimization'] === 'implemented') {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    /**
     * Generate feedback
     */
    private function generateFeedback(array $evaluation, array $challenge): array
    {
        $feedback = [];
        
        foreach ($evaluation['scores'] as $criterion => $scoreData) {
            if ($scoreData['score'] < 70) {
                $feedback[] = "Improve $criterion: Current score is " . round($scoreData['score'], 1) . "%";
            }
        }
        
        if ($evaluation['passed']) {
            $feedback[] = "Excellent design! Architecture meets all requirements.";
        } else {
            $feedback[] = "Design needs improvement to pass. Focus on areas with scores below 70%.";
        }
        
        return $feedback;
    }
    
    /**
     * Get submission
     */
    public function getSubmission(string $challengeId): ?array
    {
        return $this->submissions[$challengeId] ?? null;
    }
    
    /**
     * Get evaluation
     */
    public function getEvaluation(string $challengeId): ?array
    {
        return $this->evaluations[$challengeId] ?? null;
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
        $passedDesigns = 0;
        $totalDesigns = count($this->evaluations);
        
        foreach ($this->evaluations as $evaluation) {
            $totalScore += $evaluation['total_score'];
            if ($evaluation['passed']) {
                $passedDesigns++;
            }
        }
        
        $averageScore = $totalDesigns > 0 ? $totalScore / $totalDesigns : 0;
        
        return [
            'average_score' => $averageScore,
            'passed_designs' => $passedDesigns,
            'total_designs' => $totalDesigns,
            'pass_rate' => $totalDesigns > 0 ? ($passedDesigns / $totalDesigns) * 100 : 0,
            'overall_passed' => $passedDesigns >= 2 // Require at least 2 passed designs
        ];
    }
}

// System Design Templates and Examples
class SystemDesignTemplates
{
    private array $templates = [];
    
    public function __construct()
    {
        $this->initializeTemplates();
    }
    
    /**
     * Initialize design templates
     */
    private function initializeTemplates(): void
    {
        $this->templates = [
            'url_shortener' => [
                'components' => [
                    'url_shortener_service' => [
                        'description' => 'Core service for generating and managing short URLs',
                        'responsibilities' => [
                            'Generate short URLs',
                            'Store URL mappings',
                            'Handle redirects',
                            'Track analytics'
                        ]
                    ],
                    'redirect_service' => [
                        'description' => 'Handles URL redirections',
                        'responsibilities' => [
                            'Fast URL lookup',
                            'HTTP redirection',
                            'Analytics tracking'
                        ]
                    ],
                    'analytics_service' => [
                        'description' => 'Tracks click statistics and analytics',
                        'responsibilities' => [
                            'Click counting',
                            'User analytics',
                            'URL performance',
                            'Reporting'
                        ]
                    ],
                    'api_gateway' => [
                        'description' => 'API gateway for external communication',
                        'responsibilities' => [
                            'Request routing',
                            'Rate limiting',
                            'Authentication',
                            'Response formatting'
                        ]
                    ]
                ],
                'database_design' => [
                    'tables' => [
                        'urls' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'short_code' => 'VARCHAR(10) UNIQUE NOT NULL',
                            'original_url' => 'TEXT NOT NULL',
                            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'expires_at' => 'TIMESTAMP NULL',
                            'click_count' => 'BIGINT DEFAULT 0',
                            'created_by' => 'BIGINT NULL',
                            'INDEX idx_short_code (short_code)',
                            'INDEX idx_created_by (created_by)'
                        ],
                        'users' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'username' => 'VARCHAR(50) UNIQUE NOT NULL',
                            'email' => 'VARCHAR(100) UNIQUE NOT NULL',
                            'password_hash' => 'VARCHAR(255) NOT NULL',
                            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'api_key' => 'VARCHAR(64) UNIQUE NULL'
                        ],
                        'analytics' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'url_id' => 'BIGINT NOT NULL',
                            'clicked_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'ip_address' => 'VARCHAR(45) NOT NULL',
                            'user_agent' => 'TEXT NULL',
                            'country' => 'VARCHAR(2) NULL',
                            'ref' => 'VARCHAR(255) NULL',
                            'FOREIGN KEY (url_id) REFERENCES urls(id)'
                        ]
                    ],
                    'indexes' => [
                        'Primary indexes on all tables',
                        'Index on short_code for fast lookups',
                        'Index on created_at for analytics',
                        'Composite index on (url_id, clicked_at)'
                    ]
                ],
                'architecture' => [
                    'type' => 'Microservices',
                    'components' => [
                        'API Gateway' => 'Request routing and load balancing',
                        'URL Shortener Service' => 'Core business logic',
                        'Redirect Service' => 'High-performance redirects',
                        'Analytics Service' => 'Data aggregation',
                        'User Service' => 'User management'
                    ],
                    'data_flow' => [
                        'User Request → API Gateway → URL Shortener Service',
                        'Redirect Request → API Gateway → Redirect Service',
                        'Analytics Data → Redirect Service → Analytics Service'
                    ],
                    'scalability' => [
                        'Horizontal scaling of services',
                        'Database sharding by URL hash',
                        'CDN for static assets',
                        'Caching layer for hot URLs'
                    ]
                ],
                'api_endpoints' => [
                    'POST /api/v1/urls' => 'Create short URL',
                    'GET /api/v1/urls/{short_code}' => 'Redirect to original URL',
                    'GET /api/v1/urls/{short_code}/stats' => 'Get URL statistics',
                    'DELETE /api/v1/urls/{id}' => 'Delete URL',
                    'GET /api/v1/users/me/urls' => 'Get user URLs'
                ],
                'technology_stack' => [
                    'Backend' => 'Node.js/Python/Go',
                    'Database' => 'PostgreSQL/MySQL',
                    'Cache' => 'Redis/Memcached',
                    'Message Queue' => 'RabbitMQ/Kafka',
                    'Monitoring' => 'Prometheus/Grafana'
                ]
            ],
            'chat_application' => [
                'components' => [
                    'websocket_server' => [
                        'description' => 'WebSocket server for real-time communication',
                        'responsibilities' => [
                            'Connection management',
                            'Message routing',
                            'Room management',
                            'User presence'
                        ]
                    ],
                    'message_service' => [
                        'description' => 'Core messaging functionality',
                        'responsibilities' => [
                            'Message storage',
                            'Message retrieval',
                            'Message delivery',
                            'Message history'
                        ]
                    ],
                    'user_service' => [
                        'description' => 'User management and authentication',
                        'responsibilities' => [
                            'User registration',
                            'Authentication',
                            'Profile management',
                            'Contact management'
                        ]
                    ],
                    'notification_service' => [
                        'description' => 'Push notifications and alerts',
                        'responsibilities' => [
                            'Push notification delivery',
                            'Email notifications',
                            'In-app notifications',
                            'Notification preferences'
                        ]
                    ]
                ],
                'database_design' => [
                    'tables' => [
                        'users' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'username' => 'VARCHAR(50) UNIQUE NOT NULL',
                            'email' => 'VARCHAR(100) UNIQUE NOT NULL',
                            'password_hash' => 'VARCHAR(255) NOT NULL',
                            'avatar_url' => 'VARCHAR(255) NULL',
                            'status' => 'ENUM("online", "offline", "away") DEFAULT "offline"',
                            'last_seen' => 'TIMESTAMP NULL',
                            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                        ],
                        'rooms' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'name' => 'VARCHAR(100) NOT NULL',
                            'type' => 'ENUM("private", "group") DEFAULT "private"',
                            'created_by' => 'BIGINT NOT NULL',
                            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'FOREIGN KEY (created_by) REFERENCES users(id)'
                        ],
                        'messages' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'room_id' => 'BIGINT NOT NULL',
                            'sender_id' => 'BIGINT NOT NULL',
                            'content' => 'TEXT NOT NULL',
                            'message_type' => 'ENUM("text", "image", "file") DEFAULT "text',
                            'file_url' => 'VARCHAR(255) NULL',
                            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'FOREIGN KEY (room_id) REFERENCES rooms(id)',
                            'FOREIGN KEY (sender_id) REFERENCES users(id)'
                        ],
                        'room_members' => [
                            'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                            'room_id' => 'BIGINT NOT NULL',
                            'user_id' => 'BIGINT NOT NULL',
                            'role' => 'ENUM("admin", "member") DEFAULT "member"',
                            'joined_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                            'FOREIGN KEY (room_id) REFERENCES rooms(id)',
                            'FOREIGN KEY (user_id) REFERENCES users(id)',
                            'UNIQUE KEY (room_id, user_id)'
                        ]
                    ],
                    'indexes' => [
                        'Primary indexes on all tables',
                        'Index on room_id for message retrieval',
                        'Index on sender_id for user messages',
                        'Index on created_at for message history',
                        'Composite index on (room_id, created_at)'
                    ]
                ],
                'architecture' => [
                    'type' => 'Microservices',
                    'components' => [
                        'WebSocket Gateway' => 'WebSocket connection management',
                        'Message Service' => 'Message handling and storage',
                        'User Service' => 'User management',
                        'Room Service' => 'Room management',
                        'Notification Service' => 'Push notifications',
                        'Presence Service' => 'User presence tracking'
                    ],
                    'data_flow' => [
                        'WebSocket Connection → Message Service → Broadcast to clients',
                        'Message → Database → Notification Service → Push notifications'
                    ],
                    'scalability' => [
                        'Horizontal scaling of WebSocket servers',
                        'Database sharding by room_id',
                        'Redis for real-time data',
                        'Message queue for notifications'
                    ]
                ],
                'api_endpoints' => [
                    'POST /api/v1/auth/login' => 'User authentication',
                    'POST /api/v1/rooms' => 'Create room',
                    'POST /api/v1/rooms/{id}/messages' => 'Send message',
                    'GET /api/v1/rooms/{id}/messages' => 'Get message history',
                    'GET /api/v1/rooms/{id}/members' => 'Get room members',
                    'GET /api/v1/users/{id}/status' => 'Get user status'
                ],
                'technology_stack' => [
                    'WebSocket' => 'Socket.io/WebSockets',
                    'Backend' => 'Node.js/Go/Java',
                    'Database' => 'MongoDB/PostgreSQL',
                    'Cache' => 'Redis',
                    'Message Queue' => 'RabbitMQ/Apache Kafka',
                    'Push Notifications' => 'FCM/APNs'
                ]
            ]
        ];
    }
    
    /**
     * Get template
     */
    public function getTemplate(string $templateId): ?array
    {
        return $this->templates[$templateId] ?? null;
    }
    
    /**
     * Get all templates
     */
    public function getAllTemplates(): array
    {
        return $this->templates;
    }
}

// System Design Examples
class SystemDesignExamples
{
    private SystemDesignFramework $framework;
    private SystemDesignTemplates $templates;
    
    public function __construct()
    {
        $this->framework = new SystemDesignFramework();
        $this->templates = new SystemDesignTemplates();
    }
    
    public function demonstrateDesignOverview(): void
    {
        echo "System Design Exercise Overview\n";
        echo str_repeat("-", 35) . "\n";
        
        $challenges = $this->framework->getAllDesignChallenges();
        
        echo "Available Design Challenges:\n\n";
        
        foreach ($challenges as $id => $challenge) {
            echo "$id: {$challenge['title']}\n";
            echo "  Difficulty: {$challenge['difficulty']}\n";
            echo "  Estimated Time: {$challenge['estimated_time']}\n";
            echo "  Description: {$challenge['description']}\n";
            echo "  Requirements:\n";
            
            foreach (array_slice($challenge['requirements'], 0, 3) as $requirement) {
                echo "    • $requirement\n";
            }
            
            if (count($challenge['requirements']) > 3) {
                echo "    • And " . (count($challenge['requirements']) - 3) . " more...\n";
            }
            
            echo "  Constraints:\n";
            foreach ($challenge['constraints'] as $constraint) {
                echo "    • $constraint\n";
            }
            
            echo "\n";
        }
        
        echo "Evaluation Criteria:\n";
        echo "  • Architecture: System design and component organization\n";
        echo "  • Scalability: Ability to handle growth and load\n";
        echo "  • Database Design: Data modeling and optimization\n";
        echo "  • API Design: RESTful principles and interface design\n";
        echo "  • Performance: Optimization and efficiency\n";
        echo "  • Real-time Design: Real-time capabilities\n";
        echo "  • Security: Authentication, authorization, and protection\n";
        echo "  • Media Handling: File and media processing\n";
    }
    
    public function demonstrateDesignSubmission(): void
    {
        echo "\nDesign Submission Example\n";
        echo str_repeat("-", 28) . "\n";
        
        // Simulate submitting a URL shortener design
        $submission = [
            'architecture_pattern' => 'microservices',
            'separation_of_concerns' => 'good',
            'load_balancing' => 'implemented',
            'caching_strategy' => 'comprehensive',
            'horizontal_scaling' => 'supported',
            'database_sharding' => 'planned',
            'cdn' => 'implemented',
            'normalization' => 'proper',
            'indexing' => 'optimized',
            'connection_pooling' => 'implemented',
            'restful' => 'implemented',
            'status_codes' => 'proper',
            'versioning' => 'implemented'
        ];
        
        echo "Submitting URL Shortener Design...\n";
        $this->framework->submitDesign('url_shortener', $submission);
        
        // Get evaluation results
        $evaluation = $this->framework->getEvaluation('url_shortener');
        
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
    
    public function demonstrateDesignTemplates(): void
    {
        echo "\nDesign Templates and Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        $template = $this->templates->getTemplate('url_shortener');
        
        echo "URL Shortener Template:\n";
        echo "Components:\n";
        foreach ($template['components'] as $component => $details) {
            echo "  $component:\n";
            echo "    Description: {$details['description']}\n";
            echo "    Responsibilities:\n";
            foreach ($details['responsibilities'] as $responsibility) {
                echo "      • $responsibility\n";
            }
            echo "\n";
        }
        
        echo "Database Design:\n";
        echo "Tables:\n";
        foreach ($template['database_design']['tables'] as $table => $fields) {
            echo "  $table:\n";
            foreach ($fields as $field => $definition) {
                echo "    $field: $definition\n";
            }
            echo "\n";
        }
        
        echo "Architecture:\n";
        echo "Type: {$template['architecture']['type']}\n";
        echo "Components:\n";
        foreach ($template['architecture']['components'] as $component => $description) {
            echo "  $component: $description\n";
        }
        echo "\n";
        
        echo "Technology Stack:\n";
        foreach ($template['technology_stack'] as $area => $technology) {
            echo "  $area: $technology\n";
        }
    }
    
    public function demonstrateMultipleDesigns(): void
    {
        echo "\nMultiple Design Submissions\n";
        echo str_repeat("-", 30) . "\n";
        
        // Simulate submitting multiple designs
        $designs = [
            'url_shortener' => [
                'architecture_pattern' => 'microservices',
                'horizontal_scaling' => 'supported',
                'cdn' => 'implemented',
                'normalization' => 'proper'
            ],
            'chat_application' => [
                'architecture_pattern' => 'microservices',
                'websockets' => 'implemented',
                'message_queue' => 'implemented',
                'event_driven' => 'implemented'
            ],
            'social_media' => [
                'architecture_pattern' => 'microservices',
                'horizontal_scaling' => 'supported',
                'cdn' => 'implemented',
                'real_time_updates' => 'implemented'
            ]
        ];
        
        foreach ($designs as $designId => $submission) {
            echo "Submitting $designId...\n";
            $this->framework->submitDesign($designId, $submission);
            
            $evaluation = $this->framework->getEvaluation($designId);
            echo "Score: " . round($evaluation['total_score'], 1) . "% - " . ($evaluation['passed'] ? 'PASSED' : 'FAILED') . "\n";
        }
        
        $overallScore = $this->framework->calculateOverallScore();
        
        echo "\nOverall Results:\n";
        echo "Average Score: " . round($overallScore['average_score'], 1) . "%\n";
        echo "Passed Designs: {$overallScore['passed_designs']}/{$overallScore['total_designs']}\n";
        echo "Pass Rate: " . round($overallScore['pass_rate'], 1) . "%\n";
        echo "Overall Status: " . ($overallScore['overall_passed'] ? 'PASSED' : 'FAILED') . "\n";
    }
    
    public function demonstrateDesignProcess(): void
    {
        echo "\nSystem Design Process\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "1. Requirements Analysis\n";
        echo "   • Understand all functional requirements\n";
        echo "   • Identify non-functional requirements\n";
        echo "   • Define constraints and assumptions\n";
        echo "   • Estimate scale and load\n\n";
        
        echo "2. High-Level Design\n";
        echo "   • Choose architecture pattern\n";
        "   • Define major components\n";
        echo "   • Create data flow diagram\n";
        echo "   • Identify technology stack\n\n";
        
        echo "3. Component Design\n";
        echo "   • Design individual components\n";
        echo "   • Define interfaces between components\n";
        echo "   • Plan database schema\n";
        echo "   • Design API endpoints\n\n";
        
        echo "4. Scalability Design\n";
        echo "   • Plan horizontal scaling\n";
        echo "   • Design caching strategy\n";
        echo "   • Plan database scaling\n";
        echo "   • Consider CDN usage\n";
        echo "   • Design load balancing\n\n";
        
        echo "5. Security Design\n";
        echo "   • Design authentication system\n";
        echo "   • Plan authorization mechanisms\n";
        echo "   • Consider data encryption\n";
        echo "   • Plan API security\n";
        echo "   • Design monitoring\n\n";
        
        echo "6. Documentation\n";
        echo "   • Create architecture diagrams\n";
        echo "   • Document API endpoints\n";
        echo "   • Explain design decisions\n";
        echo "   • Include trade-offs\n";
        echo "   • Add examples\n\n";
        
        echo "Evaluation Criteria:\n";
        echo "• Architecture (25-30%)\n";
        echo "• Scalability (20-30%)\n";
        echo "• Database Design (15-20%)\n";
        echo "• API Design (10-15%)\n";
        echo "• Performance (10-15%)\n";
        echo "• Security (10-15%)\n";
        echo "• Real-time Design (10-20%)\n";
        echo "• Media Handling (10-15%)\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nSystem Design Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Start with Requirements\n";
        echo "   • Clarify functional requirements\n";
        echo "   • Define non-functional requirements\n";
        echo "   • Ask clarifying questions\n";
        echo "   • Document assumptions\n\n";
        
        echo "2. Use Established Patterns\n";
        echo "   • Use proven architectural patterns\n";
        echo "   • Apply SOLID principles\n";
        echo "   • Use design patterns appropriately\n";
        echo "   • Consider trade-offs\n\n";
        
        echo "3. Think About Scale\n";
        echo "   • Design for horizontal scaling\n";
        echo "   • Plan for bottlenecks\n";
        echo "   • Consider caching strategies\n";
        echo "   • Plan database scaling\n";
        echo "   • Use CDN for static content\n\n";
        
        echo "4. Consider Security\n";
        echo "   • Design for authentication\n";
        echo "   • Plan authorization\n";
        echo "   • Consider data protection\n";
        echo "   • Plan API security\n";
        echo "   • Design monitoring\n\n";
        
        echo "5. Document Everything\n";
        echo "   • Create clear diagrams\n";
        echo "   • Explain design decisions\n";
        echo "   • Document trade-offs\n";
        echo "   • Include examples\n";
        echo "   • Provide alternatives\n\n";
        
        echo "Common Mistakes to Avoid:\n";
        echo "• Starting without requirements\n";
        echo "• Over-engineering solutions\n";
        "• Ignoring scalability\n";
        echo "• Forgetting about security\n";
        echo "• Not documenting decisions\n";
        echo "• Not considering trade-offs";
    }
    
    public function runAllExamples(): void
    {
        echo "System Design Exercise Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateDesignOverview();
        $this->demonstrateDesignSubmission();
        $this->demonstrateDesignTemplates();
        $this->demonstrateMultipleDesigns();
        $this->demonstrateDesignProcess();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runSystemDesignExerciseDemo(): void
{
    $examples = new SystemDesignExamples();
    $examples->runAllExamples();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSystemDesignExerciseDemo();
}
?>
