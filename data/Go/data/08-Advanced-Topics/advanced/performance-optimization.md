# Go Performance Optimization

## Performance Fundamentals

### Profiling and Benchmarking
```go
package main

import (
    "fmt"
    "runtime"
    "runtime/pprof"
    "os"
    "log"
    "time"
    "sync"
    "testing"
    "bytes"
    "strings"
)

// CPU profiling example
func cpuProfilingExample() {
    // Create CPU profile file
    f, err := os.Create("cpu.prof")
    if err != nil {
        log.Fatal("could not create CPU profile: ", err)
    }
    defer f.Close()
    
    // Start CPU profiling
    if err := pprof.StartCPUProfile(f); err != nil {
        log.Fatal("could not start CPU profile: ", err)
    }
    defer pprof.StopCPUProfile()
    
    // Run code to profile
    fmt.Println("Running CPU profiling example...")
    fibonacci(35)
    fmt.Println("CPU profiling completed")
}

// Memory profiling example
func memoryProfilingExample() {
    // Create memory profile file
    f, err := os.Create("mem.prof")
    if err != nil {
        log.Fatal("could not create memory profile: ", err)
    }
    defer f.Close()
    
    // Run code that allocates memory
    fmt.Println("Running memory profiling example...")
    allocateMemory()
    
    // Write memory profile
    runtime.GC() // get up-to-date statistics
    if err := pprof.WriteHeapProfile(f); err != nil {
        log.Fatal("could not write memory profile: ", err)
    }
    
    fmt.Println("Memory profiling completed")
}

// Block profiling example
func blockProfilingExample() {
    // Enable block profiling
    runtime.SetBlockProfileRate(1)
    
    // Create block profile file
    f, err := os.Create("block.prof")
    if err != nil {
        log.Fatal("could not create block profile: ", err)
    }
    defer f.Close()
    
    // Run code with blocking operations
    fmt.Println("Running block profiling example...")
    blockingOperations()
    
    // Write block profile
    if err := pprof.Lookup("block").WriteTo(f, 0); err != nil {
        log.Fatal("could not write block profile: ", err)
    }
    
    fmt.Println("Block profiling completed")
}

// Functions to profile
func fibonacci(n int) int {
    if n <= 1 {
        return n
    }
    return fibonacci(n-1) + fibonacci(n-2)
}

func allocateMemory() {
    var data [][]byte
    for i := 0; i < 1000; i++ {
        // Allocate 1MB chunks
        chunk := make([]byte, 1024*1024)
        for j := range chunk {
            chunk[j] = byte(j % 256)
        }
        data = append(data, chunk)
    }
    
    // Keep data alive
    runtime.KeepAlive(data)
}

func blockingOperations() {
    var wg sync.WaitGroup
    var mu sync.Mutex
    
    // Create multiple goroutines that block on mutex
    for i := 0; i < 10; i++ {
        wg.Add(1)
        go func(id int) {
            defer wg.Done()
            
            for j := 0; j < 100; j++ {
                mu.Lock()
                time.Sleep(time.Microsecond) // Simulate work
                mu.Unlock()
                
                // Channel blocking
                ch := make(chan bool)
                go func() {
                    time.Sleep(time.Microsecond)
                    ch <- true
                }()
                <-ch
            }
        }(i)
    }
    
    wg.Wait()
}

// Benchmark examples
func BenchmarkStringConcatenation(b *testing.B) {
    b.Run("plus_operator", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            var result string
            for j := 0; j < 100; j++ {
                result += "test"
            }
        }
    })
    
    b.Run("strings_builder", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            var builder strings.Builder
            for j := 0; j < 100; j++ {
                builder.WriteString("test")
            }
            _ = builder.String()
        }
    })
    
    b.Run("bytes_buffer", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            var buffer bytes.Buffer
            for j := 0; j < 100; j++ {
                buffer.WriteString("test")
            }
            _ = buffer.String()
        }
    })
}

func BenchmarkMapOperations(b *testing.B) {
    b.Run("map_insert", func(b *testing.B) {
        m := make(map[int]string)
        for i := 0; i < b.N; i++ {
            m[i] = "value"
        }
    })
    
    b.Run("map_lookup", func(b *testing.B) {
        m := make(map[int]string)
        for i := 0; i < 1000; i++ {
            m[i] = "value"
        }
        
        b.ResetTimer()
        for i := 0; i < b.N; i++ {
            _ = m[i%1000]
        }
    })
    
    b.Run("map_delete", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            m := make(map[int]string)
            for j := 0; j < 100; j++ {
                m[j] = "value"
            }
            
            for j := 0; j < 100; j++ {
                delete(m, j)
            }
        }
    })
}

func BenchmarkSliceOperations(b *testing.B) {
    b.Run("slice_append", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            var slice []int
            for j := 0; j < 100; j++ {
                slice = append(slice, j)
            }
        }
    })
    
    b.Run("slice_prealloc", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            slice := make([]int, 0, 100)
            for j := 0; j < 100; j++ {
                slice = append(slice, j)
            }
        }
    })
    
    b.Run("slice_copy", func(b *testing.B) {
        src := make([]int, 1000)
        for i := range src {
            src[i] = i
        }
        
        b.ResetTimer()
        for i := 0; i < b.N; i++ {
            dst := make([]int, len(src))
            copy(dst, src)
        }
    })
}

// Performance monitoring
func monitorPerformance() {
    var m runtime.MemStats
    runtime.ReadMemStats(&m)
    
    fmt.Printf("Memory Statistics:\n")
    fmt.Printf("  Alloc: %d bytes\n", m.Alloc)
    fmt.Printf("  TotalAlloc: %d bytes\n", m.TotalAlloc)
    fmt.Printf("  Sys: %d bytes\n", m.Sys)
    fmt.Printf("  NumGC: %d\n", m.NumGC)
    fmt.Printf("  Goroutines: %d\n", runtime.NumGoroutine())
    
    // Print GC stats
    fmt.Printf("GC Stats:\n")
    fmt.Printf("  PauseTotalNs: %d\n", m.PauseTotalNs)
    fmt.Printf("  NumGC: %d\n", m.NumGC)
    fmt.Printf("  GCCPUFraction: %.2f\n", m.GCCPUFraction)
}

// Performance comparison
func comparePerformance() {
    sizes := []int{1000, 10000, 100000}
    
    for _, size := range sizes {
        fmt.Printf("\nTesting with size: %d\n", size)
        
        // Test slice vs map for lookups
        data := make([]int, size)
        for i := range data {
            data[i] = i
        }
        
        // Slice lookup
        start := time.Now()
        for i := 0; i < 1000; i++ {
            target := data[i%len(data)]
            for _, v := range data {
                if v == target {
                    break
                }
            }
        }
        sliceTime := time.Since(start)
        
        // Map lookup
        m := make(map[int]bool)
        for _, v := range data {
            m[v] = true
        }
        
        start = time.Now()
        for i := 0; i < 1000; i++ {
            target := data[i%len(data)]
            _ = m[target]
        }
        mapTime := time.Since(start)
        
        fmt.Printf("Slice lookup time: %v\n", sliceTime)
        fmt.Printf("Map lookup time: %v\n", mapTime)
        fmt.Printf("Map speedup: %.2fx\n", float64(sliceTime)/float64(mapTime))
    }
}
```

