# Common Mistakes - Beginner to Advanced

Learn from mistakes so you don't make them!

---

## Level 1: Foundations

### 1. Off-By-One Errors with Arrays

**❌ WRONG:**

```csharp
int[] numbers = new int[5];  // Valid indices: 0, 1, 2, 3, 4
int x = numbers[5];          // ERROR! Index out of range!
```

**✅ RIGHT:**

```csharp
int[] numbers = new int[5];  // Valid indices: 0, 1, 2, 3, 4
for (int i = 0; i < numbers.Length; i++) {
    numbers[i] = i;
}
```

**Why:** Arrays are 0-indexed. Size 5 means indices 0-4, not 1-5.

---

### 2. Confusing `=` with `==`

**❌ WRONG:**

```csharp
if (age = 18) {  // This assigns 18 to age!
    Console.WriteLine("Adult");
}
```

**✅ RIGHT:**

```csharp
if (age == 18) {  // This compares age to 18
    Console.WriteLine("Adult");
}
```

**Why:**

- `=` is assignment (gives a value)
- `==` is comparison (checks if equal)

---

### 3. String vs Number Concatenation

**❌ WRONG:**

```csharp
string result = "5" + "10";  // "510" - concatenation!
int total = 5 + 10;          // 15 - addition
```

**✅ RIGHT:**

```csharp
int num1 = int.Parse("5");
int num2 = int.Parse("10");
int total = num1 + num2;     // 15

// Or use interpolation
string result = $"{5 + 10}"; // "15"
```

**Why:** When both operands are strings, `+` concatenates. Parse to numbers first.

---

### 4. Forgetting Braces in Conditionals

**❌ WRONG:**

```csharp
if (x > 5)
    y = 10;
    z = 20;  // This always runs, even if x <= 5!
```

**✅ RIGHT:**

```csharp
if (x > 5) {
    y = 10;
    z = 20;  // This only runs if x > 5
}
```

**Why:** Without braces, only the next single statement is part of the if block.

---

### 5. Infinite Loops

**❌ WRONG:**

```csharp
while (true) {
    Console.WriteLine("Forever!");
    // No break condition!
}
```

**✅ RIGHT:**

```csharp
int count = 0;
while (count < 10) {
    Console.WriteLine("Counting: " + count);
    count++;
}
```

**Why:** Always ensure your loop has a way to exit. Use break or a false condition.

---

### 6. Division by Zero

**❌ WRONG:**

```csharp
int divisor = 0;
int result = 10 / divisor;  // CRASH!
```

**✅ RIGHT:**

```csharp
int divisor = 0;
if (divisor != 0) {
    int result = 10 / divisor;
} else {
    Console.WriteLine("Cannot divide by zero");
}
```

**Why:** Always check that you're not dividing by zero.

---

### 7. Modifying Loop Variable Inside Loop

**❌ WRONG:**

```csharp
for (int i = 0; i < 10; i++) {
    i = 5;  // Confusing! Loop continues from 5, not as expected
    Console.WriteLine(i);
}
```

**✅ RIGHT:**

```csharp
for (int i = 0; i < 10; i++) {
    Console.WriteLine(i);
    // Don't modify i inside the loop
}
```

**Why:** It works, but it's confusing and rarely what you want.

---

## Level 2: Object-Oriented Programming

### 8. Forgetting `new` Keyword

**❌ WRONG:**

```csharp
Person person;
person.Name = "Alice";  // NullReferenceException!
```

**✅ RIGHT:**

```csharp
Person person = new Person();
person.Name = "Alice";
```

**Why:** Without `new`, the variable is null (points to nothing). You must create an instance.

---

### 9. NullReferenceException

**❌ WRONG:**

```csharp
Person person = null;
Console.WriteLine(person.Name);  // CRASH!
```

**✅ RIGHT:**

```csharp
Person person = null;
if (person != null) {
    Console.WriteLine(person.Name);
}

// Or use null-conditional operator
Console.WriteLine(person?.Name);  // Prints nothing if null
```

**Why:** You can't access properties/methods on null. Always check first.

---

### 10. Forgetting `this` keyword

**❌ WRONG:**

```csharp
public class Person {
    private string name;

    public void SetName(string name) {
        name = name;  // Sets local variable, not the field!
    }
}
```

**✅ RIGHT:**

```csharp
public class Person {
    private string name;

    public void SetName(string name) {
        this.name = name;  // Sets the field
    }
}
```

