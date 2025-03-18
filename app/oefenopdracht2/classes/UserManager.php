<?php

class UserManager {

    private $conn;

    public function __construct($conn) { 
        $this->conn = $conn;
    }

    // Gebruiker invoegen (inserts user with regex validation)
    public function insertUser($username, $password) {
        $usernameRegex = "/^[a-zA-Z0-9]{3,25}$/"; // Restored your regex

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

    public function updateUserCredentials($userId, $newUsername, $newEmail, $newPassword, $confirmPassword) {
        $errorMessage = '';
    
        // Validate username (only letters/numbers, 3-25 chars)
        $usernameRegex = "/^[a-zA-Z0-9]{3,25}$/";
        if (!preg_match($usernameRegex, $newUsername)) {
            return "<div class='error-message' id='error-message'>Invalid username. It must be 3-25 characters long and contain only letters and numbers.</div>";
        }
    
        // Validate email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return "<div class='error-message' id='error-message'>Invalid email format.</div>";
        }
    
        // Password validation: Check if password is at least 3 characters
        if (strlen($newPassword) < 3) {
            return "<div class='error-message' id='error-message'>Password must be at least 3 characters long.</div>";
        }
    
        // Check if passwords match
        if ($newPassword !== $confirmPassword) {
            return "<div class='error-message' id='error-message'>Passwords do not match.</div>";
        }
    
        try {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->bindParam(':username', $newUsername);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return "<div class='error-message' id='error-message'>Username is already taken.</div>";
            }
    
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->bindParam(':email', $newEmail);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return "<div class='error-message' id='error-message'>Email is already in use.</div>";
            }
    
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
            if ($newUsername) {
                $stmt = $this->conn->prepare("UPDATE users SET username = :username WHERE id = :id");
                $stmt->bindParam(':username', $newUsername);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
            }
    
            if ($newEmail) {
                $stmt = $this->conn->prepare("UPDATE users SET email = :email WHERE id = :id");
                $stmt->bindParam(':email', $newEmail);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
            }
    
            if ($newPassword) {
                $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
            }
    
            return "<div id='redirect'>Account updated successfully! Redirecting in <span id='countdown'>3</span> seconds...</div>";
    
        } catch (PDOException $e) {
            return "<div class='error-message' id='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    public function deleteGamesFromWishlist($userId) {
        try {
            $sql = "DELETE FROM user_games WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }

    // Delete user and their associated games
    public function deleteUserAndGames($userId) {
        try {
            // Delete games from the user's wishlist
            $this->deleteGamesFromWishlist($userId);

            // Now delete the user from the database
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
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

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    
    }

     public function deleteSpecificGamesFromWishlist($user_id, $game_id) {   
         try {
             $sql = "DELETE FROM user_games WHERE user_id = :user_id AND game_id = :game_id";
             $stmt = $this->conn->prepare($sql);
             $stmt->bindParam(':user_id', $user_id);
             $stmt->bindParam(':game_id', $game_id);
             $stmt->execute();
         } catch (PDOException $e) {
             echo "Error: " . $e->getMessage();
             exit();
         }
     }
    
}