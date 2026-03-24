#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <limits.h>

#define MAX_SIZE 1000
#define MAX(a, b) ((a) > (b) ? (a) : (b))
#define MIN(a, b) ((a) < (b) ? (a) : (b))

// =============================================================================
// FIBONACCI SEQUENCE (DYNAMIC PROGRAMMING)
// =============================================================================

// Fibonacci with memoization (top-down)
long long fibMemo[MAX_SIZE];
long long fibonacciMemoization(int n) {
    if (n <= 1) return n;
    
    if (fibMemo[n] != -1) {
        return fibMemo[n];
    }
    
    fibMemo[n] = fibonacciMemoization(n - 1) + fibonacciMemoization(n - 2);
    return fibMemo[n];
}

// Fibonacci with tabulation (bottom-up)
long long fibonacciTabulation(int n) {
    if (n <= 1) return n;
    
    long long fib[MAX_SIZE];
    fib[0] = 0;
    fib[1] = 1;
    
    for (int i = 2; i <= n; i++) {
        fib[i] = fib[i - 1] + fib[i - 2];
    }
    
    return fib[n];
}

// =============================================================================
// LONGEST COMMON SUBSEQUENCE (LCS)
// =============================================================================

int lcs[MAX_SIZE][MAX_SIZE];

int longestCommonSubsequence(const char *str1, const char *str2) {
    int m = strlen(str1);
    int n = strlen(str2);
    
    // Initialize table
    for (int i = 0; i <= m; i++) {
        for (int j = 0; j <= n; j++) {
            if (i == 0 || j == 0) {
                lcs[i][j] = 0;
            } else if (str1[i - 1] == str2[j - 1]) {
                lcs[i][j] = lcs[i - 1][j - 1] + 1;
            } else {
                lcs[i][j] = MAX(lcs[i - 1][j], lcs[i][j - 1]);
            }
        }
    }
    
    return lcs[m][n];
}

// Reconstruct LCS string
void reconstructLCS(const char *str1, const char *str2, char *result) {
    int m = strlen(str1);
    int n = strlen(str2);
    int index = lcs[m][n];
    
    result[index] = '\0';
    index--;
    
    int i = m, j = n;
    while (i > 0 && j > 0) {
        if (str1[i - 1] == str2[j - 1]) {
            result[index] = str1[i - 1];
            i--; j--; index--;
        } else if (lcs[i - 1][j] > lcs[i][j - 1]) {
            i--;
        } else {
            j--;
        }
    }
}

// =============================================================================
// KNAPSACK PROBLEM
// =============================================================================

int knapsack[MAX_SIZE][MAX_SIZE];

int zeroOneKnapsack(int weights[], int values[], int n, int capacity) {
    // Initialize table
    for (int i = 0; i <= n; i++) {
        for (int w = 0; w <= capacity; w++) {
            if (i == 0 || w == 0) {
                knapsack[i][w] = 0;
            } else if (weights[i - 1] <= w) {
                knapsack[i][w] = MAX(values[i - 1] + knapsack[i - 1][w - weights[i - 1]],
                                   knapsack[i - 1][w]);
            } else {
                knapsack[i][w] = knapsack[i - 1][w];
            }
        }
    }
    
    return knapsack[n][capacity];
}

// =============================================================================
// COIN CHANGE PROBLEM
// =============================================================================

int coinChange[MAX_SIZE];

int minCoins(int coins[], int numCoins, int amount) {
    // Initialize with infinity
    for (int i = 0; i <= amount; i++) {
        coinChange[i] = INT_MAX;
    }
    coinChange[0] = 0;
    
    for (int i = 1; i <= amount; i++) {
        for (int j = 0; j < numCoins; j++) {
            if (coins[j] <= i) {
                int subResult = coinChange[i - coins[j]];
                if (subResult != INT_MAX && subResult + 1 < coinChange[i]) {
                    coinChange[i] = subResult + 1;
                }
            }
        }
    }
    
    return coinChange[amount] == INT_MAX ? -1 : coinChange[amount];
}

