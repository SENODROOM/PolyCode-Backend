<?php
/**
 * Monitoring and Logging Tools
 * 
 * This file demonstrates logging frameworks, monitoring systems,
    error tracking, performance monitoring, and alerting.
 */

// Advanced Logging Framework
class AdvancedLogger
{
    private array $handlers = [];
    private array $processors = [];
    private array $context = [];
    private string $channel;
    private string $level = 'INFO';
    
    public function __construct(string $channel = 'app')
    {
        $this->channel = $channel;
        $this->initializeHandlers();
    }
    
    /**
     * Initialize log handlers
     */
    private function initializeHandlers(): void
    {
        $this->handlers = [
            'file' => new FileLogger('/var/log/app.log'),
            'console' => new ConsoleLogger(),
            'database' => new DatabaseLogger(),
            'syslog' => new SyslogLogger()
        ];
    }
    
    /**
     * Add handler
     */
    public function addHandler(string $name, LogHandler $handler): void
    {
        $this->handlers[$name] = $handler;
    }
    
    /**
     * Add processor
     */
    public function addProcessor(callable $processor): void
    {
        $this->processors[] = $processor;
    }
    
    /**
     * Set context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }
    
    /**
     * Log message
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }
        
        $record = [
            'channel' => $this->channel,
            'level' => $level,
            'message' => $message,
            'context' => array_merge($this->context, $context),
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        // Apply processors
        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }
        
        // Send to handlers
        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }
    
    /**
     * Convenience methods
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }
    
    /**
     * Check if should log
     */
    private function shouldLog(string $level): bool
    {
        $levels = ['DEBUG' => 0, 'INFO' => 1, 'WARNING' => 2, 'ERROR' => 3, 'CRITICAL' => 4];
        
        return $levels[$level] >= $levels[$this->level];
    }
    
    /**
     * Set log level
     */
    public function setLevel(string $level): void
    {
        $this->level = $level;
    }
}

// Log Handler Interface
interface LogHandler
{
    public function handle(array $record): void;
}

// File Logger
class FileLogger implements LogHandler
{
    private string $file;
    private int $maxSize;
    private int $maxFiles;
    
    public function __construct(string $file, int $maxSize = 10485760, int $maxFiles = 5)
    {
        $this->file = $file;
        $this->maxSize = $maxSize;
        $this->maxFiles = $maxFiles;
    }
    
    public function handle(array $record): void
    {
        $logEntry = $this->format($record);
        
        // Rotate if needed
        $this->rotateIfNeeded();
        
        // Write to file
        file_put_contents($this->file, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    private function format(array $record): string
    {
        return sprintf(
            "[%s] %s.%s: %s %s\n",
            $record['timestamp'],
            $record['channel'],
            $record['level'],
            $record['message'],
            $this->formatContext($record['context'])
        );
    }
    
    private function formatContext(array $context): string
    {
        if (empty($context)) {
            return '';
        }
        
        return json_encode($context, JSON_UNESCAPED_SLASHES);
    }
    
    private function rotateIfNeeded(): void
    {
        if (!file_exists($this->file) || filesize($this->file) < $this->maxSize) {
            return;
        }
        
        // Rotate files
        for ($i = $this->maxFiles - 1; $i >= 1; $i--) {
            $oldFile = $this->file . '.' . $i;
            $newFile = $this->file . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i === $this->maxFiles - 1) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }
        
        // Move current file
        rename($this->file, $this->file . '.1');
    }
}

// Console Logger
class ConsoleLogger implements LogHandler
{
    public function handle(array $record): void
    {
        $color = $this->getColor($record['level']);
        $message = $this->format($record);
        
        echo "\033[{$color}m{$message}\033[0m";
    }
    
    private function getColor(string $level): string
    {
        $colors = [
            'DEBUG' => '90',    // Gray
            'INFO' => '32',     // Green
            'WARNING' => '33',  // Yellow
            'ERROR' => '31',    // Red
            'CRITICAL' => '41;37' // Red background, white text
        ];
        
        return $colors[$level] ?? '0';
    }
    
