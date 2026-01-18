<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 700px; margin: 30px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 30px;">
            📝 Final Assessment
        </h2>
        
        <form action="<?= BASE_URL ?>learner/submitQuiz/<?= $course_id ?>" method="POST">
            <?php foreach($questions as $index => $q): ?>
                <div style="margin-bottom: 25px; padding: 15px; background: #f9f9f9; border-radius: 5px; border-left: 4px solid #3498db;">
                    <p style="font-size: 1.1rem; margin-top: 0;"><strong>Q<?= $index+1 ?>: <?= $q['question_text'] ?></strong></p>
                    
                    <label style="display: block; margin: 8px 0; cursor: pointer;">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="A" required> <?= $q['option_a'] ?>
                    </label>
                    <label style="display: block; margin: 8px 0; cursor: pointer;">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="B"> <?= $q['option_b'] ?>
                    </label>
                    <label style="display: block; margin: 8px 0; cursor: pointer;">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="C"> <?= $q['option_c'] ?>
                    </label>
                    <label style="display: block; margin: 8px 0; cursor: pointer;">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="D"> <?= $q['option_d'] ?>
                    </label>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 12px; background: #27ae60;">Submit Results</button>
        </form>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>