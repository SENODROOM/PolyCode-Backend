# JavaScript Closures and Scope

Understanding closures and scope is crucial for mastering JavaScript's memory management and function behavior.

## Scope

### Global Scope
```javascript
// Variables declared outside any function
const globalVar = "I'm global";

function showGlobal() {
    console.log(globalVar); // Can access global variables
}

showGlobal(); // "I'm global"
console.log(globalVar); // "I'm global"
```

### Function Scope (var)
```javascript
function functionScope() {
    var functionVar = "I'm function-scoped";
    
    if (true) {
        var stillFunctionScoped = "Still function-scoped";
    }
    
    console.log(stillFunctionScoped); // Accessible inside function
}

// console.log(functionVar); // Error: not defined
```

### Block Scope (let/const)
```javascript
function blockScope() {
    let blockVar = "I'm block-scoped";
    
    if (true) {
        const ifBlockVar = "I'm if-block-scoped";
        console.log(ifBlockVar); // Accessible here
    }
    
    // console.log(ifBlockVar); // Error: not defined
}
```

### Lexical Scope
```javascript
const outer = "I'm outer";

function outerFunction() {
    const inner = "I'm inner";
    
    function innerFunction() {
        console.log(outer); // Can access outer variables
        console.log(inner); // Can access parent function variables
    }
    
    return innerFunction;
}

const func = outerFunction();
func(); // Logs both outer and inner variables
```

## Closures

### What is a Closure?
A closure is a function that remembers its outer variables even after the outer function has finished executing.

```javascript
function createCounter() {
    let count = 0; // Private variable
    
    return function() {
        count++; // Accesses outer variable
        return count;
    };
}

const counter1 = createCounter();
const counter2 = createCounter();

console.log(counter1()); // 1
console.log(counter1()); // 2
console.log(counter2()); // 1 (independent counter)
```

### Practical Closure Examples

#### Private Variables
```javascript
function createBankAccount(initialBalance) {
    let balance = initialBalance; // Private variable
    
    return {
        deposit: function(amount) {
            balance += amount;
            return balance;
        },
        
        withdraw: function(amount) {
            if (amount <= balance) {
                balance -= amount;
                return balance;
            }
            return "Insufficient funds";
        },
        
        getBalance: function() {
            return balance;
        }
    };
}

const account = createBankAccount(1000);
console.log(account.deposit(500)); // 1500
console.log(account.withdraw(200)); // 1300
console.log(account.getBalance()); // 1300
```

#### Function Factory
```javascript
function createMultiplier(factor) {
    return function(number) {
        return number * factor;
    };
}

const double = createMultiplier(2);
const triple = createMultiplier(3);

console.log(double(5)); // 10
console.log(triple(5)); // 15
```

#### Event Handlers
```javascript
function attachListeners() {
    const buttons = document.querySelectorAll('button');
    
    for (let i = 0; i < buttons.length; i++) {
        // Each button gets its own closure with correct i
        buttons[i].addEventListener('click', function() {
            console.log(`Button ${i} clicked`);
        });
    }
}
```

### Closure Pitfalls

#### Loop Problem with var
```javascript
// Problematic code with var
for (var i = 0; i < 3; i++) {
    setTimeout(function() {
        console.log(i); // Logs 3, 3, 3 (not 0, 1, 2)
    }, 100);
}

// Solution 1: IIFE
for (var i = 0; i < 3; i++) {
    (function(index) {
        setTimeout(function() {
            console.log(index); // Logs 0, 1, 2
        }, 100);
    })(i);
}

// Solution 2: let (ES6)
for (let i = 0; i < 3; i++) {
    setTimeout(function() {
        console.log(i); // Logs 0, 1, 2
    }, 100);
}
```

## Memory Management

### Closure Memory Leaks
```javascript
// Potential memory leak
function setupEventListeners() {
    const largeData = new Array(1000000).fill('data');
    
    document.addEventListener('click', function handler() {
        // This closure keeps largeData in memory
        console.log('Clicked');
    });
    
    // Solution: Remove event listener when done
    return function cleanup() {
        document.removeEventListener('click', handler);
    };
}

const cleanup = setupEventListeners();
// Later: cleanup() to free memory
```

### Weak References
```javascript
// Using WeakMap to avoid memory leaks
const weakMap = new WeakMap();

function createObjectWithMetadata(obj) {
    const metadata = { created: new Date() };
    weakMap.set(obj, metadata);
    
    return function getMetadata() {
        return weakMap.get(obj);
    };
}

// When obj is garbage collected, metadata is also collected
```

## Advanced Closure Patterns

### Memoization
```javascript
function memoize(fn) {
    const cache = new Map();
    
    return function(...args) {
        const key = JSON.stringify(args);
        
        if (cache.has(key)) {
            return cache.get(key);
        }
        
        const result = fn.apply(this, args);
        cache.set(key, result);
        return result;
    };
}

const expensiveFunction = memoize(function(n) {
    console.log('Computing...');
    return n * n;
});

console.log(expensiveFunction(5)); // Computing... 25
console.log(expensiveFunction(5)); // 25 (from cache)
```

### Currying
```javascript
function curry(fn) {
    return function curried(...args) {
        if (args.length >= fn.length) {
            return fn.apply(this, args);
        }
        
        return function(...nextArgs) {
            return curried.apply(this, args.concat(nextArgs));
        };
    };
}

const add = (a, b, c) => a + b + c;
const curriedAdd = curry(add);

console.log(curriedAdd(1)(2)(3)); // 6
console.log(curriedAdd(1, 2)(3)); // 6
```
