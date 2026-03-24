<?php
/**
 * Test-Driven Development Examples
 * 
 * This file demonstrates the TDD cycle (Red-Green-Refactor) by building
 * a complete application using test-first development.
 */

// Test Framework (same as unit-testing.php)
class TDDTest {
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
        echo "TDD Test Summary\n";
        echo str_repeat("=", 50) . "\n";
        echo "Tests Run: {$stats['run']}\n";
        echo "Tests Passed: {$stats['passed']}\n";
        echo "Tests Failed: {$stats['failed']}\n";
        echo "Success Rate: " . number_format($stats['success_rate'], 1) . "%\n";
        echo str_repeat("=", 50) . "\n";
    }
}

// TDD Example: Building a Bank Account System

// Step 1: Write failing test (RED)
class BankAccountTest {
    public function testInitialBalance(): void {
        $account = new BankAccount();
        TDDTest::assertEqual(0.0, $account->getBalance(), "Initial balance should be 0");
    }
    
    public function testDeposit(): void {
        $account = new BankAccount();
        $account->deposit(100.0);
        TDDTest::assertEqual(100.0, $account->getBalance(), "Balance should be 100 after deposit");
    }
    
    public function testWithdraw(): void {
        $account = new BankAccount();
        $account->deposit(100.0);
        $account->withdraw(50.0);
        TDDTest::assertEqual(50.0, $account->getBalance(), "Balance should be 50 after withdrawal");
    }
    
    public function testWithdrawInsufficientFunds(): void {
        $account = new BankAccount();
        $account->deposit(50.0);
        TDDTest::assertThrows(InsufficientFundsException::class, function() use ($account) {
            $account->withdraw(100.0);
        }, "Should throw exception for insufficient funds");
    }
    
    public function testNegativeDeposit(): void {
        $account = new BankAccount();
        TDDTest::assertThrows(InvalidArgumentException::class, function() use ($account) {
            $account->deposit(-10.0);
        }, "Should throw exception for negative deposit");
    }
    
    public function testNegativeWithdrawal(): void {
        $account = new BankAccount();
        TDDTest::assertThrows(InvalidArgumentException::class, function() use ($account) {
            $account->withdraw(-10.0);
        }, "Should throw exception for negative withdrawal");
    }
    
    public function testGetTransactionHistory(): void {
        $account = new BankAccount();
        $account->deposit(100.0);
        $account->withdraw(25.0);
        
        $history = $account->getTransactionHistory();
        TDDTest::assertEqual(2, count($history), "Should have 2 transactions");
        TDDTest::assertEqual('deposit', $history[0]['type'], "First transaction should be deposit");
        TDDTest::assertEqual('withdraw', $history[1]['type'], "Second transaction should be withdrawal");
    }
}

// Step 2: Write minimal code to pass tests (GREEN)
class InsufficientFundsException extends Exception {}

class BankAccount {
    private float $balance = 0.0;
    private array $transactions = [];
    
    public function getBalance(): float {
        return $this->balance;
    }
    
    public function deposit(float $amount): void {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be positive');
        }
        
