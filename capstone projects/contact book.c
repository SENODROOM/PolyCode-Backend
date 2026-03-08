#include <stdio.h>
#include <string.h>

#define MAX_CONTACTS 50

typedef struct {
    char name[40];
    char phone[20];
} Contact;

int main(void) {
    Contact contacts[MAX_CONTACTS];
    int count = 0;

    while (count < MAX_CONTACTS) {
        printf("Name (or 'exit'): ");
        if (fgets(contacts[count].name, sizeof(contacts[count].name), stdin) == NULL) {
            break;
        }

        contacts[count].name[strcspn(contacts[count].name, "\n")] = '\0';
        if (strcmp(contacts[count].name, "exit") == 0) {
            break;
        }

        printf("Phone: ");
        if (fgets(contacts[count].phone, sizeof(contacts[count].phone), stdin) == NULL) {
            break;
        }
        contacts[count].phone[strcspn(contacts[count].phone, "\n")] = '\0';

        count++;
    }

    printf("\nSaved contacts:\n");
    for (int i = 0; i < count; i++) {
        printf("%d) %s - %s\n", i + 1, contacts[i].name, contacts[i].phone);
    }

    return 0;
}
