<?php
/**
 * Code Quality and Refactoring
 * 
 * This file demonstrates clean code practices, refactoring techniques,
 * code quality metrics, and maintainability improvements.
 */

// Clean Code Example
class UserProcessor
{
    private UserRepository $repository;
    private EmailValidator $emailValidator;
    private Logger $logger;
    
    public function __construct(
        UserRepository $repository,
        EmailValidator $emailValidator,
        Logger $logger
    ) {
        $this->repository = $repository;
        $this->emailValidator = $emailValidator;
        $this->logger = $logger;
    }
    
    /**
     * Process user registration with validation and logging
     *
     * @param array $userData User data array with 'name' and 'email' keys
     *
     * @return User Created user entity
     *
     * @throws InvalidArgumentException When validation fails
     * @throws RuntimeException When user creation fails
     */
    public function processUserRegistration(array $userData): User
    {
        $this->validateUserData($userData);
        
        $user = $this->createUser($userData);
        $this->logUserCreation($user);
        
        return $user;
    }
    
    /**
     * Validate user data
     *
     * @param array $userData User data to validate
     *
     * @throws InvalidArgumentException When validation fails
     */
    private function validateUserData(array $userData): void
    {
        if (empty($userData['name'])) {
            throw new InvalidArgumentException('Name is required');
        }
        
        if (strlen($userData['name']) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters');
        }
        
        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Email is required');
        }
        
