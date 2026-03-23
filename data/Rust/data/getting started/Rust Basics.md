# Module 2: Rust Basics

## 🎯 Learning Objectives

By the end of this module, you will:
- Master Rust's variable binding and mutability
- Understand Rust's data types and type inference
- Learn about ownership, borrowing, and lifetimes
- Practice with functions and control flow
- Understand pattern matching in Rust

## 📚 Topics Covered

### 1. Variables and Mutability
- `let` keyword for variable binding
- `mut` keyword for mutable variables
- Shadowing vs mutation
- Constant declarations with `const`

### 2. Data Types
- Scalar types (integers, floats, booleans, characters)
- Compound types (tuples, arrays)
- Type inference
- Type annotations

### 3. Ownership System
- Ownership rules
- Moving values
- Borrowing with references
- Slices

### 4. Functions and Control Flow
- Function definitions and parameters
- Statements vs expressions
- Control flow (if, loop, while, for)
- Pattern matching with match

## 💻 Practical Examples

### Example 1: Variables and Mutability

```rust
fn main() {
    // Immutable variable (default)
    let x = 5;
    println!("x = {}", x);
    // x = 6; // This would cause an error!
    
    // Mutable variable
    let mut y = 10;
    println!("y = {}", y);
    y = 15; // This works!
    println!("y = {}", y);
    
    // Shadowing
    let z = 20;
    println!("z = {}", z);
    let z = z + 5; // New variable that shadows the old one
    println!("z = {}", z);
    
    // Constants (must have type annotation)
    const MAX_POINTS: u32 = 100_000;
    println!("Max points: {}", MAX_POINTS);
    
    // Multiple variables
    let (a, b, c) = (1, 2, 3);
    println!("a = {}, b = {}, c = {}", a, b, c);
}
```

### Example 2: Data Types in Detail

```rust
fn main() {
    // Integer types
    let small_number: i8 = 127; // -128 to 127
    let medium_number: i32 = 1_000_000; // Default integer type
    let big_number: i64 = 9_223_372_036_854_775_807;
    let unsigned_number: u32 = 4_000_000_000; // 0 to 4,294,967,295
    
    // Floating point types
    let pi: f64 = 3.141592653589793; // Default float type
    let e: f32 = 2.71828;
    
    // Boolean type
    let is_rust_awesome: bool = true;
    let is_hard: bool = false;
    
    // Character type (Unicode scalar value)
    let letter: char = 'R';
    let emoji: char = '🦀';
    let greek: char = 'α';
    
    // Compound types - Tuple
    let person: (String, i32, bool) = (
        "Alice".to_string(),
        30,
        true
    );
    
    // Destructure tuple
    let (name, age, is_student) = person;
    println!("{} is {} years old, student: {}", name, age, is_student);
    
    // Access tuple elements
    println!("Name: {}", person.0);
    println!("Age: {}", person.1);
    
    // Compound types - Array (fixed size)
    let numbers: [i32; 5] = [1, 2, 3, 4, 5];
    let same_values: [i32; 3] = [0; 3]; // [0, 0, 0]
    
    // Access array elements
    println!("First number: {}", numbers[0]);
    println!("Array length: {}", numbers.len());
    
    // Array slices
    let slice = &numbers[1..4]; // Elements 1, 2, 3
    println!("Slice: {:?}", slice);
    
    // Type inference
    let inferred = 42; // i32
    let inferred_float = 3.14; // f64
    let inferred_string = "Hello"; // &str
    
    println!("Inferred types: {} {} {}", inferred, inferred_float, inferred_string);
}
```

### Example 3: Ownership and Borrowing

