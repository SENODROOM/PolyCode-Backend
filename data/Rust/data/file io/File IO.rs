// ============================================================
//  Rust File I/O — Complete Examples
// ============================================================

use std::fs::{self, File, OpenOptions};
use std::io::{self, BufRead, BufReader, BufWriter, Write};
use std::path::{Path, PathBuf};

// ─────────────────────────────────────────────
// SECTION 1: Writing Files
// ─────────────────────────────────────────────

fn demo_write() -> io::Result<()> {
    println!("--- Writing Files ---");

    // Simple write (creates or overwrites)
    fs::write("demo_output.txt", "Hello from Rust!\nLine 2\nLine 3\n")?;
    println!("fs::write → demo_output.txt created");

    // Buffered write for performance
    let file = File::create("demo_buffered.txt")?;
    let mut writer = BufWriter::new(file);
    for i in 1..=5 {
        writeln!(writer, "Buffered line {}: value = {}", i, i * i)?;
    }
    writer.flush()?;
    println!("BufWriter → demo_buffered.txt created (5 lines)");

    // Append to file
    let mut log = OpenOptions::new()
        .create(true)
        .append(true)
        .open("demo_log.txt")?;
    writeln!(log, "[INFO] Application started")?;
    writeln!(log, "[INFO] Processing 100 records")?;
    writeln!(log, "[INFO] Done")?;
    println!("OpenOptions(append) → demo_log.txt written");

    Ok(())
}

// ─────────────────────────────────────────────
// SECTION 2: Reading Files
// ─────────────────────────────────────────────

fn demo_read() -> io::Result<()> {
    println!("\n--- Reading Files ---");

    // Read entire file at once
    let content = fs::read_to_string("demo_output.txt")?;
    println!("fs::read_to_string:\n{}", content);

    // Read as bytes
    let bytes = fs::read("demo_output.txt")?;
    println!("File size: {} bytes", bytes.len());

    // Read line by line (memory efficient for large files)
    println!("Line-by-line (demo_buffered.txt):");
    let file   = File::open("demo_buffered.txt")?;
    let reader = BufReader::new(file);
    for (i, line) in reader.lines().enumerate() {
        println!("  {}: {}", i + 1, line?);
    }

    // Read log file
    println!("\nLog file contents:");
    let log_content = fs::read_to_string("demo_log.txt")?;
    for line in log_content.lines() {
        println!("  {}", line);
    }

    Ok(())
}

// ─────────────────────────────────────────────
// SECTION 3: Directory Operations
// ─────────────────────────────────────────────

fn demo_directories() -> io::Result<()> {
    println!("\n--- Directory Operations ---");

    // Create nested directories
    fs::create_dir_all("demo_dir/sub/nested")?;
    println!("Created: demo_dir/sub/nested/");

    // Write a file inside it
    fs::write("demo_dir/hello.txt", "Hello from a subdirectory!\n")?;
    fs::write("demo_dir/sub/data.csv", "id,name,value\n1,Alice,100\n2,Bob,200\n")?;

    // List directory contents
    println!("Contents of demo_dir/:");
    for entry in fs::read_dir("demo_dir")? {
        let entry = entry?;
        let meta  = entry.metadata()?;
        println!("  {:?}  ({})", entry.file_name(),
            if meta.is_dir() { "dir" } else { "file" });
    }

    // Copy a file
    fs::copy("demo_dir/hello.txt", "demo_dir/hello_backup.txt")?;
    println!("Copied hello.txt → hello_backup.txt");

    // Check existence
    println!("demo_dir/hello.txt exists: {}", Path::new("demo_dir/hello.txt").exists());
    println!("demo_dir/missing.txt exists: {}", Path::new("demo_dir/missing.txt").exists());

    Ok(())
}

// ─────────────────────────────────────────────
// SECTION 4: Path Manipulation
// ─────────────────────────────────────────────

fn demo_paths() {
    println!("\n--- Path Operations ---");

    let path = Path::new("demo_dir/sub/data.csv");
    println!("Full path:  {:?}", path);
    println!("File name:  {:?}", path.file_name());
    println!("Stem:       {:?}", path.file_stem());
    println!("Extension:  {:?}", path.extension());
    println!("Parent:     {:?}", path.parent());
    println!("Is absolute: {}", path.is_absolute());

    // Build a path
    let mut built = PathBuf::from("output");
    built.push("reports");
    built.push("2026");
    built.push("summary");
    built.set_extension("json");
    println!("Built path: {:?}", built);

    // Join paths
    let base = Path::new("/home/user");
    let joined = base.join("projects").join("polycode");
    println!("Joined:     {:?}", joined);
}

