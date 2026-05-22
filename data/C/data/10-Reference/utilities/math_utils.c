/*
 * File: math_utils.c
 * Description: Mathematical utility functions for C programming
 */

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <time.h>
#include <stdbool.h>

// Basic number utilities
bool isEven(int num) {
    return num % 2 == 0;
}

bool isOdd(int num) {
    return num % 2 != 0;
}

bool isPrime(int num) {
    if (num <= 1) return false;
    if (num == 2) return true;
    if (num % 2 == 0) return false;
    
    for (int i = 3; i * i <= num; i += 2) {
        if (num % i == 0) return false;
    }
    return true;
}

int gcd(int a, int b) {
    while (b != 0) {
        int temp = b;
        b = a % b;
        a = temp;
    }
    return a;
}

int lcm(int a, int b) {
    if (a == 0 || b == 0) return 0;
    return abs(a * b) / gcd(a, b);
}

// Power and factorial functions
long long power(int base, int exponent) {
    if (exponent < 0) return -1; // Not supported for integers
    
    long long result = 1;
    for (int i = 0; i < exponent; i++) {
        result *= base;
    }
    return result;
}

long long factorial(int n) {
    if (n < 0) return -1;
    if (n == 0 || n == 1) return 1;
    
    long long result = 1;
    for (int i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

int fibonacci(int n) {
    if (n <= 0) return 0;
    if (n == 1) return 1;
    
    int a = 0, b = 1, c;
    for (int i = 2; i <= n; i++) {
        c = a + b;
        a = b;
        b = c;
    }
    return b;
}

// Number system conversions
int decimalToBinary(int decimal) {
    if (decimal == 0) return 0;
    
    int binary = 0;
    int place = 1;
    
    while (decimal > 0) {
        binary += (decimal % 2) * place;
        decimal /= 2;
        place *= 10;
    }
    
    return binary;
}

int binaryToDecimal(int binary) {
    int decimal = 0;
    int place = 0;
    
    while (binary > 0) {
        decimal += (binary % 10) * power(2, place);
        binary /= 10;
        place++;
    }
    
    return decimal;
}

char* decimalToHex(int decimal) {
    static char hex[20];
    char hex_chars[] = "0123456789ABCDEF";
    int index = 0;
    
    if (decimal == 0) {
        hex[0] = '0';
        hex[1] = '\0';
        return hex;
    }
    
    while (decimal > 0) {
        hex[index++] = hex_chars[decimal % 16];
        decimal /= 16;
    }
    
    hex[index] = '\0';
    
    // Reverse the string
    for (int i = 0; i < index / 2; i++) {
        char temp = hex[i];
        hex[i] = hex[index - 1 - i];
        hex[index - 1 - i] = temp;
    }
    
    return hex;
}

// Array statistics
int findMin(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0;
    
    int min = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] < min) {
            min = arr[i];
        }
    }
    return min;
}

int findMax(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0;
    
    int max = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    return max;
}

double calculateAverage(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0.0;
    
    long long sum = 0;
    for (int i = 0; i < size; i++) {
        sum += arr[i];
    }
    
    return (double)sum / size;
}

double calculateMedian(int arr[], int size) {
    if (arr == NULL || size <= 0) return 0.0;
    
    // Sort array (simple bubble sort for demonstration)
    for (int i = 0; i < size - 1; i++) {
        for (int j = 0; j < size - i - 1; j++) {
            if (arr[j] > arr[j + 1]) {
                int temp = arr[j];
                arr[j] = arr[j + 1];
                arr[j + 1] = temp;
            }
        }
    }
    
    if (size % 2 == 0) {
        return (arr[size / 2 - 1] + arr[size / 2]) / 2.0;
    } else {
        return arr[size / 2];
    }
}

// Random number utilities
void seedRandom() {
    srand(time(NULL));
}

int getRandomInt(int min, int max) {
    if (min > max) {
        int temp = min;
        min = max;
        max = temp;
    }
    return min + rand() % (max - min + 1);
}

double getRandomDouble(double min, double max) {
    if (min > max) {
        double temp = min;
        min = max;
        max = temp;
    }
    double random = ((double)rand() / RAND_MAX);
    return min + random * (max - min);
}

bool getRandomProbability(double probability) {
    if (probability < 0.0) probability = 0.0;
    if (probability > 1.0) probability = 1.0;
    
    return ((double)rand() / RAND_MAX) < probability;
}

// Geometry utilities
double degreesToRadians(double degrees) {
    return degrees * M_PI / 180.0;
}

double radiansToDegrees(double radians) {
    return radians * 180.0 / M_PI;
}

double calculateDistance(double x1, double y1, double x2, double y2) {
    double dx = x2 - x1;
    double dy = y2 - y1;
    return sqrt(dx * dx + dy * dy);
}

double calculateCircleArea(double radius) {
    return M_PI * radius * radius;
}

