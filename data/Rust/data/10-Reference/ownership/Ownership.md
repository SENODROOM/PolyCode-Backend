# Module 03: Ownership — Rust's Superpower

Ownership is what makes Rust unique. It provides memory safety without a garbage collector by enforcing rules at compile time.

---

## The Three Rules of Ownership

1. Each value in Rust has exactly **one owner**
2. There can only be **one owner at a time**
3. When the owner goes **out of scope**, the value is **dropped** (memory freed)

---

## 1. Move Semantics

```rust
let s1 = String::from("hello");
let s2 = s1;  // s1 is MOVED to s2 — s1 no longer valid

// println!("{}", s1); // ERROR: value borrowed after move

// Clone — deep copy (expensive, explicit)
let s3 = s2.clone();
println!("{} {}", s2, s3); // both valid
```

### Copy Types (Stack Values)
Primitive types (integers, floats, bools, chars, tuples of Copy types) are copied, not moved:
```rust
let x = 5;
let y = x;     // x is COPIED, not moved
println!("{} {}", x, y); // both valid — integers implement Copy
```

---

## 2. References & Borrowing

Borrowing lets you **use a value without taking ownership**.

```rust
fn calculate_length(s: &String) -> usize {
    s.len()
}

let s = String::from("hello");
let len = calculate_length(&s); // pass a reference
println!("{} has length {}", s, len); // s still valid
```

### Rules of References
- You can have **any number of immutable references** (`&T`)
- OR **exactly one mutable reference** (`&mut T`)
- Never both at the same time
- References must always be valid (no dangling references)

```rust
let mut s = String::from("hello");

// Immutable references
let r1 = &s;
let r2 = &s;
println!("{} and {}", r1, r2); // OK — both immutable

// Mutable reference (only one allowed at a time)
let r3 = &mut s;
r3.push_str(", world");
// let r4 = &mut s; // ERROR: two mutable borrows
```

---

## 3. The Borrow Checker

```rust
// Dangling reference — Rust prevents this!
fn dangle() -> &String {   // ERROR
    let s = String::from("hello");
    &s  // s dropped here — reference would dangle
}

// Correct: return the String itself (transfer ownership)
fn no_dangle() -> String {
    String::from("hello")
}
```

---

## 4. Slices

Slices are references to a contiguous portion of a collection:

```rust
let s = String::from("hello world");

// String slices
let hello = &s[0..5];   // "hello"
let world = &s[6..11];  // "world"
let all   = &s[..];     // whole string

// Array slices
let a = [1, 2, 3, 4, 5];
let slice = &a[1..3];   // [2, 3]
```

### String Slice as Function Parameter
```rust
// Better to accept &str than &String — works with both
fn first_word(s: &str) -> &str {
    let bytes = s.as_bytes();
    for (i, &byte) in bytes.iter().enumerate() {
        if byte == b' ' { return &s[0..i]; }
    }
    &s[..]
}
```

---

## 5. Lifetime Basics

Lifetimes ensure references don't outlive the data they point to.

```rust
// The compiler infers lifetimes most of the time
fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
    if x.len() > y.len() { x } else { y }
}
// 'a means: the return lives at least as long as both inputs
```

---

## Memory Model Summary

```
Stack                          Heap
┌──────────────┐              ┌──────────────────┐
│ s1           │              │                  │
│  ptr ────────┼──────────────▶  "hello"         │
│  len: 5      │              │                  │
│  cap: 5      │              └──────────────────┘
└──────────────┘
```

When `s1` goes out of scope, Rust calls `drop()` and frees the heap memory automatically.

---

## Summary

| Concept | Rule |
|---|---|
| Ownership | One owner, one scope |
| Move | Assignment transfers ownership |
| Clone | Explicit deep copy |
| Reference `&T` | Borrow without owning (any number) |
| Mut Reference `&mut T` | Exclusive borrow (only one) |
| Slice | Reference to part of a collection |

> 💡 **Golden rule**: If you get borrow checker errors, ask yourself: "Who owns this data, and for how long?" The answer usually points to the solution.
