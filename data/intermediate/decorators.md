# Python Decorators

## Basic Decorator Concepts

### What is a Decorator?
```python
# A decorator is a function that takes another function as an argument
# adds some functionality, and returns another function

# Basic decorator function
def simple_decorator(func):
    def wrapper():
        print("Something is happening before the function is called.")
        func()
        print("Something is happening after the function is called.")
    return wrapper

@simple_decorator
def say_hello():
    print("Hello!")

# Using the decorator
say_hello()
```

### Decorator with Arguments
```python
def uppercase_decorator(func):
    def wrapper(*args, **kwargs):
        result = func(*args, **kwargs)
        return result.upper()
    return wrapper

@uppercase_decorator
def greet(name):
    return f"Hello, {name}!"

print(greet("Alice"))

def add_numbers(a, b):
    return f"{a} + {b} = {a + b}"

# Apply decorator manually
decorated_add = uppercase_decorator(add_numbers)
print(decorated_add(5, 3))
```

### Decorator with Parameters
```python
def repeat_decorator(times):
    def decorator(func):
        def wrapper(*args, **kwargs):
            result = None
            for _ in range(times):
                result = func(*args, **kwargs)
            return result
        return wrapper
    return decorator

@repeat_decorator(3)
def say_goodbye():
    print("Goodbye!")

say_goodbye()

@repeat_decorator(2)
def calculate_sum(a, b):
    result = a + b
    print(f"Sum: {result}")
    return result

final_result = calculate_sum(5, 3)
print(f"Final result: {final_result}")
```

## Built-in Decorators

### @property Decorator
```python
class Person:
    def __init__(self, name, age):
        self._name = name
        self._age = age
    
    @property
    def name(self):
        return self._name
    
    @property
    def age(self):
        return self._age
    
    @age.setter
    def age(self, value):
        if value < 0:
            raise ValueError("Age cannot be negative")
        self._age = value
    
    @property
    def is_adult(self):
        return self._age >= 18

person = Person("Alice", 25)
print(f"Name: {person.name}")
print(f"Age: {person.age}")
print(f"Is adult: {person.is_adult}")

person.age = 26
print(f"New age: {person.age}")

try:
    person.age = -5
except ValueError as e:
    print(f"Error: {e}")
```

### @staticmethod and @classmethod
```python
class MathOperations:
    PI = 3.14159
    
    @staticmethod
    def add(a, b):
        return a + b
    
    @staticmethod
    def multiply(a, b):
        return a * b
    
    @classmethod
    def circle_area(cls, radius):
        return cls.PI * radius ** 2
    
    @classmethod
    def from_string(cls, description):
        parts = description.split(',')
        return cls(float(parts[0]), float(parts[1]))

# Using static methods
print(f"5 + 3 = {MathOperations.add(5, 3)}")
print(f"5 * 3 = {MathOperations.multiply(5, 3)}")

# Using class method
print(f"Circle area (r=5): {MathOperations.circle_area(5)}")
```

### @dataclass Decorator (Python 3.7+)
```python
from dataclasses import dataclass, field
from datetime import datetime
from typing import List, Optional

@dataclass
class Employee:
    name: str
    age: int
    department: str
    salary: float = 50000.0
    skills: List[str] = field(default_factory=list)
    hire_date: datetime = field(default_factory=datetime.now)
    manager: Optional['Employee'] = None
    
    def give_raise(self, percentage: float):
        self.salary *= (1 + percentage / 100)
    
    def add_skill(self, skill: str):
        self.skills.append(skill)
    
    def years_of_service(self) -> float:
        return (datetime.now() - self.hire_date).days / 365.25

# Creating employees
emp1 = Employee("Alice", 30, "Engineering", 75000.0)
emp1.add_skill("Python")
emp1.add_skill("Data Science")
emp1.give_raise(10)

emp2 = Employee("Bob", 25, "Marketing", 60000.0)
emp2.manager = emp1

print(emp1)
print(f"Emp1 salary after raise: ${emp1.salary:,.2f}")
print(f"Emp1 skills: {emp1.skills}")
print(f"Emp2 manager: {emp2.manager.name}")
```

