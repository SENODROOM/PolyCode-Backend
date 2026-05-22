<?php
/**
 * Profiling and Benchmarking Basics
 * 
 * This file demonstrates fundamental profiling techniques for PHP applications
 * including timing, memory usage, and performance measurement.
 */

// Simple Profiler Class
class Profiler {
    private array $timers = [];
    private array $memorySnapshots = [];
    private array $counters = [];
    private array $reports = [];
    
    public function startTimer(string $name): void {
        $this->timers[$name] = [
            'start' => microtime(true),
            'start_memory' => memory_get_usage(),
            'start_peak' => memory_get_peak_usage()
        ];
    }
    
    public function endTimer(string $name): array {
        if (!isset($this->timers[$name])) {
            throw new InvalidArgumentException("Timer '$name' not started");
        }
        
        $timer = $this->timers[$name];
        $duration = microtime(true) - $timer['start'];
        $memoryUsed = memory_get_usage() - $timer['start_memory'];
        $memoryPeakUsed = memory_get_peak_usage() - $timer['start_peak'];
        
        $result = [
            'name' => $name,
            'duration' => $duration,
            'duration_ms' => round($duration * 1000, 4),
            'memory_used' => $memoryUsed,
            'memory_used_mb' => round($memoryUsed / 1024 / 1024, 4),
            'memory_peak_used' => $memoryPeakUsed,
            'memory_peak_used_mb' => round($memoryPeakUsed / 1024 / 1024, 4)
        ];
        
        $this->reports[$name] = $result;
        return $result;
    }
    
    public function takeMemorySnapshot(string $label): void {
        $this->memorySnapshots[$label] = [
            'memory' => memory_get_usage(),
            'memory_mb' => round(memory_get_usage() / 1024 / 1024, 4),
            'peak' => memory_get_peak_usage(),
            'peak_mb' => round(memory_get_peak_usage() / 1024 / 1024, 4),
            'timestamp' => microtime(true)
        ];
    }
    
    public function incrementCounter(string $name, int $value = 1): void {
        $this->counters[$name] = ($this->counters[$name] ?? 0) + $value;
    }
    
    public function getCounter(string $name): int {
        return $this->counters[$name] ?? 0;
    }
    
    public function getAllCounters(): array {
        return $this->counters;
    }
    
    public function getReports(): array {
        return $this->reports;
    }
    
    public function getMemorySnapshots(): array {
        return $this->memorySnapshots;
    }
    
    public function generateReport(): array {
        $totalDuration = array_sum(array_column($this->reports, 'duration'));
        $totalMemoryUsed = array_sum(array_column($this->reports, 'memory_used'));
        
        return [
            'summary' => [
                'total_duration' => $totalDuration,
                'total_duration_ms' => round($totalDuration * 1000, 4),
                'total_memory_used' => $totalMemoryUsed,
                'total_memory_used_mb' => round($totalMemoryUsed / 1024 / 1024, 4),
                'timers_count' => count($this->reports),
                'counters' => $this->counters
            ],
            'timers' => $this->reports,
            'memory_snapshots' => $this->memorySnapshots
        ];
    }
    
    public function reset(): void {
        $this->timers = [];
        $this->memorySnapshots = [];
        $this->counters = [];
        $this->reports = [];
    }
}

// Benchmark Functions
class Benchmark {
    public static function compare(callable $func1, callable $func2, int $iterations = 10000): array {
        $profiler = new Profiler();
        
        // Benchmark first function
        $profiler->startTimer('function1');
        for ($i = 0; $i < $iterations; $i++) {
            $func1();
        }
        $result1 = $profiler->endTimer('function1');
        
        // Benchmark second function
        $profiler->startTimer('function2');
        for ($i = 0; $i < $iterations; $i++) {
            $func2();
        }
        $result2 = $profiler->endTimer('function2');
        
        return [
            'function1' => $result1,
            'function2' => $result2,
            'speedup' => $result1['duration'] / $result2['duration'],
            'winner' => $result1['duration'] < $result2['duration'] ? 'function1' : 'function2'
        ];
    }
    
