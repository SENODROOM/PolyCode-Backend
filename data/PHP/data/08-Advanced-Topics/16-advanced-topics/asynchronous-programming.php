<?php
/**
 * Asynchronous Programming in PHP
 * 
 * This file demonstrates coroutines, generators, event-driven programming,
 * non-blocking I/O, and modern async patterns in PHP.
 */

// Basic Generator Example
class BasicGeneratorExample
{
    public function countUpTo(int $max): \Generator
    {
        for ($i = 1; $i <= $max; $i++) {
            yield $i;
        }
    }
    
    public function fibonacciSequence(int $limit): \Generator
    {
        $a = 0;
        $b = 1;
        
        for ($i = 0; $i < $limit; $i++) {
            yield $a;
            [$a, $b] = [$b, $a + $b];
        }
    }
    
    public function fileReader(string $filename): \Generator
    {
        $file = fopen($filename, 'r');
        
        if (!$file) {
            throw new \RuntimeException("Cannot open file: $filename");
        }
        
        while (($line = fgets($file)) !== false) {
            yield trim($line);
        }
        
        fclose($file);
    }
    
    public function demonstrateGenerators(): void
    {
        echo "Basic Generator Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "Count up to 5:\n";
        foreach ($this->countUpTo(5) as $number) {
            echo "  $number\n";
        }
        
        echo "\nFibonacci sequence (first 10):\n";
        foreach ($this->fibonacciSequence(10) as $fib) {
            echo "  $fib\n";
        }
        
        echo "\nReading file line by line:\n";
        $content = "Line 1\nLine 2\nLine 3";
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, $content);
        
        foreach ($this->fileReader($tempFile) as $line) {
            echo "  $line\n";
        }
        
        unlink($tempFile);
    }
}

// Advanced Generator with Yield From
class AdvancedGeneratorExample
{
    public function nestedGenerators(): \Generator
    {
        yield from $this->countUpTo(3);
        yield from $this->fibonacciSequence(3);
        yield from ['apple', 'banana', 'cherry'];
    }
    
    public function countUpTo(int $max): \Generator
    {
        for ($i = 1; $i <= $max; $i++) {
            yield $i;
        }
    }
    
    public function fibonacciSequence(int $limit): \Generator
    {
        $a = 0;
        $b = 1;
        
        for ($i = 0; $i < $limit; $i++) {
            yield $a;
            [$a, $b] = [$b, $a + $b];
        }
    }
    
    public function generatorDelegation(): \Generator
    {
        // Delegate to another generator
        yield from $this->nestedGenerators();
        
        // Continue with own logic
        yield "End of delegation";
    }
    
    public function demonstrateAdvancedGenerators(): void
    {
        echo "Advanced Generator Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Nested generators with yield from:\n";
        foreach ($this->nestedGenerators() as $value) {
            echo "  $value\n";
        }
        
        echo "\nGenerator delegation:\n";
        foreach ($this->generatorDelegation() as $value) {
            echo "  $value\n";
        }
    }
}

// Generator as Iterator
class GeneratorIteratorExample
{
    public function createRange(int $start, int $end, int $step = 1): \Generator
    {
        for ($i = $start; $i <= $end; $i += $step) {
            yield $i;
        }
    }
    
    public function filterEven(\Generator $numbers): \Generator
    {
        foreach ($numbers as $number) {
            if ($number % 2 === 0) {
                yield $number;
            }
        }
    }
    
    public function mapSquare(\Generator $numbers): \Generator
    {
        foreach ($numbers as $number) {
            yield $number * $number;
        }
    }
    
    public function demonstrateIteratorPattern(): void
    {
        echo "Generator Iterator Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Range 1-10:\n";
        $range = $this->createRange(1, 10);
        foreach ($range as $number) {
            echo "  $number\n";
        }
        
        echo "\nEven numbers from 1-10:\n";
        $evenNumbers = $this->filterEven($this->createRange(1, 10));
        foreach ($evenNumbers as $number) {
            echo "  $number\n";
        }
        
        echo "\nSquares of 1-5:\n";
        $squares = $this->mapSquare($this->createRange(1, 5));
        foreach ($squares as $square) {
            echo "  $square\n";
        }
    }
}

