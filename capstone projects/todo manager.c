#include <stdio.h>
#include <string.h>

#define MAX_TASKS 50
#define MAX_LEN 100

int main(void) {
    char tasks[MAX_TASKS][MAX_LEN];
    int count = 0;
    int choice;

    do {
        printf("\n1) Add task\n2) List tasks\n0) Exit\nChoice: ");
        scanf("%d", &choice);
        getchar();

        if (choice == 1) {
            if (count >= MAX_TASKS) {
                printf("Task list full.\n");
                continue;
            }
            printf("Task text: ");
            fgets(tasks[count], MAX_LEN, stdin);
            tasks[count][strcspn(tasks[count], "\n")] = '\0';
            count++;
        } else if (choice == 2) {
            if (count == 0) {
                printf("No tasks yet.\n");
            } else {
                for (int i = 0; i < count; i++) {
                    printf("%d) %s\n", i + 1, tasks[i]);
                }
            }
        }
    } while (choice != 0);

    return 0;
}