// =============================================================================
// MATRIX CHAIN MULTIPLICATION
// =============================================================================

int matrixChain[MAX_SIZE][MAX_SIZE];

int matrixChainMultiplication(int dims[], int n) {
    // n is number of matrices, dims has n+1 elements
    for (int i = 1; i <= n; i++) {
        matrixChain[i][i] = 0;
    }
    
    // Chain length from 2 to n
    for (int length = 2; length <= n; length++) {
        for (int i = 1; i <= n - length + 1; i++) {
            int j = i + length - 1;
            matrixChain[i][j] = INT_MAX;
            
            for (int k = i; k < j; k++) {
                int cost = matrixChain[i][k] + matrixChain[k + 1][j] + 
                          dims[i - 1] * dims[k] * dims[j];
                if (cost < matrixChain[i][j]) {
                    matrixChain[i][j] = cost;
                }
            }
        }
    }
    
    return matrixChain[1][n];
}

// =============================================================================
// EDIT DISTANCE
// =============================================================================

int editDist[MAX_SIZE][MAX_SIZE];

int editDistance(const char *str1, const char *str2) {
    int m = strlen(str1);
    int n = strlen(str2);
    
    // Initialize table
    for (int i = 0; i <= m; i++) {
        for (int j = 0; j <= n; j++) {
            if (i == 0) {
                editDist[i][j] = j; // Insert all characters of str2
            } else if (j == 0) {
                editDist[i][j] = i; // Remove all characters of str1
            } else if (str1[i - 1] == str2[j - 1]) {
                editDist[i][j] = editDist[i - 1][j - 1];
            } else {
                editDist[i][j] = 1 + MIN(MIN(editDist[i][j - 1],    // Insert
                                         editDist[i - 1][j]),    // Remove
                                         editDist[i - 1][j - 1]); // Replace
            }
        }
    }
    
    return editDist[m][n];
}

// =============================================================================
// LONGEST INCREASING SUBSEQUENCE
// =============================================================================

int lis[MAX_SIZE];

int longestIncreasingSubsequence(int arr[], int n) {
    if (n == 0) return 0;
    
    // Initialize LIS values
    for (int i = 0; i < n; i++) {
        lis[i] = 1;
    }
    
    // Compute LIS values
    for (int i = 1; i < n; i++) {
        for (int j = 0; j < i; j++) {
            if (arr[j] < arr[i] && lis[j] + 1 > lis[i]) {
                lis[i] = lis[j] + 1;
            }
        }
    }
    
    // Find maximum LIS value
    int maxLis = lis[0];
    for (int i = 1; i < n; i++) {
        if (lis[i] > maxLis) {
            maxLis = lis[i];
        }
    }
    
    return maxLis;
}

// =============================================================================
// SUBSET SUM PROBLEM
// =============================================================================

int subsetSum[MAX_SIZE][MAX_SIZE];

int isSubsetSum(int set[], int n, int sum) {
    // Initialize table
    for (int i = 0; i <= n; i++) {
        subsetSum[i][0] = 1; // Sum 0 is always possible
    }
    
    for (int i = 1; i <= sum; i++) {
        subsetSum[0][i] = 0; // Sum > 0 is not possible with empty set
    }
    
    // Fill the table
    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= sum; j++) {
            if (j < set[i - 1]) {
                subsetSum[i][j] = subsetSum[i - 1][j];
            } else {
                subsetSum[i][j] = subsetSum[i - 1][j] || 
                                 subsetSum[i - 1][j - set[i - 1]];
            }
        }
    }
    
    return subsetSum[n][sum];
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateFibonacci() {
    printf("=== FIBONACCI SEQUENCE ===\n");
    
    // Initialize memoization table
    for (int i = 0; i < MAX_SIZE; i++) {
        fibMemo[i] = -1;
    }
    
    int n = 10;
    printf("Fibonacci(%d) using memoization: %lld\n", n, fibonacciMemoization(n));
    printf("Fibonacci(%d) using tabulation: %lld\n\n", n, fibonacciTabulation(n));
}

