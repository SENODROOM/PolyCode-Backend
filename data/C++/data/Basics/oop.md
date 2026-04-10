# C++ Object-Oriented Programming

## Classes and Objects

### Basic Class Definition
```cpp
#include <iostream>
#include <string>

class Person {
private:  // Access specifier
    std::string name;
    int age;
    
public:   // Access specifier
    // Constructor
    Person(const std::string& n, int a) : name(n), age(a) {
        std::cout << "Person created: " << name << std::endl;
    }
    
    // Destructor
    ~Person() {
        std::cout << "Person destroyed: " << name << std::endl;
    }
    
    // Member functions
    void introduce() const {
        std::cout << "Hi, I'm " << name << " and I'm " << age << " years old." << std::endl;
    }
    
    // Getters
    std::string getName() const { return name; }
    int getAge() const { return age; }
    
    // Setters
    void setAge(int new_age) {
        if (new_age > 0) {
            age = new_age;
        }
    }
};

int main() {
    Person person1("John", 25);
    person1.introduce();
    
    Person person2("Alice", 30);
    person2.introduce();
    
    return 0;
}
```

### Access Specifiers
```cpp
class Example {
public:    // Accessible from anywhere
    int public_var;
    
    void public_method() {
        std::cout << "Public method" << std::endl;
    }
    
protected: // Accessible from derived classes
    int protected_var;
    
    void protected_method() {
        std::cout << "Protected method" << std::endl;
    }
    
private:   // Accessible only within the class
    int private_var;
    
    void private_method() {
        std::cout << "Private method" << std::endl;
    }
    
public:
    void demonstrate_access() {
        public_var = 1;        // OK
        protected_var = 2;     // OK
        private_var = 3;       // OK
        
        public_method();      // OK
        protected_method();   // OK
        private_method();     // OK
    }
};
```

## Inheritance

### Single Inheritance
```cpp
#include <iostream>

class Animal {
protected:
    std::string name;
    
public:
    Animal(const std::string& n) : name(n) {}
    
    virtual void speak() {  // Virtual function
        std::cout << name << " makes a sound" << std::endl;
    }
    
    virtual ~Animal() {}  // Virtual destructor
};

class Dog : public Animal {  // Public inheritance
private:
    std::string breed;
    
public:
    Dog(const std::string& n, const std::string& b) 
        : Animal(n), breed(b) {}
    
    void speak() override {  // Override virtual function
        std::cout << name << " barks!" << std::endl;
    }
    
    void fetch() {
        std::cout << name << " fetches the ball" << std::endl;
    }
};

int main() {
    Animal animal("Generic Animal");
    animal.speak();  // Generic Animal makes a sound
    
    Dog dog("Buddy", "Golden Retriever");
    dog.speak();     // Buddy barks!
    dog.fetch();     // Buddy fetches the ball
    
    // Polymorphism
    Animal* animal_ptr = &dog;
    animal_ptr->speak();  // Buddy barks! (dynamic dispatch)
    
    return 0;
}
```

### Multiple Inheritance
```cpp
class Printable {
public:
    virtual void print() const = 0;  // Pure virtual function
    virtual ~Printable() = default;
};

class Serializable {
public:
    virtual std::string serialize() const = 0;
    virtual ~Serializable() = default;
};

class Document : public Printable, public Serializable {
private:
    std::string content;
    
public:
    Document(const std::string& c) : content(c) {}
    
    void print() const override {
        std::cout << "Document: " << content << std::endl;
    }
    
    std::string serialize() const override {
        return "<document>" + content + "</document>";
    }
};
```

## Polymorphism

### Virtual Functions
```cpp
class Shape {
public:
    virtual double area() const = 0;  // Pure virtual function
    virtual void draw() const {
        std::cout << "Drawing a shape" << std::endl;
    }
    virtual ~Shape() = default;  // Virtual destructor
};

class Circle : public Shape {
private:
    double radius;
    
public:
    Circle(double r) : radius(r) {}
    
    double area() const override {
        return 3.14159 * radius * radius;
    }
    
    void draw() const override {
        std::cout << "Drawing a circle with radius " << radius << std::endl;
    }
};

class Rectangle : public Shape {
private:
    double width, height;
    
public:
    Rectangle(double w, double h) : width(w), height(h) {}
    
    double area() const override {
        return width * height;
    }
    
    void draw() const override {
        std::cout << "Drawing a rectangle " << width << "x" << height << std::endl;
    }
};

void process_shape(Shape* shape) {
    shape->draw();
    std::cout << "Area: " << shape->area() << std::endl;
}
```

### Abstract Classes
```cpp
class Vehicle {
protected:
    std::string brand;
    
public:
    Vehicle(const std::string& b) : brand(b) {}
    
    // Pure virtual functions make this an abstract class
    virtual void start() = 0;
    virtual void stop() = 0;
    
    // Regular virtual function with default implementation
    virtual void honk() {
        std::cout << "Vehicle horn" << std::endl;
    }
    
    virtual ~Vehicle() = default;
};

class Car : public Vehicle {
public:
    Car(const std::string& b) : Vehicle(b) {}
    
    void start() override {
        std::cout << brand << " car starting" << std::endl;
    }
    
    void stop() override {
        std::cout << brand << " car stopping" << std::endl;
    }
    
    void honk() override {
        std::cout << brand << " car beeping" << std::endl;
    }
};
```

## Encapsulation

