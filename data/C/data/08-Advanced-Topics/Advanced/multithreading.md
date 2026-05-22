# C Multithreading

## Thread Basics

### POSIX Threads (pthreads)
```c
#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

void* thread_function(void* arg) {
    int thread_id = *(int*)arg;
    printf("Thread %d is running\n", thread_id);
    return NULL;
}

int main() {
    pthread_t threads[3];
    int thread_ids[3];
    
    // Create threads
    for (int i = 0; i < 3; i++) {
        thread_ids[i] = i;
        if (pthread_create(&threads[i], NULL, thread_function, &thread_ids[i]) != 0) {
            perror("Failed to create thread");
            return 1;
        }
    }
    
    // Wait for threads to complete
    for (int i = 0; i < 3; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("All threads completed\n");
    return 0;
}
```

### Thread Attributes
```c
#include <pthread.h>

void* thread_function(void* arg) {
    printf("Thread with custom attributes\n");
    return NULL;
}

int main() {
    pthread_t thread;
    pthread_attr_t attr;
    
    // Initialize attributes
    pthread_attr_init(&attr);
    
    // Set stack size
    size_t stack_size = 1024 * 1024; // 1MB
    pthread_attr_setstacksize(&attr, stack_size);
    
    // Set detach state
    pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_JOINABLE);
    
    // Create thread with attributes
    pthread_create(&thread, &attr, thread_function, NULL);
    
    // Clean up attributes
    pthread_attr_destroy(&attr);
    
    // Wait for thread
    pthread_join(thread, NULL);
    
    return 0;
}
```

## Thread Synchronization

### Mutexes
```c
#include <pthread.h>

typedef struct {
    int counter;
    pthread_mutex_t mutex;
} SharedData;

void* increment_thread(void* arg) {
    SharedData* data = (SharedData*)arg;
    
    for (int i = 0; i < 100000; i++) {
        pthread_mutex_lock(&data->mutex);
        data->counter++;
        pthread_mutex_unlock(&data->mutex);
    }
    
    return NULL;
}

int main() {
    SharedData data = {0};
    pthread_t threads[4];
    
    // Initialize mutex
    pthread_mutex_init(&data.mutex, NULL);
    
    // Create threads
    for (int i = 0; i < 4; i++) {
        pthread_create(&threads[i], NULL, increment_thread, &data);
    }
    
    // Wait for completion
    for (int i = 0; i < 4; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("Final counter value: %d\n", data.counter);
    
    // Clean up
    pthread_mutex_destroy(&data.mutex);
    
    return 0;
}
```

### Condition Variables
```c
#include <pthread.h>

typedef struct {
    int data_available;
    int data;
    pthread_mutex_t mutex;
    pthread_cond_t cond;
} Buffer;

void* producer(void* arg) {
    Buffer* buffer = (Buffer*)arg;
    
    for (int i = 1; i <= 5; i++) {
        pthread_mutex_lock(&buffer->mutex);
        
        buffer->data = i;
        buffer->data_available = 1;
        printf("Produced: %d\n", i);
        
        pthread_cond_signal(&buffer->cond);
        pthread_mutex_unlock(&buffer->mutex);
        
        sleep(1);
    }
    
    return NULL;
}

void* consumer(void* arg) {
    Buffer* buffer = (Buffer*)arg;
    
    for (int i = 1; i <= 5; i++) {
        pthread_mutex_lock(&buffer->mutex);
        
        while (!buffer->data_available) {
            pthread_cond_wait(&buffer->cond, &buffer->mutex);
        }
        
        printf("Consumed: %d\n", buffer->data);
        buffer->data_available = 0;
        
        pthread_mutex_unlock(&buffer->mutex);
    }
    
    return NULL;
}

int main() {
    Buffer buffer = {0};
    pthread_t producer_thread, consumer_thread;
    
    pthread_mutex_init(&buffer.mutex, NULL);
    pthread_cond_init(&buffer.cond, NULL);
    
    pthread_create(&producer_thread, NULL, producer, &buffer);
    pthread_create(&consumer_thread, NULL, consumer, &buffer);
    
    pthread_join(producer_thread, NULL);
    pthread_join(consumer_thread, NULL);
    
    pthread_mutex_destroy(&buffer.mutex);
    pthread_cond_destroy(&buffer.cond);
    
    return 0;
}
```

