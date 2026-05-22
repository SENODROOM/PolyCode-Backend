# Multithreading and Concurrency

This file contains comprehensive multithreading and concurrency examples in C, including thread creation, synchronization primitives, producer-consumer patterns, thread pools, atomic operations, and deadlock prevention.

## 📚 Multithreading Fundamentals

### 🔄 Concurrency Concepts
- **Threads**: Independent execution paths within a process
- **Synchronization**: Coordinating access to shared resources
- **Race Conditions**: Unpredictable results from concurrent access
- **Deadlocks**: Circular waiting patterns that prevent progress
- **Performance**: Utilizing multiple CPU cores effectively

### 🎯 Synchronization Primitives
- **Critical Sections**: Mutual exclusion for small code regions
- **Mutexes**: General-purpose mutual exclusion objects
- **Semaphores**: Counting synchronization objects
- **Events**: Signaling mechanism for thread coordination
- **Condition Variables**: Thread synchronization based on conditions

## 🧵 Basic Thread Operations

### Thread Creation
```c
DWORD WINAPI basicThreadFunction(LPVOID param) {
    ThreadData* data = (ThreadData*)param;
    
    printf("Thread %d started\n", data->thread_id);
    
    for (int i = 0; i < 5; i++) {
        printf("Thread %d: Count = %d\n", data->thread_id, i);
        Sleep(1000); // Simulate work
    }
    
    printf("Thread %d finished\n", data->thread_id);
    return 0;
}

void demonstrateBasicThreading() {
    HANDLE threads[MAX_THREADS];
    ThreadData thread_data[MAX_THREADS];
    
    // Create multiple threads
    for (int i = 0; i < 3; i++) {
        thread_data[i].thread_id = i + 1;
        sprintf(thread_data[i].message, "Thread %d message", i + 1);
        thread_data[i].counter = 0;
        
        threads[i] = CreateThread(
            NULL,                   // Default security attributes
            0,                      // Default stack size
            basicThreadFunction,    // Thread function
            &thread_data[i],        // Parameter to thread function
            0,                      // Default creation flags
            NULL                    // Returns thread identifier
        );
        
        if (threads[i] == NULL) {
            printf("Failed to create thread %d\n", i + 1);
        }
    }
    
    // Wait for all threads to complete
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
    
    // Clean up thread handles
    for (int i = 0; i < 3; i++) {
        CloseHandle(threads[i]);
    }
}
```

### Thread Data Structure
```c
typedef struct {
    int thread_id;
    char message[256];
    int counter;
} ThreadData;
```

**Thread Characteristics**:
- **Independent Execution**: Each thread has its own execution path
- **Shared Memory**: All threads share the same process memory space
- **Context Switching**: OS switches between threads for concurrent execution
- **Stack Space**: Each thread has its own stack for local variables

## 🔒 Critical Sections

### Critical Section Usage
```c
SharedResource shared_resource;

void initSharedResource() {
    shared_resource.value = 0;
    shared_resource.readers_count = 0;
    shared_resource.writers_waiting = 0;
    InitializeCriticalSection(&shared_resource.cs);
    shared_resource.readers_sem = CreateSemaphore(NULL, 1, 1, NULL);
    shared_resource.writers_sem = CreateSemaphore(NULL, 1, 1, NULL);
}

void enterCriticalSection() {
    EnterCriticalSection(&shared_resource.cs);
    // Critical section code here
    LeaveCriticalSection(&shared_resource.cs);
}
```

### Critical Section Properties
- **Process-Local**: Only visible within the creating process
- **Fast**: Lightweight synchronization for intra-process use
- **No Handles**: Cannot be shared between processes
- **Recursive**: Same thread can enter multiple times

## 🔐 Mutex Synchronization

