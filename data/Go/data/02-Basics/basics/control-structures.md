# Go Control Structures

## Conditional Statements

### If-Else Statements
```go
package main

import "fmt"

func main() {
    // Basic if statement
    age := 25
    if age >= 18 {
        fmt.Println("You are an adult")
    }
    
    // If-else statement
    score := 85
    if score >= 90 {
        fmt.Println("Grade: A")
    } else if score >= 80 {
        fmt.Println("Grade: B")
    } else if score >= 70 {
        fmt.Println("Grade: C")
    } else if score >= 60 {
        fmt.Println("Grade: D")
    } else {
        fmt.Println("Grade: F")
    }
    
    // If with initialization statement
    if num := 42; num%2 == 0 {
        fmt.Printf("%d is even\n", num)
    } else {
        fmt.Printf("%d is odd\n", num)
    }
    
    // Multiple conditions
    temperature := 25
    if temperature > 30 {
        fmt.Println("It's hot outside")
    } else if temperature >= 20 && temperature <= 30 {
        fmt.Println("It's pleasant outside")
    } else if temperature >= 10 && temperature < 20 {
        fmt.Println("It's cool outside")
    } else {
        fmt.Println("It's cold outside")
    }
    
    // Nested if statements
    hasLicense := true
    hasCar := true
    
    if hasLicense {
        if hasCar {
            fmt.Println("You can drive")
        } else {
            fmt.Println("You have license but no car")
        }
    } else {
        fmt.Println("You need a license to drive")
    }
    
    // If with error handling
    result, err := divide(10, 2)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Result: %d\n", result)
    }
    
    // If with multiple error checks
    if err := validateInput(""); err != nil {
        fmt.Printf("Validation error: %v\n", err)
    } else if err := processData("valid"); err != nil {
        fmt.Printf("Processing error: %v\n", err)
    } else {
        fmt.Println("All operations successful")
    }
}

func divide(a, b int) (int, error) {
    if b == 0 {
        return 0, fmt.Errorf("division by zero")
    }
    return a / b, nil
}

func validateInput(input string) error {
    if input == "" {
        return fmt.Errorf("input cannot be empty")
    }
    return nil
}

func processData(input string) error {
    if input == "invalid" {
        return fmt.Errorf("invalid input")
    }
    return nil
}
```

### Switch Statements
```go
package main

import "fmt"

func main() {
    // Basic switch statement
    day := 3
    switch day {
    case 1:
        fmt.Println("Monday")
    case 2:
        fmt.Println("Tuesday")
    case 3:
        fmt.Println("Wednesday")
    case 4:
        fmt.Println("Thursday")
    case 5:
        fmt.Println("Friday")
    case 6:
        fmt.Println("Saturday")
    case 7:
        fmt.Println("Sunday")
    default:
        fmt.Println("Invalid day")
    }
    
    // Switch with multiple cases
    grade := 'B'
    switch grade {
    case 'A', 'B', 'C':
        fmt.Println("Passing grade")
    case 'D', 'F':
        fmt.Println("Failing grade")
    default:
        fmt.Println("Invalid grade")
    }
    
    // Switch with initialization
    switch num := 15; {
    case num%2 == 0:
        fmt.Printf("%d is even\n", num)
    case num%3 == 0:
        fmt.Printf("%d is divisible by 3\n", num)
    case num%5 == 0:
        fmt.Printf("%d is divisible by 5\n", num)
    default:
        fmt.Printf("%d is not divisible by 2, 3, or 5\n", num)
    }
    
    // Switch with strings
    fruit := "apple"
    switch fruit {
    case "apple":
        fmt.Println("Red fruit")
    case "banana":
        fmt.Println("Yellow fruit")
    case "orange":
        fmt.Println("Orange fruit")
    default:
        fmt.Println("Unknown fruit")
    }
    
    // Switch with type assertion
    var value interface{} = 42
    switch v := value.(type) {
    case int:
        fmt.Printf("Integer: %d\n", v)
    case float64:
        fmt.Printf("Float: %f\n", v)
    case string:
        fmt.Printf("String: %s\n", v)
    case bool:
        fmt.Printf("Boolean: %t\n", v)
    default:
        fmt.Printf("Unknown type: %T\n", v)
    }
    
    // Switch without break (fallthrough)
    num := 2
    switch num {
    case 1:
        fmt.Println("One")
        fallthrough
    case 2:
        fmt.Println("Two")
        fallthrough
    case 3:
        fmt.Println("Three")
    default:
        fmt.Println("Other")
    }
    
    // Switch for menu selection
    choice := 2
    switch choice {
    case 1:
        fmt.Println("Option 1: View profile")
    case 2:
        fmt.Println("Option 2: Edit profile")
    case 3:
        fmt.Println("Option 3: Delete profile")
    case 4:
        fmt.Println("Option 4: Exit")
    default:
        fmt.Println("Invalid choice")
    }
    
    // Switch for HTTP status codes
    statusCode := 404
    switch statusCode {
    case 200:
        fmt.Println("OK")
    case 201:
        fmt.Println("Created")
    case 400:
        fmt.Println("Bad Request")
    case 401:
        fmt.Println("Unauthorized")
    case 403:
        fmt.Println("Forbidden")
    case 404:
        fmt.Println("Not Found")
    case 500:
        fmt.Println("Internal Server Error")
    default:
        fmt.Println("Unknown status code")
    }
    
    // Switch for file extensions
    filename := "document.pdf"
    var ext string
    if idx := len(filename) - 4; idx > 0 && filename[idx] == '.' {
        ext = filename[idx+1:]
    }
    
    switch ext {
    case "txt":
        fmt.Println("Text file")
    case "pdf":
        fmt.Println("PDF file")
    case "doc", "docx":
        fmt.Println("Word document")
    case "jpg", "jpeg", "png", "gif":
        fmt.Println("Image file")
    default:
        fmt.Println("Unknown file type")
    }
}
```

