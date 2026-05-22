# Python Performance Optimization

## Performance Measurement

### Profiling with cProfile
```python
import cProfile
import pstats
import io
import time

def slow_function():
    """Function that simulates slow operations."""
    total = 0
    for i in range(1000000):
        total += i * i
    return total

def fast_function():
    """Optimized version of the slow function."""
    return sum(i * i for i in range(1000000))

def profile_functions():
    """Profile both functions to compare performance."""
    
    # Profile slow function
    profiler = cProfile.Profile()
    profiler.enable()
    
    start = time.time()
    result1 = slow_function()
    end = time.time()
    
    profiler.disable()
    
    print(f"Slow function result: {result1}")
    print(f"Slow function time: {end - start:.4f} seconds")
    
    # Get profiling stats
    s = io.StringIO()
    ps = pstats.Stats(profiler, stream=s).sort_stats('cumulative')
    ps.print_stats(10)
    
    print("Slow function profiling:")
    print(s.getvalue())
    
    # Profile fast function
    profiler = cProfile.Profile()
    profiler.enable()
    
    start = time.time()
    result2 = fast_function()
    end = time.time()
    
    profiler.disable()
    
    print(f"\nFast function result: {result2}")
    print(f"Fast function time: {end - start:.4f} seconds")
    
    # Get profiling stats
    s = io.StringIO()
    ps = pstats.Stats(profiler, stream=s).sort_stats('cumulative')
    ps.print_stats(10)
    
    print("Fast function profiling:")
    print(s.getvalue())

if __name__ == "__main__":
    profile_functions()
```

### Memory Profiling
```python
import tracemalloc
import time

def memory_intensive_function():
    """Function that uses a lot of memory."""
    large_list = []
    for i in range(100000):
        large_list.append([j for j in range(100)])
    return len(large_list)

def memory_optimized_function():
    """Memory-optimized version."""
    # Use generator instead of list comprehension
    large_list = []
    for i in range(100000):
        large_list.append(range(100))  # More memory efficient
    return len(large_list)

def profile_memory():
    """Profile memory usage."""
    
    # Start memory tracing
    tracemalloc.start()
    
    # Take snapshot before
    snapshot1 = tracemalloc.take_snapshot()
    
    # Run memory-intensive function
    result1 = memory_intensive_function()
    
    # Take snapshot after
    snapshot2 = tracemalloc.take_snapshot()
    
    # Compare snapshots
    top_stats = snapshot2.compare_to(snapshot1, 'lineno')
    print("Memory-intensive function top differences:")
    for stat in top_stats[:10]:
        print(stat)
    
    # Reset and test optimized version
    tracemalloc.stop()
    tracemalloc.start()
    
    snapshot1 = tracemalloc.take_snapshot()
    result2 = memory_optimized_function()
    snapshot2 = tracemalloc.take_snapshot()
    
    top_stats = snapshot2.compare_to(snapshot1, 'lineno')
    print("\nMemory-optimized function top differences:")
    for stat in top_stats[:10]:
        print(stat)
    
    tracemalloc.stop()
    
    print(f"\nResults match: {result1 == result2}")

if __name__ == "__main__":
    profile_memory()
```

### Line Profiling
```python
import line_profiler

# Note: This requires line_profiler package
# pip install line_profiler

@line_profiler.profile
def complex_function(n):
    """Function with multiple operations."""
    result = 0
    for i in range(n):
        if i % 2 == 0:
            result += i
        else:
            result -= i
    
    # String operations
    text = "Hello, World! " * 100
    words = text.split()
    word_count = len(words)
    
    # List operations
    numbers = list(range(n))
    squared = [x ** 2 for x in numbers]
    summed = sum(squared)
    
    return result + word_count + summed

def line_profile_example():
    """Demonstrate line profiling."""
    print("Running line profiling...")
    result = complex_function(10000)
    print(f"Result: {result}")

# To run line profiling:
# Save this code and run: kernprof -l -v script_name.py

if __name__ == "__main__":
    line_profile_example()
```

## Algorithm Optimization