        if (!$this->emailValidator->isValid($userData['email'])) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }
    
    /**
     * Create user in repository
     *
     * @param array $userData Validated user data
     *
     * @return User Created user
     *
     * @throws RuntimeException When creation fails
     */
    private function createUser(array $userData): User
    {
        try {
            return $this->repository->create($userData);
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to create user: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Log user creation
     *
     * @param User $user Created user
     */
    private function logUserCreation(User $user): void
    {
        $this->logger->info('User created successfully', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }
}

// Code Quality Metrics
class CodeQualityAnalyzer
{
    private array $metrics = [];
    
    public function analyzeClass(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        
        $this->metrics = [
            'class_name' => $className,
            'lines_of_code' => $this->countLinesOfCode($reflection),
            'cyclomatic_complexity' => $this->calculateCyclomaticComplexity($reflection),
            'class_coupling' => $this->calculateClassCoupling($reflection),
            'method_count' => count($reflection->getMethods()),
            'property_count' => count($reflection->getProperties()),
            'depth_of_inheritance' => $this->calculateInheritanceDepth($reflection),
            'weighted_methods_per_class' => $this->calculateWMC($reflection),
            'lack_of_cohesion' => $this->calculateLCOM($reflection),
            'maintainability_index' => $this->calculateMaintainabilityIndex($reflection)
        ];
        
        return $this->metrics;
    }
    
    private function countLinesOfCode(\ReflectionClass $reflection): int
    {
        $lines = 0;
        
        foreach ($reflection->getMethods() as $method) {
            $lines += $method->getEndLine() - $method->getStartLine() + 1;
        }
        
        return $lines;
    }
    
    private function calculateCyclomaticComplexity(\ReflectionClass $reflection): int
    {
        $complexity = 0;
        
        foreach ($reflection->getMethods() as $method) {
            $complexity += $this->getMethodComplexity($method);
        }
        
        return $complexity;
    }
    
    private function getMethodComplexity(\ReflectionMethod $method): int
    {
        $complexity = 1; // Base complexity
        
        $source = file_get_contents($method->getFileName());
        $lines = file($source);
        
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        
        for ($i = $startLine - 1; $i < $endLine; $i++) {
            $line = $lines[$i];
            
            // Count decision points
            if (preg_match('/\b(if|else|elseif|while|for|foreach|switch|case|catch)\b/', $line)) {
                $complexity++;
            }
            
            // Count logical operators
            $complexity += substr_count($line, '&&') + substr_count($line, '||');
        }
        
        return $complexity;
    }
    
    private function calculateClassCoupling(\ReflectionClass $reflection): int
    {
        $coupling = 0;
        
        foreach ($reflection->getMethods() as $method) {
            $coupling += $this->getMethodCoupling($method);
        }
        
        return $coupling;
    }
    
    private function getMethodCoupling(\ReflectionMethod $method): int
    {
        $coupling = 0;
        
        // Check parameter types
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType() && $parameter->getType()->getName() !== $method->getDeclaringClass()->getName()) {
                $coupling++;
            }
        }
        
        // Check return type
        if ($method->getReturnType() && $method->getReturnType()->getName() !== $method->getDeclaringClass()->getName()) {
            $coupling++;
        }
        
        return $coupling;
    }
    
    private function calculateInheritanceDepth(\ReflectionClass $reflection): int
    {
        $depth = 0;
        $parent = $reflection->getParentClass();
        
        while ($parent) {
            $depth++;
            $parent = $parent->getParentClass();
        }
        
        return $depth;
    }
    
    private function calculateWMC(\ReflectionClass $reflection): int
    {
        $wmc = 0;
        
        foreach ($reflection->getMethods() as $method) {
            $wmc += $this->getMethodComplexity($method);
        }
        
        return $wmc;
    }
    
    private function calculateLCOM(\ReflectionClass $reflection): float
    {
        $methods = $reflection->getMethods();
        $methodCount = count($methods);
        
        if ($methodCount <= 1) {
            return 0.0;
        }
        
        $nonCohesivePairs = 0;
        
        for ($i = 0; $i < $methodCount; $i++) {
            for ($j = $i + 1; $j < $methodCount; $j++) {
                if (!$this->methodsUseSameProperties($methods[$i], $methods[$j])) {
                    $nonCohesivePairs++;
                }
            }
        }
        
        $totalPairs = ($methodCount * ($methodCount - 1)) / 2;
        
        return $totalPairs > 0 ? $nonCohesivePairs / $totalPairs : 0.0;
    }
    
    private function methodsUseSameProperties(\ReflectionMethod $method1, \ReflectionMethod $method2): bool
    {
        // Simplified check - in real implementation, this would analyze property usage
        return true;
    }
    
    private function calculateMaintainabilityIndex(\ReflectionClass $reflection): float
    {
        $loc = $this->countLinesOfCode($reflection);
        $cyclomatic = $this->calculateCyclomaticComplexity($reflection);
        $volume = $loc * log10($loc + 1);
        
        if ($volume === 0) {
            return 100.0;
        }
        
        $maintainability = 171 - 5.2 * log($volume) - 0.23 * $cyclomatic - 16.2 * log($loc);
        
        return max(0, min(100, $maintainability));
    }
    
    public function getQualityReport(): array
    {
        $report = [
            'metrics' => $this->metrics,
            'recommendations' => $this->generateRecommendations(),
            'quality_score' => $this->calculateQualityScore()
        ];
        
        return $report;
    }
    
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        if ($this->metrics['cyclomatic_complexity'] > 20) {
            $recommendations[] = 'Consider reducing cyclomatic complexity by extracting methods';
        }
        
        if ($this->metrics['class_coupling'] > 10) {
            $recommendations[] = 'Consider reducing class coupling by using interfaces';
        }
        
        if ($this->metrics['method_count'] > 20) {
            $recommendations[] = 'Consider splitting large classes into smaller ones';
        }
        
        if ($this->metrics['maintainability_index'] < 70) {
            $recommendations[] = 'Consider refactoring to improve maintainability';
        }
        
        if ($this->metrics['lack_of_cohesion'] > 0.5) {
            $recommendations[] = 'Consider improving class cohesion';
        }
        
        return $recommendations;
    }
    
    private function calculateQualityScore(): float
    {
        $score = 100.0;
        
        // Deduct points for high complexity
        if ($this->metrics['cyclomatic_complexity'] > 20) {
            $score -= 20;
        }
        
        // Deduct points for high coupling
        if ($this->metrics['class_coupling'] > 10) {
            $score -= 15;
        }
        
        // Deduct points for low maintainability
        if ($this->metrics['maintainability_index'] < 70) {
            $score -= 25;
        }
        
        // Deduct points for low cohesion
        if ($this->metrics['lack_of_cohesion'] > 0.5) {
            $score -= 15;
        }
        
        return max(0, $score);
    }
}

