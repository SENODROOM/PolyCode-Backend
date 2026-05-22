# Go Variables and Data Types

## Variable Declaration

### Variable Declaration in Go
```go
package main

import "fmt"

func main() {
    // Variable declaration with var keyword
    var name string = "John Doe"
    var age int = 30
    var isStudent bool = true
    
    fmt.Println("Name:", name)
    fmt.Println("Age:", age)
    fmt.Println("Is Student:", isStudent)
    
    // Short variable declaration (type inference)
    city := "New York"
    salary := 50000.50
    
    fmt.Println("City:", city)
    fmt.Println("Salary:", salary)
    
    // Multiple variable declaration
    var (
        firstName string = "Jane"
        lastName  string = "Smith"
        height    int     = 165
    )
    
    fmt.Printf("%s %s is %d cm tall\n", firstName, lastName, height)
}
```

### Variable Naming Conventions
```go
package main

import "fmt"

func main() {
    // CamelCase for local variables
    userName := "john_doe"
    userProfile := "active"
    
    // PascalCase for exported variables (public)
    var UserName string = "John Doe"
    var UserProfile string = "Active"
    
    // Constants use UPPER_SNAKE_CASE
    const MAX_USERS = 1000
    const API_VERSION = "v1.0"
    
    fmt.Println("User Name:", userName)
    fmt.Println("User Profile:", userProfile)
    fmt.Println("Max Users:", MAX_USERS)
    fmt.Println("API Version:", API_VERSION)
}
```

## Basic Data Types

### Numeric Types
```go
package main

import "fmt"

func main() {
    // Integer types
    var i8 int8 = 127        // 8-bit signed integer
    var i16 int16 = 32767     // 16-bit signed integer
    var i32 int32 = 2147483647 // 32-bit signed integer
    var i64 int64 = 9223372036854775807 // 64-bit signed integer
    
    // Unsigned integer types
    var ui8 uint8 = 255      // 8-bit unsigned integer
    var ui16 uint16 = 65535   // 16-bit unsigned integer
    var ui32 uint32 = 4294967295 // 32-bit unsigned integer
    var ui64 uint64 = 18446744073709551615 // 64-bit unsigned integer
    
    // Platform-dependent types
    var intVar int = 32        // Same as int32 on 32-bit, int64 on 64-bit
    var uintVar uint = 32      // Same as uint32 on 32-bit, uint64 on 64-bit
    
    // Floating-point types
    var f32 float32 = 3.14159  // 32-bit floating-point
    var f64 float64 = 3.141592653589793 // 64-bit floating-point
    
    // Complex types
    var c64 complex64 = 3 + 4i // 64-bit complex numbers
    var c128 complex128 = 1 + 2i // 128-bit complex numbers
    
    fmt.Printf("i8: %d, i16: %d, i32: %d, i64: %d\n", i8, i16, i32, i64)
    fmt.Printf("ui8: %d, ui16: %d, ui32: %d, ui64: %d\n", ui8, ui16, ui32, ui64)
    fmt.Printf("f32: %f, f64: %f\n", f32, f64)
    fmt.Printf("c64: %v, c128: %v\n", c64, c128)
}
```

### String Type
```go
package main

import "fmt"

func main() {
    // String declaration
    var greeting string = "Hello, World!"
    name := "Go Programming"
    
    // String concatenation
    message := greeting + " Welcome to " + name
    fmt.Println("Message:", message)
    
    // String length
    fmt.Println("Greeting length:", len(greeting))
    
    // String indexing
    fmt.Println("First character:", string(greeting[0]))
    fmt.Println("Last character:", string(greeting[len(greeting)-1]))
    
    // String slicing
    substring := greeting[0:5]
    fmt.Println("Substring:", substring)
    
    // Raw string literals
    rawString := `This is a raw string
with multiple lines
and special characters: \t \n \"`
    
    fmt.Println("Raw string:")
    fmt.Println(rawString)
    
    // String immutability
    // Strings in Go are immutable
    str := "hello"
    // str[0] = 'H' // This would cause a compile error
    
    // To modify a string, you need to create a new one
    modified := "H" + str[1:]
    fmt.Println("Modified string:", modified)
}
```

### Boolean Type
```go
package main

