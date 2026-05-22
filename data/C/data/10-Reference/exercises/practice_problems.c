/*
 * File: practice_problems.c
 * Description: Collection of C programming exercises
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>

// Exercise 1: Find the factorial of a number
long long factorial(int n) {
    if (n < 0) return -1; // Error case
    if (n == 0 || n == 1) return 1;
    
    long long result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

// Exercise 2: Check if a number is prime
int isPrime(int n) {
    if (n <= 1) return 0;
    if (n == 2) return 1;
    if (n % 2 == 0) return 0;
    
    for (int i = 3; i * i <= n; i += 2) {
        if (n % i == 0) return 0;
    }
    return 1;
}

// Exercise 3: Reverse a string
void reverseString(char* str) {
    if (str == NULL) return;
    
    int length = strlen(str);
    for (int i = 0; i < length / 2; i++) {
        char temp = str[i];
        str[i] = str[length - 1 - i];
        str[length - 1 - i] = temp;
    }
}

// Exercise 4: Find the largest element in an array
int findMax(int arr[], int size) {
    if (arr == NULL || size <= 0) return -1;
    
    int max = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    return max;
}

// Exercise 5: Count vowels in a string
int countVowels(const char* str) {
    if (str == NULL) return 0;
    
    int count = 0;
    while (*str) {
        char c = tolower(*str);
        if (c == 'a' || c == 'e' || c == 'i' || c == 'o' || c == 'u') {
            count++;
        }
        str++;
    }
    return count;
}

// Exercise 6: Check if a string is a palindrome
int isPalindrome(const char* str) {
    if (str == NULL) return 0;
    
    int left = 0;
    int right = strlen(str) - 1;
    
    while (left < right) {
        if (tolower(str[left]) != tolower(str[right])) {
            return 0;
        }
        left++;
        right--;
    }
    return 1;
}

// Exercise 7: Sum of digits of a number
int sumOfDigits(int n) {
    int sum = 0;
    n = abs(n); // Handle negative numbers
    
    while (n > 0) {
        sum += n % 10;
        n /= 10;
    }
    return sum;
}

// Exercise 8: Fibonacci sequence
void fibonacci(int n) {
    if (n <= 0) return;
    
    int a = 0, b = 1;
    printf("Fibonacci sequence (first %d terms): ", n);
    
    for (int i = 0; i < n; i++) {
        printf("%d ", a);
        int next = a + b;
        a = b;
        b = next;
    }
    printf("\n");
}

// Test function
void runExercises() {
    printf("=== C Programming Exercises ===\n\n");
    
    // Exercise 1: Factorial
    int num = 5;
    printf("Exercise 1: Factorial of %d = %lld\n", num, factorial(num));
    
    // Exercise 2: Prime check
    num = 17;
    printf("Exercise 2: Is %d prime? %s\n", num, isPrime(num) ? "Yes" : "No");
    
    // Exercise 3: Reverse string
    char str[] = "Hello World";
    printf("Exercise 3: Original string: %s\n", str);
    reverseString(str);
    printf("Exercise 3: Reversed string: %s\n", str);
    
    // Exercise 4: Find maximum in array
    int arr[] = {3, 7, 1, 9, 2, 5};
    int size = sizeof(arr) / sizeof(arr[0]);
    printf("Exercise 4: Maximum in array: %d\n", findMax(arr, size));
    
    // Exercise 5: Count vowels
    const char* text = "Programming in C";
    printf("Exercise 5: Vowels in '%s': %d\n", text, countVowels(text));
    
    // Exercise 6: Palindrome check
    const char* palindrome = "racecar";
    printf("Exercise 6: Is '%s' a palindrome? %s\n", palindrome, isPalindrome(palindrome) ? "Yes" : "No");
    
    // Exercise 7: Sum of digits
    num = 12345;
    printf("Exercise 7: Sum of digits of %d = %d\n", num, sumOfDigits(num));
    
    // Exercise 8: Fibonacci
    fibonacci(10);
    
    printf("\n=== All exercises completed ===\n");
}

int main() {
    runExercises();
    return 0;
}
