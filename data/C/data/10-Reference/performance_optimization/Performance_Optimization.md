# Performance Optimization

This file contains comprehensive performance optimization examples in C, including cache optimization, memory management, algorithm optimization, loop optimization, branch prediction, and profiling techniques.

## 📚 Performance Optimization Fundamentals

### ⚡ Performance Concepts
- **Cache Optimization**: Aligning data access patterns with CPU cache
- **Memory Management**: Efficient allocation and deallocation strategies
- **Algorithm Optimization**: Choosing optimal algorithms for specific problems
- **Loop Optimization**: Reducing loop overhead and improving iteration
- **Branch Prediction**: Minimizing branch mispredictions

### 🎯 Optimization Levels
- **Algorithm Level**: Choosing better algorithms
- **Data Structure Level**: Using cache-friendly structures
- **Compiler Level**: Leveraging compiler optimizations
- **Hardware Level**: Utilizing CPU features effectively

## 🧠 Cache Optimization

### Cache-Friendly Data Structures
```c
// Cache-friendly array structure
typedef struct {
    int data[16]; // Multiple of cache line size
    int count;
} CacheFriendlyArray;

// Memory-aligned structure
typedef struct __attribute__((aligned(CACHE_LINE_SIZE))) {
    int value;
    char padding[CACHE_LINE_SIZE - sizeof(int)];
} AlignedInt;
```

### Cache Access Patterns
```c
void demonstrateCacheOptimization() {
    // Cache-unfriendly: Random access
    int* random_array = malloc(ARRAY_SIZE * sizeof(int));
    int* indices = malloc(ARRAY_SIZE * sizeof(int));
    
    // Initialize with random indices
    for (int i = 0; i < ARRAY_SIZE; i++) {
        indices[i] = rand() % ARRAY_SIZE;
    }
    
    // Random access (poor cache performance)
    volatile int sum = 0;
    for (int i = 0; i < ITERATIONS; i++) {
        for (int j = 0; j < ARRAY_SIZE; j++) {
            sum += random_array[indices[j]];
        }
    }
    
    // Sequential access (good cache performance)
    sum = 0;
    for (int i = 0; i < ITERATIONS; i++) {
        for (int j = 0; j < ARRAY_SIZE; j++) {
            sum += random_array[j];
        }
    }
}
```

### Memory Pool Implementation
```c
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
```

**Cache Optimization Benefits**:
- **Reduced Misses**: Fewer cache misses improve performance
- **Spatial Locality**: Grouping related data together
- **Temporal Locality**: Reusing recently accessed data
- **Memory Bandwidth**: Better utilization of memory bandwidth

## 💾 Memory Optimization

### Memory Pool vs Malloc
```c
void compareMemoryAllocation() {
    const int ALLOC_COUNT = 100000;
    const int ALLOC_SIZE = 64;
    
    // Test malloc/free
    void** ptrs = malloc(ALLOC_COUNT * sizeof(void*));
    for (int i = 0; i < ALLOC_COUNT; i++) {
        ptrs[i] = malloc(ALLOC_SIZE);
    }
    
    for (int i = 0; i < ALLOC_COUNT; i++) {
        free(ptrs[i]);
    }
    
    // Test memory pool
    MemoryPool pool;
    initMemoryPool(&pool, ALLOC_COUNT * ALLOC_SIZE);
    
    for (int i = 0; i < ALLOC_COUNT; i++) {
        ptrs[i] = poolAlloc(&pool, ALLOC_SIZE);
    }
    
    resetMemoryPool(&pool);
}
```

### Memory Alignment
```c
// Aligned structure for better performance
typedef struct __attribute__((aligned(64))) {
    int data[16]; // Fits in one cache line
} AlignedData;

// Use restrict keyword for better optimization
void arraySum(const int* __restrict__ array, int size, int* __restrict__ result) {
    int sum = 0;
    for (int i = 0; i < size; i++) {
        sum += array[i];
    }
    *result = sum;
}
```

**Memory Optimization Benefits**:
- **Allocation Speed**: Memory pools are faster than malloc
- **Fragmentation**: Reduces memory fragmentation
- **Cache Performance**: Better cache utilization
- **Predictability**: More predictable memory usage

## 🔍 Algorithm Optimization

### String Search Optimization
```c
// Naive string search (O(n*m))
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

// KMP string search (O(n+m))
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
```

