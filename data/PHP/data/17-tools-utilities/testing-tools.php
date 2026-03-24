<?php
/**
 * PHP Testing Tools and Frameworks
 * 
 * This file demonstrates testing frameworks, code coverage,
 * mocking tools, and testing best practices.
 */

// PHPUnit Test Framework Integration
class PHPUnitIntegration
{
    private array $config;
    private array $testSuites;
    private array $coverage;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'bootstrap' => 'tests/bootstrap.php',
            'test_directory' => 'tests',
            'coverage_directory' => 'coverage',
            'coverage_format' => 'html',
            'colors' => true,
            'stop_on_failure' => false,
            'verbose' => true
        ], $config);
        
        $this->testSuites = [];
        $this->coverage = [];
    }
    
    /**
     * Create test suite
     */
    public function createTestSuite(string $name, array $files = [], array $options = []): void
    {
        $this->testSuites[$name] = [
            'name' => $name,
            'files' => $files,
            'options' => array_merge([
                'backupGlobals' => false,
                'backupStaticAttributes' => false,
                'runTestsInSeparateProcesses' => false,
                'processIsolation' => false
            ], $options)
        ];
    }
    
    /**
     * Generate PHPUnit configuration
     */
    public function generateConfig(): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<phpunit\n";
        $xml .= "    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
        $xml .= "    xsi:noNamespaceSchemaLocation=\"https://schema.phpunit.de/9.3/phpunit.xsd\"\n";
        $xml .= "    bootstrap=\"{$this->config['bootstrap']}\"\n";
        $xml .= "    colors=\"" . ($this->config['colors'] ? 'true' : 'false') . "\"\n";
        $xml .= "    stopOnFailure=\"" . ($this->config['stop_on_failure'] ? 'true' : 'false') . "\"\n";
        $xml .= "    verbose=\"" . ($this->config['verbose'] ? 'true' : 'false') . "\"\n";
        
        if (isset($this->config['coverage'])) {
            $xml .= "    cacheDirectory=\".phpunit.result.cache\"\n";
            $xml .= "    executionOrder=\"depends,defects\"\n";
            $xml .= "    forceCoversAnnotation=\"true\"\n";
            $xml .= "    beStrictAboutCoversAnnotation=\"true\"\n";
            $xml .= "    beStrictAboutOutputDuringTests=\"true\"\n";
            $xml .= "    beStrictAboutTodoAnnotatedTests=\"true\"\n";
            $xml .= "    failOnRisky=\"true\"\n";
            $xml .= "    failOnWarning=\"true\"\n";
        }
        
        $xml .= ">\n";
        
        // Test suites
        if (!empty($this->testSuites)) {
            $xml .= "    <testsuites>\n";
            
            foreach ($this->testSuites as $suite) {
                $xml .= "        <testsuite name=\"{$suite['name']}\">\n";
                
                foreach ($suite['files'] as $file) {
                    $xml .= "            <file>{$file}</file>\n";
                }
                
                $xml .= "        </testsuite>\n";
            }
            
            $xml .= "    </testsuites>\n";
        }
        
        // Source directory
        $xml .= "    <source include=\"./src\">\n";
        $xml .= "        <exclude>\n";
        $xml .= "            <directory>./vendor</directory>\n";
        $xml .= "        </exclude>\n";
        $xml .= "    </source>\n";
        
        // Coverage
        if (isset($this->config['coverage'])) {
            $xml .= "    <coverage processUncoveredFiles=\"true\">\n";
            $xml .= "        <include>\n";
            $xml .= "            <directory suffix=\".php\">./src</directory>\n";
            $xml .= "        </include>\n";
            $xml .= "        <exclude>\n";
            $xml .= "            <directory>./vendor</directory>\n";
            $xml .= "            <directory>./tests</directory>\n";
            $xml .= "        </exclude>\n";
            $xml .= "        <report>\n";
            $xml .= "            <html outputDirectory=\"{$this->config['coverage_directory']}\"/>\n";
            $xml .= "            <text outputFile=\"coverage.txt\"/>\n";
            $xml .= "            <clover outputFile=\"coverage.xml\"/>\n";
            $xml .= "        </report>\n";
            $xml .= "    </coverage>\n";
        }
        
        // Logging
        $xml .= "    <logging>\n";
        $xml .= "        <junit outputFile=\"results.junit.xml\"/>\n";
        $xml .= "        <teamcity outputFile=\"results.teamcity.txt\"/>\n";
        $xml .= "    </logging>\n";
        
        $xml .= "</phpunit>\n";
        
        return $xml;
    }
    
    /**
     * Run tests
     */
    public function runTests(string $suite = null): array
    {
        $command = './vendor/bin/phpunit';
        
        if ($suite && isset($this->testSuites[$suite])) {
            $command .= " --testsuite {$suite}";
        }
        
        if (isset($this->config['coverage'])) {
            $command .= " --coverage-html {$this->config['coverage_directory']}";
        }
        
        // Simulate test execution
        return [
            'command' => $command,
            'status' => 'success',
            'tests' => $this->simulateTestResults($suite),
            'coverage' => $this->simulateCoverage()
        ];
    }
    
    /**
     * Simulate test results
     */
    private function simulateTestResults(string $suite = null): array
    {
        $tests = [
            'unit' => [
                ['name' => 'UserTest::testCreateUser', 'status' => 'passed', 'time' => 0.05],
                ['name' => 'UserTest::testUpdateUser', 'status' => 'passed', 'time' => 0.03],
                ['name' => 'UserTest::testDeleteUser', 'status' => 'failed', 'time' => 0.02, 'error' => 'Assertion failed'],
                ['name' => 'EmailServiceTest::testSendEmail', 'status' => 'passed', 'time' => 0.08],
                ['name' => 'EmailServiceTest::testValidateEmail', 'status' => 'passed', 'time' => 0.01]
            ],
            'integration' => [
                ['name' => 'UserIntegrationTest::testUserRegistration', 'status' => 'passed', 'time' => 0.15],
                ['name' => 'UserIntegrationTest::testUserLogin', 'status' => 'passed', 'time' => 0.12],
                ['name' => 'OrderIntegrationTest::testCreateOrder', 'status' => 'passed', 'time' => 0.20]
            ],
            'feature' => [
                ['name' => 'UserRegistrationFeatureTest::testCompleteRegistration', 'status' => 'passed', 'time' => 0.45],
                ['name' => 'UserLoginFeatureTest::testLoginFlow', 'status' => 'passed', 'time' => 0.38]
            ]
        ];
        
        return $suite ? ($tests[$suite] ?? []) : array_merge(...array_values($tests));
    }
    
    /**
     * Simulate coverage data
     */
    private function simulateCoverage(): array
    {
        return [
            'lines' => 85.5,
            'functions' => 92.3,
            'classes' => 88.7,
            'methods' => 90.1,
            'branches' => 82.4
        ];
    }
}

