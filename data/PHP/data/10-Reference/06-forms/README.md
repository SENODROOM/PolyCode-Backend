# Module 6: Forms & User Input 📝

Forms are the primary way users interact with web applications. PHP makes it easy to process form data, validate input, and create dynamic, interactive web experiences.

## 🎯 Learning Objectives

After completing this module, you will:
- Understand HTML forms and PHP processing
- Handle GET and POST requests
- Validate and sanitize user input
- Work with form data securely
- Create multi-step forms
- Handle file uploads
- Implement form security best practices

## 📝 Topics Covered

1. [HTML Forms Basics](#html-forms-basics)
2. [GET vs POST](#get-vs-post)
3. [Processing Form Data](#processing-form-data)
4. [Input Validation](#input-validation)
5. [Form Security](#form-security)
6. [File Uploads](#file-uploads)
7. [Multi-step Forms](#multi-step-forms)
8. [Practical Examples](#practical-examples)
9. [Exercises](#exercises)

---

## HTML Forms Basics

### Basic Form Structure
```html
<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
</head>
<body>
    <form action="process.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

### Common Input Types
```html
<!-- Text Input -->
<input type="text" name="username" placeholder="Enter username">

<!-- Password Input -->
<input type="password" name="password" required>

<!-- Email Input -->
<input type="email" name="email" required>

<!-- Number Input -->
<input type="number" name="age" min="1" max="120">

<!-- Date Input -->
<input type="date" name="birthdate">

<!-- Radio Buttons -->
<input type="radio" name="gender" value="male" id="male">
<label for="male">Male</label>

<input type="radio" name="gender" value="female" id="female">
<label for="female">Female</label>

<!-- Checkboxes -->
<input type="checkbox" name="newsletter" value="yes">
<label>Subscribe to newsletter</label>

<!-- Select Dropdown -->
<select name="country">
    <option value="">Select Country</option>
    <option value="us">United States</option>
    <option value="uk">United Kingdom</option>
    <option value="ca">Canada</option>
</select>

<!-- Textarea -->
<textarea name="message" rows="4" cols="50"></textarea>

<!-- File Upload -->
<input type="file" name="profile_picture" accept="image/*">

<!-- Hidden Input -->
<input type="hidden" name="form_id" value="contact_form">
```

---

## GET vs POST

### GET Method
```php
<?php
    // URL: process.php?name=John&email=john@example.com
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $name = $_GET["name"] ?? "";
        $email = $_GET["email"] ?? "";
        
        echo "Name: " . htmlspecialchars($name) . "<br>";
        echo "Email: " . htmlspecialchars($email) . "<br>";
    }
?>
```

**GET Characteristics:**
- Data sent in URL
- Limited data size
- Can be bookmarked
- Not secure for sensitive data
- Good for search forms

### POST Method
```php
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        
        echo "Name: " . htmlspecialchars($name) . "<br>";
        echo "Email: " . htmlspecialchars($email) . "<br>";
    }
?>
```

**POST Characteristics:**
- Data sent in HTTP body
- No size limitations
- More secure
- Cannot be bookmarked
- Good for sensitive data

---

## Processing Form Data

### Basic Form Processing
```php
<?php
    // process.php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $message = $_POST["message"] ?? "";
        
        // Basic validation
        if (empty($name) || empty($email) || empty($message)) {
            echo "Please fill all required fields.<br>";
        } else {
            // Process the data
            echo "Thank you, $name!<br>";
            echo "We'll contact you at: $email<br>";
            echo "Your message: $message<br>";
        }
    }
?>
```

### Using Superglobals
```php
<?php
    // Access all POST data
    echo "<h3>All POST Data:</h3>";
    foreach ($_POST as $key => $value) {
        echo "$key: " . htmlspecialchars($value) . "<br>";
    }
    
    // Check if form was submitted
    if (!empty($_POST)) {
        echo "<br>Form was submitted!";
    }
    
    // Get specific form data with default
    $username = $_POST["username"] ?? "Guest";
    echo "Welcome, $username!";
?>
```

### Form Processing Class
```php
<?php
    class FormProcessor {
        private array $data;
        private array $errors = [];
        
        public function __construct() {
            $this->data = $_POST;
        }
        
        public function get(string $field, $default = null) {
            return $this->data[$field] ?? $default;
        }
        
        public function validate(string $field, array $rules): bool {
            $value = $this->get($field);
            
            foreach ($rules as $rule) {
                if (!$this->validateRule($value, $rule)) {
                    return false;
                }
            }
            return true;
        }
        
        private function validateRule($value, string $rule): bool {
            switch ($rule) {
                case 'required':
                    return !empty($value);
                case 'email':
                    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                case 'numeric':
                    return is_numeric($value);
                default:
                    return true;
            }
        }
        
        public function getErrors(): array {
            return $this->errors;
        }
        
        public function hasErrors(): bool {
            return !empty($this->errors);
        }
    }
    
    // Usage
    $processor = new FormProcessor();
    
    if ($processor->validate('email', ['required', 'email'])) {
        echo "Valid email: " . $processor->get('email');
    } else {
        echo "Invalid email address";
    }
?>
```

---

## Input Validation

### Server-Side Validation
```php
<?php
    function validateForm(): array {
        $errors = [];
        
        // Name validation
        $name = $_POST["name"] ?? "";
        if (empty($name)) {
            $errors["name"] = "Name is required";
        } elseif (strlen($name) < 2) {
            $errors["name"] = "Name must be at least 2 characters";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $errors["name"] = "Name can only contain letters and spaces";
        }
        
        // Email validation
        $email = $_POST["email"] ?? "";
        if (empty($email)) {
            $errors["email"] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        }
        
        // Age validation
        $age = $_POST["age"] ?? "";
        if (empty($age)) {
            $errors["age"] = "Age is required";
        } elseif (!is_numeric($age)) {
            $errors["age"] = "Age must be a number";
        } elseif ($age < 1 || $age > 120) {
            $errors["age"] = "Age must be between 1 and 120";
        }
        
        return $errors;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = validateForm();
        
        if (empty($errors)) {
            echo "Form submitted successfully!";
        } else {
            echo "Please fix the following errors:<br>";
            foreach ($errors as $field => $error) {
                echo "- $error<br>";
            }
        }
    }
?>
```

### Input Sanitization
```php
<?php
    function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map('sanitizeInput', $data);
        }
        
        // Remove HTML tags
        $data = strip_tags($data);
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        // Remove extra whitespace
        $data = trim($data);
        
        return $data;
    }
    
    // Sanitize all POST data
    $sanitizedData = sanitizeInput($_POST);
    
    // Or sanitize individual fields
    $name = sanitizeInput($_POST["name"] ?? "");
    $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
    $age = filter_var($_POST["age"] ?? "", FILTER_SANITIZE_NUMBER_INT);
?>
```

### Validation with Filters
```php
<?php
    // Using PHP filter functions
    $filters = [
        "name" => FILTER_SANITIZE_STRING,
        "email" => FILTER_VALIDATE_EMAIL,
        "age" => [
            "filter" => FILTER_VALIDATE_INT,
            "options" => ["min_range" => 1, "max_range" => 120]
        ],
        "website" => FILTER_VALIDATE_URL
    ];
    
    $sanitized = filter_input_array(INPUT_POST, $filters);
    
    if ($sanitized === false) {
        echo "Invalid input data";
    } else {
        echo "Validated data:<br>";
        print_r($sanitized);
    }
?>
```

---

## Form Security

### CSRF Protection
```php
<?php
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
    
    // In your form:
    $token = generateCSRFToken();
    echo "<input type='hidden' name='csrf_token' value='$token'>";
    
    // In form processing:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $token = $_POST["csrf_token"] ?? "";
        
        if (!verifyCSRFToken($token)) {
            die("CSRF token validation failed");
        }
        
        // Process form...
    }
?>
```

### Rate Limiting
```php
<?php
    session_start();
    
    function checkRateLimit(int $maxAttempts = 5, int $timeWindow = 300): bool {
        $currentTime = time();
        $attempts = $_SESSION["form_attempts"] ?? [];
        
        // Remove old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($currentTime, $timeWindow) {
            return ($currentTime - $timestamp) < $timeWindow;
        });
        
        if (count($attempts) >= $maxAttempts) {
            return false; // Rate limit exceeded
        }
        
        // Add current attempt
        $attempts[] = $currentTime;
        $_SESSION["form_attempts"] = $attempts;
        
        return true;
    }
    
    // Usage
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!checkRateLimit()) {
            die("Too many form submissions. Please try again later.");
        }
        
        // Process form...
    }
