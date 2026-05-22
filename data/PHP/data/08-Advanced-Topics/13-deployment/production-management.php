<?php
/**
 * Production Management and Monitoring
 * 
 * This file demonstrates production server management,
 * monitoring, maintenance, and operational practices.
 */

// Production Server Monitor
class ProductionMonitor {
    private array $servers = [];
    private array $metrics = [];
    private array $alerts = [];
    private array $thresholds = [];
    
    public function __construct() {
        $this->initializeThresholds();
        $this->initializeServers();
    }
    
    private function initializeThresholds(): void {
        $this->thresholds = [
            'cpu_usage' => ['warning' => 70, 'critical' => 90],
            'memory_usage' => ['warning' => 75, 'critical' => 90],
            'disk_usage' => ['warning' => 80, 'critical' => 95],
            'load_average' => ['warning' => 2.0, 'critical' => 4.0],
            'response_time' => ['warning' => 1.0, 'critical' => 2.0],
            'error_rate' => ['warning' => 5, 'critical' => 10],
            'connections' => ['warning' => 800, 'critical' => 1000]
        ];
    }
    
    private function initializeServers(): void {
        $this->servers = [
            'web-01' => [
                'host' => '192.168.1.10',
                'role' => 'web',
                'environment' => 'production',
                'status' => 'active'
            ],
            'web-02' => [
                'host' => '192.168.1.11',
                'role' => 'web',
                'environment' => 'production',
                'status' => 'active'
            ],
            'db-01' => [
                'host' => '192.168.1.20',
                'role' => 'database',
                'environment' => 'production',
                'status' => 'active'
            ],
            'cache-01' => [
                'host' => '192.168.1.30',
                'role' => 'cache',
                'environment' => 'production',
                'status' => 'active'
            ]
        ];
    }
    
    public function collectMetrics(): void {
        foreach ($this->servers as $serverName => $server) {
            if ($server['status'] !== 'active') {
                continue;
            }
            
            $this->metrics[$serverName] = [
                'timestamp' => time(),
                'cpu_usage' => $this->getCpuUsage($serverName),
                'memory_usage' => $this->getMemoryUsage($serverName),
                'disk_usage' => $this->getDiskUsage($serverName),
                'load_average' => $this->getLoadAverage($serverName),
                'response_time' => $this->getResponseTime($serverName),
                'error_rate' => $this->getErrorRate($serverName),
                'connections' => $this->getConnections($serverName),
                'uptime' => $this->getUptime($serverName),
                'processes' => $this->getProcessCount($serverName)
            ];
            
            $this->checkThresholds($serverName);
        }
    }
    
    private function getCpuUsage(string $server): float {
        // Simulate CPU usage with realistic patterns
        $base = rand(20, 60);
        $spike = rand(1, 100) <= 5 ? rand(20, 40) : 0;
        return min(100, $base + $spike);
    }
    
    private function getMemoryUsage(string $server): float {
        // Simulate memory usage
        $base = rand(40, 70);
        $spike = rand(1, 100) <= 3 ? rand(15, 25) : 0;
        return min(100, $base + $spike);
    }
    
    private function getDiskUsage(string $server): float {
        // Simulate disk usage (gradually increases)
        static $diskUsage = [];
        $diskUsage[$server] = $diskUsage[$server] ?? rand(30, 60);
        $diskUsage[$server] += rand(0, 2);
        return min(100, $diskUsage[$server]);
    }
    
    private function getLoadAverage(string $server): float {
        // Simulate load average
        $base = rand(0.5, 2.5);
        $spike = rand(1, 100) <= 8 ? rand(1, 3) : 0;
        return $base + $spike;
    }
    
    private function getResponseTime(string $server): float {
        // Simulate response time in seconds
        $base = rand(100, 800) / 1000; // 0.1-0.8s
        $spike = rand(1, 100) <= 10 ? rand(500, 2000) / 1000 : 0;
        return $base + $spike;
    }
    
    private function getErrorRate(string $server): float {
        // Simulate error rate percentage
        $base = rand(0, 3);
        $spike = rand(1, 100) <= 5 ? rand(5, 15) : 0;
        return $base + $spike;
    }
    
    private function getConnections(string $server): int {
        // Simulate connection count
        return rand(200, 1200);
    }
    
    private function getUptime(string $server): int {
        // Simulate uptime in days
        static $uptime = [];
        $uptime[$server] = $uptime[$server] ?? rand(30, 365);
        return $uptime[$server];
    }
    
    private function getProcessCount(string $server): int {
        // Simulate process count
        return rand(50, 200);
    }
    
    private function checkThresholds(string $serverName): void {
        $metrics = $this->metrics[$serverName];
        
        foreach ($metrics as $metric => $value) {
            if (!isset($this->thresholds[$metric])) {
                continue;
            }
            
            $thresholds = $this->thresholds[$metric];
            
            if ($value >= $thresholds['critical']) {
                $this->createAlert($serverName, $metric, 'critical', $value, $thresholds['critical']);
            } elseif ($value >= $thresholds['warning']) {
                $this->createAlert($serverName, $metric, 'warning', $value, $thresholds['warning']);
            }
        }
    }
    
