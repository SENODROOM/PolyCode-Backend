# LINQ (Language Integrated Query) in C#

LINQ provides a unified syntax for querying data from different sources (collections, databases, XML, etc.) directly in C# code.

## LINQ Basics

### Two Syntax Styles

#### Query Syntax (SQL-like)
```csharp
var query = from student in students
            where student.Age > 18
            orderby student.Name
            select student.Name;
```

#### Method Syntax (Lambda expressions)
```csharp
var query = students
    .Where(student => student.Age > 18)
    .OrderBy(student => student.Name)
    .Select(student => student.Name);
```

## Basic LINQ Operations

### Filtering with Where

```csharp
List<int> numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };

// Basic filtering
var evenNumbers = numbers.Where(n => n % 2 == 0);
// Result: 2, 4, 6, 8, 10

// Multiple conditions
var filtered = numbers.Where(n => n > 3 && n < 8);
// Result: 4, 5, 6, 7

// With objects
var adults = people.Where(p => p.Age >= 18);
```

### Sorting with OrderBy

```csharp
// Ascending order
var sortedByName = people.OrderBy(p => p.Name);
var sortedByAge = people.OrderBy(p => p.Age);

// Descending order
var sortedDesc = people.OrderByDescending(p => p.Name);

// Multiple sorting levels
var multiSort = people
    .OrderBy(p => p.City)
    .ThenBy(p => p.Name)
    .ThenByDescending(p => p.Age);
```

### Projection with Select

```csharp
// Transform to different type
var names = people.Select(p => p.Name);
var ages = people.Select(p => p.Age);

// Create anonymous objects
var personInfo = people.Select(p => new 
{
    p.Name,
    p.Age,
    IsAdult = p.Age >= 18
});

// Transform to complex objects
var dtos = people.Select(p => new PersonDTO
{
    FullName = $"{p.FirstName} {p.LastName}",
    AgeGroup = p.Age < 18 ? "Minor" : "Adult"
});
```

## Aggregation Operations

### Count, Sum, Average, Min, Max

```csharp
// Count
int totalPeople = people.Count();
int adultsCount = people.Count(p => p.Age >= 18);

// Sum
decimal totalSales = orders.Sum(o => o.Amount);
int totalAge = people.Sum(p => p.Age);

// Average
double averageAge = people.Average(p => p.Age);
decimal averageOrder = orders.Average(o => o.Amount);

// Min and Max
int oldestAge = people.Max(p => p.Age);
int youngestAge = people.Min(p => p.Age);
decimal maxOrder = orders.Max(o => o.Amount);
decimal minOrder = orders.Min(o => o.Amount);
```

### Advanced Aggregation

```csharp
// Aggregate with custom logic
string allNames = people
    .Select(p => p.Name)
    .Aggregate((current, next) => $"{current}, {next}");

// Product of all numbers
int product = numbers.Aggregate(1, (acc, n) => acc * n);

// Custom aggregation
var result = numbers.Aggregate(
    seed: new { Sum = 0, Count = 0 },
    func: (acc, n) => new { Sum = acc.Sum + n, Count = acc.Count + 1 },
    resultSelector: acc => new { Average = (double)acc.Sum / acc.Count, Count = acc.Count });
```

## Grouping Operations

### GroupBy

```csharp
// Group by single property
var peopleByCity = people.GroupBy(p => p.City);

foreach (var group in peopleByCity)
{
    Console.WriteLine($"City: {group.Key}");
    foreach (var person in group)
    {
        Console.WriteLine($"  {person.Name}");
    }
}

// Group by multiple properties
var peopleByCityAndAge = people.GroupBy(p => new 
{
    p.City,
    AgeGroup = p.Age < 30 ? "Young" : "Senior"
});

// Group with projection
var cityStats = people
    .GroupBy(p => p.City)
    .Select(g => new
    {
        City = g.Key,
        Count = g.Count(),
        AverageAge = g.Average(p => p.Age),
        Names = string.Join(", ", g.Select(p => p.Name))
    });
```

## Join Operations

### Inner Join

```csharp
var students = new List<Student> { /* ... */ };
var courses = new List<Course> { /* ... */ };

// Inner join
var enrollments = students
    .Join(courses,
        student => student.CourseId,
        course => course.Id,
        (student, course) => new
        {
            StudentName = student.Name,
            CourseName = course.Name,
            Grade = student.Grade
        });

// Query syntax
var enrollmentsQuery = from student in students
                      join course in courses on student.CourseId equals course.Id
                      select new
                      {
                          StudentName = student.Name,
                          CourseName = course.Name,
                          Grade = student.Grade
                      };
```

### Left Join (GroupJoin)

```csharp
var leftJoin = students
    .GroupJoin(courses,
        student => student.CourseId,
        course => course.Id,
        (student, courseGroup) => new
        {
            Student = student,
            Courses = courseGroup.DefaultIfEmpty()
        })
    .SelectMany(x => x.Courses,
        (x, course) => new
        {
            StudentName = x.Student.Name,
            CourseName = course?.Name ?? "No Course"
        });
```

## Set Operations

### Distinct, Union, Intersect, Except

```csharp
// Remove duplicates
var uniqueNames = names.Distinct();

// Union (combine and remove duplicates)
var allNumbers = numbers1.Union(numbers2);

// Intersection (common elements)
var commonNumbers = numbers1.Intersect(numbers2);

// Difference (elements in first but not in second)
var difference = numbers1.Except(numbers2);

// Concat (combine without removing duplicates)
var combined = numbers1.Concat(numbers2);
```

## Partitioning Operations