### LPS Array Computation
```c
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
```

**Algorithm Optimization Benefits**:
- **Time Complexity**: Better asymptotic performance
- **Space Complexity**: Reduced memory usage
- **Predictability**: More predictable performance
- **Scalability**: Better performance with large inputs

## 🔄 Loop Optimization

### Loop Unrolling
```c
// Unoptimized loop
void unoptimizedLoop(int* array, int size) {
    for (int i = 0; i < size; i++) {
        array[i] = array[i] * 2 + 1;
    }
}

// Optimized loop with unrolling
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
```

### SIMD-like Processing
```c
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
```

### Loop Invariant Code Motion
```c
// Before optimization
void beforeOptimization(int* array, int size, int multiplier) {
    for (int i = 0; i < size; i++) {
        array[i] = array[i] * multiplier + (multiplier * multiplier);
    }
}

// After optimization
void afterOptimization(int* array, int size, int multiplier) {
    int multiplier_squared = multiplier * multiplier; // Move out of loop
    
    for (int i = 0; i < size; i++) {
        array[i] = array[i] * multiplier + multiplier_squared;
    }
}
```

**Loop Optimization Benefits**:
- **Reduced Overhead**: Fewer loop iterations
- **Better Pipelining**: Improved CPU pipeline utilization
- **Branch Reduction**: Fewer conditional branches
- **Cache Utilization**: Better cache line usage

## 🔀 Branch Optimization

### Branch Reduction
```c
// Branch-heavy code
int branchHeavy(int* array, int size) {
    int count = 0;
    for (int i = 0; i < size; i++) {
        if (array[i] > 0) {  // Branch inside loop
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
        
        count += a + b + c + d;  // No branches
    }
    
    // Handle remaining elements
    for (; i < size; i++) {
        count += (array[i] > 0);
    }
    
    return count;
}
```

### Branchless Programming
```c
// Branchless absolute value
int branchlessAbs(int x) {
    int mask = x >> (sizeof(int) * 8 - 1);
    return (x + mask) ^ mask;
}

// Branchless min function
int branchlessMin(int a, int b) {
    return b + ((a - b) & ((a - b) >> (sizeof(int) * 8 - 1)));
}

// Branchless conditional assignment
void branchlessAssign(int* dest, int condition, int true_value, int false_value) {
    *dest = (condition * true_value) + ((1 - condition) * false_value);
}
```

**Branch Optimization Benefits**:
- **Pipeline Efficiency**: Fewer pipeline stalls
- **Prediction Accuracy**: Better branch prediction
- **Reduced Latency**: Lower instruction latency
- **Consistent Performance**: More predictable timing

## 📊 Data Structure Optimization

### Cache-Friendly Structures
```c
// Linked list (cache-unfriendly)
typedef struct ListNode {
    int data;
    struct ListNode* next;
} ListNode;

// Array-based list (cache-friendly)
typedef struct {
    int* data;
    int* next; // Index of next element
    int capacity;
    int size;
    int free_list;
} ArrayList;
```

### Structure Packing
```c
// Unoptimized structure (padding waste)
typedef struct {
    char a;      // 1 byte + 3 bytes padding
    int b;       // 4 bytes
    char c;      // 1 byte + 3 bytes padding
    double d;    // 8 bytes
} UnoptimizedStruct; // Total: 20 bytes

// Optimized structure (minimal padding)
typedef struct {
    double d;    // 8 bytes
    int b;       // 4 bytes
    char a;      // 1 byte
    char c;      // 1 byte
    char padding[2]; // 2 bytes padding
} OptimizedStruct; // Total: 16 bytes
```

**Data Structure Benefits**:
- **Cache Performance**: Better cache utilization
- **Memory Usage**: Reduced memory footprint
- **Access Speed**: Faster data access
- **Predictability**: More predictable access patterns

## 🔢 Math Optimization

### Fast Square Root
```c
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
```

### Lookup Tables
```c
// Precomputed sine table
#define SINE_TABLE_SIZE 360
static double sine_table[SINE_TABLE_SIZE];

void initSineTable() {
    for (int i = 0; i < SINE_TABLE_SIZE; i++) {
        sine_table[i] = sin(2.0 * M_PI * i / SINE_TABLE_SIZE);
    }
}

// Fast sine using lookup table
double fastSin(double angle) {
    // Normalize angle to [0, 360)
    angle = fmod(angle, 360.0);
    if (angle < 0) angle += 360.0;
    
    int index = (int)angle;
    int next_index = (index + 1) % SINE_TABLE_SIZE;
    double fraction = angle - index;
    
    // Linear interpolation
    return sine_table[index] + 
           fraction * (sine_table[next_index] - sine_table[index]);
}
```