### Mutex Implementation
```c
HANDLE global_mutex;

void initGlobalMutex() {
    global_mutex = CreateMutex(NULL, FALSE, "GlobalMutex");
}

DWORD WINAPI mutexThread(LPVOID param) {
    int thread_id = *(int*)param;
    
    printf("Thread %d: Waiting for mutex\n", thread_id);
    
    // Wait for mutex
    WaitForSingleObject(global_mutex, INFINITE);
    
    printf("Thread %d: Acquired mutex\n", thread_id);
    
    // Critical section
    for (int i = 0; i < 3; i++) {
        printf("Thread %d: Working... %d\n", thread_id, i + 1);
        Sleep(500);
    }
    
    printf("Thread %d: Releasing mutex\n", thread_id);
    
    // Release mutex
    ReleaseMutex(global_mutex);
    
    return 0;
}
```

### Mutex Characteristics
- **System-Wide**: Can be named and shared between processes
- **Slower**: Heavier weight than critical sections
- **Handle-Based**: Uses kernel objects for synchronization
- **Ownership**: Thread that acquires mutex must release it

## 🚦 Semaphore Synchronization

### Semaphore Operations
```c
HANDLE semaphore;

void initSemaphore() {
    semaphore = CreateSemaphore(NULL, 1, 1, "ResourceSemaphore");
}

void useResource() {
    // Wait for semaphore (decrement count)
    WaitForSingleObject(semaphore, INFINITE);
    
    // Use resource
    printf("Using resource\n");
    Sleep(1000);
    
    // Release semaphore (increment count)
    ReleaseSemaphore(semaphore, 1, NULL);
}
```

### Semaphore Properties
- **Counting**: Can allow multiple threads access
- **Resource Management**: Limited resource access control
- **System-Wide**: Can be shared between processes
- **Flexible**: Supports multiple resource units

## 📡 Event Synchronization

### Event-Based Coordination
```c
HANDLE start_event;
HANDLE stop_event;

void initEvents() {
    start_event = CreateEvent(NULL, TRUE, FALSE, "StartEvent");
    stop_event = CreateEvent(NULL, TRUE, FALSE, "StopEvent");
}

DWORD WINAPI eventWorkerThread(LPVOID param) {
    int worker_id = *(int*)param;
    
    printf("Worker %d: Waiting for start event\n", worker_id);
    
    // Wait for start event
    WaitForSingleObject(start_event, INFINITE);
    
    printf("Worker %d: Started working\n", worker_id);
    
    // Do work until stop event is signaled
    while (WaitForSingleObject(stop_event, 0) == WAIT_TIMEOUT) {
        printf("Worker %d: Working...\n", worker_id);
        Sleep(200);
    }
    
    printf("Worker %d: Stopped\n", worker_id);
    
    return 0;
}

void coordinateWorkers() {
    // Create worker threads
    HANDLE threads[3];
    int worker_ids[3] = {1, 2, 3};
    
    for (int i = 0; i < 3; i++) {
        threads[i] = CreateThread(NULL, 0, eventWorkerThread, &worker_ids[i], 0, NULL);
    }
    
    Sleep(1000); // Let threads start and wait
    
    printf("Signaling start event\n");
    SetEvent(start_event); // Start all workers
    
    Sleep(2000); // Let workers work for a while
    
    printf("Signaling stop event\n");
    SetEvent(stop_event); // Stop all workers
    
    // Wait for completion
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
}
```

### Event Types
- **Manual-Reset**: Event remains signaled until manually reset
- **Auto-Reset**: Event automatically resets after one thread is released
- **Signaling**: Used for one-way notifications
- **Synchronization**: Coordinates thread startup/shutdown

## 🔄 Producer-Consumer Pattern

### Buffer Implementation
```c
typedef struct {
    int buffer[MAX_PRODUCER_CONSUMER_ITEMS];
    int head;
    int tail;
    int count;
    CRITICAL_SECTION cs;
    HANDLE not_empty;
    HANDLE not_full;
} ProducerConsumerBuffer;

void initProducerConsumerBuffer() {
    pc_buffer.head = 0;
    pc_buffer.tail = 0;
    pc_buffer.count = 0;
    InitializeCriticalSection(&pc_buffer.cs);
    pc_buffer.not_empty = CreateSemaphore(NULL, 0, MAX_PRODUCER_CONSUMER_ITEMS, NULL);
    pc_buffer.not_full = CreateSemaphore(NULL, MAX_PRODUCER_CONSUMER_ITEMS, MAX_PRODUCER_CONSUMER_ITEMS, NULL);
}
```

