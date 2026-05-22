#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>

// =============================================================================
// FILE I/O OPERATIONS EXAMPLES
// =============================================================================

// Example 1: Basic File Writing
void basicFileWriting() {
    printf("=== Example 1: Basic File Writing ===\n");
    
    FILE *file = fopen("example.txt", "w");
    if (file == NULL) {
        printf("Error opening file for writing!\n");
        return;
    }
    
    fprintf(file, "Hello, World!\n");
    fprintf(file, "This is a sample file.\n");
    fprintf(file, "File I/O operations in C.\n");
    
    fclose(file);
    printf("File 'example.txt' created successfully.\n\n");
}

// Example 2: Basic File Reading
void basicFileReading() {
    printf("=== Example 2: Basic File Reading ===\n");
    
    FILE *file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Error opening file for reading!\n");
        return;
    }
    
    char buffer[256];
    printf("File contents:\n");
    while (fgets(buffer, sizeof(buffer), file) != NULL) {
        printf("%s", buffer);
    }
    
    fclose(file);
    printf("\n\n");
}

// Example 3: Character-by-Character Reading
void characterByCharacterReading() {
    printf("=== Example 3: Character-by-Character Reading ===\n");
    
    FILE *file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Error opening file!\n");
        return;
    }
    
    int charCount = 0;
    int wordCount = 0;
    int lineCount = 0;
    int inWord = 0;
    
    char ch;
    while ((ch = fgetc(file)) != EOF) {
        charCount++;
        
        if (ch == '\n') {
            lineCount++;
            inWord = 0;
        } else if (isspace(ch)) {
            inWord = 0;
        } else {
            if (!inWord) {
                wordCount++;
                inWord = 1;
            }
        }
    }
    
    fclose(file);
    printf("Statistics:\n");
    printf("Characters: %d\n", charCount);
    printf("Words: %d\n", wordCount);
    printf("Lines: %d\n\n", lineCount);
}

// Example 4: File Appending
void fileAppending() {
    printf("=== Example 4: File Appending ===\n");
    
    FILE *file = fopen("example.txt", "a");
    if (file == NULL) {
        printf("Error opening file for appending!\n");
        return;
    }
    
    fprintf(file, "This line was appended.\n");
    fprintf(file, "Appended content example.\n");
    
    fclose(file);
    printf("Content appended to file.\n\n");
}

// Example 5: Binary File Operations
void binaryFileOperations() {
    printf("=== Example 5: Binary File Operations ===\n");
    
    // Write binary data
    FILE *file = fopen("data.bin", "wb");
    if (file == NULL) {
        printf("Error opening binary file for writing!\n");
        return;
    }
    
    int numbers[] = {10, 20, 30, 40, 50};
    int size = sizeof(numbers) / sizeof(numbers[0]);
    
    fwrite(numbers, sizeof(int), size, file);
    fclose(file);
    
    // Read binary data
    file = fopen("data.bin", "rb");
    if (file == NULL) {
        printf("Error opening binary file for reading!\n");
        return;
    }
    
    int readNumbers[5];
    fread(readNumbers, sizeof(int), size, file);
    fclose(file);
    
    printf("Binary data read:\n");
    for (int i = 0; i < size; i++) {
        printf("%d ", readNumbers[i]);
    }
    printf("\n\n");
}

// Example 6: Struct File Operations
struct Student {
    int id;
    char name[50];
    float gpa;
};

void structFileOperations() {
    printf("=== Example 6: Struct File Operations ===\n");
    
    // Write struct data
    FILE *file = fopen("students.dat", "wb");
    if (file == NULL) {
        printf("Error opening file for struct writing!\n");
        return;
    }
    
    struct Student students[] = {
        {1, "Alice Johnson", 3.8},
        {2, "Bob Smith", 3.5},
        {3, "Charlie Brown", 3.9}
    };
    
    int numStudents = sizeof(students) / sizeof(students[0]);
    fwrite(students, sizeof(struct Student), numStudents, file);
    fclose(file);
    
    // Read struct data
    file = fopen("students.dat", "rb");
    if (file == NULL) {
        printf("Error opening file for struct reading!\n");
        return;
    }
    
    struct Student readStudents[3];
    fread(readStudents, sizeof(struct Student), numStudents, file);
    fclose(file);
    
    printf("Student records:\n");
    for (int i = 0; i < numStudents; i++) {
        printf("ID: %d, Name: %s, GPA: %.2f\n", 
               readStudents[i].id, readStudents[i].name, readStudents[i].gpa);
    }
    printf("\n");
}

