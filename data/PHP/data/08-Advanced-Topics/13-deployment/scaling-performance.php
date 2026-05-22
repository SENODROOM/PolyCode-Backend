<?php
/**
 * Scaling and Performance Optimization
 * 
 * This file demonstrates scaling strategies, performance optimization,
 * load balancing, and high availability techniques.
 */

// Load Balancer Manager
class LoadBalancerManager {
    private array $servers = [];
    private array $algorithms = [];
    private string $currentAlgorithm = 'round_robin';
    private array $healthChecks = [];
    private array $statistics = [];
    
    public function __construct() {
        $this->initializeAlgorithms();
        $this->initializeServers();
    }
    
    private function initializeAlgorithms(): void {
        $this->algorithms = [
            'round_robin' => [
                'name' => 'Round Robin',
                'description' => 'Distributes requests evenly across all servers'
            ],
            'least_connections' => [
                'name' => 'Least Connections',
                'description' => 'Sends requests to server with fewest active connections'
            ],
            'weighted_round_robin' => [
                'name' => 'Weighted Round Robin',
                'description' => 'Distributes based on server weights'
            ],
            'ip_hash' => [
                'name' => 'IP Hash',
                'description' => 'Distributes based on client IP hash'
            ],
            'random' => [
                'name' => 'Random',
                'description' => 'Randomly selects a server'
            ]
        ];
    }
    
    private function initializeServers(): void {
        $this->servers = [
            'web-01' => [
                'host' => '192.168.1.10',
                'port' => 80,
                'weight' => 1,
                'connections' => 0,
                'status' => 'healthy',
                'response_time' => 120,
                'cpu_usage' => 45,
                'memory_usage' => 60,
                'last_check' => time()
            ],
            'web-02' => [
                'host' => '192.168.1.11',
                'port' => 80,
                'weight' => 1,
                'connections' => 0,
                'status' => 'healthy',
                'response_time' => 95,
                'cpu_usage' => 30,
                'memory_usage' => 55,
                'last_check' => time()
            ],
            'web-03' => [
                'host' => '192.168.1.12',
                'port' => 80,
                'weight' => 2,
                'connections' => 0,
                'status' => 'healthy',
                'response_time' => 110,
                'cpu_usage' => 50,
                'memory_usage' => 65,
                'last_check' => time()
            ]
        ];
    }
    
    public function addServer(string $name, array $config): void {
        $this->servers[$name] = array_merge([
            'host' => '127.0.0.1',
            'port' => 80,
            'weight' => 1,
            'connections' => 0,
            'status' => 'healthy',
            'response_time' => 100,
            'cpu_usage' => 0,
            'memory_usage' => 0,
            'last_check' => time()
        ], $config);
    }
    
    public function removeServer(string $name): void {
        unset($this->servers[$name]);
    }
    
    public function setAlgorithm(string $algorithm): void {
        if (!isset($this->algorithms[$algorithm])) {
            throw new InvalidArgumentException("Unknown algorithm: $algorithm");
        }
        
        $this->currentAlgorithm = $algorithm;
    }
    
    public function selectServer(?string $clientIp = null): ?string {
        $healthyServers = array_filter($this->servers, fn($s) => $s['status'] === 'healthy');
        
        if (empty($healthyServers)) {
            return null;
        }
        
        switch ($this->currentAlgorithm) {
            case 'round_robin':
                return $this->roundRobin($healthyServers);
            case 'least_connections':
                return $this->leastConnections($healthyServers);
            case 'weighted_round_robin':
                return $this->weightedRoundRobin($healthyServers);
            case 'ip_hash':
                return $this->ipHash($healthyServers, $clientIp);
            case 'random':
                return $this->random($healthyServers);
            default:
                return array_key_first($healthyServers);
        }
    }
    
    private function roundRobin(array $servers): string {
        static $currentIndex = 0;
        $serverNames = array_keys($servers);
        
        $server = $serverNames[$currentIndex % count($serverNames)];
        $currentIndex++;
        
        return $server;
    }
    
    private function leastConnections(array $servers): string {
        $minConnections = PHP_INT_MAX;
        $selectedServer = null;
        
        foreach ($servers as $name => $server) {
            if ($server['connections'] < $minConnections) {
                $minConnections = $server['connections'];
                $selectedServer = $name;
            }
        }
        
        return $selectedServer;
    }
    
    private function weightedRoundRobin(array $servers): string {
        $totalWeight = array_sum(array_column($servers, 'weight'));
        $random = mt_rand(1, $totalWeight);
        $currentWeight = 0;
        
        foreach ($servers as $name => $server) {
            $currentWeight += $server['weight'];
            if ($random <= $currentWeight) {
                return $name;
            }
        }
        
        return array_key_first($servers);
    }
    
    private function ipHash(array $servers, ?string $clientIp): string {
        if ($clientIp === null) {
            return array_key_first($servers);
        }
        
        $hash = crc32($clientIp);
        $serverNames = array_keys($servers);
        $index = abs($hash) % count($serverNames);
        
        return $serverNames[$index];
    }
    
    private function random(array $servers): string {
        $serverNames = array_keys($servers);
        return $serverNames[array_rand($serverNames)];
    }
    
