# C# Syntax Cheatsheet

Quick reference for C# syntax and common patterns.

---

## Variables & Data Types

```csharp
// Integer types
int age = 25;                          // -2 billion to +2 billion
long bigNumber = 9999999999;           // Much larger
short smallNumber = 32767;             // Smaller range

// Floating point
float price = 19.99f;                  // 7 digits precision
double temperature = 98.6;             // 15-16 digits precision
decimal money = 99.99m;                // For financial (best precision)

// Text
string name = "Alice";
char letter = 'A';                     // Single character

// Boolean
bool isActive = true;
bool isStudent = false;

// Nullable types
int? optionalNumber = null;
```

---

## Operators

```csharp
// Arithmetic
int a = 10, b = 3;
int sum = a + b;          // 13
int diff = a - b;         // 7
int product = a * b;      // 30
int quotient = a / b;     // 3
int remainder = a % b;    // 1

// Comparison
bool isEqual = (a == b);   // false
bool notEqual = (a != b);  // true
bool greater = (a > b);    // true
bool less = (a < b);       // false

// Logical
bool and = (a > 5) && (b > 2);    // true AND true = true
bool or = (a < 5) || (b > 2);     // false OR true = true
bool not = !(a == b);              // true

// Ternary operator
string result = (age >= 18) ? "Adult" : "Minor";

// Assignment
x += 5;        // x = x + 5
x -= 3;        // x = x - 3
x *= 2;        // x = x * 2
```

---

## String Operations

```csharp
string name = "Alice";
string greeting = "Hello";

// Concatenation
string message1 = greeting + " " + name;           // "Hello Alice"
string message2 = $"Hello {name}!";                // Interpolation

// Common methods
int length = name.Length;                          // 5
string upper = name.ToUpper();                     // "ALICE"
string lower = name.ToLower();                     // "alice"
bool contains = name.Contains("li");               // true
string[] words = "Hello World".Split(' ');        // ["Hello", "World"]
string trimmed = "  text  ".Trim();               // "text"
string replaced = "hello".Replace("l", "L");     // "heLLo"
```

---

## Conditionals

```csharp
// if statement
if (age >= 18) {
    Console.WriteLine("Adult");
} else if (age >= 13) {
    Console.WriteLine("Teenager");
} else {
    Console.WriteLine("Child");
}

// switch statement
switch (day) {
    case "Monday":
        Console.WriteLine("Start of week");
        break;
    case "Friday":
        Console.WriteLine("Almost weekend");
        break;
    default:
        Console.WriteLine("Some day");
        break;
}

// Ternary operator
string status = (score >= 60) ? "Pass" : "Fail";
```

---

## Loops

```csharp
// for loop
for (int i = 0; i < 5; i++) {
    Console.WriteLine(i);  // 0 1 2 3 4
}

// while loop
int count = 0;
while (count < 5) {
    Console.WriteLine(count);
    count++;
}

// do-while loop (runs at least once)
do {
    Console.WriteLine("At least once");
} while (false);

// foreach loop (for collections)
int[] numbers = { 1, 2, 3, 4, 5 };
foreach (int num in numbers) {
    Console.WriteLine(num);
}

// break and continue
for (int i = 0; i < 10; i++) {
    if (i == 5) break;         // Exit loop
    if (i == 2) continue;      // Skip to next iteration
    Console.WriteLine(i);
}
```

---

## Arrays

```csharp
// Array declaration and initialization
int[] numbers = { 1, 2, 3, 4, 5 };
string[] names = new string[3];              // Empty array of size 3
int[] scores = new int[5] { 90, 85, 92, 78, 88 };

// Accessing elements (0-indexed!)
int first = numbers[0];          // 1
int second = numbers[1];         // 2

// Array length
int length = numbers.Length;     // 5

// Multi-dimensional array
int[,] matrix = new int[2, 3] {
    { 1, 2, 3 },
    { 4, 5, 6 }
};
int element = matrix[0, 1];      // 2

// Jagged array
int[][] jagged = new int[3][];
jagged[0] = new int[2];
```

---

## Collections

