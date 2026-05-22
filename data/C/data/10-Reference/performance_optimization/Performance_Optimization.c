#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <math.h>

// =============================================================================
// PERFORMANCE OPTIMIZATION FUNDAMENTALS
// =============================================================================

#define ARRAY_SIZE 1000000
#define ITERATIONS 1000
#define CACHE_LINE_SIZE 64

// High-resolution timer
typedef struct {
    clock_t start;
    clock_t end;
} Timer;

// Performance metrics
typedef struct {
    double execution_time;
    long long operations_per_second;
    size_t memory_usage;
    int cache_misses;
} PerformanceMetrics;

// =============================================================================
// BENCHMARKING TOOLS
// =============================================================================

// Start timer
void startTimer(Timer* timer) {
    timer->start = clock();
}

// Stop timer and get elapsed time
double stopTimer(Timer* timer) {
    timer->end = clock();
    return ((double)(timer->end - timer->start)) / CLOCKS_PER_SEC;
}

// Simple benchmark function
void benchmarkFunction(const char* name, void (*func)(void*), void* param) {
    Timer timer;
    startTimer(&timer);
    
    func(param);
    
    double elapsed = stopTimer(&timer);
    printf("%s: %.6f seconds\n", name, elapsed);
}

// =============================================================================
// MEMORY OPTIMIZATION
// =============================================================================

// Cache-friendly data structure
typedef struct {
    int data[16]; // Multiple of cache line size
    int count;
} CacheFriendlyArray;

// Memory-aligned structure
typedef struct __attribute__((aligned(CACHE_LINE_SIZE))) {
    int value;
    char padding[CACHE_LINE_SIZE - sizeof(int)];
} AlignedInt;

// Demonstrate cache-friendly vs cache-unfriendly access
void demonstrateCacheOptimization() {
    printf("=== CACHE OPTIMIZATION DEMO ===\n");
    
    // Cache-unfriendly: Random access
    int* random_array = malloc(ARRAY_SIZE * sizeof(int));
    int* indices = malloc(ARRAY_SIZE * sizeof(int));
    
    // Initialize arrays
    for (int i = 0; i < ARRAY_SIZE; i++) {
        random_array[i] = i;
        indices[i] = rand() % ARRAY_SIZE;
    }
    
    Timer timer;
    startTimer(&timer);
    
    // Random access (cache-unfriendly)
    volatile int sum = 0;
    for (int i = 0; i < ITERATIONS; i++) {
        for (int j = 0; j < ARRAY_SIZE; j++) {
            sum += random_array[indices[j]];
        }
    }
    
    double random_time = stopTimer(&timer);
    printf("Random access: %.6f seconds\n", random_time);
    
    // Sequential access (cache-friendly)
    startTimer(&timer);
    
    sum = 0;
    for (int i = 0; i < ITERATIONS; i++) {
        for (int j = 0; j < ARRAY_SIZE; j++) {
            sum += random_array[j];
        }
    }
    
    double sequential_time = stopTimer(&timer);
    printf("Sequential access: %.6f seconds\n", sequential_time);
    printf("Speedup: %.2fx\n", random_time / sequential_time);
    
    free(random_array);
    free(indices);
}

// Memory pool for fast allocation
typedef struct {
    char* pool;
    size_t size;
    size_t used;
} MemoryPool;

void initMemoryPool(MemoryPool* pool, size_t size) {
    pool->pool = malloc(size);
    pool->size = size;
    pool->used = 0;
}

void* poolAlloc(MemoryPool* pool, size_t size) {
    // Align to 8-byte boundary
    size = (size + 7) & ~7;
    
    if (pool->used + size > pool->size) {
        return NULL;
    }
    
    void* ptr = pool->pool + pool->used;
    pool->used += size;
    return ptr;
}

void resetMemoryPool(MemoryPool* pool) {
    pool->used = 0;
}

