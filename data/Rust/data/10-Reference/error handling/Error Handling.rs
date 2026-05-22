// ============================================================
//  Module 07: Error Handling
// ============================================================

use std::fmt;
use std::num::ParseIntError;

fn main() {
    println!("===== Module 07: Error Handling =====\n");
    result_basics();
    question_mark_operator();
    custom_errors_demo();
    real_world_demo();
}

// ─────────────────────────────────────────────
// RESULT BASICS
// ─────────────────────────────────────────────

fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 { Err("Cannot divide by zero".to_string()) }
    else        { Ok(a / b) }
}

fn parse_positive(s: &str) -> Result<u32, String> {
    let n: i64 = s.parse().map_err(|_| format!("'{}' is not a number", s))?;
    if n < 0 { return Err(format!("{} is negative", n)); }
    Ok(n as u32)
}

fn result_basics() {
    println!("--- Result Basics ---");

    // Match
    match divide(10.0, 3.0) {
        Ok(v)  => println!("10/3 = {:.4}", v),
        Err(e) => println!("Error: {}", e),
    }
    match divide(10.0, 0.0) {
        Ok(v)  => println!("Result: {}", v),
        Err(e) => println!("Error: {}", e),
    }

    // Convenience methods
    println!("unwrap_or:  {}", divide(10.0, 0.0).unwrap_or(f64::INFINITY));
    println!("map:        {:?}", divide(10.0, 2.0).map(|v| v * 100.0));
    println!("map_err:    {:?}", divide(10.0, 0.0).map_err(|e| format!("[ERR] {}", e)));
    println!("and_then:   {:?}", divide(10.0, 2.0).and_then(|v| divide(v, 2.0)));
    println!("is_ok:      {} is_err: {}", divide(10.0,2.0).is_ok(), divide(10.0,0.0).is_err());

    // Parsing
    let inputs = ["42", "-5", "abc", "100"];
    for input in &inputs {
        match parse_positive(input) {
            Ok(n)  => println!("  '{}' → {}", input, n),
            Err(e) => println!("  '{}' → ❌ {}", input, e),
        }
    }

    // Collecting Results — fail on first error
    let valid: Vec<&str> = vec!["1","2","3","4"];
    let parsed: Result<Vec<u32>, _> = valid.iter().map(|s| parse_positive(s)).collect();
    println!("All valid: {:?}", parsed);

    let mixed: Vec<&str> = vec!["1","bad","3"];
    let parsed2: Result<Vec<u32>, _> = mixed.iter().map(|s| parse_positive(s)).collect();
    println!("Mixed (fails at 'bad'): {}", parsed2.is_err());
    println!();
}

// ─────────────────────────────────────────────
// ? OPERATOR
// ─────────────────────────────────────────────

fn parse_and_double(s: &str) -> Result<i32, ParseIntError> {
    let n: i32 = s.trim().parse()?;  // ? propagates the error
    Ok(n * 2)
}

fn chain_operations(input: &str) -> Result<f64, Box<dyn std::error::Error>> {
    let n:  i64 = input.trim().parse()?;      // ParseIntError
    let sq: f64 = divide(n as f64, 2.0)
        .map_err(|e| e.into::<Box<dyn std::error::Error>>().unwrap_or_else(|_| "math".into()))?;
    Ok(sq)
}

// Simulate reading and processing data
fn process_data(data: &str) -> Result<Vec<i32>, String> {
    let numbers: Result<Vec<i32>, _> = data
        .lines()
        .filter(|l| !l.trim().is_empty())
        .map(|l| l.trim().parse::<i32>().map_err(|e| format!("Parse error on '{}': {}", l, e)))
        .collect();
    let mut nums = numbers?;
    nums.sort();
    Ok(nums)
}

fn question_mark_operator() {
    println!("--- ? Operator ---");

    let cases = ["42", " 100 ", "abc", "  7  "];
    for s in &cases {
        match parse_and_double(s) {
            Ok(v)  => println!("  parse_and_double('{}') = {}", s.trim(), v),
            Err(e) => println!("  parse_and_double('{}') → ❌ {}", s.trim(), e),
        }
    }

    let data = "10\n20\n30\n40\n50";
    match process_data(data) {
        Ok(nums) => println!("\nProcessed: {:?} (sum={})", nums, nums.iter().sum::<i32>()),
        Err(e)   => println!("Error: {}", e),
    }

    let bad_data = "10\n20\nbad\n40";
    match process_data(bad_data) {
        Ok(nums) => println!("Processed: {:?}", nums),
        Err(e)   => println!("Bad data error: {}", e),
    }
    println!();
}

// ─────────────────────────────────────────────
// CUSTOM ERROR TYPES
// ─────────────────────────────────────────────

#[derive(Debug)]
enum AppError {
    NotFound { resource: String, id: u32 },
    InvalidInput { field: String, message: String },
    Unauthorized,
    ParseError(String),
    IoError(String),
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            AppError::NotFound { resource, id }          => write!(f, "{} with id={} not found", resource, id),
            AppError::InvalidInput { field, message }    => write!(f, "Invalid '{}': {}", field, message),
            AppError::Unauthorized                       => write!(f, "Unauthorized — please login"),
            AppError::ParseError(msg)                    => write!(f, "Parse error: {}", msg),
            AppError::IoError(msg)                       => write!(f, "I/O error: {}", msg),
        }
    }
}

impl std::error::Error for AppError {}

