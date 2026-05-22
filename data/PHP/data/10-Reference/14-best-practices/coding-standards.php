<?php
/**
 * PHP Coding Standards and Conventions
 * 
 * This file demonstrates PSR standards, coding conventions,
 * formatting rules, and best practices for writing clean PHP code.
 */

// PSR-1: Basic Coding Standard
namespace App\Services;

use App\Models\User;
use Psr\Log\LoggerInterface;

/**
 * User service class following PSR-1 standards
 *
 * @package App\Services
 * @author  John Doe <john@example.com>
 * @version 1.0.0
 * @since   1.0.0
 */
class UserService
{
    private LoggerInterface $logger;
    private UserRepository $repository;
    
    /**
     * Constructor with dependency injection
     *
     * @param LoggerInterface $logger     Logger instance
     * @param UserRepository  $repository User repository
     */
    public function __construct(LoggerInterface $logger, UserRepository $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }
    
    /**
     * Create a new user
     *
     * @param array $userData User data array
     *
     * @return User Created user entity
     *
     * @throws InvalidArgumentException When user data is invalid
     * @throws RuntimeException When user creation fails
     */
    public function createUser(array $userData): User
    {
        $this->validateUserData($userData);
        
        try {
            $user = $this->repository->create($userData);
            $this->logger->info('User created successfully', ['user_id' => $user->getId()]);
            
            return $user;
        } catch (\Exception $e) {
            $this->logger->error('Failed to create user', ['error' => $e->getMessage()]);
            throw new RuntimeException('Failed to create user', 0, $e);
        }
    }
    
    /**
     * Validate user data
     *
     * @param array $userData User data to validate
     *
     * @throws InvalidArgumentException When validation fails
     */
    private function validateUserData(array $userData): void
    {
        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Email is required');
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        
        if (empty($userData['name']) || strlen($userData['name']) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters');
        }
    }
}

// PSR-2: Coding Style Guide
class CodingStyleExample
{
    // Constants should be all uppercase with underscores
    public const MAX_LOGIN_ATTEMPTS = 5;
    public const DEFAULT_TIMEOUT = 30;
    
    // Properties should have proper visibility and naming
    private string $username;
    protected ?int $userId = null;
    public array $permissions = [];
    
    // Method names should be camelCase
    public function processUserData(array $userData): bool
    {
        // Use 4 spaces for indentation
        if (empty($userData)) {
            return false;
        }
        
        // Control structures should have one space after keywords
        if (isset($userData['email']) && filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->username = $userData['email'];
            
            // Method chaining should be on multiple lines
            $this->logger
                ->info('Processing user data')
                ->withContext(['user' => $userData['email']]);
            
            return true;
        }
        
        return false;
    }
    
    // Method arguments with default values should be at the end
    public function formatUserName(
        string $firstName,
        string $lastName,
        bool $includeMiddle = false,
        ?string $middleName = null
    ): string {
        if ($includeMiddle && $middleName !== null) {
            return sprintf('%s %s %s', $firstName, $middleName, $lastName);
        }
        
        return sprintf('%s %s', $firstName, $lastName);
    }
    
    // Array declaration should be multi-line for complex arrays
    public function getUserConfig(): array
    {
        return [
            'timeout' => self::DEFAULT_TIMEOUT,
            'retry_attempts' => 3,
            'features' => [
                'notifications' => true,
                'dark_mode' => false,
                'auto_save' => true,
            ],
            'limits' => [
                'max_file_size' => 10485760,
                'max_connections' => 10,
            ],
        ];
    }
}

// PSR-4: Autoloading Standard
namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;

/**
 * User controller demonstrating PSR-4 autoloading
 */
class UserController
{
    private UserService $userService;
    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function store(CreateUserRequest $request): UserResource
    {
        $userData = $request->validated();
        $user = $this->userService->createUser($userData);
        
        return new UserResource($user);
    }
}

// PSR-7: HTTP Message Interface
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Psr7Example
{
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        // Get request method and URI
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        // Get headers
        $contentType = $request->getHeaderLine('Content-Type');
        $userAgent = $request->getHeaderLine('User-Agent');
        
        // Get body
        $body = $request->getBody();
        $content = $body->getContents();
        
        // Create response
        $response = new Response();
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        $responseData = [
            'method' => $method,
            'uri' => (string) $uri,
            'content_type' => $contentType,
            'user_agent' => $userAgent,
            'body_size' => $body->getSize(),
        ];
        
        $response->getBody()->write(json_encode($responseData));
        
        return $response;
    }
}