import "fmt"

func main() {
    // Boolean declaration
    var isLoggedIn bool = true
    var hasPermission bool = false
    
    // Boolean operations
    canAccess := isLoggedIn && hasPermission
    canEdit := isLoggedIn || hasPermission
    
    fmt.Println("Is Logged In:", isLoggedIn)
    fmt.Println("Has Permission:", hasPermission)
    fmt.Println("Can Access:", canAccess)
    fmt.Println("Can Edit:", canEdit)
    
    // Boolean comparison
    age := 25
    isAdult := age >= 18
    
    fmt.Println("Age:", age)
    fmt.Println("Is Adult:", isAdult)
    
    // Boolean in conditional statements
    if isAdult {
        fmt.Println("Welcome to the adult section")
    } else {
        fmt.Println("Please come back when you're older")
    }
    
    // Boolean functions
    func isEven(num int) bool {
        return num%2 == 0
    }
    
    fmt.Println("Is 10 even?", isEven(10))
    fmt.Println("Is 7 even?", isEven(7))
}
```

## Constants

### Constant Declaration
```go
package main

import "fmt"

const (
    // Numeric constants
    PI           = 3.141592653589793
    E            = 2.718281828459045
    GoldenRatio  = 1.618033988749895
    
    // String constants
    AppName      = "MyGoApp"
    Version      = "1.0.0"
    Description  = "A sample Go application"
    
    // Boolean constants
    DebugMode    = true
    ProductionMode = false
    
    // Integer constants
    MaxRetries   = 3
    TimeoutSec   = 30
    BufferSize   = 1024
)

func main() {
    fmt.Println("Constants:")
    fmt.Printf("PI: %f\n", PI)
    fmt.Printf("E: %f\n", E)
    fmt.Printf("Golden Ratio: %f\n", GoldenRatio)
    fmt.Printf("App Name: %s\n", AppName)
    fmt.Printf("Version: %s\n", Version)
    fmt.Printf("Description: %s\n", Description)
    fmt.Printf("Debug Mode: %t\n", DebugMode)
    fmt.Printf("Production Mode: %t\n", ProductionMode)
    fmt.Printf("Max Retries: %d\n", MaxRetries)
    fmt.Printf("Timeout: %d seconds\n", TimeoutSec)
    fmt.Printf("Buffer Size: %d bytes\n", BufferSize)
}
```

### iota for Enumerated Constants
```go
package main

import "fmt"

type Weekday int

const (
    Sunday Weekday = iota
    Monday
    Tuesday
    Wednesday
    Thursday
    Friday
    Saturday
)

type Status int

const (
    StatusPending Status = iota
    StatusInProgress
    StatusCompleted
    StatusFailed
    StatusCancelled
)

func main() {
    // Weekday constants
    fmt.Println("Weekdays:")
    fmt.Printf("Sunday: %d\n", Sunday)
    fmt.Printf("Monday: %d\n", Monday)
    fmt.Printf("Tuesday: %d\n", Tuesday)
    fmt.Printf("Wednesday: %d\n", Wednesday)
    fmt.Printf("Thursday: %d\n", Thursday)
    fmt.Printf("Friday: %d\n", Friday)
    fmt.Printf("Saturday: %d\n", Saturday)
    
    // Status constants
    fmt.Println("\nStatuses:")
    fmt.Printf("Pending: %d\n", StatusPending)
    fmt.Printf("In Progress: %d\n", StatusInProgress)
    fmt.Printf("Completed: %d\n", StatusCompleted)
    fmt.Printf("Failed: %d\n", StatusFailed)
    fmt.Printf("Cancelled: %d\n", StatusCancelled)
    
    // Using iota with custom values
    const (
        ReadPermission  = 1 << iota // 1
        WritePermission = 1 << iota // 2
        ExecutePermission = 1 << iota // 4
        AdminPermission = 1 << iota // 8
    )
    
    fmt.Println("\nPermissions:")
    fmt.Printf("Read: %d\n", ReadPermission)
    fmt.Printf("Write: %d\n", WritePermission)
    fmt.Printf("Execute: %d\n", ExecutePermission)
    fmt.Printf("Admin: %d\n", AdminPermission)
}
```

## Type Conversion

### Basic Type Conversions
```go
package main

