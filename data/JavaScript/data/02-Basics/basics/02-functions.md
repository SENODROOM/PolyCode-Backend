# JavaScript Functions

Functions are reusable blocks of code that perform specific tasks in JavaScript.

## Function Declarations

### Function Declaration
```javascript
function greet(name) {
    return "Hello, " + name + "!";
}

console.log(greet("JavaScript")); // "Hello, JavaScript!"
```

### Function Expression
```javascript
const greet = function(name) {
    return "Hello, " + name + "!";
};
```

### Arrow Function (ES6)
```javascript
const greet = (name) => {
    return "Hello, " + name + "!";
};

// Short form for single return
const add = (a, b) => a + b;
```

## Function Parameters

### Default Parameters
```javascript
function greet(name = "Guest") {
    return "Hello, " + name + "!";
}
```

### Rest Parameters
```javascript
function sum(...numbers) {
    return numbers.reduce((total, num) => total + num, 0);
}

console.log(sum(1, 2, 3, 4, 5)); // 15
```

## Higher-Order Functions
Functions that take other functions as arguments:

```javascript
const numbers = [1, 2, 3, 4, 5];

// map: Transform each element
const doubled = numbers.map(num => num * 2); // [2, 4, 6, 8, 10]

// filter: Select elements that meet condition
const evens = numbers.filter(num => num % 2 === 0); // [2, 4]

// reduce: Reduce array to single value
const sum = numbers.reduce((acc, num) => acc + num, 0); // 15
```

## Closures
Functions that remember their outer variables:

```javascript
function createCounter() {
    let count = 0;
    return function() {
        count++;
        return count;
    };
}

const counter = createCounter();
console.log(counter()); // 1
console.log(counter()); // 2
```
