<?php
/**
 * Design Patterns Implementation
 * 
 * This file demonstrates common design patterns in PHP
 * including Singleton, Factory, Observer, Strategy, and Repository patterns.
 */

// Singleton Pattern
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function __clone() {}
    public function __wakeup() {}
}

// Factory Pattern
interface Vehicle {
    public function drive(): string;
}

class Car implements Vehicle {
    public function drive(): string {
        return "Driving a car";
    }
}

class Motorcycle implements Vehicle {
    public function drive(): string {
        return "Riding a motorcycle";
    }
}

class VehicleFactory {
    public static function create(string $type): Vehicle {
        switch ($type) {
            case 'car':
                return new Car();
            case 'motorcycle':
                return new Motorcycle();
            default:
                throw new InvalidArgumentException("Unknown vehicle type: $type");
        }
    }
}

// Observer Pattern
interface Observer {
    public function update(string $event, array $data): void;
}

interface Subject {
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $event, array $data): void;
}

class User implements Subject {
    private array $observers = [];
    private string $name;
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function attach(Observer $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void {
        $this->observers = array_filter($this->observers, fn($o) => $o !== $observer);
    }
    
    public function notify(string $event, array $data): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function login(): void {
        echo "{$this->name} logged in\n";
        $this->notify('login', ['user' => $this->name]);
    }
    
    public function logout(): void {
        echo "{$this->name} logged out\n";
        $this->notify('logout', ['user' => $this->name]);
    }
}

class Logger implements Observer {
    public function update(string $event, array $data): void {
        echo "[LOG] Event: $event, Data: " . json_encode($data) . "\n";
    }
}

class EmailNotifier implements Observer {
    public function update(string $event, array $data): void {
        echo "[EMAIL] Notification sent for event: $event\n";
    }
}

// Strategy Pattern
interface PaymentStrategy {
    public function pay(float $amount): bool;
}

class CreditCardPayment implements PaymentStrategy {
    private string $cardNumber;
    
    public function __construct(string $cardNumber) {
        $this->cardNumber = $cardNumber;
    }
    
    public function pay(float $amount): bool {
        echo "Paid $amount using Credit Card ending in " . substr($this->cardNumber, -4) . "\n";
        return true;
    }
}

class PayPalPayment implements PaymentStrategy {
    private string $email;
    
    public function __construct(string $email) {
        $this->email = $email;
    }
    
    public function pay(float $amount): bool {
        echo "Paid $amount using PayPal account: $this->email\n";
        return true;
    }
}

class ShoppingCart {
    private PaymentStrategy $paymentStrategy;
    private array $items = [];
    
    public function setPaymentStrategy(PaymentStrategy $strategy): void {
        $this->paymentStrategy = $strategy;
    }
    
    public function addItem(string $item, float $price): void {
        $this->items[] = ['item' => $item, 'price' => $price];
    }
    
    public function getTotal(): float {
        return array_sum(array_column($this->items, 'price'));
    }
    
    public function checkout(): bool {
        $total = $this->getTotal();
        return $this->paymentStrategy->pay($total);
    }
}

// Repository Pattern
interface UserRepositoryInterface {
    public function findById(int $id): ?array;
    public function findAll(): array;
    public function save(array $user): bool;
    public function delete(int $id): bool;
}

class UserRepository implements UserRepositoryInterface {
    private array $users = [];
    
    public function __construct() {
        // Initialize with sample data
        $this->users = [
            1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            2 => ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];
    }
    
    public function findById(int $id): ?array {
        return $this->users[$id] ?? null;
    }
    
    public function findAll(): array {
        return $this->users;
    }
    
    public function save(array $user): bool {
        if (!isset($user['id'])) {
            $user['id'] = max(array_keys($this->users)) + 1;
        }
        $this->users[$user['id']] = $user;
        return true;
    }
    
    public function delete(int $id): bool {
        if (isset($this->users[$id])) {
            unset($this->users[$id]);
            return true;
        }
        return false;
    }
}

// Usage Examples
echo "=== Design Patterns Demo ===\n\n";

// Singleton Pattern
echo "1. Singleton Pattern:\n";
$db1 = Database::getInstance();
$db2 = Database::getInstance();
echo "Same instance: " . ($db1 === $db2 ? 'Yes' : 'No') . "\n\n";

// Factory Pattern
echo "2. Factory Pattern:\n";
$car = VehicleFactory::create('car');
$motorcycle = VehicleFactory::create('motorcycle');
echo $car->drive() . "\n";
echo $motorcycle->drive() . "\n\n";

// Observer Pattern
echo "3. Observer Pattern:\n";
$user = new User('Alice');
$logger = new Logger();
$emailNotifier = new EmailNotifier();

$user->attach($logger);
$user->attach($emailNotifier);
$user->login();
$user->logout();
echo "\n";

// Strategy Pattern
echo "4. Strategy Pattern:\n";
$cart = new ShoppingCart();
$cart->addItem('Laptop', 999.99);
$cart->addItem('Mouse', 29.99);

$cart->setPaymentStrategy(new CreditCardPayment('1234567890123456'));
$cart->checkout();

$cart->setPaymentStrategy(new PayPalPayment('user@example.com'));
$cart->checkout();
echo "\n";

// Repository Pattern
echo "5. Repository Pattern:\n";
$repository = new UserRepository();

// Find all users
$users = $repository->findAll();
echo "All users: " . count($users) . "\n";

// Find by ID
$user = $repository->findById(1);
echo "User 1: " . $user['name'] . "\n";

// Save new user
$newUser = ['name' => 'Bob Johnson', 'email' => 'bob@example.com'];
$repository->save($newUser);

// Delete user
$repository->delete(2);
echo "Users after deletion: " . count($repository->findAll()) . "\n";

echo "\n=== Design Patterns Demo Complete ===\n";
?>
