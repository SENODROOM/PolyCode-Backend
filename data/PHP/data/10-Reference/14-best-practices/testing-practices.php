<?php
/**
 * Testing Practices and Test-Driven Development
 * 
 * This file demonstrates testing best practices, TDD workflows,
 * testing patterns, and comprehensive test strategies.
 */

// Simple Test Framework
class SimpleTestFramework
{
    private array $tests = [];
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;
    
    public function test(string $description, callable $test): void
    {
        $this->tests[] = $description;
        
        try {
            $test();
            $this->results[$description] = ['status' => 'passed', 'message' => ''];
            $this->passed++;
        } catch (\Exception $e) {
            $this->results[$description] = ['status' => 'failed', 'message' => $e->getMessage()];
            $this->failed++;
        }
    }
    
    public function assertEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $error = "Expected: " . var_export($expected, true) . ", Actual: " . var_export($actual, true);
            if ($message) {
                $error .= " - $message";
            }
            throw new \Exception($error);
        }
    }
    
    public function assertTrue(bool $condition, string $message = ''): void
    {
        if (!$condition) {
            throw new \Exception($message ?: 'Assertion failed: expected true');
        }
    }
    
    public function assertFalse(bool $condition, string $message = ''): void
    {
        if ($condition) {
            throw new \Exception($message ?: 'Assertion failed: expected false');
        }
    }
    
    public function assertNull(mixed $value, string $message = ''): void
    {
        if ($value !== null) {
            throw new \Exception($message ?: 'Assertion failed: expected null');
        }
    }
    
    public function assertNotNull(mixed $value, string $message = ''): void
    {
        if ($value === null) {
            throw new \Exception($message ?: 'Assertion failed: expected not null');
        }
    }
    
    public function assertCount(int $expected, array $array, string $message = ''): void
    {
        if (count($array) !== $expected) {
            throw new \Exception($message ?: "Expected count $expected, got " . count($array));
        }
    }
    
    public function assertContains(mixed $needle, array $haystack, string $message = ''): void
    {
        if (!in_array($needle, $haystack, true)) {
            throw new \Exception($message ?: 'Assertion failed: needle not found in haystack');
        }
    }
    
    public function run(): array
    {
        foreach ($this->results as $test => $result) {
            $status = $result['status'] === 'passed' ? '✅' : '❌';
            echo "$status $test";
            
            if ($result['status'] === 'failed') {
                echo " - {$result['message']}";
            }
            echo "\n";
        }
        
        echo "\nResults: {$this->passed} passed, {$this->failed} failed\n";
        
        return [
            'total' => $this->passed + $this->failed,
            'passed' => $this->passed,
            'failed' => $this->failed,
            'success_rate' => $this->passed + $this->failed > 0 ? round(($this->passed / ($this->passed + $this->failed)) * 100, 2) : 0
        ];
    }
}

// Test-Driven Development Example
class TDDExample
{
    private SimpleTestFramework $test;
    
    public function __construct()
    {
        $this->test = new SimpleTestFramework();
    }
    
    public function demonstrateTDDWorkflow(): void
    {
        echo "Test-Driven Development Workflow\n";
        echo str_repeat("-", 35) . "\n";
        
        // Step 1: Write failing test (Red)
        echo "Step 1: Write failing test (Red)\n";
        $this->test->test('Calculator should add two numbers', function() {
            $calculator = new Calculator();
            $result = $calculator->add(2, 3);
            $this->test->assertEqual(5, $result);
        });
        
        // Step 2: Make it pass (Green)
        echo "\nStep 2: Make it pass (Green)\n";
        $this->test->test('Calculator should add two numbers - after implementation', function() {
            $calculator = new Calculator();
            $result = $calculator->add(2, 3);
            $this->test->assertEqual(5, $result);
        });
        
        // Step 3: Refactor (Refactor)
        echo "\nStep 3: Refactor (Refactor)\n";
        $this->test->test('Calculator should add multiple numbers', function() {
            $calculator = new Calculator();
            $result = $calculator->addMultiple([1, 2, 3, 4]);
            $this->test->assertEqual(10, $result);
        });
        
        $this->test->run();
    }
}

// Unit Testing Example
class UnitTestingExample
{
    private SimpleTestFramework $test;
    
