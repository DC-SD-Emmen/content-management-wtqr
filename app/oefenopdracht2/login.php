<?php
// start sessie en buffering voor pagina-output
session_start();
ob_start();

// autoload functie voor het inladen van klassen
spl_autoload_register(function ($class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// foutmelding variabele voor het tonen van foutmeldingen
$errorMessage = '';

// verwerk formulier als de request methode POST is
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

// functie om wachtwoord te verifiëren
function verify_password($username, $password) {
    global $errorMessage;
    // maak database verbinding
    $database = new Database();
    $conn = $database->getConnection();

    // controleer of de database verbinding is gelukt
    if (!$conn) {
        $errorMessage = "<div class='error-message' id='error-message'>Database connection failed.</div>";
        return;
    }

    // bereid SQL statement voor om gebruiker op te halen
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    // controleer of de gebruiker bestaat en of het wachtwoord klopt
    if ($user && password_verify($password, $user['password'])) {
        // regenereer sessie-ID voor veiligheid
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        // stop de output buffering en stuur de gebruiker door naar hun accountpagina
        ob_end_clean();
        header("Location: user.php?id=" . urlencode($user['id']));
        exit;
    } else {
        // als de login mislukt, toon een foutmelding
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
            <!-- navigatielinks naar verschillende pagina's -->
            <li class="store"><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>
            <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>
            <li class="community"><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>
            <li class="addgame"><a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>
            <li class="library">ACCOUNT</li>
        </ul>

        <h2>Login</h2>

        <!-- toon foutmelding indien aanwezig -->
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

        <!-- login formulier -->
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" class="username2" name="username" required>

            <label for="email">Email:</label>
            <input type="email" class="email1" name="email" required>

            <label for="password">Password:</label>
            <input type="password" class="password2" name="password" required>

            <input type="submit" name="submit" value="login">

            <!-- link naar registratiepagina -->
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
