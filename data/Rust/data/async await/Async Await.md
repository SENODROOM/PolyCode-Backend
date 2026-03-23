# Module 10: Async / Await

Async Rust lets you write concurrent I/O-bound code that looks synchronous. It's powered by Tokio — the most widely used async runtime.

---

## Why Async?

- **Threads** are great for CPU-bound work (parallel computation)
- **Async** is great for I/O-bound work (network, disk, timers)
- A single thread can handle thousands of concurrent async tasks
- No context-switch overhead between async tasks

---

## 1. Setup — Cargo.toml

```toml
[dependencies]
tokio = { version = "1", features = ["full"] }
```

---

## 2. async / await Basics

```rust
use tokio::time::{sleep, Duration};

// async fn returns a Future — doesn't run until awaited
async fn say_hello() -> String {
    sleep(Duration::from_millis(100)).await;  // yield control
    "Hello, async world!".to_string()
}

// #[tokio::main] wraps main in an async runtime
#[tokio::main]
async fn main() {
    let result = say_hello().await;  // .await drives the Future to completion
    println!("{}", result);
}
```

---

## 3. Running Tasks Concurrently

```rust
use tokio::join;
use tokio::task;

// join! — run futures concurrently, wait for all
#[tokio::main]
async fn main() {
    let (r1, r2, r3) = tokio::join!(
        fetch_data("url1"),
        fetch_data("url2"),
        fetch_data("url3"),
    );
    // All three run at the same time!
}

// spawn — fire & forget (like thread::spawn but async)
let handle = tokio::spawn(async {
    heavy_async_work().await
});
let result = handle.await.unwrap();
```

---

## 4. Async Error Handling

```rust
async fn fetch_user(id: u32) -> Result<User, reqwest::Error> {
    let url = format!("https://api.example.com/users/{}", id);
    let user = reqwest::get(&url)
        .await?
        .json::<User>()
        .await?;
    Ok(user)
}

// ? works exactly the same in async functions
```

---

## 5. Timeouts

```rust
use tokio::time::{timeout, Duration};

async fn with_timeout() -> Result<String, &'static str> {
    match timeout(Duration::from_secs(5), slow_operation()).await {
        Ok(result) => Ok(result),
        Err(_)     => Err("Operation timed out"),
    }
}
```

---

## 6. Channels in Async (tokio::sync)

```rust
use tokio::sync::{mpsc, oneshot};

// mpsc — multiple producers, single consumer (async version)
let (tx, mut rx) = mpsc::channel(32); // buffer size 32

tokio::spawn(async move {
    tx.send("hello").await.unwrap();
});

while let Some(msg) = rx.recv().await {
    println!("Got: {}", msg);
}

// oneshot — single value between two tasks
let (tx, rx) = oneshot::channel();
tokio::spawn(async move { tx.send(42).unwrap(); });
let value = rx.await.unwrap();
```

---

## 7. Async Traits (with async-trait crate)

```rust
use async_trait::async_trait; // cargo add async-trait

#[async_trait]
trait DataStore {
    async fn get(&self, key: &str) -> Option<String>;
    async fn set(&mut self, key: &str, value: String);
}
```

---

## Sync vs Async Comparison

```rust
// Sync — blocks the thread while waiting
fn sync_fetch(url: &str) -> String {
    // Thread is BLOCKED here doing nothing
    reqwest::blocking::get(url).unwrap().text().unwrap()
}

// Async — thread free to do other work while waiting
async fn async_fetch(url: &str) -> String {
    // Thread is RELEASED here, handles other tasks
    reqwest::get(url).await.unwrap().text().await.unwrap()
}
```

---

## Summary

| Concept | Purpose |
|---|---|
| `async fn` | Declare an async function |
| `.await` | Drive a future to completion |
| `tokio::spawn` | Run a task concurrently |
| `tokio::join!` | Run multiple futures, wait for all |
| `tokio::select!` | Wait for first future to complete |
| `tokio::sync::mpsc` | Async channels |
| `timeout()` | Limit how long to wait |

> 💡 Use `tokio::spawn` for tasks that can run truly independently. Use `join!` when you need all results. Use `select!` when you want the first result and can discard the rest.
