<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <nav>
        <a href="<?= BASE_URL ?>auth/profile">Manage Profile</a> | 
        <a href="<?= BASE_URL ?>auth/logout">Logout</a>
    </nav>
    <h1>Welcome, <?= $_SESSION['user_name']; ?> (Learner)</h1>
    <nav>
        <ul>
            <li><a href="<?= BASE_URL ?>learner/courses">Enroll in New Courses</a></li>
            <li><a href="<?= BASE_URL ?>learner/jobs">Apply for Jobs</a></li>
            <li><a href="<?= BASE_URL ?>auth/logout">Logout</a></li>
        </ul>
    </nav>
    <div style="background-color: #e8f8f5; padding: 15px; margin-bottom: 20px; border: 1px solid #2ecc71; border-radius: 5px;">
        <h3>🔔 Your Notifications</h3>
        
        <?php if (!empty($notifications)): ?>
            <ul>
            <?php foreach($notifications as $notif): ?>
                <li>
                    <?= $notif['message'] ?>
                    <?php if(!empty($notif['link']) && $notif['link'] != '#'): ?>
                        <a href="<?= $notif['link'] ?>"><strong>[View Job]</strong></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have no new notifications.</p>
        <?php endif; ?>
    </div>
    <hr>

    <h3>My Enrolled Courses</h3>
    <table border="1">
        <tr>
            <th>Course Title</th>
            <th>Difficulty</th>
        </tr>
        <?php if(!empty($myCourses)): ?>
            <?php foreach($myCourses as $course): ?>
            <tr>
                <td><?= $course['title']; ?></td>
                <td><?= $course['difficulty']; ?></td>
                <td>
                    <a href="<?= BASE_URL ?>learner/progress/<?= $course['id']; ?>">Track Progress</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">You haven't enrolled in any courses yet.</td></tr>
        <?php endif; ?>
    </table>
    <h3>My Job Applications & Hiring Status</h3>
    <table border="1">
        <tr>
            <th>Job Title</th>
            <th>Status</th>
        </tr>
        <?php foreach($myJobs as $job): ?>
        <tr>
            <td><?= $job['title']; ?></td>
            <td>
                <?php if($job['status'] == 'selected'): ?>
                    <strong style="color: green;">✅ HIRED / SELECTED</strong>
                <?php elseif($job['status'] == 'rejected'): ?>
                    <span style="color: red;">Rejected</span>
                <?php else: ?>
                    <span>Applied (Pending)</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>