// Refactoring Techniques
class RefactoringHelper
{
    /**
     * Extract Method Refactoring
     */
    public function extractMethod(string $originalMethod, array $extractedMethods): string
    {
        echo "Extract Method Refactoring:\n";
        echo "Original method: $originalMethod\n";
        echo "Extracted methods: " . implode(', ', $extractedMethods) . "\n";
        
        return "Refactored method with extracted calls to: " . implode(', ', $extractedMethods);
    }
    
    /**
     * Extract Class Refactoring
     */
    public function extractClass(string $className, array $responsibilities): string
    {
        echo "Extract Class Refactoring:\n";
        echo "Original class: $className\n";
        echo "Extracted responsibilities: " . implode(', ', $responsibilities) . "\n";
        
        return "New class created with responsibilities: " . implode(', ', $responsibilities);
    }
    
    /**
     * Replace Conditional with Polymorphism
     */
    public function replaceConditionalWithPolymorphism(array $conditions): string
    {
        echo "Replace Conditional with Polymorphism:\n";
        echo "Conditions to replace: " . implode(', ', $conditions) . "\n";
        
        return "Polymorphic solution created for: " . implode(', ', $conditions);
    }
    
    /**
     * Introduce Parameter Object
     */
    public function introduceParameterObject(array $parameters): string
    {
        echo "Introduce Parameter Object:\n";
        echo "Parameters to group: " . implode(', ', $parameters) . "\n";
        
        return "Parameter object created with: " . implode(', ', $parameters);
    }
    
    /**
     * Replace Magic Number with Symbolic Constant
     */
    public function replaceMagicNumber(float $number, string $constantName): string
    {
        echo "Replace Magic Number:\n";
        echo "Magic number: $number\n";
        echo "Constant name: $constantName\n";
        
        return "Constant defined: $constantName = $number";
    }
    
    /**
     * Decompose Conditional
     */
    public function decomposeConditional(string $complexCondition): array
    {
        echo "Decompose Conditional:\n";
        echo "Complex condition: $complexCondition\n";
        
        return [
            'condition1' => 'First part of condition',
            'condition2' => 'Second part of condition',
            'condition3' => 'Third part of condition'
        ];
    }
    
    /**
     * Replace Temporal Query with Query Object
     */
    public function replaceTemporalQuery(string $queryType, array $parameters): string
    {
        echo "Replace Temporal Query with Query Object:\n";
        echo "Query type: $queryType\n";
        echo "Parameters: " . implode(', ', $parameters) . "\n";
        
        return "Query object created for: $queryType";
    }
    
    /**
     * Introduce Null Object
     */
    public function introduceNullObject(string $className): string
    {
        echo "Introduce Null Object:\n";
        echo "Class to null: $className\n";
        
        return "Null object created for: $className";
    }
    
    /**
     * Replace Nested Conditional with Guard Clauses
     */
    public function replaceNestedConditional(array $conditions): string
    {
        echo "Replace Nested Conditional with Guard Clauses:\n";
        echo "Conditions to guard: " . implode(', ', $conditions) . "\n";
        
        return "Guard clauses implemented for: " . implode(', ', $conditions);
    }
}

// Code Smells Detection
class CodeSmellsDetector
{
    private array $smells = [];
    
    public function detectSmells(string $code): array
    {
        $this->smells = [];
        
        $this->detectLongMethod($code);
        $this->detectLargeClass($code);
        $this->detectLongParameterList($code);
        $this->detectDuplicatedCode($code);
        $this->detectComplexConditional($code);
        $this->detectMagicNumbers($code);
        $this->detectDeadCode($code);
        $this->detectSpeculativeGenerality($code);
        $this->detectFeatureEnvy($code);
        $this->detectInappropriateIntimacy($code);
        
        return $this->smells;
    }
    
    private function detectLongMethod(string $code): void
    {
        $lines = explode("\n", $code);
        
        if (count($lines) > 50) {
            $this->smells[] = [
                'type' => 'Long Method',
                'severity' => 'medium',
                'description' => 'Method is too long (' . count($lines) . ' lines)',
                'suggestion' => 'Consider extracting methods'
            ];
        }
    }
    
