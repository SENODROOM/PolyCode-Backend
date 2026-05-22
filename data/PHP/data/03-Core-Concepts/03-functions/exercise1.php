<?php
    // Exercise 1: Temperature Converter Function
    
    echo "<h2>Temperature Converter Functions</h2>";
    
    /**
     * Convert Celsius to Fahrenheit
     * @param float $celsius Temperature in Celsius
     * @param int $decimals Number of decimal places to round to
     * @return float Temperature in Fahrenheit
     */
    function celsiusToFahrenheit($celsius, $decimals = 2) {
        $fahrenheit = ($celsius * 9/5) + 32;
        return round($fahrenheit, $decimals);
    }
    
    /**
     * Convert Fahrenheit to Celsius
     * @param float $fahrenheit Temperature in Fahrenheit
     * @param int $decimals Number of decimal places to round to
     * @return float Temperature in Celsius
     */
    function fahrenheitToCelsius($fahrenheit, $decimals = 2) {
        $celsius = ($fahrenheit - 32) * 5/9;
        return round($celsius, $decimals);
    }
    
    /**
     * Convert temperature with automatic unit detection
     * @param float $temperature Temperature value
     * @param string $fromUnit Unit to convert from ('C' or 'F')
     * @param string $toUnit Unit to convert to ('C' or 'F')
     * @param int $decimals Number of decimal places
     * @return float|string Converted temperature or error message
     */
    function convertTemperature($temperature, $fromUnit, $toUnit, $decimals = 2) {
        $fromUnit = strtoupper($fromUnit);
        $toUnit = strtoupper($toUnit);
        
        // Handle edge cases
        if (!is_numeric($temperature)) {
            return "Invalid temperature value";
        }
        
        if ($fromUnit === $toUnit) {
            return round($temperature, $decimals);
        }
        
        if ($fromUnit === 'C' && $toUnit === 'F') {
            return celsiusToFahrenheit($temperature, $decimals);
        } elseif ($fromUnit === 'F' && $toUnit === 'C') {
            return fahrenheitToCelsius($temperature, $decimals);
        } else {
            return "Invalid units. Use 'C' or 'F'";
        }
    }
    
    // Test the functions
    echo "<h3>Basic Conversions:</h3>";
    echo "0°C = " . celsiusToFahrenheit(0) . "°F<br>";
    echo "100°C = " . celsiusToFahrenheit(100) . "°F<br>";
    echo "32°F = " . fahrenheitToCelsius(32) . "°C<br>";
    echo "212°F = " . fahrenheitToCelsius(212) . "°C<br>";
    
    echo "<h3>With Different Decimal Places:</h3>";
    echo "25°C = " . celsiusToFahrenheit(25, 0) . "°F (0 decimals)<br>";
    echo "25°C = " . celsiusToFahrenheit(25, 1) . "°F (1 decimal)<br>";
    echo "25°C = " . celsiusToFahrenheit(25, 2) . "°F (2 decimals)<br>";
    echo "25°C = " . celsiusToFahrenheit(25, 4) . "°F (4 decimals)<br>";
    
    echo "<h3>Universal Converter:</h3>";
    $conversions = [
        [25, 'C', 'F'],
        [77, 'F', 'C'],
        [0, 'C', 'F'],
        [32, 'F', 'C'],
        [100, 'C', 'F'],
        [212, 'F', 'C']
    ];
    
    foreach ($conversions as $conv) {
        $result = convertTemperature($conv[0], $conv[1], $conv[2]);
        echo "{$conv[0]}°{$conv[1]} = {$result}°{$conv[2]}<br>";
    }
    
    echo "<h3>Edge Cases:</h3>";
    echo "Invalid temperature: " . convertTemperature("abc", 'C', 'F') . "<br>";
    echo "Invalid units: " . convertTemperature(25, 'X', 'Y') . "<br>";
    echo "Same units: " . convertTemperature(25, 'C', 'C') . "<br>";
?>
