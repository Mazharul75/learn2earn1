<?php
require_once __DIR__ . '/../../config/database.php';

class Recommendation {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function add($job_id, $learner_id, $instructor_id) {
        $q1 = "SELECT id FROM recommendations WHERE job_id = ? AND learner_id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("ii", $job_id, $learner_id);
        $stmt1->execute();
        if ($stmt1->get_result()->num_rows > 0) return false;

        $q2 = "INSERT INTO recommendations (job_id, learner_id, instructor_id) VALUES (?, ?, ?)";
        $stmt2 = $this->connection->prepare($q2);
        $stmt2->bind_param("iii", $job_id, $learner_id, $instructor_id);
        return $stmt2->execute();
    }

    public function getByJob($job_id) {
        $query = "SELECT r.*, u.name as learner_name, u.email as learner_email, i.name as instructor_name
                  FROM recommendations r
                  JOIN users u ON r.learner_id = u.id
                  JOIN users i ON r.instructor_id = i.id
                  WHERE r.job_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>