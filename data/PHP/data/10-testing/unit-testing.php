<?php
/**
 * Unit Testing Examples
 * 
 * This file demonstrates unit testing concepts using PHPUnit-style assertions.
 * In a real project, these would be separate test files.
 */

// Simple class to test
class Calculator {
    public function add(float $a, float $b): float {
        return $a + $b;
    }
    
    public function subtract(float $a, float $b): float {
        return $a - $b;
    }
    
    public function multiply(float $a, float $b): float {
        return $a * $b;
    }
    
    public function divide(float $a, float $b): float {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return $a / $b;
    }
    
    public function power(float $base, float $exponent): float {
        return pow($base, $exponent);
    }
    
    public function isEven(int $number): bool {
        return $number % 2 === 0;
    }
    
    public function factorial(int $n): int {
        if ($n < 0) {
            throw new InvalidArgumentException('Factorial is not defined for negative numbers');
        }
        if ($n === 0 || $n === 1) {
            return 1;
        }
        return $n * $this->factorial($n - 1);
    }
}

// Email validation class
class EmailValidator {
    private array $allowedDomains = [];
    
    public function __construct(array $allowedDomains = []) {
        $this->allowedDomains = $allowedDomains;
    }
    
    public function isValid(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function isAllowedDomain(string $email): bool {
        if (!$this->isValid($email)) {
            return false;
        }
        
        $domain = substr(strrchr($email, '@'), 1);
        return empty($this->allowedDomains) || in_array($domain, $this->allowedDomains);
    }
    
    public function addAllowedDomain(string $domain): void {
        $this->allowedDomains[] = $domain;
        $this->allowedDomains = array_unique($this->allowedDomains);
    }
    
    public function getAllowedDomains(): array {
        return $this->allowedDomains;
    }
}

// String utilities class
class StringUtils {
    public function reverse(string $string): string {
        return strrev($string);
    }
    
    public function uppercase(string $string): string {
        return strtoupper($string);
    }
    
    public function lowercase(string $string): string {
        return strtolower($string);
    }
    
    public function capitalize(string $string): string {
        return ucwords(strtolower($string));
    }
    
    public function truncate(string $string, int $length, string $suffix = '...'): string {
        if (strlen($string) <= $length) {
            return $string;
        }
        
        return substr($string, 0, $length - strlen($suffix)) . $suffix;
    }
    
    public function wordCount(string $string): int {
        return str_word_count($string);
    }
    
    public function contains(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) !== false;
    }
    
