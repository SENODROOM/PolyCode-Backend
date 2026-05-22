<?php
/**
 * Performance Optimization Techniques
 * 
 * This file demonstrates various performance optimization techniques including:
 * - Profiling and benchmarking
 * - Memory optimization
 * - Database query optimization
 * - Code optimization patterns
 * - Performance monitoring
 */

// Performance Profiler Class
class PerformanceProfiler {
    private array $timers = [];
    private array $counters = [];
    private array $memoryUsage = [];
    
    public function startTimer(string $name): void {
        $this->timers[$name] = ['start' => microtime(true), 'memory' => memory_get_usage()];
    }
    
    public function endTimer(string $name): array {
        if (!isset($this->timers[$name])) {
            throw new InvalidArgumentException("Timer '$name' not started");
        }
        
        $timer = $this->timers[$name];
        $duration = microtime(true) - $timer['start'];
        $memoryUsed = memory_get_usage() - $timer['memory'];
        
        $result = [
            'duration' => $duration,
            'memory_used' => $memoryUsed,
            'start_memory' => $timer['memory'],
            'end_memory' => memory_get_usage()
        ];
        
        $this->timers[$name]['result'] = $result;
        return $result;
    }
    
    public function getTimerResult(string $name): ?array {
        return $this->timers[$name]['result'] ?? null;
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
    
    public function recordMemoryUsage(string $label): void {
        $this->memoryUsage[$label] = [
            'memory' => memory_get_usage(),
            'peak' => memory_get_peak_usage(),
            'timestamp' => microtime(true)
        ];
    }
    
    public function getMemoryUsage(): array {
        return $this->memoryUsage;
    }
    
    public function getReport(): array {
        $report = [
            'timers' => [],
            'counters' => $this->counters,
            'memory_usage' => $this->memoryUsage,
            'summary' => [
                'total_time' => 0,
                'total_memory_used' => 0,
                'peak_memory' => memory_get_peak_usage()
            ]
        ];
        
        foreach ($this->timers as $name => $timer) {
            if (isset($timer['result'])) {
                $report['timers'][$name] = $timer['result'];
                $report['summary']['total_time'] += $timer['result']['duration'];
                $report['summary']['total_memory_used'] += $timer['result']['memory_used'];
            }
        }
        
        return $report;
    }
}

// Memory Optimizer Class
class MemoryOptimizer {
    private static array $pool = [];
    private static int $poolSize = 100;
    
    // Object pooling for memory efficiency
    public static function getObject(string $class, ...$args): object {
        $key = $class . '_' . serialize($args);
        
        if (isset(self::$pool[$key])) {
            $obj = array_pop(self::$pool[$key]);
            return $obj;
        }
        
        return new $class(...$args);
    }
    
    public static function releaseObject(object $object): void {
        $class = get_class($object);
        $key = $class;
        
        if (!isset(self::$pool[$key])) {
            self::$pool[$key] = [];
        }
        
        if (count(self::$pool[$key]) < self::$poolSize) {
            self::$pool[$key][] = $object;
        }
    }
    
    // Memory-efficient array operations
    public static function arrayFilterLarge(array $array, callable $callback): array {
        $result = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result[$key] = $value;
            }
            
            // Periodic garbage collection for large arrays
            if (count($result) % 1000 === 0) {
                gc_collect_cycles();
            }
        }
        return $result;
    }
    
    // Lazy loading implementation
    public static function createLazyLoader(callable $loader): callable {
        return function() use ($loader) {
            static $loaded = null;
            
            if ($loaded === null) {
                $loaded = $loader();
            }
            
            return $loaded;
        };
    }
    
    // Memory usage monitoring
    public static function getMemoryStats(): array {
        return [
            'current' => memory_get_usage(),
            'peak' => memory_get_peak_usage(),
            'current_formatted' => self::formatBytes(memory_get_usage()),
            'peak_formatted' => self::formatBytes(memory_get_peak_usage())
        ];
    }
    
    private static function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Database Query Optimizer
