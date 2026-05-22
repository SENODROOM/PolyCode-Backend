# JavaScript Control Flow

Control flow statements determine the order in which code is executed in JavaScript.

## Conditional Statements

### if Statement
```javascript
let age = 18;

if (age >= 18) {
    console.log("You are an adult");
} else {
    console.log("You are a minor");
}
```

### if-else if-else Chain
```javascript
let score = 85;

if (score >= 90) {
    console.log("Grade: A");
} else if (score >= 80) {
    console.log("Grade: B");
} else if (score >= 70) {
    console.log("Grade: C");
} else {
    console.log("Grade: F");
}
```

### switch Statement
```javascript
let day = "Monday";

switch (day) {
    case "Monday":
        console.log("Start of the week");
        break;
    case "Friday":
        console.log("Almost weekend");
        break;
    case "Saturday":
    case "Sunday":
        console.log("Weekend!");
        break;
    default:
        console.log("Regular day");
}
```

### Ternary Operator
```javascript
let age = 20;
let message = age >= 18 ? "Adult" : "Minor";
console.log(message); // "Adult"
```

## Loops

### for Loop
```javascript
for (let i = 0; i < 5; i++) {
    console.log(`Iteration ${i}`);
}
```

### while Loop
```javascript
let count = 0;
while (count < 5) {
    console.log(`Count: ${count}`);
    count++;
}
```

### do-while Loop
```javascript
let i = 0;
do {
    console.log(`Do-while: ${i}`);
    i++;
} while (i < 3);
```

### for...in Loop (Objects)
```javascript
const person = {
    name: "John",
    age: 30,
    city: "NYC"
};

for (let key in person) {
    console.log(`${key}: ${person[key]}`);
}
```

### for...of Loop (Arrays/Strings)
```javascript
const fruits = ["apple", "banana", "orange"];

for (let fruit of fruits) {
    console.log(fruit);
}

const text = "JavaScript";
for (let char of text) {
    console.log(char);
}
```

## Loop Control

### break Statement
```javascript
for (let i = 0; i < 10; i++) {
    if (i === 5) {
        break; // Exit loop
    }
    console.log(i); // 0, 1, 2, 3, 4
}
```

### continue Statement
```javascript
for (let i = 0; i < 10; i++) {
    if (i % 2 === 0) {
        continue; // Skip even numbers
    }
    console.log(i); // 1, 3, 5, 7, 9
}
```

## Exception Handling

### try-catch Block
```javascript
try {
    // Code that might throw an error
    const result = riskyOperation();
    console.log(result);
} catch (error) {
    console.log("Error occurred:", error.message);
} finally {
    console.log("Cleanup code");
}
```

### Throwing Errors
```javascript
function divide(a, b) {
    if (b === 0) {
        throw new Error("Division by zero is not allowed");
    }
    return a / b;
}

try {
    const result = divide(10, 0);
} catch (error) {
    console.log(error.message); // "Division by zero is not allowed"
}
```
