#include <stdio.h>

int main(void) {
    int n;
    printf("Number of students: ");
    scanf("%d", &n);

    if (n <= 0 || n > 100) {
        printf("Enter a value between 1 and 100.\\n");
        return 1;
    }

    int marks[100];
    int total = 0;
    int highest = -1;
    int lowest = 101;

    for (int i = 0; i < n; i++) {
        printf("Marks for student %d: ", i + 1);
        scanf("%d", &marks[i]);

        if (marks[i] < 0 || marks[i] > 100) {
            printf("Invalid mark found.\\n");
            return 1;
        }

        total += marks[i];
        if (marks[i] > highest) highest = marks[i];
        if (marks[i] < lowest) lowest = marks[i];
    }

    printf("Average: %.2f\\n", (float)total / n);
    printf("Highest: %d\\n", highest);
    printf("Lowest : %d\\n", lowest);
    return 0;
}
