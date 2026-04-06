# Python Context Managers

## Context Manager Basics

### What is a Context Manager?
```python
# A context manager is an object that defines the runtime context
# for a block of code, handling setup and teardown automatically

# Basic context manager usage
with open('example.txt', 'w') as file:
    file.write('Hello, World!')
    # File is automatically closed when exiting the with block

# Equivalent without context manager (manual resource management)
file = open('example.txt', 'w')
try:
    file.write('Hello, World!')
finally:
    file.close()  # Must remember to close manually
```

### The with Statement
```python
# Multiple context managers
with open('input.txt', 'r') as input_file, open('output.txt', 'w') as output_file:
    content = input_file.read()
    output_file.write(content.upper())
    # Both files are automatically closed

# Nested context managers
with open('first.txt', 'w') as f1:
    f1.write('First file')
    with open('second.txt', 'w') as f2:
        f2.write('Second file')
        # f2 is closed here
    # f1 is closed here
```

## Creating Context Managers

### Class-based Context Managers
```python
class Timer:
    """Context manager for timing code execution."""
    
    def __init__(self, description="Operation"):
        self.description = description
        self.start_time = None
        self.end_time = None
    
    def __enter__(self):
        import time
        self.start_time = time.time()
        print(f"Starting: {self.description}")
        return self  # This is what 'as' receives
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        import time
        self.end_time = time.time()
        duration = self.end_time - self.start_time
        print(f"Finished: {self.description} in {duration:.4f} seconds")
        
        # Return False to re-raise exceptions, True to suppress them
        return False

# Using the Timer context manager
with Timer("File processing"):
    with open('example.txt', 'w') as file:
        file.write('Processing some data...')
        time.sleep(0.1)  # Simulate work

# Context manager that handles exceptions
class DatabaseConnection:
    """Context manager for database connections."""
    
    def __init__(self, db_name):
        self.db_name = db_name
        self.connection = None
    
    def __enter__(self):
        # Simulate database connection
        self.connection = f"Connected to {self.db_name}"
        print(f"Database connection established: {self.connection}")
        return self.connection
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if exc_type is not None:
            print(f"Error occurred: {exc_val}")
            # Rollback transaction
            print("Transaction rolled back")
        else:
            # Commit transaction
            print("Transaction committed")
        
        # Close connection
        self.connection = None
        print("Database connection closed")
        return False  # Re-raise exceptions

# Using DatabaseConnection
try:
    with DatabaseConnection("mydb") as conn:
        print(f"Using connection: {conn}")
        # Simulate an error
        raise ValueError("Database error occurred")
except ValueError as e:
    print(f"Caught error: {e}")
```

### Function-based Context Managers
```python
from contextlib import contextmanager

@contextmanager
def simple_timer(description="Operation"):
    """Simple timer context manager using decorator."""
    import time
    start_time = time.time()
    print(f"Starting: {description}")
    try:
        yield  # This is where the with block executes
    finally:
        end_time = time.time()
        duration = end_time - start_time
        print(f"Finished: {description} in {duration:.4f} seconds")

@contextmanager
def file_manager(filename, mode):
    """File manager context manager."""
    file = open(filename, mode)
    try:
        yield file
    finally:
        file.close()

@contextmanager
def temporary_directory():
    """Create and cleanup a temporary directory."""
    import tempfile
    import os
    temp_dir = tempfile.mkdtemp()
    print(f"Created temporary directory: {temp_dir}")
    try:
        yield temp_dir
    finally:
        import shutil
        shutil.rmtree(temp_dir)
        print(f"Cleaned up temporary directory: {temp_dir}")

# Using function-based context managers
with simple_timer("Writing file"):
    with file_manager('test.txt', 'w') as f:
        f.write('Hello from context manager!')

with temporary_directory() as temp_dir:
    print(f"Working in: {temp_dir}")
    # Do work in temporary directory
    with open(f"{temp_dir}/temp_file.txt", 'w') as f:
        f.write("Temporary content")
```

## Advanced Context Manager Patterns