        $this->balance += $amount;
        $this->transactions[] = [
            'type' => 'deposit',
            'amount' => $amount,
            'balance' => $this->balance,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    public function withdraw(float $amount): void {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be positive');
        }
        
        if ($amount > $this->balance) {
            throw new InsufficientFundsException('Insufficient funds');
        }
        
        $this->balance -= $amount;
        $this->transactions[] = [
            'type' => 'withdraw',
            'amount' => $amount,
            'balance' => $this->balance,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    public function getTransactionHistory(): array {
        return $this->transactions;
    }
}

// TDD Example: Building a Shopping Cart

// Step 1: Write failing tests (RED)
class ShoppingCartTest {
    public function testEmptyCart(): void {
        $cart = new ShoppingCart();
        TDDTest::assertEqual(0.0, $cart->getTotal(), "Empty cart total should be 0");
        TDDTest::assertEqual(0, $cart->getItemCount(), "Empty cart should have 0 items");
    }
    
    public function testAddItem(): void {
        $cart = new ShoppingCart();
        $item = new CartItem('Book', 12.99, 2);
        $cart->addItem($item);
        
        TDDTest::assertEqual(1, $cart->getItemCount(), "Cart should have 1 item");
        TDDTest::assertEqual(25.98, $cart->getTotal(), "Total should be 25.98");
    }
    
    public function testAddMultipleItems(): void {
        $cart = new ShoppingCart();
        $cart->addItem(new CartItem('Book', 12.99, 2));
        $cart->addItem(new CartItem('Pen', 1.99, 3));
        
        TDDTest::assertEqual(2, $cart->getItemCount(), "Cart should have 2 items");
        TDDTest::assertEqual(31.95, $cart->getTotal(), "Total should be 31.95");
    }
    
    public function testRemoveItem(): void {
        $cart = new ShoppingCart();
        $item = new CartItem('Book', 12.99, 2);
        $cart->addItem($item);
        $cart->removeItem($item);
        
        TDDTest::assertEqual(0, $cart->getItemCount(), "Cart should be empty after removal");
        TDDTest::assertEqual(0.0, $cart->getTotal(), "Total should be 0 after removal");
    }
    
    public function testClearCart(): void {
        $cart = new ShoppingCart();
        $cart->addItem(new CartItem('Book', 12.99, 2));
        $cart->addItem(new CartItem('Pen', 1.99, 3));
        $cart->clear();
        
        TDDTest::assertEqual(0, $cart->getItemCount(), "Cart should be empty after clear");
        TDDTest::assertEqual(0.0, $cart->getTotal(), "Total should be 0 after clear");
    }
    
    public function testGetItems(): void {
        $cart = new ShoppingCart();
        $item1 = new CartItem('Book', 12.99, 2);
        $item2 = new CartItem('Pen', 1.99, 3);
        $cart->addItem($item1);
        $cart->addItem($item2);
        
        $items = $cart->getItems();
        TDDTest::assertEqual(2, count($items), "Should return 2 items");
        TDDTest::assertTrue($items[0] instanceof CartItem, "Items should be CartItem instances");
    }
    
    public function testUpdateQuantity(): void {
        $cart = new ShoppingCart();
        $item = new CartItem('Book', 12.99, 2);
        $cart->addItem($item);
        $cart->updateQuantity($item, 5);
        
        TDDTest::assertEqual(64.95, $cart->getTotal(), "Total should reflect updated quantity");
    }
    
    public function testUpdateQuantityToZero(): void {
        $cart = new ShoppingCart();
        $item = new CartItem('Book', 12.99, 2);
        $cart->addItem($item);
        $cart->updateQuantity($item, 0);
        
        TDDTest::assertEqual(0, $cart->getItemCount(), "Item should be removed when quantity is 0");
    }
}

// Step 2: Write minimal code to pass tests (GREEN)
class CartItem {
    private string $name;
    private float $price;
    private int $quantity;
    
    public function __construct(string $name, float $price, int $quantity) {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getPrice(): float {
        return $this->price;
    }
    
    public function getQuantity(): int {
        return $this->quantity;
    }
    
    public function getSubtotal(): float {
        return $this->price * $this->quantity;
    }
    
    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }
    
    public function equals(CartItem $other): bool {
        return $this->name === $other->name && $this->price === $other->price;
    }
}

class ShoppingCart {
    private array $items = [];
    
    public function addItem(CartItem $item): void {
        // Check if item already exists
        foreach ($this->items as $existingItem) {
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());
                return;
            }
        }
        
        $this->items[] = $item;
    }
    
    public function removeItem(CartItem $item): void {
        foreach ($this->items as $key => $existingItem) {
            if ($existingItem->equals($item)) {
                unset($this->items[$key]);
                $this->items = array_values($this->items); // Re-index array
                return;
            }
        }
    }
    
    public function updateQuantity(CartItem $item, int $quantity): void {
        if ($quantity <= 0) {
            $this->removeItem($item);
            return;
        }
        
        foreach ($this->items as $existingItem) {
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity($quantity);
                return;
            }
        }
    }
    
    public function getItems(): array {
        return $this->items;
    }
    
    public function getItemCount(): int {
        return count($this->items);
    }
    
    public function getTotal(): float {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getSubtotal();
        }
        return $total;
    }
    
    public function clear(): void {
        $this->items = [];
    }
}

// TDD Example: Building a URL Router

// Step 1: Write failing tests (RED)
class RouterTest {
    public function testAddRoute(): void {
        $router = new Router();
        $router->addRoute('GET', '/home', 'HomeController@index');
        
        $routes = $router->getRoutes();
        TDDTest::assertTrue(isset($routes['GET']['/home']), "Route should be added");
        TDDTest::assertEqual('HomeController@index', $routes['GET']['/home'], "Route handler should match");
    }
    
