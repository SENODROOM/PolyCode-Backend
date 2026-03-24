<?php
/**
 * PHP Job Market Guide
 * 
 * This file provides comprehensive information about the PHP job market,
 * career paths, salary expectations, and industry trends.
 */

// PHP Job Market Analyzer
class PHPJobMarketAnalyzer
{
    private array $jobMarketData;
    private array $salaryData;
    private array $skillRequirements;
    private array $industryTrends;
    
    public function __construct()
    {
        $this->initializeJobMarketData();
        $this->initializeSalaryData();
        $this->initializeSkillRequirements();
        $this->initializeIndustryTrends();
    }
    
    /**
     * Initialize job market data
     */
    private function initializeJobMarketData(): void
    {
        $this->jobMarketData = [
            'current_demand' => [
                'junior_developer' => [
                    'open_positions' => 15000,
                    'growth_rate' => 0.12,
                    'competition_level' => 'High',
                    'time_to_hire' => '4-6 weeks'
                ],
                'mid_level_developer' => [
                    'open_positions' => 12000,
                    'growth_rate' => 0.15,
                    'competition_level' => 'Medium',
                    'time_to_hire' => '6-8 weeks'
                ],
                'senior_developer' => [
                    'open_positions' => 8000,
                    'growth_rate' => 0.18,
                    'competition_level' => 'Low',
                    'time_to_hire' => '8-12 weeks'
                ],
                'lead_developer' => [
                    'open_positions' => 3000,
                    'growth_rate' => 0.20,
                    'competition_level' => 'Low',
                    'time_to_hire' => '10-14 weeks'
                ],
                'architect' => [
                    'open_positions' => 1500,
                    'growth_rate' => 0.22,
                    'competition_level' => 'Very Low',
                    'time_to_hire' => '12-16 weeks'
                ]
            ],
            'geographic_distribution' => [
                'united_states' => [
                    'total_positions' => 25000,
                    'top_cities' => [
                        'San Francisco' => 4500,
                        'New York' => 3800,
                        'Seattle' => 3200,
                        'Austin' => 2800,
                        'Boston' => 2200
                    ],
                    'average_salary_range' => [
                        'junior' => [65000, 85000],
                        'mid_level' => [85000, 120000],
                        'senior' => [120000, 160000],
                        'lead' => [140000, 190000],
                        'architect' => [170000, 250000]
                    ]
                ],
                'europe' => [
                    'total_positions' => 18000,
                    'top_cities' => [
                        'London' => 3200,
                        'Berlin' => 2800,
                        'Amsterdam' => 2200,
                        'Paris' => 2000,
                        'Barcelona' => 1800
                    ],
                    'average_salary_range' => [
                        'junior' => [40000, 55000],
                        'mid_level' => [55000, 75000],
                        'senior' => [75000, 95000],
                        'lead' => [85000, 110000],
                        'architect' => [100000, 140000]
                    ]
                ],
                'asia' => [
                    'total_positions' => 22000,
                    'top_cities' => [
                        'Singapore' => 3500,
                        'Bangalore' => 3000,
                        'Tokyo' => 2800,
                        'Hong Kong' => 2500,
                        'Sydney' => 2000
                    ],
                    'average_salary_range' => [
                        'junior' => [30000, 45000],
                        'mid_level' => [45000, 65000],
                        'senior' => [65000, 85000],
                        'lead' => [75000, 95000],
                        'architect' => [90000, 120000]
                    ]
                ]
            ],
            'industry_sectors' => [
                'technology' => [
                    'percentage' => 35,
                    'companies' => ['Startups', 'SaaS', 'E-commerce', 'Social Media'],
                    'growth_trend' => 'Strong'
                ],
                'finance' => [
                    'percentage' => 20,
                    'companies' => ['Banks', 'FinTech', 'Insurance', 'Investment'],
                    'growth_trend' => 'Moderate'
                ],
                'healthcare' => [
                    'percentage' => 15,
                    'companies' => ['Hospitals', 'Medical Tech', 'Pharmaceuticals'],
                    'growth_trend' => 'Strong'
                ],
                'retail' => [
                    'percentage' => 12,
                    'companies' => ['E-commerce', 'Fashion', 'Grocery', 'Marketplace'],
                    'growth_trend' => 'Moderate'
                ],
                'education' => [
                    'percentage' => 10,
                    'companies' => ['Online Learning', 'EdTech', 'Universities'],
                    'growth_trend' => 'Strong'
                ],
                'government' => [
                    'percentage' => 8,
                    'companies' => ['Federal', 'State', 'Local', 'Military'],
                    'growth_trend' => 'Stable'
                ]
            ]
        ];
    }
    
