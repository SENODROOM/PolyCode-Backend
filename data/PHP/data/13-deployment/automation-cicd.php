<?php
/**
 * CI/CD Automation and Continuous Integration
 * 
 * This file demonstrates continuous integration, automated testing,
 * deployment pipelines, and DevOps practices for PHP applications.
 */

// CI/CD Pipeline Manager
class CICDPipeline {
    private string $name;
    private array $stages = [];
    private array $environment = [];
    private array $artifacts = [];
    private array $notifications = [];
    
    public function __construct(string $name) {
        $this->name = $name;
        $this->initializeDefaultStages();
    }
    
    private function initializeDefaultStages(): void {
        $this->stages = [
            'checkout' => [
                'name' => 'Source Code Checkout',
                'commands' => [
                    'git clone $REPOSITORY_URL .',
                    'git checkout $BRANCH',
                    'git pull origin $BRANCH'
                ],
                'timeout' => 300,
                'retry' => 3
            ],
            'setup' => [
                'name' => 'Environment Setup',
                'commands' => [
                    'composer install --no-interaction',
                    'npm install',
                    'cp .env.example .env',
                    'php artisan key:generate'
                ],
                'timeout' => 600,
                'retry' => 2
            ],
            'test' => [
                'name' => 'Run Tests',
                'commands' => [
                    'php vendor/bin/phpunit --coverage-clover=coverage.xml',
                    'npm run test',
                    'php vendor/bin/phpcs --standard=PSR12 src/',
                    'php vendor/bin/phpstan analyse src/'
                ],
                'timeout' => 1200,
                'retry' => 1
            ],
            'build' => [
                'name' => 'Build Application',
                'commands' => [
                    'npm run build',
                    'php artisan config:cache',
                    'php artisan route:cache',
                    'php artisan view:cache'
                ],
                'timeout' => 600,
                'retry' => 1
            ],
            'security' => [
                'name' => 'Security Scan',
                'commands' => [
                    'php vendor/bin/security-checker security:check',
                    'npm audit',
                    'composer audit'
                ],
                'timeout' => 300,
                'retry' => 1
            ],
            'package' => [
                'name' => 'Package Artifacts',
                'commands' => [
                    'tar -czf build.tar.gz .',
                    'docker build -t $IMAGE_NAME:$BUILD_NUMBER .',
                    'docker tag $IMAGE_NAME:$BUILD_NUMBER $IMAGE_NAME:latest'
                ],
                'timeout' => 900,
                'retry' => 1
            ],
            'deploy' => [
                'name' => 'Deploy to Server',
                'commands' => [
                    'docker push $IMAGE_NAME:$BUILD_NUMBER',
                    'kubectl apply -f k8s/deployment.yaml',
                    'kubectl rollout status deployment/$APP_NAME'
                ],
                'timeout' => 1800,
                'retry' => 2
            ]
        ];
    }
    
    public function addStage(string $name, array $stage): void {
        $this->stages[$name] = $stage;
    }
    
    public function removeStage(string $name): void {
        unset($this->stages[$name]);
    }
    
    public function setEnvironmentVariable(string $key, string $value): void {
        $this->environment[$key] = $value;
    }
    
    public function addNotification(string $type, array $config): void {
        $this->notifications[$type] = $config;
    }
    
