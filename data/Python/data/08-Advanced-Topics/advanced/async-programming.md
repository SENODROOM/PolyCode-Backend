# Python Async Programming

## Asynchronous Programming Basics

### Understanding Async/Await
```python
import asyncio
import time

# Synchronous function
def sync_function():
    print("Starting sync function")
    time.sleep(2)  # Blocking operation
    print("Finished sync function")
    return "Sync result"

# Asynchronous function
async def async_function():
    print("Starting async function")
    await asyncio.sleep(2)  # Non-blocking operation
    print("Finished async function")
    return "Async result"

# Running synchronous code
print("Synchronous execution:")
start = time.time()
result = sync_function()
end = time.time()
print(f"Result: {result}")
print(f"Time taken: {end - start:.2f} seconds")

# Running asynchronous code
print("\nAsynchronous execution:")
async def main():
    start = time.time()
    result = await async_function()
    end = time.time()
    print(f"Result: {result}")
    print(f"Time taken: {end - start:.2f} seconds")

# Run the async main function
# asyncio.run(main())  # Uncomment to run
```

### Basic Async Operations
```python
import asyncio

async def fetch_data(url):
    """Simulate fetching data from a URL."""
    print(f"Fetching data from {url}")
    await asyncio.sleep(1)  # Simulate network delay
    return f"Data from {url}"

async def process_data(data):
    """Simulate processing data."""
    print(f"Processing: {data}")
    await asyncio.sleep(0.5)  # Simulate processing time
    return f"Processed: {data}"

async def save_data(data):
    """Simulate saving data."""
    print(f"Saving: {data}")
    await asyncio.sleep(0.3)  # Simulate save time
    return f"Saved: {data}"

async def data_pipeline():
    """Sequential async operations."""
    # Fetch -> Process -> Save
    raw_data = await fetch_data("https://api.example.com/data")
    processed_data = await process_data(raw_data)
    result = await save_data(processed_data)
    return result

async def main():
    result = await data_pipeline()
    print(f"Pipeline result: {result}")

# asyncio.run(main())  # Uncomment to run
```

## Concurrent Execution

### Running Tasks Concurrently
```python
import asyncio

async def task(name, duration):
    """Simulate a task that takes some time."""
    print(f"Task {name} started")
    await asyncio.sleep(duration)
    print(f"Task {name} completed")
    return f"Task {name} result"

async def run_tasks_sequential():
    """Run tasks sequentially."""
    print("Sequential execution:")
    start = asyncio.get_event_loop().time()
    
    result1 = await task("A", 2)
    result2 = await task("B", 1)
    result3 = await task("C", 1.5)
    
    elapsed = asyncio.get_event_loop().time() - start
    print(f"Total time: {elapsed:.2f} seconds")
    return [result1, result2, result3]

async def run_tasks_concurrent():
    """Run tasks concurrently."""
    print("Concurrent execution:")
    start = asyncio.get_event_loop().time()
    
    # Create tasks
    task1 = asyncio.create_task(task("A", 2))
    task2 = asyncio.create_task(task("B", 1))
    task3 = asyncio.create_task(task("C", 1.5))
    
    # Wait for all tasks to complete
    results = await asyncio.gather(task1, task2, task3)
    
    elapsed = asyncio.get_event_loop().time() - start
    print(f"Total time: {elapsed:.2f} seconds")
    return results

async def main():
    sequential_results = await run_tasks_sequential()
    concurrent_results = await run_tasks_concurrent()
    
    print(f"Sequential: {sequential_results}")
    print(f"Concurrent: {concurrent_results}")

# asyncio.run(main())  # Uncomment to run
```