?>
```

### Input Filtering and Escaping
```php
<?php
    class SecureForm {
        public static function cleanInput($input) {
            if (is_array($input)) {
                return array_map([self::class, 'cleanInput'], $input);
            }
            
            // Remove magic quotes if enabled
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            
            // Trim whitespace
            $input = trim($input);
            
            return $input;
        }
        
        public static function escapeOutput($string): string {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
        
        public static function validateEmail($email): bool {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        public static function sanitizeString($string): string {
            return filter_var($string, FILTER_SANITIZE_STRING);
        }
        
        public static function sanitizeInt($int): int {
            return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
        }
    }
    
    // Usage
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cleanData = SecureForm::cleanInput($_POST);
        
        $name = SecureForm::escapeOutput($cleanData["name"] ?? "");
        $email = $cleanData["email"] ?? "";
        
        if (SecureForm::validateEmail($email)) {
            echo "Welcome, $name!";
        } else {
            echo "Invalid email address";
        }
    }
?>
```

---

## File Uploads

### HTML Upload Form
```html
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <input type="submit" value="Upload">
</form>
```

### PHP File Upload Processing
```php
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
        $file = $_FILES["file"];
        
        // Check if file was uploaded
        if ($file["error"] !== UPLOAD_ERR_OK) {
            echo "File upload failed with error code: " . $file["error"];
            exit;
        }
        
        // Validate file
        $allowedTypes = ["image/jpeg", "image/png", "image/gif", "application/pdf"];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file["type"], $allowedTypes)) {
            echo "File type not allowed";
            exit;
        }
        
        if ($file["size"] > $maxSize) {
            echo "File too large";
            exit;
        }
        
        // Generate unique filename
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $newFilename = uniqid() . "." . $extension;
        $uploadDir = "uploads/";
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move file
        if (move_uploaded_file($file["tmp_name"], $uploadDir . $newFilename)) {
            echo "File uploaded successfully: $newFilename";
        } else {
            echo "Failed to move uploaded file";
        }
    }