### Memory Optimization
```go
package main

import (
    "fmt"
    "runtime"
    "sync"
    "unsafe"
    "time"
)

// Memory pool pattern
type BytePool struct {
    pool sync.Pool
}

func NewBytePool(size int) *BytePool {
    return &BytePool{
        pool: sync.Pool{
            New: func() interface{} {
                return make([]byte, size)
            },
        },
    }
}

func (bp *BytePool) Get() []byte {
    return bp.pool.Get().([]byte)
}

func (bp *BytePool) Put(b []byte) {
    bp.pool.Put(b)
}

// Object pool pattern
type ObjectPool struct {
    pool sync.Pool
}

func NewObjectPool() *ObjectPool {
    return &ObjectPool{
        pool: sync.Pool{
            New: func() interface{} {
                return &struct {
                    data [1024]byte
                    id   int
                }{}
            },
        },
    }
}

func (op *ObjectPool) Get() *struct {
    data [1024]byte
    id   int
} {
    return op.pool.Get().(*struct {
        data [1024]byte
        id   int
    })
}

func (op *ObjectPool) Put(obj *struct {
    data [1024]byte
    id   int
}) {
    op.pool.Put(obj)
}

// Memory-efficient string operations
func efficientStringOperations() {
    fmt.Println("=== Memory-Efficient String Operations ===")
    
    // Use string builder for concatenation
    fmt.Println("String concatenation:")
    
    // Bad way - creates many temporary strings
    start := time.Now()
    var result string
    for i := 0; i < 10000; i++ {
        result += fmt.Sprintf("%d", i)
    }
    badTime := time.Since(start)
    
    // Good way - uses builder
    start = time.Now()
    var builder strings.Builder
    for i := 0; i < 10000; i++ {
        builder.WriteString(fmt.Sprintf("%d", i))
    }
    result = builder.String()
    goodTime := time.Since(start)
    
    fmt.Printf("Bad concatenation: %v\n", badTime)
    fmt.Printf("Good concatenation: %v\n", goodTime)
    fmt.Printf("Improvement: %.2fx\n", float64(badTime)/float64(goodTime))
    
    // String interning for repeated strings
    fmt.Println("\nString interning:")
    
    // Create a simple string interner
    interned := make(map[string]string)
    
    intern := func(s string) string {
        if existing, found := interned[s]; found {
            return existing
        }
        interned[s] = s
        return s
    }
    
    // Test with repeated strings
    repeated := []string{"hello", "world", "hello", "go", "world", "hello"}
    
    var internedStrings []string
    for _, s := range repeated {
        internedStrings = append(internedStrings, intern(s))
    }
    
    fmt.Printf("Original strings: %v\n", repeated)
    fmt.Printf("Interned strings: %v\n", internedStrings)
    fmt.Printf("Unique strings: %d\n", len(interned))
}

// Slice optimization
func sliceOptimization() {
    fmt.Println("\n=== Slice Optimization ===")
    
    // Pre-allocate slices when size is known
    fmt.Println("Slice pre-allocation:")
    
    // Bad way - grows dynamically
    start := time.Now()
    var dynamicSlice []int
    for i := 0; i < 100000; i++ {
        dynamicSlice = append(dynamicSlice, i)
    }
    dynamicTime := time.Since(start)
    
    // Good way - pre-allocate
    start = time.Now()
    preallocatedSlice := make([]int, 0, 100000)
    for i := 0; i < 100000; i++ {
        preallocatedSlice = append(preallocatedSlice, i)
    }
    preallocatedTime := time.Since(start)
    
    fmt.Printf("Dynamic slice: %v\n", dynamicTime)
    fmt.Printf("Pre-allocated slice: %v\n", preallocatedTime)
    fmt.Printf("Improvement: %.2fx\n", float64(dynamicTime)/float64(preallocatedTime))
    
    // Reuse slices to reduce allocations
    fmt.Println("\nSlice reuse:")
    
    // Use a buffer pool
    bufferPool := &sync.Pool{
        New: func() interface{} {
            return make([]int, 0, 1000)
        },
    }
    
    start = time.Now()
    for i := 0; i < 1000; i++ {
        buffer := bufferPool.Get().([]int)
        buffer = buffer[:0] // Reset length
        
        for j := 0; j < 100; j++ {
            buffer = append(buffer, j)
        }
        
        // Process buffer...
        _ = buffer
        
        bufferPool.Put(buffer)
    }
    reuseTime := time.Since(start)
    
    fmt.Printf("Slice reuse time: %v\n", reuseTime)
}

// Map optimization
func mapOptimization() {
    fmt.Println("\n=== Map Optimization ===")
    
    // Pre-allocate maps when size is known
    fmt.Println("Map pre-allocation:")
    
    // Bad way - grows dynamically
    start := time.Now()
    dynamicMap := make(map[int]string)
    for i := 0; i < 100000; i++ {
        dynamicMap[i] = fmt.Sprintf("value_%d", i)
    }
    dynamicTime := time.Since(start)
    
    // Good way - pre-allocate
    start = time.Now()
    preallocatedMap := make(map[int]string, 100000)
    for i := 0; i < 100000; i++ {
        preallocatedMap[i] = fmt.Sprintf("value_%d", i)
    }
    preallocatedTime := time.Since(start)
    
    fmt.Printf("Dynamic map: %v\n", dynamicTime)
    fmt.Printf("Pre-allocated map: %v\n", preallocatedTime)
    fmt.Printf("Improvement: %.2fx\n", float64(dynamicTime)/float64(preallocatedTime))
    
    // Use value types for small keys and values
    fmt.Println("\nValue vs pointer types:")
    
    // Map with struct values
    type SmallStruct struct {
        ID   int
        Name string
    }
    
    start = time.Now()
    structMap := make(map[int]SmallStruct, 10000)
    for i := 0; i < 10000; i++ {
        structMap[i] = SmallStruct{ID: i, Name: fmt.Sprintf("name_%d", i)}
    }
    structTime := time.Since(start)
    
    // Map with pointer values
    start = time.Now()
    pointerMap := make(map[int]*SmallStruct, 10000)
    for i := 0; i < 10000; i++ {
        pointerMap[i] = &SmallStruct{ID: i, Name: fmt.Sprintf("name_%d", i)}
    }
    pointerTime := time.Since(start)
    
    fmt.Printf("Struct map: %v\n", structTime)
    fmt.Printf("Pointer map: %v\n", pointerTime)
    
    // Use pointer map for large structs
    type LargeStruct struct {
        Data [1000]int
        ID   int
        Name string
    }
    
    start = time.Now()
    largeStructMap := make(map[int]LargeStruct, 1000)
    for i := 0; i < 1000; i++ {
        largeStructMap[i] = LargeStruct{ID: i, Name: fmt.Sprintf("name_%d", i)}
    }
    largeStructTime := time.Since(start)
    
    start = time.Now()
    largePointerMap := make(map[int]*LargeStruct, 1000)
    for i := 0; i < 1000; i++ {
        largePointerMap[i] = &LargeStruct{ID: i, Name: fmt.Sprintf("name_%d", i)}
    }
    largePointerTime := time.Since(start)
    
    fmt.Printf("Large struct map: %v\n", largeStructTime)
    fmt.Printf("Large pointer map: %v\n", largePointerTime)
    fmt.Printf("Large pointer improvement: %.2fx\n", float64(largeStructTime)/float64(largePointerTime))
}

// Zero-copy operations
func zeroCopyOperations() {
    fmt.Println("\n=== Zero-Copy Operations ===")
    
    // Use unsafe for zero-copy string conversion (use with caution)
    data := []byte("hello world")
    
    // Safe way - creates copy
    start := time.Now()
    for i := 0; i < 1000000; i++ {
        _ = string(data)
    }
    safeTime := time.Since(start)
    
    // Unsafe way - no copy
    start = time.Now()
    for i := 0; i < 1000000; i++ {
        _ = *(*string)(unsafe.Pointer(&data))
    }
    unsafeTime := time.Since(start)
    
    fmt.Printf("Safe string conversion: %v\n", safeTime)
    fmt.Printf("Unsafe string conversion: %v\n", unsafeTime)
    fmt.Printf("Unsafe improvement: %.2fx\n", float64(safeTime)/float64(unsafeTime))
    
    // Use io.Copy for efficient data transfer
    fmt.Println("\nEfficient data transfer:")
    
    // Simulate large data transfer
    largeData := make([]byte, 1024*1024) // 1MB
    for i := range largeData {
        largeData[i] = byte(i % 256)
    }
    
    // Copy with append
    start = time.Now()
    var result []byte
    for i := 0; i < 100; i++ {
        result = append(result, largeData...)
    }
    appendTime := time.Since(start)
    
    // Copy with pre-allocation
    start = time.Now()
    result2 := make([]byte, 0, len(largeData)*100)
    for i := 0; i < 100; i++ {
        result2 = append(result2, largeData...)
    }
    preallocTime := time.Since(start)
    
    fmt.Printf("Append copy: %v\n", appendTime)
    fmt.Printf("Pre-allocated copy: %v\n", preallocTime)
    fmt.Printf("Pre-alloc improvement: %.2fx\n", float64(appendTime)/float64(preallocTime))
}

// Memory leak detection
func memoryLeakDetection() {
    fmt.Println("\n=== Memory Leak Detection ===")
    
    // Monitor memory usage
    var initialMem runtime.MemStats
    runtime.ReadMemStats(&initialMem)
    
    // Simulate memory leak
    var leakyData [][]byte
    for i := 0; i < 1000; i++ {
        leakyData = append(leakyData, make([]byte, 1024*1024)) // 1MB each
    }
    
    var leakMem runtime.MemStats
    runtime.ReadMemStats(&leakMem)
    
    fmt.Printf("Initial memory: %d MB\n", initialMem.Alloc/1024/1024)
    fmt.Printf("After leak: %d MB\n", leakMem.Alloc/1024/1024)
    fmt.Printf("Memory increase: %d MB\n", (leakMem.Alloc-initialMem.Alloc)/1024/1024)
    
    // Clear leaky data
    leakyData = nil
    runtime.GC()
    
    var finalMem runtime.MemStats
    runtime.ReadMemStats(&finalMem)
    
    fmt.Printf("After GC: %d MB\n", finalMem.Alloc/1024/1024)
    
    // Detect goroutine leaks
    fmt.Println("\nGoroutine leak detection:")
    
    initialGoroutines := runtime.NumGoroutine()
    fmt.Printf("Initial goroutines: %d\n", initialGoroutines)
    
    // Create goroutines that never exit (leak)
    for i := 0; i < 10; i++ {
        go func() {
            time.Sleep(time.Hour) // Never exits
        }()
    }
    
    leakGoroutines := runtime.NumGoroutine()
    fmt.Printf("After leak: %d\n", leakGoroutines)
    fmt.Printf("Goroutine increase: %d\n", leakGoroutines-initialGoroutines)
}

// Memory optimization examples
func memoryOptimizationExamples() {
    // Print initial memory stats
    monitorPerformance()
    
    // Run optimization examples
    efficientStringOperations()
    sliceOptimization()
    mapOptimization()
    zeroCopyOperations()
    memoryLeakDetection()
    
    // Final memory stats
    fmt.Println("\n=== Final Memory Statistics ===")
    monitorPerformance()
}
```

