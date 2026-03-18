// ============================================================
//  Module 09: Concurrency — Threads, Channels, Mutex, RwLock
// ============================================================

use std::sync::{Arc, Mutex, RwLock, mpsc};
use std::thread;
use std::time::{Duration, Instant};
use std::collections::HashMap;

fn main() {
    println!("===== Module 09: Concurrency =====\n");
    thread_basics();
    channel_demo();
    shared_state_demo();
    rwlock_demo();
    real_world_demo();
}

// ─────────────────────────────────────────────
// THREADS
// ─────────────────────────────────────────────

fn thread_basics() {
    println!("--- Thread Basics ---");

    // Simple spawn
    let handle = thread::spawn(|| {
        println!("  Hello from thread {}!", thread::current().name().unwrap_or("unnamed"));
    });
    handle.join().unwrap();

    // Move ownership into thread
    let data = vec![1, 2, 3, 4, 5];
    let handle = thread::spawn(move || {
        let sum: i32 = data.iter().sum();
        println!("  Thread computed sum: {}", sum);
        sum
    });
    let result = handle.join().unwrap();
    println!("  Main received: {}", result);

    // Spawn many threads and collect results
    let start = Instant::now();
    let handles: Vec<_> = (0..5).map(|i| {
        thread::spawn(move || {
            thread::sleep(Duration::from_millis(10));
            i * i
        })
    }).collect();

    let results: Vec<i32> = handles.into_iter().map(|h| h.join().unwrap()).collect();
    println!("  5 parallel squares in {:.1}ms: {:?}", start.elapsed().as_millis(), results);
    println!();
}

// ─────────────────────────────────────────────
// CHANNELS
// ─────────────────────────────────────────────

fn channel_demo() {
    println!("--- Channels (mpsc) ---");

    // Basic send/receive
    let (tx, rx) = mpsc::channel();
    thread::spawn(move || {
        for i in 1..=5 {
            tx.send(format!("Message {}", i)).unwrap();
            thread::sleep(Duration::from_millis(5));
        }
    });

    for msg in rx { println!("  Received: {}", msg); }

    // Multiple producers
    let (tx, rx) = mpsc::channel::<(usize, i64)>();
    let handles: Vec<_> = (0..4).map(|id| {
        let tx = tx.clone();
        thread::spawn(move || {
            let sum: i64 = (1..=100).map(|x| x as i64).sum();
            tx.send((id, sum)).unwrap();
        })
    }).collect();
    drop(tx); // drop original sender so rx closes after all clones done

    let mut results = vec![(0i64); 4];
    for (id, sum) in rx { results[id] = sum; }
    for h in handles { h.join().unwrap(); }
    println!("  4 workers each summed 1..100:");
    for (i, sum) in results.iter().enumerate() { println!("    Worker {}: {}", i, sum); }

    // Pipeline: producer → transformer → consumer
    let (raw_tx, raw_rx) = mpsc::channel::<i32>();
    let (proc_tx, proc_rx) = mpsc::channel::<i32>();

    // Producer
    thread::spawn(move || {
        for i in 1..=10 { raw_tx.send(i).unwrap(); }
    });
    // Transformer
    thread::spawn(move || {
        for n in raw_rx { proc_tx.send(n * n).unwrap(); } // square each
    });
    // Consumer (main thread)
    let squares: Vec<i32> = proc_rx.into_iter().collect();
    println!("  Pipeline squares: {:?}", squares);
    println!();
}

// ─────────────────────────────────────────────
// SHARED STATE WITH MUTEX
// ─────────────────────────────────────────────

