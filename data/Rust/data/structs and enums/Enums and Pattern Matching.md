# Rust Enums & Pattern Matching

Rust's enums are algebraic data types — far more powerful than enums in C++ or Java. Combined with `match`, they eliminate entire categories of bugs.

---

## 1. Basic Enums

```rust
enum Direction { North, South, East, West }

let dir = Direction::North;
match dir {
    Direction::North => println!("Going north"),
    Direction::South => println!("Going south"),
    Direction::East  => println!("Going east"),
    Direction::West  => println!("Going west"),
}
```

---

## 2. Enums with Data

```rust
enum Shape {
    Circle(f64),               // radius
    Rectangle(f64, f64),       // width, height
    Triangle { base: f64, height: f64 },  // named fields
}

impl Shape {
    fn area(&self) -> f64 {
        match self {
            Shape::Circle(r)          => std::f64::consts::PI * r * r,
            Shape::Rectangle(w, h)    => w * h,
            Shape::Triangle { base: b, height: h } => 0.5 * b * h,
        }
    }
    fn name(&self) -> &str {
        match self {
            Shape::Circle(_)    => "Circle",
            Shape::Rectangle(..)=> "Rectangle",
            Shape::Triangle {..}=> "Triangle",
        }
    }
}
```

---

## 3. Option<T> — Safe Null Handling

```rust
// Never use null — use Option
fn find_user(id: u32) -> Option<String> {
    if id == 1 { Some("Alice".to_string()) } else { None }
}

// Pattern matching
match find_user(1) {
    Some(name) => println!("Found: {}", name),
    None       => println!("Not found"),
}

// Convenience methods
let name = find_user(1).unwrap_or("Guest".to_string());
let upper = find_user(1).map(|n| n.to_uppercase());
let len   = find_user(1).map(|n| n.len()).unwrap_or(0);

// if let — match one arm
if let Some(user) = find_user(2) {
    println!("User: {}", user);
} else {
    println!("No user found");
}
```

---

## 4. Result<T, E> — Safe Error Handling

```rust
#[derive(Debug)]
enum AppError { NotFound(String), ParseError(String), NetworkError }

fn parse_age(s: &str) -> Result<u32, AppError> {
    s.parse::<u32>().map_err(|e| AppError::ParseError(e.to_string()))
}

// ? operator — propagate errors automatically
fn process(input: &str) -> Result<String, AppError> {
    let age = parse_age(input)?;
    Ok(format!("Age: {}", age))
}

// Chaining Results
let result = parse_age("25")
    .map(|age| age * 2)
    .map_err(|e| format!("Error: {:?}", e));
```

---

## 5. Advanced Pattern Matching

```rust
let num = 7;

// Match guards
match num {
    n if n < 0  => println!("Negative: {}", n),
    0           => println!("Zero"),
    n if n < 10 => println!("Single digit: {}", n),
    n           => println!("Large: {}", n),
}

// Ranges in match
match num {
    1..=5   => println!("One to five"),
    6..=10  => println!("Six to ten"),
    _       => println!("Something else"),
}

// Destructuring tuples
let point = (3, -2);
match point {
    (0, 0)     => println!("Origin"),
    (x, 0)     => println!("On x-axis at {}", x),
    (0, y)     => println!("On y-axis at {}", y),
    (x, y)     => println!("At ({}, {})", x, y),
}

// @ bindings — bind AND test
match num {
    n @ 1..=9 => println!("Single digit: {}", n),
    n @ 10..=99 => println!("Double digit: {}", n),
    n => println!("Three+ digits: {}", n),
}

// Multiple patterns
match num {
    1 | 2 | 3 => println!("One, two, or three"),
    _ => println!("Other"),
}
```

---

## 6. The `matches!` Macro

```rust
let opt: Option<i32> = Some(5);
let is_some = matches!(opt, Some(_));        // true
let is_big  = matches!(opt, Some(n) if n > 3); // true
```

---

## Summary

| Pattern | Use Case |
|---|---|
| `match` | Exhaustive branching |
| `if let` | Match one variant |
| `while let` | Loop while pattern matches |
| `Option<T>` | Values that may be absent |
| `Result<T,E>` | Operations that may fail |
| `?` operator | Propagate errors up |

> 💡 `match` is **exhaustive** in Rust — the compiler forces you to handle every case. This is why Rust programs have far fewer runtime panics than equivalent code in other languages.
