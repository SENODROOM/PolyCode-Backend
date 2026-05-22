<?php
/**
 * Security Monitoring and Incident Response
 * 
 * This file demonstrates security event logging, monitoring,
 * and incident response techniques for PHP applications.
 */

// Security Event Logger
class SecurityEventLogger {
    private string $logFile;
    private array $eventTypes = [
        'LOGIN_SUCCESS',
        'LOGIN_FAILURE',
        'LOGOUT',
        'PASSWORD_CHANGE',
        'ACCOUNT_LOCKOUT',
        'MFA_ENABLED',
        'MFA_DISABLED',
        'MFA_FAILURE',
        'PERMISSION_DENIED',
        'DATA_ACCESS',
        'DATA_MODIFICATION',
        'SUSPICIOUS_ACTIVITY',
        'SECURITY_VIOLATION',
        'SYSTEM_ERROR',
        'API_ACCESS',
        'RATE_LIMIT_EXCEEDED'
    ];
    
    public function __construct(string $logFile = 'security_events.log') {
        $this->logFile = $logFile;
        $this->ensureLogFile();
    }
    
    private function ensureLogFile(): void {
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0600);
        }
    }
    
    public function logEvent(string $eventType, array $context = [], string $severity = 'INFO'): void {
        if (!in_array($eventType, $this->eventTypes)) {
            $eventType = 'UNKNOWN_EVENT';
        }
        
        $event = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $eventType,
            'severity' => $severity,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'session_id' => session_id() ?? 'none',
            'user_id' => $context['user_id'] ?? null,
            'context' => $context
        ];
        
        $logEntry = json_encode($event) . "\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Rotate log if too large
        $this->rotateLogIfNeeded();
        
        // Alert for critical events
        if ($severity === 'CRITICAL') {
            $this->sendAlert($event);
        }
    }
    
    public function logLoginAttempt(string $username, bool $success, string $ip): void {
        $eventType = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILURE';
        $severity = $success ? 'INFO' : 'WARNING';
        
        $this->logEvent($eventType, [
            'username' => $username,
            'success' => $success,
            'ip' => $ip
        ], $severity);
    }
    
    public function logSuspiciousActivity(string $description, array $context = []): void {
        $this->logEvent('SUSPICIOUS_ACTIVITY', array_merge([
            'description' => $description
        ], $context), 'HIGH');
    }
    
    public function logSecurityViolation(string $violation, array $context = []): void {
        $this->logEvent('SECURITY_VIOLATION', array_merge([
            'violation' => $violation
        ], $context), 'CRITICAL');
    }
    
    public function logDataAccess(string $resource, string $action, int $userId = null): void {
        $this->logEvent('DATA_ACCESS', [
            'resource' => $resource,
            'action' => $action,
            'user_id' => $userId
        ], 'INFO');
    }
    
    public function getRecentEvents(int $limit = 100): array {
        $events = [];
        $lines = array_slice(file($this->logFile, FILE_IGNORE_NEW_LINES), -$limit);
        
        foreach ($lines as $line) {
            $event = json_decode($line, true);
            if ($event !== null) {
                $events[] = $event;
            }
        }
        
        return $events;
    }
    
    public function getEventsByType(string $eventType, int $limit = 50): array {
        $allEvents = $this->getRecentEvents(1000);
        
        return array_filter($allEvents, function($event) use ($eventType) {
            return $event['event_type'] === $eventType;
        });
    }
    
    public function getEventsBySeverity(string $severity, int $limit = 50): array {
        $allEvents = $this->getRecentEvents(1000);
        
        return array_filter($allEvents, function($event) use ($severity) {
            return $event['severity'] === $severity;
        });
    }
    
    public function getEventsByTimeRange(string $startTime, string $endTime): array {
        $allEvents = $this->getRecentEvents(10000);
        
        return array_filter($allEvents, function($event) use ($startTime, $endTime) {
            return $event['timestamp'] >= $startTime && $event['timestamp'] <= $endTime;
        });
    }
    
    private function rotateLogIfNeeded(): void {
        if (filesize($this->logFile) > 10 * 1024 * 1024) { // 10MB
            $backupFile = $this->logFile . '.' . date('Y-m-d-H-i-s');
            rename($this->logFile, $backupFile);
            $this->ensureLogFile();
        }
    }
    
    private function sendAlert(array $event): void {
        // In a real implementation, this would send email, SMS, or push notifications
        error_log("CRITICAL SECURITY EVENT: " . json_encode($event));
    }
    
    public function generateReport(string $startDate, string $endDate): array {
        $events = $this->getEventsByTimeRange($startDate, $endDate);
        
        $report = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_events' => count($events),
            'events_by_type' => [],
            'events_by_severity' => [],
            'top_ips' => [],
            'top_users' => [],
            'critical_events' => []
        ];
        
        // Group by type
        foreach ($events as $event) {
            $type = $event['event_type'];
            $report['events_by_type'][$type] = ($report['events_by_type'][$type] ?? 0) + 1;
            
            $severity = $event['severity'];
            $report['events_by_severity'][$severity] = ($report['events_by_severity'][$severity] ?? 0) + 1;
            
            // Track top IPs
            $ip = $event['ip_address'];
            $report['top_ips'][$ip] = ($report['top_ips'][$ip] ?? 0) + 1;
            
            // Track top users
            if ($event['user_id']) {
                $userId = $event['user_id'];
                $report['top_users'][$userId] = ($report['top_users'][$userId] ?? 0) + 1;
            }
            
            // Collect critical events
            if ($severity === 'CRITICAL') {
                $report['critical_events'][] = $event;
            }
        }
        
        // Sort by frequency
        arsort($report['top_ips']);
        arsort($report['top_users']);
        
        return $report;
    }
}