### Private Members and Public Interface
```cpp
class BankAccount {
private:
    std::string account_number;
    double balance;
    std::string pin;
    
    // Private helper method
    bool validate_pin(const std::string& input_pin) const {
        return pin == input_pin;
    }
    
public:
    BankAccount(const std::string& acc_num, double initial_balance, const std::string& p)
        : account_number(acc_num), balance(initial_balance), pin(p) {}
    
    // Public interface
    bool withdraw(double amount, const std::string& input_pin) {
        if (!validate_pin(input_pin)) {
            std::cout << "Invalid PIN" << std::endl;
            return false;
        }
        
        if (amount > balance) {
            std::cout << "Insufficient funds" << std::endl;
            return false;
        }
        
        balance -= amount;
        std::cout << "Withdrawal successful. New balance: " << balance << std::endl;
        return true;
    }
    
    void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            std::cout << "Deposit successful. New balance: " << balance << std::endl;
        }
    }
    
    double get_balance(const std::string& input_pin) const {
        if (validate_pin(input_pin)) {
            return balance;
        }
        return -1;  // Error code
    }
};
```

## Special Member Functions

### Constructors and Destructor
```cpp
class Resource {
private:
    int* data;
    size_t size;
    
public:
    // Default constructor
    Resource() : data(nullptr), size(0) {
        std::cout << "Default constructor" << std::endl;
    }
    
    // Parameterized constructor
    Resource(size_t s) : size(s) {
        data = new int[s];
        std::cout << "Parameterized constructor" << std::endl;
    }
    
    // Copy constructor
    Resource(const Resource& other) : size(other.size) {
        data = new int[size];
        for (size_t i = 0; i < size; ++i) {
            data[i] = other.data[i];
        }
        std::cout << "Copy constructor" << std::endl;
    }
    
    // Move constructor (C++11)
    Resource(Resource&& other) noexcept : data(other.data), size(other.size) {
        other.data = nullptr;
        other.size = 0;
        std::cout << "Move constructor" << std::endl;
    }
    
    // Copy assignment operator
    Resource& operator=(const Resource& other) {
        if (this != &other) {
            delete[] data;
            size = other.size;
            data = new int[size];
            for (size_t i = 0; i < size; ++i) {
                data[i] = other.data[i];
            }
        }
        std::cout << "Copy assignment" << std::endl;
        return *this;
    }
    
    // Move assignment operator (C++11)
    Resource& operator=(Resource&& other) noexcept {
        if (this != &other) {
            delete[] data;
            data = other.data;
            size = other.size;
            other.data = nullptr;
            other.size = 0;
        }
        std::cout << "Move assignment" << std::endl;
        return *this;
    }
    
    // Destructor
    ~Resource() {
        delete[] data;
        std::cout << "Destructor" << std::endl;
    }
};
```

### Rule of Five/Three/Zero
```cpp
// Rule of Zero: Let the compiler generate special member functions
class ModernResource {
private:
    std::vector<int> data;  // Manages its own memory
    
public:
    ModernResource(size_t size) : data(size) {}
    // No need to write destructor, copy/move constructors, or assignment operators
};

// Rule of Three: Need destructor, copy constructor, copy assignment
class LegacyResource {
private:
    int* data;
    size_t size;
    
public:
    LegacyResource(size_t s) : size(s), data(new int[s]) {}
    ~LegacyResource() { delete[] data; }
    
    // Copy constructor
    LegacyResource(const LegacyResource& other) : size(other.size), data(new int[other.size]) {
        std::copy(other.data, other.data + size, data);
    }
    
    // Copy assignment
    LegacyResource& operator=(const LegacyResource& other) {
        if (this != &other) {
            delete[] data;
            size = other.size;
            data = new int[size];
            std::copy(other.data, other.data + size, data);
        }
        return *this;
    }
};
```

## Modern C++ Features

### Smart Pointers
```cpp
#include <memory>

class SmartResource {
private:
    std::unique_ptr<int[]> data;  // Unique ownership
    size_t size;
    
public:
    SmartResource(size_t s) : size(s), data(std::make_unique<int[]>(s)) {}
    
    // No need for destructor - unique_ptr handles cleanup
    // No need for copy/move - unique_ptr is move-only
    
    int& operator[](size_t index) {
        return data[index];
    }
    
    const int& operator[](size_t index) const {
        return data[index];
    }
};

class SharedResource {
private:
    std::shared_ptr<int> data;  // Shared ownership
    int ref_count;
    
public:
    SharedResource(int value) : data(std::make_shared<int>(value)) {}
    
    // Copy is allowed - shares ownership
    // Destructor automatically handled when last shared_ptr is destroyed
};
```

### constexpr and consteval
```cpp
class MathConstants {
public:
    static constexpr double PI = 3.14159265358979323846;
    static constexpr double E = 2.71828182845904523536;
    
    // constexpr function (C++11)
    static constexpr double square(double x) {
        return x * x;
    }
    
    // consteval function (C++20) - must be evaluated at compile time
    consteval static int factorial(int n) {
        return (n <= 1) ? 1 : n * factorial(n - 1);
    }
};
```

## Best Practices
- Use access specifiers to enforce encapsulation
- Make data members private, provide public accessors
- Use virtual functions for runtime polymorphism
- Always declare virtual destructors for base classes
- Follow Rule of Zero/Three/Five for resource management
- Prefer smart pointers over raw pointers
- Use `override` keyword when overriding virtual functions
- Use `final` to prevent further inheritance or overriding
- Keep classes focused on single responsibility
- Use composition over inheritance when appropriate
