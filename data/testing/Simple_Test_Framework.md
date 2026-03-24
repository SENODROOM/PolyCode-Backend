# Simple Test Framework

This file provides a lightweight, easy-to-use testing framework for C programs. It includes assertion macros, test result tracking, and examples of how to write effective unit tests.

## 🎯 Framework Overview

The framework provides:
- **Assertion Macros**: For testing conditions and values
- **Test Suite Management**: Organize tests into logical groups
- **Result Tracking**: Automatic counting of passed/failed/skipped tests
- **Performance Testing**: Basic timing capabilities
- **Memory Testing**: Memory allocation and deallocation validation

## 📚 Core Components

### Assertion Macros
- `ASSERT_TRUE(condition)` - Test if condition is true
- `ASSERT_FALSE(condition)` - Test if condition is false
- `ASSERT_EQUALS(expected, actual)` - Test equality
- `ASSERT_NOT_EQUALS(expected, actual)` - Test inequality
- `ASSERT_STRING_EQUALS(expected, actual)` - Test string equality
- `ASSERT_STRING_NOT_EQUALS(expected, actual)` - Test string inequality
- `ASSERT_NULL(ptr)` - Test if pointer is NULL
- `ASSERT_NOT_NULL(ptr)` - Test if pointer is not NULL
- `SKIP_TEST(message)` - Skip a test with reason

### Test Suite Functions
- `startTestSuite(suiteName)` - Begin a test suite
- `endTestSuite(suiteName)` - End a test suite
- `printTestSummary()` - Display comprehensive test results
- `resetTestResults()` - Clear all test counters

## 🔧 Usage Guide

### 1. Include the Framework
The framework is self-contained in a single file. Simply include it in your test program.

### 2. Write Test Functions
```c
void testMyFunction() {
    startTestSuite("My Function Tests");
    
    // Test normal cases
    ASSERT_EQUALS(5, add(2, 3));
    ASSERT_EQUALS(0, add(-2, 2));
    
    // Test edge cases
    ASSERT_EQUALS(0, add(0, 0));
    
    // Test error cases
    ASSERT_EQUALS(-1, add(INT_MAX, 1)); // Overflow case
    
    endTestSuite("My Function Tests");
}
```

### 3. Create Test Functions to Test
```c
int add(int a, int b) {
    return a + b;
}

int divide(int a, int b) {
    if (b == 0) return 0; // Error case
    return a / b;
}
```

### 4. Run Tests
```c
int main() {
    resetTestResults();
    
    testMyFunction();
    testOtherFunction();
    
    printTestSummary();
    return (g_testResults.failed_tests == 0) ? 0 : 1;
}
```

## 📊 Assertion Examples

### Basic Assertions
```c
// Boolean tests
ASSERT_TRUE(isPrime(17));
ASSERT_FALSE(isPrime(15));

// Equality tests
ASSERT_EQUALS(120, factorial(5));
ASSERT_NOT_EQUALS(0, factorial(5));

// Pointer tests
ASSERT_NOT_NULL(createArray(10, 0));
ASSERT_NULL(createArray(0, 0));
```

### String Assertions
```c
char str[50];
stringCopy(str, "Hello");
ASSERT_STRING_EQUALS("Hello", str);
ASSERT_STRING_NOT_EQUALS("World", str);
```

### Edge Case Testing
```c
// Boundary conditions
ASSERT_EQUALS(0, stringLength(""));
ASSERT_EQUALS(1, stringLength("a"));

// Error conditions
ASSERT_EQUALS(-1, factorial(-1));
ASSERT_EQUALS(0, divide(5, 0)); // Division by zero
```

## 🧪 Test Categories

### 1. Unit Tests
Test individual functions in isolation:
```c
void testAddFunction() {
    startTestSuite("Add Function");
    
    // Normal cases
    ASSERT_EQUALS(5, add(2, 3));
    ASSERT_EQUALS(-1, add(-2, 1));
    
    // Boundary cases
    ASSERT_EQUALS(0, add(0, 0));
    ASSERT_EQUALS(INT_MAX, add(INT_MAX - 1, 1));
    
    endTestSuite("Add Function");
}
```

