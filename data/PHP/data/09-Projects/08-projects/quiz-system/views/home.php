<?php
// Home page view
$quizzes = $quiz->getAll();
$categories = $category->getAll();
?>

<div class="card">
    <h1>Welcome to <?= APP_NAME ?></h1>
    <p>Challenge yourself with our interactive quizzes on various topics!</p>
</div>

<div class="card">
    <h2>Available Quizzes</h2>
    
    <?php if (empty($quizzes)): ?>
        <p>No quizzes available at the moment.</p>
    <?php else: ?>
        <div class="quiz-grid">
            <?php foreach ($quizzes as $quiz): ?>
                <div class="quiz-card">
                    <h3 class="quiz-title"><?= htmlspecialchars($quiz['title']) ?></h3>
                    <p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
                    
                    <div class="quiz-meta">
                        <span class="difficulty <?= $quiz['difficulty'] ?>">
                            <?= ucfirst($quiz['difficulty']) ?>
                        </span>
                        
                        <?php if ($quiz['time_limit'] > 0): ?>
                            <div class="timer">
                                ⏱️ <?= $quiz['time_limit'] ?>s
                            </div>
                        <?php endif; ?>
                        
                        <span>📝 <?= $this->getQuestionCount($quiz['id']) ?> questions</span>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <small>
                            Passing score: <?= $quiz['passing_score ?>% | 
                            Category: <?= htmlspecialchars($quiz['category_name'] ?? 'General') ?>
                        </small>
                    </div>
                    
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="form_type" value="start_quiz">
                        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                        <button type="submit" class="btn btn-success">Start Quiz</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Quiz Categories</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
        <?php foreach ($categories as $category): ?>
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h4><?= htmlspecialchars($category['name']) ?></h4>
                <p style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                    <?= htmlspecialchars($category['description'] ?? '') ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <h2>How It Works</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">1️⃣</div>
            <h3>Choose a Quiz</h3>
            <p>Select from our collection of quizzes across different categories and difficulty levels.</p>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">2️⃣</div>
            <h3>Answer Questions</h3>
            <p>Answer multiple choice, true/false, or fill-in-the-blank questions.</p>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">3️⃣</div>
            <h3>Get Results</h3>
            <p>Receive instant feedback, explanations, and your final score.</p>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">4️⃣</div>
            <h3>Track Progress</h3>
            <p>Monitor your performance and improve over time.</p>
        </div>
    </div>
</div>

<div class="card">
    <h2>Features</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <div>
            <h4>📚 Multiple Question Types</h4>
            <p>Multiple choice, true/false, and fill-in-the-blank questions.</p>
        </div>
        
        <div>
            <h4>⏱️ Timed Quizzes</h4>
            <p>Challenge yourself with time-limited quizzes.</p>
        </div>
        
        <div>
            <h4>📊 Instant Feedback</h4>
            <p>Get immediate feedback with detailed explanations.</p>
        </div>
        
        <div>
            <h4>🏆 Scoring System</h4>
            <p>Comprehensive scoring with passing requirements.</p>
        </div>
        
        <div>
            <h4>🎯 Difficulty Levels</h4>
            <p>Easy, medium, and hard difficulty levels.</p>
        </div>
        
        <div>
            <h4>📱 Responsive Design</h4>
            <p>Works perfectly on desktop and mobile devices.</p>
        </div>
    </div>
</div>

<div class="card">
    <h2>Quiz Tips</h2>
    <ul style="line-height: 1.8;">
        <li><strong>Read carefully:</strong> Take your time to read each question and all options.</li>
        <li><strong>Manage time:</strong> Keep an eye on the timer for timed quizzes.</li>
        <li><strong>Think logically:</strong> Use process of elimination for multiple choice questions.</li>
        <li><strong>Learn from mistakes:</strong> Review explanations for questions you get wrong.</li>
        <li><strong>Practice regularly:</strong> Take quizzes regularly to improve your knowledge.</li>
    </ul>
</div>

<?php
// Helper function to get question count
function getQuestionCount($quizId) {
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) as count FROM questions WHERE quiz_id = ?";
    $stmt = $db->query($sql, [$quizId]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}
?>
