# Module 05: Traits & Generics

Traits define shared behavior across types. Generics write code that works for any type satisfying certain constraints. Together they give Rust polymorphism without inheritance.

---

## 1. Traits — Defining Shared Behavior

```rust
trait Summary {
    // Required method — must be implemented
    fn summarize(&self) -> String;

    // Default method — can be overridden
    fn preview(&self) -> String {
        format!("{}...", &self.summarize()[..50.min(self.summarize().len())])
    }
}

struct Article { title: String, author: String, content: String }
struct Tweet   { user: String, message: String }

impl Summary for Article {
    fn summarize(&self) -> String {
        format!("{} by {}", self.title, self.author)
    }
}

impl Summary for Tweet {
    fn summarize(&self) -> String {
        format!("{}: {}", self.user, self.message)
    }
}
```

---

## 2. Trait Bounds — Constrained Generics

```rust
// impl Trait syntax (simple)
fn notify(item: &impl Summary) {
    println!("Breaking: {}", item.summarize());
}

// Generic syntax (equivalent, more flexible)
fn notify<T: Summary>(item: &T) {
    println!("Breaking: {}", item.summarize());
}

// Multiple trait bounds
fn notify_printable<T: Summary + std::fmt::Display>(item: &T) { }

// where clause (cleaner for complex bounds)
fn complex<T, U>(t: &T, u: &U) -> String
where
    T: Summary + Clone,
    U: Summary + std::fmt::Debug,
{ format!("{} {}", t.summarize(), u.summarize()) }
```

---

## 3. Generics

```rust
// Generic function
fn largest<T: PartialOrd>(list: &[T]) -> &T {
    let mut largest = &list[0];
    for item in list {
        if item > largest { largest = item; }
    }
    largest
}

// Generic struct
struct Pair<T> { first: T, second: T }

impl<T: std::fmt::Display + PartialOrd> Pair<T> {
    fn new(first: T, second: T) -> Self { Self { first, second } }
    fn max(&self) -> &T {
        if self.first >= self.second { &self.first } else { &self.second }
    }
}

// Generic enum (Option and Result are generic!)
enum MyOption<T> { Some(T), None }
enum MyResult<T, E> { Ok(T), Err(E) }
```

---

## 4. Dynamic Dispatch — Trait Objects

When you need a collection of mixed types:

```rust
// Box<dyn Trait> — heap-allocated trait object
fn make_shapes() -> Vec<Box<dyn Shape>> {
    vec![
        Box::new(Circle::new(3.0)),
        Box::new(Rectangle::new(4.0, 5.0)),
        Box::new(Triangle::new(6.0, 4.0)),
    ]
}

// dyn Trait in function parameters
fn total_area(shapes: &[Box<dyn Shape>]) -> f64 {
    shapes.iter().map(|s| s.area()).sum()
}
```

---

## 5. Standard Library Traits

```rust
use std::fmt;

// Display — for user-facing output
impl fmt::Display for Point {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "({}, {})", self.x, self.y)
    }
}

// From / Into — type conversions
impl From<(f64, f64)> for Point {
    fn from((x, y): (f64, f64)) -> Self { Self { x, y } }
}
let p: Point = (1.0, 2.0).into();

// Iterator — implement for custom types
impl Iterator for Counter {
    type Item = u32;
    fn next(&mut self) -> Option<u32> { ... }
}
```

---

## Static vs Dynamic Dispatch

| | Static (`<T: Trait>`) | Dynamic (`dyn Trait`) |
|---|---|---|
| Resolution | Compile time | Runtime |
| Performance | Faster (inlined) | Slightly slower (vtable) |
| Code size | Larger (monomorphized) | Smaller |
| Heterogeneous collections | ❌ | ✅ |

> 💡 Prefer **generics** (static dispatch) by default. Use **trait objects** (`dyn Trait`) only when you need heterogeneous collections or the type isn't known at compile time.
