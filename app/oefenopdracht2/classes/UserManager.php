<?php

class UserManager {

    private $conn;

    // constructor om de databaseverbinding te starten
    public function __construct($conn) { 
        $this->conn = $conn;
    }

    // gebruiker invoegen
    public function insertUser($username, $password) {
        // regex 
        $usernameRegex = "/^[a-zA-Z0-9]{3,25}$/"; 

        // check username
        if (!preg_match($usernameRegex, $username)) {
            echo "<div class='error-message'>Invalid username</div>";
            return;
        }

        try {
            // wachtwoord hashen
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            // voorbereiden van de query om de gebruiker in te voegen
            $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            echo "<div id='redirect'>Account updated successfully! Redirecting in <span id='countdown'>3</span> seconds...</div>";
        } catch (PDOException $e) {
            echo "<div class='error-message'>error: </div>" . $e->getMessage();
        }
    }

    // gebruiker ophalen op basis van username
    public function getUser($username) {
        try {
            // voorbereiden van de query om de gebruiker op te halen
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            // resultaat ophalen als array
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            echo "<div class='error-message'>error: </div>" . $e->getMessage();
            exit();
        }
    }

public function updateUsername($userId, $newUsername) {
    // check usernname
    $usernameRegex = "/^[a-zA-Z0-9]{3,25}$/";
    if (!preg_match($usernameRegex, $newUsername)) {
        return "<div class='error-message' id='error-message'>Invalid username. It must be 3-25 characters long and contain only letters and numbers.</div>";
    }

    try {
        // kijk of username al bestaat  
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $stmt->bindParam(':username', $newUsername);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return "<div class='error-message' id='error-message'>Username is already taken.</div>";
        }

        // Update username
        $stmt = $this->conn->prepare("UPDATE users SET username = :username WHERE id = :id");
        $stmt->bindParam(':username', $newUsername);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        // Update sessie nadat name is veranderd
        $_SESSION['username'] = $newUsername;

        return "<div id='redirect'>Username updated successfully. Redirecting in <span id='countdown'>3</span> seconds...</div>";

    } catch (PDOException $e) {
        return "<div class='error-message' id='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// update email
public function updateEmail($userId, $newEmail) {
    // check email formaat
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        return "<div class='error-message' id='error-message'>Invalid email format.</div>";
    }

    try {
        // Check of email al bestaat
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $newEmail);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return "<div class='error-message' id='error-message'>Email is already in use.</div>";
        }

        // update email
        $stmt = $this->conn->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->bindParam(':email', $newEmail);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return "<div id='redirect'>Email updated successfully.</div>";

    } catch (PDOException $e) {
        return "<div class='error-message' id='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// update password
public function updatePassword($userId, $newPassword, $confirmPassword) {
    // check password lengte
    if (strlen($newPassword) < 3) {
        return "<div class='error-message' id='error-message'>Password must be at least 3 characters long.</div>";
    }

    // kijk of het password goed is
    if ($newPassword !== $confirmPassword) {
        return "<div class='error-message' id='error-message'>Passwords do not match.</div>";
    }

    // has de nieuwe password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    try {
        // update password
        $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return "<div id='redirect'>Password updated successfully.</div>";

    } catch (PDOException $e) {
        return "<div class='error-message' id='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}



    public function deleteGamesFromWishlist($userId) {
        try {
            // query om games van de verlanglijst te verwijderen
            $sql = "DELETE FROM user_games WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "<div class='error-message'>error: </div>" . $e->getMessage();
            exit();
        }
    }

    // verwijder gebruiker en de bijbehorende games
    public function deleteUserAndGames($userId) {
        try {
            // verwijder games van de verlanglijst van de gebruiker
            $this->deleteGamesFromWishlist($userId);

            // verwijder de gebruiker uit de database
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

        } catch (PDOException $e) {
            echo "<div class='error-message'>error: </div>" . $e->getMessage();
            exit();
        }
    }

    // koppeling tussen gebruiker en game maken 
    public function connection_user_games($user_id, $game_id) {
        try {
            // controleren of de koppeling tussen gebruiker en game al bestaat
            $checkSql = "SELECT COUNT(*) FROM user_games WHERE user_id = :user_id AND game_id = :game_id";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bindParam(':user_id', $user_id);
            $checkStmt->bindParam(':game_id', $game_id);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) {
                echo "<div class='error-message'>Connection between user and game already exists.</div>";
                return false;
            }

            // de koppeling invoegen in de database
            $sql = "INSERT INTO user_games (user_id, game_id) VALUES (:user_id, :game_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();

        } catch (PDOException $e) {
            echo "<div class='error-message'>error: </div>" . $e->getMessage();
            return false;
        }
    }

    // verwijder specifieke games uit de verlanglijst van de gebruiker
    public function deleteSpecificGamesFromWishlist($user_id, $game_id) {   
         try {
             // verwijder specifieke game uit de verlanglijst
             $sql = "DELETE FROM user_games WHERE user_id = :user_id AND game_id = :game_id";
             $stmt = $this->conn->prepare($sql);
             $stmt->bindParam(':user_id', $user_id);
             $stmt->bindParam(':game_id', $game_id);
             $stmt->execute();
         } catch (PDOException $e) {
             echo "<div class='error-message'>error: </div>" . $e->getMessage();
             exit();
         }
     }
    
}

?>
