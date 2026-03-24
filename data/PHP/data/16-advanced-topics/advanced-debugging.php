<?php
/**
 * Advanced Debugging and Profiling
 * 
 * This file demonstrates advanced debugging techniques,
 * performance profiling, memory analysis, and production debugging.
 */

// Advanced Debugger Class
class AdvancedDebugger
{
    private array $breakpoints = [];
    private array $callStack = [];
    private array $variables = [];
    private bool $debugMode = false;
    private array $log = [];
    
    public function enableDebug(): void
    {
        $this->debugMode = true;
        echo "Debug mode enabled\n";
    }
    
    public function disableDebug(): void
    {
        $this->debugMode = false;
        echo "Debug mode disabled\n";
    }
    
    public function addBreakpoint(string $file, int $line, callable $condition = null): void
    {
        $this->breakpoints[] = [
            'file' => $file,
            'line' => $line,
            'condition' => $condition,
            'hit_count' => 0
        ];
    }
    
    public function logVariable(string $name, $value): void
    {
        $this->variables[$name] = [
            'value' => $value,
            'type' => gettype($value),
            'timestamp' => microtime(true)
        ];
        
        if ($this->debugMode) {
            echo "Variable '$name': " . json_encode($value) . " (" . gettype($value) . ")\n";
        }
    }
    
    public function logMessage(string $message, string $level = 'INFO'): void
    {
        $this->log[] = [
            'message' => $message,
            'level' => $level,
            'timestamp' => microtime(true),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
        ];
        
        if ($this->debugMode) {
            echo "[$level] $message\n";
        }
    }
    
    public function enterFunction(string $function, array $args = []): void
    {
        $this->callStack[] = [
            'function' => $function,
            'args' => $args,
            'timestamp' => microtime(true),
            'memory' => memory_get_usage()
        ];
        
        if ($this->debugMode) {
            echo "Entering: $function(" . implode(', ', array_map('json_encode', $args)) . ")\n";
        }
    }
    
    public function exitFunction(string $function, $result = null): void
    {
        if (!empty($this->callStack)) {
            $call = array_pop($this->callStack);
            
            if ($this->debugMode) {
                $duration = (microtime(true) - $call['timestamp']) * 1000;
                $memory = memory_get_usage() - $call['memory'];
                echo "Exiting: $function (Duration: " . round($duration, 2) . "ms, Memory: " . round($memory / 1024, 2) . "KB)\n";
                if ($result !== null) {
                    echo "Result: " . json_encode($result) . "\n";
                }
            }
        }
    }
    
    public function checkBreakpoints(string $file, int $line): bool
    {
        if (!$this->debugMode) {
            return false;
        }
        
        foreach ($this->breakpoints as &$breakpoint) {
            if ($breakpoint['file'] === $file && $breakpoint['line'] === $line) {
                $breakpoint['hit_count']++;
                
                // Check condition
                if ($breakpoint['condition']) {
                    $condition = $breakpoint['condition'];
                    if (!$condition()) {
                        continue;
                    }
                }
                
                echo "Breakpoint hit at $file:$line (Hit count: {$breakpoint['hit_count']})\n";
                return true;
            }
        }
        
        return false;
    }
    
    public function getCallStack(): array
    {
        return $this->callStack;
    }
    
    public function getVariables(): array
    {
        return $this->variables;
    }
    
    public function getLog(): array
    {
        return $this->log;
    }
    
    public function dumpCallStack(): void
    {
        echo "\nCall Stack:\n";
        echo str_repeat("-", 12) . "\n";
        
        foreach (array_reverse($this->callStack) as $index => $call) {
            echo "#$index {$call['function']}(";
            echo implode(', ', array_map(function($arg) {
                return json_encode($arg);
            }, $call['args']));
            echo ")\n";
        }
    }
    
    public function dumpVariables(): void
    {
        echo "\nVariables:\n";
        echo str_repeat("-", 10) . "\n";
        
        foreach ($this->variables as $name => $data) {
            echo "$name: " . json_encode($data['value']) . " ({$data['type']})\n";
        }
    }
    
    public function dumpLog(): void
    {
        echo "\nDebug Log:\n";
        echo str_repeat("-", 11) . "\n";
        
        foreach ($this->log as $entry) {
            $timestamp = date('H:i:s', (int)$entry['timestamp']);
            echo "[$timestamp] [{$entry['level']}] {$entry['message']}\n";
        }
    }
}