```csharp
// List<T> - dynamic array
List<int> numbers = new List<int> { 1, 2, 3 };
numbers.Add(4);
numbers.Remove(2);
int count = numbers.Count;           // 3
int element = numbers[0];            // 1

// Dictionary<K, V> - key-value pairs
Dictionary<string, int> ages = new Dictionary<string, int>
{
    { "Alice", 25 },
    { "Bob", 30 }
};
ages["Charlie"] = 28;
int aliceAge = ages["Alice"];        // 25
bool hasKey = ages.ContainsKey("Alice");  // true

// HashSet<T> - unique items
HashSet<int> unique = new HashSet<int> { 1, 2, 2, 3 };
// unique contains: 1, 2, 3

// Queue<T> - FIFO (first in, first out)
Queue<string> queue = new Queue<string>();
queue.Enqueue("First");
queue.Enqueue("Second");
string item = queue.Dequeue();   // "First"

// Stack<T> - LIFO (last in, first out)
Stack<string> stack = new Stack<string>();
stack.Push("First");
stack.Push("Second");
string top = stack.Pop();        // "Second"
```

---

## Methods

```csharp
// Basic method
void SayHello() {
    Console.WriteLine("Hello!");
}

// Method with parameters
int Add(int a, int b) {
    return a + b;
}

// Method with multiple parameters
bool IsAdult(int age, string country) {
    if (country == "US") return age >= 18;
    return age >= 21;
}

// Method with default parameter
void Greet(string name = "Guest") {
    Console.WriteLine($"Hello, {name}!");
}

// Method with optional parameters
void PrintInfo(string name, int? age = null) {
    Console.WriteLine($"Name: {name}");
    if (age.HasValue) {
        Console.WriteLine($"Age: {age}");
    }
}

// Calling methods
SayHello();                    // Hello!
int result = Add(5, 3);       // 8
Greet("Alice");               // Hello, Alice!
Greet();                       // Hello, Guest!
```

---

## Classes & Objects

```csharp
// Define a class
public class Person {
    // Properties
    public string Name { get; set; }
    public int Age { get; set; }
    public string Email { get; private set; }

    // Constructor
    public Person(string name, int age) {
        Name = name;
        Age = age;
        Email = $"{name}@example.com";
    }

    // Method
    public void Introduce() {
        Console.WriteLine($"Hi, I'm {Name}, {Age} years old");
    }

    // Property with logic
    public bool IsAdult {
        get { return Age >= 18; }
    }
}

// Create and use objects
Person person = new Person("Alice", 25);
person.Introduce();                      // Hi, I'm Alice, 25 years old
string email = person.Email;             // Alice@example.com
bool adult = person.IsAdult;             // true
```

---

## Inheritance

```csharp
// Base class
public class Animal {
    public string Name { get; set; }

    public void Eat() {
        Console.WriteLine($"{Name} is eating");
    }
}

// Derived class
public class Dog : Animal {
    public void Bark() {
        Console.WriteLine($"{Name} is barking: Woof!");
    }

    // Override base method
    public override void Eat() {
        Console.WriteLine($"{Name} is eating dog food");
    }
}

// Usage
Dog dog = new Dog { Name = "Buddy" };
dog.Eat();      // Buddy is eating dog food
dog.Bark();     // Buddy is barking: Woof!
```

---

## Interfaces

```csharp
// Define interface
public interface IAnimal {
    string Name { get; set; }
    void MakeSound();
}

// Implement interface
public class Cat : IAnimal {
    public string Name { get; set; }

    public void MakeSound() {
        Console.WriteLine("Meow!");
    }
}

// Usage
IAnimal animal = new Cat { Name = "Whiskers" };
animal.MakeSound();     // Meow!
```

---

## Exception Handling

```csharp
// Try-catch
try {
    int result = 10 / int.Parse("0");  // Will throw exception
} catch (DivideByZeroException ex) {
    Console.WriteLine("Cannot divide by zero!");
} catch (FormatException ex) {
    Console.WriteLine("Invalid number format");
} catch (Exception ex) {
    Console.WriteLine($"Error: {ex.Message}");
}

// Finally block (always runs)
try {
    // Some code
} catch (Exception ex) {
    // Handle error
} finally {
    // Cleanup code
    Console.WriteLine("Done");
}

// Throw exception
if (age < 0) {
    throw new ArgumentException("Age cannot be negative");
}
```

---

## LINQ (Querying Collections)

```csharp
int[] numbers = { 1, 2, 3, 4, 5, 6 };

// Where - filter
var evens = numbers.Where(n => n % 2 == 0);  // 2, 4, 6

// Select - transform
var doubled = numbers.Select(n => n * 2);     // 2, 4, 6, 8, 10, 12

// OrderBy - sort
var sorted = numbers.OrderBy(n => n).ToList();

// FirstOrDefault - get first or default
int first = numbers.FirstOrDefault();         // 1
int notFound = numbers.FirstOrDefault(n => n > 10);  // 0

// Count - count items
int count = numbers.Count(n => n > 3);        // 3

// Any - check if any match
bool hasOdd = numbers.Any(n => n % 2 == 1);  // true

// All - check if all match
bool allPositive = numbers.All(n => n > 0);  // true

// Combine multiple operations
var result = numbers
    .Where(n => n > 2)
    .Select(n => n * 2)
    .OrderByDescending(n => n)
    .ToList();                                // 12, 10, 8, 6
```

