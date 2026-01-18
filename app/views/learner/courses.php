<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Search and Enroll</h2>
    </div>
    
    <div style="margin-bottom: 20px;">
        <input type="text" id="courseSearch" placeholder="🔍 Search courses by title..." onkeyup="searchCourses()" 
        style="width: 100%; padding: 12px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px;">
    </div>

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
                <?php 
                    // 1. Calculate Seats
                    $public_capacity = $course['max_capacity'] - $course['reserved_seats'];
                    $seats_taken = $course['student_count'];
                    $public_seats_left = $public_capacity - $seats_taken;
                    $total_seats_left = $course['max_capacity'] - $seats_taken;
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 600;"><?= $course['title']; ?></td>
                    <td style="padding: 15px;"><?= $course['difficulty']; ?></td>
                    
                    <td style="padding: 15px;">
                        <div style="margin-bottom: 5px;">
                            <strong><?= $course['student_count']; ?></strong> Enrolled
                        </div>
                        
                        <?php if($public_seats_left > 0): ?>
                            <span style="color: #27ae60; font-size: 0.85rem;">
                                ✅ <?= $public_seats_left ?> Public Seats
                            </span>
                        <?php elseif($total_seats_left > 0): ?>
                            <span style="color: #e67e22; font-size: 0.85rem; font-weight: bold;">
                                ⚠️ Public Full (Reserved Only)
                            </span>
                        <?php else: ?>
                            <span style="color: #c0392b; font-size: 0.85rem; font-weight: bold;">
                                ⛔️ Completely Full
                            </span>
                        <?php endif; ?>
                    </td>

                    <td style="padding: 15px; text-align: center;">
                        <?php if($public_seats_left > 0): ?>
                            <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn" style="background: #2ecc71;">Enroll Now</a>
                        
                        <?php elseif($total_seats_left > 0): ?>
                            <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn" style="background: #e67e22;">Request Seat</a>
                        
                        <?php else: ?>
                            <button class="btn" style="background: #95a5a6; cursor: not-allowed;" disabled>Full</button>
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