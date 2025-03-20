<?php
session_start();

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
$newUsername = '';  // Initialize $newUsername to avoid the undefined variable error

// verkrijg de huidige gebruiker
$userId = $_SESSION['user_id'];
$currentUser = $userManager->getUser($_SESSION['username']);

// als de gebruiker niet bestaat, vernietig de sessie en stuur door naar login pagina
if (!$currentUser) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// verwerk formulierdata
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // haal het ingevoerde wachtwoord op
    $password = $_POST['current_password'] ?? ''; 

    // haal de meest recente gebruikersgegevens op
    if (!isset($_SESSION['username'])) {
        die("Error: User is not logged in.");
    }

    // haal de gegevens op van de huidige gebruiker uit de database op basis van de gebruikersnaam die is opgeslagen in de sessie
    $currentUser = $userManager->getUser($_SESSION['username']);

    // controleer of we echt een gebruiker hebben opgehaald
    if (!$currentUser || empty($currentUser['password'])) {
        die("Error: No userdata found.");
    }

    // username update
    if (isset($_POST['update_username'])) {
        $newUsername = htmlspecialchars($_POST['new_username'] ?? ''); 
        if (!empty($newUsername)) {
            // controleer of het ingevoerde wachtwoord klopt
            if (!password_verify($password, $currentUser['password'])) {
                $errorMessage = "Current password is wrong!";
            } else {
                // controleer of het woord 'successfully' in de foutmelding staat, wat aangeeft dat de update geslaagd is
                // dat doet strpos ook (string position) die checkt waar de string staat                
                if (strpos($errorMessage, 'successfully') !== false) {
                    $_SESSION['username'] = $newUsername;
                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'update_information.php';
                            }, 2000);
                        </script>";
                }
            }
        } else {
            $errorMessage = "The username field cant be empty";
        }
    }

    // USERNAME VERANDERD NIET MEER,
    // ALLLE CHECKS WERKEN MAAR HIJ VERANDERD DAN NIET,
    // EMAIL WERKT PERFECT,
    // NADAT DE PASSWORD IS VERANDERD REFRESHED HIJ DE PAGINA GOED, MAAR HIJ WORDT DAN UITGELOGD
    // MISSCHIEN WORDT DE SESSION ERGENS WEGGEHAALD? GEEN IDEE. 


    // update email 
    if (isset($_POST['update_email'])) {
        $newEmail = htmlspecialchars($_POST['new_email'] ?? ''); 
        if (!empty($newEmail)) {
            // controleer of het ingevoerde wachtwoord klopt
            if (!password_verify($password, $currentUser['password'])) {
                $errorMessage = "Current password is wrong!";
            } else {
                // update email
                $errorMessage = $userManager->updateEmail($currentUser['id'], $newEmail);
                // controleer of het woord 'successfully' in de foutmelding staat, wat aangeeft dat de update geslaagd is
                // dat doet strpos ook (string position) die checkt waar de string staat 
                if (strpos($errorMessage, 'successfully') !== false) {
                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'update_information.php';
                            }, 2000);
                          </script>";
                }
            }
        } else {
            $errorMessage = "Please enter a valid E-mail.";
        }
    }

    // update wachtwoord
    if (isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'] ?? ''; 
        $confirmPassword = $_POST['confirm_password'] ?? ''; 
        // csontroleer of het wachtwoord en de bevestiging niet leeg zijn
        if (!empty($newPassword) && !empty($confirmPassword)) {
            // check of wachtwoorden tzelfde zijn
            if ($newPassword === $confirmPassword) {
                // check of de ingevoerde wachtwoord klopt
                if (!password_verify($password, $currentUser['password'])) {
                    $errorMessage = "Current password is wrong!";
                } else {
                    // update wachtwoord
                    $errorMessage = $userManager->updatePassword($currentUser['id'], $newPassword, $confirmPassword);
                    // controleer of het woord 'successfully' in de foutmelding staat, wat aangeeft dat de update geslaagd is
                    // dat doet strpos ook (string position) die checkt waar de string staat                     

                    if (strpos($errorMessage, 'successfully') !== false) {
                        $_SESSION['username'] = $newUsername;
                        echo "<script>
                                setTimeout(function() {
                                    window.location.href = 'update_information.php';
                                }, 2000);
                            </script>";
                    }
                }
            } else {
                $errorMessage = "Passwords don't match!";
            }
        } else {
            $errorMessage = "All password fields need to be filled in!";
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
    <title>Update Information

    </title>
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

            <form id="formupdate" action="" method="post">
                <h3>Change Username</h3>
                <label for="new_username">New Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($currentUser['username'] ?? ''); ?>" required>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
                <input type="submit" name="update_username" value="Update Username">
            </form>     

            <form id="formupdate" action="" method="post">
                <h3>Change E-mail</h3>
                <label for="new_email">New E-mail:</label>
                <input type="email" name="new_email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
                <input type="submit" name="update_email" value="Update Email">
            </form>

            <form id="formupdate" action="" method="post">
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