### Semaphores
```c
#include <pthread.h>
#include <semaphore.h>

typedef struct {
    int buffer[5];
    int in, out;
    sem_t empty, full;
    pthread_mutex_t mutex;
} CircularBuffer;

void* producer(void* arg) {
    CircularBuffer* cb = (CircularBuffer*)arg;
    
    for (int i = 1; i <= 10; i++) {
        sem_wait(&cb->empty);
        pthread_mutex_lock(&cb->mutex);
        
        cb->buffer[cb->in] = i;
        printf("Produced: %d at position %d\n", i, cb->in);
        cb->in = (cb->in + 1) % 5;
        
        pthread_mutex_unlock(&cb->mutex);
        sem_post(&cb->full);
    }
    
    return NULL;
}

void* consumer(void* arg) {
    CircularBuffer* cb = (CircularBuffer*)arg;
    
    for (int i = 1; i <= 10; i++) {
        sem_wait(&cb->full);
        pthread_mutex_lock(&cb->mutex);
        
        int item = cb->buffer[cb->out];
        printf("Consumed: %d from position %d\n", item, cb->out);
        cb->out = (cb->out + 1) % 5;
        
        pthread_mutex_unlock(&cb->mutex);
        sem_post(&cb->empty);
    }
    
    return NULL;
}

int main() {
    CircularBuffer cb = {{0}, 0, 0};
    pthread_t producer_thread, consumer_thread;
    
    sem_init(&cb.empty, 0, 5);
    sem_init(&cb.full, 0, 0);
    pthread_mutex_init(&cb.mutex, NULL);
    
    pthread_create(&producer_thread, NULL, producer, &cb);
    pthread_create(&consumer_thread, NULL, consumer, &cb);
    
    pthread_join(producer_thread, NULL);
    pthread_join(consumer_thread, NULL);
    
    sem_destroy(&cb.empty);
    sem_destroy(&cb.full);
    pthread_mutex_destroy(&cb.mutex);
    
    return 0;
}
```

## Thread-Local Storage

### Thread-Specific Data
```c
#include <pthread.h>

static pthread_key_t thread_key;

void thread_specific_data_destructor(void* value) {
    free(value);
    printf("Thread-specific data destroyed\n");
}

void* thread_function(void* arg) {
    int thread_id = *(int*)arg;
    
    // Allocate thread-specific data
    int* thread_data = malloc(sizeof(int));
    *thread_data = thread_id * 100;
    
    // Set thread-specific data
    pthread_setspecific(thread_key, thread_data);
    
    // Get thread-specific data
    int* stored_data = pthread_getspecific(thread_key);
    printf("Thread %d: stored data = %d\n", thread_id, *stored_data);
    
    return NULL;
}

int main() {
    pthread_t threads[3];
    int thread_ids[3];
    
    // Create thread-specific data key
    pthread_key_create(&thread_key, thread_specific_data_destructor);
    
    // Create threads
    for (int i = 0; i < 3; i++) {
        thread_ids[i] = i;
        pthread_create(&threads[i], NULL, thread_function, &thread_ids[i]);
    }
    
    // Wait for threads
    for (int i = 0; i < 3; i++) {
        pthread_join(threads[i], NULL);
    }
    
    // Clean up
    pthread_key_delete(thread_key);
    
    return 0;
}
```

## Thread Pools

