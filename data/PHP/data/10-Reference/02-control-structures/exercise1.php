<?php
    // Exercise 1: Even/Odd Numbers
    
    echo "<h2>Even/Odd Numbers (1-20)</h2>";
    echo "Skipping multiples of 3...<br><br>";
    
    for ($i = 1; $i <= 20; $i++) {
        // Skip multiples of 3
        if ($i % 3 == 0) {
            continue;
        }
        
        echo "Number: $i - ";
        
        // Check if even or odd
        if ($i % 2 == 0) {
            echo "Even<br>";
        } else {
            echo "Odd<br>";
        }
    }
    
    echo "<br><h3>Summary:</h3>";
    echo "Numbers displayed: 1, 2, 4, 5, 7, 8, 10, 11, 13, 14, 16, 17, 19, 20<br>";
    echo "Skipped (multiples of 3): 3, 6, 9, 12, 15, 18";
?>