// Intrusion Detection System
class IntrusionDetectionSystem {
    private SecurityEventLogger $logger;
    private array $rules = [];
    private array $alerts = [];
    
    public function __construct(SecurityEventLogger $logger) {
        $this->logger = $logger;
        $this->initializeRules();
    }
    
    private function initializeRules(): void {
        $this->rules = [
            'multiple_failed_logins' => [
                'condition' => 'count(LOGIN_FAILURE, 300) > 5',
                'timeframe' => 300, // 5 minutes
                'threshold' => 5,
                'severity' => 'HIGH',
                'action' => 'lock_account'
            ],
            'brute_force_attack' => [
                'condition' => 'count(LOGIN_FAILURE, 60) > 20',
                'timeframe' => 60, // 1 minute
                'threshold' => 20,
                'severity' => 'CRITICAL',
                'action' => 'block_ip'
            ],
            'suspicious_data_access' => [
                'condition' => 'count(DATA_ACCESS, 60) > 100',
                'timeframe' => 60, // 1 minute
                'threshold' => 100,
                'severity' => 'HIGH',
                'action' => 'alert_admin'
            ],
            'privilege_escalation' => [
                'condition' => 'PERMISSION_DENIED > 10',
                'timeframe' => 300, // 5 minutes
                'threshold' => 10,
                'severity' => 'CRITICAL',
                'action' => 'lock_account'
            ],
            'unusual_login_time' => [
                'condition' => 'LOGIN_SUCCESS AND hour NOT IN (9,10,11,12,13,14,15,16,17)',
                'timeframe' => 86400, // 24 hours
                'severity' => 'MEDIUM',
                'action' => 'alert_user'
            ]
        ];
    }
    
    public function addRule(string $name, array $rule): void {
        $this->rules[$name] = $rule;
    }
    
