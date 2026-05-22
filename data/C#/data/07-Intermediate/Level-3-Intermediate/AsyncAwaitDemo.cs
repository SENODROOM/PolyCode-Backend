using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Threading;
using System.Threading.Tasks;

namespace AdvancedDemo
{
    public class AsyncAwaitDemo
    {
        private static readonly HttpClient httpClient = new HttpClient();
        
        static async Task Main(string[] args)
        {
            Console.WriteLine("=== Async/Await Demo ===\n");
            
            // 1. Basic async operations
            Console.WriteLine("1. Basic Async Operations:");
            await DemonstrateBasicAsync();
            
            // 2. Parallel async operations
            Console.WriteLine("\n2. Parallel Async Operations:");
            await DemonstrateParallelAsync();
            
            // 3. Exception handling
            Console.WriteLine("\n3. Exception Handling:");
            await DemonstrateExceptionHandling();
            
            // 4. Cancellation
            Console.WriteLine("\n4. Cancellation:");
            await DemonstrateCancellation();
            
            // 5. Async streams
            Console.WriteLine("\n5. Async Streams:");
            await DemonstrateAsyncStreams();
            
            // 6. ConfigureAwait
            Console.WriteLine("\n6. ConfigureAwait:");
            await DemonstrateConfigureAwait();
            
            // 7. ValueTask
            Console.WriteLine("\n7. ValueTask:");
            await DemonstrateValueTask();
            
            // 8. Real-world example
            Console.WriteLine("\n8. Real-World Example:");
            await DemonstrateRealWorldExample();
        }
        
        static async Task DemonstrateBasicAsync()
        {
            Console.WriteLine("Starting basic async operations...");
            
            // Simple async method
            await SimpleAsyncMethod();
            
            // Async method with return value
            string result = await AsyncMethodWithReturn();
            Console.WriteLine($"Result: {result}");
            
            // Sequential async operations
            await SequentialAsyncOperations();
        }
        
        static async Task SimpleAsyncMethod()
        {
            Console.WriteLine("  Starting simple async method...");
            await Task.Delay(1000);
            Console.WriteLine("  Simple async method completed!");
        }
        
        static async Task<string> AsyncMethodWithReturn()
        {
            Console.WriteLine("  Starting async method with return...");
            await Task.Delay(500);
            return "Async operation completed!";
        }
        
        static async Task SequentialAsyncOperations()
        {
            Console.WriteLine("  Sequential operations:");
            
            string step1 = await ProcessStepAsync("Step 1", 1000);
            Console.WriteLine($"    {step1}");
            
            string step2 = await ProcessStepAsync("Step 2", 500);
            Console.WriteLine($"    {step2}");
            
            string step3 = await ProcessStepAsync("Step 3", 300);
            Console.WriteLine($"    {step3}");
        }
        
        static async Task<string> ProcessStepAsync(string stepName, int delay)
        {
            await Task.Delay(delay);
            return $"{stepName} completed after {delay}ms";
        }
        
        static async Task DemonstrateParallelAsync()
        {
            Console.WriteLine("Starting parallel async operations...");
            
            // Concurrent operations with WhenAll
            await ParallelOperationsWithWhenAll();
            
            // First completion with WhenAny
            await FirstCompletionWithWhenAny();
            
            // Concurrent operations with results
            await ParallelOperationsWithResults();
        }
        
        static async Task ParallelOperationsWithWhenAll()
        {
            Console.WriteLine("  Parallel operations with WhenAll:");
            
            var tasks = new[]
            {
                ProcessFileAsync("file1.txt", 1000),
                ProcessFileAsync("file2.txt", 800),
                ProcessFileAsync("file3.txt", 1200)
            };
            
            await Task.WhenAll(tasks);
            Console.WriteLine("    All files processed concurrently!");
        }
        
        static async Task FirstCompletionWithWhenAny()
        {
            Console.WriteLine("  First completion with WhenAny:");
            
            var tasks = new[]
            {
                FetchFromServiceAsync("Service A", 2000),
                FetchFromServiceAsync("Service B", 1000),
                FetchFromServiceAsync("Service C", 3000)
            };
            
            Task<string> firstCompleted = await Task.WhenAny(tasks);
            string result = await firstCompleted;
            Console.WriteLine($"    First response: {result}");
        }
        
        static async Task ParallelOperationsWithResults()
        {
            Console.WriteLine("  Parallel operations with results:");
            
            var tasks = new[]
            {
                CalculateAsync(10, 5),
                CalculateAsync(20, 8),
                CalculateAsync(15, 3)
            };
            
            int[] results = await Task.WhenAll(tasks);
            Console.WriteLine($"    Results: {string.Join(", ", results)}");
            Console.WriteLine($"    Sum: {results.Sum()}");
        }
        