impl From<ParseIntError> for AppError {
    fn from(e: ParseIntError) -> Self { AppError::ParseError(e.to_string()) }
}

// User service using custom errors
#[derive(Debug, Clone)]
struct User { id: u32, name: String, age: u8, admin: bool }

struct UserService { users: Vec<User> }

impl UserService {
    fn new() -> Self {
        Self { users: vec![
            User { id: 1, name: "Alice".into(), age: 30, admin: true },
            User { id: 2, name: "Bob".into(),   age: 17, admin: false },
            User { id: 3, name: "Charlie".into(),age: 25, admin: false },
        ]}
    }

    fn find(&self, id: u32) -> Result<&User, AppError> {
        self.users.iter().find(|u| u.id == id)
            .ok_or(AppError::NotFound { resource: "User".into(), id })
    }

    fn admin_action(&self, user_id: u32, action: &str) -> Result<String, AppError> {
        let user = self.find(user_id)?;
        if !user.admin { return Err(AppError::Unauthorized); }
        Ok(format!("User '{}' executed: {}", user.name, action))
    }

    fn update_age(&mut self, user_id: u32, new_age: &str) -> Result<(), AppError> {
        let age: u8 = new_age.parse().map_err(|_|
            AppError::InvalidInput { field: "age".into(), message: format!("'{}' is not valid", new_age) }
        )?;
        if age > 150 {
            return Err(AppError::InvalidInput { field: "age".into(), message: "unrealistic value".into() });
        }
        let user = self.users.iter_mut().find(|u| u.id == user_id)
            .ok_or(AppError::NotFound { resource: "User".into(), id: user_id })?;
        user.age = age;
        Ok(())
    }
}

fn custom_errors_demo() {
    println!("--- Custom Error Types ---");

    let mut svc = UserService::new();

    let test_cases: Vec<(u32, &str)> = vec![(1,"find"),(99,"find"),(1,"admin"),(2,"admin")];
    for (id, action) in test_cases {
        let result = if action == "find" {
            svc.find(id).map(|u| format!("Found: {:?}", u)).map_err(|e| e.to_string())
        } else {
            svc.admin_action(id, "delete_all").map_err(|e| e.to_string())
        };
        match result {
            Ok(msg)  => println!("  ✅ {}", msg),
            Err(msg) => println!("  ❌ {}", msg),
        }
    }

    println!("\nUpdating ages:");
    for (id, age_str) in &[(1u32,"31"), (2,"200"), (3,"abc")] {
        match svc.update_age(*id, age_str) {
            Ok(())   => println!("  ✅ Updated user {} age to '{}'", id, age_str),
            Err(e)   => println!("  ❌ user {}: {}", id, e),
        }
    }
    println!();
}

// ─────────────────────────────────────────────
// REAL WORLD: validate & transform pipeline
// ─────────────────────────────────────────────

#[derive(Debug)]
struct Config { host: String, port: u16, workers: u8 }

fn parse_config(input: &str) -> Result<Config, AppError> {
    let mut map = std::collections::HashMap::new();
    for line in input.lines() {
        let parts: Vec<&str> = line.splitn(2, '=').collect();
        if parts.len() == 2 { map.insert(parts[0].trim(), parts[1].trim()); }
    }

    let host = map.get("host").ok_or(AppError::InvalidInput {
        field: "host".into(), message: "required field missing".into()
    })?.to_string();

    let port: u16 = map.get("port").ok_or(AppError::InvalidInput {
        field: "port".into(), message: "required field missing".into()
    })?.parse().map_err(|_| AppError::InvalidInput {
        field: "port".into(), message: "must be a number 1-65535".into()
    })?;

    let workers: u8 = map.get("workers").unwrap_or(&"4")
        .parse().map_err(|_| AppError::InvalidInput {
            field: "workers".into(), message: "must be 1-255".into()
        })?;

    Ok(Config { host, port, workers })
}

fn real_world_demo() {
    println!("--- Real-World: Config Parsing ---");

    let valid_config = "host = localhost\nport = 8080\nworkers = 8";
    let missing_port = "host = example.com\nworkers = 4";
    let bad_port     = "host = localhost\nport = notanumber";

    for (label, cfg) in &[("valid", valid_config), ("missing port", missing_port), ("bad port", bad_port)] {
        match parse_config(cfg) {
            Ok(c)  => println!("  ✅ {}: {:?}", label, c),
            Err(e) => println!("  ❌ {}: {}", label, e),
        }
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    #[test] fn test_divide_ok()        { assert!((divide(10.0,2.0).unwrap() - 5.0).abs() < 1e-9); }
    #[test] fn test_divide_err()       { assert!(divide(10.0,0.0).is_err()); }
    #[test] fn test_parse_positive()   { assert_eq!(parse_positive("42").unwrap(), 42); }
    #[test] fn test_parse_negative()   { assert!(parse_positive("-1").is_err()); }
    #[test] fn test_parse_and_double() { assert_eq!(parse_and_double("21").unwrap(), 42); }
    #[test] fn test_process_data()     { assert_eq!(process_data("3\n1\n2").unwrap(), vec![1,2,3]); }
    #[test] fn test_user_not_found()   { let svc = UserService::new(); assert!(svc.find(99).is_err()); }
    #[test] fn test_config_valid() {
        let cfg = parse_config("host=localhost\nport=8080").unwrap();
        assert_eq!(cfg.port, 8080);
    }
}
