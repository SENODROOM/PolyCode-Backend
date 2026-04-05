
---

### **functions/return-values.md**
```md
# Return Values in C

Functions can return values using the `return` statement.

## Example
```c
#include <stdio.h>

int add(int a, int b) {
    return a + b;
}

int main() {
    int sum = add(5, 7);
    printf("Sum = %d\n", sum);
    return 0;
}
#3Practice

Write a function square that returns the square of an integer.
