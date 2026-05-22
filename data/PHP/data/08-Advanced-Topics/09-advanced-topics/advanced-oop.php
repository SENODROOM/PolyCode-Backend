<?php
/**
 * Advanced Object-Oriented Programming Concepts
 * 
 * This file demonstrates advanced OOP concepts including:
 * - Abstract classes and interfaces
 * - Traits and composition
 * - Magic methods
 * - Namespaces and autoloading
 * - Late static binding
 */

// Namespace declaration
namespace App\Advanced;

// Autoloader simulation
spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Abstract Class
abstract class Shape {
    protected float $width;
    protected float $height;
    
    public function __construct(float $width, float $height) {
        $this->width = $width;
        $this->height = $height;
    }
    
    abstract public function getArea(): float;
    abstract public function getPerimeter(): float;
    
    public function getDimensions(): string {
        return "Width: {$this->width}, Height: {$this->height}";
    }
}

// Interface
interface Drawable {
    public function draw(): string;
}

interface Resizable {
    public function resize(float $factor): void;
}

// Concrete class implementing abstract class and interfaces
class Rectangle extends Shape implements Drawable, Resizable {
    public function getArea(): float {
        return $this->width * $this->height;
    }
    
    public function getPerimeter(): float {
        return 2 * ($this->width + $this->height);
    }
    
    public function draw(): string {
        return "Drawing a rectangle with dimensions {$this->width}x{$this->height}";
    }
    
    public function resize(float $factor): void {
        $this->width *= $factor;
        $this->height *= $factor;
    }
}

// Trait
trait LoggerTrait {
    private array $logs = [];
    
    public function log(string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $this->logs[] = "[$timestamp] $message";
    }
    
    public function getLogs(): array {
        return $this->logs;
    }
    
    public function clearLogs(): void {
        $this->logs = [];
    }
}

trait TimestampableTrait {
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;
    
    public function setCreatedAt(DateTime $date): void {
        $this->createdAt = $date;
    }
    
    public function setUpdatedAt(DateTime $date): void {
        $this->updatedAt = $date;
    }
    
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }
}

// Class using multiple traits
class Circle implements Drawable {
    use LoggerTrait, TimestampableTrait;
    
    private float $radius;
    
    public function __construct(float $radius) {
        $this->radius = $radius;
        $this->log("Circle created with radius $radius");
        $this->setCreatedAt(new DateTime());
    }
    
    public function getArea(): float {
        return pi() * $this->radius ** 2;
    }
    
    public function getCircumference(): float {
        return 2 * pi() * $this->radius;
    }
    
    public function draw(): string {
        return "Drawing a circle with radius {$this->radius}";
    }
    
    public function setRadius(float $radius): void {
        $this->radius = $radius;
        $this->log("Radius updated to $radius");
        $this->setUpdatedAt(new DateTime());
    }
}

// Magic Methods
class MagicClass {
    private array $data = [];
    private string $name;
    
    public function __construct(string $name) {
        $this->name = $name;
        echo "__construct called: Object '$name' created\n";
    }
    
    public function __destruct() {
        echo "__destruct called: Object '$this->name' destroyed\n";
    }
    
    public function __get(string $property) {
        echo "__get called: Accessing '$property'\n";
        return $this->data[$property] ?? null;
    }
    
    public function __set(string $property, $value): void {
        echo "__set called: Setting '$property' to '$value'\n";
        $this->data[$property] = $value;
    }
    
    public function __isset(string $property): bool {
        echo "__isset called: Checking if '$property' is set\n";
        return isset($this->data[$property]);
    }
    
    public function __unset(string $property): void {
        echo "__unset called: Unsetting '$property'\n";
        unset($this->data[$property]);
    }
    
    public function __call(string $method, array $arguments) {
        echo "__call called: Method '$method' with arguments " . implode(', ', $arguments) . "\n";
        return "Dynamic method called";
    }
    
    public static function __callStatic(string $method, array $arguments) {
        echo "__callStatic called: Static method '$method' with arguments " . implode(', ', $arguments) . "\n";
        return "Static method called";
    }
    