    private function createAlert(string $server, string $metric, string $severity, float $value, float $threshold): void {
        $alert = [
            'id' => uniqid('alert_', true),
            'server' => $server,
            'metric' => $metric,
            'severity' => $severity,
            'value' => $value,
            'threshold' => $threshold,
            'timestamp' => time(),
            'message' => $this->generateAlertMessage($server, $metric, $severity, $value, $threshold)
        ];
        
        $this->alerts[] = $alert;
        
        echo "🚨 ALERT [{$severity}]: {$alert['message']}\n";
    }
    
    private function generateAlertMessage(string $server, string $metric, string $severity, float $value, float $threshold): string {
        $metricNames = [
            'cpu_usage' => 'CPU Usage',
            'memory_usage' => 'Memory Usage',
            'disk_usage' => 'Disk Usage',
            'load_average' => 'Load Average',
            'response_time' => 'Response Time',
            'error_rate' => 'Error Rate',
            'connections' => 'Active Connections'
        ];
        
        $name = $metricNames[$metric] ?? $metric;
        $unit = in_array($metric, ['cpu_usage', 'memory_usage', 'disk_usage', 'error_rate']) ? '%' : '';
        
        return "{$server} {$name} is {$value}{$unit} (threshold: {$threshold}{$unit})";
    }
    
    public function getMetrics(): array {
        return $this->metrics;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function getServerStatus(): array {
        $status = [];
        
        foreach ($this->servers as $serverName => $server) {
            $status[$serverName] = [
                'host' => $server['host'],
                'role' => $server['role'],
                'environment' => $server['environment'],
                'status' => $server['status'],
                'metrics' => $this->metrics[$serverName] ?? null,
                'alerts' => array_filter($this->alerts, fn($alert) => $alert['server'] === $serverName)
            ];
        }
        
        return $status;
    }
    
    public function generateReport(): array {
        $report = [
            'timestamp' => time(),
            'total_servers' => count($this->servers),
            'active_servers' => count(array_filter($this->servers, fn($s) => $s['status'] === 'active')),
            'total_alerts' => count($this->alerts),
            'critical_alerts' => count(array_filter($this->alerts, fn($a) => $a['severity'] === 'critical')),
            'warning_alerts' => count(array_filter($this->alerts, fn($a) => $a['severity'] === 'warning')),
            'servers' => $this->getServerStatus()
        ];
        
        return $report;
    }
}

// Log Manager
class LogManager {
    private array $logFiles = [];
    private array $logLevels = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
    private array $patterns = [];
    
    public function __construct() {
        $this->initializeLogFiles();
        $this->initializePatterns();
    }
    
    private function initializeLogFiles(): void {
        $this->logFiles = [
            'application' => '/var/log/app/application.log',
            'access' => '/var/log/nginx/access.log',
            'error' => '/var/log/nginx/error.log',
            'php-fpm' => '/var/log/php8.1-fpm.log',
            'system' => '/var/log/syslog',
            'security' => '/var/log/security.log'
        ];
    }
    
    private function initializePatterns(): void {
        $this->patterns = [
            'error' => '/\b(error|exception|fatal|critical)\b/i',
            'sql_error' => '/\b(sql|mysql|database)\b.*\b(error|failed|exception)\b/i',
            'security' => '/\b(attack|intrusion|breach|unauthorized|forbidden)\b/i',
            'performance' => '/\b(slow|timeout|memory|performance)\b/i',
            'php_fatal' => '/\b(Fatal error|Parse error|Call to undefined function)\b/i'
        ];
    }
    
    public function analyzeLogs(): array {
        $analysis = [];
        
        foreach ($this->logFiles as $logType => $filePath) {
            $analysis[$logType] = $this->analyzeLogFile($filePath, $logType);
        }
        
        return $analysis;
    }
    
    private function analyzeLogFile(string $filePath, string $logType): array {
        $analysis = [
            'file_path' => $filePath,
            'file_size' => 0,
            'line_count' => 0,
            'error_count' => 0,
            'warning_count' => 0,
            'patterns' => [],
            'recent_errors' => [],
            'last_modified' => null
        ];
        
        // Simulate log analysis
        $analysis['file_size'] = rand(1000, 50000);
        $analysis['line_count'] = rand(100, 10000);
        $analysis['error_count'] = rand(0, 50);
        $analysis['warning_count'] = rand(0, 100);
        $analysis['last_modified'] = time() - rand(0, 3600);
        
        // Analyze patterns
        foreach ($this->patterns as $patternName => $pattern) {
            $matchCount = rand(0, 10);
            if ($matchCount > 0) {
                $analysis['patterns'][$patternName] = [
                    'count' => $matchCount,
                    'severity' => $this->getPatternSeverity($patternName)
                ];
            }
        }
        
        // Generate recent errors
        for ($i = 0; $i < min(5, $analysis['error_count']); $i++) {
            $analysis['recent_errors'][] = [
                'timestamp' => time() - rand(0, 7200),
                'message' => $this->generateLogMessage($patternName),
                'level' => 'ERROR'
            ];
        }
        
        return $analysis;
    }
    
