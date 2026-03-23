// ============================================================
//  Project 05: File Organizer
//
//  Features:
//  - Scan a directory and organize files by extension
//  - Detect duplicate files by size + name
//  - Generate detailed report
//  - Simulate moves (dry-run) before executing
//  - Statistics: file type distribution, total sizes
//
//  Run: cargo run -- /path/to/messy/folder [--execute]
//  Or:  cargo run -- . (scan current directory)
// ============================================================

use std::collections::HashMap;
use std::env;
use std::fmt;
use std::fs;
use std::path::{Path, PathBuf};

// ─────────────────────────────────────────────
// TYPES
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq, Eq, Hash)]
enum FileCategory {
    Images,
    Documents,
    Videos,
    Audio,
    Code,
    Archives,
    Data,
    Other(String),
}

impl fmt::Display for FileCategory {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            FileCategory::Other(s) => write!(f, "Other-{}", s),
            _                      => write!(f, "{:?}", self),
        }
    }
}

impl FileCategory {
    fn from_extension(ext: &str) -> Self {
        match ext.to_lowercase().as_str() {
            "jpg"|"jpeg"|"png"|"gif"|"bmp"|"svg"|"webp"|"ico" => FileCategory::Images,
            "pdf"|"doc"|"docx"|"txt"|"md"|"odt"|"rtf"|"epub" => FileCategory::Documents,
            "mp4"|"mkv"|"avi"|"mov"|"wmv"|"flv"|"webm"       => FileCategory::Videos,
            "mp3"|"flac"|"wav"|"aac"|"ogg"|"wma"|"m4a"       => FileCategory::Audio,
            "rs"|"py"|"js"|"ts"|"cpp"|"c"|"h"|"go"|"java"|"rb"|"php"|"sh"|"toml"|"yaml"|"json"|"xml" => FileCategory::Code,
            "zip"|"tar"|"gz"|"bz2"|"xz"|"rar"|"7z"          => FileCategory::Archives,
            "csv"|"xlsx"|"db"|"sqlite"|"sql"                 => FileCategory::Data,
            ext                                               => FileCategory::Other(ext.to_string()),
        }
    }
    fn folder_name(&self) -> String {
        match self {
            FileCategory::Other(s) if s.is_empty() => "Misc".into(),
            FileCategory::Other(s) => format!("Other_{}", s.to_uppercase()),
            _                      => format!("{:?}", self),
        }
    }
    fn icon(&self) -> &str {
        match self {
            FileCategory::Images    => "🖼️",
            FileCategory::Documents => "📄",
            FileCategory::Videos    => "🎬",
            FileCategory::Audio     => "🎵",
            FileCategory::Code      => "💻",
            FileCategory::Archives  => "📦",
            FileCategory::Data      => "📊",
            FileCategory::Other(_)  => "📁",
        }
    }
}

#[derive(Debug, Clone)]
struct FileInfo {
    path:     PathBuf,
    name:     String,
    extension:String,
    size:     u64,
    category: FileCategory,
}

impl FileInfo {
    fn new(path: PathBuf) -> Option<Self> {
        let name      = path.file_name()?.to_string_lossy().to_string();
        let extension = path.extension().map(|e| e.to_string_lossy().to_lowercase()).unwrap_or_default();
        let size      = fs::metadata(&path).map(|m| m.len()).unwrap_or(0);
        let category  = FileCategory::from_extension(&extension);
        Some(Self { path, name, extension, size, category })
    }
    fn size_str(&self) -> String { format_size(self.size) }
}

#[derive(Debug)]
struct OrganizerPlan {
    moves:      Vec<(PathBuf, PathBuf)>, // (from, to)
    duplicates: Vec<(FileInfo, FileInfo)>,
    stats:      HashMap<FileCategory, CategoryStats>,
}

#[derive(Debug, Default)]
struct CategoryStats {
    count:      usize,
    total_size: u64,
    extensions: HashMap<String, usize>,
}

// ─────────────────────────────────────────────
// SCANNER
// ─────────────────────────────────────────────

