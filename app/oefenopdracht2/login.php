<?php
session_start();
ob_start();

spl_autoload_register(function ($class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// voor het tonen van foutmeldingen
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // haal en reinig gebruikersinvoer
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = trim(htmlspecialchars($_POST['password'] ?? ''));

    // controleer of gebruikersnaam en wachtwoord ingevuld zijn
    if (empty($username) || empty($password)) {
        $errorMessage = "<div class='error-message' id='error-message'>Username and password are required.</div>";
    } else {
        // controleer wachtwoord via de verify_password functie
        verify_password($username, $password);
    }
}

// functie om wachtwoord te checken
function verify_password($username, $password) {
    global $errorMessage;
    $database = new Database();
    $conn = $database->getConnection();

    // controleer of de database verbinding is gelukt
    if (!$conn) {
        $errorMessage = "<div class='error-message' id='error-message'>Database connection failed.</div>";
        return;
    }

    // SQL statement om gebruiker op te halen
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    // controleer of de gebruiker bestaat en of het wachtwoord klopt
    if ($user && password_verify($password, $user['password'])) {
        // regenereer sessie-ID voor veiligheid
        // wordt gedaan zodat hackers niet je sessie kunnen stelen 
        session_regenerate_id(true); 
       
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        // was even een simpele fix om de header te laten werken
        ob_end_clean();
        header("Location: user.php?id=" . urlencode($user['id']));
        exit;
    } else {
        $errorMessage = "<div class='error-message' id='error-message'>Invalid username or password.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="eindopdracht.css">
    <title>Login</title>
</head>

<body>
    <div class="topcontainer">
        <ul id="topbar">
            <li class="store"><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>
            <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>
            <li class="community"><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>
            <li class="addgame"><a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>
            <li class="library">ACCOUNT</li>
        </ul>

        <h2>Login</h2>

        <!-- toon foutmelding als er iets fout is -->
        <?php if (!empty($errorMessage)): ?>
            <?php echo $errorMessage; ?>
            <script>
                // verberg de foutmelding na 5 seconden
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                    }
                }, 5000);
            </script>
        <?php endif; ?>

        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" class="username2" name="username" required>

            <label for="email">Email:</label>
            <input type="email" class="email1" name="email" required>

            <label for="password">Password:</label>
            <input type="password" class="password2" name="password" required>

            <input type="submit" name="submit" value="login">

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
