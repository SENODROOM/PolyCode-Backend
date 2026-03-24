<?php
/**
 * Build Automation and Deployment Tools
 * 
 * This file demonstrates build automation, Docker containerization,
 * CI/CD pipelines, and deployment strategies.
 */

// Build Automation Manager
class BuildAutomationManager
{
    private array $buildSteps = [];
    private array $environment = [];
    private array $artifacts = [];
    private array $logs = [];
    
    public function __construct(array $environment = [])
    {
        $this->environment = array_merge([
            'BUILD_NUMBER' => '1',
            'BUILD_ID' => date('Y-m-d_H-i-s'),
            'BRANCH' => 'main',
            'COMMIT' => 'abc123',
            'ENVIRONMENT' => 'development'
        ], $environment);
    }
    
    /**
     * Add build step
     */
    public function addStep(string $name, callable $step, array $options = []): void
    {
        $this->buildSteps[] = [
            'name' => $name,
            'step' => $step,
            'options' => array_merge([
                'timeout' => 300,
                'retry_count' => 0,
                'continue_on_error' => false,
                'parallel' => false
            ], $options)
        ];
    }
    
    /**
     * Execute build
     */
    public function executeBuild(): array
    {
        $buildId = $this->environment['BUILD_ID'];
        $startTime = microtime(true);
        
        echo "Starting build $buildId\n";
        
        $results = [];
        $success = true;
        
        foreach ($this->buildSteps as $step) {
            $stepStart = microtime(true);
            
            echo "Executing: {$step['name']}\n";
            
            try {
                $result = $this->executeStep($step);
                $step['result'] = $result;
                $step['success'] = true;
                $step['duration'] = microtime(true) - $stepStart;
                
                echo "✓ {$step['name']} completed in " . round($step['duration'], 2) . "s\n";
                
            } catch (Exception $e) {
                $step['success'] = false;
                $step['error'] = $e->getMessage();
                $step['duration'] = microtime(true) - $stepStart;
                
                echo "✗ {$step['name']} failed: {$e->getMessage()}\n";
                
                if (!$step['options']['continue_on_error']) {
                    $success = false;
                    break;
                }
            }
            
            $results[] = $step;
        }
        
        $totalDuration = microtime(true) - $startTime;
        
        $buildResult = [
            'build_id' => $buildId,
            'success' => $success,
            'duration' => $totalDuration,
            'steps' => $results,
            'artifacts' => $this->artifacts,
            'environment' => $this->environment
        ];
        
        echo "\nBuild " . ($success ? 'successful' : 'failed') . " in " . round($totalDuration, 2) . "s\n";
        
        return $buildResult;
    }
    
    /**
     * Execute individual step
     */
    private function executeStep(array $step): mixed
    {
        $timeout = $step['options']['timeout'];
        $retryCount = $step['options']['retry_count'];
        
        for ($attempt = 0; $attempt <= $retryCount; $attempt++) {
            try {
                return call_user_func($step['step'], $this->environment);
            } catch (Exception $e) {
                if ($attempt === $retryCount) {
                    throw $e;
                }
                
                echo "  Retrying {$step['name']} (attempt " . ($attempt + 2) . ")\n";
                sleep(1);
            }
        }
        
        return null;
    }
    
    /**
     * Add artifact
     */
    public function addArtifact(string $name, string $path): void
    {
        $this->artifacts[$name] = [
            'name' => $name,
            'path' => $path,
            'size' => filesize($path),
            'created_at' => time()
        ];
    }
    
    /**
     * Create build script
     */
    public function createBuildScript(): string
    {
        $script = "#!/bin/bash\n";
        $script .= "# Build script generated automatically\n";
        $script .= "BUILD_ID={$this->environment['BUILD_ID']}\n";
        $script .= "BUILD_NUMBER={$this->environment['BUILD_NUMBER']}\n";
        $script .= "BRANCH={$this->environment['BRANCH']}\n";
        $script .= "COMMIT={$this->environment['COMMIT']}\n\n";
        
        foreach ($this->buildSteps as $step) {
            $script .= "echo \"Executing: {$step['name']}\"\n";
            $script .= "# Step implementation would go here\n";
            $script .= "echo \"✓ {$step['name']} completed\"\n\n";
        }
        
        return $script;
    }
}

// Docker Container Manager
class DockerContainerManager
{
    private array $containers = [];
    private array $images = [];
    private array $networks = [];
    private array $volumes = [];
    
