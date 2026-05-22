#include <stdio.h>
#include <stdlib.h>
#include <string.h>

// =============================================================================
// RECURSION EXERCISES
// =============================================================================

// Exercise 1: Factorial (Classic Recursion)
long long factorialRecursive(int n) {
    if (n < 0) return -1; // Error case
    if (n == 0 || n == 1) return 1;
    return n * factorialRecursive(n - 1);
}

// Exercise 2: Fibonacci (Memoized Recursion)
long long fibMemo[100];

long long fibonacciMemoized(int n) {
    if (n < 0) return -1;
    if (n == 0 || n == 1) return n;
    
    if (fibMemo[n] != -1) {
        return fibMemo[n];
    }
    
    fibMemo[n] = fibonacciMemoized(n - 1) + fibonacciMemoized(n - 2);
    return fibMemo[n];
}

// Exercise 3: Power Function
double powerRecursive(double base, int exponent) {
    if (exponent == 0) return 1.0;
    if (exponent < 0) return 1.0 / powerRecursive(base, -exponent);
    
    // Optimized: power(base, n) = power(base², n/2) if n is even
    if (exponent % 2 == 0) {
        double half = powerRecursive(base, exponent / 2);
        return half * half;
    } else {
        return base * powerRecursive(base, exponent - 1);
    }
}

// Exercise 4: Greatest Common Divisor (Euclidean Algorithm)
int gcdRecursive(int a, int b) {
    if (b == 0) return a;
    return gcdRecursive(b, a % b);
}

// Exercise 5: Binary Search (Recursive)
int binarySearchRecursive(int arr[], int left, int right, int key) {
    if (left > right) return -1;
    
    int mid = left + (right - left) / 2;
    
    if (arr[mid] == key) return mid;
    if (arr[mid] < key) return binarySearchRecursive(arr, mid + 1, right, key);
    return binarySearchRecursive(arr, left, mid - 1, key);
}

// Exercise 6: Sum of Array Elements
int sumArrayRecursive(int arr[], int size) {
    if (size <= 0) return 0;
    return arr[0] + sumArrayRecursive(arr + 1, size - 1);
}

// Exercise 7: Find Maximum in Array
int findMaxRecursive(int arr[], int size) {
    if (size == 1) return arr[0];
    
    int maxInRest = findMaxRecursive(arr + 1, size - 1);
    return (arr[0] > maxInRest) ? arr[0] : maxInRest;
}

// Exercise 8: String Length (Recursive)
int stringLengthRecursive(const char *str) {
    if (*str == '\0') return 0;
    return 1 + stringLengthRecursive(str + 1);
}

// Exercise 9: String Reverse (Recursive)
void stringReverseRecursive(char *str) {
    if (*str == '\0') return;
    
    stringReverseRecursive(str + 1);
    
    // Print characters in reverse order
    printf("%c", *str);
}

// Exercise 10: Check Palindrome (Recursive)
int isPalindromeRecursive(const char *str, int left, int right) {
    if (left >= right) return 1; // Base case
    if (str[left] != str[right]) return 0; // Mismatch
    return isPalindromeRecursive(str, left + 1, right - 1);
}

// Exercise 11: Tower of Hanoi
void towerOfHanoi(int n, char from, char to, char aux) {
    if (n == 1) {
        printf("Move disk 1 from %c to %c\n", from, to);
        return;
    }
    
    towerOfHanoi(n - 1, from, aux, to);
    printf("Move disk %d from %c to %c\n", n, from, to);
    towerOfHanoi(n - 1, aux, to, from);
}

// Exercise 12: Sum of Digits
int sumOfDigitsRecursive(int n) {
    if (n == 0) return 0;
    return (n % 10) + sumOfDigitsRecursive(n / 10);
}

// Exercise 13: Reverse Number
int reverseNumberRecursive(int n, int reversed) {
    if (n == 0) return reversed;
    return reverseNumberRecursive(n / 10, reversed * 10 + n % 10);
}

// Exercise 14: Count Set Bits (Recursive)
int countSetBitsRecursive(unsigned int n) {
    if (n == 0) return 0;
    return (n & 1) + countSetBitsRecursive(n >> 1);
}

