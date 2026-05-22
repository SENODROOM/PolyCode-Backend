# Array Manipulation Exercises

This file contains 12 comprehensive exercises covering various array operations in C. These exercises help develop essential skills in array handling, algorithmic thinking, and problem-solving.

## 📝 Exercise List

### Exercise 1: Find Maximum Element
**Problem**: Find the largest element in an array.
**Key Concepts**: Array traversal, comparison operations
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 2: Find Minimum Element
**Problem**: Find the smallest element in an array.
**Key Concepts**: Array traversal, comparison operations
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 3: Calculate Average
**Problem**: Calculate the average of all elements in an array.
**Key Concepts**: Array traversal, summation, floating-point division
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 4: Search for Element
**Problem**: Search for a specific element and return its index.
**Key Concepts**: Linear search, array traversal
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 5: Count Occurrences
**Problem**: Count how many times a specific element appears.
**Key Concepts**: Array traversal, counting
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 6: Remove Duplicates
**Problem**: Remove duplicate elements from an array in-place.
**Key Concepts**: Array manipulation, nested loops, in-place operations
**Time Complexity**: O(n²)
**Space Complexity**: O(1)

### Exercise 7: Merge Two Arrays
**Problem**: Merge two arrays into a single array.
**Key Concepts**: Array copying, memory management
**Time Complexity**: O(n + m)
**Space Complexity**: O(n + m)

### Exercise 8: Rotate Array Left
**Problem**: Rotate array elements to the left by k positions.
**Key Concepts**: Array rotation, modulo arithmetic
**Time Complexity**: O(n × k)
**Space Complexity**: O(1)

### Exercise 9: Rotate Array Right
**Problem**: Rotate array elements to the right by k positions.
**Key Concepts**: Array rotation, modulo arithmetic
**Time Complexity**: O(n × k)
**Space Complexity**: O(1)

### Exercise 10: Find Second Largest
**Problem**: Find the second largest element in an array.
**Key Concepts**: Array traversal, tracking multiple values
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 11: Check if Array is Sorted
**Problem**: Determine if an array is sorted in ascending order.
**Key Concepts**: Array traversal, comparison
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### Exercise 12: Find Pair with Sum
**Problem**: Find two elements whose sum equals a target value.
**Key Concepts**: Nested loops, two-sum problem
**Time Complexity**: O(n²)
**Space Complexity**: O(1)

## 🎯 Learning Objectives

After completing these exercises, you should master:

1. **Array Fundamentals**: Declaration, initialization, and access
2. **Array Traversal**: Iterating through array elements
3. **Search Operations**: Linear search and element location
4. **Array Modification**: In-place operations and transformations
5. **Algorithm Analysis**: Understanding time and space complexity
6. **Edge Case Handling**: Empty arrays, single elements, duplicates
7. **Memory Management**: Understanding array memory layout
8. **Problem Decomposition**: Breaking complex problems into simpler steps

## 💡 Optimization Techniques

### 1. Remove Duplicates Optimization
The current implementation uses O(n²) time. For better performance:
- Sort the array first: O(n log n) + O(n) = O(n log n)
- Use a hash set: O(n) time, O(n) space

### 2. Array Rotation Optimization
Current implementation uses O(n × k) time. Better approaches:
- **Juggling Algorithm**: O(n) time, O(1) space
- **Reversal Algorithm**: O(n) time, O(1) space

### 3. Pair Sum Optimization
Current implementation uses O(n²) time. Better approaches:
- **Hash Table**: O(n) time, O(n) space
- **Two Pointers** (if sorted): O(n) time, O(1) space

## 🚀 Advanced Extensions

### 1. Multi-dimensional Arrays
- Matrix operations (addition, multiplication, transpose)
- 2D array traversal patterns
- Spiral traversal

### 2. Dynamic Arrays
- Implement resizable arrays
- Memory reallocation strategies
- Amortized analysis

### 3. Specialized Array Types
- Circular arrays
- Sparse arrays
- Jagged arrays

### 4. Advanced Algorithms
- Kadane's algorithm (maximum subarray sum)
- Sliding window techniques
- Prefix sums and range queries

## ⚠️ Common Pitfalls

### 1. Index Out of Bounds
```c
// Wrong
for (int i = 0; i <= size; i++) { // Should be i < size
    arr[i] = 0;
}

// Correct
for (int i = 0; i < size; i++) {
    arr[i] = 0;
}
```

### 2. Modifying Array During Traversal
```c
// Dangerous - can affect loop conditions
for (int i = 0; i < size; i++) {
    if (arr[i] == target) {
        removeElement(arr, &size, i); // size changes
    }
}
```

### 3. Not Handling Edge Cases
- Empty arrays (size = 0)
- Single-element arrays (size = 1)
- Arrays with all identical elements

### 4. Integer Overflow
```c
// Potential overflow
int sum = 0;
for (int i = 0; i < size; i++) {
    sum += arr[i]; // Can overflow for large arrays
}

// Better
long long sum = 0;
```

## 🧪 Testing Strategies

### 1. Test Cases
- **Empty array**: size = 0
- **Single element**: size = 1
- **Sorted array**: ascending order
- **Reverse sorted**: descending order
- **All same elements**: duplicates
- **Mixed positive/negative**: variety of values
- **Large array**: performance testing

### 2. Test Framework Example
```c
void testFindMaximum() {
    // Test empty array
    int empty[] = {};
    assert(findMaximum(empty, 0) == -1);
    
    // Test single element
    int single[] = {5};
    assert(findMaximum(single, 1) == 5);
    
    // Test normal case
    int normal[] = {1, 5, 3, 9, 2};
    assert(findMaximum(normal, 5) == 9);
    
    // Test with negatives
    int negatives[] = {-5, -2, -8, -1};
    assert(findMaximum(negatives, 4) == -1);
}
```

## 📊 Complexity Analysis

| Exercise | Time Complexity | Space Complexity | Difficulty |
|----------|-----------------|------------------|------------|
| Find Maximum | O(n) | O(1) | Easy |
| Find Minimum | O(n) | O(1) | Easy |
| Calculate Average | O(n) | O(1) | Easy |
| Search Element | O(n) | O(1) | Easy |
| Count Occurrences | O(n) | O(1) | Easy |
| Remove Duplicates | O(n²) | O(1) | Medium |
| Merge Arrays | O(n + m) | O(n + m) | Easy |
| Rotate Left | O(n × k) | O(1) | Medium |
| Rotate Right | O(n × k) | O(1) | Medium |
| Find Second Largest | O(n) | O(1) | Medium |
| Check Sorted | O(n) | O(1) | Easy |
| Find Pair with Sum | O(n²) | O(1) | Medium |

## 🔧 Real-World Applications

1. **Data Processing**: Finding statistics (min, max, average)
2. **Search Systems**: Element location and counting
3. **Image Processing**: Pixel manipulation using 2D arrays
4. **Game Development**: Grid-based games, inventory systems
5. **Scientific Computing**: Numerical analysis, simulations
6. **Database Systems**: Record management and indexing

## 🎓 Next Steps

After mastering array manipulation:
1. Study **Dynamic Memory Allocation** for flexible array sizes
2. Learn **Pointer Arithmetic** for advanced array operations
3. Explore **Data Structures** like linked lists and stacks
4. Practice **Algorithm Optimization** techniques
5. Work on **Multi-dimensional Array** problems

Remember: Arrays are foundational to programming. Master these exercises before moving to more complex data structures!
