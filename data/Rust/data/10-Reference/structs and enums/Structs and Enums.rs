// ============================================================
//  Module 04: Structs & Enums
// ============================================================

use std::fmt;

fn main() {
    println!("===== Module 04: Structs & Enums =====\n");
    struct_demo();
    enum_demo();
    option_demo();
    real_world_demo();
}

// ─────────────────────────────────────────────
// STRUCTS
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq)]
struct Point { x: f64, y: f64 }

impl Point {
    fn new(x: f64, y: f64) -> Self { Self { x, y } }
    fn origin() -> Self { Self::new(0.0, 0.0) }
    fn distance(&self, other: &Point) -> f64 {
        ((self.x - other.x).powi(2) + (self.y - other.y).powi(2)).sqrt()
    }
    fn translate(&mut self, dx: f64, dy: f64) { self.x += dx; self.y += dy; }
}

impl fmt::Display for Point {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "({:.1}, {:.1})", self.x, self.y)
    }
}

#[derive(Debug, Clone)]
struct Rectangle { width: f64, height: f64 }

impl Rectangle {
    fn new(width: f64, height: f64) -> Self { Self { width, height } }
    fn square(size: f64)            -> Self { Self::new(size, size) }
    fn area(&self)       -> f64 { self.width * self.height }
    fn perimeter(&self)  -> f64 { 2.0 * (self.width + self.height) }
    fn is_square(&self)  -> bool { (self.width - self.height).abs() < 1e-9 }
    fn scale(&mut self, factor: f64) { self.width *= factor; self.height *= factor; }
    fn can_contain(&self, other: &Rectangle) -> bool {
        self.width > other.width && self.height > other.height
    }
}

impl fmt::Display for Rectangle {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "Rectangle({}x{})", self.width, self.height)
    }
}

// Tuple struct
struct Color(u8, u8, u8);
impl Color {
    fn to_hex(&self) -> String { format!("#{:02X}{:02X}{:02X}", self.0, self.1, self.2) }
    fn brightness(&self) -> f64 { (self.0 as f64 * 0.299 + self.1 as f64 * 0.587 + self.2 as f64 * 0.114) / 255.0 }
}

fn struct_demo() {
    println!("--- Structs ---");

    let p1 = Point::new(3.0, 4.0);
    let p2 = Point::new(0.0, 0.0);
    println!("p1 = {}", p1);
    println!("p2 = {}", p2);
    println!("distance p1→p2: {:.4}", p1.distance(&p2));

    let mut p3 = p1.clone();
    p3.translate(1.0, -2.0);
    println!("p3 after translate: {}", p3);
    println!("p1 == p3: {}", p1 == p3);

    let mut rect = Rectangle::new(8.0, 5.0);
    println!("\n{}", rect);
    println!("  area:      {:.1}", rect.area());
    println!("  perimeter: {:.1}", rect.perimeter());
    println!("  is_square: {}", rect.is_square());
    rect.scale(2.0);
    println!("  scaled x2: {}", rect);

    let sq = Rectangle::square(6.0);
    println!("  {} is_square: {}", sq, sq.is_square());
    println!("  rect can contain sq: {}", rect.can_contain(&sq));

    let red   = Color(255, 0, 0);
    let green = Color(0, 200, 0);
    println!("\nred hex: {} brightness: {:.2}", red.to_hex(), red.brightness());
    println!("grn hex: {} brightness: {:.2}", green.to_hex(), green.brightness());
    println!();
}

// ─────────────────────────────────────────────
// ENUMS
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq)]
enum Suit { Hearts, Diamonds, Clubs, Spades }

#[derive(Debug, Clone)]
enum Rank { Number(u8), Jack, Queen, King, Ace }

#[derive(Debug, Clone)]
struct Card { rank: Rank, suit: Suit }

