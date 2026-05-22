# Traits and Generics in Rust

## Overview
**Traits** and **Generics** are the core features that allow for polymorphism and code reuse in Rust. While Rust doesn't have classes and inheritance, it uses traits to define shared behavior across different types and generics to write code that works with any type that meets certain criteria.

## Key Concepts
1.  **Traits**:
    - **Shared Behavior**: Define a set of methods that multiple types can implement.
    - **Default Implementation**: You can provide a default implementation for methods in a trait.
    - **Trait Bounds**: Restrict generic types to only those that implement a specific trait (e.g., `<T: Summary>`).
2.  **Generics**:
    - **Generic Types**: Write functions, structs, or enums that can work with any data type (e.g., `Vec<T>`).
    - **Performance**: Rust's generics are as efficient as hand-written code for each specific type (monomorphization).
3.  **Static vs. Dynamic Dispatch**:
    - **Static Dispatch**: The compiler generates specialized code for each type used (faster, uses generics).
    - **Dynamic Dispatch**: The compiler uses trait objects (e.g., `&dyn Summary`) to call methods at runtime (more flexible, slightly slower).

## Basic Syntax
```rust
// Trait
trait Summary {
    fn summarize(&self) -> String;
}

// Generic Function with Trait Bound
fn notify<T: Summary>(item: &T) {
    println!("{}", item.summarize());
}

// Struct implementation
impl Summary for NewsArticle {
    fn summarize(&self) -> String { /* ... */ }
}
```

## Best Practices
- Use traits to define common functionality that applies to different types.
- Prefer static dispatch (generics) by default for better performance.
- Use `impl Trait` for concise return types or function arguments when you only care about the trait, not the specific type.

[03) Traits and Generics.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/03)%20Object%20Oriented%20Programming/03)%20Traits%20and%20Generics.rs)