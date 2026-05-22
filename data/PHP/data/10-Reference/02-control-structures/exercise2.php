<?php
    // Exercise 2: Login Simulator
    
    echo "<h2>Login Simulator</h2>";
    
    // Simulated user input (in real app, this would come from $_POST)
    $username = "admin";
    $password = "password123";
    
    // Valid credentials
    $valid_username = "admin";
    $valid_password = "password123";
    
    echo "Attempting login with username: '$username'<br><br>";
    
    // Check credentials
    if ($username == $valid_username && $password == $valid_password) {
        echo "✅ Login successful! Welcome, $username!<br>";
        echo "Redirecting to dashboard...";
    } elseif ($username == $valid_username && $password != $valid_password) {
        echo "❌ Incorrect password for username '$username'<br>";
        echo "Please try again.";
    } elseif ($username != $valid_username && $password == $valid_password) {
        echo "❌ Username '$username' not found<br>";
        echo "Please check your username.";
    } else {
        echo "❌ Invalid username and password<br>";
        echo "Please check both credentials.";
    }
    
    echo "<br><br><h3>Test Scenarios:</h3>";
    
    // Test different scenarios
    $test_cases = [
        ["admin", "wrongpass", "Wrong password"],
        ["user", "password123", "Wrong username"],
        ["user", "wrongpass", "Both wrong"],
        ["", "", "Empty credentials"]
    ];
    
    foreach ($test_cases as $case) {
        echo "<br>Testing: '{$case[0]}' / '{$case[1]}' ({$case[2]})<br>";
        
        if ($case[0] == $valid_username && $case[1] == $valid_password) {
            echo "Result: ✅ Success";
        } elseif ($case[0] == $valid_username && $case[1] != $valid_password) {
            echo "Result: ❌ Wrong password";
        } elseif ($case[0] != $valid_username && $case[1] == $valid_password) {
            echo "Result: ❌ Wrong username";
        } else {
            echo "Result: ❌ Both wrong";
        }
    }
?>
