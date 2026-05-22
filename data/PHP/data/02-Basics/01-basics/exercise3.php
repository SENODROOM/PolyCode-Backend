<?php
    // Exercise 3: Temperature Converter
    
    echo "<h2>Temperature Converter</h2>";
    
    // Declare temperature in Celsius
    $celsius = 25;
    
    // Convert to Fahrenheit
    $fahrenheit = ($celsius * 9/5) + 32;
    
    // Display both temperatures
    echo "Temperature in Celsius: " . $celsius . "°C<br>";
    echo "Temperature in Fahrenheit: " . round($fahrenheit, 2) . "°F<br><br>";
    
    // Additional conversions
    echo "<h3>Additional Temperatures:</h3>";
    
    // Freezing point
    $freezing_c = 0;
    $freezing_f = ($freezing_c * 9/5) + 32;
    echo "Freezing point: " . $freezing_c . "°C = " . $freezing_f . "°F<br>";
    
    // Boiling point
    $boiling_c = 100;
    $boiling_f = ($boiling_c * 9/5) + 32;
    echo "Boiling point: " . $boiling_c . "°C = " . $boiling_f . "°F<br>";
    
    // Body temperature
    $body_c = 37;
    $body_f = ($body_c * 9/5) + 32;
    echo "Body temperature: " . $body_c . "°C = " . round($body_f, 1) . "°F<br>";
?>