double calculateCircleCircumference(double radius) {
    return 2 * M_PI * radius;
}

// Advanced math functions
bool isPerfectSquare(int num) {
    if (num < 0) return false;
    
    int root = (int)sqrt(num);
    return root * root == num;
}

bool isPerfectNumber(int num) {
    if (num <= 1) return false;
    
    int sum = 1; // 1 is a proper divisor for all numbers > 1
    
    for (int i = 2; i * i <= num; i++) {
        if (num % i == 0) {
            sum += i;
            if (i != num / i) {
                sum += num / i;
            }
        }
    }
    
    return sum == num;
}

int sumOfDigits(int num) {
    num = abs(num);
    int sum = 0;
    
    while (num > 0) {
        sum += num % 10;
        num /= 10;
    }
    
    return sum;
}

int reverseNumber(int num) {
    num = abs(num);
    int reversed = 0;
    
    while (num > 0) {
        reversed = reversed * 10 + num % 10;
        num /= 10;
    }
    
    return reversed;
}

bool isPalindrome(int num) {
    return num == reverseNumber(num);
}

// Utility functions
void printNumberProperties(int num) {
    printf("Properties of %d:\n", num);
    printf("  Even: %s\n", isEven(num) ? "Yes" : "No");
    printf("  Odd: %s\n", isOdd(num) ? "Yes" : "No");
    printf("  Prime: %s\n", isPrime(num) ? "Yes" : "No");
    printf("  Perfect Square: %s\n", isPerfectSquare(num) ? "Yes" : "No");
    printf("  Perfect Number: %s\n", isPerfectNumber(num) ? "Yes" : "No");
    printf("  Palindrome: %s\n", isPalindrome(num) ? "Yes" : "No");
    printf("  Sum of digits: %d\n", sumOfDigits(num));
    printf("  Reversed: %d\n", reverseNumber(num));
}

void printArrayStats(int arr[], int size) {
    if (arr == NULL || size <= 0) {
        printf("Invalid array\n");
        return;
    }
    
    printf("Array Statistics:\n");
    printf("  Size: %d\n", size);
    printf("  Min: %d\n", findMin(arr, size));
    printf("  Max: %d\n", findMax(arr, size));
    printf("  Average: %.2f\n", calculateAverage(arr, size));
    printf("  Median: %.2f\n", calculateMedian(arr, size));
}

// Test function
void testMathUtils() {
    printf("=== Math Utilities Test ===\n\n");
    
    // Seed random number generator
    seedRandom();
    
    // Test basic utilities
    printf("1. Basic number utilities:\n");
    printf("   Is 17 prime: %s\n", isPrime(17) ? "Yes" : "No");
    printf("   Is 15 even: %s\n", isEven(15) ? "Yes" : "No");
    printf("   GCD of 48 and 18: %d\n", gcd(48, 18));
    printf("   LCM of 12 and 15: %d\n", lcm(12, 15));
    
    // Test power and factorial
    printf("\n2. Power and factorial:\n");
    printf("   2^10 = %lld\n", power(2, 10));
    printf("   5! = %lld\n", factorial(5));
    printf("   Fibonacci(10) = %d\n", fibonacci(10));
    
    // Test number system conversions
    printf("\n3. Number system conversions:\n");
    int decimal = 42;
    printf("   Decimal %d to Binary: %d\n", decimal, decimalToBinary(decimal));
    printf("   Binary 101010 to Decimal: %d\n", binaryToDecimal(101010));
    printf("   Decimal %d to Hex: %s\n", decimal, decimalToHex(decimal));
    
    // Test array statistics
    printf("\n4. Array statistics:\n");
    int test_array[] = {5, 2, 8, 1, 9, 3, 7, 4, 6};
    int array_size = sizeof(test_array) / sizeof(test_array[0]);
    printArrayStats(test_array, array_size);
    
    // Test random numbers
    printf("\n5. Random numbers:\n");
    printf("   Random int (1-100): %d\n", getRandomInt(1, 100));
    printf("   Random double (0.0-1.0): %.3f\n", getRandomDouble(0.0, 1.0));
    printf("   Random probability (0.7): %s\n", getRandomProbability(0.7) ? "Success" : "Failure");
    
    // Test geometry
    printf("\n6. Geometry:\n");
    printf("   90 degrees to radians: %.3f\n", degreesToRadians(90.0));
    printf("   Distance between (0,0) and (3,4): %.2f\n", calculateDistance(0.0, 0.0, 3.0, 4.0));
    printf("   Circle area (radius 5): %.2f\n", calculateCircleArea(5.0));
    
    // Test advanced functions
    printf("\n7. Advanced math functions:\n");
    printNumberProperties(28); // Perfect number
    printNumberProperties(121); // Perfect square and palindrome
    
    printf("\n=== Math utilities test completed ===\n");
}

int main() {
    testMathUtils();
    return 0;
}
