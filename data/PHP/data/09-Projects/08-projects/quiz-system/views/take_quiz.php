<?php
// Take quiz view
$attemptId = $_GET['attempt_id'] ?? 0;
$questionId = $_GET['question_id'] ?? null;

// Get attempt details
$sql = "SELECT qa.*, q.title as quiz_title, q.description as quiz_description, q.time_limit, q.random_order
        FROM quiz_attempts qa
        JOIN quizzes q ON qa.quiz_id = q.id
        WHERE qa.id = ?";
$stmt = Database::getInstance()->query($sql, [$attemptId]);
$attempt = $stmt->fetch();

if (!$attempt) {
    echo '<div class="card"><p>Quiz attempt not found.</p></div>';
    return;
}

// Get questions
$questions = $quiz->getQuestions($attempt['quiz_id']);
$totalQuestions = count($questions);

// Find current question index
$currentQuestionIndex = 0;
if ($questionId) {
    foreach ($questions as $index => $question) {
        if ($question['id'] == $questionId) {
            $currentQuestionIndex = $index;
            break;
        }
    }
}

// Get current question
$currentQuestion = $questions[$currentQuestionIndex] ?? null;

// Calculate progress
$progress = $totalQuestions > 0 ? (($currentQuestionIndex + 1) / $totalQuestions) * 100 : 0;

// Check if quiz is complete
if ($currentQuestionIndex >= $totalQuestions && $totalQuestions > 0) {
    // Complete the quiz
    $results = $quiz->completeAttempt($attemptId);
    header("Location: ?action=results&attempt_id=$attemptId");
    exit;
}
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1><?= htmlspecialchars($attempt['quiz_title']) ?></h1>
            <p style="color: #666;">Question <?= $currentQuestionIndex + 1 ?> of <?= $totalQuestions ?></p>
        </div>
        
        <?php if ($attempt['time_limit'] > 0): ?>
            <div class="timer" id="quizTimer">
                ⏱️ <span id="timeRemaining"><?= $attempt['time_limit'] ?></span>s
            </div>
        <?php endif; ?>
    </div>
    
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?= $progress ?>%"></div>
    </div>
    
    <?php if (isset($_SESSION['last_answer_result'])): ?>
        <?php $result = $_SESSION['last_answer_result']; ?>
        <div class="message <?= $result['correct'] ? 'success' : 'error' ?>">
            <?= $result['correct'] ? '✅ Correct!' : '❌ Incorrect' ?>
            <?php if ($result['explanation']): ?>
                <div style="margin-top: 10px;">
                    <strong>Explanation:</strong> <?= htmlspecialchars($result['explanation']) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php unset($_SESSION['last_answer_result']); ?>
    <?php endif; ?>
</div>

<?php if ($currentQuestion): ?>
    <div class="card">
        <form method="post" id="quizForm">
            <input type="hidden" name="form_type" value="submit_answer">
            <input type="hidden" name="attempt_id" value="<?= $attemptId ?>">
            <input type="hidden" name="question_id" value="<?= $currentQuestion['id'] ?>">
            <input type="hidden" name="next_question_id" value="<?= $questions[$currentQuestionIndex + 1]['id'] ?? '' ?>">
            
            <div class="question-container">
                <div class="question-text">
                    <?= htmlspecialchars($currentQuestion['question_text']) ?>
                    <span style="color: #667eea; font-weight: normal;"> (<?= $currentQuestion['points'] ?> point<?= $currentQuestion['points'] > 1 ? 's' : '' ?>)</span>
                </div>
                
                <div class="options-container">
                    <?php if ($currentQuestion['question_type'] === 'multiple_choice'): ?>
                        <?php foreach ($currentQuestion['options'] as $option): ?>
                            <div class="option">
                                <label style="display: block; cursor: pointer;">
                                    <input type="checkbox" name="answer[]" value="<?= $option['id'] ?>" style="margin-right: 10px;">
                                    <?= htmlspecialchars($option['text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        
                    <?php elseif ($currentQuestion['question_type'] === 'true_false'): ?>
                        <div class="option">
                            <label style="display: block; cursor: pointer;">
                                <input type="radio" name="answer" value="1" style="margin-right: 10px;">
                                True
                            </label>
                        </div>
                        <div class="option">
                            <label style="display: block; cursor: pointer;">
                                <input type="radio" name="answer" value="0" style="margin-right: 10px;">
                                False
                            </label>
                        </div>
                        
                    <?php elseif ($currentQuestion['question_type'] === 'fill_blank'): ?>
                        <div class="option">
                            <input type="text" name="answer" placeholder="Type your answer here..." style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 16px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="quiz-navigation">
                <div>
                    <?php if ($currentQuestionIndex > 0): ?>
                        <a href="?action=take_quiz&attempt_id=<?= $attemptId ?>&question_id=<?= $questions[$currentQuestionIndex - 1]['id'] ?>" class="btn btn-secondary">
                            ← Previous
                        </a>
                    <?php endif; ?>
                </div>
                
                <div>
                    <?php if ($currentQuestionIndex < $totalQuestions - 1): ?>
                        <button type="submit" class="btn">Next Question →</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success">Complete Quiz</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <p>No more questions available.</p>
        <form method="post">
            <input type="hidden" name="form_type" value="complete_quiz">
            <input type="hidden" name="attempt_id" value="<?= $attemptId ?>">
            <button type="submit" class="btn btn-success">Complete Quiz</button>
        </form>
    </div>
<?php endif; ?>

<script>
// Timer functionality
<?php if ($attempt['time_limit'] > 0): ?>
let timeRemaining = <?= $attempt['time_limit'] ?>;
const timerElement = document.getElementById('timeRemaining');
const startTime = new Date().getTime() - (<?= $attempt['time_limit'] * 1000 ?> - timeRemaining * 1000);

function updateTimer() {
    const elapsed = Math.floor((new Date().getTime() - startTime) / 1000);
    timeRemaining = <?= $attempt['time_limit'] ?> - elapsed;
    
    if (timeRemaining <= 0) {
        // Time's up - submit quiz
        document.getElementById('quizForm').innerHTML = '<input type="hidden" name="form_type" value="complete_quiz"><input type="hidden" name="attempt_id" value="<?= $attemptId ?>">';
        document.getElementById('quizForm').submit();
        return;
    }
    
    timerElement.textContent = timeRemaining;
    
    // Change color when time is running out
    if (timeRemaining <= 30) {
        timerElement.style.color = '#dc3545';
    } else if (timeRemaining <= 60) {
        timerElement.style.color = '#ffc107';
    }
}

setInterval(updateTimer, 1000);
updateTimer();
<?php endif; ?>

// Option selection styling
document.addEventListener('DOMContentLoaded', function() {
    const options = document.querySelectorAll('.option');
    
    options.forEach(option => {
        option.addEventListener('click', function() {
            const input = this.querySelector('input[type="radio"], input[type="checkbox"]');
            if (input) {
                if (input.type === 'radio') {
                    // Remove selected class from all options
                    options.forEach(opt => opt.classList.remove('selected'));
                }
                this.classList.toggle('selected');
            }
        });
    });
    
    // Auto-submit on Enter key for fill-in-the-blank
    const textInput = document.querySelector('input[type="text"]');
    if (textInput) {
        textInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('quizForm').submit();
            }
        });
    }
});

// Warn before leaving page
window.addEventListener('beforeunload', function(e) {
    if (document.getElementById('quizForm')) {
        e.preventDefault();
        e.returnValue = 'Are you sure you want to leave? Your progress will be lost.';
        return e.returnValue;
    }
});
</script>
