# C Memory Management

## Memory Layout

### Program Memory Segments
```c
#include <stdio.h>

int global_var;          // Data segment (uninitialized)
int initialized_var = 42; // Data segment (initialized)

int main() {
    int local_var;        // Stack
    static int static_var = 10; // Data segment
    
    int *heap_var = (int*)malloc(sizeof(int)); // Heap
    
    printf("Global: %p\n", &global_var);
    printf("Initialized: %p\n", &initialized_var);
    printf("Static: %p\n", &static_var);
    printf("Local: %p\n", &local_var);
    printf("Heap: %p\n", heap_var);
    
    free(heap_var);
    return 0;
}
```

## Dynamic Memory Allocation

### malloc() - Memory Allocation
```c
#include <stdio.h>
#include <stdlib.h>

int main() {
    // Allocate memory for 10 integers
    int *ptr = (int*)malloc(10 * sizeof(int));
    
    if (ptr == NULL) {
        printf("Memory allocation failed\n");
        return 1;
    }
    
    // Use the allocated memory
    for (int i = 0; i < 10; i++) {
        ptr[i] = i * 10;
    }
    
    // Print values
    for (int i = 0; i < 10; i++) {
        printf("%d ", ptr[i]);
    }
    printf("\n");
    
    // Free the memory
    free(ptr);
    
    return 0;
}
```

### calloc() - Contiguous Allocation
```c
#include <stdio.h>
#include <stdlib.h>

int main() {
    // Allocate and initialize to zero
    int *ptr = (int*)calloc(5, sizeof(int));
    if (ptr == NULL) return 1;
    
    // Print values (all should be 0)
    for (int i = 0; i < 5; i++) {
        printf("%d ", ptr[i]);
    }
    printf("\n");
    
    free(ptr);
    return 0;
}
```

### realloc() - Reallocation
```c
#include <stdio.h>
#include <stdlib.h>

int main() {
    // Initial allocation
    int *ptr = (int*)malloc(3 * sizeof(int));
    if (ptr == NULL) return 1;
    
    ptr[0] = 10; ptr[1] = 20; ptr[2] = 30;
    
    // Resize to 5 elements
    int *new_ptr = (int*)realloc(ptr, 5 * sizeof(int));
    if (new_ptr == NULL) {
        free(ptr);
        return 1;
    }
    
    ptr = new_ptr;
    ptr[3] = 40; ptr[4] = 50;
    
    // Print all elements
    for (int i = 0; i < 5; i++) {
        printf("%d ", ptr[i]);
    }
    printf("\n");
    
    free(ptr);
    return 0;
}
```

## Memory Allocation Patterns

### Dynamic Arrays
```c
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    int *data;
    size_t size;
    size_t capacity;
} DynamicArray;

DynamicArray* create_array(size_t initial_capacity) {
    DynamicArray *arr = (DynamicArray*)malloc(sizeof(DynamicArray));
    if (arr == NULL) return NULL;
    
    arr->data = (int*)malloc(initial_capacity * sizeof(int));
    if (arr->data == NULL) {
        free(arr);
        return NULL;
    }
    
    arr->size = 0;
    arr->capacity = initial_capacity;
    return arr;
}

int push_back(DynamicArray *arr, int value) {
    if (arr->size >= arr->capacity) {
        // Resize array (double capacity)
        size_t new_capacity = arr->capacity * 2;
        int *new_data = (int*)realloc(arr->data, new_capacity * sizeof(int));
        if (new_data == NULL) return 0;
        
        arr->data = new_data;
        arr->capacity = new_capacity;
    }
    
    arr->data[arr->size++] = value;
    return 1;
}

void free_array(DynamicArray *arr) {
    if (arr != NULL) {
        free(arr->data);
        free(arr);
    }
}
```

### Dynamic Strings
```c
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

typedef struct {
    char *data;
    size_t length;
    size_t capacity;
} DynamicString;

DynamicString* create_string(const char *initial) {
    DynamicString *str = (DynamicString*)malloc(sizeof(DynamicString));
    if (str == NULL) return NULL;
    
    str->length = strlen(initial);
    str->capacity = str->length + 1;
    str->data = (char*)malloc(str->capacity);
    if (str->data == NULL) {
        free(str);
        return NULL;
    }
    
    strcpy(str->data, initial);
    return str;
}

int append_string(DynamicString *str, const char *suffix) {
    size_t suffix_len = strlen(suffix);
    size_t new_length = str->length + suffix_len;
    
    if (new_length + 1 > str->capacity) {
        size_t new_capacity = new_length + 1;
        char *new_data = (char*)realloc(str->data, new_capacity);
        if (new_data == NULL) return 0;
        
        str->data = new_data;
        str->capacity = new_capacity;
    }
    
    strcat(str->data, suffix);
    str->length = new_length;
    return 1;
}

void free_string(DynamicString *str) {
    if (str != NULL) {
        free(str->data);
        free(str);
    }
}
```

## Memory Leaks and Prevention

