#include <stdio.h>
#include <stdlib.h>
#include <limits.h>

// =============================================================================
// BIT MANIPULATION EXERCISES
// =============================================================================

// Exercise 1: Get Bit
// Get the value of bit at position 'pos' (0-indexed from right)
int getBit(int num, int pos) {
    return (num >> pos) & 1;
}

// Exercise 2: Set Bit
// Set the bit at position 'pos' to 1
int setBit(int num, int pos) {
    return num | (1 << pos);
}

// Exercise 3: Clear Bit
// Clear the bit at position 'pos' (set to 0)
int clearBit(int num, int pos) {
    return num & ~(1 << pos);
}

// Exercise 4: Toggle Bit
// Toggle the bit at position 'pos' (0->1, 1->0)
int toggleBit(int num, int pos) {
    return num ^ (1 << pos);
}

// Exercise 5: Update Bit
// Update bit at position 'pos' to value 'value' (0 or 1)
int updateBit(int num, int pos, int value) {
    int mask = ~(1 << pos);
    return (num & mask) | ((value << pos) & (1 << pos));
}

// Exercise 6: Count Set Bits (Brian Kernighan's Algorithm)
int countSetBits(int num) {
    int count = 0;
    while (num) {
        num &= (num - 1); // Clear the rightmost set bit
        count++;
    }
    return count;
}

// Exercise 7: Count Set Bits (Simple Method)
int countSetBitsSimple(int num) {
    int count = 0;
    while (num) {
        count += num & 1;
        num >>= 1;
    }
    return count;
}

// Exercise 8: Find Rightmost Set Bit
int findRightmostSetBit(int num) {
    if (num == 0) return -1;
    
    // Isolate rightmost set bit
    return num & -num;
}

// Exercise 9: Find Position of Rightmost Set Bit
int positionOfRightmostSetBit(int num) {
    if (num == 0) return -1;
    
    int position = 1;
    while ((num & 1) == 0) {
        num >>= 1;
        position++;
    }
    return position;
}

// Exercise 10: Check if Power of 2
int isPowerOf2(int num) {
    // Power of 2 has only one set bit
    return num && !(num & (num - 1));
}

// Exercise 11: Find Next Power of 2
int nextPowerOf2(int num) {
    if (num <= 1) return 1;
    
    // Find the next power of 2 greater than or equal to num
    num--;
    num |= num >> 1;
    num |= num >> 2;
    num |= num >> 4;
    num |= num >> 8;
    num |= num >> 16;
    num++;
    
    return num;
}

// Exercise 12: Swap Two Numbers (Using XOR)
void swapNumbers(int *a, int *b) {
    if (a == b) return; // Same address, no swap needed
    
    *a = *a ^ *b;
    *b = *a ^ *b;
    *a = *a ^ *b;
}

// Exercise 13: Reverse Bits
unsigned int reverseBits(unsigned int num) {
    unsigned int reversed = 0;
    int bits = sizeof(num) * 8; // Number of bits
    
    for (int i = 0; i < bits; i++) {
        reversed <<= 1;
        reversed |= (num & 1);
        num >>= 1;
    }
    
    return reversed;
}

// Exercise 14: Check if Two Numbers Have Opposite Signs
int haveOppositeSigns(int a, int b) {
    // XOR result will have sign bit set if signs are opposite
    return (a ^ b) < 0;
}

// Exercise 15: Absolute Value (Without using abs())
int absoluteValue(int num) {
    int mask = num >> (sizeof(int) * 8 - 1);
    return (num ^ mask) - mask;
}

// Exercise 16: Maximum of Two Numbers (Without using if-else)
int maximum(int a, int b) {
    return a ^ ((a ^ b) & -(a < b));
}

// Exercise 17: Minimum of Two Numbers (Without using if-else)
int minimum(int a, int b) {
    return b ^ ((a ^ b) & -(a < b));
}

// Exercise 18: Multiply by 2 (Using left shift)
int multiplyBy2(int num) {
    return num << 1;
}

// Exercise 19: Divide by 2 (Using right shift)
int divideBy2(int num) {
    return num >> 1;
}

// Exercise 20: Check if Kth Bit is Set
int isKthBitSet(int num, int k) {
    return (num >> k) & 1;
}

// Exercise 21: Set All Bits from MSB to Kth Bit
int setBitsFromMSB(int num, int k) {
    int mask = ~((1 << k) - 1);
    return num | mask;
}

// Exercise 22: Clear All Bits from MSB to Kth Bit
int clearBitsFromMSB(int num, int k) {
    int mask = (1 << k) - 1;
    return num & mask;
}

// Exercise 23: Check if Number is Even or Odd
int isEven(int num) {
    return !(num & 1);
}

