<?php
/**
 * Memory Optimization Techniques
 * 
 * This file demonstrates memory management and optimization strategies
 * for PHP applications to reduce memory footprint and prevent leaks.
 */

// Memory Monitor Class
class MemoryMonitor {
    private array $snapshots = [];
    private int $peakMemory = 0;
    private array $alerts = [];
    
    public function snapshot(string $label): void {
        $current = memory_get_usage();
        $peak = memory_get_peak_usage();
        
        $this->snapshots[$label] = [
            'current' => $current,
            'current_mb' => round($current / 1024 / 1024, 4),
            'peak' => $peak,
            'peak_mb' => round($peak / 1024 / 1024, 4),
            'timestamp' => microtime(true)
        ];
        
        if ($current > $this->peakMemory) {
            $this->peakMemory = $current;
        }
        
        // Alert if memory usage is high
        if ($current > 50 * 1024 * 1024) { // 50MB
            $this->alerts[] = [
                'type' => 'high_memory',
                'label' => $label,
                'memory_mb' => round($current / 1024 / 1024, 2),
                'timestamp' => microtime(true)
            ];
        }
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
            'time_diff' => $toSnapshot['timestamp'] - $fromSnapshot['timestamp]
        ];
    }
    
    public function getSnapshots(): array {
        return $this->snapshots;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function getPeakMemory(): int {
        return $this->peakMemory;
    }
    
    public function reset(): void {
        $this->snapshots = [];
        $this->peakMemory = 0;
        $this->alerts = [];
    }
    
    public function printReport(): void {
        echo "\nMemory Monitor Report\n";
        echo str_repeat("-", 25) . "\n";
        
        foreach ($this->snapshots as $label => $snapshot) {
            echo "$label: {$snapshot['current_mb']}MB (peak: {$snapshot['peak_mb']}MB)\n";
        }
        
        if (!empty($this->alerts)) {
            echo "\nAlerts:\n";
            foreach ($this->alerts as $alert) {
                echo "  HIGH MEMORY at {$alert['label']}: {$alert['memory_mb']}MB\n";
            }
        }
        
        echo "\nPeak memory: " . round($this->peakMemory / 1024 / 1024, 4) . "MB\n";
    }
}

// Memory-Efficient Data Structures
class MemoryEfficientStructures {
    
    // Generator for large datasets (memory efficient)
    public static function generateLargeDataset(int $size): Generator {
        for ($i = 0; $i < $size; $i++) {
            yield [
                'id' => $i,
                'name' => "Item $i",
                'value' => rand(1, 1000),
                'timestamp' => time()
            ];
        }
    }
    
    // Memory-efficient array operations
    public static function processLargeArray(callable $processor, array $array): array {
        $result = [];
        $chunkSize = 1000;
        $chunks = array_chunk($array, $chunkSize);
        
        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $result[] = $processor($item);
            }
            
            // Periodic garbage collection
            gc_collect_cycles();
        }
        
        return $result;
    }
    
    // Stream processing for large files
    public static function processLargeFile(string $filename, callable $processor): void {
        $handle = fopen($filename, 'r');
        if (!$handle) {
            throw new InvalidArgumentException("Cannot open file: $filename");
        }
        
        while (($line = fgets($handle)) !== false) {
            $processor(trim($line));
            
            // Periodic garbage collection
            if (rand(1, 100) === 1) {
                gc_collect_cycles();
            }
        }
        
        fclose($handle);
    }
    
    // Memory-efficient string operations
    public static function efficientStringConcat(array $strings): string {
        return implode('', $strings);
    }
    
    // Memory-efficient filtering
    public static function memoryEfficientFilter(array $array, callable $predicate): array {
        $result = [];
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    // Lazy evaluation wrapper
    public static function lazyLoad(callable $loader): callable {
        return function() use ($loader) {
            static $loaded = null;
            if ($loaded === null) {
                $loaded = $loader();
            }
            return $loaded;
        };
    }
}

// Object Pool for Memory Management
class ObjectPool {
    private array $pool = [];
    private string $className;
    private int $maxSize;
    
    public function __construct(string $className, int $maxSize = 100) {
        $this->className = $className;
        $this->maxSize = $maxSize;
    }
    
    public function get(): object {
        if (!empty($this->pool)) {
            $object = array_pop($this->pool);
            return $object;
        }
        
        return new $this->className();
    }
    
