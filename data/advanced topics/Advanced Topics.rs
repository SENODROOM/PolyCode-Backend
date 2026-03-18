// ============================================================
//  Module 13: Advanced Topics
//  Lifetimes, Smart Pointers, Macros, Iterators
// ============================================================

use std::cell::RefCell;
use std::collections::HashMap;
use std::rc::Rc;

fn main() {
    println!("===== Module 13: Advanced Topics =====\n");
    lifetimes_demo();
    smart_pointers_demo();
    macros_demo();
    advanced_iterators_demo();
}

// ─────────────────────────────────────────────
// LIFETIMES
// ─────────────────────────────────────────────

struct StrSplitter<'a> {
    remainder: &'a str,
    delimiter: char,
}

impl<'a> StrSplitter<'a> {
    fn new(s: &'a str, delim: char) -> Self { Self { remainder: s, delimiter: delim } }
}

impl<'a> Iterator for StrSplitter<'a> {
    type Item = &'a str;
    fn next(&mut self) -> Option<&'a str> {
        if self.remainder.is_empty() { return None; }
        if let Some(pos) = self.remainder.find(self.delimiter) {
            let token = &self.remainder[..pos];
            self.remainder = &self.remainder[pos + self.delimiter.len_utf8()..];
            Some(token)
        } else {
            let last = self.remainder;
            self.remainder = "";
            Some(last)
        }
    }
}

// Struct with lifetime — holds reference into external data
struct Config<'a> {
    source: &'a str,
    values: HashMap<&'a str, &'a str>,
}

impl<'a> Config<'a> {
    fn parse(source: &'a str) -> Self {
        let mut values = HashMap::new();
        for line in source.lines() {
            let parts: Vec<&'a str> = line.splitn(2, '=').collect();
            if parts.len() == 2 { values.insert(parts[0].trim(), parts[1].trim()); }
        }
        Config { source, values }
    }
    fn get(&self, key: &str) -> Option<&&str> { self.values.get(key) }
}

fn lifetimes_demo() {
    println!("--- Lifetimes ---");

    // Custom splitting iterator that borrows its input
    let csv = "red,green,blue,yellow,purple";
    let parts: Vec<&str> = StrSplitter::new(csv, ',').collect();
    println!("Split '{}': {:?}", csv, parts);

    // Lifetime in struct — references the original string
    let source = "host=localhost\nport=8080\ndebug=true";
    let cfg = Config::parse(source);
    println!("Config host={:?} port={:?}", cfg.get("host"), cfg.get("port"));

    // 'static lifetime
    let greeting: &'static str = "Hello from static memory";
    let owned = String::from("Hello from heap");
    let longer = longest(greeting, &owned);
    println!("Longer: {}", longer);

    println!();
}

fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
    if x.len() >= y.len() { x } else { y }
}

// ─────────────────────────────────────────────
// SMART POINTERS
// ─────────────────────────────────────────────

// Recursive type using Box
#[derive(Debug)]
enum Tree {
    Leaf(i32),
    Node(Box<Tree>, Box<Tree>),
}

impl Tree {
    fn sum(&self) -> i32 {
        match self {
            Tree::Leaf(v)       => *v,
            Tree::Node(l, r)    => l.sum() + r.sum(),
        }
    }
    fn depth(&self) -> usize {
        match self {
            Tree::Leaf(_)    => 1,
            Tree::Node(l, r) => 1 + l.depth().max(r.depth()),
        }
    }
}

// Shared ownership with Rc
#[derive(Debug)]
struct SharedData { name: String, count: u32 }

