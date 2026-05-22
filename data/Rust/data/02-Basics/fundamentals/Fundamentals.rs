// ============================================================
//  Module 02: Rust Fundamentals
//  Variables, Types, Control Flow, Functions
// ============================================================

fn main() {
    println!("===== Module 02: Fundamentals =====\n");

    // ── Variables & Mutability ───────────────────────────────
    println!("--- Variables ---");
    let x = 5;
    println!("Immutable x = {}", x);

    let mut y = 10;
    y += 5;
    println!("Mutable   y = {}", y);

    // Shadowing — can change type
    let spaces = "   ";
    let spaces = spaces.len();
    println!("Shadowed spaces (len) = {}", spaces);

    const MAX_SCORE: u32 = 1_000_000;
    println!("Constant MAX_SCORE = {}", MAX_SCORE);

    // ── Data Types ───────────────────────────────────────────
    println!("\n--- Data Types ---");
    let i: i32   = -2_147_483_648;
    let u: u32   = 4_294_967_295;
    let f: f64   = 3.141_592_653_589_793;
    let b: bool  = true;
    let c: char  = '🦀';

    println!("i32 min: {}", i);
    println!("u32 max: {}", u);
    println!("f64 pi:  {:.6}", f);
    println!("bool:    {}", b);
    println!("char:    {}", c);

    // Numeric operations
    println!("\n--- Numeric Ops ---");
    let (a, b2) = (17_i32, 5_i32);
    println!("{} + {} = {}", a, b2, a + b2);
    println!("{} - {} = {}", a, b2, a - b2);
    println!("{} * {} = {}", a, b2, a * b2);
    println!("{} / {} = {}", a, b2, a / b2);  // integer division
    println!("{} % {} = {}", a, b2, a % b2);
    println!("2^10 = {}", 2_i32.pow(10));
    println!("abs(-42) = {}", (-42_i32).abs());
    println!("sqrt(144.0) = {}", f64::sqrt(144.0));

    // Tuples
    println!("\n--- Tuples ---");
    let person: (&str, u32, f64) = ("Alice", 30, 5.6);
    println!("Name: {}, Age: {}, Height: {}", person.0, person.1, person.2);
    let (name, age, height) = person;
    println!("Destructured: {} {} {}", name, age, height);

    // Arrays
    println!("\n--- Arrays ---");
    let primes: [u32; 8] = [2, 3, 5, 7, 11, 13, 17, 19];
    println!("Primes: {:?}", primes);
    println!("First: {}, Last: {}", primes[0], primes[primes.len() - 1]);
    println!("Sum: {}", primes.iter().sum::<u32>());

    let matrix = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];
    println!("Matrix center: {}", matrix[1][1]);

    // ── Control Flow ─────────────────────────────────────────
    println!("\n--- Control Flow ---");
    let n = 42;
    let label = if n % 2 == 0 { "even" } else { "odd" };
    println!("{} is {}", n, label);

    // Grade classifier
    let score = 87;
    let grade = if score >= 90 { "A" }
                else if score >= 80 { "B" }
                else if score >= 70 { "C" }
                else { "F" };
    println!("Score {} → grade {}", score, grade);

    // loop with break value
    let mut counter = 0;
    let loop_result = loop {
        counter += 1;
        if counter == 5 { break counter * counter; }
    };
    println!("Loop result: {}", loop_result);

    // while
    print!("Countdown: ");
    let mut t = 5;
    while t > 0 { print!("{} ", t); t -= 1; }
    println!("Blast off!");

    // for ranges
    print!("Squares: ");
    for i in 1..=6 { print!("{} ", i * i); }
    println!();

    // for with enumerate
    let fruits = ["apple", "banana", "cherry"];
    for (i, fruit) in fruits.iter().enumerate() {
        println!("  {}: {}", i, fruit);
    }

    // nested loops with labels
    'outer: for i in 0..5 {
        for j in 0..5 {
            if i + j > 4 { break 'outer; }
            print!("({},{}) ", i, j);
        }
    }
    println!();

    // ── Functions ────────────────────────────────────────────
    println!("\n--- Functions ---");
    println!("greet: {}", greet("Rustacean"));
    println!("add(7,8): {}", add(7, 8));
    println!("factorial(10): {}", factorial(10));
    println!("is_prime(17): {}", is_prime(17));
    println!("is_prime(18): {}", is_prime(18));

    let data = [5, 2, 8, 1, 9, 3, 7];
    let (min, max) = min_max(&data);
    println!("data={:?}  min={} max={}", data, min, max);

    // ── Strings ──────────────────────────────────────────────
    println!("\n--- Strings ---");
    let s1: &str = "hello";
    let mut s2 = String::from("Hello");
    s2.push(',');
    s2.push_str(" World");
    s2 += "!";
    println!("&str: {}", s1);
    println!("String: {}", s2);
    println!("uppercase: {}", s2.to_uppercase());
    println!("len: {}", s2.len());
    println!("contains 'World': {}", s2.contains("World"));
    println!("replace: {}", s2.replace("World", "Rust"));
    println!("trim: '{}'", "  spaces  ".trim());

    // String slicing
    let hello = &s2[0..5];
    println!("slice [0..5]: {}", hello);

    // split & collect
    let csv = "red,green,blue,yellow";
    let colors: Vec<&str> = csv.split(',').collect();
    println!("colors: {:?}", colors);

    // String formatting
    let formatted = format!("{:>10} | {:<10} | {:^10}", "right", "left", "center");
    println!("{}", formatted);

    println!("\n✅ Module 02 complete!");
}

// ─────────────────────────────────────────────
// Helper functions
// ─────────────────────────────────────────────

fn greet(name: &str) -> String {
    format!("Hello, {}!", name)
}

fn add(a: i32, b: i32) -> i32 {
    a + b // last expression without semicolon = return value
}

fn factorial(n: u64) -> u64 {
    if n <= 1 { 1 } else { n * factorial(n - 1) }
}

fn is_prime(n: u32) -> bool {
    if n < 2 { return false; }
    if n == 2 { return true; }
    if n % 2 == 0 { return false; }
    let mut i = 3;
    while i * i <= n {
        if n % i == 0 { return false; }
        i += 2;
    }
    true
}

fn min_max(arr: &[i32]) -> (i32, i32) {
    let mut min = arr[0];
    let mut max = arr[0];
    for &x in arr {
        if x < min { min = x; }
        if x > max { max = x; }
    }
    (min, max)
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_add()       { assert_eq!(add(3, 4), 7); }
    #[test] fn test_factorial() { assert_eq!(factorial(5), 120); }
    #[test] fn test_prime()     { assert!(is_prime(17)); assert!(!is_prime(18)); }
    #[test] fn test_min_max()   { assert_eq!(min_max(&[3,1,4,1,5,9]), (1, 9)); }
}