    public function __construct()
    {
        $this->test = new SimpleTestFramework();
    }
    
    public function demonstrateUnitTesting(): void
    {
        echo "Unit Testing Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Test User class
        $this->testUserClass();
        
        // Test EmailValidator
        $this->testEmailValidator();
        
        // Test Calculator
        $this->testCalculator();
        
        // Test ArrayHelper
        $this->testArrayHelper();
        
        $this->test->run();
    }
    
    private function testUserClass(): void
    {
        $this->test->test('User should have name and email', function() {
            $user = new User('John Doe', 'john@example.com');
            $this->test->assertEqual('John Doe', $user->getName());
            $this->test->assertEqual('john@example.com', $user->getEmail());
        });
        
        $this->test->test('User should change name', function() {
            $user = new User('John Doe', 'john@example.com');
            $user->setName('Jane Doe');
            $this->test->assertEqual('Jane Doe', $user->getName());
        });
        
        $this->test->test('User should validate email', function() {
            $user = new User('John Doe', 'invalid-email');
            $this->test->assertFalse($user->hasValidEmail());
        });
    }
    
    private function testEmailValidator(): void
    {
        $this->test->test('EmailValidator should validate valid email', function() {
            $validator = new EmailValidator();
            $this->test->assertTrue($validator->isValid('test@example.com'));
        });
        
        $this->test->test('EmailValidator should reject invalid email', function() {
            $validator = new EmailValidator();
            $this->test->assertFalse($validator->isValid('invalid-email'));
        });
        
        $this->test->test('EmailValidator should reject empty email', function() {
            $validator = new EmailValidator();
            $this->test->assertFalse($validator->isValid(''));
        });
    }
    
    private function testCalculator(): void
    {
        $this->test->test('Calculator should add numbers', function() {
            $calculator = new Calculator();
            $this->test->assertEqual(5, $calculator->add(2, 3));
        });
        
        $this->test->test('Calculator should subtract numbers', function() {
            $calculator = new Calculator();
            $this->test->assertEqual(1, $calculator->subtract(5, 4));
        });
        
        $this->test->test('Calculator should multiply numbers', function() {
            $calculator = new Calculator();
            $this->test->assertEqual(6, $calculator->multiply(2, 3));
        });
        
        $this->test->test('Calculator should divide numbers', function() {
            $calculator = new Calculator();
            $this->test->assertEqual(2, $calculator->divide(6, 3));
        });
        
        $this->test->test('Calculator should handle division by zero', function() {
            $calculator = new Calculator();
            $this->test->expectException(\DivisionByZeroError::class, function() use ($calculator) {
                $calculator->divide(5, 0);
            });
        });
    }
    
    private function testArrayHelper(): void
    {
        $this->test->test('ArrayHelper should check if array contains value', function() {
            $helper = new ArrayHelper();
            $array = [1, 2, 3, 4, 5];
            $this->test->assertTrue($helper->contains($array, 3));
            $this->test->assertFalse($helper->contains($array, 6));
        });
        
        $this->test->test('ArrayHelper should filter array', function() {
            $helper = new ArrayHelper();
            $array = [1, 2, 3, 4, 5, 6];
            $filtered = $helper->filter($array, function($item) {
                return $item % 2 === 0;
            });
            $this->test->assertEqual([2, 4, 6], array_values($filtered));
        });
        
        $this->test->test('ArrayHelper should map array', function() {
            $helper = new ArrayHelper();
            $array = [1, 2, 3];
            $mapped = $helper->map($array, function($item) {
                return $item * 2;
            });
            $this->test->assertEqual([2, 4, 6], $mapped);
        });
    }
    
    private function expectException(string $exceptionClass, callable $callback): void
    {
        $exceptionThrown = false;
        
        try {
            $callback();
        } catch (\Exception $e) {
            if ($e instanceof $exceptionClass) {
                $exceptionThrown = true;
            } else {
                throw $e;
            }
        }
        
        if (!$exceptionThrown) {
            throw new \Exception("Expected exception $exceptionClass was not thrown");
        }
    }
}

// Integration Testing Example
class IntegrationTestingExample
{
    private SimpleTestFramework $test;
    
    public function __construct()
    {
        $this->test = new SimpleTestFramework();
    }
    
