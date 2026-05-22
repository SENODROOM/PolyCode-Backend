#include <stdio.h>
#include <stdlib.h>

// Linear Search
int linearSearch(int arr[], int n, int key) {
    for (int i = 0; i < n; i++) {
        if (arr[i] == key) {
            return i; // Return index if found
        }
    }
    return -1; // Return -1 if not found
}

// Binary Search (iterative)
int binarySearch(int arr[], int n, int key) {
    int left = 0;
    int right = n - 1;
    
    while (left <= right) {
        int mid = left + (right - left) / 2;
        
        if (arr[mid] == key) {
            return mid; // Return index if found
        }
        
        if (arr[mid] < key) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }
    
    return -1; // Return -1 if not found
}

// Binary Search (recursive)
int binarySearchRecursive(int arr[], int left, int right, int key) {
    if (left > right) {
        return -1;
    }
    
    int mid = left + (right - left) / 2;
    
    if (arr[mid] == key) {
        return mid;
    }
    
    if (arr[mid] < key) {
        return binarySearchRecursive(arr, mid + 1, right, key);
    } else {
        return binarySearchRecursive(arr, left, mid - 1, key);
    }
}

// Jump Search
int jumpSearch(int arr[], int n, int key) {
    int step = (int)sqrt(n);
    int prev = 0;
    
    // Find the block where element may be present
    while (arr[step - 1] < key) {
        prev = step;
        step += (int)sqrt(n);
        if (prev >= n) {
            return -1;
        }
    }
    
    // Linear search in the identified block
    while (arr[prev] < key) {
        prev++;
        if (prev == step || prev >= n) {
            return -1;
        }
    }
    
    if (arr[prev] == key) {
        return prev;
    }
    
    return -1;
}

// Interpolation Search
int interpolationSearch(int arr[], int n, int key) {
    int left = 0;
    int right = n - 1;
    
    while (left <= right && key >= arr[left] && key <= arr[right]) {
        if (left == right) {
            if (arr[left] == key) {
                return left;
            }
            return -1;
        }
        
        // Estimate position
        int pos = left + ((key - arr[left]) * (right - left)) / (arr[right] - arr[left]);
        
        if (arr[pos] == key) {
            return pos;
        }
        
        if (arr[pos] < key) {
            left = pos + 1;
        } else {
            right = pos - 1;
        }
    }
    
    return -1;
}

// Exponential Search
int exponentialSearch(int arr[], int n, int key) {
    if (arr[0] == key) {
        return 0;
    }
    
    // Find range for binary search
    int i = 1;
    while (i < n && arr[i] <= key) {
        i *= 2;
    }
    
    // Call binary search for the found range
    return binarySearchRecursive(arr, i / 2, (i < n) ? i : n - 1, key);
}

// Function to print array
void printArray(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
}

int main() {
    printf("Searching Algorithms Demonstration\n\n");
    
    // Sorted array for binary search and other advanced algorithms
    int sortedArr[] = {2, 5, 8, 12, 16, 23, 38, 56, 72, 91};
    int unsortedArr[] = {23, 5, 16, 91, 2, 38, 72, 8, 56, 12};
    
    int n = sizeof(sortedArr) / sizeof(sortedArr[0]);
    int key = 23;
    
    printf("Array: ");
    printArray(sortedArr, n);
    printf("Searching for: %d\n\n", key);
    
    // Linear Search (works on unsorted arrays)
    printf("Linear Search: ");
    int result = linearSearch(unsortedArr, n, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Binary Search (requires sorted array)
    printf("Binary Search (Iterative): ");
    result = binarySearch(sortedArr, n, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Binary Search (Recursive)
    printf("Binary Search (Recursive): ");
    result = binarySearchRecursive(sortedArr, 0, n - 1, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Jump Search
    printf("Jump Search: ");
    result = jumpSearch(sortedArr, n, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Interpolation Search
    printf("Interpolation Search: ");
    result = interpolationSearch(sortedArr, n, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Exponential Search
    printf("Exponential Search: ");
    result = exponentialSearch(sortedArr, n, key);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    // Test with element not present
    int missingKey = 50;
    printf("\nSearching for non-existent element %d:\n", missingKey);
    
    printf("Binary Search: ");
    result = binarySearch(sortedArr, n, missingKey);
    if (result != -1) {
        printf("Found at index %d\n", result);
    } else {
        printf("Not found\n");
    }
    
    return 0;
}
