<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center;">
        
        <?php if($status == 'passed'): ?>
            <h1 style="font-size: 4rem; margin: 0;">🎉</h1>
            <h2 style="color: #27ae60; margin-top: 10px;">Congratulations!</h2>
            <p style="font-size: 1.2rem; color: #555;">You Passed the Quiz</p>
            
            <div style="background: #e8f8f5; padding: 15px; border-radius: 5px; margin: 20px 0; display: inline-block; width: 80%;">
                <h3 style="margin: 0; color: #2c3e50;">Score: <?= $score ?> / <?= $total ?></h3>
            </div>
            
            <p>You have officially completed this course.</p>
            <br>
            <a href="<?= BASE_URL ?>dashboard/index" class="btn" style="background: #3498db; padding: 10px 25px;">Go to Dashboard</a>

        <?php else: ?>
            <h1 style="font-size: 4rem; margin: 0;">❌</h1>
            <h2 style="color: #c0392b; margin-top: 10px;">Quiz Failed</h2>
            <p style="font-size: 1.2rem; color: #555;">Don't give up!</p>

            <div style="background: #fdedec; padding: 15px; border-radius: 5px; margin: 20px 0; display: inline-block; width: 80%;">
                <h3 style="margin: 0; color: #c0392b;">Score: <?= $score ?> / <?= $total ?></h3>
            </div>
            
            <p>You need at least 50% to pass. Please review the materials and try again.</p>
            <br>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="<?= BASE_URL ?>learner/takeQuiz/<?= $course_id ?>" class="btn" style="background: #e67e22;">Retake Quiz</a>
                <a href="<?= BASE_URL ?>learner/progress/<?= $course_id ?>" class="btn" style="background: #95a5a6;">Back to Materials</a>
            </div>

        <?php endif; ?>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>