<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>👑 Admin Dashboard</h2>

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px;">
            <div class="alert alert-success" style="background: #e8f8f5; border: 1px solid #2ecc71; color: #27ae60;">
                <h3>✉️ Refer New Admin</h3>
                <p>Add an email here. That person will then be allowed to register as an Admin.</p>
                
                <form action="<?= BASE_URL ?>admin/invite" method="POST" style="background:none; border:none; box-shadow:none; padding:0;">
                    <input type="email" name="email" placeholder="Enter Email to Whitelist" required>
                    <button type="submit" class="btn">Authorize Email</button>
                </form>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3>📊 Quick Stats</h3>
            <p><strong>Total Users:</strong> <?= count($users) ?></p>
            <p>Manage your platform users below.</p>
        </div>
    </div>

    <hr>

    <h3>👥 User Management</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['name'] ?></td>
                <td><?= $user['email'] ?></td>
                <td>
                    <span class="role-badge badge-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
                </td>
                <td>
                    <?php if($user['role'] != 'admin' || $user['id'] != $_SESSION['user_id']): ?>
                        <a href="<?= BASE_URL ?>admin/deleteUser/<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');" style="padding: 5px 10px; font-size: 0.8rem;">Delete</a>
                    <?php else: ?>
                        <span style="color: gray;">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
