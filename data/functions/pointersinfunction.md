
---

### **functions/pointers-in-functions.md**
```md
# Pointers in Functions

Pointers can be passed to functions to modify original variables.

## Example
```c
#include <stdio.h>

void increment(int *num) {
    *num = *num + 1;
}

int main() {
    int a = 5;
    increment(&a);
    printf("a = %d\n", a);
    return 0;
}
##Practice

Write a function that swaps two integers using pointers.