    /**
     * Create Docker image
     */
    public function createImage(string $name, array $config): array
    {
        $dockerfile = $this->generateDockerfile($config);
        
        $image = [
            'name' => $name,
            'tag' => $config['tag'] ?? 'latest',
            'dockerfile' => $dockerfile,
            'base_image' => $config['base_image'] ?? 'php:8.1-cli',
            'created_at' => time()
        ];
        
        $this->images[$name] = $image;
        
        echo "Docker image '$name' created\n";
        
        return $image;
    }
    
    /**
     * Generate Dockerfile
     */
    private function generateDockerfile(array $config): string
    {
        $dockerfile = "FROM {$config['base_image']}\n\n";
        
        // Set working directory
        if (isset($config['work_dir'])) {
            $dockerfile .= "WORKDIR {$config['work_dir']}\n\n";
        }
        
        // Copy files
        if (isset($config['copy'])) {
            foreach ($config['copy'] as $source => $target) {
                $dockerfile .= "COPY $source $target\n";
            }
            $dockerfile .= "\n";
        }
        
        // Install packages
        if (isset($config['packages'])) {
            $dockerfile .= "RUN apt-get update && apt-get install -y \\\n";
            foreach ($config['packages'] as $package) {
                $dockerfile .= "    $package \\\n";
            }
            $dockerfile .= "    && rm -rf /var/lib/apt/lists/*\n\n";
        }
        
        // Install PHP extensions
        if (isset($config['php_extensions'])) {
            $dockerfile .= "RUN docker-php-ext-install \\\n";
            foreach ($config['php_extensions'] as $ext) {
                $dockerfile .= "    $ext \\\n";
            }
            $dockerfile .= "\n\n";
        }
        
        // Install Composer
        if ($config['install_composer'] ?? false) {
            $dockerfile .= "COPY --from=composer:latest /usr/bin/composer /usr/bin/composer\n\n";
        }
        
        // Run Composer
        if (isset($config['composer_install'])) {
            $dockerfile .= "RUN composer install --no-dev --optimize-autoloader\n\n";
        }
        
        // Expose ports
        if (isset($config['ports'])) {
            foreach ($config['ports'] as $port) {
                $dockerfile .= "EXPOSE $port\n";
            }
            $dockerfile .= "\n";
        }
        
        // Set entrypoint
        if (isset($config['entrypoint'])) {
            $dockerfile .= "ENTRYPOINT [\"{$config['entrypoint']}\"]\n\n";
        }
        
        // Set command
        if (isset($config['command'])) {
            if (is_array($config['command'])) {
                $cmd = implode('", "', $config['command']);
                $dockerfile .= "CMD [\"$cmd\"]\n";
            } else {
                $dockerfile .= "CMD [\"{$config['command']}\"]\n";
            }
        }
        
        return $dockerfile;
    }
    
    /**
     * Create Docker Compose configuration
     */
    public function createDockerCompose(array $services): string
    {
        $compose = [
            'version' => '3.8',
            'services' => []
        ];
        
        foreach ($services as $name => $config) {
            $service = [
                'build' => $config['build'] ?? '.',
                'image' => $config['image'] ?? null,
                'container_name' => $name,
                'restart' => $config['restart'] ?? 'unless-stopped',
                'environment' => $config['environment'] ?? []
            ];
            
            if (isset($config['ports'])) {
                $service['ports'] = $config['ports'];
            }
            
            if (isset($config['volumes'])) {
                $service['volumes'] = $config['volumes'];
            }
            
            if (isset($config['depends_on'])) {
                $service['depends_on'] = $config['depends_on'];
            }
            
            if (isset($config['networks'])) {
                $service['networks'] = $config['networks'];
            }
            
            $compose['services'][$name] = $service;
        }
        
        if (isset($config['networks'])) {
            $compose['networks'] = $config['networks'];
        }
        
        if (isset($config['volumes'])) {
            $compose['volumes'] = $config['volumes'];
        }
        
        return yaml_emit($compose);
    }
    
    /**
     * Create container
     */
    public function createContainer(string $name, string $image, array $options = []): array
    {
        $container = [
            'name' => $name,
            'image' => $image,
            'status' => 'created',
            'options' => array_merge([
                'ports' => [],
                'volumes' => [],
                'environment' => [],
                'networks' => []
            ], $options),
            'created_at' => time()
        ];
        
        $this->containers[$name] = $container;
        
        echo "Container '$name' created from image '$image'\n";
        
        return $container;
    }
    