### List Operations Optimization
```python
import time
import random

def list_append_vs_extend():
    """Compare list.append vs list.extend performance."""
    
    # Test data
    items1 = list(range(10000))
    items2 = list(range(10000))
    
    # Test append in loop
    start = time.time()
    result1 = []
    for item in items1:
        result1.append(item)
    append_time = time.time() - start
    
    # Test extend
    start = time.time()
    result2 = []
    result2.extend(items2)
    extend_time = time.time() - start
    
    print(f"Append time: {append_time:.6f} seconds")
    print(f"Extend time: {extend_time:.6f} seconds")
    print(f"Speedup: {append_time / extend_time:.2f}x")

def list_comprehension_vs_loop():
    """Compare list comprehension vs for loop."""
    
    data = range(100000)
    
    # List comprehension
    start = time.time()
    result1 = [x * 2 for x in data]
    comp_time = time.time() - start
    
    # For loop
    start = time.time()
    result2 = []
    for x in data:
        result2.append(x * 2)
    loop_time = time.time() - start
    
    print(f"List comprehension time: {comp_time:.6f} seconds")
    print(f"For loop time: {loop_time:.6f} seconds")
    print(f"Speedup: {loop_time / comp_time:.2f}x")
    
    assert result1 == result2

def set_vs_list_lookup():
    """Compare set vs list lookup performance."""
    
    # Create test data
    items = list(range(100000))
    lookup_items = random.sample(items, 1000)
    
    # List lookup
    start = time.time()
    list_results = [item in items for item in lookup_items]
    list_time = time.time() - start
    
    # Set lookup
    item_set = set(items)
    start = time.time()
    set_results = [item in item_set for item in lookup_items]
    set_time = time.time() - start
    
    print(f"List lookup time: {list_time:.6f} seconds")
    print(f"Set lookup time: {set_time:.6f} seconds")
    print(f"Speedup: {list_time / set_time:.2f}x")
    
    assert list_results == set_results

def demonstrate_list_optimizations():
    """Demonstrate various list optimizations."""
    
    print("=== List Performance Optimizations ===")
    
    print("\n1. Append vs Extend:")
    list_append_vs_extend()
    
    print("\n2. List Comprehension vs Loop:")
    list_comprehension_vs_loop()
    
    print("\n3. Set vs List Lookup:")
    set_vs_list_lookup()

if __name__ == "__main__":
    demonstrate_list_optimizations()
```

### String Operations Optimization
```python
import time

def string_concatenation_methods():
    """Compare different string concatenation methods."""
    
    # Test data
    strings = ["Hello", "World", "Python", "Optimization"] * 1000
    
    # Method 1: Using + operator
    start = time.time()
    result1 = ""
    for s in strings:
        result1 += s
    plus_time = time.time() - start
    
    # Method 2: Using join
    start = time.time()
    result2 = "".join(strings)
    join_time = time.time() - start
    
    # Method 3: Using StringIO
    from io import StringIO
    start = time.time()
    buffer = StringIO()
    for s in strings:
        buffer.write(s)
    result3 = buffer.getvalue()
    stringio_time = time.time() - start
    
    print(f"+ operator time: {plus_time:.6f} seconds")
    print(f"join() time: {join_time:.6f} seconds")
    print(f"StringIO time: {stringio_time:.6f} seconds")
    
    print(f"join() vs +: {plus_time / join_time:.2f}x faster")
    print(f"StringIO vs +: {plus_time / stringio_time:.2f}x faster")
    
    assert result1 == result2 == result3

def string_formatting_methods():
    """Compare different string formatting methods."""
    
    name = "John"
    age = 25
    iterations = 100000
    
    # Method 1: % formatting
    start = time.time()
    for _ in range(iterations):
        result = "Name: %s, Age: %d" % (name, age)
    percent_time = time.time() - start
    
    # Method 2: str.format()
    start = time.time()
    for _ in range(iterations):
        result = "Name: {}, Age: {}".format(name, age)
    format_time = time.time() - start
    
    # Method 3: f-strings (Python 3.6+)
    start = time.time()
    for _ in range(iterations):
        result = f"Name: {name}, Age: {age}"
    fstring_time = time.time() - start
    
    print(f"%% formatting time: {percent_time:.6f} seconds")
    print(f"str.format() time: {format_time:.6f} seconds")
    print(f"f-string time: {fstring_time:.6f} seconds")
    
    if fstring_time > 0:
        print(f"f-strings vs %%: {percent_time / fstring_time:.2f}x faster")
        print(f"f-strings vs format: {format_time / fstring_time:.2f}x faster")

def demonstrate_string_optimizations():
    """Demonstrate string performance optimizations."""
    
    print("=== String Performance Optimizations ===")
    
    print("\n1. String Concatenation Methods:")
    string_concatenation_methods()
    
    print("\n2. String Formatting Methods:")
    string_formatting_methods()

if __name__ == "__main__":
    demonstrate_string_optimizations()
```

