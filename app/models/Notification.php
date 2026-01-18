<?php
require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function create($user_id, $message, $link = '#') {
        $query = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iss", $user_id, $message, $link);
        return $stmt->execute();
    }

    public function getUnread($user_id) {
        $query = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>