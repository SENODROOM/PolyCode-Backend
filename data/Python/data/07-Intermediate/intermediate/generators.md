# Python Generators

## Generator Basics

### What is a Generator?
```python
# A generator is a special type of iterator that generates values on the fly
# Instead of returning a single value, generators yield multiple values

def simple_generator():
    yield 1
    yield 2
    yield 3

# Create a generator object
gen = simple_generator()
print(f"Generator object: {gen}")
print(f"Type: {type(gen)}")

# Consume the generator
print(f"First yield: {next(gen)}")
print(f"Second yield: {next(gen)}")
print(f"Third yield: {next(gen)}")

# Trying to get another value will raise StopIteration
try:
    next(gen)
except StopIteration:
    print("Generator exhausted")

# Reset generator and use in for loop
gen = simple_generator()
for value in gen:
    print(f"For loop value: {value}")
```

### Generator Functions vs Regular Functions
```python
# Regular function returns all values at once
def regular_function():
    numbers = []
    for i in range(5):
        numbers.append(i * 2)
    return numbers

# Generator function yields values one at a time
def generator_function():
    for i in range(5):
        yield i * 2

# Compare memory usage
import sys

regular_result = regular_function()
generator_result = generator_function()

print(f"Regular function result: {regular_result}")
print(f"Regular function memory: {sys.getsizeof(regular_result)} bytes")

print(f"Generator function result: {list(generator_result)}")
print(f"Generator object memory: {sys.getsizeof(generator_result)} bytes")
```

### Generator with Parameters
```python
def count_up_to(max_number):
    """Count from 1 to max_number."""
    count = 1
    while count <= max_number:
        yield count
        count += 1

def fibonacci_sequence(n):
    """Generate first n Fibonacci numbers."""
    a, b = 0, 1
    for _ in range(n):
        yield a
        a, b = b, a + b

def powers_of_two(limit):
    """Generate powers of 2 up to limit."""
    power = 1
    while power <= limit:
        yield power
        power *= 2

# Using parameterized generators
print("Counting to 5:")
for num in count_up_to(5):
    print(f"  {num}")

print("\nFirst 10 Fibonacci numbers:")
for fib in fibonacci_sequence(10):
    print(f"  {fib}")

print("\nPowers of 2 up to 100:")
for power in powers_of_two(100):
    print(f"  {power}")
```

## Generator Expressions

### List Comprehension vs Generator Expression
```python
# List comprehension (creates entire list in memory)
list_comp = [x ** 2 for x in range(10)]
print(f"List comprehension: {list_comp}")
print(f"Memory usage: {sys.getsizeof(list_comp)} bytes")

# Generator expression (creates generator object)
gen_expr = (x ** 2 for x in range(10))
print(f"Generator expression: {list(gen_expr)}")
print(f"Memory usage: {sys.getsizeof(gen_expr)} bytes")

# Generator expressions are more memory efficient for large datasets
large_list_comp = [x for x in range(1000000)]
large_gen_expr = (x for x in range(1000000))

print(f"Large list memory: {sys.getsizeof(large_list_comp)} bytes")
print(f"Large generator memory: {sys.getsizeof(large_gen_expr)} bytes")
```

### Generator Expression Examples
```python
# Filtering with generator expression
numbers = range(1, 21)
even_numbers = (x for x in numbers if x % 2 == 0)
print(f"Even numbers: {list(even_numbers)}")

# Chaining operations
squared_evens = (x ** 2 for x in even_numbers)
print(f"Squared evens: {list(squared_evens)}")

# Generator expression in function call
total = sum(x ** 2 for x in range(100) if x % 2 == 0)
print(f"Sum of squared evens (0-99): {total}")

# Finding first matching element
first_large = next((x for x in range(100) if x > 50), None)
print(f"First number > 50: {first_large}")
```

## Advanced Generator Patterns

### Infinite Generators
```python
def infinite_counter():
    """Generate numbers indefinitely."""
    count = 0
    while True:
        yield count
        count += 1

def prime_generator():
    """Generate prime numbers indefinitely."""
    def is_prime(n):
        if n < 2:
            return False
        for i in range(2, int(n ** 0.5) + 1):
            if n % i == 0:
                return False
        return True
    
    num = 2
    while True:
        if is_prime(num):
            yield num
        num += 1

def random_numbers():
    """Generate random numbers indefinitely."""
    import random
    while True:
        yield random.random()

# Using infinite generators
counter = infinite_counter()
print("First 5 counts:")
for _ in range(5):
    print(f"  {next(counter)}")

primes = prime_generator()
print("\nFirst 10 primes:")
for _ in range(10):
    print(f"  {next(primes)}")
```