// Performance Profiler
class PerformanceProfiler
{
    private array $profiles = [];
    private array $currentProfile = null;
    private array $memorySnapshots = [];
    
    public function startProfile(string $name): void
    {
        $this->currentProfile = [
            'name' => $name,
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(),
            'peak_memory' => memory_get_peak_usage(),
            'calls' => [],
            'memory_snapshots' => []
        ];
        
        // Take initial memory snapshot
        $this->takeMemorySnapshot('profile_start');
    }
    
    public function endProfile(): array
    {
        if ($this->currentProfile === null) {
            return [];
        }
        
        $this->currentProfile['end_time'] = microtime(true);
        $this->currentProfile['end_memory'] = memory_get_usage();
        $this->currentProfile['peak_memory'] = memory_get_peak_usage();
        $this->currentProfile['duration'] = $this->currentProfile['end_time'] - $this->currentProfile['start_time'];
        $this->currentProfile['memory_used'] = $this->currentProfile['end_memory'] - $this->currentProfile['start_memory'];
        
        // Take final memory snapshot
        $this->takeMemorySnapshot('profile_end');
        
        $profile = $this->currentProfile;
        $this->profiles[] = $profile;
        $this->currentProfile = null;
        
        return $profile;
    }
    
    public function recordFunctionCall(string $function, array $args = [], float $duration = null): void
    {
        if ($this->currentProfile === null) {
            return;
        }
        
        $this->currentProfile['calls'][] = [
            'function' => $function,
            'args' => $args,
            'duration' => $duration,
            'timestamp' => microtime(true),
            'memory_before' => memory_get_usage(),
            'memory_after' => memory_get_usage()
        ];
    }
    
    public function profileFunction(callable $function, array $args = []): mixed
    {
        $start = microtime(true);
        $memoryBefore = memory_get_usage();
        
        $result = call_user_func_array($function, $args);
        
        $end = microtime(true);
        $memoryAfter = memory_get_usage();
        
        $this->recordFunctionCall(
            $this->getCallableName($function),
            $args,
            ($end - $start) * 1000
        );
        
        return $result;
    }
    
    public function takeMemorySnapshot(string $label): void
    {
        $snapshot = [
            'label' => $label,
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage()
        ];
        
        $this->memorySnapshots[] = $snapshot;
        
        if ($this->currentProfile !== null) {
            $this->currentProfile['memory_snapshots'][] = $snapshot;
        }
    }
    
    public function getProfiles(): array
    {
        return $this->profiles;
    }
    
    public function getMemorySnapshots(): array
    {
        return $this->memorySnapshots;
    }
    
    public function generateReport(): string
    {
        $report = "Performance Profile Report\n";
        $report .= str_repeat("=", 30) . "\n\n";
        
        foreach ($this->profiles as $profile) {
            $report .= "Profile: {$profile['name']}\n";
            $report .= str_repeat("-", 20) . "\n";
            $report .= "Duration: " . round($profile['duration'] * 1000, 2) . "ms\n";
            $report .= "Memory Used: " . round($profile['memory_used'] / 1024, 2) . "KB\n";
            $report .= "Peak Memory: " . round($profile['peak_memory'] / 1024, 2) . "KB\n";
            $report .= "Function Calls: " . count($profile['calls']) . "\n\n";
            
            if (!empty($profile['calls'])) {
                $report .= "Function Calls:\n";
                foreach ($profile['calls'] as $call) {
                    $report .= "  {$call['function']}() - " . round($call['duration'], 2) . "ms\n";
                }
                $report .= "\n";
            }
        }
        
        return $report;
    }
    
    private function getCallableName(callable $callable): string
    {
        if (is_array($callable)) {
            if (is_object($callable[0])) {
                return get_class($callable[0]) . '::' . $callable[1];
            } else {
                return $callable[0] . '::' . $callable[1];
            }
        } elseif (is_string($callable)) {
            return $callable;
        } elseif (is_object($callable)) {
            return get_class($callable) . '::__invoke';
        } else {
            return 'anonymous';
        }
    }
}

