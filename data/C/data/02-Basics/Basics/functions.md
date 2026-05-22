# C Functions

## Function Declaration and Definition

### Basic Function Structure
```c
return_type function_name(parameter_list) {
    // Function body
    return value; // Optional
}
```

### Function Declaration (Prototype)
```c
return_type function_name(parameter_list);
```

### Example
```c
// Function declaration
int add(int a, int b);
void greet(char* name);
float calculate_average(float arr[], int size);

// Function definitions
int add(int a, int b) {
    return a + b;
}

void greet(char* name) {
    printf("Hello, %s!\n", name);
}

float calculate_average(float arr[], int size) {
    float sum = 0.0;
    for (int i = 0; i < size; i++) {
        sum += arr[i];
    }
    return sum / size;
}
```

## Function Parameters

### Pass by Value
```c
void swap(int x, int y) {
    int temp = x;
    x = y;
    y = temp;
    // Changes only affect local copies
}

int main() {
    int a = 5, b = 10;
    swap(a, b);
    // a is still 5, b is still 10
    return 0;
}
```

### Pass by Pointer (Reference)
```c
void swap(int *x, int *y) {
    int temp = *x;
    *x = *y;
    *y = temp;
    // Changes affect original variables
}

int main() {
    int a = 5, b = 10;
    swap(&a, &b);
    // a is now 10, b is now 5
    return 0;
}
```

## Return Types

### Functions with Return Values
```c
int square(int num) {
    return num * num;
}

double power(double base, int exp) {
    double result = 1.0;
    for (int i = 0; i < exp; i++) {
        result *= base;
    }
    return result;
}
```

### Void Functions (No Return Value)
```c
void print_message() {
    printf("This is a message\n");
}

void print_array(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
}
```

## Recursive Functions

### Example: Factorial
```c
int factorial(int n) {
    if (n <= 1) {
        return 1;
    }
    return n * factorial(n - 1);
}

// Example: Fibonacci
int fibonacci(int n) {
    if (n <= 1) {
        return n;
    }
    return fibonacci(n - 1) + fibonacci(n - 2);
}
```

## Function Pointers

### Declaring Function Pointers
```c
int (*func_ptr)(int, int);  // Pointer to function taking two ints, returning int
```

### Using Function Pointers
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
    int result1 = operation(5, 3);  // result1 = 8
    
    operation = multiply;
    int result2 = operation(5, 3);  // result2 = 15
    
    return 0;
}
```

## Static Functions

### Static Local Variables
```c
void counter() {
    static int count = 0;  // Initialized only once
    count++;
    printf("Count: %d\n", count);
}

// Each call increments the same variable
counter();  // Count: 1
counter();  // Count: 2
counter();  // Count: 3
```

### Static Functions (File Scope)
```c
static int helper_function() {
    // Only accessible within this file
    return 42;
}
```

## Best Practices
- Use descriptive function names
- Keep functions focused on one task
- Use function prototypes
- Document function parameters and return values
- Validate input parameters
- Avoid global variables when possible