### Producer Thread
```c
DWORD WINAPI producerThread(LPVOID param) {
    int producer_id = *(int*)param;
    
    for (int i = 0; i < 10; i++) {
        int item = producer_id * 100 + i;
        
        // Wait if buffer is full
        WaitForSingleObject(pc_buffer.not_full, INFINITE);
        
        // Enter critical section
        EnterCriticalSection(&pc_buffer.cs);
        
        // Add item to buffer
        pc_buffer.buffer[pc_buffer.tail] = item;
        pc_buffer.tail = (pc_buffer.tail + 1) % MAX_PRODUCER_CONSUMER_ITEMS;
        pc_buffer.count++;
        
        printf("Producer %d: Produced item %d\n", producer_id, item);
        
        LeaveCriticalSection(&pc_buffer.cs);
        
        // Signal that buffer is not empty
        ReleaseSemaphore(pc_buffer.not_empty, 1, NULL);
        
        Sleep(50 + (producer_id * 20)); // Simulate production time
    }
    
    return 0;
}
```

### Consumer Thread
```c
DWORD WINAPI consumerThread(LPVOID param) {
    int consumer_id = *(int*)param;
    
    for (int i = 0; i < 10; i++) {
        // Wait if buffer is empty
        WaitForSingleObject(pc_buffer.not_empty, INFINITE);
        
        // Enter critical section
        EnterCriticalSection(&pc_buffer.cs);
        
        // Remove item from buffer
        int item = pc_buffer.buffer[pc_buffer.head];
        pc_buffer.head = (pc_buffer.head + 1) % MAX_PRODUCER_CONSUMER_ITEMS;
        pc_buffer.count--;
        
        printf("Consumer %d: Consumed item %d\n", consumer_id, item);
        
        LeaveCriticalSection(&pc_buffer.cs);
        
        // Signal that buffer is not full
        ReleaseSemaphore(pc_buffer.not_full, 1, NULL);
        
        Sleep(80 + (consumer_id * 15)); // Simulate consumption time
    }
    
    return 0;
}
```

**Producer-Consumer Characteristics**:
- **Bounded Buffer**: Fixed-size buffer for items
- **Synchronization**: Producers wait when full, consumers wait when empty
- **Thread Safety**: Critical sections protect buffer operations
- **Efficiency**: Minimizes thread blocking with proper signaling

## 👥 Readers-Writers Problem

### Shared Resource Structure
```c
typedef struct {
    int value;
    int readers_count;
    int writers_waiting;
    CRITICAL_SECTION cs;
    HANDLE readers_sem;
    HANDLE writers_sem;
} SharedResource;
```

### Reader Implementation
```c
DWORD WINAPI readerThread(LPVOID param) {
    int reader_id = *(int*)param;
    
    for (int i = 0; i < 5; i++) {
        // Wait for writers to finish
        WaitForSingleObject(shared_resource.writers_sem, INFINITE);
        
        // Enter critical section
        EnterCriticalSection(&shared_resource.cs);
        shared_resource.readers_count++;
        
        // If first reader, block other writers
        if (shared_resource.readers_count == 1) {
            WaitForSingleObject(shared_resource.readers_sem, INFINITE);
        }
        
        LeaveCriticalSection(&shared_resource.cs);
        ReleaseSemaphore(shared_resource.writers_sem, 1, NULL);
        
        // Read shared resource
        printf("Reader %d: Read value = %d\n", reader_id, shared_resource.value);
        Sleep(100);
        
        // Leave critical section
        EnterCriticalSection(&shared_resource.cs);
        shared_resource.readers_count--;
        
        // If last reader, allow writers
        if (shared_resource.readers_count == 0) {
            ReleaseSemaphore(shared_resource.readers_sem, 1, NULL);
        }
        
        LeaveCriticalSection(&shared_resource.cs);
    }
    
    return 0;
}
```

