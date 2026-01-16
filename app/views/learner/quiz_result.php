<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <?php if($status == 'passed'): ?>
            <h1 style="color: green;">🎉 Congratulations!</h1>
            <h2>You Passed the Quiz</h2>
            <h3>Score: <?= $score ?> / <?= $total ?></h3>
            <p>You have officially completed this course.</p>
            <a href="<?= BASE_URL ?>dashboard/index" class="btn">Go to Dashboard</a>
        <?php else: ?>
            <h1 style="color: red;">❌ Quiz Failed</h1>
            <h3>Score: <?= $score ?> / <?= $total ?></h3>
            <p>You need at least 50% to pass. Please review the materials and try again.</p>
            <a href="<?= BASE_URL ?>learner/takeQuiz/<?= $course_id ?>" class="btn">Retake Quiz</a>
            <br><br>
            <a href="<?= BASE_URL ?>learner/progress/<?= $course_id ?>">Back to Materials</a>
        <?php endif; ?>
    </div>
</body>
</html>