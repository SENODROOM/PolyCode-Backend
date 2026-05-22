#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// =============================================================================
// BASIC STRUCTURES
// =============================================================================

// Example 1: Simple Structure
struct Point {
    int x;
    int y;
};

// Example 2: Structure with different data types
struct Student {
    int id;
    char name[50];
    float gpa;
    char grade;
};

// Example 3: Nested structures
struct Address {
    char street[100];
    char city[50];
    char state[20];
    int zipCode;
};

struct Person {
    char name[50];
    int age;
    struct Address address;
};

// Example 4: Structure with arrays
struct Team {
    char teamName[50];
    struct Player {
        char name[50];
        int jerseyNumber;
        char position[20];
    } players[11];
    int numPlayers;
};

// =============================================================================
// ADVANCED STRUCTURES
// =============================================================================

// Example 5: Structure with pointers
struct DynamicArray {
    int *data;
    int size;
    int capacity;
};

// Example 6: Self-referential structure (for linked lists)
struct Node {
    int data;
    struct Node *next;
};

// Example 7: Structure with function pointers
struct Calculator {
    double (*add)(double, double);
    double (*subtract)(double, double);
    double (*multiply)(double, double);
    double (*divide)(double, double);
};

// =============================================================================
// UNIONS
// =============================================================================

// Example 8: Basic union
union Data {
    int i;
    float f;
    char str[20];
};

// Example 9: Union with structure
union Value {
    struct {
        int integer;
        float decimal;
    } number;
    char text[50];
};

// Example 10: Discriminated union
enum DataType { INT_TYPE, FLOAT_TYPE, STRING_TYPE };

struct Variant {
    enum DataType type;
    union {
        int intValue;
        float floatValue;
        char stringValue[50];
    } data;
};

// =============================================================================
// TYPEDEF AND COMPLEX TYPES
// =============================================================================

// Example 11: Using typedef
typedef struct {
    double real;
    double imaginary;
} Complex;

typedef struct Node ListNode;
typedef struct Node* NodePtr;

// Example 12: Bit fields
struct Flags {
    unsigned int isRead : 1;
    unsigned int isWrite : 1;
    unsigned int isExecute : 1;
    unsigned int isArchive : 1;
    unsigned int reserved : 28;
};

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicStructures() {
    printf("=== BASIC STRUCTURES ===\n");
    
    // Simple structure
    struct Point p1 = {10, 20};
    printf("Point: (%d, %d)\n", p1.x, p1.y);
    
    // Structure with different types
    struct Student student = {1001, "John Doe", 3.8, 'A'};
    printf("Student: ID=%d, Name=%s, GPA=%.2f, Grade=%c\n", 
           student.id, student.name, student.gpa, student.grade);
    
    // Nested structures
    struct Person person;
    strcpy(person.name, "Alice Smith");
    person.age = 25;
    strcpy(person.address.street, "123 Main St");
    strcpy(person.address.city, "New York");
    strcpy(person.address.state, "NY");
    person.address.zipCode = 10001;
    
    printf("Person: %s, Age=%d\n", person.name, person.age);
    printf("Address: %s, %s, %s %d\n", 
           person.address.street, person.address.city, 
           person.address.state, person.address.zipCode);
    
    printf("\n");
}

void demonstrateStructureArrays() {
    printf("=== STRUCTURE ARRAYS ===\n");
    
    struct Student students[3] = {
        {1001, "Alice", 3.9, 'A'},
        {1002, "Bob", 3.5, 'B'},
        {1003, "Charlie", 3.7, 'A'}
    };
    
    printf("Student Records:\n");
    for (int i = 0; i < 3; i++) {
        printf("%d: %s - GPA: %.1f\n", 
               students[i].id, students[i].name, students[i].gpa);
    }
    
    printf("\n");
}

void demonstratePointersToStructures() {
    printf("=== POINTERS TO STRUCTURES ===\n");
    
    struct Student student = {1001, "John Doe", 3.8, 'A'};
    struct Student *ptr = &student;
    
    // Accessing members through pointer
    printf("Using -> operator: %s\n", ptr->name);
    printf("Using * operator: %s\n", (*ptr).name);
    
    // Dynamic allocation
    struct Student *dynamicStudent = (struct Student*)malloc(sizeof(struct Student));
    if (dynamicStudent != NULL) {
        dynamicStudent->id = 1002;
        strcpy(dynamicStudent->name, "Jane Smith");
        dynamicStudent->gpa = 3.9;
        dynamicStudent->grade = 'A';
        
        printf("Dynamic student: %s\n", dynamicStudent->name);
        free(dynamicStudent);
    }
    
    printf("\n");
}

