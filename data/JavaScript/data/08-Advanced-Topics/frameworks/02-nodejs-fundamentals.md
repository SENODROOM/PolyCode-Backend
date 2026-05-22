# Node.js Fundamentals

Node.js is a JavaScript runtime built on Chrome's V8 JavaScript engine that allows you to run JavaScript on the server side.

## What is Node.js?

Node.js enables JavaScript to run outside the browser, making it possible to:
- Build web servers and APIs
- Work with file systems
- Interact with databases
- Create command-line tools
- Build real-time applications

### Key Features
- **Asynchronous and Event-Driven**: Non-blocking I/O operations
- **Single-Threaded**: Uses event loop for concurrency
- **Cross-Platform**: Runs on Windows, macOS, and Linux
- **NPM**: Largest package ecosystem
- **V8 Engine**: High-performance JavaScript execution

## Getting Started

### Installation
```bash
# Download from https://nodejs.org or use version manager
# Check installation
node --version
npm --version
```

### Running Node.js Files
```bash
# Create and run a file
echo "console.log('Hello Node.js!')" > app.js
node app.js
```

## Core Modules

### File System (fs)
```javascript
const fs = require('fs');

// Read file asynchronously
fs.readFile('example.txt', 'utf8', (err, data) => {
    if (err) {
        console.error('Error reading file:', err);
        return;
    }
    console.log('File content:', data);
});

// Read file synchronously
try {
    const data = fs.readFileSync('example.txt', 'utf8');
    console.log('File content:', data);
} catch (err) {
    console.error('Error reading file:', err);
}

// Write file
fs.writeFile('output.txt', 'Hello Node.js!', 'utf8', (err) => {
    if (err) {
        console.error('Error writing file:', err);
        return;
    }
    console.log('File written successfully');
});

// Check if file exists
fs.access('example.txt', fs.constants.F_OK, (err) => {
    if (err) {
        console.log('File does not exist');
    } else {
        console.log('File exists');
    }
});
```

### Path Module
```javascript
const path = require('path');

const filePath = '/home/user/documents/file.txt';

console.log('Directory:', path.dirname(filePath)); // /home/user/documents
console.log('Filename:', path.basename(filePath)); // file.txt
console.log('Extension:', path.extname(filePath)); // .txt
console.log('Parsed:', path.parse(filePath));

// Join paths
const fullPath = path.join('/home', 'user', 'documents', 'file.txt');
console.log('Joined path:', fullPath);

// Resolve absolute path
const absolutePath = path.resolve('documents', 'file.txt');
console.log('Absolute path:', absolutePath);
```

### HTTP Module
```javascript
const http = require('http');

// Create a simple HTTP server
const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('Hello, World!');
});

const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}/`);
});

// Handle different routes
const server = http.createServer((req, res) => {
    const url = req.url;
    const method = req.method;

    if (url === '/' && method === 'GET') {
        res.writeHead(200, { 'Content-Type': 'text/html' });
        res.end('<h1>Welcome to the Homepage!</h1>');
    } else if (url === '/about' && method === 'GET') {
        res.writeHead(200, { 'Content-Type': 'text/html' });
        res.end('<h1>About Us</h1>');
    } else {
        res.writeHead(404, { 'Content-Type': 'text/html' });
        res.end('<h1>404 Not Found</h1>');
    }
});
```

### URL Module
```javascript
const url = require('url');

const urlString = 'https://example.com:8080/path/page?query=value#section';

const parsedUrl = url.parse(urlString);
console.log('Protocol:', parsedUrl.protocol); // https:
console.log('Hostname:', parsedUrl.hostname); // example.com
console.log('Port:', parsedUrl.port); // 8080
console.log('Pathname:', parsedUrl.pathname); // /path/page
console.log('Query:', parsedUrl.query); // query=value

// Modern URL API (Node.js 10+)
const myURL = new URL(urlString);
console.log('Search params:', myURL.searchParams.get('query')); // value
```

## Asynchronous Programming

### Callbacks
```javascript
const fs = require('fs');

// Callback pattern
fs.readFile('file1.txt', 'utf8', (err, data1) => {
    if (err) {
        console.error('Error reading file1:', err);
        return;
    }
    
    fs.readFile('file2.txt', 'utf8', (err, data2) => {
        if (err) {
            console.error('Error reading file2:', err);
            return;
        }
        
        console.log('Both files read successfully');
        console.log('File1:', data1);
        console.log('File2:', data2);
    });
});
```

### Promises
```javascript
const fs = require('fs').promises;

// Promise-based file operations
async function readFiles() {
    try {
        const data1 = await fs.readFile('file1.txt', 'utf8');
        const data2 = await fs.readFile('file2.txt', 'utf8');
        
        console.log('File1:', data1);
        console.log('File2:', data2);
    } catch (error) {
        console.error('Error reading files:', error);
    }
}

readFiles();
```

### Event Emitter
```javascript
const EventEmitter = require('events');

class MyEmitter extends EventEmitter {}

const myEmitter = new MyEmitter();

// Listen for events
myEmitter.on('event', () => {
    console.log('An event occurred!');
});

