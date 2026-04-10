# Python Variables

## Variable Declaration and Assignment

### Basic Variable Assignment
```python
# Integer variable
age = 25
print(f"Age: {age}")

# Float variable
height = 5.8
print(f"Height: {height}")

# String variable
name = "John Doe"
print(f"Name: {name}")

# Boolean variable
is_student = True
print(f"Is student: {is_student}")

# None value
result = None
print(f"Result: {result}")
```

### Dynamic Typing
```python
# Python is dynamically typed - same variable can hold different types
x = 10          # Integer
print(f"x is {type(x)}: {x}")

x = "Hello"     # String
print(f"x is {type(x)}: {x}")

x = [1, 2, 3]   # List
print(f"x is {type(x)}: {x}")

x = 3.14        # Float
print(f"x is {type(x)}: {x}")
```

### Multiple Assignment
```python
# Multiple variables at once
a, b, c = 10, 20, 30
print(f"a={a}, b={b}, c={c}")

# Same value to multiple variables
x = y = z = 0
print(f"x={x}, y={y}, z={z}")

# Swapping variables
a, b = b, a
print(f"After swap: a={a}, b={b}")

# Unpacking
numbers = [1, 2, 3, 4, 5]
first, second, *rest = numbers
print(f"First: {first}, Second: {second}, Rest: {rest}")
```

## Variable Naming Rules

### Valid Variable Names
```python
# Valid names
user_name = "Alice"
age1 = 25
_private_var = "secret"
camelCase = "mixed"
snake_case = "python_style"
CONSTANT_VALUE = 42
```

### Invalid Variable Names
```python
# These will cause SyntaxError
# 2name = "invalid"      # Cannot start with number
# user-name = "invalid"   # Cannot contain hyphens
# class = "invalid"      # Cannot use reserved keywords
# var$ = "invalid"       # Cannot contain special characters
```

### Naming Conventions
```python
# Snake case for variables (PEP 8)
first_name = "John"
last_name = "Doe"
user_age = 25
is_active = True

# Constants in uppercase (convention)
PI = 3.14159
MAX_USERS = 1000
DEFAULT_TIMEOUT = 30

# Private variables (convention)
_internal_var = "internal"
__private_var = "very_private"
```

## Data Types

### Basic Data Types
```python
# Integer
integer_var = 42
print(f"Integer: {integer_var}, Type: {type(integer_var)}")

# Float
float_var = 3.14159
print(f"Float: {float_var}, Type: {type(float_var)}")

# String
string_var = "Hello, Python!"
print(f"String: {string_var}, Type: {type(string_var)}")

# Boolean
bool_var = True
print(f"Boolean: {bool_var}, Type: {type(bool_var)}")

# None
none_var = None
print(f"None: {none_var}, Type: {type(none_var)}")
```

### Type Conversion
```python
# Implicit conversion
result = 5 + 3.14  # int + float = float
print(f"5 + 3.14 = {result}, Type: {type(result)}")

# Explicit conversion
num_str = "123"
num_int = int(num_str)
print(f"String '{num_str}' to int: {num_int}")

float_num = 3.99
int_num = int(float_num)
print(f"Float {float_num} to int: {int_num}")

# String conversion
age = 25
age_str = str(age)
print(f"Int {age} to string: '{age_str}'")

# Boolean conversion
zero = 0
non_zero = 42
print(f"0 to bool: {bool(zero)}")
print(f"42 to bool: {bool(non_zero)}")
print(f"Empty string to bool: {bool('')}")
print(f"Non-empty string to bool: {bool('hello')}")
```

## Variable Scope

### Local and Global Variables
```python
global_var = "I am global"

def function_example():
    local_var = "I am local"
    print(f"Inside function - global_var: {global_var}")
    print(f"Inside function - local_var: {local_var}")

function_example()
# print(local_var)  # This would cause NameError

# Modifying global variable
counter = 0

def increment_counter():
    global counter  # Declare we're using the global variable
    counter += 1
    print(f"Counter inside function: {counter}")

increment_counter()
print(f"Counter outside function: {counter}")
```

### Nonlocal Variables (Nested Functions)
```python
def outer_function():
    outer_var = "I am outer"
    
    def inner_function():
        nonlocal outer_var  # Declare we're using the outer variable
        outer_var = "I am modified by inner"
        print(f"Inside inner - outer_var: {outer_var}")
    
    print(f"Before inner call - outer_var: {outer_var}")
    inner_function()
    print(f"After inner call - outer_var: {outer_var}")

outer_function()
```

