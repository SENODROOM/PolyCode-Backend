# Async/Await in C#

Async/await is a powerful feature in C# that enables asynchronous programming, allowing applications to remain responsive while performing long-running operations.

## Understanding Asynchronous Programming

### Synchronous vs Asynchronous

```csharp
// Synchronous - blocks the thread
public void DownloadFileSync(string url)
{
    var client = new HttpClient();
    string content = client.GetStringAsync(url).Result; // Blocks!
    Console.WriteLine(content);
}

// Asynchronous - doesn't block the thread
public async Task DownloadFileAsync(string url)
{
    var client = new HttpClient();
    string content = await client.GetStringAsync(url); // Non-blocking
    Console.WriteLine(content);
}
```

## Basic Async/Await Syntax

### Key Components

```csharp
// Method returning Task (no return value)
public async Task DoWorkAsync()
{
    Console.WriteLine("Starting work...");
    await Task.Delay(1000); // Simulate async operation
    Console.WriteLine("Work completed!");
}

// Method returning Task<T> (with return value)
public async Task<string> GetDataAsync()
{
    await Task.Delay(500);
    return "Data retrieved";
}

// Method returning ValueTask<T> (for better performance with synchronous completion)
public async ValueTask<int> CalculateAsync()
{
    await Task.Delay(100);
    return 42;
}
```

## Async Method Patterns

### Simple Async Method

```csharp
public class DataProcessor
{
    public async Task ProcessDataAsync()
    {
        Console.WriteLine("Starting data processing...");
        
        // Simulate reading data
        string data = await ReadDataAsync();
        Console.WriteLine($"Data read: {data}");
        
        // Simulate processing data
        string result = await ProcessDataAsync(data);
        Console.WriteLine($"Data processed: {result}");
        
        // Simulate saving result
        await SaveDataAsync(result);
        Console.WriteLine("Data processing completed!");
    }
    
    private async Task<string> ReadDataAsync()
    {
        await Task.Delay(1000); // Simulate I/O operation
        return "Raw data";
    }
    
    private async Task<string> ProcessDataAsync(string data)
    {
        await Task.Delay(500); // Simulate CPU-bound work
        return data.ToUpper();
    }
    
    private async Task SaveDataAsync(string result)
    {
        await Task.Delay(300); // Simulate I/O operation
        // Save to database/file
    }
}
```

## Parallel Async Operations

### Running Multiple Tasks Concurrently

```csharp
public class ParallelProcessor
{
    public async Task ProcessMultipleFilesAsync()
    {
        var files = new[] { "file1.txt", "file2.txt", "file3.txt" };
        
        // Process files sequentially
        foreach (var file in files)
        {
            await ProcessFileAsync(file);
        }
        
        // Process files concurrently
        var tasks = files.Select(file => ProcessFileAsync(file));
        await Task.WhenAll(tasks);
        
        // Process with individual results
        var tasksWithResults = files.Select(file => ProcessFileWithResultAsync(file));
        var results = await Task.WhenAll(tasksWithResults);
        
        foreach (var result in results)
        {
            Console.WriteLine($"Result: {result}");
        }
    }
    
    private async Task ProcessFileAsync(string filename)
    {
        Console.WriteLine($"Processing {filename}...");
        await Task.Delay(1000);
        Console.WriteLine($"Completed {filename}");
    }
    
    private async Task<string> ProcessFileWithResultAsync(string filename)
    {
        await Task.Delay(1000);
        return $"Processed {filename}";
    }
}
```

### WhenAny for First Completion

```csharp
public async Task<string> GetFirstResponseAsync()
{
    var tasks = new[]
    {
        FetchFromService1Async(),
        FetchFromService2Async(),
        FetchFromService3Async()
    };
    
    Task<string> firstCompleted = await Task.WhenAny(tasks);
    string result = await firstCompleted;
    
    return result;
}

private async Task<string> FetchFromService1Async()
{
    await Task.Delay(2000);
    return "Service 1 response";
}

private async Task<string> FetchFromService2Async()
{
    await Task.Delay(1000);
    return "Service 2 response";
}

private async Task<string> FetchFromService3Async()
{
    await Task.Delay(3000);
    return "Service 3 response";
}
```

