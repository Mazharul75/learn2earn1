<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Applicants for: <?= $job['title']; ?></h2>

    <!-- ================= Applicants List ================= -->
    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>CV</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if(!empty($applicants)): ?>
            <?php foreach($applicants as $app): ?>
            <tr>
                <td><?= $app['name']; ?></td>
                <td><?= $app['email']; ?></td>

                <!-- CV Column (Gemini Fix Applied) -->
                <td>
                    <?php if(!empty($app['cv_file'])): ?>
                        <a href="<?= BASE_URL ?>public/uploads/cvs/<?= $app['cv_file']; ?>" target="_blank">
                            Download CV
                        </a>
                    <?php else: ?>
                        No CV
                    <?php endif; ?>
                </td>

                <td>
                    <strong><?= ucfirst($app['status']); ?></strong>
                </td>

                <td>
                    <?php if($app['status'] == 'applied'): ?>
                        <a href="<?= BASE_URL ?>client/updateApplication/<?= $app['app_id']; ?>/selected" class="btn">
                            Hire
                        </a>
                        |
                        <a href="<?= BASE_URL ?>client/updateApplication/<?= $app['app_id']; ?>/rejected" 
                           class="btn" style="background:#e74c3c;">
                            Reject
                        </a>
                    <?php else: ?>
                        <span>Decision Made</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No applicants yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <hr>

    <!-- ================= Instructor Recommendations (Gemini Feature) ================= -->
    <h3>🌟 Instructor Recommendations</h3>

    <table border="1">
        <tr>
            <th>Recommended Learner</th>
            <th>Recommended By</th>
            <th>Action</th>
        </tr>
        <?php if(!empty($recommendations)): ?>
            <?php foreach($recommendations as $rec): ?>
            <tr>
                <td><?= $rec['learner_name'] ?> (<?= $rec['learner_email'] ?>)</td>
                <td>Instructor: <?= $rec['instructor_name'] ?></td>
                <td>
                    <?php 
                        // LOGIC: Check if this learner is already in the main Applicant list
                        $alreadyApplied = false;
                        foreach($applicants as $app) {
                            // Check ID match (Safest) or Email match
                            if((isset($app['learner_id']) && $app['learner_id'] == $rec['learner_id']) || 
                               ($app['email'] == $rec['learner_email'])) {
                                $alreadyApplied = true;
                                break;
                            }
                        }
                    ?>

                    <?php if($alreadyApplied): ?>
                        <span style="color: gray; font-weight: bold;">Already Applied</span>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>client/inviteLearner/<?= $rec['learner_id'] ?>/<?= $job['id'] ?>" class="btn">Invite to Apply</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No recommendations yet.</td></tr>
        <?php endif; ?>
    </table>

    <br>

    <a href="<?= BASE_URL ?>client/index">Back to Dashboard</a>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>