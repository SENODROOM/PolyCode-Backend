<?php
    // Exercise 2: Data Type Explorer
    
    echo "<h2>Data Type Explorer</h2>";
    
    // Create variables of different data types
    $string_var = "Hello PHP";
    $int_var = 42;
    $float_var = 3.14159;
    $bool_var = true;
    $null_var = null;
    $array_var = ["apple", "banana", "orange"];
    
    echo "<h3>Using var_dump():</h3>";
    echo "String: ";
    var_dump($string_var);
    echo "<br>";
    
    echo "Integer: ";
    var_dump($int_var);
    echo "<br>";
    
    echo "Float: ";
    var_dump($float_var);
    echo "<br>";
    
    echo "Boolean: ";
    var_dump($bool_var);
    echo "<br>";
    
    echo "Null: ";
    var_dump($null_var);
    echo "<br>";
    
    echo "Array: ";
    var_dump($array_var);
    echo "<br><br>";
    
    echo "<h3>Using type checking functions:</h3>";
    echo "is_string(\$string_var): " . (is_string($string_var) ? 'true' : 'false') . "<br>";
    echo "is_int(\$int_var): " . (is_int($int_var) ? 'true' : 'false') . "<br>";
    echo "is_float(\$float_var): " . (is_float($float_var) ? 'true' : 'false') . "<br>";
    echo "is_bool(\$bool_var): " . (is_bool($bool_var) ? 'true' : 'false') . "<br>";
    echo "is_null(\$null_var): " . (is_null($null_var) ? 'true' : 'false') . "<br>";
    echo "is_array(\$array_var): " . (is_array($array_var) ? 'true' : 'false') . "<br>";
?>
