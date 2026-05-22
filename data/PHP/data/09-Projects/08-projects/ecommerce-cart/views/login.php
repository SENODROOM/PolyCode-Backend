<?php
// Login page view
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h1>Login</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="login">
        
        <div class="form-group">
            <label for="username">Username or Email:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-success">Login</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center;">
        Don't have an account? <a href="?action=register">Register here</a>
    </p>
</div>

<div class="card" style="max-width: 400px; margin: 20px auto;">
    <h3>Demo Account</h3>
    <p><strong>Username:</strong> admin</p>
    <p><strong>Password:</strong> admin123</p>
    <p style="font-size: 0.9rem; color: #666;">
        Use this account to test the e-commerce functionality
    </p>
</div>
