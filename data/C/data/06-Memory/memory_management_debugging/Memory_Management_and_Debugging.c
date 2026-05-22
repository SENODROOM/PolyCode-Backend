#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <windows.h>
#include <time.h>
#include <assert.h>

// =============================================================================
// MEMORY MANAGEMENT FUNDAMENTALS
// =============================================================================

#define MAX_ALLOCATIONS 10000
#define MEMORY_POOL_SIZE 1024 * 1024 // 1MB
#define GUARD_PATTERN 0xDEADBEEF
#define FREED_PATTERN 0xFEEDFACE

// Memory allocation tracking structure
typedef struct {
    void* pointer;
    size_t size;
    const char* file;
    int line;
    time_t timestamp;
    int is_freed;
} AllocationInfo;

// Memory pool structure
typedef struct {
    char pool[MEMORY_POOL_SIZE];
    size_t used;
    size_t total_size;
    int allocation_count;
} MemoryPool;

// Stack-based memory structure
typedef struct {
    char* buffer;
    size_t size;
    size_t top;
    int is_valid;
} MemoryStack;

// Debug memory block header
typedef struct {
    size_t size;
    const char* file;
    int line;
    unsigned int guard_before;
    unsigned int guard_after;
} DebugMemoryBlock;

// Global variables
AllocationInfo allocations[MAX_ALLOCATIONS];
int allocation_count = 0;
MemoryPool global_pool;
MemoryStack debug_stack;
CRITICAL_SECTION memory_cs;

// =============================================================================
// DEBUG MEMORY ALLOCATION
// =============================================================================

// Initialize memory tracking system
void initMemoryTracking() {
    allocation_count = 0;
    memset(allocations, 0, sizeof(allocations));
    
    // Initialize memory pool
    global_pool.used = 0;
    global_pool.total_size = MEMORY_POOL_SIZE;
    global_pool.allocation_count = 0;
    
    // Initialize memory stack
    debug_stack.buffer = malloc(MEMORY_POOL_SIZE);
    debug_stack.size = MEMORY_POOL_SIZE;
    debug_stack.top = 0;
    debug_stack.is_valid = 1;
    
    // Initialize critical section for thread safety
    InitializeCriticalSection(&memory_cs);
}

// Cleanup memory tracking system
void cleanupMemoryTracking() {
    // Check for memory leaks
    checkMemoryLeaks();
    
    // Free stack buffer
    if (debug_stack.buffer) {
        free(debug_stack.buffer);
        debug_stack.buffer = NULL;
    }
    
    // Delete critical section
    DeleteCriticalSection(&memory_cs);
}

// Add allocation to tracking
void trackAllocation(void* ptr, size_t size, const char* file, int line) {
    EnterCriticalSection(&memory_cs);
    
    if (allocation_count < MAX_ALLOCATIONS) {
        allocations[allocation_count].pointer = ptr;
        allocations[allocation_count].size = size;
        allocations[allocation_count].file = file;
        allocations[allocation_count].line = line;
        allocations[allocation_count].timestamp = time(NULL);
        allocations[allocation_count].is_freed = 0;
        allocation_count++;
    }
    
    LeaveCriticalSection(&memory_cs);
}

// Mark allocation as freed
void trackDeallocation(void* ptr) {
    EnterCriticalSection(&memory_cs);
    
    for (int i = 0; i < allocation_count; i++) {
        if (allocations[i].pointer == ptr && !allocations[i].is_freed) {
            allocations[i].is_freed = 1;
            break;
        }
    }
    
    LeaveCriticalSection(&memory_cs);
}

// Debug malloc with tracking
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

// Debug free with tracking
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

// Debug realloc with tracking
void* debugRealloc(void* ptr, size_t new_size, const char* file, int line) {
    if (!ptr) {
        return debugMalloc(new_size, file, line);
    }
    
    if (new_size == 0) {
        debugFree(ptr, file, line);
        return NULL;
    }
    
    // Get old block information
    DebugMemoryBlock* old_block = (DebugMemoryBlock*)((char*)ptr - sizeof(DebugMemoryBlock));
    size_t old_size = old_block->size;
    
    // Allocate new block
    void* new_ptr = debugMalloc(new_size, file, line);
    if (!new_ptr) {
        return NULL;
    }
    
    // Copy old data
    size_t copy_size = old_size < new_size ? old_size : new_size;
    memcpy(new_ptr, ptr, copy_size);
    
    // Free old block
    debugFree(ptr, file, line);
    
    return new_ptr;
}

