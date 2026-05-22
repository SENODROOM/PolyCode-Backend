# Module 1: Introduction to Rust

## 🎯 Learning Objectives

By the end of this module, you will:
- Understand what Rust is and its key features
- Set up a Rust development environment
- Write and run your first Rust program
- Understand Rust's ownership system basics
- Learn about Rust's safety guarantees

## 📚 Topics Covered

### 1. What is Rust?
- Systems programming language
- Created by Graydon Hoare at Mozilla
- First released in 2010, stable version 1.0 in 2015
- Focus on safety, speed, and concurrency
- Sponsored by the Rust Foundation

### 2. Key Features
- **Memory Safety**: No garbage collector, ownership system
- **Performance**: Comparable to C/C++
- **Concurrency**: Fearless concurrency with ownership
- **Tooling**: Excellent tooling with Cargo
- **Cross-platform**: Compile to many targets
- **WebAssembly**: Compile to Wasm for web

### 3. Use Cases
- **Systems Programming**: Operating systems, embedded systems
- **Web Development**: Web servers, APIs (with frameworks)
- **CLI Tools**: Command-line applications
- **Game Development**: Game engines and games
- **Blockchain**: Cryptocurrency and smart contracts
- **WebAssembly**: Browser-based applications

### 4. Setting Up Development Environment
- Installing Rust with rustup
- Using Cargo as package manager
- IDE setup (VS Code, IntelliJ IDEA)
- Online playgrounds (Rust Playground)

## 💻 Practical Examples

### Example 1: Your First Rust Program

```rust
// Hello World in Rust
fn main() {
    println!("Hello, World!");
    
    // Variables and basic operations
    let message = "Welcome to Rust!";
    println!("{}", message);
    
    let x = 10;
    let y = 5;
    println!("Sum: {}", x + y);
    println!("Product: {}", x * y);
}
```

### Example 2: Basic Data Types

```rust
fn main() {
    // Integers
    let small_number: i8 = 127;
    let normal_number: i32 = 1_000_000;
    let big_number: i64 = 9_223_372_036_854_775_807;
    let unsigned_number: u32 = 4_000_000_000;
    
    // Floating point
    let pi: f64 = 3.14159;
    let e: f32 = 2.718;
    
    // Boolean
    let is_rust_awesome: bool = true;
    
    // Character
    let letter: char = 'R';
    let emoji: char = '🦀';
    
    // Arrays (fixed size)
    let numbers: [i32; 5] = [1, 2, 3, 4, 5];
    let first = numbers[0];
    
    // Tuples
    let person: (String, i32, bool) = ("Alice".to_string(), 30, true);
    let (name, age, is_student) = person;
    
    println!("Name: {}, Age: {}, Student: {}", name, age, is_student);
}
```

### Example 3: Functions and Basic Control Flow

```rust
fn greet(name: &str) -> String {
    format!("Hello, {}!", name)
}

fn add_numbers(a: i32, b: i32) -> i32 {
    a + b
}

fn is_even(number: i32) -> bool {
    number % 2 == 0
}

fn main() {
    // Function calls
    let greeting = greet("Rustacean");
    println!("{}", greeting);
    
    let sum = add_numbers(10, 5);
    println!("Sum: {}", sum);
    
    let number = 7;
    if is_even(number) {
        println!("{} is even", number);
    } else {
        println!("{} is odd", number);
    }
    
    // Loop
    for i in 1..=5 {
        println!("Count: {}", i);
    }
}
```

### Example 4: Ownership Basics

```rust
fn main() {
    // String ownership
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved to s2
    
    // println!("{}", s1); // This would cause an error!
    println!("{}", s2); // This works
    
    // Clone to create a copy
    let s3 = String::from("world");
    let s4 = s3.clone(); // Explicit clone
    
    println!("{}", s3); // This works
    println!("{}", s4); // This also works
    
    // Function and ownership
    let message = String::from("Rust is awesome!");
    print_message(&message); // Pass reference
    println!("{}", message); // Still works because we passed a reference
}

fn print_message(msg: &String) {
    println!("Message: {}", msg);
}
```

