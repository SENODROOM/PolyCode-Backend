<?php
/**
 * Documentation Standards and Practices
 * 
 * This file demonstrates proper documentation standards,
 * API documentation, README files, and knowledge sharing practices.
 */

// PHPDoc Standards Example
/**
 * User Management Service
 *
 * This service handles user registration, authentication, and profile management.
 * It provides methods for creating, updating, and retrieving user information
 * while maintaining data integrity and security standards.
 *
 * @package App\Services
 * @author  John Doe <john@example.com>
 * @version 1.0.0
 * @since   1.0.0
 *
 * @example
 * $userService = new UserService($repository, $emailService);
 * $user = $userService->registerUser([
 *     'name' => 'John Doe',
 *     'email' => 'john@example.com',
 *     'password' => 'secure_password'
 * ]);
 *
 * @see UserRepository For data persistence operations
 * @see EmailService For email notifications
 * @link https://example.com/docs/user-service User Service Documentation
 */
class UserService
{
    /**
     * User repository instance
     *
     * @var UserRepository
     */
    private UserRepository $repository;
    
    /**
     * Email service instance
     *
     * @var EmailService
     */
    private EmailService $emailService;
    
    /**
     * Logger instance
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * Maximum allowed login attempts
     *
     * @var int
     */
    private const MAX_LOGIN_ATTEMPTS = 5;
    
    /**
     * Default user role
     *
     * @var string
     */
    private const DEFAULT_ROLE = 'user';
    
    /**
     * Constructor
     *
     * Initializes the UserService with required dependencies.
     *
     * @param UserRepository     $repository     User repository for data operations
     * @param EmailService        $emailService    Email service for notifications
     * @param LoggerInterface     $logger         Logger for error and info logging
     *
     * @throws RuntimeException When required dependencies are not available
     *
     * @since 1.0.0
     */
    public function __construct(
        UserRepository $repository,
        EmailService $emailService,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->emailService = $emailService;
        $this->logger = $logger;
        
        $this->logger->info('UserService initialized');
    }
    
    /**
     * Register a new user
     *
     * Creates a new user account with the provided data, validates the input,
     * saves the user to the database, and sends a welcome email.
     *
     * @param array $userData User registration data
     *   - name (string): User's full name (required, min 2 chars)
     *   - email (string): User's email address (required, valid email)
     *   - password (string): User's password (required, min 8 chars)
     *   - role (string|null): User's role (optional, defaults to 'user')
     *
     * @return User Created user entity with ID assigned
     *
     * @throws InvalidArgumentException When validation fails
     * @throws RuntimeException When user creation fails
     * @throws EmailException When welcome email fails to send
     *
     * @since 1.0.0
     *
     * @example
     * $userData = [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'password' => 'secure_password123'
     * ];
     * $user = $userService->registerUser($userData);
     * echo "User registered with ID: " . $user->getId();
     */
    public function registerUser(array $userData): User
    {
        $this->validateRegistrationData($userData);
        
        try {
            $user = $this->createUser($userData);
            $this->sendWelcomeEmail($user);
            $this->logUserRegistration($user);
            
            return $user;
        } catch (\Exception $e) {
            $this->logger->error('User registration failed', [
                'error' => $e->getMessage(),
                'data' => $userData
            ]);
            
            throw new RuntimeException('Failed to register user', 0, $e);
        }
    }
    
    /**
     * Authenticate user credentials
     *
     * Validates user credentials and returns user information if authentication succeeds.
     * Implements rate limiting to prevent brute force attacks.
     *
     * @param string $email    User's email address
     * @param string $password User's password
     *
     * @return User|null User entity if authentication succeeds, null otherwise
     *
     * @throws AuthenticationException When authentication fails due to invalid credentials
     * @throws RateLimitException When rate limit is exceeded
     *
     * @since 1.0.0
     *
     * @see PasswordHasher For password verification
     * @see RateLimiter For rate limiting implementation
     */
    public function authenticateUser(string $email, string $password): ?User
    {
        $this->checkRateLimit($email);
        
        $user = $this->repository->findByEmail($email);
        
        if ($user === null) {
            $this->incrementFailedAttempts($email);
            throw new AuthenticationException('Invalid credentials');
        }
        
        if (!$this->verifyPassword($password, $user->getPasswordHash())) {
            $this->incrementFailedAttempts($email);
            throw new AuthenticationException('Invalid credentials');
        }
        
        $this->clearFailedAttempts($email);
        $this->logSuccessfulLogin($user);
        
        return $user;
    }
    
