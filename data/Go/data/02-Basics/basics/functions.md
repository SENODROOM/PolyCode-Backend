# Go Functions

## Function Declaration

### Basic Function Syntax
```go
package main

import "fmt"

// Basic function declaration
func greet(name string) {
    fmt.Printf("Hello, %s!\n", name)
}

// Function with parameters and return value
func add(a, b int) int {
    return a + b
}

// Function with multiple return values
func divide(a, b int) (int, error) {
    if b == 0 {
        return 0, fmt.Errorf("division by zero")
    }
    return a / b, nil
}

// Function with named return values
func calculate(a, b int) (sum int, difference int) {
    sum = a + b
    difference = a - b
    return
}

func main() {
    // Calling basic functions
    greet("John")
    
    result := add(10, 20)
    fmt.Printf("10 + 20 = %d\n", result)
    
    quotient, err := divide(10, 2)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("10 / 2 = %d\n", quotient)
    }
    
    sum, diff := calculate(10, 5)
    fmt.Printf("Sum: %d, Difference: %d\n", sum, diff)
}
```

### Function Parameters

### Parameter Types
```go
package main

import "fmt"

// Value parameters (default in Go)
func incrementValue(x int) {
    x++
    fmt.Printf("Inside incrementValue: x = %d\n", x)
}

// Pointer parameters
func incrementPointer(x *int) {
    *x++
    fmt.Printf("Inside incrementPointer: *x = %d\n", *x)
}

// Array parameters
func printArray(arr [5]int) {
    fmt.Printf("Array: %v\n", arr)
}

// Slice parameters
func printSlice(slice []int) {
    fmt.Printf("Slice: %v\n", slice)
}

// Map parameters
func printMap(m map[string]int) {
    fmt.Printf("Map: %v\n", m)
}

// Interface parameters
func printInterface(val interface{}) {
    fmt.Printf("Interface value: %v (type: %T)\n", val, val)
}

// Variadic parameters
func sum(numbers ...int) int {
    total := 0
    for _, num := range numbers {
        total += num
    }
    return total
}

func main() {
    // Value parameter example
    x := 10
    fmt.Printf("Before incrementValue: x = %d\n", x)
    incrementValue(x)
    fmt.Printf("After incrementValue: x = %d\n", x)
    
    // Pointer parameter example
    y := 10
    fmt.Printf("Before incrementPointer: y = %d\n", y)
    incrementPointer(&y)
    fmt.Printf("After incrementPointer: y = %d\n", y)
    
    // Array parameter example
    arr := [5]int{1, 2, 3, 4, 5}
    printArray(arr)
    
    // Slice parameter example
    slice := []int{1, 2, 3, 4, 5}
    printSlice(slice)
    
    // Map parameter example
    m := map[string]int{"apple": 1, "banana": 2, "cherry": 3}
    printMap(m)
    
    // Interface parameter example
    printInterface(42)
    printInterface("hello")
    printInterface([]int{1, 2, 3})
    
    // Variadic parameter example
    total := sum(1, 2, 3, 4, 5)
    fmt.Printf("Sum of 1,2,3,4,5: %d\n", total)
}
```

### Function with Multiple Parameters
```go
package main

import "fmt"

// Function with multiple parameters
func createUser(name string, age int, email string, isActive bool) string {
    status := "inactive"
    if isActive {
        status = "active"
    }
    
    return fmt.Sprintf("User: %s, Age: %d, Email: %s, Status: %s", name, age, email, status)
}

// Function with parameters of different types
func processOrder(id int, items []string, total float64, paid bool, metadata map[string]string) {
    fmt.Printf("Order ID: %d\n", id)
    fmt.Printf("Items: %v\n", items)
    fmt.Printf("Total: $%.2f\n", total)
    fmt.Printf("Paid: %t\n", paid)
    fmt.Printf("Metadata: %v\n", metadata)
}

// Function with optional parameters (using struct)
type UserConfig struct {
    name       string
    age        int
    email      string
    isActive   bool
    department string
}

func createUserWithConfig(config UserConfig) string {
    status := "inactive"
    if config.isActive {
        status = "active"
    }
    
    dept := "general"
    if config.department != "" {
        dept = config.department
    }
    
    return fmt.Sprintf("User: %s, Age: %d, Email: %s, Status: %s, Department: %s", 
        config.name, config.age, config.email, status, dept)
}

func main() {
    // Multiple parameters example
    user := createUser("John Doe", 30, "john@example.com", true)
    fmt.Println(user)
    
    // Process order example
    orderItems := []string{"Laptop", "Mouse", "Keyboard"}
    orderMetadata := map[string]string{
        "shipping": "express",
        "priority": "high",
    }
    
    processOrder(1001, orderItems, 1299.99, true, orderMetadata)
    
    // Optional parameters example
    config := UserConfig{
        name:       "Jane Smith",
        age:        25,
        email:      "jane@example.com",
        isActive:   true,
        department: "Engineering",
    }
    
    user2 := createUserWithConfig(config)
    fmt.Println(user2)
    
    // Minimal config
    minimalConfig := UserConfig{
        name:     "Bob Johnson",
        age:      35,
        email:    "bob@example.com",
        isActive: false,
    }
    
    user3 := createUserWithConfig(minimalConfig)
    fmt.Println(user3)
}
```

