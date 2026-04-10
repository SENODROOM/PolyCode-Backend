# Python Multiprocessing

## Multiprocessing Basics

### Understanding Multiprocessing
```python
import multiprocessing
import time
import os

def cpu_bound_task(n):
    """CPU-bound task that benefits from multiprocessing."""
    print(f"Process {os.getpid()}: Starting task with n={n}")
    
    # Simulate CPU-intensive work
    result = 0
    for i in range(n):
        result += i * i
    
    print(f"Process {os.getpid()}: Completed task")
    return result

def sequential_execution():
    """Execute tasks sequentially."""
    print("Sequential execution:")
    start_time = time.time()
    
    results = []
    for n in [1000000, 2000000, 3000000]:
        result = cpu_bound_task(n)
        results.append(result)
    
    end_time = time.time()
    print(f"Sequential time: {end_time - start_time:.2f} seconds")
    return results

def parallel_execution():
    """Execute tasks in parallel using multiprocessing."""
    print("Parallel execution:")
    start_time = time.time()
    
    # Create process pool
    with multiprocessing.Pool(processes=multiprocessing.cpu_count()) as pool:
        results = pool.map(cpu_bound_task, [1000000, 2000000, 3000000])
    
    end_time = time.time()
    print(f"Parallel time: {end_time - start_time:.2f} seconds")
    return results

if __name__ == "__main__":
    print(f"Number of CPU cores: {multiprocessing.cpu_count()}")
    
    # Compare sequential vs parallel execution
    sequential_results = sequential_execution()
    parallel_results = parallel_execution()
    
    print(f"Results match: {sequential_results == parallel_results}")
```

### Process Creation
```python
import multiprocessing
import time
import os

def worker_function(name, duration):
    """Simple worker function."""
    print(f"Worker {name} (PID: {os.getpid()}) started")
    time.sleep(duration)
    print(f"Worker {name} (PID: {os.getpid()}) finished")
    return f"Worker {name} result"

def create_process():
    """Create and run a single process."""
    process = multiprocessing.Process(
        target=worker_function,
        args=("Alice", 2)
    )
    
    print("Starting process...")
    process.start()
    
    print("Waiting for process to complete...")
    process.join()
    
    print("Process completed")

def create_multiple_processes():
    """Create and run multiple processes."""
    workers = [
        ("Alice", 1),
        ("Bob", 2),
        ("Charlie", 1.5)
    ]
    
    processes = []
    
    # Create processes
    for name, duration in workers:
        process = multiprocessing.Process(
            target=worker_function,
            args=(name, duration)
        )
        processes.append(process)
        process.start()
    
    # Wait for all processes to complete
    for process in processes:
        process.join()
    
    print("All processes completed")

if __name__ == "__main__":
    print("Single process example:")
    create_process()
    
    print("\nMultiple processes example:")
    create_multiple_processes()
```

## Process Pools

### Using Pool.map
```python
import multiprocessing
import math

def calculate_square(n):
    """Calculate square of a number."""
    return n * n

def calculate_factorial(n):
    """Calculate factorial of a number."""
    return math.factorial(n)

def pool_map_example():
    """Demonstrate Pool.map usage."""
    numbers = list(range(1, 11))
    
    with multiprocessing.Pool(processes=4) as pool:
        # Calculate squares
        squares = pool.map(calculate_square, numbers)
        print(f"Squares: {squares}")
        
        # Calculate factorials
        factorials = pool.map(calculate_factorial, numbers)
        print(f"Factorials: {factorials}")

if __name__ == "__main__":
    pool_map_example()
```

### Using Pool.apply_async
```python
import multiprocessing
import time
import random

def random_task(task_id):
    """Random task with variable duration."""
    duration = random.uniform(0.5, 2.0)
    time.sleep(duration)
    return f"Task {task_id} completed in {duration:.2f}s"

def apply_async_example():
    """Demonstrate Pool.apply_async usage."""
    with multiprocessing.Pool(processes=3) as pool:
        # Submit tasks asynchronously
        results = []
        for i in range(5):
            result = pool.apply_async(random_task, (i,))
            results.append(result)
        
        # Get results as they complete
        for result in results:
            print(result.get())

if __name__ == "__main__":
    apply_async_example()
```

