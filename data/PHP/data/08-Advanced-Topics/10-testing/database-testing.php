<?php
/**
 * Database Testing Examples
 * 
 * This file demonstrates database testing strategies including:
 * - In-memory SQLite databases
 * - Database transactions for test isolation
 * - Database migrations and seeding
 * - Integration testing with real databases
 * - Data cleanup and rollback
 */

// Simple test framework
class DBTest {
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
    
    public static function assertCount(int $expected, array|Countable $array, string $message = ''): void {
        self::assertEqual($expected, count($array), $message);
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
        echo "Database Testing Summary\n";
        echo str_repeat("=", 50) . "\n";
        echo "Tests Run: {$stats['run']}\n";
        echo "Tests Passed: {$stats['passed']}\n";
        echo "Tests Failed: {$stats['failed']}\n";
        echo "Success Rate: " . number_format($stats['success_rate'], 1) . "%\n";
        echo str_repeat("=", 50) . "\n";
    }
}

// Database Test Helper
class DatabaseTestHelper {
    private PDO $pdo;
    private array $transactions = [];
    
    public function __construct(string $dsn = 'sqlite::memory:') {
        $this->pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    
    public function getPDO(): PDO {
        return $this->pdo;
    }
    
    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
        $this->transactions[] = true;
    }
    
    public function rollback(): void {
        if (!empty($this->transactions)) {
            $this->pdo->rollBack();
            array_pop($this->transactions);
        }
    }
    
    public function commit(): void {
        if (!empty($this->transactions)) {
            $this->pdo->commit();
            array_pop($this->transactions);
        }
    }
    
    public function executeSQL(string $sql): void {
        $this->pdo->exec($sql);
    }
    
    public function loadFixture(string $fixture): void {
        switch ($fixture) {
            case 'users':
                $this->executeSQL("
                    CREATE TABLE users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        name TEXT NOT NULL,
                        email TEXT UNIQUE NOT NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                
                $this->executeSQL("
                    INSERT INTO users (name, email) VALUES 
                    ('John Doe', 'john@example.com'),
                    ('Jane Smith', 'jane@example.com'),
                    ('Bob Johnson', 'bob@example.com')
                ");
                break;
                
            case 'products':
                $this->executeSQL("
                    CREATE TABLE products (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        name TEXT NOT NULL,
                        price DECIMAL(10,2) NOT NULL,
                        category TEXT NOT NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                
                $this->executeSQL("
                    INSERT INTO products (name, price, category) VALUES 
                    ('Laptop', 999.99, 'Electronics'),
                    ('Mouse', 29.99, 'Electronics'),
                    ('Book', 19.99, 'Education')
                ");
                break;
                
            case 'orders':
                $this->executeSQL("
                    CREATE TABLE orders (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        user_id INTEGER NOT NULL,
                        total DECIMAL(10,2) NOT NULL,
                        status TEXT NOT NULL DEFAULT 'pending',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id)
                    )
                ");
                
                $this->executeSQL("
                    CREATE TABLE order_items (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        order_id INTEGER NOT NULL,
                        product_id INTEGER NOT NULL,
                        quantity INTEGER NOT NULL,
                        price DECIMAL(10,2) NOT NULL,
                        FOREIGN KEY (order_id) REFERENCES orders(id),
                        FOREIGN KEY (product_id) REFERENCES products(id)
                    )
                ");
                break;
        }
    }
    
    public function truncate(string $table): void {
        $this->executeSQL("DELETE FROM $table");
    }
    
    public function tearDown(): void {
        // Rollback any remaining transactions
        while (!empty($this->transactions)) {
            $this->rollback();
        }
    }
}

// Repository Pattern for Testing
class UserRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        foreach (['name', 'email'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function count(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        return (int)$result['count'];
    }
}

class ProductRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    public function findByCategory(string $category): array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY name");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
    
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM products ORDER BY category, name");
        return $stmt->fetchAll();
    }
    
    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO products (name, price, category, created_at) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['category'],
            $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function updatePrice(int $id, float $price): bool {
        $stmt = $this->pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
        return $stmt->execute([$price, $id]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class OrderRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function create(array $data): int {
        $this->pdo->beginTransaction();
        
        try {
            // Create order
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['user_id'],
                $data['total'],
                $data['status'] ?? 'pending',
                $data['created_at'] ?? date('Y-m-d H:i:s')
            ]);
            $orderId = (int)$this->pdo->lastInsertId();
            
            // Create order items
            foreach ($data['items'] as $item) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }
            
            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if ($order) {
            // Get order items
            $stmt = $this->pdo->prepare("
                SELECT oi.*, p.name as product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$id]);
            $order['items'] = $stmt->fetchAll();
        }
        
        return $order ?: null;
    }
    
    public function findByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}

// Service Classes
class UserService {
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }
    
    public function createUser(string $name, string $email): array {
        // Check if email already exists
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            throw new InvalidArgumentException('Email already exists');
        }
        
        $userData = [
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $id = $this->userRepository->create($userData);
        return $this->userRepository->findById($id);
    }
    
    public function getUser(int $id): ?array {
        return $this->userRepository->findById($id);
    }
    
    public function updateUserEmail(int $id, string $email): bool {
        // Check if email already exists for another user
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser && $existingUser['id'] !== $id) {
            throw new InvalidArgumentException('Email already exists');
        }
        
        return $this->userRepository->update($id, ['email' => $email]);
    }
    
    public function deleteUser(int $id): bool {
        return $this->userRepository->delete($id);
    }
    
    public function getAllUsers(): array {
        return $this->userRepository->findAll();
    }
    
    public function getUserCount(): int {
        return $this->userRepository->count();
    }
}

class OrderService {
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;
    private ProductRepository $productRepository;
    
    public function __construct(
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }
    
    public function createOrder(int $userId, array $items): array {
        // Validate user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }
        
        // Validate items and calculate total
        $total = 0.0;
        $orderItems = [];
        
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);
            if (!$product) {
                throw new InvalidArgumentException("Product {$item['product_id']} not found");
            }
            
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product['price']
            ];
            
            $total += $product['price'] * $item['quantity'];
        }
        