fn shared_state_demo() {
    println!("--- Arc<Mutex<T>> ---");

    // Simple counter
    let counter = Arc::new(Mutex::new(0_u32));
    let mut handles = vec![];

    for _ in 0..10 {
        let counter = Arc::clone(&counter);
        handles.push(thread::spawn(move || {
            let mut num = counter.lock().unwrap();
            *num += 1;
        }));
    }
    for h in handles { h.join().unwrap(); }
    println!("  Counter after 10 threads: {}", *counter.lock().unwrap());

    // Shared vec — collect work from multiple threads
    let results: Arc<Mutex<Vec<u64>>> = Arc::new(Mutex::new(Vec::new()));
    let mut handles = vec![];

    for chunk_start in (0u64..=100).step_by(25) {
        let results = Arc::clone(&results);
        handles.push(thread::spawn(move || {
            let sum: u64 = (chunk_start..chunk_start+25).sum();
            results.lock().unwrap().push(sum);
        }));
    }
    for h in handles { h.join().unwrap(); }

    let partial_sums = results.lock().unwrap().clone();
    let total: u64 = partial_sums.iter().sum();
    println!("  Partial sums: {:?}", partial_sums);
    println!("  Total (should be 5050): {}", total);

    // Deadlock prevention — always acquire locks in same order
    let lock_a = Arc::new(Mutex::new("Resource A"));
    let lock_b = Arc::new(Mutex::new("Resource B"));

    let (a1, b1) = (Arc::clone(&lock_a), Arc::clone(&lock_b));
    let h1 = thread::spawn(move || {
        let _a = a1.lock().unwrap();
        thread::sleep(Duration::from_millis(1));
        let _b = b1.lock().unwrap();
        "Thread 1 done"
    });

    let (a2, b2) = (Arc::clone(&lock_a), Arc::clone(&lock_b));
    let h2 = thread::spawn(move || {
        let _a = a2.lock().unwrap(); // same order as thread 1 — no deadlock
        let _b = b2.lock().unwrap();
        "Thread 2 done"
    });

    println!("  {}", h1.join().unwrap());
    println!("  {}", h2.join().unwrap());
    println!();
}

// ─────────────────────────────────────────────
// RWLOCK
// ─────────────────────────────────────────────

fn rwlock_demo() {
    println!("--- Arc<RwLock<T>> ---");

    let config: Arc<RwLock<HashMap<String, String>>> = Arc::new(RwLock::new({
        let mut m = HashMap::new();
        m.insert("host".to_string(), "localhost".to_string());
        m.insert("port".to_string(), "8080".to_string());
        m
    }));

    // Many reader threads simultaneously
    let mut readers = vec![];
    for i in 0..5 {
        let cfg = Arc::clone(&config);
        readers.push(thread::spawn(move || {
            let data = cfg.read().unwrap();
            format!("Reader {}: host={}", i, data.get("host").unwrap())
        }));
    }
    for r in readers {
        println!("  {}", r.join().unwrap());
    }

    // One writer
    {
        let mut cfg = config.write().unwrap();
        cfg.insert("port".to_string(), "9090".to_string());
        println!("  Writer updated port to 9090");
    }

    // Read back
    println!("  port after update: {}", config.read().unwrap().get("port").unwrap());
    println!();
}

// ─────────────────────────────────────────────
// REAL-WORLD: Thread pool simulation
// ─────────────────────────────────────────────

struct WorkItem { id: u32, data: Vec<i32> }

fn process_item(item: WorkItem) -> (u32, i64, i32, i32) {
    // Simulate CPU work
    let sum: i64 = item.data.iter().map(|&x| x as i64).sum();
    let min = *item.data.iter().min().unwrap();
    let max = *item.data.iter().max().unwrap();
    (item.id, sum, min, max)
}

fn real_world_demo() {
    println!("--- Real-World: Parallel Work Queue ---");

    let work_items: Vec<WorkItem> = (1..=8).map(|id| WorkItem {
        id,
        data: (0..1000).map(|x| ((x * id) % 100) as i32 + 1).collect(),
    }).collect();

    let start = Instant::now();

    // Sequential
    let seq_results: Vec<_> = work_items.iter()
        .map(|item| {
            let sum: i64 = item.data.iter().map(|&x| x as i64).sum();
            (item.id, sum)
        })
        .collect();

    let seq_time = start.elapsed();

    // Parallel using channels
    let start = Instant::now();
    let (tx, rx) = mpsc::channel();
    let work_items: Vec<WorkItem> = (1..=8).map(|id| WorkItem {
        id,
        data: (0..1000).map(|x| ((x * id) % 100) as i32 + 1).collect(),
    }).collect();

    let handles: Vec<_> = work_items.into_iter().map(|item| {
        let tx = tx.clone();
        thread::spawn(move || {
            let result = process_item(item);
            tx.send(result).unwrap();
        })
    }).collect();
    drop(tx);

    let mut par_results: Vec<(u32, i64, i32, i32)> = rx.into_iter().collect();
    par_results.sort_by_key(|r| r.0);

    for h in handles { h.join().unwrap(); }
    let par_time = start.elapsed();

    println!("  Results (id, sum, min, max):");
    for (id, sum, min, max) in &par_results {
        println!("    Item {}: sum={} min={} max={}", id, sum, min, max);
    }
    println!("  Sequential: {:.2}ms", seq_time.as_secs_f64() * 1000.0);
    println!("  Parallel:   {:.2}ms", par_time.as_secs_f64() * 1000.0);

    println!("\n✅ Module 09 complete!");
}