void cleanupMemoryPool(MemoryPool* pool) {
    free(pool->pool);
    pool->pool = NULL;
}

// Compare malloc vs memory pool
void demonstrateMemoryPoolOptimization() {
    printf("\n=== MEMORY POOL OPTIMIZATION DEMO ===\n");
    
    const int ALLOC_COUNT = 100000;
    const int ALLOC_SIZE = 64;
    
    // Test malloc/free
    Timer timer;
    startTimer(&timer);
    
    void** ptrs = malloc(ALLOC_COUNT * sizeof(void*));
    for (int i = 0; i < ALLOC_COUNT; i++) {
        ptrs[i] = malloc(ALLOC_SIZE);
    }
    
    for (int i = 0; i < ALLOC_COUNT; i++) {
        free(ptrs[i]);
    }
    
    double malloc_time = stopTimer(&timer);
    printf("malloc/free: %.6f seconds\n", malloc_time);
    
    // Test memory pool
    MemoryPool pool;
    initMemoryPool(&pool, ALLOC_COUNT * ALLOC_SIZE);
    
    startTimer(&timer);
    
    for (int i = 0; i < ALLOC_COUNT; i++) {
        ptrs[i] = poolAlloc(&pool, ALLOC_SIZE);
    }
    
    resetMemoryPool(&pool);
    
    double pool_time = stopTimer(&timer);
    printf("Memory pool: %.6f seconds\n", pool_time);
    printf("Speedup: %.2fx\n", malloc_time / pool_time);
    
    free(ptrs);
    cleanupMemoryPool(&pool);
}

// =============================================================================
// ALGORITHM OPTIMIZATION
// =============================================================================

// Naive string search
int naiveStringSearch(const char* text, const char* pattern) {
    int text_len = strlen(text);
    int pattern_len = strlen(pattern);
    
    for (int i = 0; i <= text_len - pattern_len; i++) {
        int j;
        for (j = 0; j < pattern_len; j++) {
            if (text[i + j] != pattern[j]) {
                break;
            }
        }
        if (j == pattern_len) {
            return i;
        }
    }
    
    return -1;
}

// KMP string search (optimized)
void computeLPSArray(const char* pattern, int* lps) {
    int len = strlen(pattern);
    int i = 1, j = 0;
    lps[0] = 0;
    
    while (i < len) {
        if (pattern[i] == pattern[j]) {
            j++;
            lps[i] = j;
            i++;
        } else {
            if (j != 0) {
                j = lps[j - 1];
            } else {
                lps[i] = 0;
                i++;
            }
        }
    }
}

int kmpStringSearch(const char* text, const char* pattern) {
    int text_len = strlen(text);
    int pattern_len = strlen(pattern);
    
    if (pattern_len == 0) return 0;
    if (text_len < pattern_len) return -1;
    
    int* lps = malloc(pattern_len * sizeof(int));
    computeLPSArray(pattern, lps);
    
    int i = 0, j = 0;
    while (i < text_len) {
        if (pattern[j] == text[i]) {
            i++;
            j++;
        }
        
        if (j == pattern_len) {
            free(lps);
            return i - j;
        } else if (i < text_len && pattern[j] != text[i]) {
            if (j != 0) {
                j = lps[j - 1];
            } else {
                i++;
            }
        }
    }
    
    free(lps);
    return -1;
}

// Compare string search algorithms
void demonstrateStringSearchOptimization() {
    printf("\n=== STRING SEARCH OPTIMIZATION DEMO ===\n");
    
    const char* text = "This is a sample text for string searching algorithms demonstration";
    const char* pattern = "searching";
    
    Timer timer;
    
    // Test naive search
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS * 100; i++) {
        naiveStringSearch(text, pattern);
    }
    double naive_time = stopTimer(&timer);
    printf("Naive search: %.6f seconds\n", naive_time);
    
    // Test KMP search
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS * 100; i++) {
        kmpStringSearch(text, pattern);
    }
    double kmp_time = stopTimer(&timer);
    printf("KMP search: %.6f seconds\n", kmp_time);
    printf("Speedup: %.2fx\n", naive_time / kmp_time);
}

