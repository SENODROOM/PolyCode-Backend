# Module 1: PHP Basics 📚

Welcome to your first step in learning PHP! This module covers the fundamental concepts you'll need to start writing PHP code.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand PHP syntax and structure
- Know how to declare and use variables
- Understand PHP data types
- Work with operators
- Use comments effectively
- Handle output with echo and print

## 📝 Topics Covered

1. [PHP Syntax](#php-syntax)
2. [Variables](#variables)
3. [Data Types](#data-types)
4. [Operators](#operators)
5. [Comments](#comments)
6. [Output Statements](#output-statements)
7. [Practical Examples](#practical-examples)
8. [Exercises](#exercises)

---

## PHP Syntax

PHP code is executed on the server, and the result is sent to the browser as plain HTML.

### Basic PHP Tags
```php
<?php
    // Your PHP code goes here
?>
```

### PHP in HTML
```php
<!DOCTYPE html>
<html>
<body>
    <h1>My PHP Page</h1>
    <?php
        echo "Hello from PHP!";
    ?>
</body>
</html>
```

### File Extension
PHP files must have a `.php` extension.

---

## Variables

Variables are containers for storing information.

### Rules for Variables
- Start with `$` sign
- Must start with a letter or underscore
- Can only contain letters, numbers, and underscores
- Case-sensitive (`$name` and `$NAME` are different)

### Declaring Variables
```php
<?php
    $name = "John Doe";
    $age = 25;
    $height = 5.9;
    $is_student = true;
    
    echo $name;  // Outputs: John Doe
    echo $age;   // Outputs: 25
?>
```

### Variable Examples
```php
<?php
    // String variable
    $greeting = "Welcome to PHP!";
    
    // Integer variable
    $year = 2024;
    
    // Float variable
    $price = 19.99;
    
    // Boolean variable
    $is_active = true;
    
    // Null variable
    $nothing = null;
?>
```

---

## Data Types

PHP supports several data types:

### Scalar Types
```php
<?php
    // String
    $text = "Hello World";
    $text2 = 'Hello World';
    
    // Integer
    $number = 42;
    
    // Float (double)
    $decimal = 3.14;
    
    // Boolean
    $is_true = true;
    $is_false = false;
?>
```

### Compound Types
```php
<?php
    // Array
    $fruits = array("Apple", "Banana", "Orange");
    $numbers = [1, 2, 3, 4, 5];
    
    // Object
    class Person {
        public $name = "John";
    }
    $person = new Person();
?>
```

### Special Types
```php
<?php
    // NULL
    $empty = null;
    
    // Resource (for file handles, database connections)
    $file = fopen("test.txt", "r");
?>
```

### Checking Data Types
```php
<?php
    $value = "Hello";
    
    // Get type
    echo gettype($value);  // Outputs: string
    
    // Check specific types
    var_dump(is_string($value));  // bool(true)
    var_dump(is_int($value));     // bool(false)
    var_dump(is_null($value));    // bool(false)
?>
```

---

## Operators

### Arithmetic Operators
```php
<?php
    $a = 10;
    $b = 3;
    
    echo $a + $b;  // Addition: 13
    echo $a - $b;  // Subtraction: 7
    echo $a * $b;  // Multiplication: 30
    echo $a / $b;  // Division: 3.333...
    echo $a % $b;  // Modulus: 1
    echo $a ** $b; // Exponentiation: 1000
?>
```

### Assignment Operators
```php
<?php
    $x = 10;
    $x += 5;   // $x = $x + 5 (15)
    $x -= 3;   // $x = $x - 3 (12)
    $x *= 2;   // $x = $x * 2 (24)
    $x /= 4;   // $x = $x / 4 (6)
    $x .= " text"; // Concatenation
?>
```

### Comparison Operators
```php
<?php
    $a = 10;
    $b = "10";
    
    var_dump($a == $b);   // Equal (true)
    var_dump($a === $b);  // Identical (false)
    var_dump($a != $b);   // Not equal (false)
    var_dump($a !== $b);  // Not identical (true)
    var_dump($a > $b);    // Greater than (false)
    var_dump($a < $b);    // Less than (false)
    var_dump($a >= $b);   // Greater than or equal (true)
    var_dump($a <= $b);   // Less than or equal (true)
?>
```

### Logical Operators
```php
<?php
    $x = true;
    $y = false;
    
    var_dump($x and $y);  // And (false)
    var_dump($x or $y);   // Or (true)
    var_dump($x xor $y);  // Xor (true)
    var_dump(!$x);        // Not (false)
    var_dump($x && $y);   // And (false)
    var_dump($x || $y);   // Or (true)
?>
```

### String Operators
```php
<?php
    $txt1 = "Hello";
    $txt2 = " World";
    
    echo $txt1 . $txt2;        // Concatenation: Hello World
    echo $txt1 .= $txt2;       // Concatenation assignment
?>
```

---

## Comments

Comments are used to make code more readable.

### Single Line Comments
```php
<?php
    // This is a single line comment
    # This is also a single line comment
    echo "Hello World"; // This comment follows a statement
?>
```

### Multi-line Comments
```php
<?php
    /*
    This is a multi-line comment
    that spans across multiple lines
    */
    echo "Hello World";
?>
```

---

## Output Statements

### echo Statement
```php
<?php
    echo "Hello World";
    echo "Hello", " ", "World";  // Multiple strings
    $txt = "Hello";
    echo $txt . " World";       // With variables
?>
```

### print Statement
```php
<?php
    print "Hello World";        // Similar to echo
    $result = print "Hello";    // Returns 1
?>
```

### echo vs print
- `echo` is faster than `print`
- `echo` can take multiple parameters
- `print` always returns 1
- `echo` is an expression, `print` is a language construct

---

## Practical Examples

### Example 1: Personal Information
```php
<?php
    $first_name = "John";
    $last_name = "Doe";
    $age = 25;
    $city = "New York";
    
    echo "<h2>Personal Information</h2>";
    echo "Name: " . $first_name . " " . $last_name . "<br>";
    echo "Age: " . $age . "<br>";
    echo "City: " . $city . "<br>";
    
    // Calculate birth year
    $current_year = 2024;
    $birth_year = $current_year - $age;
    echo "Birth Year: " . $birth_year;
?>
```

### Example 2: Simple Calculator
```php
<?php
    $num1 = 15;
    $num2 = 7;
    
    echo "<h2>Calculator</h2>";
    echo "Number 1: " . $num1 . "<br>";
    echo "Number 2: " . $num2 . "<br><br>";
    
    echo "Addition: " . ($num1 + $num2) . "<br>";
    echo "Subtraction: " . ($num1 - $num2) . "<br>";
    echo "Multiplication: " . ($num1 * $num2) . "<br>";
    echo "Division: " . ($num1 / $num2) . "<br>";
    echo "Modulus: " . ($num1 % $num2) . "<br>";
?>
```

### Example 3: String Manipulation
```php
<?php
    $sentence = "PHP is a server-side scripting language";
    $word_count = str_word_count($sentence);
    $char_count = strlen($sentence);
    
    echo "Original sentence: " . $sentence . "<br>";
    echo "Word count: " . $word_count . "<br>";
    echo "Character count: " . $char_count . "<br>";
    
    // Convert to uppercase
    echo "Uppercase: " . strtoupper($sentence) . "<br>";
    
    // Replace words
    $new_sentence = str_replace("PHP", "JavaScript", $sentence);
    echo "Replaced: " . $new_sentence . "<br>";
?>
```

---

## Exercises

### Exercise 1: Variable Practice
Create a PHP file that:
1. Declares variables for your name, age, and favorite hobby
2. Outputs a sentence about yourself using these variables
3. Calculates and displays your age in months

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: Data Type Explorer
Create a PHP file that:
1. Creates variables of different data types
2. Uses `var_dump()` to display each variable and its type
3. Uses type checking functions to verify each type

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Temperature Converter
Create a PHP file that:
1. Declares a temperature in Celsius
2. Converts it to Fahrenheit (°F = °C × 9/5 + 32)
3. Displays both temperatures with appropriate labels

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand PHP syntax and tags
- [ ] I can declare and use variables
- [ ] I know the different PHP data types
- [ ] I can use various operators
- [ ] I can write effective comments
- [ ] I can output data using echo and print
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 2: Control Structures](../02-control-structures/README.md)
