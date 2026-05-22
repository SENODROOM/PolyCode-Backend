# Project 6: Quiz System 🎯

An interactive quiz system with multiple question types, scoring, and user progress tracking.

## 🎯 Learning Objectives

After completing this project, you will:
- Implement complex business logic
- Build scoring algorithms
- Create interactive user interfaces
- Handle session-based state management
- Implement timer functionality
- Build progress tracking systems
- Create data analytics and reporting

## 🛠️ Features

### Quiz Features
- ✅ Multiple question types (Multiple Choice, True/False, Fill in the Blank)
- ✅ Timer functionality
- ✅ Random question ordering
- ✅ Question categories and difficulty levels
- ✅ Score calculation and feedback
- ✅ Quiz completion certificates

### User Features
- ✅ User registration and profiles
- ✅ Quiz history and progress tracking
- ✅ Leaderboard system
- ✅ Achievement badges
- ✅ Performance analytics
- ✅ Study recommendations

### Admin Features
- ✅ Quiz creation and management
- ✅ Question bank management
- ✅ User progress monitoring
- ✅ Analytics and reporting
- ✅ Quiz scheduling
- ✅ Content moderation

## 📁 Project Structure

```
quiz-system/
├── README.md           # This file
├── index.php          # Main application
├── config/
│   ├── database.php   # Database configuration
│   └── config.php     # Application settings
├── classes/
│   ├── Quiz.php       # Quiz class
│   ├── Question.php   # Question class
│   ├── User.php       # User class
│   ├── Score.php      # Score calculation class
│   └── Timer.php      # Timer class
├── admin/
│   ├── index.php      # Admin dashboard
│   ├── quizzes.php    # Quiz management
│   ├── questions.php  # Question management
│   └── analytics.php  # Analytics dashboard
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   ├── js/
│   │   ├── quiz.js    # Quiz JavaScript
│   │   └── timer.js   # Timer functionality
│   └── images/       # Quiz images
└── database/
    └── setup.sql      # Database schema
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache, Nginx)
- JavaScript enabled browser

### Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE quiz_system;
   ```

2. **Import Schema**
   Run the SQL commands from `database/setup.sql`

### Configuration

1. **Database Configuration**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'quiz_system');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

### Running the Application

1. **Navigate to project directory**
   ```bash
   cd php-learning-guide/08-projects/quiz-system
   ```

2. **Start PHP server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application**
   - Main site: `http://localhost:8000`
   - Admin panel: `http://localhost:8000/admin`

## 📖 Database Schema

### Quizzes Table
```sql
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    time_limit INT DEFAULT 0, -- 0 = no time limit
    passing_score INT DEFAULT 70,
    random_order BOOLEAN DEFAULT FALSE,
    show_results BOOLEAN DEFAULT TRUE,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Questions Table
```sql
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'fill_blank') NOT NULL,
    points INT DEFAULT 1,
    order_index INT DEFAULT 0,
    explanation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);
```

### Question Options Table
```sql
CREATE TABLE question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
```

### Quiz Attempts Table
```sql
CREATE TABLE quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    max_score INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    time_taken INT NOT NULL, -- in seconds
    status ENUM('in_progress', 'completed', 'abandoned') DEFAULT 'in_progress',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);
```

### User Answers Table
```sql
CREATE TABLE user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    answer TEXT,
    is_correct BOOLEAN,
    points_earned INT DEFAULT 0,
    time_taken INT DEFAULT 0, -- time taken for this question
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
```

## 🔧 Core Classes

### Quiz Class
```php
<?php
class Quiz {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $quizData = [
            'title' => htmlspecialchars(trim($data['title'])),
            'description' => htmlspecialchars(trim($data['description'])),
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'difficulty' => $data['difficulty'] ?? 'medium',
            'time_limit' => !empty($data['time_limit']) ? (int)$data['time_limit'] : 0,
            'passing_score' => !empty($data['passing_score']) ? (int)$data['passing_score'] : 70,
            'random_order' => !empty($data['random_order']),
            'show_results' => !empty($data['show_results']),
            'created_by' => $data['created_by'] ?? null
        ];
        