## Looping Structures

### For Loops
```go
package main

import "fmt"

func main() {
    // Basic for loop
    for i := 0; i < 5; i++ {
        fmt.Printf("Iteration %d\n", i)
    }
    
    // For loop with multiple variables
    for i, j := 0, 10; i < j; i, j = i+1, j-1 {
        fmt.Printf("i=%d, j=%d\n", i, j)
    }
    
    // For loop as while loop
    count := 0
    for count < 5 {
        fmt.Printf("Count: %d\n", count)
        count++
    }
    
    // Infinite loop with break
    i := 0
    for {
        if i >= 5 {
            break
        }
        fmt.Printf("Infinite loop iteration %d\n", i)
        i++
    }
    
    // For loop with continue
    for i := 0; i < 10; i++ {
        if i%2 == 0 {
            continue
        }
        fmt.Printf("Odd number: %d\n", i)
    }
    
    // For loop with range (slice)
    numbers := []int{10, 20, 30, 40, 50}
    for i, num := range numbers {
        fmt.Printf("Index %d: %d\n", i, num)
    }
    
    // For loop with range (map)
    ages := map[string]int{
        "Alice": 25,
        "Bob":   30,
        "Carol": 28,
    }
    for name, age := range ages {
        fmt.Printf("%s is %d years old\n", name, age)
    }
    
    // For loop with range (string)
    message := "Hello, World!"
    for i, char := range message {
        fmt.Printf("Index %d: %c\n", i, char)
    }
    
    // For loop with range (only keys)
    for name := range ages {
        fmt.Printf("Name: %s\n", name)
    }
    
    // For loop with range (only values)
    for _, age := range ages {
        fmt.Printf("Age: %d\n", age)
    }
    
    // Nested for loops
    for i := 1; i <= 3; i++ {
        for j := 1; j <= 3; j++ {
            fmt.Printf("%d x %d = %d\n", i, j, i*j)
        }
    }
    
    // For loop with labels
    outer:
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if i == 1 && j == 1 {
                break outer
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // For loop with labels and continue
    outer2:
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if i == 1 && j == 1 {
                continue outer2
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // For loop with multiple conditions
    numbers2 := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    sum := 0
    for _, num := range numbers2 {
        if num > 5 {
            break
        }
        if num%2 == 0 {
            continue
        }
        sum += num
    }
    fmt.Printf("Sum of odd numbers <= 5: %d\n", sum)
    
    // For loop for filtering
    evenNumbers := []int{}
    for _, num := range numbers2 {
        if num%2 == 0 {
            evenNumbers = append(evenNumbers, num)
        }
    }
    fmt.Printf("Even numbers: %v\n", evenNumbers)
    
    // For loop for searching
    target := 7
    found := false
    for i, num := range numbers2 {
        if num == target {
            fmt.Printf("Found %d at index %d\n", target, i)
            found = true
            break
        }
    }
    if !found {
        fmt.Printf("%d not found\n", target)
    }
    
    // For loop with defer
    for i := 0; i < 3; i++ {
        defer fmt.Printf("Deferred iteration %d\n", i)
        fmt.Printf("Iteration %d\n", i)
    }
}
```