// Memory Leak Detector
class MemoryLeakDetector
{
    private array $snapshots = [];
    private array $objects = [];
    private array $leaks = [];
    
    public function takeSnapshot(string $label): void
    {
        $this->snapshots[$label] = [
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage(),
            'objects' => $this->getObjectsInMemory()
        ];
    }
    
    public function trackObject(object $object): string
    {
        $id = spl_object_hash($object);
        $this->objects[$id] = [
            'class' => get_class($object),
            'created_at' => microtime(true),
            'references' => $this->getObjectReferences($object)
        ];
        
        return $id;
    }
    
    public function releaseObject(object $object): void
    {
        $id = spl_object_hash($object);
        if (isset($this->objects[$id])) {
            $this->objects[$id]['released_at'] = microtime(true);
            $this->objects[$id]['released'] = true;
        }
    }
    
    public function detectLeaks(): array
    {
        $this->leaks = [];
        
        foreach ($this->objects as $id => $object) {
            if (!isset($object['released']) || !$object['released']) {
                // Check if object has been around too long
                $age = microtime(true) - $object['created_at'];
                
                if ($age > 60) { // 60 seconds threshold
                    $this->leaks[$id] = [
                        'class' => $object['class'],
                        'age' => $age,
                        'references' => $object['references']
                    ];
                }
            }
        }
        
        return $this->leaks;
    }
    
    public function getMemoryGrowth(): array
    {
        $growth = [];
        
        $labels = array_keys($this->snapshots);
        
        for ($i = 1; $i < count($labels); $i++) {
            $prev = $this->snapshots[$labels[$i - 1]];
            $curr = $this->snapshots[$labels[$i]];
            
            $growth[$labels[$i]] = [
                'memory_growth' => $curr['memory_usage'] - $prev['memory_usage'],
                'peak_growth' => $curr['memory_peak'] - $prev['memory_peak'],
                'objects_added' => count($curr['objects']) - count($prev['objects'])
            ];
        }
        
        return $growth;
    }
    
    public function generateLeakReport(): string
    {
        $leaks = $this->detectLeaks();
        $growth = $this->getMemoryGrowth();
        
        $report = "Memory Leak Detection Report\n";
        $report .= str_repeat("=", 30) . "\n\n";
        
        if (!empty($leaks)) {
            $report .= "Potential Memory Leaks:\n";
            $report .= str_repeat("-", 25) . "\n";
            
            foreach ($leaks as $id => $leak) {
                $report .= "Object ID: $id\n";
                $report .= "Class: {$leak['class']}\n";
                $report .= "Age: " . round($leak['age'], 2) . " seconds\n";
                $report .= "References: " . count($leak['references']) . "\n\n";
            }
        } else {
            $report .= "No memory leaks detected.\n\n";
        }
        
        if (!empty($growth)) {
            $report .= "Memory Growth:\n";
            $report .= str_repeat("-", 15) . "\n";
            
            foreach ($growth as $snapshot => $data) {
                $report .= "$snapshot:\n";
                $report .= "  Memory Growth: " . round($data['memory_growth'] / 1024, 2) . "KB\n";
                $report .= "  Peak Growth: " . round($data['peak_growth'] / 1024, 2) . "KB\n";
                $report .= "  Objects Added: {$data['objects_added']}\n\n";
            }
        }
        
        return $report;
    }
    
    private function getObjectsInMemory(): array
    {
        $objects = [];
        
        // This is a simplified version - in reality, you'd use more sophisticated methods
        $declaredClasses = get_declared_classes();
        
        foreach ($declaredClasses as $class) {
            if (method_exists($class, 'getInstances')) {
                $instances = $class::getInstances();
                foreach ($instances as $instance) {
                    $objects[spl_object_hash($instance)] = get_class($instance);
                }
            }
        }
        
        return $objects;
    }
    
    private function getObjectReferences(object $object): array
    {
        // Simplified reference counting
        $references = [];
        
        // Check if object is referenced by global variables
        foreach ($GLOBALS as $key => $value) {
            if ($value === $object) {
                $references[] = "\$$key";
            }
        }
        
        return $references;
    }
}