        $orderData = [
            'user_id' => $userId,
            'total' => $total,
            'status' => 'pending',
            'items' => $orderItems
        ];
        
        $orderId = $this->orderRepository->create($orderData);
        return $this->orderRepository->findById($orderId);
    }
    
    public function getOrder(int $id): ?array {
        return $this->orderRepository->findById($id);
    }
    
    public function getUserOrders(int $userId): array {
        return $this->orderRepository->findByUserId($userId);
    }
    
    public function completeOrder(int $id): bool {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new InvalidArgumentException('Order not found');
        }
        
        if ($order['status'] !== 'pending') {
            throw new InvalidArgumentException('Order cannot be completed');
        }
        
        return $this->orderRepository->updateStatus($id, 'completed');
    }
}

// Test Classes
class UserRepositoryTest {
    private DatabaseTestHelper $dbHelper;
    private UserRepository $repository;
    
    public function setUp(): void {
        $this->dbHelper = new DatabaseTestHelper();
        $this->dbHelper->loadFixture('users');
        $this->repository = new UserRepository($this->dbHelper->getPDO());
        $this->dbHelper->beginTransaction();
    }
    
    public function tearDown(): void {
        $this->dbHelper->tearDown();
    }
    
    public function testFindById(): void {
        $this->setUp();
        
        $user = $this->repository->findById(1);
        
        DBTest::assertNotNull($user, 'User should be found');
        DBTest::assertEqual('John Doe', $user['name'], 'User name should match');
        DBTest::assertEqual('john@example.com', $user['email'], 'User email should match');
        
        $this->tearDown();
    }
    
    public function testFindByIdNotFound(): void {
        $this->setUp();
        
        $user = $this->repository->findById(999);
        
        DBTest::assertEqual(null, $user, 'User should not be found');
        
        $this->tearDown();
    }
    
