# C Preprocessor

## Preprocessor Directives

### Include Directive
```c
#include <stdio.h>      // System header (angle brackets)
#include "myheader.h"   // User header (double quotes)

// Conditional includes
#ifdef _WIN32
    #include <windows.h>
#elif __linux__
    #include <unistd.h>
#endif
```

### Define Directive
```c
// Simple macros
#define PI 3.14159
#define MAX_SIZE 100
#define VERSION "1.0.0"

// Function-like macros
#define SQUARE(x) ((x) * (x))
#define MAX(a, b) ((a) > (b) ? (a) : (b))
#define ABS(x) ((x) < 0 ? -(x) : (x))

// Multi-line macros
#define PRINT_DEBUG(msg, value) \
    do { \
        printf("DEBUG: %s = %d\n", msg, value); \
    } while(0)

// Stringification
#define STRINGIFY(x) #x
#define TO_STRING(x) STRINGIFY(x)

// Concatenation
#define CONCAT(a, b) a##b
#define MAKE_VAR(name) int CONCAT(var_, name)
```

## Conditional Compilation

### If Directives
```c
#define DEBUG 1

#if DEBUG
    #define LOG(msg) printf("LOG: %s\n", msg)
#else
    #define LOG(msg) // No logging
#endif

// Multiple conditions
#if defined(UNIX) && !defined(DEBUG)
    #define OPTIMIZED 1
#elif defined(WINDOWS)
    #define WINDOWS_SPECIFIC 1
#else
    #define GENERIC 1
#endif

// Check if macro is defined
#ifdef VERSION
    printf("Version: %s\n", VERSION);
#endif

#ifndef VERSION
    #define VERSION "Unknown"
#endif
```

### Platform Detection
```c
#if defined(_WIN32) || defined(_WIN64)
    #define PLATFORM "Windows"
#elif defined(__linux__)
    #define PLATFORM "Linux"
#elif defined(__APPLE__)
    #define PLATFORM "macOS"
#else
    #define PLATFORM "Unknown"
#endif

printf("Platform: %s\n", PLATFORM);
```

## Macro Functions

### Safe Macro Design
```c
// ALWAYS use parentheses around parameters
#define SAFE_MULTIPLY(a, b) ((a) * (b))

// Problematic macro (multiple evaluations)
#define BAD_MAX(a, b) ((a) > (b) ? (a) : (b))

// Better version using inline function
static inline int safe_max(int a, int b) {
    return (a > b) ? a : b;
}

// Macro with do-while for safety
#define SWAP(a, b) \
    do { \
        typeof(a) _temp = (a); \
        (a) = (b); \
        (b) = _temp; \
    } while(0)
```

### Variadic Macros
```c
// C99 variadic macros
#define DEBUG_PRINTF(fmt, ...) \
    printf("DEBUG: " fmt "\n", ##__VA_ARGS__)

// Usage
DEBUG_PRINTF("Value: %d, Name: %s", 42, "test");

// Count arguments
#define COUNT_ARGS(...) COUNT_ARGS_IMPL(__VA_ARGS__, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
#define COUNT_ARGS_IMPL(_1, _2, _3, _4, _5, _6, _7, _8, _9, N, ...) N

int count = COUNT_ARGS(a, b, c); // Returns 3
```

## String Operations

### Stringification
```c
#define STRINGIFY(x) #x
#define TO_STRING(x) STRINGIFY(x)

int main() {
    printf(TO_STRING(Hello World) "\n"); // Prints "Hello World"
    
    int value = 42;
    printf("Variable name: " TO_STRING(value) " = %d\n", value);
    return 0;
}
```

### Token Pasting
```c
#define MAKE_FUNCTION(name) \
    void function_##name(void) { \
        printf("Function " #name " called\n"); \
    }

MAKE_FUNCTION(test)  // Creates function_test()
MAKE_FUNCTION(init)  // Creates function_init()

// Usage
function_test(); // Prints "Function test called"
function_init(); // Prints "Function init called"
```

## Header Guards

### Traditional Guards
```c
// myheader.h
#ifndef MYHEADER_H
#define MYHEADER_H

// Header content
void my_function(void);
extern int global_variable;

#endif // MYHEADER_H
```

