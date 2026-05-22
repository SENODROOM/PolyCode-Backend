# Go Concurrency

## Goroutines

### Basic Goroutine Usage
```go
package main

import (
    "fmt"
    "time"
    "sync"
    "runtime"
)

func main() {
    // Basic goroutine
    func demonstrateBasicGoroutine() {
        fmt.Println("=== Basic Goroutine ===")
        
        // Function to run in goroutine
        sayHello := func(name string) {
            for i := 0; i < 3; i++ {
                fmt.Printf("Hello, %s! (%d)\n", name, i+1)
                time.Sleep(100 * time.Millisecond)
            }
        }
        
        // Run in main goroutine
        fmt.Println("Main goroutine:")
        sayHello("Main")
        
        // Run in separate goroutine
        fmt.Println("Separate goroutine:")
        go sayHello("Goroutine")
        
        // Wait for goroutine to finish
        time.Sleep(500 * time.Millisecond)
    }
    
    // Multiple goroutines
    func demonstrateMultipleGoroutines() {
        fmt.Println("\n=== Multiple Goroutines ===")
        
        worker := func(id int) {
            fmt.Printf("Worker %d started\n", id)
            time.Sleep(200 * time.Millisecond)
            fmt.Printf("Worker %d finished\n", id)
        }
        
        // Start multiple goroutines
        for i := 1; i <= 3; i++ {
            go worker(i)
        }
        
        // Wait for all workers to finish
        time.Sleep(300 * time.Millisecond)
    }
    
    // Anonymous goroutines
    func demonstrateAnonymousGoroutines() {
        fmt.Println("\n=== Anonymous Goroutines ===")
        
        // Anonymous function in goroutine
        go func(name string) {
            fmt.Printf("Anonymous goroutine: %s\n", name)
        }("Anonymous")
        
        time.Sleep(100 * time.Millisecond)
    }
    
    // Goroutine with closure
    func demonstrateClosureGoroutine() {
        fmt.Println("\n=== Closure Goroutines ===")
        
        numbers := []int{1, 2, 3, 4, 5}
        
        for _, num := range numbers {
            go func(n int) {
                fmt.Printf("Number: %d\n", n)
            }(num) // Pass num as parameter
        }
        
        time.Sleep(100 * time.Millisecond)
    }
    
    // Goroutine lifecycle
    func demonstrateGoroutineLifecycle() {
        fmt.Println("\n=== Goroutine Lifecycle ===")
        
        longRunningTask := func() {
            fmt.Println("Long running task started")
            time.Sleep(1 * time.Second)
            fmt.Println("Long running task finished")
        }
        
        go longRunningTask()
        
        fmt.Println("Main continues while goroutine runs")
        time.Sleep(1200 * time.Millisecond)
    }
    
    // Goroutine scheduling
    func demonstrateGoroutineScheduling() {
        fmt.Println("\n=== Goroutine Scheduling ===")
        
        fmt.Printf("GOMAXPROCS: %d\n", runtime.GOMAXPROCS(0))
        
        task := func(id int, duration time.Duration) {
            start := time.Now()
            for time.Since(start) < duration {
                // Busy work
            }
            fmt.Printf("Task %d completed\n", id)
        }
        
        // Start multiple tasks
        for i := 1; i <= 4; i++ {
            go task(i, 200*time.Millisecond)
        }
        
        time.Sleep(300 * time.Millisecond)
    }
    
    // Goroutine panic handling
    func demonstratePanicHandling() {
        fmt.Println("\n=== Panic Handling ===")
        
        safeTask := func() {
            defer func() {
                if r := recover(); r != nil {
                    fmt.Printf("Recovered from panic: %v\n", r)
                }
            }()
            
            fmt.Println("About to panic")
            panic("Something went wrong!")
        }
        
        go safeTask()
        time.Sleep(100 * time.Millisecond)
    }
    
    // Goroutine with return values (using channels)
    func demonstrateReturnValues() {
        fmt.Println("\n=== Return Values with Channels ===")
        
        calculator := func(a, b int, resultChan chan int) {
            time.Sleep(100 * time.Millisecond) // Simulate work
            resultChan <- a + b
        }
        
        resultChan := make(chan int)
        
        go calculator(10, 20, resultChan)
        
        result := <-resultChan
        fmt.Printf("Calculation result: %d\n", result)
    }
    
    // Goroutine pool pattern
    func demonstrateGoroutinePool() {
        fmt.Println("\n=== Goroutine Pool ===")
        
        type Worker struct {
            id int
            jobs chan int
            results chan int
        }
        
        NewWorker := func(id int, jobs chan int, results chan int) *Worker {
            return &Worker{
                id: id,
                jobs: jobs,
                results: results,
            }
        }
        
        (w *Worker) Start := func() {
            for job := range w.jobs {
                fmt.Printf("Worker %d processing job %d\n", w.id, job)
                time.Sleep(100 * time.Millisecond)
                w.results <- job * 2
            }
        }
        
        const numJobs = 5
        const numWorkers = 2
        
        jobs := make(chan int, numJobs)
        results := make(chan int, numJobs)
        
        // Create workers
        for i := 1; i <= numWorkers; i++ {
            worker := NewWorker(i, jobs, results)
            go worker.Start()
        }
        
        // Send jobs
        for i := 1; i <= numJobs; i++ {
            jobs <- i
        }
        close(jobs)
        
        // Collect results
        for i := 1; i <= numJobs; i++ {
            result := <-results
            fmt.Printf("Result: %d\n", result)
        }
    }
    
    // Run all demonstrations
    demonstrateBasicGoroutine()
    demonstrateMultipleGoroutines()
    demonstrateAnonymousGoroutines()
    demonstrateClosureGoroutine()
    demonstrateGoroutineLifecycle()
    demonstrateGoroutineScheduling()
    demonstratePanicHandling()
    demonstrateReturnValues()
    demonstrateGoroutinePool()
}
```

