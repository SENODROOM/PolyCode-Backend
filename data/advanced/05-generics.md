# Generics in C#

Generics provide type safety and performance benefits by allowing you to write flexible, reusable code that works with multiple data types while maintaining compile-time type checking.

## Generic Basics

### Generic Classes

```csharp
// Generic class with type parameter T
public class GenericContainer<T>
{
    private T item;
    
    public GenericContainer(T item)
    {
        this.item = item;
    }
    
    public T GetItem()
    {
        return item;
    }
    
    public void SetItem(T item)
    {
        this.item = item;
    }
    
    public override string ToString()
    {
        return $"Container holds: {item}";
    }
}

// Usage
var intContainer = new GenericContainer<int>(42);
var stringContainer = new GenericContainer<string>("Hello");
var personContainer = new GenericContainer<Person>(new Person { Name = "Alice" });
```

### Generic Methods

```csharp
public class GenericMethods
{
    // Generic method
    public static void Swap<T>(ref T first, ref T second)
    {
        T temp = first;
        first = second;
        second = temp;
    }
    
    // Generic method with multiple type parameters
    public static bool AreEqual<T, U>(T item1, U item2)
    {
        return item1.Equals(item2);
    }
    
    // Generic method with constraint
    public static T Max<T>(T item1, T item2) where T : IComparable<T>
    {
        return item1.CompareTo(item2) > 0 ? item1 : item2;
    }
}

// Usage
int a = 5, b = 10;
GenericMethods.Swap(ref a, ref b); // a = 10, b = 5

int max = GenericMethods.Max(15, 8); // 15
```

## Generic Constraints

### Where Clause Constraints

```csharp
public class ConstrainedGenerics
{
    // Reference type constraint
    public class ReferenceContainer<T> where T : class
    {
        public T Item { get; set; }
    }
    
    // Value type constraint
    public struct ValueContainer<T> where T : struct
    {
        public T Item { get; set; }
    }
    
    // Interface constraint
    public class ComparableList<T> where T : IComparable<T>
    {
        private List<T> items = new List<T>();
        
        public void Add(T item)
        {
            items.Add(item);
        }
        
        public T GetMax()
        {
            if (items.Count == 0)
                throw new InvalidOperationException("List is empty");
                
            T max = items[0];
            foreach (T item in items.Skip(1))
            {
                if (item.CompareTo(max) > 0)
                    max = item;
            }
            return max;
        }
    }
    
    // Base class constraint
    public class AnimalContainer<T> where T : Animal
    {
        public T Animal { get; set; }
        
        public void MakeAnimalSound()
        {
            Animal.MakeSound();
        }
    }
    
    // Multiple constraints
    public class ComplexConstraint<T> where T : class, IDisposable, new()
    {
        public T CreateAndDispose()
        {
            using (T item = new T())
            {
                return item;
            }
        }
    }
    
    // Unmanaged constraint (C# 7.3+)
    public unsafe static void ProcessUnmanaged<T>() where T : unmanaged
    {
        T value = default(T);
        // Can use pointers with unmanaged types
        T* pointer = &value;
    }
}
```

## Generic Interfaces

### Creating Generic Interfaces

```csharp
public interface IRepository<T>
{
    T GetById(int id);
    IEnumerable<T> GetAll();
    void Add(T item);
    void Update(T item);
    void Delete(int id);
}

public interface ICalculator<T>
{
    T Add(T a, T b);
    T Subtract(T a, T b);
    T Multiply(T a, T b);
}

// Generic interface with constraints
public interface IValidatable<T> where T : class
{
    bool Validate(T item);
    ValidationResult GetValidationResult(T item);
}
```

### Implementing Generic Interfaces

