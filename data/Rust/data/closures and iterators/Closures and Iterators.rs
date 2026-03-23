// ============================================================
//  Rust Closures & Iterators — Complete Examples
// ============================================================

use std::collections::HashMap;

// ─────────────────────────────────────────────
// SECTION 1: Basic Closures
// ─────────────────────────────────────────────

fn closure_basics() {
    println!("--- Basic Closures ---");

    let add      = |a, b| a + b;
    let square   = |x: i32| x * x;
    let greet    = |name: &str| format!("Hello, {}!", name);

    println!("add(3, 4)     = {}", add(3, 4));
    println!("square(7)     = {}", square(7));
    println!("greet(\"Bob\") = {}", greet("Bob"));

    // Capturing environment
    let base = 10;
    let add_base = |x| x + base;
    println!("add_base(5)   = {} (base={})", add_base(5), base);
    println!("base still accessible: {}", base); // not moved

    // move closure
    let prefix = String::from("LOG");
    let logger = move |msg: &str| println!("[{}] {}", prefix, msg);
    logger("Application started");
    logger("Processing complete");
    // prefix no longer usable here — it was moved
}

// ─────────────────────────────────────────────
// SECTION 2: Closures as Arguments
// ─────────────────────────────────────────────

fn apply<F: Fn(i32) -> i32>(f: F, x: i32) -> i32 { f(x) }
fn apply_twice<F: Fn(i32) -> i32>(f: F, x: i32) -> i32 { f(f(x)) }
fn apply_mut<F: FnMut() -> i32>(mut f: F, times: usize) -> Vec<i32> {
    (0..times).map(|_| f()).collect()
}
fn apply_once<F: FnOnce() -> String>(f: F) -> String { f() }

fn closures_as_args() {
    println!("\n--- Closures as Arguments ---");

    println!("apply(|x| x*3, 5)         = {}", apply(|x| x * 3, 5));
    println!("apply_twice(|x| x+3, 10)  = {}", apply_twice(|x| x + 3, 10));

    // FnMut — counter closure
    let mut count = 0;
    let counter = || { count += 1; count };
    let results = apply_mut(counter, 5);
    println!("FnMut counter x5:          {:?}", results);

    // FnOnce — moves a value out
    let greeting = String::from("Welcome to Rust!");
    let once_fn  = || greeting; // moves greeting
    println!("FnOnce result:             {}", apply_once(once_fn));
}

// ─────────────────────────────────────────────
// SECTION 3: Returning Closures
// ─────────────────────────────────────────────

fn make_adder(n: i32) -> impl Fn(i32) -> i32 { move |x| x + n }
fn make_multiplier(n: i32) -> impl Fn(i32) -> i32 { move |x| x * n }
fn make_between(lo: i32, hi: i32) -> impl Fn(i32) -> bool { move |x| x >= lo && x <= hi }

fn returning_closures() {
    println!("\n--- Returning Closures ---");

    let add5    = make_adder(5);
    let triple  = make_multiplier(3);
    let is_teen = make_between(13, 19);

    println!("add5(10)     = {}", add5(10));
    println!("triple(7)    = {}", triple(7));
    println!("is_teen(15)  = {}", is_teen(15));
    println!("is_teen(25)  = {}", is_teen(25));

    // Pipeline of closures
    let pipeline: Vec<Box<dyn Fn(i32) -> i32>> = vec![
        Box::new(|x| x + 1),
        Box::new(|x| x * 2),
        Box::new(|x| x - 3),
    ];
    let result = pipeline.iter().fold(5, |acc, f| f(acc));
    println!("Pipeline(5): +1 *2 -3 = {}", result); // ((5+1)*2)-3 = 9
}

// ─────────────────────────────────────────────
// SECTION 4: Iterator Adapters
// ─────────────────────────────────────────────

