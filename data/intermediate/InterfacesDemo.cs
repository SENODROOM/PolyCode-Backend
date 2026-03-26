using System;

namespace IntermediateDemo
{
    // Basic interface
    public interface IShape
    {
        double Area { get; }
        double Perimeter { get; }
        double GetArea();
        double GetPerimeter();
        void DisplayInfo();
    }
    
    // Another interface
    public interface IDrawable
    {
        void Draw();
        string Color { get; set; }
    }
    
    // Interface with default implementation (C# 8.0+)
    public interface ILogger
    {
        void Log(string message);
        
        void LogError(string error)
        {
            Log($"ERROR: {error}");
        }
        
        void LogWarning(string warning)
        {
            Log($"WARNING: {warning}");
        }
    }
    
    // Interface inheritance
    public interface IVehicle
    {
        string Brand { get; set; }
        string Model { get; set; }
        void Start();
        void Stop();
    }
    
    public interface ICar : IVehicle
    {
        int Doors { get; set; }
        void OpenTrunk();
    }
    
    // Rectangle implementing IShape
    public class Rectangle : IShape, IDrawable
    {
        public double Width { get; set; }
        public double Height { get; set; }
        
        public double Area => Width * Height;
        public double Perimeter => 2 * (Width + Height);
        
        public string Color { get; set; } = "Red";
        
        public Rectangle(double width, double height)
        {
            Width = width;
            Height = height;
        }
        
        public double GetArea() => Area;
        public double GetPerimeter() => Perimeter;
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Rectangle: {Width} x {Height}");
            Console.WriteLine($"Area: {Area:F2}, Perimeter: {Perimeter:F2}");
        }
        
        public void Draw()
        {
            Console.WriteLine($"Drawing {Color} rectangle");
        }
    }
    
    // Circle implementing multiple interfaces
    public class Circle : IShape, IDrawable
    {
        public double Radius { get; set; }
        
        public double Area => Math.PI * Radius * Radius;
        public double Perimeter => 2 * Math.PI * Radius;
        
        public string Color { get; set; } = "Blue";
        
        public Circle(double radius)
        {
            Radius = radius;
        }
        
        public double GetArea() => Area;
        public double GetPerimeter() => Perimeter;
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Circle: Radius = {Radius}");
            Console.WriteLine($"Area: {Area:F2}, Perimeter: {Perimeter:F2}");
        }
        
        public void Draw()
        {
            Console.WriteLine($"Drawing {Color} circle");
        }
    }
    
    // Triangle implementing IShape
    public class Triangle : IShape
    {
        public double Base { get; set; }
        public double Height { get; set; }
        public double SideA { get; set; }
        public double SideB { get; set; }
        
        public double Area => 0.5 * Base * Height;
        public double Perimeter => Base + SideA + SideB;
        
        public Triangle(double @base, double height, double sideA, double sideB)
        {
            Base = @base;
            Height = height;
            SideA = sideA;
            SideB = sideB;
        }
        
        public double GetArea() => Area;
        public double GetPerimeter() => Perimeter;
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Triangle: Base = {Base}, Height = {Height}");
            Console.WriteLine($"Area: {Area:F2}, Perimeter: {Perimeter:F2}");
        }
    }
    
    // Console logger implementing ILogger
    public class ConsoleLogger : ILogger
    {
        public void Log(string message)
        {
            Console.WriteLine($"{DateTime.Now:HH:mm:ss}: {message}");
        }
        // LogError and LogWarning use default implementations
    }
    
    // Electric car implementing interface inheritance
    public class ElectricCar : ICar
    {
        public string Brand { get; set; }
        public string Model { get; set; }
        public int Doors { get; set; }
        public double BatteryLevel { get; set; }
        
        public ElectricCar(string brand, string model, int doors)
        {
            Brand = brand;
            Model = model;
            Doors = doors;
            BatteryLevel = 100;
        }
        
        public void Start()
        {
            Console.WriteLine($"{Brand} {Model} starts silently.");
        }
        
        public void Stop()
        {
            Console.WriteLine($"{Brand} {Model} stops.");
        }
        
        public void OpenTrunk()
        {
            Console.WriteLine("Electric trunk opens automatically.");
        }
    }
    
    // Explicit interface implementation
    public interface IA
    {
        void DoWork();
    }
    
    public interface IB
    {
        void DoWork();
    }
    
    public class MultiImplementation : IA, IB
    {
        public void DoWork()
        {
            Console.WriteLine("Regular DoWork implementation");
        }
        
        void IA.DoWork()
        {
            Console.WriteLine("IA.DoWork implementation");
        }
        
        void IB.DoWork()
        {
            Console.WriteLine("IB.DoWork implementation");
        }
    }
    
    class InterfacesDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Interfaces Demo ===\n");
            
            // 1. Basic interface implementation
            Console.WriteLine("1. Basic Interface Implementation:");
            IShape[] shapes = new IShape[]
            {
                new Rectangle(5, 3),
                new Circle(4),
                new Triangle(6, 4, 5, 5)
            };
            
            foreach (IShape shape in shapes)
            {
                shape.DisplayInfo();
                Console.WriteLine($"Area: {shape.GetArea():F2}");
                Console.WriteLine($"Perimeter: {shape.GetPerimeter():F2}");
                
                // Check if shape is also drawable
                if (shape is IDrawable drawable)
                {
                    drawable.Draw();
                }
                Console.WriteLine();
            }
            
            // 2. Interface with default implementation
            Console.WriteLine("2. Interface with Default Implementation:");
            ILogger logger = new ConsoleLogger();
            logger.Log("Application started");
            logger.LogError("Something went wrong!");
            logger.LogWarning("Low disk space");
            Console.WriteLine();
            
            // 3. Interface inheritance
            Console.WriteLine("3. Interface Inheritance:");
            ICar tesla = new ElectricCar("Tesla", "Model 3", 4);
            tesla.Start();
            tesla.OpenTrunk();
            tesla.Stop();
            Console.WriteLine();
            
            // 4. Explicit interface implementation
            Console.WriteLine("4. Explicit Interface Implementation:");
            MultiImplementation multi = new MultiImplementation();
            multi.DoWork(); // Regular implementation
            
            IA interfaceA = multi;
            interfaceA.DoWork(); // IA implementation
            
            IB interfaceB = multi;
            interfaceB.DoWork(); // IB implementation
            Console.WriteLine();
            
            // 5. Polymorphism with interfaces
            Console.WriteLine("5. Polymorphism with Interfaces:");
            ProcessShapes(shapes);
            
            // 6. Interface as method parameter
            Console.WriteLine("6. Interface as Method Parameter:");
            IDrawable[] drawables = new IDrawable[]
            {
                new Rectangle(3, 2) { Color = "Green" },
                new Circle(2.5) { Color = "Yellow" }
            };
            
            DrawAllObjects(drawables);
        }
        
        // Method accepting interface parameter
        static void ProcessShapes(IShape[] shapes)
        {
            Console.WriteLine("Processing shapes:");
            foreach (IShape shape in shapes)
            {
                Console.WriteLine($"Shape type: {shape.GetType().Name}");
                Console.WriteLine($"Area: {shape.Area:F2}");
                Console.WriteLine($"Perimeter: {shape.Perimeter:F2}");
                Console.WriteLine();
            }
        }
        
        // Method accepting another interface parameter
        static void DrawAllObjects(IDrawable[] drawables)
        {
            Console.WriteLine("Drawing all objects:");
            foreach (IDrawable drawable in drawables)
            {
                drawable.Draw();
            }
        }
    }
}
