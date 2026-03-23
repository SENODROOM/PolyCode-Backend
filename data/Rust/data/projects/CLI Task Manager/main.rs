// ============================================================
//  Project 01: CLI Task Manager
//
//  Features:
//  - Add, complete, delete, list tasks
//  - Persist tasks to JSON file
//  - Filter by status (all / pending / done)
//  - Priority levels (Low, Medium, High)
//
//  Cargo.toml:
//  [dependencies]
//  serde       = { version = "1", features = ["derive"] }
//  serde_json  = "1"
//
//  Usage:
//    cargo run -- add "Buy groceries" --priority high
//    cargo run -- add "Read Rust book"
//    cargo run -- list
//    cargo run -- list --filter pending
//    cargo run -- done 1
//    cargo run -- delete 2
//    cargo run -- clear
// ============================================================

use serde::{Deserialize, Serialize};
use std::env;
use std::fmt;
use std::fs;
use std::path::Path;

// ─────────────────────────────────────────────
// DATA TYPES
// ─────────────────────────────────────────────

#[derive(Debug, Clone, Serialize, Deserialize, PartialEq)]
enum Priority { Low, Medium, High }

impl fmt::Display for Priority {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            Priority::Low    => write!(f, "🟢 Low"),
            Priority::Medium => write!(f, "🟡 Medium"),
            Priority::High   => write!(f, "🔴 High"),
        }
    }
}

impl Priority {
    fn from_str(s: &str) -> Self {
        match s.to_lowercase().as_str() {
            "high" | "h"   => Priority::High,
            "medium" | "m" => Priority::Medium,
            _              => Priority::Low,
        }
    }
}

#[derive(Debug, Clone, Serialize, Deserialize)]
struct Task {
    id:       u32,
    title:    String,
    done:     bool,
    priority: Priority,
    created:  String,
}

impl Task {
    fn new(id: u32, title: String, priority: Priority) -> Self {
        Self { id, title, done: false, priority, created: timestamp() }
    }
    fn status_icon(&self) -> &str { if self.done { "✅" } else { "⏳" } }
}

impl fmt::Display for Task {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "[{:>3}] {} {:<40} {} | {}",
            self.id, self.status_icon(),
            self.title, self.priority, self.created)
    }
}

// ─────────────────────────────────────────────
// TASK STORE
// ─────────────────────────────────────────────

const DATA_FILE: &str = "tasks.json";

#[derive(Debug, Serialize, Deserialize)]
struct TaskStore { tasks: Vec<Task>, next_id: u32 }

impl TaskStore {
    fn new() -> Self { Self { tasks: Vec::new(), next_id: 1 } }

    fn load() -> Self {
        if Path::new(DATA_FILE).exists() {
            let data = fs::read_to_string(DATA_FILE).unwrap_or_default();
            serde_json::from_str(&data).unwrap_or_else(|_| Self::new())
        } else {
            Self::new()
        }
    }

    fn save(&self) {
        let json = serde_json::to_string_pretty(self).expect("Failed to serialize");
        fs::write(DATA_FILE, json).expect("Failed to write tasks.json");
    }

    fn add(&mut self, title: String, priority: Priority) -> &Task {
        let task = Task::new(self.next_id, title, priority);
        self.next_id += 1;
        self.tasks.push(task);
        self.tasks.last().unwrap()
    }

    fn complete(&mut self, id: u32) -> Result<(), String> {
        self.tasks.iter_mut()
            .find(|t| t.id == id)
            .map(|t| { t.done = true; })
            .ok_or(format!("Task #{} not found", id))
    }

    fn delete(&mut self, id: u32) -> Result<String, String> {
        let pos = self.tasks.iter().position(|t| t.id == id)
            .ok_or(format!("Task #{} not found", id))?;
        Ok(self.tasks.remove(pos).title)
    }

    fn list(&self, filter: &str) -> Vec<&Task> {
        self.tasks.iter().filter(|t| match filter {
            "done"    => t.done,
            "pending" => !t.done,
            _         => true,
        }).collect()
    }

    fn clear_done(&mut self) -> usize {
        let before = self.tasks.len();
        self.tasks.retain(|t| !t.done);
        before - self.tasks.len()
    }

    fn stats(&self) -> (usize, usize, usize) {
        let total   = self.tasks.len();
        let done    = self.tasks.iter().filter(|t| t.done).count();
        let pending = total - done;
        (total, pending, done)
    }
}

// ─────────────────────────────────────────────
// CLI COMMANDS
// ─────────────────────────────────────────────

enum Command {
    Add { title: String, priority: Priority },
    List { filter: String },
    Done { id: u32 },
    Delete { id: u32 },
    Clear,
    Stats,
    Help,
}