    public function processRequest(string $clientIp, string $requestPath): array {
        $serverName = $this->selectServer($clientIp);
        
        if ($serverName === null) {
            return [
                'success' => false,
                'error' => 'No healthy servers available',
                'response_time' => 0
            ];
        }
        
        $server = &$this->servers[$serverName];
        $server['connections']++;
        
        // Simulate request processing
        $responseTime = $server['response_time'] + rand(-20, 50);
        usleep($responseTime * 1000); // Convert to microseconds
        
        $server['connections']--;
        
        // Update statistics
        $this->updateStatistics($serverName, $responseTime);
        
        return [
            'success' => true,
            'server' => $serverName,
            'server_host' => $server['host'],
            'response_time' => $responseTime
        ];
    }
    
    private function updateStatistics(string $serverName, int $responseTime): void {
        if (!isset($this->statistics[$serverName])) {
            $this->statistics[$serverName] = [
                'total_requests' => 0,
                'total_response_time' => 0,
                'min_response_time' => PHP_INT_MAX,
                'max_response_time' => 0,
                'avg_response_time' => 0
            ];
        }
        
        $stats = &$this->statistics[$serverName];
        $stats['total_requests']++;
        $stats['total_response_time'] += $responseTime;
        $stats['min_response_time'] = min($stats['min_response_time'], $responseTime);
        $stats['max_response_time'] = max($stats['max_response_time'], $responseTime);
        $stats['avg_response_time'] = $stats['total_response_time'] / $stats['total_requests'];
    }
    
    public function performHealthChecks(): array {
        $results = [];
        
        foreach ($this->servers as $name => &$server) {
            $checkResult = $this->healthCheck($server);
            $server['status'] = $checkResult['healthy'] ? 'healthy' : 'unhealthy';
            $server['last_check'] = time();
            $server['response_time'] = $checkResult['response_time'];
            
            $results[$name] = $checkResult;
        }
        
        return $results;
    }
    
    private function healthCheck(array $server): array {
        // Simulate health check
        $healthy = rand(1, 100) > 5; // 95% success rate
        $responseTime = $server['response_time'] + rand(-10, 30);
        
        return [
            'healthy' => $healthy,
            'response_time' => $responseTime,
            'timestamp' => time()
        ];
    }
    
    public function getLoadBalancerStatus(): array {
        return [
            'algorithm' => $this->currentAlgorithm,
            'algorithm_info' => $this->algorithms[$this->currentAlgorithm],
            'servers' => $this->servers,
            'statistics' => $this->statistics,
            'total_servers' => count($this->servers),
            'healthy_servers' => count(array_filter($this->servers, fn($s) => $s['status'] === 'healthy'))
        ];
    }
}

// Auto Scaling Manager
class AutoScalingManager {
    private array $scalingGroups = [];
    private array $metrics = [];
    private array $policies = [];
    private array $instances = [];
    
    public function __construct() {
        $this->initializeScalingGroups();
        $this->initializePolicies();
    }
    
    private function initializeScalingGroups(): void {
        $this->scalingGroups = [
            'web_servers' => [
                'name' => 'Web Servers',
                'min_size' => 2,
                'max_size' => 10,
                'desired_capacity' => 3,
                'instance_type' => 't3.medium',
                'current_instances' => 3,
                'cooldown_period' => 300,
                'last_scale' => 0
            ],
            'api_servers' => [
                'name' => 'API Servers',
                'min_size' => 1,
                'max_size' => 8,
                'desired_capacity' => 2,
                'instance_type' => 't3.large',
                'current_instances' => 2,
                'cooldown_period' => 300,
                'last_scale' => 0
            ],
            'worker_servers' => [
                'name' => 'Worker Servers',
                'min_size' => 1,
                'max_size' => 5,
                'desired_capacity' => 2,
                'instance_type' => 't3.small',
                'current_instances' => 2,
                'cooldown_period' => 600,
                'last_scale' => 0
            ]
        ];
    }
    
    private function initializePolicies(): void {
        $this->policies = [
            'scale_up_cpu' => [
                'name' => 'Scale Up on High CPU',
                'type' => 'scale_up',
                'metric' => 'cpu_utilization',
                'threshold' => 70,
                'comparison' => '>',
                'period' => 300,
                'evaluation_periods' => 2,
                'adjustment' => 1,
                'cooldown' => 300
            ],
            'scale_down_cpu' => [
                'name' => 'Scale Down on Low CPU',
                'type' => 'scale_down',
                'metric' => 'cpu_utilization',
                'threshold' => 30,
                'comparison' => '<',
                'period' => 300,
                'evaluation_periods' => 3,
                'adjustment' => -1,
                'cooldown' => 600
            ],
            'scale_up_memory' => [
                'name' => 'Scale Up on High Memory',
                'type' => 'scale_up',
                'metric' => 'memory_utilization',
                'threshold' => 80,
                'comparison' => '>',
                'period' => 300,
                'evaluation_periods' => 2,
                'adjustment' => 1,
                'cooldown' => 300
            ],
            'scale_up_requests' => [
                'name' => 'Scale Up on High Requests',
                'type' => 'scale_up',
                'metric' => 'request_count',
                'threshold' => 1000,
                'comparison' => '>',
                'period' => 60,
                'evaluation_periods' => 1,
                'adjustment' => 2,
                'cooldown' => 180
            ]
        ];
    }
    
