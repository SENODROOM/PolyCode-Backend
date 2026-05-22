<?php
// Quiz System - Main Application

// Configuration
define('APP_NAME', 'Quiz System');
define('APP_VERSION', '1.0.0');
define('DB_HOST', 'localhost');
define('DB_NAME', 'quiz_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start session
session_start();

// Database class
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function setupDatabase() {
        try {
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                role ENUM('user', 'admin') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create categories table
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            
            // Create quizzes table
            $sql = "CREATE TABLE IF NOT EXISTS quizzes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category_id INT,
                difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
                time_limit INT DEFAULT 0,
                passing_score INT DEFAULT 70,
                random_order BOOLEAN DEFAULT FALSE,
                status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )";
            $this->pdo->exec($sql);
            
            // Create questions table
            $sql = "CREATE TABLE IF NOT EXISTS questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                quiz_id INT NOT NULL,
                question_text TEXT NOT NULL,
                question_type ENUM('multiple_choice', 'true_false', 'fill_blank') NOT NULL,
                points INT DEFAULT 1,
                order_index INT DEFAULT 0,
                explanation TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
            )";
            $this->pdo->exec($sql);
            
            // Create question_options table
            $sql = "CREATE TABLE IF NOT EXISTS question_options (
                id INT AUTO_INCREMENT PRIMARY KEY,
                question_id INT NOT NULL,
                option_text VARCHAR(255) NOT NULL,
                is_correct BOOLEAN DEFAULT FALSE,
                order_index INT DEFAULT 0,
                FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
            )";
            $this->pdo->exec($sql);
            
            // Create quiz_attempts table
            $sql = "CREATE TABLE IF NOT EXISTS quiz_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                quiz_id INT NOT NULL,
                score INT NOT NULL,
                max_score INT NOT NULL,
                percentage DECIMAL(5,2) NOT NULL,
                time_taken INT NOT NULL,
                status ENUM('in_progress', 'completed', 'abandoned') DEFAULT 'in_progress',
                started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                completed_at TIMESTAMP NULL,
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
            )";
            $this->pdo->exec($sql);
            
            // Insert default categories
            $this->pdo->exec("INSERT IGNORE INTO categories (name, description) VALUES 
                ('General Knowledge', 'General knowledge quizzes'),
                ('Science', 'Science and technology quizzes'),
                ('History', 'Historical quizzes'),
                ('Geography', 'Geographical quizzes'),
                ('Entertainment', 'Movies, music, and entertainment quizzes')");
            
            // Insert sample quiz
            $quizId = $this->pdo->lastInsertId();
            $this->pdo->exec("INSERT IGNORE INTO quizzes (title, description, category_id, difficulty, time_limit, passing_score, status) VALUES 
                ('PHP Programming Quiz', 'Test your knowledge of PHP programming', 2, 'medium', 300, 70, 'published')");
            
            // Get quiz ID for sample questions
            $stmt = $this->pdo->query("SELECT id FROM quizzes WHERE title = 'PHP Programming Quiz'");
            $quiz = $stmt->fetch();
            
            if ($quiz) {
                // Insert sample questions
                $questions = [
                    [
                        'quiz_id' => $quiz['id'],
                        'question_text' => 'What does PHP stand for?',
                        'question_type' => 'multiple_choice',
                        'points' => 1,
                        'order_index' => 1,
                        'explanation' => 'PHP originally stood for Personal Home Page, but now stands for PHP: Hypertext Preprocessor',
                        'options' => [
                            ['option_text' => 'Personal Home Page', 'is_correct' => true, 'order_index' => 1],
                            ['option_text' => 'Professional Hypertext Processor', 'is_correct' => false, 'order_index' => 2],
                            ['option_text' => 'Programming Hypertext Preprocessor', 'is_correct' => false, 'order_index' => 3],
                            ['option_text' => 'Private Home Page', 'is_correct' => false, 'order_index' => 4]
                        ]
                    ],
                    [
                        'quiz_id' => $quiz['id'],
                        'question_text' => 'PHP is a server-side scripting language.',
                        'question_type' => 'true_false',
                        'points' => 1,
                        'order_index' => 2,
                        'explanation' => 'PHP is indeed a server-side scripting language designed for web development.',
                        'options' => [
                            ['option_text' => 'True', 'is_correct' => true, 'order_index' => 1]
                        ]
                    ],
                    [
                        'quiz_id' => $quiz['id'],
                        'question_text' => 'What symbol is used to start a PHP code block?',
                        'question_type' => 'fill_blank',
                        'points' => 1,
                        'order_index' => 3,
                        'explanation' => 'PHP code blocks start with <?php and end with ?>',
                        'options' => [
                            ['option_text' => '<?php', 'is_correct' => true, 'order_index' => 1]
                        ]
                    ]
                ];
                
                foreach ($questions as $question) {
                    $questionData = [
                        'quiz_id' => $question['quiz_id'],
                        'question_text' => $question['question_text'],
                        'question_type' => $question['question_type'],
                        'points' => $question['points'],
                        'order_index' => $question['order_index'],
                        'explanation' => $question['explanation']
                    ];
                    
                    $questionId = $this->pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, points, order_index, explanation) VALUES (?, ?, ?, ?, ?, ?)");
                    $questionId->execute([
                        $questionData['quiz_id'],
                        $questionData['question_text'],
                        $questionData['question_type'],
                        $questionData['points'],
                        $questionData['order_index'],
                        $questionData['explanation']
                    ]);
                    
                    $lastQuestionId = $this->pdo->lastInsertId();
                    
                    // Insert options
                    foreach ($question['options'] as $option) {
                        $optionData = [
                            'question_id' => $lastQuestionId,
                            'option_text' => $option['option_text'],
                            'is_correct' => $option['is_correct'],
                            'order_index' => $option['order_index']
                        ];
                        
                        $this->pdo->prepare("INSERT INTO question_options (question_id, option_text, is_correct, order_index) VALUES (?, ?, ?, ?)")->execute([
                            $optionData['question_id'],
                            $optionData['option_text'],
                            $optionData['is_correct'],
                            $optionData['order_index']
                        ]);
                    }
                }
            }
            
            // Create admin user
            $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
            $this->pdo->exec("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, role) VALUES 
                ('admin', 'admin@example.com', '$adminHash', 'Admin', 'User', 'admin')");
            
            echo "Database setup completed!<br>";
        } catch (PDOException $e) {
            echo "Database setup error: " . $e->getMessage() . "<br>";
        }
    }
}

