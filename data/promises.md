# JavaScript Promises

## What is a Promise?
A Promise in JavaScript is used to handle asynchronous operations. It represents a value that may be available now, later, or never.

## Key Points
- A Promise has three states: pending, fulfilled, rejected
- Helps avoid callback hell
- Used with async operations like APIs

## Example
```javascript
let promise = new Promise(function(resolve, reject) {
    let success = true;

    if (success) {
        resolve("Operation successful");
    } else {
        reject("Operation failed");
    }
});

promise
    .then(result => console.log(result))
    .catch(error => console.log(error));
##Practice

Create a Promise that resolves after 2 seconds and prints "Done".
