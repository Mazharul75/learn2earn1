<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Apply for: <?= $job['title'] ?></h2>
    <p>Please upload your CV (PDF only) to complete your application.</p>
    
    <form action="<?= BASE_URL ?>learner/submitApplication" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
        
        <label>Upload CV (PDF):</label><br>
        <input type="file" name="cv" accept=".pdf" required><br><br>
        
        <button type="submit" class="btn">Submit Application</button>
    </form>
    <br>
    <a href="<?= BASE_URL ?>learner/jobs">Cancel</a>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>