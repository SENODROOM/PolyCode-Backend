using System;
using System.Collections.Generic;
using System.Linq;

namespace AdvancedDemo
{
    public class Person
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public string City { get; set; }
        public string Email { get; set; }
        public decimal Salary { get; set; }
        public DateTime HireDate { get; set; }
        
        public override string ToString()
        {
            return $"{Name} ({Age}) from {City}";
        }
    }
    
    public class Order
    {
        public int Id { get; set; }
        public int CustomerId { get; set; }
        public int ProductId { get; set; }
        public decimal Amount { get; set; }
        public DateTime OrderDate { get; set; }
        public string Status { get; set; }
    }
    
    public class Product
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Category { get; set; }
        public int Stock { get; set; }
    }
    
    public class Department
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Manager { get; set; }
        public decimal Budget { get; set; }
    }
    
    class LINQDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== LINQ Demo ===\n");
            
            // Create sample data
            var people = CreateSamplePeople();
            var orders = CreateSampleOrders();
            var products = CreateSampleProducts();
            var departments = CreateSampleDepartments();
            
            // 1. Basic LINQ operations
            Console.WriteLine("1. Basic LINQ Operations:");
            DemonstrateBasicOperations(people);
            
            // 2. Aggregation operations
            Console.WriteLine("\n2. Aggregation Operations:");
            DemonstrateAggregation(people, orders);
            
            // 3. Grouping operations
            Console.WriteLine("\n3. Grouping Operations:");
            DemonstrateGrouping(people, orders);
            
            // 4. Join operations
            Console.WriteLine("\n4. Join Operations:");
            DemonstrateJoins(people, orders, products);
            
            // 5. Set operations
            Console.WriteLine("\n5. Set Operations:");
            DemonstrateSetOperations();
            
            // 6. Partitioning operations
            Console.WriteLine("\n6. Partitioning Operations:");
            DemonstratePartitioning(people);
            
            // 7. Element operations
            Console.WriteLine("\n7. Element Operations:");
            DemonstrateElementOperations(people);
            
            // 8. Quantifier operations
            Console.WriteLine("\n8. Quantifier Operations:");
            DemonstrateQuantifierOperations(people);
            
            // 9. Complex queries
            Console.WriteLine("\n9. Complex Queries:");
            DemonstrateComplexQueries(people, orders, products, departments);
            
            // 10. Query syntax vs method syntax
            Console.WriteLine("\n10. Query Syntax vs Method Syntax:");
            DemonstrateQuerySyntax(people);
        }
        
        static List<Person> CreateSamplePeople()
        {
            return new List<Person>
            {
                new Person { Id = 1, Name = "Alice Johnson", Age = 28, City = "New York", Email = "alice@email.com", Salary = 75000, HireDate = new DateTime(2020, 3, 15) },
                new Person { Id = 2, Name = "Bob Smith", Age = 35, City = "Chicago", Email = "bob@email.com", Salary = 85000, HireDate = new DateTime(2018, 7, 22) },
                new Person { Id = 3, Name = "Charlie Brown", Age = 42, City = "New York", Email = "charlie@email.com", Salary = 95000, HireDate = new DateTime(2015, 1, 10) },
                new Person { Id = 4, Name = "Diana Prince", Age = 31, City = "Los Angeles", Email = "diana@email.com", Salary = 80000, HireDate = new DateTime(2019, 11, 5) },
                new Person { Id = 5, Name = "Eve Wilson", Age = 26, City = "Chicago", Email = "eve@email.com", Salary = 70000, HireDate = new DateTime(2021, 2, 14) },
                new Person { Id = 6, Name = "Frank Miller", Age = 38, City = "Los Angeles", Email = "frank@email.com", Salary = 90000, HireDate = new DateTime(2017, 9, 30) },
                new Person { Id = 7, Name = "Grace Lee", Age = 29, City = "New York", Email = "grace@email.com", Salary = 78000, HireDate = new DateTime(2020, 6, 18) },
                new Person { Id = 8, Name = "Henry Ford", Age = 45, City = "Chicago", Email = "henry@email.com", Salary = 100000, HireDate = new DateTime(2014, 4, 8) }
            };
        }
        
        static List<Order> CreateSampleOrders()
        {
            return new List<Order>
            {
                new Order { Id = 1, CustomerId = 1, ProductId = 1, Amount = 299.99m, OrderDate = DateTime.Now.AddDays(-10), Status = "Completed" },
                new Order { Id = 2, CustomerId = 2, ProductId = 2, Amount = 599.99m, OrderDate = DateTime.Now.AddDays(-8), Status = "Completed" },
                new Order { Id = 3, CustomerId = 1, ProductId = 3, Amount = 199.99m, OrderDate = DateTime.Now.AddDays(-5), Status = "Processing" },
                new Order { Id = 4, CustomerId = 3, ProductId = 1, Amount = 299.99m, OrderDate = DateTime.Now.AddDays(-3), Status = "Completed" },
                new Order { Id = 5, CustomerId = 4, ProductId = 4, Amount = 899.99m, OrderDate = DateTime.Now.AddDays(-2), Status = "Processing" },
                new Order { Id = 6, CustomerId = 2, ProductId = 3, Amount = 199.99m, OrderDate = DateTime.Now.AddDays(-1), Status = "Shipped" },
                new Order { Id = 7, CustomerId = 5, ProductId = 2, Amount = 599.99m, OrderDate = DateTime.Now, Status = "Pending" }
            };
        }
        
        static List<Product> CreateSampleProducts()
        {
            return new List<Product>
            {
                new Product { Id = 1, Name = "Laptop", Price = 299.99m, Category = "Electronics", Stock = 50 },
                new Product { Id = 2, Name = "Smartphone", Price = 599.99m, Category = "Electronics", Stock = 100 },
                new Product { Id = 3, Name = "Headphones", Price = 199.99m, Category = "Electronics", Stock = 75 },
                new Product { Id = 4, Name = "Desk Chair", Price = 899.99m, Category = "Furniture", Stock = 25 }
            };
        }
        
        static List<Department> CreateSampleDepartments()
        {
            return new List<Department>
            {
                new Department { Id = 1, Name = "Engineering", Manager = "Charlie Brown", Budget = 500000 },
                new Department { Id = 2, Name = "Sales", Manager = "Bob Smith", Budget = 300000 },
                new Department { Id = 3, Name = "Marketing", Manager = "Diana Prince", Budget = 200000 }
            };
        }
        
        static void DemonstrateBasicOperations(List<Person> people)
        {
            // Where - filtering
            var adults = people.Where(p => p.Age >= 30);
            Console.WriteLine($"Adults (30+): {string.Join(", ", adults.Select(p => p.Name))}");
            
            // OrderBy - sorting
            var sortedByName = people.OrderBy(p => p.Name);
            Console.WriteLine($"Sorted by name: {string.Join(", ", sortedByName.Select(p => p.Name))}");
            
            // Select - projection
            var names = people.Select(p => p.Name);
            Console.WriteLine($"All names: {string.Join(", ", names)}");
            
            // Anonymous object projection
            var personInfo = people.Select(p => new { p.Name, p.Age, IsAdult = p.Age >= 18 });
            Console.WriteLine("Person info:");
            foreach (var info in personInfo.Take(3))
            {
                Console.WriteLine($"  {info.Name} ({info.Age}) - Adult: {info.IsAdult}");
            }
        }
        
        static void DemonstrateAggregation(List<Person> people, List<Order> orders)
        {
            // Count
            int totalPeople = people.Count();
            int adults = people.Count(p => p.Age >= 30);
            Console.WriteLine($"Total people: {totalPeople}, Adults: {adults}");
            
            // Sum
            decimal totalSalary = people.Sum(p => p.Salary);
            Console.WriteLine($"Total salary expense: ${totalSalary:N0}");
            
            // Average
            double averageAge = people.Average(p => p.Age);
            decimal averageSalary = people.Average(p => p.Salary);
            Console.WriteLine($"Average age: {averageAge:F1}, Average salary: ${averageSalary:N0}");
            
            // Min and Max
            int youngestAge = people.Min(p => p.Age);
            int oldestAge = people.Max(p => p.Age);
            decimal minSalary = people.Min(p => p.Salary);
            decimal maxSalary = people.Max(p => p.Salary);
            Console.WriteLine($"Age range: {youngestAge} - {oldestAge}");
            Console.WriteLine($"Salary range: ${minSalary:N0} - ${maxSalary:N0}");
            
            // Aggregate
            string allNames = people.Select(p => p.Name).Aggregate((a, b) => $"{a}, {b}");
            Console.WriteLine($"All names: {allNames}");
        }
        
        static void DemonstrateGrouping(List<Person> people, List<Order> orders)
        {
            // Group by city
            var peopleByCity = people.GroupBy(p => p.City);
            Console.WriteLine("People by city:");
            foreach (var group in peopleByCity)
            {
                Console.WriteLine($"  {group.Key}: {group.Count()} people, avg age: {group.Average(p => p.Age):F1}");
            }
            
            // Group by age ranges
            var peopleByAgeGroup = people.GroupBy(p => p.Age < 30 ? "Young" : p.Age < 40 ? "Middle" : "Senior");
            Console.WriteLine("\nPeople by age group:");
            foreach (var group in peopleByAgeGroup)
            {
                Console.WriteLine($"  {group.Key}: {group.Count()} people");
            }
            
            // Complex grouping with projection
            var cityStats = people
                .GroupBy(p => p.City)
                .Select(g => new
                {
                    City = g.Key,
                    Count = g.Count(),
                    AverageAge = g.Average(p => p.Age),
                    TotalSalary = g.Sum(p => p.Salary),
                    Names = string.Join(", ", g.Select(p => p.Name))
                });
            
            Console.WriteLine("\nCity statistics:");
            foreach (var stat in cityStats)
            {
                Console.WriteLine($"  {stat.City}: {stat.Count} people, avg age {stat.AverageAge:F1}, total salary ${stat.TotalSalary:N0}");
            }
        }
        
        static void DemonstrateJoins(List<Person> people, List<Order> orders, List<Product> products)
        {
            // Inner join
            var customerOrders = people
                .Join(orders,
                    person => person.Id,
                    order => order.CustomerId,
                    (person, order) => new
                    {
                        CustomerName = person.Name,
                        OrderAmount = order.Amount,
                        OrderDate = order.OrderDate,
                        Status = order.Status
                    });
            
            Console.WriteLine("Customer orders:");
            foreach (var order in customerOrders.Take(5))
            {
                Console.WriteLine($"  {order.CustomerName}: ${order.Amount:F2} on {order.OrderDate:MM/dd} ({order.Status})");
            }
            
            // Three-way join
            var orderDetails = orders
                .Join(products,
                    order => order.ProductId,
                    product => product.Id,
                    (order, product) => new { order, product })
                .Join(people,
                    op => op.order.CustomerId,
                    person => person.Id,
                    (op, person) => new
                    {
                        CustomerName = person.Name,
                        ProductName = op.product.Name,
                        Amount = op.order.Amount,
                        Category = op.product.Category
                    });
            
            Console.WriteLine("\nOrder details:");
            foreach (var detail in orderDetails)
            {
                Console.WriteLine($"  {detail.CustomerName} bought {detail.ProductName} ({detail.Category}) for ${detail.Amount:F2}");
            }
        }
        
        static void DemonstrateSetOperations()
        {
            var numbers1 = new List<int> { 1, 2, 3, 4, 5 };
            var numbers2 = new List<int> { 4, 5, 6, 7, 8 };
            
            // Union
            var union = numbers1.Union(numbers2);
            Console.WriteLine($"Union: {string.Join(", ", union)}");
            
            // Intersection
            var intersection = numbers1.Intersect(numbers2);
            Console.WriteLine($"Intersection: {string.Join(", ", intersection)}");
            
            // Except
            var except = numbers1.Except(numbers2);
            Console.WriteLine($"Except (1-2): {string.Join(", ", except)}");
            
            // Concat
            var concat = numbers1.Concat(numbers2);
            Console.WriteLine($"Concat: {string.Join(", ", concat)}");
            
            // Distinct
            var withDuplicates = new List<int> { 1, 2, 2, 3, 3, 3, 4 };
            var distinct = withDuplicates.Distinct();
            Console.WriteLine($"Distinct: {string.Join(", ", distinct)}");
        }
        
        static void DemonstratePartitioning(List<Person> people)
        {
            // Take and Skip (pagination)
            var page1 = people.OrderBy(p => p.Id).Take(3);
            var page2 = people.OrderBy(p => p.Id).Skip(3).Take(3);
            
            Console.WriteLine("Page 1:");
            foreach (var person in page1)
            {
                Console.WriteLine($"  {person.Name}");
            }
            
            Console.WriteLine("Page 2:");
            foreach (var person in page2)
            {
                Console.WriteLine($"  {person.Name}");
            }
            
            // TakeWhile and SkipWhile
            var numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
            var takeWhile = numbers.TakeWhile(n => n <= 5);
            var skipWhile = numbers.SkipWhile(n => n <= 5);
            
            Console.WriteLine($"TakeWhile (<=5): {string.Join(", ", takeWhile)}");
            Console.WriteLine($"SkipWhile (<=5): {string.Join(", ", skipWhile)}");
        }
        
        static void DemonstrateElementOperations(List<Person> people)
        {
            // First and FirstOrDefault
            var firstPerson = people.First();
            var firstAdult = people.First(p => p.Age >= 30);
            var firstFromLA = people.FirstOrDefault(p => p.City == "Los Angeles");
            var firstFromBoston = people.FirstOrDefault(p => p.City == "Boston"); // Returns null
            
            Console.WriteLine($"First person: {firstPerson.Name}");
            Console.WriteLine($"First adult: {firstAdult.Name}");
            Console.WriteLine($"First from LA: {firstFromLA?.Name ?? "None"}");
            Console.WriteLine($"First from Boston: {firstFromBoston?.Name ?? "None"}");
            
            // Single and SingleOrDefault
            var singlePerson = people.SingleOrDefault(p => p.Id == 3);
            Console.WriteLine($"Person with ID 3: {singlePerson.Name}");
            
            // Last and LastOrDefault
            var lastPerson = people.Last();
            var lastYoungPerson = people.Last(p => p.Age < 30);
            Console.WriteLine($"Last person: {lastPerson.Name}");
            Console.WriteLine($"Last young person: {lastYoungPerson.Name}");
        }
        
        static void DemonstrateQuantifierOperations(List<Person> people)
        {
            // Any
            bool hasPeople = people.Any();
            bool hasAdults = people.Any(p => p.Age >= 30);
            bool hasBoston = people.Any(p => p.City == "Boston");
            
            Console.WriteLine($"Has people: {hasPeople}");
            Console.WriteLine($"Has adults: {hasAdults}");
            Console.WriteLine($"Has people from Boston: {hasBoston}");
            
            // All
            bool allAdults = people.All(p => p.Age >= 18);
            bool allHaveEmail = people.All(p => !string.IsNullOrEmpty(p.Email));
            bool allHighSalary = people.All(p => p.Salary >= 50000);
            
            Console.WriteLine($"All adults: {allAdults}");
            Console.WriteLine($"All have email: {allHaveEmail}");
            Console.WriteLine($"All have high salary: {allHighSalary}");
            
            // Contains
            bool hasAlice = people.Any(p => p.Name.Contains("Alice"));
            bool hasId5 = people.Any(p => p.Id == 5);
            
            Console.WriteLine($"Contains Alice: {hasAlice}");
            Console.WriteLine($"Contains ID 5: {hasId5}");
        }
        
        static void DemonstrateComplexQueries(List<Person> people, List<Order> orders, List<Product> products, List<Department> departments)
        {
            // Find top 3 customers by total order amount
            var topCustomers = orders
                .GroupBy(o => o.CustomerId)
                .Select(g => new
                {
                    CustomerId = g.Key,
                    TotalAmount = g.Sum(o => o.Amount),
                    OrderCount = g.Count()
                })
                .OrderByDescending(c => c.TotalAmount)
                .Take(3)
                .Join(people,
                    c => c.CustomerId,
                    p => p.Id,
                    (c, p) => new
                    {
                        CustomerName = p.Name,
                        City = p.City,
                        TotalAmount = c.TotalAmount,
                        OrderCount = c.OrderCount,
                        AverageOrder = c.TotalAmount / c.OrderCount
                    });
            
            Console.WriteLine("Top 3 customers by total order amount:");
            foreach (var customer in topCustomers)
            {
                Console.WriteLine($"  {customer.CustomerName} ({customer.City}): ${customer.TotalAmount:F2} in {customer.OrderCount} orders (avg: ${customer.AverageOrder:F2})");
            }
            
            // Find products with no recent orders
            var recentOrders = orders.Where(o => o.OrderDate >= DateTime.Now.AddDays(-7));
            var inactiveProducts = products
                .Where(p => !recentOrders.Any(o => o.ProductId == p.Id))
                .Select(p => new { p.Name, p.Category, p.Stock });
            
            Console.WriteLine("\nProducts with no recent orders:");
            foreach (var product in inactiveProducts)
            {
                Console.WriteLine($"  {product.Name} ({product.Category}) - {product.Stock} in stock");
            }
            
            // Complex grouping with multiple aggregations
            var salesReport = orders
                .Where(o => o.OrderDate.Year == DateTime.Now.Year)
                .GroupBy(o => o.Status)
                .Select(g => new
                {
                    Status = g.Key,
                    Count = g.Count(),
                    TotalAmount = g.Sum(o => o.Amount),
                    AverageAmount = g.Average(o => o.Amount),
                    MaxAmount = g.Max(o => o.Amount),
                    MinAmount = g.Min(o => o.Amount)
                })
                .OrderByDescending(r => r.TotalAmount);
            
            Console.WriteLine("\nSales report by status:");
            foreach (var report in salesReport)
            {
                Console.WriteLine($"  {report.Status}: {report.Count} orders, ${report.TotalAmount:F2} total (avg: ${report.AverageAmount:F2})");
            }
        }
        
        static void DemonstrateQuerySyntax(List<Person> people)
        {
            // Method syntax
            var methodSyntax = people
                .Where(p => p.Age >= 30)
                .OrderBy(p => p.Name)
                .Select(p => new { p.Name, p.City, p.Salary });
            
            // Query syntax
            var querySyntax = from p in people
                            where p.Age >= 30
                            orderby p.Name
                            select new { p.Name, p.City, p.Salary };
            
            Console.WriteLine("Method syntax results:");
            foreach (var result in methodSyntax)
            {
                Console.WriteLine($"  {result.Name} from {result.City} - ${result.Salary:N0}");
            }
            
            Console.WriteLine("\nQuery syntax results:");
            foreach (var result in querySyntax)
            {
                Console.WriteLine($"  {result.Name} from {result.City} - ${result.Salary:N0}");
            }
            
            // Complex query with joins
            var complexQuery = from person in people
                              join department in departments on person.City equals department.Name.Substring(0, Math.Min(person.City.Length, department.Name.Length))
                              where person.Salary > 80000
                              group person by department.Name into g
                              select new
                              {
                                  Department = g.Key,
                                  Count = g.Count(),
                                  AverageSalary = g.Average(p => p.Salary)
                              };
            
            Console.WriteLine("\nComplex query results:");
            foreach (var result in complexQuery)
            {
                Console.WriteLine($"  {result.Department}: {result.Count} people, avg salary ${result.AverageSalary:N0}");
            }
        }
    }
}
