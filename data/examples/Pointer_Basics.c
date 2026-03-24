#include <stdio.h>
#include <stdlib.h>

// Example 1: Basic Pointer Declaration and Usage
void basicPointerExample() {
    printf("=== Example 1: Basic Pointer Usage ===\n");
    
    int number = 42;
    int *ptr = &number; // ptr points to number
    
    printf("Value of number: %d\n", number);
    printf("Address of number: %p\n", (void*)&number);
    printf("Value of ptr (address): %p\n", (void*)ptr);
    printf("Value pointed to by ptr: %d\n", *ptr);
    printf("Address of ptr itself: %p\n\n", (void*)&ptr);
}

// Example 2: Pointer Arithmetic
void pointerArithmeticExample() {
    printf("=== Example 2: Pointer Arithmetic ===\n");
    
    int arr[] = {10, 20, 30, 40, 50};
    int *ptr = arr; // Points to first element
    
    printf("Array elements using pointer arithmetic:\n");
    for (int i = 0; i < 5; i++) {
        printf("arr[%d] = %d (ptr + %d = %p)\n", 
               i, *(ptr + i), i, (void*)(ptr + i));
    }
    
    printf("\nPointer increments:\n");
    printf("Initial ptr points to: %d\n", *ptr);
    ptr++; // Move to next element
    printf("After ptr++, points to: %d\n", *ptr);
    ptr += 2; // Move two elements forward
    printf("After ptr += 2, points to: %d\n", *ptr);
    ptr--; // Move one element back
    printf("After ptr--, points to: %d\n\n", *ptr);
}

// Example 3: Pointers and Functions
void modifyValue(int *ptr) {
    *ptr = 100; // Modify the value at the address
}

void swap(int *a, int *b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

void functionPointerExample() {
    printf("=== Example 3: Pointers and Functions ===\n");
    
    int x = 10;
    printf("Before modifyValue: x = %d\n", x);
    modifyValue(&x);
    printf("After modifyValue: x = %d\n", x);
    
    int y = 20, z = 30;
    printf("\nBefore swap: y = %d, z = %d\n", y, z);
    swap(&y, &z);
    printf("After swap: y = %d, z = %d\n\n", y, z);
}

// Example 4: Pointers and Arrays
void arrayPointerExample() {
    printf("=== Example 4: Pointers and Arrays ===\n");
    
    int arr[3][4] = {
        {1, 2, 3, 4},
        {5, 6, 7, 8},
        {9, 10, 11, 12}
    };
    
    printf("2D Array using pointers:\n");
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 4; j++) {
            printf("arr[%d][%d] = %d (address: %p)\n", 
                   i, j, *(*(arr + i) + j), (void*)(*(arr + i) + j));
        }
    }
    printf("\n");
}

// Example 5: Dynamic Memory Allocation
void dynamicMemoryExample() {
    printf("=== Example 5: Dynamic Memory Allocation ===\n");
    
    // Allocate memory for an integer
    int *dynamicInt = (int*)malloc(sizeof(int));
    if (dynamicInt == NULL) {
        printf("Memory allocation failed!\n");
        return;
    }
    
    *dynamicInt = 99;
    printf("Dynamically allocated integer: %d\n", *dynamicInt);
    free(dynamicInt);
    
    // Allocate memory for an array
    int size = 5;
    int *dynamicArray = (int*)malloc(size * sizeof(int));
    if (dynamicArray == NULL) {
        printf("Memory allocation failed!\n");
        return;
    }
    
    // Initialize array
    for (int i = 0; i < size; i++) {
        dynamicArray[i] = (i + 1) * 10;
    }
    
    printf("Dynamically allocated array:\n");
    for (int i = 0; i < size; i++) {
        printf("dynamicArray[%d] = %d\n", i, dynamicArray[i]);
    }
    
    free(dynamicArray);
    
    // Realloc example
    int *resizeArray = (int*)malloc(3 * sizeof(int));
    resizeArray[0] = 1;
    resizeArray[1] = 2;
    resizeArray[2] = 3;
    
    printf("\nOriginal array (size 3): ");
    for (int i = 0; i < 3; i++) {
        printf("%d ", resizeArray[i]);
    }
    
    // Resize to 5 elements
    resizeArray = (int*)realloc(resizeArray, 5 * sizeof(int));
    if (resizeArray != NULL) {
        resizeArray[3] = 4;
        resizeArray[4] = 5;
        
        printf("\nResized array (size 5): ");
        for (int i = 0; i < 5; i++) {
            printf("%d ", resizeArray[i]);
        }
    }
    
    free(resizeArray);
    printf("\n\n");
}