// Test Data Builder Pattern
class TestDataBuilder
{
    private array $data = [];
    private string $class;
    
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->data = $this->getDefaultData($class);
    }
    
    /**
     * Set property value
     */
    public function with(string $property, $value): self
    {
        $this->data[$property] = $value;
        return $this;
    }
    
    /**
     * Set multiple properties
     */
    public function withMany(array $properties): self
    {
        foreach ($properties as $property => $value) {
            $this->data[$property] = $value;
        }
        return $this;
    }
    
    /**
     * Build the object
     */
    public function build()
    {
        if (class_exists($this->class)) {
            return new $this->class($this->data);
        }
        
        return $this->data;
    }
    
    /**
     * Build multiple objects
     */
    public function buildMany(int $count): array
    {
        $objects = [];
        
        for ($i = 0; $i < $count; $i++) {
            $objects[] = $this->build();
        }
        
        return $objects;
    }
    
    /**
     * Get default data for class
     */
    private function getDefaultData(string $class): array
    {
        $defaults = [
            'User' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'Product' => [
                'id' => 1,
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Test description',
                'category' => 'Test Category',
                'created_at' => date('Y-m-d H:i:s')
            ],
            'Order' => [
                'id' => 1,
                'user_id' => 1,
                'total' => 199.98,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        return $defaults[$class] ?? [];
    }
}

// Mock Object Generator
class MockObjectGenerator
{
    private array $mocks = [];
    
    /**
     * Create mock object
     */
    public function createMock(string $class, array $methods = []): object
    {
        $mockName = $class . '_Mock_' . uniqid();
        
        $classDefinition = "class $mockName implements $class {\n";
        
        // Mock methods
        foreach ($methods as $method => $returnValue) {
            $classDefinition .= "    public function $method() {\n";
            $classDefinition .= "        return " . var_export($returnValue, true) . ";\n";
            $classDefinition .= "    }\n";
        }
        
        // Default methods
        $classDefinition .= "    public function __call(\$method, \$args) {\n";
        $classDefinition .= "        return null;\n";
        $classDefinition .= "    }\n";
        
        $classDefinition .= "}\n";
        
        // Evaluate the class definition
        eval($classDefinition);
        
        $this->mocks[$mockName] = new $mockName();
        
        return $this->mocks[$mockName];
    }
    
    /**
     * Create mock with expectations
     */
    public function createMockWithExpectations(string $class, array $expectations): object
    {
        $mockName = $class . '_Mock_' . uniqid();
        
        $classDefinition = "class $mockName implements $class {\n";
        $classDefinition .= "    private \$expectations = " . var_export($expectations, true) . ";\n";
        $classDefinition .= "    private \$calls = [];\n";
        
        foreach ($expectations as $method => $config) {
            $classDefinition .= "    public function $method() {\n";
            $classDefinition .= "        \$this->calls['$method'][] = func_get_args();\n";
            $classDefinition .= "        return \$this->expectations['$method']['return'] ?? null;\n";
            $classDefinition .= "    }\n";
        }
        
        $classDefinition .= "    public function __call(\$method, \$args) {\n";
        $classDefinition .= "        \$this->calls[\$method][] = \$args;\n";
        $classDefinition .= "        return null;\n";
        $classDefinition .= "    }\n";
        
        $classDefinition .= "    public function getCalls(\$method) {\n";
        $classDefinition .= "        return \$this->calls[\$method] ?? [];\n";
        $classDefinition .= "    }\n";
        
        $classDefinition .= "    public function wasCalled(\$method) {\n";
        $classDefinition .= "        return !empty(\$this->calls[\$method]);\n";
        $classDefinition .= "    }\n";
        
        $classDefinition .= "}\n";
        
        eval($classDefinition);
        
        return new $mockName();
    }
    
    /**
     * Create stub object
     */
    public function createStub(string $class, array $methods = []): object
    {
        $stubName = $class . '_Stub_' . uniqid();
        
        $classDefinition = "class $stubName implements $class {\n";
        
        foreach ($methods as $method => $returnValue) {
            $classDefinition .= "    public function $method() {\n";
            $classDefinition .= "        return " . var_export($returnValue, true) . ";\n";
            $classDefinition .= "    }\n";
        }
        
        $classDefinition .= "    public function __call(\$method, \$args) {\n";
        $classDefinition .= "        return null;\n";
        $classDefinition .= "    }\n";
        
        $classDefinition .= "}\n";
        
        eval($classDefinition);
        
        return new $stubName();
    }
}

// Test Database Manager
class TestDatabaseManager
{
    private string $database;
    private array $connections = [];
    private array $migrations = [];
    
    public function __construct(string $database = 'test_db')
    {
        $this->database = $database;
    }
    
    /**
     * Create in-memory database
     */
    public function createInMemoryDatabase(): void
    {
        try {
            $pdo = new PDO('sqlite::memory:');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->connections['memory'] = $pdo;
            
            echo "In-memory database created\n";
        } catch (PDOException $e) {
            echo "Failed to create in-memory database: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Create test database
     */
    public function createTestDatabase(): void
    {
        try {
            $pdo = new PDO('sqlite::memory:');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->connections['test'] = $pdo;
            
            $this->createTables($pdo);
            
            echo "Test database created\n";
        } catch (PDOException $e) {
            echo "Failed to create test database: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Create tables
     */
    private function createTables(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("
            CREATE TABLE products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("
            CREATE TABLE orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
    }
    
    /**
     * Get database connection
     */
    public function getConnection(string $type = 'test'): ?PDO
    {
        return $this->connections[$type] ?? null;
    }
    
    /**
     * Seed database with test data
     */
    public function seedDatabase(): void
    {
        $pdo = $this->getConnection();
        
        if (!$pdo) {
            return;
        }
        
        // Insert test users
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        
        $users = [
            ['John Doe', 'john@example.com', 'password123'],
            ['Jane Smith', 'jane@example.com', 'password456'],
            ['Bob Johnson', 'bob@example.com', 'password789']
        ];
        
        foreach ($users as $user) {
            $stmt->execute($user);
        }
        
        // Insert test products
        $stmt = $pdo->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
        
        $products = [
            ['Test Product 1', 99.99, 'Description for product 1'],
            ['Test Product 2', 149.99, 'Description for product 2'],
            ['Test Product 3', 199.99, 'Description for product 3']
        ];
        
        foreach ($products as $product) {
            $stmt->execute($product);
        }
        
        echo "Database seeded with test data\n";
    }
    
    /**
     * Clean database
     */
    public function cleanDatabase(): void
    {
        $pdo = $this->getConnection();
        
        if (!$pdo) {
            return;
        }
        
        $tables = ['orders', 'products', 'users'];
        
        foreach ($tables as $table) {
            $pdo->exec("DELETE FROM $table");
        }
        
        echo "Database cleaned\n";
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): void
    {
        $pdo = $this->getConnection();
        
        if ($pdo) {
            $pdo->beginTransaction();
        }
    }
    
    /**
     * Rollback transaction
     */
    public function rollbackTransaction(): void
    {
        $pdo = $this->getConnection();
        
        if ($pdo) {
            $pdo->rollBack();
        }
    }
    
    /**
     * Commit transaction
     */
    public function commitTransaction(): void
    {
        $pdo = $this->getConnection();
        
        if ($pdo) {
            $pdo->commit();
        }
    }
}

// Browser Automation Testing
class BrowserAutomation
{
    private array $browsers = [];
    private array $pages = [];
    private array $elements = [];
    
    public function __construct()
    {
        $this->initializeBrowsers();
    }
    
    /**
     * Initialize browser configurations
     */
    private function initializeBrowsers(): void
    {
        $this->browsers = [
            'chrome' => [
                'name' => 'Chrome',
                'driver' => 'chromedriver',
                'binary' => '/usr/bin/google-chrome',
                'options' => [
                    'headless' => true,
                    'disable-gpu' => true,
                    'no-sandbox' => true,
                    'disable-dev-shm-usage' => true,
                    'window-size' => '1920,1080'
                ]
            ],
            'firefox' => [
                'name' => 'Firefox',
                'driver' => 'geckodriver',
                'binary' => '/usr/bin/firefox',
                'options' => [
                    'headless' => true,
                    'window-size' => '1920,1080'
                ]
            ]
        ];
    }
    
    /**
     * Create browser instance
     */
    public function createBrowser(string $type = 'chrome'): array
    {
        if (!isset($this->browsers[$type])) {
            throw new \InvalidArgumentException("Browser type '$type' not supported");
        }
        
        $browser = $this->browsers[$type];
        
        return [
            'type' => $type,
            'name' => $browser['name'],
            'driver' => $browser['driver'],
            'options' => $browser['options'],
            'session_id' => uniqid('browser_'),
            'created_at' => time()
        ];
    }
    
    /**
     * Navigate to page
     */
    public function navigateTo(array $browser, string $url): array
    {
        echo "Browser {$browser['name']} navigating to: $url\n";
        
        return [
            'browser' => $browser['session_id'],
            'url' => $url,
            'status' => 'success',
            'title' => $this->getPageTitle($url),
            'timestamp' => time()
        ];
    }
    
    /**
     * Find element
     */
    public function findElement(array $browser, string $selector, string $by = 'css'): ?array
    {
        $element = [
            'browser' => $browser['session_id'],
            'selector' => $selector,
            'by' => $by,
            'found' => true,
            'element_id' => uniqid('element_'),
            'timestamp' => time()
        ];
        
        $this->elements[$element['element_id']] = $element;
        
        return $element;
    }
    
    /**
     * Click element
     */
    public function clickElement(array $element): array
    {
        echo "Clicking element: {$element['selector']}\n";
        
        return [
            'element' => $element['element_id'],
            'action' => 'click',
            'status' => 'success',
            'timestamp' => time()
        ];
    }
    
    /**
     * Type text
     */
    public function typeText(array $element, string $text): array
    {
        echo "Typing text: '$text' into element: {$element['selector']}\n";
        
        return [
            'element' => $element['element_id'],
            'action' => 'type',
            'text' => $text,
            'status' => 'success',
            'timestamp' => time()
        ];
    }
    
    /**
     * Get element text
     */
    public function getElementText(array $element): string
    {
        echo "Getting text from element: {$element['selector']}\n";
        
        return "Sample text from {$element['selector']}";
    }
    
    /**
     * Wait for element
     */
    public function waitForElement(array $browser, string $selector, int $timeout = 10): bool
    {
        echo "Waiting for element: $selector (timeout: {$timeout}s)\n";
        
        // Simulate waiting
        sleep(1);
        
        return true;
    }
    
    /**
     * Take screenshot
     */
    public function takeScreenshot(array $browser, string $filename): array
    {
        echo "Taking screenshot: $filename\n";
        
        return [
            'browser' => $browser['session_id'],
            'filename' => $filename,
            'status' => 'success',
            'timestamp' => time()
        ];
    }
    
    /**
     * Execute JavaScript
     */
    public function executeScript(array $browser, string $script): mixed
    {
        echo "Executing JavaScript: $script\n";
        
        // Simulate script execution
        if ($script === 'return document.title;') {
            return 'Page Title';
        }
        
        return null;
    }
    
    /**
     * Get page title
     */
    private function getPageTitle(string $url): string
    {
        $titles = [
            'https://example.com' => 'Example Domain',
            'https://google.com' => 'Google',
            'https://github.com' => 'GitHub'
        ];
        
        return $titles[$url] ?? 'Unknown Page';
    }
}

// Performance Testing
class PerformanceTesting
{
    private array $tests = [];
    private array $results = [];
    
    /**
     * Create performance test
     */
    public function createTest(string $name, callable $test, array $options = []): void
    {
        $this->tests[$name] = [
            'name' => $name,
            'test' => $test,
            'options' => array_merge([
                'iterations' => 100,
                'warmup' => 10,
                'memory_limit' => '128M',
                'timeout' => 30
            ], $options)
        ];
    }
    
    /**
     * Run performance test
     */
    public function runTest(string $name): array
    {
        if (!isset($this->tests[$name])) {
            throw new \InvalidArgumentException("Test '$name' not found");
        }
        
        $test = $this->tests[$name];
        $iterations = $test['options']['iterations'];
        $warmup = $test['options']['warmup'];
        
        // Warmup
        for ($i = 0; $i < $warmup; $i++) {
            $test['test']();
        }
        
        // Measure memory before
        $memoryBefore = memory_get_usage(true);
        
        // Run test iterations
        $times = [];
        $start = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $iterationStart = microtime(true);
            $test['test']();
            $times[] = microtime(true) - $iterationStart;
        }
        
        $totalTime = microtime(true) - $start;
        
        // Measure memory after
        $memoryAfter = memory_get_usage(true);
        
        // Calculate statistics
        $result = [
            'name' => $name,
            'iterations' => $iterations,
            'total_time' => $totalTime,
            'average_time' => $totalTime / $iterations,
            'min_time' => min($times),
            'max_time' => max($times),
            'median_time' => $this->calculateMedian($times),
            'memory_used' => $memoryAfter - $memoryBefore,
            'memory_peak' => memory_get_peak_usage(true),
            'throughput' => $iterations / $totalTime
        ];
        
        $this->results[$name] = $result;
        
        return $result;
    }
    
    /**
     * Calculate median
     */
    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        
        if ($count % 2 === 0) {
            return ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
        } else {
            return $values[floor($count / 2)];
        }
    }
    
    /**
     * Compare tests
     */
    public function compareTests(array $testNames): array
    {
        $comparison = [];
        
        foreach ($testNames as $name) {
            if (isset($this->results[$name])) {
                $comparison[$name] = $this->results[$name];
            }
        }
        
        // Sort by average time
        uasort($comparison, function($a, $b) {
            return $a['average_time'] <=> $b['average_time'];
        });
        
        return $comparison;
    }
    
    /**
     * Generate performance report
     */
    public function generateReport(array $testNames = null): string
    {
        $results = $testNames ? 
            array_intersect_key($this->results, array_flip($testNames)) : 
            $this->results;
        
        $report = "Performance Test Report\n";
        $report .= str_repeat("=", 25) . "\n\n";
        
        foreach ($results as $name => $result) {
            $report .= "Test: $name\n";
            $report .= str_repeat("-", strlen($name) + 6) . "\n";
            $report .= "Iterations: {$result['iterations']}\n";
            $report .= "Total Time: " . round($result['total_time'], 4) . "s\n";
            $report .= "Average Time: " . round($result['average_time'] * 1000, 2) . "ms\n";
            $report .= "Min Time: " . round($result['min_time'] * 1000, 2) . "ms\n";
            $report .= "Max Time: " . round($result['max_time'] * 1000, 2) . "ms\n";
            $report .= "Median Time: " . round($result['median_time'] * 1000, 2) . "ms\n";
            $report .= "Memory Used: " . round($result['memory_used'] / 1024, 2) . "KB\n";
            $report .= "Memory Peak: " . round($result['memory_peak'] / 1024, 2) . "KB\n";
            $report .= "Throughput: " . round($result['throughput'], 2) . " ops/s\n\n";
        }
        
        return $report;
    }
}

// Testing Tools Examples
class TestingToolsExamples
{
    private PHPUnitIntegration $phpunit;
    private TestDataBuilder $dataBuilder;
    private MockObjectGenerator $mockGenerator;
    private TestDatabaseManager $testDatabase;
    private BrowserAutomation $browserAutomation;
    private PerformanceTesting $performanceTesting;
    
    public function __construct()
    {
        $this->phpunit = new PHPUnitIntegration();
        $this->dataBuilder = new TestDataBuilder('User');
        $this->mockGenerator = new MockObjectGenerator();
        $this->testDatabase = new TestDatabaseManager();
        $this->browserAutomation = new BrowserAutomation();
        $this->performanceTesting = new PerformanceTesting();
    }
    
    public function demonstratePHPUnit(): void
    {
        echo "PHPUnit Integration Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create test suites
        $this->phpunit->createTestSuite('unit', [
            'tests/Unit/UserTest.php',
            'tests/Unit/EmailServiceTest.php'
        ]);
        
        $this->phpunit->createTestSuite('integration', [
            'tests/Integration/UserIntegrationTest.php',
            'tests/Integration/OrderIntegrationTest.php'
        ]);
        
        // Generate configuration
        $config = $this->phpunit->generateConfig();
        echo "PHPUnit Configuration:\n";
        echo substr($config, 0, 500) . "...\n\n";
        
        // Run tests
        $results = $this->phpunit->runTests('unit');
        echo "Test Results:\n";
        echo "Status: {$results['status']}\n";
        echo "Tests run: " . count($results['tests']) . "\n";
        
        $passed = count(array_filter($results['tests'], fn($t) => $t['status'] === 'passed'));
        $failed = count(array_filter($results['tests'], fn($t) => $t['status'] === 'failed'));
        
        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        
        // Coverage
        $coverage = $results['coverage'];
        echo "\nCode Coverage:\n";
        echo "Lines: {$coverage['lines']}%\n";
        echo "Functions: {$coverage['functions']}%\n";
        echo "Classes: {$coverage['classes']}%\n";
    }
    
    public function demonstrateDataBuilder(): void
    {
        echo "\nTest Data Builder Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create user builder
        $userBuilder = new TestDataBuilder('User');
        
        // Build single user
        $user = $userBuilder
            ->with('name', 'Jane Smith')
            ->with('email', 'jane@example.com')
            ->build();
        
        echo "Single User:\n";
        echo json_encode($user, JSON_PRETTY_PRINT) . "\n\n";
        
        // Build multiple users
        $users = $userBuilder
            ->with('name', 'John Doe')
            ->with('email', 'john@example.com')
            ->buildMany(3);
        
        echo "Multiple Users (" . count($users) . "):\n";
        foreach ($users as $index => $u) {
            echo "User " . ($index + 1) . ": {$u['name']} - {$u['email']}\n";
        }
        
        // Product builder
        $productBuilder = new TestDataBuilder('Product');
        $product = $productBuilder
            ->with('name', 'Premium Product')
            ->with('price', 299.99)
            ->withMany([
                'description' => 'High quality product',
                'category' => 'Premium'
            ])
            ->build();
        
        echo "\nProduct:\n";
        echo json_encode($product, JSON_PRETTY_PRINT) . "\n";
    }
    
    public function demonstrateMocking(): void
    {
        echo "\nMock Object Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create simple mock
        $emailServiceMock = $this->mockGenerator->createMock('EmailService', [
            'sendEmail' => true,
            'validateEmail' => true
        ]);
        
        echo "Simple Mock:\n";
        echo "sendEmail(): " . ($emailServiceMock->sendEmail() ? 'true' : 'false') . "\n";
        echo "validateEmail(): " . ($emailServiceMock->validateEmail() ? 'true' : 'false') . "\n\n";
        
        // Create mock with expectations
        $userRepositoryMock = $this->mockGenerator->createMockWithExpectations('UserRepository', [
            'findById' => ['return' => ['id' => 1, 'name' => 'John Doe']],
            'save' => ['return' => true],
            'delete' => ['return' => true]
        ]);
        
        echo "Mock with Expectations:\n";
        $user = $userRepositoryMock->findById(1);
        echo "findById(1): " . json_encode($user) . "\n";
        echo "save(): " . ($userRepositoryMock->save() ? 'true' : 'false') . "\n";
        echo "wasCalled('findById'): " . ($userRepositoryMock->wasCalled('findById') ? 'true' : 'false') . "\n";
        
        $calls = $userRepositoryMock->getCalls('findById');
        echo "getCalls('findById'): " . json_encode($calls) . "\n\n";
        
        // Create stub
        $paymentGatewayStub = $this->mockGenerator->createStub('PaymentGateway', [
            'processPayment' => ['status' => 'success', 'transaction_id' => '12345']
        ]);
        
        echo "Stub Example:\n";
        $result = $paymentGatewayStub->processPayment(100);
        echo "processPayment(100): " . json_encode($result) . "\n";
    }
    
    public function demonstrateTestDatabase(): void
    {
        echo "\nTest Database Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create test database
        $this->testDatabase->createTestDatabase();
        
        // Seed database
        $this->testDatabase->seedDatabase();
        
        // Test database operations
        $pdo = $this->testDatabase->getConnection();
        
        if ($pdo) {
            // Query users
            $stmt = $pdo->query("SELECT * FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Users in database: " . count($users) . "\n";
            foreach ($users as $user) {
                echo "  - {$user['name']} ({$user['email']})\n";
            }
            
            // Insert test record
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute(['Test User', 'test@example.com', 'password']);
            
            echo "\nInserted test user\n";
            
            // Transaction test
            $this->testDatabase->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute(['Updated User', 1]);
            
            echo "Updated user in transaction\n";
            
            $this->testDatabase->rollbackTransaction();
            echo "Transaction rolled back\n";
        }
        
        // Clean database
        $this->testDatabase->cleanDatabase();
    }
    
    public function demonstrateBrowserAutomation(): void
    {
        echo "\nBrowser Automation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create browser
        $browser = $this->browserAutomation->createBrowser('chrome');
        
        echo "Browser created: {$browser['name']}\n";
        echo "Session ID: {$browser['session_id']}\n\n";
        
        // Navigate to page
        $page = $this->browserAutomation->navigateTo($browser, 'https://example.com');
        echo "Page title: {$page['title']}\n\n";
        
        // Find and interact with elements
        $searchBox = $this->browserAutomation->findElement($page, '#search', 'css');
        $this->browserAutomation->typeText($searchBox, 'test search');
        $this->browserAutomation->clickElement($searchBox);
        
        // Take screenshot
        $screenshot = $this->browserAutomation->takeScreenshot($browser, 'test.png');
        echo "Screenshot saved: {$screenshot['filename']}\n\n";
        
        // Execute JavaScript
        $title = $this->browserAutomation->executeScript($browser, 'return document.title;');
        echo "Page title from JavaScript: $title\n";
    }
    
    public function demonstratePerformanceTesting(): void
    {
        echo "\nPerformance Testing Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create performance tests
        $this->performanceTesting->createTest('string_concatenation', function() {
            $result = '';
            for ($i = 0; $i < 100; $i++) {
                $result .= 'test';
            }
            return $result;
        });
        
        $this->performanceTesting->createTest('array_operations', function() {
            $array = range(1, 1000);
            return array_sum($array);
        });
        
        $this->performanceTesting->createTest('database_query', function() {
            $pdo = new PDO('sqlite::memory:');
            $pdo->exec("CREATE TABLE test (id INTEGER, value TEXT)");
            
            for ($i = 0; $i < 100; $i++) {
                $pdo->exec("INSERT INTO test (id, value) VALUES ($i, 'value_$i')");
            }
            
            return $pdo->query("SELECT COUNT(*) FROM test")->fetchColumn();
        });
        
        // Run tests
        $stringTest = $this->performanceTesting->runTest('string_concatenation');
        echo "String Concatenation Test:\n";
        echo "Average: " . round($stringTest['average_time'] * 1000, 2) . "ms\n";
        echo "Throughput: " . round($stringTest['throughput'], 2) . " ops/s\n\n";
        
        $arrayTest = $this->performanceTesting->runTest('array_operations');
        echo "Array Operations Test:\n";
        echo "Average: " . round($arrayTest['average_time'] * 1000, 2) . "ms\n";
        echo "Throughput: " . round($arrayTest['throughput'], 2) . " ops/s\n\n";
        
        $dbTest = $this->performanceTesting->runTest('database_query');
        echo "Database Query Test:\n";
        echo "Average: " . round($dbTest['average_time'] * 1000, 2) . "ms\n";
        echo "Throughput: " . round($dbTest['throughput'], 2) . " ops/s\n";
        
        // Compare tests
        $comparison = $this->performanceTesting->compareTests(['string_concatenation', 'array_operations', 'database_query']);
        
        echo "\nPerformance Comparison:\n";
        foreach ($comparison as $name => $result) {
            echo "$name: " . round($result['average_time'] * 1000, 2) . "ms\n";
        }
        
        // Generate report
        echo "\n" . $this->performanceTesting->generateReport();
    }
    
    public function runAllExamples(): void
    {
        echo "PHP Testing Tools Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstratePHPUnit();
        $this->demonstrateDataBuilder();
        $this->demonstrateMocking();
        $this->demonstrateTestDatabase();
        $this->demonstrateBrowserAutomation();
        $this->demonstratePerformanceTesting();
    }
}

// Testing Tools Best Practices
function printTestingToolsBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing Tools Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Unit Testing:\n";
    echo "   • Test one thing at a time\n";
    echo "   • Use descriptive test names\n";
    echo "   • Arrange-Act-Assert pattern\n";
    echo "   • Use test data builders\n";
    echo "   • Keep tests independent\n\n";
    
    echo "2. Mocking and Stubbing:\n";
    echo "   • Mock external dependencies\n";
    echo "   • Use mocks for behavior verification\n";
    echo "   • Use stubs for state verification\n";
    echo "   • Avoid over-mocking\n";
    echo "   • Verify mock interactions\n\n";
    
    echo "3. Test Data Management:\n";
    echo "   • Use factories and builders\n";
    echo "   • Keep test data minimal\n";
    echo "   • Use in-memory databases\n";
    echo "   • Clean up after tests\n";
    echo "   • Use transactions for isolation\n\n";
    
    echo "4. Integration Testing:\n";
    echo "   • Test component interactions\n";
    echo "   • Use realistic test data\n";
    echo "   • Test error scenarios\n";
    echo "   • Monitor performance\n";
    echo "   • Use proper assertions\n\n";
    
    echo "5. Browser Testing:\n";
    echo "   • Automate repetitive tasks\n";
    echo "   • Test user workflows\n";
    echo "   • Use page object pattern\n";
    echo "   • Handle dynamic content\n";
    echo "   • Take screenshots on failure\n\n";
    
    echo "6. Performance Testing:\n";
    echo "   • Measure critical paths\n";
    echo "   • Use realistic data sizes\n";
    echo "   • Monitor memory usage\n";
    echo "   • Compare implementations\n";
    echo "   • Set performance baselines";
}

// Main execution
function runTestingToolsDemo(): void
{
    $examples = new TestingToolsExamples();
    $examples->runAllExamples();
    printTestingToolsBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runTestingToolsDemo();
}
?>
