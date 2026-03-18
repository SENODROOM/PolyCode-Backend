# Structs and Methods in Rust

## Overview
Rust doesn't have "classes" in the traditional sense. Instead, it uses **Structs** to hold data and **`impl` blocks** to define methods that operate on that data. This approach achieves the goals of encapsulation and organization while maintaining Rust's unique safety guarantees.

## Key Concepts
1.  **`struct`**: A custom data type that lets you name and package together multiple related values.
2.  **`impl` (Implementation)**: A block where you define functions associated with a struct.
3.  **Methods**: Functions defined within an `impl` block that take `&self`, `&mut self`, or `self` as their first parameter.
4.  **Associated Functions**: Functions defined within an `impl` block that *do not* take `self` as a parameter (often used as constructors, e.g., `String::from()`).
5.  **`self`**: Refers to the instance of the struct the method is being called on.

## Basic Syntax
```rust
struct MyStruct {
    field: i32,
}

impl MyStruct {
    fn my_method(&self) {
        println!("{}", self.field);
    }
}
```

[01) Structs and Methods.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/03)%20Object%20Oriented%20Programming/01)%20Structs%20and%20Methods.rs)