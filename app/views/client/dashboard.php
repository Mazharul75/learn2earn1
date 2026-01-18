<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="color: #2c3e50;">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?> <span style="font-size: 0.6em; color: #7f8c8d;">(Client)</span></h1>
    </div>

    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; border-left: 5px solid #e74c3c;">
        <a href="<?= BASE_URL ?>client/post" class="btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px; background: #e74c3c;">
            <span>📢</span> Post New Job
        </a>
    </div>

    <h3 style="color: #34495e; margin-bottom: 15px;">My Posted Jobs</h3>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #555;">Job Title</th>
                    <th style="padding: 15px; text-align: center; color: #555;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($jobs)): ?>
                <?php foreach($jobs as $job) : ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 600; color: #2c3e50;"><?= $job['title']; ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="<?= BASE_URL ?>client/applicants/<?= $job['id']; ?>" class="btn" style="padding: 6px 15px; font-size: 0.85rem; background: #3498db; text-decoration: none;">
                            📄 View & Select Applicants
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2" style="padding: 40px; text-align: center; color: #7f8c8d;">No jobs posted yet. Click "Post New Job" to find talent!</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>