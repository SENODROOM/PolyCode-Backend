# Bit Manipulation Exercises

This file contains 30 comprehensive bit manipulation exercises covering fundamental bit operations, counting techniques, arithmetic operations, and advanced bit-based algorithms. Bit manipulation is a powerful technique for efficient programming, especially in systems programming, algorithms, and optimization.

## 📚 Exercise Categories

### 🔧 Basic Bit Operations
Fundamental bit manipulation techniques

### 🔢 Bit Counting
Counting set bits and finding bit positions

### 🔍 Bit Properties
Analyzing number properties using bits

### ⚡ Bit Manipulation
Advanced manipulation techniques

### 🧮 Arithmetic Operations
Performing arithmetic using bit operations

### 🎯 Array Problems
Solving array problems using bit manipulation

## 🔍 Exercise List

### 1. Get Bit
**Problem**: Get the value of bit at position 'pos' (0-indexed from right)
**Technique**: Right shift and mask
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int getBit(int num, int pos) {
    return (num >> pos) & 1;
}
```

### 2. Set Bit
**Problem**: Set the bit at position 'pos' to 1
**Technique**: OR with mask
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int setBit(int num, int pos) {
    return num | (1 << pos);
}
```

### 3. Clear Bit
**Problem**: Clear the bit at position 'pos' (set to 0)
**Technique**: AND with inverted mask
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int clearBit(int num, int pos) {
    return num & ~(1 << pos);
}
```

### 4. Toggle Bit
**Problem**: Toggle the bit at position 'pos' (0→1, 1→0)
**Technique**: XOR with mask
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int toggleBit(int num, int pos) {
    return num ^ (1 << pos);
}
```

### 5. Update Bit
**Problem**: Update bit at position 'pos' to value 'value' (0 or 1)
**Technique**: Clear then set
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int updateBit(int num, int pos, int value) {
    int mask = ~(1 << pos);
    return (num & mask) | ((value << pos) & (1 << pos));
}
```

### 6. Count Set Bits (Brian Kernighan's Algorithm)
**Problem**: Count number of 1s in binary representation
**Technique**: Clear rightmost set bit repeatedly
**Time Complexity**: O(number of set bits)
**Space Complexity**: O(1)

```c
int countSetBits(int num) {
    int count = 0;
    while (num) {
        num &= (num - 1); // Clear the rightmost set bit
        count++;
    }
    return count;
}
```

### 7. Count Set Bits (Simple Method)
**Problem**: Count set bits using simple iteration
**Technique**: Check each bit
**Time Complexity**: O(number of bits)
**Space Complexity**: O(1)

### 8. Find Rightmost Set Bit
**Problem**: Find the value of rightmost set bit
**Technique**: AND with two's complement
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int findRightmostSetBit(int num) {
    if (num == 0) return -1;
    return num & -num; // Isolate rightmost set bit
}
```

### 9. Position of Rightmost Set Bit
**Problem**: Find position (1-indexed) of rightmost set bit
**Technique**: Right shift until finding 1
**Time Complexity**: O(position of rightmost set bit)
**Space Complexity**: O(1)

### 10. Check if Power of 2
**Problem**: Determine if number is a power of 2
**Technique**: Only one set bit property
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int isPowerOf2(int num) {
    return num && !(num & (num - 1));
}
```

### 11. Find Next Power of 2
**Problem**: Find smallest power of 2 ≥ num
**Technique**: Bit propagation and increment
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 12. Swap Two Numbers (Using XOR)
**Problem**: Swap two numbers without temporary variable
**Technique**: XOR swap algorithm
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
void swapNumbers(int *a, int *b) {
    *a = *a ^ *b;
    *b = *a ^ *b;
    *a = *a ^ *b;
}
```

### 13. Reverse Bits
**Problem**: Reverse bits of a number
**Technique**: Bit-by-bit reversal
**Time Complexity**: O(number of bits)
**Space Complexity**: O(1)

### 14. Check if Two Numbers Have Opposite Signs
**Problem**: Determine if numbers have opposite signs
**Technique**: XOR sign bit check
**Time Complexity**: O(1)
**Space Complexity**: O(1)

```c
int haveOppositeSigns(int a, int b) {
    return (a ^ b) < 0; // XOR result has sign bit set if signs differ
}
```

