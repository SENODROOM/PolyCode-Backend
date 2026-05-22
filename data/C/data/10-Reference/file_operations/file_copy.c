#include <stdio.h>
#include <stdlib.h>

int copyTextFile(const char* source, const char* destination) {
    FILE* src = fopen(source, "r");
    if (src == NULL) {
        printf("Error: Cannot open source file %s\n", source);
        return 0;
    }
    
    FILE* dest = fopen(destination, "w");
    if (dest == NULL) {
        printf("Error: Cannot create destination file %s\n", destination);
        fclose(src);
        return 0;
    }
    
    char buffer[1024];
    size_t bytes_read;
    
    while ((bytes_read = fread(buffer, 1, sizeof(buffer), src)) > 0) {
        size_t bytes_written = fwrite(buffer, 1, bytes_read, dest);
        if (bytes_written != bytes_read) {
            printf("Error: Write operation failed\n");
            fclose(src);
            fclose(dest);
            return 0;
        }
    }
    
    fclose(src);
    fclose(dest);
    return 1;
}

int copyBinaryFile(const char* source, const char* destination) {
    FILE* src = fopen(source, "rb");
    if (src == NULL) {
        printf("Error: Cannot open source file %s\n", source);
        return 0;
    }
    
    FILE* dest = fopen(destination, "wb");
    if (dest == NULL) {
        printf("Error: Cannot create destination file %s\n", destination);
        fclose(src);
        return 0;
    }
    
    char buffer[1024];
    size_t bytes_read;
    
    while ((bytes_read = fread(buffer, 1, sizeof(buffer), src)) > 0) {
        size_t bytes_written = fwrite(buffer, 1, bytes_read, dest);
        if (bytes_written != bytes_read) {
            printf("Error: Write operation failed\n");
            fclose(src);
            fclose(dest);
            return 0;
        }
    }
    
    fclose(src);
    fclose(dest);
    return 1;
}

long getFileSize(const char* filename) {
    FILE* file = fopen(filename, "rb");
    if (file == NULL) return -1;
    
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    fclose(file);
    
    return size;
}

int compareFiles(const char* file1, const char* file2) {
    FILE* f1 = fopen(file1, "rb");
    FILE* f2 = fopen(file2, "rb");
    
    if (f1 == NULL || f2 == NULL) {
        if (f1) fclose(f1);
        if (f2) fclose(f2);
        return -1;
    }
    
    char buffer1[1024], buffer2[1024];
    size_t bytes1, bytes2;
    
    do {
        bytes1 = fread(buffer1, 1, sizeof(buffer1), f1);
        bytes2 = fread(buffer2, 1, sizeof(buffer2), f2);
        
        if (bytes1 != bytes2) {
            fclose(f1);
            fclose(f2);
            return 0;
        }
        
        if (memcmp(buffer1, buffer2, bytes1) != 0) {
            fclose(f1);
            fclose(f2);
            return 0;
        }
        
    } while (bytes1 > 0);
    
    fclose(f1);
    fclose(f2);
    return 1;
}

void createSampleFile(const char* filename) {
    FILE* file = fopen(filename, "w");
    if (file == NULL) {
        printf("Error: Cannot create file %s\n", filename);
        return;
    }
    
    fprintf(file, "This is a sample text file.\n");
    fprintf(file, "It contains multiple lines of text.\n");
    fprintf(file, "This file will be used for testing file copy operations.\n");
    fprintf(file, "Line 4: Some more content here.\n");
    fprintf(file, "Line 5: Final line of the sample file.\n");
    
    fclose(file);
    printf("Created sample file: %s\n", filename);
}

int main() {
    const char* source_file = "source.txt";
    const char* text_copy = "text_copy.txt";
    const char* binary_copy = "binary_copy.txt";
    
    // Create a sample file
    createSampleFile(source_file);
    
    // Get original file size
    long original_size = getFileSize(source_file);
    printf("Original file size: %ld bytes\n", original_size);
    
    // Copy as text file
    printf("\nCopying as text file...\n");
    if (copyTextFile(source_file, text_copy)) {
        printf("Text copy successful!\n");
        long text_copy_size = getFileSize(text_copy);
        printf("Text copy size: %ld bytes\n", text_copy_size);
        
        // Compare files
        int comparison = compareFiles(source_file, text_copy);
        if (comparison == 1) {
            printf("Files are identical!\n");
        } else if (comparison == 0) {
            printf("Files are different!\n");
        } else {
            printf("Error comparing files!\n");
        }
    }
    
    // Copy as binary file
    printf("\nCopying as binary file...\n");
    if (copyBinaryFile(source_file, binary_copy)) {
        printf("Binary copy successful!\n");
        long binary_copy_size = getFileSize(binary_copy);
        printf("Binary copy size: %ld bytes\n", binary_copy_size);
        
        // Compare files
        int comparison = compareFiles(source_file, binary_copy);
        if (comparison == 1) {
            printf("Files are identical!\n");
        } else if (comparison == 0) {
            printf("Files are different!\n");
        } else {
            printf("Error comparing files!\n");
        }
    }
    
    // Display content of copied files
    printf("\nContent of %s:\n", text_copy);
    FILE* file = fopen(text_copy, "r");
    if (file) {
        char line[256];
        while (fgets(line, sizeof(line), file)) {
            printf("%s", line);
        }
        fclose(file);
    }
    
    return 0;
}