## Concurrency Optimization

### Goroutine and Channel Optimization
```go
package main

import (
    "fmt"
    "runtime"
    "sync"
    "time"
    "context"
)

// Worker pool optimization
type OptimizedWorkerPool struct {
    workers    int
    jobQueue   chan Job
    resultChan chan Result
    workerPool chan chan Job
    quit       chan bool
    wg         sync.WaitGroup
}

type Job struct {
    ID     int
    Data   interface{}
    Result chan Result
}

type Result struct {
    JobID  int
    Output interface{}
    Error  error
}

func NewOptimizedWorkerPool(workers, queueSize int) *OptimizedWorkerPool {
    return &OptimizedWorkerPool{
        workers:    workers,
        jobQueue:   make(chan Job, queueSize),
        resultChan: make(chan Result, queueSize),
        workerPool: make(chan chan Job, workers),
        quit:       make(chan bool),
    }
}

func (wp *OptimizedWorkerPool) Start() {
    // Start workers
    for i := 0; i < wp.workers; i++ {
        worker := NewWorker(i+1, wp.workerPool)
        worker.Start()
    }
    
    // Start dispatcher
    go wp.dispatch()
    
    // Start result collector
    go wp.collectResults()
}

func (wp *OptimizedWorkerPool) Stop() {
    close(wp.quit)
    wp.wg.Wait()
    close(wp.jobQueue)
    close(wp.resultChan)
}

func (wp *OptimizedWorkerPool) dispatch() {
    for {
        select {
        case job := <-wp.jobQueue:
            // Get available worker
            workerChan := <-wp.workerPool
            workerChan <- job
        case <-wp.quit:
            return
        }
    }
}

func (wp *OptimizedWorkerPool) collectResults() {
    for {
        select {
        case result := <-wp.resultChan:
            // Handle result
            fmt.Printf("Job %d completed: %v\n", result.JobID, result.Output)
        case <-wp.quit:
            return
        }
    }
}

func (wp *OptimizedWorkerPool) Submit(job Job) {
    wp.jobQueue <- job
}

type Worker struct {
    id       int
    jobChan  chan Job
    workerPool chan chan Job
    quit     chan bool
}

func NewWorker(id int, workerPool chan chan Job) *Worker {
    return &Worker{
        id:        id,
        jobChan:   make(chan Job),
        workerPool: workerPool,
        quit:      make(chan bool),
    }
}

func (w *Worker) Start() {
    go func() {
        for {
            // Register worker
            w.workerPool <- w.jobChan
            
            select {
            case job := <-w.jobChan:
                // Process job
                result := w.processJob(job)
                if job.Result != nil {
                    job.Result <- result
                }
            case <-w.quit:
                return
            }
        }
    }()
}

func (w *Worker) Stop() {
    close(w.quit)
}

func (w *Worker) processJob(job Job) Result {
    // Simulate work
    time.Sleep(time.Millisecond * 10)
    
    return Result{
        JobID:  job.ID,
        Output: fmt.Sprintf("Processed by worker %d", w.id),
        Error:  nil,
    }
}

// Channel optimization
func channelOptimization() {
    fmt.Println("=== Channel Optimization ===")
    
    // Buffered vs unbuffered channels
    fmt.Println("Buffered vs unbuffered channels:")
    
    // Unbuffered channel
    start := time.Now()
    unbuffered := make(chan int)
    
    go func() {
        for i := 0; i < 10000; i++ {
            unbuffered <- i
        }
        close(unbuffered)
    }()
    
    count := 0
    for range unbuffered {
        count++
    }
    unbufferedTime := time.Since(start)
    
    // Buffered channel
    start = time.Now()
    buffered := make(chan int, 1000)
    
    go func() {
        for i := 0; i < 10000; i++ {
            buffered <- i
        }
        close(buffered)
    }()
    
    count = 0
    for range buffered {
        count++
    }
    bufferedTime := time.Since(start)
    
    fmt.Printf("Unbuffered channel: %v\n", unbufferedTime)
    fmt.Printf("Buffered channel: %v\n", bufferedTime)
    fmt.Printf("Buffered improvement: %.2fx\n", float64(unbufferedTime)/float64(bufferedTime))
    
    // Channel pooling
    fmt.Println("\nChannel pooling:")
    
    type ChannelPool struct {
        pool chan chan int
        mu   sync.Mutex
    }
    
    NewChannelPool := func(size int) *ChannelPool {
        return &ChannelPool{
            pool: make(chan chan int, size),
        }
    }
    
    (cp *ChannelPool) Get() chan int {
        select {
        case ch := <-cp.pool:
            return ch
        default:
            return make(chan int, 1)
        }
    }
    
    (cp *ChannelPool) Put(ch chan int) {
        select {
        case cp.pool <- ch:
        default:
            // Pool full, discard
        }
    }
    
    pool := NewChannelPool(10)
    
    start = time.Now()
    for i := 0; i < 1000; i++ {
        ch := pool.Get()
        go func(c chan int, id int) {
            c <- id
            pool.Put(c)
        }(ch, i)
        <-ch
    }
    poolTime := time.Since(start)
    
    fmt.Printf("Channel pool time: %v\n", poolTime)
}

// Goroutine optimization
func goroutineOptimization() {
    fmt.Println("\n=== Goroutine Optimization ===")
    
    // Goroutine pooling
    fmt.Println("Goroutine pooling:")
    
    type GoroutinePool struct {
        tasks chan func()
        wg    sync.WaitGroup
    }
    
    NewGoroutinePool := func(size int) *GoroutinePool {
        pool := &GoroutinePool{
            tasks: make(chan func(), size*2),
        }
        
        for i := 0; i < size; i++ {
            pool.wg.Add(1)
            go pool.worker()
        }
        
        return pool
    }
    
    (gp *GoroutinePool) worker() {
        defer gp.wg.Done()
        for task := range gp.tasks {
            task()
        }
    }
    
    (gp *GoroutinePool) Submit(task func()) {
        gp.tasks <- task
    }
    
    (gp *GoroutinePool) Stop() {
        close(gp.tasks)
        gp.wg.Wait()
    }
    
    // Test goroutine pool
    pool := NewGoroutinePool(10)
    defer pool.Stop()
    
    start := time.Now()
    var wg sync.WaitGroup
    
    for i := 0; i < 1000; i++ {
        wg.Add(1)
        pool.Submit(func() {
            defer wg.Done()
            time.Sleep(time.Microsecond)
        })
    }
    
    wg.Wait()
    poolTime := time.Since(start)
    
    // Compare with creating new goroutines
    start = time.Now()
    wg = sync.WaitGroup{}
    
    for i := 0; i < 1000; i++ {
        wg.Add(1)
        go func() {
            defer wg.Done()
            time.Sleep(time.Microsecond)
        }()
    }
    
    wg.Wait()
    goroutineTime := time.Since(start)
    
    fmt.Printf("Goroutine pool: %v\n", poolTime)
    fmt.Printf("Individual goroutines: %v\n", goroutineTime)
    fmt.Printf("Pool improvement: %.2fx\n", float64(goroutineTime)/float64(poolTime))
    
    // Goroutine lifecycle management
    fmt.Println("\nGoroutine lifecycle management:")
    
    // Use context for cancellation
    ctx, cancel := context.WithCancel(context.Background())
    
    start = time.Now()
    var wg2 sync.WaitGroup
    
    for i := 0; i < 100; i++ {
        wg2.Add(1)
        go func(id int) {
            defer wg2.Done()
            
            select {
            case <-ctx.Done():
                return
            case <-time.After(time.Millisecond * 10):
                // Work completed
            }
        }(i)
    }
    
    // Cancel after 5ms
    time.Sleep(time.Millisecond * 5)
    cancel()
    
    wg2.Wait()
    cancelTime := time.Since(start)
    
    fmt.Printf("Context cancellation time: %v\n", cancelTime)
}

// Parallel processing optimization
func parallelProcessingOptimization() {
    fmt.Println("\n=== Parallel Processing Optimization ===")
    
    // Data parallelism
    data := make([]int, 1000000)
    for i := range data {
        data[i] = i
    }
    
    // Sequential processing
    start := time.Now()
    sequentialSum := 0
    for _, v := range data {
        sequentialSum += v
    }
    sequentialTime := time.Since(start)
    
    // Parallel processing
    start = time.Now()
    var parallelSum int64
    var wg sync.WaitGroup
    
    numWorkers := runtime.NumCPU()
    chunkSize := len(data) / numWorkers
    
    for i := 0; i < numWorkers; i++ {
        wg.Add(1)
        start := i * chunkSize
        end := start + chunkSize
        if i == numWorkers-1 {
            end = len(data)
        }
        
        go func(chunk []int) {
            defer wg.Done()
            localSum := int64(0)
            for _, v := range chunk {
                localSum += int64(v)
            }
            atomic.AddInt64(&parallelSum, localSum)
        }(data[start:end])
    }
    
    wg.Wait()
    parallelTime := time.Since(start)
    
    fmt.Printf("Sequential sum: %d (time: %v)\n", sequentialSum, sequentialTime)
    fmt.Printf("Parallel sum: %d (time: %v)\n", parallelSum, parallelTime)
    fmt.Printf("Parallel improvement: %.2fx\n", float64(sequentialTime)/float64(parallelTime))
    
    // Pipeline pattern
    fmt.Println("\nPipeline pattern:")
    
    type PipelineStage struct {
        name string
        process func(interface{}) interface{}
    }
    
    func runPipeline(stages []PipelineStage, input interface{}) interface{} {
        current := input
        
        for _, stage := range stages {
            start := time.Now()
            current = stage.process(current)
            fmt.Printf("Stage %s: %v\n", stage.name, time.Since(start))
        }
        
        return current
    }
    
    // Create pipeline stages
    stages := []PipelineStage{
        {
            name: "generate",
            process: func(input interface{}) interface{} {
                n := input.(int)
                result := make([]int, n)
                for i := range result {
                    result[i] = i
                }
                return result
            },
        },
        {
            name: "filter",
            process: func(input interface{}) interface{} {
                data := input.([]int)
                var result []int
                for _, v := range data {
                    if v%2 == 0 {
                        result = append(result, v)
                    }
                }
                return result
            },
        },
        {
            name: "transform",
            process: func(input interface{}) interface{} {
                data := input.([]int)
                for i, v := range data {
                    data[i] = v * 2
                }
                return data
            },
        },
        {
            name: "reduce",
            process: func(input interface{}) interface{} {
                data := input.([]int)
                sum := 0
                for _, v := range data {
                    sum += v
                }
                return sum
            },
        },
    }
    
    start = time.Now()
    result := runPipeline(stages, 100000)
    pipelineTime := time.Since(start)
    
    fmt.Printf("Pipeline result: %v (time: %v)\n", result, pipelineTime)
}

// Lock optimization
func lockOptimization() {
    fmt.Println("\n=== Lock Optimization ===")
    
    // Compare different lock types
    data := make([]int, 10000)
    
    // Mutex
    var mu sync.Mutex
    start := time.Now()
    
    for i := 0; i < 1000; i++ {
        go func(id int) {
            mu.Lock()
            data[id%len(data)] = id
            mu.Unlock()
        }(i)
    }
    
    time.Sleep(time.Millisecond * 100) // Wait for goroutines
    mutexTime := time.Since(start)
    
    // RWMutex (for read-heavy workloads)
    var rwMu sync.RWMutex
    start = time.Now()
    
    for i := 0; i < 1000; i++ {
        go func(id int) {
            if id%2 == 0 {
                rwMu.Lock()
                data[id%len(data)] = id
                rwMu.Unlock()
            } else {
                rwMu.RLock()
                _ = data[id%len(data)]
                rwMu.RUnlock()
            }
        }(i)
    }
    
    time.Sleep(time.Millisecond * 100)
    rwMutexTime := time.Since(start)
    
    fmt.Printf("Mutex time: %v\n", mutexTime)
    fmt.Printf("RWMutex time: %v\n", rwMutexTime)
    
    // Atomic operations
    var counter int64
    start = time.Now()
    
    for i := 0; i < 10000; i++ {
        go func() {
            atomic.AddInt64(&counter, 1)
        }()
    }
    
    time.Sleep(time.Millisecond * 10)
    atomicTime := time.Since(start)
    
    fmt.Printf("Atomic operations time: %v\n", atomicTime)
    fmt.Printf("Final counter: %d\n", atomic.LoadInt64(&counter))
}

// Concurrency optimization examples
func concurrencyOptimizationExamples() {
    // Set GOMAXPROCS
    runtime.GOMAXPROCS(runtime.NumCPU())
    
    fmt.Printf("Number of CPUs: %d\n", runtime.NumCPU())
    fmt.Printf("GOMAXPROCS: %d\n", runtime.GOMAXPROCS(0))
    
    // Run optimization examples
    channelOptimization()
    goroutineOptimization()
    parallelProcessingOptimization()
    lockOptimization()
}
```