### Loop Control Statements
```go
package main

import "fmt"

func main() {
    // Break statement examples
    fmt.Println("Break examples:")
    
    // Break from inner loop
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if i == 1 && j == 1 {
                fmt.Println("Breaking from inner loop")
                break
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // Break from outer loop using label
    fmt.Println("\nBreak from outer loop using label:")
    outer:
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if i == 1 && j == 1 {
                fmt.Println("Breaking from outer loop")
                break outer
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // Break from switch within loop
    fmt.Println("\nBreak from switch within loop:")
    for i := 0; i < 5; i++ {
        switch i {
        case 2:
            fmt.Println("Breaking from loop")
            break
        default:
            fmt.Printf("i=%d\n", i)
        }
    }
    
    // Continue statement examples
    fmt.Println("\nContinue examples:")
    
    // Skip even numbers
    for i := 0; i < 10; i++ {
        if i%2 == 0 {
            continue
        }
        fmt.Printf("Odd number: %d\n", i)
    }
    
    // Continue with label
    fmt.Println("\nContinue with label:")
    outer:
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if i == 1 && j == 1 {
                fmt.Println("Continuing outer loop")
                continue outer
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // Continue in nested loops
    fmt.Println("\nContinue in nested loops:")
    for i := 0; i < 3; i++ {
        for j := 0; j < 3; j++ {
            if j == 1 {
                continue
            }
            fmt.Printf("i=%d, j=%d\n", i, j)
        }
    }
    
    // Goto statement examples (use sparingly)
    fmt.Println("\nGoto examples:")
    
    // Goto for error handling
    i := 0
    start:
    if i < 3 {
        fmt.Printf("Iteration %d\n", i)
        i++
        goto start
    }
    
    // Goto for cleanup
    file, err := os.Open("test.txt")
    if err != nil {
        fmt.Printf("Error opening file: %v\n", err)
        goto cleanup
    }
    
    defer file.Close()
    
    // Process file
    fmt.Println("File processed successfully")
    
    cleanup:
    fmt.Println("Cleanup completed")
    
    // Loop patterns
    fmt.Println("\nLoop patterns:")
    
    // Pattern 1: Find first match
    numbers := []int{5, 10, 15, 20, 25}
    target := 15
    var foundIndex int = -1
    
    for i, num := range numbers {
        if num == target {
            foundIndex = i
            break
        }
    }
    
    if foundIndex != -1 {
        fmt.Printf("Found %d at index %d\n", target, foundIndex)
    } else {
        fmt.Printf("%d not found\n", target)
    }
    
    // Pattern 2: Filter and collect
    numbers2 := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    var primes []int
    
    for _, num := range numbers2 {
        if isPrime(num) {
            primes = append(primes, num)
        }
    }
    
    fmt.Printf("Prime numbers: %v\n", primes)
    
    // Pattern 3: Reduce/Aggregate
    sum := 0
    for _, num := range numbers2 {
        sum += num
    }
    fmt.Printf("Sum of numbers: %d\n", sum)
    
    // Pattern 4: Map/Transform
    doubled := make([]int, len(numbers2))
    for i, num := range numbers2 {
        doubled[i] = num * 2
    }
    fmt.Printf("Doubled numbers: %v\n", doubled)
    
    // Pattern 5: Early termination
    for i := 0; i < 100; i++ {
        if i > 10 {
            break
        }
        fmt.Printf("Processing item %d\n", i)
    }
    
    // Pattern 6: Conditional iteration
    for i := 0; i < len(numbers2); i++ {
        if numbers2[i] > 5 {
            fmt.Printf("First number > 5: %d at index %d\n", numbers2[i], i)
            break
        }
    }
    
    // Pattern 7: Loop with counter and condition
    count := 0
    for i := 0; i < len(numbers2) && count < 3; i++ {
        if numbers2[i]%2 == 0 {
            fmt.Printf("Even number %d: %d\n", count+1, numbers2[i])
            count++
        }
    }
    
    // Pattern 8: Loop with timeout simulation
    startTime := time.Now()
    timeout := 2 * time.Second
    
    for {
        if time.Since(startTime) > timeout {
            fmt.Println("Timeout reached")
            break
        }
        
        // Simulate work
        time.Sleep(100 * time.Millisecond)
        fmt.Println("Working...")
    }
}

func isPrime(n int) bool {
    if n <= 1 {
        return false
    }
    if n == 2 {
        return true
    }
    if n%2 == 0 {
        return false
    }
    
    for i := 3; i*i <= n; i += 2 {
        if n%i == 0 {
            return false
        }
    }
    
    return true
}
```

