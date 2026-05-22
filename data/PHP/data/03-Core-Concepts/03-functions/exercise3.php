<?php
    // Exercise 3: Array Utility Functions
    
    echo "<h2>Array Utility Functions</h2>";
    
    /**
     * Sort array by key (ascending or descending)
     * @param array $array Array to sort
     * @param string $order 'asc' or 'desc'
     * @return array Sorted array
     */
    function sortByKey($array, $order = 'asc') {
        if ($order === 'desc') {
            krsort($array);
        } else {
            ksort($array);
        }
        return $array;
    }
    
    /**
     * Sort array by value (ascending or descending)
     * @param array $array Array to sort
     * @param string $order 'asc' or 'desc'
     * @return array Sorted array
     */
    function sortByValue($array, $order = 'asc') {
        if ($order === 'desc') {
            arsort($array);
        } else {
            asort($array);
        }
        return $array;
    }
    
    /**
     * Filter array by callback function
     * @param array $array Array to filter
     * @param callable $callback Filter function
     * @return array Filtered array
     */
    function filterArray($array, $callback) {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
    
    /**
     * Search for value in array (case-insensitive)
     * @param array $array Array to search
     * @param mixed $search Value to search for
     * @return bool True if found, false otherwise
     */
    function searchArray($array, $search) {
        foreach ($array as $value) {
            if (is_string($value) && is_string($search)) {
                if (strtolower($value) === strtolower($search)) {
                    return true;
                }
            } elseif ($value === $search) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get unique values from array (case-insensitive for strings)
     * @param array $array Array to process
     * @return array Array with unique values
     */
    function getUniqueValues($array) {
        $unique = [];
        $seen = [];
        
        foreach ($array as $value) {
            $key = is_string($value) ? strtolower($value) : $value;
            
            if (!isset($seen[$key])) {
                $unique[] = $value;
                $seen[$key] = true;
            }
        }
        
        return $unique;
    }
    
    /**
     * Flatten multidimensional array
     * @param array $array Multidimensional array
     * @return array Flattened array
     */
    function flattenArray($array) {
        $result = [];
        
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = array_merge($result, flattenArray($value));
            } else {
                $result[] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Get array statistics
     * @param array $array Numeric array
     * @return array Statistics (count, sum, avg, min, max)
     */
    function getArrayStats($array) {
        $numeric = array_filter($array, 'is_numeric');
        
        if (empty($numeric)) {
            return [
                'count' => count($array),
                'sum' => 0,
                'avg' => 0,
                'min' => null,
                'max' => null
            ];
        }
        
        return [
            'count' => count($numeric),
            'sum' => array_sum($numeric),
            'avg' => array_sum($numeric) / count($numeric),
            'min' => min($numeric),
            'max' => max($numeric)
        ];
    }
    
    /**
     * Group array by key value
     * @param array $array Array of associative arrays
     * @param string $key Key to group by
     * @return array Grouped array
     */
    function groupByKey($array, $key) {
        $grouped = [];
        
        foreach ($array as $item) {
            if (isset($item[$key])) {
                $groupKey = $item[$key];
                if (!isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [];
                }
                $grouped[$groupKey][] = $item;
            }
        }
        
        return $grouped;
    }
    
    // Test the functions
    echo "<h3>Sorting Functions:</h3>";
    
    $assocArray = [
        'c' => 'Cherry',
        'a' => 'Apple',
        'd' => 'Date',
        'b' => 'Banana'
    ];
    
    echo "Original: ";
    print_r($assocArray);
    
    echo "Sorted by key (ASC): ";
    print_r(sortByKey($assocArray, 'asc'));
    
    echo "Sorted by key (DESC): ";
    print_r(sortByKey($assocArray, 'desc'));
    
    echo "Sorted by value (ASC): ";
    print_r(sortByValue($assocArray, 'asc'));
    
    echo "<h3>Filtering Functions:</h3>";
    
    $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    $evenNumbers = filterArray($numbers, function($value) {
        return $value % 2 === 0;
    });
    
    echo "Original numbers: " . implode(', ', $numbers) . "<br>";
    echo "Even numbers: " . implode(', ', $evenNumbers) . "<br>";
    
    $words = ['apple', 'banana', 'cherry', 'date', 'elderberry'];
    $longWords = filterArray($words, function($value) {
        return strlen($value) > 5;
    });
    
    echo "Original words: " . implode(', ', $words) . "<br>";
    echo "Long words (>5 chars): " . implode(', ', $longWords) . "<br>";
    
    echo "<h3>Search Functions:</h3>";
    
    $mixedArray = ['Apple', 'Banana', 'Cherry', 123, 'apple'];
    echo "Searching for 'apple' (case-insensitive): " . (searchArray($mixedArray, 'apple') ? 'Found' : 'Not found') . "<br>";
    echo "Searching for 'APPLE' (case-insensitive): " . (searchArray($mixedArray, 'APPLE') ? 'Found' : 'Not found') . "<br>";
    echo "Searching for 123: " . (searchArray($mixedArray, 123) ? 'Found' : 'Not found') . "<br>";
    echo "Searching for 'grape': " . (searchArray($mixedArray, 'grape') ? 'Found' : 'Not found') . "<br>";
    
    echo "<h3>Unique Values:</h3>";
    
    $duplicates = ['Apple', 'Banana', 'apple', 'Cherry', 'BANANA', 'apple', 'Date'];
    echo "Original: " . implode(', ', $duplicates) . "<br>";
    echo "Unique (case-insensitive): " . implode(', ', getUniqueValues($duplicates)) . "<br>";
    
    echo "<h3>Flatten Array:</h3>";
    
    $nestedArray = [1, [2, 3], [4, [5, 6]], 7, [8, [9, [10]]]];
    echo "Nested: ";
    print_r($nestedArray);
    echo "Flattened: " . implode(', ', flattenArray($nestedArray)) . "<br>";
    
    echo "<h3>Array Statistics:</h3>";
    
    $numericArray = [10, 20, 30, 40, 50];
    $stats = getArrayStats($numericArray);
    
    echo "Numbers: " . implode(', ', $numericArray) . "<br>";
    echo "Count: " . $stats['count'] . "<br>";
    echo "Sum: " . $stats['sum'] . "<br>";
    echo "Average: " . $stats['avg'] . "<br>";
    echo "Min: " . $stats['min'] . "<br>";
    echo "Max: " . $stats['max'] . "<br>";
    
    echo "<h3>Group By Key:</h3>";
    
    $people = [
        ['name' => 'John', 'city' => 'New York', 'age' => 25],
        ['name' => 'Jane', 'city' => 'Los Angeles', 'age' => 30],
        ['name' => 'Bob', 'city' => 'New York', 'age' => 35],
        ['name' => 'Alice', 'city' => 'Chicago', 'age' => 28],
        ['name' => 'Charlie', 'city' => 'Los Angeles', 'age' => 32]
    ];
    
    $groupedByCity = groupByKey($people, 'city');
    
    echo "People grouped by city:<br>";
    foreach ($groupedByCity as $city => $cityPeople) {
        echo "<strong>$city:</strong><br>";
        foreach ($cityPeople as $person) {
            echo "  - {$person['name']} ({$person['age']})<br>";
        }
        echo "<br>";
    }
?>
