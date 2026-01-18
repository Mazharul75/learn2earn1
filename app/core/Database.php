<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    public $conn;

    // The teacher's code calls this method explicitly
    public function getConnection() {
        $this->conn = null;

        try {
            // Switch to MySQLi as required
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to ensure special characters work
            $this->conn->set_charset("utf8");

        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>