    /**
     * Initialize salary data
     */
    private function initializeSalaryData(): void
    {
        $this->salaryData = [
            'base_salaries' => [
                'united_states' => [
                    'junior_developer' => [
                        'min' => 65000,
                        'median' => 75000,
                        'max' => 85000,
                        'factors' => ['Location', 'Company Size', 'Industry', 'Experience']
                    ],
                    'mid_level_developer' => [
                        'min' => 85000,
                        'median' => 105000,
                        'max' => 120000,
                        'factors' => ['Specialization', 'Team Size', 'Performance', 'Skills']
                    ],
                    'senior_developer' => [
                        'min' => 120000,
                        'median' => 140000,
                        'max' => 160000,
                        'factors' => ['Leadership', 'Architecture', 'Mentoring', 'Impact']
                    ],
                    'lead_developer' => [
                        'min' => 140000,
                        'median' => 165000,
                        'max' => 190000,
                        'factors' => ['Team Management', 'Technical Direction', 'Budget Control']
                    ],
                    'architect' => [
                        'min' => 170000,
                        'median' => 210000,
                        'max' => 250000,
                        'factors' => ['System Architecture', 'Strategic Impact', 'Cross-functional']
                    ]
                ]
            ],
            'compensation_components' => [
                'base_salary' => 85,
                'bonus' => 10,
                'stock_options' => 3,
                'health_insurance' => 1,
                'retirement' => 1,
                'other_benefits' => 0
            ],
            'salary_increases' => [
                'annual_merit' => 3.5,
                'promotion' => 15,
                'company_change' => 12,
                'skill_upgrade' => 8
            ],
            'high_demand_skills' => [
                'laravel' => 12,
                'symfony' => 10,
                'microservices' => 15,
                'cloud_aws' => 18,
                'docker' => 14,
                'kubernetes' => 20,
                'devops' => 16,
                'machine_learning' => 25,
                'blockchain' => 20,
                'cybersecurity' => 22
            ]
        ];
    }
    
