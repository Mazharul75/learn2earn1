<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <nav>
        <a href="<?= BASE_URL ?>auth/profile">Manage Profile</a> | 
        <a href="<?= BASE_URL ?>auth/logout">Logout</a>
    </nav>
    <h1>Welcome, <?= $_SESSION['user_name']; ?> (Client)</h1>
    <nav>
        <a href="<?= BASE_URL ?>client/post">Post New Job</a> | 
        <a href="<?= BASE_URL ?>auth/logout">Logout</a>
    </nav>

    <h3>My Posted Jobs</h3>
    <table border="1">
        <tr>
            <th>Job Title</th>
            <th>Action</th>
        </tr>
        <?php if(!empty($jobs)): ?>
            <?php foreach($jobs as $job) : ?>
            <tr>
                <td><?= $job['title']; ?></td>
                <td>
                    <a href="<?= BASE_URL ?>client/applicants/<?= $job['id']; ?>">View & Select Applicants</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No jobs posted yet.</td></tr>
        <?php endif; ?>
    </table>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>