### Using Pool.imap_unordered
```python
import multiprocessing
import time

def slow_task(n):
    """Task with variable completion time."""
    time.sleep(n % 3 + 1)  # Sleep for 1-3 seconds
    return f"Task {n} completed"

def imap_unordered_example():
    """Demonstrate Pool.imap_unordered."""
    tasks = range(5, 0, -1)  # 5, 4, 3, 2, 1
    
    with multiprocessing.Pool(processes=3) as pool:
        print("Processing tasks (unordered results):")
        for result in pool.imap_unordered(slow_task, tasks):
            print(f"  {result}")

if __name__ == "__main__":
    imap_unordered_example()
```

## Inter-Process Communication

### Using Queue
```python
import multiprocessing
import time
import random

def producer(queue, max_items):
    """Producer process that puts items in queue."""
    for i in range(max_items):
        item = f"Item-{i}"
        queue.put(item)
        print(f"Produced: {item}")
        time.sleep(random.uniform(0.1, 0.5))
    
    # Signal end of production
    queue.put(None)
    print("Producer finished")

def consumer(queue, consumer_id):
    """Consumer process that gets items from queue."""
    while True:
        item = queue.get()
        
        if item is None:
            # End signal received
            queue.put(None)  # Pass signal to next consumer
            break
        
        print(f"Consumer {consumer_id}: Consumed {item}")
        time.sleep(random.uniform(0.2, 0.8))
    
    print(f"Consumer {consumer_id} finished")

def producer_consumer_example():
    """Demonstrate producer-consumer pattern."""
    queue = multiprocessing.Queue()
    
    # Create producer and consumer processes
    producer_process = multiprocessing.Process(
        target=producer,
        args=(queue, 10)
    )
    
    consumer_processes = [
        multiprocessing.Process(target=consumer, args=(queue, i))
        for i in range(2)
    ]
    
    # Start all processes
    producer_process.start()
    for consumer_process in consumer_processes:
        consumer_process.start()
    
    # Wait for completion
    producer_process.join()
    for consumer_process in consumer_processes:
        consumer_process.join()
    
    print("Producer-consumer example completed")

if __name__ == "__main__":
    producer_consumer_example()
```

### Using Pipe
```python
import multiprocessing
import time

def sender(conn):
    """Sender process that sends messages through pipe."""
    messages = ["Hello", "World", "from", "sender", "process"]
    
    for message in messages:
        conn.send(message)
        print(f"Sent: {message}")
        time.sleep(0.5)
    
    conn.send(None)  # Signal end
    conn.close()

def receiver(conn):
    """Receiver process that receives messages through pipe."""
    while True:
        message = conn.recv()
        
        if message is None:
            break
        
        print(f"Received: {message}")
        time.sleep(0.3)
    
    conn.close()
    print("Receiver finished")

def pipe_example():
    """Demonstrate inter-process communication with pipes."""
    # Create pipe
    parent_conn, child_conn = multiprocessing.Pipe()
    
    # Create processes
    sender_process = multiprocessing.Process(
        target=sender,
        args=(child_conn,)
    )
    
    receiver_process = multiprocessing.Process(
        target=receiver,
        args=(parent_conn,)
    )
    
    # Start processes
    receiver_process.start()
    sender_process.start()
    
    # Wait for completion
    sender_process.join()
    receiver_process.join()
    
    print("Pipe communication completed")

if __name__ == "__main__":
    pipe_example()
```

### Using Value and Array
```python
import multiprocessing

def worker_shared_value(shared_value):
    """Worker that modifies shared value."""
    for i in range(5):
        with shared_value.get_lock():
            shared_value.value += 1
            print(f"Worker: shared_value = {shared_value.value}")
        multiprocessing.current_process().sleep(0.1)

def worker_shared_array(shared_array):
    """Worker that modifies shared array."""
    for i in range(5):
        with shared_array.get_lock():
            shared_array[i] = i * 10
            print(f"Worker: shared_array[{i}] = {shared_array[i]}")
        multiprocessing.current_process().sleep(0.1)

def shared_memory_example():
    """Demonstrate shared memory with Value and Array."""
    
    # Shared value
    shared_value = multiprocessing.Value('i', 0)
    
    # Shared array
    shared_array = multiprocessing.Array('i', [0] * 5)
    
    # Create processes
    processes = [
        multiprocessing.Process(target=worker_shared_value, args=(shared_value,)),
        multiprocessing.Process(target=worker_shared_array, args=(shared_array,))
    ]
    
    # Start processes
    for process in processes:
        process.start()
    
    # Wait for completion
    for process in processes:
        process.join()
    
    print(f"Final shared_value: {shared_value.value}")
    print(f"Final shared_array: {list(shared_array)}")

if __name__ == "__main__":
    shared_memory_example()
```

