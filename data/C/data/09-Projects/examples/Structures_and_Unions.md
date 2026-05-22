# Structures and Unions Examples

This file contains comprehensive examples demonstrating structures and unions in C. These composite data types allow you to group related data together and create custom data types for complex applications.

## 📚 Overview

### Structures
- **Purpose**: Group related data of different types
- **Memory**: Each member has its own memory space
- **Size**: Sum of all member sizes (plus padding)
- **Access**: All members accessible simultaneously

### Unions
- **Purpose**: Store different data types in same memory space
- **Memory**: All members share the same memory space
- **Size**: Size of largest member
- **Access**: Only one member valid at a time

## 🔍 Example Categories

### 🏗️ Basic Structures
Fundamental structure concepts and usage

### 🚀 Advanced Structures
Complex structure patterns and techniques

### 🔀 Unions
Union concepts and discriminated unions

### 🎯 Special Features
Bit fields, function pointers, typedef

## 🏗️ Basic Structure Examples

### 1. Simple Structure
```c
struct Point {
    int x;
    int y;
};

// Usage
struct Point p1 = {10, 20};
printf("Point: (%d, %d)\n", p1.x, p1.y);
```

### 2. Mixed Data Types
```c
struct Student {
    int id;
    char name[50];
    float gpa;
    char grade;
};
```

### 3. Nested Structures
```c
struct Address {
    char street[100];
    char city[50];
    int zipCode;
};

struct Person {
    char name[50];
    struct Address address;
};
```

### 4. Structure Arrays
```c
struct Team {
    char teamName[50];
    struct Player players[11];
    int numPlayers;
};
```

## 🚀 Advanced Structure Examples

### 5. Dynamic Structure
```c
struct DynamicArray {
    int *data;
    int size;
    int capacity;
};
```

### 6. Self-Referential Structure
```c
struct Node {
    int data;
    struct Node *next;
};
```

### 7. Function Pointers
```c
struct Calculator {
    double (*add)(double, double);
    double (*subtract)(double, double);
};
```

## 🔀 Union Examples

### 8. Basic Union
```c
union Data {
    int i;
    float f;
    char str[20];
};

// Only one member valid at a time
union Data data;
data.i = 42;        // Valid
data.f = 3.14f;     // Now i is invalid
```

### 9. Discriminated Union
```c
enum DataType { INT_TYPE, FLOAT_TYPE, STRING_TYPE };

struct Variant {
    enum DataType type;
    union {
        int intValue;
        float floatValue;
        char stringValue[50];
    } data;
};
```

## 🎯 Special Features

### 10. Typedef
```c
typedef struct {
    double real;
    double imaginary;
} Complex;

// Usage
Complex c1 = {3.0, 4.0};
```

### 11. Bit Fields
```c
struct Flags {
    unsigned int isRead : 1;
    unsigned int isWrite : 1;
    unsigned int isExecute : 1;
    unsigned int reserved : 29;
};
```

## 💡 Key Concepts

### Structure Declaration
```c
struct StructureName {
    type member1;
    type member2;
    // ...
};
```

### Union Declaration
```c
union UnionName {
    type member1;
    type member2;
    // ...
};
```

### Member Access
```c
// Direct access
structure.member

// Pointer access
pointer->member
(*pointer).member
```

### Initialization
```c
// Designated initializers (C99)
struct Student s = {
    .id = 1001,
    .name = "John Doe",
    .gpa = 3.8
};

// Ordered initialization
struct Student s = {1001, "John Doe", 3.8, 'A'};
```

## 🚀 Advanced Techniques

### 1. Memory Alignment
```c
// Compiler adds padding for alignment
struct Example {
    char c;     // 1 byte + 3 bytes padding
    int i;      // 4 bytes
};              // Total: 8 bytes
```

### 2. Flexible Array Members
```c
struct Flexible {
    int count;
    int data[];  // Flexible array member
};
```

### 3. Anonymous Structures/Unions
```c
struct Example {
    struct {
        int x, y;
    };  // Anonymous
    union {
        float f;
        int i;
    };
};
```

### 4. Structure Packing
```c
#pragma pack(push, 1)  // 1-byte alignment
struct Packed {
    char c;
    int i;  // No padding
};
#pragma pack(pop)     // Restore default
```

## 📊 Memory Layout

### Structure Memory
```
+-----------+-----------+-----------+
|  member1  |  member2  |  member3  |
+-----------+-----------+-----------+
   offset 0    offset 4    offset 8
```

### Union Memory
```
+---------------------------+
|     member1 (largest)     |
+---------------------------+
|     member2 (same space)  |
+---------------------------+
|     member3 (same space)  |
+---------------------------+
```

## 🧪 Testing Strategies

### 1. Size Verification
```c
void testStructureSize() {
    assert(sizeof(struct Point) == 8);  // 2 ints
    assert(sizeof(union Data) == 20);   // Largest member
}
```

