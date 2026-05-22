# C Arrays

## Array Declaration and Initialization

### Basic Declaration
```c
int numbers[5];           // Array of 5 integers
float prices[10];         // Array of 10 floats
char name[50];            // Array of 50 characters (string)
```

### Declaration with Initialization
```c
int numbers[5] = {1, 2, 3, 4, 5};
float prices[] = {10.5, 20.3, 30.7};  // Size inferred from initializer
char name[] = "Hello";                 // String initialization
```

### Partial Initialization
```c
int numbers[5] = {1, 2, 3};  // Remaining elements are 0
// numbers[0] = 1, numbers[1] = 2, numbers[2] = 3
// numbers[3] = 0, numbers[4] = 0
```

## Accessing Array Elements

### Indexing
```c
int numbers[5] = {10, 20, 30, 40, 50};

int first = numbers[0];    // 10
int last = numbers[4];     // 50
numbers[2] = 35;           // Change third element

printf("%d\n", numbers[2]); // 35
```

### Array Bounds
```c
int arr[5];
arr[0] = 10;    // Valid
arr[4] = 50;    // Valid
arr[5] = 60;    // Invalid! Out of bounds
arr[-1] = 5;    // Invalid! Out of bounds
```

## Multi-dimensional Arrays

### Two-Dimensional Arrays
```c
int matrix[3][4];  // 3 rows, 4 columns

// Initialization
int matrix[3][4] = {
    {1, 2, 3, 4},
    {5, 6, 7, 8},
    {9, 10, 11, 12}
};

// Accessing elements
int value = matrix[1][2];  // 7 (row 1, column 2)
matrix[2][3] = 99;         // Change element
```

### Three-Dimensional Arrays
```c
int cube[2][3][4];  // 2 layers, 3 rows, 4 columns

// Accessing elements
int value = cube[0][1][2];  // Layer 0, row 1, column 2
```

## Array Operations

### Traversing Arrays
```c
int numbers[5] = {1, 2, 3, 4, 5};

// Using for loop
for (int i = 0; i < 5; i++) {
    printf("%d ", numbers[i]);
}

// Calculating sum
int sum = 0;
for (int i = 0; i < 5; i++) {
    sum += numbers[i];
}

// Finding maximum
int max = numbers[0];
for (int i = 1; i < 5; i++) {
    if (numbers[i] > max) {
        max = numbers[i];
    }
}
```

### Two-dimensional Array Operations
```c
int matrix[3][3] = {
    {1, 2, 3},
    {4, 5, 6},
    {7, 8, 9}
};

// Printing matrix
for (int i = 0; i < 3; i++) {
    for (int j = 0; j < 3; j++) {
        printf("%d ", matrix[i][j]);
    }
    printf("\n");
}

// Calculating row sums
for (int i = 0; i < 3; i++) {
    int row_sum = 0;
    for (int j = 0; j < 3; j++) {
        row_sum += matrix[i][j];
    }
    printf("Row %d sum: %d\n", i, row_sum);
}
```

## Arrays and Functions

### Passing Arrays to Functions
```c
// Function prototype
void print_array(int arr[], int size);
int calculate_sum(int arr[], int size);

// Function definitions
void print_array(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
}

int calculate_sum(int arr[], int size) {
    int sum = 0;
    for (int i = 0; i < size; i++) {
        sum += arr[i];
    }
    return sum;
}

// Usage
int numbers[5] = {1, 2, 3, 4, 5};
print_array(numbers, 5);
int total = calculate_sum(numbers, 5);
```

### Two-dimensional Arrays and Functions
```c
void print_matrix(int matrix[][3], int rows) {
    for (int i = 0; i < rows; i++) {
        for (int j = 0; j < 3; j++) {
            printf("%d ", matrix[i][j]);
        }
        printf("\n");
    }
}

// Usage
int matrix[3][3] = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};
print_matrix(matrix, 3);
```

## Character Arrays (Strings)

### String Declaration
```c
char str1[6] = "Hello";     // 5 characters + null terminator
char str2[] = "World";       // Size automatically calculated
char str3[50];               // Empty string
```

### String Operations
```c
char name[50] = "John Doe";
printf("Name: %s\n", name);

// String length
int length = 0;
while (name[length] != '\0') {
    length++;
}
printf("Length: %d\n", length);

// Copying strings
char dest[50];
int i = 0;
while (name[i] != '\0') {
    dest[i] = name[i];
    i++;
}
dest[i] = '\0';
```

## Common Array Algorithms

### Linear Search
```c
int linear_search(int arr[], int size, int target) {
    for (int i = 0; i < size; i++) {
        if (arr[i] == target) {
            return i;  // Found at index i
        }
    }
    return -1;  // Not found
}
```

### Bubble Sort
```c
void bubble_sort(int arr[], int size) {
    for (int i = 0; i < size - 1; i++) {
        for (int j = 0; j < size - i - 1; j++) {
            if (arr[j] > arr[j + 1]) {
                // Swap elements
                int temp = arr[j];
                arr[j] = arr[j + 1];
                arr[j + 1] = temp;
            }
        }
    }
}
```

## Best Practices
- Always check array bounds
- Use symbolic constants for array sizes
- Initialize arrays before use
- Pass array size to functions
- Be careful with string null terminators
- Consider using dynamic allocation for large arrays