### Writer Implementation
```c
DWORD WINAPI writerThread(LPVOID param) {
    int writer_id = *(int*)param;
    
    for (int i = 0; i < 5; i++) {
        // Wait for access to write
        WaitForSingleObject(shared_resource.writers_sem, INFINITE);
        WaitForSingleObject(shared_resource.readers_sem, INFINITE);
        
        // Write to shared resource
        shared_resource.value = writer_id * 10 + i;
        printf("Writer %d: Wrote value = %d\n", writer_id, shared_resource.value);
        Sleep(200);
        
        // Release access
        ReleaseSemaphore(shared_resource.readers_sem, 1, NULL);
        ReleaseSemaphore(shared_resource.writers_sem, 1, NULL);
    }
    
    return 0;
}
```

**Readers-Writers Properties**:
- **Multiple Readers**: Allows concurrent read access
- **Exclusive Writers**: Only one writer at a time
- **No Starvation**: Prevents either readers or writers from starving
- **Priority**: Can be configured for read-preferring or write-preferring

## 🏊 Thread Pool Implementation

### Thread Pool Structure
```c
typedef struct {
    HANDLE threads[MAX_THREADS];
    HANDLE work_queue_sem;
    HANDLE shutdown_event;
    CRITICAL_SECTION queue_cs;
    
    typedef struct WorkItem {
        void (*function)(void*);
        void* parameter;
        struct WorkItem* next;
    } WorkItem;
    
    WorkItem* work_queue_head;
    WorkItem* work_queue_tail;
    int thread_count;
} ThreadPool;
```

### Worker Thread Function
```c
DWORD WINAPI threadPoolWorker(LPVOID param) {
    while (TRUE) {
        // Wait for work or shutdown
        HANDLE wait_handles[2] = {thread_pool.work_queue_sem, thread_pool.shutdown_event};
        DWORD result = WaitForMultipleObjects(2, wait_handles, FALSE, INFINITE);
        
        if (result == WAIT_OBJECT_0 + 1) {
            // Shutdown event signaled
            break;
        }
        
        if (result == WAIT_OBJECT_0) {
            // Work available
            EnterCriticalSection(&thread_pool.queue_cs);
            
            WorkItem* work_item = thread_pool.work_queue_head;
            if (work_item) {
                thread_pool.work_queue_head = work_item->next;
                if (thread_pool.work_queue_head == NULL) {
                    thread_pool.work_queue_tail = NULL;
                }
            }
            
            LeaveCriticalSection(&thread_pool.queue_cs);
            
            if (work_item) {
                // Execute work
                work_item->function(work_item->parameter);
                free(work_item);
            }
        }
    }
    
    return 0;
}
```

### Thread Pool Management
```c
void initThreadPool(int thread_count) {
    thread_pool.thread_count = thread_count;
    thread_pool.work_queue_head = NULL;
    thread_pool.work_queue_tail = NULL;
    
    InitializeCriticalSection(&thread_pool.queue_cs);
    thread_pool.work_queue_sem = CreateSemaphore(NULL, 0, MAX_THREADS, NULL);
    thread_pool.shutdown_event = CreateEvent(NULL, TRUE, FALSE, NULL);
    
    // Create worker threads
    for (int i = 0; i < thread_count; i++) {
        thread_pool.threads[i] = CreateThread(NULL, 0, threadPoolWorker, NULL, 0, NULL);
    }
}

void addWorkToThreadPool(void (*function)(void*), void* parameter) {
    WorkItem* work_item = (WorkItem*)malloc(sizeof(WorkItem));
    work_item->function = function;
    work_item->parameter = parameter;
    work_item->next = NULL;
    
    EnterCriticalSection(&thread_pool.queue_cs);
    
    if (thread_pool.work_queue_tail) {
        thread_pool.work_queue_tail->next = work_item;
    } else {
        thread_pool.work_queue_head = work_item;
    }
    thread_pool.work_queue_tail = work_item;
    
    LeaveCriticalSection(&thread_pool.queue_cs);
    
    // Signal that work is available
    ReleaseSemaphore(thread_pool.work_queue_sem, 1, NULL);
}
```

**Thread Pool Benefits**:
- **Resource Efficiency**: Reuses threads instead of creating/destroying
- **Performance**: Reduces thread creation overhead
- **Scalability**: Controls maximum concurrent threads
- **Load Balancing**: Distributes work among available threads