```rust
fn main() {
    // String ownership
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved to s2
    
    // println!("{}", s1); // ERROR: value borrowed after move
    println!("s2 = {}", s2); // This works
    
    // Clone to create a deep copy
    let s3 = String::from("world");
    let s4 = s3.clone(); // Explicit clone
    
    println!("s3 = {}", s3); // This works
    println!("s4 = {}", s4); // This also works
    
    // Borrowing with references
    let s5 = String::from("Rust");
    let len = calculate_length(&s5); // Pass reference
    
    println!("Length of '{}' is {}", s5, len); // s5 is still valid
    
    // Mutable references
    let mut s6 = String::from("mutable");
    change_string(&mut s6);
    println!("Changed string: {}", s6);
    
    // String slices
    let s7 = String::from("Hello, World!");
    let hello = &s7[0..5]; // "Hello"
    let world = &s7[7..12]; // "World"
    
    println!("Slice 1: {}, Slice 2: {}", hello, world);
}

fn calculate_length(s: &String) -> usize {
    s.len()
}

fn change_string(s: &mut String) {
    s.push_str(" and modified");
}
```

### Example 4: Functions and Control Flow

```rust
// Function with parameters and return value
fn add(a: i32, b: i32) -> i32 {
    a + b // No semicolon = expression (returns value)
}

// Function that returns a tuple
fn calculate_stats(numbers: &[i32]) -> (i32, f64, i32, i32) {
    let sum: i32 = numbers.iter().sum();
    let mean: f64 = sum as f64 / numbers.len() as f64;
    let min = *numbers.iter().min().unwrap();
    let max = *numbers.iter().max().unwrap();
    
    (sum, mean, min, max)
}

// Function with no return value
fn print_greeting(name: &str) {
    println!("Hello, {}!", name);
}

// Function with early return
fn divide(a: f64, b: f64) -> Option<f64> {
    if b == 0.0 {
        None // Early return
    } else {
        Some(a / b) // Return value
    }
}

fn main() {
    // Function calls
    let result = add(10, 5);
    println!("10 + 5 = {}", result);
    
    print_greeting("Rustacean");
    
    // Working with tuples
    let numbers = [10, 20, 30, 40, 50];
    let (sum, mean, min, max) = calculate_stats(&numbers);
    
    println!("Stats: sum={}, mean={:.2}, min={}, max={}", sum, mean, min, max);
    
    // Option handling
    match divide(10.0, 2.0) {
        Some(result) => println!("10 / 2 = {}", result),
        None => println!("Cannot divide by zero"),
    }
    
    match divide(10.0, 0.0) {
        Some(result) => println!("10 / 0 = {}", result),
        None => println!("Cannot divide by zero"),
    }
}
```

### Example 5: Control Flow and Pattern Matching

```rust
fn main() {
    // If expressions
    let number = 42;
    let description = if number % 2 == 0 {
        "even"
    } else {
        "odd"
    };
    println!("{} is {}", number, description);
    
    // Loop with break and continue
    let mut counter = 0;
    let result = loop {
        counter += 1;
        
        if counter == 3 {
            continue; // Skip iteration 3
        }
        
        if counter == 5 {
            break counter * 2; // Return value from loop
        }
    };
    
    println!("Loop result: {}", result);
    
    // While loop
    let mut count = 3;
    while count > 0 {
        println!("Countdown: {}", count);
        count -= 1;
    }
    
    // For loop with ranges
    println!("Counting up:");
    for i in 1..=5 {
        println!("{}", i);
    }
    
    // For loop with iterators
    let fruits = vec!["apple", "banana", "orange"];
    println!("Fruits:");
    for fruit in &fruits {
        println!("- {}", fruit);
    }
    
    // Pattern matching with match
    let day = 3;
    match day {
        1 => println!("Monday"),
        2 => println!("Tuesday"),
        3 => println!("Wednesday"),
        4 => println!("Thursday"),
        5 => println!("Friday"),
        6 | 7 => println!("Weekend"),
        _ => println!("Invalid day"),
    }
    
    // Match with ranges
    let score = 85;
    match score {
        90..=100 => println!("A"),
        80..=89 => println!("B"),
        70..=79 => println!("C"),
        60..=69 => println!("D"),
        _ => println!("F"),
    }
    
    // Match with Option
    let maybe_number = Some(42);
    match maybe_number {
        Some(n) => println!("Number is {}", n),
        None => println!("No number"),
    }
    
    // Match with destructuring
    let point = (3, 5);
    match point {
        (0, y) => println!("On y-axis at {}", y),
        (x, 0) => println!("On x-axis at {}", x),
        (x, y) => println!("At ({}, {})", x, y),
    }
}
```

