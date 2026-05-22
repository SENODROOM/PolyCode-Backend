# Express.js Framework

Express.js is a minimal and flexible Node.js web application framework that provides a robust set of features for web and mobile applications.

## What is Express.js?

Express.js simplifies the process of building web servers, APIs, and web applications in Node.js. It provides a thin layer of fundamental web application features without obscuring Node.js features.

### Key Features
- **Routing**: Simple and powerful routing system
- **Middleware**: Modular components for handling requests
- **HTTP Utilities**: Helper methods for HTTP operations
- **Template Engines**: Support for various template engines
- **Error Handling**: Built-in error handling mechanisms
- **Static File Serving**: Built-in static file server

## Getting Started

### Installation
```bash
# Create project directory
mkdir my-express-app
cd my-express-app

# Initialize npm project
npm init -y

# Install Express
npm install express

# Install development dependencies
npm install --save-dev nodemon
```

### Basic Express Server
```javascript
const express = require('express');
const app = express();
const PORT = process.env.PORT || 3000;

// Basic route
app.get('/', (req, res) => {
    res.send('Hello, Express!');
});

// Start server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
```

## Routing

### Basic Routes
```javascript
const express = require('express');
const app = express();

// GET route
app.get('/', (req, res) => {
    res.send('Home Page');
});

// GET route with parameter
app.get('/users/:id', (req, res) => {
    const userId = req.params.id;
    res.send(`User ID: ${userId}`);
});

// POST route
app.post('/users', (req, res) => {
    res.send('Create a new user');
});

// PUT route
app.put('/users/:id', (req, res) => {
    const userId = req.params.id;
    res.send(`Update user ${userId}`);
});

// DELETE route
app.delete('/users/:id', (req, res) => {
    const userId = req.params.id;
    res.send(`Delete user ${userId}`);
});
```

### Route Parameters
```javascript
// Single parameter
app.get('/users/:userId', (req, res) => {
    const { userId } = req.params;
    res.json({ userId });
});

// Multiple parameters
app.get('/users/:userId/posts/:postId', (req, res) => {
    const { userId, postId } = req.params;
    res.json({ userId, postId });
});

// Query parameters
app.get('/search', (req, res) => {
    const { q, page = 1, limit = 10 } = req.query;
    res.json({ query: q, page, limit });
});
```

### Route Handlers
```javascript
// Multiple handlers for same route
app.get('/users', (req, res, next) => {
    console.log('First handler');
    next(); // Pass to next handler
}, (req, res) => {
    res.send('Second handler');
});

// Array of handlers
const handlers = [
    (req, res, next) => {
        console.log('Handler 1');
        next();
    },
    (req, res, next) => {
        console.log('Handler 2');
        next();
    },
    (req, res) => {
        res.send('Final handler');
    }
];

app.get('/chain', handlers);
```

### Router Module
```javascript
// routes/users.js
const express = require('express');
const router = express.Router();

// Middleware specific to this router
router.use((req, res, next) => {
    console.log('User router middleware');
    next();
});

// Define routes
router.get('/', (req, res) => {
    res.json({ message: 'Users endpoint' });
});

router.get('/:id', (req, res) => {
    const { id } = req.params;
    res.json({ userId: id });
});

router.post('/', (req, res) => {
    res.status(201).json({ message: 'User created' });
});

module.exports = router;
```

```javascript
// app.js
const userRoutes = require('./routes/users');
app.use('/users', userRoutes);
```

## Middleware

### Built-in Middleware
```javascript
const express = require('express');
const app = express();

// Parse JSON bodies
app.use(express.json());

// Parse URL-encoded bodies
app.use(express.urlencoded({ extended: true }));

// Serve static files
app.use(express.static('public'));

// Serve files from specific directory
app.use('/static', express.static('public'));
```

### Custom Middleware
```javascript
// Logger middleware
const logger = (req, res, next) => {
    const timestamp = new Date().toISOString();
    console.log(`[${timestamp}] ${req.method} ${req.url}`);
    next();
};

// Authentication middleware
const authenticate = (req, res, next) => {
    const token = req.headers.authorization;
    
    if (!token) {
        return res.status(401).json({ error: 'No token provided' });
    }
    
    // Validate token (simplified)
    if (token === 'valid-token') {
        req.user = { id: 1, name: 'John Doe' };
        next();
    } else {
        res.status(401).json({ error: 'Invalid token' });
    }
};

// Error handling middleware
const errorHandler = (err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Something went wrong!' });
};

// Apply middleware
app.use(logger);
app.use('/protected', authenticate);
app.use(errorHandler);
```

### Third-party Middleware
```bash
# Install common middleware
npm install morgan cors helmet cookie-parser
```

```javascript
const morgan = require('morgan');
const cors = require('cors');
const helmet = require('helmet');
const cookieParser = require('cookie-parser');

// HTTP request logger
app.use(morgan('combined'));

// Enable CORS
app.use(cors({
    origin: 'http://localhost:3000',
    credentials: true
}));

// Security headers
app.use(helmet());

// Parse cookies
app.use(cookieParser());
```

## Request and Response

