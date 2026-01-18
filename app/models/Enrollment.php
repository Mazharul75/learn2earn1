<?php
require_once __DIR__ . '/../core/Database.php';

class Enrollment {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function enroll($learner_id, $course_id) {
        $query = "INSERT INTO enrollments (learner_id, course_id) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        return $stmt->execute();
    }

    public function isEnrolled($learner_id, $course_id) {
        $query = "SELECT * FROM enrollments WHERE learner_id = ? AND course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getLearnerCourses($learner_id) {
        $query = "SELECT courses.* FROM courses 
                  JOIN enrollments ON courses.id = enrollments.course_id 
                  WHERE enrollments.learner_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $learner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countEnrollments($course_id) {
        $query = "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function hasCompleted($learner_id, $course_id) {
        $query = "SELECT status FROM enrollments WHERE learner_id = ? AND course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row && $row['status'] == 'completed');
    }
}
?>