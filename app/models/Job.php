<?php
require_once __DIR__ . '/../../config/database.php';

class Job {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function addJob($data) {
        $query = "INSERT INTO jobs (client_id, title, description, required_course_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("issi", $_SESSION['user_id'], $data['title'], $data['description'], $data['required_course_id']);
        return $stmt->execute();
    }

    public function getAllJobs() {
        $query = "SELECT * FROM jobs";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobById($id) {
        $query = "SELECT * FROM jobs WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getJobsByClient($id) {
        $query = "SELECT * FROM jobs WHERE client_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>