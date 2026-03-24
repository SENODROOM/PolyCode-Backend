<?php
/**
 * Code Coverage and Quality Analysis
 * 
 * This file demonstrates code coverage analysis, quality metrics,
 * and testing strategies for comprehensive code validation.
 */

// Simple test framework with coverage tracking
class CoverageTest {
    private static int $testsRun = 0;
    private static int $testsPassed = 0;
    private static int $testsFailed = 0;
    private static array $failures = [];
    
    // Coverage tracking
    private static array $executedLines = [];
    private static array $allLines = [];
    private static array $coverageData = [];
    
    public static function assertEqual(mixed $expected, mixed $actual, string $message = ''): void {
        self::$testsRun++;
        self::recordCoverage(debug_backtrace()[1]['function'], debug_backtrace()[1]['line']);
        
        if ($expected === $actual) {
            self::$testsPassed++;
            echo "✓ PASS: $message\n";
        } else {
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected: " . var_export($expected, true) . "\n";
            $failure .= "  Actual: " . var_export($actual, true) . "\n";
            echo $failure;
            self::$failures[] = $failure;
        }
    }
    
    public static function assertTrue(bool $condition, string $message = ''): void {
        self::assertEqual(true, $condition, $message);
    }
    
    public static function assertFalse(bool $condition, string $message = ''): void {
        self::assertEqual(false, $condition, $message);
    }
    
    public static function assertNotNull(mixed $value, string $message = ''): void {
        self::$testsRun++;
        self::recordCoverage(debug_backtrace()[1]['function'], debug_backtrace()[1]['line']);
        
        if ($value !== null) {
            self::$testsPassed++;
            echo "✓ PASS: $message\n";
        } else {
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected: not null\n";
            $failure .= "  Actual: null\n";
            echo $failure;
            self::$failures[] = $failure;
        }
    }
    
    public static function assertThrows(string $expectedException, callable $callback, string $message = ''): void {
        self::$testsRun++;
        self::recordCoverage(debug_backtrace()[1]['function'], debug_backtrace()[1]['line']);
        
        try {
            $callback();
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected exception: $expectedException\n";
            $failure .= "  No exception was thrown\n";
            echo $failure;
            self::$failures[] = $failure;
        } catch (Exception $e) {
            if ($e instanceof $expectedException) {
                self::$testsPassed++;
                echo "✓ PASS: $message\n";
            } else {
                self::$testsFailed++;
                $failure = "✗ FAIL: $message\n";
                $failure .= "  Expected exception: $expectedException\n";
                $failure .= "  Actual exception: " . get_class($e) . "\n";
                echo $failure;
                self::$failures[] = $failure;
            }
        }
    }
    
    private static function recordCoverage(string $function, int $line): void {
        $key = "$function:$line";
        self::$executedLines[$key] = true;
    }
    
    public static function registerFile(string $file): void {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        self::$allLines[$file] = $lines;
    }
    
    public static function getCoverage(): array {
        $totalLines = 0;
        $executedLines = 0;
        
        foreach (self::$allLines as $file => $lines) {
            foreach ($lines as $lineNum => $line) {
                $lineNum++; // Convert to 1-based
                $totalLines++;
                
                // Simple heuristic: count non-empty, non-comment lines
                if (trim($line) !== '' && !str_starts_with(trim($line), '//') && !str_starts_with(trim($line), '#')) {
                    $key = basename($file) . ":$lineNum";
                    if (isset(self::$executedLines[$key])) {
                        $executedLines++;
                    }
                }
            }
        }
        
        $percentage = $totalLines > 0 ? ($executedLines / $totalLines) * 100 : 0;
        
        return [
            'total_lines' => $totalLines,
            'executed_lines' => $executedLines,
            'coverage_percentage' => round($percentage, 2),
            'executed_line_numbers' => array_keys(self::$executedLines)
        ];
    }
    