### Example 5: Structs and Methods

```rust
struct User {
    username: String,
    email: String,
    age: u32,
    active: bool,
}

impl User {
    fn new(username: String, email: String, age: u32) -> User {
        User {
            username,
            email,
            age,
            active: true,
        }
    }
    
    fn is_adult(&self) -> bool {
        self.age >= 18
    }
    
    fn deactivate(&mut self) {
        self.active = false;
    }
    
    fn display(&self) {
        println!("User: {} ({})", self.username, self.email);
        println!("Age: {}, Active: {}", self.age, self.active);
    }
}

fn main() {
    let mut user = User::new(
        "rustacean".to_string(),
        "rust@example.com".to_string(),
        25,
    );
    
    user.display();
    
    if user.is_adult() {
        println!("User is an adult");
    }
    
    user.deactivate();
    user.display();
}
```

## 🎮 Interactive Exercises

### Exercise 1: Basic Variables
Create variables for:
- Your name (String)
- Your age (u32)
- Your height in centimeters (f64)
- Whether you like Rust (bool)

Then display them using println!.

```rust
// Your solution here
```

### Exercise 2: Simple Functions
Create functions for:
- Adding two numbers
- Checking if a number is positive
- Converting Celsius to Fahrenheit

```rust
// Your solution here
```

### Exercise 3: Struct Practice
Create a `Book` struct with:
- title (String)
- author (String)
- pages (u32)
- available (bool)

Add methods to:
- Display book information
- Borrow/unborrow the book

```rust
// Your solution here
```

## 🌐 Real-World Applications

### 1. Command-Line Calculator

```rust
use std::io;

fn main() {
    println!("Simple Rust Calculator");
    
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
        
        let result = match operation {
            "add" => num1 + num2,
            "subtract" => num1 - num2,
            "multiply" => num1 * num2,
            "divide" => num1 / num2,
            _ => {
                println!("Unknown operation!");
                continue;
            }
        };
        
        println!("Result: {}", result);
    }
}
```

### 2. Simple Database

```rust
use std::collections::HashMap;

struct Database {
    users: HashMap<String, User>,
}

#[derive(Debug, Clone)]
struct User {
    name: String,
    email: String,
    age: u32,
}

impl Database {
    fn new() -> Database {
        Database {
            users: HashMap::new(),
        }
    }
    
    fn add_user(&mut self, username: String, user: User) {
        self.users.insert(username, user);
    }
    
    fn get_user(&self, username: &str) -> Option<&User> {
        self.users.get(username)
    }
    
    fn list_users(&self) {
        println!("Database Users:");
        for (username, user) in &self.users {
            println!("{}: {} ({})", username, user.name, user.email);
        }
    }
}

fn main() {
    let mut db = Database::new();
    
    // Add some users
    db.add_user(
        "alice".to_string(),
        User {
            name: "Alice Johnson".to_string(),
            email: "alice@example.com".to_string(),
            age: 28,
        },
    );
    
    db.add_user(
        "bob".to_string(),
        User {
            name: "Bob Smith".to_string(),
            email: "bob@example.com".to_string(),
            age: 32,
        },
    );
    
    // List all users
    db.list_users();
    
    // Get a specific user
    if let Some(user) = db.get_user("alice") {
        println!("Found user: {:?}", user);
    }
    
    // Try to get a non-existent user
    match db.get_user("charlie") {
        Some(user) => println!("Found user: {:?}", user),
        None => println!("User 'charlie' not found"),
    }
}
```

### 3. File Processor

