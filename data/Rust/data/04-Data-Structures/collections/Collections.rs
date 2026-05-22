// ============================================================
//  Module 06: Collections — Vec, HashMap, HashSet, VecDeque
// ============================================================

use std::collections::{HashMap, HashSet, VecDeque, BinaryHeap};

fn main() {
    println!("===== Module 06: Collections =====\n");
    vec_demo();
    hashmap_demo();
    hashset_demo();
    deque_and_heap_demo();
    real_world_demo();
}

// ─────────────────────────────────────────────
// VEC
// ─────────────────────────────────────────────

fn vec_demo() {
    println!("--- Vec<T> ---");

    // Creation
    let mut v: Vec<i32> = Vec::new();
    let v2 = vec![10, 20, 30, 40, 50];
    let zeros = vec![0; 5];
    println!("v2: {:?}", v2);
    println!("zeros: {:?}", zeros);

    // Push / pop
    for i in 1..=5 { v.push(i * i); }
    println!("squares: {:?}", v);
    println!("pop: {:?}", v.pop());

    // Access
    println!("first: {:?}", v.first());
    println!("last:  {:?}", v.last());
    println!("v[1]:  {}", v[1]);
    println!("get(10): {:?}", v.get(10)); // safe — returns None

    // Iteration & transformation
    let doubled: Vec<i32> = v.iter().map(|&x| x * 2).collect();
    println!("doubled: {:?}", doubled);
    let evens: Vec<i32>   = v.iter().filter(|&&x| x % 2 == 0).copied().collect();
    println!("evens:   {:?}", evens);

    // Sorting
    let mut words = vec!["banana", "apple", "cherry", "date", "elderberry"];
    words.sort();
    println!("sorted:  {:?}", words);
    words.sort_by_key(|s| s.len());
    println!("by len:  {:?}", words);

    // Dedup
    let mut duped = vec![1,1,2,2,3,3,3,4];
    duped.dedup();
    println!("deduped: {:?}", duped);

    // Flatten
    let nested = vec![vec![1,2], vec![3,4,5], vec![6]];
    let flat: Vec<i32> = nested.into_iter().flatten().collect();
    println!("flat:    {:?}", flat);

    // Windows & chunks
    let data = vec![1,2,3,4,5,6];
    let windows: Vec<&[i32]> = data.windows(3).collect();
    println!("windows(3): {:?}", windows);

    // Stats
    let nums = vec![5, 2, 8, 1, 9, 3, 7, 4, 6];
    println!("sum: {} min: {:?} max: {:?}", nums.iter().sum::<i32>(), nums.iter().min(), nums.iter().max());
    println!();
}

// ─────────────────────────────────────────────
// HASHMAP
// ─────────────────────────────────────────────

fn hashmap_demo() {
    println!("--- HashMap<K, V> ---");

    // Basic usage
    let mut scores: HashMap<&str, i32> = HashMap::new();
    scores.insert("Alice", 95);
    scores.insert("Bob",   87);
    scores.insert("Charlie", 72);
    scores.insert("Diana", 90);

    println!("Alice's score: {:?}", scores.get("Alice"));
    println!("Dave's score:  {:?}", scores.get("Dave").unwrap_or(&0));

    // Entry API — insert if absent
    scores.entry("Eve").or_insert(88);
    scores.entry("Alice").or_insert(0);  // won't change Alice
    println!("After entry: Alice={}, Eve={}", scores["Alice"], scores["Eve"]);

    // Entry API — update value
    *scores.entry("Bob").or_insert(0) += 10;
    println!("Bob +10: {}", scores["Bob"]);

    // Sorted output
    let mut sorted: Vec<(&str, i32)> = scores.iter().map(|(&k,&v)| (k,v)).collect();
    sorted.sort_by(|a, b| b.1.cmp(&a.1));
    println!("\nLeaderboard:");
    for (i, (name, score)) in sorted.iter().enumerate() {
        println!("  {}. {} — {}", i+1, name, score);
    }

    // Build from iterator
    let pairs = vec![("a", 1), ("b", 2), ("c", 3)];
    let map: HashMap<&str, i32> = pairs.into_iter().collect();
    println!("\nFrom iterator: {:?}", map);

    // Word frequency counter
    let text = "the quick brown fox jumps over the lazy dog the fox";
    let mut freq: HashMap<&str, usize> = HashMap::new();
    for word in text.split_whitespace() {
        *freq.entry(word).or_insert(0) += 1;
    }
    let mut freq_sorted: Vec<(&&str, &usize)> = freq.iter().collect();
    freq_sorted.sort_by(|a,b| b.1.cmp(a.1));
    println!("\nWord frequency:");
    for (word, count) in freq_sorted.iter().take(5) {
        println!("  '{}': {}", word, count);
    }
    println!();
}

