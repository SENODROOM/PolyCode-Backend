<?php
/**
 * Advanced PHP Features
 * 
 * This file demonstrates advanced PHP 8+ features, type system,
 * attributes, JIT compilation, and cutting-edge PHP capabilities.
 */

// PHP 8.0+ Union Types
class UnionTypesExample
{
    private int|string $id;
    private string|float|null $value;
    
    public function __construct(int|string $id, string|float|null $value = null)
    {
        $this->id = $id;
        $this->value = $value;
    }
    
    public function getId(): int|string
    {
        return $this->id;
    }
    
    public function getValue(): string|float|null
    {
        return $this->value;
    }
    
    public function processValue(int|float $value): string
    {
        return "Processed: " . $value;
    }
}

// PHP 8.0+ Named Arguments
class NamedArgumentsExample
{
    public function createUser(
        string $name,
        string $email,
        ?string $phone = null,
        bool $active = true,
        array $roles = []
    ): array {
        return [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'active' => $active,
            'roles' => $roles
        ];
    }
    
    public function demonstrateNamedArguments(): void
    {
        // Traditional positional arguments
        $user1 = $this->createUser('John', 'john@example.com', '555-1234', true, ['admin']);
        
        // Named arguments (order doesn't matter)
        $user2 = $this->createUser(
            email: 'jane@example.com',
            name: 'Jane',
            active: false,
            roles: ['user']
        );
        
        // Skip optional parameters
        $user3 = $this->createUser(
            name: 'Bob',
            email: 'bob@example.com',
            roles: ['editor']
        );
        
        echo "User 1: " . json_encode($user1) . "\n";
        echo "User 2: " . json_encode($user2) . "\n";
        echo "User 3: " . json_encode($user3) . "\n";
    }
}

// PHP 8.0+ Match Expression
class MatchExpressionExample
{
    public function getHttpStatus(int $code): string
    {
        return match($code) {
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            default => 'Unknown Status'
        };
    }
    
    public function getUserType(string $role): string
    {
        return match($role) {
            'admin', 'superadmin' => 'Administrator',
            'editor', 'moderator' => 'Content Manager',
            'user', 'member' => 'Regular User',
            'guest', 'visitor' => 'Guest',
            default => 'Unknown Role'
        };
    }
    
    public function calculateShippingCost(float $weight, string $zone): float
    {
        return match(true) {
            $weight < 1 => 5.00,
            $weight < 5 => 10.00,
            $weight < 10 => 15.00,
            default => 20.00
        } + match($zone) {
            'domestic' => 0,
            'international' => 25.00,
            'express' => 50.00,
            default => 10.00
        };
    }
}

// PHP 8.0+ Nullsafe Operator
class NullsafeOperatorExample
{
    private ?User $user;
    private ?Address $address;
    private ?Country $country;
    
    public function __construct(?User $user = null)
    {
        $this->user = $user;
        $this->address = $user?->getAddress();
        $this->country = $this->address?->getCountry();
    }
    
    public function getUserName(): ?string
    {
        // Traditional null checking
        if ($this->user !== null) {
            return $this->user->getName();
        }
        return null;
    }
    
    public function getUserNameNullsafe(): ?string
    {
        // Using nullsafe operator
        return $this->user?->getName();
    }
    
    public function getCountryName(): ?string
    {
        // Chained nullsafe operations
        return $this->user?->getAddress()?->getCountry()?->getName();
    }
    
    public function getFullLocation(): ?string
    {
        return $this->user?->getAddress()?->getCountry()?->getName() . 
               ', ' . 
               $this->user?->getAddress()?->getCity();
    }
}

// PHP 8.0+ Constructor Property Promotion
class ConstructorPromotionExample
{
    public function function __construct(
        private string $name,
        private string $email,
        private int $age,
        private array $roles = [],
        private bool $active = true
    ) {}
    
    public function getUserInfo(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'roles' => $this->roles,
            'active' => $this->active
        ];
    }
    
    public function updateEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function addRole(string $role): void
    {
        $this->roles[] = $role;
    }
    
    public function deactivate(): void
    {
        $this->active = false;
    }
}