// Coroutine-Based Task Scheduler
class TaskScheduler
{
    private array $tasks = [];
    private array $running = [];
    private array $completed = [];
    
    public function addTask(string $name, \Generator $task): void
    {
        $this->tasks[$name] = $task;
    }
    
    public function run(): array
    {
        while (!empty($this->tasks) || !empty($this->running)) {
            $this->tick();
        }
        
        return $this->completed;
    }
    
    private function tick(): void
    {
        // Start new tasks
        foreach ($this->tasks as $name => $task) {
            if (!$task->valid()) {
                $this->completed[$name] = $task->getReturn();
                unset($this->tasks[$name]);
            } else {
                $this->running[$name] = $task;
                unset($this->tasks[$name]);
            }
        }
        
        // Run active tasks
        foreach ($this->running as $name => $task) {
            try {
                $task->next();
                
                if (!$task->valid()) {
                    $this->completed[$name] = $task->getReturn();
                    unset($this->running[$name]);
                }
            } catch (\Exception $e) {
                $this->completed[$name] = ['error' => $e->getMessage()];
                unset($this->running[$name]);
            }
        }
    }
    
    public function createAsyncTask(callable $callback, array $args = []): \Generator
    {
        // Simulate async operation
        yield 'pending';
        
        // Simulate work
        $result = call_user_func_array($callback, $args);
        
        yield 'completed';
        
        return $result;
    }
}

// Async HTTP Client Simulator
class AsyncHttpClient
{
    private TaskScheduler $scheduler;
    
    public function __construct()
    {
        $this->scheduler = new TaskScheduler();
    }
    
    public function get(string $url): \Generator
    {
        yield "Starting GET request to: $url";
        
        // Simulate network delay
        yield from $this->delay(1000);
        
        yield "Request completed";
        
        return ['status' => 200, 'body' => "Response from $url"];
    }
    
    public function post(string $url, array $data): \Generator
    {
        yield "Starting POST request to: $url";
        
        yield from $this->delay(1500);
        
        yield "Request completed";
        
        return ['status' => 201, 'body' => "Created resource at $url"];
    }
    
    public function batchRequests(array $urls): \Generator
    {
        $results = [];
        
        foreach ($urls as $url) {
            yield "Processing: $url";
            $results[$url] = yield from $this->get($url);
        }
        
        yield "All requests completed";
        
        return $results;
    }
    
    private function delay(int $milliseconds): \Generator
    {
        $start = microtime(true);
        $end = $start + ($milliseconds / 1000);
        
        while (microtime(true) < $end) {
            yield; // Yield control back to scheduler
        }
        
        return true;
    }
}

// Event Loop Simulator
class EventLoop
{
    private array $events = [];
    private array $timers = [];
    private bool $running = false;
    
    public function addEvent(string $name, callable $callback): void
    {
        $this->events[$name] = $callback;
    }
    
    public function addTimer(int $delay, callable $callback): string
    {
        $id = uniqid('timer_');
        $this->timers[$id] = [
            'callback' => $callback,
            'execute_at' => microtime(true) + ($delay / 1000),
            'interval' => $delay,
            'recurring' => false
        ];
        
        return $id;
    }
    
    public function addRecurringTimer(int $interval, callable $callback): string
    {
        $id = $this->addTimer($interval, $callback);
        $this->timers[$id]['recurring'] = true;
        
        return $id;
    }
    
    public function run(): void
    {
        $this->running = true;
        
        while ($this->running) {
            $this->tick();
            usleep(1000); // 1ms tick
        }
    }
    
    public function stop(): void
    {
        $this->running = false;
    }
    
    private function tick(): void
    {
        $now = microtime(true);
        
        // Execute timers
        foreach ($this->timers as $id => $timer) {
            if ($now >= $timer['execute_at']) {
                $timer['callback']();
                
                if ($timer['recurring']) {
                    $timer['execute_at'] = $now + ($timer['interval'] / 1000);
                } else {
                    unset($this->timers[$id]);
                }
            }
        }
    }
}