// ─────────────────────────────────────────────
// HASHSET
// ─────────────────────────────────────────────

fn hashset_demo() {
    println!("--- HashSet<T> ---");

    // Unique elements
    let mut tags: HashSet<&str> = HashSet::new();
    tags.insert("rust");
    tags.insert("programming");
    tags.insert("rust");  // duplicate ignored
    tags.insert("systems");
    println!("tags ({}): contains duplicate removed", tags.len());
    println!("contains 'rust': {}", tags.contains("rust"));

    // Set operations
    let a: HashSet<i32> = vec![1,2,3,4,5].into_iter().collect();
    let b: HashSet<i32> = vec![3,4,5,6,7].into_iter().collect();

    let mut union: Vec<i32>        = a.union(&b).copied().collect();
    let mut inter: Vec<i32>        = a.intersection(&b).copied().collect();
    let mut diff_ab: Vec<i32>      = a.difference(&b).copied().collect();
    let mut sym_diff: Vec<i32>     = a.symmetric_difference(&b).copied().collect();

    union.sort(); inter.sort(); diff_ab.sort(); sym_diff.sort();
    println!("A:            {:?}", { let mut v: Vec<i32> = a.iter().copied().collect(); v.sort(); v });
    println!("B:            {:?}", { let mut v: Vec<i32> = b.iter().copied().collect(); v.sort(); v });
    println!("union:        {:?}", union);
    println!("intersection: {:?}", inter);
    println!("A - B:        {:?}", diff_ab);
    println!("sym_diff:     {:?}", sym_diff);
    println!("A subset B:   {}", a.is_subset(&b));
    println!("A disjoint C: {}", a.is_disjoint(&HashSet::from([10,20,30])));

    // Dedup a Vec using HashSet
    let with_dups = vec![3,1,4,1,5,9,2,6,5,3,5];
    let unique: HashSet<i32> = with_dups.iter().copied().collect();
    println!("\nDeduped: {:?} → {} unique values", with_dups, unique.len());
    println!();
}

// ─────────────────────────────────────────────
// VECDEQUE & BINARYHEAP
// ─────────────────────────────────────────────

