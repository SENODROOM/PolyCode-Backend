# Python Control Flow

## Conditional Statements

### if Statement
```python
age = 25

if age >= 18:
    print("You are an adult")
    print("You can vote")
```

### if-else Statement
```python
score = 85

if score >= 60:
    print("You passed the exam")
else:
    print("You failed the exam")
```

### if-elif-else Statement
```python
grade = 85

if grade >= 90:
    print("Grade: A")
elif grade >= 80:
    print("Grade: B")
elif grade >= 70:
    print("Grade: C")
elif grade >= 60:
    print("Grade: D")
else:
    print("Grade: F")
```

### Nested if Statements
```python
age = 25
has_license = True

if age >= 18:
    if has_license:
        print("You can drive")
    else:
        print("You need a license")
else:
    print("You are too young to drive")
```

### Ternary Operator
```python
# Conditional expression
age = 20
status = "adult" if age >= 18 else "minor"
print(f"Status: {status}")

# Nested ternary
score = 75
grade = "A" if score >= 90 else "B" if score >= 80 else "C"
print(f"Grade: {grade}")
```

## Loops

### for Loop
```python
# Loop over a range
for i in range(5):
    print(f"Count: {i}")

# Loop over a list
fruits = ["apple", "banana", "orange"]
for fruit in fruits:
    print(f"Fruit: {fruit}")

# Loop over a string
for char in "Hello":
    print(f"Character: {char}")

# Loop with index
for index, fruit in enumerate(fruits):
    print(f"{index}: {fruit}")

# Loop over dictionary
person = {"name": "Alice", "age": 25, "city": "New York"}
for key, value in person.items():
    print(f"{key}: {value}")
```

### while Loop
```python
# Basic while loop
count = 1
while count <= 5:
    print(f"Count: {count}")
    count += 1

# While loop with condition
number = 0
while number < 10:
    if number == 5:
        break
    print(f"Number: {number}")
    number += 1

# Infinite loop with break
import random
while True:
    num = random.randint(1, 10)
    print(f"Random number: {num}")
    if num == 7:
        print("Got 7, breaking loop")
        break
```

### do-while Equivalent
```python
# Python doesn't have do-while, but we can simulate it
while True:
    user_input = input("Enter 'quit' to exit: ")
    if user_input.lower() == 'quit':
        break
    print(f"You entered: {user_input}")
```

## Loop Control Statements

### break Statement
```python
# Break from loop
for i in range(10):
    if i == 5:
        break
    print(f"Number: {i}")

print("Loop ended")

# Break from nested loops
for i in range(3):
    for j in range(3):
        if i == 1 and j == 1:
            print("Breaking from nested loops")
            break
    print(f"i: {i}")
```

### continue Statement
```python
# Skip even numbers
for i in range(10):
    if i % 2 == 0:
        continue
    print(f"Odd number: {i}")

# Skip specific values
words = ["hello", "world", "skip", "python", "programming"]
for word in words:
    if word == "skip":
        continue
    print(f"Word: {word}")
```

### pass Statement
```python
# Pass does nothing (placeholder)
for i in range(5):
    if i == 3:
        pass  # Do nothing for i == 3
    print(f"Number: {i}")

# Empty function with pass
def empty_function():
    pass

# Empty class with pass
class EmptyClass:
    pass
```

## Advanced Loop Patterns

### List Comprehensions
```python
# Basic list comprehension
squares = [x**2 for x in range(10)]
print(f"Squares: {squares}")

# List comprehension with condition
even_numbers = [x for x in range(20) if x % 2 == 0]
print(f"Even numbers: {even_numbers}")

# List comprehension with transformation
words = ["hello", "world", "python"]
uppercase = [word.upper() for word in words]
print(f"Uppercase: {uppercase}")

# Nested list comprehension
matrix = [[i*j for j in range(3)] for i in range(3)]
print(f"Matrix: {matrix}")
```