### asyncio.gather with Error Handling
```python
import asyncio

async def risky_task(name, should_fail=False):
    """Task that might fail."""
    print(f"Risky task {name} started")
    await asyncio.sleep(1)
    
    if should_fail:
        raise ValueError(f"Task {name} failed")
    
    print(f"Risky task {name} completed")
    return f"Task {name} success"

async def gather_with_exceptions():
    """Handle exceptions in asyncio.gather."""
    print("Gather with exception handling:")
    
    tasks = [
        risky_task("A", False),
        risky_task("B", True),  # This will fail
        risky_task("C", False),
    ]
    
    # return_exceptions=True captures exceptions instead of raising them
    results = await asyncio.gather(*tasks, return_exceptions=True)
    
    for i, result in enumerate(results):
        if isinstance(result, Exception):
            print(f"Task {chr(65+i)} failed with: {result}")
        else:
            print(f"Task {chr(65+i)} succeeded with: {result}")

async def main():
    await gather_with_exceptions()

# asyncio.run(main())  # Uncomment to run
```

## Async Context Managers

### Async Context Managers
```python
import asyncio
from contextlib import asynccontextmanager

@asynccontextmanager
async def database_connection():
    """Async context manager for database connection."""
    print("Connecting to database...")
    await asyncio.sleep(0.5)  # Simulate connection time
    
    connection = "DatabaseConnection"
    
    try:
        yield connection
    finally:
        print("Closing database connection...")
        await asyncio.sleep(0.2)  # Simulate cleanup time

@asynccontextmanager
async def file_handler(filename, mode):
    """Async context manager for file operations."""
    print(f"Opening file {filename}...")
    await asyncio.sleep(0.1)
    
    try:
        yield f"FileHandler({filename}, {mode})"
    finally:
        print(f"Closing file {filename}...")
        await asyncio.sleep(0.1)

async def use_async_context_managers():
    """Use multiple async context managers."""
    
    # Nested context managers
    async with database_connection() as db:
        async with file_handler("data.txt", "w") as file:
            print(f"Using {db} with {file}")
            await asyncio.sleep(1)
    
    print("All resources cleaned up")

async def main():
    await use_async_context_managers()

# asyncio.run(main())  # Uncomment to run
```

### Custom Async Context Manager Class
```python
import asyncio
from typing import Optional

class AsyncTimer:
    """Async context manager for timing operations."""
    
    def __init__(self, description="Operation"):
        self.description = description
        self.start_time: Optional[float] = None
        self.end_time: Optional[float] = None
    
    async def __aenter__(self):
        self.start_time = asyncio.get_event_loop().time()
        print(f"Starting: {self.description}")
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        self.end_time = asyncio.get_event_loop().time()
        duration = self.end_time - self.start_time
        print(f"Finished: {self.description} in {duration:.4f} seconds")
        
        if exc_type:
            print(f"Exception occurred: {exc_val}")
        
        return False  # Don't suppress exceptions

class AsyncResourcePool:
    """Async context manager for resource pooling."""
    
    def __init__(self, max_resources=3):
        self.max_resources = max_resources
        self.available_resources = list(range(max_resources))
        self.allocated_resources = []
    
    async def acquire_resource(self):
        """Acquire a resource from the pool."""
        while not self.available_resources:
            print("No resources available, waiting...")
            await asyncio.sleep(0.1)
        
        resource = self.available_resources.pop(0)
        self.allocated_resources.append(resource)
        print(f"Acquired resource: {resource}")
        return resource
    
    async def release_resource(self, resource):
        """Release a resource back to the pool."""
        if resource in self.allocated_resources:
            self.allocated_resources.remove(resource)
            self.available_resources.append(resource)
            print(f"Released resource: {resource}")
    
    async def __aenter__(self):
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        # Release all allocated resources
        for resource in self.allocated_resources.copy():
            await self.release_resource(resource)
        return False

async def use_custom_async_managers():
    """Use custom async context managers."""
    
    # Use AsyncTimer
    async with AsyncTimer("File processing"):
        await asyncio.sleep(1)
    
    # Use AsyncResourcePool
    async with AsyncResourcePool(max_resources=2) as pool:
        async def worker(worker_id):
            resource = await pool.acquire_resource()
            await asyncio.sleep(0.5)
            await pool.release_resource(resource)
            return f"Worker {worker_id} finished"
        
        # Run workers concurrently
        tasks = [worker(i) for i in range(3)]
        results = await asyncio.gather(*tasks)
        print(f"Worker results: {results}")

async def main():
    await use_custom_async_managers()

# asyncio.run(main())  # Uncomment to run
```