    public function demonstrateIntegrationTesting(): void
    {
        echo "Integration Testing Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test User Registration Service
        $this->testUserRegistrationService();
        
        // Test Order Processing
        $this->testOrderProcessing();
        
        // Test Email Service Integration
        $this->testEmailServiceIntegration();
        
        $this->test->run();
    }
    
    private function testUserRegistrationService(): void
    {
        $this->test->test('UserRegistrationService should register user successfully', function() {
            $repository = new InMemoryUserRepository();
            $emailService = new MockEmailService();
            $service = new UserRegistrationService($repository, $emailService);
            
            $userData = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123'
            ];
            
            $user = $service->register($userData);
            
            $this->test->assertNotNull($user);
            $this->test->assertEqual('John Doe', $user->getName());
            $this->test->assertEqual('john@example.com', $user->getEmail());
            $this->test->assertNotNull($user->getId());
            
            // Verify user was saved
            $savedUser = $repository->findById($user->getId());
            $this->test->assertNotNull($savedUser);
            $this->test->assertEqual($user->getId(), $savedUser->getId());
            
            // Verify email was sent
            $this->test->assertTrue($emailService->wasWelcomeEmailSent($user));
        });
        
        $this->test->test('UserRegistrationService should reject invalid email', function() {
            $repository = new InMemoryUserRepository();
            $emailService = new MockEmailService();
            $service = new UserRegistrationService($repository, $emailService);
            
            $userData = [
                'name' => 'John Doe',
                'email' => 'invalid-email',
                'password' => 'password123'
            ];
            
            $this->test->expectException(\InvalidArgumentException::class, function() use ($service, $userData) {
                $service->register($userData);
            });
        });
    }
    
    private function testOrderProcessing(): void
    {
        $this->test->test('OrderProcessing should process order successfully', function() {
            $paymentService = new MockPaymentService();
            $inventoryService = new MockInventoryService();
            $notificationService = new MockNotificationService();
            
            $service = new OrderProcessingService($paymentService, $inventoryService, $notificationService);
            
            $orderData = [
                'user_id' => 1,
                'items' => [
                    ['product_id' => 1, 'quantity' => 2, 'price' => 10.99],
                    ['product_id' => 2, 'quantity' => 1, 'price' => 24.99]
                ]
            ];
            
            $result = $service->processOrder($orderData);
            
            $this->test->assertTrue($result['success']);
            $this->test->assertNotNull($result['order_id']);
            $this->test->assertEqual(46.97, $result['total_amount']);
            
            // Verify payment was processed
            $this->test->assertTrue($paymentService->wasPaymentProcessed($result['order_id']));
            
            // Verify inventory was updated
            $this->test->assertTrue($inventoryService->wasInventoryUpdated());
            
            // Verify notification was sent
            $this->test->assertTrue($notificationService->wasOrderNotificationSent($result['order_id']));
        });
    }
    
    private function testEmailServiceIntegration(): void
    {
        $this->test->test('EmailService should send welcome email', function() {
            $emailService = new EmailService();
            $templateService = new EmailTemplateService();
            
            $user = new User(1, 'John Doe', 'john@example.com');
            $template = $templateService->getWelcomeTemplate();
            
            $result = $emailService->sendEmail($user->getEmail(), $template['subject'], $template['body']);
            
            $this->test->assertTrue($result['sent']);
            $this->test->assertNotNull($result['message_id']);
        });
    }
}

// Test Doubles (Mocks, Stubs, Fakes)
class MockEmailService
{
    private array $sentEmails = [];
    
    public function sendWelcomeEmail(User $user): void
    {
        $this->sentEmails[] = [
            'type' => 'welcome',
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ];
    }
    
    public function wasWelcomeEmailSent(User $user): bool
    {
        foreach ($this->sentEmails as $email) {
            if ($email['type'] === 'welcome' && $email['user_id'] === $user->getId()) {
                return true;
            }
        }
        
        return false;
    }
}

class MockPaymentService
{
    private array $payments = [];
    
    public function processPayment(int $orderId, float $amount): bool
    {
        $this->payments[$orderId] = [
            'amount' => $amount,
            'status' => 'processed',
            'timestamp' => time()
        ];
        
        return true;
    }
    