## Return Values

### Single and Multiple Return Values
```go
package main

import "fmt"

// Single return value
func getMessage() string {
    return "Hello, World!"
}

// Multiple return values
func divideAndRemainder(a, b int) (int, int) {
    quotient := a / b
    remainder := a % b
    return quotient, remainder
}

// Named return values
func calculateStats(numbers []int) (min, max, sum int) {
    min = numbers[0]
    max = numbers[0]
    sum = 0
    
    for _, num := range numbers {
        if num < min {
            min = num
        }
        if num > max {
            max = num
        }
        sum += num
    }
    
    return
}

// Function returning error
func sqrt(x float64) (float64, error) {
    if x < 0 {
        return 0, fmt.Errorf("cannot calculate square root of negative number")
    }
    return x * x, nil
}

// Function returning interface{}
func processData(data interface{}) (interface{}, error) {
    switch v := data.(type) {
    case int:
        return v * 2, nil
    case string:
        return v + " processed", nil
    case []int:
        sum := 0
        for _, num := range v {
            sum += num
        }
        return sum, nil
    default:
        return nil, fmt.Errorf("unsupported data type: %T", data)
    }
}

func main() {
    // Single return value
    msg := getMessage()
    fmt.Println(msg)
    
    // Multiple return values
    quotient, remainder := divideAndRemainder(10, 3)
    fmt.Printf("10 divided by 3: quotient = %d, remainder = %d\n", quotient, remainder)
    
    // Named return values
    numbers := []int{5, 2, 8, 1, 9}
    min, max, sum := calculateStats(numbers)
    fmt.Printf("Numbers: %v\n", numbers)
    fmt.Printf("Min: %d, Max: %d, Sum: %d\n", min, max, sum)
    
    // Function returning error
    result, err := sqrt(16)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Square root of 16: %f\n", result)
    }
    
    result2, err2 := sqrt(-4)
    if err2 != nil {
        fmt.Printf("Error: %v\n", err2)
    } else {
        fmt.Printf("Square root of -4: %f\n", result2)
    }
    
    // Function returning interface{}
    processed, err := processData(42)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Processed result: %v (type: %T)\n", processed, processed)
    }
}
```

### Error Handling with Return Values
```go
package main

import "fmt"
import "os"

// Custom error type
type ValidationError struct {
    Field   string
    Message string
}

func (e ValidationError) Error() string {
    return fmt.Sprintf("Validation error in field '%s': %s", e.Field, e.Message)
}

// Function that returns custom error
func validateEmail(email string) error {
    if email == "" {
        return ValidationError{"email", "email cannot be empty"}
    }
    
    if !contains(email, "@") {
        return ValidationError{"email", "email must contain @ symbol"}
    }
    
    return nil
}

// Function that returns multiple values including error
func parseUserData(data map[string]interface{}) (map[string]interface{}, error) {
    var result map[string]interface{} = make(map[string]interface{})
    
    // Validate required fields
    if _, ok := data["name"]; !ok {
        return nil, ValidationError{"name", "name is required"}
    }
    
    if _, ok := data["age"]; !ok {
        return nil, ValidationError{"age", "age is required"}
    }
    
    // Process data
    for key, value := range data {
        switch key {
        case "name":
            if str, ok := value.(string); ok {
                result[key] = strings.ToUpper(str)
            }
        case "age":
            if num, ok := value.(float64); ok {
                result[key] = int(num)
            }
        case "email":
            if str, ok := value.(string); ok {
                if err := validateEmail(str); err != nil {
                    return nil, err
                }
                result[key] = strings.ToLower(str)
            }
        default:
            result[key] = value
        }
    }
    
    return result, nil
}

// Function that handles file operations with error
func readFile(filename string) (string, error) {
    data, err := os.ReadFile(filename)
    if err != nil {
        return "", fmt.Errorf("failed to read file '%s': %w", filename, err)
    }
    return string(data), nil
}

// Function that handles database operations
func getUserByID(id int) (map[string]interface{}, error) {
    // Simulate database query
    if id == 0 {
        return nil, fmt.Errorf("user ID cannot be zero")
    }
    
    if id == 999 {
        return nil, fmt.Errorf("user not found")
    }
    
    // Simulate found user
    return map[string]interface{}{
        "id":    id,
        "name":  "John Doe",
        "email": "john@example.com",
        "age":   30,
    }, nil
}

func main() {
    // Error handling examples
    data := map[string]interface{}{
        "name":  "John Doe",
        "age":   30.5,
        "email": "JOHN@EXAMPLE.COM",
    }
    
    // Parse user data with error handling
    userData, err := parseUserData(data)
    if err != nil {
        fmt.Printf("Error parsing user data: %v\n", err)
        
        // Type assertion to get specific error type
        if validationErr, ok := err.(ValidationError); ok {
            fmt.Printf("Field: %s, Message: %s\n", validationErr.Field, validationErr.Message)
        }
    } else {
        fmt.Printf("Parsed user data: %v\n", userData)
    }
    
    // File operations with error handling
    content, err := readFile("nonexistent.txt")
    if err != nil {
        fmt.Printf("Error reading file: %v\n", err)
    } else {
        fmt.Printf("File content: %s\n", content)
    }
    
    // Database operations with error handling
    user, err := getUserByID(999)
    if err != nil {
        fmt.Printf("Error getting user: %v\n", err)
    } else {
        fmt.Printf("Found user: %v\n", user)
    }
    
    // Chain error handling
    if err := parseUserData(data); err != nil {
        fmt.Printf("First error: %v\n", err)
        return
    }
    
    if err := readFile("config.json"); err != nil {
        fmt.Printf("Second error: %v\n", err)
        return
    }
    
    fmt.Println("All operations completed successfully")
}

// Helper function
func contains(s, substr string) bool {
    return len(s) > 0 && len(substr) > 0 && s[0:len(substr)] == substr
}
```