    private function detectLargeClass(string $code): void
    {
        $lines = explode("\n", $code);
        
        if (count($lines) > 200) {
            $this->smells[] = [
                'type' => 'Large Class',
                'severity' => 'high',
                'description' => 'Class is too large (' . count($lines) . ' lines)',
                'suggestion' => 'Consider extracting classes'
            ];
        }
    }
    
    private function detectLongParameterList(string $code): void
    {
        if (preg_match('/function\s+\w+\s*\([^)]{200,})/', $code)) {
            $this->smells[] = [
                'type' => 'Long Parameter List',
                'severity' => 'medium',
                'description' => 'Method has too many parameters',
                'suggestion' => 'Consider using parameter object'
            ];
        }
    }
    
    private function detectDuplicatedCode(string $code): void
    {
        // Simplified detection - in real implementation, this would be more sophisticated
        $patterns = [
            '/if\s*\([^)]+\)\s*\{\s*return\s*[^;]+;\s*\}/',
            '/foreach\s*\([^)]+\)\s*\{\s*[^}]+;\s*\}/'
        ];
        
        foreach ($patterns as $pattern) {
            $matches = [];
            if (preg_match_all($pattern, $code, $matches) > 1) {
                $this->smells[] = [
                    'type' => 'Duplicated Code',
                    'severity' => 'high',
                    'description' => 'Code duplication detected',
                    'suggestion' => 'Consider extracting common code'
                ];
                break;
            }
        }
    }
    
    private function detectComplexConditional(string $code): void
    {
        if (preg_match('/if\s*\([^)]+\)\s*\{[^}]*if\s*\([^)]+)/', $code)) {
            $this->smells[] = [
                'type' => 'Complex Conditional',
                'severity' => 'medium',
                'description' => 'Complex conditional detected',
                'suggestion' => 'Consider decomposing conditional'
            ];
        }
    }
    
    private function detectMagicNumbers(string $code): void
    {
        if (preg_match_all('/\b(?!0|1|2|10|100)\d{2,}\b/', $code, $matches)) {
            $this->smells[] = [
                'type' => 'Magic Numbers',
                'severity' => 'low',
                'description' => 'Magic numbers detected: ' . implode(', ', array_unique($matches[0])),
                'suggestion' => 'Replace with named constants'
            ];
        }
    }
    
    private function detectDeadCode(string $code): void
    {
        // Simplified detection
        if (preg_match('/\$[a-zA-Z_]\w*\s*=\s*[^;]+;[^}]*\$[a-zA-Z_]\w*\s*=\s*[^;]+;/', $code)) {
            $this->smells[] = [
                'type' => 'Dead Code',
                'severity' => 'low',
                'description' => 'Potential dead code detected',
                'suggestion' => 'Remove unused code'
            ];
        }
    }
    
    private function detectSpeculativeGenerality(string $code): void
    {
        // Simplified detection
        if (preg_match('/abstract\s+class\s+\w+.*implements\s+\w+(,\s*\w+){2,}/', $code)) {
            $this->smells[] = [
                'type' => 'Speculative Generality',
                'severity' => 'medium',
                'description' => 'Class may be doing too much',
                'suggestion' => 'Consider splitting responsibilities'
            ];
        }
    }
    
    private function detectFeatureEnvy(string $code): void
    {
        // Simplified detection
        if (preg_match('/\$[a-zA-Z_]\w*->[a-zA-Z_]\w*\([^)]*\);[^\n]*\$[a-zA-Z_]\w*->[a-zA-Z_]\w*\([^)]*\);[^\n]*\$[a-zA-Z_]\w*->[a-zA-Z_]\w*\([^)]*\);/', $code)) {
            $this->smells[] = [
                'type' => 'Feature Envy',
                'severity' => 'medium',
                'description' => 'Method may be envious of another class',
                'suggestion' => 'Consider moving method to appropriate class'
            ];
        }
    }
    
    private function detectInappropriateIntimacy(string $code): void
    {
        // Simplified detection
        if (preg_match('/\$[a-zA-Z_]\w*->private_[a-zA-Z_]\w*\([^)]*\);/', $code)) {
            $this->smells[] = [
                'type' => 'Inappropriate Intimacy',
                'severity' => 'high',
                'description' => 'Accessing private members of another class',
                'suggestion' => 'Consider moving logic or using public interface'
            ];
        }
    }
    