## Advanced Multiprocessing

### Process Pool with Callback
```python
import multiprocessing
import time

def task_with_callback(task_id):
    """Task that takes some time to complete."""
    time.sleep(1)
    return f"Task {task_id} completed"

def callback(result):
    """Callback function called when task completes."""
    print(f"Callback received: {result}")

def callback_example():
    """Demonstrate Pool with callback."""
    with multiprocessing.Pool(processes=3) as pool:
        # Submit tasks with callbacks
        for i in range(5):
            pool.apply_async(
                task_with_callback,
                args=(i,),
                callback=callback
            )
        
        # Wait for all tasks to complete
        pool.close()
        pool.join()

if __name__ == "__main__":
    callback_example()
```

### Process Pool with Partial Functions
```python
import multiprocessing
from functools import partial

def power(base, exponent):
    """Calculate base raised to exponent."""
    return base ** exponent

def partial_function_example():
    """Demonstrate Pool with partial functions."""
    
    # Create partial functions
    square = partial(power, exponent=2)
    cube = partial(power, exponent=3)
    
    numbers = [1, 2, 3, 4, 5]
    
    with multiprocessing.Pool(processes=2) as pool:
        # Use partial functions
        squares = pool.map(square, numbers)
        cubes = pool.map(cube, numbers)
        
        print(f"Numbers: {numbers}")
        print(f"Squares: {squares}")
        print(f"Cubes: {cubes}")

if __name__ == "__main__":
    partial_function_example()
```

### Subclassing Process
```python
import multiprocessing
import time

class WorkerProcess(multiprocessing.Process):
    """Custom worker process class."""
    
    def __init__(self, task_queue, result_queue):
        super().__init__()
        self.task_queue = task_queue
        self.result_queue = result_queue
    
    def run(self):
        """Run the worker process."""
        print(f"Worker {self.pid} started")
        
        while True:
            try:
                # Get task from queue
                task = self.task_queue.get(timeout=1)
                
                if task is None:
                    # Shutdown signal
                    break
                
                # Process task
                result = self.process_task(task)
                
                # Put result in queue
                self.result_queue.put(result)
                
            except Exception as e:
                print(f"Worker {self.pid} error: {e}")
        
        print(f"Worker {self.pid} finished")
    
    def process_task(self, task):
        """Process a single task."""
        task_id, data = task
        time.sleep(0.5)  # Simulate work
        return f"Task {task_id} processed: {data * 2}"

def custom_process_example():
    """Demonstrate custom process subclass."""
    
    # Create queues
    task_queue = multiprocessing.Queue()
    result_queue = multiprocessing.Queue()
    
    # Create worker processes
    workers = [
        WorkerProcess(task_queue, result_queue)
        for _ in range(3)
    ]
    
    # Start workers
    for worker in workers:
        worker.start()
    
    # Submit tasks
    tasks = [(i, i * 10) for i in range(10)]
    for task in tasks:
        task_queue.put(task)
    
    # Collect results
    results = []
    for _ in range(len(tasks)):
        result = result_queue.get()
        results.append(result)
        print(f"Received: {result}")
    
    # Shutdown workers
    for _ in workers:
        task_queue.put(None)
    
    # Wait for workers to finish
    for worker in workers:
        worker.join()
    
    print(f"All results: {results}")

if __name__ == "__main__":
    custom_process_example()
```

## Real-world Applications