// ─────────────────────────────────────────────
// SECTION 5: CSV Parsing
// ─────────────────────────────────────────────

#[derive(Debug)]
struct Record { id: u32, name: String, value: f64 }

fn parse_csv(path: &str) -> io::Result<Vec<Record>> {
    let content = fs::read_to_string(path)?;
    let mut records = Vec::new();

    for (i, line) in content.lines().enumerate() {
        if i == 0 { continue; } // skip header
        let fields: Vec<&str> = line.split(',').collect();
        if fields.len() < 3 { continue; }
        records.push(Record {
            id:    fields[0].parse().unwrap_or(0),
            name:  fields[1].to_string(),
            value: fields[2].parse().unwrap_or(0.0),
        });
    }
    Ok(records)
}

fn write_csv(path: &str, records: &[Record]) -> io::Result<()> {
    let mut w = BufWriter::new(File::create(path)?);
    writeln!(w, "id,name,value")?;
    for r in records {
        writeln!(w, "{},{},{:.2}", r.id, r.name, r.value)?;
    }
    w.flush()
}

fn demo_csv() -> io::Result<()> {
    println!("\n--- CSV Processing ---");

    let records = parse_csv("demo_dir/sub/data.csv")?;
    println!("Parsed {} records:", records.len());
    for r in &records { println!("  {:?}", r); }

    let total: f64 = records.iter().map(|r| r.value).sum();
    println!("Total value: {:.2}", total);

    // Write enriched CSV
    write_csv("demo_dir/output.csv", &records)?;
    println!("Wrote enriched CSV → demo_dir/output.csv");

    Ok(())
}

// ─────────────────────────────────────────────
// SECTION 6: Error Handling Patterns
// ─────────────────────────────────────────────

fn read_config(path: &str) -> Result<String, Box<dyn std::error::Error>> {
    if !Path::new(path).exists() {
        // Return default config if file missing
        return Ok("default_config".to_string());
    }
    Ok(fs::read_to_string(path)?)
}

fn demo_errors() {
    println!("\n--- Error Handling ---");

    // Non-existent file with graceful fallback
    match fs::read_to_string("nonexistent.txt") {
        Ok(content) => println!("Content: {}", content),
        Err(e)      => println!("Graceful error: {} (kind: {:?})", e, e.kind()),
    }

    // Using our config reader with fallback
    match read_config("missing_config.toml") {
        Ok(cfg)  => println!("Config loaded: {}", cfg),
        Err(e)   => println!("Config error: {}", e),
    }
}

// ─────────────────────────────────────────────
// Cleanup helper
// ─────────────────────────────────────────────

fn cleanup() {
    let _ = fs::remove_file("demo_output.txt");
    let _ = fs::remove_file("demo_buffered.txt");
    let _ = fs::remove_file("demo_log.txt");
    let _ = fs::remove_dir_all("demo_dir");
    println!("\n🧹 Demo files cleaned up");
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Rust File I/O Demo =====\n");

    if let Err(e) = demo_write()       { eprintln!("Write error: {}", e); }
    if let Err(e) = demo_read()        { eprintln!("Read error: {}", e); }
    if let Err(e) = demo_directories() { eprintln!("Dir error: {}", e); }
    demo_paths();
    if let Err(e) = demo_csv()         { eprintln!("CSV error: {}", e); }
    demo_errors();
    cleanup();

    println!("\n✅ File I/O demo complete!");
}

#[cfg(test)]
mod tests {
    use super::*;
    use std::io::Write;

    #[test]
    fn test_write_and_read() {
        let path = "test_io_temp.txt";
        fs::write(path, "hello rust").unwrap();
        let content = fs::read_to_string(path).unwrap();
        assert_eq!(content, "hello rust");
        fs::remove_file(path).unwrap();
    }

    #[test]
    fn test_path_extension() {
        let p = Path::new("report.csv");
        assert_eq!(p.extension().unwrap(), "csv");
        assert_eq!(p.file_stem().unwrap(), "report");
    }

    #[test]
    fn test_nonexistent_read_error() {
        let result = fs::read_to_string("definitely_does_not_exist.txt");
        assert!(result.is_err());
        assert_eq!(result.unwrap_err().kind(), io::ErrorKind::NotFound);
    }
}
