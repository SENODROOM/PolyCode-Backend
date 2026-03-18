// ============================================================
//  Module 10: Async / Await
//
//  Cargo.toml dependencies needed:
//  tokio = { version = "1", features = ["full"] }
//
//  Run: cargo run
// ============================================================

use std::collections::HashMap;
use std::sync::Arc;
use std::time::{Duration, Instant};
use tokio::sync::{mpsc, oneshot, RwLock};
use tokio::time::{sleep, timeout};

// ─────────────────────────────────────────────
// SECTION 1: Async Basics
// ─────────────────────────────────────────────

async fn greet(name: &str, delay_ms: u64) -> String {
    sleep(Duration::from_millis(delay_ms)).await;
    format!("Hello, {}!", name)
}

async fn async_basics() {
    println!("--- Async Basics ---");

    // Sequential await
    let start = Instant::now();
    let r1 = greet("Alice", 50).await;
    let r2 = greet("Bob", 50).await;
    println!("  Sequential: {} | {} ({:.0}ms)", r1, r2, start.elapsed().as_millis());

    // Concurrent with join!
    let start = Instant::now();
    let (r1, r2, r3) = tokio::join!(
        greet("Alice", 50),
        greet("Bob",   50),
        greet("Carol", 50),
    );
    println!("  Concurrent: {} | {} | {} ({:.0}ms)", r1, r2, r3, start.elapsed().as_millis());
}

// ─────────────────────────────────────────────
// SECTION 2: Spawning Tasks
// ─────────────────────────────────────────────

async fn compute_sum(start: u64, end: u64) -> u64 {
    sleep(Duration::from_millis(10)).await; // simulate I/O wait
    (start..=end).sum()
}

async fn spawn_demo() {
    println!("\n--- Spawning Tasks ---");

    let start = Instant::now();

    // Spawn 4 independent tasks
    let handles: Vec<_> = vec![(1,250), (251,500), (501,750), (751,1000)]
        .into_iter()
        .map(|(s, e)| tokio::spawn(async move { compute_sum(s, e).await }))
        .collect();

    let mut total = 0u64;
    for h in handles {
        total += h.await.unwrap();
    }

    println!("  Sum 1..1000 = {} (expected {})", total, (1u64..=1000).sum::<u64>());
    println!("  Time: {:.0}ms (4 tasks run concurrently)", start.elapsed().as_millis());
}

// ─────────────────────────────────────────────
// SECTION 3: Error Handling in Async
// ─────────────────────────────────────────────

#[derive(Debug)]
enum FetchError {
    NotFound(u32),
    Timeout,
    NetworkError(String),
}

impl std::fmt::Display for FetchError {
    fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
        match self {
            FetchError::NotFound(id)     => write!(f, "User {} not found", id),
            FetchError::Timeout          => write!(f, "Request timed out"),
            FetchError::NetworkError(e)  => write!(f, "Network error: {}", e),
        }
    }
}

#[derive(Debug, Clone)]
struct User { id: u32, name: String, email: String }

async fn fetch_user(id: u32) -> Result<User, FetchError> {
    sleep(Duration::from_millis(20)).await;
    match id {
        1 => Ok(User { id: 1, name: "Alice".into(), email: "alice@example.com".into() }),
        2 => Ok(User { id: 2, name: "Bob".into(),   email: "bob@example.com".into() }),
        _ => Err(FetchError::NotFound(id)),
    }
}

async fn fetch_with_timeout(id: u32, limit_ms: u64) -> Result<User, FetchError> {
    match timeout(Duration::from_millis(limit_ms), fetch_user(id)).await {
        Ok(result) => result,
        Err(_)     => Err(FetchError::Timeout),
    }
}

async fn error_handling_demo() {
    println!("\n--- Error Handling in Async ---");

    for id in [1, 2, 99] {
        match fetch_user(id).await {
            Ok(user)  => println!("  ✅ id={}: {} <{}>", user.id, user.name, user.email),
            Err(e)    => println!("  ❌ id={}: {}", id, e),
        }
    }

    println!("\n  With timeout (30ms limit):");
    match fetch_with_timeout(1, 30).await {
        Ok(user) => println!("  ✅ Got {}", user.name),
        Err(e)   => println!("  ❌ {}", e),
    }

    println!("  With timeout (5ms limit — will expire):");
    match fetch_with_timeout(1, 5).await {
        Ok(user) => println!("  ✅ Got {}", user.name),
        Err(e)   => println!("  ❌ {}", e),
    }
}

// ─────────────────────────────────────────────
// SECTION 4: Async Channels
// ─────────────────────────────────────────────

async fn channel_demo() {
    println!("\n--- Async Channels ---");

    // mpsc — producer/consumer
    let (tx, mut rx) = mpsc::channel::<String>(32);

    // Spawn producer
    let producer = tokio::spawn(async move {
        for i in 1..=5 {
            let msg = format!("Task result #{}", i);
            tx.send(msg).await.unwrap();
            sleep(Duration::from_millis(5)).await;
        }
    });

    // Consume in main task
    let mut count = 0;
    while let Some(msg) = rx.recv().await {
        println!("  Received: {}", msg);
        count += 1;
    }
    producer.await.unwrap();
    println!("  Total received: {}", count);

    // oneshot — single response
    println!("\n  oneshot channel:");
    let (answer_tx, answer_rx) = oneshot::channel::<u64>();

    tokio::spawn(async move {
        sleep(Duration::from_millis(10)).await;
        let answer: u64 = (1..=100).sum();
        answer_tx.send(answer).unwrap();
    });

    let answer = answer_rx.await.unwrap();
    println!("  Sum 1..100 = {}", answer);
}