### Parallel Image Processing
```python
import multiprocessing
from PIL import Image, ImageFilter
import os

def process_image(image_path, output_dir):
    """Process a single image."""
    try:
        # Open image
        with Image.open(image_path) as img:
            # Apply filter
            filtered = img.filter(ImageFilter.BLUR)
            
            # Save processed image
            filename = os.path.basename(image_path)
            output_path = os.path.join(output_dir, f"blurred_{filename}")
            filtered.save(output_path)
            
            return f"Processed: {filename}"
    except Exception as e:
        return f"Error processing {image_path}: {e}"

def parallel_image_processing(input_dir, output_dir):
    """Process multiple images in parallel."""
    
    # Create output directory
    os.makedirs(output_dir, exist_ok=True)
    
    # Get list of image files
    image_files = [
        os.path.join(input_dir, f) 
        for f in os.listdir(input_dir) 
        if f.lower().endswith(('.jpg', '.jpeg', '.png', '.bmp'))
    ]
    
    if not image_files:
        print("No image files found")
        return
    
    print(f"Processing {len(image_files)} images...")
    
    # Process images in parallel
    with multiprocessing.Pool(processes=multiprocessing.cpu_count()) as pool:
        results = pool.map(process_image, image_files)
    
    # Print results
    for result in results:
        print(result)

# Note: This requires Pillow
# pip install Pillow
# Create some dummy images for testing
def create_dummy_images():
    """Create dummy images for testing."""
    from PIL import Image, ImageDraw
    
    input_dir = "input_images"
    output_dir = "output_images"
    
    os.makedirs(input_dir, exist_ok=True)
    
    # Create dummy images
    for i in range(5):
        img = Image.new('RGB', (100, 100), color=(255, 0, 0))
        draw = ImageDraw.Draw(img)
        draw.rectangle([25, 25, 75, 75], fill=(0, 255, 0))
        draw.text((35, 45), str(i), fill=(255, 255, 255))
        img.save(os.path.join(input_dir, f"image_{i}.png"))
    
    print(f"Created dummy images in {input_dir}")

if __name__ == "__main__":
    # Create dummy images
    create_dummy_images()
    
    # Process images
    parallel_image_processing("input_images", "output_images")
```

### Parallel Data Processing
```python
import multiprocessing
import pandas as pd
import numpy as np
import time

def process_chunk(chunk_data, chunk_id):
    """Process a chunk of data."""
    print(f"Processing chunk {chunk_id} with {len(chunk_data)} rows")
    
    # Simulate processing time
    time.sleep(0.5)
    
    # Convert to DataFrame and process
    df = pd.DataFrame(chunk_data)
    
    # Example processing: calculate statistics
    stats = {
        'chunk_id': chunk_id,
        'mean': df.iloc[:, 0].mean(),
        'std': df.iloc[:, 0].std(),
        'count': len(df)
    }
    
    return stats

def parallel_data_processing():
    """Demonstrate parallel data processing."""
    
    # Generate sample data
    print("Generating sample data...")
    data = np.random.randn(100000, 5)  # 100k rows, 5 columns
    
    # Split data into chunks
    chunk_size = 10000
    chunks = [data[i:i+chunk_size] for i in range(0, len(data), chunk_size)]
    
    print(f"Processing {len(chunks)} chunks of size {chunk_size}")
    
    # Process chunks in parallel
    with multiprocessing.Pool(processes=multiprocessing.cpu_count()) as pool:
        results = pool.starmap(process_chunk, chunks, range(len(chunks)))
    
    # Aggregate results
    print("\nResults:")
    for result in results:
        print(f"Chunk {result['chunk_id']}: "
              f"mean={result['mean']:.3f}, "
              f"std={result['std']:.3f}, "
              f"count={result['count']}")

# Note: This requires pandas and numpy
# pip install pandas numpy
if __name__ == "__main__":
    parallel_data_processing()
```

### Parallel Web Scraping
```python
import multiprocessing
import requests
from bs4 import BeautifulSoup
import time
import os

def scrape_url(url):
    """Scrape a single URL."""
    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        
        soup = BeautifulSoup(response.content, 'html.parser')
        title = soup.title.string if soup.title else "No title"
        
        return f"{url}: {title}"
    except Exception as e:
        return f"{url}: Error - {e}"

def parallel_web_scraping(urls):
    """Scrape multiple URLs in parallel."""
    
    print(f"Scraping {len(urls)} URLs...")
    
    # Scrape URLs in parallel
    with multiprocessing.Pool(processes=multiprocessing.cpu_count()) as pool:
        results = pool.map(scrape_url, urls)
    
    # Print results
    print("\nScraping results:")
    for result in results:
        print(f"  {result}")

# Note: This requires requests and beautifulsoup4
# pip install requests beautifulsoup4
if __name__ == "__main__":
    # Example URLs (replace with real URLs for actual scraping)
    test_urls = [
        "https://httpbin.org/html",
        "https://httpbin.org/json",
        "https://httpbin.org/xml",
        "https://httpbin.org/forms/post",
        "https://httpbin.org/robots.txt"
    ]
    
    parallel_web_scraping(test_urls)
```

## Performance and Optimization

