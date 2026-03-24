# Sorting Algorithms

This file contains implementations of various sorting algorithms in C, along with explanations of their time and space complexity.

## 📚 Algorithms Covered

### 1. Bubble Sort
- **Time Complexity**: O(n²) worst and average case, O(n) best case (when array is already sorted)
- **Space Complexity**: O(1)
- **Description**: Repeatedly swaps adjacent elements if they are in wrong order
- **When to use**: Educational purposes, small datasets, nearly sorted data

### 2. Selection Sort
- **Time Complexity**: O(n²) in all cases
- **Space Complexity**: O(1)
- **Description**: Finds the minimum element and places it at the beginning
- **When to use**: Small datasets, when memory writes are expensive

### 3. Insertion Sort
- **Time Complexity**: O(n²) worst and average case, O(n) best case
- **Space Complexity**: O(1)
- **Description**: Builds sorted array one item at a time by inserting each element into its correct position
- **When to use**: Small datasets, nearly sorted data, online algorithms

### 4. Merge Sort
- **Time Complexity**: O(n log n) in all cases
- **Space Complexity**: O(n)
- **Description**: Divide and conquer algorithm that splits array into halves and merges them back
- **When to use**: Large datasets, stable sorting needed, worst-case performance guaranteed

### 5. Quick Sort
- **Time Complexity**: O(n log n) average case, O(n²) worst case
- **Space Complexity**: O(log n) average case, O(n) worst case
- **Description**: Picks a pivot element and partitions array around it
- **When to use**: Large datasets, average-case performance important, in-place sorting needed

## 🔍 Key Concepts

### Stability
- **Stable algorithms**: Maintain relative order of equal elements
- **Stable**: Bubble Sort, Insertion Sort, Merge Sort
- **Unstable**: Selection Sort, Quick Sort (standard implementation)

### In-place Sorting
- **In-place**: Sorts without requiring extra memory proportional to input size
- **In-place**: Bubble Sort, Selection Sort, Insertion Sort, Quick Sort
- **Not in-place**: Merge Sort (requires O(n) extra space)

### Adaptive Algorithms
- **Adaptive**: Takes advantage of existing order in input
- **Adaptive**: Bubble Sort, Insertion Sort
- **Non-adaptive**: Selection Sort, Merge Sort, Quick Sort

## 💡 Usage Tips

1. **For small arrays (< 50 elements)**: Use Insertion Sort
2. **For nearly sorted arrays**: Use Insertion Sort or Bubble Sort
3. **For large arrays**: Use Quick Sort (average case) or Merge Sort (worst case)
4. **When stability is required**: Use Merge Sort
5. **When memory is limited**: Use Quick Sort or Heap Sort

## 🧪 Testing the Code

Compile and run the program:
```bash
gcc -o sorting Sorting_Algorithms.c
./sorting
```

The program will demonstrate all five sorting algorithms on the same input array and show the sorted results.

## 📊 Performance Comparison

| Algorithm | Best Case | Average Case | Worst Case | Space | Stable |
|-----------|-----------|--------------|------------|-------|---------|
| Bubble Sort | O(n) | O(n²) | O(n²) | O(1) | Yes |
| Selection Sort | O(n²) | O(n²) | O(n²) | O(1) | No |
| Insertion Sort | O(n) | O(n²) | O(n²) | O(1) | Yes |
| Merge Sort | O(n log n) | O(n log n) | O(n log n) | O(n) | Yes |
| Quick Sort | O(n log n) | O(n log n) | O(n²) | O(log n) | No |

## 🚀 Advanced Topics

- **Hybrid Algorithms**: Combining different sorting algorithms
- **External Sorting**: Sorting data that doesn't fit in memory
- **Parallel Sorting**: Using multiple processors to sort faster
- **Optimization Techniques**: Improving constant factors in implementations