## Memory Optimization

### Generator vs List
```python
import time
import sys

def memory_usage_comparison():
    """Compare memory usage of generators vs lists."""
    
    # Create large list
    print("Creating large list...")
    large_list = [i for i in range(1000000)]
    list_memory = sys.getsizeof(large_list)
    print(f"List memory usage: {list_memory / 1024 / 1024:.2f} MB")
    
    # Create generator
    print("Creating generator...")
    large_generator = (i for i in range(1000000))
    generator_memory = sys.getsizeof(large_generator)
    print(f"Generator memory usage: {generator_memory} bytes")
    
    memory_ratio = list_memory / generator_memory
    print(f"Memory ratio: {memory_ratio:.2f}x")
    
    # Time comparison for iteration
    start = time.time()
    list_sum = sum(large_list)
    list_time = time.time() - start
    
    start = time.time()
    generator_sum = sum(large_generator)
    generator_time = time.time() - start
    
    print(f"\nList iteration time: {list_time:.6f} seconds")
    print(f"Generator iteration time: {generator_time:.6f} seconds")
    
    assert list_sum == generator_sum

def lazy_evaluation_example():
    """Demonstrate lazy evaluation benefits."""
    
    def process_large_dataset():
        """Process large dataset lazily."""
        # Generator that yields processed items
        def data_generator():
            for i in range(1000000):
                # Simulate expensive processing
                yield i * i
        
        # Process items one at a time
        count = 0
        for item in data_generator():
            if item % 1000000 == 0:  # Only process specific items
                count += 1
        
        return count
    
    start = time.time()
    result = process_large_dataset()
    end = time.time()
    
    print(f"Lazy evaluation result: {result}")
    print(f"Time taken: {end - start:.4f} seconds")

def demonstrate_memory_optimizations():
    """Demonstrate memory optimization techniques."""
    
    print("=== Memory Optimization Techniques ===")
    
    print("\n1. Generator vs List Memory Usage:")
    memory_usage_comparison()
    
    print("\n2. Lazy Evaluation:")
    lazy_evaluation_example()

if __name__ == "__main__":
    demonstrate_memory_optimizations()
```