## Custom Decorators

### Timing Decorator
```python
import time
from functools import wraps

def timing_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        start_time = time.time()
        result = func(*args, **kwargs)
        end_time = time.time()
        print(f"{func.__name__} took {end_time - start_time:.4f} seconds to execute.")
        return result
    return wrapper

@timing_decorator
def slow_function():
    time.sleep(1)
    return "Function completed"

@timing_decorator
def fibonacci(n):
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)

result = slow_function()
print(f"Result: {result}")

fib_result = fibonacci(10)
print(f"Fibonacci(10): {fib_result}")
```

### Logging Decorator
```python
import logging
from functools import wraps

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def log_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        logger.info(f"Calling {func.__name__} with args={args}, kwargs={kwargs}")
        try:
            result = func(*args, **kwargs)
            logger.info(f"{func.__name__} returned: {result}")
            return result
        except Exception as e:
            logger.error(f"{func.__name__} raised: {e}")
            raise
    return wrapper

@log_decorator
def divide(a, b):
    return a / b

@log_decorator
def risky_operation(x):
    if x < 0:
        raise ValueError("Negative values not allowed")
    return x * 2

# Test logging decorator
result = divide(10, 2)
print(f"Division result: {result}")

try:
    risky_operation(-5)
except ValueError as e:
    print(f"Caught error: {e}")
```

### Authentication Decorator
```python
from functools import wraps

class User:
    def __init__(self, username, role):
        self.username = username
        self.role = role
        self.is_authenticated = True

# Simulate current user
current_user = User("alice", "admin")

def require_auth(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        if not current_user.is_authenticated:
            raise PermissionError("User not authenticated")
        return func(*args, **kwargs)
    return wrapper

def require_role(required_role):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            if current_user.role != required_role:
                raise PermissionError(f"Requires {required_role} role")
            return func(*args, **kwargs)
        return wrapper
    return decorator

@require_auth
def view_dashboard():
    return "Dashboard data"

@require_auth
@require_role("admin")
def delete_user(user_id):
    return f"User {user_id} deleted"

@require_role("user")
def view_profile():
    return f"Profile for {current_user.username}"

# Test authentication
print(view_dashboard())
print(view_profile())
print(delete_user(123))
```

### Cache Decorator
```python
from functools import wraps, lru_cache

# Custom cache decorator
def cache_decorator(func):
    cache = {}
    
    @wraps(func)
    def wrapper(*args, **kwargs):
        # Create a cache key from arguments
        key = str(args) + str(sorted(kwargs.items()))
        
        if key in cache:
            print(f"Cache hit for {func.__name__}({key})")
            return cache[key]
        
        print(f"Cache miss for {func.__name__}({key})")
        result = func(*args, **kwargs)
        cache[key] = result
        return result
    
    return wrapper

@cache_decorator
def expensive_operation(x, y):
    print(f"Performing expensive operation with {x}, {y}")
    time.sleep(0.1)  # Simulate expensive computation
    return x ** y

# Using built-in lru_cache
@lru_cache(maxsize=128)
def fibonacci_cached(n):
    if n <= 1:
        return n
    return fibonacci_cached(n-1) + fibonacci_cached(n-2)

# Test cache decorator
result1 = expensive_operation(2, 8)
result2 = expensive_operation(2, 8)  # Should hit cache
result3 = expensive_operation(3, 4)  # Should miss cache

# Test lru_cache
print(f"Fibonacci(10): {fibonacci_cached(10)}")
print(f"Fibonacci(15): {fibonacci_cached(15)}")
print(f"Cache info: {fibonacci_cached.cache_info()}")
```

## Advanced Decorator Patterns

