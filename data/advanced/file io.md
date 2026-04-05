
---

### **advanced/file-io.md**
```md
# File I/O in C

C can read from and write to files using `FILE` pointers.

## Example
```c
#include <stdio.h>

int main() {
    FILE *fptr = fopen("example.txt", "w");
    fprintf(fptr, "Hello File!\n");
    fclose(fptr);
    return 0;
}
##
---

### **advanced/file-io.md**
```md
# File I/O in C

C can read from and write to files using `FILE` pointers.

## Example
```c
#include <stdio.h>

int main() {
    FILE *fptr = fopen("example.txt", "w");
    fprintf(fptr, "Hello File!\n");
    fclose(fptr);
    return 0;
}