### Object Pooling
```python
import time

class ExpensiveObject:
    """Class that represents an expensive-to-create object."""
    
    def __init__(self, id):
        self.id = id
        # Simulate expensive initialization
        time.sleep(0.01)
        self.data = list(range(1000))  # Use some memory
    
    def reset(self, new_id):
        """Reset object for reuse."""
        self.id = new_id
        self.data = list(range(1000))

class ObjectPool:
    """Simple object pool for reusing expensive objects."""
    
    def __init__(self, max_size=10):
        self.max_size = max_size
        self.pool = []
        self.created_count = 0
    
    def get_object(self):
        """Get an object from the pool or create a new one."""
        if self.pool:
            return self.pool.pop()
        elif self.created_count < self.max_size:
            self.created_count += 1
            return ExpensiveObject(self.created_count)
        else:
            raise Exception("Pool exhausted")
    
    def return_object(self, obj):
        """Return an object to the pool."""
        if len(self.pool) < self.max_size:
            obj.reset(self.created_count + 1)
            self.pool.append(obj)

def object_pooling_example():
    """Demonstrate object pooling benefits."""
    
    def without_pool():
        """Create objects without pooling."""
        start = time.time()
        objects = []
        
        for i in range(100):
            obj = ExpensiveObject(i)
            objects.append(obj)
        
        end = time.time()
        return end - start
    
    def with_pool():
        """Use object pooling."""
        pool = ObjectPool(max_size=20)
        start = time.time()
        objects = []
        
        for i in range(100):
            obj = pool.get_object()
            objects.append(obj)
        
        # Return objects to pool
        for obj in objects:
            pool.return_object(obj)
        
        end = time.time()
        return end - start
    
    print("Without pooling:")
    no_pool_time = without_pool()
    print(f"Time: {no_pool_time:.4f} seconds")
    
    print("\nWith pooling:")
    with_pool_time = with_pool()
    print(f"Time: {with_pool_time:.4f} seconds")
    
    print(f"Speedup: {no_pool_time / with_pool_time:.2f}x")

if __name__ == "__main__":
    object_pooling_example()
```

## I/O Optimization

### File I/O Optimization
```python
import time
import os

def file_reading_methods():
    """Compare different file reading methods."""
    
    # Create test file
    test_file = "test_file.txt"
    content = "Line {}\n" * 100000
    
    with open(test_file, 'w') as f:
        f.write(content)
    
    # Method 1: read() all at once
    start = time.time()
    with open(test_file, 'r') as f:
        data1 = f.read()
    read_time = time.time() - start
    
    # Method 2: readlines()
    start = time.time()
    with open(test_file, 'r') as f:
        data2 = f.readlines()
    readlines_time = time.time() - start
    
    # Method 3: Line by line
    start = time.time()
    data3 = []
    with open(test_file, 'r') as f:
        for line in f:
            data3.append(line)
    line_by_line_time = time.time() - start
    
    print(f"read() time: {read_time:.6f} seconds")
    print(f"readlines() time: {readlines_time:.6f} seconds")
    print(f"line by line time: {line_by_line_time:.6f} seconds")
    
    # Clean up
    os.remove(test_file)

def file_writing_methods():
    """Compare different file writing methods."""
    
    test_file = "test_write.txt"
    data = ["Line {}\n".format(i) for i in range(100000)]
    
    # Method 1: Write all at once
    start = time.time()
    with open(test_file, 'w') as f:
        f.write(''.join(data))
    write_all_time = time.time() - start
    
    # Method 2: Write line by line
    start = time.time()
    with open(test_file, 'w') as f:
        for line in data:
            f.write(line)
    write_line_time = time.time() - start
    
    print(f"Write all at once time: {write_all_time:.6f} seconds")
    print(f"Write line by line time: {write_line_time:.6f} seconds")
    print(f"Speedup: {write_line_time / write_all_time:.2f}x")
    
    # Clean up
    os.remove(test_file)

def demonstrate_io_optimizations():
    """Demonstrate I/O optimization techniques."""
    
    print("=== I/O Optimization ===")
    
    print("\n1. File Reading Methods:")
    file_reading_methods()
    
    print("\n2. File Writing Methods:")
    file_writing_methods()

if __name__ == "__main__":
    demonstrate_io_optimizations()
```

### Buffering Optimization
```python
import time

def buffering_example():
    """Demonstrate buffering effects."""
    
    test_file = "buffer_test.txt"
    data = "Line {}\n".format(i) for i in range(10000)
    
    # Unbuffered writing
    start = time.time()
    with open(test_file, 'w', buffering=0) as f:  # No buffering
        for line in data:
            f.write(line)
    unbuffered_time = time.time() - start
    
    # Buffered writing
    start = time.time()
    with open(test_file, 'w', buffering=8192) as f:  # 8KB buffer
        for line in data:
            f.write(line)
    buffered_time = time.time() - start
    
    print(f"Unbuffered time: {unbuffered_time:.6f} seconds")
    print(f"Buffered time: {buffered_time:.6f} seconds")
    print(f"Speedup: {unbuffered_time / buffered_time:.2f}x")
    
    # Clean up
    import os
    os.remove(test_file)

if __name__ == "__main__":
    buffering_example()
```

