# JavaScript Prototypes and Inheritance

JavaScript uses prototypal inheritance, where objects can inherit directly from other objects.

## Prototypes

### Understanding Prototypes
```javascript
// Every object has a prototype
const person = {
    name: "John",
    age: 30
};

console.log(person.__proto__); // Object.prototype
console.log(Object.getPrototypeOf(person)); // Same as __proto__
```

### Prototype Chain
```javascript
const animal = {
    eats: true
};

const rabbit = {
    jumps: true
};

// Set rabbit's prototype to animal
Object.setPrototypeOf(rabbit, animal);

console.log(rabbit.eats); // true (inherited from animal)
console.log(rabbit.jumps); // true (own property)
```

### Constructor Functions and Prototypes
```javascript
function Person(name, age) {
    this.name = name;
    this.age = age;
}

// Add methods to prototype
Person.prototype.greet = function() {
    return `Hello, I'm ${this.name}`;
};

Person.prototype.celebrateBirthday = function() {
    this.age++;
    return `Happy ${this.age}th birthday!`;
};

const john = new Person("John", 30);
console.log(john.greet()); // "Hello, I'm John"
console.log(john.celebrateBirthday()); // "Happy 31st birthday!"
```

## Inheritance Patterns

### Prototype Inheritance
```javascript
function Animal(name) {
    this.name = name;
}

Animal.prototype.eat = function() {
    return `${this.name} is eating`;
};

function Dog(name, breed) {
    Animal.call(this, name); // Call parent constructor
    this.breed = breed;
}

// Inherit from Animal
Dog.prototype = Object.create(Animal.prototype);
Dog.prototype.constructor = Dog;

// Add Dog-specific methods
Dog.prototype.bark = function() {
    return `${this.name} barks`;
};

const dog = new Dog("Rex", "Labrador");
console.log(dog.eat()); // "Rex is eating" (inherited)
console.log(dog.bark()); // "Rex barks" (own method)
```

### ES6 Classes (Syntactic Sugar)
```javascript
class Animal {
    constructor(name) {
        this.name = name;
    }
    
    eat() {
        return `${this.name} is eating`;
    }
    
    static getSpecies() {
        return "Animal";
    }
}

class Dog extends Animal {
    constructor(name, breed) {
        super(name); // Call parent constructor
        this.breed = breed;
    }
    
    bark() {
        return `${this.name} barks`;
    }
    
    // Override parent method
    eat() {
        return `${this.name} (a ${this.breed}) is eating`;
    }
}

const dog = new Dog("Buddy", "Golden Retriever");
console.log(dog.eat()); // "Buddy (a Golden Retriever) is eating"
console.log(dog.bark()); // "Buddy barks"
```

## Prototype Methods

### Checking Properties
```javascript
const person = { name: "John" };
Object.setPrototypeOf(person, { age: 30 });

// hasOwnProperty: Check own properties
console.log(person.hasOwnProperty('name')); // true
console.log(person.hasOwnProperty('age')); // false

// in operator: Check own and inherited properties
console.log('name' in person); // true
console.log('age' in person); // true
```

### Getting and Setting Prototypes
```javascript
const parent = { parentMethod: function() { return "parent"; } };
const child = { childMethod: function() { return "child"; } };

// Set prototype
Object.setPrototypeOf(child, parent);

console.log(child.parentMethod()); // "parent"
console.log(child.childMethod()); // "child"

// Get prototype
const proto = Object.getPrototypeOf(child);
console.log(proto === parent); // true
```

### Creating Objects with Specific Prototype
```javascript
const personProto = {
    greet: function() {
        return `Hello, I'm ${this.name}`;
    }
};

const person = Object.create(personProto);
person.name = "John";
console.log(person.greet()); // "Hello, I'm John"

// Create with properties
const employee = Object.create(personProto, {
    name: { value: "Jane", writable: true },
    position: { value: "Developer", enumerable: true }
});
```

## Advanced Prototype Concepts

### Property Descriptors
```javascript
const obj = {};

// Define property with descriptor
Object.defineProperty(obj, 'secret', {
    value: 'hidden',
    writable: false,
    enumerable: false,
    configurable: false
});

console.log(obj.secret); // 'hidden'
obj.secret = 'new'; // Fails (writable: false)
console.log(Object.keys(obj)); // [] (enumerable: false)
```

### Multiple Inheritance (Mixins)
```javascript
const canFly = {
    fly() {
        return `${this.name} is flying`;
    }
};

const canSwim = {
    swim() {
        return `${this.name} is swimming`;
    }
};

class Duck {
    constructor(name) {
        this.name = name;
    }
}

// Mix in capabilities
Object.assign(Duck.prototype, canFly, canSwim);

const duck = new Duck("Donald");
console.log(duck.fly()); // "Donald is flying"
console.log(duck.swim()); // "Donald is swimming"
```

### Prototype Pollution Prevention
```javascript
// Dangerous: Modifying Object.prototype
Object.prototype.badMethod = function() {
    return "This affects all objects!";
};

// Safe: Use specific prototype
function MyConstructor() {}
MyConstructor.prototype.goodMethod = function() {
    return "Only affects instances";
};

// Or use Object.create with null prototype
const safeObj = Object.create(null);
```

## Performance Considerations

### Prototype Property Lookup
```javascript
// Fast: Own property lookup
const obj = { ownProp: 'value' };
console.log(obj.ownProp); // Direct access

// Slower: Prototype chain lookup
const parent = { inheritedProp: 'value' };
const child = Object.create(parent);
console.log(child.inheritedProp); // Traverses prototype chain
```

### Optimizing for Performance
```javascript
// Good: Methods on prototype
function MyClass() {}
MyClass.prototype.method = function() { /* ... */ };

// Avoid: Methods in constructor (creates new function each time)
function BadClass() {
    this.method = function() { /* ... */ };
}

// For hot code paths, consider caching prototype lookups
const protoMethod = MyClass.prototype.method;
function fastFunction() {
    return protoMethod.call(this);
}
```