## ⚡ Atomic Operations

### Interlocked Functions
```c
volatile LONG atomic_counter = 0;

DWORD WINAPI atomicIncrementThread(LPVOID param) {
    int thread_id = *(int*)param;
    
    for (int i = 0; i < 1000; i++) {
        InterlockedIncrement(&atomic_counter);
        if (i % 100 == 0) {
            printf("Thread %d: Counter = %ld\n", thread_id, atomic_counter);
        }
    }
    
    return 0;
}
```

### Atomic Operations Available
```c
// Basic atomic operations
LONG InterlockedIncrement(LONG* Addend);
LONG InterlockedDecrement(LONG* Addend);
LONG InterlockedExchange(LONG* Target, LONG Value);
LONG InterlockedCompareExchange(LONG* Destination, LONG Exchange, LONG Comperand);

// 64-bit operations (on x64)
LONG64 InterlockedIncrement64(LONG64* Addend);
LONG64 InterlockedDecrement64(LONG64* Addend);
LONG64 InterlockedExchange64(LONG64* Target, LONG64 Value);

// Pointer operations
PVOID InterlockedExchangePointer(PVOID* Target, PVOID Value);
PVOID InterlockedCompareExchangePointer(PVOID* Destination, PVOID Exchange, PVOID Comperand);
```

**Atomic Operations Characteristics**:
- **Lock-Free**: No mutex or critical section needed
- **Hardware Support**: Uses CPU atomic instructions
- **Performance**: Very fast for simple operations
- **Limited**: Only supports basic operations

## 🔄 Condition Variables

### Condition Variable Implementation
```c
typedef struct {
    CRITICAL_SECTION cs;
    CONDITION_VARIABLE cv;
    int data_ready;
    int shared_data;
} ConditionVariableDemo;

void initConditionVariableDemo() {
    InitializeCriticalSection(&cv_demo.cs);
    InitializeConditionVariable(&cv_demo.cv);
    cv_demo.data_ready = 0;
    cv_demo.shared_data = 0;
}

DWORD WINAPI cvProducerThread(LPVOID param) {
    int producer_id = *(int*)param;
    
    for (int i = 0; i < 5; i++) {
        EnterCriticalSection(&cv_demo.cs);
        
        // Wait for consumer to process previous data
        while (cv_demo.data_ready) {
            SleepConditionVariableCS(&cv_demo.cv, &cv_demo.cs, INFINITE);
        }
        
        // Produce data
        cv_demo.shared_data = producer_id * 100 + i;
        cv_demo.data_ready = 1;
        
        printf("Producer %d: Produced data %d\n", producer_id, cv_demo.shared_data);
        
        LeaveCriticalSection(&cv_demo.cs);
        
        // Signal consumer
        WakeConditionVariable(&cv_demo.cv);
        
        Sleep(100);
    }
    
    return 0;
}
```

### Condition Variable Operations
```c
// Initialize condition variable
void InitializeConditionVariable(PCONDITION_VARIABLE ConditionVariable);

// Wait for condition variable
BOOL SleepConditionVariableCS(PCONDITION_VARIABLE ConditionVariable, PCRITICAL_SECTION CriticalSection, DWORD dwMilliseconds);

// Wake one thread waiting on condition variable
void WakeConditionVariable(PCONDITION_VARIABLE ConditionVariable);

// Wake all threads waiting on condition variable
void WakeAllConditionVariable(PCONDITION_VARIABLE ConditionVariable);
```

**Condition Variable Features**:
- **Efficient**: Avoids busy waiting
- **Flexible**: Can wait with timeout
- **Coordinated**: Works with critical sections
- **Spurious Wakeups**: Must recheck condition after waking

## 💀 Deadlock Prevention

### Deadlock Conditions
1. **Mutual Exclusion**: Resources cannot be shared
2. **Hold and Wait**: Thread holds resource while waiting for another
3. **No Preemption**: Resources cannot be forcibly taken
4. **Circular Wait**: Circular chain of waiting threads