// Exercise 15: Check Prime (Recursive)
int isPrimeRecursive(int n, int divisor) {
    if (n <= 2) return (n == 2) ? 1 : 0;
    if (divisor * divisor > n) return 1;
    if (n % divisor == 0) return 0;
    return isPrimeRecursive(n, divisor + 1);
}

// Exercise 16: Permutations of String
void swap(char *x, char *y) {
    char temp = *x;
    *x = *y;
    *y = temp;
}

void printPermutations(char *str, int start, int end) {
    if (start == end) {
        printf("%s\n", str);
        return;
    }
    
    for (int i = start; i <= end; i++) {
        swap(&str[start], &str[i]);
        printPermutations(str, start + 1, end);
        swap(&str[start], &str[i]); // Backtrack
    }
}

// Exercise 17: Subsets of Set
void printSubsets(int arr[], int n, int index, int *subset, int subsetSize) {
    if (index == n) {
        printf("{ ");
        for (int i = 0; i < subsetSize; i++) {
            printf("%d ", subset[i]);
        }
        printf("}\n");
        return;
    }
    
    // Exclude current element
    printSubsets(arr, n, index + 1, subset, subsetSize);
    
    // Include current element
    subset[subsetSize] = arr[index];
    printSubsets(arr, n, index + 1, subset, subsetSize + 1);
}

// Exercise 18: Decimal to Binary (Recursive)
void decimalToBinaryRecursive(int n) {
    if (n == 0) return;
    decimalToBinaryRecursive(n / 2);
    printf("%d", n % 2);
}

// Exercise 19: Tree Traversal Simulations (using arrays)

// Preorder traversal simulation
void preorderRecursive(int arr[], int index, int size) {
    if (index >= size || arr[index] == -1) return;
    
    printf("%d ", arr[index]); // Visit root
    preorderRecursive(arr, 2 * index + 1, size); // Left subtree
    preorderRecursive(arr, 2 * index + 2, size); // Right subtree
}

// Inorder traversal simulation
void inorderRecursive(int arr[], int index, int size) {
    if (index >= size || arr[index] == -1) return;
    
    inorderRecursive(arr, 2 * index + 1, size); // Left subtree
    printf("%d ", arr[index]); // Visit root
    inorderRecursive(arr, 2 * index + 2, size); // Right subtree
}

// Postorder traversal simulation
void postorderRecursive(int arr[], int index, int size) {
    if (index >= size || arr[index] == -1) return;
    
    postorderRecursive(arr, 2 * index + 1, size); // Left subtree
    postorderRecursive(arr, 2 * index + 2, size); // Right subtree
    printf("%d ", arr[index]); // Visit root
}

