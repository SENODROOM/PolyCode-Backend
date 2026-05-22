# C Pointers

## Pointer Basics

### What is a Pointer?
A pointer is a variable that stores the memory address of another variable.

### Pointer Declaration
```c
int *ptr;           // Pointer to an integer
char *str;          // Pointer to a character
float *fptr;        // Pointer to a float
double *dptr;       // Pointer to a double
```

### Address Operator (&) and Dereference Operator (*)
```c
int num = 42;
int *ptr = &num;    // ptr now holds the address of num

printf("Value of num: %d\n", num);        // 42
printf("Address of num: %p\n", &num);     // Memory address
printf("Value of ptr: %p\n", ptr);        // Same as &num
printf("Value pointed by ptr: %d\n", *ptr); // 42
```

## Pointer Operations

### Pointer Assignment
```c
int a = 10, b = 20;
int *ptr1 = &a;
int *ptr2 = &b;

ptr1 = ptr2;  // ptr1 now points to b
```

### Pointer Arithmetic
```c
int arr[5] = {10, 20, 30, 40, 50};
int *ptr = arr;  // Points to first element

printf("%d\n", *ptr);      // 10
ptr++;                     // Points to next element
printf("%d\n", *ptr);      // 20
ptr += 2;                  // Points to third element from current
printf("%d\n", *ptr);      // 40

// Pointer difference
int *ptr1 = &arr[0];
int *ptr2 = &arr[4];
printf("Difference: %ld\n", ptr2 - ptr1); // 4
```

## Pointers and Arrays

### Array Name as Pointer
```c
int arr[5] = {1, 2, 3, 4, 5};
int *ptr = arr;

// Both are equivalent
printf("%d\n", arr[2]);    // 3
printf("%d\n", *(arr + 2)); // 3
printf("%d\n", ptr[2]);    // 3
printf("%d\n", *(ptr + 2)); // 3
```

### Pointer Arithmetic with Arrays
```c
int arr[5] = {10, 20, 30, 40, 50};
int *ptr = arr;

for (int i = 0; i < 5; i++) {
    printf("%d ", *(ptr + i));  // 10 20 30 40 50
}
```

## Pointers and Functions

### Pass by Reference
```c
void swap(int *a, int *b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

int main() {
    int x = 5, y = 10;
    printf("Before: x = %d, y = %d\n", x, y);
    swap(&x, &y);
    printf("After: x = %d, y = %d\n", x, y);
    return 0;
}
```

### Returning Pointers
```c
int* create_array(int size) {
    int *arr = (int*)malloc(size * sizeof(int));
    if (arr != NULL) {
        for (int i = 0; i < size; i++) {
            arr[i] = i * 10;
        }
    }
    return arr;
}

// Usage
int *numbers = create_array(5);
if (numbers != NULL) {
    for (int i = 0; i < 5; i++) {
        printf("%d ", numbers[i]);
    }
    free(numbers);  // Don't forget to free!
}
```

## Dynamic Memory Allocation

### malloc() - Memory Allocation
```c
int *ptr = (int*)malloc(5 * sizeof(int));
if (ptr == NULL) {
    printf("Memory allocation failed\n");
    return 1;
}

// Use the allocated memory
for (int i = 0; i < 5; i++) {
    ptr[i] = i + 1;
}

free(ptr);  // Free allocated memory
```

### calloc() - Contiguous Allocation
```c
int *ptr = (int*)calloc(5, sizeof(int));
// Allocates memory for 5 integers and initializes to 0

// No need to initialize, calloc sets to 0
for (int i = 0; i < 5; i++) {
    printf("%d ", ptr[i]);  // All zeros
}

free(ptr);
```

### realloc() - Reallocation
```c
int *ptr = (int*)malloc(3 * sizeof(int));
ptr[0] = 10; ptr[1] = 20; ptr[2] = 30;

// Resize to 5 elements
ptr = (int*)realloc(ptr, 5 * sizeof(int));
if (ptr != NULL) {
    ptr[3] = 40; ptr[4] = 50;
}

free(ptr);
```

## Advanced Pointer Concepts

### Pointer to Pointer
```c
int num = 42;
int *ptr = &num;
int **pptr = &ptr;

printf("Value: %d\n", num);
printf("Value via ptr: %d\n", *ptr);
printf("Value via pptr: %d\n", **pptr);
```

### Function Pointers
```c
int add(int a, int b) {
    return a + b;
}

int multiply(int a, int b) {
    return a * b;
}

int main() {
    int (*operation)(int, int);
    
    operation = add;
    printf("Addition: %d\n", operation(5, 3));  // 8
    
    operation = multiply;
    printf("Multiplication: %d\n", operation(5, 3));  // 15
    
    return 0;
}
```

### Array of Function Pointers
```c
int add(int a, int b) { return a + b; }
int subtract(int a, int b) { return a - b; }
int multiply(int a, int b) { return a * b; }

int main() {
    int (*operations[])(int, int) = {add, subtract, multiply};
    
    for (int i = 0; i < 3; i++) {
        printf("%d\n", operations[i](10, 5));
    }
    
    return 0;
}
```

## Pointers and Strings

### String Manipulation with Pointers
```c
char str[] = "Hello";
char *ptr = str;

// Traverse string
while (*ptr != '\0') {
    printf("%c", *ptr);
    ptr++;
}
printf("\n");

// String length
ptr = str;
int length = 0;
while (*ptr++ != '\0') {
    length++;
}
printf("Length: %d\n", length);
```

## Common Pointer Pitfalls

### Dangling Pointers
```c
int *create_int() {
    int x = 42;
    return &x;  // ERROR: Returning address of local variable
}

// Correct way
int *create_int() {
    int *x = (int*)malloc(sizeof(int));
    *x = 42;
    return x;  // Caller must free this memory
}
```

### Memory Leaks
```c
void memory_leak_example() {
    int *ptr = (int*)malloc(sizeof(int));
    *ptr = 42;
    // Forgot to free(ptr)
}

// Correct way
void no_leak_example() {
    int *ptr = (int*)malloc(sizeof(int));
    *ptr = 42;
    free(ptr);  // Always free allocated memory
}
```

## Best Practices
- Always initialize pointers
- Check for NULL after malloc/calloc
- Always free allocated memory
- Avoid returning pointers to local variables
- Use const with pointers when appropriate
- Be careful with pointer arithmetic
- Document pointer ownership and lifetime
