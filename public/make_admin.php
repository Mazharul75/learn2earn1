<?php
// STANDALONE SCRIPT - No external files required

// 1. Database Credentials (Standard XAMPP Defaults)
$host = 'localhost';
$dbname = 'learn2earn'; // Verify this is your exact DB name
$user = 'root';
$pass = '';

try {
    // 2. Connect Directly using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Define Admin Data
    $email = 'admin@learn2earn.com';
    $password = '123456'; 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Super Admin';

    // 4. Check if User Exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existing = $stmt->fetch();

    if ($existing) {
        // UPDATE existing user
        $sql = "UPDATE users SET password = :pass, role = 'admin' WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['pass' => $hashed_password, 'email' => $email]);
        echo "<h1>✅ Success!</h1><p>Existing User updated. Admin password reset to: <strong>123456</strong></p>";
    } else {
        // INSERT new user
        $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :pass, 'admin')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'email' => $email, 'pass' => $hashed_password]);
        echo "<h1>✅ Success!</h1><p>New Admin created with password: <strong>123456</strong></p>";
    }

    // 5. Add to Whitelist (Safe Insert)
    $stmt = $pdo->prepare("SELECT id FROM admin_invites WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if (!$stmt->fetch()) {
        $sql = "INSERT INTO admin_invites (email, invited_by) VALUES (:email, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        echo "<p>Admin email added to whitelist.</p>";
    }

    echo "<br><a href='../auth/login'>Go to Login</a>";

} catch (PDOException $e) {
    die("<h1>❌ Database Error</h1><p>" . $e->getMessage() . "</p><p>Check if your database name is actually 'learn2earn'</p>");
}
?>
