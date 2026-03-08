#include <stdio.h>

typedef enum {
    CS,
    EE,
    ME
} Department;

typedef struct {
    int id;
    char name[30];
    Department dept;
} Student;

const char *dept_to_text(Department d) {
    switch (d) {
        case CS: return "CS";
        case EE: return "EE";
        case ME: return "ME";
        default: return "Unknown";
    }
}

int main(void) {
    Student s = {101, "Aisha", CS};
    printf("Student{id=%d, name=%s, dept=%s}\\n", s.id, s.name, dept_to_text(s.dept));
    return 0;
}
