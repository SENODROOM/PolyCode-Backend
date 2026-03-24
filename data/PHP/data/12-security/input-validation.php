<?php
/**
 * Input Validation and Output Encoding
 * 
 * This file demonstrates comprehensive input validation and output
 * encoding techniques to prevent XSS, SQL injection, and other attacks.
 */

// Advanced Input Validator
class InputValidator {
    private array $rules = [];
    private array $errors = [];
    private array $sanitizers = [];
    
    public function __construct() {
        $this->initializeDefaultRules();
        $this->initializeSanitizers();
    }
    
    private function initializeDefaultRules(): void {
        $this->rules = [
            'email' => [
                'required' => true,
                'max_length' => 254,
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'filter' => FILTER_VALIDATE_EMAIL
            ],
            'username' => [
                'required' => true,
                'min_length' => 3,
                'max_length' => 50,
                'pattern' => '/^[a-zA-Z0-9_-]+$/'
            ],
            'password' => [
                'required' => true,
                'min_length' => 8,
                'max_length' => 128,
                'require_uppercase' => true,
                'require_lowercase' => true,
                'require_number' => true,
                'require_special' => true
            ],
            'url' => [
                'required' => false,
                'max_length' => 2048,
                'filter' => FILTER_VALIDATE_URL,
                'allowed_schemes' => ['http', 'https']
            ],
            'phone' => [
                'required' => false,
                'pattern' => '/^\+?[\d\s\-\(\)]+$/',
                'max_length' => 20
            ],
            'numeric' => [
                'required' => false,
                'filter' => FILTER_VALIDATE_INT,
                'min_range' => 0,
                'max_range' => PHP_INT_MAX
            ],
            'alpha' => [
                'required' => false,
                'pattern' => '/^[a-zA-Z]+$/'
            ],
            'alphanumeric' => [
                'required' => false,
                'pattern' => '/^[a-zA-Z0-9]+$/'
            ]
        ];
    }
    
    private function initializeSanitizers(): void {
        $this->sanitizers = [
            'string' => function($value) {
                return trim($value);
            },
            'int' => function($value) {
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            },
            'float' => function($value) {
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
            },
            'email' => function($value) {
                return filter_var($value, FILTER_SANITIZE_EMAIL);
            },
            'url' => function($value) {
                return filter_var($value, FILTER_SANITIZE_URL);
            },
            'html' => function($value) {
                return strip_tags($value);
            }
        ];
    }
    
    public function addRule(string $field, array $rule): void {
        $this->rules[$field] = $rule;
    }
    
    public function addSanitizer(string $type, callable $sanitizer): void {
        $this->sanitizers[$type] = $sanitizer;
    }
    
    public function validate(array $data, array $rules = []): array {
        $this->errors = [];
        $validated = [];
        $useRules = empty($rules) ? $this->rules : array_intersect_key($this->rules, array_flip($rules));
        
        foreach ($useRules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Check if required
            if (isset($rule['required']) && $rule['required'] && ($value === null || $value === '')) {
                $this->errors[$field][] = "$field is required";
                continue;
            }
            
            // Skip validation if field is optional and empty
            if (!isset($rule['required']) || !$rule['required']) {
                if ($value === null || $value === '') {
                    $validated[$field] = null;
                    continue;
                }
            }
            
            // Sanitize input
            $sanitizedValue = $this->sanitize($value, $rule['sanitize'] ?? 'string');
            
            // Apply filters
            if (isset($rule['filter'])) {
                $filtered = filter_var($sanitizedValue, $rule['filter'], $rule['filter_options'] ?? []);
                if ($filtered === false) {
                    $this->errors[$field][] = "$field is invalid";
                    continue;
                }
                $sanitizedValue = $filtered;
            }
            
            // Validate length
            if (isset($rule['min_length']) && strlen($sanitizedValue) < $rule['min_length']) {
                $this->errors[$field][] = "$field must be at least {$rule['min_length']} characters";
            }
            
            if (isset($rule['max_length']) && strlen($sanitizedValue) > $rule['max_length']) {
                $this->errors[$field][] = "$field must not exceed {$rule['max_length']} characters";
            }
            
            // Validate pattern
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $sanitizedValue)) {
                $this->errors[$field][] = "$field format is invalid";
            }
            
            // Validate range
            if (isset($rule['min_range']) && $sanitizedValue < $rule['min_range']) {
                $this->errors[$field][] = "$field must be at least {$rule['min_range']}";
            }
            