    public function wasPaymentProcessed(int $orderId): bool
    {
        return isset($this->payments[$orderId]);
    }
}

class MockInventoryService
{
    private bool $updated = false;
    
    public function updateInventory(array $items): bool
    {
        $this->updated = true;
        return true;
    }
    
    public function wasInventoryUpdated(): bool
    {
        return $this->updated;
    }
}

class MockNotificationService
{
    private array $notifications = [];
    
    public function sendOrderNotification(int $orderId, int $userId): bool
    {
        $this->notifications[$orderId] = [
            'user_id' => $userId,
            'timestamp' => time()
        ];
        
        return true;
    }
    
    public function wasOrderNotificationSent(int $orderId): bool
    {
        return isset($this->notifications[$orderId]);
    }
}

// Test Data Builder
class UserBuilder
{
    private string $name = 'John Doe';
    private string $email = 'john@example.com';
    private ?int $id = null;
    
    public static function aUser(): self
    {
        return new self();
    }
    
    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
    public function withEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    public function withId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function build(): User
    {
        $user = new User($this->id, $this->name, $this->email);
        
        if ($this->id === null) {
            $user->setId(rand(1, 1000));
        }
        
        return $user;
    }
}

// Test Data Factory
class UserFactory
{
    public static function create(array $overrides = []): User
    {
        $defaults = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'id' => rand(1, 1000)
        ];
        
        $data = array_merge($defaults, $overrides);
        
        return new User($data['id'], $data['name'], $data['email']);
    }
    
    public static function createAdmin(array $overrides = []): User
    {
        $defaults = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'id' => rand(1, 1000)
        ];
        
        $data = array_merge($defaults, $overrides);
        
        $user = new User($data['id'], $data['name'], $data['email']);
        $user->setRole('admin');
        
        return $user;
    }
}

// Test Fixtures
class UserTestFixture
{
    private array $users = [];
    
    public function __construct()
    {
        $this->createDefaultUsers();
    }
    
    private function createDefaultUsers(): void
    {
        $this->users['regular_user'] = UserFactory::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'id' => 1
        ]);
        
        $this->users['admin_user'] = UserFactory::createAdmin([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'id' => 2
        ]);
        
        $this->users['inactive_user'] = UserFactory::create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'id' => 3
        ]);
        
        $this->users['inactive_user']->setStatus('inactive');
    }
    
    public function getUser(string $key): User
    {
        return $this->users[$key];
    }
    
    public function getAllUsers(): array
    {
        return $this->users;
    }
}

// Test Utilities
class TestUtilities
{
    public static function assertArraysEqual(array $expected, array $actual, string $message = ''): void
    {
        $differences = [];
        
        // Check if keys match
        $expectedKeys = array_keys($expected);
        $actualKeys = array_keys($actual);
        
        if ($expectedKeys !== $actualKeys) {
            throw new \Exception($message ?: 'Array keys do not match');
        }
        
        // Check values
        foreach ($expected as $key => $value) {
            if (!array_key_exists($key, $actual) || $actual[$key] !== $value) {
                $differences[] = "$key: expected " . var_export($value, true) . ", got " . var_export($actual[$key] ?? null, true);
            }
        }
        
        if (!empty($differences)) {
            throw new \Exception($message ?: "Arrays differ: " . implode(', ', $differences));
        }
    }
    
    public static function assertStringContains(string $needle, string $haystack, string $message = ''): void
    {
        if (strpos($haystack, $needle) === false) {
            throw new \Exception($message ?: "String '$needle' not found in haystack");
        }
    }
    
    public static function assertStringStartsWith(string $prefix, string $string, string $message = ''): void
    {
        if (!str_starts_with($string, $prefix)) {
            throw new \Exception($message ?: "String does not start with '$prefix'");
        }
    }
    
    public static function assertStringEndsWith(string $suffix, string $string, string $message = ''): void
    {
        if (!str_ends_with($string, $suffix)) {
            throw new \Exception($message ?: "String does not end with '$suffix'");
        }
    }
    
    public static function assertRegExp(string $pattern, string $string, string $message = ''): void
    {
        if (!preg_match($pattern, $string)) {
            throw new \Exception($message ?: "String does not match pattern '$pattern'");
        }
    }
}