void demonstrateLCS() {
    printf("=== LONGEST COMMON SUBSEQUENCE ===\n");
    
    const char *str1 = "AGGTAB";
    const char *str2 = "GXTXAYB";
    
    int length = longestCommonSubsequence(str1, str2);
    char result[100];
    reconstructLCS(str1, str2, result);
    
    printf("String 1: %s\n", str1);
    printf("String 2: %s\n", str2);
    printf("LCS Length: %d\n", length);
    printf("LCS String: %s\n\n", result);
}

void demonstrateKnapsack() {
    printf("=== 0/1 KNAPSACK PROBLEM ===\n");
    
    int values[] = {60, 100, 120};
    int weights[] = {10, 20, 30};
    int capacity = 50;
    int n = sizeof(values) / sizeof(values[0]);
    
    int maxValue = zeroOneKnapsack(weights, values, n, capacity);
    printf("Items: {60, 100, 120}\n");
    printf("Weights: {10, 20, 30}\n");
    printf("Capacity: %d\n", capacity);
    printf("Maximum Value: %d\n\n", maxValue);
}

void demonstrateCoinChange() {
    printf("=== COIN CHANGE PROBLEM ===\n");
    
    int coins[] = {1, 3, 4};
    int amount = 6;
    int numCoins = sizeof(coins) / sizeof(coins[0]);
    
    int minCoinsNeeded = minCoins(coins, numCoins, amount);
    printf("Coins: {1, 3, 4}\n");
    printf("Amount: %d\n", amount);
    printf("Minimum coins needed: %d\n\n", minCoinsNeeded);
}

void demonstrateMatrixChain() {
    printf("=== MATRIX CHAIN MULTIPLICATION ===\n");
    
    int dims[] = {10, 30, 5, 60};
    int n = sizeof(dims) / sizeof(dims[0]) - 1; // Number of matrices
    
    int minCost = matrixChainMultiplication(dims, n);
    printf("Matrix dimensions: ");
    for (int i = 0; i < n + 1; i++) {
        printf("%d ", dims[i]);
    }
    printf("\nNumber of matrices: %d\n", n);
    printf("Minimum multiplication cost: %d\n\n", minCost);
}

void demonstrateEditDistance() {
    printf("=== EDIT DISTANCE ===\n");
    
    const char *str1 = "kitten";
    const char *str2 = "sitting";
    
    int distance = editDistance(str1, str2);
    printf("String 1: %s\n", str1);
    printf("String 2: %s\n", str2);
    printf("Edit Distance: %d\n\n", distance);
}

void demonstrateLIS() {
    printf("=== LONGEST INCREASING SUBSEQUENCE ===\n");
    
    int arr[] = {10, 22, 9, 33, 21, 50, 41, 60};
    int n = sizeof(arr) / sizeof(arr[0]);
    
    int length = longestIncreasingSubsequence(arr, n);
    printf("Array: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", arr[i]);
    }
    printf("\nLIS Length: %d\n\n", length);
}

void demonstrateSubsetSum() {
    printf("=== SUBSET SUM PROBLEM ===\n");
    
    int set[] = {3, 34, 4, 12, 5, 2};
    int sum = 9;
    int n = sizeof(set) / sizeof(set[0]);
    
    int possible = isSubsetSum(set, n, sum);
    printf("Set: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", set[i]);
    }
    printf("\nTarget Sum: %d\n", sum);
    printf("Subset possible: %s\n\n", possible ? "Yes" : "No");
}

int main() {
    printf("Dynamic Programming Algorithms\n");
    printf("==============================\n\n");
    
    demonstrateFibonacci();
    demonstrateLCS();
    demonstrateKnapsack();
    demonstrateCoinChange();
    demonstrateMatrixChain();
    demonstrateEditDistance();
    demonstrateLIS();
    demonstrateSubsetSum();
    
    printf("All dynamic programming examples demonstrated!\n");
    return 0;
}
