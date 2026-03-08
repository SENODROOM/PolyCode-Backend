#include <stdio.h>

int main(void) {
    int n;
    printf("Print numbers up to: ");
    scanf("%d", &n);

    printf("for loop: ");
    for (int i = 1; i <= n; i++) {
        printf("%d ", i);
    }
    printf("\\n");

    int sum = 0;
    int i = 1;
    while (i <= n) {
        sum += i;
        i++;
    }
    printf("while loop sum: %d\\n", sum);

    int attempts = 0;
    do {
        attempts++;
    } while (attempts < 1);
    printf("do-while runs at least once: %d\\n", attempts);

    return 0;
}
