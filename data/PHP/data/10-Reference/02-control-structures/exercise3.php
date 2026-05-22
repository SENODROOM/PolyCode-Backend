<?php
    // Exercise 3: Fibonacci Sequence
    
    echo "<h2>Fibonacci Sequence</h2>";
    echo "First 10 Fibonacci numbers:<br><br>";
    
    // Initialize first two numbers
    $a = 0;
    $b = 1;
    
    echo "<div style='font-family: monospace; font-size: 18px;'>";
    
    // Generate and display first 10 Fibonacci numbers
    for ($i = 1; $i <= 10; $i++) {
        echo "$a";
        
        // Add comma except for last number
        if ($i < 10) {
            echo ", ";
        }
        
        // Add line break every 5 numbers for better formatting
        if ($i % 5 == 0) {
            echo "<br>";
        }
        
        // Calculate next number
        $next = $a + $b;
        $a = $b;
        $b = $next;
    }
    
    echo "</div>";
    
    echo "<br><br><h3>How it works:</h3>";
    echo "Starting with 0 and 1, each subsequent number is the sum of the two preceding ones.<br>";
    echo "Formula: F(n) = F(n-1) + F(n-2)<br><br>";
    
    echo "<h3>Step-by-step calculation:</h3>";
    echo "1. Start with 0, 1<br>";
    echo "2. 0 + 1 = 1<br>";
    echo "3. 1 + 1 = 2<br>";
    echo "4. 1 + 2 = 3<br>";
    echo "5. 2 + 3 = 5<br>";
    echo "6. 3 + 5 = 8<br>";
    echo "7. 5 + 8 = 13<br>";
    echo "8. 8 + 13 = 21<br>";
    echo "9. 13 + 21 = 34<br>";
    echo "10. 21 + 34 = 55<br>";
?>
