# Error Handling in Rust

## Overview
Rust classifies errors into two categories: **recoverable** and **unrecoverable**. This tutorial focuses on recoverable errors using the `Result` type, which is a hallmark of Rust's safety and reliability.

## Key Concepts
1.  **`panic!`**: Used for unrecoverable errors. It stops program execution immediately.
2.  **`Result<T, E>`**: An enum used for functions that can fail.
    - `Ok(T)`: The operation succeeded, and `T` is the returned value.
    - `Err(E)`: The operation failed, and `E` is the error information.
3.  **Pattern Matching (`match`)**: The primary way to handle the different variants of a `Result`.
4.  **`unwrap()` and `expect()`**: Quick ways to get the value from an `Ok`, but will `panic!` if the result is an `Err`. Use sparingly.
5.  **The `?` Operator**: A concise way to propagate errors up the call stack.

## Basic Syntax
```rust
fn function_that_fails() -> Result<i32, String> {
    if true { Ok(42) } else { Err("Fail".into()) }
}

fn main() {
    match function_that_fails() {
        Ok(v) => println!("Value: {}", v),
        Err(e) => println!("Error: {}", e),
    }
}
```

[02) Error Handling.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/10)%20Advanced%20Topics/02)%20Error%20Handling.rs)