# Memory Management and Debugging

This file contains comprehensive memory management and debugging examples in C, including memory tracking, leak detection, memory pools, smart pointers, garbage collection, and debugging tools.

## 📚 Memory Management Fundamentals

### 🧠 Memory Concepts
- **Heap Memory**: Dynamic memory allocation at runtime
- **Stack Memory**: Automatic memory management for local variables
- **Memory Leaks**: Allocated memory that is never freed
- **Buffer Overflows**: Writing beyond allocated memory boundaries
- **Dangling Pointers**: Pointers to freed memory locations

### 🎯 Memory Operations
- **Allocation**: Requesting memory from the system
- **Deallocation**: Returning memory to the system
- **Reallocation**: Changing the size of allocated memory
- **Access**: Reading from or writing to memory locations

## 🔍 Memory Tracking System

### Allocation Tracking Structure
```c
typedef struct {
    void* pointer;
    size_t size;
    const char* file;
    int line;
    time_t timestamp;
    int is_freed;
} AllocationInfo;
```

### Debug Memory Block Structure
```c
typedef struct {
    size_t size;
    const char* file;
    int line;
    unsigned int guard_before;
    unsigned int guard_after;
} DebugMemoryBlock;
```

### Debug Memory Allocation
```c
void* debugMalloc(size_t size, const char* file, int line) {
    // Allocate extra space for debug information
    size_t total_size = sizeof(DebugMemoryBlock) + size + sizeof(unsigned int);
    DebugMemoryBlock* block = (DebugMemoryBlock*)malloc(total_size);
    
    if (!block) {
        printf("Memory allocation failed: %zu bytes at %s:%d\n", size, file, line);
        return NULL;
    }
    
    // Set up debug information
    block->size = size;
    block->file = file;
    block->line = line;
    block->guard_before = GUARD_PATTERN;
    
    // Set guard pattern after the data
    unsigned int* guard_after = (unsigned int*)((char*)block + sizeof(DebugMemoryBlock) + size);
    *guard_after = GUARD_PATTERN;
    
    // Track allocation
    void* user_ptr = (char*)block + sizeof(DebugMemoryBlock);
    trackAllocation(user_ptr, size, file, line);
    
    return user_ptr;
}
```

### Debug Memory Deallocation
```c
void debugFree(void* ptr, const char* file, int line) {
    if (!ptr) {
        printf("Attempt to free NULL pointer at %s:%d\n", file, line);
        return;
    }
    
    // Get debug block
    DebugMemoryBlock* block = (DebugMemoryBlock*)((char*)ptr - sizeof(DebugMemoryBlock));
    
    // Check guard patterns
    if (block->guard_before != GUARD_PATTERN) {
        printf("Memory corruption detected before block at %s:%d\n", file, line);
    }
    
    unsigned int* guard_after = (unsigned int*)((char*)block + sizeof(DebugMemoryBlock) + block->size);
    if (*guard_after != GUARD_PATTERN) {
        printf("Memory corruption detected after block at %s:%d\n", file, line);
    }
    
    // Mark as freed
    trackDeallocation(ptr);
    
    // Fill freed memory with pattern
    memset(ptr, FREED_PATTERN & 0xFF, block->size);
    
    free(block);
}
```

### Debug Macros
```c
#define MALLOC(size) debugMalloc(size, __FILE__, __LINE__)
#define FREE(ptr) debugFree(ptr, __FILE__, __LINE__)
#define REALLOC(ptr, size) debugRealloc(ptr, size, __FILE__, __LINE__)
```

## 🔍 Memory Leak Detection

### Leak Detection Function
```c
void checkMemoryLeaks() {
    EnterCriticalSection(&memory_cs);
    
    printf("\n=== MEMORY LEAK DETECTION ===\n");
    printf("Total allocations: %d\n", allocation_count);
    
    int leak_count = 0;
    size_t total_leaked = 0;
    
    for (int i = 0; i < allocation_count; i++) {
        if (!allocations[i].is_freed) {
            leak_count++;
            total_leaked += allocations[i].size;
            
            printf("LEAK: %zu bytes allocated at %s:%d\n", 
                   allocations[i].size, allocations[i].file, allocations[i].line);
        }
    }
    
    if (leak_count == 0) {
        printf("No memory leaks detected!\n");
    } else {
        printf("Total leaks: %d allocations, %zu bytes\n", leak_count, total_leaked);
    }
    
    LeaveCriticalSection(&memory_cs);
}
```

