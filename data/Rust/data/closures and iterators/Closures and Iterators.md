# Rust Closures & Iterators

Closures and iterators are the most powerful and idiomatic tools in Rust. They let you write expressive, zero-cost functional code.

---

## 1. Closures

A closure is an anonymous function that **captures its environment**.

```rust
let add = |a, b| a + b;
println!("{}", add(3, 4)); // 7

// Closures can capture variables
let multiplier = 3;
let triple = |x| x * multiplier;
println!("{}", triple(5)); // 15
```

### Capture Modes
```rust
let name = String::from("Alice");

// Borrow (default when possible)
let greet = || println!("Hello, {}!", name);
greet();
println!("{}", name); // name still usable

// Move — transfers ownership into closure
let greeting = move || println!("Hi, {}!", name);
greeting();
// println!("{}", name); // ERROR — name was moved
```

### Fn Traits
| Trait | Meaning |
|---|---|
| `FnOnce` | Can only be called once (consumes captured vars) |
| `FnMut` | Can be called repeatedly, mutates captures |
| `Fn` | Can be called repeatedly, no mutation |

```rust
fn apply_twice<F: Fn(i32) -> i32>(f: F, x: i32) -> i32 { f(f(x)) }
println!("{}", apply_twice(|x| x + 3, 10)); // 16
```

---

## 2. Iterators

Iterators are **lazy** — nothing runs until you consume them.

### Creating Iterators
```rust
let v = vec![1, 2, 3, 4, 5];
let iter = v.iter();       // borrows: yields &T
let iter = v.into_iter();  // consumes: yields T
let iter = v.iter_mut();   // mutable borrow: yields &mut T

// Ranges
(1..=5).for_each(|x| print!("{} ", x));  // 1 2 3 4 5
```

### Adapters (Lazy — return new iterators)
```rust
let v = vec![1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// map — transform each element
let doubled: Vec<i32> = v.iter().map(|&x| x * 2).collect();

// filter — keep matching elements
let evens: Vec<&i32> = v.iter().filter(|&&x| x % 2 == 0).collect();

// filter_map — filter AND transform in one step
let parsed: Vec<i32> = vec!["1", "bad", "3"].iter()
    .filter_map(|s| s.parse().ok())
    .collect();

// flat_map — flatten nested results
let nested = vec![vec![1,2], vec![3,4]];
let flat: Vec<i32> = nested.into_iter().flatten().collect();

// take / skip
let first3: Vec<i32> = v.iter().copied().take(3).collect();
let after3: Vec<i32> = v.iter().copied().skip(3).collect();

// enumerate — add index
for (i, val) in v.iter().enumerate() {
    println!("{}: {}", i, val);
}

// zip — pair two iterators
let letters = vec!['a','b','c'];
let numbers = vec![1, 2, 3];
let pairs: Vec<_> = letters.iter().zip(numbers.iter()).collect();

// chain — concatenate iterators
let combined: Vec<i32> = (1..=3).chain(7..=9).collect();
```

### Consumers (Eager — end the chain)
```rust
let v = vec![1, 2, 3, 4, 5];

let sum:    i32         = v.iter().sum();
let count:  usize       = v.iter().count();
let max:    Option<&i32>= v.iter().max();
let found:  Option<&i32>= v.iter().find(|&&x| x > 3);
let any_big: bool       = v.iter().any(|&x| x > 4);
let all_pos: bool       = v.iter().all(|&x| x > 0);
let product: i32        = v.iter().fold(1, |acc, &x| acc * x);
let doubled: Vec<i32>   = v.iter().map(|&x| x * 2).collect();
```

---

## 3. Custom Iterators

```rust
struct Counter { count: u32, max: u32 }

impl Counter {
    fn new(max: u32) -> Self { Counter { count: 0, max } }
}

impl Iterator for Counter {
    type Item = u32;
    fn next(&mut self) -> Option<u32> {
        if self.count < self.max {
            self.count += 1;
            Some(self.count)
        } else { None }
    }
}

// Now Counter works with all iterator adapters!
let sum: u32 = Counter::new(5).sum();           // 15
let evens: Vec<u32> = Counter::new(10).filter(|x| x % 2 == 0).collect();
```

---

## Summary

| Pattern | Code |
|---|---|
| Transform elements | `.map(\|x\| ...)` |
| Filter elements | `.filter(\|x\| ...)` |
| Combine filter+map | `.filter_map(\|x\| ...)` |
| Reduce to one value | `.fold(init, \|acc, x\| ...)` |
| Collect into Vec | `.collect::<Vec<_>>()` |
| Short-circuit find | `.find(\|x\| ...)` / `.position(\|x\| ...)` |

> 💡 Iterator chains compile down to the same machine code as hand-written loops. There is **zero runtime cost** for the abstraction.