// Production Debugger
class ProductionDebugger
{
    private array $config;
    private array $logs = [];
    private string $logFile;
    private bool $enabled;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'enabled' => false,
            'log_level' => 'ERROR',
            'log_file' => 'debug.log',
            'max_log_size' => 10485760, // 10MB
            'max_log_files' => 5,
            'include_trace' => false,
            'include_memory' => false
        ], $config);
        
        $this->enabled = $this->config['enabled'];
        $this->logFile = $this->config['log_file'];
    }
    
    public function enable(): void
    {
        $this->enabled = true;
        $this->log('DEBUG', 'Production debugger enabled');
    }
    
    public function disable(): void
    {
        $this->log('DEBUG', 'Production debugger disabled');
        $this->enabled = false;
    }
    
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }
        
        // Check log level
        if (!$this->shouldLog($level)) {
            return;
        }
        
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        // Add additional info if enabled
        if ($this->config['include_trace']) {
            $entry['trace'] = $this->getTrace();
        }
        
        if ($this->config['include_memory']) {
            $entry['memory'] = [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true)
            ];
        }
        
        $this->logs[] = $entry;
        
        // Write to file
        $this->writeToFile($entry);
        
        // Rotate logs if needed
        $this->rotateLogs();
    }
    
    public function logException(\Throwable $exception, array $context = []): void
    {
        $this->log('ERROR', $exception->getMessage(), array_merge([
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ], $context));
    }
    
    public function logPerformance(string $operation, float $duration, array $metrics = []): void
    {
        $this->log('PERF', "$operation took {$duration}ms", array_merge([
            'duration_ms' => $duration,
            'memory_before' => memory_get_usage(),
            'memory_after' => memory_get_usage()
        ], $metrics));
    }
    
    public function logRequest(array $request, array $response, float $duration): void
    {
        $this->log('REQUEST', 'Request processed', [
            'method' => $request['method'] ?? 'GET',
            'url' => $request['url'] ?? '',
            'status' => $response['status'] ?? 200,
            'duration' => $duration,
            'memory' => memory_get_usage()
        ]);
    }
    
    public function getLogs(array $filters = []): array
    {
        $filteredLogs = $this->logs;
        
        if (!empty($filters['level'])) {
            $filteredLogs = array_filter($filteredLogs, function($log) use ($filters) {
                return $log['level'] === $filters['level'];
            });
        }
        
        if (!empty($filters['since'])) {
            $since = strtotime($filters['since']);
            $filteredLogs = array_filter($filteredLogs, function($log) use ($since) {
                return strtotime($log['timestamp']) >= $since;
            });
        }
        
        return array_values($filteredLogs);
    }
    
    public function clearLogs(): void
    {
        $this->logs = [];
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
    
    private function shouldLog(string $level): bool
    {
        $levels = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
        $configLevel = array_search($this->config['log_level'], $levels);
        $messageLevel = array_search($level, $levels);
        
        return $messageLevel >= $configLevel;
    }
    
    private function getTrace(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        return array_map(function($frame) {
            return [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null
            ];
        }, $trace);
    }
    
    private function writeToFile(array $entry): void
    {
        $logEntry = json_encode($entry) . "\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    private function rotateLogs(): void
    {
        if (!file_exists($this->logFile)) {
            return;
        }
        
        if (filesize($this->logFile) > $this->config['max_log_size']) {
            // Rotate logs
            $timestamp = date('Y-m-d_H-i-s');
            $rotatedFile = pathinfo($this->logFile, PATHINFO_FILENAME) . "_$timestamp.log";
            
            rename($this->logFile, $rotatedFile);
            
            // Clean up old logs
            $this->cleanupOldLogs();
        }
    }
    
    private function cleanupOldLogs(): void
    {
        $pattern = pathinfo($this->logFile, PATHINFO_FILENAME) . '_*.log';
        $files = glob($pattern);
        
        if (count($files) > $this->config['max_log_files']) {
            // Sort by creation time (oldest first)
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove oldest files
            $toRemove = count($files) - $this->config['max_log_files'];
            for ($i = 0; $i < $toRemove; $i++) {
                unlink($files[$i]);
            }
        }
    }
}

// Error Tracking System
class ErrorTrackingSystem
{
    private array $errors = [];
    private array $config;
    private ProductionDebugger $debugger;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'enabled' => true,
            'notification_threshold' => 5,
            'notification_webhook' => null,
            'auto_grouping' => true,
            'include_context' => true
        ], $config);
        
        $this->debugger = new ProductionDebugger($this->config);
        
        // Set up error and exception handlers
        if ($this->config['enabled']) {
            set_error_handler([$this, 'handleError']);
            set_exception_handler([$this, 'handleException']);
            register_shutdown_function([$this, 'handleShutdown']);
        }
    }
    
    public function handleError(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error = [
            'type' => 'ERROR',
            'errno' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => date('Y-m-d H:i:s'),
            'stack_trace' => debug_backtrace(),
            'group_id' => $this->getGroupId($errstr, $errfile, $errline)
        ];
        
        $this->recordError($error);
        
        return true;
    }
    
    public function handleException(\Throwable $exception): void
    {
        $error = [
            'type' => 'EXCEPTION',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => date('Y-m-d H:i:s'),
            'stack_trace' => $exception->getTrace(),
            'group_id' => $this->getGroupId($exception->getMessage(), $exception->getFile(), $exception->getLine())
        ];
        
        $this->recordError($error);
    }
    
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    private function recordError(array $error): void
    {
        $this->errors[] = $error;
        
        // Log to debugger
        $this->debugger->log('ERROR', $error['message'], [
            'file' => $error['file'],
            'line' => $error['line'],
            'type' => $error['type']
        ]);
        
        // Check if notification threshold is reached
        $groupErrors = array_filter($this->errors, function($e) use ($error) {
            return $e['group_id'] === $error['group_id'];
        });
        
        if (count($groupErrors) >= $this->config['notification_threshold']) {
            $this->sendNotification($error, count($groupErrors));
        }
    }
    
    private function getGroupId(string $message, string $file, int $line): string
    {
        if (!$this->config['auto_grouping']) {
            return uniqid();
        }
        
        // Create a group ID based on error signature
        $signature = md5($message . $file . $line);
        return $signature;
    }
    
    private function sendNotification(array $error, int $count): void
    {
        if (!$this->config['notification_webhook']) {
            return;
        }
        
        $payload = [
            'error' => $error,
            'count' => $count,
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => $this->getEnvironment()
        ];
        
        // Send notification (simplified)
        $this->debugger->log('NOTIFICATION', 'Error threshold reached', $payload);
    }
    
    private function getEnvironment(): string
    {
        return $_ENV['APP_ENV'] ?? 'production';
    }
    
    public function getErrors(array $filters = []): array
    {
        $filteredErrors = $this->errors;
        
        if (!empty($filters['type'])) {
            $filteredErrors = array_filter($filteredErrors, function($error) use ($filters) {
                return $error['type'] === $filters['type'];
            });
        }
        
        if (!empty($filters['since'])) {
            $since = strtotime($filters['since']);
            $filteredErrors = array_filter($filteredErrors, function($error) use ($since) {
                return strtotime($error['timestamp']) >= $since;
            });
        }
        
        return array_values($filteredErrors);
    }
    
    public function getErrorGroups(): array
    {
        $groups = [];
        
        foreach ($this->errors as $error) {
            $groupId = $error['group_id'];
            
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'first_occurrence' => $error['timestamp'],
                    'last_occurrence' => $error['timestamp'],
                    'count' => 0,
                    'message' => $error['message'],
                    'file' => $error['file'],
                    'line' => $error['line']
                ];
            }
            
            $groups[$groupId]['count']++;
            $groups[$groupId]['last_occurrence'] = $error['timestamp'];
        }
        
        return $groups;
    }
    
    public function clearErrors(): void
    {
        $this->errors = [];
        $this->debugger->clearLogs();
    }
}

