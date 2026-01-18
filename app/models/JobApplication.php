<?php
class JobApplication extends Model {
    public function apply($job_id, $learner_id, $cv_file) {
        $this->db->query("INSERT INTO job_applications (job_id, learner_id, cv_file, status) 
                          VALUES (:job_id, :learner_id, :cv, 'applied')");
        $this->db->bind(':job_id', $job_id);
        $this->db->bind(':learner_id', $learner_id);
        $this->db->bind(':cv', $cv_file);
        return $this->db->execute();
    }

    public function getApplicantsByJob($job_id) {
        // ADD job_applications.learner_id to the SELECT list
        $this->db->query("SELECT users.name, users.email, job_applications.id as app_id, 
                          job_applications.status, job_applications.cv_file, job_applications.learner_id 
                          FROM job_applications 
                          JOIN users ON job_applications.learner_id = users.id 
                          WHERE job_applications.job_id = :job_id");
        $this->db->bind(':job_id', $job_id);
        return $this->db->resultSet();
    }

    public function alreadyApplied($job_id, $learner_id) {
        $this->db->query("SELECT * FROM job_applications WHERE job_id = :job_id AND learner_id = :learner_id");
        $this->db->bind(':job_id', $job_id);
        $this->db->bind(':learner_id', $learner_id);
        return $this->db->single();
    }

    public function getApplicantsForClient($client_id) {
        $this->db->query("SELECT users.name, jobs.title as job_title, job_applications.status 
                          FROM job_applications 
                          JOIN jobs ON job_applications.job_id = jobs.id 
                          JOIN users ON job_applications.learner_id = users.id 
                          WHERE jobs.client_id = :client_id");
        $this->db->bind(':client_id', $client_id);
        return $this->db->resultSet();
    }

    public function updateStatus($app_id, $status) {
        $this->db->query("UPDATE job_applications SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $app_id);
        return $this->db->execute();
    }

    public function selectLearner($app_id) {
        $this->db->query("UPDATE job_applications SET status = 'selected' WHERE id = :id");
        $this->db->bind(':id', $app_id);
        return $this->db->execute();
    }

    public function inviteLearner($learner_id, $job_id) {
        // FIX: Check for duplicates first to prevent crash
        $this->db->query("SELECT id FROM job_applications WHERE job_id = :jid AND learner_id = :lid");
        $this->db->bind(':jid', $job_id);
        $this->db->bind(':lid', $learner_id);
        
        if ($this->db->single()) {
            return false; // Already applied/invited
        }

        $this->db->query("INSERT INTO job_applications (job_id, learner_id, status) VALUES (:jid, :lid, 'applied')");
        $this->db->bind(':lid', $learner_id);
        $this->db->bind(':jid', $job_id);
        return $this->db->execute();
    }
    public function getLearnerApplications($learner_id) {
        $this->db->query("SELECT jobs.title, job_applications.status 
                          FROM job_applications 
                          JOIN jobs ON job_applications.job_id = jobs.id 
                          WHERE job_applications.learner_id = :lid");
        $this->db->bind(':lid', $learner_id);
        return $this->db->resultSet();
    }
    // AJAX: Search applicants for a specific job
    public function searchApplicants($job_id, $keyword) {
        $this->db->query("SELECT users.name, users.email, job_applications.id as app_id, 
                          job_applications.status, job_applications.cv_file 
                          FROM job_applications 
                          JOIN users ON job_applications.learner_id = users.id 
                          WHERE job_applications.job_id = :jid 
                          AND (users.name LIKE :kw OR users.email LIKE :kw)");
        $this->db->bind(':jid', $job_id);
        $this->db->bind(':kw', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
}