// PSR-12: Extended Coding Style Guide
class Psr12Example
{
    // Use proper spacing around binary operators
    public const DEFAULT_LIMIT = 100;
    public const MAX_ATTEMPTS = 5;
    
    // Use proper spacing around assignment operators
    private string $name = '';
    private ?int $age = null;
    private array $hobbies = [];
    
    // Method declaration with proper spacing
    public function calculateAgeInDays(int $years): int
    {
        // Use consistent spacing around operators
        return $years * 365 + ($years / 4);
    }
    
    // Use proper spacing in array declarations
    public function getPersonalInfo(): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
            'hobbies' => $this->hobbies,
            'age_in_days' => $this->age !== null ? $this->calculateAgeInDays($this->age) : 0,
        ];
    }
    
    // Use proper spacing in conditional statements
    public function isAdult(): bool
    {
        return $this->age !== null && $this->age >= 18;
    }
    
    // Use proper spacing in method calls
    public function addHobby(string $hobby): void
    {
        if (!in_array($hobby, $this->hobbies, true)) {
            $this->hobbies[] = $hobby;
        }
    }
}

// Naming Conventions
class NamingConventions
{
    // Class names should be PascalCase
    public const CLASS_NAME_EXAMPLE = 'example';
    
    // Constants should be UPPER_SNAKE_CASE
    private const MAX_RETRY_ATTEMPTS = 3;
    private const DEFAULT_TIMEOUT = 30;
    
    // Properties should be camelCase
    private string $firstName = '';
    private string $lastName = '';
    private ?DateTime $birthDate = null;
    
    // Methods should be camelCase
    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
    
    // Boolean methods should start with is/has/can
    public function hasBirthDate(): bool
    {
        return $this->birthDate !== null;
    }
    
    public function canVote(): bool
    {
        return $this->hasBirthDate() && $this->getAge() >= 18;
    }
    
    // Getter methods should start with get
    public function getAge(): ?int
    {
        if ($this->birthDate === null) {
            return null;
        }
        
        return $this->birthDate->diff(new DateTime())->y;
    }
    
    // Setter methods should start with set
    public function setBirthDate(DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
    
    // Private methods can be more descriptive
    private function calculateDaysUntilBirthday(): int
    {
        if ($this->birthDate === null) {
            return 0;
        }
        
        $today = new DateTime();
        $birthday = new DateTime($today->format('Y') . '-' . $this->birthDate->format('m-d'));
        
        if ($birthday < $today) {
            $birthday->modify('+1 year');
        }
        
        return $today->diff($birthday)->days;
    }
}

// Documentation Standards
/**
 * Calculator class demonstrating proper documentation
 *
 * This class provides basic arithmetic operations with proper
 * error handling and validation.
 *
 * @package App\Math
 * @author  John Doe <john@example.com>
 * @version 1.0.0
 * @since   1.0.0
 *
 * @example
 * $calculator = new Calculator();
 * $result = $calculator->add(5, 3);
 * echo $result; // Outputs: 8
 */
class Calculator
{
    /**
     * Current precision for decimal operations
     *
     * @var int Number of decimal places
     */
    private int $precision = 2;
    
    /**
     * Operation history
     *
     * @var array<string, float> History of operations
     */
    private array $history = [];
    
    /**
     * Constructor
     *
     * @param int $precision Number of decimal places for calculations
     *
     * @throws InvalidArgumentException When precision is negative
     */
    public function __construct(int $precision = 2)
    {
        if ($precision < 0) {
            throw new InvalidArgumentException('Precision must be non-negative');
        }
        
        $this->precision = $precision;
    }
    
    /**
     * Add two numbers
     *
     * Performs addition with the specified precision and logs the operation.
     *
     * @param float $a First operand
     * @param float $b Second operand
     *
     * @return float The sum of $a and $b
     *
     * @throws InvalidArgumentException When operands are not numeric
     *
     * @see subtract() For subtraction operation
     * @see multiply() For multiplication operation
     * @see divide() For division operation
     */
    public function add(float $a, float $b): float
    {
        $this->validateNumeric($a);
        $this->validateNumeric($b);
        
        $result = round($a + $b, $this->precision);
        $this->logOperation('add', $a, $b, $result);
        
        return $result;
    }
    