## Anonymous Functions

### Lambda Functions in Go
```go
package main

import "fmt"

// Function that accepts a function as parameter
func operate(a, b int, operation func(int, int) int) int {
    return operation(a, b)
}

// Higher-order function returning a function
func getMultiplier(factor int) func(int) int {
    return func(x int) int {
        return x * factor
    }
}

// Function returning a closure
func makeCounter() func() int {
    count := 0
    return func() int {
        count++
        return count
    }
}

// Function with anonymous functions
func processNumbers(numbers []int, filter func(int) bool, transform func(int) int) []int {
    var result []int
    
    for _, num := range numbers {
        if filter(num) {
            transformed := transform(num)
            result = append(result, transformed)
        }
    }
    
    return result
}

func main() {
    // Using anonymous function with operate
    sum := operate(5, 3, func(a, b int) int {
        return a + b
    })
    
    difference := operate(5, 3, func(a, b int) int {
        return a - b
    })
    
    product := operate(5, 3, func(a, b int) int {
        return a * b
    })
    
    fmt.Printf("Sum: %d\n", sum)
    fmt.Printf("Difference: %d\n", difference)
    fmt.Printf("Product: %d\n", product)
    
    // Using function that returns a function
    double := getMultiplier(2)
    triple := getMultiplier(3)
    
    fmt.Printf("Double of 10: %d\n", double(10))
    fmt.Printf("Triple of 10: %d\n", triple(10))
    
    // Using closure
    counter := makeCounter()
    fmt.Printf("Count: %d\n", counter())
    fmt.Printf("Count: %d\n", counter())
    fmt.Printf("Count: %d\n", counter())
    
    // Using processNumbers with anonymous functions
    numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Filter even numbers and double them
    evenDoubled := processNumbers(
        numbers,
        func(x int) bool { return x%2 == 0 },
        func(x int) int { return x * 2 },
    )
    
    fmt.Printf("Even numbers doubled: %v\n", evenDoubled)
    
    // Filter numbers greater than 5 and square them
    largeSquared := processNumbers(
        numbers,
        func(x int) bool { return x > 5 },
        func(x int) int { return x * x },
    )
    
    fmt.Printf("Numbers > 5 squared: %v\n", largeSquared)
    
    // Anonymous function as immediate function
    result := func(x int) int {
        return x * x * x
    }(5)
    
    fmt.Printf("5 cubed: %d\n", result)
    
    // Anonymous function in goroutine
    go func() {
        fmt.Println("Anonymous function in goroutine")
    }()
    
    // Wait for goroutine to complete (in real app, use proper synchronization)
    time.Sleep(100 * time.Millisecond)
}
```

## Higher-Order Functions

