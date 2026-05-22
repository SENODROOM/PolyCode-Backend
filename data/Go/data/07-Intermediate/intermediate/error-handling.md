# Go Error Handling

## Error Basics

### Understanding Go Errors
```go
package main

import (
    "fmt"
    "errors"
    "os"
    "strconv"
)

func main() {
    // Basic error creation
    func demonstrateBasicErrors() {
        fmt.Println("=== Basic Errors ===")
        
        // Create error with errors.New
        err1 := errors.New("something went wrong")
        fmt.Printf("Error 1: %v\n", err1)
        fmt.Printf("Error 1 type: %T\n", err1)
        
        // Create error with fmt.Errorf
        err2 := fmt.Errorf("operation failed: %w", err1)
        fmt.Printf("Error 2: %v\n", err2)
        
        // Error comparison
        err3 := errors.New("something went wrong")
        fmt.Printf("err1 == err3: %t\n", err1 == err3)
        
        // Check if error is nil
        var err4 error
        fmt.Printf("err4 is nil: %t\n", err4 == nil)
    }
    
    // Error handling patterns
    func demonstrateErrorHandling() {
        fmt.Println("\n=== Error Handling Patterns ===")
        
        // Function that returns error
        divide := func(a, b float64) (float64, error) {
            if b == 0 {
                return 0, errors.New("division by zero")
            }
            return a / b, nil
        }
        
        // Handle error immediately
        result, err := divide(10, 2)
        if err != nil {
            fmt.Printf("Error: %v\n", err)
        } else {
            fmt.Printf("Result: %.2f\n", result)
        }
        
        // Handle error with return
        result, err = divide(10, 0)
        if err != nil {
            fmt.Printf("Error: %v\n", err)
            return
        }
        fmt.Printf("Result: %.2f\n", result)
        
        // Handle error with panic (rarely used)
        func mustDivide(a, b float64) float64 {
            result, err := divide(a, b)
            if err != nil {
                panic(err)
            }
            return result
        }
        
        defer func() {
            if r := recover(); r != nil {
                fmt.Printf("Recovered from panic: %v\n", r)
            }
        }()
        
        result = mustDivide(10, 0)
        fmt.Printf("Result: %.2f\n", result)
    }
    
    // Custom error types
    func demonstrateCustomErrors() {
        fmt.Println("\n=== Custom Error Types ===")
        
        // Custom error type
        type ValidationError struct {
            Field   string
            Message string
            Code    int
        }
        
        func (ve ValidationError) Error() string {
            return fmt.Sprintf("Validation Error [%d]: %s - %s", ve.Code, ve.Field, ve.Message)
        }
        
        // Function that returns custom error
        validateAge := func(age int) error {
            if age < 0 {
                return ValidationError{
                    Field:   "age",
                    Message: "age cannot be negative",
                    Code:    1001,
                }
            }
            if age > 120 {
                return ValidationError{
                    Field:   "age",
                    Message: "age seems unrealistic",
                    Code:    1002,
                }
            }
            return nil
        }
        
        // Handle custom error
        err := validateAge(-5)
        if err != nil {
            fmt.Printf("Error: %v\n", err)
            
            // Type assertion
            if validationErr, ok := err.(ValidationError); ok {
                fmt.Printf("Field: %s\n", validationErr.Field)
                fmt.Printf("Message: %s\n", validationErr.Message)
                fmt.Printf("Code: %d\n", validationErr.Code)
            }
        }
        
        err = validateAge(25)
        if err != nil {
            fmt.Printf("Error: %v\n", err)
        } else {
            fmt.Println("Age is valid")
        }
    }
    
    // Error wrapping
    func demonstrateErrorWrapping() {
        fmt.Println("\n=== Error Wrapping ===")
        
        // Function that wraps errors
        readFile := func(filename string) ([]byte, error) {
            data, err := os.ReadFile(filename)
            if err != nil {
                return nil, fmt.Errorf("failed to read file '%s': %w", filename, err)
            }
            return data, nil
        }
        
        // Function that wraps again
        processFile := func(filename string) error {
            data, err := readFile(filename)
            if err != nil {
                return fmt.Errorf("failed to process file '%s': %w", filename, err)
            }
            
            fmt.Printf("File content: %s\n", string(data))
            return nil
        }
        
        // Handle wrapped errors
        err := processFile("nonexistent.txt")
        if err != nil {
            fmt.Printf("Error: %v\n", err)
            
            // Unwrap error
            unwrapped := errors.Unwrap(err)
            fmt.Printf("Unwrapped: %v\n", unwrapped)
            
            // Unwrap multiple times
            for {
                unwrapped = errors.Unwrap(unwrapped)
                if unwrapped == nil {
                    break
                }
                fmt.Printf("Further unwrapped: %v\n", unwrapped)
            }
            
            // Check if error is specific type
            if errors.Is(err, os.ErrNotExist) {
                fmt.Printf("File does not exist\n")
            }
            
            // Get error chain
            fmt.Printf("Error chain:\n")
            for err != nil {
                fmt.Printf("  - %v\n", err)
                err = errors.Unwrap(err)
            }
        }
    }
    
    // Error handling with multiple returns
    func demonstrateMultipleReturns() {
        fmt.Println("\n=== Multiple Returns ===")
        
        // Function with multiple returns
        parseUser := func(input string) (name string, age int, err error) {
            parts := strings.Split(input, ",")
            if len(parts) != 2 {
                return "", 0, fmt.Errorf("invalid input format, expected 'name,age'")
            }
            
            name = strings.TrimSpace(parts[0])
            if name == "" {
                return "", 0, fmt.Errorf("name cannot be empty")
            }
            
            ageStr := strings.TrimSpace(parts[1])
            age, err = strconv.Atoi(ageStr)
            if err != nil {
                return "", 0, fmt.Errorf("invalid age: %w", err)
            }
            
            if age < 0 || age > 120 {
                return "", 0, fmt.Errorf("age %d is out of valid range", age)
            }
            
            return name, age, nil
        }
        
        // Test cases
        inputs := []string{
            "John Doe,30",
            "Jane Smith,25",
            "invalid",
            ",25",
            "John,-5",
            "Alice,150",
        }
        
        for _, input := range inputs {
            name, age, err := parseUser(input)
            if err != nil {
                fmt.Printf("Input '%s': Error - %v\n", input, err)
            } else {
                fmt.Printf("Input '%s': Name='%s', Age=%d\n", input, name, age)
            }
        }
    }
    
    // Error handling with defer
    func demonstrateDeferError() {
        fmt.Println("\n=== Defer Error Handling ===")
        
        // File operation with defer
        processFile := func(filename string) error {
            file, err := os.Open(filename)
            if err != nil {
                return fmt.Errorf("failed to open file: %w", err)
            }
            defer file.Close()
            
            // Read file
            data := make([]byte, 100)
            _, err = file.Read(data)
            if err != nil {
                return fmt.Errorf("failed to read file: %w", err)
            }
            
            fmt.Printf("Read %d bytes\n", len(data))
            return nil
        }
        
        err := processFile("nonexistent.txt")
        if err != nil {
            fmt.Printf("Error: %v\n", err)
        }
        
        // Defer with error handling
        func withErrorHandling() {
            var err error
            
            defer func() {
                if err != nil {
                    fmt.Printf("Deferred error handling: %v\n", err)
                }
            }()
            
            // Simulate operation
            err = errors.New("operation failed")
        }()
        
        withErrorHandling()
    }
    
    // Panic and recover
    func demonstratePanicRecover() {
        fmt.Println("\n=== Panic and Recover ===")
        
        // Function that panics
        riskyOperation := func() {
            fmt.Println("Starting risky operation...")
            panic("something went terribly wrong")
        }
        
        // Safe wrapper with recover
        safeOperation := func() {
            defer func() {
                if r := recover(); r != nil {
                    fmt.Printf("Recovered from panic: %v\n", r)
                }
            }()
            
            riskyOperation()
            fmt.Println("This line won't be reached")
        }
        
        safeOperation()
        fmt.Println("Program continues after recovery")
        
        // Panic with custom error
        panicWithError := func() {
            err := ValidationError{
                Field:   "email",
                Message: "invalid email format",
                Code:    2001,
            }
            panic(err)
        }
        
        func safePanicWrapper() {
            defer func() {
                if r := recover(); r != nil {
                    if err, ok := r.(ValidationError); ok {
                        fmt.Printf("Recovered validation error: Field=%s, Message=%s, Code=%d\n", 
                            err.Field, err.Message, err.Code)
                    } else {
                        fmt.Printf("Recovered from panic: %v\n", r)
                    }
                }
            }()
            
            panicWithError()
        }
        
        safePanicWrapper()
    }
    
    // Error handling best practices
    func demonstrateBestPractices() {
        fmt.Println("\n=== Error Handling Best Practices ===")
        
        // 1. Handle errors immediately
        func immediateHandling() {
            data, err := os.ReadFile("config.json")
            if err != nil {
                fmt.Printf("Failed to read config: %v\n", err)
                return
            }
            fmt.Printf("Config loaded: %d bytes\n", len(data))
        }
        
        // 2. Add context to errors
        func contextualErrors() {
            configPath := "config.json"
            data, err := os.ReadFile(configPath)
            if err != nil {
                fmt.Printf("Failed to read config file '%s': %v\n", configPath, err)
                return
            }
            fmt.Printf("Config loaded: %d bytes\n", len(data))
        }
        
        // 3. Use sentinel errors
        var ErrNotFound = errors.New("not found")
        
        func sentinelErrors() {
            findUser := func(id int) (string, error) {
                if id == 999 {
                    return "", ErrNotFound
                }
                return fmt.Sprintf("User%d", id), nil
            }
            
            name, err := findUser(999)
            if err != nil {
                if errors.Is(err, ErrNotFound) {
                    fmt.Printf("User not found\n")
                } else {
                    fmt.Printf("Error finding user: %v\n", err)
                }
                return
            }
            fmt.Printf("Found user: %s\n", name)
        }
        
        // 4. Log errors with context
        func loggingErrors() {
            operation := func(step string) error {
                if step == "critical" {
                    return fmt.Errorf("critical operation failed")
                }
                return nil
            }
            
            steps := []string{"step1", "step2", "critical", "step4"}
            
            for _, step := range steps {
                err := operation(step)
                if err != nil {
                    fmt.Printf("LOG: Operation '%s' failed: %v\n", step, err)
                    return
                }
                fmt.Printf("Operation '%s' completed\n", step)
            }
        }
        
        immediateHandling()
        contextualErrors()
        sentinelErrors()
        loggingErrors()
    }
    
    // Run all demonstrations
    demonstrateBasicErrors()
    demonstrateErrorHandling()
    demonstrateCustomErrors()
    demonstrateErrorWrapping()
    demonstrateMultipleReturns()
    demonstrateDeferError()
    demonstratePanicRecover()
    demonstrateBestPractices()
}
```