**Why:** Without `this`, it's ambiguous which `name` you mean.

---

### 11. Mutable Reference Types

**❌ WRONG:**

```csharp
List<int> original = new List<int> { 1, 2, 3 };
List<int> copy = original;
copy.Add(4);
// original also has 4! They're the same object!
```

**✅ RIGHT:**

```csharp
List<int> original = new List<int> { 1, 2, 3 };
List<int> copy = new List<int>(original);  // Creates new copy
copy.Add(4);
// original still has 1, 2, 3
```

**Why:** Lists and objects are reference types. Assignment only copies the reference, not the data.

---

### 12. Confusing Inheritance and Interface

**❌ WRONG:**

```csharp
// Only inherit from one class
public class Dog : Animal, Cat {  // ERROR!
}
```

**✅ RIGHT:**

```csharp
// Inherit from one class, implement multiple interfaces
public class Dog : Animal, IRunnable, IJumpable {
}
```

**Why:** C# allows single inheritance (one base class) but multiple interfaces.

---

### 13. Not Calling Base Constructor

**❌ WRONG:**

```csharp
public class Dog : Animal {
    public Dog() {
        // Base constructor not called!
        // Animal might not be properly initialized
    }
}
```

**✅ RIGHT:**

```csharp
public class Dog : Animal {
    public Dog() : base() {
        // Base constructor called
    }
}
```

**Why:** Parent class initialization is important. Call it explicitly or implicitly.

---

## Level 3: Intermediate Concepts

### 14. Deferred Execution with LINQ

**❌ WRONG:**

```csharp
List<int> numbers = new List<int> { 1, 2, 3, 4, 5 };
var query = numbers.Where(n => n > 2);  // Not executed yet!
numbers.Add(10);
// query now includes 10!
```

**✅ RIGHT:**

```csharp
List<int> numbers = new List<int> { 1, 2, 3, 4, 5 };
var query = numbers.Where(n => n > 2).ToList();  // Executed now
numbers.Add(10);
// query still has only the original numbers
```

**Why:** LINQ is lazy. It doesn't execute until you iterate or call ToList().

---

### 15. Async Without Await

**❌ WRONG:**

```csharp
public async Task<string> FetchData() {
    Thread.Sleep(1000);  // Blocks the thread!
    return "Data";
}
```

**✅ RIGHT:**

```csharp
public async Task<string> FetchData() {
    await Task.Delay(1000);  // Doesn't block
    return "Data";
}
```

**Why:** Use `await Task.Delay()` not `Thread.Sleep()` in async methods.

---

### 16. Fire and Forget Async (Dangerous)

**❌ WRONG:**

```csharp
// Calling async without awaiting
FetchData();  // Exception will be swallowed!
```

**✅ RIGHT:**

```csharp
// Option 1: Await
await FetchData();

// Option 2: If you must fire-and-forget
_ = FetchData();  // At least document it
```

**Why:** Exceptions in unawaited async methods are lost. Always await or handle exceptions.

---

### 17. Async Void (Almost Never)

**❌ WRONG:**

```csharp
public async void ButtonClick() {
    await SomeAsync();  // Can't track when it completes
}
```

**✅ RIGHT:**

```csharp
public async Task ButtonClick() {
    await SomeAsync();  // Caller can await
}
```

**Why:** `async void` makes error handling and tracking difficult. Use `async Task` instead.

---

### 18. Capturing Loop Variable in Lambda

**❌ WRONG:**

```csharp
List<Action> actions = new List<Action>();
for (int i = 0; i < 3; i++) {
    actions.Add(() => Console.WriteLine(i));
}
foreach (var action in actions) {
    action();  // Prints 3, 3, 3!
}
```

**✅ RIGHT:**

```csharp
List<Action> actions = new List<Action>();
for (int i = 0; i < 3; i++) {
    int captured = i;  // Capture the value
    actions.Add(() => Console.WriteLine(captured));
}
foreach (var action in actions) {
    action();  // Prints 0, 1, 2
}
```

**Why:** The lambda captures the variable, not the value. After the loop, `i` is 3.

---

## Level 4: Professional Development

### 19. Over-Engineering

**❌ WRONG:**

```csharp
// Complex factory pattern for a simple object
public class PersonFactory {
    public PersonFactory(IServiceProvider provider,
                         IConfiguration config,
                         ILogger<PersonFactory> logger) {
        // 50 lines of setup
    }
}
```