// Advanced Debugging Examples
class AdvancedDebuggingExamples
{
    private AdvancedDebugger $debugger;
    private PerformanceProfiler $profiler;
    private MemoryLeakDetector $leakDetector;
    private ProductionDebugger $prodDebugger;
    private ErrorTrackingSystem $errorTracker;
    
    public function __construct()
    {
        $this->debugger = new AdvancedDebugger();
        $this->profiler = new PerformanceProfiler();
        $this->leakDetector = new MemoryLeakDetector();
        $this->prodDebugger = new ProductionDebugger(['enabled' => true]);
        $this->errorTracker = new ErrorTrackingSystem();
    }
    
    public function demonstrateAdvancedDebugger(): void
    {
        echo "Advanced Debugger Example\n";
        echo str_repeat("-", 25) . "\n";
        
        $this->debugger->enableDebug();
        
        // Add breakpoints
        $this->debugger->addBreakpoint(__FILE__, __LINE__);
        
        // Log variables
        $this->debugger->logVariable('test_var', 'test_value');
        $this->debugger->logVariable('number', 42);
        $this->debugger->logVariable('array', [1, 2, 3]);
        
        // Log messages
        $this->debugger->logMessage('Starting debugging session', 'INFO');
        $this->debugger->logMessage('Processing data', 'DEBUG');
        
        // Simulate function calls
        $this->debugger->enterFunction('calculateSum', [10, 20]);
        $result = $this->calculateSum(10, 20);
        $this->debugger->exitFunction('calculateSum', $result);
        
        $this->debugger->enterFunction('processData', ['data']);
        $this->processData(['key' => 'value']);
        $this->debugger->exitFunction('processData');
        
        // Check breakpoints
        $this->debugger->checkBreakpoints(__FILE__, __LINE__);
        
        // Dump information
        $this->debugger->dumpCallStack();
        $this->debugger->dumpVariables();
        $this->debugger->dumpLog();
        
        $this->debugger->disableDebug();
    }
    