---

## Async/Await

```csharp
// Async method
public async Task<string> FetchDataAsync() {
    await Task.Delay(1000);  // Simulate delay
    return "Data fetched";
}

// Call async method
string result = await FetchDataAsync();  // Waits for result

// Async with return value
public async Task<int> GetNumberAsync() {
    await Task.Delay(500);
    return 42;
}

// Using Task.Run for CPU-bound work
var result = await Task.Run(() => {
    // Long-running operation
    return ExpensiveCalculation();
});

// Multiple async operations
Task<string> task1 = FetchDataAsync();
Task<string> task2 = FetchDataAsync();
await Task.WhenAll(task1, task2);  // Wait for both
```

---

## Lambda Expressions

```csharp
// Simple lambda
Func<int, int> square = x => x * x;
int result = square(5);  // 25

// With multiple parameters
Func<int, int, int> add = (a, b) => a + b;
int sum = add(3, 4);     // 7

// Multiple statements
Func<int, string> describe = x => {
    if (x < 0) return "Negative";
    if (x == 0) return "Zero";
    return "Positive";
};

// With LINQ
var adults = people
    .Where(p => p.Age >= 18)
    .Select(p => p.Name)
    .ToList();
```

---

## File I/O

```csharp
// Read text file
string content = File.ReadAllText("file.txt");
string[] lines = File.ReadAllLines("file.txt");

// Write text file
File.WriteAllText("file.txt", "Hello World");
File.WriteAllLines("file.txt", new[] { "Line 1", "Line 2" });

// Append to file
File.AppendAllText("file.txt", "\nNew line");

// Check if file exists
if (File.Exists("file.txt")) {
    // Do something
}

// Delete file
File.Delete("file.txt");

// Using statements (auto-dispose)
using (StreamReader reader = new StreamReader("file.txt")) {
    string line;
    while ((line = reader.ReadLine()) != null) {
        Console.WriteLine(line);
    }
}
```

---

## Console Input/Output

```csharp
// Output
Console.WriteLine("Hello World");           // With newline
Console.Write("No newline");               // Without newline
Console.WriteLine($"Value: {x}");          // Interpolation

// Input
string input = Console.ReadLine();         // Read string
int number = int.Parse(Console.ReadLine()); // Read and convert

// Safe input
if (int.TryParse(Console.ReadLine(), out int number)) {
    Console.WriteLine($"Number: {number}");
} else {
    Console.WriteLine("Invalid input");
}

// Colors (Windows only)
Console.ForegroundColor = ConsoleColor.Green;
Console.WriteLine("Green text");
Console.ResetColor();
```

---

## DateTime

```csharp
// Current date and time
DateTime now = DateTime.Now;
DateTime today = DateTime.Today;
DateTime utc = DateTime.UtcNow;

// Create specific date
DateTime birthday = new DateTime(1990, 5, 15);

// Add/subtract time
DateTime tomorrow = now.AddDays(1);
DateTime nextMonth = now.AddMonths(1);
TimeSpan difference = tomorrow - now;

// Get components
int year = now.Year;
int month = now.Month;
int day = now.Day;
int hour = now.Hour;

// Format
string formatted = now.ToString("yyyy-MM-dd");       // 2024-01-15
string formatted2 = now.ToString("MM/dd/yyyy");     // 01/15/2024
```

---

## Common Patterns

```csharp
// Null coalescing operator
string name = userInput ?? "Guest";        // Use default if null

// Null-conditional operator
int? count = person?.GetAge();             // Returns null if person is null

// Pattern matching
if (obj is string text) {
    Console.WriteLine(text.Length);
}

// Using statement (auto-dispose)
using (var reader = new StreamReader("file.txt")) {
    // Use reader
}  // Automatically disposed
```

---

## Tips & Tricks

- **Naming:** Use PascalCase for classes/methods, camelCase for variables
- **Braces:** Always use braces for if/loops, even single statements
- **Comments:** Explain WHY, not WHAT
- **Testing:** Test edge cases and error scenarios
- **Performance:** Measure before optimizing

---

**Print or bookmark this for quick reference while coding!**