    private function format(array $record): string
    {
        return sprintf(
            "[%s] %s.%s: %s %s\n",
            $record['timestamp'],
            $record['channel'],
            $record['level'],
            $record['message'],
            json_encode($record['context'])
        );
    }
}

// Database Logger
class DatabaseLogger implements LogHandler
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?? $this->createConnection();
        $this->createTable();
    }
    
    public function handle(array $record): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO logs (channel, level, message, context, timestamp, memory_usage, peak_memory)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $record['channel'],
            $record['level'],
            $record['message'],
            json_encode($record['context']),
            $record['timestamp'],
            $record['memory_usage'],
            $record['peak_memory']
        ]);
    }
    
    private function createConnection(): PDO
    {
        return new PDO('sqlite::memory:');
    }
    
    private function createTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                channel TEXT NOT NULL,
                level TEXT NOT NULL,
                message TEXT NOT NULL,
                context TEXT,
                timestamp DATETIME NOT NULL,
                memory_usage INTEGER,
                peak_memory INTEGER
            )
        ");
    }
}

// Syslog Logger
class SyslogLogger implements LogHandler
{
    private string $ident;
    private int $facility;
    
    public function __construct(string $ident = 'php-app', int $facility = LOG_USER)
    {
        $this->ident = $ident;
        $this->facility = $facility;
        openlog($this->ident, LOG_PID | LOG_ODELAY, $this->facility);
    }
    
    public function handle(array $record): void
    {
        $priority = $this->getPriority($record['level']);
        $message = $this->format($record);
        
        syslog($priority, $message);
    }
    
    private function getPriority(string $level): int
    {
        $priorities = [
            'DEBUG' => LOG_DEBUG,
            'INFO' => LOG_INFO,
            'WARNING' => LOG_WARNING,
            'ERROR' => LOG_ERR,
            'CRITICAL' => LOG_CRIT
        ];
        
        return $priorities[$level] ?? LOG_INFO;
    }
    
    private function format(array $record): string
    {
        return sprintf(
            "[%s.%s] %s %s",
            $record['channel'],
            $record['level'],
            $record['message'],
            json_encode($record['context'])
        );
    }
    
    public function __destruct()
    {
        closelog();
    }
}

// Performance Monitor
class PerformanceMonitor
{
    private array $metrics = [];
    private array $timers = [];
    private array $gauges = [];
    private array $counters = [];
    
    /**
     * Start timer
     */
    public function startTimer(string $name): void
    {
        $this->timers[$name] = [
            'start' => microtime(true),
            'start_memory' => memory_get_usage(true)
        ];
    }
    
    /**
     * End timer
     */
    public function endTimer(string $name): array
    {
        if (!isset($this->timers[$name])) {
            return [];
        }
        
        $timer = $this->timers[$name];
        $duration = microtime(true) - $timer['start'];
        $memoryUsed = memory_get_usage(true) - $timer['start_memory'];
        
        $result = [
            'name' => $name,
            'duration' => $duration,
            'memory_used' => $memoryUsed,
            'timestamp' => time()
        ];
        
        $this->metrics[] = $result;
        unset($this->timers[$name]);
        
        return $result;
    }
    
    /**
     * Set gauge
     */
    public function setGauge(string $name, float $value): void
    {
        $this->gauges[$name] = [
            'name' => $name,
            'value' => $value,
            'timestamp' => time()
        ];
    }
    