### 2. Integration Tests
Test multiple functions working together:
```c
void testArrayOperations() {
    startTestSuite("Array Operations");
    
    int *arr = createArray(5, 10);
    ASSERT_NOT_NULL(arr);
    
    ASSERT_EQUALS(10, findMax(arr, 5));
    ASSERT_TRUE(containsValue(arr, 5, 10));
    
    freeArray(&arr);
    ASSERT_NULL(arr);
    
    endTestSuite("Array Operations");
}
```

### 3. Performance Tests
Basic performance measurement:
```c
void testPerformance() {
    startTestSuite("Performance");
    
    clock_t start = clock();
    
    // Perform operation
    int result = fibonacci(30);
    
    clock_t end = clock();
    double time = ((double)(end - start)) / CLOCKS_PER_SEC;
    
    ASSERT_TRUE(result > 0);
    ASSERT_TRUE(time < 1.0); // Should complete in < 1 second
    
    printf("Fibonacci(30) took %f seconds\n", time);
    
    endTestSuite("Performance");
}
```

### 4. Edge Case Tests
Test boundary and error conditions:
```c
void testEdgeCases() {
    startTestSuite("Edge Cases");
    
    // Empty inputs
    ASSERT_EQUALS(0, stringLength(""));
    ASSERT_NULL(createArray(0, 0));
    
    // Invalid inputs
    ASSERT_EQUALS(-1, factorial(-1));
    ASSERT_EQUALS(0, divide(5, 0));
    
    // Maximum values
    ASSERT_EQUALS(INT_MAX, findMax((int[]){INT_MAX}, 1));
    
    endTestSuite("Edge Cases");
}
```

## 📈 Test Organization

### Recommended Structure
```c
// 1. Include framework and headers
#include <stdio.h>
#include <stdlib.h>
// ... other includes

// 2. Functions to test
int add(int a, int b) { return a + b; }

// 3. Test functions
void testMath() { /* tests */ }
void testStrings() { /* tests */ }

// 4. Main test runner
int main() {
    resetTestResults();
    testMath();
    testStrings();
    printTestSummary();
    return 0;
}
```

### Test Naming Conventions
- Use descriptive names: `testAddFunction`, `testStringCopy`
- Group related tests: `testMathFunctions`, `testStringOperations`
- Include test type: `testEdgeCases`, `testPerformance`

## 🔍 Best Practices

### 1. Test Coverage
- **Normal Cases**: Typical usage scenarios
- **Edge Cases**: Boundary conditions, empty inputs
- **Error Cases**: Invalid inputs, NULL pointers
- **Performance Cases**: Speed and resource usage

### 2. Test Independence
```c
// Good - Each test is independent
void testFunction1() {
    int *arr = createArray(5, 0);
    ASSERT_NOT_NULL(arr);
    freeArray(&arr);
}

void testFunction2() {
    int *arr = createArray(3, 1);
    ASSERT_NOT_NULL(arr);
    freeArray(&arr);
}
```

### 3. Clear Test Names
```c
// Good - Descriptive names
void testAddFunctionWithPositiveNumbers()
void testAddFunctionWithNegativeNumbers()
void testAddFunctionWithZero()

// Bad - Vague names
void test1()
void test2()
void testAdd()
```

### 4. Meaningful Assertions
```c
// Good - Clear what's being tested
ASSERT_EQUALS(5, add(2, 3)); // Testing normal addition
ASSERT_EQUALS(0, add(-2, 2)); // Testing addition with negatives

// Bad - Unclear purpose
ASSERT_EQUALS(5, result); // What is result testing?
```

## ⚠️ Common Pitfalls

### 1. Testing Implementation Details
```c
// Bad - Tests internal implementation
ASSERT_EQUALS(42, internal_counter); // Tests private variable

// Good - Tests public behavior
ASSERT_EQUALS(42, getPublicValue()); // Tests public interface
```

### 2. Hardcoded Test Data
```c
// Bad - Magic numbers
ASSERT_EQUALS(42, calculateSomething());

// Good - Meaningful test data
const int EXPECTED_RESULT = 42;
ASSERT_EQUALS(EXPECTED_RESULT, calculateSomething());
```