// PHP 8.1+ Enums
enum Status: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::COMPLETED => 'Completed'
        };
    }
    
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::PENDING => in_array($newStatus, [self::APPROVED, self::REJECTED]),
            self::APPROVED => $newStatus === self::COMPLETED,
            self::REJECTED => false,
            self::COMPLETED => false
        };
    }
}

enum UserRole: int
{
    case GUEST = 0;
    case USER = 1;
    case MODERATOR = 2;
    case ADMIN = 3;
    case SUPERADMIN = 4;
    
    public function getPermissions(): array
    {
        return match($this) {
            self::GUEST => ['read'],
            self::USER => ['read', 'comment'],
            self::MODERATOR => ['read', 'comment', 'moderate'],
            self::ADMIN => ['read', 'comment', 'moderate', 'admin'],
            self::SUPERADMIN => ['*']
        };
    }
    
    public function can(string $permission): bool
    {
        return in_array($permission, $this->getPermissions()) || 
               in_array('*', $this->getPermissions());
    }
}

enum Priority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case URGENT = 4;
    case CRITICAL = 5;
    
    public function getColor(): string
    {
        return match($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
            self::CRITICAL => 'purple'
        };
    }
    
    public function getWeight(): int
    {
        return $this->value;
    }
}

// PHP 8.1+ Fibers (Coroutines)
class FiberExample
{
    public function demonstrateBasicFiber(): void
    {
        $fiber = new Fiber(function(): void {
            echo "Fiber started\n";
            
            $value = Fiber::suspend('First suspend');
            echo "Resumed with: $value\n";
            
            $value = Fiber::suspend('Second suspend');
            echo "Resumed again with: $value\n";
            
            echo "Fiber finished\n";
        });
        
        // Start the fiber
        $result = $fiber->start();
        echo "Suspended with: $result\n";
        
        // Resume the fiber
        $result = $fiber->resume('Hello from main!');
        echo "Suspended with: $result\n";
        
        // Resume again
        $result = $fiber->resume('Final message');
        echo "Fiber completed with: $result\n";
    }
    
    public function createTaskFiber(string $taskName, int $duration): Fiber
    {
        return new Fiber(function() use ($taskName, $duration): string {
            echo "Task '$taskName' started\n";
            
            for ($i = 0; $i < $duration; $i++) {
                echo "Task '$taskName' progress: " . (($i + 1) / $duration * 100) . "%\n";
                Fiber::suspend($i + 1);
            }
            
            echo "Task '$taskName' completed\n";
            return "Task '$taskName' result";
        });
    }
    
    public function runMultipleTasks(): void
    {
        $tasks = [
            $this->createTaskFiber('Data Processing', 5),
            $this->createTaskFiber('Image Generation', 3),
            $this->createTaskFiber('Report Generation', 4)
        ];
        
        $running = $tasks;
        $results = [];
        
        while (!empty($running)) {
            foreach ($running as $index => $fiber) {
                if (!$fiber->isStarted()) {
                    $fiber->start();
                } elseif (!$fiber->isTerminated()) {
                    $fiber->resume();
                } else {
                    $results[$index] = $fiber->getReturn();
                    unset($running[$index]);
                }
            }
        }
        
        echo "\nAll tasks completed!\n";
        foreach ($results as $result) {
            echo "Result: $result\n";
        }
    }
}

// PHP 8.1+ Readonly Properties
class ReadonlyPropertiesExample
{
    public readonly string $id;
    public readonly DateTimeImmutable $createdAt;
    public readonly array $metadata;
    
    public function __construct(string $id, array $metadata = [])
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->metadata = $metadata;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    // This would cause an error - readonly properties cannot be modified
    // public function setId(string $id): void {
    //     $this->id = $id; // Fatal error
    // }
}