### Take, Skip, TakeWhile, SkipWhile

```csharp
// Pagination
var page1 = products.OrderBy(p => p.Id).Skip(0).Take(10);
var page2 = products.OrderBy(p => p.Id).Skip(10).Take(10);

// Conditional partitioning
var firstFew = numbers.TakeWhile(n => n < 5);
var afterFirst = numbers.SkipWhile(n => n < 5);
```

## Element Operations

### First, FirstOrDefault, Single, SingleOrDefault

```csharp
// First - throws if no elements
var firstPerson = people.First();
var firstAdult = people.First(p => p.Age >= 18);

// FirstOrDefault - returns default if no elements
var firstPersonOrDefault = people.FirstOrDefault();
var firstAdultOrDefault = people.FirstOrDefault(p => p.Age >= 18);

// Single - expects exactly one element
var singlePerson = people.Single(p => p.Id == 5);

// SingleOrDefault - returns default if no elements, throws if multiple
var singlePersonOrDefault = people.SingleOrDefault(p => p.Id == 5);
```

## Quantifier Operations

### Any, All, Contains

```csharp
// Any - checks if any elements exist or satisfy condition
bool hasPeople = people.Any();
bool hasAdults = people.Any(p => p.Age >= 18);

// All - checks if all elements satisfy condition
bool allAdults = people.All(p => p.Age >= 18);
bool allHaveEmail = people.All(p => !string.IsNullOrEmpty(p.Email));

// Contains - checks if specific element exists
bool hasAlice = people.Any(p => p.Name == "Alice");
bool containsNumber = numbers.Contains(5);
```

## Generation Operations

### Range, Repeat, Empty

```csharp
// Generate sequence of numbers
var numbers1to10 = Enumerable.Range(1, 10);
var evenNumbers = Enumerable.Range(0, 10).Select(n => n * 2);

// Repeat element
var repeated = Enumerable.Repeat("Hello", 5);

// Empty sequence
var empty = Enumerable.Empty<string>();
```

## LINQ to Objects Examples

### Complex Queries

```csharp
// Find top 5 customers by total order amount
var topCustomers = orders
    .GroupBy(o => o.CustomerId)
    .Select(g => new
    {
        CustomerId = g.Key,
        TotalAmount = g.Sum(o => o.Amount),
        OrderCount = g.Count()
    })
    .OrderByDescending(c => c.TotalAmount)
    .Take(5)
    .Join(customers,
        c => c.CustomerId,
        cust => cust.Id,
        (c, cust) => new
        {
            CustomerName = cust.Name,
            TotalAmount = c.TotalAmount,
            OrderCount = c.OrderCount,
            AverageOrder = c.TotalAmount / c.OrderCount
        });

// Find products that haven't been ordered in last 30 days
var inactiveProducts = products
    .Where(p => !orders
        .Any(o => o.ProductId == p.Id && 
                   o.OrderDate >= DateTime.Now.AddDays(-30)));

// Complex grouping with multiple aggregations
var salesReport = orders
    .Where(o => o.OrderDate.Year == DateTime.Now.Year)
    .GroupBy(o => new { o.ProductId, o.Customer.Region })
    .Select(g => new
    {
        ProductId = g.Key.ProductId,
        Region = g.Key.Region,
        TotalSales = g.Sum(o => o.Amount),
        OrderCount = g.Count(),
        AverageOrder = g.Average(o => o.Amount),
        MaxOrder = g.Max(o => o.Amount),
        MinOrder = g.Min(o => o.Amount)
    })
    .OrderBy(r => r.Region)
    .ThenByDescending(r => r.TotalSales);
```

## LINQ Best Practices

### Performance Tips

```csharp
// Use deferred execution wisely
var query = people.Where(p => p.Age > 18); // Query not executed yet
var results = query.ToList(); // Query executed here

// Avoid multiple enumerations
var peopleList = people.Where(p => p.Age > 18).ToList(); // Execute once
var count = peopleList.Count();
var first = peopleList.First();

// Use appropriate methods
bool exists = people.Any(p => p.Id == 5); // Better than FirstOrDefault != null

// Consider indexing for large collections
var indexedPeople = people.ToLookup(p => p.City);
var cityPeople = indexedPeople["New York"];
```

### Readability Tips

```csharp
// Break complex queries into multiple steps
var adults = people.Where(p => p.Age >= 18);
var sortedAdults = adults.OrderBy(p => p.Name);
var adultNames = sortedAdults.Select(p => p.Name);

// Use meaningful variable names
var activeCustomers = customers.Where(c => c.IsActive);
var recentOrders = activeCustomers.SelectMany(c => c.Orders)
                                   .Where(o => o.Date >= DateTime.Now.AddDays(-30));

// Use query syntax for complex joins
var complexJoin = from customer in customers
                 join order in orders on customer.Id equals order.CustomerId
                 join product in products on order.ProductId equals product.Id
                 where customer.IsActive && order.Date >= DateTime.Now.AddDays(-30)
                 group new { order, product } by customer.Name into g
                 select new
                 {
                     CustomerName = g.Key,
                     TotalSpent = g.Sum(x => x.order.Amount),
                     ProductCount = g.Select(x => x.product.Id).Distinct().Count()
                 };
```

## LINQ Providers

### Different LINQ Providers

- **LINQ to Objects**: In-memory collections
- **LINQ to XML**: XML documents
- **LINQ to SQL**: SQL Server databases
- **LINQ to Entities**: Entity Framework
- **LINQ to JSON**: JSON documents (via third-party libraries)

Each provider translates LINQ queries to the appropriate data source language while maintaining the same C# syntax.