## Concurrency Optimization

### Threading vs Multiprocessing
```python
import threading
import multiprocessing
import time
import math

def cpu_bound_task(n):
    """CPU-bound task."""
    return sum(math.sqrt(i) for i in range(n))

def io_bound_task(duration):
    """I/O-bound task."""
    time.sleep(duration)
    return f"Task completed in {duration} seconds"

def threading_vs_multiprocessing():
    """Compare threading vs multiprocessing performance."""
    
    tasks = [1000000, 2000000, 3000000, 4000000]
    
    # Sequential execution
    start = time.time()
    sequential_results = [cpu_bound_task(n) for n in tasks]
    sequential_time = time.time() - start
    
    # Threading (not effective for CPU-bound tasks)
    start = time.time()
    threads = []
    results = []
    
    def task_wrapper(n, results_list):
        result = cpu_bound_task(n)
        results_list.append(result)
    
    for n in tasks:
        thread = threading.Thread(target=task_wrapper, args=(n, results))
        threads.append(thread)
        thread.start()
    
    for thread in threads:
        thread.join()
    
    threading_time = time.time() - start
    
    # Multiprocessing (effective for CPU-bound tasks)
    start = time.time()
    with multiprocessing.Pool() as pool:
        multiprocessing_results = pool.map(cpu_bound_task, tasks)
    multiprocessing_time = time.time() - start
    
    print(f"Sequential time: {sequential_time:.4f} seconds")
    print(f"Threading time: {threading_time:.4f} seconds")
    print(f"Multiprocessing time: {multiprocessing_time:.4f} seconds")
    
    print(f"Multiprocessing vs Sequential: {sequential_time / multiprocessing_time:.2f}x faster")
    print(f"Multiprocessing vs Threading: {threading_time / multiprocessing_time:.2f}x faster")

if __name__ == "__main__":
    threading_vs_multiprocessing()
```

### Async I/O Optimization
```python
import asyncio
import time
import aiohttp
import requests

async def async_http_request(url):
    """Asynchronous HTTP request."""
    async with aiohttp.ClientSession() as session:
        async with session.get(url) as response:
            return await response.text()

def sync_http_request(url):
    """Synchronous HTTP request."""
    response = requests.get(url)
    return response.text

async def async_vs_sync_http():
    """Compare async vs sync HTTP requests."""
    
    urls = [
        "https://httpbin.org/delay/1",
        "https://httpbin.org/delay/1",
        "https://httpbin.org/delay/1",
        "https://httpbin.org/delay/1",
        "https://httpbin.org/delay/1"
    ]
    
    # Synchronous requests
    start = time.time()
    sync_results = [sync_http_request(url) for url in urls]
    sync_time = time.time() - start
    
    # Asynchronous requests
    start = time.time()
    async_results = await asyncio.gather(*[async_http_request(url) for url in urls])
    async_time = time.time() - start
    
    print(f"Synchronous HTTP time: {sync_time:.4f} seconds")
    print(f"Asynchronous HTTP time: {async_time:.4f} seconds")
    print(f"Speedup: {sync_time / async_time:.2f}x")

# Note: This requires aiohttp and requests
# pip install aiohttp requests
# asyncio.run(async_vs_sync_http())  # Uncomment to run

if __name__ == "__main__":
    print("HTTP comparison requires aiohttp and requests packages")
    print("Install with: pip install aiohttp requests")
```

## Data Structure Optimization

