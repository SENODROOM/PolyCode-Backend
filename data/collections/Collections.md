# Module 06: Collections

Rust's standard library provides several efficient, safe collection types. The three most used are `Vec`, `HashMap`, and `HashSet`.

---

## 1. Vec<T> — Dynamic Array

```rust
// Creating
let mut v: Vec<i32> = Vec::new();
let v = vec![1, 2, 3, 4, 5];

// Adding / removing
v.push(6);           // add to end
v.insert(0, 0);      // insert at index
v.pop();             // remove last → Option<T>
v.remove(0);         // remove at index (expensive — shifts)
v.retain(|&x| x % 2 == 0); // keep only even

// Accessing
let third = &v[2];          // panics if out of bounds
let third = v.get(2);       // returns Option<&T>

// Iterating
for val in &v { print!("{} ", val); }
for val in &mut v { *val *= 2; }
let doubled: Vec<i32> = v.iter().map(|&x| x * 2).collect();

// Useful methods
v.sort();
v.sort_by(|a, b| b.cmp(a));   // descending
v.dedup();                     // remove consecutive duplicates
v.reverse();
v.len(); v.is_empty();
v.contains(&5);
v.iter().sum::<i32>();
v.iter().min(); v.iter().max();
```

---

## 2. HashMap<K, V>

```rust
use std::collections::HashMap;

let mut scores: HashMap<String, i32> = HashMap::new();

// Inserting
scores.insert("Alice".to_string(), 95);
scores.insert("Bob".to_string(), 87);

// Entry API — insert only if absent
scores.entry("Charlie".to_string()).or_insert(72);
*scores.entry("Alice".to_string()).or_insert(0) += 5; // increment

// Accessing
let score = scores["Alice"];               // panics if missing
let score = scores.get("Alice");           // Option<&V>
let score = scores.get("Dave").unwrap_or(&0);

// Iterating
for (name, score) in &scores { println!("{}: {}", name, score); }

// Removing
scores.remove("Bob");

// Useful methods
scores.contains_key("Alice");
scores.len();
scores.keys().collect::<Vec<_>>();
scores.values().collect::<Vec<_>>();
```

---

## 3. HashSet<T>

```rust
use std::collections::HashSet;

let mut set: HashSet<i32> = HashSet::new();
set.insert(1); set.insert(2); set.insert(3);
set.insert(2); // duplicate — ignored!

set.contains(&1);  // true
set.remove(&2);

// Set operations
let a: HashSet<i32> = [1,2,3,4].iter().cloned().collect();
let b: HashSet<i32> = [3,4,5,6].iter().cloned().collect();

let union:        HashSet<_> = a.union(&b).collect();
let intersection: HashSet<_> = a.intersection(&b).collect();
let difference:   HashSet<_> = a.difference(&b).collect();
```

---

## 4. VecDeque — Double-Ended Queue

```rust
use std::collections::VecDeque;

let mut deque: VecDeque<i32> = VecDeque::new();
deque.push_front(1); // O(1)
deque.push_back(2);  // O(1)
deque.pop_front();   // O(1)
deque.pop_back();    // O(1)
// Use for BFS, sliding window, FIFO queues
```

---

## 5. BinaryHeap — Priority Queue

```rust
use std::collections::BinaryHeap;

let mut heap = BinaryHeap::new(); // max-heap
heap.push(3); heap.push(1); heap.push(4);
println!("{:?}", heap.pop()); // Some(4) — always the max
```

---

## Summary

| Collection | Use When |
|---|---|
| `Vec<T>` | Default ordered list |
| `HashMap<K,V>` | Key-value lookup |
| `HashSet<T>` | Unique values, membership test |
| `VecDeque<T>` | Queue (push/pop both ends) |
| `BinaryHeap<T>` | Priority queue (always get max) |

> 💡 When building a `HashMap` from pairs, use `.collect::<HashMap<_,_>>()` on an iterator of tuples. Much faster than inserting one by one.
