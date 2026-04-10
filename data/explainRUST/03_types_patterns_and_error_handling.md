## Types & Data Structures

```rust
// Tuple — fixed size, different types
let tup: (i32, f64, char) = (500, 6.4, 'Z');
let (x, y, z) = tup;   // destructure
let first = tup.0;     // index access

// Array — fixed size, same type (stack-allocated)
let arr: [i32; 5] = [1, 2, 3, 4, 5];
let zeros = [0; 100];  // 100 zeros

// Vec<T> — dynamic array (heap-allocated)
let mut v: Vec<i32> = Vec::new();
v.push(1);
v.push(2);
let v2 = vec![1, 2, 3];  // macro shorthand
let third = &v2[2];      // panics if out of bounds
let third = v2.get(2);   // returns Option<&i32> (safe)

// HashMap
use std::collections::HashMap;
let mut scores: HashMap<String, i32> = HashMap::new();
scores.insert(String::from("Alice"), 100);
scores.insert(String::from("Bob"), 85);
let alice = scores.get("Alice");  // Option<&i32>

// Struct
struct Point {
    x: f64,
    y: f64,
}

struct Rectangle {
    origin: Point,
    width: f64,
    height: f64,
}

impl Rectangle {
    // Associated function (constructor)
    fn new(x: f64, y: f64, w: f64, h: f64) -> Self {
        Rectangle {
            origin: Point { x, y },
            width: w,
            height: h,
        }
    }

    fn area(&self) -> f64 {
        self.width * self.height
    }

    fn is_square(&self) -> bool {
        (self.width - self.height).abs() < f64::EPSILON
    }
}

// Enum — Rust's enums are algebraic data types (powerful!)
enum Shape {
    Circle(f64),                        // tuple variant
    Rectangle { width: f64, height: f64 }, // struct variant
    Triangle(f64, f64, f64),            // three sides
    Point,                              // unit variant
}

impl Shape {
    fn area(&self) -> f64 {
        match self {
            Shape::Circle(r) => std::f64::consts::PI * r * r,
            Shape::Rectangle { width, height } => width * height,
            Shape::Triangle(a, b, c) => {
                let s = (a + b + c) / 2.0;
                (s * (s-a) * (s-b) * (s-c)).sqrt()  // Heron's formula
            }
            Shape::Point => 0.0,
        }
    }
}
```

---

## Pattern Matching

Rust's `match` is exhaustive — the compiler forces you to handle every case.

```rust
// Match on value
let num = 7;
let desc = match num {
    1         => "one",
    2 | 3     => "two or three",
    4..=6     => "four to six",
    7..=9     => "seven to nine",
    _         => "something else",  // wildcard (mandatory if not exhaustive)
};

// Match on enum with data
let shape = Shape::Circle(5.0);
match shape {
    Shape::Circle(r) if r > 10.0 => println!("Big circle: {r}"),
    Shape::Circle(r)              => println!("Circle with radius {r}"),
    Shape::Rectangle { width, height } => {
        println!("{width} × {height} rectangle")
    }
    _ => println!("Other shape"),
}

// Option<T> — Rust's replacement for null
let some_value: Option<i32> = Some(42);
let no_value:   Option<i32> = None;

match some_value {
    Some(n) => println!("Got: {n}"),
    None    => println!("Nothing"),
}

// Shorthand with if let
if let Some(n) = some_value {
    println!("Got: {n}");
}

// while let
let mut stack = vec![1, 2, 3];
while let Some(top) = stack.pop() {
    println!("{top}");
}

// Destructuring in match
let point = (3, -7);
match point {
    (0, 0)  => println!("Origin"),
    (x, 0)  => println!("On x-axis at {x}"),
    (0, y)  => println!("On y-axis at {y}"),
    (x, y)  => println!("At ({x}, {y})"),
}
```

---

## Error Handling

Rust has no exceptions. Errors are values.

```rust
// Result<T, E> — either Ok(value) or Err(error)
use std::fs::File;
use std::io::{self, Read};

fn read_file(path: &str) -> Result<String, io::Error> {
    let mut file = File::open(path)?;  // ? propagates error up
    let mut contents = String::new();
    file.read_to_string(&mut contents)?;
    Ok(contents)
}

// The ? operator desugars to:
// match result {
//     Ok(val) => val,
//     Err(e)  => return Err(e.into()),
// }

// Custom error types with thiserror crate
use thiserror::Error;

#[derive(Error, Debug)]
enum AppError {
    #[error("IO error: {0}")]
    Io(#[from] io::Error),

    #[error("Parse error: expected number, got '{0}'")]
    Parse(String),

    #[error("Not found: {resource} with id {id}")]
    NotFound { resource: String, id: u64 },
}

// Panic — for unrecoverable errors
fn divide(a: f64, b: f64) -> f64 {
    if b == 0.0 {
        panic!("Division by zero!");  // crashes the program
    }
    a / b
}

// unwrap/expect — panic if None/Err (use sparingly)
let val = some_option.unwrap();                  // panics with generic message
let val = some_option.expect("should have val"); // panics with your message

// Graceful alternatives
let val = some_option.unwrap_or(0);              // default value
let val = some_option.unwrap_or_else(|| compute_default());
let val = some_option.map(|n| n * 2);            // transform if Some
```

---

