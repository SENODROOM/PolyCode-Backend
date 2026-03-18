# Module 01: Getting Started with Rust

## What is Rust?

Rust is a systems programming language focused on three goals:
- **Safety** — eliminates memory bugs at compile time (no null pointers, no dangling references, no data races)
- **Speed** — zero-cost abstractions, no garbage collector, performance comparable to C/C++
- **Concurrency** — fearless concurrency enforced by the compiler

Rust is used at Mozilla, Microsoft, Google, Amazon, Meta, and the Linux kernel.

---

## Installation

### Linux / macOS
```bash
curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh
source $HOME/.cargo/env
```

### Windows
Download and run `rustup-init.exe` from https://rustup.rs

### Verify
```bash
rustc --version   # rustc 1.77.0 (or newer)
cargo --version   # cargo 1.77.0
```

---

## Cargo — Rust's Build System & Package Manager

Cargo does everything: build, test, run, manage dependencies.

```bash
cargo new hello_world      # Create new project
cargo new --lib my_lib     # Create a library crate
cargo build                # Compile (debug)
cargo build --release      # Compile (optimized)
cargo run                  # Build + run
cargo test                 # Run all tests
cargo check                # Fast type-check (no binary)
cargo doc --open           # Generate & open docs
cargo add serde            # Add a dependency
cargo update               # Update dependencies
```

### Project Structure
```
my_project/
├── Cargo.toml       ← manifest (dependencies, metadata)
├── Cargo.lock       ← exact dependency versions (commit this for apps)
└── src/
    └── main.rs      ← entry point (fn main)
```

### Cargo.toml Example
```toml
[package]
name    = "my_project"
version = "0.1.0"
edition = "2021"

[dependencies]
serde       = { version = "1.0", features = ["derive"] }
serde_json  = "1.0"
tokio       = { version = "1", features = ["full"] }
```

---

## Hello World

```rust
fn main() {
    println!("Hello, World!");
}
```

Run it:
```bash
cargo run
```

### println! Formatting
```rust
// Basic
println!("Hello, {}!", "Rust");

// Multiple values
println!("{} + {} = {}", 2, 3, 2 + 3);

// Named arguments
println!("{name} is {age}", name="Alice", age=30);

// Debug print (for any type implementing Debug)
println!("{:?}", vec![1, 2, 3]);
println!("{:#?}", vec![1, 2, 3]);  // pretty-printed

// Width & padding
println!("{:>10}", "right");    // right-align in 10 chars
println!("{:<10}", "left");     // left-align
println!("{:^10}", "center");   // centered
println!("{:0>5}", 42);         // zero-padded: 00042

// Floats
println!("{:.2}", 3.14159);     // 3.14
println!("{:8.3}", 3.14159);    // "   3.142"
```

---

## Comments & Documentation

```rust
// Single-line comment

/* Multi-line
   comment */

/// Documentation comment — appears in cargo doc
/// # Examples
/// ```
/// let x = my_function(5);
/// assert_eq!(x, 10);
/// ```
fn my_function(n: i32) -> i32 { n * 2 }

//! Module-level documentation (at top of file)
```

---

## The Rust Compiler is Your Friend

Unlike other languages, Rust's compiler gives you **detailed, actionable error messages**.

```
error[E0502]: cannot borrow `s` as mutable because it is also borrowed as immutable
  --> src/main.rs:6:14
   |
5  |     let r1 = &s;
   |              -- immutable borrow occurs here
6  |     let r2 = &mut s;
   |              ^^^^^^ mutable borrow occurs here
```

Always read the full error — it usually tells you exactly what to do.

---

## Key Differences from Other Languages

| Concept | Other Languages | Rust |
|---|---|---|
| Memory | GC or manual | Ownership system |
| Null | `null` / `nil` | `Option<T>` |
| Exceptions | `try/catch` | `Result<T, E>` |
| Inheritance | Classes inherit | Traits compose |
| Threads | May have data races | Compiler prevents data races |

> 💡 **Mindset shift**: In Rust, fighting the compiler early means fewer bugs in production. The compiler is your pair programmer catching bugs before you run a single line.