    /**
     * Initialize skill requirements
     */
    private function initializeSkillRequirements(): void
    {
        $this->skillRequirements = [
            'core_technical_skills' => [
                'php_fundamentals' => [
                    'proficiency' => 'Expert',
                    'importance' => 'Critical',
                    'assessment' => 'Object-oriented programming, design patterns, error handling'
                ],
                'frameworks' => [
                    'proficiency' => 'Advanced',
                    'importance' => 'Critical',
                    'assessment' => 'Laravel, Symfony, CodeIgniter, Slim'
                ],
                'databases' => [
                    'proficiency' => 'Advanced',
                    'importance' => 'Critical',
                    'assessment' => 'MySQL, PostgreSQL, MongoDB, Redis'
                ],
                'apis' => [
                    'proficiency' => 'Advanced',
                    'importance' => 'Critical',
                    'assessment' => 'RESTful APIs, GraphQL, SOAP, API design'
                ],
                'version_control' => [
                    'proficiency' => 'Expert',
                    'importance' => 'Critical',
                    'assessment' => 'Git, GitHub, GitLab, branching strategies'
                ]
            ],
            'advanced_technical_skills' => [
                'cloud_services' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'High',
                    'assessment' => 'AWS, Azure, GCP, serverless architecture'
                ],
                'containerization' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'High',
                    'assessment' => 'Docker, Kubernetes, container orchestration'
                ],
                'cicd' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'High',
                    'assessment' => 'Jenkins, GitHub Actions, CI/CD pipelines'
                ],
                'testing' => [
                    'proficiency' => 'Advanced',
                    'importance' => 'High',
                    'assessment' => 'PHPUnit, TDD, integration testing, test automation'
                ],
                'security' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'High',
                    'assessment' => 'OWASP, authentication, encryption, security best practices'
                ]
            ],
            'soft_skills' => [
                'communication' => [
                    'proficiency' => 'Expert',
                    'importance' => 'Critical',
                    'assessment' => 'Technical writing, presentations, stakeholder communication'
                ],
                'teamwork' => [
                    'proficiency' => 'Expert',
                    'importance' => 'Critical',
                    'assessment' => 'Collaboration, code reviews, mentoring'
                ],
                'problem_solving' => [
                    'proficiency' => 'Expert',
                    'importance' => 'Critical',
                    'assessment' => 'Analytical thinking, debugging, optimization'
                ],
                'project_management' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'High',
                    'assessment' => 'Agile, Scrum, project planning, time management'
                ],
                'leadership' => [
                    'proficiency' => 'Intermediate',
                    'importance' => 'Medium',
                    'assessment' => 'Team leading, decision making, strategic thinking'
                ]
            ]
        ];
    }
    
    /**
     * Initialize industry trends
     */
    private function initializeIndustryTrends(): void
    {
        $this->industryTrends = [
            'emerging_technologies' => [
                'serverless_php' => [
                    'growth_rate' => 0.35,
                    'adoption_level' => 'Early',
                    'skill_demand' => 'High',
                    'timeline' => '2-3 years'
                ],
                'php_8_features' => [
                    'growth_rate' => 0.45,
                    'adoption_level' => 'Growing',
                    'skill_demand' => 'High',
                    'timeline' => '1-2 years'
                ],
                'microservices' => [
                    'growth_rate' => 0.28,
                    'adoption_level' => 'Mature',
                    'skill_demand' => 'Very High',
                    'timeline' => 'Current'
                ],
                'headless_cms' => [
                    'growth_rate' => 0.40,
                    'adoption_level' => 'Growing',
                    'skill_demand' => 'High',
                    'timeline' => '1-2 years'
                ],
                'api_first' => [
                    'growth_rate' => 0.32,
                    'adoption_level' => 'Mature',
                    'skill_demand' => 'Very High',
                    'timeline' => 'Current'
                ]
            ],
            'market_trends' => [
                'remote_work' => [
                    'growth_rate' => 0.65,
                    'impact' => 'High',
                    'opportunities' => 'Global job market, flexible schedules'
                ],
                'freelance_gig' => [
                    'growth_rate' => 0.42,
                    'impact' => 'Medium',
                    'opportunities' => 'Project-based work, higher rates'
                ],
                'specialization' => [
                    'growth_rate' => 0.28,
                    'impact' => 'Medium',
                    'opportunities' => 'Niche expertise, premium rates'
                ],
                'full_stack' => [
                    'growth_rate' => 0.35,
                    'impact' => 'High',
                    'opportunities' => 'Versatile roles, broader opportunities'
                ],
                'consulting' => [
                    'growth_rate' => 0.25,
                    'impact' => 'Low',
                    'opportunities' => 'Expert advice, high-value projects'
                ]
            ],
            'future_outlook' => [
                'job_growth' => [
                    '2024' => 0.08,
                    '2025' => 0.10,
                    '2026' => 0.12,
                    '2027' => 0.11,
                    '2028' => 0.10
                ],
                'salary_growth' => [
                    '2024' => 0.04,
                    '2025' => 0.05,
                    '2026' => 0.05,
                    '2027' => 0.04,
                    '2028' => 0.04
                ],
                'skill_evolution' => [
                    'declining' => ['Legacy PHP', 'Manual deployments', 'Basic HTML'],
                    'stable' => ['Core PHP', 'MySQL', 'JavaScript'],
                    'growing' => ['PHP 8+', 'Cloud services', 'DevOps'],
                    'emerging' => ['AI Integration', 'Serverless', 'Edge computing']
                ]
            ]
        ];
    }
    
    /**
     * Get job market overview
     */
    public function getJobMarketOverview(): array
    {
        return [
            'total_open_positions' => array_sum(array_column($this->jobMarketData['current_demand'], 'open_positions')),
            'average_growth_rate' => array_sum(array_column($this->jobMarketData['current_demand'], 'growth_rate')) / count($this->jobMarketData['current_demand']),
            'top_growth_roles' => $this->getTopGrowthRoles(),
            'geographic_opportunities' => $this->getGeographicOpportunities(),
            'industry_distribution' => $this->jobMarketData['industry_sectors']
        ];
    }
    
    /**
     * Get top growth roles
     */
    private function getTopGrowthRoles(): array
    {
        $roles = $this->jobMarketData['current_demand'];
        
        uasort($roles, function($a, $b) {
            return $b['growth_rate'] <=> $a['growth_rate'];
        });
        
        return array_slice($roles, 0, 3, true);
    }
    
    /**
     * Get geographic opportunities
     */
    private function getGeographicOpportunities(): array
    {
        $opportunities = [];
        
        foreach ($this->jobMarketData['geographic_distribution'] as $region => $data) {
            $opportunities[$region] = [
                'total_positions' => $data['total_positions'],
                'top_cities' => array_slice($data['top_cities'], 0, 3, true),
                'average_salary' => $this->calculateAverageSalary($data['average_salary_range'])
            ];
        }
        
        return $opportunities;
    }
    
    /**
     * Calculate average salary
     */
    private function calculateAverageSalary(array $salaryRanges): array
    {
        $averages = [];
        
        foreach ($salaryRanges as $level => $range) {
            $averages[$level] = array_sum($range) / count($range);
        }
        
        return $averages;
    }
    
    /**
     * Get salary analysis
     */
    public function getSalaryAnalysis(string $level = 'mid_level_developer', string $region = 'united_states'): array
    {
        $salaryRange = $this->salaryData['base_salaries'][$region][$level] ?? null;
        
        if (!$salaryRange) {
            return ['error' => 'Invalid level or region'];
        }
        
        return [
            'base_salary' => $salaryRange,
            'total_compensation' => $this->calculateTotalCompensation($salaryRange),
            'market_factors' => $salaryRange['factors'],
            'growth_potential' => $this->calculateGrowthPotential($level),
            'negotiation_tips' => $this->getNegotiationTips($level, $region)
        ];
    }
    
    /**
     * Calculate total compensation
     */
    private function calculateTotalCompensation(array $baseSalary): array
    {
        $median = $baseSalary['median'];
        $components = $this->salaryData['compensation_components'];
        
        $total = [
            'base' => $median,
            'bonus' => $median * ($components['bonus'] / 100),
            'stock_options' => $median * ($components['stock_options'] / 100),
            'benefits' => $median * (($components['health_insurance'] + $components['retirement'] + $components['other_benefits']) / 100),
            'total' => 0
        ];
        
        $total['total'] = array_sum($total);
        $total['percentage_increase'] = (($total['total'] - $total['base']) / $total['base']) * 100;
        
        return $total;
    }
    
    /**
     * Calculate growth potential
     */
    private function calculateGrowthPotential(string $level): array
    {
        $increases = $this->salaryData['salary_increases'];
        
        return [
            'annual_merit_increase' => $increases['annual_merit'],
            'promotion_increase' => $increases['promotion'],
            'job_change_increase' => $increases['company_change'],
            'skill_upgrade_increase' => $increases['skill_upgrade'],
            'five_year_projection' => $this->calculateFiveYearProjection($level)
        ];
    }
    
    /**
     * Calculate five year projection
     */
    private function calculateFiveYearProjection(string $level): array
    {
        $annualGrowth = 0.06; // 6% average annual growth
        $projection = [];
        
        for ($year = 1; $year <= 5; $year++) {
            $projection[$year] = [
                'year' => $year,
                'multiplier' => pow(1 + $annualGrowth, $year),
                'estimated_increase_percentage' => ($pow(1 + $annualGrowth, $year) - 1) * 100
            ];
        }
        
        return $projection;
    }
    
    /**
     * Get negotiation tips
     */
    private function getNegotiationTips(string $level, string $region): array
    {
        $tips = [
            'research' => 'Research salaries for similar roles in your area',
            'timing' => 'Negotiate after receiving an offer but before accepting',
            'multiple_offers' => 'Having multiple offers increases leverage',
            'total_compensation' => 'Consider total compensation, not just base salary',
            'benefits' => 'Factor in health insurance, retirement, and other benefits',
            'location' => 'Cost of living affects salary negotiations',
            'experience' => 'Highlight relevant experience and achievements',
            'skills' => 'Emphasize in-demand skills and certifications'
        ];
        
        if ($level === 'senior_developer' || $level === 'lead_developer' || $level === 'architect') {
            $tips[] = 'leadership';
            $tips[] = 'impact';
            $tips[] = 'scope';
        }
        
        return $tips;
    }
    
    /**
     * Get skill requirements analysis
     */
    public function getSkillRequirementsAnalysis(): array
    {
        return [
            'core_skills' => $this->analyzeSkillCategory('core_technical_skills'),
            'advanced_skills' => $this->analyzeSkillCategory('advanced_technical_skills'),
            'soft_skills' => $this->analyzeSkillCategory('soft_skills'),
            'skill_gap_analysis' => $this->identifySkillGaps(),
            'learning_recommendations' => $this->getLearningRecommendations()
        ];
    }
    
    /**
     * Analyze skill category
     */
    private function analyzeSkillCategory(string $category): array
    {
        $skills = $this->skillRequirements[$category] ?? [];
        $analysis = [];
        
        foreach ($skills as $skill => $data) {
            $analysis[$skill] = [
                'proficiency' => $data['proficiency'],
                'importance' => $data['importance'],
                'assessment' => $data['assessment'],
                'priority_score' => $this->calculateSkillPriority($data)
            ];
        }
        
        uasort($analysis, function($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });
        
        return $analysis;
    }
    
    /**
     * Calculate skill priority
     */
    private function calculateSkillPriority(array $skillData): int
    {
        $proficiencyScores = [
            'Beginner' => 1,
            'Intermediate' => 2,
            'Advanced' => 3,
            'Expert' => 4
        ];
        
        $importanceScores = [
            'Low' => 1,
            'Medium' => 2,
            'High' => 3,
            'Critical' => 4
        ];
        
        return $proficiencyScores[$skillData['proficiency']] * $importanceScores[$skillData['importance']];
    }
    
    /**
     * Identify skill gaps
     */
    private function identifySkillGaps(): array
    {
        $gaps = [];
        
        foreach ($this->skillRequirements as $category => $skills) {
            foreach ($skills as $skill => $data) {
                if ($data['importance'] === 'Critical' && $data['proficiency'] !== 'Expert') {
                    $gaps[$skill] = [
                        'category' => $category,
                        'current_level' => $data['proficiency'],
                        'required_level' => 'Expert',
                        'priority' => 'High'
                    ];
                } elseif ($data['importance'] === 'High' && $data['proficiency'] === 'Beginner') {
                    $gaps[$skill] = [
                        'category' => $category,
                        'current_level' => $data['proficiency'],
                        'required_level' => 'Advanced',
                        'priority' => 'Medium'
                    ];
                }
            }
        }
        
        return $gaps;
    }
    
    /**
     * Get learning recommendations
     */
    private function getLearningRecommendations(): array
    {
        return [
            'online_courses' => [
                'Laravel' => 'Laravel official documentation and Laracasts',
                'Symfony' => 'Symfony official documentation and SymfonyCasts',
                'Testing' => 'PHPUnit documentation and testing best practices',
                'DevOps' => 'Docker, Kubernetes, and CI/CD courses'
            ],
            'certifications' => [
                'Zend Certified PHP Engineer',
                'AWS Certified Developer',
                'Docker Certified Associate',
                'Kubernetes Administrator'
            ],
            'books' => [
                'Clean Code' => 'Robert C. Martin',
                'Design Patterns' => 'Gang of Four',
                'The Art of Computer Programming' => 'Donald Knuth',
                'Clean Architecture' => 'Robert C. Martin'
            ],
            'practice_projects' => [
                'E-commerce platform',
                'RESTful API service',
                'Content management system',
                'Real-time chat application'
            ]
        ];
    }
    
    /**
     * Get industry trends analysis
     */
    public function getIndustryTrendsAnalysis(): array
    {
        return [
            'emerging_technologies' => $this->analyzeEmergingTechnologies(),
            'market_trends' => $this->analyzeMarketTrends(),
            'future_outlook' => $this->analyzeFutureOutlook(),
            'recommendations' => $this->getTrendRecommendations()
        ];
    }
    
    /**
     * Analyze emerging technologies
     */
    private function analyzeEmergingTechnologies(): array
    {
        $technologies = $this->industryTrends['emerging_technologies'];
        
        foreach ($technologies as $tech => &$data) {
            $data['investment_recommendation'] = $this->getInvestmentRecommendation($data);
            $data['learning_priority'] = $this->getLearningPriority($data);
        }
        
        return $technologies;
    }
    
    /**
     * Get investment recommendation
     */
    private function getInvestmentRecommendation(array $technology): string
    {
        if ($technology['growth_rate'] > 0.40 && $technology['skill_demand'] === 'High') {
            return 'High Priority - Invest now';
        } elseif ($technology['growth_rate'] > 0.25 && $technology['skill_demand'] === 'High') {
            return 'Medium Priority - Plan for next year';
        } else {
            return 'Low Priority - Monitor trend';
        }
    }
    
    /**
     * Get learning priority
     */
    private function getLearningPriority(array $technology): int
    {
        $priority = 0;
        $priority += $technology['growth_rate'] * 100;
        $priority += $technology['skill_demand'] === 'Very High' ? 50 : 
                    ($technology['skill_demand'] === 'High' ? 30 : 
                    ($technology['skill_demand'] === 'Medium' ? 15 : 5));
        $priority += $technology['adoption_level'] === 'Current' ? 20 : 
                    ($technology['adoption_level'] === 'Mature' ? 10 : 5);
        
        return (int) $priority;
    }
    
    /**
     * Analyze market trends
     */
    private function analyzeMarketTrends(): array
    {
        return $this->industryTrends['market_trends'];
    }
    
    /**
     * Analyze future outlook
     */
    private function analyzeFutureOutlook(): array
    {
        $outlook = $this->industryTrends['future_outlook'];
        
        $outlook['average_job_growth'] = array_sum($outlook['job_growth']) / count($outlook['job_growth']);
        $outlook['average_salary_growth'] = array_sum($outlook['salary_growth']) / count($outlook['salary_growth']);
        
        return $outlook;
    }
    
    /**
     * Get trend recommendations
     */
    private function getTrendRecommendations(): array
    {
        return [
            'skills_to_focus' => [
                'PHP 8+ features',
                'Cloud services (AWS/Azure/GCP)',
                'Containerization (Docker/Kubernetes)',
                'API design and development',
                'Testing and automation'
            ],
            'career_strategies' => [
                'Specialize in high-demand areas',
                'Develop full-stack capabilities',
                'Build strong portfolio',
                'Network actively',
                'Stay current with trends'
            ],
            'investment_areas' => [
                'Continuous learning',
                'Professional certifications',
                'Open source contributions',
                'Personal branding',
                'Soft skills development'
            ]
        ];
    }
    
    /**
     * Generate comprehensive report
     */
    public function generateComprehensiveReport(): string
    {
        $report = "PHP Job Market Analysis Report\n";
        $report .= str_repeat("=", 35) . "\n\n";
        
        // Executive Summary
        $overview = $this->getJobMarketOverview();
        $report .= "Executive Summary:\n";
        $report .= str_repeat("-", 18) . "\n";
        $report .= "Total Open Positions: {$overview['total_open_positions']}\n";
        $report .= "Average Growth Rate: " . round($overview['average_growth_rate'] * 100, 1) . "%\n\n";
        
        // Salary Analysis
        $report .= "Salary Analysis:\n";
        $report .= str_repeat("-", 16) . "\n";
        $salaryAnalysis = $this->getSalaryAnalysis();
        $report .= "Base Salary Range: \${$salaryAnalysis['base_salary']['min']} - \${$salaryAnalysis['base_salary']['max']}\n";
        $report .= "Total Compensation: \${$salaryAnalysis['total_compensation']['total']} (+" . round($salaryAnalysis['total_compensation']['percentage_increase'], 1) . "%)\n\n";
        
        // Skills Analysis
        $report .= "Skills Analysis:\n";
        $report .= str_repeat("-", 16) . "\n";
        $skillsAnalysis = $this->getSkillRequirementsAnalysis();
        $report .= "Critical Skills Gap: " . count($skillsAnalysis['skill_gap_analysis']) . "\n";
        $report .= "Top Priority Skills:\n";
        
        $allSkills = array_merge($skillsAnalysis['core_skills'], $skillsAnalysis['advanced_skills']);
        $topSkills = array_slice($allSkills, 0, 5, true);
        
        foreach ($topSkills as $skill => $data) {
            $report .= "  - $skill (Priority Score: {$data['priority_score']})\n";
        }
        $report .= "\n";
        
        // Industry Trends
        $report .= "Industry Trends:\n";
        $report .= str_repeat("-", 16) . "\n";
        $trendsAnalysis = $this->getIndustryTrendsAnalysis();
        $report .= "Average Job Growth: " . round($trendsAnalysis['future_outlook']['average_job_growth'] * 100, 1) . "%\n";
        $report .= "Average Salary Growth: " . round($trendsAnalysis['future_outlook']['average_salary_growth'] * 100, 1) . "%\n\n";
        
        // Recommendations
        $report .= "Recommendations:\n";
        $report .= str_repeat("-", 16) . "\n";
        
        foreach ($trendsAnalysis['recommendations']['skills_to_focus'] as $skill) {
            $report .= "• Focus on: $skill\n";
        }
        
        $report .= "\nCareer Strategies:\n";
        foreach ($trendsAnalysis['recommendations']['career_strategies'] as $strategy) {
            $report .= "• $strategy\n";
        }
        
        return $report;
    }
}