    public function release(object $object): void {
        if (count($this->pool) < $this->maxSize && $object instanceof $this->className) {
            // Reset object state if needed
            if (method_exists($object, 'reset')) {
                $object->reset();
            }
            $this->pool[] = $object;
        }
    }
    
    public function clear(): void {
        $this->pool = [];
    }
    
    public function size(): int {
        return count($this->pool);
    }
}

// Memory-Efficient Cache
class MemoryEfficientCache {
    private array $cache = [];
    private int $maxSize;
    private array $accessOrder = [];
    
    public function __construct(int $maxSize = 1000) {
        $this->maxSize = $maxSize;
    }
    
    public function get(string $key): mixed {
        if (isset($this->cache[$key])) {
            // Update access order for LRU
            $this->updateAccessOrder($key);
            return $this->cache[$key];
        }
        return null;
    }
    
    public function set(string $key, mixed $value): void {
        // Remove oldest if cache is full
        if (count($this->cache) >= $this->maxSize && !isset($this->cache[$key])) {
            $oldestKey = array_shift($this->accessOrder);
            unset($this->cache[$oldestKey]);
        }
        
        $this->cache[$key] = $value;
        $this->updateAccessOrder($key);
    }
    
    public function has(string $key): bool {
        return isset($this->cache[$key]);
    }
    
    public function delete(string $key): bool {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
            $this->accessOrder = array_values(array_diff($this->accessOrder, [$key]));
            return true;
        }
        return false;
    }
    
    public function clear(): void {
        $this->cache = [];
        $this->accessOrder = [];
    }
    
    public function size(): int {
        return count($this->cache);
    }
    
    private function updateAccessOrder(string $key): void {
        $this->accessOrder = array_values(array_diff($this->accessOrder, [$key]));
        $this->accessOrder[] = $key;
    }
}

// Memory Usage Examples
class MemoryUsageExamples {
    private MemoryMonitor $monitor;
    
    public function __construct() {
        $this->monitor = new MemoryMonitor();
    }
    
    public function demonstrateMemoryLeaks(): void {
        echo "Memory Leak Demonstration\n";
        echo str_repeat("-", 30) . "\n";
        
        $this->monitor->snapshot('start');
        
        // Simulate memory leak
        $leakyArray = [];
        for ($i = 0; $i < 100000; $i++) {
            $leakyArray[] = str_repeat('x', 1000);
        }
        
        $this->monitor->snapshot('after_leak');
        
        // Clean up properly
        unset($leakyArray);
        gc_collect_cycles();
        
        $this->monitor->snapshot('after_cleanup');
        
        $diff = $this->monitor->getDifference('start', 'after_leak');
        $cleanupDiff = $this->monitor->getDifference('after_leak', 'after_cleanup');
        
        echo "Memory allocated: {$diff['memory_diff_mb']}MB\n";
        echo "Memory freed: " . abs($cleanupDiff['memory_diff_mb']) . "MB\n";
        echo "Current memory: " . round(memory_get_usage() / 1024 / 1024, 4) . "MB\n\n";
    }
    
    public function demonstrateGenerators(): void {
        echo "Generator vs Array Memory Usage\n";
        echo str_repeat("-", 35) . "\n";
        
        $this->monitor->snapshot('before_array');
        
        // Create large array (memory intensive)
        $largeArray = range(1, 100000);
        foreach ($largeArray as $number) {
            $result = $number * 2;
        }
        
        $this->monitor->snapshot('after_array');
        
        unset($largeArray);
        gc_collect_cycles();
        
        $this->monitor->snapshot('after_array_cleanup');
        
        // Use generator (memory efficient)
        $this->monitor->snapshot('before_generator');
        
        $generator = function() {
            for ($i = 1; $i <= 100000; $i++) {
                yield $i;
            }
        };
        
        foreach ($generator() as $number) {
            $result = $number * 2;
        }
        
        $this->monitor->snapshot('after_generator');
        
        $arrayDiff = $this->monitor->getDifference('before_array', 'after_array');
        $generatorDiff = $this->monitor->getDifference('before_generator', 'after_generator');
        
        echo "Array approach: {$arrayDiff['memory_diff_mb']}MB\n";
        echo "Generator approach: {$generatorDiff['memory_diff_mb']}MB\n";
        echo "Memory savings: " . round($arrayDiff['memory_diff_mb'] - $generatorDiff['memory_diff_mb'], 4) . "MB\n\n";
    }
    
