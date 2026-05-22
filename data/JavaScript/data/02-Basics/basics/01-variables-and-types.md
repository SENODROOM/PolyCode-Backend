# JavaScript Variables and Data Types

## Variable Declaration
JavaScript provides three ways to declare variables:

### `var` (Function Scope)
```javascript
var message = "Hello";
var count = 42;
```

### `let` (Block Scope)
```javascript
let username = "john_doe";
let score = 95;
```

### `const` (Block Scope, Immutable)
```javascript
const API_KEY = "abc123";
const MAX_USERS = 1000;
```

## Data Types

### Primitive Types
1. **String**: Text enclosed in quotes
   ```javascript
   let name = "JavaScript";
   let greeting = 'Hello World';
   ```

2. **Number**: Integers and floats
   ```javascript
   let age = 25;
   let price = 19.99;
   let pi = 3.14159;
   ```

3. **Boolean**: true or false
   ```javascript
   let isLoggedIn = true;
   let hasError = false;
   ```

4. **Undefined**: Variable declared but not assigned
   ```javascript
   let result; // undefined
   ```

5. **Null**: Intentional empty value
   ```javascript
   let emptyValue = null;
   ```

6. **Symbol**: Unique identifier
   ```javascript
   let id = Symbol('description');
   ```

7. **BigInt**: Large numbers
   ```javascript
   let bigNumber = 9007199254740991n;
   ```

### Reference Types
- **Object**: Key-value pairs
- **Array**: Ordered list
- **Function**: Executable code block

## Type Checking
```javascript
typeof variable; // Returns the data type
Array.isArray(array); // Check if array
```
