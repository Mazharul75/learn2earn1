<?php
require_once __DIR__ . '/../core/Database.php';

class Course {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function addCourse($data) {
        $query = "INSERT INTO courses (instructor_id, title, description, difficulty, max_capacity, reserved_seats, prerequisite_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->connection->prepare($query);
        
        // ssssiis -> string, string, string, string, int, int, string/int (null is handled)
        $stmt->bind_param("isssiii", 
            $_SESSION['user_id'], 
            $data['title'], 
            $data['description'], 
            $data['difficulty'], 
            $data['max_capacity'], 
            $data['reserved_seats'],
            $data['prerequisite_id']
        );
        
        return $stmt->execute();
    }

    // Logic Helper (kept from previous improvements)
    private function prepareForDisplay($courses) {
        $processed = [];
        $isSingle = isset($courses['id']);
        $items = $isSingle ? [$courses] : $courses;

        foreach ($items as $c) {
            $max = $c['max_capacity'];
            $taken = $c['student_count'] ?? 0;
            $reserved = $c['reserved_seats'];
            
            $public_left = $max - $reserved - $taken;
            $total_left = $max - $taken;

            $c['ui_public_seats'] = ($public_left > 0) ? $public_left : 0;
            $c['ui_is_public_full'] = ($public_left <= 0);
            $c['ui_is_totally_full'] = ($total_left <= 0);
            
            $c['ui_badge_color'] = match($c['difficulty']) {
                'Beginner' => '#2ecc71',
                'Intermediate' => '#f39c12',
                'Advanced' => '#e74c3c',
                default => '#95a5a6'
            };
            $processed[] = $c;
        }
        return $isSingle ? $processed[0] : $processed;
    }

    public function getAllCourses() {
        $query = "SELECT c.*, COUNT(e.learner_id) as student_count 
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.id = e.course_id 
                  GROUP BY c.id";
        
        $result = $this->connection->query($query);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        return $this->prepareForDisplay($rows);
    }

    public function getCourseById($id) {
        $query = "SELECT c.*, COUNT(e.learner_id) as student_count 
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.id = e.course_id 
                  WHERE c.id = ? 
                  GROUP BY c.id";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $this->prepareForDisplay($row);
    }

    public function getCoursesByInstructor($instructor_id) {
        $query = "SELECT c.*, COUNT(e.learner_id) as student_count 
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.id = e.course_id 
                  WHERE c.instructor_id = ? 
                  GROUP BY c.id";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $instructor_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCourseList() {
        $query = "SELECT id, title FROM courses ORDER BY title ASC";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMaterials($course_id) {
        $query = "SELECT * FROM materials WHERE course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addMaterial($data) {
        $query = "INSERT INTO materials (course_id, file_name) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("is", $data['course_id'], $data['file_name']);
        return $stmt->execute();
    }

    public function addTask($course_id, $title, $description) {
        $query = "INSERT INTO course_tasks (course_id, title, description) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iss", $course_id, $title, $description);
        return $stmt->execute();
    }

    public function getStudentsByCourse($course_id) {
        $query = "SELECT users.id, users.name, users.email, enrollments.progress, enrollments.enrolled_at
                  FROM enrollments
                  JOIN users ON enrollments.learner_id = users.id
                  WHERE enrollments.course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCompletedStudents($course_id) {
        $query = "SELECT users.id, users.name, users.email 
                  FROM enrollments 
                  JOIN users ON enrollments.learner_id = users.id 
                  WHERE enrollments.course_id = ? AND enrollments.progress = 100";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchCourses($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT c.*, COUNT(e.learner_id) as student_count 
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.id = e.course_id 
                  WHERE c.title LIKE ?
                  GROUP BY c.id";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchEnrolledStudents($course_id, $keyword) {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT u.name, u.email, e.progress, e.enrolled_at 
                  FROM enrollments e
                  JOIN users u ON e.learner_id = u.id
                  WHERE e.course_id = ? 
                  AND (u.name LIKE ? OR u.email LIKE ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iss", $course_id, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>