### Functions that Accept Functions
```go
package main

import "fmt"

// Higher-order function that takes a function and a slice
func filter(numbers []int, predicate func(int) bool) []int {
    var result []int
    for _, num := range numbers {
        if predicate(num) {
            result = append(result, num)
        }
    }
    return result
}

// Higher-order function that takes a function and a slice
func map(numbers []int, transform func(int) int) []int {
    result := make([]int, len(numbers))
    for i, num := range numbers {
        result[i] = transform(num)
    }
    return result
}

// Higher-order function that reduces a slice
func reduce(numbers []int, reducer func(int, int) int, initial int) int {
    result := initial
    for _, num := range numbers {
        result = reducer(result, num)
    }
    return result
}

// Higher-order function that takes a function and applies it to each element
func forEach(numbers []int, action func(int)) {
    for _, num := range numbers {
        action(num)
    }
}

// Higher-order function that composes two functions
func compose(f, g func(int) int) func(int) int {
    return func(x int) int {
        return f(g(x))
    }
}

// Currying example
func curryAdd(a int) func(int) int {
    return func(b int) int {
        return a + b
    }
}

// Partial application example
func partialMultiply(factor int) func(int) int {
    return func(x int) int {
        return x * factor
    }
}

func main() {
    numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Filter example
    evenNumbers := filter(numbers, func(x int) bool { return x%2 == 0 })
    fmt.Printf("Even numbers: %v\n", evenNumbers)
    
    // Map example
    doubled := map(numbers, func(x int) int { return x * 2 })
    fmt.Printf("Doubled numbers: %v\n", doubled)
    
    // Reduce example
    sum := reduce(numbers, func(a, b int) int { return a + b }, 0)
    fmt.Printf("Sum: %d\n", sum)
    
    // ForEach example
    forEach(numbers, func(x int) { fmt.Printf("%d ", x) })
    fmt.Println()
    
    // Composition example
    addOne := func(x int) int { return x + 1 }
    multiplyByTwo := func(x int) int { return x * 2 }
    
    addOneThenMultiplyByTwo := compose(multiplyByTwo, addOne)
    result := addOneThenMultiplyByTwo(5)
    fmt.Printf("Add one then multiply by two (5): %d\n", result)
    
    // Currying example
    addFive := curryAdd(5)
    result2 := addFive(3)
    fmt.Printf("5 + 3 = %d\n", result2)
    
    // Partial application example
    double := partialMultiply(2)
    result3 := double(10)
    fmt.Printf("10 * 2 = %d\n", result3)
    
    // Function composition with multiple functions
    isEven := func(x int) bool { return x%2 == 0 }
    isPositive := func(x int) bool { return x > 0 }
    
    isPositiveEven := func(x int) bool {
        return isPositive(x) && isEven(x)
    }
    
    positiveEvenNumbers := filter(numbers, isPositiveEven)
    fmt.Printf("Positive even numbers: %v\n", positiveEvenNumbers)
    
    // Pipeline example
    pipeline := func(initial []int, operations ...func(int) int) []int {
        result := initial
        for _, op := range operations {
            result = map(result, op)
        }
        return result
    }
    
    result4 := pipeline(
        numbers,
        func(x int) int { return x + 1 },
        func(x int) int { return x * 2 },
        func(x int) int { return x - 3 },
    )
    
    fmt.Printf("Pipeline result: %v\n", result4)
}
```

## Recursion

