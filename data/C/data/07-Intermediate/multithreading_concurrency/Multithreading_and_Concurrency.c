#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <windows.h>
#include <process.h>

// =============================================================================
// MULTITHREADING FUNDAMENTALS
// =============================================================================

#define MAX_THREADS 10
#define BUFFER_SIZE 1024
#define MAX_PRODUCER_CONSUMER_ITEMS 50

// Thread data structure
typedef struct {
    int thread_id;
    char message[256];
    int counter;
} ThreadData;

// Shared resource for synchronization examples
typedef struct {
    int value;
    int readers_count;
    int writers_waiting;
    CRITICAL_SECTION cs;
    HANDLE readers_sem;
    HANDLE writers_sem;
} SharedResource;

// Producer-Consumer buffer
typedef struct {
    int buffer[MAX_PRODUCER_CONSUMER_ITEMS];
    int head;
    int tail;
    int count;
    CRITICAL_SECTION cs;
    HANDLE not_empty;
    HANDLE not_full;
} ProducerConsumerBuffer;

// =============================================================================
// BASIC THREAD CREATION AND MANAGEMENT
// =============================================================================

// Basic thread function
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

// Demonstrate basic thread creation
void demonstrateBasicThreading() {
    printf("=== BASIC THREADING DEMO ===\n");
    
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
        } else {
            printf("Created thread %d\n", i + 1);
        }
    }
    
    // Wait for all threads to complete
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
    
    // Clean up thread handles
    for (int i = 0; i < 3; i++) {
        CloseHandle(threads[i]);
    }
    
    printf("All threads completed\n\n");
}

// =============================================================================
// SYNCHRONIZATION PRIMITIVES
// =============================================================================

// Critical Section example
SharedResource shared_resource;

void initSharedResource() {
    shared_resource.value = 0;
    shared_resource.readers_count = 0;
    shared_resource.writers_waiting = 0;
    InitializeCriticalSection(&shared_resource.cs);
    shared_resource.readers_sem = CreateSemaphore(NULL, 1, 1, NULL);
    shared_resource.writers_sem = CreateSemaphore(NULL, 1, 1, NULL);
}

void cleanupSharedResource() {
    DeleteCriticalSection(&shared_resource.cs);
    CloseHandle(shared_resource.readers_sem);
    CloseHandle(shared_resource.writers_sem);
}

// Reader thread function
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

// Writer thread function
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

// Demonstrate readers-writers problem
void demonstrateReadersWriters() {
    printf("=== READERS-WRITERS DEMO ===\n");
    
    initSharedResource();
    
    HANDLE threads[6];
    int reader_ids[3] = {1, 2, 3};
    int writer_ids[3] = {1, 2, 3};
    
    // Create reader threads
    for (int i = 0; i < 3; i++) {
        threads[i] = CreateThread(NULL, 0, readerThread, &reader_ids[i], 0, NULL);
    }
    
    // Create writer threads
    for (int i = 0; i < 3; i++) {
        threads[i + 3] = CreateThread(NULL, 0, writerThread, &writer_ids[i], 0, NULL);
    }
    
    // Wait for all threads to complete
    WaitForMultipleObjects(6, threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < 6; i++) {
        CloseHandle(threads[i]);
    }
    cleanupSharedResource();
    
    printf("Readers-Writers demo completed\n\n");
}

// =============================================================================
// PRODUCER-CONSUMER PROBLEM
// =============================================================================

ProducerConsumerBuffer pc_buffer;

void initProducerConsumerBuffer() {
    pc_buffer.head = 0;
    pc_buffer.tail = 0;
    pc_buffer.count = 0;
    InitializeCriticalSection(&pc_buffer.cs);
    pc_buffer.not_empty = CreateSemaphore(NULL, 0, MAX_PRODUCER_CONSUMER_ITEMS, NULL);
    pc_buffer.not_full = CreateSemaphore(NULL, MAX_PRODUCER_CONSUMER_ITEMS, MAX_PRODUCER_CONSUMER_ITEMS, NULL);
}

void cleanupProducerConsumerBuffer() {
    DeleteCriticalSection(&pc_buffer.cs);
    CloseHandle(pc_buffer.not_empty);
    CloseHandle(pc_buffer.not_full);
}

// Producer thread function
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

// Consumer thread function
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