    /**
     * Update user profile
     *
     * Updates user profile information with the provided data.
     * Only updates the fields that are provided in the $profileData array.
     *
     * @param int   $userId     User ID to update
     * @param array $profileData Profile data to update
     *   - name (string|null): User's name
     *   - email (string|null): User's email
     *   - phone (string|null): User's phone number
     *   - address (string|null): User's address
     *
     * @return bool True if update was successful, false otherwise
     *
     * @throws UserNotFoundException When user is not found
     * @throws ValidationException When profile data is invalid
     *
     * @since 1.1.0
     */
    public function updateProfile(int $userId, array $profileData): bool
    {
        $user = $this->repository->findById($userId);
        
        if ($user === null) {
            throw new UserNotFoundException("User with ID $userId not found");
        }
        
        $this->validateProfileData($profileData);
        
        try {
            $this->applyProfileUpdates($user, $profileData);
            $this->repository->save($user);
            
            $this->logProfileUpdate($user, $profileData);
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Profile update failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get user by ID
     *
     * Retrieves user information from the repository.
     *
     * @param int $userId User ID to retrieve
     *
     * @return User|null User entity if found, null otherwise
     *
     * @since 1.0.0
     */
    public function getUserById(int $userId): ?User
    {
        return $this->repository->findById($userId);
    }
    
    /**
     * Get user by email
     *
     * Retrieves user information by email address.
     *
     * @param string $email Email address to search for
     *
     * @return User|null User entity if found, null otherwise
     *
     * @since 1.0.0
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }
    
    /**
     * Delete user account
     *
     * Permanently deletes a user account and all associated data.
     * This action cannot be undone.
     *
     * @param int $userId User ID to delete
     *
     * @return bool True if deletion was successful, false otherwise
     *
     * @throws UserNotFoundException When user is not found
     * @throws DeleteRestrictionException When user cannot be deleted
     *
     * @since 1.2.0
     */
    public function deleteUser(int $userId): bool
    {
        $user = $this->repository->findById($userId);
        
        if ($user === null) {
            throw new UserNotFoundException("User with ID $userId not found");
        }
        
        if ($this->hasActiveOrders($userId)) {
            throw new DeleteRestrictionException('Cannot delete user with active orders');
        }
        
        try {
            $this->repository->delete($user);
            $this->logUserDeletion($user);
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('User deletion failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Change user password
     *
     * Updates user's password after validating the current password.
     *
     * @param int    $userId        User ID
     * @param string $currentPassword Current password
     * @param string $newPassword    New password
     *
     * @return bool True if password was changed successfully, false otherwise
     *
     * @throws UserNotFoundException When user is not found
     * @throws AuthenticationException When current password is invalid
     * @throws ValidationException When new password is invalid
     *
     * @since 1.1.0
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->repository->findById($userId);
        
        if ($user === null) {
            throw new UserNotFoundException("User with ID $userId not found");
        }
        
        if (!$this->verifyPassword($currentPassword, $user->getPasswordHash())) {
            throw new AuthenticationException('Current password is invalid');
        }
        
        $this->validatePassword($newPassword);
        
        try {
            $hashedPassword = $this->hashPassword($newPassword);
            $user->setPasswordHash($hashedPassword);
            
            $this->repository->save($user);
            $this->logPasswordChange($user);
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Password change failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    // Private methods with documentation
    
    /**
     * Validate user registration data
     *
     * @param array $userData Registration data to validate
     *
     * @throws InvalidArgumentException When validation fails
     */
    private function validateRegistrationData(array $userData): void
    {
        if (empty($userData['name'])) {
            throw new InvalidArgumentException('Name is required');
        }
        
        if (strlen($userData['name']) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters');
        }
        
        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Email is required');
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        
        if (empty($userData['password'])) {
            throw new InvalidArgumentException('Password is required');
        }
        
        if (strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }
        
        if ($this->repository->findByEmail($userData['email']) !== null) {
            throw new InvalidArgumentException('Email already exists');
        }
    }
    
    /**
     * Create user entity
     *
     * @param array $userData User data
     *
     * @return User Created user entity
     */
    private function createUser(array $userData): User
    {
        $user = new User();
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $user->setPasswordHash($this->hashPassword($userData['password']));
        $user->setRole($userData['role'] ?? self::DEFAULT_ROLE);
        $user->setStatus('active');
        $user->setCreatedAt(new \DateTime());
        
        $this->repository->save($user);
        
        return $user;
    }
    
    /**
     * Send welcome email to user
     *
     * @param User $user User to send email to
     *
     * @throws EmailException When email fails to send
     */
    private function sendWelcomeEmail(User $user): void
    {
        try {
            $this->emailService->sendWelcomeEmail($user);
        } catch (\Exception $e) {
            throw new EmailException('Failed to send welcome email: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Hash password using secure algorithm
     *
     * @param string $password Plain text password
     *
     * @return string Hashed password
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    /**
     * Verify password against hash
     *
     * @param string $password Plain text password
     * @param string $hash     Password hash
     *
     * @return bool True if password matches hash, false otherwise
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Check rate limit for login attempts
     *
     * @param string $email Email address to check
     *
     * @throws RateLimitException When rate limit is exceeded
     */
    private function checkRateLimit(string $email): void
    {
        $attempts = $this->getFailedAttempts($email);
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            throw new RateLimitException('Too many failed attempts. Please try again later.');
        }
    }
    
    /**
     * Get number of failed attempts for email
     *
     * @param string $email Email address
     *
     * @return int Number of failed attempts
     */
    private function getFailedAttempts(string $email): int
    {
        // Implementation would check cache/database for failed attempts
        return 0;
    }
    
    /**
     * Increment failed attempts counter
     *
     * @param string $email Email address
     */
    private function incrementFailedAttempts(string $email): void
    {
        // Implementation would increment counter in cache/database
    }
    
    /**
     * Clear failed attempts counter
     *
     * @param string $email Email address
     */
    private function clearFailedAttempts(string $email): void
    {
        // Implementation would clear counter in cache/database
    }
    
    /**
     * Check if user has active orders
     *
     * @param int $userId User ID
     *
     * @return bool True if user has active orders, false otherwise
     */
    private function hasActiveOrders(int $userId): bool
    {
        // Implementation would check order repository
        return false;
    }
    
    /**
     * Log user registration event
     *
     * @param User $user Registered user
     */
    private function logUserRegistration(User $user): void
    {
        $this->logger->info('User registered', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);
    }
    
    /**
     * Log successful login event
     *
     * @param User $user Logged in user
     */
    private function logSuccessfulLogin(User $user): void
    {
        $this->logger->info('User logged in', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }
    
    /**
     * Log profile update event
     *
     * @param User  $user        Updated user
     * @param array $profileData Updated profile data
     */
    private function logProfileUpdate(User $user, array $profileData): void
    {
        $this->logger->info('User profile updated', [
            'user_id' => $user->getId(),
            'updated_fields' => array_keys($profileData)
        ]);
    }
    
    /**
     * Log user deletion event
     *
     * @param User $user Deleted user
     */
    private function logUserDeletion(User $user): void
    {
        $this->logger->info('User deleted', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }
    
    /**
     * Log password change event
     *
     * @param User $user User whose password was changed
     */
    private function logPasswordChange(User $user): void
    {
        $this->logger->info('User password changed', [
            'user_id' => $user->getId()
        ]);
    }
    
    /**
     * Validate profile data
     *
     * @param array $profileData Profile data to validate
     *
     * @throws ValidationException When validation fails
     */
    private function validateProfileData(array $profileData): void
    {
        if (isset($profileData['name']) && strlen($profileData['name']) < 2) {
            throw new ValidationException('Name must be at least 2 characters');
        }
        
        if (isset($profileData['email']) && !filter_var($profileData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format');
        }
    }
    
    /**
     * Apply profile updates to user
     *
     * @param User  $user        User to update
     * @param array $profileData Profile data to apply
     */
    private function applyProfileUpdates(User $user, array $profileData): void
    {
        if (isset($profileData['name'])) {
            $user->setName($profileData['name']);
        }
        
        if (isset($profileData['email'])) {
            $user->setEmail($profileData['email']);
        }
        
        if (isset($profileData['phone'])) {
            $user->setPhone($profileData['phone']);
        }
        
        if (isset($profileData['address'])) {
            $user->setAddress($profileData['address']);
        }
    }
    
    /**
     * Validate password
     *
     * @param string $password Password to validate
     *
     * @throws ValidationException When password is invalid
     */
    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new ValidationException('Password must be at least 8 characters');
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            throw new ValidationException('Password must contain at least one uppercase letter');
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            throw new ValidationException('Password must contain at least one lowercase letter');
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            throw new ValidationException('Password must contain at least one number');
        }
    }
}

// API Documentation Generator
class ApiDocumentationGenerator
{
    private array $endpoints = [];
    private array $models = [];
    
    /**
     * Add API endpoint documentation
     *
     * @param string $method HTTP method
     * @param string $path   API path
     * @param array  $doc    Documentation array
     */
    public function addEndpoint(string $method, string $path, array $doc): void
    {
        $this->endpoints[strtoupper($method)][$path] = $doc;
    }
    
    /**
     * Add model documentation
     *
     * @param string $name Model name
     * @param array  $doc  Documentation array
     */
    public function addModel(string $name, array $doc): void
    {
        $this->models[$name] = $doc;
    }
    
    /**
     * Generate OpenAPI documentation
     *
     * @return array OpenAPI specification
     */
    public function generateOpenApi(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'User Management API',
                'version' => '1.0.0',
                'description' => 'API for user management operations',
                'contact' => [
                    'name' => 'API Support',
                    'email' => 'api@example.com'
                ]
            ],
            'servers' => [
                [
                    'url' => 'https://api.example.com/v1',
                    'description' => 'Production server'
                ],
                [
                    'url' => 'https://staging-api.example.com/v1',
                    'description' => 'Staging server'
                ]
            ],
            'paths' => $this->generatePaths(),
            'components' => [
                'schemas' => $this->generateSchemas()
            ]
        ];
    }
    
    /**
     * Generate API paths
     *
     * @return array Paths documentation
     */
    private function generatePaths(): array
    {
        $paths = [];
        
        foreach ($this->endpoints as $method => $methodEndpoints) {
            foreach ($methodEndpoints as $path => $doc) {
                if (!isset($paths[$path])) {
                    $paths[$path] = [];
                }
                
                $paths[$path][strtolower($method)] = $this->generateEndpointDoc($doc);
            }
        }
        
        return $paths;
    }
    
    /**
     * Generate endpoint documentation
     *
     * @param array $doc Endpoint documentation
     *
     * @return array Endpoint documentation in OpenAPI format
     */
    private function generateEndpointDoc(array $doc): array
    {
        $endpointDoc = [
            'summary' => $doc['summary'] ?? '',
            'description' => $doc['description'] ?? '',
            'operationId' => $doc['operationId'] ?? '',
            'tags' => $doc['tags'] ?? [],
            'responses' => $this->generateResponses($doc['responses'] ?? [])
        ];
        
        if (isset($doc['parameters'])) {
            $endpointDoc['parameters'] = $this->generateParameters($doc['parameters']);
        }
        
        if (isset($doc['requestBody'])) {
            $endpointDoc['requestBody'] = $this->generateRequestBody($doc['requestBody']);
        }
        
        return $endpointDoc;
    }
    
    /**
     * Generate responses documentation
     *
     * @param array $responses Responses array
     *
     * @return array Responses documentation
     */
    private function generateResponses(array $responses): array
    {
        $responseDocs = [];
        
        foreach ($responses as $code => $response) {
            $responseDocs[$code] = [
                'description' => $response['description'] ?? '',
                'content' => $this->generateContent($response['content'] ?? [])
            ];
        }
        
        return $responseDocs;
    }
    
    /**
     * Generate content documentation
     *
     * @param array $content Content array
     *
     * @return array Content documentation
     */
    private function generateContent(array $content): array
    {
        $contentDocs = [];
        
        foreach ($content as $mediaType => $schema) {
            $contentDocs[$mediaType] = [
                'schema' => $schema
            ];
        }
        
        return $contentDocs;
    }
    
    /**
     * Generate parameters documentation
     *
     * @param array $parameters Parameters array
     *
     * @return array Parameters documentation
     */
    private function generateParameters(array $parameters): array
    {
        $parameterDocs = [];
        
        foreach ($parameters as $param) {
            $parameterDocs[] = [
                'name' => $param['name'],
                'in' => $param['in'] ?? 'query',
                'description' => $param['description'] ?? '',
                'required' => $param['required'] ?? false,
                'schema' => $param['schema'] ?? ['type' => 'string']
            ];
        }
        
        return $parameterDocs;
    }
    
    /**
     * Generate request body documentation
     *
     * @param array $requestBody Request body array
     *
     * @return array Request body documentation
     */
    private function generateRequestBody(array $requestBody): array
    {
        return [
            'description' => $requestBody['description'] ?? '',
            'required' => $requestBody['required'] ?? false,
            'content' => $this->generateContent($requestBody['content'] ?? [])
        ];
    }
    
    /**
     * Generate schemas documentation
     *
     * @return array Schemas documentation
     */
    private function generateSchemas(): array
    {
        $schemas = [];
        
        foreach ($this->models as $name => $model) {
            $schemas[$name] = [
                'type' => 'object',
                'properties' => $model['properties'] ?? [],
                'required' => $model['required'] ?? []
            ];
            
            if (isset($model['description'])) {
                $schemas[$name]['description'] = $model['description'];
            }
        }
        
        return $schemas;
    }
    
    /**
     * Generate Markdown documentation
     *
     * @return string Markdown documentation
     */
    public function generateMarkdown(): string
    {
        $markdown = "# API Documentation\n\n";
        $markdown .= "This document describes the User Management API endpoints and models.\n\n";
        
        $markdown .= "## Base URL\n\n";
        $markdown .= "```\nhttps://api.example.com/v1\n```\n\n";
        
        $markdown .= "## Authentication\n\n";
        $markdown .= "All API requests must include an authentication token in the Authorization header:\n\n";
        $markdown .= "```\nAuthorization: Bearer <token>\n```\n\n";
        
        $markdown .= "## Endpoints\n\n";
        
        foreach ($this->endpoints as $method => $methodEndpoints) {
            foreach ($methodEndpoints as $path => $doc) {
                $markdown .= "### {$method} {$path}\n\n";
                
                if (isset($doc['summary'])) {
                    $markdown .= "**Summary:** {$doc['summary']}\n\n";
                }
                
                if (isset($doc['description'])) {
                    $markdown .= "**Description:** {$doc['description']}\n\n";
                }
                
                if (isset($doc['parameters'])) {
                    $markdown .= "**Parameters:**\n\n";
                    $markdown .= "| Name | Type | Required | Description |\n";
                    $markdown .= "|------|------|----------|-------------|\n";
                    
                    foreach ($doc['parameters'] as $param) {
                        $required = $param['required'] ? 'Yes' : 'No';
                        $markdown .= "| {$param['name']} | {$param['type'] ?? 'string'} | $required | {$param['description'] ?? ''} |\n";
                    }
                    $markdown .= "\n";
                }
                
                if (isset($doc['responses'])) {
                    $markdown .= "**Responses:**\n\n";
                    
                    foreach ($doc['responses'] as $code => $response) {
                        $markdown .= "#### {$code} {$response['description']}\n\n";
                        
                        if (isset($response['example'])) {
                            $markdown .= "```json\n" . json_encode($response['example'], JSON_PRETTY_PRINT) . "\n```\n\n";
                        }
                    }
                }
                
                $markdown .= "---\n\n";
            }
        }
        
        $markdown .= "## Models\n\n";
        
        foreach ($this->models as $name => $model) {
            $markdown .= "### {$name}\n\n";
            
            if (isset($model['description'])) {
                $markdown .= "{$model['description']}\n\n";
            }
            
            $markdown .= "**Properties:**\n\n";
            $markdown .= "| Name | Type | Required | Description |\n";
            $markdown .= "|------|------|----------|-------------|\n";
            
            foreach ($model['properties'] as $propName => $property) {
                $required = in_array($propName, $model['required'] ?? []) ? 'Yes' : 'No';
                $type = $property['type'] ?? 'string';
                $description = $property['description'] ?? '';
                $markdown .= "| $propName | $type | $required | $description |\n";
            }
            
            $markdown .= "\n";
        }
        
        return $markdown;
    }
}

// README Generator
class ReadmeGenerator
{
    private array $sections = [];
    
    /**
     * Add section to README
     *
     * @param string $title   Section title
     * @param string $content Section content
     */
    public function addSection(string $title, string $content): void
    {
        $this->sections[$title] = $content;
    }
    
    /**
     * Generate README content
     *
     * @return string README content in Markdown format
     */
    public function generate(): string
    {
        $readme = '';
        
        foreach ($this->sections as $title => $content) {
            $readme .= "## $title\n\n";
            $readme .= "$content\n\n";
        }
        
        return $readme;
    }
    
    /**
     * Generate project README
     *
     * @param array $project Project information
     *
     * @return string Complete README content
     */
    public function generateProjectReadme(array $project): string
    {
        $readme = "# {$project['name']}\n\n";
        
        if (isset($project['description'])) {
            $readme .= "{$project['description']}\n\n";
        }
        
        if (isset($project['badges'])) {
            foreach ($project['badges'] as $badge) {
                $readme .= "[![{$badge['text']}]({$badge['url']})]({$badge['link']}) ";
            }
            $readme .= "\n\n";
        }
        
        if (isset($project['installation'])) {
            $readme .= "## Installation\n\n";
            $readme .= "{$project['installation']}\n\n";
        }
        
        if (isset($project['usage'])) {
            $readme .= "## Usage\n\n";
            $readme .= "{$project['usage']}\n\n";
        }
        
        if (isset($project['api'])) {
            $readme .= "## API Documentation\n\n";
            $readme .= "{$project['api']}\n\n";
        }
        
        if (isset($project['contributing'])) {
            $readme .= "## Contributing\n\n";
            $readme .= "{$project['contributing']}\n\n";
        }
        
        if (isset($project['license'])) {
            $readme .= "## License\n\n";
            $readme .= "{$project['license']}\n\n";
        }
        
        return $readme;
    }
}

// Code Comment Generator
class CodeCommentGenerator
{
    /**
     * Generate class comment template
     *
     * @param string $className Class name
     * @param string $description Class description
     *
     * @return string Class comment template
     */
    public function generateClassComment(string $className, string $description): string
    {
        return "/**\n" .
               " * $description\n" .
               " *\n" .
               " * @package App\\Models\n" .
               " * @author  Generated\n" .
               " * @version 1.0.0\n" .
               " * @since   1.0.0\n" .
               " */\n" .
               "class $className\n" .
               "{\n" .
               "    // Class implementation\n" .
               "}\n";
    }
    
    /**
     * Generate method comment template
     *
     * @param string $methodName Method name
     * @param string $description Method description
     * @param array  $parameters Method parameters
     * @param string $returnType Return type
     *
     * @return string Method comment template
     */
    public function generateMethodComment(string $methodName, string $description, array $parameters = [], string $returnType = 'void'): string
    {
        $comment = "/**\n" .
                 " * $description\n" .
                 " *\n";
        
        foreach ($parameters as $param => $type) {
            $comment .= " * @param $type \$$param $param parameter\n";
        }
        
        if ($returnType !== 'void') {
            $comment .= " * @return $returnType Method return value\n";
        }
        
        $comment .= " */\n" .
                 "public function $methodName(";
        
        $paramList = [];
        foreach ($parameters as $param => $type) {
            $paramList[] = "$type \$$param";
        }
        
        $comment .= implode(', ', $paramList);
        $comment .= "): $returnType\n" .
                 "{\n" .
                 "    // Method implementation\n" .
                 "}\n";
        
        return $comment;
    }
    
    /**
     * Generate property comment template
     *
     * @param string $propertyName Property name
     * @param string $type Property type
     * @param string $description Property description
     *
     * @return string Property comment template
     */
    public function generatePropertyComment(string $propertyName, string $type, string $description): string
    {
        return "/**\n" .
               " * $description\n" .
               " *\n" .
               " * @var $type\n" .
               " */\n" .
               "private $type \$$propertyName;\n";
    }
}

// Documentation Examples
class DocumentationExamples
{
    private ApiDocumentationGenerator $apiGenerator;
    private ReadmeGenerator $readmeGenerator;
    private CodeCommentGenerator $commentGenerator;
    
    public function __construct()
    {
        $this->apiGenerator = new ApiDocumentationGenerator();
        $this->readmeGenerator = new ReadmeGenerator();
        $this->commentGenerator = new CodeCommentGenerator();
    }
    
    public function demonstrateApiDocumentation(): void
    {
        echo "API Documentation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Add API endpoints
        $this->apiGenerator->addEndpoint('POST', '/users', [
            'summary' => 'Create a new user',
            'description' => 'Creates a new user account with the provided data',
            'tags' => ['Users'],
            'operationId' => 'createUser',
            'requestBody' => [
                'description' => 'User data to create',
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/UserCreate']
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'User created successfully',
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/User']
                        ]
                    ]
                ],
                '400' => [
                    'description' => 'Invalid input data'
                ],
                '409' => [
                    'description' => 'User already exists'
                ]
            ]
        ]);
        
        $this->apiGenerator->addEndpoint('GET', '/users/{id}', [
            'summary' => 'Get user by ID',
            'description' => 'Retrieves user information by ID',
            'tags' => ['Users'],
            'operationId' => 'getUserById',
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'description' => 'User ID',
                    'required' => true,
                    'schema' => ['type' => 'integer']
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'User information',
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/User']
                        ]
                    ]
                ],
                '404' => [
                    'description' => 'User not found'
                ]
            ]
        ]);
        
        // Add models
        $this->apiGenerator->addModel('User', [
            'description' => 'User entity',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'description' => 'User ID'
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'User name'
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'User email'
                ],
                'role' => [
                    'type' => 'string',
                    'description' => 'User role'
                ],
                'status' => [
                    'type' => 'string',
                    'description' => 'User status'
                ],
                'created_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'description' => 'Creation timestamp'
                ]
            ],
            'required' => ['id', 'name', 'email', 'role', 'status']
        ]);
        
        $this->apiGenerator->addModel('UserCreate', [
            'description' => 'User creation data',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'User name'
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'User email'
                ],
                'password' => [
                    'type' => 'string',
                    'description' => 'User password'
                ],
                'role' => [
                    'type' => 'string',
                    'description' => 'User role'
                ]
            ],
            'required' => ['name', 'email', 'password']
        ]);
        
        // Generate OpenAPI documentation
        $openApi = $this->apiGenerator->generateOpenApi();
        
        echo "OpenAPI Documentation (first 500 characters):\n";
        echo json_encode($openApi, JSON_PRETTY_PRINT) . "\n\n";
        
        // Generate Markdown documentation
        $markdown = $this->apiGenerator->generateMarkdown();
        
        echo "Markdown Documentation (first 1000 characters):\n";
        echo substr($markdown, 0, 1000) . "...\n\n";
    }
    
    public function demonstrateReadmeGeneration(): void
    {
        echo "README Generation Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $project = [
            'name' => 'User Management System',
            'description' => 'A comprehensive user management system with authentication, authorization, and profile management.',
            'badges' => [
                [
                    'text' => 'Build Status',
                    'url' => 'https://img.shields.io/badge/build-passing-brightgreen',
                    'link' => 'https://github.com/example/user-management/actions'
                ],
                [
                    'text' => 'Coverage',
                    'url' => 'https://img.shields.io/badge/coverage-95%25-brightgreen',
                    'link' => 'https://codecov.io/gh/example/user-management'
                ],
                [
                    'text' => 'License',
                    'url' => 'https://img.shields.io/badge/license-MIT-blue',
                    'link' => 'LICENSE'
                ]
            ],
            'installation' => "## Installation\n\n" .
                             "1. Clone the repository:\n" .
                             "```bash\n" .
                             "git clone https://github.com/example/user-management.git\n" .
                             "cd user-management\n" .
                             "```\n\n" .
                             "2. Install dependencies:\n" .
                             "```bash\n" .
                             "composer install\n" .
                             "npm install\n" .
                             "```\n\n" .
                             "3. Configure environment:\n" .
                             "```bash\n" .
                             "cp .env.example .env\n" .
                             "php artisan key:generate\n" .
                             "```\n\n" .
                             "4. Run migrations:\n" .
                             "```bash\n" .
                             "php artisan migrate\n" .
                             "```",
            'usage' => "## Usage\n\n" .
                       "### Basic Usage\n\n" .
                       "```php\n" .
                       "use App\\Services\\UserService;\n\n" .
                       "\$userService = new UserService(\$repository, \$emailService);\n" .
                       "\$user = \$userService->registerUser([\n" .
                       "    'name' => 'John Doe',\n" .
                       "    'email' => 'john@example.com',\n" .
                       "    'password' => 'secure_password'\n" .
                       "]);\n" .
                       "```\n\n" .
                       "### API Usage\n\n" .
                       "```bash\n" .
                       "curl -X POST https://api.example.com/users \\\n" .
                       "  -H 'Content-Type: application/json' \\\n" .
                       "  -d '{\"name\":\"John Doe\",\"email\":\"john@example.com\",\"password\":\"secure_password\"}'\n" .
                       "```",
            'contributing' => "## Contributing\n\n" .
                             "Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details.\n\n" .
                             "### Development Setup\n\n" .
                             "1. Fork the repository\n" .
                             "2. Create a feature branch\n" .
                             "3. Make your changes\n" .
                             "4. Add tests\n" .
                             "5. Run tests\n" .
                             "6. Submit a pull request\n\n" .
                             "### Code Standards\n\n" .
                             "- Follow PSR-12 coding standards\n" .
                             "- Write tests for new features\n" .
                             "- Update documentation\n" .
                             "- Keep code coverage above 90%",
            'license' => "## License\n\n" .
                       "This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details."
        ];
        
        $readme = $this->readmeGenerator->generateProjectReadme($project);
        
        echo "Generated README (first 1500 characters):\n";
        echo substr($readme, 0, 1500) . "...\n\n";
    }
    
    public function demonstrateCodeComments(): void
    {
        echo "Code Comment Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Generate class comment
        $classComment = $this->commentGenerator->generateClassComment(
            'Product',
            'Product entity for e-commerce application'
        );
        
        echo "Generated Class Comment:\n";
        echo $classComment . "\n\n";
        
        // Generate method comment
        $methodComment = $this->commentGenerator->generateMethodComment(
            'calculatePrice',
            'Calculate total price including tax and discounts',
            [
                'quantity' => 'int',
                'discountCode' => 'string|null'
            ],
            'float'
        );
        
        echo "Generated Method Comment:\n";
        echo $methodComment . "\n\n";
        
        // Generate property comment
        $propertyComment = $this->commentGenerator->generatePropertyComment(
            'price',
            'float',
            'Product price in USD'
        );
        
        echo "Generated Property Comment:\n";
        echo $propertyComment . "\n\n";
    }
    
    public function demonstrateDocumentationStandards(): void
    {
        echo "Documentation Standards Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. PHPDoc Standards:\n";
        echo "   - Use proper tags (@param, @return, @throws)\n";
        echo "   - Include type hints for parameters and return values\n";
        echo "   - Provide clear descriptions\n";
        echo "   - Use @link for cross-references\n";
        echo "   - Include @since and @version tags\n\n";
        
        echo "2. API Documentation:\n";
        echo "   - Use OpenAPI/Swagger specifications\n";
        echo "   - Include request/response examples\n";
        echo "   - Document error responses\n";
        echo "   - Provide authentication information\n";
        echo "   - Include rate limiting information\n\n";
        
        echo "3. README Standards:\n";
        echo "   - Clear project description\n";
        echo "   - Installation instructions\n";
        echo "   - Usage examples\n";
        echo "   - Contributing guidelines\n";
        echo "   - License information\n\n";
        
        echo "4. Code Comments:\n";
        echo "   - Comment complex algorithms\n";
        echo "   - Explain business logic\n";
        echo "   - Document configuration options\n";
        echo "   - Include usage examples\n";
        echo "   - Keep comments up to date\n\n";
        
        echo "5. Knowledge Sharing:\n";
        echo "   - Maintain documentation wiki\n";
        echo "   - Conduct code reviews\n";
        echo "   - Hold knowledge sharing sessions\n";
        echo "   - Document architectural decisions\n";
        echo "   - Create onboarding guides\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Documentation Standards and Practices Examples\n";
        echo str_repeat("=", 50) . "\n";
        
        $this->demonstrateApiDocumentation();
        $this->demonstrateReadmeGeneration();
        $this->demonstrateCodeComments();
        $this->demonstrateDocumentationStandards();
    }
}

