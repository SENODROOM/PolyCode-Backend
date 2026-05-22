#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>

// Example 1: String Length (Manual Implementation)
int stringLength(const char *str) {
    int length = 0;
    while (str[length] != '\0') {
        length++;
    }
    return length;
}

// Example 2: String Copy (Manual Implementation)
void stringCopy(char *dest, const char *src) {
    int i = 0;
    while (src[i] != '\0') {
        dest[i] = src[i];
        i++;
    }
    dest[i] = '\0'; // Add null terminator
}

// Example 3: String Concatenation (Manual Implementation)
void stringConcat(char *dest, const char *src) {
    int destLen = stringLength(dest);
    int i = 0;
    while (src[i] != '\0') {
        dest[destLen + i] = src[i];
        i++;
    }
    dest[destLen + i] = '\0'; // Add null terminator
}

// Example 4: String Comparison (Manual Implementation)
int stringCompare(const char *str1, const char *str2) {
    int i = 0;
    while (str1[i] != '\0' && str2[i] != '\0') {
        if (str1[i] != str2[i]) {
            return str1[i] - str2[i];
        }
        i++;
    }
    return str1[i] - str2[i];
}

// Example 5: String Reverse
void stringReverse(char *str) {
    int length = stringLength(str);
    for (int i = 0; i < length / 2; i++) {
        char temp = str[i];
        str[i] = str[length - 1 - i];
        str[length - 1 - i] = temp;
    }
}

// Example 6: Case Conversion - To Uppercase
void toUpperCase(char *str) {
    for (int i = 0; str[i] != '\0'; i++) {
        str[i] = toupper(str[i]);
    }
}

// Example 7: Case Conversion - To Lowercase
void toLowerCase(char *str) {
    for (int i = 0; str[i] != '\0'; i++) {
        str[i] = tolower(str[i]);
    }
}

// Example 8: Count Vowels
int countVowels(const char *str) {
    int count = 0;
    for (int i = 0; str[i] != '\0'; i++) {
        char c = tolower(str[i]);
        if (c == 'a' || c == 'e' || c == 'i' || c == 'o' || c == 'u') {
            count++;
        }
    }
    return count;
}

// Example 9: Count Words
int countWords(const char *str) {
    int count = 0;
    int inWord = 0;
    
    for (int i = 0; str[i] != '\0'; i++) {
        if (!isspace(str[i]) && !inWord) {
            count++;
            inWord = 1;
        } else if (isspace(str[i])) {
            inWord = 0;
        }
    }
    return count;
}

// Example 10: Remove Spaces
void removeSpaces(char *str) {
    int writeIndex = 0;
    for (int i = 0; str[i] != '\0'; i++) {
        if (!isspace(str[i])) {
            str[writeIndex++] = str[i];
        }
    }
    str[writeIndex] = '\0';
}

// Example 11: Find Substring
int findSubstring(const char *str, const char *substr) {
    int strLen = stringLength(str);
    int subLen = stringLength(substr);
    
    for (int i = 0; i <= strLen - subLen; i++) {
        int j;
        for (j = 0; j < subLen; j++) {
            if (str[i + j] != substr[j]) {
                break;
            }
        }
        if (j == subLen) {
            return i; // Found at position i
        }
    }
    return -1; // Not found
}

// Example 12: Replace Character
void replaceChar(char *str, char oldChar, char newChar) {
    for (int i = 0; str[i] != '\0'; i++) {
        if (str[i] == oldChar) {
            str[i] = newChar;
        }
    }
}

// Example 13: Check Palindrome
int isPalindrome(const char *str) {
    int left = 0;
    int right = stringLength(str) - 1;
    
    while (left < right) {
        if (tolower(str[left]) != tolower(str[right])) {
            return 0; // Not palindrome
        }
        left++;
        right--;
    }
    return 1; // Is palindrome
}

// Example 14: Trim Leading and Trailing Spaces
void trim(char *str) {
    int start = 0;
    int end = stringLength(str) - 1;
    
    // Find first non-space character
    while (start <= end && isspace(str[start])) {
        start++;
    }
    
    // Find last non-space character
    while (end >= start && isspace(str[end])) {
        end--;
    }
    
    // Shift characters
    int writeIndex = 0;
    for (int i = start; i <= end; i++) {
        str[writeIndex++] = str[i];
    }
    str[writeIndex] = '\0';
}

