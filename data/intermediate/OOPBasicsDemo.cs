using System;

namespace IntermediateDemo
{
    // Car class demonstrating OOP concepts
    public class Car
    {
        // Private fields (encapsulation)
        private string brand;
        private string model;
        private int year;
        private double speed;
        private double fuel;
        
        // Public properties with validation
        public string Brand 
        { 
            get { return brand; }
            private set { brand = value; }
        }
        
        public string Model 
        { 
            get { return model; }
            private set { model = value; }
        }
        
        public int Year 
        { 
            get { return year; }
            private set { year = value; }
        }
        
        public double Speed 
        { 
            get { return speed; }
            private set { speed = value; }
        }
        
        public double Fuel 
        { 
            get { return fuel; }
            private set { fuel = value; }
        }
        
        // Read-only property
        public bool IsMoving => Speed > 0;
        
        // Auto-implemented property
        public string LicensePlate { get; set; }
        
        // Static property
        public static int TotalCarsCreated { get; private set; }
        
        // Default constructor
        public Car()
        {
            brand = "Unknown";
            model = "Unknown";
            year = DateTime.Now.Year;
            speed = 0;
            fuel = 50;
            LicensePlate = "UNREG";
            TotalCarsCreated++;
        }
        
        // Parameterized constructor
        public Car(string brand, string model, int year) : this()
        {
            this.brand = brand;
            this.model = model;
            this.year = year;
        }
        
        // Constructor chaining
        public Car(string brand, string model) : this(brand, model, DateTime.Now.Year)
        {
        }
        
        // Methods
        public void Accelerate(double amount)
        {
            if (amount > 0 && fuel > 0)
            {
                Speed += amount;
                Fuel -= amount * 0.1; // Consume fuel
                
                if (Fuel < 0) Fuel = 0;
                if (Speed > 200) Speed = 200; // Max speed limit
            }
        }
        
        public void Brake(double amount)
        {
            if (amount > 0)
            {
                Speed -= amount;
                if (Speed < 0) Speed = 0;
            }
        }
        
        public void Refuel(double amount)
        {
            if (amount > 0)
            {
                Fuel += amount;
                if (Fuel > 60) Fuel = 60; // Max fuel capacity
            }
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"{Year} {Brand} {Model}");
            Console.WriteLine($"License Plate: {LicensePlate}");
            Console.WriteLine($"Speed: {Speed:F1} km/h");
            Console.WriteLine($"Fuel: {Fuel:F1}L");
            Console.WriteLine($"Status: {(IsMoving ? "Moving" : "Stopped")}");
            Console.WriteLine();
        }
        
        // Static method
        public static void DisplayCarStatistics()
        {
            Console.WriteLine($"Total cars created: {TotalCarsCreated}");
        }
    }
    
    // BankAccount class demonstrating encapsulation
    public class BankAccount
    {
        private double balance;
        private string accountNumber;
        private string ownerName;
        
        public double Balance 
        { 
            get { return balance; }
            private set { balance = value; }
        }
        
        public string AccountNumber 
        { 
            get { return accountNumber; }
            private set { accountNumber = value; }
        }
        
        public string OwnerName 
        { 
            get { return ownerName; }
            set 
            { 
                if (!string.IsNullOrEmpty(value))
                    ownerName = value; 
            }
        }
        
        public BankAccount(string ownerName, double initialBalance = 0)
        {
            this.ownerName = ownerName;
            this.balance = initialBalance;
            this.accountNumber = GenerateAccountNumber();
        }
        
        public void Deposit(double amount)
        {
            if (amount > 0)
            {
                balance += amount;
                Console.WriteLine($"Deposited: ${amount:F2}. New balance: ${balance:F2}");
            }
            else
            {
                Console.WriteLine("Invalid deposit amount.");
            }
        }
        
        public bool Withdraw(double amount)
        {
            if (amount > 0 && balance >= amount)
            {
                balance -= amount;
                Console.WriteLine($"Withdrew: ${amount:F2}. New balance: ${balance:F2}");
                return true;
            }
            else
            {
                Console.WriteLine("Insufficient funds or invalid amount.");
                return false;
            }
        }
        
        public void DisplayAccountInfo()
        {
            Console.WriteLine($"Account: {accountNumber}");
            Console.WriteLine($"Owner: {ownerName}");
            Console.WriteLine($"Balance: ${balance:F2}");
        }
        
        private string GenerateAccountNumber()
        {
            Random rand = new Random();
            return rand.Next(100000, 999999).ToString();
        }
    }
    
    class OOPBasicsDemo
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== OOP Basics Demo ===\n");
            
            // Car objects
            Console.WriteLine("Car Objects:");
            Car car1 = new Car("Toyota", "Camry", 2022);
            car1.LicensePlate = "ABC123";
            
            Car car2 = new Car("Honda", "Civic");
            car2.LicensePlate = "XYZ789";
            
            Car car3 = new Car(); // Default constructor
            
            // Use car objects
            car1.DisplayInfo();
            car1.Accelerate(60);
            car1.DisplayInfo();
            car1.Brake(20);
            car1.DisplayInfo();
            
            car2.DisplayInfo();
            car2.Accelerate(80);
            car2.Refuel(20);
            car2.DisplayInfo();
            
            car3.DisplayInfo();
            
            // Static method
            Car.DisplayCarStatistics();
            
            Console.WriteLine("\nBank Account Objects:");
            
            // BankAccount objects
            BankAccount account1 = new BankAccount("Alice Johnson", 1000);
            BankAccount account2 = new BankAccount("Bob Smith");
            
            account1.DisplayAccountInfo();
            account1.Deposit(500);
            account1.Withdraw(200);
            account1.Withdraw(2000); // Should fail
            
            Console.WriteLine();
            account2.DisplayAccountInfo();
            account2.Deposit(300);
            account2.OwnerName = "Robert Smith";
            account2.DisplayAccountInfo();
        }
    }
}
