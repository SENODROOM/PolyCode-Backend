# Python Functions

## Basic Function Definition

### Simple Function
```python
# Basic function definition
def greet():
    print("Hello, World!")

# Calling the function
greet()

# Function with return value
def get_message():
    return "Hello from function"

message = get_message()
print(message)
```

### Function with Parameters
```python
# Function with parameters
def greet_person(name):
    print(f"Hello, {name}!")

greet_person("Alice")

# Function with default parameters
def greet_with_title(name, title="Mr./Ms."):
    print(f"Hello, {title} {name}!")

greet_with_title("Bob")
greet_with_title("Alice", "Dr.")

# Function with multiple parameters
def add_numbers(a, b, c=0):
    return a + b + c

result = add_numbers(10, 20)
print(f"10 + 20 = {result}")

result = add_numbers(10, 20, 30)
print(f"10 + 20 + 30 = {result}")
```

### Function with Return Types
```python
# Function returning different types
def calculate(operation, a, b):
    if operation == "add":
        return a + b
    elif operation == "subtract":
        return a - b
    elif operation == "multiply":
        return a * b
    elif operation == "divide":
        return a / b if b != 0 else "Cannot divide by zero"
    else:
        return "Unknown operation"

result = calculate("add", 10, 5)
print(f"Addition result: {result}")

result = calculate("divide", 10, 0)
print(f"Division result: {result}")
```

## Function Parameters

### Positional and Keyword Arguments
```python
def describe_person(name, age, city):
    return f"{name} is {age} years old and lives in {city}"

# Positional arguments
info = describe_person("Alice", 25, "New York")
print(info)

# Keyword arguments
info = describe_person(age=30, name="Bob", city="Los Angeles")
print(info)

# Mixed arguments
info = describe_person("Charlie", city="Chicago", age=35)
print(info)
```

### Variable Number of Arguments
```python
# *args for variable positional arguments
def sum_all(*numbers):
    return sum(numbers)

total = sum_all(1, 2, 3, 4, 5)
print(f"Sum: {total}")

total = sum_all(10, 20, 30)
print(f"Sum: {total}")

# **kwargs for variable keyword arguments
def print_person_info(**kwargs):
    for key, value in kwargs.items():
        print(f"{key}: {value}")

print_person_info(name="Alice", age=25, city="New York", email="alice@example.com")

# Combining *args and **kwargs
def flexible_function(*args, **kwargs):
    print(f"Positional args: {args}")
    print(f"Keyword args: {kwargs}")

flexible_function(1, 2, 3, name="Alice", age=25)
```

### Argument Unpacking
```python
# Unpacking lists/tuples
def process_three_values(a, b, c):
    return a + b + c

values = [1, 2, 3]
result = process_three_values(*values)
print(f"Result from list unpacking: {result}")

values_tuple = (10, 20, 30)
result = process_three_values(*values_tuple)
print(f"Result from tuple unpacking: {result}")

# Unpacking dictionaries
def create_person(name, age, city):
    return f"{name}, {age}, {city}"

person_info = {"name": "Alice", "age": 25, "city": "New York}
result = create_person(**person_info)
print(f"Result from dict unpacking: {result}")
```

## Function Scope and Lifetime

### Local and Global Variables
```python
global_var = "I am global"

def demonstrate_scope():
    local_var = "I am local"
    print(f"Inside function - global: {global_var}")
    print(f"Inside function - local: {local_var}")

demonstrate_scope()
# print(local_var)  # This would cause NameError

# Modifying global variables
counter = 0

def increment_counter():
    global counter
    counter += 1
    print(f"Counter inside function: {counter}")

increment_counter()
print(f"Counter outside function: {counter}")
```

### Nonlocal Variables
```python
def outer_function():
    outer_var = "I am outer"
    
    def inner_function():
        nonlocal outer_var
        outer_var = "Modified by inner"
        print(f"Inner function - outer_var: {outer_var}")
    
    print(f"Before inner call - outer_var: {outer_var}")
    inner_function()
    print(f"After inner call - outer_var: {outer_var}")

outer_function()
```

## Advanced Function Concepts

### Nested Functions
```python
def outer_function(x):
    def inner_function(y):
        return x + y
    
    result = inner_function(10)
    return result

output = outer_function(5)
print(f"Nested function result: {output}")

# Functions as return values
def get_multiplier(factor):
    def multiplier(number):
        return number * factor
    
    return multiplier

times_two = get_multiplier(2)
times_three = get_multiplier(3)

print(f"5 * 2 = {times_two(5)}")
print(f"5 * 3 = {times_three(5)}")
```