    public function collectMetrics(): void {
        foreach ($this->scalingGroups as $groupName => $group) {
            $this->metrics[$groupName] = [
                'timestamp' => time(),
                'cpu_utilization' => $this->generateCpuMetric($groupName),
                'memory_utilization' => $this->generateMemoryMetric($groupName),
                'request_count' => $this->generateRequestMetric($groupName),
                'response_time' => $this->generateResponseTimeMetric($groupName)
            ];
        }
    }
    
    private function generateCpuMetric(string $groupName): float {
        $base = rand(20, 60);
        $spike = rand(1, 100) <= 10 ? rand(20, 40) : 0;
        return min(100, $base + $spike);
    }
    
    private function generateMemoryMetric(string $groupName): float {
        $base = rand(40, 70);
        $spike = rand(1, 100) <= 5 ? rand(15, 25) : 0;
        return min(100, $base + $spike);
    }
    
    private function generateRequestMetric(string $groupName): int {
        $base = rand(200, 800);
        $spike = rand(1, 100) <= 15 ? rand(300, 800) : 0;
        return $base + $spike;
    }
    
    private function generateResponseTimeMetric(string $groupName): float {
        $base = rand(50, 200);
        $spike = rand(1, 100) <= 8 ? rand(100, 300) : 0;
        return ($base + $spike) / 1000; // Convert to seconds
    }
    
    public function evaluateScalingPolicies(): array {
        $scalingActions = [];
        $currentTime = time();
        
        foreach ($this->scalingGroups as $groupName => $group) {
            if (!isset($this->metrics[$groupName])) {
                continue;
            }
            
            $metrics = $this->metrics[$groupName];
            
            // Check cooldown period
            if ($currentTime - $group['last_scale'] < $group['cooldown_period']) {
                continue;
            }
            
            foreach ($this->policies as $policyName => $policy) {
                if ($this->shouldTriggerPolicy($policy, $metrics, $group)) {
                    $action = $this->executeScalingAction($groupName, $policy);
                    if ($action['executed']) {
                        $scalingActions[] = [
                            'group' => $groupName,
                            'policy' => $policyName,
                            'action' => $action,
                            'timestamp' => $currentTime
                        ];
                        
                        // Update last scale time
                        $this->scalingGroups[$groupName]['last_scale'] = $currentTime;
                        break; // Only one action per group per evaluation
                    }
                }
            }
        }
        
        return $scalingActions;
    }
    
    private function shouldTriggerPolicy(array $policy, array $metrics, array $group): bool {
        if (!isset($metrics[$policy['metric']])) {
            return false;
        }
        
        $value = $metrics[$policy['metric']];
        $threshold = $policy['threshold'];
        
        switch ($policy['comparison']) {
            case '>':
                return $value > $threshold;
            case '<':
                return $value < $threshold;
            case '>=':
                return $value >= $threshold;
            case '<=':
                return $value <= $threshold;
            case '==':
                return $value == $threshold;
            default:
                return false;
        }
    }
    
    private function executeScalingAction(string $groupName, array $policy): array {
        $group = &$this->scalingGroups[$groupName];
        $adjustment = $policy['adjustment'];
        
        $newCapacity = $group['desired_capacity'] + $adjustment;
        
        // Check bounds
        if ($newCapacity < $group['min_size']) {
            $newCapacity = $group['min_size'];
        } elseif ($newCapacity > $group['max_size']) {
            $newCapacity = $group['max_size'];
        }
        
        if ($newCapacity == $group['desired_capacity']) {
            return ['executed' => false, 'reason' => 'No capacity change needed'];
        }
        
        $oldCapacity = $group['desired_capacity'];
        $group['desired_capacity'] = $newCapacity;
        
        // Simulate instance launch/termination
        if ($adjustment > 0) {
            $launched = $this->launchInstances($groupName, $adjustment);
            $group['current_instances'] += $launched;
        } else {
            $terminated = $this->terminateInstances($groupName, abs($adjustment));
            $group['current_instances'] -= $terminated;
        }
        
        return [
            'executed' => true,
            'type' => $policy['type'],
            'old_capacity' => $oldCapacity,
            'new_capacity' => $newCapacity,
            'adjustment' => $adjustment,
            'instances_affected' => abs($adjustment)
        ];
    }
    
    private function launchInstances(string $groupName, int $count): int {
        $launched = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $instanceId = uniqid('i-', true);
            $this->instances[$instanceId] = [
                'id' => $instanceId,
                'group' => $groupName,
                'status' => 'launching',
                'launch_time' => time(),
                'instance_type' => $this->scalingGroups[$groupName]['instance_type']
            ];
            
            // Simulate launch time
            usleep(100000); // 0.1 seconds
            $this->instances[$instanceId]['status'] = 'running';
            
            $launched++;
        }
        
        return $launched;
    }
    
    private function terminateInstances(string $groupName, int $count): int {
        $groupInstances = array_filter($this->instances, fn($i) => $i['group'] === $groupName && $i['status'] === 'running');
        $terminated = 0;
        
        foreach (array_slice($groupInstances, 0, $count) as $instance) {
            $this->instances[$instance['id']]['status'] = 'terminating';
            
            // Simulate termination time
            usleep(50000); // 0.05 seconds
            unset($this->instances[$instance['id']]);
            
            $terminated++;
        }
        
        return $terminated;
    }
    
    public function getScalingStatus(): array {
        return [
            'groups' => $this->scalingGroups,
            'metrics' => $this->metrics,
            'policies' => $this->policies,
            'instances' => $this->instances,
            'total_instances' => count($this->instances)
        ];
    }
}

