/*
 * File: condition_variables.c
 * Description: Condition variable synchronization examples
 */

#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <unistd.h>
#include <time.h>

#define BUFFER_SIZE 10
#define NUM_PRODUCERS 3
#define NUM_CONSUMERS 2
#define NUM_ITEMS 20

// Bounded buffer structure
typedef struct {
    int buffer[BUFFER_SIZE];
    int in;
    int out;
    int count;
    pthread_mutex_t mutex;
    pthread_cond_t not_empty;
    pthread_cond_t not_full;
} BoundedBuffer;

// Initialize bounded buffer
void buffer_init(BoundedBuffer* buffer) {
    buffer->in = 0;
    buffer->out = 0;
    buffer->count = 0;
    pthread_mutex_init(&buffer->mutex, NULL);
    pthread_cond_init(&buffer->not_empty, NULL);
    pthread_cond_init(&buffer->not_full, NULL);
}

// Destroy bounded buffer
void buffer_destroy(BoundedBuffer* buffer) {
    pthread_mutex_destroy(&buffer->mutex);
    pthread_cond_destroy(&buffer->not_empty);
    pthread_cond_destroy(&buffer->not_full);
}

// Put item into buffer
void buffer_put(BoundedBuffer* buffer, int item) {
    pthread_mutex_lock(&buffer->mutex);
    
    // Wait while buffer is full
    while (buffer->count == BUFFER_SIZE) {
        printf("Producer waiting: buffer full\n");
        pthread_cond_wait(&buffer->not_full, &buffer->mutex);
    }
    
    // Add item to buffer
    buffer->buffer[buffer->in] = item;
    buffer->in = (buffer->in + 1) % BUFFER_SIZE;
    buffer->count++;
    
    printf("Producer produced: %d (buffer count: %d)\n", item, buffer->count);
    
    // Signal that buffer is not empty
    pthread_cond_signal(&buffer->not_empty);
    
    pthread_mutex_unlock(&buffer->mutex);
}

// Get item from buffer
int buffer_get(BoundedBuffer* buffer) {
    pthread_mutex_lock(&buffer->mutex);
    
    // Wait while buffer is empty
    while (buffer->count == 0) {
        printf("Consumer waiting: buffer empty\n");
        pthread_cond_wait(&buffer->not_empty, &buffer->mutex);
    }
    
    // Remove item from buffer
    int item = buffer->buffer[buffer->out];
    buffer->out = (buffer->out + 1) % BUFFER_SIZE;
    buffer->count--;
    
    printf("Consumer consumed: %d (buffer count: %d)\n", item, buffer->count);
    
    // Signal that buffer is not full
    pthread_cond_signal(&buffer->not_full);
    
    pthread_mutex_unlock(&buffer->mutex);
    
    return item;
}

// Producer thread
void* producer(void* arg) {
    BoundedBuffer* buffer = (BoundedBuffer*)arg;
    int producer_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < NUM_ITEMS; i++) {
        int item = producer_id * 100 + i;
        buffer_put(buffer, item);
        usleep(rand() % 100000); // Random delay 0-100ms
    }
    
    printf("Producer %d finished\n", producer_id);
    return NULL;
}

// Consumer thread
void* consumer(void* arg) {
    BoundedBuffer* buffer = (BoundedBuffer*)arg;
    int consumer_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < NUM_ITEMS; i++) {
        int item = buffer_get(buffer);
        usleep(rand() % 150000); // Random delay 0-150ms
    }
    
    printf("Consumer %d finished\n", consumer_id);
    return NULL;
}

// Barrier synchronization structure
typedef struct {
    pthread_mutex_t mutex;
    pthread_cond_t cond;
    int count;
    int waiting;
    int thread_count;
} Barrier;

// Initialize barrier
void barrier_init(Barrier* barrier, int thread_count) {
    pthread_mutex_init(&barrier->mutex, NULL);
    pthread_cond_init(&barrier->cond, NULL);
    barrier->count = 0;
    barrier->waiting = 0;
    barrier->thread_count = thread_count;
}

// Destroy barrier
void barrier_destroy(Barrier* barrier) {
    pthread_mutex_destroy(&barrier->mutex);
    pthread_cond_destroy(&barrier->cond);
}

// Wait at barrier
void barrier_wait(Barrier* barrier) {
    pthread_mutex_lock(&barrier->mutex);
    
    barrier->waiting++;
    
    if (barrier->waiting == barrier->thread_count) {
        // All threads have arrived, release them
        barrier->waiting = 0;
        barrier->count++;
        pthread_cond_broadcast(&barrier->cond);
        printf("Barrier %d: All threads arrived, releasing\n", barrier->count);
    } else {
        // Wait for other threads
        printf("Thread waiting at barrier %d (%d/%d)\n", 
               barrier->count + 1, barrier->waiting, barrier->thread_count);
        pthread_cond_wait(&barrier->cond, &barrier->mutex);
    }
    
    pthread_mutex_unlock(&barrier->mutex);
}

