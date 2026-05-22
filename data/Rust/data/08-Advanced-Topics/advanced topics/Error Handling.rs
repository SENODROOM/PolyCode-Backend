/**
 * Error Handling in Rust
 * Rust uses the Result enum for recoverable errors.
 */

fn divide(numerator: f64, denominator: f64) -> Result<f64, String> {
    if denominator == 0.0 {
        Err(String::from("Cannot divide by zero"))
    } else {
        Ok(numerator / denominator)
    }
}

fn main() {
    let numbers = vec![(10.0, 2.0), (5.0, 0.0)];

    for (a, b) in numbers {
        let result = divide(a, b);

        match result {
            Ok(value) => println!("{} / {} = {}", a, b, value),
            Err(e) => println!("Error: {}", e),
        }
    }

    // Using the '?' operator (only works in functions returning Result/Option)
    // Here we'll just demonstrate unwrap_or for simple fallback
    let safe_result = divide(10.0, 0.0).unwrap_or(0.0);
    println!("Safe Fallback Result: {}", safe_result);
}