### Common Memory Leak Patterns
```c
// LEAK: Forgetting to free
void leak_example() {
    int *ptr = (int*)malloc(sizeof(int));
    *ptr = 42;
    // Forgot to call free(ptr)
}

// LEAK: Losing pointer
void leak_example2() {
    int *ptr = (int*)malloc(sizeof(int));
    ptr = (int*)malloc(sizeof(int)); // Lost first allocation
    free(ptr);
}

// LEAK: Early return
int leak_example3(int condition) {
    int *ptr = (int*)malloc(sizeof(int));
    if (condition) {
        return 0; // Early return without freeing
    }
    free(ptr);
    return 1;
}
```

### Safe Memory Management Patterns
```c
// SAFE: Always free before returning
int safe_function(int condition) {
    int *ptr = (int*)malloc(sizeof(int));
    if (ptr == NULL) return 0;
    
    if (condition) {
        free(ptr);
        return 0;
    }
    
    // Use ptr...
    free(ptr);
    return 1;
}

// SAFE: Use goto for cleanup (common in C)
int safe_with_goto(int condition) {
    int *ptr1 = NULL;
    int *ptr2 = NULL;
    
    ptr1 = (int*)malloc(sizeof(int));
    if (ptr1 == NULL) goto cleanup;
    
    ptr2 = (int*)malloc(sizeof(int));
    if (ptr2 == NULL) goto cleanup;
    
    // Use pointers...
    
cleanup:
    if (ptr1) free(ptr1);
    if (ptr2) free(ptr2);
    return 0;
}
```

## Memory Debugging

### Memory Tracking
```c
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    void *ptr;
    size_t size;
    const char *file;
    int line;
} AllocationInfo;

#define MAX_ALLOCATIONS 1000
static AllocationInfo allocations[MAX_ALLOCATIONS];
static int allocation_count = 0;

void* debug_malloc(size_t size, const char *file, int line) {
    void *ptr = malloc(size);
    if (ptr != NULL && allocation_count < MAX_ALLOCATIONS) {
        allocations[allocation_count].ptr = ptr;
        allocations[allocation_count].size = size;
        allocations[allocation_count].file = file;
        allocations[allocation_count].line = line;
        allocation_count++;
    }
    return ptr;
}

void debug_free(void *ptr) {
    free(ptr);
    
    // Remove from tracking
    for (int i = 0; i < allocation_count; i++) {
        if (allocations[i].ptr == ptr) {
            allocations[i] = allocations[allocation_count - 1];
            allocation_count--;
            break;
        }
    }
}

void print_leaks() {
    printf("Memory leaks detected:\n");
    for (int i = 0; i < allocation_count; i++) {
        printf("Leak: %zu bytes at %p (%s:%d)\n",
               allocations[i].size, allocations[i].ptr,
               allocations[i].file, allocations[i].line);
    }
}

#define MALLOC(size) debug_malloc(size, __FILE__, __LINE__)
#define FREE(ptr) debug_free(ptr)
```

## Advanced Memory Management

### Memory Pools
```c
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    char *pool;
    size_t size;
    size_t used;
} MemoryPool;

MemoryPool* create_pool(size_t size) {
    MemoryPool *pool = (MemoryPool*)malloc(sizeof(MemoryPool));
    if (pool == NULL) return NULL;
    
    pool->pool = (char*)malloc(size);
    if (pool->pool == NULL) {
        free(pool);
        return NULL;
    }
    
    pool->size = size;
    pool->used = 0;
    return pool;
}

void* pool_alloc(MemoryPool *pool, size_t size) {
    if (pool->used + size > pool->size) {
        return NULL; // Out of memory
    }
    
    void *ptr = pool->pool + pool->used;
    pool->used += size;
    return ptr;
}

void reset_pool(MemoryPool *pool) {
    pool->used = 0;
}

void free_pool(MemoryPool *pool) {
    if (pool != NULL) {
        free(pool->pool);
        free(pool);
    }
}
```

### Custom Allocators
```c
#include <stdio.h>
#include <stdlib.h>

typedef struct Block {
    size_t size;
    struct Block *next;
} Block;

static Block *free_list = NULL;

void* custom_malloc(size_t size) {
    // Round up to multiple of sizeof(Block)
    size = (size + sizeof(Block) - 1) & ~(sizeof(Block) - 1);
    
    // Search free list
    Block **prev = &free_list;
    Block *block = free_list;
    
    while (block != NULL) {
        if (block->size >= size) {
            *prev = block->next;
            return (void*)block;
        }
        prev = &block->next;
        block = block->next;
    }
    
    // Request from system
    Block *new_block = (Block*)malloc(size + sizeof(Block));
    if (new_block == NULL) return NULL;
    
    new_block->size = size;
    return (void*)new_block;
}

void custom_free(void *ptr) {
    if (ptr == NULL) return;
    
    Block *block = (Block*)ptr;
    block->next = free_list;
    free_list = block;
}
```

## Best Practices
- Always check malloc return value
- Free every allocation exactly once
- Use valgrind or similar tools to detect leaks
- Initialize allocated memory
- Avoid memory fragmentation
- Use appropriate allocation strategies
- Consider memory alignment requirements
- Document ownership and lifetime
- Use RAII-like patterns when possible
- Profile memory usage in critical sections
