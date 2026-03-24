# File I/O Operations Examples

This file contains 15 comprehensive examples demonstrating file input/output operations in C. File I/O is essential for data persistence, configuration management, and external data processing.

## 📚 File I/O Overview

### File Operations
- **Opening**: `fopen()` with various modes
- **Reading**: `fread()`, `fgets()`, `fgetc()`
- **Writing**: `fwrite()`, `fprintf()`, `fputc()`
- **Positioning**: `fseek()`, `ftell()`, `rewind()`
- **Closing**: `fclose()`

### File Modes
- **"r"**: Read (file must exist)
- **"w"**: Write (creates new or overwrites)
- **"a"**: Append (adds to end)
- **"r+"**: Read and write (file must exist)
- **"w+"**: Read and write (creates new or overwrites)
- **"a+"**: Read and append

### Binary vs Text
- **Text**: Human-readable, line endings translated
- **Binary**: Raw data, exact byte representation

## 🔍 Example List

### 1. Basic File Writing
**Purpose**: Create and write to a text file
**Functions**: `fopen()`, `fprintf()`, `fclose()`
**Key Points**: Error checking, proper file closure

```c
FILE *file = fopen("example.txt", "w");
if (file == NULL) {
    printf("Error opening file!\n");
    return;
}

fprintf(file, "Hello, World!\n");
fclose(file);
```

### 2. Basic File Reading
**Purpose**: Read content from a text file
**Functions**: `fopen()`, `fgets()`, `fclose()`
**Key Points**: Line-by-line reading, buffer management

### 3. Character-by-Character Reading
**Purpose**: Process file character by character
**Functions**: `fgetc()`, EOF detection
**Applications**: Text analysis, character counting

```c
char ch;
while ((ch = fgetc(file)) != EOF) {
    // Process character
    charCount++;
}
```

### 4. File Appending
**Purpose**: Add content to existing file
**Mode**: "a" (append)
**Key Points**: Preserves existing content

### 5. Binary File Operations
**Purpose**: Read/write binary data
**Functions**: `fwrite()`, `fread()`
**Applications**: Structured data, serialization

```c
// Write binary data
fwrite(data, sizeof(element_type), count, file);

// Read binary data
fread(buffer, sizeof(element_type), count, file);
```

### 6. Struct File Operations
**Purpose**: Save and load structured data
**Technique**: Direct struct I/O
**Applications**: Database storage, configuration files

```c
struct Student {
    int id;
    char name[50];
    float gpa;
};

fwrite(&student, sizeof(struct Student), 1, file);
```

### 7. File Position Operations
**Purpose**: Navigate within files
**Functions**: `fseek()`, `ftell()`, `rewind()`
**Applications**: Random access, file analysis

```c
// Seek to end
fseek(file, 0, SEEK_END);
long size = ftell(file);

// Seek to beginning
fseek(file, 0, SEEK_SET);
```

### 8. Error Handling in File Operations
**Purpose**: Robust file operation handling
**Techniques**: `errno`, return value checking
**Key Points**: Graceful error recovery

```c
FILE *file = fopen("file.txt", "r");
if (file == NULL) {
    if (errno == ENOENT) {
        printf("File does not exist\n");
    }
}
```

### 9. File Copying
**Purpose**: Duplicate file contents
**Method**: Character-by-character or block copying
**Applications**: Backup operations, file management

### 10. File Search and Replace
**Purpose**: Modify file contents
**Technique**: Read-modify-write pattern
**Applications**: Text processing, configuration updates

### 11. Temporary Files
**Purpose**: Create files with automatic cleanup
**Function**: `tmpfile()`
**Applications**: Intermediate processing, security

```c
FILE *temp = tmpfile();
// Use temporary file
fclose(temp); // Automatically deleted
```

### 12. File Existence Check
**Purpose**: Verify file availability
**Method**: Attempt to open file
**Applications**: Pre-operation validation

### 13. File Information
**Purpose**: Gather file metadata
**Operations**: Size calculation, line counting
**Applications**: File analysis, progress reporting

### 14. CSV File Processing
**Purpose**: Handle comma-separated values
**Technique**: String tokenization
**Applications**: Data import/export, spreadsheet handling

```c
char *token = strtok(line, ",");
while (token != NULL) {
    // Process field
    token = strtok(NULL, ",");
}
```

### 15. Buffered File Operations
**Purpose**: Optimize I/O performance
**Function**: `setvbuf()`
**Applications**: Large file processing, performance tuning

## 💡 Key File I/O Concepts

### File Pointers
```c
FILE *file; // File handle
file = fopen("filename", "mode"); // Open file
// Use file...
fclose(file); // Close file
```

### Standard Streams
```c
stdin   // Standard input (keyboard)
stdout  // Standard output (screen)
stderr  // Standard error (screen)
```

### File Positioning
- **SEEK_SET**: From beginning
- **SEEK_CUR**: From current position
- **SEEK_END**: From end

### Error Handling
```c
if (file == NULL) {
    perror("Error message"); // Print system error
    // Handle error
}
```

## 🚀 Advanced Techniques

### 1. Memory-Mapped Files
```c
// Platform-specific (POSIX/Windows)
// Map file to memory for direct access
```

### 2. File Locking
```c
// Prevent concurrent access
// Platform-specific implementations
```

### 3. Directory Operations
```c
// List directory contents
// Platform-specific (dirent.h on Unix)
```