    public function analyzeEvents(): array {
        $events = $this->logger->getRecentEvents(1000);
        $alerts = [];
        
        foreach ($this->rules as $ruleName => $rule) {
            $violations = $this->evaluateRule($rule, $events);
            
            if (!empty($violations)) {
                $alert = [
                    'rule' => $ruleName,
                    'severity' => $rule['severity'],
                    'action' => $rule['action'],
                    'violations' => $violations,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                
                $alerts[] = $alert;
                $this->alerts[] = $alert;
                
                // Log the alert
                $this->logger->logEvent('INTRUSION_DETECTED', [
                    'rule' => $ruleName,
                    'violations' => count($violations),
                    'action' => $rule['action']
                ], $rule['severity']);
            }
        }
        
        return $alerts;
    }
    
    private function evaluateRule(array $rule, array $events): array {
        $violations = [];
        $timeframe = $rule['timeframe'];
        $threshold = $rule['threshold'];
        
        // Group events by IP and user for analysis
        $eventsByIP = [];
        $eventsByUser = [];
        
        foreach ($events as $event) {
            $ip = $event['ip_address'];
            $userId = $event['user_id'];
            
            if (!isset($eventsByIP[$ip])) {
                $eventsByIP[$ip] = [];
            }
            $eventsByIP[$ip][] = $event;
            
            if ($userId) {
                if (!isset($eventsByUser[$userId])) {
                    $eventsByUser[$userId] = [];
                }
                $eventsByUser[$userId][] = $event;
            }
        }
        
        // Evaluate each rule condition
        switch ($ruleName) {
            case 'multiple_failed_logins':
                foreach ($eventsByIP as $ip => $ipEvents) {
                    $failedLogins = array_filter($ipEvents, fn($e) => $e['event_type'] === 'LOGIN_FAILURE');
                    if (count($failedLogins) > $threshold) {
                        $violations[] = ['ip' => $ip, 'count' => count($failedLogins)];
                    }
                }
                break;
                
            case 'brute_force_attack':
                foreach ($eventsByIP as $ip => $ipEvents) {
                    $recentEvents = array_filter($ipEvents, fn($e) => 
                        strtotime($e['timestamp']) > time() - $timeframe
                    );
                    $failedLogins = array_filter($recentEvents, fn($e) => $e['event_type'] === 'LOGIN_FAILURE');
                    if (count($failedLogins) > $threshold) {
                        $violations[] = ['ip' => $ip, 'count' => count($failedLogins)];
                    }
                }
                break;
                
            case 'suspicious_data_access':
                foreach ($eventsByUser as $userId => $userEvents) {
                    $recentEvents = array_filter($userEvents, fn($e) => 
                        strtotime($e['timestamp']) > time() - $timeframe
                    );
                    $dataAccess = array_filter($recentEvents, fn($e) => $e['event_type'] === 'DATA_ACCESS');
                    if (count($dataAccess) > $threshold) {
                        $violations[] = ['user_id' => $userId, 'count' => count($dataAccess)];
                    }
                }
                break;
                
            case 'privilege_escalation':
                foreach ($eventsByUser as $userId => $userEvents) {
                    $deniedAccess = array_filter($userEvents, fn($e) => $e['event_type'] === 'PERMISSION_DENIED');
                    if (count($deniedAccess) > $threshold) {
                        $violations[] = ['user_id' => $userId, 'count' => count($deniedAccess)];
                    }
                }
                break;
                
            case 'unusual_login_time':
                foreach ($events as $event) {
                    if ($event['event_type'] === 'LOGIN_SUCCESS') {
                        $hour = (int)date('H', strtotime($event['timestamp']));
                        if ($hour < 9 || $hour > 17) {
                            $violations[] = ['user_id' => $event['user_id'], 'hour' => $hour];
                        }
                    }
                }
                break;
        }
        
        return $violations;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function clearAlerts(): void {
        $this->alerts = [];
    }
    
    public function getStatistics(): array {
        $events = $this->logger->getRecentEvents(1000);
        
        $stats = [
            'total_events' => count($events),
            'events_by_type' => [],
            'events_by_severity' => [],
            'unique_ips' => 0,
            'unique_users' => 0,
            'alerts_triggered' => count($this->alerts)
        ];
        
        $ips = [];
        $users = [];
        
        foreach ($events as $event) {
            $type = $event['event_type'];
            $severity = $event['severity'];
            
            $stats['events_by_type'][$type] = ($stats['events_by_type'][$type] ?? 0) + 1;
            $stats['events_by_severity'][$severity] = ($stats['events_by_severity'][$severity] ?? 0) + 1;
            
            $ips[] = $event['ip_address'];
            if ($event['user_id']) {
                $users[] = $event['user_id'];
            }
        }
        
        $stats['unique_ips'] = count(array_unique($ips));
        $stats['unique_users'] = count(array_unique($users));
        
        return $stats;
    }
}

// Incident Response System
class IncidentResponseSystem {
    private SecurityEventLogger $logger;
    private IntrusionDetectionSystem $ids;
    private array $incidents = [];
    private array $responseActions = [];
    
    public function __construct(SecurityEventLogger $logger, IntrusionDetectionSystem $ids) {
        $this->logger = $logger;
        $this->ids = $ids;
        $this->initializeResponseActions();
    }
    
    private function initializeResponseActions(): void {
        $this->responseActions = [
            'lock_account' => [
                'description' => 'Lock user account',
                'automated' => true,
                'severity' => 'HIGH'
            ],
            'block_ip' => [
                'description' => 'Block IP address',
                'automated' => true,
                'severity' => 'CRITICAL'
            ],
            'alert_admin' => [
                'description' => 'Alert administrator',
                'automated' => true,
                'severity' => 'MEDIUM'
            ],
            'alert_user' => [
                'description' => 'Alert user',
                'automated' => true,
                'severity' => 'LOW'
            ],
            'force_logout' => [
                'description' => 'Force logout all sessions',
                'automated' => true,
                'severity' => 'HIGH'
            ],
            'investigate' => [
                'description' => 'Manual investigation required',
                'automated' => false,
                'severity' => 'HIGH'
            ]
        ];
    }
    
    public function createIncident(array $alert): string {
        $incidentId = uniqid('INC_', true);
        
        $incident = [
            'id' => $incidentId,
            'alert' => $alert,
            'status' => 'OPEN',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'assigned_to' => null,
            'actions_taken' => [],
            'notes' => [],
            'severity' => $alert['severity']
        ];
        
        $this->incidents[$incidentId] = $incident;
        
        // Log incident creation
        $this->logger->logEvent('INCIDENT_CREATED', [
            'incident_id' => $incidentId,
            'rule' => $alert['rule'],
            'severity' => $alert['severity']
        ], $alert['severity']);
        
        return $incidentId;
    }
    
    public function executeResponse(string $incidentId, string $action, array $context = []): bool {
        if (!isset($this->incidents[$incidentId])) {
            return false;
        }
        
        $incident = &$this->incidents[$incidentId];
        
        if (!isset($this->responseActions[$action])) {
            return false;
        }
        
        $responseAction = $this->responseActions[$action];
        
        // Execute the action
        $success = $this->performAction($action, $context);
        
        if ($success) {
            $incident['actions_taken'][] = [
                'action' => $action,
                'context' => $context,
                'performed_at' => date('Y-m-d H:i:s'),
                'performed_by' => 'system'
            ];
            
            $incident['updated_at'] = date('Y-m-d H:i:s');
            
            // Log action
            $this->logger->logEvent('RESPONSE_ACTION', [
                'incident_id' => $incidentId,
                'action' => $action,
                'context' => $context,
                'success' => $success
            ], $responseAction['severity']);
        }
        
        return $success;
    }
    
    private function performAction(string $action, array $context): bool {
        switch ($action) {
            case 'lock_account':
                // In a real implementation, this would lock the user account
                return true;
                
            case 'block_ip':
                // In a real implementation, this would block the IP in firewall
                return true;
                
            case 'alert_admin':
                // In a real implementation, this would send email/SMS to admin
                error_log("SECURITY ALERT: " . json_encode($context));
                return true;
                
            case 'alert_user':
                // In a real implementation, this would send notification to user
                return true;
                
            case 'force_logout':
                // In a real implementation, this would invalidate all user sessions
                return true;
                
            case 'investigate':
                // Manual action - just log for now
                return true;
                
            default:
                return false;
        }
    }
    
    public function updateIncidentStatus(string $incidentId, string $status, string $note = null): bool {
        if (!isset($this->incidents[$incidentId])) {
            return false;
        }
        
        $validStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->incidents[$incidentId]['status'] = $status;
        $this->incidents[$incidentId]['updated_at'] = date('Y-m-d H:i:s');
        
        if ($note) {
            $this->incidents[$incidentId]['notes'][] = [
                'note' => $note,
                'added_at' => date('Y-m-d H:i:s'),
                'added_by' => 'system'
            ];
        }
        
        return true;
    }
    
    public function assignIncident(string $incidentId, string $assignedTo): bool {
        if (!isset($this->incidents[$incidentId])) {
            return false;
        }
        
        $this->incidents[$incidentId]['assigned_to'] = $assignedTo;
        $this->incidents[$incidentId]['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    public function getIncident(string $incidentId): ?array {
        return $this->incidents[$incidentId] ?? null;
    }
    
    public function getIncidents(array $filters = []): array {
        $incidents = $this->incidents;
        
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $incidents = array_filter($incidents, function($incident) use ($key, $value) {
                    return ($incident[$key] ?? null) === $value;
                });
            }
        }
        
        return $incidents;
    }
    
    public function getIncidentsByStatus(string $status): array {
        return $this->getIncidents(['status' => $status]);
    }
    
    public function getIncidentsBySeverity(string $severity): array {
        return $this->getIncidents(['severity' => $severity]);
    }
    
    public function getActiveIncidents(): array {
        return $this->getIncidents(['status' => 'OPEN']);
    }
    
    public function generateIncidentReport(): array {
        $incidents = $this->incidents;
        
        $report = [
            'total_incidents' => count($incidents),
            'incidents_by_status' => [],
            'incidents_by_severity' => [],
            'incidents_by_action' => [],
            'average_resolution_time' => 0,
            'open_incidents' => 0,
            'resolved_incidents' => 0
        ];
        
        $resolutionTimes = [];
        
        foreach ($incidents as $incident) {
            $status = $incident['status'];
            $severity = $incident['severity'];
            
            $report['incidents_by_status'][$status] = ($report['incidents_by_status'][$status] ?? 0) + 1;
            $report['incidents_by_severity'][$severity] = ($report['incidents_by_severity'][$severity] ?? 0) + 1;
            
            if ($status === 'OPEN') {
                $report['open_incidents']++;
            } elseif ($status === 'CLOSED') {
                $report['resolved_incidents']++;
                
                // Calculate resolution time
                $created = strtotime($incident['created_at']);
                $updated = strtotime($incident['updated_at']);
                $resolutionTimes[] = $updated - $created;
            }
            
            // Count actions
            foreach ($incident['actions_taken'] as $action) {
                $actionName = $action['action'];
                $report['incidents_by_action'][$actionName] = ($report['incidents_by_action'][$actionName] ?? 0) + 1;
            }
        }
        
        if (!empty($resolutionTimes)) {
            $report['average_resolution_time'] = array_sum($resolutionTimes) / count($resolutionTimes);
        }
        
        return $report;
    }
    
    public function autoRespond(): array {
        $alerts = $this->ids->analyzeEvents();
        $processedIncidents = [];
        
        foreach ($alerts as $alert) {
            $incidentId = $this->createIncident($alert);
            
            // Execute automated response actions
            if (isset($alert['action'])) {
                $this->executeResponse($incidentId, $alert['action'], $alert['violations']);
            }
            
            // Update status based on severity
            if ($alert['severity'] === 'CRITICAL') {
                $this->updateIncidentStatus($incidentId, 'IN_PROGRESS', 'Critical incident - immediate attention required');
            }
            
            $processedIncidents[] = $incidentId;
        }
        
        return $processedIncidents;
    }
}

// Security Monitoring Examples
class SecurityMonitoringExamples {
    private SecurityEventLogger $logger;
    private IntrusionDetectionSystem $ids;
    private IncidentResponseSystem $irs;
    