// Quiz class
class Quiz {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($status = 'published') {
        $sql = "SELECT q.*, c.name as category_name
                FROM quizzes q
                LEFT JOIN categories c ON q.category_id = c.id
                WHERE q.status = ?
                ORDER BY q.created_at DESC";
        $stmt = $this->db->query($sql, [$status]);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT q.*, c.name as category_name
                FROM quizzes q
                LEFT JOIN categories c ON q.category_id = c.id
                WHERE q.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getQuestions($quizId) {
        $sql = "SELECT q.*, 
                       GROUP_CONCAT(
                           JSON_OBJECT('id', o.id, 'text', o.option_text, 'is_correct', o.is_correct, 'order', o.order_index)
                           ORDER BY o.order_index
                       ) as options
                FROM questions q
                LEFT JOIN question_options o ON q.id = o.question_id
                WHERE q.quiz_id = ?
                GROUP BY q.id
                ORDER BY q.order_index";
        
        $stmt = $this->db->query($sql, [$quizId]);
        $questions = $stmt->fetchAll();
        
        // Process options
        foreach ($questions as &$question) {
            if ($question['options']) {
                $options = explode(',', $question['options']);
                $question['options'] = array_map(function($option) {
                    return json_decode($option, true);
                }, $options);
            } else {
                $question['options'] = [];
            }
        }
        
        return $questions;
    }
    
    public function startAttempt($quizId, $userId = null) {
        $quiz = $this->getById($quizId);
        $maxScore = $this->getMaxScore($quizId);
        
        $attemptData = [
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => 0,
            'max_score' => $maxScore,
            'percentage' => 0,
            'time_taken' => 0,
            'status' => 'in_progress'
        ];
        
        return $this->db->insert('quiz_attempts', $attemptData);
    }
    
