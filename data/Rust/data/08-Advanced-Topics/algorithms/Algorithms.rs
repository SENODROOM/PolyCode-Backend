// ============================================================
//  Rust Algorithms — Complete Examples
// ============================================================

use std::collections::{HashMap, HashSet, VecDeque};

// ─────────────────────────────────────────────
// SECTION 1: Searching
// ─────────────────────────────────────────────

fn binary_search<T: Ord>(arr: &[T], target: &T) -> Option<usize> {
    let (mut left, mut right) = (0, arr.len());
    while left < right {
        let mid = left + (right - left) / 2;
        match arr[mid].cmp(target) {
            std::cmp::Ordering::Equal   => return Some(mid),
            std::cmp::Ordering::Less    => left  = mid + 1,
            std::cmp::Ordering::Greater => right = mid,
        }
    }
    None
}

// ─────────────────────────────────────────────
// SECTION 2: Sorting
// ─────────────────────────────────────────────

fn merge_sort(arr: &mut Vec<i32>) {
    let len = arr.len();
    if len <= 1 { return; }
    let mid   = len / 2;
    let mut l = arr[..mid].to_vec();
    let mut r = arr[mid..].to_vec();
    merge_sort(&mut l);
    merge_sort(&mut r);
    let (mut i, mut j, mut k) = (0, 0, 0);
    while i < l.len() && j < r.len() {
        if l[i] <= r[j] { arr[k] = l[i]; i += 1; } else { arr[k] = r[j]; j += 1; }
        k += 1;
    }
    while i < l.len() { arr[k] = l[i]; i += 1; k += 1; }
    while j < r.len() { arr[k] = r[j]; j += 1; k += 1; }
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

fn quick_sort(arr: &mut [i32]) {
    if arr.len() <= 1 { return; }
    let p = partition(arr);
    quick_sort(&mut arr[..p]);
    quick_sort(&mut arr[p + 1..]);
}

// ─────────────────────────────────────────────
// SECTION 3: Graph Algorithms
// ─────────────────────────────────────────────

fn bfs<'a>(graph: &'a HashMap<&str, Vec<&'a str>>, start: &'a str) -> Vec<String> {
    let mut visited = HashSet::new();
    let mut queue   = VecDeque::new();
    let mut order   = Vec::new();
    visited.insert(start);
    queue.push_back(start);
    while let Some(node) = queue.pop_front() {
        order.push(node.to_string());
        if let Some(neighbors) = graph.get(node) {
            for &n in neighbors { if visited.insert(n) { queue.push_back(n); } }
        }
    }
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

fn dfs(graph: &HashMap<&str, Vec<&str>>, start: &str) -> Vec<String> {
    let mut visited = HashSet::new();
    let mut order   = Vec::new();
    dfs_helper(graph, start, &mut visited, &mut order);
    order
}

// ─────────────────────────────────────────────
// SECTION 4: Dynamic Programming
// ─────────────────────────────────────────────

fn fib(n: u64, memo: &mut HashMap<u64, u64>) -> u64 {
    if n <= 1 { return n; }
    if let Some(&v) = memo.get(&n) { return v; }
    let result = fib(n - 1, memo) + fib(n - 2, memo);
    memo.insert(n, result);
    result
}

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

fn coin_change(coins: &[u32], amount: u32) -> Option<u32> {
    let mut dp = vec![u32::MAX; (amount + 1) as usize];
    dp[0] = 0;
    for i in 1..=(amount as usize) {
        for &c in coins {
            if (c as usize) <= i && dp[i - c as usize] != u32::MAX {
                dp[i] = dp[i].min(dp[i - c as usize] + 1);
            }
        }
    }
    if dp[amount as usize] == u32::MAX { None } else { Some(dp[amount as usize]) }
}

// ─────────────────────────────────────────────
// SECTION 5: Iterator-based Algorithms
// ─────────────────────────────────────────────

fn iterator_demos() {
    let data = vec![3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5];

    let sum_squares: i32 = data.iter().filter(|&&x| x % 2 == 0).map(|&x| x * x).sum();
    println!("Sum of squares of evens: {}", sum_squares);

    let max = data.iter().copied().max().unwrap_or(0);
    let min = data.iter().copied().min().unwrap_or(0);
    println!("Max: {}, Min: {}", max, min);

    let mut freq: HashMap<i32, usize> = HashMap::new();
    data.iter().for_each(|&x| *freq.entry(x).or_insert(0) += 1);
    let mut freq_vec: Vec<_> = freq.iter().collect();
    freq_vec.sort_by(|a, b| b.1.cmp(a.1));
    println!("Most frequent: {} (appears {} times)", freq_vec[0].0, freq_vec[0].1);

    let running_max: Vec<i32> = data.iter()
        .scan(i32::MIN, |m, &x| { *m = (*m).max(x); Some(*m) })
        .collect();
    println!("Running max: {:?}", running_max);
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Rust Algorithms Demo =====\n");

    // Searching
    println!("--- Binary Search ---");
    let sorted = vec![2, 5, 8, 12, 16, 23, 38, 56, 72, 91];
    println!("Search for 23: {:?}", binary_search(&sorted, &23));
    println!("Search for 99: {:?}", binary_search(&sorted, &99));
    println!("std binary_search for 56: {:?}", sorted.binary_search(&56));

    // Sorting
    println!("\n--- Sorting ---");
    let data = vec![64, 34, 25, 12, 22, 11, 90];
    println!("Original:   {:?}", data);

    let mut ms = data.clone(); merge_sort(&mut ms);
    println!("Merge Sort: {:?}", ms);

    let mut qs = data.clone(); quick_sort(&mut qs);
    println!("Quick Sort: {:?}", qs);

    let mut ss = data.clone(); ss.sort_unstable();
    println!("sort_unstable: {:?}", ss);

    // Graph
    println!("\n--- Graph BFS & DFS ---");
    let mut graph: HashMap<&str, Vec<&str>> = HashMap::new();
    graph.insert("A", vec!["B", "C"]);
    graph.insert("B", vec!["D", "E"]);
    graph.insert("C", vec!["F"]);
    graph.insert("D", vec![]);
    graph.insert("E", vec![]);
    graph.insert("F", vec![]);
    println!("BFS from A: {:?}", bfs(&graph, "A"));
    println!("DFS from A: {:?}", dfs(&graph, "A"));

    // Dynamic Programming
    println!("\n--- Dynamic Programming ---");
    let mut memo = HashMap::new();
    let fibs: Vec<u64> = (0..=10).map(|i| fib(i, &mut memo)).collect();
    println!("Fibonacci(0..10): {:?}", fibs);
    println!("LCS(\"ABCBDAB\",\"BDCAB\"): {}", lcs("ABCBDAB", "BDCAB"));
    println!("Coin change(11, [1,5,6,9]): {:?}", coin_change(&[1,5,6,9], 11));

    // Iterator algorithms
    println!("\n--- Iterator Algorithms ---");
    iterator_demos();

    println!("\n✅ All algorithm demos complete!");
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test] fn test_binary_search_found()     { assert_eq!(binary_search(&[1,3,5,7,9], &5), Some(2)); }
    #[test] fn test_binary_search_not_found() { assert_eq!(binary_search(&[1,3,5], &4), None); }
    #[test] fn test_merge_sort() { let mut v = vec![3,1,4,1,5]; merge_sort(&mut v); assert_eq!(v, vec![1,1,3,4,5]); }
    #[test] fn test_quick_sort() { let mut v = vec![9,3,6,1,8]; quick_sort(&mut v); assert_eq!(v, vec![1,3,6,8,9]); }
    #[test] fn test_lcs() { assert_eq!(lcs("ABCBDAB","BDCAB"), 4); }
    #[test] fn test_coin_change() { assert_eq!(coin_change(&[1,5,6,9], 11), Some(2)); }
    #[test] fn test_coin_impossible() { assert_eq!(coin_change(&[2], 3), None); }
}
