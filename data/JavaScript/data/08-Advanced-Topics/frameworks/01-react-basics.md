# React Basics

React is a JavaScript library for building user interfaces, particularly web applications with dynamic, interactive components.

## What is React?

React is a declarative, efficient, and flexible JavaScript library for building user interfaces. It lets you compose complex UIs from small and isolated pieces of code called components.

### Key Concepts
- **Components**: Reusable UI building blocks
- **JSX**: JavaScript XML syntax extension
- **Virtual DOM**: Efficient DOM updates
- **Unidirectional Data Flow**: Data flows from parent to child
- **State Management**: Component state and props

## Setting Up React

### Create React App
```bash
npx create-react-app my-app
cd my-app
npm start
```

### Basic Project Structure
```
my-app/
├── public/
│   ├── index.html
│   └── favicon.ico
├── src/
│   ├── App.js
│   ├── index.js
│   └── components/
├── package.json
└── README.md
```

## Components

### Functional Components
```javascript
import React from 'react';

function Welcome(props) {
    return <h1>Hello, {props.name}!</h1>;
}

// Arrow function component
const Welcome = (props) => {
    return <h1>Hello, {props.name}!</h1>;
};

// Short form (implicit return)
const Welcome = (props) => <h1>Hello, {props.name}!</h1>;

export default Welcome;
```

### Class Components
```javascript
import React, { Component } from 'react';

class Welcome extends Component {
    render() {
        return <h1>Hello, {this.props.name}!</h1>;
    }
}

export default Welcome;
```

## JSX

### What is JSX?
JSX is a syntax extension for JavaScript that looks similar to HTML. It allows you to write HTML-like code in your JavaScript files.

### Basic JSX
```javascript
const element = <h1>Hello, World!</h1>;

const element = (
    <div>
        <h1>Welcome to React</h1>
        <p>This is a paragraph.</p>
    </div>
);
```

### JSX Expressions
```javascript
function formatName(user) {
    return user.firstName + ' ' + user.lastName;
}

const user = {
    firstName: 'John',
    lastName: 'Doe'
};

const element = (
    <h1>
        Hello, {formatName(user)}!
    </h1>
);
```

### JSX Attributes
```javascript
const element = <div className="container" id="main">Content</div>;

// Dynamic attributes
const element = <img src={user.avatarUrl} alt={user.name} />;

// Boolean attributes
const element = <button disabled={isDisabled}>Click me</button>;
```

## Props

### Passing Props
```javascript
// Parent component
function App() {
    return (
        <div>
            <Welcome name="John" age={30} />
            <Welcome name="Jane" age={25} />
        </div>
    );
}

// Child component
function Welcome(props) {
    return (
        <div>
            <h1>Hello, {props.name}!</h1>
            <p>Age: {props.age}</p>
        </div>
    );
}
```

### Props Destructuring
```javascript
function Welcome({ name, age }) {
    return (
        <div>
            <h1>Hello, {name}!</h1>
            <p>Age: {age}</p>
        </div>
    );
}

// With default values
function Welcome({ name = "Guest", age = 0 }) {
    return (
        <div>
            <h1>Hello, {name}!</h1>
            <p>Age: {age}</p>
        </div>
    );
}
```

### Props Validation (PropTypes)
```javascript
import PropTypes from 'prop-types';

function Welcome({ name, age }) {
    return <h1>Hello, {name}! Age: {age}</h1>;
}

Welcome.propTypes = {
    name: PropTypes.string.isRequired,
    age: PropTypes.number
};

Welcome.defaultProps = {
    name: 'Guest',
    age: 0
};
```

## State

### useState Hook
```javascript
import React, { useState } from 'react';

function Counter() {
    const [count, setCount] = useState(0);

    return (
        <div>
            <p>You clicked {count} times</p>
            <button onClick={() => setCount(count + 1)}>
                Click me
            </button>
        </div>
    );
}
```