    /**
     * Subtract two numbers
     *
     * @param float $a Minuend
     * @param float $b Subtrahend
     *
     * @return float The difference of $a and $b
     *
     * @throws InvalidArgumentException When operands are not numeric
     */
    public function subtract(float $a, float $b): float
    {
        $this->validateNumeric($a);
        $this->validateNumeric($b);
        
        $result = round($a - $b, $this->precision);
        $this->logOperation('subtract', $a, $b, $result);
        
        return $result;
    }
    
    /**
     * Multiply two numbers
     *
     * @param float $a Multiplicand
     * @param float $b Multiplier
     *
     * @return float The product of $a and $b
     *
     * @throws InvalidArgumentException When operands are not numeric
     */
    public function multiply(float $a, float $b): float
    {
        $this->validateNumeric($a);
        $this->validateNumeric($b);
        
        $result = round($a * $b, $this->precision);
        $this->logOperation('multiply', $a, $b, $result);
        
        return $result;
    }
    
    /**
     * Divide two numbers
     *
     * @param float $a Dividend
     * @param float $b Divisor
     *
     * @return float The quotient of $a and $b
     *
     * @throws InvalidArgumentException When operands are not numeric
     * @throws DivisionByZeroError When divisor is zero
     */
    public function divide(float $a, float $b): float
    {
        $this->validateNumeric($a);
        $this->validateNumeric($b);
        
        if ($b == 0) {
            throw new DivisionByZeroError('Division by zero');
        }
        
        $result = round($a / $b, $this->precision);
        $this->logOperation('divide', $a, $b, $result);
        
        return $result;
    }
    
    /**
     * Get operation history
     *
     * Returns an array of all operations performed with this calculator.
     *
     * @return array<string, float> Operation history
     */
    public function getHistory(): array
    {
        return $this->history;
    }
    
    /**
     * Clear operation history
     *
     * Removes all entries from the operation history.
     *
     * @return void
     */
    public function clearHistory(): void
    {
        $this->history = [];
    }
    
    /**
     * Validate numeric input
     *
     * @param mixed $value Value to validate
     *
     * @throws InvalidArgumentException When value is not numeric
     */
    private function validateNumeric($value): void
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Value must be numeric');
        }
    }
    
    /**
     * Log operation to history
     *
     * @param string $operation Operation type
     * @param float  $a         First operand
     * @param float  $b         Second operand
     * @param float  $result    Operation result
     *
     * @return void
     */
    private function logOperation(string $operation, float $a, float $b, float $result): void
    {
        $this->history[] = [
            'operation' => $operation,
            'operands' => [$a, $b],
            'result' => $result,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}

// Code Organization
namespace App\Utilities;

/**
 * String utility class
 */
class StringUtils
{
    /**
     * Convert string to snake_case
     *
     * @param string $string Input string
     *
     * @return string Snake case string
     */
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $string));
    }
    
    /**
     * Convert string to camelCase
     *
     * @param string $string Input string
     *
     * @return string Camel case string
     */
    public static function toCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }
    
    /**
     * Truncate string to specified length
     *
     * @param string $string    String to truncate
     * @param int    $length    Maximum length
     * @param string $suffix    Suffix to add if truncated
     *
     * @return string Truncated string
     */
    public static function truncate(string $string, int $length, string $suffix = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        
        return substr($string, 0, $length - strlen($suffix)) . $suffix;
    }
}

// Error Handling Standards
class ErrorHandler
{
    /**
     * Handle exceptions with proper logging
     *
     * @param \Throwable $exception Exception to handle
     *
     * @return void
     */
    public function handleException(\Throwable $exception): void
    {
        // Log the exception
        error_log(sprintf(
            '[%s] %s: %s in %s on line %d',
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
        
        // Handle different exception types
        if ($exception instanceof \InvalidArgumentException) {
            $this->handleInvalidArgumentException($exception);
        } elseif ($exception instanceof \RuntimeException) {
            $this->handleRuntimeException($exception);
        } else {
            $this->handleGenericException($exception);
        }
    }
    
    /**
     * Handle invalid argument exceptions
     *
     * @param \InvalidArgumentException $exception Exception to handle
     *
     * @return void
     */
    private function handleInvalidArgumentException(\InvalidArgumentException $exception): void
    {
        // Log with appropriate level
        error_log('Invalid argument: ' . $exception->getMessage());
        
        // Return user-friendly error
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid request',
            'message' => $exception->getMessage()
        ]);
    }
    