// Testing Patterns Examples
class TestingPatternsExamples
{
    private SimpleTestFramework $test;
    
    public function __construct()
    {
        $this->test = new SimpleTestFramework();
    }
    
    public function demonstrateTestPatterns(): void
    {
        echo "Testing Patterns Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test Builder Pattern
        $this->demonstrateTestBuilder();
        
        // Test Factory Pattern
        $this->demonstrateTestFactory();
        
        // Test Fixture Pattern
        $this->demonstrateTestFixture();
        
        // Test Data Pattern
        $this->demonstrateTestData();
        
        // Test Double Pattern
        $this->demonstrateTestDoubles();
        
        $this->test->run();
    }
    
    private function demonstrateTestBuilder(): void
    {
        $this->test->test('UserBuilder should create user with name', function() {
            $user = UserBuilder::aUser()
                ->withName('Jane Doe')
                ->withEmail('jane@example.com')
                ->build();
            
            TestUtilities::assertEqual('Jane Doe', $user->getName());
            TestUtilities::assertEqual('jane@example.com', $user->getEmail());
        });
        
        $this->test->test('UserBuilder should create user with custom properties', function() {
            $user = UserBuilder::aUser()
                ->withName('Bob Smith')
                ->withEmail('bob@example.com')
                ->withId(123)
                ->build();
            
            TestUtilities::assertEqual('Bob Smith', $user->getName());
            TestUtilities::assertEqual('bob@example.com', $user->getEmail());
            TestUtilities::assertEqual(123, $user->getId());
        });
    }
    
    private function demonstrateTestFactory(): void
    {
        $this->test->test('UserFactory should create default user', function() {
            $user = UserFactory::create();
            
            TestUtilities::assertEqual('John Doe', $user->getName());
            TestUtilities::assertEqual('john@example.com', $user->getEmail());
            TestUtilities::assertNotNull($user->getId());
        });
        
        $this->test->test('UserFactory should create admin user', function() {
            $admin = UserFactory::createAdmin();
            
            TestUtilities::assertEqual('Admin User', $admin->getName());
            TestUtilities::assertEqual('admin@example.com', $admin->getEmail());
            TestUtilities::assertEqual('admin', $admin->getRole());
        });
        
        $this->test->test('UserFactory should create user with overrides', function() {
            $user = UserFactory::create([
                'name' => 'Custom User',
                'email' => 'custom@example.com'
            ]);
            
            TestUtilities::assertEqual('Custom User', $user->getName());
            TestUtilities::assertEqual('custom@example.com', $user->getEmail());
        });
    }
    
    private function demonstrateTestFixture(): void
    {
        $this->test->test('UserTestFixture should provide default users', function() {
            $fixture = new UserTestFixture();
            
            $regularUser = $fixture->getUser('regular_user');
            TestUtilities::assertEqual('Regular User', $regularUser->getName());
            
            $adminUser = $fixture->getUser('admin_user');
            TestUtilities::assertEqual('Admin User', $adminUser->getName());
            TestUtilities::assertEqual('admin', $adminUser->getRole());
            
            $inactiveUser = $fixture->getUser('inactive_user');
            TestUtilities::assertEqual('inactive', $inactiveUser->getStatus());
        });
        
        $this->test->test('UserTestFixture should provide all users', function() {
            $fixture = new UserTestFixture();
            $users = $fixture->getAllUsers();
            
            TestUtilities::assertCount(3, $users);
            TestUtilities::assertArrayHasKey('regular_user', $users);
            TestUtilities::assertArrayHasKey('admin_user', $users);
            TestUtilities::assertArrayHasKey('inactive_user', $users);
        });
    }
    
    private function demonstrateTestData(): void
    {
        $this->test->test('Test data should be consistent', function() {
            $user1 = UserFactory::create(['id' => 1]);
            $user2 = UserFactory::create(['id' => 1]);
            
            TestUtilities::assertEqual($user1->getId(), $user2->getId());
            TestUtilities::assertEqual($user1->getName(), $user2->getName());
        });
        
        $this->test->test('Test data should be independent', function() {
            $user1 = UserFactory::create();
            $user2 = UserFactory::create();
            
            TestUtilities::assertNotEqual($user1->getId(), $user2->getId());
        });
    }
    