// CDN Manager
class CDNManager {
    private array $cdnConfig = [];
    private array $cacheRules = [];
    private array $statistics = [];
    private array $edgeLocations = [];
    
    public function __construct() {
        $this->initializeCDNConfig();
        $this->initializeEdgeLocations();
        $this->initializeCacheRules();
    }
    
    private function initializeCDNConfig(): void {
        $this->cdnConfig = [
            'provider' => 'cloudflare',
            'domain' => 'example.com',
            'cache_ttl' => 3600,
            'compression' => true,
            'security' => [
                'ddos_protection' => true,
                'ssl' => true,
                'firewall' => true
            ],
            'optimization' => [
                'image_optimization' => true,
                'minification' => true,
                'brotli_compression' => true
            ]
        ];
    }
    
    private function initializeEdgeLocations(): void {
        $this->edgeLocations = [
            'us-east-1' => [
                'name' => 'US East (N. Virginia)',
                'country' => 'US',
                'city' => 'Ashburn',
                'cache_size' => '500GB',
                'requests_served' => 0,
                'hit_rate' => 0
            ],
            'us-west-2' => [
                'name' => 'US West (Oregon)',
                'country' => 'US',
                'city' => 'Portland',
                'cache_size' => '500GB',
                'requests_served' => 0,
                'hit_rate' => 0
            ],
            'eu-west-1' => [
                'name' => 'EU West (Ireland)',
                'country' => 'IE',
                'city' => 'Dublin',
                'cache_size' => '500GB',
                'requests_served' => 0,
                'hit_rate' => 0
            ],
            'ap-southeast-1' => [
                'name' => 'AP Southeast (Singapore)',
                'country' => 'SG',
                'city' => 'Singapore',
                'cache_size' => '500GB',
                'requests_served' => 0,
                'hit_rate' => 0
            ]
        ];
    }
    
    private function initializeCacheRules(): void {
        $this->cacheRules = [
            'static_assets' => [
                'pattern' => '*.css|*.js|*.png|*.jpg|*.jpeg|*.gif|*.ico|*.svg|*.woff|*.woff2',
                'cache_ttl' => 31536000, // 1 year
                'browser_ttl' => 31536000,
                'edge_ttl' => 2592000, // 30 days
                'compression' => true
            ],
            'api_responses' => [
                'pattern' => '/api/*',
                'cache_ttl' => 300, // 5 minutes
                'browser_ttl' => 0,
                'edge_ttl' => 300,
                'compression' => true,
                'vary_on' => ['Accept', 'Authorization']
            ],
            'html_pages' => [
                'pattern' => '*.html',
                'cache_ttl' => 3600, // 1 hour
                'browser_ttl' => 600, // 10 minutes
                'edge_ttl' => 3600,
                'compression' => true
            ],
            'images' => [
                'pattern' => '*.png|*.jpg|*.jpeg|*.gif|*.webp',
                'cache_ttl' => 2592000, // 30 days
                'browser_ttl' => 86400, // 1 day
                'edge_ttl' => 2592000,
                'compression' => false,
                'optimization' => ['auto_webp', 'auto_avif']
            ]
        ];
    }
    
    public function addCacheRule(string $name, array $rule): void {
        $this->cacheRules[$name] = $rule;
    }
    
    public function purgeCache(array $paths = []): array {
        $purgeResults = [];
        
        if (empty($paths)) {
            // Purge all cache
            echo "Purging entire CDN cache...\n";
            
            foreach ($this->edgeLocations as $location => $data) {
                $purgeResults[$location] = [
                    'purged_files' => rand(1000, 10000),
                    'purge_time' => rand(5, 30),
                    'status' => 'success'
                ];
                
                // Reset statistics
                $this->edgeLocations[$location]['requests_served'] = 0;
                $this->edgeLocations[$location]['hit_rate'] = 0;
            }
        } else {
            // Purge specific paths
            echo "Purging specific paths: " . implode(', ', $paths) . "\n";
            
            foreach ($this->edgeLocations as $location => $data) {
                $purgeResults[$location] = [
                    'purged_files' => count($paths) * rand(10, 50),
                    'purge_time' => rand(2, 15),
                    'status' => 'success'
                ];
            }
        }
        
        return $purgeResults;
    }
    
    public function simulateRequest(string $path, string $userLocation): array {
        // Select nearest edge location
        $edgeLocation = $this->selectEdgeLocation($userLocation);
        
        // Check if content is cached
        $isCached = $this->isContentCached($path, $edgeLocation);
        $responseTime = $isCached ? rand(10, 50) : rand(100, 300);
        
        // Update statistics
        $this->edgeLocations[$edgeLocation]['requests_served']++;
        
        if ($isCached) {
            $hitRate = $this->edgeLocations[$edgeLocation]['hit_rate'];
            $totalRequests = $this->edgeLocations[$edgeLocation]['requests_served'];
            $this->edgeLocations[$edgeLocation]['hit_rate'] = (($hitRate * ($totalRequests - 1)) + 1) / $totalRequests;
        }
        
        return [
            'edge_location' => $edgeLocation,
            'cache_hit' => $isCached,
            'response_time' => $responseTime,
            'path' => $path,
            'user_location' => $userLocation
        ];
    }
    
