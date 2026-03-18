/**
 * Data Structures in Rust
 * This tutorial covers Vectors and HashMaps from the standard library.
 */

use std::collections::HashMap;

fn main() {
    // Vector (Dynamic Array on the heap)
    let mut fruits = vec!["Apple", "Banana", "Cherry"];
    fruits.push("Date");

    println!("--- Vector ---");
    for fruit in &fruits {
        print!("{} ", fruit);
    }
    println!();

    // HashMap (Key-Value Store)
    let mut scores = HashMap::new();
    scores.insert(String::from("Alice"), 95);
    scores.insert(String::from("Bob"), 88);

    println!("\n--- HashMap ---");
    for (name, score) in &scores {
        println!("{}: {}", name, score);
    }

    // Accessing a value (returns Option<&V>)
    let alice_score = scores.get("Alice");
    match alice_score {
        Some(s) => println!("Alice's score is {}", s),
        None => println!("Alice not found"),
    }
}