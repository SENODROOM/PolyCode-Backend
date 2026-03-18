# Module 02: Rust Fundamentals

Variables, data types, control flow, and functions — the building blocks of every Rust program.

---

## 1. Variables & Mutability

```rust
let x = 5;          // immutable — cannot reassign
let mut y = 5;      // mutable
y += 1;             // OK

// Shadowing — redeclare same name, can change type
let spaces = "   ";
let spaces = spaces.len(); // now it's a usize, not &str

// Constants — always immutable, must annotate type
const MAX_POINTS: u32 = 100_000;
```

---

## 2. Data Types

### Scalar Types
```rust
// Integers
let a: i8   = -128;          // 8-bit signed
let b: u8   = 255;           // 8-bit unsigned
let c: i32  = 2_147_483_647; // 32-bit signed (default)
let d: u64  = 18_446_744_073_709_551_615;
let e: i128 = -170_141_183_460_469_231_731_687_303_715_884_105_728;
let f: isize = 42;            // pointer-sized signed

// Floats
let g: f32 = 3.14;           // 32-bit
let h: f64 = 3.14159265;     // 64-bit (default)

// Boolean
let t: bool = true;
let f: bool = false;

// Character — 4 bytes, Unicode scalar value
let ch: char = '😎';
```

### Integer Literals
```rust
let decimal     = 98_222;
let hex         = 0xff;
let octal       = 0o77;
let binary      = 0b1111_0000;
let byte        = b'A';    // u8 only
```

### Compound Types
```rust
// Tuple — fixed size, mixed types
let tup: (i32, f64, bool) = (500, 6.4, true);
let (x, y, z) = tup;       // destructure
println!("{}", tup.0);     // access by index

// Array — fixed size, same type, stack-allocated
let arr: [i32; 5] = [1, 2, 3, 4, 5];
let zeros = [0; 10];       // [0, 0, 0, ..., 0] — 10 elements
println!("{}", arr[0]);
println!("len: {}", arr.len());
```

---

## 3. Control Flow

### if / else if / else
```rust
let number = 7;

if number < 5 {
    println!("less than five");
} else if number == 5 {
    println!("five");
} else {
    println!("greater than five");
}

// if is an expression — returns a value
let description = if number % 2 == 0 { "even" } else { "odd" };
```

### Loops
```rust
// loop — infinite, exit with break
let mut counter = 0;
let result = loop {
    counter += 1;
    if counter == 10 { break counter * 2; } // break returns a value
};

// while
let mut n = 3;
while n != 0 { print!("{} ", n); n -= 1; }

// for — most common, preferred
for i in 0..5 { print!("{} ", i); }       // 0 1 2 3 4
for i in 0..=5 { print!("{} ", i); }      // 0 1 2 3 4 5
for i in (0..5).rev() { print!("{} ", i); }// 4 3 2 1 0

let arr = [10, 20, 30];
for val in arr { println!("{}", val); }
for (i, val) in arr.iter().enumerate() { println!("{}: {}", i, val); }
```

---

## 4. Functions

```rust
// Basic function
fn greet(name: &str) -> String {
    format!("Hello, {}!", name)   // no semicolon = return value
}

// Multiple parameters
fn add(a: i32, b: i32) -> i32 {
    a + b
}

// Multiple return values via tuple
fn min_max(arr: &[i32]) -> (i32, i32) {
    let mut min = arr[0];
    let mut max = arr[0];
    for &x in arr { if x < min { min = x; } if x > max { max = x; } }
    (min, max)
}

// Early return
fn divide(a: f64, b: f64) -> f64 {
    if b == 0.0 { return 0.0; }
    a / b
}
```

### Statements vs Expressions
```rust
// Statement — does not return a value (has semicolon)
let x = 5;

// Expression — returns a value (no semicolon)
let y = {
    let x = 3;
    x + 1        // this is the expression value → y = 4
};
```

---

## 5. String Types

```rust
// &str — string slice, immutable, stack reference
let hello: &str = "Hello";

// String — heap-allocated, growable, owned
let mut owned = String::from("Hello");
owned.push(' ');
owned.push_str("World");
owned += "!";

// Convert between them
let s: String = hello.to_string();
let slice: &str = &owned;

// Useful String methods
let s = String::from("  hello world  ");
println!("{}", s.trim());                    // "hello world"
println!("{}", s.to_uppercase());            // "  HELLO WORLD  "
println!("{}", s.contains("world"));         // true
println!("{}", s.replace("world", "Rust"));  // "  hello Rust  "
let parts: Vec<&str> = "a,b,c".split(',').collect();
```

---

## Summary

| Concept | Key Point |
|---|---|
| Variables | Immutable by default, `mut` to mutate |
| Shadowing | Re-bind name, can change type |
| Types | Statically typed, usually inferred |
| Control flow | `if` is an expression, `for` iterates ranges |
| Functions | Last expression is return value |
| Strings | `&str` (borrowed) vs `String` (owned) |

> 💡 Rust requires all variables to be **initialized before use**. The compiler rejects uninitialized reads — no undefined behavior.
