# Go Testing

## Testing Fundamentals

### Basic Unit Testing
```go
package main

import (
    "testing"
    "fmt"
    "strings"
)

// Simple function to test
func Add(a, b int) int {
    return a + b
}

func Subtract(a, b int) int {
    return a - b
}

func Multiply(a, b int) int {
    return a * b
}

func Divide(a, b float64) (float64, error) {
    if b == 0 {
        return 0, fmt.Errorf("division by zero")
    }
    return a / b, nil
}

// String manipulation function
func Greet(name string) string {
    if name == "" {
        return "Hello, World!"
    }
    return fmt.Sprintf("Hello, %s!", name)
}

func IsPalindrome(s string) bool {
    s = strings.ToLower(strings.ReplaceAll(s, " ", ""))
    for i := 0; i < len(s)/2; i++ {
        if s[i] != s[len(s)-1-i] {
            return false
        }
    }
    return true
}

// Basic unit tests
func TestAdd(t *testing.T) {
    tests := []struct {
        name     string
        a, b     int
        expected int
    }{
        {"positive numbers", 2, 3, 5},
        {"negative numbers", -1, -2, -3},
        {"mixed numbers", -1, 2, 1},
        {"zero", 0, 5, 5},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result := Add(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Add(%d, %d) = %d; want %d", tt.a, tt.b, result, tt.expected)
            }
        })
    }
}

func TestSubtract(t *testing.T) {
    tests := []struct {
        a, b     int
        expected int
    }{
        {5, 3, 2},
        {3, 5, -2},
        {0, 0, 0},
        {-5, -3, -2},
    }
    
    for _, tt := range tests {
        t.Run(fmt.Sprintf("%d-%d", tt.a, tt.b), func(t *testing.T) {
            result := Subtract(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Subtract(%d, %d) = %d; want %d", tt.a, tt.b, result, tt.expected)
            }
        })
    }
}

func TestMultiply(t *testing.T) {
    tests := []struct {
        a, b     int
        expected int
    }{
        {2, 3, 6},
        {-2, 3, -6},
        {0, 5, 0},
        {-2, -3, 6},
    }
    
    for _, tt := range tests {
        t.Run(fmt.Sprintf("%d*%d", tt.a, tt.b), func(t *testing.T) {
            result := Multiply(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Multiply(%d, %d) = %d; want %d", tt.a, tt.b, result, tt.expected)
            }
        })
    }
}

func TestDivide(t *testing.T) {
    tests := []struct {
        name        string
        a, b        float64
        expected    float64
        expectError bool
    }{
        {"normal division", 10.0, 2.0, 5.0, false},
        {"negative division", -10.0, 2.0, -5.0, false},
        {"decimal division", 7.0, 2.0, 3.5, false},
        {"division by zero", 10.0, 0.0, 0.0, true},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result, err := Divide(tt.a, tt.b)
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Divide(%f, %f) expected error but got none", tt.a, tt.b)
                }
            } else {
                if err != nil {
                    t.Errorf("Divide(%f, %f) unexpected error: %v", tt.a, tt.b, err)
                }
                if result != tt.expected {
                    t.Errorf("Divide(%f, %f) = %f; want %f", tt.a, tt.b, result, tt.expected)
                }
            }
        })
    }
}

func TestGreet(t *testing.T) {
    tests := []struct {
        name     string
        input    string
        expected string
    }{
        {"empty name", "", "Hello, World!"},
        {"simple name", "John", "Hello, John!"},
        {"name with spaces", "John Doe", "Hello, John Doe!"},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result := Greet(tt.input)
            if result != tt.expected {
                t.Errorf("Greet(%q) = %q; want %q", tt.input, result, tt.expected)
            }
        })
    }
}

func TestIsPalindrome(t *testing.T) {
    tests := []struct {
        input    string
        expected bool
    }{
        {"racecar", true},
        {"hello", false},
        {"A man a plan a canal Panama", true},
        {"", true},
        {"a", true},
        {"ab", false},
    }
    
    for _, tt := range tests {
        t.Run(tt.input, func(t *testing.T) {
            result := IsPalindrome(tt.input)
            if result != tt.expected {
                t.Errorf("IsPalindrome(%q) = %t; want %t", tt.input, result, tt.expected)
            }
        })
    }
}

// Example tests
func ExampleAdd() {
    result := Add(2, 3)
    fmt.Println(result)
    // Output: 5
}

func ExampleGreet() {
    fmt.Println(Greet("World"))
    // Output: Hello, World!
}

// Benchmark tests
func BenchmarkAdd(b *testing.B) {
    for i := 0; i < b.N; i++ {
        Add(i, i+1)
    }
}

func BenchmarkMultiply(b *testing.B) {
    for i := 0; i < b.N; i++ {
        Multiply(i, i+1)
    }
}

func BenchmarkIsPalindrome(b *testing.B) {
    s := "A man a plan a canal Panama"
    for i := 0; i < b.N; i++ {
        IsPalindrome(s)
    }
}
```

