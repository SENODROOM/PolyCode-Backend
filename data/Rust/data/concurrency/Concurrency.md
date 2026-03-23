# Module 09: Concurrency

Rust's ownership system makes it impossible to have data races at compile time — "fearless concurrency". You write concurrent code knowing the compiler will catch the dangerous patterns.

---

## 1. Threads

```rust
use std::thread;
use std::time::Duration;

// Spawn a thread
let handle = thread::spawn(|| {
    println!("Hello from a new thread!");
    thread::sleep(Duration::from_millis(100));
});

handle.join().unwrap(); // wait for thread to finish

// Move ownership into thread
let data = vec![1, 2, 3];
let handle = thread::spawn(move || {
    println!("Thread got: {:?}", data); // data moved here
});
handle.join().unwrap();
```

---

## 2. Channels — Message Passing

```rust
use std::sync::mpsc; // multiple producer, single consumer

let (tx, rx) = mpsc::channel();

thread::spawn(move || {
    tx.send("Hello from thread!").unwrap();
    tx.send("Another message").unwrap();
});

let msg1 = rx.recv().unwrap(); // blocking receive
let msg2 = rx.recv().unwrap();

// Non-blocking
match rx.try_recv() {
    Ok(msg)  => println!("Got: {}", msg),
    Err(_)   => println!("No message yet"),
}

// Receive all messages
for received in rx { println!("{}", received); }
```

---

## 3. Shared State — Arc + Mutex

```rust
use std::sync::{Arc, Mutex};

// Arc = atomic reference counted (thread-safe Rc)
// Mutex = mutual exclusion (one thread at a time)

let counter = Arc::new(Mutex::new(0));
let mut handles = vec![];

for _ in 0..10 {
    let counter = Arc::clone(&counter);
    let handle = thread::spawn(move || {
        let mut num = counter.lock().unwrap();
        *num += 1;
    }); // MutexGuard released (unlocked) when it goes out of scope
    handles.push(handle);
}

for h in handles { h.join().unwrap(); }
println!("Final count: {}", *counter.lock().unwrap()); // 10
```

---

## 4. RwLock — Multiple Readers OR One Writer

```rust
use std::sync::RwLock;

let data = Arc::new(RwLock::new(vec![1, 2, 3]));

// Many readers at once
let r1 = data.read().unwrap();
let r2 = data.read().unwrap();
println!("{:?} {:?}", r1, r2);
drop(r1); drop(r2);

// One writer — exclusive
let mut w = data.write().unwrap();
w.push(4);
```

---

## 5. Rayon — Data Parallelism

```rust
use rayon::prelude::*; // cargo add rayon

// Sequential
let sum: i64 = (1..=1_000_000).sum();

// Parallel — just change iter() to par_iter()!
let sum: i64 = (1_i64..=1_000_000).into_par_iter().sum();

// Parallel map/filter
let results: Vec<i32> = data
    .par_iter()
    .filter(|&&x| x % 2 == 0)
    .map(|&x| x * x)
    .collect();
```

---

## Concurrency Patterns Summary

| Pattern | Tool | Use When |
|---|---|---|
| Fire & forget task | `thread::spawn` | Independent work |
| Communicate between threads | `mpsc::channel` | Message passing |
| Shared mutable state | `Arc<Mutex<T>>` | Multiple threads write |
| Shared read-heavy state | `Arc<RwLock<T>>` | Many readers, few writers |
| CPU-bound data parallelism | `rayon` | Process large datasets |

> 💡 **Prefer channels over shared state** when possible. "Share memory by communicating, don't communicate by sharing memory." Channels are easier to reason about.
