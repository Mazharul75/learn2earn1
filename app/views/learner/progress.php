<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Course: <?= $data['course']['title']; ?></h2>

    <?php if($data['is_empty']): ?>
        
        <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 20px; text-align: center; margin-top: 20px;">
            <h3>🚧 Content Coming Soon</h3>
            <p>The instructor hasn't added any tasks or materials to this course yet.</p>
            <p>Please check back later!</p>
        </div>

    <?php else: ?>
        <h3>1. Course Materials</h3>
        <ul>
            <?php if(!empty($data['materials'])): ?>
                <?php foreach($data['materials'] as $m): ?>
                    <li>
                        <a href="<?= BASE_URL ?>public/uploads/materials/<?= $m['file_name']; ?>" target="_blank">
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
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Instructions</th>
                    <th>Your Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['tasks'])): ?>
                    <tr><td colspan="4">No tasks assigned.</td></tr>
                <?php else: ?>
                    <?php foreach($data['tasks'] as $task): ?>
                    <tr>
                        <td><?= $task['title']; ?></td>
                        <td><?= $task['description']; ?></td>
                        <td>
                            <span style="color: <?= $task['status_color'] ?>; font-weight: bold;">
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
                                    <button type="submit" class="btn" style="font-size:12px; margin-top:5px;">Submit Work</button>
                                </form>
                            <?php else: ?>
                                <span>--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <hr>

        <h3>Materials Checklist</h3>
        <ul style="list-style: none; padding: 0;">
            <?php if(!empty($data['materials'])): ?>
                <?php foreach($data['materials'] as $m): ?>
                
                <?php 
                    // Check if this material ID is in the "checked_ids" list we fetched
                    $is_checked = in_array($m['id'], $data['checked_ids']); 
                ?>

                <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; border-radius: 5px;">
                    <span>📄 <?= $m['file_name']; ?></span>

                    <?php if($is_checked): ?>
                        
                        <button class="btn" disabled style="background: #27ae60; color: white; cursor: default; border: none; padding: 5px 15px; border-radius: 4px;">
                            ✅ Checked
                        </button>
                    
                    <?php else: ?>
                        
                        <a href="<?= BASE_URL ?>learner/checkout/<?= $m['id']; ?>" style="text-decoration: none;">
                            <div style="background: #ecf0f1; color: #7f8c8d; padding: 5px 15px; border-radius: 4px; border: 1px solid #bdc3c7;">
                                ⬜️ Mark as Done
                            </div>
                        </a>

                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No items to check out.</li>
            <?php endif; ?>
        </ul>

        
        <hr>

        <div style="margin-top: 30px; margin-bottom: 50px; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
            
            <?php if($data['is_completed']): ?>
                <div style="background: #e8f8f5; padding: 20px; border-radius: 5px; color: #27ae60; text-align: center; border: 1px solid #2ecc71;">
                    <h1 style="font-size: 3rem; margin: 0;">🏆</h1>
                    <h3 style="margin-top: 10px;">Course Completed!</h3>
                    <p>Congratulations! You have successfully passed the final quiz.</p>
                    <p>You have mastered <strong><?= $data['course']['title'] ?></strong>.</p>
                    
                    <button class="btn" disabled style="background: #27ae60; cursor: default; opacity: 1;">✅ Certificate Earned</button>
                </div>

            <?php elseif($data['allFinished']): ?>
                
                <?php if($data['has_quiz']): ?>
                    <div style="background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;">
                        <h4>🎉 All materials completed!</h4>
                        <p>You can now take the quiz to finish the course.</p>
                        <a href="<?= BASE_URL ?>learner/takeQuiz/<?= $data['course_id'] ?>" class="btn" style="background: #27ae60;">Take Course Quiz</a>
                    </div>
                
                <?php else: ?>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;">
                        <h4>⏳ Course Work Complete</h4>
                        <p>However, the <strong>Final Quiz is not available yet</strong>.</p>
                        <p>Please contact your instructor to add the quiz.</p>
                        <button class="btn" disabled style="background: gray; cursor: not-allowed;">Quiz Not Ready</button>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p style="color: #c0392b; font-weight: bold;">🔒 Quiz Locked</p>
                <p>Please complete all tasks and checkout all materials to unlock the final quiz.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?> <?php require_once __DIR__ . '/../layouts/footer.php'; ?>