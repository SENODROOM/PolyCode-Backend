<?php
// Login page view
if (isLoggedIn()) {
    header('Location: ?action=home');
    exit;
}
?>

<div class="card">
    <h1>Login</h1>
    
    <form method="post" class="login-form">
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

<div class="card" style="margin-top: 20px;">
    <h3>Demo Accounts</h3>
    <p><strong>Admin:</strong> admin / admin123</p>
    <p><strong>Test User:</strong> You can register a new account</p>
</div>
