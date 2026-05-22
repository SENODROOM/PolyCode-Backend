<?php
/**
 * Advanced Security Practices
 * 
 * This file demonstrates advanced security concepts, cryptography,
 * security audits, compliance, and enterprise-level security practices.
 */

// Advanced Cryptography Manager
class AdvancedCryptography
{
    private string $encryptionKey;
    private string $signingKey;
    private array $algorithms;
    
    public function __construct(string $encryptionKey, string $signingKey)
    {
        $this->encryptionKey = $encryptionKey;
        $this->signingKey = $signingKey;
        $this->algorithms = [
            'encryption' => 'aes-256-gcm',
            'hashing' => 'sha256',
            'signing' => 'ed25519'
        ];
    }
    
    /**
     * Encrypt data with authenticated encryption
     */
    public function encrypt(string $data, ?string $additionalData = null): array
    {
        $iv = random_bytes(16);
        $tag = '';
        
        $encrypted = openssl_encrypt(
            $data,
            $this->algorithms['encryption'],
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $additionalData ?? '',
            16
        );
        
        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed');
        }
        
        return [
            'data' => base64_encode($encrypted),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'algorithm' => $this->algorithms['encryption'],
            'timestamp' => time()
        ];
    }
    
    /**
     * Decrypt data with authentication
     */
    public function decrypt(array $encryptedData, ?string $additionalData = null): string
    {
        $data = base64_decode($encryptedData['data']);
        $iv = base64_decode($encryptedData['iv']);
        $tag = base64_decode($encryptedData['tag']);
        
        $decrypted = openssl_decrypt(
            $data,
            $this->algorithms['encryption'],
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $additionalData ?? ''
        );
        
        if ($decrypted === false) {
            throw new \RuntimeException('Decryption failed - data may be tampered');
        }
        
        return $decrypted;
    }
    
    /**
     * Create digital signature
     */
    public function sign(string $data): array
    {
        $signature = '';
        openssl_sign($data, $signature, $this->signingKey, OPENSSL_ALGO_SHA256);
        
        return [
            'signature' => base64_encode($signature),
            'algorithm' => 'sha256',
            'timestamp' => time()
        ];
    }
    
    /**
     * Verify digital signature
     */
    public function verify(string $data, array $signatureData): bool
    {
        $signature = base64_decode($signatureData['signature']);
        
        return openssl_verify(
            $data,
            $signature,
            $this->signingKey,
            OPENSSL_ALGO_SHA256
        ) === 1;
    }
    
    /**
     * Secure hash with salt
     */
    public function secureHash(string $data, ?string $salt = null): array
    {
        if ($salt === null) {
            $salt = random_bytes(32);
        }
        
        $hash = hash_pbkdf2(
            $this->algorithms['hashing'],
            $data,
            $salt,
            100000, // iterations
            64,
            true
        );
        
        return [
            'hash' => base64_encode($hash),
            'salt' => base64_encode($salt),
            'algorithm' => $this->algorithms['hashing'],
            'iterations' => 100000
        ];
    }
    
    /**
     * Verify hash
     */
    public function verifyHash(string $data, array $hashData): bool
    {
        $salt = base64_decode($hashData['salt']);
        $hash = base64_decode($hashData['hash']);
        
        $computedHash = hash_pbkdf2(
            $hashData['algorithm'],
            $data,
            $salt,
            $hashData['iterations'],
            64,
            true
        );
        
        return hash_equals($hash, $computedHash);
    }
    
    /**
     * Generate secure token
     */
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Generate password with security requirements
     */
    public function generateSecurePassword(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
}

