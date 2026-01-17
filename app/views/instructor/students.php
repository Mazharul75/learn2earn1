<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Students Enrolled in: <?= $course['title']; ?></h2>
    <table border="1">
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Date Enrolled</th>
        </tr>
        <?php if(!empty($students)): ?>
            <?php foreach($students as $student): ?>
            <tr>
                <td><?= $student['name']; ?></td>
                <td><?= $student['email']; ?></td>
                <td>
                    <?php if($student['progress'] == 100): ?>
                        <span style="color:green; font-weight:bold;">✅ Completed (Passed)</span>
                    <?php else: ?>
                        <span style="color:orange;">⏳ In Progress</span>
                    <?php endif; ?>
                </td>
                <td><?= $student['enrolled_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <a href="<?= BASE_URL ?>instructor/index">Back</a>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>