    private function getPatternSeverity(string $pattern): string {
        $severities = [
            'error' => 'high',
            'sql_error' => 'critical',
            'security' => 'critical',
            'performance' => 'medium',
            'php_fatal' => 'critical'
        ];
        
        return $severities[$pattern] ?? 'medium';
    }
    
    private function generateLogMessage(string $type): string {
        $messages = [
            'error' => [
                'Database connection failed',
                'File not found: /path/to/file.php',
                'Call to undefined method',
                'Division by zero'
            ],
            'sql_error' => [
                'SQLSTATE[HY000]: General error',
                'MySQL server has gone away',
                'Table doesn\'t exist',
                'Duplicate entry for key'
            ],
            'security' => [
                'Potential XSS attack detected',
                'Failed login attempt from IP',
                'SQL injection attempt blocked',
                'Unauthorized access attempt'
            ],
            'performance' => [
                'Slow query detected: 5.2s',
                'Memory limit exceeded',
                'Request timeout',
                'High CPU usage detected'
            ],
            'php_fatal' => [
                'Fatal error: Call to undefined function',
                'Parse error: syntax error, unexpected',
                'Allowed memory size exhausted'
            ]
        ];
        
        $typeMessages = $messages[$type] ?? ['Generic error message'];
        return $typeMessages[array_rand($typeMessages)];
    }
    
    public function searchLogs(string $pattern, int $limit = 100): array {
        // Simulate log search
        $results = [];
        
        for ($i = 0; $i < min($limit, 50); $i++) {
            $results[] = [
                'timestamp' => time() - rand(0, 86400),
                'file' => array_rand($this->logFiles),
                'line' => rand(1, 10000),
                'message' => $this->generateLogMessage('error'),
                'level' => 'ERROR'
            ];
        }
        
        return $results;
    }
    
    public function rotateLogs(): array {
        $rotationResults = [];
        
        foreach ($this->logFiles as $logType => $filePath) {
            $rotationResults[$logType] = [
                'file' => $filePath,
                'rotated' => true,
                'backup_file' => $filePath . '.' . date('Y-m-d-H-i-s'),
                'size_before' => rand(1000, 50000),
                'size_after' => 0,
                'timestamp' => time()
            ];
        }
        
        return $rotationResults;
    }
    
    public function getLogStatistics(): array {
        $stats = [
            'total_files' => count($this->logFiles),
            'total_size' => 0,
            'error_count' => 0,
            'warning_count' => 0,
            'patterns_found' => []
        ];
        
        foreach ($this->logFiles as $logType => $filePath) {
            $analysis = $this->analyzeLogFile($filePath, $logType);
            $stats['total_size'] += $analysis['file_size'];
            $stats['error_count'] += $analysis['error_count'];
            $stats['warning_count'] += $analysis['warning_count'];
            
            foreach ($analysis['patterns'] as $pattern => $data) {
                if (!isset($stats['patterns_found'][$pattern])) {
                    $stats['patterns_found'][$pattern] = 0;
                }
                $stats['patterns_found'][$pattern] += $data['count'];
            }
        }
        
        return $stats;
    }
}

// Backup Manager
class BackupManager {
    private array $backupConfig = [];
    private array $backupHistory = [];
    
    public function __construct() {
        $this->initializeBackupConfig();
    }
    
    private function initializeBackupConfig(): void {
        $this->backupConfig = [
            'database' => [
                'enabled' => true,
                'schedule' => '0 2 * * *', // 2 AM daily
                'retention_days' => 30,
                'compression' => true,
                'destination' => '/backups/database'
            ],
            'files' => [
                'enabled' => true,
                'schedule' => '0 3 * * *', // 3 AM daily
                'retention_days' => 7,
                'compression' => true,
                'destination' => '/backups/files',
                'include' => [
                    '/var/www/html/storage',
                    '/var/www/html/config',
                    '/var/log'
                ]
            ],
            'application' => [
                'enabled' => true,
                'schedule' => '0 1 * * 0', // 1 AM weekly
                'retention_days' => 14,
                'compression' => true,
                'destination' => '/backups/application'
            ]
        ];
    }
    
