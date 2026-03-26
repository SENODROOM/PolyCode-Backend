# Exception Handling in C#

Exception handling is a mechanism to handle runtime errors and maintain program flow when unexpected situations occur.

## What is an Exception?

An exception is an event that occurs during the execution of a program that disrupts the normal flow of instructions.

## Basic Exception Handling Structure

```csharp
try
{
    // Code that might cause an exception
    int result = 10 / 0;
}
catch (DivideByZeroException ex)
{
    // Handle specific exception
    Console.WriteLine($"Cannot divide by zero: {ex.Message}");
}
catch (Exception ex)
{
    // Handle any other exception
    Console.WriteLine($"An error occurred: {ex.Message}");
}
finally
{
    // Code that always executes
    Console.WriteLine("Cleanup code here");
}
```

## Common Exception Types

### System Exception Hierarchy
- `Exception` - Base class for all exceptions
- `SystemException` - Base for runtime exceptions
- `ArgumentException` - Invalid method arguments
- `ArgumentNullException` - Null argument passed
- `ArgumentOutOfRangeException` - Argument out of valid range
- `InvalidOperationException` - Invalid operation
- `NotSupportedException` - Operation not supported
- `NullReferenceException` - Null reference access
- `IndexOutOfRangeException` - Array index out of bounds
- `DivideByZeroException` - Division by zero
- `FormatException` - Invalid format conversion
- `IOException` - Input/output operation failure

## Exception Handling Examples

### 1. Basic Try-Catch

```csharp
public void DivideNumbers()
{
    try
    {
        Console.Write("Enter first number: ");
        int num1 = Convert.ToInt32(Console.ReadLine());
        
        Console.Write("Enter second number: ");
        int num2 = Convert.ToInt32(Console.ReadLine());
        
        int result = num1 / num2;
        Console.WriteLine($"Result: {result}");
    }
    catch (DivideByZeroException)
    {
        Console.WriteLine("Error: Cannot divide by zero!");
    }
    catch (FormatException)
    {
        Console.WriteLine("Error: Please enter valid numbers!");
    }
    catch (Exception ex)
    {
        Console.WriteLine($"Unexpected error: {ex.Message}");
    }
}
```

### 2. Finally Block

```csharp
public void ReadFile()
{
    StreamReader reader = null;
    try
    {
        reader = new StreamReader("data.txt");
        string content = reader.ReadToEnd();
        Console.WriteLine(content);
    }
    catch (FileNotFoundException)
    {
        Console.WriteLine("File not found!");
    }
    catch (IOException ex)
    {
        Console.WriteLine($"IO Error: {ex.Message}");
    }
    finally
    {
        reader?.Close(); // Always close the file
        Console.WriteLine("File reader closed.");
    }
}
```

### 3. Using Statement (Automatic Cleanup)

```csharp
public void ReadFileWithUsing()
{
    try
    {
        using (StreamReader reader = new StreamReader("data.txt"))
        {
            string content = reader.ReadToEnd();
            Console.WriteLine(content);
        } // reader is automatically closed here
    }
    catch (FileNotFoundException)
    {
        Console.WriteLine("File not found!");
    }
}
```

## Throwing Exceptions

### Throwing Built-in Exceptions

```csharp
public class BankAccount
{
    private decimal balance;
    
    public void Withdraw(decimal amount)
    {
        if (amount <= 0)
        {
            throw new ArgumentException("Withdrawal amount must be positive.");
        }
        
        if (amount > balance)
        {
            throw new InvalidOperationException("Insufficient funds.");
        }
        
        balance -= amount;
    }
}
```

### Throwing Custom Exceptions

```csharp
public class InsufficientFundsException : Exception
{
    public decimal RequestedAmount { get; }
    public decimal AvailableBalance { get; }
    
    public InsufficientFundsException(decimal requested, decimal available)
        : base($"Insufficient funds. Requested: {requested}, Available: {available}")
    {
        RequestedAmount = requested;
        AvailableBalance = available;
    }
}

public class BankAccount
{
    private decimal balance;
    
    public void Withdraw(decimal amount)
    {
        if (amount > balance)
        {
            throw new InsufficientFundsException(amount, balance);
        }
        balance -= amount;
    }
}
```

## Exception Properties

```csharp
try
{
    // Code that might throw exception
}
catch (Exception ex)
{
    Console.WriteLine($"Message: {ex.Message}");
    Console.WriteLine($"Source: {ex.Source}");
    Console.WriteLine($"StackTrace: {ex.StackTrace}");
    Console.WriteLine($"HelpLink: {ex.HelpLink}");
    
    if (ex.InnerException != null)
    {
        Console.WriteLine($"Inner Exception: {ex.InnerException.Message}");
    }
}
```

## Rethrowing Exceptions

```csharp
public void ProcessData()
{
    try
    {
        // Some processing
        ValidateData();
    }
    catch (Exception ex)
    {
        // Log the exception
        LogError(ex);
        
        // Rethrow the exception
        throw; // Preserves original stack trace
        
        // Or throw new exception with original as inner exception
        // throw new ApplicationException("Processing failed", ex);
    }
}
```

## Exception Filters (C# 6.0+)

```csharp
try
{
    // Code that might throw exception
}
catch (ArgumentException ex) when (ex.ParamName == "email")
{
    Console.WriteLine("Invalid email format");
}
catch (ArgumentException ex) when (ex.ParamName == "age")
{
    Console.WriteLine("Invalid age value");
}
catch (Exception ex)
{
    Console.WriteLine($"General error: {ex.Message}");
}
```

## Best Practices

### DO:
- Use specific exception types
- Include meaningful error messages
- Use `finally` for cleanup
- Use `using` statement for disposable objects
- Log exceptions for debugging
- Create custom exceptions for domain-specific errors
- Use exception filters when appropriate

### DON'T:
- Use exceptions for normal program flow
- Catch `Exception` unless necessary
- Ignore exceptions (empty catch blocks)
- Throw exceptions from finally blocks
- Use exceptions for validation that can be prevented

## Exception Handling Patterns

### 1. Try-Parse Pattern

```csharp
// Instead of:
try
{
    int number = int.Parse(input);
}
catch (FormatException)
{
    // Handle error
}

// Use:
if (int.TryParse(input, out int number))
{
    // Success
}
else
{
    // Handle error
}
```

### 2. Null Check Pattern

```csharp
// Instead of catching NullReferenceException:
try
{
    string result = obj.Property.Method();
}
catch (NullReferenceException)
{
    // Handle null
}

// Use null checking:
if (obj?.Property?.Method() != null)
{
    string result = obj.Property.Method();
}
```

## Debugging Exceptions

- Use `Debug` menu in Visual Studio
- Set breakpoints in catch blocks
- Use `Exception Settings` to break on specific exceptions
- Examine the `StackTrace` for call hierarchy
- Use `InnerException` for nested exceptions
