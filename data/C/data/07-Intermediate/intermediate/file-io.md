# C File I/O

## File Handling Basics

### File Pointers
```c
#include <stdio.h>

FILE *file_pointer;
```

### Opening Files
```c
FILE *fopen(const char *filename, const char *mode);

// File modes:
// "r"   - Read (file must exist)
// "w"   - Write (creates new or overwrites existing)
// "a"   - Append (creates new or appends to existing)
// "r+"  - Read and write (file must exist)
// "w+"  - Read and write (creates new or overwrites)
// "a+"  - Read and append (creates new or appends)

// Examples
FILE *fp = fopen("data.txt", "r");
if (fp == NULL) {
    printf("Error opening file!\n");
    return 1;
}
```

### Closing Files
```c
int fclose(FILE *fp);

// Always close files when done
fclose(fp);
```

## Text File Operations

### Writing to Text Files
```c
#include <stdio.h>

int main() {
    FILE *fp = fopen("output.txt", "w");
    if (fp == NULL) {
        printf("Cannot create file\n");
        return 1;
    }
    
    // Writing formatted output
    fprintf(fp, "Name: %s\n", "John Doe");
    fprintf(fp, "Age: %d\n", 30);
    fprintf(fp, "Salary: %.2f\n", 50000.50);
    
    // Writing strings
    fputs("This is a line of text\n", fp);
    
    // Writing characters
    fputc('A', fp);
    fputc('\n', fp);
    
    fclose(fp);
    return 0;
}
```

### Reading from Text Files
```c
#include <stdio.h>

int main() {
    FILE *fp = fopen("input.txt", "r");
    if (fp == NULL) {
        printf("Cannot open file\n");
        return 1;
    }
    
    // Reading formatted input
    char name[50];
    int age;
    float salary;
    
    fscanf(fp, "Name: %s\n", name);
    fscanf(fp, "Age: %d\n", &age);
    fscanf(fp, "Salary: %f\n", &salary);
    
    printf("Name: %s, Age: %d, Salary: %.2f\n", name, age, salary);
    
    // Reading line by line
    rewind(fp);  // Go back to beginning
    char line[100];
    
    while (fgets(line, sizeof(line), fp) != NULL) {
        printf("%s", line);
    }
    
    // Reading character by character
    rewind(fp);
    int ch;
    
    while ((ch = fgetc(fp)) != EOF) {
        putchar(ch);
    }
    
    fclose(fp);
    return 0;
}
```

## Binary File Operations

### Writing Binary Files
```c
#include <stdio.h>

struct Student {
    int id;
    char name[50];
    float gpa;
};

int main() {
    FILE *fp = fopen("students.dat", "wb");
    if (fp == NULL) {
        printf("Cannot create file\n");
        return 1;
    }
    
    struct Student students[] = {
        {101, "Alice", 3.8},
        {102, "Bob", 3.5},
        {103, "Charlie", 3.9}
    };
    
    // Write array to binary file
    fwrite(students, sizeof(struct Student), 3, fp);
    
    fclose(fp);
    return 0;
}
```

### Reading Binary Files
```c
#include <stdio.h>

struct Student {
    int id;
    char name[50];
    float gpa;
};

int main() {
    FILE *fp = fopen("students.dat", "rb");
    if (fp == NULL) {
        printf("Cannot open file\n");
        return 1;
    }
    
    struct Student students[3];
    
    // Read from binary file
    size_t count = fread(students, sizeof(struct Student), 3, fp);
    
    for (int i = 0; i < count; i++) {
        printf("ID: %d, Name: %s, GPA: %.2f\n", 
               students[i].id, students[i].name, students[i].gpa);
    }
    
    fclose(fp);
    return 0;
}
```

## File Positioning

### File Position Functions
```c
#include <stdio.h>

int main() {
    FILE *fp = fopen("data.txt", "r+");
    if (fp == NULL) {
        printf("Cannot open file\n");
        return 1;
    }
    
    // Get current position
    long pos = ftell(fp);
    printf("Current position: %ld\n", pos);
    
    // Move to beginning
    rewind(fp);
    
    // Move to specific position
    fseek(fp, 10, SEEK_SET);  // 10 bytes from beginning
    fseek(fp, -5, SEEK_CUR);  // 5 bytes back from current
    fseek(fp, 0, SEEK_END);   // End of file
    
    // Check if at end of file
    if (feof(fp)) {
        printf("At end of file\n");
    }
    
    fclose(fp);
    return 0;
}
```

