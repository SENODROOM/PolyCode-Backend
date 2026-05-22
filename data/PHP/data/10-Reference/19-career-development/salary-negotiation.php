<?php
/**
 * Salary and Compensation Negotiation
 * 
 * This file demonstrates salary research, negotiation strategies,
 * compensation analysis, and career advancement planning.
 */

// Salary Research Analyzer
class SalaryResearchAnalyzer
{
    private array $salaryData = [];
    private array $marketData = [];
    private array $industryData = [];
    
    public function __construct()
    {
        $this->initializeSalaryData();
        $this->initializeMarketData();
        $this->initializeIndustryData();
    }
    
    /**
     * Initialize salary data
     */
    private function initializeSalaryData(): void
    {
        $this->salaryData = [
            'united_states' => [
                'junior_developer' => [
                    'base_range' => [65000, 85000],
                    'median' => 75000,
                    'bonus_range' => [0, 5000],
                    'equity_range' => [0, 10000],
                    'total_range' => [65000, 100000]
                ],
                'mid_level_developer' => [
                    'base_range' => [85000, 120000],
                    'median' => 105000,
                    'bonus_range' => [5000, 15000],
                    'equity_range' => [10000, 25000],
                    'total_range' => [100000, 160000]
                ],
                'senior_developer' => [
                    'base_range' => [120000, 160000],
                    'median' => 140000,
                    'bonus_range' => [10000, 25000],
                    'equity_range' => [25000, 50000],
                    'total_range' => [155000, 235000]
                ],
                'lead_developer' => [
                    'base_range' => [140000, 190000],
                    'median' => 165000,
                    'bonus_range' => [15000, 30000],
                    'equity_range' => [40000, 75000],
                    'total_range' => [195000, 295000]
                ],
                'architect' => [
                    'base_range' => [170000, 250000],
                    'median' => 210000,
                    'bonus_range' => [20000, 40000],
                    'equity_range' => [60000, 120000],
                    'total_range' => [250000, 410000]
                ]
            ],
            'europe' => [
                'junior_developer' => [
                    'base_range' => [40000, 55000],
                    'median' => 47500,
                    'bonus_range' => [0, 3000],
                    'equity_range' => [0, 5000],
                    'total_range' => [40000, 63000]
                ],
                'mid_level_developer' => [
                    'base_range' => [55000, 75000],
                    'median' => 65000,
                    'bonus_range' => [3000, 8000],
                    'equity_range' => [5000, 15000],
                    'total_range' => [63000, 98000]
                ],
                'senior_developer' => [
                    'base_range' => [75000, 95000],
                    'median' => 85000,
                    'bonus_range' => [8000, 15000],
                    'equity_range' => [15000, 30000],
                    'total_range' => [98000, 140000]
                ],
                'lead_developer' => [
                    'base_range' => [85000, 110000],
                    'median' => 97500,
                    'bonus_range' => [10000, 20000],
                    'equity_range' => [25000, 45000],
                    'total_range' => [120000, 175000]
                ],
                'architect' => [
                    'base_range' => [100000, 140000],
                    'median' => 120000,
                    'bonus_range' => [15000, 25000],
                    'equity_range' => [40000, 70000],
                    'total_range' => [155000, 235000]
                ]
            ],
            'asia' => [
                'junior_developer' => [
                    'base_range' => [30000, 45000],
                    'median' => 37500,
                    'bonus_range' => [0, 2000],
                    'equity_range' => [0, 3000],
                    'total_range' => [30000, 50000]
                ],
                'mid_level_developer' => [
                    'base_range' => [45000, 65000],
                    'median' => 55000,
                    'bonus_range' => [2000, 6000],
                    'equity_range' => [3000, 10000],
                    'total_range' => [50000, 81000]
                ],
                'senior_developer' => [
                    'base_range' => [65000, 85000],
                    'median' => 75000,
                    'bonus_range' => [6000, 12000],
                    'equity_range' => [10000, 25000],
                    'total_range' => [81000, 122000]
                ],
                'lead_developer' => [
                    'base_range' => [75000, 95000],
                    'median' => 85000,
                    'bonus_range' => [8000, 15000],
                    'equity_range' => [20000, 40000],
                    'total_range' => [103000, 150000]
                ],
                'architect' => [
                    'base_range' => [90000, 120000],
                    'median' => 105000,
                    'bonus_range' => [10000, 20000],
                    'equity_range' => [35000, 60000],
                    'total_range' => [135000, 200000]
                ]
            ]
        ];
    }
    
    /**
     * Initialize market data
     */
    private function initializeMarketData(): void
    {
        $this->marketData = [
            'cost_of_living' => [
                'san_francisco' => 1.8,
                'new_york' => 1.7,
                'seattle' => 1.5,
                'austin' => 1.2,
                'chicago' => 1.1,
                'boston' => 1.4,
                'los_angeles' => 1.6,
                'denver' => 1.1,
                'atlanta' => 1.0,
                'miami' => 1.0
            ],
            'demand_multiplier' => [
                'high_tech' => 1.2,
                'fintech' => 1.15,
                'healthcare_tech' => 1.1,
                'ecommerce' => 1.05,
                'traditional' => 1.0,
                'government' => 0.95
            ],
            'company_size_multiplier' => [
                'startup' => 0.9,
                'small' => 0.95,
                'medium' => 1.0,
                'large' => 1.1,
                'enterprise' => 1.2
            ],
            'experience_adjustment' => [
                0 => 0.7,    // 0 years
                1 => 0.8,    // 1 year
                2 => 0.85,   // 2 years
                3 => 0.9,    // 3 years
                5 => 1.0,    // 5 years
                7 => 1.1,    // 7 years
                10 => 1.2,   // 10 years
                15 => 1.3,   // 15 years
                20 => 1.4    // 20+ years
            ]
        ];
    }
    
    /**
     * Initialize industry data
     */
    private function initializeIndustryData(): void
    {
        $this->industryData = [
            'technology' => [
                'average_salary' => 120000,
                'growth_rate' => 0.12,
                'benefits_score' => 0.85,
                'work_life_balance' => 0.75
            ],
            'finance' => [
                'average_salary' => 135000,
                'growth_rate' => 0.08,
                'benefits_score' => 0.90,
                'work_life_balance' => 0.65
            ],
            'healthcare' => [
                'average_salary' => 110000,
                'growth_rate' => 0.10,
                'benefits_score' => 0.88,
                'work_life_balance' => 0.80
            ],
            'retail' => [
                'average_salary' => 95000,
                'growth_rate' => 0.06,
                'benefits_score' => 0.75,
                'work_life_balance' => 0.70
            ],
            'education' => [
                'average_salary' => 85000,
                'growth_rate' => 0.07,
                'benefits_score' => 0.80,
                'work_life_balance' => 0.85
            ],
            'government' => [
                'average_salary' => 90000,
                'growth_rate' => 0.04,
                'benefits_score' => 0.95,
                'work_life_balance' => 0.90
            ]
        ];
    }
    
