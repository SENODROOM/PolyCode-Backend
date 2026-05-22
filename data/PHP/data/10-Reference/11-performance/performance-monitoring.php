<?php
/**
 * Performance Monitoring and Metrics
 * 
 * This file demonstrates real-time performance monitoring,
 * metrics collection, and performance analysis tools.
 */

// Performance Metrics Collector
class PerformanceMetrics {
    private array $metrics = [];
    private array $timers = [];
    private array $counters = [];
    private array $gauges = [];
    private array $histograms = [];
    
    public function startTimer(string $name): void {
        $this->timers[$name] = [
            'start' => microtime(true),
            'start_memory' => memory_get_usage()
        ];
    }
    
    public function endTimer(string $name): array {
        if (!isset($this->timers[$name])) {
            throw new InvalidArgumentException("Timer '$name' not started");
        }
        
        $timer = $this->timers[$name];
        $duration = microtime(true) - $timer['start'];
        $memoryUsed = memory_get_usage() - $timer['start_memory'];
        
        $result = [
            'name' => $name,
            'duration' => $duration,
            'duration_ms' => round($duration * 1000, 4),
            'memory_used' => $memoryUsed,
            'memory_used_mb' => round($memoryUsed / 1024 / 1024, 4),
            'timestamp' => microtime(true)
        ];
        
        $this->metrics[$name][] = $result;
        unset($this->timers[$name]);
        
        return $result;
    }
    
    public function incrementCounter(string $name, int $value = 1): void {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = ['value' => 0, 'timestamp' => microtime(true)];
        }
        
        $this->counters[$name]['value'] += $value;
        $this->counters[$name]['timestamp'] = microtime(true);
    }
    
    public function setGauge(string $name, float $value): void {
        $this->gauges[$name] = [
            'value' => $value,
            'timestamp' => microtime(true)
        ];
    }
    
    public function recordHistogram(string $name, float $value): void {
        if (!isset($this->histograms[$name])) {
            $this->histograms[$name] = [
                'values' => [],
                'count' => 0,
                'sum' => 0,
                'min' => PHP_FLOAT_MAX,
                'max' => PHP_FLOAT_MIN
            ];
        }
        
        $histogram = &$this->histograms[$name];
        $histogram['values'][] = $value;
        $histogram['count']++;
        $histogram['sum'] += $value;
        $histogram['min'] = min($histogram['min'], $value);
        $histogram['max'] = max($histogram['max'], $value);
    }
    
    public function getMetrics(): array {
        return [
            'timers' => $this->metrics,
            'counters' => $this->counters,
            'gauges' => $this->gauges,
            'histograms' => $this->histograms
        ];
    }
    
    public function getTimerStats(string $name): array {
        if (!isset($this->metrics[$name]) || empty($this->metrics[$name])) {
            return [];
        }
        
        $values = array_column($this->metrics[$name], 'duration');
        sort($values);
        
        $count = count($values);
        $sum = array_sum($values);
        
        return [
            'count' => $count,
            'sum' => $sum,
            'min' => min($values),
            'max' => max($values),
            'mean' => $sum / $count,
            'median' => $count % 2 === 0 ? ($values[$count/2 - 1] + $values[$count/2]) / 2 : $values[floor($count/2)],
            'p95' => $values[intval($count * 0.95)],
            'p99' => $values[intval($count * 0.99)]
        ];
    }
    
    public function getHistogramStats(string $name): array {
        if (!isset($this->histograms[$name])) {
            return [];
        }
        
        $histogram = $this->histograms[$name];
        $values = $histogram['values'];
        sort($values);
        
        $count = $histogram['count'];
        
        return [
            'count' => $count,
            'sum' => $histogram['sum'],
            'min' => $histogram['min'],
            'max' => $histogram['max'],
            'mean' => $histogram['sum'] / $count,
            'median' => $count % 2 === 0 ? ($values[$count/2 - 1] + $values[$count/2]) / 2 : $values[floor($count/2)],
            'p95' => $values[intval($count * 0.95)],
            'p99' => $values[intval($count * 0.99)]
        ];
    }
    
    public function reset(): void {
        $this->metrics = [];
        $this->timers = [];
        $this->counters = [];
        $this->gauges = [];
        $this->histograms = [];
    }
}

