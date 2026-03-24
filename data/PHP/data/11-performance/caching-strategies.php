<?php
/**
 * Caching Strategies for Performance
 * 
 * This file demonstrates various caching strategies and implementations
 * to improve PHP application performance.
 */

// Cache Interface
interface CacheInterface {
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value, int $ttl = 3600): bool;
    public function delete(string $key): bool;
    public function clear(): bool;
    public function has(string $key): bool;
    public function getMultiple(array $keys): array;
    public function setMultiple(array $values, int $ttl = 3600): bool;
    public function deleteMultiple(array $keys): bool;
}

// In-Memory Cache Implementation
class InMemoryCache implements CacheInterface {
    private array $cache = [];
    private array $ttl = [];
    private int $maxSize;
    private array $accessOrder = [];
    
    public function __construct(int $maxSize = 1000) {
        $this->maxSize = $maxSize;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        if (!isset($this->cache[$key])) {
            return $default;
        }
        
        // Check TTL
        if (isset($this->ttl[$key]) && $this->ttl[$key] < time()) {
            $this->delete($key);
            return $default;
        }
        
        $this->updateAccessOrder($key);
        return $this->cache[$key];
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        // Remove oldest if cache is full
        if (count($this->cache) >= $this->maxSize && !isset($this->cache[$key])) {
            $oldestKey = array_shift($this->accessOrder);
            unset($this->cache[$oldestKey]);
            unset($this->ttl[$oldestKey]);
        }
        
        $this->cache[$key] = $value;
        if ($ttl > 0) {
            $this->ttl[$key] = time() + $ttl;
        }
        
        $this->updateAccessOrder($key);
        return true;
    }
    
    public function delete(string $key): bool {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
            unset($this->ttl[$key]);
            $this->accessOrder = array_values(array_diff($this->accessOrder, [$key]));
            return true;
        }
        return false;
    }
    
    public function clear(): bool {
        $this->cache = [];
        $this->ttl = [];
        $this->accessOrder = [];
        return true;
    }
    
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    public function getMultiple(array $keys): array {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }
    
    public function deleteMultiple(array $keys): bool {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
    
    private function updateAccessOrder(string $key): void {
        $this->accessOrder = array_values(array_diff($this->accessOrder, [$key]));
        $this->accessOrder[] = $key;
    }
    
    public function getStats(): array {
        return [
            'size' => count($this->cache),
            'max_size' => $this->maxSize,
            'hit_rate' => $this->calculateHitRate(),
            'memory_usage' => memory_get_usage()
        ];
    }
    
    private function calculateHitRate(): float {
        // Simplified hit rate calculation
        return 0.0; // Would need to track hits/misses
    }
}

// File-Based Cache
class FileCache implements CacheInterface {
    private string $cacheDir;
    private string $extension;
    
    public function __construct(string $cacheDir = './cache', string $extension = '.cache') {
        $this->cacheDir = rtrim($cacheDir, '/\\');
        $this->extension = $extension;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    private function getFilePath(string $key): string {
        $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        return $this->cacheDir . DIRECTORY_SEPARATOR . $safeKey . $this->extension;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            return $default;
        }
        
        $data = unserialize($content);
        
        // Check TTL
        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        $filePath = $this->getFilePath($key);
        $data = [
            'value' => $value,
            'created' => time(),
            'expires' => $ttl === 0 ? 0 : time() + $ttl
        ];
        
        $result = file_put_contents($filePath, serialize($data));
        return $result !== false;
    }
    
    public function delete(string $key): bool {
        $filePath = $this->getFilePath($key);
        return file_exists($filePath) && unlink($filePath);
    }
    
    public function clear(): bool {
        $files = glob($this->cacheDir . '/*' . $this->extension);
        $deleted = 0;
        
        foreach ($files as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }
        
        return $deleted > 0;
    }
    
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    public function getMultiple(array $keys): array {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }
    