    /**
     * Analyze salary for specific role and location
     */
    public function analyzeSalary(array $criteria): array
    {
        $role = $criteria['role'] ?? 'mid_level_developer';
        $region = $criteria['region'] ?? 'united_states';
        $location = $criteria['location'] ?? '';
        $experience = $criteria['experience'] ?? 5;
        $industry = $criteria['industry'] ?? 'technology';
        $companySize = $criteria['company_size'] ?? 'medium';
        
        $baseSalary = $this->salaryData[$region][$role] ?? null;
        
        if (!$baseSalary) {
            return ['error' => 'Invalid role or region'];
        }
        
        // Calculate adjustments
        $adjustments = [
            'base_median' => $baseSalary['median'],
            'experience_adjustment' => $this->getExperienceMultiplier($experience),
            'location_adjustment' => $this->getLocationMultiplier($location),
            'industry_adjustment' => $this->getIndustryMultiplier($industry),
            'company_size_adjustment' => $this->getCompanySizeMultiplier($companySize)
        ];
        
        // Calculate adjusted salary
        $adjustedMedian = $adjustments['base_median'];
        $adjustedMedian *= $adjustments['experience_adjustment'];
        $adjustedMedian *= $adjustments['location_adjustment'];
        $adjustedMedian *= $adjustments['industry_adjustment'];
        $adjustedMedian *= $adjustments['company_size_adjustment'];
        
        // Calculate total compensation
        $totalCompensation = [
            'base_salary' => $adjustedMedian,
            'bonus' => $adjustedMedian * 0.1, // 10% bonus average
            'equity' => $adjustedMedian * 0.15, // 15% equity average
            'benefits_value' => $adjustedMedian * 0.25, // 25% benefits value
            'total' => 0
        ];
        
        $totalCompensation['total'] = array_sum($totalCompensation);
        
        return [
            'role' => $role,
            'region' => $region,
            'location' => $location,
            'experience' => $experience,
            'industry' => $industry,
            'company_size' => $companySize,
            'base_salary_range' => $baseSalary['base_range'],
            'adjusted_salary' => $adjustedMedian,
            'adjustments' => $adjustments,
            'total_compensation' => $totalCompensation,
            'market_position' => $this->calculateMarketPosition($adjustedMedian, $baseSalary),
            'negotiation_range' => $this->calculateNegotiationRange($adjustedMedian)
        ];
    }
    
    /**
     * Get experience multiplier
     */
    private function getExperienceMultiplier(int $years): float
    {
        $adjustments = $this->marketData['experience_adjustment'];
        
        foreach ($adjustments as $maxYears => $multiplier) {
            if ($years <= $maxYears) {
                return $multiplier;
            }
        }
        
        return end($adjustments);
    }
    
    /**
     * Get location multiplier
     */
    private function getLocationMultiplier(string $location): float
    {
        if (empty($location)) {
            return 1.0;
        }
        
        $location = strtolower(str_replace([' ', '-'], '_', $location));
        $costOfLiving = $this->marketData['cost_of_living'];
        
        return $costOfLiving[$location] ?? 1.0;
    }
    
    /**
     * Get industry multiplier
     */
    private function getIndustryMultiplier(string $industry): float
    {
        return $this->marketData['demand_multiplier'][$industry] ?? 1.0;
    }
    
    /**
     * Get company size multiplier
     */
    private function getCompanySizeMultiplier(string $size): float
    {
        return $this->marketData['company_size_multiplier'][$size] ?? 1.0;
    }
    
    /**
     * Calculate market position
     */
    private function calculateMarketPosition(float $salary, array $salaryData): string
    {
        $range = $salaryData['base_range'];
        
        if ($salary < $range[0]) {
            return 'Below Market';
        } elseif ($salary < ($range[0] + ($range[1] - $range[0]) * 0.25)) {
            return '25th Percentile';
        } elseif ($salary < ($range[0] + ($range[1] - $range[0]) * 0.5)) {
            return '50th Percentile';
        } elseif ($salary < ($range[0] + ($range[1] - $range[0]) * 0.75)) {
            return '75th Percentile';
        } elseif ($salary <= $range[1]) {
            return '90th Percentile';
        } else {
            return 'Above Market';
        }
    }
    
    /**
     * Calculate negotiation range
     */
    private function calculateNegotiationRange(float $baseSalary): array
    {
        return [
            'conservative' => $baseSalary * 1.05,
            'moderate' => $baseSalary * 1.10,
            'aggressive' => $baseSalary * 1.15,
            'maximum' => $baseSalary * 1.20
        ];
    }
    
    /**
     * Compare multiple offers
     */
    public function compareOffers(array $offers): array
    {
        $comparison = [];
        
        foreach ($offers as $index => $offer) {
            $analysis = $this->analyzeSalary($offer);
            $comparison[$index] = [
                'company' => $offer['company'] ?? 'Company ' . ($index + 1),
                'base_salary' => $analysis['adjusted_salary'],
                'total_compensation' => $analysis['total_compensation']['total'],
                'market_position' => $analysis['market_position'],
                'benefits_score' => $this->calculateBenefitsScore($offer),
                'work_life_balance' => $this->calculateWorkLifeBalance($offer),
                'growth_potential' => $this->calculateGrowthPotential($offer),
                'overall_score' => 0
            ];
        }
        
        // Calculate overall scores
        foreach ($comparison as &$comp) {
            $comp['overall_score'] = 
                ($comp['total_compensation'] / 100000) * 0.4 +
                $comp['benefits_score'] * 0.2 +
                $comp['work_life_balance'] * 0.15 +
                $comp['growth_potential'] * 0.25;
        }
        
        // Sort by overall score
        uasort($comparison, function($a, $b) {
            return $b['overall_score'] <=> $a['overall_score'];
        });
        
        return $comparison;
    }
    
    /**
     * Calculate benefits score
     */
    private function calculateBenefitsScore(array $offer): float
    {
        $score = 0.5; // Base score
        
        if (isset($offer['health_insurance'])) {
            $score += 0.15;
        }
        
        if (isset($offer['retirement_401k'])) {
            $score += 0.1;
        }
        
        if (isset($offer['paid_time_off'])) {
            $score += 0.1;
        }
        
        if (isset($offer['remote_work'])) {
            $score += 0.1;
        }
        
        if (isset($offer['training_budget'])) {
            $score += 0.05;
        }
        
        return min($score, 1.0);
    }
    
    /**
     * Calculate work-life balance score
     */
    private function calculateWorkLifeBalance(array $offer): float
    {
        $score = 0.5; // Base score
        
        if (isset($offer['flexible_hours'])) {
            $score += 0.15;
        }
        
        if (isset($offer['remote_work'])) {
            $score += 0.2;
        }
        
        if (isset($offer['unlimited_pto'])) {
            $score += 0.1;
        }
        
        if (isset($offer['no_weekend_work'])) {
            $score += 0.05;
        }
        
        return min($score, 1.0);
    }
    
    /**
     * Calculate growth potential
     */
    private function calculateGrowthPotential(array $offer): float
    {
        $score = 0.5; // Base score
        
        if (isset($offer['promotion_path'])) {
            $score += 0.15;
        }
        
        if (isset($offer['training_budget'])) {
            $score += 0.1;
        }
        
        if (isset($offer['mentorship_program'])) {
            $score += 0.1;
        }
        
        if (isset($offer['fast_growth'])) {
            $score += 0.15;
        }
        
        return min($score, 1.0);
    }
}

// Negotiation Coach
class NegotiationCoach
{
    private array $strategies = [];
    private array $tactics = [];
    private array $commonMistakes = [];
    
    public function __construct()
    {
        $this->initializeStrategies();
        $this->initializeTactics();
        $this->initializeCommonMistakes();
    }
    