    /**
     * Handle runtime exceptions
     *
     * @param \RuntimeException $exception Exception to handle
     *
     * @return void
     */
    private function handleRuntimeException(\RuntimeException $exception): void
    {
        // Log with appropriate level
        error_log('Runtime error: ' . $exception->getMessage());
        
        // Return user-friendly error
        http_response_code(500);
        echo json_encode([
            'error' => 'Server error',
            'message' => 'An error occurred while processing your request'
        ]);
    }
    
    /**
     * Handle generic exceptions
     *
     * @param \Throwable $exception Exception to handle
     *
     * @return void
     */
    private function handleGenericException(\Throwable $exception): void
    {
        // Log with appropriate level
        error_log('Unexpected error: ' . $exception->getMessage());
        
        // Return user-friendly error
        http_response_code(500);
        echo json_encode([
            'error' => 'Server error',
            'message' => 'An unexpected error occurred'
        ]);
    }
}

// Coding Standards Examples
class CodingStandardsExamples
{
    private Calculator $calculator;
    private ErrorHandler $errorHandler;
    
    public function __construct()
    {
        $this->calculator = new Calculator(2);
        $this->errorHandler = new ErrorHandler();
    }
    
    public function demonstrateNamingConventions(): void
    {
        echo "Naming Conventions Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Class names (PascalCase)
        $userService = new UserService();
        $calculator = new Calculator();
        $errorHandler = new ErrorHandler();
        
        // Method names (camelCase)
        $fullName = $userService->getFullName();
        $result = $calculator->add(5, 3);
        
        // Variable names (camelCase)
        $maxRetryAttempts = 5;
        $defaultTimeout = 30;
        $currentUser = null;
        
        // Constants (UPPER_SNAKE_CASE)
        echo "MAX_RETRY_ATTEMPTS: " . UserService::MAX_LOGIN_ATTEMPTS . "\n";
        echo "DEFAULT_TIMEOUT: " . Calculator::DEFAULT_TIMEOUT . "\n";
        
        // Boolean methods (is/has/can)
        $isAdult = $userService->isAdult();
        $hasBirthDate = $userService->hasBirthDate();
        $canVote = $userService->canVote();
        
        echo "Boolean methods:\n";
        echo "  isAdult: " . ($isAdult ? 'true' : 'false') . "\n";
        echo "  hasBirthDate: " . ($hasBirthDate ? 'true' : 'false') . "\n";
        echo "  canVote: " . ($canVote ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateCodeStyle(): void
    {
        echo "\nCode Style Examples\n";
        echo str_repeat("-", 20) . "\n";
        
        // Proper spacing and indentation
        $numbers = [1, 2, 3, 4, 5];
        $sum = 0;
        
        foreach ($numbers as $number) {
            $sum += $number;
        }
        
        echo "Sum of numbers: $sum\n";
        
        // Proper conditional formatting
        if ($sum > 10) {
            echo "Sum is greater than 10\n";
        } elseif ($sum > 5) {
            echo "Sum is greater than 5\n";
        } else {
            echo "Sum is 5 or less\n";
        }
        
        // Proper array formatting
        $config = [
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'name' => 'myapp',
            ],
            'cache' => [
                'driver' => 'redis',
                'ttl' => 3600,
            ],
        ];
        
        echo "Database host: " . $config['database']['host'] . "\n";
        echo "Cache TTL: " . $config['cache']['ttl'] . "\n";
    }
    
    public function demonstrateDocumentation(): void
    {
        echo "\nDocumentation Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Using documented methods
        $result = $this->calculator->add(10.5, 5.25);
        echo "Addition result: $result\n";
        
        $result = $this->calculator->subtract(10, 3);
        echo "Subtraction result: $result\n";
        
        $result = $this->calculator->multiply(4, 2.5);
        echo "Multiplication result: $result\n";
        
        $result = $this->calculator->divide(10, 2);
        echo "Division result: $result\n";
        
        // Get operation history
        $history = $this->calculator->getHistory();
        echo "Operation count: " . count($history) . "\n";
        
        // Clear history
        $this->calculator->clearHistory();
        echo "History cleared\n";
    }
    
    public function demonstrateStringUtilities(): void
    {
        echo "\nString Utility Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // String case conversion
        $camelCase = 'userName';
        $snakeCase = StringUtils::toSnakeCase($camelCase);
        echo "Camel to snake: $camelCase -> $snakeCase\n";
        
        $snakeCase = 'user_name';
        $camelCase = StringUtils::toCamelCase($snakeCase);
        echo "Snake to camel: $snakeCase -> $camelCase\n";
        
        // String truncation
        $longString = 'This is a very long string that needs to be truncated';
        $truncated = StringUtils::truncate($longString, 20);
        echo "Truncated: $truncated\n";
        
        $truncatedWithSuffix = StringUtils::truncate($longString, 20, ' [more]');
        echo "Truncated with suffix: $truncatedWithSuffix\n";
    }
    
    public function demonstrateErrorHandling(): void
    {
        echo "\nError Handling Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        try {
            // This will throw an exception
            $result = $this->calculator->divide(10, 0);
        } catch (\DivisionByZeroError $e) {
            $this->errorHandler->handleException($e);
        }
        
        try {
            // This will throw an exception
            $result = $this->calculator->divide('invalid', 5);
        } catch (\InvalidArgumentException $e) {
            $this->errorHandler->handleException($e);
        }
        
        try {
            // Simulate a runtime error
            throw new \RuntimeException('Simulated runtime error');
        } catch (\RuntimeException $e) {
            $this->errorHandler->handleException($e);
        }
    }
    