### Dictionary Comprehensions
```python
# Basic dictionary comprehension
squares_dict = {x: x**2 for x in range(5)}
print(f"Squares dict: {squares_dict}")

# Dictionary comprehension with condition
even_squares = {x: x**2 for x in range(10) if x % 2 == 0}
print(f"Even squares: {squares_dict}")

# Transform dictionary
person = {"name": "Alice", "age": 25, "city": "New York"}
upper_person = {k.upper(): v for k, v in person.items()}
print(f"Upper case keys: {upper_person}")
```

### Set Comprehensions
```python
# Basic set comprehension
unique_squares = {x**2 for x in range(10)}
print(f"Unique squares: {unique_squares}")

# Set comprehension with condition
even_numbers = {x for x in range(20) if x % 2 == 0}
print(f"Even numbers set: {even_numbers}")
```

### Generator Expressions
```python
# Generator expression (memory efficient)
squares_gen = (x**2 for x in range(10))
print(f"Squares generator: {list(squares_gen)}")

# Generator with condition
even_gen = (x for x in range(20) if x % 2 == 0)
print(f"Even generator: {list(even_gen)}")

# Generator for large sequences
def fibonacci_generator(n):
    a, b = 0, 1
    for _ in range(n):
        yield a
        a, b = b, a + b

fib_gen = fibonacci_generator(10)
print(f"Fibonacci: {list(fib_gen)}")
```

## Exception Handling in Control Flow

### try-except Blocks
```python
# Basic exception handling
try:
    result = 10 / 0
except ZeroDivisionError:
    print("Cannot divide by zero")

# Multiple exceptions
try:
    num = int("hello")
except ValueError:
    print("Invalid number format")
except TypeError:
    print("Type error")

# Exception with else and finally
try:
    file = open("nonexistent.txt", "r")
except FileNotFoundError:
    print("File not found")
else:
    print("File opened successfully")
    file.close()
finally:
    print("Cleanup code (always executed)")
```

### Custom Exceptions
```python
class CustomError(Exception):
    def __init__(self, message):
        self.message = message
        super().__init__(self.message)

def risky_operation():
    raise CustomError("Something went wrong")

try:
    risky_operation()
except CustomError as e:
    print(f"Custom error: {e.message}")
```

## Functional Programming Constructs

### map() Function
```python
# Using map with built-in functions
numbers = [1, 2, 3, 4, 5]
squared = list(map(lambda x: x**2, numbers))
print(f"Squared: {squared}")

# Using map with regular functions
def square(x):
    return x**2

squared_func = list(map(square, numbers))
print(f"Squared with function: {squared_func}")

# Multiple sequences
numbers1 = [1, 2, 3]
numbers2 = [4, 5, 6]
summed = list(map(lambda x, y: x + y, numbers1, numbers2))
print(f"Summed: {summed}")
```

### filter() Function
```python
# Using filter
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
even = list(filter(lambda x: x % 2 == 0, numbers))
print(f"Even numbers: {even}")

# Using filter with function
def is_prime(n):
    if n < 2:
        return False
    for i in range(2, int(n**0.5) + 1):
        if n % i == 0:
            return False
    return True

primes = list(filter(is_prime, range(20)))
print(f"Primes: {primes}")
```

### reduce() Function
```python
from functools import reduce

# Using reduce
numbers = [1, 2, 3, 4, 5]
sum_all = reduce(lambda x, y: x + y, numbers)
print(f"Sum: {sum_all}")

product = reduce(lambda x, y: x * y, numbers)
print(f"Product: {product}")

# Reduce with initial value
sum_with_initial = reduce(lambda x, y: x + y, numbers, 10)
print(f"Sum with initial: {sum_with_initial}")
```

## Advanced Control Flow

### Context Managers
```python
# Using with statement
with open("example.txt", "w") as file:
    file.write("Hello, World!")
# File automatically closed

# Custom context manager
from contextlib import contextmanager

@contextmanager
def timer():
    import time
    start = time.time()
    yield
    end = time.time()
    print(f"Operation took {end - start:.4f} seconds")

with timer():
    result = sum(range(1000000))
    print(f"Sum result: {result}")
```