    public function deleteMultiple(array $keys): bool {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
    
    public function cleanup(): int {
        $files = glob($this->cacheDir . '/*' . $this->extension);
        $cleaned = 0;
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content !== false) {
                $data = unserialize($content);
                if ($data['expires'] !== 0 && $data['expires'] < time()) {
                    if (unlink($file)) {
                        $cleaned++;
                    }
                }
            }
        }
        
        return $cleaned;
    }
}

// Multi-Level Cache (L1: Memory, L2: File)
class MultiLevelCache implements CacheInterface {
    private CacheInterface $l1Cache; // Memory cache
    private CacheInterface $l2Cache; // File cache
    
    public function __construct(CacheInterface $l1Cache, CacheInterface $l2Cache) {
        $this->l1Cache = $l1Cache;
        $this->l2Cache = $l2Cache;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        // Try L1 cache first
        $value = $this->l1Cache->get($key);
        if ($value !== null) {
            return $value;
        }
        
        // Try L2 cache
        $value = $this->l2Cache->get($key);
        if ($value !== null) {
            // Promote to L1 cache
            $this->l1Cache->set($key, $value);
            return $value;
        }
        
        return $default;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        $l1Result = $this->l1Cache->set($key, $value, $ttl);
        $l2Result = $this->l2Cache->set($key, $value, $ttl);
        
        return $l1Result && $l2Result;
    }
    
    public function delete(string $key): bool {
        $l1Result = $this->l1Cache->delete($key);
        $l2Result = $this->l2Cache->delete($key);
        
        return $l1Result || $l2Result;
    }
    
    public function clear(): bool {
        $l1Result = $this->l1Cache->clear();
        $l2Result = $this->l2Cache->clear();
        
        return $l1Result && $l2Result;
    }
    
    public function has(string $key): bool {
        return $this->l1Cache->has($key) || $this->l2Cache->has($key);
    }
    
    public function getMultiple(array $keys): array {
        $result = [];
        
        // Try L1 cache first
        $l1Results = $this->l1Cache->getMultiple($keys);
        $missingKeys = [];
        
        foreach ($keys as $key) {
            if ($l1Results[$key] !== null) {
                $result[$key] = $l1Results[$key];
            } else {
                $missingKeys[] = $key;
            }
        }
        
        // Try L2 cache for missing keys
        if (!empty($missingKeys)) {
            $l2Results = $this->l2Cache->getMultiple($missingKeys);
            foreach ($l2Results as $key => $value) {
                if ($value !== null) {
                    $result[$key] = $value;
                    // Promote to L1 cache
                    $this->l1Cache->set($key, $value);
                }
            }
        }
        
        return $result;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        $l1Result = $this->l1Cache->setMultiple($values, $ttl);
        $l2Result = $this->l2Cache->setMultiple($values, $ttl);
        
        return $l1Result && $l2Result;
    }
    
    public function deleteMultiple(array $keys): bool {
        $l1Result = $this->l1Cache->deleteMultiple($keys);
        $l2Result = $this->l2Cache->deleteMultiple($keys);
        
        return $l1Result || $l2Result;
    }
    
    public function getStats(): array {
        return [
            'l1_stats' => $this->l1Cache->getStats(),
            'l2_stats' => method_exists($this->l2Cache, 'getStats') ? $this->l2Cache->getStats() : []
        ];
    }
}