```csharp
public class InMemoryRepository<T> : IRepository<T> where T : class, IEntity
{
    private readonly Dictionary<int, T> items = new Dictionary<int, T>();
    private int nextId = 1;
    
    public T GetById(int id)
    {
        return items.TryGetValue(id, out T item) ? item : null;
    }
    
    public IEnumerable<T> GetAll()
    {
        return items.Values;
    }
    
    public void Add(T item)
    {
        item.Id = nextId++;
        items[item.Id] = item;
    }
    
    public void Update(T item)
    {
        if (items.ContainsKey(item.Id))
        {
            items[item.Id] = item;
        }
    }
    
    public void Delete(int id)
    {
        items.Remove(id);
    }
}

public class MathCalculator : ICalculator<int>, ICalculator<double>
{
    public int Add(int a, int b) => a + b;
    public int Subtract(int a, int b) => a - b;
    public int Multiply(int a, int b) => a * b;
    
    public double Add(double a, double b) => a + b;
    public double Subtract(double a, double b) => a - b;
    public double Multiply(double a, double b) => a * b;
}
```

## Generic Collections

### Custom Generic Collection

```csharp
public class GenericStack<T>
{
    private T[] items;
    private int top;
    private readonly int capacity;
    
    public GenericStack(int capacity = 10)
    {
        this.capacity = capacity;
        items = new T[capacity];
        top = -1;
    }
    
    public void Push(T item)
    {
        if (top >= capacity - 1)
            throw new InvalidOperationException("Stack is full");
            
        items[++top] = item;
    }
    
    public T Pop()
    {
        if (top < 0)
            throw new InvalidOperationException("Stack is empty");
            
        return items[top--];
    }
    
    public T Peek()
    {
        if (top < 0)
            throw new InvalidOperationException("Stack is empty");
            
        return items[top];
    }
    
    public bool IsEmpty => top < 0;
    public int Count => top + 1;
}

public class GenericQueue<T>
{
    private readonly LinkedList<T> items = new LinkedList<T>();
    
    public void Enqueue(T item)
    {
        items.AddLast(item);
    }
    
    public T Dequeue()
    {
        if (items.Count == 0)
            throw new InvalidOperationException("Queue is empty");
            
        T item = items.First.Value;
        items.RemoveFirst();
        return item;
    }
    
    public T Peek()
    {
        if (items.Count == 0)
            throw new InvalidOperationException("Queue is empty");
            
        return items.First.Value;
    }
    
    public int Count => items.Count;
    public bool IsEmpty => items.Count == 0;
}
```

## Generic Delegates and Events

### Generic Delegates

```csharp
public delegate T GenericTransformer<T>(T item);
public delegate bool GenericPredicate<T>(T item);
public delegate void GenericEventHandler<T>(T sender, EventArgs e);

public class GenericProcessor<T>
{
    public event GenericEventHandler<T> Processed;
    
    public List<T> Filter(List<T> items, GenericPredicate<T> predicate)
    {
        var result = new List<T>();
        foreach (T item in items)
        {
            if (predicate(item))
            {
                result.Add(item);
            }
        }
        return result;
    }
    
    public List<T> Transform(List<T> items, GenericTransformer<T> transformer)
    {
        var result = new List<T>();
        foreach (T item in items)
        {
            T transformed = transformer(item);
            result.Add(transformed);
            OnProcessed(transformed);
        }
        return result;
    }
    
    protected virtual void OnProcessed(T item)
    {
        Processed?.Invoke(item, EventArgs.Empty);
    }
}
```

## Variance in Generics

### Covariance and Contravariance

```csharp
// Covariant interface (out keyword)
public interface ICovariant<out T>
{
    T GetItem();
}

public class CovariantContainer<T> : ICovariant<T>
{
    private T item;
    
    public CovariantContainer(T item)
    {
        this.item = item;
    }
    
    public T GetItem() => item;
}

// Contravariant interface (in keyword)
public interface IContravariant<in T>
{
    void SetItem(T item);
}

public class ContravariantContainer<T> : IContravariant<T>
{
    private T item;
    
    public void SetItem(T item)
    {
        this.item = item;
    }
}

// Usage
ICovariant<object> objContainer = new CovariantContainer<string>("Hello"); // Covariance
IContravariant<string> strContainer = new ContravariantContainer<object>(); // Contravariance
```

## Advanced Generic Patterns

### Generic Factory Pattern