## 🎮 Interactive Exercises

### Exercise 1: Variable Practice
Create variables for:
- Your name (String)
- Your age (u32)
- Whether you like programming (bool)
- A list of your favorite numbers (array)

Make the age mutable and change it.

```rust
// Your solution here
```

### Exercise 2: Ownership Exercise
Create a function that takes a string reference and returns its length without taking ownership. Demonstrate that the original string is still accessible after the function call.

```rust
// Your solution here
```

### Exercise 3: Pattern Matching
Create a function that takes an age and returns a life stage:
- 0-12: "Child"
- 13-17: "Teenager"
- 18-64: "Adult"
- 65+: "Senior"

Use pattern matching to implement this.

```rust
// Your solution here
```

## 🌐 Real-World Applications

### 1. Simple Calculator

```rust
use std::io;

enum Operation {
    Add,
    Subtract,
    Multiply,
    Divide,
}

fn calculate(a: f64, b: f64, op: Operation) -> Option<f64> {
    match op {
        Operation::Add => Some(a + b),
        Operation::Subtract => Some(a - b),
        Operation::Multiply => Some(a * b),
        Operation::Divide => {
            if b == 0.0 {
                None
            } else {
                Some(a / b)
            }
        }
    }
}

fn main() {
    println!("Rust Calculator");
    
    loop {
        println!("Enter operation (add, subtract, multiply, divide, quit):");
        
        let mut operation = String::new();
        io::stdin()
            .read_line(&mut operation)
            .expect("Failed to read line");
        
        let operation = operation.trim();
        
        if operation == "quit" {
            break;
        }
        
        let op = match operation {
            "add" => Operation::Add,
            "subtract" => Operation::Subtract,
            "multiply" => Operation::Multiply,
            "divide" => Operation::Divide,
            _ => {
                println!("Unknown operation!");
                continue;
            }
        };
        
        println!("Enter first number:");
        let mut num1 = String::new();
        io::stdin()
            .read_line(&mut num1)
            .expect("Failed to read line");
        
        let num1: f64 = match num1.trim().parse() {
            Ok(num) => num,
            Err(_) => {
                println!("Invalid number!");
                continue;
            }
        };
        
        println!("Enter second number:");
        let mut num2 = String::new();
        io::stdin()
            .read_line(&mut num2)
            .expect("Failed to read line");
        
        let num2: f64 = match num2.trim().parse() {
            Ok(num) => num,
            Err(_) => {
                println!("Invalid number!");
                continue;
            }
        };
        
        match calculate(num1, num2, op) {
            Some(result) => println!("Result: {}", result),
            None => println!("Error: Cannot divide by zero"),
        }
    }
}
```

### 2: Temperature Converter