fn smart_pointers_demo() {
    println!("--- Smart Pointers ---");

    // Box<T>
    let b = Box::new(42);
    println!("Box: {} (on heap, auto-deref)", *b);

    // Box for recursive type
    let tree = Tree::Node(
        Box::new(Tree::Node(
            Box::new(Tree::Leaf(1)),
            Box::new(Tree::Leaf(2)),
        )),
        Box::new(Tree::Node(
            Box::new(Tree::Leaf(3)),
            Box::new(Tree::Node(
                Box::new(Tree::Leaf(4)),
                Box::new(Tree::Leaf(5)),
            )),
        )),
    );
    println!("Tree sum={} depth={}", tree.sum(), tree.depth());

    // Rc<T> — multiple owners (single thread)
    let shared = Rc::new(SharedData { name: "Config".to_string(), count: 42 });
    let clone1 = Rc::clone(&shared);
    let clone2 = Rc::clone(&shared);
    println!("Rc strong count: {}", Rc::strong_count(&shared));
    println!("All three see: {} count={}", shared.name, clone2.count);
    drop(clone1);
    println!("After drop: strong count = {}", Rc::strong_count(&shared));

    // RefCell<T> — interior mutability
    let data = RefCell::new(vec![1, 2, 3]);
    {
        let mut borrow = data.borrow_mut();
        borrow.push(4);
        borrow.push(5);
    } // mut borrow released
    println!("RefCell data: {:?}", data.borrow());

    // Rc<RefCell<T>> — shared mutable ownership
    let counter = Rc::new(RefCell::new(0));
    let c1 = Rc::clone(&counter);
    let c2 = Rc::clone(&counter);
    *c1.borrow_mut() += 10;
    *c2.borrow_mut() += 20;
    println!("Rc<RefCell> counter = {}", *counter.borrow());

    println!();
}

// ─────────────────────────────────────────────
// MACROS
// ─────────────────────────────────────────────

// Declarative macro
macro_rules! log {
    (INFO,  $msg:expr)            => { println!("[INFO]  {}", $msg); };
    (WARN,  $msg:expr)            => { println!("[WARN]  {}", $msg); };
    (ERROR, $msg:expr)            => { eprintln!("[ERROR] {}", $msg); };
    ($level:ident, $fmt:literal, $($arg:tt)*) => {
        println!(concat!("[", stringify!($level), "] ", $fmt), $($arg)*);
    };
}

macro_rules! map {
    ($($k:expr => $v:expr),* $(,)?) => {{
        let mut m = HashMap::new();
        $(m.insert($k, $v);)*
        m
    }};
}

macro_rules! assert_approx {
    ($left:expr, $right:expr, $tol:expr) => {
        let diff = ($left - $right).abs();
        assert!(diff < $tol,
            "assert_approx failed: |{} - {}| = {} >= {}",
            $left, $right, diff, $tol);
    };
}

macro_rules! vec_of_strings {
    ($($s:expr),* $(,)?) => {
        vec![$($s.to_string()),*]
    };
}

fn macros_demo() {
    println!("--- Macros ---");

    log!(INFO,  "Application started");
    log!(WARN,  "Low memory");
    log!(INFO,  "Processing {} items", 42);

    let settings = map![
        "host"    => "localhost",
        "port"    => "8080",
        "debug"   => "true",
    ];
    println!("map! result: {:?}", settings);

    let names = vec_of_strings!["Alice", "Bob", "Charlie"];
    println!("vec_of_strings: {:?}", names);

    assert_approx!(3.14159, std::f64::consts::PI, 0.0001);
    println!("assert_approx! passed for PI");

    println!();
}

// ─────────────────────────────────────────────
// ADVANCED ITERATORS
// ─────────────────────────────────────────────

fn is_prime(n: u64) -> bool {
    if n < 2 { return false; }
    if n == 2 { return true; }
    if n % 2 == 0 { return false; }
    let mut i = 3;
    while i * i <= n { if n % i == 0 { return false; } i += 2; }
    true
}

struct Primes { current: u64 }
impl Primes { fn new() -> Self { Primes { current: 1 } } }
impl Iterator for Primes {
    type Item = u64;
    fn next(&mut self) -> Option<u64> {
        loop {
            self.current += 1;
            if is_prime(self.current) { return Some(self.current); }
        }
    }
}