// System Resource Monitor
class SystemMonitor {
    private array $snapshots = [];
    
    public function takeSnapshot(string $label = null): array {
        $snapshot = [
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(),
            'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 4),
            'memory_peak' => memory_get_peak_usage(),
            'memory_peak_mb' => round(memory_get_peak_usage() / 1024 / 1024, 4),
            'cpu_load' => sys_getloadavg(),
            'disk_usage' => $this->getDiskUsage(),
            'network_io' => $this->getNetworkIO()
        ];
        
        if ($label) {
            $this->snapshots[$label] = $snapshot;
        }
        
        return $snapshot;
    }
    
    private function getDiskUsage(): array {
        $usage = [];
        
        // Get disk usage for current directory
        $free = disk_free_space('.');
        $total = disk_total_space('.');
        
        if ($free !== false && $total !== false) {
            $usage['free'] = $free;
            $usage['free_mb'] = round($free / 1024 / 1024, 4);
            $usage['total'] = $total;
            $usage['total_mb'] = round($total / 1024 / 1024, 4);
            $usage['used'] = $total - $free;
            $usage['used_mb'] = round(($total - $free) / 1024 / 1024, 4);
            $usage['usage_percentage'] = round((($total - $free) / $total) * 100, 2);
        }
        
        return $usage;
    }
    
    private function getNetworkIO(): array {
        // This is a simplified version - in production, you'd read from /proc/net/dev
        return [
            'bytes_sent' => 0,
            'bytes_received' => 0,
            'packets_sent' => 0,
            'packets_received' => 0
        ];
    }
    
    public function getSnapshots(): array {
        return $this->snapshots;
    }
    
    public function getDifference(string $from, string $to): array {
        if (!isset($this->snapshots[$from]) || !isset($this->snapshots[$to])) {
            throw new InvalidArgumentException("Snapshots '$from' or '$to' not found");
        }
        
        $fromSnapshot = $this->snapshots[$from];
        $toSnapshot = $this->snapshots[$to];
        
        return [
            'time_diff' => $toSnapshot['timestamp'] - $fromSnapshot['timestamp'],
            'memory_diff' => $toSnapshot['memory_usage'] - $fromSnapshot['memory_usage'],
            'memory_diff_mb' => round(($toSnapshot['memory_usage'] - $fromSnapshot['memory_usage']) / 1024 / 1024, 4),
            'peak_diff' => $toSnapshot['memory_peak'] - $fromSnapshot['memory_peak'],
            'peak_diff_mb' => round(($toSnapshot['memory_peak'] - $fromSnapshot['memory_peak']) / 1024 / 1024, 4),
            'disk_usage_diff' => $this->calculateDiskDiff($fromSnapshot['disk_usage'], $toSnapshot['disk_usage'])
        ];
    }
    
    private function calculateDiskDiff(array $from, array $to): array {
        if (empty($from) || empty($to)) {
            return [];
        }
        
        return [
            'used_diff' => $to['used'] - $from['used'],
            'used_diff_mb' => round(($to['used'] - $from['used']) / 1024 / 1024, 4),
            'usage_percentage_diff' => $to['usage_percentage'] - $from['usage_percentage']
        ];
    }
    
    public function reset(): void {
        $this->snapshots = [];
    }
}

// Application Performance Monitor
class ApplicationPerformanceMonitor {
    private PerformanceMetrics $metrics;
    private SystemMonitor $systemMonitor;
    private array $alerts = [];
    private array $thresholds = [
        'response_time' => 1.0, // seconds
        'memory_usage' => 100 * 1024 * 1024, // 100MB
        'error_rate' => 0.05, // 5%
        'cpu_usage' => 0.8 // 80%
    ];
    
    public function __construct() {
        $this->metrics = new PerformanceMetrics();
        $this->systemMonitor = new SystemMonitor();
    }
    
    public function setThresholds(array $thresholds): void {
        $this->thresholds = array_merge($this->thresholds, $thresholds);
    }
    
