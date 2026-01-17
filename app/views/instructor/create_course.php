<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Add New Course</h2>
    <form action="<?= BASE_URL ?>instructor/create" method="POST">
        <input type="text" name="title" placeholder="Course Title" required><br><br>
        <textarea name="description" placeholder="Course Description"></textarea><br><br>
        <select name="difficulty" required>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
        </select><br><br>

        <label>Maximum Class Size:</label>
        <input type="number" name="max_capacity" placeholder="e.g. 50" required min="1"><br>

        <label>Reserved Seats (For VIPs/Manual Add):</label>
        <input type="number" name="reserved_seats" placeholder="e.g. 5" required min="0"><br><br>

        <button type="submit">Create Course</button>

    </form>
    <br>
    <a href="<?= BASE_URL ?>instructor/index">Back to Dashboard</a>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>