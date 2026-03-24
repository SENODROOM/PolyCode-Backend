<?php
/**
 * Design Principles and Patterns
 * 
 * This file demonstrates SOLID principles, design patterns,
 * and architectural patterns for building maintainable PHP applications.
 */

// SOLID Principles Demonstration

// S - Single Responsibility Principle
class UserRegistration
{
    private EmailValidator $emailValidator;
    private UserRepository $userRepository;
    private EmailSender $emailSender;
    
    public function __construct(
        EmailValidator $emailValidator,
        UserRepository $userRepository,
        EmailSender $emailSender
    ) {
        $this->emailValidator = $emailValidator;
        $this->userRepository = $userRepository;
        $this->emailSender = $emailSender;
    }
    
    /**
     * Register a new user
     * This class only handles user registration logic
     */
    public function register(array $userData): User
    {
        $this->validateUserData($userData);
        
        $user = $this->userRepository->create($userData);
        $this->emailSender->sendWelcomeEmail($user);
        
        return $user;
    }
    
    private function validateUserData(array $userData): void
    {
        if (!$this->emailValidator->isValid($userData['email'])) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }
}

// O - Open/Closed Principle
interface PaymentProcessor
{
    public function processPayment(float $amount): PaymentResult;
}

class CreditCardProcessor implements PaymentProcessor
{
    public function processPayment(float $amount): PaymentResult
    {
        // Credit card processing logic
        return new PaymentResult(true, 'Credit card payment processed');
    }
}

class PayPalProcessor implements PaymentProcessor
{
    public function processPayment(float $amount): PaymentResult
    {
        // PayPal processing logic
        return new PaymentResult(true, 'PayPal payment processed');
    }
}

class PaymentService
{
    private PaymentProcessor $processor;
    
    public function __construct(PaymentProcessor $processor)
    {
        $this->processor = $processor;
    }
    
    public function processPayment(float $amount): PaymentResult
    {
        return $this->processor->processPayment($amount);
    }
}

// L - Liskov Substitution Principle
abstract class Bird
{
    abstract public function fly(): void;
    abstract public function makeSound(): void;
}

class Sparrow extends Bird
{
    public function fly(): void
    {
        echo "Sparrow is flying\n";
    }
    
    public function makeSound(): void
    {
        echo "Sparrow chirps\n";
    }
}

class Ostrich extends Bird
{
    public function fly(): void
    {
        // Ostriches can't fly - this violates LSP
        // Better solution: create separate interfaces
        throw new \RuntimeException("Ostriches can't fly");
    }
    
    public function makeSound(): void
    {
        echo "Ostrich booms\n";
    }
}

// Better LSP implementation
interface Flyable
{
    public function fly(): void;
}

interface Soundable
{
    public function makeSound(): void;
}

class Eagle implements Flyable, Soundable
{
    public function fly(): void
    {
        echo "Eagle soars high\n";
    }
    
    public function makeSound(): void
    {
        echo "Eagle screeches\n";
    }
}

class Penguin implements Soundable
{
    public function makeSound(): void
    {
        echo "Penguin squawks\n";
    }
}

// I - Interface Segregation Principle
interface Printable
{
    public function print(): void;
}

interface Scannable
{
    public function scan(): void;
}

interface Faxable
{
    public function fax(): void;
}

class BasicPrinter implements Printable
{
    public function print(): void
    {
        echo "Printing document\n";
    }
}

class MultiFunctionPrinter implements Printable, Scannable, Faxable
{
    public function print(): void
    {
        echo "Printing document\n";
    }
    
    public function scan(): void
    {
        echo "Scanning document\n";
    }
    
    public function fax(): void
    {
        echo "Faxing document\n";
    }
}

// D - Dependency Inversion Principle
interface LoggerInterface
{
    public function log(string $message): void;
}

class FileLogger implements LoggerInterface
{
    public function log(string $message): void
    {
        echo "Logging to file: $message\n";
    }
}