struct Fibonacci { a: u64, b: u64 }
impl Fibonacci { fn new() -> Self { Fibonacci { a: 0, b: 1 } } }
impl Iterator for Fibonacci {
    type Item = u64;
    fn next(&mut self) -> Option<u64> {
        let next = self.a;
        self.a = self.b;
        self.b = next + self.b;
        Some(next)
    }
}

fn advanced_iterators_demo() {
    println!("--- Advanced Iterators ---");

    // Infinite prime iterator
    let primes_10: Vec<u64> = Primes::new().take(10).collect();
    println!("First 10 primes: {:?}", primes_10);

    let prime_sum_100: u64 = Primes::new().take_while(|&p| p < 100).sum();
    println!("Sum of primes < 100: {}", prime_sum_100);

    // Fibonacci
    let fibs: Vec<u64> = Fibonacci::new().take(12).collect();
    println!("First 12 Fibonacci: {:?}", fibs);

    let first_fib_over_1000 = Fibonacci::new().find(|&f| f > 1000);
    println!("First Fibonacci > 1000: {:?}", first_fib_over_1000);

    // scan — running statistics
    let data = vec![4.0, 7.0, 2.0, 9.0, 5.0, 1.0, 8.0, 3.0, 6.0];
    let running_max: Vec<f64> = data.iter()
        .scan(f64::NEG_INFINITY, |max, &x| { *max = max.max(x); Some(*max) })
        .collect();
    println!("Running max: {:?}", running_max);

    let running_avg: Vec<f64> = data.iter().enumerate()
        .scan(0.0, |sum, (i, &x)| { *sum += x; Some(*sum / (i + 1) as f64) })
        .collect();
    let rounded: Vec<f64> = running_avg.iter().map(|x| (x * 100.0).round() / 100.0).collect();
    println!("Running avg: {:?}", rounded);

    // zip + unzip
    let names = vec!["Alice", "Bob", "Carol"];
    let scores = vec![95, 87, 92];
    let combined: Vec<_> = names.iter().zip(scores.iter()).collect();
    println!("Zipped: {:?}", combined);

    // chain multiple iterators
    let a = 1..=3;
    let b = 7..=9;
    let c = vec![50, 51];
    let chained: Vec<i32> = a.chain(b).chain(c.into_iter()).collect();
    println!("Chained: {:?}", chained);

    // cycle — repeat forever
    let pattern: Vec<&str> = ["A","B","C"].iter().copied().cycle().take(10).collect();
    println!("Cycle: {:?}", pattern);

    // flat_map
    let words = vec!["Hello World", "Rust Programming", "is fun"];
    let all_words: Vec<&str> = words.iter().flat_map(|s| s.split_whitespace()).collect();
    println!("flat_map words: {:?}", all_words);

    // Complex pipeline
    let result: HashMap<char, usize> = "Hello World Rust"
        .chars()
        .filter(|c| c.is_alphabetic())
        .map(|c| c.to_ascii_lowercase())
        .fold(HashMap::new(), |mut acc, c| { *acc.entry(c).or_insert(0) += 1; acc });
    let mut sorted: Vec<(char, usize)> = result.into_iter().collect();
    sorted.sort_by(|a, b| b.1.cmp(&a.1).then(a.0.cmp(&b.0)));
    println!("Char freq (top 5): {:?}", &sorted[..5]);

    println!("\n✅ Module 13 complete!");
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_tree_sum()   { let t = Tree::Node(Box::new(Tree::Leaf(3)), Box::new(Tree::Leaf(4))); assert_eq!(t.sum(), 7); }
    #[test] fn test_primes()     { let p: Vec<u64> = Primes::new().take(5).collect(); assert_eq!(p, vec![2,3,5,7,11]); }
    #[test] fn test_fibonacci()  { let f: Vec<u64> = Fibonacci::new().take(7).collect(); assert_eq!(f, vec![0,1,1,2,3,5,8]); }
    #[test] fn test_str_splitter() {
        let parts: Vec<&str> = StrSplitter::new("a,b,c", ',').collect();
        assert_eq!(parts, vec!["a", "b", "c"]);
    }
}