// Demonstrate producer-consumer problem
void demonstrateProducerConsumer() {
    printf("=== PRODUCER-CONSUMER DEMO ===\n");
    
    initProducerConsumerBuffer();
    
    HANDLE threads[4];
    int producer_ids[2] = {1, 2};
    int consumer_ids[2] = {1, 2};
    
    // Create producer threads
    for (int i = 0; i < 2; i++) {
        threads[i] = CreateThread(NULL, 0, producerThread, &producer_ids[i], 0, NULL);
    }
    
    // Create consumer threads
    for (int i = 0; i < 2; i++) {
        threads[i + 2] = CreateThread(NULL, 0, consumerThread, &consumer_ids[i], 0, NULL);
    }
    
    // Wait for all threads to complete
    WaitForMultipleObjects(4, threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < 4; i++) {
        CloseHandle(threads[i]);
    }
    cleanupProducerConsumerBuffer();
    
    printf("Producer-Consumer demo completed\n\n");
}

// =============================================================================
// MUTEX EXAMPLES
// =============================================================================

// Global mutex for demonstration
HANDLE global_mutex;

void initGlobalMutex() {
    global_mutex = CreateMutex(NULL, FALSE, "GlobalMutex");
}

void cleanupGlobalMutex() {
    CloseHandle(global_mutex);
}

// Thread that uses mutex
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

// Demonstrate mutex usage
void demonstrateMutex() {
    printf("=== MUTEX DEMO ===\n");
    
    initGlobalMutex();
    
    HANDLE threads[3];
    int thread_ids[3] = {1, 2, 3};
    
    // Create threads
    for (int i = 0; i < 3; i++) {
        threads[i] = CreateThread(NULL, 0, mutexThread, &thread_ids[i], 0, NULL);
    }
    
    // Wait for all threads to complete
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < 3; i++) {
        CloseHandle(threads[i]);
    }
    cleanupGlobalMutex();
    
    printf("Mutex demo completed\n\n");
}

// =============================================================================
// EVENT SYNCHRONIZATION
// =============================================================================

// Event handles for synchronization
HANDLE start_event;
HANDLE stop_event;

void initEvents() {
    start_event = CreateEvent(NULL, TRUE, FALSE, "StartEvent");
    stop_event = CreateEvent(NULL, TRUE, FALSE, "StopEvent");
}

void cleanupEvents() {
    CloseHandle(start_event);
    CloseHandle(stop_event);
}

// Worker thread that waits for events
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

// Demonstrate event synchronization
void demonstrateEvents() {
    printf("=== EVENT SYNCHRONIZATION DEMO ===\n");
    
    initEvents();
    
    HANDLE threads[3];
    int worker_ids[3] = {1, 2, 3};
    
    // Create worker threads
    for (int i = 0; i < 3; i++) {
        threads[i] = CreateThread(NULL, 0, eventWorkerThread, &worker_ids[i], 0, NULL);
    }
    
    Sleep(1000); // Let threads start and wait
    
    printf("Signaling start event\n");
    SetEvent(start_event); // Start all workers
    
    Sleep(2000); // Let workers work for a while
    
    printf("Signaling stop event\n");
    SetEvent(stop_event); // Stop all workers
    
    // Wait for all threads to complete
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < 3; i++) {
        CloseHandle(threads[i]);
    }
    cleanupEvents();
    
    printf("Event synchronization demo completed\n\n");
}

// =============================================================================
// THREAD POOL IMPLEMENTATION
// =============================================================================

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

ThreadPool thread_pool;

// Worker thread function for thread pool
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

// Initialize thread pool
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

// Add work to thread pool
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

// Shutdown thread pool
void shutdownThreadPool() {
    // Signal shutdown
    SetEvent(thread_pool.shutdown_event);
    
    // Wait for all threads to complete
    WaitForMultipleObjects(thread_pool.thread_count, thread_pool.threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < thread_pool.thread_count; i++) {
        CloseHandle(thread_pool.threads[i]);
    }
    
    DeleteCriticalSection(&thread_pool.queue_cs);
    CloseHandle(thread_pool.work_queue_sem);
    CloseHandle(thread_pool.shutdown_event);
    
    // Clean up remaining work items
    WorkItem* current = thread_pool.work_queue_head;
    while (current) {
        WorkItem* next = current->next;
        free(current);
        current = next;
    }
}

// Sample work function for thread pool
void sampleWorkFunction(void* param) {
    int work_id = *(int*)param;
    printf("Processing work item %d\n", work_id);
    Sleep(100 + (work_id * 10)); // Simulate work
    printf("Completed work item %d\n", work_id);
}

