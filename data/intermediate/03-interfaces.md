# Interfaces in C#

An interface defines a contract that classes can implement. It contains only the signatures of methods, properties, events, or indexers. A class implementing an interface must provide implementations for all its members.

## Interface Definition

```csharp
public interface IShape
{
    // Properties (no access modifiers, implicitly public)
    double Area { get; }
    double Perimeter { get; }
    
    // Method signatures (no implementation)
    double GetArea();
    double GetPerimeter();
    void DisplayInfo();
}
```

## Implementing Interfaces

```csharp
public class Rectangle : IShape
{
    public double Width { get; set; }
    public double Height { get; set; }
    
    // Implement interface properties
    public double Area => Width * Height;
    public double Perimeter => 2 * (Width + Height);
    
    public Rectangle(double width, double height)
    {
        Width = width;
        Height = height;
    }
    
    // Implement interface methods
    public double GetArea()
    {
        return Area;
    }
    
    public double GetPerimeter()
    {
        return Perimeter;
    }
    
    public void DisplayInfo()
    {
        Console.WriteLine($"Rectangle: {Width} x {Height}");
        Console.WriteLine($"Area: {Area:F2}, Perimeter: {Perimeter:F2}");
    }
}
```

## Multiple Interface Implementation

A class can implement multiple interfaces:

```csharp
public interface IDrawable
{
    void Draw();
    string Color { get; set; }
}

public interface IMovable
{
    void Move(double x, double y);
    double X { get; set; }
    double Y { get; set; }
}

public class Circle : IShape, IDrawable, IMovable
{
    public double Radius { get; set; }
    
    // IShape implementation
    public double Area => Math.PI * Radius * Radius;
    public double Perimeter => 2 * Math.PI * Radius;
    
    // IDrawable implementation
    public string Color { get; set; } = "Black";
    
    // IMovable implementation
    public double X { get; set; }
    public double Y { get; set; }
    
    public Circle(double radius)
    {
        Radius = radius;
    }
    
    // IShape methods
    public double GetArea() => Area;
    public double GetPerimeter() => Perimeter;
    public void DisplayInfo()
    {
        Console.WriteLine($"Circle: Radius = {Radius}");
        Console.WriteLine($"Area: {Area:F2}, Perimeter: {Perimeter:F2}");
    }
    
    // IDrawable method
    public void Draw()
    {
        Console.WriteLine($"Drawing {Color} circle at ({X}, {Y}) with radius {Radius}");
    }
    
    // IMovable method
    public void Move(double x, double y)
    {
        X += x;
        Y += y;
        Console.WriteLine($"Circle moved to ({X}, {Y})");
    }
}
```

## Interface Inheritance

Interfaces can inherit from other interfaces:

```csharp
public interface IVehicle
{
    string Brand { get; set; }
    string Model { get; set; }
    void Start();
    void Stop();
}

public interface ICar : IVehicle
{
    int Doors { get; set; }
    void OpenTrunk();
}

public interface IElectricVehicle : IVehicle
{
    double BatteryLevel { get; set; }
    void Charge();
    double GetRange();
}

public class ElectricCar : ICar, IElectricVehicle
{
    public string Brand { get; set; }
    public string Model { get; set; }
    public int Doors { get; set; }
    public double BatteryLevel { get; set; }
    
    public ElectricCar(string brand, string model, int doors)
    {
        Brand = brand;
        Model = model;
        Doors = doors;
        BatteryLevel = 100;
    }
    
    // IVehicle implementation
    public void Start()
    {
        Console.WriteLine($"{Brand} {Model} starts silently.");
    }
    
    public void Stop()
    {
        Console.WriteLine($"{Brand} {Model} stops.");
    }
    
    // ICar implementation
    public void OpenTrunk()
    {
        Console.WriteLine("Electric trunk opens.");
    }
    
    // IElectricVehicle implementation
    public void Charge()
    {
        BatteryLevel = 100;
        Console.WriteLine("Battery fully charged.");
    }
    
    public double GetRange()
    {
        return BatteryLevel * 4; // 4 km per percent
    }
}
```

## Default Interface Methods (C# 8.0+)

Interfaces can provide default implementations:

```csharp
public interface ILogger
{
    void Log(string message);
    
    // Default implementation
    void LogError(string error)
    {
        Log($"ERROR: {error}");
    }
    
    void LogWarning(string warning)
    {
        Log($"WARNING: {warning}");
    }
}

public class ConsoleLogger : ILogger
{
    public void Log(string message)
    {
        Console.WriteLine($"{DateTime.Now}: {message}");
    }
    // LogError and LogWarning use default implementations
}
```

## Explicit Interface Implementation

When a class implements multiple interfaces with conflicting members:

```csharp
public interface IA
{
    void DoWork();
}

public interface IB
{
    void DoWork();
}

public class MultiImplementation : IA, IB
{
    // Explicit implementation
    void IA.DoWork()
    {
        Console.WriteLine("IA.DoWork implementation");
    }
    
    void IB.DoWork()
    {
        Console.WriteLine("IB.DoWork implementation");
    }
    
    // Regular implementation
    public void DoWork()
    {
        Console.WriteLine("Regular DoWork implementation");
    }
}
```

## Using Interfaces for Polymorphism

```csharp
public class Program
{
    public static void Main()
    {
        // Interface as type
        IShape[] shapes = new IShape[]
        {
            new Rectangle(5, 3),
            new Circle(4),
            new Triangle(6, 4)
        };
        
        foreach (IShape shape in shapes)
        {
            shape.DisplayInfo();
            Console.WriteLine($"Area: {shape.GetArea():F2}");
            Console.WriteLine();
        }
    }
    
    // Method accepting interface
    public static void DrawAllShapes(IDrawable[] drawables)
    {
        foreach (IDrawable drawable in drawables)
        {
            drawable.Draw();
        }
    }
}
```

## Interface vs Abstract Class

| Feature | Interface | Abstract Class |
|---------|-----------|----------------|
| Multiple inheritance | Yes | No |
| Implementation | No (except default methods) | Can have implementations |
| Access modifiers | Not allowed | Allowed |
| Fields | No | Yes |
| Constructors | No | Yes |
| Purpose | Define contract | Provide base implementation |

## Best Practices

- Use interfaces to define contracts and enable polymorphism
- Prefer interfaces over abstract classes when multiple inheritance is needed
- Keep interfaces focused and small (Interface Segregation Principle)
- Use descriptive interface names with 'I' prefix
- Implement interfaces explicitly when needed to avoid conflicts
- Use default interface methods for backward compatibility
