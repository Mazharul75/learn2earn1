<?php
require_once __DIR__ . '/../../config/database.php';

class CourseRequest {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function createRequest($learner_id, $course_id) {
        $status = 'pending';
        $query = "INSERT INTO course_requests (learner_id, course_id, status) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iis", $learner_id, $course_id, $status);
        return $stmt->execute();
    }

    public function hasPendingRequest($learner_id, $course_id) {
        $status = 'pending';
        $query = "SELECT id FROM course_requests WHERE learner_id = ? AND course_id = ? AND status = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iis", $learner_id, $course_id, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getRequestsByInstructor($instructor_id) {
        $status = 'pending';
        $query = "SELECT cr.id, cr.course_id, cr.learner_id, u.name as learner_name, u.email as learner_email, c.title as course_title 
                  FROM course_requests cr
                  JOIN courses c ON cr.course_id = c.id
                  JOIN users u ON cr.learner_id = u.id
                  WHERE c.instructor_id = ? AND cr.status = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("is", $instructor_id, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function approveRequest($request_id) {
        // get infos
        $q1 = "SELECT * FROM course_requests WHERE id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("i", $request_id);
        $stmt1->execute();
        $req = $stmt1->get_result()->fetch_assoc();

        if ($req) {
            // 2. Update
            $status = 'approved';
            $q2 = "UPDATE course_requests SET status = ? WHERE id = ?";
            $stmt2 = $this->connection->prepare($q2);
            $stmt2->bind_param("si", $status, $request_id);
            $stmt2->execute();
            return $req;
        }
        return false;
    }

    public function rejectRequest($request_id) {
        $status = 'rejected';
        $query = "UPDATE course_requests SET status = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $status, $request_id);
        return $stmt->execute();
    }
}
?>