// Example 7: File Position Operations
void filePositionOperations() {
    printf("=== Example 7: File Position Operations ===\n");
    
    FILE *file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Error opening file!\n");
        return;
    }
    
    // Get file size
    fseek(file, 0, SEEK_END);
    long fileSize = ftell(file);
    printf("File size: %ld bytes\n", fileSize);
    
    // Reset to beginning
    fseek(file, 0, SEEK_SET);
    
    // Read first 10 characters
    char buffer[11];
    fread(buffer, 1, 10, file);
    buffer[10] = '\0';
    printf("First 10 characters: %s\n", buffer);
    
    // Get current position
    long currentPos = ftell(file);
    printf("Current position: %ld\n", currentPos);
    
    // Seek to middle
    fseek(file, fileSize / 2, SEEK_SET);
    char midChar = fgetc(file);
    printf("Character at middle: %c\n", midChar);
    
    fclose(file);
    printf("\n");
}

// Example 8: Error Handling in File Operations
void errorHandlingExample() {
    printf("=== Example 8: Error Handling ===\n");
    
    const char *filename = "nonexistent.txt";
    FILE *file = fopen(filename, "r");
    
    if (file == NULL) {
        printf("Error opening '%s': ", filename);
        
        // Check specific error types
        if (errno == ENOENT) {
            printf("File does not exist.\n");
        } else if (errno == EACCES) {
            printf("Permission denied.\n");
        } else {
            printf("Unknown error (errno: %d).\n", errno);
        }
    } else {
        fclose(file);
    }
    
    // Safe file opening with error checking
    file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Failed to open file\n");
        return;
    }
    
    // Check for read errors
    char buffer[100];
    if (fgets(buffer, sizeof(buffer), file) == NULL) {
        if (feof(file)) {
            printf("Reached end of file\n");
        } else if (ferror(file)) {
            printf("Error reading file\n");
        }
    }
    
    fclose(file);
    printf("\n");
}

// Example 9: File Copying
void fileCopying() {
    printf("=== Example 9: File Copying ===\n");
    
    FILE *source = fopen("example.txt", "r");
    if (source == NULL) {
        printf("Error opening source file!\n");
        return;
    }
    
    FILE *dest = fopen("copy.txt", "w");
    if (dest == NULL) {
        printf("Error opening destination file!\n");
        fclose(source);
        return;
    }
    
    char ch;
    while ((ch = fgetc(source)) != EOF) {
        fputc(ch, dest);
    }
    
    fclose(source);
    fclose(dest);
    printf("File copied successfully to 'copy.txt'\n\n");
}

// Example 10: File Search and Replace
void fileSearchAndReplace() {
    printf("=== Example 10: File Search and Replace ===\n");
    
    FILE *file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Error opening file!\n");
        return;
    }
    
    // Read entire file into memory
    fseek(file, 0, SEEK_END);
    long fileSize = ftell(file);
    fseek(file, 0, SEEK_SET);
    
    char *content = (char*)malloc(fileSize + 1);
    fread(content, 1, fileSize, file);
    content[fileSize] = '\0';
    fclose(file);
    
    // Search and replace
    char *search = "Hello";
    char *replace = "Hi";
    char *pos = content;
    int replaceCount = 0;
    
    while ((pos = strstr(pos, search)) != NULL) {
        // Check if replacement fits
        if (strlen(replace) <= strlen(search)) {
            memcpy(pos, replace, strlen(replace));
            replaceCount++;
        }
        pos += strlen(search);
    }
    
    // Write back to file
    file = fopen("example.txt", "w");
    if (file != NULL) {
        fwrite(content, 1, strlen(content), file);
        fclose(file);
        printf("Replaced %d occurrences of '%s' with '%s'\n", replaceCount, search, replace);
    }
    
    free(content);
    printf("\n");
}

// Example 11: Temporary Files
void temporaryFiles() {
    printf("=== Example 11: Temporary Files ===\n");
    
    // Create temporary file
    FILE *tempFile = tmpfile();
    if (tempFile == NULL) {
        printf("Error creating temporary file!\n");
        return;
    }
    
    // Write to temporary file
    fprintf(tempFile, "This is temporary data\n");
    fprintf(tempFile, "It will be deleted when closed\n");
    
    // Rewind and read
    rewind(tempFile);
    
    char buffer[256];
    while (fgets(buffer, sizeof(buffer), tempFile) != NULL) {
        printf("%s", buffer);
    }
    
    // Temporary file is automatically deleted when closed
    fclose(tempFile);
    printf("Temporary file closed and deleted.\n\n");
}

