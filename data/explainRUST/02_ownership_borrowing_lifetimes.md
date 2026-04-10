## Ownership System

This is Rust's **defining feature** — a set of rules the compiler enforces at compile time to manage memory without GC.

### The Three Rules of Ownership

```
Ownership Rules
───────────────────────────────────────────────────────
Rule 1: Each value has exactly ONE owner
Rule 2: There can only be one owner at a time
Rule 3: When the owner goes out of scope, value is dropped
───────────────────────────────────────────────────────
```

```rust
fn main() {
    // s1 owns the String
    let s1 = String::from("hello");

    // MOVE: ownership transfers to s2
    let s2 = s1;

    // println!("{s1}");  ❌ COMPILE ERROR: s1 was moved!
    println!("{s2}");     // ✅ s2 owns it now

    // CLONE: deep copy (explicit, possibly expensive)
    let s3 = s2.clone();
    println!("{s2} and {s3}");  // ✅ both valid

    // Copy types (stack data) — automatically copied, not moved
    let x = 5;
    let y = x;  // x is COPIED (integers implement Copy trait)
    println!("{x} and {y}");  // ✅ both valid

} // s2 and s3 dropped here — memory freed automatically (no GC!)
```

### Memory Layout

```
Stack vs Heap
──────────────────────────────────────────────────────
STACK (fast, fixed size, LIFO)
┌──────────────────────────────┐
│  s2                          │
│  ┌────────┬────────┬───────┐ │
│  │ ptr    │ len: 5 │ cap:5 │ │ ◄── metadata on stack
│  └───┬────┴────────┴───────┘ │
└──────┼───────────────────────┘
       │
       ▼ pointer to heap
HEAP (slow, dynamic size)
┌─────────────────────────────┐
│  h e l l o                  │ ◄── actual data on heap
│  0 1 2 3 4                  │
└─────────────────────────────┘
       ▲
       When s2 is dropped, this memory is freed via `drop()`
──────────────────────────────────────────────────────
```

---

## Borrowing & References

Instead of transferring ownership, you can *borrow* values.

```
Borrowing Rules (The Borrow Checker)
──────────────────────────────────────────────────────────────
At any given time, you can have EITHER:
  • Any number of immutable references (&T), OR
  • Exactly ONE mutable reference (&mut T)
  • But NOT both at the same time
References must always be valid (no dangling references)
──────────────────────────────────────────────────────────────
```

```rust
fn main() {
    let s = String::from("hello");

    // Immutable reference — s is borrowed but not moved
    let len = calculate_length(&s);
    println!("'{s}' has {len} characters");  // s still valid here!

    // Mutable reference
    let mut s2 = String::from("hello");
    change(&mut s2);
    println!("{s2}");  // "hello, world"

    // Multiple immutable refs — OK
    let r1 = &s;
    let r2 = &s;
    println!("{r1} {r2}");  // ✅

    // Cannot mix mutable and immutable refs
    let mut s3 = String::from("hello");
    let r3 = &s3;
    // let r4 = &mut s3;  ❌ Cannot borrow s3 as mutable while immutable ref r3 exists
}

fn calculate_length(s: &String) -> usize {
    s.len()  // s is borrowed, NOT owned — won't be dropped here
}

fn change(s: &mut String) {
    s.push_str(", world");
}
```

---

## Lifetimes

Lifetimes ensure references don't outlive the data they point to.

```rust
// The borrow checker tracks lifetimes automatically in simple cases
// For complex cases, you annotate with lifetime parameters

// 'a is a lifetime parameter — read as "lifetime a"
fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
    if x.len() > y.len() { x } else { y }
}

// This would NOT compile — dangling reference
fn dangling() -> &String {   // ❌
    let s = String::from("hello");
    &s  // s is dropped at end of function, reference would be invalid!
}

// Solution: return owned value
fn not_dangling() -> String {  // ✅
    String::from("hello")
}

// Struct with lifetime annotation
struct Important<'a> {
    content: &'a str,  // struct cannot outlive the str it references
}

impl<'a> Important<'a> {
    fn announce(&self) -> &str {
        self.content
    }
}
```

---