    public function demonstrateCodeOrganization(): void
    {
        echo "\nCode Organization Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Demonstrate proper namespace usage
        $calculator = new \App\Math\Calculator();
        $stringUtils = new \App\Utilities\StringUtils();
        
        echo "Calculator created from namespace: " . get_class($calculator) . "\n";
        echo "String utils created from namespace: " . get_class($stringUtils) . "\n";
        
        // Demonstrate proper autoloading
        $reflection = new \ReflectionClass(\App\Services\UserService::class);
        echo "UserService file: " . $reflection->getFileName() . "\n";
        echo "UserService namespace: " . $reflection->getNamespaceName() . "\n";
    }
    
    public function runAllExamples(): void
    {
        echo "PHP Coding Standards Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateNamingConventions();
        $this->demonstrateCodeStyle();
        $this->demonstrateDocumentation();
        $this->demonstrateStringUtilities();
        $this->demonstrateErrorHandling();
        $this->demonstrateCodeOrganization();
    }
}

// Coding Standards Best Practices
function printCodingStandardsBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "PHP Coding Standards Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. PSR Standards:\n";
    echo "   • Follow PSR-1 basic coding standard\n";
    echo "   • Apply PSR-2 coding style guide\n";
    echo "   • Use PSR-4 autoloading standard\n";
    echo "   • Implement PSR-7 HTTP message interface\n";
    echo "   • Follow PSR-12 extended coding style\n\n";
    
    echo "2. Naming Conventions:\n";
    echo "   • Use PascalCase for class names\n";
    echo "   • Use camelCase for method and variable names\n";
    echo "   • Use UPPER_SNAKE_CASE for constants\n";
    echo "   • Start boolean methods with is/has/can\n";
    echo "   • Use descriptive and meaningful names\n\n";
    
    echo "3. Code Formatting:\n";
    echo "   • Use 4 spaces for indentation\n";
    echo "   • Add spaces around operators\n";
    echo "   • Format arrays consistently\n";
    echo "   • Use proper line breaks\n";
    echo "   • Keep lines under 120 characters\n\n";
    
    echo "4. Documentation:\n";
    echo "   • Document all public methods\n";
    echo "   • Use proper PHPDoc tags\n";
    echo "   • Include parameter and return types\n";
    echo "   • Add examples in documentation\n";
    echo "   • Document exceptions thrown\n\n";
    
    echo "5. Error Handling:\n";
    echo "   • Use specific exception types\n";
    echo "   • Handle exceptions appropriately\n";
    echo "   • Log errors with proper context\n";
    echo "   • Provide user-friendly error messages\n";
    echo "   • Use try-catch blocks correctly\n\n";
    
    echo "6. Code Organization:\n";
    echo "   • Use proper namespaces\n";
    echo "   • Organize files logically\n";
    echo "   • Follow dependency injection principles\n";
    echo "   • Keep classes focused and small\n";
    echo "   • Use consistent file structure";
}

// Main execution
function runCodingStandardsDemo(): void
{
    $examples = new CodingStandardsExamples();
    $examples->runAllExamples();
    printCodingStandardsBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCodingStandardsDemo();
}
?>