class DatabaseQueryOptimizer {
    private PDO $pdo;
    private array $queryLog = [];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function optimizedQuery(string $sql, array $params = [], array $options = []): array {
        $startTime = microtime(true);
        
        // Add query options
        if (isset($options['limit']) && !str_contains($sql, 'LIMIT')) {
            $sql .= " LIMIT " . (int)$options['limit'];
        }
        
        // Prepare and execute
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $duration = microtime(true) - $startTime;
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Log query
        $this->queryLog[] = [
            'sql' => $sql,
            'params' => $params,
            'duration' => $duration,
            'rows' => count($result),
            'memory' => memory_get_usage()
        ];
        
        return $result;
    }
    
    public function batchInsert(string $table, array $data, int $batchSize = 1000): bool {
        if (empty($data)) {
            return true;
        }
        
        $columns = array_keys($data[0]);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES ($placeholders)";
        
        $this->pdo->beginTransaction();
        
        try {
            $batch = [];
            foreach ($data as $row) {
                $batch[] = array_values($row);
                
                if (count($batch) >= $batchSize) {
                    $this->executeBatch($sql, $batch);
                    $batch = [];
                }
            }
            
            // Insert remaining records
            if (!empty($batch)) {
                $this->executeBatch($sql, $batch);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    private function executeBatch(string $sql, array $batch): void {
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($batch as $params) {
            $stmt->execute($params);
        }
    }
    
    public function getQueryStats(): array {
        $stats = [
            'total_queries' => count($this->queryLog),
            'total_duration' => 0,
            'avg_duration' => 0,
            'slow_queries' => [],
            'memory_usage' => 0
        ];
        
        foreach ($this->queryLog as $query) {
            $stats['total_duration'] += $query['duration'];
            $stats['memory_usage'] = max($stats['memory_usage'], $query['memory']);
            
            if ($query['duration'] > 0.1) { // Slow queries > 100ms
                $stats['slow_queries'][] = $query;
            }
        }
        
        if ($stats['total_queries'] > 0) {
            $stats['avg_duration'] = $stats['total_duration'] / $stats['total_queries'];
        }
        
        return $stats;
    }
    
    public function clearQueryLog(): void {
        $this->queryLog = [];
    }
}

// Code Optimization Patterns
class CodeOptimizer {
    // Fast array search using hash table
    public static function fastArraySearch(array $haystack, array $needles): array {
        $lookup = [];
        
        // Create lookup table
        foreach ($haystack as $item) {
            $lookup[$item] = true;
        }
        
        // Search needles
        $found = [];
        foreach ($needles as $needle) {
            if (isset($lookup[$needle])) {
                $found[] = $needle;
            }
        }
        
        return $found;
    }
    
    // Optimized string operations
    public static function optimizeStringOperations(array $strings): array {
        // Pre-allocate result array
        $results = array_fill(0, count($strings), null);
        
        foreach ($strings as $i => $string) {
            // Use more efficient string operations
            $results[$i] = [
                'length' => strlen($string),
                'words' => str_word_count($string),
                'upper' => strtoupper($string),
                'lower' => strtolower($string)
            ];
        }
        
        return $results;
    }
    
    // Loop optimization
    public static function optimizedLoop(array $data, callable $processor): array {
        $count = count($data);
        $result = [];
        
        // Pre-allocate result array
        for ($i = 0; $i < $count; $i++) {
            $result[$i] = null;
        }
        
        // Process in chunks for better memory management
        $chunkSize = 1000;
        for ($i = 0; $i < $count; $i += $chunkSize) {
            $chunk = array_slice($data, $i, $chunkSize);
            
            foreach ($chunk as $key => $item) {
                $result[$key] = $processor($item);
            }
            
            // Optional: periodic garbage collection
            if ($i % ($chunkSize * 10) === 0) {
                gc_collect_cycles();
            }
        }
        
        return $result;
    }
    
    // Cache expensive computations
    private static array $computationCache = [];
    
    public static function cachedComputation(callable $computation, string $key, int $ttl = 3600): mixed {
        $cacheKey = $key . '_' . crc32(serialize($computation));
        
        if (isset(self::$computationCache[$cacheKey])) {
            $cached = self::$computationCache[$cacheKey];
            
            if ($cached['expires'] > time()) {
                return $cached['result'];
            }
        }
        
        $result = $computation();
        self::$computationCache[$cacheKey] = [
            'result' => $result,
            'expires' => time() + $ttl
        ];
        
        return $result;
    }
    
    public static function clearComputationCache(): void {
        self::$computationCache = [];
    }
}

// Performance Monitor
class PerformanceMonitor {
    private array $metrics = [];
    private array $alerts = [];
    
    public function addMetric(string $name, float $value, array $context = []): void {
        $this->metrics[] = [
            'name' => $name,
            'value' => $value,
            'context' => $context,
            'timestamp' => microtime(true)
        ];
        
        // Check for performance alerts
        $this->checkAlerts($name, $value);
    }
    
    private function checkAlerts(string $name, float $value): void {
        $thresholds = [
            'response_time' => 1.0, // 1 second
            'memory_usage' => 50 * 1024 * 1024, // 50MB
            'query_time' => 0.1, // 100ms
            'cpu_usage' => 80.0 // 80%
        ];
        
        if (isset($thresholds[$name]) && $value > $thresholds[$name]) {
            $this->alerts[] = [
                'metric' => $name,
                'value' => $value,
                'threshold' => $thresholds[$name],
                'timestamp' => microtime(true)
            ];
        }
    }
    
    public function getMetrics(): array {
        return $this->metrics;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function getReport(): array {
        $report = [
            'total_metrics' => count($this->metrics),
            'total_alerts' => count($this->alerts),
            'metric_summary' => [],
            'recent_alerts' => array_slice($this->alerts, -5)
        ];
        
        // Calculate metric summaries
        $grouped = [];
        foreach ($this->metrics as $metric) {
            $name = $metric['name'];
            if (!isset($grouped[$name])) {
                $grouped[$name] = ['values' => [], 'count' => 0];
            }
            $grouped[$name]['values'][] = $metric['value'];
            $grouped[$name]['count']++;
        }
        
        foreach ($grouped as $name => $data) {
            $values = $data['values'];
            $report['metric_summary'][$name] = [
                'count' => $data['count'],
                'min' => min($values),
                'max' => max($values),
                'avg' => array_sum($values) / count($values)
            ];
        }
        
        return $report;
    }
}

// Usage Examples
echo "=== Performance Optimization Demo ===\n\n";

// Initialize profiler
$profiler = new PerformanceProfiler();

// 1. Profiling Example
echo "1. Profiling Example:\n";
$profiler->startTimer('string_processing');

$strings = array_fill(0, 10000, 'Sample string for performance testing');
$optimized = CodeOptimizer::optimizeStringOperations($strings);

$profiler->endTimer('string_processing');
$result = $profiler->getTimerResult('string_processing');
echo "String processing: " . number_format($result['duration'] * 1000, 2) . "ms\n";
echo "Memory used: " . MemoryOptimizer::formatBytes($result['memory_used']) . "\n\n";

// 2. Memory Optimization
echo "2. Memory Optimization:\n";
$profiler->startTimer('memory_optimization');

$largeArray = range(1, 100000);
$filtered = MemoryOptimizer::arrayFilterLarge($largeArray, function($value) {
    return $value % 2 === 0;
});

$profiler->endTimer('memory_optimization');
$result = $profiler->getTimerResult('memory_optimization');
echo "Memory optimization: " . number_format($result['duration'] * 1000, 2) . "ms\n";
echo "Filtered " . count($filtered) . " even numbers\n\n";

// 3. Fast Array Search
echo "3. Fast Array Search:\n";
$profiler->startTimer('fast_search');

$haystack = range(1, 50000);
$needles = [100, 500, 1000, 25000, 50000];
$found = CodeOptimizer::fastArraySearch($haystack, $needles);

$profiler->endTimer('fast_search');
$result = $profiler->getTimerResult('fast_search');
echo "Fast search: " . number_format($result['duration'] * 1000, 2) . "ms\n";
echo "Found: " . implode(', ', $found) . "\n\n";

// 4. Cached Computation
echo "4. Cached Computation:\n";
$profiler->startTimer('first_computation');

$expensiveFunction = function() {
    // Simulate expensive computation
    usleep(10000); // 10ms
    return array_sum(range(1, 1000));
};

$result1 = CodeOptimizer::cachedComputation($expensiveFunction, 'expensive_calc');
$profiler->endTimer('first_computation');

$profiler->startTimer('cached_computation');
$result2 = CodeOptimizer::cachedComputation($expensiveFunction, 'expensive_calc');
$profiler->endTimer('cached_computation');

$firstResult = $profiler->getTimerResult('first_computation');
$cachedResult = $profiler->getTimerResult('cached_computation');

echo "First computation: " . number_format($firstResult['duration'] * 1000, 2) . "ms\n";
echo "Cached computation: " . number_format($cachedResult['duration'] * 1000, 2) . "ms\n";
echo "Speedup: " . number_format($firstResult['duration'] / $cachedResult['duration'], 1) . "x\n\n";

// 5. Database Query Optimization (simulation)
echo "5. Database Query Optimization:\n";
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->exec('CREATE TABLE test_data (id INTEGER PRIMARY KEY, value TEXT, score INTEGER)');
    
    // Insert test data
    $pdo->beginTransaction();
    for ($i = 1; $i <= 1000; $i++) {
        $pdo->exec("INSERT INTO test_data (value, score) VALUES ('item_$i', " . rand(1, 100) . ")");
    }
    $pdo->commit();
    
    $optimizer = new DatabaseQueryOptimizer($pdo);
    
    $profiler->startTimer('optimized_query');
    $results = $optimizer->optimizedQuery('SELECT * FROM test_data WHERE score > ?', [50], ['limit' => 100]);
    $profiler->endTimer('optimized_query');
    
    $queryResult = $profiler->getTimerResult('optimized_query');
    echo "Optimized query: " . number_format($queryResult['duration'] * 1000, 2) . "ms\n";
    echo "Rows returned: " . count($results) . "\n";
    
    $stats = $optimizer->getQueryStats();
    echo "Query stats: " . json_encode($stats) . "\n";
    
} catch (Exception $e) {
    echo "Database simulation error: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Performance Monitoring
echo "6. Performance Monitoring:\n";
$monitor = new PerformanceMonitor();

$monitor->addMetric('response_time', 0.5);
$monitor->addMetric('response_time', 0.8);
$monitor->addMetric('response_time', 1.2); // This should trigger alert
$monitor->addMetric('memory_usage', 30 * 1024 * 1024);
$monitor->addMetric('memory_usage', 60 * 1024 * 1024); // This should trigger alert

$report = $monitor->getReport();
echo "Performance Report:\n";
echo "Total metrics: {$report['total_metrics']}\n";
echo "Total alerts: {$report['total_alerts']}\n";

foreach ($report['metric_summary'] as $name => $summary) {
    echo "$name: avg=" . number_format($summary['avg'], 3) . ", min=" . number_format($summary['min'], 3) . ", max=" . number_format($summary['max'], 3) . "\n";
}

echo "\n";

// 7. Final Performance Report
echo "7. Final Performance Report:\n";
$finalReport = $profiler->getReport();
echo "Total execution time: " . number_format($finalReport['summary']['total_time'] * 1000, 2) . "ms\n";
echo "Total memory used: " . MemoryOptimizer::formatBytes($finalReport['summary']['total_memory_used']) . "\n";
echo "Peak memory: " . MemoryOptimizer::formatBytes($finalReport['summary']['peak_memory']) . "\n";

echo "\nTimers:\n";
foreach ($finalReport['timers'] as $name => $timer) {
    echo "  $name: " . number_format($timer['duration'] * 1000, 2) . "ms (" . MemoryOptimizer::formatBytes($timer['memory_used']) . ")\n";
}

echo "\nCounters:\n";
foreach ($finalReport['counters'] as $name => $count) {
    echo "  $name: $count\n";
}

echo "\n=== Performance Optimization Demo Complete ===\n";
?>