    public static function getStats(): array {
        return [
            'run' => self::$testsRun,
            'passed' => self::$testsPassed,
            'failed' => self::$testsFailed,
            'success_rate' => self::$testsRun > 0 ? (self::$testsPassed / self::$testsRun) * 100 : 0
        ];
    }
    
    public static function reset(): void {
        self::$testsRun = 0;
        self::$testsPassed = 0;
        self::$testsFailed = 0;
        self::$failures = [];
        self::$executedLines = [];
    }
    
    public static function printSummary(): void {
        $stats = self::getStats();
        $coverage = self::getCoverage();
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Test Coverage Analysis\n";
        echo str_repeat("=", 50) . "\n";
        echo "Tests Run: {$stats['run']}\n";
        echo "Tests Passed: {$stats['passed']}\n";
        echo "Tests Failed: {$stats['failed']}\n";
        echo "Success Rate: " . number_format($stats['success_rate'], 1) . "%\n";
        echo "\n";
        echo "Code Coverage:\n";
        echo "Lines Executed: {$coverage['executed_lines']} / {$coverage['total_lines']}\n";
        echo "Coverage: {$coverage['coverage_percentage']}%\n";
        echo str_repeat("=", 50) . "\n";
    }
}

// Code Quality Metrics
class CodeQualityAnalyzer {
    private string $file;
    private array $metrics = [];
    
    public function __construct(string $file) {
        $this->file = $file;
        $this->analyze();
    }
    
    private function analyze(): void {
        $content = file_get_contents($this->file);
        $lines = file($this->file, FILE_IGNORE_NEW_LINES);
        
        $this->metrics = [
            'file' => basename($this->file),
            'total_lines' => count($lines),
            'code_lines' => 0,
            'comment_lines' => 0,
            'empty_lines' => 0,
            'functions' => 0,
            'classes' => 0,
            'complexity' => 0,
            'cyclomatic_complexity' => 0,
            'maintainability_index' => 0
        ];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            if ($trimmed === '') {
                $this->metrics['empty_lines']++;
            } elseif (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $this->metrics['comment_lines']++;
            } else {
                $this->metrics['code_lines']++;
            }
        }
        
        // Count functions and classes
        $tokens = token_get_all($content);
        $inFunction = false;
        $inClass = false;
        $braceCount = 0;
        $decisionPoints = 0;
        
        foreach ($tokens as $token) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_FUNCTION:
                        if (!$inClass) {
                            $this->metrics['functions']++;
                            $inFunction = true;
                        }
                        break;
                    case T_CLASS:
                        $this->metrics['classes']++;
                        $inClass = true;
                        break;
                    case T_IF:
                    case T_ELSEIF:
                    case T_WHILE:
                    case T_FOR:
                    case T_FOREACH:
                    case T_CASE:
                    case T_CATCH:
                        $decisionPoints++;
                        break;
                    case T_SWITCH:
                        $decisionPoints += 2; // Switch is more complex
                        break;
                    case T_LOGICAL_AND:
                    case T_LOGICAL_OR:
                        $decisionPoints++;
                        break;
                    case '{':
                        $braceCount++;
                        break;
                    case '}':
                        $braceCount--;
                        if ($braceCount === 0) {
                            $inFunction = false;
                            $inClass = false;
                        }
                        break;
                }
            }
        }
        
        $this->metrics['cyclomatic_complexity'] = $decisionPoints + 1;
        $this->metrics['maintainability_index'] = $this->calculateMaintainabilityIndex();
    }
    
    private function calculateMaintainabilityIndex(): float {
        // Simplified maintainability index calculation
        $volume = $this->metrics['code_lines'] * log10(max(1, $this->metrics['functions'] + 1));
        $complexity = $this->metrics['cyclomatic_complexity'];
        
        if ($volume === 0) {
            return 100;
        }
        
        $mi = 171 - 5.2 * log10($volume) - 0.23 * $complexity - 16.2 * log10(max(1, $this->metrics['functions'] + 1));
        
        return max(0, min(100, $mi));
    }
    
    public function getMetrics(): array {
        return $this->metrics;
    }
    
    public function getQualityScore(): string {
        $mi = $this->metrics['maintainability_index'];
        
        if ($mi >= 85) {
            return 'Excellent';
        } elseif ($mi >= 70) {
            return 'Good';
        } elseif ($mi >= 50) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }
    
    public function getRecommendations(): array {
        $recommendations = [];
        $metrics = $this->metrics;
        
        if ($metrics['cyclomatic_complexity'] > 10) {
            $recommendations[] = "Consider reducing cyclomatic complexity (current: {$metrics['cyclomatic_complexity']})";
        }
        
        if ($metrics['functions'] > 20) {
            $recommendations[] = "Consider splitting large files ({$metrics['functions']} functions)";
        }
        
        if ($metrics['code_lines'] / max(1, $metrics['functions']) > 50) {
            $recommendations[] = "Functions are too long on average";
        }
        
        if ($metrics['comment_lines'] / max(1, $metrics['code_lines']) < 0.1) {
            $recommendations[] = "Consider adding more documentation";
        }
        
        return $recommendations;
    }
}

