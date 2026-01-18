<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></h1>
    </div>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px; display: flex; gap: 15px;">
        <a href="<?= BASE_URL ?>instructor/create" class="btn" style="text-decoration: none;">➕ Create New Course</a>
        <a href="<?= BASE_URL ?>instructor/viewJobs" class="btn" style="text-decoration: none; background: #3498db;">💼 Recommend Students</a>
        <a href="<?= BASE_URL ?>instructor/requests" class="btn" style="text-decoration: none; background: #e67e22;">📩 Seat Requests</a>
    </div>

    <h3>Your Courses</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #ecf0f1;">
            <tr>
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Difficulty</th>
                <th style="padding: 10px;">Enrollment Status</th> 
                <th style="padding: 10px;">Description</th>
                <th style="padding: 10px;">Action</th> 
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($courses)): ?>
            <?php foreach($courses as $course) : ?>
            <tr>
                <td style="padding: 10px;"><?= $course['title']; ?></td>
                <td style="padding: 10px;">
                    <span class="role-badge" style="background:#eee; color:#333; padding: 2px 8px; border-radius: 4px;">
                        <?= $course['difficulty']; ?>
                    </span>
                </td>
                
                <td style="padding: 10px;">
                    <strong><?= $course['student_count']; ?> / <?= $course['max_capacity']; ?></strong>
                    <br>
                    <small style="color: #7f8c8d;">(<?= $course['reserved_seats']; ?> Reserved)</small>
                </td>

                <td style="padding: 10px;"><?= substr($course['description'], 0, 50); ?>...</td>
                
                <td style="padding: 10px;">
                    <a href="<?= BASE_URL ?>instructor/manage/<?= $course['id']; ?>" class="btn" style="padding:5px 10px; font-size:12px;">⚙️ Manage</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="padding: 20px; text-align: center;">No courses created yet. Click "Create New Course" to begin.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>