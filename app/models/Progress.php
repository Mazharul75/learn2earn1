<?php
class Progress extends Model {
    // Join tasks with completion table to see what this learner has finished
    public function getTasksByCourse($course_id, $learner_id) {
        $this->db->query("SELECT ct.*, tc.status, tc.submission_file, tc.instructor_feedback, tc.completed_at 
                          FROM course_tasks ct 
                          LEFT JOIN task_completion tc ON ct.id = tc.task_id AND tc.learner_id = :lid 
                          WHERE ct.course_id = :cid");
        $this->db->bind(':cid', $course_id);
        $this->db->bind(':lid', $learner_id);
        return $this->db->resultSet();
    }

    public function checkPrerequisites($course_id, $learner_id) {
        // 1. Check Materials (Must be viewed)
        $this->db->query("SELECT COUNT(*) as total FROM materials WHERE course_id = :cid");
        $this->db->bind(':cid', $course_id);
        $matTotal = $this->db->single()['total'];

        $this->db->query("SELECT COUNT(*) as completed FROM material_completion 
                          WHERE learner_id = :lid AND material_id IN (SELECT id FROM materials WHERE course_id = :cid)");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        $matDone = $this->db->single()['completed'];

        // 2. Check Tasks (Must be APPROVED)
        $this->db->query("SELECT COUNT(*) as total FROM course_tasks WHERE course_id = :cid");
        $this->db->bind(':cid', $course_id);
        $taskTotal = $this->db->single()['total'];
        
        // Count only APPROVED tasks
        $this->db->query("SELECT COUNT(*) as approved FROM task_completion 
                          WHERE learner_id = :lid AND status = 'approved' 
                          AND task_id IN (SELECT id FROM course_tasks WHERE course_id = :cid)");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        $taskApproved = $this->db->single()['approved'];

        $materialsOK = ($matTotal == 0) || ($matTotal == $matDone);
        $tasksOK     = ($taskTotal == 0) || ($taskTotal == $taskApproved);

        return $materialsOK && $tasksOK;
    }

    public function submitTask($learner_id, $task_id, $filename) {
        // Check if exists (Resubmission logic)
        $this->db->query("SELECT id FROM task_completion WHERE learner_id = :lid AND task_id = :tid");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':tid', $task_id);
        
        if ($this->db->single()) {
            // Update existing
            $this->db->query("UPDATE task_completion SET submission_file = :file, status = 'pending' WHERE learner_id = :lid AND task_id = :tid");
        } else {
            // Insert new
            $this->db->query("INSERT INTO task_completion (learner_id, task_id, submission_file, status) VALUES (:lid, :tid, :file, 'pending')");
        }
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':tid', $task_id);
        $this->db->bind(':file', $filename);
        return $this->db->execute();
    }

    // Instructor: Get pending submissions for a course
    public function getPendingSubmissions($course_id) {
        $this->db->query("SELECT tc.id as completion_id, tc.submission_file, tc.status, u.name as student_name, ct.title as task_title 
                          FROM task_completion tc
                          JOIN users u ON tc.learner_id = u.id
                          JOIN course_tasks ct ON tc.task_id = ct.id
                          WHERE ct.course_id = :cid AND tc.status = 'pending'");
        $this->db->bind(':cid', $course_id);
        return $this->db->resultSet();
    }

    // Instructor: Approve/Reject
    public function updateTaskStatus($completion_id, $status, $feedback = '') {
        $this->db->query("UPDATE task_completion SET status = :status, instructor_feedback = :feedback WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':feedback', $feedback);
        $this->db->bind(':id', $completion_id);
        return $this->db->execute();
    }



    public function checkoutMaterial($learner_id, $material_id) {
        $this->db->query("INSERT INTO material_completion (learner_id, material_id) VALUES (:lid, :mid)");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':mid', $material_id);
        return $this->db->execute();
    }

    public function markCourseComplete($learner_id, $course_id) {
        // Update the new 'status' column we added to the enrollments table
        $this->db->query("UPDATE enrollments SET status = 'completed' WHERE learner_id = :lid AND course_id = :cid");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        return $this->db->execute();
    }

    // Insert into the completion table instead of updating the task table
    public function markTaskDone($task_id, $learner_id) {
        $this->db->query("INSERT INTO task_completion (learner_id, task_id) VALUES (:lid, :tid)");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':tid', $task_id);
        return $this->db->execute();
    }

    public function isCourseCompleted($course_id, $learner_id) {
        $this->db->query("SELECT progress FROM enrollments 
                          WHERE learner_id = :lid AND course_id = :cid");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':cid', $course_id);
        
        $row = $this->db->single();

        // If progress is 100 (set by passing the quiz), the course is officially done.
        if ($row && $row['progress'] == 100) {
            return true;
        }
        return false;
    }
}