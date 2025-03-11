<?php

    class UserManager {

        private $conn;

        public function __construct( $conn ) { 
            $this->conn = $conn;
        }

        public function insertUser($username, $password) {

            $usernameRegex = "/^[a-zA-Z0-9]{5,20}$/";

            if (!preg_match($usernameRegex, $username)) {
                echo "Invalid username";
                return;
            }

            $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->close();
        }
    }