### Simple Thread Pool
```c
#include <pthread.h>
#include <stdatomic.h>

typedef struct Task {
    void (*function)(void* arg);
    void* arg;
    struct Task* next;
} Task;

typedef struct {
    Task* head;
    Task* tail;
    pthread_mutex_t mutex;
    pthread_cond_t cond;
    atomic_int shutdown;
} TaskQueue;

typedef struct {
    pthread_t* threads;
    TaskQueue queue;
    int thread_count;
} ThreadPool;

void* worker_thread(void* arg) {
    ThreadPool* pool = (ThreadPool*)arg;
    
    while (!atomic_load(&pool->queue.shutdown)) {
        pthread_mutex_lock(&pool->queue.mutex);
        
        while (pool->queue.head == NULL && !atomic_load(&pool->queue.shutdown)) {
            pthread_cond_wait(&pool->queue.cond, &pool->queue.mutex);
        }
        
        if (atomic_load(&pool->queue.shutdown)) {
            pthread_mutex_unlock(&pool->queue.mutex);
            break;
        }
        
        Task* task = pool->queue.head;
        pool->queue.head = task->next;
        if (pool->queue.head == NULL) {
            pool->queue.tail = NULL;
        }
        
        pthread_mutex_unlock(&pool->queue.mutex);
        
        task->function(task->arg);
        free(task);
    }
    
    return NULL;
}

ThreadPool* thread_pool_create(int thread_count) {
    ThreadPool* pool = malloc(sizeof(ThreadPool));
    pool->thread_count = thread_count;
    pool->threads = malloc(sizeof(pthread_t) * thread_count);
    pool->queue.head = NULL;
    pool->queue.tail = NULL;
    atomic_init(&pool->queue.shutdown, 0);
    
    pthread_mutex_init(&pool->queue.mutex, NULL);
    pthread_cond_init(&pool->queue.cond, NULL);
    
    for (int i = 0; i < thread_count; i++) {
        pthread_create(&pool->threads[i], NULL, worker_thread, pool);
    }
    
    return pool;
}

void thread_pool_submit(ThreadPool* pool, void (*function)(void*), void* arg) {
    Task* task = malloc(sizeof(Task));
    task->function = function;
    task->arg = arg;
    task->next = NULL;
    
    pthread_mutex_lock(&pool->queue.mutex);
    
    if (pool->queue.tail == NULL) {
        pool->queue.head = pool->queue.tail = task;
    } else {
        pool->queue.tail->next = task;
        pool->queue.tail = task;
    }
    
    pthread_cond_signal(&pool->queue.cond);
    pthread_mutex_unlock(&pool->queue.mutex);
}

void thread_pool_destroy(ThreadPool* pool) {
    atomic_store(&pool->queue.shutdown, 1);
    pthread_cond_broadcast(&pool->queue.cond);
    
    for (int i = 0; i < pool->thread_count; i++) {
        pthread_join(pool->threads[i], NULL);
    }
    
    pthread_mutex_destroy(&pool->queue.mutex);
    pthread_cond_destroy(&pool->queue.cond);
    
    free(pool->threads);
    free(pool);
}
```

## Atomic Operations

### Using Atomics
```c
#include <stdatomic.h>

typedef struct {
    atomic_int counter;
    atomic_flag flag;
} AtomicData;

void* atomic_increment_thread(void* arg) {
    AtomicData* data = (AtomicData*)arg;
    
    for (int i = 0; i < 100000; i++) {
        atomic_fetch_add(&data->counter, 1);
    }
    
    return NULL;
}

void* atomic_flag_thread(void* arg) {
    AtomicData* data = (AtomicData*)arg;
    
    while (atomic_flag_test_and_set(&data->flag)) {
        // Wait for flag to be cleared
        sched_yield();
    }
    
    printf("Thread acquired flag\n");
    sleep(1);
    printf("Thread releasing flag\n");
    
    atomic_flag_clear(&data->flag);
    
    return NULL;
}

int main() {
    AtomicData data;
    atomic_init(&data.counter, 0);
    atomic_flag_clear(&data.flag);
    
    pthread_t threads[4];
    
    // Test atomic counter
    for (int i = 0; i < 4; i++) {
        pthread_create(&threads[i], NULL, atomic_increment_thread, &data);
    }
    
    for (int i = 0; i < 4; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("Final counter: %d\n", atomic_load(&data.counter));
    
    // Test atomic flag
    pthread_create(&threads[0], NULL, atomic_flag_thread, &data);
    pthread_create(&threads[1], NULL, atomic_flag_thread, &data);
    
    pthread_join(threads[0], NULL);
    pthread_join(threads[1], NULL);
    
    return 0;
}
```

## Best Practices
- Always initialize synchronization primitives
- Lock mutexes for the shortest time possible
- Avoid deadlocks by consistent lock ordering
- Use condition variables for complex synchronization
- Consider atomic operations for simple counters
- Handle thread creation failures
- Clean up resources properly
- Avoid busy waiting when possible
- Use thread pools for managing many short tasks
- Be aware of false sharing in multi-core systems
- Profile and optimize critical sections
- Consider memory barriers for complex synchronization