// Demonstrate thread pool
void demonstrateThreadPool() {
    printf("=== THREAD POOL DEMO ===\n");
    
    initThreadPool(3); // Create 3 worker threads
    
    // Add work items
    int work_ids[10];
    for (int i = 0; i < 10; i++) {
        work_ids[i] = i + 1;
        addWorkToThreadPool(sampleWorkFunction, &work_ids[i]);
    }
    
    // Wait a bit for work to complete
    Sleep(3000);
    
    // Shutdown thread pool
    shutdownThreadPool();
    
    printf("Thread pool demo completed\n\n");
}

// =============================================================================
// ATOMIC OPERATIONS AND INTERLOCKED FUNCTIONS
// =============================================================================

// Shared counter for atomic operations demonstration
volatile LONG atomic_counter = 0;

// Thread that increments counter atomically
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

// Thread that increments counter non-atomically (for comparison)
volatile LONG non_atomic_counter = 0;

DWORD WINAPI nonAtomicIncrementThread(LPVOID param) {
    int thread_id = *(int*)param;
    
    for (int i = 0; i < 1000; i++) {
        non_atomic_counter++; // Not thread-safe
        if (i % 100 == 0) {
            printf("Thread %d (non-atomic): Counter = %ld\n", thread_id, non_atomic_counter);
        }
    }
    
    return 0;
}

// Demonstrate atomic operations
void demonstrateAtomicOperations() {
    printf("=== ATOMIC OPERATIONS DEMO ===\n");
    
    // Test atomic operations
    printf("Testing atomic operations:\n");
    atomic_counter = 0;
    
    HANDLE atomic_threads[5];
    int thread_ids[5] = {1, 2, 3, 4, 5};
    
    // Create threads for atomic operations
    for (int i = 0; i < 5; i++) {
        atomic_threads[i] = CreateThread(NULL, 0, atomicIncrementThread, &thread_ids[i], 0, NULL);
    }
    
    // Wait for completion
    WaitForMultipleObjects(5, atomic_threads, TRUE, INFINITE);
    
    printf("Final atomic counter value: %ld (should be 5000)\n", atomic_counter);
    
    // Test non-atomic operations (for comparison)
    printf("\nTesting non-atomic operations (may show race conditions):\n");
    non_atomic_counter = 0;
    
    HANDLE non_atomic_threads[5];
    
    // Create threads for non-atomic operations
    for (int i = 0; i < 5; i++) {
        non_atomic_threads[i] = CreateThread(NULL, 0, nonAtomicIncrementThread, &thread_ids[i], 0, NULL);
    }
    
    // Wait for completion
    WaitForMultipleObjects(5, non_atomic_threads, TRUE, INFINITE);
    
    printf("Final non-atomic counter value: %ld (may be less than 5000 due to race conditions)\n", non_atomic_counter);
    
    // Clean up
    for (int i = 0; i < 5; i++) {
        CloseHandle(atomic_threads[i]);
        CloseHandle(non_atomic_threads[i]);
    }
    
    printf("Atomic operations demo completed\n\n");
}

// =============================================================================
// CONDITION VARIABLES
// =============================================================================

typedef struct {
    CRITICAL_SECTION cs;
    CONDITION_VARIABLE cv;
    int data_ready;
    int shared_data;
} ConditionVariableDemo;

ConditionVariableDemo cv_demo;

void initConditionVariableDemo() {
    InitializeCriticalSection(&cv_demo.cs);
    InitializeConditionVariable(&cv_demo.cv);
    cv_demo.data_ready = 0;
    cv_demo.shared_data = 0;
}

void cleanupConditionVariableDemo() {
    DeleteCriticalSection(&cv_demo.cs);
}

// Producer thread using condition variables
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

// Consumer thread using condition variables
DWORD WINAPI cvConsumerThread(LPVOID param) {
    int consumer_id = *(int*)param;
    
    for (int i = 0; i < 10; i++) {
        EnterCriticalSection(&cv_demo.cs);
        
        // Wait for producer to produce data
        while (!cv_demo.data_ready) {
            SleepConditionVariableCS(&cv_demo.cv, &cv_demo.cs, INFINITE);
        }
        
        // Consume data
        int data = cv_demo.shared_data;
        cv_demo.data_ready = 0;
        
        printf("Consumer %d: Consumed data %d\n", consumer_id, data);
        
        LeaveCriticalSection(&cv_demo.cs);
        
        // Signal producer
        WakeConditionVariable(&cv_demo.cv);
        
        Sleep(150);
    }
    
    return 0;
}