**Math Optimization Benefits**:
- **Speed**: Faster mathematical operations
- **Accuracy**: Acceptable precision for many applications
- **Precomputation**: Trade memory for speed
- **Approximation**: Good enough approximations

## ⚙️ Compiler Optimizations

### Compiler Hints
```c
// Force inlining
static inline int __attribute__((always_inline)) fastMax(int a, int b) {
    return a > b ? a : b;
}

// Restrict pointers for better optimization
void arraySum(const int* __restrict__ array, int size, int* __restrict__ result) {
    int sum = 0;
    for (int i = 0; i < size; i++) {
        sum += array[i];
    }
    *result = sum;
}

// Likely/unlikely branches
void processValue(int value) {
    if (__builtin_expect(value > 0, 1)) {
        // Likely branch
        handlePositive(value);
    } else {
        // Unlikely branch
        handleNegative(value);
    }
}
```

### Vectorization
```c
// Vectorizable loop
void vectorizableLoop(float* a, float* b, float* result, int size) {
    for (int i = 0; i < size; i++) {
        result[i] = a[i] + b[i];
    }
}

// Compile with: gcc -O3 -march=native -ftree-vectorize
```

**Compiler Optimization Benefits**:
- **Automatic Optimization**: Compiler handles optimizations
- **Vectorization**: SIMD instruction utilization
- **Inline Expansion**: Function call overhead reduction
- **Dead Code Elimination**: Removal of unused code

## 📈 Performance Profiling

### Profiling Framework
```c
typedef struct {
    const char* function_name;
    double total_time;
    int call_count;
    double min_time;
    double max_time;
} ProfileEntry;

ProfileEntry profiles[100];
int profile_count = 0;

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
```

### High-Resolution Timer
```c
typedef struct {
    clock_t start;
    clock_t end;
} Timer;

void startTimer(Timer* timer) {
    timer->start = clock();
}

double stopTimer(Timer* timer) {
    timer->end = clock();
    return ((double)(timer->end - timer->start)) / CLOCKS_PER_SEC;
}
```

**Profiling Benefits**:
- **Bottleneck Identification**: Find actual performance issues
- **Optimization Focus**: Target important optimizations
- **Regression Detection**: Catch performance regressions
- **Measurement Accuracy**: Precise performance metrics

## 🔄 Parallel Processing

### Data Parallelism
```c
void parallelProcessing(int* array, int size) {
    const int CHUNKS = 4;
    const int CHUNK_SIZE = size / CHUNKS;
    long long partial_sums[CHUNKS] = {0};
    
    // Process chunks in parallel
    for (int chunk = 0; chunk < CHUNKS; chunk++) {
        int start = chunk * CHUNK_SIZE;
        int end = (chunk == CHUNKS - 1) ? size : start + CHUNK_SIZE;
        
        for (int i = start; i < end; i++) {
            partial_sums[chunk] += array[i] * array[i];
        }
    }
    
    // Combine results
    long long sum = 0;
    for (int i = 0; i < CHUNKS; i++) {
        sum += partial_sums[i];
    }
}
```

### Memory Access Patterns
```c
// Row-major access (cache-friendly)
void rowMajorAccess(int** matrix, int rows, int cols) {
    volatile int sum = 0;
    for (int i = 0; i < rows; i++) {
        for (int j = 0; j < cols; j++) {
            sum += matrix[i][j];
        }
    }
}

// Column-major access (cache-unfriendly)
void columnMajorAccess(int** matrix, int rows, int cols) {
    volatile int sum = 0;
    for (int j = 0; j < cols; j++) {
        for (int i = 0; i < rows; i++) {
            sum += matrix[i][j];
        }
    }
}
```

**Parallel Processing Benefits**:
- **CPU Utilization**: Better multi-core usage
- **Scalability**: Performance scales with cores
    - **Throughput**: Higher processing throughput
    - **Responsiveness**: Better application responsiveness

## ⚠️ Common Pitfalls

