<?php
/**
 * Caching Strategies Implementation
 * 
 * This file demonstrates various caching strategies including:
 * - In-memory caching with APCu
 * - File-based caching
 * - Database result caching
 * - Cache invalidation strategies
 * - Performance monitoring
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

// APCu Cache Implementation
class ApcuCache implements CacheInterface {
    private string $prefix;
    
    public function __construct(string $prefix = '') {
        $this->prefix = $prefix;
        
        if (!extension_loaded('apcu')) {
            throw new RuntimeException('APCu extension is not loaded');
        }
    }
    
    private function getPrefixedKey(string $key): string {
        return $this->prefix . $key;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        $value = apcu_fetch($this->getPrefixedKey($key), $success);
        return $success ? $value : $default;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        return apcu_store($this->getPrefixedKey($key), $value, $ttl);
    }
    
    public function delete(string $key): bool {
        return apcu_delete($this->getPrefixedKey($key));
    }
    
    public function clear(): bool {
        return apcu_clear_cache();
    }
    
    public function has(string $key): bool {
        return apcu_exists($this->getPrefixedKey($key));
    }
    
    public function getMultiple(array $keys): array {
        $prefixedKeys = array_map([$this, 'getPrefixedKey'], $keys);
        $results = apcu_fetch($prefixedKeys);
        
        $return = [];
        foreach ($keys as $key) {
            $prefixedKey = $this->getPrefixedKey($key);
            $return[$key] = $results[$prefixedKey] ?? null;
        }
        
        return $return;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        $prefixedValues = [];
        foreach ($values as $key => $value) {
            $prefixedValues[$this->getPrefixedKey($key)] = $value;
        }
        
        return apcu_store($prefixedValues, null, $ttl);
    }
    
    public function deleteMultiple(array $keys): bool {
        $prefixedKeys = array_map([$this, 'getPrefixedKey'], $keys);
        $deleted = 0;
        
        foreach ($prefixedKeys as $key) {
            if (apcu_delete($key)) {
                $deleted++;
            }
        }
        
        return $deleted === count($keys);
    }
}

// File Cache Implementation
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
        
        // Check if expired
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
            'expires' => $ttl === 0 ? 0 : time() + $ttl,
            'created' => time()
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
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        return $results;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        $success = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }
    
    public function deleteMultiple(array $keys): bool {
        $success = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }
        return $success;
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

// Cache Decorator with Serialization
class SerializedCacheDecorator implements CacheInterface {
    private CacheInterface $cache;
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        $value = $this->cache->get($key, $default);
        return $value !== $default ? unserialize($value) : $default;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        return $this->cache->set($key, serialize($value), $ttl);
    }
    
    public function delete(string $key): bool {
        return $this->cache->delete($key);
    }
    
    public function clear(): bool {
        return $this->cache->clear();
    }
    
    public function has(string $key): bool {
        return $this->cache->has($key);
    }
    
    public function getMultiple(array $keys): array {
        $results = $this->cache->getMultiple($keys);
        return array_map(function($value) {
            return $value !== null ? unserialize($value) : null;
        }, $results);
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool {
        $serializedValues = array_map('serialize', $values);
        return $this->cache->setMultiple($serializedValues, $ttl);
    }
    
    public function deleteMultiple(array $keys): bool {
        return $this->cache->deleteMultiple($keys);
    }
}

// Cache Manager with Multiple Backends
class CacheManager {
    private array $caches = [];
    private string $defaultCache = 'default';
    
    public function addCache(string $name, CacheInterface $cache): void {
        $this->caches[$name] = $cache;
    }
    
    public function setDefault(string $name): void {
        if (!isset($this->caches[$name])) {
            throw new InvalidArgumentException("Cache '$name' not found");
        }
        $this->defaultCache = $name;
    }
    
    public function getCache(string $name = null): CacheInterface {
        $name = $name ?? $this->defaultCache;
        
        if (!isset($this->caches[$name])) {
            throw new InvalidArgumentException("Cache '$name' not found");
        }
        
        return $this->caches[$name];
    }
    
    public function get(string $key, mixed $default = null, string $cacheName = null): mixed {
        return $this->getCache($cacheName)->get($key, $default);
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600, string $cacheName = null): bool {
        return $this->getCache($cacheName)->set($key, $value, $ttl);
    }
    
    public function delete(string $key, string $cacheName = null): bool {
        return $this->getCache($cacheName)->delete($key);
    }
    
    public function clear(string $cacheName = null): bool {
        return $this->getCache($cacheName)->clear();
    }
}

// Database Query Cache
class DatabaseQueryCache {
    private CacheInterface $cache;
    private int $defaultTtl;
    
    public function __construct(CacheInterface $cache, int $defaultTtl = 3600) {
        $this->cache = $cache;
        $this->defaultTtl = $defaultTtl;
    }
    
    public function query(PDO $pdo, string $sql, array $params = [], int $ttl = null): array {
        $ttl = $ttl ?? $this->defaultTtl;
        $cacheKey = $this->generateCacheKey($sql, $params);
        
        // Try to get from cache
        $result = $this->cache->get($cacheKey);
        if ($result !== null) {
            echo "Cache HIT for query: $sql\n";
            return $result;
        }
        
        // Execute query
        echo "Cache MISS for query: $sql\n";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache result
        $this->cache->set($cacheKey, $result, $ttl);
        
        return $result;
    }
    
    public function invalidate(string $pattern = null): void {
        if ($pattern) {
            // In a real implementation, you'd need to track cache keys
            $this->cache->clear();
        } else {
            $this->cache->clear();
        }
    }
    
    private function generateCacheKey(string $sql, array $params): string {
        return 'query_' . md5($sql . serialize($params));
    }
}

// Performance Monitor
class CachePerformanceMonitor {
    private array $stats = [];
    private array $operations = [];
    
    public function recordOperation(string $operation, string $key, bool $hit = null, float $duration = 0): void {
        $this->operations[] = [
            'operation' => $operation,
            'key' => $key,
            'hit' => $hit,
            'duration' => $duration,
            'timestamp' => microtime(true)
        ];
        
        // Update stats
        if (!isset($this->stats[$operation])) {
            $this->stats[$operation] = [
                'count' => 0,
                'hits' => 0,
                'misses' => 0,
                'total_duration' => 0
            ];
        }
        
        $this->stats[$operation]['count']++;
        $this->stats[$operation]['total_duration'] += $duration;
        
        if ($hit !== null) {
            if ($hit) {
                $this->stats[$operation]['hits']++;
            } else {
                $this->stats[$operation]['misses']++;
            }
        }
    }
    
    public function getStats(): array {
        $stats = [];
        
        foreach ($this->stats as $operation => $data) {
            $stats[$operation] = [
                'count' => $data['count'],
                'hits' => $data['hits'],
                'misses' => $data['misses'],
                'hit_rate' => $data['hits'] + $data['misses'] > 0 ? 
                    ($data['hits'] / ($data['hits'] + $data['misses'])) * 100 : 0,
                'avg_duration' => $data['count'] > 0 ? $data['total_duration'] / $data['count'] : 0
            ];
        }
        
        return $stats;
    }
    
    public function getOperations(): array {
        return $this->operations;
    }
    
    public function reset(): void {
        $this->stats = [];
        $this->operations = [];
    }
}

// Usage Examples
echo "=== Caching Strategies Demo ===\n\n";

// Initialize cache manager
$cacheManager = new CacheManager();

// Add different cache backends
try {
    $fileCache = new FileCache('./cache');
    $cacheManager->addCache('file', $fileCache);
    $cacheManager->setDefault('file');
} catch (Exception $e) {
    echo "File cache error: " . $e->getMessage() . "\n";
}

// Add APCu if available
if (extension_loaded('apcu')) {
    try {
        $apcuCache = new ApcuCache('demo_');
        $cacheManager->addCache('apcu', $apcuCache);
    } catch (Exception $e) {
        echo "APCu cache error: " . $e->getMessage() . "\n";
    }
}

// Add serialized cache
$serializedCache = new SerializedCacheDecorator($fileCache);
$cacheManager->addCache('serialized', $serializedCache);

// Performance monitor
$monitor = new CachePerformanceMonitor();

// 1. Basic Cache Operations
echo "1. Basic Cache Operations:\n";
$startTime = microtime(true);
$cacheManager->set('user:1', ['name' => 'John', 'email' => 'john@example.com']);
$monitor->recordOperation('set', 'user:1', null, microtime(true) - $startTime);

$startTime = microtime(true);
$user = $cacheManager->get('user:1');
$monitor->recordOperation('get', 'user:1', true, microtime(true) - $startTime);

echo "User: " . json_encode($user) . "\n";

$startTime = microtime(true);
$nonExistent = $cacheManager->get('user:999', 'default');
$monitor->recordOperation('get', 'user:999', false, microtime(true) - $startTime);

echo "Non-existent user: " . $nonExistent . "\n\n";

// 2. Multiple Operations
echo "2. Multiple Operations:\n";
$multipleData = [
    'product:1' => ['name' => 'Laptop', 'price' => 999.99],
    'product:2' => ['name' => 'Mouse', 'price' => 29.99],
    'product:3' => ['name' => 'Keyboard', 'price' => 79.99]
];

$startTime = microtime(true);
$cacheManager->setMultiple($multipleData);
$monitor->recordOperation('setMultiple', 'products', null, microtime(true) - $startTime);

$startTime = microtime(true);
$products = $cacheManager->getMultiple(['product:1', 'product:2', 'product:3']);
$monitor->recordOperation('getMultiple', 'products', true, microtime(true) - $startTime);

echo "Products: " . json_encode($products) . "\n\n";

// 3. Serialized Cache
echo "3. Serialized Cache:\n";
$complexObject = (object) [
    'users' => ['John', 'Jane', 'Bob'],
    'settings' => ['theme' => 'dark', 'lang' => 'en'],
    'metadata' => ['version' => '1.0', 'created' => '2024-01-01']
];

$cacheManager->set('complex_data', $complexObject, 3600, 'serialized');
$retrievedObject = $cacheManager->get('complex_data', null, 'serialized');

echo "Complex object: " . json_encode($retrievedObject) . "\n\n";

// 4. Database Query Cache (simulation)
echo "4. Database Query Cache:\n";
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)');
    $pdo->exec("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");
    $pdo->exec("INSERT INTO users (name, email) VALUES ('Jane', 'jane@example.com')");
    
    $queryCache = new DatabaseQueryCache($fileCache);
    
    // First query (cache miss)
    $users1 = $queryCache->query($pdo, 'SELECT * FROM users');
    
    // Second query (cache hit)
    $users2 = $queryCache->query($pdo, 'SELECT * FROM users');
    
    echo "Users from database: " . json_encode($users1) . "\n";
    
} catch (Exception $e) {
    echo "Database simulation error: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Performance Statistics
echo "5. Performance Statistics:\n";
$stats = $monitor->getStats();
foreach ($stats as $operation => $data) {
    echo "$operation:\n";
    echo "  Count: {$data['count']}\n";
    echo "  Hit Rate: " . number_format($data['hit_rate'], 2) . "%\n";
    echo "  Avg Duration: " . number_format($data['avg_duration'] * 1000, 4) . "ms\n";
    echo "\n";
}

// 6. Cache Cleanup
echo "6. Cache Cleanup:\n";
if (method_exists($fileCache, 'cleanup')) {
    $cleaned = $fileCache->cleanup();
    echo "Cleaned up $cleaned expired cache files\n";
}

echo "\n=== Caching Strategies Demo Complete ===\n";
?>
