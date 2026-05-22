<?php
/**
 * Deployment Strategies and Environment Management
 * 
 * This file demonstrates various deployment strategies,
 * environment management, and release processes.
 */

// Environment Manager
class EnvironmentManager {
    private string $currentEnvironment;
    private array $environments = [];
    private array $config = [];
    
    public function __construct(string $environment = 'development') {
        $this->currentEnvironment = $environment;
        $this->initializeEnvironments();
        $this->loadEnvironmentConfig();
    }
    
    private function initializeEnvironments(): void {
        $this->environments = [
            'development' => [
                'debug' => true,
                'error_reporting' => E_ALL,
                'display_errors' => true,
                'log_errors' => true,
                'cache' => false,
                'database' => [
                    'host' => 'localhost',
                    'name' => 'app_dev',
                    'user' => 'dev_user',
                    'password' => 'dev_pass'
                ]
            ],
            'testing' => [
                'debug' => true,
                'error_reporting' => E_ALL,
                'display_errors' => false,
                'log_errors' => true,
                'cache' => false,
                'database' => [
                    'host' => 'localhost',
                    'name' => 'app_test',
                    'user' => 'test_user',
                    'password' => 'test_pass'
                ]
            ],
            'staging' => [
                'debug' => false,
                'error_reporting' => E_ALL & ~E_NOTICE & ~E_DEPRECATED,
                'display_errors' => false,
                'log_errors' => true,
                'cache' => true,
                'database' => [
                    'host' => 'staging-db.example.com',
                    'name' => 'app_staging',
                    'user' => 'staging_user',
                    'password' => 'staging_pass'
                ]
            ],
            'production' => [
                'debug' => false,
                'error_reporting' => 0,
                'display_errors' => false,
                'log_errors' => true,
                'cache' => true,
                'database' => [
                    'host' => 'prod-db.example.com',
                    'name' => 'app_production',
                    'user' => 'prod_user',
                    'password' => 'prod_pass'
                ]
            ]
        ];
    }
    
    private function loadEnvironmentConfig(): void {
        $this->config = $this->environments[$this->currentEnvironment] ?? $this->environments['production'];
        
        // Load environment-specific overrides
        $envFile = ".env.{$this->currentEnvironment}";
        if (file_exists($envFile)) {
            $envConfig = parse_ini_file($envFile);
            $this->config = array_merge($this->config, $envConfig);
        }
        
        // Load local overrides
        if (file_exists('.env.local')) {
            $localConfig = parse_ini_file('.env.local');
            $this->config = array_merge($this->config, $localConfig);
        }
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return $this->config[$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): void {
        $this->config[$key] = $value;
    }
    
    public function getCurrentEnvironment(): string {
        return $this->currentEnvironment;
    }
    
    public function isProduction(): bool {
        return $this->currentEnvironment === 'production';
    }
    
    public function isDevelopment(): bool {
        return $this->currentEnvironment === 'development';
    }
    
    public function getAllConfig(): array {
        return $this->config;
    }
    
    public function applyPHPSettings(): void {
        if ($this->get('error_reporting') !== null) {
            error_reporting($this->get('error_reporting'));
        }
        
        if ($this->get('display_errors') !== null) {
            ini_set('display_errors', $this->get('display_errors') ? '1' : '0');
        }
        
        if ($this->get('log_errors') !== null) {
            ini_set('log_errors', $this->get('log_errors') ? '1' : '0');
        }
        
        if ($this->get('memory_limit') !== null) {
            ini_set('memory_limit', $this->get('memory_limit'));
        }
        
        if ($this->get('max_execution_time') !== null) {
            ini_set('max_execution_time', $this->get('max_execution_time'));
        }
        
        if ($this->get('timezone') !== null) {
            date_default_timezone_set($this->get('timezone'));
        }
    }
}

// Deployment Strategy Interface
interface DeploymentStrategy {
    public function deploy(array $config): array;
    public function rollback(string $deploymentId): array;
    public function getStatus(): array;
}

// Blue-Green Deployment
class BlueGreenDeployment implements DeploymentStrategy {
    private string $blueEnvironment;
    private string $greenEnvironment;
    private array $deployments = [];
    
