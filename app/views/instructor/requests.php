<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <nav>
        <a href="<?= BASE_URL ?>instructor/index">Back to Dashboard</a>
    </nav>

    <h2>📩 Seat Requests</h2>
    <p>These students want to join your full courses via Reserved Seats.</p>

    <table border="1">
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Course Requested</th>
            <th>Action</th>
        </tr>
        <?php if(!empty($requests)): ?>
            <?php foreach($requests as $r): ?>
            <tr>
                <td><?= $r['learner_name'] ?></td>
                <td><?= $r['learner_email'] ?></td>
                <td><strong><?= $r['course_title'] ?></strong></td>
                <td>
                    <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/approve" class="btn">✅ Approve</a>
                    <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/reject" class="btn btn-danger" style="background:red;">❌ Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No pending requests.</td></tr>
        <?php endif; ?>
    </table>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>