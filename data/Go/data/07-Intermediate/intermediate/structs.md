# Go Structs

## Struct Basics

### Struct Definition and Declaration
```go
package main

import "fmt"

func main() {
    // Basic struct definition
    type Person struct {
        Name string
        Age  int
        City string
    }
    
    // Creating struct instances
    var p1 Person
    p1.Name = "John Doe"
    p1.Age = 30
    p1.City = "New York"
    
    fmt.Printf("Person 1: %+v\n", p1)
    
    // Struct literal
    p2 := Person{
        Name: "Jane Smith",
        Age:  25,
        City: "Boston",
    }
    
    fmt.Printf("Person 2: %+v\n", p2)
    
    // Struct literal with field order
    p3 := Person{"Alice Johnson", 28, "Chicago"}
    fmt.Printf("Person 3: %+v\n", p3)
    
    // Partial struct literal (zero values for missing fields)
    p4 := Person{Name: "Bob Brown"}
    fmt.Printf("Person 4: %+v\n", p4)
    
    // Pointer to struct
    p5 := &Person{Name: "Carol Davis", Age: 35, City: "Seattle"}
    fmt.Printf("Person 5 (pointer): %+v\n", p5)
    fmt.Printf("Dereferenced: %+v\n", *p5)
    
    // Struct with different field types
    type Employee struct {
        ID          int
        FirstName   string
        LastName    string
        Email       string
        Salary      float64
        IsActive    bool
        Department  string
        HireDate    time.Time
        Skills      []string
        Address     Address
    }
    
    type Address struct {
        Street  string
        City    string
        State   string
        ZipCode string
    }
    
    emp := Employee{
        ID:        1001,
        FirstName: "John",
        LastName:  "Doe",
        Email:     "john.doe@company.com",
        Salary:    75000.50,
        IsActive:  true,
        Department: "Engineering",
        HireDate:  time.Now(),
        Skills:    []string{"Go", "Python", "Docker"},
        Address: Address{
            Street:  "123 Main St",
            City:    "San Francisco",
            State:   "CA",
            ZipCode: "94102",
        },
    }
    
    fmt.Printf("Employee: %+v\n", emp)
    
    // Accessing struct fields
    fmt.Printf("Employee Name: %s %s\n", emp.FirstName, emp.LastName)
    fmt.Printf("Employee Email: %s\n", emp.Email)
    fmt.Printf("Employee Address: %s, %s, %s %s\n", 
        emp.Address.Street, emp.Address.City, 
        emp.Address.State, emp.Address.ZipCode)
    
    // Modifying struct fields
    emp.Salary = 80000.00
    emp.Skills = append(emp.Skills, "Kubernetes")
    fmt.Printf("Updated Employee: %+v\n", emp)
    
    // Comparing structs
    emp2 := Employee{
        ID:        1001,
        FirstName: "John",
        LastName:  "Doe",
        Email:     "john.doe@company.com",
        Salary:    75000.50,
        IsActive:  true,
        Department: "Engineering",
        HireDate:  emp.HireDate,
        Skills:    []string{"Go", "Python", "Docker"},
        Address: Address{
            Street:  "123 Main St",
            City:    "San Francisco",
            State:   "CA",
            ZipCode: "94102",
        },
    }
    
    fmt.Printf("emp == emp2: %t\n", emp == emp2)
    
    emp3 := Employee{
        ID:        1002,
        FirstName: "Jane",
        LastName:  "Smith",
        Email:     "jane.smith@company.com",
        Salary:    65000.00,
        IsActive:  true,
        Department: "Marketing",
        HireDate:  time.Now(),
        Skills:    []string{"SEO", "Analytics"},
        Address: Address{
            Street:  "456 Oak Ave",
            City:    "Los Angeles",
            State:   "CA",
            ZipCode: "90210",
        },
    }
    
    fmt.Printf("emp == emp3: %t\n", emp == emp3)
    
    // Anonymous struct
    user := struct {
        Username string
        Email    string
        Age      int
    }{
        Username: "johndoe",
        Email:    "john@example.com",
        Age:      30,
    }
    
    fmt.Printf("Anonymous struct: %+v\n", user)
    
    // Nested anonymous struct
    product := struct {
        Name     string
        Price    float64
        Category struct {
            Name  string
            Level string
        }
    }{
        Name:  "Laptop",
        Price: 999.99,
        Category: struct {
            Name  string
            Level string
        }{
            Name:  "Electronics",
            Level: "Premium",
        },
    }
    
    fmt.Printf("Nested anonymous struct: %+v\n", product)
}
```