    public function createBackup(string $type): array {
        if (!isset($this->backupConfig[$type])) {
            return ['success' => false, 'error' => "Unknown backup type: $type"];
        }
        
        $config = $this->backupConfig[$type];
        
        if (!$config['enabled']) {
            return ['success' => false, 'error' => "Backup type '$type' is disabled"];
        }
        
        $backupId = uniqid('backup_', true);
        $startTime = microtime(true);
        
        echo "Starting backup: $type\n";
        echo str_repeat("-", 25) . "\n";
        
        try {
            $result = $this->performBackup($type, $config, $backupId);
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            $backup = [
                'id' => $backupId,
                'type' => $type,
                'status' => 'success',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $duration,
                'size' => $result['size'],
                'file_path' => $result['file_path'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->backupHistory[] = $backup;
            
            echo "✅ Backup completed successfully\n";
            echo "File: {$backup['file_path']}\n";
            echo "Size: {$backup['size']}\n";
            echo "Duration: " . round($duration, 2) . "s\n";
            
            return [
                'success' => true,
                'backup' => $backup
            ];
            
        } catch (Exception $e) {
            $backup = [
                'id' => $backupId,
                'type' => $type,
                'status' => 'failed',
                'start_time' => $startTime,
                'end_time' => microtime(true),
                'error' => $e->getMessage(),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->backupHistory[] = $backup;
            
            echo "❌ Backup failed: {$e->getMessage()}\n";
            
            return [
                'success' => false,
                'backup' => $backup
            ];
        }
    }
    
    private function performBackup(string $type, array $config, string $backupId): array {
        switch ($type) {
            case 'database':
                return $this->backupDatabase($config, $backupId);
            case 'files':
                return $this->backupFiles($config, $backupId);
            case 'application':
                return $this->backupApplication($config, $backupId);
            default:
                throw new InvalidArgumentException("Unsupported backup type: $type");
        }
    }
    
    private function backupDatabase(array $config, string $backupId): array {
        echo "  Creating database dump\n";
        usleep(2000000); // 2 seconds
        
        $filename = $config['destination'] . '/db_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        if ($config['compression']) {
            $filename .= '.gz';
        }
        
        $size = rand(10000000, 50000000); // 10-50MB
        
        return [
            'file_path' => $filename,
            'size' => $this->formatBytes($size)
        ];
    }
    
    private function backupFiles(array $config, string $backupId): array {
        echo "  Archiving files\n";
        
        foreach ($config['include'] as $path) {
            echo "    Including: $path\n";
            usleep(200000); // 0.2 seconds
        }
        
        $filename = $config['destination'] . '/files_backup_' . date('Y-m-d_H-i-s') . '.tar';
        
        if ($config['compression']) {
            $filename .= '.gz';
        }
        
        $size = rand(50000000, 200000000); // 50-200MB
        
        return [
            'file_path' => $filename,
            'size' => $this->formatBytes($size)
        ];
    }
    
    private function backupApplication(array $config, string $backupId): array {
        echo "  Creating application snapshot\n";
        usleep(3000000); // 3 seconds
        
        $filename = $config['destination'] . '/app_backup_' . date('Y-m-d_H-i-s') . '.tar';
        
        if ($config['compression']) {
            $filename .= '.gz';
        }
        
        $size = rand(100000000, 500000000); // 100-500MB
        
        return [
            'file_path' => $filename,
            'size' => $this->formatBytes($size)
        ];
    }
    
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    public function restoreBackup(string $backupId): array {
        $backup = $this->findBackup($backupId);
        
        if (!$backup) {
            return ['success' => false, 'error' => "Backup not found: $backupId"];
        }
        
        if ($backup['status'] !== 'success') {
            return ['success' => false, 'error' => "Cannot restore failed backup: $backupId"];
        }
        
        echo "Restoring backup: {$backup['id']}\n";
        echo "Type: {$backup['type']}\n";
        echo "File: {$backup['file_path']}\n";
        echo "Size: {$backup['size']}\n";
        echo "Created: {$backup['created_at']}\n";
        
        // Simulate restore process
        echo "  Extracting backup\n";
        usleep(2000000); // 2 seconds
        
        echo "  Restoring data\n";
        usleep(3000000); // 3 seconds
        
        echo "✅ Restore completed successfully\n";
        
        return [
            'success' => true,
            'backup' => $backup
        ];
    }
    
    private function findBackup(string $backupId): ?array {
        foreach ($this->backupHistory as $backup) {
            if ($backup['id'] === $backupId) {
                return $backup;
            }
        }
        return null;
    }
    
    public function cleanupOldBackups(): array {
        $cleaned = [];
        $currentTime = time();
        
        foreach ($this->backupHistory as $backup) {
            $ageInDays = ($currentTime - strtotime($backup['created_at'])) / 86400;
            $retentionDays = $this->backupConfig[$backup['type']]['retention_days'] ?? 30;
            
            if ($ageInDays > $retentionDays) {
                echo "Deleting old backup: {$backup['id']} ({$ageInDays} days old)\n";
                $cleaned[] = $backup;
            }
        }
        
        $this->backupHistory = array_filter($this->backupHistory, function($backup) use ($cleaned) {
            return !in_array($backup, $cleaned);
        });
        
        return [
            'cleaned_count' => count($cleaned),
            'remaining_count' => count($this->backupHistory)
        ];
    }
    
    public function getBackupHistory(): array {
        return $this->backupHistory;
    }
    
    public function getBackupStatistics(): array {
        $stats = [
            'total_backups' => count($this->backupHistory),
            'successful_backups' => 0,
            'failed_backups' => 0,
            'total_size' => 0,
            'by_type' => []
        ];
        
        foreach ($this->backupHistory as $backup) {
            if ($backup['status'] === 'success') {
                $stats['successful_backups']++;
                // Convert size back to bytes for calculation
                $sizeBytes = $this->parseBytes($backup['size']);
                $stats['total_size'] += $sizeBytes;
            } else {
                $stats['failed_backups']++;
            }
            
            $type = $backup['type'];
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['count' => 0, 'successful' => 0, 'failed' => 0];
            }
            $stats['by_type'][$type]['count']++;
            
            if ($backup['status'] === 'success') {
                $stats['by_type'][$type]['successful']++;
            } else {
                $stats['by_type'][$type]['failed']++;
            }
        }
        
        $stats['total_size'] = $this->formatBytes($stats['total_size']);
        
        return $stats;
    }
    
    private function parseBytes(string $size): int {
        $units = ['B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824, 'TB' => 1099511627776];
        
        preg_match('/([\d.]+)\s*([A-Z]+)/', $size, $matches);
        
        if (!$matches) {
            return 0;
        }
        
        $value = (float) $matches[1];
        $unit = $matches[2];
        
        return (int) ($value * ($units[$unit] ?? 1));
    }
}

// Maintenance Scheduler
class MaintenanceScheduler {
    private array $tasks = [];
    private array $schedule = [];
    
    public function __construct() {
        $this->initializeTasks();
    }
    
    private function initializeTasks(): void {
        $this->tasks = [
            'log_rotation' => [
                'name' => 'Log Rotation',
                'schedule' => '0 0 * * *', // Daily at midnight
                'enabled' => true,
                'last_run' => null,
                'next_run' => null
            ],
            'backup_cleanup' => [
                'name' => 'Backup Cleanup',
                'schedule' => '0 1 * * 0', // Weekly on Sunday at 1 AM
                'enabled' => true,
                'last_run' => null,
                'next_run' => null
            ],
            'cache_cleanup' => [
                'name' => 'Cache Cleanup',
                'schedule' => '0 2 * * *', // Daily at 2 AM
                'enabled' => true,
                'last_run' => null,
                'next_run' => null
            ],
            'security_scan' => [
                'name' => 'Security Scan',
                'schedule' => '0 3 * * 1', // Weekly on Monday at 3 AM
                'enabled' => true,
                'last_run' => null,
                'next_run' => null
            ],
            'performance_check' => [
                'name' => 'Performance Check',
                'schedule' => '*/30 * * * *', // Every 30 minutes
                'enabled' => true,
                'last_run' => null,
                'next_run' => null
            ]
        ];
    }
    
    public function addTask(string $name, array $config): void {
        $this->tasks[$name] = array_merge([
            'name' => $name,
            'schedule' => '0 0 * * *',
            'enabled' => true,
            'last_run' => null,
            'next_run' => null
        ], $config);
    }
    
    public function runScheduledTasks(): array {
        $currentTime = time();
        $results = [];
        
        foreach ($this->tasks as $taskName => $task) {
            if (!$task['enabled']) {
                continue;
            }
            
            if ($this->shouldRunTask($task, $currentTime)) {
                echo "Running scheduled task: {$task['name']}\n";
                $result = $this->executeTask($taskName, $task);
                $results[$taskName] = $result;
                
                $this->tasks[$taskName]['last_run'] = $currentTime;
                $this->tasks[$taskName]['next_run'] = $this->calculateNextRun($task['schedule'], $currentTime);
            }
        }
        
        return $results;
    }
    
    private function shouldRunTask(array $task, int $currentTime): bool {
        if ($task['last_run'] === null) {
            return true;
        }
        
        $nextRun = $this->calculateNextRun($task['schedule'], $task['last_run']);
        
        return $currentTime >= $nextRun;
    }
    
    private function calculateNextRun(string $schedule, int $timestamp): int {
        // Simplified cron calculation
        $parts = explode(' ', $schedule);
        
        $minute = $parts[0];
        $hour = $parts[1];
        $day = $parts[2];
        $month = $parts[3];
        $weekday = $parts[4];
        
        $nextRun = $timestamp + 3600; // Default to 1 hour later
        
        return $nextRun;
    }
    
    private function executeTask(string $taskName, array $task): array {
        $startTime = microtime(true);
        
        try {
            switch ($taskName) {
                case 'log_rotation':
                    $result = $this->performLogRotation();
                    break;
                case 'backup_cleanup':
                    $result = $this->performBackupCleanup();
                    break;
                case 'cache_cleanup':
                    $result = $this->performCacheCleanup();
                    break;
                case 'security_scan':
                    $result = $this->performSecurityScan();
                    break;
                case 'performance_check':
                    $result = $this->performPerformanceCheck();
                    break;
                default:
                    $result = ['success' => false, 'message' => "Unknown task: $taskName"];
            }
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            return array_merge($result, [
                'duration' => $duration,
                'executed_at' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'duration' => microtime(true) - $startTime,
                'executed_at' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    private function performLogRotation(): array {
        echo "  Rotating log files\n";
        
        // Simulate log rotation
        $rotatedFiles = rand(5, 15);
        $freedSpace = rand(1000000, 10000000);
        
        usleep(500000); // 0.5 seconds
        
        return [
            'success' => true,
            'rotated_files' => $rotatedFiles,
            'freed_space' => $this->formatBytes($freedSpace)
        ];
    }
    
    private function performBackupCleanup(): array {
        echo "  Cleaning old backups\n";
        
        $backupManager = new BackupManager();
        $result = $backupManager->cleanupOldBackups();
        
        usleep(1000000); // 1 second
        
        return [
            'success' => true,
            'cleaned_count' => $result['cleaned_count'],
            'remaining_count' => $result['remaining_count']
        ];
    }
    
    private function performCacheCleanup(): array {
        echo "  Cleaning cache files\n";
        
        // Simulate cache cleanup
        $cleanedFiles = rand(10, 50);
        $freedSpace = rand(500000, 5000000);
        
        usleep(300000); // 0.3 seconds
        
        return [
            'success' => true,
            'cleaned_files' => $cleanedFiles,
            'freed_space' => $this->formatBytes($freedSpace)
        ];
    }
    
    private function performSecurityScan(): array {
        echo "  Running security scan\n";
        
        // Simulate security scan
        $scannedFiles = rand(100, 1000);
        $threatsFound = rand(0, 5);
        
        usleep(2000000); // 2 seconds
        
        return [
            'success' => true,
            'scanned_files' => $scannedFiles,
            'threats_found' => $threatsFound,
            'severity' => $threatsFound > 0 ? 'medium' : 'low'
        ];
    }
    
    private function performPerformanceCheck(): array {
        echo "  Checking performance metrics\n";
        
        // Simulate performance check
        $metrics = [
            'cpu_usage' => rand(20, 80),
            'memory_usage' => rand(30, 85),
            'disk_io' => rand(10, 90),
            'network_latency' => rand(1, 50)
        ];
        
        usleep(100000); // 0.1 seconds
        
        return [
            'success' => true,
            'metrics' => $metrics,
            'status' => $this->evaluatePerformance($metrics)
        ];
    }
    
    private function evaluatePerformance(array $metrics): string {
        if ($metrics['cpu_usage'] > 80 || $metrics['memory_usage'] > 85) {
            return 'warning';
        } elseif ($metrics['cpu_usage'] > 90 || $metrics['memory_usage'] > 95) {
            return 'critical';
        }
        
        return 'good';
    }
    
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    public function getTaskStatus(): array {
        return $this->tasks;
    }
    
    public function getSchedule(): array {
        return array_map(function($task) {
            return [
                'name' => $task['name'],
                'schedule' => $task['schedule'],
                'enabled' => $task['enabled'],
                'last_run' => $task['last_run'] ? date('Y-m-d H:i:s', $task['last_run']) : 'Never',
                'next_run' => $task['next_run'] ? date('Y-m-d H:i:s', $task['next_run']) : 'Unknown'
            ];
        }, $this->tasks);
    }
}

// Production Management Examples
class ProductionManagementExamples {
    private ProductionMonitor $monitor;
    private LogManager $logManager;
    private BackupManager $backupManager;
    private MaintenanceScheduler $scheduler;
    
    public function __construct() {
        $this->monitor = new ProductionMonitor();
        $this->logManager = new LogManager();
        $this->backupManager = new BackupManager();
        $this->scheduler = new MaintenanceScheduler();
    }
    
    public function demonstrateMonitoring(): void {
        echo "Production Monitoring Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Collect metrics
        $this->monitor->collectMetrics();
        
        // Show server status
        $status = $this->monitor->getServerStatus();
        
        echo "Server Status:\n";
        foreach ($status as $serverName => $serverInfo) {
            echo "\n{$serverName} ({$serverInfo['host']}):\n";
            echo "  Role: {$serverInfo['role']}\n";
            echo "  Status: {$serverInfo['status']}\n";
            
            if ($serverInfo['metrics']) {
                $metrics = $serverInfo['metrics'];
                echo "  CPU: {$metrics['cpu_usage']}%\n";
                echo "  Memory: {$metrics['memory_usage']}%\n";
                echo "  Disk: {$metrics['disk_usage']}%\n";
                echo "  Load: {$metrics['load_average']}\n";
                echo "  Response Time: {$metrics['response_time']}s\n";
                echo "  Connections: {$metrics['connections']}\n";
                echo "  Uptime: {$metrics['uptime']} days\n";
            }
            
            if (!empty($serverInfo['alerts'])) {
                echo "  Alerts: " . count($serverInfo['alerts']) . "\n";
            }
        }
        
        // Show alerts
        $alerts = $this->monitor->getAlerts();
        if (!empty($alerts)) {
            echo "\nActive Alerts: " . count($alerts) . "\n";
        }
    }
    
    public function demonstrateLogManagement(): void {
        echo "\nLog Management Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Analyze logs
        $analysis = $this->logManager->analyzeLogs();
        
        echo "Log Analysis Results:\n";
        foreach ($analysis as $logType => $data) {
            echo "\n{$logType} Logs:\n";
            echo "  File: {$data['file_path']}\n";
            echo "  Size: {$this->formatBytes($data['file_size'])}\n";
            echo "  Lines: {$data['line_count']}\n";
            echo "  Errors: {$data['error_count']}\n";
            echo "  Warnings: {$data['warning_count']}\n";
            
            if (!empty($data['patterns'])) {
                echo "  Patterns:\n";
                foreach ($data['patterns'] as $pattern => $info) {
                    echo "    {$pattern}: {$info['count']} occurrences ({$info['severity']})\n";
                }
            }
            
            if (!empty($data['recent_errors'])) {
                echo "  Recent Errors:\n";
                foreach (array_slice($data['recent_errors'], 0, 3) as $error) {
                    echo "    " . date('H:i:s', $error['timestamp']) . " - {$error['message']}\n";
                }
            }
        }
        
        // Show statistics
        $stats = $this->logManager->getLogStatistics();
        echo "\nLog Statistics:\n";
        echo "Total Files: {$stats['total_files']}\n";
        echo "Total Size: {$stats['total_size']}\n";
        echo "Total Errors: {$stats['error_count']}\n";
        echo "Total Warnings: {$stats['warning_count']}\n";
        
        if (!empty($stats['patterns_found'])) {
            echo "Patterns Found:\n";
            foreach ($stats['patterns_found'] as $pattern => $count) {
                echo "  {$pattern}: $count occurrences\n";
            }
        }
    }
    
    public function demonstrateBackupManagement(): void {
        echo "\nBackup Management Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create different types of backups
        $backupTypes = ['database', 'files', 'application'];
        
        foreach ($backupTypes as $type) {
            echo "\nCreating {$type} backup:\n";
            $result = $this->backupManager->createBackup($type);
            
            if ($result['success']) {
                echo "✅ Backup created successfully\n";
            } else {
                echo "❌ Backup failed: {$result['error']}\n";
            }
        }
        
        // Show backup statistics
        $stats = $this->backupManager->getBackupStatistics();
        echo "\nBackup Statistics:\n";
        echo "Total Backups: {$stats['total_backups']}\n";
        echo "Successful: {$stats['successful_backups']}\n";
        echo "Failed: {$stats['failed_backups']}\n";
        echo "Total Size: {$stats['total_size']}\n";
        
        echo "\nBackups by Type:\n";
        foreach ($stats['by_type'] as $type => $data) {
            echo "  {$type}: {$data['count']} ({$data['successful']} successful, {$data['failed']} failed)\n";
        }
        
        // Cleanup old backups
        echo "\nCleaning up old backups...\n";
        $cleanupResult = $this->backupManager->cleanupOldBackups();
        echo "Cleaned: {$cleanupResult['cleaned_count']} backups\n";
        echo "Remaining: {$cleanupResult['remaining_count']} backups\n";
    }
    
    public function demonstrateMaintenanceScheduler(): void {
        echo "\nMaintenance Scheduler Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show schedule
        $schedule = $this->scheduler->getSchedule();
        
        echo "Maintenance Schedule:\n";
        foreach ($schedule as $task) {
            echo "  {$task['name']}: {$task['schedule']} ({$task['enabled'] ? 'Enabled' : 'Disabled'})\n";
            echo "    Last run: {$task['last_run']}\n";
            echo "    Next run: {$task['next_run']}\n";
        }
        
        // Run scheduled tasks
        echo "\nRunning scheduled tasks...\n";
        $results = $this->scheduler->runScheduledTasks();
        
        if (!empty($results)) {
            echo "\nTask Results:\n";
            foreach ($results as $taskName => $result) {
                $statusIcon = $result['success'] ? '✅' : '❌';
                echo "  {$statusIcon} {$taskName}: " . ($result['success'] ? $result['message'] ?? 'Success' : $result['error']) . "\n";
                echo "    Duration: " . round($result['duration'], 2) . "s\n";
            }
        }
    }
    
    public function demonstrateProductionReport(): void {
        echo "\nProduction Management Report\n";
        echo str_repeat("-", 30) . "\n";
        
        // Generate comprehensive report
        $report = [
            'timestamp' => time(),
            'servers' => $this->monitor->getServerStatus(),
            'alerts' => $this->monitor->getAlerts(),
            'logs' => $this->logManager->analyzeLogs(),
            'backups' => $this->backupManager->getBackupStatistics(),
            'tasks' => $this->scheduler->getTaskStatus()
        ];
        
        echo "Report Generated: " . date('Y-m-d H:i:s', $report['timestamp']) . "\n\n";
        
        // Summary
        $totalServers = count($report['servers']);
        $activeServers = count(array_filter($report['servers'], fn($s) => $s['status'] === 'active'));
        $totalAlerts = count($report['alerts']);
        $criticalAlerts = count(array_filter($report['alerts'], fn($a) => $a['severity'] === 'critical'));
        
        echo "Summary:\n";
        echo "Servers: $activeServers/$totalServers active\n";
        echo "Alerts: $totalAlerts total ($criticalAlerts critical)\n";
        echo "Backups: {$report['backups']['total_backups']} total\n";
        
        // Server health
        echo "\nServer Health:\n";
        foreach ($report['servers'] as $serverName => $server) {
            if ($server['status'] !== 'active') {
                continue;
            }
            
            $metrics = $server['metrics'];
            $health = 'Good';
            
            if ($metrics['cpu_usage'] > 80 || $metrics['memory_usage'] > 85 || $metrics['disk_usage'] > 90) {
                $health = 'Warning';
            }
            if ($metrics['cpu_usage'] > 90 || $metrics['memory_usage'] > 95 || $metrics['disk_usage'] > 95) {
                $health = 'Critical';
            }
            
            $healthIcon = match($health) {
                'Good' => '✅',
                'Warning' => '⚠️',
                'Critical' => '🔴'
            };
            
            echo "  $healthIcon $serverName: $health\n";
        }
        
        // Recent alerts
        if (!empty($report['alerts'])) {
            echo "\nRecent Alerts:\n";
            $recentAlerts = array_slice($report['alerts'], -5);
            foreach ($recentAlerts as $alert) {
                $severityIcon = match($alert['severity']) {
                    'critical' => '🔴',
                    'high' => '🟠',
                    'medium' => '🟡',
                    'low' => '🔵'
                };
                echo "  $severityIcon {$alert['server']} - {$alert['message']}\n";
            }
        }
        
        // Backup status
        echo "\nBackup Status:\n";
        echo "  Last backup: " . date('Y-m-d H:i:s', time() - rand(3600, 86400)) . "\n";
        echo "  Success rate: " . round(($report['backups']['successful_backups'] / max(1, $report['backups']['total_backups'])) * 100, 1) . "%\n";
        echo "  Total size: {$report['backups']['total_size']}\n";
    }
    
    public function runAllExamples(): void {
        echo "Production Management Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateMonitoring();
        $this->demonstrateLogManagement();
        $this->demonstrateBackupManagement();
        $this->demonstrateMaintenanceScheduler();
        $this->demonstrateProductionReport();
    }
    
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

// Production Management Best Practices
function printProductionManagementBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Production Management Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Monitoring:\n";
    echo "   • Monitor all critical metrics\n";
    echo "   • Set appropriate thresholds\n";
    echo "   • Implement alerting\n";
    echo "   • Use dashboards for visibility\n";
    echo "   • Monitor application health\n\n";
    
    echo "2. Log Management:\n";
    echo "   • Implement structured logging\n";
    echo "   • Use log rotation\n";
    echo "   • Centralize log collection\n";
    echo "   • Monitor error patterns\n";
    echo "   • Archive old logs\n\n";
    
    echo "3. Backup Strategy:\n";
    echo "   • Implement automated backups\n";
    echo "   • Use multiple backup types\n";
    echo "   • Test restore procedures\n";
    echo "   • Store backups securely\n";
    echo " • Implement retention policies\n\n";
    
    echo "4. Maintenance:\n";
    echo "   • Schedule regular maintenance\n";
    echo "   • Automate routine tasks\n";
    echo "   • Monitor maintenance results\n";
    echo "   • Document procedures\n";
    echo "   • Use maintenance windows\n\n";
    
    echo "5. Security:\n";
    echo "   • Monitor security events\n";
    echo "   • Regular security scans\n";
    echo "   • Update systems regularly\n";
    echo "   • Implement access controls\n";
    echo "   • Use intrusion detection\n\n";
    
    echo "6. Performance:\n";
    echo "   • Monitor performance metrics\n";
    echo "   • Optimize bottlenecks\n";
    echo "   • Use caching effectively\n";
    echo "   • Monitor resource usage\n";
    echo "   • Implement scaling strategies";
}

// Main execution
function runProductionManagementDemo(): void {
    $examples = new ProductionManagementExamples();
    $examples->runAllExamples();
    printProductionManagementBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runProductionManagementDemo();
}
?>
