<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Search and Enroll</h2>
    </div>
    
    <input type="text" id="courseSearch" placeholder="🔍 Search courses..." onkeyup="searchCourses()" 
           style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;" id="courseTable">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #555;">Title</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Level</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Availability</th>
                    <th style="padding: 15px; text-align: center; color: #555;">Action</th>
                </tr>
            </thead>
            <tbody id="courseResults">
                <?php foreach($allCourses as $course) : ?>
                <tr style="border-bottom: 1px solid #eee;">
                    
                    <td style="padding: 15px; font-weight: 600;"><?= $course['title']; ?></td>
                    
                    <td style="padding: 15px;">
                        <span style="background: <?= $course['ui_badge_color']; ?>; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.85rem;">
                            <?= $course['difficulty']; ?>
                        </span>
                    </td>
                    
                    <td style="padding: 15px;">
                        <div style="margin-bottom: 5px;">
                            <strong><?= $course['student_count']; ?></strong> Enrolled
                        </div>
                        
                        <?php if($course['ui_is_totally_full']): ?>
                            <span style="color: #c0392b; font-weight: bold;">⛔️ Full</span>
                        
                        <?php elseif($course['ui_is_public_full']): ?>
                            <span style="color: #e67e22; font-weight: bold;">⚠️ Reserved Only</span>
                        
                        <?php else: ?>
                            <span style="color: #27ae60;">
                                ✅ <?= $course['ui_public_seats'] ?> Seats Left
                            </span>
                        <?php endif; ?>
                    </td>

                    <td style="padding: 15px; text-align: center;">
                        <?php if($course['ui_is_totally_full']): ?>
                            <button class="btn" style="background:gray; cursor:not-allowed;" disabled>Full</button>
                        
                        <?php elseif($course['ui_is_public_full']): ?>
                            <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn" style="background: #e67e22;">Request Seat</a>
                        
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn" style="background: #2ecc71;">Enroll</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    function searchCourses() {
        let query = document.getElementById('courseSearch').value;
        let url = '<?= BASE_URL ?>learner/search?query=' + query;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                let tbody = document.getElementById('courseResults');
                tbody.innerHTML = ''; 

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center;">No courses found matching that title.</td></tr>';
                    return;
                }

                data.forEach(course => {
                    // Re-calculate basic availability logic for JS (Simplified)
                    let statusBadge = `<span style="color:#2980b9;">Check details to enroll</span>`;
                    
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px; font-weight: 600;">${course.title}</td>
                            <td style="padding: 15px;">${course.difficulty}</td>
                            <td style="padding: 15px;">${statusBadge}</td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="<?= BASE_URL ?>learner/enroll/${course.id}" class="btn" style="background: #3498db;">View & Enroll</a>
                            </td>
                        </tr>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>