// Promise Implementation
class Promise
{
    private $state = 'pending';
    private $value = null;
    private $reason = null;
    private array $onFulfilled = [];
    private array $onRejected = [];
    
    public function __construct(callable $executor = null)
    {
        if ($executor) {
            $executor(
                fn($value) => $this->resolve($value),
                fn($reason) => $this->reject($reason)
            );
        }
    }
    
    public function then(callable $onFulfilled = null, callable $onRejected = null): self
    {
        $promise = new self();
        
        if ($this->state === 'fulfilled' && $onFulfilled) {
            $promise->resolve($onFulfilled($this->value));
        } elseif ($this->state === 'rejected' && $onRejected) {
            $promise->reject($onRejected($this->reason));
        } else {
            if ($onFulfilled) {
                $this->onFulfilled[] = $onFulfilled;
            }
            if ($onRejected) {
                $this->onRejected[] = $onRejected;
            }
            
            $this->onFulfilled[] = fn($value) => $promise->resolve($value);
            $this->onRejected[] = fn($reason) => $promise->reject($reason);
        }
        
        return $promise;
    }
    
    public function catch(callable $onRejected): self
    {
        return $this->then(null, $onRejected);
    }
    
    public function finally(callable $onFinally): self
    {
        $promise = new self();
        
        $handler = fn($value) => {
            $onFinally();
            return $value;
        };
        
        if ($this->state === 'fulfilled') {
            $promise->resolve($handler($this->value));
        } elseif ($this->state === 'rejected') {
            $onFinally();
            $promise->reject($this->reason);
        } else {
            $this->onFulfilled[] = $handler;
            $this->onRejected[] = $onFinally;
            $this->onFulfilled[] = fn($value) => $promise->resolve($value);
            $this->onRejected[] = fn($reason) => $promise->reject($reason);
        }
        
        return $promise;
    }
    
    public function resolve($value): void
    {
        if ($this->state !== 'pending') {
            return;
        }
        
        $this->state = 'fulfilled';
        $this->value = $value;
        
        foreach ($this->onFulfilled as $callback) {
            $callback($value);
        }
    }
    
    public function reject($reason): void
    {
        if ($this->state !== 'pending') {
            return;
        }
        
        $this->state = 'rejected';
        $this->reason = $reason;
        
        foreach ($this->onRejected as $callback) {
            $callback($reason);
        }
    }
    
    public function getState(): string
    {
        return $this->state;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function getReason()
    {
        return $this->reason;
    }
    
    public static function resolve($value): self
    {
        $promise = new self();
        $promise->resolve($value);
        return $promise;
    }
    
    public static function reject($reason): self
    {
        $promise = new self();
        $promise->reject($reason);
        return $promise;
    }
    
    public static function all(array $promises): self
    {
        $promise = new self();
        $results = [];
        $completed = 0;
        $total = count($promises);
        
        if ($total === 0) {
            $promise->resolve($results);
            return $promise;
        }
        
        foreach ($promises as $index => $p) {
            $p->then(
                function($value) use (&$results, &$completed, $total, $promise, $index) {
                    $results[$index] = $value;
                    $completed++;
                    
                    if ($completed === $total) {
                        ksort($results);
                        $promise->resolve(array_values($results));
                    }
                },
                function($reason) use ($promise) {
                    $promise->reject($reason);
                }
            );
        }
        
        return $promise;
    }
}

// Async/Await Implementation
class AsyncAwait
{
    private static array $awaiting = [];
    
    public static function async(callable $callback): Promise
    {
        $promise = new Promise();
        
        // Simulate async execution
        setTimeout(function() use ($callback, $promise) {
            try {
                $result = $callback();
                $promise->resolve($result);
            } catch (\Exception $e) {
                $promise->reject($e);
            }
        }, 0);
        
        return $promise;
    }
    
    public static function await(Promise $promise)
    {
        if ($promise->getState() === 'pending') {
            self::$awaiting[] = $promise;
            
            // In a real implementation, this would yield control
            // For demonstration, we'll simulate the wait
            while ($promise->getState() === 'pending') {
                usleep(1000); // 1ms
            }
        }
        
        if ($promise->getState() === 'fulfilled') {
            return $promise->getValue();
        } else {
            throw new \RuntimeException($promise->getReason());
        }
    }
    