### Table-Driven Tests
```go
package main

import (
    "testing"
    "math"
)

// Calculator struct for more complex testing
type Calculator struct {
    memory float64
}

func NewCalculator() *Calculator {
    return &Calculator{memory: 0}
}

func (c *Calculator) Add(a, b float64) float64 {
    return a + b
}

func (c *Calculator) Subtract(a, b float64) float64 {
    return a - b
}

func (c *Calculator) Multiply(a, b float64) float64 {
    return a * b
}

func (c *Calculator) Divide(a, b float64) (float64, error) {
    if b == 0 {
        return 0, fmt.Errorf("division by zero")
    }
    return a / b, nil
}

func (c *Calculator) Power(base, exponent float64) float64 {
    return math.Pow(base, exponent)
}

func (c *Calculator) Sqrt(x float64) (float64, error) {
    if x < 0 {
        return 0, fmt.Errorf("square root of negative number")
    }
    return math.Sqrt(x), nil
}

func (c *Calculator) Store(value float64) {
    c.memory = value
}

func (c *Calculator) Recall() float64 {
    return c.memory
}

func (c *Calculator) Clear() {
    c.memory = 0
}

// Table-driven tests for calculator
func TestCalculatorOperations(t *testing.T) {
    calc := NewCalculator()
    
    tests := []struct {
        name        string
        operation   string
        a, b        float64
        expected    float64
        expectError bool
        errorMsg    string
    }{
        // Addition tests
        {"add positive", "add", 2.0, 3.0, 5.0, false, ""},
        {"add negative", "add", -2.0, -3.0, -5.0, false, ""},
        {"add mixed", "add", -2.0, 3.0, 1.0, false, ""},
        {"add zero", "add", 0.0, 5.0, 5.0, false, ""},
        
        // Subtraction tests
        {"subtract positive", "subtract", 5.0, 3.0, 2.0, false, ""},
        {"subtract negative", "subtract", -5.0, -3.0, -2.0, false, ""},
        {"subtract mixed", "subtract", 5.0, -3.0, 8.0, false, ""},
        {"subtract zero", "subtract", 5.0, 0.0, 5.0, false, ""},
        
        // Multiplication tests
        {"multiply positive", "multiply", 2.0, 3.0, 6.0, false, ""},
        {"multiply negative", "multiply", -2.0, 3.0, -6.0, false, ""},
        {"multiply both negative", "multiply", -2.0, -3.0, 6.0, false, ""},
        {"multiply zero", "multiply", 0.0, 5.0, 0.0, false, ""},
        
        // Division tests
        {"divide positive", "divide", 10.0, 2.0, 5.0, false, ""},
        {"divide negative", "divide", -10.0, 2.0, -5.0, false, ""},
        {"divide decimal", "divide", 7.0, 2.0, 3.5, false, ""},
        {"divide by zero", "divide", 10.0, 0.0, 0.0, true, "division by zero"},
        
        // Power tests
        {"power positive", "power", 2.0, 3.0, 8.0, false, ""},
        {"power zero", "power", 5.0, 0.0, 1.0, false, ""},
        {"power negative", "power", 2.0, -1.0, 0.5, false, ""},
        
        // Square root tests
        {"sqrt positive", "sqrt", 9.0, 0.0, 3.0, false, ""},
        {"sqrt zero", "sqrt", 0.0, 0.0, 0.0, false, ""},
        {"sqrt negative", "sqrt", -9.0, 0.0, 0.0, true, "square root of negative number"},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            var result float64
            var err error
            
            switch tt.operation {
            case "add":
                result = calc.Add(tt.a, tt.b)
            case "subtract":
                result = calc.Subtract(tt.a, tt.b)
            case "multiply":
                result = calc.Multiply(tt.a, tt.b)
            case "divide":
                result, err = calc.Divide(tt.a, tt.b)
            case "power":
                result = calc.Power(tt.a, tt.b)
            case "sqrt":
                result, err = calc.Sqrt(tt.a)
            default:
                t.Fatalf("Unknown operation: %s", tt.operation)
            }
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Expected error but got none")
                } else if tt.errorMsg != "" && err.Error() != tt.errorMsg {
                    t.Errorf("Expected error message %q, got %q", tt.errorMsg, err.Error())
                }
            } else {
                if err != nil {
                    t.Errorf("Unexpected error: %v", err)
                }
                if math.Abs(result-tt.expected) > 1e-10 {
                    t.Errorf("Expected %f, got %f", tt.expected, result)
                }
            }
        })
    }
}

func TestCalculatorMemory(t *testing.T) {
    calc := NewCalculator()
    
    tests := []struct {
        name     string
        action   string
        value    float64
        expected float64
    }{
        {"initial recall", "recall", 0.0, 0.0},
        {"store positive", "store", 42.0, 42.0},
        {"recall after store", "recall", 0.0, 42.0},
        {"store negative", "store", -10.0, -10.0},
        {"recall after negative store", "recall", 0.0, -10.0},
        {"clear", "clear", 0.0, 0.0},
        {"recall after clear", "recall", 0.0, 0.0},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            var result float64
            
            switch tt.action {
            case "store":
                calc.Store(tt.value)
                result = calc.Recall()
            case "recall":
                result = calc.Recall()
            case "clear":
                calc.Clear()
                result = calc.Recall()
            }
            
            if result != tt.expected {
                t.Errorf("Expected %f, got %f", tt.expected, result)
            }
        })
    }
}

// Test helper functions
func TestHelperFunctions(t *testing.T) {
    // Helper function to compare floating point numbers
    assertFloatEqual := func(t *testing.T, got, want float64) {
        t.Helper()
        if math.Abs(got-want) > 1e-10 {
            t.Errorf("Expected %f, got %f", want, got)
        }
    }
    
    // Helper function to check for errors
    assertError := func(t *testing.T, err error, wantErr bool) {
        t.Helper()
        if (err != nil) != wantErr {
            t.Errorf("Error: got %v, want error %v", err, wantErr)
        }
    }
    
    calc := NewCalculator()
    
    t.Run("helper functions", func(t *testing.T) {
        result := calc.Add(2.5, 3.5)
        assertFloatEqual(t, result, 6.0)
        
        _, err := calc.Divide(10.0, 0.0)
        assertError(t, err, true)
    })
}

// Test setup and teardown
func TestCalculatorWithSetup(t *testing.T) {
    // Setup function
    setup := func() *Calculator {
        calc := NewCalculator()
        calc.Store(100.0)
        return calc
    }
    
    // Teardown function
    teardown := func(calc *Calculator) {
        calc.Clear()
    }
    
    tests := []struct {
        name     string
        testFunc func(*Calculator, *testing.T)
    }{
        {"memory operations", func(calc *Calculator, t *testing.T) {
            if calc.Recall() != 100.0 {
                t.Errorf("Expected memory to be 100.0, got %f", calc.Recall())
            }
            
            calc.Store(200.0)
            if calc.Recall() != 200.0 {
                t.Errorf("Expected memory to be 200.0, got %f", calc.Recall())
            }
        }},
        {"calculation with memory", func(calc *Calculator, t *testing.T) {
            memory := calc.Recall()
            result := calc.Add(memory, 50.0)
            if result != 150.0 {
                t.Errorf("Expected 150.0, got %f", result)
            }
        }},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            calc := setup()
            defer teardown(calc)
            tt.testFunc(calc, t)
        })
    }
}
```

## Testing with Mocks and Fakes