### Advanced Error Handling
```go
package main

import (
    "fmt"
    "errors"
    "context"
    "time"
    "sync"
)

func main() {
    // Error aggregation
    func demonstrateErrorAggregation() {
        fmt.Println("=== Error Aggregation ===")
        
        type MultiError struct {
            Errors []error
        }
        
        func (me MultiError) Error() string {
            if len(me.Errors) == 0 {
                return "no errors"
            }
            
            var result string
            for i, err := range me.Errors {
                if i > 0 {
                    result += "; "
                }
                result += err.Error()
            }
            return result
        }
        
        func (me *MultiError) Add(err error) {
            if err != nil {
                me.Errors = append(me.Errors, err)
            }
        }
        
        func (me MultiError) HasErrors() bool {
            return len(me.Errors) > 0
        }
        
        // Function that collects multiple errors
        validateUser := func(user map[string]interface{}) error {
            var multiErr MultiError
            
            if name, ok := user["name"].(string); !ok || name == "" {
                multiErr.Add(errors.New("name is required"))
            }
            
            if age, ok := user["age"].(int); !ok || age < 0 {
                multiErr.Add(errors.New("age must be a positive integer"))
            }
            
            if email, ok := user["email"].(string); !ok || email == "" {
                multiErr.Add(errors.New("email is required"))
            }
            
            if multiErr.HasErrors() {
                return multiErr
            }
            
            return nil
        }
        
        // Test cases
        users := []map[string]interface{}{
            {"name": "John", "age": 30, "email": "john@example.com"},
            {"name": "", "age": -5, "email": ""},
            {"age": 25, "email": "jane@example.com"},
        }
        
        for i, user := range users {
            err := validateUser(user)
            if err != nil {
                fmt.Printf("User %d validation failed: %v\n", i+1, err)
                
                if multiErr, ok := err.(MultiError); ok {
                    fmt.Printf("  - %d errors occurred\n", len(multiErr.Errors))
                }
            } else {
                fmt.Printf("User %d is valid\n", i+1)
            }
        }
    }
    
    // Error retry mechanisms
    func demonstrateRetryMechanisms() {
        fmt.Println("\n=== Retry Mechanisms ===")
        
        // Exponential backoff retry
        func retryWithBackoff(operation func() error, maxAttempts int) error {
            var err error
            backoff := 100 * time.Millisecond
            
            for attempt := 1; attempt <= maxAttempts; attempt++ {
                err = operation()
                if err == nil {
                    return nil
                }
                
                fmt.Printf("Attempt %d failed: %v\n", attempt, err)
                
                if attempt < maxAttempts {
                    fmt.Printf("Retrying in %v...\n", backoff)
                    time.Sleep(backoff)
                    backoff *= 2
                }
            }
            
            return fmt.Errorf("operation failed after %d attempts: %w", maxAttempts, err)
        }
        
        // Operation that fails initially
        attemptCount := 0
        unreliableOperation := func() error {
            attemptCount++
            if attemptCount < 3 {
                return fmt.Errorf("temporary failure (attempt %d)", attemptCount)
            }
            fmt.Println("Operation succeeded!")
            return nil
        }
        
        err := retryWithBackoff(unreliableOperation, 5)
        if err != nil {
            fmt.Printf("Final error: %v\n", err)
        }
        
        // Reset for next test
        attemptCount = 0
        
        // Operation that always fails
        alwaysFailingOperation := func() error {
            return errors.New("permanent failure")
        }
        
        err = retryWithBackoff(alwaysFailingOperation, 3)
        if err != nil {
            fmt.Printf("Final error: %v\n", err)
        }
    }
    
    // Error handling with context
    func demonstrateContextErrors() {
        fmt.Println("\n=== Context Error Handling ===")
        
        // Operation with context cancellation
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
        
        // Operation with sufficient time
        ctx1, cancel1 := context.WithTimeout(context.Background(), 2*time.Second)
        defer cancel1()
        
        err := operation(ctx1, 1*time.Second)
        fmt.Printf("Result 1: %v\n", err)
        
        // Operation with insufficient time
        ctx2, cancel2 := context.WithTimeout(context.Background(), 1*time.Second)
        defer cancel2()
        
        err = operation(ctx2, 2*time.Second)
        fmt.Printf("Result 2: %v\n", err)
        
        // Operation with manual cancellation
        ctx3, cancel3 := context.WithCancel(context.Background())
        
        go func() {
            time.Sleep(500 * time.Millisecond)
            fmt.Println("Cancelling operation...")
            cancel3()
        }()
        
        err = operation(ctx3, 2*time.Second)
        fmt.Printf("Result 3: %v\n", err)
    }
    
    // Error handling patterns
    func demonstrateErrorPatterns() {
        fmt.Println("\n=== Error Handling Patterns ===")
        
        // 1. Result type pattern
        type Result[T any] struct {
            Value T
            Error error
        }
        
        func safeDivide(a, b float64) Result[float64] {
            if b == 0 {
                return Result[float64]{Error: errors.New("division by zero")}
            }
            return Result[float64]{Value: a / b}
        }
        
        result := safeDivide(10, 2)
        if result.Error != nil {
            fmt.Printf("Error: %v\n", result.Error)
        } else {
            fmt.Printf("Result: %.2f\n", result.Value)
        }
        
        // 2. Either type pattern
        type Either[L, R any] struct {
            Left  L
            Right R
            IsLeft bool
        }
        
        func parseEither(s string) Either[error, int] {
            num, err := strconv.Atoi(s)
            if err != nil {
                return Either[error, int]{Left: err, IsLeft: true}
            }
            return Either[error, int]{Right: num, IsLeft: false}
        }
        
        either := parseEither("123")
        if either.IsLeft {
            fmt.Printf("Error: %v\n", either.Left)
        } else {
            fmt.Printf("Value: %d\n", either.Right)
        }
        
        // 3. Error chain pattern
        type ErrorChain struct {
            errors []error
        }
        
        func (ec *ErrorChain) Add(err error) {
            if err != nil {
                ec.errors = append(ec.errors, err)
            }
        }
        
        func (ec ErrorChain) Error() string {
            var result string
            for i, err := range ec.errors {
                if i > 0 {
                    result += " -> "
                }
                result += err.Error()
            }
            return result
        }
        
        func (ec ErrorChain) HasErrors() bool {
            return len(ec.errors) > 0
        }
        
        func (ec ErrorChain) Unwrap() []error {
            return ec.errors
        }
        
        func processWithChain() error {
            var chain ErrorChain
            
            // Step 1
            chain.Add(errors.New("step 1 failed"))
            
            // Step 2
            chain.Add(errors.New("step 2 failed"))
            
            // Step 3
            chain.Add(errors.New("step 3 failed"))
            
            if chain.HasErrors() {
                return chain
            }
            
            return nil
        }
        
        err = processWithChain()
        if err != nil {
            fmt.Printf("Chain error: %v\n", err)
            
            if chain, ok := err.(ErrorChain); ok {
                fmt.Printf("Individual errors:\n")
                for i, e := range chain.Unwrap() {
                    fmt.Printf("  %d. %v\n", i+1, e)
                }
            }
        }
    }
    
    // Error handling middleware
    func demonstrateErrorMiddleware() {
        fmt.Println("\n=== Error Middleware ===")
        
        type Handler func() error
        
        type ErrorHandler func(error) error
        
        func withMiddleware(handler Handler, middlewares ...ErrorHandler) error {
            err := handler()
            
            for _, middleware := range middlewares {
                if err != nil {
                    err = middleware(err)
                }
            }
            
            return err
        }
        
        // Logging middleware
        loggingMiddleware := func(err error) error {
            fmt.Printf("LOG: Error occurred: %v\n", err)
            return err
        }
        
        // Context middleware
        contextMiddleware := func(err error) error {
            return fmt.Errorf("operation failed in context: %w", err)
        }
        
        // Metrics middleware
        metricsMiddleware := func(err error) error {
            fmt.Printf("METRICS: Error recorded\n")
            return err
        }
        
        // Handler that fails
        failingHandler := func() error {
            return errors.New("handler error")
        }
        
        err := withMiddleware(failingHandler, 
            loggingMiddleware, 
            contextMiddleware, 
            metricsMiddleware)
        
        fmt.Printf("Final error: %v\n", err)
    }
    
    // Error handling with channels
    func demonstrateChannelErrors() {
        fmt.Println("\n=== Channel Error Handling ===")
        
        type Result struct {
            Value interface{}
            Error error
        }
        
        worker := func(id int, jobs <-chan int, results chan<- Result) {
            for job := range jobs {
                fmt.Printf("Worker %d processing job %d\n", id, job)
                
                // Simulate work
                time.Sleep(100 * time.Millisecond)
                
                // Simulate occasional failure
                if job%7 == 0 {
                    results <- Result{Error: fmt.Errorf("job %d failed", job)}
                } else {
                    results <- Result{Value: job * 2}
                }
            }
        }
        
        // Setup worker pool
        const numWorkers = 3
        const numJobs = 10
        
        jobs := make(chan int, numJobs)
        results := make(chan Result, numJobs)
        
        // Start workers
        for i := 1; i <= numWorkers; i++ {
            go worker(i, jobs, results)
        }
        
        // Send jobs
        go func() {
            for i := 1; i <= numJobs; i++ {
                jobs <- i
            }
            close(jobs)
        }()
        
        // Collect results
        for i := 0; i < numJobs; i++ {
            result := <-results
            if result.Error != nil {
                fmt.Printf("Job failed: %v\n", result.Error)
            } else {
                fmt.Printf("Job result: %v\n", result.Value)
            }
        }
    }
    
    // Error handling with recovery
    func demonstrateRecovery() {
        fmt.Println("\n=== Recovery Patterns ===")
        
        // Safe function wrapper
        func safeFunction(fn func() error) (err error) {
            defer func() {
                if r := recover(); r != nil {
                    switch v := r.(type) {
                    case error:
                        err = fmt.Errorf("panic recovered: %w", v)
                    case string:
                        err = fmt.Errorf("panic recovered: %s", v)
                    default:
                        err = fmt.Errorf("panic recovered: %v", v)
                    }
                }
            }()
            
            return fn()
        }
        
        // Function that panics
        panickingFunction := func() error {
            panic("something went wrong")
        }
        
        // Function with normal error
        normalFunction := func() error {
            return errors.New("normal error")
        }
        
        // Test safe wrapper
        err := safeFunction(panickingFunction)
        fmt.Printf("Panicking function result: %v\n", err)
        
        err = safeFunction(normalFunction)
        fmt.Printf("Normal function result: %v\n", err)
        
        // Recovery with stack trace
        func withStackTrace(fn func() error) error {
            defer func() {
                if r := recover(); r != nil {
                    fmt.Printf("Stack trace:\n")
                    debug.PrintStack()
                }
            }()
            
            return fn()
        }
        
        err = withStackTrace(panickingFunction)
        fmt.Printf("With stack trace result: %v\n", err)
    }
    
    // Error handling in concurrent operations
    func demonstrateConcurrentErrors() {
        fmt.Println("\n=== Concurrent Error Handling ===")
        
        type TaskResult struct {
            ID    int
            Error error
        }
        
        worker := func(id int, tasks <-chan int, results chan<- TaskResult) {
            for task := range tasks {
                fmt.Printf("Worker %d processing task %d\n", id, task)
                
                // Simulate work
                time.Sleep(50 * time.Millisecond)
                
                // Simulate failure
                if task%5 == 0 {
                    results <- TaskResult{ID: task, Error: fmt.Errorf("task %d failed", task)}
                } else {
                    fmt.Printf("Worker %d completed task %d\n", id, task)
                    results <- TaskResult{ID: task, Error: nil}
                }
            }
        }
        
        // Concurrent task processing
        const numWorkers = 4
        const numTasks = 20
        
        tasks := make(chan int, numTasks)
        results := make(chan TaskResult, numTasks)
        
        // Start workers
        for i := 1; i <= numWorkers; i++ {
            go worker(i, tasks, results)
        }
        
        // Send tasks
        go func() {
            for i := 1; i <= numTasks; i++ {
                tasks <- i
            }
            close(tasks)
        }()
        
        // Collect results
        var errors []error
        var successCount int
        
        for i := 0; i < numTasks; i++ {
            result := <-results
            if result.Error != nil {
                errors = append(errors, result.Error)
            } else {
                successCount++
            }
        }
        
        fmt.Printf("Completed tasks: %d\n", successCount)
        fmt.Printf("Failed tasks: %d\n", len(errors))
        
        if len(errors) > 0 {
            fmt.Printf("Errors:\n")
            for _, err := range errors {
                fmt.Printf("  - %v\n", err)
            }
        }
    }
    
    // Error handling with timeouts
    func demonstrateTimeoutErrors() {
        fmt.Println("\n=== Timeout Error Handling ===")
        
        operation := func(name string, duration time.Duration) error {
            fmt.Printf("Operation %s started\n", name)
            
            select {
            case <-time.After(duration):
                fmt.Printf("Operation %s completed\n", name)
                return nil
            case <-time.After(1 * time.Second):
                return fmt.Errorf("operation %s timed out", name)
            }
        }
        
        // Test with timeout
        operations := []struct {
            name     string
            duration time.Duration
        }{
            {"Fast", 500 * time.Millisecond},
            {"Slow", 2 * time.Second},
            {"Medium", 800 * time.Millisecond},
        }
        
        for _, op := range operations {
            err := operation(op.name, op.duration)
            if err != nil {
                fmt.Printf("Error: %v\n", err)
            }
        }
    }
    
    // Run all demonstrations
    demonstrateErrorAggregation()
    demonstrateRetryMechanisms()
    demonstrateContextErrors()
    demonstrateErrorPatterns()
    demonstrateErrorMiddleware()
    demonstrateChannelErrors()
    demonstrateRecovery()
    demonstrateConcurrentErrors()
    demonstrateTimeoutErrors()
}
```

