# Vectors and HashMaps in Rust

## Overview
Rust's standard library includes several common collection types stored on the heap. This tutorial covers the two most frequently used: **Vectors** and **HashMaps**.

## Key Data Structures
1.  **`Vec<T>`**: A growable array of elements of type `T`. It's the most common collection in Rust and guarantees contiguous memory.
2.  **`HashMap<K, V>`**: Stores a mapping of keys of type `K` to values of type `V`. It uses a hashing function to determine how to store keys and values.

## Key Concepts
- **Heap Allocation**: Both collections store their data on the heap, allowing them to grow at runtime.
- **Safety**: Rust's ownership system ensures that you cannot access elements out of bounds (returning `Option` or panicking) and prevents data races.
- **`Option<T>`**: Methods like `get()` return an `Option`, forcing you to handle the case where a key or index might not exist.

## Basic Syntax
```rust
use std::collections::HashMap;

let mut v = vec![1, 2, 3];
let mut map = HashMap::new();
map.insert("key", 10);
```

[01) Vectors and HashMaps.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/04)%20Data%20Structures/01)%20Vectors%20and%20HashMaps.rs)