    public function __construct() {
        $this->logger = new SecurityEventLogger();
        $this->ids = new IntrusionDetectionSystem($this->logger);
        $this->irs = new IncidentResponseSystem($this->logger, $this->ids);
    }
    
    public function demonstrateEventLogging(): void {
        echo "Security Event Logging Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Simulate various security events
        $this->logger->logLoginAttempt('admin', true, '192.168.1.100');
        $this->logger->logLoginAttempt('admin', false, '192.168.1.101');
        $this->logger->logLoginAttempt('john', false, '192.168.1.101');
        $this->logger->logLoginAttempt('john', false, '192.168.1.101');
        $this->logger->logLoginAttempt('john', false, '192.168.1.101');
        $this->logger->logLoginAttempt('jane', true, '192.168.1.102');
        
        $this->logger->logDataAccess('user_profile', 'read', 123);
        $this->logger->logDataAccess('admin_panel', 'access_denied', 456);
        $this->logger->logDataAccess('sensitive_data', 'read', 123);
        
        $this->logger->logSuspiciousActivity('Multiple failed login attempts', [
            'ip' => '192.168.1.101',
            'attempts' => 3
        ]);
        
        $this->logger->logSecurityViolation('SQL injection attempt detected', [
            'query' => 'SELECT * FROM users WHERE id = 1 OR 1=1',
            'ip' => '192.168.1.103'
        ]);
        
        echo "Security events logged successfully\n";
        
        // Show recent events
        $recentEvents = $this->logger->getRecentEvents(10);
        echo "\nRecent security events:\n";
        
        foreach ($recentEvents as $event) {
            echo "  [{$event['timestamp']}] {$event['event_type']} - {$event['severity']} - IP: {$event['ip_address']}\n";
        }
    }
    
