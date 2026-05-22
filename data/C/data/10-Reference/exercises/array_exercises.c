/*
 * File: array_exercises.c
 * Description: Collection of array manipulation exercises
 */

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>

// Exercise 1: Find the second largest element in an array
int findSecondLargest(int arr[], int size) {
    if (size < 2) return INT_MIN;
    
    int first = INT_MIN, second = INT_MIN;
    
    for (int i = 0; i < size; i++) {
        if (arr[i] > first) {
            second = first;
            first = arr[i];
        } else if (arr[i] > second && arr[i] != first) {
            second = arr[i];
        }
    }
    
    return (second == INT_MIN) ? INT_MIN : second;
}

// Exercise 2: Rotate array to the right by k positions
void rotateRight(int arr[], int size, int k) {
    if (size == 0) return;
    
    k = k % size; // Handle k > size
    if (k == 0) return;
    
    int* temp = (int*)malloc(k * sizeof(int));
    if (temp == NULL) return;
    
    // Copy last k elements
    for (int i = 0; i < k; i++) {
        temp[i] = arr[size - k + i];
    }
    
    // Shift remaining elements
    for (int i = size - 1; i >= k; i--) {
        arr[i] = arr[i - k];
    }
    
    // Copy temp to beginning
    for (int i = 0; i < k; i++) {
        arr[i] = temp[i];
    }
    
    free(temp);
}

// Exercise 3: Merge two sorted arrays
int* mergeSortedArrays(int arr1[], int size1, int arr2[], int size2, int* result_size) {
    *result_size = size1 + size2;
    int* result = (int*)malloc(*result_size * sizeof(int));
    if (result == NULL) return NULL;
    
    int i = 0, j = 0, k = 0;
    
    while (i < size1 && j < size2) {
        if (arr1[i] <= arr2[j]) {
            result[k++] = arr1[i++];
        } else {
            result[k++] = arr2[j++];
        }
    }
    
    // Copy remaining elements
    while (i < size1) {
        result[k++] = arr1[i++];
    }
    
    while (j < size2) {
        result[k++] = arr2[j++];
    }
    
    return result;
}

// Exercise 4: Find subarray with maximum sum (Kadane's algorithm)
int maxSubarraySum(int arr[], int size) {
    if (size == 0) return 0;
    
    int max_so_far = arr[0];
    int max_ending_here = arr[0];
    
    for (int i = 1; i < size; i++) {
        max_ending_here = (arr[i] > max_ending_here + arr[i]) ? arr[i] : max_ending_here + arr[i];
        max_so_far = (max_so_far > max_ending_here) ? max_so_far : max_ending_here;
    }
    
    return max_so_far;
}

// Exercise 5: Remove duplicates from array (in-place)
int removeDuplicates(int arr[], int size) {
    if (size == 0 || size == 1) return size;
    
    int write_index = 1;
    
    for (int i = 1; i < size; i++) {
        int j;
        for (j = 0; j < write_index; j++) {
            if (arr[i] == arr[j]) {
                break;
            }
        }
        
        if (j == write_index) {
            arr[write_index] = arr[i];
            write_index++;
        }
    }
    
    return write_index;
}

// Exercise 6: Find frequency of each element
void printFrequency(int arr[], int size) {
    int* freq = (int*)calloc(size, sizeof(int));
    if (freq == NULL) return;
    
    int unique_count = 0;
    
    for (int i = 0; i < size; i++) {
        int j;
        for (j = 0; j < unique_count; j++) {
            if (arr[i] == arr[j]) {
                freq[j]++;
                break;
            }
        }
        
        if (j == unique_count) {
            arr[unique_count] = arr[i];
            freq[unique_count] = 1;
            unique_count++;
        }
    }
    
    printf("Element Frequency:\n");
    for (int i = 0; i < unique_count; i++) {
        printf("%d\t: %d\n", arr[i], freq[i]);
    }
    
    free(freq);
}

// Exercise 7: Reverse array in-place
void reverseArray(int arr[], int size) {
    for (int i = 0; i < size / 2; i++) {
        int temp = arr[i];
        arr[i] = arr[size - 1 - i];
        arr[size - 1 - i] = temp;
    }
}

// Exercise 8: Check if array is palindrome
int isPalindrome(int arr[], int size) {
    for (int i = 0; i < size / 2; i++) {
        if (arr[i] != arr[size - 1 - i]) {
            return 0;
        }
    }
    return 1;
}

// Exercise 9: Find missing number in array (1 to n)
int findMissingNumber(int arr[], int size) {
    int n = size + 1;
    int expected_sum = n * (n + 1) / 2;
    int actual_sum = 0;
    
    for (int i = 0; i < size; i++) {
        actual_sum += arr[i];
    }
    
    return expected_sum - actual_sum;
}

