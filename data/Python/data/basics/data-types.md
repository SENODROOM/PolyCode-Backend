# Python Data Types

## Basic Data Types

### Numeric Types
```python
# Integer (int)
age = 25
temperature = -10
large_number = 12345678901234567890

print(f"Age: {age}, Type: {type(age)}")
print(f"Temperature: {temperature}, Type: {type(temperature)}")
print(f"Large number: {large_number}, Type: {type(large_number)}")

# Float (float)
pi = 3.14159
scientific = 1.23e-4
negative_float = -2.5

print(f"Pi: {pi}, Type: {type(pi)}")
print(f"Scientific: {scientific}, Type: {type(scientific)}")

# Complex (complex)
z = 3 + 4j
print(f"Complex: {z}, Type: {type(z)}")
print(f"Real part: {z.real}, Imaginary part: {z.imag}")
```

### Boolean Type
```python
# Boolean (bool)
is_true = True
is_false = False

print(f"True: {is_true}, Type: {type(is_true)}")
print(f"False: {is_false}, Type: {type(is_false)}")

# Boolean operations
print(f"True and False: {True and False}")
print(f"True or False: {True or False}")
print(f"not True: {not True}")

# Truthiness
print(f"0 is truthy: {bool(0)}")
print(f"1 is truthy: {bool(1)}")
print(f"Empty string is truthy: {bool('')}")
print(f"Non-empty string is truthy: {bool('hello')}")
```

### String Type
```python
# String (str)
single_quote = 'Hello'
double_quote = "World"
triple_quote = '''Multi-line
string'''

print(f"Single: {single_quote}")
print(f"Double: {double_quote}")
print(f"Triple: {triple_quote}")

# String operations
greeting = "Hello, World!"
print(f"Length: {len(greeting)}")
print(f"Upper: {greeting.upper()}")
print(f"Lower: {greeting.lower()}")
print(f"Title: {greeting.title()}")

# String formatting
name = "Alice"
age = 25
print(f"Formatted: {name} is {age} years old")
print("Old format: %s is %d years old" % (name, age))
print("Format method: {} is {} years old".format(name, age))
```

## Collection Types

### List
```python
# List (mutable, ordered)
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", 3.14, True]
nested = [[1, 2], [3, 4]]

print(f"Numbers: {numbers}")
print(f"Mixed: {mixed}")
print(f"Nested: {nested}")

# List operations
numbers.append(6)
numbers.insert(0, 0)
numbers.extend([7, 8, 9])
removed = numbers.pop()
sliced = numbers[2:5]

print(f"After operations: {numbers}")
print(f"Removed: {removed}")
print(f"Sliced: {sliced}")

# List comprehensions
squares = [x**2 for x in range(10)]
even_numbers = [x for x in range(10) if x % 2 == 0]
print(f"Squares: {squares}")
print(f"Even numbers: {even_numbers}")
```

### Tuple
```python
# Tuple (immutable, ordered)
coordinates = (10, 20)
single_element = (42,)  # Note the comma
empty_tuple = ()

print(f"Coordinates: {coordinates}")
print(f"Single element: {single_element}")

# Tuple operations
x, y = coordinates  # Unpacking
print(f"Unpacked: x={x}, y={y}")

# Tuple methods
numbers = (1, 2, 3, 2, 1)
print(f"Count of 2: {numbers.count(2)}")
print(f"Index of 3: {numbers.index(3)}")

# Named tuples (Python 3.6+)
from collections import namedtuple
Point = namedtuple('Point', ['x', 'y'])
p = Point(10, 20)
print(f"Point: {p.x}, {p.y}")
```

### Set
```python
# Set (mutable, unordered, unique elements)
numbers = {1, 2, 3, 4, 5}
duplicates = {1, 2, 2, 3, 3}  # Automatically removes duplicates

print(f"Numbers: {numbers}")
print(f"Duplicates removed: {duplicates}")

# Set operations
set1 = {1, 2, 3, 4}
set2 = {3, 4, 5, 6}

print(f"Union: {set1 | set2}")
print(f"Intersection: {set1 & set2}")
print(f"Difference: {set1 - set2}")
print(f"Symmetric difference: {set1 ^ set2}")

# Set methods
numbers.add(6)
numbers.update({7, 8, 9})
numbers.remove(1)
numbers.discard(10)  # No error if not found

print(f"Modified set: {numbers}")
```

### Dictionary
```python
# Dictionary (mutable, key-value pairs)
person = {
    "name": "Alice",
    "age": 25,
    "city": "New York"
}

print(f"Person: {person}")

# Dictionary operations
person["email"] = "alice@example.com"
person["age"] = 26  # Update
del person["city"]   # Delete

print(f"Modified person: {person}")

# Dictionary methods
keys = person.keys()
values = person.values()
items = person.items()

print(f"Keys: {keys}")
print(f"Values: {values}")
print(f"Items: {items}")

# Dictionary comprehension
squares = {x: x**2 for x in range(5)}
print(f"Squares dict: {squares}")
```