?>
```

### Advanced Upload Handler
```php
<?php
    class FileUploader {
        private array $allowedTypes = [];
        private int $maxSize;
        private string $uploadDir;
        private array $errors = [];
        
        public function __construct(string $uploadDir = "uploads/", int $maxSize = 5242880) {
            $this->uploadDir = $uploadDir;
            $this->maxSize = $maxSize;
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
        }
        
        public function setAllowedTypes(array $types): void {
            $this->allowedTypes = $types;
        }
        
        public function upload(string $fieldName): ?string {
            if (!isset($_FILES[$fieldName])) {
                $this->errors[] = "No file uploaded";
                return null;
            }
            
            $file = $_FILES[$fieldName];
            
            if ($file["error"] !== UPLOAD_ERR_OK) {
                $this->errors[] = $this->getUploadErrorMessage($file["error"]);
                return null;
            }
            
            if (!$this->validateFile($file)) {
                return null;
            }
            
            return $this->moveFile($file);
        }
        
        private function validateFile(array $file): bool {
            // Check file size
            if ($file["size"] > $this->maxSize) {
                $this->errors[] = "File too large";
                return false;
            }
            
            // Check file type
            if (!empty($this->allowedTypes) && !in_array($file["type"], $this->allowedTypes)) {
                $this->errors[] = "File type not allowed";
                return false;
            }
            
            return true;
        }
        
        private function moveFile(array $file): ?string {
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            $newFilename = uniqid() . "." . $extension;
            $destination = $this->uploadDir . $newFilename;
            
            if (move_uploaded_file($file["tmp_name"], $destination)) {
                return $newFilename;
            }
            
            $this->errors[] = "Failed to move uploaded file";
            return null;
        }
        
        private function getUploadErrorMessage(int $errorCode): string {
            return match($errorCode) {
                UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize",
                UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                default => "Unknown upload error"
            };
        }
        
        public function getErrors(): array {
            return $this->errors;
        }
    }
    
    // Usage
    $uploader = new FileUploader("uploads/", 10485760); // 10MB
    $uploader->setAllowedTypes(["image/jpeg", "image/png", "application/pdf"]);
    
    $filename = $uploader->upload("document");
    
    if ($filename) {
        echo "File uploaded: $filename";
    } else {
        echo "Upload failed: " . implode(", ", $uploader->getErrors());
    }
