<?php
// Results page view
$attemptId = $_GET['attempt_id'] ?? 0;
$results = $quiz->getAttemptResults($attemptId);

if (!$results) {
    echo '<div class="card"><p>Results not found.</p></div>';
    return;
}

$passed = $results['percentage'] >= 70; // Default passing score
$scoreColor = $passed ? '#28a745' : '#dc3545';
$scoreText = $passed ? 'PASSED' : 'FAILED';
?>

<div class="card">
    <h1>Quiz Results</h1>
    
    <div class="score-display">
        <div class="score-number" style="color: <?= $scoreColor ?>;">
            <?= $results['percentage'] ?>%
        </div>
        <div class="score-label">
            You scored <?= $results['score'] ?> out of <?= $results['max_score'] ?> points
        </div>
        <div class="score-label <?= $passed ? 'passed' : 'failed' ?>">
            <?= $scoreText ?>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <div style="font-size: 2rem; font-weight: bold; color: #667eea;">
                <?= count($results['answers']) ?>
            </div>
            <div style="color: #666;">Questions Answered</div>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <div style="font-size: 2rem; font-weight: bold; color: #28a745;">
                <?= count(array_filter($results['answers'], function($answer) { return $answer['is_correct']; })) ?>
            </div>
            <div style="color: #666;">Correct Answers</div>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <div style="font-size: 2rem; font-weight: bold; color: #dc3545;">
                <?= count(array_filter($results['answers'], function($answer) { return !$answer['is_correct']; })) ?>
            </div>
            <div style="color: #666;">Incorrect Answers</div>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <div style="font-size: 2rem; font-weight: bold; color: #6c757d;">
                <?= gmdate('i:s', $results['time_taken']) ?>
            </div>
            <div style="color: #666;">Time Taken</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Answer Review</h2>
    
    <?php foreach ($results['answers'] as $index => $answer): ?>
        <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid <?= $answer['is_correct'] ? '#28a745' : '#dc3545' ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0; color: #333;">
                        Question <?= $index + 1 ?>
                        <span style="color: #667eea; font-weight: normal;"> (<?= $answer['points'] ?> point<?= $answer['points'] > 1 ? 's' : '' ?>)</span>
                    </h3>
                    <p style="margin: 10px 0; color: #333;"><?= htmlspecialchars($answer['question_text']) ?></p>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: <?= $answer['is_correct'] ? '#28a745' : '#dc3545' ?>;">
                        <?= $answer['is_correct'] ? '✅' : '❌' ?>
                    </div>
                    <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">
                        <?= $answer['is_correct'] ? 'Correct' : 'Incorrect' ?>
                    </div>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Your Answer:</strong> 
                <span style="color: <?= $answer['is_correct'] ? '#28a745' : '#dc3545' ?>;">
                    <?php if ($answer['question_type'] === 'multiple_choice'): ?>
                        <?php
                        $userAnswerIds = is_array(json_decode($answer['answer'])) ? json_decode($answer['answer']) : [$answer['answer']];
                        $userOptions = array_filter($answer['options'], function($option) use ($userAnswerIds) {
                            return in_array($option['id'], $userAnswerIds);
                        });
                        echo htmlspecialchars(implode(', ', array_column($userOptions, 'text')));
                        ?>
                    <?php elseif ($answer['question_type'] === 'true_false'): ?>
                        <?= $answer['answer'] ? 'True' : 'False' ?>
                    <?php else: ?>
                        <?= htmlspecialchars($answer['answer']) ?>
                    <?php endif; ?>
                </span>
            </div>
            
            <?php if (!$answer['is_correct']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Correct Answer:</strong>
                    <span style="color: #28a745;">
                        <?php
                        $correctOptions = array_filter($answer['options'], function($option) {
                            return $option['is_correct'];
                        });
                        echo htmlspecialchars(implode(', ', array_column($correctOptions, 'text')));
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if ($answer['explanation']): ?>
                <div class="explanation">
                    <strong>Explanation:</strong> <?= htmlspecialchars($answer['explanation']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Performance Summary</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
        <div>
            <h3>Quiz Details</h3>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Quiz:</strong> <?= htmlspecialchars($results['quiz_title']) ?></li>
                <li><strong>Completed:</strong> <?= date('M j, Y H:i', strtotime($results['completed_at'])) ?></li>
                <li><strong>Time Taken:</strong> <?= gmdate('i:s', $results['time_taken']) ?></li>
                <li><strong>Status:</strong> <span style="color: <?= $scoreColor ?>; font-weight: bold;"><?= $scoreText ?></span></li>
            </ul>
        </div>
        
        <div>
            <h3>Score Breakdown</h3>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Total Points:</strong> <?= $results['max_score'] ?></li>
                <li><strong>Earned Points:</strong> <?= $results['score'] ?></li>
                <li><strong>Percentage:</strong> <?= $results['percentage'] ?>%</li>
                <li><strong>Accuracy:</strong> <?= round((count(array_filter($results['answers'], function($answer) { return $answer['is_correct']; })) / count($results['answers'])) * 100, 1) ?>%</li>
            </ul>
        </div>
        
        <div>
            <h3>Recommendations</h3>
            <ul style="line-height: 1.6;">
                <?php if ($passed): ?>
                    <li>🎉 Great job! You passed the quiz.</li>
                    <li>📚 Review the explanations for questions you got wrong.</li>
                    <li>🎯 Try a more difficult quiz next time.</li>
                <?php else: ?>
                    <li>📖 Review the material and try again.</li>
                    <li>💡 Pay attention to the explanations for incorrect answers.</li>
                    <li>🔄 Practice with similar questions to improve.</li>
                <?php endif; ?>
                <li>⏱️ <?= $results['time_taken'] > 300 ? 'Consider managing your time better on timed quizzes.' : 'Good time management!' ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div style="text-align: center;">
        <h3>What's Next?</h3>
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px; flex-wrap: wrap;">
            <a href="?action=home" class="btn">Take Another Quiz</a>
            <a href="?action=take_quiz&attempt_id=<?= $attemptId ?>&review=1" class="btn btn-secondary">Review Quiz</a>
            <button onclick="window.print()" class="btn btn-secondary">Print Results</button>
            <button onclick="shareResults()" class="btn btn-secondary">Share Results</button>
        </div>
    </div>
</div>

<script>
function shareResults() {
    const text = `I scored ${results['percentage']}% on the "${results['quiz_title']}" quiz!`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Quiz Results',
            text: text,
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(text + ' ' + window.location.href).then(() => {
            alert('Results copied to clipboard!');
        });
    }
}

// Add print styles
window.addEventListener('beforeprint', function() {
    document.body.style.background = 'white';
});

window.addEventListener('afterprint', function() {
    document.body.style.background = '';
});
</script>

<style>
@media print {
    body {
        background: white !important;
    }
    
    .btn {
        display: none;
    }
    
    .quiz-navigation {
        display: none;
    }
}
</style>
