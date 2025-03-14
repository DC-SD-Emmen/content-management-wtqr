<?php

class UserManager {

    private $conn;

    public function __construct($conn) { 
        $this->conn = $conn;
    }

    // Gebruiker invoegen (inserts user with regex validation)
    public function insertUser($username, $password) {
        $usernameRegex = "/^[a-zA-Z0-9]{5,20}$/"; // Restored your regex

        if (!preg_match($usernameRegex, $username)) {
            echo "Invalid username";
            return;
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Secure password hashing
            $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            echo "User inserted successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Gebruiker ophalen (retrieve user by username)
    public function getUser($username) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }

    // Nieuwe functie: Gebruiker ophalen via ID
    public function getUserById($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }

    // Nieuwe functie: Gebruiker verwijderen (delete user by ID)
    public function deleteUser($userId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            echo "User deleted successfully.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Nieuwe functie: Haal alle games van een gebruiker op
    public function getUserGames($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT g.* FROM games g 
                                         JOIN user_games ug ON g.id = ug.game_id 
                                         WHERE ug.user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Nieuwe functie: Wachtwoord bijwerken (secure password update)
    public function updatePassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            echo "Password updated successfully.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Koppeling tussen gebruiker en game maken (link user to a game)
    public function connection_user_games($user_id, $game_id) {
        try {
            $checkSql = "SELECT COUNT(*) FROM user_games WHERE user_id = :user_id AND game_id = :game_id";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bindParam(':user_id', $user_id);
            $checkStmt->bindParam(':game_id', $game_id);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) {
                echo "Connection between user and game already exists.";
                return false;
            }

            $sql = "INSERT INTO user_games (user_id, game_id) VALUES (:user_id, :game_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();

            echo "Connection successfully created!";
            return true;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