// Exercise 20: Ackermann Function (Advanced)
int ackermann(int m, int n) {
    if (m == 0) return n + 1;
    if (n == 0) return ackermann(m - 1, 1);
    return ackermann(m - 1, ackermann(m, n - 1));
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicRecursion() {
    printf("=== BASIC RECURSION EXERCISES ===\n");
    
    // Factorial
    printf("Factorial of 5: %lld\n", factorialRecursive(5));
    
    // Fibonacci (with memoization)
    for (int i = 0; i < 20; i++) {
        fibMemo[i] = -1;
    }
    printf("Fibonacci of 10: %lld\n", fibonacciMemoized(10));
    
    // Power
    printf("2^8: %.0f\n", powerRecursive(2, 8));
    
    // GCD
    printf("GCD of 48 and 18: %d\n", gcdRecursive(48, 18));
    
    // Sum of digits
    printf("Sum of digits in 1234: %d\n\n", sumOfDigitsRecursive(1234));
}

void demonstrateArrayRecursion() {
    printf("=== ARRAY RECURSION EXERCISES ===\n");
    
    int arr[] = {1, 5, 3, 9, 2, 8};
    int size = sizeof(arr) / sizeof(arr[0]);
    
    printf("Array: ");
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
    
    printf("Sum of array: %d\n", sumArrayRecursive(arr, size));
    printf("Maximum element: %d\n", findMaxRecursive(arr, size));
    
    // Binary search
    int sortedArr[] = {1, 2, 3, 4, 5, 6, 7, 8, 9};
    int sortedSize = sizeof(sortedArr) / sizeof(sortedArr[0]);
    int key = 5;
    int result = binarySearchRecursive(sortedArr, 0, sortedSize - 1, key);
    printf("Binary search for %d: %s\n", key, result != -1 ? "Found" : "Not found");
    
    printf("\n");
}

void demonstrateStringRecursion() {
    printf("=== STRING RECURSION EXERCISES ===\n");
    
    const char *testStr = "hello";
    printf("String: \"%s\"\n", testStr);
    printf("Length (recursive): %d\n", stringLengthRecursive(testStr));
    
    printf("String reversed: ");
    char strCopy[] = "hello";
    stringReverseRecursive(strCopy);
    printf("\n");
    
    printf("Is palindrome: %s\n", 
           isPalindromeRecursive(testStr, 0, strlen(testStr) - 1) ? "Yes" : "No");
    
    printf("Is \"racecar\" palindrome: %s\n", 
           isPalindromeRecursive("racecar", 0, 6) ? "Yes" : "No");
    
    printf("\n");
}

void demonstrateMathRecursion() {
    printf("=== MATHEMATICAL RECURSION EXERCISES ===\n");
    
    // Reverse number
    int num = 1234;
    int reversed = reverseNumberRecursive(num, 0);
    printf("Reverse of %d: %d\n", num, reversed);
    
    // Count set bits
    unsigned int binaryNum = 13; // 1101 in binary
    printf("Set bits in %u: %d\n", binaryNum, countSetBitsRecursive(binaryNum));
    
    // Check prime
    int primeNum = 17;
    printf("Is %d prime: %s\n", primeNum, 
           isPrimeRecursive(primeNum, 2) ? "Yes" : "No");
    
    // Decimal to binary
    printf("Binary of 10: ");
    decimalToBinaryRecursive(10);
    printf("\n\n");
}

void demonstrateAdvancedRecursion() {
    printf("=== ADVANCED RECURSION EXERCISES ===\n");
    
    // Tower of Hanoi
    printf("Tower of Hanoi (3 disks):\n");
    towerOfHanoi(3, 'A', 'C', 'B');
    
    // Permutations
    printf("\nPermutations of \"ABC\":\n");
    char permStr[] = "ABC";
    printPermutations(permStr, 0, 2);
    
    // Subsets
    printf("Subsets of {1, 2, 3}:\n");
    int set[] = {1, 2, 3};
    int subset[3];
    printSubsets(set, 3, 0, subset, 0);
    
    // Tree traversals (simulated with array)
    printf("Tree traversals (array representation):\n");
    int tree[] = {1, 2, 3, 4, 5, -1, 6}; // -1 represents null
    int treeSize = sizeof(tree) / sizeof(tree[0]);
    
    printf("Preorder: ");
    preorderRecursive(tree, 0, treeSize);
    printf("\n");
    
    printf("Inorder: ");
    inorderRecursive(tree, 0, treeSize);
    printf("\n");
    
    printf("Postorder: ");
    postorderRecursive(tree, 0, treeSize);
    printf("\n");
    
    // Ackermann function (be careful with large values)
    printf("\nAckermann(2, 2): %d\n", ackermann(2, 2));
}

// =============================================================================
// PERFORMANCE TEST
// =============================================================================

void performanceTest() {
    printf("\n=== PERFORMANCE COMPARISON ===\n");
    
    clock_t start, end;
    
    // Test factorial performance
    start = clock();
    long long result = factorialRecursive(20);
    end = clock();
    double time = ((double)(end - start)) / CLOCKS_PER_SEC;
    printf("Factorial(20) = %lld, Time: %f seconds\n", result, time);
    
    // Test Fibonacci with and without memoization
    start = clock();
    // Reset memo array
    for (int i = 0; i < 40; i++) {
        fibMemo[i] = -1;
    }
    long long fibResult = fibonacciMemoized(35);
    end = clock();
    time = ((double)(end - start)) / CLOCKS_PER_SEC;
    printf("Fibonacci(35) with memoization = %lld, Time: %f seconds\n", fibResult, time);
}

int main() {
    printf("Recursion Exercises\n");
    printf("==================\n\n");
    
    demonstrateBasicRecursion();
    demonstrateArrayRecursion();
    demonstrateStringRecursion();
    demonstrateMathRecursion();
    demonstrateAdvancedRecursion();
    performanceTest();
    
    printf("\nAll recursion exercises demonstrated!\n");
    return 0;
}
