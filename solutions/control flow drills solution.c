#include <stdio.h>

int main(void) {
    int n;
    scanf("%d", &n);

    if (n < 1) {
        printf("Enter n >= 1\n");
        return 1;
    }

    int odd = 0, even = 0;
    int i = 1;
    while (i <= n) {
        if (i % 2 == 0) {
            even += i;
        } else {
            odd += i;
        }
        i++;
    }

    printf("odd=%d even=%d\n", odd, even);
    return 0;
}