## Async Iterators and Generators

### Async Generators
```python
import asyncio

async def async_counter(max_count):
    """Async generator that yields numbers."""
    for i in range(max_count):
        print(f"Yielding {i}")
        await asyncio.sleep(0.5)  # Simulate async work
        yield i

async def async_range(start, end, step=1):
    """Async range generator."""
    current = start
    while current < end:
        print(f"Yielding {current}")
        await asyncio.sleep(0.3)
        yield current
        current += step

async def process_async_generator():
    """Process values from async generator."""
    print("Processing async counter:")
    async for number in async_counter(5):
        print(f"Received: {number}")
    
    print("\nProcessing async range:")
    async for number in async_range(10, 15, 2):
        print(f"Received: {number}")

async def main():
    await process_async_generator()

# asyncio.run(main())  # Uncomment to run
```

### Async Iterators
```python
import asyncio

class AsyncFileReader:
    """Async iterator for reading files."""
    
    def __init__(self, filename):
        self.filename = filename
        self.file = None
    
    async def __aenter__(self):
        # Simulate async file opening
        print(f"Opening {self.filename}")
        await asyncio.sleep(0.1)
        self.file = f"Line 1\nLine 2\nLine 3\nLine 4\nLine 5"
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        # Simulate async file closing
        print(f"Closing {self.filename}")
        await asyncio.sleep(0.1)
        self.file = None
    
    def __aiter__(self):
        return self
    
    async def __anext__(self):
        if not self.file:
            raise StopAsyncIteration
        
        lines = self.file.split('\n')
        if not lines:
            raise StopAsyncIteration
        
        line = lines.pop(0)
        await asyncio.sleep(0.2)  # Simulate read delay
        return line.strip()

async def process_async_iterator():
    """Process lines from async file reader."""
    async with AsyncFileReader("example.txt") as reader:
        async for line in reader:
            print(f"Read line: {line}")

async def main():
    await process_async_iterator()

# asyncio.run(main())  # Uncomment to run
```

## Real-world Applications

### Async Web Scraping
```python
import asyncio
import aiohttp
from bs4 import BeautifulSoup

async def fetch_url(session, url):
    """Fetch content from a URL."""
    try:
        async with session.get(url) as response:
            return await response.text()
    except Exception as e:
        print(f"Error fetching {url}: {e}")
        return None

async def parse_html(html, url):
    """Parse HTML content."""
    if html is None:
        return None
    
    soup = BeautifulSoup(html, 'html.parser')
    title = soup.title.string if soup.title else "No title"
    return f"{url}: {title}"

async def scrape_websites(urls):
    """Scrape multiple websites concurrently."""
    async with aiohttp.ClientSession() as session:
        # Create tasks for fetching URLs
        fetch_tasks = [fetch_url(session, url) for url in urls]
        html_contents = await asyncio.gather(*fetch_tasks)
        
        # Create tasks for parsing HTML
        parse_tasks = [
            parse_html(html, url) 
            for html, url in zip(html_contents, urls)
        ]
        results = await asyncio.gather(*parse_tasks)
        
        return [result for result in results if result is not None]

async def main():
    # Example URLs (replace with real URLs for actual scraping)
    urls = [
        "https://httpbin.org/html",
        "https://httpbin.org/json",
        "https://httpbin.org/xml"
    ]
    
    results = await scrape_websites(urls)
    for result in results:
        print(result)

# Note: This requires aiohttp and beautifulsoup4
# pip install aiohttp beautifulsoup4
# asyncio.run(main())  # Uncomment to run
```