import "fmt"
import "strconv"

func main() {
    // Integer to float
    var i int = 42
    var f float64 = float64(i)
    fmt.Printf("Integer %d to float64: %f\n", i, f)
    
    // Float to integer (truncation)
    var f2 float64 = 3.99
    var i2 int = int(f2)
    fmt.Printf("Float64 %f to int: %d\n", f2, i2)
    
    // String to integer
    str := "123"
    i3, err := strconv.Atoi(str)
    if err == nil {
        fmt.Printf("String '%s' to int: %d\n", str, i3)
    }
    
    // Integer to string
    i4 := 456
    str2 := strconv.Itoa(i4)
    fmt.Printf("Int %d to string: '%s'\n", i4, str2)
    
    // String to float64
    str3 := "3.14159"
    f3, err := strconv.ParseFloat(str3, 64)
    if err == nil {
        fmt.Printf("String '%s' to float64: %f\n", str3, f3)
    }
    
    // Float64 to string
    f4 := 2.71828
    str4 := strconv.FormatFloat(f4, 'f', 6, 64)
    fmt.Printf("Float64 %f to string: '%s'\n", f4, str4)
    
    // Boolean to string
    b := true
    str5 := strconv.FormatBool(b)
    fmt.Printf("Boolean %t to string: '%s'\n", b, str5)
    
    // String to boolean
    str6 := "true"
    b2, err := strconv.ParseBool(str6)
    if err == nil {
        fmt.Printf("String '%s' to boolean: %t\n", str6, b2)
    }
}
```

### Type Assertions
```go
package main

import "fmt"

func main() {
    var i interface{} = 42
    
    // Type assertion
    if value, ok := i.(int); ok {
        fmt.Printf("Value is int: %d\n", value)
    }
    
    // Type assertion with different types
    var x interface{} = "hello"
    
    if str, ok := x.(string); ok {
        fmt.Printf("Value is string: %s\n", str)
    }
    
    // Type switch
    var y interface{} = 3.14
    
    switch v := y.(type) {
    case int:
        fmt.Println("Value is integer")
    case float64:
        fmt.Println("Value is float64")
    case string:
        fmt.Println("Value is string")
    case bool:
        fmt.Println("Value is boolean")
    default:
        fmt.Printf("Value is of unknown type: %T\n", v)
    }
    
    // Type assertion with panic
    var z interface{} = "test"
    str := z.(string) // This will panic if z is not a string
    fmt.Printf("Extracted string: %s\n", str)
    
    // Safe type assertion
    var w interface{} = 42
    if num, ok := w.(int); ok {
        fmt.Printf("Safely extracted int: %d\n", num)
    } else {
        fmt.Println("w is not an int")
    }
}
```

## Zero Values

### Zero Values in Go
```go
package main

import "fmt"

func main() {
    // Zero values for different types
    var i int
    var f float64
    var b bool
    var s string
    var p *int
    var sl []int
    var m map[string]int
    var ch chan int
    var fn func()
    var err error
    var inter interface{}
    
    fmt.Println("Zero values:")
    fmt.Printf("int: %d\n", i)
    fmt.Printf("float64: %f\n", f)
    fmt.Printf("bool: %t\n", b)
    fmt.Printf("string: '%s'\n", s)
    fmt.Printf("pointer: %v\n", p)
    fmt.Printf("slice: %v\n", sl)
    fmt.Printf("map: %v\n", m)
    fmt.Printf("channel: %v\n", ch)
    fmt.Printf("function: %v\n", fn)
    fmt.Printf("error: %v\n", err)
    fmt.Printf("interface: %v\n", inter)
    
    // Checking if a value is zero
    var x int = 0
    if x == 0 {
        fmt.Println("x is zero")
    }
    
    var y string = ""
    if y == "" {
        fmt.Println("y is zero")
    }
    
    // Using the zero value function
    var z float64
    if isZero(z) {
        fmt.Println("z is zero")
    }
}