    private function demonstrateTestDoubles(): void
    {
        $this->test->test('Mock should record interactions', function() {
            $mock = new MockEmailService();
            $user = UserFactory::create();
            
            $mock->sendWelcomeEmail($user);
            
            TestUtilities::assertTrue($mock->wasWelcomeEmailSent($user));
        });
        
        $this->test->test('Stub should return predefined data', function() {
            $stub = new UserRepositoryStub();
            $user = $stub->findById(1);
            
            TestUtilities::assertNotNull($user);
            TestUtilities::assertEqual('Test User', $user->getName());
        });
        
        $this->test->test('Fake should provide working implementation', function() {
            $fake = new InMemoryUserRepository();
            $user = UserFactory::create();
            
            $fake->save($user);
            $retrieved = $fake->findById($user->getId());
            
            TestUtilities::assertNotNull($retrieved);
            TestUtilities::assertEqual($user->getId(), $retrieved->getId());
        });
    }
    
    private function assertNotEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected === $actual) {
            throw new \Exception($message ?: "Expected values to be different");
        }
    }
    
    private function assertArrayHasKey(string $key, array $array, string $message = ''): void
    {
        if (!array_key_exists($key, $array)) {
            throw new \Exception($message ?: "Array does not have key '$key'");
        }
    }
}

// Supporting Classes for Testing Examples
class User
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $role = 'user';
    private string $status = 'active';
    
    public function __construct(?int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function getRole(): string
    {
        return $this->role;
    }
    
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    public function hasValidEmail(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class EmailValidator
{
    public function isValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class Calculator
{
    public function add(float $a, float $b): float
    {
        return $a + $b;
    }
    
    public function subtract(float $a, float $b): float
    {
        return $a - $b;
    }
    
    public function multiply(float $a, float $b): float
    {
        return $a * $b;
    }
    
    public function divide(float $a, float $b): float
    {
        if ($b === 0) {
            throw new \DivisionByZeroError('Cannot divide by zero');
        }
        
        return $a / $b;
    }
    
    public function addMultiple(array $numbers): float
    {
        return array_sum($numbers);
    }
}

class ArrayHelper
{
    public function contains(array $array, $value): bool
    {
        return in_array($value, $array, true);
    }
    
    public function filter(array $array, callable $callback): array
    {
        return array_values(array_filter($array, $callback));
    }
    
    public function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }
}

class UserRegistrationService
{
    private UserRepository $repository;
    private EmailService $emailService;
    
    public function __construct(UserRepository $repository, EmailService $emailService)
    {
        $this->repository = $repository;
        $this->emailService = $emailService;
    }
    
    public function register(array $userData): User
    {
        $this->validateUserData($userData);
        
        $user = new User(null, $userData['name'], $userData['email']);
        $this->repository->save($user);
        $this->emailService->sendWelcomeEmail($user);
        
        return $user;
    }
    
    private function validateUserData(array $userData): void
    {
        if (empty($userData['name'])) {
            throw new \InvalidArgumentException('Name is required');
        }
        
        if (empty($userData['email'])) {
            throw new \InvalidArgumentException('Email is required');
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }
}

class OrderProcessingService
{
    private PaymentService $paymentService;
    private InventoryService $inventoryService;
    private NotificationService $notificationService;
    
    public function __construct(
        PaymentService $paymentService,
        InventoryService $inventoryService,
        NotificationService $notificationService
    ) {
        $this->paymentService = $paymentService;
        $this->inventoryService = $inventoryService;
        $this->notificationService = $notificationService;
    }
    
    public function processOrder(array $orderData): array
    {
        $totalAmount = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $orderData['items']));
        
        $orderId = $this->generateOrderId();
        
        if (!$this->paymentService->processPayment($orderId, $totalAmount)) {
            throw new \RuntimeException('Payment failed');
        }
        
        $this->inventoryService->updateInventory($orderData['items']);
        $this->notificationService->sendOrderNotification($orderId, $orderData['user_id']);
        
        return [
            'success' => true,
            'order_id' => $orderId,
            'total_amount' => $totalAmount
        ];
    }
    
    private function generateOrderId(): int
    {
        return rand(1000, 9999);
    }
}

class EmailService
{
    public function sendEmail(string $to, string $subject, string $body): array
    {
        // Simulate email sending
        return [
            'sent' => true,
            'message_id' => uniqid('msg_', true),
            'to' => $to,
            'subject' => $subject
        ];
    }
}

class EmailTemplateService
{
    public function getWelcomeTemplate(): array
    {
        return [
            'subject' => 'Welcome to our service!',
            'body' => 'Thank you for registering with our service. We\'re excited to have you on board!'
        ];
    }
}

interface UserRepository
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
}