### Mock Objects
```go
package main

import (
    "testing"
    "time"
    "fmt"
)

// Interface for dependency injection
type Database interface {
    GetUser(id int) (User, error)
    SaveUser(user User) error
    DeleteUser(id int) error
}

type User struct {
    ID    int
    Name  string
    Email string
    Age   int
}

// Mock database implementation
type MockDatabase struct {
    users    map[int]User
    getUserCalled bool
    saveUserCalled bool
    deleteUserCalled bool
    lastUserID int
    lastSavedUser User
    lastDeletedID int
    errors map[string]error
}

func NewMockDatabase() *MockDatabase {
    return &MockDatabase{
        users: make(map[int]User),
        errors: make(map[string]error),
    }
}

func (m *MockDatabase) GetUser(id int) (User, error) {
    m.getUserCalled = true
    m.lastUserID = id
    
    if err, exists := m.errors["GetUser"]; exists {
        return User{}, err
    }
    
    user, exists := m.users[id]
    if !exists {
        return User{}, fmt.Errorf("user not found")
    }
    
    return user, nil
}

func (m *MockDatabase) SaveUser(user User) error {
    m.saveUserCalled = true
    m.lastSavedUser = user
    
    if err, exists := m.errors["SaveUser"]; exists {
        return err
    }
    
    m.users[user.ID] = user
    return nil
}

func (m *MockDatabase) DeleteUser(id int) error {
    m.deleteUserCalled = true
    m.lastDeletedID = id
    
    if err, exists := m.errors["DeleteUser"]; exists {
        return err
    }
    
    delete(m.users, id)
    return nil
}

// Mock helper methods
func (m *MockDatabase) SetError(method string, err error) {
    m.errors[method] = err
}

func (m *MockDatabase) ClearErrors() {
    m.errors = make(map[string]error)
}

func (m *MockDatabase) AddUser(user User) {
    m.users[user.ID] = user
}

func (m *MockDatabase) WasGetUserCalled() bool {
    return m.getUserCalled
}

func (m *MockDatabase) WasSaveUserCalled() bool {
    return m.saveUserCalled
}

func (m *MockDatabase) WasDeleteUserCalled() bool {
    return m.deleteUserCalled
}

func (m *MockDatabase) GetLastUserID() int {
    return m.lastUserID
}

func (m *MockDatabase) GetLastSavedUser() User {
    return m.lastSavedUser
}

func (m *MockDatabase) GetLastDeletedID() int {
    return m.lastDeletedID
}

// Service that uses the database
type UserService struct {
    db Database
}

func NewUserService(db Database) *UserService {
    return &UserService{db: db}
}

func (us *UserService) GetUser(id int) (User, error) {
    if id <= 0 {
        return User{}, fmt.Errorf("invalid user ID")
    }
    
    return us.db.GetUser(id)
}

func (us *UserService) CreateUser(name, email string, age int) (User, error) {
    if name == "" {
        return User{}, fmt.Errorf("name cannot be empty")
    }
    
    if email == "" {
        return User{}, fmt.Errorf("email cannot be empty")
    }
    
    if age < 0 || age > 120 {
        return User{}, fmt.Errorf("invalid age")
    }
    
    // Generate ID (in real app, this would be more sophisticated)
    id := time.Now().Nanosecond()
    
    user := User{
        ID:    id,
        Name:  name,
        Email: email,
        Age:   age,
    }
    
    err := us.db.SaveUser(user)
    if err != nil {
        return User{}, fmt.Errorf("failed to save user: %w", err)
    }
    
    return user, nil
}

func (us *UserService) UpdateUser(id int, name, email string, age int) (User, error) {
    user, err := us.db.GetUser(id)
    if err != nil {
        return User{}, fmt.Errorf("failed to get user: %w", err)
    }
    
    if name != "" {
        user.Name = name
    }
    
    if email != "" {
        user.Email = email
    }
    
    if age >= 0 {
        user.Age = age
    }
    
    err = us.db.SaveUser(user)
    if err != nil {
        return User{}, fmt.Errorf("failed to update user: %w", err)
    }
    
    return user, nil
}

func (us *UserService) DeleteUser(id int) error {
    if id <= 0 {
        return fmt.Errorf("invalid user ID")
    }
    
    return us.db.DeleteUser(id)
}

// Tests with mocks
func TestUserService_GetUser(t *testing.T) {
    tests := []struct {
        name          string
        userID        int
        setupMock     func(*MockDatabase)
        expectedUser  User
        expectError   bool
        expectedError string
    }{
        {
            name:   "valid user",
            userID: 1,
            setupMock: func(m *MockDatabase) {
                m.AddUser(User{ID: 1, Name: "John", Email: "john@example.com", Age: 30})
            },
            expectedUser: User{ID: 1, Name: "John", Email: "john@example.com", Age: 30},
            expectError:   false,
        },
        {
            name:          "user not found",
            userID:        999,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "failed to get user: user not found",
        },
        {
            name:          "invalid user ID",
            userID:        -1,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "invalid user ID",
        },
        {
            name:   "database error",
            userID: 1,
            setupMock: func(m *MockDatabase) {
                m.SetError("GetUser", fmt.Errorf("database connection failed"))
            },
            expectError:   true,
            expectedError: "failed to get user: database connection failed",
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            
            user, err := service.GetUser(tt.userID)
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Expected error but got none")
                } else if tt.expectedError != "" && err.Error() != tt.expectedError {
                    t.Errorf("Expected error %q, got %q", tt.expectedError, err.Error())
                }
            } else {
                if err != nil {
                    t.Errorf("Unexpected error: %v", err)
                }
                if user != tt.expectedUser {
                    t.Errorf("Expected user %+v, got %+v", tt.expectedUser, user)
                }
            }
            
            // Verify mock was called correctly
            if tt.userID > 0 && tt.expectedError != "invalid user ID" {
                if !mockDB.WasGetUserCalled() {
                    t.Errorf("Expected GetUser to be called")
                }
                if mockDB.GetLastUserID() != tt.userID {
                    t.Errorf("Expected GetUser called with ID %d, got %d", tt.userID, mockDB.GetLastUserID())
                }
            }
        })
    }
}

func TestUserService_CreateUser(t *testing.T) {
    tests := []struct {
        name          string
        nameInput     string
        emailInput    string
        ageInput      int
        setupMock     func(*MockDatabase)
        expectError   bool
        expectedError string
        verifyMock    func(*MockDatabase)
    }{
        {
            name:       "valid user creation",
            nameInput:  "John Doe",
            emailInput: "john@example.com",
            ageInput:   30,
            setupMock:  func(m *MockDatabase) {},
            expectError: false,
            verifyMock: func(m *MockDatabase) {
                if !m.WasSaveUserCalled() {
                    t.Errorf("Expected SaveUser to be called")
                }
                savedUser := m.GetLastSavedUser()
                if savedUser.Name != "John Doe" || savedUser.Email != "john@example.com" || savedUser.Age != 30 {
                    t.Errorf("Saved user mismatch: %+v", savedUser)
                }
            },
        },
        {
            name:          "empty name",
            nameInput:     "",
            emailInput:    "john@example.com",
            ageInput:      30,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "name cannot be empty",
        },
        {
            name:          "empty email",
            nameInput:     "John Doe",
            emailInput:    "",
            ageInput:      30,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "email cannot be empty",
        },
        {
            name:          "invalid age",
            nameInput:     "John Doe",
            emailInput:    "john@example.com",
            ageInput:      -5,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "invalid age",
        },
        {
            name:       "database save error",
            nameInput:  "John Doe",
            emailInput: "john@example.com",
            ageInput:   30,
            setupMock: func(m *MockDatabase) {
                m.SetError("SaveUser", fmt.Errorf("database write failed"))
            },
            expectError:   true,
            expectedError: "failed to save user: database write failed",
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            
            user, err := service.CreateUser(tt.nameInput, tt.emailInput, tt.ageInput)
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Expected error but got none")
                } else if tt.expectedError != "" && err.Error() != tt.expectedError {
                    t.Errorf("Expected error %q, got %q", tt.expectedError, err.Error())
                }
            } else {
                if err != nil {
                    t.Errorf("Unexpected error: %v", err)
                }
                if user.Name != tt.nameInput || user.Email != tt.emailInput || user.Age != tt.ageInput {
                    t.Errorf("Created user mismatch: %+v", user)
                }
            }
            
            if tt.verifyMock != nil {
                tt.verifyMock(mockDB)
            }
        })
    }
}

func TestUserService_UpdateUser(t *testing.T) {
    tests := []struct {
        name          string
        userID        int
        nameInput     string
        emailInput    string
        ageInput      int
        setupMock     func(*MockDatabase)
        expectError   bool
        expectedError string
    }{
        {
            name:       "successful update",
            userID:     1,
            nameInput:  "John Updated",
            emailInput: "john.updated@example.com",
            ageInput:   35,
            setupMock: func(m *MockDatabase) {
                m.AddUser(User{ID: 1, Name: "John", Email: "john@example.com", Age: 30})
            },
            expectError: false,
        },
        {
            name:       "partial update",
            userID:     1,
            nameInput:  "John Updated",
            emailInput: "",
            ageInput:   -1, // negative means don't update
            setupMock: func(m *MockDatabase) {
                m.AddUser(User{ID: 1, Name: "John", Email: "john@example.com", Age: 30})
            },
            expectError: false,
        },
        {
            name:          "user not found",
            userID:        999,
            nameInput:     "John Updated",
            emailInput:    "john.updated@example.com",
            ageInput:      35,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "failed to get user: user not found",
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            
            user, err := service.UpdateUser(tt.userID, tt.nameInput, tt.emailInput, tt.ageInput)
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Expected error but got none")
                } else if tt.expectedError != "" && err.Error() != tt.expectedError {
                    t.Errorf("Expected error %q, got %q", tt.expectedError, err.Error())
                }
            } else {
                if err != nil {
                    t.Errorf("Unexpected error: %v", err)
                }
                
                // Verify updated fields
                if tt.nameInput != "" && user.Name != tt.nameInput {
                    t.Errorf("Expected name %q, got %q", tt.nameInput, user.Name)
                }
                if tt.emailInput != "" && user.Email != tt.emailInput {
                    t.Errorf("Expected email %q, got %q", tt.emailInput, user.Email)
                }
                if tt.ageInput >= 0 && user.Age != tt.ageInput {
                    t.Errorf("Expected age %d, got %d", tt.ageInput, user.Age)
                }
                
                // Verify SaveUser was called
                if !mockDB.WasSaveUserCalled() {
                    t.Errorf("Expected SaveUser to be called")
                }
            }
        })
    }
}

func TestUserService_DeleteUser(t *testing.T) {
    tests := []struct {
        name          string
        userID        int
        setupMock     func(*MockDatabase)
        expectError   bool
        expectedError string
        verifyMock    func(*MockDatabase)
    }{
        {
            name:   "successful deletion",
            userID: 1,
            setupMock: func(m *MockDatabase) {
                m.AddUser(User{ID: 1, Name: "John", Email: "john@example.com", Age: 30})
            },
            expectError: false,
            verifyMock: func(m *MockDatabase) {
                if !mockDB.WasDeleteUserCalled() {
                    t.Errorf("Expected DeleteUser to be called")
                }
                if mockDB.GetLastDeletedID() != 1 {
                    t.Errorf("Expected DeleteUser called with ID 1, got %d", mockDB.GetLastDeletedID())
                }
            },
        },
        {
            name:          "invalid user ID",
            userID:        -1,
            setupMock:     func(m *MockDatabase) {},
            expectError:   true,
            expectedError: "invalid user ID",
        },
        {
            name:   "database error",
            userID: 1,
            setupMock: func(m *MockDatabase) {
                m.SetError("DeleteUser", fmt.Errorf("database connection failed"))
            },
            expectError:   true,
            expectedError: "database connection failed",
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            
            err := service.DeleteUser(tt.userID)
            
            if tt.expectError {
                if err == nil {
                    t.Errorf("Expected error but got none")
                } else if tt.expectedError != "" && err.Error() != tt.expectedError {
                    t.Errorf("Expected error %q, got %q", tt.expectedError, err.Error())
                }
            } else {
                if err != nil {
                    t.Errorf("Unexpected error: %v", err)
                }
            }
            
            if tt.verifyMock != nil {
                tt.verifyMock(mockDB)
            }
        })
    }
}
```