    public function getSmellReport(): array
    {
        $report = [
            'total_smells' => count($this->smells),
            'by_severity' => [
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ],
            'by_type' => [],
            'recommendations' => []
        ];
        
        foreach ($this->smells as $smell) {
            $report['by_severity'][$smell['severity']]++;
            
            if (!isset($report['by_type'][$smell['type']])) {
                $report['by_type'][$smell['type']] = 0;
            }
            $report['by_type'][$smell['type']]++;
            
            $report['recommendations'][] = $smell['suggestion'];
        }
        
        return $report;
    }
}

// Clean Code Generator
class CleanCodeGenerator
{
    public function generateCleanMethod(
        string $methodName,
        array $parameters,
        string $description,
        array $steps
    ): string {
        $code = "/**\n";
        $code .= " * $description\n";
        $code .= " *\n";
        
        foreach ($parameters as $param => $type) {
            $code .= " * @param $type $$param $param parameter\n";
        }
        
        $code .= " * @return mixed Result of operation\n";
        $code .= " */\n";
        $code .= "public function $methodName(";
        
        $paramList = [];
        foreach ($parameters as $param => $type) {
            $paramList[] = "$type $$param";
        }
        
        $code .= implode(', ', $paramList);
        $code .= ")\n{\n";
        
        foreach ($steps as $step) {
            $code .= "    $step\n";
        }
        
        $code .= "}\n";
        
        return $code;
    }
    
    public function generateCleanClass(
        string $className,
        string $description,
        array $properties,
        array $methods
    ): string {
        $code = "/**\n";
        $code .= " * $description\n";
        $code .= " *\n";
        $code .= " * @package App\\Models\n";
        $code .= " */\n";
        $code .= "class $className\n";
        $code .= "{\n";
        
        // Properties
        foreach ($properties as $property => $type) {
            $code .= "    private $type $$property;\n";
        }
        
        if (!empty($properties)) {
            $code .= "\n";
        }
        
        // Methods
        foreach ($methods as $method) {
            $code .= $method;
            $code .= "\n";
        }
        
        $code .= "}\n";
        
        return $code;
    }
    
    public function refactorLongMethod(string $methodName, array $extractedMethods): array
    {
        $refactored = [
            'original_method' => $methodName,
            'extracted_methods' => $extractedMethods,
            'before' => $this->generateLongMethodExample($methodName),
            'after' => $this->generateRefactoredMethod($methodName, $extractedMethods)
        ];
        
        return $refactored;
    }
    
    private function generateLongMethodExample(string $methodName): string
    {
        return "public function $methodName()\n{\n" .
               "    // Long method with multiple responsibilities\n" .
               "    // Validation logic\n" .
               "    // Processing logic\n" .
               "    // Formatting logic\n" .
               "    // Logging logic\n" .
               "    // Error handling logic\n" .
               "}\n";
    }
    
    private function generateRefactoredMethod(string $methodName, array $extractedMethods): string
    {
        $code = "public function $methodName()\n{\n";
        $code .= "    \$this->validateInput();\n";
        $code .= "    \$result = \$this->processData();\n";
        $code .= "    \$this->formatResult(\$result);\n";
        $code .= "    \$this->logOperation();\n";
        $code .= "}\n\n";
        
        foreach ($extractedMethods as $method) {
            $code .= "private function $method()\n{\n";
            $code .= "    // Extracted logic for $method\n";
            $code .= "}\n\n";
        }
        
        return $code;
    }
}

// Code Quality Examples
class CodeQualityExamples
{
    private CodeQualityAnalyzer $analyzer;
    private RefactoringHelper $refactorer;
    private CodeSmellsDetector $smellsDetector;
    private CleanCodeGenerator $generator;
    
    public function __construct()
    {
        $this->analyzer = new CodeQualityAnalyzer();
        $this->refactorer = new RefactoringHelper();
        $this->smellsDetector = new CodeSmellsDetector();
        $this->generator = new CleanCodeGenerator();
    }
    