### Context Manager with Parameters
```python
class ResourcePool:
    """Context manager for managing a pool of resources."""
    
    def __init__(self, max_resources=3):
        self.max_resources = max_resources
        self.available_resources = list(range(max_resources))
        self.allocated_resources = []
    
    def __enter__(self):
        if not self.available_resources:
            raise RuntimeError("No resources available")
        
        resource = self.available_resources.pop(0)
        self.allocated_resources.append(resource)
        print(f"Allocated resource: {resource}")
        return resource
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if self.allocated_resources:
            resource = self.allocated_resources.pop()
            self.available_resources.append(resource)
            print(f"Released resource: {resource}")
        return False

# Using resource pool
pool = ResourcePool(max_resources=2)

with pool as resource1:
    print(f"Using resource: {resource1}")
    
    with pool as resource2:
        print(f"Using resource: {resource2}")
        
        # This would fail - no more resources
        try:
            with pool as resource3:
                print(f"Using resource: {resource3}")
        except RuntimeError as e:
            print(f"Expected error: {e}")
```

### Stacked Context Managers
```python
from contextlib import ExitStack

class ConfigManager:
    """Manages configuration changes."""
    
    def __init__(self):
        self.original_config = {}
        self.current_config = {}
    
    def set_config(self, key, value):
        self.original_config[key] = self.current_config.get(key)
        self.current_config[key] = value
        print(f"Set config: {key} = {value}")
    
    def __enter__(self):
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        # Restore original configuration
        for key, value in self.original_config.items():
            if value is None:
                self.current_config.pop(key, None)
            else:
                self.current_config[key] = value
            print(f"Restored config: {key} = {value}")
        return False

class Logger:
    """Simple logger context manager."""
    
    def __init__(self, name):
        self.name = name
    
    def __enter__(self):
        print(f"Logger {self.name} started")
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if exc_type:
            print(f"Logger {self.name} detected error: {exc_val}")
        else:
            print(f"Logger {self.name} finished successfully")
        return False

# Using ExitStack for multiple context managers
with ExitStack() as stack:
    # Enter multiple context managers
    config = stack.enter_context(ConfigManager())
    logger = stack.enter_context(Logger("main"))
    
    # Use the managers
    config.set_config('debug', True)
    config.set_config('verbose', False)
    
    print("Doing work with managed resources...")
    
    # All managers are automatically exited when leaving the with block
```

### Async Context Managers (Python 3.7+)
```python
import asyncio

class AsyncTimer:
    """Async context manager for timing."""
    
    def __init__(self, description="Async operation"):
        self.description = description
        self.start_time = None
    
    async def __aenter__(self):
        import time
        self.start_time = time.time()
        print(f"Starting async: {self.description}")
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        import time
        end_time = time.time()
        duration = end_time - self.start_time
        print(f"Finished async: {self.description} in {duration:.4f} seconds")
        return False

class AsyncFileHandler:
    """Async file handler context manager."""
    
    def __init__(self, filename, mode):
        self.filename = filename
        self.mode = mode
        self.file = None
    
    async def __aenter__(self):
        import aiofiles
        self.file = await aiofiles.open(self.filename, self.mode)
        print(f"Opened async file: {self.filename}")
        return self.file
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        if self.file:
            await self.file.close()
            print(f"Closed async file: {self.filename}")
        return False

async def async_example():
    """Example using async context managers."""
    
    async with AsyncTimer("Async file operations"):
        async with AsyncFileHandler('async_test.txt', 'w') as file:
            await file.write('Hello from async context manager!')
            await asyncio.sleep(0.1)  # Simulate async work
    
    print("Async operations completed")

# Run async example
# asyncio.run(async_example())  # Uncomment to run
```

## Practical Applications

