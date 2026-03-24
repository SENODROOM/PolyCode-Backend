<?php
/**
 * Mocking and Stubbing Examples
 * 
 * This file demonstrates advanced testing techniques including:
 * - Test doubles (mocks, stubs, fakes, spies)
 * - Dependency injection for testability
 * - Isolation testing
 * - Behavior verification
 */

// Simple test framework
class MockTest {
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
    
    public static function assertTrue(bool $condition, string $message = ''): void {
        self::assertEqual(true, $condition, $message);
    }
    
    public static function assertFalse(bool $condition, string $message = ''): void {
        self::assertEqual(false, $condition, $message);
    }
    
    public static function assertNotNull(mixed $value, string $message = ''): void {
        self::$testsRun++;
        
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
        echo "Mocking & Stubbing Test Summary\n";
        echo str_repeat("=", 50) . "\n";
        echo "Tests Run: {$stats['run']}\n";
        echo "Tests Passed: {$stats['passed']}\n";
        echo "Tests Failed: {$stats['failed']}\n";
        echo "Success Rate: " . number_format($stats['success_rate'], 1) . "%\n";
        echo str_repeat("=", 50) . "\n";
    }
}

// Interfaces for dependency injection
interface EmailService {
    public function sendEmail(string $to, string $subject, string $body): bool;
}

interface PaymentGateway {
    public function processPayment(float $amount, string $cardNumber): array;
}

interface UserRepository {
    public function findById(int $id): ?array;
    public function save(array $user): bool;
    public function delete(int $id): bool;
}

interface Logger {
    public function log(string $message): void;
    public function error(string $message): void;
}

// Real implementations (for production)
class SmtpEmailService implements EmailService {
    public function sendEmail(string $to, string $subject, string $body): bool {
        // Real SMTP implementation
        echo "Sending real email to: $to\n";
        return true;
    }
}

class StripePaymentGateway implements PaymentGateway {
    public function processPayment(float $amount, string $cardNumber): array {
        // Real Stripe API call
        echo "Processing real payment of $$amount via Stripe\n";
        return ['success' => true, 'transaction_id' => 'stripe_' . uniqid()];
    }
}

class DatabaseUserRepository implements UserRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    
    public function save(array $user): bool {
        // Database save logic
        return true;
    }
    
    public function delete(int $id): bool {
        // Database delete logic
        return true;
    }
}

class FileLogger implements Logger {
    private string $logFile;
    
    public function __construct(string $logFile = 'app.log') {
        $this->logFile = $logFile;
    }
    
    public function log(string $message): void {
        file_put_contents($this->logFile, "[LOG] $message\n", FILE_APPEND);
    }
    
    public function error(string $message): void {
        file_put_contents($this->logFile, "[ERROR] $message\n", FILE_APPEND);
    }
}

// Test Doubles

// 1. Stub - Returns canned answers
class StubEmailService implements EmailService {
    private bool $shouldSend;
    
    public function __construct(bool $shouldSend = true) {
        $this->shouldSend = $shouldSend;
    }
    
    public function sendEmail(string $to, string $subject, string $body): bool {
        return $this->shouldSend;
    }
}

// 2. Mock - Verifies behavior
class MockEmailService implements EmailService {
    private array $sentEmails = [];
    private bool $shouldSend = true;
    
    public function __construct(bool $shouldSend = true) {
        $this->shouldSend = $shouldSend;
    }
    
    public function sendEmail(string $to, string $subject, string $body): bool {
        $this->sentEmails[] = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body
        ];
        return $this->shouldSend;
    }
    
    public function getSentEmails(): array {
        return $this->sentEmails;
    }
    
    public function wasEmailSent(string $to, string $subject): bool {
        foreach ($this->sentEmails as $email) {
            if ($email['to'] === $to && $email['subject'] === $subject) {
                return true;
            }
        }
        return false;
    }
    
    public function getEmailCount(): int {
        return count($this->sentEmails);
    }
}

// 3. Fake - Working but simplified implementation
class FakePaymentGateway implements PaymentGateway {
    private array $transactions = [];
    private bool $shouldFail = false;
    