    public function demonstrateIntrusionDetection(): void {
        echo "\nIntrusion Detection Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Analyze events for intrusions
        $alerts = $this->ids->analyzeEvents();
        
        echo "Intrusion detection analysis complete\n";
        echo "Alerts triggered: " . count($alerts) . "\n\n";
        
        foreach ($alerts as $alert) {
            echo "Alert: {$alert['rule']}\n";
            echo "Severity: {$alert['severity']}\n";
            echo "Action: {$alert['action']}\n";
            echo "Violations: " . count($alert['violations']) . "\n";
            
            foreach ($alert['violations'] as $violation) {
                echo "  - " . json_encode($violation) . "\n";
            }
            echo "\n";
        }
        
        // Show IDS statistics
        $stats = $this->ids->getStatistics();
        echo "IDS Statistics:\n";
        echo "Total events: {$stats['total_events']}\n";
        echo "Unique IPs: {$stats['unique_ips']}\n";
        echo "Unique users: {$stats['unique_users']}\n";
        echo "Alerts triggered: {$stats['alerts_triggered']}\n";
    }
    
    public function demonstrateIncidentResponse(): void {
        echo "Incident Response Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Auto-respond to alerts
        $incidentIds = $this->irs->autoRespond();
        
        echo "Auto-response completed\n";
        echo "Incidents created: " . count($incidentIds) . "\n\n";
        
        // Show incident details
        foreach ($incidentIds as $incidentId) {
            $incident = $this->irs->getIncident($incidentId);
            
            echo "Incident: $incidentId\n";
            echo "Status: {$incident['status']}\n";
            echo "Severity: {$incident['severity']}\n";
            echo "Actions taken: " . count($incident['actions_taken']) . "\n";
            
            foreach ($incident['actions_taken'] as $action) {
                echo "  - {$action['action']} at {$action['performed_at']}\n";
            }
            echo "\n";
        }
        
        // Manual incident management
        if (!empty($incidentIds)) {
            $firstIncidentId = $incidentIds[0];
            
            // Add note to incident
            $this->irs->updateIncidentStatus($firstIncidentId, 'IN_PROGRESS', 'Investigating potential security breach');
            
            // Assign to security team
            $this->irs->assignIncident($firstIncidentId, 'security_team');
            
            echo "Manual incident management:\n";
            echo "Updated status to IN_PROGRESS\n";
            echo "Assigned to security_team\n";
            
            $updatedIncident = $this->irs->getIncident($firstIncidentId);
            echo "Current status: {$updatedIncident['status']}\n";
            echo "Assigned to: {$updatedIncident['assigned_to']}\n";
        }
        
        // Generate incident report
        $report = $this->irs->generateIncidentReport();
        echo "\nIncident Report:\n";
        echo "Total incidents: {$report['total_incidents']}\n";
        echo "Open incidents: {$report['open_incidents']}\n";
        echo "Resolved incidents: {$report['resolved_incidents']}\n";
        echo "Average resolution time: " . round($report['average_resolution_time'], 2) . " seconds\n";
    }
    
