using System;

namespace BasicsDemo
{
    class ConditionalsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Conditionals Demo ===\n");
            
            // if statement
            int age = 18;
            Console.WriteLine($"Age: {age}");
            
            if (age >= 18)
            {
                Console.WriteLine("You are eligible to vote!");
            }
            
            // if-else statement
            int score = 75;
            Console.WriteLine($"\nScore: {score}");
            
            if (score >= 60)
            {
                Console.WriteLine("You passed the exam!");
            }
            else
            {
                Console.WriteLine("You failed the exam.");
            }
            
            // if-else if-else statement
            int grade = 85;
            Console.WriteLine($"\nGrade: {grade}");
            
            if (grade >= 90)
            {
                Console.WriteLine("Grade: A");
            }
            else if (grade >= 80)
            {
                Console.WriteLine("Grade: B");
            }
            else if (grade >= 70)
            {
                Console.WriteLine("Grade: C");
            }
            else if (grade >= 60)
            {
                Console.WriteLine("Grade: D");
            }
            else
            {
                Console.WriteLine("Grade: F");
            }
            
            // Logical operators
            bool hasLicense = true;
            bool hasCar = false;
            
            Console.WriteLine($"\nHas License: {hasLicense}");
            Console.WriteLine($"Has Car: {hasCar}");
            
            if (hasLicense && hasCar)
            {
                Console.WriteLine("You can drive!");
            }
            else if (hasLicense || hasCar)
            {
                Console.WriteLine("You have some driving resources.");
            }
            else
            {
                Console.WriteLine("You cannot drive yet.");
            }
            
            // switch statement
            string dayOfWeek = "Wednesday";
            Console.WriteLine($"\nDay: {dayOfWeek}");
            
            switch (dayOfWeek.ToLower())
            {
                case "monday":
                    Console.WriteLine("Start of the work week!");
                    break;
                case "tuesday":
                case "wednesday":
                case "thursday":
                    Console.WriteLine("Regular work day.");
                    break;
                case "friday":
                    Console.WriteLine("TGIF! Almost weekend!");
                    break;
                case "saturday":
                case "sunday":
                    Console.WriteLine("Weekend!");
                    break;
                default:
                    Console.WriteLine("Invalid day!");
                    break;
            }
            
            // Ternary operator
            int number = 15;
            string result = (number % 2 == 0) ? "Even" : "Odd";
            Console.WriteLine($"\nNumber {number} is {result}");
            
            // Nested conditions
            int temperature = 25;
            bool isRaining = false;
            
            Console.WriteLine($"\nTemperature: {temperature}°C");
            Console.WriteLine($"Is Raining: {isRaining}");
            
            if (temperature > 20)
            {
                if (isRaining)
                {
                    Console.WriteLine("Warm but rainy - bring an umbrella!");
                }
                else
                {
                    Console.WriteLine("Warm and sunny - great weather!");
                }
            }
            else
            {
                if (isRaining)
                {
                    Console.WriteLine("Cold and rainy - dress warmly!");
                }
                else
                {
                    Console.WriteLine("Cold but dry - bring a jacket!");
                }
            }
            
            // Complex conditions
            int examScore = 78;
            int attendance = 85;
            bool submittedProject = true;
            
            Console.WriteLine($"\nExam Score: {examScore}");
            Console.WriteLine($"Attendance: {attendance}%");
            Console.WriteLine($"Project Submitted: {submittedProject}");
            
            if (examScore >= 70 && attendance >= 75 && submittedProject)
            {
                Console.WriteLine("Congratulations! You passed the course!");
            }
            else
            {
                Console.WriteLine("You did not meet all requirements to pass.");
            }
        }
    }
}