## Integration Testing

### HTTP Service Testing
```go
package main

import (
    "testing"
    "net/http"
    "net/http/httptest"
    "encoding/json"
    "bytes"
    "strings"
)

// HTTP service for testing
type UserHandler struct {
    service *UserService
}

func NewUserHandler(service *UserService) *UserHandler {
    return &UserHandler{service: service}
}

func (uh *UserHandler) GetUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.GetUser(id)
    if err != nil {
        if strings.Contains(err.Error(), "not found") {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusInternalServerError)
        }
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) CreateUser(w http.ResponseWriter, r *http.Request) {
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.CreateUser(req.Name, req.Email, req.Age)
    if err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusCreated)
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) UpdateUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    var req struct {
        Name  string `json:"name"`
        Email string `json:"email"`
        Age   int    `json:"age"`
    }
    
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Invalid request body", http.StatusBadRequest)
        return
    }
    
    user, err := uh.service.UpdateUser(id, req.Name, req.Email, req.Age)
    if err != nil {
        if strings.Contains(err.Error(), "not found") {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusBadRequest)
        }
        return
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(user)
}

func (uh *UserHandler) DeleteUser(w http.ResponseWriter, r *http.Request) {
    idStr := strings.TrimPrefix(r.URL.Path, "/users/")
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "Invalid user ID", http.StatusBadRequest)
        return
    }
    
    err = uh.service.DeleteUser(id)
    if err != nil {
        if strings.Contains(err.Error(), "not found") {
            http.Error(w, "User not found", http.StatusNotFound)
        } else {
            http.Error(w, err.Error(), http.StatusBadRequest)
        }
        return
    }
    
    w.WriteHeader(http.StatusNoContent)
}

// HTTP tests
func TestUserHandler_GetUser(t *testing.T) {
    tests := []struct {
        name           string
        userID         string
        setupMock      func(*MockDatabase)
        expectedStatus int
        expectedUser   *User
        expectError    bool
    }{
        {
            name:   "valid user",
            userID: "1",
            setupMock: func(m *MockDatabase) {
                m.AddUser(User{ID: 1, Name: "John", Email: "john@example.com", Age: 30})
            },
            expectedStatus: http.StatusOK,
            expectedUser:   &User{ID: 1, Name: "John", Email: "john@example.com", Age: 30},
        },
        {
            name:           "user not found",
            userID:         "999",
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusNotFound,
            expectError:    true,
        },
        {
            name:           "invalid user ID",
            userID:         "invalid",
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusBadRequest,
            expectError:    true,
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            handler := NewUserHandler(service)
            
            req := httptest.NewRequest("GET", "/users/"+tt.userID, nil)
            w := httptest.NewRecorder()
            
            handler.GetUser(w, req)
            
            resp := w.Result()
            if resp.StatusCode != tt.expectedStatus {
                t.Errorf("Expected status %d, got %d", tt.expectedStatus, resp.StatusCode)
            }
            
            if tt.expectedUser != nil {
                var user User
                if err := json.NewDecoder(resp.Body).Decode(&user); err != nil {
                    t.Errorf("Failed to decode response: %v", err)
                } else if user != *tt.expectedUser {
                    t.Errorf("Expected user %+v, got %+v", *tt.expectedUser, user)
                }
            }
        })
    }
}

func TestUserHandler_CreateUser(t *testing.T) {
    tests := []struct {
        name           string
        requestBody    string
        setupMock      func(*MockDatabase)
        expectedStatus int
        expectError    bool
    }{
        {
            name:        "valid user creation",
            requestBody: `{"name": "John Doe", "email": "john@example.com", "age": 30}`,
            setupMock:   func(m *MockDatabase) {},
            expectedStatus: http.StatusCreated,
        },
        {
            name:           "invalid JSON",
            requestBody:    `{"name": "John Doe", "email": "john@example.com", "age":}`,
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusBadRequest,
            expectError:    true,
        },
        {
            name:           "empty name",
            requestBody:    `{"name": "", "email": "john@example.com", "age": 30}`,
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusBadRequest,
            expectError:    true,
        },
        {
            name:           "empty email",
            requestBody:    `{"name": "John Doe", "email": "", "age": 30}`,
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusBadRequest,
            expectError:    true,
        },
        {
            name:           "invalid age",
            requestBody:    `{"name": "John Doe", "email": "john@example.com", "age": -5}`,
            setupMock:      func(m *MockDatabase) {},
            expectedStatus: http.StatusBadRequest,
            expectError:    true,
        },
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            mockDB := NewMockDatabase()
            tt.setupMock(mockDB)
            
            service := NewUserService(mockDB)
            handler := NewUserHandler(service)
            
            req := httptest.NewRequest("POST", "/users", bytes.NewBufferString(tt.requestBody))
            req.Header.Set("Content-Type", "application/json")
            w := httptest.NewRecorder()
            
            handler.CreateUser(w, req)
            
            resp := w.Result()
            if resp.StatusCode != tt.expectedStatus {
                t.Errorf("Expected status %d, got %d", tt.expectedStatus, resp.StatusCode)
            }
            
            if tt.expectedStatus == http.StatusCreated {
                var user User
                if err := json.NewDecoder(resp.Body).Decode(&user); err != nil {
                    t.Errorf("Failed to decode response: %v", err)
                } else {
                    if user.Name != "John Doe" || user.Email != "john@example.com" || user.Age != 30 {
                        t.Errorf("Created user mismatch: %+v", user)
                    }
                }
            }
        })
    }
}

// Integration test with HTTP server
func TestUserHandler_Integration(t *testing.T) {
    mockDB := NewMockDatabase()
    service := NewUserService(mockDB)
    handler := NewUserHandler(service)
    
    // Create test server
    server := httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        switch {
        case r.Method == "POST" && r.URL.Path == "/users":
            handler.CreateUser(w, r)
        case r.Method == "GET" && strings.HasPrefix(r.URL.Path, "/users/"):
            handler.GetUser(w, r)
        case r.Method == "PUT" && strings.HasPrefix(r.URL.Path, "/users/"):
            handler.UpdateUser(w, r)
        case r.Method == "DELETE" && strings.HasPrefix(r.URL.Path, "/users/"):
            handler.DeleteUser(w, r)
        default:
            http.NotFound(w, r)
        }
    }))
    defer server.Close()
    
    client := server.Client()
    
    // Test user creation
    createReq := `{"name": "Integration User", "email": "integration@example.com", "age": 25}`
    resp, err := client.Post(server.URL+"/users", "application/json", bytes.NewBufferString(createReq))
    if err != nil {
        t.Fatalf("Failed to create user: %v", err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusCreated {
        t.Errorf("Expected status %d, got %d", http.StatusCreated, resp.StatusCode)
    }
    
    var createdUser User
    if err := json.NewDecoder(resp.Body).Decode(&createdUser); err != nil {
        t.Errorf("Failed to decode created user: %v", err)
    }
    
    // Test user retrieval
    resp, err = client.Get(server.URL + "/users/" + fmt.Sprintf("%d", createdUser.ID))
    if err != nil {
        t.Fatalf("Failed to get user: %v", err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusOK {
        t.Errorf("Expected status %d, got %d", http.StatusOK, resp.StatusCode)
    }
    
    var retrievedUser User
    if err := json.NewDecoder(resp.Body).Decode(&retrievedUser); err != nil {
        t.Errorf("Failed to decode retrieved user: %v", err)
    }
    
    if retrievedUser != createdUser {
        t.Errorf("Retrieved user mismatch: expected %+v, got %+v", createdUser, retrievedUser)
    }
    
    // Test user update
    updateReq := `{"name": "Updated User", "email": "updated@example.com", "age": 30}`
    req, _ := http.NewRequest("PUT", server.URL+"/users/"+fmt.Sprintf("%d", createdUser.ID), bytes.NewBufferString(updateReq))
    req.Header.Set("Content-Type", "application/json")
    
    resp, err = client.Do(req)
    if err != nil {
        t.Fatalf("Failed to update user: %v", err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusOK {
        t.Errorf("Expected status %d, got %d", http.StatusOK, resp.StatusCode)
    }
    
    var updatedUser User
    if err := json.NewDecoder(resp.Body).Decode(&updatedUser); err != nil {
        t.Errorf("Failed to decode updated user: %v", err)
    }
    
    if updatedUser.Name != "Updated User" || updatedUser.Email != "updated@example.com" || updatedUser.Age != 30 {
        t.Errorf("Updated user mismatch: %+v", updatedUser)
    }
    
    // Test user deletion
    req, _ = http.NewRequest("DELETE", server.URL+"/users/"+fmt.Sprintf("%d", createdUser.ID), nil)
    resp, err = client.Do(req)
    if err != nil {
        t.Fatalf("Failed to delete user: %v", err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusNoContent {
        t.Errorf("Expected status %d, got %d", http.StatusNoContent, resp.StatusCode)
    }
    
    // Verify user is deleted
    resp, err = client.Get(server.URL + "/users/" + fmt.Sprintf("%d", createdUser.ID))
    if err != nil {
        t.Fatalf("Failed to get deleted user: %v", err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusNotFound {
        t.Errorf("Expected status %d, got %d", http.StatusNotFound, resp.StatusCode)
    }
}
```