            if (isset($rule['max_range']) && $sanitizedValue > $rule['max_range']) {
                $this->errors[$field][] = "$field must not exceed {$rule['max_range']}";
            }
            
            // Validate password requirements
            if ($field === 'password') {
                $this->validatePassword($sanitizedValue, $rule);
            }
            
            // Validate URL schemes
            if ($field === 'url' && isset($rule['allowed_schemes'])) {
                $parsed = parse_url($sanitizedValue);
                if (!in_array(strtolower($parsed['scheme'] ?? ''), $rule['allowed_schemes'])) {
                    $this->errors[$field][] = "$field scheme is not allowed";
                }
            }
            
            $validated[$field] = $sanitizedValue;
        }
        
        return $validated;
    }
    
    private function validatePassword(string $password, array $rule): void {
        if (isset($rule['require_uppercase']) && $rule['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $this->errors['password'][] = "Password must contain at least one uppercase letter";
        }
        
        if (isset($rule['require_lowercase']) && $rule['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $this->errors['password'][] = "Password must contain at least one lowercase letter";
        }
        
        if (isset($rule['require_number']) && $rule['require_number'] && !preg_match('/[0-9]/', $password)) {
            $this->errors['password'][] = "Password must contain at least one number";
        }
        
        if (isset($rule['require_special']) && $rule['require_special'] && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $this->errors['password'][] = "Password must contain at least one special character";
        }
    }
    
    private function sanitize(mixed $value, string $type): mixed {
        if (!isset($this->sanitizers[$type])) {
            return $value;
        }
        
        return ($this->sanitizers[$type])($value);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    public function getFirstError(): ?string {
        if (empty($this->errors)) {
            return null;
        }
        
        return reset($this->errors)[0];
    }
}

// Output Encoder
class OutputEncoder {
    private array $contexts = [
        'html' => [
            'function' => 'htmlspecialchars',
            'flags' => ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE
        ],
        'js' => [
            'function' => 'json_encode',
            'flags' => JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
        ],
        'css' => [
            'function' => 'htmlspecialchars',
            'flags' => ENT_QUOTES | ENT_HTML5
        ],
        'url' => [
            'function' => 'rawurlencode'
        ],
        'xml' => [
            'function' => 'htmlspecialchars',
            'flags' => ENT_XML1 | ENT_QUOTES
        ],
        'attribute' => [
            'function' => 'htmlspecialchars',
            'flags' => ENT_QUOTES | ENT_HTML5
        ]
    ];
    
    public function encode(mixed $value, string $context = 'html'): string {
        if (!isset($this->contexts[$context])) {
            throw new InvalidArgumentException("Unknown context: $context");
        }
        
        $encoder = $this->contexts[$context];
        
        switch ($encoder['function']) {
            case 'htmlspecialchars':
                return htmlspecialchars($value, $encoder['flags'] ?? ENT_QUOTES, 'UTF-8');
            case 'json_encode':
                return json_encode($value, $encoder['flags'] ?? 0);
            case 'rawurlencode':
                return rawurlencode($value);
            default:
                return $value;
        }
    }
    
    public function encodeForHTML(string $value): string {
        return $this->encode($value, 'html');
    }
    
    public function encodeForJS(mixed $value): string {
        return $this->encode($value, 'js');
    }
    
    public function encodeForCSS(string $value): string {
        return $this->encode($value, 'css');
    }
    
    public function encodeForURL(string $value): string {
        return $this->encode($value, 'url');
    }
    
    public function encodeForXML(string $value): string {
        return $this->encode($value, 'xml');
    }
    
    public function encodeForAttribute(string $value): string {
        return $this->encode($value, 'attribute');
    }
}

// XSS Protection
class XSSProtection {
    private OutputEncoder $encoder;
    private array $allowedTags = [];
    private array $allowedAttributes = [];
    
    public function __construct(array $allowedTags = [], array $allowedAttributes = []) {
        $this->encoder = new OutputEncoder();
        $this->allowedTags = $allowedTags;
        $this->allowedAttributes = $allowedAttributes;
    }
    
    public function cleanHTML(string $html): string {
        // Remove potentially dangerous content
        $html = $this->removeDangerousElements($html);
        
        // Strip scripts and dangerous attributes
        $html = strip_tags($html, implode('', $this->allowedTags));
        
        // Clean remaining attributes
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*');
        
        foreach ($nodes as $node) {
            if ($node->hasAttributes()) {
                $attributes = [];
                foreach ($node->attributes as $attr) {
                    if (in_array($attr->nodeName, $this->allowedAttributes)) {
                        $attributes[$attr->nodeName] = $this->encoder->encodeForAttribute($attr->nodeValue);
                    }
                }
                
                // Remove all attributes and re-add allowed ones
                foreach ($node->attributes as $attr) {
                    $node->removeAttribute($attr->nodeName);
                }
                
                foreach ($attributes as $name => $value) {
                    $node->setAttribute($name, $value);
                }
            }
        }
        
        return $dom->saveHTML();
    }
    
    private function removeDangerousElements(string $html): string {
        $dangerousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/is',
            '/<form[^>]*>.*?<\/form>/is',
            '/<input[^>]*>/is',
            '/javascript:/i',
            '/on\w+\s*=/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }
        
        return $html;
    }
    
    public function detectXSS(string $input): bool {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/<.*?on\w+\s*=.*?>/i',
            '/<.*?expression\s*\(.*?\)/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}

// SQL Injection Protection
class SQLInjectionProtection {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function secureQuery(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function escapeIdentifier(string $identifier): string {
        // Only allow alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid identifier');
        }
        
        return "`$identifier`";
    }
    
    public function buildWhereClause(array $conditions): array {
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $field = $this->escapeIdentifier($field);
            $placeholder = '?';
            
            if (is_array($value)) {
                $placeholders = implode(',', array_fill(0, count($value), '?'));
                $where[] = "$field IN ($placeholders)";
                $params = array_merge($params, $value);
            } else {
                $where[] = "$field = $placeholder";
                $params[] = $value;
            }
        }
        
        return [
            'where' => empty($where) ? '' : 'WHERE ' . implode(' AND ', $where),
            'params' => $params
        ];
    }
    
    public function detectSQLInjection(string $input): bool {
        $sqlPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
            '/(--|\/\*|\*\/|;|\'|"|`)/',
            '/(\b(OR|AND)\b\s+\d+\s*=\s*\d+)/i',
            '/(\b(OR|AND)\b\s+["\'][^"\']*["\']\s*=\s*["\'][^"\']*["\'])/i',
            '/(\b(OR|AND)\b\s+\w+\s*=\s*\w+)/i',
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i'
        ];
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}

// CSRF Protection
class CSRFProtection {
    private string $token;
    private string $sessionKey = 'csrf_token';
    private int $tokenLength = 32;
    private int $expiry = 3600; // 1 hour
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->token = $this->generateToken();
        $_SESSION[$this->sessionKey] = [
            'token' => $this->token,
            'expires' => time() + $this->expiry
        ];
    }
    
    private function generateToken(): string {
        return bin2hex(random_bytes($this->tokenLength / 2));
    }
    
    public function getToken(): string {
        return $this->token;
    }
    
    public function validateToken(string $token): bool {
        if (!isset($_SESSION[$this->sessionKey])) {
            return false;
        }
        
        $sessionData = $_SESSION[$this->sessionKey];
        
        // Check if token matches
        if (!hash_equals($sessionData['token'], $token)) {
            return false;
        }
        
        // Check if token has expired
        if (time() > $sessionData['expires']) {
            unset($_SESSION[$this->sessionKey]);
            return false;
        }
        
        return true;
    }
    
    public function generateHiddenField(): string {
        return '<input type="hidden" name="csrf_token" value="' . $this->getToken() . '">';
    }
    
    public function generateMetaTag(): string {
        return '<meta name="csrf-token" content="' . $this->getToken() . '">';
    }
    
    public function regenerateToken(): void {
        $this->token = $this->generateToken();
        $_SESSION[$this->sessionKey] = [
            'token' => $this->token,
            'expires' => time() + $this->expiry
        ];
    }
    
    public function cleanup(): void {
        if (isset($_SESSION[$this->sessionKey]) && time() > $_SESSION[$this->sessionKey]['expires']) {
            unset($_SESSION[$this->sessionKey]);
        }
    }
}

// Input Validation Examples
class InputValidationExamples {
    private InputValidator $validator;
    private OutputEncoder $encoder;
    private XSSProtection $xssProtection;
    private SQLInjectionProtection $sqlProtection;
    private CSRFProtection $csrfProtection;
    
    public function __construct() {
        $this->validator = new InputValidator();
        $this->encoder = new OutputEncoder();
        $this->xssProtection = new XSSProtection();
        
        // Create a mock PDO for SQL injection examples
        $pdo = new PDO('sqlite::memory:');
        $this->sqlProtection = new SQLInjectionProtection($pdo);
        
        $this->csrfProtection = new CSRFProtection();
    }
    
    public function demonstrateInputValidation(): void {
        echo "Input Validation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test data
        $testData = [
            'email' => 'john@example.com',
            'username' => 'john_doe123',
            'password' => 'SecurePass123!',
            'url' => 'https://example.com',
            'phone' => '+1 (555) 123-4567',
            'numeric' => '42',
            'invalid_email' => 'invalid-email',
            'short_password' => '123',
            'xss_attempt' => '<script>alert("xss")</script>'
        ];
        
        echo "Validating user registration data:\n";
        $validated = $this->validator->validate($testData, ['email', 'username', 'password']);
        
        if ($this->validator->hasErrors()) {
            echo "Validation errors:\n";
            foreach ($this->validator->getErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    echo "  $field: $error\n";
                }
            }
        } else {
            echo "All fields validated successfully!\n";
            echo "Validated data:\n";
            foreach ($validated as $field => $value) {
                echo "  $field: $value\n";
            }
        }
        
        echo "\nTesting invalid data:\n";
        $invalidData = [
            'email' => $testData['invalid_email'],
            'password' => $testData['short_password']
        ];
        
        $this->validator->validate($invalidData, ['email', 'password']);
        
        foreach ($this->validator->getErrors() as $field => $errors) {
            foreach ($errors as $error) {
                echo "  $field: $error\n";
            }
        }
    }
    
    public function demonstrateOutputEncoding(): void {
        echo "\nOutput Encoding Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $dangerousInputs = [
            '<script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '" onmouseover="alert(\'XSS\')"',
            '<?php system($_GET["cmd"]); ?>',
            'Test & Example',
            'Test "Quotes" Example'
        ];
        
        foreach ($dangerousInputs as $input) {
            echo "Input: $input\n";
            echo "HTML: " . $this->encoder->encodeForHTML($input) . "\n";
            echo "JS: " . $this->encoder->encodeForJS($input) . "\n";
            echo "URL: " . $this->encoder->encodeForURL($input) . "\n";
            echo "Attribute: " . $this->encoder->encodeForAttribute($input) . "\n\n";
        }
    }
    
    public function demonstrateXSSProtection(): void {
        echo "XSS Protection Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $xssAttempts = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(\'XSS\')">',
            '<a href="javascript:alert(\'XSS\')">Click me</a>',
            '<div style="background:url(javascript:alert(\'XSS\'))">XSS</div>',
            '<svg onload="alert(\'XSS\')"></svg>'
        ];
        
        foreach ($xssAttempts as $attempt) {
            echo "XSS Attempt: " . substr($attempt, 0, 50) . "...\n";
            echo "Detected: " . ($this->xssProtection->detectXSS($attempt) ? 'YES' : 'NO') . "\n";
            echo "Cleaned: " . substr($this->xssProtection->cleanHTML($attempt), 0, 50) . "...\n\n";
        }
        
        echo "HTML Cleaning Example:\n";
        $dirtyHTML = '<div class="user-content"><script>alert("bad")</script><p>Hello <strong>World</strong>!</p><img src="x" onerror="alert(\'xss\')"></div>';
        echo "Original: $dirtyHTML\n";
        echo "Cleaned: " . $this->xssProtection->cleanHTML($dirtyHTML) . "\n";
    }
    
    public function demonstrateSQLInjectionProtection(): void {
        echo "\nSQL Injection Protection Examples\n";
        echo str_repeat("-", 40) . "\n";
        
        $maliciousInputs = [
            "admin' --",
            "admin' OR '1'='1",
            "admin'; DROP TABLE users; --",
            "1' UNION SELECT username, password FROM users --",
            "'; UPDATE users SET password='hacked' WHERE id=1; --"
        ];
        
        foreach ($maliciousInputs as $input) {
            echo "Malicious input: $input\n";
            echo "SQL injection detected: " . ($this->sqlProtection->detectSQLInjection($input) ? 'YES' : 'NO') . "\n\n";
        }
        
        echo "Secure query example:\n";
        $conditions = [
            'username' => 'admin',
            'status' => 'active'
        ];
        
        $whereClause = $this->sqlProtection->buildWhereClause($conditions);
        echo "WHERE clause: " . $whereClause['where'] . "\n";
        echo "Parameters: " . json_encode($whereClause['params']) . "\n";
    }
    
    public function demonstrateCSRFProtection(): void {
        echo "\nCSRF Protection Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Generated CSRF token: " . $this->csrfProtection->getToken() . "\n";
        echo "Hidden field: " . $this->csrfProtection->generateHiddenField() . "\n";
        echo "Meta tag: " . $this->csrfProtection->generateMetaTag() . "\n";
        
        // Simulate token validation
        $validToken = $this->csrfProtection->getToken();
        $invalidToken = 'invalid_token_12345';
        
        echo "\nToken validation:\n";
        echo "Valid token: " . ($this->csrfProtection->validateToken($validToken) ? 'VALID' : 'INVALID') . "\n";
        echo "Invalid token: " . ($this->csrfProtection->validateToken($invalidToken) ? 'VALID' : 'INVALID') . "\n";
        
        // Regenerate token
        $oldToken = $this->csrfProtection->getToken();
        $this->csrfProtection->regenerateToken();
        $newToken = $this->csrfProtection->getToken();
        
        echo "\nToken regeneration:\n";
        echo "Old token: $oldToken\n";
        echo "New token: $newToken\n";
        echo "Old token valid: " . ($this->csrfProtection->validateToken($oldToken) ? 'VALID' : 'INVALID') . "\n";
    }
    
    public function demonstrateComprehensiveSecurity(): void {
        echo "\nComprehensive Security Example\n";
        echo str_repeat("-", 35) . "\n";
        
        // Simulate form submission
        $_POST = [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'bio' => '<script>alert("xss")</script>I love coding!',
            'csrf_token' => $this->csrfProtection->getToken()
        ];
        
        echo "Processing user profile update:\n";
        
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->csrfProtection->validateToken($csrfToken)) {
            echo "❌ CSRF validation failed\n";
            return;
        }
        echo "✅ CSRF validation passed\n";
        
        // Validate input
        $validated = $this->validator->validate($_POST, ['username', 'email', 'bio']);
        
        if ($this->validator->hasErrors()) {
            echo "❌ Input validation failed:\n";
            foreach ($this->validator->getErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    echo "  $field: $error\n";
                }
            }
            return;
        }
        echo "✅ Input validation passed\n";
        
        // Clean and encode output
        $cleanBio = $this->xssProtection->cleanHTML($validated['bio']);
        $safeBio = $this->encoder->encodeForHTML($cleanBio);
        
        echo "✅ Content cleaned and encoded\n";
        echo "Safe bio output: $safeBio\n";
        
        // Simulate database operation
        $username = $validated['username'];
        $email = $validated['email'];
        
        if ($this->sqlProtection->detectSQLInjection($username) || $this->sqlProtection->detectSQLInjection($email)) {
            echo "❌ SQL injection detected\n";
            return;
        }
        
        echo "✅ SQL injection check passed\n";
        echo "✅ User profile updated safely\n";
    }
    
    public function runAllExamples(): void {
        echo "Input Validation and Security Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateInputValidation();
        $this->demonstrateOutputEncoding();
        $this->demonstrateXSSProtection();
        $this->demonstrateSQLInjectionProtection();
        $this->demonstrateCSRFProtection();
        $this->demonstrateComprehensiveSecurity();
    }
}

