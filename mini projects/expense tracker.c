#include <stdio.h>

int main(void) {
    int n;
    printf("How many expenses? ");
    scanf("%d", &n);

    if (n <= 0 || n > 100) {
        printf("Invalid count.\n");
        return 1;
    }

    float expenses[100];
    float total = 0.0f;

    for (int i = 0; i < n; i++) {
        printf("Expense %d: ", i + 1);
        scanf("%f", &expenses[i]);
        if (expenses[i] < 0) {
            printf("Expense cannot be negative.\n");
            return 1;
        }
        total += expenses[i];
    }

    printf("Total: %.2f\n", total);
    printf("Average: %.2f\n", total / n);
    return 0;
}