    public function startRequest(string $requestId): void {
        $this->metrics->startTimer("request:$requestId");
        $this->systemMonitor->takeSnapshot("request_start:$requestId");
    }
    
    public function endRequest(string $requestId, bool $success = true): array {
        $result = $this->metrics->endTimer("request:$requestId");
        $this->systemMonitor->takeSnapshot("request_end:$requestId");
        
        $this->metrics->incrementCounter('total_requests');
        
        if (!$success) {
            $this->metrics->incrementCounter('error_requests');
        }
        
        // Check for performance alerts
        $this->checkAlerts($result, $requestId);
        
        return $result;
    }
    
    public function recordDatabaseQuery(string $query, float $duration, int $rows = 0): void {
        $this->metrics->recordHistogram('db_query_duration', $duration);
        $this->metrics->incrementCounter('db_queries');
        $this->metrics->incrementCounter('db_rows_returned', $rows);
        
        if ($duration > $this->thresholds['response_time']) {
            $this->addAlert('slow_query', "Slow query detected: {$query} ({$duration}s)");
        }
    }
    
    public function recordCacheOperation(string $operation, bool $hit): void {
        $this->metrics->incrementCounter("cache_$operation");
        if ($hit) {
            $this->metrics->incrementCounter('cache_hits');
        } else {
            $this->metrics->incrementCounter('cache_misses');
        }
    }
    
    public function recordMemoryUsage(): void {
        $current = memory_get_usage();
        $this->metrics->setGauge('memory_usage', $current);
        
        if ($current > $this->thresholds['memory_usage']) {
            $this->addAlert('high_memory', "High memory usage: " . round($current / 1024 / 1024, 2) . "MB");
        }
    }
    
    public function recordError(string $type, string $message): void {
        $this->metrics->incrementCounter("error_$type");
        $this->addAlert('error', "$type: $message");
    }
    
    private function checkAlerts(array $requestResult, string $requestId): void {
        if ($requestResult['duration'] > $this->thresholds['response_time']) {
            $this->addAlert('slow_request', "Slow request $requestId: {$requestResult['duration_ms']}ms");
        }
        
        $errorRate = $this->calculateErrorRate();
        if ($errorRate > $this->thresholds['error_rate']) {
            $this->addAlert('high_error_rate', "High error rate: " . round($errorRate * 100, 2) . "%");
        }
    }
    
    private function calculateErrorRate(): float {
        $total = $this->metrics->counters['total_requests']['value'] ?? 0;
        $errors = $this->metrics->counters['error_requests']['value'] ?? 0;
        
        return $total > 0 ? $errors / $total : 0;
    }
    
    private function addAlert(string $type, string $message): void {
        $this->alerts[] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => microtime(true)
        ];
    }
    
    public function getReport(): array {
        $report = [
            'timestamp' => microtime(true),
            'metrics' => $this->metrics->getMetrics(),
            'alerts' => $this->alerts,
            'system_snapshots' => $this->systemMonitor->getSnapshots(),
            'summary' => $this->generateSummary()
        ];
        
        return $report;
    }
    
    private function generateSummary(): array {
        $totalRequests = $this->metrics->counters['total_requests']['value'] ?? 0;
        $errorRequests = $this->metrics->counters['error_requests']['value'] ?? 0;
        $errorRate = $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0;
        
        $cacheHits = $this->metrics->counters['cache_hits']['value'] ?? 0;
        $cacheMisses = $this->metrics->counters['cache_misses']['value'] ?? 0;
        $totalCacheOps = $cacheHits + $cacheMisses;
        $cacheHitRate = $totalCacheOps > 0 ? ($cacheHits / $totalCacheOps) * 100 : 0;
        
        return [
            'total_requests' => $totalRequests,
            'error_rate' => round($errorRate, 2),
            'cache_hit_rate' => round($cacheHitRate, 2),
            'total_alerts' => count($this->alerts),
            'avg_response_time' => $this->getAverageResponseTime(),
            'current_memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . 'MB'
        ];
    }
    
    private function getAverageResponseTime(): float {
        $requestTimers = array_filter($this->metrics->getMetrics()['timers'], function($key) {
            return str_starts_with($key, 'request:');
        }, ARRAY_FILTER_USE_KEY);
        
        if (empty($requestTimers)) {
            return 0;
        }
        
        $allDurations = [];
        foreach ($requestTimers as $timers) {
            foreach ($timers as $timer) {
                $allDurations[] = $timer['duration'];
            }
        }
        
        return count($allDurations) > 0 ? array_sum($allDurations) / count($allDurations) : 0;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function clearAlerts(): void {
        $this->alerts = [];
    }
    
    public function reset(): void {
        $this->metrics->reset();
        $this->systemMonitor->reset();
        $this->alerts = [];
    }
}

