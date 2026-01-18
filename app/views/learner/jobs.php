<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Available Skill-Based Jobs</h2>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #555;">Job Title</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Prerequisite</th>
                    <th style="padding: 15px; text-align: center; color: #555;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($allJobs)): ?>
                <?php foreach($allJobs as $job) : ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;"><?= $job['title']; ?></td>
                    <td style="padding: 15px; color: #666;">
                        <?= $job['required_course_id'] ? "Must Complete Course ID: " . $job['required_course_id'] : 'No Prerequisite'; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <?php if($job['is_unlocked']): ?>
                            <a href="<?= BASE_URL ?>learner/applyForm/<?= $job['id']; ?>" class="btn" style="background: #9b59b6;">
                                Apply With CV
                            </a>
                        <?php else: ?>
                            <span style="color: #c0392b; font-weight: bold; font-size: 0.9rem;">
                                🔒 Locked (Course Incomplete)
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" style="padding: 20px; text-align: center;">No jobs available at the moment.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>