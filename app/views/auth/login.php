<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="max-width: 400px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Welcome Back</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px; font-size: 0.9rem; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/login" method="POST">
            <label style="font-weight: bold; display: block; margin-bottom: 8px; color: #555;">Email Address</label>
            <input type="email" name="email" placeholder="e.g. user@example.com" required 
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                   style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">

            <label style="font-weight: bold; display: block; margin-bottom: 8px; color: #555;">Password</label>
            <input type="password" name="password" placeholder="Enter your password" required
                   style="width: 100%; padding: 12px; margin-bottom: 25px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">

            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 12px; background: #3498db;">Login</button>
        </form>
     
        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">
        
        <p style="text-align: center; color: #666;">
            Don't have an account? <br>
            <a href="<?= BASE_URL ?>auth/register" style="color: #27ae60; font-weight: bold; text-decoration: none;">Create an Account</a>
        </p>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>