    public function submitAnswer($attemptId, $questionId, $answer) {
        $question = $this->getQuestionById($questionId);
        $isCorrect = $this->validateAnswer($question, $answer);
        $pointsEarned = $isCorrect ? $question['points'] : 0;
        
        // Check if answer already exists
        $sql = "SELECT id FROM user_answers WHERE attempt_id = ? AND question_id = ?";
        $stmt = $this->db->query($sql, [$attemptId, $questionId]);
        $existingAnswer = $stmt->fetch();
        
        if ($existingAnswer) {
            // Update existing answer
            $this->db->update('user_answers', [
                'answer' => $answer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned
            ], 'id = ?', [$existingAnswer['id']]);
        } else {
            // Insert new answer
            $answerData = [
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'answer' => $answer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned
            ];
            
            $this->db->insert('user_answers', $answerData);
        }
        
        return [
            'correct' => $isCorrect,
            'points' => $pointsEarned,
            'explanation' => $question['explanation']
        ];
    }
    
    public function completeAttempt($attemptId) {
        // Calculate final score
        $sql = "SELECT SUM(points_earned) as score, 
                       (SELECT SUM(points) FROM questions WHERE quiz_id = qa.quiz_id) as max_score
                FROM user_answers ua
                JOIN quiz_attempts qa ON ua.attempt_id = qa.id
                WHERE ua.attempt_id = ?";
        $stmt = $this->db->query($sql, [$attemptId]);
        $scoreData = $stmt->fetch();
        
        $score = $scoreData['score'] ?? 0;
        $maxScore = $scoreData['maxScore'] ?? 1;
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
        
        // Update attempt
        $this->db->update('quiz_attempts', [
            'score' => $score,
            'percentage' => $percentage,
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$attemptId]);
        
        return [
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round($percentage, 2),
            'passed' => $percentage >= $this->getPassingScore($attemptId)
        ];
    }
    
    public function getAttemptResults($attemptId) {
        $sql = "SELECT qa.*, q.title as quiz_title, q.description as quiz_description
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                WHERE qa.id = ?";
        $stmt = $this->db->query($sql, [$attemptId]);
        $attempt = $stmt->fetch();
        
        if ($attempt) {
            // Get user answers
            $sql = "SELECT ua.*, q.question_text, q.question_type, q.explanation,
                           GROUP_CONCAT(
                               JSON_OBJECT('id', o.id, 'text', o.option_text, 'is_correct', o.is_correct)
                               ORDER BY o.order_index
                           ) as options
                    FROM user_answers ua
                    JOIN questions q ON ua.question_id = q.id
                    LEFT JOIN question_options o ON q.id = o.question_id
                    WHERE ua.attempt_id = ?
                    GROUP BY ua.id
                    ORDER BY q.order_index";
            $stmt = $this->db->query($sql, [$attemptId]);
            $attempt['answers'] = $stmt->fetchAll();
            
            // Process options
            foreach ($attempt['answers'] as &$answer) {
                if ($answer['options']) {
                    $options = explode(',', $answer['options']);
                    $answer['options'] = array_map(function($option) {
                        return json_decode($option, true);
                    }, $options);
                } else {
                    $answer['options'] = [];
                }
            }
        }
        
        return $attempt;
    }
    
    private function getQuestionById($questionId) {
        $sql = "SELECT q.*, 
                       GROUP_CONCAT(
                           JSON_OBJECT('id', o.id, 'text', o.option_text, 'is_correct', o.is_correct)
                           ORDER BY o.order_index
                       ) as options
                FROM questions q
                LEFT JOIN question_options o ON q.id = o.question_id
                WHERE q.id = ?
                GROUP BY q.id";
        $stmt = $this->db->query($sql, [$questionId]);
        $question = $stmt->fetch();
        
        if ($question && $question['options']) {
            $options = explode(',', $question['options']);
            $question['options'] = array_map(function($option) {
                return json_decode($option, true);
            }, $options);
        } else {
            $question['options'] = [];
        }
        
        return $question;
    }
    
    private function validateAnswer($question, $answer) {
        switch ($question['question_type']) {
            case 'multiple_choice':
                $correctOptions = array_filter($question['options'], function($option) {
                    return $option['is_correct'];
                });
                $correctIds = array_column($correctOptions, 'id');
                $answerIds = is_array($answer) ? $answer : [$answer];
                return count(array_intersect($correctIds, $answerIds)) === count($correctIds) &&
                       count($answerIds) === count($correctIds);
                
            case 'true_false':
                return (bool)$answer === (bool)$question['options'][0]['is_correct'];
                
            case 'fill_blank':
                return strtolower(trim($answer)) === strtolower(trim($question['options'][0]['option_text']));
                
            default:
                return false;
        }
    }
    
