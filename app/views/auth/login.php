<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Login</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>auth/login" method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"><br>

        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required><br>

        <button type="submit">Login</button>
    </form>
 
    <p>Don't have an account? <a href="<?= BASE_URL ?>auth/register">Register here</a></p>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>