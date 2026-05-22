#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// =============================================================================
// SIMPLE TEST FRAMEWORK
// =============================================================================

// Test result structure
typedef struct {
    int total_tests;
    int passed_tests;
    int failed_tests;
    int skipped_tests;
} TestResults;

// Global test results
static TestResults g_testResults = {0, 0, 0, 0};

// Test assertion macros
#define ASSERT_TRUE(condition) \
    do { \
        g_testResults.total_tests++; \
        if (condition) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %s\n", #condition); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %s (Line %d)\n", #condition, __LINE__); \
        } \
    } while(0)

#define ASSERT_FALSE(condition) \
    do { \
        g_testResults.total_tests++; \
        if (!(condition)) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: !(%s)\n", #condition); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: !(%s) (Line %d)\n", #condition, __LINE__); \
        } \
    } while(0)

#define ASSERT_EQUALS(expected, actual) \
    do { \
        g_testResults.total_tests++; \
        if ((expected) == (actual)) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %s == %s (%d)\n", #expected, #actual, (int)(expected)); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %s (%d) != %s (%d) (Line %d)\n", \
                   #expected, (int)(expected), #actual, (int)(actual), __LINE__); \
        } \
    } while(0)

#define ASSERT_NOT_EQUALS(expected, actual) \
    do { \
        g_testResults.total_tests++; \
        if ((expected) != (actual)) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %s != %s\n", #expected, #actual); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %s == %s (Line %d)\n", #expected, #actual, __LINE__); \
        } \
    } while(0)

#define ASSERT_STRING_EQUALS(expected, actual) \
    do { \
        g_testResults.total_tests++; \
        if (strcmp((expected), (actual)) == 0) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: \"%s\" == \"%s\"\n", #expected, #actual); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: \"%s\" != \"%s\" (Line %d)\n", \
                   (expected), (actual), __LINE__); \
        } \
    } while(0)

#define ASSERT_STRING_NOT_EQUALS(expected, actual) \
    do { \
        g_testResults.total_tests++; \
        if (strcmp((expected), (actual)) != 0) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: \"%s\" != \"%s\"\n", #expected, #actual); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: \"%s\" == \"%s\" (Line %d)\n", \
                   (expected), (actual), __LINE__); \
        } \
    } while(0)

#define ASSERT_NULL(ptr) \
    do { \
        g_testResults.total_tests++; \
        if ((ptr) == NULL) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %s is NULL\n", #ptr); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %s is not NULL (Line %d)\n", #ptr, __LINE__); \
        } \
    } while(0)

#define ASSERT_NOT_NULL(ptr) \
    do { \
        g_testResults.total_tests++; \
        if ((ptr) != NULL) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %s is not NULL\n", #ptr); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %s is NULL (Line %d)\n", #ptr, __LINE__); \
        } \
    } while(0)

#define SKIP_TEST(message) \
    do { \
        g_testResults.total_tests++; \
        g_testResults.skipped_tests++; \
        printf("- SKIP: %s\n", message); \
    } while(0)

// Test suite functions
void startTestSuite(const char *suiteName) {
    printf("\n=== Test Suite: %s ===\n", suiteName);
}

void endTestSuite(const char *suiteName) {
    printf("\n--- End of %s ---\n", suiteName);
}

void printTestSummary() {
    printf("\n" "=" * 20 "\n");
    printf("TEST SUMMARY\n");
    printf("=" * 20 "\n");
    printf("Total Tests: %d\n", g_testResults.total_tests);
    printf("Passed: %d\n", g_testResults.passed_tests);
    printf("Failed: %d\n", g_testResults.failed_tests);
    printf("Skipped: %d\n", g_testResults.skipped_tests);
    
    if (g_testResults.failed_tests == 0) {
        printf("\n🎉 ALL TESTS PASSED! 🎉\n");
    } else {
        printf("\n❌ %d TESTS FAILED ❌\n", g_testResults.failed_tests);
    }
    
    double successRate = (double)g_testResults.passed_tests / g_testResults.total_tests * 100.0;
    printf("Success Rate: %.1f%%\n", successRate);
}