// Security Auditor
class SecurityAuditor
{
    private array $vulnerabilities = [];
    private array $checks = [];
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'check_sql_injection' => true,
            'check_xss' => true,
            'check_csrf' => true,
            'check_file_inclusion' => true,
            'check_authentication' => true,
            'check_authorization' => true,
            'check_data_validation' => true,
            'check_error_handling' => true
        ], $config);
        
        $this->initializeChecks();
    }
    
    /**
     * Initialize security checks
     */
    private function initializeChecks(): void
    {
        $this->checks = [
            'sql_injection' => [
                'name' => 'SQL Injection',
                'severity' => 'HIGH',
                'description' => 'Check for SQL injection vulnerabilities',
                'test' => [$this, 'checkSQLInjection']
            ],
            'xss' => [
                'name' => 'Cross-Site Scripting',
                'severity' => 'HIGH',
                'description' => 'Check for XSS vulnerabilities',
                'test' => [$this, 'checkXSS']
            ],
            'csrf' => [
                'name' => 'CSRF',
                'severity' => 'MEDIUM',
                'description' => 'Check for CSRF protection',
                'test' => [$this, 'checkCSRF']
            ],
            'file_inclusion' => [
                'name' => 'File Inclusion',
                'severity' => 'HIGH',
                'description' => 'Check for file inclusion vulnerabilities',
                'test' => [$this, 'checkFileInclusion']
            ],
            'authentication' => [
                'name' => 'Authentication',
                'severity' => 'HIGH',
                'description' => 'Check authentication mechanisms',
                'test' => [$this, 'checkAuthentication']
            ],
            'authorization' => [
                'name' => 'Authorization',
                'severity' => 'MEDIUM',
                'description' => 'Check authorization controls',
                'test' => [$this, 'checkAuthorization']
            ],
            'data_validation' => [
                'name' => 'Data Validation',
                'severity' => 'MEDIUM',
                'description' => 'Check input validation',
                'test' => [$this, 'checkDataValidation']
            ],
            'error_handling' => [
                'name' => 'Error Handling',
                'severity' => 'LOW',
                'description' => 'Check error handling practices',
                'test' => [$this, 'checkErrorHandling']
            ]
        ];
    }
    
    /**
     * Run security audit
     */
    public function audit(array $applicationData): array
    {
        $this->vulnerabilities = [];
        
        foreach ($this->checks as $checkName => $check) {
            if ($this->config["check_$checkName"]) {
                $result = call_user_func($check['test'], $applicationData);
                
                if ($result['vulnerable']) {
                    $this->vulnerabilities[] = [
                        'type' => $checkName,
                        'name' => $check['name'],
                        'severity' => $check['severity'],
                        'description' => $check['description'],
                        'findings' => $result['findings'],
                        'recommendations' => $result['recommendations']
                    ];
                }
            }
        }
        
        return $this->generateReport();
    }
    
    /**
     * Check for SQL injection
     */
    private function checkSQLInjection(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check database queries
        if (isset($data['database_queries'])) {
            foreach ($data['database_queries'] as $query) {
                if (strpos($query, '$') !== false && strpos($query, 'prepare') === false) {
                    $vulnerabilities[] = "Direct variable interpolation in SQL query: $query";
                    $recommendations[] = "Use prepared statements for database queries";
                }
            }
        }
        
        // Check input validation
        if (isset($data['user_inputs'])) {
            foreach ($data['user_inputs'] as $input) {
                if (!$this->isValidated($input)) {
                    $vulnerabilities[] = "Unvalidated user input: $input";
                    $recommendations[] = "Validate all user inputs before database operations";
                }
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check for XSS vulnerabilities
     */
    private function checkXSS(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check output encoding
        if (isset($data['outputs'])) {
            foreach ($data['outputs'] as $output) {
                if (strpos($output, 'echo') !== false && strpos($output, 'htmlspecialchars') === false) {
                    $vulnerabilities[] = "Unencoded output: $output";
                    $recommendations[] = "Use htmlspecialchars() or similar for output encoding";
                }
            }
        }
        
        // Check Content Security Policy
        if (!isset($data['csp_header'])) {
            $vulnerabilities[] = "Missing Content Security Policy header";
            $recommendations[] = "Implement CSP header to prevent XSS attacks";
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check for CSRF protection
     */
    private function checkCSRF(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check CSRF tokens
        if (isset($data['forms'])) {
            foreach ($data['forms'] as $form) {
                if (strpos($form, 'POST') !== false && strpos($form, 'csrf') === false) {
                    $vulnerabilities[] = "POST form without CSRF token: $form";
                    $recommendations[] = "Add CSRF tokens to all state-changing forms";
                }
            }
        }
        
        // Check SameSite cookie attribute
        if (isset($data['cookies'])) {
            foreach ($data['cookies'] as $cookie) {
                if (strpos($cookie, 'SameSite') === false) {
                    $vulnerabilities[] = "Cookie without SameSite attribute: $cookie";
                    $recommendations[] = "Set SameSite attribute on cookies";
                }
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check for file inclusion vulnerabilities
     */
    private function checkFileInclusion(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check file includes
        if (isset($data['file_includes'])) {
            foreach ($data['file_includes'] as $include) {
                if (strpos($include, '$_') !== false) {
                    $vulnerabilities[] = "Dynamic file inclusion: $include";
                    $recommendations[] = "Avoid dynamic file inclusion with user input";
                }
            }
        }
        
        // Check file uploads
        if (isset($data['file_uploads'])) {
            foreach ($data['file_uploads'] as $upload) {
                if (!isset($upload['validation'])) {
                    $vulnerabilities[] = "Unvalidated file upload: $upload";
                    $recommendations[] = "Validate file types and sizes for uploads";
                }
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check authentication mechanisms
     */
    private function checkAuthentication(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check password hashing
        if (isset($data['password_storage'])) {
            if (strpos($data['password_storage'], 'password_hash') === false) {
                $vulnerabilities[] = "Weak password hashing method";
                $recommendations[] = "Use password_hash() with PASSWORD_ARGON2ID or PASSWORD_BCRYPT";
            }
        }
        
        // Check session security
        if (isset($data['session_config'])) {
            if (!isset($data['session_config']['secure']) || !$data['session_config']['secure']) {
                $vulnerabilities[] = "Insecure session configuration";
                $recommendations[] = "Set session.cookie_secure and session.cookie_httponly";
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check authorization controls
     */
    private function checkAuthorization(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check access control
        if (isset($data['access_controls'])) {
            foreach ($data['access_controls'] as $control) {
                if (!isset($control['role_check'])) {
                    $vulnerabilities[] = "Missing role-based access control: $control";
                    $recommendations[] = "Implement proper role-based access control";
                }
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check data validation
     */
    private function checkDataValidation(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check input validation
        if (isset($data['inputs'])) {
            foreach ($data['inputs'] as $input) {
                if (!$this->isValidated($input)) {
                    $vulnerabilities[] = "Unvalidated input: $input";
                    $recommendations[] = "Validate all user inputs";
                }
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check error handling
     */
    private function checkErrorHandling(array $data): array
    {
        $vulnerabilities = [];
        $recommendations = [];
        
        // Check error display
        if (isset($data['error_config'])) {
            if ($data['error_config']['display_errors'] ?? true) {
                $vulnerabilities[] = "Error display enabled in production";
                $recommendations[] = "Disable error display in production";
            }
        }
        
        return [
            'vulnerable' => !empty($vulnerabilities),
            'findings' => $vulnerabilities,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Check if input is validated
     */
    private function isValidated(string $input): bool
    {
        // Simplified validation check
        $validationFunctions = ['filter_var', 'preg_match', 'htmlspecialchars', 'strip_tags'];
        
        foreach ($validationFunctions as $func) {
            if (strpos($input, $func) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate security report
     */
    private function generateReport(): array
    {
        $severityCounts = [
            'HIGH' => 0,
            'MEDIUM' => 0,
            'LOW' => 0
        ];
        
        foreach ($this->vulnerabilities as $vuln) {
            $severityCounts[$vuln['severity']]++;
        }
        
        return [
            'scan_date' => date('Y-m-d H:i:s'),
            'total_vulnerabilities' => count($this->vulnerabilities),
            'severity_breakdown' => $severityCounts,
            'vulnerabilities' => $this->vulnerabilities,
            'risk_score' => $this->calculateRiskScore($severityCounts),
            'recommendations' => $this->generateRecommendations()
        ];
    }
    
    /**
     * Calculate risk score
     */
    private function calculateRiskScore(array $severityCounts): int
    {
        return ($severityCounts['HIGH'] * 10) + ($severityCounts['MEDIUM'] * 5) + ($severityCounts['LOW'] * 1);
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        foreach ($this->vulnerabilities as $vuln) {
            $recommendations = array_merge($recommendations, $vuln['recommendations']);
        }
        
        return array_unique($recommendations);
    }
}

// API Security Manager
class APISecurityManager
{
    private array $config;
    private AdvancedCryptography $crypto;
    private array $rateLimits = [];
    private array $apiKeys = [];
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'require_https' => true,
            'enable_cors' => true,
            'enable_rate_limiting' => true,
            'enable_api_keys' => true,
            'jwt_secret' => 'default_secret_change_me',
            'rate_limit_window' => 3600, // 1 hour
            'rate_limit_requests' => 1000
        ], $config);
        
        $this->crypto = new AdvancedCryptography(
            $this->config['jwt_secret'],
            $this->config['jwt_secret']
        );
        
        $this->initializeAPIKeys();
    }
    
    /**
     * Initialize API keys
     */
    private function initializeAPIKeys(): void
    {
        $this->apiKeys = [
            'key_123456' => [
                'id' => 1,
                'name' => 'Test Client',
                'permissions' => ['read', 'write'],
                'rate_limit' => 100,
                'created_at' => time()
            ],
            'key_789012' => [
                'id' => 2,
                'name' => 'Premium Client',
                'permissions' => ['read', 'write', 'admin'],
                'rate_limit' => 1000,
                'created_at' => time()
            ]
        ];
    }
    
    /**
     * Validate API request
     */
    public function validateRequest(array $request): array
    {
        $errors = [];
        
        // Check HTTPS
        if ($this->config['require_https'] && !isset($request['https'])) {
            $errors[] = 'HTTPS required';
        }
        
        // Check API key
        if ($this->config['enable_api_keys']) {
            $apiKeyResult = $this->validateAPIKey($request);
            if (!$apiKeyResult['valid']) {
                $errors[] = $apiKeyResult['error'];
            }
        }
        
        // Check rate limiting
        if ($this->config['enable_rate_limiting']) {
            $rateLimitResult = $this->checkRateLimit($request);
            if (!$rateLimitResult['allowed']) {
                $errors[] = 'Rate limit exceeded';
            }
        }
        
        // Check JWT token
        if (isset($request['authorization'])) {
            $jwtResult = $this->validateJWT($request['authorization']);
            if (!$jwtResult['valid']) {
                $errors[] = $jwtResult['error'];
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validate API key
     */
    private function validateAPIKey(array $request): array
    {
        $apiKey = $request['api_key'] ?? $request['headers']['X-API-Key'] ?? null;
        
        if (!$apiKey) {
            return ['valid' => false, 'error' => 'API key required'];
        }
        
        if (!isset($this->apiKeys[$apiKey])) {
            return ['valid' => false, 'error' => 'Invalid API key'];
        }
        
        return ['valid' => true, 'key_data' => $this->apiKeys[$apiKey]];
    }
    
    /**
     * Check rate limiting
     */
    private function checkRateLimit(array $request): array
    {
        $clientId = $request['client_ip'] ?? 'unknown';
        $window = $this->config['rate_limit_window'];
        $limit = $this->config['rate_limit_requests'];
        
        $now = time();
        $windowStart = $now - $window;
        
        // Clean old entries
        if (isset($this->rateLimits[$clientId])) {
            $this->rateLimits[$clientId] = array_filter(
                $this->rateLimits[$clientId],
                fn($timestamp) => $timestamp > $windowStart
            );
        } else {
            $this->rateLimits[$clientId] = [];
        }
        
        // Check current count
        $currentCount = count($this->rateLimits[$clientId]);
        
        if ($currentCount >= $limit) {
            return [
                'allowed' => false,
                'limit' => $limit,
                'remaining' => 0,
                'reset_time' => $windowStart + $window
            ];
        }
        
        // Add current request
        $this->rateLimits[$clientId][] = $now;
        
        return [
            'allowed' => true,
            'limit' => $limit,
            'remaining' => $limit - $currentCount - 1,
            'reset_time' => $windowStart + $window
        ];
    }
    
    /**
     * Validate JWT token
     */
    private function validateJWT(string $token): array
    {
        // Simplified JWT validation
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return ['valid' => false, 'error' => 'Invalid token format'];
        }
        
        try {
            $header = json_decode(base64_decode($parts[0]), true);
            $payload = json_decode(base64_decode($parts[1]), true);
            $signature = $parts[2];
            
            if (!$payload || !isset($payload['exp'])) {
                return ['valid' => false, 'error' => 'Invalid token payload'];
            }
            
            if ($payload['exp'] < time()) {
                return ['valid' => false, 'error' => 'Token expired'];
            }
            
            // Verify signature (simplified)
            $expectedSignature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $this->config['jwt_secret']);
            
            if (!hash_equals($expectedSignature, $signature)) {
                return ['valid' => false, 'error' => 'Invalid token signature'];
            }
            
            return ['valid' => true, 'payload' => $payload];
            
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Token parsing error'];
        }
    }
    
    /**
     * Generate JWT token
     */
    public function generateJWT(array $payload, int $expiresIn = 3600): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        
        $payload['exp'] = time() + $expiresIn;
        $payload['iat'] = time();
        
        $headerEncoded = base64_encode(json_encode($header));
        $payloadEncoded = base64_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->config['jwt_secret']);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }
    
    /**
     * Add CORS headers
     */
    public function addCORSHeaders(array $allowedOrigins = [], array $allowedMethods = []): array
    {
        $headers = [];
        
        if ($this->config['enable_cors']) {
            $headers['Access-Control-Allow-Origin'] = $allowedOrigins[0] ?? '*';
            $headers['Access-Control-Allow-Methods'] = implode(', ', $allowedMethods ?: ['GET', 'POST', 'PUT', 'DELETE']);
            $headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-API-Key';
            $headers['Access-Control-Max-Age'] = '86400';
        }
        
        return $headers;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate input data
     */
    public function validateInput(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                if ($rule['required'] ?? false) {
                    $errors[$field] = "$field is required";
                }
                continue;
            }
            
            $value = $data[$field];
            
            // Type validation
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "$field must be a valid email";
                        }
                        break;
                    case 'int':
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field] = "$field must be an integer";
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            $errors[$field] = "$field must be a string";
                        }
                        break;
                }
            }
            
            // Length validation
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = "$field must be at least {$rule['min_length']} characters";
            }
            
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = "$field must not exceed {$rule['max_length']} characters";
            }
            
            // Pattern validation
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                $errors[$field] = "$field format is invalid";
            }
        }
        
        return $errors;
    }
}

// Compliance Manager
class ComplianceManager
{
    private array $standards;
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->initializeStandards();
    }
    
    /**
     * Initialize compliance standards
     */
    private function initializeStandards(): void
    {
        $this->standards = [
            'GDPR' => [
                'name' => 'General Data Protection Regulation',
                'requirements' => [
                    'data_protection' => 'Implement data protection measures',
                    'consent' => 'Obtain explicit consent for data processing',
                    'data_breach_notification' => 'Notify within 72 hours of breach',
                    'right_to_erasure' => 'Provide right to delete personal data',
                    'data_portability' => 'Provide data in machine-readable format',
                    'privacy_policy' => 'Maintain clear privacy policy'
                ]
            ],
            'HIPAA' => [
                'name' => 'Health Insurance Portability and Accountability Act',
                'requirements' => [
                    'access_control' => 'Implement access controls',
                    'audit_controls' => 'Implement audit controls',
                    'integrity' => 'Protect data integrity',
                    'person_authentication' => 'Verify person identity',
                    'transmission_security' => 'Secure data transmission'
                ]
            ],
            'PCI_DSS' => [
                'name' => 'Payment Card Industry Data Security Standard',
                'requirements' => [
                    'network_security' => 'Maintain secure network',
                    'cardholder_data' => 'Protect cardholder data',
                    'vulnerability_management' => 'Maintain vulnerability program',
                    'access_control' => 'Implement strong access controls',
                    'monitoring' => 'Regularly monitor and test networks'
                ]
            ],
            'SOC2' => [
                'name' => 'Service Organization Control 2',
                'requirements' => [
                    'security' => 'Implement security controls',
                    'availability' => 'Ensure service availability',
                    'processing_integrity' => 'Maintain processing integrity',
                    'confidentiality' => 'Protect confidential information',
                    'privacy' => 'Protect personal information'
                ]
            ]
        ];
    }
    
    /**
     * Check compliance
     */
    public function checkCompliance(array $applicationData, array $standards = []): array
    {
        if (empty($standards)) {
            $standards = array_keys($this->standards);
        }
        
        $results = [];
        
        foreach ($standards as $standard) {
            if (isset($this->standards[$standard])) {
                $results[$standard] = $this->checkStandard($standard, $applicationData);
            }
        }
        
        return $results;
    }
    
    /**
     * Check specific standard
     */
    private function checkStandard(string $standard, array $data): array
    {
        $requirements = $this->standards[$standard]['requirements'];
        $results = [];
        
        foreach ($requirements as $requirement => $description) {
            $results[$requirement] = $this->checkRequirement($requirement, $data);
        }
        
        return [
            'name' => $this->standards[$standard]['name'],
            'compliant' => $this->calculateCompliance($results),
            'requirements' => $results
        ];
    }
    
    /**
     * Check specific requirement
     */
    private function checkRequirement(string $requirement, array $data): array
    {
        $compliant = false;
        $evidence = [];
        
        switch ($requirement) {
            case 'data_protection':
                $compliant = isset($data['encryption']) && $data['encryption']['enabled'];
                $evidence = ['encryption_enabled' => $data['encryption']['enabled'] ?? false];
                break;
                
            case 'consent':
                $compliant = isset($data['consent_management']) && $data['consent_management']['enabled'];
                $evidence = ['consent_management' => $data['consent_management']['enabled'] ?? false];
                break;
                
            case 'access_control':
                $compliant = isset($data['access_control']) && $data['access_control']['implemented'];
                $evidence = ['access_control' => $data['access_control']['implemented'] ?? false];
                break;
                
            case 'audit_controls':
                $compliant = isset($data['audit_logging']) && $data['audit_logging']['enabled'];
                $evidence = ['audit_logging' => $data['audit_logging']['enabled'] ?? false];
                break;
                
            case 'network_security':
                $compliant = isset($data['firewall']) && $data['firewall']['enabled'];
                $evidence = ['firewall' => $data['firewall']['enabled'] ?? false];
                break;
                
            default:
                $compliant = false;
                $evidence = ['unknown_requirement' => true];
        }
        
        return [
            'compliant' => $compliant,
            'evidence' => $evidence,
            'description' => $this->standards['GDPR']['requirements'][$requirement] ?? 'Unknown requirement'
        ];
    }
    
    /**
     * Calculate compliance percentage
     */
    private function calculateCompliance(array $results): float
    {
        $total = count($results);
        $compliant = count(array_filter($results, fn($r) => $r['compliant']));
        
        return $total > 0 ? ($compliant / $total) * 100 : 0;
    }
    
    /**
     * Generate compliance report
     */
    public function generateReport(array $complianceResults): array
    {
        $overallCompliance = 0;
        $totalStandards = count($complianceResults);
        
        foreach ($complianceResults as $result) {
            $overallCompliance += $result['compliant'];
        }
        
        $overallCompliance = $totalStandards > 0 ? $overallCompliance / $totalStandards : 0;
        
        return [
            'report_date' => date('Y-m-d H:i:s'),
            'overall_compliance' => round($overallCompliance, 2),
            'standards_checked' => $totalStandards,
            'detailed_results' => $complianceResults,
            'recommendations' => $this->generateComplianceRecommendations($complianceResults)
        ];
    }
    
    /**
     * Generate compliance recommendations
     */
    private function generateComplianceRecommendations(array $results): array
    {
        $recommendations = [];
        
        foreach ($results as $standard => $result) {
            foreach ($result['requirements'] as $requirement => $check) {
                if (!$check['compliant']) {
                    $recommendations[] = [
                        'standard' => $standard,
                        'requirement' => $requirement,
                        'description' => $check['description'],
                        'priority' => $this->getRequirementPriority($requirement)
                    ];
                }
            }
        }
        
        // Sort by priority
        usort($recommendations, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        
        return $recommendations;
    }
    
    /**
     * Get requirement priority
     */
    private function getRequirementPriority(string $requirement): int
    {
        $priorities = [
            'data_protection' => 10,
            'access_control' => 9,
            'consent' => 8,
            'audit_controls' => 7,
            'network_security' => 6,
            'transmission_security' => 5
        ];
        
        return $priorities[$requirement] ?? 1;
    }
}

// Advanced Security Examples
class AdvancedSecurityExamples
{
    private AdvancedCryptography $crypto;
    private SecurityAuditor $auditor;
    private APISecurityManager $apiSecurity;
    private ComplianceManager $compliance;
    
    public function __construct()
    {
        $this->crypto = new AdvancedCryptography('encryption_key_123', 'signing_key_456');
        $this->auditor = new SecurityAuditor();
        $this->apiSecurity = new APISecurityManager();
        $this->compliance = new ComplianceManager();
    }
    
    public function demonstrateCryptography(): void
    {
        echo "Advanced Cryptography Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Encryption
        $data = "Sensitive data to encrypt";
        $encrypted = $this->crypto->encrypt($data);
        
        echo "Original: $data\n";
        echo "Encrypted: " . json_encode($encrypted) . "\n";
        
        // Decryption
        $decrypted = $this->crypto->decrypt($encrypted);
        echo "Decrypted: $decrypted\n";
        echo "Match: " . ($data === $decrypted ? 'Yes' : 'No') . "\n";
        
        // Digital signature
        $signature = $this->crypto->sign($data);
        echo "\nSignature: " . json_encode($signature) . "\n";
        
        $verified = $this->crypto->verify($data, $signature);
        echo "Verified: " . ($verified ? 'Yes' : 'No') . "\n";
        
        // Secure hashing
        $hash = $this->crypto->secureHash('password123');
        echo "\nHash: " . json_encode($hash) . "\n";
        
        $hashVerified = $this->crypto->verifyHash('password123', $hash);
        echo "Hash verified: " . ($hashVerified ? 'Yes' : 'No') . "\n";
        
        // Token generation
        $token = $this->crypto->generateToken(32);
        echo "\nSecure token: $token\n";
        
        $password = $this->crypto->generateSecurePassword();
        echo "Secure password: $password\n";
    }
    
    public function demonstrateSecurityAudit(): void
    {
        echo "\nSecurity Audit Example\n";
        echo str_repeat("-", 25) . "\n";
        
        // Simulate application data
        $appData = [
            'database_queries' => [
                "SELECT * FROM users WHERE id = $user_id",
                "SELECT * FROM posts WHERE title = '$title'"
            ],
            'user_inputs' => [
                '$_POST["username"]',
                '$_GET["search"]'
            ],
            'outputs' => [
                'echo $user_input;',
                'print $data;'
            ],
            'forms' => [
                '<form method="POST"><input type="text" name="data"></form>'
            ],
            'password_storage' => 'md5($password)',
            'session_config' => [
                'secure' => false,
                'httponly' => false
            ],
            'error_config' => [
                'display_errors' => true
            ]
        ];
        
        // Run audit
        $report = $this->auditor->audit($appData);
        
        echo "Audit completed on: {$report['scan_date']}\n";
        echo "Total vulnerabilities: {$report['total_vulnerabilities']}\n";
        echo "Risk score: {$report['risk_score']}\n\n";
        
        echo "Severity breakdown:\n";
        foreach ($report['severity_breakdown'] as $severity => $count) {
            echo "  $severity: $count\n";
        }
        
        echo "\nTop vulnerabilities:\n";
        foreach (array_slice($report['vulnerabilities'], 0, 3) as $vuln) {
            echo "  {$vuln['name']} ({$vuln['severity']})\n";
            echo "    Description: {$vuln['description']}\n";
            echo "    Findings: " . implode(', ', $vuln['findings']) . "\n\n";
        }
        
        echo "Recommendations:\n";
        foreach (array_slice($report['recommendations'], 0, 5) as $rec) {
            echo "  • $rec\n";
        }
    }
    
    public function demonstrateAPISecurity(): void
    {
        echo "\nAPI Security Example\n";
        echo str_repeat("-", 22) . "\n";
        
        // Generate JWT
        $payload = [
            'user_id' => 123,
            'email' => 'user@example.com',
            'role' => 'user'
        ];
        
        $jwt = $this->apiSecurity->generateJWT($payload);
        echo "Generated JWT: " . substr($jwt, 0, 50) . "...\n";
        
        // Validate request
        $request = [
            'https' => true,
            'api_key' => 'key_123456',
            'client_ip' => '192.168.1.1',
            'authorization' => $jwt,
            'data' => [
                'name' => '<script>alert("xss")</script>',
                'email' => 'test@example.com'
            ]
        ];
        
        $validation = $this->apiSecurity->validateRequest($request);
        echo "Request validation: " . ($validation['valid'] ? 'Valid' : 'Invalid') . "\n";
        
        if (!$validation['valid']) {
            echo "Errors: " . implode(', ', $validation['errors']) . "\n";
        }
        
        // Sanitize input
        $sanitized = $this->apiSecurity->sanitizeInput($request['data']);
        echo "Sanitized name: " . $sanitized['name'] . "\n";
        
        // Validate input
        $rules = [
            'name' => [
                'required' => true,
                'type' => 'string',
                'min_length' => 2,
                'max_length' => 50
            ],
            'email' => [
                'required' => true,
                'type' => 'email'
            ]
        ];
        
        $inputValidation = $this->apiSecurity->validateInput($request['data'], $rules);
        echo "Input validation: " . (empty($inputValidation) ? 'Valid' : 'Invalid') . "\n";
        
        if (!empty($inputValidation)) {
            echo "Validation errors: " . implode(', ', $inputValidation) . "\n";
        }
        
        // Add CORS headers
        $corsHeaders = $this->apiSecurity->addCORSHeaders(
            ['https://example.com'],
            ['GET', 'POST', 'PUT']
        );
        
        echo "\nCORS headers:\n";
        foreach ($corsHeaders as $header => $value) {
            echo "  $header: $value\n";
        }
    }
    
    public function demonstrateCompliance(): void
    {
        echo "\nCompliance Example\n";
        echo str_repeat("-", 20) . "\n";
        
        // Simulate application data
        $appData = [
            'encryption' => ['enabled' => true],
            'consent_management' => ['enabled' => false],
            'access_control' => ['implemented' => true],
            'audit_logging' => ['enabled' => true],
            'firewall' => ['enabled' => false]
        ];
        
        // Check compliance
        $complianceResults = $this->compliance->checkCompliance($appData, ['GDPR', 'HIPAA', 'PCI_DSS']);
        
        echo "Compliance Results:\n";
        foreach ($complianceResults as $standard => $result) {
            echo "\n{$result['name']}: " . round($result['compliant'], 1) . "% compliant\n";
            
            foreach ($result['requirements'] as $requirement => $check) {
                $status = $check['compliant'] ? '✓' : '✗';
                echo "  $status {$requirement}: {$check['description']}\n";
            }
        }
        
        // Generate report
        $report = $this->compliance->generateReport($complianceResults);
        
        echo "\nOverall compliance: {$report['overall_compliance']}%\n";
        echo "Standards checked: {$report['standards_checked']}\n";
        
        echo "\nTop recommendations:\n";
        foreach (array_slice($report['recommendations'], 0, 3) as $rec) {
            echo "  [{$rec['standard']}] {$rec['requirement']}: {$rec['description']}\n";
        }
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Security Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateCryptography();
        $this->demonstrateSecurityAudit();
        $this->demonstrateAPISecurity();
        $this->demonstrateCompliance();
    }
}

// Advanced Security Best Practices
function printAdvancedSecurityBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Advanced Security Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Cryptography:\n";
    echo "   • Use authenticated encryption\n";
    echo "   • Implement proper key management\n";
    echo "   • Use modern algorithms (AES-256-GCM)\n";
    echo "   • Generate secure random values\n";
    echo "   • Implement digital signatures\n\n";
    
    echo "2. Security Auditing:\n";
    echo "   • Conduct regular security audits\n";
    echo "   • Use automated scanning tools\n";
    echo "   • Check for common vulnerabilities\n";
    echo "   • Implement penetration testing\n";
    echo "   • Monitor for new threats\n\n";
    
    echo "3. API Security:\n";
    echo "   • Implement proper authentication\n";
    echo "   • Use JWT tokens securely\n";
    echo "   • Implement rate limiting\n";
    echo "   • Validate all inputs\n";
    echo "   • Use HTTPS everywhere\n\n";
    
    echo "4. Compliance:\n";
    echo "   • Understand applicable regulations\n";
    echo "   • Implement required controls\n";
    echo "   • Maintain compliance documentation\n";
    echo "   • Conduct regular assessments\n";
    echo "   • Stay updated on changes\n\n";
    
    echo "5. Data Protection:\n";
    echo "   • Encrypt sensitive data at rest\n";
    echo "   • Encrypt data in transit\n";
    echo "   • Implement proper access controls\n";
    echo "   • Use data masking\n";
    echo "   • Implement backup encryption\n\n";
    
    echo "6. Monitoring:\n";
    echo "   • Implement security logging\n";
    echo "   • Monitor for anomalies\n";
    echo "   • Set up alerting\n";
    echo "   • Conduct incident response\n";
    echo "   • Regular security reviews";
}

// Main execution
function runAdvancedSecurityDemo(): void
{
    $examples = new AdvancedSecurityExamples();
    $examples->runAllExamples();
    printAdvancedSecurityBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAdvancedSecurityDemo();
}
?>
