# Basic Programming Exercises

This file contains 12 fundamental programming exercises that help build core C programming skills. Each exercise includes a problem description, solution approach, and common pitfalls to avoid.

## 📝 Exercise List

### Exercise 1: Factorial Calculator
**Problem**: Calculate the factorial of a given number.
**Key Concepts**: Loops, integer overflow handling
**Solution**: Iterative approach to avoid stack overflow
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 2: Fibonacci Sequence
**Problem**: Generate the first n terms of the Fibonacci sequence.
**Key Concepts**: Iterative sequences, mathematical patterns
**Solution**: Iterative approach for efficiency
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 3: Prime Number Checker
**Problem**: Determine if a given number is prime.
**Key Concepts**: Mathematical algorithms, optimization
**Solution**: Check divisibility up to √n
**Time Complexity**: O(√n)
**Space Complexity**: O(1)

### Exercise 4: Palindrome Checker
**Problem**: Check if a string reads the same forwards and backwards.
**Key Concepts**: String manipulation, two-pointer technique
**Solution**: Compare characters from both ends
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 5: Array Reversal
**Problem**: Reverse the elements of an array in-place.
**Key Concepts**: Array manipulation, swapping
**Solution**: Swap elements symmetrically
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 6: String Length Calculator
**Problem**: Calculate the length of a string without using strlen().
**Key Concepts**: String traversal, null termination
**Solution**: Count characters until null terminator
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 7: Greatest Common Divisor (GCD)
**Problem**: Find the GCD of two numbers using Euclidean algorithm.
**Key Concepts**: Mathematical algorithms, recursion/iteration
**Solution**: Repeated division until remainder is 0
**Time Complexity**: O(log min(a,b))
**Space Complexity**: O(1)

### Exercise 8: Sum of Digits
**Problem**: Calculate the sum of digits in a number.
**Key Concepts**: Number manipulation, modulo operation
**Solution**: Extract digits using modulo 10
**Time Complexity**: O(log n)
**Space Complexity**: O(1)

### Exercise 9: Power Function
**Problem**: Calculate base^exponent without using pow().
**Key Concepts**: Mathematical operations, handling negative exponents
**Solution**: Iterative multiplication
**Time Complexity**: O(|exponent|)
**Space Complexity**: O(1)

### Exercise 10: Binary to Decimal Conversion
**Problem**: Convert a binary string to decimal number.
**Key Concepts**: Number systems, positional notation
**Solution**: Process each bit with appropriate power of 2
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 11: Decimal to Binary Conversion
**Problem**: Convert a decimal number to binary string.
**Key Concepts**: Number systems, division algorithm
**Solution**: Repeated division by 2
**Time Complexity**: O(log n)
**Space Complexity**: O(log n)

### Exercise 12: Vowel Counter
**Problem**: Count the number of vowels in a string.
**Key Concepts**: String traversal, character classification
**Solution**: Check each character against vowel set
**Time Complexity**: O(n)
**Space Complexity**: O(1)

## 🎯 Learning Objectives

After completing these exercises, you should master:

1. **Loop Structures**: for, while, do-while loops
2. **Conditional Logic**: if-else statements, switch-case
3. **Array Manipulation**: Accessing, modifying, reversing arrays
4. **String Handling**: Traversal, comparison, length calculation
5. **Mathematical Operations**: Basic arithmetic, modulo, power functions
6. **Algorithm Design**: Problem-solving approaches and optimization
7. **Memory Management**: Understanding stack vs heap, avoiding overflow
8. **Debugging Skills**: Common error identification and resolution

## 💡 Problem-Solving Strategies

### 1. Understand the Problem
- Read the problem statement carefully
- Identify input/output requirements
- Consider edge cases

### 2. Plan Your Approach
- Break down the problem into smaller steps
- Choose appropriate data structures
- Consider multiple solution approaches

### 3. Implement the Solution
- Write clean, readable code
- Add comments where necessary
- Test with sample inputs

### 4. Test and Debug
- Test with various inputs (edge cases, normal cases)
- Check for logical errors
- Optimize if necessary

## ⚠️ Common Pitfalls

### 1. Off-by-One Errors
- Incorrect loop boundaries
- Array index mistakes
- String termination issues

### 2. Integer Overflow
- Factorial calculations
- Large number operations
- Power function results

### 3. Null Pointer Issues
- Uninitialized strings
- Array bounds checking
- Memory access errors

### 4. Logic Errors
- Incorrect conditionals
- Wrong mathematical formulas
- Incomplete edge case handling

## 🚀 Extension Exercises

### Easy Extensions
1. **Factorial**: Handle large numbers with arrays
2. **Fibonacci**: Generate up to a maximum value
3. **Prime Numbers**: Generate all primes up to n
4. **Palindrome**: Ignore case and non-alphanumeric characters

### Medium Extensions
1. **Array Operations**: Rotate, shift, merge arrays
2. **String Operations**: Substring search, replacement
3. **Number Systems**: Octal, hexadecimal conversions
4. **Mathematical Functions**: Square root, trigonometric functions

### Hard Extensions
1. **Advanced Algorithms**: Sieve of Eratosthenes for primes
2. **Data Structures**: Implement stacks, queues
3. **Recursion**: Convert iterative solutions to recursive
4. **Memory Management**: Dynamic allocation for large problems

## 🧪 Testing Your Solutions

### Test Cases to Consider
1. **Normal Cases**: Typical input values
2. **Edge Cases**: Minimum/maximum values, empty inputs
3. **Error Cases**: Invalid inputs, negative numbers
4. **Boundary Cases**: Values at algorithm limits

### Sample Test Framework
```c
void testFactorial() {
    assert(factorial(0) == 1);
    assert(factorial(1) == 1);
    assert(factorial(5) == 120);
    assert(factorial(-1) == -1); // Error case
}
```

## 📈 Progress Tracking

### Difficulty Levels
- **Beginner**: Exercises 1-4 (basic concepts)
- **Intermediate**: Exercises 5-8 (arrays and strings)
- **Advanced**: Exercises 9-12 (mathematical operations)

### Time Estimates
- **Beginner**: 15-30 minutes per exercise
- **Intermediate**: 20-45 minutes per exercise
- **Advanced**: 30-60 minutes per exercise

## 🎓 Next Steps

After mastering these exercises:
1. Move to **Data Structures** exercises
2. Practice **Algorithm** implementations
3. Work on **Project** development
4. Study **Advanced Topics** like pointers and memory management

Remember: Practice is key to programming mastery. Try to solve each exercise independently before looking at the solution!