    /**
     * Initialize negotiation strategies
     */
    private function initializeStrategies(): void
    {
        $this->strategies = [
            'preparation' => [
                'research_market_rates' => 'Research current market rates for your role and location',
                'know_your_worth' => 'Understand your value based on skills and experience',
                'identify_leverage' => 'Identify your unique value proposition and leverage points',
                'prepare_alternatives' => 'Have alternative offers or BATNA (Best Alternative to Negotiated Agreement)',
                'practice_scenarios' => 'Practice different negotiation scenarios and responses'
            ],
            'timing' => [
                'wait_for_offer' => 'Wait until you have a written offer before negotiating',
                'express_enthusiasm' => 'Show genuine enthusiasm for the role and company',
                'ask_for_time' => 'Ask for reasonable time to consider the offer',
                'follow_up_promptly' => 'Follow up promptly with your counter-offer'
            ],
            'communication' => [
                'be_professional' => 'Maintain professional tone throughout negotiations',
                'be_specific' => 'Be specific with your requests and justifications',
                'use_data' => 'Use market data and specific examples to support your requests',
                'listen_actively' => 'Listen to understand their constraints and priorities',
                'be_flexible' => 'Show willingness to find creative solutions'
            ],
            'value_proposition' => [
                'highlight_skills' => 'Emphasize skills that are in high demand',
                'show_achievements' => 'Provide specific examples of past achievements',
                'demonstrate_impact' => 'Show how you can impact their business',
                'offer_solutions' => 'Position yourself as a solution to their problems',
                'future_potential' => 'Highlight your growth potential and long-term value'
            ]
        ];
    }
    
    /**
     * Initialize negotiation tactics
     */
    private function initializeTactics(): void
    {
        $this->tactics = [
            'anchoring' => [
                'description' => 'Set a high but reasonable anchor point',
                'when_to_use' => 'Early in negotiations',
                'example' => 'Based on my research and experience, I\'m targeting a salary in the range of $X-$Y'
            ],
            'bracketing' => [
                'description' => 'Use a range to establish boundaries',
                'when_to_use' => 'When discussing salary expectations',
                'example' => 'I\'m looking for a role in the $X-$Y range, depending on the total compensation package'
            ],
            'multiple_offer' => [
                'description' => 'Leverage multiple offers to create competition',
                'when_to_use' => 'When you have multiple offers',
                'example' => 'I have another offer for $X, but I\'m really interested in this role because...'
            ],
            'value_add' => [
                'description' => 'Focus on additional value you bring',
                'when_to_use' => 'When salary negotiation hits a ceiling',
                'example' => 'While the base salary is important, I can also bring X, Y, and Z to the team'
            ],
            'creative_solutions' => [
                'description' => 'Propose creative solutions to meet both sides',
                'when_to_use' => 'When direct salary increases aren\'t possible',
                'example' => 'If the base salary is fixed, perhaps we could discuss a signing bonus or performance-based compensation'
            ]
        ];
    }
    
    /**
     * Initialize common mistakes
     */
    private function initializeCommonMistakes(): void
    {
        $this->commonMistakes = [
            'accepting_first_offer' => [
                'mistake' => 'Accepting the first offer without negotiation',
                'consequence' => 'Leaving money and benefits on the table',
                'solution' => 'Always negotiate, even if the offer seems good'
            ],
            'focusing_only_on_salary' => [
                'mistake' => 'Focusing only on base salary',
                'consequence' => 'Missing out on valuable benefits and equity',
                'solution' => 'Consider total compensation package'
            ],
            'making_ultimatums' => [
                'mistake' => 'Making ultimatums or threats',
                'consequence' => 'Damaging relationship and losing opportunity',
                'solution' => 'Maintain collaborative approach'
            ],
            'not_doing_research' => [
                'mistake' => 'Not researching market rates',
                'consequence' => 'Having unrealistic expectations',
                'solution' => 'Research thoroughly before negotiating'
            ],
            'being_too_aggressive' => [
                'mistake' => 'Being too aggressive or demanding',
                'consequence' => 'Creating negative impression',
                'solution' => 'Be firm but professional and collaborative'
            ],
            'not_having_alternatives' => [
                'mistake' => 'Not having alternative options',
                'consequence' => 'Weak negotiation position',
                'solution' => 'Always have alternatives or BATNA'
            ]
        ];
    }
    
    /**
     * Generate negotiation plan
     */
    public function generateNegotiationPlan(array $offer, array $personalInfo): array
    {
        $plan = [
            'preparation' => $this->prepareNegotiation($offer, $personalInfo),
            'strategy' => $this->selectStrategy($offer, $personalInfo),
            'tactics' => $this->selectTactics($offer, $personalInfo),
            'talking_points' => $this->generateTalkingPoints($offer, $personalInfo),
            'scenarios' => $this->prepareScenarios($offer, $personalInfo),
            'follow_up' => $this->planFollowUp($offer, $personalInfo)
        ];
        
        return $plan;
    }
    
    /**
     * Prepare for negotiation
     */
    private function prepareNegotiation(array $offer, array $personalInfo): array
    {
        return [
            'market_research' => [
                'task' => 'Research market rates for similar roles',
                'resources' => ['Glassdoor', 'Levels.fyi', 'Payscale', 'Industry reports'],
                'timeline' => '2-3 days before negotiation'
            ],
            'value_assessment' => [
                'task' => 'Assess your unique value proposition',
                'factors' => ['Skills', 'Experience', 'Achievements', 'Industry knowledge'],
                'timeline' => '1-2 days before negotiation'
            ],
            'alternative_preparation' => [
                'task' => 'Prepare alternatives and BATNA',
                'considerations' => ['Other offers', 'Current job', 'Freelance options'],
                'timeline' => 'Before negotiation starts'
            ],
            'practice' => [
                'task' => 'Practice negotiation scenarios',
                'methods' => ['Role-playing', 'Recording practice', 'Getting feedback'],
                'timeline' => 'Ongoing preparation'
            ]
        ];
    }
    
    /**
     * Select negotiation strategy
     */
    private function selectStrategy(array $offer, array $personalInfo): array
    {
        $strategy = [
            'primary_approach' => 'collaborative',
            'tone' => 'professional and enthusiastic',
            'key_messages' => [
                'Excitement about the role',
                'Commitment to company success',
                'Value you bring to the team',
                'Fair compensation expectations'
            ],
            'fallback_positions' => [
                'Focus on benefits and equity',
                'Consider performance-based compensation',
                'Negotiate for professional development opportunities'
            ]
        ];
        
        // Adjust based on personal situation
        if (isset($personalInfo['multiple_offers']) && $personalInfo['multiple_offers']) {
            $strategy['primary_approach'] = 'competitive';
            $strategy['key_messages'][] = 'Other offer considerations';
        }
        
        if (isset($personalInfo['current_employment']) && !$personalInfo['current_employment']) {
            $strategy['primary_approach'] = 'flexible';
            $strategy['key_messages'][] = 'Open to finding the right fit';
        }
        
        return $strategy;
    }
    
    /**
     * Select negotiation tactics
     */
    private function selectTactics(array $offer, array $personalInfo): array
    {
        $tactics = [
            'opening' => 'express enthusiasm and gratitude',
            'salary_discussion' => 'use market data and value proposition',
            'benefits_focus' => 'emphasize total compensation',
            'closing' => 'seek mutual agreement'
        ];
        
        return $tactics;
    }
    