    public function demonstrateCodeQualityAnalysis(): void
    {
        echo "Code Quality Analysis Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Analyze a sample class
        $report = $this->analyzer->analyzeClass(UserProcessor::class);
        
        echo "Quality Metrics for " . $report['class_name'] . ":\n";
        echo "Lines of Code: " . $report['lines_of_code'] . "\n";
        echo "Cyclomatic Complexity: " . $report['cyclomatic_complexity'] . "\n";
        echo "Class Coupling: " . $report['class_coupling'] . "\n";
        echo "Method Count: " . $report['method_count'] . "\n";
        echo "Property Count: " . $report['property_count'] . "\n";
        echo "Inheritance Depth: " . $report['depth_of_inheritance'] . "\n";
        echo "WMC: " . $report['weighted_methods_per_class'] . "\n";
        echo "LCOM: " . round($report['lack_of_cohesion'], 2) . "\n";
        echo "Maintainability Index: " . round($report['maintainability_index'], 2) . "\n";
        
        // Show recommendations
        $qualityReport = $this->analyzer->getQualityReport();
        
        echo "\nQuality Score: " . round($qualityReport['quality_score'], 2) . "/100\n";
        
        if (!empty($qualityReport['recommendations'])) {
            echo "\nRecommendations:\n";
            foreach ($qualityReport['recommendations'] as $recommendation) {
                echo "  • $recommendation\n";
            }
        }
    }
    
    public function demonstrateRefactoring(): void
    {
        echo "\nRefactoring Techniques Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Extract Method
        echo "1. Extract Method:\n";
        $result = $this->refactorer->extractMethod(
            'processUserData',
            ['validateInput', 'processData', 'saveResult']
        );
        echo $result . "\n\n";
        
        // Extract Class
        echo "2. Extract Class:\n";
        $result = $this->refactorer->extractClass(
            'UserManager',
            ['validation', 'persistence', 'notification']
        );
        echo $result . "\n\n";
        
        // Replace Conditional with Polymorphism
        echo "3. Replace Conditional with Polymorphism:\n";
        $result = $this->refactorer->replaceConditionalWithPolymorphism(
            ['payment_type', 'user_role', 'subscription_level']
        );
        echo $result . "\n\n";
        
        // Introduce Parameter Object
        echo "4. Introduce Parameter Object:\n";
        $result = $this->refactorer->introduceParameterObject(
            ['user_name', 'user_email', 'user_phone', 'user_address']
        );
        echo $result . "\n\n";
        
        // Replace Magic Number
        echo "5. Replace Magic Number:\n";
        $result = $this->refactorer->replaceMagicNumber(
            86400,
            'SECONDS_PER_DAY'
        );
        echo $result . "\n\n";
        
        // Decompose Conditional
        echo "6. Decompose Conditional:\n";
        $result = $this->refactorer->decomposeConditional(
            'if (user->isActive() && user->hasPermission() && user->isVerified())'
        );
        echo "Decomposed into: " . implode(', ', array_keys($result)) . "\n\n";
        
        // Replace Temporal Query
        echo "7. Replace Temporal Query:\n";
        $result = $this->refactorer->replaceTemporalQuery(
            'getUserByDateRange',
            ['start_date', 'end_date', 'status']
        );
        echo $result . "\n\n";
        
        // Introduce Null Object
        echo "8. Introduce Null Object:\n";
        $result = $this->refactorer->introduceNullObject('UserService');
        echo $result . "\n\n";
        
        // Replace Nested Conditional
        echo "9. Replace Nested Conditional:\n";
        $result = $this->refactorer->replaceNestedConditional(
            ['user_validation', 'permission_check', 'rate_limit_check']
        );
        echo $result . "\n";
    }
    
    public function demonstrateCodeSmellsDetection(): void
    {
        echo "\nCode Smells Detection Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Sample code with smells
        $codeWithSmells = $this->generateCodeWithSmells();
        
        // Detect smells
        $smells = $this->smellsDetector->detectSmells($codeWithSmells);
        
        echo "Code Smells Detected:\n";
        foreach ($smells as $smell) {
            $severityIcon = match($smell['severity']) {
                'high' => '🔴',
                'medium' => '🟡',
                'low' => '🔵'
            };
            
            echo "  $severityIcon {$smell['type']}: {$smell['description']}\n";
            echo "    Suggestion: {$smell['suggestion']}\n\n";
        }
        
        // Show report
        $report = $this->smellsDetector->getSmellReport();
        
        echo "Smell Report:\n";
        echo "Total Smells: {$report['total_smells']}\n";
        echo "By Severity: High ({$report['by_severity']['high']}), Medium ({$report['by_severity']['medium']}), Low ({$report['by_severity']['low']})\n";
        
        if (!empty($report['by_type'])) {
            echo "By Type:\n";
            foreach ($report['by_type'] as $type => $count) {
                echo "  $type: $count\n";
            }
        }
    }
    
