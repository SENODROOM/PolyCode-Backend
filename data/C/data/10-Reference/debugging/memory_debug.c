#include <stdio.h>
#include <stdlib.h>
#include <string.h>

// Simple memory tracking system
#define MAX_ALLOCS 100

typedef struct {
    void* ptr;
    size_t size;
    const char* file;
    int line;
} Allocation;

static Allocation allocations[MAX_ALLOCS];
static int alloc_count = 0;

void* debug_malloc(size_t size, const char* file, int line) {
    void* ptr = malloc(size);
    
    if (ptr != NULL && alloc_count < MAX_ALLOCS) {
        allocations[alloc_count].ptr = ptr;
        allocations[alloc_count].size = size;
        allocations[alloc_count].file = file;
        allocations[alloc_count].line = line;
        alloc_count++;
        
        printf("[MALLOC] %p (%zu bytes) at %s:%d\n", ptr, size, file, line);
    }
    
    return ptr;
}

void debug_free(void* ptr, const char* file, int line) {
    if (ptr == NULL) return;
    
    // Find and remove allocation record
    for (int i = 0; i < alloc_count; i++) {
        if (allocations[i].ptr == ptr) {
            printf("[FREE] %p (%zu bytes) at %s:%d\n", ptr, allocations[i].size, file, line);
            
            // Shift remaining allocations
            for (int j = i; j < alloc_count - 1; j++) {
                allocations[j] = allocations[j + 1];
            }
            alloc_count--;
            break;
        }
    }
    
    free(ptr);
}

void print_memory_leaks() {
    printf("\n=== Memory Leak Report ===\n");
    if (alloc_count == 0) {
        printf("No memory leaks detected!\n");
    } else {
        printf("Found %d memory leaks:\n", alloc_count);
        for (int i = 0; i < alloc_count; i++) {
            printf("  Leak: %p (%zu bytes) allocated at %s:%d\n", 
                   allocations[i].ptr, allocations[i].size, 
                   allocations[i].file, allocations[i].line);
        }
    }
    printf("========================\n");
}

// Convenience macros
#define MALLOC(size) debug_malloc(size, __FILE__, __LINE__)
#define FREE(ptr) debug_free(ptr, __FILE__, __LINE__)

int main() {
    printf("Memory debugging example\n");
    printf("=======================\n");
    
    // Allocate some memory
    char* str1 = (char*)MALLOC(100);
    char* str2 = (char*)MALLOC(200);
    char* str3 = (char*)MALLOC(50);
    
    strcpy(str1, "Hello, World!");
    strcpy(str2, "This is a memory debugging example");
    strcpy(str3, "Short string");
    
    printf("Allocated 3 blocks of memory\n");
    
    // Free only 2 blocks (intentionally leak one)
    FREE(str1);
    FREE(str2);
    // str3 is intentionally leaked to demonstrate detection
    
    printf("Freed 2 blocks, 1 intentionally leaked\n");
    
    // Check for memory leaks
    print_memory_leaks();
    
    return 0;
}