// PHP 8.2+ Standalone Types (true, false, null)
class StandaloneTypesExample
{
    public function processValue(mixed $value): string
    {
        return match($value) {
            true => 'Boolean true',
            false => 'Boolean false',
            null => 'Null value',
            default => 'Other type: ' . gettype($value)
        };
    }
    
    public function validateInput(string|null $input): bool|null
    {
        if ($input === null) {
            return null;
        }
        
        return match($input) {
            'true', '1', 'yes' => true,
            'false', '0', 'no' => false,
            default => null
        };
    }
    
    public function getConfigValue(string $key): mixed
    {
        $config = [
            'debug' => true,
            'production' => false,
            'cache_enabled' => true,
            'timeout' => null
        ];
        
        return $config[$key] ?? null;
    }
}

// PHP 8.2+ Disjunctive Normal Form Types
class DNFTypesExample
{
    public function processValue((int|string) $value): string
    {
        return match(true) {
            is_int($value) => "Integer: $value",
            is_string($value) => "String: $value",
            default => "Unknown type"
        };
    }
    
    public function handleRequest((GET|POST) $method): void
    {
        match($method) {
            GET => $this->handleGetRequest(),
            POST => $this->handlePostRequest()
        };
    }
    
    private function handleGetRequest(): void
    {
        echo "Handling GET request\n";
    }
    
    private function handlePostRequest(): void
    {
        echo "Handling POST request\n";
    }
    
    public function validateData((array|object) $data): bool
    {
        return match(true) {
            is_array($data) => $this->validateArray($data),
            is_object($data) => $this->validateObject($data),
            default => false
        };
    }
    
    private function validateArray(array $data): bool
    {
        return !empty($data);
    }
    
    private function validateObject(object $data): bool
    {
        return method_exists($data, 'validate') ? $data->validate() : true;
    }
}

// PHP 8.3+ Typed Class Constants
class TypedConstantsExample
{
    public const string APP_NAME = 'MyApp';
    public const int MAX_USERS = 1000;
    public const float TAX_RATE = 0.08;
    public const bool DEBUG_MODE = true;
    public const array DEFAULT_CONFIG = ['key' => 'value'];
    
    public function getAppInfo(): array
    {
        return [
            'name' => self::APP_NAME,
            'max_users' => self::MAX_USERS,
            'tax_rate' => self::TAX_RATE,
            'debug_mode' => self::DEBUG_MODE,
            'config' => self::DEFAULT_CONFIG
        ];
    }
    
    public function calculateTax(float $amount): float
    {
        return $amount * self::TAX_RATE;
    }
    
    public function canAddUser(int $currentUsers): bool
    {
        return $currentUsers < self::MAX_USERS;
    }
}

// Advanced Attributes
#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(public string $name) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $type,
        public bool $nullable = false,
        public ?int $maxLength = null
    ) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey
{
    public function __construct(public bool $autoIncrement = true) {}
}

#[Attribute(Attribute::TARGET_CLASS)]
class ApiResource
{
    public function __construct(
        public string $endpoint,
        public array $methods = ['GET', 'POST', 'PUT', 'DELETE']
    ) {}
}

// Example with attributes
#[Table('users')]
#[ApiResource('/api/users')]
class User
{
    #[Column('int')]
    #[PrimaryKey]
    public int $id;
    
    #[Column('string', maxLength: 255)]
    public string $name;
    
    #[Column('string', maxLength: 255)]
    public string $email;
    
    #[Column('datetime')]
    public DateTime $createdAt;
    
    #[Column('text', nullable: true)]
    public ?string $bio;
}

// Attribute Reader
class AttributeReader
{
    public function getTableAttributes(object $object): ?Table
    {
        $reflection = new ReflectionClass($object);
        $attributes = $reflection->getAttributes(Table::class);
        
        return $attributes[0] ?? null;
    }
    
    public function getPropertyAttributes(object $object, string $property): array
    {
        $reflection = new ReflectionProperty($object, $property);
        
        return array_map(
            fn($attr) => $attr->newInstance(),
            $reflection->getAttributes()
        );
    }
    
