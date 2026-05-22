<?php
// Registration page view
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h1>Register</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="register">
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required minlength="3" maxlength="50">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required minlength="8">
            <small>Password must be at least 8 characters</small>
        </div>
        
        <button type="submit" class="btn btn-success">Register</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center;">
        Already have an account? <a href="?action=login">Login here</a>
    </p>
</div>
