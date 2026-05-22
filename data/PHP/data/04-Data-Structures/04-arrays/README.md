# Module 4: Arrays 📊

Arrays are fundamental data structures in PHP that allow you to store multiple values in a single variable. They are incredibly versatile and essential for most PHP applications.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand different types of arrays
- Create and manipulate arrays
- Use built-in array functions effectively
- Work with multidimensional arrays
- Sort, search, and filter arrays
- Apply array operations in practical scenarios

## 📝 Topics Covered

1. [Array Types](#array-types)
2. [Creating Arrays](#creating-arrays)
3. [Accessing Array Elements](#accessing-array-elements)
4. [Modifying Arrays](#modifying-arrays)
5. [Array Functions](#array-functions)
6. [Multidimensional Arrays](#multidimensional-arrays)
7. [Array Operations](#array-operations)
8. [Practical Examples](#practical-examples)
9. [Exercises](#exercises)

---

## Array Types

### Indexed Arrays
Arrays with numeric indices starting from 0.

```php
<?php
    $fruits = ["Apple", "Banana", "Orange"];
    echo $fruits[0];  // Outputs: Apple
    echo $fruits[1];  // Outputs: Banana
    echo $fruits[2];  // Outputs: Orange
?>
```

### Associative Arrays
Arrays with named keys.

```php
<?php
    $person = [
        "name" => "John Doe",
        "age" => 25,
        "city" => "New York"
    ];
    
    echo $person["name"];  // Outputs: John Doe
    echo $person["age"];   // Outputs: 25
    echo $person["city"];  // Outputs: New York
?>
```

### Multidimensional Arrays
Arrays containing other arrays.

```php
<?php
    $matrix = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
    ];
    
    echo $matrix[0][1];  // Outputs: 2
    echo $matrix[2][2];  // Outputs: 9
?>
```

---

## Creating Arrays

### Using array() Constructor
```php
<?php
    // Indexed array
    $numbers = array(1, 2, 3, 4, 5);
    
    // Associative array
    $user = array(
        "name" => "John",
        "email" => "john@example.com",
        "age" => 25
    );
    
    // Mixed array
    $mixed = array("Apple", 42, true, null);
?>
```

### Using Square Brackets (PHP 5.4+)
```php
<?php
    // Indexed array
    $fruits = ["Apple", "Banana", "Orange"];
    
    // Associative array
    $config = [
        "host" => "localhost",
        "username" => "admin",
        "password" => "secret"
    ];
?>
```

### Creating Empty Arrays
```php
<?php
    $emptyArray = [];
    $anotherEmpty = array();
    
    // Add elements later
    $emptyArray[] = "First element";
    $emptyArray[] = "Second element";
?>
```

### Dynamic Array Creation
```php
<?php
    // Create array from string
    $sentence = "Hello World PHP";
    $words = explode(" ", $sentence);
    // Result: ["Hello", "World", "PHP"]
    
    // Create array from range
    $numbers = range(1, 10);
    // Result: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    
    $letters = range('a', 'e');
    // Result: ['a', 'b', 'c', 'd', 'e']
?>
```

---

## Accessing Array Elements

### Access by Index
```php
<?php
    $colors = ["Red", "Green", "Blue"];
    echo $colors[0];  // Red
    echo $colors[1];  // Green
    echo $colors[2];  // Blue
?>
```

### Access by Key
```php
<?php
    $student = [
        "name" => "Alice",
        "grade" => "A",
        "score" => 95
    ];
    
    echo $student["name"];   // Alice
    echo $student["grade"];  // A
    echo $student["score"];  // 95
?>
```

### Check if Element Exists
```php
<?php
    $arr = ["a" => "Apple", "b" => "Banana"];
    
    // Check if key exists
    if (array_key_exists("a", $arr)) {
        echo "Key 'a' exists";
    }
    
    // Alternative method
    if (isset($arr["b"])) {
        echo "Key 'b' exists";
    }
    
    // Check if value exists
    if (in_array("Apple", $arr)) {
        echo "Value 'Apple' exists";
    }
?>
```

### Safe Access with Null Coalescing (PHP 7+)
```php
<?php
    $config = ["host" => "localhost"];
    
    // Safe access with default value
    $host = $config["host"] ?? "default_host";
    $port = $config["port"] ?? 3306;
    
    echo $host;  // localhost
    echo $port;  // 3306 (default)
?>
```

---

## Modifying Arrays

### Adding Elements
```php
<?php
    $fruits = ["Apple", "Banana"];
    
    // Add to end
    $fruits[] = "Orange";
    array_push($fruits, "Grape");
    
    // Add to beginning
    array_unshift($fruits, "Mango");
    
    // Add at specific position
    $fruits[3] = "Kiwi";
?>
```

### Removing Elements
```php
<?php
    $numbers = [1, 2, 3, 4, 5];
    
    // Remove from end
    $last = array_pop($numbers);  // Removes 5
    
    // Remove from beginning
    $first = array_shift($numbers);  // Removes 1
    
    // Remove specific element
    unset($numbers[2]);  // Removes element at index 2
    
    // Remove by value
    $filtered = array_diff($numbers, [3]);
?>
```

### Modifying Elements
```php
<?php
    $arr = ["a", "b", "c"];
    
    // Change by index
    $arr[0] = "Apple";
    
    // Change by key
    $assoc = ["name" => "John"];
    $assoc["name"] = "Jane";
    
    // Modify multiple elements
    $arr = array_map(function($item) {
        return strtoupper($item);
    }, $arr);
?>
```

---

## Array Functions

### Counting Elements
```php
<?php
    $arr = [1, 2, 3, 4, 5];
    
    echo count($arr);           // 5
    echo sizeof($arr);          // 5 (alias of count)
    
    // Count multidimensional
    $multi = [[1, 2], [3, 4], [5, 6]];
    echo count($multi, COUNT_RECURSIVE);  // 6
?>
```

### Searching Arrays
```php
<?php
    $fruits = ["Apple", "Banana", "Orange", "Apple"];
    
    // Find value
    $index = array_search("Orange", $fruits);  // 2
    $index = array_search("Apple", $fruits);   // 0 (first occurrence)
    
    // Check if value exists
    $exists = in_array("Banana", $fruits);  // true
    
    // Get all keys for a value
    $keys = array_keys($fruits, "Apple");  // [0, 3]
?>
```

### Sorting Arrays
```php
<?php
    $numbers = [3, 1, 4, 1, 5, 9];
    $fruits = ["Orange", "Apple", "Banana"];
    
    // Sort by value
    sort($numbers);        // [1, 1, 3, 4, 5, 9]
    rsort($numbers);       // [9, 5, 4, 3, 1, 1]
    
    // Sort by value (maintain key association)
    asort($fruits);        // ["Apple", "Banana", "Orange"]
    arsort($fruits);       // ["Orange", "Banana", "Apple"]
    
    // Sort by key
    ksort($fruits);        // Sort by keys
    krsort($fruits);       // Reverse sort by keys
    
    // Custom sort
    usort($numbers, function($a, $b) {
        return $a <=> $b;  // Spaceship operator
    });
?>
```

### Filtering Arrays
```php
<?php
    $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    // Filter even numbers
    $even = array_filter($numbers, function($n) {
        return $n % 2 === 0;
    });
    // Result: [2, 4, 6, 8, 10]
    
    // Filter with keys
    $assoc = ["a" => 1, "b" => 2, "c" => 3];
    $filtered = array_filter($assoc, function($value, $key) {
        return $key === "b";
    }, ARRAY_FILTER_USE_BOTH);
?>
```

### Mapping Arrays
```php
<?php
    $numbers = [1, 2, 3, 4, 5];
    
    // Square each number
    $squared = array_map(function($n) {
        return $n * $n;
    }, $numbers);
    // Result: [1, 4, 9, 16, 25]
    
    // Map with keys
    $assoc = ["a" => 1, "b" => 2];
    $mapped = array_map(function($value) {
        return $value * 10;
    }, $assoc);
?>
```

### Reducing Arrays
```php
<?php
    $numbers = [1, 2, 3, 4, 5];
    
    // Sum all numbers
    $sum = array_reduce($numbers, function($carry, $item) {
        return $carry + $item;
    }, 0);
    // Result: 15
    
    // Product of all numbers
    $product = array_reduce($numbers, function($carry, $item) {
        return $carry * $item;
    }, 1);
    // Result: 120
?>
```

---

## Multidimensional Arrays

### Creating Multidimensional Arrays
```php
<?php
    // 2D array (matrix)
    $matrix = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
    ];
    
    // 3D array
    $cube = [
        [
            [1, 2],
            [3, 4]
        ],
        [
            [5, 6],
            [7, 8]
        ]
    ];
    
    // Associative multidimensional
    $students = [
        "student1" => [
            "name" => "John",
            "grades" => [90, 85, 95]
        ],
        "student2" => [
            "name" => "Jane",
            "grades" => [88, 92, 87]
        ]
    ];
?>
```

### Accessing Multidimensional Elements
```php
<?php
    $matrix = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
    ];
    
    echo $matrix[0][1];  // 2
    echo $matrix[2][2];  // 9
    
    $students = [
        "John" => ["math" => 90, "english" => 85],
        "Jane" => ["math" => 88, "english" => 92]
    ];
    
    echo $students["John"]["math"];     // 90
    echo $students["Jane"]["english"];  // 92
?>
```

### Iterating Multidimensional Arrays
```php
<?php
    $matrix = [
        [1, 2, 3],
        [4, 5, 6]
    ];
    
    // Nested foreach
    foreach ($matrix as $row) {
        foreach ($row as $value) {
            echo $value . " ";
        }
        echo "<br>";
    }
    
    // With keys
    $students = [
        "John" => ["math" => 90, "english" => 85],
        "Jane" => ["math" => 88, "english" => 92]
    ];
    
    foreach ($students as $name => $grades) {
        echo "$name: ";
        foreach ($grades as $subject => $grade) {
            echo "$subject=$grade ";
        }
        echo "<br>";
    }
?>
```

---

## Array Operations

### Merging Arrays
```php
<?php
    $arr1 = [1, 2, 3];
    $arr2 = [4, 5, 6];
    
    // Merge arrays
    $merged = array_merge($arr1, $arr2);
    // Result: [1, 2, 3, 4, 5, 6]
    
    // Merge with renumbering
    $merged = $arr1 + $arr2;
    // Result: [1, 2, 3, 4, 5, 6] (if keys don't conflict)
    
    // Merge associative arrays
    $assoc1 = ["a" => "Apple", "b" => "Banana"];
    $assoc2 = ["c" => "Cherry", "d" => "Date"];
    $merged = array_merge($assoc1, $assoc2);
?>
```

### Slicing Arrays
```php
<?php
    $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    // Get slice
    $slice = array_slice($numbers, 2, 4);
    // Result: [3, 4, 5, 6]
    
    // Get from position to end
    $slice = array_slice($numbers, 5);
    // Result: [6, 7, 8, 9, 10]
    
    // Get last 3 elements
    $slice = array_slice($numbers, -3);
    // Result: [8, 9, 10]
?>
```

### Chunking Arrays
```php
<?php
    $numbers = range(1, 10);
    
    // Split into chunks of 3
    $chunks = array_chunk($numbers, 3);
    // Result: [[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]]
    
    // Preserve keys
    $assoc = ["a" => 1, "b" => 2, "c" => 3, "d" => 4];
    $chunks = array_chunk($assoc, 2, true);
?>
```

### Unique Values
```php
<?php
    $duplicates = [1, 2, 2, 3, 3, 3, 4, 5];
    
    // Get unique values
    $unique = array_unique($duplicates);
    // Result: [1, 2, 3, 4, 5]
    
    // Count unique values
    $count = array_count_values($duplicates);
    // Result: [1 => 1, 2 => 2, 3 => 3, 4 => 1, 5 => 1]
?>
```

---

## Practical Examples

### Example 1: Shopping Cart
```php
<?php
    $cart = [
        ["name" => "Apple", "price" => 1.50, "quantity" => 3],
        ["name" => "Banana", "price" => 0.80, "quantity" => 5],
        ["name" => "Orange", "price" => 2.00, "quantity" => 2]
    ];
    
    // Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item["price"] * $item["quantity"];
    }
    
    echo "Shopping Cart:<br>";
    foreach ($cart as $item) {
        $subtotal = $item["price"] * $item["quantity"];
        echo "{$item['name']} x {$item['quantity']} = $" . number_format($subtotal, 2) . "<br>";
    }
    echo "Total: $" . number_format($total, 2);
?>
```

### Example 2: Student Grades
```php
<?php
    $students = [
        "Alice" => ["math" => 92, "english" => 88, "science" => 95],
        "Bob" => ["math" => 78, "english" => 85, "science" => 82],
        "Charlie" => ["math" => 95, "english" => 92, "science" => 98]
    ];
    
    foreach ($students as $name => $grades) {
        $average = array_sum($grades) / count($grades);
        $letter = $average >= 90 ? "A" : ($average >= 80 ? "B" : "C");
        
        echo "$name: Average = " . round($average, 1) . " (Grade $letter)<br>";
    }
?>
```

### Example 3: Data Processing
```php
<?php
    $data = [
        ["id" => 1, "category" => "A", "value" => 100],
        ["id" => 2, "category" => "B", "value" => 150],
        ["id" => 3, "category" => "A", "value" => 120],
        ["id" => 4, "category" => "C", "value" => 80],
        ["id" => 5, "category" => "B", "value" => 200]
    ];
    
    // Group by category
    $grouped = [];
    foreach ($data as $item) {
        $grouped[$item["category"]][] = $item["value"];
    }
    
    // Calculate sums by category
    foreach ($grouped as $category => $values) {
        $sum = array_sum($values);
        $count = count($values);
        echo "Category $category: $count items, Total = $sum, Average = " . round($sum/$count, 1) . "<br>";
    }
?>
```

---

## Exercises

### Exercise 1: Array Manipulation
Create a PHP file that:
1. Creates an array of numbers
2. Filters even numbers
3. Calculates statistics (sum, average, min, max)
4. Sorts the array

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: Contact List
Create a PHP file that:
1. Creates an array of contacts
2. Allows adding, removing, and searching contacts
3. Displays contacts in different formats

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Data Analysis
Create a PHP file that:
1. Processes sales data from an array
2. Groups data by categories
3. Calculates totals and averages

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand different array types
- [ ] I can create and modify arrays
- [ ] I can use array functions effectively
- [ ] I can work with multidimensional arrays
- [ ] I can perform array operations
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 5: Object-Oriented Programming](../05-oop/README.md)