### 2. Offset Testing
```c
void testMemberOffsets() {
    assert(offsetof(struct Student, gpa) == 54);
}
```

### 3. Pointer Access
```c
void testPointerAccess() {
    struct Student s = {1001, "John", 3.8};
    struct Student *ptr = &s;
    assert(ptr->id == 1001);
}
```

## ⚠️ Common Pitfalls

### 1. Forgetting `struct` Keyword
```c
// Wrong
Student s;

// Right
struct Student s;

// Or use typedef
typedef struct Student Student;
Student s;
```

### 2. Union Member Confusion
```c
union Data u;
u.i = 42;
u.f = 3.14f;  // u.i is now invalid!
printf("%d", u.i);  // Undefined behavior
```

### 3. Structure Assignment
```c
struct Student s1, s2;
s1 = s2;  // Copy all members
// For pointers, only copies pointer, not pointed data
```

### 4. Memory Alignment Issues
```c
// Don't assume no padding
struct Example {
    char c;
    int i;
};
// sizeof(struct Example) might be 8, not 5
```

### 5. Flexible Array Misuse
```c
struct Flexible {
    int count;
    int data[];  // Must be last member
};
// Cannot have other members after flexible array
```

## 🔧 Real-World Applications

### 1. Database Records
```c
struct Employee {
    int id;
    char name[50];
    double salary;
    // ... other fields
};
```

### 2. Graphics Programming
```c
struct Color {
    unsigned char red;
    unsigned char green;
    unsigned char blue;
    unsigned char alpha;
};
```

### 3. Network Protocols
```c
struct PacketHeader {
    uint16_t sourcePort;
    uint16_t destPort;
    uint16_t length;
    uint16_t checksum;
};
```

### 4. Configuration Management
```c
struct Config {
    char server[100];
    int port;
    int timeout;
    bool useSSL;
};
```

## 🎓 Best Practices

### 1. Use Typedef for Complex Types
```c
typedef struct {
    // members
} ComplexType;
```

### 2. Initialize Structures
```c
struct Point p = {0};  // Zero-initialize
// Or use designated initializers
struct Point p = {.x = 10, .y = 20};
```

### 3. Use Pointers for Large Structures
```c
void processLargeStruct(struct LargeStruct *ls);
// Pass by reference instead of by value
```

### 4. Document Memory Layout
```c
struct Example {
    /* 0 */ int field1;
    /* 4 */ int field2;
    /* 8 */ char field3;
    /* 9 */ char padding[3];
};
```

### 5. Use Unions Carefully
```c
struct Variant {
    enum Type type;
    union {
        int i;
        float f;
    } value;
};
// Always check type before accessing union
```

## 🔄 Structure vs Union Comparison

| Feature | Structure | Union |
|---------|-----------|-------|
| Memory | Separate for each member | Shared between members |
| Size | Sum of all members | Size of largest member |
| Access | All members accessible | One member at a time |
| Use Case | Group related data | Type punning, variants |
| Initialization | All members can be initialized | Only first member |

## 🧠 Advanced Patterns

### 1. Object-Oriented Programming
```c
struct AnimalVTable {
    void (*speak)(void*);
    void (*move)(void*);
};

struct Animal {
    struct AnimalVTable *vtable;
    char name[50];
};
```

### 2. Generic Data Structures
```c
struct GenericNode {
    void *data;
    size_t dataSize;
    struct GenericNode *next;
};
```

### 3. State Machines
```c
struct StateMachine {
    enum State current;
    void (*states[])(void*);
};
```

### 4. Serialization
```c
struct Serializable {
    void (*serialize)(void*, FILE*);
    void (*deserialize)(void*, FILE*);
};
```

## 📈 Performance Considerations

### 1. Cache Efficiency
- Order members by size (largest first)
- Keep related data together
- Avoid padding waste

### 2. Memory Usage
- Use bit fields for flags
- Consider unions for alternatives
- Allocate dynamically when appropriate

### 3. Copy Overhead
- Pass large structures by pointer
- Avoid unnecessary copying
- Use move semantics where possible

## 🔍 Debugging Structures

### 1. Print Functions
```c
void printStudent(struct Student *s) {
    printf("Student: %s (ID: %d)\n", s->name, s->id);
}
```

### 2. Memory Inspection
```c
void dumpMemory(void *ptr, size_t size) {
    unsigned char *bytes = (unsigned char*)ptr;
    for (size_t i = 0; i < size; i++) {
        printf("%02x ", bytes[i]);
    }
}
```

### 3. Validation Functions
```c
bool isValidStudent(struct Student *s) {
    return s->id > 0 && strlen(s->name) > 0;
}
```

Structures and unions are fundamental to C programming, enabling you to create complex, organized data structures for real-world applications. Master these concepts to build robust, maintainable software!
