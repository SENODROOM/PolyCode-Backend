<?php
    // Exercise 1: Array Manipulation
    
    echo "<h2>Array Manipulation Exercise</h2>";
    
    // 1. Create an array of numbers
    $numbers = [15, 3, 8, 12, 7, 25, 1, 18, 6, 22, 14, 9];
    
    echo "<h3>Original Array:</h3>";
    echo "Numbers: " . implode(", ", $numbers) . "<br>";
    echo "Count: " . count($numbers) . "<br><br>";
    
    // 2. Filter even numbers
    $evenNumbers = array_filter($numbers, function($num) {
        return $num % 2 === 0;
    });
    
    $oddNumbers = array_filter($numbers, function($num) {
        return $num % 2 !== 0;
    });
    
    echo "<h3>Filtered Arrays:</h3>";
    echo "Even numbers: " . implode(", ", $evenNumbers) . "<br>";
    echo "Odd numbers: " . implode(", ", $oddNumbers) . "<br><br>";
    
    // 3. Calculate statistics
    $stats = [
        'count' => count($numbers),
        'sum' => array_sum($numbers),
        'average' => array_sum($numbers) / count($numbers),
        'min' => min($numbers),
        'max' => max($numbers),
        'even_count' => count($evenNumbers),
        'odd_count' => count($oddNumbers)
    ];
    
    echo "<h3>Array Statistics:</h3>";
    echo "Total count: " . $stats['count'] . "<br>";
    echo "Sum: " . $stats['sum'] . "<br>";
    echo "Average: " . round($stats['average'], 2) . "<br>";
    echo "Minimum: " . $stats['min'] . "<br>";
    echo "Maximum: " . $stats['max'] . "<br>";
    echo "Even numbers count: " . $stats['even_count'] . "<br>";
    echo "Odd numbers count: " . $stats['odd_count'] . "<br><br>";
    
    // 4. Sort the array
    $ascending = $numbers;
    sort($ascending);
    
    $descending = $numbers;
    rsort($descending);
    
    echo "<h3>Sorted Arrays:</h3>";
    echo "Ascending: " . implode(", ", $ascending) . "<br>";
    echo "Descending: " . implode(", ", $descending) . "<br><br>";
    
    // Additional operations
    echo "<h3>Additional Operations:</h3>";
    
    // Find numbers greater than 10
    $greaterThan10 = array_filter($numbers, function($num) {
        return $num > 10;
    });
    echo "Numbers > 10: " . implode(", ", $greaterThan10) . "<br>";
    
    // Square each number
    $squared = array_map(function($num) {
        return $num * $num;
    }, $numbers);
    echo "Squared: " . implode(", ", $squared) . "<br>";
    
    // Get unique values (if there were duplicates)
    $withDuplicates = array_merge($numbers, [8, 15, 3]);
    $unique = array_unique($withDuplicates);
    echo "With duplicates: " . implode(", ", $withDuplicates) . "<br>";
    echo "Unique values: " . implode(", ", $unique) . "<br>";
    
    // Chunk into groups of 3
    $chunks = array_chunk($numbers, 3);
    echo "<br>Chunked into groups of 3:<br>";
    foreach ($chunks as $index => $chunk) {
        echo "Group " . ($index + 1) . ": " . implode(", ", $chunk) . "<br>";
    }
    
    // Find prime numbers
    function isPrime($num) {
        if ($num <= 1) return false;
        if ($num <= 3) return true;
        if ($num % 2 === 0 || $num % 3 === 0) return false;
        
        for ($i = 5; $i * $i <= $num; $i += 6) {
            if ($num % $i === 0 || $num % ($i + 2) === 0) {
                return false;
            }
        }
        return true;
    }
    
    $primes = array_filter($numbers, 'isPrime');
    echo "<br>Prime numbers: " . implode(", ", $primes) . "<br>";
    
    // Range analysis
    $range = $stats['max'] - $stats['min'];
    echo "Range (max - min): $range<br>";
    
    // Median calculation
    $sorted = $ascending;
    $count = count($sorted);
    $median = ($count % 2 === 0) 
        ? ($sorted[$count/2 - 1] + $sorted[$count/2]) / 2
        : $sorted[floor($count/2)];
    echo "Median: $median<br>";
    
    // Standard deviation
    $variance = array_sum(array_map(function($num) use ($stats) {
        return pow($num - $stats['average'], 2);
    }, $numbers)) / $stats['count'];
    $stdDev = sqrt($variance);
    echo "Standard deviation: " . round($stdDev, 2) . "<br>";
?>
