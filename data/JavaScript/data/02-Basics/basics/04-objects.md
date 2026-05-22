# JavaScript Objects

Objects are collections of key-value pairs in JavaScript. They are the fundamental data structure for storing complex data.

## Creating Objects

### Object Literal
```javascript
const person = {
    name: "John Doe",
    age: 30,
    city: "New York",
    isStudent: false
};
```

### Object Constructor
```javascript
const person = new Object();
person.name = "John Doe";
person.age = 30;
```

### Constructor Function
```javascript
function Person(name, age) {
    this.name = name;
    this.age = age;
    this.greet = function() {
        return "Hello, I'm " + this.name;
    };
}

const john = new Person("John", 30);
```

### ES6 Class
```javascript
class Person {
    constructor(name, age) {
        this.name = name;
        this.age = age;
    }
    
    greet() {
        return `Hello, I'm ${this.name}`;
    }
}

const jane = new Person("Jane", 25);
```

## Accessing Properties

### Dot Notation
```javascript
console.log(person.name); // "John Doe"
person.age = 31; // Update
```

### Bracket Notation
```javascript
console.log(person["name"]); // "John Doe"
const prop = "age";
console.log(person[prop]); // 30
```

### Destructuring
```javascript
const { name, age } = person;
console.log(name, age); // "John Doe", 30
```

## Object Methods

### Built-in Methods
```javascript
const obj = { name: "John", age: 30 };

// Object.keys(): Get all keys
const keys = Object.keys(obj); // ["name", "age"]

// Object.values(): Get all values
const values = Object.values(obj); // ["John", 30]

// Object.entries(): Get key-value pairs
const entries = Object.entries(obj); // [["name", "John"], ["age", 30]]

// Object.assign(): Copy properties
const newObj = Object.assign({}, obj, { city: "NYC" });

// Object.freeze(): Prevent modifications
Object.freeze(obj);
```

### Adding Methods
```javascript
const calculator = {
    add: function(a, b) {
        return a + b;
    },
    
    subtract(a, b) {
        return a - b;
    }
};

console.log(calculator.add(5, 3)); // 8
console.log(calculator.subtract(10, 4)); // 6
```

## This Keyword
```javascript
const person = {
    name: "John",
    age: 30,
    
    greet() {
        return `Hello, I'm ${this.name} and I'm ${this.age} years old`;
    },
    
    celebrateBirthday() {
        this.age++;
        return `Happy birthday! Now I'm ${this.age}`;
    }
};
```

## Prototypes
```javascript
function Person(name) {
    this.name = name;
}

Person.prototype.greet = function() {
    return `Hello, I'm ${this.name}`;
};

const john = new Person("John");
console.log(john.greet()); // "Hello, I'm John"
```