    public function getAllAttributes(object $object): array
    {
        $attributes = [];
        
        // Class attributes
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getAttributes() as $attr) {
            $attributes['class'][] = $attr->newInstance();
        }
        
        // Property attributes
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attr) {
                $attributes['properties'][$property->getName()][] = $attr->newInstance();
            }
        }
        
        return $attributes;
    }
    
    public function generateSQL(object $object): string
    {
        $tableAttr = $this->getTableAttributes($object);
        if (!$tableAttr) {
            return 'No table attribute found';
        }
        
        $sql = "CREATE TABLE {$tableAttr->name} (\n";
        
        $reflection = new ReflectionClass($object);
        $columns = [];
        
        foreach ($reflection->getProperties() as $property) {
            $propertyAttrs = $this->getPropertyAttributes($object, $property->getName());
            
            $columnDef = '';
            $primaryKey = false;
            
            foreach ($propertyAttrs as $attr) {
                if ($attr instanceof Column) {
                    $columnDef = $property->getName() . ' ' . $attr->type;
                    
                    if ($attr->maxLength) {
                        $columnDef .= "({$attr->maxLength})";
                    }
                    
                    if (!$attr->nullable) {
                        $columnDef .= ' NOT NULL';
                    }
                } elseif ($attr instanceof PrimaryKey) {
                    $primaryKey = true;
                }
            }
            
            if ($columnDef) {
                $columns[] = '    ' . $columnDef . ($primaryKey ? ' PRIMARY KEY' : '');
            }
        }
        
        $sql .= implode(",\n", $columns);
        $sql .= "\n);";
        
        return $sql;
    }
}

// JIT Compilation Simulation
class JITCompilationExample
{
    private bool $jitEnabled = false;
    private array $jitStats = [
        'compiled_functions' => 0,
        'execution_count' => 0,
        'optimizations' => 0
    ];
    
    public function enableJIT(): void
    {
        $this->jitEnabled = true;
        echo "JIT compilation enabled\n";
    }
    
    public function disableJIT(): void
    {
        $this->jitEnabled = false;
        echo "JIT compilation disabled\n";
    }
    
    public function executeFunction(callable $function, array $args = [])
    {
        $this->jitStats['execution_count']++;
        
        if ($this->jitEnabled && $this->jitStats['execution_count'] > 10) {
            // Simulate JIT compilation
            $this->jitStats['compiled_functions']++;
            $this->jitStats['optimizations']++;
            
            echo "Function compiled with JIT optimizations\n";
        }
        
        return $function(...$args);
    }
    
    public function getJITStats(): array
    {
        return $this->jitStats;
    }
    
    public function benchmarkFunction(callable $function, int $iterations = 1000): array
    {
        $results = [];
        
        // Benchmark without JIT
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $function();
        }
        $results['without_jit'] = microtime(true) - $start;
        
        // Benchmark with JIT
        $this->enableJIT();
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $this->executeFunction($function);
        }
        $results['with_jit'] = microtime(true) - $start;
        
        $results['improvement'] = (($results['without_jit'] - $results['with_jit']) / $results['without_jit']) * 100;
        
        return $results;
    }
}

// Advanced Type System
class AdvancedTypeSystem
{
    public function processValue(mixed $value): string
    {
        return match(true) {
            $value is int => "Integer: $value",
            $value is float => "Float: $value",
            $value is string => "String: $value",
            $value is bool => "Boolean: " . ($value ? 'true' : 'false'),
            $value is array => "Array with " . count($value) . " items",
            $value is object => "Object of " . get_class($value),
            $value is null => "Null value",
            default => "Unknown type"
        };
    }
    
    public function validateInput(mixed $input): bool
    {
        return match(true) {
            $input is int && $input > 0 => true,
            $input is string && strlen($input) > 0 => true,
            $input is array && !empty($input) => true,
            default => false
        };
    }
    
    public function processCollection(iterable $collection): int
    {
        $count = 0;
        foreach ($collection as $item) {
            $count++;
        }
        return $count;
    }
    