// Performance Dashboard
class PerformanceDashboard {
    private ApplicationPerformanceMonitor $monitor;
    
    public function __construct(ApplicationPerformanceMonitor $monitor) {
        $this->monitor = $monitor;
    }
    
    public function generateReport(): string {
        $report = $this->monitor->getReport();
        
        $output = "Performance Report\n";
        $output .= str_repeat("=", 50) . "\n";
        $output .= "Generated: " . date('Y-m-d H:i:s', $report['timestamp']) . "\n\n";
        
        // Summary
        $summary = $report['summary'];
        $output .= "Summary:\n";
        $output .= str_repeat("-", 20) . "\n";
        $output .= "Total Requests: {$summary['total_requests']}\n";
        $output .= "Error Rate: {$summary['error_rate']}%\n";
        $output .= "Cache Hit Rate: {$summary['cache_hit_rate']}%\n";
        $output .= "Avg Response Time: " . round($summary['avg_response_time'] * 1000, 2) . "ms\n";
        $output .= "Memory Usage: {$summary['current_memory_usage']}\n";
        $output .= "Alerts: {$summary['total_alerts']}\n\n";
        
        // Recent Alerts
        if (!empty($report['alerts'])) {
            $output .= "Recent Alerts:\n";
            $output .= str_repeat("-", 20) . "\n";
            
            $recentAlerts = array_slice($report['alerts'], -5);
            foreach ($recentAlerts as $alert) {
                $output .= "[" . date('H:i:s', $alert['timestamp']) . "] {$alert['type']}: {$alert['message']}\n";
            }
            $output .= "\n";
        }
        
        // Performance Metrics
        $metrics = $report['metrics'];
        
        if (!empty($metrics['timers'])) {
            $output .= "Performance Metrics:\n";
            $output .= str_repeat("-", 25) . "\n";
            
            foreach ($metrics['timers'] as $name => $timers) {
                if (!empty($timers)) {
                    $stats = $this->monitor->metrics->getTimerStats($name);
                    $output .= "$name:\n";
                    $output .= "  Count: {$stats['count']}\n";
                    $output .= "  Avg: " . round($stats['mean'] * 1000, 2) . "ms\n";
                    $output .= "  Min: " . round($stats['min'] * 1000, 2) . "ms\n";
                    $output .= "  Max: " . round($stats['max'] * 1000, 2) . "ms\n";
                    $output .= "  P95: " . round($stats['p95'] * 1000, 2) . "ms\n";
                    $output .= "  P99: " . round($stats['p99'] * 1000, 2) . "ms\n\n";
                }
            }
        }
        
        // Counters
        if (!empty($metrics['counters'])) {
            $output .= "Counters:\n";
            $output .= str_repeat("-", 15) . "\n";
            
            foreach ($metrics['counters'] as $name => $counter) {
                $output .= "$name: {$counter['value']}\n";
            }
            $output .= "\n";
        }
        
        // Gauges
        if (!empty($metrics['gauges'])) {
            $output .= "Gauges:\n";
            $output .= str_repeat("-", 12) . "\n";
            
            foreach ($metrics['gauges'] as $name => $gauge) {
                $value = is_numeric($gauge['value']) ? round($gauge['value'], 2) : $gauge['value'];
                $output .= "$name: $value\n";
            }
            $output .= "\n";
        }
        
        return $output;
    }
    
