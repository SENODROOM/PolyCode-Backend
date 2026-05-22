// 17_async_programming.rs
// Comprehensive examples of async programming in Rust
// Note: This file requires tokio runtime to execute
// Run with: cargo run --bin 17_async_programming

use std::time::Duration;
use std::pin::Pin;
use std::task::{Context, Poll};
use std::future::Future;

// =========================================
// BASIC ASYNC FUNCTIONS
// =========================================

async fn hello_world() {
    println!("Hello, async world!");
}

async fn add_numbers(a: i32, b: i32) -> i32 {
    println!("Adding {} and {}", a, b);
    tokio::time::sleep(Duration::from_millis(100)).await;
    a + b
}

// =========================================
// ASYNC BLOCKS AND EXPRESSIONS
// =========================================

fn create_async_future() -> impl Future<Output = i32> {
    async {
        println!("Inside async block");
        tokio::time::sleep(Duration::from_millis(50)).await;
        42
    }
}

#[tokio::main]
async fn main() {
    hello_world().await;
    
    let sum = add_numbers(5, 10).await;
    println!("Sum: {}", sum);
    
    let future_val = create_async_future().await;
    println!("Future Value: {}", future_val);
}