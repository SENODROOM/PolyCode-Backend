<?php
    // Exercise 3: Multi-Step Survey Form
    
    session_start();
    
    class SurveyForm {
        private array $steps;
        private int $currentStep;
        private array $stepData = [];
        private bool $completed = false;
        private array $errors = [];
        
        public function __construct() {
            $this->steps = $this->defineSteps();
            $this->currentStep = $_SESSION["survey_current_step"] ?? 1;
            $this->stepData = $_SESSION["survey_step_data"] ?? [];
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->handleFormSubmission();
            }
            
            $this->completed = $_SESSION["survey_completed"] ?? false;
        }
        
        private function defineSteps(): array {
            return [
                1 => [
                    "title" => "Personal Information",
                    "description" => "Tell us about yourself",
                    "fields" => [
                        "first_name" => [
                            "type" => "text",
                            "label" => "First Name",
                            "required" => true,
                            "validation" => ["required", "min_length:2", "max_length:50", "letters_only"]
                        ],
                        "last_name" => [
                            "type" => "text",
                            "label" => "Last Name",
                            "required" => true,
                            "validation" => ["required", "min_length:2", "max_length:50", "letters_only"]
                        ],
                        "email" => [
                            "type" => "email",
                            "label" => "Email Address",
                            "required" => true,
                            "validation" => ["required", "email"]
                        ],
                        "phone" => [
                            "type" => "tel",
                            "label" => "Phone Number",
                            "required" => false,
                            "validation" => ["phone"]
                        ],
                        "birthdate" => [
                            "type" => "date",
                            "label" => "Date of Birth",
                            "required" => true,
                            "validation" => ["required", "date", "age_min:13", "age_max:120"]
                        ]
                    ]
                ],
                2 => [
                    "title" => "Professional Information",
                    "description" => "Tell us about your work",
                    "fields" => [
                        "employment_status" => [
                            "type" => "select",
                            "label" => "Employment Status",
                            "required" => true,
                            "options" => [
                                "" => "Select Status",
                                "employed" => "Employed",
                                "self_employed" => "Self-Employed",
                                "unemployed" => "Unemployed",
                                "student" => "Student",
                                "retired" => "Retired"
                            ],
                            "validation" => ["required", "in:employed,self_employed,unemployed,student,retired"]
                        ],
                        "industry" => [
                            "type" => "text",
                            "label" => "Industry/Field",
                            "required" => true,
                            "validation" => ["required", "min_length:2", "max_length:100"]
                        ],
                        "years_experience" => [
                            "type" => "number",
                            "label" => "Years of Experience",
                            "required" => true,
                            "min" => 0,
                            "max" => 50,
                            "validation" => ["required", "number", "min:0", "max:50"]
                        ],
                        "job_title" => [
                            "type" => "text",
                            "label" => "Current Job Title",
                            "required" => false,
                            "validation" => ["max_length:100"]
                        ],
                        "company" => [
                            "type" => "text",
                            "label" => "Company Name",
                            "required" => false,
                            "validation" => ["max_length:100"]
                        ]
                    ]
                ],
                3 => [
                    "title" => "Education",
                    "description" => "Tell us about your education",
                    "fields" => [
                        "highest_education" => [
                            "type" => "select",
                            "label" => "Highest Level of Education",
                            "required" => true,
                            "options" => [
                                "" => "Select Education Level",
                                "high_school" => "High School",
                                "some_college" => "Some College",
                                "associates" => "Associate's Degree",
                                "bachelors" => "Bachelor's Degree",
                                "masters" => "Master's Degree",
                                "phd" => "PhD",
                                "other" => "Other"
                            ],
                            "validation" => ["required", "in:high_school,some_college,associates,bachelors,masters,phd,other"]
                        ],
                        "field_of_study" => [
                            "type" => "text",
                            "label" => "Field of Study",
                            "required" => true,
                            "validation" => ["required", "min_length:2", "max_length:100"]
                        ],
                        "institution" => [
                            "type" => "text",
                            "label" => "Institution Name",
                            "required" => true,
                            "validation" => ["required", "min_length:2", "max_length:100"]
                        ],
                        "graduation_year" => [
                            "type" => "number",
                            "label" => "Graduation Year",
                            "required" => true,
                            "min" => 1950,
                            "max" => date("Y") + 5,
                            "validation" => ["required", "number", "min:1950", "max:" . (date("Y") + 5)]
                        ]
                    ]
                ],
                4 => [
                    "title" => "Interests & Preferences",
                    "description" => "Tell us about your interests",
                    "fields" => [
                        "hobbies" => [
                            "type" => "textarea",
                            "label" => "Hobbies and Interests",
                            "required" => true,
                            "rows" => 4,
                            "validation" => ["required", "min_length:10", "max_length:500"]
                        ],
                        "favorite_activities" => [
                            "type" => "checkbox_group",
                            "label" => "Favorite Activities",
                            "required" => true,
                            "options" => [
                                "reading" => "Reading",
                                "sports" => "Sports",
                                "travel" => "Travel",
                                "cooking" => "Cooking",
                                "music" => "Music",
                                "gaming" => "Gaming",
                                "art" => "Art",
                                "technology" => "Technology"
                            ],
                            "validation" => ["required", "min_checkboxes:1"]
                        ],
                        "newsletter_topics" => [
                            "type" => "checkbox_group",
                            "label" => "Newsletter Topics of Interest",
                            "required" => false,
                            "options" => [
                                "technology" => "Technology",
                                "business" => "Business",
                                "health" => "Health & Wellness",
                                "education" => "Education",
                                "entertainment" => "Entertainment",
                                "science" => "Science"
                            ]
                        ]
                    ]
                ],
                5 => [
                    "title" => "Feedback & Comments",
                    "description" => "Share your thoughts with us",
                    "fields" => [
                        "satisfaction" => [
                            "type" => "radio",
                            "label" => "Overall Satisfaction with this Survey",
                            "required" => true,
                            "options" => [
                                "very_satisfied" => "Very Satisfied",
                                "satisfied" => "Satisfied",
                                "neutral" => "Neutral",
                                "dissatisfied" => "Dissatisfied",
                                "very_dissatisfied" => "Very Dissatisfied"
                            ],
                            "validation" => ["required"]
                        ],
                        "improvement_suggestions" => [
                            "type" => "textarea",
                            "label" => "How can we improve this survey?",
                            "required" => false,
                            "rows" => 4,
                            "validation" => ["max_length:1000"]
                        ],
                        "additional_comments" => [
                            "type" => "textarea",
                            "label" => "Additional Comments",
                            "required" => false,
                            "rows" => 4,
                            "validation" => ["max_length:1000"]
                        ],
                        "contact_permission" => [
                            "type" => "radio",
                            "label" => "May we contact you for follow-up?",
                            "required" => true,
                            "options" => [
                                "yes" => "Yes",
                                "no" => "No"
                            ],
                            "validation" => ["required"]
                        ]
                    ]
                ]
            ];
        }
        
        private function handleFormSubmission(): void {
            $action = $_POST["action"] ?? "";
            
            if ($action === "next") {
                if ($this->validateCurrentStep()) {
                    $this->saveCurrentStepData();
                    $this->nextStep();
                }
            } elseif ($action === "previous") {
                $this->previousStep();
            } elseif ($action === "finish") {
                if ($this->validateCurrentStep()) {
                    $this->saveCurrentStepData();
                    $this->completeSurvey();
                }
            } elseif ($action === "restart") {
                $this->restart();
            }
        }
        
        private function validateCurrentStep(): bool {
            $this->errors = [];
            $stepFields = $this->steps[$this->currentStep]["fields"];
            
            foreach ($stepFields as $fieldName => $fieldConfig) {
                $value = $this->getFieldValue($fieldName);
                
                if ($fieldConfig["required"] && empty($value)) {
                    $this->errors[$fieldName] = $fieldConfig["label"] . " is required";
                    continue;
                }
                
                if (!empty($value) && isset($fieldConfig["validation"])) {
                    foreach ($fieldConfig["validation"] as $rule) {
                        $error = $this->validateField($value, $rule, $fieldConfig["label"]);
                        if ($error) {
                            $this->errors[$fieldName] = $error;
                            break;
                        }
                    }
                }
            }
            
            return empty($this->errors);
        }
        
        private function getFieldValue(string $fieldName) {
            $fieldConfig = $this->steps[$this->currentStep]["fields"][$fieldName] ?? [];
            
            switch ($fieldConfig["type"] ?? "") {
                case "checkbox_group":
                    return $_POST[$fieldName] ?? [];
                default:
                    return trim($_POST[$fieldName] ?? "");
            }
        }
        
        private function validateField($value, string $rule, string $label): ?string {
            $parts = explode(":", $rule);
            $ruleName = $parts[0];
            $ruleValue = $parts[1] ?? null;
            
            switch ($ruleName) {
                case "required":
                    return empty($value) ? "$label is required" : null;
                    
                case "min_length":
                    return strlen($value) < $ruleValue ? "$label must be at least $ruleValue characters" : null;
                    
                case "max_length":
                    return strlen($value) > $ruleValue ? "$label must not exceed $ruleValue characters" : null;
                    
                case "email":
                    return !filter_var($value, FILTER_VALIDATE_EMAIL) ? "Invalid email format" : null;
                    
                case "phone":
                    return !preg_match("/^[\d\s\-\+\(\)]+$/", $value) ? "Invalid phone number format" : null;
                    
                case "date":
                    $date = DateTime::createFromFormat('Y-m-d', $value);
                    return !$date ? "Invalid date format" : null;
                    
                case "age_min":
                    $birthdate = DateTime::createFromFormat('Y-m-d', $value);
                    $age = $birthdate->diff(new DateTime())->y;
                    return $age < $ruleValue ? "You must be at least $ruleValue years old" : null;
                    
                case "age_max":
                    $birthdate = DateTime::createFromFormat('Y-m-d', $value);
                    $age = $birthdate->diff(new DateTime())->y;
                    return $age > $ruleValue ? "Invalid age" : null;
                    
                case "letters_only":
                    return !preg_match("/^[a-zA-Z\s'-]+$/", $value) ? "$label can only contain letters" : null;
                    
                case "number":
                    return !is_numeric($value) ? "$label must be a number" : null;
                    
                case "min":
                    return is_numeric($value) && $value < $ruleValue ? "$label must be at least $ruleValue" : null;
                    
                case "max":
                    return is_numeric($value) && $value > $ruleValue ? "$label must not exceed $ruleValue" : null;
                    
                case "in":
                    $allowedValues = explode(",", $ruleValue);
                    return !in_array($value, $allowedValues) ? "Invalid selection" : null;
                    
                case "min_checkboxes":
                    if (is_array($value)) {
                        return count($value) < $ruleValue ? "Please select at least $ruleValue options" : null;
                    }
                    return "Please select at least $ruleValue options";
                    
                default:
                    return null;
            }
        }
        
        private function saveCurrentStepData(): void {
            $stepFields = $this->steps[$this->currentStep]["fields"];
            
            foreach ($stepFields as $fieldName => $fieldConfig) {
                $this->stepData[$this->currentStep][$fieldName] = $this->getFieldValue($fieldName);
            }
            
            $_SESSION["survey_step_data"] = $this->stepData;
        }
        
        private function nextStep(): void {
            if ($this->currentStep < count($this->steps)) {
                $this->currentStep++;
                $_SESSION["survey_current_step"] = $this->currentStep;
            }
        }
        
        private function previousStep(): void {
            if ($this->currentStep > 1) {
                $this->currentStep--;
                $_SESSION["survey_current_step"] = $this->currentStep;
            }
        }
        
        private function completeSurvey(): void {
            $this->completed = true;
            $_SESSION["survey_completed"] = true;
        }
        
        private function restart(): void {
            unset($_SESSION["survey_current_step"]);
            unset($_SESSION["survey_step_data"]);
            unset($_SESSION["survey_completed"]);
            $this->currentStep = 1;
            $this->stepData = [];
            $this->completed = false;
        }
        
        private function getAllData(): array {
            $allData = [];
            for ($i = 1; $i <= count($this->steps); $i++) {
                if (isset($this->stepData[$i])) {
                    $allData = array_merge($allData, $this->stepData[$i]);
                }
            }
            return $allData;
        }
        
        public function render(): string {
            $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Multi-Step Survey</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .progress { background: #e9ecef; border-radius: 8px; height: 8px; margin-bottom: 30px; overflow: hidden; }
        .progress-bar { background: #007bff; height: 100%; transition: width 0.3s; }
        .step-header { margin-bottom: 30px; }
        .step-header h2 { margin: 0 0 10px 0; color: #333; }
        .step-header p { margin: 0; color: #666; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; 
            box-sizing: border-box; 
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
            border-color: #007bff; outline: none; 
        }
        .radio-group, .checkbox-group { display: flex; flex-direction: column; gap: 8px; }
        .radio-group label, .checkbox-group label { 
            display: flex; align-items: center; font-weight: normal; cursor: pointer; 
        }
        .radio-group input, .checkbox-group input { margin-right: 8px; width: auto; }
        .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .navigation { display: flex; justify-content: space-between; margin-top: 30px; gap: 10px; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
        .summary { background: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
        .summary h3 { margin: 0 0 15px 0; }
        .summary-item { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { font-weight: bold; color: #333; }
        .summary-value { color: #666; }
        .step-indicator { text-align: center; margin-bottom: 20px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Multi-Step Survey</h1>";
            
            if ($this->completed) {
                $html .= $this->renderCompletionPage();
            } else {
                $html .= $this->renderCurrentStep();
            }
            
            $html .= "</div>
</body>
</html>";
            
            return $html;
        }
        
        private function renderCurrentStep(): string {
            $stepInfo = $this->steps[$this->currentStep];
            $progress = ($this->currentStep / count($this->steps)) * 100;
            
            $html = "<div class='progress'>
                <div class='progress-bar' style='width: {$progress}%'></div>
            </div>";
            
            $html .= "<div class='step-indicator'>
                Step {$this->currentStep} of " . count($this->steps) . "
            </div>";
            
            $html .= "<div class='step-header'>
                <h2>{$stepInfo['title']}</h2>
                <p>{$stepInfo['description']}</p>
            </div>";
            
            // Show errors
            if (!empty($this->errors)) {
                $html .= "<div class='error' style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>
                    <strong>Please fix the following errors:</strong><ul>";
                foreach ($this->errors as $error) {
                    $html .= "<li>$error</li>";
                }
                $html .= "</ul></div>";
            }
            
            $html .= "<form method='post'>";
            
            foreach ($stepInfo["fields"] as $fieldName => $fieldConfig) {
                $value = $this->stepData[$this->currentStep][$fieldName] ?? "";
                $html .= $this->renderField($fieldName, $fieldConfig, $value);
            }
            
            $html .= "<div class='navigation'>";
            
            if ($this->currentStep > 1) {
                $html .= "<button type='submit' name='action' value='previous' class='btn btn-secondary'>Previous</button>";
            } else {
                $html .= "<div></div>";
            }
            
            if ($this->currentStep < count($this->steps)) {
                $html .= "<button type='submit' name='action' value='next' class='btn btn-primary'>Next</button>";
            } else {
                $html .= "<button type='submit' name='action' value='finish' class='btn btn-primary'>Complete Survey</button>";
            }
            
            $html .= "</div></form>";
            
            return $html;
        }
        
        private function renderField(string $fieldName, array $config, $value): string {
            $html = "<div class='form-group'>";
            $required = $config["required"] ? " required" : "";
            $error = $this->errors[$fieldName] ?? "";
            
            switch ($config["type"]) {
                case "text":
                case "email":
                case "tel":
                case "number":
                case "date":
                    $html .= "<label for='$fieldName'>{$config['label']}</label>";
                    $html .= "<input type='{$config['type']}' id='$fieldName' name='$fieldName' value='" . htmlspecialchars($value) . "'$required>";
                    break;
                    
                case "textarea":
                    $html .= "<label for='$fieldName'>{$config['label']}</label>";
                    $html .= "<textarea id='$fieldName' name='$fieldName' rows='{$config['rows']}'$required>" . htmlspecialchars($value) . "</textarea>";
                    break;
                    
                case "select":
                    $html .= "<label for='$fieldName'>{$config['label']}</label>";
                    $html .= "<select id='$fieldName' name='$fieldName'$required>";
                    foreach ($config["options"] as $optionValue => $optionLabel) {
                        $selected = $optionValue == $value ? "selected" : "";
                        $html .= "<option value='$optionValue' $selected>$optionLabel</option>";
                    }
                    $html .= "</select>";
                    break;
                    
                case "radio":
                    $html .= "<label>{$config['label']}</label>";
                    $html .= "<div class='radio-group'>";
                    foreach ($config["options"] as $optionValue => $optionLabel) {
                        $checked = $optionValue == $value ? "checked" : "";
                        $html .= "<label>
                            <input type='radio' name='$fieldName' value='$optionValue' $checked$required>
                            $optionLabel
                        </label>";
                    }
                    $html .= "</div>";
                    break;
                    
                case "checkbox_group":
                    $html .= "<label>{$config['label']}</label>";
                    $html .= "<div class='checkbox-group'>";
                    foreach ($config["options"] as $optionValue => $optionLabel) {
                        $checked = is_array($value) && in_array($optionValue, $value) ? "checked" : "";
                        $html .= "<label>
                            <input type='checkbox' name='{$fieldName}[]' value='$optionValue' $checked>
                            $optionLabel
                        </label>";
                    }
                    $html .= "</div>";
                    break;
            }
            
            if ($error) {
                $html .= "<span class='error'>$error</span>";
            }
            
            $html .= "</div>";
            return $html;
        }
        
        private function renderCompletionPage(): string {
            $allData = $this->getAllData();
            
            $html = "<div class='success'>
                <h2>🎉 Survey Completed!</h2>
                <p>Thank you for completing our survey. Your responses have been recorded.</p>
            </div>";
            
            $html .= "<div class='summary'>
                <h3>Survey Summary</h3>";
            
            // Personal Information
            $html .= "<div class='summary-item'>
                <div class='summary-label'>Personal Information</div>
                <div class='summary-value'>
                    Name: " . htmlspecialchars($allData["first_name"] . " " . $allData["last_name"]) . "<br>
                    Email: " . htmlspecialchars($allData["email"]) . "<br>
                    Phone: " . htmlspecialchars($allData["phone"] ?? "Not provided") . "<br>
                    Birthdate: " . htmlspecialchars($allData["birthdate"]) . "
                </div>
            </div>";
            
            // Professional Information
            $html .= "<div class='summary-item'>
                <div class='summary-label'>Professional Information</div>
                <div class='summary-value'>
                    Employment Status: " . htmlspecialchars($allData["employment_status"]) . "<br>
                    Industry: " . htmlspecialchars($allData["industry"]) . "<br>
                    Years of Experience: " . htmlspecialchars($allData["years_experience"]) . "<br>
                    Job Title: " . htmlspecialchars($allData["job_title"] ?? "Not provided") . "<br>
                    Company: " . htmlspecialchars($allData["company"] ?? "Not provided") . "
                </div>
            </div>";
            
            // Education
            $html .= "<div class='summary-item'>
                <div class='summary-label'>Education</div>
                <div class='summary-value'>
                    Highest Education: " . htmlspecialchars($allData["highest_education"]) . "<br>
                    Field of Study: " . htmlspecialchars($allData["field_of_study"]) . "<br>
                    Institution: " . htmlspecialchars($allData["institution"]) . "<br>
                    Graduation Year: " . htmlspecialchars($allData["graduation_year"]) . "
                </div>
            </div>";
            
            // Interests
            $html .= "<div class='summary-item'>
                <div class='summary-label'>Interests & Preferences</div>
                <div class='summary-value'>
                    Hobbies: " . htmlspecialchars($allData["hobbies"]) . "<br>
                    Favorite Activities: " . implode(", ", $allData["favorite_activities"] ?? []) . "<br>
                    Newsletter Topics: " . implode(", ", $allData["newsletter_topics"] ?? []) . "
                </div>
            </div>";
            
            // Feedback
            $html .= "<div class='summary-item'>
                <div class='summary-label'>Feedback</div>
                <div class='summary-value'>
                    Satisfaction: " . htmlspecialchars($allData["satisfaction"]) . "<br>
                    Contact Permission: " . htmlspecialchars($allData["contact_permission"]) . "
                </div>
            </div>";
            
            $html .= "</div>";
            
            $html .= "<form method='post'>
                <button type='submit' name='action' value='restart' class='btn btn-primary'>Start New Survey</button>
            </form>";
            
            return $html;
        }
    }
    
    // Display the survey
    $survey = new SurveyForm();
    echo $survey->render();
?>
