<?php
/**
 * Framework Comparison and Selection Guide
 * 
 * This file provides comprehensive comparison of PHP frameworks,
 * selection criteria, and decision-making guidance.
 */

// Framework Selection Criteria
class FrameworkSelectionCriteria
{
    private array $criteria = [];
    private array $weights = [];
    
    public function __construct()
    {
        $this->initializeCriteria();
        $this->initializeWeights();
    }
    
    /**
     * Initialize selection criteria
     */
    private function initializeCriteria(): void
    {
        $this->criteria = [
            'performance' => [
                'description' => 'Framework performance and speed',
                'options' => ['Excellent', 'Good', 'Fair', 'Poor'],
                'weight' => 0.15
            ],
            'learning_curve' => [
                'description' => 'Ease of learning and adoption',
                'options' => ['Easy', 'Medium', 'Hard', 'Very Hard'],
                'weight' => 0.12
            ],
            'documentation' => [
                'description' => 'Quality and completeness of documentation',
                'options' => ['Excellent', 'Good', 'Fair', 'Poor'],
                'weight' => 0.10
            ],
            'community' => [
                'description' => 'Size and activity of community',
                'options' => ['Large', 'Medium', 'Small', 'Tiny'],
                'weight' => 0.08
            ],
            'ecosystem' => [
                'description' => 'Availability of packages and extensions',
                'options' => ['Rich', 'Good', 'Limited', 'Minimal'],
                'weight' => 0.10
            ],
            'features' => [
                'description' => 'Built-in features and functionality',
                'options' => ['Comprehensive', 'Good', 'Basic', 'Minimal'],
                'weight' => 0.12
            ],
            'scalability' => [
                'description' => 'Ability to handle growth and scale',
                'options' => ['Excellent', 'Good', 'Fair', 'Poor'],
                'weight' => 0.10
            ],
            'security' => [
                'description' => 'Built-in security features',
                'options' => ['Strong', 'Good', 'Basic', 'Minimal'],
                'weight' => 0.08
            ],
            'flexibility' => [
                'description' => 'Customization and extensibility',
                'options' => ['High', 'Good', 'Limited', 'Rigid'],
                'weight' => 0.08
            ],
            'maintenance' => [
                'description' => 'Long-term maintenance and support',
                'options' => ['Active', 'Stable', 'Slow', 'Inactive'],
                'weight' => 0.07
            ]
        ];
    }
    
    /**
     * Initialize default weights
     */
    private function initializeWeights(): void
    {
        foreach ($this->criteria as $name => $criterion) {
            $this->weights[$name] = $criterion['weight'];
        }
    }
    
    /**
     * Set custom weights
     */
    public function setWeights(array $weights): void
    {
        $this->weights = array_merge($this->weights, $weights);
    }
    
    /**
     * Get criteria
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }
    
    /**
     * Get weights
     */
    public function getWeights(): array
    {
        return $this->weights;
    }
    
    /**
     * Score a framework
     */
    public function scoreFramework(array $frameworkData): float
    {
        $totalScore = 0;
        
        foreach ($this->criteria as $criterion => $data) {
            $value = $frameworkData[$criterion] ?? 'Fair';
            $score = $this->getCriterionScore($value, $data['options']);
            $totalScore += $score * $this->weights[$criterion];
        }
        
        return $totalScore;
    }
    
    /**
     * Get criterion score
     */
    private function getCriterionScore(string $value, array $options): float
    {
        $index = array_search($value, $options);
        
        if ($index === false) {
            return 0.5; // Default score
        }
        
        // Map to 0-1 scale (reversed for learning curve)
        if ($this->isReverseScoring($value)) {
            return 1.0 - ($index / (count($options) - 1));
        }
        
        return $index / (count($options) - 1);
    }
    
    /**
     * Check if criterion should be reverse scored
     */
    private function isReverseScoring(string $criterion): bool
    {
        return in_array($criterion, ['learning_curve']);
    }
}

// Project Type Analyzer
class ProjectTypeAnalyzer
{
    private array $projectTypes = [];
    
    public function __construct()
    {
        $this->initializeProjectTypes();
    }
    
