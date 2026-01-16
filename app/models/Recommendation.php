<?php
class Recommendation extends Model {
    public function add($job_id, $learner_id, $instructor_id) {
        // Prevent duplicate recommendation
        $this->db->query("SELECT id FROM recommendations WHERE job_id = :jid AND learner_id = :lid");
        $this->db->bind(':jid', $job_id);
        $this->db->bind(':lid', $learner_id);
        if($this->db->single()) return false;

        $this->db->query("INSERT INTO recommendations (job_id, learner_id, instructor_id) VALUES (:jid, :lid, :iid)");
        $this->db->bind(':jid', $job_id);
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':iid', $instructor_id);
        return $this->db->execute();
    }

    // Get recommendations for a specific job (Client view)
    public function getByJob($job_id) {
        $this->db->query("SELECT r.*, u.name as learner_name, u.email as learner_email, i.name as instructor_name
                          FROM recommendations r
                          JOIN users u ON r.learner_id = u.id
                          JOIN users i ON r.instructor_id = i.id
                          WHERE r.job_id = :jid");
        $this->db->bind(':jid', $job_id);
        return $this->db->resultSet();
    }
}