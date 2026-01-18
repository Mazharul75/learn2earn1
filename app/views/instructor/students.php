<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Students Enrolled in: <span style="color: #3498db;"><?= $course['title']; ?></span></h2>
    
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead style="background: #ecf0f1;">
            <tr>
                <th style="padding: 10px;">Student Name</th>
                <th style="padding: 10px;">Email</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px;">Date Enrolled</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($students)): ?>
            <?php foreach($students as $student): ?>
            <tr>
                <td style="padding: 10px;"><?= $student['name']; ?></td>
                <td style="padding: 10px;"><?= $student['email']; ?></td>
                <td style="padding: 10px;">
                    <?php if($student['progress'] == 100): ?>
                        <span style="color:green; font-weight:bold;">✅ Completed (Passed)</span>
                    <?php else: ?>
                        <span style="color:orange;">⏳ In Progress</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 10px;"><?= $student['enrolled_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="padding: 15px; text-align: center;">No students currently enrolled.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    
    <br>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>