void resetTestResults() {
    g_testResults.total_tests = 0;
    g_testResults.passed_tests = 0;
    g_testResults.failed_tests = 0;
    g_testResults.skipped_tests = 0;
}

// =============================================================================
// SAMPLE FUNCTIONS TO TEST
// =============================================================================

// Math functions
int add(int a, int b) {
    return a + b;
}

int multiply(int a, int b) {
    return a * b;
}

int factorial(int n) {
    if (n < 0) return -1;
    if (n == 0 || n == 1) return 1;
    
    int result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

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

// String functions
int stringLength(const char *str) {
    if (str == NULL) return 0;
    
    int length = 0;
    while (str[length] != '\0') {
        length++;
    }
    return length;
}

void stringCopy(char *dest, const char *src) {
    if (dest == NULL || src == NULL) return;
    
    int i = 0;
    while (src[i] != '\0') {
        dest[i] = src[i];
        i++;
    }
    dest[i] = '\0';
}

int stringCompare(const char *str1, const char *str2) {
    if (str1 == NULL || str2 == NULL) return -1;
    
    int i = 0;
    while (str1[i] != '\0' && str2[i] != '\0') {
        if (str1[i] != str2[i]) {
            return str1[i] - str2[i];
        }
        i++;
    }
    return str1[i] - str2[i];
}

// Array functions
int findMax(int arr[], int size) {
    if (arr == NULL || size <= 0) return -1;
    
    int max = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    return max;
}

int containsValue(int arr[], int size, int value) {
    if (arr == NULL || size <= 0) return 0;
    
    for (int i = 0; i < size; i++) {
        if (arr[i] == value) {
            return 1;
        }
    }
    return 0;
}

// Memory functions
int* createArray(int size, int defaultValue) {
    if (size <= 0) return NULL;
    
    int *arr = (int*)malloc(size * sizeof(int));
    if (arr == NULL) return NULL;
    
    for (int i = 0; i < size; i++) {
        arr[i] = defaultValue;
    }
    return arr;
}

void freeArray(int **arr) {
    if (arr != NULL && *arr != NULL) {
        free(*arr);
        *arr = NULL;
    }
}

// =============================================================================
// TEST SUITES
// =============================================================================

void testMathFunctions() {
    startTestSuite("Math Functions");
    
    // Test add function
    ASSERT_EQUALS(5, add(2, 3));
    ASSERT_EQUALS(0, add(-2, 2));
    ASSERT_EQUALS(-5, add(-2, -3));
    
    // Test multiply function
    ASSERT_EQUALS(6, multiply(2, 3));
    ASSERT_EQUALS(0, multiply(2, 0));
    ASSERT_EQUALS(6, multiply(-2, -3));
    ASSERT_EQUALS(-6, multiply(-2, 3));
    
    // Test factorial function
    ASSERT_EQUALS(1, factorial(0));
    ASSERT_EQUALS(1, factorial(1));
    ASSERT_EQUALS(6, factorial(3));
    ASSERT_EQUALS(120, factorial(5));
    ASSERT_EQUALS(-1, factorial(-1)); // Error case
    
    // Test isPrime function
    ASSERT_TRUE(isPrime(2));
    ASSERT_TRUE(isPrime(3));
    ASSERT_TRUE(isPrime(5));
    ASSERT_TRUE(isPrime(17));
    ASSERT_FALSE(isPrime(1));
    ASSERT_FALSE(isPrime(4));
    ASSERT_FALSE(isPrime(9));
    ASSERT_FALSE(isPrime(15));
    
    endTestSuite("Math Functions");
}

void testStringFunctions() {
    startTestSuite("String Functions");
    
    // Test stringLength
    ASSERT_EQUALS(0, stringLength(""));
    ASSERT_EQUALS(5, stringLength("Hello"));
    ASSERT_EQUALS(12, stringLength("Hello World!"));
    ASSERT_EQUALS(0, stringLength(NULL)); // NULL case
    
    // Test stringCopy
    char dest[50];
    stringCopy(dest, "Hello");
    ASSERT_STRING_EQUALS("Hello", dest);
    
    stringCopy(dest, "");
    ASSERT_STRING_EQUALS("", dest);
    
    // Test stringCompare
    ASSERT_EQUALS(0, stringCompare("Hello", "Hello"));
    ASSERT_NOT_EQUALS(0, stringCompare("Hello", "World"));
    ASSERT_EQUALS(-1, stringCompare(NULL, "Hello")); // NULL case
    
    endTestSuite("String Functions");
}

void testArrayFunctions() {
    startTestSuite("Array Functions");
    
    // Test findMax
    int arr1[] = {1, 5, 3, 9, 2};
    ASSERT_EQUALS(9, findMax(arr1, 5));
    
    int arr2[] = {-5, -2, -8, -1};
    ASSERT_EQUALS(-1, findMax(arr2, 4));
    
    ASSERT_EQUALS(-1, findMax(NULL, 5)); // NULL case
    ASSERT_EQUALS(-1, findMax(arr1, 0)); // Zero size case
    
    // Test containsValue
    ASSERT_TRUE(containsValue(arr1, 5, 9));
    ASSERT_FALSE(containsValue(arr1, 5, 7));
    ASSERT_FALSE(containsValue(NULL, 5, 1)); // NULL case
    
    endTestSuite("Array Functions");
}

void testMemoryFunctions() {
    startTestSuite("Memory Functions");
    
    // Test createArray
    int *arr = createArray(5, 42);
    ASSERT_NOT_NULL(arr);
    if (arr) {
        ASSERT_EQUALS(42, arr[0]);
        ASSERT_EQUALS(42, arr[4]);
        
        // Test freeArray
        freeArray(&arr);
        ASSERT_NULL(arr);
    }
    
    // Test edge cases
    ASSERT_NULL(createArray(0, 1)); // Zero size
    ASSERT_NULL(createArray(-1, 1)); // Negative size
    
    // Test freeArray with NULL
    int *nullArr = NULL;
    freeArray(&nullArr); // Should not crash
    ASSERT_NULL(nullArr);
    
    endTestSuite("Memory Functions");
}

void testEdgeCases() {
    startTestSuite("Edge Cases");
    
    // Test boundary conditions
    ASSERT_EQUALS(2147483647, add(2147483646, 1)); // Near INT_MAX
    ASSERT_EQUALS(-2147483648, add(-2147483647, -1)); // Near INT_MIN
    
    // Test empty strings
    ASSERT_STRING_EQUALS("", "");
    ASSERT_EQUALS(0, stringLength(""));
    
    // Test single element arrays
    int singleArr[] = {42};
    ASSERT_EQUALS(42, findMax(singleArr, 1));
    ASSERT_TRUE(containsValue(singleArr, 1, 42));
    ASSERT_FALSE(containsValue(singleArr, 1, 0));
    
    // Skip example
    SKIP_TEST("Complex performance test - requires setup");
    
    endTestSuite("Edge Cases");
}

// Performance test (simple example)
void testPerformance() {
    startTestSuite("Performance Tests");
    
    clock_t start, end;
    double cpu_time_used;
    
    // Test array search performance
    int largeArray[10000];
    for (int i = 0; i < 10000; i++) {
        largeArray[i] = i;
    }
    
    start = clock();
    int found = containsValue(largeArray, 10000, 9999);
    end = clock();
    cpu_time_used = ((double) (end - start)) / CLOCKS_PER_SEC;
    
    ASSERT_TRUE(found);
    printf("Search took %f seconds\n", cpu_time_used);
    
    // Test should complete quickly
    ASSERT_TRUE(cpu_time_used < 0.1); // Should be less than 0.1 seconds
    
    endTestSuite("Performance Tests");
}

// =============================================================================
// MAIN TEST RUNNER
// =============================================================================

int main() {
    printf("Simple Test Framework Demonstration\n");
    printf("===================================\n");
    
    // Reset test results
    resetTestResults();
    
    // Run all test suites
    testMathFunctions();
    testStringFunctions();
    testArrayFunctions();
    testMemoryFunctions();
    testEdgeCases();
    testPerformance();
    
    // Print final summary
    printTestSummary();
    
    // Return appropriate exit code
    return (g_testResults.failed_tests == 0) ? 0 : 1;
}
