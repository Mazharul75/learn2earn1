<?php
require_once __DIR__ . '/../../config/database.php';

class Progress {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getTasksByCourse($course_id, $learner_id) {
        $query = "SELECT ct.*, tc.status, tc.submission_file, tc.instructor_feedback, tc.completed_at 
                  FROM course_tasks ct 
                  LEFT JOIN task_completion tc ON ct.id = tc.task_id AND tc.learner_id = ? 
                  WHERE ct.course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCheckedMaterials($learner_id, $course_id) {
        $query = "SELECT material_id FROM material_completion 
                  WHERE learner_id = ? 
                  AND material_id IN (SELECT id FROM materials WHERE course_id = ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return array_column($rows, 'material_id');
    }

    public function checkPrerequisites($course_id, $learner_id) {
        // 1. Materials are here 
        $q1 = "SELECT COUNT(*) as total FROM materials WHERE course_id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("i", $course_id);
        $stmt1->execute();
        $matTotal = $stmt1->get_result()->fetch_assoc()['total'];

        $q2 = "SELECT COUNT(DISTINCT material_id) as completed FROM material_completion 
               WHERE learner_id = ? AND material_id IN (SELECT id FROM materials WHERE course_id = ?)";
        $stmt2 = $this->connection->prepare($q2);
        $stmt2->bind_param("ii", $learner_id, $course_id);
        $stmt2->execute();
        $matDone = $stmt2->get_result()->fetch_assoc()['completed'];

        // 2. tasks are here 
        $q3 = "SELECT COUNT(*) as total FROM course_tasks WHERE course_id = ?";
        $stmt3 = $this->connection->prepare($q3);
        $stmt3->bind_param("i", $course_id);
        $stmt3->execute();
        $taskTotal = $stmt3->get_result()->fetch_assoc()['total'];

        $q4 = "SELECT COUNT(*) as approved FROM task_completion 
               WHERE learner_id = ? AND status = 'approved' 
               AND task_id IN (SELECT id FROM course_tasks WHERE course_id = ?)";
        $stmt4 = $this->connection->prepare($q4);
        $stmt4->bind_param("ii", $learner_id, $course_id);
        $stmt4->execute();
        $taskApproved = $stmt4->get_result()->fetch_assoc()['approved'];

        $materialsOK = ($matTotal == 0) || ($matDone >= $matTotal);
        $tasksOK     = ($taskTotal == 0) || ($taskApproved >= $taskTotal);

        return $materialsOK && $tasksOK;
    }

    public function checkoutMaterial($learner_id, $material_id) {
        $q1 = "SELECT id FROM material_completion WHERE learner_id = ? AND material_id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("ii", $learner_id, $material_id);
        $stmt1->execute();
        $res = $stmt1->get_result();
        
        if($res->num_rows > 0) return true;

        $q2 = "INSERT INTO material_completion (learner_id, material_id) VALUES (?, ?)";
        $stmt2 = $this->connection->prepare($q2);
        $stmt2->bind_param("ii", $learner_id, $material_id);
        return $stmt2->execute();
    }

    public function submitTask($learner_id, $task_id, $filename) {
        $q1 = "SELECT id FROM task_completion WHERE learner_id = ? AND task_id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("ii", $learner_id, $task_id);
        $stmt1->execute();
        $exists = $stmt1->get_result()->num_rows > 0;

        if ($exists) {
            $status = 'pending';
            $q2 = "UPDATE task_completion SET submission_file = ?, status = ? WHERE learner_id = ? AND task_id = ?";
            $stmt2 = $this->connection->prepare($q2);
            $stmt2->bind_param("ssii", $filename, $status, $learner_id, $task_id);
            return $stmt2->execute();
        } else {
            $status = 'pending';
            $q2 = "INSERT INTO task_completion (learner_id, task_id, submission_file, status) VALUES (?, ?, ?, ?)";
            $stmt2 = $this->connection->prepare($q2);
            $stmt2->bind_param("iiss", $learner_id, $task_id, $filename, $status);
            return $stmt2->execute();
        }
    }
   //pending submissions tasks
    public function getPendingSubmissions($course_id) {
        $status = 'pending';
        $query = "SELECT tc.id as completion_id, tc.submission_file, tc.status, u.name as student_name, ct.title as task_title 
                  FROM task_completion tc
                  JOIN users u ON tc.learner_id = u.id
                  JOIN course_tasks ct ON tc.task_id = ct.id
                  WHERE ct.course_id = ? AND tc.status = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("is", $course_id, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTaskStatus($completion_id, $status, $feedback) {
        $query = "UPDATE task_completion SET status = ?, instructor_feedback = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssi", $status, $feedback, $completion_id);
        return $stmt->execute();
    }

    public function markCourseComplete($learner_id, $course_id) {
        $status = 'completed';
        $progress = 100;
        $query = "UPDATE enrollments SET status = ?, progress = ? WHERE learner_id = ? AND course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("siii", $status, $progress, $learner_id, $course_id);
        return $stmt->execute();
    }

    public function isCourseCompleted($course_id, $learner_id) {
        $query = "SELECT status FROM enrollments WHERE learner_id = ? AND course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $learner_id, $course_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ($row && $row['status'] == 'completed');
    }
}
?>