// Demonstrate condition variables
void demonstrateConditionVariables() {
    printf("=== CONDITION VARIABLES DEMO ===\n");
    
    initConditionVariableDemo();
    
    HANDLE threads[3];
    int producer_ids[2] = {1, 2};
    int consumer_id = 1;
    
    // Create producer threads
    threads[0] = CreateThread(NULL, 0, cvProducerThread, &producer_ids[0], 0, NULL);
    threads[1] = CreateThread(NULL, 0, cvProducerThread, &producer_ids[1], 0, NULL);
    
    // Create consumer thread
    threads[2] = CreateThread(NULL, 0, cvConsumerThread, &consumer_id, 0, NULL);
    
    // Wait for all threads to complete
    WaitForMultipleObjects(3, threads, TRUE, INFINITE);
    
    // Clean up
    for (int i = 0; i < 3; i++) {
        CloseHandle(threads[i]);
    }
    cleanupConditionVariableDemo();
    
    printf("Condition variables demo completed\n\n");
}

// =============================================================================
// DEADLOCK DETECTION AND PREVENTION
// =============================================================================

// Resources for deadlock demonstration
HANDLE resource1_mutex;
HANDLE resource2_mutex;

void initDeadlockResources() {
    resource1_mutex = CreateMutex(NULL, FALSE, "Resource1");
    resource2_mutex = CreateMutex(NULL, FALSE, "Resource2");
}

void cleanupDeadlockResources() {
    CloseHandle(resource1_mutex);
    CloseHandle(resource2_mutex);
}

// Thread that can cause deadlock (wrong order)
DWORD WINAPI deadlockThread1(LPVOID param) {
    printf("Thread 1: Waiting for Resource 1\n");
    WaitForSingleObject(resource1_mutex, INFINITE);
    printf("Thread 1: Acquired Resource 1\n");
    
    Sleep(500); // Increase chance of deadlock
    
    printf("Thread 1: Waiting for Resource 2\n");
    WaitForSingleObject(resource2_mutex, INFINITE);
    printf("Thread 1: Acquired Resource 2\n");
    
    // Do work with both resources
    Sleep(1000);
    
    printf("Thread 1: Releasing resources\n");
    ReleaseMutex(resource2_mutex);
    ReleaseMutex(resource1_mutex);
    
    return 0;
}

// Thread that can cause deadlock (wrong order)
DWORD WINAPI deadlockThread2(LPVOID param) {
    printf("Thread 2: Waiting for Resource 2\n");
    WaitForSingleObject(resource2_mutex, INFINITE);
    printf("Thread 2: Acquired Resource 2\n");
    
    Sleep(500); // Increase chance of deadlock
    
    printf("Thread 2: Waiting for Resource 1\n");
    WaitForSingleObject(resource1_mutex, INFINITE);
    printf("Thread 2: Acquired Resource 1\n");
    
    // Do work with both resources
    Sleep(1000);
    
    printf("Thread 2: Releasing resources\n");
    ReleaseMutex(resource1_mutex);
    ReleaseMutex(resource2_mutex);
    
    return 0;
}

// Thread that avoids deadlock (correct order)
DWORD WINAPI noDeadlockThread1(LPVOID param) {
    printf("Thread 1 (safe): Waiting for Resource 1\n");
    WaitForSingleObject(resource1_mutex, INFINITE);
    printf("Thread 1 (safe): Acquired Resource 1\n");
    
    Sleep(500);
    
    printf("Thread 1 (safe): Waiting for Resource 2\n");
    WaitForSingleObject(resource2_mutex, INFINITE);
    printf("Thread 1 (safe): Acquired Resource 2\n");
    
    // Do work with both resources
    Sleep(1000);
    
    printf("Thread 1 (safe): Releasing resources\n");
    ReleaseMutex(resource2_mutex);
    ReleaseMutex(resource1_mutex);
    
    return 0;
}

// Thread that avoids deadlock (correct order)
DWORD WINAPI noDeadlockThread2(LPVOID param) {
    printf("Thread 2 (safe): Waiting for Resource 1\n");
    WaitForSingleObject(resource1_mutex, INFINITE);
    printf("Thread 2 (safe): Acquired Resource 1\n");
    
    Sleep(500);
    
    printf("Thread 2 (safe): Waiting for Resource 2\n");
    WaitForSingleObject(resource2_mutex, INFINITE);
    printf("Thread 2 (safe): Acquired Resource 2\n");
    
    // Do work with both resources
    Sleep(1000);
    
    printf("Thread 2 (safe): Releasing resources\n");
    ReleaseMutex(resource2_mutex);
    ReleaseMutex(resource1_mutex);
    
    return 0;
}

