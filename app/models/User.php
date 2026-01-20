<?php
require_once __DIR__ . '/../../config/database.php';

class User {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function register($data) {
        $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
    
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['password'], $data['role']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id); 
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findUserByEmail($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function updateProfile($data) {
        if (!empty($data['password'])) {
         
            $query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssi", $data['name'], $data['email'], $data['password'], $data['id']);
        } else {
           
            $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssi", $data['name'], $data['email'], $data['id']);
        }
        
        return $stmt->execute();
    }
    public function deleteUser($id) {
        // 1. Delete Notifications
        $stmt = $this->connection->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 2. Delete Enrollments
        $stmt = $this->connection->prepare("DELETE FROM enrollments WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 3. Delete Job Applications
        $stmt = $this->connection->prepare("DELETE FROM job_applications WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 4. Delete Course Requests
        $stmt = $this->connection->prepare("DELETE FROM course_requests WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 5. Delete Recommendations
        $stmt = $this->connection->prepare("DELETE FROM recommendations WHERE learner_id = ? OR instructor_id = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $stmt->close();

        // 6. Delete Task Completions (As Learner)
        $stmt = $this->connection->prepare("DELETE FROM task_completion WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 7. NEW: Delete Material Completions (This was the hidden blocker!)
        $stmt = $this->connection->prepare("DELETE FROM material_completion WHERE learner_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 8. Delete Admin Invites sent by this user
        $stmt = $this->connection->prepare("DELETE FROM admin_invites WHERE invited_by = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 9. Handle Client Jobs (Cascading delete manually)
        $stmt = $this->connection->prepare("SELECT id FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $jobResult = $stmt->get_result();
        while ($row = $jobResult->fetch_assoc()) {
            $jobId = $row['id'];
            // Clean Job Apps
            $delApp = $this->connection->prepare("DELETE FROM job_applications WHERE job_id = ?");
            $delApp->bind_param("i", $jobId);
            $delApp->execute();
            // Clean Recommendations
            $delRec = $this->connection->prepare("DELETE FROM recommendations WHERE job_id = ?");
            $delRec->bind_param("i", $jobId);
            $delRec->execute();
        }
        $stmt->close();
        $stmt = $this->connection->prepare("DELETE FROM jobs WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 10. Handle Instructor Courses (Deep Cleaning)
        $stmt = $this->connection->prepare("SELECT id FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $courseResult = $stmt->get_result();
        while ($row = $courseResult->fetch_assoc()) {
            $courseId = $row['id'];
            
            // Unlink Jobs
            $unlink = $this->connection->prepare("UPDATE jobs SET required_course_id = NULL WHERE required_course_id = ?");
            $unlink->bind_param("i", $courseId);
            $unlink->execute();

            // Delete Course dependencies
            $this->connection->query("DELETE FROM enrollments WHERE course_id = $courseId");
            $this->connection->query("DELETE FROM course_requests WHERE course_id = $courseId");
            
            // Deep Clean Materials
            $this->connection->query("DELETE FROM material_completion WHERE material_id IN (SELECT id FROM materials WHERE course_id = $courseId)");
            $this->connection->query("DELETE FROM materials WHERE course_id = $courseId");

            // Deep Clean Tasks
            $this->connection->query("DELETE FROM task_completion WHERE task_id IN (SELECT id FROM course_tasks WHERE course_id = $courseId)");
            $this->connection->query("DELETE FROM course_tasks WHERE course_id = $courseId");

            // Deep Clean Quizzes
            $this->connection->query("DELETE FROM questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE course_id = $courseId)");
            $this->connection->query("DELETE FROM quizzes WHERE course_id = $courseId");
        }
        $stmt->close();
        $stmt = $this->connection->prepare("DELETE FROM courses WHERE instructor_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 11. FINALLY: Delete the User
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>