impl Card {
    fn value(&self) -> u8 {
        match &self.rank {
            Rank::Number(n) => *n,
            Rank::Jack | Rank::Queen | Rank::King => 10,
            Rank::Ace => 11,
        }
    }
    fn is_face_card(&self) -> bool {
        matches!(self.rank, Rank::Jack | Rank::Queen | Rank::King)
    }
}

impl fmt::Display for Card {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        let rank = match &self.rank {
            Rank::Number(n) => n.to_string(),
            Rank::Jack  => "J".to_string(),
            Rank::Queen => "Q".to_string(),
            Rank::King  => "K".to_string(),
            Rank::Ace   => "A".to_string(),
        };
        let suit = match self.suit {
            Suit::Hearts   => "♥",
            Suit::Diamonds => "♦",
            Suit::Clubs    => "♣",
            Suit::Spades   => "♠",
        };
        write!(f, "{}{}", rank, suit)
    }
}

#[derive(Debug)]
enum AppCommand {
    Quit,
    Help,
    SetValue { key: String, value: String },
    GetValue(String),
    List { prefix: Option<String> },
}

impl AppCommand {
    fn execute(&self) -> String {
        match self {
            AppCommand::Quit         => "Goodbye!".to_string(),
            AppCommand::Help         => "Available: quit, help, set, get, list".to_string(),
            AppCommand::SetValue { key, value } => format!("Set {} = {}", key, value),
            AppCommand::GetValue(key) => format!("Getting value for '{}'", key),
            AppCommand::List { prefix: Some(p) } => format!("Listing keys with prefix '{}'", p),
            AppCommand::List { prefix: None }     => "Listing all keys".to_string(),
        }
    }
}

fn enum_demo() {
    println!("--- Enums ---");

    let hand = vec![
        Card { rank: Rank::Ace,       suit: Suit::Spades  },
        Card { rank: Rank::King,      suit: Suit::Hearts  },
        Card { rank: Rank::Number(7), suit: Suit::Clubs   },
        Card { rank: Rank::Queen,     suit: Suit::Diamonds},
    ];
    print!("Hand: ");
    for card in &hand { print!("{} ", card); }
    println!();
    let total: u8 = hand.iter().map(|c| c.value()).sum();
    println!("Total value: {}", total);
    let face_cards: Vec<_> = hand.iter().filter(|c| c.is_face_card()).collect();
    println!("Face cards: {}", face_cards.len());

    println!("\nCommand dispatch:");
    let commands = vec![
        AppCommand::Help,
        AppCommand::SetValue { key: "name".to_string(), value: "Alice".to_string() },
        AppCommand::GetValue("name".to_string()),
        AppCommand::List { prefix: Some("user_".to_string()) },
        AppCommand::List { prefix: None },
        AppCommand::Quit,
    ];
    for cmd in &commands {
        println!("  {:?} → {}", cmd, cmd.execute());
    }
    println!();
}

// ─────────────────────────────────────────────
// OPTION
// ─────────────────────────────────────────────

fn divide(a: f64, b: f64) -> Option<f64> {
    if b == 0.0 { None } else { Some(a / b) }
}

fn find_first_even(numbers: &[i32]) -> Option<i32> {
    numbers.iter().find(|&&x| x % 2 == 0).copied()
}

fn option_demo() {
    println!("--- Option<T> ---");

    // Basic matching
    let result = divide(10.0, 3.0);
    match result {
        Some(v) => println!("10/3 = {:.4}", v),
        None    => println!("Division by zero"),
    }
    println!("10/0 = {:?}", divide(10.0, 0.0));

    // Option methods
    let nums = vec![1, 3, 5, 4, 7, 9];
    let first_even = find_first_even(&nums);
    println!("First even in {:?}: {:?}", nums, first_even);
    println!("unwrap_or: {}", find_first_even(&[1,3,5]).unwrap_or(-1));
    println!("map: {:?}", find_first_even(&nums).map(|x| x * 10));
    println!("filter: {:?}", first_even.filter(|&x| x > 3));

    // if let
    let odd_only = vec![1, 3, 5, 7];
    if let Some(e) = find_first_even(&odd_only) {
        println!("Found even: {}", e);
    } else {
        println!("No even numbers found");
    }

    // Chaining Options
    let nested: Option<Option<i32>> = Some(Some(42));
    println!("flatten: {:?}", nested.flatten());
    println!();
}

