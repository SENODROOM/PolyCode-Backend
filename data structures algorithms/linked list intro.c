#include <stdio.h>
#include <stdlib.h>

typedef struct Node {
    int value;
    struct Node *next;
} Node;

Node *create_node(int value) {
    Node *node = (Node *)malloc(sizeof(Node));
    if (node == NULL) {
        return NULL;
    }
    node->value = value;
    node->next = NULL;
    return node;
}

void print_list(Node *head) {
    while (head != NULL) {
        printf("%d", head->value);
        if (head->next != NULL) {
            printf(" -> ");
        }
        head = head->next;
    }
    printf("\\n");
}

void free_list(Node *head) {
    while (head != NULL) {
        Node *next = head->next;
        free(head);
        head = next;
    }
}

int main(void) {
    Node *a = create_node(10);
    Node *b = create_node(20);
    Node *c = create_node(30);

    if (a == NULL || b == NULL || c == NULL) {
        free_list(a);
        free_list(b);
        free_list(c);
        printf("Allocation failed.\\n");
        return 1;
    }

    a->next = b;
    b->next = c;

    print_list(a);
    free_list(a);
    return 0;
}
