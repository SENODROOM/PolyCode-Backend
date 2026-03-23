# Module 07: Error Handling

Rust has no exceptions. Instead it uses `Result<T, E>` for recoverable errors and `panic!` for unrecoverable ones — making error handling explicit, composable, and impossible to accidentally ignore.

---

## 1. panic! — Unrecoverable Errors

```rust
panic!("Something went terribly wrong");
let v = vec![1,2,3];
v[10]; // panics: index out of bounds
```

Use `panic!` only for programming errors (bugs), never for user input or network failures.

---

## 2. Result<T, E> — Recoverable Errors

```rust
// Result is defined as:
// enum Result<T, E> { Ok(T), Err(E) }

fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 { Err("Division by zero".to_string()) }
    else        { Ok(a / b) }
}

// Handling Results
match divide(10.0, 2.0) {
    Ok(result) => println!("Result: {}", result),
    Err(e)     => println!("Error: {}", e),
}

// Convenience methods
let result = divide(10.0, 2.0).unwrap();          // panics on Err
let result = divide(10.0, 0.0).unwrap_or(0.0);    // default on Err
let result = divide(10.0, 2.0).expect("Math failed"); // panic with message
let doubled = divide(10.0, 2.0).map(|v| v * 2.0); // transform Ok value
```

---

## 3. The ? Operator — Error Propagation

```rust
use std::fs;
use std::num::ParseIntError;

fn parse_and_double(s: &str) -> Result<i32, ParseIntError> {
    let n: i32 = s.parse()?;  // ? = if Err, return early with that error
    Ok(n * 2)
}

// Chaining with ?
fn read_and_parse(path: &str) -> Result<i32, Box<dyn std::error::Error>> {
    let content = fs::read_to_string(path)?;
    let number: i32 = content.trim().parse()?;
    Ok(number * 2)
}

// main() can return Result
fn main() -> Result<(), Box<dyn std::error::Error>> {
    let n = parse_and_double("42")?;
    println!("{}", n);
    Ok(())
}
```

---

## 4. Custom Error Types

```rust
use std::fmt;

#[derive(Debug)]
enum AppError {
    NotFound(String),
    InvalidInput { field: String, message: String },
    IoError(std::io::Error),
    ParseError(String),
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            AppError::NotFound(id)  => write!(f, "Not found: {}", id),
            AppError::InvalidInput { field, message } =>
                write!(f, "Invalid {}: {}", field, message),
            AppError::IoError(e)    => write!(f, "I/O error: {}", e),
            AppError::ParseError(s) => write!(f, "Parse error: {}", s),
        }
    }
}

impl std::error::Error for AppError {}

// Auto-convert from std errors using From
impl From<std::io::Error> for AppError {
    fn from(e: std::io::Error) -> Self { AppError::IoError(e) }
}
```

---

## 5. Option vs Result

```rust
// Option<T> — value may be absent (no error context)
fn find_user(id: u32) -> Option<User> { ... }

// Result<T, E> — operation may fail with an error
fn fetch_user(id: u32) -> Result<User, DbError> { ... }

// Convert between them
let opt: Option<i32> = Some(5);
let res: Result<i32, &str> = opt.ok_or("was None");

let res: Result<i32, &str> = Ok(5);
let opt: Option<i32> = res.ok();
```

---

## Error Handling Best Practices

| Situation | Use |
|---|---|
| Programming bug | `panic!` / `unwrap` |
| Expected failure | `Result<T, E>` |
| Value might be absent | `Option<T>` |
| Multiple error types | `Box<dyn Error>` or custom enum |
| Library code | Custom error type |
| Application code | `anyhow` crate (simpler) |

> 💡 Never use `.unwrap()` in library code or on user-provided data. Reserve it for tests or situations where you've verified the value is present through other means.
