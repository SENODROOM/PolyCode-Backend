# Module 04: Structs & Enums

Custom data types are the backbone of Rust programs. Structs group related data; enums represent one of several possible values.

---

## 1. Structs

### Defining & Creating
```rust
struct User {
    username: String,
    email: String,
    age: u32,
    active: bool,
}

let user = User {
    username: String::from("alice"),
    email:    String::from("alice@example.com"),
    age:      30,
    active:   true,
};
println!("{}", user.username);
```

### Update Syntax
```rust
let user2 = User {
    email: String::from("bob@example.com"),
    ..user1  // remaining fields copied from user1
};
```

### Tuple Structs (named tuples)
```rust
struct Color(u8, u8, u8);
struct Point(f64, f64);

let red   = Color(255, 0, 0);
let point = Point(3.0, 4.0);
println!("R={}", red.0);
```

### Methods & Associated Functions
```rust
#[derive(Debug)]
struct Rectangle {
    width:  f64,
    height: f64,
}

impl Rectangle {
    // Associated function (constructor — no self)
    fn new(width: f64, height: f64) -> Self {
        Self { width, height }
    }
    fn square(size: f64) -> Self { Self::new(size, size) }

    // Methods
    fn area(&self)     -> f64  { self.width * self.height }
    fn perimeter(&self)-> f64  { 2.0 * (self.width + self.height) }
    fn is_square(&self)-> bool { self.width == self.height }
    fn scale(&mut self, factor: f64) { self.width *= factor; self.height *= factor; }
}
```

---

## 2. Enums

### Basic Enum
```rust
#[derive(Debug)]
enum Direction { North, South, East, West }

let dir = Direction::North;
```

### Enums with Data (Algebraic Data Types)
```rust
#[derive(Debug)]
enum Message {
    Quit,                       // no data
    Move { x: i32, y: i32 },   // named fields
    Write(String),              // single value
    ChangeColor(u8, u8, u8),   // tuple
}
```

### Methods on Enums
```rust
impl Message {
    fn process(&self) {
        match self {
            Message::Quit               => println!("Quitting"),
            Message::Move { x, y }     => println!("Moving to ({},{})", x, y),
            Message::Write(text)        => println!("Writing: {}", text),
            Message::ChangeColor(r,g,b) => println!("Color: rgb({},{},{})", r, g, b),
        }
    }
}
```

---

## 3. Option<T> — Safe Null

```rust
// Option is defined as:
// enum Option<T> { Some(T), None }

fn find_user(id: u32) -> Option<&'static str> {
    match id {
        1 => Some("Alice"),
        2 => Some("Bob"),
        _ => None,
    }
}

// Pattern matching
match find_user(1) {
    Some(name) => println!("Found: {}", name),
    None       => println!("Not found"),
}

// Concise forms
if let Some(name) = find_user(3) { println!("{}", name); }
let name = find_user(2).unwrap_or("Guest");
let name = find_user(5).map(|n| n.to_uppercase());
```

---

## 4. Derive Macros

```rust
#[derive(Debug, Clone, PartialEq)]
struct Point { x: f64, y: f64 }

let p1 = Point { x: 1.0, y: 2.0 };
let p2 = p1.clone();
println!("{:?}", p1);          // Debug
println!("{}", p1 == p2);      // PartialEq
```

---

## Summary

| Feature | Use |
|---|---|
| `struct` | Group related named fields |
| Tuple struct | Lightweight named tuple |
| `impl` block | Add methods and constructors |
| `enum` | One of several variants (can hold data) |
| `Option<T>` | Value may or may not exist |
| `#[derive]` | Auto-implement common traits |

> 💡 Prefer enums over boolean flags. `Status::Active` is clearer than `is_active: true` and scales to more states without changing the API.