fn scan_directory(dir: &Path) -> Vec<FileInfo> {
    let mut files = Vec::new();
    scan_recursive(dir, &mut files);
    files
}

fn scan_recursive(dir: &Path, files: &mut Vec<FileInfo>) {
    let entries = match fs::read_dir(dir) {
        Ok(e) => e,
        Err(_)=> return,
    };
    for entry in entries.filter_map(|e| e.ok()) {
        let path = entry.path();
        if path.is_dir() {
            // Don't recurse into already-organized folders
            let folder = path.file_name().unwrap_or_default().to_string_lossy();
            if !matches!(folder.as_ref(), "Images"|"Documents"|"Videos"|"Audio"|"Code"|"Archives"|"Data") {
                scan_recursive(&path, files);
            }
        } else if path.is_file() {
            if let Some(info) = FileInfo::new(path) { files.push(info); }
        }
    }
}

fn build_plan(source_dir: &Path, files: &[FileInfo]) -> OrganizerPlan {
    let mut moves   = Vec::new();
    let mut stats: HashMap<FileCategory, CategoryStats> = HashMap::new();

    // Build stats and moves
    for file in files {
        let entry = stats.entry(file.category.clone()).or_default();
        entry.count += 1;
        entry.total_size += file.size;
        *entry.extensions.entry(file.extension.clone()).or_insert(0) += 1;

        let dest_folder = source_dir.join(file.category.folder_name());
        let dest_path   = dest_folder.join(&file.name);
        // Only move if it's not already in the right place
        if file.path.parent() != Some(&dest_folder) {
            moves.push((file.path.clone(), dest_path));
        }
    }

    // Detect duplicates (same name + same size)
    let mut by_key: HashMap<(String, u64), Vec<&FileInfo>> = HashMap::new();
    for file in files {
        by_key.entry((file.name.clone(), file.size)).or_default().push(file);
    }
    let duplicates: Vec<(FileInfo, FileInfo)> = by_key.values()
        .filter(|group| group.len() > 1)
        .filter_map(|group| {
            if group.len() >= 2 { Some((group[0].clone(), group[1].clone())) }
            else { None }
        })
        .collect();

    OrganizerPlan { moves, duplicates, stats }
}

fn execute_plan(plan: &OrganizerPlan) -> (usize, usize) {
    let mut succeeded = 0;
    let mut failed    = 0;
    for (from, to) in &plan.moves {
        if let Some(parent) = to.parent() {
            if let Err(e) = fs::create_dir_all(parent) {
                eprintln!("  ❌ Failed to create dir {:?}: {}", parent, e);
                failed += 1;
                continue;
            }
        }
        match fs::rename(from, to) {
            Ok(())  => { println!("  ✅ {:?} → {:?}", from.file_name().unwrap_or_default(), to.parent().unwrap_or(to)); succeeded += 1; }
            Err(e)  => { eprintln!("  ❌ {:?}: {}", from.file_name().unwrap_or_default(), e); failed += 1; }
        }
    }
    (succeeded, failed)
}

// ─────────────────────────────────────────────
// REPORT
// ─────────────────────────────────────────────