    private function getMaxScore($quizId) {
        $sql = "SELECT SUM(points) as total FROM questions WHERE quiz_id = ?";
        $stmt = $this->db->query($sql, [$quizId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 1;
    }
    
    private function getPassingScore($attemptId) {
        $sql = "SELECT q.passing_score 
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                WHERE qa.id = ?";
        $stmt = $this->db->query($sql, [$attemptId]);
        $result = $stmt->fetch();
        return $result['passing_score'] ?? 70;
    }
}

// Category class
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username']];
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Initialize database
$db = Database::getInstance();
$db->setupDatabase();

// Initialize classes
$quiz = new Quiz();
$category = new Category();

// Handle requests
$action = $_GET['action'] ?? 'home';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['form_type'] ?? '') {
        case 'start_quiz':
            $quizId = $_POST['quiz_id'];
            $userId = getCurrentUser()['id'] ?? null;
            $attemptId = $quiz->startAttempt($quizId, $userId);
            header("Location: ?action=take_quiz&attempt_id=$attemptId");
            exit;
            break;
            
        case 'submit_answer':
            $attemptId = $_POST['attempt_id'];
            $questionId = $_POST['question_id'];
            $answer = $_POST['answer'] ?? '';
            
            $result = $quiz->submitAnswer($attemptId, $questionId, $answer);
            
            // Store result in session for display
            $_SESSION['last_answer_result'] = $result;
            
            header("Location: ?action=take_quiz&attempt_id=$attemptId&question_id=" . ($_POST['next_question_id'] ?? ''));
            exit;
            break;
            
        case 'complete_quiz':
            $attemptId = $_POST['attempt_id'];
            $results = $quiz->completeAttempt($attemptId);
            header("Location: ?action=results&attempt_id=$attemptId");
            exit;
            break;
    }
}

// Handle logout
if ($action === 'logout') {
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .quiz-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            border: 2px solid #e9ecef;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
        }

        .quiz-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .quiz-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .quiz-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .difficulty {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .difficulty.easy {
            background: #d4edda;
            color: #155724;
        }

        .difficulty.medium {
            background: #fff3cd;
            color: #856404;
        }

        .difficulty.hard {
            background: #f8d7da;
            color: #721c24;
        }

        .timer {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: bold;
            color: #dc3545;
        }

        .question-container {
            margin-bottom: 30px;
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .options-container {
            display: grid;
            gap: 15px;
        }

        .option {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option:hover {
            background: #e9ecef;
            border-color: #667eea;
        }

        .option input[type="radio"],
        .option input[type="checkbox"] {
            margin-right: 10px;
        }

        .option.selected {
            background: #e7f3ff;
            border-color: #667eea;
        }

        .option.correct {
            background: #d4edda;
            border-color: #28a745;
        }

        .option.incorrect {
            background: #f8d7da;
            border-color: #dc3545;
        }

        .explanation {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-top: 15px;
            border-radius: 0 8px 8px 0;
        }

        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(90deg, #667eea, #764ba2);
            height: 100%;
            transition: width 0.3s ease;
        }

        .score-display {
            text-align: center;
            padding: 30px;
        }

        .score-number {
            font-size: 4rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .score-label {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }

        .passed {
            color: #28a745;
        }

        .failed {
            color: #dc3545;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .quiz-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .quiz-grid {
                grid-template-columns: 1fr;
            }
            
            .quiz-navigation {
                flex-direction: column;
                gap: 15px;
            }
            
            .quiz-meta {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">🎯 <?= APP_NAME ?></div>
            <div class="subtitle">Test your knowledge with interactive quizzes</div>
        </header>

        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <?php
        switch ($action) {
            case 'home':
                include 'views/home.php';
                break;
            case 'take_quiz':
                include 'views/take_quiz.php';
                break;
            case 'results':
                include 'views/results.php';
                break;
            default:
                include 'views/home.php';
        }
        ?>
    </div>
</body>
</html>
