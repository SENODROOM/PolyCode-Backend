#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <time.h>
#include <math.h>

// =============================================================================
// STRING UTILITIES
// =============================================================================

// Safe string copy with length limit
void safeStringCopy(char *dest, const char *src, size_t destSize) {
    if (dest == NULL || src == NULL || destSize == 0) return;
    
    size_t i;
    for (i = 0; i < destSize - 1 && src[i] != '\0'; i++) {
        dest[i] = src[i];
    }
    dest[i] = '\0';
}

// Safe string concatenation with length limit
void safeStringConcat(char *dest, const char *src, size_t destSize) {
    if (dest == NULL || src == NULL || destSize == 0) return;
    
    size_t destLen = strlen(dest);
    size_t i;
    
    for (i = 0; i < destSize - destLen - 1 && src[i] != '\0'; i++) {
        dest[destLen + i] = src[i];
    }
    dest[destLen + i] = '\0';
}

// Trim whitespace from both ends
void trimString(char *str) {
    if (str == NULL) return;
    
    // Trim leading whitespace
    char *start = str;
    while (isspace((unsigned char)*start)) {
        start++;
    }
    
    // If all whitespace
    if (*start == '\0') {
        *str = '\0';
        return;
    }
    
    // Trim trailing whitespace
    char *end = str + strlen(str) - 1;
    while (end > start && isspace((unsigned char)*end)) {
        end--;
    }
    
    // Shift trimmed string to beginning
    size_t len = end - start + 1;
    memmove(str, start, len);
    str[len] = '\0';
}

// Split string by delimiter
int splitString(char *str, char delimiter, char **tokens, int maxTokens) {
    if (str == NULL || tokens == NULL || maxTokens <= 0) return 0;
    
    int count = 0;
    char *token = strtok(str, &delimiter);
    
    while (token != NULL && count < maxTokens) {
        tokens[count] = token;
        count++;
        token = strtok(NULL, &delimiter);
    }
    
    return count;
}

// Check if string contains only digits
int isNumeric(const char *str) {
    if (str == NULL || *str == '\0') return 0;
    
    while (*str) {
        if (!isdigit((unsigned char)*str)) {
            return 0;
        }
        str++;
    }
    return 1;
}

// =============================================================================
// ARRAY UTILITIES
// =============================================================================

// Find minimum in array
int findMin(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0;
    
    int min = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] < min) {
            min = arr[i];
        }
    }
    return min;
}

// Find maximum in array
int findMax(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0;
    
    int max = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    return max;
}

// Calculate sum of array
long long arraySum(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0;
    
    long long sum = 0;
    for (int i = 0; i < size; i++) {
        sum += arr[i];
    }
    return sum;
}

// Calculate average of array
double arrayAverage(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0.0;
    
    return (double)arraySum(arr, size) / size;
}

// Count occurrences of value in array
int countOccurrences(int arr[], int size, int value) {
    if (arr == NULL || size <= 0) return 0;
    
    int count = 0;
    for (int i = 0; i < size; i++) {
        if (arr[i] == value) {
            count++;
        }
    }
    return count;
}

// Check if array contains value
int containsValue(int arr[], int size, int value) {
    return countOccurrences(arr, size, value) > 0;
}

// Print array with formatting
void printArray(int arr[], int size, const char *separator) {
    if (arr == NULL || size <= 0) return;
    
    const char *sep = separator ? separator : ", ";
    
    for (int i = 0; i < size; i++) {
        printf("%d", arr[i]);
        if (i < size - 1) {
            printf("%s", sep);
        }
    }
}

// =============================================================================
// INPUT VALIDATION UTILITIES
// =============================================================================

// Get integer input with validation
int getIntegerInput(const char *prompt, int min, int max) {
    int value;
    char input[100];
    
    while (1) {
        if (prompt) printf("%s", prompt);
        
        if (fgets(input, sizeof(input), stdin) == NULL) {
            printf("Input error. Please try again.\n");
            continue;
        }
        
        // Remove newline
        input[strcspn(input, "\n")] = '\0';
        
        // Check if input is numeric
        if (!isNumeric(input)) {
            printf("Invalid input. Please enter a number.\n");
            continue;
        }
        
        value = atoi(input);
        
        // Check range
        if (value < min || value > max) {
            printf("Input must be between %d and %d. Please try again.\n", min, max);
            continue;
        }
        
        break;
    }
    
    return value;
}