    public function __construct(string $blueEnv = 'blue', string $greenEnv = 'green') {
        $this->blueEnvironment = $blueEnv;
        $this->greenEnvironment = $greenEnv;
    }
    
    public function deploy(array $config): array {
        $deploymentId = uniqid('deploy_', true);
        $targetEnvironment = $this->getInactiveEnvironment();
        $activeEnvironment = $this->getActiveEnvironment();
        
        echo "Starting Blue-Green deployment: $deploymentId\n";
        echo "Target environment: $targetEnvironment\n";
        echo "Active environment: $activeEnvironment\n";
        
        try {
            // Step 1: Deploy to inactive environment
            echo "Step 1: Deploying to $targetEnvironment\n";
            $this->deployToEnvironment($targetEnvironment, $config);
            
            // Step 2: Run health checks
            echo "Step 2: Running health checks\n";
            $healthCheck = $this->runHealthCheck($targetEnvironment);
            
            if (!$healthCheck['healthy']) {
                throw new RuntimeException("Health check failed: " . $healthCheck['message']);
            }
            
            // Step 3: Switch traffic to new environment
            echo "Step 3: Switching traffic to $targetEnvironment\n";
            $this->switchTraffic($targetEnvironment);
            
            // Step 4: Verify deployment
            echo "Step 4: Verifying deployment\n";
            $verification = $this->verifyDeployment($targetEnvironment);
            
            if (!$verification['success']) {
                // Rollback automatically
                echo "Verification failed, rolling back...\n";
                $this->rollback($deploymentId);
                throw new RuntimeException("Deployment verification failed");
            }
            
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'environment' => $targetEnvironment,
                'status' => 'success',
                'deployed_at' => date('Y-m-d H:i:s'),
                'config' => $config
            ];
            
            echo "Deployment $deploymentId completed successfully\n";
            
            return [
                'success' => true,
                'deployment_id' => $deploymentId,
                'environment' => $targetEnvironment,
                'message' => 'Deployment completed successfully'
            ];
            
        } catch (Exception $e) {
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'environment' => $targetEnvironment,
                'status' => 'failed',
                'deployed_at' => date('Y-m-d H:i:s'),
                'error' => $e->getMessage()
            ];
            