// Worker thread for barrier example
void* barrier_worker(void* arg) {
    Barrier* barrier = (Barrier*)arg;
    int thread_id = (int)(long)pthread_self() % 1000;
    
    for (int phase = 0; phase < 3; phase++) {
        // Do some work
        printf("Thread %d: Working on phase %d\n", thread_id, phase);
        usleep(rand() % 200000); // Random work time
        
        // Wait at barrier
        printf("Thread %d: Reached barrier for phase %d\n", thread_id, phase);
        barrier_wait(barrier);
        
        printf("Thread %d: Passed barrier for phase %d\n", thread_id, phase);
    }
    
    return NULL;
}

// Read-write lock structure
typedef struct {
    pthread_mutex_t mutex;
    pthread_cond_t read_ok;
    pthread_cond_t write_ok;
    int active_readers;
    int waiting_writers;
    int active_writers;
} RWLock;

// Initialize read-write lock
void rwlock_init(RWLock* lock) {
    pthread_mutex_init(&lock->mutex, NULL);
    pthread_cond_init(&lock->read_ok, NULL);
    pthread_cond_init(&lock->write_ok, NULL);
    lock->active_readers = 0;
    lock->waiting_writers = 0;
    lock->active_writers = 0;
}

// Destroy read-write lock
void rwlock_destroy(RWLock* lock) {
    pthread_mutex_destroy(&lock->mutex);
    pthread_cond_destroy(&lock->read_ok, NULL);
    pthread_cond_destroy(&lock->write_ok, NULL);
}

// Acquire read lock
void rwlock_read_lock(RWLock* lock) {
    pthread_mutex_lock(&lock->mutex);
    
    // Wait if there are active or waiting writers
    while (lock->active_writers > 0 || lock->waiting_writers > 0) {
        printf("Reader waiting: writer active or waiting\n");
        pthread_cond_wait(&lock->read_ok, &lock->mutex);
    }
    
    lock->active_readers++;
    printf("Reader acquired lock (active readers: %d)\n", lock->active_readers);
    
    pthread_mutex_unlock(&lock->mutex);
}

// Release read lock
void rwlock_read_unlock(RWLock* lock) {
    pthread_mutex_lock(&lock->mutex);
    
    lock->active_readers--;
    printf("Reader released lock (active readers: %d)\n", lock->active_readers);
    
    // If no more active readers, signal waiting writers
    if (lock->active_readers == 0 && lock->waiting_writers > 0) {
        pthread_cond_signal(&lock->write_ok);
    }
    
    pthread_mutex_unlock(&lock->mutex);
}

// Acquire write lock
void rwlock_write_lock(RWLock* lock) {
    pthread_mutex_lock(&lock->mutex);
    
    lock->waiting_writers++;
    
    // Wait if there are active readers or writers
    while (lock->active_readers > 0 || lock->active_writers > 0) {
        printf("Writer waiting: readers or writers active\n");
        pthread_cond_wait(&lock->write_ok, &lock->mutex);
    }
    
    lock->waiting_writers--;
    lock->active_writers++;
    printf("Writer acquired lock\n");
    
    pthread_mutex_unlock(&lock->mutex);
}

// Release write lock
void rwlock_write_unlock(RWLock* lock) {
    pthread_mutex_lock(&lock->mutex);
    
    lock->active_writers--;
    printf("Writer released lock\n");
    
    // Signal waiting readers or writers
    if (lock->waiting_writers > 0) {
        pthread_cond_signal(&lock->write_ok);
    } else {
        pthread_cond_broadcast(&lock->read_ok);
    }
    
    pthread_mutex_unlock(&lock->mutex);
}

// Shared data structure
typedef struct {
    int data;
    RWLock rwlock;
} SharedData;

// Reader thread
void* reader_thread(void* arg) {
    SharedData* shared = (SharedData*)arg;
    int reader_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < 5; i++) {
        rwlock_read_lock(&shared->rwlock);
        printf("Reader %d: Read data = %d\n", reader_id, shared->data);
        usleep(100000); // Simulate reading
        rwlock_read_unlock(&shared->rwlock);
        
        usleep(200000); // Wait between reads
    }
    
    return NULL;
}

// Writer thread
void* writer_thread(void* arg) {
    SharedData* shared = (SharedData*)arg;
    int writer_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < 3; i++) {
        rwlock_write_lock(&shared->rwlock);
        shared->data += 10;
        printf("Writer %d: Wrote data = %d\n", writer_id, shared->data);
        usleep(200000); // Simulate writing
        rwlock_write_unlock(&shared->rwlock);
        
        usleep(300000); // Wait between writes
    }
    
    return NULL;
}

// Semaphore-like structure using condition variables
typedef struct {
    pthread_mutex_t mutex;
    pthread_cond_t cond;
    int count;
    int max_count;
} Semaphore;

