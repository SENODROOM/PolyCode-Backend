/*
 * File: string_exercises.c
 * Description: Collection of string manipulation exercises
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <stdbool.h>

// Exercise 1: Count words in a string
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

// Exercise 2: Reverse words in a string
void reverseWords(char* str) {
    if (str == NULL) return;
    
    int length = strlen(str);
    
    // Reverse the entire string
    for (int i = 0; i < length / 2; i++) {
        char temp = str[i];
        str[i] = str[length - 1 - i];
        str[length - 1 - i] = temp;
    }
    
    // Reverse each word
    int start = 0;
    for (int i = 0; i <= length; i++) {
        if (str[i] == ' ' || str[i] == '\0') {
            for (int j = start, k = i - 1; j < k; j++, k--) {
                char temp = str[j];
                str[j] = str[k];
                str[k] = temp;
            }
            start = i + 1;
        }
    }
}

// Exercise 3: Check if string is anagram
bool isAnagram(const char* str1, const char* str2) {
    if (str1 == NULL || str2 == NULL) return false;
    
    int len1 = strlen(str1);
    int len2 = strlen(str2);
    
    if (len1 != len2) return false;
    
    int count[256] = {0};
    
    // Count characters in first string
    for (int i = 0; i < len1; i++) {
        count[(unsigned char)str1[i]]++;
    }
    
    // Subtract characters from second string
    for (int i = 0; i < len2; i++) {
        count[(unsigned char)str2[i]]--;
    }
    
    // Check if all counts are zero
    for (int i = 0; i < 256; i++) {
        if (count[i] != 0) return false;
    }
    
    return true;
}

// Exercise 4: Remove all spaces from string
void removeSpaces(char* str) {
    if (str == NULL) return;
    
    int write_index = 0;
    int read_index = 0;
    
    while (str[read_index]) {
        if (!isspace((unsigned char)str[read_index])) {
            str[write_index++] = str[read_index];
        }
        read_index++;
    }
    
    str[write_index] = '\0';
}

// Exercise 5: Convert to title case
void toTitleCase(char* str) {
    if (str == NULL) return;
    
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
}

// Exercise 6: Find longest word in string
char* findLongestWord(const char* str) {
    if (str == NULL) return NULL;
    
    static char longest_word[100];
    int max_length = 0;
    int current_length = 0;
    int word_start = 0;
    
    for (int i = 0; str[i]; i++) {
        if (isspace((unsigned char)str[i]) || str[i] == '\0') {
            if (current_length > max_length) {
                max_length = current_length;
                strncpy(longest_word, str + word_start, current_length);
                longest_word[current_length] = '\0';
            }
            current_length = 0;
            word_start = i + 1;
        } else {
            current_length++;
        }
    }
    
    // Check last word
    if (current_length > max_length) {
        strncpy(longest_word, str + word_start, current_length);
        longest_word[current_length] = '\0';
    }
    
    return longest_word;
}

// Exercise 7: Check if string is palindrome
bool isPalindrome(const char* str) {
    if (str == NULL) return false;
    
    int left = 0;
    int right = strlen(str) - 1;
    
    while (left < right) {
        // Skip non-alphanumeric characters
        while (left < right && !isalnum((unsigned char)str[left])) {
            left++;
        }
        while (left < right && !isalnum((unsigned char)str[right])) {
            right--;
        }
        
        if (tolower((unsigned char)str[left]) != tolower((unsigned char)str[right])) {
            return false;
        }
        
        left++;
        right--;
    }
    
    return true;
}

// Exercise 8: Count vowels and consonants
void countVowelsConsonants(const char* str, int* vowels, int* consonants) {
    if (str == NULL || vowels == NULL || consonants == NULL) return;
    
    *vowels = 0;
    *consonants = 0;
    
    while (*str) {
        char c = tolower((unsigned char)*str);
        
        if (isalpha((unsigned char)c)) {
            if (c == 'a' || c == 'e' || c == 'i' || c == 'o' || c == 'u') {
                (*vowels)++;
            } else {
                (*consonants)++;
            }
        }
        str++;
    }
}

// Exercise 9: Replace substring
void replaceSubstring(char* str, const char* old_sub, const char* new_sub) {
    if (str == NULL || old_sub == NULL || new_sub == NULL) return;
    
    int str_len = strlen(str);
    int old_len = strlen(old_sub);
    int new_len = strlen(new_sub);
    
    if (old_len == 0) return;
    
    char* result = (char*)malloc(str_len + 100); // Extra space for replacement
    if (result == NULL) return;
    
    int i = 0, j = 0;
    
    while (i < str_len) {
        if (strncmp(str + i, old_sub, old_len) == 0) {
            strcpy(result + j, new_sub);
            i += old_len;
            j += new_len;
        } else {
            result[j++] = str[i++];
        }
    }
    
    result[j] = '\0';
    strcpy(str, result);
    free(result);
}

// Exercise 10: Extract numbers from string
void extractNumbers(const char* str, int* numbers, int* count) {
    if (str == NULL || numbers == NULL || count == NULL) return;
    
    *count = 0;
    int current_num = 0;
    bool in_number = false;
    
    for (int i = 0; str[i]; i++) {
        if (isdigit((unsigned char)str[i])) {
            current_num = current_num * 10 + (str[i] - '0');
            in_number = true;
        } else if (in_number) {
            if (*count < 100) { // Prevent buffer overflow
                numbers[(*count)++] = current_num;
            }
            current_num = 0;
            in_number = false;
        }
    }
    
    // Add last number if string ends with digit
    if (in_number && *count < 100) {
        numbers[(*count)++] = current_num;
    }
}

// Helper function to print string with quotes
void printString(const char* str) {
    printf("\"%s\"", str);
}

// Test function
void runStringExercises() {
    printf("=== String Exercises ===\n\n");
    
    // Test strings
    char test_str1[] = "Hello World Programming in C";
    char test_str2[] = "racecar";
    char test_str3[] = "A man a plan a canal Panama";
    char test_str4[] = "The quick brown fox jumps over the lazy dog";
    char test_str5[] = "abc123def45ghi6";
    
    // Exercise 1: Count words
    int word_count = countWords(test_str1);
    printf("1. Word count in ");
    printString(test_str1);
    printf(": %d\n", word_count);
    
    // Exercise 2: Reverse words
    char rev_words[] = "Hello World Programming";
    printf("\n2. Reverse words:\n");
    printf("   Original: ");
    printString(rev_words);
    printf("\n");
    reverseWords(rev_words);
    printf("   Reversed: ");
    printString(rev_words);
    printf("\n");
    
    // Exercise 3: Anagram check
    const char* str1 = "listen";
    const char* str2 = "silent";
    printf("\n3. Anagram check:\n");
    printf("   ");
    printString(str1);
    printf(" and ");
    printString(str2);
    printf(" are anagrams: %s\n", isAnagram(str1, str2) ? "Yes" : "No");
    
    // Exercise 4: Remove spaces
    char no_spaces[] = "Hello World C Programming";
    printf("\n4. Remove spaces:\n");
    printf("   Original: ");
    printString(no_spaces);
    printf("\n");
    removeSpaces(no_spaces);
    printf("   Without spaces: ");
    printString(no_spaces);
    printf("\n");
    
    // Exercise 5: Title case
    char title_case[] = "hello world programming in c";
    printf("\n5. Title case:\n");
    printf("   Original: ");
    printString(title_case);
    printf("\n");
    toTitleCase(title_case);
    printf("   Title case: ");
    printString(title_case);
    printf("\n");
    
    // Exercise 6: Longest word
    printf("\n6. Longest word in ");
    printString(test_str4);
    printf(": %s\n", findLongestWord(test_str4));
    
    // Exercise 7: Palindrome check
    printf("\n7. Palindrome check:\n");
    printf("   ");
    printString(test_str2);
    printf(" is palindrome: %s\n", isPalindrome(test_str2) ? "Yes" : "No");
    printf("   ");
    printString(test_str3);
    printf(" is palindrome: %s\n", isPalindrome(test_str3) ? "Yes" : "No");
    
    // Exercise 8: Count vowels and consonants
    int vowels, consonants;
    countVowelsConsonants(test_str1, &vowels, &consonants);
    printf("\n8. Vowels and consonants in ");
    printString(test_str1);
    printf(":\n");
    printf("   Vowels: %d, Consonants: %d\n", vowels, consonants);
    
    // Exercise 9: Replace substring
    char replace_test[] = "Hello World, Hello Universe";
    printf("\n9. Replace substring:\n");
    printf("   Original: ");
    printString(replace_test);
    printf("\n");
    replaceSubstring(replace_test, "Hello", "Hi");
    printf("   After replacing \"Hello\" with \"Hi\": ");
    printString(replace_test);
    printf("\n");
    
    // Exercise 10: Extract numbers
    int numbers[100];
    int num_count;
    extractNumbers(test_str5, numbers, &num_count);
    printf("\n10. Extract numbers from ");
    printString(test_str5);
    printf(":\n");
    printf("    Numbers found: ");
    for (int i = 0; i < num_count; i++) {
        printf("%d", numbers[i]);
        if (i < num_count - 1) printf(", ");
    }
    printf("\n");
    
    printf("\n=== All string exercises completed ===\n");
}

int main() {
    runStringExercises();
    return 0;
}