// =============================================================================
// LOOP OPTIMIZATION
// =============================================================================

// Unoptimized loop
void unoptimizedLoop(int* array, int size) {
    for (int i = 0; i < size; i++) {
        array[i] = array[i] * 2 + 1;
    }
}

// Optimized loop with loop unrolling
void optimizedLoop(int* array, int size) {
    int i;
    int remaining = size % 4;
    
    // Process 4 elements at a time
    for (i = 0; i < size - remaining; i += 4) {
        array[i] = array[i] * 2 + 1;
        array[i + 1] = array[i + 1] * 2 + 1;
        array[i + 2] = array[i + 2] * 2 + 1;
        array[i + 3] = array[i + 3] * 2 + 1;
    }
    
    // Process remaining elements
    for (; i < size; i++) {
        array[i] = array[i] * 2 + 1;
    }
}

// SIMD-like optimization (using pointer arithmetic)
void simdLikeLoop(int* array, int size) {
    int i;
    for (i = 0; i < size - 3; i += 4) {
        // Process 4 elements with minimal branching
        int a = array[i];
        int b = array[i + 1];
        int c = array[i + 2];
        int d = array[i + 3];
        
        array[i] = a * 2 + 1;
        array[i + 1] = b * 2 + 1;
        array[i + 2] = c * 2 + 1;
        array[i + 3] = d * 2 + 1;
    }
    
    // Handle remaining elements
    for (; i < size; i++) {
        array[i] = array[i] * 2 + 1;
    }
}

// Compare loop optimizations
void demonstrateLoopOptimization() {
    printf("\n=== LOOP OPTIMIZATION DEMO ===\n");
    
    int* array1 = malloc(ARRAY_SIZE * sizeof(int));
    int* array2 = malloc(ARRAY_SIZE * sizeof(int));
    int* array3 = malloc(ARRAY_SIZE * sizeof(int));
    
    // Initialize arrays
    for (int i = 0; i < ARRAY_SIZE; i++) {
        array1[i] = array2[i] = array3[i] = i;
    }
    
    Timer timer;
    
    // Test unoptimized loop
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS; i++) {
        unoptimizedLoop(array1, ARRAY_SIZE);
    }
    double unoptimized_time = stopTimer(&timer);
    printf("Unoptimized loop: %.6f seconds\n", unoptimized_time);
    
    // Test optimized loop
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS; i++) {
        optimizedLoop(array2, ARRAY_SIZE);
    }
    double optimized_time = stopTimer(&timer);
    printf("Optimized loop: %.6f seconds\n", optimized_time);
    printf("Speedup: %.2fx\n", unoptimized_time / optimized_time);
    
    // Test SIMD-like loop
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS; i++) {
        simdLikeLoop(array3, ARRAY_SIZE);
    }
    double simd_time = stopTimer(&timer);
    printf("SIMD-like loop: %.6f seconds\n", simd_time);
    printf("SIMD speedup: %.2fx\n", unoptimized_time / simd_time);
    
    free(array1);
    free(array2);
    free(array3);
}

// =============================================================================
// BRANCH PREDICTION OPTIMIZATION
// =============================================================================

// Branch-heavy code
int branchHeavy(int* array, int size) {
    int count = 0;
    for (int i = 0; i < size; i++) {
        if (array[i] > 0) {
            count++;
        }
    }
    return count;
}

// Branch-optimized code
int branchOptimized(int* array, int size) {
    int count = 0;
    int i = 0;
    
    // Process in chunks to reduce branch misprediction
    for (; i < size - 3; i += 4) {
        int a = array[i] > 0;
        int b = array[i + 1] > 0;
        int c = array[i + 2] > 0;
        int d = array[i + 3] > 0;
        
        count += a + b + c + d;
    }
    
    // Handle remaining elements
    for (; i < size; i++) {
        count += (array[i] > 0);
    }
    
    return count;
}