    public static function measure(callable $func, int $iterations = 1000): array {
        $profiler = new Profiler();
        $profiler->startTimer('benchmark');
        
        for ($i = 0; $i < $iterations; $i++) {
            $func();
        }
        
        $result = $profiler->endTimer('benchmark');
        $result['iterations'] = $iterations;
        $result['avg_duration'] = $result['duration'] / $iterations;
        $result['avg_duration_ms'] = round($result['avg_duration'] * 1000000, 4); // microseconds
        
        return $result;
    }
}

// Memory Usage Analyzer
class MemoryAnalyzer {
    private array $snapshots = [];
    
    public function snapshot(string $label): void {
        $this->snapshots[$label] = [
            'current' => memory_get_usage(),
            'current_mb' => round(memory_get_usage() / 1024 / 1024, 4),
            'peak' => memory_get_peak_usage(),
            'peak_mb' => round(memory_get_peak_usage() / 1024 / 1024, 4),
            'timestamp' => microtime(true)
        ];
    }
    
    public function getDifference(string $from, string $to): array {
        if (!isset($this->snapshots[$from]) || !isset($this->snapshots[$to])) {
            throw new InvalidArgumentException("Snapshots '$from' or '$to' not found");
        }
        
        $fromSnapshot = $this->snapshots[$from];
        $toSnapshot = $this->snapshots[$to];
        
        return [
            'memory_diff' => $toSnapshot['current'] - $fromSnapshot['current'],
            'memory_diff_mb' => round(($toSnapshot['current'] - $fromSnapshot['current']) / 1024 / 1024, 4),
            'peak_diff' => $toSnapshot['peak'] - $fromSnapshot['peak'],
            'peak_diff_mb' => round(($toSnapshot['peak'] - $fromSnapshot['peak']) / 1024 / 1024, 4),
            'time_diff' => $toSnapshot['timestamp'] - $fromSnapshot['timestamp']
        ];
    }
    
    public function getSnapshots(): array {
        return $this->snapshots;
    }
    
    public function reset(): void {
        $this->snapshots = [];
    }
}

// Example Functions for Benchmarking
class ExampleFunctions {
    // Inefficient string concatenation
    public static function slowStringConcat(array $strings): string {
        $result = '';
        foreach ($strings as $string) {
            $result .= $string;
        }
        return $result;
    }
    
    // Efficient string concatenation
    public static function fastStringConcat(array $strings): string {
        return implode('', $strings);
    }
    
    // Inefficient array access
    public static function slowArrayAccess(array $data, array $keys): array {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $result[] = $data[$key];
            }
        }
        return $result;
    }
    
    // Efficient array access
    public static function fastArrayAccess(array $data, array $keys): array {
        return array_intersect_key($data, array_flip($keys));
    }
    
    // Inefficient loop
    public static function slowLoop(int $n): int {
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $i;
        }
        return $sum;
    }
    
    // Efficient loop
    public static function fastLoop(int $n): int {
        return array_sum(range(0, $n - 1));
    }
    
    // Memory intensive operation
    public static function memoryIntensive(int $size): array {
        $data = [];
        for ($i = 0; $i < $size; $i++) {
            $data[] = str_repeat('x', 1000);
        }
        return $data;
    }
    
    // CPU intensive operation
    public static function cpuIntensive(int $iterations): int {
        $result = 0;
        for ($i = 0; $i < $iterations; $i++) {
            $result += sqrt($i) * sin($i);
        }
        return $result;
    }
}

// Performance Testing Examples
class PerformanceTests {
    private Profiler $profiler;
    private MemoryAnalyzer $memoryAnalyzer;
    
    public function __construct() {
        $this->profiler = new Profiler();
        $this->memoryAnalyzer = new MemoryAnalyzer();
    }
    