### Recursive Functions
```go
package main

import "fmt"

// Factorial using recursion
func factorial(n int) int {
    if n <= 1 {
        return 1
    }
    return n * factorial(n-1)
}

// Fibonacci sequence using recursion
func fibonacci(n int) int {
    if n <= 1 {
        return n
    }
    return fibonacci(n-1) + fibonacci(n-2)
}

// Greatest Common Divisor using recursion
func gcd(a, b int) int {
    if b == 0 {
        return a
    }
    return gcd(b, a%b)
}

// Binary search using recursion
func binarySearch(arr []int, target, low, high int) int {
    if low > high {
        return -1
    }
    
    mid := low + (high-low)/2
    
    if arr[mid] == target {
        return mid
    } else if arr[mid] < target {
        return binarySearch(arr, target, mid+1, high)
    } else {
        return binarySearch(arr, target, low, mid-1)
    }
}

// Tree node structure
type TreeNode struct {
    Value int
    Left  *TreeNode
    Right *TreeNode
}

// Insert into BST using recursion
func insert(root *TreeNode, value int) *TreeNode {
    if root == nil {
        return &TreeNode{Value: value}
    }
    
    if value < root.Value {
        root.Left = insert(root.Left, value)
    } else {
        root.Right = insert(root.Right, value)
    }
    
    return root
}

// Search in BST using recursion
func search(root *TreeNode, value int) bool {
    if root == nil {
        return false
    }
    
    if root.Value == value {
        return true
    }
    
    if value < root.Value {
        return search(root.Left, value)
    } else {
        return search(root.Right, value)
    }
}

// Tree traversal using recursion
func inorderTraversal(root *TreeNode) []int {
    if root == nil {
        return []int{}
    }
    
    var result []int
    result = append(result, inorderTraversal(root.Left)...)
    result = append(result, root.Value)
    result = append(result, inorderTraversal(root.Right)...)
    
    return result
}

// Tower of Hanoi using recursion
func towerOfHanoi(n int, from, to, aux string) {
    if n == 1 {
        fmt.Printf("Move disk 1 from %s to %s\n", from, to)
        return
    }
    
    towerOfHanoi(n-1, from, aux, to)
    fmt.Printf("Move disk %d from %s to %s\n", n, from, to)
    towerOfHanoi(n-1, aux, to, from)
}

// Directory traversal using recursion
type FileInfo struct {
    Name string
    IsDir bool
    Size  int64
}

func walkDirectory(path string, callback func(FileInfo)) error {
    entries, err := os.ReadDir(path)
    if err != nil {
        return err
    }
    
    for _, entry := range entries {
        fullPath := filepath.Join(path, entry.Name())
        
        info, err := entry.Info()
        if err != nil {
            return err
        }
        
        fileInfo := FileInfo{
            Name:  entry.Name(),
            IsDir: entry.IsDir(),
            Size:  info.Size(),
        }
        
        callback(fileInfo)
        
        if entry.IsDir() {
            walkDirectory(fullPath, callback)
        }
    }
    
    return nil
}

func main() {
    // Factorial example
    fmt.Printf("Factorial of 5: %d\n", factorial(5))
    
    // Fibonacci example
    fmt.Printf("Fibonacci of 10: %d\n", fibonacci(10))
    
    // GCD example
    fmt.Printf("GCD of 48 and 18: %d\n", gcd(48, 18))
    
    // Binary search example
    sortedArray := []int{1, 3, 5, 7, 9, 11, 13, 15, 17, 19}
    index := binarySearch(sortedArray, 7, 0, len(sortedArray)-1)
    fmt.Printf("Binary search for 7: index %d\n", index)
    
    // BST example
    root := new(TreeNode)
    root = insert(root, 50)
    root = insert(root, 30)
    root = insert(root, 70)
    root = insert(root, 20)
    root = insert(root, 40)
    root = insert(root, 60)
    root = insert(root, 80)
    
    fmt.Printf("Search for 40 in BST: %t\n", search(root, 40))
    fmt.Printf("Search for 90 in BST: %t\n", search(root, 90))
    
    traversal := inorderTraversal(root)
    fmt.Printf("Inorder traversal: %v\n", traversal)
    
    // Tower of Hanoi example
    fmt.Println("Tower of Hanoi with 3 disks:")
    towerOfHanoi(3, "A", "C", "B")
    
    // Directory traversal example
    currentDir, _ := os.Getwd()
    fmt.Printf("Walking directory: %s\n", currentDir)
    
    var totalSize int64
    err := walkDirectory(currentDir, func(fileInfo FileInfo) {
        if !fileInfo.IsDir {
            totalSize += fileInfo.Size
        }
        fmt.Printf("%s (%s, %d bytes)\n", fileInfo.Name, 
            map[bool]string{true: "DIR", false: "FILE"}[fileInfo.IsDir], 
            fileInfo.Size)
    })
    
    if err != nil {
        fmt.Printf("Error walking directory: %v\n", err)
    } else {
        fmt.Printf("Total size of files: %d bytes\n", totalSize)
    }
}
```

## Method Functions

