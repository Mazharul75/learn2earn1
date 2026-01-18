<?php
class Database {
    // FIX: Hardcode the values here. Do NOT use DB_HOST.
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';      // Default XAMPP password is empty
    private $dbname = 'learn2earn'; // Your Database Name
    
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->dbname
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset("utf8");
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>