    /**
     * Increment counter
     */
    public function incrementCounter(string $name, int $value = 1): void
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = [
                'name' => $name,
                'value' => 0,
                'timestamp' => time()
            ];
        }
        
        $this->counters[$name]['value'] += $value;
        $this->counters[$name]['timestamp'] = time();
    }
    
    /**
     * Get system metrics
     */
    public function getSystemMetrics(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => $this->getMemoryLimit(),
            'cpu_usage' => sys_getloadavg()[0] ?? 0,
            'disk_usage' => $this->getDiskUsage(),
            'timestamp' => time()
        ];
    }
    
    /**
     * Get application metrics
     */
    public function getApplicationMetrics(): array
    {
        return [
            'metrics' => $this->metrics,
            'gauges' => $this->gauges,
            'counters' => $this->counters,
            'timers' => $this->timers,
            'timestamp' => time()
        ];
    }
    
    /**
     * Export metrics to Prometheus format
     */
    public function exportPrometheus(): string
    {
        $output = '';
        
        // System metrics
        $system = $this->getSystemMetrics();
        $output .= "# HELP php_memory_usage_bytes Current memory usage in bytes\n";
        $output .= "# TYPE php_memory_usage_bytes gauge\n";
        $output .= "php_memory_usage_bytes {$system['memory_usage']}\n";
        
        $output .= "# HELP php_memory_peak_bytes Peak memory usage in bytes\n";
        $output .= "# TYPE php_memory_peak_bytes gauge\n";
        $output .= "php_memory_peak_bytes {$system['memory_peak']}\n";
        
        // Application metrics
        foreach ($this->gauges as $gauge) {
            $output .= "# HELP php_{$gauge['name']} Current value of {$gauge['name']}\n";
            $output .= "# TYPE php_{$gauge['name']} gauge\n";
            $output .= "php_{$gauge['name']} {$gauge['value']}\n";
        }
        
        foreach ($this->counters as $counter) {
            $output .= "# HELP php_{$counter['name']} Total count of {$counter['name']}\n";
            $output .= "# TYPE php_{$counter['name']} counter\n";
            $output .= "php_{$counter['name']} {$counter['value']}\n";
        }
        
        return $output;
    }
    
    private function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        
        if ($limit === '-1') {
            return -1;
        }
        
        return $this->parseMemoryLimit($limit);
    }
    
    private function parseMemoryLimit(string $limit): int
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return (int) $limit;
        }
    }
    
    private function getDiskUsage(): array
    {
        $path = '/';
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'percentage' => ($used / $total) * 100
        ];
    }
}

// Error Tracking System
class ErrorTrackingSystem
{
    private array $errors = [];
    private array $config;
    private AdvancedLogger $logger;
    
    public function __construct(array $config = [], AdvancedLogger $logger = null)
    {
        $this->config = array_merge([
            'enabled' => true,
            'capture_exceptions' => true,
            'capture_errors' => true,
            'sample_rate' => 1.0,
            'environment' => 'production',
            'release' => '1.0.0'
        ], $config);
        
        $this->logger = $logger ?: new AdvancedLogger('error_tracking');
        
        if ($this->config['enabled']) {
            $this->setupHandlers();
        }
    }
    