### Async Database Operations
```python
import asyncio
import sqlite3
from contextlib import asynccontextmanager

class AsyncDatabase:
    """Async wrapper for SQLite database operations."""
    
    def __init__(self, db_path):
        self.db_path = db_path
        self.connection = None
    
    async def connect(self):
        """Connect to database asynchronously."""
        await asyncio.sleep(0.1)  # Simulate connection time
        self.connection = sqlite3.connect(self.db_path)
        self.connection.row_factory = sqlite3.Row
        print(f"Connected to {self.db_path}")
    
    async def execute_query(self, query, params=None):
        """Execute a database query asynchronously."""
        if not self.connection:
            await self.connect()
        
        await asyncio.sleep(0.05)  # Simulate query time
        
        if params:
            cursor = self.connection.execute(query, params)
        else:
            cursor = self.connection.execute(query)
        
        return cursor.fetchall()
    
    async def close(self):
        """Close database connection."""
        if self.connection:
            await asyncio.sleep(0.05)  # Simulate close time
            self.connection.close()
            self.connection = None
            print("Database connection closed")

@asynccontextmanager
async def get_database(db_path):
    """Async context manager for database."""
    db = AsyncDatabase(db_path)
    try:
        await db.connect()
        yield db
    finally:
        await db.close()

async def setup_database(db_path):
    """Setup database with sample data."""
    async with get_database(db_path) as db:
        # Create table
        await db.execute_query("""
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT UNIQUE,
                age INTEGER
            )
        """)
        
        # Insert sample data
        users = [
            ("Alice", "alice@example.com", 25),
            ("Bob", "bob@example.com", 30),
            ("Charlie", "charlie@example.com", 35)
        ]
        
        for name, email, age in users:
            await db.execute_query(
                "INSERT INTO users (name, email, age) VALUES (?, ?, ?)",
                (name, email, age)
            )
        
        print("Database setup complete")

async def query_database(db_path):
    """Query database asynchronously."""
    async with get_database(db_path) as db:
        # Query all users
        users = await db.execute_query("SELECT * FROM users")
        
        print("All users:")
        for user in users:
            print(f"  {dict(user)}")
        
        # Query with parameters
        young_users = await db.execute_query(
            "SELECT * FROM users WHERE age < ?", (30,)
        )
        
        print("\nYoung users (age < 30):")
        for user in young_users:
            print(f"  {dict(user)}")

async def main():
    db_path = "async_database.db"
    
    await setup_database(db_path)
    await query_database(db_path)

# asyncio.run(main())  # Uncomment to run
```

### Async HTTP Server
```python
from aiohttp import web
import asyncio
import json

async def handle_get(request):
    """Handle GET requests."""
    name = request.query.get('name', 'World')
    return web.Response(text=f"Hello, {name}!")

async def handle_post(request):
    """Handle POST requests."""
    try:
        data = await request.json()
        response_data = {
            "received": data,
            "status": "success"
        }
        return web.json_response(response_data)
    except json.JSONDecodeError:
        return web.Response(
            text="Invalid JSON",
            status=400
        )

async def handle_websocket(request):
    """Handle WebSocket connections."""
    ws = web.WebSocketResponse()
    await ws.prepare(request)
    
    print("WebSocket connection opened")
    
    try:
        async for msg in ws:
            if msg.type == web.WSMsgType.TEXT:
                message = msg.data
                print(f"Received: {message}")
                await ws.send_str(f"Echo: {message}")
            elif msg.type == web.WSMsgType.ERROR:
                print(f"WebSocket error: {ws.exception()}")
    except Exception as e:
        print(f"WebSocket error: {e}")
    finally:
        print("WebSocket connection closed")
    
    return ws

def create_app():
    """Create and configure the web application."""
    app = web.Application()
    
    # Add routes
    app.router.add_get('/', handle_get)
    app.router.add_post('/', handle_post)
    app.router.add_get('/ws', handle_websocket)
    
    return app

async def start_server():
    """Start the async web server."""
    app = create_app()
    
    runner = web.AppRunner(app)
    await runner.setup()
    
    site = web.TCPSite(runner, 'localhost', 8080)
    await runner.start()
    
    print("Server started at http://localhost:8080")
    print("Try:")
    print("  GET http://localhost:8080/")
    print("  GET http://localhost:8080/?name=Alice")
    print("  POST http://localhost:8080/ with JSON data")
    print("  WebSocket: ws://localhost:8080/ws")
    
    try:
        await asyncio.sleep(30)  # Run for 30 seconds
    except KeyboardInterrupt:
        print("Shutting down server...")
    finally:
        await runner.cleanup()

# Note: This requires aiohttp
# pip install aiohttp
# asyncio.run(start_server())  # Uncomment to run
```

