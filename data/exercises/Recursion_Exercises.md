# Recursion Exercises

This file contains 20 comprehensive recursion exercises covering fundamental concepts, mathematical problems, array operations, string manipulation, and advanced recursive patterns. Recursion is a powerful problem-solving technique where a function calls itself to solve smaller instances of the same problem.

## 📚 Exercise Categories

### 🎯 Basic Recursion
Fundamental recursive patterns and classic examples

### 📊 Array Recursion  
Recursive operations on arrays and searching

### 🔤 String Recursion
String manipulation using recursive approaches

### 🔢 Mathematical Recursion
Mathematical problems solved recursively

### 🚀 Advanced Recursion
Complex recursive patterns and combinatorial problems

## 🔍 Exercise List

### 1. Factorial (Classic Recursion)
**Problem**: Calculate factorial of a number using recursion
**Base Case**: n = 0 or n = 1
**Recursive Case**: n × factorial(n-1)
**Time Complexity**: O(n)
**Space Complexity**: O(n) due to call stack

### 2. Fibonacci (Memoized Recursion)
**Problem**: Calculate nth Fibonacci number with memoization
**Optimization**: Cache results to avoid recomputation
**Time Complexity**: O(n) with memoization, O(2^n) without
**Space Complexity**: O(n) for cache + call stack

### 3. Power Function
**Problem**: Calculate base^exponent recursively
**Optimization**: Use exponentiation by squaring for even exponents
**Time Complexity**: O(log n) optimized, O(n) naive
**Space Complexity**: O(log n) optimized

### 4. Greatest Common Divisor (Euclidean Algorithm)
**Problem**: Find GCD using recursive Euclidean algorithm
**Base Case**: b = 0, return a
**Recursive Case**: gcd(b, a % b)
**Time Complexity**: O(log min(a,b))
**Space Complexity**: O(log min(a,b))

### 5. Binary Search (Recursive)
**Problem**: Search for element in sorted array recursively
**Base Case**: left > right (not found)
**Recursive Case**: Search left or right half
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### 6. Sum of Array Elements
**Problem**: Calculate sum of array elements recursively
**Base Case**: empty array (size = 0)
**Recursive Case**: first element + sum of rest
**Time Complexity**: O(n)
**Space Complexity**: O(n)

### 7. Find Maximum in Array
**Problem**: Find maximum element recursively
**Base Case**: single element array
**Recursive Case**: max(first, max of rest)
**Time Complexity**: O(n)
**Space Complexity**: O(n)

### 8. String Length (Recursive)
**Problem**: Calculate string length without using loops
**Base Case**: null terminator
**Recursive Case**: 1 + length of rest
**Time Complexity**: O(n)
**Space Complexity**: O(n)

### 9. String Reverse (Recursive)
**Problem**: Print string in reverse order recursively
**Base Case**: empty string
**Recursive Case**: reverse rest, then print first
**Time Complexity**: O(n)
**Space Complexity**: O(n)

### 10. Check Palindrome (Recursive)
**Problem**: Check if string reads the same forwards and backwards
**Base Case**: left >= right
**Recursive Case**: compare characters and move inward
**Time Complexity**: O(n/2)
**Space Complexity**: O(n/2)

### 11. Tower of Hanoi
**Problem**: Move disks between pegs following rules
**Base Case**: single disk
**Recursive Case**: Move n-1 disks, then nth disk, then n-1 disks
**Time Complexity**: O(2^n)
**Space Complexity**: O(n)

### 12. Sum of Digits
**Problem**: Calculate sum of digits in a number
**Base Case**: n = 0
**Recursive Case**: last digit + sum of remaining
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### 13. Reverse Number
**Problem**: Reverse digits of a number recursively
**Base Case**: n = 0
**Recursive Case**: build reversed number digit by digit
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### 14. Count Set Bits
**Problem**: Count number of 1s in binary representation
**Base Case**: n = 0
**Recursive Case**: last bit + count of remaining bits
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### 15. Check Prime (Recursive)
**Problem**: Check if number is prime recursively
**Base Case**: divisor² > n
**Recursive Case**: check divisibility by increasing divisors
**Time Complexity**: O(√n)
**Space Complexity**: O(√n)

