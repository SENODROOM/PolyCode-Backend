using System;
using System.IO;

namespace IntermediateDemo
{
    // Custom exception
    public class InsufficientFundsException : Exception
    {
        public decimal RequestedAmount { get; }
        public decimal AvailableBalance { get; }
        
        public InsufficientFundsException(decimal requested, decimal available)
            : base($"Insufficient funds. Requested: ${requested:F2}, Available: ${available:F2}")
        {
            RequestedAmount = requested;
            AvailableBalance = available;
        }
    }
    
    // Custom exception for invalid age
    public class InvalidAgeException : Exception
    {
        public int Age { get; }
        
        public InvalidAgeException(int age) 
            : base($"Invalid age: {age}. Age must be between 0 and 150.")
        {
            Age = age;
        }
    }
    
    public class BankAccount
    {
        public string AccountNumber { get; set; }
        public string OwnerName { get; set; }
        private decimal balance;
        
        public decimal Balance 
        { 
            get { return balance; }
            private set { balance = value; }
        }
        
        public BankAccount(string accountNumber, string ownerName, decimal initialBalance = 0)
        {
            AccountNumber = accountNumber;
            OwnerName = ownerName;
            balance = initialBalance;
        }
        
        public void Deposit(decimal amount)
        {
            if (amount <= 0)
            {
                throw new ArgumentException("Deposit amount must be positive.", nameof(amount));
            }
            
            balance += amount;
            Console.WriteLine($"Deposited: ${amount:F2}. New balance: ${balance:F2}");
        }
        
        public void Withdraw(decimal amount)
        {
            if (amount <= 0)
            {
                throw new ArgumentException("Withdrawal amount must be positive.", nameof(amount));
            }
            
            if (amount > balance)
            {
                throw new InsufficientFundsException(amount, balance);
            }
            
            balance -= amount;
            Console.WriteLine($"Withdrew: ${amount:F2}. New balance: ${balance:F2}");
        }
        
        public void DisplayBalance()
        {
            Console.WriteLine($"Account {AccountNumber} ({OwnerName}): ${balance:F2}");
        }
    }
    
    public class Person
    {
        public string Name { get; set; }
        public int Age { get; set; }
        public string Email { get; set; }
        
        public Person(string name, int age, string email)
        {
            Name = name;
            SetAge(age);
            SetEmail(email);
        }
        
        public void SetAge(int age)
        {
            if (age < 0 || age > 150)
            {
                throw new InvalidAgeException(age);
            }
            Age = age;
        }
        
        public void SetEmail(string email)
        {
            if (string.IsNullOrWhiteSpace(email))
            {
                throw new ArgumentNullException(nameof(email), "Email cannot be null or empty.");
            }
            
            if (!email.Contains("@"))
            {
                throw new ArgumentException("Invalid email format.", nameof(email));
            }
            
            Email = email;
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Name: {Name}, Age: {Age}, Email: {Email}");
        }
    }
    
    public class FileProcessor
    {
        public void ProcessFile(string filePath)
        {
            StreamReader reader = null;
            try
            {
                reader = new StreamReader(filePath);
                string content = reader.ReadToEnd();
                Console.WriteLine($"File content ({content.Length} characters):");
                Console.WriteLine(content.Substring(0, Math.Min(100, content.Length)));
                if (content.Length > 100) Console.WriteLine("...");
            }
            catch (FileNotFoundException)
            {
                Console.WriteLine($"Error: File '{filePath}' not found.");
            }
            catch (UnauthorizedAccessException)
            {
                Console.WriteLine($"Error: Access denied to file '{filePath}'.");
            }
            catch (IOException ex)
            {
                Console.WriteLine($"IO Error: {ex.Message}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Unexpected error: {ex.Message}");
            }
            finally
            {
                reader?.Close();
                Console.WriteLine("File reader closed.");
            }
        }
        
        public void ProcessFileWithUsing(string filePath)
        {
            try
            {
                using (StreamReader reader = new StreamReader(filePath))
                {
                    string content = reader.ReadToEnd();
                    Console.WriteLine($"Processed file: {Path.GetFileName(filePath)}");
                    Console.WriteLine($"Line count: {content.Split('\n').Length}");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to process file: {ex.Message}");
            }
        }
    }
    
    public class Calculator
    {
        public double Divide(double numerator, double denominator)
        {
            if (denominator == 0)
            {
                throw new DivideByZeroException("Cannot divide by zero.");
            }
            return numerator / denominator;
        }
        
        public double SquareRoot(double number)
        {
            if (number < 0)
            {
                throw new ArgumentOutOfRangeException(nameof(number), "Cannot calculate square root of negative number.");
            }
            return Math.Sqrt(number);
        }
        