myEmitter.on('data', (data) => {
    console.log('Received data:', data);
});

// Emit events
myEmitter.emit('event');
myEmitter.emit('data', { message: 'Hello World' });

// Once listener
myEmitter.once('welcome', () => {
    console.log('Welcome event (will only fire once)');
});

myEmitter.emit('welcome');
myEmitter.emit('welcome'); // Won't fire again
```

## NPM (Node Package Manager)

### Package Management
```bash
# Initialize a new project
npm init
npm init -y  # Skip questions

# Install packages
npm install express          # Local dependency
npm install -g nodemon       # Global package
npm install --save-dev jest  # Development dependency

# Install from package.json
npm install

# Update packages
npm update

# Uninstall packages
npm uninstall express

# List installed packages
npm list
npm list --global
```

### package.json
```json
{
  "name": "my-node-app",
  "version": "1.0.0",
  "description": "A sample Node.js application",
  "main": "app.js",
  "scripts": {
    "start": "node app.js",
    "dev": "nodemon app.js",
    "test": "jest"
  },
  "dependencies": {
    "express": "^4.18.0",
    "lodash": "^4.17.21"
  },
  "devDependencies": {
    "nodemon": "^2.0.15",
    "jest": "^27.5.1"
  },
  "keywords": ["node", "javascript", "api"],
  "author": "Your Name",
  "license": "MIT"
}
```

## Building a Simple Web Server

### Basic Express Server
```javascript
// Install express: npm install express
const express = require('express');
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json()); // Parse JSON bodies
app.use(express.urlencoded({ extended: true })); // Parse URL-encoded bodies

// Routes
app.get('/', (req, res) => {
    res.json({ message: 'Welcome to the API!' });
});

app.get('/users', (req, res) => {
    const users = [
        { id: 1, name: 'John Doe', email: 'john@example.com' },
        { id: 2, name: 'Jane Smith', email: 'jane@example.com' }
    ];
    res.json(users);
});

app.post('/users', (req, res) => {
    const { name, email } = req.body;
    
    if (!name || !email) {
        return res.status(400).json({ error: 'Name and email are required' });
    }
    
    const newUser = {
        id: Date.now(),
        name,
        email
    };
    
    res.status(201).json(newUser);
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Something went wrong!' });
});

// Start server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
```

## File System Operations

### Working with Directories
```javascript
const fs = require('fs');
const path = require('path');

// Create directory
fs.mkdir('my-directory', (err) => {
    if (err) {
        console.error('Error creating directory:', err);
        return;
    }
    console.log('Directory created successfully');
});

// Read directory contents
fs.readdir('.', (err, files) => {
    if (err) {
        console.error('Error reading directory:', err);
        return;
    }
    
    console.log('Directory contents:');
    files.forEach(file => {
        console.log(file);
    });
});

// Check if path is a file or directory
fs.stat('example.txt', (err, stats) => {
    if (err) {
        console.error('Error getting stats:', err);
        return;
    }
    
    console.log('Is file:', stats.isFile());
    console.log('Is directory:', stats.isDirectory());
    console.log('File size:', stats.size, 'bytes');
    console.log('Created:', stats.birthtime);
    console.log('Modified:', stats.mtime);
});
```

## Environment Variables

### Using process.env
```javascript
// Access environment variables
const PORT = process.env.PORT || 3000;
const DB_HOST = process.env.DB_HOST || 'localhost';
const DB_USER = process.env.DB_USER;
const DB_PASS = process.env.DB_PASS;

console.log('Port:', PORT);
console.log('Database host:', DB_HOST);

// Set environment variables in code
process.env.MY_VARIABLE = 'some value';
console.log('My variable:', process.env.MY_VARIABLE);

// List all environment variables
console.log('All environment variables:');
console.log(process.env);
```

### Using dotenv
```bash
# Install dotenv
npm install dotenv
```

```javascript
// .env file
DB_HOST=localhost
DB_USER=myuser
DB_PASS=mypassword
API_KEY=your-api-key-here
```

```javascript
// Load environment variables from .env file
require('dotenv').config();

const dbHost = process.env.DB_HOST;
const dbUser = process.env.DB_USER;
const dbPass = process.env.DB_PASS;
const apiKey = process.env.API_KEY;

console.log('Database configuration loaded');
```

## Streams

### Reading and Writing Streams
```javascript
const fs = require('fs');

// Read stream
const readStream = fs.createReadStream('large-file.txt');
readStream.on('data', (chunk) => {
    console.log('Received chunk:', chunk.length, 'bytes');
});

readStream.on('end', () => {
    console.log('Finished reading file');
});

readStream.on('error', (err) => {
    console.error('Error reading file:', err);
});

// Write stream
const writeStream = fs.createWriteStream('output.txt');
writeStream.write('Hello, ');
writeStream.write('World!');
writeStream.end();

// Pipe streams (copy file)
const readStream = fs.createReadStream('source.txt');
const writeStream = fs.createWriteStream('destination.txt');

readStream.pipe(writeStream);

readStream.on('end', () => {
    console.log('File copied successfully');
});
```