### Process vs Thread Comparison
```python
import multiprocessing
import threading
import time
import os

def cpu_intensive_task(n):
    """CPU-intensive task."""
    result = 0
    for i in range(n):
        result += i * i
    return result

def run_with_processes(tasks):
    """Run tasks using multiprocessing."""
    start_time = time.time()
    
    with multiprocessing.Pool(processes=multiprocessing.cpu_count()) as pool:
        results = pool.map(cpu_intensive_task, tasks)
    
    end_time = time.time()
    return results, end_time - start_time

def run_with_threads(tasks):
    """Run tasks using threading."""
    start_time = time.time()
    
    threads = []
    results = []
    
    def task_wrapper(task, result_list):
        result = cpu_intensive_task(task)
        result_list.append(result)
    
    for task in tasks:
        thread = threading.Thread(target=task_wrapper, args=(task, results))
        threads.append(thread)
        thread.start()
    
    for thread in threads:
        thread.join()
    
    end_time = time.time()
    return results, end_time - start_time

def compare_processes_vs_threads():
    """Compare performance of processes vs threads."""
    
    tasks = [1000000, 2000000, 3000000, 4000000]
    
    print(f"Number of CPU cores: {multiprocessing.cpu_count()}")
    print(f"Running tasks: {tasks}")
    
    # Run with processes
    process_results, process_time = run_with_processes(tasks)
    
    # Run with threads
    thread_results, thread_time = run_with_threads(tasks)
    
    print(f"\nProcess time: {process_time:.2f} seconds")
    print(f"Thread time: {thread_time:.2f} seconds")
    print(f"Speedup: {thread_time / process_time:.2f}x")
    
    print(f"Results match: {process_results == thread_results}")

if __name__ == "__main__":
    compare_processes_vs_threads()
```

### Memory Management
```python
import multiprocessing
import sys
import psutil
import os

def memory_intensive_task():
    """Task that uses a lot of memory."""
    # Create large list
    large_list = list(range(1000000))
    return len(large_list)

def monitor_memory():
    """Monitor memory usage of a process."""
    process = psutil.Process(os.getpid())
    memory_info = process.memory_info()
    
    return {
        'rss': memory_info.rss / 1024 / 1024,  # MB
        'vms': memory_info.vms / 1024 / 1024   # MB
        'percent': process.memory_percent()
    }

def memory_management_example():
    """Demonstrate memory management in multiprocessing."""
    
    print("Memory management example:")
    print(f"Initial memory: {monitor_memory()}")
    
    with multiprocessing.Pool(processes=2) as pool:
        # Run memory-intensive tasks
        results = pool.map(memory_intensive_task, range(4))
        
        print(f"During processing: {monitor_memory()}")
        print(f"Results: {results}")
    
    print(f"After processing: {monitor_memory()}")

# Note: This requires psutil
# pip install psutil
if __name__ == "__main__":
    memory_management_example()
```

## Best Practices