    public function testFindByEmail(): void {
        $this->setUp();
        
        $user = $this->repository->findByEmail('jane@example.com');
        
        DBTest::assertNotNull($user, 'User should be found');
        DBTest::assertEqual('Jane Smith', $user['name'], 'User name should match');
        
        $this->tearDown();
    }
    
    public function testCreateUser(): void {
        $this->setUp();
        
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
        
        $id = $this->repository->create($userData);
        $user = $this->repository->findById($id);
        
        DBTest::assertTrue($id > 0, 'User ID should be positive');
        DBTest::assertNotNull($user, 'User should be created');
        DBTest::assertEqual('Test User', $user['name'], 'User name should match');
        DBTest::assertEqual('test@example.com', $user['email'], 'User email should match');
        
        $this->tearDown();
    }
    
    public function testUpdateUser(): void {
        $this->setUp();
        
        $result = $this->repository->update(1, ['name' => 'Updated Name']);
        $user = $this->repository->findById(1);
        
        DBTest::assertTrue($result, 'Update should succeed');
        DBTest::assertEqual('Updated Name', $user['name'], 'User name should be updated');
        
        $this->tearDown();
    }
    
    public function testDeleteUser(): void {
        $this->setUp();
        
        $result = $this->repository->delete(1);
        $user = $this->repository->findById(1);
        
        DBTest::assertTrue($result, 'Delete should succeed');
        DBTest::assertEqual(null, $user, 'User should be deleted');
        
        $this->tearDown();
    }
    
    public function testFindAll(): void {
        $this->setUp();
        
        $users = $this->repository->findAll();
        
        DBTest::assertEqual(3, count($users), 'Should find 3 users');
        
        $this->tearDown();
    }
}

class UserServiceTest {
    private DatabaseTestHelper $dbHelper;
    private UserService $userService;
    
    public function setUp(): void {
        $this->dbHelper = new DatabaseTestHelper();
        $this->dbHelper->loadFixture('users');
        $userRepository = new UserRepository($this->dbHelper->getPDO());
        $this->userService = new UserService($userRepository);
        $this->dbHelper->beginTransaction();
    }
    
    public function tearDown(): void {
        $this->dbHelper->tearDown();
    }
    
    public function testCreateUser(): void {
        $this->setUp();
        
        $user = $this->userService->createUser('Test User', 'test@example.com');
        
        DBTest::assertNotNull($user, 'User should be created');
        DBTest::assertEqual('Test User', $user['name'], 'User name should match');
        DBTest::assertEqual('test@example.com', $user['email'], 'User email should match');
        
        $this->tearDown();
    }
    
    public function testCreateUserDuplicateEmail(): void {
        $this->setUp();
        
        DBTest::assertThrows(InvalidArgumentException::class, function() {
            $this->userService->createUser('Test User', 'john@example.com');
        }, 'Should throw exception for duplicate email');
        
        $this->tearDown();
    }
    
    public function testUpdateUserEmail(): void {
        $this->setUp();
        
        $result = $this->userService->updateUserEmail(1, 'updated@example.com');
        $user = $this->userService->getUser(1);
        
        DBTest::assertTrue($result, 'Update should succeed');
        DBTest::assertEqual('updated@example.com', $user['email'], 'Email should be updated');
        
        $this->tearDown();
    }
    
    public function testUpdateUserEmailDuplicate(): void {
        $this->setUp();
        
        DBTest::assertThrows(InvalidArgumentException::class, function() {
            $this->userService->updateUserEmail(1, 'jane@example.com');
        }, 'Should throw exception for duplicate email');
        
        $this->tearDown();
    }
}

class OrderServiceTest {
    private DatabaseTestHelper $dbHelper;
    private OrderService $orderService;
    