    /**
     * Generate talking points
     */
    private function generateTalkingPoints(array $offer, array $personalInfo): array
    {
        return [
            'opening' => [
                'Thank you for the offer - I\'m really excited about this opportunity',
                'The role aligns perfectly with my skills and career goals',
                'I\'m impressed by the team and company culture'
            ],
            'value_proposition' => [
                'My X years of experience in PHP development',
                'Specific skills: Laravel, Symfony, database optimization',
                'Past achievements: [specific examples]',
                'Industry knowledge and connections'
            ],
            'salary_discussion' => [
                'Based on my research of similar roles in this area',
                'My experience and skill set justify a salary of $X',
                'I\'m confident I can bring significant value to justify this investment',
                'I\'m flexible on the total compensation package'
            ],
            'benefits_negotiation' => [
                'Could we discuss additional vacation days?',
                'Is there flexibility in the professional development budget?',
                'Could we consider a signing bonus to bridge the gap?',
                'What about performance-based bonuses?'
            ],
            'closing' => [
                'I\'m committed to making this work',
                'I\'m excited about the opportunity to contribute',
                'I\'m confident we can find a mutually beneficial arrangement',
                'When can we finalize the details?'
            ]
        ];
    }
    
    /**
     * Prepare negotiation scenarios
     */
    private function prepareScenarios(array $offer, array $personalInfo): array
    {
        return [
            'positive_response' => [
                'scenario' => 'They accept your counter-offer',
                'response' => 'Express gratitude and confirm details in writing',
                'next_steps' => 'Request written offer letter and start date'
            ],
            'partial_acceptance' => [
                'scenario' => 'They meet you halfway',
                'response' => 'Express appreciation and confirm the agreement',
                'next_steps' => 'Get all details in writing and prepare for start'
            ],
            'firm_position' => [
                'scenario' => 'They say the offer is firm',
                'response' => 'Acknowledge constraint, discuss alternative compensation',
                'next_steps' => 'Focus on benefits, equity, or performance-based compensation'
            ],
            'withdrawal' => [
                'scenario' => 'They withdraw the offer',
                'response' => 'Stay professional, express gratitude for consideration',
                'next_steps' => 'Continue job search, follow up for future opportunities'
            ]
        ];
    }
    
    /**
     * Plan follow-up
     */
    private function planFollowUp(array $offer, array $personalInfo): array
    {
        return [
            'immediate' => [
                'action' => 'Send thank-you email after negotiation',
                'content' => 'Express gratitude, summarize key points, confirm next steps',
                'timing' => 'Within 24 hours'
            ],
            'follow_up' => [
                'action' => 'Follow up if no response',
                'content' => 'Gentle reminder of your interest and next steps',
                'timing' => '3-5 business days'
            ],
            'final' => [
                'action' => 'Final confirmation and acceptance',
                'content' => 'Confirm final offer details and acceptance',
                'timing' => 'Upon receiving final offer'
            ]
        ];
    }
    
    /**
     * Generate negotiation script
     */
    public function generateNegotiationScript(array $plan, string $scenario): string
    {
        $script = "Negotiation Script: $scenario\n";
        $script .= str_repeat("=", 40) . "\n\n";
        
        $script .= "Opening:\n";
        foreach ($plan['talking_points']['opening'] as $point) {
            $script .= "  \"$point\"\n";
        }
        
        $script .= "\nValue Proposition:\n";
        foreach ($plan['talking_points']['value_proposition'] as $point) {
            $script .= "  \"$point\"\n";
        }
        
        $script .= "\nSalary Discussion:\n";
        foreach ($plan['talking_points']['salary_discussion'] as $point) {
            $script .= "  \"$point\"\n";
        }
        
        $script .= "\nBenefits Negotiation:\n";
        foreach ($plan['talking_points']['benefits_negotiation'] as $point) {
            $script .= "  \"$point\"\n";
        }
        
        $script .= "\nClosing:\n";
        foreach ($plan['talking_points']['closing'] as $point) {
            $script .= "  \"$point\"\n";
        }
        
        return $script;
    }
    
    /**
     * Get negotiation tips
     */
    public function getNegotiationTips(): array
    {
        return [
            'preparation' => [
                'Research market rates thoroughly',
                'Know your minimum acceptable offer',
                'Prepare specific examples of your value',
                'Practice your negotiation points',
                'Have alternative options ready'
            ],
            'timing' => [
                'Wait for written offer before negotiating',
                'Take time to consider the offer',
                'Don\'t respond immediately under pressure',
                'Choose the right time to negotiate',
                'Be patient but persistent'
            ],
            'communication' => [
                'Be confident but humble',
                'Use specific data and examples',
                'Listen more than you talk',
                'Ask clarifying questions',
                'Maintain professional tone'
            ],
            'strategy' => [
                'Focus on mutual benefit',
                'Be creative with solutions',
                'Consider total compensation',
                'Know when to compromise',
                'Always leave the door open'
            ]
        ];
    }
    
    /**
     * Get common mistakes to avoid
     */
    public function getCommonMistakes(): array
    {
        return $this->commonMistakes;
    }
}

// Career Advancement Planner
class CareerAdvancementPlanner
{
    private array $careerPaths = [];
    private array $skillProgression = [];
    private array $advancementMetrics = [];
    
    public function __construct()
    {
        $this->initializeCareerPaths();
        $this->initializeSkillProgression();
        $this->initializeAdvancementMetrics();
    }
    