```rust
enum TemperatureUnit {
    Celsius,
    Fahrenheit,
    Kelvin,
}

struct Temperature {
    value: f64,
    unit: TemperatureUnit,
}

impl Temperature {
    fn new(value: f64, unit: TemperatureUnit) -> Temperature {
        Temperature { value, unit }
    }
    
    fn to_celsius(&self) -> f64 {
        match self.unit {
            TemperatureUnit::Celsius => self.value,
            TemperatureUnit::Fahrenheit => (self.value - 32.0) * 5.0 / 9.0,
            TemperatureUnit::Kelvin => self.value - 273.15,
        }
    }
    
    fn to_fahrenheit(&self) -> f64 {
        match self.unit {
            TemperatureUnit::Celsius => (self.value * 9.0 / 5.0) + 32.0,
            TemperatureUnit::Fahrenheit => self.value,
            TemperatureUnit::Kelvin => (self.value - 273.15) * 9.0 / 5.0 + 32.0,
        }
    }
    
    fn to_kelvin(&self) -> f64 {
        match self.unit {
            TemperatureUnit::Celsius => self.value + 273.15,
            TemperatureUnit::Fahrenheit => (self.value - 32.0) * 5.0 / 9.0 + 273.15,
            TemperatureUnit::Kelvin => self.value,
        }
    }
    
    fn convert_to(&self, target_unit: TemperatureUnit) -> Temperature {
        let celsius = self.to_celsius();
        let converted_value = match target_unit {
            TemperatureUnit::Celsius => celsius,
            TemperatureUnit::Fahrenheit => (celsius * 9.0 / 5.0) + 32.0,
            TemperatureUnit::Kelvin => celsius + 273.15,
        };
        
        Temperature::new(converted_value, target_unit)
    }
    
    fn display(&self) {
        let unit_str = match self.unit {
            TemperatureUnit::Celsius => "°C",
            TemperatureUnit::Fahrenheit => "°F",
            TemperatureUnit::Kelvin => "K",
        };
        println!("Temperature: {:.2}{}", self.value, unit_str);
    }
}

fn main() {
    let temps = vec![
        Temperature::new(25.0, TemperatureUnit::Celsius),
        Temperature::new(77.0, TemperatureUnit::Fahrenheit),
        Temperature::new(298.15, TemperatureUnit::Kelvin),
    ];
    
    println!("Temperature Conversions:");
    println!("========================");
    
    for temp in &temps {
        temp.display();
        
        let celsius = temp.convert_to(TemperatureUnit::Celsius);
        let fahrenheit = temp.convert_to(TemperatureUnit::Fahrenheit);
        let kelvin = temp.convert_to(TemperatureUnit::Kelvin);
        
        println!("  {:.2}°C", celsius.value);
        println!("  {:.2}°F", fahrenheit.value);
        println!("  {:.2}K", kelvin.value);
        println!();
    }
}
```

### 3: Simple Inventory System

```rust
#[derive(Debug, Clone)]
struct Product {
    id: u32,
    name: String,
    price: f64,
    quantity: u32,
}

impl Product {
    fn new(id: u32, name: String, price: f64, quantity: u32) -> Product {
        Product {
            id,
            name,
            price,
            quantity,
        }
    }
    
    fn total_value(&self) -> f64 {
        self.price * self.quantity as f64
    }
    
    fn display(&self) {
        println!("Product {}: {} - ${:.2} ({} in stock)", 
                 self.id, self.name, self.price, self.quantity);
    }
}

struct Inventory {
    products: Vec<Product>,
}

impl Inventory {
    fn new() -> Inventory {
        Inventory {
            products: Vec::new(),
        }
    }
    
    fn add_product(&mut self, product: Product) {
        self.products.push(product);
    }
    
    fn find_product(&self, id: u32) -> Option<&Product> {
        self.products.iter().find(|p| p.id == id)
    }
    
    fn update_quantity(&mut self, id: u32, new_quantity: u32) -> bool {
        match self.products.iter_mut().find(|p| p.id == id) {
            Some(product) => {
                product.quantity = new_quantity;
                true
            }
            None => false,
        }
    }
    
    fn remove_product(&mut self, id: u32) -> bool {
        if let Some(pos) = self.products.iter().position(|p| p.id == id) {
            self.products.remove(pos);
            true
        } else {
            false
        }
    }
    
    fn total_inventory_value(&self) -> f64 {
        self.products.iter().map(|p| p.total_value()).sum()
    }
    
    fn display_all(&self) {
        println!("Inventory:");
        println!("==========");
        for product in &self.products {
            product.display();
        }
        println!("Total inventory value: ${:.2}", self.total_inventory_value());
    }
    
    fn low_stock_products(&self, threshold: u32) -> Vec<&Product> {
        self.products
            .iter()
            .filter(|p| p.quantity < threshold)
            .collect()
    }
}

fn main() {
    let mut inventory = Inventory::new();
    
    // Add some products
    inventory.add_product(Product::new(1, "Laptop".to_string(), 999.99, 10));
    inventory.add_product(Product::new(2, "Mouse".to_string(), 29.99, 50));
    inventory.add_product(Product::new(3, "Keyboard".to_string(), 79.99, 25));
    inventory.add_product(Product::new(4, "Monitor".to_string(), 299.99, 5));
    
    // Display all products
    inventory.display_all();
    
    // Find a product
    match inventory.find_product(2) {
        Some(product) => println!("Found product: {}", product.name),
        None => println!("Product not found"),
    }
    
    // Update quantity
    inventory.update_quantity(4, 2); // Monitor quantity to 2
    println!("\nAfter updating monitor quantity:");
    inventory.display_all();
    
    // Show low stock products (less than 10)
    let low_stock = inventory.low_stock_products(10);
    println!("\nLow stock products (< 10):");
    for product in low_stock {
        product.display();
    }
    
    // Remove a product
    inventory.remove_product(1);
    println!("\nAfter removing laptop:");
    inventory.display_all();
}
```

