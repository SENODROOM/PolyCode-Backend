#include <stdio.h>

#define MAX 5

typedef struct {
    int data[MAX];
    int front;
    int rear;
} Queue;

void enqueue(Queue *q, int value) {
    if (q->rear == MAX - 1) {
        printf("Queue full\n");
        return;
    }
    if (q->front == -1) q->front = 0;
    q->data[++(q->rear)] = value;
}

int dequeue(Queue *q) {
    if (q->front == -1 || q->front > q->rear) {
        printf("Queue empty\n");
        return -1;
    }
    return q->data[(q->front)++];
}

int main(void) {
    Queue q = {.front = -1, .rear = -1};
    enqueue(&q, 5);
    enqueue(&q, 15);
    enqueue(&q, 25);

    printf("dequeue: %d\n", dequeue(&q));
    printf("dequeue: %d\n", dequeue(&q));
    return 0;
}
