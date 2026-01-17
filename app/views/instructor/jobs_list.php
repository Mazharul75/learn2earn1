<!DOCTYPE html>
<html>
<head>
    <title>Recommend Students</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <nav>
        <a href="<?= BASE_URL ?>instructor/index">Back to Dashboard</a>
    </nav>
    <h2>Available Jobs</h2>
    <p>Select a job to recommend a high-performing student.</p>
    
    <table border="1">
        <tr>
            <th>Job Title</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php if(!empty($jobs)): ?>
            <?php foreach($jobs as $job): ?>
            <tr>
                <td><?= $job['title'] ?></td>
                <td><?= substr($job['description'], 0, 50) ?>...</td>
                <td>
                    <a href="<?= BASE_URL ?>instructor/recommend/<?= $job['id'] ?>" class="btn">Recommend Student</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No active jobs found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>