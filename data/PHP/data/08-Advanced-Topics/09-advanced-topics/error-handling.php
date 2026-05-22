<?php
/**
 * Advanced Error Handling and Logging
 * 
 * This file demonstrates comprehensive error handling strategies,
 * custom exception classes, logging mechanisms, and debugging techniques.
 */

// Custom Exception Classes
class ValidationException extends Exception {
    private array $errors;
    
    public function __construct(array $errors, string $message = "Validation failed", int $code = 0, Throwable $previous = null) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

class DatabaseException extends Exception {
    private string $sql;
    private array $params;
    
    public function __construct(string $message, string $sql = "", array $params = [], int $code = 0, Throwable $previous = null) {
        $this->sql = $sql;
        $this->params = $params;
        parent::__construct($message, $code, $previous);
    }
    
    public function getSql(): string {
        return $this->sql;
    }
    
    public function getParams(): array {
        return $this->params;
    }
}

class ApiException extends Exception {
    private int $httpCode;
    private array $details;
    
    public function __construct(string $message, int $httpCode = 500, array $details = [], int $code = 0, Throwable $previous = null) {
        $this->httpCode = $httpCode;
        $this->details = $details;
        parent::__construct($message, $code, $previous);
    }
    
    public function getHttpCode(): int {
        return $this->httpCode;
    }
    
    public function getDetails(): array {
        return $this->details;
    }
}

// Logger Interface and Implementation
interface LoggerInterface {
    public function emergency(string $message, array $context = []): void;
    public function alert(string $message, array $context = []): void;
    public function critical(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function notice(string $message, array $context = []): void;
    public function info(string $message, array $context = []): void;
    public function debug(string $message, array $context = []): void;
    public function log(string $level, string $message, array $context = []): void;
}

class FileLogger implements LoggerInterface {
    private string $logFile;
    private array $context = [];
    
    public function __construct(string $logFile = 'app.log') {
        $this->logFile = $logFile;
    }
    
    public function emergency(string $message, array $context = []): void {
        $this->log('EMERGENCY', $message, $context);
    }
    
    public function alert(string $message, array $context = []): void {
        $this->log('ALERT', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void {
        $this->log('CRITICAL', $message, $context);
    }
    
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }
    
    public function notice(string $message, array $context = []): void {
        $this->log('NOTICE', $message, $context);
    }
    
    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }
    
    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }
    
    public function log(string $level, string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[$timestamp] $level: $message$contextStr" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

class DatabaseLogger implements LoggerInterface {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->createTable();
    }
    
    private function createTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            level VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            context JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }
    
    private function log(string $level, string $message, array $context = []): void {
        $sql = "INSERT INTO logs (level, message, context) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$level, $message, json_encode($context)]);
    }
    
    public function emergency(string $message, array $context = []): void {
        $this->log('EMERGENCY', $message, $context);
    }
    
    public function alert(string $message, array $context = []): void {
        $this->log('ALERT', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void {
        $this->log('CRITICAL', $message, $context);
    }
    
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }
    
    public function notice(string $message, array $context = []): void {
        $this->log('NOTICE', $message, $context);
    }
    
    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }
    
    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }
    
    public function log(string $level, string $message, array $context = []): void {
        $this->log($level, $message, $context);
    }
}

// Error Handler Class
class ErrorHandler {
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function register(): void {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    public function handleError(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED'
        ];
        
        $level = $errorTypes[$errno] ?? 'UNKNOWN';
        $message = "$errstr in $errfile on line $errline";
        
        $this->logger->error($message, [
            'type' => $level,
            'file' => $errfile,
            'line' => $errline,
            'errno' => $errno
        ]);
        
        return true;
    }
    
    public function handleException(Throwable $exception): void {
        $this->logger->critical($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'class' => get_class($exception)
        ]);
        
