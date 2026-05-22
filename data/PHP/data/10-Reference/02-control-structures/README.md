# Module 2: Control Structures 🔄

Control structures allow you to control the flow of your program based on conditions and loops. This module covers how to make decisions and repeat actions in PHP.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand conditional statements (if/else, switch)
- Use comparison and logical operators effectively
- Implement loops (for, while, do-while, foreach)
- Control loop execution (break, continue)
- Write clean and efficient conditional logic

## 📝 Topics Covered

1. [Conditional Statements](#conditional-statements)
2. [Switch Statement](#switch-statement)
3. [Loops](#loops)
4. [Loop Control](#loop-control)
5. [Nested Control Structures](#nested-control-structures)
6. [Practical Examples](#practical-examples)
7. [Exercises](#exercises)

---

## Conditional Statements

### if Statement
Executes code if a condition is true.

```php
<?php
    $age = 18;
    
    if ($age >= 18) {
        echo "You are eligible to vote!";
    }
?>
```

### if-else Statement
Executes one block if condition is true, another if false.

```php
<?php
    $temperature = 25;
    
    if ($temperature > 30) {
        echo "It's hot outside!";
    } else {
        echo "It's pleasant outside!";
    }
?>
```

### if-elseif-else Statement
Multiple conditions in sequence.

```php
<?php
    $grade = 85;
    
    if ($grade >= 90) {
        echo "A";
    } elseif ($grade >= 80) {
        echo "B";
    } elseif ($grade >= 70) {
        echo "C";
    } elseif ($grade >= 60) {
        echo "D";
    } else {
        echo "F";
    }
?>
```

### Alternative Syntax
PHP provides alternative syntax for templates.

```php
<?php if ($age >= 18): ?>
    <h3>Welcome, adult user!</h3>
<?php else: ?>
    <h3>Welcome, young user!</h3>
<?php endif; ?>
```

---

## Switch Statement

Useful when comparing a variable against multiple values.

### Basic Switch
```php
<?php
    $day = "Monday";
    
    switch ($day) {
        case "Monday":
            echo "Start of the work week";
            break;
        case "Friday":
            echo "Almost weekend!";
            break;
        case "Saturday":
        case "Sunday":
            echo "Weekend!";
            break;
        default:
            echo "Midweek day";
    }
?>
```

### Switch with Expression (PHP 8+)
```php
<?php
    $status = "active";
    
    $message = match ($status) {
        'active' => 'Account is active',
        'inactive' => 'Account is inactive',
        'suspended' => 'Account is suspended',
        default => 'Unknown status'
    };
    
    echo $message;
?>
```

---

## Loops

### for Loop
Execute code a specific number of times.

```php
<?php
    for ($i = 1; $i <= 10; $i++) {
        echo "Number: $i<br>";
    }
    
    // Countdown
    for ($i = 10; $i >= 1; $i--) {
        echo $i . "... ";
    }
    echo "Lift off!";
?>
```

### while Loop
Execute code while condition is true.

```php
<?php
    $count = 1;
    
    while ($count <= 5) {
        echo "Count: $count<br>";
        $count++;
    }
    
    // Reading lines until empty
    $line = "not empty";
    while ($line != "") {
        // Process line
        $line = ""; // Would be actual input
    }
?>
```

### do-while Loop
Always executes at least once.

```php
<?php
    $count = 6;
    
    do {
        echo "Count: $count<br>";
        $count++;
    } while ($count <= 5); // This condition is false, but loop runs once
?>
```

### foreach Loop
Iterate over arrays.

```php
<?php
    $fruits = ["Apple", "Banana", "Orange"];
    
    foreach ($fruits as $fruit) {
        echo $fruit . "<br>";
    }
    
    // With key and value
    $person = [
        "name" => "John",
        "age" => 25,
        "city" => "New York"
    ];
    
    foreach ($person as $key => $value) {
        echo "$key: $value<br>";
    }
?>
```

---

## Loop Control

### break Statement
Exit the loop immediately.

```php
<?php
    for ($i = 1; $i <= 10; $i++) {
        if ($i == 5) {
            break; // Exit loop when i equals 5
        }
        echo $i . " ";
    }
    // Output: 1 2 3 4
?>
```

### continue Statement
Skip the rest of the current iteration.

```php
<?php
    for ($i = 1; $i <= 10; $i++) {
        if ($i % 2 == 0) {
            continue; // Skip even numbers
        }
        echo $i . " ";
    }
    // Output: 1 3 5 7 9
?>
```

### break with Levels
Break out of nested loops.

```php
<?php
    for ($i = 1; $i <= 3; $i++) {
        for ($j = 1; $j <= 3; $j++) {
            if ($i == 2 && $j == 2) {
                break 2; // Break out of both loops
            }
            echo "$i,$j ";
        }
    }
    // Output: 1,1 1,2 1,3 2,1
?>
```

---

## Nested Control Structures

### Nested if Statements
```php
<?php
    $age = 25;
    $hasLicense = true;
    
    if ($age >= 18) {
        if ($hasLicense) {
            echo "You can drive!";
        } else {
            echo "You need a license to drive.";
        }
    } else {
        echo "You're too young to drive.";
    }
?>
```

### Nested Loops
```php
<?php
    // Multiplication table
    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 5; $j++) {
            echo $i * $j . "\t";
        }
        echo "<br>";
    }
?>
```

### Loops with Conditionals
```php
<?php
    $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    foreach ($numbers as $number) {
        if ($number % 2 == 0) {
            echo "$number is even<br>";
        } else {
            echo "$number is odd<br>";
        }
    }
?>
```

---

## Practical Examples

### Example 1: Grade Calculator
```php
<?php
    $scores = [85, 92, 78, 95, 88];
    
    echo "<h2>Grade Calculator</h2>";
    
    foreach ($scores as $score) {
        echo "Score: $score - ";
        
        if ($score >= 90) {
            echo "Grade: A (Excellent!)<br>";
        } elseif ($score >= 80) {
            echo "Grade: B (Good)<br>";
        } elseif ($score >= 70) {
            echo "Grade: C (Average)<br>";
        } elseif ($score >= 60) {
            echo "Grade: D (Below Average)<br>";
        } else {
            echo "Grade: F (Fail)<br>";
        }
    }
?>
```

### Example 2: Number Guessing Game Logic
```php
<?php
    $secretNumber = 42;
    $guesses = [10, 25, 50, 42, 30];
    
    echo "<h2>Number Guessing Game</h2>";
    echo "Secret number: $secretNumber<br><br>";
    
    foreach ($guesses as $index => $guess) {
        echo "Guess #" . ($index + 1) . ": $guess - ";
        
        if ($guess == $secretNumber) {
            echo "Correct! You found it!<br>";
            break;
        } elseif ($guess < $secretNumber) {
            echo "Too low!<br>";
        } else {
            echo "Too high!<br>";
        }
    }
?>
```

### Example 3: ATM Menu
```php
<?php
    $balance = 1000;
    $choice = "withdraw";
    $amount = 200;
    
    echo "<h2>ATM Menu</h2>";
    echo "Current balance: $" . number_format($balance, 2) . "<br><br>";
    
    switch ($choice) {
        case "balance":
            echo "Your balance is: $" . number_format($balance, 2);
            break;
            
        case "deposit":
            echo "Deposited: $" . number_format($amount, 2) . "<br>";
            $balance += $amount;
            echo "New balance: $" . number_format($balance, 2);
            break;
            
        case "withdraw":
            if ($amount > $balance) {
                echo "Insufficient funds!";
            } else {
                echo "Withdrew: $" . number_format($amount, 2) . "<br>";
                $balance -= $amount;
                echo "New balance: $" . number_format($balance, 2);
            }
            break;
            
        default:
            echo "Invalid choice!";
    }
?>
```

### Example 4: Prime Number Checker
```php
<?php
    $number = 17;
    $isPrime = true;
    
    echo "<h2>Prime Number Checker</h2>";
    echo "Checking if $number is prime...<br><br>";
    
    if ($number <= 1) {
        $isPrime = false;
    } else {
        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i == 0) {
                $isPrime = false;
                break;
            }
        }
    }
    
    if ($isPrime) {
        echo "$number is a prime number!";
    } else {
        echo "$number is not a prime number.";
    }
?>
```

---

## Exercises

### Exercise 1: Even/Odd Numbers
Create a PHP file that:
1. Uses a loop to display numbers 1-20
2. For each number, display whether it's even or odd
3. Skip multiples of 3 using continue

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: Login Simulator
Create a PHP file that:
1. Simulates a login with username and password
2. Uses if-elseif-else to check credentials
3. Provides appropriate messages for different scenarios

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Fibonacci Sequence
Create a PHP file that:
1. Generates the first 10 Fibonacci numbers
2. Uses a loop to calculate and display them
3. Format the output nicely

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand if/else/elseif statements
- [ ] I can use switch statements effectively
- [ ] I can implement different types of loops
- [ ] I can control loop execution with break and continue
- [ ] I can nest control structures
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 3: Functions](../03-functions/README.md)