### Generator Chaining
```python
def chain_generators(*generators):
    """Chain multiple generators together."""
    for generator in generators:
        yield from generator

def numbers_1_to_5():
    yield from range(1, 6)

def numbers_6_to_10():
    yield from range(6, 11)

def letters_a_to_e():
    for char in 'abcde':
        yield char

def letters_f_to_j():
    for char in 'fghij':
        yield char

# Chain generators
combined = chain_generators(numbers_1_to_5(), numbers_6_to_10(), 
                           letters_a_to_e(), letters_f_to_j)

print("Chained generators:")
for item in combined:
    print(f"  {item}")
```

### Generator Pipelines
```python
def read_file_lines(filename):
    """Read file line by line."""
    with open(filename, 'r') as file:
        for line in file:
            yield line.strip()

def filter_lines(lines, keyword):
    """Filter lines containing keyword."""
    for line in lines:
        if keyword in line:
            yield line

def transform_lines(lines, transform_func):
    """Transform lines using a function."""
    for line in lines:
        yield transform_func(line)

# Create a sample file for demonstration
sample_content = """Python is awesome
Python generators are powerful
Learning Python is fun
Python programming rocks
Generators save memory"""

with open('sample.txt', 'w') as f:
    f.write(sample_content)

# Create pipeline
lines = read_file_lines('sample.txt')
python_lines = filter_lines(lines, 'Python')
uppercase_lines = transform_lines(python_lines, str.upper)

print("Pipeline results:")
for line in uppercase_lines:
    print(f"  {line}")
```

## Generator Methods

### send() Method
```python
def accumulator():
    """Accumulator that receives values via send()."""
    total = 0
    while True:
        received = yield total
        if received is not None:
            total += received

# Create and start the generator
acc = accumulator()
next(acc)  # Prime the generator

print(f"Initial total: {acc.send(10)}")
print(f"After adding 10: {acc.send(5)}")
print(f"After adding 5: {acc.send(15)}")
print(f"After adding 15: {acc.send(0)}")  # Send 0 to just get current total
```

### throw() Method
```python
def generator_with_error_handling():
    """Generator that can handle thrown exceptions."""
    try:
        while True:
            received = yield
            print(f"Received: {received}")
    except ValueError as e:
        print(f"ValueError caught: {e}")
        yield "Error handled"
    except GeneratorExit:
        print("Generator exiting")
        raise

gen = generator_with_error_handling()
next(gen)  # Prime the generator

gen.send("Hello")
gen.send("World")

# Throw an exception
result = gen.throw(ValueError("Invalid value"))
print(f"After throw: {result}")

gen.send("Back to normal")

# Close the generator
gen.close()
```

### close() Method
```python
def resource_generator():
    """Generator that manages resources."""
    try:
        print("Resource acquired")
        yield "Using resource"
        yield "Still using resource"
    finally:
        print("Resource released")

gen = resource_generator()
print(f"First yield: {next(gen)}")
print(f"Second yield: {next(gen)}")

# Close the generator (triggers finally block)
gen.close()

# Generator automatically closed when garbage collected
def auto_cleanup_generator():
    print("Generator created")
    try:
        yield "Value 1"
        yield "Value 2"
    finally:
        print("Cleanup performed")

# When generator goes out of scope, cleanup is performed
def use_generator():
    gen = auto_cleanup_generator()
    print(f"Got: {next(gen)}")
    # Generator will be cleaned up when function exits

use_generator()
```

## Practical Applications

### Processing Large Files
```python
def process_large_file(filename, chunk_size=1024):
    """Process large file in chunks."""
    with open(filename, 'r') as file:
        while True:
            chunk = file.read(chunk_size)
            if not chunk:
                break
            yield chunk

# Simulate large file processing
def create_large_file():
    content = "This is a line in a large file.\n" * 1000
    with open('large_file.txt', 'w') as f:
        f.write(content)

create_large_file()

# Process file without loading it all into memory
chunk_count = 0
for chunk in process_large_file('large_file.txt'):
    chunk_count += 1
    if chunk_count <= 3:  # Show first few chunks
        print(f"Chunk {chunk_count}: {chunk[:50]}...")

print(f"Total chunks processed: {chunk_count}")
```