### Database Transaction Management
```python
class DatabaseTransaction:
    """Context manager for database transactions."""
    
    def __init__(self, connection):
        self.connection = connection
        self.transaction_active = False
    
    def __enter__(self):
        print("BEGIN TRANSACTION")
        self.transaction_active = True
        return self.connection
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if exc_type is not None:
            print("ROLLBACK - Error occurred")
        else:
            print("COMMIT - Transaction successful")
        
        self.transaction_active = False
        return False  # Don't suppress exceptions

class MockDatabase:
    """Mock database for demonstration."""
    
    def __init__(self, name):
        self.name = name
        self.data = {}
    
    def execute(self, query):
        print(f"Executing: {query}")
        if "ERROR" in query:
            raise ValueError("Database error")
        return "Query executed"

# Using database transaction manager
db = MockDatabase("test_db")

try:
    with DatabaseTransaction(db):
        db.execute("INSERT INTO users VALUES (1, 'Alice')")
        db.execute("INSERT INTO users VALUES (2, 'Bob')")
        # db.execute("ERROR QUERY")  # Uncomment to test rollback
except ValueError as e:
    print(f"Caught error: {e}")

print("Database operations completed")
```

### HTTP Request Management
```python
import requests
from contextlib import contextmanager

@contextmanager
def http_session():
    """Context manager for HTTP sessions."""
    session = requests.Session()
    try:
        yield session
    finally:
        session.close()

@contextmanager
def api_request(method, url, timeout=30):
    """Context manager for API requests with error handling."""
    try:
        response = requests.request(method, url, timeout=timeout)
        response.raise_for_status()
        yield response
    except requests.exceptions.RequestException as e:
        print(f"Request failed: {e}")
        raise
    finally:
        print("Request completed")

# Using HTTP context managers
with http_session() as session:
    with api_request('GET', 'https://httpbin.org/get') as response:
        data = response.json()
        print(f"API response: {data.get('url', 'No URL')}")
```

### Lock Management
```python
import threading
from contextlib import contextmanager

class ThreadSafeCounter:
    """Thread-safe counter with lock management."""
    
    def __init__(self):
        self.value = 0
        self._lock = threading.Lock()
    
    @contextmanager
    def lock(self):
        """Context manager for acquiring lock."""
        self._lock.acquire()
        try:
            yield
        finally:
            self._lock.release()
    
    def increment(self):
        """Increment counter safely."""
        with self.lock():
            self.value += 1
            return self.value
    
    def get_value(self):
        """Get current value."""
        with self.lock():
            return self.value

# Demonstrate thread-safe operations
counter = ThreadSafeCounter()

def worker(worker_id):
    """Worker function that increments counter."""
    for _ in range(1000):
        value = counter.increment()
    print(f"Worker {worker_id} finished, final value: {value}")

# Create multiple threads
threads = []
for i in range(5):
    thread = threading.Thread(target=worker, args=(i,))
    threads.append(thread)
    thread.start()

# Wait for all threads to complete
for thread in threads:
    thread.join()

print(f"Final counter value: {counter.get_value()}")
```

### Configuration Management
```python
import os
from contextlib import contextmanager

class EnvironmentManager:
    """Context manager for temporary environment variable changes."""
    
    def __init__(self, **env_vars):
        self.env_vars = env_vars
        self.original_values = {}
    
    def __enter__(self):
        # Store original values
        for key, value in self.env_vars.items():
            self.original_values[key] = os.environ.get(key)
            os.environ[key] = str(value)
            print(f"Set {key} = {value}")
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        # Restore original values
        for key, original_value in self.original_values.items():
            if original_value is None:
                os.environ.pop(key, None)
                print(f"Removed {key}")
            else:
                os.environ[key] = original_value
                print(f"Restored {key} = {original_value}")
        return False

@contextmanager
def working_directory(path):
    """Context manager for temporary working directory changes."""
    original_cwd = os.getcwd()
    try:
        os.chdir(path)
        print(f"Changed directory to: {path}")
        yield os.getcwd()
    finally:
        os.chdir(original_cwd)
        print(f"Restored directory to: {original_cwd}")

# Using environment and directory managers
with EnvironmentManager(DEBUG='true', LOG_LEVEL='INFO'):
    print(f"DEBUG: {os.environ.get('DEBUG')}")
    print(f"LOG_LEVEL: {os.environ.get('LOG_LEVEL')}")
    
    # Environment is restored when exiting the block

# Using working directory manager
import tempfile
with tempfile.TemporaryDirectory() as temp_dir:
    with working_directory(temp_dir):
        print(f"Current directory: {os.getcwd()}")
        # Do work in temporary directory
        with open('test.txt', 'w') as f:
            f.write('Working in temp directory')
    # Directory is restored automatically
```