### Goroutine Patterns
```go
package main

import (
    "fmt"
    "time"
    "sync"
    "context"
)

func main() {
    // Fan-out/Fan-in pattern
    func demonstrateFanOutFanIn() {
        fmt.Println("=== Fan-out/Fan-in Pattern ===")
        
        // Producer
        producer := func(nums []int, out chan int) {
            for _, num := range nums {
                out <- num
            }
            close(out)
        }
        
        // Worker
        worker := func(in chan int, out chan int, id int) {
            for num := range in {
                result := num * num
                fmt.Printf("Worker %d: %d^2 = %d\n", id, num, result)
                out <- result
            }
        }
        
        // Fan-in
        fanIn := func(channels []chan int, out chan int) {
            var wg sync.WaitGroup
            for _, ch := range channels {
                wg.Add(1)
                go func(c chan int) {
                    defer wg.Done()
                    for val := range c {
                        out <- val
                    }
                }(ch)
            }
            
            go func() {
                wg.Wait()
                close(out)
            }()
        }
        
        // Setup
        numbers := []int{1, 2, 3, 4, 5, 6, 7, 8}
        producerChan := make(chan int)
        
        // Start producer
        go producer(numbers, producerChan)
        
        // Fan-out to multiple workers
        numWorkers := 3
        workerChans := make([]chan int, numWorkers)
        resultChans := make([]chan int, numWorkers)
        
        for i := 0; i < numWorkers; i++ {
            workerChans[i] = make(chan int)
            resultChans[i] = make(chan int)
            go worker(workerChans[i], resultChans[i], i+1)
        }
        
        // Distribute work
        go func() {
            for num := range producerChan {
                workerIndex := num % numWorkers
                workerChans[workerIndex] <- num
            }
            for _, ch := range workerChans {
                close(ch)
            }
        }()
        
        // Fan-in results
        finalChan := make(chan int)
        fanIn(resultChans, finalChan)
        
        // Collect results
        for result := range finalChan {
            fmt.Printf("Final result: %d\n", result)
        }
    }
    
    // Pipeline pattern
    func demonstratePipeline() {
        fmt.Println("\n=== Pipeline Pattern ===")
        
        // Stage 1: Generate numbers
        generator := func(done <-chan struct{}, nums ...int) <-chan int {
            out := make(chan int)
            go func() {
                defer close(out)
                for _, num := range nums {
                    select {
                    case out <- num:
                    case <-done:
                        return
                    }
                }
            }()
            return out
        }
        
        // Stage 2: Square numbers
        squarer := func(done <-chan struct{}, in <-chan int) <-chan int {
            out := make(chan int)
            go func() {
                defer close(out)
                for num := range in {
                    select {
                    case out <- num * num:
                    case <-done:
                        return
                    }
                }
            }()
            return out
        }
        
        // Stage 3: Filter even numbers
        filter := func(done <-chan struct{}, in <-chan int) <-chan int {
            out := make(chan int)
            go func() {
                defer close(out)
                for num := range in {
                    if num%2 == 0 {
                        select {
                        case out <- num:
                        case <-done:
                            return
                        }
                    }
                }
            }()
            return out
        }
        
        // Build pipeline
        done := make(chan struct{})
        defer close(done)
        
        numbers := generator(done, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
        squared := squarer(done, numbers)
        filtered := filter(done, squared)
        
        // Consume results
        for result := range filtered {
            fmt.Printf("Pipeline result: %d\n", result)
        }
    }
    
    // Worker pool pattern
    func demonstrateWorkerPool() {
        fmt.Println("\n=== Worker Pool Pattern ===")
        
        type Job struct {
            ID     int
            Data   string
            Result chan string
        }
        
        type Worker struct {
            ID       int
            JobQueue chan Job
            Quit     chan bool
        }
        
        NewWorker := func(id int, jobQueue chan Job) *Worker {
            return &Worker{
                ID:       id,
                JobQueue: jobQueue,
                Quit:     make(chan bool),
            }
        }
        
        (w *Worker) Start := func() {
            go func() {
                for {
                    select {
                    case job := <-w.JobQueue:
                        fmt.Printf("Worker %d processing job %d\n", w.ID, job.ID)
                        time.Sleep(100 * time.Millisecond)
                        job.Result <- fmt.Sprintf("Worker %d completed job %d: %s", w.ID, job.ID, job.Data)
                    case <-w.Quit:
                        fmt.Printf("Worker %d stopping\n", w.ID)
                        return
                    }
                }
            }()
        }
        
        (w *Worker) Stop := func() {
            w.Quit <- true
        }
        
        // Create worker pool
        const numWorkers = 3
        jobQueue := make(chan Job, 10)
        workers := make([]*Worker, numWorkers)
        
        for i := 0; i < numWorkers; i++ {
            workers[i] = NewWorker(i+1, jobQueue)
            workers[i].Start()
        }
        
        // Send jobs
        jobs := []Job{
            {ID: 1, Data: "Process file A", Result: make(chan string)},
            {ID: 2, Data: "Process file B", Result: make(chan string)},
            {ID: 3, Data: "Process file C", Result: make(chan string)},
            {ID: 4, Data: "Process file D", Result: make(chan string)},
            {ID: 5, Data: "Process file E", Result: make(chan string)},
        }
        
        // Dispatch jobs
        for _, job := range jobs {
            jobQueue <- job
        }
        
        // Collect results
        for _, job := range jobs {
            result := <-job.Result
            fmt.Printf("Result: %s\n", result)
        }
        
        // Stop workers
        for _, worker := range workers {
            worker.Stop()
        }
    }
    
    // Timeout pattern
    func demonstrateTimeout() {
        fmt.Println("\n=== Timeout Pattern ===")
        
        task := func() (string, error) {
            time.Sleep(2 * time.Second)
            return "Task completed", nil
        }
        
        // With timeout
        ctx, cancel := context.WithTimeout(context.Background(), 1*time.Second)
        defer cancel()
        
        done := make(chan struct {
            result string
            err    error
        })
        
        go func() {
            result, err := task()
            done <- struct {
                result string
                err    error
            }{result, err}
        }()
        
        select {
        case res := <-done:
            if res.err != nil {
                fmt.Printf("Task failed: %v\n", res.err)
            } else {
                fmt.Printf("Task succeeded: %s\n", res.result)
            }
        case <-ctx.Done():
            fmt.Printf("Task timed out: %v\n", ctx.Err())
        }
        
        // Without timeout
        ctx2, cancel2 := context.WithTimeout(context.Background(), 3*time.Second)
        defer cancel2()
        
        done2 := make(chan struct {
            result string
            err    error
        })
        
        go func() {
            result, err := task()
            done2 <- struct {
                result string
                err    error
            }{result, err}
        }()
        
        select {
        case res := <-done2:
            if res.err != nil {
                fmt.Printf("Task failed: %v\n", res.err)
            } else {
                fmt.Printf("Task succeeded: %s\n", res.result)
            }
        case <-ctx2.Done():
            fmt.Printf("Task timed out: %v\n", ctx2.Err())
        }
    }
    
    // Retry pattern
    func demonstrateRetry() {
        fmt.Println("\n=== Retry Pattern ===")
        
        unreliableOperation := func(attempt int) error {
            if attempt < 3 {
                return fmt.Errorf("operation failed on attempt %d", attempt)
            }
            fmt.Printf("Operation succeeded on attempt %d\n", attempt)
            return nil
        }
        
        retry := func(operation func(int) error, maxAttempts int) error {
            var err error
            for attempt := 1; attempt <= maxAttempts; attempt++ {
                err = operation(attempt)
                if err == nil {
                    return nil
                }
                
                fmt.Printf("Attempt %d failed: %v\n", attempt, err)
                
                if attempt < maxAttempts {
                    backoff := time.Duration(attempt) * 100 * time.Millisecond
                    fmt.Printf("Retrying in %v...\n", backoff)
                    time.Sleep(backoff)
                }
            }
            return fmt.Errorf("operation failed after %d attempts: %v", maxAttempts, err)
        }
        
        err := retry(unreliableOperation, 5)
        if err != nil {
            fmt.Printf("Final error: %v\n", err)
        } else {
            fmt.Println("Operation succeeded")
        }
    }
    
    // Circuit breaker pattern
    func demonstrateCircuitBreaker() {
        fmt.Println("\n=== Circuit Breaker Pattern ===")
        
        type CircuitBreaker struct {
            maxFailures   int
            failureCount  int
            state         string // "closed", "open", "half-open"
            nextAttempt    time.Time
            resetTimeout  time.Duration
            mu            sync.Mutex
        }
        
        NewCircuitBreaker := func(maxFailures int, resetTimeout time.Duration) *CircuitBreaker {
            return &CircuitBreaker{
                maxFailures:  maxFailures,
                state:        "closed",
                resetTimeout: resetTimeout,
            }
        }
        
        (cb *CircuitBreaker) Call := func(operation func() error) error {
            cb.mu.Lock()
            defer cb.mu.Unlock()
            
            // Check if circuit is open
            if cb.state == "open" {
                if time.Now().After(cb.nextAttempt) {
                    cb.state = "half-open"
                } else {
                    return fmt.Errorf("circuit breaker is open")
                }
            }
            
            // Execute operation
            err := operation()
            
            if err != nil {
                cb.failureCount++
                if cb.failureCount >= cb.maxFailures {
                    cb.state = "open"
                    cb.nextAttempt = time.Now().Add(cb.resetTimeout)
                    fmt.Printf("Circuit breaker opened due to %d failures\n", cb.failureCount)
                }
                return err
            }
            
            // Success - reset failure count
            cb.failureCount = 0
            if cb.state == "half-open" {
                cb.state = "closed"
                fmt.Println("Circuit breaker closed again")
            }
            
            return nil
        }
        
        // Test circuit breaker
        cb := NewCircuitBreaker(3, 2*time.Second)
        
        operation := func(success bool) func() error {
            return func() error {
                if success {
                    return nil
                }
                return fmt.Errorf("operation failed")
            }
        }
        
        // Test failures
        for i := 0; i < 4; i++ {
            err := cb.Call(operation(false))
            if err != nil {
                fmt.Printf("Call %d failed: %v\n", i+1, err)
            } else {
                fmt.Printf("Call %d succeeded\n", i+1)
            }
        }
        
        // Wait for reset timeout
        fmt.Println("Waiting for circuit breaker to reset...")
        time.Sleep(3 * time.Second)
        
        // Test after reset
        err := cb.Call(operation(true))
        if err != nil {
            fmt.Printf("Call after reset failed: %v\n", err)
        } else {
            fmt.Printf("Call after reset succeeded\n")
        }
    }
    
    // Run all demonstrations
    demonstrateFanOutFanIn()
    demonstratePipeline()
    demonstrateWorkerPool()
    demonstrateTimeout()
    demonstrateRetry()
    demonstrateCircuitBreaker()
}
```

