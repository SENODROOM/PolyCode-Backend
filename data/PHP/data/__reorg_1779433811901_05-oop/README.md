# Module 5: Object-Oriented Programming 🏗️

Object-Oriented Programming (OOP) is a programming paradigm that uses objects and classes to structure code. PHP has robust OOP support that helps create organized, reusable, and maintainable code.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand OOP concepts and principles
- Create classes and objects
- Work with properties and methods
- Implement inheritance and polymorphism
- Use encapsulation and access modifiers
- Apply OOP design patterns

## 📝 Topics Covered

1. [OOP Concepts](#oop-concepts)
2. [Classes and Objects](#classes-and-objects)
3. [Properties and Methods](#properties-and-methods)
4. [Constructors and Destructors](#constructors-and-destructors)
5. [Inheritance](#inheritance)
6. [Access Modifiers](#access-modifiers)
7. [Static Members](#static-members)
8. [Abstract Classes and Interfaces](#abstract-classes-and-interfaces)
9. [Practical Examples](#practical-examples)
10. [Exercises](#exercises)

---

## OOP Concepts

### Core Principles
- **Encapsulation**: Bundling data and methods together
- **Inheritance**: Creating new classes from existing ones
- **Polymorphism**: Different objects responding to the same message
- **Abstraction**: Hiding complex implementation details

### Why Use OOP?
- Code reusability
- Better organization
- Easier maintenance
- Modularity
- Real-world modeling

---

## Classes and Objects

### Defining a Class
```php
<?php
    class Car {
        // Properties (variables)
        public $brand;
        public $model;
        public $color;
        
        // Methods (functions)
        public function start() {
            echo "The car is starting...";
        }
        
        public function stop() {
            echo "The car is stopping...";
        }
    }
?>
```

### Creating Objects
```php
<?php
    // Create instances (objects)
    $car1 = new Car();
    $car2 = new Car();
    
    // Set properties
    $car1->brand = "Toyota";
    $car1->model = "Camry";
    $car1->color = "Blue";
    
    $car2->brand = "Honda";
    $car2->model = "Civic";
    $car2->color = "Red";
    
    // Call methods
    $car1->start();  // Outputs: The car is starting...
    $car2->stop();   // Outputs: The car is stopping...
?>
```

### Class with Type Hints
```php
<?php
    class Product {
        public string $name;
        public float $price;
        public int $quantity;
        
        public function calculateTotal(): float {
            return $this->price * $this->quantity;
        }
    }
    
    $product = new Product();
    $product->name = "Laptop";
    $product->price = 999.99;
    $product->quantity = 2;
    
    echo $product->calculateTotal();  // 1999.98
?>
```

---

## Properties and Methods

### Properties (Class Variables)
```php
<?php
    class User {
        public string $name;
        public int $age;
        public string $email;
        private string $password;  // Private property
        
        public function setPassword(string $password): void {
            // Hash password before storing
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
        
        public function verifyPassword(string $input): bool {
            return password_verify($input, $this->password);
        }
    }
?>
```

### Methods (Class Functions)
```php
<?php
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
            if ($b == 0) {
                throw new Exception("Cannot divide by zero");
            }
            return $a / $b;
        }
    }
?>
```

### The $this Keyword
```php
<?php
    class Person {
        public string $name;
        public int $age;
        
        public function introduce(): string {
            return "Hello, my name is " . $this->name . " and I am " . $this->age . " years old.";
        }
        
        public function haveBirthday(): void {
            $this->age++;
            echo "Happy birthday! You are now " . $this->age . " years old.";
        }
    }
    
    $person = new Person();
    $person->name = "John";
    $person->age = 25;
    
    echo $person->introduce();  // Hello, my name is John and I am 25 years old.
    $person->haveBirthday();   // Happy birthday! You are now 26 years old.
?>
```

---

## Constructors and Destructors

### Constructor (__construct)
```php
<?php
    class Student {
        public string $name;
        public int $grade;
        public array $courses;
        
        public function __construct(string $name, int $grade) {
            $this->name = $name;
            $this->grade = $grade;
            $this->courses = [];
            echo "Student $name has been created.<br>";
        }
        
        public function addCourse(string $course): void {
            $this->courses[] = $course;
        }
        
        public function getStudentInfo(): string {
            return "Student: {$this->name}, Grade: {$this->grade}, Courses: " . implode(", ", $this->courses);
        }
    }
    
    $student = new Student("Alice", 10);  // Constructor called automatically
    $student->addCourse("Math");
    $student->addCourse("Science");
    echo $student->getStudentInfo();
?>
```

### Destructor (__destruct)
```php
<?php
    class DatabaseConnection {
        private $connection;
        
        public function __construct(string $host, string $username, string $password) {
            $this->connection = new mysqli($host, $username, $password);
            echo "Database connection established.<br>";
        }
        
        public function __destruct() {
            if ($this->connection) {
                $this->connection->close();
                echo "Database connection closed.<br>";
            }
        }
    }
    
    $db = new DatabaseConnection("localhost", "user", "pass");
    // When script ends or $db is unset, destructor is called automatically
?>
```

---

## Inheritance

### Basic Inheritance
```php
<?php
    class Animal {
        public string $name;
        public int $age;
        
        public function __construct(string $name, int $age) {
            $this->name = $name;
            $this->age = $age;
        }
        
        public function eat(): void {
            echo "{$this->name} is eating.<br>";
        }
        
        public function sleep(): void {
            echo "{$this->name} is sleeping.<br>";
        }
    }
    
    class Dog extends Animal {
        public string $breed;
        
        public function __construct(string $name, int $age, string $breed) {
            parent::__construct($name, $age);  // Call parent constructor
            $this->breed = $breed;
        }
        
        public function bark(): void {
            echo "{$this->name} is barking!<br>";
        }
        
        // Override parent method
        public function eat(): void {
            echo "{$this->name} ({$this->breed}) is eating dog food.<br>";
        }
    }
    
    $dog = new Dog("Buddy", 3, "Golden Retriever");
    $dog->eat();    // Calls overridden method
    $dog->sleep();  // Calls parent method
    $dog->bark();   // Calls child method
?>
```

### Method Overriding
```php
<?php
    class Shape {
        protected string $color;
        
        public function __construct(string $color) {
            $this->color = $color;
        }
        
        public function getArea(): float {
            return 0;  // Default implementation
        }
        
        public function getDescription(): string {
            return "A {$this->color} shape";
        }
    }
    
    class Circle extends Shape {
        private float $radius;
        
        public function __construct(string $color, float $radius) {
            parent::__construct($color);
            $this->radius = $radius;
        }
        
        public function getArea(): float {
            return pi() * $this->radius * $this->radius;
        }
        
        public function getDescription(): string {
            return "A {$this->color} circle with radius {$this->radius}";
        }
    }
    
    class Rectangle extends Shape {
        private float $width;
        private float $height;
        
        public function __construct(string $color, float $width, float $height) {
            parent::__construct($color);
            $this->width = $width;
            $this->height = $height;
        }
        
        public function getArea(): float {
            return $this->width * $this->height;
        }
        
        public function getDescription(): string {
            return "A {$this->color} rectangle ({$this->width} x {$this->height})";
        }
    }
    
    $circle = new Circle("red", 5);
    $rectangle = new Rectangle("blue", 4, 6);
    
    echo $circle->getDescription() . " - Area: " . $circle->getArea() . "<br>";
    echo $rectangle->getDescription() . " - Area: " . $rectangle->getArea() . "<br>";
?>
```

---

## Access Modifiers

### Public, Private, Protected
```php
<?php
    class BankAccount {
        public string $accountNumber;    // Accessible anywhere
        protected float $balance;        // Accessible in class and subclasses
        private string $pin;              // Accessible only in this class
        
        public function __construct(string $accountNumber, string $pin) {
            $this->accountNumber = $accountNumber;
            $this->pin = $pin;
            $this->balance = 0.0;
        }
        
        public function deposit(float $amount): void {
            if ($amount > 0) {
                $this->balance += $amount;
                echo "Deposited: $" . number_format($amount, 2) . "<br>";
            }
        }
        
        public function getBalance(): float {
            return $this->balance;
        }
        
        public function withdraw(float $amount, string $pin): bool {
            if ($this->verifyPin($pin) && $amount <= $this->balance) {
                $this->balance -= $amount;
                echo "Withdrew: $" . number_format($amount, 2) . "<br>";
                return true;
            }
            echo "Withdrawal failed.<br>";
            return false;
        }
        
        private function verifyPin(string $pin): bool {
            return $this->pin === $pin;
        }
        
        protected function getAccountInfo(): string {
            return "Account {$this->accountNumber} has balance $" . number_format($this->balance, 2);
        }
    }
    
    class SavingsAccount extends BankAccount {
        private float $interestRate;
        
        public function __construct(string $accountNumber, string $pin, float $interestRate) {
            parent::__construct($accountNumber, $pin);
            $this->interestRate = $interestRate;
        }
        
        public function applyInterest(): void {
            $interest = $this->balance * $this->interestRate;
            $this->balance += $interest;
            echo "Interest applied: $" . number_format($interest, 2) . "<br>";
        }
        
        public function displayInfo(): void {
            // Can access protected property and method
            echo $this->getAccountInfo() . "<br>";
            echo "Interest rate: " . ($this->interestRate * 100) . "%<br>";
        }
    }
    
    $account = new BankAccount("123456", "9876");
    $account->deposit(1000);
    echo "Balance: $" . number_format($account->getBalance(), 2) . "<br>";
    
    $savings = new SavingsAccount("789012", "1234", 0.05);
    $savings->deposit(5000);
    $savings->applyInterest();
    $savings->displayInfo();
?>
```

### Getters and Setters
```php
<?php
    class Product {
        private string $name;
        private float $price;
        private int $stock;
        
        public function getName(): string {
            return $this->name;
        }
        
        public function setName(string $name): void {
            if (strlen($name) >= 2) {
                $this->name = $name;
            } else {
                throw new Exception("Product name must be at least 2 characters");
            }
        }
        
        public function getPrice(): float {
            return $this->price;
        }
        
        public function setPrice(float $price): void {
            if ($price > 0) {
                $this->price = $price;
            } else {
                throw new Exception("Price must be positive");
            }
        }
        
        public function getStock(): int {
            return $this->stock;
        }
        
        public function setStock(int $stock): void {
            if ($stock >= 0) {
                $this->stock = $stock;
            } else {
                throw new Exception("Stock cannot be negative");
            }
        }
        
        public function isInStock(): bool {
            return $this->stock > 0;
        }
    }
    
    $product = new Product();
    $product->setName("Laptop");
    $product->setPrice(999.99);
    $product->setStock(10);
    
    echo "Product: " . $product->getName() . "<br>";
    echo "Price: $" . number_format($product->getPrice(), 2) . "<br>";
    echo "In stock: " . ($product->isInStock() ? "Yes" : "No") . "<br>";
?>
```

---

## Static Members

### Static Properties and Methods
```php
<?php
    class Counter {
        private static int $count = 0;
        private static array $instances = [];
        
        public string $name;
        
        public function __construct(string $name) {
            $this->name = $name;
            self::$count++;
            self::$instances[] = $this;
        }
        
        public static function getCount(): int {
            return self::$count;
        }
        
        public static function getInstances(): array {
            return self::$instances;
        }
        
        public static function getAverageNameLength(): float {
            if (self::$count === 0) {
                return 0;
            }
            
            $totalLength = 0;
            foreach (self::$instances as $instance) {
                $totalLength += strlen($instance->name);
            }
            
            return $totalLength / self::$count;
        }
        
        public function getInstanceInfo(): string {
            return "Instance '{$this->name}' (Total instances: " . self::$count . ")";
        }
    }
    
    $obj1 = new Counter("Object 1");
    $obj2 = new Counter("Object 2");
    $obj3 = new Counter("Third Object");
    
    echo "Total count: " . Counter::getCount() . "<br>";
    echo $obj1->getInstanceInfo() . "<br>";
    echo $obj2->getInstanceInfo() . "<br>";
    echo "Average name length: " . round(Counter::getAverageNameLength(), 2) . "<br>";
?>
```

### Static Utility Class
```php
<?php
    class MathUtils {
        public static function PI(): float {
            return 3.14159265359;
        }
        
        public static function circleArea(float $radius): float {
            return self::PI() * $radius * $radius;
        }
        
        public static function factorial(int $n): int {
            if ($n <= 1) {
                return 1;
            }
            return $n * self::factorial($n - 1);
        }
        
        public static function isPrime(int $n): bool {
            if ($n <= 1) return false;
            if ($n <= 3) return true;
            
            for ($i = 2; $i * $i <= $n; $i++) {
                if ($n % $i === 0) {
                    return false;
                }
            }
            return true;
        }
    }
    
    echo "PI: " . MathUtils::PI() . "<br>";
    echo "Circle area (radius 5): " . MathUtils::circleArea(5) . "<br>";
    echo "Factorial of 5: " . MathUtils::factorial(5) . "<br>";
    echo "Is 17 prime? " . (MathUtils::isPrime(17) ? "Yes" : "No") . "<br>";
?>
```

---

## Abstract Classes and Interfaces

### Abstract Classes
```php
<?php
    abstract class Vehicle {
        protected string $brand;
        protected string $model;
        protected int $year;
        
        public function __construct(string $brand, string $model, int $year) {
            $this->brand = $brand;
            $this->model = $model;
            $this->year = $year;
        }
        
        public function getInfo(): string {
            return "{$this->year} {$this->brand} {$this->model}";
        }
        
        // Abstract method - must be implemented by child classes
        abstract public function start(): void;
        abstract public function stop(): void;
        
        // Concrete method - can be used as-is or overridden
        public function honk(): void {
            echo "Beep beep!<br>";
        }
    }
    
    class Car extends Vehicle {
        private int $doors;
        
        public function __construct(string $brand, string $model, int $year, int $doors) {
            parent::__construct($brand, $model, $year);
            $this->doors = $doors;
        }
        
        public function start(): void {
            echo "Car engine starting... Vroom!<br>";
        }
        
        public function stop(): void {
            echo "Car engine stopping...<br>";
        }
        
        public function openTrunk(): void {
            echo "Trunk opened.<br>";
        }
    }
    
    class Motorcycle extends Vehicle {
        private bool $hasHelmet;
        
        public function __construct(string $brand, string $model, int $year, bool $hasHelmet) {
            parent::__construct($brand, $model, $year);
            $this->hasHelmet = $hasHelmet;
        }
        
        public function start(): void {
            echo "Motorcycle starting... Vroom vroom!<br>";
        }
        
        public function stop(): void {
            echo "Motorcycle stopping...<br>";
        }
        
        public function doWheelie(): void {
            echo "Doing a wheelie!<br>";
        }
        
        // Override parent method
        public function honk(): void {
            echo "Beep beep beep!<br>";
        }
    }
    
    $car = new Car("Toyota", "Camry", 2022, 4);
    $motorcycle = new Motorcycle("Harley", "Sportster", 2021, true);
    
    echo $car->getInfo() . "<br>";
    $car->start();
    $car->honk();
    $car->stop();
    
    echo $motorcycle->getInfo() . "<br>";
    $motorcycle->start();
    $motorcycle->honk();
    $motorcycle->stop();
?>
```

### Interfaces
```php
<?php
    interface Drawable {
        public function draw(): void;
        public function getArea(): float;
    }
    
    interface Resizable {
        public function resize(float $factor): void;
    }
    
    class Rectangle implements Drawable, Resizable {
        private float $width;
        private float $height;
        
        public function __construct(float $width, float $height) {
            $this->width = $width;
            $this->height = $height;
        }
        
        public function draw(): void {
            echo "Drawing rectangle ({$this->width} x {$this->height})<br>";
        }
        
        public function getArea(): float {
            return $this->width * $this->height;
        }
        
        public function resize(float $factor): void {
            $this->width *= $factor;
            $this->height *= $factor;
            echo "Rectangle resized by factor $factor<br>";
        }
    }
    
    class Circle implements Drawable, Resizable {
        private float $radius;
        
        public function __construct(float $radius) {
            $this->radius = $radius;
        }
        
        public function draw(): void {
            echo "Drawing circle (radius: {$this->radius})<br>";
        }
        
        public function getArea(): float {
            return pi() * $this->radius * $this->radius;
        }
        
        public function resize(float $factor): void {
            $this->radius *= $factor;
            echo "Circle resized by factor $factor<br>";
        }
    }
    
    function drawShape(Drawable $shape): void {
        $shape->draw();
        echo "Area: " . number_format($shape->getArea(), 2) . "<br>";
    }
    
    function resizeAndDraw(Resizable $shape, float $factor): void {
        $shape->resize($factor);
        if ($shape instanceof Drawable) {
            drawShape($shape);
        }
    }
    
    $rectangle = new Rectangle(10, 5);
    $circle = new Circle(7);
    
    drawShape($rectangle);
    drawShape($circle);
    
    echo "<br>Resizing shapes:<br>";
    resizeAndDraw($rectangle, 2);
    resizeAndDraw($circle, 1.5);
?>
```

---

## Practical Examples

### Example 1: E-commerce System
```php
<?php
    abstract class Product {
        protected string $name;
        protected float $price;
        protected int $stock;
        
        public function __construct(string $name, float $price, int $stock) {
            $this->name = $name;
            $this->price = $price;
            $this->stock = $stock;
        }
        
        public function getName(): string {
            return $this->name;
        }
        
        public function getPrice(): float {
            return $this->price;
        }
        
        public function getStock(): int {
            return $this->stock;
        }
        
        public function isInStock(): bool {
            return $this->stock > 0;
        }
        
        abstract public function getDetails(): string;
        
        public function reduceStock(int $quantity): bool {
            if ($this->stock >= $quantity) {
                $this->stock -= $quantity;
                return true;
            }
            return false;
        }
    }
    
    class PhysicalProduct extends Product {
        private float $weight;
        private string $dimensions;
        
        public function __construct(string $name, float $price, int $stock, float $weight, string $dimensions) {
            parent::__construct($name, $price, $stock);
            $this->weight = $weight;
            $this->dimensions = $dimensions;
        }
        
        public function getDetails(): string {
            return "Physical: {$this->name}, Weight: {$this->weight}kg, Size: {$this->dimensions}";
        }
        
        public function getShippingCost(): float {
            return $this->weight * 5.99;  // $5.99 per kg
        }
    }
    
    class DigitalProduct extends Product {
        private string $fileFormat;
        private float $fileSize;
        
        public function __construct(string $name, float $price, string $fileFormat, float $fileSize) {
            parent::__construct($name, $price, 999);  // Unlimited stock
            $this->fileFormat = $fileFormat;
            $this->fileSize = $fileSize;
        }
        
        public function getDetails(): string {
            return "Digital: {$this->name}, Format: {$this->fileFormat}, Size: {$this->fileSize}MB";
        }
        
        public function getShippingCost(): float {
            return 0;  // No shipping for digital products
        }
    }
    
    class ShoppingCart {
        private array $items = [];
        
        public function addItem(Product $product, int $quantity): void {
            if ($product->isInStock()) {
                $this->items[] = ['product' => $product, 'quantity' => $quantity];
                $product->reduceStock($quantity);
                echo "Added {$quantity} x {$product->getName()} to cart<br>";
            } else {
                echo "Product {$product->getName()} is out of stock<br>";
            }
        }
        
        public function getTotal(): float {
            $total = 0;
            foreach ($this->items as $item) {
                $total += $item['product']->getPrice() * $item['quantity'];
                $total += $item['product']->getShippingCost();
            }
            return $total;
        }
        
        public function getCartSummary(): string {
            $summary = "Cart Contents:<br>";
            foreach ($this->items as $item) {
                $summary .= "- {$item['quantity']} x {$item['product']->getName()} @ $" . 
                           number_format($item['product']->getPrice(), 2) . "<br>";
            }
            $summary .= "Total: $" . number_format($this->getTotal(), 2);
            return $summary;
        }
    }
    
    // Demo
    $laptop = new PhysicalProduct("Laptop", 999.99, 5, 2.5, "35x25x2 cm");
    $ebook = new DigitalProduct("PHP eBook", 29.99, "PDF", 5.2);
    
    $cart = new ShoppingCart();
    $cart->addItem($laptop, 1);
    $cart->addItem($ebook, 2);
    
    echo $laptop->getDetails() . "<br>";
    echo $ebook->getDetails() . "<br><br>";
    
    echo $cart->getCartSummary();
?>
```

---

## Exercises

### Exercise 1: Bank Account System
Create a PHP file that:
1. Implements a BankAccount class with inheritance
2. Creates SavingsAccount and CheckingAccount subclasses
3. Demonstrates polymorphism and encapsulation

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: Shape Calculator
Create a PHP file that:
1. Uses abstract classes and interfaces
2. Implements different shape classes
3. Calculates areas and perimeters

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Employee Management
Create a PHP file that:
1. Creates an employee hierarchy
2. Uses static members for tracking
3. Implements proper encapsulation

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand OOP concepts and principles
- [ ] I can create classes and objects
- [ ] I can work with properties and methods
- [ ] I understand constructors and destructors
- [ ] I can implement inheritance
- [ ] I understand access modifiers
- [ ] I can use static members
- [ ] I can work with abstract classes and interfaces
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 6: Forms & User Input](../06-forms/README.md)