    public function testAddMultipleRoutes(): void {
        $router = new Router();
        $router->addRoute('GET', '/home', 'HomeController@index');
        $router->addRoute('POST', '/login', 'AuthController@login');
        
        $routes = $router->getRoutes();
        TDDTest::assertEqual(2, count($routes), "Should have 2 route groups");
        TDDTest::assertEqual(1, count($routes['GET']), "Should have 1 GET route");
        TDDTest::assertEqual(1, count($routes['POST']), "Should have 1 POST route");
    }
    
    public function testDispatchExistingRoute(): void {
        $router = new Router();
        $router->addRoute('GET', '/home', 'HomeController@index');
        
        $result = $router->dispatch('GET', '/home');
        TDDTest::assertEqual('HomeController@index', $result, "Should dispatch to correct handler");
    }
    
    public function testDispatchNonExistentRoute(): void {
        $router = new Router();
        
        TDDTest::assertThrows(RouteNotFoundException::class, function() use ($router) {
            $router->dispatch('GET', '/nonexistent');
        }, "Should throw exception for non-existent route");
    }
    
    public function testDispatchWithParameters(): void {
        $router = new Router();
        $router->addRoute('GET', '/user/{id}', 'UserController@show');
        
        $result = $router->dispatch('GET', '/user/123');
        TDDTest::assertEqual('UserController@show', $result['handler'], "Should dispatch to correct handler");
        TDDTest::assertEqual(['id' => '123'], $result['params'], "Should extract route parameters");
    }
    
    public function testDispatchWithMultipleParameters(): void {
        $router = new Router();
        $router->addRoute('GET', '/user/{id}/post/{postId}', 'UserController@post');
        
        $result = $router->dispatch('GET', '/user/123/post/456');
        TDDTest::assertEqual(['id' => '123', 'postId' => '456'], $result['params'], "Should extract multiple parameters");
    }
    
    public function testAddRouteWithConstraints(): void {
        $router = new Router();
        $router->addRoute('GET', '/user/{id}', 'UserController@show', ['id' => '\d+']);
        
        $result = $router->dispatch('GET', '/user/123');
        TDDTest::assertEqual('UserController@show', $result['handler'], "Should match with constraints");
        
        TDDTest::assertThrows(RouteNotFoundException::class, function() use ($router) {
            $router->dispatch('GET', '/user/abc');
        }, "Should not match when constraints fail");
    }
}

// Step 2: Write minimal code to pass tests (GREEN)
class RouteNotFoundException extends Exception {}

class Router {
    private array $routes = [];
    
    public function addRoute(string $method, string $path, string $handler, array $constraints = []): void {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'constraints' => $constraints
        ];
    }
    
    public function getRoutes(): array {
        return $this->routes;
    }
    
    public function dispatch(string $method, string $uri): array {
        if (!isset($this->routes[$method])) {
            throw new RouteNotFoundException("No routes found for method: $method");
        }
        
        foreach ($this->routes[$method] as $path => $route) {
            $params = $this->matchRoute($path, $uri, $route['constraints']);
            if ($params !== null) {
                return [
                    'handler' => $route['handler'],
                    'params' => $params
                ];
            }
        }
        
        throw new RouteNotFoundException("No route found for: $method $uri");
    }
    
    private function matchRoute(string $routePath, string $uri, array $constraints): ?array {
        // Convert route path to regex
        $pattern = preg_replace('/{([^}]+)}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }
        
        // Extract parameter names from route path
        preg_match_all('/{([^}]+)}/', $routePath, $paramNames);
        $paramNames = $paramNames[1];
        
        $params = [];
        for ($i = 1; $i < count($matches); $i++) {
            $paramName = $paramNames[$i - 1] ?? '';
            $paramValue = $matches[$i];
            
            // Check constraints
            if (isset($constraints[$paramName]) && !preg_match('#^' . $constraints[$paramName] . '$#', $paramValue)) {
                return null;
            }
            
            $params[$paramName] = $paramValue;
        }
        
        return $params;
    }
}

// Step 3: Refactor (REFACTOR)
// After tests pass, we can improve the code structure, add features, etc.

// Enhanced Bank Account with additional features (after refactoring)
class EnhancedBankAccount extends BankAccount {
    private string $accountNumber;
    private string $ownerName;
    
    public function __construct(string $accountNumber, string $ownerName) {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
    }
    
    public function getAccountNumber(): string {
        return $this->accountNumber;
    }
    
    public function getOwnerName(): string {
        return $this->ownerName;
    }
    
