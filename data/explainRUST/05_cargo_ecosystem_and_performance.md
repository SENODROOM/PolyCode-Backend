## Cargo & Ecosystem

```
Cargo — Rust's build system and package manager
──────────────────────────────────────────────────
cargo new my_project        Create new project
cargo build                 Compile (debug mode)
cargo build --release       Compile with optimizations
cargo run                   Build and run
cargo test                  Run tests
cargo doc --open            Generate and open docs
cargo check                 Type-check without building
cargo clippy                Lint your code
cargo fmt                   Format code (rustfmt)
cargo add tokio             Add dependency
──────────────────────────────────────────────────

Cargo.toml structure:
[package]
name = "my_app"
version = "0.1.0"
edition = "2021"

[dependencies]
serde = { version = "1.0", features = ["derive"] }
tokio = { version = "1", features = ["full"] }
reqwest = "0.11"
anyhow = "1.0"
```

**Popular Crates (Libraries):**

| Crate | Purpose |
|-------|---------|
| `serde` | Serialization/deserialization (JSON, etc.) |
| `tokio` | Async runtime |
| `reqwest` | HTTP client |
| `axum` | Web framework |
| `sqlx` | Async database queries |
| `clap` | CLI argument parsing |
| `rayon` | Data parallelism |
| `regex` | Regular expressions |
| `anyhow` / `thiserror` | Error handling |

---

## Performance

```
Benchmark: Binary search on 10M elements (approximate)
──────────────────────────────────────────────────────
Language    Time      Memory
──────────────────────────────────────────────────────
C           1.0×      1×
Rust        1.0–1.1×  1×      ← matches C!
C++         1.0–1.2×  1×
Go          1.5–2×    2–3×
Java        2–4×      5–10×   (JVM warmup)
Python      50–100×   10–20×
──────────────────────────────────────────────────────
Rust achieves C-level performance with memory safety guarantees
```

**Why Rust is fast:**
- Zero-cost abstractions (iterators, generics compile away)
- No garbage collector pauses
- LLVM backend with aggressive optimizations
- Stack allocation by default
- Cache-friendly data layouts
- Inlining, monomorphization of generics

---

## Quick Reference

```rust
// --- Syntax Cheatsheet ---

// Variables
let x: i32 = 5;
let mut y = 10;

// Function
fn add(a: i32, b: i32) -> i32 { a + b }

// If expression (returns value!)
let max = if a > b { a } else { b };

// Loop
loop { break value; }      // returns value from loop!
for i in 0..10 { }        // exclusive range
for i in 0..=10 { }       // inclusive range

// Closures
let add = |a, b| a + b;
let doubled: Vec<_> = v.iter().map(|x| x * 2).collect();

// Struct update syntax
let p2 = Point { x: 1.0, ..p1 };

// Slice
let slice: &[i32] = &arr[1..4];

// String vs &str
let owned: String = String::from("hello");
let borrowed: &str = "world";       // string literal
let slice: &str = &owned[0..3];     // "hel"
```

---

*Rust: because you deserve a language that's simultaneously the fastest, safest, and most enjoyable to write — once you wrestle the borrow checker into friendship. 🦀*