    public function handleRequest(object $request): void
    {
        match($request::class) {
            'GetRequest' => $this->handleGet($request),
            'PostRequest' => $this->handlePost($request),
            'PutRequest' => $this->handlePut($request),
            'DeleteRequest' => $this->handleDelete($request),
            default => throw new InvalidArgumentException('Unknown request type')
        };
    }
    
    private function handleGet(object $request): void
    {
        echo "Handling GET request\n";
    }
    
    private function handlePost(object $request): void
    {
        echo "Handling POST request\n";
    }
    
    private function handlePut(object $request): void
    {
        echo "Handling PUT request\n";
    }
    
    private function handleDelete(object $request): void
    {
        echo "Handling DELETE request\n";
    }
}

// Advanced PHP Features Examples
class AdvancedPHPFeaturesExamples
{
    private UnionTypesExample $unionTypes;
    private NamedArgumentsExample $namedArguments;
    private MatchExpressionExample $matchExpression;
    private NullsafeOperatorExample $nullsafeOperator;
    private ConstructorPromotionExample $constructorPromotion;
    private FiberExample $fiberExample;
    private ReadonlyPropertiesExample $readonlyProperties;
    private StandaloneTypesExample $standaloneTypes;
    private DNFTypesExample $dnfTypes;
    private TypedConstantsExample $typedConstants;
    private AttributeReader $attributeReader;
    private JITCompilationExample $jitCompilation;
    private AdvancedTypeSystem $typeSystem;
    
    public function __construct()
    {
        $this->unionTypes = new UnionTypesExample();
        $this->namedArguments = new NamedArgumentsExample();
        $this->matchExpression = new MatchExpressionExample();
        $this->nullsafeOperator = new NullsafeOperatorExample();
        $this->constructorPromotion = new ConstructorPromotionExample('John', 'john@example.com', 25);
        $this->fiberExample = new FiberExample();
        $this->readonlyProperties = new ReadonlyPropertiesExample('user123', ['role' => 'admin']);
        $this->standaloneTypes = new StandaloneTypesExample();
        $this->dnfTypes = new DNFTypesExample();
        $this->typedConstants = new TypedConstantsExample();
        $this->attributeReader = new AttributeReader();
        $this->jitCompilation = new JITCompilationExample();
        $this->typeSystem = new AdvancedTypeSystem();
    }
    
    public function demonstrateUnionTypes(): void
    {
        echo "Union Types Example\n";
        echo str_repeat("-", 20) . "\n";
        
        $obj = new UnionTypesExample(123, 'test value');
        echo "ID: {$obj->getId()}\n";
        echo "Value: {$obj->getValue()}\n";
        
        $obj2 = new UnionTypesExample('abc', 45.67);
        echo "ID: {$obj2->getId()}\n";
        echo "Value: {$obj2->getValue()}\n";
        
        echo "Processed: " . $obj->processValue(789) . "\n";
        echo "Processed: " . $obj->processValue('hello') . "\n";
    }
    
    public function demonstrateNamedArguments(): void
    {
        echo "\nNamed Arguments Example\n";
        echo str_repeat("-", 28) . "\n";
        
        $this->namedArguments->demonstrateNamedArguments();
    }
    
    public function demonstrateMatchExpression(): void
    {
        echo "\nMatch Expression Example\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "HTTP Status 200: " . $this->matchExpression->getHttpStatus(200) . "\n";
        echo "HTTP Status 404: " . $this->matchExpression->getHttpStatus(404) . "\n";
        echo "HTTP Status 999: " . $this->matchExpression->getHttpStatus(999) . "\n";
        
        echo "Role 'admin': " . $this->matchExpression->getUserType('admin') . "\n";
        echo "Role 'guest': " . $this->matchExpression->getUserType('guest') . "\n";
        
        echo "Shipping cost: " . $this->matchExpression->calculateShippingCost(2.5, 'international') . "\n";
        echo "Shipping cost: " . $this->matchExpression->calculateShippingCost(15.0, 'domestic') . "\n";
    }
    