interface PaymentService
{
    public function processPayment(int $orderId, float $amount): bool;
}

interface InventoryService
{
    public function updateInventory(array $items): bool;
}

interface NotificationService
{
    public function sendOrderNotification(int $orderId, int $userId): bool;
}

class InMemoryUserRepository implements UserRepository
{
    private array $users = [];
    
    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $user->setId(count($this->users) + 1);
        }
        
        $this->users[$user->getId()] = $user;
    }
    
    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }
}

class UserRepositoryStub implements UserRepository
{
    public function save(User $user): void
    {
        // Stub implementation - doesn't actually save
    }
    
    public function findById(int $id): ?User
    {
        if ($id === 1) {
            return new User(1, 'Test User', 'test@example.com');
        }
        
        return null;
    }
}

// Testing Practices Examples
class TestingPracticesExamples
{
    private SimpleTestFramework $test;
    
    public function __construct()
    {
        $this->test = new SimpleTestFramework();
    }
    
    public function demonstrateTestingPractices(): void
    {
        echo "Testing Best Practices Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test Naming Conventions
        $this->demonstrateTestNaming();
        
        // Test Structure
        $this->demonstrateTestStructure();
        
        // Test Organization
        $this->demonstrateTestOrganization();
        
        // Test Maintenance
        $this->demonstrateTestMaintenance();
        
        $this->test->run();
    }
    
    private function demonstrateTestNaming(): void
    {
        $this->test->test('Test names should describe what is being tested', function() {
            // Good: testUserRegistrationWithValidData
            // Bad: test1, testUser, testRegistration
            
            $calculator = new Calculator();
            $result = $calculator->add(2, 3);
            TestUtilities::assertEqual(5, $result);
        });
        
        $this->test->test('Test names should include scenario when relevant', function() {
            // Good: testUserRegistrationFailsWithInvalidEmail
            // Bad: testUserRegistrationInvalid
            
            $validator = new EmailValidator();
            TestUtilities::assertFalse($validator->isValid('invalid-email'));
        });
        
        $this->test->test('Test names should be in present tense', function() {
            // Good: calculatesTotal, validatesEmail
            // Bad: calculatedTotal, willValidateEmail
            
            $calculator = new Calculator();
            $result = $calculator->add(2, 3);
            TestUtilities::assertEqual(5, $result);
        });
    }
    
    private function demonstrateTestStructure(): void
    {
        $this->test->test('Tests should follow AAA pattern', function() {
            // Arrange
            $calculator = new Calculator();
            $a = 10;
            $b = 5;
            
            // Act
            $result = $calculator->add($a, $b);
            
            // Assert
            TestUtilities::assertEqual(15, $result);
        });
        
        $this->test->test('Tests should have single assertion', function() {
            // Good: One assertion per test
            // Bad: Multiple assertions in one test
            
            $user = UserBuilder::aUser()
                ->withName('Test User')
                ->build();
            
            TestUtilities::assertEqual('Test User', $user->getName());
        });
        
        $this->test->test('Tests should be independent', function() {
            // Each test should be able to run independently
            
            $user1 = UserFactory::create(['id' => 1]);
            $user2 = UserFactory::create(['id' => 2]);
            
            TestUtilities::assertNotEqual($user1->getId(), $user2->getId());
        });
    }
    