// Generic function to check zero value
func isZero(value interface{}) bool {
    switch v := value.(type) {
    case int:
        return v == 0
    case float64:
        return v == 0.0
    case bool:
        return v == false
    case string:
        return v == ""
    case pointer:
        return v == nil
    case []int:
        return v == nil
    case map[string]int:
        return v == nil
    case chan int:
        return v == nil
    case func():
        return v == nil
    case error:
        return v == nil
    case interface{}:
        return v == nil
    default:
        return false
    }
}
```

## Variable Scope

### Variable Scope Examples
```go
package main

import "fmt"

// Package-level variable
var packageVar string = "I am package level"

func main() {
    // Function-level variable
    var functionVar string = "I am function level"
    
    fmt.Println("Package variable:", packageVar)
    fmt.Println("Function variable:", functionVar)
    
    // Block-level variable
    if true {
        blockVar := "I am block level"
        fmt.Println("Block variable:", blockVar)
    }
    
    // blockVar is not accessible here
    // fmt.Println(blockVar) // This would cause a compile error
    
    // Shadowing
    x := 10
    fmt.Println("Outer x:", x)
    
    if true {
        x := 20 // This shadows the outer x
        fmt.Println("Inner x:", x)
    }
    
    fmt.Println("Outer x after block:", x)
    
    // Global variables in other packages
    fmt.Println("Global variable:", globalVar)
    
    // Demonstrating scope with functions
    outer()
    inner()
}

func outer() {
    outerVar := "I am in outer function"
    fmt.Println("Outer function variable:", outerVar)
    
    inner()
    
    // innerVar is not accessible here
    // fmt.Println(innerVar) // This would cause a compile error
}

func inner() {
    innerVar := "I am in inner function"
    fmt.Println("Inner function variable:", innerVar)
}

// Global variable (can be accessed by other packages if it starts with capital letter)
var globalVar string = "I am global level"
```

## Best Practices

### Variable Best Practices
```go
package main

import "fmt"

// Good practices examples
func goodPractices() {
    // 1. Use short variable declaration when type is clear
    name := "John Doe"
    age := 30
    
    // 2. Use var declaration when you need to specify type or zero value
    var config map[string]string
    config = make(map[string]string)
    
    // 3. Use meaningful variable names
    var userAge int
    var isActive bool
    var errorMessage string
    
    // 4. Group related variables
    var (
        firstName string = "John"
        lastName  string = "Doe"
        email     string = "john@example.com"
    )
    
    // 5. Use constants for values that don't change
    const MaxLoginAttempts = 3
    const SessionTimeout = 3600
    
    // 6. Use descriptive names for boolean variables
    var isLoggedIn, hasPermission, canEdit bool
    
    // 7. Initialize variables when possible
    var count int = 0
    var isValid bool = true
    var result string = "success"
    
    fmt.Printf("Name: %s, Age: %d\n", name, age)
    fmt.Printf("Config: %v\n", config)
    fmt.Printf("User: %s %s (%s)\n", firstName, lastName, email)
    fmt.Printf("Count: %d, Valid: %t, Result: %s\n", count, isValid, result)
    fmt.Printf("Login Attempts: %d, Timeout: %d\n", MaxLoginAttempts, SessionTimeout)
    fmt.Printf("Logged In: %t, Has Permission: %t, Can Edit: %t\n", isLoggedIn, hasPermission, canEdit)
}

