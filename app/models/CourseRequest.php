<?php
class CourseRequest extends Model {
    public function createRequest($learner_id, $course_id) {
        $this->db->query("INSERT INTO course_requests (learner_id, course_id, status) VALUES (:lid, :cid, 'pending')");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        return $this->db->execute();
    }

    public function hasPendingRequest($learner_id, $course_id) {
        $this->db->query("SELECT id FROM course_requests WHERE learner_id = :lid AND course_id = :cid AND status = 'pending'");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        return $this->db->single();
    }

    // For Instructor: Get all pending requests for their courses
    public function getRequestsByInstructor($instructor_id) {
        $this->db->query("SELECT cr.id, cr.course_id, cr.learner_id, u.name as learner_name, u.email as learner_email, c.title as course_title 
                          FROM course_requests cr
                          JOIN courses c ON cr.course_id = c.id
                          JOIN users u ON cr.learner_id = u.id
                          WHERE c.instructor_id = :iid AND cr.status = 'pending'");
        $this->db->bind(':iid', $instructor_id);
        return $this->db->resultSet();
    }

    public function approveRequest($request_id) {
        // 1. Get request details first
        $this->db->query("SELECT * FROM course_requests WHERE id = :id");
        $this->db->bind(':id', $request_id);
        $req = $this->db->single();

        if ($req) {
            // 2. Update Status
            $this->db->query("UPDATE course_requests SET status = 'approved' WHERE id = :id");
            $this->db->bind(':id', $request_id);
            $this->db->execute();

            return $req; // Return details so controller can enroll & notify
        }
        return false;
    }
    
    public function rejectRequest($request_id) {
        $this->db->query("UPDATE course_requests SET status = 'rejected' WHERE id = :id");
        $this->db->bind(':id', $request_id);
        return $this->db->execute();
    }
}
