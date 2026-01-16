<!DOCTYPE html>
<html>
<head>
    <title>Create Course</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Add New Course</h2>
    <form action="<?= BASE_URL ?>instructor/create" method="POST">
        <input type="text" name="title" placeholder="Course Title" required><br><br>
        <textarea name="description" placeholder="Course Description"></textarea><br><br>
        <select name="difficulty" required>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
        </select><br><br>
        <button type="submit">Create Course</button>
    </form>
    <br>
    <a href="<?= BASE_URL ?>instructor/index">Back to Dashboard</a>
</body>
</html>