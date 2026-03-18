# Project 01: CLI Task Manager

A command-line task manager that persists tasks to a JSON file.

## Skills Practiced
- Structs, Enums, impl blocks
- Serde JSON serialization/deserialization
- File I/O
- Command-line argument parsing
- Error handling with Result
- Pattern matching

## Setup

```toml
# Cargo.toml
[dependencies]
serde      = { version = "1", features = ["derive"] }
serde_json = "1"
```

## Usage

```bash
# Add tasks
cargo run -- add "Buy groceries"
cargo run -- add "Finish Rust course" --priority high
cargo run -- add "Read a book" -p low

# List tasks
cargo run -- list
cargo run -- list --filter pending
cargo run -- list -f done

# Complete a task
cargo run -- done 1

# Delete a task
cargo run -- delete 2

# Stats
cargo run -- stats

# Clear completed tasks
cargo run -- clear
```

## Sample Output

```
──────────────────────────────────────────────────────────────────────
  ID   Title                                      Priority     Created
──────────────────────────────────────────────────────────────────────
[  1] ✅ Buy groceries                            🟡 Medium  | 2026-03-01
[  2] ⏳ Finish Rust course                       🔴 High    | 2026-03-01
[  3] ⏳ Read a book                              🟢 Low     | 2026-03-01
──────────────────────────────────────────────────────────────────────
Total: 3  Pending: 2  Done: 1
```

## Key Concepts

- `serde` derives automatic JSON serialization for structs/enums
- `TaskStore::load()` reads from file, falls back to empty store
- `TaskStore::save()` writes pretty-printed JSON after every change
- Commands parsed from `std::env::args()` — no extra crate needed