    /**
     * Initialize project types
     */
    private function initializeProjectTypes(): void
    {
        $this->projectTypes = [
            'small_website' => [
                'name' => 'Small Website/Blog',
                'description' => 'Personal blog, small business website, portfolio',
                'complexity' => 'Low',
                'team_size' => '1-2',
                'timeline' => '1-4 weeks',
                'budget' => 'Low',
                'requirements' => [
                    'performance' => 'Good',
                    'learning_curve' => 'Easy',
                    'documentation' => 'Good',
                    'community' => 'Medium',
                    'ecosystem' => 'Good',
                    'features' => 'Basic',
                    'scalability' => 'Fair',
                    'security' => 'Basic',
                    'flexibility' => 'Good',
                    'maintenance' => 'Stable'
                ],
                'recommended_frameworks' => ['codeigniter', 'slim'],
                'avoid_frameworks' => ['symfony', 'phalcon']
            ],
            'medium_webapp' => [
                'name' => 'Medium Web Application',
                'description' => 'SaaS application, e-commerce site, corporate portal',
                'complexity' => 'Medium',
                'team_size' => '3-8',
                'timeline' => '1-3 months',
                'budget' => 'Medium',
                'requirements' => [
                    'performance' => 'Good',
                    'learning_curve' => 'Medium',
                    'documentation' => 'Good',
                    'community' => 'Large',
                    'ecosystem' => 'Rich',
                    'features' => 'Good',
                    'scalability' => 'Good',
                    'security' => 'Good',
                    'flexibility' => 'Good',
                    'maintenance' => 'Active'
                ],
                'recommended_frameworks' => ['laravel', 'yii'],
                'avoid_frameworks' => ['phalcon']
            ],
            'large_enterprise' => [
                'name' => 'Large Enterprise Application',
                'description' => 'Enterprise ERP, large-scale platform, mission-critical system',
                'complexity' => 'High',
                'team_size' => '10+',
                'timeline' => '6+ months',
                'budget' => 'High',
                'requirements' => [
                    'performance' => 'Excellent',
                    'learning_curve' => 'Hard',
                    'documentation' => 'Excellent',
                    'community' => 'Large',
                    'ecosystem' => 'Rich',
                    'features' => 'Comprehensive',
                    'scalability' => 'Excellent',
                    'security' => 'Strong',
                    'flexibility' => 'High',
                    'maintenance' => 'Active'
                ],
                'recommended_frameworks' => ['symfony', 'laravel'],
                'avoid_frameworks' => ['codeigniter', 'slim']
            ],
            'api_microservice' => [
                'name' => 'API/Microservice',
                'description' => 'REST API, GraphQL service, microservice architecture',
                'complexity' => 'Medium',
                'team_size' => '2-5',
                'timeline' => '2-8 weeks',
                'budget' => 'Medium',
                'requirements' => [
                    'performance' => 'Excellent',
                    'learning_curve' => 'Easy',
                    'documentation' => 'Good',
                    'community' => 'Medium',
                    'ecosystem' => 'Good',
                    'features' => 'Basic',
                    'scalability' => 'Excellent',
                    'security' => 'Good',
                    'flexibility' => 'High',
                    'maintenance' => 'Active'
                ],
                'recommended_frameworks' => ['slim', 'laravel'],
                'avoid_frameworks' => ['yii', 'codeigniter']
            ],
            'high_performance' => [
                'name' => 'High Performance Application',
                'description' => 'Real-time app, gaming platform, high-traffic site',
                'complexity' => 'High',
                'team_size' => '5+',
                'timeline' => '3-6 months',
                'budget' => 'High',
                'requirements' => [
                    'performance' => 'Excellent',
                    'learning_curve' => 'Hard',
                    'documentation' => 'Good',
                    'community' => 'Medium',
                    'ecosystem' => 'Good',
                    'features' => 'Good',
                    'scalability' => 'Excellent',
                    'security' => 'Good',
                    'flexibility' => 'High',
                    'maintenance' => 'Active'
                ],
                'recommended_frameworks' => ['phalcon', 'slim'],
                'avoid_frameworks' => ['yii', 'codeigniter']
            ],
            'rapid_prototype' => [
                'name' => 'Rapid Prototype/MVP',
                'description' => 'Startup MVP, proof of concept, quick demo',
                'complexity' => 'Low',
                'team_size' => '1-3',
                'timeline' => '1-2 weeks',
                'budget' => 'Low',
                'requirements' => [
                    'performance' => 'Fair',
                    'learning_curve' => 'Easy',
                    'documentation' => 'Good',
                    'community' => 'Large',
                    'ecosystem' => 'Rich',
                    'features' => 'Good',
                    'scalability' => 'Fair',
                    'security' => 'Basic',
                    'flexibility' => 'Good',
                    'maintenance' => 'Active'
                ],
                'recommended_frameworks' => ['laravel', 'codeigniter'],
                'avoid_frameworks' => ['symfony', 'phalcon']
            ]
        ];
    }
    
    /**
     * Get project type
     */
    public function getProjectType(string $type): ?array
    {
        return $this->projectTypes[$type] ?? null;
    }
    
    /**
     * Get all project types
     */
    public function getAllProjectTypes(): array
    {
        return $this->projectTypes;
    }
    
