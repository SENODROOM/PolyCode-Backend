#include <stdio.h>
#include <stdlib.h>
#include <time.h>

// Exercise 1: Find Maximum Element
int findMaximum(int arr[], int size) {
    if (size <= 0) return -1; // Error case
    
    int max = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] > max) {
            max = arr[i];
        }
    }
    return max;
}

// Exercise 2: Find Minimum Element
int findMinimum(int arr[], int size) {
    if (size <= 0) return -1; // Error case
    
    int min = arr[0];
    for (int i = 1; i < size; i++) {
        if (arr[i] < min) {
            min = arr[i];
        }
    }
    return min;
}

// Exercise 3: Calculate Average
double calculateAverage(int arr[], int size) {
    if (size <= 0) return 0.0;
    
    int sum = 0;
    for (int i = 0; i < size; i++) {
        sum += arr[i];
    }
    return (double)sum / size;
}

// Exercise 4: Search for Element
int searchElement(int arr[], int size, int key) {
    for (int i = 0; i < size; i++) {
        if (arr[i] == key) {
            return i; // Return index
        }
    }
    return -1; // Not found
}

// Exercise 5: Count Occurrences
int countOccurrences(int arr[], int size, int key) {
    int count = 0;
    for (int i = 0; i < size; i++) {
        if (arr[i] == key) {
            count++;
        }
    }
    return count;
}

// Exercise 6: Remove Duplicates
int removeDuplicates(int arr[], int size) {
    if (size == 0) return 0;
    
    int uniqueIndex = 0;
    for (int i = 1; i < size; i++) {
        int j;
        for (j = 0; j < uniqueIndex; j++) {
            if (arr[i] == arr[j]) {
                break;
            }
        }
        if (j == uniqueIndex) {
            arr[++uniqueIndex] = arr[i];
        }
    }
    return uniqueIndex + 1;
}

// Exercise 7: Merge Two Arrays
void mergeArrays(int arr1[], int size1, int arr2[], int size2, int result[]) {
    int index = 0;
    
    // Copy first array
    for (int i = 0; i < size1; i++) {
        result[index++] = arr1[i];
    }
    
    // Copy second array
    for (int i = 0; i < size2; i++) {
        result[index++] = arr2[i];
    }
}

// Exercise 8: Rotate Array Left
void rotateLeft(int arr[], int size, int positions) {
    positions = positions % size; // Handle positions > size
    
    for (int i = 0; i < positions; i++) {
        int temp = arr[0];
        for (int j = 0; j < size - 1; j++) {
            arr[j] = arr[j + 1];
        }
        arr[size - 1] = temp;
    }
}

// Exercise 9: Rotate Array Right
void rotateRight(int arr[], int size, int positions) {
    positions = positions % size; // Handle positions > size
    
    for (int i = 0; i < positions; i++) {
        int temp = arr[size - 1];
        for (int j = size - 1; j > 0; j--) {
            arr[j] = arr[j - 1];
        }
        arr[0] = temp;
    }
}

// Exercise 10: Find Second Largest
int findSecondLargest(int arr[], int size) {
    if (size < 2) return -1;
    
    int largest = arr[0];
    int secondLargest = arr[0];
    
    for (int i = 1; i < size; i++) {
        if (arr[i] > largest) {
            secondLargest = largest;
            largest = arr[i];
        } else if (arr[i] > secondLargest && arr[i] != largest) {
            secondLargest = arr[i];
        }
    }
    
    return secondLargest;
}

// Exercise 11: Check if Array is Sorted
int isSorted(int arr[], int size) {
    for (int i = 0; i < size - 1; i++) {
        if (arr[i] > arr[i + 1]) {
            return 0; // Not sorted
        }
    }
    return 1; // Sorted
}

// Exercise 12: Find Sum of Two Numbers Equal to Target
int findPairWithSum(int arr[], int size, int target, int *index1, int *index2) {
    for (int i = 0; i < size - 1; i++) {
        for (int j = i + 1; j < size; j++) {
            if (arr[i] + arr[j] == target) {
                *index1 = i;
                *index2 = j;
                return 1; // Pair found
            }
        }
    }
    return 0; // No pair found
}

// Helper function to print array
void printArray(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
}

// Helper function to generate random array
void generateRandomArray(int arr[], int size, int min, int max) {
    srand(time(NULL));
    for (int i = 0; i < size; i++) {
        arr[i] = min + rand() % (max - min + 1);
    }
}

