<!DOCTYPE html>
<html>
<head>
    <title>Manage Course</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>

    <h2>Manage Course: <?= $course['title']; ?></h2>

    <!-- ================= Upload Course Material ================= -->
    <h3>1. Upload New Material</h3>
    <form action="<?= BASE_URL ?>instructor/uploadMaterial" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
        <input type="file" name="material" required>
        <button type="submit">Upload Material</button>
    </form>

    <!-- ================= Existing Materials ================= -->
    <h3>Existing Materials</h3>
    <ul>
        <?php if(!empty($materials)): ?>
            <?php foreach($materials as $m): ?>
                <li>
                    <a href="<?= BASE_URL ?>public/uploads/<?= $m['file_name']; ?>" target="_blank">
                        <?= $m['file_name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No materials uploaded yet.</li>
        <?php endif; ?>
    </ul>
    <hr>
    
    <h3>2. Manage Learning Tasks</h3>
    <form action="<?= BASE_URL ?>instructor/createTask" method="POST">
        <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
        <input type="text" name="title" placeholder="Task Title" required style="width: 300px;"><br><br>
        <textarea name="description" placeholder="Instructions" required style="width: 300px; height: 80px;"></textarea><br>
        <button type="submit" class="btn">Add Task</button>
    </form>

    <br>

    <div style="background: #e8f6f3; padding: 15px; border: 1px solid #1abc9c;">
        <h4>📋 Pending Student Submissions</h4>
        
        <?php if(empty($submissions)): ?>
            <p>No pending submissions to review.</p>
        <?php else: ?>
            <table border="1" style="width:100%; background:white;">
                <tr>
                    <th>Student</th>
                    <th>Task</th>
                    <th>Submission</th>
                    <th>Action</th>
                </tr>
                <?php foreach($submissions as $sub): ?>
                <tr>
                    <td><?= $sub['student_name'] ?></td>
                    <td><?= $sub['task_title'] ?></td>
                    <td>
                        <a href="<?= $sub['file_url'] ?>" target="_blank">View File</a>
                    </td>
                    <td>
                        <form action="<?= BASE_URL ?>instructor/reviewTask" method="POST">
                            <input type="hidden" name="completion_id" value="<?= $sub['completion_id'] ?>">
                            <textarea name="feedback" placeholder="Feedback..." required></textarea><br>
                            <button type="submit" name="status" value="approved" style="background:green; color:white;">Approve</button>
                            <button type="submit" name="status" value="rejected" style="background:red; color:white;">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <hr>

    <!-- ================= Quiz Management (Gemini Fix Applied) ================= -->
    <h3>3. Quiz Management</h3>

    <?php if (!empty($questions)): ?>
        <p>
            This course already has an active quiz with 
            <strong><?= count($questions); ?></strong> question(s).
            You may add more questions below.
        </p>
    <?php else: ?>
        <p>
            No quiz exists yet. Add the first question to create the quiz.
        </p>
    <?php endif; ?>

    <!-- ================= Quiz Question Form ================= -->
    <form action="<?= BASE_URL ?>instructor/createQuiz" method="POST">
        <input type="hidden" name="course_id" value="<?= $course['id']; ?>">

        <input type="text" name="question_text" placeholder="Question" required><br>

        <input type="text" name="option_a" placeholder="Option A" required>
        <input type="text" name="option_b" placeholder="Option B" required><br>

        <input type="text" name="option_c" placeholder="Option C" required>
        <input type="text" name="option_d" placeholder="Option D" required><br>

        <input type="text" name="correct_option" placeholder="Correct (A, B, C, or D)" required><br>

        <button type="submit">
            <?= !empty($questions) ? 'Add Another Question' : 'Create Quiz & Add First Question'; ?>
        </button>
    </form>

    <hr>

    <!-- ================= Existing Quiz Questions ================= -->
    <h3>Current Quiz Questions</h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>Question</th>
            <th>Correct Option</th>
        </tr>

        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
                <tr>
                    <td><?= $q['question_text']; ?></td>
                    <td><?= $q['correct_option']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No quiz questions added yet.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