?>
```

---

## Multi-step Forms

### Step-by-Step Form
```php
<?php
    session_start();
    
    class MultiStepForm {
        private array $steps = [];
        private int $currentStep = 1;
        private int $totalSteps;
        
        public function __construct(array $steps) {
            $this->steps = $steps;
            $this->totalSteps = count($steps);
            $this->currentStep = $_SESSION["current_step"] ?? 1;
        }
        
        public function getCurrentStep(): int {
            return $this->currentStep;
        }
        
        public function getTotalSteps(): int {
            return $this->totalSteps;
        }
        
        public function nextStep(): void {
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
                $_SESSION["current_step"] = $this->currentStep;
            }
        }
        
        public function previousStep(): void {
            if ($this->currentStep > 1) {
                $this->currentStep--;
                $_SESSION["current_step"] = $this->currentStep;
            }
        }
        
        public function saveStepData(array $data): void {
            $_SESSION["step_data"][$this->currentStep] = $data;
        }
        
        public function getStepData(int $step = null): array {
            $step = $step ?? $this->currentStep;
            return $_SESSION["step_data"][$step] ?? [];
        }
        
        public function getAllData(): array {
            $allData = [];
            for ($i = 1; $i <= $this->totalSteps; $i++) {
                $allData = array_merge($allData, $this->getStepData($i));
            }
            return $allData;
        }
        
        public function reset(): void {
            unset($_SESSION["current_step"]);
            unset($_SESSION["step_data"]);
            $this->currentStep = 1;
        }
        
        public function renderStep(): string {
            $stepData = $this->steps[$this->currentStep - 1];
            $savedData = $this->getStepData();
            
            $html = "<h2>Step {$this->currentStep} of {$this->totalSteps}: {$stepData['title']}</h2>";
            $html .= "<form method='post'>";
            
            foreach ($stepData['fields'] as $field => $config) {
                $value = $savedData[$field] ?? "";
                $html .= $this->renderField($field, $config, $value);
            }
            
            $html .= "<div class='navigation'>";
            
            if ($this->currentStep > 1) {
                $html .= "<button type='submit' name='action' value='previous'>Previous</button>";
            }
            
            if ($this->currentStep < $this->totalSteps) {
                $html .= "<button type='submit' name='action' value='next'>Next</button>";
            } else {
                $html .= "<button type='submit' name='action' value='finish'>Finish</button>";
            }
            
            $html .= "</div></form>";
            
            return $html;
        }
        
        private function renderField(string $name, array $config, string $value): string {
            $html = "<div class='field'>";
            $html .= "<label for='$name'>{$config['label']}</label>";
            
            switch ($config['type']) {
                case 'text':
                case 'email':
                case 'password':
                    $html .= "<input type='{$config['type']}' id='$name' name='$name' value='$value' required>";
                    break;
                case 'textarea':
                    $html .= "<textarea id='$name' name='$name' required>$value</textarea>";
                    break;
                case 'select':
                    $html .= "<select id='$name' name='$name' required>";
                    foreach ($config['options'] as $optionValue => $optionLabel) {
                        $selected = $optionValue == $value ? "selected" : "";
                        $html .= "<option value='$optionValue' $selected>$optionLabel</option>";
                    }
                    $html .= "</select>";
                    break;
            }
            
            $html .= "</div>";
            return $html;
        }
    }
    
    // Usage
    $formSteps = [
        [
            'title' => 'Personal Information',
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Full Name'],
                'email' => ['type' => 'email', 'label' => 'Email Address']
            ]
        ],
        [
            'title' => 'Address Information',
            'fields' => [
                'address' => ['type' => 'text', 'label' => 'Street Address'],
                'city' => ['type' => 'text', 'label' => 'City'],
                'country' => [
                    'type' => 'select',
                    'label' => 'Country',
                    'options' => ['us' => 'United States', 'uk' => 'United Kingdom', 'ca' => 'Canada']
                ]
            ]
        ],
        [
            'title' => 'Additional Information',
            'fields' => [
                'phone' => ['type' => 'text', 'label' => 'Phone Number'],
                'message' => ['type' => 'textarea', 'label' => 'Message']
            ]
        ]
    ];
    
    $form = new MultiStepForm($formSteps);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST["action"] ?? "";
        
        // Save current step data
        $form->saveStepData($_POST);
        
        if ($action === "next") {
            $form->nextStep();
        } elseif ($action === "previous") {
            $form->previousStep();
        } elseif ($action === "finish") {
            // Process complete form
            $allData = $form->getAllData();
            echo "<h2>Form Completed!</h2>";
            echo "<pre>" . print_r($allData, true) . "</pre>";
            $form->reset();
            exit;
        }
    }
    
    echo $form->renderStep();
