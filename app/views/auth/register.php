<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>
    <h2>Create Account</h2>
    <form action="<?php echo BASE_URL; ?>auth/register" method="POST">
        <input type="text" name="name" placeholder="Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <select name="role" required>
            <option value="learner">Learner</option>
            <option value="instructor">Instructor</option>
            <option value="client">Client</option>
        </select><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>