## Special Data Types

### None Type
```python
# None represents absence of value
result = None
empty_list = []
empty_string = ""

print(f"None: {result}, Type: {type(result)}")
print(f"Empty list is None: {empty_list is None}")
print(f"Empty string is None: {empty_string is None}")

# Checking for None
def get_value(condition):
    if condition:
        return "Some value"
    return None

value = get_value(False)
if value is None:
    print("No value returned")
```

### Bytes and Bytearray
```python
# Bytes (immutable)
text = "Hello"
bytes_data = text.encode('utf-8')

print(f"Bytes: {bytes_data}")
print(f"Type: {type(bytes_data)}")

# Bytearray (mutable)
mutable_bytes = bytearray(b"Hello")
mutable_bytes[0] = ord('J')

print(f"Mutable bytes: {mutable_bytes}")
print(f"Type: {type(mutable_bytes)}")
```

## Type Checking and Conversion

### Type Checking
```python
# Using type()
value = 42
print(f"Type of {value}: {type(value)}")

# Using isinstance()
print(f"Is int: {isinstance(value, int)}")
print(f"Is str: {isinstance(value, str)}")

# Checking multiple types
print(f"Is int or float: {isinstance(value, (int, float))}")

# Type hints (Python 3.5+)
from typing import Union, List, Dict, Optional

def process_data(data: Union[int, str]) -> str:
    if isinstance(data, int):
        return f"Processing integer: {data}"
    elif isinstance(data, str):
        return f"Processing string: {data}"
    return "Unknown type"

print(process_data(42))
print(process_data("hello"))
```

### Type Conversion
```python
# Numeric conversions
int_val = int(3.14)      # 3
float_val = float(42)    # 42.0
str_val = str(123)        # "123"

print(f"int(3.14): {int_val}")
print(f"float(42): {float_val}")
print(f"str(123): {str_val}")

# String to numeric
num_str = "123"
num_int = int(num_str)
num_float = float(num_str)

print(f"String '{num_str}' to int: {num_int}")
print(f"String '{num_str}' to float: {num_float}")

# List to tuple and vice versa
my_list = [1, 2, 3]
my_tuple = tuple(my_list)
my_list_from_tuple = list(my_tuple)

print(f"List to tuple: {my_tuple}")
print(f"Tuple to list: {my_list_from_tuple}")

# String to list and vice versa
sentence = "Hello world"
words = sentence.split()
joined = " ".join(words)

print(f"String to list: {words}")
print(f"List to string: {joined}")
```

## Advanced Data Types

### Range
```python
# Range object (sequence of numbers)
r1 = range(10)        # 0-9
r2 = range(1, 11)     # 1-10
r3 = range(0, 10, 2)  # 0, 2, 4, 6, 8

print(f"Range 0-9: {list(r1)}")
print(f"Range 1-10: {list(r2)}")
print(f"Range step 2: {list(r3)}")

# Range operations
print(f"Length of range: {len(r1)}")
print(f"First element: {r1[0]}")
print(f"Last element: {r1[-1]}")
```

### Frozenset
```python
# Frozenset (immutable set)
immutable_set = frozenset([1, 2, 3, 4, 5])

print(f"Frozenset: {immutable_set}")
print(f"Type: {type(immutable_set)}")

# Frozenset operations
set1 = {1, 2, 3}
set2 = frozenset({3, 4, 5})

print(f"Union: {set1 | set2}")
print(f"Intersection: {set1 & set2}")
```

### Memoryview
```python
# Memoryview (memory-efficient view of binary data)
data = b"Hello, World!"
mv = memoryview(data)

print(f"Memoryview: {mv}")
print(f"Type: {type(mv)}")

# Modifying through memoryview
mutable_data = bytearray(b"Hello")
mv_mutable = memoryview(mutable_data)
mv_mutable[0] = ord('J')

print(f"Modified: {mutable_data}")
```

## Custom Data Types

### Using dataclasses (Python 3.7+)
```python
from dataclasses import dataclass
from typing import List

@dataclass
class Person:
    name: str
    age: int
    email: str = "unknown@example.com"
    friends: List[str] = None
    
    def __post_init__(self):
        if self.friends is None:
            self.friends = []

# Creating instances
person1 = Person("Alice", 25, "alice@example.com")
person2 = Person("Bob", 30)

print(f"Person 1: {person1}")
print(f"Person 2: {person2}")

# Accessing fields
print(f"Name: {person1.name}")
print(f"Age: {person1.age}")
```