### Prevention Strategies
```c
// Strategy 1: Consistent Resource Ordering
void safeResourceAccess() {
    // Always acquire resources in the same order
    WaitForSingleObject(resource1_mutex, INFINITE);
    WaitForSingleObject(resource2_mutex, INFINITE);
    
    // Use both resources
    doWork();
    
    // Release in reverse order
    ReleaseMutex(resource2_mutex);
    ReleaseMutex(resource1_mutex);
}

// Strategy 2: Try-Lock with Timeout
BOOL tryLockWithTimeout() {
    DWORD result1 = WaitForSingleObject(resource1_mutex, 1000);
    if (result1 != WAIT_OBJECT_0) return FALSE;
    
    DWORD result2 = WaitForSingleObject(resource2_mutex, 1000);
    if (result2 != WAIT_OBJECT_0) {
        ReleaseMutex(resource1_mutex);
        return FALSE;
    }
    
    // Use resources
    doWork();
    
    // Release
    ReleaseMutex(resource2_mutex);
    ReleaseMutex(resource1_mutex);
    return TRUE;
}
```

### Deadlock Detection
```c
BOOL detectDeadlock() {
    // Check for circular wait conditions
    // This is a simplified example
    HANDLE resources[2] = {resource1_mutex, resource2_mutex};
    
    for (int i = 0; i < 2; i++) {
        DWORD result = WaitForSingleObject(resources[i], 0);
        if (result == WAIT_TIMEOUT) {
            printf("Potential deadlock detected on resource %d\n", i);
            return TRUE;
        }
        ReleaseMutex(resources[i]);
    }
    
    return FALSE;
}
```

## 📊 Performance Considerations

### Thread Creation Overhead
```c
void measureThreadCreationOverhead() {
    PerformanceTimer timer;
    const int iterations = 1000;
    
    // Measure thread creation time
    startTimer(&timer);
    for (int i = 0; i < iterations; i++) {
        HANDLE thread = CreateThread(NULL, 0, dummyThreadFunction, NULL, 0, NULL);
        WaitForSingleObject(thread, INFINITE);
        CloseHandle(thread);
    }
    double thread_time = stopTimer(&timer);
    
    printf("Thread creation overhead: %.6f seconds per thread\n", thread_time / iterations);
}
```

### Synchronization Overhead
```c
void measureSynchronizationOverhead() {
    PerformanceTimer timer;
    const int iterations = 1000000;
    
    // Measure critical section overhead
    startTimer(&timer);
    for (int i = 0; i < iterations; i++) {
        EnterCriticalSection(&cs);
        // Minimal work
        LeaveCriticalSection(&cs);
    }
    double cs_time = stopTimer(&timer);
    
    // Measure mutex overhead
    startTimer(&timer);
    for (int i = 0; i < iterations; i++) {
        WaitForSingleObject(mutex, INFINITE);
        ReleaseMutex(mutex);
    }
    double mutex_time = stopTimer(&timer);
    
    printf("Critical section overhead: %.9f seconds per operation\n", cs_time / iterations);
    printf("Mutex overhead: %.9f seconds per operation\n", mutex_time / iterations);
}
```

### Performance Optimization Tips
```c
// 1. Minimize lock contention
void minimizeContention() {
    // Use fine-grained locking
    EnterCriticalSection(&data_cs);
    // Only access data that needs protection
    LeaveCriticalSection(&data_cs);
    
    // Use lock-free algorithms when possible
    InterlockedIncrement(&shared_counter);
}

// 2. Reduce lock hold time
void reduceLockHoldTime() {
    // Prepare data outside lock
    int processed_data = prepareData();
    
    EnterCriticalSection(&cs);
    shared_data = processed_data; // Minimal work inside lock
    LeaveCriticalSection(&cs);
}

// 3. Use appropriate synchronization primitive
void chooseRightPrimitive() {
    // For intra-process, short-duration: Critical Section
    EnterCriticalSection(&cs);
    shortWork();
    LeaveCriticalSection(&cs);
    
    // For cross-process or long-duration: Mutex
    WaitForSingleObject(mutex, INFINITE);
    longWork();
    ReleaseMutex(mutex);
}
```

## 🔧 Best Practices