```csharp
public class GenericFactory<T> where T : class, new()
{
    public static T Create()
    {
        return new T();
    }
    
    public static T CreateWithParameters(params object[] args)
    {
        return (T)Activator.CreateInstance(typeof(T), args);
    }
}

// Generic singleton pattern
public class GenericSingleton<T> where T : class, new()
{
    private static readonly Lazy<T> instance = new Lazy<T>(() => new T());
    
    public static T Instance => instance.Value;
}
```

### Generic Builder Pattern

```csharp
public class GenericBuilder<T>
{
    private readonly T item;
    
    public GenericBuilder()
    {
        item = Activator.CreateInstance<T>();
    }
    
    public GenericBuilder<T> With<TProperty>(Expression<Func<T, TProperty>> property, TProperty value)
    {
        var propertyInfo = (PropertyInfo)((MemberExpression)property.Body).Member;
        propertyInfo.SetValue(item, value);
        return this;
    }
    
    public T Build() => item;
}

// Usage
var person = new GenericBuilder<Person>()
    .With(p => p.Name, "Alice")
    .With(p => p.Age, 30)
    .Build();
```

## Generic Reflection

### Working with Generic Types

```csharp
public class GenericReflection
{
    public static void AnalyzeGenericType(Type type)
    {
        if (type.IsGenericType)
        {
            Console.WriteLine($"Generic type: {type.Name}");
            Console.WriteLine($"Generic type definition: {type.GetGenericTypeDefinition().Name}");
            
            Type[] typeArguments = type.GetGenericArguments();
            Console.WriteLine("Type arguments:");
            foreach (Type arg in typeArguments)
            {
                Console.WriteLine($"  {arg.Name}");
            }
        }
    }
    
    public static object CreateGenericInstance(Type genericType, params Type[] typeArguments)
    {
        Type constructedType = genericType.MakeGenericType(typeArguments);
        return Activator.CreateInstance(constructedType);
    }
    
    public static void InvokeGenericMethod(object instance, string methodName, Type[] typeArguments, params object[] parameters)
    {
        MethodInfo method = instance.GetType().GetMethod(methodName);
        MethodInfo genericMethod = method.MakeGenericMethod(typeArguments);
        genericMethod.Invoke(instance, parameters);
    }
}
```

## Performance Considerations

### Generic vs Non-Generic

```csharp
public class PerformanceComparison
{
    // Generic version - type-safe, no boxing
    public static T GenericMax<T>(T item1, T item2) where T : IComparable<T>
    {
        return item1.CompareTo(item2) > 0 ? item1 : item2;
    }
    
    // Non-generic version - requires boxing for value types
    public static object NonGenericMax(object item1, object item2)
    {
        IComparable comparable1 = (IComparable)item1;
        IComparable comparable2 = (IComparable)item2;
        return comparable1.CompareTo(comparable2) > 0 ? item1 : item2;
    }
}
```

## Best Practices

### DO:
- Use generics for type safety and performance
- Apply constraints to limit acceptable types
- Consider `IEnumerable<T>` over `IEnumerable` when possible
- Use generic interfaces for reusable APIs
- Consider variance (`in`, `out`) for interfaces
- Use generic collections instead of non-generic ones

### DON'T:
- Overuse generics when simple types suffice
- Forget to specify constraints when needed
- Mix generic and non-generic code unnecessarily
- Use `object` when generics would be more appropriate
- Ignore performance implications of boxing/unboxing

## Common Generic Types in .NET

### Built-in Generic Types

```csharp
// Collections
List<T>, Dictionary<TKey, TValue>, HashSet<T>, Queue<T>, Stack<T>

// Delegates
Action<T>, Func<T>, Predicate<T>, Comparison<T>

// Interfaces
IEnumerable<T>, IList<T>, ICollection<T>, IComparable<T>, IEquatable<T>

// Tasks and async
Task<T>, ValueTask<T>, IAsyncEnumerable<T>

// Nullable
Nullable<T> (or T?)

// Tuples
ValueTuple<T1, T2>, Tuple<T1, T2>
```

Generics are a fundamental feature of modern C# programming, enabling type-safe, reusable, and high-performance code across a wide variety of scenarios.