### Using NamedTuple
```python
from collections import namedtuple

# Creating a custom data type
Employee = namedtuple('Employee', ['name', 'id', 'department'])

emp1 = Employee("Alice", 1001, "Engineering")
emp2 = Employee("Bob", 1002, "Marketing")

print(f"Employee 1: {emp1}")
print(f"Name: {emp1.name}")
print(f"ID: {emp1.id}")
print(f"Department: {emp1.department}")
```

## Type System Features

### Duck Typing
```python
# Python uses duck typing - "if it walks like a duck and quacks like a duck"
class Duck:
    def quack(self):
        return "Quack!"

class Person:
    def quack(self):
        return "I'm quacking!"

def make_quack(obj):
    return obj.quack()

duck = Duck()
person = Person()

print(f"Duck says: {make_quack(duck)}")
print(f"Person says: {make_quack(person)}")
```

### Dynamic vs Static Typing
```python
# Dynamic typing - types checked at runtime
def process_data(data):
    if isinstance(data, str):
        return data.upper()
    elif isinstance(data, (int, float)):
        return data * 2
    return str(data)

print(f"String: {process_data('hello')}")
print(f"Number: {process_data(5)}")
print(f"Other: {process_data([1, 2, 3])}")

# Type hints for better IDE support and documentation
from typing import Union

def process_data_typed(data: Union[str, int, float]) -> Union[str, int, float]:
    if isinstance(data, str):
        return data.upper()
    elif isinstance(data, (int, float)):
        return data * 2
    return data
```

## Performance Considerations

### Choosing the Right Data Type
```python
import timeit

# List vs Set for membership testing
large_list = list(range(100000))
large_set = set(range(100000))

# Test list membership
list_time = timeit.timeit(lambda: 99999 in large_list, number=1000)

# Test set membership
set_time = timeit.timeit(lambda: 99999 in large_set, number=1000)

print(f"List membership time: {list_time:.6f} seconds")
print(f"Set membership time: {set_time:.6f} seconds")

# String concatenation methods
def concat_plus():
    result = ""
    for i in range(1000):
        result += str(i)
    return result

def concat_join():
    return "".join(str(i) for i in range(1000))

# Compare performance
plus_time = timeit.timeit(concat_plus, number=100)
join_time = timeit.timeit(concat_join, number=100)

print(f"String concatenation (+): {plus_time:.6f} seconds")
print(f"String concatenation (join): {join_time:.6f} seconds")
```

## Best Practices

### Data Type Best Practices
```python
# Use appropriate data types
from typing import List, Dict, Optional, Union

# Use specific types when possible
names: List[str] = ["Alice", "Bob", "Charlie"]
ages: Dict[str, int] = {"Alice": 25, "Bob": 30}

# Use Optional for nullable types
def find_user(user_id: int) -> Optional[str]:
    users = {1: "Alice", 2: "Bob"}
    return users.get(user_id)

# Use Union for multiple possible types
def process_value(value: Union[int, str, float]) -> str:
    return str(value)

# Use frozenset for immutable sets
CONFIG_KEYS = frozenset(["host", "port", "timeout"])

# Use tuple for fixed collections
COORDINATES = (10, 20, 30)

# Use constants for fixed values
DEFAULT_TIMEOUT = 30
MAX_CONNECTIONS = 100
```

### Memory Efficiency
```python
# Use generators for large sequences
def large_sequence():
    for i in range(1000000):
        yield i

# More memory efficient than list(range(1000000))
sequence_gen = large_sequence()

# Use __slots__ for classes with many instances
class Point:
    __slots__ = ['x', 'y']  # Reduces memory usage
    
    def __init__(self, x, y):
        self.x = x
        self.y = y

# Use appropriate numeric types
small_numbers = [i for i in range(100)]  # int
precise_numbers = [float(i) for i in range(100)]  # float when precision needed
```

## Summary

Python provides a rich set of built-in data types:

**Basic Types:**
- `int` - Integers of arbitrary size
- `float` - Floating-point numbers
- `str` - Unicode strings
- `bool` - Boolean values
- `None` - Null value

**Collection Types:**
- `list` - Mutable ordered sequences
- `tuple` - Immutable ordered sequences
- `set` - Mutable unordered unique elements
- `frozenset` - Immutable unordered unique elements
- `dict` - Mutable key-value mappings

**Special Types:**
- `bytes` - Immutable byte sequences
- `bytearray` - Mutable byte sequences
- `range` - Sequence of numbers
- `memoryview` - Memory-efficient views

Choose the right data type based on your needs for mutability, ordering, uniqueness, and performance requirements.