## Error Handling and Cleanup

### Robust Error Handling
```python
class RobustFileManager:
    """File manager with comprehensive error handling."""
    
    def __init__(self, filename, mode='r'):
        self.filename = filename
        self.mode = mode
        self.file = None
    
    def __enter__(self):
        try:
            self.file = open(self.filename, self.mode)
            print(f"File opened: {self.filename}")
            return self.file
        except IOError as e:
            print(f"Failed to open file {self.filename}: {e}")
            raise
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if self.file:
            try:
                self.file.close()
                print(f"File closed: {self.filename}")
            except IOError as e:
                print(f"Error closing file {self.filename}: {e}")
        
        # Handle different exception types
        if exc_type is IOError:
            print(f"I/O error occurred: {exc_val}")
            return True  # Suppress I/O errors
        elif exc_type is KeyboardInterrupt:
            print("Operation interrupted by user")
            return False  # Re-raise KeyboardInterrupt
        elif exc_type is not None:
            print(f"Unexpected error: {exc_val}")
            return False  # Re-raise other exceptions
        
        return False

# Test robust error handling
try:
    with RobustFileManager('nonexistent.txt', 'r') as f:
        content = f.read()
        print(content)
except IOError:
    print("Handled file not found error")

try:
    with RobustFileManager('test.txt', 'w') as f:
        f.write('Hello, World!')
        raise KeyboardInterrupt("User interrupted")
except KeyboardInterrupt:
    print("Handled keyboard interrupt")
```

### Resource Cleanup Patterns
```python
class ResourceManager:
    """Generic resource manager with cleanup registration."""
    
    def __init__(self):
        self.resources = []
        self.cleanup_executed = False
    
    def add_resource(self, resource, cleanup_func):
        """Add a resource with its cleanup function."""
        self.resources.append((resource, cleanup_func))
    
    def __enter__(self):
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if not self.cleanup_executed:
            self.cleanup()
        return False
    
    def cleanup(self):
        """Execute all cleanup functions."""
        print("Executing cleanup procedures...")
        self.cleanup_executed = True
        
        # Clean up in reverse order (LIFO)
        for resource, cleanup_func in reversed(self.resources):
            try:
                cleanup_func(resource)
                print(f"Cleaned up: {resource}")
            except Exception as e:
                print(f"Error cleaning up {resource}: {e}")

# Using resource manager
def custom_cleanup(resource):
    """Custom cleanup function."""
    print(f"Custom cleanup for: {resource}")

with ResourceManager() as manager:
    # Add resources
    manager.add_resource("file1", lambda r: print(f"Closing {r}"))
    manager.add_resource("file2", custom_cleanup)
    manager.add_resource("connection", lambda r: print(f"Closing {r}"))
    
    print("Working with resources...")
    # Resources are automatically cleaned up
```

## Performance Considerations

### Context Manager Overhead
```python
import time
import contextlib

def manual_resource_management():
    """Manual resource management."""
    file = open('test.txt', 'w')
    try:
        file.write('Manual management')
    finally:
        file.close()

def context_manager_management():
    """Context manager resource management."""
    with open('test.txt', 'w') as file:
        file.write('Context manager')

@contextlib.contextmanager
def custom_context_manager():
    """Custom context manager."""
    file = open('test.txt', 'w')
    try:
        yield file
    finally:
        file.close()

def custom_context_management():
    """Custom context manager usage."""
    with custom_context_manager() as file:
        file.write('Custom context manager')

# Compare performance
iterations = 10000

manual_time = timeit.timeit(manual_resource_management, number=iterations)
context_time = timeit.timeit(context_manager_management, number=iterations)
custom_time = timeit.timeit(custom_context_management, number=iterations)

print(f"Manual management: {manual_time:.4f} seconds")
print(f"Context manager: {context_time:.4f} seconds")
print(f"Custom context: {custom_time:.4f} seconds")
print(f"Overhead: {((context_time - manual_time) / manual_time * 100):.2f}%")
```