### Memory Statistics
```c
void printMemoryStatistics() {
    EnterCriticalSection(&memory_cs);
    
    printf("\n=== MEMORY STATISTICS ===\n");
    printf("Total allocations: %d\n", allocation_count);
    
    size_t total_allocated = 0;
    int active_allocations = 0;
    
    for (int i = 0; i < allocation_count; i++) {
        if (!allocations[i].is_freed) {
            active_allocations++;
            total_allocated += allocations[i].size;
        }
    }
    
    printf("Active allocations: %d\n", active_allocations);
    printf("Total allocated memory: %zu bytes\n", total_allocated);
    printf("Average allocation size: %.2f bytes\n", 
           active_allocations > 0 ? (double)total_allocated / active_allocations : 0.0);
    
    LeaveCriticalSection(&memory_cs);
}
```

## 🏊 Memory Pool Implementation

### Memory Pool Structure
```c
typedef struct {
    char pool[MEMORY_POOL_SIZE];
    size_t used;
    size_t total_size;
    int allocation_count;
} MemoryPool;
```

### Pool Allocation
```c
void* poolAlloc(MemoryPool* pool, size_t size) {
    // Align size to 8-byte boundary
    size = (size + 7) & ~7;
    
    if (pool->used + size > pool->total_size) {
        printf("Pool allocation failed: %zu bytes requested, %zu available\n", 
               size, pool->total_size - pool->used);
        return NULL;
    }
    
    void* ptr = pool->pool + pool->used;
    pool->used += size;
    pool->allocation_count++;
    
    return ptr;
}
```

### Pool Statistics
```c
void printPoolStatistics(MemoryPool* pool) {
    printf("Pool Statistics:\n");
    printf("  Total size: %zu bytes\n", pool->total_size);
    printf("  Used: %zu bytes\n", pool->used);
    printf("  Available: %zu bytes\n", pool->total_size - pool->used);
    printf("  Utilization: %.2f%%\n", 
           (double)pool->used / pool->total_size * 100.0);
    printf("  Allocation count: %d\n", pool->allocation_count);
}
```

**Memory Pool Benefits**:
- **Performance**: Fast allocation from pre-allocated memory
- **Fragmentation**: Reduces memory fragmentation
- **Predictability**: Known memory usage patterns
- **Bulk Deallocation**: Reset entire pool at once

## 📚 Stack-Based Memory Allocation

### Memory Stack Structure
```c
typedef struct {
    char* buffer;
    size_t size;
    size_t top;
    int is_valid;
} MemoryStack;
```

### Stack Allocation
```c
void* stackAlloc(MemoryStack* stack, size_t size) {
    if (!stack->is_valid) {
        printf("Stack is not valid\n");
        return NULL;
    }
    
    // Align size to 8-byte boundary
    size = (size + 7) & ~7;
    
    if (stack->top + size > stack->size) {
        printf("Stack allocation failed: %zu bytes requested, %zu available\n", 
               size, stack->size - stack->top);
        return NULL;
    }
    
    void* ptr = stack->buffer + stack->top;
    stack->top += size;
    
    return ptr;
}
```

### Stack Marker System
```c
size_t stackMark(MemoryStack* stack) {
    return stack->top;
}

void stackFreeToMarker(MemoryStack* stack, size_t marker) {
    if (marker <= stack->top && marker <= stack->size) {
        stack->top = marker;
    }
}
```

**Stack Allocation Benefits**:
- **Speed**: O(1) allocation and deallocation
- **Bulk Cleanup**: Free multiple allocations at once
- **LIFO Order**: Last allocated, first freed
- **No Fragmentation**: Sequential allocation pattern

## 🧠 Smart Pointer Implementation

### Smart Pointer Structure
```c
typedef struct {
    void* ptr;
    int* ref_count;
    void (*destructor)(void*);
} SmartPointer;
```

### Smart Pointer Creation
```c
SmartPointer createSmartPointer(void* ptr, void (*destructor)(void*)) {
    SmartPointer sp;
    sp.ptr = ptr;
    sp.ref_count = malloc(sizeof(int));
    *sp.ref_count = 1;
    sp.destructor = destructor;
    return sp;
}
```

### Reference Counting
```c
SmartPointer copySmartPointer(SmartPointer* sp) {
    (*sp->ref_count)++;
    return *sp;
}

void releaseSmartPointer(SmartPointer* sp) {
    if (sp->ptr && sp->ref_count) {
        (*sp->ref_count)--;
        if (*sp->ref_count == 0) {
            if (sp->destructor) {
                sp->destructor(sp->ptr);
            }
            free(sp->ref_count);
        }
        sp->ptr = NULL;
        sp->ref_count = NULL;
    }
}
```