            return [
                'success' => false,
                'deployment_id' => $deploymentId,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function rollback(string $deploymentId): array {
        if (!isset($this->deployments[$deploymentId])) {
            return ['success' => false, 'error' => 'Deployment not found'];
        }
        
        $deployment = $this->deployments[$deploymentId];
        $previousEnvironment = $this->getActiveEnvironment();
        
        echo "Rolling back deployment: $deploymentId\n";
        echo "Switching traffic back to $previousEnvironment\n";
        
        try {
            $this->switchTraffic($previousEnvironment);
            
            $deployment['status'] = 'rolled_back';
            $deployment['rolled_back_at'] = date('Y-m-d H:i:s');
            
            return [
                'success' => true,
                'message' => 'Rollback completed successfully',
                'environment' => $previousEnvironment
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Rollback failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function getStatus(): array {
        $activeEnvironment = $this->getActiveEnvironment();
        $inactiveEnvironment = $this->getInactiveEnvironment();
        
        return [
            'active_environment' => $activeEnvironment,
            'inactive_environment' => $inactiveEnvironment,
            'deployments' => $this->deployments,
            'health_status' => [
                $activeEnvironment => $this->runHealthCheck($activeEnvironment),
                $inactiveEnvironment => $this->runHealthCheck($inactiveEnvironment)
            ]
        ];
    }
    
    private function getActiveEnvironment(): string {
        // In a real implementation, this would check load balancer configuration
        return $this->blueEnvironment;
    }
    
    private function getInactiveEnvironment(): string {
        $active = $this->getActiveEnvironment();
        return $active === $this->blueEnvironment ? $this->greenEnvironment : $this->blueEnvironment;
    }
    
    private function deployToEnvironment(string $environment, array $config): void {
        // Simulate deployment process
        echo "  - Copying files to $environment\n";
        echo "  - Installing dependencies\n";
        echo "  - Running database migrations\n";
        echo "  - Clearing caches\n";
        
        // Simulate deployment time
        usleep(100000); // 0.1 seconds
    }
    
    private function runHealthCheck(string $environment): array {
        // Simulate health check
        echo "  - Checking application health\n";
        echo "  - Verifying database connection\n";
        echo "  - Testing API endpoints\n";
        
        // Simulate health check time
        usleep(50000); // 0.05 seconds
        
        // 90% chance of success for demo
        $healthy = rand(1, 100) <= 90;
        
        return [
            'healthy' => $healthy,
            'message' => $healthy ? 'All checks passed' : 'Health check failed',
            'checks' => [
                'application' => $healthy,
                'database' => $healthy,
                'cache' => $healthy
            ]
        ];
    }
    
    private function switchTraffic(string $environment): void {
        // Simulate traffic switching
        echo "  - Updating load balancer configuration\n";
        echo "  - Switching DNS records\n";
        echo "  - Waiting for propagation\n";
        
        usleep(100000); // 0.1 seconds
    }
    
    private function verifyDeployment(string $environment): array {
        // Simulate deployment verification
        echo "  - Checking application version\n";
        echo "  - Running smoke tests\n";
        echo "  - Monitoring error rates\n";
        
        usleep(50000); // 0.05 seconds
        
        // 95% chance of success for demo
        $success = rand(1, 100) <= 95;
        
        return [
            'success' => $success,
            'message' => $success ? 'Deployment verified' : 'Verification failed',
            'tests_passed' => $success ? 10 : 7
        ];
    }
}

// Rolling Deployment
class RollingDeployment implements DeploymentStrategy {
    private array $servers = [];
    private array $deployments = [];
    private int $batchSize = 2;
    
    public function __construct(array $servers = []) {
        $this->servers = $servers ?: [
            'server1.example.com',
            'server2.example.com',
            'server3.example.com',
            'server4.example.com'
        ];
    }
    
    public function setBatchSize(int $size): void {
        $this->batchSize = $size;
    }
    
    public function deploy(array $config): array {
        $deploymentId = uniqid('rolling_', true);
        
        echo "Starting Rolling deployment: $deploymentId\n";
        echo "Batch size: {$this->batchSize}\n";
        echo "Total servers: " . count($this->servers) . "\n";
        
        try {
            $batches = array_chunk($this->servers, $this->batchSize);
            $deployedServers = [];
            $failedServers = [];
            
            foreach ($batches as $batchIndex => $batch) {
                echo "\nDeploying batch " . ($batchIndex + 1) . " of " . count($batches) . "\n";
                
                foreach ($batch as $server) {
                    echo "  Deploying to $server\n";
                    
                    try {
                        $this->deployToServer($server, $config);
                        $healthCheck = $this->runHealthCheck($server);
                        
                        if ($healthCheck['healthy']) {
                            $deployedServers[] = $server;
                            echo "    ✅ $server deployed successfully\n";
                        } else {
                            $failedServers[] = $server;
                            echo "    ❌ $server health check failed\n";
                            
                            // Stop deployment on failure
                            throw new RuntimeException("Health check failed for $server");
                        }
                        
                    } catch (Exception $e) {
                        $failedServers[] = $server;
                        echo "    ❌ $server deployment failed: " . $e->getMessage() . "\n";
                        throw $e;
                    }
                    
                    // Small delay between deployments
                    usleep(10000); // 0.01 seconds
                }
                
                // Wait between batches
                if ($batchIndex < count($batches) - 1) {
                    echo "  Waiting for batch stabilization...\n";
                    usleep(100000); // 0.1 seconds
                }
            }
            
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'strategy' => 'rolling',
                'status' => 'success',
                'deployed_at' => date('Y-m-d H:i:s'),
                'deployed_servers' => $deployedServers,
                'failed_servers' => $failedServers,
                'config' => $config
            ];
            
            echo "\nDeployment $deploymentId completed successfully\n";
            echo "Deployed servers: " . count($deployedServers) . "\n";
            echo "Failed servers: " . count($failedServers) . "\n";
            
            return [
                'success' => true,
                'deployment_id' => $deploymentId,
                'deployed_servers' => $deployedServers,
                'failed_servers' => $failedServers,
                'message' => 'Rolling deployment completed successfully'
            ];
            
        } catch (Exception $e) {
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'strategy' => 'rolling',
                'status' => 'failed',
                'deployed_at' => date('Y-m-d H:i:s'),
                'error' => $e->getMessage()
            ];
            
            return [
                'success' => false,
                'deployment_id' => $deploymentId,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function rollback(string $deploymentId): array {
        if (!isset($this->deployments[$deploymentId])) {
            return ['success' => false, 'error' => 'Deployment not found'];
        }
        
        $deployment = $this->deployments[$deploymentId];
        
        echo "Rolling back deployment: $deploymentId\n";
        
        try {
            // In a real implementation, this would revert to previous version
            foreach ($this->servers as $server) {
                echo "  Rolling back $server\n";
                $this->rollbackServer($server);
                usleep(10000); // 0.01 seconds
            }
            
            $deployment['status'] = 'rolled_back';
            $deployment['rolled_back_at'] = date('Y-m-d H:i:s');
            
            return [
                'success' => true,
                'message' => 'Rollback completed successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Rollback failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function getStatus(): array {
        $serverStatus = [];
        
        foreach ($this->servers as $server) {
            $serverStatus[$server] = $this->runHealthCheck($server);
        }
        
        return [
            'strategy' => 'rolling',
            'servers' => $this->servers,
            'batch_size' => $this->batchSize,
            'server_status' => $serverStatus,
            'deployments' => $this->deployments
        ];
    }
    
    private function deployToServer(string $server, array $config): void {
        // Simulate server deployment
        echo "    - Copying files to $server\n";
        echo "    - Installing dependencies\n";
        echo "    - Restarting services\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function rollbackServer(string $server): void {
        // Simulate server rollback
        echo "    - Reverting files on $server\n";
        echo "    - Restarting services\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function runHealthCheck(string $server): array {
        // Simulate health check
        echo "    - Checking $server health\n";
        
        usleep(10000); // 0.01 seconds
        
        // 95% chance of success for demo
        $healthy = rand(1, 100) <= 95;
        
        return [
            'healthy' => $healthy,
            'message' => $healthy ? 'Server is healthy' : 'Server health check failed',
            'response_time' => rand(50, 200) . 'ms'
        ];
    }
}

// Canary Deployment
class CanaryDeployment implements DeploymentStrategy {
    private array $servers = [];
    private array $deployments = [];
    private float $canaryTrafficPercentage = 10.0;
    
    public function __construct(array $servers = []) {
        $this->servers = $servers ?: [
            'server1.example.com',
            'server2.example.com',
            'server3.example.com',
            'server4.example.com',
            'server5.example.com'
        ];
    }
    
    public function setCanaryTrafficPercentage(float $percentage): void {
        $this->canaryTrafficPercentage = max(1.0, min(100.0, $percentage));
    }
    
    public function deploy(array $config): array {
        $deploymentId = uniqid('canary_', true);
        $canaryServers = array_slice($this->servers, 0, 1); // Start with 1 server
        
        echo "Starting Canary deployment: $deploymentId\n";
        echo "Canary traffic percentage: {$this->canaryTrafficPercentage}%\n";
        echo "Canary servers: " . implode(', ', $canaryServers) . "\n";
        
        try {
            // Phase 1: Deploy to canary servers
            echo "\nPhase 1: Deploying to canary servers\n";
            
            foreach ($canaryServers as $server) {
                echo "  Deploying to $server\n";
                $this->deployToServer($server, $config);
                $healthCheck = $this->runHealthCheck($server);
                
                if (!$healthCheck['healthy']) {
                    throw new RuntimeException("Health check failed for canary server $server");
                }
                
                echo "    ✅ $server deployed successfully\n";
            }
            
            // Phase 2: Route canary traffic
            echo "\nPhase 2: Routing {$this->canaryTrafficPercentage}% traffic to canary\n";
            $this->routeCanaryTraffic($canaryServers, $this->canaryTrafficPercentage);
            
            // Phase 3: Monitor canary performance
            echo "\nPhase 3: Monitoring canary performance\n";
            $monitoringResult = $this->monitorCanary($canaryServers);
            
            if (!$monitoringResult['success']) {
                echo "Canary monitoring failed, rolling back...\n";
                $this->rollback($deploymentId);
                throw new RuntimeException("Canary deployment failed: " . $monitoringResult['message']);
            }
            
            // Phase 4: Gradual rollout
            echo "\nPhase 4: Gradual rollout to all servers\n";
            $remainingServers = array_slice($this->servers, 1);
            
            foreach ($remainingServers as $server) {
                echo "  Deploying to $server\n";
                $this->deployToServer($server, $config);
                $healthCheck = $this->runHealthCheck($server);
                
                if (!$healthCheck['healthy']) {
                    echo "    ❌ $server health check failed\n";
                    // Continue with other servers but mark as partial failure
                } else {
                    echo "    ✅ $server deployed successfully\n";
                }
                
                usleep(10000); // 0.01 seconds
            }
            
            // Phase 5: Route all traffic
            echo "\nPhase 5: Routing 100% traffic to new version\n";
            $this->routeAllTraffic();
            
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'strategy' => 'canary',
                'status' => 'success',
                'deployed_at' => date('Y-m-d H:i:s'),
                'canary_servers' => $canaryServers,
                'config' => $config
            ];
            
            echo "\nCanary deployment $deploymentId completed successfully\n";
            
            return [
                'success' => true,
                'deployment_id' => $deploymentId,
                'message' => 'Canary deployment completed successfully'
            ];
            
        } catch (Exception $e) {
            $this->deployments[$deploymentId] = [
                'id' => $deploymentId,
                'strategy' => 'canary',
                'status' => 'failed',
                'deployed_at' => date('Y-m-d H:i:s'),
                'error' => $e->getMessage()
            ];
            
            return [
                'success' => false,
                'deployment_id' => $deploymentId,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function rollback(string $deploymentId): array {
        if (!isset($this->deployments[$deploymentId])) {
            return ['success' => false, 'error' => 'Deployment not found'];
        }
        
        $deployment = $this->deployments[$deploymentId];
        
        echo "Rolling back canary deployment: $deploymentId\n";
        
        try {
            // Route all traffic back to stable version
            echo "  Routing traffic back to stable version\n";
            $this->routeStableTraffic();
            
            // Rollback all servers
            foreach ($this->servers as $server) {
                echo "  Rolling back $server\n";
                $this->rollbackServer($server);
                usleep(10000); // 0.01 seconds
            }
            
            $deployment['status'] = 'rolled_back';
            $deployment['rolled_back_at'] = date('Y-m-d H:i:s');
            
            return [
                'success' => true,
                'message' => 'Canary rollback completed successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Canary rollback failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function getStatus(): array {
        return [
            'strategy' => 'canary',
            'servers' => $this->servers,
            'canary_traffic_percentage' => $this->canaryTrafficPercentage,
            'deployments' => $this->deployments
        ];
    }
    
    private function deployToServer(string $server, array $config): void {
        echo "    - Copying files to $server\n";
        echo "    - Installing dependencies\n";
        echo "    - Restarting services\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function rollbackServer(string $server): void {
        echo "    - Reverting files on $server\n";
        echo "    - Restarting services\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function runHealthCheck(string $server): array {
        echo "    - Checking $server health\n";
        
        usleep(10000); // 0.01 seconds
        
        // 95% chance of success for demo
        $healthy = rand(1, 100) <= 95;
        
        return [
            'healthy' => $healthy,
            'message' => $healthy ? 'Server is healthy' : 'Server health check failed'
        ];
    }
    
    private function routeCanaryTraffic(array $servers, float $percentage): void {
        echo "    - Configuring load balancer for canary traffic\n";
        echo "    - Setting traffic split: {$percentage}% to canary\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function routeAllTraffic(): void {
        echo "    - Configuring load balancer for 100% traffic\n";
        echo "    - Switching all traffic to new version\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function routeStableTraffic(): void {
        echo "    - Configuring load balancer for stable traffic\n";
        echo "    - Switching traffic back to stable version\n";
        
        usleep(20000); // 0.02 seconds
    }
    
    private function monitorCanary(array $servers): array {
        echo "    - Monitoring error rates\n";
        echo "    - Checking response times\n";
        echo "    - Analyzing user experience metrics\n";
        
        // Simulate monitoring period
        usleep(100000); // 0.1 seconds
        
        // 85% chance of success for demo (canary is more sensitive)
        $success = rand(1, 100) <= 85;
        
        return [
            'success' => $success,
            'message' => $success ? 'Canary monitoring passed' : 'Canary monitoring failed',
            'error_rate' => $success ? '0.1%' : '2.5%',
            'response_time' => $success ? '120ms' : '350ms'
        ];
    }
}

// Deployment Manager
class DeploymentManager {
    private array $strategies = [];
    private DeploymentStrategy $currentStrategy;
    private EnvironmentManager $environment;
    
    public function __construct(EnvironmentManager $environment) {
        $this->environment = $environment;
        $this->strategies = [
            'blue_green' => new BlueGreenDeployment(),
            'rolling' => new RollingDeployment(),
            'canary' => new CanaryDeployment()
        ];
        $this->currentStrategy = $this->strategies['blue_green'];
    }
    
    public function setStrategy(string $strategy): void {
        if (!isset($this->strategies[$strategy])) {
            throw new InvalidArgumentException("Unknown deployment strategy: $strategy");
        }
        
        $this->currentStrategy = $this->strategies[$strategy];
    }
    
    public function addStrategy(string $name, DeploymentStrategy $strategy): void {
        $this->strategies[$name] = $strategy;
    }
    
    public function deploy(array $config = []): array {
        if ($this->environment->isProduction()) {
            echo "Deploying to PRODUCTION environment\n";
        } else {
            echo "Deploying to {$this->environment->getCurrentEnvironment()} environment\n";
        }
        
        // Apply environment-specific settings
        $this->environment->applyPHPSettings();
        
        // Merge environment config with deployment config
        $finalConfig = array_merge($this->environment->getAllConfig(), $config);
        
        return $this->currentStrategy->deploy($finalConfig);
    }
    
    public function rollback(string $deploymentId): array {
        return $this->currentStrategy->rollback($deploymentId);
    }
    
    public function getStatus(): array {
        return array_merge([
            'environment' => $this->environment->getCurrentEnvironment(),
            'current_strategy' => get_class($this->currentStrategy),
            'available_strategies' => array_keys($this->strategies)
        ], $this->currentStrategy->getStatus());
    }
    
    public function getDeploymentHistory(): array {
        // This would typically come from a database or log files
        return [];
    }
}

// Deployment Examples
class DeploymentExamples {
    private EnvironmentManager $environment;
    private DeploymentManager $deploymentManager;
    
    public function __construct() {
        $this->environment = new EnvironmentManager('staging');
        $this->deploymentManager = new DeploymentManager($this->environment);
    }
    
    public function demonstrateEnvironmentManagement(): void {
        echo "Environment Management Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test different environments
        $environments = ['development', 'testing', 'staging', 'production'];
        
        foreach ($environments as $env) {
            echo "\nEnvironment: $env\n";
            echo str_repeat("-", 20) . "\n";
            
            $envManager = new EnvironmentManager($env);
            
            echo "Debug mode: " . ($envManager->get('debug') ? 'ON' : 'OFF') . "\n";
            echo "Error reporting: " . $envManager->get('error_reporting') . "\n";
            echo "Display errors: " . ($envManager->get('display_errors') ? 'ON' : 'OFF') . "\n";
            echo "Cache enabled: " . ($envManager->get('cache') ? 'YES' : 'NO') . "\n";
            
            $dbConfig = $envManager->get('database');
            echo "Database host: " . $dbConfig['host'] . "\n";
            echo "Database name: " . $dbConfig['name'] . "\n";
            
            echo "Is production: " . ($envManager->isProduction() ? 'YES' : 'NO') . "\n";
            echo "Is development: " . ($envManager->isDevelopment() ? 'YES' : 'NO') . "\n";
        }
    }
    
    public function demonstrateBlueGreenDeployment(): void {
        echo "\nBlue-Green Deployment Example\n";
        echo str_repeat("-", 35) . "\n";
        
        $this->deploymentManager->setStrategy('blue_green');
        
        $deploymentConfig = [
            'version' => 'v1.2.3',
            'repository' => 'https://github.com/example/app.git',
            'branch' => 'main',
            'commit' => 'abc123def456'
        ];
        
        echo "Starting Blue-Green deployment...\n";
        $result = $this->deploymentManager->deploy($deploymentConfig);
        
        if ($result['success']) {
            echo "✅ Deployment successful!\n";
            echo "Deployment ID: {$result['deployment_id']}\n";
            echo "Environment: {$result['environment']}\n";
            
            // Simulate rollback
            echo "\nSimulating rollback...\n";
            $rollbackResult = $this->deploymentManager->rollback($result['deployment_id']);
            
            if ($rollbackResult['success']) {
                echo "✅ Rollback successful!\n";
                echo "Environment: {$rollbackResult['environment']}\n";
            } else {
                echo "❌ Rollback failed: {$rollbackResult['error']}\n";
            }
        } else {
            echo "❌ Deployment failed: {$result['error']}\n";
        }
        
        // Show status
        echo "\nDeployment Status:\n";
        $status = $this->deploymentManager->getStatus();
        echo "Active environment: {$status['active_environment']}\n";
        echo "Inactive environment: {$status['inactive_environment']}\n";
        echo "Total deployments: " . count($status['deployments']) . "\n";
    }
    
    public function demonstrateRollingDeployment(): void {
        echo "\nRolling Deployment Example\n";
        echo str_repeat("-", 30) . "\n";
        
        $this->deploymentManager->setStrategy('rolling');
        
        // Set custom batch size
        $rollingStrategy = $this->deploymentManager->strategies['rolling'];
        $rollingStrategy->setBatchSize(2);
        
        $deploymentConfig = [
            'version' => 'v2.0.0',
            'repository' => 'https://github.com/example/app.git',
            'branch' => 'release',
            'commit' => 'xyz789abc456'
        ];
        
        echo "Starting Rolling deployment...\n";
        $result = $this->deploymentManager->deploy($deploymentConfig);
        
        if ($result['success']) {
            echo "✅ Deployment successful!\n";
            echo "Deployment ID: {$result['deployment_id']}\n";
            echo "Deployed servers: " . implode(', ', $result['deployed_servers']) . "\n";
            echo "Failed servers: " . (empty($result['failed_servers']) ? 'None' : implode(', ', $result['failed_servers'])) . "\n";
        } else {
            echo "❌ Deployment failed: {$result['error']}\n";
        }
        
        // Show status
        echo "\nRolling Deployment Status:\n";
        $status = $this->deploymentManager->getStatus();
        echo "Strategy: {$status['strategy']}\n";
        echo "Batch size: {$status['batch_size']}\n";
        echo "Total servers: " . count($status['servers']) . "\n";
        
        echo "Server health:\n";
        foreach ($status['server_status'] as $server => $health) {
            $statusIcon = $health['healthy'] ? '✅' : '❌';
            echo "  $statusIcon $server - {$health['message']} ({$health['response_time']})\n";
        }
    }
    
    public function demonstrateCanaryDeployment(): void {
        echo "\nCanary Deployment Example\n";
        echo str_repeat("-", 28) . "\n";
        
        $this->deploymentManager->setStrategy('canary');
        
        // Set custom canary traffic percentage
        $canaryStrategy = $this->deploymentManager->strategies['canary'];
        $canaryStrategy->setCanaryTrafficPercentage(5.0);
        
        $deploymentConfig = [
            'version' => 'v2.1.0-beta',
            'repository' => 'https://github.com/example/app.git',
            'branch' => 'feature/new-ui',
            'commit' => 'def456ghi789'
        ];
        
        echo "Starting Canary deployment...\n";
        $result = $this->deploymentManager->deploy($deploymentConfig);
        
        if ($result['success']) {
            echo "✅ Deployment successful!\n";
            echo "Deployment ID: {$result['deployment_id']}\n";
        } else {
            echo "❌ Deployment failed: {$result['error']}\n";
        }
        
        // Show status
        echo "\nCanary Deployment Status:\n";
        $status = $this->deploymentManager->getStatus();
        echo "Strategy: {$status['strategy']}\n";
        echo "Canary traffic percentage: {$status['canary_traffic_percentage']}%\n";
        echo "Total servers: " . count($status['servers']) . "\n";
    }
    
    public function demonstrateDeploymentComparison(): void {
        echo "\nDeployment Strategy Comparison\n";
        echo str_repeat("-", 35) . "\n";
        
        $strategies = ['blue_green', 'rolling', 'canary'];
        $deploymentConfig = [
            'version' => 'v1.0.0',
            'repository' => 'https://github.com/example/app.git'
        ];
        
        foreach ($strategies as $strategy) {
            echo "\n" . ucfirst($strategy) . " Deployment:\n";
            echo str_repeat("-", 25) . "\n";
            
            $this->deploymentManager->setStrategy($strategy);
            $startTime = microtime(true);
            
            $result = $this->deploymentManager->deploy($deploymentConfig);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            echo "Result: " . ($result['success'] ? '✅ Success' : '❌ Failed') . "\n";
            echo "Duration: {$duration}ms\n";
            
            if ($result['success']) {
                echo "Deployment ID: {$result['deployment_id']}\n";
            } else {
                echo "Error: {$result['error']}\n";
            }
        }
    }
    
    public function runAllExamples(): void {
        echo "Deployment Strategies Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateEnvironmentManagement();
        $this->demonstrateBlueGreenDeployment();
        $this->demonstrateRollingDeployment();
        $this->demonstrateCanaryDeployment();
        $this->demonstrateDeploymentComparison();
    }
}

// Deployment Best Practices
function printDeploymentBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Deployment Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Environment Management:\n";
    echo "   • Separate environments for each stage\n";
    echo "   • Use configuration management\n";
    echo "   • Environment-specific settings\n";
    echo "   • Secure secrets management\n";
    echo "   • Consistent environment setup\n\n";
    
    echo "2. Deployment Strategies:\n";
    echo "   • Choose strategy based on requirements\n";
    echo "   • Blue-Green for zero-downtime\n";
    echo "   • Rolling for gradual updates\n";
    echo "   • Canary for risk mitigation\n";
    echo "   • Always have rollback plans\n\n";
    
    echo "3. Automation:\n";
    echo "   • Automate deployment process\n";
    echo "   • Use CI/CD pipelines\n";
    echo "   • Implement automated testing\n";
    echo "   • Use infrastructure as code\n";
    echo "   • Monitor deployment health\n\n";
    
    echo "4. Safety Measures:\n";
    echo "   • Test in staging first\n";
    echo "   • Implement health checks\n";
    echo "   • Monitor error rates\n";
    echo "   • Set up alerting\n";
    echo "   • Document procedures\n\n";
    
    echo "5. Performance:\n";
    echo "   • Optimize deployment speed\n";
    echo "   • Minimize downtime\n";
    echo "   • Use parallel deployments\n";
    echo "   • Cache warming strategies\n";
    echo "   • Performance testing\n\n";
    
    echo "6. Security:\n";
    echo "   • Secure deployment pipelines\n";
    echo "   • Encrypt sensitive data\n";
    echo "   • Use secure connections\n";
    echo "   • Implement access controls\n";
    echo "   • Audit deployment activities";
}

// Main execution
function runDeploymentStrategiesDemo(): void {
    $examples = new DeploymentExamples();
    $examples->runAllExamples();
    printDeploymentBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDeploymentStrategiesDemo();
}
?>
