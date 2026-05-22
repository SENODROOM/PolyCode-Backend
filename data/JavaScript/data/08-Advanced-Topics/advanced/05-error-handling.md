# JavaScript Error Handling

Proper error handling is essential for building robust and maintainable JavaScript applications.

## Error Types

### Built-in Error Types
```javascript
// SyntaxError: Invalid syntax
try {
    eval("let x = ;"); // Invalid syntax
} catch (error) {
    console.log(error instanceof SyntaxError); // true
}

// ReferenceError: Undefined variable
try {
    console.log(undefinedVariable);
} catch (error) {
    console.log(error instanceof ReferenceError); // true
}

// TypeError: Wrong type operation
try {
    null.method(); // null has no methods
} catch (error) {
    console.log(error instanceof TypeError); // true
}

// RangeError: Number out of range
try {
    new Array(-1); // Invalid array length
} catch (error) {
    console.log(error instanceof RangeError); // true
}

// URIError: URI manipulation errors
try {
    decodeURIComponent('%'); // Invalid URI
} catch (error) {
    console.log(error instanceof URIError); // true
}
```

### Custom Errors
```javascript
class CustomError extends Error {
    constructor(message, code) {
        super(message);
        this.name = "CustomError";
        this.code = code;
    }
}

class ValidationError extends Error {
    constructor(field, message) {
        super(`Validation failed for ${field}: ${message}`);
        this.name = "ValidationError";
        this.field = field;
    }
}

// Using custom errors
function validateEmail(email) {
    if (!email.includes('@')) {
        throw new ValidationError('email', 'must contain @ symbol');
    }
    return true;
}
```

## Try-Catch-Finally

### Basic Structure
```javascript
try {
    // Code that might throw an error
    riskyOperation();
} catch (error) {
    // Handle the error
    console.error("Error occurred:", error.message);
} finally {
    // Always executes (cleanup)
    console.log("Cleanup code");
}
```

### Nested Try-Catch
```javascript
function processData(data) {
    try {
        try {
            // Parse JSON
            const parsed = JSON.parse(data);
            return parsed;
        } catch (parseError) {
            // Handle JSON parsing error
            console.log("Invalid JSON format");
            return null;
        }
    } catch (error) {
        // Handle other errors
        console.log("Unexpected error:", error.message);
        throw error; // Re-throw if needed
    }
}
```

### Finally Block Behavior
```javascript
function testFinally() {
    try {
        console.log("Try block");
        return "from try";
    } catch (error) {
        console.log("Catch block");
        return "from catch";
    } finally {
        console.log("Finally block"); // Always executes
        return "from finally"; // Overrides other returns
    }
}

console.log(testFinally()); // "Finally block" then "from finally"
```

## Throwing Errors

### Throwing Built-in Errors
```javascript
function divide(a, b) {
    if (typeof a !== 'number' || typeof b !== 'number') {
        throw new TypeError("Both arguments must be numbers");
    }
    
    if (b === 0) {
        throw new Error("Division by zero is not allowed");
    }
    
    return a / b;
}
```

### Throwing Custom Errors
```javascript
class APIError extends Error {
    constructor(message, statusCode) {
        super(message);
        this.name = "APIError";
        this.statusCode = statusCode;
    }
}

async function fetchUserData(userId) {
    if (!userId) {
        throw new APIError("User ID is required", 400);
    }
    
    try {
        const response = await fetch(`/api/users/${userId}`);
        
        if (!response.ok) {
            throw new APIError(`Failed to fetch user: ${response.status}`, response.status);
        }
        
        return await response.json();
    } catch (error) {
        if (error instanceof APIError) {
            throw error;
        }
        throw new APIError("Network error", 500);
    }
}
```

## Error Handling Patterns

### Error-First Callbacks (Node.js style)
```javascript
function readFile(filename, callback) {
    setTimeout(() => {
        const success = Math.random() > 0.5;
        
        if (success) {
            callback(null, "File content");
        } else {
            callback(new Error("File not found"));
        }
    }, 1000);
}

readFile("example.txt", (error, content) => {
    if (error) {
        console.error("Error:", error.message);
        return;
    }
    console.log("Content:", content);
});
```

### Promise Error Handling
```javascript
function fetchData() {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            const success = Math.random() > 0.5;
            
            if (success) {
                resolve({ data: "success" });
            } else {
                reject(new Error("Network error"));
            }
        }, 1000);
    });
}

// Method 1: catch()
fetchData()
    .then(data => console.log(data))
    .catch(error => console.error("Error:", error.message));

// Method 2: try-catch with async/await
async function getData() {
    try {
        const data = await fetchData();
        console.log(data);
    } catch (error) {
        console.error("Error:", error.message);
    }
}
```

### Global Error Handling
```javascript
// Browser global error handler
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
    // Send to error tracking service
});

// Unhandled promise rejection
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
    event.preventDefault(); // Prevent default browser behavior
});

// Node.js global error handlers
process.on('uncaughtException', (error) => {
    console.error('Uncaught Exception:', error);
    // Graceful shutdown
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});
```

## Best Practices

### Specific Error Handling
```javascript
// Good: Handle specific errors
try {
    const result = riskyOperation();
} catch (error) {
    if (error instanceof ValidationError) {
        // Handle validation errors
        showUserError(error.message);
    } else if (error instanceof NetworkError) {
        // Handle network errors
        retryOperation();
    } else {
        // Handle unexpected errors
        logError(error);
        showGenericError();
    }
}

// Avoid: Generic error handling
try {
    riskyOperation();
} catch (error) {
    console.log("Something went wrong"); // Too generic
}
```

### Error Logging
```javascript
class Logger {
    static log(error, context = {}) {
        const logEntry = {
            timestamp: new Date().toISOString(),
            message: error.message,
            stack: error.stack,
            context,
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        console.error('Error logged:', logEntry);
        
        // Send to logging service
        this.sendToService(logEntry);
    }
    
    static sendToService(logEntry) {
        // Implementation for sending logs to external service
        fetch('/api/logs', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logEntry)
        }).catch(console.error);
    }
}

// Usage
try {
    riskyOperation();
} catch (error) {
    Logger.log(error, { userId: currentUser.id, action: 'data-processing' });
}
```

### Graceful Degradation
```javascript
function loadUserData() {
    try {
        const data = localStorage.getItem('userData');
        return JSON.parse(data);
    } catch (error) {
        console.warn('Failed to load user data from localStorage:', error.message);
        
        // Fallback to default data
        return {
            name: 'Guest',
            preferences: {}
        };
    }
}

function initializeApp() {
    try {
        // Primary initialization
        primaryInit();
    } catch (error) {
        console.error('Primary initialization failed:', error);
        
        try {
            // Fallback initialization
            fallbackInit();
        } catch (fallbackError) {
            console.error('Fallback initialization failed:', fallbackError);
            // Minimal initialization
            minimalInit();
        }
    }
}
```