    public function execute(): array {
        $pipelineId = uniqid('pipeline_', true);
        $startTime = microtime(true);
        
        echo "Executing CI/CD Pipeline: {$this->name}\n";
        echo "Pipeline ID: $pipelineId\n";
        echo "Environment: " . ($this->environment['ENVIRONMENT'] ?? 'development') . "\n";
        echo "Branch: " . ($this->environment['BRANCH'] ?? 'main') . "\n\n";
        
        $results = [
            'pipeline_id' => $pipelineId,
            'name' => $this->name,
            'start_time' => $startTime,
            'stages' => [],
            'status' => 'running',
            'artifacts' => []
        ];
        
        foreach ($this->stages as $stageName => $stage) {
            echo "Stage: {$stage['name']}\n";
            echo str_repeat("-", 30) . "\n";
            
            $stageResult = $this->executeStage($stageName, $stage);
            $results['stages'][$stageName] = $stageResult;
            
            if ($stageResult['status'] === 'failed') {
                echo "❌ Stage failed: {$stageResult['error']}\n";
                $results['status'] = 'failed';
                $this->sendNotification('pipeline_failed', [
                    'pipeline' => $this->name,
                    'stage' => $stageName,
                    'error' => $stageResult['error']
                ]);
                break;
            }
            
            echo "✅ Stage completed in {$stageResult['duration']}s\n\n";
        }
        
        $endTime = microtime(true);
        $results['end_time'] = $endTime;
        $results['total_duration'] = $endTime - $startTime;
        $results['status'] = $results['status'] === 'failed' ? 'failed' : 'success';
        
        if ($results['status'] === 'success') {
            echo "🎉 Pipeline completed successfully!\n";
            $this->sendNotification('pipeline_success', [
                'pipeline' => $this->name,
                'duration' => round($results['total_duration'], 2)
            ]);
        } else {
            echo "💥 Pipeline failed!\n";
        }
        
        echo "Total duration: " . round($results['total_duration'], 2) . "s\n";
        
        return $results;
    }
    
    private function executeStage(string $stageName, array $stage): array {
        $startTime = microtime(true);
        $result = [
            'name' => $stage['name'],
            'start_time' => $startTime,
            'commands' => [],
            'status' => 'running',
            'duration' => 0
        ];
        
        foreach ($stage['commands'] as $command) {
            // Replace environment variables
            $processedCommand = $this->processCommand($command);
            
            echo "  Executing: $processedCommand\n";
            
            $commandResult = $this->executeCommand($processedCommand, $stage['timeout'] ?? 300);
            $result['commands'][] = $commandResult;
            
            if (!$commandResult['success']) {
                $result['status'] = 'failed';
                $result['error'] = $commandResult['error'];
                $result['end_time'] = microtime(true);
                $result['duration'] = $result['end_time'] - $startTime;
                return $result;
            }
            
            echo "    ✅ Success\n";
        }
        
        $result['status'] = 'success';
        $result['end_time'] = microtime(true);
        $result['duration'] = $result['end_time'] - $startTime;
        
        return $result;
    }
    
    private function processCommand(string $command): string {
        foreach ($this->environment as $key => $value) {
            $command = str_replace('$' . $key, $value, $command);
        }
        return $command;
    }
    
    private function executeCommand(string $command, int $timeout): array {
        // Simulate command execution
        $executionTime = rand(1, 10);
        
        // Simulate failure for demo (10% chance)
        $success = rand(1, 100) > 10;
        
        if ($executionTime > $timeout) {
            return [
                'success' => false,
                'error' => 'Command timeout',
                'duration' => $timeout,
                'output' => ''
            ];
        }
        
        usleep($executionTime * 100000); // Convert to microseconds
        
        return [
            'success' => $success,
            'error' => $success ? null : 'Command failed',
            'duration' => $executionTime,
            'output' => $success ? 'Command output' : 'Error output'
        ];
    }
    
    private function sendNotification(string $type, array $data): void {
        if (!isset($this->notifications[$type])) {
            return;
        }
        
        $notification = $this->notifications[$type];
        
        echo "📧 Sending notification: $type\n";
        
        switch ($notification['type'] ?? 'webhook') {
            case 'webhook':
                $this->sendWebhook($notification['url'], $data);
                break;
            case 'email':
                $this->sendEmail($notification['to'], $notification['subject'], $data);
                break;
            case 'slack':
                $this->sendSlack($notification['webhook'], $data);
                break;
        }
    }
    
    private function sendWebhook(string $url, array $data): void {
        // Simulate webhook call
        echo "  Webhook URL: $url\n";
        echo "  Payload: " . json_encode($data) . "\n";
    }
    
    private function sendEmail(string $to, string $subject, array $data): void {
        // Simulate email sending
        echo "  To: $to\n";
        echo "  Subject: $subject\n";
        echo "  Data: " . json_encode($data) . "\n";
    }
    
    private function sendSlack(string $webhook, array $data): void {
        // Simulate Slack notification
        echo "  Slack webhook: $webhook\n";
        echo "  Message: " . json_encode($data) . "\n";
    }
    