// ─────────────────────────────────────────────
// SECTION 5: Shared Async State
// ─────────────────────────────────────────────

type Cache = Arc<RwLock<HashMap<u32, User>>>;

async fn get_user_cached(id: u32, cache: &Cache) -> Result<User, FetchError> {
    // Check cache first (read lock)
    {
        let read = cache.read().await;
        if let Some(user) = read.get(&id) {
            return Ok(user.clone());
        }
    } // read lock released here

    // Cache miss — fetch and store (write lock)
    let user = fetch_user(id).await?;
    {
        let mut write = cache.write().await;
        write.insert(id, user.clone());
    }
    Ok(user)
}

async fn shared_state_demo() {
    println!("\n--- Shared Async State (Cache) ---");

    let cache: Cache = Arc::new(RwLock::new(HashMap::new()));

    // First access — cache misses
    for id in [1, 2, 1, 2, 1] {
        let cache = Arc::clone(&cache);
        match get_user_cached(id, &cache).await {
            Ok(user) => {
                let cache_size = cache.read().await.len();
                println!("  id={}: {} (cache size: {})", id, user.name, cache_size);
            }
            Err(e) => println!("  id={}: ❌ {}", id, e),
        }
    }
}

// ─────────────────────────────────────────────
// SECTION 6: select! — Race Futures
// ─────────────────────────────────────────────

async fn select_demo() {
    println!("\n--- select! (First Wins) ---");

    // Simulate two data sources — take whoever responds first
    async fn source_a() -> &'static str {
        sleep(Duration::from_millis(30)).await;
        "Data from Source A"
    }
    async fn source_b() -> &'static str {
        sleep(Duration::from_millis(15)).await;
        "Data from Source B"  // faster!
    }

    let result = tokio::select! {
        r = source_a() => r,
        r = source_b() => r,
    };
    println!("  Winner: {}", result);

    // select! with timeout
    let fast = tokio::select! {
        result = fetch_user(1) => result.map(|u| u.name),
        _ = sleep(Duration::from_millis(5)) => Err(FetchError::Timeout),
    };
    println!("  Race vs 5ms timeout: {}", if fast.is_ok() { "fetch won" } else { "timeout won" });

    let slow = tokio::select! {
        result = fetch_user(1) => result.map(|u| u.name),
        _ = sleep(Duration::from_millis(100)) => Err(FetchError::Timeout),
    };
    println!("  Race vs 100ms timeout: {}", if slow.is_ok() { "fetch won ✅" } else { "timeout won" });
}

// ─────────────────────────────────────────────
// REAL-WORLD: Async task processor
// ─────────────────────────────────────────────

#[derive(Debug)]
struct JobResult { job_id: u32, value: u64, duration_ms: u64 }

async fn process_job(job_id: u32) -> JobResult {
    let start = Instant::now();
    let delay = (job_id * 7 % 50) as u64 + 10; // 10–56ms
    sleep(Duration::from_millis(delay)).await;
    let value: u64 = (1..=job_id as u64).map(|x| x * x).sum();
    JobResult { job_id, value, duration_ms: start.elapsed().as_millis() as u64 }
}

async fn real_world_demo() {
    println!("\n--- Real-World: Async Job Processor ---");

    let job_ids: Vec<u32> = (1..=8).collect();
    let start = Instant::now();

    // All jobs run concurrently
    let handles: Vec<_> = job_ids.iter().map(|&id| tokio::spawn(process_job(id))).collect();
    let mut results: Vec<JobResult> = Vec::new();
    for h in handles { results.push(h.await.unwrap()); }
    results.sort_by_key(|r| r.job_id);

    let total_wall = start.elapsed().as_millis();
    let total_compute: u64 = results.iter().map(|r| r.duration_ms).sum();

    println!("  {:>5} {:>12} {:>10}", "JobID", "Value", "Time(ms)");
    println!("  {}", "─".repeat(30));
    for r in &results {
        println!("  {:>5} {:>12} {:>10}", r.job_id, r.value, r.duration_ms);
    }
    println!("  {}", "─".repeat(30));
    println!("  Wall time:    {}ms", total_wall);
    println!("  Total compute: {}ms (would be sequential)", total_compute);
    println!("  Speedup:       ~{:.1}x", total_compute as f64 / total_wall as f64);

    println!("\n✅ Module 10 complete!");
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

#[tokio::main]
async fn main() {
    println!("===== Module 10: Async / Await =====\n");
    async_basics().await;
    spawn_demo().await;
    error_handling_demo().await;
    channel_demo().await;
    shared_state_demo().await;
    select_demo().await;
    real_world_demo().await;
}