    /**
     * Analyze project requirements
     */
    public function analyzeProject(array $requirements): array
    {
        $matches = [];
        
        foreach ($this->projectTypes as $type => $data) {
            $score = $this->calculateMatchScore($requirements, $data);
            $matches[$type] = [
                'score' => $score,
                'data' => $data
            ];
        }
        
        // Sort by score
        uasort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $matches;
    }
    
    /**
     * Calculate match score
     */
    private function calculateMatchScore(array $requirements, array $projectType): float
    {
        $score = 0;
        $factors = 0;
        
        // Check complexity match
        if (isset($requirements['complexity'])) {
            $complexityMap = ['Low' => 0, 'Medium' => 1, 'High' => 2];
            $reqComplexity = $complexityMap[$requirements['complexity']] ?? 1;
            $typeComplexity = $complexityMap[$projectType['complexity']] ?? 1;
            
            $score += 1 - abs($reqComplexity - $typeComplexity) / 2;
            $factors++;
        }
        
        // Check team size match
        if (isset($requirements['team_size'])) {
            $teamSizeMap = [
                '1-2' => 0, '3-8' => 1, '10+' => 2,
                '1-3' => 0, '2-5' => 1, '5+' => 2
            ];
            $reqTeamSize = $teamSizeMap[$requirements['team_size']] ?? 1;
            $typeTeamSize = $teamSizeMap[$projectType['team_size']] ?? 1;
            
            $score += 1 - abs($reqTeamSize - $typeTeamSize) / 2;
            $factors++;
        }
        
        // Check timeline match
        if (isset($requirements['timeline'])) {
            $timelineMap = [
                '1-2 weeks' => 0, '1-4 weeks' => 0, '2-8 weeks' => 1,
                '1-3 months' => 1, '3-6 months' => 2, '6+ months' => 2
            ];
            $reqTimeline = $timelineMap[$requirements['timeline']] ?? 1;
            $typeTimeline = $timelineMap[$projectType['timeline']] ?? 1;
            
            $score += 1 - abs($reqTimeline - $typeTimeline) / 2;
            $factors++;
        }
        
        // Check budget match
        if (isset($requirements['budget'])) {
            $budgetMap = ['Low' => 0, 'Medium' => 1, 'High' => 2];
            $reqBudget = $budgetMap[$requirements['budget']] ?? 1;
            $typeBudget = $budgetMap[$projectType['budget']] ?? 1;
            
            $score += 1 - abs($reqBudget - $typeBudget) / 2;
            $factors++;
        }
        
        return $factors > 0 ? $score / $factors : 0;
    }
}

// Framework Recommendation Engine
class FrameworkRecommendationEngine
{
    private FrameworkSelectionCriteria $criteria;
    private ProjectTypeAnalyzer $projectAnalyzer;
    private array $frameworks = [];
    
    public function __construct()
    {
        $this->criteria = new FrameworkSelectionCriteria();
        $this->projectAnalyzer = new ProjectTypeAnalyzer();
        $this->initializeFrameworks();
    }
    