// Compare branch optimizations
void demonstrateBranchOptimization() {
    printf("\n=== BRANCH OPTIMIZATION DEMO ===\n");
    
    int* array = malloc(ARRAY_SIZE * sizeof(int));
    
    // Create array with mixed positive/negative values
    for (int i = 0; i < ARRAY_SIZE; i++) {
        array[i] = (i % 3 == 0) ? -i : i;
    }
    
    Timer timer;
    
    // Test branch-heavy code
    startTimer(&timer);
    volatile int result1;
    for (int i = 0; i < ITERATIONS; i++) {
        result1 = branchHeavy(array, ARRAY_SIZE);
    }
    double branch_time = stopTimer(&timer);
    printf("Branch-heavy: %.6f seconds\n", branch_time);
    
    // Test branch-optimized code
    startTimer(&timer);
    volatile int result2;
    for (int i = 0; i < ITERATIONS; i++) {
        result2 = branchOptimized(array, ARRAY_SIZE);
    }
    double optimized_time = stopTimer(&timer);
    printf("Branch-optimized: %.6f seconds\n", optimized_time);
    printf("Speedup: %.2fx\n", branch_time / optimized_time);
    
    free(array);
}

// =============================================================================
// DATA STRUCTURE OPTIMIZATION
// =============================================================================

// Linked list node
typedef struct ListNode {
    int data;
    struct ListNode* next;
} ListNode;

// Array-based list (more cache-friendly)
typedef struct {
    int* data;
    int* next; // Index of next element, -1 for end
    int capacity;
    int size;
    int free_list;
} ArrayList;

// Initialize array list
void initArrayList(ArrayList* list, int capacity) {
    list->data = malloc(capacity * sizeof(int));
    list->next = malloc(capacity * sizeof(int));
    list->capacity = capacity;
    list->size = 0;
    list->free_list = 0;
    
    // Initialize free list
    for (int i = 0; i < capacity - 1; i++) {
        list->next[i] = i + 1;
    }
    list->next[capacity - 1] = -1;
}

// Add to array list
int arrayListAdd(ArrayList* list, int value) {
    if (list->free_list == -1) {
        return -1; // Full
    }
    
    int index = list->free_list;
    list->free_list = list->next[index];
    
    list->data[index] = value;
    list->next[index] = -1; // End of list
    list->size++;
    
    return index;
}

// Compare linked list vs array list
void demonstrateDataStructureOptimization() {
    printf("\n=== DATA STRUCTURE OPTIMIZATION DEMO ===\n");
    
    const int LIST_SIZE = 10000;
    
    // Create linked list
    ListNode* head = NULL;
    ListNode* tail = NULL;
    
    Timer timer;
    startTimer(&timer);
    
    // Build linked list
    for (int i = 0; i < LIST_SIZE; i++) {
        ListNode* node = malloc(sizeof(ListNode));
        node->data = i;
        node->next = NULL;
        
        if (head == NULL) {
            head = tail = node;
        } else {
            tail->next = node;
            tail = node;
        }
    }
    
    // Traverse linked list
    volatile int sum = 0;
    ListNode* current = head;
    while (current != NULL) {
        sum += current->data;
        current = current->next;
    }
    
    double linked_time = stopTimer(&timer);
    printf("Linked list: %.6f seconds\n", linked_time);
    
    // Clean up linked list
    current = head;
    while (current != NULL) {
        ListNode* next = current->next;
        free(current);
        current = next;
    }
    
    // Test array list
    ArrayList array_list;
    initArrayList(&array_list, LIST_SIZE);
    
    startTimer(&timer);
    
    // Build array list
    for (int i = 0; i < LIST_SIZE; i++) {
        arrayListAdd(&array_list, i);
    }
    
    // Traverse array list
    sum = 0;
    for (int i = 0; i < LIST_SIZE; i++) {
        sum += array_list.data[i];
    }
    
    double array_time = stopTimer(&timer);
    printf("Array list: %.6f seconds\n", array_time);
    printf("Speedup: %.2fx\n", linked_time / array_time);
    
    free(array_list.data);
    free(array_list.next);
}