### Data Processing Pipeline
```python
def read_csv(filename):
    """Read CSV file row by row."""
    import csv
    with open(filename, 'r') as file:
        reader = csv.reader(file)
        for row in reader:
            yield row

def filter_rows(rows, column_index, condition):
    """Filter rows based on column condition."""
    for row in rows:
        if condition(row[column_index]):
            yield row

def transform_rows(rows, column_index, transform_func):
    """Transform specific column in rows."""
    for row in rows:
        row[column_index] = transform_func(row[column_index])
        yield row

def aggregate_rows(rows, key_func, agg_func):
    """Aggregate rows by key."""
    groups = {}
    for row in rows:
        key = key_func(row)
        if key not in groups:
            groups[key] = []
        groups[key].append(row)
    
    for key, group_rows in groups.items():
        yield key, agg_func(group_rows)

# Create sample CSV
csv_content = """name,age,salary
Alice,25,50000
Bob,30,60000
Charlie,25,45000
David,35,70000
Eve,30,55000"""

with open('employees.csv', 'w') as f:
    f.write(csv_content)

# Build processing pipeline
pipeline = read_csv('employees.csv')
pipeline = filter_rows(pipeline, 1, lambda age: int(age) >= 30)  # Filter age >= 30
pipeline = transform_rows(pipeline, 2, lambda salary: float(salary) * 1.1)  # 10% raise

print("Processed employees (age >= 30, 10% raise):")
for row in pipeline:
    print(f"  {row}")
```

### Web Scraping with Generators
```python
def scrape_urls(urls):
    """Scrape multiple URLs yielding results as they come."""
    import requests
    import time
    
    for url in urls:
        try:
            response = requests.get(url, timeout=5)
            yield url, response.status_code, len(response.text)
        except Exception as e:
            yield url, None, str(e)
        time.sleep(1)  # Be respectful to servers

# Example with mock URLs (replace with real URLs to test)
test_urls = [
    "https://httpbin.org/status/200",
    "https://httpbin.org/status/404",
    "https://httpbin.org/status/500"
]

print("Scraping results:")
for url, status, size in scrape_urls(test_urls):
    if status:
        print(f"  {url}: Status {status}, Size {size} bytes")
    else:
        print(f"  {url}: Error - {size}")
```

### Real-time Data Processing
```python
import time
import random

def sensor_data_generator(interval=1):
    """Generate sensor data in real-time."""
    while True:
        timestamp = time.time()
        temperature = random.uniform(20.0, 30.0)
        humidity = random.uniform(30.0, 70.0)
        yield timestamp, temperature, humidity
        time.sleep(interval)

def alert_generator(data_generator, temp_threshold=28.0):
    """Generate alerts when conditions are met."""
    for timestamp, temp, humidity in data_generator:
        if temp > temp_threshold:
            yield timestamp, temp, humidity, f"High temperature alert: {temp:.1f}°C"

def moving_average(data_generator, window_size=5):
    """Calculate moving average of temperature."""
    window = []
    for timestamp, temp, humidity in data_generator:
        window.append(temp)
        if len(window) > window_size:
            window.pop(0)
        
        if len(window) == window_size:
            avg_temp = sum(window) / len(window)
            yield timestamp, avg_temp

# Simulate real-time processing
sensor_gen = sensor_data_generator(interval=0.5)
alert_gen = alert_generator(sensor_gen)

print("Monitoring for alerts (10 samples):")
for i, (timestamp, temp, humidity, alert) in enumerate(alert_gen):
    if i < 10:
        print(f"  {alert}")
    else:
        break
```

## Generator Performance

### Memory Efficiency
```python
import sys
import tracemalloc

def list_of_squares(n):
    """Return list of squares."""
    return [i ** 2 for i in range(n)]

def generator_of_squares(n):
    """Generate squares."""
    for i in range(n):
        yield i ** 2

# Compare memory usage
n = 1000000

# List version
tracemalloc.start()
squares_list = list_of_squares(n)
current, peak = tracemalloc.get_traced_memory()
print(f"List memory usage: {peak / 1024 / 1024:.2f} MB")
tracemalloc.stop()

# Generator version
tracemalloc.start()
squares_gen = generator_of_squares(n)
current, peak = tracemalloc.get_traced_memory()
print(f"Generator memory usage: {peak / 1024:.2f} KB")
tracemalloc.stop()

# Process generator to show it works
count = 0
for square in squares_gen:
    count += 1
    if count >= 10:  # Just process first 10
        break
print(f"Processed {count} items from generator")
```