    private function selectEdgeLocation(string $userLocation): string {
        // Simplified location selection
        $locationMap = [
            'US' => 'us-east-1',
            'EU' => 'eu-west-1',
            'Asia' => 'ap-southeast-1',
            'default' => 'us-west-2'
        ];
        
        return $locationMap[$userLocation] ?? $locationMap['default'];
    }
    
    private function isContentCached(string $path, string $edgeLocation): bool {
        // Check cache rules
        foreach ($this->cacheRules as $rule) {
            if ($this->matchesPattern($path, $rule['pattern'])) {
                // Simulate cache hit based on TTL
                return rand(1, 100) <= 85; // 85% hit rate
            }
        }
        
        // Default cache behavior
        return rand(1, 100) <= 70; // 70% hit rate
    }
    
    private function matchesPattern(string $path, string $pattern): bool {
        // Simplified pattern matching
        if (strpos($pattern, '*') !== false) {
            $regex = '/^' . str_replace('*', '.*', preg_quote($pattern, '/')) . '$/';
            return preg_match($regex, $path);
        }
        
        return $path === $pattern;
    }
    
    public function getCDNStatistics(): array {
        $totalRequests = 0;
        $totalHits = 0;
        $avgHitRate = 0;
        
        foreach ($this->edgeLocations as $location => $data) {
            $totalRequests += $data['requests_served'];
            $totalHits += $data['requests_served'] * $data['hit_rate'];
        }
        
        if ($totalRequests > 0) {
            $avgHitRate = $totalHits / $totalRequests;
        }
        
        return [
            'total_requests' => $totalRequests,
            'average_hit_rate' => round($avgHitRate * 100, 2),
            'edge_locations' => $this->edgeLocations,
            'cache_rules' => $this->cacheRules,
            'config' => $this->cdnConfig
        ];
    }
}

// Performance Optimizer
class PerformanceOptimizer {
    private array $optimizations = [];
    private array $benchmarks = [];
    
    public function __construct() {
        $this->initializeOptimizations();
    }
    
    private function initializeOptimizations(): void {
        $this->optimizations = [
            'database_query_cache' => [
                'name' => 'Database Query Cache',
                'description' => 'Cache frequently used database queries',
                'impact' => 'high',
                'implementation' => 'redis',
                'estimated_improvement' => '30-50%'
            ],
            'opcode_cache' => [
                'name' => 'OPcache',
                'description' => 'Cache compiled PHP bytecode',
                'impact' => 'high',
                'implementation' => 'php_opcache',
                'estimated_improvement' => '20-40%'
            ],
            'response_compression' => [
                'name' => 'Response Compression',
                'description' => 'Compress HTTP responses',
                'impact' => 'medium',
                'implementation' => 'gzip/brotli',
                'estimated_improvement' => '15-30%'
            ],
            'lazy_loading' => [
                'name' => 'Lazy Loading',
                'description' => 'Load data only when needed',
                'impact' => 'medium',
                'implementation' => 'application',
                'estimated_improvement' => '20-35%'
            ],
            'connection_pooling' => [
                'name' => 'Database Connection Pooling',
                'description' => 'Reuse database connections',
                'impact' => 'medium',
                'implementation' => 'pdo_pool',
                'estimated_improvement' => '10-25%'
            ],
            'static_asset_caching' => [
                'name' => 'Static Asset Caching',
                'description' => 'Cache CSS, JS, and images',
                'impact' => 'high',
                'implementation' => 'cdn/browser',
                'estimated_improvement' => '40-60%'
            ],
            'session_storage' => [
                'name' => 'Session Storage Optimization',
                'description' => 'Store sessions in Redis',
                'impact' => 'medium',
                'implementation' => 'redis',
                'estimated_improvement' => '15-25%'
            ],
            'image_optimization' => [
                'name' => 'Image Optimization',
                'description' => 'Compress and optimize images',
                'impact' => 'medium',
                'implementation' => 'cdn',
                'estimated_improvement' => '20-40%'
            ]
        ];
    }
    
    public function runBenchmark(string $scenario): array {
        echo "Running benchmark: $scenario\n";
        
        $benchmark = [
            'scenario' => $scenario,
            'start_time' => microtime(true),
            'metrics' => []
        ];
        
        // Simulate different scenarios
        switch ($scenario) {
            case 'baseline':
                $benchmark['metrics'] = $this->runBaselineTest();
                break;
            case 'with_cache':
                $benchmark['metrics'] = $this->runCachedTest();
                break;
            case 'with_optimization':
                $benchmark['metrics'] = $this->runOptimizedTest();
                break;
            case 'load_test':
                $benchmark['metrics'] = $this->runLoadTest();
                break;
            default:
                $benchmark['metrics'] = $this->runBaselineTest();
        }
        
        $benchmark['end_time'] = microtime(true);
        $benchmark['duration'] = $benchmark['end_time'] - $benchmark['start_time'];
        
        return $benchmark;
    }
    
