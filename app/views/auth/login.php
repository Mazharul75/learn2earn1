<!DOCTYPE html>
<html>
<head>
    <title>Login - Learn2Earn</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
    <style>
        /* Add alert style locally if not in style.css yet */
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .alert-danger { background: #fce4e4; color: #c0392b; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
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
</body>
</html>