    public function demonstrateObjectPooling(): void {
        echo "Object Pool Demonstration\n";
        echo str_repeat("-", 28) . "\n";
        
        class TestObject {
            public $data;
            
            public function __construct() {
                $this->data = str_repeat('x', 1000);
            }
            
            public function reset(): void {
                $this->data = str_repeat('x', 1000);
            }
        }
        
        $this->monitor->snapshot('before_pool_test');
        
        // Without object pool
        $objects = [];
        for ($i = 0; $i < 1000; $i++) {
            $objects[] = new TestObject();
        }
        
        $this->monitor->snapshot('without_pool');
        
        unset($objects);
        gc_collect_cycles();
        
        $this->monitor->snapshot('without_pool_cleanup');
        
        // With object pool
        $pool = new ObjectPool(TestObject::class, 100);
        
        for ($i = 0; $i < 1000; $i++) {
            $obj = $pool->get();
            $pool->release($obj);
        }
        
        $this->monitor->snapshot('with_pool');
        
        $withoutPoolDiff = $this->monitor->getDifference('before_pool_test', 'without_pool');
        $withPoolDiff = $this->monitor->getDifference('without_pool_cleanup', 'with_pool');
        
        echo "Without pool: {$withoutPoolDiff['memory_diff_mb']}MB\n";
        echo "With pool: {$withPoolDiff['memory_diff_mb']}MB\n";
        echo "Memory savings: " . round($withoutPoolDiff['memory_diff_mb'] - $withPoolDiff['memory_diff_mb'], 4) . "MB\n";
        echo "Pool size: {$pool->size()}\n\n";
    }
    
    public function demonstrateCaching(): void {
        echo "Memory-Efficient Caching\n";
        echo str_repeat("-", 28) . "\n";
        
        $this->monitor->snapshot('before_cache');
        
        $cache = new MemoryEfficientCache(1000);
        
        // Fill cache
        for ($i = 0; $i < 2000; $i++) {
            $cache->set("key_$i", str_repeat('data', 100));
        }
        
        $this->monitor->snapshot('cache_filled');
        
        // Access some items
        for ($i = 0; $i < 100; $i++) {
            $cache->get("key_" . rand(0, 1999));
        }
        
        $this->monitor->snapshot('cache_accessed');
        
        $cacheDiff = $this->monitor->getDifference('before_cache', 'cache_filled');
        echo "Cache memory usage: {$cacheDiff['memory_diff_mb']}MB\n";
        echo "Cache size: {$cache->size()} items\n";
        echo "Max size: 1000 items (LRU eviction)\n\n";
    }
    
    public function demonstrateStringOptimization(): void {
        echo "String Optimization Techniques\n";
        echo str_repeat("-", 32) . "\n";
        
        $this->monitor->snapshot('before_strings');
        
        // Inefficient string concatenation
        $result1 = '';
        for ($i = 0; $i < 10000; $i++) {
            $result1 .= "Item $i\n";
        }
        
        $this->monitor->snapshot('inefficient_concat');
        
        unset($result1);
        gc_collect_cycles();
        
        $this->monitor->snapshot('after_inefficient_cleanup');
        
        // Efficient string concatenation
        $strings = [];
        for ($i = 0; $i < 10000; $i++) {
            $strings[] = "Item $i\n";
        }
        $result2 = implode('', $strings);
        
        $this->monitor->snapshot('efficient_concat');
        
        $inefficientDiff = $this->monitor->getDifference('before_strings', 'inefficient_concat');
        $efficientDiff = $this->monitor->getDifference('after_inefficient_cleanup', 'efficient_concat');
        
        echo "Inefficient concatenation: {$inefficientDiff['memory_diff_mb']}MB\n";
        echo "Efficient concatenation: {$efficientDiff['memory_diff_mb']}MB\n";
        echo "Memory savings: " . round($inefficientDiff['memory_diff_mb'] - $efficientDiff['memory_diff_mb'], 4) . "MB\n\n";
    }
    
    public function runAllExamples(): void {
        echo "Memory Optimization Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateMemoryLeaks();
        $this->demonstrateGenerators();
        $this->demonstrateObjectPooling();
        $this->demonstrateCaching();
        $this->demonstrateStringOptimization();
        
        $this->monitor->printReport();
    }
}

