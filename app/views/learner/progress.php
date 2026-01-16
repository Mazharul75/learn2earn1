<!DOCTYPE html>
<html>
<head>
    <title>Learning Progress</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Course: <?= $course['title']; ?></h2>

    <h3>1. Course Materials</h3>
    <ul>
        <?php if(!empty($materials)): ?>
            <?php foreach($materials as $m): ?>
                <li>
                    <a href="<?= BASE_URL ?>public/uploads/<?= $m['file_name']; ?>" target="_blank">
                        View Material: <?= $m['file_name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No materials uploaded yet.</li>
        <?php endif; ?>
    </ul>
<br>
<h3>2. Learning Tasks</h3>
    <table border="1">
        <tr>
            <th>Task</th>
            <th>Instructions</th>
            <th>Your Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($tasks as $task): ?>
        <tr>
            <td><?= $task['title']; ?></td>
            <td><?= $task['description']; ?></td>
            <td>
                <span style="color: <?= $task['status_color'] ?>">
                    <?= $task['status_label'] ?>
                </span>
                
                <?php if (!empty($task['instructor_feedback'])): ?>
                    <br><small><strong>Feedback:</strong> <?= $task['instructor_feedback'] ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php if($task['is_uploadable']): ?>
                    <form action="<?= BASE_URL ?>learner/submitTask" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                        <input type="file" name="task_file" required>
                        <button type="submit" class="btn" style="font-size:12px;">Submit Work</button>
                    </form>
                <?php else: ?>
                    <span>--</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <h3>Materials Checklist</h3>
    <ul>
        <?php foreach($materials as $m): ?>
        <li>
            <?= $m['file_name']; ?> 
            <a href="<?= BASE_URL ?>learner/checkout/<?= $m['id']; ?>" class="btn">Checkout</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <hr>

    <?php if($allFinished): ?>
        <div style="background: #d4edda; padding: 20px;">
            <h4>🎉 All materials completed!</h4>
            <p>You can now take the quiz to finish the course.</p>
            <a href="<?= BASE_URL ?>learner/takeQuiz/<?= $course_id ?>" class="btn">Take Course Quiz</a>
        </div>
    <?php else: ?>
        <p style="color: red;">Please checkout all materials to unlock the quiz.</p>
    <?php endif; ?>
     
    <br>
    <a href="<?= BASE_URL ?>dashboard/index">Back to Dashboard</a>
</body>
</html>