<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <nav>
        <a href="<?= BASE_URL ?>auth/profile">Manage Profile</a> | 
        <a href="<?= BASE_URL ?>auth/logout">Logout</a>
    </nav>
    <h1>Welcome, <?= $_SESSION['user_name']; ?></h1>
    <nav>
        <a href="<?= BASE_URL ?>instructor/create">Create New Course</a> |
        <a href="<?= BASE_URL ?>instructor/viewJobs">Recommend Students for Jobs</a> |
        <a href="<?= BASE_URL ?>auth/logout">Logout</a>
    </nav>

    <h3>Your Courses</h3>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Difficulty</th>
            <th>Description</th>
            <th>View enrolled Students</th>
        </tr>
        <?php if(!empty($courses)): ?>
            <?php foreach($courses as $course) : ?>
            <tr>
                <td><?= $course['title']; ?></td>
                <td><?= $course['difficulty']; ?></td>
                <td>
                    <a href="<?= BASE_URL ?>instructor/manage/<?= $course['id']; ?>">Manage & Upload</a>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>instructor/students/<?= $course['id']; ?>">View Students</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No courses created yet.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>