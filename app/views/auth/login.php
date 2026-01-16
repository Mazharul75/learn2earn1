<!DOCTYPE html>
<html>
<head>
    <title>Login - Learn2Earn</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Login</h2>
    <form action="<?php echo BASE_URL; ?>/auth/login" method="POST">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="<?php echo BASE_URL; ?>auth/register">Register here</a></p>
</body>
</html>