    /**
     * Initialize framework data
     */
    private function initializeFrameworks(): void
    {
        $this->frameworks = [
            'laravel' => [
                'name' => 'Laravel',
                'version' => '9.x',
                'type' => 'Full-Stack',
                'license' => 'MIT',
                'performance' => 'Good',
                'learning_curve' => 'Medium',
                'documentation' => 'Excellent',
                'community' => 'Large',
                'ecosystem' => 'Rich',
                'features' => 'Comprehensive',
                'scalability' => 'Good',
                'security' => 'Strong',
                'flexibility' => 'Good',
                'maintenance' => 'Active',
                'use_cases' => [
                    'medium_webapp',
                    'large_enterprise',
                    'rapid_prototype',
                    'api_microservice'
                ],
                'strengths' => [
                    'Elegant syntax',
                    'Rich ecosystem',
                    'Excellent documentation',
                    'Active community',
                    'Powerful CLI tools'
                ],
                'weaknesses' => [
                    'Performance overhead',
                    'Memory usage',
                    'Steep learning curve'
                ]
            ],
            'symfony' => [
                'name' => 'Symfony',
                'version' => '6.x',
                'type' => 'Full-Stack',
                'license' => 'MIT',
                'performance' => 'Excellent',
                'learning_curve' => 'Hard',
                'documentation' => 'Excellent',
                'community' => 'Large',
                'ecosystem' => 'Rich',
                'features' => 'Comprehensive',
                'scalability' => 'Excellent',
                'security' => 'Strong',
                'flexibility' => 'High',
                'maintenance' => 'Active',
                'use_cases' => [
                    'large_enterprise',
                    'medium_webapp',
                    'api_microservice'
                ],
                'strengths' => [
                    'High performance',
                    'Flexible architecture',
                    'Enterprise-ready',
                    'Excellent documentation',
                    'Strong typing'
                ],
                'weaknesses' => [
                    'Complex configuration',
                    'Steep learning curve',
                    'Verbose code'
                ]
            ],
            'codeigniter' => [
                'name' => 'CodeIgniter',
                'version' => '4.x',
                'type' => 'Lightweight',
                'license' => 'MIT',
                'performance' => 'Excellent',
                'learning_curve' => 'Easy',
                'documentation' => 'Good',
                'community' => 'Medium',
                'ecosystem' => 'Good',
                'features' => 'Basic',
                'scalability' => 'Fair',
                'security' => 'Basic',
                'flexibility' => 'Good',
                'maintenance' => 'Stable',
                'use_cases' => [
                    'small_website',
                    'medium_webapp',
                    'rapid_prototype'
                ],
                'strengths' => [
                    'Fast performance',
                    'Easy to learn',
                    'Small footprint',
                    'Good documentation',
                    'No dependencies'
                ],
                'weaknesses' => [
                    'Less modern features',
                    'Limited ecosystem',
                    'No built-in ORM',
                    'Less type safety'
                ]
            ],
            'slim' => [
                'name' => 'Slim',
                'version' => '4.x',
                'type' => 'Microframework',
                'license' => 'MIT',
                'performance' => 'Excellent',
                'learning_curve' => 'Easy',
                'documentation' => 'Good',
                'community' => 'Medium',
                'ecosystem' => 'Good',
                'features' => 'Basic',
                'scalability' => 'Excellent',
                'security' => 'Basic',
                'flexibility' => 'High',
                'maintenance' => 'Active',
                'use_cases' => [
                    'api_microservice',
                    'high_performance',
                    'small_website'
                ],
                'strengths' => [
                    'Minimal footprint',
                    'Fast performance',
                    'PSR compliant',
                    'Easy to extend',
                    'Flexible'
                ],
                'weaknesses' => [
                    'Limited features',
                    'No built-in ORM',
                    'Manual setup',
                    'Smaller community'
                ]
            ],
            'phalcon' => [
                'name' => 'Phalcon',
                'version' => '5.x',
                'type' => 'Full-Stack',
                'license' => 'BSD-3',
                'performance' => 'Excellent',
                'learning_curve' => 'Hard',
                'documentation' => 'Good',
                'community' => 'Small',
                'ecosystem' => 'Good',
                'features' => 'Good',
                'scalability' => 'Excellent',
                'security' => 'Good',
                'flexibility' => 'High',
                'maintenance' => 'Active',
                'use_cases' => [
                    'high_performance',
                    'api_microservice',
                    'large_enterprise'
                ],
                'strengths' => [
                    'Excellent performance',
                    'Low memory usage',
                    'C extension',
                    'Rich features',
                    'Type hints'
                ],
                'weaknesses' => [
                    'Requires C knowledge',
                    'Complex setup',
                    'Smaller community',
                    'Extension dependencies'
                ]
            ],
            'yii' => [
                'name' => 'Yii',
                'version' => '2.x',
                'type' => 'Full-Stack',
                'license' => 'BSD-3',
                'performance' => 'Good',
                'learning_curve' => 'Medium',
                'documentation' => 'Good',
                'community' => 'Large',
                'ecosystem' => 'Rich',
                'features' => 'Good',
                'scalability' => 'Good',
                'security' => 'Good',
                'flexibility' => 'Good',
                'maintenance' => 'Active',
                'use_cases' => [
                    'medium_webapp',
                    'large_enterprise',
                    'small_website'
                ],
                'strengths' => [
                    'Easy to start',
                    'Good performance',
                    'Rich features',
                    'Good documentation',
                    'Large community'
                ],
                'weaknesses' => [
                    'Convention heavy',
                    'Magic methods',
                    'Less type safety',
                    'Older architecture'
                ]
            ]
        ];
    }
    
