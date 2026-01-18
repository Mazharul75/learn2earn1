<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Course: <span style="color: #3498db;"><?= $data['course']['title']; ?></span></h2>
        <a href="<?= BASE_URL ?>dashboard/index" class="btn" style="background: #95a5a6; text-decoration: none; padding: 8px 15px; font-size: 0.9rem;">
            &larr; Dashboard
        </a>
    </div>

    <?php if($data['is_empty']): ?>
        <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 30px; text-align: center; border-radius: 8px;">
            <h3 style="margin-top: 0;">🚧 Content Coming Soon</h3>
            <p>The instructor hasn't added any tasks or materials to this course yet.</p>
            <p>Please check back later!</p>
        </div>
    <?php else: ?>

        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">1. Course Materials</h3>
        <ul style="list-style: none; padding: 0;">
            <?php if(!empty($data['materials'])): ?>
                <?php foreach($data['materials'] as $m): ?>
                    <li style="margin-bottom: 10px; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 5px; border-left: 4px solid #3498db;">
                        <a href="<?= BASE_URL ?>public/uploads/materials/<?= $m['file_name']; ?>" target="_blank" style="text-decoration: none; font-weight: 500; color: #2c3e50;">
                            📄 View Material: <span style="color: #3498db;"><?= $m['file_name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="color: #777;">No materials uploaded yet.</li>
            <?php endif; ?>
        </ul>

        <br>

        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">2. Learning Tasks</h3>
        <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
            <table border="0" style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #555;">Task</th>
                        <th style="padding: 15px; text-align: left; color: #555;">Instructions</th>
                        <th style="padding: 15px; text-align: left; color: #555;">Your Status</th>
                        <th style="padding: 15px; text-align: left; color: #555;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($data['tasks'])): ?>
                    <tr><td colspan="4" style="padding: 20px; text-align: center;">No tasks assigned.</td></tr>
                <?php else: ?>
                    <?php foreach($data['tasks'] as $task): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; font-weight: 600;"><?= $task['title']; ?></td>
                        <td style="padding: 15px; color: #666;"><?= $task['description']; ?></td>
                        <td style="padding: 15px;">
                            <span style="color: <?= $task['status_color'] ?>; font-weight: bold;">
                                <?= $task['status_label'] ?>
                            </span>
                            <?php if (!empty($task['instructor_feedback'])): ?>
                                <div style="margin-top: 5px; font-size: 0.85rem; background: #fff8e1; padding: 5px; border-radius: 4px;">
                                    <strong>💬 Feedback:</strong> <?= $task['instructor_feedback'] ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php if($task['is_uploadable']): ?>
                                <form action="<?= BASE_URL ?>learner/submitTask" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                                    <input type="file" name="task_file" required style="font-size: 0.8rem; width: 200px;"><br>
                                    <button type="submit" class="btn" style="font-size: 0.8rem; padding: 4px 10px; margin-top: 5px;">Submit Work</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999;">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <br><br>

        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">3. Materials Checklist</h3>
        <ul style="list-style: none; padding: 0;">
            <?php if(!empty($data['materials'])): ?>
                <?php foreach($data['materials'] as $m): ?>
                <?php $is_checked = in_array($m['id'], $data['checked_ids']); ?>
                
                <li style="margin-bottom: 12px; padding: 12px 15px; background: #fff; border: 1px solid <?= $is_checked ? '#c3e6cb' : '#ddd' ?>; border-left: 5px solid <?= $is_checked ? '#28a745' : '#ccc' ?>; display: flex; justify-content: space-between; align-items: center; border-radius: 4px; transition: all 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <span style="font-weight: 500; color: <?= $is_checked ? '#155724' : '#555' ?>;">
                        📄 <?= $m['file_name']; ?>
                    </span>

                    <?php if($is_checked): ?>
                        <div style="background-color: #d4edda; color: #155724; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                            <span>✔</span> Completed
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>learner/checkout/<?= $data['course_id'] ?>/<?= $m['id']; ?>" style="text-decoration: none;">
                            <div style="background: #f8f9fa; color: #6c757d; padding: 5px 15px; border-radius: 20px; border: 1px solid #ced4da; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                                ⬜ Mark as Done
                            </div>
                        </a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <br><hr><br>

        <div style="padding: 25px; border: 1px solid #ddd; border-radius: 8px; text-align: center; margin-bottom: 50px;">
            <?php if($data['is_completed']): ?>
                <div style="background: #e8f8f5; padding: 20px; border-radius: 8px; border: 1px solid #2ecc71;">
                    <h1 style="font-size: 3rem; margin: 0;">🏆</h1>
                    <h3 style="color: #27ae60;">Course Completed!</h3>
                    <p>Congratulations! You have passed the quiz and mastered this course.</p>
                    <button class="btn" disabled style="background: #27ae60; opacity: 1; cursor: default;">✅ Certificate Earned</button>
                </div>

            <?php elseif($data['allFinished']): ?>
                <?php if($data['has_quiz']): ?>
                    <div style="background: #d4edda; padding: 20px; border-radius: 8px; border: 1px solid #c3e6cb;">
                        <h3 style="color: #155724;">🎉 Ready for Final Assessment</h3>
                        <p>You have completed all materials and tasks.</p>
                        <a href="<?= BASE_URL ?>learner/takeQuiz/<?= $data['course_id'] ?>" class="btn" style="background: #27ae60; font-size: 1.1rem; padding: 12px 25px;">📝 Take Final Quiz</a>
                    </div>
                <?php else: ?>
                    <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border: 1px solid #ffeeba;">
                        <h3 style="color: #856404;">⏳ Work Complete</h3>
                        <p>However, the <strong>Final Quiz is not available yet</strong>. Please contact the instructor.</p>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="background: #f2f2f2; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #7f8c8d;">🔒 Quiz Locked</h3>
                    <p>Complete all tasks and check off all materials to unlock the final quiz.</p>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>