// Job Market Examples
class JobMarketExamples
{
    private PHPJobMarketAnalyzer $analyzer;
    
    public function __construct()
    {
        $this->analyzer = new PHPJobMarketAnalyzer();
    }
    
    public function demonstrateJobMarketAnalysis(): void
    {
        echo "PHP Job Market Analysis Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Job market overview
        $overview = $this->analyzer->getJobMarketOverview();
        
        echo "Job Market Overview:\n";
        echo "Total Open Positions: {$overview['total_open_positions']}\n";
        echo "Average Growth Rate: " . round($overview['average_growth_rate'] * 100, 1) . "%\n";
        
        echo "\nTop Growth Roles:\n";
        foreach ($overview['top_growth_roles'] as $role => $data) {
            $roleName = ucwords(str_replace('_', ' ', $role));
            echo "  $roleName: " . round($data['growth_rate'] * 100, 1) . "% growth, {$data['open_positions']} positions\n";
        }
        
        echo "\nGeographic Opportunities:\n";
        foreach ($overview['geographic_opportunities'] as $region => $data) {
            echo "  $region: {$data['total_positions']} positions\n";
            echo "    Top Cities: " . implode(', ', array_keys($data['top_cities'])) . "\n";
            echo "    Average Salary: \${$data['average_salary']['mid_level_developer']}\n";
        }
        
        echo "\nIndustry Distribution:\n";
        foreach ($overview['industry_distribution'] as $industry => $data) {
            echo "  $data[name]: {$data['percentage']}% ({$data['growth_trend']} growth)\n";
        }
    }
    