        public int ParseInteger(string input)
        {
            try
            {
                return int.Parse(input);
            }
            catch (FormatException)
            {
                throw new FormatException($"'{input}' is not a valid integer.");
            }
            catch (OverflowException)
            {
                throw new OverflowException($"'{input}' is too large or too small for an integer.");
            }
        }
    }
    
    class ExceptionsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exception Handling Demo ===\n");
            
            // 1. Basic exception handling
            Console.WriteLine("1. Basic Exception Handling:");
            DemonstrateBasicExceptions();
            
            // 2. Custom exceptions
            Console.WriteLine("\n2. Custom Exceptions:");
            DemonstrateCustomExceptions();
            
            // 3. File operations with exceptions
            Console.WriteLine("\n3. File Operations:");
            DemonstrateFileExceptions();
            
            // 4. Exception filters (C# 6.0+)
            Console.WriteLine("\n4. Exception Filters:");
            DemonstrateExceptionFilters();
            
            // 5. Exception properties
            Console.WriteLine("\n5. Exception Properties:");
            DemonstrateExceptionProperties();
        }
        
        static void DemonstrateBasicExceptions()
        {
            Calculator calc = new Calculator();
            
            // Division by zero
            try
            {
                double result = calc.Divide(10, 0);
                Console.WriteLine($"Result: {result}");
            }
            catch (DivideByZeroException ex)
            {
                Console.WriteLine($"Caught: {ex.Message}");
            }
            
            // Invalid input parsing
            try
            {
                int number = calc.ParseInteger("abc");
                Console.WriteLine($"Parsed number: {number}");
            }
            catch (FormatException ex)
            {
                Console.WriteLine($"Caught: {ex.Message}");
            }
            
            // Try-parse alternative
            string input = "123";
            if (int.TryParse(input, out int number))
            {
                Console.WriteLine($"Successfully parsed: {number}");
            }
            else
            {
                Console.WriteLine($"Failed to parse: {input}");
            }
        }
        
        static void DemonstrateCustomExceptions()
        {
            BankAccount account = new BankAccount("12345", "John Doe", 100);
            account.DisplayBalance();
            
            try
            {
                account.Deposit(50);
                account.Withdraw(200); // This will throw custom exception
            }
            catch (InsufficientFundsException ex)
            {
                Console.WriteLine($"Custom exception caught: {ex.Message}");
                Console.WriteLine($"Requested: ${ex.RequestedAmount:F2}, Available: ${ex.AvailableBalance:F2}");
            }
            catch (ArgumentException ex)
            {
                Console.WriteLine($"Argument exception: {ex.Message}");
            }
            
            // Person with invalid age
            try
            {
                Person person = new Person("Alice", -5, "alice@email.com");
                person.DisplayInfo();
            }
            catch (InvalidAgeException ex)
            {
                Console.WriteLine($"Invalid age exception: {ex.Message}");
            }
            catch (ArgumentException ex)
            {
                Console.WriteLine($"Argument exception: {ex.Message}");
            }
        }
        
        static void DemonstrateFileExceptions()
        {
            FileProcessor processor = new FileProcessor();
            
            // Try to read a non-existent file
            processor.ProcessFile("nonexistent.txt");
            
            Console.WriteLine();
            
            // Try to read with using statement
            processor.ProcessFileWithUsing("nonexistent.txt");
        }
        
        static void DemonstrateExceptionFilters()
        {
            try
            {
                // Simulate different types of errors
                throw new ArgumentException("Invalid email format", "email");
            }
            catch (ArgumentException ex) when (ex.ParamName == "email")
            {
                Console.WriteLine($"Email validation error: {ex.Message}");
            }
            catch (ArgumentException ex) when (ex.ParamName == "age")
            {
                Console.WriteLine($"Age validation error: {ex.Message}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"General error: {ex.Message}");
            }
        }
        
        static void DemonstrateExceptionProperties()
        {
            try
            {
                // Create a nested exception
                try
                {
                    int.Parse("not a number");
                }
                catch (FormatException ex)
                {
                    throw new ApplicationException("Failed to process user input", ex);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Exception Type: {ex.GetType().Name}");
                Console.WriteLine($"Message: {ex.Message}");
                Console.WriteLine($"Source: {ex.Source}");
                
                if (ex.InnerException != null)
                {
                    Console.WriteLine($"Inner Exception: {ex.InnerException.Message}");
                    Console.WriteLine($"Inner Type: {ex.InnerException.GetType().Name}");
                }
                
                Console.WriteLine($"Stack Trace (first 3 lines):");
                var stackLines = ex.StackTrace.Split('\n').Take(3);
                foreach (var line in stackLines)
                {
                    Console.WriteLine(line.Trim());
                }
            }
        }
    }
}