    /**
     * Start container
     */
    public function startContainer(string $name): bool
    {
        if (!isset($this->containers[$name])) {
            return false;
        }
        
        $this->containers[$name]['status'] = 'running';
        $this->containers[$name]['started_at'] = time();
        
        echo "Container '$name' started\n";
        
        return true;
    }
    
    /**
     * Stop container
     */
    public function stopContainer(string $name): bool
    {
        if (!isset($this->containers[$name])) {
            return false;
        }
        
        $this->containers[$name]['status'] = 'stopped';
        $this->containers[$name]['stopped_at'] = time();
        
        echo "Container '$name' stopped\n";
        
        return true;
    }
    
    /**
     * Get container status
     */
    public function getContainerStatus(string $name): ?array
    {
        return $this->containers[$name] ?? null;
    }
    
    /**
     * List containers
     */
    public function listContainers(): array
    {
        return $this->containers;
    }
}

// CI/CD Pipeline Manager
class CICDPipelineManager
{
    private array $pipelines = [];
    private array $stages = [];
    private arrayjobs = [];
    private array $artifacts = [];
    
    /**
     * Create pipeline
     */
    public function createPipeline(string $name, array $config): array
    {
        $pipeline = [
            'name' => $name,
            'trigger' => $config['trigger'] ?? 'push',
            'branches' => $config['branches'] ?? ['main', 'develop'],
            'variables' => $config['variables'] ?? [],
            'stages' => $config['stages'] ?? [],
            'cache' => $config['cache'] ?? [],
            'artifacts' => $config['artifacts'] ?? []
        ];
        
        $this->pipelines[$name] = $pipeline;
        
        return $pipeline;
    }
    
    /**
     * Add stage to pipeline
     */
    public function addStage(string $pipeline, string $stageName, array $jobs): void
    {
        if (!isset($this->pipelines[$pipeline])) {
            return;
        }
        
        $this->pipelines[$pipeline]['stages'][] = [
            'name' => $stageName,
            'jobs' => $jobs
        ];
    }
    
    /**
     * Create job
     */
    public function createJob(string $name, array $config): array
    {
        $job = [
            'name' => $name,
            'stage' => $config['stage'] ?? 'build',
            'script' => $config['script'] ?? [],
            'image' => $config['image'] ?? 'php:8.1',
            'variables' => $config['variables'] ?? [],
            'artifacts' => $config['artifacts'] ?? [],
            'cache' => $config['cache'] ?? [],
            'dependencies' => $config['dependencies'] ?? [],
            'rules' => $config['rules'] ?? [],
            'timeout' => $config['timeout'] ?? 3600,
            'retry' => $config['retry'] ?? 0,
            'allow_failure' => $config['allow_failure'] ?? false
        ];
        
        return $job;
    }
    
    /**
     * Generate GitLab CI configuration
     */
    public function generateGitLabCI(string $pipeline): string
    {
        if (!isset($this->pipelines[$pipeline])) {
            return '';
        }
        
        $config = $this->pipelines[$pipeline];
        $yaml = [];
        
        // Global configuration
        $yaml['stages'] = array_column($config['stages'], 'name');
        $yaml['variables'] = $config['variables'];
        
        // Cache configuration
        if (!empty($config['cache'])) {
            $yaml['cache'] = $config['cache'];
        }
        
        // Jobs
        foreach ($config['stages'] as $stage) {
            foreach ($stage['jobs'] as $job) {
                $yaml[$job['name']] = [
                    'stage' => $stage['name'],
                    'script' => $job['script'],
                    'image' => $job['image']
                ];
                
                if (!empty($job['variables'])) {
                    $yaml[$job['name']]['variables'] = $job['variables'];
                }
                
                if (!empty($job['artifacts'])) {
                    $yaml[$job['name']]['artifacts'] = $job['artifacts'];
                }
                
                if (!empty($job['dependencies'])) {
                    $yaml[$job['name']]['dependencies'] = $job['dependencies'];
                }
                
                if (!empty($job['rules'])) {
                    $yaml[$job['name']]['rules'] = $job['rules'];
                }
                
                if ($job['timeout'] !== 3600) {
                    $yaml[$job['name']]['timeout'] = $job['timeout'];
                }
                
                if ($job['retry'] > 0) {
                    $yaml[$job['name']]['retry'] = $job['retry'];
                }
                
                if ($job['allow_failure']) {
                    $yaml[$job['name']]['allow_failure'] = true;
                }
            }
        }
        
        return yaml_emit($yaml);
    }
    