### Generators and Yield
```python
# Generator function
def count_up_to(n):
    count = 1
    while count <= n:
        yield count
        count += 1

# Using generator
counter = count_up_to(5)
for number in counter:
    print(f"Count: {number}")

# Generator with send()
def echo_generator():
    while True:
        received = yield
        print(f"Echo: {received}")

echo = echo_generator()
next(echo)  # Start generator
echo.send("Hello")
echo.send("World")
```

### Coroutines (Python 3.5+)
```python
import asyncio

async def say_after(delay, what):
    await asyncio.sleep(delay)
    print(what)

async def main():
    print("Started")
    await say_after(1, "Hello")
    await say_after(2, "World")
    print("Finished")

# Run coroutine
# asyncio.run(main())  # Uncomment to run
```

## Pattern Matching (Python 3.10+)

### Structural Pattern Matching
```python
# Pattern matching with match (Python 3.10+)
def process_data(data):
    match data:
        case int():
            return "Integer"
        case str():
            return "String"
        case list():
            return "List"
        case dict():
            return "Dictionary"
        case _:
            return "Unknown type"

# Test pattern matching
print(process_data(42))
print(process_data("hello"))
print(process_data([1, 2, 3]))

# Advanced pattern matching
def describe_point(point):
    match point:
        case (0, 0):
            return "Origin"
        case (x, 0):
            return f"On x-axis at {x}"
        case (0, y):
            return f"On y-axis at {y}"
        case (x, y):
            return f"Point at ({x}, {y})"

print(describe_point((0, 0)))
print(describe_point((5, 0)))
print(describe_point((0, 3)))
print(describe_point((5, 7)))
```

## Best Practices

### Control Flow Best Practices
```python
# Use meaningful variable names
user_age = 25  # Good
a = 25        # Bad

# Keep conditions simple and readable
if age >= 18 and has_license:  # Good
    print("Can drive")

# Avoid deeply nested conditions
if age >= 18:
    if has_license:
        print("Can drive")

# Better approach
can_drive = age >= 18 and has_license
if can_drive:
    print("Can drive")

# Use list comprehensions when appropriate
# Good - simple transformation
squares = [x**2 for x in range(10)]

# Avoid complex list comprehensions
# Bad - too complex
result = [x**2 for x in range(100) if x % 2 == 0 if x > 10]

# Better - use regular loop
result = []
for x in range(100):
    if x % 2 == 0 and x > 10:
        result.append(x**2)

# Handle exceptions appropriately
try:
    result = risky_operation()
except SpecificError as e:
    handle_specific_error(e)
except Exception as e:
    handle_general_error(e)
```

### Performance Considerations
```python
# Use appropriate data structures
# Fast membership testing
my_set = set(range(100000))
if 99999 in my_set:  # O(1) average
    print("Found")

# Slower membership testing
my_list = list(range(100000))
if 99999 in my_list:  # O(n)
    print("Found")

# Use generators for large sequences
# Memory efficient
large_gen = (x**2 for x in range(1000000))

# Less memory efficient
large_list = [x**2 for x in range(1000000)]

# Use built-in functions when possible
# Good
total = sum(range(1000))

# Less efficient
total = 0
for i in range(1000):
    total += i
```

## Summary

Python control flow structures include:

**Conditional Statements:**
- `if`, `elif`, `else` for branching
- Ternary operator for simple conditions
- Pattern matching (Python 3.10+)

**Loops:**
- `for` loops for iteration
- `while` loops for conditional iteration
- Loop control with `break`, `continue`, `pass`

**Functional Constructs:**
- List/dict/set comprehensions
- Generator expressions
- `map()`, `filter()`, `reduce()`

**Advanced Features:**
- Context managers (`with` statement)
- Generators and `yield`
- Coroutines with `async/await`
- Exception handling with `try/except/finally`

Choose the right control flow structure based on readability, performance, and the specific requirements of your code.