    public function __construct(bool $shouldFail = false) {
        $this->shouldFail = $shouldFail;
    }
    
    public function processPayment(float $amount, string $cardNumber): array {
        if ($this->shouldFail) {
            return ['success' => false, 'error' => 'Payment failed'];
        }
        
        $transaction = [
            'success' => true,
            'transaction_id' => 'fake_' . uniqid(),
            'amount' => $amount,
            'card_last4' => substr($cardNumber, -4)
        ];
        
        $this->transactions[] = $transaction;
        return $transaction;
    }
    
    public function getTransactions(): array {
        return $this->transactions;
    }
    
    public function getTransactionCount(): int {
        return count($this->transactions);
    }
}

// 4. Spy - Records information about calls
class SpyUserRepository implements UserRepository {
    private array $users = [];
    private array $calls = [];
    
    public function __construct() {
        // Initialize with test data
        $this->users[1] = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
        $this->users[2] = ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'];
    }
    
    public function findById(int $id): ?array {
        $this->calls[] = ['method' => 'findById', 'args' => [$id]];
        return $this->users[$id] ?? null;
    }
    
    public function save(array $user): bool {
        $this->calls[] = ['method' => 'save', 'args' => [$user]];
        if (isset($user['id'])) {
            $this->users[$user['id']] = $user;
        }
        return true;
    }
    
    public function delete(int $id): bool {
        $this->calls[] = ['method' => 'delete', 'args' => [$id]];
        unset($this->users[$id]);
        return true;
    }
    
    public function getCalls(): array {
        return $this->calls;
    }
    
    public function wasCalled(string $method): bool {
        foreach ($this->calls as $call) {
            if ($call['method'] === $method) {
                return true;
            }
        }
        return false;
    }
    
    public function getCallCount(): int {
        return count($this->calls);
    }
}

// 5. Mock with expectations
class MockLogger implements Logger {
    private array $logs = [];
    private array $expectations = [];
    
    public function expectLog(string $message): void {
        $this->expectations[] = ['type' => 'log', 'message' => $message];
    }
    
    public function expectError(string $message): void {
        $this->expectations[] = ['type' => 'error', 'message' => $message];
    }
    
    public function log(string $message): void {
        $this->logs[] = ['type' => 'log', 'message' => $message];
    }
    
    public function error(string $message): void {
        $this->logs[] = ['type' => 'error', 'message' => $message];
    }
    
    public function getLogs(): array {
        return $this->logs;
    }
    
    public function verify(): bool {
        if (count($this->logs) !== count($this->expectations)) {
            return false;
        }
        
        for ($i = 0; $i < count($this->expectations); $i++) {
            if ($this->logs[$i] !== $this->expectations[$i]) {
                return false;
            }
        }
        
        return true;
    }
}

// System Under Test (SUT)
class UserService {
    private EmailService $emailService;
    private UserRepository $userRepository;
    private Logger $logger;
    
    public function __construct(EmailService $emailService, UserRepository $userRepository, Logger $logger) {
        $this->emailService = $emailService;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }
    
    public function registerUser(string $name, string $email): array {
        $this->logger->log("Registering user: $name");
        
        // Check if user already exists
        $existingUsers = $this->userRepository->findAll() ?? [];
        foreach ($existingUsers as $user) {
            if ($user['email'] === $email) {
                $this->logger->error("User with email $email already exists");
                throw new InvalidArgumentException('User already exists');
            }
        }
        
        // Create new user
        $user = [
            'id' => rand(1, 1000),
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->userRepository->save($user);
        
        // Send welcome email
        $subject = 'Welcome to our service!';
        $body = "Hello $name, welcome to our service!";
        $this->emailService->sendEmail($email, $subject, $body);
        
        $this->logger->log("User registered successfully: $email");
        
        return $user;
    }
    
    public function deleteUser(int $userId): bool {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            $this->logger->error("User not found: $userId");
            return false;
        }
        
        $this->userRepository->delete($userId);
        
        // Send goodbye email
        $subject = 'Account deleted';
        $body = "Goodbye {$user['name']}, your account has been deleted.";
        $this->emailService->sendEmail($user['email'], $subject, $body);
        
        $this->logger->log("User deleted: $userId");
        
        return true;
    }
    