// Example 6: Pointer to Pointer
void pointerToPointerExample() {
    printf("=== Example 6: Pointer to Pointer ===\n");
    
    int value = 42;
    int *ptr = &value;
    int **ptr_to_ptr = &ptr;
    
    printf("Value: %d\n", value);
    printf("Address of value: %p\n", (void*)&value);
    printf("ptr points to: %p, value at ptr: %d\n", (void*)ptr, *ptr);
    printf("ptr_to_ptr points to: %p\n", (void*)ptr_to_ptr);
    printf("Value at ptr_to_ptr: %p\n", (void*)*ptr_to_ptr);
    printf("Final value: %d\n\n", **ptr_to_ptr);
}

// Example 7: Function Pointers
int add(int a, int b) { return a + b; }
int subtract(int a, int b) { return a - b; }
int multiply(int a, int b) { return a * b; }
int divide(int a, int b) { return b != 0 ? a / b : 0; }

void functionPointerExample() {
    printf("=== Example 7: Function Pointers ===\n");
    
    // Array of function pointers
    int (*operations[])(int, int) = {add, subtract, multiply, divide};
    char *op_names[] = {"add", "subtract", "multiply", "divide"};
    
    int a = 10, b = 5;
    
    for (int i = 0; i < 4; i++) {
        int result = operations[i](a, b);
        printf("%d %s %d = %d\n", a, op_names[i], b, result);
    }
    
    // Function pointer as parameter
    printf("\nUsing function pointer as parameter:\n");
    int (*operation)(int, int) = add;
    printf("Result of operation: %d\n\n", operation(a, b));
}

// Example 8: Void Pointers
void voidPointerExample() {
    printf("=== Example 8: Void Pointers ===\n");
    
    int i = 42;
    float f = 3.14f;
    char c = 'A';
    
    void *void_ptr;
    
    // Point to integer
    void_ptr = &i;
    printf("Void pointer points to int: %d\n", *(int*)void_ptr);
    
    // Point to float
    void_ptr = &f;
    printf("Void pointer points to float: %.2f\n", *(float*)void_ptr);
    
    // Point to character
    void_ptr = &c;
    printf("Void pointer points to char: %c\n\n", *(char*)void_ptr);
}

// Example 9: NULL Pointers and Safety
void nullPointerExample() {
    printf("=== Example 9: NULL Pointers and Safety ===\n");
    
    int *null_ptr = NULL;
    
    printf("NULL pointer value: %p\n", (void*)null_ptr);
    
    // Always check for NULL before dereferencing
    if (null_ptr != NULL) {
        printf("This won't print\n");
    } else {
        printf("Pointer is NULL, cannot dereference\n");
    }
    
    // Safe allocation and check
    int *safe_ptr = (int*)malloc(sizeof(int));
    if (safe_ptr != NULL) {
        *safe_ptr = 123;
        printf("Safely allocated and used: %d\n", *safe_ptr);
        free(safe_ptr);
    }
    
    printf("\n");
}

// Example 10: Pointer Casting and Type Safety
void pointerCastingExample() {
    printf("=== Example 10: Pointer Casting ===\n");
    
    int numbers[] = {1, 2, 3, 4, 5};
    char *char_ptr = (char*)numbers;
    
    printf("Original int array: ");
    for (int i = 0; i < 5; i++) {
        printf("%d ", numbers[i]);
    }
    
    printf("\nInterpreted as bytes: ");
    for (int i = 0; i < 5 * sizeof(int); i++) {
        printf("%02x ", (unsigned char)char_ptr[i]);
    }
    
    printf("\n\n");
}

int main() {
    printf("Pointer Basics Examples\n");
    printf("======================\n\n");
    
    basicPointerExample();
    pointerArithmeticExample();
    functionPointerExample();
    arrayPointerExample();
    dynamicMemoryExample();
    pointerToPointerExample();
    functionPointerExample();
    voidPointerExample();
    nullPointerExample();
    pointerCastingExample();
    
    printf("All examples completed!\n");
    return 0;
}
