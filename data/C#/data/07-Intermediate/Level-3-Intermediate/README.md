# Level 3: Intermediate Concepts ⭐⭐⭐

**Duration:** 2-3 weeks | **Prerequisites:** Level 2 | **Difficulty:** Intermediate-Advanced

## 🎯 What You'll Learn

Take your C# skills to the professional level:

- ✅ LINQ (Language Integrated Query) - Query data like SQL
- ✅ Async/await - Parallel execution and responsive apps
- ✅ Generics - Type-safe, reusable code
- ✅ Delegates & Events - Loose coupling and event-driven code
- ✅ Lambda expressions - Functional programming
- ✅ Extension methods - Add methods to existing types
- ✅ Advanced collections - Deep understanding of data structures

## 📚 Topics

### **1. LINQ (Language Integrated Query)**

Write queries that work on any collection.

**You'll understand:**

- Query syntax vs method syntax
- Filtering with Where()
- Transforming with Select()
- Sorting and grouping
- Deferred execution
- Common LINQ operators
- Working with databases via LINQ

📖 [Lesson](01-linq/lesson.md) |
💻 [Example](01-linq/example.cs) |
🧪 [Exercise](01-linq/exercise.cs)

---

### **2. Async & Await**

Make your applications responsive and fast.

**You'll understand:**

- Synchronous vs asynchronous code
- The async/await pattern
- Tasks and Task<T>
- Running multiple operations in parallel
- Exception handling in async code
- When to use async
- Common pitfalls and best practices

📖 [Lesson](02-async-await/lesson.md) |
💻 [Example](02-async-await/example.cs) |
🧪 [Exercise](02-async-await/exercise.cs)

---

### **3. Generics**

Write flexible, type-safe code.

**You'll understand:**

- Generic classes and methods
- Type parameters and constraints
- Generic interfaces
- Why generics matter
- Covariance and contravariance (advanced)
- Using generics effectively

📖 [Lesson](03-generics/lesson.md) |
💻 [Example](03-generics/example.cs) |
🧪 [Exercise](03-generics/exercise.cs)

---

### **4. Delegates & Events**

Implement the observer pattern and event-driven architecture.

**You'll understand:**

- What delegates are (type-safe function pointers)
- Creating and using delegates
- Events and event handlers
- Publishing and subscribing to events
- Event patterns and conventions
- When to use events

📖 [Lesson](04-delegates-events/lesson.md) |
💻 [Example](04-delegates-events/example.cs) |
🧪 [Exercise](04-delegates-events/exercise.cs)

---

### **5. Lambda Expressions**

Write concise, functional-style code.

**You'll understand:**

- Lambda syntax and structure
- Using lambdas with delegates
- Lambdas with LINQ
- Expression trees (introduction)
- When lambdas improve readability
- Functional programming concepts

📖 [Lesson](05-lambdas/lesson.md) |
💻 [Example](05-lambdas/example.cs) |
🧪 [Exercise](05-lambdas/exercise.cs)

---

### **6. Advanced Collections**

Master data structure selection and usage.

**You'll understand:**

- Performance characteristics of collections
- Specialized collections (LinkedList, SortedDictionary, etc.)
- Implementing IEnumerable and IEnumerator
- Custom collections
- Thread-safe collections
- Memory efficiency

📖 [Lesson](06-advanced-collections/lesson.md) |
💻 [Example](06-advanced-collections/example.cs) |
🧪 [Exercise](06-advanced-collections/exercise.cs)

---

## 🎯 Learning Objectives Checklist

By the end of Level 3, you should be able to:

- [ ] Write LINQ queries to filter, sort, and transform data
- [ ] Use async/await to make responsive applications
- [ ] Create generic classes and methods
- [ ] Implement event-driven patterns
- [ ] Write clean lambda expressions
- [ ] Choose the right collection for the job
- [ ] Build applications combining async and LINQ
- [ ] Understand functional programming concepts
- [ ] Optimize code for performance
- [ ] Work with Entity Framework (LINQ to SQL)

---

## 🎮 Real-World Projects

### **Project 3: Library Management System**

Build a library system with books, members, and transactions.

**Concepts:** LINQ queries, collections, advanced OOP

👉 [Go to Library Management Project](../Projects/04-Library-Management/)

### **Project 4: Data Analysis Tool**

Analyze data using LINQ and create reports.

**Concepts:** LINQ, collections, filtering/grouping

👉 [Go to Data Analysis Project](../Projects/04-Library-Management/)

---

## 🚀 Real-World Applications

After Level 3, you can build:

- ✅ Full-stack web applications (ASP.NET Core)
- ✅ Data-heavy applications with Entity Framework
- ✅ Multi-threaded and parallel applications
- ✅ Event-driven systems
- ✅ APIs and microservices

---

## 💡 When You'll Use These

### **LINQ** - Every. Single. Day.

```csharp
// Get active users over 18
var adults = users.Where(u => u.IsActive && u.Age > 18)
                   .OrderBy(u => u.Name)
                   .ToList();
```

### **Async** - Building responsive web apps

```csharp
// Call API without freezing UI
var data = await httpClient.GetAsync(url);
```

### **Generics** - Already using them

```csharp
var numbers = new List<int>(); // List<T> is generic!
```

---

## 🔗 Your Learning Path

```
Level 1: Foundations ✓
Level 2: OOP Basics ✓
         ↓
You are here → Level 3: Intermediate
             ↓
        Level 4: Professional (Patterns, Testing)
```

---

## ⏭️ Next Steps

1. **Master LINQ first** - It's the most immediately useful
2. **Understand async/await** - Critical for web development
3. **Build projects** - Combine these concepts
4. **Then explore generics & events** - Deeper understanding
5. **Move to Level 4** - Ready for enterprise patterns!

---

**Ready? → [LINQ Basics](01-linq/)**
