# Inheritance in C#

Inheritance is a fundamental OOP concept that allows a class to inherit properties and methods from another class. It promotes code reuse and establishes a hierarchical relationship between classes.

## Basic Inheritance Syntax

```csharp
// Base class (parent/superclass)
public class Animal
{
    public string Name { get; set; }
    public int Age { get; set; }
    
    public Animal(string name, int age)
    {
        Name = name;
        Age = age;
    }
    
    public void Eat()
    {
        Console.WriteLine($"{Name} is eating.");
    }
    
    public void Sleep()
    {
        Console.WriteLine($"{Name} is sleeping.");
    }
    
    public virtual void MakeSound()
    {
        Console.WriteLine($"{Name} makes a sound.");
    }
}

// Derived class (child/subclass)
public class Dog : Animal
{
    public string Breed { get; set; }
    
    public Dog(string name, int age, string breed) : base(name, age)
    {
        Breed = breed;
    }
    
    // Override base class method
    public override void MakeSound()
    {
        Console.WriteLine($"{Name} barks: Woof! Woof!");
    }
    
    // New method specific to Dog
    public void WagTail()
    {
        Console.WriteLine($"{Name} is wagging its tail.");
    }
}
```

## Key Concepts

### 1. Base and Derived Classes

- **Base Class**: The class being inherited from (parent)
- **Derived Class**: The class that inherits (child)
- Use `:` to indicate inheritance
- Use `base` keyword to access base class members

### 2. Constructors in Inheritance

```csharp
public class Vehicle
{
    public string Brand { get; set; }
    public string Model { get; set; }
    
    public Vehicle(string brand, string model)
    {
        Brand = brand;
        Model = model;
    }
}

public class Car : Vehicle
{
    public int Doors { get; set; }
    
    // Call base constructor
    public Car(string brand, string model, int doors) : base(brand, model)
    {
        Doors = doors;
    }
}
```

### 3. Method Overriding

Use `virtual` in base class and `override` in derived class:

```csharp
public class Shape
{
    public virtual double GetArea()
    {
        return 0;
    }
    
    public virtual void Display()
    {
        Console.WriteLine("This is a shape.");
    }
}

public class Rectangle : Shape
{
    public double Width { get; set; }
    public double Height { get; set; }
    
    public Rectangle(double width, double height)
    {
        Width = width;
        Height = height;
    }
    
    public override double GetArea()
    {
        return Width * Height;
    }
    
    public override void Display()
    {
        Console.WriteLine($"Rectangle: {Width} x {Height}");
    }
}
```

### 4. Method Hiding

Use `new` keyword to hide base class methods:

```csharp
public class Parent
{
    public void ShowMessage()
    {
        Console.WriteLine("Parent message");
    }
}

public class Child : Parent
{
    public new void ShowMessage()
    {
        Console.WriteLine("Child message");
    }
}
```

### 5. Abstract Classes and Methods

Abstract classes cannot be instantiated and may contain abstract methods:

```csharp
public abstract class Employee
{
    public string Name { get; set; }
    public int Id { get; set; }
    
    public Employee(string name, int id)
    {
        Name = name;
        Id = id;
    }
    
    // Concrete method
    public void DisplayInfo()
    {
        Console.WriteLine($"Employee: {Name}, ID: {Id}");
    }
    
    // Abstract method (must be overridden)
    public abstract double CalculateSalary();
}

public class FullTimeEmployee : Employee
{
    public double MonthlySalary { get; set; }
    
    public FullTimeEmployee(string name, int id, double monthlySalary) 
        : base(name, id)
    {
        MonthlySalary = monthlySalary;
    }
    
    public override double CalculateSalary()
    {
        return MonthlySalary;
    }
}

public class PartTimeEmployee : Employee
{
    public double HourlyRate { get; set; }
    public int HoursWorked { get; set; }
    
    public PartTimeEmployee(string name, int id, double hourlyRate, int hoursWorked) 
        : base(name, id)
    {
        HourlyRate = hourlyRate;
        HoursWorked = hoursWorked;
    }
    
    public override double CalculateSalary()
    {
        return HourlyRate * HoursWorked;
    }
}
```

### 6. Sealed Classes and Methods

Prevent further inheritance or overriding:

```csharp
public sealed class FinalClass
{
    // Cannot be inherited from
}

public class BaseClass
{
    public sealed void FinalMethod()
    {
        // Cannot be overridden in derived classes
    }
}
```

## Polymorphism

Inheritance enables polymorphism - treating objects of different classes through a common interface:

```csharp
public class Program
{
    public static void Main()
    {
        // Polymorphic array
        Animal[] animals = new Animal[]
        {
            new Dog("Buddy", 3, "Golden Retriever"),
            new Cat("Whiskers", 2, "Persian"),
            new Dog("Max", 5, "German Shepherd")
        };
        
        foreach (Animal animal in animals)
        {
            animal.MakeSound(); // Calls appropriate override
            animal.Eat();
        }
    }
}
```

## Best Practices

- Use inheritance when there's a clear "is-a" relationship
- Favor composition over inheritance when appropriate
- Use abstract classes for common base functionality
- Use `virtual` and `override` for polymorphic behavior
- Keep inheritance hierarchies shallow (avoid deep inheritance)
- Use `sealed` to prevent unwanted inheritance
- Follow Liskov Substitution Principle
