<!DOCTYPE html>
<html>
<head>
    <title>Available Jobs</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Available Skill-Based Jobs</h2>
    <table border="1">
        <tr>
            <th>Job Title</th>
            <th>Required Course</th>
            <th>Action</th>
        </tr>
        <?php foreach($allJobs as $job) : ?>
        <tr>
            <td><?= $job['title']; ?></td>
            <td><?= $job['required_course_id'] ? "Course ID: " . $job['required_course_id'] : 'None'; ?></td>
            <td>
                <?php if($job['is_unlocked']): ?>
                    <a href="<?= BASE_URL ?>learner/applyForm/<?= $job['id']; ?>" class="btn">Apply With CV</a>
                <?php else: ?>
                    <span style="color: red;">🔒 Locked (Complete Course First)</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="<?= BASE_URL ?>dashboard/index">Back to Dashboard</a>
</body>
</html>