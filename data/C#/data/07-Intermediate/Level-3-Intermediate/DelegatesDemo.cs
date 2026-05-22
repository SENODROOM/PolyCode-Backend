using System;
using System.Collections.Generic;
using System.Linq;

namespace AdvancedDemo
{
    // Custom delegate declarations
    public delegate void SimpleDelegate(string message);
    public delegate int MathOperation(int a, int b);
    public delegate bool FilterCondition<T>(T item);
    
    public class DelegatesDemo
    {
        // Methods that match delegate signatures
        public static void ShowMessage(string message)
        {
            Console.WriteLine($"Message: {message}");
        }
        
        public static void LogMessage(string message)
        {
            Console.WriteLine($"LOG: {DateTime.Now:HH:mm:ss}: {message}");
        }
        
        public static void EmailMessage(string message)
        {
            Console.WriteLine($"EMAIL: Sending '{message}' to user@example.com");
        }
        
        public static int Add(int a, int b) => a + b;
        public static int Multiply(int a, int b) => a * b;
        public static int Subtract(int a, int b) => a - b;
        public static int Divide(int a, int b) => a / b;
        
        // Methods for filtering
        public static bool IsEven(int number) => number % 2 == 0;
        public static bool IsPositive(int number) => number > 0;
        public static bool IsLongString(string text) => text.Length > 10;
        
        static void Main(string[] args)
        {
            Console.WriteLine("=== Delegates Demo ===\n");
            
            // 1. Basic delegate usage
            DemonstrateBasicDelegates();
            
            // 2. Multicast delegates
            DemonstrateMulticastDelegates();
            
            // 3. Built-in delegates (Action, Func, Predicate)
            DemonstrateBuiltInDelegates();
            
            // 4. Lambda expressions with delegates
            DemonstrateLambdaExpressions();
            
            // 5. Delegates as parameters
            DemonstrateDelegateParameters();
            
            // 6. Anonymous methods
            DemonstrateAnonymousMethods();
            
            // 7. Practical examples
            DemonstratePracticalExamples();
        }
        
        static void DemonstrateBasicDelegates()
        {
            Console.WriteLine("1. Basic Delegate Usage:");
            
            // Create delegate instances
            SimpleDelegate messageDelegate = new SimpleDelegate(ShowMessage);
            MathOperation mathDelegate = Add;
            
            // Invoke delegates
            messageDelegate("Hello from basic delegate!");
            int result = mathDelegate(10, 5);
            Console.WriteLine($"10 + 5 = {result}");
            
            // Change delegate methods
            messageDelegate = LogMessage;
            mathDelegate = Multiply;
            
            messageDelegate("This is a log message");
            result = mathDelegate(10, 5);
            Console.WriteLine($"10 * 5 = {result}");
            
            Console.WriteLine();
        }
        
        static void DemonstrateMulticastDelegates()
        {
            Console.WriteLine("2. Multicast Delegates:");
            
            // Create multicast delegate
            SimpleDelegate multicastDelegate = ShowMessage;
            multicastDelegate += LogMessage;
            multicastDelegate += EmailMessage;
            
            Console.WriteLine("Invoking multicast delegate:");
            multicastDelegate("Multicast message");
            
            // Remove one method
            Console.WriteLine("\nAfter removing LogMessage:");
            multicastDelegate -= LogMessage;
            multicastDelegate("Another multicast message");
            
            // Get invocation list
            var invocationList = multicastDelegate.GetInvocationList();
            Console.WriteLine($"\nMethods in delegate: {invocationList.Length}");
            foreach (var del in invocationList)
            {
                Console.WriteLine($"  - {del.Method.Name}");
            }
            
            Console.WriteLine();
        }
        
