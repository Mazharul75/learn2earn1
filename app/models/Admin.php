<?php
require_once __DIR__ . '/../../config/database.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllUsers() {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Feature: Invite Admin
    public function inviteAdmin($email, $invited_by) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO admin_invites (email, invited_by) VALUES (?, ?)");
        $stmt->bind_param("si", $email, $invited_by);
        return $stmt->execute();
    }

    public function isInvited($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id FROM admin_invites WHERE email = ? AND status = 'pending'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function consumeInvite($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE admin_invites SET status = 'used' WHERE email = ?");
        $stmt->bind_param("s", $email);
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $conn = $this->db->getConnection();

        // =========================================================
        // STEP 1: CLEAN UP LEARNER DATA
        // =========================================================
        
        // 1. Delete Notifications
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 2. Delete Enrollments (as a student)
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 3. Delete Job Applications
        $stmt = $conn->prepare("DELETE FROM job_applications WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 4. Delete Course Requests
        $stmt = $conn->prepare("DELETE FROM course_requests WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();


        // =========================================================
        // STEP 2: CLEAN UP INSTRUCTOR DATA (The Fix for your Error)
        // =========================================================
        
        // A. Delete Recommendations involved with this user
        $stmt = $conn->prepare("DELETE FROM recommendations WHERE learner_id = ? OR instructor_id = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $stmt->close();

        // B. Handle COURSES owned by this instructor
        // First, get all course IDs this user owns
        $stmt = $conn->prepare("SELECT id FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $courseResult = $stmt->get_result();

        while ($row = $courseResult->fetch_assoc()) {
            $courseId = $row['id'];

            // CRITICAL FIX: Unlink Jobs that require this course
            // If we don't do this, we get the 'jobs_ibfk_2' error
            $unlinkJobs = $conn->prepare("UPDATE jobs SET required_course_id = NULL WHERE required_course_id = ?");
            $unlinkJobs->bind_param("i", $courseId);
            $unlinkJobs->execute();
            $unlinkJobs->close();

            // Delete other learners' enrollments in this course
            $delEnroll = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
            $delEnroll->bind_param("i", $courseId);
            $delEnroll->execute();
            $delEnroll->close();

            // Delete materials for this course
            $delMat = $conn->prepare("DELETE FROM materials WHERE course_id = ?");
            $delMat->bind_param("i", $courseId);
            $delMat->execute();
            $delMat->close();
            
            // Delete pending requests for this course
            $delReq = $conn->prepare("DELETE FROM course_requests WHERE course_id = ?");
            $delReq->bind_param("i", $courseId);
            $delReq->execute();
            $delReq->close();
        }
        $stmt->close();

        // Now safe to delete the courses themselves
        $stmt = $conn->prepare("DELETE FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();


        // =========================================================
        // STEP 3: CLEAN UP CLIENT DATA (Jobs)
        // =========================================================

        // Get IDs of jobs posted by this user
        $stmt = $conn->prepare("SELECT id FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $jobResult = $stmt->get_result();
        
        while ($row = $jobResult->fetch_assoc()) {
            $jobId = $row['id'];
            
            // Delete applications for this job
            $delApp = $conn->prepare("DELETE FROM job_applications WHERE job_id = ?");
            $delApp->bind_param("i", $jobId);
            $delApp->execute();
            $delApp->close();

            // Delete recommendations linked to this job
            $delRec = $conn->prepare("DELETE FROM recommendations WHERE job_id = ?");
            $delRec->bind_param("i", $jobId);
            $delRec->execute();
            $delRec->close();
        }
        $stmt->close();

        // Delete the jobs
        $stmt = $conn->prepare("DELETE FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();


        // =========================================================
        // STEP 4: FINALLY DELETE THE USER
        // =========================================================
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>