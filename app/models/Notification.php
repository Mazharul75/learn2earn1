<?php
require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    
    public function create($user_id, $message, $link) {
        $conn = $this->db->getConnection();
        
        
        $check = $conn->prepare("SELECT id FROM notifications WHERE user_id = ? AND message = ? AND is_read = 0");
        $check->bind_param("is", $user_id, $message);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
       
            $check->close();
            return true; 
        }
        $check->close();

       
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        
   
        $stmt->bind_param("iss", $user_id, $message, $link);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function getUnread($user_id) {
        $conn = $this->db->getConnection();
     
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    public function markAsRead($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>