## Channels

### Channel Basics
```go
package main

import (
    "fmt"
    "time"
    "sync"
)

func main() {
    // Basic channel operations
    func demonstrateBasicChannels() {
        fmt.Println("=== Basic Channels ===")
        
        // Unbuffered channel
        ch := make(chan string)
        
        // Sender
        go func() {
            ch <- "Hello from goroutine"
            fmt.Println("Message sent")
        }()
        
        // Receiver
        msg := <-ch
        fmt.Printf("Message received: %s\n", msg)
        
        // Buffered channel
        bufferedCh := make(chan int, 3)
        
        // Send to buffered channel
        bufferedCh <- 1
        bufferedCh <- 2
        bufferedCh <- 3
        
        fmt.Printf("Buffered channel length: %d, capacity: %d\n", len(bufferedCh), cap(bufferedCh))
        
        // Receive from buffered channel
        fmt.Printf("Received: %d\n", <-bufferedCh)
        fmt.Printf("Received: %d\n", <-bufferedCh)
        fmt.Printf("Received: %d\n", <-bufferedCh)
    }
    
    // Channel directions
    func demonstrateChannelDirections() {
        fmt.Println("\n=== Channel Directions ===")
        
        // Send-only channel
        sender := func(ch chan<- int) {
            for i := 1; i <= 3; i++ {
                ch <- i
                fmt.Printf("Sent: %d\n", i)
            }
            close(ch)
        }
        
        // Receive-only channel
        receiver := func(ch <-chan int) {
            for value := range ch {
                fmt.Printf("Received: %d\n", value)
            }
        }
        
        ch := make(chan int)
        
        go sender(ch)
        receiver(ch)
    }
    
    // Channel closing
    func demonstrateChannelClosing() {
        fmt.Println("\n=== Channel Closing ===")
        
        producer := func(ch chan int) {
            for i := 1; i <= 5; i++ {
                ch <- i
                fmt.Printf("Produced: %d\n", i)
            }
            close(ch)
            fmt.Println("Producer closed channel")
        }
        
        consumer := func(ch chan int) {
            for value := range ch {
                fmt.Printf("Consumed: %d\n", value)
            }
            fmt.Println("Consumer finished")
        }
        
        ch := make(chan int)
        
        go producer(ch)
        consumer(ch)
    }
    
    // Select statement
    func demonstrateSelect() {
        fmt.Println("\n=== Select Statement ===")
        
        ch1 := make(chan string)
        ch2 := make(chan string)
        
        go func() {
            time.Sleep(100 * time.Millisecond)
            ch1 <- "Message from channel 1"
        }()
        
        go func() {
            time.Sleep(200 * time.Millisecond)
            ch2 <- "Message from channel 2"
        }()
        
        for i := 0; i < 2; i++ {
            select {
            case msg1 := <-ch1:
                fmt.Printf("Received from ch1: %s\n", msg1)
            case msg2 := <-ch2:
                fmt.Printf("Received from ch2: %s\n", msg2)
            }
        }
    }
    
    // Select with timeout
    func demonstrateSelectTimeout() {
        fmt.Println("\n=== Select with Timeout ===")
        
        ch := make(chan string)
        
        go func() {
            time.Sleep(2 * time.Second)
            ch <- "Delayed message"
        }()
        
        select {
        case msg := <-ch:
            fmt.Printf("Received: %s\n", msg)
        case <-time.After(1 * time.Second):
            fmt.Println("Timeout occurred")
        }
    }
    
    // Select with default
    func demonstrateSelectDefault() {
        fmt.Println("\n=== Select with Default ===")
        
        ch := make(chan string)
        
        // Non-blocking receive
        select {
        case msg := <-ch:
            fmt.Printf("Received: %s\n", msg)
        default:
            fmt.Println("No message available")
        }
        
        // Non-blocking send
        select {
        case ch <- "Test message":
            fmt.Println("Message sent")
        default:
            fmt.Println("Cannot send - channel not ready")
        }
    }
    
    // Channel as semaphore
    func demonstrateSemaphore() {
        fmt.Println("\n=== Channel as Semaphore ===")
        
        const maxConcurrency = 3
        semaphore := make(chan struct{}, maxConcurrency)
        
        worker := func(id int) {
            semaphore <- struct{}{} // Acquire
            fmt.Printf("Worker %d started\n", id)
            time.Sleep(500 * time.Millisecond)
            fmt.Printf("Worker %d finished\n", id)
            <-semaphore // Release
        }
        
        // Start more workers than semaphore allows
        for i := 1; i <= 6; i++ {
            go worker(i)
        }
        
        time.Sleep(2 * time.Second)
    }
    
    // Fan-in pattern
    func demonstrateFanIn() {
        fmt.Println("\n=== Fan-in Pattern ===")
        
        producer := func(id int, out chan string) {
            for i := 1; i <= 3; i++ {
                out <- fmt.Sprintf("Producer %d: Message %d", id, i)
                time.Sleep(100 * time.Millisecond)
            }
        }
        
        fanIn := func(channels ...chan string) chan string {
            out := make(chan string)
            
            for _, ch := range channels {
                go func(c chan string) {
                    for msg := range c {
                        out <- msg
                    }
                }(ch)
            }
            
            return out
        }
        
        // Create producers
        ch1 := make(chan string)
        ch2 := make(chan string)
        ch3 := make(chan string)
        
        go producer(1, ch1)
        go producer(2, ch2)
        go producer(3, ch3)
        
        // Fan-in
        combined := fanIn(ch1, ch2, ch3)
        
        // Consume combined messages
        for i := 0; i < 9; i++ {
            msg := <-combined
            fmt.Printf("Combined: %s\n", msg)
        }
    }
    
    // Fan-out pattern
    func demonstrateFanOut() {
        fmt.Println("\n=== Fan-out Pattern ===")
        
        producer := func(out chan int) {
            for i := 1; i <= 10; i++ {
                out <- i
                time.Sleep(50 * time.Millisecond)
            }
            close(out)
        }
        
        worker := func(id int, in <-chan int, out chan string) {
            for num := range in {
                result := fmt.Sprintf("Worker %d processed %d", id, num)
                out <- result
                time.Sleep(100 * time.Millisecond)
            }
        }
        
        // Setup
        in := make(chan int)
        out := make(chan string)
        
        // Start producer
        go producer(in)
        
        // Start workers (fan-out)
        for i := 1; i <= 3; i++ {
            go worker(i, in, out)
        }
        
        // Collect results
        for i := 0; i < 10; i++ {
            result := <-out
            fmt.Printf("Result: %s\n", result)
        }
    }
    
    // Channel for synchronization
    func demonstrateSynchronization() {
        fmt.Println("\n=== Channel Synchronization ===")
        
        done := make(chan bool)
        
        task := func(id int) {
            fmt.Printf("Task %d started\n", id)
            time.Sleep(200 * time.Millisecond)
            fmt.Printf("Task %d finished\n", id)
            done <- true
        }
        
        // Start multiple tasks
        for i := 1; i <= 3; i++ {
            go task(i)
        }
        
        // Wait for all tasks
        for i := 1; i <= 3; i++ {
            <-done
        }
        
        fmt.Println("All tasks completed")
    }
    
    // Channel for one-shot events
    func demonstrateOneShot() {
        fmt.Println("\n=== One-shot Channel ===")
        
        // Using channel for one-shot notification
        done := make(chan struct{})
        
        go func() {
            fmt.Println("Goroutine working...")
            time.Sleep(1 * time.Second)
            close(done) // Signal completion
        }()
        
        fmt.Println("Main waiting for completion...")
        <-done // Wait for completion
        fmt.Println("Main continuing")
    }
    
    // Channel with WaitGroup
    func demonstrateWaitGroup() {
        fmt.Println("\n=== WaitGroup with Channels ===")
        
        var wg sync.WaitGroup
        results := make(chan int, 3)
        
        worker := func(id int) {
            defer wg.Done()
            
            fmt.Printf("Worker %d started\n", id)
            time.Sleep(100 * time.Millisecond)
            
            result := id * 10
            results <- result
            fmt.Printf("Worker %d finished with result %d\n", id, result)
        }
        
        // Start workers
        for i := 1; i <= 3; i++ {
            wg.Add(1)
            go worker(i)
        }
        
        // Wait for all workers in separate goroutine
        go func() {
            wg.Wait()
            close(results)
        }()
        
        // Collect results
        for result := range results {
            fmt.Printf("Collected result: %d\n", result)
        }
        
        fmt.Println("All workers completed")
    }
    
    // Run all demonstrations
    demonstrateBasicChannels()
    demonstrateChannelDirections()
    demonstrateChannelClosing()
    demonstrateSelect()
    demonstrateSelectTimeout()
    demonstrateSelectDefault()
    demonstrateSemaphore()
    demonstrateFanIn()
    demonstrateFanOut()
    demonstrateSynchronization()
    demonstrateOneShot()
    demonstrateWaitGroup()
}
```