    /**
     * Generate GitHub Actions workflow
     */
    public function generateGitHubActions(string $pipeline): string
    {
        if (!isset($this->pipelines[$pipeline])) {
            return '';
        }
        
        $config = $this->pipelines[$pipeline];
        
        $workflow = [
            'name' => $pipeline,
            'on' => [
                'push' => [
                    'branches' => $config['branches']
                ]
            ],
            'env' => $config['variables'],
            'jobs' => []
        ];
        
        foreach ($config['stages'] as $stage) {
            foreach ($stage['jobs'] as $job) {
                $workflow['jobs'][$job['name']] = [
                    'runs-on' => 'ubuntu-latest',
                    'container' => [
                        'image' => $job['image']
                    ],
                    'steps' => [
                        [
                            'name' => 'Checkout code',
                            'uses' => 'actions/checkout@v3'
                        ]
                    ]
                ];
                
                // Add script steps
                foreach ($job['script'] as $script) {
                    $workflow['jobs'][$job['name']]['steps'][] = [
                        'name' => 'Run script',
                        'run' => $script
                    ];
                }
                
                // Add artifacts
                if (!empty($job['artifacts'])) {
                    $workflow['jobs'][$job['name']]['steps'][] = [
                        'name' => 'Upload artifacts',
                        'uses' => 'actions/upload-artifact@v3',
                        'with' => [
                            'name' => $job['name'],
                            'path' => $job['artifacts']['paths'] ?? []
                        ]
                    ];
                }
                
                // Add dependencies
                if (!empty($job['dependencies'])) {
                    $workflow['jobs'][$job['name']]['needs'] = $job['dependencies'];
                }
            }
        }
        
        return yaml_emit($workflow);
    }
    
    /**
     * Execute pipeline
     */
    public function executePipeline(string $pipeline): array
    {
        if (!isset($this->pipelines[$pipeline])) {
            return ['error' => 'Pipeline not found'];
        }
        
        $config = $this->pipelines[$pipeline];
        $startTime = microtime(true);
        
        echo "Executing pipeline: $pipeline\n";
        
        $results = [];
        $success = true;
        
        foreach ($config['stages'] as $stage) {
            echo "\nStage: {$stage['name']}\n";
            echo str_repeat("-", strlen($stage['name']) + 7) . "\n";
            
            foreach ($stage['jobs'] as $job) {
                $jobStart = microtime(true);
                
                echo "Job: {$job['name']}\n";
                
                try {
                    // Simulate job execution
                    $jobResult = $this->executeJob($job);
                    $jobResult['duration'] = microtime(true) - $jobStart;
                    $jobResult['success'] = true;
                    
                    echo "✓ {$job['name']} completed\n";
                    
                } catch (Exception $e) {
                    $jobResult = [
                        'job' => $job,
                        'error' => $e->getMessage(),
                        'duration' => microtime(true) - $jobStart,
                        'success' => false
                    ];
                    
                    echo "✗ {$job['name']} failed: {$e->getMessage()}\n";
                    
                    if (!$job['allow_failure']) {
                        $success = false;
                        break;
                    }
                }
                
                $results[] = $jobResult;
            }
            
            if (!$success) {
                break;
            }
        }
        
        $totalDuration = microtime(true) - $startTime;
        
        return [
            'pipeline' => $pipeline,
            'success' => $success,
            'duration' => $totalDuration,
            'stages' => $config['stages'],
            'results' => $results
        ];
    }
    
    /**
     * Execute job
     */
    private function executeJob(array $job): array
    {
        // Simulate job execution
        foreach ($job['script'] as $script) {
            echo "  Running: $script\n";
            
            // Simulate script execution time
            usleep(100000); // 0.1 seconds
        }
        
        return ['job' => $job, 'output' => 'Job completed successfully'];
    }
}

// Deployment Manager
class DeploymentManager
{
    private array $environments = [];
    private array $deployments = [];
    private array $strategies = [];
    
    public function __construct()
    {
        $this->initializeStrategies();
    }
    
