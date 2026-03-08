#include <ctype.h>
#include <stdio.h>

int main(void) {
    char text[1000];
    int vowels = 0, digits = 0, spaces = 0;

    printf("Enter a line: ");
    fgets(text, sizeof(text), stdin);

    for (int i = 0; text[i] != '\0'; i++) {
        char c = (char)tolower((unsigned char)text[i]);
        if (c == 'a' || c == 'e' || c == 'i' || c == 'o' || c == 'u') {
            vowels++;
        }
        if (isdigit((unsigned char)c)) {
            digits++;
        }
        if (c == ' ') {
            spaces++;
        }
    }

    printf("Vowels: %d\n", vowels);
    printf("Digits: %d\n", digits);
    printf("Spaces: %d\n", spaces);
    return 0;
}
