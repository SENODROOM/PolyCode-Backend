# Delegates in C#

A delegate is a type-safe function pointer that references methods with a specific parameter list and return type. Delegates enable methods to be passed as parameters, stored in variables, and invoked dynamically.

## Delegate Basics

### Declaration

```csharp
// Delegate declaration
public delegate void SimpleDelegate(string message);
public delegate int MathOperation(int a, int b);
public delegate bool Predicate<T>(T item);
```

### Creating and Using Delegates

```csharp
public class DelegateExample
{
    // Methods that match delegate signature
    public static void ShowMessage(string message)
    {
        Console.WriteLine($"Message: {message}");
    }
    
    public static void LogMessage(string message)
    {
        Console.WriteLine($"LOG: {DateTime.Now}: {message}");
    }
    
    public static int Add(int a, int b)
    {
        return a + b;
    }
    
    public static int Multiply(int a, int b)
    {
        return a * b;
    }
    
    public static void Main()
    {
        // Create delegate instances
        SimpleDelegate messageDelegate = new SimpleDelegate(ShowMessage);
        MathOperation mathDelegate = Add;
        
        // Invoke delegates
        messageDelegate("Hello, World!");
        int result = mathDelegate(5, 3);
        Console.WriteLine($"Result: {result}");
        
        // Change delegate method
        messageDelegate = LogMessage;
        messageDelegate("This is a log message");
        
        mathDelegate = Multiply;
        result = mathDelegate(5, 3);
        Console.WriteLine($"Result: {result}");
    }
}
```

## Multicast Delegates

Delegates can hold references to multiple methods:

```csharp
public class MulticastExample
{
    public static void Method1()
    {
        Console.WriteLine("Method 1 executed");
    }
    
    public static void Method2()
    {
        Console.WriteLine("Method 2 executed");
    }
    
    public static void Method3()
    {
        Console.WriteLine("Method 3 executed");
    }
    
    public static void Main()
    {
        // Create multicast delegate
        Action multicastDelegate = Method1;
        multicastDelegate += Method2;
        multicastDelegate += Method3;
        
        Console.WriteLine("Invoking multicast delegate:");
        multicastDelegate();
        
        // Remove a method
        Console.WriteLine("\nAfter removing Method2:");
        multicastDelegate -= Method2;
        multicastDelegate();
        
        // Get invocation list
        Console.WriteLine($"\nMethods in delegate: {multicastDelegate.GetInvocationList().Length}");
    }
}
```

## Built-in Delegate Types

### Action Delegate

Represents a method that doesn't return a value:

```csharp
// Action with no parameters
Action action1 = () => Console.WriteLine("No parameters");

// Action with parameters
Action<string> action2 = (message) => Console.WriteLine(message);
Action<int, int> action3 = (a, b) => Console.WriteLine($"Sum: {a + b}");

// Invoke
action1();
action2("Hello");
action3(5, 3);
```

### Func Delegate

Represents a method that returns a value:

```csharp
// Func with no parameters, returns int
Func<int> func1 = () => 42;

// Func with parameters, returns string
Func<int, int, string> func2 = (a, b) => $"Sum of {a} and {b} is {a + b}";

// Invoke
int number = func1();
string result = func2(5, 3);
Console.WriteLine(number);
Console.WriteLine(result);
```

### Predicate Delegate

Represents a method that returns a boolean:

```csharp
Predicate<int> isEven = (number) => number % 2 == 0;
Predicate<string> isLong = (text) => text.Length > 10;

bool even = isEven(4);      // true
bool longText = isLong("Hello"); // false
```

## Delegates with Lambda Expressions

```csharp
public class LambdaExample
{
    public static void Main()
    {
        // Lambda expressions with delegates
        Func<int, int> square = x => x * x;
        Func<int, int, int> add = (a, b) => a + b;
        Action<string> print = message => Console.WriteLine(message);
        Predicate<int> isPositive = x => x > 0;
        
        Console.WriteLine($"Square of 5: {square(5)}");
        Console.WriteLine($"Add 3 + 7: {add(3, 7)}");
        print("Hello from lambda!");
        Console.WriteLine($"Is 5 positive? {isPositive(5)}");
        
        // Complex lambda
        Func<List<int>, List<int>> filterEvenNumbers = numbers =>
        {
            var result = new List<int>();
            foreach (int num in numbers)
            {
                if (num % 2 == 0)
                    result.Add(num);
            }
            return result;
        };
        
        var numbers = new List<int> { 1, 2, 3, 4, 5, 6 };
        var evenNumbers = filterEvenNumbers(numbers);
        Console.WriteLine($"Even numbers: {string.Join(", ", evenNumbers)}");
    }
}
```

