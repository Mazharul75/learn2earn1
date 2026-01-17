<?php
class Enrollment extends Model {
    // Save a new enrollment to the database
    public function enroll($learner_id, $course_id) {
        $this->db->query("INSERT INTO enrollments (learner_id, course_id) VALUES (:learner_id, :course_id)");
        $this->db->bind(':learner_id', $learner_id);
        $this->db->bind(':course_id', $course_id);
        return $this->db->execute();
    }

    // Check if a learner is already enrolled in a specific course
    public function isEnrolled($learner_id, $course_id) {
        $this->db->query("SELECT * FROM enrollments WHERE learner_id = :learner_id AND course_id = :course_id");
        $this->db->bind(':learner_id', $learner_id);
        $this->db->bind(':course_id', $course_id);
        return $this->db->single();
    }

    public function getLearnerCourses($learner_id) {
        $this->db->query("SELECT courses.* FROM courses 
                          JOIN enrollments ON courses.id = enrollments.course_id 
                          WHERE enrollments.learner_id = :learner_id");
        $this->db->bind(':learner_id', $learner_id);
        return $this->db->resultSet();
    }

        // Add this to Enrollment.php
    public function countEnrollments($course_id) {
        $this->db->query("SELECT COUNT(*) as count FROM enrollments WHERE course_id = :cid");
        $this->db->bind(':cid', $course_id);
        $row = $this->db->single();
        return $row['count'];
    }

    // Check if a learner has COMPLETED a specific course
    public function hasCompleted($learner_id, $course_id) {
        $this->db->query("SELECT status FROM enrollments WHERE learner_id = :lid AND course_id = :cid");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        $row = $this->db->single();
        
        // Return TRUE only if status is 'completed'
        return ($row && $row['status'] == 'completed');
    }

    public function markAsCompleted($learner_id, $course_id) {
        $this->db->query("UPDATE enrollments SET status = 'completed' WHERE learner_id = :lid AND course_id = :cid");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        return $this->db->execute();
    }

}