    public function demonstrateNullsafeOperator(): void
    {
        echo "\nNullsafe Operator Example\n";
        echo str_repeat("-", 28) . "\n";
        
        echo "Traditional: " . ($this->nullsafeOperator->getUserName() ?? 'null') . "\n";
        echo "Nullsafe: " . ($this->nullsafeOperator->getUserNameNullsafe() ?? 'null') . "\n";
        echo "Chained: " . ($this->nullsafeOperator->getCountryName() ?? 'null') . "\n";
        echo "Complex: " . ($this->nullsafeOperator->getFullLocation() ?? 'null') . "\n";
    }
    
    public function demonstrateConstructorPromotion(): void
    {
        echo "\nConstructor Property Promotion Example\n";
        echo str_repeat("-", 42) . "\n";
        
        echo "User info: " . json_encode($this->constructorPromotion->getUserInfo()) . "\n";
        
        $this->constructorPromotion->updateEmail('newemail@example.com');
        $this->constructorPromotion->addRole('editor');
        $this->constructorPromotion->deactivate();
        
        echo "Updated info: " . json_encode($this->constructorPromotion->getUserInfo()) . "\n";
    }
    
    public function demonstrateEnums(): void
    {
        echo "\nEnums Example\n";
        echo str_repeat("-", 15) . "\n";
        
        // String enum
        $status = Status::APPROVED;
        echo "Status: " . $status->value . "\n";
        echo "Label: " . $status->getLabel() . "\n";
        echo "Can transition to COMPLETED: " . ($status->canTransitionTo(Status::COMPLETED) ? 'Yes' : 'No') . "\n";
        
        // Int enum
        $role = UserRole::ADMIN;
        echo "Role: " . $role->name . "\n";
        echo "Permissions: " . implode(', ', $role->getPermissions()) . "\n";
        echo "Can moderate: " . ($role->can('moderate') ? 'Yes' : 'No') . "\n";
        
        // Priority enum
        $priority = Priority::HIGH;
        echo "Priority: " . $priority->name . "\n";
        echo "Color: " . $priority->getColor() . "\n";
        echo "Weight: " . $priority->getWeight() . "\n";
    }
    
    public function demonstrateFibers(): void
    {
        echo "\nFibers (Coroutines) Example\n";
        echo str_repeat("-", 32) . "\n";
        
        echo "Basic Fiber:\n";
        $this->fiberExample->demonstrateBasicFiber();
        
        echo "\nMultiple Tasks:\n";
        $this->fiberExample->runMultipleTasks();
    }
    
    public function demonstrateReadonlyProperties(): void
    {
        echo "\nReadonly Properties Example\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "ID: " . $this->readonlyProperties->getId() . "\n";
        echo "Created At: " . $this->readonlyProperties->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
        echo "Metadata: " . json_encode($this->readonlyProperties->getMetadata()) . "\n";
        
        // This would cause an error
        // $this->readonlyProperties->id = 'new-id'; // Fatal error
    }
    