### Struct Methods
```go
package main

import "fmt"
import "math"

func main() {
    // Rectangle struct with methods
    type Rectangle struct {
        Width  float64
        Height float64
    }
    
    // Value receiver method
    func (r Rectangle) Area() float64 {
        return r.Width * r.Height
    }
    
    // Value receiver method
    func (r Rectangle) Perimeter() float64 {
        return 2 * (r.Width + r.Height)
    }
    
    // Value receiver method
    func (r Rectangle) IsSquare() bool {
        return r.Width == r.Height
    }
    
    // Pointer receiver method (modifies struct)
    func (r *Rectangle) Scale(factor float64) {
        r.Width *= factor
        r.Height *= factor
    }
    
    // Pointer receiver method (modifies struct)
    func (r *Rectangle) Resize(width, height float64) {
        r.Width = width
        r.Height = height
    }
    
    // Method with return values
    func (r Rectangle) Dimensions() (float64, float64) {
        return r.Width, r.Height
    }
    
    // Method that calls other methods
    func (r Rectangle) String() string {
        return fmt.Sprintf("Rectangle{Width: %.2f, Height: %.2f, Area: %.2f}", 
            r.Width, r.Height, r.Area())
    }
    
    // Create and use Rectangle
    rect := Rectangle{Width: 10.0, Height: 5.0}
    
    fmt.Printf("Rectangle: %s\n", rect.String())
    fmt.Printf("Area: %.2f\n", rect.Area())
    fmt.Printf("Perimeter: %.2f\n", rect.Perimeter())
    fmt.Printf("Is square: %t\n", rect.IsSquare())
    
    // Use pointer receiver method
    rect.Scale(2.0)
    fmt.Printf("After scaling: %s\n", rect.String())
    
    // Use another pointer receiver method
    rect.Resize(15.0, 8.0)
    fmt.Printf("After resizing: %s\n", rect.String())
    
    // BankAccount struct with methods
    type BankAccount struct {
        AccountNumber string
        Balance      float64
        Owner         string
        IsActive      bool
    }
    
    // Constructor-like function
    func NewBankAccount(accountNumber, owner string, initialBalance float64) *BankAccount {
        return &BankAccount{
            AccountNumber: accountNumber,
            Balance:      initialBalance,
            Owner:         owner,
            IsActive:      true,
        }
    }
    
    // Deposit method
    func (ba *BankAccount) Deposit(amount float64) error {
        if amount <= 0 {
            return fmt.Errorf("deposit amount must be positive")
        }
        
        ba.Balance += amount
        return nil
    }
    
    // Withdraw method
    func (ba *BankAccount) Withdraw(amount float64) error {
        if amount <= 0 {
            return fmt.Errorf("withdrawal amount must be positive")
        }
        
        if amount > ba.Balance {
            return fmt.Errorf("insufficient funds")
        }
        
        ba.Balance -= amount
        return nil
    }
    
    // Get balance method
    func (ba BankAccount) GetBalance() float64 {
        return ba.Balance
    }
    
    // Transfer method
    func (ba *BankAccount) Transfer(to *BankAccount, amount float64) error {
        // Withdraw from this account
        if err := ba.Withdraw(amount); err != nil {
            return err
        }
        
        // Deposit to target account
        if err := to.Deposit(amount); err != nil {
            // Deposit back if transfer fails
            ba.Deposit(amount)
            return err
        }
        
        return nil
    }
    
    // Close account method
    func (ba *BankAccount) Close() error {
        if ba.Balance > 0 {
            return fmt.Errorf("cannot close account with positive balance")
        }
        
        ba.IsActive = false
        return nil
    }
    
    // String method
    func (ba BankAccount) String() string {
        status := "Active"
        if !ba.IsActive {
            status = "Inactive"
        }
        
        return fmt.Sprintf("Account[%s]: Owner: %s, Balance: $%.2f, Status: %s", 
            ba.AccountNumber, ba.Owner, ba.Balance, status)
    }
    
    // Create and use BankAccount
    account1 := NewBankAccount("123456", "John Doe", 1000.0)
    account2 := NewBankAccount("789012", "Jane Smith", 500.0)
    
    fmt.Printf("Account 1: %s\n", account1.String())
    fmt.Printf("Account 2: %s\n", account2.String())
    
    // Deposit
    err := account1.Deposit(500.0)
    if err != nil {
        fmt.Printf("Deposit error: %v\n", err)
    } else {
        fmt.Printf("Deposit successful. New balance: $%.2f\n", account1.GetBalance())
    }
    
    // Withdraw
    err = account1.Withdraw(200.0)
    if err != nil {
        fmt.Printf("Withdrawal error: %v\n", err)
    } else {
        fmt.Printf("Withdrawal successful. New balance: $%.2f\n", account1.GetBalance())
    }
    
    // Transfer
    err = account1.Transfer(account2, 300.0)
    if err != nil {
        fmt.Printf("Transfer error: %v\n", err)
    } else {
        fmt.Printf("Transfer successful\n")
        fmt.Printf("Account 1 balance: $%.2f\n", account1.GetBalance())
        fmt.Printf("Account 2 balance: $%.2f\n", account2.GetBalance())
    }
    
    // Close account
    err = account2.Close()
    if err != nil {
        fmt.Printf("Close error: %v\n", err)
    } else {
        fmt.Printf("Account closed: %s\n", account2.String())
    }
    
    // Calculator struct with methods
    type Calculator struct {
        Memory float64
        History []string
    }
    
    func NewCalculator() *Calculator {
        return &Calculator{
            Memory:  0,
            History: []string{},
        }
    }
    
    func (c *Calculator) Add(a, b float64) float64 {
        result := a + b
        c.History = append(c.History, fmt.Sprintf("%.2f + %.2f = %.2f", a, b, result))
        return result
    }
    
    func (c *Calculator) Subtract(a, b float64) float64 {
        result := a - b
        c.History = append(c.History, fmt.Sprintf("%.2f - %.2f = %.2f", a, b, result))
        return result
    }
    
    func (c *Calculator) Multiply(a, b float64) float64 {
        result := a * b
        c.History = append(c.History, fmt.Sprintf("%.2f * %.2f = %.2f", a, b, result))
        return result
    }
    
    func (c *Calculator) Divide(a, b float64) (float64, error) {
        if b == 0 {
            return 0, fmt.Errorf("division by zero")
        }
        
        result := a / b
        c.History = append(c.History, fmt.Sprintf("%.2f / %.2f = %.2f", a, b, result))
        return result, nil
    }
    
    func (c *Calculator) Power(base, exponent float64) float64 {
        result := math.Pow(base, exponent)
        c.History = append(c.History, fmt.Sprintf("%.2f ^ %.2f = %.2f", base, exponent, result))
        return result
    }
    
    func (c *Calculator) Store(value float64) {
        c.Memory = value
        c.History = append(c.History, fmt.Sprintf("Stored %.2f in memory", value))
    }
    
    func (c *Calculator) Recall() float64 {
        c.History = append(c.History, fmt.Sprintf("Recalled %.2f from memory", c.Memory))
        return c.Memory
    }
    
    func (c *Calculator) Clear() {
        c.Memory = 0
        c.History = append(c.History, "Cleared memory")
    }
    
    func (c Calculator) GetHistory() []string {
        return c.History
    }
    
    func (c Calculator) String() string {
        return fmt.Sprintf("Calculator{Memory: %.2f, History: %d operations}", 
            c.Memory, len(c.History))
    }
    
    // Create and use Calculator
    calc := NewCalculator()
    
    result := calc.Add(10, 5)
    fmt.Printf("10 + 5 = %.2f\n", result)
    
    result = calc.Multiply(result, 2)
    fmt.Printf("Result * 2 = %.2f\n", result)
    
    calc.Store(result)
    fmt.Printf("Stored %.2f in memory\n", calc.Memory)
    
    recalled := calc.Recall()
    fmt.Printf("Recalled %.2f from memory\n", recalled)
    
    result, err := calc.Divide(20, 4)
    if err != nil {
        fmt.Printf("Division error: %v\n", err)
    } else {
        fmt.Printf("20 / 4 = %.2f\n", result)
    }
    
    fmt.Printf("Calculator: %s\n", calc.String())
    fmt.Printf("History:\n")
    for i, operation := range calc.GetHistory() {
        fmt.Printf("%d. %s\n", i+1, operation)
    }
}
```