## Range Iteration

### Range with Different Data Types
```go
package main

import "fmt"

func main() {
    // Range with slices
    fmt.Println("Range with slices:")
    fruits := []string{"apple", "banana", "cherry", "date"}
    
    // Range with index and value
    for i, fruit := range fruits {
        fmt.Printf("Index %d: %s\n", i, fruit)
    }
    
    // Range with only value
    for _, fruit := range fruits {
        fmt.Printf("Fruit: %s\n", fruit)
    }
    
    // Range with only index
    for i := range fruits {
        fmt.Printf("Index: %d\n", i)
    }
    
    // Range with maps
    fmt.Println("\nRange with maps:")
    studentGrades := map[string]int{
        "Alice": 85,
        "Bob":   92,
        "Carol": 78,
        "David": 95,
    }
    
    // Range with key and value
    for name, grade := range studentGrades {
        fmt.Printf("%s: %d\n", name, grade)
    }
    
    // Range with only key
    for name := range studentGrades {
        fmt.Printf("Student: %s\n", name)
    }
    
    // Range with only value
    for _, grade := range studentGrades {
        fmt.Printf("Grade: %d\n", grade)
    }
    
    // Range with strings
    fmt.Println("\nRange with strings:")
    message := "Hello, World!"
    
    // Range with index and rune
    for i, char := range message {
        fmt.Printf("Index %d: %c (Unicode: %U)\n", i, char, char)
    }
    
    // Range with only rune
    for _, char := range message {
        fmt.Printf("Character: %c\n", char)
    }
    
    // Range with only index
    for i := range message {
        fmt.Printf("Index: %d\n", i)
    }
    
    // Range with arrays
    fmt.Println("\nRange with arrays:")
    numbers := [5]int{10, 20, 30, 40, 50}
    
    for i, num := range numbers {
        fmt.Printf("Index %d: %d\n", i, num)
    }
    
    // Range with channels
    fmt.Println("\nRange with channels:")
    ch := make(chan int, 3)
    ch <- 1
    ch <- 2
    ch <- 3
    close(ch)
    
    for value := range ch {
        fmt.Printf("Received: %d\n", value)
    }
    
    // Range with custom types
    fmt.Println("\nRange with custom types:")
    type Person struct {
        Name string
        Age  int
    }
    
    people := []Person{
        {"Alice", 25},
        {"Bob", 30},
        {"Carol", 28},
    }
    
    for i, person := range people {
        fmt.Printf("Person %d: %s, %d years old\n", i, person.Name, person.Age)
    }
    
    // Range for filtering
    fmt.Println("\nRange for filtering:")
    numbers2 := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Filter even numbers
    var evenNumbers []int
    for _, num := range numbers2 {
        if num%2 == 0 {
            evenNumbers = append(evenNumbers, num)
        }
    }
    fmt.Printf("Even numbers: %v\n", evenNumbers)
    
    // Filter strings by length
    words := []string{"apple", "banana", "kiwi", "strawberry", "fig"}
    var longWords []string
    for _, word := range words {
        if len(word) > 5 {
            longWords = append(longWords, word)
        }
    }
    fmt.Printf("Long words: %v\n", longWords)
    
    // Range for transformation
    fmt.Println("\nRange for transformation:")
    
    // Double each number
    doubled := make([]int, len(numbers2))
    for i, num := range numbers2 {
        doubled[i] = num * 2
    }
    fmt.Printf("Doubled numbers: %v\n", doubled)
    
    // Convert to uppercase
    fruits2 := []string{"apple", "banana", "cherry"}
    uppercase := make([]string, len(fruits2))
    for i, fruit := range fruits2 {
        uppercase[i] = strings.ToUpper(fruit)
    }
    fmt.Printf("Uppercase fruits: %v\n", uppercase)
    
    // Range for aggregation
    fmt.Println("\nRange for aggregation:")
    
    // Sum all numbers
    sum := 0
    for _, num := range numbers2 {
        sum += num
    }
    fmt.Printf("Sum: %d\n", sum)
    
    // Find maximum
    max := numbers2[0]
    for _, num := range numbers2 {
        if num > max {
            max = num
        }
    }
    fmt.Printf("Maximum: %d\n", max)
    
    // Count occurrences
    grades := []int{85, 92, 78, 95, 85, 92, 78}
    gradeCounts := make(map[int]int)
    for _, grade := range grades {
        gradeCounts[grade]++
    }
    fmt.Printf("Grade counts: %v\n", gradeCounts)
    
    // Range with nested structures
    fmt.Println("\nRange with nested structures:")
    type Student struct {
        Name    string
        Grades  []int
        Address struct {
            Street string
            City   string
        }
    }
    
    students := []Student{
        {
            Name:   "Alice",
            Grades: []int{85, 92, 78},
            Address: struct {
                Street string
                City   string
            }{"123 Main St", "New York"},
        },
        {
            Name:   "Bob",
            Grades: []int{90, 88, 95},
            Address: struct {
                Street string
                City   string
            }{"456 Oak Ave", "Boston"},
        },
    }
    
    for _, student := range students {
        fmt.Printf("Student: %s\n", student.Name)
        fmt.Printf("Address: %s, %s\n", student.Address.Street, student.Address.City)
        
        total := 0
        for _, grade := range student.Grades {
            total += grade
        }
        avg := float64(total) / float64(len(student.Grades))
        fmt.Printf("Average grade: %.2f\n", avg)
        fmt.Println()
    }
    
    // Range with error handling
    fmt.Println("\nRange with error handling:")
    data := map[string]interface{}{
        "name":   "John",
        "age":    30,
        "active": true,
        "score":  85.5,
    }
    
    for key, value := range data {
        switch v := value.(type) {
        case string:
            fmt.Printf("%s (string): %s\n", key, v)
        case int:
            fmt.Printf("%s (int): %d\n", key, v)
        case bool:
            fmt.Printf("%s (bool): %t\n", key, v)
        case float64:
            fmt.Printf("%s (float64): %.2f\n", key, v)
        default:
            fmt.Printf("%s (unknown): %v\n", key, v)
        }
    }
    
    // Range for performance comparison
    fmt.Println("\nRange for performance comparison:")
    largeSlice := make([]int, 1000000)
    for i := range largeSlice {
        largeSlice[i] = i
    }
    
    // Traditional for loop
    start := time.Now()
    sum1 := 0
    for i := 0; i < len(largeSlice); i++ {
        sum1 += largeSlice[i]
    }
    duration1 := time.Since(start)
    
    // Range loop
    start = time.Now()
    sum2 := 0
    for _, num := range largeSlice {
        sum2 += num
    }
    duration2 := time.Since(start)
    
    fmt.Printf("Traditional for loop: %d (took %v)\n", sum1, duration1)
    fmt.Printf("Range loop: %d (took %v)\n", sum2, duration2)
}
```

