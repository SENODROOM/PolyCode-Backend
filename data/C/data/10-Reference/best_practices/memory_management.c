/*
 * File: memory_management.c
 * Description: Best practices for memory management in C
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>

// Memory allocation tracking structure
typedef struct MemoryBlock {
    void* ptr;
    size_t size;
    const char* file;
    int line;
    struct MemoryBlock* next;
} MemoryBlock;

// Global memory tracking
static MemoryBlock* memory_blocks = NULL;
static size_t total_allocated = 0;
static size_t total_freed = 0;

// Safe malloc with tracking
void* tracked_malloc(size_t size, const char* file, int line) {
    if (size == 0) {
        fprintf(stderr, "Warning: malloc(0) called at %s:%d\n", file, line);
        return NULL;
    }
    
    void* ptr = malloc(size);
    if (ptr == NULL) {
        fprintf(stderr, "Memory allocation failed at %s:%d (requested %zu bytes)\n", file, line, size);
        return NULL;
    }
    
    // Add to tracking list
    MemoryBlock* block = (MemoryBlock*)malloc(sizeof(MemoryBlock));
    if (block != NULL) {
        block->ptr = ptr;
        block->size = size;
        block->file = file;
        block->line = line;
        block->next = memory_blocks;
        memory_blocks = block;
        total_allocated += size;
    }
    
    return ptr;
}

// Safe free with tracking
void tracked_free(void* ptr, const char* file, int line) {
    if (ptr == NULL) {
        fprintf(stderr, "Warning: free(NULL) called at %s:%d\n", file, line);
        return;
    }
    
    // Find and remove from tracking list
    MemoryBlock** current = &memory_blocks;
    while (*current != NULL) {
        if ((*current)->ptr == ptr) {
            MemoryBlock* to_remove = *current;
            *current = (*current)->next;
            total_freed += to_remove->size;
            free(to_remove);
            free(ptr);
            return;
        }
        current = &(*current)->next;
    }
    
    fprintf(stderr, "Warning: Attempting to free untracked pointer at %s:%d\n", file, line);
    free(ptr);
}

// Convenience macros
#define MALLOC(size) tracked_malloc(size, __FILE__, __LINE__)
#define FREE(ptr) tracked_free(ptr, __FILE__, __LINE__)

// Memory leak detection
void detectMemoryLeaks() {
    printf("\n=== Memory Leak Report ===\n");
    printf("Total allocated: %zu bytes\n", total_allocated);
    printf("Total freed: %zu bytes\n", total_freed);
    printf("Leaked memory: %zu bytes\n", total_allocated - total_freed);
    
    if (memory_blocks == NULL) {
        printf("No memory leaks detected!\n");
    } else {
        printf("Memory leaks found:\n");
        MemoryBlock* current = memory_blocks;
        int leak_count = 0;
        
        while (current != NULL) {
            printf("  Leak %d: %p (%zu bytes) allocated at %s:%d\n", 
                   ++leak_count, current->ptr, current->size, current->file, current->line);
            current = current->next;
        }
    }
    printf("========================\n");
}

// Cleanup all tracked memory
void cleanupAllMemory() {
    MemoryBlock* current = memory_blocks;
    while (current != NULL) {
        MemoryBlock* next = current->next;
        printf("Freeing leaked memory: %p (%zu bytes)\n", current->ptr, current->size);
        free(current->ptr);
        free(current);
        current = next;
    }
    memory_blocks = NULL;
}

// Safe string duplication
char* safe_strdup(const char* str) {
    if (str == NULL) {
        return NULL;
    }
    
    size_t len = strlen(str) + 1;
    char* dup = (char*)MALLOC(len);
    if (dup != NULL) {
        strcpy(dup, str);
    }
    
    return dup;
}

// Dynamic array structure
typedef struct {
    int* data;
    size_t size;
    size_t capacity;
} DynamicArray;

// Create dynamic array
DynamicArray* createDynamicArray(size_t initial_capacity) {
    DynamicArray* array = (DynamicArray*)MALLOC(sizeof(DynamicArray));
    if (array == NULL) {
        return NULL;
    }
    
    array->data = (int*)MALLOC(initial_capacity * sizeof(int));
    if (array->data == NULL) {
        FREE(array);
        return NULL;
    }
    
    array->size = 0;
    array->capacity = initial_capacity;
    return array;
}

// Resize dynamic array
int resizeDynamicArray(DynamicArray* array, size_t new_capacity) {
    if (array == NULL || new_capacity < array->size) {
        return 0;
    }
    
    int* new_data = (int*)MALLOC(new_capacity * sizeof(int));
    if (new_data == NULL) {
        return 0;
    }
    
    // Copy existing data
    if (array->size > 0) {
        memcpy(new_data, array->data, array->size * sizeof(int));
    }
    
    // Free old data and update
    FREE(array->data);
    array->data = new_data;
    array->capacity = new_capacity;
    
    return 1;
}

// Add element to dynamic array
int addToArray(DynamicArray* array, int value) {
    if (array == NULL) {
        return 0;
    }
    
    // Resize if needed
    if (array->size >= array->capacity) {
        size_t new_capacity = array->capacity * 2;
        if (!resizeDynamicArray(array, new_capacity)) {
            return 0;
        }
    }
    
    array->data[array->size++] = value;
    return 1;
}

// Free dynamic array
void freeDynamicArray(DynamicArray* array) {
    if (array != NULL) {
        if (array->data != NULL) {
            FREE(array->data);
        }
        FREE(array);
    }
}

// Matrix structure
typedef struct {
    double** data;
    size_t rows;
    size_t cols;
} Matrix;

// Create matrix
Matrix* createMatrix(size_t rows, size_t cols) {
    if (rows == 0 || cols == 0) {
        return NULL;
    }
    
    Matrix* matrix = (Matrix*)MALLOC(sizeof(Matrix));
    if (matrix == NULL) {
        return NULL;
    }
    
    // Allocate row pointers
    matrix->data = (double**)MALLOC(rows * sizeof(double*));
    if (matrix->data == NULL) {
        FREE(matrix);
        return NULL;
    }
    
    // Allocate each row
    for (size_t i = 0; i < rows; i++) {
        matrix->data[i] = (double*)MALLOC(cols * sizeof(double));
        if (matrix->data[i] == NULL) {
            // Cleanup already allocated rows
            for (size_t j = 0; j < i; j++) {
                FREE(matrix->data[j]);
            }
            FREE(matrix->data);
            FREE(matrix);
            return NULL;
        }
        
        // Initialize to zero
        for (size_t j = 0; j < cols; j++) {
            matrix->data[i][j] = 0.0;
        }
    }
    
    matrix->rows = rows;
    matrix->cols = cols;
    return matrix;
}

// Free matrix
void freeMatrix(Matrix* matrix) {
    if (matrix != NULL) {
        if (matrix->data != NULL) {
            for (size_t i = 0; i < matrix->rows; i++) {
                if (matrix->data[i] != NULL) {
                    FREE(matrix->data[i]);
                }
            }
            FREE(matrix->data);
        }
        FREE(matrix);
    }
}

// Set matrix value
int setMatrixValue(Matrix* matrix, size_t row, size_t col, double value) {
    if (matrix == NULL || matrix->data == NULL) {
        return 0;
    }
    
    if (row >= matrix->rows || col >= matrix->cols) {
        return 0;
    }
    
    matrix->data[row][col] = value;
    return 1;
}

// Get matrix value
int getMatrixValue(Matrix* matrix, size_t row, size_t col, double* value) {
    if (matrix == NULL || matrix->data == NULL || value == NULL) {
        return 0;
    }
    
    if (row >= matrix->rows || col >= matrix->cols) {
        return 0;
    }
    
    *value = matrix->data[row][col];
    return 1;
}

// Print matrix
void printMatrix(Matrix* matrix) {
    if (matrix == NULL || matrix->data == NULL) {
        printf("Invalid matrix\n");
        return;
    }
    
    printf("Matrix (%zux%zu):\n", matrix->rows, matrix->cols);
    for (size_t i = 0; i < matrix->rows; i++) {
        for (size_t j = 0; j < matrix->cols; j++) {
            printf("%.2f\t", matrix->data[i][j]);
        }
        printf("\n");
    }
}

void demonstrateMemoryManagement() {
    printf("=== Memory Management Demonstration ===\n");
    
    // Test 1: Basic allocation and deallocation
    printf("\n1. Basic memory allocation:\n");
    int* numbers = (int*)MALLOC(10 * sizeof(int));
    if (numbers != NULL) {
        for (int i = 0; i < 10; i++) {
            numbers[i] = i * 10;
        }
        printf("   Allocated and initialized array\n");
        FREE(numbers);
        printf("   Freed array\n");
    }
    
    // Test 2: String duplication
    printf("\n2. String duplication:\n");
    char* original = "Hello, Memory Management!";
    char* duplicate = safe_strdup(original);
    if (duplicate != NULL) {
        printf("   Original: %s\n", original);
        printf("   Duplicate: %s\n", duplicate);
        FREE(duplicate);
        printf("   Freed duplicate\n");
    }
    
    // Test 3: Dynamic array
    printf("\n3. Dynamic array:\n");
    DynamicArray* array = createDynamicArray(5);
    if (array != NULL) {
        printf("   Created dynamic array with capacity %zu\n", array->capacity);
        
        // Add elements
        for (int i = 0; i < 15; i++) {
            if (addToArray(array, i * i)) {
                printf("   Added element %d: %d\n", i, i * i);
            } else {
                printf("   Failed to add element %d\n", i);
                break;
            }
        }
        
        printf("   Final size: %zu, capacity: %zu\n", array->size, array->capacity);
        freeDynamicArray(array);
        printf("   Freed dynamic array\n");
    }
    
    // Test 4: Matrix operations
    printf("\n4. Matrix operations:\n");
    Matrix* matrix = createMatrix(3, 4);
    if (matrix != NULL) {
        printf("   Created 3x4 matrix\n");
        
        // Set some values
        for (size_t i = 0; i < matrix->rows; i++) {
            for (size_t j = 0; j < matrix->cols; j++) {
                setMatrixValue(matrix, i, j, (double)(i * matrix->cols + j) * 1.5);
            }
        }
        
        printMatrix(matrix);
        freeMatrix(matrix);
        printf("   Freed matrix\n");
    }
    
    // Test 5: Intentional memory leak for demonstration
    printf("\n5. Intentional memory leak:\n");
    void* leak = MALLOC(100);
    printf("   Allocated 100 bytes but not freeing (for demonstration)\n");
    
    printf("\n=== Memory management demonstration completed ===\n");
}

int main() {
    demonstrateMemoryManagement();
    
    // Show memory leak report
    detectMemoryLeaks();
    
    // Cleanup any remaining leaks
    cleanupAllMemory();
    
    // Final leak check
    detectMemoryLeaks();
    
    return 0;
}