class DatabaseLogger implements LoggerInterface
{
    public function log(string $message): void
    {
        echo "Logging to database: $message\n";
    }
}

class OrderService
{
    private LoggerInterface $logger;
    
    // Depends on abstraction, not concrete implementation
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function createOrder(array $orderData): Order
    {
        $order = new Order($orderData);
        $this->logger->log("Order created: {$order->getId()}");
        
        return $order;
    }
}

// Additional Principles

// DRY - Don't Repeat Yourself
class ValidationHelper
{
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^\+?[\d\s\-\(\)]+$/', $phone);
    }
    
    public static function sanitizeString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

class UserValidator
{
    public function validate(array $userData): array
    {
        $errors = [];
        
        if (!ValidationHelper::validateEmail($userData['email'])) {
            $errors['email'] = 'Invalid email address';
        }
        
        if (isset($userData['phone']) && !ValidationHelper::validatePhone($userData['phone'])) {
            $errors['phone'] = 'Invalid phone number';
        }
        
        return $errors;
    }
}

// KISS - Keep It Simple, Stupid
class SimpleCalculator
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
}

// YAGNI - You Aren't Gonna Need It
class SimpleUserRepository
{
    private array $users = [];
    
    public function save(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }
    
    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }
    
    public function delete(int $id): void
    {
        unset($this->users[$id]);
    }
}

// Design Patterns

// Factory Pattern
interface Notification
{
    public function send(string $message): void;
}

class EmailNotification implements Notification
{
    public function send(string $message): void
    {
        echo "Sending email: $message\n";
    }
}

class SMSNotification implements Notification
{
    public function send(string $message): void
    {
        echo "Sending SMS: $message\n";
    }
}

class PushNotification implements Notification
{
    public function send(string $message): void
    {
        echo "Sending push notification: $message\n";
    }
}

class NotificationFactory
{
    public static function create(string $type): Notification
    {
        switch ($type) {
            case 'email':
                return new EmailNotification();
            case 'sms':
                return new SMSNotification();
            case 'push':
                return new PushNotification();
            default:
                throw new InvalidArgumentException("Unknown notification type: $type");
        }
    }
}

// Singleton Pattern
class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private \PDO $connection;
    
    private function __construct()
    {
        $this->connection = new \PDO('sqlite::memory:');
    }
    
    public static function getInstance(): DatabaseConnection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getConnection(): \PDO
    {
        return $this->connection;
    }
    
    private function __clone() {}
    public function __wakeup() {}
}

// Observer Pattern
interface Observer
{
    public function update(string $event, array $data): void;
}

interface Subject
{
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $event, array $data): void;
}

class User implements Subject
{
    private array $observers = [];
    private string $name;
    private string $email;
    
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }
    
    public function notify(string $event, array $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function changeEmail(string $newEmail): void
    {
        $oldEmail = $this->email;
        $this->email = $newEmail;
        
        $this->notify('email_changed', [
            'old_email' => $oldEmail,
            'new_email' => $newEmail
        ]);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
}

class EmailLogger implements Observer
{
    public function update(string $event, array $data): void
    {
        echo "Email Logger: User $event - " . json_encode($data) . "\n";
    }
}

class AuditLogger implements Observer
{
    public function update(string $event, array $data): void
    {
        echo "Audit Logger: User $event - " . json_encode($data) . "\n";
    }
}

// Strategy Pattern
interface SortingStrategy
{
    public function sort(array $items): array;
}

class BubbleSortStrategy implements SortingStrategy
{
    public function sort(array $items): array
    {
        $sorted = $items;
        $n = count($sorted);
        
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($sorted[$j] > $sorted[$j + 1]) {
                    $temp = $sorted[$j];
                    $sorted[$j] = $sorted[$j + 1];
                    $sorted[$j + 1] = $temp;
                }
            }
        }
        
        return $sorted;
    }
}