    public function setUp(): void {
        $this->dbHelper = new DatabaseTestHelper();
        $this->dbHelper->loadFixture('users');
        $this->dbHelper->loadFixture('products');
        $this->dbHelper->loadFixture('orders');
        
        $orderRepository = new OrderRepository($this->dbHelper->getPDO());
        $userRepository = new UserRepository($this->dbHelper->getPDO());
        $productRepository = new ProductRepository($this->dbHelper->getPDO());
        
        $this->orderService = new OrderService($orderRepository, $userRepository, $productRepository);
        $this->dbHelper->beginTransaction();
    }
    
    public function tearDown(): void {
        $this->dbHelper->tearDown();
    }
    
    public function testCreateOrder(): void {
        $this->setUp();
        
        $items = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1]
        ];
        
        $order = $this->orderService->createOrder(1, $items);
        
        DBTest::assertNotNull($order, 'Order should be created');
        DBTest::assertEqual(1, $order['user_id'], 'User ID should match');
        DBTest::assertEqual(2029.97, $order['total'], 'Total should be calculated correctly');
        DBTest::assertEqual('pending', $order['status'], 'Status should be pending');
        DBTest::assertEqual(2, count($order['items']), 'Should have 2 items');
        
        $this->tearDown();
    }
    
    public function testCreateOrderUserNotFound(): void {
        $this->setUp();
        
        $items = [['product_id' => 1, 'quantity' => 1]];
        
        DBTest::assertThrows(InvalidArgumentException::class, function() use ($items) {
            $this->orderService->createOrder(999, $items);
        }, 'Should throw exception for non-existent user');
        
        $this->tearDown();
    }
    
    public function testCreateOrderProductNotFound(): void {
        $this->setUp();
        
        $items = [['product_id' => 999, 'quantity' => 1]];
        
        DBTest::assertThrows(InvalidArgumentException::class, function() use ($items) {
            $this->orderService->createOrder(1, $items);
        }, 'Should throw exception for non-existent product');
        
        $this->tearDown();
    }
    
    public function testCompleteOrder(): void {
        $this->setUp();
        
        // First create an order
        $items = [['product_id' => 1, 'quantity' => 1]];
        $order = $this->orderService->createOrder(1, $items);
        
        // Then complete it
        $result = $this->orderService->completeOrder($order['id']);
        $updatedOrder = $this->orderService->getOrder($order['id']);
        
        DBTest::assertTrue($result, 'Order completion should succeed');
        DBTest::assertEqual('completed', $updatedOrder['status'], 'Status should be completed');
        
        $this->tearDown();
    }
    
    public function testCompleteOrderNotFound(): void {
        $this->setUp();
        
        DBTest::assertThrows(InvalidArgumentException::class, function() {
            $this->orderService->completeOrder(999);
        }, 'Should throw exception for non-existent order');
        
        $this->tearDown();
    }
}

class DatabaseIntegrationTest {
    private DatabaseTestHelper $dbHelper;
    
    public function setUp(): void {
        $this->dbHelper = new DatabaseTestHelper();
        $this->dbHelper->loadFixture('users');
        $this->dbHelper->loadFixture('products');
        $this->dbHelper->loadFixture('orders');
        $this->dbHelper->beginTransaction();
    }
    
    public function tearDown(): void {
        $this->dbHelper->tearDown();
    }
    
