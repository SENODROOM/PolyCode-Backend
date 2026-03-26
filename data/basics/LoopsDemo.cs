using System;

namespace BasicsDemo
{
    class LoopsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Loops Demo ===\n");
            
            // for loop
            Console.WriteLine("For Loop - Counting to 5:");
            for (int i = 1; i <= 5; i++)
            {
                Console.WriteLine($"Count: {i}");
            }
            
            // for loop with different increments
            Console.WriteLine("\nFor Loop - Even numbers up to 10:");
            for (int i = 0; i <= 10; i += 2)
            {
                Console.WriteLine($"Even: {i}");
            }
            
            // while loop
            Console.WriteLine("\nWhile Loop - Countdown:");
            int countdown = 5;
            while (countdown > 0)
            {
                Console.WriteLine($"Countdown: {countdown}");
                countdown--;
            }
            Console.WriteLine("Blast off!");
            
            // do-while loop
            Console.WriteLine("\nDo-While Loop - Number input:");
            int number;
            do
            {
                Console.Write("Enter a number between 1-10 (0 to exit): ");
                number = Convert.ToInt32(Console.ReadLine());
                if (number != 0)
                {
                    Console.WriteLine($"You entered: {number}");
                }
            } while (number != 0);
            
            // foreach loop with array
            Console.WriteLine("\nForeach Loop - Fruits:");
            string[] fruits = { "Apple", "Banana", "Orange", "Grape", "Mango" };
            foreach (string fruit in fruits)
            {
                Console.WriteLine($"Fruit: {fruit}");
            }
            
            // foreach loop with List
            Console.WriteLine("\nForeach Loop - Numbers:");
            var numbers = new List<int> { 10, 20, 30, 40, 50 };
            foreach (int num in numbers)
            {
                Console.WriteLine($"Number: {num}");
            }
            
            // break statement
            Console.WriteLine("\nBreak Statement - Stop at 3:");
            for (int i = 1; i <= 10; i++)
            {
                if (i == 4)
                    break;
                Console.WriteLine($"i = {i}");
            }
            
            // continue statement
            Console.WriteLine("\nContinue Statement - Skip even numbers:");
            for (int i = 1; i <= 10; i++)
            {
                if (i % 2 == 0)
                    continue;
                Console.WriteLine($"Odd number: {i}");
            }
            
            // nested loops
            Console.WriteLine("\nNested Loops - Multiplication Table:");
            for (int i = 1; i <= 3; i++)
            {
                for (int j = 1; j <= 5; j++)
                {
                    Console.Write($"{i * j,4}");
                }
                Console.WriteLine();
            }
            
            // loop with string manipulation
            Console.WriteLine("\nLoop - String Reversal:");
            string text = "Hello World";
            string reversed = "";
            for (int i = text.Length - 1; i >= 0; i--)
            {
                reversed += text[i];
            }
            Console.WriteLine($"Original: {text}");
            Console.WriteLine($"Reversed: {reversed}");
            
            // loop to find prime numbers
            Console.WriteLine("\nLoop - Prime Numbers up to 20:");
            for (int num = 2; num <= 20; num++)
            {
                bool isPrime = true;
                for (int i = 2; i <= num / 2; i++)
                {
                    if (num % i == 0)
                    {
                        isPrime = false;
                        break;
                    }
                }
                if (isPrime)
                {
                    Console.Write($"{num} ");
                }
            }
            Console.WriteLine();
            
            // foreach with dictionary
            Console.WriteLine("\nForeach Loop - Dictionary:");
            var studentGrades = new Dictionary<string, int>
            {
                { "Alice", 95 },
                { "Bob", 87 },
                { "Charlie", 92 }
            };
            
            foreach (var student in studentGrades)
            {
                Console.WriteLine($"{student.Key}: {student.Value}");
            }
        }
    }
}