### 15. Absolute Value (Without using abs())
**Problem**: Calculate absolute value using bit operations
**Technique**: Sign mask manipulation
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 16-17. Maximum/Minimum Without if-else
**Problem**: Find max/min without conditional statements
**Technique**: Bit manipulation with sign bit
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 18-19. Multiply/Divide by 2
**Problem**: Multiply or divide by 2 using bit shifts
**Technique**: Left/right shift operations
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 20. Check if Kth Bit is Set
**Problem**: Check if bit at position k is 1
**Technique**: Right shift and mask
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 21-22. Set/Clear Bits from MSB
**Problem**: Manipulate all bits from MSB to position k
**Technique**: Mask creation and application
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 23. Check Even/Odd
**Problem**: Determine if number is even or odd
**Technique**: Check least significant bit
**Time Complexity**: O(1)
**Space Complexity**: O(1)

### 24. Multiply Using Bit Manipulation
**Problem**: Multiply two numbers using bit operations
**Technique**: Russian peasant multiplication
**Time Complexity**: O(log b) where b is multiplier
**Space Complexity**: O(1)

### 25. Add Using Bit Manipulation
**Problem**: Add two numbers using bit operations
**Technique**: Iterative carry propagation
**Time Complexity**: O(number of bits)
**Space Complexity**: O(1)

### 26. Subtract Using Bit Manipulation
**Problem**: Subtract two numbers using bit operations
**Technique**: Borrow propagation
**Time Complexity**: O(number of bits)
**Space Complexity**: O(1)

### 27. Find Missing Number in Array
**Problem**: Find missing number from 1 to n using XOR
**Technique**: XOR all numbers and array elements
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 28. Find Single Number
**Problem**: Find element appearing once when others appear twice
**Technique**: XOR all elements
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 29. Find Two Single Numbers
**Problem**: Find two elements appearing once when others appear twice
**Technique**: XOR partitioning
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 30. Check Binary Palindrome
**Problem**: Check if binary representation is palindrome
**Technique**: Two-pointer bit comparison
**Time Complexity**: O(number of bits)
**Space Complexity**: O(1)

## 💡 Key Bit Manipulation Concepts

### Bitwise Operators
```c
&  // AND: Sets bit if both bits are 1
|  // OR: Sets bit if either bit is 1
^  // XOR: Sets bit if bits are different
~  // NOT: Inverts all bits
<< // Left shift: Shifts bits left
>> // Right shift: Shifts bits right
```

### Common Masks
```c
(1 << pos)        // Mask with single bit at position pos
~(1 << pos)       // Mask with all bits except position pos
(1 << n) - 1     // Mask with n rightmost bits set
~((1 << n) - 1)  // Mask with n rightmost bits cleared
```

### Bit Properties
- **Power of 2**: Only one set bit
- **Even/Odd**: Determined by LSB
- **Sign**: Determined by MSB
- **Two's Complement**: Negative numbers representation

## 🚀 Advanced Techniques

### 1. Brian Kernighan's Algorithm
Efficient set bit counting:
```c
while (num) {
    num &= (num - 1); // Clear rightmost set bit
    count++;
}
```

### 2. XOR Properties
- `a ^ a = 0`
- `a ^ 0 = a`
- `a ^ b = b ^ a` (Commutative)
- `(a ^ b) ^ c = a ^ (b ^ c)` (Associative)

### 3. Bit Propagation
Set all bits to the right of set bit:
```c
num |= num >> 1;
num |= num >> 2;
num |= num >> 4;
num |= num >> 8;
num |= num >> 16;
```

### 4. Isolate Rightmost Set Bit
```c
int rightmost = num & -num; // Two's complement trick
```

### 5. Clear Rightmost Set Bit
```c
num &= (num - 1);
```

## 📊 Performance Analysis

| Operation | Time | Space | Use Case |
|-----------|------|-------|----------|
| Get/Set/Clear Bit | O(1) | O(1) | Basic operations |
| Count Set Bits | O(k) where k=set bits | O(1) | Population count |
| Reverse Bits | O(log n) | O(1) | Bit reversal |
| Power of 2 Check | O(1) | O(1) | Number properties |
| XOR Operations | O(1) | O(1) | Pair finding |

## 🧪 Testing Strategies