    private function runBaselineTest(): array {
        return [
            'response_time' => rand(200, 500),
            'throughput' => rand(50, 100),
            'memory_usage' => rand(50, 80),
            'cpu_usage' => rand(30, 60),
            'error_rate' => rand(0, 5)
        ];
    }
    
    private function runCachedTest(): array {
        return [
            'response_time' => rand(50, 150),
            'throughput' => rand(150, 300),
            'memory_usage' => rand(40, 70),
            'cpu_usage' => rand(20, 45),
            'error_rate' => rand(0, 2)
        ];
    }
    
    private function runOptimizedTest(): array {
        return [
            'response_time' => rand(30, 100),
            'throughput' => rand(200, 400),
            'memory_usage' => rand(30, 60),
            'cpu_usage' => rand(15, 40),
            'error_rate' => rand(0, 1)
        ];
    }
    
    private function runLoadTest(): array {
        return [
            'concurrent_users' => rand(100, 1000),
            'requests_per_second' => rand(500, 2000),
            'avg_response_time' => rand(100, 800),
            'peak_response_time' => rand(500, 2000),
            'error_rate' => rand(0, 10),
            'success_rate' => rand(90, 100)
        ];
    }
    
    public function compareBenchmarks(array $benchmarks): array {
        $comparison = [
            'scenarios' => [],
            'improvements' => []
        ];
        
        $baseline = null;
        
        foreach ($benchmarks as $benchmark) {
            $scenario = $benchmark['scenario'];
            $metrics = $benchmark['metrics'];
            
            $comparison['scenarios'][$scenario] = $metrics;
            
            if ($scenario === 'baseline') {
                $baseline = $metrics;
            } elseif ($baseline !== null) {
                $improvement = [
                    'response_time' => round((($baseline['response_time'] - $metrics['response_time']) / $baseline['response_time']) * 100, 2),
                    'throughput' => round((($metrics['throughput'] - $baseline['throughput']) / $baseline['throughput']) * 100, 2),
                    'memory_usage' => round((($baseline['memory_usage'] - $metrics['memory_usage']) / $baseline['memory_usage']) * 100, 2),
                    'cpu_usage' => round((($baseline['cpu_usage'] - $metrics['cpu_usage']) / $baseline['cpu_usage']) * 100, 2)
                ];
                
                $comparison['improvements'][$scenario] = $improvement;
            }
        }
        
        return $comparison;
    }
    
    public function getOptimizationRecommendations(): array {
        return [
            'immediate' => array_filter($this->optimizations, fn($o) => $o['impact'] === 'high'),
            'short_term' => array_filter($this->optimizations, fn($o) => $o['impact'] === 'medium'),
            'long_term' => array_filter($this->optimizations, fn($o) => $o['impact'] === 'low')
        ];
    }
    
    public function getOptimizations(): array {
        return $this->optimizations;
    }
}

// Scaling and Performance Examples
class ScalingPerformanceExamples {
    private LoadBalancerManager $loadBalancer;
    private AutoScalingManager $autoScaling;
    private CDNManager $cdn;
    private PerformanceOptimizer $optimizer;
    
    public function __construct() {
        $this->loadBalancer = new LoadBalancerManager();
        $this->autoScaling = new AutoScalingManager();
        $this->cdn = new CDNManager();
        $this->optimizer = new PerformanceOptimizer();
    }
    
    public function demonstrateLoadBalancing(): void {
        echo "Load Balancing Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show load balancer status
        $status = $this->loadBalancer->getLoadBalancerStatus();
        
        echo "Load Balancer Configuration:\n";
        echo "Algorithm: {$status['algorithm_info']['name']}\n";
        echo "Description: {$status['algorithm_info']['description']}\n";
        echo "Servers: {$status['healthy_servers']}/{$status['total_servers']} healthy\n\n";
        
        // Test different algorithms
        $algorithms = ['round_robin', 'least_connections', 'weighted_round_robin'];
        $clientIps = ['192.168.1.100', '192.168.1.101', '192.168.1.102'];
        
        foreach ($algorithms as $algorithm) {
            echo "Testing algorithm: $algorithm\n";
            $this->loadBalancer->setAlgorithm($algorithm);
            
            for ($i = 0; $i < 10; $i++) {
                $clientIp = $clientIps[array_rand($clientIps)];
                $result = $this->loadBalancer->processRequest($clientIp, '/api/users');
                
                if ($result['success']) {
                    echo "  Request $i: {$result['server']} ({$result['response_time']}ms)\n";
                }
            }
            
            echo "\n";
        }
        
        // Perform health checks
        echo "Performing health checks...\n";
        $healthResults = $this->loadBalancer->performHealthChecks();
        
        foreach ($healthResults as $server => $result) {
            $status = $result['healthy'] ? '✅ Healthy' : '❌ Unhealthy';
            echo "  $server: $status ({$result['response_time']}ms)\n";
        }
    }
    