// Exercise 10: Sort array using selection sort
void selectionSort(int arr[], int size) {
    for (int i = 0; i < size - 1; i++) {
        int min_idx = i;
        
        for (int j = i + 1; j < size; j++) {
            if (arr[j] < arr[min_idx]) {
                min_idx = j;
            }
        }
        
        if (min_idx != i) {
            int temp = arr[i];
            arr[i] = arr[min_idx];
            arr[min_idx] = temp;
        }
    }
}

// Helper function to print array
void printArray(int arr[], int size) {
    printf("[");
    for (int i = 0; i < size; i++) {
        printf("%d", arr[i]);
        if (i < size - 1) printf(", ");
    }
    printf("]\n");
}

// Test function
void runArrayExercises() {
    printf("=== Array Exercises ===\n\n");
    
    // Test array
    int arr[] = {3, 1, 4, 1, 5, 9, 2, 6, 5, 3};
    int size = sizeof(arr) / sizeof(arr[0]);
    
    printf("Original array: ");
    printArray(arr, size);
    
    // Exercise 1: Second largest
    int second_largest = findSecondLargest(arr, size);
    printf("\n1. Second largest element: %d\n", second_largest);
    
    // Exercise 2: Rotate array
    int arr_copy[10];
    memcpy(arr_copy, arr, sizeof(arr));
    rotateRight(arr_copy, size, 3);
    printf("\n2. Array rotated right by 3 positions: ");
    printArray(arr_copy, size);
    
    // Exercise 3: Merge sorted arrays
    int arr1[] = {1, 3, 5, 7};
    int arr2[] = {2, 4, 6, 8};
    int merged_size;
    int* merged = mergeSortedArrays(arr1, 4, arr2, 4, &merged_size);
    if (merged != NULL) {
        printf("\n3. Merged sorted arrays: ");
        printArray(merged, merged_size);
        free(merged);
    }
    
    // Exercise 4: Maximum subarray sum
    int test_arr[] = {-2, -3, 4, -1, -2, 1, 5, -3};
    int test_size = sizeof(test_arr) / sizeof(test_arr[0]);
    int max_sum = maxSubarraySum(test_arr, test_size);
    printf("\n4. Maximum subarray sum: %d\n", max_sum);
    
    // Exercise 5: Remove duplicates
    int dup_arr[] = {1, 2, 2, 3, 4, 4, 5, 5, 5};
    int dup_size = sizeof(dup_arr) / sizeof(dup_arr[0]);
    int new_size = removeDuplicates(dup_arr, dup_size);
    printf("\n5. Array after removing duplicates: ");
    printArray(dup_arr, new_size);
    
    // Exercise 6: Frequency count
    printf("\n6. Frequency of elements:\n");
    int freq_arr[] = {1, 2, 3, 2, 1, 4, 5, 3, 2, 1};
    int freq_size = sizeof(freq_arr) / sizeof(freq_arr[0]);
    printFrequency(freq_arr, freq_size);
    
    // Exercise 7: Reverse array
    int rev_arr[] = {1, 2, 3, 4, 5};
    int rev_size = sizeof(rev_arr) / sizeof(rev_arr[0]);
    printf("\n7. Original array: ");
    printArray(rev_arr, rev_size);
    reverseArray(rev_arr, rev_size);
    printf("   Reversed array: ");
    printArray(rev_arr, rev_size);
    
    // Exercise 8: Palindrome check
    int pal_arr1[] = {1, 2, 3, 2, 1};
    int pal_arr2[] = {1, 2, 3, 4, 5};
    int pal_size1 = sizeof(pal_arr1) / sizeof(pal_arr1[0]);
    int pal_size2 = sizeof(pal_arr2) / sizeof(pal_arr2[0]);
    
    printf("\n8. Palindrome check:\n");
    printf("   [1,2,3,2,1] is palindrome: %s\n", isPalindrome(pal_arr1, pal_size1) ? "Yes" : "No");
    printf("   [1,2,3,4,5] is palindrome: %s\n", isPalindrome(pal_arr2, pal_size2) ? "Yes" : "No");
    
    // Exercise 9: Find missing number
    int missing_arr[] = {1, 2, 4, 5, 6}; // Missing 3
    int missing_size = sizeof(missing_arr) / sizeof(missing_arr[0]);
    int missing = findMissingNumber(missing_arr, missing_size);
    printf("\n9. Missing number in [1,2,4,5,6]: %d\n", missing);
    
    // Exercise 10: Selection sort
    int sort_arr[] = {64, 34, 25, 12, 22, 11, 90};
    int sort_size = sizeof(sort_arr) / sizeof(sort_arr[0]);
    printf("\n10. Selection sort:\n");
    printf("    Original array: ");
    printArray(sort_arr, sort_size);
    selectionSort(sort_arr, sort_size);
    printf("    Sorted array: ");
    printArray(sort_arr, sort_size);
    
    printf("\n=== All array exercises completed ===\n");
}

int main() {
    runArrayExercises();
    return 0;
}