    /**
     * Get framework recommendations
     */
    public function getRecommendations(array $projectRequirements): array
    {
        // Analyze project type
        $projectMatches = $this->projectAnalyzer->analyzeProject($projectRequirements);
        $bestProjectType = array_key_first($projectMatches);
        
        // Get project type data
        $projectType = $this->projectAnalyzer->getProjectType($bestProjectType);
        
        // Score frameworks based on project requirements
        $scores = [];
        
        foreach ($this->frameworks as $name => $framework) {
            $score = $this->calculateFrameworkScore($framework, $projectRequirements, $projectType);
            $scores[$name] = [
                'score' => $score,
                'framework' => $framework,
                'match_reason' => $this->generateMatchReason($framework, $projectType)
            ];
        }
        
        // Sort by score
        uasort($scores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($scores, 0, 5, true);
    }
    
    /**
     * Calculate framework score
     */
    private function calculateFrameworkScore(array $framework, array $requirements, array $projectType): float
    {
        $score = 0;
        
        // Base score from criteria
        $criteriaScore = $this->criteria->scoreFramework($framework);
        $score += $criteriaScore * 0.6;
        
        // Project type match
        $projectMatch = in_array($projectType['name'], $framework['use_cases']) ? 1 : 0;
        $score += $projectMatch * 0.3;
        
        // Avoid list penalty
        $avoidPenalty = in_array($framework['name'], $projectType['avoid_frameworks']) ? -0.5 : 0;
        $score += $avoidPenalty;
        
        // Recommendation bonus
        $recommendBonus = in_array($framework['name'], $projectType['recommended_frameworks']) ? 0.2 : 0;
        $score += $recommendBonus;
        
        return max(0, min(1, $score));
    }
    
    /**
     * Generate match reason
     */
    private function generateMatchReason(array $framework, array $projectType): string
    {
        $reasons = [];
        
        // Check if it's recommended
        if (in_array($framework['name'], $projectType['recommended_frameworks'])) {
            $reasons[] = 'Recommended for this project type';
        }
        
        // Check if it should be avoided
        if (in_array($framework['name'], $projectType['avoid_frameworks'])) {
            $reasons[] = 'Not recommended for this project type';
        }
        
        // Check performance match
        if ($projectType['requirements']['performance'] === 'Excellent' && $framework['performance'] === 'Excellent') {
            $reasons[] = 'Excellent performance match';
        }
        
        // Check learning curve match
        if ($projectType['complexity'] === 'Low' && $framework['learning_curve'] === 'Easy') {
            $reasons[] = 'Easy learning curve for simple projects';
        }
        
        // Check complexity match
        if ($projectType['complexity'] === 'High' && $framework['type'] === 'Full-Stack') {
            $reasons[] = 'Suitable for complex projects';
        }
        
        return implode(', ', $reasons) ?: 'General purpose framework';
    }
    
    /**
     * Get detailed comparison
     */
    public function getDetailedComparison(array $frameworks): array
    {
        $comparison = [];
        
        foreach ($frameworks as $name) {
            if (isset($this->frameworks[$name])) {
                $comparison[$name] = $this->frameworks[$name];
            }
        }
        
        return $comparison;
    }
    
    /**
     * Generate comparison matrix
     */
    public function generateComparisonMatrix(array $frameworks): array
    {
        $matrix = [];
        $criteria = $this->criteria->getCriteria();
        
        foreach ($frameworks as $frameworkName) {
            if (!isset($this->frameworks[$frameworkName])) {
                continue;
            }
            
            $framework = $this->frameworks[$frameworkName];
            $matrix[$frameworkName] = [];
            
            foreach ($criteria as $criterion => $data) {
                $matrix[$frameworkName][$criterion] = $framework[$criterion] ?? 'Fair';
            }
        }
        
        return $matrix;
    }
    
    /**
     * Get migration guide
     */
    public function getMigrationGuide(string $from, string $to): array
    {
        $fromFramework = $this->frameworks[$from] ?? null;
        $toFramework = $this->frameworks[$to] ?? null;
        
        if (!$fromFramework || !$toFramework) {
            return ['error' => 'Framework not found'];
        }
        
        $guide = [
            'from' => $fromFramework['name'],
            'to' => $toFramework['name'],
            'difficulty' => $this->calculateMigrationDifficulty($fromFramework, $toFramework),
            'steps' => $this->generateMigrationSteps($fromFramework, $toFramework),
            'considerations' => $this->generateMigrationConsiderations($fromFramework, $toFramework)
        ];
        
        return $guide;
    }
    
    /**
     * Calculate migration difficulty
     */
    private function calculateMigrationDifficulty(array $from, array $to): string
    {
        $difficulty = 0;
        
        // Type difference
        if ($from['type'] !== $to['type']) {
            $difficulty += 2;
        }
        
        // Learning curve difference
        $curveMap = ['Easy' => 1, 'Medium' => 2, 'Hard' => 3];
        $fromCurve = $curveMap[$from['learning_curve']] ?? 2;
        $toCurve = $curveMap[$to['learning_curve']] ?? 2;
        $difficulty += abs($fromCurve - $toCurve);
        
        // Feature difference
        $featureMap = ['Minimal' => 1, 'Basic' => 2, 'Good' => 3, 'Comprehensive' => 4];
        $fromFeatures = $featureMap[$from['features']] ?? 2;
        $toFeatures = $featureMap[$to['features']] ?? 2;
        $difficulty += abs($fromFeatures - $toFeatures);
        
        if ($difficulty <= 2) {
            return 'Easy';
        } elseif ($difficulty <= 4) {
            return 'Medium';
        } else {
            return 'Hard';
        }
    }
    
    /**
     * Generate migration steps
     */
    private function generateMigrationSteps(array $from, array $to): array
    {
        $steps = [
            '1. Backup existing application',
            '2. Set up new framework environment',
            '3. Identify differences in architecture',
            '4. Plan data migration strategy',
            '5. Migrate configuration',
            '6. Rewrite controllers/routes',
            '7. Migrate models/entities',
            '8. Update views/templates',
            '9. Migrate business logic',
            '10. Update dependencies',
            '11. Test thoroughly',
            '12. Deploy and monitor'
        ];
        
        // Add framework-specific steps
        if ($to['name'] === 'Laravel') {
            $steps[] = '13. Set up Laravel specific features (Eloquent, Blade, etc.)';
        } elseif ($to['name'] === 'Symfony') {
            $steps[] = '13. Configure Symfony bundles and services';
        } elseif ($to['name'] === 'Phalcon') {
            $steps[] = '13. Install and configure Phalcon extension';
        }
        
        return $steps;
    }
    
    /**
     * Generate migration considerations
     */
    private function generateMigrationConsiderations(array $from, array $to): array
    {
        $considerations = [
            'Team training on new framework',
            'Time and budget allocation',
            'Potential downtime during migration',
            'Data consistency and integrity',
            'Performance differences',
            'Security implications',
            'Third-party integration compatibility'
        ];
        
        // Add specific considerations
        if ($from['type'] === 'Microframework' && $to['type'] === 'Full-Stack') {
            $considerations[] = 'Significant architectural changes required';
        }
        
        if ($to['name'] === 'Phalcon') {
            $considerations[] = 'Server requirements for C extension';
        }
        
        return $considerations;
    }
}

// Framework Comparison Examples
class FrameworkComparisonExamples
{
    private FrameworkRecommendationEngine $engine;
    private ProjectTypeAnalyzer $projectAnalyzer;
    
