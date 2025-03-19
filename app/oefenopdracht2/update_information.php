<?php
// start de sessie
session_start();

// automatisch laden van klassen
spl_autoload_register(function ($class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$userManager = new UserManager($database->getConnection());

$errorMessage = '';
$successMessage = '';

// verkrijg de huidige gebruiker
$userId = $_SESSION['user_id'];
$currentUser = $userManager->getUser($_SESSION['username']);

// Debug: Toon huidige gebruiker
echo "Current User: <pre>" . print_r($currentUser, true) . "</pre><br>";

// als de gebruiker niet bestaat, vernietig de sessie en stuur door naar login pagina
if (!$currentUser) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// verwerk formulierdata
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'] ?? ''; // verkrijg het huidige wachtwoord

    // Debug: Toon het ingevoerde wachtwoord
    echo "Entered Password: " . htmlspecialchars($currentPassword) . "<br>";

    // als de gebruiker de gebruikersnaam wil bijwerken
    if (isset($_POST['update_username'])) {
        $newUsername = htmlspecialchars($_POST['new_username'] ?? ''); // verkrijg de nieuwe gebruikersnaam

    if (!empty($newUsername)) {
        // controleer of het huidige wachtwoord overeenkomt met het opgeslagen wachtwoord
        if (!password_verify($currentPassword, $currentUser['password'])) {
            // Debug: Toon hash van ingevoerd wachtwoord
            echo "Entered Password Hash: " . password_hash($currentPassword, PASSWORD_DEFAULT) . "<br>";
            
            $errorMessage = "Current password is incorrect!";
        } else {
            // update de gebruikersnaam
            $result = $userManager->updateUserCredentials(
                $userId, 
                $newUsername,
                $currentUser['email'],
                $currentUser['password'],
                $currentUser['password']
            );
            if (strpos($result, 'successfully') !== false) {
                $_SESSION['username'] = $newUsername; // sla de nieuwe gebruikersnaam op in de sessie
                $successMessage = "Username successfully updated!";
            } else {
                $errorMessage = $result;
            }
        }
    } else {
        $errorMessage = "Username field cannot be empty!";
    }
}

    // als de gebruiker de e-mail wil bijwerken
    if (isset($_POST['update_email'])) {
        $newEmail = htmlspecialchars($_POST['new_email'] ?? ''); // krijg het nieuwe e-mailadres

    if (!empty($newEmail) && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        // controleer of het huidige wachtwoord overeenkomt met het opgeslagen wachtwoord
        if (!password_verify($currentPassword, $currentUser['password'])) {
            // Debug: Toon hash van ingevoerd wachtwoord
            echo "Entered Password Hash: " . password_hash($currentPassword, PASSWORD_DEFAULT) . "<br>";
            
            $errorMessage = "Current password is incorrect!";
        } else {
            // update het e-mailadres
            $result = $userManager->updateUserCredentials(
                $userId, 
                $currentUser['username'], 
                $newEmail,
                $currentUser['password'],
                $currentUser['password'] 
            );
            if (strpos($result, 'successfully') !== false) {
                $successMessage = "Email successfully updated!";
            } else {
                $errorMessage = $result;
            }
        }
    } else {
        $errorMessage = "Please enter a valid email address.";
    }
}


   // verwerk het bijwerken van het wachtwoord
   if (isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'] ?? ''; // verkrijg het nieuwe wachtwoord
        $confirmPassword = $_POST['confirm_password'] ?? ''; // verkrijg het bevestigde wachtwoord

        if (!empty($newPassword) && !empty($confirmPassword)) {
            if ($newPassword === $confirmPassword) {
                // Debug: Toon opgeslagen wachtwoordhash
                echo "Stored Password Hash: " . $currentUser['password'] . "<br>";

                // controleer of het huidige wachtwoord overeenkomt met het opgeslagen wachtwoord
                if (!password_verify($currentPassword, $currentUser['password'])) {
                    // Debug: Toon hash van ingevoerd wachtwoord
                    echo "Entered Password Hash: " . password_hash($currentPassword, PASSWORD_DEFAULT) . "<br>";
                    
                    $errorMessage = "Current password is incorrect!";
                } else {
                    // werk het wachtwoord bij
                    $result = $userManager->updateUserCredentials(
                        $userId, 
                        $currentUser['username'],
                        $currentUser['email'], 
                        $newPassword, 
                        $confirmPassword 
                    );
                    if (strpos($result, 'successfully') !== false) {
                        $successMessage = "Password successfully changed. Redirecting...";
                        echo "<script>setTimeout(() => window.location.href = 'user.php', 3000);</script>";
                    } else {
                        $errorMessage = $result;
                    }
                }
            } else {
                $errorMessage = "Passwords don't match!";
            }
        } else {
            $errorMessage = "All password fields must be filled!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="eindopdracht.css">
    <title>Update Credentials</title>
</head>
<body>
    <div class="topcontainer">
    <ul id="topbar"> 
                <li class="store" ><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>

                <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>

                <li class="community" ><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>

                <li class="addgame"> <a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>

                <li class="library">ACCOUNT</li> 
            </ul>

        <div class="containerupdate">
            <h2>Update Your Information</h2>

            <?php 
            // toon foutmelding of succesbericht
            if (!empty($errorMessage)) echo "<div id='error-message'>$errorMessage</div>";
            if (!empty($successMessage)) echo "<div id='redirect'>$successMessage</div>";
            ?>

            <form action="" method="post">
                <h3>Change Username</h3>
                <label for="new_username">New Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($currentUser['username'] ?? ''); ?>" required>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
                <input type="submit" name="update_username" value="Update Username">
            </form>     

            <form action="" method="post">
                <h3>Change E-mail</h3>
                <label for="new_email">New E-mail:</label>
                <input type="email" name="new_email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
                <input type="submit" name="update_email" value="Update Email">
            </form>

            <form action="" method="post">
                <h3>Change Password</h3>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>

                <input type="submit" name="update_password" value="Update Password">
                <p><a href="user.php">Back to Dashboard</a></p>

            </form>
        </div>
    </div>

    <script>
        // verberg berichten na 5 seconden
        setTimeout(() => {
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('redirect');
            if (errorMessage) errorMessage.style.display = 'none';
            if (successMessage) successMessage.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
