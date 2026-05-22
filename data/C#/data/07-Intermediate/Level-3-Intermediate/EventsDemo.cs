using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace AdvancedDemo
{
    // Custom event arguments
    public class TemperatureChangedEventArgs : EventArgs
    {
        public double OldTemperature { get; }
        public double NewTemperature { get; }
        public DateTime ChangeTime { get; }
        
        public TemperatureChangedEventArgs(double oldTemp, double newTemp)
        {
            OldTemperature = oldTemp;
            NewTemperature = newTemp;
            ChangeTime = DateTime.Now;
        }
    }
    
    public class StockPriceChangedEventArgs : EventArgs
    {
        public string Symbol { get; }
        public decimal OldPrice { get; }
        public decimal NewPrice { get; }
        public decimal Change => NewPrice - OldPrice;
        public double PercentChange => OldPrice != 0 ? (double)(Change / OldPrice * 100) : 0;
        
        public StockPriceChangedEventArgs(string symbol, decimal oldPrice, decimal newPrice)
        {
            Symbol = symbol;
            OldPrice = oldPrice;
            NewPrice = newPrice;
        }
    }
    
    public class NotificationEventArgs : EventArgs
    {
        public string Message { get; }
        public NotificationLevel Level { get; }
        public DateTime Timestamp { get; }
        
        public NotificationEventArgs(string message, NotificationLevel level)
        {
            Message = message;
            Level = level;
            Timestamp = DateTime.Now;
        }
    }
    
    public enum NotificationLevel
    {
        Info,
        Warning,
        Error
    }
    
    // Publisher classes
    public class Thermostat
    {
        private double temperature;
        
        public event EventHandler<TemperatureChangedEventArgs> TemperatureChanged;
        
        public double Temperature
        {
            get { return temperature; }
            set
            {
                if (Math.Abs(temperature - value) > 0.1) // Only trigger for significant changes
                {
                    double oldTemp = temperature;
                    temperature = value;
                    OnTemperatureChanged(new TemperatureChangedEventArgs(oldTemp, temperature));
                }
            }
        }
        
        protected virtual void OnTemperatureChanged(TemperatureChangedEventArgs e)
        {
            TemperatureChanged?.Invoke(this, e);
        }
    }
    
    public class StockMarket
    {
        private Dictionary<string, decimal> stockPrices = new Dictionary<string, decimal>();
        private readonly Random random = new Random();
        
        public event EventHandler<StockPriceChangedEventArgs> StockPriceChanged;
        
        public void AddStock(string symbol, decimal initialPrice)
        {
            stockPrices[symbol] = initialPrice;
        }
        
        public void SimulateMarketActivity()
        {
            var symbols = stockPrices.Keys.ToList();
            
            foreach (string symbol in symbols)
            {
                if (stockPrices.TryGetValue(symbol, out decimal currentPrice))
                {
                    // Simulate price change (-10% to +10%)
                    double changePercent = (random.NextDouble() - 0.5) * 0.2;
                    decimal newPrice = currentPrice * (1 + (decimal)changePercent);
                    
                    UpdateStockPrice(symbol, newPrice);
                }
            }
        }
        
        private void UpdateStockPrice(string symbol, decimal newPrice)
        {
            if (stockPrices.TryGetValue(symbol, out decimal oldPrice))
            {
                stockPrices[symbol] = newPrice;
                OnStockPriceChanged(new StockPriceChangedEventArgs(symbol, oldPrice, newPrice));
            }
        }
        
        protected virtual void OnStockPriceChanged(StockPriceChangedEventArgs e)
        {
            StockPriceChanged?.Invoke(this, e);
        }
    }
    
    public class NotificationService
    {
        public event EventHandler<NotificationEventArgs> Notification;
        
        public void SendInfo(string message)
        {
            SendNotification(message, NotificationLevel.Info);
        }
        
        public void SendWarning(string message)
        {
            SendNotification(message, NotificationLevel.Warning);
        }
        
        public void SendError(string message)
        {
            SendNotification(message, NotificationLevel.Error);
        }
        
        private void SendNotification(string message, NotificationLevel level)
        {
            var args = new NotificationEventArgs(message, level);
            OnNotification(args);
        }
        
        protected virtual void OnNotification(NotificationEventArgs e)
        {
            Notification?.Invoke(this, e);
        }
    }
    
    // Subscriber classes
    public class TemperatureMonitor
    {
        public void Subscribe(Thermostat thermostat)
        {
            thermostat.TemperatureChanged += OnTemperatureChanged;
        }
        
        private void OnTemperatureChanged(object sender, TemperatureChangedEventArgs e)
        {
            Console.WriteLine($"Temperature Monitor: {e.OldTemperature:F1}°C → {e.NewTemperature:F1}°C at {e.ChangeTime:HH:mm:ss}");
            
            if (e.NewTemperature > 30)
            {
                Console.WriteLine("  WARNING: High temperature detected!");
            }
            else if (e.NewTemperature < 10)
            {
                Console.WriteLine("  WARNING: Low temperature detected!");
            }
        }
    }
    
    public class StockTrader
    {
        public string Name { get; }
        private Dictionary<string, decimal> portfolio = new Dictionary<string, decimal>();
        
        public StockTrader(string name)
        {
            Name = name;
        }
        
        public void SubscribeToMarket(StockMarket market)
        {
            market.StockPriceChanged += OnStockPriceChanged;
        }
        
        public void BuyStock(string symbol, decimal price)
        {
            portfolio[symbol] = price;
            Console.WriteLine($"{Name} bought {symbol} at ${price:F2}");
        }
        
        private void OnStockPriceChanged(object sender, StockPriceChangedEventArgs e)
        {
            Console.WriteLine($"{Name} notified: {e.Symbol} ${e.OldPrice:F2} → ${e.NewPrice:F2} ({e.PercentChange:+0.00;-0.00}%)");
            
            // Check if we own this stock
            if (portfolio.TryGetValue(e.Symbol, out decimal purchasePrice))
            {
                decimal profit = e.NewPrice - purchasePrice;
                double profitPercent = (double)(profit / purchasePrice * 100);
                
                if (profitPercent > 10)
                {
                    Console.WriteLine($"  {Name}: Time to sell {e.Symbol}! Profit: {profitPercent:+0.00}%");
                }
                else if (profitPercent < -5)
                {
                    Console.WriteLine($"  {Name}: Consider cutting losses on {e.Symbol}! Loss: {profitPercent:0.00}%");
                }
            }
            else
            {
                // Look for buying opportunities
                if (e.PercentChange < -8)
                {
                    Console.WriteLine($"  {Name}: {e.Symbol} looks cheap! Consider buying.");
                    BuyStock(e.Symbol, e.NewPrice);
                }
            }
        }
    }
    
    public class NotificationLogger
    {
        private List<NotificationEventArgs> notifications = new List<NotificationEventArgs>();
        
        public void Subscribe(NotificationService service)
        {
            service.Notification += OnNotification;
        }
        
        private void OnNotification(object sender, NotificationEventArgs e)
        {
            notifications.Add(e);
            
            string prefix = e.Level switch
            {
                NotificationLevel.Info => "INFO",
                NotificationLevel.Warning => "WARN",
                NotificationLevel.Error => "ERROR",
                _ => "UNKNOWN"
            };
            
            Console.WriteLine($"[{e.Timestamp:HH:mm:ss}] {prefix}: {e.Message}");
        }
        
        public void PrintSummary()
        {
            Console.WriteLine($"\nNotification Summary:");
            Console.WriteLine($"  Total: {notifications.Count}");
            Console.WriteLine($"  Info: {notifications.Count(n => n.Level == NotificationLevel.Info)}");
            Console.WriteLine($"  Warning: {notifications.Count(n => n.Level == NotificationLevel.Warning)}");
            Console.WriteLine($"  Error: {notifications.Count(n => n.Level == NotificationLevel.Error)}");
        }
    }
    
    // Async event example
    public class AsyncEventPublisher
    {
        public event Func<object, AsyncEventArgs, Task> AsyncEvent;
        
        public async Task RaiseAsyncEvent()
        {
            var args = new AsyncEventArgs();
            
            if (AsyncEvent != null)
            {
                var tasks = AsyncEvent.GetInvocationList()
                    .Cast<Func<object, AsyncEventArgs, Task>>()
                    .Select(handler => handler(this, args));
                
                await Task.WhenAll(tasks);
            }
        }
    }
    
    public class AsyncEventArgs : EventArgs
    {
        public List<string> Messages { get; } = new List<string>();
        public DateTime StartTime { get; } = DateTime.Now;
    }
    
    public class AsyncEventHandler
    {
        public async Task HandleAsyncEvent(object sender, AsyncEventArgs e)
        {
            int delay = new Random().Next(100, 500);
            await Task.Delay(delay);
            
            e.Messages.Add($"Handler completed in {delay}ms at {DateTime.Now:HH:mm:ss}");
        }
    }
    
    class EventsDemo
    {
        static async Task Main(string[] args)
        {
            Console.WriteLine("=== Events Demo ===\n");
            
            // 1. Temperature monitoring example
            Console.WriteLine("1. Temperature Monitoring:");
            DemonstrateTemperatureEvents();
            
            // 2. Stock market example
            Console.WriteLine("\n2. Stock Market Trading:");
            DemonstrateStockMarketEvents();
            
            // 3. Notification system
            Console.WriteLine("\n3. Notification System:");
            DemonstrateNotificationEvents();
            
            // 4. Async events
            Console.WriteLine("\n4. Async Events:");
            await DemonstrateAsyncEvents();
            
            // 5. Event subscription management
            Console.WriteLine("\n5. Event Subscription Management:");
            DemonstrateEventManagement();
        }
        
        static void DemonstrateTemperatureEvents()
        {
            var thermostat = new Thermostat();
            var monitor = new TemperatureMonitor();
            
            // Subscribe to temperature changes
            monitor.Subscribe(thermostat);
            
            // Simulate temperature changes
            thermostat.Temperature = 20;
            thermostat.Temperature = 25;
            thermostat.Temperature = 32; // Should trigger warning
            thermostat.Temperature = 8;  // Should trigger warning
            thermostat.Temperature = 22;
        }
        
        static void DemonstrateStockMarketEvents()
        {
            var market = new StockMarket();
            var trader1 = new StockTrader("Alice");
            var trader2 = new StockTrader("Bob");
            
            // Add some stocks
            market.AddStock("AAPL", 150m);
            market.AddStock("GOOGL", 2500m);
            market.AddStock("MSFT", 300m);
            
            // Subscribe traders
            trader1.SubscribeToMarket(market);
            trader2.SubscribeToMarket(market);
            
            // Give one trader some initial stocks
            trader1.BuyStock("AAPL", 150m);
            trader2.BuyStock("GOOGL", 2500m);
            
            // Simulate market activity
            Console.WriteLine("Simulating market activity...");
            for (int i = 0; i < 5; i++)
            {
                market.SimulateMarketActivity();
                System.Threading.Thread.Sleep(500);
            }
        }
        
        static void DemonstrateNotificationEvents()
        {
            var notificationService = new NotificationService();
            var logger = new NotificationLogger();
            
            // Subscribe logger
            logger.Subscribe(notificationService);
            
            // Send various notifications
            notificationService.SendInfo("Application started successfully");
            notificationService.SendWarning("Memory usage is above 80%");
            notificationService.SendInfo("User logged in");
            notificationService.SendError("Database connection failed");
            notificationService.SendWarning("Disk space is running low");
            
            // Print summary
            logger.PrintSummary();
        }
        
        static async Task DemonstrateAsyncEvents()
        {
            var publisher = new AsyncEventPublisher();
            var handler1 = new AsyncEventHandler();
            var handler2 = new AsyncEventHandler();
            var handler3 = new AsyncEventHandler();
            
            // Subscribe async handlers
            publisher.AsyncEvent += handler1.HandleAsyncEvent;
            publisher.AsyncEvent += handler2.HandleAsyncEvent;
            publisher.AsyncEvent += handler3.HandleAsyncEvent;
            
            Console.WriteLine("Raising async event...");
            await publisher.RaiseAsyncEvent();
            
            Console.WriteLine("All async handlers completed!");
        }
        
        static void DemonstrateEventManagement()
        {
            var thermostat = new Thermostat();
            var monitor = new TemperatureMonitor();
            
            // Subscribe
            thermostat.TemperatureChanged += monitor.OnTemperatureChanged;
            Console.WriteLine("Subscribed to temperature changes");
            
            thermostat.Temperature = 25;
            
            // Unsubscribe
            thermostat.TemperatureChanged -= monitor.OnTemperatureChanged;
            Console.WriteLine("Unsubscribed from temperature changes");
            
            thermostat.Temperature = 30; // No notification should appear
            Console.WriteLine("Temperature changed but no notification received (unsubscribed)");
        }
    }
}
