# C Strings

## String Basics

### What is a C String?
A C string is an array of characters terminated by a null character (`\0`).

### String Declaration
```c
char str1[6] = "Hello";     // Explicit size
char str2[] = "World";      // Size automatically calculated
char str3[50] = "C Programming";  // Larger buffer
char *str4 = "Pointer string";    // Pointer to string literal
```

### String Initialization
```c
// Character by character
char str1[6] = {'H', 'e', 'l', 'l', 'o', '\0'};

// String literal
char str2[] = "Hello";

// Partial initialization
char str3[10] = "Hello";    // Remaining elements are '\0'
```

## String Input/Output

### String Input
```c
#include <stdio.h>

int main() {
    char name[50];
    
    // scanf - stops at whitespace
    printf("Enter your name: ");
    scanf("%s", name);
    printf("Hello, %s!\n", name);
    
    // fgets - reads entire line
    char full_name[100];
    printf("Enter your full name: ");
    fgets(full_name, sizeof(full_name), stdin);
    
    // Remove newline if present
    size_t len = strlen(full_name);
    if (len > 0 && full_name[len - 1] == '\n') {
        full_name[len - 1] = '\0';
    }
    
    printf("Full name: %s\n", full_name);
    
    return 0;
}
```

### String Output
```c
char message[] = "Hello, World!";
printf("%s\n", message);     // Print entire string
printf("%.5s\n", message);   // Print first 5 characters
printf("%20s\n", message);   // Right-aligned in 20 characters
printf("%-20s\n", message);  // Left-aligned in 20 characters
```

## String Functions

### String Length
```c
size_t strlen(const char *str);

// Custom implementation
size_t my_strlen(const char *str) {
    size_t len = 0;
    while (*str++ != '\0') {
        len++;
    }
    return len;
}

// Usage
char text[] = "Hello";
printf("Length: %zu\n", strlen(text));  // 5
```

### String Copy
```c
char *strcpy(char *dest, const char *src);
char *strncpy(char *dest, const char *src, size_t n);

// Custom implementation
char *my_strcpy(char *dest, const char *src) {
    char *original_dest = dest;
    while ((*dest++ = *src++) != '\0');
    return original_dest;
}

// Usage
char dest[50];
char src[] = "Hello, World!";
strcpy(dest, src);

// Safer alternative
strncpy(dest, src, sizeof(dest) - 1);
dest[sizeof(dest) - 1] = '\0';  // Ensure null termination
```

### String Concatenation
```c
char *strcat(char *dest, const char *src);
char *strncat(char *dest, const char *src, size_t n);

// Usage
char str1[50] = "Hello, ";
char str2[] = "World!";
strcat(str1, str2);  // str1 = "Hello, World!"

// Safer alternative
strncat(str1, str2, sizeof(str1) - strlen(str1) - 1);
```

### String Comparison
```c
int strcmp(const char *str1, const char *str2);
int strncmp(const char *str1, const char *str2, size_t n);

// Custom implementation
int my_strcmp(const char *str1, const char *str2) {
    while (*str1 && (*str1 == *str2)) {
        str1++;
        str2++;
    }
    return *(unsigned char*)str1 - *(unsigned char*)str2;
}

// Usage
char str1[] = "Apple";
char str2[] = "Banana";

if (strcmp(str1, str2) < 0) {
    printf("%s comes before %s\n", str1, str2);
} else if (strcmp(str1, str2) > 0) {
    printf("%s comes after %s\n", str1, str2);
} else {
    printf("Strings are equal\n");
}
```

### String Search
```c
char *strchr(const char *str, int c);      // Find first occurrence
char *strrchr(const char *str, int c);     // Find last occurrence
char *strstr(const char *haystack, const char *needle);  // Find substring

// Usage
char text[] = "Hello, World!";
char *ptr = strchr(text, 'o');  // Points to first 'o'
if (ptr != NULL) {
    printf("Found 'o' at position: %ld\n", ptr - text);
}

char *substr = strstr(text, "World");
if (substr != NULL) {
    printf("Found substring: %s\n", substr);
}
```

## String Manipulation

### Tokenization
```c
char *strtok(char *str, const char *delim);

// Usage
char sentence[] = "This is a sample sentence";
char *token = strtok(sentence, " ");

while (token != NULL) {
    printf("%s\n", token);
    token = strtok(NULL, " ");
}

// Note: strtok modifies the original string
// For multiple strings, use strtok_r or make a copy
```

### String Conversion
```c
#include <stdlib.h>

// String to integer
int num = atoi("123");           // 123
long lnum = atol("123456789");  // 123456789

// String to floating-point
double d = atof("3.14159");      // 3.14159

// Safer alternatives
int num2;
if (sscanf("123", "%d", &num2) == 1) {
    printf("Successfully parsed: %d\n", num2);
}

// Integer to string
char buffer[50];
sprintf(buffer, "%d", 42);       // "42"
```

## Advanced String Operations

### Custom String Functions
```c
// Count occurrences of a character
int count_char(const char *str, char c) {
    int count = 0;
    while (*str) {
        if (*str == c) count++;
        str++;
    }
    return count;
}

// Reverse a string
void reverse_string(char *str) {
    int len = strlen(str);
    for (int i = 0; i < len / 2; i++) {
        char temp = str[i];
        str[i] = str[len - 1 - i];
        str[len - 1 - i] = temp;
    }
}

// Check if string is palindrome
int is_palindrome(const char *str) {
    int len = strlen(str);
    for (int i = 0; i < len / 2; i++) {
        if (str[i] != str[len - 1 - i]) {
            return 0;
        }
    }
    return 1;
}
```

### Dynamic String Allocation
```c
char *create_dynamic_string(const char *src) {
    char *dest = (char*)malloc(strlen(src) + 1);
    if (dest != NULL) {
        strcpy(dest, src);
    }
    return dest;
}

// Usage
char *dynamic_str = create_dynamic_string("Dynamic string");
if (dynamic_str != NULL) {
    printf("%s\n", dynamic_str);
    free(dynamic_str);  // Don't forget to free!
}
```

## String Arrays

### Array of Strings
```c
// Array of string literals
const char *days[] = {
    "Sunday", "Monday", "Tuesday", 
    "Wednesday", "Thursday", "Friday", "Saturday"
};

for (int i = 0; i < 7; i++) {
    printf("%s\n", days[i]);
}

// 2D array for modifiable strings
char names[5][50] = {
    "Alice", "Bob", "Charlie", "David", "Eve"
};

strcpy(names[2], "Christopher");  // Modify third name
```

## Common String Pitfalls

### Buffer Overflow
```c
// DANGEROUS - No bounds checking
char small[10];
strcpy(small, "This string is too long");  // Buffer overflow!

// SAFE - Use strncpy
strncpy(small, "This string is too long", sizeof(small) - 1);
small[sizeof(small) - 1] = '\0';
```

### Forgetting Null Terminator
```c
char str[5];
str[0] = 'H'; str[1] = 'e'; str[2] = 'l'; str[3] = 'l'; str[4] = 'o';
// Missing null terminator!

// Correct way
char str2[6] = {'H', 'e', 'l', 'l', 'o', '\0'};
```

## Best Practices
- Always allocate space for null terminator
- Use safer functions like strncpy, strncat
- Check return values of string functions
- Be careful with buffer sizes
- Initialize string arrays
- Use const for string literals that shouldn't be modified
- Consider using string libraries for complex operations
