/**
 * Lifetimes in Rust
 * This tutorial covers explicit lifetime annotations to ensure memory safety.
 * Lifetimes are a way for the compiler to track how long references are valid.
 */

// --- Function with Explicit Lifetimes ---
// 'a is a lifetime parameter that says both input references
// must live at least as long as the returned reference.
fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
    if x.len() > y.len() {
        x
    } else {
        y
    }
}

// --- Struct with Explicit Lifetimes ---
// This struct can't live longer than the string reference it holds.
struct ImportantExcerpt<'a> {
    part: &'a str,
}

fn main() {
    let string1 = String::from("abcd");
    let string2 = "xyz";

    let result = longest(string1.as_str(), string2);
    println!("The longest string is {}", result);

    let novel = String::from("Call me Ishmael. Some years ago...");
    let first_sentence = novel.split('.').next().expect("Could not find a '.'");
    let i = ImportantExcerpt {
        part: first_sentence,
    };

    println!("Excerpt: {}", i.part);
}