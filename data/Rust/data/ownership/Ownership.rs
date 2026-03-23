// ============================================================
//  Module 03: Ownership, Borrowing & Slices
// ============================================================

fn main() {
    println!("===== Module 03: Ownership =====\n");

    ownership_basics();
    borrowing_demo();
    slice_demo();
    lifetime_demo();
}

fn ownership_basics() {
    println!("--- Ownership & Move ---");

    // Stack types: Copy
    let x = 42;
    let y = x; // copied
    println!("Both valid: x={} y={}", x, y);

    // Heap types: Move
    let s1 = String::from("hello");
    let s2 = s1; // s1 is MOVED — no longer valid
    println!("s2 (owner): {}", s2);
    // println!("{}", s1); // would not compile

    // Clone — explicit deep copy
    let s3 = s2.clone();
    println!("s2={} s3={} (independent copies)", s2, s3);

    // Ownership through functions
    let s = String::from("ownership");
    let s = takes_and_gives_back(s); // moved in, moved back
    println!("Got back: {}", s);

    // Functions take ownership of heap types
    let greeting = String::from("Hello!");
    let len = calculate_length(&greeting); // borrow, not move
    println!("'{}' has length {}", greeting, len); // still valid
    println!();
}

fn takes_and_gives_back(s: String) -> String { s }

fn calculate_length(s: &String) -> usize { s.len() }

fn borrowing_demo() {
    println!("--- Borrowing ---");

    let mut s = String::from("hello");

    // Multiple immutable borrows — OK
    {
        let r1 = &s;
        let r2 = &s;
        println!("r1={} r2={}", r1, r2);
    } // r1 and r2 dropped here

    // One mutable borrow — OK
    {
        let r3 = &mut s;
        r3.push_str(", world");
        println!("r3 mutated: {}", r3);
    } // r3 dropped here

    println!("s after mutation: {}", s);

    // Demonstrating scope-based borrowing
    let mut data = vec![1, 2, 3, 4, 5];
    let sum = sum_slice(&data);
    println!("Sum of {:?} = {}", data, sum);

    double_all(&mut data);
    println!("Doubled: {:?}", data);

    // Struct with borrows
    let novel = String::from("Call me Ishmael. Some years ago...");
    let first_sentence = first_sentence(&novel);
    println!("First sentence: {}", first_sentence);
    println!();
}

fn sum_slice(v: &[i32]) -> i32 { v.iter().sum() }

fn double_all(v: &mut Vec<i32>) {
    for x in v.iter_mut() { *x *= 2; }
}

fn first_sentence(s: &str) -> &str {
    s.split('.').next().unwrap_or(s)
}

fn slice_demo() {
    println!("--- Slices ---");

    // String slices
    let sentence = String::from("the quick brown fox");
    let first = first_word(&sentence);
    println!("First word: {}", first);

    let words: Vec<&str> = sentence.split_whitespace().collect();
    println!("All words: {:?}", words);
    println!("Last word: {}", words.last().unwrap_or(&""));

    // Array slices
    let numbers = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
    println!("Full array: {:?}", numbers);
    println!("Slice [2..5]: {:?}", &numbers[2..5]);
    println!("Slice [..3]:  {:?}", &numbers[..3]);
    println!("Slice [7..]:  {:?}", &numbers[7..]);
    println!("Max of slice: {}", max_of_slice(&numbers[3..7]));

    // Mutable slice
    let mut data = [5, 3, 8, 1, 9, 2, 7, 4, 6];
    sort_slice(&mut data);
    println!("Sorted: {:?}", data);
    println!();
}

fn first_word(s: &str) -> &str {
    for (i, ch) in s.char_indices() {
        if ch == ' ' { return &s[0..i]; }
    }
    &s[..]
}

fn max_of_slice(s: &[i32]) -> i32 {
    *s.iter().max().unwrap_or(&0)
}

fn sort_slice(s: &mut [i32]) {
    s.sort();
}

fn lifetime_demo() {
    println!("--- Lifetimes ---");

    let string1 = String::from("long string is long");
    let result;
    {
        let string2 = String::from("xyz");
        result = longest(string1.as_str(), string2.as_str());
        println!("Longest: {}", result);
    }

    // Struct with a lifetime annotation
    let novel = String::from("Chapter 1. Once upon a time...");
    let first_chapter = ImportantExcerpt {
        part: novel.split('.').next().expect("Could not find '.'"),
    };
    println!("Excerpt: {}", first_chapter.part);
    println!("Level: {}", first_chapter.level(3));
    println!();
}

// Lifetime annotation: result lives as long as the shorter of x or y
fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
    if x.len() > y.len() { x } else { y }
}

// Struct holding a reference — must annotate lifetime
struct ImportantExcerpt<'a> {
    part: &'a str,
}

impl<'a> ImportantExcerpt<'a> {
    fn level(&self, importance: u8) -> String {
        format!("[Level {}] {}", importance, self.part)
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_first_word()     { assert_eq!(first_word("hello world"), "hello"); }
    #[test] fn test_sum_slice()      { assert_eq!(sum_slice(&[1,2,3,4,5]), 15); }
    #[test] fn test_max_of_slice()   { assert_eq!(max_of_slice(&[3,1,4,1,5,9]), 9); }
    #[test] fn test_longest()        { assert_eq!(longest("hello", "hi"), "hello"); }
    #[test] fn test_double_all() {
        let mut v = vec![1,2,3];
        double_all(&mut v);
        assert_eq!(v, vec![2,4,6]);
    }
}