### Dictionary Optimization
```python
import time
import random

def dict_vs_list_lookup():
    """Compare dictionary vs list lookup performance."""
    
    # Create test data
    items = list(range(100000))
    lookup_items = random.sample(items, 1000)
    
    # List lookup
    start = time.time()
    list_results = []
    for item in lookup_items:
        list_results.append(item in items)
    list_time = time.time() - start
    
    # Dictionary lookup
    item_dict = {item: True for item in items}
    start = time.time()
    dict_results = []
    for item in lookup_items:
        dict_results.append(item in item_dict)
    dict_time = time.time() - start
    
    print(f"List lookup time: {list_time:.6f} seconds")
    print(f"Dictionary lookup time: {dict_time:.6f} seconds")
    print(f"Speedup: {list_time / dict_time:.2f}x")

def dict_comprehension_vs_loop():
    """Compare dictionary comprehension vs loop."""
    
    data = list(range(100000))
    
    # Dictionary comprehension
    start = time.time()
    result1 = {x: x * x for x in data}
    comp_time = time.time() - start
    
    # For loop
    start = time.time()
    result2 = {}
    for x in data:
        result2[x] = x * x
    loop_time = time.time() - start
    
    print(f"Dictionary comprehension time: {comp_time:.6f} seconds")
    print(f"For loop time: {loop_time:.6f} seconds")
    print(f"Speedup: {loop_time / comp_time:.2f}x")
    
    assert result1 == result2

def demonstrate_dict_optimizations():
    """Demonstrate dictionary optimizations."""
    
    print("=== Dictionary Optimization ===")
    
    print("\n1. Dictionary vs List Lookup:")
    dict_vs_list_lookup()
    
    print("\n2. Dictionary Comprehension vs Loop:")
    dict_comprehension_vs_loop()

if __name__ == "__main__":
    demonstrate_dict_optimizations()
```

## Caching and Memoization

### Memoization Examples
```python
import time
from functools import lru_cache

def fibonacci_recursive(n):
    """Recursive Fibonacci without memoization."""
    if n <= 1:
        return n
    return fibonacci_recursive(n-1) + fibonacci_recursive(n-2)

@lru_cache(maxsize=None)
def fibonacci_memoized(n):
    """Recursive Fibonacci with memoization."""
    if n <= 1:
        return n
    return fibonacci_memoized(n-1) + fibonacci_memoized(n-2)

def memoization_example():
    """Demonstrate memoization benefits."""
    
    n = 35
    
    # Without memoization
    start = time.time()
    result1 = fibonacci_recursive(n)
    no_cache_time = time.time() - start
    
    # With memoization
    start = time.time()
    result2 = fibonacci_memoized(n)
    with_cache_time = time.time() - start
    
    print(f"Without memoization: {no_cache_time:.4f} seconds")
    print(f"With memoization: {with_cache_time:.6f} seconds")
    print(f"Speedup: {no_cache_time / with_cache_time:.2f}x")
    
    assert result1 == result2
    print(f"Fibonacci({n}) = {result1}")

# Custom memoization decorator
def memoize(func):
    """Custom memoization decorator."""
    cache = {}
    
    def wrapper(*args):
        if args in cache:
            return cache[args]
        result = func(*args)
        cache[args] = result
        return result
    
    return wrapper

@memoize
def expensive_function(x, y):
    """Expensive function for testing."""
    time.sleep(0.1)  # Simulate expensive computation
    return x * y

def custom_memoization_example():
    """Demonstrate custom memoization."""
    
    start = time.time()
    result1 = expensive_function(10, 20)
    result2 = expensive_function(10, 20)  # Should use cache
    result3 = expensive_function(30, 40)
    result4 = expensive_function(30, 40)  # Should use cache
    total_time = time.time() - start
    
    print(f"Custom memoization results: {result1}, {result3}")
    print(f"Total time: {total_time:.4f} seconds")
    print("Should be ~0.4 seconds (2 actual computations)")

if __name__ == "__main__":
    print("=== Memoization ===")
    
    print("\n1. LRU Cache Example:")
    memoization_example()
    
    print("\n2. Custom Memoization:")
    custom_memoization_example()
```

## Best Practices