    public function demonstrateAutoScaling(): void {
        echo "\nAuto Scaling Example\n";
        echo str_repeat("-", 20) . "\n";
        
        // Collect metrics
        $this->autoScaling->collectMetrics();
        
        // Show current scaling status
        $status = $this->autoScaling->getScalingStatus();
        
        echo "Current Scaling Groups:\n";
        foreach ($status['groups'] as $groupName => $group) {
            echo "  {$group['name']}:\n";
            echo "    Current: {$group['current_instances']}\n";
            echo "    Desired: {$group['desired_capacity']}\n";
            echo "    Min/Max: {$group['min_size']}/{$group['max_size']}\n";
            
            if (isset($status['metrics'][$groupName])) {
                $metrics = $status['metrics'][$groupName];
                echo "    CPU: {$metrics['cpu_utilization']}%\n";
                echo "    Memory: {$metrics['memory_utilization']}%\n";
                echo "    Requests: {$metrics['request_count']}\n";
            }
        }
        
        // Evaluate scaling policies
        echo "\nEvaluating scaling policies...\n";
        $actions = $this->autoScaling->evaluateScalingPolicies();
        
        if (!empty($actions)) {
            foreach ($actions as $action) {
                $icon = $action['action']['type'] === 'scale_up' ? '⬆️' : '⬇️';
                echo "  $icon {$action['group']}: {$action['action']['type']} from {$action['action']['old_capacity']} to {$action['action']['new_capacity']}\n";
            }
        } else {
            echo "  No scaling actions required\n";
        }
        
        // Show updated status
        $updatedStatus = $this->autoScaling->getScalingStatus();
        echo "\nUpdated Scaling Groups:\n";
        foreach ($updatedStatus['groups'] as $groupName => $group) {
            echo "  {$group['name']}: {$group['current_instances']} instances\n";
        }
    }
    
    public function demonstrateCDN(): void {
        echo "\nCDN Management Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show CDN configuration
        $stats = $this->cdn->getCDNStatistics();
        
        echo "CDN Configuration:\n";
        echo "Provider: {$stats['config']['provider']}\n";
        echo "Domain: {$stats['config']['domain']}\n";
        echo "Cache TTL: {$stats['config']['cache_ttl']}s\n";
        
        echo "\nEdge Locations:\n";
        foreach ($stats['edge_locations'] as $location => $data) {
            echo "  {$data['name']}: {$data['requests_served']} requests, " . round($data['hit_rate'] * 100, 1) . "% hit rate\n";
        }
        
        // Simulate CDN requests
        echo "\nSimulating CDN requests...\n";
        $paths = ['/style.css', '/script.js', '/image.jpg', '/api/data', '/page.html'];
        $locations = ['US', 'EU', 'Asia'];
        
        for ($i = 0; $i < 20; $i++) {
            $path = $paths[array_rand($paths)];
            $location = $locations[array_rand($locations)];
            $result = $this->cdn->simulateRequest($path, $location);
            
            $cacheIcon = $result['cache_hit'] ? '✅' : '❌';
            echo "  Request $i: {$result['path']} -> {$result['edge_location']} {$cacheIcon} ({$result['response_time']}ms)\n";
        }
        
        // Show updated statistics
        $updatedStats = $this->cdn->getCDNStatistics();
        echo "\nUpdated CDN Statistics:\n";
        echo "Total Requests: {$updatedStats['total_requests']}\n";
        echo "Average Hit Rate: {$updatedStats['average_hit_rate']}%\n";
        
        // Purge cache
        echo "\nPurging CDN cache...\n";
        $purgeResults = $this->cdn->purgeCache(['/api/data', '/page.html']);
        
        foreach ($purgeResults as $location => $result) {
            echo "  $location: {$result['purged_files']} files purged\n";
        }
    }
    
    public function demonstratePerformanceOptimization(): void {
        echo "\nPerformance Optimization Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show optimization recommendations
        $recommendations = $this->optimizer->getOptimizationRecommendations();
        
        echo "Optimization Recommendations:\n";
        echo "\nImmediate (High Impact):\n";
        foreach ($recommendations['immediate'] as $opt) {
            echo "  • {$opt['name']}: {$opt['estimated_improvement']} improvement\n";
            echo "    {$opt['description']}\n";
        }
        
        echo "\nShort Term (Medium Impact):\n";
        foreach ($recommendations['short_term'] as $opt) {
            echo "  • {$opt['name']}: {$opt['estimated_improvement']} improvement\n";
        }
        
        // Run benchmarks
        echo "\nRunning Performance Benchmarks...\n";
        $scenarios = ['baseline', 'with_cache', 'with_optimization'];
        $benchmarks = [];
        
        foreach ($scenarios as $scenario) {
            $benchmarks[] = $this->optimizer->runBenchmark($scenario);
        }
        
        // Compare benchmarks
        $comparison = $this->optimizer->compareBenchmarks($benchmarks);
        
        echo "\nBenchmark Results:\n";
        foreach ($comparison['scenarios'] as $scenario => $metrics) {
            echo "  $scenario:\n";
            echo "    Response Time: {$metrics['response_time']}ms\n";
            echo "    Throughput: {$metrics['throughput']} req/s\n";
            echo "    Memory Usage: {$metrics['memory_usage']}%\n";
            echo "    CPU Usage: {$metrics['cpu_usage']}%\n";
        }
        
        echo "\nPerformance Improvements:\n";
        foreach ($comparison['improvements'] as $scenario => $improvement) {
            echo "  $scenario vs baseline:\n";
            echo "    Response Time: {$improvement['response_time']}% improvement\n";
            echo "    Throughput: {$improvement['throughput']}% improvement\n";
            echo "    Memory Usage: {$improvement['memory_usage']}% improvement\n";
            echo "    CPU Usage: {$improvement['cpu_usage']}% improvement\n";
        }
        
        // Run load test
        echo "\nRunning Load Test...\n";
        $loadTest = $this->optimizer->runBenchmark('load_test');
        $metrics = $loadTest['metrics'];
        
        echo "Load Test Results:\n";
        echo "  Concurrent Users: {$metrics['concurrent_users']}\n";
        echo "  Requests/Second: {$metrics['requests_per_second']}\n";
        echo "  Avg Response Time: {$metrics['avg_response_time']}ms\n";
        echo "  Peak Response Time: {$metrics['peak_response_time']}ms\n";
        echo "  Success Rate: {$metrics['success_rate']}%\n";
    }
    
