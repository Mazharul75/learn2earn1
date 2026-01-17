<!DOCTYPE html>
<html>
<head>
    <title>Search Courses</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Search and Enroll</h2>
    
    <input type="text" id="courseSearch" placeholder="Search by title..." onkeyup="searchCourses()">
    <br><br>

    <table border="1" id="courseTable">
        <thead>
            <tr>
                <th>Title</th>
                <th>Difficulty</th>
                <th>Action</th>
            </tr>
        </thead>
            <tbody id="courseResults">
            <?php foreach($allCourses as $course) : ?>
            <?php 
                // LOGIC: Calculate seats available to the public
                $public_capacity = $course['max_capacity'] - $course['reserved_seats'];
                $seats_taken = $course['student_count'];
                $seats_left = $public_capacity - $seats_taken;
                
                // Ensure we don't show negative numbers if reserved seats overlap
                if ($seats_left < 0) $seats_left = 0;
            ?>
            <tr>
                <td><?= $course['title']; ?></td>
                <td><?= $course['difficulty']; ?></td>
                
                <td>
                    <span style="color: #2980b9; font-weight: bold;">
                        <?= $course['student_count']; ?> Students Enrolled
                    </span>
                    <br>
                    <?php if($seats_left > 0): ?>
                        <span style="color: #27ae60;">
                            ✅ <?= $seats_left ?> Seats Available
                        </span>
                    <?php else: ?>
                        <span style="color: #c0392b; font-weight: bold;">
                            ⛔️ Full (Waitlist Only)
                        </span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if($seats_left > 0): ?>
                        <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn">Enroll</a>
                    <?php else: ?>
                        <button class="btn" style="background:gray; cursor:not-allowed;" disabled>Full</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    
    </script>
</body>
</html>




<!DOCTYPE html>
<html>
<head>
    <title>Search Courses</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Search and Enroll</h2>
    
    <input type="text" id="courseSearch" placeholder="Search by title..." onkeyup="searchCourses()">
    <br><br>

    <table border="1" id="courseTable">
        <thead>
            <tr>
                <th>Title</th>
                <th>Difficulty</th>
                <th>Availability</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="courseResults">
            <?php foreach($allCourses as $course) : ?>
            <?php 
                // 1. Calculate Public Availability
                $public_capacity = $course['max_capacity'] - $course['reserved_seats'];
                $seats_taken = $course['student_count'];
                
                $public_seats_left = $public_capacity - $seats_taken;
                
                // 2. Calculate TOTAL Availability (Including Reserved)
                $total_seats_left = $course['max_capacity'] - $seats_taken;
            ?>
            <tr>
                <td><?= $course['title']; ?></td>
                <td><?= $course['difficulty']; ?></td>
                
                <td>
                    <span style="color: #2980b9; font-weight: bold;">
                        <?= $course['student_count']; ?> Students Enrolled
                    </span>
                    <br>
                    
                    <?php if($public_seats_left > 0): ?>
                        <span style="color: #27ae60;">
                            ✅ <?= $public_seats_left ?> Public Seats Available
                        </span>
                    <?php elseif($total_seats_left > 0): ?>
                        <span style="color: #f39c12; font-weight: bold;">
                            ⚠️ Public Full (Reserved Seats Only)
                        </span>
                    <?php else: ?>
                        <span style="color: #c0392b; font-weight: bold;">
                            ⛔️ Completely Full
                        </span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if($public_seats_left > 0): ?>
                        <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn">Enroll</a>
                    
                    <?php elseif($total_seats_left > 0): ?>
                        <a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>" class="btn" style="background: #e67e22;">Request Seat</a>
                    
                    <?php else: ?>
                        <button class="btn" style="background:gray; cursor:not-allowed;" disabled>Full</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    

    function searchCourses() {
        let query = document.getElementById('courseSearch').value;
        // ERROR FIX: Changed '&' to '?'
        let url = '<?= BASE_URL ?>learner/search?query=' + query;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                let tbody = document.getElementById('courseResults');
                tbody.innerHTML = ''; 

                // FIX: Check if data is empty to avoid crashes
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3">No courses found.</td></tr>';
                    return;
                }

                data.forEach(course => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${course.title}</td>
                            <td>${course.difficulty}</td>
                            <td><a href="<?= BASE_URL ?>learner/enroll/${course.id}">Enroll</a></td>
                        </tr>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }


    </script>
</body>
</html>