class QuickSortStrategy implements SortingStrategy
{
    public function sort(array $items): array
    {
        if (count($items) <= 1) {
            return $items;
        }
        
        $pivot = $items[0];
        $left = [];
        $right = [];
        
        for ($i = 1; $i < count($items); $i++) {
            if ($items[$i] < $pivot) {
                $left[] = $items[$i];
            } else {
                $right[] = $items[$i];
            }
        }
        
        return array_merge(
            $this->sort($left),
            [$pivot],
            $this->sort($right)
        );
    }
}

class Sorter
{
    private SortingStrategy $strategy;
    
    public function __construct(SortingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }
    
    public function setStrategy(SortingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }
    
    public function sort(array $items): array
    {
        return $this->strategy->sort($items);
    }
}

// Repository Pattern
interface Repository
{
    public function find(int $id): ?object;
    public function findAll(): array;
    public function save(object $entity): void;
    public function delete(object $entity): void;
}

class UserRepository implements Repository
{
    private array $users = [];
    private int $nextId = 1;
    
    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }
    
    public function findAll(): array
    {
        return array_values($this->users);
    }
    
    public function save(object $entity): void
    {
        if (!$entity instanceof User) {
            throw new InvalidArgumentException('Entity must be a User');
        }
        
        if ($entity->getId() === null) {
            $entity->setId($this->nextId++);
        }
        
        $this->users[$entity->getId()] = $entity;
    }
    
    public function delete(object $entity): void
    {
        if (!$entity instanceof User) {
            throw new InvalidArgumentException('Entity must be a User');
        }
        
        unset($this->users[$entity->getId()]);
    }
    
    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        
        return null;
    }
}

// Decorator Pattern
interface Coffee
{
    public function getCost(): float;
    public function getDescription(): string;
}

class SimpleCoffee implements Coffee
{
    public function getCost(): float
    {
        return 2.0;
    }
    
    public function getDescription(): string
    {
        return 'Simple coffee';
    }
}

class CoffeeDecorator implements Coffee
{
    protected Coffee $coffee;
    
    public function __construct(Coffee $coffee)
    {
        $this->coffee = $coffee;
    }
    
    public function getCost(): float
    {
        return $this->coffee->getCost();
    }
    
    public function getDescription(): string
    {
        return $this->coffee->getDescription();
    }
}

class MilkDecorator extends CoffeeDecorator
{
    public function getCost(): float
    {
        return $this->coffee->getCost() + 0.5;
    }
    
    public function getDescription(): string
    {
        return $this->coffee->getDescription() . ', milk';
    }
}

class SugarDecorator extends CoffeeDecorator
{
    public function getCost(): float
    {
        return $this->coffee->getCost() + 0.2;
    }
    
    public function getDescription(): string
    {
        return $this->coffee->getDescription() . ', sugar';
    }
}

// Command Pattern
interface Command
{
    public function execute(): void;
    public function undo(): void;
}

class Light
{
    private bool $isOn = false;
    
    public function turnOn(): void
    {
        $this->isOn = true;
        echo "Light is on\n";
    }
    
    public function turnOff(): void
    {
        $this->isOn = false;
        echo "Light is off\n";
    }
    
    public function isOn(): bool
    {
        return $this->isOn;
    }
}

class LightOnCommand implements Command
{
    private Light $light;
    
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    
    public function execute(): void
    {
        $this->light->turnOn();
    }
    
    public function undo(): void
    {
        $this->light->turnOff();
    }
}

class LightOffCommand implements Command
{
    private Light $light;
    
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    
    public function execute(): void
    {
        $this->light->turnOff();
    }
    
    public function undo(): void
    {
        $this->light->turnOn();
    }
}

class RemoteControl
{
    private array $history = [];
    
    public function executeCommand(Command $command): void
    {
        $command->execute();
        $this->history[] = $command;
    }
    
    public function undoLastCommand(): void
    {
        if (!empty($this->history)) {
            $lastCommand = array_pop($this->history);
            $lastCommand->undo();
        }
    }
}