int main() {
    printf("Array Manipulation Exercises\n\n");
    
    // Create test array
    int arr[] = {12, 5, 8, 3, 15, 7, 9, 12, 5, 20};
    int size = sizeof(arr) / sizeof(arr[0]);
    
    printf("Original array: ");
    printArray(arr, size);
    printf("\n");
    
    // Exercise 1: Find Maximum
    printf("Exercise 1: Maximum Element\n");
    int max = findMaximum(arr, size);
    printf("Maximum element: %d\n\n", max);
    
    // Exercise 2: Find Minimum
    printf("Exercise 2: Minimum Element\n");
    int min = findMinimum(arr, size);
    printf("Minimum element: %d\n\n", min);
    
    // Exercise 3: Calculate Average
    printf("Exercise 3: Calculate Average\n");
    double avg = calculateAverage(arr, size);
    printf("Average: %.2f\n\n", avg);
    
    // Exercise 4: Search Element
    printf("Exercise 4: Search Element\n");
    int key = 12;
    int index = searchElement(arr, size, key);
    if (index != -1) {
        printf("Element %d found at index %d\n\n", key, index);
    } else {
        printf("Element %d not found\n\n", key);
    }
    
    // Exercise 5: Count Occurrences
    printf("Exercise 5: Count Occurrences\n");
    int count = countOccurrences(arr, size, key);
    printf("Element %d appears %d times\n\n", key, count);
    
    // Exercise 6: Remove Duplicates
    printf("Exercise 6: Remove Duplicates\n");
    int arrCopy[] = {12, 5, 8, 3, 15, 7, 9, 12, 5, 20};
    int copySize = sizeof(arrCopy) / sizeof(arrCopy[0]);
    printf("Before: ");
    printArray(arrCopy, copySize);
    int newSize = removeDuplicates(arrCopy, copySize);
    printf("After: ");
    printArray(arrCopy, newSize);
    printf("\n");
    
    // Exercise 7: Merge Arrays
    printf("Exercise 7: Merge Arrays\n");
    int arr1[] = {1, 3, 5};
    int arr2[] = {2, 4, 6, 8};
    int size1 = sizeof(arr1) / sizeof(arr1[0]);
    int size2 = sizeof(arr2) / sizeof(arr2[0]);
    int merged[size1 + size2];
    mergeArrays(arr1, size1, arr2, size2, merged);
    printf("Array 1: ");
    printArray(arr1, size1);
    printf("Array 2: ");
    printArray(arr2, size2);
    printf("Merged: ");
    printArray(merged, size1 + size2);
    printf("\n");
    
    // Exercise 8: Rotate Left
    printf("Exercise 8: Rotate Left\n");
    int rotateArr[] = {1, 2, 3, 4, 5};
    int rotateSize = sizeof(rotateArr) / sizeof(rotateArr[0]);
    printf("Before: ");
    printArray(rotateArr, rotateSize);
    rotateLeft(rotateArr, rotateSize, 2);
    printf("After rotating left by 2: ");
    printArray(rotateArr, rotateSize);
    printf("\n");
    
    // Exercise 9: Rotate Right
    printf("Exercise 9: Rotate Right\n");
    int rotateArr2[] = {1, 2, 3, 4, 5};
    int rotateSize2 = sizeof(rotateArr2) / sizeof(rotateArr2[0]);
    printf("Before: ");
    printArray(rotateArr2, rotateSize2);
    rotateRight(rotateArr2, rotateSize2, 2);
    printf("After rotating right by 2: ");
    printArray(rotateArr2, rotateSize2);
    printf("\n");
    
    // Exercise 10: Find Second Largest
    printf("Exercise 10: Find Second Largest\n");
    int secondMax = findSecondLargest(arr, size);
    printf("Second largest element: %d\n\n", secondMax);
    
    // Exercise 11: Check if Sorted
    printf("Exercise 11: Check if Sorted\n");
    int sortedArr[] = {1, 2, 3, 4, 5};
    int unsortedArr[] = {3, 1, 4, 2, 5};
    int sortedSize = sizeof(sortedArr) / sizeof(sortedArr[0]);
    int unsortedSize = sizeof(unsortedArr) / sizeof(unsortedArr[0]);
    printf("Array {1, 2, 3, 4, 5} is %ssorted\n", isSorted(sortedArr, sortedSize) ? "" : "not ");
    printf("Array {3, 1, 4, 2, 5} is %ssorted\n\n", isSorted(unsortedArr, unsortedSize) ? "" : "not ");
    
    // Exercise 12: Find Pair with Sum
    printf("Exercise 12: Find Pair with Sum\n");
    int target = 20;
    int index1, index2;
    if (findPairWithSum(arr, size, target, &index1, &index2)) {
        printf("Pair found: arr[%d] = %d + arr[%d] = %d = %d\n", 
               index1, arr[index1], index2, arr[index2], target);
    } else {
        printf("No pair found that sums to %d\n", target);
    }
    printf("\n");
    
    return 0;
}