## Algorithm Optimization

### Efficient Algorithms and Data Structures
```go
package main

import (
    "fmt"
    "sort"
    "time"
    "math"
)

// Sorting optimization
func sortingOptimization() {
    fmt.Println("=== Sorting Optimization ===")
    
    // Generate test data
    sizes := []int{1000, 10000, 100000}
    
    for _, size := range sizes {
        fmt.Printf("\nTesting with size: %d\n", size)
        
        // Generate random data
        data := make([]int, size)
        for i := range data {
            data[i] = rand.Intn(size * 10)
        }
        
        // Test different sorting algorithms
        
        // Bubble sort (inefficient)
        bubbleData := make([]int, len(data))
        copy(bubbleData, data)
        
        start := time.Now()
        bubbleSort(bubbleData)
        bubbleTime := time.Since(start)
        
        // Quick sort
        quickData := make([]int, len(data))
        copy(quickData, data)
        
        start = time.Now()
        quickSort(quickData)
        quickTime := time.Since(start)
        
        // Built-in sort
        builtinData := make([]int, len(data))
        copy(builtinData, data)
        
        start = time.Now()
        sort.Ints(builtinData)
        builtinTime := time.Since(start)
        
        fmt.Printf("Bubble sort: %v\n", bubbleTime)
        fmt.Printf("Quick sort: %v\n", quickTime)
        fmt.Printf("Built-in sort: %v\n", builtinTime)
        fmt.Printf("Quick vs Bubble: %.2fx faster\n", float64(bubbleTime)/float64(quickTime))
        fmt.Printf("Built-in vs Quick: %.2fx faster\n", float64(quickTime)/float64(builtinTime))
    }
}

func bubbleSort(arr []int) {
    n := len(arr)
    for i := 0; i < n-1; i++ {
        for j := 0; j < n-i-1; j++ {
            if arr[j] > arr[j+1] {
                arr[j], arr[j+1] = arr[j+1], arr[j]
            }
        }
    }
}

func quickSort(arr []int) {
    if len(arr) <= 1 {
        return
    }
    
    pivot := arr[len(arr)/2]
    left, right := 0, len(arr)-1
    
    for left <= right {
        for arr[left] < pivot {
            left++
        }
        for arr[right] > pivot {
            right--
        }
        if left <= right {
            arr[left], arr[right] = arr[right], arr[left]
            left++
            right--
        }
    }
    
    quickSort(arr[:right+1])
    quickSort(arr[left:])
}

// Search optimization
func searchOptimization() {
    fmt.Println("\n=== Search Optimization ===")
    
    // Generate sorted data
    data := make([]int, 100000)
    for i := range data {
        data[i] = i * 2
    }
    
    targets := []int{0, 50000, 99999, 100001}
    
    for _, target := range targets {
        fmt.Printf("Searching for %d:\n", target)
        
        // Linear search
        start := time.Now()
        found := false
        for _, v := range data {
            if v == target {
                found = true
                break
            }
        }
        linearTime := time.Since(start)
        
        // Binary search
        start = time.Now()
        left, right := 0, len(data)-1
        binaryFound := false
        
        for left <= right {
            mid := left + (right-left)/2
            if data[mid] == target {
                binaryFound = true
                break
            } else if data[mid] < target {
                left = mid + 1
            } else {
                right = mid - 1
            }
        }
        binaryTime := time.Since(start)
        
        fmt.Printf("  Linear search: %v (found: %t)\n", linearTime, found)
        fmt.Printf("  Binary search: %v (found: %t)\n", binaryTime, binaryFound)
        
        if linearTime > 0 {
            fmt.Printf("  Binary improvement: %.2fx faster\n", float64(linearTime)/float64(binaryTime))
        }
    }
}

// Data structure optimization
func dataStructureOptimization() {
    fmt.Println("\n=== Data Structure Optimization ===")
    
    // Compare different data structures for common operations
    
    // Array vs Slice vs Map for lookups
    fmt.Println("Lookup performance:")
    
    size := 10000
    data := make([]int, size)
    for i := range data {
        data[i] = i
    }
    
    // Array lookup
    start := time.Now()
    for i := 0; i < 1000; i++ {
        target := data[rand.Intn(len(data))]
        for _, v := range data {
            if v == target {
                break
            }
        }
    }
    arrayTime := time.Since(start)
    
    // Map lookup
    m := make(map[int]bool)
    for _, v := range data {
        m[v] = true
    }
    
    start = time.Now()
    for i := 0; i < 1000; i++ {
        target := data[rand.Intn(len(data))]
        _ = m[target]
    }
    mapTime := time.Since(start)
    
    fmt.Printf("Array lookup: %v\n", arrayTime)
    fmt.Printf("Map lookup: %v\n", mapTime)
    fmt.Printf("Map improvement: %.2fx faster\n", float64(arrayTime)/float64(mapTime))
    
    // Slice vs Map for insertion
    fmt.Println("\nInsertion performance:")
    
    // Slice insertion (at end)
    start = time.Now()
    var slice []int
    for i := 0; i < 10000; i++ {
        slice = append(slice, i)
    }
    sliceInsertTime := time.Since(start)
    
    // Map insertion
    start = time.Now()
    insertMap := make(map[int]bool)
    for i := 0; i < 10000; i++ {
        insertMap[i] = true
    }
    mapInsertTime := time.Since(start)
    
    fmt.Printf("Slice insertion: %v\n", sliceInsertTime)
    fmt.Printf("Map insertion: %v\n", mapInsertTime)
    
    // String operations optimization
    fmt.Println("\nString operations optimization:")
    
    // String concatenation vs builder
    strings := make([]string, 1000)
    for i := range strings {
        strings[i] = fmt.Sprintf("string_%d", i)
    }
    
    // Concatenation
    start = time.Now()
    var result string
    for _, s := range strings {
        result += s
    }
    concatTime := time.Since(start)
    
    // Builder
    start = time.Now()
    var builder strings.Builder
    for _, s := range strings {
        builder.WriteString(s)
    }
    result = builder.String()
    builderTime := time.Since(start)
    
    fmt.Printf("String concatenation: %v\n", concatTime)
    fmt.Printf("String builder: %v\n", builderTime)
    fmt.Printf("Builder improvement: %.2fx faster\n", float64(concatTime)/float64(builderTime))
}

// Mathematical optimization
func mathematicalOptimization() {
    fmt.Println("\n=== Mathematical Optimization ===")
    
    // Fibonacci sequence optimization
    
    // Recursive (inefficient)
    start := time.Now()
    fibRecursive(30)
    recursiveTime := time.Since(start)
    
    // Memoized
    memo := make(map[int]int)
    start = time.Now()
    fibMemoized(30, memo)
    memoizedTime := time.Since(start)
    
    // Iterative
    start = time.Now()
    fibIterative(30)
    iterativeTime := time.Since(start)
    
    fmt.Printf("Recursive Fibonacci: %v\n", recursiveTime)
    fmt.Printf("Memoized Fibonacci: %v\n", memoizedTime)
    fmt.Printf("Iterative Fibonacci: %v\n", iterativeTime)
    fmt.Printf("Memoized vs Recursive: %.2fx faster\n", float64(recursiveTime)/float64(memoizedTime))
    fmt.Printf("Iterative vs Memoized: %.2fx faster\n", float64(memoizedTime)/float64(iterativeTime))
    
    // Prime number generation
    fmt.Println("\nPrime number generation:")
    
    limit := 100000
    
    // Simple sieve
    start = time.Now()
    simpleSieve(limit)
    simpleSieveTime := time.Since(start)
    
    // Optimized sieve
    start = time.Now()
    optimizedSieve(limit)
    optimizedSieveTime := time.Since(start)
    
    fmt.Printf("Simple sieve: %v\n", simpleSieveTime)
    fmt.Printf("Optimized sieve: %v\n", optimizedSieveTime)
    fmt.Printf("Optimized improvement: %.2fx faster\n", float64(simpleSieveTime)/float64(optimizedSieveTime))
}

func fibRecursive(n int) int {
    if n <= 1 {
        return n
    }
    return fibRecursive(n-1) + fibRecursive(n-2)
}

func fibMemoized(n int, memo map[int]int) int {
    if n <= 1 {
        return n
    }
    
    if val, exists := memo[n]; exists {
        return val
    }
    
    memo[n] = fibMemoized(n-1, memo) + fibMemoized(n-2, memo)
    return memo[n]
}

func fibIterative(n int) int {
    if n <= 1 {
        return n
    }
    
    a, b := 0, 1
    for i := 2; i <= n; i++ {
        a, b = b, a+b
    }
    
    return b
}

func simpleSieve(limit int) []int {
    if limit < 2 {
        return []int{}
    }
    
    isPrime := make([]bool, limit+1)
    for i := 2; i <= limit; i++ {
        isPrime[i] = true
    }
    
    for i := 2; i*i <= limit; i++ {
        if isPrime[i] {
            for j := i * i; j <= limit; j += i {
                isPrime[j] = false
            }
        }
    }
    
    var primes []int
    for i := 2; i <= limit; i++ {
        if isPrime[i] {
            primes = append(primes, i)
        }
    }
    
    return primes
}

func optimizedSieve(limit int) []int {
    if limit < 2 {
        return []int{}
    }
    
    // Only consider odd numbers
    size := (limit + 1) / 2
    isPrime := make([]bool, size)
    
    // Mark all odd numbers as prime initially
    for i := range isPrime {
        isPrime[i] = true
    }
    
    for i := 1; i*i <= limit; i += 2 {
        if isPrime[i/2] {
            for j := i * i; j <= limit; j += 2 * i {
                isPrime[j/2] = false
            }
        }
    }
    
    var primes []int
    primes = append(primes, 2) // 2 is the only even prime
    
    for i := 1; i < size; i++ {
        if isPrime[i] {
            primes = append(primes, 2*i+1)
        }
    }
    
    return primes
}

// Cache optimization
func cacheOptimization() {
    fmt.Println("\n=== Cache Optimization ===")
    
    // LRU Cache implementation
    type LRUCache struct {
        capacity int
        cache    map[int]*Node
        head     *Node
        tail     *Node
    }
    
    type Node struct {
        key   int
        value int
        prev  *Node
        next  *Node
    }
    
    NewLRUCache := func(capacity int) *LRUCache {
        head := &Node{}
        tail := &Node{}
        head.next = tail
        tail.prev = head
        
        return &LRUCache{
            capacity: capacity,
            cache:    make(map[int]*Node),
            head:     head,
            tail:     tail,
        }
    }
    
    (lru *LRUCache) Get(key int) int {
        if node, exists := lru.cache[key]; exists {
            lru.moveToHead(node)
            return node.value
        }
        return -1
    }
    
    (lru *LRUCache) Put(key, value int) {
        if node, exists := lru.cache[key]; exists {
            node.value = value
            lru.moveToHead(node)
        } else {
            node := &Node{key: key, value: value}
            lru.cache[key] = node
            lru.addToHead(node)
            
            if len(lru.cache) > lru.capacity {
                lru.removeTail()
            }
        }
    }
    
    (lru *LRUCache) moveToHead(node *Node) {
        lru.removeNode(node)
        lru.addToHead(node)
    }
    
    (lru *LRUCache) addToHead(node *Node) {
        node.prev = lru.head
        node.next = lru.head.next
        lru.head.next.prev = node
        lru.head.next = node
    }
    
    (lru *LRUCache) removeNode(node *Node) {
        node.prev.next = node.next
        node.next.prev = node.prev
    }
    
    (lru *LRUCache) removeTail() {
        last := lru.tail.prev
        lru.removeNode(last)
        delete(lru.cache, last.key)
    }
    
    // Test cache performance
    cache := NewLRUCache(1000)
    
    // Cache hits
    start := time.Now()
    for i := 0; i < 10000; i++ {
        key := i % 1000
        cache.Put(key, key*2)
        cache.Get(key)
    }
    cacheTime := time.Since(start)
    
    // No cache (direct computation)
    start = time.Now()
    for i := 0; i < 10000; i++ {
        key := i % 1000
        _ = key * 2 // Simulate computation
    }
    noCacheTime := time.Since(start)
    
    fmt.Printf("With cache: %v\n", cacheTime)
    fmt.Printf("Without cache: %v\n", noCacheTime)
    
    if noCacheTime > cacheTime {
        fmt.Printf("Cache improvement: %.2fx faster\n", float64(noCacheTime)/float64(cacheTime))
    }
}

// Algorithm optimization examples
func algorithmOptimizationExamples() {
    sortingOptimization()
    searchOptimization()
    dataStructureOptimization()
    mathematicalOptimization()
    cacheOptimization()
}
```