// =============================================================================
// MATH OPTIMIZATION
// =============================================================================

// Standard sqrt
double standardSqrt(double x) {
    return sqrt(x);
}

// Fast sqrt approximation (Quake algorithm)
double fastSqrt(double x) {
    if (x <= 0) return 0;
    
    // Bit-level hack for initial approximation
    union {
        double d;
        int i[2];
    } conv;
    
    conv.d = x;
    conv.i[1] = 0x5fe3eb9c - (conv.i[1] >> 1);
    conv.d = conv.d * 1.5;
    
    // Newton-Raphson iteration
    conv.d = conv.d * (1.5 - (x * 0.5 * conv.d * conv.d));
    
    return conv.d;
}

// Compare sqrt implementations
void demonstrateMathOptimization() {
    printf("\n=== MATH OPTIMIZATION DEMO ===\n");
    
    const int TEST_COUNT = 1000000;
    double* values = malloc(TEST_COUNT * sizeof(double));
    
    // Generate test values
    for (int i = 0; i < TEST_COUNT; i++) {
        values[i] = (double)i + 1.0;
    }
    
    Timer timer;
    volatile double result;
    
    // Test standard sqrt
    startTimer(&timer);
    for (int i = 0; i < TEST_COUNT; i++) {
        result = standardSqrt(values[i]);
    }
    double standard_time = stopTimer(&timer);
    printf("Standard sqrt: %.6f seconds\n", standard_time);
    
    // Test fast sqrt
    startTimer(&timer);
    for (int i = 0; i < TEST_COUNT; i++) {
        result = fastSqrt(values[i]);
    }
    double fast_time = stopTimer(&timer);
    printf("Fast sqrt: %.6f seconds\n", fast_time);
    printf("Speedup: %.2fx\n", standard_time / fast_time);
    
    // Check accuracy
    double error = 0;
    for (int i = 0; i < 100; i++) {
        double standard = standardSqrt(values[i]);
        double fast = fastSqrt(values[i]);
        error += fabs(standard - fast) / standard;
    }
    printf("Average relative error: %.6f%%\n", error / 100 * 100);
    
    free(values);
}

// =============================================================================
// COMPILER OPTIMIZATIONS
// =============================================================================

// Function with optimization hints
static inline int __attribute__((always_inline)) fastMax(int a, int b) {
    return a > b ? a : b;
}

// Restrict pointer for better optimization
void arraySum(const int* __restrict__ array, int size, int* __restrict__ result) {
    int sum = 0;
    for (int i = 0; i < size; i++) {
        sum += array[i];
    }
    *result = sum;
}

// Demonstrate compiler optimizations
void demonstrateCompilerOptimizations() {
    printf("\n=== COMPILER OPTIMIZATION DEMO ===\n");
    
    const int SIZE = 100000;
    int* array = malloc(SIZE * sizeof(int));
    
    // Initialize array
    for (int i = 0; i < SIZE; i++) {
        array[i] = i;
    }
    
    Timer timer;
    volatile int result;
    
    // Test with restrict pointers
    startTimer(&timer);
    for (int i = 0; i < ITERATIONS; i++) {
        arraySum(array, SIZE, &result);
    }
    double restrict_time = stopTimer(&timer);
    printf("With restrict: %.6f seconds\n", restrict_time);
    
    // Test inline function
    startTimer(&timer);
    int max_val = 0;
    for (int i = 0; i < SIZE; i++) {
        max_val = fastMax(max_val, array[i]);
    }
    double inline_time = stopTimer(&timer);
    printf("Inline function: %.6f seconds\n", inline_time);
    
    free(array);
}

