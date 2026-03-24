# Common Utility Functions

This file contains a comprehensive collection of reusable utility functions for C programming. These functions cover string manipulation, array operations, input validation, mathematical calculations, file handling, time operations, memory management, and debugging utilities.

## 📚 Utility Categories

### 🔤 String Utilities
Safe and efficient string manipulation functions with proper bounds checking.

### 📊 Array Utilities
Common array operations including statistics, searching, and formatting.

### ✅ Input Validation Utilities
Robust input handling with validation and error checking.

### 🔢 Mathematical Utilities
Mathematical functions for common calculations and number theory.

### 📁 File Utilities
File operations with error handling and memory management.

### ⏰ Time and Date Utilities
Time manipulation, formatting, and calculation functions.

### 💾 Memory Utilities
Safe memory allocation, reallocation, and secure data handling.

### 🐛 Debugging Utilities
Tools for debugging, memory inspection, and assertion checking.

## 🔤 String Utilities

### `safeStringCopy(char *dest, const char *src, size_t destSize)`
**Purpose**: Copy string with buffer overflow protection
**Parameters**: Destination buffer, source string, destination size
**Returns**: void
**Safety**: Prevents buffer overflow by respecting destination size

### `safeStringConcat(char *dest, const char *src, size_t destSize)`
**Purpose**: Concatenate strings with buffer overflow protection
**Parameters**: Destination buffer, source string, destination size
**Returns**: void
**Safety**: Ensures null termination and prevents overflow

### `trimString(char *str)`
**Purpose**: Remove leading and trailing whitespace
**Parameters**: String to trim (modified in-place)
**Returns**: void
**Features**: Handles all whitespace characters

### `splitString(char *str, char delimiter, char **tokens, int maxTokens)`
**Purpose**: Split string by delimiter into tokens
**Parameters**: String to split, delimiter, token array, max tokens
**Returns**: Number of tokens found
**Note**: Modifies original string (uses strtok)

### `isNumeric(const char *str)`
**Purpose**: Check if string contains only digits
**Parameters**: String to check
**Returns**: 1 if numeric, 0 otherwise
**Use Cases**: Input validation for numbers

## 📊 Array Utilities

### `findMin(int arr[], int size)`
**Purpose**: Find minimum value in integer array
**Complexity**: O(n) time, O(1) space
**Safety**: Checks for NULL pointer and invalid size

### `findMax(int arr[], int size)`
**Purpose**: Find maximum value in integer array
**Complexity**: O(n) time, O(1) space
**Safety**: Checks for NULL pointer and invalid size

### `arraySum(int arr[], int size)`
**Purpose**: Calculate sum of array elements
**Returns**: 64-bit sum to prevent overflow
**Complexity**: O(n) time, O(1) space

### `arrayAverage(int arr[], int size)`
**Purpose**: Calculate average of array elements
**Returns**: Double precision average
**Complexity**: O(n) time, O(1) space

### `countOccurrences(int arr[], int size, int value)`
**Purpose**: Count occurrences of specific value
**Complexity**: O(n) time, O(1) space
**Use Cases**: Frequency analysis, validation

### `containsValue(int arr[], int size, int value)`
**Purpose**: Quick check if value exists in array
**Returns**: 1 if found, 0 otherwise
**Optimization**: Stops at first occurrence

### `printArray(int arr[], int size, const char *separator)`
**Purpose**: Print array with custom separator
**Features**: Flexible formatting, NULL separator defaults to comma
**Use Cases**: Debugging, output formatting

## ✅ Input Validation Utilities

### `getIntegerInput(const char *prompt, int min, int max)`
**Purpose**: Get validated integer input from user
**Features**: Range checking, type validation, error handling
**Returns**: Validated integer within specified range
**Use Cases**: Menu systems, parameter input

### `getStringInput(const char *prompt, char *buffer, size_t bufferSize, int allowEmpty)`
**Purpose**: Get validated string input from user
**Features**: Buffer overflow protection, empty input control
**Parameters**: Prompt, buffer, buffer size, allow empty flag
**Safety**: Prevents buffer overflow, handles edge cases

## 🔢 Mathematical Utilities

### `factorial(int n)`
**Purpose**: Calculate factorial of non-negative integer
**Returns**: 64-bit factorial, -1 for negative input
**Complexity**: O(n) time, O(1) space
**Limitations**: Limited by 64-bit integer range

### `isPrime(int n)`
**Purpose**: Check if number is prime
**Algorithm**: Optimized trial division up to √n
**Complexity**: O(√n) time, O(1) space
**Optimization**: Skips even numbers after 2

### `gcd(int a, int b)`
**Purpose**: Calculate greatest common divisor
**Algorithm**: Euclidean algorithm
**Complexity**: O(log min(a,b)) time, O(1) space
**Use Cases**: Fraction simplification, number theory

### `lcm(int a, int b)`
**Purpose**: Calculate least common multiple
**Formula**: |a × b| / gcd(a,b)
**Complexity**: O(log min(a,b)) time, O(1) space

### `randomInRange(int min, int max)`
**Purpose**: Generate random integer in specified range
**Features**: Handles min > max case automatically
**Note**: Requires srand() initialization
**Use Cases**: Game development, simulations

## 📁 File Utilities

### `fileExists(const char *filename)`
**Purpose**: Check if file exists and is readable
**Returns**: 1 if exists, 0 otherwise
**Safety**: Properly closes file handle