// Exercise 24: Multiply Two Numbers Using Bit Manipulation
int multiplyUsingBits(int a, int b) {
    int result = 0;
    
    while (b) {
        // If current bit of b is set, add a to result
        if (b & 1) {
            result += a;
        }
        
        // Double a and halve b
        a <<= 1;
        b >>= 1;
    }
    
    return result;
}

// Exercise 25: Add Two Numbers Using Bit Manipulation
int addUsingBits(int a, int b) {
    while (b != 0) {
        // Carry now contains common set bits of a and b
        int carry = a & b;
        
        // Sum of bits where at least one is not set
        a = a ^ b;
        
        // Shift carry by one
        b = carry << 1;
    }
    
    return a;
}

// Exercise 26: Subtract Two Numbers Using Bit Manipulation
int subtractUsingBits(int a, int b) {
    while (b != 0) {
        // Borrow contains common set bits of b and unset bits of a
        int borrow = (~a) & b;
        
        // Subtraction of bits where at least one is not set
        a = a ^ b;
        
        // Shift borrow by one
        b = borrow << 1;
    }
    
    return a;
}

// Exercise 27: Find Missing Number in Array (Using XOR)
int findMissingNumber(int arr[], int n) {
    // XOR all numbers from 1 to n
    int xor1 = 0;
    for (int i = 1; i <= n; i++) {
        xor1 ^= i;
    }
    
    // XOR all elements in array
    int xor2 = 0;
    for (int i = 0; i < n - 1; i++) {
        xor2 ^= arr[i];
    }
    
    // Missing number is XOR of both results
    return xor1 ^ xor2;
}

// Exercise 28: Find Single Number in Array (Others Appear Twice)
int findSingleNumber(int arr[], int n) {
    int result = 0;
    for (int i = 0; i < n; i++) {
        result ^= arr[i];
    }
    return result;
}

// Exercise 29: Find Two Numbers That Appear Once (Others Appear Twice)
void findTwoSingleNumbers(int arr[], int n, int *num1, int *num2) {
    int xorResult = 0;
    
    // XOR all elements
    for (int i = 0; i < n; i++) {
        xorResult ^= arr[i];
    }
    
    // Find rightmost set bit in xorResult
    int rightmostSetBit = xorResult & -xorResult;
    
    // Divide array into two groups based on rightmost set bit
    *num1 = 0;
    *num2 = 0;
    
    for (int i = 0; i < n; i++) {
        if (arr[i] & rightmostSetBit) {
            *num1 ^= arr[i];
        } else {
            *num2 ^= arr[i];
        }
    }
}