// Input Validation Best Practices
function printInputValidationBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Input Validation Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Validation Principles:\n";
    echo "   • Never trust user input\n";
    echo "   • Validate on the server side\n";
    echo "   • Use whitelist approach\n";
    echo "   • Validate all input types\n\n";
    
    echo "2. Output Encoding:\n";
    echo "   • Context-aware encoding\n";
    echo "   • HTML: htmlspecialchars()\n";
    echo "   • JavaScript: json_encode()\n";
    echo "   • CSS: htmlspecialchars()\n";
    echo "   • URL: urlencode()\n\n";
    
    echo "3. XSS Prevention:\n";
    echo "   • Escape all output\n";
    echo "   • Use Content Security Policy\n";
    echo "   • Sanitize HTML content\n";
    echo "   • Validate and sanitize uploads\n\n";
    
    echo "4. SQL Injection Prevention:\n";
    echo "   • Use prepared statements\n";
    echo "   • Parameterized queries\n";
    echo "   • Escape identifiers properly\n";
    echo "   • Use ORM when possible\n\n";
    
    echo "5. CSRF Protection:\n";
    echo "   • Use CSRF tokens\n";
    echo "   • Validate tokens on server\n";
    echo "   • Use SameSite cookies\n";
    echo "   • Implement referrer checking\n\n";
    
    echo "6. Additional Measures:\n";
    echo "   • Implement rate limiting\n";
    echo "   • Use CAPTCHA when needed\n";
    echo "   • Log security events\n";
    echo "   • Regular security audits";
}

// Main execution
function runInputValidationDemo(): void {
    $examples = new InputValidationExamples();
    $examples->runAllExamples();
    printInputValidationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runInputValidationDemo();
}
?>