## Struct Composition

### Embedding and Composition
```go
package main

import "fmt"

func main() {
    // Basic composition
    type Address struct {
        Street  string
        City    string
        State   string
        ZipCode string
    }
    
    type Person struct {
        Name    string
        Age     int
        Address Address
    }
    
    // Create person with embedded address
    person := Person{
        Name: "John Doe",
        Age:  30,
        Address: Address{
            Street:  "123 Main St",
            City:    "New York",
            State:   "NY",
            ZipCode: "10001",
        },
    }
    
    fmt.Printf("Person: %+v\n", person)
    fmt.Printf("Address: %+v\n", person.Address)
    fmt.Printf("City: %s\n", person.Address.City)
    
    // Method promotion with embedding
    type Animal struct {
        Name string
        Age  int
    }
    
    func (a Animal) Speak() string {
        return fmt.Sprintf("%s makes a sound", a.Name)
    }
    
    func (a Animal) Eat() string {
        return fmt.Sprintf("%s is eating", a.Name)
    }
    
    type Dog struct {
        Animal
        Breed string
    }
    
    // Override Speak method
    func (d Dog) Speak() string {
        return fmt.Sprintf("%s barks", d.Name)
    }
    
    func (d Dog) WagTail() string {
        return fmt.Sprintf("%s is wagging tail", d.Name)
    }
    
    // Create and use embedded struct
    dog := Dog{
        Animal: Animal{Name: "Buddy", Age: 3},
        Breed:  "Golden Retriever",
    }
    
    fmt.Printf("Dog: %+v\n", dog)
    fmt.Printf("Dog speaks: %s\n", dog.Speak())
    fmt.Printf("Dog eats: %s\n", dog.Eat())
    fmt.Printf("Dog wags tail: %s\n", dog.WagTail())
    
    // Multiple embedding
    type Flyer interface {
        Fly() string
    }
    
    type Bird struct {
        Name     string
        Wingspan int
    }
    
    func (b Bird) Fly() string {
        return fmt.Sprintf("%s is flying", b.Name)
    }
    
    type Swimmer interface {
        Swim() string
    }
    
    type Fish struct {
        Name  string
        Fins  int
    }
    
    func (f Fish) Swim() string {
        return fmt.Sprintf("%s is swimming", f.Name)
    }
    
    type Duck struct {
        Bird
        Fish
        Color string
    }
    
    func (d Duck) Quack() string {
        return fmt.Sprintf("%s is quacking", d.Name)
    }
    
    // Create and use multiple embedding
    duck := Duck{
        Bird: Bird{Name: "Donald", Wingspan: 60},
        Fish: Fish{Name: "Donald", Fins: 2},
        Color: "White",
    }
    
    fmt.Printf("Duck: %+v\n", duck)
    fmt.Printf("Duck flies: %s\n", duck.Fly())
    fmt.Printf("Duck swims: %s\n", duck.Swim())
    fmt.Printf("Duck quacks: %s\n", duck.Quack())
    
    // Composition with interfaces
    type Logger interface {
        Log(message string)
    }
    
    type ConsoleLogger struct{}
    
    func (cl ConsoleLogger) Log(message string) {
        fmt.Printf("LOG: %s\n", message)
    }
    
    type FileLogger struct {
        Filename string
    }
    
    func (fl FileLogger) Log(message string) {
        fmt.Printf("FILE [%s]: %s\n", fl.Filename, message)
    }
    
    type Service struct {
        Name   string
        Logger Logger
    }
    
    func (s Service) Start() {
        s.Logger.Log(fmt.Sprintf("Service %s started", s.Name))
    }
    
    func (s Service) Stop() {
        s.Logger.Log(fmt.Sprintf("Service %s stopped", s.Name))
    }
    
    // Create services with different loggers
    consoleService := Service{
        Name:   "Console Service",
        Logger: ConsoleLogger{},
    }
    
    fileService := Service{
        Name:   "File Service",
        Logger: FileLogger{Filename: "service.log"},
    }
    
    consoleService.Start()
    consoleService.Stop()
    
    fileService.Start()
    fileService.Stop()
    
    // Composition with pointers
    type Engine struct {
        Power  int
        Type   string
        Active bool
    }
    
    func (e *Engine) Start() {
        e.Active = true
        fmt.Printf("Engine started (Power: %d, Type: %s)\n", e.Power, e.Type)
    }
    
    func (e *Engine) Stop() {
        e.Active = false
        fmt.Printf("Engine stopped\n")
    }
    
    func (e Engine) Status() string {
        if e.Active {
            return "Running"
        }
        return "Stopped"
    }
    
    type Car struct {
        Make    string
        Model   string
        Year    int
        Engine  *Engine
        Color   string
    }
    
    func (c Car) Start() {
        fmt.Printf("Starting %s %s (%d)\n", c.Make, c.Model, c.Year)
        c.Engine.Start()
    }
    
    func (c Car) Stop() {
        fmt.Printf("Stopping %s %s (%d)\n", c.Make, c.Model, c.Year)
        c.Engine.Stop()
    }
    
    func (c Car) GetEngineStatus() string {
        return c.Engine.Status()
    }
    
    // Create and use composition with pointers
    engine := &Engine{Power: 200, Type: "V6", Active: false}
    car := Car{
        Make:   "Toyota",
        Model:  "Camry",
        Year:   2022,
        Engine: engine,
        Color:  "Blue",
    }
    
    fmt.Printf("Car: %+v\n", car)
    fmt.Printf("Engine status: %s\n", car.GetEngineStatus())
    
    car.Start()
    fmt.Printf("Engine status: %s\n", car.GetEngineStatus())
    
    car.Stop()
    fmt.Printf("Engine status: %s\n", car.GetEngineStatus())
    
    // Composition with anonymous structs
    type Product struct {
        ID          int
        Name        string
        Price       float64
        Description struct {
            Short string
            Long  string
        }
        Inventory struct {
            Quantity int
            Location string
        }
    }
    
    product := Product{
        ID:    1001,
        Name:  "Laptop",
        Price: 999.99,
        Description: struct {
            Short string
            Long  string
        }{
            Short: "High-performance laptop",
            Long:  "A powerful laptop with the latest processor, ample RAM, and fast storage for all your computing needs.",
        },
        Inventory: struct {
            Quantity int
            Location string
        }{
            Quantity: 50,
            Location: "Warehouse A",
        },
    }
    
    fmt.Printf("Product: %+v\n", product)
    fmt.Printf("Short description: %s\n", product.Description.Short)
    fmt.Printf("Quantity: %d\n", product.Inventory.Quantity)
    
    // Composition with methods
    type Author struct {
        Name  string
        Email string
    }
    
    func (a Author) GetInfo() string {
        return fmt.Sprintf("Author: %s (%s)", a.Name, a.Email)
    }
    
    type Book struct {
        Title    string
        ISBN     string
        Author   Author
        Year     int
        Genre    string
        Pages    int
        Publisher string
    }
    
    func (b Book) GetInfo() string {
        return fmt.Sprintf("Book: %s by %s (%s, %d pages)", 
            b.Title, b.Author.Name, b.ISBN, b.Pages)
    }
    
    func (b Book) GetFullInfo() string {
        return fmt.Sprintf("%s\n%s\nPublished: %d\nGenre: %s\nPublisher: %s", 
            b.GetInfo(), b.Author.GetInfo(), b.Year, b.Genre, b.Publisher)
    }
    
    // Create and use composed structs with methods
    author := Author{Name: "Jane Smith", Email: "jane@example.com"}
    book := Book{
        Title:     "Go Programming",
        ISBN:      "978-1234567890",
        Author:    author,
        Year:      2023,
        Genre:     "Programming",
        Pages:     450,
        Publisher: "Tech Books",
    }
    
    fmt.Printf("Book info: %s\n", book.GetInfo())
    fmt.Printf("Full info:\n%s\n", book.GetFullInfo())
}
```