// Cache with Tags
class TaggedCache implements CacheInterface {
    private CacheInterface $cache;
    private array $tags;
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
        $this->tags = [];
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return $this->cache->get($key, $default);
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        return $this->cache->set($key, $value, $ttl);
    }
    
    public function setWithTags(string $key, mixed $value, array $tags, int $ttl = 3600): bool {
        // Store the value
        $result = $this->cache->set($key, $value, $ttl);
        
        // Store tag associations
        foreach ($tags as $tag) {
            $taggedKeys = $this->cache->get("tag:$tag", []);
            if (!in_array($key, $taggedKeys)) {
                $taggedKeys[] = $key;
                $this->cache->set("tag:$tag", $taggedKeys, $ttl);
            }
        }
        
        // Store tags for the key
        $this->cache->set("tags:$key", $tags, $ttl);
        
        return $result;
    }
    
    public function delete(string $key): bool {
        // Get tags for the key
        $tags = $this->cache->get("tags:$key", []);
        
        // Remove key from all tag lists
        foreach ($tags as $tag) {
            $taggedKeys = $this->cache->get("tag:$tag", []);
            $taggedKeys = array_values(array_diff($taggedKeys, [$key]));
            $this->cache->set("tag:$tag", $taggedKeys);
        }
        
        // Delete the key and its tags
        $this->cache->delete("tags:$key");
        return $this->cache->delete($key);
    }
    
    public function clear(): bool {
        return $this->cache->clear();
    }
    
    public function clearTag(string $tag): bool {
        $taggedKeys = $this->cache->get("tag:$tag", []);
        
        foreach ($taggedKeys as $key) {
            $this->cache->delete($key);
            $this->cache->delete("tags:$key");
        }
        
        return $this->cache->delete("tag:$tag");
    }
    
    public function has(string $key): bool {
        return $this->cache->has($key);
    }
    
    public function getMultiple(array $keys): array {
        return $this->cache->getMultiple($keys);
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        return $this->cache->setMultiple($values, $ttl);
    }
    
    public function deleteMultiple(array $keys): bool {
        return $this->cache->deleteMultiple($keys);
    }
}

// Cache Decorator with Statistics
class CacheStatsDecorator implements CacheInterface {
    private CacheInterface $cache;
    private array $stats = [
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
        'clears' => 0
    ];
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        $value = $this->cache->get($key, $default);
        
        if ($value !== $default) {
            $this->stats['hits']++;
        } else {
            $this->stats['misses']++;
        }
        
        return $value;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        $this->stats['sets']++;
        return $this->cache->set($key, $value, $ttl);
    }
    
    public function delete(string $key): bool {
        $this->stats['deletes']++;
        return $this->cache->delete($key);
    }
    
    public function clear(): bool {
        $this->stats['clears']++;
        return $this->cache->clear();
    }
    
    public function has(string $key): bool {
        return $this->cache->has($key);
    }
    
    public function getMultiple(array $keys): array {
        return $this->cache->getMultiple($keys);
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        $this->stats['sets'] += count($values);
        return $this->cache->setMultiple($values, $ttl);
    }
    
    public function deleteMultiple(array $keys): bool {
        $this->stats['deletes'] += count($keys);
        return $this->cache->deleteMultiple($keys);
    }
    
    public function getStats(): array {
        $total = $this->stats['hits'] + $this->stats['misses'];
        $hitRate = $total > 0 ? ($this->stats['hits'] / $total) * 100 : 0;
        
        return array_merge($this->stats, [
            'hit_rate' => round($hitRate, 2),
            'total_requests' => $total
        ]);
    }
    
    public function resetStats(): void {
        $this->stats = [
            'hits' => 0,
            'misses' => 0,
            'sets' => 0,
            'deletes' => 0,
            'clears' => 0
        ];
    }
}

// Cache Warmer
class CacheWarmer {
    private CacheInterface $cache;
    private array $warmers = [];
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function addWarmer(string $key, callable $warmer, array $tags = [], int $ttl = 3600): void {
        $this->warmers[$key] = [
            'callback' => $warmer,
            'tags' => $tags,
            'ttl' => $ttl
        ];
    }
    
    public function warm(string $key): bool {
        if (!isset($this->warmers[$key])) {
            return false;
        }
        
        $warmer = $this->warmers[$key];
        $value = ($warmer['callback'])();
        
        if ($this->cache instanceof TaggedCache) {
            return $this->cache->setWithTags($key, $value, $warmer['tags'], $warmer['ttl']);
        } else {
            return $this->cache->set($key, $value, $warmer['ttl']);
        }
    }
    
    public function warmAll(): array {
        $results = [];
        
        foreach ($this->warmers as $key => $warmer) {
            $results[$key] = $this->warm($key);
        }
        
        return $results;
    }
    
