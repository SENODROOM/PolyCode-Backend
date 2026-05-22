<?php
/**
 * Database Performance Optimization
 * 
 * This file demonstrates database performance optimization techniques
 * including query optimization, connection management, and caching.
 */

// Database Performance Monitor
class DatabasePerformanceMonitor {
    private array $queries = [];
    private array $slowQueries = [];
    private float $slowQueryThreshold = 0.1; // 100ms
    
    public function recordQuery(string $sql, float $duration, int $rows, array $params = []): void {
        $query = [
            'sql' => $sql,
            'duration' => $duration,
            'duration_ms' => round($duration * 1000, 4),
            'rows' => $rows,
            'params' => $params,
            'timestamp' => microtime(true)
        ];
        
        $this->queries[] = $query;
        
        if ($duration > $this->slowQueryThreshold) {
            $this->slowQueries[] = $query;
        }
    }
    
    public function getStats(): array {
        $totalQueries = count($this->queries);
        $totalDuration = array_sum(array_column($this->queries, 'duration'));
        $totalRows = array_sum(array_column($this->queries, 'rows'));
        
        return [
            'total_queries' => $totalQueries,
            'total_duration' => $totalDuration,
            'total_duration_ms' => round($totalDuration * 1000, 4),
            'avg_duration' => $totalQueries > 0 ? $totalDuration / $totalQueries : 0,
            'avg_duration_ms' => $totalQueries > 0 ? round(($totalDuration / $totalQueries) * 1000, 4) : 0,
            'total_rows' => $totalRows,
            'slow_queries' => count($this->slowQueries),
            'slow_query_percentage' => $totalQueries > 0 ? (count($this->slowQueries) / $totalQueries) * 100 : 0
        ];
    }
    
    public function getSlowQueries(): array {
        return $this->slowQueries;
    }
    
    public function getQueries(): array {
        return $this->queries;
    }
    
    public function reset(): void {
        $this->queries = [];
        $this->slowQueries = [];
    }
    
    public function setSlowQueryThreshold(float $threshold): void {
        $this->slowQueryThreshold = $threshold;
    }
}

// Optimized Database Connection Pool
class DatabaseConnectionPool {
    private array $connections = [];
    private array $available = [];
    private array $inUse = [];
    private string $dsn;
    private string $username;
    private string $password;
    private int $maxConnections;
    private int $createdConnections = 0;
    
    public function __construct(string $dsn, string $username, string $password, int $maxConnections = 10) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->maxConnections = $maxConnections;
    }
    
    public function getConnection(): PDO {
        if (!empty($this->available)) {
            $connectionId = array_pop($this->available);
            $this->inUse[$connectionId] = true;
            return $this->connections[$connectionId];
        }
        
        if ($this->createdConnections < $this->maxConnections) {
            $connection = $this->createConnection();
            $connectionId = $this->createdConnections++;
            $this->connections[$connectionId] = $connection;
            $this->inUse[$connectionId] = true;
            return $connection;
        }
        
        throw new RuntimeException('No available database connections');
    }
    
    public function releaseConnection(PDO $connection): void {
        foreach ($this->connections as $id => $conn) {
            if ($conn === $connection) {
                unset($this->inUse[$id]);
                $this->available[] = $id;
                return;
            }
        }
        
        throw new RuntimeException('Connection not found in pool');
    }
    
    private function createConnection(): PDO {
        $pdo = new PDO($this->dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'",
            PDO::ATTR_PERSISTENT => true
        ]);
        
        return $pdo;
    }
    
    public function getStats(): array {
        return [
            'total_connections' => $this->createdConnections,
            'available_connections' => count($this->available),
            'in_use_connections' => count($this->inUse),
            'max_connections' => $this->maxConnections
        ];
    }
    
    public function closeAll(): void {
        foreach ($this->connections as $connection) {
            $connection = null;
        }
        $this->connections = [];
        $this->available = [];
        $this->inUse = [];
        $this->createdConnections = 0;
    }
}