### 3. Missing Edge Cases
```c
// Incomplete - Only tests normal case
ASSERT_EQUALS(5, add(2, 3));

// Complete - Tests various cases
ASSERT_EQUALS(5, add(2, 3));      // Normal
ASSERT_EQUALS(0, add(0, 0));      // Zero
ASSERT_EQUALS(-1, add(-2, 1));    // Negative
```

### 4. Not Testing Error Conditions
```c
// Missing error testing
ASSERT_EQUALS(5, divide(10, 2));

// Complete with error testing
ASSERT_EQUALS(5, divide(10, 2));   // Normal
ASSERT_EQUALS(0, divide(10, 0));   // Error case
```

## 🚀 Advanced Features

### 1. Custom Assertions
```c
#define ASSERT_IN_RANGE(value, min, max) \
    do { \
        g_testResults.total_tests++; \
        if ((value) >= (min) && (value) <= (max)) { \
            g_testResults.passed_tests++; \
            printf("✓ PASS: %d is in range [%d, %d]\n", (value), (min), (max)); \
        } else { \
            g_testResults.failed_tests++; \
            printf("✗ FAIL: %d not in range [%d, %d] (Line %d)\n", \
                   (value), (min), (max), __LINE__); \
        } \
    } while(0)

// Usage
ASSERT_IN_RANGE(temperature, -50, 150);
```

### 2. Test Data Generation
```c
void generateTestData(int *array, int size, int min, int max) {
    for (int i = 0; i < size; i++) {
        array[i] = min + rand() % (max - min + 1);
    }
}

void testWithRandomData() {
    startTestSuite("Random Data Tests");
    
    int testArray[100];
    generateTestData(testArray, 100, 1, 1000);
    
    int max = findMax(testArray, 100);
    ASSERT_TRUE(max >= 1 && max <= 1000);
    
    endTestSuite("Random Data Tests");
}
```

### 3. Memory Leak Detection
```c
void testMemoryManagement() {
    startTestSuite("Memory Management");
    
    int *arr = createArray(1000, 0);
    ASSERT_NOT_NULL(arr);
    
    // Use array...
    
    freeArray(&arr);
    ASSERT_NULL(arr); // Verify array was freed
    
    endTestSuite("Memory Management");
}
```

## 📊 Test Metrics

### Coverage Metrics
- **Function Coverage**: Percentage of functions tested
- **Branch Coverage**: Percentage of code paths tested
- **Statement Coverage**: Percentage of statements executed

### Success Metrics
- **Pass Rate**: Percentage of tests passing
- **Test Count**: Total number of tests
- **Execution Time**: Time to run all tests

## 🔧 Integration with Build Systems

### Makefile Integration
```makefile
test: test_framework
	./test_framework

test_framework: test_framework.c
	gcc -Wall -Wextra -o test_framework test_framework.c

.PHONY: test
```

### Continuous Integration
```bash
#!/bin/bash
# CI script
make test
if [ $? -eq 0 ]; then
    echo "All tests passed!"
    exit 0
else
    echo "Tests failed!"
    exit 1
fi
```

## 🎓 Learning Outcomes

After using this framework, you should understand:

1. **Test Design**: How to write effective test cases
2. **Assertion Usage**: When and how to use different assertions
3. **Test Organization**: How to structure test suites
4. **Edge Case Testing**: Importance of boundary conditions
5. **Debugging**: Using tests to find and fix bugs
6. **Maintainability**: Keeping tests readable and maintainable

## 🔄 Test-Driven Development

### TDD Workflow
1. **Write Test**: Write a failing test for new functionality
2. **Implement Code**: Write minimal code to make test pass
3. **Refactor**: Improve code while keeping tests passing
4. **Repeat**: Add next test and continue

### Example
```c
// 1. Write failing test
void testNewFeature() {
    ASSERT_EQUALS(10, newFunction(5, 2));
}

// 2. Implement minimal code
int newFunction(int a, int b) {
    return 10; // Just enough to pass
}

// 3. Refactor to correct implementation
int newFunction(int a, int b) {
    return a * b; // Correct implementation
}
```

This simple test framework provides a solid foundation for testing C programs while emphasizing clarity, maintainability, and comprehensive coverage.