    public function scheduleWarm(string $key): void {
        // In a real implementation, this would use a job queue
        // For demo purposes, we'll just warm it immediately
        $this->warm($key);
    }
}

// Caching Examples
class CachingExamples {
    private CacheInterface $cache;
    
    public function __construct() {
        $this->cache = new CacheStatsDecorator(
            new MultiLevelCache(
                new InMemoryCache(100),
                new FileCache('./cache')
            )
        );
    }
    
    public function demonstrateBasicCaching(): void {
        echo "Basic Caching Demonstration\n";
        echo str_repeat("-", 35) . "\n";
        
        // Set some values
        $this->cache->set('user:1', ['id' => 1, 'name' => 'John Doe']);
        $this->cache->set('user:2', ['id' => 2, 'name' => 'Jane Smith']);
        
        // Get values
        $user1 = $this->cache->get('user:1');
        $user2 = $this->cache->get('user:2');
        $user3 = $this->cache->get('user:3', 'default');
        
        echo "User 1: " . json_encode($user1) . "\n";
        echo "User 2: " . json_encode($user2) . "\n";
        echo "User 3: $user3\n";
        
        // Check existence
        echo "User 1 exists: " . ($this->cache->has('user:1') ? 'Yes' : 'No') . "\n";
        echo "User 3 exists: " . ($this->cache->has('user:3') ? 'Yes' : 'No') . "\n";
        
        // Delete
        $this->cache->delete('user:1');
        echo "User 1 after delete: " . json_encode($this->cache->get('user:1', 'not found')) . "\n";
        
        // Multiple operations
        $this->cache->setMultiple([
            'config:app_name' => 'MyApp',
            'config:version' => '1.0.0',
            'config:debug' => true
        ]);
        
        $configs = $this->cache->getMultiple(['config:app_name', 'config:version', 'config:debug']);
        echo "Configs: " . json_encode($configs) . "\n";
    }
    
    public function demonstrateTaggedCaching(): void {
        echo "\nTagged Caching Demonstration\n";
        echo str_repeat("-", 35) . "\n";
        
        $taggedCache = new TaggedCache($this->cache);
        
        // Set values with tags
        $taggedCache->setWithTags('user:1', ['id' => 1, 'name' => 'John'], ['users', 'active']);
        $taggedCache->setWithTags('user:2', ['id' => 2, 'name' => 'Jane'], ['users', 'active']);
        $taggedCache->setWithTags('post:1', ['id' => 1, 'title' => 'Hello'], ['posts', 'published']);
        
        echo "User 1: " . json_encode($taggedCache->get('user:1')) . "\n";
        echo "Post 1: " . json_encode($taggedCache->get('post:1')) . "\n";
        
        // Clear by tag
        $taggedCache->clearTag('users');
        
        echo "User 1 after clearing 'users' tag: " . json_encode($taggedCache->get('user:1', 'not found')) . "\n";
        echo "Post 1 after clearing 'users' tag: " . json_encode($taggedCache->get('post:1')) . "\n";
        
        $taggedCache->clearTag('posts');
        echo "Post 1 after clearing 'posts' tag: " . json_encode($taggedCache->get('post:1', 'not found')) . "\n";
    }
    
    public function demonstrateCacheWarming(): void {
        echo "\nCache Warming Demonstration\n";
        echo str_repeat("-", 35) . "\n";
        
        $warmer = new CacheWarmer($this->cache);
        
        // Add warmers
        $warmer->addWarmer('stats:users', function() {
            return ['total' => 1000, 'active' => 850, 'new_today' => 25];
        }, ['stats'], 300);
        
        $warmer->addWarmer('stats:posts', function() {
            return ['total' => 5000, 'published' => 4500, 'drafts' => 500];
        }, ['stats'], 300);
        
        // Warm specific cache
        $warmer->warm('stats:users');
        echo "Users stats: " . json_encode($this->cache->get('stats:users')) . "\n";
        
        // Warm all
        $results = $warmer->warmAll();
        echo "Warm all results: " . json_encode($results) . "\n";
        
        echo "Posts stats: " . json_encode($this->cache->get('stats:posts')) . "\n";
    }
    