    public function getPipelineDefinition(): array {
        return [
            'name' => $this->name,
            'stages' => $this->stages,
            'environment' => $this->environment,
            'notifications' => $this->notifications
        ];
    }
}

// Automated Test Runner
class AutomatedTestRunner {
    private array $testSuites = [];
    private array $results = [];
    private array $coverage = [];
    
    public function __construct() {
        $this->initializeTestSuites();
    }
    
    private function initializeTestSuites(): void {
        $this->testSuites = [
            'unit' => [
                'name' => 'Unit Tests',
                'command' => 'php vendor/bin/phpunit --testsuite=Unit',
                'timeout' => 300,
                'required' => true
            ],
            'integration' => [
                'name' => 'Integration Tests',
                'command' => 'php vendor/bin/phpunit --testsuite=Integration',
                'timeout' => 600,
                'required' => true
            ],
            'feature' => [
                'name' => 'Feature Tests',
                'command' => 'php vendor/bin/phpunit --testsuite=Feature',
                'timeout' => 900,
                'required' => false
            ],
            'browser' => [
                'name' => 'Browser Tests',
                'command' => 'npm run test:browser',
                'timeout' => 1200,
                'required' => false
            ],
            'performance' => [
                'name' => 'Performance Tests',
                'command' => 'npm run test:performance',
                'timeout' => 600,
                'required' => false
            ],
            'security' => [
                'name' => 'Security Tests',
                'command' => 'php vendor/bin/phpunit --testsuite=Security',
                'timeout' => 300,
                'required' => true
            ]
        ];
    }
    
    public function addTestSuite(string $name, array $config): void {
        $this->testSuites[$name] = $config;
    }
    
    public function runTests(array $testSuites = null): array {
        $testSuites = $testSuites ?: array_keys($this->testSuites);
        $startTime = microtime(true);
        
        echo "Running Automated Tests\n";
        echo str_repeat("-", 25) . "\n";
        
        $results = [
            'start_time' => $startTime,
            'test_suites' => [],
            'overall_status' => 'running',
            'total_tests' => 0,
            'passed_tests' => 0,
            'failed_tests' => 0,
            'skipped_tests' => 0
        ];
        
        foreach ($testSuites as $suiteName) {
            if (!isset($this->testSuites[$suiteName])) {
                continue;
            }
            
            $suite = $this->testSuites[$suiteName];
            
            echo "Running {$suite['name']}...\n";
            
            $suiteResult = $this->runTestSuite($suiteName, $suite);
            $results['test_suites'][$suiteName] = $suiteResult;
            
            $results['total_tests'] += $suiteResult['total'];
            $results['passed_tests'] += $suiteResult['passed'];
            $results['failed_tests'] += $suiteResult['failed'];
            $results['skipped_tests'] += $suiteResult['skipped'];
            
            if ($suite['required'] && $suiteResult['status'] === 'failed') {
                echo "❌ Required test suite failed\n";
                $results['overall_status'] = 'failed';
                break;
            }
            
            echo "✅ {$suite['name']} completed\n";
            echo "   Passed: {$suiteResult['passed']}, Failed: {$suiteResult['failed']}, Skipped: {$suiteResult['skipped']}\n\n";
        }
        
        $endTime = microtime(true);
        $results['end_time'] = $endTime;
        $results['duration'] = $endTime - $startTime;
        $results['overall_status'] = $results['overall_status'] === 'failed' ? 'failed' : 'success';
        
        // Generate coverage report
        $this->generateCoverageReport($results);
        
        echo "Test Results Summary:\n";
        echo "Total Tests: {$results['total_tests']}\n";
        echo "Passed: {$results['passed_tests']}\n";
        echo "Failed: {$results['failed_tests']}\n";
        echo "Skipped: {$results['skipped_tests']}\n";
        echo "Duration: " . round($results['duration'], 2) . "s\n";
        echo "Status: " . ucfirst($results['overall_status']) . "\n";
        
        if (isset($this->coverage['percentage'])) {
            echo "Coverage: {$this->coverage['percentage']}%\n";
        }
        
        return $results;
    }
    
