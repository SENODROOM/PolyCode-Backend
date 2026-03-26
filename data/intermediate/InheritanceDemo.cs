using System;

namespace IntermediateDemo
{
    // Base class
    public class Animal
    {
        public string Name { get; set; }
        public int Age { get; set; }
        public string Color { get; set; }
        
        public Animal(string name, int age, string color)
        {
            Name = name;
            Age = age;
            Color = color;
        }
        
        public virtual void Eat()
        {
            Console.WriteLine($"{Name} is eating.");
        }
        
        public virtual void Sleep()
        {
            Console.WriteLine($"{Name} is sleeping.");
        }
        
        public virtual void MakeSound()
        {
            Console.WriteLine($"{Name} makes a generic animal sound.");
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"{Name} - Age: {Age}, Color: {Color}");
        }
    }
    
    // Derived class: Dog
    public class Dog : Animal
    {
        public string Breed { get; set; }
        public bool IsTrained { get; set; }
        
        public Dog(string name, int age, string color, string breed, bool isTrained = false) 
            : base(name, age, color)
        {
            Breed = breed;
            IsTrained = isTrained;
        }
        
        public override void MakeSound()
        {
            Console.WriteLine($"{Name} barks: Woof! Woof!");
        }
        
        public override void Eat()
        {
            Console.WriteLine($"{Name} is eating dog food enthusiastically.");
        }
        
        // Dog-specific method
        public void WagTail()
        {
            Console.WriteLine($"{Name} is wagging its tail happily.");
        }
        
        public void Fetch()
        {
            if (IsTrained)
            {
                Console.WriteLine($"{Name} fetches the ball and brings it back!");
            }
            else
            {
                Console.WriteLine($"{Name} looks confused and doesn't fetch.");
            }
        }
    }
    
    // Derived class: Cat
    public class Cat : Animal
    {
        public bool IsIndoor { get; set; }
        public string FavoriteToy { get; set; }
        
        public Cat(string name, int age, string color, bool isIndoor = true, string favoriteToy = "ball") 
            : base(name, age, color)
        {
            IsIndoor = isIndoor;
            FavoriteToy = favoriteToy;
        }
        
        public override void MakeSound()
        {
            Console.WriteLine($"{Name} meows: Meow! Purr...");
        }
        
        public override void Sleep()
        {
            Console.WriteLine($"{Name} is sleeping in a cozy spot, purring loudly.");
        }
        
        // Cat-specific method
        public void Play()
        {
            Console.WriteLine($"{Name} is playing with its {FavoriteToy}.");
        }
        
        public void Climb()
        {
            if (!IsIndoor)
            {
                Console.WriteLine($"{Name} climbs a tree gracefully.");
            }
            else
            {
                Console.WriteLine($"{Name} climbs the cat tree.");
            }
        }
    }
    
    // Abstract base class for vehicles
    public abstract class Vehicle
    {
        public string Brand { get; set; }
        public string Model { get; set; }
        public int Year { get; set; }
        public double Speed { get; protected set; }
        
        protected Vehicle(string brand, string model, int year)
        {
            Brand = brand;
            Model = model;
            Year = year;
            Speed = 0;
        }
        
        public abstract void Accelerate(double amount);
        public abstract void Brake(double amount);
        
        public void DisplayInfo()
        {
            Console.WriteLine($"{Year} {Brand} {Model} - Speed: {Speed} km/h");
        }
    }
    
    // Derived class: Car
    public class Car : Vehicle
    {
        public int Doors { get; set; }
        public string FuelType { get; set; }
        
        public Car(string brand, string model, int year, int doors, string fuelType = "Gasoline") 
            : base(brand, model, year)
        {
            Doors = doors;
            FuelType = fuelType;
        }
        
        public override void Accelerate(double amount)
        {
            Speed += amount;
            if (Speed > 200) Speed = 200;
            Console.WriteLine($"Car accelerates to {Speed} km/h");
        }
        
        public override void Brake(double amount)
        {
            Speed -= amount;
            if (Speed < 0) Speed = 0;
            Console.WriteLine($"Car decelerates to {Speed} km/h");
        }
        
        public void OpenTrunk()
        {
            Console.WriteLine("Car trunk opens.");
        }
    }
    
    // Derived class: Motorcycle
    public class Motorcycle : Vehicle
    {
        public string Type { get; set; }
        public bool HasHelmet { get; set; }
        
        public Motorcycle(string brand, string model, int year, string type, bool hasHelmet = true) 
            : base(brand, model, year)
        {
            Type = type;
            HasHelmet = hasHelmet;
        }
        
        public override void Accelerate(double amount)
        {
            Speed += amount * 1.5; // Motorcycles accelerate faster
            if (Speed > 250) Speed = 250;
            Console.WriteLine($"Motorcycle accelerates to {Speed} km/h");
        }
        
        public override void Brake(double amount)
        {
            Speed -= amount * 1.2; // Motorcycles brake faster
            if (Speed < 0) Speed = 0;
            Console.WriteLine($"Motorcycle decelerates to {Speed} km/h");
        }
        
        public void DoWheelie()
        {
            if (Speed > 20 && Speed < 100)
            {
                Console.WriteLine("Motorcycle does a wheelie!");
            }
            else
            {
                Console.WriteLine("Cannot do wheelie at this speed.");
            }
        }
    }
    
    class InheritanceDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Inheritance Demo ===\n");
            
            // Animal inheritance
            Console.WriteLine("1. Animal Inheritance:");
            Dog dog = new Dog("Buddy", 3, "Golden", "Golden Retriever", true);
            Cat cat = new Cat("Whiskers", 2, "Black", true, "mouse toy");
            
            Console.WriteLine("Dog:");
            dog.DisplayInfo();
            dog.MakeSound();
            dog.Eat();
            dog.WagTail();
            dog.Fetch();
            
            Console.WriteLine("\nCat:");
            cat.DisplayInfo();
            cat.MakeSound();
            cat.Sleep();
            cat.Play();
            cat.Climb();
            
            // Polymorphism with animals
            Console.WriteLine("\n2. Polymorphism with Animals:");
            Animal[] animals = { dog, cat };
            
            foreach (Animal animal in animals)
            {
                Console.WriteLine($"\n{animal.GetType().Name}:");
                animal.DisplayInfo();
                animal.MakeSound();
                animal.Eat();
            }
            
            // Vehicle inheritance
            Console.WriteLine("\n3. Vehicle Inheritance:");
            Car car = new Car("Toyota", "Camry", 2022, 4, "Hybrid");
            Motorcycle motorcycle = new Motorcycle("Harley", "Sportster", 2021, "Cruiser", true);
            
            Console.WriteLine("Car:");
            car.DisplayInfo();
            car.Accelerate(50);
            car.Accelerate(30);
            car.Brake(20);
            car.OpenTrunk();
            
            Console.WriteLine("\nMotorcycle:");
            motorcycle.DisplayInfo();
            motorcycle.Accelerate(40);
            motorcycle.DoWheelie();
            motorcycle.Brake(30);
            
            // Polymorphism with vehicles
            Console.WriteLine("\n4. Polymorphism with Vehicles:");
            Vehicle[] vehicles = { car, motorcycle };
            
            foreach (Vehicle vehicle in vehicles)
            {
                Console.WriteLine($"\n{vehicle.GetType().Name}:");
                vehicle.DisplayInfo();
                vehicle.Accelerate(25);
                vehicle.Brake(10);
            }
        }
    }
}
