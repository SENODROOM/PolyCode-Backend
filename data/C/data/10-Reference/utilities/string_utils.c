/*
 * File: string_utils.c
 * Description: Comprehensive string utility functions
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <stdbool.h>

// String validation functions
bool isEmpty(const char* str) {
    return str == NULL || strlen(str) == 0;
}

bool isNumeric(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isdigit((unsigned char)*str)) return false;
        str++;
    }
    return true;
}

bool isAlpha(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isalpha((unsigned char)*str)) return false;
        str++;
    }
    return true;
}

bool isAlphaNumeric(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isalnum((unsigned char)*str)) return false;
        str++;
    }
    return true;
}

// String manipulation functions
char* trimWhitespace(char* str) {
    if (str == NULL) return NULL;
    
    // Trim leading whitespace
    char* start = str;
    while (isspace((unsigned char)*start)) {
        start++;
    }
    
    // Trim trailing whitespace
    char* end = str + strlen(str) - 1;
    while (end > start && isspace((unsigned char)*end)) {
        end--;
    }
    
    // Write new null terminator
    *(end + 1) = '\0';
    
    // Move trimmed string to beginning if needed
    if (start != str) {
        memmove(str, start, strlen(start) + 1);
    }
    
    return str;
}

char* toLowerCase(char* str) {
    if (str == NULL) return NULL;
    
    for (int i = 0; str[i]; i++) {
        str[i] = tolower((unsigned char)str[i]);
    }
    return str;
}

char* toUpperCase(char* str) {
    if (str == NULL) return NULL;
    
    for (int i = 0; str[i]; i++) {
        str[i] = toupper((unsigned char)str[i]);
    }
    return str;
}

char* capitalizeWords(char* str) {
    if (str == NULL) return NULL;
    
    bool new_word = true;
    
    for (int i = 0; str[i]; i++) {
        if (isspace((unsigned char)str[i])) {
            new_word = true;
        } else if (new_word) {
            str[i] = toupper((unsigned char)str[i]);
            new_word = false;
        } else {
            str[i] = tolower((unsigned char)str[i]);
        }
    }
    return str;
}

// String comparison functions
bool startsWith(const char* str, const char* prefix) {
    if (str == NULL || prefix == NULL) return false;
    return strncmp(str, prefix, strlen(prefix)) == 0;
}

bool endsWith(const char* str, const char* suffix) {
    if (str == NULL || suffix == NULL) return false;
    
    size_t str_len = strlen(str);
    size_t suffix_len = strlen(suffix);
    
    if (suffix_len > str_len) return false;
    
    return strcmp(str + str_len - suffix_len, suffix) == 0;
}

bool contains(const char* str, const char* substr) {
    if (str == NULL || substr == NULL) return false;
    return strstr(str, substr) != NULL;
}

bool equalsIgnoreCase(const char* str1, const char* str2) {
    if (str1 == NULL || str2 == NULL) return str1 == str2;
    
    while (*str1 && *str2) {
        if (tolower((unsigned char)*str1) != tolower((unsigned char)*str2)) {
            return false;
        }
        str1++;
        str2++;
    }
    
    return *str1 == *str2;
}

// String transformation functions
char* reverseString(char* str) {
    if (str == NULL) return NULL;
    
    int length = strlen(str);
    for (int i = 0; i < length / 2; i++) {
        char temp = str[i];
        str[i] = str[length - 1 - i];
        str[length - 1 - i] = temp;
    }
    return str;
}

char* removeChars(char* str, const char* chars_to_remove) {
    if (str == NULL || chars_to_remove == NULL) return str;
    
    int write_index = 0;
    int read_index = 0;
    
    while (str[read_index]) {
        bool should_remove = false;
        
        for (int i = 0; chars_to_remove[i]; i++) {
            if (str[read_index] == chars_to_remove[i]) {
                should_remove = true;
                break;
            }
        }
        
        if (!should_remove) {
            str[write_index++] = str[read_index];
        }
        read_index++;
    }
    
    str[write_index] = '\0';
    return str;
}

char* replaceChar(char* str, char old_char, char new_char) {
    if (str == NULL) return str;
    
    for (int i = 0; str[i]; i++) {
        if (str[i] == old_char) {
            str[i] = new_char;
        }
    }
    return str;
}

char* padLeft(char* str, char pad_char, int total_length) {
    if (str == NULL || total_length <= strlen(str)) return str;
    
    int current_length = strlen(str);
    int pad_length = total_length - current_length;
    
    // Shift existing characters to the right
    for (int i = current_length; i >= 0; i--) {
        str[i + pad_length] = str[i];
    }
    
    // Add padding characters
    for (int i = 0; i < pad_length; i++) {
        str[i] = pad_char;
    }
    
    return str;
}

char* padRight(char* str, char pad_char, int total_length) {
    if (str == NULL || total_length <= strlen(str)) return str;
    
    int current_length = strlen(str);
    int pad_length = total_length - current_length;
    
    // Add padding characters
    for (int i = current_length; i < total_length; i++) {
        str[i] = pad_char;
    }
    
    str[total_length] = '\0';
    return str;
}

// String splitting and joining
int splitString(const char* str, char delimiter, char** parts, int max_parts) {
    if (str == NULL || parts == NULL || max_parts <= 0) return 0;
    
    int part_count = 0;
    const char* start = str;
    const char* current = str;
    
    while (*current && part_count < max_parts - 1) {
        if (*current == delimiter) {
            int length = current - start;
            parts[part_count] = (char*)malloc(length + 1);
            if (parts[part_count] != NULL) {
                strncpy(parts[part_count], start, length);
                parts[part_count][length] = '\0';
                part_count++;
            }
            start = current + 1;
        }
        current++;
    }
    
    // Add the last part
    if (part_count < max_parts) {
        int length = strlen(start);
        parts[part_count] = (char*)malloc(length + 1);
        if (parts[part_count] != NULL) {
            strcpy(parts[part_count], start);
            part_count++;
        }
    }
    
    return part_count;
}

char* joinStrings(char** parts, int count, const char* separator) {
    if (parts == NULL || count <= 0) return NULL;
    
    // Calculate total length needed
    int total_length = 0;
    int sep_length = (separator != NULL) ? strlen(separator) : 0;
    
    for (int i = 0; i < count; i++) {
        if (parts[i] != NULL) {
            total_length += strlen(parts[i]);
            if (i < count - 1) {
                total_length += sep_length;
            }
        }
    }
    
    char* result = (char*)malloc(total_length + 1);
    if (result == NULL) return NULL;
    
    result[0] = '\0';
    
    for (int i = 0; i < count; i++) {
        if (parts[i] != NULL) {
            strcat(result, parts[i]);
            if (i < count - 1 && separator != NULL) {
                strcat(result, separator);
            }
        }
    }
    
    return result;
}

// String analysis functions
int countChar(const char* str, char target) {
    if (str == NULL) return 0;
    
    int count = 0;
    while (*str) {
        if (*str == target) count++;
        str++;
    }
    return count;
}

int countWords(const char* str) {
    if (str == NULL) return 0;
    
    int count = 0;
    bool in_word = false;
    
    while (*str) {
        if (isspace((unsigned char)*str)) {
            in_word = false;
        } else if (!in_word) {
            in_word = true;
            count++;
        }
        str++;
    }
    
    return count;
}

int countOccurrences(const char* str, const char* substr) {
    if (str == NULL || substr == NULL) return 0;
    
    int count = 0;
    size_t substr_len = strlen(substr);
    const char* current = str;
    
    while ((current = strstr(current, substr)) != NULL) {
        count++;
        current += substr_len;
    }
    
    return count;
}

// Utility functions
char* leftSubstring(const char* str, int length) {
    if (str == NULL || length <= 0) return NULL;
    
    int str_len = strlen(str);
    if (length > str_len) length = str_len;
    
    char* result = (char*)malloc(length + 1);
    if (result == NULL) return NULL;
    
    strncpy(result, str, length);
    result[length] = '\0';
    
    return result;
}

char* rightSubstring(const char* str, int length) {
    if (str == NULL || length <= 0) return NULL;
    
    int str_len = strlen(str);
    if (length > str_len) length = str_len;
    
    char* result = (char*)malloc(length + 1);
    if (result == NULL) return NULL;
    
    strcpy(result, str + str_len - length);
    
    return result;
}

char* midSubstring(const char* str, int start, int length) {
    if (str == NULL || start < 0 || length <= 0) return NULL;
    
    int str_len = strlen(str);
    if (start >= str_len) return NULL;
    
    if (start + length > str_len) length = str_len - start;
    
    char* result = (char*)malloc(length + 1);
    if (result == NULL) return NULL;
    
    strncpy(result, str + start, length);
    result[length] = '\0';
    
    return result;
}

// Test function
void testStringUtils() {
    printf("=== String Utilities Test ===\n\n");
    
    // Test strings
    char test_str1[] = "  Hello World  ";
    char test_str2[] = "hello world programming";
    char test_str3[] = "12345";
    char test_str4[] = "abc123def";
    
    // Validation tests
    printf("1. Validation tests:\n");
    printf("   Is \"12345\" numeric: %s\n", isNumeric(test_str3) ? "Yes" : "No");
    printf("   Is \"hello\" empty: %s\n", isEmpty("hello") ? "Yes" : "No");
    printf("   Is \"abc123def\" alphanumeric: %s\n", isAlphaNumeric(test_str4) ? "Yes" : "No");
    
    // Manipulation tests
    printf("\n2. Manipulation tests:\n");
    printf("   Original: \"%s\"\n", test_str1);
    printf("   Trimmed: \"%s\"\n", trimWhitespace(strdup(test_str1)));
    printf("   Uppercase: \"%s\"\n", toUpperCase(strdup(test_str2)));
    printf("   Capitalized: \"%s\"\n", capitalizeWords(strdup(test_str2)));
    
    // Comparison tests
    printf("\n3. Comparison tests:\n");
    printf("   \"Hello\" starts with \"He\": %s\n", startsWith("Hello", "He") ? "Yes" : "No");
    printf("   \"World\" ends with \"ld\": %s\n", endsWith("World", "ld") ? "Yes" : "No");
    printf("   \"Hello World\" contains \"World\": %s\n", contains("Hello World", "World") ? "Yes" : "No");
    
    // Transformation tests
    printf("\n4. Transformation tests:\n");
    char rev_test[] = "Hello";
    printf("   Reverse \"Hello\": \"%s\"\n", reverseString(strdup(rev_test)));
    
    char remove_test[] = "Hello, World!";
    printf("   Remove punctuation from \"%s\": \"%s\"\n", remove_test, removeChars(strdup(remove_test), ",!"));
    
    // Analysis tests
    printf("\n5. Analysis tests:\n");
    printf("   Count 'l' in \"Hello\": %d\n", countChar("Hello", 'l'));
    printf("   Count words in \"Hello World Programming\": %d\n", countWords("Hello World Programming"));
    printf("   Count \"test\" in \"test test test\": %d\n", countOccurrences("test test test", "test"));
    
    // Substring tests
    printf("\n6. Substring tests:\n");
    char* left = leftSubstring("Hello World", 5);
    char* right = rightSubstring("Hello World", 5);
    char* mid = midSubstring("Hello World", 2, 3);
    
    printf("   Left 5 chars of \"Hello World\": \"%s\"\n", left);
    printf("   Right 5 chars of \"Hello World\": \"%s\"\n", right);
    printf("   Middle 3 chars from pos 2: \"%s\"\n", mid);
    
    free(left);
    free(right);
    free(mid);
    
    printf("\n=== String utilities test completed ===\n");
}

int main() {
    testStringUtils();
    return 0;
}