### Closures
```python
def make_counter():
    count = 0
    
    def increment():
        nonlocal count
        count += 1
        return count
    
    return increment

counter1 = make_counter()
counter2 = make_counter()

print(f"Counter 1: {counter1()}")  # 1
print(f"Counter 1: {counter1()}")  # 2
print(f"Counter 2: {counter2()}")  # 1
print(f"Counter 1: {counter1()}")  # 3
print(f"Counter 2: {counter2()}")  # 2
```

### Decorators
```python
# Simple decorator
def uppercase_decorator(func):
    def wrapper():
        result = func()
        return result.upper()
    return wrapper

@uppercase_decorator
def greet():
    return "hello, world!"

print(greet())

# Decorator with parameters
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
def say_hello():
    print("Hello!")

say_hello()

# Decorator with return value preservation
def log_decorator(func):
    def wrapper(*args, **kwargs):
        print(f"Calling {func.__name__} with args: {args}, kwargs: {kwargs}")
        result = func(*args, **kwargs)
        print(f"{func.__name__} returned: {result}")
        return result
    return wrapper

@log_decorator
def add(a, b):
    return a + b

result = add(5, 3)
print(f"Final result: {result}")
```

## Lambda Functions

### Basic Lambda Functions
```python
# Simple lambda
square = lambda x: x ** 2
print(f"Square of 5: {square(5)}")

# Lambda with multiple arguments
add = lambda x, y: x + y
print(f"5 + 3 = {add(5, 3)}")

# Lambda with conditional
max_of_two = lambda x, y: x if x > y else y
print(f"Max of 10 and 20: {max_of_two(10, 20)}")
```

### Lambda in Higher-Order Functions
```python
# Using lambda with map
numbers = [1, 2, 3, 4, 5]
squared = list(map(lambda x: x ** 2, numbers))
print(f"Squared: {squared}")

# Using lambda with filter
even_numbers = list(filter(lambda x: x % 2 == 0, numbers))
print(f"Even numbers: {even_numbers}")

# Using lambda with sorted
people = [
    {"name": "Alice", "age": 25},
    {"name": "Bob", "age": 30},
    {"name": "Charlie", "age": 20}
]

sorted_people = sorted(people, key=lambda person: person["age"])
print(f"Sorted by age: {[p['name'] for p in sorted_people]}")
```

## Function Annotations and Type Hints

### Basic Type Hints
```python
from typing import List, Dict, Optional, Union

# Function with type hints
def add_numbers(a: int, b: int) -> int:
    return a + b

result = add_numbers(5, 3)
print(f"5 + 3 = {result}")

# Complex type hints
def process_data(
    data: List[int], 
    operation: str, 
    threshold: Optional[int] = None
) -> Union[int, List[int]]:
    if operation == "sum":
        return sum(data)
    elif operation == "filter" and threshold:
        return [x for x in data if x > threshold]
    else:
        return data

numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
result = process_data(numbers, "filter", 5)
print(f"Filtered result: {result}")
```

### Advanced Type Hints
```python
from typing import Callable, TypeVar, Generic

# Generic type variable
T = TypeVar('T')

def generic_function(x: T, y: T) -> T:
    return x + y

# This works for strings
string_result = generic_function("Hello, ", "World!")
print(f"String result: {string_result}")

# This works for numbers (if they support +)
number_result = generic_function(5, 3)
print(f"Number result: {number_result}")

# Callable type hint
def apply_operation(x: int, operation: Callable[[int], int]) -> int:
    return operation(x)

def square(n: int) -> int:
    return n ** 2

result = apply_operation(5, square)
print(f"Square of 5: {result}")
```

## Recursion

### Recursive Functions
```python
# Factorial using recursion
def factorial(n: int) -> int:
    if n <= 1:
        return 1
    return n * factorial(n - 1)

print(f"Factorial of 5: {factorial(5)}")
print(f"Factorial of 0: {factorial(0)}")

# Fibonacci sequence
def fibonacci(n: int) -> int:
    if n <= 1:
        return n
    return fibonacci(n - 1) + fibonacci(n - 2)

print(f"Fibonacci of 10: {fibonacci(10)}")

# Directory traversal (recursive)
import os

def list_files(directory: str) -> List[str]:
    files = []
    for item in os.listdir(directory):
        full_path = os.path.join(directory, item)
        if os.path.isdir(full_path):
            files.extend(list_files(full_path))
        else:
            files.append(full_path)
    return files

# Uncomment to test (requires actual directory)
# files = list_files(".")
# print(f"Files: {files[:5]}...")  # Show first 5 files
```

## Generators

### Generator Functions
```python
# Simple generator
def count_up_to(n: int):
    count = 1
    while count <= n:
        yield count
        count += 1

# Using generator
counter = count_up_to(5)
for number in counter:
    print(f"Count: {number}")

# Generator expression
squares_gen = (x ** 2 for x in range(10))
print(f"Squares from generator: {list(squares_gen)}")

# Infinite generator
def infinite_counter():
    count = 0
    while True:
        yield count
        count += 1

# Using infinite generator
counter = infinite_counter()
for i in range(5):
    print(f"Infinite count: {next(counter)}")
```

