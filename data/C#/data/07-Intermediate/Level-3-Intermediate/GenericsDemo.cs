using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;

namespace AdvancedDemo
{
    // Basic entity interface for repository example
    public interface IEntity
    {
        int Id { get; set; }
    }
    
    public class Person : IEntity, IComparable<Person>
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public string Email { get; set; }
        
        public int CompareTo(Person other)
        {
            return string.Compare(Name, other.Name, StringComparison.OrdinalIgnoreCase);
        }
        
        public override string ToString()
        {
            return $"{Name} (Age: {Age}, Email: {Email})";
        }
    }
    
    public class Product : IEntity, IComparable<Product>
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Category { get; set; }
        
        public int CompareTo(Product other)
        {
            return Price.CompareTo(other.Price);
        }
        
        public override string ToString()
        {
            return $"{Name} - ${Price:F2} ({Category})";
        }
    }
    
    // Generic container class
    public class GenericContainer<T>
    {
        private T item;
        
        public GenericContainer(T item)
        {
            this.item = item;
        }
        
        public T GetItem() => item;
        
        public void SetItem(T item)
        {
            this.item = item;
        }
        
        public override string ToString()
        {
            return $"Container holds: {item}";
        }
    }
    
    // Generic stack implementation
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
    
    // Generic repository interface
    public interface IRepository<T> where T : class, IEntity
    {
        T GetById(int id);
        IEnumerable<T> GetAll();
        void Add(T item);
        void Update(T item);
        void Delete(int id);
        int Count { get; }
    }
    
    // In-memory repository implementation
    public class InMemoryRepository<T> : IRepository<T> where T : class, IEntity, new()
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
        
        public int Count => items.Count;
    }
    
    // Generic processor with constraints
    public class GenericProcessor<T> where T : IComparable<T>
    {
        private readonly List<T> items = new List<T>();
        
        public void Add(T item)
        {
            items.Add(item);
        }
        
        public T GetMax()
        {
            if (items.Count == 0)
                throw new InvalidOperationException("No items to process");
                
            T max = items[0];
            foreach (T item in items.Skip(1))
            {
                if (item.CompareTo(max) > 0)
                    max = item;
            }
            return max;
        }
        
        public T GetMin()
        {
            if (items.Count == 0)
                throw new InvalidOperationException("No items to process");
                
            T min = items[0];
            foreach (T item in items.Skip(1))
            {
                if (item.CompareTo(min) < 0)
                    min = item;
            }
            return min;
        }
        
        public List<T> GetSorted()
        {
            return items.OrderBy(item => item).ToList();
        }
    }
    
    // Generic factory
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
    
    // Generic builder pattern
    public class GenericBuilder<T> where T : class, new()
    {
        private readonly T item;
        
        public GenericBuilder()
        {
            item = new T();
        }
        
        public GenericBuilder<T> With<TProperty>(Expression<Func<T, TProperty>> property, TProperty value)
        {
            var propertyInfo = (PropertyInfo)((MemberExpression)property.Body).Member;
            propertyInfo.SetValue(item, value);
            return this;
        }
        
        public T Build() => item;
    }
    
    // Generic calculator with interface constraints
    public interface ICalculator<T>
    {
        T Add(T a, T b);
        T Subtract(T a, T b);
        T Multiply(T a, T b);
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
    
    class GenericsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Generics Demo ===\n");
            
            // 1. Basic generic container
            Console.WriteLine("1. Basic Generic Container:");
            DemonstrateGenericContainer();
            
            // 2. Generic stack
            Console.WriteLine("\n2. Generic Stack:");
            DemonstrateGenericStack();
            
            // 3. Generic repository
            Console.WriteLine("\n3. Generic Repository:");
            DemonstrateGenericRepository();
            
            // 4. Generic processor with constraints
            Console.WriteLine("\n4. Generic Processor with Constraints:");
            DemonstrateGenericProcessor();
            
            // 5. Generic factory
            Console.WriteLine("\n5. Generic Factory:");
            DemonstrateGenericFactory();
            
            // 6. Generic builder
            Console.WriteLine("\n6. Generic Builder:");
            DemonstrateGenericBuilder();
            
            // 7. Generic calculator
            Console.WriteLine("\n7. Generic Calculator:");
            DemonstrateGenericCalculator();
            
            // 8. Generic methods
            Console.WriteLine("\n8. Generic Methods:");
            DemonstrateGenericMethods();
            
            // 9. Generic constraints
            Console.WriteLine("\n9. Generic Constraints:");
            DemonstrateGenericConstraints();
        }
        
        static void DemonstrateGenericContainer()
        {
            var intContainer = new GenericContainer<int>(42);
            var stringContainer = new GenericContainer<string>("Hello Generics!");
            var personContainer = new GenericContainer<Person>(new Person 
            { 
                Name = "Alice", 
                Age = 30, 
                Email = "alice@example.com" 
            });
            
            Console.WriteLine($"  {intContainer}");
            Console.WriteLine($"  {stringContainer}");
            Console.WriteLine($"  {personContainer}");
        }
        
        static void DemonstrateGenericStack()
        {
            var intStack = new GenericStack<int>();
            var stringStack = new GenericStack<string>();
            
            // Push items
            Console.WriteLine("  Pushing items to integer stack:");
            for (int i = 1; i <= 5; i++)
            {
                intStack.Push(i);
                Console.WriteLine($"    Pushed: {i}");
            }
            
            // Pop items
            Console.WriteLine("\n  Popping items from integer stack:");
            while (!intStack.IsEmpty)
            {
                int item = intStack.Pop();
                Console.WriteLine($"    Popped: {item}");
            }
            
            // String stack
            stringStack.Push("First");
            stringStack.Push("Second");
            stringStack.Push("Third");
            
            Console.WriteLine("\n  String stack peek: " + stringStack.Peek());
            Console.WriteLine("  String stack count: " + stringStack.Count);
        }
        
        static void DemonstrateGenericRepository()
        {
            var personRepo = new InMemoryRepository<Person>();
            var productRepo = new InMemoryRepository<Product>();
            
            // Add people
            personRepo.Add(new Person { Name = "Alice", Age = 28, Email = "alice@email.com" });
            personRepo.Add(new Person { Name = "Bob", Age = 35, Email = "bob@email.com" });
            personRepo.Add(new Person { Name = "Charlie", Age = 42, Email = "charlie@email.com" });
            
            // Add products
            productRepo.Add(new Product { Name = "Laptop", Price = 999.99m, Category = "Electronics" });
            productRepo.Add(new Product { Name = "Mouse", Price = 29.99m, Category = "Electronics" });
            productRepo.Add(new Product { Name = "Desk", Price = 299.99m, Category = "Furniture" });
            
            Console.WriteLine("  People in repository:");
            foreach (var person in personRepo.GetAll())
            {
                Console.WriteLine($"    {person}");
            }
            
            Console.WriteLine("\n  Products in repository:");
            foreach (var product in productRepo.GetAll())
            {
                Console.WriteLine($"    {product}");
            }
            
            Console.WriteLine($"\n  Person count: {personRepo.Count}");
            Console.WriteLine($"  Product count: {productRepo.Count}");
            
            // Get by ID
            var person = personRepo.GetById(1);
            Console.WriteLine($"\n  Person with ID 1: {person}");
        }
        
        static void DemonstrateGenericProcessor()
        {
            var personProcessor = new GenericProcessor<Person>();
            var productProcessor = new GenericProcessor<Product>();
            
            // Add people
            personProcessor.Add(new Person { Name = "Alice", Age = 28, Email = "alice@email.com" });
            personProcessor.Add(new Person { Name = "Bob", Age = 35, Email = "bob@email.com" });
            personProcessor.Add(new Person { Name = "Charlie", Age = 42, Email = "charlie@email.com" });
            
            // Add products
            productProcessor.Add(new Product { Name = "Laptop", Price = 999.99m, Category = "Electronics" });
            productProcessor.Add(new Product { Name = "Mouse", Price = 29.99m, Category = "Electronics" });
            productProcessor.Add(new Product { Name = "Desk", Price = 299.99m, Category = "Furniture" });
            
            Console.WriteLine("  Person processor results:");
            Console.WriteLine($"    Max (by name): {personProcessor.GetMax()}");
            Console.WriteLine($"    Min (by name): {personProcessor.GetMin()}");
            Console.WriteLine($"    Sorted: {string.Join(", ", personProcessor.GetSorted())}");
            
            Console.WriteLine("\n  Product processor results:");
            Console.WriteLine($"    Max (by price): {productProcessor.GetMax()}");
            Console.WriteLine($"    Min (by price): {productProcessor.GetMin()}");
            Console.WriteLine($"    Sorted: {string.Join(", ", productProcessor.GetSorted())}");
        }
        
        static void DemonstrateGenericFactory()
        {
            var person1 = GenericFactory<Person>.Create();
            person1.Name = "Factory Person";
            person1.Age = 25;
            person1.Email = "factory@email.com";
            
            Console.WriteLine($"  Created person: {person1}");
            
            // Note: CreateWithParameters would require constructors to be defined
            Console.WriteLine("  Generic factory creates instances with default constructor");
        }
        
        static void DemonstrateGenericBuilder()
        {
            var person = new GenericBuilder<Person>()
                .With(p => p.Name, "Builder Person")
                .With(p => p.Age, 32)
                .With(p => p.Email, "builder@email.com")
                .Build();
            
            Console.WriteLine($"  Built person: {person}");
            
            var product = new GenericBuilder<Product>()
                .With(p => p.Name, "Builder Product")
                .With(p => p.Price, 199.99m)
                .With(p => p.Category, "Test")
                .Build();
            
            Console.WriteLine($"  Built product: {product}");
        }
        
        static void DemonstrateGenericCalculator()
        {
            var calculator = new MathCalculator();
            
            Console.WriteLine("  Integer calculations:");
            Console.WriteLine($"    5 + 3 = {calculator.Add(5, 3)}");
            Console.WriteLine($"    10 - 4 = {calculator.Subtract(10, 4)}");
            Console.WriteLine($"    6 * 7 = {calculator.Multiply(6, 7)}");
            
            Console.WriteLine("\n  Double calculations:");
            Console.WriteLine($"    5.5 + 3.2 = {calculator.Add(5.5, 3.2)}");
            Console.WriteLine($"    10.0 - 4.5 = {calculator.Subtract(10.0, 4.5)}");
            Console.WriteLine($"    6.5 * 7.2 = {calculator.Multiply(6.5, 7.2)}");
        }
        
        static void DemonstrateGenericMethods()
        {
            // Generic swap method
            int a = 10, b = 20;
            Console.WriteLine($"  Before swap: a = {a}, b = {b}");
            Swap(ref a, ref b);
            Console.WriteLine($"  After swap: a = {a}, b = {b}");
            
            string str1 = "Hello", str2 = "World";
            Console.WriteLine($"  Before swap: str1 = {str1}, str2 = {str2}");
            Swap(ref str1, ref str2);
            Console.WriteLine($"  After swap: str1 = {str1}, str2 = {str2}");
            
            // Generic max method
            Console.WriteLine($"\n  Max of 15 and 8: {GenericMax(15, 8)}");
            Console.WriteLine($"  Max of 'Apple' and 'Banana': {GenericMax("Apple", "Banana")}");
        }
        
        static void DemonstrateGenericConstraints()
        {
            Console.WriteLine("  Demonstrating different constraint types:");
            
            // Reference type constraint
            var refContainer = new ReferenceContainer<string>("Reference type");
            Console.WriteLine($"    Reference container: {refContainer.GetItem()}");
            
            // Value type constraint
            var valContainer = new ValueContainer<int>(42);
            Console.WriteLine($"    Value container: {valContainer.GetItem()}");
            
            // Interface constraint
            var comparableList = new ComparableList<int>();
            comparableList.Add(5);
            comparableList.Add(2);
            comparableList.Add(8);
            comparableList.Add(1);
            Console.WriteLine($"    Max in comparable list: {comparableList.GetMax()}");
        }
        
        // Generic methods
        static void Swap<T>(ref T first, ref T second)
        {
            T temp = first;
            first = second;
            second = temp;
        }
        
        static T GenericMax<T>(T item1, T item2) where T : IComparable<T>
        {
            return item1.CompareTo(item2) > 0 ? item1 : item2;
        }
    }
    
    // Supporting classes for constraint demonstration
    public class ReferenceContainer<T> where T : class
    {
        private T item;
        
        public ReferenceContainer(T item)
        {
            this.item = item;
        }
        
        public T GetItem() => item;
    }
    
    public struct ValueContainer<T> where T : struct
    {
        private T item;
        
        public ValueContainer(T item)
        {
            this.item = item;
        }
        
        public T GetItem() => item;
    }
    
    public class ComparableList<T> where T : IComparable<T>
    {
        private readonly List<T> items = new List<T>();
        
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
}
