<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Class List: <span style="color: #3498db;"><?= $course['title']; ?></span></h2>
        <a href="<?= BASE_URL ?>instructor/index" class="btn" style="background: #95a5a6; text-decoration: none;">&larr; Dashboard</a>
    </div>
    
    <input type="text" id="studentSearch" placeholder="🔍 Search student by name or email..." onkeyup="searchStudents()"
           style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #555;">Student Name</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Email</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Progress</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Date Enrolled</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php if(!empty($students)): ?>
                    <?php foreach($students as $student): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; font-weight: 500;"><?= $student['name']; ?></td>
                        <td style="padding: 15px; color: #666;"><?= $student['email']; ?></td>
                        <td style="padding: 15px;">
                            <?php if($student['progress'] == 100): ?>
                                <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">✅ Completed</span>
                            <?php else: ?>
                                <span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⏳ In Progress</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; color: #7f8c8d;"><?= date('M d, Y', strtotime($student['enrolled_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="padding: 40px; text-align: center; color: #7f8c8d;">No students yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    function searchStudents() {
        var query = document.getElementById('studentSearch').value;
        var courseId = <?= $course['id'] ?>;
        var url = '<?= BASE_URL ?>instructor/searchStudentsApi?course_id=' + courseId + '&query=' + query;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                try {
                    var data = JSON.parse(this.responseText);
                    var tbody = document.getElementById('studentTableBody');
                    tbody.innerHTML = ''; 

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center;">No students found.</td></tr>';
                        return;
                    }

                    // Loop through JSON data
                    for (var i = 0; i < data.length; i++) {
                        var s = data[i];
                        
                        // Badge Logic
                        var badge = (s.progress == 100) 
                            ? '<span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">✅ Completed</span>'
                            : '<span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⏳ In Progress</span>';

                        var row = '<tr style="border-bottom: 1px solid #eee;">' +
                                    '<td style="padding: 15px; font-weight: 500;">' + s.name + '</td>' +
                                    '<td style="padding: 15px; color: #666;">' + s.email + '</td>' +
                                    '<td style="padding: 15px;">' + badge + '</td>' +
                                    '<td style="padding: 15px; color: #7f8c8d;">' + s.enrolled_at + '</td>' +
                                  '</tr>';
                        tbody.innerHTML += row;
                    }
                } catch (error) {
                    console.error("Invalid JSON", error);
                }
            }
        };
        xhr.open("GET", url, true);
        xhr.send();
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>