# Object-Oriented Programming (OOP) Basics in C#

Object-Oriented Programming is a programming paradigm based on the concept of "objects" which contain data (attributes) and code (methods).

## Core OOP Concepts

### 1. Classes and Objects

A **class** is a blueprint for creating objects. An **object** is an instance of a class.

```csharp
public class Car
{
    // Fields (attributes)
    public string brand;
    public string model;
    public int year;
    public double speed;
    
    // Constructor
    public Car(string brand, string model, int year)
    {
        this.brand = brand;
        this.model = model;
        this.year = year;
        this.speed = 0;
    }
    
    // Methods (behaviors)
    public void Accelerate(double amount)
    {
        speed += amount;
    }
    
    public void Brake(double amount)
    {
        speed -= amount;
        if (speed < 0) speed = 0;
    }
    
    public void DisplayInfo()
    {
        Console.WriteLine($"{year} {brand} {model} - Speed: {speed} km/h");
    }
}
```

### 2. Encapsulation

Encapsulation is the bundling of data and methods that operate on that data within one unit. Use access modifiers to control access:

```csharp
public class BankAccount
{
    // Private field - not accessible from outside
    private double balance;
    
    // Public property - controlled access
    public double Balance
    {
        get { return balance; }
        private set { balance = value; }
    }
    
    public BankAccount(double initialBalance)
    {
        balance = initialBalance;
    }
    
    public void Deposit(double amount)
    {
        if (amount > 0)
        {
            balance += amount;
        }
    }
    
    public bool Withdraw(double amount)
    {
        if (amount > 0 && balance >= amount)
        {
            balance -= amount;
            return true;
        }
        return false;
    }
}
```

### 3. Properties

Properties provide controlled access to private fields:

```csharp
public class Person
{
    private string name;
    private int age;
    
    public string Name
    {
        get { return name; }
        set 
        { 
            if (!string.IsNullOrEmpty(value))
                name = value; 
        }
    }
    
    public int Age
    {
        get { return age; }
        set 
        { 
            if (value >= 0 && value <= 150)
                age = value; 
        }
    }
    
    // Auto-implemented property
    public string Email { get; set; }
    
    // Read-only property
    public bool IsAdult => Age >= 18;
}
```

### 4. Constructors

Constructors initialize objects when they're created:

```csharp
public class Student
{
    public string Name { get; set; }
    public int Grade { get; set; }
    
    // Default constructor
    public Student()
    {
        Name = "Unknown";
        Grade = 0;
    }
    
    // Parameterized constructor
    public Student(string name, int grade)
    {
        Name = name;
        Grade = grade;
    }
    
    // Constructor chaining
    public Student(string name) : this(name, 0)
    {
    }
}
```

### 5. Static Members

Static members belong to the class rather than to instances:

```csharp
public class MathHelper
{
    public static double PI = 3.14159;
    
    public static int Add(int a, int b)
    {
        return a + b;
    }
    
    public static double CircleArea(double radius)
    {
        return PI * radius * radius;
    }
}
```

## Creating and Using Objects

```csharp
public class Program
{
    public static void Main()
    {
        // Create objects
        Car myCar = new Car("Toyota", "Camry", 2022);
        Car anotherCar = new Car("Honda", "Civic", 2023);
        
        // Use objects
        myCar.Accelerate(50);
        myCar.DisplayInfo();
        
        // Static members
        double area = MathHelper.CircleArea(5);
        Console.WriteLine($"Circle area: {area}");
    }
}
```

## Best Practices

- Use meaningful class and method names
- Keep classes focused on single responsibility
- Use encapsulation to protect data
- Prefer properties over public fields
- Use constructors to ensure objects are in valid state
- Apply appropriate access modifiers
- Follow naming conventions (PascalCase for classes, camelCase for fields)