## Advanced Patterns

### Async Producer-Consumer
```python
import asyncio
import random
from asyncio import Queue

class AsyncProducer:
    """Async producer that generates items."""
    
    def __init__(self, queue: Queue, max_items=10):
        self.queue = queue
        self.max_items = max_items
        self.produced = 0
    
    async def produce(self):
        """Produce items and put them in the queue."""
        while self.produced < self.max_items:
            item = f"Item-{self.produced}"
            
            # Simulate production time
            await asyncio.sleep(random.uniform(0.1, 0.5))
            
            await self.queue.put(item)
            self.produced += 1
            print(f"Produced: {item}")
        
        # Signal end of production
        await self.queue.put(None)
        print("Producer finished")

class AsyncConsumer:
    """Async consumer that processes items."""
    
    def __init__(self, queue: Queue, consumer_id: int):
        self.queue = queue
        self.consumer_id = consumer_id
        self.consumed = 0
    
    async def consume(self):
        """Consume items from the queue."""
        while True:
            item = await self.queue.get()
            
            if item is None:
                await self.queue.put(None)  # Signal other consumers
                break
            
            # Simulate processing time
            await asyncio.sleep(random.uniform(0.2, 0.8))
            
            self.consumed += 1
            print(f"Consumer {self.consumer_id} consumed: {item}")
        
        print(f"Consumer {self.consumer_id} finished (consumed {self.consumed} items)")

async def producer_consumer_pattern():
    """Demonstrate async producer-consumer pattern."""
    queue = Queue()
    
    # Create producer and consumers
    producer = AsyncProducer(queue, max_items=20)
    consumers = [AsyncConsumer(queue, i) for i in range(3)]
    
    # Start all tasks
    tasks = [
        asyncio.create_task(producer.produce()),
        *[asyncio.create_task(consumer.consume()) for consumer in consumers]
    ]
    
    # Wait for all tasks to complete
    await asyncio.gather(*tasks)

async def main():
    await producer_consumer_pattern()

# asyncio.run(main())  # Uncomment to run
```

### Async Rate Limiting
```python
import asyncio
import time
from collections import deque

class AsyncRateLimiter:
    """Rate limiter for async operations."""
    
    def __init__(self, max_calls, time_window):
        self.max_calls = max_calls
        self.time_window = time_window
        self.calls = deque()
    
    async def acquire(self):
        """Acquire permission to proceed."""
        current_time = time.time()
        
        # Remove old calls outside the time window
        while self.calls and current_time - self.calls[0] > self.time_window:
            self.calls.popleft()
        
        # Wait if we've hit the limit
        if len(self.calls) >= self.max_calls:
            sleep_time = self.time_window - (current_time - self.calls[0])
            if sleep_time > 0:
                await asyncio.sleep(sleep_time)
        
        self.calls.append(time.time())

async def rate_limited_api_call(limiter, call_id):
    """Simulate a rate-limited API call."""
    await limiter.acquire()
    
    print(f"API call {call_id} executed")
    await asyncio.sleep(0.1)  # Simulate API processing
    return f"Result for call {call_id}"

async def demonstrate_rate_limiting():
    """Demonstrate rate limiting."""
    # 5 calls per 2 seconds
    limiter = AsyncRateLimiter(max_calls=5, time_window=2)
    
    # Create many concurrent calls
    tasks = [
        rate_limited_api_call(limiter, i)
        for i in range(15)
    ]
    
    results = await asyncio.gather(*tasks)
    
    for result in results:
        print(result)

async def main():
    await demonstrate_rate_limiting()

# asyncio.run(main())  # Uncomment to run
```