// Design Principles Examples
class DesignPrinciplesExamples
{
    public function demonstrateSOLID(): void
    {
        echo "SOLID Principles Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Single Responsibility
        echo "1. Single Responsibility Principle:\n";
        $emailValidator = new EmailValidator();
        $userRepository = new UserRepository();
        $emailSender = new EmailSender();
        
        $registration = new UserRegistration($emailValidator, $userRepository, $emailSender);
        
        // Open/Closed
        echo "\n2. Open/Closed Principle:\n";
        $creditCardProcessor = new CreditCardProcessor();
        $paymentService = new PaymentService($creditCardProcessor);
        
        $result = $paymentService->processPayment(100.0);
        echo "Payment result: " . $result->getMessage() . "\n";
        
        // Can easily add new payment processors without modifying PaymentService
        $paypalProcessor = new PayPalProcessor();
        $paypalService = new PaymentService($paypalProcessor);
        
        $result = $paypalService->processPayment(50.0);
        echo "PayPal result: " . $result->getMessage() . "\n";
        
        // Liskov Substitution
        echo "\n3. Liskov Substitution Principle:\n";
        $birds = [new Eagle(), new Penguin()];
        
        foreach ($birds as $bird) {
            if ($bird instanceof Soundable) {
                $bird->makeSound();
            }
            
            if ($bird instanceof Flyable) {
                $bird->fly();
            }
        }
        
        // Interface Segregation
        echo "\n4. Interface Segregation Principle:\n";
        $basicPrinter = new BasicPrinter();
        $basicPrinter->print();
        
        $multiPrinter = new MultiFunctionPrinter();
        $multiPrinter->print();
        $multiPrinter->scan();
        
        // Dependency Inversion
        echo "\n5. Dependency Inversion Principle:\n";
        $fileLogger = new FileLogger();
        $orderService = new OrderService($fileLogger);
        
        $order = $orderService->createOrder(['id' => 1, 'amount' => 100]);
        
        // Can easily switch to different logger without changing OrderService
        $dbLogger = new DatabaseLogger();
        $orderService2 = new OrderService($dbLogger);
        $order2 = $orderService2->createOrder(['id' => 2, 'amount' => 200]);
    }
    
    public function demonstrateAdditionalPrinciples(): void
    {
        echo "\nAdditional Principles Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // DRY
        echo "1. DRY (Don't Repeat Yourself):\n";
        $validator = new UserValidator();
        
        $userData = [
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567'
        ];
        
        $errors = $validator->validate($userData);
        echo "Validation errors: " . (empty($errors) ? 'None' : implode(', ', $errors)) . "\n";
        
        // KISS
        echo "\n2. KISS (Keep It Simple, Stupid):\n";
        $calculator = new SimpleCalculator();
        
        echo "5 + 3 = " . $calculator->add(5, 3) . "\n";
        echo "10 - 4 = " . $calculator->subtract(10, 4) . "\n";
        echo "6 * 7 = " . $calculator->multiply(6, 7) . "\n";
        echo "15 / 3 = " . $calculator->divide(15, 3) . "\n";
        
        // YAGNI
        echo "\n3. YAGNI (You Aren't Gonna Need It):\n";
        $userRepository = new SimpleUserRepository();
        
        $user = new User(1, 'John Doe', 'john@example.com');
        $userRepository->save($user);
        
        $foundUser = $userRepository->find(1);
        echo "Found user: " . ($foundUser ? $foundUser->getName() : 'None') . "\n";
    }
    
