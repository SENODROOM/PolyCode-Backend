<?php
/**
 * Data Protection and Encryption
 * 
 * This file demonstrates data encryption, secure storage,
 * and data protection techniques for PHP applications.
 */

// Encryption Manager
class EncryptionManager {
    private string $key;
    private string $algorithm;
    private int $keyLength;
    private string $ivLength;
    
    public function __construct(string $key = null, string $algorithm = 'AES-256-CBC') {
        $this->algorithm = $algorithm;
        $this->keyLength = $this->getKeyLength();
        $this->ivLength = $this->getIVLength();
        
        if ($key === null) {
            $this->key = $this->generateKey();
        } else {
            $this->key = $key;
        }
        
        $this->validateKey();
    }
    
    private function getKeyLength(): int {
        switch ($this->algorithm) {
            case 'AES-128-CBC':
                return 16;
            case 'AES-192-CBC':
                return 24;
            case 'AES-256-CBC':
                return 32;
            default:
                throw new InvalidArgumentException("Unsupported algorithm: {$this->algorithm}");
        }
    }
    
    private function getIVLength(): int {
        return openssl_cipher_iv_length($this->algorithm);
    }
    
    private function generateKey(): string {
        return random_bytes($this->keyLength);
    }
    
    private function validateKey(): void {
        if (strlen($this->key) !== $this->keyLength) {
            throw new InvalidArgumentException("Invalid key length for algorithm {$this->algorithm}");
        }
    }
    
    public function encrypt(string $data): string {
        $iv = random_bytes($this->ivLength);
        $encrypted = openssl_encrypt($data, $this->algorithm, $this->key, OPENSSL_RAW_DATA, $iv);
        
        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed');
        }
        
        // Combine IV and encrypted data
        return base64_encode($iv . $encrypted);
    }
    
    public function decrypt(string $encryptedData): string {
        $data = base64_decode($encryptedData);
        
        if ($data === false || strlen($data) < $this->ivLength) {
            throw new InvalidArgumentException('Invalid encrypted data');
        }
        
        $iv = substr($data, 0, $this->ivLength);
        $encrypted = substr($data, $this->ivLength);
        
        $decrypted = openssl_decrypt($encrypted, $this->algorithm, $this->key, OPENSSL_RAW_DATA, $iv);
        
        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed');
        }
        
        return $decrypted;
    }
    
    public function encryptFile(string $inputFile, string $outputFile): void {
        if (!file_exists($inputFile)) {
            throw new InvalidArgumentException("Input file not found: $inputFile");
        }
        
        $data = file_get_contents($inputFile);
        $encryptedData = $this->encrypt($data);
        
        file_put_contents($outputFile, $encryptedData);
    }
    
    public function decryptFile(string $inputFile, string $outputFile): void {
        if (!file_exists($inputFile)) {
            throw new InvalidArgumentException("Input file not found: $inputFile");
        }
        
        $encryptedData = file_get_contents($inputFile);
        $decryptedData = $this->decrypt($encryptedData);
        
        file_put_contents($outputFile, $decryptedData);
    }
    
    public function getKey(): string {
        return base64_encode($this->key);
    }
    
    public function setKey(string $key): void {
        $this->key = base64_decode($key);
        $this->validateKey();
    }
}

// Secure Hash Manager
class SecureHashManager {
    private string $algorithm;
    private int $iterations;
    private string $salt;
    
    public function __construct(string $algorithm = 'sha256', int $iterations = 100000) {
        $this->algorithm = $algorithm;
        $this->iterations = $iterations;
        $this->salt = random_bytes(32);
    }
    
    public function hash(string $data): string {
        $hash = hash_pbkdf2($this->algorithm, $data, $this->salt, $this->iterations, 32, true);
        
        // Combine algorithm, iterations, salt, and hash
        return base64_encode($this->algorithm . ':' . $this->iterations . ':' . base64_encode($this->salt) . ':' . base64_encode($hash));
    }
    