        // Display user-friendly error page
        $this->displayErrorPage($exception);
    }
    
    public function handleShutdown(): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            $this->logger->critical($error['message'], [
                'type' => 'FATAL',
                'file' => $error['file'],
                'line' => $error['line']
            ]);
        }
    }
    
    private function displayErrorPage(Throwable $exception): void {
        if (ini_get('display_errors')) {
            echo "<h1>Application Error</h1>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
            echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
            
            if ($exception instanceof ValidationException) {
                echo "<h3>Validation Errors:</h3>";
                echo "<ul>";
                foreach ($exception->getErrors() as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<h1>Something went wrong</h1>";
            echo "<p>We're sorry, but something went wrong. Please try again later.</p>";
        }
    }
}

// Validation Class
class Validator {
    private array $errors = [];
    
    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule => $ruleValue) {
                if (!$this->validateRule($field, $value, $rule, $ruleValue)) {
                    break; // Stop validation on first error for this field
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function validateRule(string $field, $value, string $rule, $ruleValue): bool {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = "$field is required";
                    return false;
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$field must be a valid email";
                    return false;
                }
                break;
                
            case 'min':
                if (strlen($value) < $ruleValue) {
                    $this->errors[$field][] = "$field must be at least $ruleValue characters";
                    return false;
                }
                break;
                
            case 'max':
                if (strlen($value) > $ruleValue) {
                    $this->errors[$field][] = "$field must not exceed $ruleValue characters";
                    return false;
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "$field must be numeric";
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

// Usage Examples
echo "=== Advanced Error Handling Demo ===\n\n";

// Initialize logger and error handler
$fileLogger = new FileLogger('error_demo.log');
$errorHandler = new ErrorHandler($fileLogger);
$errorHandler->register();

// 1. Custom Exceptions
echo "1. Custom Exceptions:\n";
try {
    throw new ValidationException(['name' => 'Name is required', 'email' => 'Email is invalid']);
} catch (ValidationException $e) {
    echo "Validation Exception: " . $e->getMessage() . "\n";
    echo "Errors: " . implode(', ', $e->getErrors()) . "\n";
}

try {
    throw new DatabaseException("Connection failed", "SELECT * FROM users", [], 500);
} catch (DatabaseException $e) {
    echo "Database Exception: " . $e->getMessage() . "\n";
    echo "SQL: " . $e->getSql() . "\n";
}

try {
    throw new ApiException("User not found", 404, ['user_id' => 123]);
} catch (ApiException $e) {
    echo "API Exception: " . $e->getMessage() . "\n";
    echo "HTTP Code: " . $e->getHttpCode() . "\n";
    echo "Details: " . json_encode($e->getDetails()) . "\n";
}
echo "\n";

// 2. Logging
echo "2. Logging:\n";
$fileLogger->info('Application started');
$fileLogger->debug('Debug information', ['user_id' => 123, 'action' => 'login']);
$fileLogger->warning('Deprecated function used', ['function' => 'old_function']);
$fileLogger->error('Database connection failed', ['host' => 'localhost', 'port' => 3306]);
echo "Check error_demo.log for logged messages\n\n";

// 3. Validation
echo "3. Validation:\n";
$validator = new Validator();
$data = [
    'name' => '',
    'email' => 'invalid-email',
    'age' => 'abc'
];

$rules = [
    'name' => ['required', 'min' => 2],
    'email' => ['required', 'email'],
    'age' => ['numeric']
];

if (!$validator->validate($data, $rules)) {
    echo "Validation failed:\n";
    foreach ($validator->getErrors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }
}
echo "\n";

// 4. Error Handling
echo "4. Error Handling:\n";
try {
    // Simulate various error conditions
    $result = 10 / 0; // This will trigger a warning
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

// Trigger a user error
trigger_error("This is a user warning", E_USER_WARNING);

// Test validation with exception
try {
    $validator->validate(['email' => ''], ['email' => ['required', 'email']]);
    if (!empty($validator->getErrors())) {
        throw new ValidationException($validator->getErrors());
    }
} catch (ValidationException $e) {
    echo "Caught validation exception: " . $e->getMessage() . "\n";
}

echo "\n=== Advanced Error Handling Demo Complete ===\n";
?>