?>
```

---

## Practical Examples

### Example 1: Contact Form with Validation
```php
<?php
    // contact.php
    class ContactForm {
        private array $data = [];
        private array $errors = [];
        
        public function __construct() {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->processForm();
            }
        }
        
        private function processForm(): void {
            $this->data = [
                "name" => trim($_POST["name"] ?? ""),
                "email" => trim($_POST["email"] ?? ""),
                "subject" => trim($_POST["subject"] ?? ""),
                "message" => trim($_POST["message"] ?? "")
            ];
            
            $this->validate();
            
            if (empty($this->errors)) {
                $this->sendEmail();
                $this->data = []; // Clear form
                $this->success = true;
            }
        }
        
        private function validate(): void {
            if (empty($this->data["name"])) {
                $this->errors["name"] = "Name is required";
            } elseif (strlen($this->data["name"]) < 2) {
                $this->errors["name"] = "Name must be at least 2 characters";
            }
            
            if (empty($this->data["email"])) {
                $this->errors["email"] = "Email is required";
            } elseif (!filter_var($this->data["email"], FILTER_VALIDATE_EMAIL)) {
                $this->errors["email"] = "Invalid email format";
            }
            
            if (empty($this->data["subject"])) {
                $this->errors["subject"] = "Subject is required";
            }
            
            if (empty($this->data["message"])) {
                $this->errors["message"] = "Message is required";
            } elseif (strlen($this->data["message"]) < 10) {
                $this->errors["message"] = "Message must be at least 10 characters";
            }
        }
        
        private function sendEmail(): void {
            $to = "admin@example.com";
            $subject = "Contact Form: " . $this->data["subject"];
            $message = "From: " . $this->data["name"] . " (" . $this->data["email"] . ")\n\n";
            $message .= $this->data["message"];
            
            $headers = "From: " . $this->data["email"] . "\r\n";
            $headers .= "Reply-To: " . $this->data["email"];
            
            mail($to, $subject, $message, $headers);
        }
        
        public function render(): string {
            $html = "<!DOCTYPE html><html><head><title>Contact Form</title></head><body>";
            
            if (isset($this->success)) {
                $html .= "<h2>Thank you! Your message has been sent.</h2>";
            }
            
            $html .= "<h1>Contact Us</h1>";
            $html .= "<form method='post'>";
            
            // Name field
            $html .= "<div>";
            $html .= "<label for='name'>Name:</label>";
            $html .= "<input type='text' id='name' name='name' value='" . htmlspecialchars($this->data["name"] ?? "") . "'>";
            if (isset($this->errors["name"])) {
                $html .= "<span class='error'>{$this->errors['name']}</span>";
            }
            $html .= "</div>";
            
            // Email field
            $html .= "<div>";
            $html .= "<label for='email'>Email:</label>";
            $html .= "<input type='email' id='email' name='email' value='" . htmlspecialchars($this->data["email"] ?? "") . "'>";
            if (isset($this->errors["email"])) {
                $html .= "<span class='error'>{$this->errors['email']}</span>";
            }
            $html .= "</div>";
            
            // Subject field
            $html .= "<div>";
            $html .= "<label for='subject'>Subject:</label>";
            $html .= "<input type='text' id='subject' name='subject' value='" . htmlspecialchars($this->data["subject"] ?? "") . "'>";
            if (isset($this->errors["subject"])) {
                $html .= "<span class='error'>{$this->errors['subject']}</span>";
            }
            $html .= "</div>";
            
            // Message field
            $html .= "<div>";
            $html .= "<label for='message'>Message:</label>";
            $html .= "<textarea id='message' name='message' rows='5'>" . htmlspecialchars($this->data["message"] ?? "") . "</textarea>";
            if (isset($this->errors["message"])) {
                $html .= "<span class='error'>{$this->errors['message']}</span>";
            }
            $html .= "</div>";
            
            $html .= "<button type='submit'>Send Message</button>";
            $html .= "</form>";
            $html .= "</body></html>";
            
            return $html;
        }
    }
    
    $contactForm = new ContactForm();
    echo $contactForm->render();
?>
```

---

## Exercises

### Exercise 1: Registration Form
Create a registration form that:
1. Collects user information (name, email, password)
2. Validates all inputs
3. Shows success/error messages
4. Implements security measures

**Solution:** [exercise1.php](exercise1.php)

### Exercise 2: File Upload Gallery
Create a file upload system that:
1. Allows image uploads
2. Validates file types and sizes
3. Creates thumbnails
4. Displays uploaded images

**Solution:** [exercise2.php](exercise2.php)

### Exercise 3: Survey Form
Create a multi-step survey form that:
1. Has multiple steps
2. Saves progress between steps
3. Validates each step
4. Shows completion summary

**Solution:** [exercise3.php](exercise3.php)

---

## 🎯 Module Completion Checklist

- [ ] I understand HTML forms and PHP processing
- [ ] I can handle GET and POST requests
- [ ] I can validate and sanitize user input
- [ ] I understand form security best practices
- [ ] I can handle file uploads
- [ ] I can create multi-step forms
- [ ] I completed all exercises

---

**Ready for the next module?** ➡️ [Module 7: Database Connectivity](../07-database/README.md)