## Performance Testing

### Benchmark Testing
```go
package main

import (
    "testing"
    "sort"
    "strings"
    "sync"
    "time"
)

// Functions to benchmark
func BubbleSort(arr []int) []int {
    n := len(arr)
    result := make([]int, n)
    copy(result, arr)
    
    for i := 0; i < n-1; i++ {
        for j := 0; j < n-i-1; j++ {
            if result[j] > result[j+1] {
                result[j], result[j+1] = result[j+1], result[j]
            }
        }
    }
    return result
}

func QuickSort(arr []int) []int {
    if len(arr) <= 1 {
        return arr
    }
    
    pivot := arr[0]
    var left, right []int
    
    for _, v := range arr[1:] {
        if v <= pivot {
            left = append(left, v)
        } else {
            right = append(right, v)
        }
    }
    
    result := append(QuickSort(left), pivot)
    result = append(result, QuickSort(right)...)
    return result
}

func GoSort(arr []int) []int {
    result := make([]int, len(arr))
    copy(result, arr)
    sort.Ints(result)
    return result
}

func StringConcatenate(strs []string) string {
    var result string
    for _, s := range strs {
        result += s
    }
    return result
}

func StringBuilderConcatenate(strs []string) string {
    var builder strings.Builder
    for _, s := range strs {
        builder.WriteString(s)
    }
    return builder.String()
}

func ConcurrentSum(numbers []int) int {
    const numWorkers = 4
    var wg sync.WaitGroup
    results := make(chan int, numWorkers)
    
    chunkSize := len(numbers) / numWorkers
    for i := 0; i < numWorkers; i++ {
        wg.Add(1)
        go func(start int) {
            defer wg.Done()
            
            end := start + chunkSize
            if end > len(numbers) {
                end = len(numbers)
            }
            
            sum := 0
            for _, num := range numbers[start:end] {
                sum += num
            }
            results <- sum
        }(i * chunkSize)
    }
    
    wg.Wait()
    close(results)
    
    total := 0
    for result := range results {
        total += result
    }
    
    return total
}

func SequentialSum(numbers []int) int {
    sum := 0
    for _, num := range numbers {
        sum += num
    }
    return sum
}

// Benchmark tests
func BenchmarkBubbleSort(b *testing.B) {
    sizes := []int{100, 1000, 5000}
    
    for _, size := range sizes {
        b.Run(fmt.Sprintf("size_%d", size), func(b *testing.B) {
            arr := generateRandomArray(size)
            
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                BubbleSort(arr)
            }
        })
    }
}

func BenchmarkQuickSort(b *testing.B) {
    sizes := []int{100, 1000, 5000}
    
    for _, size := range sizes {
        b.Run(fmt.Sprintf("size_%d", size), func(b *testing.B) {
            arr := generateRandomArray(size)
            
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                QuickSort(arr)
            }
        })
    }
}

func BenchmarkGoSort(b *testing.B) {
    sizes := []int{100, 1000, 5000}
    
    for _, size := range sizes {
        b.Run(fmt.Sprintf("size_%d", size), func(b *testing.B) {
            arr := generateRandomArray(size)
            
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                GoSort(arr)
            }
        })
    }
}

func BenchmarkStringConcatenation(b *testing.B) {
    sizes := []int{100, 1000, 5000}
    
    for _, size := range sizes {
        strs := generateStringArray(size, "test")
        
        b.Run(fmt.Sprintf("concat_size_%d", size), func(b *testing.B) {
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                StringConcatenate(strs)
            }
        })
        
        b.Run(fmt.Sprintf("builder_size_%d", size), func(b *testing.B) {
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                StringBuilderConcatenate(strs)
            }
        })
    }
}

func BenchmarkSum(b *testing.B) {
    sizes := []int{1000, 10000, 100000}
    
    for _, size := range sizes {
        numbers := generateRandomArray(size)
        
        b.Run(fmt.Sprintf("sequential_size_%d", size), func(b *testing.B) {
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                SequentialSum(numbers)
            }
        })
        
        b.Run(fmt.Sprintf("concurrent_size_%d", size), func(b *testing.B) {
            b.ResetTimer()
            for i := 0; i < b.N; i++ {
                ConcurrentSum(numbers)
            }
        })
    }
}

func BenchmarkMemoryAllocation(b *testing.B) {
    b.Run("slice_allocation", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            _ = make([]int, 1000)
        }
    })
    
    b.Run("map_allocation", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            _ = make(map[string]int)
        }
    })
    
    b.Run("struct_allocation", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            _ = User{ID: i, Name: "User", Email: "user@example.com", Age: 30}
        }
    })
}

func BenchmarkJSONOperations(b *testing.B) {
    user := User{ID: 1, Name: "John Doe", Email: "john@example.com", Age: 30}
    
    b.Run("marshal", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            json.Marshal(user)
        }
    })
    
    userJSON, _ := json.Marshal(user)
    
    b.Run("unmarshal", func(b *testing.B) {
        for i := 0; i < b.N; i++ {
            var u User
            json.Unmarshal(userJSON, &u)
        }
    })
}

// Helper functions for benchmarks
func generateRandomArray(size int) []int {
    arr := make([]int, size)
    for i := 0; i < size; i++ {
        arr[i] = rand.Intn(size * 10)
    }
    return arr
}

func generateStringArray(size int, content string) []string {
    strs := make([]string, size)
    for i := 0; i < size; i++ {
        strs[i] = content
    }
    return strs
}

// Performance profiling test
func TestPerformanceProfile(t *testing.T) {
    if testing.Short() {
        t.Skip("Skipping performance test in short mode")
    }
    
    // Test with large dataset
    largeArray := generateRandomArray(100000)
    
    start := time.Now()
    result := QuickSort(largeArray)
    duration := time.Since(start)
    
    t.Logf("QuickSort of 100,000 elements took: %v", duration)
    
    // Verify result is sorted
    for i := 1; i < len(result); i++ {
        if result[i] < result[i-1] {
            t.Errorf("Array not sorted at index %d", i)
        }
    }
    
    // Compare with Go's built-in sort
    start = time.Now()
    result2 := GoSort(largeArray)
    goSortDuration := time.Since(start)
    
    t.Logf("Go sort of 100,000 elements took: %v", goSortDuration)
    
    if goSortDuration < duration {
        t.Logf("Go sort is %.2fx faster", float64(duration)/float64(goSortDuration))
    } else {
        t.Logf("QuickSort is %.2fx faster", float64(goSortDuration)/float64(duration))
    }
    
    // Verify both results are the same
    if len(result) != len(result2) {
        t.Errorf("Different result lengths: %d vs %d", len(result), len(result2))
    }
    
    for i := range result {
        if result[i] != result2[i] {
            t.Errorf("Different results at index %d: %d vs %d", i, result[i], result2[i])
        }
    }
}

// Memory usage test
func TestMemoryUsage(t *testing.T) {
    sizes := []int{1000, 10000, 100000}
    
    for _, size := range sizes {
        t.Run(fmt.Sprintf("size_%d", size), func(t *testing.T) {
            // Test slice memory usage
            start := time.Now()
            slice := make([]int, size)
            sliceCreationTime := time.Since(start)
            
            // Fill slice
            start = time.Now()
            for i := 0; i < size; i++ {
                slice[i] = i
            }
            fillTime := time.Since(start)
            
            t.Logf("Slice size %d: creation %v, fill %v", size, sliceCreationTime, fillTime)
            
            // Test map memory usage
            start = time.Now()
            m := make(map[int]int, size)
            mapCreationTime := time.Since(start)
            
            // Fill map
            start = time.Now()
            for i := 0; i < size; i++ {
                m[i] = i
            }
            mapFillTime := time.Since(start)
            
            t.Logf("Map size %d: creation %v, fill %v", size, mapCreationTime, mapFillTime)
        })
    }
}

// Concurrent performance test
func TestConcurrentPerformance(t *testing.T) {
    numbers := generateRandomArray(1000000)
    
    // Sequential sum
    start := time.Now()
    sequentialResult := SequentialSum(numbers)
    sequentialTime := time.Since(start)
    
    // Concurrent sum
    start = time.Now()
    concurrentResult := ConcurrentSum(numbers)
    concurrentTime := time.Since(start)
    
    t.Logf("Sequential sum: %v (result: %d)", sequentialTime, sequentialResult)
    t.Logf("Concurrent sum: %v (result: %d)", concurrentTime, concurrentResult)
    
    if sequentialResult != concurrentResult {
        t.Errorf("Results don't match: sequential=%d, concurrent=%d", sequentialResult, concurrentResult)
    }
    
    speedup := float64(sequentialTime) / float64(concurrentTime)
    t.Logf("Concurrent speedup: %.2fx", speedup)
}
```