### Async Caching
```python
import asyncio
from functools import wraps
import time

class AsyncCache:
    """Async cache with TTL (time-to-live)."""
    
    def __init__(self, ttl=300):  # 5 minutes default TTL
        self.cache = {}
        self.ttl = ttl
    
    def __call__(self, func):
        @wraps(func)
        async def wrapper(*args, **kwargs):
            # Create cache key
            key = str(args) + str(sorted(kwargs.items()))
            
            current_time = time.time()
            
            # Check if cached and not expired
            if key in self.cache:
                cached_data, timestamp = self.cache[key]
                if current_time - timestamp < self.ttl:
                    print(f"Cache hit for {func.__name__}")
                    return cached_data
                else:
                    print(f"Cache expired for {func.__name__}")
            
            # Execute function and cache result
            print(f"Cache miss for {func.__name__}")
            result = await func(*args, **kwargs)
            self.cache[key] = (result, current_time)
            return result
        
        return wrapper

# Create cache instance
cache = AsyncCache(ttl=5)  # 5 seconds TTL

@cache
async def expensive_operation(operation_id, duration=1):
    """Simulate an expensive operation."""
    print(f"Starting expensive operation {operation_id}")
    await asyncio.sleep(duration)
    return f"Result of operation {operation_id}"

async def demonstrate_async_caching():
    """Demonstrate async caching."""
    
    # First calls - cache misses
    print("First round (cache misses):")
    result1 = await expensive_operation("A", 0.5)
    result2 = await expensive_operation("B", 0.3)
    
    # Second calls - cache hits
    print("\nSecond round (cache hits):")
    result3 = await expensive_operation("A", 0.5)
    result4 = await expensive_operation("B", 0.3)
    
    # Wait for cache to expire
    print("\nWaiting for cache to expire...")
    await asyncio.sleep(6)
    
    # Third calls - cache expired
    print("\nThird round (cache expired):")
    result5 = await expensive_operation("A", 0.5)
    result6 = await expensive_operation("B", 0.3)

async def main():
    await demonstrate_async_caching()

# asyncio.run(main())  # Uncomment to run
```

## Error Handling and Debugging

### Async Error Handling
```python
import asyncio

async def risky_operation(success_rate=0.8):
    """Operation that might fail."""
    import random
    
    await asyncio.sleep(0.5)
    
    if random.random() > success_rate:
        raise ValueError("Random failure occurred")
    
    return "Success"

async def handle_async_errors():
    """Demonstrate async error handling patterns."""
    
    # Pattern 1: Try-catch in async function
    try:
        result = await risky_operation(0.5)  # Lower success rate
        print(f"Operation succeeded: {result}")
    except ValueError as e:
        print(f"Operation failed: {e}")
    
    # Pattern 2: asyncio.gather with exception handling
    tasks = [
        risky_operation(0.7),
        risky_operation(0.9),
        risky_operation(0.6)
    ]
    
    results = await asyncio.gather(*tasks, return_exceptions=True)
    
    print("\nResults with exception handling:")
    for i, result in enumerate(results):
        if isinstance(result, Exception):
            print(f"Task {i} failed: {result}")
        else:
            print(f"Task {i} succeeded: {result}")
    
    # Pattern 3: Retry mechanism
    async def retry_operation(max_retries=3, delay=0.1):
        for attempt in range(max_retries):
            try:
                return await risky_operation(0.3)  # Low success rate
            except ValueError as e:
                if attempt == max_retries - 1:
                    raise
                print(f"Attempt {attempt + 1} failed, retrying in {delay}s...")
                await asyncio.sleep(delay)
    
    try:
        result = await retry_operation()
        print(f"Retry succeeded: {result}")
    except ValueError as e:
        print(f"All retries failed: {e}")

async def main():
    await handle_async_errors()

# asyncio.run(main())  # Uncomment to run
```

