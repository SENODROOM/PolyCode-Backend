# Module 12: Testing in Rust

Rust has first-class testing built into the language and cargo. No frameworks needed — `cargo test` is all you need to run a comprehensive test suite.

---

## 1. Unit Tests

Tests live in the same file as the code, inside a `#[cfg(test)]` module.

```rust
fn add(a: i32, b: i32) -> i32 { a + b }

#[cfg(test)]
mod tests {
    use super::*;  // import everything from parent module

    #[test]
    fn test_add_positive() {
        assert_eq!(add(2, 3), 5);
    }

    #[test]
    fn test_add_negative() {
        assert_eq!(add(-1, -1), -2);
    }
}
```

Run: `cargo test`

---

## 2. Assertions

```rust
assert!(condition);                    // panics if false
assert_eq!(left, right);               // panics if !=
assert_ne!(left, right);               // panics if ==
assert!(val > 0, "Expected positive, got {}", val);  // custom message

// Float comparison (never use == for floats)
let result = 0.1 + 0.2;
assert!((result - 0.3).abs() < 1e-10, "Float mismatch: {}", result);
```

---

## 3. Testing Panics & Errors

```rust
#[test]
#[should_panic]
fn test_divide_by_zero() {
    let _ = 10 / 0;
}

#[test]
#[should_panic(expected = "Division by zero")]
fn test_panic_message() {
    panic!("Division by zero");
}

// Test Result-returning functions
#[test]
fn test_returns_error() -> Result<(), String> {
    let result = some_function()?;
    assert_eq!(result, 42);
    Ok(())
}
```

---

## 4. Test Attributes

```rust
#[test]
#[ignore]                        // skip unless run with --ignored
fn expensive_test() { ... }

// Run ignored: cargo test -- --ignored

#[test]
#[cfg(target_os = "linux")]     // conditional test
fn linux_only_test() { ... }
```

---

## 5. Integration Tests

Lives in `tests/` directory — separate crate, tests the public API.

```
my_project/
├── src/
│   └── lib.rs
└── tests/
    └── integration_test.rs     ← cargo test finds these automatically
```

```rust
// tests/integration_test.rs
use my_project::Calculator;

#[test]
fn test_full_workflow() {
    let calc = Calculator::new();
    assert_eq!(calc.add(5, 3), 8);
    assert_eq!(calc.multiply(4, 4), 16);
}
```

---

## 6. Test Organization

```rust
#[cfg(test)]
mod tests {
    use super::*;

    mod unit {
        use super::*;
        #[test] fn test_small() { ... }
    }

    mod integration {
        use super::*;
        #[test] fn test_big() { ... }
    }
}
```

---

## 7. Benchmarks (nightly only)

```rust
#![feature(test)]
extern crate test;
use test::Bencher;

#[bench]
fn bench_sort_1000(b: &mut Bencher) {
    b.iter(|| {
        let mut v: Vec<i32> = (0..1000).rev().collect();
        v.sort();
    });
}
```

---

## Cargo Test Commands

```bash
cargo test                         # run all tests
cargo test test_add                # run tests matching "test_add"
cargo test -- --nocapture          # show println! output
cargo test -- --test-threads=1     # run sequentially
cargo test -- --ignored            # run ignored tests
cargo test --doc                   # run doc tests
```

---

## Summary

| What to test | How |
|---|---|
| Normal output | `assert_eq!` / `assert!` |
| Panics | `#[should_panic]` |
| Error returns | Return `Result<(), E>` from test |
| Platform-specific | `#[cfg(target_os = "...")]` |
| Slow tests | `#[ignore]` |
| Public API | `tests/` directory |

> 💡 Write tests for **behavior, not implementation**. Test what the function does, not how it does it. This lets you refactor freely without rewriting tests.
