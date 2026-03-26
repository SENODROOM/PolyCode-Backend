# Events in C#

Events are a mechanism for class-to-class communication that follows the publisher-subscriber pattern. They allow objects to notify other objects when something of interest occurs.

## Event Basics

### Declaration and Usage

```csharp
public class Publisher
{
    // Declare an event
    public event EventHandler SomethingHappened;
    
    // Method to raise the event
    public void DoSomething()
    {
        Console.WriteLine("Something is happening...");
        
        // Raise the event
        SomethingHappened?.Invoke(this, EventArgs.Empty);
    }
}

public class Subscriber
{
    public void Subscribe(Publisher publisher)
    {
        // Subscribe to the event
        publisher.SomethingHappened += HandleSomethingHappened;
    }
    
    // Event handler method
    private void HandleSomethingHappened(object sender, EventArgs e)
    {
        Console.WriteLine("Subscriber: Something happened!");
    }
}
```

## Custom Event Arguments

### Creating Custom EventArgs

```csharp
// Custom event arguments class
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

public class Thermostat
{
    private double temperature;
    
    // Event with custom arguments
    public event EventHandler<TemperatureChangedEventArgs> TemperatureChanged;
    
    public double Temperature
    {
        get { return temperature; }
        set
        {
            if (temperature != value)
            {
                double oldTemp = temperature;
                temperature = value;
                
                // Raise event with custom arguments
                OnTemperatureChanged(new TemperatureChangedEventArgs(oldTemp, temperature));
            }
        }
    }
    
    protected virtual void OnTemperatureChanged(TemperatureChangedEventArgs e)
    {
        TemperatureChanged?.Invoke(this, e);
    }
}
```

## Event Accessors

### Custom Event Implementation

```csharp
public class CustomEventPublisher
{
    private EventHandlerList events = new EventHandlerList();
    private static readonly object SomethingHappenedKey = new object();
    
    // Custom event with explicit accessors
    public event EventHandler SomethingHappened
    {
        add
        {
            events.AddHandler(SomethingHappenedKey, value);
            Console.WriteLine($"Subscriber added: {value.Method.Name}");
        }
        remove
        {
            events.RemoveHandler(SomethingHappenedKey, value);
            Console.WriteLine($"Subscriber removed: {value.Method.Name}");
        }
    }
    
    public void DoSomething()
    {
        Console.WriteLine("Doing something...");
        
        // Get and invoke handlers
        EventHandler handler = (EventHandler)events[SomethingHappenedKey];
        handler?.Invoke(this, EventArgs.Empty);
    }
}
```

## Generic Event Handler

### Using EventHandler<T>

```csharp
public class NotificationEventArgs : EventArgs
{
    public string Message { get; }
    public NotificationLevel Level { get; }
    
    public NotificationEventArgs(string message, NotificationLevel level)
    {
        Message = message;
        Level = level;
    }
}

public enum NotificationLevel
{
    Info,
    Warning,
    Error
}

public class NotificationService
{
    // Generic event handler
    public event EventHandler<NotificationEventArgs> Notification;
    
    public void SendNotification(string message, NotificationLevel level)
    {
        var args = new NotificationEventArgs(message, level);
        OnNotification(args);
    }
    
    protected virtual void OnNotification(NotificationEventArgs e)
    {
        Notification?.Invoke(this, e);
    }
}
```

## Weak Events

### Preventing Memory Leaks

```csharp
public class WeakEventManager<TEventArgs> where TEventArgs : EventArgs
{
    private readonly List<WeakReference<EventHandler<TEventArgs>>> _handlers = new List<WeakReference<EventHandler<TEventArgs>>>();
    
    public void AddHandler(EventHandler<TEventArgs> handler)
    {
        _handlers.Add(new WeakReference<EventHandler<TEventArgs>>(handler));
    }
    
    public void RemoveHandler(EventHandler<TEventArgs> handler)
    {
        for (int i = _handlers.Count - 1; i >= 0; i--)
        {
            if (_handlers[i].TryGetTarget(out var target) && target == handler)
            {
                _handlers.RemoveAt(i);
                break;
            }
        }
    }
    
    public void RaiseEvent(object sender, TEventArgs e)
    {
        for (int i = _handlers.Count - 1; i >= 0; i--)
        {
            if (_handlers[i].TryGetTarget(out var handler))
            {
                handler?.Invoke(sender, e);
            }
            else
            {
                // Remove dead weak references
                _handlers.RemoveAt(i);
            }
        }
    }
}

public class WeakEventPublisher
{
    private readonly WeakEventManager<EventArgs> _eventManager = new WeakEventManager<EventArgs>();
    
    public event EventHandler SomethingHappened
    {
        add => _eventManager.AddHandler(value);
        remove => _eventManager.RemoveHandler(value);
    }
    
    public void DoSomething()
    {
        _eventManager.RaiseEvent(this, EventArgs.Empty);
    }
}
```

## Event Patterns

### Publisher-Subscriber Pattern

