# Rust Algorithms

Classic algorithms in idiomatic Rust — leveraging the ownership model, iterators, and zero-cost abstractions.

---

## Why Algorithms in Rust?

- **Memory safety** — no buffer overflows, no use-after-free
- **Performance** — comparable to C/C++ with no GC pauses
- **Iterators** — algorithms compose cleanly with `.map()`, `.filter()`, `.fold()`

---

## 1. Searching

### Binary Search (manual)
```rust
fn binary_search<T: Ord>(arr: &[T], target: &T) -> Option<usize> {
    let (mut left, mut right) = (0, arr.len());
    while left < right {
        let mid = left + (right - left) / 2;
        match arr[mid].cmp(target) {
            std::cmp::Ordering::Equal   => return Some(mid),
            std::cmp::Ordering::Less    => left = mid + 1,
            std::cmp::Ordering::Greater => right = mid,
        }
    }
    None
}
// Rust std: slice.binary_search(&target)
```

---

## 2. Sorting

### Merge Sort
```rust
fn merge_sort(arr: &mut Vec<i32>) {
    let len = arr.len();
    if len <= 1 { return; }
    let mid = len / 2;
    let mut left  = arr[..mid].to_vec();
    let mut right = arr[mid..].to_vec();
    merge_sort(&mut left);
    merge_sort(&mut right);
    let (mut i, mut j, mut k) = (0, 0, 0);
    while i < left.len() && j < right.len() {
        if left[i] <= right[j] { arr[k] = left[i];  i += 1; }
        else                   { arr[k] = right[j]; j += 1; }
        k += 1;
    }
    while i < left.len()  { arr[k] = left[i];  i += 1; k += 1; }
    while j < right.len() { arr[k] = right[j]; j += 1; k += 1; }
}
```

### Quick Sort
```rust
fn quick_sort(arr: &mut [i32]) {
    if arr.len() <= 1 { return; }
    let pivot_idx = partition(arr);
    quick_sort(&mut arr[..pivot_idx]);
    quick_sort(&mut arr[pivot_idx + 1..]);
}

fn partition(arr: &mut [i32]) -> usize {
    let pivot = arr[arr.len() - 1];
    let mut i = 0;
    for j in 0..arr.len() - 1 {
        if arr[j] <= pivot { arr.swap(i, j); i += 1; }
    }
    arr.swap(i, arr.len() - 1);
    i
}
```

---

## 3. Graph Algorithms (BFS/DFS with HashMaps)

```rust
use std::collections::{HashMap, HashSet, VecDeque};

fn bfs(graph: &HashMap<&str, Vec<&str>>, start: &str) -> Vec<String> {
    let mut visited = HashSet::new();
    let mut queue   = VecDeque::new();
    let mut order   = Vec::new();
    visited.insert(start);
    queue.push_back(start);
    while let Some(node) = queue.pop_front() {
        order.push(node.to_string());
        if let Some(neighbors) = graph.get(node) {
            for &n in neighbors {
                if visited.insert(n) { queue.push_back(n); }
            }
        }
    }
    order
}

fn dfs(graph: &HashMap<&str, Vec<&str>>, start: &str) -> Vec<String> {
    let mut visited = HashSet::new();
    let mut order   = Vec::new();
    dfs_helper(graph, start, &mut visited, &mut order);
    order
}

fn dfs_helper<'a>(graph: &HashMap<&'a str, Vec<&'a str>>, node: &'a str,
                   visited: &mut HashSet<&'a str>, order: &mut Vec<String>) {
    if !visited.insert(node) { return; }
    order.push(node.to_string());
    if let Some(neighbors) = graph.get(node) {
        for &n in neighbors { dfs_helper(graph, n, visited, order); }
    }
}
```

---

## 4. Dynamic Programming

```rust
// Fibonacci with memoization
fn fib(n: u64, memo: &mut HashMap<u64, u64>) -> u64 {
    if n <= 1 { return n; }
    if let Some(&v) = memo.get(&n) { return v; }
    let result = fib(n - 1, memo) + fib(n - 2, memo);
    memo.insert(n, result);
    result
}

// Longest Common Subsequence
fn lcs(a: &str, b: &str) -> usize {
    let (a, b): (Vec<char>, Vec<char>) = (a.chars().collect(), b.chars().collect());
    let (m, n) = (a.len(), b.len());
    let mut dp = vec![vec![0usize; n + 1]; m + 1];
    for i in 1..=m {
        for j in 1..=n {
            dp[i][j] = if a[i-1] == b[j-1] { dp[i-1][j-1] + 1 }
                       else { dp[i-1][j].max(dp[i][j-1]) };
        }
    }
    dp[m][n]
}

// Coin Change — minimum coins
fn coin_change(coins: &[u32], amount: u32) -> Option<u32> {
    let mut dp = vec![u32::MAX; (amount + 1) as usize];
    dp[0] = 0;
    for i in 1..=amount as usize {
        for &c in coins {
            if c as usize <= i && dp[i - c as usize] != u32::MAX {
                dp[i] = dp[i].min(dp[i - c as usize] + 1);
            }
        }
    }
    if dp[amount as usize] == u32::MAX { None } else { Some(dp[amount as usize]) }
}
```

---

## 5. Iterator-based Algorithms

```rust
// Rust shines here — algorithms as iterator chains
let data = vec![3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5];

// Sum of squares of even numbers
let result: i32 = data.iter()
    .filter(|&&x| x % 2 == 0)
    .map(|&x| x * x)
    .sum();

// Running maximum
let running_max: Vec<i32> = data.iter()
    .scan(i32::MIN, |max, &x| { *max = (*max).max(x); Some(*max) })
    .collect();

// Group by frequency
let mut freq: HashMap<i32, usize> = HashMap::new();
data.iter().for_each(|&x| *freq.entry(x).or_insert(0) += 1);
```

---

## Summary

| Algorithm | Rust Idiom |
|---|---|
| Sort | `slice.sort()` / `sort_by` / `sort_unstable` |
| Binary Search | `slice.binary_search(&v)` |
| Map/Filter | Iterator adapters |
| Count/Sum/Min/Max | `.count()` `.sum()` `.min()` `.max()` |
| Group/Frequency | `HashMap` + `.entry().or_insert()` |

> 💡 Prefer `sort_unstable` when stability doesn't matter — it's ~20% faster. Use `sort_by_key` for complex comparisons.
