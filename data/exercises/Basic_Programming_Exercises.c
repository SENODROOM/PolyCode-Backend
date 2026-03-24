#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <ctype.h>

// Exercise 1: Factorial Calculator
long long factorial(int n) {
    if (n < 0) return -1; // Error for negative numbers
    if (n == 0 || n == 1) return 1;
    
    long long result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

// Exercise 2: Fibonacci Sequence
void fibonacci(int n) {
    if (n <= 0) return;
    
    long long a = 0, b = 1;
    printf("Fibonacci sequence (first %d terms): ", n);
    
    for (int i = 0; i < n; i++) {
        printf("%lld ", a);
        long long next = a + b;
        a = b;
        b = next;
    }
    printf("\n");
}

// Exercise 3: Prime Number Checker
int isPrime(int n) {
    if (n <= 1) return 0;
    if (n <= 3) return 1;
    if (n % 2 == 0 || n % 3 == 0) return 0;
    
    for (int i = 5; i * i <= n; i += 6) {
        if (n % i == 0 || n % (i + 2) == 0) {
            return 0;
        }
    }
    return 1;
}

// Exercise 4: Palindrome Checker
int isPalindrome(const char *str) {
    int left = 0;
    int right = strlen(str) - 1;
    
    while (left < right) {
        if (str[left] != str[right]) {
            return 0;
        }
        left++;
        right--;
    }
    return 1;
}

// Exercise 5: Array Reversal
void reverseArray(int arr[], int size) {
    for (int i = 0; i < size / 2; i++) {
        int temp = arr[i];
        arr[i] = arr[size - 1 - i];
        arr[size - 1 - i] = temp;
    }
}

// Exercise 6: String Length Calculator
int stringLength(const char *str) {
    int length = 0;
    while (str[length] != '\0') {
        length++;
    }
    return length;
}

// Exercise 7: Greatest Common Divisor (GCD)
int gcd(int a, int b) {
    while (b != 0) {
        int temp = b;
        b = a % b;
        a = temp;
    }
    return a;
}

// Exercise 8: Sum of Digits
int sumOfDigits(int n) {
    int sum = 0;
    n = abs(n); // Handle negative numbers
    
    while (n > 0) {
        sum += n % 10;
        n /= 10;
    }
    return sum;
}

// Exercise 9: Power Function
double power(double base, int exponent) {
    if (exponent == 0) return 1.0;
    if (exponent < 0) {
        return 1.0 / power(base, -exponent);
    }
    
    double result = 1.0;
    for (int i = 0; i < exponent; i++) {
        result *= base;
    }
    return result;
}

// Exercise 10: Binary to Decimal Conversion
int binaryToDecimal(const char *binary) {
    int decimal = 0;
    int length = strlen(binary);
    
    for (int i = 0; i < length; i++) {
        if (binary[i] == '1') {
            decimal += power(2, length - 1 - i);
        } else if (binary[i] != '0') {
            return -1; // Invalid binary number
        }
    }
    return decimal;
}

// Exercise 11: Decimal to Binary Conversion
void decimalToBinary(int n, char *binary) {
    if (n == 0) {
        strcpy(binary, "0");
        return;
    }
    
    int index = 0;
    int temp = n;
    
    // Calculate binary digits in reverse
    while (temp > 0) {
        binary[index++] = (temp % 2) + '0';
        temp /= 2;
    }
    binary[index] = '\0';
    
    // Reverse the string
    for (int i = 0; i < index / 2; i++) {
        char tempChar = binary[i];
        binary[i] = binary[index - 1 - i];
        binary[index - 1 - i] = tempChar;
    }
}

// Exercise 12: Vowel Counter
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

// Helper function to print array
void printArray(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
}

int main() {
    printf("Basic Programming Exercises\n\n");
    
    // Exercise 1: Factorial
    printf("Exercise 1: Factorial\n");
    int num = 5;
    printf("Factorial of %d = %lld\n\n", num, factorial(num));
    
    // Exercise 2: Fibonacci
    printf("Exercise 2: Fibonacci\n");
    fibonacci(10);
    printf("\n");
    
    // Exercise 3: Prime Number
    printf("Exercise 3: Prime Number Check\n");
    int primeNum = 17;
    printf("%d is %sprime\n\n", primeNum, isPrime(primeNum) ? "" : "not ");
    
    // Exercise 4: Palindrome
    printf("Exercise 4: Palindrome Check\n");
    const char *palindromeStr = "racecar";
    printf("\"%s\" is %sa palindrome\n\n", palindromeStr, isPalindrome(palindromeStr) ? "" : "not ");
    
    // Exercise 5: Array Reversal
    printf("Exercise 5: Array Reversal\n");
    int arr[] = {1, 2, 3, 4, 5};
    int size = sizeof(arr) / sizeof(arr[0]);
    printf("Original array: ");
    printArray(arr, size);
    reverseArray(arr, size);
    printf("Reversed array: ");
    printArray(arr, size);
    printf("\n");
    
    // Exercise 6: String Length
    printf("Exercise 6: String Length\n");
    const char *testStr = "Hello, World!";
    printf("Length of \"%s\" = %d\n\n", testStr, stringLength(testStr));
    
    // Exercise 7: GCD
    printf("Exercise 7: Greatest Common Divisor\n");
    int a = 48, b = 18;
    printf("GCD of %d and %d = %d\n\n", a, b, gcd(a, b));
    
    // Exercise 8: Sum of Digits
    printf("Exercise 8: Sum of Digits\n");
    int digitNum = 12345;
    printf("Sum of digits in %d = %d\n\n", digitNum, sumOfDigits(digitNum));
    
    // Exercise 9: Power Function
    printf("Exercise 9: Power Function\n");
    double base = 2.0;
    int exp = 8;
    printf("%.1f^%d = %.1f\n\n", base, exp, power(base, exp));
    
    // Exercise 10: Binary to Decimal
    printf("Exercise 10: Binary to Decimal\n");
    const char *binary = "1010";
    int decimal = binaryToDecimal(binary);
    printf("Binary %s = Decimal %d\n\n", binary, decimal);
    
    // Exercise 11: Decimal to Binary
    printf("Exercise 11: Decimal to Binary\n");
    int decNum = 10;
    char binaryResult[32];
    decimalToBinary(decNum, binaryResult);
    printf("Decimal %d = Binary %s\n\n", decNum, binaryResult);
    
    // Exercise 12: Vowel Counter
    printf("Exercise 12: Vowel Counter\n");
    const char *vowelStr = "Programming in C";
    printf("Vowels in \"%s\" = %d\n\n", vowelStr, countVowels(vowelStr));
    
    return 0;
}