// Macros for debug memory allocation
#define MALLOC(size) debugMalloc(size, __FILE__, __LINE__)
#define FREE(ptr) debugFree(ptr, __FILE__, __LINE__)
#define REALLOC(ptr, size) debugRealloc(ptr, size, __FILE__, __LINE__)

// =============================================================================
// MEMORY LEAK DETECTION
// =============================================================================

// Check for memory leaks
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

// Print memory usage statistics
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

// =============================================================================
// MEMORY POOL IMPLEMENTATION
// =============================================================================

// Initialize memory pool
void initMemoryPool(MemoryPool* pool, size_t size) {
    pool->used = 0;
    pool->total_size = size;
    pool->allocation_count = 0;
}

// Allocate from memory pool
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

// Reset memory pool
void resetMemoryPool(MemoryPool* pool) {
    pool->used = 0;
    pool->allocation_count = 0;
}

// Get pool usage statistics
void printPoolStatistics(MemoryPool* pool) {
    printf("Pool Statistics:\n");
    printf("  Total size: %zu bytes\n", pool->total_size);
    printf("  Used: %zu bytes\n", pool->used);
    printf("  Available: %zu bytes\n", pool->total_size - pool->used);
    printf("  Utilization: %.2f%%\n", 
           (double)pool->used / pool->total_size * 100.0);
    printf("  Allocation count: %d\n", pool->allocation_count);
}

// =============================================================================
// STACK-BASED MEMORY ALLOCATION
// =============================================================================

// Initialize memory stack
void initMemoryStack(MemoryStack* stack, size_t size) {
    stack->buffer = malloc(size);
    stack->size = size;
    stack->top = 0;
    stack->is_valid = 1;
}

// Allocate from stack
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

// Create stack marker (for bulk deallocation)
size_t stackMark(MemoryStack* stack) {
    return stack->top;
}

// Free to marker
void stackFreeToMarker(MemoryStack* stack, size_t marker) {
    if (marker <= stack->top && marker <= stack->size) {
        stack->top = marker;
    }
}

// Reset stack
void resetMemoryStack(MemoryStack* stack) {
    stack->top = 0;
}

// Cleanup memory stack
void cleanupMemoryStack(MemoryStack* stack) {
    if (stack->buffer) {
        free(stack->buffer);
        stack->buffer = NULL;
    }
    stack->is_valid = 0;
}

// =============================================================================
// SMART POINTER IMPLEMENTATION
// =============================================================================

// Smart pointer structure
typedef struct {
    void* ptr;
    int* ref_count;
    void (*destructor)(void*);
} SmartPointer;

// Create smart pointer
SmartPointer createSmartPointer(void* ptr, void (*destructor)(void*)) {
    SmartPointer sp;
    sp.ptr = ptr;
    sp.ref_count = malloc(sizeof(int));
    *sp.ref_count = 1;
    sp.destructor = destructor;
    return sp;
}

// Copy smart pointer
SmartPointer copySmartPointer(SmartPointer* sp) {
    (*sp->ref_count)++;
    return *sp;
}

// Release smart pointer
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

// Get reference count
int getReferenceCount(SmartPointer* sp) {
    return sp->ref_count ? *sp->ref_count : 0;
}

// =============================================================================
// BUFFER OVERFLOW DETECTION
// =============================================================================

// Safe string copy
void safeStringCopy(char* dest, const char* src, size_t dest_size) {
    if (dest && src && dest_size > 0) {
        strncpy(dest, src, dest_size - 1);
        dest[dest_size - 1] = '\0';
    }
}

// Safe string concatenation
void safeStringConcat(char* dest, const char* src, size_t dest_size) {
    if (dest && src && dest_size > 0) {
        size_t dest_len = strlen(dest);
        if (dest_len < dest_size - 1) {
            strncat(dest, src, dest_size - dest_len - 1);
        }
    }
}

// Buffer overflow detection
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

// =============================================================================
// MEMORY PROFILING
// =============================================================================

// Memory profile structure
typedef struct {
    size_t total_allocated;
    size_t total_freed;
    int allocation_count;
    int free_count;
    size_t peak_usage;
    size_t current_usage;
    time_t start_time;
} MemoryProfile;

MemoryProfile memory_profile = {0};

// Begin memory profiling
void beginMemoryProfiling() {
    memset(&memory_profile, 0, sizeof(MemoryProfile));
    memory_profile.start_time = time(NULL);
}

// Record allocation
void recordAllocation(size_t size) {
    memory_profile.total_allocated += size;
    memory_profile.allocation_count++;
    memory_profile.current_usage += size;
    
    if (memory_profile.current_usage > memory_profile.peak_usage) {
        memory_profile.peak_usage = memory_profile.current_usage;
    }
}