    /**
     * Setup error handlers
     */
    private function setupHandlers(): void
    {
        if ($this->config['capture_exceptions']) {
            set_exception_handler([$this, 'handleException']);
        }
        
        if ($this->config['capture_errors']) {
            set_error_handler([$this, 'handleError']);
        }
        
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle exception
     */
    public function handleException(\Throwable $exception): void
    {
        if (!$this->shouldCapture()) {
            return;
        }
        
        $error = [
            'type' => 'exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => time(),
            'environment' => $this->config['environment'],
            'release' => $this->config['release'],
            'request' => $this->getRequestContext(),
            'user' => $this->getUserContext()
        ];
        
        $this->captureError($error);
    }
    
    /**
     * Handle error
     */
    public function handleError(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        if (!$this->shouldCapture()) {
            return false;
        }
        
        $error = [
            'type' => 'error',
            'errno' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => time(),
            'environment' => $this->config['environment'],
            'release' => $this->config['release'],
            'request' => $this->getRequestContext(),
            'user' => $this->getUserContext()
        ];
        
        $this->captureError($error);
        
        return true;
    }
    
    /**
     * Handle shutdown
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    /**
     * Capture error
     */
    private function captureError(array $error): void
    {
        $errorId = uniqid('error_');
        $error['id'] = $errorId;
        
        $this->errors[] = $error;
        
        // Log error
        $this->logger->error($error['message'], [
            'error_id' => $errorId,
            'type' => $error['type'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        
        // Send to external service (simulated)
        $this->sendToService($error);
    }
    
    /**
     * Check if should capture
     */
    private function shouldCapture(): bool
    {
        return $this->config['enabled'] && (mt_rand() / mt_getrandmax()) <= $this->config['sample_rate'];
    }
    
    /**
     * Get request context
     */
    private function getRequestContext(): array
    {
        return [
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'headers' => $this->getHeaders(),
            'get' => $_GET,
            'post' => $_POST,
            'cookies' => $_COOKIE
        ];
    }
    
    /**
     * Get user context
     */
    private function getUserContext(): array
    {
        // This would integrate with your authentication system
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null
        ];
    }
    
    /**
     * Get headers
     */
    private function getHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }
    
    /**
     * Send to service
     */
    private function sendToService(array $error): void
    {
        // Simulate sending to external service
        echo "Sending error {$error['id']} to tracking service\n";
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get error statistics
     */
    public function getErrorStats(): array
    {
        $stats = [
            'total' => count($this->errors),
            'by_type' => [],
            'by_environment' => [],
            'recent' => 0
        ];
        
        $oneHourAgo = time() - 3600;
        
        foreach ($this->errors as $error) {
            $type = $error['type'] ?? 'unknown';
            $env = $error['environment'] ?? 'unknown';
            
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
            $stats['by_environment'][$env] = ($stats['by_environment'][$env] ?? 0) + 1;
            
            if ($error['timestamp'] > $oneHourAgo) {
                $stats['recent']++;
            }
        }
        
        return $stats;
    }
}

// Alerting System
class AlertingSystem
{
    private array $rules = [];
    private array $channels = [];
    private array $alerts = [];
    
    public function __construct()
    {
        $this->initializeChannels();
    }
    
    /**
     * Initialize alert channels
     */
    private function initializeChannels(): void
    {
        $this->channels = [
            'email' => new EmailAlertChannel(),
            'slack' => new SlackAlertChannel(),
            'webhook' => new WebhookAlertChannel(),
            'sms' => new SMSAlertChannel()
        ];
    }
    
    /**
     * Add alert rule
     */
    public function addRule(string $name, callable $condition, array $config): void
    {
        $this->rules[$name] = [
            'name' => $name,
            'condition' => $condition,
            'config' => array_merge([
                'severity' => 'warning',
                'channels' => ['email'],
                'cooldown' => 300, // 5 minutes
                'enabled' => true
            ], $config)
        ];
    }
    
    /**
     * Check rules
     */
    public function checkRules(array $metrics): array
    {
        $triggered = [];
        
        foreach ($this->rules as $name => $rule) {
            if (!$rule['config']['enabled']) {
                continue;
            }
            
            // Check cooldown
            if ($this->isInCooldown($name)) {
                continue;
            }
            
            // Evaluate condition
            $condition = $rule['condition'];
            if ($condition($metrics)) {
                $alert = $this->triggerAlert($name, $rule, $metrics);
                $triggered[] = $alert;
            }
        }
        
        return $triggered;
    }
    
    /**
     * Trigger alert
     */
    private function triggerAlert(string $ruleName, array $rule, array $metrics): array
    {
        $alert = [
            'id' => uniqid('alert_'),
            'rule' => $ruleName,
            'severity' => $rule['config']['severity'],
            'message' => $this->generateMessage($rule, $metrics),
            'metrics' => $metrics,
            'timestamp' => time(),
            'status' => 'triggered'
        ];
        
        $this->alerts[] = $alert;
        
        // Send to channels
        foreach ($rule['config']['channels'] as $channelName) {
            if (isset($this->channels[$channelName])) {
                $this->channels[$channelName]->send($alert);
            }
        }
        
        return $alert;
    }
    
    /**
     * Generate alert message
     */
    private function generateMessage(array $rule, array $metrics): string
    {
        return "Alert: {$rule['name']} triggered\n" .
               "Severity: {$rule['config']['severity']}\n" .
               "Metrics: " . json_encode($metrics, JSON_PRETTY_PRINT);
    }
    
    /**
     * Check if in cooldown
     */
    private function isInCooldown(string $ruleName): bool
    {
        $rule = $this->rules[$ruleName];
        $cooldown = $rule['config']['cooldown'];
        
        // Find last alert for this rule
        $lastAlert = null;
        for ($i = count($this->alerts) - 1; $i >= 0; $i--) {
            if ($this->alerts[$i]['rule'] === $ruleName) {
                $lastAlert = $this->alerts[$i];
                break;
            }
        }
        
        if (!$lastAlert) {
            return false;
        }
        
        return (time() - $lastAlert['timestamp']) < $cooldown;
    }
    
    /**
     * Get alerts
     */
    public function getAlerts(): array
    {
        return $this->alerts;
    }
    
    /**
     * Get alert statistics
     */
    public function getAlertStats(): array
    {
        $stats = [
            'total' => count($this->alerts),
            'by_severity' => [],
            'by_rule' => [],
            'recent' => 0
        ];
        
        $oneHourAgo = time() - 3600;
        
        foreach ($this->alerts as $alert) {
            $severity = $alert['severity'];
            $rule = $alert['rule'];
            
            $stats['by_severity'][$severity] = ($stats['by_severity'][$severity] ?? 0) + 1;
            $stats['by_rule'][$rule] = ($stats['by_rule'][$rule] ?? 0) + 1;
            
            if ($alert['timestamp'] > $oneHourAgo) {
                $stats['recent']++;
            }
        }
        
        return $stats;
    }
}

// Alert Channel Interface
interface AlertChannel
{
    public function send(array $alert): void;
}

// Email Alert Channel
class EmailAlertChannel implements AlertChannel
{
    public function send(array $alert): void
    {
        echo "Sending email alert: {$alert['message']}\n";
        // In real implementation, would send actual email
    }
}

// Slack Alert Channel
class SlackAlertChannel implements AlertChannel
{
    public function send(array $alert): void
    {
        echo "Sending Slack alert: {$alert['message']}\n";
        // In real implementation, would send to Slack webhook
    }
}

// Webhook Alert Channel
class WebhookAlertChannel implements AlertChannel
{
    public function send(array $alert): void
    {
        echo "Sending webhook alert: {$alert['message']}\n";
        // In real implementation, would send HTTP request
    }
}

// SMS Alert Channel
class SMSAlertChannel implements AlertChannel
{
    public function send(array $alert): void
    {
        echo "Sending SMS alert: {$alert['message']}\n";
        // In real implementation, would send SMS
    }
}

// Monitoring and Logging Examples
class MonitoringLoggingExamples
{
    private AdvancedLogger $logger;
    private PerformanceMonitor $performanceMonitor;
    private ErrorTrackingSystem $errorTracker;
    private AlertingSystem $alertingSystem;
    
    public function __construct()
    {
        $this->logger = new AdvancedLogger('monitoring_demo');
        $this->performanceMonitor = new PerformanceMonitor();
        $this->errorTracker = new ErrorTrackingSystem();
        $this->alertingSystem = new AlertingSystem();
    }
    
    public function demonstrateLogging(): void
    {
        echo "Advanced Logging Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Add processors
        $this->logger->addProcessor(function($record) {
            $record['request_id'] = uniqid('req_');
            $record['user_id'] = $_SESSION['user_id'] ?? null;
            return $record;
        });
        
        // Set context
        $this->logger->setContext([
            'app_version' => '1.0.0',
            'environment' => 'development'
        ]);
        
        // Log different levels
        $this->logger->debug('Debug message for troubleshooting');
        $this->logger->info('User logged in', ['user_id' => 123, 'ip' => '192.168.1.1']);
        $this->logger->warning('Low disk space', ['free_space' => '100MB']);
        $this->logger->error('Database connection failed', ['error' => 'Connection timeout']);
        $this->logger->critical('System out of memory', ['memory_usage' => '512MB']);
        
        echo "Logged messages to multiple handlers\n";
    }
    
    public function demonstratePerformanceMonitoring(): void
    {
        echo "\nPerformance Monitoring Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Monitor function execution
        $this->performanceMonitor->startTimer('database_query');
        
        // Simulate database query
        usleep(50000); // 50ms
        
        $result = $this->performanceMonitor->endTimer('database_query');
        echo "Database query took: " . round($result['duration'] * 1000, 2) . "ms\n";
        echo "Memory used: " . round($result['memory_used'] / 1024, 2) . "KB\n";
        
        // Monitor API call
        $this->performanceMonitor->startTimer('api_call');
        
        // Simulate API call
        usleep(100000); // 100ms
        
        $result = $this->performanceMonitor->endTimer('api_call');
        echo "API call took: " . round($result['duration'] * 1000, 2) . "ms\n";
        
        // Set gauges
        $this->performanceMonitor->setGauge('active_users', 1250);
        $this->performanceMonitor->setGauge('cpu_usage', 75.5);
        $this->performanceMonitor->setGauge('memory_usage_percent', 68.2);
        
        // Increment counters
        $this->performanceMonitor->incrementCounter('page_views');
        $this->performanceMonitor->incrementCounter('api_requests', 5);
        $this->performanceMonitor->incrementCounter('errors', 1);
        
        // Get system metrics
        $systemMetrics = $this->performanceMonitor->getSystemMetrics();
        echo "\nSystem Metrics:\n";
        echo "Memory Usage: " . round($systemMetrics['memory_usage'] / 1024 / 1024, 2) . "MB\n";
        echo "Memory Peak: " . round($systemMetrics['memory_peak'] / 1024 / 1024, 2) . "MB\n";
        echo "CPU Usage: " . $systemMetrics['cpu_usage'] . "%\n";
        echo "Disk Usage: " . round($systemMetrics['disk_usage']['percentage'], 2) . "%\n";
        
        // Export to Prometheus format
        $prometheus = $this->performanceMonitor->exportPrometheus();
        echo "\nPrometheus Export:\n";
        echo substr($prometheus, 0, 300) . "...\n";
    }
    
    public function demonstrateErrorTracking(): void
    {
        echo "\nError Tracking Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Trigger some errors
        try {
            throw new RuntimeException('Test exception for tracking');
        } catch (Exception $e) {
            // Exception will be automatically captured
        }
        
        // Trigger a warning
        trigger_error('Test warning for tracking', E_USER_WARNING);
        
        // Get error statistics
        $stats = $this->errorTracker->getErrorStats();
        echo "Error Statistics:\n";
        echo "Total Errors: {$stats['total']}\n";
        echo "Recent (1 hour): {$stats['recent']}\n";
        
        echo "\nBy Type:\n";
        foreach ($stats['by_type'] as $type => $count) {
            echo "  $type: $count\n";
        }
        
        echo "\nBy Environment:\n";
        foreach ($stats['by_environment'] as $env => $count) {
            echo "  $env: $count\n";
        }
        
        // Get recent errors
        $errors = $this->errorTracker->getErrors();
        if (!empty($errors)) {
            echo "\nRecent Errors:\n";
            foreach (array_slice($errors, -3) as $error) {
                echo "  {$error['type']}: {$error['message']} ({$error['file']}:{$error['line']})\n";
            }
        }
    }
    
    public function demonstrateAlerting(): void
    {
        echo "\nAlerting System Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Add alert rules
        $this->alertingSystem->addRule('high_error_rate', function($metrics) {
            return ($metrics['error_rate'] ?? 0) > 0.05; // 5% error rate
        }, [
            'severity' => 'critical',
            'channels' => ['email', 'slack'],
            'cooldown' => 600
        ]);
        
        $this->alertingSystem->addRule('high_memory_usage', function($metrics) {
            return ($metrics['memory_usage_percent'] ?? 0) > 90;
        }, [
            'severity' => 'warning',
            'channels' => ['email'],
            'cooldown' => 300
        ]);
        
        $this->alertingSystem->addRule('low_disk_space', function($metrics) {
            return ($metrics['disk_usage_percent'] ?? 0) > 95;
        }, [
            'severity' => 'critical',
            'channels' => ['email', 'slack', 'sms'],
            'cooldown' => 1800
        ]);
        
        // Simulate metrics that trigger alerts
        $triggeringMetrics = [
            'error_rate' => 0.08, // 8% error rate
            'memory_usage_percent' => 92.5,
            'disk_usage_percent' => 96.2
        ];
        
        echo "Checking alert rules with triggering metrics...\n";
        $alerts = $this->alertingSystem->checkRules($triggeringMetrics);
        
        echo "Triggered alerts: " . count($alerts) . "\n";
        foreach ($alerts as $alert) {
            echo "  - {$alert['rule']} ({$alert['severity']})\n";
        }
        
        // Get alert statistics
        $stats = $this->alertingSystem->getAlertStats();
        echo "\nAlert Statistics:\n";
        echo "Total Alerts: {$stats['total']}\n";
        echo "Recent (1 hour): {$stats['recent']}\n";
        
        echo "\nBy Severity:\n";
        foreach ($stats['by_severity'] as $severity => $count) {
            echo "  $severity: $count\n";
        }
        
        echo "\nBy Rule:\n";
        foreach ($stats['by_rule'] as $rule => $count) {
            echo "  $rule: $count\n";
        }
    }
    
    public function demonstrateIntegratedMonitoring(): void
    {
        echo "\nIntegrated Monitoring Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Simulate a complete monitoring scenario
        $this->performanceMonitor->startTimer('user_registration');
        
        // Log user registration attempt
        $this->logger->info('User registration started', ['email' => 'user@example.com']);
        
        try {
            // Simulate processing
            usleep(75000); // 75ms
            
            // Simulate an error
            if (rand(0, 10) > 8) {
                throw new RuntimeException('Email service unavailable');
            }
            
            // Success
            $this->logger->info('User registration completed', ['email' => 'user@example.com']);
            
        } catch (Exception $e) {
            $this->logger->error('User registration failed', [
                'email' => 'user@example.com',
                'error' => $e->getMessage()
            ]);
            
            // This would be captured by error tracker
        }
        
        $result = $this->performanceMonitor->endTimer('user_registration');
        
        // Update metrics
        $this->performanceMonitor->incrementCounter('registrations');
        $this->performanceMonitor->setGauge('registration_time', $result['duration'] * 1000);
        
        // Check alerting
        $metrics = $this->performanceMonitor->getApplicationMetrics();
        $alerts = $this->alertingSystem->checkRules([
            'registration_time' => $result['duration'] * 1000,
            'error_rate' => 0.1
        ]);
        
        echo "Monitoring cycle completed\n";
        echo "Processing time: " . round($result['duration'] * 1000, 2) . "ms\n";
        echo "Alerts triggered: " . count($alerts) . "\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Monitoring and Logging Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateLogging();
        $this->demonstratePerformanceMonitoring();
        $this->demonstrateErrorTracking();
        $this->demonstrateAlerting();
        $this->demonstrateIntegratedMonitoring();
    }
}

// Monitoring and Logging Best Practices
function printMonitoringLoggingBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Monitoring and Logging Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Logging:\n";
    echo "   • Use structured logging\n";
    echo "   • Log at appropriate levels\n";
    echo "   • Include context information\n";
    echo "   • Use multiple handlers\n";
    echo "   • Implement log rotation\n\n";
    
    echo "2. Performance Monitoring:\n";
    echo "   • Monitor critical paths\n";
    echo "   • Track business metrics\n";
    echo "   • Use appropriate granularity\n";
    echo "   • Export standard formats\n";
    echo "   • Set up dashboards\n\n";
    
    echo "3. Error Tracking:\n";
    echo "   • Capture all exceptions\n";
    echo "   • Include request context\n";
    echo "   • Use sampling for high volume\n";
    echo "   • Implement deduplication\n";
    echo "   • Set up alerting\n\n";
    
    echo "4. Alerting:\n";
    echo "   • Define clear thresholds\n";
    echo "   • Use appropriate severity levels\n";
    echo "   • Implement cooldown periods\n";
    echo "   • Use multiple channels\n";
    echo "   • Test alert delivery\n\n";
    
    echo "5. System Monitoring:\n";
    echo "   • Monitor system resources\n";
    echo "   • Track application health\n";
    echo "   • Use synthetic monitoring\n";
    echo "   • Monitor external dependencies\n";
    echo "   • Set up SLA monitoring\n\n";
    
    echo "6. Data Management:\n";
    echo "   • Implement data retention\n";
    echo "   • Use efficient storage\n";
    echo "   • Compress old data\n";
    echo "   • Archive important logs\n";
    echo "   • Monitor storage usage";
}

// Main execution
function runMonitoringLoggingDemo(): void
{
    $examples = new MonitoringLoggingExamples();
    $examples->runAllExamples();
    printMonitoringLoggingBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMonitoringLoggingDemo();
}
?>
