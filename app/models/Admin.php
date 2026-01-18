<?php
require_once __DIR__ . '/../../config/database.php';

class Admin {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function isInvited($email) {
        $query = "SELECT id FROM admin_invites WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function inviteAdmin($email, $admin_id) {
        $query = "INSERT INTO admin_invites (email, invited_by) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $email, $admin_id);
        return $stmt->execute();
    }

    public function consumeInvite($email) {
        $query = "DELETE FROM admin_invites WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        return $stmt->execute();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM users ORDER BY created_at DESC";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteUser($user_id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>