    public function demonstrateComprehensiveMonitoring(): void {
        echo "\nComprehensive Security Monitoring\n";
        echo str_repeat("-", 40) . "\n";
        
        // Simulate a security incident scenario
        echo "Scenario: Simulated security breach attempt\n";
        echo "1. Attacker attempts multiple logins\n";
        
        // Simulate attack
        for ($i = 1; $i <= 25; $i++) {
            $this->logger->logLoginAttempt('admin', false, '192.168.1.200');
        }
        
        echo "2. Attacker tries to access sensitive data\n";
        $this->logger->logDataAccess('admin_panel', 'access_denied', 999);
        $this->logger->logDataAccess('user_database', 'access_denied', 999);
        
        echo "3. Security violation detected\n";
        $this->logger->logSecurityViolation('Privilege escalation attempt', [
            'user_id' => 999,
            'target_resource' => 'admin_panel',
            'ip' => '192.168.1.200'
        ]);
        
        echo "\nRunning security analysis...\n";
        
        // Run intrusion detection
        $alerts = $this->ids->analyzeEvents();
        echo "Alerts detected: " . count($alerts) . "\n";
        
        // Auto-respond
        $incidentIds = $this->irs->autoRespond();
        echo "Incidents created: " . count($incidentIds) . "\n";
        
        // Show comprehensive report
        echo "\nComprehensive Security Report:\n";
        echo str_repeat("-", 35) . "\n";
        
        // Event statistics
        $stats = $this->ids->getStatistics();
        echo "Event Statistics:\n";
        echo "  Total events: {$stats['total_events']}\n";
        echo "  Unique IPs: {$stats['unique_ips']}\n";
        echo "  Events by type:\n";
        foreach ($stats['events_by_type'] as $type => $count) {
            echo "    $type: $count\n";
        }
        
        // Incident statistics
        $incidentReport = $this->irs->generateIncidentReport();
        echo "\nIncident Statistics:\n";
        echo "  Total incidents: {$incidentReport['total_incidents']}\n";
        echo "  Open incidents: {$incidentReport['open_incidents']}\n";
        echo "  Incidents by severity:\n";
        foreach ($incidentReport['incidents_by_severity'] as $severity => $count) {
            echo "    $severity: $count\n";
        }
        
        // Recent critical events
        $criticalEvents = $this->logger->getEventsBySeverity('CRITICAL', 5);
        echo "\nRecent Critical Events:\n";
        foreach ($criticalEvents as $event) {
            echo "  [{$event['timestamp']}] {$event['event_type']} - {$event['context']['violation'] ?? 'N/A'}\n";
        }
    }
    
