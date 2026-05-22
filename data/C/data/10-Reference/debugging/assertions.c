#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <string.h>

// Function to calculate factorial with assertions
int factorial(int n) {
    // Pre-condition: n should be non-negative
    assert(n >= 0 && "Factorial is not defined for negative numbers");
    
    // Base case
    if (n == 0 || n == 1) {
        return 1;
    }
    
    // Recursive case
    return n * factorial(n - 1);
}

// Function to find maximum in array with assertions
int findMaximum(int arr[], int size) {
    // Pre-conditions
    assert(arr != NULL && "Array pointer cannot be NULL");
    assert(size > 0 && "Array size must be positive");
    
    int max = arr[0];
    
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    
    // Post-condition: max should be greater than or equal to all elements
    for (int i = 0; i < size; i++) {
        assert(max >= arr[i] && "Maximum should be greater than or equal to all elements");
    }
    
    return max;
}

// Function to copy string with assertions
void safeStringCopy(char* dest, const char* src, size_t dest_size) {
    // Pre-conditions
    assert(dest != NULL && "Destination buffer cannot be NULL");
    assert(src != NULL && "Source string cannot be NULL");
    assert(dest_size > 0 && "Destination size must be positive");
    
    size_t src_len = strlen(src);
    
    // Ensure destination is large enough
    assert(src_len < dest_size && "Destination buffer too small for source string");
    
    strcpy(dest, src);
    
    // Post-condition: destination should equal source
    assert(strcmp(dest, src) == 0 && "String copy failed");
}

// Function to divide with assertions
double divide(double numerator, double denominator) {
    // Pre-condition: denominator should not be zero
    assert(denominator != 0.0 && "Division by zero is not allowed");
    
    double result = numerator / denominator;
    
    // Post-condition: result * denominator should equal numerator (within floating point precision)
    assert(abs(result * denominator - numerator) < 0.0001 && "Division result is inconsistent");
    
    return result;
}

// Function to check array is sorted with assertions
int isSorted(int arr[], int size) {
    assert(arr != NULL && "Array pointer cannot be NULL");
    assert(size >= 0 && "Array size cannot be negative");
    
    for (int i = 0; i < size - 1; i++) {
        if (arr[i] > arr[i + 1]) {
            return 0;
        }
    }
    
    return 1;
}

// Function to search in array with assertions
int linearSearch(int arr[], int size, int target) {
    assert(arr != NULL && "Array pointer cannot be NULL");
    assert(size >= 0 && "Array size cannot be negative");
    
    for (int i = 0; i < size; i++) {
        if (arr[i] == target) {
            // Post-condition: found index should be within bounds
            assert(i >= 0 && i < size && "Found index is out of bounds");
            assert(arr[i] == target && "Found element does not match target");
            return i;
        }
    }
    
    return -1; // Not found
}

// Custom assertion macro with message
#define ASSERT_WITH_MSG(condition, message) \
    do { \
        if (!(condition)) { \
            fprintf(stderr, "Assertion failed: %s\n", message); \
            fprintf(stderr, "File: %s, Line: %d\n", __FILE__, __LINE__); \
            abort(); \
        } \
    } while(0)

// Function using custom assertions
void processAge(int age) {
    ASSERT_WITH_MSG(age >= 0, "Age cannot be negative");
    ASSERT_WITH_MSG(age <= 150, "Age seems unrealistic (over 150)");
    
    if (age < 18) {
        printf("Minor\n");
    } else if (age < 65) {
        printf("Adult\n");
    } else {
        printf("Senior\n");
    }
}

int main() {
    printf("=== Assertion Examples ===\n\n");
    
    // Test factorial with valid input
    printf("Testing factorial with valid input:\n");
    int result = factorial(5);
    printf("5! = %d\n", result);
    
    // Test array maximum
    printf("\nTesting array maximum:\n");
    int arr[] = {3, 7, 2, 9, 1, 5};
    int max = findMaximum(arr, 6);
    printf("Maximum in array: %d\n", max);
    
    // Test string copy
    printf("\nTesting safe string copy:\n");
    char dest[50];
    safeStringCopy(dest, "Hello, Assertions!", sizeof(dest));
    printf("Copied string: %s\n", dest);
    
    // Test division
    printf("\nTesting division:\n");
    double div_result = divide(10.0, 2.0);
    printf("10.0 / 2.0 = %.2f\n", div_result);
    
    // Test sorted check
    printf("\nTesting sorted array check:\n");
    int sorted_arr[] = {1, 2, 3, 4, 5};
    int unsorted_arr[] = {3, 1, 4, 2, 5};
    
    printf("Array [1,2,3,4,5] is sorted: %s\n", isSorted(sorted_arr, 5) ? "Yes" : "No");
    printf("Array [3,1,4,2,5] is sorted: %s\n", isSorted(unsorted_arr, 5) ? "Yes" : "No");
    
    // Test linear search
    printf("\nTesting linear search:\n");
    int index = linearSearch(arr, 6, 7);
    printf("Found 7 at index: %d\n", index);
    
    // Test age processing
    printf("\nTesting age processing:\n");
    processAge(25);
    processAge(16);
    processAge(70);
    
    printf("\n=== All assertion tests passed ===\n");
    
    // Uncomment the following lines to see assertion failures:
    // factorial(-1);  // This will trigger an assertion
    // divide(10.0, 0.0);  // This will trigger an assertion
    
    return 0;
}
