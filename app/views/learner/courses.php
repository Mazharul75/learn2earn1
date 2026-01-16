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
            <tr>
                <td><?= $course['title']; ?></td>
                <td><?= $course['difficulty']; ?></td>
                <td><a href="<?= BASE_URL ?>learner/enroll/<?= $course['id']; ?>">Enroll</a></td>
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