### 16. Permutations of String
**Problem**: Generate all permutations of a string
**Base Case**: single character
**Recursive Case**: Fix each character, permute rest
**Time Complexity**: O(n!)
**Space Complexity**: O(n!)

### 17. Subsets of Set
**Problem**: Generate all subsets of a set
**Base Case**: all elements processed
**Recursive Case**: include/exclude each element
**Time Complexity**: O(2^n)
**Space Complexity**: O(n)

### 18. Decimal to Binary (Recursive)
**Problem**: Convert decimal number to binary recursively
**Base Case**: n = 0
**Recursive Case**: convert n/2, then print n%2
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### 19. Tree Traversal Simulations
**Problem**: Simulate tree traversals using array representation
**Preorder**: Root, Left, Right
**Inorder**: Left, Root, Right  
**Postorder**: Left, Right, Root
**Time Complexity**: O(n)
**Space Complexity**: O(n)

### 20. Ackermann Function
**Problem**: Implement Ackermann function (advanced)
**Base Cases**: m = 0, n = 0
**Recursive Cases**: Complex nested recursion
**Time Complexity**: Extremely fast-growing
**Space Complexity**: Varies with input

## 💡 Key Recursion Concepts

### Base Case and Recursive Case
Every recursive function must have:
1. **Base Case**: Condition to stop recursion
2. **Recursive Case**: Function calling itself with modified input

```c
int factorial(int n) {
    if (n <= 1) return 1;           // Base case
    return n * factorial(n - 1);    // Recursive case
}
```

### Tail Recursion
Recursive call is the last operation:
```c
// Tail recursive
int factorialTail(int n, int accumulator) {
    if (n <= 1) return accumulator;
    return factorialTail(n - 1, n * accumulator);
}
```

### Memoization
Cache results to avoid recomputation:
```c
int fibMemo[MAX_SIZE];

int fibonacci(int n) {
    if (n <= 1) return n;
    if (fibMemo[n] != -1) return fibMemo[n]; // Cache hit
    
    return fibMemo[n] = fibonacci(n - 1) + fibonacci(n - 2);
}
```

## 🚀 Recursion Patterns

### 1. Linear Recursion
Single recursive call per function:
```c
int sumArray(int arr[], int n) {
    if (n == 0) return 0;
    return arr[0] + sumArray(arr + 1, n - 1);
}
```

### 2. Binary Recursion
Two recursive calls per function:
```c
int fibonacci(int n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2);
}
```

### 3. Multiple Recursion
Multiple recursive calls:
```c
void towerOfHanoi(int n, char from, char to, char aux) {
    if (n == 1) return;
    towerOfHanoi(n - 1, from, aux, to);
    towerOfHanoi(n - 1, aux, to, from);
}
```

### 4. Nested Recursion
Recursive call as parameter:
```c
int ackermann(int m, int n) {
    if (m == 0) return n + 1;
    return ackermann(m - 1, ackermann(m, n - 1));
}
```

## 📊 Complexity Analysis

| Exercise | Time | Space | Type |
|----------|------|-------|------|
| Factorial | O(n) | O(n) | Linear |
| Fibonacci (memoized) | O(n) | O(n) | Binary |
| Power (optimized) | O(log n) | O(log n) | Binary |
| Binary Search | O(log n) | O(log n) | Binary |
| Tower of Hanoi | O(2^n) | O(n) | Multiple |
| Permutations | O(n!) | O(n) | Multiple |
| Subsets | O(2^n) | O(n) | Binary |

## 🧪 Testing Strategies

### 1. Base Case Testing
```c
void testBaseCases() {
    assert(factorial(0) == 1);
    assert(factorial(1) == 1);
    assert(sumArray(emptyArray, 0) == 0);
}
```

### 2. Edge Case Testing
```c
void testEdgeCases() {
    assert(factorial(-1) == -1); // Error case
    assert(binarySearch(arr, 0, -1, key) == -1); // Invalid range
}
```

### 3. Performance Testing
```c
void performanceTest() {
    clock_t start = clock();
    long long result = factorial(20);
    clock_t end = clock();
    double time = ((double)(end - start)) / CLOCKS_PER_SEC;
    printf("Time: %f seconds\n", time);
}
```

### 4. Recursion Depth Testing
```c
void testRecursionDepth() {
    // Test with large inputs to check stack overflow
    // May need to increase stack size for deep recursion
}
```

