<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<h2>Final Assessment</h2>
<form action="<?= BASE_URL ?>learner/submitQuiz/<?= $course_id ?>" method="POST">
    <?php foreach($questions as $index => $q): ?>
        <p><strong>Q<?= $index+1 ?>: <?= $q['question_text'] ?></strong></p>
        <input type="radio" name="answer[<?= $q['id'] ?>]" value="A"> <?= $q['option_a'] ?><br>
        <input type="radio" name="answer[<?= $q['id'] ?>]" value="B"> <?= $q['option_b'] ?><br>
        <input type="radio" name="answer[<?= $q['id'] ?>]" value="C"> <?= $q['option_c'] ?><br>
        <input type="radio" name="answer[<?= $q['id'] ?>]" value="D"> <?= $q['option_d'] ?><br>
    <?php endforeach; ?>
    <br><button type="submit" class="btn">Submit Results</button>
</form>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>