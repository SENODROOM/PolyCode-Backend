#ifndef DEBUG_MACROS_H
#define DEBUG_MACROS_H

#include <stdio.h>
#include <stdlib.h>

// Debug printing macros
#ifdef DEBUG
    #define DEBUG_PRINT(fmt, ...) printf("[DEBUG] %s:%d: " fmt "\n", __FILE__, __LINE__, ##__VA_ARGS__)
    #define DEBUG_VAR(var) printf("[DEBUG] %s:%d: %s = %d\n", __FILE__, __LINE__, #var, var)
#else
    #define DEBUG_PRINT(fmt, ...)
    #define DEBUG_VAR(var)
#endif

// Error handling macro
#define CHECK_ERROR(condition, message) \
    do { \
        if (condition) { \
            fprintf(stderr, "[ERROR] %s:%d: %s\n", __FILE__, __LINE__, message); \
            exit(EXIT_FAILURE); \
        } \
    } while(0)

// Memory allocation with error checking
#define SAFE_MALLOC(ptr, size) \
    do { \
        ptr = malloc(size); \
        if (ptr == NULL) { \
            fprintf(stderr, "[ERROR] %s:%d: Memory allocation failed\n", __FILE__, __LINE__); \
            exit(EXIT_FAILURE); \
        } \
    } while(0)

#endif // DEBUG_MACROS_H
