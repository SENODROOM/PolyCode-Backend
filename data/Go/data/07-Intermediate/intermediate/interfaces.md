# Go Interfaces

## Interface Basics

### Interface Definition and Implementation
```go
package main

import "fmt"

func main() {
    // Basic interface definition
    type Speaker interface {
        Speak() string
    }
    
    // Implementations
    type Dog struct {
        Name string
        Breed string
    }
    
    func (d Dog) Speak() string {
        return fmt.Sprintf("%s barks", d.Name)
    }
    
    type Cat struct {
        Name string
        Color string
    }
    
    func (c Cat) Speak() string {
        return fmt.Sprintf("%s meows", c.Name)
    }
    
    type Person struct {
        Name string
        Language string
    }
    
    func (p Person) Speak() string {
        return fmt.Sprintf("%s says hello", p.Name)
    }
    
    // Using interfaces
    var speaker Speaker
    
    // Can assign any type that implements Speak()
    speaker = Dog{Name: "Buddy", Breed: "Golden Retriever"}
    fmt.Printf("Dog: %s\n", speaker.Speak())
    
    speaker = Cat{Name: "Whiskers", Color: "Orange"}
    fmt.Printf("Cat: %s\n", speaker.Speak())
    
    speaker = Person{Name: "John", Language: "English"}
    fmt.Printf("Person: %s\n", speaker.Speak())
    
    // Interface with multiple methods
    type Animal interface {
        Speak() string
        Eat() string
        Sleep() string
    }
    
    type Lion struct {
        Name string
        Age  int
    }
    
    func (l Lion) Speak() string {
        return fmt.Sprintf("%s roars", l.Name)
    }
    
    func (l Lion) Eat() string {
        return fmt.Sprintf("%s is eating meat", l.Name)
    }
    
    func (l Lion) Sleep() string {
        return fmt.Sprintf("%s is sleeping", l.Name)
    }
    
    var animal Animal
    animal = Lion{Name: "Simba", Age: 5}
    
    fmt.Printf("Lion speaks: %s\n", animal.Speak())
    fmt.Printf("Lion eats: %s\n", animal.Eat())
    fmt.Printf("Lion sleeps: %s\n", animal.Sleep())
    
    // Interface composition
    type Walker interface {
        Walk() string
    }
    
    type Runner interface {
        Run() string
    }
    
    type Athlete interface {
        Walker
        Runner
    }
    
    type Human struct {
        Name string
        Age  int
    }
    
    func (h Human) Walk() string {
        return fmt.Sprintf("%s is walking", h.Name)
    }
    
    func (h Human) Run() string {
        return fmt.Sprintf("%s is running", h.Name)
    }
    
    var athlete Athlete
    athlete = Human{Name: "Alice", Age: 25}
    
    fmt.Printf("Athlete walks: %s\n", athlete.Walk())
    fmt.Printf("Athlete runs: %s\n", athlete.Run())
    
    // Empty interface
    var anything interface{}
    
    anything = 42
    fmt.Printf("Integer: %v (type: %T)\n", anything, anything)
    
    anything = "Hello, World!"
    fmt.Printf("String: %v (type: %T)\n", anything, anything)
    
    anything = []int{1, 2, 3}
    fmt.Printf("Slice: %v (type: %T)\n", anything, anything)
    
    // Type assertions
    var value interface{} = "Hello, Go!"
    
    if str, ok := value.(string); ok {
        fmt.Printf("String value: %s\n", str)
    }
    
    if num, ok := value.(int); ok {
        fmt.Printf("Integer value: %d\n", num)
    } else {
        fmt.Printf("Not an integer\n")
    }
    
    // Type switch
    var data interface{} = 3.14
    
    switch v := data.(type) {
    case int:
        fmt.Printf("Integer: %d\n", v)
    case float64:
        fmt.Printf("Float64: %f\n", v)
    case string:
        fmt.Printf("String: %s\n", v)
    case []int:
        fmt.Printf("Integer slice: %v\n", v)
    default:
        fmt.Printf("Unknown type: %T\n", v)
    }
    
    // Interface with pointer receiver
    type Counter interface {
        Increment()
        GetValue() int
        Reset()
    }
    
    type SimpleCounter struct {
        value int
    }
    
    func (sc *SimpleCounter) Increment() {
        sc.value++
    }
    
    func (sc SimpleCounter) GetValue() int {
        return sc.value
    }
    
    func (sc *SimpleCounter) Reset() {
        sc.value = 0
    }
    
    var counter Counter
    counter = &SimpleCounter{value: 0} // Must use pointer for methods that modify
    
    counter.Increment()
    counter.Increment()
    fmt.Printf("Counter value: %d\n", counter.GetValue())
    
    counter.Reset()
    fmt.Printf("Counter after reset: %d\n", counter.GetValue())
    
    // Interface in function parameters
    func makeSound(s Speaker) {
        fmt.Printf("Sound: %s\n", s.Speak())
    }
    
    makeSound(Dog{Name: "Max"})
    makeSound(Cat{Name: "Luna"})
    makeSound(Person{Name: "Jane"})
    
    // Interface as return type
    func createSpeaker(kind string) Speaker {
        switch kind {
        case "dog":
            return Dog{Name: "Buddy"}
        case "cat":
            return Cat{Name: "Whiskers"}
        case "person":
            return Person{Name: "John"}
        default:
            return nil
        }
    }
    
    speaker1 := createSpeaker("dog")
    speaker2 := createSpeaker("cat")
    speaker3 := createSpeaker("person")
    
    if speaker1 != nil {
        fmt.Printf("Created speaker: %s\n", speaker1.Speak())
    }
    if speaker2 != nil {
        fmt.Printf("Created speaker: %s\n", speaker2.Speak())
    }
    if speaker3 != nil {
        fmt.Printf("Created speaker: %s\n", speaker3.Speak())
    }
    
    // Interface slice
    speakers := []Speaker{
        Dog{Name: "Rex"},
        Cat{Name: "Mittens"},
        Person{Name: "Bob"},
    }
    
    fmt.Println("All speakers:")
    for i, s := range speakers {
        fmt.Printf("%d: %s\n", i+1, s.Speak())
    }
    
    // Interface comparison
    var speaker1 Speaker = Dog{Name: "Buddy"}
    var speaker2 Speaker = Dog{Name: "Buddy"}
    var speaker3 Speaker = Cat{Name: "Whiskers"}
    
    // Interfaces can be compared if underlying types are comparable
    // and have the same dynamic type and value
    fmt.Printf("speaker1 == speaker2: %t\n", speaker1 == speaker2)
    fmt.Printf("speaker1 == speaker3: %t\n", speaker1 == speaker3)
    
    // Nil interface
    var nilSpeaker Speaker
    fmt.Printf("Nil speaker: %v (is nil: %t)\n", nilSpeaker, nilSpeaker == nil)
    
    // Interface with nil concrete value
    var dog *Dog
    var dogSpeaker Speaker = dog
    fmt.Printf("Dog speaker with nil value: %v (is nil: %t)\n", dogSpeaker, dogSpeaker == nil)
}
```