```csharp
public class StockMarket
{
    // Event for stock price changes
    public event EventHandler<StockPriceChangedEventArgs> StockPriceChanged;
    
    private Dictionary<string, decimal> stockPrices = new Dictionary<string, decimal>();
    
    public void UpdateStockPrice(string symbol, decimal newPrice)
    {
        if (stockPrices.TryGetValue(symbol, out decimal oldPrice))
        {
            stockPrices[symbol] = newPrice;
            
            var args = new StockPriceChangedEventArgs(symbol, oldPrice, newPrice);
            OnStockPriceChanged(args);
        }
        else
        {
            stockPrices[symbol] = newPrice;
        }
    }
    
    protected virtual void OnStockPriceChanged(StockPriceChangedEventArgs e)
    {
        StockPriceChanged?.Invoke(this, e);
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

public class StockTrader
{
    public string Name { get; }
    
    public StockTrader(string name)
    {
        Name = name;
    }
    
    public void SubscribeToStock(StockMarket market)
    {
        market.StockPriceChanged += OnStockPriceChanged;
    }
    
    private void OnStockPriceChanged(object sender, StockPriceChangedEventArgs e)
    {
        Console.WriteLine($"{Name} notified: {e.Symbol} changed from ${e.OldPrice} to ${e.NewPrice} ({e.PercentChange:F2}%)");
        
        // Trading logic
        if (e.PercentChange > 5)
        {
            Console.WriteLine($"  {Name}: Consider buying {e.Symbol}!");
        }
        else if (e.PercentChange < -5)
        {
            Console.WriteLine($"  {Name}: Consider selling {e.Symbol}!");
        }
    }
}
```

## Async Events

### Asynchronous Event Handling

```csharp
public class AsyncEventPublisher
{
    public event Func<object, AsyncEventArgs, Task> AsyncEvent;
    
    public async Task RaiseAsyncEvent()
    {
        var args = new AsyncEventArgs();
        
        if (AsyncEvent != null)
        {
            // Invoke all handlers concurrently
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
}

public class AsyncEventHandler
{
    public async Task HandleAsyncEvent(object sender, AsyncEventArgs e)
    {
        await Task.Delay(100); // Simulate async work
        e.Messages.Add($"Async handler completed at {DateTime.Now:HH:mm:ss}");
    }
}
```

## Event Best Practices

### Guidelines

```csharp
public class BestPracticePublisher
{
    // 1. Use EventHandler<T> for custom events
    public event EventHandler<CustomEventArgs> CustomEvent;
    
    // 2. Use protected virtual method for raising events
    protected virtual void OnCustomEvent(CustomEventArgs e)
    {
        CustomEvent?.Invoke(this, e);
    }
    
    // 3. Provide thread-safe event raising
    private readonly object eventLock = new object();
    
    public void TriggerEvent()
    {
        var args = new CustomEventArgs("Event triggered");
        
        lock (eventLock)
        {
            OnCustomEvent(args);
        }
    }
    
    // 4. Provide subscription management methods
    public void Subscribe(EventHandler<CustomEventArgs> handler)
    {
        CustomEvent += handler;
    }
    
    public void Unsubscribe(EventHandler<CustomEventArgs> handler)
    {
        CustomEvent -= handler;
    }
}

public class CustomEventArgs : EventArgs
{
    public string Message { get; }
    
    public CustomEventArgs(string message)
    {
        Message = message;
    }
}
```

## Common Event Scenarios

### UI Events

```csharp
public class Button
{
    public event EventHandler Click;
    
    public void SimulateClick()
    {
        Click?.Invoke(this, EventArgs.Empty);
    }
}

public class Form
{
    private Button button = new Button();
    
    public Form()
    {
        button.Click += Button_Click;
    }
    
    private void Button_Click(object sender, EventArgs e)
    {
        Console.WriteLine("Button was clicked!");
    }
    
    public void Show()
    {
        button.SimulateClick();
    }
}
```

### Data Change Events

```csharp
public class ObservablePerson
{
    private string name;
    
    public string Name
    {
        get { return name; }
        set
        {
            if (name != value)
            {
                string oldName = name;
                name = value;
                NameChanged?.Invoke(this, new PropertyChangedEventArgs(nameof(Name), oldName, name));
            }
        }
    }
    
    public event EventHandler<PropertyChangedEventArgs> NameChanged;
}

public class PropertyChangedEventArgs : EventArgs
{
    public string PropertyName { get; }
    public object OldValue { get; }
    public object NewValue { get; }
    
    public PropertyChangedEventArgs(string propertyName, object oldValue, object newValue)
    {
        PropertyName = propertyName;
        OldValue = oldValue;
        NewValue = newValue;
    }
}
```

## Best Practices

- Use `EventHandler<T>` for custom events
- Always check for null before raising events (`?.Invoke()`)
- Use protected virtual methods for raising events in derived classes
- Consider weak events for long-lived publishers
- Unsubscribe from events to prevent memory leaks
- Use meaningful event and argument names
- Keep event handlers short and non-blocking
- Consider async events for long-running operations
- Use proper exception handling in event handlers
- Document events and their expected behavior
