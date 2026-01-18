<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #2c3e50;">Post a Skill-Based Job</h2>
        <p style="color: #666; margin-bottom: 20px;">Define your requirements and find top talent.</p>

        <form name="jobForm" action="<?= BASE_URL ?>client/post" method="POST" onsubmit="return validateJobForm()">
            
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Job Title:</label>
            <input type="text" name="title" placeholder="e.g. Junior Web Developer" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">

            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Job Requirements / Description:</label>
            <textarea name="description" placeholder="Describe the role..." required 
                      style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;"></textarea>

            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Requirement: Must Complete Course (Optional):</label>
            <select name="required_course_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
                <option value="">-- No Specific Course Required --</option>
                <?php foreach($courses as $course): ?>
                    <option value="<?= $course['id']; ?>"><?= $course['title']; ?></option>
                <?php endforeach; ?>
            </select>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="background: #e74c3c; flex: 1;">Post Job</button>
                <a href="<?= BASE_URL ?>client/index" class="btn" style="background: #95a5a6; text-decoration: none; flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    function validateJobForm() {
        let title = document.forms["jobForm"]["title"].value;
        let desc = document.forms["jobForm"]["description"].value;

        if (title.length < 5) {
            alert("Job title must be at least 5 characters long.");
            return false;
        }
        if (desc.trim() == "") {
            alert("Description cannot be empty.");
            return false;
        }
        return true;
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>