## Special Variables

### Built-in Variables
```python
# __name__ - module name
print(f"Module name: {__name__}")

# __doc__ - module docstring
"""
This is a module docstring.
"""
print(f"Module docstring: {__doc__}")

# __file__ - file path (when run from file)
# print(f"File path: {__file__}")

# __package__ - package name
# print(f"Package: {__package__}")
```

### Magic Methods (Dunder Methods)
```python
class MyClass:
    def __init__(self, value):
        self.value = value
    
    def __str__(self):
        return f"MyClass with value: {self.value}"
    
    def __repr__(self):
        return f"MyClass({self.value})"

obj = MyClass(42)
print(f"str(obj): {str(obj)}")
print(f"repr(obj): {repr(obj)}")
```

## Best Practices

### Variable Best Practices
```python
# Use descriptive names
user_age = 25  # Good
a = 25        # Bad - not descriptive

# Initialize variables before use
total = 0
for i in range(10):
    total += i

# Use constants for fixed values
TAX_RATE = 0.08
DISCOUNT_THRESHOLD = 100

# Group related variables
user_name = "Alice"
user_email = "alice@example.com"
user_age = 25

# Use type hints (Python 3.5+)
from typing import List, Optional

def process_data(items: List[int], limit: Optional[int] = None) -> bool:
    if limit is None:
        limit = len(items)
    
    processed = 0
    for item in items[:limit]:
        processed += 1
    
    return processed > 0

# Avoid single-letter variable names (except for counters)
for i in range(10):  # 'i' is acceptable for loop counters
    print(f"Processing item {i}")
```

### Memory Management
```python
# Python handles memory automatically
import sys

# Check memory usage
small_list = [1, 2, 3]
large_list = list(range(1000000))

print(f"Small list size: {sys.getsizeof(small_list)} bytes")
print(f"Large list size: {sys.getsizeof(large_list)} bytes")

# Variables are garbage collected when no longer referenced
def memory_example():
    temp_data = [x for x in range(1000)]
    return sum(temp_data)

# temp_data is automatically cleaned up when function exits
result = memory_example()
```

## Common Pitfalls

### Common Variable Mistakes
```python
# Mistake 1: Using undefined variable
# print(undefined_var)  # NameError

# Mistake 2: Variable shadowing
x = 10
def shadow_example():
    x = 20  # This creates a new local variable
    print(f"Local x: {x}")

shadow_example()
print(f"Global x: {x}")  # Global x is unchanged

# Mistake 3: Mutable default arguments
def bad_function(items=[]):  # Don't do this!
    items.append(1)
    return items

def good_function(items=None):  # Do this instead
    if items is None:
        items = []
    items.append(1)
    return items

# Mistake 4: Modifying list while iterating
numbers = [1, 2, 3, 4, 5]
for num in numbers:
    if num == 3:
        numbers.remove(num)  # This can cause issues

# Better approach
numbers = [1, 2, 3, 4, 5]
numbers = [num for num in numbers if num != 3]
```

## Advanced Topics

### Variable Attributes
```python
class Person:
    species = "Homo sapiens"  # Class attribute
    
    def __init__(self, name, age):
        self.name = name      # Instance attribute
        self.age = age        # Instance attribute

person1 = Person("Alice", 25)
person2 = Person("Bob", 30)

print(f"Class attribute: {Person.species}")
print(f"Person1 name: {person1.name}")
print(f"Person2 age: {person2.age}")

# Adding attributes dynamically
person1.city = "New York"
print(f"Person1 city: {person1.city}")
```

### Environment Variables
```python
import os

# Getting environment variables
home_dir = os.environ.get('HOME', 'Default')
path = os.environ.get('PATH', '')

print(f"Home directory: {home_dir}")
print(f"Path: {path[:50]}...")  # Show first 50 chars

# Setting environment variables
os.environ['MY_VAR'] = 'my_value'
my_var = os.environ.get('MY_VAR')
print(f"My variable: {my_var}")
```

## Summary

Python variables are:
- **Dynamically typed** - no need to declare types
- **Reference-based** - variables point to objects in memory
- **Scope-aware** - local, global, and nonlocal scopes
- **Flexible** - can hold different types over time
- **Memory-managed** - automatic garbage collection

Follow PEP 8 naming conventions and use descriptive names for clean, readable code.