    public function verify(string $data, string $hash): bool {
        $parts = explode(':', base64_decode($hash));
        
        if (count($parts) !== 4) {
            return false;
        }
        
        $algorithm = $parts[0];
        $iterations = (int)$parts[1];
        $salt = base64_decode($parts[2]);
        $expectedHash = base64_decode($parts[3]);
        
        $actualHash = hash_pbkdf2($algorithm, $data, $salt, $iterations, 32, true);
        
        return hash_equals($expectedHash, $actualHash);
    }
    
    public function hashFile(string $filename): string {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException("File not found: $filename");
        }
        
        $data = file_get_contents($filename);
        return $this->hash($data);
    }
    
    public function verifyFile(string $filename, string $hash): bool {
        if (!file_exists($filename)) {
            return false;
        }
        
        return $this->verify(file_get_contents($filename), $hash);
    }
    
    public function generateChecksum(string $data): string {
        return hash('sha256', $data);
    }
    
    public function verifyChecksum(string $data, string $checksum): bool {
        return hash_equals($checksum, $this->generateChecksum($data));
    }
}

// Data Masking
class DataMasker {
    private array $patterns = [
        'email' => '/([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
        'phone' => '/(\d{3})[-.\s]?(\d{3})[-.\s]?(\d{4})/',
        'credit_card' => '/(\d{4})[-\s]?(\d{4})[-\s]?(\d{4})[-\s]?(\d{4})/',
        'ssn' => '/(\d{3})[-\s]?(\d{2})[-\s]?(\d{4})/',
        'ip_address' => '/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/'
    ];
    
    public function maskEmail(string $email, bool $showDomain = true): string {
        if (!preg_match($this->patterns['email'], $email, $matches)) {
            return $email;
        }
        
        $username = $matches[1];
        $domain = $matches[2];
        
        if (strlen($username) <= 2) {
            $maskedUsername = str_repeat('*', strlen($username));
        } else {
            $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
        }
        
        return $showDomain ? $maskedUsername . '@' . $domain : $maskedUsername . '@***';
    }
    
    public function maskPhone(string $phone): string {
        if (!preg_match($this->patterns['phone'], $phone, $matches)) {
            return $phone;
        }
        
        return $matches[1] . '-***-' . $matches[3];
    }
    
    public function maskCreditCard(string $card): string {
        if (!preg_match($this->patterns['credit_card'], $card, $matches)) {
            return $card;
        }
        
        return $matches[1] . '-****-****-' . $matches[4];
    }
    
    public function maskSSN(string $ssn): string {
        if (!preg_match($this->patterns['ssn'], $ssn, $matches)) {
            return $ssn;
        }
        
        return '***-**-' . $matches[3];
    }
    
    public function maskIPAddress(string $ip): string {
        if (!preg_match($this->patterns['ip_address'], $ip, $matches)) {
            return $ip;
        }
        
        $parts = explode('.', $matches[1]);
        return $parts[0] . '.' . $parts[1] . '.*.*';
    }
    
    public function maskString(string $string, int $showFirst = 2, int $showLast = 2): string {
        $length = strlen($string);
        
        if ($length <= $showFirst + $showLast) {
            return str_repeat('*', $length);
        }
        
        $first = substr($string, 0, $showFirst);
        $last = substr($string, -$showLast);
        $middle = str_repeat('*', $length - $showFirst - $showLast);
        
        return $first . $middle . $last;
    }
    
