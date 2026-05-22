<?php
/**
 * Security Fundamentals
 * 
 * This file demonstrates common PHP security vulnerabilities
 * and best practices for secure coding.
 */

// Security Vulnerability Scanner
class SecurityScanner {
    private array $vulnerabilities = [];
    private array $patterns = [
        'sql_injection' => [
            'pattern' => '/\$\w+\s*=\s*["\'].*\$\w+.*["\']/',
            'description' => 'Potential SQL injection vulnerability',
            'severity' => 'HIGH'
        ],
        'xss' => [
            'pattern' => '/echo\s*\$\w+|print\s*\$\w+/',
            'description' => 'Potential XSS vulnerability (unescaped output)',
            'severity' => 'HIGH'
        ],
        'file_inclusion' => [
            'pattern' => '/include\s*\$\w+|require\s*\$\w+/',
            'description' => 'Potential file inclusion vulnerability',
            'severity' => 'HIGH'
        ],
        'eval_usage' => [
            'pattern' => '/eval\s*\(/',
            'description' => 'Use of eval() function',
            'severity' => 'HIGH'
        ],
        'weak_password' => [
            'pattern' => '/password\s*=\s*["\'][^"\']{1,6}["\']/',
            'description' => 'Weak password detected',
            'severity' => 'MEDIUM'
        ]
    ];
    
    public function scanFile(string $filename): array {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException("File not found: $filename");
        }
        
        $content = file_get_contents($filename);
        $this->vulnerabilities = [];
        
        foreach ($this->patterns as $type => $pattern) {
            if (preg_match_all($pattern['pattern'], $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $lineNumber = $this->getLineNumber($content, $match[1]);
                    
                    $this->vulnerabilities[] = [
                        'type' => $type,
                        'description' => $pattern['description'],
                        'severity' => $pattern['severity'],
                        'line' => $lineNumber,
                        'code' => $match[0],
                        'file' => basename($filename)
                    ];
                }
            }
        }
        
        return $this->vulnerabilities;
    }
    
    private function getLineNumber(string $content, int $position): int {
        $lines = explode("\n", substr($content, 0, $position));
        return count($lines);
    }
    
    public function getVulnerabilities(): array {
        return $this->vulnerabilities;
    }
    
    public function generateReport(): string {
        if (empty($this->vulnerabilities)) {
            return "No vulnerabilities found.\n";
        }
        
        $report = "Security Vulnerability Report\n";
        $report .= str_repeat("=", 40) . "\n\n";
        
        // Group by severity
        $grouped = [];
        foreach ($this->vulnerabilities as $vuln) {
            $grouped[$vuln['severity']][] = $vuln;
        }
        
        foreach (['HIGH', 'MEDIUM', 'LOW'] as $severity) {
            if (isset($grouped[$severity])) {
                $report .= "$severity Severity:\n";
                $report .= str_repeat("-", 20) . "\n";
                
                foreach ($grouped[$severity] as $vuln) {
                    $report .= "File: {$vuln['file']}\n";
                    $report .= "Line: {$vuln['line']}\n";
                    $report .= "Type: {$vuln['type']}\n";
                    $report .= "Description: {$vuln['description']}\n";
                    $report .= "Code: " . trim($vuln['code']) . "\n\n";
                }
            }
        }
        
        return $report;
    }
}

// Secure Input Handler
class SecureInputHandler {
    private array $allowedTags = [];
    private array $allowedAttributes = [];
    private bool $stripTags = true;
    
    public function __construct(array $allowedTags = [], array $allowedAttributes = []) {
        $this->allowedTags = $allowedTags;
        $this->allowedAttributes = $allowedAttributes;
    }
    
    public function sanitizeInput(string $input): string {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Check for extremely long input
        if (strlen($input) > 10000) {
            throw new InvalidArgumentException('Input too long');
        }
        
        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        return $input;
    }
    
    public function validateEmail(string $email): bool {
        $email = $this->sanitizeInput($email);
        
        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Additional checks
        if (strlen($email) > 254) {
            return false;
        }
        
        // Check for consecutive dots
        if (strpos($email, '..') !== false) {
            return false;
        }
        
        return true;
    }
    
    public function validateURL(string $url): bool {
        $url = $this->sanitizeInput($url);
        
        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        $parsed = parse_url($url);
        
        // Check for dangerous protocols
        $allowedSchemes = ['http', 'https'];
        if (!in_array(strtolower($parsed['scheme'] ?? ''), $allowedSchemes)) {
            return false;
        }
        
        return true;
    }
    