// Record deallocation
void recordDeallocation(size_t size) {
    memory_profile.total_freed += size;
    memory_profile.free_count++;
    memory_profile.current_usage -= size;
}

// Print memory profile
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

// =============================================================================
// GARBAGE COLLECTION (SIMPLE MARK-AND-SWEEP)
// =============================================================================

// GC object structure
typedef struct GCObject {
    void* data;
    int marked;
    struct GCObject* next;
    void (*finalizer)(void*);
} GCObject;

// GC heap structure
typedef struct {
    GCObject* objects;
    size_t total_objects;
    size_t total_memory;
} GCHeap;

GCHeap gc_heap = {NULL, 0, 0};

// Initialize garbage collector
void initGC() {
    gc_heap.objects = NULL;
    gc_heap.total_objects = 0;
    gc_heap.total_memory = 0;
}

// Allocate GC object
void* gcAlloc(size_t size, void (*finalizer)(void*)) {
    GCObject* obj = malloc(sizeof(GCObject) + size);
    if (!obj) {
        return NULL;
    }
    
    obj->data = (char*)obj + sizeof(GCObject);
    obj->marked = 0;
    obj->next = gc_heap.objects;
    obj->finalizer = finalizer;
    
    gc_heap.objects = obj;
    gc_heap.total_objects++;
    gc_heap.total_memory += size;
    
    return obj->data;
}

// Mark object as reachable
void markObject(void* ptr) {
    GCObject* obj = (GCObject*)((char*)ptr - sizeof(GCObject));
    if (!obj->marked) {
        obj->marked = 1;
        // In a real GC, you would mark referenced objects here
    }
}

// Sweep unreachable objects
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