    public static function run(callable $generator)
    {
        $promise = new Promise();
        
        try {
            $gen = $generator();
            
            $next = function($value = null) use ($gen, &$next, $promise) {
                try {
                    $result = $gen->send($value);
                    
                    if ($result instanceof Promise) {
                        $result->then(
                            function($value) use ($next) {
                                $next($value);
                            },
                            function($reason) use ($promise) {
                                $promise->reject($reason);
                            }
                        );
                    } elseif ($gen->valid()) {
                        $next($result);
                    } else {
                        $promise->resolve($gen->getReturn());
                    }
                } catch (\Exception $e) {
                    $promise->reject($e);
                }
            };
            
            $next();
        } catch (\Exception $e) {
            $promise->reject($e);
        }
        
        return $promise;
    }
}

// Reactive Programming Implementation
class Observable
{
    private array $observers = [];
    
    public function subscribe(callable $observer): string
    {
        $id = uniqid('observer_');
        $this->observers[$id] = $observer;
        return $id;
    }
    
    public function unsubscribe(string $id): void
    {
        unset($this->observers[$id]);
    }
    
    public function next($value): void
    {
        foreach ($this->observers as $observer) {
            $observer($value);
        }
    }
    
    public function error(\Throwable $error): void
    {
        foreach ($this->observers as $observer) {
            $observer($error);
        }
    }
    
    public function complete(): void
    {
        foreach ($this->observers as $observer) {
            $observer(null);
        }
    }
    
    public function map(callable $transform): Observable
    {
        $observable = new self();
        
        $this->subscribe(function($value) use ($transform, $observable) {
            if ($value instanceof \Throwable) {
                $observable->error($value);
            } elseif ($value === null) {
                $observable->complete();
            } else {
                $observable->next($transform($value));
            }
        });
        
        return $observable;
    }
    
    public function filter(callable $predicate): Observable
    {
        $observable = new self();
        
        $this->subscribe(function($value) use ($predicate, $observable) {
            if ($value instanceof \Throwable) {
                $observable->error($value);
            } elseif ($value === null) {
                $observable->complete();
            } elseif ($predicate($value)) {
                $observable->next($value);
            }
        });
        
        return $observable;
    }
    
    public static function fromArray(array $values): self
    {
        $observable = new self();
        
        setTimeout(function() use ($values, $observable) {
            foreach ($values as $value) {
                $observable->next($value);
            }
            $observable->complete();
        }, 0);
        
        return $observable;
    }
    
    public static function interval(int $milliseconds, callable $generator = null): self
    {
        $observable = new self();
        $counter = 0;
        
        $timer = setInterval(function() use ($milliseconds, $generator, &$counter, $observable) {
            $value = $generator ? $generator($counter) : $counter;
            $observable->next($value);
            $counter++;
        }, $milliseconds);
        
        return $observable;
    }
}

// Non-blocking I/O Simulator
class NonBlockingIO
{
    private array $operations = [];
    private EventLoop $loop;
    
    public function __construct()
    {
        $this->loop = new EventLoop();
    }
    
    public function readFile(string $filename, callable $callback): void
    {
        $operationId = uniqid('read_');
        
        $this->operations[$operationId] = [
            'type' => 'read',
            'filename' => $filename,
            'callback' => $callback,
            'start_time' => microtime(true)
        ];
        
        // Simulate non-blocking read
        $this->loop->addTimer(50, function() use ($operationId) {
            $this->completeOperation($operationId, file_get_contents($this->operations[$operationId]['filename']));
        });
    }
    
    public function writeFile(string $filename, string $content, callable $callback): void
    {
        $operationId = uniqid('write_');
        
        $this->operations[$operationId] = [
            'type' => 'write',
            'filename' => $filename,
            'content' => $content,
            'callback' => $callback,
            'start_time' => microtime(true)
        ];
        
        // Simulate non-blocking write
        $this->loop->addTimer(100, function() use ($operationId) {
            $result = file_put_contents($this->operations[$operationId]['filename'], $this->operations[$operationId]['content']);
            $this->completeOperation($operationId, $result);
        });
    }
    