### Methods on Structs
```go
package main

import "fmt"
import "math"

// Rectangle struct with methods
type Rectangle struct {
    Width  float64
    Height float64
}

// Method to calculate area
func (r Rectangle) Area() float64 {
    return r.Width * r.Height
}

// Method to calculate perimeter
func (r Rectangle) Perimeter() float64 {
    return 2 * (r.Width + r.Height)
}

// Method to check if square
func (r Rectangle) IsSquare() bool {
    return r.Width == r.Height
}

// Method to resize
func (r *Rectangle) Resize(width, height float64) {
    r.Width = width
    r.Height = height
}

// Method to scale
func (r *Rectangle) Scale(factor float64) {
    r.Width *= factor
    r.Height *= factor
}

// Stringer method
func (r Rectangle) String() string {
    return fmt.Sprintf("Rectangle{Width: %.2f, Height: %.2f}", r.Width, r.Height)
}

// Circle struct with methods
type Circle struct {
    Radius float64
}

// Method to calculate area
func (c Circle) Area() float64 {
    return math.Pi * c.Radius * c.Radius
}

// Method to calculate circumference
func (c Circle) Circumference() float64 {
    return 2 * math.Pi * c.Radius
}

// Method to resize
func (c *Circle) Resize(radius float64) {
    c.Radius = radius
}

// Stringer method
func (c Circle) String() string {
    return fmt.Sprintf("Circle{Radius: %.2f}", c.Radius)
}

// Shape interface
type Shape interface {
    Area() float64
    Perimeter() float64
}

// Rectangle implements Shape interface
func (r Rectangle) Perimeter() float64 {
    return 2 * (r.Width + r.Height)
}

// Circle implements Shape interface
func (c Circle) Perimeter() float64 {
    return 2 * math.Pi * c.Radius
}

// Function that works with any Shape
func printShapeDetails(shape Shape) {
    fmt.Printf("Area: %.2f\n", shape.Area())
    fmt.Printf("Perimeter: %.2f\n", shape.Perimeter())
}

// Person struct with methods
type Person struct {
    FirstName string
    LastName  string
    Age       int
}

// Method to get full name
func (p Person) FullName() string {
    return p.FirstName + " " + p.LastName
}

// Method to check if adult
func (p Person) IsAdult() bool {
    return p.Age >= 18
}

// Method to update age
func (p *Person) HaveBirthday() {
    p.Age++
}

// Method to change name
func (p *Person) ChangeName(firstName, lastName string) {
    p.FirstName = firstName
    p.LastName = lastName
}

// Stringer method
func (p Person) String() string {
    return fmt.Sprintf("Person{Name: %s, Age: %d}", p.FullName(), p.Age)
}

// BankAccount struct with methods
type BankAccount struct {
    AccountNumber string
    Balance      float64
    Owner        Person
}

// Method to deposit
func (ba *BankAccount) Deposit(amount float64) error {
    if amount <= 0 {
        return fmt.Errorf("deposit amount must be positive")
    }
    
    ba.Balance += amount
    return nil
}

// Method to withdraw
func (ba *BankAccount) Withdraw(amount float64) error {
    if amount <= 0 {
        return fmt.Errorf("withdraw amount must be positive")
    }
    
    if amount > ba.Balance {
        return fmt.Errorf("insufficient funds")
    }
    
    ba.Balance -= amount
    return nil
}

// Method to get balance
func (ba BankAccount) GetBalance() float64 {
    return ba.Balance
}

// Method to transfer
func (ba *BankAccount) Transfer(to *BankAccount, amount float64) error {
    // Withdraw from this account
    if err := ba.Withdraw(amount); err != nil {
        return err
    }
    
    // Deposit to target account
    if err := to.Deposit(amount); err != nil {
        // Deposit back to this account if transfer fails
        ba.Deposit(amount)
        return err
    }
    
    return nil
}

// Stringer method
func (ba BankAccount) String() string {
    return fmt.Sprintf("Account: %s, Balance: $%.2f, Owner: %s", 
        ba.AccountNumber, ba.Balance, ba.Owner.FullName())
}

func main() {
    // Rectangle methods
    rect := Rectangle{Width: 10.0, Height: 5.0}
    fmt.Printf("Rectangle: %s\n", rect)
    fmt.Printf("Area: %.2f\n", rect.Area())
    fmt.Printf("Perimeter: %.2f\n", rect.Perimeter())
    fmt.Printf("Is square: %t\n", rect.IsSquare())
    
    rect.Resize(20.0, 10.0)
    fmt.Printf("Resized rectangle: %s\n", rect)
    
    rect.Scale(1.5)
    fmt.Printf("Scaled rectangle: %s\n", rect)
    
    // Circle methods
    circle := Circle{Radius: 7.5}
    fmt.Printf("Circle: %s\n", circle)
    fmt.Printf("Area: %.2f\n", circle.Area())
    fmt.Printf("Circumference: %.2f\n", circle.Circumference())
    
    circle.Resize(10.0)
    fmt.Printf("Resized circle: %s\n", circle)
    
    // Shape interface usage
    shapes := []Shape{&rect, &circle}
    for _, shape := range shapes {
        printShapeDetails(shape)
        fmt.Println()
    }
    
    // Person methods
    person := Person{FirstName: "John", LastName: "Doe", Age: 25}
    fmt.Printf("Person: %s\n", person)
    fmt.Printf("Full name: %s\n", person.FullName())
    fmt.Printf("Is adult: %t\n", person.IsAdult())
    
    person.HaveBirthday()
    fmt.Printf("After birthday: %s\n", person)
    
    person.ChangeName("Jane", "Smith")
    fmt.Printf("After name change: %s\n", person)
    
    // BankAccount methods
    owner := Person{FirstName: "Alice", LastName: "Johnson", Age: 30}
    account := BankAccount{
        AccountNumber: "123456789",
        Balance: 1000.0,
        Owner: owner,
    }
    
    fmt.Printf("Account: %s\n", account)
    
    // Deposit
    err := account.Deposit(500.0)
    if err != nil {
        fmt.Printf("Deposit error: %v\n", err)
    } else {
        fmt.Printf("Deposit successful. New balance: $%.2f\n", account.GetBalance())
    }
    
    // Withdraw
    err = account.Withdraw(200.0)
    if err != nil {
        fmt.Printf("Withdrawal error: %v\n", err)
    } else {
        fmt.Printf("Withdrawal successful. New balance: $%.2f\n", account.GetBalance())
    }
    
    // Invalid withdrawal
    err = account.Withdraw(2000.0)
    if err != nil {
        fmt.Printf("Invalid withdrawal error: %v\n", err)
    }
    
    // Transfer
    otherAccount := BankAccount{
        AccountNumber: "987654321",
        Balance: 500.0,
        Owner: Person{FirstName: "Bob", LastName: "Smith", Age: 35},
    }
    
    err = account.Transfer(&otherAccount, 300.0)
    if err != nil {
        fmt.Printf("Transfer error: %v\n", err)
    } else {
        fmt.Printf("Transfer successful.\n")
        fmt.Printf("Source account balance: $%.2f\n", account.GetBalance())
        fmt.Printf("Target account balance: $%.2f\n", otherAccount.GetBalance())
    }
}
```

