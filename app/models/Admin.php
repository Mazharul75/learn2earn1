<?php
class Admin extends Model {
    
    public function isInvited($email) {
        $this->db->query("SELECT id FROM admin_invites WHERE email = :email");
        $this->db->bind(':email', $email);
        $this->db->single();
        return ($this->db->rowCount() > 0);
    }

    public function inviteAdmin($email, $admin_id) {
        $this->db->query("INSERT INTO admin_invites (email, invited_by) VALUES (:email, :aid)");
        $this->db->bind(':email', $email);
        $this->db->bind(':aid', $admin_id);
        return $this->db->execute();
    }

    // --- NEW: CONSUME INVITE (Delete after use) ---
    public function consumeInvite($email) {
        $this->db->query("DELETE FROM admin_invites WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    public function getAllUsers() {
        $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function deleteUser($user_id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $user_id);
        return $this->db->execute();
    }
}