    public function httpRequest(string $url, callable $callback): void
    {
        $operationId = uniqid('http_');
        
        $this->operations[$operationId] = [
            'type' => 'http',
            'url' => $url,
            'callback' => $callback,
            'start_time' => microtime(true)
        ];
        
        // Simulate HTTP request
        $this->loop->addTimer(200, function() use ($operationId) {
            $this->completeOperation($operationId, ['status' => 200, 'body' => "Response from {$this->operations[$operationId]['url']}"]);
        });
    }
    
    private function completeOperation(string $operationId, $result): void
    {
        if (isset($this->operations[$operationId])) {
            $operation = $this->operations[$operationId];
            $duration = (microtime(true) - $operation['start_time']) * 1000;
            
            echo "Operation {$operation['type']} completed in " . round($duration, 2) . "ms\n";
            
            $operation['callback']($result);
            
            unset($this->operations[$operationId]);
        }
    }
    
    public function run(): void
    {
        $this->loop->run();
    }
    
    public function stop(): void
    {
        $this->loop->stop();
    }
}

// Asynchronous Programming Examples
class AsynchronousProgrammingExamples
{
    private BasicGeneratorExample $basicGenerator;
    private AdvancedGeneratorExample $advancedGenerator;
    private GeneratorIteratorExample $generatorIterator;
    private TaskScheduler $scheduler;
    private AsyncHttpClient $httpClient;
    private EventLoop $eventLoop;
    private NonBlockingIO $nonBlockingIO;
    
    public function __construct()
    {
        $this->basicGenerator = new BasicGeneratorExample();
        $this->advancedGenerator = new AdvancedGeneratorExample();
        $this->generatorIterator = new GeneratorIteratorExample();
        $this->scheduler = new TaskScheduler();
        $this->httpClient = new AsyncHttpClient();
        $this->eventLoop = new EventLoop();
        $this->nonBlockingIO = new NonBlockingIO();
    }
    
    public function demonstrateGenerators(): void
    {
        $this->basicGenerator->demonstrateGenerators();
        $this->advancedGenerator->demonstrateAdvancedGenerators();
        $this->generatorIterator->demonstrateIteratorPattern();
    }
    
    public function demonstrateTaskScheduler(): void
    {
        echo "\nTask Scheduler Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Add tasks
        $this->scheduler->addTask('task1', $this->createTask('Task 1', 3));
        $this->scheduler->addTask('task2', $this->createTask('Task 2', 2));
        $this->scheduler->addTask('task3', $this->createTask('Task 3', 1));
        
        // Run scheduler
        $results = $this->scheduler->run();
        
        echo "Task Results:\n";
        foreach ($results as $name => $result) {
            echo "  $name: " . json_encode($result) . "\n";
        }
    }
    
    private function createTask(string $name, int $steps): \Generator
    {
        yield "$name started";
        
        for ($i = 1; $i <= $steps; $i++) {
            yield "$name step $i";
            // Simulate work
            usleep(100000); // 0.1 seconds
        }
        
        yield "$name completed";
        
        return "$name result";
    }
    
    public function demonstrateAsyncHttpClient(): void
    {
        echo "\nAsync HTTP Client Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Single request
        echo "Single request:\n";
        $response = $this->httpClient->get('https://api.example.com/users');
        foreach ($response as $message) {
            echo "  $message\n";
        }
        echo "Result: " . json_encode($response->getReturn()) . "\n";
        
        // Batch requests
        echo "\nBatch requests:\n";
        $urls = [
            'https://api.example.com/users',
            'https://api.example.com/posts',
            'https://api.example.com/comments'
        ];
        
        $batchResponse = $this->httpClient->batchRequests($urls);
        foreach ($batchResponse as $message) {
            echo "  $message\n";
        }
        echo "Results: " . json_encode($batchResponse->getReturn()) . "\n";
    }
    