## Best Practices

### Function Best Practices
```go
package main

import "fmt"
import "errors"

// Good practices examples
func goodPractices() {
    // 1. Use meaningful function names
    calculateTotalPrice := func(items []Item) float64 {
        var total float64
        for _, item := range items {
            total += item.Price * float64(item.Quantity)
        }
        return total
    }
    
    // 2. Keep functions small and focused
    validateEmail := func(email string) error {
        if email == "" {
            return errors.New("email cannot be empty")
        }
        if len(email) < 5 {
            return errors.New("email too short")
        }
        if !contains(email, "@") {
            return errors.New("email must contain @")
        }
        return nil
    }
    
    // 3. Use descriptive parameter names
    processUserData := func(userName string, userAge int, userEmail string) error {
        if userName == "" {
            return errors.New("username cannot be empty")
        }
        if userAge < 18 {
            return errors.New("user must be at least 18 years old")
        }
        if userEmail == "" {
            return errors.New("email cannot be empty")
        }
        
        // Process user data here
        fmt.Printf("Processing user: %s, %d, %s\n", userName, userAge, userEmail)
        return nil
    }
    
    // 4. Return errors for failure cases
    readFile := func(filename string) (string, error) {
        data, err := os.ReadFile(filename)
        if err != nil {
            return "", fmt.Errorf("failed to read file '%s': %w", filename, err)
        }
        return string(data), nil
    }
    
    // 5. Use interfaces for flexibility
    type Processor interface {
        Process(data []byte) ([]byte, error)
    }
    
    jsonProcessor := func(data []byte) ([]byte, error) {
        // Add JSON processing logic
        return data, nil
    }
    
    // 6. Use defer for cleanup
    processFile := func(filename string) error {
        file, err := os.Open(filename, os.O_RDWR|os.O_CREATE, 0644)
        if err != nil {
            return err
        }
        defer file.Close()
        
        // Process file
        _, err = file.WriteString("processed data")
        if err != nil {
            return err
        }
        
        return nil
    }
    
    // 7. Handle errors properly
    safeDivide := func(a, b int) (int, error) {
        if b == 0 {
            return 0, fmt.Errorf("division by zero")
        }
        return a / b, nil
    }
    
    // 8. Use type assertions when appropriate
    assertPositive := func(x int) error {
        if x <= 0 {
            return errors.New("value must be positive")
        }
        return nil
    }
    
    // 9. Document functions
    // CalculateAge calculates the age of a person given their birth year.
    // It returns the age as an integer.
    calculateAge := func(birthYear int) int {
        currentYear := 2023
        return currentYear - birthYear
    }
    
    // 10. Use constants for magic numbers
    const (
        MaxRetries = 3
        TimeoutSec  = 30
        BufferSize = 1024
    )
    
    retryOperation := func() error {
        var lastErr error
        
        for i := 0; i < MaxRetries; i++ {
            err = attemptOperation()
            if err == nil {
                return nil
            }
            lastErr = err
            time.Sleep(time.Duration(i) * time.Second)
        }
        
        return lastErr
    }
    
    // Example usage
    items := []Item{
        {Name: "Laptop", Price: 999.99, Quantity: 2},
        {Name: "Mouse", Price: 29.99, Quantity: 1},
        {Name: 'Keyboard', Price: 49.99, Quantity: 1},
    }
    
    total := calculateTotalPrice(items)
    fmt.Printf("Total price: $%.2f\n", total)
    
    err := validateEmail("john@example.com")
    if err != nil {
        fmt.Printf("Email validation error: %v\n", err)
    }
    
    err = processUserData("John", 25, "john@example.com")
    if err != nil {
        fmt.Printf("User processing error: %v\n", err)
    }
    
    content, err := readFile("config.json")
    if err != nil {
        fmt.Printf("File read error: %v\n", err)
    } else {
        fmt.Printf("File content: %s\n", content)
    }
    
    result, err := safeDivide(10, 2)
    if err != nil {
        fmt.Printf("Division error: %v\n", err)
    } else {
        fmt.Printf("Division result: %d\n", result)
    }
    
    err = assertPositive(5)
    if err != nil {
        fmt.Printf("Assertion error: %v\n", err)
    }
    
    age := calculateAge(1990)
    fmt.Printf("Age: %d\n", age)
    
    err = processFile("test.txt")
    if err != nil {
        fmt.Printf("File processing error: %v\n", err)
    }
    
    err = retryOperation()
    if err != nil {
        fmt.Printf("Retry error: %v\n", err)
    }
}

// Bad practices examples
func badPractices() {
    // 1. Poor function names
    func x(a, b int) int { // Should be more descriptive
        return a + b
    }
    
    // 2. Functions that do too many things
    func processAndSaveAndNotify(data []byte, filename string, recipients []string) error { // Should be broken down
        // Process data
        processedData := jsonProcess(data)
        
        // Save to file
        file, err := os.Create(filename)
        if err != nil {
            return err
        }
        defer file.Close()
        
        _, err = file.Write(processedData)
        if err != nil {
            return err
        }
        
        // Send notifications
        for _, recipient := range recipients {
            err := sendEmail(recipient, "Data processed")
            if err != nil {
                return err
            }
        }
        
        return nil
    }
    
    // 3. Not handling errors
    func unsafeDivide(a, b int) int {
        return a / b // Could panic if b is 0
    }
    
    // 4. Using magic numbers
    func calculate(x int) int {
        return x * 3.14159 // Should use constant
    }
    
    // 5. Not documenting functions
    func complexCalc(a, b, c, d, e, f, g, h, i, j) int {
        // Complex calculation without comments
        return a + b + c + d + e + f + g + h + i + j
    }
    
    // 6. Not using interfaces for similar functions
    func processInts(data []int) []int {
        // Process integers
        return data
    }
    
    func processStrings(data []string) []string {
        // Process strings
        return data
    }
    
    // Should use interface
    type Processor interface {
        Process(data interface{}) interface{}
    }
    
    // 7. Not using defer for cleanup
    func readFileWithoutCleanup(filename string) (string, error) {
        file, err := os.Open(filename, os.O_RDONLY)
        if err != nil {
            return "", err
        }
        
        // File is not closed here - potential resource leak
        return string(file.Name())
    }
    
    // 8. Panic instead of returning errors
    func panicOnError(data []byte) []byte {
        if len(data) == 0 {
            panic("data cannot be empty") // Should return error
        }
        return data
    }
    
    // 9. Not using type assertions
    func processUnknownType(data interface{}) {
        // No type checking
        fmt.Printf("Processing: %v\n", data)
    }
    
    // 10. Not handling edge cases
    func getFirstElement(slice []int) int {
        return slice[0] // Will panic if slice is empty
    }
    
    fmt.Printf("Bad practice examples:\n")
    
    // Demonstrate bad practices
    result := x(10, 20)
    fmt.Printf("x(10, 20) = %d\n", result)
    
    unsafeDivide(10, 0) // This would panic
    
    fmt.Printf("Complex calculation: %d\n", complexCalc(1, 2, 3, 4, 5, 6, 7, 8, 9, 10))
    
    intData := processInts([]int{1, 2, 3})
    stringData := processStrings([]string{"a", "b", "c"})
    fmt.Printf("Int data: %v\n", intData)
    fmt.Printf("String data: %v\n", stringData)
    
    fileContent, err := readFileWithoutCleanup("test.txt")
    if err != nil {
        fmt.Printf("File error: %v\n", err)
    } else {
        fmt.Printf("File content: %s\n", fileContent)
    }
    
    emptyData := []int{}
    first := getFirstElement(emptyData) // This would panic
    fmt.Printf("First element: %d\n", first)
}

// Helper functions
type Item struct {
    Name     string
    Price    float64
    Quantity int
}

func sendEmail(recipient string, message string) error {
    // Simulate email sending
    fmt.Printf("Sending email to %s: %s\n", recipient, message)
    return nil
}

func attemptOperation() error {
    // Simulate operation that might fail
    if time.Now().UnixNano()%2 == 0 {
        return errors.New("random failure")
    }
    return nil
}

func contains(s, substr string) bool {
    return len(s) > 0 && len(substr) > 0 && s[0:len(substr)] == substr
}

func main() {
    goodPractices()
    badPractices()
}
```