### 1. Edge Cases
```c
void testEdgeCases() {
    assert(getBit(0, 0) == 0); // Zero number
    assert(setBit(INT_MAX, 31) == INT_MAX); // Overflow case
    assert(countSetBits(0) == 0); // No set bits
    assert(isPowerOf2(1) == 1); // Smallest power of 2
}
```

### 2. Boundary Values
```c
void testBoundaries() {
    assert(countSetBits(UINT_MAX) == 32); // All bits set
    assert(isPowerOf2(INT_MAX) == 0); // Not power of 2
    assert(findRightmostSetBit(0) == -1); // No set bits
}
```

### 3. Property Verification
```c
void testProperties() {
    // Test commutativity of XOR
    int a = 10, b = 20;
    assert((a ^ b) == (b ^ a));
    
    // Test power of 2 properties
    assert(isPowerOf2(16) && !isPowerOf2(15));
    
    // Test even/odd
    assert(isEven(10) && !isEven(11));
}
```

## ⚠️ Common Pitfalls

### 1. Operator Precedence
```c
// Wrong
int result = num & 1 << pos; // << has higher precedence

// Right
int result = num & (1 << pos);
```

### 2. Signed vs Unsigned
```c
// Dangerous with right shift of negative numbers
int shifted = -1 >> 1; // Implementation-defined

// Better to use unsigned
unsigned int shifted = (unsigned int)-1 >> 1;
```

### 3. Integer Overflow
```c
// Potential overflow
int result = num << 31; // May overflow

// Check before operation
if (num <= (INT_MAX >> 31)) {
    result = num << 31;
}
```

### 4. Undefined Behavior
```c
// Undefined: shifting by >= width of type
int bad = num << 32; // Undefined for 32-bit int

// Undefined: left shift into sign bit
int bad2 = num << 31; // If num > 1
```

### 5. Endianness Issues
```c
// Platform-dependent behavior
unsigned int num = 0x12345678;
unsigned char *bytes = (unsigned char*)&num;
// Byte order depends on endianness
```

## 🔧 Real-World Applications

### 1. Graphics Programming
- **Color manipulation**: RGB/ARGB channel operations
- **Pixel operations**: Masking and blending
- **Compression**: Bit-level data compression

### 2. Systems Programming
- **Device drivers**: Register manipulation
- **Network protocols**: Packet header parsing
- **File systems**: Permission bits

### 3. Algorithms
- **Hash functions**: Bit mixing and distribution
- **Cryptography**: Bit-level operations
- **Error detection**: Parity bits, checksums

### 4. Performance Optimization
- **Fast math**: Bit-based arithmetic
- **Memory efficiency**: Bit packing
- **Cache optimization**: Bit-based indexing

## 🎓 Learning Path

### Beginner Level
1. **Basic Operations**: Get, set, clear, toggle bits
2. **Simple Properties**: Even/odd, power of 2
3. **Counting**: Basic set bit counting

### Intermediate Level
1. **Advanced Counting**: Brian Kernighan's algorithm
2. **Bit Manipulation**: Swapping, reversing
3. **Arithmetic**: Bit-based addition, multiplication

### Advanced Level
1. **Complex Algorithms**: XOR-based array problems
2. **Optimization**: Bit propagation techniques
3. **System Programming**: Register manipulation

## 🔄 When to Use Bit Manipulation

### Ideal for Bit Manipulation
- **Flag management**: Multiple boolean states
- **Performance critical**: Optimized arithmetic
- **Memory constrained**: Bit packing
- **Low-level programming**: Hardware interaction

### Consider Alternatives
- **Readability**: Clear code vs bit tricks
- **Portability**: Platform-dependent behavior
- **Maintenance**: Complex bit operations
- **Debugging**: Harder to debug bit code

## 🧠 Debugging Bit Operations

### 1. Binary Printing
```c
void printBinary(int num) {
    for (int i = 31; i >= 0; i--) {
        printf("%d", (num >> i) & 1);
        if (i % 8 == 0) printf(" ");
    }
}
```

### 2. Step-by-Step Tracing
```c
int result = num & (num - 1);
// Print: num, num-1, result in binary
```

### 3. Property Verification
```c
// Verify power of 2 property
assert(isPowerOf2(num) == (countSetBits(num) == 1));
```

Bit manipulation is a fundamental skill for efficient programming and systems development. Master these exercises to write optimized, low-level code and solve complex algorithmic problems!
