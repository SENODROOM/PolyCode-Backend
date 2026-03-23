// ============================================================
//  Module 12: Testing in Rust
//  Run: cargo test -- --nocapture
// ============================================================

use std::collections::HashMap;

// ─────────────────────────────────────────────
// CODE UNDER TEST
// ─────────────────────────────────────────────

pub fn add(a: i32, b: i32) -> i32 { a + b }
pub fn subtract(a: i32, b: i32) -> i32 { a - b }
pub fn multiply(a: i32, b: i32) -> i32 { a * b }
pub fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 { Err("Division by zero".into()) } else { Ok(a / b) }
}
pub fn factorial(n: u64) -> Result<u64, String> {
    if n > 20 { return Err(format!("Input {} too large", n)); }
    Ok((1..=n).product())
}
pub fn is_palindrome(s: &str) -> bool {
    let clean: String = s.chars().filter(|c| c.is_alphanumeric()).map(|c| c.to_lowercase().next().unwrap()).collect();
    clean.chars().eq(clean.chars().rev())
}

#[derive(Debug, Clone, PartialEq)]
pub struct ShoppingCart {
    items: HashMap<String, (f64, u32)>, // name → (price, qty)
}

impl ShoppingCart {
    pub fn new() -> Self { Self { items: HashMap::new() } }

    pub fn add_item(&mut self, name: &str, price: f64, qty: u32) -> Result<(), String> {
        if price < 0.0 { return Err(format!("Price cannot be negative: {}", price)); }
        if qty == 0    { return Err("Quantity must be at least 1".into()); }
        let entry = self.items.entry(name.to_string()).or_insert((price, 0));
        entry.1 += qty;
        Ok(())
    }

    pub fn remove_item(&mut self, name: &str) -> Result<(), String> {
        if self.items.remove(name).is_none() { return Err(format!("'{}' not in cart", name)); }
        Ok(())
    }

    pub fn total(&self) -> f64 {
        self.items.values().map(|(price, qty)| price * *qty as f64).sum()
    }

    pub fn item_count(&self) -> u32 {
        self.items.values().map(|(_, qty)| qty).sum()
    }

    pub fn apply_discount(&mut self, pct: f64) -> Result<(), String> {
        if pct < 0.0 || pct > 100.0 { return Err(format!("Invalid discount {}%", pct)); }
        for (price, _) in self.items.values_mut() { *price *= 1.0 - pct / 100.0; }
        Ok(())
    }
}

pub struct TextProcessor;

impl TextProcessor {
    pub fn word_count(text: &str) -> usize {
        text.split_whitespace().count()
    }

    pub fn char_frequency(text: &str) -> HashMap<char, usize> {
        let mut freq = HashMap::new();
        for c in text.chars().filter(|c| !c.is_whitespace()) {
            *freq.entry(c.to_lowercase().next().unwrap()).or_insert(0) += 1;
        }
        freq
    }

    pub fn most_common_word(text: &str) -> Option<String> {
        let mut freq: HashMap<&str, usize> = HashMap::new();
        for word in text.split_whitespace() { *freq.entry(word).or_insert(0) += 1; }
        freq.into_iter().max_by_key(|(_, v)| *v).map(|(k, _)| k.to_string())
    }

    pub fn truncate(text: &str, max_len: usize) -> String {
        if text.len() <= max_len { return text.to_string(); }
        format!("{}...", &text[..max_len.saturating_sub(3)])
    }
}

// ─────────────────────────────────────────────
// MAIN — shows what's testable
// ─────────────────────────────────────────────

fn main() {
    println!("===== Module 12: Testing =====");
    println!("Run: cargo test -- --nocapture\n");

    println!("add(2,3) = {}", add(2, 3));
    println!("factorial(5) = {:?}", factorial(5));
    println!("is_palindrome(\"racecar\") = {}", is_palindrome("racecar"));

    let mut cart = ShoppingCart::new();
    cart.add_item("Apple", 1.50, 3).unwrap();
    cart.add_item("Bread", 2.99, 1).unwrap();
    println!("Cart total: ${:.2}", cart.total());

    let text = "the quick brown fox jumps over the lazy dog the fox";
    println!("Word count: {}", TextProcessor::word_count(text));
    println!("Most common: {:?}", TextProcessor::most_common_word(text));
}

// ─────────────────────────────────────────────
// TESTS
// ─────────────────────────────────────────────

#[cfg(test)]
mod tests {
    use super::*;

    // ── Math ──────────────────────────────────
    mod math_tests {
        use super::*;

        #[test] fn add_positive()          { assert_eq!(add(3, 4), 7); }
        #[test] fn add_negative()          { assert_eq!(add(-3, -4), -7); }
        #[test] fn add_mixed()             { assert_eq!(add(-5, 10), 5); }
        #[test] fn add_zero()              { assert_eq!(add(0, 0), 0); }
        #[test] fn subtract_basic()        { assert_eq!(subtract(10, 3), 7); }
        #[test] fn multiply_basic()        { assert_eq!(multiply(6, 7), 42); }
        #[test] fn multiply_by_zero()      { assert_eq!(multiply(999, 0), 0); }

        #[test]
        fn divide_ok() {
            let result = divide(10.0, 4.0).unwrap();
            assert!((result - 2.5).abs() < 1e-10, "Expected 2.5, got {}", result);
        }

        #[test] fn divide_by_zero_is_err() { assert!(divide(10.0, 0.0).is_err()); }
        #[test] fn divide_error_message()  { assert_eq!(divide(1.0, 0.0).unwrap_err(), "Division by zero"); }