// ─────────────────────────────────────────────
// REAL-WORLD: Student grade system
// ─────────────────────────────────────────────

#[derive(Debug, Clone)]
struct Student {
    name:    String,
    grades:  Vec<f64>,
}

#[derive(Debug, PartialEq)]
enum LetterGrade { A, B, C, D, F }

impl Student {
    fn new(name: &str) -> Self { Self { name: name.to_string(), grades: Vec::new() } }
    fn add_grade(&mut self, g: f64) { self.grades.push(g); }
    fn average(&self) -> Option<f64> {
        if self.grades.is_empty() { return None; }
        Some(self.grades.iter().sum::<f64>() / self.grades.len() as f64)
    }
    fn letter_grade(&self) -> Option<LetterGrade> {
        self.average().map(|avg| match avg as u32 {
            90..=100 => LetterGrade::A,
            80..=89  => LetterGrade::B,
            70..=79  => LetterGrade::C,
            60..=69  => LetterGrade::D,
            _        => LetterGrade::F,
        })
    }
    fn is_passing(&self) -> bool {
        self.average().map(|a| a >= 60.0).unwrap_or(false)
    }
}

impl fmt::Display for LetterGrade {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "{:?}", self)
    }
}

fn real_world_demo() {
    println!("--- Real-World: Student Grades ---");

    let mut students = vec![
        Student::new("Alice"),
        Student::new("Bob"),
        Student::new("Charlie"),
        Student::new("Diana"),
    ];

    students[0].add_grade(95.0); students[0].add_grade(88.0); students[0].add_grade(92.0);
    students[1].add_grade(72.0); students[1].add_grade(68.0); students[1].add_grade(75.0);
    students[2].add_grade(58.0); students[2].add_grade(61.0); students[2].add_grade(55.0);
    students[3].add_grade(85.0); students[3].add_grade(90.0); students[3].add_grade(87.0);

    println!("{:<10} {:>8} {:>6} {:>8}", "Name", "Average", "Grade", "Passing");
    println!("{}", "─".repeat(36));
    for s in &students {
        let avg = s.average().map(|a| format!("{:.1}", a)).unwrap_or("N/A".to_string());
        let grade = s.letter_grade().map(|g| format!("{}", g)).unwrap_or("N/A".to_string());
        println!("{:<10} {:>8} {:>6} {:>8}", s.name, avg, grade, if s.is_passing() { "✅" } else { "❌" });
    }

    let class_avg: f64 = students.iter().filter_map(|s| s.average()).sum::<f64>() / students.len() as f64;
    println!("\nClass average: {:.1}", class_avg);

    let top = students.iter().max_by(|a, b|
        a.average().partial_cmp(&b.average()).unwrap()
    );
    if let Some(s) = top {
        println!("Top student:   {} ({:.1})", s.name, s.average().unwrap());
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_rectangle_area()    { assert_eq!(Rectangle::new(4.0, 5.0).area(), 20.0); }
    #[test] fn test_square_is_square()  { assert!(Rectangle::square(5.0).is_square()); }
    #[test] fn test_divide_some()       { assert!((divide(10.0, 4.0).unwrap() - 2.5).abs() < 1e-9); }
    #[test] fn test_divide_none()       { assert!(divide(10.0, 0.0).is_none()); }
    #[test] fn test_card_value()        { assert_eq!(Card { rank: Rank::Ace, suit: Suit::Spades }.value(), 11); }
    #[test] fn test_student_grade() {
        let mut s = Student::new("Test");
        s.add_grade(92.0); s.add_grade(88.0);
        assert_eq!(s.letter_grade(), Some(LetterGrade::A));
    }
}
