<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Available Jobs</h2>
    <p>Select a job to recommend a high-performing student.</p>
    
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background: #eaf2f8;">
            <tr>
                <th style="padding: 10px;">Job Title</th>
                <th style="padding: 10px;">Description</th>
                <th style="padding: 10px;">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($jobs)): ?>
            <?php foreach($jobs as $job): ?>
            <tr>
                <td style="padding: 10px;"><?= $job['title'] ?></td>
                <td style="padding: 10px;"><?= substr($job['description'], 0, 50) ?>...</td>
                <td style="padding: 10px;">
                    <a href="<?= BASE_URL ?>instructor/recommend/<?= $job['id'] ?>" class="btn">Recommend Student</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="padding: 20px; text-align: center;">No active jobs found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>