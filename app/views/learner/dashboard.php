<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="color: #2c3e50;">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?> <span style="font-size: 0.6em; color: #7f8c8d;">(Learner)</span></h1>
    </div>

    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; border-left: 5px solid #3498db;">
        <a href="<?= BASE_URL ?>learner/courses" class="btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <span>🔍</span> Browse & Enroll in Courses
        </a>
        <a href="<?= BASE_URL ?>learner/jobs" class="btn" style="text-decoration: none; background: #9b59b6; display: flex; align-items: center; gap: 8px;">
            <span>💼</span> Apply for Jobs
        </a>
    </div>

    <?php if (!empty($notifications)): ?>
    <div style="background: #e8f8f5; padding: 20px; border-radius: 8px; border: 1px solid #d1f2eb; margin-bottom: 30px;">
        <h3 style="color: #16a085; margin-top: 0;">🔔 Your Notifications</h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php foreach($notifications as $notif): ?>
                <li style="background: white; padding: 10px 15px; margin-bottom: 10px; border-radius: 5px; border-left: 3px solid #2ecc71; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <?= $notif['message'] ?>
                    <?php if(!empty($notif['link']) && $notif['link'] != '#'): ?>
                        <a href="<?= $notif['link'] ?>" style="margin-left: 10px; color: #2980b9; text-decoration: none; font-weight: bold;">
                             &rarr; View Details
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <div>
            <h3 style="color: #34495e; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">My Enrolled Courses</h3>
            <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
                <table border="0" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 12px; text-align: left; color: #555;">Course Title</th>
                            <th style="padding: 12px; text-align: left; color: #555;">Level</th>
                            <th style="padding: 12px; text-align: center; color: #555;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($myCourses)): ?>
                        <?php foreach($myCourses as $course): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; font-weight: 500;"><?= $course['title']; ?></td>
                            <td style="padding: 12px;">
                                <span class="role-badge" style="background:#eee; color:#333; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;">
                                    <?= $course['difficulty']; ?>
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="<?= BASE_URL ?>learner/progress/<?= $course['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.85rem; background: #2980b9;">
                                    Track Progress
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="padding: 20px; text-align: center; color: #7f8c8d;">You haven't enrolled in any courses yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h3 style="color: #34495e; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">Job Applications</h3>
            <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
                <table border="0" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 12px; text-align: left; color: #555;">Job Title</th>
                            <th style="padding: 12px; text-align: center; color: #555;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($myJobs)): ?>
                        <?php foreach($myJobs as $job): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><?= $job['title']; ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <?php if($job['status'] == 'selected'): ?>
                                    <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">
                                        ✅ Hired
                                    </span>
                                <?php elseif($job['status'] == 'rejected'): ?>
                                    <span style="background: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">
                                        ❌ Rejected
                                    </span>
                                <?php else: ?>
                                    <span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">
                                        ⏳ Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2" style="padding: 20px; text-align: center; color: #7f8c8d;">No active job applications.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>