    public function sanitizeHTML(string $html): string {
        if ($this->stripTags) {
            return strip_tags($html, implode('', $this->allowedTags));
        }
        
        // More sophisticated HTML sanitization would go here
        // For demo purposes, we'll use strip_tags
        return strip_tags($html, implode('', $this->allowedTags));
    }
    
    public function escapeOutput(string $value, string $context = 'html'): string {
        switch ($context) {
            case 'html':
                return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            case 'js':
                return json_encode($value);
            case 'css':
                return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            case 'url':
                return urlencode($value);
            default:
                return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
}

// Secure Configuration Manager
class SecureConfig {
    private array $config = [];
    private string $configFile;
    
    public function __construct(string $configFile) {
        $this->configFile = $configFile;
        $this->loadConfig();
    }
    
    private function loadConfig(): void {
        if (!file_exists($this->configFile)) {
            $this->createDefaultConfig();
        }
        
        $content = file_get_contents($this->configFile);
        $this->config = unserialize($content);
        
        if ($this->config === false) {
            throw new RuntimeException('Failed to load configuration');
        }
    }
    
    private function createDefaultConfig(): void {
        $this->config = [
            'security' => [
                'session_timeout' => 3600,
                'max_login_attempts' => 5,
                'lockout_duration' => 900,
                'password_min_length' => 8,
                'require_special_chars' => true,
                'csrf_token_lifetime' => 3600
            ],
            'encryption' => [
                'algorithm' => 'AES-256-CBC',
                'key_derivation' => 'PBKDF2',
                'iterations' => 100000
            ],
            'logging' => [
                'log_level' => 'INFO',
                'log_security_events' => true,
                'max_log_size' => 10485760, // 10MB
                'log_retention_days' => 30
            ]
        ];
        
        $this->saveConfig();
    }
    
    public function get(string $key, mixed $default = null): mixed {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function set(string $key, mixed $value): void {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach (array_slice($keys, 0, -1) as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config[end($keys)] = $value;
        $this->saveConfig();
    }
    
    private function saveConfig(): void {
        $content = serialize($this->config);
        file_put_contents($this->configFile, $content);
        
        // Set secure permissions
        chmod($this->configFile, 0600);
    }
}

// Security Headers Manager
class SecurityHeaders {
    private array $headers = [];
    
    public function __construct() {
        $this->setDefaultHeaders();
    }
    
    private function setDefaultHeaders(): void {
        $this->headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()'
        ];
    }
    
    public function setHeader(string $name, string $value): void {
        $this->headers[$name] = $value;
    }
    
    public function removeHeader(string $name): void {
        unset($this->headers[$name]);
    }
    
    public function sendHeaders(): void {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }
    
    public function getHeaders(): array {
        return $this->headers;
    }
}

// Rate Limiter
class RateLimiter {
    private array $attempts = [];
    private int $maxAttempts;
    private int $windowSize;
    
    public function __construct(int $maxAttempts = 5, int $windowSize = 900) {
        $this->maxAttempts = $maxAttempts;
        $this->windowSize = $windowSize;
    }
    
    public function isAllowed(string $identifier): bool {
        $now = time();
        
        // Clean old attempts
        if (isset($this->attempts[$identifier])) {
            $this->attempts[$identifier] = array_filter(
                $this->attempts[$identifier],
                fn($time) => $time > $now - $this->windowSize
            );
        }
        
        // Check if limit exceeded
        if (isset($this->attempts[$identifier]) && count($this->attempts[$identifier]) >= $this->maxAttempts) {
            return false;
        }
        
        // Record attempt
        $this->attempts[$identifier][] = $now;
        
        return true;
    }
    
    public function getRemainingAttempts(string $identifier): int {
        if (!isset($this->attempts[$identifier])) {
            return $this->maxAttempts;
        }
        
        $now = time();
        $recentAttempts = array_filter(
            $this->attempts[$identifier],
            fn($time) => $time > $now - $this->windowSize
        );
        
        return max(0, $this->maxAttempts - count($recentAttempts));
    }
    
    public function getResetTime(string $identifier): int {
        if (!isset($this->attempts[$identifier]) || empty($this->attempts[$identifier])) {
            return 0;
        }
        
        $oldestAttempt = min($this->attempts[$identifier]);
        return $oldestAttempt + $this->windowSize;
    }
}

// Security Logger
class SecurityLogger {
    private string $logFile;
    private array $levels = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
    
    public function __construct(string $logFile = 'security.log') {
        $this->logFile = $logFile;
        $this->ensureLogFileExists();
    }
    
    private function ensureLogFileExists(): void {
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0600);
        }
    }
    
    public function log(string $level, string $message, array $context = []): void {
        if (!in_array(strtoupper($level), $this->levels)) {
            $level = 'INFO';
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        
        $logEntry = "[$timestamp] [$level] $message$contextStr\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Rotate log if too large
        $this->rotateLogIfNeeded();
    }
    
    public function logSecurityEvent(string $event, array $context = []): void {
        $this->log('WARNING', "SECURITY: $event", $context);
    }
    
    public function logLoginAttempt(string $username, bool $success, string $ip): void {
        $this->log(
            $success ? 'INFO' : 'WARNING',
            "Login attempt: $username - " . ($success ? 'SUCCESS' : 'FAILED'),
            ['ip' => $ip, 'username' => $username]
        );
    }
    
    public function logSuspiciousActivity(string $activity, array $context = []): void {
        $this->log('ERROR', "SUSPICIOUS: $activity", $context);
    }
    
    private function rotateLogIfNeeded(): void {
        if (filesize($this->logFile) > 10 * 1024 * 1024) { // 10MB
            $backupFile = $this->logFile . '.' . date('Y-m-d-H-i-s');
            rename($this->logFile, $backupFile);
            $this->ensureLogFileExists();
        }
    }
    
    public function getRecentLogs(int $lines = 50): array {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $content = file_get_contents($this->logFile);
        $logLines = explode("\n", trim($content));
        
        return array_slice($logLines, -$lines);
    }
}

// Security Examples
class SecurityExamples {
    private SecurityScanner $scanner;
    private SecureInputHandler $inputHandler;
    private SecurityLogger $logger;
    private RateLimiter $rateLimiter;
    
    public function __construct() {
        $this->scanner = new SecurityScanner();
        $this->inputHandler = new SecureInputHandler();
        $this->logger = new SecurityLogger();
        $this->rateLimiter = new RateLimiter();
    }
    
    public function demonstrateVulnerabilityScanning(): void {
        echo "Security Vulnerability Scanning\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create a vulnerable file for demonstration
        $vulnerableCode = '<?php
$username = $_POST["username"];
$password = $_POST["password"];
$sql = "SELECT * FROM users WHERE username = \'$username\' AND password = \'$password\'";
$result = mysql_query($sql);
echo "Welcome, $username!";
include $page;
eval($code);
?>';
        
        file_put_contents('vulnerable_example.php', $vulnerableCode);
        
        // Scan the file
        $vulnerabilities = $this->scanner->scanFile('vulnerable_example.php');
        $report = $this->scanner->generateReport();
        
        echo $report;
        
        // Clean up
        unlink('vulnerable_example.php');
    }
    
    public function demonstrateInputValidation(): void {
        echo "\nInput Validation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test various inputs
        $testInputs = [
            'normal' => 'john@example.com',
            'xss' => '<script>alert("xss")</script>',
            'sql_injection' => "'; DROP TABLE users; --",
            'null_bytes' => "test\0malicious",
            'long_input' => str_repeat('a', 15000),
            'control_chars' => "test\x00\x01\x02"
        ];
        
        foreach ($testInputs as $type => $input) {
            echo "Testing $type input:\n";
            
            try {
                $sanitized = $this->inputHandler->sanitizeInput($input);
                echo "  Original: " . substr($input, 0, 50) . (strlen($input) > 50 ? '...' : '') . "\n";
                echo "  Sanitized: " . substr($sanitized, 0, 50) . (strlen($sanitized) > 50 ? '...' : '') . "\n";
                
                if ($type === 'normal') {
                    echo "  Email valid: " . ($this->inputHandler->validateEmail($sanitized) ? 'Yes' : 'No') . "\n";
                }
            } catch (Exception $e) {
                echo "  Error: " . $e->getMessage() . "\n";
            }
            
            echo "\n";
        }
    }
    
    public function demonstrateOutputEscaping(): void {
        echo "Output Escaping Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $dangerousInputs = [
            '<script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '" onmouseover="alert(\'XSS\')"',
            '<?php system($_GET["cmd"]); ?>'
        ];
        
        foreach ($dangerousInputs as $input) {
            echo "Input: $input\n";
            echo "HTML escaped: " . $this->inputHandler->escapeOutput($input, 'html') . "\n";
            echo "JS escaped: " . $this->inputHandler->escapeOutput($input, 'js') . "\n";
            echo "URL escaped: " . $this->inputHandler->escapeOutput($input, 'url') . "\n\n";
        }
    }
    
    public function demonstrateRateLimiting(): void {
        echo "Rate Limiting Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $identifier = 'user_123';
        
        echo "Testing rate limiting for $identifier (max 5 attempts):\n";
        
        for ($i = 1; $i <= 7; $i++) {
            $allowed = $this->rateLimiter->isAllowed($identifier);
            $remaining = $this->rateLimiter->getRemainingAttempts($identifier);
            $resetTime = $this->rateLimiter->getResetTime($identifier);
            
            echo "Attempt $i: " . ($allowed ? 'ALLOWED' : 'BLOCKED');
            echo " | Remaining: $remaining";
            echo " | Reset in: " . max(0, $resetTime - time()) . "s\n";
        }
    }
    
    public function demonstrateSecurityLogging(): void {
        echo "\nSecurity Logging Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Log various security events
        $this->logger->logSecurityEvent('Failed login attempt', [
            'username' => 'admin',
            'ip' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0'
        ]);
        
        $this->logger->logLoginAttempt('john@example.com', false, '192.168.1.101');
        $this->logger->logLoginAttempt('jane@example.com', true, '192.168.1.102');
        
        $this->logger->logSuspiciousActivity('Multiple failed attempts from same IP', [
            'ip' => '192.168.1.100',
            'attempts' => 5,
            'timeframe' => '5 minutes'
        ]);
        
        echo "Security events logged. Recent logs:\n";
        $recentLogs = $this->logger->getRecentLogs(5);
        
        foreach ($recentLogs as $log) {
            echo "  $log\n";
        }
    }
    
    public function demonstrateSecurityHeaders(): void {
        echo "\nSecurity Headers Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $headers = new SecurityHeaders();
        
        echo "Default security headers:\n";
        foreach ($headers->getHeaders() as $name => $value) {
            echo "  $name: $value\n";
        }
        
        // Customize headers
        $headers->setHeader('X-Custom-Security', 'Enabled');
        $headers->removeHeader('Permissions-Policy');
        
        echo "\nModified headers:\n";
        foreach ($headers->getHeaders() as $name => $value) {
            echo "  $name: $value\n";
        }
    }
    
    public function demonstrateSecureConfiguration(): void {
        echo "\nSecure Configuration Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        $config = new SecureConfig('secure_config.dat');
        
        echo "Security settings:\n";
        echo "  Session timeout: " . $config->get('security.session_timeout') . " seconds\n";
        echo "  Max login attempts: " . $config->get('security.max_login_attempts') . "\n";
        echo "  Password min length: " . $config->get('security.password_min_length') . "\n";
        echo "  Encryption algorithm: " . $config->get('encryption.algorithm') . "\n";
        
        // Update configuration
        $config->set('security.max_login_attempts', 10);
        echo "  Updated max login attempts: " . $config->get('security.max_login_attempts') . "\n";
    }
    
    public function runAllExamples(): void {
        echo "Security Fundamentals Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateVulnerabilityScanning();
        $this->demonstrateInputValidation();
        $this->demonstrateOutputEscaping();
        $this->demonstrateRateLimiting();
        $this->demonstrateSecurityLogging();
        $this->demonstrateSecurityHeaders();
        $this->demonstrateSecureConfiguration();
    }
}

// Security Best Practices
function printSecurityBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Security Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Input Validation:\n";
    echo "   • Never trust user input\n";
    echo "   • Validate all incoming data\n";
    echo "   • Use whitelist approach\n";
    echo "   • Sanitize and escape output\n\n";
    
    echo "2. Authentication:\n";
    echo "   • Use strong password policies\n";
    echo "   • Implement rate limiting\n";
    echo "   • Use multi-factor authentication\n";
    echo "   • Secure session management\n\n";
    
    echo "3. Data Protection:\n";
    echo "   • Encrypt sensitive data\n";
    echo "   • Use secure hashing for passwords\n";
    echo "   • Implement proper access controls\n";
    echo "   • Secure database connections\n\n";
    
    echo "4. Code Security:\n";
    echo "   • Avoid eval() and similar functions\n";
    echo "   • Use prepared statements\n";
    echo "   • Implement error handling\n";
    echo "   • Regular security audits\n\n";
    
    echo "5. Infrastructure:\n";
    echo "   • Keep software updated\n";
    echo "   • Use HTTPS everywhere\n";
    echo "   • Implement security headers\n";
    echo "   • Monitor and log activities\n\n";
    
    echo "6. OWASP Top 10:\n";
    echo "   • Injection flaws\n";
    echo "   • Broken authentication\n";
    echo "   • Sensitive data exposure\n";
    echo "   • XML external entities\n";
    echo "   • Broken access control\n";
    echo "   • Security misconfiguration\n";
    echo "   • Cross-site scripting\n";
    echo "   • Insecure deserialization\n";
    echo "   • Using components with vulnerabilities\n";
    echo "   • Insufficient logging";
}

// Main execution
function runSecurityFundamentalsDemo(): void {
    $examples = new SecurityExamples();
    $examples->runAllExamples();
    printSecurityBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSecurityFundamentalsDemo();
}
?>
