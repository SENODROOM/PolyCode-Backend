// ============================================================
//  Module 05: Traits & Generics
// ============================================================

use std::fmt;

fn main() {
    println!("===== Module 05: Traits & Generics =====\n");
    trait_basics();
    generics_demo();
    trait_objects_demo();
    standard_traits_demo();
}

// ─────────────────────────────────────────────
// TRAITS
// ─────────────────────────────────────────────

trait Describable {
    fn name(&self) -> &str;
    fn describe(&self) -> String;
    // Default implementation
    fn short_description(&self) -> String {
        let d = self.describe();
        if d.len() > 40 { format!("{}...", &d[..40]) } else { d }
    }
}

trait Priceable {
    fn price(&self) -> f64;
    fn discounted_price(&self, discount_pct: f64) -> f64 {
        self.price() * (1.0 - discount_pct / 100.0)
    }
}

#[derive(Debug, Clone)]
struct Book { title: String, author: String, pages: u32, price: f64 }

#[derive(Debug, Clone)]
struct Movie { title: String, director: String, runtime_mins: u32, price: f64 }

#[derive(Debug, Clone)]
struct Song { title: String, artist: String, duration_secs: u32, price: f64 }

impl Describable for Book {
    fn name(&self) -> &str { &self.title }
    fn describe(&self) -> String {
        format!("'{}' by {} ({} pages)", self.title, self.author, self.pages)
    }
}

impl Describable for Movie {
    fn name(&self) -> &str { &self.title }
    fn describe(&self) -> String {
        format!("'{}' dir. {} ({}min)", self.title, self.director, self.runtime_mins)
    }
}

impl Describable for Song {
    fn name(&self) -> &str { &self.title }
    fn describe(&self) -> String {
        format!("'{}' by {} ({}s)", self.title, self.artist, self.duration_secs)
    }
}

impl Priceable for Book  { fn price(&self) -> f64 { self.price  } }
impl Priceable for Movie { fn price(&self) -> f64 { self.price  } }
impl Priceable for Song  { fn price(&self) -> f64 { self.price  } }

// Function with trait bound — works for any Describable
fn print_item(item: &impl Describable) {
    println!("  {}", item.describe());
}

// Multiple trait bounds
fn print_price_item<T: Describable + Priceable>(item: &T) {
    println!("  {} — ${:.2}", item.name(), item.price());
}

fn trait_basics() {
    println!("--- Trait Basics ---");
    let book  = Book  { title: "The Rust Programming Language".into(), author: "Steve Klabnik".into(), pages: 552, price: 39.99 };
    let movie = Movie { title: "Inception".into(), director: "Christopher Nolan".into(), runtime_mins: 148, price: 14.99 };
    let song  = Song  { title: "Bohemian Rhapsody".into(), artist: "Queen".into(), duration_secs: 354, price: 1.29 };

    print_item(&book);
    print_item(&movie);
    print_item(&song);

    println!("\nWith prices:");
    print_price_item(&book);
    print_price_item(&movie);
    print_price_item(&song);

    println!("\nDiscounts (20%):");
    println!("  Book:  ${:.2}", book.discounted_price(20.0));
    println!("  Movie: ${:.2}", movie.discounted_price(20.0));
    println!();
}

// ─────────────────────────────────────────────
// GENERICS
// ─────────────────────────────────────────────

fn largest<T: PartialOrd>(list: &[T]) -> &T {
    list.iter().reduce(|a, b| if a >= b { a } else { b }).unwrap()
}

fn smallest<T: PartialOrd>(list: &[T]) -> &T {
    list.iter().reduce(|a, b| if a <= b { a } else { b }).unwrap()
}

#[derive(Debug)]
struct Stack<T> {
    items: Vec<T>,
}

impl<T> Stack<T> {
    fn new() -> Self { Self { items: Vec::new() } }
    fn push(&mut self, item: T) { self.items.push(item); }
    fn pop(&mut self) -> Option<T> { self.items.pop() }
    fn peek(&self) -> Option<&T> { self.items.last() }
    fn is_empty(&self) -> bool { self.items.is_empty() }
    fn size(&self) -> usize { self.items.len() }
}

impl<T: fmt::Display> Stack<T> {
    fn print_all(&self) {
        print!("  Stack [");
        for (i, item) in self.items.iter().enumerate() {
            if i > 0 { print!(", "); }
            print!("{}", item);
        }
        println!("]  top={}", self.size());
    }
}

// Generic pair
#[derive(Debug)]
struct Pair<T> { first: T, second: T }

impl<T: PartialOrd + fmt::Display> Pair<T> {
    fn new(first: T, second: T) -> Self { Self { first, second } }
    fn max_val(&self) -> &T { if self.first >= self.second { &self.first } else { &self.second } }
    fn min_val(&self) -> &T { if self.first <= self.second { &self.first } else { &self.second } }
}

fn generics_demo() {
    println!("--- Generics ---");

    let numbers   = vec![34, 50, 25, 100, 65];
    let chars     = vec!['y', 'm', 'a', 'q'];
    let strings   = vec!["banana", "apple", "cherry", "date"];
    println!("largest number: {}", largest(&numbers));
    println!("largest char:   {}", largest(&chars));
    println!("largest string: {}", largest(&strings));
    println!("smallest num:   {}", smallest(&numbers));

    println!("\nGeneric Stack<i32>:");
    let mut stack: Stack<i32> = Stack::new();
    for &x in &[10, 20, 30, 40, 50] { stack.push(x); }
    stack.print_all();
    println!("  pop: {:?}", stack.pop());
    println!("  peek: {:?}", stack.peek());
    stack.print_all();

    println!("\nGeneric Stack<&str>:");
    let mut word_stack: Stack<&str> = Stack::new();
    for w in &["hello", "world", "rust"] { word_stack.push(w); }
    word_stack.print_all();

    println!("\nGeneric Pair:");
    let p1 = Pair::new(5, 10);
    let p2 = Pair::new("apple", "orange");
    println!("  ({}, {}) → max={}, min={}", p1.first, p1.second, p1.max_val(), p1.min_val());
    println!("  ({}, {}) → max={}", p2.first, p2.second, p2.max_val());
    println!();
}