### Standard Library Interfaces
```go
package main

import (
    "fmt"
    "io"
    "os"
    "strings"
    "bufio"
    "time"
)

func main() {
    // io.Reader interface
    func demonstrateReader() {
        fmt.Println("=== io.Reader Demo ===")
        
        // String implements io.Reader
        reader := strings.NewReader("Hello, World!")
        
        buffer := make([]byte, 5)
        
        for {
            n, err := reader.Read(buffer)
            if err != nil {
                if err == io.EOF {
                    fmt.Println("Reached end of file")
                } else {
                    fmt.Printf("Error: %v\n", err)
                }
                break
            }
            
            fmt.Printf("Read %d bytes: %s\n", n, buffer[:n])
        }
    }
    
    // io.Writer interface
    func demonstrateWriter() {
        fmt.Println("\n=== io.Writer Demo ===")
        
        var builder strings.Builder
        var writer io.Writer = &builder
        
        writer.Write([]byte("Hello, "))
        writer.Write([]byte("World!"))
        writer.Write([]byte("\n"))
        
        fmt.Printf("Writer content: %s", builder.String())
    }
    
    // io.ReadWriter interface (composition)
    func demonstrateReadWriter() {
        fmt.Println("\n=== io.ReadWriter Demo ===")
        
        var buffer strings.Builder
        readWriter := struct {
            io.Reader
            io.Writer
        }{
            Reader: strings.NewReader("Hello from reader"),
            Writer: &buffer,
        }
        
        // Read from reader
        buf := make([]byte, 20)
        n, _ := readWriter.Read(buf)
        fmt.Printf("Read: %s\n", buf[:n])
        
        // Write to writer
        readWriter.Write([]byte("Hello from writer"))
        fmt.Printf("Writer content: %s\n", buffer.String())
    }
    
    // fmt.Stringer interface
    type Person struct {
        Name string
        Age  int
    }
    
    func (p Person) String() string {
        return fmt.Sprintf("%s (%d years old)", p.Name, p.Age)
    }
    
    func demonstrateStringer() {
        fmt.Println("\n=== fmt.Stringer Demo ===")
        
        person := Person{Name: "John Doe", Age: 30}
        fmt.Printf("Person: %s\n", person) // Uses String() method
        fmt.Printf("Person with %+v: %+v\n", person, person)
    }
    
    // error interface
    type CustomError struct {
        Code    int
        Message string
    }
    
    func (ce CustomError) Error() string {
        return fmt.Sprintf("Error %d: %s", ce.Code, ce.Message)
    }
    
    func demonstrateError() {
        fmt.Println("\n=== error Interface Demo ===")
        
        var err error
        
        err = CustomError{Code: 404, Message: "Not Found"}
        fmt.Printf("Error: %v\n", err)
        
        // Type assertion on error
        if customErr, ok := err.(CustomError); ok {
            fmt.Printf("Error code: %d\n", customErr.Code)
        }
    }
    
    // sort.Interface
    type People []Person
    
    func (p People) Len() int {
        return len(p)
    }
    
    func (p People) Less(i, j int) bool {
        return p[i].Age < p[j].Age
    }
    
    func (p People) Swap(i, j int) {
        p[i], p[j] = p[j], p[i]
    }
    
    func demonstrateSort() {
        fmt.Println("\n=== sort.Interface Demo ===")
        
        people := People{
            {Name: "Alice", Age: 25},
            {Name: "Bob", Age: 30},
            {Name: "Charlie", Age: 20},
        }
        
        fmt.Printf("Before sort: %v\n", people)
        
        // Would need to import sort package
        // sort.Sort(people)
        
        fmt.Printf("After sort: %v\n", people)
    }
    
    // json.Marshaler and json.Unmarshaler
    type Product struct {
        ID    int
        Name  string
        Price float64
    }
    
    func (p Product) MarshalJSON() ([]byte, error) {
        return []byte(fmt.Sprintf(`{"id":%d,"name":"%s","price":%.2f}`, 
            p.ID, p.Name, p.Price)), nil
    }
    
    func demonstrateJSON() {
        fmt.Println("\n=== JSON Interfaces Demo ===")
        
        product := Product{ID: 1, Name: "Laptop", Price: 999.99}
        
        // Would need to import json package
        // jsonData, _ := json.Marshal(product)
        // fmt.Printf("JSON: %s\n", jsonData)
        
        fmt.Printf("Custom JSON: %s\n", string(product.MarshalJSON()))
    }
    
    // time.Duration String() method
    func demonstrateTime() {
        fmt.Println("\n=== time.Duration Demo ===")
        
        duration := 2*time.Hour + 30*time.Minute + 45*time.Second
        fmt.Printf("Duration: %s\n", duration) // Uses String() method
    }
    
    // bufio.Scanner
    func demonstrateScanner() {
        fmt.Println("\n=== bufio.Scanner Demo ===")
        
        text := "Hello World\nGo Programming\nInterface Examples"
        scanner := bufio.NewScanner(strings.NewReader(text))
        
        for scanner.Scan() {
            fmt.Printf("Line: %s\n", scanner.Text())
        }
        
        if err := scanner.Err(); err != nil {
            fmt.Printf("Scanner error: %v\n", err)
        }
    }
    
    // File operations with interfaces
    func demonstrateFileInterfaces() {
        fmt.Println("\n=== File Interfaces Demo ===")
        
        // Create a temporary file
        file, err := os.CreateTemp("", "interface_example_*.txt")
        if err != nil {
            fmt.Printf("Error creating temp file: %v\n", err)
            return
        }
        defer os.Remove(file.Name())
        defer file.Close()
        
        // Use as io.Writer
        writer := io.Writer(file)
        writer.Write([]byte("Hello, File!"))
        
        // Reset file position
        file.Seek(0, 0)
        
        // Use as io.Reader
        reader := io.Reader(file)
        buffer := make([]byte, 1024)
        n, err := reader.Read(buffer)
        if err != nil && err != io.EOF {
            fmt.Printf("Error reading file: %v\n", err)
            return
        }
        
        fmt.Printf("File content: %s\n", buffer[:n])
    }
    
    // Interface as function parameter
    func processReader(r io.Reader) {
        buffer := make([]byte, 1024)
        n, err := r.Read(buffer)
        if err != nil && err != io.EOF {
            fmt.Printf("Error reading: %v\n", err)
            return
        }
        
        fmt.Printf("Processed: %s\n", buffer[:n])
    }
    
    func demonstrateInterfaceParameter() {
        fmt.Println("\n=== Interface Parameter Demo ===")
        
        // Process different readers
        processReader(strings.NewReader("Hello from string"))
        
        file, _ := os.CreateTemp("", "param_test_*.txt")
        file.WriteString("Hello from file")
        file.Seek(0, 0)
        processReader(file)
        file.Close()
        os.Remove(file.Name())
    }
    
    // Interface composition in standard library
    func demonstrateInterfaceComposition() {
        fmt.Println("\n=== Interface Composition Demo ===")
        
        // io.ReadWriteCloser = io.Reader + io.Writer + io.Closer
        var rwc io.ReadWriteCloser
        
        file, err := os.CreateTemp("", "composition_test_*.txt")
        if err != nil {
            fmt.Printf("Error creating file: %v\n", err)
            return
        }
        defer os.Remove(file.Name())
        
        rwc = file
        
        // Use as writer
        rwc.Write([]byte("Hello, Composition!"))
        
        // Use as reader
        file.Seek(0, 0)
        buffer := make([]byte, 100)
        n, _ := rwc.Read(buffer)
        fmt.Printf("Read from composition: %s\n", buffer[:n])
        
        // Use as closer
        rwc.Close()
    }
    
    // Run all demonstrations
    demonstrateReader()
    demonstrateWriter()
    demonstrateReadWriter()
    demonstrateStringer()
    demonstrateError()
    demonstrateSort()
    demonstrateJSON()
    demonstrateTime()
    demonstrateScanner()
    demonstrateFileInterfaces()
    demonstrateInterfaceParameter()
    demonstrateInterfaceComposition()
}
```

