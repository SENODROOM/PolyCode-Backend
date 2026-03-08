#include <stdio.h>

void bubble_sort(int arr[], int n) {
    for (int pass = 0; pass < n - 1; pass++) {
        for (int i = 0; i < n - pass - 1; i++) {
            if (arr[i] > arr[i + 1]) {
                int temp = arr[i];
                arr[i] = arr[i + 1];
                arr[i + 1] = temp;
            }
        }
    }
}

int binary_search(int arr[], int n, int target) {
    int left = 0;
    int right = n - 1;

    while (left <= right) {
        int mid = left + (right - left) / 2;
        if (arr[mid] == target) {
            return mid;
        }
        if (arr[mid] < target) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }

    return -1;
}

int main(void) {
    int arr[] = {9, 4, 7, 1, 3};
    int n = (int)(sizeof(arr) / sizeof(arr[0]));

    bubble_sort(arr, n);

    printf("Sorted: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", arr[i]);
    }
    printf("\\n");

    int target = 7;
    int index = binary_search(arr, n, target);
    printf("Target %d index: %d\\n", target, index);
    return 0;
}
