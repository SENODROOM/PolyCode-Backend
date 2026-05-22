## Functions

```javascript
// Function Declaration (hoisted)
function greet(name) {
  return `Hello, ${name}!`;
}

// Function Expression (not hoisted)
const greet = function(name) {
  return `Hello, ${name}!`;
};

// Arrow Function (ES6) — shorter, no own 'this'
const greet = (name) => `Hello, ${name}!`;
const square = n => n * n;  // single param, no parens needed
const noop = () => {};      // no params

// Default Parameters
function power(base, exp = 2) {
  return base ** exp;
}
power(3);    // 9
power(3, 3); // 27

// Rest Parameters
function sum(...nums) {
  return nums.reduce((a, b) => a + b, 0);
}
sum(1, 2, 3, 4); // 10

// Destructuring in Parameters
function display({ name, age = 0 }) {
  console.log(`${name} is ${age}`);
}

// Closures — functions that remember their outer scope
function counter() {
  let count = 0;
  return {
    increment: () => ++count,
    decrement: () => --count,
    value:     () => count,
  };
}
const c = counter();
c.increment(); // 1
c.increment(); // 2
c.value();     // 2

// IIFE — Immediately Invoked Function Expression
(function() {
  const private = "I'm encapsulated!";
})();
```

---

## Object-Oriented Programming

### Prototypes & Classes

```
Prototype Chain
───────────────
myDog
  │
  ├── name: "Rex"
  ├── bark: [Function]
  │
  └── [[Prototype]] ──► Dog.prototype
                          │
                          ├── constructor: Dog
                          ├── toString: [Function]
                          │
                          └── [[Prototype]] ──► Object.prototype
                                                │
                                                └── [[Prototype]] ──► null
```

```javascript
// ES6 Class syntax (syntactic sugar over prototypes)
class Animal {
  #name;  // Private field (ES2022)

  constructor(name, sound) {
    this.#name = name;
    this.sound = sound;
  }

  // Getter
  get name() { return this.#name; }

  speak() {
    return `${this.#name} says ${this.sound}!`;
  }

  // Static method
  static kingdom() { return "Animalia"; }
}

class Dog extends Animal {
  #tricks = [];

  constructor(name) {
    super(name, "Woof");
  }

  learn(trick) {
    this.#tricks.push(trick);
    return this;  // method chaining
  }

  perform() {
    return this.#tricks.join(", ");
  }
}

const rex = new Dog("Rex");
rex.learn("sit").learn("shake").learn("roll over");
console.log(rex.speak());    // "Rex says Woof!"
console.log(rex.perform());  // "sit, shake, roll over"
console.log(Dog.kingdom());  // "Animalia"
```

---

