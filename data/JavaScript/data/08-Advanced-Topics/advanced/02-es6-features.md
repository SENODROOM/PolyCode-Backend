# JavaScript ES6+ Features

ES6 (ECMAScript 2015) introduced many powerful features that modernized JavaScript development.

## Arrow Functions

### Basic Syntax
```javascript
// Traditional function
function add(a, b) {
    return a + b;
}

// Arrow function
const add = (a, b) => a + b;

// With multiple statements
const multiply = (a, b) => {
    const result = a * b;
    return result;
};

// Single parameter (no parentheses needed)
const double = x => x * 2;

// No parameters
const getRandom = () => Math.random();
```

## Template Literals

### String Interpolation
```javascript
const name = "John";
const age = 30;

// Template literal
const message = `Hello, I'm ${name} and I'm ${age} years old`;

// Multi-line strings
const html = `
    <div>
        <h1>${name}</h1>
        <p>Age: ${age}</p>
    </div>
`;
```

## Destructuring

### Object Destructuring
```javascript
const person = {
    name: "John",
    age: 30,
    city: "NYC"
};

// Basic destructuring
const { name, age } = person;

// With default values
const { name, country = "USA" } = person;

// Renaming properties
const { name: fullName, age: years } = person;

// Nested destructuring
const user = {
    profile: {
        name: "Jane",
        contact: {
            email: "jane@example.com"
        }
    }
};
const { profile: { name, contact: { email } } } = user;
```

### Array Destructuring
```javascript
const colors = ["red", "green", "blue"];

// Basic destructuring
const [first, second] = colors;

// Skip elements
const [, , third] = colors; // "blue"

// With rest operator
const [primary, ...others] = colors;
```

## Spread and Rest Operators

### Spread Operator
```javascript
// Array spreading
const arr1 = [1, 2, 3];
const arr2 = [4, 5, 6];
const combined = [...arr1, ...arr2]; // [1, 2, 3, 4, 5, 6]

// Object spreading
const obj1 = { a: 1, b: 2 };
const obj2 = { c: 3, d: 4 };
const merged = { ...obj1, ...obj2 }; // { a: 1, b: 2, c: 3, d: 4 }

// Function arguments
const numbers = [1, 2, 3];
const max = Math.max(...numbers); // 3
```

### Rest Operator
```javascript
// Function parameters
function sum(...numbers) {
    return numbers.reduce((total, num) => total + num, 0);
}

// Array destructuring
const [first, ...rest] = [1, 2, 3, 4, 5];
console.log(first); // 1
console.log(rest); // [2, 3, 4, 5]
```

## Enhanced Object Literals

### Property Shorthand
```javascript
const name = "John";
const age = 30;

const person = {
    name, // same as name: name
    age,  // same as age: age
    greet() { // Method shorthand
        return `Hello, I'm ${this.name}`;
    }
};
```

### Computed Property Names
```javascript
const prop = "dynamic";
const obj = {
    [prop]: "value",
    [`prefix_${prop}`]: "another value"
};
```

## Classes

### Class Declaration
```javascript
class Person {
    constructor(name, age) {
        this.name = name;
        this.age = age;
    }
    
    greet() {
        return `Hello, I'm ${this.name}`;
    }
    
    static getSpecies() {
        return "Homo sapiens";
    }
}

const john = new Person("John", 30);
console.log(john.greet());
console.log(Person.getSpecies());
```

### Inheritance
```javascript
class Animal {
    constructor(name) {
        this.name = name;
    }
    
    speak() {
        return `${this.name} makes a sound`;
    }
}

class Dog extends Animal {
    constructor(name, breed) {
        super(name); // Call parent constructor
        this.breed = breed;
    }
    
    speak() {
        return `${this.name} barks`;
    }
}
```

## Modules

### Exporting
```javascript
// math.js
export const PI = 3.14159;
export function add(a, b) {
    return a + b;
}

export default class Calculator {
    multiply(a, b) {
        return a * b;
    }
}
```

### Importing
```javascript
// main.js
import Calculator, { PI, add } from './math.js';

const calc = new Calculator();
console.log(calc.multiply(5, 3));
console.log(add(2, 3));
```

## New Array Methods

### Array.from()
```javascript
const arrayLike = { 0: 'a', 1: 'b', 2: 'c', length: 3 };
const array = Array.from(arrayLike); // ['a', 'b', 'c']

// With mapping function
const numbers = Array.from({ length: 5 }, (_, i) => i + 1); // [1, 2, 3, 4, 5]
```

### Array.find() and Array.findIndex()
```javascript
const users = [
    { id: 1, name: "John" },
    { id: 2, name: "Jane" }
];

const user = users.find(u => u.id === 2); // { id: 2, name: "Jane" }
const index = users.findIndex(u => u.name === "John"); // 0
```

### Array.includes()
```javascript
const fruits = ["apple", "banana", "orange"];
const hasApple = fruits.includes("apple"); // true
```

## Default Parameters

```javascript
function greet(name = "Guest", age = 0) {
    return `Hello ${name}, you are ${age} years old`;
}

greet(); // "Hello Guest, you are 0 years old"
greet("John", 30); // "Hello John, you are 30 years old"
```