// Bad practices examples
func badPractices() {
    // 1. Using single-letter variable names (except for loop counters)
    var x int = 10
    var y string = "hello"
    
    // 2. Not initializing variables when appropriate
    var count int
    var isValid bool
    
    // 3. Using magic numbers instead of constants
    timeout := 30 // Should use a constant
    
    // 4. Not using short declaration when type is obvious
    var name string = "John" // Should use :=
    
    // 5. Not grouping related variables
    var firstName string = "John"
    var lastName string = "Doe"
    var email string = "john@example.com"
    
    fmt.Printf("Bad practice examples:\n")
    fmt.Printf("x: %d, y: %s\n", x, y)
    fmt.Printf("Count: %d, Valid: %t\n", count, isValid)
    fmt.Printf("Timeout: %d\n", timeout)
    fmt.Printf("Name: %s\n", name)
    fmt.Printf("User: %s %s (%s)\n", firstName, lastName, email)
}

func main() {
    goodPractices()
    badPractices()
}
```

## Type Safety

### Type Safety Examples
```go
package main

import "fmt"

// Type safety functions
func addIntegers(a, b int) int {
    return a + b
}

func addFloats(a, b float64) float64 {
    return a + b
}

func processNumbers(numbers []interface{}) {
    for i, num := range numbers {
        switch v := num.(type) {
        case int:
            fmt.Printf("Processing integer at index %d: %d\n", i, v)
        case float64:
            fmt.Printf("Processing float64 at index %d: %f\n", i, v)
        case string:
            fmt.Printf("Processing string at index %d: %s\n", i, v)
        default:
            fmt.Printf("Unknown type at index %d: %T\n", i, v)
        }
    }
}

func typeSafetyExamples() {
    // Type-safe operations
    intResult := addIntegers(10, 20)
    floatResult := addFloats(10.5, 20.5)
    
    fmt.Printf("Integer addition: %d\n", intResult)
    fmt.Printf("Float addition: %f\n", floatResult)
    
    // Type-safe collections
    var intSlice []int
    var stringSlice []string
    
    intSlice = append(intSlice, 1, 2, 3)
    stringSlice = append(stringSlice, "hello", "world")
    
    fmt.Printf("Int slice: %v\n", intSlice)
    fmt.Printf("String slice: %v\n", stringSlice)
    
    // Type checking with interface{}
    var numbers []interface{}
    numbers = append(numbers, 42)
    numbers = append(numbers, 3.14)
    numbers = append(numbers, "hello")
    
    processNumbers(numbers)
    
    // Type assertion with error handling
    var value interface{} = 42
    
    if intValue, ok := value.(int); ok {
        fmt.Printf("Value is integer: %d\n", intValue)
    } else {
        fmt.Println("Value is not an integer")
    }
    
    // Type switch for comprehensive type checking
    var data interface{} = "test"
    
    switch v := data.(type) {
    case string:
        fmt.Printf("String value: %s\n", v)
    case int:
        fmt.Printf("Integer value: %d\n", v)
    case float64:
        fmt.Printf("Float64 value: %f\n", v)
    case bool:
        fmt.Printf("Boolean value: %t\n", v)
    default:
        fmt.Printf("Unknown type: %T\n", v)
    }
}

func main() {
    typeSafetyExamples()
}
```

## Summary

Go variables and data types provide:

**Variable Declaration:**
- `var` keyword for explicit type declaration
- `:=` for type inference
- Multiple variable declaration
- Naming conventions (camelCase, PascalCase, UPPER_SNAKE_CASE)

**Basic Data Types:**
- Integer types (int8, int16, int32, int64)
- Unsigned integer types (uint8, uint16, uint32, uint64)
- Floating-point types (float32, float64)
- Boolean type (bool)
- String type (immutable)
- Complex types (complex64, complex128)

**Constants:**
- `const` keyword for compile-time constants
- `iota` for enumerated constants
- Grouped constant declarations
- Type-safe constant usage

**Type Conversion:**
- Explicit type conversions
- Type assertions with error checking
- Type switches for comprehensive checking
- Safe type handling

**Zero Values:**
- Default values for all types
- Zero value checking
- Initialization best practices
- Memory management

**Best Practices:**
- Meaningful variable names
- Proper initialization
- Type safety considerations
- Scope management
- Code readability

Go's static typing and type inference provide a good balance between safety and convenience, making it easier to write correct and maintainable code.