### `getFileSize(const char *filename)`
**Purpose**: Get file size in bytes
**Returns**: File size or -1 on error
**Use Cases**: Memory allocation, validation

### `readFileToString(const char *filename)`
**Purpose**: Read entire file into dynamically allocated string
**Returns**: Allocated string or NULL on error
**Memory**: Caller must free returned string
**Safety**: Proper error handling and null termination

## ⏰ Time and Date Utilities

### `getCurrentTimestamp()`
**Purpose**: Get current Unix timestamp
**Returns**: time_t value
**Use Cases**: Logging, timing operations

### `formatTimestamp(time_t timestamp, char *buffer, size_t bufferSize, const char *format)`
**Purpose**: Format timestamp as readable string
**Features**: Custom format strings, default format available
**Format**: Uses strftime() format codes
**Example**: "%Y-%m-%d %H:%M:%S"

### `timeDifference(time_t start, time_t end)`
**Purpose**: Calculate difference between timestamps
**Returns**: Difference in seconds
**Use Cases**: Performance measurement, duration calculation

## 💾 Memory Utilities

### `safeMalloc(size_t size)`
**Purpose**: Safe memory allocation with error checking
**Features**: Exits on failure with error message
**Returns**: Valid pointer or terminates program
**Use Cases**: Critical memory allocation

### `safeRealloc(void *ptr, size_t newSize)`
**Purpose**: Safe memory reallocation
**Features**: Proper error handling, memory cleanup on failure
**Safety**: Frees original memory if reallocation fails

### `secureZeroMemory(void *ptr, size_t size)`
**Purpose**: Securely zero memory (for sensitive data)
**Features**: Prevents compiler optimization
**Use Cases**: Password clearing, cryptographic data
**Security**: Uses volatile pointer to prevent optimization

## 🐛 Debugging Utilities

### `printHexDump(const void *ptr, size_t size, const char *label)`
**Purpose**: Print memory contents in hexadecimal format
**Features**: Address labels, 16-byte rows formatting
**Use Cases**: Memory inspection, debugging data structures

### `ASSERT(condition, message)`
**Purpose**: Assertion macro for debugging
**Features**: Error message, program termination on failure
**Use Cases**: Development-time validation, debugging

## 💡 Usage Guidelines

### 1. Include Headers
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <time.h>
#include <math.h>
```

### 2. Error Handling
- Always check return values where applicable
- Use safe functions instead of standard ones when possible
- Initialize pointers to NULL

### 3. Memory Management
- Free dynamically allocated memory
- Use safe allocation functions for critical code
- Clear sensitive data when done

### 4. Input Validation
- Always validate user input
- Use range checking for numeric input
- Prevent buffer overflow in string operations

## 🚀 Performance Considerations

### Time Complexity Analysis
| Category | Function | Time Complexity | Space Complexity |
|----------|----------|-----------------|------------------|
| String | safeStringCopy | O(n) | O(1) |
| String | trimString | O(n) | O(1) |
| Array | findMin/findMax | O(n) | O(1) |
| Array | arraySum | O(n) | O(1) |
| Math | isPrime | O(√n) | O(1) |
| Math | gcd | O(log n) | O(1) |
| File | readFileToString | O(n) | O(n) |

### Optimization Tips
1. **Batch Operations**: Process multiple items together
2. **Early Exit**: Stop loops when conditions are met
3. **Memory Locality**: Access memory sequentially
4. **Avoid Reallocations**: Pre-allocate when possible

## ⚠️ Safety Considerations

### Buffer Overflow Prevention
- Always specify buffer sizes
- Use safe string functions
- Validate array bounds

### Memory Safety
- Initialize pointers to NULL
- Check allocation success
- Free allocated memory
- Avoid dangling pointers

### Input Validation
- Validate all user input
- Check string lengths
- Verify numeric ranges

## 🔧 Integration Examples

### Example 1: Reading and Processing Data
```c
// Read file
char *data = readFileToString("input.txt");
if (data) {
    // Process lines
    char *lines[100];
    int lineCount = splitString(data, '\n', lines, 100);
    
    for (int i = 0; i < lineCount; i++) {
        trimString(lines[i]);
        if (strlen(lines[i]) > 0) {
            // Process non-empty line
        }
    }
    
    free(data);
}
```

### Example 2: User Input Processing
```c
// Get validated input
int choice = getIntegerInput("Enter choice (1-5): ", 1, 5);
char filename[256];
getStringInput("Enter filename: ", filename, sizeof(filename), 0);

if (fileExists(filename)) {
    long size = getFileSize(filename);
    printf("File size: %ld bytes\n", size);
}
```

### Example 3: Array Processing
```c
// Generate random array
int size = 10;
int *arr = safeMalloc(size * sizeof(int));

for (int i = 0; i < size; i++) {
    arr[i] = randomInRange(1, 100);
}

// Calculate statistics
printf("Array: ");
printArray(arr, size, ", ");
printf("\n");
printf("Min: %d, Max: %d, Average: %.2f\n",
       findMin(arr, size), findMax(arr, size), arrayAverage(arr, size));

free(arr);
```

## 🎓 Best Practices

1. **Use Safe Functions**: Prefer safe variants over standard ones
2. **Validate Inputs**: Always check user input and parameters
3. **Handle Errors**: Check return values and handle failures
4. **Memory Management**: Proper allocation and deallocation
5. **Documentation**: Comment complex functions and algorithms
6. **Testing**: Test edge cases and error conditions

These utility functions provide a solid foundation for robust C programming applications while emphasizing safety, efficiency, and maintainability.
