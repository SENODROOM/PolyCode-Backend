# Rust Course Projects

Five real-world projects that combine everything learned in the course.

---

## Project 01: CLI Task Manager
**File:** `01_cli_task_manager/main.rs`

A full-featured command-line task manager.
- Structs + Enums for domain modelling
- `serde`/`serde_json` for JSON persistence
- File I/O with `fs::read_to_string` / `fs::write`
- CLI arg parsing from `std::env::args`
- Custom `Display` implementations
- `#[cfg(test)]` unit test suite

```toml
[dependencies]
serde      = { version = "1", features = ["derive"] }
serde_json = "1"
```

---

## Project 02: Bank System
**File:** `02_bank_system/main.rs`

A simulated banking system with accounts and transactions.
- Custom error enum with `Display` + `std::error::Error`
- Multiple account types with different interest rates
- Transfer validation (frozen accounts, insufficient funds)
- Transaction history per account
- Account statements

No external dependencies.

---

## Project 03: Inventory Management System
**File:** `03_inventory_system/main.rs`

A product inventory system with sales tracking.
- Generic search and filter functions
- Category-based organisation with enums
- Sales recording and profit calculations
- Low-stock alerts
- CSV export
- 12 unit tests covering all operations

No external dependencies.

---

## Project 04: Mini HTTP Server
**File:** `04_mini_http_server/main.rs`

A working HTTP/1.1 server built from scratch using `TcpListener`.
- Custom HTTP request parser
- Path-based router with function pointers
- Thread pool (4 workers, `Arc<Mutex<Receiver>>`)
- Query parameter parsing
- HTML response generation
- Zero external dependencies

```bash
cargo run
# Then visit: http://localhost:7878
```

---

## Project 05: File Organizer
**File:** `05_file_organizer/main.rs`

Scans a directory and organises files by type.
- Recursive directory scanning with `fs::read_dir`
- Extension-based categorisation (8 categories)
- Duplicate detection by name + size
- Dry-run mode (preview before applying)
- Detailed statistics report
- `--execute` flag to apply changes

```bash
cargo run -- /path/to/messy/folder           # dry run
cargo run -- /path/to/messy/folder --execute # apply
```

---

## Running Any Project

Each project is a standalone binary. Copy the `main.rs` into a new cargo project:

```bash
cargo new project_name
cp main.rs project_name/src/main.rs
cd project_name
# Add dependencies to Cargo.toml if needed
cargo run
cargo test
```