## Exception Handling in Async Methods

### Try-Catch with Async

```csharp
public async Task HandleExceptionsAsync()
{
    try
    {
        await RiskyOperationAsync();
    }
    catch (HttpRequestException ex)
    {
        Console.WriteLine($"HTTP error: {ex.Message}");
    }
    catch (TimeoutException ex)
    {
        Console.WriteLine($"Timeout: {ex.Message}");
    }
    catch (Exception ex)
    {
        Console.WriteLine($"General error: {ex.Message}");
    }
}

private async Task RiskyOperationAsync()
{
    await Task.Delay(500);
    throw new InvalidOperationException("Something went wrong!");
}
```

### Exception Handling with Multiple Tasks

```csharp
public async Task HandleMultipleTaskExceptionsAsync()
{
    var tasks = new[]
    {
        Task.Run(() => { throw new InvalidOperationException("Task 1 failed"); }),
        Task.Run(() => { throw new ArgumentException("Task 2 failed"); }),
        Task.Run(() => { return "Task 3 succeeded"; })
    };
    
    try
    {
        await Task.WhenAll(tasks);
    }
    catch (Exception ex)
    {
        // WhenAll throws the first exception, but others are available
        Console.WriteLine($"Caught: {ex.Message}");
        
        // Check all tasks for exceptions
        foreach (var task in tasks)
        {
            if (task.IsFaulted)
            {
                foreach (var innerEx in task.Exception.InnerExceptions)
                {
                    Console.WriteLine($"Task exception: {innerEx.Message}");
                }
            }
        }
    }
}
```

## Cancellation in Async Operations

### Using CancellationToken

```csharp
public class CancellableOperation
{
    public async Task LongRunningOperationAsync(CancellationToken cancellationToken = default)
    {
        for (int i = 0; i < 10; i++)
        {
            cancellationToken.ThrowIfCancellationRequested();
            
            Console.WriteLine($"Processing step {i}...");
            await Task.Delay(1000, cancellationToken);
        }
        
        Console.WriteLine("Operation completed!");
    }
    
    public async Task DemonstrateCancellationAsync()
    {
        var cts = new CancellationTokenSource();
        
        // Start operation
        var operationTask = LongRunningOperationAsync(cts.Token);
        
        // Cancel after 3 seconds
        cts.CancelAfter(TimeSpan.FromSeconds(3));
        
        try
        {
            await operationTask;
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Operation was cancelled!");
        }
    }
}
```

## Async Streams (C# 8.0+)

### IAsyncEnumerable

```csharp
public async IAsyncEnumerable<int> GenerateNumbersAsync()
{
    for (int i = 0; i < 10; i++)
    {
        await Task.Delay(500); // Simulate async work
        yield return i;
    }
}

public async Task ConsumeAsyncStreamAsync()
{
    await foreach (int number in GenerateNumbersAsync())
    {
        Console.WriteLine($"Received: {number}");
    }
}

// With cancellation
public async Task ConsumeWithCancellationAsync(CancellationToken cancellationToken)
{
    await foreach (int number in GenerateNumbersAsync().WithCancellation(cancellationToken))
    {
        Console.WriteLine($"Received: {number}");
    }
}
```

## ConfigureAwait

### Avoiding Deadlocks

```csharp
public class ConfigureAwaitExample
{
    // Bad - can cause deadlocks in UI/ASP.NET contexts
    public async Task<string> BadExampleAsync()
    {
        var result = await GetDataAsync(); // Captures context
        return result.ToUpper();
    }
    
    // Good - avoids deadlocks
    public async Task<string> GoodExampleAsync()
    {
        var result = await GetDataAsync().ConfigureAwait(false); // Doesn't capture context
        return result.ToUpper();
    }
    
    private async Task<string> GetDataAsync()
    {
        await Task.Delay(1000);
        return "data";
    }
}
```

