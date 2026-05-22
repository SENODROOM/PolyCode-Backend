<?php
    // Exercise 2: String Manipulation Functions
    
    echo "<h2>String Manipulation Functions</h2>";
    
    /**
     * Validate if a string is a valid email
     * @param string $email Email to validate
     * @return bool True if valid, false otherwise
     */
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate if a string contains only letters
     * @param string $string String to validate
     * @return bool True if only letters, false otherwise
     */
    function isAlpha($string) {
        return preg_match('/^[a-zA-Z]+$/', $string) === 1;
    }
    
    /**
     * Validate if a string contains only alphanumeric characters
     * @param string $string String to validate
     * @return bool True if alphanumeric, false otherwise
     */
    function isAlphanumeric($string) {
        return preg_match('/^[a-zA-Z0-9]+$/', $string) === 1;
    }
    
    /**
     * Format a phone number (US format)
     * @param string $phone Phone number string
     * @return string Formatted phone number or original if invalid
     */
    function formatPhoneNumber($phone) {
        // Remove all non-digit characters
        $digits = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if we have exactly 10 digits
        if (strlen($digits) === 10) {
            return '(' . substr($digits, 0, 3) . ') ' . 
                   substr($digits, 3, 3) . '-' . 
                   substr($digits, 6);
        }
        
        return $phone; // Return original if not 10 digits
    }
    
    /**
     * Create a URL-friendly slug from a string
     * @param string $text Text to convert to slug
     * @return string URL-friendly slug
     */
    function createSlug($text) {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Replace special characters with spaces
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Remove dashes from beginning and end
        $text = trim($text, '-');
        
        // Replace multiple dashes with single dash
        $text = preg_replace('/-+/', '-', $text);
        
        return $text;
    }
    
    /**
     * Truncate text to specified length with ellipsis
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $ellipsis Ellipsis string
     * @return string Truncated text
     */
    function truncateText($text, $length = 100, $ellipsis = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length - strlen($ellipsis)) . $ellipsis;
    }
    
    /**
     * Capitalize each word in a string (title case)
     * @param string $text Text to capitalize
     * @return string Title-cased text
     */
    function titleCase($text) {
        // Convert to lowercase first
        $text = strtolower($text);
        
        // Capitalize first letter of each word
        $text = ucwords($text);
        
        // Handle common exceptions
        $exceptions = ['and', 'or', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for'];
        $words = explode(' ', $text);
        
        foreach ($words as $i => $word) {
            if ($i > 0 && in_array(strtolower($word), $exceptions)) {
                $words[$i] = strtolower($word);
            }
        }
        
        return implode(' ', $words);
    }
    
    // Test the functions
    echo "<h3>Email Validation:</h3>";
    $emails = [
        "test@example.com",
        "user.name@domain.co.uk",
        "invalid-email",
        "user@.com",
        "user@domain",
        "test+tag@example.com"
    ];
    
    foreach ($emails as $email) {
        echo "$email: " . (isValidEmail($email) ? "✅ Valid" : "❌ Invalid") . "<br>";
    }
    
    echo "<h3>String Validation:</h3>";
    $strings = [
        "HelloWorld",
        "Hello123",
        "Hello World",
        "123456",
        "Test_String"
    ];
    
    foreach ($strings as $string) {
        echo "'$string' - Alpha: " . (isAlpha($string) ? "Yes" : "No") . 
             ", Alphanumeric: " . (isAlphanumeric($string) ? "Yes" : "No") . "<br>";
    }
    
    echo "<h3>Phone Number Formatting:</h3>";
    $phones = [
        "1234567890",
        "(123) 456-7890",
        "123-456-7890",
        "123.456.7890",
        "123456789",  // Invalid (9 digits)
        "12345678901" // Invalid (11 digits)
    ];
    
    foreach ($phones as $phone) {
        echo "'$phone' → '" . formatPhoneNumber($phone) . "'<br>";
    }
    
    echo "<h3>Slug Creation:</h3>";
    $titles = [
        "Hello World! This is a Test",
        "PHP Functions & String Manipulation",
        "  Multiple   Spaces   Here  ",
        "Special Characters @#$%^&*()",
        "Title-Cased-String"
    ];
    
    foreach ($titles as $title) {
        echo "'$title' → '" . createSlug($title) . "'<br>";
    }
    
    echo "<h3>Text Truncation:</h3>";
    $longText = "This is a very long text that needs to be truncated to a specific length for display purposes.";
    echo "Original: " . strlen($longText) . " characters<br>";
    echo "50 chars: " . truncateText($longText, 50) . "<br>";
    echo "30 chars: " . truncateText($longText, 30) . "<br>";
    echo "20 chars: " . truncateText($longText, 20) . "<br>";
    
    echo "<h3>Title Case:</h3>";
    $titles = [
        "the quick brown fox jumps over the lazy dog",
        "HELLO WORLD IN UPPERCASE",
        "a tale of two cities",
        "lord of the rings: the fellowship of the ring"
    ];
    
    foreach ($titles as $title) {
        echo "'$title' → '" . titleCase($title) . "'<br>";
    }
?>
