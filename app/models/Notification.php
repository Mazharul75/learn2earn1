<?php
require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =========================================================
    // FIX: SMART CREATE FUNCTION
    // =========================================================
    public function create($user_id, $message, $link) {
        $conn = $this->db->getConnection();
        
        // 1. DUPLICATE CHECK
        // Check if an UNREAD notification with this exact message already exists
        $check = $conn->prepare("SELECT id FROM notifications WHERE user_id = ? AND message = ? AND is_read = 0");
        $check->bind_param("is", $user_id, $message);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            // It already exists! Do NOT insert. Return true to pretend it worked.
            $check->close();
            return true; 
        }
        $check->close();

        // 2. INSERT (Only if it's new)
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        
        // Note: If your database doesn't have 'created_at', remove ', created_at' and ', NOW()'
        // Try this first. If it errors, remove the date part.
        
        $stmt->bind_param("iss", $user_id, $message, $link);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function getUnread($user_id) {
        $conn = $this->db->getConnection();
        // Order by ID DESC so newest are on top
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Helper to clear notifications when viewed
    public function markAsRead($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>