    public function __construct()
    {
        $this->engine = new FrameworkRecommendationEngine();
        $this->projectAnalyzer = new ProjectTypeAnalyzer();
    }
    
    public function demonstrateProjectAnalysis(): void
    {
        echo "Project Type Analysis Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test different project requirements
        $projects = [
            'small_blog' => [
                'complexity' => 'Low',
                'team_size' => '1-2',
                'timeline' => '1-4 weeks',
                'budget' => 'Low'
            ],
            'enterprise_app' => [
                'complexity' => 'High',
                'team_size' => '10+',
                'timeline' => '6+ months',
                'budget' => 'High'
            ],
            'api_service' => [
                'complexity' => 'Medium',
                'team_size' => '2-5',
                'timeline' => '2-8 weeks',
                'budget' => 'Medium'
            ],
            'startup_mvp' => [
                'complexity' => 'Low',
                'team_size' => '1-3',
                'timeline' => '1-2 weeks',
                'budget' => 'Low'
            ]
        ];
        
        foreach ($projects as $name => $requirements) {
            echo "\nProject: $name\n";
            echo str_repeat("-", strlen($name) + 9) . "\n";
            
            $matches = $this->projectAnalyzer->analyzeProject($requirements);
            
            foreach ($matches as $type => $match) {
                if ($match['score'] > 0.5) {
                    echo "Match: {$match['data']['name']} (Score: " . round($match['score'] * 100) . "%)\n";
                    echo "Description: {$match['data']['description']}\n";
                    echo "Recommended: " . implode(', ', $match['data']['recommended_frameworks']) . "\n";
                    echo "\n";
                    break;
                }
            }
        }
    }
    