## Struct Tags

### JSON and Database Tags
```go
package main

import (
    "encoding/json"
    "fmt"
    "strings"
    "time"
)

func main() {
    // JSON tags
    type User struct {
        ID       int    `json:"id"`
        Username string `json:"username"`
        Email    string `json:"email"`
        Password string `json:"-"` // Don't include in JSON
        Active   bool   `json:"active,omitempty"` // Omit if zero value
        Created  time.Time `json:"created_at"`
        Updated  time.Time `json:"updated_at,omitempty"`
    }
    
    user := User{
        ID:       1,
        Username: "johndoe",
        Email:    "john@example.com",
        Password: "secret123",
        Active:   true,
        Created:  time.Now(),
    }
    
    // Marshal to JSON
    jsonData, err := json.Marshal(user)
    if err != nil {
        fmt.Printf("Error marshaling to JSON: %v\n", err)
        return
    }
    
    fmt.Printf("JSON: %s\n", jsonData)
    
    // Unmarshal from JSON
    var user2 User
    err = json.Unmarshal(jsonData, &user2)
    if err != nil {
        fmt.Printf("Error unmarshaling from JSON: %v\n", err)
        return
    }
    
    fmt.Printf("Unmarshaled: %+v\n", user2)
    
    // Custom JSON tags
    type Product struct {
        ID          int     `json:"product_id"`
        Name        string  `json:"name"`
        Price       float64 `json:"price,string"` // Convert to/from string
        Description string  `json:"description,omitempty"`
        Tags        []string `json:"tags,omitempty"`
        Available   bool    `json:"available"`
        CreatedAt   time.Time `json:"created_at"`
    }
    
    product := Product{
        ID:          1001,
        Name:        "Laptop",
        Price:       999.99,
        Description: "High-performance laptop",
        Tags:        []string{"electronics", "computer"},
        Available:   true,
        CreatedAt:   time.Now(),
    }
    
    // Marshal with custom tags
    productJSON, err := json.Marshal(product)
    if err != nil {
        fmt.Printf("Error marshaling product: %v\n", err)
        return
    }
    
    fmt.Printf("Product JSON: %s\n", productJSON)
    
    // Database tags (common convention)
    type Customer struct {
        ID        int       `db:"id" json:"id"`
        FirstName string    `db:"first_name" json:"first_name"`
        LastName  string    `db:"last_name" json:"last_name"`
        Email     string    `db:"email" json:"email"`
        Phone     string    `db:"phone" json:"phone,omitempty"`
        Address   string    `db:"address" json:"address,omitempty"`
        CreatedAt time.Time `db:"created_at" json:"created_at"`
        UpdatedAt time.Time `db:"updated_at" json:"updated_at,omitempty"`
    }
    
    customer := Customer{
        ID:        1,
        FirstName: "John",
        LastName:  "Doe",
        Email:     "john@example.com",
        Phone:     "555-1234",
        Address:   "123 Main St",
        CreatedAt: time.Now(),
    }
    
    fmt.Printf("Customer: %+v\n", customer)
    
    // XML tags
    type Order struct {
        ID        int       `xml:"id,attr"`
        CustomerID int      `xml:"customer_id"`
        Items     []Item    `xml:"items>item"`
        Total     float64   `xml:"total"`
        Status    string    `xml:"status"`
        CreatedAt time.Time `xml:"created_at"`
    }
    
    type Item struct {
        ID       int     `xml:"id,attr"`
        Name     string  `xml:"name"`
        Price    float64 `xml:"price"`
        Quantity int     `xml:"quantity"`
    }
    
    order := Order{
        ID:        1001,
        CustomerID: 1,
        Items: []Item{
            {ID: 1, Name: "Laptop", Price: 999.99, Quantity: 1},
            {ID: 2, Name: "Mouse", Price: 29.99, Quantity: 1},
        },
        Total:     1029.98,
        Status:    "pending",
        CreatedAt: time.Now(),
    }
    
    // Marshal to XML (would need xml package)
    fmt.Printf("Order: %+v\n", order)
    
    // Custom validation tags
    type Registration struct {
        Username string `validate:"required,min=3,max=20" json:"username"`
        Email    string `validate:"required,email" json:"email"`
        Password string `validate:"required,min=8" json:"password"`
        Age      int    `validate:"min=18,max=120" json:"age"`
        Terms    bool   `validate:"required" json:"terms"`
    }
    
    registration := Registration{
        Username: "johndoe",
        Email:    "john@example.com",
        Password: "password123",
        Age:      30,
        Terms:    true,
    }
    
    fmt.Printf("Registration: %+v\n", registration)
    
    // Configuration tags
    type Config struct {
        Database DatabaseConfig `json:"database" yaml:"database"`
        Server   ServerConfig   `json:"server" yaml:"server"`
        Logging  LoggingConfig  `json:"logging" yaml:"logging"`
    }
    
    type DatabaseConfig struct {
        Host     string `json:"host" yaml:"host"`
        Port     int    `json:"port" yaml:"port"`
        Name     string `json:"name" yaml:"name"`
        Username string `json:"username" yaml:"username"`
        Password string `json:"password" yaml:"password"`
    }
    
    type ServerConfig struct {
        Host string `json:"host" yaml:"host"`
        Port int    `json:"port" yaml:"port"`
        Mode string `json:"mode" yaml:"mode"`
    }
    
    type LoggingConfig struct {
        Level  string `json:"level" yaml:"level"`
        Format string `json:"format" yaml:"format"`
        Output string `json:"output" yaml:"output"`
    }
    
    config := Config{
        Database: DatabaseConfig{
            Host:     "localhost",
            Port:     5432,
            Name:     "myapp",
            Username: "user",
            Password: "pass",
        },
        Server: ServerConfig{
            Host: "0.0.0.0",
            Port: 8080,
            Mode: "production",
        },
        Logging: LoggingConfig{
            Level:  "info",
            Format: "json",
            Output: "stdout",
        },
    }
    
    // Marshal to JSON
    configJSON, err := json.MarshalIndent(config, "", "  ")
    if err != nil {
        fmt.Printf("Error marshaling config: %v\n", err)
        return
    }
    
    fmt.Printf("Config JSON:\n%s\n", configJSON)
    
    // API response tags
    type APIResponse struct {
        Success bool        `json:"success"`
        Message string      `json:"message"`
        Data    interface{} `json:"data,omitempty"`
        Errors  []string    `json:"errors,omitempty"`
        Meta    ResponseMeta `json:"meta,omitempty"`
    }
    
    type ResponseMeta struct {
        RequestID string `json:"request_id"`
        Timestamp string `json:"timestamp"`
        Version   string `json:"version"`
    }
    
    type UserResponse struct {
        ID       int    `json:"id"`
        Username string `json:"username"`
        Email    string `json:"email"`
        Created  string `json:"created"`
    }
    
    // Success response
    successResponse := APIResponse{
        Success: true,
        Message: "User created successfully",
        Data: UserResponse{
            ID:       1,
            Username: "johndoe",
            Email:    "john@example.com",
            Created:  "2023-01-01T00:00:00Z",
        },
        Meta: ResponseMeta{
            RequestID: "req-123",
            Timestamp: time.Now().Format(time.RFC3339),
            Version:   "v1.0",
        },
    }
    
    successJSON, err := json.Marshal(successResponse)
    if err != nil {
        fmt.Printf("Error marshaling success response: %v\n", err)
        return
    }
    
    fmt.Printf("Success Response: %s\n", successJSON)
    
    // Error response
    errorResponse := APIResponse{
        Success: false,
        Message: "Validation failed",
        Errors:  []string{"Username is required", "Email is invalid"},
        Meta: ResponseMeta{
            RequestID: "req-456",
            Timestamp: time.Now().Format(time.RFC3339),
            Version:   "v1.0",
        },
    }
    
    errorJSON, err := json.Marshal(errorResponse)
    if err != nil {
        fmt.Printf("Error marshaling error response: %v\n", err)
        return
    }
    
    fmt.Printf("Error Response: %s\n", errorJSON)
    
    // Field mapping tags
    type LegacyUser struct {
        UserID    int    `json:"user_id" db:"user_id"`
        UserName  string `json:"user_name" db:"username"`
        UserEmail string `json:"user_email" db:"email"`
        UserAge   int    `json:"user_age" db:"age"`
    }
    
    type ModernUser struct {
        ID    int    `json:"id" db:"user_id"`
        Name  string `json:"name" db:"username"`
        Email string `json:"email" db:"email"`
        Age   int    `json:"age" db:"age"`
    }
    
    // Convert between legacy and modern format
    legacyUser := LegacyUser{
        UserID:    1,
        UserName:  "johndoe",
        UserEmail: "john@example.com",
        UserAge:   30,
    }
    
    // Convert to modern format
    modernUser := ModernUser{
        ID:    legacyUser.UserID,
        Name:  legacyUser.UserName,
        Email: legacyUser.UserEmail,
        Age:   legacyUser.UserAge,
    }
    
    fmt.Printf("Legacy: %+v\n", legacyUser)
    fmt.Printf("Modern: %+v\n", modernUser)
    
    // Conditional serialization
    type ProductInfo struct {
        ID          int      `json:"id"`
        Name        string   `json:"name"`
        Price       float64   `json:"price"`
        Description string   `json:"description,omitempty"`
        InStock     bool      `json:"in_stock"`
        Discount    *float64  `json:"discount,omitempty"` // Pointer to allow nil
        Tags        []string  `json:"tags,omitempty"`
        Metadata    map[string]interface{} `json:"metadata,omitempty"`
    }
    
    // Product with discount
    productWithDiscount := ProductInfo{
        ID:          1001,
        Name:        "Laptop",
        Price:       999.99,
        Description: "High-performance laptop",
        InStock:     true,
        Discount:    pointerToFloat64(10.0), // 10% discount
        Tags:        []string{"electronics", "computer"},
        Metadata: map[string]interface{}{
            "brand": "Dell",
            "model": "XPS 15",
        },
    }
    
    // Product without discount
    productWithoutDiscount := ProductInfo{
        ID:      1002,
        Name:    "Mouse",
        Price:   29.99,
        InStock: true,
        Discount: nil, // No discount
    }
    
    productWithDiscountJSON, _ := json.MarshalIndent(productWithDiscount, "", "  ")
    productWithoutDiscountJSON, _ := json.MarshalIndent(productWithoutDiscount, "", "  ")
    
    fmt.Printf("Product with discount:\n%s\n", productWithDiscountJSON)
    fmt.Printf("Product without discount:\n%s\n", productWithoutDiscountJSON)
}

func pointerToFloat64(f float64) *float64 {
    return &f
}
```

## Summary

Go structs provide:

**Struct Basics:**
- Collection of named fields
- Value type by default
- Can be used with pointers
- Support for zero values
- Memory-efficient data organization

**Struct Methods:**
- Value receivers (read-only)
- Pointer receivers (modify struct)
- Method promotion with embedding
- Interface implementation
- Method chaining

**Struct Composition:**
- Embedding for code reuse
- Interface composition
- Pointer composition
- Anonymous struct fields
- Multiple inheritance patterns

**Struct Tags:**
- JSON serialization tags
- Database field mapping
- XML serialization
- Custom validation tags
- Configuration management

**Key Features:**
- Type safety
- Memory efficiency
- Encapsulation
- Method association
- Interface implementation

**Best Practices:**
- Use pointers for modification
- Embed for composition
- Tags for serialization
- Zero value initialization
- Clear field naming

**Common Use Cases:**
- Data modeling
- API responses
- Configuration
- Database entities
- Domain objects

Go structs provide a powerful, flexible, and type-safe way to organize and manipulate data, with excellent support for methods, composition, and serialization through tags.