// Run garbage collection
void runGC() {
    printf("Running garbage collection...\n");
    
    // Mark phase (simplified - in real GC, you'd trace from roots)
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

// =============================================================================
// DEBUGGING TOOLS
// =============================================================================

// Print memory hex dump
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

// Validate memory integrity
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

// Memory boundary checker
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

// =============================================================================
// PERFORMANCE ANALYSIS
// =============================================================================

// Memory allocation benchmark
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

// Memory fragmentation analysis
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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateMemoryTracking() {
    printf("=== MEMORY TRACKING DEMO ===\n");
    
    initMemoryTracking();
    
    // Test allocations
    char* ptr1 = MALLOC(100);
    char* ptr2 = MALLOC(200);
    char* ptr3 = MALLOC(300);
    
    // Use memory
    strcpy(ptr1, "Test string 1");
    strcpy(ptr2, "Test string 2");
    strcpy(ptr3, "Test string 3");
    
    printf("Allocated memory:\n");
    printf("ptr1: %s\n", ptr1);
    printf("ptr2: %s\n", ptr2);
    printf("ptr3: %s\n", ptr3);
    
    // Free some memory
    FREE(ptr2);
    
    // Print statistics
    printMemoryStatistics();
    
    // Intentionally leak memory for demonstration
    char* leaked = MALLOC(50);
    strcpy(leaked, "This will be leaked");
    
    // Check for leaks
    checkMemoryLeaks();
    
    // Cleanup
    FREE(ptr1);
    FREE(ptr3);
    
    cleanupMemoryTracking();
}

void demonstrateMemoryPool() {
    printf("=== MEMORY POOL DEMO ===\n");
    
    MemoryPool pool;
    initMemoryPool(&pool, 4096);
    
    // Allocate from pool
    void* ptr1 = poolAlloc(&pool, 100);
    void* ptr2 = poolAlloc(&pool, 200);
    void* ptr3 = poolAlloc(&pool, 300);
    
    printf("Allocated from pool:\n");
    printf("ptr1: %p\n", ptr1);
    printf("ptr2: %p\n", ptr2);
    printf("ptr3: %p\n", ptr3);
    
    printPoolStatistics(&pool);
    
    // Reset pool
    resetMemoryPool(&pool);
    printf("Pool reset\n");
    printPoolStatistics(&pool);
}

void demonstrateMemoryStack() {
    printf("=== MEMORY STACK DEMO ===\n");
    
    MemoryStack stack;
    initMemoryStack(&stack, 4096);
    
    // Allocate from stack
    size_t marker1 = stackMark(&stack);
    char* ptr1 = stackAlloc(&stack, 100);
    strcpy(ptr1, "Stack allocation 1");
    
    size_t marker2 = stackMark(&stack);
    char* ptr2 = stackAlloc(&stack, 200);
    strcpy(ptr2, "Stack allocation 2");
    
    char* ptr3 = stackAlloc(&stack, 300);
    strcpy(ptr3, "Stack allocation 3");
    
    printf("Stack allocations:\n");
    printf("ptr1: %s\n", ptr1);
    printf("ptr2: %s\n", ptr2);
    printf("ptr3: %s\n", ptr3);
    
    // Free to marker2
    stackFreeToMarker(&stack, marker2);
    printf("Freed to marker2\n");
    
    // Reset stack
    resetMemoryStack(&stack);
    printf("Stack reset\n");
    
    cleanupMemoryStack(&stack);
}

void demonstrateSmartPointer() {
    printf("=== SMART POINTER DEMO ===\n");
    
    // Create a managed resource
    char* resource = malloc(100);
    strcpy(resource, "Managed resource");
    
    SmartPointer sp = createSmartPointer(resource, free);
    
    printf("Resource: %s\n", (char*)sp.ptr);
    printf("Reference count: %d\n", getReferenceCount(&sp));
    
    // Copy smart pointer
    SmartPointer sp_copy = copySmartPointer(&sp);
    printf("After copy - Reference count: %d\n", getReferenceCount(&sp));
    
    // Release one copy
    releaseSmartPointer(&sp_copy);
    printf("After release - Reference count: %d\n", getReferenceCount(&sp));
    
    // Release original
    releaseSmartPointer(&sp);
    printf("Resource should be freed now\n");
}

void demonstrateBufferOverflowDetection() {
    printf("=== BUFFER OVERFLOW DETECTION DEMO ===\n");
    
    char buffer[16];
    
    // Safe copy
    safeStringCopy(buffer, "This is safe", sizeof(buffer));
    printf("Safe copy: %s\n", buffer);
    
    // Unsafe copy (would cause overflow)
    char* long_string = "This string is too long for the buffer and would cause overflow";
    safeStringCopy(buffer, long_string, sizeof(buffer));
    printf("Truncated copy: %s\n", buffer);
    
    // Detect overflow
    int overflow = detectBufferOverflow(buffer, sizeof(buffer), strlen(buffer));
    printf("Overflow detected: %s\n", overflow ? "Yes" : "No");
}

void demonstrateMemoryProfiling() {
    printf("=== MEMORY PROFILING DEMO ===\n");
    
    beginMemoryProfiling();
    
    // Simulate some allocations
    for (int i = 0; i < 100; i++) {
        void* ptr = malloc(i * 10);
        recordAllocation(i * 10);
        
        // Free some allocations
        if (i % 3 == 0) {
            free(ptr);
            recordDeallocation(i * 10);
        }
    }
    
    printMemoryProfile();
}

void demonstrateGarbageCollection() {
    printf("=== GARBAGE COLLECTION DEMO ===\n");
    
    initGC();
    
    // Create some GC objects
    void* obj1 = gcAlloc(100, NULL);
    void* obj2 = gcAlloc(200, NULL);
    void* obj3 = gcAlloc(300, NULL);
    
    printf("Created 3 objects\n");
    printf("Total objects: %zu\n", gc_heap.total_objects);
    
    // Mark some as reachable
    markObject(obj1);
    markObject(obj2);
    
    // Run garbage collection
    runGC();
    
    printf("After GC - Total objects: %zu\n", gc_heap.total_objects);
}

void demonstrateDebuggingTools() {
    printf("=== DEBUGGING TOOLS DEMO ===\n");
    
    // Create test data
    char test_data[64];
    for (int i = 0; i < 64; i++) {
        test_data[i] = i;
    }
    
    printf("Hex dump of test data:\n");
    printMemoryHexDump(test_data, 64);
    
    // Validate memory
    int valid = validateMemoryIntegrity(test_data, 64);
    printf("Memory integrity: %s\n", valid ? "Valid" : "Invalid");
    
    // Check boundaries
    printf("Memory boundaries around test_data:\n");
    checkMemoryBoundaries(test_data, 64);
}

void demonstratePerformanceAnalysis() {
    printf("=== PERFORMANCE ANALYSIS DEMO ===\n");
    
    benchmarkMemoryAllocation();
    analyzeMemoryFragmentation();
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Memory Management and Debugging\n");
    printf("===============================\n\n");
    
    // Run all demonstrations
    demonstrateMemoryTracking();
    demonstrateMemoryPool();
    demonstrateMemoryStack();
    demonstrateSmartPointer();
    demonstrateBufferOverflowDetection();
    demonstrateMemoryProfiling();
    demonstrateGarbageCollection();
    demonstrateDebuggingTools();
    demonstratePerformanceAnalysis();
    
    printf("\nAll memory management and debugging examples demonstrated!\n");
    printf("Note: Some features use Windows-specific APIs.\n");
    printf("For cross-platform development, consider using portable libraries.\n");
    
    return 0;
}