    public function demonstrateCleanCodeGeneration(): void
    {
        echo "\nClean Code Generation Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Generate clean method
        echo "1. Clean Method Generation:\n";
        $method = $this->generator->generateCleanMethod(
            'calculateTotal',
            ['items' => 'array', 'tax' => 'float'],
            'Calculate total price including tax',
            [
                '$total = 0;',
                'foreach ($items as $item) {',
                '    $total += $item->getPrice();',
                '}',
                '$totalWithTax = $total * (1 + $tax);',
                'return $totalWithTax;'
            ]
        );
        
        echo $method . "\n\n";
        
        // Generate clean class
        echo "2. Clean Class Generation:\n";
        $class = $this->generator->generateCleanClass(
            'Product',
            'Product entity for e-commerce application',
            [
                'id' => 'int',
                'name' => 'string',
                'price' => 'float',
                'category' => 'string'
            ],
            [
                $method,
                $this->generator->generateCleanMethod(
                    'applyDiscount',
                    ['discount' => 'float'],
                    'Apply discount to product price',
                    [
                        '$this->price *= (1 - $discount);',
                        '$this->price = max(0, $this->price);'
                    ]
                )
            ]
        );
        
        echo $class . "\n\n";
        
        // Refactor long method
        echo "3. Method Refactoring Example:\n";
        $refactoring = $this->generator->refactorLongMethod(
            'processOrder',
            ['validateOrder', 'calculateTotal', 'applyDiscount', 'saveOrder']
        );
        
        echo "Before refactoring:\n";
        echo $refactoring['before'] . "\n";
        
        echo "After refactoring:\n";
        echo $refactoring['after'] . "\n";
    }
    
    public function demonstrateCleanCodePrinciples(): void
    {
        echo "\nClean Code Principles Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show clean vs messy code comparison
        echo "1. Meaningful Names:\n";
        
        $messyCode = "function d($x, $y) { return $x + $y; }";
        $cleanCode = "function calculateSum($firstNumber, $secondNumber) { return $firstNumber + $secondNumber; }";
        
        echo "Messy: $messyCode\n";
        echo "Clean: $cleanCode\n\n";
        
        echo "2. Functions Should Do One Thing:\n";
        
        $messyFunction = "function processUser($data) {\n" .
                         "    // Validate\n" .
                         "    // Save to DB\n" .
                         "    // Send email\n" .
                         "    // Log activity\n" .
                         "}";
        
        $cleanFunctions = [
            "function validateUser(\$data) { /* validation logic */ }",
            "function saveUser(\$user) { /* save logic */ }",
            "function sendWelcomeEmail(\$user) { /* email logic */ }",
            "function logUserActivity(\$user) { /* logging logic */ }"
        ];
        
        echo "Messy: One function doing everything\n$messyFunction\n";
        echo "Clean: Multiple focused functions\n";
        foreach ($cleanFunctions as $function) {
            echo $function . "\n";
        }
        
        echo "\n3. Comments Should Explain Why, Not What:\n";
        
        $badComments = [
            "// Get user from database",
            "// Loop through users",
            "// Return the result"
        ];
        
        $goodComments = [
            "// Use cache to avoid expensive database query",
            "// Handle edge case where user might be null",
            "// This is a workaround for legacy API limitation"
        ];
        
        echo "Bad Comments (explain what):\n";
        foreach ($badComments as $comment) {
            echo "  $comment\n";
        }
        
        echo "\nGood Comments (explain why):\n";
        foreach ($goodComments as $comment) {
            echo "  $comment\n";
        }
        
        echo "\n4. Error Handling:\n";
        
        $badErrorHandling = "function divide($a, $b) {\n" .
                               "    return $a / $b;\n" .
                               "}";
        
        $goodErrorHandling = "function divide($a, $b) {\n" .
                                 "    if ($b === 0) {\n" .
                                 "        throw new InvalidArgumentException('Cannot divide by zero');\n" .
                                 "    }\n" .
                                 "    return $a / $b;\n" .
                                 "}";
        
        echo "Bad Error Handling:\n$badErrorHandling\n";
        echo "Good Error Handling:\n$goodErrorHandling\n";
    }
    
