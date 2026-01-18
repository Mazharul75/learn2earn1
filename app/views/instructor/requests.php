<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>📩 Seat Requests</h2>
    <p>These students want to join your full courses via Reserved Seats.</p>

    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background: #fdf2e9;">
            <tr>
                <th style="padding: 10px;">Student Name</th>
                <th style="padding: 10px;">Email</th>
                <th style="padding: 10px;">Course Requested</th>
                <th style="padding: 10px;">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($requests)): ?>
            <?php foreach($requests as $r): ?>
            <tr>
                <td style="padding: 10px;"><?= $r['learner_name'] ?></td>
                <td style="padding: 10px;"><?= $r['learner_email'] ?></td>
                <td style="padding: 10px;"><strong><?= $r['course_title'] ?></strong></td>
                <td style="padding: 10px;">
                    <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/approve" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">✅ Approve</a>
                    <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/reject" class="btn btn-danger" style="background:red; padding: 5px 10px; font-size: 0.8rem;">❌ Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="padding: 20px; text-align: center;">No pending requests.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>