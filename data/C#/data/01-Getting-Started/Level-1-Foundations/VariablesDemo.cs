using System;

namespace BasicsDemo
{
    class VariablesDemo
    {
        static void Main(string[] args)
        {
            // Integer variables
            int age = 25;
            int score = 100;
            
            // Floating point variables
            double height = 5.9;
            float weight = 150.5f;
            decimal price = 19.99m;
            
            // String and character
            string name = "Alice Johnson";
            char grade = 'A';
            
            // Boolean
            bool isStudent = true;
            bool hasLicense = false;
            
            // DateTime
            DateTime birthDate = new DateTime(1998, 5, 15);
            DateTime today = DateTime.Now;
            
            // Constants
            const double PI = 3.14159;
            const int MAX_SCORE = 100;
            
            // Display all variables
            Console.WriteLine("=== Variables Demo ===");
            Console.WriteLine($"Name: {name}");
            Console.WriteLine($"Age: {age}");
            Console.WriteLine($"Height: {height} feet");
            Console.WriteLine($"Weight: {weight} pounds");
            Console.WriteLine($"Price: ${price}");
            Console.WriteLine($"Grade: {grade}");
            Console.WriteLine($"Is Student: {isStudent}");
            Console.WriteLine($"Has License: {hasLicense}");
            Console.WriteLine($"Birth Date: {birthDate:d}");
            Console.WriteLine($"Today: {today:d}");
            Console.WriteLine($"PI: {PI}");
            Console.WriteLine($"Max Score: {MAX_SCORE}");
            
            // Variable operations
            int newAge = age + 1;
            double total = price * 2;
            
            Console.WriteLine($"\nNext year age: {newAge}");
            Console.WriteLine($"Total for 2 items: ${total}");
        }
    }
}