    private function generateCodeWithSmells(): string
    {
        return "<?php\n" .
               "class LongClass {\n" .
               "    public function veryLongMethodWithManyParametersAndComplexLogic(\$param1, \$param2, \$param3, \$param4, \$param5, \$param6, \$param7, \$param8) {\n" .
               "        if (\$param1 > 0 && \$param2 < 100 && \$param3 != null && (\$param4 || \$param5)) {\n" .
               "            if (\$param6) {\n" .
               "                \$result1 = \$param1 * \$param2;\n" .
               "                \$result2 = \$result1 + 100;\n" .
               "                \$result3 = \$result2 * 1.1;\n" .
               "                \$result4 = \$result3 - 50;\n" .
               "                return \$result4;\n" .
               "            } else {\n" .
               "                \$result1 = \$param1 + \$param2;\n" .
               "                \$result2 = \$result1 * 2;\n" .
               "                return \$result2;\n" .
               "            }\n" .
               "        }\n" .
               "        \n" .
               "        // Duplicated code\n" .
               "        \$temp = \$param1 + \$param2;\n" .
               "        \$temp = \$temp * 2;\n" .
               "        \$temp = \$temp + 50;\n" .
               "        \n" .
               "        \$temp2 = \$param3 + \$param4;\n" .
               "        \$temp2 = \$temp2 * 2;\n" .
               "        \$temp2 = \$temp2 + 50;\n" .
               "        \n" .
               "        return 42; // Magic number\n" .
               "    }\n" .
               "}\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Code Quality and Refactoring Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateCodeQualityAnalysis();
        $this->demonstrateRefactoring();
        $this->demonstrateCodeSmellsDetection();
        $this->demonstrateCleanCodeGeneration();
        $this->demonstrateCleanCodePrinciples();
    }
}

// Code Quality Best Practices
function printCodeQualityBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Code Quality Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Clean Code:\n";
    echo "   • Use meaningful names\n";
    echo "   • Keep functions small and focused\n";
    echo "   • Write code that reads like prose\n";
    echo "   • Avoid magic numbers and strings\n";
    echo "   • Use comments to explain why, not what\n\n";
    
    echo "2. Refactoring:\n";
    echo "   • Refactor when you add features\n";
    echo "   • Extract methods and classes\n";
    echo "   • Replace conditionals with polymorphism\n";
    echo "   • Use design patterns appropriately\n";
    echo "   • Keep refactoring small and incremental\n\n";
    
    echo "3. Code Smells:\n";
    echo "   • Long methods and classes\n";
    echo "   • Duplicated code\n";
    echo "   • Large parameter lists\n";
    echo "   • Complex conditionals\n";
    echo "   • Feature envy and inappropriate intimacy\n\n";
    
    echo "4. Quality Metrics:\n";
    echo "   • Monitor cyclomatic complexity\n";
    echo "   • Track maintainability index\n";
    echo "   • Measure class coupling\n";
    echo "   • Check code cohesion\n";
    echo "   • Use automated analysis tools\n\n";
    
    echo "5. Testing and Quality:\n";
    echo "   • Write tests before refactoring\n";
    echo "   • Use code coverage metrics\n";
    echo "   • Implement static analysis\n";
    echo "   • Use continuous integration\n";
    echo "   • Regular code reviews\n\n";
    
    echo "6. Documentation:\n";
    echo "   • Document public interfaces\n";
    echo "   • Use self-documenting code\n";
    echo "   • Keep documentation up to date\n";
    echo "   • Include examples in documentation\n";
    echo "   • Document complex algorithms";
}

// Main execution
function runCodeQualityDemo(): void
{
    $examples = new CodeQualityExamples();
    $examples->runAllExamples();
    printCodeQualityBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCodeQualityDemo();
}
?>
