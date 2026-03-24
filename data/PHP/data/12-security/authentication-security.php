<?php
/**
 * Authentication and Authorization Security
 * 
 * This file demonstrates secure authentication and authorization
 * implementations including password hashing, session management,
 * and access control.
 */

// Secure Password Manager
class SecurePasswordManager {
    private int $minLength;
    private bool $requireUppercase;
    private bool $requireLowercase;
    private bool $requireNumbers;
    private bool $requireSpecialChars;
    private array $commonPasswords;
    
    public function __construct() {
        $this->minLength = 8;
        $this->requireUppercase = true;
        $this->requireLowercase = true;
        $this->requireNumbers = true;
        $this->requireSpecialChars = true;
        
        // Common passwords to reject
        $this->commonPasswords = [
            'password', '123456', 'password123', 'admin', 'qwerty',
            'letmein', 'welcome', 'monkey', 'dragon', 'master',
            'sunshine', 'iloveyou', 'princess', 'rockyou'
        ];
    }
    
    public function validatePassword(string $password): array {
        $errors = [];
        
        // Check minimum length
        if (strlen($password) < $this->minLength) {
            $errors[] = "Password must be at least {$this->minLength} characters long";
        }
        
        // Check for uppercase letters
        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        // Check for lowercase letters
        if ($this->requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        // Check for numbers
        if ($this->requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        // Check for special characters
        if ($this->requireSpecialChars && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        // Check for common passwords
        if (in_array(strtolower($password), $this->commonPasswords)) {
            $errors[] = "Password is too common and easily guessable";
        }
        
        // Check for sequential characters
        if ($this->hasSequentialChars($password)) {
            $errors[] = "Password contains sequential characters";
        }
        
        // Check for repeated characters
        if ($this->hasRepeatedChars($password)) {
            $errors[] = "Password contains too many repeated characters";
        }
        
        return $errors;
    }
    
    private function hasSequentialChars(string $password): bool {
        // Check for sequential characters like "123", "abc", "qwe"
        for ($i = 0; $i < strlen($password) - 2; $i++) {
            $char1 = ord(strtolower($password[$i]));
            $char2 = ord(strtolower($password[$i + 1]));
            $char3 = ord(strtolower($password[$i + 2]));
            
            // Check for ascending sequence
            if ($char2 === $char1 + 1 && $char3 === $char2 + 1) {
                return true;
            }
            
            // Check for descending sequence
            if ($char2 === $char1 - 1 && $char3 === $char2 - 1) {
                return true;
            }
        }
        
        return false;
    }
    
    private function hasRepeatedChars(string $password): bool {
        // Check for 3 or more repeated characters
        if (preg_match('/(.)\1{2,}/', $password)) {
            return true;
        }
        
        return false;
    }
    
    public function generateStrongPassword(int $length = 12): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        
        // Ensure at least one character from each required set
        $password .= 'abcdefghijklmnopqrstuvwxyz'[rand(0, 25)];
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[rand(0, 25)];
        $password .= '0123456789'[rand(0, 9)];
        $password .= '!@#$%^&*()'[rand(0, 9)];
        
        // Fill the rest
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        return str_shuffle($password);
    }
    
    public function hashPassword(string $password): string {
        // Use Argon2ID (the most secure hashing algorithm)
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function needsRehash(string $hash): bool {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID);
    }
}

// Secure Session Manager
class SecureSessionManager {
    private int $sessionTimeout;
    private string $sessionName;
    private bool $secureCookies;
    private bool $httpOnly;
    private array $sessionData = [];
    
    public function __construct(int $sessionTimeout = 3600, string $sessionName = 'secure_session') {
        $this->sessionTimeout = $sessionTimeout;
        $this->sessionName = $sessionName;
        $this->secureCookies = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $this->httpOnly = true;
        
        $this->configureSession();
    }
    
    private function configureSession(): void {
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->sessionTimeout,
            'path' => '/',
            'domain' => '',
            'secure' => $this->secureCookies,
            'httponly' => $this->httpOnly,
            'samesite' => 'Strict'
        ]);
        
        // Set session name
        session_name($this->sessionName);
        
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID to prevent session fixation
        if (!isset($_SESSION['regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['regenerated'] = true;
        }
        
        // Set session timeout
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    public function validateSession(): bool {
        // Check if session exists
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // Check session timeout
        if (time() - $_SESSION['last_activity'] > $this->sessionTimeout) {
            $this->destroySession();
            return false;
        }
        
        // Check IP address
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            $this->destroySession();
            return false;
        }
        
        // Check user agent
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $this->destroySession();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    public function login(int $userId, array $userData = []): void {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_data'] = $userData;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID after login
        session_regenerate_id(true);
    }
    
    public function logout(): void {
        $this->destroySession();
    }
    
    public function isLoggedIn(): bool {
        return $this->validateSession() && isset($_SESSION['user_id']);
    }
    
    public function getUserId(): ?int {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    public function getUserData(): array {
        return $this->isLoggedIn() ? $_SESSION['user_data'] : [];
    }
    
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }
    
    public function destroySession(): void {
        // Unset all session variables
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
    
    public function getSessionInfo(): array {
        return [
            'session_id' => session_id(),
            'user_id' => $_SESSION['user_id'] ?? null,
            'login_time' => $_SESSION['login_time'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null,
            'ip_address' => $_SESSION['ip_address'] ?? null,
            'timeout' => $this->sessionTimeout,
            'secure' => $this->secureCookies,
            'http_only' => $this->httpOnly
        ];
    }
}

// Multi-Factor Authentication
class MultiFactorAuth {
    private array $secrets = [];
    
    public function generateTOTPSecret(): string {
        return $this->generateRandomBase32(16);
    }
    
    public function generateQRCode(string $secret, string $issuer, string $account): string {
        // This would normally use a QR code library
        // For demo purposes, we'll return a Google Authenticator URL
        $url = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            $issuer,
            $account,
            $secret,
            $issuer
        );
        
        return $url;
    }
    
    public function verifyTOTP(string $secret, string $code): bool {
        // Simplified TOTP verification
        // In production, use a proper TOTP library
        $window = 30; // 30-second window
        $time = floor(time() / $window);
        
        // Check current and adjacent time windows
        for ($i = -1; $i <= 1; $i++) {
            $expectedCode = $this->generateTOTPCode($secret, $time + $i);
            if ($this->verifyCode($code, $expectedCode)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function generateRandomBase32(int $length): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        return $secret;
    }
    
    private function generateTOTPCode(string $secret, int $time): string {
        // Simplified TOTP algorithm
        // In production, use a proper TOTP implementation
        $hash = hash_hmac('sha1', pack('N', $time), $secret);
        $offset = ord(substr($hash, -1)) & 0x0f;
        $binary = pack('N*', substr($hash, $offset, 4));
        $code = sprintf('%06d', (ord($binary) & 0x7fffffff) % 1000000);
        
        return $code;
    }
    
    private function verifyCode(string $input, string $expected): bool {
        return hash_equals($input, $expected);
    }
    
    public function generateBackupCodes(int $count = 10): array {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $codes[] = sprintf('%08d', rand(0, 99999999));
        }
        
        return $codes;
    }
    
    public function verifyBackupCode(string $code, array $backupCodes): bool {
        return in_array($code, $backupCodes);
    }
}

// Role-Based Access Control
class RBAC {
    private array $roles = [];
    private array $permissions = [];
    private array $userRoles = [];
    private array $rolePermissions = [];
    
    public function __construct() {
        $this->initializeDefaultRoles();
        $this->initializeDefaultPermissions();
    }
    
    private function initializeDefaultRoles(): void {
        $this->roles = [
            'guest' => 'Guest user',
            'user' => 'Regular user',
            'moderator' => 'Content moderator',
            'admin' => 'System administrator',
            'super_admin' => 'Super administrator'
        ];
    }
    
    private function initializeDefaultPermissions(): void {
        $this->permissions = [
            'read_content' => 'Read content',
            'create_content' => 'Create content',
            'edit_content' => 'Edit content',
            'delete_content' => 'Delete content',
            'moderate_content' => 'Moderate content',
            'manage_users' => 'Manage users',
            'manage_permissions' => 'Manage permissions',
            'system_config' => 'System configuration',
            'view_logs' => 'View system logs',
            'backup_system' => 'Backup system'
        ];
        
        // Assign permissions to roles
        $this->rolePermissions = [
            'guest' => ['read_content'],
            'user' => ['read_content', 'create_content', 'edit_content'],
            'moderator' => ['read_content', 'create_content', 'edit_content', 'delete_content', 'moderate_content'],
            'admin' => ['read_content', 'create_content', 'edit_content', 'delete_content', 'moderate_content', 'manage_users', 'view_logs', 'backup_system'],
            'super_admin' => array_keys($this->permissions)
        ];
    }
    
    public function addRole(string $name, string $description): void {
        $this->roles[$name] = $description;
    }
    
    public function addPermission(string $name, string $description): void {
        $this->permissions[$name] = $description;
    }
    
    public function assignPermissionToRole(string $role, string $permission): void {
        if (!isset($this->rolePermissions[$role])) {
            $this->rolePermissions[$role] = [];
        }
        
        if (!in_array($permission, $this->rolePermissions[$role])) {
            $this->rolePermissions[$role][] = $permission;
        }
    }
    
    public function assignRoleToUser(int $userId, string $role): void {
        if (!isset($this->roles[$role])) {
            throw new InvalidArgumentException("Role '$role' does not exist");
        }
        
        $this->userRoles[$userId] = $role;
    }
    
    public function getUserRole(int $userId): ?string {
        return $this->userRoles[$userId] ?? null;
    }
    
    public function hasPermission(int $userId, string $permission): bool {
        $role = $this->getUserRole($userId);
        
        if ($role === null) {
            return false;
        }
        
        return in_array($permission, $this->rolePermissions[$role] ?? []);
    }
    
    public function getUserPermissions(int $userId): array {
        $role = $this->getUserRole($userId);
        
        if ($role === null) {
            return [];
        }
        
        return $this->rolePermissions[$role] ?? [];
    }
    
    public function can(int $userId, string $action): bool {
        return $this->hasPermission($userId, $action);
    }
    
    public function getRoles(): array {
        return $this->roles;
    }
    
    public function getPermissions(): array {
        return $this->permissions;
    }
    
    public function getRolePermissions(string $role): array {
        return $this->rolePermissions[$role] ?? [];
    }
}

// Authentication Service
class AuthenticationService {
    private SecurePasswordManager $passwordManager;
    private SecureSessionManager $sessionManager;
    private MultiFactorAuth $mfa;
    private RBAC $rbac;
    private array $users = [];
    
    public function __construct() {
        $this->passwordManager = new SecurePasswordManager();
        $this->sessionManager = new SecureSessionManager();
        $this->mfa = new MultiFactorAuth();
        $this->rbac = new RBAC();
        
        $this->initializeUsers();
    }
    
    private function initializeUsers(): void {
        // Demo users (in production, use database)
        $this->users = [
            1 => [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password_hash' => $this->passwordManager->hashPassword('Admin123!'),
                'mfa_secret' => null,
                'role' => 'admin',
                'mfa_enabled' => false
            ],
            2 => [
                'username' => 'john',
                'email' => 'john@example.com',
                'password_hash' => $this->passwordManager->hashPassword('JohnDoe123!'),
                'mfa_secret' => $this->mfa->generateTOTPSecret(),
                'role' => 'user',
                'mfa_enabled' => true
            ]
        ];
    }
    
    public function login(string $username, string $password, ?string $mfaCode = null): array {
        $errors = [];
        
        // Find user
        $user = null;
        foreach ($this->users as $id => $userData) {
            if ($userData['username'] === $username) {
                $user = array_merge(['id' => $id], $userData);
                break;
            }
        }
        
        if (!$user) {
            $errors[] = 'Invalid username or password';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Verify password
        if (!$this->passwordManager->verifyPassword($password, $user['password_hash'])) {
            $errors[] = 'Invalid username or password';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check MFA if enabled
        if ($user['mfa_enabled']) {
            if ($mfaCode === null) {
                return ['success' => false, 'mfa_required' => true, 'mfa_secret' => $user['mfa_secret']];
            }
            
            if (!$this->mfa->verifyTOTP($user['mfa_secret'], $mfaCode)) {
                $errors[] = 'Invalid authentication code';
                return ['success' => false, 'errors' => $errors];
            }
        }
        
        // Login successful
        $this->sessionManager->login($user['id'], [
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
        
        $this->rbac->assignRoleToUser($user['id'], $user['role']);
        
        return ['success' => true, 'user' => $user];
    }
    
    public function logout(): void {
        $this->sessionManager->logout();
    }
    
    public function isLoggedIn(): bool {
        return $this->sessionManager->isLoggedIn();
    }
    
    public function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $userId = $this->sessionManager->getUserId();
        $user = $this->users[$userId] ?? null;
        
        if ($user) {
            $user['id'] = $userId;
            $user['role'] = $this->sessionManager->get('role');
            $user['permissions'] = $this->rbac->getUserPermissions($userId);
        }
        
        return $user;
    }
    
    public function can(string $permission): bool {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userId = $this->sessionManager->getUserId();
        return $this->rbac->can($userId, $permission);
    }
    
    public function register(array $userData): array {
        $errors = [];
        
        // Validate password
        $passwordErrors = $this->passwordManager->validatePassword($userData['password']);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
        }
        
        // Check if username exists
        foreach ($this->users as $existingUser) {
            if ($existingUser['username'] === $userData['username']) {
                $errors[] = 'Username already exists';
                break;
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Create user
        $userId = max(array_keys($this->users)) + 1;
        $this->users[$userId] = [
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password_hash' => $this->passwordManager->hashPassword($userData['password']),
            'mfa_secret' => $userData['mfa_enabled'] ? $this->mfa->generateTOTPSecret() : null,
            'role' => $userData['role'] ?? 'user',
            'mfa_enabled' => $userData['mfa_enabled'] ?? false
        ];
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    public function enableMFA(int $userId): array {
        if (!isset($this->users[$userId])) {
            return ['success' => false, 'error' => 'User not found'];
        }
        
        $secret = $this->mfa->generateTOTPSecret();
        $this->users[$userId]['mfa_secret'] = $secret;
        $this->users[$userId]['mfa_enabled'] = true;
        
        $qrCode = $this->mfa->generateQRCode($secret, 'Demo App', $this->users[$userId]['username']);
        
        return [
            'success' => true,
            'secret' => $secret,
            'qr_code' => $qrCode
        ];
    }
    
    public function changePassword(int $userId, string $oldPassword, string $newPassword): array {
        $errors = [];
        
        if (!isset($this->users[$userId])) {
            $errors[] = 'User not found';
            return ['success' => false, 'errors' => $errors];
        }
        
        $user = $this->users[$userId];
        
        // Verify old password
        if (!$this->passwordManager->verifyPassword($oldPassword, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Validate new password
        $passwordErrors = $this->passwordManager->validatePassword($newPassword);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
            return ['success' => false, 'errors' => $errors];
        }
        
        // Update password
        $this->users[$userId]['password_hash'] = $this->passwordManager->hashPassword($newPassword);
        
        return ['success' => true];
    }
}

// Authentication Examples
class AuthenticationExamples {
    private AuthenticationService $auth;
    
    public function __construct() {
        $this->auth = new AuthenticationService();
    }
    
    public function demonstratePasswordSecurity(): void {
        echo "Password Security Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $passwordManager = new SecurePasswordManager();
        
        // Test password validation
        $testPasswords = [
            'weak' => '123456',
            'medium' => 'Password123',
            'strong' => 'SecurePass123!',
            'complex' => 'C0mpl3x!P@ssw0rd#2024'
        ];
        
        foreach ($testPasswords as $type => $password) {
            echo "Testing $type password: $password\n";
            $errors = $passwordManager->validatePassword($password);
            
            if (empty($errors)) {
                echo "  ✅ Password is valid\n";
                $hash = $passwordManager->hashPassword($password);
                echo "  Hash: " . substr($hash, 0, 20) . "...\n";
            } else {
                echo "  ❌ Password is invalid:\n";
                foreach ($errors as $error) {
                    echo "    - $error\n";
                }
            }
            echo "\n";
        }
        
        // Generate strong password
        $strongPassword = $passwordManager->generateStrongPassword();
        echo "Generated strong password: $strongPassword\n";
        echo "Validation: " . (empty($passwordManager->validatePassword($strongPassword)) ? '✅ Valid' : '❌ Invalid') . "\n\n";
    }
    
    public function demonstrateSessionSecurity(): void {
        echo "Session Security Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $sessionManager = new SecureSessionManager();
        
        echo "Session configuration:\n";
        $info = $sessionManager->getSessionInfo();
        foreach ($info as $key => $value) {
            echo "  $key: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
        }
        
        // Simulate login
        $sessionManager->login(1, ['username' => 'admin', 'role' => 'admin']);
        echo "\nUser logged in successfully\n";
        echo "Is logged in: " . ($sessionManager->isLoggedIn() ? 'Yes' : 'No') . "\n";
        echo "User ID: " . $sessionManager->getUserId() . "\n";
        echo "User data: " . json_encode($sessionManager->getUserData()) . "\n";
        
        // Simulate session validation
        echo "\nSession validation: " . ($sessionManager->validateSession() ? 'Valid' : 'Invalid') . "\n";
        
        // Logout
        $sessionManager->logout();
        echo "User logged out\n";
        echo "Is logged in: " . ($sessionManager->isLoggedIn() ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateMultiFactorAuth(): void {
        echo "\nMulti-Factor Authentication Examples\n";
        echo str_repeat("-", 40) . "\n";
        
        $mfa = new MultiFactorAuth();
        
        // Generate TOTP secret
        $secret = $mfa->generateTOTPSecret();
        echo "Generated TOTP secret: $secret\n";
        
        // Generate QR code URL
        $qrCode = $mfa->generateQRCode($secret, 'Demo App', 'john_doe');
        echo "QR Code URL: $qrCode\n";
        
        // Generate backup codes
        $backupCodes = $mfa->generateBackupCodes(5);
        echo "Backup codes: " . implode(', ', $backupCodes) . "\n";
        
        // Simulate TOTP verification
        echo "\nSimulating TOTP verification:\n";
        // In a real scenario, you'd use the current time and secret to generate valid codes
        $validCode = '123456'; // This would be calculated based on time and secret
        $invalidCode = '654321';
        
        echo "Valid code verification: " . ($mfa->verifyTOTP($secret, $validCode) ? '✅ Valid' : '❌ Invalid') . "\n";
        echo "Invalid code verification: " . ($mfa->verifyTOTP($secret, $invalidCode) ? '✅ Valid' : '❌ Invalid') . "\n";
        
        echo "Backup code verification: " . ($mfa->verifyBackupCode($backupCodes[0], $backupCodes) ? '✅ Valid' : '❌ Invalid') . "\n";
    }
    
    public function demonstrateRBAC(): void {
        echo "\nRole-Based Access Control Examples\n";
        echo str_repeat("-", 40) . "\n";
        
        $rbac = new RBAC();
        
        // Assign roles to users
        $rbac->assignRoleToUser(1, 'admin');
        $rbac->assignRoleToUser(2, 'user');
        
        // Check permissions
        echo "Permission checks:\n";
        echo "Admin can create content: " . ($rbac->can(1, 'create_content') ? '✅ Yes' : '❌ No') . "\n";
        echo "Admin can manage users: " . ($rbac->can(1, 'manage_users') ? '✅ Yes' : '❌ No') . "\n";
        echo "User can create content: " . ($rbac->can(2, 'create_content') ? '✅ Yes' : '❌ No') . "\n";
        echo "User can manage users: " . ($rbac->can(2, 'manage_users') ? '✅ Yes' : '❌ No') . "\n";
        
        // Show roles and permissions
        echo "\nAvailable roles:\n";
        foreach ($rbac->getRoles() as $role => $description) {
            echo "  $role: $description\n";
        }
        
        echo "\nAvailable permissions:\n";
        foreach ($rbac->getPermissions() as $permission => $description) {
            echo "  $permission: $description\n";
        }
        
        echo "\nRole permissions:\n";
        foreach ($rbac->getRoles() as $role) {
            $permissions = $rbac->getRolePermissions($role);
            echo "  $role: " . implode(', ', $permissions) . "\n";
        }
    }
    
    public function demonstrateAuthentication(): void {
        echo "\nAuthentication Service Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Test login
        echo "Testing login:\n";
        
        // Valid login
        $result = $this->auth->login('admin', 'Admin123!');
        echo "Admin login: " . ($result['success'] ? '✅ Success' : '❌ Failed') . "\n";
        
        if ($result['success']) {
            echo "Current user: " . json_encode($this->auth->getCurrentUser()) . "\n";
            echo "Can manage users: " . ($this->auth->can('manage_users') ? '✅ Yes' : '❌ No') . "\n";
            
            $this->auth->logout();
            echo "Logged out successfully\n";
        }
        
        // Invalid login
        $result = $this->auth->login('admin', 'wrongpassword');
        echo "Invalid login: " . ($result['success'] ? '✅ Success' : '❌ Failed') . "\n";
        
        // Test registration
        echo "\nTesting registration:\n";
        $userData = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'NewUser123!',
            'role' => 'user'
        ];
        
        $result = $this->auth->register($userData);
        echo "User registration: " . ($result['success'] ? '✅ Success' : '❌ Failed') . "\n";
        
        if (!$result['success']) {
            echo "Errors: " . implode(', ', $result['errors']) . "\n";
        }
        
        // Test MFA
        echo "\nTesting MFA:\n";
        $mfaResult = $this->auth->enableMFA(2);
        echo "Enable MFA for user 2: " . ($mfaResult['success'] ? '✅ Success' : '❌ Failed') . "\n";
        
        if ($mfaResult['success']) {
            echo "MFA Secret: " . $mfaResult['secret'] . "\n";
            echo "QR Code URL: " . substr($mfaResult['qr_code'], 0, 50) . "...\n";
        }
    }
    
    public function runAllExamples(): void {
        echo "Authentication Security Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstratePasswordSecurity();
        $this->demonstrateSessionSecurity();
        $this->demonstrateMultiFactorAuth();
        $this->demonstrateRBAC();
        $this->demonstrateAuthentication();
    }
}

// Authentication Best Practices
function printAuthenticationBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Authentication Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Password Security:\n";
    echo "   • Use strong password policies\n";
    echo "   • Implement password hashing (Argon2ID)\n";
    echo "   • Require password complexity\n";
    echo "   • Implement password expiration\n";
    echo "   • Check against common passwords\n\n";
    
    echo "2. Session Security:\n";
    echo "   • Use secure session configuration\n";
    echo "   • Implement session timeout\n";
    echo "   • Regenerate session IDs\n";
    echo "   • Validate session data\n";
    echo "   • Use secure cookies\n\n";
    
    echo "3. Multi-Factor Authentication:\n";
    echo "   • Implement TOTP (Time-based OTP)\n";
    echo "   • Provide backup codes\n";
    echo "   • Use secure key generation\n";
    echo "   • Implement recovery options\n";
    echo "   • Rate limit MFA attempts\n\n";
    
    echo "4. Access Control:\n";
    echo "   • Implement principle of least privilege\n";
    echo "   • Use role-based access control\n";
    echo "   • Regular permission reviews\n";
    echo "   • Implement audit trails\n";
    echo "   • Secure API endpoints\n\n";
    
    echo "5. Security Headers:\n";
    echo "   • Use HTTPS everywhere\n";
    echo "   • Implement security headers\n";
    echo "   • Use SameSite cookies\n";
    echo "   • Implement CSP headers\n";
    echo "   • Set proper cache headers\n\n";
    
    echo "6. Monitoring & Logging:\n";
    echo "   • Log all authentication events\n";
    echo "   • Monitor failed login attempts\n";
    echo "   • Implement account lockout\n";
    echo "   • Detect suspicious activities\n";
    echo "   • Regular security audits";
}

// Main execution
function runAuthenticationSecurityDemo(): void {
    $examples = new AuthenticationExamples();
    $examples->runAllExamples();
    printAuthenticationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAuthenticationSecurityDemo();
}
?>