    public function generateHTMLReport(): string {
        $report = $this->monitor->getReport();
        $summary = $report['summary'];
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Performance Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f0f0f0; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .metric { background: #f9f9f9; padding: 15px; border-radius: 5px; text-align: center; }
        .metric-value { font-size: 2em; font-weight: bold; color: #333; }
        .metric-label { color: #666; margin-top: 5px; }
        .alert { background: #ffebee; border-left: 4px solid #f44336; padding: 10px; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { border-bottom: 2px solid #333; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Performance Dashboard</h1>
        <p>Generated: ' . date('Y-m-d H:i:s', $report['timestamp']) . '</p>
    </div>
    
    <div class="section">
        <h2>Summary</h2>
        <div class="summary">
            <div class="metric">
                <div class="metric-value">' . $summary['total_requests'] . '</div>
                <div class="metric-label">Total Requests</div>
            </div>
            <div class="metric">
                <div class="metric-value">' . $summary['error_rate'] . '%</div>
                <div class="metric-label">Error Rate</div>
            </div>
            <div class="metric">
                <div class="metric-value">' . $summary['cache_hit_rate'] . '%</div>
                <div class="metric-label">Cache Hit Rate</div>
            </div>
            <div class="metric">
                <div class="metric-value">' . round($summary['avg_response_time'] * 1000, 2) . 'ms</div>
                <div class="metric-label">Avg Response Time</div>
            </div>
            <div class="metric">
                <div class="metric-value">' . $summary['current_memory_usage'] . '</div>
                <div class="metric-label">Memory Usage</div>
            </div>
            <div class="metric">
                <div class="metric-value">' . $summary['total_alerts'] . '</div>
                <div class="metric-label">Alerts</div>
            </div>
        </div>
    </div>';
        
        // Alerts section
        if (!empty($report['alerts'])) {
            $html .= '<div class="section">
                <h2>Recent Alerts</h2>';
            
            $recentAlerts = array_slice($report['alerts'], -10);
            foreach ($recentAlerts as $alert) {
                $html .= '<div class="alert">
                    <strong>' . htmlspecialchars($alert['type']) . '</strong>: ' . 
                    htmlspecialchars($alert['message']) . 
                    ' <small>(' . date('H:i:s', $alert['timestamp']) . ')</small>
                </div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        
        return $html;
    }
}

// Performance Monitoring Examples
class PerformanceMonitoringExamples {
    private ApplicationPerformanceMonitor $monitor;
    private PerformanceDashboard $dashboard;
    
    public function __construct() {
        $this->monitor = new ApplicationPerformanceMonitor();
        $this->dashboard = new PerformanceDashboard($this->monitor);
    }
    
    public function simulateApplication(): void {
        echo "Simulating Application Performance\n";
        echo str_repeat("-", 40) . "\n";
        
        // Simulate multiple requests
        for ($i = 1; $i <= 20; $i++) {
            $requestId = "req_$i";
            $this->monitor->startRequest($requestId);
            
            // Simulate request processing
            usleep(rand(10000, 100000)); // 10-100ms
            
            // Simulate database queries
            $this->monitor->recordDatabaseQuery("SELECT * FROM users WHERE id = ?", rand(1, 50) / 1000, rand(1, 10));
            
            // Simulate cache operations
            $this->monitor->recordCacheOperation('get', rand(0, 1) === 1);
            $this->monitor->recordCacheOperation('set', true);
            
            // Simulate occasional errors
            $success = rand(1, 20) !== 1; // 5% error rate
            if (!$success) {
                $this->monitor->recordError('database', 'Connection timeout');
            }
            
            $this->monitor->endRequest($requestId, $success);
            
            // Record memory usage periodically
            if ($i % 5 === 0) {
                $this->monitor->recordMemoryUsage();
            }
        }
        
        // Generate report
        echo $this->dashboard->generateReport();
    }
    
    public function demonstrateRealTimeMonitoring(): void {
        echo "\nReal-Time Monitoring Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Set custom thresholds
        $this->monitor->setThresholds([
            'response_time' => 0.05, // 50ms
            'memory_usage' => 50 * 1024 * 1024 // 50MB
        ]);
        
        // Simulate real-time monitoring
        for ($i = 1; $i <= 10; $i++) {
            $requestId = "realtime_$i";
            $this->monitor->startRequest($requestId);
            
            // Simulate varying response times
            $responseTime = rand(10000, 200000); // 10-200ms
            usleep($responseTime);
            
            $this->monitor->endRequest($requestId, true);
            
            // Show current status
            $report = $this->monitor->getReport();
            $summary = $report['summary'];
            
            echo "Request $i: " . round($responseTime / 1000, 2) . "ms | ";
            echo "Total: {$summary['total_requests']} | ";
            echo "Errors: {$summary['error_rate']}% | ";
            echo "Alerts: {$summary['total_alerts']}\n";
            
            // Small delay to simulate real-time
            usleep(100000); // 100ms
        }
        
        echo "\nFinal Report:\n";
        echo $this->dashboard->generateReport();
    }
    
    public function demonstrateAlertSystem(): void {
        echo "\nAlert System Demonstration\n";
        echo str_repeat("-", 35) . "\n";
        
        // Trigger various alerts
        $this->monitor->setThresholds([
            'response_time' => 0.01, // 10ms (very low for demo)
            'memory_usage' => 1 * 1024 * 1024 // 1MB (very low for demo)
        ]);
        
        // Trigger slow request alert
        $this->monitor->startRequest('slow_request');
        usleep(50000); // 50ms
        $this->monitor->endRequest('slow_request', true);
        
        // Trigger high memory alert
        $this->monitor->recordMemoryUsage();
        
        // Trigger error alert
        $this->monitor->recordError('validation', 'Invalid input data');
        
        // Show alerts
        $alerts = $this->monitor->getAlerts();
        echo "Generated " . count($alerts) . " alerts:\n";
        
        foreach ($alerts as $alert) {
            echo "  [{$alert['type']}] {$alert['message']}\n";
        }
        
        // Clear alerts
        $this->monitor->clearAlerts();
        echo "\nAlerts cleared. Current count: " . count($this->monitor->getAlerts()) . "\n";
    }
    
    public function runAllExamples(): void {
        echo "Performance Monitoring Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->simulateApplication();
        $this->demonstrateRealTimeMonitoring();
        $this->demonstrateAlertSystem();
    }
}

// Performance Monitoring Best Practices
function printPerformanceMonitoringBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Performance Monitoring Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Metrics Collection:\n";
    echo "   • Track response times\n";
    echo "   • Monitor error rates\n";
    echo "   • Measure resource usage\n";
    echo "   • Track business metrics\n\n";
    
    echo "2. Alerting:\n";
    echo "   • Set appropriate thresholds\n";
    echo "   • Avoid alert fatigue\n";
    echo "   • Use multi-level alerts\n";
    echo "   • Include actionable information\n\n";
    
    echo "3. Real-Time Monitoring:\n";
    echo "   • Use efficient data structures\n";
    echo "   • Implement sampling for high traffic\n";
    echo "   • Use time-series databases\n";
    echo "   • Implement dashboards\n\n";
    
    echo "4. Historical Analysis:\n";
    echo "   • Store performance data\n";
    echo "   • Analyze trends over time\n";
    echo "   • Identify performance patterns\n";
    echo "   • Correlate with events\n\n";
    
    echo "5. Integration:\n";
    echo "   • Integrate with APM tools\n";
    echo "   • Use standard metrics formats\n";
    echo "   • Implement health checks\n";
    echo "   • Connect to monitoring systems\n\n";
    
    echo "6. Performance Optimization:\n";
    echo "   • Monitor before optimizing\n";
    echo "   • Focus on bottlenecks\n";
    echo "   • Measure impact of changes\n";
    echo "   • Continuously monitor performance";
}

// Main execution
function runPerformanceMonitoringDemo(): void {
    $examples = new PerformanceMonitoringExamples();
    $examples->runAllExamples();
    printPerformanceMonitoringBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPerformanceMonitoringDemo();
}
?>