        static void DemonstrateBuiltInDelegates()
        {
            Console.WriteLine("3. Built-in Delegates (Action, Func, Predicate):");
            
            // Action delegates (no return value)
            Action action1 = () => Console.WriteLine("Action with no parameters");
            Action<string> action2 = (msg) => Console.WriteLine($"Action: {msg}");
            Action<int, int> action3 = (a, b) => Console.WriteLine($"Action: {a} + {b} = {a + b}");
            
            action1();
            action2("Hello from Action");
            action3(7, 8);
            
            // Func delegates (with return value)
            Func<int> func1 = () => 42;
            Func<int, int> func2 = (x) => x * x;
            Func<int, int, string> func3 = (a, b) => $"{a} + {b} = {a + b}";
            
            int number = func1();
            int square = func2(6);
            string sum = func3(15, 25);
            
            Console.WriteLine($"\nFunc results:");
            Console.WriteLine($"  func1(): {number}");
            Console.WriteLine($"  func2(6): {square}");
            Console.WriteLine($"  func3(15, 25): {sum}");
            
            // Predicate delegates (return bool)
            Predicate<int> isEven = IsEven;
            Predicate<int> isPositive = IsPositive;
            Predicate<string> isLong = IsLongString;
            
            Console.WriteLine($"\nPredicate results:");
            Console.WriteLine($"  Is 4 even? {isEven(4)}");
            Console.WriteLine($"  Is 7 even? {isEven(7)}");
            Console.WriteLine($"  Is -5 positive? {isPositive(-5)}");
            Console.WriteLine($"  Is 'Hello World' long? {isLong("Hello World")}");
            
            Console.WriteLine();
        }
        
        static void DemonstrateLambdaExpressions()
        {
            Console.WriteLine("4. Lambda Expressions with Delegates:");
            
            // Simple lambdas
            Func<int, int> square = x => x * x;
            Func<int, int, int> add = (a, b) => a + b;
            Action<string> print = msg => Console.WriteLine($"Lambda: {msg}");
            Predicate<int> isAdult = age => age >= 18;
            
            Console.WriteLine($"Square of 8: {square(8)}");
            Console.WriteLine($"Add 12 + 18: {add(12, 18)}");
            print("Hello from lambda!");
            Console.WriteLine($"Is age 20 adult? {isAdult(20)}");
            
            // Complex lambda with multiple statements
            Func<List<int>, List<int>> processNumbers = numbers =>
            {
                var result = new List<int>();
                foreach (int num in numbers)
                {
                    if (num % 2 == 0)
                        result.Add(num * 2);
                    else
                        result.Add(num + 10);
                }
                return result.OrderByDescending(x => x).ToList();
            };
            
            var numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8 };
            var processed = processNumbers(numbers);
            Console.WriteLine($"\nOriginal: {string.Join(", ", numbers)}");
            Console.WriteLine($"Processed: {string.Join(", ", processed)}");
            
            Console.WriteLine();
        }
        
        static void DemonstrateDelegateParameters()
        {
            Console.WriteLine("5. Delegates as Parameters:");
            
            var numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
            
            // Process with different operations
            ProcessNumbers(numbers, x => x * 2, "Double");
            ProcessNumbers(numbers, x => x * x, "Square");
            ProcessNumbers(numbers, x => x + 100, "Add 100");
            
            // Filter with different conditions
            FilterNumbers(numbers, x => x % 2 == 0, "Even numbers");
            FilterNumbers(numbers, x => x > 5, "Numbers > 5");
            FilterNumbers(numbers, x => x % 3 == 0, "Multiples of 3");
            
            // Process with callback
            ProcessWithCallback("Data.txt", result => Console.WriteLine($"Callback: {result}"));
        }
        
        static void ProcessNumbers(List<int> numbers, Func<int, int> operation, string operationName)
        {
            Console.WriteLine($"\n{operationName}:");
            var results = numbers.Select(operation).ToList();
            Console.WriteLine($"  {string.Join(", ", results)}");
        }
        
        static void FilterNumbers(List<int> numbers, Predicate<int> condition, string filterName)
        {
            Console.WriteLine($"\n{filterName}:");
            var filtered = numbers.Where(x => condition(x)).ToList();
            Console.WriteLine($"  {string.Join(", ", filtered)}");
        }
        