### Modern Guards
```c
// myheader.h
#pragma once

// Header content
void my_function(void);
extern int global_variable;
```

## Predefined Macros

### Standard Macros
```c
#include <stdio.h>

int main() {
    printf("File: %s\n", __FILE__);
    printf("Line: %d\n", __LINE__);
    printf("Date: %s\n", __DATE__);
    printf("Time: %s\n", __TIME__);
    printf("Function: %s\n", __func__);
    printf("STDC: %d\n", __STDC__);
    printf("STDC_VERSION: %ld\n", __STDC_VERSION__);
    
    return 0;
}
```

### Compiler-Specific Macros
```c
#if defined(__GNUC__)
    #define COMPILER "GCC"
    #define GCC_VERSION (__GNUC__ * 10000 + __GNUC_MINOR__ * 100 + __GNUC_PATCHLEVEL__)
#elif defined(_MSC_VER)
    #define COMPILER "MSVC"
    #define MSVC_VERSION _MSC_VER
#elif defined(__clang__)
    #define COMPILER "Clang"
    #define CLANG_VERSION (__clang_major__ * 10000 + __clang_minor__ * 100 + __clang_patchlevel__)
#endif

printf("Compiler: %s\n", COMPILER);
```

## Advanced Techniques

### X-Macros
```c
// Define list of operations
#define OPERATIONS \
    X(ADD, +) \
    X(SUBTRACT, -) \
    X(MULTIPLY, *) \
    X(DIVIDE, /)

// Generate enum
enum Operation {
    #define X(name, op) name,
    OPERATIONS
    #undef X
    OPERATION_COUNT
};

// Generate function table
double calculate(int op, double a, double b) {
    switch (op) {
        #define X(name, op) case name: return a op b;
        OPERATIONS
        #undef X
        default: return 0.0;
    }
}

// Generate string names
const char* operation_names[] = {
    #define X(name, op) #name,
    OPERATIONS
    #undef X
};
```

### Compile-Time Calculations
```c
// Compile-time factorial (recursive)
#define FACTORIAL(n) FACTORIAL_IMPL(n)
#define FACTORIAL_IMPL(n) FACTORIAL_##n
#define FACTORIAL_0 1
#define FACTORIAL_1 1
#define FACTORIAL_2 2
#define FACTORIAL_3 6
#define FACTORIAL_4 24

// Compile-time array size
#define ARRAY_SIZE(arr) (sizeof(arr) / sizeof((arr)[0]))

// Compile-time string length
#define STRLEN(s) (sizeof(s) - 1)

// Compile-time max
#define MAX3(a, b, c) ((a) > (b) ? ((a) > (c) ? (a) : (c)) : ((b) > (c) ? (b) : (c)))
```

### Debug Macros
```c
#ifdef DEBUG
    #define ASSERT(condition) \
        do { \
            if (!(condition)) { \
                printf("Assertion failed: %s, file %s, line %d\n", \
                       #condition, __FILE__, __LINE__); \
                abort(); \
            } \
        } while(0)
    
    #define TRACE() printf("TRACE: %s:%d:%s\n", __FILE__, __LINE__, __func__);
#else
    #define ASSERT(condition) // No-op
    #define TRACE() // No-op
#endif

// Usage
ASSERT(x > 0);
TRACE();
```

## Error Handling

### Error Directives
```c
#if !defined(__STDC_VERSION__) || __STDC_VERSION__ < 199901L
    #error "This program requires C99 or later"
#endif

#ifndef REQUIRED_FEATURE
    #error "REQUIRED_FEATURE must be defined"
#endif

#warning "This feature is deprecated"
```

### Line Control
```c
// Change line number and filename for error reporting
#line 100 "custom_filename.c"
int error_here; // Will report as line 100 in custom_filename.c

// Restore original
#line __LINE__ __FILE__
```

## Best Practices
- Use parentheses around macro parameters
- Use do-while(0) for multi-statement macros
- Prefer inline functions over macros when possible
- Use #pragma once for new code
- Be careful with macro side effects
- Document macro behavior and limitations
- Use meaningful macro names
- Avoid macros that expand to nothing
- Test macros with various inputs
- Consider using enum constants instead of #define
