# String Operations Examples

This file contains 15 fundamental string operations in C, demonstrating manual implementations of common string functions. Each example shows how to work with C-style strings (null-terminated character arrays).

## 📝 Examples Overview

### 1. String Length (Manual Implementation)
**Purpose**: Calculate the length of a string without using `strlen()`
**Key Concept**: Iterate until null terminator (`\0`)
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 2. String Copy (Manual Implementation)
**Purpose**: Copy one string to another without using `strcpy()`
**Key Concept**: Character-by-character copying with null terminator
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 3. String Concatenation (Manual Implementation)
**Purpose**: Join two strings without using `strcat()`
**Key Concept**: Find end of destination, then append source
**Time Complexity**: O(n + m)
**Space Complexity**: O(1)

### 4. String Comparison (Manual Implementation)
**Purpose**: Compare two strings without using `strcmp()`
**Key Concept**: Character-by-character comparison
**Time Complexity**: O(min(n, m))
**Space Complexity**: O(1)

### 5. String Reverse
**Purpose**: Reverse a string in-place
**Key Concept**: Two-pointer swapping technique
**Time Complexity**: O(n/2)
**Space Complexity**: O(1)

### 6. Case Conversion - To Uppercase
**Purpose**: Convert string to uppercase
**Key Concept**: Use `toupper()` function from ctype.h
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 7. Case Conversion - To Lowercase
**Purpose**: Convert string to lowercase
**Key Concept**: Use `tolower()` function from ctype.h
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 8. Count Vowels
**Purpose**: Count the number of vowels in a string
**Key Concept**: Character classification and counting
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 9. Count Words
**Purpose**: Count the number of words in a string
**Key Concept**: State machine for word boundaries
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 10. Remove Spaces
**Purpose**: Remove all spaces from a string
**Key Concept**: In-place character shifting
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 11. Find Substring
**Purpose**: Find the position of a substring within a string
**Key Concept**: Naive string matching algorithm
**Time Complexity**: O(n × m)
**Space Complexity**: O(1)

### 12. Replace Character
**Purpose**: Replace all occurrences of a character
**Key Concept**: Linear scan and replacement
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 13. Check Palindrome
**Purpose**: Determine if a string reads the same forwards and backwards
**Key Concept**: Two-pointer comparison with case insensitivity
**Time Complexity**: O(n/2)
**Space Complexity**: O(1)

### 14. Trim Leading and Trailing Spaces
**Purpose**: Remove spaces from both ends of a string
**Key Concept**: Find boundaries and shift characters
**Time Complexity**: O(n)
**Space Complexity**: O(1)

### 15. String Tokenizer (Simple)
**Purpose**: Split a string by a delimiter
**Key Concept**: Scan for delimiter characters
**Time Complexity**: O(n)
**Space Complexity**: O(1)

## 🔍 Key Concepts Explained

### Null Termination
C strings are arrays of characters terminated by a null character (`\0`). This is crucial for:
- Determining string length
- Preventing buffer overflows
- String operations safety

### Memory Management
- **Static allocation**: Fixed-size character arrays
- **Buffer overflow**: Always ensure destination has enough space
- **Null terminator**: Must be manually added in custom implementations

### Character Classification
The `<ctype.h>` library provides useful functions:
- `isalpha()`: Check if character is alphabetic
- `isdigit()`: Check if character is numeric
- `isspace()`: Check if character is whitespace
- `toupper()`/`tolower()`: Case conversion

## 💡 Best Practices

### 1. Buffer Safety
```c
// Good: Check buffer size
char dest[100];
if (stringLength(src) < sizeof(dest) - 1) {
    stringCopy(dest, src);
}

// Bad: No size check
char dest[10];
stringCopy(dest, "This string is too long"); // Buffer overflow!
```

### 2. Null Terminator Handling
```c
// Good: Always add null terminator
dest[i] = '\0';

// Bad: Forgetting null terminator
// This can cause undefined behavior in string operations
```

### 3. Const Correctness
```c
// Good: Use const for input-only strings
int stringLength(const char *str);

// Good: Non-const for modifiable strings
void toUpperCase(char *str);
```

## ⚠️ Common Pitfalls

### 1. Buffer Overflow
```c
char small[10];
stringCopy(small, "This string is definitely too long"); // CRASH!
```

### 2. Forgetting Null Terminator
```c
char str[5];
str[0] = 'H'; str[1] = 'e'; str[2] = 'l'; str[3] = 'l'; str[4] = 'o';
// Missing str[5] = '\0';
printf("%s", str); // Undefined behavior!
```

### 3. Off-by-One Errors
```c
// Wrong
for (int i = 0; i <= length; i++) { // Should be i < length
    // Process str[i]
}

// Correct
for (int i = 0; i < length; i++) {
    // Process str[i]
}
```

## 🚀 Advanced String Topics

### 1. String Searching Algorithms
- **Knuth-Morris-Pratt (KMP)**: O(n + m) time
- **Boyer-Moore**: O(n/m) average case
- **Rabin-Karp**: O(n + m) with hashing

### 2. Dynamic String Handling
- Resizable strings using `realloc()`
- String builders for efficient concatenation
- Memory pool management

### 3. Unicode and UTF-8
- Multi-byte character handling
- Wide character functions (`wchar.h`)
- Internationalization considerations

### 4. String Optimization
- String interning
- Copy-on-write semantics
- Memory-mapped strings

## 📊 Performance Comparison

| Operation | Manual | Library | Notes |
|-----------|--------|---------|-------|
| String Length | O(n) | O(n) | Similar performance |
| String Copy | O(n) | O(n) | Library may use optimizations |
| String Concat | O(n+m) | O(n+m) | Library handles reallocation |
| String Compare | O(min(n,m)) | O(min(n,m)) | Library may use early exit |
| String Search | O(n×m) | O(n×m) | Library may use SIMD |

## 🧪 Testing Your Functions

### Test Cases to Include
1. **Empty string**: `""`
2. **Single character**: `"a"`
3. **Normal string**: `"Hello, World!"`
4. **Long string**: Test performance
5. **Special characters**: `"!@#$%^&*()"`
6. **Mixed case**: `"HeLLo WoRLd"`
7. **With spaces**: `"  Hello World  "`
8. **Unicode**: Test with UTF-8 if applicable

### Example Test Framework
```c
void testStringLength() {
    assert(stringLength("") == 0);
    assert(stringLength("a") == 1);
    assert(stringLength("Hello") == 5);
    assert(stringLength("Hello, World!") == 13);
}

void testStringReverse() {
    char str1[] = "abc";
    stringReverse(str1);
    assert(strcmp(str1, "cba") == 0);
    
    char str2[] = "racecar";
    stringReverse(str2);
    assert(strcmp(str2, "racecar") == 0); // Palindrome
}
```

## 🔧 Real-World Applications

1. **Text Processing**: File parsing, data cleaning
2. **User Input**: Validation, sanitization
3. **Database Operations**: Query building, result formatting
4. **Network Communication**: Protocol parsing, message formatting
5. **Security**: Password handling, encryption keys
6. **Localization**: Translation, character encoding

## 🎓 Next Steps

After mastering these string operations:
1. Study **Dynamic Memory Allocation** for flexible string sizes
2. Learn **Regular Expressions** for pattern matching
3. Explore **File I/O** for reading/writing text files
4. Practice **String Algorithms** (searching, sorting, matching)
5. Understand **Character Encoding** (ASCII, UTF-8, Unicode)

Remember: String manipulation is fundamental to programming. Master these examples before moving to more complex string algorithms!