**Smart Pointer Benefits**:
- **Automatic Cleanup**: Memory freed when reference count reaches zero
- **Shared Ownership**: Multiple pointers can reference same object
- **Exception Safety**: Automatic cleanup on errors
- **Resource Management**: Works with any resource type

## 🗑️ Garbage Collection

### GC Object Structure
```c
typedef struct GCObject {
    void* data;
    int marked;
    struct GCObject* next;
    void (*finalizer)(void*);
} GCObject;
```

### Mark-and-Sweep GC
```c
void markObject(void* ptr) {
    GCObject* obj = (GCObject*)((char*)ptr - sizeof(GCObject));
    if (!obj->marked) {
        obj->marked = 1;
        // In a real GC, you would mark referenced objects here
    }
}

void sweepGC() {
    GCObject** current = &gc_heap.objects;
    while (*current) {
        if (!(*current)->marked) {
            GCObject* to_free = *current;
            *current = to_free->next;
            
            if (to_free->finalizer) {
                to_free->finalizer(to_free->data);
            }
            
            free(to_free);
            gc_heap.total_objects--;
        } else {
            (*current)->marked = 0;
            current = &(*current)->next;
        }
    }
}
```

### GC Execution
```c
void runGC() {
    printf("Running garbage collection...\n");
    
    // Mark phase (simplified)
    GCObject* obj = gc_heap.objects;
    while (obj) {
        // In a real implementation, you'd mark reachable objects
        obj = obj->next;
    }
    
    // Sweep phase
    sweepGC();
    
    printf("GC completed. Objects: %zu, Memory: %zu bytes\n", 
           gc_heap.total_objects, gc_heap.total_memory);
}
```

**Garbage Collection Benefits**:
- **Automatic Memory Management**: No manual free calls needed
- **Memory Safety**: Reduces dangling pointer issues
- **Convenience**: Easier programming model
- **Performance**: Can optimize memory usage patterns

## 🔒 Buffer Overflow Detection

### Safe String Operations
```c
void safeStringCopy(char* dest, const char* src, size_t dest_size) {
    if (dest && src && dest_size > 0) {
        strncpy(dest, src, dest_size - 1);
        dest[dest_size - 1] = '\0';
    }
}

void safeStringConcat(char* dest, const char* src, size_t dest_size) {
    if (dest && src && dest_size > 0) {
        size_t dest_len = strlen(dest);
        if (dest_len < dest_size - 1) {
            strncat(dest, src, dest_size - dest_len - 1);
        }
    }
}
```

### Overflow Detection
```c
int detectBufferOverflow(const char* buffer, size_t buffer_size, size_t used_size) {
    // Check for common overflow patterns
    if (used_size >= buffer_size) {
        return 1; // Overflow detected
    }
    
    // Check for null terminator in strings
    if (buffer_size > 0 && buffer[used_size] != '\0') {
        return 1; // Possible overflow
    }
    
    return 0; // No overflow detected
}
```

## 🛠️ Debugging Tools

### Memory Hex Dump
```c
void printMemoryHexDump(const void* ptr, size_t size) {
    const unsigned char* bytes = (const unsigned char*)ptr;
    
    for (size_t i = 0; i < size; i += 16) {
        printf("%08zx: ", i);
        
        // Print hex bytes
        for (size_t j = 0; j < 16; j++) {
            if (i + j < size) {
                printf("%02x ", bytes[i + j]);
            } else {
                printf("   ");
            }
            
            if (j == 7) printf(" ");
        }
        
        printf(" ");
        
        // Print ASCII representation
        for (size_t j = 0; j < 16 && i + j < size; j++) {
            char c = bytes[i + j];
            printf("%c", (c >= 32 && c <= 126) ? c : '.');
        }
        
        printf("\n");
    }
}
```

### Memory Integrity Validation
```c
int validateMemoryIntegrity(void* ptr, size_t size) {
    if (!ptr || size == 0) {
        return 0;
    }
    
    // Check if pointer is readable
    __try {
        volatile char test = *((volatile char*)ptr);
        (void)test; // Suppress unused variable warning
    } __except(EXCEPTION_EXECUTE_HANDLER) {
        return 0; // Access violation
    }
    
    // Check for common corruption patterns
    unsigned int* uint_ptr = (unsigned int*)ptr;
    for (size_t i = 0; i < size / sizeof(unsigned int); i++) {
        if (uint_ptr[i] == GUARD_PATTERN || uint_ptr[i] == FREED_PATTERN) {
            return 0; // Corruption detected
        }
    }
    
    return 1; // Memory appears valid
}
```