## Async in Different Contexts

### UI Applications

```csharp
public class UIExample
{
    // Event handler in UI
    private async void Button_Click(object sender, EventArgs e)
    {
        button.Enabled = false;
        
        try
        {
            string data = await LoadDataAsync();
            textBox.Text = data;
        }
        catch (Exception ex)
        {
            MessageBox.Show($"Error: {ex.Message}");
        }
        finally
        {
            button.Enabled = true;
        }
    }
    
    private async Task<string> LoadDataAsync()
    {
        await Task.Delay(2000);
        return "Loaded data";
    }
}
```

### ASP.NET Core

```csharp
[ApiController]
[Route("api/[controller]")]
public class DataController : ControllerBase
{
    [HttpGet]
    public async Task<IActionResult> GetDataAsync()
    {
        try
        {
            var data = await _dataService.GetDataAsync();
            return Ok(data);
        }
        catch (Exception ex)
        {
            return StatusCode(500, ex.Message);
        }
    }
    
    [HttpPost]
    public async Task<IActionResult> SaveDataAsync([FromBody] DataModel model)
    {
        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }
        
        await _dataService.SaveDataAsync(model);
        return Ok();
    }
}
```

## Performance Considerations

### ValueTask for High-Frequency Operations

```csharp
public class HighFrequencyOperations
{
    private readonly Dictionary<int, string> _cache = new();
    
    // Use ValueTask when operation might complete synchronously
    public async ValueTask<string> GetCachedDataAsync(int key)
    {
        if (_cache.TryGetValue(key, out string cachedValue))
        {
            return cachedValue; // Synchronous completion
        }
        
        // Asynchronous operation
        string data = await LoadDataFromSourceAsync(key);
        _cache[key] = data;
        return data;
    }
    
    private async Task<string> LoadDataFromSourceAsync(int key)
    {
        await Task.Delay(100);
        return $"Data for {key}";
    }
}
```

## Best Practices

### DO:
- Use `async` all the way down (avoid async void)
- Use `ConfigureAwait(false)` in library code
- Handle exceptions properly with try-catch
- Use cancellation tokens for long-running operations
- Consider `ValueTask` for high-frequency operations
- Use `Task.WhenAll` for concurrent operations

### DON'T:
- Use `.Result` or `.Wait()` (can cause deadlocks)
- Use `async void` except for event handlers
- Forget to handle exceptions in async methods
- Mix blocking and async code
- Ignore cancellation tokens when available
- Create async methods that don't need to be async

## Common Patterns

### Async Factory Pattern

```csharp
public class AsyncFactory
{
    private readonly string _data;
    
    private AsyncFactory(string data)
    {
        _data = data;
    }
    
    public static async Task<AsyncFactory> CreateAsync()
    {
        string data = await LoadInitialDataAsync();
        return new AsyncFactory(data);
    }
    
    private static async Task<string> LoadInitialDataAsync()
    {
        await Task.Delay(1000);
        return "Initial data";
    }
}
```

### Async Initialization Pattern

```csharp
public class AsyncInitializer
{
    private bool _initialized = false;
    private readonly SemaphoreSlim _semaphore = new(1, 1);
    
    public async Task EnsureInitializedAsync()
    {
        if (_initialized) return;
        
        await _semaphore.WaitAsync();
        try
        {
            if (!_initialized)
            {
                await InitializeAsync();
                _initialized = true;
            }
        }
        finally
        {
            _semaphore.Release();
        }
    }
    
    private async Task InitializeAsync()
    {
        // Perform async initialization
        await Task.Delay(1000);
    }
}
```