    public function demonstrateStandaloneTypes(): void
    {
        echo "\nStandalone Types Example\n";
        echo str_repeat("-", 28) . "\n";
        
        echo "true: " . $this->standaloneTypes->processValue(true) . "\n";
        echo "false: " . $this->standaloneTypes->processValue(false) . "\n";
        echo "null: " . $this->standaloneTypes->processValue(null) . "\n";
        
        echo "Validate 'yes': " . ($this->standaloneTypes->validateInput('yes') ? 'true' : 'false') . "\n";
        echo "Validate 'no': " . ($this->standaloneTypes->validateInput('no') ? 'true' : 'false') . "\n";
        
        echo "Debug config: " . ($this->standaloneTypes->getConfigValue('debug') ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateDNFTypes(): void
    {
        echo "\nDisjunctive Normal Form Types Example\n";
        echo str_repeat("-", 42) . "\n";
        
        echo "Process 123: " . $this->dnfTypes->processValue(123) . "\n";
        echo "Process 'hello': " . $this->dnfTypes->processValue('hello') . "\n";
        
        $this->dnfTypes->handleRequest(new GET());
        $this->dnfTypes->handleRequest(new POST());
        
        echo "Validate array: " . ($this->dnfTypes->validateData([1, 2, 3]) ? 'true' : 'false') . "\n";
        echo "Validate object: " . ($this->dnfTypes->validateData(new stdClass()) ? 'true' : 'false') . "\n";
    }
    
    public function demonstrateTypedConstants(): void
    {
        echo "\nTyped Constants Example\n";
        echo str_repeat("-", 26) . "\n";
        
        echo "App info: " . json_encode($this->typedConstants->getAppInfo()) . "\n";
        echo "Tax on $100: " . $this->typedConstants->calculateTax(100) . "\n";
        echo "Can add user (500/1000): " . ($this->typedConstants->canAddUser(500) ? 'Yes' : 'No') . "\n";
        echo "Can add user (1000/1000): " . ($this->typedConstants->canAddUser(1000) ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateAttributes(): void
    {
        echo "\nAttributes Example\n";
        echo str_repeat("-", 20) . "\n";
        
        $user = new User();
        
        $tableAttr = $this->attributeReader->getTableAttributes($user);
        echo "Table: " . ($tableAttr ? $tableAttr->name : 'None') . "\n";
        
        $idAttrs = $this->attributeReader->getPropertyAttributes($user, 'id');
        echo "ID attributes: " . count($idAttrs) . "\n";
        
        $allAttrs = $this->attributeReader->getAllAttributes($user);
        echo "Total class attributes: " . count($allAttrs['class']) . "\n";
        echo "Total property attributes: " . count($allAttrs['properties']) . "\n";
        
        echo "\nGenerated SQL:\n";
        echo $this->attributeReader->generateSQL($user) . "\n";
    }
    
    public function demonstrateJITCompilation(): void
    {
        echo "\nJIT Compilation Example\n";
        echo str_repeat("-", 25) . "\n";
        
        $testFunction = function() {
            return pow(2, 10);
        };
        
        $benchmark = $this->jitCompilation->benchmarkFunction($testFunction, 1000);
        
        echo "Without JIT: " . number_format($benchmark['without_jit'] * 1000, 2) . "ms\n";
        echo "With JIT: " . number_format($benchmark['with_jit'] * 1000, 2) . "ms\n";
        echo "Improvement: " . number_format($benchmark['improvement'], 2) . "%\n";
        
        echo "\nJIT Stats: " . json_encode($this->jitCompilation->getJITStats()) . "\n";
    }
    
    public function demonstrateAdvancedTypeSystem(): void
    {
        echo "\nAdvanced Type System Example\n";
        echo str_repeat("-", 32) . "\n";
        
        echo "Process 42: " . $this->typeSystem->processValue(42) . "\n";
        echo "Process 'test': " . $this->typeSystem->processValue('test') . "\n";
        echo "Process array: " . $this->typeSystem->processValue([1, 2, 3]) . "\n";
        
        echo "Validate 10: " . ($this->typeSystem->validateInput(10) ? 'true' : 'false') . "\n";
        echo "Validate 0: " . ($this->typeSystem->validateInput(0) ? 'true' : 'false') . "\n";
        
        echo "Collection count: " . $this->typeSystem->processCollection([1, 2, 3, 4, 5]) . "\n";
        
        $this->typeSystem->handleRequest(new GetRequest());
        $this->typeSystem->handleRequest(new PostRequest());
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced PHP Features Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateUnionTypes();
        $this->demonstrateNamedArguments();
        $this->demonstrateMatchExpression();
        $this->demonstrateNullsafeOperator();
        $this->demonstrateConstructorPromotion();
        $this->demonstrateEnums();
        $this->demonstrateFibers();
        $this->demonstrateReadonlyProperties();
        $this->demonstrateStandaloneTypes();
        $this->demonstrateDNFTypes();
        $this->demonstrateTypedConstants();
        $this->demonstrateAttributes();
        $this->demonstrateJITCompilation();
        $this->demonstrateAdvancedTypeSystem();
    }
}

// Supporting classes for examples
class User
{
    public ?Address $address;
    
    public function __construct(?Address $address = null)
    {
        $this->address = $address;
    }
    
    public function getName(): ?string
    {
        return 'John Doe';
    }
    
    public function getAddress(): ?Address
    {
        return $this->address;
    }
}

class Address
{
    public ?Country $country;
    public string $city = 'New York';
    
    public function __construct(?Country $country = null)
    {
        $this->country = $country;
    }
    
    public function getCountry(): ?Country
    {
        return $this->country;
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
}

class Country
{
    public string $name = 'USA';
    
    public function getName(): string
    {
        return $this->name;
    }
}

class GetRequest {}
class PostRequest {}
class PutRequest {}
class DeleteRequest {}

// Advanced PHP Features Best Practices
function printAdvancedPHPFeaturesBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Advanced PHP Features Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Union Types:\n";
    echo "   • Use for flexible parameter types\n";
    echo "   • Combine with null for optional values\n";
    echo "   • Keep unions simple and readable\n";
    echo "   • Document expected types\n";
    echo "   • Consider using interfaces instead\n\n";
    
    echo "2. Named Arguments:\n";
    echo "   • Use for complex function signatures\n";
    echo "   • Skip optional parameters\n";
    echo "   • Improve code readability\n";
    echo "   • Use in array functions\n";
    echo "   • Document parameter names\n\n";
    
    echo "3. Match Expressions:\n";
    echo "   • Replace complex switch statements\n";
    echo "   • Use exhaustive matching\n";
    echo "   • Handle all possible cases\n";
    echo "   • Keep match conditions simple\n";
    echo "   • Use for type checking\n\n";
    
    echo "4. Nullsafe Operator:\n";
    echo "   • Chain null checks safely\n";
    echo "   • Replace nested if statements\n";
    echo "   • Use for optional method calls\n";
    echo "   • Keep chains readable\n";
    echo "   • Don't overuse\n\n";
    
    echo "5. Constructor Property Promotion:\n";
    echo "   • Use for simple DTOs\n";
    echo "   • Reduce boilerplate code\n";
    echo "   • Keep constructors clean\n";
    echo "   • Document promoted properties\n";
    echo "   • Use with readonly when possible\n\n";
    
    echo "6. Enums:\n";
    echo "   • Use for fixed value sets\n";
    echo "   • Add methods for behavior\n";
    echo "   • Use backed enums for values\n";
    echo "   • Implement validation logic\n";
    echo "   • Use in type hints\n\n";
    
    echo "7. Fibers:\n";
    echo "   • Use for cooperative multitasking\n";
    echo "   • Implement coroutines\n";
    echo "   • Handle I/O operations\n";
    echo "   • Keep fibers simple\n";
    echo "   • Use proper error handling\n\n";
    
    echo "8. Readonly Properties:\n";
    echo "   • Use for immutable data\n";
    echo "   • Promote immutability\n";
    echo "   • Use in value objects\n";
    echo "   • Document readonly behavior\n";
    echo "   • Consider using const\n\n";
    
    echo "9. Attributes:\n";
    echo "   • Use for metadata\n";
    echo "   • Implement declarative programming\n";
    echo "   • Create custom attributes\n";
    echo "   • Use for framework integration\n";
    echo "   • Keep attributes simple\n\n";
    
    echo "10. Type System:\n";
    echo "   • Use strict typing\n";
    echo "   • Leverage DNF types\n";
    echo "   • Use standalone types\n";
    echo "   • Implement proper validation\n";
    echo "   • Document type constraints";
}

// Main execution
function runAdvancedPHPFeaturesDemo(): void
{
    $examples = new AdvancedPHPFeaturesExamples();
    $examples->runAllExamples();
    printAdvancedPHPFeaturesBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAdvancedPHPFeaturesDemo();
}
?>
