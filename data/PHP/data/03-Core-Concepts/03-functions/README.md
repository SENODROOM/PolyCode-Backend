# Module 3: Functions 🔧

Functions are reusable blocks of code that perform specific tasks. They help organize your code, reduce repetition, and make your programs more maintainable.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand how to create and call functions
- Work with function parameters and arguments
- Use return values effectively
- Understand variable scope
- Create built-in and custom functions
- Handle function overloading and default values

## 📝 Topics Covered

1. [Creating Functions](#creating-functions)
2. [Function Parameters](#function-parameters)
3. [Return Values](#return-values)
4. [Variable Scope](#variable-scope)
5. [Built-in Functions](#built-in-functions)
6. [Advanced Function Concepts](#advanced-function-concepts)
7. [Practical Examples](#practical-examples)
8. [Exercises](#exercises)

---

## Creating Functions

### Basic Function Definition
```php
<?php
    function sayHello() {
        echo "Hello, World!";
    }
    
    // Call the function
    sayHello();
?>
```

### Function with Parameters
```php
<?php
    function greet($name) {
        echo "Hello, " . $name . "!";
    }
    
    greet("John");  // Outputs: Hello, John!
    greet("Jane");  // Outputs: Hello, Jane!
?>
```

### Function Naming Rules
- Function names must start with a letter or underscore
- Can contain letters, numbers, and underscores
- Case-insensitive (but best practice is consistent)
- Use descriptive names

```php
<?php
    function calculate_total() { }      // Good: descriptive
    function getUserData() { }         // Good: camelCase
    function get_user_data() { }       // Good: snake_case
    function a() { }                   // Bad: not descriptive
    function 123function() { }         // Bad: starts with number
?>
```

---

## Function Parameters

### Required Parameters
```php
<?php
    function add($a, $b) {
        return $a + $b;
    }
    
    echo add(5, 3);  // Outputs: 8
    // add(5); would cause an error
?>
```

### Optional Parameters with Default Values
```php
<?php
    function greet($name, $greeting = "Hello") {
        echo "$greeting, $name!";
    }
    
    greet("John");                    // Outputs: Hello, John!
    greet("Jane", "Good morning");    // Outputs: Good morning, Jane!
?>
```

### Multiple Optional Parameters
```php
<?php
    function createProfile($name, $age = 25, $city = "Unknown") {
        echo "Name: $name, Age: $age, City: $city";
    }
    
    createProfile("John");                           // Name: John, Age: 25, City: Unknown
    createProfile("Jane", 30);                       // Name: Jane, Age: 30, City: Unknown
    createProfile("Bob", 35, "New York");            // Name: Bob, Age: 35, City: New York
?>
```

### Type Hinting (PHP 7+)
```php
<?php
    function addNumbers(int $a, int $b): int {
        return $a + $b;
    }
    
    function processString(string $text): string {
        return strtoupper($text);
    }
    
    function processArray(array $data): array {
        return array_map('strtoupper', $data);
    }
?>
```

---

## Return Values

### Single Return Value
```php
<?php
    function multiply($a, $b) {
        return $a * $b;
    }
    
    $result = multiply(4, 5);
    echo $result;  // Outputs: 20
?>
```

### Multiple Return Points
```php
<?php
    function getGrade($score) {
        if ($score >= 90) {
            return "A";
        } elseif ($score >= 80) {
            return "B";
        } elseif ($score >= 70) {
            return "C";
        } else {
            return "F";
        }
    }
    
    echo getGrade(85);  // Outputs: B
?>
```

### Returning Arrays
```php
<?php
    function getUserInfo() {
        return [
            "name" => "John Doe",
            "age" => 25,
            "email" => "john@example.com"
        ];
    }
    
    $user = getUserInfo();
    echo $user["name"];  // Outputs: John Doe
?>
```

### Returning Multiple Values (List Destructuring)
```php
<?php
    function getCoordinates() {
        return [10, 20];
    }
    
    // PHP 7.1+
    [$x, $y] = getCoordinates();
    echo "X: $x, Y: $y";  // Outputs: X: 10, Y: 20
    
    // Older PHP
    $coords = getCoordinates();
    echo "X: " . $coords[0] . ", Y: " . $coords[1];
?>
```

---

## Variable Scope

### Global Scope
```php
<?php
    $global_var = "I am global";
    
    function showGlobal() {
        global $global_var;
        echo $global_var;  // Outputs: I am global
    }
    
    showGlobal();
?>
```

### Local Scope
```php
<?php
    function testScope() {
        $local_var = "I am local";
        echo $local_var;  // Works inside function
    }
    
    testScope();
    // echo $local_var;  // Error: undefined variable
?>
```

### Static Variables
```php
<?php
    function counter() {
        static $count = 0;
        $count++;
        echo $count . " ";
    }
    
    counter();  // Outputs: 1
    counter();  // Outputs: 2
    counter();  // Outputs: 3
?>
```

### Superglobals
```php
<?php
    function showSuperglobals() {
        echo "Server name: " . $_SERVER['SERVER_NAME'] . "<br>";
        echo "Request method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
        
        if (isset($_GET['name'])) {
            echo "Hello, " . $_GET['name'];
        }
    }
?>
```

---

## Built-in Functions

### String Functions
```php
<?php
    $text = "Hello World";
    
    echo strlen($text);              // 11
    echo strtoupper($text);         // HELLO WORLD
    echo strtolower($text);         // hello world
    echo str_replace("World", "PHP", $text);  // Hello PHP
    echo strpos($text, "World");     // 6
    echo substr($text, 0, 5);        // Hello
?>
```

### Array Functions
```php
<?php
    $fruits = ["Apple", "Banana", "Orange"];
    
    echo count($fruits);            // 3
    echo sort($fruits);             // Sorts array
    echo in_array("Apple", $fruits); // 1 (true)
    echo array_push($fruits, "Grape"); // Add element
    echo array_pop($fruits);        // Remove last element
?>
```

### Math Functions
```php
<?php
    echo rand(1, 100);              // Random number 1-100
    echo round(3.14159, 2);         // 3.14
    echo ceil(3.2);                 // 4
    echo floor(3.8);                // 3
    echo abs(-5);                   // 5
    echo pow(2, 3);                 // 8
    echo sqrt(16);                  // 4
?>
```

### Date/Time Functions
```php
<?php
    echo date("Y-m-d H:i:s");       // Current date and time
    echo strtotime("tomorrow");     // Timestamp for tomorrow
    echo date("l", time());         // Full day name
    echo mktime(12, 0, 0, 1, 1, 2024); // Create timestamp
?>
```

---

## Advanced Function Concepts

### Anonymous Functions (Closures)
```php
<?php
    $greet = function($name) {
        echo "Hello, $name!";
    };
    
    $greet("World");  // Outputs: Hello, World!
    
    // With array functions
    $numbers = [1, 2, 3, 4, 5];
    $doubled = array_map(function($n) {
        return $n * 2;
    }, $numbers);
    
    print_r($doubled);  // [2, 4, 6, 8, 10]
?>
```

### Arrow Functions (PHP 7.4+)
```php
<?php
    $numbers = [1, 2, 3, 4, 5];
    
    // Traditional closure
    $squared = array_map(function($n) {
        return $n * $n;
    }, $numbers);
    
    // Arrow function
    $squared = array_map(fn($n) => $n * $n, $numbers);
    
    print_r($squared);  // [1, 4, 9, 16, 25]
?>
```

### Variable Functions
```php
<?php
    function add($a, $b) {
        return $a + $b;
    }
    
    function subtract($a, $b) {
        return $a - $b;
    }
    
    $operation = "add";
    echo $operation(5, 3);  // Outputs: 8
    
    $operation = "subtract";
    echo $operation(5, 3);  // Outputs: 2
?>
```

### Recursive Functions
```php
<?php
    function factorial($n) {
        if ($n <= 1) {
            return 1;
        }
        return $n * factorial($n - 1);
    }
    
    echo factorial(5);  // Outputs: 120 (5 * 4 * 3 * 2 * 1)
?>
```

---

## Practical Examples

### Example 1: Calculator Class
```php
<?php
    function calculator($operation, $a, $b) {
        switch ($operation) {
            case "add":
                return $a + $b;
            case "subtract":
                return $a - $b;
            case "multiply":
                return $a * $b;
            case "divide":
                return $b != 0 ? $a / $b : "Cannot divide by zero";
            default:
                return "Invalid operation";
        }
    }
    
    echo "<h2>Calculator</h2>";
    echo "5 + 3 = " . calculator("add", 5, 3) . "<br>";
    echo "5 - 3 = " . calculator("subtract", 5, 3) . "<br>";
    echo "5 * 3 = " . calculator("multiply", 5, 3) . "<br>";
    echo "5 / 3 = " . calculator("divide", 5, 3) . "<br>";
?>
```

### Example 2: String Validator
```php
<?php
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    function validatePassword($password) {
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }
    
    function validateInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    echo "<h2>Input Validation</h2>";
    
    $emails = ["test@example.com", "invalid-email", "user@domain.org"];
    foreach ($emails as $email) {
        echo "$email: " . (validateEmail($email) ? "Valid" : "Invalid") . "<br>";
    }
    
    $passwords = ["password", "Password123", "pass", "SecurePass1"];
    foreach ($passwords as $pass) {
        echo "$pass: " . (validatePassword($pass) ? "Strong" : "Weak") . "<br>";
    }
?>
```

### Example 3: File Operations
```php
<?php
    function readConfig($filename) {
        if (!file_exists($filename)) {
            return "Config file not found";
        }
        
        $config = file_get_contents($filename);
        return parse_ini_string($config);
    }
    
    function writeLog($message, $level = "INFO") {
        $timestamp = date("Y-m-d H:i:s");
        $logEntry = "[$timestamp] [$level] $message\n";
        
        file_put_contents("app.log", $logEntry, FILE_APPEND);
        return "Log entry added";
    }
    
    function createBackup($filename) {
        if (!file_exists($filename)) {
            return "File not found";
        }
        
        $backupName = $filename . ".backup." . date("YmdHis");
        return copy($filename, $backupName) ? "Backup created: $backupName" : "Backup failed";
    }
    
    echo "<h2>File Operations</h2>";
    echo writeLog("Application started");
    echo "<br>" . writeLog("User logged in", "SUCCESS");
    echo "<br>" . writeLog("Database error", "ERROR");
?>
```

---

## Exercises

### Exercise 1: Temperature Converter Function
Create a PHP file that:
1. Creates functions to convert between Celsius and Fahrenheit
2. Uses default parameters for rounding
3. Handles edge cases

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: String Manipulation Functions
Create a PHP file that:
1. Creates functions to validate and format strings
2. Uses regular expressions for validation
3. Returns formatted results

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Array Utility Functions
Create a PHP file that:
1. Creates functions to work with arrays
2. Includes sorting, filtering, and searching
3. Uses built-in array functions effectively

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I can create and call functions
- [ ] I understand function parameters and default values
- [ ] I can use return values effectively
- [ ] I understand variable scope
- [ ] I can use built-in PHP functions
- [ ] I can create anonymous functions
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 4: Arrays](../04-arrays/README.md)