        static void ProcessWithCallback(string data, Action<string> callback)
        {
            Console.WriteLine($"\nProcessing {data}...");
            // Simulate processing
            System.Threading.Thread.Sleep(500);
            callback($"Completed processing {data}");
        }
        
        static void DemonstrateAnonymousMethods()
        {
            Console.WriteLine("6. Anonymous Methods:");
            
            // Anonymous method syntax
            Func<int, int> factorial = delegate(int n)
            {
                if (n <= 1) return 1;
                int result = 1;
                for (int i = 2; i <= n; i++)
                    result *= i;
                return result;
            };
            
            Action<string> repeatMessage = delegate(string message)
            {
                for (int i = 1; i <= 3; i++)
                    Console.WriteLine($"{i}. {message}");
            };
            
            Console.WriteLine($"Factorial of 5: {factorial(5)}");
            Console.WriteLine($"Factorial of 6: {factorial(6)}");
            
            repeatMessage("Hello from anonymous method!");
            
            // Anonymous method with closure
            int multiplier = 7;
            Func<int, int> multiplyByMultiplier = delegate(int x)
            {
                return x * multiplier;
            };
            
            Console.WriteLine($"\nUsing closure (multiplier = {multiplier}):");
            Console.WriteLine($"8 * {multiplier} = {multiplyByMultiplier(8)}");
            Console.WriteLine($"12 * {multiplier} = {multiplyByMultiplier(12)}");
            
            Console.WriteLine();
        }
        
        static void DemonstratePracticalExamples()
        {
            Console.WriteLine("7. Practical Examples:");
            
            // Strategy pattern with delegates
            var data = new List<int> { 5, 2, 8, 1, 9, 3, 7, 4, 6 };
            
            Console.WriteLine("Original data:");
            Console.WriteLine($"  {string.Join(", ", data)}");
            
            // Different processing strategies
            var strategies = new Dictionary<string, Func<List<int>, List<int>>>
            {
                ["Sort Ascending"] = list => list.OrderBy(x => x).ToList(),
                ["Sort Descending"] = list => list.OrderByDescending(x => x).ToList(),
                ["Even Only"] = list => list.Where(x => x % 2 == 0).ToList(),
                ["Greater than 5"] = list => list.Where(x => x > 5).ToList(),
                ["Squared"] = list => list.Select(x => x * x).ToList()
            };
            
            foreach (var strategy in strategies)
            {
                var result = strategy.Value(data);
                Console.WriteLine($"\n{strategy.Key}:");
                Console.WriteLine($"  {string.Join(", ", result)}");
            }
            
            // Event-like pattern with delegates
            Console.WriteLine("\nEvent-like Pattern:");
            
            Action<string> eventHandlers = null;
            
            // Subscribe multiple handlers
            eventHandlers += (msg) => Console.WriteLine($"Handler 1: {msg}");
            eventHandlers += (msg) => Console.WriteLine($"Handler 2: {msg.ToUpper()}");
            eventHandlers += (msg) => Console.WriteLine($"Handler 3: Length = {msg.Length}");
            
            // Trigger event
            eventHandlers?.Invoke("Event triggered!");
            
            // Calculator with configurable operations
            Console.WriteLine("\nCalculator with Delegates:");
            var calculator = new Calculator();
            
            calculator.SetOperation(Add);
            calculator.Calculate(10, 5);
            
            calculator.SetOperation(Multiply);
            calculator.Calculate(10, 5);
            
            calculator.SetOperation((a, b) => Math.Pow(a, b)); // Lambda as operation
            calculator.Calculate(2, 8);
        }
    }
    
    // Calculator class that uses delegates
    public class Calculator
    {
        private MathOperation operation;
        
        public void SetOperation(MathOperation op)
        {
            operation = op;
        }
        
        public void Calculate(int a, int b)
        {
            if (operation != null)
            {
                int result = operation(a, b);
                Console.WriteLine($"Calculation: {a} ? {b} = {result}");
            }
            else
            {
                Console.WriteLine("No operation set!");
            }
        }
    }
}