// =============================================================================
// PARALLEL OPTIMIZATION (SIMULATION)
// =============================================================================

// Simulated parallel processing
void simulateParallelProcessing() {
    printf("\n=== PARALLEL PROCESSING DEMO ===\n");
    
    const int SIZE = 1000000;
    int* array = malloc(SIZE * sizeof(int));
    
    // Initialize array
    for (int i = 0; i < SIZE; i++) {
        array[i] = i;
    }
    
    Timer timer;
    volatile long long sum = 0;
    
    // Sequential processing
    startTimer(&timer);
    for (int i = 0; i < SIZE; i++) {
        sum += array[i] * array[i];
    }
    double sequential_time = stopTimer(&timer);
    printf("Sequential: %.6f seconds\n", sequential_time);
    
    // Simulated parallel processing (divide and conquer)
    startTimer(&timer);
    
    const int CHUNKS = 4;
    const int CHUNK_SIZE = SIZE / CHUNKS;
    long long partial_sums[CHUNKS] = {0};
    
    // Process chunks in parallel (simulated)
    for (int chunk = 0; chunk < CHUNKS; chunk++) {
        int start = chunk * CHUNK_SIZE;
        int end = (chunk == CHUNKS - 1) ? SIZE : start + CHUNK_SIZE;
        
        for (int i = start; i < end; i++) {
            partial_sums[chunk] += array[i] * array[i];
        }
    }
    
    // Combine results
    sum = 0;
    for (int i = 0; i < CHUNKS; i++) {
        sum += partial_sums[i];
    }
    
    double parallel_time = stopTimer(&timer);
    printf("Simulated parallel: %.6f seconds\n", parallel_time);
    printf("Theoretical speedup: %.2fx\n", sequential_time / parallel_time);
    
    free(array);
}

// =============================================================================
// MEMORY ACCESS PATTERNS
// =============================================================================

// Row-major vs column-major access
void demonstrateMemoryAccessPatterns() {
    printf("\n=== MEMORY ACCESS PATTERNS DEMO ===\n");
    
    const int ROWS = 1000;
    const int COLS = 1000;
    int** matrix = malloc(ROWS * sizeof(int*));
    
    // Allocate matrix
    for (int i = 0; i < ROWS; i++) {
        matrix[i] = malloc(COLS * sizeof(int));
        for (int j = 0; j < COLS; j++) {
            matrix[i][j] = i * COLS + j;
        }
    }
    
    Timer timer;
    volatile int sum = 0;
    
    // Row-major access (cache-friendly)
    startTimer(&timer);
    for (int i = 0; i < ROWS; i++) {
        for (int j = 0; j < COLS; j++) {
            sum += matrix[i][j];
        }
    }
    double row_major_time = stopTimer(&timer);
    printf("Row-major access: %.6f seconds\n", row_major_time);
    
    // Column-major access (cache-unfriendly)
    startTimer(&timer);
    for (int j = 0; j < COLS; j++) {
        for (int i = 0; i < ROWS; i++) {
            sum += matrix[i][j];
        }
    }
    double col_major_time = stopTimer(&timer);
    printf("Column-major access: %.6f seconds\n", col_major_time);
    printf("Speedup: %.2fx\n", col_major_time / row_major_time);
    
    // Clean up
    for (int i = 0; i < ROWS; i++) {
        free(matrix[i]);
    }
    free(matrix);
}

// =============================================================================
// PERFORMANCE PROFILING
// =============================================================================

// Performance profiler structure
typedef struct {
    const char* function_name;
    double total_time;
    int call_count;
    double min_time;
    double max_time;
} ProfileEntry;

ProfileEntry profiles[100];
int profile_count = 0;

