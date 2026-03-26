using System;

namespace BasicsDemo
{
    class MethodsDemo
    {
        // Method with no parameters and no return value
        public static void SayHello()
        {
            Console.WriteLine("Hello, World!");
        }
        
        // Method with parameters
        public static void GreetPerson(string name, int age)
        {
            Console.WriteLine($"Hello {name}, you are {age} years old!");
        }
        
        // Method with return value
        public static int Add(int a, int b)
        {
            return a + b;
        }
        
        // Method with multiple parameters and return value
        public static double CalculateArea(double length, double width)
        {
            return length * width;
        }
        
        // Method overloading - same name, different parameters
        public static int Multiply(int a, int b)
        {
            return a * b;
        }
        
        public static double Multiply(double a, double b)
        {
            return a * b;
        }
        
        public static int Multiply(int a, int b, int c)
        {
            return a * b * c;
        }
        
        // Method with validation
        public static bool IsEven(int number)
        {
            return number % 2 == 0;
        }
        
        // Method that returns multiple values using tuple
        public static (int quotient, int remainder) Divide(int dividend, int divisor)
        {
            int quotient = dividend / divisor;
            int remainder = dividend % divisor;
            return (quotient, remainder);
        }
        
        // Recursive method
        public static int Factorial(int n)
        {
            if (n <= 1)
                return 1;
            return n * Factorial(n - 1);
        }
        
        static void Main(string[] args)
        {
            Console.WriteLine("=== Methods Demo ===\n");
            
            // Calling methods
            SayHello();
            
            GreetPerson("Alice", 25);
            GreetPerson("Bob", 30);
            
            int sum = Add(15, 25);
            Console.WriteLine($"15 + 25 = {sum}");
            
            double area = CalculateArea(10.5, 5.2);
            Console.WriteLine($"Area of rectangle (10.5 x 5.2) = {area}");
            
            // Method overloading
            Console.WriteLine($"Multiply ints: {Multiply(3, 4)}");
            Console.WriteLine($"Multiply doubles: {Multiply(3.5, 4.2)}");
            Console.WriteLine($"Multiply three ints: {Multiply(2, 3, 4)}");
            
            // Validation method
            Console.WriteLine($"Is 10 even? {IsEven(10)}");
            Console.WriteLine($"Is 7 even? {IsEven(7)}");
            
            // Method returning multiple values
            var (quotient, remainder) = Divide(17, 5);
            Console.WriteLine($"17 ÷ 5 = {quotient} remainder {remainder}");
            
            // Recursive method
            Console.WriteLine($"Factorial of 5 = {Factorial(5)}");
            Console.WriteLine($"Factorial of 6 = {Factorial(6)}");
        }
    }
}