## Exception Handling

### Error Handling in Go
```go
package main

import "fmt"
import "errors"

// Custom error type
type ValidationError struct {
    Field   string
    Message string
}

func (e ValidationError) Error() string {
    return fmt.Sprintf("validation error in field '%s': %s", e.Field, e.Message)
}

// Function that returns an error
func divide(a, b float64) (float64, error) {
    if b == 0 {
        return 0, errors.New("division by zero")
    }
    return a / b, nil
}

// Function that returns custom error
func validateAge(age int) error {
    if age < 0 {
        return ValidationError{"age", "age cannot be negative"}
    }
    if age > 120 {
        return ValidationError{"age", "age seems unrealistic"}
    }
    return nil
}

// Function that wraps errors
func processFile(filename string) error {
    data, err := os.ReadFile(filename)
    if err != nil {
        return fmt.Errorf("failed to read file '%s': %w", filename, err)
    }
    
    // Process data
    fmt.Printf("File content: %s\n", string(data))
    return nil
}

// Function that handles multiple errors
func validateUser(user map[string]interface{}) error {
    var errors []error
    
    // Check name
    if name, ok := user["name"].(string); !ok || name == "" {
        errors = append(errors, ValidationError{"name", "name is required"})
    }
    
    // Check age
    if age, ok := user["age"].(int); ok {
        if err := validateAge(age); err != nil {
            errors = append(errors, err)
        }
    } else {
        errors = append(errors, ValidationError{"age", "age is required and must be an integer"})
    }
    
    // Check email
    if email, ok := user["email"].(string); !ok || email == "" {
        errors = append(errors, ValidationError{"email", "email is required"})
    }
    
    if len(errors) > 0 {
        return fmt.Errorf("validation failed: %v", errors)
    }
    
    return nil
}

// Function with panic and recover
func riskyOperation() {
    defer func() {
        if r := recover(); r != nil {
            fmt.Printf("Recovered from panic: %v\n", r)
        }
    }()
    
    // Simulate a panic
    panic("something went wrong")
}

// Function that demonstrates different error handling patterns
func demonstrateErrorHandling() {
    fmt.Println("=== Error Handling Examples ===")
    
    // 1. Basic error handling
    fmt.Println("\n1. Basic error handling:")
    result, err := divide(10, 2)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Result: %.2f\n", result)
    }
    
    result, err = divide(10, 0)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Result: %.2f\n", result)
    }
    
    // 2. Custom error handling
    fmt.Println("\n2. Custom error handling:")
    err = validateAge(25)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Println("Age is valid")
    }
    
    err = validateAge(-5)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
        
        // Type assertion for custom error
        if validationErr, ok := err.(ValidationError); ok {
            fmt.Printf("Field: %s, Message: %s\n", validationErr.Field, validationErr.Message)
        }
    }
    
    // 3. Error wrapping
    fmt.Println("\n3. Error wrapping:")
    err = processFile("nonexistent.txt")
    if err != nil {
        fmt.Printf("Error: %v\n", err)
        
        // Unwrap error
        unwrapped := errors.Unwrap(err)
        if unwrapped != nil {
            fmt.Printf("Unwrapped error: %v\n", unwrapped)
        }
    }
    
    // 4. Multiple errors
    fmt.Println("\n4. Multiple errors:")
    user := map[string]interface{}{
        "name":  "",
        "age":   -5,
        "email": "",
    }
    
    err = validateUser(user)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    }
    
    // 5. Panic and recover
    fmt.Println("\n5. Panic and recover:")
    riskyOperation()
    fmt.Println("Program continues after panic")
    
    // 6. Defer for cleanup
    fmt.Println("\n6. Defer for cleanup:")
    err = fileOperation()
    if err != nil {
        fmt.Printf("File operation error: %v\n", err)
    }
    
    // 7. Error handling with defer
    fmt.Println("\n7. Error handling with defer:")
    err = deferredOperation()
    if err != nil {
        fmt.Printf("Deferred operation error: %v\n", err)
    }
    
    // 8. Error handling in loops
    fmt.Println("\n8. Error handling in loops:")
    err = processItems([]string{"item1", "item2", "item3"})
    if err != nil {
        fmt.Printf("Process items error: %v\n", err)
    }
}

func fileOperation() error {
    file, err := os.Open("test.txt")
    if err != nil {
        return err
    }
    defer file.Close()
    
    // Process file
    fmt.Println("File opened successfully")
    return nil
}

func deferredOperation() error {
    var err error
    
    defer func() {
        if err != nil {
            fmt.Printf("Cleanup after error: %v\n", err)
        }
    }()
    
    // Simulate operation
    err = errors.New("simulated error")
    return err
}

func processItems(items []string) error {
    for i, item := range items {
        if err := processItem(item); err != nil {
            return fmt.Errorf("failed to process item %d (%s): %w", i, item, err)
        }
    }
    return nil
}

func processItem(item string) error {
    if item == "item2" {
        return errors.New("item2 cannot be processed")
    }
    fmt.Printf("Processed: %s\n", item)
    return nil
}

// Error handling best practices
func bestPractices() {
    fmt.Println("\n=== Error Handling Best Practices ===")
    
    // 1. Always handle errors
    fmt.Println("\n1. Always handle errors:")
    if err := someOperation(); err != nil {
        fmt.Printf("Operation failed: %v\n", err)
        return
    }
    fmt.Println("Operation succeeded")
    
    // 2. Use meaningful error messages
    fmt.Println("\n2. Use meaningful error messages:")
    err := meaningfulError()
    if err != nil {
        fmt.Printf("Meaningful error: %v\n", err)
    }
    
    // 3. Use custom error types
    fmt.Println("\n3. Use custom error types:")
    err = customTypeError()
    if err != nil {
        switch e := err.(type) {
        case *BusinessError:
            fmt.Printf("Business error: %s (Code: %d)\n", e.Message, e.Code)
        case *SystemError:
            fmt.Printf("System error: %s (Component: %s)\n", e.Message, e.Component)
        default:
            fmt.Printf("Unknown error: %v\n", e)
        }
    }
    
    // 4. Wrap errors with context
    fmt.Println("\n4. Wrap errors with context:")
    err = wrappedError()
    if err != nil {
        fmt.Printf("Wrapped error: %v\n", err)
        
        // Check if error is of specific type
        if errors.Is(err, ErrNotFound) {
            fmt.Println("Item not found")
        }
    }
    
    // 5. Use defer for cleanup
    fmt.Println("\n5. Use defer for cleanup:")
    err = cleanupOperation()
    if err != nil {
        fmt.Printf("Cleanup operation error: %v\n", err)
    }
}

// Custom error types for best practices
type BusinessError struct {
    Code    int
    Message string
}

func (e *BusinessError) Error() string {
    return fmt.Sprintf("Business error %d: %s", e.Code, e.Message)
}

type SystemError struct {
    Component string
    Message  string
}

func (e *SystemError) Error() string {
    return fmt.Sprintf("System error in %s: %s", e.Component, e.Message)
}

var ErrNotFound = errors.New("item not found")

func someOperation() error {
    return nil
}

func meaningfulError() error {
    return fmt.Errorf("failed to process order %d: payment method expired", 12345)
}

func customTypeError() error {
    return &BusinessError{Code: 1001, Message: "Insufficient funds"}
}

func wrappedError() error {
    err := errors.New("database connection failed")
    return fmt.Errorf("failed to save user: %w", err)
}

func cleanupOperation() error {
    resource, err := acquireResource()
    if err != nil {
        return err
    }
    defer releaseResource(resource)
    
    // Use resource
    return useResource(resource)
}

func acquireResource() (string, error) {
    return "resource-123", nil
}

func releaseResource(resource string) {
    fmt.Printf("Released resource: %s\n", resource)
}

func useResource(resource string) error {
    fmt.Printf("Using resource: %s\n", resource)
    return nil
}

func main() {
    demonstrateErrorHandling()
    bestPractices()
}
```