    public function demonstrateRecommendations(): void
    {
        echo "\nFramework Recommendations Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test different scenarios
        $scenarios = [
            'small_business_website' => [
                'complexity' => 'Low',
                'team_size' => '1-2',
                'timeline' => '1-4 weeks',
                'budget' => 'Low',
                'requirements' => [
                    'Easy to learn',
                    'Fast development',
                    'Good documentation'
                ]
            ],
            'enterprise_saaS' => [
                'complexity' => 'High',
                'team_size' => '10+',
                'timeline' => '6+ months',
                'budget' => 'High',
                'requirements' => [
                    'High performance',
                    'Scalable',
                    'Strong security',
                    'Enterprise features'
                ]
            ],
            'rest_api' => [
                'complexity' => 'Medium',
                'team_size' => '2-5',
                'timeline' => '2-8 weeks',
                'budget' => 'Medium',
                'requirements' => [
                    'High performance',
                    'Lightweight',
                    'Easy to extend',
                    'JSON support'
                ]
            ],
            'startup_mvp' => [
                'complexity' => 'Low',
                'team_size' => '1-3',
                'timeline' => '1-2 weeks',
                'budget' => 'Low',
                'requirements' => [
                    'Rapid development',
                    'Rich ecosystem',
                    'Good documentation',
                    'Community support'
                ]
            ]
        ];
        
        foreach ($scenarios as $name => $requirements) {
            echo "\nScenario: $name\n";
            echo str_repeat("-", strlen($name) + 10) . "\n";
            
            $recommendations = $this->engine->getRecommendations($requirements);
            
            foreach ($recommendations as $framework => $data) {
                echo "\n{$data['framework']['name']} (Score: " . round($data['score'] * 100) . "%)\n";
                echo "Type: {$data['framework']['type']}\n";
                echo "Performance: {$data['framework']['performance']}\n";
                echo "Learning Curve: {$data['framework']['learning_curve']}\n";
                echo "Match Reason: {$data['match_reason']}\n";
                
                if (!empty($data['framework']['strengths'])) {
                    echo "Strengths:\n";
                    foreach ($data['framework']['strengths'] as $strength) {
                        echo "  • $strength\n";
                    }
                }
                
                echo "\n";
                break; // Show only top recommendation
            }
        }
    }
    
    public function demonstrateComparison(): void
    {
        echo "\nFramework Comparison Matrix\n";
        echo str_repeat("-", 30) . "\n";
        
        $frameworks = ['laravel', 'symfony', 'codeigniter', 'slim', 'phalcon', 'yii'];
        $matrix = $this->engine->generateComparisonMatrix($frameworks);
        
        // Print header
        echo str_pad('Framework', 15);
        echo str_pad('Performance', 12);
        echo str_pad('Learning', 10);
        echo str_pad('Docs', 8);
        echo str_pad('Community', 10);
        echo str_pad('Ecosystem', 10);
        echo str_pad('Features', 10);
        echo "\n";
        
        echo str_repeat('-', 15);
        echo str_repeat('-', 12);
        echo str_repeat('-', 10);
        echo str_repeat('-', 8);
        echo str_repeat('-', 10);
        echo str_repeat('-', 10);
        echo str_repeat('-', 10);
        echo "\n";
        
        // Print data
        foreach ($matrix as $framework => $criteria) {
            echo str_pad($framework, 15);
            echo str_pad($criteria['performance'], 12);
            echo str_pad($criteria['learning_curve'], 10);
            echo str_pad($criteria['documentation'], 8);
            echo str_pad($criteria['community'], 10);
            echo str_pad($criteria['ecosystem'], 10);
            echo str_pad($criteria['features'], 10);
            echo "\n";
        }
        
        // Detailed comparison
        echo "\nDetailed Comparison:\n";
        echo str_repeat("-", 20) . "\n";
        
        $comparison = $this->engine->getDetailedComparison(['laravel', 'symfony']);
        
        foreach ($comparison as $name => $framework) {
            echo "\n{$framework['name']} ({$framework['version']})\n";
            echo str_repeat("-", strlen($framework['name']) + strlen($framework['version']) + 3) . "\n";
            echo "Type: {$framework['type']}\n";
            echo "License: {$framework['license']}\n";
            echo "Performance: {$framework['performance']}\n";
            echo "Learning Curve: {$framework['learning_curve']}\n";
            echo "Documentation: {$framework['documentation']}\n";
            echo "Community: {$framework['community']}\n";
            echo "Ecosystem: {$framework['ecosystem']}\n";
            echo "Features: {$framework['features']}\n";
            echo "Scalability: {$framework['scalability']}\n";
            echo "Security: {$framework['security']}\n";
            echo "Flexibility: {$framework['flexibility']}\n";
            echo "Maintenance: {$framework['maintenance']}\n";
            
            echo "\nStrengths:\n";
            foreach ($framework['strengths'] as $strength) {
                echo "  • $strength\n";
            }
            
            echo "\nWeaknesses:\n";
            foreach ($framework['weaknesses'] as $weakness) {
                echo "  • $weakness\n";
            }
            
            echo "\n";
        }
    }
    