    public function demonstrateDesignPatterns(): void
    {
        echo "\nDesign Patterns Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Factory Pattern
        echo "1. Factory Pattern:\n";
        $emailNotification = NotificationFactory::create('email');
        $smsNotification = NotificationFactory::create('sms');
        $pushNotification = NotificationFactory::create('push');
        
        $emailNotification->send('Hello via email');
        $smsNotification->send('Hello via SMS');
        $pushNotification->send('Hello via push');
        
        // Singleton Pattern
        echo "\n2. Singleton Pattern:\n";
        $db1 = DatabaseConnection::getInstance();
        $db2 = DatabaseConnection::getInstance();
        
        echo "Same instance: " . ($db1 === $db2 ? 'Yes' : 'No') . "\n";
        
        // Observer Pattern
        echo "\n3. Observer Pattern:\n";
        $user = new User('John Doe', 'john@example.com');
        
        $emailLogger = new EmailLogger();
        $auditLogger = new AuditLogger();
        
        $user->attach($emailLogger);
        $user->attach($auditLogger);
        
        $user->changeEmail('john.doe@example.com');
        
        // Strategy Pattern
        echo "\n4. Strategy Pattern:\n";
        $numbers = [5, 2, 8, 1, 9, 3];
        
        $sorter = new Sorter(new BubbleSortStrategy());
        $sorted = $sorter->sort($numbers);
        echo "Bubble sorted: " . implode(', ', $sorted) . "\n";
        
        $sorter->setStrategy(new QuickSortStrategy());
        $sorted = $sorter->sort($numbers);
        echo "Quick sorted: " . implode(', ', $sorted) . "\n";
        
        // Repository Pattern
        echo "\n5. Repository Pattern:\n";
        $userRepository = new UserRepository();
        
        $user1 = new User(null, 'Alice', 'alice@example.com');
        $user2 = new User(null, 'Bob', 'bob@example.com');
        
        $userRepository->save($user1);
        $userRepository->save($user2);
        
        $allUsers = $userRepository->findAll();
        echo "Total users: " . count($allUsers) . "\n";
        
        $foundUser = $userRepository->findByEmail('alice@example.com');
        echo "Found user by email: " . ($foundUser ? $foundUser->getName() : 'None') . "\n";
        
        // Decorator Pattern
        echo "\n6. Decorator Pattern:\n";
        $coffee = new SimpleCoffee();
        echo $coffee->getDescription() . " - $" . $coffee->getCost() . "\n";
        
        $coffeeWithMilk = new MilkDecorator($coffee);
        echo $coffeeWithMilk->getDescription() . " - $" . $coffeeWithMilk->getCost() . "\n";
        
        $coffeeWithMilkAndSugar = new SugarDecorator($coffeeWithMilk);
        echo $coffeeWithMilkAndSugar->getDescription() . " - $" . $coffeeWithMilkAndSugar->getCost() . "\n";
        
        // Command Pattern
        echo "\n7. Command Pattern:\n";
        $light = new Light();
        $lightOn = new LightOnCommand($light);
        $lightOff = new LightOffCommand($light);
        
        $remote = new RemoteControl();
        
        $remote->executeCommand($lightOn);
        $remote->executeCommand($lightOff);
        
        echo "Undoing last command:\n";
        $remote->undoLastCommand();
    }
    
    public function demonstrateArchitecturalPatterns(): void
    {
        echo "\nArchitectural Patterns Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // MVC Pattern (simplified)
        echo "1. MVC Pattern (simplified):\n";
        
        // Model
        $userModel = new User(1, 'John Doe', 'john@example.com');
        
        // View
        $userView = new UserView();
        
        // Controller
        $userController = new UserController($userModel, $userView);
        
        $userController->displayUser();
        
        // Repository Pattern (already demonstrated)
        echo "\n2. Repository Pattern (already demonstrated above)\n";
        
        // Service Layer Pattern
        echo "\n3. Service Layer Pattern:\n";
        $userService = new ApplicationUserService();
        $userService->registerUser('Jane Doe', 'jane@example.com');
    }
    
    public function runAllExamples(): void
    {
        echo "Design Principles and Patterns Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateSOLID();
        $this->demonstrateAdditionalPrinciples();
        $this->demonstrateDesignPatterns();
        $this->demonstrateArchitecturalPatterns();
    }
}