### Request Object
```javascript
app.post('/data', (req, res) => {
    // Get headers
    const userAgent = req.headers['user-agent'];
    const contentType = req.headers['content-type'];
    
    // Get query parameters
    const { search, page } = req.query;
    
    // Get route parameters
    const { id } = req.params;
    
    // Get request body (with express.json() middleware)
    const { name, email } = req.body;
    
    // Get cookies (with cookie-parser middleware)
    const sessionId = req.cookies.sessionId;
    
    console.log('Request details:', {
        userAgent,
        contentType,
        search,
        page,
        id,
        name,
        email,
        sessionId
    });
    
    res.json({ received: true });
});
```

### Response Object
```javascript
app.get('/responses', (req, res) => {
    // Send text response
    res.send('Hello World');
    
    // Send JSON response
    res.json({ message: 'Success', data: [1, 2, 3] });
    
    // Send status code
    res.status(404).send('Not Found');
    
    // Send file
    res.sendFile(__dirname + '/public/index.html');
    
    // Set headers
    res.set('Content-Type', 'application/json');
    res.set('X-Custom-Header', 'value');
    
    // Redirect
    res.redirect('/login');
    res.redirect(301, '/new-location');
    
    // End response
    res.end();
});
```

## Template Engines

### Setting up EJS
```bash
npm install ejs
```

```javascript
// Set view engine
app.set('view engine', 'ejs');
app.set('views', './views');

// Render template
app.get('/', (req, res) => {
    res.render('index', { 
        title: 'Home Page',
        user: { name: 'John', email: 'john@example.com' }
    });
});
```

```ejs
<!-- views/index.ejs -->
<!DOCTYPE html>
<html>
<head>
    <title><%= title %></title>
</head>
<body>
    <h1>Welcome <%= user.name %></h1>
    <p>Email: <%= user.email %></p>
</body>
</html>
```

## Error Handling

### Synchronous Errors
```javascript
app.get('/error', (req, res) => {
    throw new Error('Something went wrong!');
});
```

### Asynchronous Errors
```javascript
app.get('/async-error', async (req, res, next) => {
    try {
        await someAsyncOperation();
        res.send('Success');
    } catch (error) {
        next(error); // Pass to error handler
    }
});
```

### Error Handling Middleware
```javascript
// 404 handler
app.use((req, res, next) => {
    res.status(404).json({ error: 'Not Found' });
});

// General error handler
app.use((err, req, res, next) => {
    console.error(err.stack);
    
    // Don't send error details in production
    const isDevelopment = process.env.NODE_ENV === 'development';
    
    res.status(err.status || 500).json({
        error: err.message,
        ...(isDevelopment && { stack: err.stack })
    });
});
```

## RESTful API Example

### Complete API Structure
```javascript
const express = require('express');
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(morgan('combined'));

// In-memory data store
let users = [
    { id: 1, name: 'John Doe', email: 'john@example.com' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com' }
];
let nextId = 3;

// Routes

// GET all users
app.get('/api/users', (req, res) => {
    res.json(users);
});

// GET user by ID
app.get('/api/users/:id', (req, res) => {
    const user = users.find(u => u.id === parseInt(req.params.id));
    
    if (!user) {
        return res.status(404).json({ error: 'User not found' });
    }
    
    res.json(user);
});

// POST create user
app.post('/api/users', (req, res) => {
    const { name, email } = req.body;
    
    if (!name || !email) {
        return res.status(400).json({ 
            error: 'Name and email are required' 
        });
    }
    
    const newUser = {
        id: nextId++,
        name,
        email
    };
    
    users.push(newUser);
    
    res.status(201).json(newUser);
});

// PUT update user
app.put('/api/users/:id', (req, res) => {
    const user = users.find(u => u.id === parseInt(req.params.id));
    
    if (!user) {
        return res.status(404).json({ error: 'User not found' });
    }
    
    const { name, email } = req.body;
    
    if (name) user.name = name;
    if (email) user.email = email;
    
    res.json(user);
});

// DELETE user
app.delete('/api/users/:id', (req, res) => {
    const userIndex = users.findIndex(u => u.id === parseInt(req.params.id));
    
    if (userIndex === -1) {
        return res.status(404).json({ error: 'User not found' });
    }
    
    users.splice(userIndex, 1);
    
    res.status(204).send();
});

// Error handling
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Internal server error' });
});

// Start server
app.listen(PORT, () => {
    console.log(`API server running on port ${PORT}`);
});
```

## Testing Express Apps

### Basic Testing with Jest
```bash
npm install --save-dev jest supertest
```

```javascript
// tests/users.test.js
const request = require('supertest');
const app = require('../app');

describe('Users API', () => {
    test('GET /api/users should return all users', async () => {
        const response = await request(app)
            .get('/api/users')
            .expect(200);
        
        expect(Array.isArray(response.body)).toBe(true);
        expect(response.body.length).toBeGreaterThan(0);
    });
    
    test('POST /api/users should create a new user', async () => {
        const newUser = {
            name: 'Test User',
            email: 'test@example.com'
        };
        
        const response = await request(app)
            .post('/api/users')
            .send(newUser)
            .expect(201);
        
        expect(response.body.name).toBe(newUser.name);
        expect(response.body.email).toBe(newUser.email);
        expect(response.body.id).toBeDefined();
    });
});
```