fn print_report(plan: &OrganizerPlan, files: &[FileInfo], dry_run: bool) {
    let total_size: u64 = files.iter().map(|f| f.size).sum();
    println!("\n{:═<65}", "");
    println!("  File Organizer Report");
    println!("{:═<65}", "");
    println!("  Total files scanned: {}", files.len());
    println!("  Total size:          {}", format_size(total_size));
    println!("  Files to move:       {}", plan.moves.len());
    println!("  Potential duplicates:{}", plan.duplicates.len());
    println!("  Mode:                {}", if dry_run { "DRY RUN (use --execute to apply)" } else { "EXECUTE" });

    println!("\n{:─<65}", "");
    println!("  {:<15} {:>8} {:>12} {:<25}", "Category", "Files", "Size", "Extensions");
    println!("{:─<65}", "");

    let mut cats: Vec<(&FileCategory, &CategoryStats)> = plan.stats.iter().collect();
    cats.sort_by(|a, b| b.1.total_size.cmp(&a.1.total_size));
    for (cat, stat) in &cats {
        let exts: Vec<String> = {
            let mut v: Vec<(&String, &usize)> = stat.extensions.iter().collect();
            v.sort_by(|a, b| b.1.cmp(a.1));
            v.iter().take(4).map(|(e,_)| format!(".{}", e)).collect()
        };
        println!("  {} {:<14} {:>8} {:>12} {:<25}",
            cat.icon(), format!("{}", cat), stat.count, format_size(stat.total_size), exts.join(" "));
    }

    if !plan.duplicates.is_empty() {
        println!("\n  ⚠ Potential Duplicates (same name + size):");
        for (a, b) in plan.duplicates.iter().take(5) {
            println!("    {} ({}) duplicated at:", a.name, a.size_str());
            println!("      {:?}", a.path);
            println!("      {:?}", b.path);
        }
    }

    if dry_run && !plan.moves.is_empty() {
        println!("\n  Preview (first 8 moves):");
        for (from, to) in plan.moves.iter().take(8) {
            println!("    {:?} → {}/", from.file_name().unwrap_or_default(), to.parent().and_then(|p| p.file_name()).unwrap_or_default().to_string_lossy());
        }
        if plan.moves.len() > 8 { println!("    ... and {} more", plan.moves.len() - 8); }
    }
    println!("{:═<65}", "");
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Project 05: File Organizer =====\n");

    let args: Vec<String> = env::args().collect();
    let source = if args.len() >= 2 { args[1].as_str() } else { "." };
    let execute = args.contains(&"--execute".to_string());

    let source_path = Path::new(source);
    if !source_path.exists() {
        eprintln!("❌ Path does not exist: {}", source);
        std::process::exit(1);
    }

    println!("📂 Scanning: {:?}", source_path.canonicalize().unwrap_or_else(|_| source_path.to_path_buf()));

    let files = scan_directory(source_path);
    if files.is_empty() {
        println!("No files found to organize.");
        return;
    }

    let plan = build_plan(source_path, &files);
    print_report(&plan, &files, !execute);

    if execute {
        println!("\n🚀 Executing plan...");
        let (ok, fail) = execute_plan(&plan);
        println!("\n✅ Done! {} moved, {} failed.", ok, fail);
    } else {
        println!("\n💡 Run with --execute to apply changes.");
    }
}

// ─────────────────────────────────────────────
// UTILITIES
// ─────────────────────────────────────────────

fn format_size(bytes: u64) -> String {
    const KB: u64 = 1024;
    const MB: u64 = KB * 1024;
    const GB: u64 = MB * 1024;
    if bytes >= GB      { format!("{:.2} GB", bytes as f64 / GB as f64) }
    else if bytes >= MB { format!("{:.2} MB", bytes as f64 / MB as f64) }
    else if bytes >= KB { format!("{:.2} KB", bytes as f64 / KB as f64) }
    else                { format!("{} B",  bytes) }
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test] fn category_images()    { assert_eq!(FileCategory::from_extension("jpg"),  FileCategory::Images); }
    #[test] fn category_code()      { assert_eq!(FileCategory::from_extension("rs"),   FileCategory::Code); }
    #[test] fn category_archives()  { assert_eq!(FileCategory::from_extension("zip"),  FileCategory::Archives); }
    #[test] fn category_other()     { assert_eq!(FileCategory::from_extension("xyz"),  FileCategory::Other("xyz".into())); }
    #[test] fn format_size_bytes()  { assert_eq!(format_size(512), "512 B"); }
    #[test] fn format_size_kb()     { assert_eq!(format_size(2048), "2.00 KB"); }
    #[test] fn format_size_mb()     { assert_eq!(format_size(1_048_576), "1.00 MB"); }
    #[test] fn folder_names()       { assert_eq!(FileCategory::Images.folder_name(), "Images"); }
}