### Async Debugging
```python
import asyncio
import logging

# Configure logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

async def debug_async_function():
    """Async function with debug logging."""
    logger.debug("Starting async function")
    
    try:
        logger.debug("Performing async operation")
        await asyncio.sleep(1)
        logger.debug("Async operation completed")
        return "Success"
    except Exception as e:
        logger.error(f"Error in async function: {e}")
        raise

async def debug_task(name, duration):
    """Task with debug information."""
    logger.debug(f"Starting task {name}")
    
    start_time = asyncio.get_event_loop().time()
    await asyncio.sleep(duration)
    end_time = asyncio.get_event_loop().time()
    
    logger.debug(f"Task {name} completed in {end_time - start_time:.2f}s")
    return f"Task {name} result"

async def debug_concurrent_operations():
    """Debug concurrent async operations."""
    logger.debug("Starting concurrent operations")
    
    tasks = [
        debug_task("A", 0.5),
        debug_task("B", 1.0),
        debug_task("C", 0.3)
    ]
    
    results = await asyncio.gather(*tasks)
    logger.debug(f"All tasks completed: {results}")
    
    return results

async def main():
    result = await debug_async_function()
    print(f"Result: {result}")
    
    results = await debug_concurrent_operations()
    print(f"Concurrent results: {results}")

# asyncio.run(main())  # Uncomment to run
```

## Performance Considerations

### Async vs Sync Performance
```python
import asyncio
import time
import requests

def sync_http_request(url):
    """Synchronous HTTP request."""
    return requests.get(url).json()

async def async_http_request(session, url):
    """Asynchronous HTTP request."""
    async with session.get(url) as response:
        return await response.json()

async def compare_sync_async():
    """Compare synchronous vs asynchronous performance."""
    urls = [
        "https://httpbin.org/json",
        "https://httpbin.org/uuid",
        "https://httpbin.org/ip"
    ] * 3  # 9 total requests
    
    # Synchronous version
    print("Synchronous version:")
    start = time.time()
    sync_results = []
    for url in urls:
        result = sync_http_request(url)
        sync_results.append(result)
    sync_time = time.time() - start
    
    # Asynchronous version
    print("\nAsynchronous version:")
    start = time.time()
    
    async def run_async():
        import aiohttp
        async with aiohttp.ClientSession() as session:
            tasks = [async_http_request(session, url) for url in urls]
            async_results = await asyncio.gather(*tasks)
            return async_results
    
    async_results = await run_async()
    async_time = time.time() - start
    
    print(f"\nPerformance comparison:")
    print(f"Synchronous: {sync_time:.2f} seconds")
    print(f"Asynchronous: {async_time:.2f} seconds")
    print(f"Speedup: {sync_time / async_time:.2f}x")

# Note: This requires both requests and aiohttp
# pip install requests aiohttp
# asyncio.run(compare_sync_async())  # Uncomment to run
```