## 🛠️ Common Pitfalls and Solutions

### 1. Ownership Issues
```rust
// Problem: Trying to use moved values
let s1 = String::from("hello");
let s2 = s1;
// println!("{}", s1); // ERROR: value borrowed after move

// Solution: Use references or clone
let s1 = String::from("hello");
let s2 = s1.clone(); // Explicit clone
println!("{}", s1); // Works!
println!("{}", s2); // Works!
```

### 2. Mutable References
```rust
// Problem: Multiple mutable references
let mut s = String::from("hello");
let r1 = &mut s;
let r2 = &mut s; // ERROR: cannot borrow as mutable more than once

// Solution: Scope the references properly
let mut s = String::from("hello");
{
    let r1 = &mut s;
    r1.push_str(" world");
} // r1 goes out of scope
let r2 = &mut s; // This works now
r2.push_str("!");
```

### 3. Type Inference Issues
```rust
// Problem: Ambiguous type
let numbers = vec![]; // ERROR: type annotation needed

// Solution: Specify the type
let numbers: Vec<i32> = vec![];
// Or provide initial values
let numbers = vec![1, 2, 3]; // Type inferred as Vec<i32>
```

## 📝 Best Practices

### 1. Variable Naming
- Use snake_case for variables and functions
- Use PascalCase for types and structs
- Use descriptive names
- Avoid single-letter variables (except for counters)

### 2. Ownership Management
- Prefer references over cloning when possible
- Use immutable references by default
- Only use mutable references when necessary
- Understand the lifetime of references

### 3. Error Handling
- Use Result for operations that can fail
- Use Option for values that might be absent
- Handle errors gracefully with match or unwrap_or
- Avoid using unwrap() in production code

## 🎯 Key Takeaways

1. **Ownership is unique** - Only one owner at a time
2. **Borrowing is safe** - Compiler prevents data races
3. **Mutability is explicit** - Use `mut` keyword
4. **Type inference is powerful** - But sometimes you need annotations
5. **Pattern matching is expressive** - Use match instead of if-else chains

## 🚀 Next Steps

1. Practice with ownership and borrowing concepts
2. Explore more complex data structures
3. Learn about traits and generics
4. Build small command-line applications
5. Explore error handling in depth

## 📚 Additional Resources

### Documentation
- [The Rust Book: Common Programming Concepts](https://doc.rust-lang.org/book/ch03-00-common-programming-concepts.html)
- [Rust by Example: Variables](https://doc.rust-lang.org/rust-by-example/variables.html)
- [Rust Reference: Ownership](https://doc.rust-lang.org/reference/ownership.html)

### Interactive Practice
- [Rustlings: Variables](https://github.com/rust-lang/rustlings/blob/main/exercises/variables/variables.rs)
- [Exercism Rust Track](https://exercism.org/tracks/rust)
- [Rust Playground](https://play.rust-lang.org/)

---

**Ready to master Rust basics?** Let's move to the next module and learn about control flow! 🚀