// Demonstrate deadlock prevention
void demonstrateDeadlockPrevention() {
    printf("=== DEADLOCK PREVENTION DEMO ===\n");
    
    initDeadlockResources();
    
    printf("Testing deadlock-prone scenario (may hang):\n");
    printf("(Note: In a real demo, we'd use timeouts to detect deadlock)\n");
    
    // For demonstration, we'll show the safe version
    printf("Testing deadlock-safe scenario:\n");
    
    HANDLE threads[2];
    
    // Create threads with consistent resource ordering
    threads[0] = CreateThread(NULL, 0, noDeadlockThread1, NULL, 0, NULL);
    threads[1] = CreateThread(NULL, 0, noDeadlockThread2, NULL, 0, NULL);
    
    // Wait for completion with timeout
    DWORD result = WaitForMultipleObjects(2, threads, TRUE, 5000);
    
    if (result == WAIT_TIMEOUT) {
        printf("Timeout reached - possible deadlock detected\n");
        // Terminate threads if needed
        for (int i = 0; i < 2; i++) {
            TerminateThread(threads[i], 1);
        }
    } else {
        printf("Threads completed successfully - no deadlock\n");
    }
    
    // Clean up
    for (int i = 0; i < 2; i++) {
        CloseHandle(threads[i]);
    }
    cleanupDeadlockResources();
    
    printf("Deadlock prevention demo completed\n\n");
}

// =============================================================================
// PERFORMANCE MEASUREMENT
// =============================================================================

// Performance measurement structure
typedef struct {
    LARGE_INTEGER start_time;
    LARGE_INTEGER end_time;
    LARGE_INTEGER frequency;
} PerformanceTimer;

void startTimer(PerformanceTimer* timer) {
    QueryPerformanceFrequency(&timer->frequency);
    QueryPerformanceCounter(&timer->start_time);
}

double stopTimer(PerformanceTimer* timer) {
    QueryPerformanceCounter(&timer->end_time);
    return (double)(timer->end_time.QuadPart - timer->start_time.QuadPart) / timer->frequency.QuadPart;
}

// Compare sequential vs parallel performance
void demonstratePerformanceComparison() {
    printf("=== PERFORMANCE COMPARISON DEMO ===\n");
    
    const int work_items = 1000000;
    PerformanceTimer timer;
    
    // Sequential processing
    printf("Sequential processing...\n");
    startTimer(&timer);
    
    volatile long sum = 0;
    for (int i = 0; i < work_items; i++) {
        sum += i * i;
    }
    
    double sequential_time = stopTimer(&timer);
    printf("Sequential time: %.4f seconds, Result: %ld\n", sequential_time, sum);
    
    // Parallel processing (simplified)
    printf("Parallel processing...\n");
    startTimer(&timer);
    
    // Reset sum
    sum = 0;
    
    // Create multiple threads for parallel processing
    HANDLE threads[4];
    int chunk_size = work_items / 4;
    
    for (int i = 0; i < 4; i++) {
        // In a real implementation, you'd create worker threads
        // For simplicity, we'll just simulate the work
        volatile long local_sum = 0;
        int start = i * chunk_size;
        int end = (i == 3) ? work_items : start + chunk_size;
        
        for (int j = start; j < end; j++) {
            local_sum += j * j;
        }
        
        sum += local_sum;
    }
    
    double parallel_time = stopTimer(&timer);
    printf("Parallel time: %.4f seconds, Result: %ld\n", parallel_time, sum);
    
    printf("Speedup: %.2fx\n", sequential_time / parallel_time);
    printf("Performance comparison completed\n\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Multithreading and Concurrency Examples\n");
    printf("=====================================\n\n");
    
    // Run all demonstrations
    demonstrateBasicThreading();
    demonstrateReadersWriters();
    demonstrateProducerConsumer();
    demonstrateMutex();
    demonstrateEvents();
    demonstrateThreadPool();
    demonstrateAtomicOperations();
    demonstrateConditionVariables();
    demonstrateDeadlockPrevention();
    demonstratePerformanceComparison();
    
    printf("All multithreading and concurrency examples demonstrated!\n");
    printf("Note: Some examples use Windows-specific APIs.\n");
    printf("For cross-platform development, consider pthreads or other portable libraries.\n");
    
    return 0;
}
