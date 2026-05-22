/*
 * File: helper_functions.c
 * Description: Collection of useful utility functions for C programming
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <time.h>

// String utilities
int isEmpty(const char* str) {
    return str == NULL || strlen(str) == 0;
}

int startsWith(const char* str, const char* prefix) {
    if (str == NULL || prefix == NULL) return 0;
    return strncmp(str, prefix, strlen(prefix)) == 0;
}

int endsWith(const char* str, const char* suffix) {
    if (str == NULL || suffix == NULL) return 0;
    
    size_t str_len = strlen(str);
    size_t suffix_len = strlen(suffix);
    
    if (suffix_len > str_len) return 0;
    
    return strcmp(str + str_len - suffix_len, suffix) == 0;
}

char* trim(char* str) {
    if (str == NULL) return NULL;
    
    // Trim leading whitespace
    char* start = str;
    while (isspace((unsigned char)*start)) {
        start++;
    }
    
    // Trim trailing whitespace
    char* end = str + strlen(str) - 1;
    while (end > start && isspace((unsigned char)*end)) {
        end--;
    }
    
    // Write new null terminator
    *(end + 1) = '\0';
    
    // Move trimmed string to beginning if needed
    if (start != str) {
        memmove(str, start, strlen(start) + 1);
    }
    
    return str;
}

// Array utilities
void printIntArray(int arr[], int size) {
    printf("[");
    for (int i = 0; i < size; i++) {
        printf("%d", arr[i]);
        if (i < size - 1) printf(", ");
    }
    printf("]\n");
}

int arrayContains(int arr[], int size, int value) {
    for (int i = 0; i < size; i++) {
        if (arr[i] == value) return 1;
    }
    return 0;
}

void swapInt(int* a, int* b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

// Math utilities
int getRandomInt(int min, int max) {
    static int seeded = 0;
    if (!seeded) {
        srand(time(NULL));
        seeded = 1;
    }
    return min + rand() % (max - min + 1);
}

double getRandomDouble(double min, double max) {
    static int seeded = 0;
    if (!seeded) {
        srand(time(NULL));
        seeded = 1;
    }
    double random = ((double)rand() / RAND_MAX);
    return min + random * (max - min);
}

int isEven(int num) {
    return num % 2 == 0;
}

int isOdd(int num) {
    return num % 2 != 0;
}

// File utilities
int fileExists(const char* filename) {
    FILE* file = fopen(filename, "r");
    if (file) {
        fclose(file);
        return 1;
    }
    return 0;
}

long getFileSize(const char* filename) {
    FILE* file = fopen(filename, "rb");
    if (file == NULL) return -1;
    
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    fclose(file);
    
    return size;
}

// Input utilities
void clearInputBuffer() {
    int c;
    while ((c = getchar()) != '\n' && c != EOF);
}

int getIntInput(const char* prompt, int min, int max) {
    int value;
    char buffer[100];
    
    while (1) {
        printf("%s", prompt);
        if (fgets(buffer, sizeof(buffer), stdin) == NULL) {
            printf("Input error. Please try again.\n");
            continue;
        }
        
        if (sscanf(buffer, "%d", &value) == 1) {
            if (value >= min && value <= max) {
                return value;
            } else {
                printf("Please enter a value between %d and %d.\n", min, max);
            }
        } else {
            printf("Invalid input. Please enter a number.\n");
        }
    }
}

// Time utilities
char* getCurrentTime() {
    static char buffer[80];
    time_t rawtime;
    struct tm* timeinfo;
    
    time(&rawtime);
    timeinfo = localtime(&rawtime);
    
    strftime(buffer, sizeof(buffer), "%Y-%m-%d %H:%M:%S", timeinfo);
    return buffer;
}

void delay(int milliseconds) {
    clock_t start_time = clock();
    while (clock() < start_time + milliseconds);
}

// Validation utilities
int isValidEmail(const char* email) {
    if (isEmpty(email)) return 0;
    
    int at_count = 0;
    int dot_count = 0;
    
    // Count @ and . characters
    for (int i = 0; email[i]; i++) {
        if (email[i] == '@') at_count++;
        if (email[i] == '.') dot_count++;
    }
    
    // Basic email validation
    return (at_count == 1 && dot_count >= 1 && 
            !startsWith(email, "@") && !endsWith(email, "@") &&
            !startsWith(email, ".") && !endsWith(email, "."));
}

int isValidPhoneNumber(const char* phone) {
    if (isEmpty(phone)) return 0;
    
    // Check if all characters are digits or common phone symbols
    for (int i = 0; phone[i]; i++) {
        if (!isdigit(phone[i]) && phone[i] != '-' && phone[i] != '(' && phone[i] != ')' && phone[i] != '+') {
            return 0;
        }
    }
    
    return 1;
}

// Test function
void testUtilities() {
    printf("=== Testing Utility Functions ===\n\n");
    
    // String tests
    printf("String Tests:\n");
    char test_str[] = "  Hello World  ";
    printf("Original: '%s'\n", test_str);
    printf("Trimmed: '%s'\n", trim(test_str));
    printf("Starts with 'Hello': %s\n", startsWith(test_str, "Hello") ? "Yes" : "No");
    printf("Ends with 'World': %s\n", endsWith(test_str, "World") ? "Yes" : "No");
    
    // Array tests
    printf("\nArray Tests:\n");
    int arr[] = {1, 2, 3, 4, 5};
    printf("Array: ");
    printIntArray(arr, 5);
    printf("Contains 3: %s\n", arrayContains(arr, 5, 3) ? "Yes" : "No");
    printf("Contains 6: %s\n", arrayContains(arr, 5, 6) ? "Yes" : "No");
    
    // Math tests
    printf("\nMath Tests:\n");
    printf("Random number (1-100): %d\n", getRandomInt(1, 100));
    printf("Random double (0.0-1.0): %.3f\n", getRandomDouble(0.0, 1.0));
    printf("Is 4 even: %s\n", isEven(4) ? "Yes" : "No");
    printf("Is 7 odd: %s\n", isOdd(7) ? "Yes" : "No");
    
    // Time tests
    printf("\nTime Tests:\n");
    printf("Current time: %s\n", getCurrentTime());
    
    // Validation tests
    printf("\nValidation Tests:\n");
    printf("Email 'user@example.com' valid: %s\n", isValidEmail("user@example.com") ? "Yes" : "No");
    printf("Email 'invalid@' valid: %s\n", isValidEmail("invalid@") ? "Yes" : "No");
    printf("Phone '123-456-7890' valid: %s\n", isValidPhoneNumber("123-456-7890") ? "Yes" : "No");
    
    printf("\n=== Utility tests completed ===\n");
}

int main() {
    testUtilities();
    return 0;
}
