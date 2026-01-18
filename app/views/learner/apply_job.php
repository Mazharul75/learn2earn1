<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #2c3e50;">Apply for: <span style="color: #9b59b6;"><?= $job['title'] ?></span></h2>
        <p style="color: #666; margin-bottom: 20px;">Please upload your CV (PDF only) to complete your application.</p>
        
        <form action="<?= BASE_URL ?>learner/submitApplication" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
            
            <label style="font-weight: bold; display: block; margin-bottom: 10px;">Upload CV (PDF):</label>
            <input type="file" name="cv" accept=".pdf" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="background: #27ae60; flex: 1;">Submit Application</button>
                <a href="<?= BASE_URL ?>learner/jobs" class="btn" style="background: #95a5a6; text-decoration: none; flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>