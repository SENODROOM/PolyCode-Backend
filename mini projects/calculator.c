#include <stdio.h>

int main(void) {
    int choice;
    double a, b;

    printf("1) Add\\n2) Subtract\\n3) Multiply\\n4) Divide\\n");
    printf("Choose: ");
    scanf("%d", &choice);

    printf("Enter two numbers: ");
    scanf("%lf %lf", &a, &b);

    switch (choice) {
        case 1:
            printf("Result: %.2lf\\n", a + b);
            break;
        case 2:
            printf("Result: %.2lf\\n", a - b);
            break;
        case 3:
            printf("Result: %.2lf\\n", a * b);
            break;
        case 4:
            if (b == 0) {
                printf("Division by zero is not allowed.\\n");
            } else {
                printf("Result: %.2lf\\n", a / b);
            }
            break;
        default:
            printf("Invalid option.\\n");
            break;
    }

    return 0;
}
