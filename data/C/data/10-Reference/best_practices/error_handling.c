/*
 * File: error_handling.c
 * Description: Comprehensive error handling examples in C
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <errno.h>
#include <limits.h>

// Error codes
typedef enum {
    SUCCESS = 0,
    ERROR_NULL_POINTER,
    ERROR_INVALID_PARAMETER,
    ERROR_MEMORY_ALLOCATION,
    ERROR_FILE_OPERATION,
    ERROR_OVERFLOW,
    ERROR_DIVISION_BY_ZERO,
    ERROR_BUFFER_OVERFLOW,
    ERROR_INVALID_STATE
} ErrorCode;

// Error messages
const char* error_messages[] = {
    "Success",
    "Null pointer encountered",
    "Invalid parameter provided",
    "Memory allocation failed",
    "File operation failed",
    "Arithmetic overflow detected",
    "Division by zero attempted",
    "Buffer overflow prevented",
    "Invalid operation state"
};

// Result structure for functions that can fail
typedef struct {
    ErrorCode error_code;
    int success;
    char error_message[256];
} Result;

// Initialize result structure
Result initResult() {
    Result result = {SUCCESS, 1, ""};
    return result;
}

// Set error in result
void setError(Result* result, ErrorCode code, const char* additional_info) {
    result->error_code = code;
    result->success = 0;
    
    if (additional_info != NULL) {
        snprintf(result->error_message, sizeof(result->error_message), 
                "%s: %s", error_messages[code], additional_info);
    } else {
        strncpy(result->error_message, error_messages[code], 
                sizeof(result->error_message) - 1);
        result->error_message[sizeof(result->error_message) - 1] = '\0';
    }
}

// Safe memory allocation with error handling
void* safeMalloc(size_t size, Result* result) {
    if (size == 0) {
        setError(result, ERROR_INVALID_PARAMETER, "Size cannot be zero");
        return NULL;
    }
    
    if (size > SIZE_MAX / 2) { // Prevent extremely large allocations
        setError(result, ERROR_INVALID_PARAMETER, "Size too large");
        return NULL;
    }
    
    void* ptr = malloc(size);
    if (ptr == NULL) {
        setError(result, ERROR_MEMORY_ALLOCATION, strerror(errno));
        return NULL;
    }
    
    return ptr;
}

// Safe string copy with overflow protection
Result safeStringCopy(char* dest, const char* src, size_t dest_size) {
    Result result = initResult();
    
    if (dest == NULL || src == NULL) {
        setError(&result, ERROR_NULL_POINTER, "Destination or source is NULL");
        return result;
    }
    
    if (dest_size == 0) {
        setError(&result, ERROR_INVALID_PARAMETER, "Destination size is zero");
        return result;
    }
    
    size_t src_len = strlen(src);
    
    if (src_len >= dest_size) {
        setError(&result, ERROR_BUFFER_OVERFLOW, "Source string too long for destination");
        return result;
    }
    
    strcpy(dest, src);
    return result;
}

// Safe integer addition with overflow detection
Result safeAddInt(int a, int b, int* result) {
    Result res = initResult();
    
    if (result == NULL) {
        setError(&res, ERROR_NULL_POINTER, "Result pointer is NULL");
        return res;
    }
    
    // Check for overflow
    if ((b > 0 && a > INT_MAX - b) || (b < 0 && a < INT_MIN - b)) {
        setError(&res, ERROR_OVERFLOW, "Integer addition would overflow");
        return res;
    }
    
    *result = a + b;
    return res;
}

// Safe division with zero check
Result safeDivide(int numerator, int denominator, double* result) {
    Result res = initResult();
    
    if (result == NULL) {
        setError(&res, ERROR_NULL_POINTER, "Result pointer is NULL");
        return res;
    }
    
    if (denominator == 0) {
        setError(&res, ERROR_DIVISION_BY_ZERO, "Cannot divide by zero");
        return res;
    }
    
    *result = (double)numerator / denominator;
    return res;
}

// Safe file opening with error handling
FILE* safeFileOpen(const char* filename, const char* mode, Result* result) {
    Result res = initResult();
    
    if (filename == NULL || mode == NULL) {
        setError(&res, ERROR_NULL_POINTER, "Filename or mode is NULL");
        if (result) *result = res;
        return NULL;
    }
    
    FILE* file = fopen(filename, mode);
    if (file == NULL) {
        setError(&res, ERROR_FILE_OPERATION, strerror(errno));
        if (result) *result = res;
        return NULL;
    }
    
    if (result) *result = res;
    return file;
}

// Function to validate array parameters
Result validateArrayParams(const void* array, size_t size, size_t element_size) {
    Result result = initResult();
    
    if (array == NULL) {
        setError(&result, ERROR_NULL_POINTER, "Array pointer is NULL");
        return result;
    }
    
    if (size == 0) {
        setError(&result, ERROR_INVALID_PARAMETER, "Array size cannot be zero");
        return result;
    }
    
    if (element_size == 0) {
        setError(&result, ERROR_INVALID_PARAMETER, "Element size cannot be zero");
        return result;
    }
    
    return result;
}

// Safe array access with bounds checking
Result safeArrayAccess(const int* array, size_t size, size_t index, int* value) {
    Result result = validateArrayParams(array, size, sizeof(int));
    if (!result.success) return result;
    
    if (value == NULL) {
        setError(&result, ERROR_NULL_POINTER, "Value pointer is NULL");
        return result;
    }
    
    if (index >= size) {
        setError(&result, ERROR_INVALID_PARAMETER, "Array index out of bounds");
        return result;
    }
    
    *value = array[index];
    return result;
}

// Function to demonstrate error handling patterns
void demonstrateErrorHandling() {
    printf("=== Error Handling Demonstration ===\n\n");
    
    // Test 1: Safe memory allocation
    printf("1. Testing safe memory allocation:\n");
    Result result = initResult();
    int* numbers = (int*)safeMalloc(10 * sizeof(int), &result);
    
    if (result.success) {
        printf("   Memory allocation successful\n");
        free(numbers);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test 2: Safe string copy
    printf("\n2. Testing safe string copy:\n");
    char buffer[20];
    result = safeStringCopy(buffer, "Hello, World!", sizeof(buffer));
    
    if (result.success) {
        printf("   String copy successful: '%s'\n", buffer);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test buffer overflow
    result = safeStringCopy(buffer, "This is a very long string that will cause buffer overflow", sizeof(buffer));
    if (!result.success) {
        printf("   Buffer overflow correctly detected: %s\n", result.error_message);
    }
    
    // Test 3: Safe integer addition
    printf("\n3. Testing safe integer addition:\n");
    int sum_result;
    result = safeAddInt(1000000, 2000000, &sum_result);
    
    if (result.success) {
        printf("   Addition successful: %d\n", sum_result);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test overflow
    result = safeAddInt(INT_MAX, 1, &sum_result);
    if (!result.success) {
        printf("   Overflow correctly detected: %s\n", result.error_message);
    }
    
    // Test 4: Safe division
    printf("\n4. Testing safe division:\n");
    double div_result;
    result = safeDivide(10, 2, &div_result);
    
    if (result.success) {
        printf("   Division successful: %.2f\n", div_result);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test division by zero
    result = safeDivide(10, 0, &div_result);
    if (!result.success) {
        printf("   Division by zero correctly detected: %s\n", result.error_message);
    }
    
    // Test 5: Safe file operations
    printf("\n5. Testing safe file operations:\n");
    FILE* file = safeFileOpen("test.txt", "w", &result);
    
    if (result.success) {
        printf("   File opened successfully\n");
        fprintf(file, "Test content\n");
        fclose(file);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test 6: Safe array access
    printf("\n6. Testing safe array access:\n");
    int test_array[] = {10, 20, 30, 40, 50};
    int value;
    
    result = safeArrayAccess(test_array, 5, 2, &value);
    if (result.success) {
        printf("   Array access successful: value = %d\n", value);
    } else {
        printf("   Error: %s\n", result.error_message);
    }
    
    // Test bounds checking
    result = safeArrayAccess(test_array, 5, 10, &value);
    if (!result.success) {
        printf("   Bounds violation correctly detected: %s\n", result.error_message);
    }
    
    printf("\n=== Error handling demonstration completed ===\n");
}

// Function that uses error handling for a real-world scenario
Result processUserData(const char* name, int age, double salary) {
    Result result = initResult();
    
    // Validate input parameters
    if (name == NULL) {
        setError(&result, ERROR_NULL_POINTER, "Name cannot be NULL");
        return result;
    }
    
    if (strlen(name) == 0) {
        setError(&result, ERROR_INVALID_PARAMETER, "Name cannot be empty");
        return result;
    }
    
    if (age < 0 || age > 150) {
        setError(&result, ERROR_INVALID_PARAMETER, "Age must be between 0 and 150");
        return result;
    }
    
    if (salary < 0) {
        setError(&result, ERROR_INVALID_PARAMETER, "Salary cannot be negative");
        return result;
    }
    
    // Simulate processing
    printf("Processing user: %s, Age: %d, Salary: %.2f\n", name, age, salary);
    
    return result;
}

int main() {
    demonstrateErrorHandling();
    
    printf("\n=== Real-world Scenario ===\n");
    
    // Test user data processing
    Result result = processUserData("John Doe", 30, 50000.0);
    if (result.success) {
        printf("User data processed successfully\n");
    } else {
        printf("Error processing user data: %s\n", result.error_message);
    }
    
    // Test with invalid data
    result = processUserData(NULL, 30, 50000.0);
    if (!result.success) {
        printf("Error correctly detected: %s\n", result.error_message);
    }
    
    result = processUserData("Jane", -5, 40000.0);
    if (!result.success) {
        printf("Error correctly detected: %s\n", result.error_message);
    }
    
    return 0;
}
