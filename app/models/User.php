<?php
class User extends Model {
    // Register user [cite: 1210]
    public function register($data) {
        $this->db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        return $this->db->execute();
    }

    // Find user by email and verify password [cite: 1211]
    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        if ($row && password_verify($password, $row['password'])) {
            return $row;
        }
        return false;
    }

    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateProfile($data) {
        // 1. If New Password is provided -> Update Name, Email, AND Password
        if (!empty($data['password'])) {
            $this->db->query("UPDATE users SET name = :name, email = :email, password = :pass WHERE id = :id");
            $this->db->bind(':pass', $data['password']);
        } 
        // 2. If No New Password -> Update ONLY Name and Email
        else {
            $this->db->query("UPDATE users SET name = :name, email = :email WHERE id = :id");
        }

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':id', $data['id']);
        
        try {
            return $this->db->execute();
        } catch (PDOException $e) {
            // Likely an "Email already exists" error (Integrity Constraint)
            return false; 
        }
    }
}