    public function demonstrateEventLoop(): void
    {
        echo "\nEvent Loop Example\n";
        echo str_repeat("-", 20) . "\n";
        
        $counter = 0;
        
        // Add events
        $this->eventLoop->addEvent('tick', function() use (&$counter) {
            $counter++;
            echo "Tick $counter\n";
            
            if ($counter >= 5) {
                $this->eventLoop->stop();
            }
        });
        
        // Add timer
        $this->eventLoop->addTimer(1000, function() {
            echo "Timer executed after 1 second\n";
        });
        
        // Add recurring timer
        $this->eventLoop->addRecurringTimer(500, function() {
            echo "Recurring timer every 500ms\n";
        });
        
        echo "Starting event loop (will stop after 5 ticks)...\n";
        $this->eventLoop->run();
    }
    
    public function demonstratePromises(): void
    {
        echo "\nPromise Example\n";
        echo str_repeat("-", 18) . "\n";
        
        // Create and resolve promise
        $promise1 = new Promise(function($resolve, $reject) {
            setTimeout(function() use ($resolve) {
                $resolve('Promise 1 resolved');
            }, 100);
        });
        
        // Create and reject promise
        $promise2 = new Promise(function($resolve, $reject) {
            setTimeout(function() use ($reject) {
                $reject('Promise 2 rejected');
            }, 150);
        });
        
        // Chain promises
        $promise1
            ->then(function($value) {
                echo "Then 1: $value\n";
                return "Transformed: $value";
            })
            ->then(function($value) {
                echo "Then 2: $value\n";
                return $value;
            })
            ->catch(function($reason) {
                echo "Catch: $reason\n";
            })
            ->finally(function() {
                echo "Finally 1\n";
            });
        
        $promise2
            ->then(function($value) {
                echo "Then 3: $value\n";
            })
            ->catch(function($reason) {
                echo "Catch 2: $reason\n";
            })
            ->finally(function() {
                echo "Finally 2\n";
            });
        
        // Promise all
        $promise3 = Promise::all([
            Promise::resolve('Result 1'),
            Promise::resolve('Result 2'),
            Promise::resolve('Result 3')
        ]);
        
        $promise3->then(function($results) {
            echo "Promise All: " . json_encode($results) . "\n";
        });
    }
    
    public function demonstrateAsyncAwait(): void
    {
        echo "\nAsync/Await Example\n";
        echo str_repeat("-", 22) . "\n";
        
        // Async function simulation
        $asyncFunction = function() {
            yield "Starting async operation";
            
            yield from $this->delay(100);
            
            yield "Async operation completed";
            
            return "Async result";
        };
        
        // Run async function
        $promise = AsyncAwait::run($asyncFunction);
        
        $promise->then(function($result) {
            echo "Async/Await result: $result\n";
        });
    }
    
    public function demonstrateReactiveProgramming(): void
    {
        echo "\nReactive Programming Example\n";
        echo str_repeat("-", 32) . "\n";
        
        // Create observable from array
        $observable = Observable::fromArray([1, 2, 3, 4, 5]);
        
        // Subscribe and transform
        $observable
            ->map(function($value) {
                return $value * 2;
            })
            ->filter(function($value) {
                return $value > 4;
            })
            ->subscribe(function($value) {
                echo "Observable value: $value\n";
            });
        
        // Create interval observable
        $interval = Observable::interval(500, function($counter) {
            return "Interval: $counter";
        });
        
        $counter = 0;
        $interval->subscribe(function($value) use (&$counter) {
            echo "$value\n";
            $counter++;
            
            if ($counter >= 3) {
                echo "Stopping interval observable\n";
                // In a real implementation, we'd unsubscribe here
            }
        });
    }
    
    public function demonstrateNonBlockingIO(): void
    {
        echo "\nNon-blocking I/O Example\n";
        echo str_repeat("-", 28) . "\n";
        
        // Create temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'async_test');
        file_put_contents($tempFile, 'Hello, Async World!');
        
        // Non-blocking operations
        $this->nonBlockingIO->readFile($tempFile, function($content) {
            echo "File read: $content\n";
        });
        
        $this->nonBlockingIO->writeFile($tempFile, 'New content', function($bytes) {
            echo "File written: $bytes bytes\n";
        });
        
        $this->nonBlockingIO->httpRequest('https://api.example.com', function($response) {
            echo "HTTP response: " . json_encode($response) . "\n";
        });
        
        echo "Starting non-blocking I/O operations...\n";
        
        // Run for a short time to complete operations
        $this->eventLoop->addTimer(500, function() {
            $this->nonBlockingIO->stop();
        });
        
        $this->nonBlockingIO->run();
        
        // Clean up
        unlink($tempFile);
    }
    
