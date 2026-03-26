# Methods in C#

Methods (also called functions) are blocks of code that perform specific tasks. They help organize code, make it reusable, and improve readability.

## Method Syntax

```csharp
access_modifier return_type MethodName(parameter_list)
{
    // Method body
    return value; // if return_type is not void
}
```

## Parts of a Method

- **Access Modifier**: `public`, `private`, `protected`, `internal`
- **Return Type**: Data type of the value returned (`void` if no return value)
- **Method Name**: Descriptive name following PascalCase
- **Parameters**: Input values (optional)
- **Method Body**: Code that executes when called

## Method Examples

### Method with No Parameters and No Return Value

```csharp
public void SayHello()
{
    Console.WriteLine("Hello, World!");
}
```

### Method with Parameters

```csharp
public void GreetPerson(string name, int age)
{
    Console.WriteLine($"Hello {name}, you are {age} years old!");
}
```

### Method with Return Value

```csharp
public int Add(int a, int b)
{
    return a + b;
}
```

### Method with Multiple Parameters and Return Value

```csharp
public double CalculateArea(double length, double width)
{
    return length * width;
}
```

## Method Overloading

You can have multiple methods with the same name but different parameters:

```csharp
public int Add(int a, int b)
{
    return a + b;
}

public double Add(double a, double b)
{
    return a + b;
}

public int Add(int a, int b, int c)
{
    return a + b + c;
}
```

## Static vs Instance Methods

- **Static methods**: Belong to the class, called using class name
- **Instance methods**: Belong to an object, called using object instance

```csharp
public class Calculator
{
    // Static method
    public static int Multiply(int a, int b)
    {
        return a * b;
    }
    
    // Instance method
    public int Divide(int a, int b)
    {
        return a / b;
    }
}
```

## Best Practices

- Use descriptive method names
- Keep methods focused on one task
- Limit the number of parameters (consider using objects or tuples)
- Add comments for complex logic
- Use appropriate access modifiers