    public function __toString(): string {
        return "__toString called: MagicClass '{$this->name}'";
    }
    
    public function __invoke(string $message): void {
        echo "__invoke called: Object called as function with message '$message'\n";
    }
    
    public function __sleep(): array {
        echo "__sleep called: Preparing for serialization\n";
        return ['data', 'name'];
    }
    
    public function __wakeup(): void {
        echo "__wakeup called: Waking up from serialization\n";
    }
    
    public function __clone(): void {
        echo "__clone called: Object cloned\n";
        $this->name = "Clone of " . $this->name;
    }
}

// Late Static Binding
abstract class ParentClass {
    public static function who(): void {
        echo static::class . "\n";
    }
    
    public function create(): self {
        return new static();
    }
}

class ChildClass extends ParentClass {
    public static function who(): void {
        echo "Child class implementation\n";
    }
}

class AnotherChild extends ParentClass {
    public static function who(): void {
        echo "Another child implementation\n";
    }
}

// Anonymous Classes
interface Greeting {
    public function sayHello(string $name): string;
}

$anonymousGreeting = new class implements Greeting {
    public function sayHello(string $name): string {
        return "Hello, $name from anonymous class!";
    }
};

// Type Declarations and Return Types
class TypeDeclarations {
    public function addIntegers(int $a, int $b): int {
        return $a + $b;
    }
    
    public function processArray(array $data): ?string {
        return empty($data) ? null : implode(', ', $data);
    }
    
    public function getUserData(int $id): object {
        return (object) [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com'
        ];
    }
    
    public function divideNumbers(float $a, float $b): float {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return $a / $b;
    }
    
    public function processItems(iterable $items): array {
        $result = [];
        foreach ($items as $item) {
            $result[] = $item;
        }
        return $result;
    }
}

// Usage Examples
echo "=== Advanced OOP Concepts Demo ===\n\n";

// Abstract Class and Interface
echo "1. Abstract Class and Interface:\n";
$rectangle = new Rectangle(10, 5);
echo $rectangle->getDimensions() . "\n";
echo "Area: " . $rectangle->getArea() . "\n";
echo "Perimeter: " . $rectangle->getPerimeter() . "\n";
echo $rectangle->draw() . "\n";

$rectangle->resize(2);
echo "After resize: " . $rectangle->getDimensions() . "\n\n";

// Traits
echo "2. Traits:\n";
$circle = new Circle(7);
echo "Area: " . $circle->getArea() . "\n";
echo $circle->draw() . "\n";

$circle->setRadius(10);
echo "Logs:\n";
foreach ($circle->getLogs() as $log) {
    echo "  $log\n";
}
echo "\n";

// Magic Methods
echo "3. Magic Methods:\n";
$magic = new MagicClass('Test');
$magic->property = 'value';
echo $magic->property . "\n";
echo isset($magic->property) ? "Property is set\n" : "Property is not set\n";
unset($magic->property);

echo $magic->dynamicMethod('arg1', 'arg2') . "\n";
echo MagicClass::staticMethod('static') . "\n";
echo $magic . "\n";
$magic('Hello from invoke');

// Late Static Binding
echo "\n4. Late Static Binding:\n";
ParentClass::who();
ChildClass::who();
AnotherChild::who();

$child = new ChildClass();
$created = $child->create();
echo get_class($created) . "\n\n";

// Anonymous Class
echo "5. Anonymous Class:\n";
echo $anonymousGreeting->sayHello('World') . "\n\n";

// Type Declarations
echo "6. Type Declarations:\n";
$typed = new TypeDeclarations();
echo "Sum: " . $typed->addIntegers(5, 3) . "\n";
echo "Array processing: " . $typed->processArray(['a', 'b', 'c']) . "\n";
echo "User data: " . json_encode($typed->getUserData(1)) . "\n";
echo "Division: " . $typed->divideNumbers(10.0, 2.0) . "\n";

echo "\n=== Advanced OOP Concepts Demo Complete ===\n";
?>
