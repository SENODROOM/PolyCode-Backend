# JavaScript Arrays

Arrays are ordered collections of values in JavaScript.

## Creating Arrays

### Array Literal
```javascript
const fruits = ["apple", "banana", "orange"];
const numbers = [1, 2, 3, 4, 5];
const mixed = ["text", 42, true, null];
```

### Array Constructor
```javascript
const empty = new Array();
const sized = new Array(5); // [empty × 5]
const fromArgs = new Array("a", "b", "c");
```

## Array Methods

### Adding Elements
```javascript
const arr = [1, 2, 3];

// push: Add to end
arr.push(4); // [1, 2, 3, 4]

// unshift: Add to beginning
arr.unshift(0); // [0, 1, 2, 3, 4]

// splice: Add at specific position
arr.splice(2, 0, 1.5); // [0, 1, 1.5, 2, 3, 4]
```

### Removing Elements
```javascript
const arr = [1, 2, 3, 4, 5];

// pop: Remove from end
const last = arr.pop(); // 5, arr is [1, 2, 3, 4]

// shift: Remove from beginning
const first = arr.shift(); // 1, arr is [2, 3, 4]

// splice: Remove from specific position
arr.splice(1, 1); // [2, 4]
```

### Finding Elements
```javascript
const fruits = ["apple", "banana", "orange"];

// indexOf: Find index of element
const index = fruits.indexOf("banana"); // 1

// includes: Check if element exists
const hasApple = fruits.includes("apple"); // true

// find: Find element that matches condition
const found = fruits.find(fruit => fruit.length > 5); // "banana"
```

### Transforming Arrays
```javascript
const numbers = [1, 2, 3, 4, 5];

// map: Transform each element
const doubled = numbers.map(num => num * 2); // [2, 4, 6, 8, 10]

// filter: Keep elements that match condition
const evens = numbers.filter(num => num % 2 === 0); // [2, 4]

// reduce: Reduce to single value
const sum = numbers.reduce((acc, num) => acc + num, 0); // 15

// slice: Extract portion
const slice = numbers.slice(1, 3); // [2, 3]
```

## Iterating Arrays
```javascript
const fruits = ["apple", "banana", "orange"];

// for...of loop
for (const fruit of fruits) {
    console.log(fruit);
}

// forEach method
fruits.forEach((fruit, index) => {
    console.log(`${index}: ${fruit}`);
});

// traditional for loop
for (let i = 0; i < fruits.length; i++) {
    console.log(fruits[i]);
}
```
