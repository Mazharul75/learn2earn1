<?php
class Job extends Model {
    public function addJob($data) {
        $this->db->query("INSERT INTO jobs (client_id, title, description, required_course_id) 
                          VALUES (:client_id, :title, :description, :rcid)");
        $this->db->bind(':client_id', $_SESSION['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':rcid', $data['required_course_id']);
        return $this->db->execute();
    }

    public function getAllJobs() {
        $this->db->query("SELECT * FROM jobs");
        return $this->db->resultSet();
    }
    
    public function getJobById($id) {
        $this->db->query("SELECT * FROM jobs WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getJobsByClient($id) {
        $this->db->query("SELECT * FROM jobs WHERE client_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->resultSet();
    }

    
}