## ⚠️ Common Pitfalls

### 1. Missing Base Case
```c
// Wrong - infinite recursion
int factorial(int n) {
    return n * factorial(n - 1); // No base case!
}

// Right - with base case
int factorial(int n) {
    if (n <= 1) return 1; // Base case
    return n * factorial(n - 1);
}
```

### 2. Wrong Base Case
```c
// Wrong - incorrect base condition
int factorial(int n) {
    if (n == 0) return 0; // Should return 1
    return n * factorial(n - 1);
}
```

### 3. Stack Overflow
```c
// Dangerous - deep recursion
int fibonacci(int n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2); // Exponential calls
}

// Better - use memoization or iteration
```

### 4. Inefficient Recursion
```c
// Inefficient - recalculates same values
int fibonacci(int n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2);
}

// Efficient - with memoization
```

### 5. Modifying Input Incorrectly
```c
// Wrong - modifies array pointer
int sumArray(int arr[], int n) {
    if (n == 0) return 0;
    return arr[0] + sumArray(arr + 1, n - 1); // Pointer moves
}

// Better - use index parameter
int sumArrayIndexed(int arr[], int n, int index) {
    if (index >= n) return 0;
    return arr[index] + sumArrayIndexed(arr, n, index + 1);
}
```

## 🔧 Optimization Techniques

### 1. Memoization
Cache results to avoid recomputation:
```c
#define MAX_SIZE 1000
int memo[MAX_SIZE];

int fibonacci(int n) {
    if (n <= 1) return n;
    if (memo[n] != -1) return memo[n];
    return memo[n] = fibonacci(n - 1) + fibonacci(n - 2);
}
```

### 2. Tail Recursion Optimization
Make recursive call the last operation:
```c
// Compiler can optimize to loop
int factorialTail(int n, int acc) {
    if (n <= 1) return acc;
    return factorialTail(n - 1, n * acc);
}
```

### 3. Iterative Conversion
Convert recursion to iteration for better performance:
```c
// Iterative factorial
int factorialIterative(int n) {
    int result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}
```

### 4. Divide and Conquer
Break problem into smaller subproblems:
```c
int power(int base, int exp) {
    if (exp == 0) return 1;
    if (exp % 2 == 0) {
        int half = power(base, exp / 2);
        return half * half;
    }
    return base * power(base, exp - 1);
}
```

## 🎓 Learning Path

### Beginner Level
1. **Basic Patterns**: Factorial, sum, array operations
2. **String Recursion**: Length, reverse, palindrome
3. **Mathematical**: GCD, power, digit operations

### Intermediate Level
1. **Searching**: Binary search
2. **Combinatorial**: Permutations, subsets
3. **Classic Problems**: Tower of Hanoi

### Advanced Level
1. **Complex Recursion**: Ackermann function
2. **Tree Algorithms**: Traversals, operations
3. **Dynamic Programming**: Memoization patterns

## 🔄 When to Use Recursion

### Good for Recursion
- **Tree structures**: Natural hierarchical representation
- **Divide and conquer**: Problems that split naturally
- **Backtracking**: Generating all possibilities
- **Mathematical definitions**: Problems defined recursively

### Better with Iteration
- **Simple loops**: When recursion adds overhead
- **Large inputs**: To avoid stack overflow
- **Performance critical**: When optimization is needed
- **Linear processing**: Simple sequential operations

## 🧠 Debugging Recursive Functions

### 1. Print Debugging
```c
int factorial(int n) {
    printf("factorial(%d)\n", n); // Debug print
    if (n <= 1) return 1;
    return n * factorial(n - 1);
}
```

### 2. Stack Visualization
Track call stack manually:
```
factorial(4)
  factorial(3)
    factorial(2)
      factorial(1) -> returns 1
    returns 2 * 1 = 2
  returns 3 * 2 = 6
returns 4 * 6 = 24
```

### 3. Base Case Verification
Ensure base case is reached:
```c
void testBaseCaseReach() {
    // Test with smallest valid input
    assert(factorial(1) == 1);
    assert(factorial(0) == 1);
}
```

Recursion is a fundamental programming concept that enables elegant solutions to complex problems. Master these exercises to develop strong recursive thinking and problem-solving skills!
