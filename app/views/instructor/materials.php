<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<h2>Upload Materials for Course</h2>
<form action="<?= BASE_URL ?>instructor/uploadMaterial" method="POST">
    <input type="hidden" name="course_id" value="<?= $data['course_id'] ?>">
    <input type="text" name="title" placeholder="Material Title (e.g. Lecture 1)" required><br><br>
    <input type="text" name="file_path" placeholder="URL or File Name" required><br><br>
    <button type="submit">Add Material</button>
</form>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>