    public function getUser(int $userId): ?array {
        $this->logger->log("Fetching user: $userId");
        return $this->userRepository->findById($userId);
    }
}

// Add missing method to SpyUserRepository
class SpyUserRepositoryWithFindAll extends SpyUserRepository {
    public function findAll(): array {
        $this->calls[] = ['method' => 'findAll', 'args' => []];
        return array_values($this->users);
    }
}

// Order Processing System
class OrderProcessor {
    private PaymentGateway $paymentGateway;
    private UserRepository $userRepository;
    private EmailService $emailService;
    
    public function __construct(PaymentGateway $paymentGateway, UserRepository $userRepository, EmailService $emailService) {
        $this->paymentGateway = $paymentGateway;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }
    
    public function processOrder(int $userId, array $items, string $cardNumber): array {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }
        
        $total = array_sum(array_column($items, 'price'));
        
        // Process payment
        $paymentResult = $this->paymentGateway->processPayment($total, $cardNumber);
        
        if (!$paymentResult['success']) {
            throw new RuntimeException('Payment failed');
        }
        
        // Send confirmation email
        $subject = 'Order Confirmation';
        $body = "Your order of $$total has been processed successfully.";
        $this->emailService->sendEmail($user['email'], $subject, $body);
        
        return [
            'success' => true,
            'order_id' => uniqid(),
            'transaction_id' => $paymentResult['transaction_id'],
            'total' => $total
        ];
    }
}

// Test Classes
class UserServiceTest {
    public function testRegisterUserSuccess(): void {
        // Arrange
        $emailService = new MockEmailService(true);
        $userRepository = new SpyUserRepositoryWithFindAll();
        $logger = new MockLogger();
        
        $userService = new UserService($emailService, $userRepository, $logger);
        
        // Act
        $user = $userService->registerUser('Test User', 'test@example.com');
        
        // Assert
        MockTest::assertNotNull($user, 'User should be created');
        MockTest::assertEqual('Test User', $user['name'], 'User name should match');
        MockTest::assertEqual('test@example.com', $user['email'], 'User email should match');
        MockTest::assertTrue($emailService->wasEmailSent('test@example.com', 'Welcome to our service!'), 'Welcome email should be sent');
        MockTest::assertTrue($userRepository->wasCalled('save'), 'User should be saved');
        MockTest::assertTrue($userRepository->wasCalled('findAll'), 'Existing users should be checked');
    }
    
    public function testRegisterUserDuplicateEmail(): void {
        // Arrange
        $emailService = new MockEmailService(false);
        $userRepository = new SpyUserRepositoryWithFindAll();
        $logger = new MockLogger();
        
        $userService = new UserService($emailService, $userRepository, $logger);
        
        // Act & Assert
        MockTest::assertThrows(InvalidArgumentException::class, function() use ($userService) {
            $userService->registerUser('John Doe', 'john@example.com'); // Existing email
        }, 'Should throw exception for duplicate email');
        
        MockTest::assertFalse($emailService->wasEmailSent('john@example.com', 'Welcome to our service!'), 'Email should not be sent for duplicate');
    }
    
    public function testDeleteUserSuccess(): void {
        // Arrange
        $emailService = new MockEmailService(true);
        $userRepository = new SpyUserRepositoryWithFindAll();
        $logger = new MockLogger();
        
        $userService = new UserService($emailService, $userRepository, $logger);
        
        // Act
        $result = $userService->deleteUser(1);
        
        // Assert
        MockTest::assertTrue($result, 'User should be deleted');
        MockTest::assertTrue($userRepository->wasCalled('findById'), 'User should be fetched first');
        MockTest::assertTrue($userRepository->wasCalled('delete'), 'User should be deleted');
        MockTest::assertTrue($emailService->wasEmailSent('john@example.com', 'Account deleted'), 'Goodbye email should be sent');
    }
    
