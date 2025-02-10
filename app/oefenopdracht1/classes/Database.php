<?php
    class Database {

        private $servername = "mysql";
        private $username = "root";
        private $password = "root";
        private $privatedb =  "user_login";
        private $dbname = "user_login";
        private $conn;

        //bij het aanmaken van een nieuw object, wordt construct functie automatisch direct uitgevoerd
        public function __construct() {
            try {
                $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // echo "Connected successfully";

            } catch (PDOException $e) {
                error_log("Connection failed: " . $e->getMessage());
            }
        }

        public function getConnection() {
            return $this->conn;
        }

    }

?>