## Summary

Go control structures provide:

**Conditional Statements:**
- `if-else` statements for branching logic
- `if` with initialization for variable scoping
- `switch` statements for multi-way branching
- Type switches for interface{} type checking
- Fallthrough for switch case execution

**Looping Structures:**
- `for` loops for iteration
- `for` as `while` loop
- Infinite loops with `break`
- `range` iteration for slices, maps, strings, arrays, and channels
- Loop control with `break`, `continue`, and `goto`

**Loop Control:**
- `break` to exit loops early
- `continue` to skip iterations
- Labeled `break` and `continue` for nested loops
- `goto` for complex control flow (use sparingly)
- Loop patterns for common operations

**Range Iteration:**
- Range over slices, arrays, maps, strings, and channels
- Index and value iteration
- Key and value iteration for maps
- Rune iteration for strings
- Channel iteration until closed

**Error Handling:**
- Multiple return values for error handling
- Custom error types
- Error wrapping with context
- `panic` and `recover` for exceptional cases
- `defer` for resource cleanup

**Best Practices:**
- Always handle errors explicitly
- Use meaningful error messages
- Wrap errors with context
- Use `defer` for cleanup operations
- Prefer error returns over panics
- Use custom error types for domain-specific errors

Go's control structures provide clear, concise, and powerful ways to control program flow while maintaining the language's philosophy of simplicity and explicit error handling.