### Boundary Checking
```c
void checkMemoryBoundaries(void* ptr, size_t size) {
    // Check before boundary
    char* before = (char*)ptr - 16;
    printf("Memory before boundary:\n");
    printMemoryHexDump(before, 16);
    
    // Check after boundary
    char* after = (char*)ptr + size;
    printf("Memory after boundary:\n");
    printMemoryHexDump(after, 16);
}
```

## 📊 Memory Profiling

### Profile Structure
```c
typedef struct {
    size_t total_allocated;
    size_t total_freed;
    int allocation_count;
    int free_count;
    size_t peak_usage;
    size_t current_usage;
    time_t start_time;
} MemoryProfile;
```

### Profile Recording
```c
void recordAllocation(size_t size) {
    memory_profile.total_allocated += size;
    memory_profile.allocation_count++;
    memory_profile.current_usage += size;
    
    if (memory_profile.current_usage > memory_profile.peak_usage) {
        memory_profile.peak_usage = memory_profile.current_usage;
    }
}

void recordDeallocation(size_t size) {
    memory_profile.total_freed += size;
    memory_profile.free_count++;
    memory_profile.current_usage -= size;
}
```

### Profile Analysis
```c
void printMemoryProfile() {
    time_t elapsed = time(NULL) - memory_profile.start_time;
    
    printf("\n=== MEMORY PROFILE ===\n");
    printf("Profiling duration: %ld seconds\n", elapsed);
    printf("Total allocated: %zu bytes\n", memory_profile.total_allocated);
    printf("Total freed: %zu bytes\n", memory_profile.total_freed);
    printf("Current usage: %zu bytes\n", memory_profile.current_usage);
    printf("Peak usage: %zu bytes\n", memory_profile.peak_usage);
    printf("Allocations: %d\n", memory_profile.allocation_count);
    printf("Deallocations: %d\n", memory_profile.free_count);
    printf("Average allocation size: %.2f bytes\n", 
           memory_profile.allocation_count > 0 ? 
           (double)memory_profile.total_allocated / memory_profile.allocation_count : 0.0);
    printf("Allocation rate: %.2f allocs/sec\n", 
           elapsed > 0 ? (double)memory_profile.allocation_count / elapsed : 0.0);
}
```

## ⚡ Performance Analysis

### Memory Allocation Benchmark
```c
void benchmarkMemoryAllocation() {
    printf("\n=== MEMORY ALLOCATION BENCHMARK ===\n");
    
    const int iterations = 10000;
    const size_t alloc_size = 1024;
    
    // Benchmark malloc/free
    {
        clock_t start = clock();
        
        void* ptrs[iterations];
        for (int i = 0; i < iterations; i++) {
            ptrs[i] = malloc(alloc_size);
        }
        
        for (int i = 0; i < iterations; i++) {
            free(ptrs[i]);
        }
        
        clock_t end = clock();
        double time_taken = ((double)(end - start)) / CLOCKS_PER_SEC;
        
        printf("malloc/free: %d iterations in %.4f seconds\n", iterations, time_taken);
        printf("Average per operation: %.6f seconds\n", time_taken / (iterations * 2));
    }
    
    // Benchmark memory pool
    {
        MemoryPool pool;
        initMemoryPool(&pool, iterations * alloc_size);
        
        clock_t start = clock();
        
        void* ptrs[iterations];
        for (int i = 0; i < iterations; i++) {
            ptrs[i] = poolAlloc(&pool, alloc_size);
        }
        
        resetMemoryPool(&pool);
        
        clock_t end = clock();
        double time_taken = ((double)(end - start)) / CLOCKS_PER_SEC;
        
        printf("Memory pool: %d iterations in %.4f seconds\n", iterations, time_taken);
        printf("Average per operation: %.6f seconds\n", time_taken / (iterations * 2));
    }
}
```

