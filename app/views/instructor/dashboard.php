<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="color: #2c3e50;">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></h1>
    </div>

    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; border-left: 5px solid #3498db;">
        
        <a href="<?= BASE_URL ?>instructor/create" class="btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <span>➕</span> Create New Course
        </a>
        
        <a href="<?= BASE_URL ?>instructor/viewJobs" class="btn" style="text-decoration: none; background: #3498db; display: flex; align-items: center; gap: 8px;">
            <span>💼</span> Recommend Students
        </a>
        
        <a href="<?= BASE_URL ?>instructor/requests" class="btn" style="text-decoration: none; background: #e67e22; display: flex; align-items: center; gap: 8px;">
            <span>📩</span> Seat Requests
        </a>
    </div>

    <h3 style="color: #34495e; margin-bottom: 15px;">Your Active Courses</h3>
    
 
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Course Title</th>
                    <th style="padding: 15px; text-align: left;">Level</th>
                    <th style="padding: 15px; text-align: left;">Capacity</th> 
                    <th style="padding: 15px; text-align: left;">Description</th>
                    <th style="padding: 15px; text-align: center;">Management</th> 
                    <th style="padding: 15px; text-align: center;">Student List</th> </tr>
            </thead>
            <tbody>
            <?php if(!empty($courses)): ?>
                <?php foreach($courses as $course) : ?>
                <tr style="border-bottom: 1px solid #eee; transition: background 0.2s;">
                    
                    <td style="padding: 15px; font-weight: 600; color: #2c3e50;">
                        <?= $course['title']; ?>
                    </td>

                    <td style="padding: 15px;">
                        <?php 
                            $badgeColor = '#95a5a6'; // Default Grey
                            if($course['difficulty'] == 'Beginner') $badgeColor = '#2ecc71'; // Green
                            if($course['difficulty'] == 'Intermediate') $badgeColor = '#f39c12'; // Orange
                            if($course['difficulty'] == 'Advanced') $badgeColor = '#e74c3c'; // Red
                        ?>
                        <span style="background: <?= $badgeColor ?>; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem;">
                            <?= $course['difficulty']; ?>
                        </span>
                    </td>
                    
                    <td style="padding: 15px;">
                        <span style="font-weight: bold; color: #333;"><?= $course['student_count']; ?></span> 
                        <span style="color: #999;">/ <?= $course['max_capacity']; ?></span>
                        <div style="font-size: 0.8rem; color: #e67e22; margin-top: 3px;">
                            (<?= $course['reserved_seats']; ?> Reserved)
                        </div>
                    </td>

                    <td style="padding: 15px; color: #666; font-size: 0.9rem;">
                        <?= substr($course['description'], 0, 40); ?>...
                    </td>
                    
                    <td style="padding: 15px; text-align: center;">
                        <a href="<?= BASE_URL ?>instructor/manage/<?= $course['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #34495e; color: white; text-decoration: none; border-radius: 4px;">
                            ⚙️ Tools
                        </a>
                    </td>

                    <td style="padding: 15px; text-align: center;">
                        <a href="<?= BASE_URL ?>instructor/students/<?= $course['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #8e44ad; color: white; text-decoration: none; border-radius: 4px;">
                            👥 View
                        </a>
                    </td>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #7f8c8d;">
                        <h3>📭 No courses found</h3>
                        <p>Get started by clicking "Create New Course" above.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>