### 1. Thread Safety Guidelines
```c
// DO: Protect shared data
void safeSharedAccess() {
    EnterCriticalSection(&shared_data_cs);
    shared_value++;
    LeaveCriticalSection(&shared_data_cs);
}

// DON'T: Access shared data without protection
void unsafeSharedAccess() {
    shared_value++; // Race condition!
}
```

### 2. Resource Management
```c
// DO: Always release resources
void properResourceManagement() {
    HANDLE mutex = CreateMutex(NULL, FALSE, NULL);
    if (mutex) {
        WaitForSingleObject(mutex, INFINITE);
        // Use resource
        ReleaseMutex(mutex);
        CloseHandle(mutex);
    }
}

// DON'T: Forget to release
void resourceLeak() {
    HANDLE mutex = CreateMutex(NULL, FALSE, NULL);
    WaitForSingleObject(mutex, INFINITE);
    // Forgot to release and close!
}
```

### 3. Error Handling
```c
// DO: Check return values
BOOL safeThreadOperation() {
    HANDLE thread = CreateThread(NULL, 0, threadFunction, NULL, 0, NULL);
    if (!thread) {
        printf("Failed to create thread: %d\n", GetLastError());
        return FALSE;
    }
    
    WaitForSingleObject(thread, INFINITE);
    CloseHandle(thread);
    return TRUE;
}
```

### 4. Thread Design Patterns
```c
// Thread-per-connection pattern
void handleConnection(SOCKET socket) {
    HANDLE thread = CreateThread(NULL, 0, connectionHandlerThread, (LPVOID)socket, 0, NULL);
    if (thread) {
        CloseHandle(thread); // Thread will clean up itself
    }
}

// Thread pool pattern
void processWorkItem(WorkItem* item) {
    addWorkToThreadPool(processFunction, item);
}
```

### 5. Debugging Multithreaded Code
```c
// Use thread-local storage for debugging
__declspec(thread) int thread_id;

void debugLog(const char* message) {
    printf("[Thread %d] %s\n", thread_id, message);
}

// Use synchronization for debugging output
CRITICAL_SECTION debug_cs;

void safeDebugLog(const char* message) {
    EnterCriticalSection(&debug_cs);
    printf("[%d] %s\n", GetCurrentThreadId(), message);
    LeaveCriticalSection(&debug_cs);
}
```

## ⚠️ Common Pitfalls

### 1. Race Conditions
```c
// Wrong: Non-atomic increment
int counter = 0;
void incrementCounter() {
    counter++; // Race condition!
}

// Right: Atomic increment
volatile LONG atomic_counter = 0;
void incrementCounterSafe() {
    InterlockedIncrement(&atomic_counter);
}
```

### 2. Deadlocks
```c
// Wrong: Inconsistent resource ordering
void thread1() {
    WaitForSingleObject(resource1, INFINITE);
    WaitForSingleObject(resource2, INFINITE);
    // Work
    ReleaseMutex(resource2);
    ReleaseMutex(resource1);
}

void thread2() {
    WaitForSingleObject(resource2, INFINITE);
    WaitForSingleObject(resource1, INFINITE);
    // Work
    ReleaseMutex(resource1);
    ReleaseMutex(resource2);
}

// Right: Consistent ordering
void bothThreads() {
    WaitForSingleObject(resource1, INFINITE);
    WaitForSingleObject(resource2, INFINITE);
    // Work
    ReleaseMutex(resource2);
    ReleaseMutex(resource1);
}
```

### 3. Priority Inversion
```c
// Wrong: Low-priority thread holding high-priority resource
void lowPriorityThread() {
    WaitForSingleObject(resource, INFINITE);
    doLongOperation(); // Blocks high-priority thread
    ReleaseMutex(resource);
}

// Right: Minimize critical section time
void lowPriorityThreadSafe() {
    // Prepare data outside critical section
    Data data = prepareData();
    
    WaitForSingleObject(resource, INFINITE);
    shared_data = data; // Minimal work inside
    ReleaseMutex(resource);
    
    doLongOperation(); // Outside critical section
}
```