## Summary

Go performance optimization provides:

**Profiling Tools:**
- CPU profiling with pprof
- Memory profiling
- Block profiling
- Trace profiling
- Benchmark testing

**Memory Optimization:**
- Object pooling
- Slice pre-allocation
- Map optimization
- Zero-copy operations
- Memory leak detection

**Concurrency Optimization:**
- Worker pools
- Channel optimization
- Goroutine pooling
- Lock optimization
- Parallel processing

**Algorithm Optimization:**
- Efficient sorting
- Search algorithms
- Data structure selection
- Mathematical optimizations
- Caching strategies

**Key Features:**
- Built-in profiling tools
- Efficient garbage collection
- Concurrent programming support
- Memory management
- Performance monitoring

**Best Practices:**
- Profile before optimizing
- Use appropriate data structures
- Minimize allocations
- Optimize hot paths
- Consider concurrency

**Common Optimizations:**
- String building vs concatenation
- Slice pre-allocation
- Map vs slice for lookups
- Worker pools
- Caching frequently used data

**Performance Monitoring:**
- Runtime statistics
- Memory usage tracking
- Goroutine monitoring
- GC performance
- Benchmark comparisons

Go provides excellent tools and techniques for performance optimization, with built-in profiling, efficient memory management, and powerful concurrency features.