### Decorator Class
```python
class CountCalls:
    def __init__(self, func):
        self.func = func
        self.count = 0
    
    def __call__(self, *args, **kwargs):
        self.count += 1
        print(f"Call {self.count} of {self.func.__name__}")
        return self.func(*args, **kwargs)

@CountCalls
def greet(name):
    return f"Hello, {name}!"

greet("Alice")
greet("Bob")
greet("Charlie")
```

### Decorator with State
```python
class Timer:
    def __init__(self, func):
        self.func = func
        self.total_time = 0
        self.call_count = 0
    
    def __call__(self, *args, **kwargs):
        import time
        start_time = time.time()
        result = self.func(*args, **kwargs)
        end_time = time.time()
        
        execution_time = end_time - start_time
        self.total_time += execution_time
        self.call_count += 1
        
        print(f"{self.func.__name__} executed in {execution_time:.4f}s")
        return result
    
    def average_time(self):
        if self.call_count == 0:
            return 0
        return self.total_time / self.call_count

@Timer
def process_data(data):
    import random
    time.sleep(random.uniform(0.1, 0.5))
    return len(data)

# Test timer decorator
data = list(range(1000))
for i in range(3):
    result = process_data(data)

timer_decorator = process_data.__self__  # Get the decorator instance
print(f"Average execution time: {timer_decorator.average_time():.4f}s")
print(f"Total calls: {timer_decorator.call_count}")
```

### Decorator Factory
```python
def validate_types(**type_hints):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            # Get function signature
            from inspect import signature
            sig = signature(func)
            bound_args = sig.bind(*args, **kwargs)
            bound_args.apply_defaults()
            
            # Validate types
            for name, value in bound_args.arguments.items():
                if name in type_hints:
                    expected_type = type_hints[name]
                    if not isinstance(value, expected_type):
                        raise TypeError(
                            f"Argument {name} must be {expected_type.__name__}, "
                            f"got {type(value).__name__}"
                        )
            
            return func(*args, **kwargs)
        return wrapper
    return decorator

@validate_types(name=str, age=int, email=str)
def create_user(name, age, email):
    return f"User created: {name}, {age}, {email}"

# Test type validation
try:
    user = create_user("Alice", 25, "alice@example.com")
    print(user)
except TypeError as e:
    print(f"Type error: {e}")

try:
    user = create_user("Bob", "twenty-five", "bob@example.com")
except TypeError as e:
    print(f"Type error: {e}")
```

## Decorator Composition

### Multiple Decorators
```python
def bold_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        return f"<b>{func(*args, **kwargs)}</b>"
    return wrapper

def italic_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        return f"<i>{func(*args, **kwargs)}</i>"
    return wrapper

def underline_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        return f"<u>{func(*args, **kwargs)}</u>"
    return wrapper

# Apply multiple decorators
@bold_decorator
@italic_decorator
@underline_decorator
def greet(name):
    return f"Hello, {name}!"

print(greet("Alice"))

# Order matters - decorators are applied from bottom to top
@underline_decorator
@italic_decorator
@bold_decorator
def farewell(name):
    return f"Goodbye, {name}!"

print(farewell("Bob"))
```

### Conditional Decorators
```python
def conditional_decorator(condition):
    def decorator(func):
        if condition:
            return timing_decorator(func)
        else:
            return func
    return decorator

# Apply decorator conditionally
@conditional_decorator(True)
def fast_function():
    return "This will be timed"

@conditional_decorator(False)
def slow_function():
    return "This won't be timed"

fast_function()
slow_function()
```

## Practical Examples

### Retry Decorator
```python
import random
from functools import wraps

def retry(max_attempts=3, delay=1):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            for attempt in range(max_attempts):
                try:
                    return func(*args, **kwargs)
                except Exception as e:
                    if attempt == max_attempts - 1:
                        raise
                    print(f"Attempt {attempt + 1} failed: {e}. Retrying in {delay}s...")
                    time.sleep(delay)
            return None
        return wrapper
    return decorator

@retry(max_attempts=3, delay=0.5)
def unreliable_function():
    if random.random() < 0.7:  # 70% chance of failure
        raise ConnectionError("Connection failed")
    return "Success!"

try:
    result = unreliable_function()
    print(f"Result: {result}")
except Exception as e:
    print(f"Final error: {e}")
```