    public function demonstratePerformance(): void {
        echo "\nPerformance Demonstration\n";
        echo str_repeat("-", 30) . "\n";
        
        // Simulate expensive operation
        $expensiveOperation = function($id) {
            usleep(10000); // 10ms delay
            return ['id' => $id, 'data' => "Expensive data for $id"];
        };
        
        $iterations = 100;
        
        // Without cache
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $result = $expensiveOperation($i % 10);
        }
        $withoutCache = microtime(true) - $startTime;
        
        // With cache
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $key = "data:" . ($i % 10);
            $result = $this->cache->get($key);
            if ($result === null) {
                $result = $expensiveOperation($i % 10);
                $this->cache->set($key, $result);
            }
        }
        $withCache = microtime(true) - $startTime;
        
        echo "Without cache: " . round($withoutCache * 1000, 4) . "ms\n";
        echo "With cache: " . round($withCache * 1000, 4) . "ms\n";
        echo "Speedup: " . round($withoutCache / $withCache, 2) . "x faster\n";
    }
    
    public function showCacheStats(): void {
        echo "\nCache Statistics\n";
        echo str_repeat("-", 20) . "\n";
        
        if ($this->cache instanceof CacheStatsDecorator) {
            $stats = $this->cache->getStats();
            
            echo "Hits: {$stats['hits']}\n";
            echo "Misses: {$stats['misses']}\n";
            echo "Sets: {$stats['sets']}\n";
            echo "Deletes: {$stats['deletes']}\n";
            echo "Hit Rate: {$stats['hit_rate']}%\n";
            echo "Total Requests: {$stats['total_requests']}\n";
        }
        
        if ($this->cache instanceof MultiLevelCache) {
            $multiStats = $this->cache->getStats();
            echo "\nMulti-Level Cache Stats:\n";
            echo "L1 (Memory): " . json_encode($multiStats['l1_stats']) . "\n";
            echo "L2 (File): " . json_encode($multiStats['l2_stats']) . "\n";
        }
    }
    
    public function runAllExamples(): void {
        echo "Caching Strategies Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateBasicCaching();
        $this->demonstrateTaggedCaching();
        $this->demonstrateCacheWarming();
        $this->demonstratePerformance();
        $this->showCacheStats();
    }
}

// Caching Best Practices
function printCachingBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Caching Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Cache Strategy:\n";
    echo "   • Cache frequently accessed data\n";
    echo "   • Use appropriate TTL values\n";
    echo "   • Implement cache invalidation\n";
    echo "   • Consider cache warming\n\n";
    
    echo "2. Cache Layers:\n";
    echo "   • Use multi-level caching\n";
    echo "   • Memory cache for hot data\n";
    echo "   • File/database cache for persistence\n";
    echo "   • CDN for static content\n\n";
    
    echo "3. Cache Keys:\n";
    echo "   • Use descriptive, consistent keys\n";
    echo "   • Include versioning in keys\n";
    echo "   • Avoid key collisions\n";
    echo "   • Use hierarchical naming\n\n";
    
    echo "4. Cache Invalidation:\n";
    echo "   • Time-based expiration\n";
    echo "   • Event-based invalidation\n";
    echo "   • Tag-based invalidation\n";
    echo "   • Manual invalidation\n\n";
    
    echo "5. Performance:\n";
    echo "   • Monitor cache hit rates\n";
    echo "   • Optimize cache size\n";
    echo "   • Use compression for large values\n";
    echo "   • Consider distributed caching\n\n";
    
    echo "6. Security:\n";
    echo "   • Validate cached data\n";
    echo "   • Sanitize cache keys\n";
    echo "   • Consider encryption for sensitive data\n";
    echo "   • Implement access controls\n";
}

// Main execution
function runCachingDemo(): void {
    $examples = new CachingExamples();
    $examples->runAllExamples();
    printCachingBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCachingDemo();
}
?>