    private function runTestSuite(string $suiteName, array $suite): array {
        $startTime = microtime(true);
        
        // Simulate test execution
        $executionTime = rand(10, 60);
        usleep($executionTime * 10000); // Convert to microseconds
        
        // Generate random test results
        $total = rand(10, 100);
        $passed = rand($total * 0.8, $total);
        $failed = $total - $passed;
        $skipped = rand(0, 5);
        
        $result = [
            'name' => $suite['name'],
            'command' => $suite['command'],
            'start_time' => $startTime,
            'end_time' => microtime(true),
            'duration' => $executionTime,
            'total' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'skipped' => $skipped,
            'status' => $failed > 0 ? 'failed' : 'success',
            'coverage' => rand(70, 95)
        ];
        
        // Required test suites have higher success rate
        if ($suite['required']) {
            $result['failed'] = rand(0, 2);
            $result['passed'] = $total - $result['failed'];
            $result['status'] = $result['failed'] > 0 ? 'failed' : 'success';
        }
        
        return $result;
    }
    
    private function generateCoverageReport(array $results): void {
        $totalCoverage = 0;
        $suiteCount = 0;
        
        foreach ($results['test_suites'] as $suite) {
            if (isset($suite['coverage'])) {
                $totalCoverage += $suite['coverage'];
                $suiteCount++;
            }
        }
        
        if ($suiteCount > 0) {
            $this->coverage = [
                'percentage' => round($totalCoverage / $suiteCount, 2),
                'threshold' => 80.0,
                'status' => ($totalCoverage / $suiteCount) >= 80 ? 'passed' : 'failed'
            ];
        }
    }
    
    public function getResults(): array {
        return [
            'test_suites' => $this->testSuites,
            'results' => $this->results,
            'coverage' => $this->coverage
        ];
    }
}

// Docker Build Manager
class DockerBuildManager {
    private string $imageName;
    private string $tag;
    private array $buildArgs = [];
    private array $dockerfile = [];
    
    public function __construct(string $imageName, string $tag = 'latest') {
        $this->imageName = $imageName;
        $this->tag = $tag;
        $this->initializeDockerfile();
    }
    
    private function initializeDockerfile(): void {
        $this->dockerfile = [
            'FROM php:8.1-fpm-alpine',
            'WORKDIR /var/www/html',
            'RUN apk add --no-cache libzip-dev libpng-dev libwebp-dev',
            'RUN docker-php-ext-configure gd --with-webp',
            'RUN docker-php-ext-install gd zip pdo_mysql bcmath',
            'RUN pecl install redis',
            'RUN docker-php-ext-enable redis',
            'COPY composer.json composer.lock ./',
            'RUN composer install --no-interaction --no-dev --optimize-autoloader',
            'COPY . .',
            'RUN chown -R www-data:www-data /var/www/html',
            'RUN chmod -R 755 /var/www/html/storage',
            'EXPOSE 9000',
            'CMD ["php-fpm"]'
        ];
    }
    
    public function addDockerfileInstruction(string $instruction): void {
        $this->dockerfile[] = $instruction;
    }
    
    public function addBuildArg(string $name, string $value): void {
        $this->buildArgs[$name] = $value;
    }
    
    public function generateDockerfile(): string {
        return implode("\n", $this->dockerfile);
    }
    
    public function build(): array {
        echo "Building Docker Image: {$this->imageName}:{$this->tag}\n";
        echo str_repeat("-", 40) . "\n";
        
        $startTime = microtime(true);
        
        // Generate Dockerfile
        $dockerfileContent = $this->generateDockerfile();
        echo "Dockerfile generated:\n";
        echo substr($dockerfileContent, 0, 200) . "...\n\n";
        
        // Simulate build process
        $buildSteps = [
            'Sending build context to daemon',
            'Step 1/12 : FROM php:8.1-fpm-alpine',
            'Step 2/12 : WORKDIR /var/www/html',
            'Step 3/12 : RUN apk add --no-cache libzip-dev libpng-dev libwebp-dev',
            'Step 4/12 : RUN docker-php-ext-configure gd --with-webp',
            'Step 5/12 : RUN docker-php-ext-install gd zip pdo_mysql bcmath',
            'Step 6/12 : RUN pecl install redis',
            'Step 7/12 : RUN docker-php-ext-enable redis',
            'Step 8/12 : COPY composer.json composer.lock ./',
            'Step 9/12 : RUN composer install --no-interaction --no-dev',
            'Step 10/12 : COPY . .',
            'Step 11/12 : RUN chown -R www-data:www-data /var/www/html',
            'Step 12/12 : CMD ["php-fpm"]'
        ];
        
        foreach ($buildSteps as $step) {
            echo "$step\n";
            usleep(100000); // 0.1 seconds
            echo "  ✅ Success\n";
        }
        
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        $imageId = 'sha256:' . substr(md5($this->imageName . $this->tag), 0, 12);
        $size = rand(100, 500) . 'MB';
        
        echo "\nBuild completed successfully!\n";
        echo "Image ID: $imageId\n";
        echo "Size: $size\n";
        echo "Duration: " . round($duration, 2) . "s\n";
        
        return [
            'success' => true,
            'image_name' => "{$this->imageName}:{$this->tag}",
            'image_id' => $imageId,
            'size' => $size,
            'duration' => $duration
        ];
    }
    
