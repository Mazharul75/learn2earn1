<?php
class Notification extends Model {
    public function create($user_id, $message, $link = '#') {
        $this->db->query("INSERT INTO notifications (user_id, message, link) VALUES (:uid, :msg, :link)");
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':msg', $message);
        $this->db->bind(':link', $link);
        return $this->db->execute();
    }

    public function getUnread($user_id) {
        // Order by newest first
        $this->db->query("SELECT * FROM notifications WHERE user_id = :uid AND is_read = 0 ORDER BY created_at DESC");
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function markRead($id) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}