// Memory Analyzer Tool
class MemoryAnalyzer {
    private array $variables = [];
    
    public function analyzeVariable(string $name, mixed $variable): void {
        $size = $this->getVariableSize($variable);
        
        $this->variables[$name] = [
            'type' => gettype($variable),
            'size' => $size,
            'size_mb' => round($size / 1024 / 1024, 4),
            'details' => $this->getVariableDetails($variable)
        ];
    }
    
    private function getVariableSize(mixed $variable): int {
        $startMemory = memory_get_usage();
        
        // Serialize to estimate size
        $serialized = serialize($variable);
        
        return strlen($serialized);
    }
    
    private function getVariableDetails(mixed $variable): array {
        $details = [];
        
        switch (gettype($variable)) {
            case 'array':
                $details['count'] = count($variable);
                $details['max_depth'] = $this->getArrayDepth($variable);
                break;
            case 'object':
                $details['class'] = get_class($variable);
                $details['properties'] = count(get_object_vars($variable));
                break;
            case 'string':
                $details['length'] = strlen($variable);
                break;
        }
        
        return $details;
    }
    
    private function getArrayDepth(array $array): int {
        $maxDepth = 1;
        
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->getArrayDepth($value) + 1;
                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }
        
        return $maxDepth;
    }
    
    public function getReport(): array {
        // Sort by size
        uasort($this->variables, function($a, $b) {
            return $b['size'] <=> $a['size'];
        });
        
        return $this->variables;
    }
    
    public function printReport(): void {
        $report = $this->getReport();
        
        echo "\nVariable Memory Analysis\n";
        echo str_repeat("-", 30) . "\n";
        
        foreach ($report as $name => $info) {
            echo "\n$name:\n";
            echo "  Type: {$info['type']}\n";
            echo "  Size: {$info['size_mb']}MB\n";
            
            foreach ($info['details'] as $key => $value) {
                echo "  $key: $value\n";
            }
        }
    }
}

// Memory Optimization Best Practices
function printMemoryOptimizationBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Memory Optimization Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Use Generators:\n";
    echo "   • For large datasets\n";
    echo "   • When processing files\n";
    echo "   • To avoid loading everything in memory\n\n";
    
    echo "2. Object Pooling:\n";
    echo "   • Reuse expensive objects\n";
    echo "   • Reduce garbage collection overhead\n";
    echo "   • For frequently created/destroyed objects\n\n";
    
    echo "3. Efficient Data Structures:\n";
    echo "   • Use appropriate data types\n";
    echo "   • Avoid unnecessary nesting\n";
    echo "   • Consider SplFixedArray for large numeric arrays\n\n";
    
    echo "4. String Operations:\n";
    echo "   • Use implode() for concatenation\n";
    echo "   • Avoid string concatenation in loops\n";
    echo "   • Use string functions efficiently\n\n";
    
    echo "5. Garbage Collection:\n";
    echo "   • Call gc_collect_cycles() when needed\n";
    echo "   • Unset large variables when done\n";
    echo "   • Break circular references\n\n";
    
    echo "6. Caching Strategies:\n";
    echo "   • Use LRU eviction\n";
    echo "   • Limit cache size\n";
    echo "   • Use memory-efficient storage\n\n";
    
    echo "7. Monitoring:\n";
    echo "   • Track memory usage\n";
    echo "   • Set memory limits\n";
    echo "   • Profile memory hotspots\n";
}

// Main execution
function runMemoryOptimizationDemo(): void {
    // Run memory usage examples
    $examples = new MemoryUsageExamples();
    $examples->runAllExamples();
    
    // Demonstrate memory analyzer
    echo "\nMemory Analyzer Demo\n";
    echo str_repeat("-", 25) . "\n";
    
    $analyzer = new MemoryAnalyzer();
    
    // Analyze different variable types
    $smallArray = range(1, 100);
    $largeArray = range(1, 10000);
    $largeString = str_repeat('x', 100000);
    $object = (object) ['name' => 'Test', 'data' => range(1, 1000)];
    
    $analyzer->analyzeVariable('small_array', $smallArray);
    $analyzer->analyzeVariable('large_array', $largeArray);
    $analyzer->analyzeVariable('large_string', $largeString);
    $analyzer->analyzeVariable('object', $object);
    
    $analyzer->printReport();
    
    // Print best practices
    printMemoryOptimizationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMemoryOptimizationDemo();
}
?>