        #[test] fn factorial_zero()        { assert_eq!(factorial(0).unwrap(), 1); }
        #[test] fn factorial_one()         { assert_eq!(factorial(1).unwrap(), 1); }
        #[test] fn factorial_five()        { assert_eq!(factorial(5).unwrap(), 120); }
        #[test] fn factorial_ten()         { assert_eq!(factorial(10).unwrap(), 3_628_800); }
        #[test] fn factorial_too_large()   { assert!(factorial(21).is_err()); }
    }

    // ── Palindrome ────────────────────────────
    mod palindrome_tests {
        use super::*;

        #[test] fn simple_palindrome()     { assert!(is_palindrome("racecar")); }
        #[test] fn not_palindrome()        { assert!(!is_palindrome("hello")); }
        #[test] fn single_char()           { assert!(is_palindrome("a")); }
        #[test] fn empty_string()          { assert!(is_palindrome("")); }
        #[test] fn case_insensitive()      { assert!(is_palindrome("RaceCar")); }
        #[test] fn with_spaces()           { assert!(is_palindrome("A man a plan a canal Panama")); }
        #[test] fn with_punctuation()      { assert!(is_palindrome("Was it a car or a cat I saw?")); }
        #[test] fn numbers()               { assert!(is_palindrome("12321")); }
    }

    // ── ShoppingCart ──────────────────────────
    mod cart_tests {
        use super::*;

        fn make_cart() -> ShoppingCart {
            let mut cart = ShoppingCart::new();
            cart.add_item("Apple",  1.50, 3).unwrap();
            cart.add_item("Bread",  2.99, 1).unwrap();
            cart.add_item("Milk",   3.49, 2).unwrap();
            cart
        }

        #[test]
        fn new_cart_is_empty() {
            let cart = ShoppingCart::new();
            assert_eq!(cart.total(), 0.0);
            assert_eq!(cart.item_count(), 0);
        }

        #[test]
        fn add_and_total() {
            let cart = make_cart();
            let expected = 1.50 * 3.0 + 2.99 + 3.49 * 2.0;
            assert!((cart.total() - expected).abs() < 0.001, "Expected {:.2}, got {:.2}", expected, cart.total());
        }

        #[test]
        fn item_count() {
            let cart = make_cart();
            assert_eq!(cart.item_count(), 6); // 3 + 1 + 2
        }

        #[test]
        fn add_same_item_accumulates() {
            let mut cart = ShoppingCart::new();
            cart.add_item("Apple", 1.50, 2).unwrap();
            cart.add_item("Apple", 1.50, 3).unwrap();
            assert_eq!(cart.item_count(), 5);
        }

        #[test]
        fn negative_price_rejected() {
            let mut cart = ShoppingCart::new();
            assert!(cart.add_item("Bad", -1.0, 1).is_err());
        }

        #[test]
        fn zero_quantity_rejected() {
            let mut cart = ShoppingCart::new();
            assert!(cart.add_item("Bad", 1.0, 0).is_err());
        }

        #[test]
        fn remove_existing_item() {
            let mut cart = make_cart();
            let before = cart.item_count();
            cart.remove_item("Bread").unwrap();
            assert_eq!(cart.item_count(), before - 1);
        }

        #[test]
        fn remove_missing_item_errors() {
            let mut cart = make_cart();
            assert!(cart.remove_item("Unicorn").is_err());
        }

        #[test]
        fn apply_discount() {
            let mut cart = ShoppingCart::new();
            cart.add_item("Widget", 10.0, 1).unwrap();
            let before = cart.total();
            cart.apply_discount(20.0).unwrap();
            assert!((cart.total() - before * 0.8).abs() < 0.001);
        }

        #[test]
        fn invalid_discount_rejected() {
            let mut cart = make_cart();
            assert!(cart.apply_discount(-5.0).is_err());
            assert!(cart.apply_discount(101.0).is_err());
        }
    }

    // ── TextProcessor ─────────────────────────
    mod text_tests {
        use super::*;

        #[test] fn word_count_normal()   { assert_eq!(TextProcessor::word_count("hello world"),    2); }
        #[test] fn word_count_empty()    { assert_eq!(TextProcessor::word_count(""),               0); }
        #[test] fn word_count_spaces()   { assert_eq!(TextProcessor::word_count("  a  b   c  "),   3); }

        #[test]
        fn char_frequency() {
            let freq = TextProcessor::char_frequency("aab");
            assert_eq!(freq[&'a'], 2);
            assert_eq!(freq[&'b'], 1);
            assert!(!freq.contains_key(&' '));
        }

        #[test]
        fn most_common_word() {
            let text = "the fox the fox the";
            assert_eq!(TextProcessor::most_common_word(text), Some("the".to_string()));
        }

        #[test] fn truncate_short()  { assert_eq!(TextProcessor::truncate("Hi",     10), "Hi"); }
        #[test] fn truncate_long()   { assert_eq!(TextProcessor::truncate("Hello World", 8), "Hello..."); }
        #[test] fn truncate_exact()  { assert_eq!(TextProcessor::truncate("12345",   5), "12345"); }
    }

    // ── should_panic ──────────────────────────
    #[test]
    #[should_panic]
    fn vec_out_of_bounds() {
        let v = vec![1, 2, 3];
        let _ = v[10]; // panics
    }

    #[test]
    #[should_panic(expected = "attempt to divide by zero")]
    fn integer_divide_by_zero() {
        let _ = 5_i32 / 0;
    }

    // ── Result-returning test ─────────────────
    #[test]
    fn test_divide_result() -> Result<(), String> {
        let result = divide(10.0, 2.0)?;
        assert!((result - 5.0).abs() < 1e-10);
        Ok(())
    }

    // ── Ignored (expensive) ───────────────────
    #[test]
    #[ignore]
    fn expensive_test() {
        let sum: u64 = (1..=1_000_000).sum();
        assert_eq!(sum, 500_000_500_000);
    }
}