    public function push(string $registry = null): array {
        $registry = $registry ?: 'docker.io';
        $fullImageName = "{$registry}/{$this->imageName}:{$this->tag}";
        
        echo "Pushing Docker Image: $fullImageName\n";
        echo str_repeat("-", 40) . "\n";
        
        $startTime = microtime(true);
        
        // Simulate push process
        $pushSteps = [
            'The push refers to repository',
            'Preparing build context',
            'Layer 1/10 : FROM php:8.1-fpm-alpine',
            'Layer 2/10 : WORKDIR /var/www/html',
            'Layer 3/10 : RUN apk add --no-cache',
            'Layer 4/10 : RUN docker-php-ext-install',
            'Layer 5/10 : RUN pecl install',
            'Layer 6/10 : COPY composer.json',
            'Layer 7/10 : RUN composer install',
            'Layer 8/10 : COPY . .',
            'Layer 9/10 : RUN chown -R www-data',
            'Layer 10/10 : CMD ["php-fpm"]'
        ];
        
        foreach ($pushSteps as $step) {
            echo "$step\n";
            usleep(50000); // 0.05 seconds
            echo "  ✅ Pushed\n";
        }
        
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        echo "\nPush completed successfully!\n";
        echo "Duration: " . round($duration, 2) . "s\n";
        
        return [
            'success' => true,
            'image_name' => $fullImageName,
            'duration' => $duration
        ];
    }
}

// Kubernetes Deployment Manager
class KubernetesDeploymentManager {
    private string $appName;
    private string $namespace;
    private array $deployment = [];
    private array $service = [];
    private array $ingress = [];
    
    public function __construct(string $appName, string $namespace = 'default') {
        $this->appName = $appName;
        $this->namespace = $namespace;
        $this->initializeKubernetesResources();
    }
    