    /**
     * Initialize deployment strategies
     */
    private function initializeStrategies(): void
    {
        $this->strategies = [
            'blue_green' => [
                'name' => 'Blue-Green Deployment',
                'description' => 'Deploy to inactive environment, then switch traffic',
                'steps' => [
                    'Deploy to green environment',
                    'Run health checks on green',
                    'Switch traffic to green',
                    'Keep blue as backup'
                ]
            ],
            'rolling' => [
                'name' => 'Rolling Deployment',
                'description' => 'Update instances one by one',
                'steps' => [
                    'Update first instance',
                    'Wait for health check',
                    'Update next instance',
                    'Continue until all updated'
                ]
            ],
            'canary' => [
                'name' => 'Canary Deployment',
                'description' => 'Deploy to small subset first',
                'steps' => [
                    'Deploy to canary instances',
                    'Monitor metrics',
                    'Gradually increase traffic',
                    'Full deployment if successful'
                ]
            ],
            'feature_flags' => [
                'name' => 'Feature Flags',
                'description' => 'Deploy code, enable features via flags',
                'steps' => [
                    'Deploy with flags disabled',
                    'Enable flags for test users',
                    'Monitor performance',
                    'Enable for all users'
                ]
            ]
        ];
    }
    
    /**
     * Create environment
     */
    public function createEnvironment(string $name, array $config): array
    {
        $environment = [
            'name' => $name,
            'type' => $config['type'] ?? 'production',
            'servers' => $config['servers'] ?? [],
            'load_balancer' => $config['load_balancer'] ?? null,
            'database' => $config['database'] ?? null,
            'cache' => $config['cache'] ?? null,
            'monitoring' => $config['monitoring'] ?? [],
            'created_at' => time()
        ];
        
        $this->environments[$name] = $environment;
        
        echo "Environment '$name' created\n";
        
        return $environment;
    }
    
    /**
     * Deploy application
     */
    public function deploy(string $environment, string $version, string $strategy = 'rolling'): array
    {
        if (!isset($this->environments[$environment])) {
            throw new Exception("Environment '$environment' not found");
        }
        
        if (!isset($this->strategies[$strategy])) {
            throw new Exception("Strategy '$strategy' not found");
        }
        
        $deployment = [
            'id' => uniqid('deploy_'),
            'environment' => $environment,
            'version' => $version,
            'strategy' => $strategy,
            'status' => 'in_progress',
            'started_at' => time(),
            'steps' => [],
            'rollback_available' => false
        ];
        
        echo "Starting deployment of version $version to $environment using $strategy strategy\n";
        
        // Execute deployment steps
        $steps = $this->strategies[$strategy]['steps'];
        $success = true;
        
        foreach ($steps as $index => $step) {
            $stepStart = microtime(true);
            
            echo "Step " . ($index + 1) . ": $step\n";
            
            try {
                $this->executeDeploymentStep($step, $environment, $version);
                $duration = microtime(true) - $stepStart;
                
                $deployment['steps'][] = [
                    'step' => $step,
                    'status' => 'success',
                    'duration' => $duration,
                    'timestamp' => time()
                ];
                
                echo "✓ Step completed\n";
                
            } catch (Exception $e) {
                $duration = microtime(true) - $stepStart;
                
                $deployment['steps'][] = [
                    'step' => $step,
                    'status' => 'failed',
                    'duration' => $duration,
                    'error' => $e->getMessage(),
                    'timestamp' => time()
                ];
                
                echo "✗ Step failed: {$e->getMessage()}\n";
                
                $success = false;
                break;
            }
        }
        
        $deployment['status'] = $success ? 'success' : 'failed';
        $deployment['completed_at'] = time();
        $deployment['duration'] = $deployment['completed_at'] - $deployment['started_at'];
        
        if ($success) {
            $deployment['rollback_available'] = true;
            echo "\n✓ Deployment successful\n";
        } else {
            echo "\n✗ Deployment failed\n";
        }
        
        $this->deployments[] = $deployment;
        
        return $deployment;
    }
    
    /**
     * Execute deployment step
     */
    private function executeDeploymentStep(string $step, string $environment, string $version): void
    {
        // Simulate step execution
        switch ($step) {
            case strpos($step, 'Deploy') !== false:
                echo "  Deploying code to servers...\n";
                sleep(1);
                break;
                
            case strpos($step, 'health') !== false:
                echo "  Running health checks...\n";
                sleep(0.5);
                break;
                
            case strpos($step, 'traffic') !== false:
                echo "  Switching traffic...\n";
                sleep(0.5);
                break;
                
            case strpos($step, 'Monitor') !== false:
                echo "  Monitoring metrics...\n";
                sleep(1);
                break;
                
            default:
                echo "  Executing step...\n";
                sleep(0.5);
        }
    }
    