// Supporting classes for examples
class EmailValidator
{
    public function isValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class UserRepository
{
    private array $users = [];
    
    public function create(array $userData): User
    {
        $user = new User(count($this->users) + 1, $userData['name'], $userData['email']);
        $this->users[$user->getId()] = $user;
        return $user;
    }
}

class EmailSender
{
    public function sendWelcomeEmail(User $user): void
    {
        echo "Welcome email sent to: " . $user->getEmail() . "\n";
    }
}

class PaymentResult
{
    private bool $success;
    private string $message;
    
    public function __construct(bool $success, string $message)
    {
        $this->success = $success;
        $this->message = $message;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
}

class User
{
    private ?int $id;
    private string $name;
    private string $email;
    
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
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}

class Order
{
    private array $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function getId(): int
    {
        return $this->data['id'];
    }
}

class UserView
{
    public function display(User $user): void
    {
        echo "User: {$user->getName()} ({$user->getEmail()})\n";
    }
}

class UserController
{
    private User $model;
    private UserView $view;
    
    public function __construct(User $model, UserView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }
    
    public function displayUser(): void
    {
        $this->view->display($this->model);
    }
}

class ApplicationUserService
{
    private UserRepository $repository;
    private EmailSender $emailSender;
    
    public function __construct()
    {
        $this->repository = new UserRepository();
        $this->emailSender = new EmailSender();
    }
    
    public function registerUser(string $name, string $email): User
    {
        $userData = ['name' => $name, 'email' => $email];
        $user = $this->repository->create($userData);
        $this->emailSender->sendWelcomeEmail($user);
        
        return $user;
    }
}

// Design Principles Best Practices
function printDesignPrinciplesBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Design Principles and Patterns Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. SOLID Principles:\n";
    echo "   • Single Responsibility: One reason to change\n";
    echo "   • Open/Closed: Open for extension, closed for modification\n";
    echo "   • Liskov Substitution: Subtypes must be substitutable\n";
    echo "   • Interface Segregation: Small, focused interfaces\n";
    echo "   • Dependency Inversion: Depend on abstractions\n\n";
    
    echo "2. Additional Principles:\n";
    echo "   • DRY: Don't repeat yourself\n";
    echo "   • KISS: Keep it simple, stupid\n";
    echo "   • YAGNI: You aren't gonna need it\n";
    echo "   • Separation of concerns\n";
    echo "   • Composition over inheritance\n\n";
    
    echo "3. Design Patterns:\n";
    echo "   • Creational: Factory, Singleton, Builder\n";
    echo "   • Structural: Adapter, Decorator, Proxy\n";
    echo "   • Behavioral: Observer, Strategy, Command\n";
    echo "   • Use patterns appropriately\n";
    echo "   • Don't over-engineer\n\n";
    
    echo "4. Architectural Patterns:\n";
    echo "   • MVC: Model-View-Controller\n";
    echo "   • Repository: Data access abstraction\n";
    echo "   • Service Layer: Business logic encapsulation\n";
    echo "   • Dependency Injection: Loose coupling\n";
    echo "   • Event-Driven: Decoupled communication\n\n";
    
    echo "5. Code Organization:\n";
    echo "   • Logical namespace structure\n";
    echo "   • Consistent naming conventions\n";
    echo "   • Proper class responsibilities\n";
    echo "   • Clear interfaces and contracts\n";
    echo "   • Testable and maintainable code\n\n";
    
    echo "6. Best Practices:\n";
    echo "   • Favor composition over inheritance\n";
    echo "   • Program to interfaces, not implementations\n";
    echo "   • Keep classes small and focused\n";
    echo "   • Use dependency injection\n";
    echo "   • Write clear, self-documenting code";
}

// Main execution
function runDesignPrinciplesDemo(): void
{
    $examples = new DesignPrinciplesExamples();
    $examples->runAllExamples();
    printDesignPrinciplesBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDesignPrinciplesDemo();
}
?>
