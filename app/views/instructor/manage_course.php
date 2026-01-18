<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Manage Course: <?= $course['title']; ?></h2>
    <a href="<?= BASE_URL ?>instructor/index" style="margin-bottom: 20px; display: inline-block;">&larr; Back to Dashboard</a>

    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3>1. Upload New Material</h3>
        <form action="<?= BASE_URL ?>instructor/uploadMaterial" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
            <input type="file" name="material" required>
            <button type="submit" class="btn" style="font-size: 0.9rem;">Upload Material</button>
        </form>

        <h4 style="margin-top: 20px;">Existing Materials</h4>
        <ul>
            <?php if(!empty($materials)): ?>
                <?php foreach($materials as $m): ?>
                    <li>
                        <a href="<?= BASE_URL ?>public/uploads/materials/<?= $m['file_name']; ?>" target="_blank">
                            📄 <?= $m['file_name']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No materials uploaded yet.</li>
            <?php endif; ?>
        </ul>
    </div>

    <hr>
    
    <div style="margin-bottom: 30px;">
        <h3>2. Manage Learning Tasks</h3>
        <form action="<?= BASE_URL ?>instructor/createTask" method="POST" style="background: #fff; padding: 15px; border: 1px solid #ddd;">
            <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
            <input type="text" name="title" placeholder="Task Title" required style="width: 100%; margin-bottom: 10px; padding: 8px;"><br>
            <textarea name="description" placeholder="Task Instructions..." required style="width: 100%; height: 80px; padding: 8px;"></textarea><br>
            <button type="submit" class="btn" style="margin-top: 10px;">Add Task</button>
        </form>

        <br>

        <div style="background: #e8f6f3; padding: 15px; border: 1px solid #1abc9c; border-radius: 5px;">
            <h4>📋 Pending Student Submissions</h4>
            
            <?php if(empty($submissions)): ?>
                <p>No pending submissions to review.</p>
            <?php else: ?>
                <table border="1" style="width:100%; background:white; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #d1f2eb;">
                            <th style="padding:8px;">Student</th>
                            <th style="padding:8px;">Task</th>
                            <th style="padding:8px;">Submission</th>
                            <th style="padding:8px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($submissions as $sub): ?>
                    <tr>
                        <td style="padding:8px;"><?= $sub['student_name'] ?></td>
                        <td style="padding:8px;"><?= $sub['task_title'] ?></td>
                        <td style="padding:8px;">
                            <a href="<?= $sub['file_url'] ?>" target="_blank">View File</a>
                        </td>
                        <td style="padding:8px;">
                            <form action="<?= BASE_URL ?>instructor/reviewTask" method="POST">
                                <input type="hidden" name="completion_id" value="<?= $sub['completion_id'] ?>">
                                <textarea name="feedback" placeholder="Feedback..." required style="width: 100%; height: 40px;"></textarea><br>
                                <button type="submit" name="status" value="approved" style="background:green; color:white; border:none; padding:5px 10px; cursor:pointer;">Approve</button>
                                <button type="submit" name="status" value="rejected" style="background:red; color:white; border:none; padding:5px 10px; cursor:pointer;">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <div style="background: #fdf2e9; padding: 20px; border-radius: 8px; border: 1px solid #fae5d3;">
        <h3>3. Quiz Management</h3>

        <?php if (!empty($questions)): ?>
            <p>
                This course has an active quiz with <strong><?= count($questions); ?></strong> question(s).
            </p>
        <?php else: ?>
            <p>No quiz exists yet. Add the first question to create the quiz.</p>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>instructor/createQuiz" method="POST" style="margin-top: 15px;">
            <input type="hidden" name="course_id" value="<?= $course['id']; ?>">

            <input type="text" name="question_text" placeholder="Enter Question Text" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="option_a" placeholder="Option A" required style="padding: 8px;">
                <input type="text" name="option_b" placeholder="Option B" required style="padding: 8px;">
                <input type="text" name="option_c" placeholder="Option C" required style="padding: 8px;">
                <input type="text" name="option_d" placeholder="Option D" required style="padding: 8px;">
            </div>
            <br>
            <input type="text" name="correct_option" placeholder="Correct Answer (A, B, C, or D)" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>

            <button type="submit" class="btn" style="background: #e67e22;">
                <?= !empty($questions) ? 'Add Another Question' : 'Create Quiz & Add First Question'; ?>
            </button>
        </form>

        <br>

        <h4>Current Quiz Questions</h4>
        <table border="1" style="width: 100%; background: white; border-collapse: collapse;">
            <tr style="background: #f6ddcc;">
                <th style="padding: 8px;">Question</th>
                <th style="padding: 8px;">Correct Option</th>
            </tr>
            <?php if (!empty($questions)): ?>
                <?php foreach ($questions as $q): ?>
                    <tr>
                        <td style="padding: 8px;"><?= $q['question_text']; ?></td>
                        <td style="padding: 8px; text-align: center; font-weight: bold;"><?= $q['correct_option']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2" style="padding: 8px;">No quiz questions added yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>