    public function testDeleteUserNotFound(): void {
        // Arrange
        $emailService = new MockEmailService(false);
        $userRepository = new SpyUserRepositoryWithFindAll();
        $logger = new MockLogger();
        
        $userService = new UserService($emailService, $userRepository, $logger);
        
        // Act
        $result = $userService->deleteUser(999);
        
        // Assert
        MockTest::assertFalse($result, 'Should return false for non-existent user');
        MockTest::assertFalse($emailService->wasEmailSent('', 'Account deleted'), 'Email should not be sent');
    }
}

class OrderProcessorTest {
    public function testProcessOrderSuccess(): void {
        // Arrange
        $paymentGateway = new FakePaymentGateway(false);
        $userRepository = new SpyUserRepository();
        $emailService = new MockEmailService(true);
        
        $processor = new OrderProcessor($paymentGateway, $userRepository, $emailService);
        
        $items = [
            ['name' => 'Book', 'price' => 12.99],
            ['name' => 'Pen', 'price' => 2.99]
        ];
        
        // Act
        $result = $processor->processOrder(1, $items, '4111111111111111');
        
        // Assert
        MockTest::assertTrue($result['success'], 'Order should be processed successfully');
        MockTest::assertEqual(15.98, $result['total'], 'Total should be correct');
        MockTest::assertNotNull($result['order_id'], 'Order ID should be generated');
        MockTest::assertNotNull($result['transaction_id'], 'Transaction ID should be returned');
        MockTest::assertTrue($emailService->wasEmailSent('john@example.com', 'Order Confirmation'), 'Confirmation email should be sent');
        MockTest::assertEqual(1, $paymentGateway->getTransactionCount(), 'Payment should be processed');
    }
    
    public function testProcessOrderPaymentFailure(): void {
        // Arrange
        $paymentGateway = new FakePaymentGateway(true); // Will fail
        $userRepository = new SpyUserRepository();
        $emailService = new MockEmailService(false);
        
        $processor = new OrderProcessor($paymentGateway, $userRepository, $emailService);
        
        $items = [['name' => 'Book', 'price' => 12.99]];
        
        // Act & Assert
        MockTest::assertThrows(RuntimeException::class, function() use ($processor, $items) {
            $processor->processOrder(1, $items, '4111111111111111');
        }, 'Should throw exception for payment failure');
        
        MockTest::assertFalse($emailService->wasEmailSent('john@example.com', 'Order Confirmation'), 'Email should not be sent on failure');
    }
    
    public function testProcessOrderUserNotFound(): void {
        // Arrange
        $paymentGateway = new FakePaymentGateway(false);
        $userRepository = new SpyUserRepository();
        $emailService = new MockEmailService(false);
        
        $processor = new OrderProcessor($paymentGateway, $userRepository, $emailService);
        
        $items = [['name' => 'Book', 'price' => 12.99]];
        
        // Act & Assert
        MockTest::assertThrows(InvalidArgumentException::class, function() use ($processor, $items) {
            $processor->processOrder(999, $items, '4111111111111111');
        }, 'Should throw exception for non-existent user');
    }
}

class MockingExamplesTest {
    public function testStubExample(): void {
        // Arrange
        $emailService = new StubEmailService(true);
        
        // Act
        $result = $emailService->sendEmail('test@example.com', 'Test', 'Test body');
        
        // Assert
        MockTest::assertTrue($result, 'Stub should return true');
    }
    
    public function testMockExample(): void {
        // Arrange
        $emailService = new MockEmailService(true);
        
        // Act
        $emailService->sendEmail('test@example.com', 'Test', 'Test body');
        
        // Assert
        MockTest::assertTrue($emailService->wasEmailSent('test@example.com', 'Test'), 'Mock should record email');
        MockTest::assertEqual(1, $emailService->getEmailCount(), 'Mock should record one email');
    }
    
