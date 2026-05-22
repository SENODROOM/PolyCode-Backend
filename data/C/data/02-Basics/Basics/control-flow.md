# C Control Flow

## Conditional Statements

### if Statement
```c
if (condition) {
    // Code to execute if condition is true
}
```

### if-else Statement
```c
if (condition) {
    // Code if condition is true
} else {
    // Code if condition is false
}
```

### if-else if-else Ladder
```c
if (condition1) {
    // Code if condition1 is true
} else if (condition2) {
    // Code if condition2 is true
} else {
    // Code if all conditions are false
}
```

### Example
```c
int score = 85;

if (score >= 90) {
    printf("Grade A\n");
} else if (score >= 80) {
    printf("Grade B\n");
} else if (score >= 70) {
    printf("Grade C\n");
} else {
    printf("Grade F\n");
}
```

## switch Statement
```c
switch (expression) {
    case constant1:
        // Code for case 1
        break;
    case constant2:
        // Code for case 2
        break;
    default:
        // Code if no case matches
}
```

### Example
```c
int day = 3;

switch (day) {
    case 1:
        printf("Monday\n");
        break;
    case 2:
        printf("Tuesday\n");
        break;
    case 3:
        printf("Wednesday\n");
        break;
    case 4:
        printf("Thursday\n");
        break;
    case 5:
        printf("Friday\n");
        break;
    default:
        printf("Weekend\n");
}
```

## Loops

### for Loop
```c
for (initialization; condition; increment) {
    // Loop body
}
```

### Example
```c
// Print numbers 1 to 10
for (int i = 1; i <= 10; i++) {
    printf("%d ", i);
}

// Calculate factorial
int n = 5;
int factorial = 1;
for (int i = 1; i <= n; i++) {
    factorial *= i;
}
```

### while Loop
```c
while (condition) {
    // Loop body
}
```

### Example
```c
int count = 1;
while (count <= 5) {
    printf("Count: %d\n", count);
    count++;
}

// Input validation
int number;
do {
    printf("Enter a positive number: ");
    scanf("%d", &number);
} while (number <= 0);
```

### do-while Loop
```c
do {
    // Loop body
} while (condition);
```

### Example
```c
int menu_choice;
do {
    printf("1. Option 1\n");
    printf("2. Option 2\n");
    printf("3. Exit\n");
    printf("Enter choice: ");
    scanf("%d", &menu_choice);
    
    switch (menu_choice) {
        case 1:
            printf("You chose option 1\n");
            break;
        case 2:
            printf("You chose option 2\n");
            break;
        case 3:
            printf("Exiting...\n");
            break;
        default:
            printf("Invalid choice\n");
    }
} while (menu_choice != 3);
```

## Jump Statements

### break Statement
```c
// Exit from loop or switch
for (int i = 1; i <= 10; i++) {
    if (i == 6) {
        break;  // Exit loop when i equals 6
    }
    printf("%d ", i);  // Prints: 1 2 3 4 5
}
```

### continue Statement
```c
// Skip to next iteration
for (int i = 1; i <= 10; i++) {
    if (i % 2 == 0) {
        continue;  // Skip even numbers
    }
    printf("%d ", i);  // Prints: 1 3 5 7 9
}
```

### goto Statement (Use sparingly)
```c
int i = 1;
start:
printf("%d ", i);
i++;
if (i <= 5) {
    goto start;
}
```