    public function startsWith(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) === 0;
    }
    
    public function endsWith(string $haystack, string $needle): bool {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

// Simple test framework (PHPUnit-like assertions)
class SimpleTest {
    private static int $testsRun = 0;
    private static int $testsPassed = 0;
    private static int $testsFailed = 0;
    private static array $failures = [];
    
    public static function assertEqual(mixed $expected, mixed $actual, string $message = ''): void {
        self::$testsRun++;
        
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
    
    public static function assertNotEqual(mixed $expected, mixed $actual, string $message = ''): void {
        self::$testsRun++;
        
        if ($expected !== $actual) {
            self::$testsPassed++;
            echo "✓ PASS: $message\n";
        } else {
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected values to be different\n";
            $failure .= "  Both values: " . var_export($expected, true) . "\n";
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
    
    public static function assertNull(mixed $value, string $message = ''): void {
        self::assertEqual(null, $value, $message);
    }
    
    public static function assertNotNull(mixed $value, string $message = ''): void {
        self::assertNotEqual(null, $value, $message);
    }
    
    public static function assertInstanceOf(string $expectedClass, mixed $object, string $message = ''): void {
        self::$testsRun++;
        
        if ($object instanceof $expectedClass) {
            self::$testsPassed++;
            echo "✓ PASS: $message\n";
        } else {
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected instance of: $expectedClass\n";
            $failure .= "  Actual: " . gettype($object) . "\n";
            echo $failure;
            self::$failures[] = $failure;
        }
    }
    
    public static function assertThrows(string $expectedException, callable $callback, string $message = ''): void {
        self::$testsRun++;
        
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
    
    public static function assertCount(int $expectedCount, array|Countable $array, string $message = ''): void {
        self::assertEqual($expectedCount, count($array), $message);
    }
    
    public static function assertContains(mixed $needle, array $haystack, string $message = ''): void {
        self::$testsRun++;
        
        if (in_array($needle, $haystack)) {
            self::$testsPassed++;
            echo "✓ PASS: $message\n";
        } else {
            self::$testsFailed++;
            $failure = "✗ FAIL: $message\n";
            $failure .= "  Expected array to contain: " . var_export($needle, true) . "\n";
            $failure .= "  Array: " . var_export($haystack, true) . "\n";
            echo $failure;
            self::$failures[] = $failure;
        }
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
    }
    
    public static function printSummary(): void {
        $stats = self::getStats();
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Test Summary\n";
        echo str_repeat("=", 50) . "\n";
        echo "Tests Run: {$stats['run']}\n";
        echo "Tests Passed: {$stats['passed']}\n";
        echo "Tests Failed: {$stats['failed']}\n";
        echo "Success Rate: " . number_format($stats['success_rate'], 1) . "%\n";
        
        if ($stats['failed'] > 0) {
            echo "\nFailures:\n";
            foreach (self::$failures as $failure) {
                echo $failure . "\n";
            }
        }
        
        echo str_repeat("=", 50) . "\n";
    }
}

// Test Data Providers
class DataProvider {
    public static function additionProvider(): array {
        return [
            [1, 2, 3],
            [0, 0, 0],
            [-1, 1, 0],
            [1.5, 2.5, 4.0],
            [-5, -10, -15]
        ];
    }
    
    public static function multiplicationProvider(): array {
        return [
            [2, 3, 6],
            [0, 5, 0],
            [-2, 3, -6],
            [2, -3, -6],
            [1.5, 2, 3.0]
        ];
    }
    
    public static function emailProvider(): array {
        return [
            ['test@example.com', true],
            ['user.name@domain.co.uk', true],
            ['invalid-email', false],
            ['@domain.com', false],
            ['user@', false],
            ['', false]
        ];
    }
    
    public static function stringProvider(): array {
        return [
            ['hello', 'olleh'],
            ['World', 'dlroW'],
            ['', ''],
            ['12345', '54321'],
            ['A', 'A']
        ];
    }
}

// Test Fixtures
class TestFixture {
    private Calculator $calculator;
    private EmailValidator $emailValidator;
    private StringUtils $stringUtils;
    
    public function __construct() {
        $this->calculator = new Calculator();
        $this->emailValidator = new EmailValidator(['example.com', 'test.org']);
        $this->stringUtils = new StringUtils();
    }
    
    public function getCalculator(): Calculator {
        return $this->calculator;
    }
    
    public function getEmailValidator(): EmailValidator {
        return $this->emailValidator;
    }
    
    public function getStringUtils(): StringUtils {
        return $this->stringUtils;
    }
}

// Test Classes
class CalculatorTest {
    private TestFixture $fixture;
    
    public function __construct() {
        $this->fixture = new TestFixture();
    }
    
    public function runAllTests(): void {
        echo "Running Calculator Tests...\n";
        echo str_repeat("-", 30) . "\n";
        
        $this->testAddition();
        $this->testSubtraction();
        $this->testMultiplication();
        $this->testDivision();
        $this-> testDivisionByZero();
        $this->testPower();
        $this->testIsEven();
        $this->testFactorial();
    }
    
    private function testAddition(): void {
        $calculator = $this->fixture->getCalculator();
        
        // Test with data provider
        foreach (DataProvider::additionProvider() as [$a, $b, $expected]) {
            SimpleTest::assertEqual($expected, $calculator->add($a, $b), 
                "Addition: $a + $b should equal $expected");
        }
    }
    
    private function testSubtraction(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertEqual(5, $calculator->subtract(10, 5), "10 - 5 = 5");
        SimpleTest::assertEqual(-5, $calculator->subtract(5, 10), "5 - 10 = -5");
        SimpleTest::assertEqual(0, $calculator->subtract(5, 5), "5 - 5 = 0");
    }
    
    private function testMultiplication(): void {
        $calculator = $this->fixture->getCalculator();
        
        // Test with data provider
        foreach (DataProvider::multiplicationProvider() as [$a, $b, $expected]) {
            SimpleTest::assertEqual($expected, $calculator->multiply($a, $b), 
                "Multiplication: $a * $b should equal $expected");
        }
    }
    
    private function testDivision(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertEqual(2, $calculator->divide(10, 5), "10 / 5 = 2");
        SimpleTest::assertEqual(-2, $calculator->divide(-10, 5), "-10 / 5 = -2");
        SimpleTest::assertEqual(2.5, $calculator->divide(5, 2), "5 / 2 = 2.5");
    }
    
    private function testDivisionByZero(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertThrows(InvalidArgumentException::class, function() use ($calculator) {
            $calculator->divide(10, 0);
        }, "Division by zero should throw InvalidArgumentException");
    }
    
    private function testPower(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertEqual(8, $calculator->power(2, 3), "2^3 = 8");
        SimpleTest::assertEqual(1, $calculator->power(5, 0), "5^0 = 1");
        SimpleTest::assertEqual(0.25, $calculator->power(2, -2), "2^-2 = 0.25");
    }
    
    private function testIsEven(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertTrue($calculator->isEven(2), "2 is even");
        SimpleTest::assertTrue($calculator->isEven(0), "0 is even");
        SimpleTest::assertFalse($calculator->isEven(1), "1 is not even");
        SimpleTest::assertFalse($calculator->isEven(3), "3 is not even");
    }
    
    private function testFactorial(): void {
        $calculator = $this->fixture->getCalculator();
        
        SimpleTest::assertEqual(1, $calculator->factorial(0), "0! = 1");
        SimpleTest::assertEqual(1, $calculator->factorial(1), "1! = 1");
        SimpleTest::assertEqual(120, $calculator->factorial(5), "5! = 120");
        SimpleTest::assertEqual(3628800, $calculator->factorial(10), "10! = 3628800");
        
        SimpleTest::assertThrows(InvalidArgumentException::class, function() use ($calculator) {
            $calculator->factorial(-1);
        }, "Factorial of negative number should throw exception");
    }
}

class EmailValidatorTest {
    private TestFixture $fixture;
    
    public function __construct() {
        $this->fixture = new TestFixture();
    }
    
    public function runAllTests(): void {
        echo "\nRunning Email Validator Tests...\n";
        echo str_repeat("-", 30) . "\n";
        
        $this->testIsValid();
        $this->testIsAllowedDomain();
        $this->testAddAllowedDomain();
        $this->testGetAllowedDomains();
    }
    
    private function testIsValid(): void {
        $validator = $this->fixture->getEmailValidator();
        
        // Test with data provider
        foreach (DataProvider::emailProvider() as [$email, $expected]) {
            SimpleTest::assertEqual($expected, $validator->isValid($email), 
                "Email '$email' should be " . ($expected ? 'valid' : 'invalid'));
        }
    }
    
    private function testIsAllowedDomain(): void {
        $validator = $this->fixture->getEmailValidator();
        
        SimpleTest::assertTrue($validator->isAllowedDomain('test@example.com'), 
            "test@example.com should be allowed");
        SimpleTest::assertTrue($validator->isAllowedDomain('user@test.org'), 
            "user@test.org should be allowed");
        SimpleTest::assertFalse($validator->isAllowedDomain('user@other.com'), 
            "user@other.com should not be allowed");
        SimpleTest::assertFalse($validator->isAllowedDomain('invalid-email'), 
            "Invalid email should not be allowed");
    }
    
    private function testAddAllowedDomain(): void {
        $validator = $this->fixture->getEmailValidator();
        
        $validator->addAllowedDomain('newdomain.com');
        SimpleTest::assertTrue($validator->isAllowedDomain('test@newdomain.com'), 
            "newdomain.com should be allowed after adding");
        
        // Test duplicate doesn't create duplicates
        $validator->addAllowedDomain('newdomain.com');
        $domains = $validator->getAllowedDomains();
        SimpleTest::assertEqual(1, count(array_filter($domains, fn($d) => $d === 'newdomain.com')), 
            "Duplicate domain should not be added twice");
    }
    
    private function testGetAllowedDomains(): void {
        $validator = $this->fixture->getEmailValidator();
        
        $domains = $validator->getAllowedDomains();
        SimpleTest::assertCount(2, $domains, "Should have 2 allowed domains");
        SimpleTest::assertContains('example.com', $domains, "example.com should be in allowed domains");
        SimpleTest::assertContains('test.org', $domains, "test.org should be in allowed domains");
    }
}

class StringUtilsTest {
    private TestFixture $fixture;
    
    public function __construct() {
        $this->fixture = new TestFixture();
    }
    
    public function runAllTests(): void {
        echo "\nRunning String Utils Tests...\n";
        echo str_repeat("-", 30) . "\n";
        
        $this->testReverse();
        $this->testUppercase();
        $this->testLowercase();
        $this->testCapitalize();
        $this->testTruncate();
        $this->testWordCount();
        $this->testContains();
        $this->testStartsWith();
        $this->testEndsWith();
    }
    
    private function testReverse(): void {
        $utils = $this->fixture->getStringUtils();
        
        // Test with data provider
        foreach (DataProvider::stringProvider() as [$input, $expected]) {
            SimpleTest::assertEqual($expected, $utils->reverse($input), 
                "Reverse of '$input' should be '$expected'");
        }
    }
    
    private function testUppercase(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertEqual('HELLO', $utils->uppercase('hello'), "hello -> HELLO");
        SimpleTest::assertEqual('WORLD', $utils->uppercase('World'), "World -> WORLD");
        SimpleTest::assertEqual('', $utils->uppercase(''), "empty string -> empty string");
    }
    
    private function testLowercase(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertEqual('hello', $utils->lowercase('HELLO'), "HELLO -> hello");
        SimpleTest::assertEqual('world', $utils->lowercase('World'), "World -> world");
        SimpleTest::assertEqual('', $utils->lowercase(''), "empty string -> empty string");
    }
    
    private function testCapitalize(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertEqual('Hello World', $utils->capitalize('hello world'), "hello world -> Hello World");
        SimpleTest::assertEqual('Test String', $utils->capitalize('TEST STRING'), "TEST STRING -> Test String");
        SimpleTest::assertEqual('', $utils->capitalize(''), "empty string -> empty string");
    }
    
    private function testTruncate(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertEqual('Hello...', $utils->truncate('Hello World', 8), "Truncate with default suffix");
        SimpleTest::assertEqual('Hello---', $utils->truncate('Hello World', 8, '---'), "Truncate with custom suffix");
        SimpleTest::assertEqual('Hello', $utils->truncate('Hello', 10), "String shorter than limit");
        SimpleTest::assertEqual('Hello World', $utils->truncate('Hello World', 11), "String equal to limit");
    }
    
    private function testWordCount(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertEqual(2, $utils->wordCount('Hello world'), "Hello world has 2 words");
        SimpleTest::assertEqual(1, $utils->wordCount('Hello'), "Hello has 1 word");
        SimpleTest::assertEqual(0, $utils->wordCount(''), "Empty string has 0 words");
        SimpleTest::assertEqual(5, $utils->wordCount('This is a test string'), "5 words");
    }
    
    private function testContains(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertTrue($utils->contains('Hello world', 'world'), "Contains 'world'");
        SimpleTest::assertTrue($utils->contains('Hello world', 'Hello'), "Contains 'Hello'");
        SimpleTest::assertFalse($utils->contains('Hello world', 'test'), "Does not contain 'test'");
        SimpleTest::assertFalse($utils->contains('', 'test'), "Empty string doesn't contain anything");
    }
    
    private function testStartsWith(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertTrue($utils->startsWith('Hello world', 'Hello'), "Starts with 'Hello'");
        SimpleTest::assertFalse($utils->startsWith('Hello world', 'world'), "Does not start with 'world'");
        SimpleTest::assertTrue($utils->startsWith('test', 'test'), "String starts with itself");
        SimpleTest::assertFalse($utils->startsWith('', 'test'), "Empty string doesn't start with anything");
    }
    
    private function testEndsWith(): void {
        $utils = $this->fixture->getStringUtils();
        
        SimpleTest::assertTrue($utils->endsWith('Hello world', 'world'), "Ends with 'world'");
        SimpleTest::assertFalse($utils->endsWith('Hello world', 'Hello'), "Does not end with 'Hello'");
        SimpleTest::assertTrue($utils->endsWith('test', 'test'), "String ends with itself");
        SimpleTest::assertFalse($utils->endsWith('', 'test'), "Empty string doesn't end with anything");
    }
}

// Main test runner
function runAllTests(): void {
    SimpleTest::reset();
    
    echo "Unit Testing Examples\n";
    echo str_repeat("=", 50) . "\n\n";
    
    // Run all test suites
    $calculatorTest = new CalculatorTest();
    $calculatorTest->runAllTests();
    
    $emailTest = new EmailValidatorTest();
    $emailTest->runAllTests();
    
    $stringTest = new StringUtilsTest();
    $stringTest->runAllTests();
    
    // Print summary
    SimpleTest::printSummary();
}

// Run the tests
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAllTests();
}
?>