    /**
     * Rollback deployment
     */
    public function rollback(string $deploymentId): array
    {
        $deployment = null;
        
        foreach ($this->deployments as $d) {
            if ($d['id'] === $deploymentId) {
                $deployment = $d;
                break;
            }
        }
        
        if (!$deployment) {
            throw new Exception("Deployment '$deploymentId' not found");
        }
        
        if (!$deployment['rollback_available']) {
            throw new Exception("Rollback not available for deployment '$deploymentId'");
        }
        
        echo "Rolling back deployment $deploymentId\n";
        
        $rollback = [
            'deployment_id' => $deploymentId,
            'status' => 'in_progress',
            'started_at' => time()
        ];
        
        // Simulate rollback
        sleep(2);
        
        $rollback['status'] = 'success';
        $rollback['completed_at'] = time();
        $rollback['duration'] = $rollback['completed_at'] - $rollback['started_at'];
        
        echo "✓ Rollback completed\n";
        
        return $rollback;
    }
    
    /**
     * Get deployment history
     */
    public function getDeploymentHistory(string $environment = null): array
    {
        if ($environment) {
            return array_filter($this->deployments, fn($d) => $d['environment'] === $environment);
        }
        
        return $this->deployments;
    }
    
    /**
     * Get environment status
     */
    public function getEnvironmentStatus(string $environment): array
    {
        if (!isset($this->environments[$environment])) {
            throw new Exception("Environment '$environment' not found");
        }
        
        $env = $this->environments[$environment];
        $deployments = $this->getDeploymentHistory($environment);
        $latestDeployment = end($deployments);
        
        return [
            'environment' => $env,
            'latest_deployment' => $latestDeployment,
            'status' => $latestDeployment['status'] ?? 'no_deployment',
            'version' => $latestDeployment['version'] ?? null
        ];
    }
}

// Build and Deployment Examples
class BuildDeploymentExamples
{
    private BuildAutomationManager $buildManager;
    private DockerContainerManager $dockerManager;
    private CICDPipelineManager $cicdManager;
    private DeploymentManager $deploymentManager;
    
    public function __construct()
    {
        $this->buildManager = new BuildAutomationManager();
        $this->dockerManager = new DockerContainerManager();
        $this->cicdManager = new CICDPipelineManager();
        $this->deploymentManager = new DeploymentManager();
    }
    
    public function demonstrateBuildAutomation(): void
    {
        echo "Build Automation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Add build steps
        $this->buildManager->addStep('Install Dependencies', function($env) {
            echo "  Running composer install...\n";
            return 'Dependencies installed';
        });
        
        $this->buildManager->addStep('Run Tests', function($env) {
            echo "  Running PHPUnit tests...\n";
            return 'Tests passed';
        });
        
        $this->buildManager->addStep('Code Quality Check', function($env) {
            echo "  Running PHPStan analysis...\n";
            return 'Code quality OK';
        });
        
        $this->buildManager->addStep('Build Assets', function($env) {
            echo "  Building frontend assets...\n";
            return 'Assets built';
        });
        
        $this->buildManager->addStep('Package Application', function($env) {
            echo "  Creating deployment package...\n";
            return 'Package created';
        });
        
        // Execute build
        $result = $this->buildManager->executeBuild();
        
        echo "\nBuild Summary:\n";
        echo "Build ID: {$result['build_id']}\n";
        echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: " . round($result['duration'], 2) . "s\n";
        echo "Steps: " . count($result['steps']) . "\n";
        
        // Generate build script
        $script = $this->buildManager->createBuildScript();
        echo "\nGenerated Build Script:\n";
        echo substr($script, 0, 300) . "...\n";
    }
    