    public function transfer(EnhancedBankAccount $toAccount, float $amount): void {
        $this->withdraw($amount);
        $toAccount->deposit($amount);
    }
    
    public function getAccountSummary(): array {
        return [
            'account_number' => $this->accountNumber,
            'owner_name' => $this->ownerName,
            'balance' => $this->getBalance(),
            'transaction_count' => count($this->getTransactionHistory())
        ];
    }
}

// TDD Demo Runner
function runTDDDemo(): void {
    TDDTest::reset();
    
    echo "Test-Driven Development Examples\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "1. Bank Account System (TDD Example)\n";
    echo str_repeat("-", 40) . "\n";
    
    $bankAccountTest = new BankAccountTest();
    $bankAccountTest->testInitialBalance();
    $bankAccountTest->testDeposit();
    $bankAccountTest->testWithdraw();
    $bankAccountTest->testWithdrawInsufficientFunds();
    $bankAccountTest->testNegativeDeposit();
    $bankAccountTest->testNegativeWithdrawal();
    $bankAccountTest->testGetTransactionHistory();
    
    echo "\n2. Shopping Cart System (TDD Example)\n";
    echo str_repeat("-", 40) . "\n";
    
    $cartTest = new ShoppingCartTest();
    $cartTest->testEmptyCart();
    $cartTest->testAddItem();
    $cartTest->testAddMultipleItems();
    $cartTest->testRemoveItem();
    $cartTest->testClearCart();
    $cartTest->testGetItems();
    $cartTest->testUpdateQuantity();
    $cartTest->testUpdateQuantityToZero();
    
    echo "\n3. URL Router System (TDD Example)\n";
    echo str_repeat("-", 40) . "\n";
    
    $routerTest = new RouterTest();
    $routerTest->testAddRoute();
    $routerTest->testAddMultipleRoutes();
    $routerTest->testDispatchExistingRoute();
    $routerTest->testDispatchNonExistentRoute();
    $routerTest->testDispatchWithParameters();
    $routerTest->testDispatchWithMultipleParameters();
    $routerTest->testAddRouteWithConstraints();
    
    // Print summary
    TDDTest::printSummary();
    
    // Demonstrate refactored code
    echo "\n4. Refactored Code Example\n";
    echo str_repeat("-", 40) . "\n";
    
    $account1 = new EnhancedBankAccount('123456', 'John Doe');
    $account2 = new EnhancedBankAccount('789012', 'Jane Smith');
    
    $account1->deposit(1000.0);
    $account2->deposit(500.0);
    
    echo "Before transfer:\n";
    echo "Account 1: " . json_encode($account1->getAccountSummary()) . "\n";
    echo "Account 2: " . json_encode($account2->getAccountSummary()) . "\n";
    
    $account1->transfer($account2, 200.0);
    
    echo "\nAfter transfer:\n";
    echo "Account 1: " . json_encode($account1->getAccountSummary()) . "\n";
    echo "Account 2: " . json_encode($account2->getAccountSummary()) . "\n";
}

// TDD Best Practices Guide
function printTDDPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TDD Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. RED Phase:\n";
    echo "   - Write a small, focused test that fails\n";
    echo "   - Test one specific behavior or requirement\n";
    echo "   - Keep tests simple and readable\n";
    echo "   - Use descriptive test names\n\n";
    
    echo "2. GREEN Phase:\n";
    echo "   - Write the minimum code needed to pass the test\n";
    echo "   - Don't add extra features yet\n";
    echo "   - Focus on making the test pass\n";
    echo "   - Use simple, straightforward implementations\n\n";
    
    echo "3. REFACTOR Phase:\n";
    echo "   - Improve code quality without changing behavior\n";
    echo "   - Remove duplication\n";
    echo "   - Improve naming and structure\n";
    echo "   - Ensure all tests still pass\n\n";
    
    echo "4. General Guidelines:\n";
    echo "   - Write tests before production code\n";
    echo "   - Keep test cycles short (minutes, not hours)\n";
    echo "   - Test one thing at a time\n";
    echo "   - Use meaningful test data\n";
    echo "   - Maintain high test coverage\n";
    echo "   - Run tests frequently\n\n";
    
    echo "5. Benefits of TDD:\n";
    echo "   - Better code design\n";
    echo "   - Fewer bugs\n";
    echo "   - Comprehensive test suite\n";
    echo "   - Confidence in refactoring\n";
    echo "   - Living documentation\n";
    echo "   - Reduced debugging time\n";
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runTDDDemo();
    printTDDPractices();
}
?>
