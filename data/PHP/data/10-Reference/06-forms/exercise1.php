<?php
    // Exercise 1: Registration Form with Validation and Security
    
    // Start session for CSRF protection
    session_start();
    
    // Generate CSRF token
    function generateCSRFToken(): string {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_token"];
    }
    
    // Verify CSRF token
    function verifyCSRFToken($token): bool {
        return isset($_SESSION["csrf_token"]) && 
               hash_equals($_SESSION["csrf_token"], $token);
    }
    
    // Rate limiting
    function checkRateLimit(int $maxAttempts = 3, int $timeWindow = 900): bool {
        $currentTime = time();
        $attempts = $_SESSION["registration_attempts"] ?? [];
        
        // Remove old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($currentTime, $timeWindow) {
            return ($currentTime - $timestamp) < $timeWindow;
        });
        
        if (count($attempts) >= $maxAttempts) {
            return false;
        }
        
        $attempts[] = $currentTime;
        $_SESSION["registration_attempts"] = $attempts;
        
        return true;
    }
    
    // Registration class
    class RegistrationForm {
        private array $data = [];
        private array $errors = [];
        private bool $success = false;
        private array $users = [];
        
        public function __construct() {
            // Load existing users (in real app, this would be from database)
            $this->users = $_SESSION["users"] ?? [];
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->processRegistration();
            }
        }
        
        private function processRegistration(): void {
            // CSRF protection
            if (!verifyCSRFToken($_POST["csrf_token"] ?? "")) {
                $this->errors["security"] = "Security token validation failed";
                return;
            }
            
            // Rate limiting
            if (!checkRateLimit()) {
                $this->errors["rate_limit"] = "Too many registration attempts. Please try again later.";
                return;
            }
            
            // Collect and sanitize data
            $this->data = [
                "first_name" => trim($_POST["first_name"] ?? ""),
                "last_name" => trim($_POST["last_name"] ?? ""),
                "email" => trim(strtolower($_POST["email"] ?? "")),
                "password" => $_POST["password"] ?? "",
                "confirm_password" => $_POST["confirm_password"] ?? "",
                "phone" => trim($_POST["phone"] ?? ""),
                "birthdate" => $_POST["birthdate"] ?? "",
                "gender" => $_POST["gender"] ?? "",
                "newsletter" => isset($_POST["newsletter"]) ? 1 : 0,
                "terms" => isset($_POST["terms"]) ? 1 : 0
            ];
            
            $this->validate();
            
            if (empty($this->errors)) {
                $this->registerUser();
            }
        }
        
        private function validate(): void {
            // First Name
            if (empty($this->data["first_name"])) {
                $this->errors["first_name"] = "First name is required";
            } elseif (strlen($this->data["first_name"]) < 2) {
                $this->errors["first_name"] = "First name must be at least 2 characters";
            } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $this->data["first_name"])) {
                $this->errors["first_name"] = "First name can only contain letters, spaces, hyphens, and apostrophes";
            }
            
            // Last Name
            if (empty($this->data["last_name"])) {
                $this->errors["last_name"] = "Last name is required";
            } elseif (strlen($this->data["last_name"]) < 2) {
                $this->errors["last_name"] = "Last name must be at least 2 characters";
            } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $this->data["last_name"])) {
                $this->errors["last_name"] = "Last name can only contain letters, spaces, hyphens, and apostrophes";
            }
            
            // Email
            if (empty($this->data["email"])) {
                $this->errors["email"] = "Email is required";
            } elseif (!filter_var($this->data["email"], FILTER_VALIDATE_EMAIL)) {
                $this->errors["email"] = "Invalid email format";
            } elseif ($this->emailExists($this->data["email"])) {
                $this->errors["email"] = "Email already registered";
            }
            
            // Password
            if (empty($this->data["password"])) {
                $this->errors["password"] = "Password is required";
            } elseif (strlen($this->data["password"]) < 8) {
                $this->errors["password"] = "Password must be at least 8 characters";
            } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/", $this->data["password"])) {
                $this->errors["password"] = "Password must contain uppercase, lowercase, number, and special character";
            }
            
            // Confirm Password
            if ($this->data["password"] !== $this->data["confirm_password"]) {
                $this->errors["confirm_password"] = "Passwords do not match";
            }
            
            // Phone (optional but if provided, validate)
            if (!empty($this->data["phone"])) {
                if (!preg_match("/^[\d\s\-\+\(\)]+$/", $this->data["phone"])) {
                    $this->errors["phone"] = "Invalid phone number format";
                } elseif (strlen(preg_replace("/\D/", "", $this->data["phone"])) < 10) {
                    $this->errors["phone"] = "Phone number must have at least 10 digits";
                }
            }
            
            // Birthdate
            if (empty($this->data["birthdate"])) {
                $this->errors["birthdate"] = "Birthdate is required";
            } else {
                $birthdate = DateTime::createFromFormat('Y-m-d', $this->data["birthdate"]);
                if (!$birthdate) {
                    $this->errors["birthdate"] = "Invalid birthdate format";
                } else {
                    $age = $birthdate->diff(new DateTime())->y;
                    if ($age < 13) {
                        $this->errors["birthdate"] = "You must be at least 13 years old";
                    } elseif ($age > 120) {
                        $this->errors["birthdate"] = "Invalid birthdate";
                    }
                }
            }
            
            // Gender
            if (empty($this->data["gender"])) {
                $this->errors["gender"] = "Please select your gender";
            } elseif (!in_array($this->data["gender"], ['male', 'female', 'other'])) {
                $this->errors["gender"] = "Invalid gender selection";
            }
            
            // Terms
            if (!$this->data["terms"]) {
                $this->errors["terms"] = "You must agree to the terms and conditions";
            }
        }
        
        private function emailExists(string $email): bool {
            foreach ($this->users as $user) {
                if ($user["email"] === $email) {
                    return true;
                }
            }
            return false;
        }
        
        private function registerUser(): void {
            // Hash password
            $hashedPassword = password_hash($this->data["password"], PASSWORD_DEFAULT);
            
            // Create user record
            $user = [
                "id" => count($this->users) + 1,
                "first_name" => htmlspecialchars($this->data["first_name"]),
                "last_name" => htmlspecialchars($this->data["last_name"]),
                "email" => $this->data["email"],
                "password" => $hashedPassword,
                "phone" => htmlspecialchars($this->data["phone"]),
                "birthdate" => $this->data["birthdate"],
                "gender" => $this->data["gender"],
                "newsletter" => $this->data["newsletter"],
                "registered_at" => date("Y-m-d H:i:s")
            ];
            
            // Save user (in real app, save to database)
            $this->users[] = $user;
            $_SESSION["users"] = $this->users;
            
            // Clear sensitive data
            unset($this->data["password"]);
            unset($this->data["confirm_password"]);
            
            $this->success = true;
            
            // Clear rate limit on successful registration
            unset($_SESSION["registration_attempts"]);
        }
        
        public function render(): string {
            $csrfToken = generateCSRFToken();
            
            $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>User Registration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        input[type='checkbox'] { width: auto; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .requirements { font-size: 12px; color: #666; margin-top: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .progress { background: #f0f0f0; border-radius: 4px; margin-bottom: 20px; }
        .progress-bar { background: #007bff; height: 20px; border-radius: 4px; transition: width 0.3s; }
    </style>
</head>
<body>
    <h1>Create Your Account</h1>";
            
            if ($this->success) {
                $html .= "<div class='success'>
                    <h2>Registration Successful!</h2>
                    <p>Thank you for registering, {$this->data['first_name']}!</p>
                    <p>Your account has been created and you can now log in.</p>
                    <p><strong>User ID:</strong> " . end($this->users)["id"] . "</p>
                    <p><strong>Email:</strong> {$this->data['email']}</p>
                </div>";
            } else {
                // Show errors if any
                if (!empty($this->errors)) {
                    $html .= "<div class='error' style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;'>";
                    $html .= "<strong>Please fix the following errors:</strong><ul>";
                    foreach ($this->errors as $error) {
                        $html .= "<li>$error</li>";
                    }
                    $html .= "</ul></div>";
                }
                
                $html .= "<form method='post'>
                    <input type='hidden' name='csrf_token' value='$csrfToken'>
                    
                    <div class='form-group'>
                        <label for='first_name'>First Name *</label>
                        <input type='text' id='first_name' name='first_name' value='" . htmlspecialchars($this->data["first_name"] ?? "") . "' required>
                        <span class='error'>" . ($this->errors["first_name"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='last_name'>Last Name *</label>
                        <input type='text' id='last_name' name='last_name' value='" . htmlspecialchars($this->data["last_name"] ?? "") . "' required>
                        <span class='error'>" . ($this->errors["last_name"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='email'>Email Address *</label>
                        <input type='email' id='email' name='email' value='" . htmlspecialchars($this->data["email"] ?? "") . "' required>
                        <span class='error'>" . ($this->errors["email"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='password'>Password *</label>
                        <input type='password' id='password' name='password' required>
                        <div class='requirements'>
                            Password must contain: 8+ characters, uppercase, lowercase, number, and special character
                        </div>
                        <span class='error'>" . ($this->errors["password"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='confirm_password'>Confirm Password *</label>
                        <input type='password' id='confirm_password' name='confirm_password' required>
                        <span class='error'>" . ($this->errors["confirm_password"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='phone'>Phone Number</label>
                        <input type='tel' id='phone' name='phone' value='" . htmlspecialchars($this->data["phone"] ?? "") . "' placeholder='(123) 456-7890'>
                        <span class='error'>" . ($this->errors["phone"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label for='birthdate'>Birthdate *</label>
                        <input type='date' id='birthdate' name='birthdate' value='" . htmlspecialchars($this->data["birthdate"] ?? "") . "' required>
                        <span class='error'>" . ($this->errors["birthdate"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <label>Gender *</label>
                        <div>
                            <input type='radio' id='male' name='gender' value='male' " . ($this->data["gender"] == "male" ? "checked" : "") . ">
                            <label for='male' style='display: inline; font-weight: normal;'>Male</label>
                            
                            <input type='radio' id='female' name='gender' value='female' " . ($this->data["gender"] == "female" ? "checked" : "") . ">
                            <label for='female' style='display: inline; font-weight: normal;'>Female</label>
                            
                            <input type='radio' id='other' name='gender' value='other' " . ($this->data["gender"] == "other" ? "checked" : "") . ">
                            <label for='other' style='display: inline; font-weight: normal;'>Other</label>
                        </div>
                        <span class='error'>" . ($this->errors["gender"] ?? "") . "</span>
                    </div>
                    
                    <div class='form-group'>
                        <div>
                            <input type='checkbox' id='newsletter' name='newsletter' value='1' " . ($this->data["newsletter"] ? "checked" : "") . ">
                            <label for='newsletter' style='display: inline; font-weight: normal;'>Subscribe to newsletter</label>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <div>
                            <input type='checkbox' id='terms' name='terms' value='1' " . ($this->data["terms"] ? "checked" : "") . ">
                            <label for='terms' style='display: inline; font-weight: normal;'>I agree to the Terms and Conditions *</label>
                        </div>
                        <span class='error'>" . ($this->errors["terms"] ?? "") . "</span>
                    </div>
                    
                    <button type='submit'>Register</button>
                </form>";
            }
            
            $html .= "</body></html>";
            return $html;
        }
    }
    
    // Display the registration form
    $registration = new RegistrationForm();
    echo $registration->render();
?>
