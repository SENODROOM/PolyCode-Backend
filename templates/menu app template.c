#include <stdio.h>

int main(void) {
    int choice;

    do {
        printf("\n1) Option A\n2) Option B\n0) Exit\nChoice: ");
        scanf("%d", &choice);

        switch (choice) {
            case 1:
                printf("Option A selected\n");
                break;
            case 2:
                printf("Option B selected\n");
                break;
            case 0:
                printf("Exiting...\n");
                break;
            default:
                printf("Invalid choice\n");
                break;
        }
    } while (choice != 0);

    return 0;
}