    public function maskData(array $data, array $fields): array {
        $masked = $data;
        
        foreach ($fields as $field => $type) {
            if (isset($masked[$field])) {
                switch ($type) {
                    case 'email':
                        $masked[$field] = $this->maskEmail($masked[$field]);
                        break;
                    case 'phone':
                        $masked[$field] = $this->maskPhone($masked[$field]);
                        break;
                    case 'credit_card':
                        $masked[$field] = $this->maskCreditCard($masked[$field]);
                        break;
                    case 'ssn':
                        $masked[$field] = $this->maskSSN($masked[$field]);
                        break;
                    case 'ip':
                        $masked[$field] = $this->maskIPAddress($masked[$field]);
                        break;
                    case 'string':
                        $masked[$field] = $this->maskString($masked[$field]);
                        break;
                }
            }
        }
        
        return $masked;
    }
}

// Secure Data Storage
class SecureDataStorage {
    private EncryptionManager $encryption;
    private SecureHashManager $hash;
    private DataMasker $masker;
    private string $storagePath;
    
    public function __construct(string $storagePath = './secure_storage') {
        $this->encryption = new EncryptionManager();
        $this->hash = new SecureHashManager();
        $this->masker = new DataMasker();
        $this->storagePath = $storagePath;
        
        $this->ensureStorageDirectory();
    }
    
    private function ensureStorageDirectory(): void {
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0700, true);
        }
    }
    
    public function store(string $key, mixed $data, bool $encrypt = true, bool $hash = true): bool {
        $serialized = serialize($data);
        $filename = $this->getFilename($key);
        
        if ($encrypt) {
            $serialized = $this->encryption->encrypt($serialized);
        }
        
        if ($hash) {
            $checksum = $this->hash->generateChecksum($serialized);
            $serialized .= '|' . $checksum;
        }
        
        $result = file_put_contents($filename, $serialized, LOCK_EX);
        
        if ($result !== false) {
            chmod($filename, 0600);
        }
        
        return $result !== false;
    }
    
    public function retrieve(string $key, bool $decrypt = true, bool $verifyHash = true): mixed {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        
        if ($verifyHash) {
            $parts = explode('|', $data, 2);
            
            if (count($parts) === 2) {
                $payload = $parts[0];
                $checksum = $parts[1];
                
                if (!$this->hash->verifyChecksum($payload, $checksum)) {
                    throw new RuntimeException('Data integrity check failed');
                }
                
                $data = $payload;
            }
        }
        
        if ($decrypt) {
            $data = $this->encryption->decrypt($data);
        }
        
        return unserialize($data);
    }
    
    public function delete(string $key): bool {
        $filename = $this->getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return false;
    }
    
    public function exists(string $key): bool {
        return file_exists($this->getFilename($key));
    }
    
    private function getFilename(string $key): string {
        $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        return $this->storagePath . '/' . $safeKey . '.dat';
    }
    
    public function getStorageInfo(): array {
        $files = glob($this->storagePath . '/*.dat');
        $totalSize = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
        }
        
        return [
            'files_count' => count($files),
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'storage_path' => $this->storagePath
        ];
    }
}

// API Security
class APISecurity {
    private string $apiKey;
    private array $allowedOrigins = [];
    private int $rateLimit = 100;
    private array $rateLimitStorage = [];
    
    public function __construct(string $apiKey = null) {
        $this->apiKey = $apiKey ?? $this->generateApiKey();
    }
    
    private function generateApiKey(): string {
        return bin2hex(random_bytes(32));
    }
    
    public function getApiKey(): string {
        return $this->apiKey;
    }
    
    public function validateApiKey(string $key): bool {
        return hash_equals($this->apiKey, $key);
    }
    
    public function addAllowedOrigin(string $origin): void {
        $this->allowedOrigins[] = $origin;
    }
    
    public function validateOrigin(string $origin): bool {
        if (empty($this->allowedOrigins)) {
            return true; // Allow all if no restrictions
        }
        
        return in_array($origin, $this->allowedOrigins);
    }
    
    public function setRateLimit(int $limit): void {
        $this->rateLimit = $limit;
    }
    