    public function testStringConcatenation(): void {
        echo "String Concatenation Performance Test\n";
        echo str_repeat("-", 40) . "\n";
        
        $strings = array_fill(0, 1000, 'Hello World ');
        
        // Test slow method
        $this->profiler->startTimer('slow_concat');
        $result1 = ExampleFunctions::slowStringConcat($strings);
        $slowResult = $this->profiler->endTimer('slow_concat');
        
        // Test fast method
        $this->profiler->startTimer('fast_concat');
        $result2 = ExampleFunctions::fastStringConcat($strings);
        $fastResult = $this->profiler->endTimer('fast_concat');
        
        echo "Slow method: {$slowResult['duration_ms']}ms, {$slowResult['memory_used_mb']}MB\n";
        echo "Fast method: {$fastResult['duration_ms']}ms, {$fastResult['memory_used_mb']}MB\n";
        echo "Speedup: " . round($slowResult['duration'] / $fastResult['duration'], 2) . "x faster\n";
        echo "Memory reduction: " . round(($slowResult['memory_used'] - $fastResult['memory_used']) / 1024 / 1024, 4) . "MB\n\n";
    }
    
    public function testArrayAccess(): void {
        echo "Array Access Performance Test\n";
        echo str_repeat("-", 35) . "\n";
        
        $data = array_fill(0, 10000, 'value');
        $keys = array_rand($data, 1000);
        
        // Test slow method
        $this->profiler->startTimer('slow_array');
        $result1 = ExampleFunctions::slowArrayAccess($data, $keys);
        $slowResult = $this->profiler->endTimer('slow_array');
        
        // Test fast method
        $this->profiler->startTimer('fast_array');
        $result2 = ExampleFunctions::fastArrayAccess($data, $keys);
        $fastResult = $this->profiler->endTimer('fast_array');
        
        echo "Slow method: {$slowResult['duration_ms']}ms\n";
        echo "Fast method: {$fastResult['duration_ms']}ms\n";
        echo "Speedup: " . round($slowResult['duration'] / $fastResult['duration'], 2) . "x faster\n\n";
    }
    
    public function testLoopPerformance(): void {
        echo "Loop Performance Test\n";
        echo str_repeat("-", 25) . "\n";
        
        $n = 1000000;
        
        // Test slow loop
        $this->profiler->startTimer('slow_loop');
        $result1 = ExampleFunctions::slowLoop($n);
        $slowResult = $this->profiler->endTimer('slow_loop');
        
        // Test fast loop
        $this->profiler->startTimer('fast_loop');
        $result2 = ExampleFunctions::fastLoop($n);
        $fastResult = $this->profiler->endTimer('fast_loop');
        
        echo "Slow loop: {$slowResult['duration_ms']}ms\n";
        echo "Fast loop: {$fastResult['duration_ms']}ms\n";
        echo "Speedup: " . round($slowResult['duration'] / $fastResult['duration'], 2) . "x faster\n\n";
    }
    
    public function testMemoryUsage(): void {
        echo "Memory Usage Analysis\n";
        echo str_repeat("-", 25) . "\n";
        
        $this->memoryAnalyzer->snapshot('start');
        
        // Memory intensive operation
        $this->profiler->startTimer('memory_intensive');
        $data = ExampleFunctions::memoryIntensive(1000);
        $result = $this->profiler->endTimer('memory_intensive');
        
        $this->memoryAnalyzer->snapshot('after_memory_intensive');
        
        // Clean up
        unset($data);
        gc_collect_cycles();
        
        $this->memoryAnalyzer->snapshot('after_cleanup');
        
        $diff1 = $this->memoryAnalyzer->getDifference('start', 'after_memory_intensive');
        $diff2 = $this->memoryAnalyzer->getDifference('after_memory_intensive', 'after_cleanup');
        
        echo "Memory intensive operation: {$result['duration_ms']}ms\n";
        echo "Memory used: {$diff1['memory_diff_mb']}MB\n";
        echo "Memory freed after cleanup: {$diff2['memory_diff_mb']}MB\n";
        echo "Current memory: " . round(memory_get_usage() / 1024 / 1024, 4) . "MB\n";
        echo "Peak memory: " . round(memory_get_peak_usage() / 1024 / 1024, 4) . "MB\n\n";
    }
    
    public function testCpuIntensive(): void {
        echo "CPU Intensive Operation Test\n";
        echo str_repeat("-", 35) . "\n";
        
        $iterations = 100000;
        
        $this->profiler->startTimer('cpu_intensive');
        $result = ExampleFunctions::cpuIntensive($iterations);
        $cpuResult = $this->profiler->endTimer('cpu_intensive');
        
        echo "CPU intensive operation: {$cpuResult['duration_ms']}ms\n";
        echo "Iterations: $iterations\n";
        echo "Time per iteration: " . round($cpuResult['duration'] / $iterations * 1000000, 4) . " microseconds\n\n";
    }
    