        static async Task ProcessFileAsync(string filename, int delay)
        {
            Console.WriteLine($"    Processing {filename}...");
            await Task.Delay(delay);
            Console.WriteLine($"    Completed {filename}");
        }
        
        static async Task<string> FetchFromServiceAsync(string serviceName, int delay)
        {
            await Task.Delay(delay);
            return $"Response from {serviceName}";
        }
        
        static async Task<int> CalculateAsync(int a, int b)
        {
            await Task.Delay(500);
            return a + b;
        }
        
        static async Task DemonstrateExceptionHandling()
        {
            Console.WriteLine("Demonstrating exception handling...");
            
            // Single exception
            await HandleSingleException();
            
            // Multiple exceptions with WhenAll
            await HandleMultipleExceptions();
        }
        
        static async Task HandleSingleException()
        {
            Console.WriteLine("  Single exception handling:");
            
            try
            {
                await RiskyOperationAsync();
            }
            catch (InvalidOperationException ex)
            {
                Console.WriteLine($"    Caught: {ex.Message}");
            }
        }
        
        static async Task HandleMultipleExceptions()
        {
            Console.WriteLine("  Multiple exception handling:");
            
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
                Console.WriteLine($"    First exception: {ex.Message}");
                
                // Check all tasks for exceptions
                foreach (var task in tasks)
                {
                    if (task.IsFaulted && task.Exception != null)
                    {
                        foreach (var innerEx in task.Exception.InnerExceptions)
                        {
                            Console.WriteLine($"    Task exception: {innerEx.Message}");
                        }
                    }
                }
            }
        }
        
        static async Task RiskyOperationAsync()
        {
            await Task.Delay(100);
            throw new InvalidOperationException("Something went wrong!");
        }
        
        static async Task DemonstrateCancellation()
        {
            Console.WriteLine("Demonstrating cancellation...");
            
            var cts = new CancellationTokenSource();
            
            // Start a long-running operation
            var operationTask = LongRunningOperationAsync(cts.Token);
            
            // Cancel after 2 seconds
            cts.CancelAfter(TimeSpan.FromSeconds(2));
            
            try
            {
                await operationTask;
            }
            catch (OperationCanceledException)
            {
                Console.WriteLine("  Operation was cancelled!");
            }
        }
        
        static async Task LongRunningOperationAsync(CancellationToken cancellationToken)
        {
            Console.WriteLine("  Starting long-running operation...");
            
            for (int i = 0; i < 10; i++)
            {
                cancellationToken.ThrowIfCancellationRequested();
                Console.WriteLine($"    Step {i + 1}/10");
                await Task.Delay(500, cancellationToken);
            }
            
            Console.WriteLine("  Operation completed!");
        }
        
        static async Task DemonstrateAsyncStreams()
        {
            Console.WriteLine("Demonstrating async streams...");
            
            // Consume async stream
            await ConsumeNumbersAsync();
            
            // With cancellation
            var cts = new CancellationTokenSource();
            cts.CancelAfter(TimeSpan.FromSeconds(3));
            
            try
            {
                await ConsumeNumbersWithCancellationAsync(cts.Token);
            }
            catch (OperationCanceledException)
            {
                Console.WriteLine("  Async stream consumption was cancelled!");
            }
        }
        
        static async Task ConsumeNumbersAsync()
        {
            Console.WriteLine("  Consuming numbers:");
            await foreach (int number in GenerateNumbersAsync())
            {
                Console.WriteLine($"    Received: {number}");
            }
        }
        
        static async Task ConsumeNumbersWithCancellationAsync(CancellationToken cancellationToken)
        {
            Console.WriteLine("  Consuming numbers with cancellation:");
            await foreach (int number in GenerateNumbersAsync().WithCancellation(cancellationToken))
            {
                Console.WriteLine($"    Received: {number}");
            }
        }
        
        static async IAsyncEnumerable<int> GenerateNumbersAsync()
        {
            for (int i = 0; i < 10; i++)
            {
                await Task.Delay(500);
                yield return i;
            }
        }
        
        static async Task DemonstrateConfigureAwait()
        {
            Console.WriteLine("Demonstrating ConfigureAwait...");
            
            // Without ConfigureAwait (captures context)
            string result1 = await OperationWithContext();
            Console.WriteLine($"  Without ConfigureAwait: {result1}");
            
            // With ConfigureAwait(false) (doesn't capture context)
            string result2 = await OperationWithoutContext();
            Console.WriteLine($"  With ConfigureAwait(false): {result2}");
        }
        
        static async Task<string> OperationWithContext()
        {
            await Task.Delay(100); // Captures context
            return "Completed with context";
        }
        
        static async Task<string> OperationWithoutContext()
        {
            await Task.Delay(100).ConfigureAwait(false); // Doesn't capture context
            return "Completed without context";
        }
        