## Advanced Interfaces

### Interface Design Patterns
```go
package main

import (
    "fmt"
    "context"
    "time"
)

func main() {
    // Strategy Pattern with interfaces
    func demonstrateStrategy() {
        fmt.Println("=== Strategy Pattern ===")
        
        type PaymentStrategy interface {
            Pay(amount float64) string
        }
        
        type CreditCard struct {
            CardNumber string
            Name       string
        }
        
        func (cc CreditCard) Pay(amount float64) string {
            return fmt.Sprintf("Paid $%.2f with credit card ending in %s", 
                amount, cc.CardNumber[len(cc.CardNumber)-4:])
        }
        
        type PayPal struct {
            Email string
        }
        
        func (pp PayPal) Pay(amount float64) string {
            return fmt.Sprintf("Paid $%.2f with PayPal account %s", amount, pp.Email)
        }
        
        type BankTransfer struct {
            AccountNumber string
            BankName      string
        }
        
        func (bt BankTransfer) Pay(amount float64) string {
            return fmt.Sprintf("Paid $%.2f via bank transfer to %s", amount, bt.AccountNumber)
        }
        
        // Process payment with different strategies
        processPayment := func(strategy PaymentStrategy, amount float64) {
            fmt.Printf("Processing: %s\n", strategy.Pay(amount))
        }
        
        creditCard := CreditCard{CardNumber: "1234567890123456", Name: "John Doe"}
        payPal := PayPal{Email: "john@example.com"}
        bankTransfer := BankTransfer{AccountNumber: "9876543210", BankName: "First Bank"}
        
        processPayment(creditCard, 100.00)
        processPayment(payPal, 50.00)
        processPayment(bankTransfer, 200.00)
    }
    
    // Repository Pattern
    func demonstrateRepository() {
        fmt.Println("\n=== Repository Pattern ===")
        
        type User struct {
            ID    int
            Name  string
            Email string
        }
        
        type UserRepository interface {
            Create(user User) error
            GetByID(id int) (User, error)
            Update(user User) error
            Delete(id int) error
            GetAll() ([]User, error)
        }
        
        // In-memory implementation
        type InMemoryUserRepository struct {
            users map[int]User
            nextID int
        }
        
        func NewInMemoryUserRepository() *InMemoryUserRepository {
            return &InMemoryUserRepository{
                users: make(map[int]User),
                nextID: 1,
            }
        }
        
        func (repo *InMemoryUserRepository) Create(user User) error {
            user.ID = repo.nextID
            repo.users[repo.nextID] = user
            repo.nextID++
            return nil
        }
        
        func (repo *InMemoryUserRepository) GetByID(id int) (User, error) {
            user, exists := repo.users[id]
            if !exists {
                return User{}, fmt.Errorf("user not found")
            }
            return user, nil
        }
        
        func (repo *InMemoryUserRepository) Update(user User) error {
            if _, exists := repo.users[user.ID]; !exists {
                return fmt.Errorf("user not found")
            }
            repo.users[user.ID] = user
            return nil
        }
        
        func (repo *InMemoryUserRepository) Delete(id int) error {
            if _, exists := repo.users[id]; !exists {
                return fmt.Errorf("user not found")
            }
            delete(repo.users, id)
            return nil
        }
        
        func (repo *InMemoryUserRepository) GetAll() ([]User, error) {
            users := make([]User, 0, len(repo.users))
            for _, user := range repo.users {
                users = append(users, user)
            }
            return users, nil
        }
        
        // Use the repository
        var repo UserRepository = NewInMemoryUserRepository()
        
        // Create users
        repo.Create(User{Name: "John Doe", Email: "john@example.com"})
        repo.Create(User{Name: "Jane Smith", Email: "jane@example.com"})
        
        // Get all users
        users, _ := repo.GetAll()
        fmt.Printf("All users: %v\n", users)
        
        // Get user by ID
        user, _ := repo.GetByID(1)
        fmt.Printf("User 1: %v\n", user)
        
        // Update user
        user.Name = "John Updated"
        repo.Update(user)
        
        updatedUser, _ := repo.GetByID(1)
        fmt.Printf("Updated user 1: %v\n", updatedUser)
        
        // Delete user
        repo.Delete(2)
        
        remainingUsers, _ := repo.GetAll()
        fmt.Printf("Remaining users: %v\n", remainingUsers)
    }
    
    // Observer Pattern
    func demonstrateObserver() {
        fmt.Println("\n=== Observer Pattern ===")
        
        type Event interface {
            GetData() interface{}
        }
        
        type Observer interface {
            Notify(event Event)
        }
        
        type Subject interface {
            Attach(observer Observer)
            Detach(observer Observer)
            Notify(event Event)
        }
        
        // Concrete event
        type TemperatureEvent struct {
            Temperature float64
            Location    string
        }
        
        func (te TemperatureEvent) GetData() interface{} {
            return te
        }
        
        // Concrete subject
        type WeatherStation struct {
            observers []Observer
            temperature float64
            location    string
        }
        
        func (ws *WeatherStation) Attach(observer Observer) {
            ws.observers = append(ws.observers, observer)
        }
        
        func (ws *WeatherStation) Detach(observer Observer) {
            for i, obs := range ws.observers {
                if obs == observer {
                    ws.observers = append(ws.observers[:i], ws.observers[i+1:]...)
                    break
                }
            }
        }
        
        func (ws *WeatherStation) Notify(event Event) {
            for _, observer := range ws.observers {
                observer.Notify(event)
            }
        }
        
        func (ws *WeatherStation) SetTemperature(temp float64) {
            ws.temperature = temp
            event := TemperatureEvent{
                Temperature: temp,
                Location:    ws.location,
            }
            ws.Notify(event)
        }
        
        // Concrete observers
        type TemperatureDisplay struct {
            Name string
        }
        
        func (td TemperatureDisplay) Notify(event Event) {
            if tempEvent, ok := event.GetData().(TemperatureEvent); ok {
                fmt.Printf("%s: Temperature changed to %.1f°C in %s\n", 
                    td.Name, tempEvent.Temperature, tempEvent.Location)
            }
        }
        
        type TemperatureLogger struct {
            LogFile string
        }
        
        func (tl TemperatureLogger) Notify(event Event) {
            if tempEvent, ok := event.GetData().(TemperatureEvent); ok {
                fmt.Printf("Logging to %s: %.1f°C at %s\n", 
                    tl.LogFile, tempEvent.Temperature, time.Now().Format(time.RFC3339))
            }
        }
        
        // Use the observer pattern
        station := &WeatherStation{location: "New York"}
        
        display1 := &TemperatureDisplay{Name: "Display 1"}
        display2 := &TemperatureDisplay{Name: "Display 2"}
        logger := &TemperatureLogger{LogFile: "temp.log"}
        
        station.Attach(display1)
        station.Attach(display2)
        station.Attach(logger)
        
        station.SetTemperature(25.5)
        station.SetTemperature(26.0)
        
        station.Detach(display2)
        station.SetTemperature(24.8)
    }
    
    // Command Pattern
    func demonstrateCommand() {
        fmt.Println("\n=== Command Pattern ===")
        
        type Command interface {
            Execute() error
            Undo() error
        }
        
        type Light struct {
            IsOn bool
        }
        
        func (l *Light) TurnOn() {
            l.IsOn = true
            fmt.Println("Light is ON")
        }
        
        func (l *Light) TurnOff() {
            l.IsOn = false
            fmt.Println("Light is OFF")
        }
        
        type LightOnCommand struct {
            light *Light
        }
        
        func (loc LightOnCommand) Execute() error {
            loc.light.TurnOn()
            return nil
        }
        
        func (loc LightOnCommand) Undo() error {
            loc.light.TurnOff()
            return nil
        }
        
        type LightOffCommand struct {
            light *Light
        }
        
        func (loc LightOffCommand) Execute() error {
            loc.light.TurnOff()
            return nil
        }
        
        func (loc LightOffCommand) Undo() error {
            loc.light.TurnOn()
            return nil
        }
        
        type RemoteControl struct {
            command Command
            history []Command
        }
        
        func (rc *RemoteControl) SetCommand(command Command) {
            rc.command = command
        }
        
        func (rc *RemoteControl) PressButton() error {
            if rc.command == nil {
                return fmt.Errorf("no command set")
            }
            
            err := rc.command.Execute()
            if err == nil {
                rc.history = append(rc.history, rc.command)
            }
            return err
        }
        
        func (rc *RemoteControl) PressUndo() error {
            if len(rc.history) == 0 {
                return fmt.Errorf("no commands to undo")
            }
            
            lastCommand := rc.history[len(rc.history)-1]
            rc.history = rc.history[:len(rc.history)-1]
            return lastCommand.Undo()
        }
        
        // Use command pattern
        light := &Light{IsOn: false}
        remote := &RemoteControl{}
        
        onCommand := LightOnCommand{light: light}
        offCommand := LightOffCommand{light: light}
        
        remote.SetCommand(onCommand)
        remote.PressButton()
        
        remote.SetCommand(offCommand)
        remote.PressButton()
        
        remote.PressUndo()
        remote.PressUndo()
    }
    
    // Factory Pattern with interfaces
    func demonstrateFactory() {
        fmt.Println("\n=== Factory Pattern ===")
        
        type Animal interface {
            MakeSound() string
            Move() string
        }
        
        type Dog struct {
            Breed string
        }
        
        func (d Dog) MakeSound() string {
            return "Woof!"
        }
        
        func (d Dog) Move() string {
            return "Running"
        }
        
        type Cat struct {
            Color string
        }
        
        func (c Cat) MakeSound() string {
            return "Meow!"
        }
        
        func (c Cat) Move() string {
            return "Prowling"
        }
        
        type Bird struct {
            Species string
        }
        
        func (b Bird) MakeSound() string {
            return "Tweet!"
        }
        
        func (b Bird) Move() string {
            return "Flying"
        }
        
        type AnimalFactory interface {
            CreateAnimal(species string) (Animal, error)
        }
        
        type SimpleAnimalFactory struct{}
        
        func (saf SimpleAnimalFactory) CreateAnimal(species string) (Animal, error) {
            switch species {
            case "dog":
                return Dog{Breed: "Golden Retriever"}, nil
            case "cat":
                return Cat{Color: "Orange"}, nil
            case "bird":
                return Bird{Species: "Sparrow"}, nil
            default:
                return nil, fmt.Errorf("unknown species: %s", species)
            }
        }
        
        // Use factory
        factory := SimpleAnimalFactory{}
        
        animals := []string{"dog", "cat", "bird", "fish"}
        
        for _, species := range animals {
            animal, err := factory.CreateAnimal(species)
            if err != nil {
                fmt.Printf("Error creating %s: %v\n", species, err)
                continue
            }
            
            fmt.Printf("%s makes sound: %s, moves: %s\n", 
                species, animal.MakeSound(), animal.Move())
        }
    }
    
    // Adapter Pattern
    func demonstrateAdapter() {
        fmt.Println("\n=== Adapter Pattern ===")
        
        // Legacy interface
        type LegacyPrinter interface {
            Print(text string)
        }
        
        type OldPrinter struct {
            Name string
        }
        
        func (op OldPrinter) Print(text string) {
            fmt.Printf("[%s] %s\n", op.Name, text)
        }
        
        // New interface
        type ModernPrinter interface {
            PrintDocument(doc string)
            ScanDocument() string
        }
        
        // Adapter to make legacy printer work with new interface
        type PrinterAdapter struct {
            legacy LegacyPrinter
        }
        
        func (pa PrinterAdapter) PrintDocument(doc string) {
            pa.legacy.Print(doc)
        }
        
        func (pa PrinterAdapter) ScanDocument() string {
            return "Scanned document (not supported by legacy printer)"
        }
        
        // Use adapter
        oldPrinter := OldPrinter{Name: "Legacy Printer"}
        adapter := PrinterAdapter{legacy: oldPrinter}
        
        var modern ModernPrinter = adapter
        
        modern.PrintDocument("Hello, World!")
        fmt.Printf("Scan result: %s\n", modern.ScanDocument())
    }
    
    // Decorator Pattern
    func demonstrateDecorator() {
        fmt.Println("\n=== Decorator Pattern ===")
        
        type DataSource interface {
            WriteData(data string)
            ReadData() string
        }
        
        type FileDataSource struct {
            filename string
            content  string
        }
        
        func (fds FileDataSource) WriteData(data string) {
            fds.content = data
            fmt.Printf("Writing '%s' to file %s\n", data, fds.filename)
        }
        
        func (fds FileDataSource) ReadData() string {
            fmt.Printf("Reading from file %s\n", fds.filename)
            return fds.content
        }
        
        // Base decorator
        type DataSourceDecorator struct {
            wrappee DataSource
        }
        
        func (dsd DataSourceDecorator) WriteData(data string) {
            dsd.wrappee.WriteData(data)
        }
        
        func (dsd DataSourceDecorator) ReadData() string {
            return dsd.wrappee.ReadData()
        }
        
        // Encryption decorator
        type EncryptionDecorator struct {
            DataSourceDecorator
        }
        
        func NewEncryptionDecorator(source DataSource) *EncryptionDecorator {
            return &EncryptionDecorator{
                DataSourceDecorator: DataSourceDecorator{wrappee: source},
            }
        }
        
        func (ed EncryptionDecorator) WriteData(data string) {
            encrypted := "ENCRYPTED:" + data
            ed.wrappee.WriteData(encrypted)
        }
        
        func (ed EncryptionDecorator) ReadData() string {
            data := ed.wrappee.ReadData()
            if len(data) > 10 && data[:10] == "ENCRYPTED:" {
                return data[10:] // Remove encryption prefix
            }
            return data
        }
        
        // Compression decorator
        type CompressionDecorator struct {
            DataSourceDecorator
        }
        
        func NewCompressionDecorator(source DataSource) *CompressionDecorator {
            return &CompressionDecorator{
                DataSourceDecorator: DataSourceDecorator{wrappee: source},
            }
        }
        
        func (cd CompressionDecorator) WriteData(data string) {
            compressed := "COMPRESSED:" + data
            cd.wrappee.WriteData(compressed)
        }
        
        func (cd CompressionDecorator) ReadData() string {
            data := cd.wrappee.ReadData()
            if len(data) > 11 && data[:11] == "COMPRESSED:" {
                return data[11:] // Remove compression prefix
            }
            return data
        }
        
        // Use decorators
        source := FileDataSource{filename: "data.txt"}
        
        // Add encryption
        encrypted := NewEncryptionDecorator(&source)
        encrypted.WriteData("Secret data")
        fmt.Printf("Read encrypted: %s\n", encrypted.ReadData())
        
        // Add compression to encrypted
        compressed := NewCompressionDecorator(encrypted)
        compressed.WriteData("More secret data")
        fmt.Printf("Read compressed and encrypted: %s\n", compressed.ReadData())
    }
    
    // Run all demonstrations
    demonstrateStrategy()
    demonstrateRepository()
    demonstrateObserver()
    demonstrateCommand()
    demonstrateFactory()
    demonstrateAdapter()
    demonstrateDecorator()
}
```

