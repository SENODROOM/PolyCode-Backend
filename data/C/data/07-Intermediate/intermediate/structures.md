# C Structures

## Structure Basics

### What is a Structure?
A structure is a user-defined data type that groups related variables of different data types under a single name.

### Structure Declaration
```c
struct Person {
    char name[50];
    int age;
    float height;
    char email[100];
};

// Declaration and initialization
struct Person person1 = {"John Doe", 30, 5.8, "john@example.com"};

// Accessing members
printf("Name: %s\n", person1.name);
printf("Age: %d\n", person1.age);
person1.age = 31;  // Modify member
```

### Structure Definition with typedef
```c
typedef struct {
    char name[50];
    int age;
    float salary;
} Employee;

// Now we can use Employee directly
Employee emp1 = {"Alice Smith", 28, 75000.50};
Employee emp2;
```

## Structure Initialization

### Initialization at Declaration
```c
struct Point {
    int x;
    int y;
};

struct Point p1 = {10, 20};
struct Point p2 = {.x = 30, .y = 40};  // Designated initializers

// Partial initialization
struct Point p3 = {50};  // y will be 0
struct Point p4 = {.y = 60};  // x will be 0
```

### Initialization After Declaration
```c
struct Student {
    char name[50];
    int id;
    float gpa;
};

struct Student s1;
strcpy(s1.name, "Bob Johnson");
s1.id = 12345;
s1.gpa = 3.8;
```

## Nested Structures

### Structures Within Structures
```c
struct Address {
    char street[100];
    char city[50];
    char state[20];
    int zip_code;
};

struct Person {
    char name[50];
    int age;
    struct Address address;
};

// Usage
struct Person person = {
    "Jane Doe",
    25,
    {"123 Main St", "New York", "NY", 10001}
};

// Accessing nested members
printf("Street: %s\n", person.address.street);
printf("City: %s\n", person.address.city);
```

### Self-referential Structures
```c
struct Node {
    int data;
    struct Node *next;  // Pointer to same structure type
};

// Usage for linked list
struct Node node1 = {10, NULL};
struct Node node2 = {20, NULL};
node1.next = &node2;
```

## Structures and Functions

### Passing Structures to Functions
```c
struct Point {
    int x;
    int y;
};

// Pass by value (copy)
void print_point(struct Point p) {
    printf("Point: (%d, %d)\n", p.x, p.y);
}

// Pass by pointer (reference)
void modify_point(struct Point *p) {
    p->x = 100;
    p->y = 200;
}

// Return structure
struct Point create_point(int x, int y) {
    struct Point p = {x, y};
    return p;
}

// Usage
int main() {
    struct Point p1 = {10, 20};
    print_point(p1);
    
    modify_point(&p1);
    print_point(p1);  // Now (100, 200)
    
    struct Point p2 = create_point(50, 60);
    print_point(p2);
    
    return 0;
}
```

## Arrays of Structures

### Structure Arrays
```c
struct Employee {
    int id;
    char name[50];
    float salary;
};

struct Employee employees[3] = {
    {101, "Alice", 75000.0},
    {102, "Bob", 65000.0},
    {103, "Charlie", 80000.0}
};

// Accessing array elements
for (int i = 0; i < 3; i++) {
    printf("ID: %d, Name: %s, Salary: %.2f\n", 
           employees[i].id, employees[i].name, employees[i].salary);
}

// Modifying array elements
employees[1].salary = 70000.0;
```

## Pointers to Structures

### Structure Pointers
```c
struct Point {
    int x;
    int y;
};

struct Point p1 = {10, 20};
struct Point *ptr = &p1;

// Accessing members via pointer
printf("X: %d\n", (*ptr).x);    // Method 1
printf("Y: %d\n", ptr->y);     // Method 2 (arrow operator)

// Modifying via pointer
ptr->x = 30;
ptr->y = 40;
```

### Dynamic Allocation of Structures
```c
struct Person *create_person(const char *name, int age) {
    struct Person *p = (struct Person*)malloc(sizeof(struct Person));
    if (p != NULL) {
        strcpy(p->name, name);
        p->age = age;
    }
    return p;
}

// Usage
struct Person *person = create_person("Dynamic Person", 35);
if (person != NULL) {
    printf("Name: %s, Age: %d\n", person->name, person->age);
    free(person);  // Don't forget to free!
}
```

## Bit Fields

### Bit Field Declaration
```c
struct Flags {
    unsigned int is_enabled : 1;
    unsigned int has_permission : 1;
    unsigned int priority : 3;
    unsigned int status : 3;
};

// Usage
struct Flags config = {0};
config.is_enabled = 1;
config.has_permission = 1;
config.priority = 5;
config.status = 2;

printf("Size of Flags: %zu bytes\n", sizeof(struct Flags));
```

## Unions

### Union Declaration
```c
union Data {
    int i;
    float f;
    char str[20];
};

// Usage
union Data data;
data.i = 42;
printf("Integer: %d\n", data.i);

data.f = 3.14f;
printf("Float: %.2f\n", data.f);

strcpy(data.str, "Hello");
printf("String: %s\n", data.str);

// Note: Only one member can hold a value at a time
```

## Structure Alignment and Padding

### Understanding Memory Layout
```c
struct Example {
    char c;      // 1 byte + 3 bytes padding
    int i;       // 4 bytes
    short s;     // 2 bytes + 2 bytes padding
    double d;    // 8 bytes
};

// Total size might be larger than sum of individual sizes
printf("Size of struct: %zu bytes\n", sizeof(struct Example));
```

### Controlling Alignment
```c
// Packed structure (compiler-specific)
#pragma pack(push, 1)
struct Packed {
    char c;
    int i;
    short s;
};
#pragma pack(pop)

printf("Packed struct size: %zu bytes\n", sizeof(struct Packed));
```

## Advanced Structure Usage

### Function Pointers in Structures
```c
struct Calculator {
    int (*add)(int, int);
    int (*multiply)(int, int);
};

int add_func(int a, int b) { return a + b; }
int multiply_func(int a, int b) { return a * b; }

struct Calculator calc = {add_func, multiply_func};
printf("5 + 3 = %d\n", calc.add(5, 3));
```

### Variable Length Structures
```c
struct FlexibleArray {
    int length;
    int data[];  // Flexible array member (C99)
};

struct FlexibleArray *create_array(int size) {
    struct FlexibleArray *arr = (struct FlexibleArray*)malloc(
        sizeof(struct FlexibleArray) + size * sizeof(int));
    if (arr != NULL) {
        arr->length = size;
        for (int i = 0; i < size; i++) {
            arr->data[i] = i * 10;
        }
    }
    return arr;
}
```

## Common Structure Patterns

### Linked List Node
```c
struct ListNode {
    int data;
    struct ListNode *next;
};

// Creating a linked list
struct ListNode *create_node(int data) {
    struct ListNode *node = (struct ListNode*)malloc(sizeof(struct ListNode));
    if (node != NULL) {
        node->data = data;
        node->next = NULL;
    }
    return node;
}
```

### Binary Tree Node
```c
struct TreeNode {
    int data;
    struct TreeNode *left;
    struct TreeNode *right;
};
```

## Best Practices
- Use meaningful structure names
- Group related data together
- Use typedef for cleaner syntax
- Pass structures by pointer for efficiency
- Initialize all structure members
- Consider memory alignment for performance
- Use const for structure parameters that shouldn't be modified
- Document structure purpose and member meanings
