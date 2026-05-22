## Syntax & Fundamentals

### Variables

```javascript
// Three ways to declare variables
var   oldWay  = "function-scoped, hoisted (avoid)";
let   mutable = "block-scoped, can be reassigned";
const fixed   = "block-scoped, cannot be reassigned";

// Temporal Dead Zone — let/const are NOT accessible before declaration
// console.log(x); // ❌ ReferenceError
let x = 5;
```

### Operators

```javascript
// Arithmetic
let a = 10 + 3;   // 13
let b = 10 ** 2;  // 100 (exponentiation)
let c = 10 % 3;   // 1  (modulo)

// Comparison — ALWAYS use === (strict equality)
5 == "5"   // true  ⚠️  (type coercion)
5 === "5"  // false ✅  (no coercion)
5 !== 6    // true

// Logical
true && false  // false
true || false  // true
!true          // false

// Nullish coalescing (??)
null ?? "default"      // "default"
undefined ?? "default" // "default"
0 ?? "default"         // 0 (only null/undefined trigger it)

// Optional chaining (?.)
const user = null;
user?.profile?.name  // undefined (no error!)
```

---

## Data Types

```
JavaScript Types
│
├── Primitives (7 types — immutable, stored by value)
│   ├── number       42, 3.14, NaN, Infinity
│   ├── string       "hello", 'world', `template`
│   ├── boolean      true / false
│   ├── null         intentional absence of value
│   ├── undefined    variable declared but not assigned
│   ├── symbol       unique identifier (ES6)
│   └── bigint       arbitrary precision integers (ES2020)
│
└── Objects (mutable, stored by reference)
    ├── Object       { key: value }
    ├── Array        [1, 2, 3]
    ├── Function     function() {}
    ├── Date         new Date()
    ├── RegExp       /pattern/
    └── Map/Set      new Map(), new Set()
```

### Type Coercion (The Famous Quirks)

```javascript
// JavaScript's infamous type coercion
"5" + 3       // "53"  (number → string)
"5" - 3       // 2     (string → number)
true + true   // 2
[] + []       // ""
[] + {}       // "[object Object]"
{} + []       // 0  (in some contexts!)

typeof null   // "object"  ← historical bug, kept for compatibility
typeof NaN    // "number"  ← NaN is ironically a number type

// Use explicit conversion
Number("42")   // 42
String(42)     // "42"
Boolean(0)     // false
```

---

## Control Flow

```javascript
// if / else if / else
if (score >= 90) {
  grade = "A";
} else if (score >= 80) {
  grade = "B";
} else {
  grade = "F";
}

// Ternary operator
const status = age >= 18 ? "adult" : "minor";

// Switch
switch (day) {
  case "Monday":
    console.log("Start of the week");
    break;
  case "Friday":
    console.log("TGIF!");
    break;
  default:
    console.log("Midweek");
}

// Loops
for (let i = 0; i < 5; i++) { /* classic */ }
while (condition) { /* while */ }
do { /* at least once */ } while (condition);

// Modern iteration
for (const item of [1, 2, 3]) { /* iterables */ }
for (const key in object)      { /* object keys */ }

// Array methods (preferred over loops)
const nums = [1, 2, 3, 4, 5];
nums.map(n => n * 2);         // [2, 4, 6, 8, 10]
nums.filter(n => n % 2 === 0); // [2, 4]
nums.reduce((acc, n) => acc + n, 0); // 15
nums.find(n => n > 3);        // 4
nums.every(n => n > 0);       // true
nums.some(n => n > 4);        // true
```

---