// Query Builder with Optimization
class OptimizedQueryBuilder {
    private string $table;
    private array $select = [];
    private array $where = [];
    private array $joins = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $bindings = [];
    
    public function __construct(string $table) {
        $this->table = $table;
    }
    
    public function select(array $columns): self {
        $this->select = $columns;
        return $this;
    }
    
    public function where(string $condition, mixed $value = null): self {
        $this->where[] = $condition;
        if ($value !== null) {
            $this->bindings[] = $value;
        }
        return $this;
    }
    
    public function join(string $table, string $condition): self {
        $this->joins[] = "JOIN $table ON $condition";
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit(int $limit, int $offset = null): self {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    
    public function getSQL(): string {
        $sql = "SELECT " . (empty($this->select) ? "*" : implode(', ', $this->select));
        $sql .= " FROM $this->table";
        
        foreach ($this->joins as $join) {
            $sql .= " $join";
        }
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT $this->limit";
            if ($this->offset !== null) {
                $sql .= " OFFSET $this->offset";
            }
        }
        
        return $sql;
    }
    
    public function getBindings(): array {
        return $this->bindings;
    }
}

// Database Cache Layer
class DatabaseCache {
    private array $cache = [];
    private int $maxSize;
    private array $accessOrder = [];
    private int $ttl; // Time to live in seconds
    
    public function __construct(int $maxSize = 1000, int $ttl = 300) {
        $this->maxSize = $maxSize;
        $this->ttl = $ttl;
    }
    
    public function get(string $key): mixed {
        if (isset($this->cache[$key])) {
            $item = $this->cache[$key];
            
            // Check if expired
            if ($item['expires'] < time()) {
                $this->delete($key);
                return null;
            }
            
            $this->updateAccessOrder($key);
            return $item['data'];
        }
        
        return null;
    }
    
    public function set(string $key, mixed $data): void {
        // Remove expired items
        $this->cleanupExpired();
        
        // Remove oldest if cache is full
        if (count($this->cache) >= $this->maxSize && !isset($this->cache[$key])) {
            $oldestKey = array_shift($this->accessOrder);
            unset($this->cache[$oldestKey]);
        }
        
        $this->cache[$key] = [
            'data' => $data,
            'created' => time(),
            'expires' => time() + $this->ttl
        ];
        
        $this->updateAccessOrder($key);
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
    
    private function updateAccessOrder(string $key): void {
        $this->accessOrder = array_values(array_diff($this->accessOrder, [$key]));
        $this->accessOrder[] = $key;
    }
    
    private function cleanupExpired(): void {
        $now = time();
        foreach ($this->cache as $key => $item) {
            if ($item['expires'] < $now) {
                $this->delete($key);
            }
        }
    }
    
    public function getStats(): array {
        $this->cleanupExpired();
        return [
            'size' => count($this->cache),
            'max_size' => $this->maxSize,
            'ttl' => $this->ttl
        ];
    }
}

// Optimized Database Repository
class OptimizedUserRepository {
    private PDO $pdo;
    private DatabasePerformanceMonitor $monitor;
    private DatabaseCache $cache;
    
    public function __construct(PDO $pdo, DatabasePerformanceMonitor $monitor, DatabaseCache $cache) {
        $this->pdo = $pdo;
        $this->monitor = $monitor;
        $this->cache = $cache;
    }
    
    public function findById(int $id): ?array {
        $cacheKey = "user_$id";
        
        // Try cache first
        $user = $this->cache->get($cacheKey);
        if ($user !== null) {
            return $user;
        }
        
        // Query database
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = ?";
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, $user ? 1 : 0, [$id]);
        
        if ($user) {
            $this->cache->set($cacheKey, $user);
        }
        
        return $user ?: null;
    }
    
    public function findByEmail(string $email): ?array {
        $cacheKey = "user_email_" . md5($email);
        
        // Try cache first
        $user = $this->cache->get($cacheKey);
        if ($user !== null) {
            return $user;
        }
        
        // Query database with index hint
        $sql = "SELECT id, name, email, created_at FROM users USE INDEX (idx_email) WHERE email = ?";
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, $user ? 1 : 0, [$email]);
        