## Best Practices

### Context Manager Best Practices
```python
# 1. Always implement both __enter__ and __exit__
class CompleteManager:
    def __enter__(self):
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        return False

# 2. Handle exceptions appropriately
class ExceptionHandlingManager:
    def __enter__(self):
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        if exc_type is ValueError:
            print(f"Handled ValueError: {exc_val}")
            return True  # Suppress
        return False  # Re-raise others

# 3. Use contextlib.contextmanager for simple cases
from contextlib import contextmanager

@contextmanager
def simple_manager():
    """Simple context manager using decorator."""
    resource = acquire_resource()
    try:
        yield resource
    finally:
        release_resource(resource)

def acquire_resource():
    """Simulate resource acquisition."""
    print("Resource acquired")
    return "resource"

def release_resource(resource):
    """Simulate resource release."""
    print(f"Resource released: {resource}")

# 4. Document context manager behavior
class DocumentedManager:
    """
    Context manager for demonstration.
    
    Returns:
        str: A greeting message
    
    Raises:
        ValueError: If something goes wrong
    """
    
    def __enter__(self):
        return "Hello from context manager!"
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        return False

# 5. Use type hints for clarity
from typing import Generic, TypeVar

T = TypeVar('T')

class GenericManager(Generic[T]):
    """Generic context manager with type hints."""
    
    def __init__(self, resource: T):
        self.resource = resource
    
    def __enter__(self) -> T:
        return self.resource
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        return False

# 6. Consider using ExitStack for multiple resources
from contextlib import ExitStack

def multiple_resources():
    """Manage multiple resources with ExitStack."""
    with ExitStack() as stack:
        file1 = stack.enter_context(open('file1.txt', 'w'))
        file2 = stack.enter_context(open('file2.txt', 'w'))
        # Both files are automatically closed

# 7. Implement proper cleanup
class CleanupManager:
    """Manager with comprehensive cleanup."""
    
    def __init__(self):
        self.resources = []
    
    def __enter__(self):
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        # Always cleanup, even if exceptions occur
        for resource in reversed(self.resources):
            try:
                resource.close()
            except Exception as e:
                print(f"Cleanup error: {e}")
        return False

# 8. Use context managers for thread safety
import threading

class ThreadSafeManager:
    """Thread-safe context manager."""
    
    def __init__(self):
        self._lock = threading.Lock()
    
    def __enter__(self):
        self._lock.acquire()
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        self._lock.release()
        return False

# 9. Consider async context managers for async code
class AsyncManager:
    """Async context manager example."""
    
    async def __aenter__(self):
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        return False

# 10. Test context managers thoroughly
def test_context_manager():
    """Test context manager behavior."""
    manager = DocumentedManager()
    
    with manager as greeting:
        assert greeting == "Hello from context manager!"
    
    # Test exception handling
    try:
        with manager:
            raise ValueError("Test error")
    except ValueError:
        pass  # Expected
```

## Summary

Python context managers provide powerful resource management:

**Core Benefits:**
- Automatic resource cleanup
- Exception safety
- Clean, readable syntax
- Guaranteed cleanup even with errors

**Implementation Methods:**
- Class-based with `__enter__` and `__exit__`
- Function-based with `@contextmanager` decorator
- Async versions with `__aenter__` and `__aexit__`

**Common Use Cases:**
- File handling and I/O operations
- Database transactions
- Network connections
- Lock and thread management
- Configuration and environment changes
- Temporary resource management

**Advanced Features:**
- Multiple resource management with `ExitStack`
- Exception handling and suppression
- Custom cleanup procedures
- Async/await support
- Generic typing support

**Best Practices:**
- Always implement both enter and exit methods
- Handle exceptions appropriately
- Use `@contextmanager` for simple cases
- Document behavior and return types
- Test exception scenarios
- Consider performance implications
- Use type hints for clarity
- Implement comprehensive cleanup

Context managers are essential for writing robust, maintainable Python code that handles resources safely and efficiently.
