# Rust Variables and Mutability

## Overview
This tutorial covers how to declare variables and manage data in Rust. Rust's focus on safety means its default behavior for variables is **immutability** (cannot be changed).

## Key Components
1.  **`let`**: Declares an immutable variable (default).
2.  **`let mut`**: Declares a mutable variable that can be updated.
3.  **`const`**: Declares a global constant (requires explicit type annotation).
4.  **`println!`**: A macro for formatted output to the console.
5.  **Shadowing**: Declaring a new variable with the same name to "hide" the old one.

## Basic Syntax
```rust
fn main() {
    let name = "Alice"; // Immutable
    let mut score = 100; // Mutable
    score += 50;
    println!("{} score: {}", name, score);
}
```

## Shadowing vs. Mutability
- **Mutability**: Change the *value* of the same memory location.
- **Shadowing**: Create a *new* variable with the same name, allowing for a change in *type* or *visibility*.

[01) Variables and Mutability.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/02)%20Fundamentals/01)%20Variables%20and%20Mutability.rs)