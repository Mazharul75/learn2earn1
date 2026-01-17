<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Recommend Student for: <?= $job['title'] ?></h2>

    <?php if(empty($students)): ?>
        <div style="background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; color: #721c24;">
            <p><strong>Notice:</strong> No students have completed the required course for this job yet.</p>
            <p>Required Course ID: <?= $job['required_course_id'] ?></p>
            <a href="<?= BASE_URL ?>instructor/viewJobs">Back to Jobs</a>
        </div>
    <?php else: ?>
        <form action="<?= BASE_URL ?>instructor/submitRecommendation" method="POST">
            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
            
            <label>Select a Course Graduate:</label><br>
            <select name="learner_id" required style="padding: 10px; width: 300px;">
                <?php foreach($students as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= $s['name'] ?> (<?= $s['email'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit" class="btn">Submit Recommendation & Notify Student</button>
        </form>
        <br>
        <a href="<?= BASE_URL ?>instructor/viewJobs">Cancel</a>
    <?php endif; ?>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>