    public function runAllTests(): void {
        echo "Performance Profiling Examples\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $this->testStringConcatenation();
        $this->testArrayAccess();
        $this->testLoopPerformance();
        $this->testMemoryUsage();
        $this->testCpuIntensive();
        
        // Generate final report
        $report = $this->profiler->generateReport();
        echo "Overall Performance Summary\n";
        echo str_repeat("-", 30) . "\n";
        echo "Total time: {$report['summary']['total_duration_ms']}ms\n";
        echo "Total memory used: {$report['summary']['total_memory_used_mb']}MB\n";
        echo "Operations performed: {$report['summary']['timers_count']}\n\n";
    }
}

// Advanced Profiler with Call Stack
class AdvancedProfiler {
    private array $callStack = [];
    private array $functions = [];
    private bool $enabled = false;
    
    public function enable(): void {
        $this->enabled = true;
        $this->callStack = [];
        $this->functions = [];
    }
    
    public function disable(): void {
        $this->enabled = false;
    }
    
    public function enterFunction(string $name): void {
        if (!$this->enabled) return;
        
        $this->callStack[] = [
            'name' => $name,
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage()
        ];
    }
    
    public function exitFunction(string $name): void {
        if (!$this->enabled) return;
        
        $stackDepth = count($this->callStack) - 1;
        if ($stackDepth >= 0 && $this->callStack[$stackDepth]['name'] === $name) {
            $call = array_pop($this->callStack);
            $duration = microtime(true) - $call['start_time'];
            $memoryUsed = memory_get_usage() - $call['start_memory'];
            
            if (!isset($this->functions[$name])) {
                $this->functions[$name] = [
                    'calls' => 0,
                    'total_duration' => 0,
                    'total_memory' => 0,
                    'min_duration' => PHP_FLOAT_MAX,
                    'max_duration' => 0
                ];
            }
            
            $this->functions[$name]['calls']++;
            $this->functions[$name]['total_duration'] += $duration;
            $this->functions[$name]['total_memory'] += $memoryUsed;
            $this->functions[$name]['min_duration'] = min($this->functions[$name]['min_duration'], $duration);
            $this->functions[$name]['max_duration'] = max($this->functions[$name]['max_duration'], $duration);
        }
    }
    
    public function getReport(): array {
        $report = [];
        
        foreach ($this->functions as $name => $data) {
            $report[$name] = [
                'calls' => $data['calls'],
                'total_duration' => $data['total_duration'],
                'avg_duration' => $data['total_duration'] / $data['calls'],
                'min_duration' => $data['min_duration'],
                'max_duration' => $data['max_duration'],
                'total_memory' => $data['total_memory'],
                'avg_memory' => $data['total_memory'] / $data['calls']
            ];
        }
        
        // Sort by total duration
        uasort($report, function($a, $b) {
            return $b['total_duration'] <=> $a['total_duration'];
        });
        
        return $report;
    }
}

// Decorator for automatic function profiling
function profileFunction(callable $func, string $name = null): callable {
    static $profiler = null;
    if ($profiler === null) {
        $profiler = new AdvancedProfiler();
        $profiler->enable();
    }
    
    $functionName = $name ?? 'anonymous_' . spl_object_hash($func);
    
    return function(...$args) use ($func, $functionName, $profiler) {
        $profiler->enterFunction($functionName);
        try {
            return $func(...$args);
        } finally {
            $profiler->exitFunction($functionName);
        }
    };
}

// Demo of automatic profiling
class ProfilingDemo {
    public function processData(array $data): array {
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->transformItem($item);
        }
        return $result;
    }
    
    private function transformItem(array $item): array {
        return [
            'id' => $item['id'],
            'name' => strtoupper($item['name']),
            'value' => $item['value'] * 2
        ];
    }
    
    public function calculateStats(array $numbers): array {
        return [
            'sum' => array_sum($numbers),
            'avg' => array_sum($numbers) / count($numbers),
            'min' => min($numbers),
            'max' => max($numbers)
        ];
    }
}