## Error Handling

### File Operation Errors
```c
#include <stdio.h>
#include <errno.h>
#include <string.h>

int main() {
    FILE *fp = fopen("nonexistent.txt", "r");
    if (fp == NULL) {
        printf("Error opening file: %s\n", strerror(errno));
        return 1;
    }
    
    // Check for read errors
    char buffer[100];
    if (fgets(buffer, sizeof(buffer), fp) == NULL) {
        if (feof(fp)) {
            printf("Reached end of file\n");
        } else if (ferror(fp)) {
            printf("Error reading file: %s\n", strerror(errno));
        }
    }
    
    fclose(fp);
    return 0;
}
```

## Standard File Streams

### Predefined File Streams
```c
#include <stdio.h>

int main() {
    // Standard input (keyboard)
    printf("Enter your name: ");
    char name[50];
    fgets(name, sizeof(name), stdin);
    
    // Standard output (screen)
    printf("Hello, %s", name);
    
    // Standard error (error messages)
    fprintf(stderr, "This is an error message\n");
    
    return 0;
}
```

## Advanced File Operations

### File Copying
```c
#include <stdio.h>

int copy_file(const char *source, const char *destination) {
    FILE *src = fopen(source, "rb");
    if (src == NULL) {
        return 0;
    }
    
    FILE *dest = fopen(destination, "wb");
    if (dest == NULL) {
        fclose(src);
        return 0;
    }
    
    int ch;
    while ((ch = fgetc(src)) != EOF) {
        fputc(ch, dest);
    }
    
    fclose(src);
    fclose(dest);
    return 1;
}

int main() {
    if (copy_file("source.txt", "backup.txt")) {
        printf("File copied successfully\n");
    } else {
        printf("File copy failed\n");
    }
    
    return 0;
}
```

### File Statistics
```c
#include <stdio.h>
#include <sys/stat.h>

void print_file_info(const char *filename) {
    struct stat file_stat;
    
    if (stat(filename, &file_stat) == 0) {
        printf("File: %s\n", filename);
        printf("Size: %ld bytes\n", file_stat.st_size);
        printf("Last modified: %ld\n", file_stat.st_mtime);
        printf("Permissions: %o\n", file_stat.st_mode & 0777);
    } else {
        printf("Cannot get file information\n");
    }
}
```

### Temporary Files
```c
#include <stdio.h>

int main() {
    // Create temporary file
    FILE *temp = tmpfile();
    if (temp == NULL) {
        printf("Cannot create temporary file\n");
        return 1;
    }
    
    // Write to temporary file
    fprintf(temp, "This is temporary data\n");
    fprintf(temp, "It will be deleted when closed\n");
    
    // Read from temporary file
    rewind(temp);
    char line[100];
    while (fgets(line, sizeof(line), temp) != NULL) {
        printf("%s", line);
    }
    
    // File is automatically deleted when closed
    fclose(temp);
    
    return 0;
}
```

## File Buffering

### Controlling Buffering
```c
#include <stdio.h>

int main() {
    FILE *fp = fopen("buffered.txt", "w");
    if (fp == NULL) {
        return 1;
    }
    
    // Set buffering mode
    setvbuf(fp, NULL, _IONBF, 0);  // No buffering
    // setvbuf(fp, NULL, _IOLBF, 0); // Line buffering
    // setvbuf(fp, NULL, _IOFBF, 1024); // Full buffering
    
    fprintf(fp, "This will be written immediately\n");
    
    // Force write to disk
    fflush(fp);
    
    fclose(fp);
    return 0;
}
```

## Best Practices
- Always check if file operations succeed
- Close files when done to prevent resource leaks
- Use appropriate file modes
- Handle end-of-file conditions properly
- Use binary mode for binary data
- Consider file permissions and security
- Use meaningful file names and paths
- Implement proper error handling and logging
- Use buffering for performance optimization
- Be careful with file paths in different operating systems