    public function runAllExamples(): void {
        echo "Security Monitoring Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateEventLogging();
        $this->demonstrateIntrusionDetection();
        $this->demonstrateIncidentResponse();
        $this->demonstrateComprehensiveMonitoring();
        
        // Cleanup
        $this->cleanup();
    }
    
    private function cleanup(): void {
        // Clean up log files
        $logFile = 'security_events.log';
        if (file_exists($logFile)) {
            unlink($logFile);
        }
    }
}

// Security Monitoring Best Practices
function printSecurityMonitoringBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Security Monitoring Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Event Logging:\n";
    echo "   • Log all security-relevant events\n";
    echo "   • Use structured logging formats\n";
    echo "   • Include context and metadata\n";
    echo "   • Implement log rotation\n";
    echo "   • Secure log storage and access\n\n";
    
    echo "2. Intrusion Detection:\n";
    echo "   • Implement multiple detection rules\n";
    echo "   • Use behavioral analysis\n";
    echo "   • Monitor for anomalies\n";
    echo "   • Set appropriate thresholds\n";
    echo "   • Regularly update detection rules\n\n";
    
    echo "3. Incident Response:\n";
    echo "   • Have predefined response procedures\n";
    echo "   • Implement automated responses\n";
    echo "   • Maintain incident tracking\n";
    echo "   • Escalate critical incidents\n";
    echo "   • Conduct post-incident reviews\n\n";
    
    echo "4. Alerting:\n";
    echo "   • Use multiple alert channels\n";
    echo "   • Implement alert prioritization\n";
    echo "   • Avoid alert fatigue\n";
    echo "   • Provide actionable alerts\n";
    echo "   • Test alert systems regularly\n\n";
    
    echo "5. Monitoring:\n";
    echo "   • Real-time monitoring\n";
    echo "   • Dashboard and visualization\n";
    echo "   • Regular security reviews\n";
    echo "   • Trend analysis\n";
    echo "   • Compliance reporting\n\n";
    
    echo "6. Integration:\n";
    echo "   • SIEM integration\n";
    echo "   • SOAR automation\n";
    echo "   • Threat intelligence feeds\n";
    echo "   • External monitoring services\n";
    echo "   • Cross-platform correlation";
}

// Main execution
function runSecurityMonitoringDemo(): void {
    $examples = new SecurityMonitoringExamples();
    $examples->runAllExamples();
    printSecurityMonitoringBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSecurityMonitoringDemo();
}
?>