### Time Efficiency
```python
import timeit

def sum_with_list(n):
    """Sum using list comprehension."""
    return sum([i ** 2 for i in range(n)])

def sum_with_generator(n):
    """Sum using generator expression."""
    return sum((i ** 2 for i in range(n)))

def sum_with_generator_no_parens(n):
    """Sum using generator without parentheses."""
    return sum(i ** 2 for i in range(n))

# Time comparison
n = 1000000

list_time = timeit.timeit(lambda: sum_with_list(n), number=10)
gen_time = timeit.timeit(lambda: sum_with_generator(n), number=10)
gen_no_parens_time = timeit.timeit(lambda: sum_with_generator_no_parens(n), number=10)

print(f"List comprehension time: {list_time:.4f} seconds")
print(f"Generator expression time: {gen_time:.4f} seconds")
print(f"Generator no parentheses time: {gen_no_parens_time:.4f} seconds")
```

## Best Practices

### Generator Best Practices
```python
# 1. Use descriptive names for generator functions
def user_data_generator(database_connection):
    """Generate user data from database."""
    pass

# 2. Keep generators simple and focused
def simple_generator():
    """Simple, focused generator."""
    yield from range(10)

# 3. Use yield from for delegation
def delegating_generator():
    """Delegate to another generator."""
    yield from simple_generator()

# 4. Handle exceptions properly
def robust_generator():
    """Generator with proper error handling."""
    try:
        while True:
            try:
                data = yield
                # Process data
                yield f"Processed: {data}"
            except ValueError:
                yield "Invalid data"
                continue
    except GeneratorExit:
        # Cleanup code
        pass

# 5. Use context managers for resource management
from contextlib import contextmanager

@contextmanager
def file_reader(filename):
    """Context manager for file reading."""
    file = open(filename, 'r')
    try:
        yield file
    finally:
        file.close()

# 6. Document generator behavior
def documented_generator():
    """
    Generator that yields numbers 1 to 5.
    
    Yields:
        int: Numbers from 1 to 5
    """
    for i in range(1, 6):
        yield i

# 7. Use type hints for clarity
from typing import Generator, Iterator

def typed_generator() -> Iterator[int]:
    """Generator with type hints."""
    yield from range(10)

# 8. Consider using itertools for complex operations
import itertools

def complex_generator():
    """Complex generator using itertools."""
    # Chain multiple iterators
    yield from itertools.chain(range(5), range(10, 15))
    
    # Combinations
    yield from itertools.combinations('ABCD', 2)
    
    # Cycle through values
    yield from itertools.islice(itertools.cycle('XYZ'), 5)

# 9. Use generators for lazy evaluation
def lazy_evaluation():
    """Demonstrate lazy evaluation benefits."""
    # This won't execute until the generator is consumed
    yield from (expensive_operation(x) for x in range(1000))

def expensive_operation(x):
    """Simulate expensive computation."""
    import time
    time.sleep(0.001)  # Simulate work
    return x * 2

# 10. Profile when necessary
def profile_generator():
    """Profile generator performance."""
    import cProfile
    
    def profile_func():
        gen = (x ** 2 for x in range(10000))
        return sum(gen)
    
    cProfile.run('profile_func()')
```

## Summary

Python generators provide powerful capabilities:

**Core Benefits:**
- Memory efficiency for large datasets
- Lazy evaluation for performance
- Clean syntax for iterators
- Pipeline processing capabilities

**Key Features:**
- `yield` keyword for value generation
- Generator expressions for concise syntax
- `send()`, `throw()`, and `close()` methods
- `yield from` for delegation

**Common Patterns:**
- Processing large files and streams
- Real-time data processing
- Web scraping and API calls
- Mathematical sequences and algorithms
- Data transformation pipelines

**Performance Considerations:**
- Significant memory savings for large datasets
- Slightly slower than list comprehensions for small data
- Excellent for streaming and infinite sequences
- Lazy evaluation reduces unnecessary computation

**Best Practices:**
- Use generators for large or infinite sequences
- Keep generators simple and focused
- Handle exceptions and cleanup properly
- Document behavior and yielded types
- Profile when performance is critical

Generators are essential tools for writing efficient, memory-conscious Python code, especially when dealing with large datasets or streaming data.