## Error Handling Best Practices

### Production-Ready Error Handling
```go
package main

import (
    "fmt"
    "log"
    "os"
    "runtime/debug"
    "time"
    "encoding/json"
)

func main() {
    // Structured error handling
    func demonstrateStructuredErrors() {
        fmt.Println("=== Structured Error Handling ===")
        
        type ErrorCode string
        
        const (
            ErrCodeValidation ErrorCode = "VALIDATION_ERROR"
            ErrCodeNotFound   ErrorCode = "NOT_FOUND"
            ErrCodeInternal   ErrorCode = "INTERNAL_ERROR"
            ErrCodeTimeout    ErrorCode = "TIMEOUT"
        )
        
        type AppError struct {
            Code      ErrorCode   `json:"code"`
            Message   string      `json:"message"`
            Details   interface{} `json:"details,omitempty"`
            Timestamp time.Time   `json:"timestamp"`
            Stack     string      `json:"stack,omitempty"`
        }
        
        func (ae AppError) Error() string {
            return fmt.Sprintf("[%s] %s", ae.Code, ae.Message)
        }
        
        func NewAppError(code ErrorCode, message string, details interface{}) *AppError {
            return &AppError{
                Code:      code,
                Message:   message,
                Details:   details,
                Timestamp: time.Now(),
                Stack:     string(debug.Stack()),
            }
        }
        
        // Usage examples
        validationErr := NewAppError(ErrCodeValidation, 
            "Invalid input parameters", 
            map[string]string{
                "field": "email",
                "issue": "invalid format",
            })
        
        notFoundErr := NewAppError(ErrCodeNotFound, 
            "User not found", 
            map[string]interface{}{
                "user_id": 123,
                "query":   "SELECT * FROM users WHERE id = 123",
            })
        
        fmt.Printf("Validation error: %v\n", validationErr)
        fmt.Printf("Not found error: %v\n", notFoundErr)
        
        // JSON serialization
        jsonErr, _ := json.MarshalIndent(validationErr, "", "  ")
        fmt.Printf("JSON error:\n%s\n", jsonErr)
    }
    
    // Error logging strategies
    func demonstrateErrorLogging() {
        fmt.Println("\n=== Error Logging Strategies ===")
        
        // Custom logger
        type ErrorLogger struct {
            service string
            version string
        }
        
        func NewErrorLogger(service, version string) *ErrorLogger {
            return &ErrorLogger{
                service: service,
                version: version,
            }
        }
        
        func (el *ErrorLogger) LogError(err error, context map[string]interface{}) {
            logEntry := map[string]interface{}{
                "timestamp": time.Now().UTC().Format(time.RFC3339),
                "service":   el.service,
                "version":   el.version,
                "error":     err.Error(),
                "context":   context,
            }
            
            // In production, this would go to a structured logging system
            jsonData, _ := json.MarshalIndent(logEntry, "", "  ")
            fmt.Printf("LOG: %s\n", jsonData)
        }
        
        func (el *ErrorLogger) LogPanic(recovered interface{}, context map[string]interface{}) {
            logEntry := map[string]interface{}{
                "timestamp": time.Now().UTC().Format(time.RFC3339),
                "service":   el.service,
                "version":   el.version,
                "panic":     fmt.Sprintf("%v", recovered),
                "stack":     string(debug.Stack()),
                "context":   context,
            }
            
            jsonData, _ := json.MarshalIndent(logEntry, "", "  ")
            fmt.Printf("PANIC LOG: %s\n", jsonData)
        }
        
        logger := NewErrorLogger("user-service", "v1.2.3")
        
        // Log different types of errors
        err := fmt.Errorf("database connection failed")
        logger.LogError(err, map[string]interface{}{
            "operation": "user_lookup",
            "user_id":   123,
            "query":     "SELECT * FROM users WHERE id = ?",
        })
        
        // Simulate panic
        func riskyOperation() {
            panic("critical failure")
        }
        
        defer func() {
            if r := recover(); r != nil {
                logger.LogPanic(r, map[string]interface{}{
                    "operation": "data_processing",
                    "batch_id":  "batch_123",
                })
            }
        }()
        
        riskyOperation()
    }
    
    // Error handling in web services
    func demonstrateWebErrorHandling() {
        fmt.Println("\n=== Web Service Error Handling ===")
        
        type HTTPError struct {
            StatusCode int    `json:"status_code"`
            Code       string `json:"code"`
            Message    string `json:"message"`
            Details    interface{} `json:"details,omitempty"`
        }
        
        func (he HTTPError) Error() string {
            return fmt.Sprintf("HTTP %d: %s", he.StatusCode, he.Message)
        }
        
        func NewHTTPError(statusCode int, code, message string, details interface{}) *HTTPError {
            return &HTTPError{
                StatusCode: statusCode,
                Code:       code,
                Message:    message,
                Details:    details,
            }
        }
        
        // Common HTTP errors
        var (
            ErrBadRequest     = NewHTTPError(400, "BAD_REQUEST", "Invalid request", nil)
            ErrUnauthorized   = NewHTTPError(401, "UNAUTHORIZED", "Authentication required", nil)
            ErrForbidden      = NewHTTPError(403, "FORBIDDEN", "Access denied", nil)
            ErrNotFound       = NewHTTPError(404, "NOT_FOUND", "Resource not found", nil)
            ErrInternalServer = NewHTTPError(500, "INTERNAL_ERROR", "Internal server error", nil)
        )
        
        // Error handler middleware simulation
        func errorHandler(err error) *HTTPError {
            switch e := err.(type) {
            case *HTTPError:
                return e
            case interface{ Code() string }:
                // Custom error with Code() method
                return NewHTTPError(400, e.Code(), e.Error(), nil)
            default:
                return ErrInternalServer
            }
        }
        
        // Test error handling
        err := ErrBadRequest
        handledErr := errorHandler(err)
        fmt.Printf("Handled error: %v\n", handledErr)
        
        // JSON response simulation
        jsonResponse, _ := json.MarshalIndent(handledErr, "", "  ")
        fmt.Printf("JSON response:\n%s\n", jsonResponse)
    }
    
    // Error handling in data processing
    func demonstrateDataProcessingErrors() {
        fmt.Println("\n=== Data Processing Error Handling ===")
        
        type ProcessingError struct {
            Stage    string `json:"stage"`
            ItemID   string `json:"item_id"`
            Error    string `json:"error"`
            Recover  bool   `json:"recoverable"`
        }
        
        func (pe ProcessingError) Error() string {
            return fmt.Sprintf("Processing error at stage '%s' for item '%s': %s", 
                pe.Stage, pe.ItemID, pe.Error)
        }
        
        type DataProcessor struct {
            errors []ProcessingError
        }
        
        func NewDataProcessor() *DataProcessor {
            return &DataProcessor{errors: make([]ProcessingError, 0)}
        }
        
        func (dp *DataProcessor) ProcessItem(itemID string, data interface{}) error {
            // Simulate processing stages
            stages := []string{"validation", "transformation", "storage"}
            
            for _, stage := range stages {
                // Simulate occasional failure
                if itemID == "item_123" && stage == "validation" {
                    err := ProcessingError{
                        Stage:   stage,
                        ItemID:  itemID,
                        Error:   "invalid data format",
                        Recover: true,
                    }
                    dp.errors = append(dp.errors, err)
                    return err
                }
                
                if itemID == "item_456" && stage == "storage" {
                    err := ProcessingError{
                        Stage:   stage,
                        ItemID:  itemID,
                        Error:   "database connection failed",
                        Recover: false,
                    }
                    dp.errors = append(dp.errors, err)
                    return err
                }
                
                fmt.Printf("Item %s: %s stage completed\n", itemID, stage)
                time.Sleep(10 * time.Millisecond)
            }
            
            return nil
        }
        
        func (dp *DataProcessor) ProcessBatch(items []string) {
            var failed []string
            var recovered []string
            
            for _, itemID := range items {
                err := dp.ProcessItem(itemID, nil)
                if err != nil {
                    if procErr, ok := err.(ProcessingError); ok && procErr.Recover {
                        recovered = append(recovered, itemID)
                        fmt.Printf("Item %s: processing failed but recoverable\n", itemID)
                    } else {
                        failed = append(failed, itemID)
                        fmt.Printf("Item %s: processing failed permanently\n", itemID)
                    }
                } else {
                    fmt.Printf("Item %s: processing completed successfully\n", itemID)
                }
            }
            
            fmt.Printf("Batch processing summary:\n")
            fmt.Printf("  Total: %d\n", len(items))
            fmt.Printf("  Successful: %d\n", len(items)-len(failed)-len(recovered))
            fmt.Printf("  Failed (recoverable): %d\n", len(recovered))
            fmt.Printf("  Failed (permanent): %d\n", len(failed))
            
            if len(dp.errors) > 0 {
                fmt.Printf("  Errors:\n")
                for _, err := range dp.errors {
                    fmt.Printf("    - %v\n", err)
                }
            }
        }
        
        processor := NewDataProcessor()
        items := []string{"item_001", "item_123", "item_456", "item_789"}
        processor.ProcessBatch(items)
    }
    
    // Error handling with metrics
    func demonstrateErrorMetrics() {
        fmt.Println("\n=== Error Metrics ===")
        
        type ErrorMetrics struct {
            TotalErrors    int64     `json:"total_errors"`
            ErrorsByCode  map[string]int64 `json:"errors_by_code"`
            ErrorsByTime  []time.Time `json:"errors_by_time"`
            LastError     time.Time  `json:"last_error"`
            ErrorRate     float64    `json:"error_rate"`
        }
        
        func NewErrorMetrics() *ErrorMetrics {
            return &ErrorMetrics{
                ErrorsByCode: make(map[string]int64),
                ErrorsByTime: make([]time.Time, 0),
            }
        }
        
        func (em *ErrorMetrics) RecordError(code string) {
            em.TotalErrors++
            em.ErrorsByCode[code]++
            em.ErrorsByTime = append(em.ErrorsByTime, time.Now())
            em.LastError = time.Now()
            
            // Keep only last 100 error timestamps
            if len(em.ErrorsByTime) > 100 {
                em.ErrorsByTime = em.ErrorsByTime[1:]
            }
        }
        
        func (em *ErrorMetrics) CalculateRate(totalOperations int64) {
            if totalOperations > 0 {
                em.ErrorRate = float64(em.TotalErrors) / float64(totalOperations)
            }
        }
        
        func (em *ErrorMetrics) GetSummary() map[string]interface{} {
            return map[string]interface{}{
                "total_errors":    em.TotalErrors,
                "errors_by_code":  em.ErrorsByCode,
                "last_error":      em.LastError,
                "error_rate":      em.ErrorRate,
            }
        }
        
        metrics := NewErrorMetrics()
        
        // Simulate error recording
        errorCodes := []string{"VALIDATION_ERROR", "DATABASE_ERROR", "NETWORK_ERROR", 
            "VALIDATION_ERROR", "TIMEOUT_ERROR", "DATABASE_ERROR"}
        
        for i, code := range errorCodes {
            metrics.RecordError(code)
            time.Sleep(10 * time.Millisecond)
        }
        
        metrics.CalculateRate(1000)
        
        summary := metrics.GetSummary()
        jsonData, _ := json.MarshalIndent(summary, "", "  ")
        fmt.Printf("Error metrics:\n%s\n", jsonData)
    }
    
    // Error handling configuration
    func demonstrateErrorConfiguration() {
        fmt.Println("\n=== Error Handling Configuration ===")
        
        type ErrorConfig struct {
            LogLevel      string            `json:"log_level"`
            EnableStack   bool              `json:"enable_stack"`
            RetryAttempts int               `json:"retry_attempts"`
            RetryDelay    time.Duration     `json:"retry_delay"`
            ErrorCodes    map[string]string `json:"error_codes"`
            Handlers      []string          `json:"handlers"`
        }
        
        func DefaultErrorConfig() *ErrorConfig {
            return &ErrorConfig{
                LogLevel:      "INFO",
                EnableStack:   true,
                RetryAttempts: 3,
                RetryDelay:    100 * time.Millisecond,
                ErrorCodes: map[string]string{
                    "VALIDATION_ERROR": "user input validation failed",
                    "DATABASE_ERROR":  "database operation failed",
                    "NETWORK_ERROR":   "network connectivity issue",
                    "TIMEOUT_ERROR":   "operation timed out",
                    "INTERNAL_ERROR":  "unexpected internal error",
                },
                Handlers: []string{"logger", "metrics", "alerting"},
            }
        }
        
        config := DefaultErrorConfig()
        
        fmt.Printf("Default error configuration:\n")
        jsonData, _ := json.MarshalIndent(config, "", "  ")
        fmt.Printf("%s\n", jsonData)
        
        // Configuration from environment (simulation)
        envConfig := &ErrorConfig{
            LogLevel:      "DEBUG",
            EnableStack:   false,
            RetryAttempts: 5,
            RetryDelay:    200 * time.Millisecond,
        }
        
        fmt.Printf("Environment-based configuration:\n")
        envData, _ := json.MarshalIndent(envConfig, "", "  ")
        fmt.Printf("%s\n", envData)
    }
    
    // Error handling testing
    func demonstrateErrorTesting() {
        fmt.Println("\n=== Error Testing ===")
        
        type ErrorTestCase struct {
            Name        string      `json:"name"`
            Input       interface{} `json:"input"`
            ExpectedErr string      `json:"expected_error"`
            ShouldFail  bool        `json:"should_fail"`
        }
        
        type TestResult struct {
            Name     string `json:"name"`
            Passed   bool   `json:"passed"`
            Error    string `json:"error,omitempty"`
            Duration string `json:"duration"`
        }
        
        func validateInput(input interface{}) error {
            str, ok := input.(string)
            if !ok {
                return errors.New("input must be string")
            }
            
            if len(str) < 3 {
                return errors.New("input too short")
            }
            
            if len(str) > 10 {
                return errors.New("input too long")
            }
            
            return nil
        }
        
        func runErrorTest(testCase ErrorTestCase) TestResult {
            start := time.Now()
            
            err := validateInput(testCase.Input)
            duration := time.Since(start)
            
            result := TestResult{
                Name:     testCase.Name,
                Duration: duration.String(),
            }
            
            if testCase.ShouldFail {
                if err != nil {
                    if testCase.ExpectedErr == "" || err.Error() == testCase.ExpectedErr {
                        result.Passed = true
                    } else {
                        result.Error = fmt.Sprintf("expected error '%s', got '%s'", 
                            testCase.ExpectedErr, err.Error())
                    }
                } else {
                    result.Error = "expected error but got none"
                }
            } else {
                if err == nil {
                    result.Passed = true
                } else {
                    result.Error = fmt.Sprintf("unexpected error: %v", err)
                }
            }
            
            return result
        }
        
        // Test cases
        testCases := []ErrorTestCase{
            {
                Name:        "valid input",
                Input:       "hello",
                ExpectedErr: "",
                ShouldFail:  false,
            },
            {
                Name:        "too short",
                Input:       "hi",
                ExpectedErr: "input too short",
                ShouldFail:  true,
            },
            {
                Name:        "too long",
                Input:       "this is too long",
                ExpectedErr: "input too long",
                ShouldFail:  true,
            },
            {
                Name:        "wrong type",
                Input:       123,
                ExpectedErr: "input must be string",
                ShouldFail:  true,
            },
        }
        
        var results []TestResult
        var passed, failed int
        
        for _, testCase := range testCases {
            result := runErrorTest(testCase)
            results = append(results, result)
            
            if result.Passed {
                passed++
            } else {
                failed++
            }
        }
        
        fmt.Printf("Test Results:\n")
        fmt.Printf("  Total: %d\n", len(results))
        fmt.Printf("  Passed: %d\n", passed)
        fmt.Printf("  Failed: %d\n", failed)
        
        fmt.Printf("Detailed Results:\n")
        for _, result := range results {
            status := "PASS"
            if !result.Passed {
                status = "FAIL"
            }
            fmt.Printf("  %s: %s (%s)\n", result.Name, status, result.Duration)
            if result.Error != "" {
                fmt.Printf("    Error: %s\n", result.Error)
            }
        }
    }
    
    // Run all demonstrations
    demonstrateStructuredErrors()
    demonstrateErrorLogging()
    demonstrateWebErrorHandling()
    demonstrateDataProcessingErrors()
    demonstrateErrorMetrics()
    demonstrateErrorConfiguration()
    demonstrateErrorTesting()
}
```

## Summary

Go error handling provides:

**Error Basics:**
- Built-in error interface
- Error creation with errors.New and fmt.Errorf
- Custom error types
- Error wrapping and unwrapping
- Nil error for success

**Error Handling Patterns:**
- Multiple return values
- Immediate error checking
- Error propagation
- Contextual error information
- Structured error data

**Advanced Error Handling:**
- Error aggregation
- Retry mechanisms
- Context cancellation
- Error middleware
- Recovery patterns

**Production Features:**
- Structured logging
- Error metrics
- HTTP error responses
- Data processing errors
- Configuration management

**Best Practices:**
- Handle errors explicitly
- Add context to errors
- Use appropriate error types
- Log errors with context
- Test error cases

**Key Features:**
- Explicit error handling
- No exceptions
- Error wrapping
- Context support
- Panic/recover mechanism

**Common Use Cases:**
- Input validation
- Database operations
- Network requests
- File operations
- Business logic validation

Go's error handling philosophy emphasizes explicit, clear, and manageable error handling that makes code more reliable and easier to maintain.