## Testing Best Practices

### Test Organization and Utilities
```go
package main

import (
    "testing"
    "fmt"
    "os"
    "io/ioutil"
    "path/filepath"
)

// Test utilities and helpers
type TestSuite struct {
    name     string
    setup    func() interface{}
    teardown func(interface{})
    tests    []TestCase
}

type TestCase struct {
    name     string
    testFunc func(*testing.T, interface{})
}

func RunTestSuite(t *testing.T, suite TestSuite) {
    t.Run(suite.name, func(t *testing.T) {
        var fixture interface{}
        
        if suite.setup != nil {
            fixture = suite.setup()
        }
        
        if suite.teardown != nil {
            defer suite.teardown(fixture)
        }
        
        for _, tc := range suite.tests {
            t.Run(tc.name, func(t *testing.T) {
                tc.testFunc(t, fixture)
            })
        }
    })
}

// Assertion helpers
type Assertions struct {
    t *testing.T
}

func NewAssertions(t *testing.T) *Assertions {
    return &Assertions{t: t}
}

func (a *Assertions) Equal(expected, actual interface{}) {
    a.t.Helper()
    if expected != actual {
        a.t.Errorf("Expected %v, got %v", expected, actual)
    }
}

func (a *Assertions) NotEqual(expected, actual interface{}) {
    a.t.Helper()
    if expected == actual {
        a.t.Errorf("Expected %v to be different from %v", expected, actual)
    }
}

func (a *Assertions) True(condition bool, msg ...string) {
    a.t.Helper()
    if !condition {
        if len(msg) > 0 {
            a.t.Error(msg[0])
        } else {
            a.t.Error("Expected condition to be true")
        }
    }
}

func (a *Assertions) False(condition bool, msg ...string) {
    a.t.Helper()
    if condition {
        if len(msg) > 0 {
            a.t.Error(msg[0])
        } else {
            a.t.Error("Expected condition to be false")
        }
    }
}

func (a *Assertions) Nil(value interface{}) {
    a.t.Helper()
    if value != nil {
        a.t.Errorf("Expected nil, got %v", value)
    }
}

func (a *Assertions) NotNil(value interface{}) {
    a.t.Helper()
    if value == nil {
        a.t.Error("Expected not nil")
    }
}

func (a *Assertions) Error(err error) {
    a.t.Helper()
    if err == nil {
        a.t.Error("Expected error")
    }
}

func (a *Assertions) NoError(err error) {
    a.t.Helper()
    if err != nil {
        a.t.Errorf("Unexpected error: %v", err)
    }
}

func (a *Assertions) Contains(slice, element interface{}) {
    a.t.Helper()
    // Simple implementation for strings
    if str, ok := slice.(string); ok {
        if elem, ok := element.(string); ok {
            if !strings.Contains(str, elem) {
                a.t.Errorf("Expected %q to contain %q", str, elem)
            }
            return
        }
    }
    a.t.Error("Contains not implemented for these types")
}

// Test data generators
type TestDataGenerator struct {
    rand *rand.Rand
}

func NewTestDataGenerator() *TestDataGenerator {
    return &TestDataGenerator{
        rand: rand.New(rand.NewSource(time.Now().UnixNano())),
    }
}

func (tg *TestDataGenerator) RandomString(length int) string {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    result := make([]byte, length)
    for i := range result {
        result[i] = charset[tg.rand.Intn(len(charset))]
    }
    return string(result)
}

func (tg *TestDataGenerator) RandomEmail() string {
    return fmt.Sprintf("%s@example.com", tg.RandomString(8))
}

func (tg *TestDataGenerator) RandomUser() User {
    return User{
        ID:    tg.rand.Intn(10000),
        Name:  tg.RandomString(10),
        Email: tg.RandomEmail(),
        Age:   tg.rand.Intn(80) + 18,
    }
}

func (tg *TestDataGenerator) RandomUsers(count int) []User {
    users := make([]User, count)
    for i := range users {
        users[i] = tg.RandomUser()
    }
    return users
}

// File system test utilities
type TestFileSystem struct {
    tempDir string
    t       *testing.T
}

func NewTestFileSystem(t *testing.T) *TestFileSystem {
    tempDir, err := ioutil.TempDir("", "test_")
    if err != nil {
        t.Fatalf("Failed to create temp dir: %v", err)
    }
    
    return &TestFileSystem{
        tempDir: tempDir,
        t:       t,
    }
}

func (tfs *TestFileSystem) Cleanup() {
    os.RemoveAll(tfs.tempDir)
}

func (tfs *TestFileSystem) Path(filename string) string {
    return filepath.Join(tfs.tempDir, filename)
}

func (tfs *TestFileSystem) WriteFile(filename, content string) {
    path := tfs.Path(filename)
    err := ioutil.WriteFile(path, []byte(content), 0644)
    if err != nil {
        tfs.t.Fatalf("Failed to write file %s: %v", filename, err)
    }
}

func (tfs *TestFileSystem) ReadFile(filename string) string {
    path := tfs.Path(filename)
    content, err := ioutil.ReadFile(path)
    if err != nil {
        tfs.t.Fatalf("Failed to read file %s: %v", filename, err)
    }
    return string(content)
}

func (tfs *TestFileSystem) Exists(filename string) bool {
    path := tfs.Path(filename)
    _, err := os.Stat(path)
    return !os.IsNotExist(err)
}

// Example test suite
func TestUserService_Comprehensive(t *testing.T) {
    generator := NewTestDataGenerator()
    
    suite := TestSuite{
        name: "UserService Comprehensive Tests",
        setup: func() interface{} {
            mockDB := NewMockDatabase()
            service := NewUserService(mockDB)
            return struct {
                db      *MockDatabase
                service *UserService
            }{
                db:      mockDB,
                service: service,
            }
        },
        teardown: func(fixture interface{}) {
            // Cleanup if needed
        },
        tests: []TestCase{
            {
                name: "create_multiple_users",
                testFunc: func(t *testing.T, fixture interface{}) {
                    setup := fixture.(struct {
                        db      *MockDatabase
                        service *UserService
                    })
                    
                    assert := NewAssertions(t)
                    
                    users := generator.RandomUsers(5)
                    
                    for _, user := range users {
                        created, err := setup.service.CreateUser(user.Name, user.Email, user.Age)
                        assert.NoError(err)
                        assert.NotEqual(0, created.ID)
                        assert.Equal(user.Name, created.Name)
                        assert.Equal(user.Email, created.Email)
                        assert.Equal(user.Age, created.Age)
                    }
                },
            },
            {
                name: "update_user_fields",
                testFunc: func(t *testing.T, fixture interface{}) {
                    setup := fixture.(struct {
                        db      *MockDatabase
                        service *UserService
                    })
                    
                    assert := NewAssertions(t)
                    
                    // Create user first
                    user := generator.RandomUser()
                    setup.db.AddUser(user)
                    
                    // Update name
                    updated, err := setup.service.UpdateUser(user.ID, "Updated Name", "", -1)
                    assert.NoError(err)
                    assert.Equal("Updated Name", updated.Name)
                    assert.Equal(user.Email, updated.Email)
                    assert.Equal(user.Age, updated.Age)
                    
                    // Update email
                    updated, err = setup.service.UpdateUser(user.ID, "", "updated@example.com", -1)
                    assert.NoError(err)
                    assert.Equal("Updated Name", updated.Name)
                    assert.Equal("updated@example.com", updated.Email)
                    assert.Equal(user.Age, updated.Age)
                    
                    // Update age
                    updated, err = setup.service.UpdateUser(user.ID, "", "", 35)
                    assert.NoError(err)
                    assert.Equal("Updated Name", updated.Name)
                    assert.Equal("updated@example.com", updated.Email)
                    assert.Equal(35, updated.Age)
                },
            },
            {
                name: "handle_concurrent_operations",
                testFunc: func(t *testing.T, fixture interface{}) {
                    setup := fixture.(struct {
                        db      *MockDatabase
                        service *UserService
                    })
                    
                    assert := NewAssertions(t)
                    
                    const numGoroutines = 10
                    const numOperations = 5
                    
                    var wg sync.WaitGroup
                    errors := make(chan error, numGoroutines*numOperations)
                    
                    for i := 0; i < numGoroutines; i++ {
                        wg.Add(1)
                        go func(id int) {
                            defer wg.Done()
                            
                            for j := 0; j < numOperations; j++ {
                                user := generator.RandomUser()
                                user.ID = id*100 + j // Ensure unique IDs
                                
                                _, err := setup.service.CreateUser(user.Name, user.Email, user.Age)
                                if err != nil {
                                    errors <- fmt.Errorf("goroutine %d, op %d: %w", id, j, err)
                                    return
                                }
                            }
                        }(i)
                    }
                    
                    wg.Wait()
                    close(errors)
                    
                    for err := range errors {
                        assert.NoError(err)
                    }
                },
            },
        },
    }
    
    RunTestSuite(t, suite)
}

// Integration test with file system
func TestFileBasedUserService(t *testing.T) {
    suite := TestSuite{
        name: "File-based UserService",
        setup: func() interface{} {
            fs := NewTestFileSystem(t)
            return fs
        },
        teardown: func(fixture interface{}) {
            fs := fixture.(*TestFileSystem)
            fs.Cleanup()
        },
        tests: []TestCase{
            {
                name: "save_and_load_user",
                testFunc: func(t *testing.T, fixture interface{}) {
                    fs := fixture.(*TestFileSystem)
                    assert := NewAssertions(t)
                    
                    user := User{ID: 1, Name: "Test User", Email: "test@example.com", Age: 30}
                    userJSON, _ := json.Marshal(user)
                    
                    fs.WriteFile("user_1.json", string(userJSON))
                    assert.True(fs.Exists("user_1.json"))
                    
                    loadedJSON := fs.ReadFile("user_1.json")
                    assert.Contains(loadedJSON, "Test User")
                    assert.Contains(loadedJSON, "test@example.com")
                },
            },
            {
                name: "handle_missing_file",
                testFunc: func(t *testing.T, fixture interface{}) {
                    fs := fixture.(*TestFileSystem)
                    assert := NewAssertions(t)
                    
                    assert.False(fs.Exists("nonexistent.json"))
                    
                    content := fs.ReadFile("nonexistent.json")
                    assert.Equal("", content)
                },
            },
        },
    }
    
    RunTestSuite(t, suite)
}

// Test configuration and environment
func TestWithEnvironment(t *testing.T) {
    // Save original environment
    originalEnv := os.Getenv("TEST_ENV")
    defer func() {
        if originalEnv != "" {
            os.Setenv("TEST_ENV", originalEnv)
        } else {
            os.Unsetenv("TEST_ENV")
        }
    }()
    
    tests := []struct {
        name     string
        envValue string
        expected string
    }{
        {"development", "dev", "development mode"},
        {"production", "prod", "production mode"},
        {"testing", "test", "testing mode"},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            os.Setenv("TEST_ENV", tt.envValue)
            
            env := os.Getenv("TEST_ENV")
            if env != tt.envValue {
                t.Errorf("Expected env %s, got %s", tt.envValue, env)
            }
            
            // Test environment-specific behavior
            var mode string
            switch env {
            case "dev":
                mode = "development mode"
            case "prod":
                mode = "production mode"
            case "test":
                mode = "testing mode"
            default:
                mode = "unknown mode"
            }
            
            if mode != tt.expected {
                t.Errorf("Expected mode %s, got %s", tt.expected, mode)
            }
        })
    }
}

// Performance regression test
func TestPerformanceRegression(t *testing.T) {
    if testing.Short() {
        t.Skip("Skipping performance regression test in short mode")
    }
    
    // Define performance thresholds
    thresholds := map[string]time.Duration{
        "user_creation": 1 * time.Millisecond,
        "user_retrieval": 500 * time.Microsecond,
        "user_update":    750 * time.Microsecond,
    }
    
    mockDB := NewMockDatabase()
    service := NewUserService(mockDB)
    
    // Test user creation performance
    start := time.Now()
    user, err := service.CreateUser("Perf Test", "perf@example.com", 30)
    creationTime := time.Since(start)
    
    if err != nil {
        t.Fatalf("Failed to create user: %v", err)
    }
    
    if creationTime > thresholds["user_creation"] {
        t.Errorf("User creation took %v, threshold is %v", creationTime, thresholds["user_creation"])
    }
    
    // Test user retrieval performance
    mockDB.AddUser(user)
    
    start = time.Now()
    _, err = service.GetUser(user.ID)
    retrievalTime := time.Since(start)
    
    if err != nil {
        t.Fatalf("Failed to get user: %v", err)
    }
    
    if retrievalTime > thresholds["user_retrieval"] {
        t.Errorf("User retrieval took %v, threshold is %v", retrievalTime, thresholds["user_retrieval"])
    }
    
    // Test user update performance
    start = time.Now()
    _, err = service.UpdateUser(user.ID, "Updated", "updated@example.com", 35)
    updateTime := time.Since(start)
    
    if err != nil {
        t.Fatalf("Failed to update user: %v", err)
    }
    
    if updateTime > thresholds["user_update"] {
        t.Errorf("User update took %v, threshold is %v", updateTime, thresholds["user_update"])
    }
    
    t.Logf("Performance results:")
    t.Logf("  Creation: %v (threshold: %v)", creationTime, thresholds["user_creation"])
    t.Logf("  Retrieval: %v (threshold: %v)", retrievalTime, thresholds["user_retrieval"])
    t.Logf("  Update:    %v (threshold: %v)", updateTime, thresholds["user_update"])
}
```

## Summary

Go testing provides:

**Testing Fundamentals:**
- Built-in testing package
- Table-driven tests
- Benchmark tests
- Example tests
- Subtests for organization

**Advanced Testing:**
- Mock objects and fakes
- Integration testing
- HTTP service testing
- Performance testing
- Concurrent testing

**Testing Patterns:**
- Test suites and fixtures
- Assertion helpers
- Test data generators
- File system utilities
- Environment testing

**Best Practices:**
- Test organization
- Helper functions
- Setup/teardown
- Error testing
- Performance regression

**Key Features:**
- Comprehensive standard library
- Easy to write and maintain
- Good tooling support
- Coverage analysis
- Benchmarking capabilities

**Common Use Cases:**
- Unit testing
- Integration testing
- API testing
- Performance testing
- Regression testing

Go's testing philosophy emphasizes simplicity, readability, and comprehensive coverage with excellent tooling support.