    public function demonstrateDocker(): void
    {
        echo "\nDocker Container Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create Docker image
        $imageConfig = [
            'base_image' => 'php:8.1-apache',
            'work_dir' => '/var/www/html',
            'copy' => [
                '.' => '/var/www/html/'
            ],
            'packages' => ['git', 'zip', 'unzip'],
            'php_extensions' => ['pdo', 'mysqli', 'gd', 'curl'],
            'install_composer' => true,
            'composer_install' => true,
            'ports' => [80],
            'command' => 'apache2-foreground'
        ];
        
        $image = $this->dockerManager->createImage('my-app', $imageConfig);
        
        echo "Docker Image:\n";
        echo "Name: {$image['name']}\n";
        echo "Tag: {$image['tag']}\n";
        echo "Base Image: {$image['base_image']}\n";
        
        echo "\nDockerfile:\n";
        echo $image['dockerfile'];
        
        // Create Docker Compose
        $services = [
            'web' => [
                'build' => '.',
                'ports' => ['8080:80'],
                'environment' => [
                    'APP_ENV' => 'production'
                ],
                'volumes' => [
                    '.:/var/www/html'
                ],
                'depends_on' => ['database']
            ],
            'database' => [
                'image' => 'mysql:8.0',
                'environment' => [
                    'MYSQL_ROOT_PASSWORD' => 'secret',
                    'MYSQL_DATABASE' => 'myapp'
                ],
                'volumes' => [
                    'db_data:/var/lib/mysql'
                ]
            ]
        ];
        
        $compose = $this->dockerManager->createDockerCompose($services);
        
        echo "\nDocker Compose:\n";
        echo substr($compose, 0, 400) . "...\n";
        
        // Create and manage containers
        $container = $this->dockerManager->createContainer('my-app-container', 'my-app:latest');
        $this->dockerManager->startContainer('my-app-container');
        
        $status = $this->dockerManager->getContainerStatus('my-app-container');
        echo "\nContainer Status:\n";
        echo "Name: {$status['name']}\n";
        echo "Image: {$status['image']}\n";
        echo "Status: {$status['status']}\n";
        
        $this->dockerManager->stopContainer('my-app-container');
    }
    
    public function demonstrateCICD(): void
    {
        echo "\nCI/CD Pipeline Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create pipeline
        $pipelineConfig = [
            'trigger' => 'push',
            'branches' => ['main', 'develop'],
            'variables' => [
                'APP_ENV' => 'production',
                'NODE_VERSION' => '16'
            ],
            'cache' => [
                'paths' => ['vendor/', 'node_modules/']
            ]
        ];
        
        $this->cicdManager->createPipeline('my-app-pipeline', $pipelineConfig);
        
        // Create jobs
        $buildJob = $this->cicdManager->createJob('build', [
            'stage' => 'build',
            'script' => [
                'composer install --no-dev',
                'npm install',
                'npm run build'
            ],
            'artifacts' => [
                'paths' => ['dist/', 'vendor/']
            ]
        ]);
        
        $testJob = $this->cicdManager->createJob('test', [
            'stage' => 'test',
            'script' => [
                'vendor/bin/phpunit',
                'npm run test'
            ],
            'dependencies' => ['build']
        ]);
        
        $deployJob = $this->cicdManager->createJob('deploy', [
            'stage' => 'deploy',
            'script' => [
                'rsync -av dist/ user@server:/var/www/html/',
                'ssh user@server "systemctl reload apache2"'
            ],
            'dependencies' => ['test'],
            'rules' => [
                ['if' => '$CI_COMMIT_BRANCH == "main"']
            ]
        ]);
        
        // Add stages
        $this->cicdManager->addStage('my-app-pipeline', 'build', [$buildJob]);
        $this->cicdManager->addStage('my-app-pipeline', 'test', [$testJob]);
        $this->cicdManager->addStage('my-app-pipeline', 'deploy', [$deployJob]);
        
        // Generate configurations
        $gitlabCI = $this->cicdManager->generateGitLabCI('my-app-pipeline');
        echo "GitLab CI Configuration:\n";
        echo substr($gitlabCI, 0, 400) . "...\n\n";
        
        $githubActions = $this->cicdManager->generateGitHubActions('my-app-pipeline');
        echo "GitHub Actions Workflow:\n";
        echo substr($githubActions, 0, 400) . "...\n\n";
        
        // Execute pipeline
        $result = $this->cicdManager->executePipeline('my-app-pipeline');
        
        echo "Pipeline Execution:\n";
        echo "Pipeline: {$result['pipeline']}\n";
        echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: " . round($result['duration'], 2) . "s\n";
        echo "Stages: " . count($result['stages']) . "\n";
        echo "Results: " . count($result['results']) . "\n";
    }
    
