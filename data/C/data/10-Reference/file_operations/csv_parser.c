#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define MAX_LINE_LENGTH 1024
#define MAX_FIELDS 50
#define MAX_FIELD_LENGTH 100

void parseCSVLine(const char* line, char fields[][MAX_FIELD_LENGTH], int* fieldCount) {
    char tempLine[MAX_LINE_LENGTH];
    strcpy(tempLine, line);
    
    char* token = strtok(tempLine, ",");
    *fieldCount = 0;
    
    while (token != NULL && *fieldCount < MAX_FIELDS) {
        // Remove trailing newline if present
        size_t len = strlen(token);
        if (len > 0 && token[len - 1] == '\n') {
            token[len - 1] = '\0';
        }
        
        strcpy(fields[*fieldCount], token);
        (*fieldCount)++;
        token = strtok(NULL, ",");
    }
}

void readCSVFile(const char* filename) {
    FILE* file = fopen(filename, "r");
    
    if (file == NULL) {
        printf("Error: Could not open file %s\n", filename);
        return;
    }
    
    char line[MAX_LINE_LENGTH];
    char fields[MAX_FIELDS][MAX_FIELD_LENGTH];
    int fieldCount;
    int lineNum = 0;
    
    printf("CSV Contents:\n");
    printf("------------\n");
    
    while (fgets(line, sizeof(line), file) != NULL) {
        parseCSVLine(line, fields, &fieldCount);
        
        printf("Line %d: ", ++lineNum);
        for (int i = 0; i < fieldCount; i++) {
            printf("[%s] ", fields[i]);
        }
        printf("\n");
    }
    
    fclose(file);
}

int main() {
    const char* filename = "data.csv";
    
    // Create a sample CSV file
    FILE* sample = fopen(filename, "w");
    fprintf(sample, "Name,Age,City\n");
    fprintf(sample, "John,25,New York\n");
    fprintf(sample, "Jane,30,London\n");
    fprintf(sample, "Bob,35,Paris\n");
    fclose(sample);
    
    // Read and parse the CSV file
    readCSVFile(filename);
    
    return 0;
}