// Exercise 30: Check if Binary Representation is Palindrome
int isBinaryPalindrome(int num) {
    if (num == 0) return 1;
    
    int left = sizeof(int) * 8 - 1;
    
    // Skip leading zeros
    while (left >= 0 && !getBit(num, left)) {
        left--;
    }
    
    int right = 0;
    
    while (left > right) {
        if (getBit(num, left) != getBit(num, right)) {
            return 0;
        }
        left--;
        right++;
    }
    
    return 1;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicBitOperations() {
    printf("=== BASIC BIT OPERATIONS ===\n");
    
    int num = 42; // Binary: 101010
    printf("Original number: %d (Binary: ", num);
    for (int i = 7; i >= 0; i--) {
        printf("%d", getBit(num, i));
    }
    printf(")\n");
    
    printf("Get bit at position 1: %d\n", getBit(num, 1));
    printf("Set bit at position 0: %d\n", setBit(num, 0));
    printf("Clear bit at position 3: %d\n", clearBit(num, 3));
    printf("Toggle bit at position 2: %d\n", toggleBit(num, 2));
    printf("Update bit at position 1 to 0: %d\n", updateBit(num, 1, 0));
    
    printf("\n");
}

void demonstrateBitCounting() {
    printf("=== BIT COUNTING ===\n");
    
    int numbers[] = {0, 1, 7, 15, 16, 31, 42, 255};
    int size = sizeof(numbers) / sizeof(numbers[0]);
    
    for (int i = 0; i < size; i++) {
        int num = numbers[i];
        printf("Number: %3d, Set bits (Brian): %2d, Set bits (Simple): %2d\n",
               num, countSetBits(num), countSetBitsSimple(num));
    }
    
    printf("\n");
}

void demonstrateBitProperties() {
    printf("=== BIT PROPERTIES ===\n");
    
    int numbers[] = {1, 2, 3, 4, 5, 6, 7, 8, 16, 31, 32, 33};
    int size = sizeof(numbers) / sizeof(numbers[0]);
    
    for (int i = 0; i < size; i++) {
        int num = numbers[i];
        printf("Number: %2d, Power of 2: %s, Next Power of 2: %2d\n",
               num, isPowerOf2(num) ? "Yes" : "No", nextPowerOf2(num));
    }
    
    printf("\n");
}

void demonstrateBitManipulation() {
    printf("=== BIT MANIPULATION ===\n");
    
    int a = 10, b = 20;
    printf("Before swap: a = %d, b = %d\n", a, b);
    swapNumbers(&a, &b);
    printf("After swap: a = %d, b = %d\n", a, b);
    
    int num = 13; // Binary: 1101
    printf("\nOriginal: %d (Binary: ", num);
    for (int i = 7; i >= 0; i--) {
        printf("%d", getBit(num, i));
    }
    printf(")\n");
    
    unsigned int reversed = reverseBits(num);
    printf("Reversed: %u (Binary: ", reversed);
    for (int i = 7; i >= 0; i--) {
        printf("%d", getBit(reversed, i));
    }
    printf(")\n");
    
    printf("Opposite signs test:\n");
    printf("10 and 20: %s\n", haveOppositeSigns(10, 20) ? "Yes" : "No");
    printf("10 and -20: %s\n", haveOppositeSigns(10, -20) ? "Yes" : "No");
    
    printf("\n");
}

void demonstrateArithmeticOperations() {
    printf("=== ARITHMETIC OPERATIONS USING BITS ===\n");
    
    int x = 15, y = 7;
    printf("Arithmetic operations:\n");
    printf("x = %d, y = %d\n", x, y);
    printf("x + y (using bits): %d\n", addUsingBits(x, y));
    printf("x - y (using bits): %d\n", subtractUsingBits(x, y));
    printf("x * y (using bits): %d\n", multiplyUsingBits(x, y));
    printf("x * 2 (using left shift): %d\n", multiplyBy2(x));
    printf("x / 2 (using right shift): %d\n", divideBy2(x));
    
    printf("\n");
}

void demonstrateArrayProblems() {
    printf("=== ARRAY PROBLEMS USING BITS ===\n");
    
    // Missing number problem
    int arr1[] = {1, 2, 4, 5, 6}; // Missing 3
    int n1 = 6;
    int missing = findMissingNumber(arr1, n1);
    printf("Missing number in array {1, 2, 4, 5, 6}: %d\n", missing);
    
    // Single number problem
    int arr2[] = {2, 3, 5, 4, 5, 3, 4}; // Single: 2
    int n2 = sizeof(arr2) / sizeof(arr2[0]);
    int single = findSingleNumber(arr2, n2);
    printf("Single number in array {2, 3, 5, 4, 5, 3, 4}: %d\n", single);
    
    // Two single numbers problem
    int arr3[] = {4, 1, 2, 1, 2, 3}; // Singles: 3, 4
    int n3 = sizeof(arr3) / sizeof(arr3[0]);
    int num1, num2;
    findTwoSingleNumbers(arr3, n3, &num1, &num2);
    printf("Two single numbers in array {4, 1, 2, 1, 2, 3}: %d and %d\n", num1, num2);
    
    printf("\n");
}

void demonstrateAdvancedOperations() {
    printf("=== ADVANCED BIT OPERATIONS ===\n");
    
    int num = 42;
    printf("Number: %d\n", num);
    printf("Absolute value: %d\n", absoluteValue(-42));
    printf("Maximum of %d and %d: %d\n", num, 35, maximum(num, 35));
    printf("Minimum of %d and %d: %d\n", num, 35, minimum(num, 35));
    printf("Is even: %s\n", isEven(num) ? "Yes" : "No");
    printf("Is binary palindrome: %s\n", isBinaryPalindrome(num) ? "Yes" : "No");
    
    printf("\nBit manipulation examples:\n");
    printf("Set bits from MSB to position 3: %d\n", setBitsFromMSB(num, 3));
    printf("Clear bits from MSB to position 3: %d\n", clearBitsFromMSB(num, 3));
    printf("Rightmost set bit: %d\n", findRightmostSetBit(num));
    printf("Position of rightmost set bit: %d\n", positionOfRightmostSetBit(num));
    
    printf("\n");
}

// Helper function to print binary representation
void printBinary(int num) {
    for (int i = 31; i >= 0; i--) {
        printf("%d", getBit(num, i));
        if (i % 8 == 0) printf(" ");
    }
}

int main() {
    printf("Bit Manipulation Exercises\n");
    printf("==========================\n\n");
    
    demonstrateBasicBitOperations();
    demonstrateBitCounting();
    demonstrateBitProperties();
    demonstrateBitManipulation();
    demonstrateArithmeticOperations();
    demonstrateArrayProblems();
    demonstrateAdvancedOperations();
    
    printf("All bit manipulation exercises demonstrated!\n");
    return 0;
}
