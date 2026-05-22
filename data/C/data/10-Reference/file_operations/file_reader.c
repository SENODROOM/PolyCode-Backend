#include <stdio.h>
#include <stdlib.h>

#define MAX_LINE_LENGTH 1024

void readFile(const char* filename) {
    FILE* file = fopen(filename, "r");
    
    if (file == NULL) {
        printf("Error: Could not open file %s\n", filename);
        return;
    }
    
    char line[MAX_LINE_LENGTH];
    int lineCount = 0;
    
    printf("Contents of %s:\n", filename);
    printf("-------------------\n");
    
    while (fgets(line, sizeof(line), file) != NULL) {
        printf("%d: %s", ++lineCount, line);
    }
    
    fclose(file);
}

void writeFile(const char* filename, const char* content) {
    FILE* file = fopen(filename, "w");
    
    if (file == NULL) {
        printf("Error: Could not create file %s\n", filename);
        return;
    }
    
    fprintf(file, "%s", content);
    fclose(file);
    
    printf("Successfully wrote to %s\n", filename);
}

int main() {
    const char* filename = "sample.txt";
    
    // Write to file
    writeFile(filename, "Hello, World!\nThis is a sample file.\nCreated using C file operations.");
    
    // Read from file
    readFile(filename);
    
    return 0;
}
