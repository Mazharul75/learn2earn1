<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Post a Skill-Based Job</h2>
    <form action="<?= BASE_URL ?>client/post" method="POST">
        <input type="text" name="title" placeholder="Job Title" required><br><br>
        <textarea name="description" placeholder="Job Requirements"></textarea><br><br>
        <label>Requirement: Select Course to be completed</label>
        <select name="required_course_id">
            <option value="">No Requirement</option>
            <?php foreach($courses as $course): ?>
                <option value="<?= $course['id']; ?>"><?= $course['title']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Post Job</button>
    </form>
    <br>
    <a href="<?= BASE_URL ?>client/index">Back to Dashboard</a>
    <script>
    function validateJobForm() {
        let title = document.forms["jobForm"]["title"].value;
        let desc = document.forms["jobForm"]["description"].value;

        if (title.length < 5) {
            alert("Job title must be at least 5 characters long.");
            return false;
        }
        if (desc == "") {
            alert("Description cannot be empty.");
            return false;
        }
        return true;
    }
    </script>

    <form name="jobForm" action="<?= BASE_URL ?>client/post" method="POST" onsubmit="return validateJobForm()"></form>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>