### Async Best Practices
```python
import asyncio
from typing import List, Optional

# Best Practice 1: Use proper type hints
async def typed_function(param1: str, param2: int) -> str:
    """Async function with type hints."""
    await asyncio.sleep(0.1)
    return f"{param1}_{param2}"

# Best Practice 2: Use context managers for resources
class AsyncResource:
    """Async resource manager."""
    
    async def __aenter__(self):
        await asyncio.sleep(0.1)
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        await asyncio.sleep(0.1)
        return False

async def use_resources():
    """Use async context managers properly."""
    async with AsyncResource() as resource:
        # Use resource here
        await asyncio.sleep(0.5)
    # Resource automatically cleaned up

# Best Practice 3: Handle cancellation gracefully
class CancellableTask:
    """Task that can be cancelled."""
    
    def __init__(self, name):
        self.name = name
        self.cancelled = False
    
    async def run(self):
        """Run task with cancellation support."""
        try:
            for i in range(10):
                if self.cancelled:
                    print(f"{self.name}: Task cancelled")
                    break
                
                print(f"{self.name}: Working {i}")
                await asyncio.sleep(0.5)
        except asyncio.CancelledError:
            print(f"{self.name}: Task was cancelled")
    
    def cancel(self):
        """Cancel the task."""
        self.cancelled = True

# Best Practice 4: Use proper error handling
async def robust_operation():
    """Robust async operation with error handling."""
    try:
        await asyncio.sleep(0.5)
        return "Success"
    except asyncio.CancelledError:
        print("Operation was cancelled")
        raise
    except Exception as e:
        print(f"Operation failed: {e}")
        raise

# Best Practice 5: Use asyncio.shield for cancellation isolation
async def shielded_operation():
    """Operation shielded from cancellation."""
    try:
        await asyncio.shield(robust_operation())
        return "Shielded success"
    except Exception as e:
        print(f"Shielded operation failed: {e}")
        raise

# Best Practice 6: Use timeouts for operations
async def timeout_operation():
    """Operation with timeout."""
    try:
        result = await asyncio.wait_for(
            asyncio.sleep(2),  # This will take 2 seconds
            timeout=1.0      # But we only wait 1 second
        )
        return result
    except asyncio.TimeoutError:
        print("Operation timed out")
        return "Timeout"

# Best Practice 7: Use semaphores for concurrency control
class AsyncSemaphore:
    """Async semaphore for concurrency control."""
    
    def __init__(self, max_concurrent):
        self.semaphore = asyncio.Semaphore(max_concurrent)
    
    async def acquire(self):
        await self.semaphore.acquire()
    
    def release(self):
        self.semaphore.release()
    
    async def __aenter__(self):
        await self.acquire()
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        self.release()

async def limited_concurrency():
    """Demonstrate limited concurrency."""
    semaphore = AsyncSemaphore(max_concurrent=2)
    
    async def worker(worker_id):
        async with semaphore:
            print(f"Worker {worker_id} started")
            await asyncio.sleep(1)
            print(f"Worker {worker_id} finished")
    
    tasks = [worker(i) for i in range(5)]
    await asyncio.gather(*tasks)

# Best Practice 8: Use asyncio.run for main entry point
def main():
    """Main entry point with proper async handling."""
    async def async_main():
        print("Starting async main")
        
        # Demonstrate best practices
        await use_resources()
        
        # Test cancellation
        task = CancellableTask("Test")
        run_task = asyncio.create_task(task.run())
        
        await asyncio.sleep(1)
        task.cancel()
        await run_task
        
        # Test timeout
        await timeout_operation()
        
        # Test limited concurrency
        await limited_concurrency()
        
        print("Async main completed")
    
    # Run the async main function
    asyncio.run(async_main())

# main()  # Uncomment to run
```

## Summary

Python async programming provides powerful capabilities:

**Core Concepts:**
- `async/await` syntax for asynchronous code
- Event loop for managing concurrent execution
- Non-blocking operations for better performance
- Coroutines for cooperative multitasking

**Key Features:**
- `asyncio.gather()` for concurrent task execution
- Async context managers for resource management
- Async generators and iterators
- Proper error handling and cancellation

**Common Patterns:**
- Producer-consumer with async queues
- Rate limiting for API calls
- Async caching with TTL
- Concurrent web scraping and API calls
- Database operations with async drivers

**Best Practices:**
- Use type hints for clarity
- Handle cancellation gracefully
- Use context managers for resources
- Implement proper error handling
- Control concurrency with semaphores
- Use timeouts for reliability

**Performance Benefits:**
- Concurrent I/O operations
- Non-blocking network requests
- Efficient resource utilization
- Scalable web applications

Async programming is essential for high-performance Python applications, especially those involving I/O-bound operations, network requests, or real-time processing.