    public function demonstratePerformanceProfiler(): void
    {
        echo "\nPerformance Profiler Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Profile a function
        $this->profiler->startProfile('string_operations');
        
        $this->profiler->profileFunction('strlen', ['test string']);
        $this->profiler->profileFunction('strtoupper', ['test string']);
        $this->profiler->profileFunction('array_merge', [[1, 2], [3, 4]]);
        
        // Take memory snapshots
        $this->profiler->takeMemorySnapshot('before_heavy_operation');
        
        // Heavy operation
        $result = $this->heavyOperation();
        
        $this->profiler->takeMemorySnapshot('after_heavy_operation');
        
        $profile = $this->profiler->endProfile();
        
        echo "Profile completed:\n";
        echo "Duration: " . round($profile['duration'] * 1000, 2) . "ms\n";
        echo "Memory Used: " . round($profile['memory_used'] / 1024, 2) . "KB\n";
        echo "Function Calls: " . count($profile['calls']) . "\n";
        
        // Generate report
        echo "\n" . $this->profiler->generateReport();
    }
    
    public function demonstrateMemoryLeakDetection(): void
    {
        echo "\nMemory Leak Detection Example\n";
        echo str_repeat("-", 32) . "\n";
        
        // Take initial snapshot
        $this->leakDetector->takeSnapshot('initial');
        
        // Create some objects
        $object1 = new stdClass();
        $object2 = new stdClass();
        $object3 = new stdClass();
        
        // Track objects
        $id1 = $this->leakDetector->trackObject($object1);
        $id2 = $this->leakDetector->trackObject($object2);
        $id3 = $this->leakDetector->trackObject($object3);
        
        // Release some objects
        $this->leakDetector->releaseObject($object2);
        unset($object2);
        
        // Take another snapshot
        $this->leakDetector->takeSnapshot('after_objects');
        
        // Simulate some operations
        for ($i = 0; $i < 100; $i++) {
            $temp = new stdClass();
            $temp->data = "data_$i";
        }
        
        // Take final snapshot
        $this->leakDetector->takeSnapshot('final');
        
        // Detect leaks
        $leaks = $this->leakDetector->detectLeaks();
        
        echo "Detected " . count($leaks) . " potential leaks\n";
        
        // Generate report
        echo "\n" . $this->leakDetector->generateLeakReport();
    }
    
    public function demonstrateProductionDebugger(): void
    {
        echo "\nProduction Debugger Example\n";
        echo str_repeat("-", 28) . "\n";
        
        // Log different levels
        $this->prodDebugger->log('INFO', 'Application started');
        $this->prodDebugger->log('WARNING', 'Low memory warning');
        $this->prodDebugger->log('ERROR', 'Database connection failed');
        
        // Log exception
        try {
            throw new RuntimeException('Test exception');
        } catch (Exception $e) {
            $this->prodDebugger->logException($e);
        }
        
        // Log performance
        $this->prodDebugger->logPerformance('api_call', 150.5, [
            'endpoint' => '/api/users',
            'queries' => 3
        ]);
        
        // Log request
        $this->prodDebugger->logRequest(
            ['method' => 'GET', 'url' => '/api/users'],
            ['status' => 200, 'data' => ['users' => []]],
            150.5
        );
        
        // Get logs
        $logs = $this->prodDebugger->getLogs(['level' => 'ERROR']);
        
        echo "Error logs: " . count($logs) . "\n";
        
        foreach ($logs as $log) {
            echo "[{$log['timestamp']}] {$log['message']}\n";
        }
    }
    
