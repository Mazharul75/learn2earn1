<?php
require_once "../app/core/Model.php";

class User extends Model {

    public function register($data) {
        $this->db->query(
            "INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)"
        );

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $user = $this->db->single();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