    private function initializeKubernetesResources(): void {
        $this->deployment = [
            'apiVersion' => 'apps/v1',
            'kind' => 'Deployment',
            'metadata' => [
                'name' => $this->appName,
                'namespace' => $this->namespace,
                'labels' => [
                    'app' => $this->appName,
                    'version' => 'v1.0.0'
                ]
            ],
            'spec' => [
                'replicas' => 3,
                'selector' => [
                    'matchLabels' => [
                        'app' => $this->appName
                    ]
                ],
                'template' => [
                    'metadata' => [
                        'labels' => [
                            'app' => $this->appName,
                            'version' => 'v1.0.0'
                        ]
                    ],
                    'spec' => [
                        'containers' => [
                            [
                                'name' => $this->appName,
                                'image' => 'nginx:latest',
                                'ports' => [
                                    [
                                        'containerPort' => 80,
                                        'protocol' => 'TCP'
                                    ]
                                ],
                                'resources' => [
                                    'requests' => [
                                        'memory' => '128Mi',
                                        'cpu' => '100m'
                                    ],
                                    'limits' => [
                                        'memory' => '256Mi',
                                        'cpu' => '200m'
                                    ]
                                ],
                                'env' => [
                                    [
                                        'name' => 'APP_ENV',
                                        'value' => 'production'
                                    ]
                                ],
                                'livenessProbe' => [
                                    'httpGet' => [
                                        'path' => '/health',
                                        'port' => 80
                                    ],
                                    'initialDelaySeconds' => 30,
                                    'periodSeconds' => 10
                                ],
                                'readinessProbe' => [
                                    'httpGet' => [
                                        'path' => '/ready',
                                        'port' => 80
                                    ],
                                    'initialDelaySeconds' => 5,
                                    'periodSeconds' => 5
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $this->service = [
            'apiVersion' => 'v1',
            'kind' => 'Service',
            'metadata' => [
                'name' => $this->appName,
                'namespace' => $this->namespace,
                'labels' => [
                    'app' => $this->appName
                ]
            ],
            'spec' => [
                'selector' => [
                    'app' => $this->appName
                ],
                'ports' => [
                    [
                        'protocol' => 'TCP',
                        'port' => 80,
                        'targetPort' => 80
                    ]
                ],
                'type' => 'ClusterIP'
            ]
        ];
        
        $this->ingress = [
            'apiVersion' => 'networking.k8s.io/v1',
            'kind' => 'Ingress',
            'metadata' => [
                'name' => $this->appName,
                'namespace' => $this->namespace,
                'annotations' => [
                    'nginx.ingress.kubernetes.io/rewrite-target' => '/',
                    'cert-manager.io/cluster-issuer' => 'letsencrypt-prod'
                ]
            ],
            'spec' => [
                'tls' => [
                    [
                        'hosts' => [
                            'example.com',
                            'www.example.com'
                        ],
                        'secretName' => $this->appName . '-tls'
                    ]
                ],
                'rules' => [
                    [
                        'host' => 'example.com',
                        'http' => [
                            'paths' => [
                                [
                                    'path' => '/',
                                    'pathType' => 'Prefix',
                                    'backend' => [
                                        'service' => [
                                            'name' => $this->appName,
                                            'port' => [
                                                'number' => 80
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public function setImage(string $image): void {
        $this->deployment['spec']['template']['spec']['containers'][0]['image'] = $image;
    }
    
    public function setReplicas(int $replicas): void {
        $this->deployment['spec']['replicas'] = $replicas;
    }
    
    public function setResources(array $requests, array $limits): void {
        $container = &$this->deployment['spec']['template']['spec']['containers'][0];
        $container['resources']['requests'] = $requests;
        $container['resources']['limits'] = $limits;
    }
    
    public function deploy(): array {
        echo "Deploying to Kubernetes: {$this->appName}\n";
        echo str_repeat("-", 40) . "\n";
        
        $startTime = microtime(true);
        
        // Generate YAML files
        $deploymentYaml = $this->generateYAML($this->deployment);
        $serviceYaml = $this->generateYAML($this->service);
        $ingressYaml = $this->generateYAML($this->ingress);
        
        echo "Generated Kubernetes manifests:\n";
        echo "- Deployment: " . strlen($deploymentYaml) . " bytes\n";
        echo "- Service: " . strlen($serviceYaml) . " bytes\n";
        echo "- Ingress: " . strlen($ingressYaml) . " bytes\n\n";
        
        // Simulate deployment
        $deploymentSteps = [
            'namespace/default created',
            'deployment.apps/' . $this->appName . ' created',
            'service/' . $this->appName . ' created',
            'ingress.networking.k8s.io/' . $this->appName . ' created'
        ];
        
        foreach ($deploymentSteps as $step) {
            echo "$step\n";
            usleep(100000); // 0.1 seconds
            echo "  ✅ Success\n";
        }
        
        // Wait for rollout
        echo "\nWaiting for deployment rollout...\n";
        for ($i = 1; $i <= 10; $i++) {
            echo "  Rollout status: $i/10 replicas ready\n";
            usleep(50000); // 0.05 seconds
        }
        
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        echo "\nDeployment completed successfully!\n";
        echo "Duration: " . round($duration, 2) . "s\n";
        echo "Status: Available\n";
        echo "Replicas: 3 ready\n";
        
        return [
            'success' => true,
            'app_name' => $this->appName,
            'namespace' => $this->namespace,
            'duration' => $duration,
            'replicas' => 3,
            'status' => 'available'
        ];
    }
    
    private function generateYAML(array $data): string {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    
    public function getStatus(): array {
        return [
            'deployment' => $this->deployment,
            'service' => $this->service,
            'ingress' => $this->ingress,
            'status' => 'ready'
        ];
    }
}

// CI/CD Examples
class CICDExamples {
    private CICDPipeline $pipeline;
    private AutomatedTestRunner $testRunner;
    private DockerBuildManager $docker;
    private KubernetesDeploymentManager $k8s;
    
    public function __construct() {
        $this->pipeline = new CICDPipeline('web-app-pipeline');
        $this->testRunner = new AutomatedTestRunner();
        $this->docker = new DockerBuildManager('myapp');
        $this->k8s = new KubernetesDeploymentManager('myapp', 'production');
        
        $this->setupPipeline();
    }
    
    private function setupPipeline(): void {
        // Set environment variables
        $this->pipeline->setEnvironmentVariable('REPOSITORY_URL', 'https://github.com/example/myapp.git');
        $this->pipeline->setEnvironmentVariable('BRANCH', 'main');
        $this->pipeline->setEnvironmentVariable('IMAGE_NAME', 'myapp');
        $this->pipeline->setEnvironmentVariable('BUILD_NUMBER', '123');
        $this->pipeline->setEnvironmentVariable('APP_NAME', 'myapp');
        
        // Add notifications
        $this->pipeline->addNotification('pipeline_success', [
            'type' => 'webhook',
            'url' => 'https://hooks.slack.com/services/...'
        ]);
        
        $this->pipeline->addNotification('pipeline_failed', [
            'type' => 'email',
            'to' => 'devops@example.com',
            'subject' => 'Pipeline Failed'
        ]);
    }
    
    public function demonstrateCICDPipeline(): void {
        echo "CI/CD Pipeline Example\n";
        echo str_repeat("-", 25) . "\n";
        
        $result = $this->pipeline->execute();
        
        echo "\nPipeline Results:\n";
        echo "Status: {$result['status']}\n";
        echo "Duration: " . round($result['total_duration'], 2) . "s\n";
        
        foreach ($result['stages'] as $stageName => $stage) {
            echo "  {$stage['name']}: {$stage['status']} ({$stage['duration']}s)\n";
        }
    }
    
    public function demonstrateAutomatedTesting(): void {
        echo "\nAutomated Testing Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Run all test suites
        $results = $this->testRunner->runTests();
        
        echo "\nTest Results:\n";
        echo "Overall Status: {$results['overall_status']}\n";
        echo "Duration: " . round($results['duration'], 2) . "s\n";
        echo "Total Tests: {$results['total_tests']}\n";
        echo "Passed: {$results['passed_tests']}\n";
        echo "Failed: {$results['failed_tests']}\n";
        echo "Skipped: {$results['skipped_tests']}\n";
        
        if (isset($results['test_suites'])) {
            echo "\nTest Suite Details:\n";
            foreach ($results['test_suites'] as $name => $suite) {
                echo "  {$name}: {$suite['status']} ({$suite['passed']}/{$suite['total']})\n";
            }
        }
    }
    
    public function demonstrateDockerBuild(): void {
        echo "\nDocker Build Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Build Docker image
        $buildResult = $this->docker->build();
        
        if ($buildResult['success']) {
            echo "\nDocker Build Results:\n";
            echo "Image: {$buildResult['image_name']}\n";
            echo "ID: {$buildResult['image_id']}\n";
            echo "Size: {$buildResult['size']}\n";
            echo "Duration: " . round($buildResult['duration'], 2) . "s\n";
            
            // Push to registry
            echo "\nPushing to registry...\n";
            $pushResult = $this->docker->push();
            
            if ($pushResult['success']) {
                echo "Push Results:\n";
                echo "Image: {$pushResult['image_name']}\n";
                echo "Duration: " . round($pushResult['duration'], 2) . "s\n";
            }
        }
    }
    
    public function demonstrateKubernetesDeployment(): void {
        echo "\nKubernetes Deployment Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Set Docker image for deployment
        $this->docker->setBuildArg('IMAGE_NAME', 'myapp:latest');
        $this->k8s->setImage('myapp:latest');
        
        // Deploy to Kubernetes
        $deployResult = $this->k8s->deploy();
        
        if ($deployResult['success']) {
            echo "\nKubernetes Deployment Results:\n";
            echo "App: {$deployResult['app_name']}\n";
            echo "Namespace: {$deployResult['namespace']}\n";
            echo "Status: {$deployResult['status']}\n";
            echo "Replicas: {$deployResult['replicas']}\n";
            echo "Duration: " . round($deployResult['duration'], 2) . "s\n";
        }
    }
    
    public function demonstrateCompleteCICD(): void {
        echo "\nComplete CI/CD Workflow\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Source Code Checkout\n";
        echo "2. Environment Setup\n";
        echo "3. Run Automated Tests\n";
        echo "4. Security Scanning\n";
        echo "5. Build Docker Image\n";
        echo "6. Push to Registry\n";
        echo "7. Deploy to Kubernetes\n";
        echo "8. Health Checks\n";
        echo "9. Rollout Monitoring\n";
        echo "10. Notification\n\n";
        
        // Simulate complete workflow
        $totalDuration = 0;
        $workflowSteps = [
            'checkout' => 10,
            'setup' => 30,
            'tests' => 120,
            'security' => 45,
            'build' => 180,
            'push' => 60,
            'deploy' => 90,
            'health_checks' => 30,
            'monitoring' => 15
        ];
        
        foreach ($workflowSteps as $step => $duration) {
            echo "Executing: $step\n";
            usleep($duration * 1000); // Convert to microseconds
            echo "  ✅ Completed\n";
            $totalDuration += $duration;
        }
        
        echo "\n✅ Complete CI/CD Workflow Completed!\n";
        echo "Total Duration: {$totalDuration}s\n";
        echo "Status: Success\n";
        echo "Deployment: Available\n";
        
        // Send success notification
        echo "\n📧 Sending notifications...\n";
        echo "  Slack: Pipeline completed successfully\n";
        echo "  Email: Deployment notification sent\n";
    }
    
    public function runAllExamples(): void {
        echo "CI/CD Automation Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateCICDPipeline();
        $this->demonstrateAutomatedTesting();
        $this->demonstrateDockerBuild();
        $this->demonstrateKubernetesDeployment();
        $this->demonstrateCompleteCICD();
    }
}

// CI/CD Best Practices
function printCICDBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "CI/CD Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Pipeline Design:\n";
    echo "   • Keep pipelines simple and fast\n";
    echo "   • Use parallel execution when possible\n";
    echo "   • Fail fast with early validation\n";
    echo "   • Implement proper error handling\n";
    echo "   • Use consistent naming conventions\n\n";
    
    echo "2. Testing Strategy:\n";
    echo "   • Test at multiple levels (unit, integration, E2E)\n";
    echo "   • Automate test execution\n";
    echo "   • Use test coverage metrics\n";
    echo "   • Implement security testing\n";
    echo "   • Run performance tests regularly\n\n";
    
    echo "3. Build Optimization:\n";
    echo "   • Use Docker layer caching\n";
    echo "   • Optimize Dockerfile instructions\n";
    echo "   • Use multi-stage builds\n";
    echo "   • Minimize image sizes\n";
    echo "   • Use appropriate base images\n\n";
    
    echo "4. Deployment Safety:\n";
    echo "   • Use blue-green or canary deployments\n";
    echo "   • Implement health checks\n";
    echo "   • Have rollback strategies\n";
    echo "   • Use infrastructure as code\n";
    echo "   • Monitor deployment health\n\n";
    
    echo "5. Security Integration:\n";
    echo "   • Scan dependencies for vulnerabilities\n";
    echo "   • Use secret management\n";
    echo "   • Implement access controls\n";
    echo "   • Audit pipeline activities\n";
    echo "   • Use secure registries\n\n";
    
    echo "6. Monitoring & Alerting:\n";
    echo "   • Monitor pipeline performance\n";
    echo "   • Set up appropriate alerts\n";
    echo "   • Track deployment metrics\n";
    echo "   • Use dashboards for visibility\n";
    echo "   • Implement SLA monitoring";
}

// Main execution
function runCICDDemo(): void {
    $examples = new CICDExamples();
    $examples->runAllExamples();
    printCICDBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCICDDemo();
}
?>