### Advanced Channel Patterns
```go
package main

import (
    "fmt"
    "time"
    "context"
    "sync"
)

func main() {
    // Worker pool with channels
    func demonstrateWorkerPool() {
        fmt.Println("=== Advanced Worker Pool ===")
        
        type Task struct {
            ID   int
            Data string
        }
        
        type Result struct {
            TaskID int
            Output string
            Error  error
        }
        
        worker := func(id int, tasks <-chan Task, results chan<- Result) {
            for task := range tasks {
                fmt.Printf("Worker %d processing task %d\n", id, task.ID)
                
                // Simulate work
                time.Sleep(100 * time.Millisecond)
                
                result := Result{
                    TaskID: task.ID,
                    Output: fmt.Sprintf("Processed: %s", task.Data),
                }
                
                results <- result
            }
        }
        
        const numWorkers = 3
        const numTasks = 10
        
        tasks := make(chan Task, numTasks)
        results := make(chan Result, numTasks)
        
        // Start workers
        for i := 1; i <= numWorkers; i++ {
            go worker(i, tasks, results)
        }
        
        // Send tasks
        go func() {
            for i := 1; i <= numTasks; i++ {
                tasks <- Task{
                    ID:   i,
                    Data: fmt.Sprintf("Task %d data", i),
                }
            }
            close(tasks)
        }()
        
        // Collect results
        for i := 0; i < numTasks; i++ {
            result := <-results
            fmt.Printf("Task %d result: %s\n", result.TaskID, result.Output)
        }
    }
    
    // Pub/Sub pattern
    func demonstratePubSub() {
        fmt.Println("\n=== Pub/Sub Pattern ===")
        
        type Message struct {
            Topic   string
            Content string
        }
        
        type Subscriber struct {
            ID    int
            Topic string
            Ch    chan Message
        }
        
        type Broker struct {
            subscribers map[string][]chan Message
            mu          sync.RWMutex
        }
        
        NewBroker := func() *Broker {
            return &Broker{
                subscribers: make(map[string][]chan Message),
            }
        }
        
        (b *Broker) Subscribe := func(topic string) chan Message {
            b.mu.Lock()
            defer b.mu.Unlock()
            
            ch := make(chan Message, 10)
            b.subscribers[topic] = append(b.subscribers[topic], ch)
            return ch
        }
        
        (b *Broker) Publish := func(msg Message) {
            b.mu.RLock()
            defer b.mu.RUnlock()
            
            if subscribers, exists := b.subscribers[msg.Topic]; exists {
                for _, sub := range subscribers {
                    go func(ch chan Message) {
                        ch <- msg
                    }(sub)
                }
            }
        }
        
        broker := NewBroker()
        
        // Subscribe to topics
        techSub := broker.Subscribe("tech")
        newsSub := broker.Subscribe("news")
        
        // Start subscribers
        go func() {
            for msg := range techSub {
                fmt.Printf("Tech subscriber received: %s\n", msg.Content)
            }
        }()
        
        go func() {
            for msg := range newsSub {
                fmt.Printf("News subscriber received: %s\n", msg.Content)
            }
        }()
        
        // Publish messages
        broker.Publish(Message{Topic: "tech", Content: "New Go release"})
        broker.Publish(Message{Topic: "news", Content: "Breaking news"})
        broker.Publish(Message{Topic: "tech", Content: "Go conference announced"})
        
        time.Sleep(100 * time.Millisecond)
    }
    
    // Rate limiting with channels
    func demonstrateRateLimiting() {
        fmt.Println("\n=== Rate Limiting ===")
        
        // Token bucket rate limiter
        rateLimiter := func(rate int, burst int) <-chan time.Time {
            tokens := make(chan time.Time, burst)
            
            // Fill bucket
            for i := 0; i < burst; i++ {
                tokens <- time.Time{}
            }
            
            go func() {
                ticker := time.NewTicker(time.Second / time.Duration(rate))
                defer ticker.Stop()
                
                for {
                    select {
                    case <-ticker.C:
                        select {
                        case tokens <- time.Time{}:
                        default: // Bucket full
                        }
                    }
                }
            }()
            
            return tokens
        }
        
        // Use rate limiter
        limiter := rateLimiter(3, 5) // 3 requests per second, burst of 5
        
        request := func(id int) {
            <-limiter // Wait for token
            fmt.Printf("Request %d processed at %s\n", id, time.Now().Format("15:04:05.000"))
        }
        
        // Make requests
        for i := 1; i <= 10; i++ {
            go request(i)
        }
        
        time.Sleep(3 * time.Second)
    }
    
    // Context cancellation with channels
    func demonstrateContextCancellation() {
        fmt.Println("\n=== Context Cancellation ===")
        
        task := func(ctx context.Context, id int) {
            for {
                select {
                case <-ctx.Done():
                    fmt.Printf("Task %d cancelled: %v\n", id, ctx.Err())
                    return
                default:
                    fmt.Printf("Task %d working...\n", id)
                    time.Sleep(200 * time.Millisecond)
                }
            }
        }
        
        // Create context with cancellation
        ctx, cancel := context.WithCancel(context.Background())
        
        // Start tasks
        for i := 1; i <= 3; i++ {
            go task(ctx, i)
        }
        
        // Cancel after 1 second
        time.Sleep(1 * time.Second)
        cancel()
        
        time.Sleep(500 * time.Millisecond)
    }
    
    // Context timeout with channels
    func demonstrateContextTimeout() {
        fmt.Println("\n=== Context Timeout ===")
        
        operation := func(ctx context.Context, duration time.Duration) error {
            fmt.Printf("Operation started, will take %v\n", duration)
            
            select {
            case <-time.After(duration):
                fmt.Println("Operation completed successfully")
                return nil
            case <-ctx.Done():
                fmt.Printf("Operation cancelled: %v\n", ctx.Err())
                return ctx.Err()
            }
        }
        
        // Operation with sufficient timeout
        ctx1, cancel1 := context.WithTimeout(context.Background(), 2*time.Second)
        defer cancel1()
        
        err := operation(ctx1, 1*time.Second)
        fmt.Printf("Result 1: %v\n", err)
        
        // Operation with insufficient timeout
        ctx2, cancel2 := context.WithTimeout(context.Background(), 1*time.Second)
        defer cancel2()
        
        err = operation(ctx2, 2*time.Second)
        fmt.Printf("Result 2: %v\n", err)
    }
    
    // Deadlock detection
    func demonstrateDeadlockDetection() {
        fmt.Println("\n=== Deadlock Detection ===")
        
        // Safe operation
        func safeOperation() {
            ch := make(chan int)
            
            go func() {
                ch <- 42
            }()
            
            value := <-ch
            fmt.Printf("Safe operation received: %d\n", value)
        }
        
        // Unsafe operation (would deadlock)
        func unsafeOperation() {
            ch := make(chan int)
            
            // This would deadlock because no sender
            // value := <-ch
            // fmt.Printf("Unsafe operation received: %d\n", value)
            
            // Use select with timeout to detect deadlock
            select {
            case value := <-ch:
                fmt.Printf("Unsafe operation received: %d\n", value)
            case <-time.After(100 * time.Millisecond):
                fmt.Println("Unsafe operation: potential deadlock detected")
            }
        }
        
        safeOperation()
        unsafeOperation()
    }
    
    // Channel for graceful shutdown
    func demonstrateGracefulShutdown() {
        fmt.Println("\n=== Graceful Shutdown ===")
        
        type Server struct {
            shutdown chan struct{}
            wg       sync.WaitGroup
        }
        
        NewServer := func() *Server {
            return &Server{
                shutdown: make(chan struct{}),
            }
        }
        
        (s *Server) Start := func() {
            // Start worker
            s.wg.Add(1)
            go func() {
                defer s.wg.Done()
                
                for {
                    select {
                    case <-s.shutdown:
                        fmt.Println("Worker shutting down...")
                        return
                    default:
                        fmt.Println("Worker working...")
                        time.Sleep(200 * time.Millisecond)
                    }
                }
            }()
            
            fmt.Println("Server started")
        }
        
        (s *Server) Stop := func() {
            fmt.Println("Initiating shutdown...")
            close(s.shutdown)
            
            done := make(chan struct{})
            go func() {
                s.wg.Wait()
                close(done)
            }()
            
            select {
            case <-done:
                fmt.Println("Server shut down gracefully")
            case <-time.After(2 * time.Second):
                fmt.Println("Server shutdown timeout")
            }
        }
        
        server := NewServer()
        server.Start()
        
        // Run for a bit then shutdown
        time.Sleep(1 * time.Second)
        server.Stop()
    }
    
    // Channel for resource pooling
    func demonstrateResourcePool() {
        fmt.Println("\n=== Resource Pool ===")
        
        type Resource struct {
            ID int
        }
        
        type Pool struct {
            resources chan Resource
            factory   func() Resource
        }
        
        NewPool := func(size int, factory func() Resource) *Pool {
            pool := &Pool{
                resources: make(chan Resource, size),
                factory:   factory,
            }
            
            // Pre-fill pool
            for i := 0; i < size; i++ {
                pool.resources <- factory()
            }
            
            return pool
        }
        
        (p *Pool) Get := func() Resource {
            return <-p.resources
        }
        
        (p *Pool) Put := func(resource Resource) {
            p.resources <- resource
        }
        
        // Create pool
        pool := NewPool(3, func() Resource {
            staticID := 0
            return Resource{ID: staticID}
        })
        
        // Use resources
        for i := 0; i < 5; i++ {
            go func(id int) {
                resource := pool.Get()
                fmt.Printf("Goroutine %d got resource %d\n", id, resource.ID)
                
                time.Sleep(100 * time.Millisecond)
                
                pool.Put(resource)
                fmt.Printf("Goroutine %d returned resource %d\n", id, resource.ID)
            }(i)
        }
        
        time.Sleep(500 * time.Millisecond)
    }
    
    // Run all demonstrations
    demonstrateWorkerPool()
    demonstratePubSub()
    demonstrateRateLimiting()
    demonstrateContextCancellation()
    demonstrateContextTimeout()
    demonstrateDeadlockDetection()
    demonstrateGracefulShutdown()
    demonstrateResourcePool()
}
```

## Summary

Go concurrency provides:

**Goroutines:**
- Lightweight threads managed by Go runtime
- Simple syntax with `go` keyword
- Efficient stack management
- Communication via channels
- Built-in scheduling

**Channels:**
- Typed communication pipes
- Buffered and unbuffered variants
- Directional channels
- Select statements
- Closing and range operations

**Concurrency Patterns:**
- Fan-out/Fan-in for distribution
- Pipeline for data processing
- Worker pool for task distribution
- Pub/Sub for messaging
- Rate limiting for flow control

**Synchronization:**
- Context for cancellation
- WaitGroup for coordination
- Mutex for mutual exclusion
- Atomic operations
- Once for initialization

**Best Practices:**
- Share memory by communicating
- Use channels for coordination
- Handle cancellation properly
- Avoid data races
- Design for graceful shutdown

**Key Features:**
- CSP-inspired design
- Built-in race detector
- Efficient scheduling
- Memory safety
- Simple primitives

**Common Use Cases:**
- Web servers
- Data processing pipelines
- Concurrent I/O operations
- Background tasks
- Real-time systems

Go's concurrency model provides a simple, powerful, and safe way to build concurrent applications with excellent performance and maintainability.