// Initialize semaphore
void semaphore_init(Semaphore* sem, int initial_count, int max_count) {
    pthread_mutex_init(&sem->mutex, NULL);
    pthread_cond_init(&sem->cond, NULL);
    sem->count = initial_count;
    sem->max_count = max_count;
}

// Destroy semaphore
void semaphore_destroy(Semaphore* sem) {
    pthread_mutex_destroy(&sem->mutex);
    pthread_cond_destroy(&sem->cond);
}

// Wait (P operation)
void semaphore_wait(Semaphore* sem) {
    pthread_mutex_lock(&sem->mutex);
    
    while (sem->count == 0) {
        printf("Semaphore wait: count is 0, waiting\n");
        pthread_cond_wait(&sem->cond, &sem->mutex);
    }
    
    sem->count--;
    printf("Semaphore wait: count = %d\n", sem->count);
    
    pthread_mutex_unlock(&sem->mutex);
}

// Signal (V operation)
void semaphore_signal(Semaphore* sem) {
    pthread_mutex_lock(&sem->mutex);
    
    sem->count++;
    printf("Semaphore signal: count = %d\n", sem->count);
    
    pthread_cond_signal(&sem->cond);
    
    pthread_mutex_unlock(&sem->mutex);
}

// Semaphore test threads
void* semaphore_producer(void* arg) {
    Semaphore* sem = (Semaphore*)arg;
    int producer_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < 5; i++) {
        printf("Producer %d: Producing item %d\n", producer_id, i);
        usleep(200000);
        semaphore_signal(sem);
    }
    
    return NULL;
}

void* semaphore_consumer(void* arg) {
    Semaphore* sem = (Semaphore*)arg;
    int consumer_id = (int)(long)pthread_self() % 1000;
    
    for (int i = 0; i < 5; i++) {
        semaphore_wait(sem);
        printf("Consumer %d: Consumed item\n", consumer_id);
        usleep(150000);
    }
    
    return NULL;
}

// Test functions
void test_producer_consumer() {
    printf("=== Producer-Consumer with Condition Variables ===\n");
    
    BoundedBuffer buffer;
    buffer_init(&buffer);
    
    pthread_t producers[NUM_PRODUCERS];
    pthread_t consumers[NUM_CONSUMERS];
    
    // Create producer threads
    for (int i = 0; i < NUM_PRODUCERS; i++) {
        pthread_create(&producers[i], NULL, producer, &buffer);
    }
    
    // Create consumer threads
    for (int i = 0; i < NUM_CONSUMERS; i++) {
        pthread_create(&consumers[i], NULL, consumer, &buffer);
    }
    
    // Wait for all threads
    for (int i = 0; i < NUM_PRODUCERS; i++) {
        pthread_join(producers[i], NULL);
    }
    for (int i = 0; i < NUM_CONSUMERS; i++) {
        pthread_join(consumers[i], NULL);
    }
    
    buffer_destroy(&buffer);
}

void test_barrier() {
    printf("\n=== Barrier Synchronization ===\n");
    
    Barrier barrier;
    barrier_init(&barrier, 4);
    
    pthread_t threads[4];
    
    // Create worker threads
    for (int i = 0; i < 4; i++) {
        pthread_create(&threads[i], NULL, barrier_worker, &barrier);
    }
    
    // Wait for all threads
    for (int i = 0; i < 4; i++) {
        pthread_join(threads[i], NULL);
    }
    
    barrier_destroy(&barrier);
}

void test_read_write_lock() {
    printf("\n=== Read-Write Lock ===\n");
    
    SharedData shared;
    shared.data = 0;
    rwlock_init(&shared.rwlock);
    
    pthread_t readers[3];
    pthread_t writers[2];
    
    // Create reader threads
    for (int i = 0; i < 3; i++) {
        pthread_create(&readers[i], NULL, reader_thread, &shared);
    }
    
    // Create writer threads
    for (int i = 0; i < 2; i++) {
        pthread_create(&writers[i], NULL, writer_thread, &shared);
    }
    
    // Wait for all threads
    for (int i = 0; i < 3; i++) {
        pthread_join(readers[i], NULL);
    }
    for (int i = 0; i < 2; i++) {
        pthread_join(writers[i], NULL);
    }
    
    rwlock_destroy(&shared.rwlock);
    
    printf("Final data value: %d\n", shared.data);
}

void test_semaphore() {
    printf("\n=== Semaphore Implementation ===\n");
    
    Semaphore sem;
    semaphore_init(&sem, 0, 10);
    
    pthread_t producer, consumer;
    
    // Create producer and consumer
    pthread_create(&producer, NULL, semaphore_producer, &sem);
    pthread_create(&consumer, NULL, semaphore_consumer, &sem);
    
    // Wait for threads
    pthread_join(producer, NULL);
    pthread_join(consumer, NULL);
    
    semaphore_destroy(&sem);
}

int main() {
    srand(time(NULL));
    
    test_producer_consumer();
    test_barrier();
    test_read_write_lock();
    test_semaphore();
    
    printf("\n=== Condition variable examples completed ===\n");
    
    return 0;
}
