<?php
class Database {
    // Teacher's Property Style
    private $host = DB_HOST;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $database = DB_NAME;
    
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        // Teacher's Connection Style
        $this->connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        
        // Bonus: Ensure utf8 (Teacher might not have this, but it prevents bugs)
        $this->connection->set_charset("utf8");
    }

    public function getConnection() {
        return $this->connection;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>