    public function demonstrateComprehensiveScaling(): void {
        echo "\nComprehensive Scaling Strategy\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Load Balancing Setup\n";
        echo "   • Multiple web servers behind load balancer\n";
        echo "   • Health checks and failover\n";
        echo "   • Traffic distribution algorithms\n";
        echo "   • SSL termination\n\n";
        
        echo "2. Auto Scaling Configuration\n";
        echo "   • Scale based on CPU and memory metrics\n";
        echo "   • Scale based on request count\n";
        echo "   • Minimum and maximum instance limits\n";
        echo "   • Cooldown periods\n\n";
        
        echo "3. CDN Integration\n";
        echo "   • Static asset caching\n";
        echo "   • Geographic distribution\n";
        echo "   • Cache purging and invalidation\n";
        echo "   • Performance optimization\n\n";
        
        echo "4. Performance Optimization\n";
        echo "   • Database query caching\n";
        echo "   • Opcode caching\n";
        echo "   • Response compression\n";
        echo "   • Connection pooling\n\n";
        
        echo "5. Monitoring and Alerting\n";
        echo "   • Real-time metrics collection\n";
        echo "   • Automated scaling triggers\n";
        echo "   • Performance monitoring\n";
        echo "   • Health checks\n";
        
        // Simulate comprehensive scenario
        echo "\nSimulating High Traffic Scenario...\n";
        
        // Simulate increased load
        for ($i = 0; $i < 50; $i++) {
            $clientIp = '192.168.1.' . rand(100, 200);
            $result = $this->loadBalancer->processRequest($clientIp, '/api/data');
            
            if ($i % 10 === 0) {
                echo "  Request $i: {$result['server']} ({$result['response_time']}ms)\n";
            }
        }
        
        // Trigger auto-scaling evaluation
        $this->autoScaling->collectMetrics();
        $actions = $this->autoScaling->evaluateScalingPolicies();
        
        if (!empty($actions)) {
            echo "\nAuto-scaling triggered:\n";
            foreach ($actions as $action) {
                $icon = $action['action']['type'] === 'scale_up' ? '⬆️' : '⬇️';
                echo "  $icon {$action['group']}: {$action['action']['type']}\n";
            }
        }
        
        echo "\n✅ Scaling strategy successfully handled the load\n";
    }
    
    public function runAllExamples(): void {
        echo "Scaling and Performance Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateLoadBalancing();
        $this->demonstrateAutoScaling();
        $this->demonstrateCDN();
        $this->demonstratePerformanceOptimization();
        $this->demonstrateComprehensiveScaling();
    }
}

// Scaling and Performance Best Practices
function printScalingPerformanceBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Scaling and Performance Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Load Balancing:\n";
    echo "   • Use appropriate load balancing algorithms\n";
    echo "   • Implement health checks\n";
    echo "   • Configure failover mechanisms\n";
    echo "   • Monitor load balancer performance\n";
    echo "   • Use SSL termination efficiently\n\n";
    
    echo "2. Auto Scaling:\n";
    echo "   • Set appropriate thresholds\n";
    echo "   • Use multiple metrics for scaling\n";
    echo "   • Configure cooldown periods\n";
    echo "   • Test scaling policies\n";
    echo "   • Monitor scaling events\n\n";
    
    echo "3. CDN Usage:\n";
    echo "   • Cache static assets aggressively\n";
    echo "   • Use appropriate cache TTLs\n";
    echo "   • Implement cache invalidation\n";
    echo "   • Optimize images and assets\n";
    echo "   • Monitor CDN performance\n\n";
    
    echo "4. Performance Optimization:\n";
    echo "   • Enable opcode caching\n";
    echo "   • Use database query caching\n";
    echo "   • Implement response compression\n";
    echo "   • Optimize database queries\n";
    echo "   • Use connection pooling\n\n";
    
    echo "5. Monitoring:\n";
    echo "   • Monitor key performance metrics\n";
    echo "   • Set up alerting thresholds\n";
    echo "   • Track scaling events\n";
    echo "   • Monitor CDN performance\n";
    echo "   • Use APM tools\n\n";
    
    echo "6. High Availability:\n";
    echo "   • Design for failure\n";
    echo "   • Implement redundancy\n";
    echo "   • Use multiple availability zones\n";
    echo "   • Test disaster recovery\n";
    echo "   • Regular failover testing";
}

// Main execution
function runScalingPerformanceDemo(): void {
    $examples = new ScalingPerformanceExamples();
    $examples->runAllExamples();
    printScalingPerformanceBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runScalingPerformanceDemo();
}
?>