fn deque_and_heap_demo() {
    println!("--- VecDeque & BinaryHeap ---");

    // VecDeque — O(1) push/pop both ends
    let mut deque: VecDeque<i32> = VecDeque::new();
    deque.push_back(1);
    deque.push_back(2);
    deque.push_front(0);
    deque.push_front(-1);
    println!("Deque: {:?}", deque);
    println!("pop_front: {:?}", deque.pop_front());
    println!("pop_back:  {:?}", deque.pop_back());
    println!("After: {:?}", deque);

    // BFS with VecDeque
    println!("\nBFS with VecDeque (graph traversal):");
    let graph = vec![
        vec![1, 2],    // 0 → 1, 2
        vec![3],       // 1 → 3
        vec![3, 4],    // 2 → 3, 4
        vec![],        // 3
        vec![],        // 4
    ];
    let mut queue: VecDeque<usize> = VecDeque::from([0]);
    let mut visited = vec![false; graph.len()];
    visited[0] = true;
    let mut order = Vec::new();
    while let Some(node) = queue.pop_front() {
        order.push(node);
        for &neighbor in &graph[node] {
            if !visited[neighbor] { visited[neighbor] = true; queue.push_back(neighbor); }
        }
    }
    println!("  BFS order: {:?}", order);

    // BinaryHeap — max-heap priority queue
    println!("\nBinaryHeap (max-heap):");
    let mut heap = BinaryHeap::from(vec![3, 1, 4, 1, 5, 9, 2, 6]);
    println!("  heap peek (max): {:?}", heap.peek());
    while let Some(top) = heap.pop() {
        print!("{} ", top);
    }
    println!("(sorted descending)");

    // Min-heap via Reverse
    use std::cmp::Reverse;
    let mut min_heap: BinaryHeap<Reverse<i32>> = BinaryHeap::new();
    for &x in &[5, 2, 8, 1, 9, 3] { min_heap.push(Reverse(x)); }
    print!("Min-heap order: ");
    while let Some(Reverse(val)) = min_heap.pop() { print!("{} ", val); }
    println!();
    println!();
}

// ─────────────────────────────────────────────
// REAL-WORLD: Library catalog
// ─────────────────────────────────────────────

#[derive(Debug, Clone)]
struct BookRecord { title: String, author: String, genre: String, year: u16, copies: u32 }

fn real_world_demo() {
    println!("--- Real-World: Library Catalog ---");

    let books = vec![
        BookRecord { title: "Dune".into(),              author: "Frank Herbert".into(), genre: "Sci-Fi".into(),  year: 1965, copies: 3 },
        BookRecord { title: "1984".into(),               author: "George Orwell".into(), genre: "Dystopia".into(),year: 1949, copies: 5 },
        BookRecord { title: "The Hobbit".into(),         author: "J.R.R. Tolkien".into(),genre: "Fantasy".into(), year: 1937, copies: 4 },
        BookRecord { title: "Foundation".into(),         author: "Isaac Asimov".into(), genre: "Sci-Fi".into(),   year: 1951, copies: 2 },
        BookRecord { title: "Brave New World".into(),    author: "Aldous Huxley".into(),genre: "Dystopia".into(), year: 1932, copies: 3 },
        BookRecord { title: "The Name of the Wind".into(),author: "Patrick Rothfuss".into(),genre: "Fantasy".into(),year: 2007,copies: 6},
    ];

    // Group by genre
    let mut by_genre: HashMap<&str, Vec<&BookRecord>> = HashMap::new();
    for book in &books { by_genre.entry(&book.genre).or_default().push(book); }

    println!("Books by genre:");
    let mut genres: Vec<&&str> = by_genre.keys().collect();
    genres.sort();
    for genre in genres {
        println!("  {} ({} books):", genre, by_genre[genre].len());
        for b in &by_genre[genre] { println!("    - {} ({})", b.title, b.year); }
    }

    // Total copies
    let total: u32 = books.iter().map(|b| b.copies).sum();
    println!("\nTotal copies in library: {}", total);

    // Authors as a set (unique)
    let authors: HashSet<&str> = books.iter().map(|b| b.author.as_str()).collect();
    println!("Unique authors: {}", authors.len());

    // Search by title prefix
    let query = "the";
    let results: Vec<&BookRecord> = books.iter()
        .filter(|b| b.title.to_lowercase().starts_with(query))
        .collect();
    println!("\nSearch '{}': {} results", query, results.len());
    for r in results { println!("  {}", r.title); }
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_vec_sum()   { let v = vec![1,2,3,4,5]; assert_eq!(v.iter().sum::<i32>(), 15); }
    #[test] fn test_hashmap()   { let mut m = HashMap::new(); m.insert("a",1); assert_eq!(m["a"], 1); }
    #[test] fn test_hashset_dedup() {
        let v = vec![1,1,2,2,3];
        let s: HashSet<i32> = v.into_iter().collect();
        assert_eq!(s.len(), 3);
    }
}