    public function checkRateLimit(string $identifier): bool {
        $now = time();
        $window = 3600; // 1 hour window
        
        if (!isset($this->rateLimitStorage[$identifier])) {
            $this->rateLimitStorage[$identifier] = [];
        }
        
        // Clean old requests
        $this->rateLimitStorage[$identifier] = array_filter(
            $this->rateLimitStorage[$identifier],
            fn($time) => $time > $now - $window
        );
        
        // Check limit
        if (count($this->rateLimitStorage[$identifier]) >= $this->rateLimit) {
            return false;
        }
        
        // Record request
        $this->rateLimitStorage[$identifier][] = $now;
        
        return true;
    }
    
    public function generateJWT(array $payload, int $expiresIn = 3600): string {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiresIn;
        
        $headerEncoded = base64_encode(json_encode($header));
        $payloadEncoded = base64_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->apiKey, true);
        $signatureEncoded = base64_encode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    public function validateJWT(string $token): array {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid JWT format');
        }
        
        $headerEncoded = $parts[0];
        $payloadEncoded = $parts[1];
        $signatureEncoded = $parts[2];
        
        $signature = base64_decode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->apiKey, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            throw new RuntimeException('Invalid JWT signature');
        }
        
        $payload = json_decode(base64_decode($payloadEncoded), true);
        
        if ($payload['exp'] < time()) {
            throw new RuntimeException('JWT has expired');
        }
        
        return $payload;
    }
    
    public function encryptAPIResponse(array $data): string {
        return $this->encryption->encrypt(json_encode($data));
    }
    
    public function decryptAPIRequest(string $encryptedData): array {
        $decrypted = $this->encryption->decrypt($encryptedData);
        return json_decode($decrypted, true);
    }
}

// Data Protection Examples
class DataProtectionExamples {
    private EncryptionManager $encryption;
    private SecureHashManager $hash;
    private DataMasker $masker;
    private SecureDataStorage $storage;
    private APISecurity $api;
    
    public function __construct() {
        $this->encryption = new EncryptionManager();
        $this->hash = new SecureHashManager();
        $this->masker = new DataMasker();
        $this->storage = new SecureDataStorage('./demo_storage');
        $this->api = new APISecurity();
    }
    
    public function demonstrateEncryption(): void {
        echo "Encryption Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $sensitiveData = [
            'user_id' => 12345,
            'email' => 'john@example.com',
            'ssn' => '123-45-6789',
            'credit_card' => '4111-1111-1111-1111',
            'message' => 'This is a secret message'
        ];
        
        echo "Original data: " . json_encode($sensitiveData) . "\n";
        
        // Encrypt data
        $serialized = serialize($sensitiveData);
        $encrypted = $this->encryption->encrypt($serialized);
        
        echo "Encrypted: " . substr($encrypted, 0, 50) . "...\n";
        
        // Decrypt data
        $decrypted = $this->encryption->decrypt($encrypted);
        $restored = unserialize($decrypted);
        
        echo "Decrypted: " . json_encode($restored) . "\n";
        echo "Data integrity: " . (json_encode($sensitiveData) === json_encode($restored) ? '✅ Valid' : '❌ Invalid') . "\n\n";
        
        // File encryption
        $testFile = 'test_data.txt';
        file_put_contents($testFile, 'Sensitive file content');
        
        $encryptedFile = 'test_data.enc';
        $this->encryption->encryptFile($testFile, $encryptedFile);
        
        echo "File encrypted: $encryptedFile\n";
        
        $decryptedFile = 'test_data_decrypted.txt';
        $this->encryption->decryptFile($encryptedFile, $decryptedFile);
        
        echo "File decrypted: $decryptedFile\n";
        echo "Content: " . file_get_contents($decryptedFile) . "\n";
        