// Example classes for testing coverage
class MathUtils {
    public static function add(int $a, int $b): int {
        return $a + $b;
    }
    
    public static function subtract(int $a, int $b): int {
        return $a - $b;
    }
    
    public static function multiply(int $a, int $b): int {
        return $a * $b;
    }
    
    public static function divide(int $a, int $b): int {
        if ($b === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return $a / $b;
    }
    
    public static function factorial(int $n): int {
        if ($n < 0) {
            throw new InvalidArgumentException('Factorial not defined for negative numbers');
        }
        if ($n === 0 || $n === 1) {
            return 1;
        }
        return $n * self::factorial($n - 1);
    }
    
    public static function isPrime(int $n): bool {
        if ($n <= 1) {
            return false;
        }
        if ($n === 2) {
            return true;
        }
        if ($n % 2 === 0) {
            return false;
        }
        
        for ($i = 3; $i <= sqrt($n); $i += 2) {
            if ($n % $i === 0) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function fibonacci(int $n): int {
        if ($n <= 0) {
            throw new InvalidArgumentException('Fibonacci not defined for non-positive numbers');
        }
        if ($n === 1 || $n === 2) {
            return 1;
        }
        
        $a = 1;
        $b = 1;
        
        for ($i = 3; $i <= $n; $i++) {
            $temp = $a + $b;
            $a = $b;
            $b = $temp;
        }
        
        return $b;
    }
}

class StringProcessor {
    private string $value;
    
    public function __construct(string $value) {
        $this->value = $value;
    }
    
    public function getValue(): string {
        return $this->value;
    }
    
    public function reverse(): string {
        return strrev($this->value);
    }
    
    public function uppercase(): string {
        return strtoupper($this->value);
    }
    
    public function lowercase(): string {
        return strtolower($this->value);
    }
    
    public function capitalize(): string {
        return ucwords(strtolower($this->value));
    }
    
    public function length(): int {
        return strlen($this->value);
    }
    
    public function isEmpty(): bool {
        return empty($this->value);
    }
    
    public function contains(string $needle): bool {
        return strpos($this->value, $needle) !== false;
    }
    
    public function startsWith(string $needle): bool {
        return strpos($this->value, $needle) === 0;
    }
    
    public function endsWith(string $needle): bool {
        return substr($this->value, -strlen($needle)) === $needle;
    }
    
    public function replace(string $search, string $replace): string {
        return str_replace($search, $replace, $this->value);
    }
    
    public function split(string $delimiter): array {
        return explode($delimiter, $this->value);
    }
    
    public function trim(): string {
        return trim($this->value);
    }
    
    public function pad(int $length, string $padString = ' ', int $padType = STR_PAD_BOTH): string {
        return str_pad($this->value, $length, $padString, $padType);
    }
    
    public function substring(int $start, int $length = null): string {
        return $length === null ? substr($this->value, $start) : substr($this->value, $start, $length);
    }
    
    public function toArray(): array {
        return str_split($this->value);
    }
    
    public function wordCount(): int {
        return str_word_count($this->value);
    }
}

class ArrayHelper {
    public static function sum(array $array): int {
        return array_sum($array);
    }
    
    public static function average(array $array): float {
        if (empty($array)) {
            return 0;
        }
        return array_sum($array) / count($array);
    }
    
    public static function max(array $array): mixed {
        return empty($array) ? null : max($array);
    }
    
    public static function min(array $array): mixed {
        return empty($array) ? null : min($array);
    }
    
    public static function filter(array $array, callable $callback): array {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
    
    public static function map(array $array, callable $callback): array {
        return array_map($callback, $array);
    }
    
    public static function unique(array $array): array {
        return array_unique($array);
    }
    
    public static function sort(array $array, bool $ascending = true): array {
        $arrayCopy = $array;
        if ($ascending) {
            sort($arrayCopy);
        } else {
            rsort($arrayCopy);
        }
        return $arrayCopy;
    }
    
    public static function contains(array $array, mixed $value): bool {
        return in_array($value, $array);
    }
    
    public static function chunk(array $array, int $size): array {
        return array_chunk($array, $size);
    }
    
    public static function flatten(array $array): array {
        $result = [];
        array_walk_recursive($array, function($value) use (&$result) {
            $result[] = $value;
        });
        return $result;
    }
    
    public static function pluck(array $array, string $key): array {
        return array_column($array, $key);
    }
    
    public static function where(array $array, callable $callback): array {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
    
    public static function first(array $array, callable $callback = null): mixed {
        if ($callback === null) {
            return reset($array);
        }
        
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        
        return null;
    }
    
    public static function groupBy(array $array, callable $callback): array {
        $result = [];
        foreach ($array as $item) {
            $key = $callback($item);
            if (!isset($result[$key])) {
                $result[$key] = [];
            }
            $result[$key][] = $item;
        }
        return $result;
    }
}

// Test Classes
class MathUtilsTest {
    public function runAllTests(): void {
        echo "MathUtils Tests:\n";
        echo str_repeat("-", 20) . "\n";
        
        $this->testAdd();
        $this->testSubtract();
        $this->testMultiply();
        $this->testDivide();
        $this->testDivideByZero();
        $this->testFactorial();
        $this->testFactorialNegative();
        $this->testIsPrime();
        $this->testFibonacci();
        $this->testFibonacciInvalid();
    }
    
    private function testAdd(): void {
        CoverageTest::assertEqual(5, MathUtils::add(2, 3), "2 + 3 = 5");
        CoverageTest::assertEqual(0, MathUtils::add(-2, 2), "-2 + 2 = 0");
        CoverageTest::assertEqual(-5, MathUtils::add(-2, -3), "-2 + -3 = -5");
    }
    
    private function testSubtract(): void {
        CoverageTest::assertEqual(1, MathUtils::subtract(5, 4), "5 - 4 = 1");
        CoverageTest::assertEqual(-1, MathUtils::subtract(4, 5), "4 - 5 = -1");
    }
    
    private function testMultiply(): void {
        CoverageTest::assertEqual(6, MathUtils::multiply(2, 3), "2 * 3 = 6");
        CoverageTest::assertEqual(-6, MathUtils::multiply(-2, 3), "-2 * 3 = -6");
    }
    
    private function testDivide(): void {
        CoverageTest::assertEqual(2, MathUtils::divide(6, 3), "6 / 3 = 2");
        CoverageTest::assertEqual(2.5, MathUtils::divide(5, 2), "5 / 2 = 2.5");
    }
    
    private function testDivideByZero(): void {
        CoverageTest::assertThrows(InvalidArgumentException::class, function() {
            MathUtils::divide(5, 0);
        }, "Division by zero should throw exception");
    }
    
    private function testFactorial(): void {
        CoverageTest::assertEqual(1, MathUtils::factorial(0), "0! = 1");
        CoverageTest::assertEqual(1, MathUtils::factorial(1), "1! = 1");
        CoverageTest::assertEqual(6, MathUtils::factorial(3), "3! = 6");
        CoverageTest::assertEqual(120, MathUtils::factorial(5), "5! = 120");
    }
    
    private function testFactorialNegative(): void {
        CoverageTest::assertThrows(InvalidArgumentException::class, function() {
            MathUtils::factorial(-1);
        }, "Factorial of negative number should throw exception");
    }
    
    private function testIsPrime(): void {
        CoverageTest::assertFalse(MathUtils::isPrime(1), "1 is not prime");
        CoverageTest::assertTrue(MathUtils::isPrime(2), "2 is prime");
        CoverageTest::assertTrue(MathUtils::isPrime(3), "3 is prime");
        CoverageTest::assertFalse(MathUtils::isPrime(4), "4 is not prime");
        CoverageTest::assertTrue(MathUtils::isPrime(13), "13 is prime");
    }
    
    private function testFibonacci(): void {
        CoverageTest::assertEqual(1, MathUtils::fibonacci(1), "F(1) = 1");
        CoverageTest::assertEqual(1, MathUtils::fibonacci(2), "F(2) = 1");
        CoverageTest::assertEqual(2, MathUtils::fibonacci(3), "F(3) = 2");
        CoverageTest::assertEqual(55, MathUtils::fibonacci(10), "F(10) = 55");
    }
    
    private function testFibonacciInvalid(): void {
        CoverageTest::assertThrows(InvalidArgumentException::class, function() {
            MathUtils::fibonacci(0);
        }, "Fibonacci of 0 should throw exception");
    }
}

class StringProcessorTest {
    public function runAllTests(): void {
        echo "\nStringProcessor Tests:\n";
        echo str_repeat("-", 25) . "\n";
        
        $processor = new StringProcessor("Hello World");
        
        $this->testConstructor($processor);
        $this->testReverse($processor);
        $this->testUppercase($processor);
        $this->testLowercase($processor);
        $this->testCapitalize($processor);
        $this->testLength($processor);
        $this->testIsEmpty($processor);
        $this->testContains($processor);
        $this->testStartsWith($processor);
        $this->testEndsWith($processor);
        $this->testReplace($processor);
        $this->testSplit($processor);
        $this->testTrim($processor);
    }
    
    private function testConstructor(StringProcessor $processor): void {
        CoverageTest::assertEqual("Hello World", $processor->getValue(), "Constructor should set value");
    }
    
    private function testReverse(StringProcessor $processor): void {
        CoverageTest::assertEqual("dlroW olleH", $processor->reverse(), "Reverse should work");
    }
    
    private function testUppercase(StringProcessor $processor): void {
        CoverageTest::assertEqual("HELLO WORLD", $processor->uppercase(), "Uppercase should work");
    }
    
    private function testLowercase(StringProcessor $processor): void {
        CoverageTest::assertEqual("hello world", $processor->lowercase(), "Lowercase should work");
    }
    
    private function testCapitalize(StringProcessor $processor): void {
        CoverageTest::assertEqual("Hello World", $processor->capitalize(), "Capitalize should work");
    }
    
    private function testLength(StringProcessor $processor): void {
        CoverageTest::assertEqual(11, $processor->length(), "Length should be 11");
    }
    
    private function testIsEmpty(StringProcessor $processor): void {
        CoverageTest::assertFalse($processor->isEmpty(), "Should not be empty");
        
        $emptyProcessor = new StringProcessor("");
        CoverageTest::assertTrue($emptyProcessor->isEmpty(), "Empty string should be empty");
    }
    
    private function testContains(StringProcessor $processor): void {
        CoverageTest::assertTrue($processor->contains("World"), "Should contain 'World'");
        CoverageTest::assertFalse($processor->contains("xyz"), "Should not contain 'xyz'");
    }
    
    private function testStartsWith(StringProcessor $processor): void {
        CoverageTest::assertTrue($processor->startsWith("Hello"), "Should start with 'Hello'");
        CoverageTest::assertFalse($processor->startsWith("World"), "Should not start with 'World'");
    }
    
    private function testEndsWith(StringProcessor $processor): void {
        CoverageTest::assertTrue($processor->endsWith("World"), "Should end with 'World'");
        CoverageTest::assertFalse($processor->endsWith("Hello"), "Should not end with 'Hello'");
    }
    
    private function testReplace(StringProcessor $processor): void {
        CoverageTest::assertEqual("Hezzo Worzd", $processor->replace("l", "z"), "Replace should work");
    }
    
    private function testSplit(StringProcessor $processor): void {
        $result = $processor->split(" ");
        CoverageTest::assertEqual(["Hello", "World"], $result, "Split should work");
    }
    
    private function testTrim(StringProcessor $processor): void {
        $paddedProcessor = new StringProcessor("  Hello World  ");
        CoverageTest::assertEqual("Hello World", $paddedProcessor->trim(), "Trim should work");
    }
}

class ArrayHelperTest {
    public function runAllTests(): void {
        echo "\nArrayHelper Tests:\n";
        echo str_repeat("-", 20) . "\n";
        
        $this->testSum();
        $this->testAverage();
        $this->testMaxMin();
        $this->testFilter();
        $this->testMap();
        $this->testUnique();
        $this->testSort();
        $this->testContains();
        $this->testChunk();
        $this->testFlatten();
        $this->testGroupBy();
    }
    
    private function testSum(): void {
        CoverageTest::assertEqual(15, ArrayHelper::sum([1, 2, 3, 4, 5]), "Sum should work");
        CoverageTest::assertEqual(0, ArrayHelper::sum([]), "Empty array sum should be 0");
    }
    
    private function testAverage(): void {
        CoverageTest::assertEqual(3.0, ArrayHelper::average([1, 2, 3, 4, 5]), "Average should work");
        CoverageTest::assertEqual(0.0, ArrayHelper::average([]), "Empty array average should be 0");
    }
    
    private function testMaxMin(): void {
        CoverageTest::assertEqual(5, ArrayHelper::max([1, 2, 3, 4, 5]), "Max should work");
        CoverageTest::assertEqual(1, ArrayHelper::min([1, 2, 3, 4, 5]), "Min should work");
        CoverageTest::assertEqual(null, ArrayHelper::max([]), "Empty array max should be null");
    }
    
    private function testFilter(): void {
        $result = ArrayHelper::filter([1, 2, 3, 4, 5], fn($n) => $n % 2 === 0);
        CoverageTest::assertEqual([2, 4], array_values($result), "Filter should work");
    }
    
    private function testMap(): void {
        $result = ArrayHelper::map([1, 2, 3], fn($n) => $n * 2);
        CoverageTest::assertEqual([2, 4, 6], $result, "Map should work");
    }
    
    private function testUnique(): void {
        $result = ArrayHelper::unique([1, 2, 2, 3, 3, 3]);
        CoverageTest::assertEqual([1, 2, 3], array_values($result), "Unique should work");
    }
    
    private function testSort(): void {
        $result = ArrayHelper::sort([3, 1, 4, 2]);
        CoverageTest::assertEqual([1, 2, 3, 4], $result, "Sort should work");
        
        $result = ArrayHelper::sort([3, 1, 4, 2], false);
        CoverageTest::assertEqual([4, 3, 2, 1], $result, "Descending sort should work");
    }
    
    private function testContains(): void {
        CoverageTest::assertTrue(ArrayHelper::contains([1, 2, 3, 4, 5], 3), "Contains should work");
        CoverageTest::assertFalse(ArrayHelper::contains([1, 2, 3, 4, 5], 6), "Contains should work for missing");
    }
    
    private function testChunk(): void {
        $result = ArrayHelper::chunk([1, 2, 3, 4, 5], 2);
        CoverageTest::assertEqual([[1, 2], [3, 4], [5]], $result, "Chunk should work");
    }
    
    private function testFlatten(): void {
        $result = ArrayHelper::flatten([[1, 2], [3, 4], [5]]);
        CoverageTest::assertEqual([1, 2, 3, 4, 5], $result, "Flatten should work");
    }
    
    private function testGroupBy(): void {
        $data = [
            ['name' => 'John', 'age' => 25],
            ['name' => 'Jane', 'age' => 30],
            ['name' => 'Bob', 'age' => 25]
        ];
        
        $result = ArrayHelper::groupBy($data, fn($item) => $item['age']);
        CoverageTest::assertEqual(25, array_key_first($result), "GroupBy should work");
    }
}

// Main test runner
function runCoverageAnalysis(): void {
    CoverageTest::reset();
    
    echo "Code Coverage and Quality Analysis\n";
    echo str_repeat("=", 50) . "\n\n";
    
    // Register files for coverage
    CoverageTest::registerFile(__FILE__);
    
    // Run tests
    $mathTest = new MathUtilsTest();
    $mathTest->runAllTests();
    
    $stringTest = new StringProcessorTest();
    $stringTest->runAllTests();
    
    $arrayTest = new ArrayHelperTest();
    $arrayTest->runAllTests();
    
    // Print test summary
    CoverageTest::printSummary();
    
    // Analyze code quality
    echo "\nCode Quality Analysis:\n";
    echo str_repeat("-", 30) . "\n";
    
    $analyzer = new CodeQualityAnalyzer(__FILE__);
    $metrics = $analyzer->getMetrics();
    
    echo "File: {$metrics['file']}\n";
    echo "Total Lines: {$metrics['total_lines']}\n";
    echo "Code Lines: {$metrics['code_lines']}\n";
    echo "Comment Lines: {$metrics['comment_lines']}\n";
    echo "Empty Lines: {$metrics['empty_lines']}\n";
    echo "Functions: {$metrics['functions']}\n";
    echo "Classes: {$metrics['classes']}\n";
    echo "Cyclomatic Complexity: {$metrics['cyclomatic_complexity']}\n";
    echo "Maintainability Index: " . round($metrics['maintainability_index'], 2) . "\n";
    echo "Quality Score: {$analyzer->getQualityScore()}\n";
    
    $recommendations = $analyzer->getRecommendations();
    if (!empty($recommendations)) {
        echo "\nRecommendations:\n";
        foreach ($recommendations as $rec) {
            echo "• $rec\n";
        }
    } else {
        echo "\n✓ No recommendations - code quality is good!\n";
    }
}

// Coverage Best Practices Guide
function printCoverageBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Code Coverage Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Coverage Goals:\n";
    echo "   • Aim for 80%+ line coverage\n";
    echo "   • Focus on critical paths\n";
    echo "   • Cover edge cases and error conditions\n";
    echo "   • Don't sacrifice quality for coverage\n\n";
    
    echo "2. What to Test:\n";
    echo "   • Public methods and interfaces\n";
    echo "   • Business logic and algorithms\n";
    echo "   • Error handling and edge cases\n";
    echo "   • Integration points\n\n";
    
    echo "3. What NOT to Cover:\n";
    echo "   • Simple getters/setters\n";
    echo "   • Trivial constructors\n";
    echo "   • Auto-generated code\n";
    echo "   • Third-party libraries\n\n";
    
    echo "4. Coverage Tools:\n";
    echo "   • PHPUnit Coverage Extension\n";
    echo "   • Xdebug\n";
    echo "   • PCOV\n";
    echo "   • Codecov, Coveralls\n\n";
    
    echo "5. Quality Metrics:\n";
    echo "   • Cyclomatic Complexity\n";
    echo "   • Maintainability Index\n";
    echo "   • Code Duplication\n";
    echo "   • Technical Debt\n\n";
    
    echo "6. Continuous Integration:\n";
    echo "   • Run coverage on every build\n";
    echo "   • Set minimum coverage thresholds\n";
    echo "   • Fail builds on coverage drops\n";
    echo "   • Generate coverage reports\n";
}

// Run the analysis
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCoverageAnalysis();
    printCoverageBestPractices();
}
?>