// Profile function entry
void profileEnter(const char* name) {
    // Find or create profile entry
    int index = -1;
    for (int i = 0; i < profile_count; i++) {
        if (strcmp(profiles[i].function_name, name) == 0) {
            index = i;
            break;
        }
    }
    
    if (index == -1 && profile_count < 100) {
        index = profile_count++;
        profiles[index].function_name = name;
        profiles[index].total_time = 0;
        profiles[index].call_count = 0;
        profiles[index].min_time = INFINITY;
        profiles[index].max_time = 0;
    }
    
    if (index != -1) {
        profiles[index].call_count++;
    }
}

// Profile function exit
void profileExit(const char* name, double elapsed_time) {
    for (int i = 0; i < profile_count; i++) {
        if (strcmp(profiles[i].function_name, name) == 0) {
            profiles[i].total_time += elapsed_time;
            if (elapsed_time < profiles[i].min_time) {
                profiles[i].min_time = elapsed_time;
            }
            if (elapsed_time > profiles[i].max_time) {
                profiles[i].max_time = elapsed_time;
            }
            break;
        }
    }
}

// Print profile results
void printProfileResults() {
    printf("\n=== PERFORMANCE PROFILE ===\n");
    printf("Function            Calls    Total(s)   Avg(s)     Min(s)     Max(s)\n");
    printf("------------------------------------------------------------\n");
    
    for (int i = 0; i < profile_count; i++) {
        double avg_time = profiles[i].total_time / profiles[i].call_count;
        printf("%-20s %-8d %-10.6f %-10.6f %-10.6f %-10.6f\n",
               profiles[i].function_name,
               profiles[i].call_count,
               profiles[i].total_time,
               avg_time,
               profiles[i].min_time,
               profiles[i].max_time);
    }
}

// Profiled functions
void profiledFunction1() {
    profileEnter("function1");
    
    // Simulate work
    volatile int sum = 0;
    for (int i = 0; i < 100000; i++) {
        sum += i;
    }
    
    Timer timer;
    startTimer(&timer);
    // Do some work
    for (int i = 0; i < 50000; i++) {
        sum += i * 2;
    }
    double elapsed = stopTimer(&timer);
    
    profileExit("function1", elapsed);
}

void profiledFunction2() {
    profileEnter("function2");
    
    Timer timer;
    startTimer(&timer);
    
    // Simulate different work
    volatile double product = 1.0;
    for (int i = 1; i <= 1000; i++) {
        product *= i;
    }
    
    double elapsed = stopTimer(&timer);
    profileExit("function2", elapsed);
}

void demonstratePerformanceProfiling() {
    printf("\n=== PERFORMANCE PROFILING DEMO ===\n");
    
    // Run profiled functions
    for (int i = 0; i < 10; i++) {
        profiledFunction1();
        profiledFunction2();
    }
    
    printProfileResults();
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Performance Optimization Examples\n");
    printf("=================================\n\n");
    
    // Run all optimization demonstrations
    demonstrateCacheOptimization();
    demonstrateMemoryPoolOptimization();
    demonstrateStringSearchOptimization();
    demonstrateLoopOptimization();
    demonstrateBranchOptimization();
    demonstrateDataStructureOptimization();
    demonstrateMathOptimization();
    demonstrateCompilerOptimizations();
    simulateParallelProcessing();
    demonstrateMemoryAccessPatterns();
    demonstratePerformanceProfiling();
    
    printf("\nAll performance optimization examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Cache-friendly data structures significantly improve performance\n");
    printf("- Memory pools are much faster than malloc/free for many small allocations\n");
    printf("- Algorithm choice matters more than micro-optimizations\n");
    printf("- Loop unrolling and SIMD-like optimizations can provide speedups\n");
    printf("- Branch prediction optimization reduces pipeline stalls\n");
    printf("- Memory access patterns have huge impact on performance\n");
    printf("- Profiling helps identify actual bottlenecks\n");
    
    return 0;
}