### Rate Limiting Decorator
```python
import time
from collections import defaultdict

class RateLimiter:
    def __init__(self, max_calls, time_window):
        self.max_calls = max_calls
        self.time_window = time_window
        self.calls = defaultdict(list)
    
    def is_allowed(self, identifier):
        now = time.time()
        call_times = self.calls[identifier]
        
        # Remove old calls outside the time window
        self.calls[identifier] = [call_time for call_time in call_times 
                                 if now - call_time < self.time_window]
        
        # Check if under the limit
        if len(self.calls[identifier]) < self.max_calls:
            self.calls[identifier].append(now)
            return True
        
        return False

def rate_limit(max_calls=5, time_window=60):
    limiter = RateLimiter(max_calls, time_window)
    
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            # Use first argument as identifier (e.g., user_id)
            identifier = args[0] if args else "default"
            
            if not limiter.is_allowed(identifier):
                raise Exception(f"Rate limit exceeded for {identifier}")
            
            return func(*args, **kwargs)
        return wrapper
    return decorator

@rate_limit(max_calls=3, time_window=5)
def api_call(user_id, data):
    return f"Processing data for {user_id}: {data}"

# Test rate limiting
for i in range(5):
    try:
        result = api_call("user123", f"request_{i}")
        print(f"Request {i}: {result}")
    except Exception as e:
        print(f"Request {i}: {e}")
    time.sleep(1)
```

## Best Practices

### Decorator Best Practices
```python
from functools import wraps
import inspect

# 1. Always use @wraps to preserve metadata
def proper_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        """Wrapper function documentation."""
        return func(*args, **kwargs)
    return wrapper

# 2. Handle both positional and keyword arguments
def flexible_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        print(f"Args: {args}")
        print(f"Kwargs: {kwargs}")
        return func(*args, **kwargs)
    return wrapper

# 3. Preserve function signature
def signature_preserving_decorator(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        return func(*args, **kwargs)
    # Copy signature
    wrapper.__signature__ = inspect.signature(func)
    return wrapper

# 4. Use class-based decorators for state management
class StatefulDecorator:
    def __init__(self, func):
        self.func = func
        self.state = {}
        wraps(func)(self)  # Preserve metadata
    
    def __call__(self, *args, **kwargs):
        # Access self.state here
        return self.func(*args, **kwargs)

# 5. Chain decorators properly
def decorator1(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        print("Decorator 1 before")
        result = func(*args, **kwargs)
        print("Decorator 1 after")
        return result
    return wrapper

def decorator2(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        print("Decorator 2 before")
        result = func(*args, **kwargs)
        print("Decorator 2 after")
        return result
    return wrapper

# Order matters: decorator2 is applied first, then decorator1
@decorator1
@decorator2
def example_function():
    print("Function executing")

example_function()
```

## Summary

Python decorators are powerful tools for:

**Core Concepts:**
- Functions that wrap other functions
- Add functionality without modifying original code
- Use @ syntax for clean application
- Can accept parameters and maintain state

**Built-in Decorators:**
- `@property` for managed attributes
- `@staticmethod` and `@classmethod` for class methods
- `@dataclass` for simple data structures
- `@abstractmethod` for abstract methods

**Common Patterns:**
- Timing and performance monitoring
- Logging and debugging
- Authentication and authorization
- Caching and memoization
- Error handling and retry logic
- Rate limiting and validation

**Best Practices:**
- Use `@wraps` to preserve metadata
- Handle flexible arguments with `*args, **kwargs`
- Consider class-based decorators for state
- Apply decorators in correct order
- Document decorator behavior clearly

Decorators enable clean, reusable, and maintainable code by separating cross-cutting concerns from business logic.
