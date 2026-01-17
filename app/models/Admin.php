<?php
class Admin extends Model {
    
    // Check if an email is on the "Allowed List"
    public function isInvited($email) {
        $this->db->query("SELECT id FROM admin_invites WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return ($this->db->rowCount() > 0);
    }

    // Add a new email to the whitelist
    public function inviteAdmin($email, $admin_id) {
        $this->db->query("INSERT INTO admin_invites (email, invited_by) VALUES (:email, :aid)");
        $this->db->bind(':email', $email);
        $this->db->bind(':aid', $admin_id);
        return $this->db->execute();
    }

    // Get all users for management
    public function getAllUsers() {
        $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    // Delete a user
    public function deleteUser($user_id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $user_id);
        return $this->db->execute();
    }
}