// Documentation Best Practices
function printDocumentationBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Documentation Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Code Documentation:\n";
    echo "   • Follow PHPDoc standards\n";
    echo "   • Document public interfaces\n";
    echo "   • Include type hints\n";
    echo "   • Provide clear descriptions\n";
    echo "   • Use consistent formatting\n\n";
    
    echo "2. API Documentation:\n";
    echo "   • Use OpenAPI specifications\n";
    echo "   • Include examples\n";
    echo "   • Document errors\n";
    echo "   • Keep documentation current\n";
    echo "   • Use interactive tools\n\n";
    
    echo "3. Project Documentation:\n";
    echo "   • Write clear README files\n";
    echo "   • Include installation guides\n";
    echo "   • Provide usage examples\n";
    echo "   • Document architecture\n";
    echo "   • Include contribution guidelines\n\n";
    
    echo "4. Knowledge Sharing:\n";
    echo "   • Maintain documentation wiki\n";
    echo "   • Conduct regular reviews\n";
    echo "   • Use version control for docs\n";
    echo "   • Create onboarding materials\n";
    echo "   • Document decisions\n\n";
    
    echo "5. Documentation Tools:\n";
    echo "   • Use automated generators\n";
    echo "   • Integrate with CI/CD\n";
    echo "   • Use documentation linters\n";
    echo "   • Generate from code comments\n";
    echo "   • Use versioned documentation\n\n";
    
    echo "6. Maintenance:\n";
    echo "   • Review documentation regularly\n";
    echo "   • Update with code changes\n";
    echo "   • Fix broken links\n";
    echo "   • Remove outdated content\n";
    echo "   • Gather user feedback";
}

// Main execution
function runDocumentationDemo(): void
{
    $examples = new DocumentationExamples();
    $examples->runAllExamples();
    printDocumentationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDocumentationDemo();
}
?>
