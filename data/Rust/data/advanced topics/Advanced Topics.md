# Module 13: Advanced Topics

Lifetimes in detail, macros, smart pointers, and a peek at unsafe — the tools that make Rust truly powerful.

---

## 1. Lifetimes — Deep Dive

```rust
// Lifetime elision — Rust infers these common cases automatically
fn first_word(s: &str) -> &str { ... }        // same lifetime inferred
fn longest<'a>(x: &'a str, y: &'a str) -> &'a str { ... } // explicit

// Structs holding references need lifetime annotations
struct Parser<'a> {
    input: &'a str,
    position: usize,
}

impl<'a> Parser<'a> {
    fn new(input: &'a str) -> Self { Self { input, position: 0 } }
    fn current(&self) -> Option<char> { self.input.chars().nth(self.position) }
}

// 'static — lives for the entire program duration
fn get_greeting() -> &'static str { "Hello, World!" }
let s: &'static str = "I live forever";
```

---

## 2. Smart Pointers

### Box<T> — Heap Allocation
```rust
// Force heap allocation (recursive types, large data)
let boxed = Box::new(5);
println!("{}", *boxed);  // auto-deref

// Recursive type — needs Box to have known size
enum List {
    Cons(i32, Box<List>),
    Nil,
}
let list = List::Cons(1, Box::new(List::Cons(2, Box::new(List::Nil))));
```

### Rc<T> — Reference Counting (Single Thread)
```rust
use std::rc::Rc;

let shared = Rc::new(vec![1, 2, 3]);
let clone1 = Rc::clone(&shared);
let clone2 = Rc::clone(&shared);
println!("Ref count: {}", Rc::strong_count(&shared)); // 3
// All three point to the same data — freed when count hits 0
```

### RefCell<T> — Interior Mutability
```rust
use std::cell::RefCell;

let data = RefCell::new(vec![1, 2, 3]);
data.borrow_mut().push(4);  // runtime-checked mutable borrow
println!("{:?}", data.borrow()); // runtime-checked immutable borrow
```

---

## 3. Macros

### Declarative Macros (macro_rules!)
```rust
macro_rules! say {
    ($msg:expr) => { println!(">> {}", $msg); };
    ($fmt:expr, $($arg:tt)*) => { println!(">> {}", format!($fmt, $($arg)*)); };
}

say!("Hello");
say!("Hello, {}!", "world");

// Create a map in one line
macro_rules! map {
    ($($k:expr => $v:expr),*) => {{
        let mut m = std::collections::HashMap::new();
        $(m.insert($k, $v);)*
        m
    }};
}
let m = map!["a" => 1, "b" => 2, "c" => 3];
```

### Derive Macros
```rust
#[derive(Debug, Clone, PartialEq, Eq, Hash, PartialOrd, Ord)]
struct Point { x: i32, y: i32 }
```

---

## 4. Iterators — Advanced Patterns

```rust
// Custom iterator with state
struct Primes { current: u64 }

impl Iterator for Primes {
    type Item = u64;
    fn next(&mut self) -> Option<u64> {
        self.current += 1;
        while !is_prime(self.current) { self.current += 1; }
        Some(self.current)
    }
}

// Lazy evaluation — no computation until consumed
let first_10_primes: Vec<u64> = Primes { current: 1 }.take(10).collect();

// scan — rolling state
let running_avg: Vec<f64> = data.iter().enumerate()
    .scan(0.0, |sum, (i, &x)| { *sum += x; Some(*sum / (i + 1) as f64) })
    .collect();
```

---

## 5. Unsafe Rust

```rust
// unsafe is an escape hatch — use sparingly and with great care
unsafe {
    // Raw pointers (no borrowing rules)
    let x = 5;
    let raw = &x as *const i32;
    println!("{}", *raw);  // dereferencing raw pointer

    // Call unsafe functions (FFI, low-level OS)
    libc::memcpy(dst, src, n);
}

// The 5 things you can only do in unsafe:
// 1. Dereference raw pointers
// 2. Call unsafe functions or methods
// 3. Access/modify mutable static variables
// 4. Implement unsafe traits
// 5. Access fields of unions
```

---

## Summary

| Feature | Use When |
|---|---|
| `Box<T>` | Heap allocation, recursive types |
| `Rc<T>` | Multiple owners, single thread |
| `Arc<T>` | Multiple owners, multiple threads |
| `RefCell<T>` | Interior mutability, single thread |
| `Mutex<T>` | Interior mutability, multiple threads |
| `macro_rules!` | Reduce repetitive code patterns |
| `unsafe` | FFI, raw pointers, last resort |

> 💡 Rust's `unsafe` doesn't turn off the borrow checker — it just lets you do a small set of additional things. The vast majority of "unsafe" in C/C++ is perfectly safe in Rust because of the ownership system.
