<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <h2>Account Management</h2>
    
    <?php if(isset($data['error'])): ?>
        <div style="color: red; background: #fce4e4; padding: 10px; margin-bottom: 10px;">
            <?= $data['error'] ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>auth/updateProfile" method="POST">
        <h3>1. Personal Details</h3>
        <label>Full Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
        
        <label>Email Address:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>
        
        <hr>
        <h3>2. Security Confirmation</h3>
        <p>Enter your <strong>Current Password</strong> to save <em>any</em> changes.</p>
        <input type="password" name="current_password" placeholder="Current Password" required><br><br>

        <label>New Password (Optional):</label><br>
        <small>Leave blank if you don't want to change it.</small><br>
        <input type="password" name="new_password" placeholder="New Password"><br><br>
        
        <button type="submit" class="btn">Save Changes</button>
    </form>
    <br>
    <a href="<?= BASE_URL ?>dashboard/index">Back to Dashboard</a>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>