    private function delay(int $milliseconds): \Generator
    {
        $start = microtime(true);
        $end = $start + ($milliseconds / 1000);
        
        while (microtime(true) < $end) {
            yield;
        }
        
        return true;
    }
    
    public function runAllExamples(): void
    {
        echo "Asynchronous Programming Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateGenerators();
        $this->demonstrateTaskScheduler();
        $this->demonstrateAsyncHttpClient();
        $this->demonstrateEventLoop();
        $this->demonstratePromises();
        $this->demonstrateAsyncAwait();
        $this->demonstrateReactiveProgramming();
        $this->demonstrateNonBlockingIO();
    }
}

// Helper functions for simulation
function setTimeout(callable $callback, int $milliseconds): void
{
    // In a real implementation, this would use proper async mechanisms
    // For demonstration, we'll simulate with usleep
    usleep($milliseconds * 1000);
    $callback();
}

function setInterval(callable $callback, int $milliseconds): string
{
    // In a real implementation, this would create a recurring timer
    // For demonstration, we'll just call it once
    $callback();
    return 'timer_' . uniqid();
}

// Asynchronous Programming Best Practices
function printAsynchronousProgrammingBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Asynchronous Programming Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Generators:\n";
    echo "   • Use for memory-efficient iteration\n";
    echo "   • Implement lazy evaluation\n";
    echo "   • Create custom iterators\n";
    echo "   • Use yield from for delegation\n";
    echo "   • Handle exceptions properly\n\n";
    
    echo "2. Coroutines:\n";
    echo "   • Use for cooperative multitasking\n";
    echo "   • Implement async patterns\n";
    echo "   • Handle I/O operations\n";
    echo "   • Use proper error handling\n";
    echo "   • Avoid blocking operations\n\n";
    
    echo "3. Event Loops:\n";
    echo "   • Use for non-blocking operations\n";
    echo "   • Implement proper timers\n";
    echo "   • Handle events efficiently\n";
    echo "   • Avoid busy waiting\n";
    echo "   • Use proper cleanup\n\n";
    
    echo "4. Promises:\n";
    echo "   • Use for async operations\n";
    echo "   • Chain promises properly\n";
    echo "   • Handle errors consistently\n";
    echo "   • Use finally for cleanup\n";
    echo "   • Implement proper states\n\n";
    
    echo "5. Reactive Programming:\n";
    echo "   • Use for event-driven systems\n";
    echo "   • Transform streams properly\n";
    echo "   • Filter events efficiently\n";
    echo "   • Handle backpressure\n";
    echo "   • Use proper subscription\n\n";
    
    echo "6. Non-blocking I/O:\n";
    echo "   • Use for high-performance apps\n";
    echo "   • Implement proper callbacks\n";
    echo "   • Handle errors gracefully\n";
    echo "   • Use connection pooling\n";
    echo "   • Monitor resource usage\n\n";
    
    echo "7. Performance:\n";
    echo "   • Profile async operations\n";
    echo "   • Optimize generator usage\n";
    echo "   • Use proper memory management\n";
    echo "   • Avoid excessive yielding\n";
    echo "   • Monitor system resources\n\n";
    
    echo "8. Error Handling:\n";
    echo "   • Use try-catch in generators\n";
    echo "   • Propagate errors properly\n";
    echo "   • Implement retry logic\n";
    echo "   • Use proper logging\n";
    echo "   • Handle edge cases";
}

// Main execution
function runAsynchronousProgrammingDemo(): void
{
    $examples = new AsynchronousProgrammingExamples();
    $examples->runAllExamples();
    printAsynchronousProgrammingBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAsynchronousProgrammingDemo();
}
?>