## Delegates as Parameters

```csharp
public class DelegateParameters
{
    // Method that accepts a delegate parameter
    public static void ProcessNumbers(List<int> numbers, Func<int, int> operation)
    {
        Console.WriteLine($"Processing numbers with operation:");
        foreach (int num in numbers)
        {
            int result = operation(num);
            Console.WriteLine($"{num} -> {result}");
        }
    }
    
    // Method that accepts Action delegate
    public static void ProcessWithCallback(string data, Action<string> callback)
    {
        Console.WriteLine($"Processing: {data}");
        // Simulate processing
        Thread.Sleep(1000);
        callback($"Completed processing: {data}");
    }
    
    public static void Main()
    {
        var numbers = new List<int> { 1, 2, 3, 4, 5 };
        
        // Process with different operations
        ProcessNumbers(numbers, x => x * 2);    // Double
        ProcessNumbers(numbers, x => x * x);    // Square
        ProcessNumbers(numbers, x => x + 10);   // Add 10
        
        // Process with callback
        ProcessWithCallback("File.txt", result => Console.WriteLine($"Callback: {result}"));
    }
}
```

## Anonymous Methods

```csharp
public class AnonymousMethods
{
    public static void Main()
    {
        // Anonymous method syntax
        Func<int, int> square = delegate(int x)
        {
            return x * x;
        };
        
        Action<string> print = delegate(string message)
        {
            Console.WriteLine($"Anonymous: {message}");
        };
        
        Console.WriteLine($"Square of 6: {square(6)}");
        print("Hello from anonymous method");
        
        // Anonymous method with closure
        int multiplier = 5;
        Func<int, int> multiplyByMultiplier = delegate(int x)
        {
            return x * multiplier;
        };
        
        Console.WriteLine($"10 * {multiplier} = {multiplyByMultiplier(10)}");
    }
}
```

## Delegate Covariance and Contravariance

```csharp
public class VarianceExample
{
    // Base and derived classes
    public class BaseClass { }
    public class DerivedClass : BaseClass { }
    
    // Delegate types
    public delegate BaseClass BaseDelegate();
    public delegate void DerivedDelegate(DerivedClass derived);
    
    // Methods
    public static DerivedClass GetDerived() { return new DerivedClass(); }
    public static void ProcessBase(BaseClass baseObj) { }
    
    public static void Main()
    {
        // Covariance: method return type can be more derived
        BaseDelegate covariantDelegate = GetDerived;
        BaseClass result = covariantDelegate();
        
        // Contravariance: method parameter type can be less derived
        DerivedDelegate contravariantDelegate = ProcessBase;
        contravariantDelegate(new DerivedClass());
    }
}
```

## Practical Examples

### Event Handling

```csharp
public class EventExample
{
    public delegate void NotifyEventHandler(string message);
    public event NotifyEventHandler OnNotify;
    
    public void TriggerEvent(string message)
    {
        OnNotify?.Invoke(message);
    }
    
    public static void Main()
    {
        var example = new EventExample();
        
        // Subscribe to event
        example.OnNotify += (msg) => Console.WriteLine($"Listener 1: {msg}");
        example.OnNotify += (msg) => Console.WriteLine($"Listener 2: {msg}");
        
        // Trigger event
        example.TriggerEvent("Hello from event!");
    }
}
```

### Strategy Pattern with Delegates

```csharp
public class StrategyPattern
{
    public static void ProcessData(List<int> data, Func<List<int>, List<int>> strategy)
    {
        Console.WriteLine($"Original: {string.Join(", ", data)}");
        var result = strategy(data);
        Console.WriteLine($"Processed: {string.Join(", ", result)}");
    }
    
    public static void Main()
    {
        var numbers = new List<int> { 5, 2, 8, 1, 9, 3 };
        
        // Different strategies
        ProcessData(numbers, list => list.OrderBy(x => x).ToList());           // Sort
        ProcessData(numbers, list => list.Where(x => x > 5).ToList());          // Filter > 5
        ProcessData(numbers, list => list.Select(x => x * 2).ToList());         // Double
        ProcessData(numbers, list => list.OrderByDescending(x => x).ToList());   // Reverse sort
    }
}
```

## Best Practices

- Use built-in delegate types (`Action`, `Func`, `Predicate`) when possible
- Keep delegate signatures simple and focused
- Use lambda expressions for concise delegate creation
- Consider null checks before invoking delegates (`?.Invoke()`)
- Use events for pub/sub scenarios instead of direct delegate invocation
- Be aware of memory leaks with event subscriptions
- Use generic delegates for type safety and reusability
