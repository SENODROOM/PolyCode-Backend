# JavaScript Async Programming

Asynchronous programming allows JavaScript to handle long-running operations without blocking the main thread.

## Callbacks

### Basic Callback Pattern
```javascript
function fetchData(callback) {
    setTimeout(() => {
        const data = { id: 1, name: "John" };
        callback(data);
    }, 1000);
}

fetchData((result) => {
    console.log("Data received:", result);
});
```

### Callback Hell (Pyramid of Doom)
```javascript
getData(function(a) {
    getMoreData(a, function(b) {
        getMoreData(b, function(c) {
            getMoreData(c, function(d) {
                console.log("Final data:", d);
            });
        });
    });
});
```

## Promises

### Creating Promises
```javascript
const promise = new Promise((resolve, reject) => {
    setTimeout(() => {
        const success = true;
        if (success) {
            resolve("Operation successful!");
        } else {
            reject("Operation failed!");
        }
    }, 1000);
});
```

### Using Promises
```javascript
promise
    .then(result => {
        console.log("Success:", result);
        return "Next step";
    })
    .then(result => {
        console.log("Chained:", result);
    })
    .catch(error => {
        console.log("Error:", error);
    })
    .finally(() => {
        console.log("Cleanup");
    });
```

### Promise Methods
```javascript
// Promise.all: All promises must resolve
Promise.all([promise1, promise2, promise3])
    .then(results => console.log(results));

// Promise.race: First promise to settle
Promise.race([promise1, promise2])
    .then(result => console.log(result));

// Promise.allSettled: Wait for all promises to settle
Promise.allSettled([promise1, promise2])
    .then(results => console.log(results));
```

## Async/Await

### Basic Syntax
```javascript
async function fetchData() {
    try {
        const response = await fetch('https://api.example.com/data');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Error:", error);
    }
}

fetchData().then(data => console.log(data));
```

### Async Arrow Functions
```javascript
const getData = async () => {
    const data = await fetchAPI();
    return data;
};
```

### Parallel Async Operations
```javascript
async function fetchMultiple() {
    const [users, posts, comments] = await Promise.all([
        fetch('/api/users'),
        fetch('/api/posts'),
        fetch('/api/comments')
    ]);
    
    return { users, posts, comments };
}
```

## Error Handling in Async Code

### Try-Catch with Async/Await
```javascript
async function handleAsync() {
    try {
        const result = await riskyOperation();
        return result;
    } catch (error) {
        console.log("Caught error:", error.message);
        throw error; // Re-throw if needed
    }
}
```

### Error Handling with Promises
```javascript
fetchData()
    .then(data => processData(data))
    .catch(error => {
        if (error instanceof NetworkError) {
            console.log("Network issue");
        } else {
            console.log("Other error:", error);
        }
    });
```

## Real-World Examples

### API Call with Async/Await
```javascript
async function getUserData(userId) {
    try {
        const response = await fetch(`https://jsonplaceholder.typicode.com/users/${userId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const user = await response.json();
        return user;
    } catch (error) {
        console.error("Failed to fetch user:", error);
        return null;
    }
}
```

### Sequential vs Parallel Operations
```javascript
// Sequential (slower)
async function sequential() {
    const user = await getUser(1);
    const posts = await getUserPosts(user.id);
    const comments = await getPostComments(posts[0].id);
    return { user, posts, comments };
}

// Parallel (faster)
async function parallel() {
    const [user, posts, comments] = await Promise.all([
        getUser(1),
        getUserPosts(1),
        getPostComments(1)
    ]);
    return { user, posts, comments };
}
```
