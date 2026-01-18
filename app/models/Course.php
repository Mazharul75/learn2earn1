<?php
class Course extends Model {

    public function addCourse($data) {
        $this->db->query("INSERT INTO courses (instructor_id, title, description, difficulty, max_capacity, reserved_seats, prerequisite_id) 
                          VALUES (:uid, :t, :d, :diff, :max, :res, :pre)");
        $this->db->bind(':uid', $_SESSION['user_id']);
        $this->db->bind(':t', $data['title']);
        $this->db->bind(':d', $data['description']);
        $this->db->bind(':diff', $data['difficulty']);
        $this->db->bind(':max', $data['max_capacity']);
        $this->db->bind(':res', $data['reserved_seats']);
        // Handle NULL for beginner courses
        $this->db->bind(':pre', !empty($data['prerequisite_id']) ? $data['prerequisite_id'] : null);
        
        return $this->db->execute();
    }

    // Helper to fetch simple list for dropdown (id and title only)
    public function getCourseList() {
        $this->db->query("SELECT id, title FROM courses ORDER BY title ASC");
        return $this->db->resultSet();
    }

        // Updated: Fetch courses + Count of enrolled students
    public function getCoursesByInstructor($instructor_id) {
        $this->db->query("SELECT c.*, COUNT(e.learner_id) as student_count 
                          FROM courses c 
                          LEFT JOIN enrollments e ON c.id = e.course_id 
                          WHERE c.instructor_id = :uid 
                          GROUP BY c.id");
        $this->db->bind(':uid', $instructor_id);
        return $this->db->resultSet();
    }

    // Updated: Fetch all courses + Count of enrolled students
    public function getAllCourses() {
        $this->db->query("SELECT c.*, COUNT(e.learner_id) as student_count 
                          FROM courses c 
                          LEFT JOIN enrollments e ON c.id = e.course_id 
                          GROUP BY c.id");
        $raw = $this->db->resultSet();
        
        // STRICT MVC: Process data before returning
        return $this->prepareForDisplay($raw);
    }

    public function prepareForDisplay($courses) {
        $processed = [];
        
        // If single record, wrap in array to treat same way
        $isSingle = isset($courses['id']);
        $items = $isSingle ? [$courses] : $courses;

        foreach ($items as $c) {
            // 1. Calculate Seats Logic
            $max = $c['max_capacity'];
            $taken = $c['student_count'] ?? 0; // Handle null
            $reserved = $c['reserved_seats'];
            
            $public_left = $max - $reserved - $taken;
            $total_left = $max - $taken;

            // 2. Determine UI State (Logic moved from View to Model)
            $c['ui_public_seats'] = ($public_left > 0) ? $public_left : 0;
            $c['ui_is_public_full'] = ($public_left <= 0);
            $c['ui_is_totally_full'] = ($total_left <= 0);
            
            // 3. Badge Colors (Presentation Logic in Model)
            $c['ui_badge_color'] = match($c['difficulty']) {
                'Beginner' => '#2ecc71', // Green
                'Intermediate' => '#f39c12', // Orange
                'Advanced' => '#e74c3c', // Red
                default => '#95a5a6' // Grey
            };

            $processed[] = $c;
        }

        return $isSingle ? $processed[0] : $processed;
    }

    // Needed for Instructor/manage view [cite: 98]
    public function getCourseById($id) {
        $this->db->query("SELECT c.*, COUNT(e.learner_id) as student_count 
                          FROM courses c 
                          LEFT JOIN enrollments e ON c.id = e.course_id 
                          WHERE c.id = :id
                          GROUP BY c.id");
        $this->db->bind(':id', $id);
        $raw = $this->db->single();
        
        return $this->prepareForDisplay($raw);
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
        // Fix: Added COUNT(e.learner_id) and JOIN to get enrollment numbers
        $this->db->query("SELECT c.*, COUNT(e.learner_id) as student_count 
                          FROM courses c 
                          LEFT JOIN enrollments e ON c.id = e.course_id 
                          WHERE c.title LIKE :keyword
                          GROUP BY c.id");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
}