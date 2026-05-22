// ============================================================
//  Rust Enums & Pattern Matching — Complete Examples
// ============================================================

use std::fmt;

// ─────────────────────────────────────────────
// SECTION 1: Basic Enums
// ─────────────────────────────────────────────

#[derive(Debug)]
enum Direction { North, South, East, West }

impl Direction {
    fn opposite(&self) -> Direction {
        match self {
            Direction::North => Direction::South,
            Direction::South => Direction::North,
            Direction::East  => Direction::West,
            Direction::West  => Direction::East,
        }
    }
    fn is_vertical(&self) -> bool {
        matches!(self, Direction::North | Direction::South)
    }
}

impl fmt::Display for Direction {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "{:?}", self)
    }
}

// ─────────────────────────────────────────────
// SECTION 2: Enums with Data
// ─────────────────────────────────────────────

#[derive(Debug)]
enum Shape {
    Circle(f64),
    Rectangle(f64, f64),
    Triangle { base: f64, height: f64 },
}

impl Shape {
    fn area(&self) -> f64 {
        match self {
            Shape::Circle(r) => std::f64::consts::PI * r * r,
            Shape::Rectangle(w, h) => w * h,
            Shape::Triangle { base: b, height: h } => 0.5 * b * h,
        }
    }
    fn perimeter(&self) -> f64 {
        match self {
            Shape::Circle(r) => 2.0 * std::f64::consts::PI * r,
            Shape::Rectangle(w, h) => 2.0 * (w + h),
            Shape::Triangle { base: b, height: h } => {
                let side = (b * b / 4.0 + h * h).sqrt();
                b + 2.0 * side
            }
        }
    }
    fn name(&self) -> &str {
        match self {
            Shape::Circle(_)     => "Circle",
            Shape::Rectangle(..) => "Rectangle",
            Shape::Triangle {..} => "Triangle",
        }
    }
    fn scale(&self, factor: f64) -> Shape {
        match self {
            Shape::Circle(r)    => Shape::Circle(r * factor),
            Shape::Rectangle(w, h) => Shape::Rectangle(w * factor, h * factor),
            Shape::Triangle { base, height } => Shape::Triangle {
                base: base * factor, height: height * factor
            },
        }
    }
}

// ─────────────────────────────────────────────
// SECTION 3: Option<T>
// ─────────────────────────────────────────────

struct Database {
    users: Vec<(u32, String, u32)>, // id, name, age
}

impl Database {
    fn new() -> Self {
        Database {
            users: vec![
                (1, "Alice".to_string(), 30),
                (2, "Bob".to_string(), 25),
                (3, "Charlie".to_string(), 35),
            ]
        }
    }

    fn find_by_id(&self, id: u32) -> Option<&(u32, String, u32)> {
        self.users.iter().find(|u| u.0 == id)
    }

    fn find_by_name(&self, name: &str) -> Option<&(u32, String, u32)> {
        self.users.iter().find(|u| u.1.to_lowercase() == name.to_lowercase())
    }

    fn oldest_user(&self) -> Option<&(u32, String, u32)> {
        self.users.iter().max_by_key(|u| u.2)
    }
}

fn demo_option(db: &Database) {
    println!("--- Option<T> ---");

    // Pattern matching
    match db.find_by_id(1) {
        Some((_, name, age)) => println!("Found user: {} (age {})", name, age),
        None => println!("User not found"),
    }

    // if let
    if let Some(user) = db.find_by_name("bob") {
        println!("Found Bob: id={}, age={}", user.0, user.2);
    }

    // unwrap_or / unwrap_or_else
    let default = ("Guest".to_string(), 0u32);
    let (name, age) = db.find_by_id(99)
        .map(|u| (u.1.clone(), u.2))
        .unwrap_or(default);
    println!("ID 99: name={}, age={}", name, age);

    // map — transform the inner value
    let name_upper = db.find_by_id(2).map(|u| u.1.to_uppercase());
    println!("ID 2 upper: {:?}", name_upper);

    // and_then — chain Option operations
    let senior = db.find_by_id(3).and_then(|u| if u.2 > 30 { Some(u) } else { None });
    println!("Senior (>30) ID 3: {:?}", senior.map(|u| &u.1));

    // ? operator in a function returning Option
    fn get_user_email(db: &Database, id: u32) -> Option<String> {
        let user = db.find_by_id(id)?;
        Some(format!("{}@example.com", user.1.to_lowercase()))
    }
    println!("Email for ID 1: {:?}", get_user_email(db, 1));
    println!("Email for ID 9: {:?}", get_user_email(db, 9));

    if let Some(oldest) = db.oldest_user() {
        println!("Oldest user: {} ({})", oldest.1, oldest.2);
    }
}

// ─────────────────────────────────────────────
// SECTION 4: Result<T, E>
// ─────────────────────────────────────────────

#[derive(Debug)]
enum AppError {
    ParseError(String),
    ValidationError(String),
    NotFound(String),
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            AppError::ParseError(m)      => write!(f, "Parse error: {}", m),
            AppError::ValidationError(m) => write!(f, "Validation: {}", m),
            AppError::NotFound(m)        => write!(f, "Not found: {}", m),
        }
    }
}

fn parse_age(s: &str) -> Result<u32, AppError> {
    s.parse::<u32>()
        .map_err(|e| AppError::ParseError(format!("'{}' — {}", s, e)))
}

