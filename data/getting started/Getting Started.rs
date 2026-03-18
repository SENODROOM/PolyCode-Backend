// ============================================================
//  Module 01: Getting Started with Rust
//  Run: cargo run
// ============================================================

fn main() {
    println!("===== Module 01: Getting Started =====\n");

    // ── Hello World ─────────────────────────────────────────
    println!("Hello, World!");
    println!("Hello, {}!", "Rust");

    // ── println! formatting ──────────────────────────────────
    println!("\n--- Formatting ---");
    println!("Integer:  {}", 42);
    println!("Float:    {:.3}", 3.14159);
    println!("Debug:    {:?}", vec![1, 2, 3]);
    println!("Binary:   {:b}", 42);
    println!("Hex:      {:x}", 255);
    println!("Octet:    {:o}", 8);
    println!("Padded:   {:>10}", "right");
    println!("Padded:   {:<10}", "left");
    println!("Padded:   {:^10}", "center");
    println!("ZeroPad:  {:0>5}", 42);
    println!("Named:    {name} is {age}", name = "Alice", age = 30);

    // ── Basic expressions ────────────────────────────────────
    println!("\n--- Expressions ---");
    println!("2 + 3   = {}", 2 + 3);
    println!("10 / 3  = {}", 10 / 3);    // integer division
    println!("10 % 3  = {}", 10 % 3);    // remainder
    println!("2_i32.pow(10) = {}", 2_i32.pow(10));
    println!("f64::sqrt(2.0) = {:.6}", f64::sqrt(2.0));

    // ── Printing different types ─────────────────────────────
    println!("\n--- Types ---");
    let integer: i32 = 42;
    let float: f64   = 3.14;
    let boolean: bool = true;
    let character: char = '🦀';
    let text: &str = "Hello Rust";

    println!("i32:  {}", integer);
    println!("f64:  {}", float);
    println!("bool: {}", boolean);
    println!("char: {}", character);
    println!("str:  {}", text);

    // ── Pretty debug ─────────────────────────────────────────
    println!("\n--- Debug printing ---");
    let numbers = [10, 20, 30, 40, 50];
    let pair    = (42, "hello");
    println!("Array: {:?}", numbers);
    println!("Tuple: {:?}", pair);
    println!("Pretty:\n{:#?}", vec!["rust", "is", "awesome"]);

    // ── eprintln! for errors ─────────────────────────────────
    eprintln!("(This goes to stderr — useful for logs/errors)");

    println!("\n✅ Module 01 complete! You're set up and running Rust.");
}