### 1. Premature Optimization
```c
// Wrong: Optimize without profiling
void prematureOptimization() {
    // Complex optimization for rarely used code
    int result = (x & 0xFF) * 0x01010101; // Bit tricks for multiplication
}

// Right: Profile first, then optimize
void smartOptimization() {
    // Simple, readable code
    int result = x * 255;
    
    // Only optimize if profiling shows it's a bottleneck
}
```

### 2. Ignoring Algorithm Complexity
```c
// Wrong: Optimize implementation but use wrong algorithm
void wrongOptimization() {
    // Optimized O(n²) bubble sort
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n - i - 1; j++) {
            if (array[j] > array[j + 1]) {
                swap(&array[j], &array[j + 1]);
            }
        }
    }
}

// Right: Use better algorithm
void rightOptimization() {
    // Use O(n log n) quicksort
    quicksort(array, 0, n - 1);
}
```

### 3. Cache-Unfriendly Data Structures
```c
// Wrong: Poor cache locality
typedef struct {
    int id;
    char name[256];  // Large field
    double value;
} LargeStruct;

LargeStruct* array[1000]; // Poor cache utilization

// Right: Cache-friendly layout
typedef struct {
    int id;
    double value;
} CompactStruct;

char names[1000][256];  // Separate large data
CompactStruct* array[1000]; // Better cache utilization
```

### 4. Micro-optimizations That Don't Matter
```c
// Wrong: Trivial optimizations
void trivialOptimization() {
    // Using bit shifts instead of division
    int result = x >> 2;  // Instead of x / 4
}

// Right: Focus on significant optimizations
void significantOptimization() {
    // Algorithm-level optimization
    // Data structure choice
    // Memory access patterns
}
```

## 🔧 Best Practices

### 1. Profile First
```c
void profileFirstApproach() {
    // 1. Write clear, correct code
    // 2. Profile to find bottlenecks
    // 3. Optimize only the bottlenecks
    // 4. Profile again to verify improvement
}
```

### 2. Measure, Don't Guess
```c
void measurePerformance() {
    Timer timer;
    startTimer(&timer);
    
    // Code to measure
    algorithm();
    
    double elapsed = stopTimer(&timer);
    printf("Time: %.6f seconds\n", elapsed);
}
```

### 3. Consider the Whole System
```c
void holisticOptimization() {
    // Consider:
    // - Memory bandwidth
    // - Cache hierarchy
    // - CPU pipeline
    // - Branch prediction
    // - Parallel execution
}
```

### 4. Maintain Readability
```c
void maintainReadability() {
    // Keep code readable even when optimizing
    // Add comments explaining optimizations
    // Use meaningful variable names
    // Structure code logically
}
```

### 5. Test Optimizations
```c
void testOptimizations() {
    // Verify optimized code produces correct results
    // Test with different input sizes
    // Test on different hardware
    // Measure actual performance improvement
}
```

## 🔧 Real-World Applications

### 1. Game Engine Optimization
```c
void gameEngineOptimization() {
    // Use spatial partitioning for collision detection
    // Optimize rendering with frustum culling
    // Use object pooling for frequent allocations
    // Optimize physics calculations
}
```

### 2. Database Query Optimization
```c
void databaseOptimization() {
    // Use appropriate data structures
    // Optimize memory access patterns
    // Minimize branch mispredictions
    // Use vectorized operations
}
```

### 3. Image Processing
```c
void imageProcessingOptimization() {
    // Process pixels in cache-friendly order
    // Use SIMD instructions
    // Minimize memory allocations
    // Optimize filter algorithms
}
```

### 4. Scientific Computing
```c
void scientificOptimization() {
    // Use optimized numerical algorithms
    // Leverage BLAS libraries
    // Optimize memory layout for matrices
    // Use parallel processing
}
```

## 📚 Further Reading

### Books
- "Computer Systems: A Programmer's Perspective" by Randal Bryant
- "Optimizing C++" by Steve Heller
- "The Art of Computer Programming" by Donald Knuth

### Topics
- CPU architecture and pipelines
- Cache memory hierarchy
- Vector processing and SIMD
- Parallel programming patterns
- Performance analysis tools

Performance optimization in C requires understanding of computer architecture, algorithms, and profiling techniques. Master these concepts to write high-performance C code that makes the most of modern hardware!