### Multiprocessing Best Practices
```python
import multiprocessing
import os
import logging

# Best Practice 1: Always use if __name__ == "__main__"
def safe_multiprocessing():
    """Safe multiprocessing with proper main guard."""
    
    def worker(x):
        return x * x
    
    with multiprocessing.Pool(4) as pool:
        results = pool.map(worker, range(10))
    
    return results

# Best Practice 2: Handle exceptions properly
def safe_worker(x):
    """Worker function with error handling."""
    try:
        if x < 0:
            raise ValueError("Negative numbers not allowed")
        return x * x
    except Exception as e:
        logging.error(f"Error processing {x}: {e}")
        return None

def robust_multiprocessing():
    """Robust multiprocessing with error handling."""
    
    def safe_map(pool, func, iterable):
        """Safely map function over iterable."""
        results = []
        
        for item in iterable:
            try:
                result = pool.apply(func, (item,))
                results.append(result)
            except Exception as e:
                logging.error(f"Error processing {item}: {e}")
                results.append(None)
        
        return results
    
    with multiprocessing.Pool(4) as pool:
        results = safe_map(pool, safe_worker, range(-5, 10))
    
    return results

# Best Practice 3: Use appropriate process pool size
def optimal_pool_size():
    """Determine optimal pool size based on task type."""
    
    # For CPU-bound tasks: number of CPU cores
    cpu_pool_size = multiprocessing.cpu_count()
    print(f"CPU-bound tasks optimal pool size: {cpu_pool_size}")
    
    # For I/O-bound tasks: can be larger
    io_pool_size = min(multiprocessing.cpu_count() * 2, 8)
    print(f"I/O-bound tasks optimal pool size: {io_pool_size}")

# Best Practice 4: Use proper cleanup
class ManagedProcess:
    """Process with proper cleanup."""
    
    def __init__(self, name):
        self.name = name
        self.process = None
    
    def start(self):
        self.process = multiprocessing.Process(target=self._run)
        self.process.start()
    
    def stop(self):
        if self.process and self.process.is_alive():
            self.process.terminate()
            self.process.join()
    
    def _run(self):
        try:
            print(f"Process {self.name} started")
            # Do work here
            import time
            time.sleep(2)
            print(f"Process {self.name} completed")
        except Exception as e:
            print(f"Process {self.name} error: {e}")

def managed_process_example():
    """Demonstrate managed process lifecycle."""
    
    processes = [ManagedProcess(f"Worker-{i}") for i in range(3)]
    
    # Start all processes
    for process in processes:
        process.start()
    
    # Wait for completion
    for process in processes:
        process.stop()
    
    print("All processes stopped")

# Best Practice 5: Use appropriate inter-process communication
def ipc_best_practices():
    """Demonstrate best practices for IPC."""
    
    # For small data: use Value or Array
    shared_value = multiprocessing.Value('i', 0)
    
    # For larger data: use Queue or Pipe
    queue = multiprocessing.Queue()
    
    # For two-way communication: use Pipe
    parent_conn, child_conn = multiprocessing.Pipe()
    
    print("IPC best practices demonstrated")

# Best Practice 6: Consider task granularity
def task_granularity():
    """Demonstrate appropriate task granularity."""
    
    # Too fine-grained (overhead > work)
    def fine_task(x):
        return x + 1
    
    # Too coarse-grained (poor parallelization)
    def coarse_task():
        return sum(range(1000000))
    
    # Just right (balanced)
    def balanced_task(chunk):
        return sum(chunk)
    
    # Create balanced chunks
    data = list(range(1000000))
    chunk_size = 100000
    chunks = [data[i:i+chunk_size] for i in range(0, len(data), chunk_size)]
    
    print(f"Task granularity: {len(data)} items split into {len(chunks)} chunks of size {chunk_size}")

# Best Practice 7: Monitor resource usage
def resource_monitoring():
    """Monitor system resources during multiprocessing."""
    
    import psutil
    import time
    
    def get_memory_usage():
        return psutil.virtual_memory().percent
    
    print("Resource monitoring:")
    print(f"Initial memory usage: {get_memory_usage():.1f}%")
    
    # Run multiprocessing with monitoring
    with multiprocessing.Pool(4) as pool:
        results = pool.map(safe_worker, range(100))
        print(f"During multiprocessing: {get_memory_usage():.1f}%")
    
    print(f"Final memory usage: {get_memory_usage():.1f}%")

if __name__ == "__main__":
    # Demonstrate best practices
    print("=== Safe Multiprocessing ===")
    safe_results = safe_multiprocessing()
    
    print("\n=== Robust Multiprocessing ===")
    robust_results = robust_multiprocessing()
    
    print("\n=== Optimal Pool Size ===")
    optimal_pool_size()
    
    print("\n=== Managed Process ===")
    managed_process_example()
    
    print("\n=== IPC Best Practices ===")
    ipc_best_practices()
    
    print("\n=== Task Granularity ===")
    task_granularity()
    
    print("\n=== Resource Monitoring ===")
    resource_monitoring()
```

## Summary

Python multiprocessing provides powerful capabilities:

**Core Benefits:**
- True parallelism (bypasses GIL)
- CPU-intensive task acceleration
- Process isolation and stability
- Scalable to multiple CPU cores

**Key Features:**
- Process pools for task management
- Inter-process communication (Queue, Pipe, Value, Array)
- Shared memory with synchronization
- Subclassable Process class

**Common Patterns:**
- Producer-consumer with queues
- Parallel data processing
- Image/video processing
- Web scraping and API calls
- Numerical computations

**Communication Methods:**
- Queue for producer-consumer patterns
- Pipe for two-way communication
- Value/Array for shared memory
- Managers for complex state

**Performance Considerations:**
- Process overhead vs thread overhead
- Memory usage per process
- Task granularity optimization
- Resource monitoring and cleanup

**Best Practices:**
- Always use `if __name__ == "__main__"`
- Handle exceptions properly
- Choose appropriate pool size
- Use proper cleanup procedures
- Monitor resource usage
- Balance task granularity

Multiprocessing is essential for CPU-bound Python applications, providing true parallelism that can significantly improve performance on multi-core systems.