    public function demonstrateErrorTracking(): void
    {
        echo "\nError Tracking System Example\n";
        echo str_repeat("-", 32) . "\n";
        
        // Trigger some errors
        trigger_error('Test warning', E_USER_WARNING);
        trigger_error('Test notice', E_USER_NOTICE);
        
        // Trigger an exception
        try {
            throw new InvalidArgumentException('Test exception for tracking');
        } catch (Exception $e) {
            // Exception is automatically handled by the tracker
        }
        
        // Get error groups
        $groups = $this->errorTracker->getErrorGroups();
        
        echo "Error Groups:\n";
        foreach ($groups as $groupId => $group) {
            echo "Group: $groupId\n";
            echo "  Count: {$group['count']}\n";
            echo "  Message: {$group['message']}\n";
            echo "  File: {$group['file']}\n";
            echo "  Line: {$group['line']}\n";
            echo "  First: {$group['first_occurrence']}\n";
            echo "  Last: {$group['last_occurrence']}\n\n";
        }
        
        // Get recent errors
        $recentErrors = $this->errorTracker->getErrors(['since' => '-1 hour']);
        
        echo "Recent errors (last hour): " . count($recentErrors) . "\n";
    }
    
    private function calculateSum(int $a, int $b): int
    {
        return $a + $b;
    }
    
    private function processData(array $data): array
    {
        return array_map('strtoupper', $data);
    }
    
    private function heavyOperation(): array
    {
        $result = [];
        
        for ($i = 0; $i < 1000; $i++) {
            $result[] = "item_$i";
        }
        
        return $result;
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Debugging Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateAdvancedDebugger();
        $this->demonstratePerformanceProfiler();
        $this->demonstrateMemoryLeakDetection();
        $this->demonstrateProductionDebugger();
        $this->demonstrateErrorTracking();
    }
}

// Advanced Debugging Best Practices
function printAdvancedDebuggingBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Advanced Debugging Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Development Debugging:\n";
    echo "   • Use proper breakpoints\n";
    echo "   • Log variables and state\n";
    echo "   • Track call stack\n";
    echo "   • Use step debugging\n";
    echo "   • Profile performance\n\n";
    
    echo "2. Performance Profiling:\n";
    echo "   • Profile critical paths\n";
    echo "   • Monitor memory usage\n";
    echo "   • Track function calls\n";
    echo "   • Analyze bottlenecks\n";
    echo "   • Compare before/after\n";
    
    echo "3. Memory Management:\n";
    echo "   • Detect memory leaks\n";
    echo "   • Monitor memory growth\n";
    echo "   • Track object references\n";
    echo "   • Optimize memory usage\n";
    echo "   • Use memory profiling\n";
    
    echo "4. Production Debugging:\n";
    echo "   • Use structured logging\n";
    echo "   • Log errors and exceptions\n";
    echo "   • Monitor performance\n";
    echo "   • Track user actions\n";
    echo "   • Implement alerting\n";
    
    echo "5. Error Tracking:\n";
    echo "   • Group similar errors\n";
    echo "   • Set notification thresholds\n";
    echo "   • Include context information\n";
    echo "   • Track error frequency\n";
    echo "   • Implement reporting\n";
    
    echo "6. Best Practices:\n";
    echo "   • Don't debug in production\n";
    echo "   • Use proper log levels\n";
    echo "   • Rotate log files\n";
    echo "   • Monitor system resources\n";
    echo "   • Document debugging procedures";
}

// Main execution
function runAdvancedDebuggingDemo(): void
{
    $examples = new AdvancedDebuggingExamples();
    $examples->runAllExamples();
    printAdvancedDebuggingBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAdvancedDebuggingDemo();
}
?>
