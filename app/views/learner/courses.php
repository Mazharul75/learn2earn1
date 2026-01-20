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
        var query = document.getElementById('courseSearch').value;
        var url = '<?= BASE_URL ?>learner/search?query=' + query;

        
        var xhr = new XMLHttpRequest();

        // 2. Define what happens when the server responds
        xhr.onreadystatechange = function() {
            // readyState 4 means "Done", status 200 means "OK"
            if (this.readyState == 4 && this.status == 200) {
                try {
                    // 3. Parse the JSON response
                    var data = JSON.parse(this.responseText);
                    var tbody = document.getElementById('courseResults');
                    tbody.innerHTML = ''; 

                    // Handle No Results
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center;">No courses found.</td></tr>';
                        return;
                    }

                    // 4. Loop through data (Classic For Loop or forEach)
                    data.forEach(function(course) {
                        
                        // Parse Numbers
                        var max = parseInt(course.max_capacity);
                        var reserved = parseInt(course.reserved_seats);
                        var taken = parseInt(course.student_count) || 0;

                        // Math Logic
                        var public_left = max - reserved - taken;
                        var total_left = max - taken;

                        var availabilityHtml = '';
                        var actionHtml = '';

                        // Logic deciding what buttons to show
                        if (public_left > 0) {
                            availabilityHtml = '<div style="margin-bottom: 5px;"><strong>' + taken + '</strong> Enrolled</div>' +
                                               '<span style="color: #27ae60; font-size: 0.85rem;">✅ ' + public_left + ' Public Seats</span>';
                            actionHtml = '<a href="<?= BASE_URL ?>learner/enroll/' + course.id + '" class="btn" style="background: #2ecc71;">Enroll</a>';
                        
                        } else if (total_left > 0) {
                            availabilityHtml = '<div style="margin-bottom: 5px;"><strong>' + taken + '</strong> Enrolled</div>' +
                                               '<span style="color: #e67e22; font-size: 0.85rem; font-weight: bold;">⚠️ Reserved Only</span>';
                            actionHtml = '<a href="<?= BASE_URL ?>learner/enroll/' + course.id + '" class="btn" style="background: #e67e22;">Request Seat</a>';
                        
                        } else {
                            availabilityHtml = '<div style="margin-bottom: 5px;"><strong>' + taken + '</strong> Enrolled</div>' +
                                               '<span style="color: #c0392b; font-size: 0.85rem; font-weight: bold;">⛔️ Full</span>';
                            actionHtml = '<button class="btn" style="background:gray; cursor:not-allowed;" disabled>Full</button>';
                        }

                        // Append Row
                        var row = '<tr style="border-bottom: 1px solid #eee;">' +
                                    '<td style="padding: 15px; font-weight: 600;">' + course.title + '</td>' +
                                    '<td style="padding: 15px;">' + course.difficulty + '</td>' +
                                    '<td style="padding: 15px;">' + availabilityHtml + '</td>' +
                                    '<td style="padding: 15px; text-align: center;">' + actionHtml + '</td>' +
                                  '</tr>';
                        
                        tbody.innerHTML += row;
                    });

                } catch (error) {
                    console.error("Invalid JSON:", error);
                }
            }
        };

        // 5. Open and Send request
        xhr.open("GET", url, true);
        xhr.send();
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>