## Interface Best Practices

### Interface Design Guidelines
```go
package main

import (
    "fmt"
    "context"
    "io"
)

func main() {
    // 1. Keep interfaces small and focused
    func demonstrateSmallInterfaces() {
        fmt.Println("=== Small Interfaces ===")
        
        // Good: Small, focused interfaces
        type Reader interface {
            Read([]byte) (int, error)
        }
        
        type Writer interface {
            Write([]byte) (int, error)
        }
        
        type Closer interface {
            Close() error
        }
        
        // Compose for larger functionality
        type ReadWriter interface {
            Reader
            Writer
        }
        
        type ReadWriteCloser interface {
            Reader
            Writer
            Closer
        }
        
        fmt.Println("Small interfaces are easier to implement and test")
    }
    
    // 2. Accept interfaces, return concrete types
    func demonstrateAcceptInterfaces() {
        fmt.Println("\n=== Accept Interfaces, Return Concrete Types ===")
        
        type Processor interface {
            Process(data string) string
        }
        
        type TextProcessor struct {
            Name string
        }
        
        func (tp TextProcessor) Process(data string) string {
            return fmt.Sprintf("[%s] Processed: %s", tp.Name, data)
        }
        
        // Function accepts interface
        func processData(processor Processor, data string) {
            result := processor.Process(data)
            fmt.Printf("Result: %s\n", result)
        }
        
        // Function returns concrete type
        func createProcessor(name string) TextProcessor {
            return TextProcessor{Name: name}
        }
        
        processor := createProcessor("Text Processor")
        processData(processor, "Hello, World!")
    }
    
    // 3. Design for behavior, not data
    func demonstrateBehaviorDesign() {
        fmt.Println("\n=== Design for Behavior ===")
        
        // Bad: Data-focused interface
        type UserData interface {
            GetName() string
            GetEmail() string
            GetAge() int
        }
        
        // Good: Behavior-focused interface
        type User interface {
            Authenticate(password string) bool
            UpdateProfile(profile UserProfile) error
            SendNotification(message string) error
        }
        
        type UserProfile struct {
            Name  string
            Email string
            Age   int
        }
        
        type AccountUser struct {
            profile UserProfile
            hashedPassword string
        }
        
        func (au AccountUser) Authenticate(password string) bool {
            // Simplified authentication
            return password == "secret123"
        }
        
        func (au AccountUser) UpdateProfile(profile UserProfile) error {
            au.profile = profile
            fmt.Printf("Profile updated for %s\n", profile.Name)
            return nil
        }
        
        func (au AccountUser) SendNotification(message string) error {
            fmt.Printf("Notification sent to %s: %s\n", au.profile.Email, message)
            return nil
        }
        
        var user User = AccountUser{
            profile: UserProfile{Name: "John Doe", Email: "john@example.com", Age: 30},
            hashedPassword: "hashed_secret",
        }
        
        if user.Authenticate("secret123") {
            user.UpdateProfile(UserProfile{Name: "John Updated", Email: "john.updated@example.com", Age: 31})
            user.SendNotification("Your profile has been updated")
        }
    }
    
    // 4. Use interface composition
    func demonstrateComposition() {
        fmt.Println("\n=== Interface Composition ===")
        
        type Reader interface {
            Read([]byte) (int, error)
        }
        
        type Writer interface {
            Write([]byte) (int, error)
        }
        
        type Closer interface {
            Close() error
        }
        
        type Flusher interface {
            Flush() error
        }
        
        // Compose interfaces as needed
        type ReadWriter interface {
            Reader
            Writer
        }
        
        type WriteCloser interface {
            Writer
            Closer
        }
        
        type ReadWriteCloser interface {
            Reader
            Writer
            Closer
        }
        
        type ReadWriteFlusher interface {
            Reader
            Writer
            Flusher
        }
        
        type ReadWriteCloserFlusher interface {
            Reader
            Writer
            Closer
            Flusher
        }
        
        fmt.Println("Compose interfaces to create specific functionality")
    }
    
    // 5. Use context.Context for cancellation and deadlines
    func demonstrateContext() {
        fmt.Println("\n=== Context Usage ===")
        
        type Worker interface {
            DoWork(ctx context.Context, data string) error
        }
        
        type SlowWorker struct {
            Name string
        }
        
        func (sw SlowWorker) DoWork(ctx context.Context, data string) error {
            select {
            case <-ctx.Done():
                return ctx.Err()
            case <-time.After(2 * time.Second):
                fmt.Printf("[%s] Work completed: %s\n", sw.Name, data)
                return nil
            }
        }
        
        worker := SlowWorker{Name: "Slow Worker"}
        
        // Work with context
        ctx, cancel := context.WithTimeout(context.Background(), 1*time.Second)
        defer cancel()
        
        err := worker.DoWork(ctx, "Important data")
        if err != nil {
            fmt.Printf("Work failed: %v\n", err)
        }
        
        // Work without timeout
        ctx2 := context.Background()
        err = worker.DoWork(ctx2, "Less important data")
        if err != nil {
            fmt.Printf("Work failed: %v\n", err)
        }
    }
    
    // 6. Handle nil interface values properly
    func demonstrateNilHandling() {
        fmt.Println("\n=== Nil Interface Handling ===")
        
        type Logger interface {
            Log(message string)
        }
        
        type ConsoleLogger struct{}
        
        func (cl ConsoleLogger) Log(message string) {
            fmt.Printf("LOG: %s\n", message)
        }
        
        func processWithLogger(logger Logger, data string) {
            // Always check for nil
            if logger == nil {
                fmt.Printf("Processing %s (no logging)\n", data)
                return
            }
            
            logger.Log(fmt.Sprintf("Processing %s", data))
            fmt.Printf("Processed %s\n", data)
        }
        
        // With logger
        var logger Logger = ConsoleLogger{}
        processWithLogger(logger, "data1")
        
        // Without logger
        var nilLogger Logger
        processWithLogger(nilLogger, "data2")
        
        // Interface with nil concrete value
        var pointerLogger *ConsoleLogger
        var interfaceLogger Logger = pointerLogger
        
        fmt.Printf("Interface is nil: %t\n", interfaceLogger == nil)
        fmt.Printf("Interface value is nil: %t\n", interfaceLogger == (*ConsoleLogger)(nil))
    }
    
    // 7. Use type assertions carefully
    func demonstrateTypeAssertions() {
        fmt.Println("\n=== Type Assertions ===")
        
        func processValue(value interface{}) {
            // Good: Use comma-ok for safe assertions
            if str, ok := value.(string); ok {
                fmt.Printf("String: %s\n", str)
                return
            }
            
            if num, ok := value.(int); ok {
                fmt.Printf("Integer: %d\n", num)
                return
            }
            
            if slice, ok := value.([]string); ok {
                fmt.Printf("String slice: %v\n", slice)
                return
            }
            
            // Use type switch for multiple possibilities
            switch v := value.(type) {
            case float64:
                fmt.Printf("Float64: %f\n", v)
            case bool:
                fmt.Printf("Boolean: %t\n", v)
            case map[string]int:
                fmt.Printf("Map: %v\n", v)
            default:
                fmt.Printf("Unknown type: %T\n", v)
            }
        }
        
        processValue("Hello")
        processValue(42)
        processValue([]string{"a", "b", "c"})
        processValue(3.14)
        processValue(true)
        processValue(map[string]int{"key": 1})
    }
    
    // 8. Design interfaces for testing
    func demonstrateTestableInterfaces() {
        fmt.Println("\n=== Testable Interfaces ===")
        
        type Database interface {
            Get(key string) (string, error)
            Set(key, value string) error
            Delete(key string) error
        }
        
        // Production implementation
        type RealDatabase struct {
            data map[string]string
        }
        
        func NewRealDatabase() *RealDatabase {
            return &RealDatabase{data: make(map[string]string)}
        }
        
        func (rd *RealDatabase) Get(key string) (string, error) {
            value, exists := rd.data[key]
            if !exists {
                return "", fmt.Errorf("key not found")
            }
            return value, nil
        }
        
        func (rd *RealDatabase) Set(key, value string) error {
            rd.data[key] = value
            return nil
        }
        
        func (rd *RealDatabase) Delete(key string) error {
            delete(rd.data, key)
            return nil
        }
        
        // Test implementation
        type MockDatabase struct {
            data map[string]string
            errors map[string]error
        }
        
        func NewMockDatabase() *MockDatabase {
            return &MockDatabase{
                data: make(map[string]string),
                errors: make(map[string]error),
            }
        }
        
        func (md *MockDatabase) Get(key string) (string, error) {
            if err, exists := md.errors[key]; exists {
                return "", err
            }
            return md.data[key], nil
        }
        
        func (md *MockDatabase) Set(key, value string) error {
            if err, exists := md.errors[key]; exists {
                return err
            }
            md.data[key] = value
            return nil
        }
        
        func (md *MockDatabase) Delete(key string) error {
            if err, exists := md.errors[key]; exists {
                return err
            }
            delete(md.data, key)
            return nil
        }
        
        func (md *MockDatabase) SetError(key string, err error) {
            md.errors[key] = err
        }
        
        // Service that uses the interface
        type UserService struct {
            db Database
        }
        
        func NewUserService(db Database) *UserService {
            return &UserService{db: db}
        }
        
        func (us *UserService) GetUser(key string) (string, error) {
            return us.db.Get(key)
        }
        
        func (us *UserService) SaveUser(key, value string) error {
            return us.db.Set(key, value)
        }
        
        // Use with real database
        realDB := NewRealDatabase()
        userService := NewUserService(realDB)
        
        userService.SaveUser("user1", "John Doe")
        user, _ := userService.GetUser("user1")
        fmt.Printf("Real DB user: %s\n", user)
        
        // Use with mock database for testing
        mockDB := NewMockDatabase()
        testService := NewUserService(mockDB)
        
        testService.SaveUser("test1", "Test User")
        testUser, _ := testService.GetUser("test1")
        fmt.Printf("Mock DB user: %s\n", testUser)
        
        // Test error case
        mockDB.SetError("error_key", fmt.Errorf("database error"))
        _, err := testService.GetUser("error_key")
        fmt.Printf("Error case: %v\n", err)
    }
    
    // 9. Avoid interface pollution
    func demonstrateAvoidPollution() {
        fmt.Println("\n=== Avoid Interface Pollution ===")
        
        // Bad: Unnecessary interface
        type Adder interface {
            Add(a, b int) int
        }
        
        type Calculator struct{}
        
        func (c Calculator) Add(a, b int) int {
            return a + b
        }
        
        // Good: Use concrete type when interface isn't needed
        func addNumbers(a, b int) int {
            return a + b
        }
        
        // Only use interface when you need abstraction
        type MathOperation interface {
            Calculate(a, b int) int
        }
        
        type AddOperation struct{}
        
        func (ao AddOperation) Calculate(a, b int) int {
            return a + b
        }
        
        type MultiplyOperation struct{}
        
        func (mo MultiplyOperation) Calculate(a, b int) int {
            return a * b
        }
        
        func performOperation(op MathOperation, a, b int) int {
            return op.Calculate(a, b)
        }
        
        result1 := addNumbers(5, 3)
        fmt.Printf("Direct addition: %d\n", result1)
        
        result2 := performOperation(AddOperation{}, 5, 3)
        fmt.Printf("Interface addition: %d\n", result2)
        
        result3 := performOperation(MultiplyOperation{}, 5, 3)
        fmt.Printf("Interface multiplication: %d\n", result3)
    }
    
    // 10. Document interface contracts
    func demonstrateDocumentation() {
        fmt.Println("\n=== Interface Documentation ===")
        
        // Cache interface with clear documentation
        type Cache interface {
            // Store stores a value with the given key and expiration time.
            // If the key already exists, it overwrites the existing value.
            // Returns an error if the storage fails.
            Store(key string, value interface{}, expiration time.Duration) error
            
            // Retrieve retrieves a value for the given key.
            // Returns the value and a boolean indicating if the key was found.
            // Returns false if the key doesn't exist or has expired.
            Retrieve(key string) (interface{}, bool)
            
            // Delete removes a value for the given key.
            // Returns an error if the deletion fails.
            Delete(key string) error
            
            // Clear removes all entries from the cache.
            // Returns an error if the clear operation fails.
            Clear() error
            
            // Stats returns cache statistics including hit rate, miss rate, and size.
            Stats() CacheStats
        }
        
        type CacheStats struct {
            HitCount  int64
            MissCount int64
            Size      int64
        }
        
        fmt.Println("Well-documented interfaces are easier to use correctly")
    }
    
    // Run all demonstrations
    demonstrateSmallInterfaces()
    demonstrateAcceptInterfaces()
    demonstrateBehaviorDesign()
    demonstrateComposition()
    demonstrateContext()
    demonstrateNilHandling()
    demonstrateTypeAssertions()
    demonstrateTestableInterfaces()
    demonstrateAvoidPollution()
    demonstrateDocumentation()
}
```