**✅ RIGHT:**

```csharp
// Simple creation
var person = new Person { Name = "Alice" };
```

**Why:** Don't add complexity unless you need it. YAGNI (You Aren't Gonna Need It).

---

### 20. Testing Implementation Instead of Behavior

**❌ WRONG:**

```csharp
[Test]
public void TestCalculatorInternalState() {
    var calc = new Calculator();
    calc._result = 5;  // Testing private field!
    Assert.AreEqual(5, calc._result);
}
```

**✅ RIGHT:**

```csharp
[Test]
public void TestCalculatorReturnsCorrectSum() {
    var calc = new Calculator();
    var result = calc.Add(2, 3);
    Assert.AreEqual(5, result);
}
```

**Why:** Test behavior, not implementation. Implementation can change.

---

### 21. Catching Exceptions Too Broadly

**❌ WRONG:**

```csharp
try {
    var result = int.Parse(input);
} catch (Exception ex) {  // Catches everything!
    Console.WriteLine("Error: " + ex.Message);
}
```

**✅ RIGHT:**

```csharp
try {
    var result = int.Parse(input);
} catch (FormatException ex) {
    Console.WriteLine("Invalid format");
} catch (OverflowException ex) {
    Console.WriteLine("Number too large");
}
```

**Why:** Catch specific exceptions. Broad catches hide bugs.

---

### 22. Synchronous Wrapper Around Async Code

**❌ WRONG:**

```csharp
public string FetchData() {
    return FetchDataAsync().Result;  // Deadlock risk!
}
```

**✅ RIGHT:**

```csharp
public async Task<string> FetchData() {
    return await FetchDataAsync();
}
```

**Why:** Calling `.Result` on async code can cause deadlocks. Use async all the way.

---

### 23. Not Disposing Resources

**❌ WRONG:**

```csharp
var reader = new StreamReader("file.txt");
var content = reader.ReadToEnd();
// Leaks memory - file handle not released
```

**✅ RIGHT:**

```csharp
using (var reader = new StreamReader("file.txt")) {
    var content = reader.ReadToEnd();
}  // Automatically disposed

// Or with using declaration (C# 8+)
using var reader = new StreamReader("file.txt");
var content = reader.ReadToEnd();
```

**Why:** Unmanaged resources need cleanup. Use `using` statements.

---

### 24. N+1 Query Problem

**❌ WRONG:**

```csharp
// Queries database in a loop!
var users = database.Users.ToList();
foreach (var user in users) {
    var posts = database.Posts.Where(p => p.UserId == user.Id).ToList();
}
```

**✅ RIGHT:**

```csharp
// Single query with join
var usersWithPosts = database.Users
    .Include(u => u.Posts)
    .ToList();
```

**Why:** Multiple queries in loops are slow. Use joins or eager loading.

---

### 25. Modifying Collection During Iteration

**❌ WRONG:**

```csharp
var list = new List<int> { 1, 2, 3, 4, 5 };
foreach (var item in list) {
    if (item == 3) {
        list.Remove(item);  // ERROR!
    }
}
```

**✅ RIGHT:**

```csharp
var list = new List<int> { 1, 2, 3, 4, 5 };
var toRemove = list.Where(x => x == 3).ToList();
foreach (var item in toRemove) {
    list.Remove(item);
}

// Or use LINQ
var filtered = list.Where(x => x != 3).ToList();
```

**Why:** Modifying a collection during iteration causes unexpected behavior.

---

## Summary: Key Takeaways

1. **Arrays are 0-indexed**
2. **Always use `==` for comparison, `=` for assignment**
3. **Check for null before using objects**
4. **Use `new` to create instances**
5. **Parse strings to numbers before calculations**
6. **Understand reference vs value types**
7. **Use `await` with async methods**
8. **Catch specific exceptions**
9. **Use `using` for resource cleanup**
10. **Test behavior, not implementation**

---

## Prevention Checklist

Before submitting code:

- [ ] Ran and tested the code
- [ ] No compiler warnings
- [ ] Checked for null references
- [ ] Verified array bounds
- [ ] Tested edge cases
- [ ] No infinite loops
- [ ] Resources properly disposed
- [ ] Appropriate exception handling
- [ ] Followed naming conventions
- [ ] Code is readable and clear

---

**Learn from these mistakes so you don't have to make them!**