    /**
     * Initialize career paths
     */
    private function initializeCareerPaths(): void
    {
        $this->careerPaths = [
            'technical_track' => [
                'junior_developer' => [
                    'duration' => '1-2 years',
                    'skills_required' => ['PHP fundamentals', 'Basic OOP', 'SQL', 'Git'],
                    'responsibilities' => 'Code implementation, bug fixes, documentation',
                    'next_role' => 'mid_level_developer',
                    'salary_increase' => '20-30%'
                ],
                'mid_level_developer' => [
                    'duration' => '2-3 years',
                    'skills_required' => ['Advanced OOP', 'Framework expertise', 'Testing', 'Performance optimization'],
                    'responsibilities' => 'Feature development, code reviews, mentoring',
                    'next_role' => 'senior_developer',
                    'salary_increase' => '25-35%'
                ],
                'senior_developer' => [
                    'duration' => '3-5 years',
                    'skills_required' => ['Architecture', 'System design', 'Leadership', 'Business acumen'],
                    'responsibilities' => 'Technical leadership, architecture decisions, strategic planning',
                    'next_role' => 'lead_developer',
                    'salary_increase' => '30-40%'
                ],
                'lead_developer' => [
                    'duration' => '4-6 years',
                    'skills_required' => ['Team management', 'Project management', 'Technical direction', 'Communication'],
                    'responsibilities' => 'Team leadership, technical strategy, resource management',
                    'next_role' => 'architect',
                    'salary_increase' => '35-50%'
                ],
                'architect' => [
                    'duration' => '5+ years',
                    'skills_required' => ['System architecture', 'Enterprise design', 'Strategic thinking', 'Cross-functional knowledge'],
                    'responsibilities' => 'System architecture, technology strategy, innovation',
                    'next_role' => 'cto/vp_engineering',
                    'salary_increase' => '40-60%'
                ]
            ],
            'management_track' => [
                'team_lead' => [
                    'duration' => '2-3 years',
                    'skills_required' => ['Leadership', 'Communication', 'Project management', 'Technical expertise'],
                    'responsibilities' => 'Team coordination, project oversight, mentoring',
                    'next_role' => 'engineering_manager',
                    'salary_increase' => '25-35%'
                ],
                'engineering_manager' => [
                    'duration' => '3-5 years',
                    'skills_required' => ['People management', 'Budget management', 'Strategic planning', 'Business metrics'],
                    'responsibilities' => 'Team management, resource allocation, performance management',
                    'next_role' => 'director',
                    'salary_increase' => '30-45%'
                ],
                'director' => [
                    'duration' => '5-7 years',
                    'skills_required' => ['Leadership', 'Business strategy', 'Financial management', 'Organizational design'],
                    'responsibilities' => 'Department leadership, strategic planning, budget oversight',
                    'next_role' => 'vp_engineering',
                    'salary_increase' => '40-60%'
                ],
                'vp_engineering' => [
                    'duration' => '5+ years',
                    'skills_required' => ['Executive leadership', 'Business acumen', 'Industry knowledge', 'Innovation'],
                    'responsibilities' => 'Technology strategy, team leadership, business alignment',
                    'next_role' => 'cto',
                    'salary_increase' => '50-100%'
                ]
            ],
            'specialist_track' => [
                'security_specialist' => [
                    'duration' => '3-5 years',
                    'skills_required' => ['Security principles', 'Compliance', 'Risk assessment', 'Security tools'],
                    'responsibilities' => 'Security implementation, risk analysis, compliance',
                    'next_role' => 'security_architect',
                    'salary_increase' => '35-50%'
                ],
                'performance_specialist' => [
                    'duration' => '3-5 years',
                    'skills_required' => ['Performance optimization', 'Monitoring', 'Caching', 'Database tuning'],
                    'responsibilities' => 'Performance analysis, optimization, monitoring',
                    'next_role' => 'performance_architect',
                    'salary_increase' => '35-50%'
                ],
                'devops_specialist' => [
                    'duration' => '3-5 years',
                    'skills_required' => ['DevOps practices', 'CI/CD', 'Cloud services', 'Infrastructure'],
                    'responsibilities' => 'Deployment automation, infrastructure management, monitoring',
                    'next_role' => 'devops_architect',
                    'salary_increase' => '40-60%'
                ]
            ]
        ];
    }
    
    /**
     * Initialize skill progression
     */
    private function initializeSkillProgression(): void
    {
        $this->skillProgression = [
            'technical_skills' => [
                'beginner' => [
                    'skills' => ['Basic PHP syntax', 'Simple CRUD operations', 'Basic HTML/CSS'],
                    'timeline' => '0-6 months',
                    'learning_resources' => ['PHP Manual', 'Online tutorials', 'Practice projects']
                ],
                'intermediate' => [
                    'skills' => ['OOP concepts', 'Framework basics', 'Database design', 'API development'],
                    'timeline' => '6-18 months',
                    'learning_resources' => ['Framework documentation', 'Advanced tutorials', 'Real projects']
                ],
                'advanced' => [
                    'skills' => ['System design', 'Architecture patterns', 'Performance optimization', 'Security'],
                    'timeline' => '2-4 years',
                    'learning_resources' => ['Architecture books', 'Conference talks', 'Open source']
                ],
                'expert' => [
                    'skills' => ['Enterprise architecture', 'Strategic planning', 'Innovation', 'Leadership'],
                    'timeline' => '5+ years',
                    'learning_resources' => ['Executive education', 'Industry conferences', 'Mentoring']
                ]
            ],
            'soft_skills' => [
                'foundation' => [
                    'skills' => ['Communication', 'Teamwork', 'Time management', 'Problem solving'],
                    'timeline' => '0-1 year',
                    'development' => ['Daily practice', 'Team projects', 'Feedback seeking']
                ],
                'professional' => [
                    'skills' => ['Leadership', 'Mentoring', 'Presentation', 'Negotiation'],
                    'timeline' => '2-3 years',
                    'development' => ['Leadership roles', 'Public speaking', 'Negotiation practice']
                ],
                'strategic' => [
                    'skills' => ['Strategic thinking', 'Business acumen', 'Innovation', 'Executive presence'],
                    'timeline' => '4-6 years',
                    'development' => ['Business courses', 'Executive training', 'Board exposure']
                ]
            ]
        ];
    }
    
    /**
     * Initialize advancement metrics
     */
    private function initializeAdvancementMetrics(): void
    {
        $this->advancementMetrics = [
            'technical_metrics' => [
                'code_quality' => [
                    'description' => 'Code review scores, bug rates, maintainability',
                    'measurement' => 'Code review ratings, bug tracking metrics',
                    'target' => 'Consistently high ratings, low bug rates'
                ],
                'technical_depth' => [
                    'description' => 'Complexity of problems solved, technical innovations',
                    'measurement' => 'Project complexity, patents, innovations',
                    'target' => 'Leading complex projects, technical innovations'
                ],
                'knowledge_sharing' => [
                    'description' => 'Documentation, mentoring, presentations',
                    'measurement' => 'Documentation quality, mentee success, speaking engagements',
                    'target' => 'Recognized expert, successful mentor'
                ]
            ],
            'business_metrics' => [
                'impact' => [
                    'description' => 'Business impact of technical decisions',
                    'measurement' => 'ROI, cost savings, revenue generation',
                    'target' => 'Measurable positive business impact'
                ],
                'collaboration' => [
                    'description' => 'Cross-functional collaboration and influence',
                    'measurement' => 'Project success, team feedback, stakeholder satisfaction',
                    'target' => 'Recognized collaborator, trusted advisor'
                ],
                'leadership' => [
                    'description' => 'Team leadership and development',
                    'measurement' => 'Team performance, retention, promotion rates',
                    'target' => 'High-performing teams, successful promotions'
                ]
            ],
            'personal_metrics' => [
                'learning' => [
                    'description' => 'Continuous learning and skill development',
                    'measurement' => 'Certifications, courses, new skills acquired',
                    'target' => 'Continuous skill development, recognized expertise'
                ],
                'networking' => [
                    'description' => 'Professional network and industry presence',
                    'measurement' => 'Network size, speaking engagements, industry recognition',
                    'target' => 'Strong professional network, industry recognition'
                ],
                'reputation' => [
                    'description' => 'Professional reputation and brand',
                    'measurement' => 'Recommendations, references, industry reputation',
                    'target' => 'Excellent professional reputation'
                ]
            ]
        ];
    }
    
    /**
     * Create career advancement plan
     */
    public function createAdvancementPlan(array $profile): array
    {
        $plan = [
            'current_assessment' => $this->assessCurrentPosition($profile),
            'career_path' => $this->recommendCareerPath($profile),
            'skill_development' => $this->createSkillDevelopmentPlan($profile),
            'timeline' => $this->createAdvancementTimeline($profile),
            'metrics' => $this->defineSuccessMetrics($profile),
            'resources' => $this->identifyResources($profile)
        ];
        
        return $plan;
    }
    
    /**
     * Assess current position
     */
    private function assessCurrentPosition(array $profile): array
    {
        return [
            'current_role' => $profile['current_role'] ?? 'junior_developer',
            'experience_years' => $profile['experience_years'] ?? 0,
            'skill_level' => $profile['skill_level'] ?? 'intermediate',
            'strengths' => $profile['strengths'] ?? [],
            'areas_for_improvement' => $profile['areas_for_improvement'] ?? [],
            'career_satisfaction' => $profile['career_satisfaction'] ?? 7,
            'readiness_for_advancement' => $this->calculateReadinessScore($profile)
        ];
    }
    