### Fragmentation Analysis
```c
void analyzeMemoryFragmentation() {
    printf("\n=== MEMORY FRAGMENTATION ANALYSIS ===\n");
    
    const int num_allocations = 100;
    size_t allocations[num_allocations];
    void* ptrs[num_allocations];
    
    // Allocate random sized blocks
    srand(time(NULL));
    for (int i = 0; i < num_allocations; i++) {
        allocations[i] = (rand() % 1024) + 64; // 64-1087 bytes
        ptrs[i] = malloc(allocations[i]);
    }
    
    // Free every other allocation
    for (int i = 1; i < num_allocations; i += 2) {
        free(ptrs[i]);
        ptrs[i] = NULL;
    }
    
    // Try to allocate larger blocks
    int successful_allocs = 0;
    for (int i = 0; i < 50; i++) {
        void* ptr = malloc(2048); // 2KB blocks
        if (ptr) {
            successful_allocs++;
            free(ptr);
        }
    }
    
    printf("Successfully allocated %d/50 large blocks after fragmentation\n", successful_allocs);
    
    // Clean up remaining allocations
    for (int i = 0; i < num_allocations; i++) {
        if (ptrs[i]) {
            free(ptrs[i]);
        }
    }
}
```

## ⚠️ Common Memory Issues

### 1. Memory Leaks
```c
// Wrong - Forgetting to free
void memoryLeakExample() {
    char* ptr = malloc(100);
    strcpy(ptr, "This will be leaked");
    // Forgot to free(ptr)!
}

// Right - Proper cleanup
void noMemoryLeakExample() {
    char* ptr = malloc(100);
    if (ptr) {
        strcpy(ptr, "This will be freed");
        free(ptr);
    }
}
```

### 2. Double Free
```c
// Wrong - Freeing twice
void doubleFreeExample() {
    char* ptr = malloc(100);
    free(ptr);
    free(ptr); // Undefined behavior!
}

// Right - Set pointer to NULL after free
void noDoubleFreeExample() {
    char* ptr = malloc(100);
    if (ptr) {
        free(ptr);
        ptr = NULL; // Prevent double free
    }
}
```

### 3. Use After Free
```c
// Wrong - Using freed memory
void useAfterFreeExample() {
    char* ptr = malloc(100);
    strcpy(ptr, "Test");
    free(ptr);
    printf("%s\n", ptr); // Undefined behavior!
}

// Right - Don't use freed memory
void noUseAfterFreeExample() {
    char* ptr = malloc(100);
    if (ptr) {
        strcpy(ptr, "Test");
        printf("%s\n", ptr);
        free(ptr);
        ptr = NULL;
    }
}
```

### 4. Buffer Overflow
```c
// Wrong - Writing beyond buffer
void bufferOverflowExample() {
    char buffer[10];
    strcpy(buffer, "This string is too long"); // Overflow!
}

// Right - Use safe functions
void noBufferOverflowExample() {
    char buffer[10];
    safeStringCopy(buffer, "This string is too long", sizeof(buffer));
}
```

### 5. Dangling Pointers
```c
// Wrong - Pointer to local variable
void danglingPointerExample() {
    char* ptr;
    {
        char local_buffer[100];
        strcpy(local_buffer, "Local data");
        ptr = local_buffer; // ptr becomes dangling
    }
    printf("%s\n", ptr); // Undefined behavior!
}

// Right - Use heap allocation
void noDanglingPointerExample() {
    char* ptr = malloc(100);
    if (ptr) {
        strcpy(ptr, "Heap data");
        printf("%s\n", ptr);
        free(ptr);
    }
}
```

## 🔧 Best Practices

### 1. Always Check Return Values
```c
void safeMemoryAllocation() {
    char* ptr = malloc(1000);
    if (!ptr) {
        printf("Memory allocation failed\n");
        return;
    }
    
    // Use ptr
    free(ptr);
}
```

### 2. Initialize Pointers
```c
void initializePointers() {
    char* ptr = NULL; // Always initialize
    // ... later
    if (ptr) {
        free(ptr);
    }
}
```

### 3. Use RAII Pattern
```c
typedef struct {
    char* data;
} ManagedResource;

void initManagedResource(ManagedResource* mr, size_t size) {
    mr->data = malloc(size);
}

void cleanupManagedResource(ManagedResource* mr) {
    if (mr->data) {
        free(mr->data);
        mr->data = NULL;
    }
}

void useManagedResource() {
    ManagedResource mr;
    initManagedResource(&mr, 1000);
    
    // Use mr.data
    
    cleanupManagedResource(&mr); // Always cleanup
}
```

### 4. Use Memory Pools for Performance
```c
void highPerformanceAllocation() {
    MemoryPool pool;
    initMemoryPool(&pool, 1024 * 1024); // 1MB pool
    
    // Fast allocations from pool
    void* ptr1 = poolAlloc(&pool, 100);
    void* ptr2 = poolAlloc(&pool, 200);
    
    // Bulk cleanup
    resetMemoryPool(&pool);
}
```