void demonstrateStructureFunctions() {
    printf("=== STRUCTURE FUNCTIONS ===\n");
    
    // Function that returns structure
    struct Point createPoint(int x, int y) {
        struct Point p = {x, y};
        return p;
    }
    
    // Function that takes structure as parameter
    void printPoint(struct Point p) {
        printf("Point: (%d, %d)\n", p.x, p.y);
    }
    
    // Function that modifies structure (pass by reference)
    void movePoint(struct Point *p, int dx, int dy) {
        p->x += dx;
        p->y += dy;
    }
    
    struct Point p = createPoint(5, 10);
    printPoint(p);
    
    movePoint(&p, 3, 4);
    printf("After moving: ");
    printPoint(p);
    
    printf("\n");
}

void demonstrateUnions() {
    printf("=== UNIONS ===\n");
    
    // Basic union
    union Data data;
    
    data.i = 42;
    printf("Data as integer: %d\n", data.i);
    
    data.f = 3.14f;
    printf("Data as float: %.2f\n", data.f);
    
    strcpy(data.str, "Hello");
    printf("Data as string: %s\n", data.str);
    
    // Note: Only one member is valid at a time
    printf("Size of union: %zu bytes\n", sizeof(union Data));
    printf("Size of largest member: %zu bytes\n", sizeof(float));
    
    printf("\n");
}

void demonstrateDiscriminatedUnion() {
    printf("=== DISCRIMINATED UNION ===\n");
    
    struct Variant variants[3];
    
    // Integer variant
    variants[0].type = INT_TYPE;
    variants[0].data.intValue = 42;
    
    // Float variant
    variants[1].type = FLOAT_TYPE;
    variants[1].data.floatValue = 3.14f;
    
    // String variant
    variants[2].type = STRING_TYPE;
    strcpy(variants[2].data.stringValue, "Hello World");
    
    for (int i = 0; i < 3; i++) {
        switch (variants[i].type) {
            case INT_TYPE:
                printf("Integer: %d\n", variants[i].data.intValue);
                break;
            case FLOAT_TYPE:
                printf("Float: %.2f\n", variants[i].data.floatValue);
                break;
            case STRING_TYPE:
                printf("String: %s\n", variants[i].data.stringValue);
                break;
        }
    }
    
    printf("\n");
}

void demonstrateBitFields() {
    printf("=== BIT FIELDS ===\n");
    
    struct Flags fileFlags;
    
    // Set individual flags
    fileFlags.isRead = 1;
    fileFlags.isWrite = 1;
    fileFlags.isExecute = 0;
    fileFlags.isArchive = 1;
    
    printf("File permissions:\n");
    printf("Read: %s\n", fileFlags.isRead ? "Yes" : "No");
    printf("Write: %s\n", fileFlags.isWrite ? "Yes" : "No");
    printf("Execute: %s\n", fileFlags.isExecute ? "Yes" : "No");
    printf("Archive: %s\n", fileFlags.isArchive ? "Yes" : "No");
    
    printf("Size of Flags structure: %zu bytes\n", sizeof(struct Flags));
    printf("Size of unsigned int: %zu bytes\n", sizeof(unsigned int));
    
    printf("\n");
}

void demonstrateComplexStructures() {
    printf("=== COMPLEX STRUCTURES ===\n");
    
    // Complex number with typedef
    Complex c1 = {3.0, 4.0};
    Complex c2 = {1.0, 2.0};
    
    printf("Complex numbers:\n");
    printf("c1 = %.1f + %.1fi\n", c1.real, c1.imaginary);
    printf("c2 = %.1f + %.1fi\n", c2.real, c2.imaginary);
    
    // Dynamic array structure
    struct DynamicArray arr;
    arr.size = 0;
    arr.capacity = 10;
    arr.data = (int*)malloc(arr.capacity * sizeof(int));
    
    if (arr.data != NULL) {
        // Add some elements
        for (int i = 0; i < 5; i++) {
            arr.data[i] = i * 10;
            arr.size++;
        }
        
        printf("Dynamic array contents: ");
        for (int i = 0; i < arr.size; i++) {
            printf("%d ", arr.data[i]);
        }
        printf("\n");
        
        free(arr.data);
    }
    
    printf("\n");
}

