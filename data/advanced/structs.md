
---

### **advanced/structs.md**
```md
# Structs in C

Structs allow grouping different types of variables together.

## Example
```c
#include <stdio.h>

struct Student {
    char name[20];
    int age;
};

int main() {
    struct Student s1 = {"Alice", 20};
    printf("%s is %d years old\n", s1.name, s1.age);
    return 0;
}
##Practice

Create a struct for Book with title, author, and price. Print book details.
