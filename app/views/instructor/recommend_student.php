<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Recommend Student for: <span style="color:#2c3e50;"><?= $job['title'] ?></span></h2>

    <?php if(empty($students)): ?>
        <div style="background-color: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-top: 20px;">
            <h3>⚠️ No Eligible Students</h3>
            <p>No students have completed the required course for this job yet.</p>
            <p style="font-family: monospace; background: rgba(255,255,255,0.5); padding: 5px; display: inline-block;">Required Course ID: <?= $job['required_course_id'] ?></p>
            <br><br>
            <a href="<?= BASE_URL ?>instructor/viewJobs" class="btn" style="background: #721c24;">Back to Jobs</a>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 500px; margin-top: 20px;">
            <form action="<?= BASE_URL ?>instructor/submitRecommendation" method="POST">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                
                <label>Select a Course Graduate:</label><br>
                <select name="learner_id" required style="padding: 10px; width: 100%; margin: 10px 0;">
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= $s['name'] ?> (<?= $s['email'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <button type="submit" class="btn" style="width: 100%;">Submit Recommendation & Notify Student</button>
            </form>
            <br>
            <a href="<?= BASE_URL ?>instructor/viewJobs" style="text-decoration: underline; color: #7f8c8d;">Cancel</a>
        </div>
    <?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>