// Example 15: String Tokenizer (Simple)
void tokenize(const char *str, char delimiter) {
    printf("Tokens separated by '%c':\n", delimiter);
    
    int start = 0;
    int length = stringLength(str);
    
    for (int i = 0; i <= length; i++) {
        if (str[i] == delimiter || str[i] == '\0') {
            // Print token
            for (int j = start; j < i; j++) {
                printf("%c", str[j]);
            }
            printf("\n");
            start = i + 1;
        }
    }
}

int main() {
    printf("String Operations Examples\n\n");
    
    // Example 1: String Length
    printf("Example 1: String Length\n");
    const char *testStr = "Hello, World!";
    printf("Length of \"%s\" = %d\n\n", testStr, stringLength(testStr));
    
    // Example 2: String Copy
    printf("Example 2: String Copy\n");
    char dest[50];
    stringCopy(dest, testStr);
    printf("Copied string: \"%s\"\n\n", dest);
    
    // Example 3: String Concatenation
    printf("Example 3: String Concatenation\n");
    char concat[100] = "Hello";
    stringConcat(concat, ", C Programming!");
    printf("Concatenated: \"%s\"\n\n", concat);
    
    // Example 4: String Comparison
    printf("Example 4: String Comparison\n");
    const char *str1 = "Apple";
    const char *str2 = "Banana";
    int cmp = stringCompare(str1, str2);
    printf("Comparing \"%s\" and \"%s\": %d\n\n", str1, str2, cmp);
    
    // Example 5: String Reverse
    printf("Example 5: String Reverse\n");
    char reverseStr[] = "Programming";
    printf("Original: \"%s\"\n", reverseStr);
    stringReverse(reverseStr);
    printf("Reversed: \"%s\"\n\n", reverseStr);
    
    // Example 6: To Uppercase
    printf("Example 6: To Uppercase\n");
    char upperStr[] = "hello world";
    printf("Original: \"%s\"\n", upperStr);
    toUpperCase(upperStr);
    printf("Uppercase: \"%s\"\n\n", upperStr);
    
    // Example 7: To Lowercase
    printf("Example 7: To Lowercase\n");
    char lowerStr[] = "HELLO WORLD";
    printf("Original: \"%s\"\n", lowerStr);
    toLowerCase(lowerStr);
    printf("Lowercase: \"%s\"\n\n", lowerStr);
    
    // Example 8: Count Vowels
    printf("Example 8: Count Vowels\n");
    const char *vowelStr = "Programming in C";
    printf("Vowels in \"%s\": %d\n\n", vowelStr, countVowels(vowelStr));
    
    // Example 9: Count Words
    printf("Example 9: Count Words\n");
    const char *wordStr = "This is a sample sentence";
    printf("Words in \"%s\": %d\n\n", wordStr, countWords(wordStr));
    
    // Example 10: Remove Spaces
    printf("Example 10: Remove Spaces\n");
    char spaceStr[] = "H e l l o W o r l d";
    printf("Original: \"%s\"\n", spaceStr);
    removeSpaces(spaceStr);
    printf("Without spaces: \"%s\"\n\n", spaceStr);
    
    // Example 11: Find Substring
    printf("Example 11: Find Substring\n");
    const char *mainStr = "Hello World, Welcome to C";
    const char *subStr = "World";
    int pos = findSubstring(mainStr, subStr);
    if (pos != -1) {
        printf("Substring \"%s\" found at position %d in \"%s\"\n\n", subStr, pos, mainStr);
    } else {
        printf("Substring \"%s\" not found\n\n", subStr);
    }
    
    // Example 12: Replace Character
    printf("Example 12: Replace Character\n");
    char replaceStr[] = "Hello World";
    printf("Original: \"%s\"\n", replaceStr);
    replaceChar(replaceStr, 'o', '*');
    printf("After replacing 'o' with '*': \"%s\"\n\n", replaceStr);
    
    // Example 13: Check Palindrome
    printf("Example 13: Check Palindrome\n");
    const char *pal1 = "racecar";
    const char *pal2 = "hello";
    printf("\"%s\" is %sa palindrome\n", pal1, isPalindrome(pal1) ? "" : "not ");
    printf("\"%s\" is %sa palindrome\n\n", pal2, isPalindrome(pal2) ? "" : "not ");
    
    // Example 14: Trim Spaces
    printf("Example 14: Trim Spaces\n");
    char trimStr[] = "   Hello World   ";
    printf("Original: \"%s\"\n", trimStr);
    trim(trimStr);
    printf("Trimmed: \"%s\"\n\n", trimStr);
    
    // Example 15: String Tokenizer
    printf("Example 15: String Tokenizer\n");
    const char *tokenStr = "C,Programming,Is,Fun";
    tokenize(tokenStr, ',');
    printf("\n");
    
    return 0;
}