fn validate_age(age: u32) -> Result<u32, AppError> {
    if age < 18  { return Err(AppError::ValidationError("Must be 18+".to_string())); }
    if age > 120 { return Err(AppError::ValidationError("Unrealistic age".to_string())); }
    Ok(age)
}

fn process_age_input(s: &str) -> Result<String, AppError> {
    let age = parse_age(s)?;
    let validated = validate_age(age)?;
    Ok(format!("Valid age: {}", validated))
}

fn demo_result() {
    println!("\n--- Result<T, E> ---");

    let inputs = ["25", "17", "abc", "150", "42"];
    for input in inputs {
        match process_age_input(input) {
            Ok(msg)  => println!("  ✅ '{}' → {}", input, msg),
            Err(err) => println!("  ❌ '{}' → {}", input, err),
        }
    }

    // map / map_err
    let doubled = parse_age("10").map(|n| n * 2);
    println!("\nparse_age(\"10\").map(*2) = {:?}", doubled);

    // unwrap_or / unwrap_or_else
    let safe = parse_age("bad").unwrap_or(0);
    println!("parse_age(\"bad\").unwrap_or(0) = {}", safe);

    // Collecting Results — fail on first error
    let values = vec!["1", "2", "3", "4"];
    let parsed: Result<Vec<u32>, _> = values.iter().map(|s| parse_age(s)).collect();
    println!("All valid: {:?}", parsed);

    let mixed = vec!["1", "bad", "3"];
    let parsed2: Result<Vec<u32>, _> = mixed.iter().map(|s| parse_age(s)).collect();
    println!("Mixed (fails): {}", parsed2.is_err());
}

// ─────────────────────────────────────────────
// SECTION 5: Advanced Pattern Matching
// ─────────────────────────────────────────────

fn demo_advanced_patterns() {
    println!("\n--- Advanced Patterns ---");

    // Match guards
    let nums = [-5, 0, 3, 7, 15, 100];
    for &n in &nums {
        let label = match n {
            n if n < 0  => format!("negative ({})", n),
            0           => "zero".to_string(),
            n if n < 10 => format!("single digit ({})", n),
            n if n < 100=> format!("double digit ({})", n),
            n           => format!("large ({})", n),
        };
        println!("  {} → {}", n, label);
    }

    // Destructuring structs
    struct Point { x: i32, y: i32 }
    let points = vec![Point{x:0,y:0}, Point{x:3,y:0}, Point{x:0,y:-4}, Point{x:2,y:3}];
    for p in &points {
        let pos = match p {
            Point { x: 0, y: 0 } => "origin",
            Point { x, y: 0 }    => "on x-axis",
            Point { x: 0, y }    => "on y-axis",
            _                    => "general",
        };
        println!("  ({},{}) → {}", p.x, p.y, pos);
    }

    // @ bindings
    let score = 87u32;
    let grade = match score {
        n @ 90..=100 => format!("A ({})", n),
        n @ 80..=89  => format!("B ({})", n),
        n @ 70..=79  => format!("C ({})", n),
        n @ 60..=69  => format!("D ({})", n),
        n            => format!("F ({})", n),
    };
    println!("\nScore {}: {}", score, grade);

    // while let
    let mut stack = vec![1, 2, 3, 4, 5];
    print!("Pop stack: ");
    while let Some(top) = stack.pop() { print!("{} ", top); }
    println!();

    // Nested pattern matching
    let nested: Option<Result<i32, &str>> = Some(Ok(42));
    match nested {
        Some(Ok(n))  => println!("Got value: {}", n),
        Some(Err(e)) => println!("Inner error: {}", e),
        None         => println!("Nothing"),
    }
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Rust Enums & Pattern Matching =====\n");

    // Directions
    println!("--- Enums ---");
    let dir = Direction::North;
    println!("Direction: {}", dir);
    println!("Opposite:  {}", dir.opposite());
    println!("Vertical:  {}", dir.is_vertical());

    // Shapes
    println!("\n--- Enums with Data ---");
    let shapes = vec![
        Shape::Circle(5.0),
        Shape::Rectangle(4.0, 6.0),
        Shape::Triangle { base: 8.0, height: 3.0 },
    ];
    for s in &shapes {
        println!("  {}: area={:.2}, perimeter={:.2}", s.name(), s.area(), s.perimeter());
    }
    let big = shapes[0].scale(2.0);
    println!("  Scaled circle: area={:.2}", big.area());

    // Option
    let db = Database::new();
    demo_option(&db);

    // Result
    demo_result();

    // Advanced patterns
    demo_advanced_patterns();

    println!("\n✅ All enum & pattern matching demos complete!");
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test] fn test_direction_opposite() { assert!(matches!(Direction::North.opposite(), Direction::South)); }
    #[test] fn test_circle_area()        { let s = Shape::Circle(1.0); assert!((s.area() - std::f64::consts::PI).abs() < 1e-9); }
    #[test] fn test_rectangle_area()     { let s = Shape::Rectangle(3.0, 4.0); assert_eq!(s.area(), 12.0); }
    #[test] fn test_parse_age_valid()    { assert_eq!(parse_age("25").unwrap(), 25); }
    #[test] fn test_parse_age_invalid()  { assert!(parse_age("abc").is_err()); }
    #[test] fn test_validate_age_minor() { assert!(validate_age(17).is_err()); }
    #[test] fn test_validate_age_valid() { assert_eq!(validate_age(25).unwrap(), 25); }
}
