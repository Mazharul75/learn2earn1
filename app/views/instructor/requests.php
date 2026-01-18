<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>📩 Seat Requests</h2>
        
    </div>

    <div style="background: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0; color: #b7791f;"><strong>Note:</strong> These students are requesting "Reserved Seats" because the public slots are full.</p>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #fdf2e9; border-bottom: 2px solid #fae5d3;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Student Name</th>
                    <th style="padding: 15px; text-align: left;">Email</th>
                    <th style="padding: 15px; text-align: left;">Course Requested</th>
                    <th style="padding: 15px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($requests)): ?>
                <?php foreach($requests as $r): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;"><?= $r['learner_name'] ?></td>
                    <td style="padding: 15px; color: #666;"><?= $r['learner_email'] ?></td>
                    <td style="padding: 15px;"><strong><?= $r['course_title'] ?></strong></td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/approve" class="btn" style="background: #27ae60; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.9rem; margin-right: 5px;">
                            ✅ Approve
                        </a>
                        <a href="<?= BASE_URL ?>instructor/handleRequest/<?= $r['id'] ?>/reject" class="btn" style="background: #c0392b; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.9rem;">
                            ❌ Reject
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 40px; text-align: center; color: #7f8c8d;">
                        <h3>✨ All Caught Up!</h3>
                        <p>No pending seat requests at this time.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>