// Main execution
function runProfilingDemo(): void {
    // Basic profiling
    $tests = new PerformanceTests();
    $tests->runAllTests();
    
    // Advanced profiling demo
    echo "Advanced Profiling Demo\n";
    echo str_repeat("=", 30) . "\n";
    
    $profiler = new AdvancedProfiler();
    $profiler->enable();
    
    $demo = new ProfilingDemo();
    
    // Profile some operations
    $profiler->enterFunction('processData');
    $data = array_fill(0, 1000, ['id' => 1, 'name' => 'test', 'value' => 10]);
    $result = $demo->processData($data);
    $profiler->exitFunction('processData');
    
    $profiler->enterFunction('calculateStats');
    $numbers = range(1, 1000);
    $stats = $demo->calculateStats($numbers);
    $profiler->exitFunction('calculateStats');
    
    $report = $profiler->getReport();
    
    echo "Function Performance Report:\n";
    foreach ($report as $name => $data) {
        echo "\n$name:\n";
        echo "  Calls: {$data['calls']}\n";
        echo "  Total time: " . round($data['total_duration'] * 1000, 4) . "ms\n";
        echo "  Avg time: " . round($data['avg_duration'] * 1000, 4) . "ms\n";
        echo "  Min time: " . round($data['min_duration'] * 1000, 4) . "ms\n";
        echo "  Max time: " . round($data['max_duration'] * 1000, 4) . "ms\n";
        echo "  Avg memory: " . round($data['avg_memory'] / 1024, 4) . "KB\n";
    }
    
    // Demonstrate automatic profiling
    echo "\nAutomatic Function Profiling Demo:\n";
    echo str_repeat("-", 40) . "\n";
    
    $slowFunc = profileFunction(function($n) {
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $i;
        }
        return $sum;
    }, 'slow_sum');
    
    $fastFunc = profileFunction(function($n) {
        return array_sum(range(0, $n - 1));
    }, 'fast_sum');
    
    // Call the functions
    for ($i = 0; $i < 10; $i++) {
        $slowFunc(1000);
        $fastFunc(1000);
    }
    
    $autoReport = $profiler->getReport();
    echo "Automatic profiling results:\n";
    if (isset($autoReport['slow_sum'])) {
        $data = $autoReport['slow_sum'];
        echo "slow_sum: {$data['calls']} calls, " . round($data['avg_duration'] * 1000, 4) . "ms avg\n";
    }
    if (isset($autoReport['fast_sum'])) {
        $data = $autoReport['fast_sum'];
        echo "fast_sum: {$data['calls']} calls, " . round($data['avg_duration'] * 1000, 4) . "ms avg\n";
    }
}

// Profiling Best Practices
function printProfilingBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Profiling Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. When to Profile:\n";
    echo "   • Before optimization (baseline)\n";
    echo "   • After optimization (verify improvement)\n";
    echo "   • When performance issues are reported\n";
    echo "   • Regularly in development\n\n";
    
    echo "2. What to Measure:\n";
    echo "   • Execution time (wall clock)\n";
    echo "   • CPU time (processor usage)\n";
    echo "   • Memory usage (current and peak)\n";
    echo "   • I/O operations (database, files)\n";
    echo "   • Network latency\n\n";
    
    echo "3. Profiling Tools:\n";
    echo "   • Xdebug Profiler\n";
    echo "   • Blackfire.io\n";
    echo "   • Tideways\n";
    echo "   • PHP built-in functions (microtime, memory_get_usage)\n\n";
    
    echo "4. Common Pitfalls:\n";
    echo "   • Profiling in production\n";
    echo "   • Optimizing without measuring\n";
    echo "   • Micro-optimizations prematurely\n";
    echo "   • Ignoring the big picture\n\n";
    
    echo "5. Optimization Strategy:\n";
    echo "   • Measure first\n";
    echo "   • Identify bottlenecks\n";
    echo "   • Optimize biggest impact first\n";
    echo "   • Verify improvements\n";
    echo "   • Repeat as needed\n";
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runProfilingDemo();
    printProfilingBestPractices();
}
?>