```rust
use std::fs;
use std::io::{self, Write};

struct FileProcessor {
    content: String,
}

impl FileProcessor {
    fn new(content: String) -> FileProcessor {
        FileProcessor { content }
    }
    
    fn word_count(&self) -> usize {
        self.content.split_whitespace().count()
    }
    
    fn line_count(&self) -> usize {
        self.content.lines().count()
    }
    
    fn char_count(&self) -> usize {
        self.content.chars().count()
    }
    
    fn find_word(&self, word: &str) -> usize {
        self.content
            .split_whitespace()
            .filter(|w| *w == word)
            .count()
    }
    
    fn to_uppercase(&self) -> String {
        self.content.to_uppercase()
    }
    
    fn save_to_file(&self, filename: &str) -> io::Result<()> {
        let mut file = fs::File::create(filename)?;
        file.write_all(self.content.as_bytes())?;
        Ok(())
    }
}

fn main() {
    let sample_text = "Hello, World!\nThis is a sample text file.\nIt contains multiple lines and words.\nHello again!";
    
    let processor = FileProcessor::new(sample_text.to_string());
    
    println!("Text Analysis:");
    println!("Word count: {}", processor.word_count());
    println!("Line count: {}", processor.line_count());
    println!("Character count: {}", processor.char_count());
    println!("Occurrences of 'Hello': {}", processor.find_word("Hello"));
    
    println!("\nUppercase version:");
    println!("{}", processor.to_uppercase());
    
    // Save to file
    match processor.save_to_file("output.txt") {
        Ok(_) => println!("File saved successfully!"),
        Err(e) => println!("Error saving file: {}", e),
    }
}
```

## 🛠️ Development Tools

### Rust Toolchain
- **rustup**: Rust version manager and installer
- **cargo**: Build system and package manager
- **rustc**: Rust compiler
- **rustdoc**: Documentation generator

### IDE Support
- **VS Code**: rust-analyzer extension
- **IntelliJ IDEA**: Rust plugin
- **Sublime Text**: Rust Enhanced package
- **Vim/Neovim**: rust.vim plugin

### Essential Commands
```bash
# Install Rust
curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh

# Create new project
cargo new hello_world
cargo new --lib my_library

# Build project
cargo build
cargo build --release

# Run project
cargo run

# Run tests
cargo test

# Generate documentation
cargo doc --open

# Check for errors without building
cargo check

# Format code
cargo fmt

# Lint code
cargo clippy
```

## 📝 Best Practices

### 1. Code Style
- Use rustfmt for consistent formatting
- Follow naming conventions (snake_case for variables, PascalCase for types)
- Use meaningful variable and function names
- Add comments for complex logic

### 2. Error Handling
- Use Result for operations that can fail
- Use Option for values that might be absent
- Handle errors gracefully with match or unwrap_or
- Avoid using unwrap() in production code

### 3. Memory Safety
- Understand ownership rules
- Use references (&) to borrow without taking ownership
- Use clone() only when necessary
- Leverage the compiler for safety

## 🎯 Key Takeaways

1. **Rust prioritizes safety** - No null pointer exceptions, no data races
2. **Ownership system** - Unique approach to memory management
3. **Excellent tooling** - Cargo, rustfmt, clippy
4. **Performance** - Zero-cost abstractions
5. **Growing ecosystem** - WebAssembly, embedded systems, web development

## 🚀 Next Steps

1. Practice basic Rust syntax and concepts
2. Explore Cargo and project structure
3. Learn about ownership and borrowing in depth
4. Build small command-line applications
5. Explore popular Rust crates and frameworks

## 📚 Additional Resources

### Documentation
- [The Rust Book](https://doc.rust-lang.org/book/)
- [Rust by Example](https://doc.rust-lang.org/rust-by-example/)
- [Rust Reference](https://doc.rust-lang.org/reference/)

### Interactive Learning
- [Rustlings](https://github.com/rust-lang/rustlings/)
- [Exercism Rust Track](https://exercism.org/tracks/rust)
- [Rust Playground](https://play.rust-lang.org/)

### Books
- "The Rust Programming Language" by Steve Klabnik and Carol Nichols
- "Programming Rust" by Jim Blandy and Jason Orendorff
- "Rust in Action" by Tim McNamara

---

**Ready to master Rust?** Let's move to the next module and learn about Rust basics! 🚀