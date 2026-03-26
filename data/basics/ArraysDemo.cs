using System;

namespace BasicsDemo
{
    class ArraysDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Arrays Demo ===\n");
            
            // Single-dimensional array
            Console.WriteLine("1. Single-Dimensional Array:");
            int[] numbers = { 10, 20, 30, 40, 50 };
            Console.WriteLine("Array elements:");
            foreach (int num in numbers)
            {
                Console.Write($"{num} ");
            }
            Console.WriteLine($"\nArray length: {numbers.Length}");
            Console.WriteLine($"First element: {numbers[0]}");
            Console.WriteLine($"Last element: {numbers[numbers.Length - 1]}");
            
            // Modify array elements
            numbers[2] = 35;
            Console.WriteLine("\nAfter modifying index 2:");
            foreach (int num in numbers)
            {
                Console.Write($"{num} ");
            }
            
            // Two-dimensional array
            Console.WriteLine("\n\n2. Two-Dimensional Array:");
            int[,] matrix = { 
                { 1, 2, 3 }, 
                { 4, 5, 6 }, 
                { 7, 8, 9 } 
            };
            
            Console.WriteLine("Matrix elements:");
            for (int i = 0; i < matrix.GetLength(0); i++)
            {
                for (int j = 0; j < matrix.GetLength(1); j++)
                {
                    Console.Write($"{matrix[i, j],3}");
                }
                Console.WriteLine();
            }
            
            Console.WriteLine($"Matrix dimensions: {matrix.GetLength(0)}x{matrix.GetLength(1)}");
            
            // Jagged array
            Console.WriteLine("\n3. Jagged Array:");
            int[][] jagged = new int[][]
            {
                new int[] { 1, 2 },
                new int[] { 3, 4, 5 },
                new int[] { 6, 7, 8, 9 }
            };
            
            Console.WriteLine("Jagged array elements:");
            for (int i = 0; i < jagged.Length; i++)
            {
                Console.Write($"Row {i}: ");
                for (int j = 0; j < jagged[i].Length; j++)
                {
                    Console.Write($"{jagged[i][j]} ");
                }
                Console.WriteLine();
            }
            
            // Array operations
            Console.WriteLine("\n4. Array Operations:");
            string[] fruits = { "Apple", "Orange", "Banana", "Grape", "Mango" };
            
            Console.WriteLine("Original array:");
            foreach (string fruit in fruits)
            {
                Console.Write($"{fruit} ");
            }
            
            // Sort array
            Array.Sort(fruits);
            Console.WriteLine("\n\nAfter sorting:");
            foreach (string fruit in fruits)
            {
                Console.Write($"{fruit} ");
            }
            
            // Reverse array
            Array.Reverse(fruits);
            Console.WriteLine("\n\nAfter reversing:");
            foreach (string fruit in fruits)
            {
                Console.Write($"{fruit} ");
            }
            
            // Search in array
            int[] scores = { 85, 92, 78, 95, 88 };
            int index = Array.IndexOf(scores, 95);
            Console.WriteLine($"\n\nScore 95 found at index: {index}");
            
            bool hasHighScore = Array.Exists(scores, score => score >= 90);
            Console.WriteLine($"Has score >= 90: {hasHighScore}");
            
            // Array copying
            Console.WriteLine("\n5. Array Copying:");
            int[] source = { 1, 2, 3, 4, 5 };
            int[] destination = new int[5];
            Array.Copy(source, destination, 5);
            
            Console.WriteLine("Source array:");
            foreach (int num in source)
            {
                Console.Write($"{num} ");
            }
            
            Console.WriteLine("\nDestination array:");
            foreach (int num in destination)
            {
                Console.Write($"{num} ");
            }
            
            // Dynamic array operations
            Console.WriteLine("\n\n6. Dynamic Array Operations:");
            int[] dynamicArray = new int[0];
            
            // Add elements (creating new array each time)
            for (int i = 1; i <= 5; i++)
            {
                Array.Resize(ref dynamicArray, dynamicArray.Length + 1);
                dynamicArray[dynamicArray.Length - 1] = i * 10;
            }
            
            Console.WriteLine("Dynamic array after adding elements:");
            foreach (int num in dynamicArray)
            {
                Console.Write($"{num} ");
            }
            
            // Find min and max
            Console.WriteLine("\n\n7. Min and Max:");
            int[] values = { 15, 3, 8, 23, 42, 7, 16 };
            
            int min = values[0];
            int max = values[0];
            int sum = 0;
            
            foreach (int val in values)
            {
                if (val < min) min = val;
                if (val > max) max = val;
                sum += val;
            }
            
            Console.WriteLine($"Array: {string.Join(", ", values)}");
            Console.WriteLine($"Min: {min}");
            Console.WriteLine($"Max: {max}");
            Console.WriteLine($"Sum: {sum}");
            Console.WriteLine($"Average: {(double)sum / values.Length:F2}");
        }
    }
}
