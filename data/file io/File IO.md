# Rust File I/O

Rust's file I/O is explicit, safe, and composable. Every operation returns a `Result` — errors must be handled, never ignored.

---

## 1. Reading Files

### Read Entire File to String
```rust
use std::fs;

let content = fs::read_to_string("data.txt")?;
println!("{}", content);
```

### Read as Bytes
```rust
let bytes = fs::read("image.png")?;
println!("File size: {} bytes", bytes.len());
```

### Read Line by Line (Memory Efficient)
```rust
use std::fs::File;
use std::io::{BufRead, BufReader};

let file   = File::open("data.txt")?;
let reader = BufReader::new(file);

for (i, line) in reader.lines().enumerate() {
    let line = line?;
    println!("{}: {}", i + 1, line);
}
```

---

## 2. Writing Files

### Write Entire String
```rust
use std::fs;

fs::write("output.txt", "Hello, Rust!\n")?;
```

### Write with More Control
```rust
use std::fs::File;
use std::io::{Write, BufWriter};

let file   = File::create("output.txt")?;
let mut w  = BufWriter::new(file);

writeln!(w, "Line 1")?;
writeln!(w, "Line 2: {}", 42)?;
w.flush()?; // ensure buffer is written
```

### Append to File
```rust
use std::fs::OpenOptions;
use std::io::Write;

let mut file = OpenOptions::new().append(true).open("log.txt")?;
writeln!(file, "New log entry")?;
```

---

## 3. File & Directory Operations

```rust
use std::fs;
use std::path::Path;

// Check if path exists
if Path::new("config.toml").exists() { println!("Config found"); }

// Create directory (and parents)
fs::create_dir_all("output/reports")?;

// Copy a file
fs::copy("source.txt", "backup.txt")?;

// Rename / move
fs::rename("old_name.txt", "new_name.txt")?;

// Delete
fs::remove_file("temp.txt")?;
fs::remove_dir_all("temp_folder")?;

// List directory contents
for entry in fs::read_dir(".")? {
    let entry = entry?;
    println!("{:?}", entry.path());
}
```

---

## 4. Working with Paths

```rust
use std::path::{Path, PathBuf};

let path = Path::new("/home/user/docs/report.pdf");
println!("filename:  {:?}", path.file_name());   // "report.pdf"
println!("stem:      {:?}", path.file_stem());   // "report"
println!("extension: {:?}", path.extension());   // "pdf"
println!("parent:    {:?}", path.parent());      // "/home/user/docs"

// Build paths safely (handles OS separators)
let mut p = PathBuf::from("/home/user");
p.push("projects");
p.push("my_app");
p.set_extension("toml");
println!("Built path: {:?}", p);
```

---

## 5. CSV Parsing (Manual)

```rust
fn parse_csv(content: &str) -> Vec<Vec<String>> {
    content.lines()
        .map(|line| line.split(',').map(|f| f.trim().to_string()).collect())
        .collect()
}

// Usage
let csv = "name,age,city\nAlice,30,NYC\nBob,25,LA";
let rows = parse_csv(csv);
for row in &rows { println!("{:?}", row); }
```

---

## Error Handling Pattern

```rust
use std::io;

fn read_config(path: &str) -> Result<Config, io::Error> {
    let content = fs::read_to_string(path)?;
    // parse content...
    Ok(Config { /* ... */ })
}

// With ? operator, errors propagate automatically
fn main() -> Result<(), Box<dyn std::error::Error>> {
    let config = read_config("config.txt")?;
    Ok(())
}
```

---

## Summary

| Task | Function |
|---|---|
| Read whole file | `fs::read_to_string(path)?` |
| Read bytes | `fs::read(path)?` |
| Read lines lazily | `BufReader::new(file).lines()` |
| Write whole file | `fs::write(path, content)?` |
| Buffered write | `BufWriter::new(File::create(path)?)` |
| Append | `OpenOptions::new().append(true).open(path)?` |
| Create dirs | `fs::create_dir_all(path)?` |

> 💡 Always use `BufReader`/`BufWriter` for large files. Unbuffered I/O makes a syscall per byte — buffered I/O batches them and can be 100× faster.
