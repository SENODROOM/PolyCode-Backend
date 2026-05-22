# Loops in C#

Loops allow you to execute a block of code repeatedly. C# provides several types of loops for different scenarios.

## for Loop

The `for` loop is used when you know how many times you want to iterate.

```csharp
for (initializer; condition; iterator)
{
    // Code to execute
}
```

### Example:
```csharp
for (int i = 0; i < 5; i++)
{
    Console.WriteLine($"Iteration {i}");
}
```

## while Loop

The `while` loop continues as long as the condition is true. Use when you don't know the exact number of iterations.

```csharp
while (condition)
{
    // Code to execute
}
```

### Example:
```csharp
int count = 0;
while (count < 5)
{
    Console.WriteLine($"Count: {count}");
    count++;
}
```

## do-while Loop

The `do-while` loop executes the code at least once before checking the condition.

```csharp
do
{
    // Code to execute
} while (condition);
```

### Example:
```csharp
int number;
do
{
    Console.Write("Enter a positive number: ");
    number = Convert.ToInt32(Console.ReadLine());
} while (number <= 0);
```

## foreach Loop

The `foreach` loop is used to iterate over collections and arrays.

```csharp
foreach (var item in collection)
{
    // Code to execute for each item
}
```

### Example:
```csharp
string[] fruits = { "Apple", "Banana", "Orange" };
foreach (string fruit in fruits)
{
    Console.WriteLine(fruit);
}
```

## Loop Control Statements

### break Statement
Exits the loop immediately:

```csharp
for (int i = 0; i < 10; i++)
{
    if (i == 5)
        break;
    Console.WriteLine(i);
}
```

### continue Statement
Skips the current iteration and continues with the next:

```csharp
for (int i = 0; i < 10; i++)
{
    if (i % 2 == 0)
        continue;
    Console.WriteLine(i);
}
```

## Nested Loops

Loops can be nested inside other loops:

```csharp
for (int i = 1; i <= 3; i++)
{
    for (int j = 1; j <= 3; j++)
    {
        Console.WriteLine($"{i} x {j} = {i * j}");
    }
}
```

## Best Practices

- Choose the right loop type for your use case
- Avoid infinite loops (ensure conditions will eventually be false)
- Use meaningful variable names for loop counters
- Keep loop bodies as simple as possible
- Consider using `foreach` for collections when you don't need the index
- Use `break` and `continue` sparingly for better readability