    public function testFakeExample(): void {
        // Arrange
        $paymentGateway = new FakePaymentGateway(false);
        
        // Act
        $result1 = $paymentGateway->processPayment(100.0, '4111111111111111');
        $result2 = $paymentGateway->processPayment(50.0, '4222222222222222');
        
        // Assert
        MockTest::assertTrue($result1['success'], 'Fake should succeed');
        MockTest::assertTrue($result2['success'], 'Fake should succeed');
        MockTest::assertEqual(2, $paymentGateway->getTransactionCount(), 'Fake should record both transactions');
    }
    
    public function testSpyExample(): void {
        // Arrange
        $userRepository = new SpyUserRepository();
        
        // Act
        $userRepository->findById(1);
        $userRepository->save(['id' => 3, 'name' => 'Test']);
        $userRepository->delete(2);
        
        // Assert
        MockTest::assertTrue($userRepository->wasCalled('findById'), 'Spy should record findById call');
        MockTest::assertTrue($userRepository->wasCalled('save'), 'Spy should record save call');
        MockTest::assertTrue($userRepository->wasCalled('delete'), 'Spy should record delete call');
        MockTest::assertEqual(3, $userRepository->getCallCount(), 'Spy should record 3 calls');
    }
    
    public function testMockWithExpectations(): void {
        // Arrange
        $logger = new MockLogger();
        $logger->expectLog('Starting process');
        $logger->expectLog('Process completed');
        
        // Act
        $logger->log('Starting process');
        $logger->log('Process completed');
        
        // Assert
        MockTest::assertTrue($logger->verify(), 'Mock should verify all expectations');
    }
}

// Main test runner
function runMockingTests(): void {
    MockTest::reset();
    
    echo "Mocking and Stubbing Examples\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "1. Basic Test Doubles\n";
    echo str_repeat("-", 30) . "\n";
    
    $mockingTest = new MockingExamplesTest();
    $mockingTest->testStubExample();
    $mockingTest->testMockExample();
    $mockingTest->testFakeExample();
    $mockingTest->testSpyExample();
    $mockingTest->testMockWithExpectations();
    
    echo "\n2. UserService Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $userServiceTest = new UserServiceTest();
    $userServiceTest->testRegisterUserSuccess();
    $userServiceTest->testRegisterUserDuplicateEmail();
    $userServiceTest->testDeleteUserSuccess();
    $userServiceTest->testDeleteUserNotFound();
    
    echo "\n3. OrderProcessor Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $orderProcessorTest = new OrderProcessorTest();
    $orderProcessorTest->testProcessOrderSuccess();
    $orderProcessorTest->testProcessOrderPaymentFailure();
    $orderProcessorTest->testProcessOrderUserNotFound();
    
    // Print summary
    MockTest::printSummary();
}

// Mocking Best Practices Guide
function printMockingBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Mocking and Stubbing Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. When to Use Test Doubles:\n";
    echo "   • Stub: When you need canned responses\n";
    echo "   • Mock: When you need to verify behavior\n";
    echo "   • Fake: When you need a lightweight implementation\n";
    echo "   • Spy: When you need to record interactions\n\n";
    
    echo "2. Best Practices:\n";
    echo "   • Use interfaces for dependency injection\n";
    echo "   • Keep mocks simple and focused\n";
    echo "   • Test one behavior at a time\n";
    echo "   • Don't mock everything\n";
    echo "   • Use real objects when possible\n";
    echo "   • Verify only what matters\n\n";
    
    echo "3. Common Mistakes:\n";
    echo "   • Over-mocking (too many dependencies)\n";
    echo "   • Mocking value objects\n";
    echo "   • Testing implementation details\n";
    echo "   • brittle tests (too specific expectations)\n";
    echo "   • complex mock setup\n\n";
    
    echo "4. Alternatives to Mocking:\n";
    echo "   • Use real in-memory databases\n";
    echo "   • Use lightweight test implementations\n";
    echo "   • Use builder patterns for test data\n";
    echo "   • Use integration tests for complex interactions\n\n";
    
    echo "5. Tools for PHP:\n";
    echo "   • PHPUnit Mock Builder\n";
    echo "   • Mockery\n";
    echo "   • Prophecy\n";
    echo "   • Phake\n";
}

// Run the tests
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMockingTests();
    printMockingBestPractices();
}
?>
