<?php
require_once __DIR__ . '/../../config/database.php';

class JobApplication {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    // =========================================================
    // SMART APPLY: UPDATE if invited, INSERT if new
    // =========================================================
    public function apply($job_id, $learner_id, $cv_file) {
        // 1. Check existing record
        $existing = $this->alreadyApplied($job_id, $learner_id);

        if ($existing) {
            // SCENARIO 1: INVITED USER APPLIES
            // We UPDATE the existing row: Add CV and change status to 'applied'
            $query = "UPDATE job_applications SET cv_file = ?, status = 'applied' WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("si", $cv_file, $existing['id']);
            return $stmt->execute();
        } else {
            // SCENARIO 2: NEW APPLICANT
            // We INSERT a new row
            $status = 'applied';
            $query = "INSERT INTO job_applications (job_id, learner_id, cv_file, status) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("iiss", $job_id, $learner_id, $cv_file, $status);
            return $stmt->execute();
        }
    }

    public function getApplicantsByJob($job_id) {
        $query = "SELECT users.name, users.email, job_applications.id as app_id, 
                  job_applications.status, job_applications.cv_file, job_applications.learner_id 
                  FROM job_applications 
                  JOIN users ON job_applications.learner_id = users.id 
                  WHERE job_applications.job_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function alreadyApplied($job_id, $learner_id) {
        $query = "SELECT * FROM job_applications WHERE job_id = ? AND learner_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $job_id, $learner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateStatus($app_id, $status) {
        $query = "UPDATE job_applications SET status = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $status, $app_id);
        return $stmt->execute();
    }

    public function selectLearner($app_id) {
        $status = 'selected';
        $query = "UPDATE job_applications SET status = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $status, $app_id);
        return $stmt->execute();
    }

    public function getLearnerApplications($learner_id) {
        $query = "SELECT jobs.title, job_applications.status 
                  FROM job_applications 
                  JOIN jobs ON job_applications.job_id = jobs.id 
                  WHERE job_applications.learner_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $learner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchApplicants($job_id, $keyword) {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT users.name, users.email, job_applications.id as app_id, 
                  job_applications.status, job_applications.cv_file 
                  FROM job_applications 
                  JOIN users ON job_applications.learner_id = users.id 
                  WHERE job_applications.job_id = ? 
                  AND (users.name LIKE ? OR users.email LIKE ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iss", $job_id, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function recordInvitation($job_id, $learner_id) {
        if ($this->alreadyApplied($job_id, $learner_id)) {
            return true; 
        }

        $query = "INSERT INTO job_applications (job_id, learner_id, cv_file, status) VALUES (?, ?, '', 'invited')";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $job_id, $learner_id);
        return $stmt->execute();
    }
}
?>