// ─────────────────────────────────────────────
// TRAIT OBJECTS (dynamic dispatch)
// ─────────────────────────────────────────────

trait Shape {
    fn area(&self) -> f64;
    fn perimeter(&self) -> f64;
    fn kind(&self) -> &str;
}

struct Circle   { radius: f64 }
struct Rect     { width: f64, height: f64 }
struct Triangle { a: f64, b: f64, c: f64 }

impl Shape for Circle {
    fn area(&self)      -> f64 { std::f64::consts::PI * self.radius * self.radius }
    fn perimeter(&self) -> f64 { 2.0 * std::f64::consts::PI * self.radius }
    fn kind(&self)      -> &str { "Circle" }
}
impl Shape for Rect {
    fn area(&self)      -> f64 { self.width * self.height }
    fn perimeter(&self) -> f64 { 2.0 * (self.width + self.height) }
    fn kind(&self)      -> &str { "Rectangle" }
}
impl Shape for Triangle {
    fn area(&self) -> f64 {
        let s = (self.a + self.b + self.c) / 2.0;
        (s*(s-self.a)*(s-self.b)*(s-self.c)).sqrt()
    }
    fn perimeter(&self) -> f64 { self.a + self.b + self.c }
    fn kind(&self)      -> &str { "Triangle" }
}

fn total_area(shapes: &[Box<dyn Shape>]) -> f64 {
    shapes.iter().map(|s| s.area()).sum()
}

fn largest_shape(shapes: &[Box<dyn Shape>]) -> Option<&dyn Shape> {
    shapes.iter().map(|s| s.as_ref()).reduce(|a, b| if a.area() >= b.area() { a } else { b })
}

fn trait_objects_demo() {
    println!("--- Trait Objects (dyn Trait) ---");

    let shapes: Vec<Box<dyn Shape>> = vec![
        Box::new(Circle   { radius: 5.0 }),
        Box::new(Rect     { width: 4.0, height: 6.0 }),
        Box::new(Triangle { a: 3.0, b: 4.0, c: 5.0 }),
        Box::new(Circle   { radius: 2.0 }),
    ];

    println!("{:<12} {:>10} {:>12}", "Kind", "Area", "Perimeter");
    println!("{}", "─".repeat(36));
    for s in &shapes {
        println!("{:<12} {:>10.3} {:>12.3}", s.kind(), s.area(), s.perimeter());
    }
    println!("{}", "─".repeat(36));
    println!("{:<12} {:>10.3}", "TOTAL", total_area(&shapes));

    if let Some(biggest) = largest_shape(&shapes) {
        println!("Largest: {} (area={:.3})", biggest.kind(), biggest.area());
    }
    println!();
}

// ─────────────────────────────────────────────
// STANDARD LIBRARY TRAITS
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq)]
struct Temperature { celsius: f64 }

impl Temperature {
    fn new(celsius: f64) -> Self { Self { celsius } }
    fn to_fahrenheit(&self) -> f64 { self.celsius * 9.0/5.0 + 32.0 }
    fn to_kelvin(&self)     -> f64 { self.celsius + 273.15 }
}

impl fmt::Display for Temperature {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "{:.1}°C ({:.1}°F)", self.celsius, self.to_fahrenheit())
    }
}

impl PartialOrd for Temperature {
    fn partial_cmp(&self, other: &Self) -> Option<std::cmp::Ordering> {
        self.celsius.partial_cmp(&other.celsius)
    }
}

impl From<f64> for Temperature {
    fn from(c: f64) -> Self { Self::new(c) }
}

impl std::ops::Add for Temperature {
    type Output = Temperature;
    fn add(self, other: Temperature) -> Temperature {
        Temperature::new(self.celsius + other.celsius)
    }
}

fn standard_traits_demo() {
    println!("--- Standard Library Traits ---");

    let boiling  = Temperature::new(100.0);
    let freezing = Temperature::new(0.0);
    let body     = Temperature::from(37.0);

    println!("Boiling:  {}", boiling);
    println!("Freezing: {}", freezing);
    println!("Body:     {} ({}K)", body, body.to_kelvin());

    println!("boiling > freezing: {}", boiling > freezing);
    println!("boiling == boiling: {}", boiling == boiling.clone());

    let mut temps: Vec<Temperature> = vec![
        Temperature::new(37.0), Temperature::new(-10.0),
        Temperature::new(100.0), Temperature::new(21.0),
    ];
    temps.sort_by(|a, b| a.partial_cmp(b).unwrap());
    println!("Sorted: {:?}", temps.iter().map(|t| t.celsius).collect::<Vec<_>>());

    println!("\n✅ Module 05 complete!");
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_largest()          { assert_eq!(*largest(&[3,1,4,1,5,9,2,6]), 9); }
    #[test] fn test_stack()            { let mut s: Stack<i32> = Stack::new(); s.push(1); s.push(2); assert_eq!(s.pop(), Some(2)); }
    #[test] fn test_circle_area()      { let c = Circle { radius: 1.0 }; assert!((c.area() - std::f64::consts::PI).abs() < 1e-9); }
    #[test] fn test_temperature_from() { let t = Temperature::from(100.0); assert!((t.to_fahrenheit() - 212.0).abs() < 0.001); }
}
