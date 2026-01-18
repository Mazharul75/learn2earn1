<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function register($data) {
        $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        // "ssss" means 4 strings
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['password'], $data['role']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id); // "i" for integer
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findUserByEmail($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function updateProfile($data) {
        if (!empty($data['password'])) {
            // Update Password too
            $query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssi", $data['name'], $data['email'], $data['password'], $data['id']);
        } else {
            // Update only Name/Email
            $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssi", $data['name'], $data['email'], $data['id']);
        }
        
        return $stmt->execute();
    }
}
?>