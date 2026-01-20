<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 700px; margin: 30px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #2c3e50;">Account Management</h2>
        </div>
        
        <?php if(isset($data['error'])): ?>
            <div style="color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <?= $data['error'] ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/updateProfile" method="POST">
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 25px;">
                <h3 style="margin-top: 0; color: #34495e; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">
                    1. Personal Details
                </h3>
                
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Full Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px;">
                
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Email Address:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            
            <div style="background: #fff8e1; padding: 20px; border-radius: 8px; border: 1px solid #ffeeba; margin-bottom: 25px;">
                <h3 style="margin-top: 0; color: #b7791f; border-bottom: 2px solid #fae5d3; padding-bottom: 10px; margin-bottom: 15px;">
                    2. Security & Confirmation
                </h3>
                
                <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #d35400;">Current Password (Required to save):</label>
                <input type="password" name="current_password" placeholder="Enter your current password" required
                       style="width: 100%; padding: 10px; border: 1px solid #d35400; border-radius: 5px; margin-bottom: 20px;">

                <label style="font-weight: bold; display: block; margin-bottom: 5px;">New Password (Optional):</label>
                <small style="color: #666; display: block; margin-bottom: 5px;">Leave blank if you don't want to change it.</small>
                <input type="password" name="new_password" placeholder="New Password"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            
            <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 1.1rem; background: #27ae60;">Save Changes</button>
        </form>
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #eee;">
            <h3 style="color: #c0392b; margin-top: 0;">⚠️ Danger Zone</h3>
            <p style="color: #666; font-size: 0.9rem;">
                Once you delete your account, there is no going back. All your data will be permanently removed.
            </p>
            
            <form action="<?= BASE_URL ?>auth/deleteAccount" method="POST" onsubmit="return confirm('ARE YOU SURE?\n\nThis will permanently delete your account, courses, and job applications.\nThis action CANNOT be undone.');">
                <button type="submit" class="btn" style="background: #c0392b; color: white; width: 100%; padding: 12px; font-weight: bold;">
                    🗑️ Permanently Delete My Account
                </button>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>