<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Upload Materials for Course</h2>

    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; max-width: 500px;">
        <form action="<?= BASE_URL ?>instructor/uploadMaterial" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $data['course_id'] ?>">
            
            <label>Select File:</label><br>
            <input type="file" name="material" required style="margin: 10px 0;"><br>
            
            <button type="submit" class="btn">Add Material</button>
        </form>
        <br>
        <a href="javascript:history.back()">Cancel</a>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>