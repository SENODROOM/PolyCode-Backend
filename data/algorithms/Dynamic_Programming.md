# Dynamic Programming Algorithms

This file contains implementations of classic dynamic programming problems in C. Dynamic programming is a method for solving complex problems by breaking them down into simpler subproblems.

## 📚 Algorithm Overview

Dynamic programming is characterized by:
- **Optimal Substructure**: Optimal solution can be constructed from optimal solutions of subproblems
- **Overlapping Subproblems**: Same subproblems are solved multiple times
- **Memoization**: Store results of subproblems to avoid recomputation
- **Tabulation**: Bottom-up approach building solutions iteratively

## 🔍 Algorithms Implemented

### 1. Fibonacci Sequence
**Problem**: Compute nth Fibonacci number
**Approaches**: 
- **Memoization** (Top-down): Cache results of recursive calls
- **Tabulation** (Bottom-up): Build table iteratively

**Time Complexity**: O(n) for both approaches
**Space Complexity**: O(n) for tabulation, O(n) for memoization (plus recursion stack)

### 2. Longest Common Subsequence (LCS)
**Problem**: Find longest sequence present in both strings in same order
**Application**: DNA sequence analysis, version control, plagiarism detection

**Time Complexity**: O(m × n) where m, n are string lengths
**Space Complexity**: O(m × n)

### 3. 0/1 Knapsack Problem
**Problem**: Maximize value of items in knapsack with weight constraint
**Application**: Resource allocation, investment decisions, cargo loading

**Time Complexity**: O(n × W) where n is items, W is capacity
**Space Complexity**: O(n × W)

### 4. Coin Change Problem
**Problem**: Find minimum number of coins to make given amount
**Application**: Currency systems, vending machines, making change

**Time Complexity**: O(amount × number of coins)
**Space Complexity**: O(amount)

### 5. Matrix Chain Multiplication
**Problem**: Find optimal parenthesization for matrix multiplication
**Application**: Database query optimization, compiler optimization

**Time Complexity**: O(n³) where n is number of matrices
**Space Complexity**: O(n²)

### 6. Edit Distance (Levenshtein Distance)
**Problem**: Find minimum operations to convert one string to another
**Application**: Spell checkers, DNA sequencing, plagiarism detection

**Time Complexity**: O(m × n) where m, n are string lengths
**Space Complexity**: O(m × n)

### 7. Longest Increasing Subsequence (LIS)
**Problem**: Find longest increasing subsequence in array
**Application**: Stock analysis, sequence analysis, pattern recognition

**Time Complexity**: O(n²) basic implementation
**Space Complexity**: O(n)

### 8. Subset Sum Problem
**Problem**: Determine if subset exists that sums to target
**Application**: Partition problems, resource allocation, cryptography

**Time Complexity**: O(n × sum) where n is set size, sum is target
**Space Complexity**: O(n × sum)

## 💡 Key Concepts

### Memoization vs Tabulation

#### Memoization (Top-Down)
```c
// Store results of expensive function calls
long long fibMemo[MAX_SIZE];
long long fibonacciMemoization(int n) {
    if (n <= 1) return n;
    
    if (fibMemo[n] != -1) {  // Check cache
        return fibMemo[n];
    }
    
    fibMemo[n] = fibonacciMemoization(n - 1) + fibonacciMemoization(n - 2);
    return fibMemo[n];
}
```

**Advantages**:
- Natural recursive structure
- Computes only needed subproblems
- Easy to understand

**Disadvantages**:
- Recursion overhead
- Stack depth limitations
- May compute unnecessary subproblems

#### Tabulation (Bottom-Up)
```c
// Build table iteratively
long long fibonacciTabulation(int n) {
    if (n <= 1) return n;
    
    long long fib[MAX_SIZE];
    fib[0] = 0;
    fib[1] = 1;
    
    for (int i = 2; i <= n; i++) {
        fib[i] = fib[i - 1] + fib[i - 2];  // Build up
    }
    
    return fib[n];
}
```

**Advantages**:
- No recursion overhead
- Guaranteed to compute all needed subproblems
- Better cache performance
- Can be space-optimized

**Disadvantages**:
- May compute unnecessary subproblems
- Less intuitive for some problems
- Requires careful ordering

### State Definition

The key to dynamic programming is defining the state:

```c
// Example: LCS state definition
// lcs[i][j] = length of LCS of str1[0..i-1] and str2[0..j-1]

if (str1[i - 1] == str2[j - 1]) {
    lcs[i][j] = lcs[i - 1][j - 1] + 1;  // Characters match
} else {
    lcs[i][j] = MAX(lcs[i - 1][j], lcs[i][j - 1]);  // Characters don't match
}
```

### Transition Relations

Each DP problem has a transition relation:

```c
// Knapsack transition
// dp[i][w] = maximum value using first i items with capacity w

if (weights[i - 1] <= w) {
    dp[i][w] = MAX(values[i - 1] + dp[i - 1][w - weights[i - 1]],  // Take item
                   dp[i - 1][w]);                                    // Skip item
} else {
    dp[i][w] = dp[i - 1][w];  // Can't take item
}
```

## 🚀 Optimization Techniques