void demonstrateFunctionPointers() {
    printf("=== FUNCTION POINTERS IN STRUCTURES ===\n");
    
    // Arithmetic functions
    double add(double a, double b) { return a + b; }
    double subtract(double a, double b) { return a - b; }
    double multiply(double a, double b) { return a * b; }
    double divide(double a, double b) { return b != 0 ? a / b : 0; }
    
    struct Calculator calc;
    calc.add = add;
    calc.subtract = subtract;
    calc.multiply = multiply;
    calc.divide = divide;
    
    double a = 10.0, b = 3.0;
    
    printf("Calculator operations with %.1f and %.1f:\n", a, b);
    printf("Add: %.2f\n", calc.add(a, b));
    printf("Subtract: %.2f\n", calc.subtract(a, b));
    printf("Multiply: %.2f\n", calc.multiply(a, b));
    printf("Divide: %.2f\n", calc.divide(a, b));
    
    printf("\n");
}

void demonstrateMemoryLayout() {
    printf("=== MEMORY LAYOUT ===\n");
    
    printf("Size of various structures:\n");
    printf("Point: %zu bytes\n", sizeof(struct Point));
    printf("Student: %zu bytes\n", sizeof(struct Student));
    printf("Person: %zu bytes\n", sizeof(struct Person));
    printf("Data union: %zu bytes\n", sizeof(union Data));
    printf("Variant: %zu bytes\n", sizeof(struct Variant));
    printf("Flags: %zu bytes\n", sizeof(struct Flags));
    
    // Show alignment
    printf("\nAlignment in structures:\n");
    printf("char offset in Student: %zu\n", offsetof(struct Student, grade));
    printf("float offset in Student: %zu\n", offsetof(struct Student, gpa));
    
    printf("\n");
}

void demonstrateLinkedListNode() {
    printf("=== LINKED LIST NODE STRUCTURE ===\n");
    
    // Create nodes
    struct Node node1 = {10, NULL};
    struct Node node2 = {20, NULL};
    struct Node node3 = {30, NULL};
    
    // Link them
    node1.next = &node2;
    node2.next = &node3;
    
    // Traverse list
    struct Node *current = &node1;
    printf("Linked list: ");
    while (current != NULL) {
        printf("%d", current->data);
        if (current->next != NULL) {
            printf(" -> ");
        }
        current = current->next;
    }
    printf(" -> NULL\n");
    
    printf("\n");
}

// =============================================================================
// ADVANCED EXAMPLE: EMPLOYEE DATABASE
// =============================================================================

struct Date {
    int day;
    int month;
    int year;
};

struct Employee {
    int id;
    char name[50];
    struct Date birthDate;
    struct Date hireDate;
    double salary;
    char department[30];
    struct Address address;
};

void printEmployee(struct Employee emp) {
    printf("ID: %d\n", emp.id);
    printf("Name: %s\n", emp.name);
    printf("Birth Date: %02d/%02d/%04d\n", emp.birthDate.day, 
           emp.birthDate.month, emp.birthDate.year);
    printf("Hire Date: %02d/%02d/%04d\n", emp.hireDate.day, 
           emp.hireDate.month, emp.hireDate.year);
    printf("Salary: $%.2f\n", emp.salary);
    printf("Department: %s\n", emp.department);
    printf("Address: %s, %s, %s %d\n", emp.address.street, 
           emp.address.city, emp.address.state, emp.address.zipCode);
}

void demonstrateEmployeeDatabase() {
    printf("=== EMPLOYEE DATABASE EXAMPLE ===\n");
    
    struct Employee emp1 = {
        1001,
        "John Anderson",
        {15, 6, 1985},
        {1, 9, 2010},
        75000.00,
        "Engineering",
        {"456 Tech Blvd", "San Francisco", "CA", 94105}
    };
    
    struct Employee emp2 = {
        1002,
        "Sarah Johnson",
        {22, 3, 1990},
        {15, 1, 2015},
        68000.00,
        "Marketing",
        {"789 Market St", "San Francisco", "CA", 94102}
    };
    
    printf("Employee Records:\n");
    printf("================\n\n");
    
    printEmployee(emp1);
    printf("\n");
    printEmployee(emp2);
    
    printf("\n");
}

int main() {
    printf("Structures and Unions Examples\n");
    printf("==============================\n\n");
    
    demonstrateBasicStructures();
    demonstrateStructureArrays();
    demonstratePointersToStructures();
    demonstrateStructureFunctions();
    demonstrateUnions();
    demonstrateDiscriminatedUnion();
    demonstrateBitFields();
    demonstrateComplexStructures();
    demonstrateFunctionPointers();
    demonstrateMemoryLayout();
    demonstrateLinkedListNode();
    demonstrateEmployeeDatabase();
    
    printf("All structure and union examples demonstrated!\n");
    return 0;
}