        // Cleanup
        unlink($testFile);
        unlink($encryptedFile);
        unlink($decryptedFile);
    }
    
    public function demonstrateHashing(): void {
        echo "\nHashing Examples\n";
        echo str_repeat("-", 20) . "\n";
        
        $data = 'Important data to hash';
        
        echo "Original data: $data\n";
        
        // Generate hash
        $hash = $this->hash->hash($data);
        echo "Hash: $hash\n";
        
        // Verify hash
        $isValid = $this->hash->verify($data, $hash);
        echo "Verification: " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Test with wrong data
        $wrongData = 'Wrong data';
        $isWrongValid = $this->hash->verify($wrongData, $hash);
        echo "Wrong data verification: " . ($isWrongValid ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // File hashing
        $testFile = 'hash_test.txt';
        file_put_contents($testFile, 'File content for hashing');
        
        $fileHash = $this->hash->hashFile($testFile);
        echo "File hash: $fileHash\n";
        
        $fileValid = $this->hash->verifyFile($testFile, $fileHash);
        echo "File verification: " . ($fileValid ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Checksum
        $checksum = $this->hash->generateChecksum($data);
        echo "Checksum: $checksum\n";
        
        $checksumValid = $this->hash->verifyChecksum($data, $checksum);
        echo "Checksum verification: " . ($checksumValid ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Cleanup
        unlink($testFile);
    }
    
    public function demonstrateDataMasking(): void {
        echo "\nData Masking Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        $sensitiveData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '555-123-4567',
            'ssn' => '123-45-6789',
            'credit_card' => '4111-1111-1111-1111',
            'ip_address' => '192.168.1.100'
        ];
        
        echo "Original data: " . json_encode($sensitiveData) . "\n";
        
        // Mask individual fields
        echo "\nIndividual masking:\n";
        echo "Email: " . $this->masker->maskEmail($sensitiveData['email']) . "\n";
        echo "Phone: " . $this->masker->maskPhone($sensitiveData['phone']) . "\n";
        echo "SSN: " . $this->masker->maskSSN($sensitiveData['ssn']) . "\n";
        echo "Credit Card: " . $this->masker->maskCreditCard($sensitiveData['credit_card']) . "\n";
        echo "IP Address: " . $this->masker->maskIPAddress($sensitiveData['ip_address']) . "\n";
        echo "Name: " . $this->masker->maskString($sensitiveData['name']) . "\n";
        
        // Mask entire data structure
        $maskingRules = [
            'email' => 'email',
            'phone' => 'phone',
            'ssn' => 'ssn',
            'credit_card' => 'credit_card',
            'ip_address' => 'ip',
            'name' => 'string'
        ];
        
        $maskedData = $this->masker->maskData($sensitiveData, $maskingRules);
        
        echo "\nMasked data: " . json_encode($maskedData) . "\n";
    }
    
    public function demonstrateSecureStorage(): void {
        echo "\nSecure Storage Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        $userData = [
            'id' => 123,
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'preferences' => ['theme' => 'dark', 'notifications' => true]
        ];
        
        // Store data
        $key = 'user_123';
        $stored = $this->storage->store($key, $userData);
        echo "Data stored: " . ($stored ? '✅ Success' : '❌ Failed') . "\n";
        
        // Check if exists
        $exists = $this->storage->exists($key);
        echo "Data exists: " . ($exists ? 'Yes' : 'No') . "\n";
        
        // Retrieve data
        $retrieved = $this->storage->retrieve($key);
        echo "Retrieved data: " . json_encode($retrieved) . "\n";
        echo "Data integrity: " . (json_encode($userData) === json_encode($retrieved) ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Storage info
        $info = $this->storage->getStorageInfo();
        echo "\nStorage info:\n";
        echo "Files count: {$info['files_count']}\n";
        echo "Total size: {$info['total_size_mb']} MB\n";
        echo "Storage path: {$info['storage_path']}\n";
        
        // Delete data
        $deleted = $this->storage->delete($key);
        echo "Data deleted: " . ($deleted ? '✅ Success' : '❌ Failed') . "\n";
    }
    
    public function demonstrateAPISecurity(): void {
        echo "\nAPI Security Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // API Key validation
        $apiKey = $this->api->getApiKey();
        echo "Generated API Key: " . substr($apiKey, 0, 16) . "...\n";
        
        $validKey = $this->api->validateApiKey($apiKey);
        echo "Valid API key: " . ($validKey ? '✅ Yes' : '❌ No') . "\n";
        
        $invalidKey = 'invalid_key_12345';
        $invalidKeyValid = $this->api->validateApiKey($invalidKey);
        echo "Invalid API key: " . ($invalidKeyValid ? '✅ Yes' : '❌ No') . "\n";
        
        // Origin validation
        $this->api->addAllowedOrigin('https://example.com');
        $this->api->addAllowedOrigin('https://api.example.com');
        
        $validOrigin = $this->api->validateOrigin('https://example.com');
        echo "Valid origin: " . ($validOrigin ? '✅ Yes' : '❌ No') . "\n";
        
        $invalidOrigin = $this->api->validateOrigin('https://malicious.com');
        echo "Invalid origin: " . ($invalidOrigin ? '✅ Yes' : '❌ No') . "\n";
        
        // Rate limiting
        $identifier = 'client_123';
        $this->api->setRateLimit(5);
        
        echo "\nRate limiting (5 requests allowed):\n";
        for ($i = 1; $i <= 7; $i++) {
            $allowed = $this->api->checkRateLimit($identifier);
            echo "Request $i: " . ($allowed ? '✅ Allowed' : '❌ Blocked') . "\n";
        }
        
        // JWT tokens
        $payload = [
            'user_id' => 123,
            'username' => 'johndoe',
            'role' => 'user'
        ];
        
        $token = $this->api->generateJWT($payload, 3600);
        echo "\nGenerated JWT: " . substr($token, 0, 50) . "...\n";
        
        try {
            $decoded = $this->api->validateJWT($token);
            echo "JWT validation: ✅ Valid\n";
            echo "Decoded payload: " . json_encode($decoded) . "\n";
        } catch (Exception $e) {
            echo "JWT validation: ❌ Invalid - " . $e->getMessage() . "\n";
        }
        
        // API encryption
        $apiData = [
            'status' => 'success',
            'data' => ['message' => 'Secure API response']
        ];
        
        $encryptedResponse = $this->api->encryptAPIResponse($apiData);
        echo "\nEncrypted API response: " . substr($encryptedResponse, 0, 50) . "...\n";
        
        // Note: In a real scenario, the client would decrypt this
        $decryptedResponse = $this->api->decryptAPIRequest($encryptedResponse);
        echo "Decrypted response: " . json_encode($decryptedResponse) . "\n";
    }
    
    public function demonstrateComprehensiveProtection(): void {
        echo "\nComprehensive Data Protection Example\n";
        echo str_repeat("-", 40) . "\n";
        
        // Simulate processing sensitive user data
        $rawUserData = [
            'id' => 123,
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '(555) 123-4567',
            'ssn' => '123-45-6789',
            'credit_card' => '4111-1111-1111-1111',
            'address' => '123 Main St, Anytown, USA',
            'preferences' => [
                'newsletter' => true,
                'theme' => 'dark',
                'privacy_level' => 'high'
            ]
        ];
        
        echo "Processing user data with comprehensive protection:\n";
        echo "Original data: " . json_encode($rawUserData, JSON_PRETTY_PRINT) . "\n\n";
        
        // Step 1: Mask sensitive fields for logging
        $maskingRules = [
            'email' => 'email',
            'phone' => 'phone',
            'ssn' => 'ssn',
            'credit_card' => 'credit_card'
        ];
        
        $maskedData = $this->masker->maskData($rawUserData, $maskingRules);
        echo "1. Masked for logging: " . json_encode($maskedData) . "\n\n";
        
        // Step 2: Encrypt for storage
        $storageKey = 'user_' . $rawUserData['id'];
        $stored = $this->storage->store($storageKey, $rawUserData);
        echo "2. Encrypted storage: " . ($stored ? '✅ Success' : '❌ Failed') . "\n\n";
        
        // Step 3: Generate data hash for integrity
        $dataHash = $this->hash->hash(serialize($rawUserData));
        echo "3. Data integrity hash: " . substr($dataHash, 0, 32) . "...\n\n";
        
        // Step 4: Generate API token for access
        $apiToken = $this->api->generateJWT([
            'user_id' => $rawUserData['id'],
            'access_level' => 'user',
            'data_hash' => $dataHash
        ]);
        
        echo "4. API access token: " . substr($apiToken, 0, 50) . "...\n\n";
        
        // Step 5: Retrieve and verify
        $retrievedData = $this->storage->retrieve($storageKey);
        $retrievedHash = $this->hash->hash(serialize($retrievedData));
        
        echo "5. Data integrity check: " . ($this->hash->verify(serialize($retrievedData), $dataHash) ? '✅ Valid' : '❌ Invalid') . "\n";
        echo "   Retrieved data matches: " . (json_encode($rawUserData) === json_encode($retrievedData) ? '✅ Yes' : '❌ No') . "\n\n";
        
        // Step 6: Clean up
        $this->storage->delete($storageKey);
        echo "6. Secure cleanup: ✅ Completed\n";
    }
    
    public function runAllExamples(): void {
        echo "Data Protection Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateEncryption();
        $this->demonstrateHashing();
        $this->demonstrateDataMasking();
        $this->demonstrateSecureStorage();
        $this->demonstrateAPISecurity();
        $this->demonstrateComprehensiveProtection();
        
        // Cleanup storage directory
        $this->cleanupStorage();
    }
    
    private function cleanupStorage(): void {
        $storagePath = './demo_storage';
        if (is_dir($storagePath)) {
            $files = glob($storagePath . '/*.dat');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($storagePath);
        }
    }
}

// Data Protection Best Practices
function printDataProtectionBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Data Protection Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Encryption:\n";
    echo "   • Use strong encryption algorithms\n";
    echo "   • Generate secure random keys\n";
    echo "   • Use proper IV generation\n";
    echo "   • Encrypt data at rest and in transit\n";
    echo "   • Regularly rotate encryption keys\n\n";
    
    echo "2. Hashing:\n";
    echo "   • Use salted hashes for passwords\n";
    echo "   • Use appropriate hash algorithms\n";
    echo "   • Implement proper key derivation\n";
    echo "   • Verify data integrity\n";
    echo "   • Use different salts per hash\n\n";
    
    echo "3. Data Masking:\n";
    echo "   • Mask sensitive data in logs\n";
    echo "   • Use consistent masking rules\n";
    echo "   • Preserve data format\n";
    echo "   • Implement reversible masking\n";
    echo "   • Apply masking at multiple levels\n\n";
    
    echo "4. Secure Storage:\n";
    echo "   • Use encrypted file systems\n";
    echo "   • Implement proper permissions\n";
    echo "   • Store encryption keys securely\n";
    echo "   • Use secure backup strategies\n";
    echo "   • Implement data retention policies\n\n";
    
    echo "5. API Security:\n";
    echo "   • Use API keys and tokens\n";
    echo "   • Implement rate limiting\n";
    echo "   • Validate origins and requests\n";
    echo "   • Use HTTPS everywhere\n";
    echo "   • Implement proper authentication\n\n";
    
    echo "6. Compliance:\n";
    echo "   • Follow GDPR requirements\n";
    echo "   • Implement data minimization\n";
    echo "   • Provide data access controls\n";
    echo "   • Maintain audit trails\n";
    echo "   • Regular security assessments";
}

// Main execution
function runDataProtectionDemo(): void {
    $examples = new DataProtectionExamples();
    $examples->runAllExamples();
    printDataProtectionBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDataProtectionDemo();
}
?>