### 4. Resource Leaks
```c
// Wrong: Forgetting to close handles
void leakResources() {
    for (int i = 0; i < 1000; i++) {
        CreateThread(NULL, 0, workerThread, NULL, 0, NULL); // Leak!
    }
}

// Right: Proper cleanup
void noResourceLeaks() {
    HANDLE threads[1000];
    
    for (int i = 0; i < 1000; i++) {
        threads[i] = CreateThread(NULL, 0, workerThread, NULL, 0, NULL);
    }
    
    WaitForMultipleObjects(1000, threads, TRUE, INFINITE);
    
    for (int i = 0; i < 1000; i++) {
        CloseHandle(threads[i]);
    }
}
```

## 🔧 Real-World Applications

### 1. Web Server
```c
void handleClientConnection(SOCKET client_socket) {
    // Create thread to handle each client
    HANDLE thread = CreateThread(NULL, 0, clientHandlerThread, 
                                  (LPVOID)client_socket, 0, NULL);
    CloseHandle(thread); // Thread manages its own cleanup
}
```

### 2. Image Processing
```c
void processImageParallel(Image* image) {
    // Divide image into sections
    int num_threads = 4;
    int height_per_thread = image->height / num_threads;
    
    HANDLE threads[num_threads];
    ImageSection sections[num_threads];
    
    for (int i = 0; i < num_threads; i++) {
        sections[i].image = image;
        sections[i].start_y = i * height_per_thread;
        sections[i].end_y = (i + 1) * height_per_thread;
        
        threads[i] = CreateThread(NULL, 0, imageProcessingThread, 
                                  &sections[i], 0, NULL);
    }
    
    WaitForMultipleObjects(num_threads, threads, TRUE, INFINITE);
    
    for (int i = 0; i < num_threads; i++) {
        CloseHandle(threads[i]);
    }
}
```

### 3. Data Processing Pipeline
```c
void processDataPipeline() {
    // Create pipeline stages
    Stage stages[3];
    stages[0].function = readData;
    stages[1].function = processData;
    stages[2].function = writeData;
    
    // Connect stages with buffers
    connectStages(&stages[0], &stages[1]);
    connectStages(&stages[1], &stages[2]);
    
    // Start pipeline threads
    for (int i = 0; i < 3; i++) {
        stages[i].thread = CreateThread(NULL, 0, stageWorkerThread, 
                                       &stages[i], 0, NULL);
    }
    
    // Wait for completion
    WaitForMultipleObjects(3, stage_threads, TRUE, INFINITE);
}
```

### 4. Real-time Systems
```c
void realTimeProcessing() {
    // Use high-priority threads for time-critical tasks
    HANDLE high_priority_thread = CreateThread(NULL, 0, timeCriticalTask, 
                                              NULL, 0, NULL);
    SetThreadPriority(high_priority_thread, THREAD_PRIORITY_TIME_CRITICAL);
    
    // Use normal priority for background tasks
    HANDLE background_thread = CreateThread(NULL, 0, backgroundTask, 
                                            NULL, 0, NULL);
    SetThreadPriority(background_thread, THREAD_PRIORITY_NORMAL);
}
```

## 🎓 Learning Path

### 1. Start with Basic Concepts
- Thread creation and termination
- Basic synchronization with critical sections
- Simple producer-consumer patterns

### 2. Advanced Synchronization
- Mutexes and semaphores
- Event-based coordination
- Condition variables

### 3. Complex Patterns
- Thread pools
- Readers-writers problem
- Deadlock prevention

### 4. Performance Optimization
- Atomic operations
- Lock-free algorithms
- Performance measurement

### 5. Real-World Applications
- Network servers
- Parallel processing
- Real-time systems

## 📚 Further Reading

### Books
- "Programming with POSIX Threads" by David R. Butenhof
- "C++ Concurrency in Action" by Anthony Williams
- "The Art of Multiprocessor Programming" by Maurice Herlihy

### Topics
- Memory models and visibility
- Lock-free and wait-free algorithms
- Parallel algorithms and patterns
- Real-time scheduling
- Distributed systems concepts

Multithreading in C provides powerful capabilities for concurrent programming. Master these concepts to build efficient, responsive, and scalable applications that take full advantage of modern multi-core processors!
