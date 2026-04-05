
---

### **advanced/memory-management.md**
```md
# Memory Management in C

C allows dynamic memory allocation using `malloc`, `calloc`, and `free`.

## Example
```c
#include <stdio.h>
#include <stdlib.h>

int main() {
    int *ptr = (int*) malloc(5 * sizeof(int));
    for(int i = 0; i < 5; i++) ptr[i] = i+1;

    for(int i = 0; i < 5; i++) printf("%d ", ptr[i]);

    free(ptr);
    return 0;
}
##Practice

Allocate memory for an array of 10 integers, initialize, print, then free it.