## Summary

Go functions provide:

**Function Declaration:**
- Basic function syntax with parameters and return values
- Multiple return values for better error handling
- Named return values for clarity
- Short variable declaration with type inference

**Function Parameters:**
- Value parameters (default behavior)
- Pointer parameters for modification
- Array and slice parameters
- Map and interface parameters
- Variadic parameters for flexibility

**Return Values:**
- Single and multiple return values
- Error handling with return values
- Named return values for clarity
- Interface{} for flexibility

**Advanced Features:**
- Anonymous functions (lambdas)
- Higher-order functions
- Function composition
- Closures and lexical scope
- Currying and partial application

**Object-Oriented Programming:**
- Methods on structs
- Interface implementation
    - Method receivers (value vs pointer)
    - Stringer methods
    - Interface methods

**Recursion:**
- Classic recursive algorithms
    - Factorial, Fibonacci, GCD
    - Tree operations
    - Directory traversal
    - Tower of Hanoi

**Best Practices:**
- Meaningful function names
- Small, focused functions
- Proper error handling
- Interface usage for flexibility
- Defer for resource cleanup
- Type assertions for safety
- Documentation and comments
- Constant usage instead of magic numbers

Go's function system provides excellent support for functional programming patterns while maintaining simplicity and performance. The combination of first-class functions, multiple return values, and interfaces makes Go functions powerful and expressive.
