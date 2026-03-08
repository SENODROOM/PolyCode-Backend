#include <stdio.h>

int main(void) {
    FILE *fp = fopen("notes.txt", "w");
    if (fp == NULL) {
        printf("Could not create file.\\n");
        return 1;
    }

    fprintf(fp, "Line 1: C file handling\\n");
    fprintf(fp, "Line 2: Always close files\\n");
    fclose(fp);

    fp = fopen("notes.txt", "r");
    if (fp == NULL) {
        printf("Could not read file.\\n");
        return 1;
    }

    char line[100];
    while (fgets(line, sizeof(line), fp) != NULL) {
        printf("%s", line);
    }
    fclose(fp);
    return 0;
}