### 4. File Attributes
```c
// Get file metadata
// Platform-specific (stat.h on Unix)
```

## 📊 Performance Considerations

### Buffering Strategies
| Buffer Type | Use Case | Performance |
|-------------|----------|-------------|
| No Buffering | Real-time I/O | Slower |
| Line Buffering | Text files | Balanced |
| Full Buffering | Binary files | Fastest |

### I/O Optimization
1. **Buffer Size**: Larger buffers for large files
2. **Block Operations**: Use `fread()`/`fwrite()` for bulk data
3. **Minimize Seeks**: Sequential access is faster
4. **Batch Operations**: Group multiple small writes

## 🧪 Testing Strategies

### 1. File Creation Tests
```c
void testFileCreation() {
    FILE *file = fopen("test.txt", "w");
    assert(file != NULL);
    fclose(file);
    
    // Verify file exists
    assert(fileExists("test.txt"));
}
```

### 2. Content Verification
```c
void testFileContent() {
    // Write known content
    // Read back and verify
    assert(strcmp(content, expected) == 0);
}
```

### 3. Error Handling Tests
```c
void testErrorHandling() {
    FILE *file = fopen("/nonexistent/path/file.txt", "r");
    assert(file == NULL);
    assert(errno == ENOENT);
}
```

### 4. Binary Data Tests
```c
void testBinaryIO() {
    // Write binary data
    // Read back and verify exact match
    assert(memcmp(original, read, size) == 0);
}
```

## ⚠️ Common Pitfalls

### 1. Forgetting to Close Files
```c
// Wrong
FILE *file = fopen("data.txt", "r");
// Use file...
// Forgot: fclose(file);

// Right
FILE *file = fopen("data.txt", "r");
// Use file...
fclose(file);
```

### 2. Buffer Overflows
```c
// Wrong
char buffer[10];
fgets(buffer, 100, file); // Potential overflow

// Right
char buffer[10];
fgets(buffer, sizeof(buffer), file);
```

### 3. Ignoring Return Values
```c
// Wrong
fread(buffer, 1, size, file); // Ignore return value

// Right
size_t read = fread(buffer, 1, size, file);
if (read != size) {
    // Handle partial read
}
```

### 4. Text vs Binary Mode
```c
// Wrong for binary data
FILE *file = fopen("data.bin", "r"); // Text mode

// Right for binary data
FILE *file = fopen("data.bin", "rb"); // Binary mode
```

### 5. Path Separator Issues
```c
// Platform-dependent
FILE *file = fopen("data\\file.txt", "r"); // Windows only

// Better approach
FILE *file = fopen("data/file.txt", "r"); // Use forward slashes
```

## 🔧 Real-World Applications

### 1. Configuration Files
```c
// Read application settings
FILE *config = fopen("config.ini", "r");
// Parse key-value pairs
```

### 2. Data Logging
```c
// Write application logs
FILE *log = fopen("app.log", "a");
fprintf(log, "[%s] %s\n", timestamp, message);
```

### 3. Data Import/Export
```c
// CSV processing
// JSON parsing
// XML handling
```

### 4. Database Operations
```c
// Simple file-based database
// Record management
// Index files
```

### 5. File Utilities
```c
// File copy/move operations
// Directory traversal
// File compression
```

## 🎓 Best Practices

### 1. Always Check Return Values
```c
FILE *file = fopen("data.txt", "r");
if (file == NULL) {
    // Handle error
    return;
}
```

### 2. Use Appropriate Buffer Sizes
```c
#define BUFFER_SIZE 4096
char buffer[BUFFER_SIZE];
```

### 3. Close Files Promptly
```c
FILE *file = fopen("data.txt", "r");
if (file) {
    // Use file...
    fclose(file); // Close as soon as done
}
```

### 4. Handle Platform Differences
```c
#ifdef _WIN32
    // Windows-specific code
#else
    // Unix-specific code
#endif
```

### 5. Use Safe String Operations
```c
fgets(buffer, sizeof(buffer), file); // Safe
// vs
gets(buffer); // Dangerous, deprecated
```

## 🔄 File I/O Patterns

### 1. Read-Process-Write Pattern
```c
// Read input file
FILE *input = fopen("input.txt", "r");
// Process data
// Write output file
FILE *output = fopen("output.txt", "w");
```

### 2. Streaming Pattern
```c
// Process large files without loading entirely
while (fgets(line, sizeof(line), file)) {
    // Process line by line
}
```

### 3. Buffer-and-Flush Pattern
```c
// Accumulate data in buffer
// Write when buffer is full
if (bufferFull) {
    fwrite(buffer, 1, bufferSize, file);
    bufferIndex = 0;
}
```

## 🧠 Debugging File Operations

### 1. File Handle Validation
```c
assert(file != NULL);
assert(ferror(file) == 0);
```

### 2. Content Verification
```c
// Print file contents for debugging
char ch;
while ((ch = fgetc(file)) != EOF) {
    putchar(ch);
}
```

### 3. Position Tracking
```c
long pos = ftell(file);
printf("Current position: %ld\n", pos);
```

### 4. Error Code Inspection
```c
if (ferror(file)) {
    printf("File error occurred\n");
    clearerr(file); // Clear error flags
}
```

File I/O is fundamental to most real-world applications. Master these operations to create robust data persistence and external data processing capabilities!