### Multiple State Variables
```javascript
function UserProfile() {
    const [name, setName] = useState('');
    const [age, setAge] = useState(0);
    const [isLoggedIn, setIsLoggedIn] = useState(false);

    return (
        <div>
            <input 
                value={name} 
                onChange={(e) => setName(e.target.value)} 
                placeholder="Name"
            />
            <input 
                type="number" 
                value={age} 
                onChange={(e) => setAge(parseInt(e.target.value))} 
                placeholder="Age"
            />
            <button onClick={() => setIsLoggedIn(!isLoggedIn)}>
                {isLoggedIn ? 'Logout' : 'Login'}
            </button>
        </div>
    );
}
```

### State with Objects
```javascript
function UserForm() {
    const [user, setUser] = useState({
        name: '',
        email: '',
        age: 0
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setUser(prevUser => ({
            ...prevUser,
            [name]: value
        }));
    };

    return (
        <form>
            <input
                name="name"
                value={user.name}
                onChange={handleChange}
                placeholder="Name"
            />
            <input
                name="email"
                value={user.email}
                onChange={handleChange}
                placeholder="Email"
            />
            <input
                name="age"
                type="number"
                value={user.age}
                onChange={handleChange}
                placeholder="Age"
            />
        </form>
    );
}
```

## Event Handling

### Basic Event Handlers
```javascript
function Button() {
    const handleClick = () => {
        console.log('Button clicked!');
    };

    return <button onClick={handleClick}>Click me</button>;
}
```

### Event with Parameters
```javascript
function ButtonList() {
    const buttons = ['Button 1', 'Button 2', 'Button 3'];

    const handleClick = (buttonName) => {
        console.log(`${buttonName} was clicked`);
    };

    return (
        <div>
            {buttons.map((button, index) => (
                <button 
                    key={index}
                    onClick={() => handleClick(button)}
                >
                    {button}
                </button>
            ))}
        </div>
    );
}
```

### Form Events
```javascript
function SearchForm() {
    const [searchTerm, setSearchTerm] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log('Searching for:', searchTerm);
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder="Search..."
            />
            <button type="submit">Search</button>
        </form>
    );
}
```

## Conditional Rendering

### If-Else Rendering
```javascript
function Greeting({ isLoggedIn }) {
    if (isLoggedIn) {
        return <h1>Welcome back!</h1>;
    } else {
        return <h1>Please log in</h1>;
    }
}
```

### Ternary Operator
```javascript
function Greeting({ isLoggedIn }) {
    return (
        <div>
            {isLoggedIn ? <h1>Welcome back!</h1> : <h1>Please log in</h1>}
        </div>
    );
}
```

### Logical AND Operator
```javascript
function Notification({ message }) {
    return (
        <div>
            {message && <div className="notification">{message}</div>}
        </div>
    );
}
```

## Lists and Keys

### Rendering Lists
```javascript
function NumberList({ numbers }) {
    const listItems = numbers.map((number) =>
        <li key={number.toString()}>
            {number}
        </li>
    );

    return <ul>{listItems}</ul>;
}

// Usage
<NumberList numbers={[1, 2, 3, 4, 5]} />
```

### List with Components
```javascript
function ListItem({ item }) {
    return <li>{item.name}: ${item.price}</li>;
}

function ShoppingList({ items }) {
    return (
        <ul>
            {items.map((item) => (
                <ListItem 
                    key={item.id} 
                    item={item} 
                />
            ))}
        </ul>
    );
}
```

## Lifecycle Effects

### useEffect Hook
```javascript
import React, { useState, useEffect } from 'react';

function Timer() {
    const [count, setCount] = useState(0);

    // Similar to componentDidMount and componentDidUpdate
    useEffect(() => {
        document.title = `You clicked ${count} times`;
    });

    // Similar to componentWillUnmount
    useEffect(() => {
        const timer = setInterval(() => {
            setCount(count + 1);
        }, 1000);

        return () => {
            clearInterval(timer);
        };
    }, []); // Empty dependency array means run once

    return <div>Count: {count}</div>;
}
```

### Fetching Data
```javascript
function UserProfile({ userId }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchUser(userId)
            .then(data => {
                setUser(data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching user:', error);
                setLoading(false);
            });
    }, [userId]); // Re-run when userId changes

    if (loading) return <div>Loading...</div>;
    if (!user) return <div>User not found</div>;

    return <div>{user.name}</div>;
}
```
