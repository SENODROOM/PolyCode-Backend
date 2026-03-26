using System;
using System.Collections.Generic;
using System.Linq;

namespace IntermediateDemo
{
    public class Person
    {
        public string Name { get; set; }
        public int Age { get; set; }
        public string City { get; set; }
        
        public override string ToString()
        {
            return $"{Name} ({Age}) from {City}";
        }
    }
    
    public class Product
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Category { get; set; }
        
        public override string ToString()
        {
            return $"{Name} - ${Price:F2} ({Category})";
        }
    }
    
    class CollectionsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Collections Demo ===\n");
            
            // 1. List<T>
            DemonstrateList();
            
            // 2. Dictionary<TKey, TValue>
            DemonstrateDictionary();
            
            // 3. HashSet<T>
            DemonstrateHashSet();
            
            // 4. Queue<T>
            DemonstrateQueue();
            
            // 5. Stack<T>
            DemonstrateStack();
            
            // 6. LinkedList<T>
            DemonstrateLinkedList();
            
            // 7. LINQ with Collections
            DemonstrateLINQ();
            
            // 8. Complex collection operations
            DemonstrateComplexOperations();
        }
        
        static void DemonstrateList()
        {
            Console.WriteLine("1. List<T> Operations:");
            
            List<string> fruits = new List<string>();
            
            // Add elements
            fruits.Add("Apple");
            fruits.Add("Banana");
            fruits.Add("Orange");
            fruits.Insert(1, "Grape");
            
            Console.WriteLine("Initial list:");
            fruits.ForEach(f => Console.WriteLine($"  {f}"));
            
            // Access by index
            Console.WriteLine($"\nFirst fruit: {fruits[0]}");
            Console.WriteLine($"Last fruit: {fruits[fruits.Count - 1]}");
            
            // Remove elements
            fruits.Remove("Banana");
            fruits.RemoveAt(0);
            
            Console.WriteLine("\nAfter removals:");
            fruits.ForEach(f => Console.WriteLine($"  {f}"));
            
            // Sort and reverse
            fruits.Sort();
            Console.WriteLine("\nSorted:");
            fruits.ForEach(f => Console.WriteLine($"  {f}"));
            
            fruits.Reverse();
            Console.WriteLine("\nReversed:");
            fruits.ForEach(f => Console.WriteLine($"  {f}"));
            
            // Contains and index
            bool hasApple = fruits.Contains("Apple");
            int orangeIndex = fruits.IndexOf("Orange");
            Console.WriteLine($"\nContains Apple: {hasApple}");
            Console.WriteLine($"Orange index: {orangeIndex}");
            
            Console.WriteLine();
        }
        
        static void DemonstrateDictionary()
        {
            Console.WriteLine("2. Dictionary<TKey, TValue> Operations:");
            
            Dictionary<string, int> studentGrades = new Dictionary<string, int>();
            
            // Add key-value pairs
            studentGrades["Alice"] = 95;
            studentGrades["Bob"] = 87;
            studentGrades.Add("Charlie", 92);
            
            Console.WriteLine("Student grades:");
            foreach (var kvp in studentGrades)
            {
                Console.WriteLine($"  {kvp.Key}: {kvp.Value}");
            }
            
            // Access values
            Console.WriteLine($"\nAlice's grade: {studentGrades["Alice"]}");
            
            // Safe access with TryGetValue
            if (studentGrades.TryGetValue("David", out int davidsGrade))
            {
                Console.WriteLine($"David's grade: {davidsGrade}");
            }
            else
            {
                Console.WriteLine("David not found in dictionary");
            }
            
            // Check if key exists
            Console.WriteLine($"Contains Bob: {studentGrades.ContainsKey("Bob")}");
            Console.WriteLine($"Contains grade 90: {studentGrades.ContainsValue(90)}");
            
            // Update value
            studentGrades["Alice"] = 98;
            Console.WriteLine($"\nAlice's updated grade: {studentGrades["Alice"]}");
            
            // Remove key-value pair
            studentGrades.Remove("Bob");
            Console.WriteLine("\nAfter removing Bob:");
            foreach (var kvp in studentGrades)
            {
                Console.WriteLine($"  {kvp.Key}: {kvp.Value}");
            }
            
            Console.WriteLine();
        }
        
        static void DemonstrateHashSet()
        {
            Console.WriteLine("3. HashSet<T> Operations:");
            
            HashSet<string> uniqueNames = new HashSet<string>();
            
            // Add elements (duplicates are ignored)
            uniqueNames.Add("Alice");
            uniqueNames.Add("Bob");
            uniqueNames.Add("Charlie");
            uniqueNames.Add("Alice"); // Won't be added again
            
            Console.WriteLine("Unique names:");
            foreach (string name in uniqueNames)
            {
                Console.WriteLine($"  {name}");
            }
            
            Console.WriteLine($"\nCount: {uniqueNames.Count}");
            Console.WriteLine($"Contains Alice: {uniqueNames.Contains("Alice")}");
            
            // Set operations
            HashSet<string> set1 = new HashSet<string> { "A", "B", "C", "D" };
            HashSet<string> set2 = new HashSet<string> { "C", "D", "E", "F" };
            
            Console.WriteLine("\nSet1: " + string.Join(", ", set1));
            Console.WriteLine("Set2: " + string.Join(", ", set2));
            
            // Union
            var union = new HashSet<string>(set1);
            union.UnionWith(set2);
            Console.WriteLine("Union: " + string.Join(", ", union));
            
            // Intersection
            var intersection = new HashSet<string>(set1);
            intersection.IntersectWith(set2);
            Console.WriteLine("Intersection: " + string.Join(", ", intersection));
            
            // Difference
            var difference = new HashSet<string>(set1);
            difference.ExceptWith(set2);
            Console.WriteLine("Set1 - Set2: " + string.Join(", ", difference));
            
            Console.WriteLine();
        }
        
        static void DemonstrateQueue()
        {
            Console.WriteLine("4. Queue<T> Operations:");
            
            Queue<string> taskQueue = new Queue<string>();
            
            // Enqueue items
            taskQueue.Enqueue("Task 1");
            taskQueue.Enqueue("Task 2");
            taskQueue.Enqueue("Task 3");
            
            Console.WriteLine("Queue contents:");
            foreach (string task in taskQueue)
            {
                Console.WriteLine($"  {task}");
            }
            
            // Peek at first item
            Console.WriteLine($"\nNext task: {taskQueue.Peek()}");
            Console.WriteLine($"Count after peek: {taskQueue.Count}");
            
            // Dequeue items
            Console.WriteLine("\nProcessing tasks:");
            while (taskQueue.Count > 0)
            {
                string task = taskQueue.Dequeue();
                Console.WriteLine($"  Processing: {task}");
                Console.WriteLine($"  Remaining: {taskQueue.Count}");
            }
            
            Console.WriteLine();
        }
        
        static void DemonstrateStack()
        {
            Console.WriteLine("5. Stack<T> Operations:");
            
            Stack<string> browserHistory = new Stack<string>();
            
            // Push items
            browserHistory.Push("Page 1");
            browserHistory.Push("Page 2");
            browserHistory.Push("Page 3");
            
            Console.WriteLine("Browser history (top to bottom):");
            foreach (string page in browserHistory)
            {
                Console.WriteLine($"  {page}");
            }
            
            // Peek at top item
            Console.WriteLine($"\nCurrent page: {browserHistory.Peek()}");
            
            // Pop items (going back in history)
            Console.WriteLine("\nGoing back in history:");
            while (browserHistory.Count > 0)
            {
                string page = browserHistory.Pop();
                Console.WriteLine($"  Going back from: {page}");
                Console.WriteLine($"  Pages left: {browserHistory.Count}");
            }
            
            Console.WriteLine();
        }
        
        static void DemonstrateLinkedList()
        {
            Console.WriteLine("6. LinkedList<T> Operations:");
            
            LinkedList<string> playlist = new LinkedList<string>();
            
            // Add nodes
            playlist.AddLast("Song 1");
            playlist.AddLast("Song 2");
            playlist.AddLast("Song 3");
            
            Console.WriteLine("Initial playlist:");
            foreach (string song in playlist)
            {
                Console.WriteLine($"  {song}");
            }
            
            // Find and add after
            LinkedListNode<string> node = playlist.Find("Song 2");
            if (node != null)
            {
                playlist.AddAfter(node, "Song 2.5");
            }
            
            // Add before first
            playlist.AddFirst("Song 0.5");
            
            Console.WriteLine("\nModified playlist:");
            foreach (string song in playlist)
            {
                Console.WriteLine($"  {song}");
            }
            
            // Remove specific song
            playlist.Remove("Song 2");
            
            // Remove first and last
            playlist.RemoveFirst();
            playlist.RemoveLast();
            
            Console.WriteLine("\nFinal playlist:");
            foreach (string song in playlist)
            {
                Console.WriteLine($"  {song}");
            }
            
            Console.WriteLine();
        }
        
        static void DemonstrateLINQ()
        {
            Console.WriteLine("7. LINQ with Collections:");
            
            List<Person> people = new List<Person>
            {
                new Person { Name = "Alice", Age = 25, City = "New York" },
                new Person { Name = "Bob", Age = 30, City = "Chicago" },
                new Person { Name = "Charlie", Age = 35, City = "New York" },
                new Person { Name = "Diana", Age = 28, City = "Chicago" },
                new Person { Name = "Eve", Age = 22, City = "Los Angeles" }
            };
            
            // Where - filter
            var newYorkers = people.Where(p => p.City == "New York");
            Console.WriteLine("People from New York:");
            foreach (var person in newYorkers)
            {
                Console.WriteLine($"  {person}");
            }
            
            // OrderBy - sort
            var sortedByAge = people.OrderBy(p => p.Age);
            Console.WriteLine("\nPeople sorted by age:");
            foreach (var person in sortedByAge)
            {
                Console.WriteLine($"  {person}");
            }
            
            // Select - transform
            var names = people.Select(p => p.Name);
            Console.WriteLine("\nJust names:");
            Console.WriteLine("  " + string.Join(", ", names));
            
            // GroupBy
            var groupedByCity = people.GroupBy(p => p.City);
            Console.WriteLine("\nPeople grouped by city:");
            foreach (var group in groupedByCity)
            {
                Console.WriteLine($"  {group.Key}: {string.Join(", ", group.Select(p => p.Name))}");
            }
            
            // Aggregate functions
            int totalPeople = people.Count();
            int averageAge = (int)people.Average(p => p.Age);
            int oldestAge = people.Max(p => p.Age);
            int youngestAge = people.Min(p => p.Age);
            
            Console.WriteLine($"\nStatistics:");
            Console.WriteLine($"  Total people: {totalPeople}");
            Console.WriteLine($"  Average age: {averageAge}");
            Console.WriteLine($"  Oldest age: {oldestAge}");
            Console.WriteLine($"  Youngest age: {youngestAge}");
            
            Console.WriteLine();
        }
        
        static void DemonstrateComplexOperations()
        {
            Console.WriteLine("8. Complex Collection Operations:");
            
            // Create a dictionary of lists
            Dictionary<string, List<Product>> productsByCategory = new Dictionary<string, List<Product>>();
            
            List<Product> products = new List<Product>
            {
                new Product { Id = 1, Name = "Laptop", Price = 999.99m, Category = "Electronics" },
                new Product { Id = 2, Name = "Mouse", Price = 29.99m, Category = "Electronics" },
                new Product { Id = 3, Name = "Book", Price = 19.99m, Category = "Books" },
                new Product { Id = 4, Name = "Pen", Price = 2.99m, Category = "Books" },
                new Product { Id = 5, Name = "Desk", Price = 199.99m, Category = "Furniture" }
            };
            
            // Group products by category
            foreach (Product product in products)
            {
                if (!productsByCategory.ContainsKey(product.Category))
                {
                    productsByCategory[product.Category] = new List<Product>();
                }
                productsByCategory[product.Category].Add(product);
            }
            
            Console.WriteLine("Products by category:");
            foreach (var category in productsByCategory)
            {
                Console.WriteLine($"\n{category.Key}:");
                foreach (var product in category.Value)
                {
                    Console.WriteLine($"  {product}");
                }
                
                // Calculate category statistics
                decimal totalValue = category.Value.Sum(p => p.Price);
                decimal avgPrice = category.Value.Average(p => p.Price);
                Console.WriteLine($"  Total value: ${totalValue:F2}");
                Console.WriteLine($"  Average price: ${avgPrice:F2}");
            }
            
            // Find expensive products
            var expensiveProducts = products.Where(p => p.Price > 100).OrderByDescending(p => p.Price);
            Console.WriteLine("\nExpensive products (> $100):");
            foreach (var product in expensiveProducts)
            {
                Console.WriteLine($"  {product}");
            }
            
            // Create lookup table
            var productLookup = products.ToLookup(p => p.Category);
            Console.WriteLine("\nUsing lookup table:");
            var electronics = productLookup["Electronics"];
            foreach (var product in electronics)
            {
                Console.WriteLine($"  {product}");
            }
        }
    }
}