fn iterator_adapters() {
    println!("\n--- Iterator Adapters ---");

    let numbers = vec![1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    // map
    let doubled: Vec<i32> = numbers.iter().map(|&x| x * 2).collect();
    println!("map (*2):         {:?}", doubled);

    // filter
    let evens: Vec<&i32> = numbers.iter().filter(|&&x| x % 2 == 0).collect();
    println!("filter (evens):   {:?}", evens);

    // filter_map
    let words = vec!["1", "two", "3", "four", "5"];
    let parsed: Vec<i32> = words.iter().filter_map(|s| s.parse().ok()).collect();
    println!("filter_map parse: {:?}", parsed);

    // flat_map / flatten
    let nested = vec![vec![1, 2, 3], vec![4, 5], vec![6, 7, 8, 9]];
    let flat: Vec<i32> = nested.into_iter().flatten().collect();
    println!("flatten:          {:?}", flat);

    // take / skip
    let first3: Vec<i32> = numbers.iter().copied().take(3).collect();
    let skip3:  Vec<i32> = numbers.iter().copied().skip(7).collect();
    println!("take(3):          {:?}", first3);
    println!("skip(7):          {:?}", skip3);

    // enumerate
    println!("enumerate:");
    for (i, val) in numbers.iter().take(4).enumerate() {
        println!("  [{}] = {}", i, val);
    }

    // zip
    let letters = vec!['a', 'b', 'c', 'd', 'e'];
    let zipped: Vec<(char, i32)> = letters.into_iter()
        .zip(numbers.iter().copied())
        .collect();
    println!("zip:              {:?}", &zipped[..3]);

    // chain
    let chained: Vec<i32> = (1..=3).chain(8..=10).collect();
    println!("chain:            {:?}", chained);

    // scan — running total
    let running: Vec<i32> = numbers.iter()
        .scan(0, |acc, &x| { *acc += x; Some(*acc) })
        .collect();
    println!("scan (running sum): {:?}", running);

    // windows & chunks
    let chunks: Vec<&[i32]> = numbers.chunks(3).collect();
    println!("chunks(3):        {:?}", chunks);
}

// ─────────────────────────────────────────────
// SECTION 5: Iterator Consumers
// ─────────────────────────────────────────────

fn iterator_consumers() {
    println!("\n--- Iterator Consumers ---");

    let data = vec![3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5];

    println!("sum:       {}", data.iter().sum::<i32>());
    println!("product:   {}", data.iter().product::<i32>());
    println!("count:     {}", data.iter().count());
    println!("max:       {:?}", data.iter().max());
    println!("min:       {:?}", data.iter().min());
    println!("any > 8:   {}", data.iter().any(|&x| x > 8));
    println!("all > 0:   {}", data.iter().all(|&x| x > 0));
    println!("find > 4:  {:?}", data.iter().find(|&&x| x > 4));
    println!("position 9:{:?}", data.iter().position(|&x| x == 9));

    // fold — custom reduction
    let concat = data.iter().fold(String::new(), |mut s, x| {
        if !s.is_empty() { s.push(','); }
        s.push_str(&x.to_string());
        s
    });
    println!("fold concat: {}", concat);

    // collect into HashMap (frequency count)
    let mut freq: HashMap<i32, usize> = HashMap::new();
    data.iter().for_each(|&x| *freq.entry(x).or_insert(0) += 1);
    let mut freq_vec: Vec<_> = freq.iter().collect();
    freq_vec.sort_by(|a, b| b.1.cmp(a.1).then(a.0.cmp(b.0)));
    println!("frequency top3: {:?}", &freq_vec[..3]);

    // partition — split into two Vecs
    let (evens, odds): (Vec<i32>, Vec<i32>) = data.iter().copied().partition(|x| x % 2 == 0);
    println!("evens: {:?}", evens);
    println!("odds:  {:?}", odds);

    // unzip
    let pairs = vec![(1, 'a'), (2, 'b'), (3, 'c')];
    let (nums, chars): (Vec<i32>, Vec<char>) = pairs.into_iter().unzip();
    println!("unzip nums:  {:?}", nums);
    println!("unzip chars: {:?}", chars);
}

// ─────────────────────────────────────────────
// SECTION 6: Custom Iterator
// ─────────────────────────────────────────────

struct Fibonacci { a: u64, b: u64 }

impl Fibonacci {
    fn new() -> Self { Fibonacci { a: 0, b: 1 } }
}

impl Iterator for Fibonacci {
    type Item = u64;
    fn next(&mut self) -> Option<u64> {
        let next = self.a;
        self.a   = self.b;
        self.b   = next + self.b;
        Some(next)
    }
}

fn custom_iterator() {
    println!("\n--- Custom Iterator (Fibonacci) ---");

    // First 10 Fibonacci numbers
    let fibs: Vec<u64> = Fibonacci::new().take(10).collect();
    println!("First 10: {:?}", fibs);

    // Sum of first 20 Fibonacci numbers
    let sum: u64 = Fibonacci::new().take(20).sum();
    println!("Sum of first 20: {}", sum);

    // First Fibonacci number > 1000
    let first_big = Fibonacci::new().find(|&x| x > 1000);
    println!("First > 1000: {:?}", first_big);

    // Even Fibonacci numbers under 100
    let even_fibs: Vec<u64> = Fibonacci::new()
        .take_while(|&x| x < 100)
        .filter(|x| x % 2 == 0)
        .collect();
    println!("Even Fibs < 100: {:?}", even_fibs);
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Rust Closures & Iterators =====\n");
    closure_basics();
    closures_as_args();
    returning_closures();
    iterator_adapters();
    iterator_consumers();
    custom_iterator();
    println!("\n✅ All closure & iterator demos complete!");
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test] fn test_make_adder()      { assert_eq!(make_adder(5)(10), 15); }
    #[test] fn test_make_multiplier() { assert_eq!(make_multiplier(3)(7), 21); }
    #[test] fn test_make_between()    { assert!(make_between(1,10)(5)); assert!(!make_between(1,10)(11)); }

    #[test]
    fn test_fibonacci_iterator() {
        let fibs: Vec<u64> = Fibonacci::new().take(8).collect();
        assert_eq!(fibs, vec![0, 1, 1, 2, 3, 5, 8, 13]);
    }

    #[test]
    fn test_apply_twice() {
        assert_eq!(apply_twice(|x| x * 2, 3), 12);
    }
}