    public function demonstrateSalaryAnalysis(): void
    {
        echo "\nSalary Analysis Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $levels = ['junior_developer', 'mid_level_developer', 'senior_developer', 'lead_developer', 'architect'];
        $regions = ['united_states', 'europe', 'asia'];
        
        foreach ($levels as $level) {
            echo "\n" . ucwords(str_replace('_', ' ', $level)) . ":\n";
            echo str_repeat("-", strlen(ucwords(str_replace('_', ' ', $level))) . "\n";
            
            foreach ($regions as $region) {
                $analysis = $this->analyzer->getSalaryAnalysis($level, $region);
                
                if (isset($analysis['error'])) {
                    continue;
                }
                
                echo "  $region:\n";
                echo "    Base Salary: \${$analysis['base_salary']['min']} - \${$analysis['base_salary']['max']}\n";
                echo "    Total Compensation: \${$analysis['total_compensation']['total']} (+" . round($analysis['total_compensation']['percentage_increase'], 1) . "%)\n";
                echo "    Growth Potential: " . round($analysis['growth_potential']['annual_merit_increase'] * 100, 1) . "% annual merit increase\n";
            }
        }
    }
    
    public function demonstrateSkillRequirements(): void
    {
        echo "\nSkill Requirements Analysis\n";
        echo str_repeat("-", 28) . "\n";
        
        $skillsAnalysis = $this->analyzer->getSkillRequirementsAnalysis();
        
        echo "Core Technical Skills:\n";
        foreach ($skillsAnalysis['core_skills'] as $skill => $data) {
            echo "  $skill: {$data['proficiency']} proficiency, {$data['importance']} importance (Score: {$data['priority_score']})\n";
        }
        
        echo "\nAdvanced Technical Skills:\n";
        foreach ($skillsAnalysis['advanced_skills'] as $skill => $data) {
            echo "  $skill: {$data['proficiency']} proficiency, {$data['importance']} importance (Score: {$data['priority_score']})\n";
        }
        
        echo "\nSoft Skills:\n";
        foreach ($skillsAnalysis['soft_skills'] as $skill => $data) {
            echo "  $skill: {$data['proficiency']} proficiency, {$data['importance']} importance (Score: {$data['priority_score']})\n";
        }
        
        echo "\nSkill Gaps Identified:\n";
        foreach ($skillsAnalysis['skill_gap_analysis'] as $skill => $gap) {
            echo "  $skill: {$gap['current_level']} → {$gap['required_level']} ({$gap['priority']} priority)\n";
        }
        
        echo "\nLearning Recommendations:\n";
        echo "Online Courses:\n";
        foreach ($skillsAnalysis['learning_recommendations']['online_courses'] as $skill => $resource) {
            echo "  $skill: $resource\n";
        }
        
        echo "\nCertifications:\n";
        foreach ($skillsAnalysis['learning_recommendations']['certifications'] as $cert) {
            echo "  • $cert\n";
        }
    }
    
    public function demonstrateIndustryTrends(): void
    {
        echo "\nIndustry Trends Analysis\n";
        echo str_repeat("-", 25) . "\n";
        
        $trendsAnalysis = $this->analyzer->getIndustryTrendsAnalysis();
        
        echo "Emerging Technologies:\n";
        foreach ($trendsAnalysis['emerging_technologies'] as $tech => $data) {
            echo "  $tech: " . round($data['growth_rate'] * 100, 1) . "% growth, {$data['skill_demand']} demand\n";
            echo "    Investment: {$data['investment_recommendation']}\n";
            echo "    Timeline: {$data['timeline']}\n";
        }
        
        echo "\nMarket Trends:\n";
        foreach ($trendsAnalysis['market_trends'] as $trend => $data) {
            echo "  " . ucwords(str_replace('_', ' ', $trend)) . ": " . round($data['growth_rate'] * 100, 1) . "% growth ({$data['impact']} impact)\n";
        }
        
        echo "\nFuture Outlook:\n";
        echo "Average Job Growth: " . round($trendsAnalysis['future_outlook']['average_job_growth'] * 100, 1) . "%\n";
        echo "Average Salary Growth: " . round($trendsAnalysis['future_outlook']['average_salary_growth'] * 100, 1) . "%\n";
        
        echo "\nFive-Year Job Growth Projection:\n";
        foreach ($trendsAnalysis['future_outlook']['job_growth'] as $year => $growth) {
            echo "  $year: " . round($growth * 100, 1) . "%\n";
        }
        
        echo "\nSkill Evolution:\n";
        echo "Declining: " . implode(', ', $trendsAnalysis['future_outlook']['skill_evolution']['declining']) . "\n";
        echo "Stable: " . implode(', ', $trendsAnalysis['future_outlook']['skill_evolution']['stable']) . "\n";
        echo "Growing: " . implode(', ', $trendsAnalysis['future_outlook']['skill_evolution']['growing']) . "\n";
        echo "Emerging: " . implode(', ', $trendsAnalysis['future_outlook']['skill_evolution']['emerging']) . "\n";
    }
    
    public function demonstrateComprehensiveReport(): void
    {
        echo "\nComprehensive Report\n";
        echo str_repeat("-", 20) . "\n";
        
        $report = $this->analyzer->generateComprehensiveReport();
        echo $report;
    }
    
    public function runAllExamples(): void
    {
        echo "PHP Job Market Guide Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateJobMarketAnalysis();
        $this->demonstrateSalaryAnalysis();
        $this->demonstrateSkillRequirements();
        $this->demonstrateIndustryTrends();
        $this->demonstrateComprehensiveReport();
    }
}

// Job Market Best Practices
function printJobMarketBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Job Market Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Market Research:\n";
    echo "   • Research current job market trends\n";
    echo "   • Understand salary expectations\n";
    echo "   • Identify in-demand skills\n";
    echo "   • Analyze geographic opportunities\n";
    echo "   • Study industry sectors\n\n";
    
    echo "2. Skill Development:\n";
    echo "   • Focus on core PHP skills\n";
    echo "   • Learn modern frameworks\n";
    echo "   • Master database concepts\n";
    echo "   • Develop soft skills\n";
    echo "   • Stay current with trends\n\n";
    
    echo "3. Salary Negotiation:\n";
    echo "   • Research market rates\n";
    echo "   • Know your worth\n";
    echo "   • Consider total compensation\n";
    echo "   • Negotiate confidently\n";
    echo "   • Get offers in writing\n\n";
    
    echo "4. Career Planning:\n";
    echo "   • Set clear goals\n";
    echo "   • Create development plan\n";
    echo "   • Build strong portfolio\n";
    echo "   • Network actively\n";
    echo "   • Seek mentorship\n\n";
    
    echo "5. Continuous Learning:\n";
    echo "   • Learn emerging technologies\n";
    echo "   • Obtain certifications\n";
    echo "   • Attend conferences\n";
    echo "   • Contribute to open source\n";
    echo "   • Share knowledge";
}

// Main execution
function runJobMarketGuideDemo(): void
{
    $examples = new JobMarketExamples();
    $examples->runAllExamples();
    printJobMarketBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runJobMarketGuideDemo();
}
?>