### Generator with Send and Close
```python
def echo_generator():
    while True:
        received = yield
        if received is None:
            break
        print(f"Echo: {received}")

echo = echo_generator()
next(echo)  # Start generator
echo.send("Hello")
echo.send("World")
echo.close()  # Close generator
```

## Async Functions

### Basic Async Functions
```python
import asyncio

async def say_after(delay: int, message: str):
    await asyncio.sleep(delay)
    print(message)

async def main():
    print("Started")
    await say_after(1, "Hello")
    await say_after(2, "World")
    print("Finished")

# Run async function
# asyncio.run(main())  # Uncomment to run
```

### Async Generators
```python
async def async_counter():
    count = 0
    while count < 5:
        yield count
        count += 1
        await asyncio.sleep(0.1)

async def main():
    async for count in async_counter():
        print(f"Async count: {count}")

# Run async generator
# asyncio.run(main())  # Uncomment to run
```

## Function Best Practices

### Documentation Strings
```python
def calculate_area(length: float, width: float) -> float:
    """
    Calculate the area of a rectangle.
    
    Args:
        length (float): The length of the rectangle
        width (float): The width of the rectangle
    
    Returns:
        float: The area of the rectangle
    
    Raises:
        ValueError: If length or width is negative
    
    Example:
        >>> calculate_area(5.0, 3.0)
        15.0
    """
    if length < 0 or width < 0:
        raise ValueError("Length and width must be positive")
    return length * width

area = calculate_area(5.0, 3.0)
print(f"Area: {area}")
```

### Error Handling
```python
def safe_divide(a: float, b: float) -> Optional[float]:
    """
    Safely divide two numbers.
    
    Returns None if division by zero is attempted.
    """
    try:
        return a / b
    except ZeroDivisionError:
        return None
    except TypeError:
        return None

result = safe_divide(10, 2)
print(f"10 / 2 = {result}")

result = safe_divide(10, 0)
print(f"10 / 0 = {result}")
```

### Function Composition
```python
from typing import Callable

def compose(f: Callable, g: Callable) -> Callable:
    """Compose two functions f(g(x))"""
    return lambda x: f(g(x))

def add_five(x: int) -> int:
    return x + 5

def multiply_by_two(x: int) -> int:
    return x * 2

# Compose functions
composed = compose(add_five, multiply_by_two)
result = composed(10)  # (10 * 2) + 5 = 25
print(f"Composed result: {result}")
```

## Performance Considerations

### Function Call Overhead
```python
import timeit

# Function call overhead
def add(a, b):
    return a + b

# Direct addition
def direct_add(a, b):
    return a + b

# Compare performance
time_func = timeit.timeit(lambda: add(1, 2), number=1000000)
time_direct = timeit.timeit(lambda: 1 + 2, number=1000000)

print(f"Function call time: {time_func:.6f} seconds")
print(f"Direct operation time: {time_direct:.6f} seconds")
```

### Memoization
```python
from functools import lru_cache

# Expensive function
def fibonacci_slow(n: int) -> int:
    if n <= 1:
        return n
    return fibonacci_slow(n - 1) + fibonacci_slow(n - 2)

# Memoized version
@lru_cache(maxsize=None)
def fibonacci_fast(n: int) -> int:
    if n <= 1:
        return n
    return fibonacci_fast(n - 1) + fibonacci_fast(n - 2)

# Compare performance
import time
start = time.time()
result = fibonacci_slow(35)
slow_time = time.time() - start

start = time.time()
result = fibonacci_fast(35)
fast_time = time.time() - start

print(f"Slow fibonacci time: {slow_time:.6f} seconds")
print(f"Fast fibonacci time: {fast_time:.6f} seconds")
```

## Summary

Python functions are powerful and flexible:

**Basic Features:**
- Defining functions with `def`
- Parameters with defaults and variable arguments
- Return values and multiple returns
- Local, global, and nonlocal scope

**Advanced Features:**
- Nested functions and closures
- Decorators for function modification
- Lambda functions for anonymous operations
- Generators for memory-efficient iteration
- Async functions for concurrent programming

**Type Safety:**
- Type hints for better documentation
- Generic functions with TypeVar
- Callable types for function parameters

**Best Practices:**
- Use descriptive names and docstrings
- Handle errors appropriately
- Consider performance implications
- Use generators for large sequences
- Apply decorators for cross-cutting concerns

Functions are fundamental building blocks in Python, enabling code reuse, organization, and abstraction.
