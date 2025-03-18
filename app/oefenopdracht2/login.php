<?php
session_start();
ob_start();

$host = "mysql";
$dbname = "my-wonderful-website";
$charset = "utf8";
$port = "3306";

spl_autoload_register(function ($class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = trim(htmlspecialchars($_POST['password'] ?? ''));

    if (empty($username) || empty($password)) {
        $errorMessage = "<div class='error-message' id='error-message'>Username and password are required.</div>";
    } else {
        verify_password($username, $password);
    }
}

function verify_password($username, $password) {
    global $errorMessage;
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        $errorMessage = "<div class='error-message' id='error-message'>Database connection failed.</div>";
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

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

        <?php if (!empty($errorMessage)): ?>
            <?php echo $errorMessage; ?>
            <script>
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

            <input type="submit" name="submit" value="Login">

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