    public function demonstrateMigration(): void
    {
        echo "\nFramework Migration Guide\n";
        echo str_repeat("-", 30) . "\n";
        
        $migrations = [
            ['codeigniter', 'laravel'],
            ['laravel', 'symfony'],
            ['symfony', 'phalcon'],
            ['slim', 'laravel']
        ];
        
        foreach ($migrations as [$from, $to]) {
            $guide = $this->engine->getMigrationGuide($from, $to);
            
            echo "\nMigration: {$guide['from']} → {$guide['to']}\n";
            echo str_repeat("-", strlen($guide['from']) + strlen($guide['to']) + 3) . "\n";
            echo "Difficulty: {$guide['difficulty']}\n";
            
            echo "\nKey Steps:\n";
            foreach (array_slice($guide['steps'], 0, 5) as $step) {
                echo "  • $step\n";
            }
            echo "  ... and " . (count($guide['steps']) - 5) . " more steps\n";
            
            echo "\nConsiderations:\n";
            foreach (array_slice($guide['considerations'], 0, 3) as $consideration) {
                echo "  • $consideration\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateSelectionProcess(): void
    {
        echo "\nFramework Selection Process\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Step 1: Define Project Requirements\n";
        echo "  • Project type and complexity\n";
        echo "  • Team size and expertise\n";
        echo "  • Timeline and budget\n";
        echo "  • Performance requirements\n";
        echo "  • Security needs\n";
        echo "  • Scalability requirements\n\n";
        
        echo "Step 2: Analyze Project Type\n";
        echo "  • Match requirements to project type\n";
        echo "  • Identify suitable frameworks\n";
        echo "  • Consider framework recommendations\n\n";
        
        echo "Step 3: Evaluate Frameworks\n";
        echo "  • Score based on criteria\n";
        echo "  • Compare strengths and weaknesses\n";
        echo "  • Consider ecosystem and community\n";
        echo "  • Evaluate learning curve\n\n";
        
        echo "Step 4: Make Decision\n";
        echo "  • Choose top-ranked framework\n";
        echo "  • Consider team preferences\n";
        echo "  • Plan migration if needed\n";
        echo "  • Start with proof of concept\n\n";
        
        echo "Example Selection Process:\n";
        echo "Project: Medium-sized SaaS application\n";
        echo "Team: 5 developers, mixed experience\n";
        echo "Timeline: 3 months\n";
        echo "Budget: Medium\n";
        echo "Requirements: High performance, scalable, good documentation\n\n";
        
        $requirements = [
            'complexity' => 'Medium',
            'team_size' => '3-8',
            'timeline' => '1-3 months',
            'budget' => 'Medium'
        ];
        
        $recommendations = $this->engine->getRecommendations($requirements);
        
        echo "Top Recommendations:\n";
        foreach ($recommendations as $framework => $data) {
            echo "  {$data['framework']['name']}: " . round($data['score'] * 100) . "%\n";
            echo "    {$data['match_reason']}\n";
        }
    }
    
    public function runAllExamples(): void
    {
        echo "Framework Comparison and Selection Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateProjectAnalysis();
        $this->demonstrateRecommendations();
        $this->demonstrateComparison();
        $this->demonstrateMigration();
        $this->demonstrateSelectionProcess();
    }
}

// Framework Selection Best Practices
function printFrameworkSelectionBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Framework Selection Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Requirements Analysis:\n";
    echo "   • Define clear project requirements\n";
    echo "   • Assess team skills and experience\n";
    echo "   • Consider timeline and budget constraints\n";
    echo "   • Identify performance and scalability needs\n";
    echo "   • Evaluate security requirements\n\n";
    
    echo "2. Framework Evaluation:\n";
    echo "   • Compare multiple frameworks\n";
    echo "   • Use consistent evaluation criteria\n";
    echo "   • Consider long-term maintenance\n";
    echo "   • Evaluate ecosystem and community\n";
    echo "   • Test with proof of concept\n\n";
    
    echo "3. Decision Making:\n";
    echo "   • Use data-driven approach\n";
    echo "   • Consider team preferences\n";
    echo "   • Plan for migration if needed\n";
    echo "   • Document decision rationale\n";
    echo "   • Get stakeholder buy-in\n\n";
    
    echo "4. Implementation:\n";
    echo "   • Start with small project\n";
    echo "   • Invest in team training\n";
    echo "   • Follow framework conventions\n";
    echo "   • Implement proper testing\n";
    echo "   • Monitor performance\n\n";
    
    echo "5. Maintenance:\n";
    echo "   • Keep framework updated\n";
    echo "   • Monitor security updates\n";
    echo "   • Track community changes\n";
    echo "   • Plan for future migrations\n";
    echo "   • Document customizations\n\n";
    
    echo "6. Common Mistakes to Avoid:\n";
    echo "   • Choosing based on popularity only\n";
    echo "   • Ignoring team skills\n";
    echo "   • Over-engineering simple projects\n";
    echo "   • Not considering long-term costs\n";
    echo "   • Skipping proof of concept";
}

// Main execution
function runFrameworkComparisonDemo(): void
{
    $examples = new FrameworkComparisonExamples();
    $examples->runAllExamples();
    printFrameworkSelectionBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runFrameworkComparisonDemo();
}
?>