    private function demonstrateTestOrganization(): void
    {
        $this->test->test('Tests should be organized by class', function() {
            // All User-related tests in UserTest class
            // All Calculator-related tests in CalculatorTest class
            
            $user = UserFactory::create();
            TestUtilities::assertNotNull($user);
        });
        
        $this->test->test('Tests should be organized by feature', function() {
            // All registration tests together
            // All validation tests together
            
            $validator = new EmailValidator();
            TestUtilities::assertTrue($validator->isValid('test@example.com'));
        });
        
        $this->test->test('Tests should use descriptive names', function() {
            // testShouldReturnTrueWhenValidEmailProvided
            // testShouldThrowExceptionWhenInvalidEmailProvided
            
            $validator = new EmailValidator();
            TestUtilities::assertTrue($validator->isValid('valid@example.com'));
            TestUtilities::assertFalse($validator->isValid('invalid-email'));
        });
    }
    
    private function demonstrateTestMaintenance(): void
    {
        $this->test->test('Tests should be maintainable', function() {
            // Use builders and factories instead of hardcoded data
            // Use helper methods for common assertions
            
            $user = UserBuilder::aUser()
                ->withName('Maintainable User')
                ->build();
            
            TestUtilities::assertEqual('Maintainable User', $user->getName());
        });
        
        $this->test->test('Tests should use clear error messages', function() {
            // Include context in assertion messages
            // Explain what went wrong and what was expected
            
            try {
                throw new \Exception('Test exception');
                TestUtilities::assertTrue(false);
            } catch (\Exception $e) {
                TestUtilities::assertEqual('Test exception', $e->getMessage());
            }
        });
        
        $this->test->test('Tests should use appropriate assertions', function() {
            // Use specific assertions for specific checks
            // Use generic assertions only when necessary
            
            $array = [1, 2, 3, 4, 5];
            TestUtilities::assertContains(3, $array);
            TestUtilities::assertCount(5, $array);
            TestUtilities::assertEqual([1, 2, 3, 4, 5], $array);
        });
    }
    
    public function runAllExamples(): void
    {
        echo "Testing Practices Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        // Run TDD example
        $tddExample = new TDDExample();
        $tddExample->demonstrateTDDWorkflow();
        
        echo "\n";
        
        // Run unit testing example
        $unitExample = new UnitTestingExample();
        $unitExample->demonstrateUnitTesting();
        
        echo "\n";
        
        // Run integration testing example
        $integrationExample = new IntegrationTestingExample();
        $integrationExample->demonstrateIntegrationTesting();
        
        echo "\n";
        
        // Run testing patterns example
        $patternsExample = new TestingPatternsExamples();
        $patternsExample->demonstrateTestPatterns();
        
        echo "\n";
        
        // Run testing practices example
        $this->demonstrateTestingPractices();
    }
}

// Testing Best Practices
function printTestingBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Test-Driven Development:\n";
    echo "   • Write failing test first\n";
    echo "   • Make it pass with minimal code\n";
    echo "   • Refactor to improve design\n";
    echo "   • Repeat for each feature\n";
    echo "   • Keep tests fast and focused\n\n";
    
    echo "2. Unit Testing:\n";
    echo "   • Test individual components\n";
    echo "   • Use dependency injection\n";
    echo "   • Mock external dependencies\n";
    echo "   • Test edge cases and boundaries\n";
    echo "   • Aim for high code coverage\n\n";
    
    echo "3. Integration Testing:\n";
    echo "   • Test component interactions\n";
    echo "   • Use real dependencies\n";
    echo "   • Test database operations\n";
    echo "   • Test API endpoints\n";
    echo "   • Use test databases\n\n";
    
    echo "4. Test Data Management:\n";
    echo "   • Use test builders for complex objects\n";
    echo "   • Use factories for test data\n";
    echo "   • Use fixtures for test setup\n";
    echo "   • Keep test data independent\n";
    echo "   • Clean up after tests\n\n";
    
    echo "5. Test Organization:\n";
    echo "   • Organize tests by class\n";
    echo "   • Group related tests together\n";
    echo "   • Use descriptive test names\n";
    echo "   • Follow naming conventions\n";
    echo "   • Use directory structure\n\n";
    
    echo "6. Test Quality:\n";
    echo "   • Write clear test names\n";
    echo "   • Use AAA pattern\n";
    echo "   • Single assertion per test\n";
    echo "   • Make tests independent\n";
    echo "   • Provide good error messages";
}

// Main execution
function runTestingPracticesDemo(): void
{
    $examples = new TestingPracticesExamples();
    $examples->runAllExamples();
    printTestingBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runTestingPracticesDemo();
}
?>
