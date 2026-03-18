/**
 * Rust Variables and Mutability
 * This example demonstrates basic variable declaration and the mut keyword.
 */

fn main() {
    // Immutable Variable (Default)
    let name = "Rust Learner";
    let age = 30;

    // Output using formatted print macro
    println!("Hello, {}! You are {} years old.", name, age);

    // Mutable Variable (using 'mut')
    let mut score = 100;
    println!("Initial Score: {}", score);

    // Update value
    score = score + 50;
    println!("Updated Score: {}", score);

    // Shadowing
    let score = score * 2;
    println!("Shadowed Score (immutable): {}", score);

    // Constant
    const PI: f64 = 3.14159;
    println!("Constant PI: {}", PI);
}