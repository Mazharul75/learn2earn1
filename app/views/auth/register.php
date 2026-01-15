<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<form method="POST" action="<?= BASE_URL ?>/auth/register">
    <input type="text" name="name" placeholder="Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <select name="role" required>
        <option value="">Select Role</option>
        <option value="learner">Learner</option>
        <option value="instructor">Instructor</option>
        <option value="client">Client</option>
    </select><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>