fn parse_args(args: &[String]) -> Result<Command, String> {
    if args.len() < 2 { return Ok(Command::Help); }
    match args[1].as_str() {
        "add" => {
            if args.len() < 3 { return Err("Usage: add \"task title\" [--priority low|medium|high]".into()); }
            let title    = args[2].clone();
            let priority = if args.len() >= 5 && (args[3] == "--priority" || args[3] == "-p") {
                Priority::from_str(&args[4])
            } else { Priority::Medium };
            Ok(Command::Add { title, priority })
        }
        "list" | "ls" => {
            let filter = if args.len() >= 4 && (args[2] == "--filter" || args[2] == "-f") {
                args[3].clone()
            } else { "all".to_string() };
            Ok(Command::List { filter })
        }
        "done" | "complete" => {
            let id = args.get(2).and_then(|s| s.parse().ok()).ok_or("Usage: done <id>")?;
            Ok(Command::Done { id })
        }
        "delete" | "rm" => {
            let id = args.get(2).and_then(|s| s.parse().ok()).ok_or("Usage: delete <id>")?;
            Ok(Command::Delete { id })
        }
        "clear" => Ok(Command::Clear),
        "stats" => Ok(Command::Stats),
        _       => Ok(Command::Help),
    }
}

fn run(store: &mut TaskStore, cmd: Command) {
    match cmd {
        Command::Add { title, priority } => {
            let task = store.add(title, priority);
            println!("✅ Added #{}: {}", task.id, task.title);
            store.save();
        }
        Command::List { filter } => {
            let tasks = store.list(&filter);
            if tasks.is_empty() {
                println!("No tasks found (filter: {})", filter);
                return;
            }
            println!("\n{:─<70}", "");
            println!("{:>5}   {:<42} {:>10}   {}", "ID", "Title", "Priority", "Created");
            println!("{:─<70}", "");
            for task in tasks { println!("{}", task); }
            println!("{:─<70}", "");
            let (total, pending, done) = store.stats();
            println!("Total: {}  Pending: {}  Done: {}", total, pending, done);
        }
        Command::Done { id } => match store.complete(id) {
            Ok(())   => { println!("✅ Task #{} marked as done", id); store.save(); }
            Err(e)   => println!("❌ {}", e),
        },
        Command::Delete { id } => match store.delete(id) {
            Ok(title) => { println!("🗑  Deleted #{}: {}", id, title); store.save(); }
            Err(e)    => println!("❌ {}", e),
        },
        Command::Clear => {
            let removed = store.clear_done();
            println!("🧹 Cleared {} completed task(s)", removed);
            store.save();
        }
        Command::Stats => {
            let (total, pending, done) = store.stats();
            let pct = if total > 0 { done * 100 / total } else { 0 };
            println!("📊 Task Statistics");
            println!("   Total:   {}", total);
            println!("   Pending: {}", pending);
            println!("   Done:    {} ({}%)", done, pct);
        }
        Command::Help => {
            println!("Task Manager — Usage:");
            println!("  add \"title\" [-p low|medium|high]  Add a task");
            println!("  list [-f all|pending|done]         List tasks");
            println!("  done <id>                          Mark as done");
            println!("  delete <id>                        Delete task");
            println!("  clear                              Remove done tasks");
            println!("  stats                              Show statistics");
        }
    }
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    let args: Vec<String> = env::args().collect();
    let mut store = TaskStore::load();

    match parse_args(&args) {
        Ok(cmd)  => run(&mut store, cmd),
        Err(msg) => eprintln!("❌ Error: {}", msg),
    }
}

fn timestamp() -> String {
    // Minimal timestamp without external crate
    use std::time::{SystemTime, UNIX_EPOCH};
    let secs = SystemTime::now().duration_since(UNIX_EPOCH).unwrap().as_secs();
    let days   = secs / 86400;
    let years  = 1970 + days / 365;
    let months = (days % 365) / 30 + 1;
    let day    = (days % 365) % 30 + 1;
    format!("{:04}-{:02}-{:02}", years, months, day)
}

// ─────────────────────────────────────────────
// TESTS
// ─────────────────────────────────────────────

#[cfg(test)]
mod tests {
    use super::*;

    fn fresh_store() -> TaskStore { TaskStore::new() }

    #[test] fn add_task()          { let mut s = fresh_store(); s.add("Test".into(), Priority::Low); assert_eq!(s.tasks.len(), 1); }
    #[test] fn add_increments_id() { let mut s = fresh_store(); s.add("A".into(), Priority::Low); s.add("B".into(), Priority::High); assert_eq!(s.tasks[1].id, 2); }
    #[test] fn complete_task()     { let mut s = fresh_store(); s.add("Test".into(), Priority::Low); s.complete(1).unwrap(); assert!(s.tasks[0].done); }
    #[test] fn complete_missing()  { let mut s = fresh_store(); assert!(s.complete(99).is_err()); }
    #[test] fn delete_task()       { let mut s = fresh_store(); s.add("Del".into(), Priority::Low); s.delete(1).unwrap(); assert!(s.tasks.is_empty()); }
    #[test] fn filter_pending()    { let mut s = fresh_store(); s.add("A".into(), Priority::Low); s.add("B".into(), Priority::Low); s.complete(1).unwrap(); assert_eq!(s.list("pending").len(), 1); }
    #[test] fn filter_done()       { let mut s = fresh_store(); s.add("A".into(), Priority::Low); s.complete(1).unwrap(); assert_eq!(s.list("done").len(), 1); }
    #[test] fn clear_done()        { let mut s = fresh_store(); s.add("A".into(), Priority::Low); s.add("B".into(), Priority::Low); s.complete(1).unwrap(); s.clear_done(); assert_eq!(s.tasks.len(), 1); }
    #[test] fn priority_from_str() { assert_eq!(Priority::from_str("high"), Priority::High); assert_eq!(Priority::from_str("low"), Priority::Low); }
}