### 1. Space Optimization
Many DP problems can be optimized to use O(n) space:

```c
// Original: O(n × W) space
int knapsack[n + 1][W + 1];

// Optimized: O(W) space
int knapsack[2][W + 1];  // Only need current and previous row
```

### 2. Rolling Arrays
Use only necessary previous states:

```c
// For problems where only previous state matters
int dp[2][size];
int current = 0, previous = 1;

for (int i = 0; i < n; i++) {
    current = 1 - current;  // Swap roles
    previous = 1 - current;
    
    // Use dp[current] and dp[previous]
}
```

### 3. Path Reconstruction
Store additional information to reconstruct solutions:

```c
// Store parent pointers or decisions
int parent[MAX_SIZE][MAX_SIZE];

// When updating DP table
if (new_value > dp[i][j]) {
    dp[i][j] = new_value;
    parent[i][j] = decision;  // Store how we got here
}
```

## 📊 Complexity Analysis

| Problem | Time | Space | Optimizable Space |
|---------|------|-------|------------------|
| Fibonacci | O(n) | O(n) | O(1) |
| LCS | O(m×n) | O(m×n) | O(min(m,n)) |
| Knapsack | O(n×W) | O(n×W) | O(W) |
| Coin Change | O(amount×coins) | O(amount) | O(amount) |
| Matrix Chain | O(n³) | O(n²) | O(n²) |
| Edit Distance | O(m×n) | O(m×n) | O(min(m,n)) |
| LIS | O(n²) | O(n) | O(n) |
| Subset Sum | O(n×sum) | O(n×sum) | O(sum) |

## 🧪 Testing Strategies

### 1. Small Test Cases
```c
// Test with known results
assert(fibonacciTabulation(10) == 55);
assert(longestCommonSubsequence("ABCBDAB", "BDCABA") == 4);
```

### 2. Edge Cases
```c
// Empty inputs
assert(longestCommonSubsequence("", "ABC") == 0);
assert(zeroOneKnapsack(empty_weights, empty_values, 0, 0) == 0);
```

### 3. Large Test Cases
```c
// Performance testing
int large_array[1000];
// Test with large inputs to verify complexity
```

### 4. Boundary Conditions
```c
// Maximum values, minimum values
assert(fibonacciTabulation(0) == 0);
assert(fibonacciTabulation(1) == 1);
```

## ⚠️ Common Pitfalls

### 1. Incorrect State Definition
```c
// Wrong: State doesn't capture all necessary information
// Right: State includes all relevant parameters
```

### 2. Wrong Base Cases
```c
// Missing or incorrect base cases lead to wrong answers
dp[0][0] = 0;  // Always initialize base cases properly
```

### 3. Off-by-One Errors
```c
// Be careful with array indices
// dp[i][j] usually corresponds to first i elements, not i-th element
```

### 4. Integer Overflow
```c
// Use appropriate data types
long long result;  // For large Fibonacci numbers
```

### 5. Memory Issues
```c
// Check array bounds
#define MAX_SIZE 1000  // Ensure sufficient size
```

## 🔧 Real-World Applications

### 1. Bioinformatics
- **Sequence Alignment**: LCS, Edit Distance for DNA/protein sequences
- **Phylogenetic Trees**: Finding evolutionary relationships
- **Gene Prediction**: Pattern recognition in genomic data

### 2. Computer Science
- **Compiler Optimization**: Instruction scheduling, register allocation
- **Database Query Optimization**: Join order optimization
- **Network Routing**: Shortest path algorithms

### 3. Economics and Finance
- **Portfolio Optimization**: Knapsack-like problems
- **Option Pricing**: Binomial option pricing model
- **Resource Allocation**: Optimal distribution of resources

### 4. Operations Research
- **Supply Chain Management**: Inventory optimization
- **Production Planning**: Scheduling problems
- **Transportation**: Route optimization

## 🎓 Learning Path

### Beginner Level
1. **Fibonacci**: Understand basic DP concepts
2. **Factorial**: Simple recursion with memoization
3. **Coin Change**: Basic optimization problem

### Intermediate Level
1. **LCS**: String-based DP problems
2. **Knapsack**: Classic optimization
3. **Edit Distance**: String transformation

### Advanced Level
1. **Matrix Chain Multiplication**: Complex DP
2. **Subset Sum**: NP-complete problems
3. **Custom DP Problems**: Design new DP solutions

## 🔄 Problem-Solving Framework

### 1. Identify DP Structure
- Does problem have optimal substructure?
- Are there overlapping subproblems?
- Can we define a state?

### 2. Define State
- What parameters define the subproblem?
- How many dimensions does the state need?
- What are the bounds on each dimension?

### 3. Find Recurrence
- How do we relate larger problems to smaller ones?
- What are the base cases?
- What is the transition relation?

### 4. Implement Solution
- Choose memoization or tabulation
- Handle base cases properly
- Optimize space if possible

### 5. Test and Verify
- Test with small examples
- Check edge cases
- Verify complexity

Dynamic programming is a powerful technique that transforms exponential-time problems into polynomial-time solutions through clever reuse of subproblem solutions. Master these patterns to solve a wide range of optimization problems!