// Get string input with validation
void getStringInput(const char *prompt, char *buffer, size_t bufferSize, int allowEmpty) {
    if (buffer == NULL || bufferSize == 0) return;
    
    while (1) {
        if (prompt) printf("%s", prompt);
        
        if (fgets(buffer, bufferSize, stdin) == NULL) {
            printf("Input error. Please try again.\n");
            continue;
        }
        
        // Remove newline
        buffer[strcspn(buffer, "\n")] = '\0';
        
        // Trim whitespace
        trimString(buffer);
        
        // Check if empty
        if (!allowEmpty && strlen(buffer) == 0) {
            printf("Input cannot be empty. Please try again.\n");
            continue;
        }
        
        break;
    }
}

// =============================================================================
// MATHEMATICAL UTILITIES
// =============================================================================

// Calculate factorial
long long factorial(int n) {
    if (n < 0) return -1; // Error
    if (n == 0 || n == 1) return 1;
    
    long long result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

// Check if number is prime
int isPrime(int n) {
    if (n <= 1) return 0;
    if (n <= 3) return 1;
    if (n % 2 == 0 || n % 3 == 0) return 0;
    
    for (int i = 5; i * i <= n; i += 6) {
        if (n % i == 0 || n % (i + 2) == 0) {
            return 0;
        }
    }
    return 1;
}

// Calculate GCD using Euclidean algorithm
int gcd(int a, int b) {
    while (b != 0) {
        int temp = b;
        b = a % b;
        a = temp;
    }
    return a;
}

// Calculate LCM
int lcm(int a, int b) {
    if (a == 0 || b == 0) return 0;
    return abs(a * b) / gcd(a, b);
}

// Generate random number in range
int randomInRange(int min, int max) {
    if (min > max) {
        int temp = min;
        min = max;
        max = temp;
    }
    return min + rand() % (max - min + 1);
}

// =============================================================================
// FILE UTILITIES
// =============================================================================

// Check if file exists
int fileExists(const char *filename) {
    FILE *file = fopen(filename, "r");
    if (file) {
        fclose(file);
        return 1;
    }
    return 0;
}

// Get file size
long getFileSize(const char *filename) {
    FILE *file = fopen(filename, "rb");
    if (file == NULL) return -1;
    
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    fclose(file);
    
    return size;
}

// Read file content into string
char* readFileToString(const char *filename) {
    FILE *file = fopen(filename, "r");
    if (file == NULL) return NULL;
    
    // Get file size
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    fseek(file, 0, SEEK_SET);
    
    // Allocate memory
    char *content = (char*)malloc(size + 1);
    if (content == NULL) {
        fclose(file);
        return NULL;
    }
    
    // Read file
    size_t readSize = fread(content, 1, size, file);
    content[readSize] = '\0';
    
    fclose(file);
    return content;
}

// =============================================================================
// TIME AND DATE UTILITIES
// =============================================================================

// Get current timestamp
time_t getCurrentTimestamp() {
    return time(NULL);
}

// Format timestamp to string
void formatTimestamp(time_t timestamp, char *buffer, size_t bufferSize, const char *format) {
    if (buffer == NULL || bufferSize == 0) return;
    
    struct tm *localTime = localtime(&timestamp);
    if (localTime == NULL) {
        strcpy(buffer, "Invalid time");
        return;
    }
    
    const char *fmt = format ? format : "%Y-%m-%d %H:%M:%S";
    strftime(buffer, bufferSize, fmt, localTime);
}

// Calculate difference between timestamps in seconds
long timeDifference(time_t start, time_t end) {
    return difftime(end, start);
}

// =============================================================================
// MEMORY UTILITIES
// =============================================================================

// Safe memory allocation with error checking
void* safeMalloc(size_t size) {
    void *ptr = malloc(size);
    if (ptr == NULL) {
        fprintf(stderr, "Memory allocation failed for %zu bytes\n", size);
        exit(EXIT_FAILURE);
    }
    return ptr;
}

// Safe memory reallocation
void* safeRealloc(void *ptr, size_t newSize) {
    void *newPtr = realloc(ptr, newSize);
    if (newPtr == NULL && newSize > 0) {
        fprintf(stderr, "Memory reallocation failed for %zu bytes\n", newSize);
        free(ptr);
        exit(EXIT_FAILURE);
    }
    return newPtr;
}

// Secure memory clear (for sensitive data)
void secureZeroMemory(void *ptr, size_t size) {
    if (ptr == NULL || size == 0) return;
    
    volatile char *p = (volatile char*)ptr;
    while (size--) {
        *p++ = 0;
    }
}

// =============================================================================
// DEBUGGING UTILITIES
// =============================================================================

// Print memory dump in hex
void printHexDump(const void *ptr, size_t size, const char *label) {
    if (ptr == NULL) return;
    
    if (label) printf("%s:\n", label);
    
    const unsigned char *bytes = (const unsigned char*)ptr;
    for (size_t i = 0; i < size; i++) {
        if (i % 16 == 0) {
            printf("%08zx: ", i);
        }
        printf("%02x ", bytes[i]);
        if (i % 16 == 15 || i == size - 1) {
            printf("\n");
        }
    }
}

// Simple assertion macro
#define ASSERT(condition, message) \
    do { \
        if (!(condition)) { \
            fprintf(stderr, "Assertion failed: %s\n", message); \
            exit(EXIT_FAILURE); \
        } \
    } while(0)

// =============================================================================
// DEMONSTRATION MAIN FUNCTION
// =============================================================================

int main() {
    printf("Common Utility Functions Demonstration\n");
    printf("=====================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // String utilities demo
    printf("=== STRING UTILITIES ===\n");
    char dest[50] = "Hello";
    safeStringConcat(dest, ", World!", sizeof(dest));
    printf("Concatenated: %s\n", dest);
    
    char trimStr[] = "   Hello World   ";
    trimString(trimStr);
    printf("Trimmed: '%s'\n", trimStr);
    
    printf("Is '12345' numeric: %s\n", isNumeric("12345") ? "Yes" : "No");
    printf("Is '12a45' numeric: %s\n\n", isNumeric("12a45") ? "Yes" : "No");
    
    // Array utilities demo
    printf("=== ARRAY UTILITIES ===\n");
    int arr[] = {5, 2, 8, 1, 9, 3};
    int size = sizeof(arr) / sizeof(arr[0]);
    
    printf("Array: ");
    printArray(arr, size, ", ");
    printf("\n");
    
    printf("Min: %d\n", findMin(arr, size));
    printf("Max: %d\n", findMax(arr, size));
    printf("Sum: %lld\n", arraySum(arr, size));
    printf("Average: %.2f\n", arrayAverage(arr, size));
    printf("Count of 5: %d\n\n", countOccurrences(arr, size, 5));
    
    // Mathematical utilities demo
    printf("=== MATHEMATICAL UTILITIES ===\n");
    printf("Factorial of 5: %lld\n", factorial(5));
    printf("Is 17 prime: %s\n", isPrime(17) ? "Yes" : "No");
    printf("Is 15 prime: %s\n", isPrime(15) ? "Yes" : "No");
    printf("GCD of 48 and 18: %d\n", gcd(48, 18));
    printf("LCM of 12 and 15: %d\n", lcm(12, 15));
    printf("Random number (1-100): %d\n\n", randomInRange(1, 100));
    
    // Time utilities demo
    printf("=== TIME UTILITIES ===\n");
    time_t now = getCurrentTimestamp();
    char timeBuffer[100];
    formatTimestamp(now, timeBuffer, sizeof(timeBuffer), NULL);
    printf("Current time: %s\n\n", timeBuffer);
    
    // Memory utilities demo
    printf("=== MEMORY UTILITIES ===\n");
    int *dynamicArray = (int*)safeMalloc(5 * sizeof(int));
    for (int i = 0; i < 5; i++) {
        dynamicArray[i] = i * 10;
    }
    printf("Dynamic array: ");
    printArray(dynamicArray, 5, ", ");
    printf("\n");
    secureZeroMemory(dynamicArray, 5 * sizeof(int));
    free(dynamicArray);
    
    printf("\nAll utilities demonstrated successfully!\n");
    return 0;
}