        if ($user) {
            $this->cache->set($cacheKey, $user);
        }
        
        return $user ?: null;
    }
    
    public function findAll(int $limit = 100, int $offset = 0): array {
        $cacheKey = "users_all_$limit" . "_$offset";
        
        // Try cache first
        $users = $this->cache->get($cacheKey);
        if ($users !== null) {
            return $users;
        }
        
        // Use optimized query builder
        $builder = new OptimizedQueryBuilder('users');
        $sql = $builder
            ->select(['id', 'name', 'email', 'created_at'])
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->getSQL();
        
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, count($users));
        
        $this->cache->set($cacheKey, $users);
        
        return $users;
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)";
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
        
        $id = (int)$this->pdo->lastInsertId();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, 1);
        
        // Clear relevant cache entries
        $this->cache->clear();
        
        return $id;
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        foreach (['name', 'email'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($values);
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, $stmt->rowCount(), $values);
        
        // Clear relevant cache entries
        $this->cache->delete("user_$id");
        $this->cache->clear();
        
        return $result;
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM users WHERE id = ?";
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$id]);
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, $stmt->rowCount(), [$id]);
        
        // Clear relevant cache entries
        $this->cache->delete("user_$id");
        $this->cache->clear();
        
        return $result;
    }
    
    public function search(string $term, int $limit = 50): array {
        $cacheKey = "user_search_" . md5($term) . "_$limit";
        
        // Try cache first
        $users = $this->cache->get($cacheKey);
        if ($users !== null) {
            return $users;
        }
        
        // Use full-text search if available, otherwise LIKE
        $sql = "SELECT id, name, email, created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY name LIMIT ?";
        $searchTerm = "%$term%";
        
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        $users = $stmt->fetchAll();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, count($users), [$searchTerm, $searchTerm, $limit]);
        
        $this->cache->set($cacheKey, $users);
        
        return $users;
    }
}

// Batch Operations for Performance
class BatchOperations {
    private PDO $pdo;
    private DatabasePerformanceMonitor $monitor;
    
    public function __construct(PDO $pdo, DatabasePerformanceMonitor $monitor) {
        $this->pdo = $pdo;
        $this->monitor = $monitor;
    }
    
    public function batchInsert(string $table, array $data, int $batchSize = 1000): int {
        if (empty($data)) {
            return 0;
        }
        
        $columns = array_keys($data[0]);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)";
        
        $this->pdo->beginTransaction();
        $totalInserted = 0;
        
        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach (array_chunk($data, $batchSize) as $batch) {
                $startTime = microtime(true);
                
                foreach ($batch as $row) {
                    $stmt->execute(array_values($row));
                    $totalInserted++;
                }
                
                $duration = microtime(true) - $startTime;
                $this->monitor->recordQuery($sql, $duration, count($batch));
            }
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
        