    /**
     * Calculate readiness score
     */
    private function calculateReadinessScore(array $profile): array
    {
        $score = 0;
        $factors = [];
        
        // Experience factor
        $experience = $profile['experience_years'] ?? 0;
        if ($experience >= 2) $score += 25;
        $factors['experience'] = min($experience * 5, 25);
        
        // Skills factor
        $skills = $profile['skills'] ?? [];
        $skillScore = min(count($skills) * 5, 25);
        $score += $skillScore;
        $factors['skills'] = $skillScore;
        
        // Performance factor
        $performance = $profile['performance_rating'] ?? 3;
        $performanceScore = $performance * 5;
        $score += $performanceScore;
        $factors['performance'] = $performanceScore;
        
        // Leadership factor
        $leadership = $profile['leadership_experience'] ?? false;
        $leadershipScore = $leadership ? 25 : 0;
        $score += $leadershipScore;
        $factors['leadership'] = $leadershipScore;
        
        return [
            'total_score' => $score,
            'factors' => $factors,
            'readiness_level' => $this->getReadinessLevel($score)
        ];
    }
    
    /**
     * Get readiness level
     */
    private function getReadinessLevel(int $score): string
    {
        if ($score >= 80) return 'Ready for advancement';
        if ($score >= 60) return 'Nearly ready';
        if ($score >= 40) return 'Developing';
        return 'Needs more experience';
    }
    
