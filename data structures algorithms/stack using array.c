#include <stdio.h>

#define MAX 5

typedef struct {
    int data[MAX];
    int top;
} Stack;

void push(Stack *s, int value) {
    if (s->top == MAX - 1) {
        printf("Stack overflow\n");
        return;
    }
    s->data[++(s->top)] = value;
}

int pop(Stack *s) {
    if (s->top == -1) {
        printf("Stack underflow\n");
        return -1;
    }
    return s->data[(s->top)--];
}

int main(void) {
    Stack s = {.top = -1};
    push(&s, 10);
    push(&s, 20);
    push(&s, 30);

    printf("pop: %d\n", pop(&s));
    printf("pop: %d\n", pop(&s));
    return 0;
}