### 5. Profile Memory Usage
```c
void profileMemoryUsage() {
    beginMemoryProfiling();
    
    // Do memory-intensive work
    for (int i = 0; i < 1000; i++) {
        void* ptr = malloc(i * 10);
        recordAllocation(i * 10);
        // ... use ptr
        free(ptr);
        recordDeallocation(i * 10);
    }
    
    printMemoryProfile();
}
```

## 🔧 Real-World Applications

### 1. Game Engine Memory Management
```c
void gameEngineMemoryManagement() {
    // Use memory pools for game objects
    MemoryPool entity_pool;
    initMemoryPool(&entity_pool, 1024 * 1024);
    
    // Allocate entities from pool
    void* entity = poolAlloc(&entity_pool, sizeof(Entity));
    
    // Reset pool each level
    resetMemoryPool(&entity_pool);
}
```

### 2. Web Server Connection Handling
```c
void handleConnectionWithMemoryPool() {
    MemoryPool request_pool;
    initMemoryPool(&request_pool, 64 * 1024); // 64KB per request
    
    // Allocate request data from pool
    char* buffer = poolAlloc(&request_pool, 4096);
    
    // Process request
    
    // Pool automatically cleaned up
}
```

### 3. Database Query Processing
```c
void processQueryWithSmartPointers() {
    // Use smart pointers for automatic cleanup
    SmartPointer result = createSmartPointer(allocateQueryResult(), freeQueryResult);
    
    // Process result
    processQueryResult(result.ptr);
    
    // Automatically freed when result goes out of scope
    releaseSmartPointer(&result);
}
```

### 4. Image Processing
```c
void processImageWithMemoryPool() {
    // Use stack for temporary allocations
    MemoryStack temp_stack;
    initMemoryStack(&temp_stack, 10 * 1024 * 1024); // 10MB
    
    // Allocate temporary buffers
    void* temp_buffer1 = stackAlloc(&temp_stack, 1024 * 1024);
    void* temp_buffer2 = stackAlloc(&temp_stack, 2 * 1024 * 1024);
    
    // Process image
    
    // All temporary memory automatically freed
    cleanupMemoryStack(&temp_stack);
}
```

## 📚 Cross-Platform Considerations

### Platform-Specific Memory Functions
```c
#ifdef _WIN32
    #include <windows.h>
    void* aligned_alloc(size_t alignment, size_t size) {
        return _aligned_malloc(size, alignment);
    }
    void aligned_free(void* ptr) {
        _aligned_free(ptr);
    }
#else
    #include <stdlib.h>
    void* aligned_alloc(size_t alignment, size_t size) {
        return posix_memalign(NULL, alignment, size);
    }
    void aligned_free(void* ptr) {
        free(ptr);
    }
#endif
```

### Portable Memory Debugging
```c
// Use portable debugging libraries
// - Valgrind (Linux)
// - AddressSanitizer (Clang/GCC)
// - Dr. Memory (Windows)
// - Custom debug allocator

void portableMemoryDebugging() {
#ifdef _WIN32
    // Windows-specific debugging
    _CrtSetDbgFlag(_CRTDBG_ALLOC_MEM_DF | _CRTDBG_LEAK_CHECK_DF);
#else
    // Use external tools like Valgrind
    // valgrind --leak-check=full ./program
#endif
}
```

## 🎓 Learning Path

### 1. Basic Memory Management
- malloc, free, realloc
- Memory leaks and detection
- Basic debugging techniques

### 2. Advanced Memory Management
- Memory pools and stacks
- Smart pointers and reference counting
- Custom allocators

### 3. Memory Debugging
- Buffer overflow detection
- Memory corruption detection
- Profiling and optimization

### 4. Garbage Collection
- Mark-and-sweep algorithms
- Reference counting
- Generational GC

### 5. Performance Optimization
- Memory allocation patterns
- Cache optimization
- Fragmentation reduction

## 📚 Further Reading

### Books
- "C Programming: A Modern Approach" by K. N. King
- "The C Programming Language" by Kernighan and Ritchie
- "Memory as a Programming Concept" by Franta and Muth

### Topics
- Memory management algorithms
- Garbage collection techniques
- Memory debugging tools
- Performance optimization
- Memory safety in C

Memory management and debugging in C provides critical skills for building robust, efficient applications. Master these concepts to create software that handles memory safely and effectively!