    /**
     * Recommend career path
     */
    private function recommendCareerPath(array $profile): array
    {
        $currentRole = $profile['current_role'] ?? 'junior_developer';
        $interests = $profile['interests'] ?? ['technical'];
        $strengths = $profile['strengths'] ?? [];
        
        $recommendations = [];
        
        // Technical track
        if (in_array('technical', $interests) || in_array('coding', $strengths)) {
            $recommendations[] = [
                'track' => 'technical_track',
                'reason' => 'Strong technical skills and interests',
                'next_role' => $this->getNextRole($currentRole, 'technical_track'),
                'timeline' => $this->getTimelineToNextRole($currentRole, 'technical_track')
            ];
        }
        
        // Management track
        if (in_array('leadership', $interests) || in_array('mentoring', $strengths)) {
            $recommendations[] = [
                'track' => 'management_track',
                'reason' => 'Leadership interests and mentoring strengths',
                'next_role' => $this->getNextRole($currentRole, 'management_track'),
                'timeline' => $this->getTimelineToNextRole($currentRole, 'management_track')
            ];
        }
        
        // Specialist track
        if (in_array('specialization', $interests)) {
            $recommendations[] = [
                'track' => 'specialist_track',
                'reason' => 'Interest in specialized areas',
                'next_role' => $this->getNextRole($currentRole, 'specialist_track'),
                'timeline' => $this->getTimelineToNextRole($currentRole, 'specialist_track')
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Get next role in career path
     */
    private function getNextRole(string $currentRole, string $track): string
    {
        $paths = $this->careerPaths[$track] ?? [];
        
        foreach ($paths as $role => $details) {
            if ($role === $currentRole) {
                return $details['next_role'];
            }
        }
        
        return $currentRole;
    }
    
    /**
     * Get timeline to next role
     */
    private function getTimelineToNextRole(string $currentRole, string $track): string
    {
        $paths = $this->careerPaths[$track] ?? [];
        
        foreach ($paths as $role => $details) {
            if ($role === $currentRole) {
                return $details['duration'];
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Create skill development plan
     */
    private function createSkillDevelopmentPlan(array $profile): array
    {
        $plan = [
            'current_skills' => $profile['skills'] ?? [],
            'target_skills' => $this->identifyTargetSkills($profile),
            'learning_resources' => $this->getLearningResources($profile),
            'practice_projects' => $this->suggestPracticeProjects($profile),
            'timeline' => $this->createSkillTimeline($profile)
        ];
        
        return $plan;
    }
    
    /**
     * Identify target skills
     */
    private function identifyTargetSkills(array $profile): array
    {
        $currentRole = $profile['current_role'] ?? 'junior_developer';
        $nextRole = $this->getNextRole($currentRole, 'technical_track');
        
        $targetSkills = [
            'php_advanced' => 'Advanced PHP concepts and patterns',
            'framework_mastery' => 'Deep expertise in chosen framework',
            'architecture' => 'System design and architecture',
            'leadership' => 'Team leadership and management',
            'business_acumen' => 'Business understanding and strategy'
        ];
        
        return $targetSkills;
    }
    
    /**
     * Get learning resources
     */
    private function getLearningResources(array $profile): array
    {
        return [
            'online_courses' => [
                'Laravel official documentation',
                'SymfonyCasts',
                'Pluralsight PHP courses',
                'Coursera computer science courses'
            ],
            'books' => [
                'Clean Code by Robert C. Martin',
                'Design Patterns by Gang of Four',
                'Clean Architecture by Robert C. Martin',
                'The Pragmatic Programmer'
            ],
            'practice_platforms' => [
                'LeetCode for coding challenges',
                'HackerRank for problem solving',
                'GitHub for open source contributions',
                'Stack Overflow for community learning'
            ],
            'certifications' => [
                'Zend Certified PHP Engineer',
                'AWS Certified Developer',
                'Docker Certified Associate'
            ]
        ];
    }
    
    /**
     * Suggest practice projects
     */
    private function suggestPracticeProjects(array $profile): array
    {
        return [
            'beginner' => [
                'Personal blog system',
                'Todo list application',
                'Weather API integration',
                'Simple CRUD application'
            ],
            'intermediate' => [
                'E-commerce platform',
                'RESTful API service',
                'Content management system',
                'Real-time chat application'
            ],
            'advanced' => [
                'Microservices architecture',
                'Performance monitoring system',
                'Security audit tool',
                'Scalable web application'
            ]
        ];
    }
    
    /**
     * Create skill timeline
     */
    private function createSkillTimeline(array $profile): array
    {
        return [
            '3_months' => [
                'Master current framework',
                'Improve database skills',
                'Learn testing practices'
            ],
            '6_months' => [
                'Study design patterns',
                'Learn security best practices',
                'Contribute to open source'
            ],
            '1_year' => [
                'Master system design',
                'Develop leadership skills',
                'Build portfolio projects'
            ],
            '2_years' => [
                'Specialize in chosen area',
                'Mentor junior developers',
                'Speak at conferences'
            ]
        ];
    }
    
    /**
     * Create advancement timeline
     */
    private function createAdvancementTimeline(array $profile): array
    {
        return [
            'short_term' => [
                'period' => '3-6 months',
                'goals' => [
                    'Master current role responsibilities',
                    'Develop key skills for next role',
                    'Build strong relationships'
                ],
                'metrics' => [
                    'Performance rating > 4',
                    'Complete 2-3 key projects',
                    'Receive positive feedback'
                ]
            ],
            'medium_term' => [
                'period' => '1-2 years',
                'goals' => [
                    'Take on leadership responsibilities',
                    'Mentor team members',
                    'Develop business acumen'
                ],
                'metrics' => [
                    'Lead successful projects',
                    'Mentor 2-3 junior developers',
                    'Contribute to strategic decisions'
                ]
            ],
            'long_term' => [
                'period' => '3-5 years',
                'goals' => [
                    'Achieve next role promotion',
                    'Establish industry presence',
                    'Develop strategic thinking'
                ],
                'metrics' => [
                    'Promotion to next role',
                    'Speaking engagements',
                    'Industry recognition'
                ]
            ]
        ];
    }
    
    /**
     * Define success metrics
     */
    private function defineSuccessMetrics(array $profile): array
    {
        return [
            'technical_excellence' => [
                'Code quality metrics',
                'Project success rates',
                'Technical innovation',
                'Knowledge sharing'
            ],
            'business_impact' => [
                'ROI contributions',
                'Cost savings',
                'Revenue generation',
                'Customer satisfaction'
            ],
            'leadership_effectiveness' => [
                'Team performance',
                'Employee retention',
                'Mentoring success',
                'Cross-functional influence'
            ],
            'personal_growth' => [
                'Skill development',
                'Network expansion',
                'Industry recognition',
                'Career advancement'
            ]
        ];
    }
    
    /**
     * Identify resources
     */
    private function identifyResources(array $profile): array
    {
        return [
            'internal' => [
                'Mentoring programs',
                'Training budgets',
                'Conference attendance',
                'Internal mobility programs'
            ],
            'external' => [
                'Professional organizations',
                'Industry conferences',
                'Online communities',
                'Professional certifications'
            ],
            'network' => [
                'Industry connections',
                'Professional associations',
                'Alumni networks',
                'Social media presence'
            ]
        ];
    }
    
    /**
     * Generate comprehensive advancement report
     */
    public function generateAdvancementReport(array $profile): string
    {
        $plan = $this->createAdvancementPlan($profile);
        
        $report = "Career Advancement Plan\n";
        $report .= str_repeat("=", 25) . "\n\n";
        
        // Current Assessment
        $report .= "Current Assessment:\n";
        $report .= str_repeat("-", 19) . "\n";
        $report .= "Current Role: {$plan['current_assessment']['current_role']}\n";
        $report .= "Experience: {$plan['current_assessment']['experience_years']} years\n";
        $report .= "Skill Level: {$plan['current_assessment']['skill_level']}\n";
        $report .= "Readiness: {$plan['current_assessment']['readiness_for_advancement']['readiness_level']}\n";
        $report .= "Readiness Score: {$plan['current_assessment']['readiness_for_advancement']['total_score']}/100\n\n";
        
        // Career Path Recommendations
        $report .= "Career Path Recommendations:\n";
        $report .= str_repeat("-", 28) . "\n";
        foreach ($plan['career_path'] as $recommendation) {
            $report .= "Track: {$recommendation['track']}\n";
            $report .= "Reason: {$recommendation['reason']}\n";
            $report .= "Next Role: {$recommendation['next_role']}\n";
            $report .= "Timeline: {$recommendation['timeline']}\n\n";
        }
        
        // Skill Development
        $report .= "Skill Development Plan:\n";
        $report .= str_repeat("-", 21) . "\n";
        $report .= "Target Skills:\n";
        foreach ($plan['skill_development']['target_skills'] as $skill => $description) {
            $report .= "  • $skill: $description\n";
        }
        $report .= "\nLearning Resources:\n";
        foreach ($plan['skill_development']['learning_resources']['online_courses'] as $resource) {
            $report .= "  • $resource\n";
        }
        $report .= "\n";
        
        // Timeline
        $report .= "Advancement Timeline:\n";
        $report .= str_repeat("-", 21) . "\n";
        foreach ($plan['timeline']['short_term']['goals'] as $goal) {
            $report .= "Short Term (3-6 months): $goal\n";
        }
        foreach ($plan['timeline']['medium_term']['goals'] as $goal) {
            $report .= "Medium Term (1-2 years): $goal\n";
        }
        foreach ($plan['timeline']['long_term']['goals'] as $goal) {
            $report .= "Long Term (3-5 years): $goal\n";
        }
        
        return $report;
    }
}

// Salary and Negotiation Examples
class SalaryNegotiationExamples
{
    private SalaryResearchAnalyzer $analyzer;
    private NegotiationCoach $coach;
    private CareerAdvancementPlanner $planner;
    
    public function __construct()
    {
        $this->analyzer = new SalaryResearchAnalyzer();
        $this->coach = new NegotiationCoach();
        $this->planner = new CareerAdvancementPlanner();
    }
    
    public function demonstrateSalaryAnalysis(): void
    {
        echo "Salary Analysis Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Analyze different scenarios
        $scenarios = [
            [
                'role' => 'mid_level_developer',
                'region' => 'united_states',
                'location' => 'san_francisco',
                'experience' => 5,
                'industry' => 'technology',
                'company_size' => 'medium'
            ],
            [
                'role' => 'senior_developer',
                'region' => 'europe',
                'location' => 'london',
                'experience' => 8,
                'industry' => 'fintech',
                'company_size' => 'large'
            ],
            [
                'role' => 'lead_developer',
                'region' => 'asia',
                'location' => 'singapore',
                'experience' => 10,
                'industry' => 'ecommerce',
                'company_size' => 'startup'
            ]
        ];
        
        foreach ($scenarios as $index => $scenario) {
            echo "\nScenario " . ($index + 1) . ":\n";
            $analysis = $this->analyzer->analyzeSalary($scenario);
            
            echo "Role: {$analysis['role']}\n";
            echo "Location: {$analysis['location']}\n";
            echo "Base Salary: \${analysis['adjusted_salary']}\n";
            echo "Total Compensation: \${analysis['total_compensation']['total']}\n";
            echo "Market Position: {$analysis['market_position']}\n";
            echo "Negotiation Range: \${analysis['negotiation_range']['conservative']} - \${analysis['negotiation_range']['aggressive']}\n\n";
        }
    }
    
    public function demonstrateOfferComparison(): void
    {
        echo "\nOffer Comparison Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $offers = [
            [
                'company' => 'TechCorp',
                'base_salary' => 120000,
                'bonus' => 10000,
                'equity' => 20000,
                'health_insurance' => true,
                'retirement_401k' => true,
                'paid_time_off' => 20,
                'remote_work' => true,
                'training_budget' => 2000
            ],
            [
                'company' => 'StartupXYZ',
                'base_salary' => 110000,
                'bonus' => 15000,
                'equity' => 50000,
                'health_insurance' => true,
                'retirement_401k' => false,
                'paid_time_off' => 25,
                'remote_work' => true,
                'training_budget' => 5000
            ],
            [
                'company' => 'EnterpriseCo',
                'base_salary' => 130000,
                'bonus' => 20000,
                'equity' => 30000,
                'health_insurance' => true,
                'retirement_401k' => true,
                'paid_time_off' => 22,
                'remote_work' => false,
                'training_budget' => 3000
            ]
        ];
        
        $comparison = $this->analyzer->compareOffers($offers);
        
        echo "Offer Comparison Results:\n";
        foreach ($comparison as $index => $offer) {
            echo "\n{$index + 1}. {$offer['company']}\n";
            echo "   Base Salary: \${offer['base_salary']}\n";
            echo "   Total Compensation: \${offer['total_compensation']}\n";
            echo "   Market Position: {$offer['market_position']}\n";
            echo "   Overall Score: " . round($offer['overall_score'], 2) . "\n";
        }
        
        echo "\nRecommendation:\n";
        echo "Best offer: " . $comparison[0]['company'] . "\n";
        echo "Reason: Highest overall score with good balance of compensation and benefits\n";
    }
    
    public function demonstrateNegotiationPlanning(): void
    {
        echo "\nNegotiation Planning Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Sample offer and personal info
        $offer = [
            'company' => 'TechCorp',
            'base_salary' => 100000,
            'bonus' => 5000,
            'equity' => 10000,
            'location' => 'San Francisco',
            'role' => 'Mid-Level PHP Developer'
        ];
        
        $personalInfo = [
            'experience' => 5,
            'skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript'],
            'current_salary' => 95000,
            'multiple_offers' => true,
            'current_employment' => true
        ];
        
        // Generate negotiation plan
        $plan = $this->coach->generateNegotiationPlan($offer, $personalInfo);
        
        echo "Negotiation Strategy:\n";
        echo "Primary Approach: {$plan['strategy']['primary_approach']}\n";
        echo "Tone: {$plan['strategy']['tone']}\n";
        echo "Key Messages:\n";
        foreach ($plan['strategy']['key_messages'] as $message) {
            echo "  • $message\n";
        }
        
        echo "\nPreparation Tasks:\n";
        foreach ($plan['preparation'] as $task => $details) {
            echo "$task:\n";
            echo "  Task: {$details['task']}\n";
            echo "  Timeline: {$details['timeline']}\n";
            echo "  Resources: " . implode(', ', $details['resources']) . "\n\n";
        }
        
        // Generate negotiation script
        $script = $this->coach->generateNegotiationScript($plan, 'standard');
        echo "Negotiation Script:\n";
        echo substr($script, 0, 500) . "...\n";
    }
    
    public function demonstrateCareerAdvancement(): void
    {
        echo "\nCareer Advancement Planning\n";
        echo str_repeat("-", 30) . "\n";
        
        // Sample profile
        $profile = [
            'current_role' => 'mid_level_developer',
            'experience_years' => 5,
            'skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Docker'],
            'strengths' => ['coding', 'problem_solving', 'mentoring'],
            'areas_for_improvement' => ['architecture', 'leadership'],
            'interests' => ['technical', 'leadership'],
            'performance_rating' => 4,
            'leadership_experience' => false
        ];
        
        // Generate advancement plan
        $plan = $this->planner->createAdvancementPlan($profile);
        
        echo "Current Assessment:\n";
        echo "Role: {$plan['current_assessment']['current_role']}\n";
        echo "Experience: {$plan['current_assessment']['experience_years']} years\n";
        echo "Readiness: {$plan['current_assessment']['readiness_for_advancement']['readiness_level']}\n";
        echo "Readiness Score: {$plan['current_assessment']['readiness_for_advancement']['total_score']}/100\n\n";
        
        echo "Career Path Recommendations:\n";
        foreach ($plan['career_path'] as $recommendation) {
            echo "Track: {$recommendation['track']}\n";
            echo "Next Role: {$recommendation['next_role']}\n";
            echo "Timeline: {$recommendation['timeline']}\n";
            echo "Reason: {$recommendation['reason']}\n\n";
        }
        
        echo "Skill Development Target:\n";
        foreach ($plan['skill_development']['target_skills'] as $skill => $description) {
            echo "• $skill: $description\n";
        }
        
        echo "\nAdvancement Timeline:\n";
        echo "Short Term (3-6 months):\n";
        foreach ($plan['timeline']['short_term']['goals'] as $goal) {
            echo "  • $goal\n";
        }
        
        echo "\nMedium Term (1-2 years):\n";
        foreach ($plan['timeline']['medium_term']['goals'] as $goal) {
            echo "  • $goal\n";
        }
        
        echo "\nLong Term (3-5 years):\n";
        foreach ($plan['timeline']['long_term']['goals'] as $goal) {
            echo "  • $goal\n";
        }
        
        // Generate comprehensive report
        $report = $this->planner->generateAdvancementReport($profile);
        echo "\n" . substr($report, 0, 500) . "...\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nSalary and Negotiation Best Practices\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "Salary Research:\n";
        echo "  • Research market rates thoroughly\n";
        echo "  • Consider total compensation\n";
        echo "  • Account for location differences\n";
        echo "  • Factor in experience level\n";
        echo "  • Use multiple data sources\n";
        echo "  • Consider industry differences\n\n";
        
        echo "Negotiation Preparation:\n";
        echo "  • Know your minimum acceptable offer\n";
        echo "  • Prepare specific examples of value\n";
        echo "  • Research the company thoroughly\n";
        echo "  • Practice negotiation scenarios\n";
        echo "  • Have alternative options ready\n";
        echo "  • Prepare talking points\n\n";
        
        echo "Negotiation Execution:\n";
        echo "  • Wait for written offer\n";
        echo "  • Express enthusiasm first\n";
        echo "  • Use collaborative approach\n";
        echo "  • Be specific with requests\n";
        echo "  • Use data to support claims\n";
        echo "  • Consider total compensation\n";
        echo "  • Be patient but persistent\n\n";
        
        echo "Career Advancement:\n";
        echo "  • Set clear career goals\n";
        echo "  • Develop relevant skills\n";
        echo "  • Build strong relationships\n";
        echo "  • Take on leadership roles\n";
        echo "  • Mentor junior developers\n";
        echo "  • Contribute to strategic decisions\n";
        echo "  • Establish industry presence\n";
        echo "  • Continuously learn and grow";
    }
    
    public function runAllExamples(): void
    {
        echo "Salary and Negotiation Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateSalaryAnalysis();
        $this->demonstrateOfferComparison();
        $this->demonstrateNegotiationPlanning();
        $this->demonstrateCareerAdvancement();
        $this->demonstrateBestPractices();
    }
}

// Salary and Negotiation Best Practices
function printSalaryNegotiationBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Salary and Negotiation Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Salary Research:\n";
    echo "   • Use multiple data sources\n";
    echo "   • Consider total compensation\n";
    echo "   • Account for location factors\n";
    echo "   • Research industry standards\n";
    echo "   • Consider company size\n";
    echo "   • Factor in experience level\n\n";
    
    echo "2. Negotiation Preparation:\n";
    echo "   • Know your worth\n";
    echo "   • Prepare specific examples\n";
    echo "   • Research the company\n";
    echo "   • Practice your approach\n";
    echo "   • Have alternatives ready\n";
    echo "   • Set minimum requirements\n\n";
    
    echo "3. Negotiation Tactics:\n";
    echo "   • Use collaborative approach\n";
    echo "   • Focus on mutual benefit\n";
    echo "   • Be specific and data-driven\n";
    echo "   • Consider total compensation\n";
    echo "   • Be creative with solutions\n";
    echo "   • Know when to compromise\n\n";
    
    echo "4. Career Advancement:\n";
    echo "   • Set clear goals\n";
    echo "   • Develop relevant skills\n";
    echo "   • Build strong network\n";
    echo "   • Take leadership roles\n";
    echo "   • Mentor others\n";
    echo "   • Continuously learn\n";
    echo "   • Track achievements\n\n";
    
    echo "5. Common Mistakes to Avoid:\n";
    echo "   • Accepting first offer\n";
    echo "   • Focusing only on salary\n";
    echo "   • Making ultimatums\n";
    echo "   • Being too aggressive\n";
    echo "   • Not doing research\n";
    echo "   • Having no alternatives";
}

// Main execution
function runSalaryNegotiationDemo(): void
{
    $examples = new SalaryNegotiationExamples();
    $examples->runAllExamples();
    printSalaryNegotiationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSalaryNegotiationDemo();
}
?>