// Example 12: File Existence Check
int fileExists(const char *filename) {
    FILE *file = fopen(filename, "r");
    if (file) {
        fclose(file);
        return 1;
    }
    return 0;
}

void fileExistenceCheck() {
    printf("=== Example 12: File Existence Check ===\n");
    
    const char *files[] = {"example.txt", "nonexistent.txt", "data.bin"};
    int numFiles = sizeof(files) / sizeof(files[0]);
    
    for (int i = 0; i < numFiles; i++) {
        printf("'%s' exists: %s\n", files[i], 
               fileExists(files[i]) ? "Yes" : "No");
    }
    
    printf("\n");
}

// Example 13: File Information
void fileInfo() {
    printf("=== Example 13: File Information ===\n");
    
    const char *filename = "example.txt";
    
    if (fileExists(filename)) {
        FILE *file = fopen(filename, "r");
        if (file) {
            // Get file size
            fseek(file, 0, SEEK_END);
            long size = ftell(file);
            
            // Count lines
            fseek(file, 0, SEEK_SET);
            int lines = 0;
            char ch;
            while ((ch = fgetc(file)) != EOF) {
                if (ch == '\n') lines++;
            }
            
            printf("File: %s\n", filename);
            printf("Size: %ld bytes\n", size);
            printf("Lines: %d\n", lines);
            
            fclose(file);
        }
    }
    
    printf("\n");
}

// Example 14: CSV File Processing
void csvFileProcessing() {
    printf("=== Example 14: CSV File Processing ===\n");
    
    // Create sample CSV file
    FILE *csvFile = fopen("data.csv", "w");
    if (csvFile) {
        fprintf(csvFile, "Name,Age,City\n");
        fprintf(csvFile, "Alice,25,New York\n");
        fprintf(csvFile, "Bob,30,Los Angeles\n");
        fprintf(csvFile, "Charlie,35,Chicago\n");
        fclose(csvFile);
    }
    
    // Read CSV file
    csvFile = fopen("data.csv", "r");
    if (csvFile == NULL) {
        printf("Error opening CSV file!\n");
        return;
    }
    
    char line[256];
    int lineNum = 0;
    
    while (fgets(line, sizeof(line), csvFile)) {
        line[strcspn(line, "\n")] = '\0'; // Remove newline
        
        if (lineNum == 0) {
            printf("Header: %s\n", line);
        } else {
            // Parse CSV line
            char *token = strtok(line, ",");
            int field = 0;
            
            printf("Record %d: ", lineNum);
            while (token != NULL) {
                printf("%s", token);
                token = strtok(NULL, ",");
                if (token) printf(", ");
            }
            printf("\n");
        }
        lineNum++;
    }
    
    fclose(csvFile);
    printf("\n");
}

// Example 15: Buffered File Operations
void bufferedFileOperations() {
    printf("=== Example 15: Buffered File Operations ===\n");
    
    // Set custom buffer size
    FILE *file = fopen("example.txt", "r");
    if (file == NULL) {
        printf("Error opening file!\n");
        return;
    }
    
    char buffer[1024];
    if (setvbuf(file, buffer, _IOFBF, sizeof(buffer)) != 0) {
        printf("Warning: Could not set buffer\n");
    }
    
    // Read with custom buffer
    char content[256];
    size_t bytesRead = fread(content, 1, sizeof(content) - 1, file);
    content[bytesRead] = '\0';
    
    printf("Read %zu bytes with custom buffer:\n", bytesRead);
    printf("%s\n", content);
    
    fclose(file);
    printf("\n");
}

// =============================================================================
// MAIN DEMONSTRATION FUNCTION
// =============================================================================

int main() {
    printf("File I/O Operations Examples\n");
    printf("============================\n\n");
    
    // Create initial file for demonstrations
    basicFileWriting();
    
    // Run all examples
    basicFileReading();
    characterByCharacterReading();
    fileAppending();
    binaryFileOperations();
    structFileOperations();
    filePositionOperations();
    errorHandlingExample();
    fileCopying();
    fileSearchAndReplace();
    temporaryFiles();
    fileExistenceCheck();
    fileInfo();
    csvFileProcessing();
    bufferedFileOperations();
    
    // Show final file content
    printf("=== Final File Content ===\n");
    FILE *file = fopen("example.txt", "r");
    if (file) {
        char buffer[256];
        while (fgets(buffer, sizeof(buffer), file) != NULL) {
            printf("%s", buffer);
        }
        fclose(file);
    }
    
    printf("\nAll file I/O examples demonstrated!\n");
    return 0;
}
