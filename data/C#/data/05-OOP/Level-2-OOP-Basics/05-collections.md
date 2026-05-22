# Collections in C#

Collections are data structures that store and manage groups of objects. .NET provides various collection types for different scenarios.

## Collection Categories

### 1. Generic Collections (System.Collections.Generic)
- Type-safe at compile time
- Better performance than non-generic
- Recommended for new code

### 2. Non-Generic Collections (System.Collections)
- Store objects of type `object`
- Require casting
- Legacy support

## Generic Collections

### List<T>

Dynamic array that can grow and shrink:

```csharp
List<string> names = new List<string>();
names.Add("Alice");
names.Add("Bob");
names.Add("Charlie");

// Insert at specific position
names.Insert(1, "David");

// Access by index
string first = names[0];

// Remove elements
names.Remove("Bob");
names.RemoveAt(0);

// Check if contains
bool hasAlice = names.Contains("Alice");

// Sort and reverse
names.Sort();
names.Reverse();

// Iterate
foreach (string name in names)
{
    Console.WriteLine(name);
}
```

### Dictionary<TKey, TValue>

Key-value pairs for fast lookup:

```csharp
Dictionary<string, int> ages = new Dictionary<string, int>();
ages.Add("Alice", 25);
ages["Bob"] = 30;

// Access values
int aliceAge = ages["Alice"];

// Safe access with TryGetValue
if (ages.TryGetValue("Charlie", out int charlieAge))
{
    Console.WriteLine($"Charlie is {charlieAge} years old");
}

// Check if key exists
bool hasBob = ages.ContainsKey("Bob");

// Remove key-value pair
ages.Remove("Alice");

// Iterate
foreach (var kvp in ages)
{
    Console.WriteLine($"{kvp.Key}: {kvp.Value}");
}

// Iterate keys or values
foreach (string name in ages.Keys)
{
    Console.WriteLine(name);
}

foreach (int age in ages.Values)
{
    Console.WriteLine(age);
}
```

### HashSet<T>

Unique elements with fast operations:

```csharp
HashSet<string> uniqueNames = new HashSet<string>();
uniqueNames.Add("Alice");
uniqueNames.Add("Bob");
uniqueNames.Add("Alice"); // Won't be added again

// Check if contains
bool hasAlice = uniqueNames.Contains("Alice");

// Set operations
HashSet<string> set1 = new HashSet<string> { "A", "B", "C" };
HashSet<string> set2 = new HashSet<string> { "B", "C", "D" };

// Union
set1.UnionWith(set2);

// Intersection
set1.IntersectWith(set2);

// Difference
set1.ExceptWith(set2);
```

### Queue<T>

First-In, First-Out (FIFO) collection:

```csharp
Queue<string> queue = new Queue<string>();
queue.Enqueue("First");
queue.Enqueue("Second");
queue.Enqueue("Third");

// Peek at first element without removing
string first = queue.Peek();

// Remove and return first element
string removed = queue.Dequeue();

// Check count
int count = queue.Count;

// Clear queue
queue.Clear();
```

### Stack<T>

Last-In, First-Out (LIFO) collection:

```csharp
Stack<string> stack = new Stack<string>();
stack.Push("First");
stack.Push("Second");
stack.Push("Third");

// Peek at top element without removing
string top = stack.Peek();

// Remove and return top element
string popped = stack.Pop();

// Check if contains
bool hasSecond = stack.Contains("Second");
```

### LinkedList<T>

Doubly-linked list for efficient insertions/deletions:

```csharp
LinkedList<string> linkedList = new LinkedList<string>();
linkedList.AddLast("First");
linkedList.AddLast("Second");
linkedList.AddLast("Third");

// Add after specific node
LinkedListNode<string> node = linkedList.Find("Second");
linkedList.AddAfter(node, "Inserted");

// Add before specific node
linkedList.AddBefore(node, "Before Second");

// Remove first/last
linkedList.RemoveFirst();
linkedList.RemoveLast();

// Remove specific value
linkedList.Remove("Second");
```

## Collection Interfaces

### IEnumerable<T>

Enables iteration with `foreach`:

```csharp
public class MyCollection : IEnumerable<int>
{
    private List<int> items = new List<int>();
    
    public void Add(int item) => items.Add(item);
    
    public IEnumerator<int> GetEnumerator()
    {
        return items.GetEnumerator();
    }
    
    IEnumerator IEnumerable.GetEnumerator()
    {
        return GetEnumerator();
    }
}
```

### ICollection<T>

Basic collection operations:

```csharp
ICollection<string> collection = new List<string>();
collection.Add("Item");
collection.Remove("Item");
int count = collection.Count;
bool contains = collection.Contains("Item");
```

### IList<T>

Ordered collection with index access:

```csharp
IList<string> list = new List<string>();
list[0] = "First"; // Index access
list.Insert(1, "Second");
list.RemoveAt(0);
```

## LINQ with Collections

Language Integrated Query (LINQ) provides powerful querying capabilities:

```csharp
List<Person> people = new List<Person>
{
    new Person { Name = "Alice", Age = 25, City = "New York" },
    new Person { Name = "Bob", Age = 30, City = "Chicago" },
    new Person { Name = "Charlie", Age = 35, City = "New York" }
};

// Where - filter
var newYorkers = people.Where(p => p.City == "New York");

// OrderBy - sort
var sortedByAge = people.OrderBy(p => p.Age);

// Select - transform
var names = people.Select(p => p.Name);

// First/FirstOrDefault
var firstPerson = people.First();
var personOrDefault = people.FirstOrDefault(p => p.Age > 40);

// Any/All
bool hasPeople = people.Any();
bool allAdults = people.All(p => p.Age >= 18);

// Count
int count = people.Count(p => p.City == "New York");

// GroupBy
var groupedByCity = people.GroupBy(p => p.City);
```

## Performance Considerations

| Collection | Access Time | Insert Time | Delete Time | Memory Usage |
|------------|-------------|-------------|-------------|--------------|
| List<T> | O(1) | O(n) | O(n) | Low |
| Dictionary<TKey,TValue> | O(1) | O(1) | O(1) | Medium |
| HashSet<T> | O(1) | O(1) | O(1) | Medium |
| Queue<T> | O(1) | O(1) | O(1) | Low |
| Stack<T> | O(1) | O(1) | O(1) | Low |
| LinkedList<T> | O(n) | O(1) | O(1) | High |

## Best Practices

- Use `List<T>` for general-purpose collections
- Use `Dictionary<TKey,TValue>` for key-value lookups
- Use `HashSet<T>` for unique elements
- Use `Queue<T>` for FIFO scenarios
- Use `Stack<T>` for LIFO scenarios
- Use `LinkedList<T>` for frequent insertions/deletions
- Prefer generic collections over non-generic
- Consider using `ReadOnlyCollection<T>` for immutable data
- Use appropriate collection initializers for cleaner code