    public function demonstrateDeployment(): void
    {
        echo "\nDeployment Examples\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create environments
        $prodConfig = [
            'type' => 'production',
            'servers' => [
                ['host' => 'prod1.example.com', 'role' => 'web'],
                ['host' => 'prod2.example.com', 'role' => 'web']
            ],
            'load_balancer' => 'lb.example.com',
            'database' => 'prod-db.example.com',
            'cache' => 'prod-cache.example.com'
        ];
        
        $stagingConfig = [
            'type' => 'staging',
            'servers' => [
                ['host' => 'staging.example.com', 'role' => 'web']
            ],
            'database' => 'staging-db.example.com'
        ];
        
        $this->deploymentManager->createEnvironment('production', $prodConfig);
        $this->deploymentManager->createEnvironment('staging', $stagingConfig);
        
        // Show deployment strategies
        echo "Deployment Strategies:\n";
        foreach ($this->deploymentManager->strategies as $key => $strategy) {
            echo "\n{$strategy['name']}:\n";
            echo "Description: {$strategy['description']}\n";
            echo "Steps:\n";
            foreach ($strategy['steps'] as $step) {
                echo "  - $step\n";
            }
        }
        
        // Deploy to staging
        echo "\nDeploying to staging...\n";
        $stagingDeploy = $this->deploymentManager->deploy('staging', '1.2.3', 'rolling');
        
        echo "Staging Deployment:\n";
        echo "ID: {$stagingDeploy['id']}\n";
        echo "Status: {$stagingDeploy['status']}\n";
        echo "Duration: " . round($stagingDeploy['duration'], 2) . "s\n";
        
        // Deploy to production
        echo "\nDeploying to production...\n";
        $prodDeploy = $this->deploymentManager->deploy('production', '1.2.3', 'blue_green');
        
        echo "Production Deployment:\n";
        echo "ID: {$prodDeploy['id']}\n";
        echo "Status: {$prodDeploy['status']}\n";
        echo "Duration: " . round($prodDeploy['duration'], 2) . "s\n";
        
        // Get environment status
        $prodStatus = $this->deploymentManager->getEnvironmentStatus('production');
        echo "\nProduction Environment Status:\n";
        echo "Status: {$prodStatus['status']}\n";
        echo "Version: {$prodStatus['version']}\n";
        echo "Latest Deployment: {$prodStatus['latest_deployment']['id']}\n";
        
        // Show deployment history
        $history = $this->deploymentManager->getDeploymentHistory();
        echo "\nDeployment History:\n";
        foreach ($history as $deploy) {
            echo "{$deploy['environment']} - {$deploy['version']} - {$deploy['status']} - " . date('Y-m-d H:i:s', $deploy['started_at']) . "\n";
        }
    }
    
    public function runAllExamples(): void
    {
        echo "Build and Deployment Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateBuildAutomation();
        $this->demonstrateDocker();
        $this->demonstrateCICD();
        $this->demonstrateDeployment();
    }
}

// Build and Deployment Best Practices
function printBuildDeploymentBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Build and Deployment Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Build Automation:\n";
    echo "   • Use consistent build processes\n";
    echo "   • Implement proper error handling\n";
    echo "   • Cache build dependencies\n";
    echo "   • Parallelize build steps\n";
    echo "   • Generate build artifacts\n\n";
    
    echo "2. Docker Containerization:\n";
    echo "   • Use minimal base images\n";
    echo "   • Layer Dockerfile efficiently\n";
    echo "   • Use multi-stage builds\n";
    echo "   • Optimize image size\n";
    echo "   • Security scan images\n\n";
    
    echo "3. CI/CD Pipelines:\n";
    echo "   • Automate testing and deployment\n";
    echo "   • Use environment-specific configs\n";
    echo "   • Implement proper branching\n";
    echo "   • Monitor pipeline performance\n";
    echo "   • Use proper artifact management\n\n";
    
    echo "4. Deployment Strategies:\n";
    echo "   • Choose appropriate strategy\n";
    echo "   • Implement health checks\n";
    echo "   • Use canary releases\n";
    echo "   • Plan rollback procedures\n";
    echo "   • Monitor deployment metrics\n\n";
    
    echo "5. Environment Management:\n";
    echo "   • Separate environments\n";
    echo "   • Use infrastructure as code\n";
    echo "   • Implement proper secrets management\n";
    echo "   • Monitor environment health\n";
    echo "   • Document environment configs\n\n";
    
    echo "6. Security:\n";
    echo "   • Scan dependencies\n";
    echo "   • Use secure build processes\n";
    echo "   • Implement access controls\n";
    echo "   • Monitor security alerts\n";
    echo "   • Keep systems updated";
}

// Main execution
function runBuildDeploymentDemo(): void
{
    $examples = new BuildDeploymentExamples();
    $examples->runAllExamples();
    printBuildDeploymentBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runBuildDeploymentDemo();
}
?>