        return $this->db->insert('quizzes', $quizData);
    }
    
    public function getAll($status = 'published', $categoryId = null, $limit = 20) {
        $sql = "SELECT q.*, c.name as category_name, u.username as creator_name
                FROM quizzes q
                LEFT JOIN categories c ON q.category_id = c.id
                LEFT JOIN users u ON q.created_by = u.id
                WHERE q.status = ?";
        
        $params = [$status];
        
        if ($categoryId) {
            $sql .= " AND q.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY q.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT q.*, c.name as category_name, u.username as creator_name
                FROM quizzes q
                LEFT JOIN categories c ON q.category_id = c.id
                LEFT JOIN users u ON q.created_by = u.id
                WHERE q.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getQuestions($quizId, $userId = null) {
        $sql = "SELECT q.*, 
                       (SELECT GROUP_CONCAT(
                           JSON_OBJECT('id', o.id, 'text', o.option_text, 'is_correct', o.is_correct, 'order', o.order_index)
                           ORDER BY o.order_index
                       ) FROM question_options o WHERE o.question_id = q.id) as options
                FROM questions q
                WHERE q.quiz_id = ?
                ORDER BY " . ($this->getById($quizId)['random_order'] ? 'RAND()' : 'q.order_index');
        
        $stmt = $this->db->query($sql, [$quizId]);
        $questions = $stmt->fetchAll();
        
        // Process options
        foreach ($questions as &$question) {
            if ($question['options']) {
                $question['options'] = array_map(function($option) {
                    return json_decode($option, true);
                }, explode(',', $question['options']));
            } else {
                $question['options'] = [];
            }
        }
        
        return $questions;
    }
    
    public function startAttempt($userId, $quizId) {
        // Check if user has an active attempt
        $sql = "SELECT id FROM quiz_attempts 
                WHERE user_id = ? AND quiz_id = ? AND status = 'in_progress'";
        $stmt = $this->db->query($sql, [$userId, $quizId]);
        $activeAttempt = $stmt->fetch();
        
        if ($activeAttempt) {
            return $activeAttempt['id'];
        }
        
        // Create new attempt
        $attemptData = [
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => 0,
            'max_score' => $this->getMaxScore($quizId),
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
        $maxScore = $scoreData['max_score'] ?? 1;
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
        $sql = "SELECT qa.*, q.title as quiz_title, q.description as quiz_description,
                       u.username, u.first_name, u.last_name
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                JOIN users u ON qa.user_id = u.id
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
            
            // Process options for each answer
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
    
    public function getLeaderboard($quizId = null, $limit = 10) {
        $sql = "SELECT qa.*, q.title as quiz_title, u.username, u.first_name, u.last_name
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                JOIN users u ON qa.user_id = u.id
                WHERE qa.status = 'completed'";
        
        $params = [];
        
        if ($quizId) {
            $sql .= " AND qa.quiz_id = ?";
            $params[] = $quizId;
        }
        
        $sql .= " ORDER BY qa.percentage DESC, qa.time_taken ASC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
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
                // Case-insensitive comparison for fill in the blank
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
?>
```

### Question Class
```php
<?php
class Question {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $questionData = [
            'quiz_id' => $data['quiz_id'],
            'question_text' => htmlspecialchars(trim($data['question_text'])),
            'question_type' => $data['question_type'],
            'points' => $data['points'] ?? 1,
            'order_index' => $data['order_index'] ?? 0,
            'explanation' => !empty($data['explanation']) ? htmlspecialchars(trim($data['explanation'])) : null
        ];
        
        $questionId = $this->db->insert('questions', $questionData);
        
        // Add options if provided
        if (!empty($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $index => $option) {
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => htmlspecialchars(trim($option['text'])),
                    'is_correct' => !empty($option['is_correct']),
                    'order_index' => $index
                ];
                
                $this->db->insert('question_options', $optionData);
            }
        }
        
        return $questionId;
    }
    
    public function update($questionId, $data) {
        $allowedFields = ['question_text', 'points', 'order_index', 'explanation'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = htmlspecialchars(trim($data[$field]));
            }
        }
        
        if (!empty($updateData)) {
            $this->db->update('questions', $updateData, 'id = ?', [$questionId]);
        }
        
        // Update options if provided
        if (!empty($data['options']) && is_array($data['options'])) {
            // Delete existing options
            $this->db->delete('question_options', 'question_id = ?', [$questionId]);
            
            // Add new options
            foreach ($data['options'] as $index => $option) {
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => htmlspecialchars(trim($option['text'])),
                    'is_correct' => !empty($option['is_correct']),
                    'order_index' => $index
                ];
                
                $this->db->insert('question_options', $optionData);
            }
        }
        
        return true;
    }
    
    public function delete($questionId) {
        // Delete question (options will be deleted due to foreign key constraint)
        return $this->db->delete('questions', 'id = ?', [$questionId]);
    }
    
    public function getByQuizId($quizId) {
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
}
?>
```

### Score Class
```php
<?php
class Score {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function calculateScore($attemptId) {
        $sql = "SELECT SUM(points_earned) as earned, 
                       (SELECT SUM(points) FROM questions WHERE quiz_id = qa.quiz_id) as total
                FROM user_answers ua
                JOIN quiz_attempts qa ON ua.attempt_id = qa.id
                WHERE ua.attempt_id = ?";
        $stmt = $this->db->query($sql, [$attemptId]);
        $result = $stmt->fetch();
        
        $earned = $result['earned'] ?? 0;
        $total = $result['total'] ?? 1;
        $percentage = $total > 0 ? ($earned / $total) * 100 : 0;
        
        return [
            'earned' => $earned,
            'total' => $total,
            'percentage' => round($percentage, 2)
        ];
    }
    
    public function getUserStats($userId) {
        $sql = "SELECT COUNT(*) as total_attempts,
                       AVG(percentage) as avg_score,
                       MAX(percentage) as best_score,
                       AVG(time_taken) as avg_time
                FROM quiz_attempts
                WHERE user_id = ? AND status = 'completed'";
        $stmt = $this->db->query($sql, [$userId]);
        $stats = $stmt->fetch();
        
        // Get recent activity
        $sql = "SELECT qa.*, q.title as quiz_title
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                WHERE qa.user_id = ? AND qa.status = 'completed'
                ORDER BY qa.completed_at DESC
                LIMIT 5";
        $stmt = $this->db->query($sql, [$userId]);
        $stats['recent_activity'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    public function getQuizStats($quizId) {
        $sql = "SELECT COUNT(*) as total_attempts,
                       AVG(percentage) as avg_score,
                       MAX(percentage) as best_score,
                       AVG(time_taken) as avg_time,
                       COUNT(CASE WHEN percentage >= (SELECT passing_score FROM quizzes WHERE id = ?) THEN 1 END) as passed
                FROM quiz_attempts
                WHERE quiz_id = ? AND status = 'completed'";
        $stmt = $this->db->query($sql, [$quizId, $quizId]);
        $stats = $stmt->fetch();
        
        $stats['pass_rate'] = $stats['total_attempts'] > 0 ? 
            ($stats['passed'] / $stats['total_attempts']) * 100 : 0;
        
        return $stats;
    }
    
    public function getLeaderboard($quizId = null, $limit = 10) {
        $sql = "SELECT qa.*, q.title as quiz_title, u.username, u.first_name, u.last_name,
                       RANK() OVER (ORDER BY qa.percentage DESC, qa.time_taken ASC) as rank
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                JOIN users u ON qa.user_id = u.id
                WHERE qa.status = 'completed'";
        
        $params = [];
        
        if ($quizId) {
            $sql .= " AND qa.quiz_id = ?";
            $params[] = $quizId;
        }
        
        $sql .= " ORDER BY qa.percentage DESC, qa.time_taken ASC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
}
?>
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Question Bank**: Create reusable question bank
2. **Quiz Templates**: Pre-built quiz templates
3. **Study Mode**: Practice mode without scoring
4. **Print Results**: Printable quiz results

### Intermediate Challenges
1. **Adaptive Testing**: Difficulty based on performance
2. **Question Pool**: Random questions from larger pool
3. **Time Tracking**: Per-question time tracking
4. **Progress Reports**: Detailed progress reports

### Advanced Challenges
1. **AI Questions**: AI-generated questions
2. **Proctoring**: Quiz proctoring system
3. **Multiplayer**: Real-time quiz competitions
4. **Analytics Dashboard**: Advanced analytics

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] Quiz creation and management
- [ ] Question creation with different types
- [ ] Quiz taking functionality
- [ ] Timer functionality
- [ ] Score calculation
- [ ] Leaderboard system
- [ ] User progress tracking
- [ ] Admin panel functionality

### Quiz Testing
- [ ] Multiple choice questions
- [ ] True/false questions
- [ ] Fill in the blank questions
- [ ] Random question order
- [ ] Time limits
- [ ] Score calculations
- [ ] Certificate generation

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ Complex business logic
- ✅ Session management
- ✅ Timer functionality
- ✅ Score algorithms
- ✅ Data analytics
- ✅ User progress tracking
- ✅ Interactive interfaces
- ✅ Database relationships
- ✅ Error handling

## 🚀 Next Steps

1. **Add More Question Types**: Essay, matching, ordering
2. **Mobile App**: Native mobile quiz application
3. **Gamification**: Points, badges, achievements
4. **Integration**: LMS integration
5. **AI Features**: AI-powered quiz recommendations

---

**🎉 Congratulations! You've completed all PHP Learning Guide projects!**

You now have a comprehensive understanding of PHP development from basics to advanced applications. Each project has taught you valuable skills that you can apply to real-world development.
