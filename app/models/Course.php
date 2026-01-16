<?php
class Course extends Model {
    // Feature 1: Instructor Create Course [cite: 31, 147]
    public function addCourse($data) {
        $this->db->query("INSERT INTO courses (instructor_id, title, description, difficulty) VALUES (:uid, :t, :d, :diff)");
        $this->db->bind(':uid', $_SESSION['user_id']);
        $this->db->bind(':t', $data['title']);
        $this->db->bind(':d', $data['description']);
        $this->db->bind(':diff', $data['difficulty']);
        return $this->db->execute();
    }

    // Used by Instructor Dashboard [cite: 87, 149]
    public function getCoursesByInstructor($instructor_id){
        $this->db->query(
            "SELECT * FROM courses WHERE instructor_id = :instructor_id"
        );
        $this->db->bind(':instructor_id', $instructor_id);
        return $this->db->resultSet();
    }
    // Used by Learner Search & Browse [cite: 105, 150]
    public function getAllCourses() {
        $this->db->query("SELECT * FROM courses");
        return $this->db->resultSet();
    }

    // Needed for Instructor/manage view [cite: 98]
    public function getCourseById($id) {
        $this->db->query("SELECT * FROM courses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Feature 2: Get materials for a specific course [cite: 152]
    public function getMaterials($course_id) {
        $this->db->query("SELECT * FROM materials WHERE course_id = :cid");
        $this->db->bind(':cid', $course_id);
        return $this->db->resultSet();
    }

    // Feature 2: Added logic to handle Material Upload correctly [cite: 100, 152]
    public function addMaterial($data) {
        // Database uses 'file_name'
        $this->db->query("INSERT INTO materials (course_id, file_name) VALUES (:cid, :file)");
        $this->db->bind(':cid', $data['course_id']);
        $this->db->bind(':file', $data['file_name']); 
        return $this->db->execute();
    }

    public function addTask($course_id, $title, $description) {
        $this->db->query("INSERT INTO course_tasks (course_id, title, description) VALUES (:cid, :title, :desc)");
        $this->db->bind(':cid', $course_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':desc', $description);
        return $this->db->execute();
    }
    
    // Feature 3: View enrolled students list 
    public function getStudentsByCourse($course_id) {
        // FIX: Added 'users.id' back to the SELECT list
        $this->db->query("SELECT users.id, users.name, users.email, enrollments.progress, enrollments.enrolled_at
            FROM enrollments
            JOIN users ON enrollments.learner_id = users.id
            WHERE enrollments.course_id = :course_id");
        $this->db->bind(':course_id', $course_id);
        return $this->db->resultSet();
    }

    public function getCompletedStudents($course_id) {
        $this->db->query("SELECT users.id, users.name, users.email 
                          FROM enrollments 
                          JOIN users ON enrollments.learner_id = users.id 
                          WHERE enrollments.course_id = :cid 
                          AND enrollments.progress = 100");
        $this->db->bind(':cid', $course_id);
        return $this->db->resultSet();
    }

    // AJAX Course Search Feature [cite: 114, 151]
    public function searchCourses($keyword) {
        $this->db->query("SELECT * FROM courses WHERE title LIKE :keyword");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
}