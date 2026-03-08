#include <stdio.h>

int main(void) {
    int marks;
    printf("Enter marks (0-100): ");
    scanf("%d", &marks);

    if (marks < 0 || marks > 100) {
        printf("Invalid marks.\\n");
        return 0;
    }

    if (marks >= 90) {
        printf("Grade: A\\n");
    } else if (marks >= 75) {
        printf("Grade: B\\n");
    } else if (marks >= 60) {
        printf("Grade: C\\n");
    } else {
        printf("Grade: D\\n");
    }

    switch (marks / 10) {
        case 10:
        case 9:
            printf("Excellent performance.\\n");
            break;
        case 8:
        case 7:
            printf("Good job.\\n");
            break;
        default:
            printf("Keep practicing.\\n");
            break;
    }

    return 0;
}