    public function testComplexQuery(): void {
        $this->setUp();
        
        $pdo = $this->dbHelper->getPDO();
        
        // Test complex join query
        $stmt = $pdo->prepare("
            SELECT u.name, COUNT(o.id) as order_count
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            GROUP BY u.id, u.name
            ORDER BY order_count DESC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        DBTest::assertEqual(3, count($results), 'Should return 3 users');
        DBTest::assertEqual(0, $results[0]['order_count'], 'All users should have 0 orders initially');
        
        $this->tearDown();
    }
    
    public function testTransactionRollback(): void {
        $this->setUp();
        
        $pdo = $this->dbHelper->getPDO();
        $userRepository = new UserRepository($pdo);
        
        $initialCount = $userRepository->count();
        
        // Start a nested transaction
        $pdo->beginTransaction();
        
        try {
            // Create a user
            $userRepository->create(['name' => 'Test', 'email' => 'test@test.com']);
            
            // Simulate an error
            throw new Exception('Simulated error');
        } catch (Exception $e) {
            $pdo->rollBack();
        }
        
        $finalCount = $userRepository->count();
        
        DBTest::assertEqual($initialCount, $finalCount, 'Rollback should undo the insert');
        
        $this->tearDown();
    }
    
    public function testDataIntegrity(): void {
        $this->setUp();
        
        $pdo = $this->dbHelper->getPDO();
        
        // Test foreign key constraints
        DBTest::assertThrows(PDOException::class, function() use ($pdo) {
            $pdo->exec("INSERT INTO orders (user_id, total) VALUES (999, 100.00)");
        }, 'Should enforce foreign key constraint');
        
        $this->tearDown();
    }
}

// Main test runner
function runDatabaseTests(): void {
    DBTest::reset();
    
    echo "Database Testing Examples\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "1. UserRepository Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $userRepoTest = new UserRepositoryTest();
    $userRepoTest->testFindById();
    $userRepoTest->testFindByIdNotFound();
    $userRepoTest->testFindByEmail();
    $userRepoTest->testCreateUser();
    $userRepoTest->testUpdateUser();
    $userRepoTest->testDeleteUser();
    $userRepoTest->testFindAll();
    
    echo "\n2. UserService Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $userServiceTest = new UserServiceTest();
    $userServiceTest->testCreateUser();
    $userServiceTest->testCreateUserDuplicateEmail();
    $userServiceTest->testUpdateUserEmail();
    $userServiceTest->testUpdateUserEmailDuplicate();
    
    echo "\n3. OrderService Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $orderServiceTest = new OrderServiceTest();
    $orderServiceTest->testCreateOrder();
    $orderServiceTest->testCreateOrderUserNotFound();
    $orderServiceTest->testCreateOrderProductNotFound();
    $orderServiceTest->testCompleteOrder();
    $orderServiceTest->testCompleteOrderNotFound();
    
    echo "\n4. Database Integration Tests\n";
    echo str_repeat("-", 30) . "\n";
    
    $integrationTest = new DatabaseIntegrationTest();
    $integrationTest->testComplexQuery();
    $integrationTest->testTransactionRollback();
    $integrationTest->testDataIntegrity();
    
    // Print summary
    DBTest::printSummary();
}

// Database Testing Best Practices Guide
function printDatabaseTestingBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Database Testing Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Database Setup:\n";
    echo "   • Use in-memory databases for unit tests\n";
    echo "   • Use transactions for test isolation\n";
    echo "   • Create fixtures for consistent test data\n";
    echo "   • Clean up after each test\n\n";
    
    echo "2. Test Strategies:\n";
    echo "   • Unit tests with in-memory SQLite\n";
    echo "   • Integration tests with real databases\n";
    echo "   • Use separate test databases\n";
    echo "   • Test migrations and schema changes\n\n";
    
    echo "3. Data Management:\n";
    echo "   • Use factories for test data\n";
    echo "   • Use seeders for initial data\n";
    echo "   • Avoid hardcoding test data\n";
    echo "   • Use meaningful test data\n\n";
    
    echo "4. Performance:\n";
    echo "   • Keep database tests fast\n";
    echo "   • Use connection pooling\n";
    echo "   • Optimize test queries\n";
    echo "   • Run database tests in parallel\n\n";
    
    echo "5. Common Pitfalls:\n";
    echo "   • Tests depending on existing data\n";
    echo "   • Tests modifying shared state\n";
    echo "   • Slow database operations\n";
    echo "   • Complex test setup\n";
    echo "   • Brittle test assertions\n\n";
    
    echo "6. Tools for PHP:\n";
    echo "   • PHPUnit Database Extension\n";
    echo "   • Doctrine DBAL\n";
    echo "   • Faker for test data\n";
    echo "   • Database migrations tools\n";
}

// Run the tests
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDatabaseTests();
    printDatabaseTestingBestPractices();
}
?>