        static async Task DemonstrateValueTask()
        {
            Console.WriteLine("Demonstrating ValueTask...");
            
            var cache = new DataCache();
            
            // First call - async completion
            string result1 = await cache.GetDataAsync(1);
            Console.WriteLine($"  First call: {result1}");
            
            // Second call - synchronous completion (from cache)
            string result2 = await cache.GetDataAsync(1);
            Console.WriteLine($"  Second call: {result2}");
            
            // Third call - async completion
            string result3 = await cache.GetDataAsync(2);
            Console.WriteLine($"  Third call: {result3}");
        }
        
        static async Task DemonstrateRealWorldExample()
        {
            Console.WriteLine("Real-world example: Data processing pipeline...");
            
            var processor = new DataProcessor();
            
            try
            {
                // Process data with timeout
                using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(5));
                
                ProcessingResult result = await processor.ProcessDataPipelineAsync(cts.Token);
                
                Console.WriteLine($"  Processing completed:");
                Console.WriteLine($"    Records processed: {result.RecordsProcessed}");
                Console.WriteLine($"    Success rate: {result.SuccessRate:P2}");
                Console.WriteLine($"    Duration: {result.Duration.TotalSeconds:F2} seconds");
            }
            catch (OperationCanceledException)
            {
                Console.WriteLine("  Processing timed out!");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"  Processing failed: {ex.Message}");
            }
        }
    }
    
    public class DataCache
    {
        private readonly Dictionary<int, string> _cache = new();
        
        public async ValueTask<string> GetDataAsync(int key)
        {
            if (_cache.TryGetValue(key, out string cachedValue))
            {
                return cachedValue; // Synchronous completion
            }
            
            // Simulate async data loading
            await Task.Delay(500);
            string data = $"Data for key {key}";
            _cache[key] = data;
            return data;
        }
    }
    
    public class DataProcessor
    {
        public async Task<ProcessingResult> ProcessDataPipelineAsync(CancellationToken cancellationToken)
        {
            var stopwatch = System.Diagnostics.Stopwatch.StartNew();
            
            // Step 1: Load data
            var data = await LoadDataAsync(cancellationToken);
            
            // Step 2: Process data in parallel
            var processedData = await ProcessDataInParallelAsync(data, cancellationToken);
            
            // Step 3: Save results
            await SaveResultsAsync(processedData, cancellationToken);
            
            stopwatch.Stop();
            
            return new ProcessingResult
            {
                RecordsProcessed = processedData.Count,
                SuccessRate = (double)processedData.Count(p => p.IsSuccess) / processedData.Count,
                Duration = stopwatch.Elapsed
            };
        }
        
        private async Task<List<DataRecord>> LoadDataAsync(CancellationToken cancellationToken)
        {
            Console.WriteLine("  Loading data...");
            await Task.Delay(1000, cancellationToken);
            
            return Enumerable.Range(1, 100)
                .Select(i => new DataRecord { Id = i, Value = $"Record {i}" })
                .ToList();
        }
        
        private async Task<List<ProcessedRecord>> ProcessDataInParallelAsync(List<DataRecord> data, CancellationToken cancellationToken)
        {
            Console.WriteLine("  Processing data...");
            
            var tasks = data.Select(record => ProcessRecordAsync(record, cancellationToken));
            var results = await Task.WhenAll(tasks);
            
            return results.ToList();
        }
        
        private async Task<ProcessedRecord> ProcessRecordAsync(DataRecord record, CancellationToken cancellationToken)
        {
            await Task.Delay(50, cancellationToken);
            
            // Simulate some processing logic
            bool isSuccess = record.Id % 10 != 0; // 90% success rate
            
            return new ProcessedRecord
            {
                Id = record.Id,
                OriginalValue = record.Value,
                ProcessedValue = record.Value.ToUpper(),
                IsSuccess = isSuccess,
                ProcessingTime = TimeSpan.FromMilliseconds(50)
            };
        }
        
        private async Task SaveResultsAsync(List<ProcessedRecord> processedData, CancellationToken cancellationToken)
        {
            Console.WriteLine("  Saving results...");
            await Task.Delay(500, cancellationToken);
            // Simulate saving to database
        }
    }
    
    public class DataRecord
    {
        public int Id { get; set; }
        public string Value { get; set; }
    }
    
    public class ProcessedRecord
    {
        public int Id { get; set; }
        public string OriginalValue { get; set; }
        public string ProcessedValue { get; set; }
        public bool IsSuccess { get; set; }
        public TimeSpan ProcessingTime { get; set; }
    }
    
    public class ProcessingResult
    {
        public int RecordsProcessed { get; set; }
        public double SuccessRate { get; set; }
        public TimeSpan Duration { get; set; }
    }
}