        return $totalInserted;
    }
    
    public function batchUpdate(string $table, array $updates, string $keyField = 'id'): int {
        if (empty($updates)) {
            return 0;
        }
        
        $this->pdo->beginTransaction();
        $totalUpdated = 0;
        
        try {
            foreach ($updates as $update) {
                $key = $update[$keyField];
                unset($update[$keyField]);
                
                $fields = [];
                $values = [];
                
                foreach ($update as $field => $value) {
                    $fields[] = "$field = ?";
                    $values[] = $value;
                }
                
                $values[] = $key;
                $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $keyField = ?";
                
                $startTime = microtime(true);
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($values);
                $totalUpdated += $stmt->rowCount();
                
                $duration = microtime(true) - $startTime;
                $this->monitor->recordQuery($sql, $duration, $stmt->rowCount(), $values);
            }
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
        
        return $totalUpdated;
    }
    
    public function batchDelete(string $table, array $ids, string $keyField = 'id'): int {
        if (empty($ids)) {
            return 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM $table WHERE $keyField IN ($placeholders)";
        
        $startTime = microtime(true);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        $deleted = $stmt->rowCount();
        
        $duration = microtime(true) - $startTime;
        $this->monitor->recordQuery($sql, $duration, $deleted, $ids);
        
        return $deleted;
    }
}

// Database Performance Examples
class DatabasePerformanceExamples {
    private DatabasePerformanceMonitor $monitor;
    private DatabaseCache $cache;
    
    public function __construct() {
        $this->monitor = new DatabasePerformanceMonitor();
        $this->cache = new DatabaseCache(100, 300); // 100 items, 5 minutes TTL
    }
    
    public function demonstrateQueryOptimization(): void {
        echo "Query Optimization Demonstration\n";
        echo str_repeat("-", 40) . "\n";
        
        // Create in-memory SQLite database
        $pdo = new PDO('sqlite::memory:');
        
        // Create table with indexes
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("CREATE INDEX idx_email ON users(email)");
        $pdo->exec("CREATE INDEX idx_name ON users(name)");
        
        // Insert sample data
        $batchOps = new BatchOperations($pdo, $this->monitor);
        $users = [];
        
        for ($i = 1; $i <= 1000; $i++) {
            $users[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $batchOps->batchInsert('users', $users);
        
        $repository = new OptimizedUserRepository($pdo, $this->monitor, $this->cache);
        
        // Test individual queries
        $user1 = $repository->findById(1);
        $user2 = $repository->findById(1); // Should hit cache
        $user3 = $repository->findByEmail('user500@example.com');
        $user4 = $repository->findByEmail('user500@example.com'); // Should hit cache
        
        $allUsers = $repository->findAll(10, 0);
        $searchResults = $repository->search('User 100');
        
        // Show performance stats
        $stats = $this->monitor->getStats();
        echo "Total queries: {$stats['total_queries']}\n";
        echo "Total duration: {$stats['total_duration_ms']}ms\n";
        echo "Average duration: {$stats['avg_duration_ms']}ms\n";
        echo "Total rows: {$stats['total_rows']}\n";
        echo "Slow queries: {$stats['slow_queries']}\n";
        echo "Slow query percentage: " . number_format($stats['slow_query_percentage'], 2) . "%\n";
        
        echo "\nCache stats:\n";
        $cacheStats = $this->cache->getStats();
        echo "Cache size: {$cacheStats['size']}\n";
        echo "Cache hits: ~" . (count($allUsers) > 0 ? '2' : '0') . " (estimated)\n";
    }
    
    public function demonstrateBatchOperations(): void {
        echo "\nBatch Operations Demonstration\n";
        echo str_repeat("-", 35) . "\n";
        
        $pdo = new PDO('sqlite::memory:');
        
        $pdo->exec("
            CREATE TABLE products (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                category TEXT NOT NULL
            )
        ");
        
        $batchOps = new BatchOperations($pdo, $this->monitor);
        
        // Batch insert
        $products = [];
        for ($i = 1; $i <= 5000; $i++) {
            $products[] = [
                'name' => "Product $i",
                'price' => rand(10, 1000) / 100,
                'category' => ['Electronics', 'Books', 'Clothing'][rand(0, 2)]
            ];
        }
        
        $startTime = microtime(true);
        $inserted = $batchOps->batchInsert('products', $products, 1000);
        $insertDuration = microtime(true) - $startTime;
        
        echo "Batch inserted $inserted products in " . round($insertDuration * 1000, 4) . "ms\n";
        
        // Batch update
        $updates = [];
        for ($i = 1; $i <= 100; $i++) {
            $updates[] = ['id' => $i, 'price' => rand(50, 500) / 100];
        }
        
        $startTime = microtime(true);
        $updated = $batchOps->batchUpdate('products', $updates);
        $updateDuration = microtime(true) - $startTime;
        
        echo "Batch updated $updated products in " . round($updateDuration * 1000, 4) . "ms\n";
        
        // Batch delete
        $idsToDelete = range(1, 50);
        
        $startTime = microtime(true);
        $deleted = $batchOps->batchDelete('products', $idsToDelete);
        $deleteDuration = microtime(true) - $startTime;
        
        echo "Batch deleted $deleted products in " . round($deleteDuration * 1000, 4) . "ms\n";
    }
    
    public function demonstrateConnectionPooling(): void {
        echo "\nConnection Pool Demonstration\n";
        echo str_repeat("-", 30) . "\n";
        
        try {
            $pool = new DatabaseConnectionPool('sqlite::memory:', '', '', 5);
            
            // Simulate concurrent operations
            $connections = [];
            for ($i = 0; $i < 3; $i++) {
                $connections[] = $pool->getConnection();
            }
            
            $stats = $pool->getStats();
            echo "In-use connections: {$stats['in_use_connections']}\n";
            echo "Available connections: {$stats['available_connections']}\n";
            echo "Total connections: {$stats['total_connections']}\n";
            
            // Release connections
            foreach ($connections as $connection) {
                $pool->releaseConnection($connection);
            }
            
            $stats = $pool->getStats();
            echo "\nAfter release:\n";
            echo "In-use connections: {$stats['in_use_connections']}\n";
            echo "Available connections: {$stats['available_connections']}\n";
            
            $pool->closeAll();
        } catch (Exception $e) {
            echo "Connection pool demo skipped: " . $e->getMessage() . "\n";
        }
    }
    
    public function runAllExamples(): void {
        echo "Database Performance Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateQueryOptimization();
        $this->demonstrateBatchOperations();
        $this->demonstrateConnectionPooling();
        
        // Show final performance report
        echo "\nFinal Performance Report\n";
        echo str_repeat("-", 30) . "\n";
        
        $stats = $this->monitor->getStats();
        echo "Database Performance Summary:\n";
        echo "  Total queries: {$stats['total_queries']}\n";
        echo "  Total time: {$stats['total_duration_ms']}ms\n";
        echo "  Average time: {$stats['avg_duration_ms']}ms\n";
        echo "  Total rows processed: {$stats['total_rows']}\n";
        echo "  Slow queries: {$stats['slow_queries']}\n";
        
        if (!empty($this->monitor->getSlowQueries())) {
            echo "\nSlow Queries:\n";
            foreach ($this->monitor->getSlowQueries() as $query) {
                echo "  {$query['sql']} ({$query['duration_ms']}ms)\n";
            }
        }
    }
}

// Database Performance Best Practices
function printDatabasePerformanceBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Database Performance Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Query Optimization:\n";
    echo "   • Use appropriate indexes\n";
    echo "   • Avoid SELECT *\n";
    echo "   • Use LIMIT for large result sets\n";
    echo "   • Optimize WHERE clauses\n\n";
    
    echo "2. Connection Management:\n";
    echo "   • Use connection pooling\n";
    echo "   • Reuse connections when possible\n";
    echo "   • Close connections properly\n";
    echo "   • Use persistent connections\n\n";
    
    echo "3. Caching Strategies:\n";
    echo "   • Cache frequent queries\n";
    echo "   • Use appropriate TTL\n";
    echo "   • Implement cache invalidation\n";
    echo "   • Consider distributed caching\n\n";
    
    echo "4. Batch Operations:\n";
    echo "   • Use transactions for multiple operations\n";
    echo "   • Batch insert/update/delete operations\n";
    echo "   • Use prepared statements\n";
    echo "   • Avoid N+1 query problems\n\n";
    
    echo "5. Database Design:\n";
    echo "   • Normalize appropriately\n";
    echo "   • Choose right data types\n";
    echo "   • Add necessary indexes\n";
    echo "   • Consider partitioning for large tables\n\n";
    
    echo "6. Monitoring:\n";
    echo "   • Track query performance\n";
    echo "   • Monitor slow queries\n";
    echo "   • Set up alerts for performance issues\n";
    echo "   • Regular performance reviews\n";
}

// Main execution
function runDatabasePerformanceDemo(): void {
    $examples = new DatabasePerformanceExamples();
    $examples->runAllExamples();
    printDatabasePerformanceBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDatabasePerformanceDemo();
}
?>