### Performance Optimization Checklist
```python
import time
import sys

def performance_checklist():
    """Comprehensive performance optimization checklist."""
    
    print("=== Python Performance Optimization Checklist ===")
    
    print("\n1. Algorithm Selection:")
    print("   ✓ Use appropriate data structures (dict for lookups)")
    print("   ✓ Choose O(1) over O(n) when possible")
    print("   ✓ Use built-in functions (often optimized in C)")
    
    print("\n2. Data Structure Optimization:")
    print("   ✓ Use sets for membership testing")
    print("   ✓ Use generators for large datasets")
    print("   ✓ Use deque for append/pop from both ends")
    
    print("\n3. Memory Management:")
    print("   ✓ Use generators instead of lists when possible")
    print("   ✓ Delete large objects when done")
    print("   ✓ Use object pooling for expensive objects")
    
    print("\n4. I/O Optimization:")
    print("   ✓ Use buffered I/O")
    print("   ✓ Process files in chunks")
    print("   ✓ Use async I/O for network operations")
    
    print("\n5. Concurrency:")
    print("   ✓ Use multiprocessing for CPU-bound tasks")
    print("   ✓ Use threading for I/O-bound tasks")
    print("   ✓ Use async/await for concurrent I/O")
    
    print("\n6. Caching:")
    print("   ✓ Use @lru_cache for function memoization")
    print("   ✓ Cache expensive computations")
    print("   ✓ Use built-in caching mechanisms")
    
    print("\n7. Code Optimization:")
    print("   ✓ Use list comprehensions instead of loops")
    print("   ✓ Use string join() instead of +")
    print("   ✓ Use f-strings for formatting")
    
    print("\n8. Profiling:")
    print("   ✓ Profile before optimizing")
    print("   ✓ Focus on bottlenecks")
    print("   ✓ Measure actual improvements")

def optimization_examples():
    """Show specific optimization examples."""
    
    print("\n=== Optimization Examples ===")
    
    # Example 1: Use built-in functions
    print("\n1. Use built-in functions:")
    data = list(range(1000000))
    
    start = time.time()
    result1 = sum(data)
    manual_time = time.time() - start
    
    start = time.time()
    result2 = sum(x for x in data)
    generator_time = time.time() - start
    
    print(f"   Manual sum: {manual_time:.6f}s")
    print(f"   Generator sum: {generator_time:.6f}s")
    
    # Example 2: Local variable optimization
    print("\n2. Local variable optimization:")
    
    def slow_function():
        total = 0
        for i in range(100000):
            total += len(str(i))
        return total
    
    def fast_function():
        total = 0
        str_len = len  # Local variable lookup
        for i in range(100000):
            total += str_len(str(i))
        return total
    
    start = time.time()
    result1 = slow_function()
    slow_time = time.time() - start
    
    start = time.time()
    result2 = fast_function()
    fast_time = time.time() - start
    
    print(f"   Slow version: {slow_time:.6f}s")
    print(f"   Fast version: {fast_time:.6f}s")
    print(f"   Speedup: {slow_time / fast_time:.2f}x")
    
    assert result1 == result2

if __name__ == "__main__":
    performance_checklist()
    optimization_examples()
```

## Summary

Python performance optimization involves multiple areas:

**Measurement Tools:**
- `cProfile` for function profiling
- `tracemalloc` for memory profiling
- `line_profiler` for line-by-line analysis
- `timeit` for micro-benchmarks

**Algorithm Optimization:**
- Choose appropriate data structures
- Use O(1) operations over O(n)
- Leverage built-in optimized functions
- Implement caching and memoization

**Memory Optimization:**
- Use generators for lazy evaluation
- Implement object pooling
- Optimize string operations
- Use appropriate buffer sizes

**I/O Optimization:**
- Use buffered operations
- Process data in chunks
- Implement async I/O patterns
- Optimize file operations

**Concurrency Optimization:**
- Use multiprocessing for CPU-bound tasks
- Use threading for I/O-bound tasks
- Implement async/await patterns
- Choose appropriate concurrency model

**Best Practices:**
- Profile before optimizing
- Focus on actual bottlenecks
- Measure improvements
- Consider readability vs performance
- Use built-in optimizations

Performance optimization should be applied judiciously, focusing on actual bottlenecks while maintaining code readability and maintainability.