## Summary

Go interfaces provide:

**Interface Basics:**
- Method sets without implementation
- Implicit implementation
- Type safety and polymorphism
- Empty interface for any type
- Type assertions and switches

**Standard Library Interfaces:**
- io.Reader/Writer for I/O operations
- fmt.Stringer for string representation
- error interface for error handling
- sort.Interface for sorting
- json.Marshaler/Unmarshaler for JSON

**Advanced Interface Patterns:**
- Strategy pattern for algorithms
- Repository pattern for data access
- Observer pattern for event handling
- Command pattern for operations
- Factory pattern for object creation
- Adapter pattern for compatibility
- Decorator pattern for enhancement

**Interface Design Principles:**
- Small, focused interfaces
- Accept interfaces, return concrete types
- Design for behavior, not data
- Use composition over inheritance
- Handle nil values properly
- Design for testability
- Avoid interface pollution
- Document contracts clearly

**Key Features:**
- Implicit implementation
- Type safety
- Polymorphism
- Composition
- Testing support
- Decoupling

**Best Practices:**
- Keep interfaces small
- Use composition
- Design for behavior
- Test with interfaces
- Document contracts
- Handle nil values
- Avoid over-abstraction

**Common Use Cases:**
- Dependency injection
- Testing and mocking
- Plugin systems
- API design
- Data access layers
- Event handling

Go interfaces provide a powerful, flexible, and type-safe way to achieve polymorphism and decoupling while maintaining simplicity and performance.
