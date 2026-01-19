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

    
    public function inviteAdmin($email, $invited_by) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO admin_invites (email, invited_by) VALUES (?, ?)");
        $stmt->bind_param("si", $email, $invited_by);
        return $stmt->execute();
    }

    public function isInvited($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id FROM admin_invites WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function consumeInvite($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM admin_invites WHERE email = ?");
        $stmt->bind_param("s", $email);
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $conn = $this->db->getConnection();

        // 1. Delete Notifications
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 2. Delete Enrollments
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

        // 5. Delete Recommendations
        $stmt = $conn->prepare("DELETE FROM recommendations WHERE learner_id = ? OR instructor_id = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $stmt->close();

        // 6. Handle Client Jobs (Clean up before delete)
        $stmt = $conn->prepare("SELECT id FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $jobResult = $stmt->get_result();
        while ($row = $jobResult->fetch_assoc()) {
            $jobId = $row['id'];
            $delApp = $conn->prepare("DELETE FROM job_applications WHERE job_id = ?");
            $delApp->bind_param("i", $jobId);
            $delApp->execute();
            $delRec = $conn->prepare("DELETE FROM recommendations WHERE job_id = ?");
            $delRec->bind_param("i", $jobId);
            $delRec->execute();
        }
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 7. Handle Instructor Courses 
        $stmt = $conn->prepare("SELECT id FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $courseResult = $stmt->get_result();
        while ($row = $courseResult->fetch_assoc()) {
            $courseId = $row['id'];
            $unlinkJobs = $conn->prepare("UPDATE jobs SET required_course_id = NULL WHERE required_course_id = ?");
            $unlinkJobs->bind_param("i", $courseId);
            $unlinkJobs->execute();
            $delEnroll = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
            $delEnroll->bind_param("i", $courseId);
            $delEnroll->execute();
            $delMat = $conn->prepare("DELETE FROM materials WHERE course_id = ?");
            $delMat->bind_param("i", $courseId);
            $delMat->execute();
            $delReq = $conn->prepare("DELETE FROM course_requests WHERE course_id = ?");
            $delReq->bind_param("i", $courseId);
            $delReq->execute();
        }
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 8. Delete User
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>