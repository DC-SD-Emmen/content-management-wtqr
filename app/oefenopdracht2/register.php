<?php
session_start();

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

$messages = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    // Validate username (only letters and numbers, 3-25 characters)
    $usernameRegex = "/^[a-zA-Z0-9]{3,25}$/"; 
    if (!preg_match($usernameRegex, $username)) {
        $errors[] = "Invalid username. It must be 3-25 characters long and contain only letters and numbers.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if username or email already exists in the database
    if (empty($errors)) {
        $database = new Database();
        $conn = $database->getConnection();

        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        $userExists = $checkStmt->fetchColumn();

        if ($userExists > 0) {
            $errors[] = "Username or email already exists. Please choose another.";
        }
    }

    // If no errors, register the user
    if (empty($errors)) {
        register_user($username, $email, $password);
    }
}

// Function to register the user
function register_user($username, $email, $password) {
    global $errors, $messages;

    $database = new Database();
    $conn = $database->getConnection();

    // Hash the password securely
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $passwordHash);

    if ($stmt->execute()) {
        $messages[] = "You have been registered successfully. Redirecting in <span id='countdown'>3</span> seconds...";
    } else {
        $errors[] = "An error occurred during registration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="eindopdracht.css">
    <title>Registration</title>
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

        <h2>Registration</h2>

        <div id="message-container">
            <?php foreach ($errors as $error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endforeach; ?>

            <?php foreach ($messages as $message): ?>
                <div id="redirect"><?php echo $message; ?></div>
            <?php endforeach; ?>
        </div>

        <script>
            setTimeout(() => {
                const messageContainer = document.getElementById('message-container');
                if (messageContainer) {
                    messageContainer.style.display = 'none';
                }
            }, 5000);

            if (document.getElementById('redirect')) {
                let countdown = 3;
                const countdownElement = document.getElementById('countdown');
                const interval = setInterval(() => {
                    countdown--;
                    if (countdownElement) countdownElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        window.location.href = 'login.php';
                    }
                }, 1000);
            }
        </script>

        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" class="username1" name="username" required>

            <label for="email">Email:</label>
            <input type="email" class="email1" name="email" required>

            <label for="password">Password:</label>
            <input type="password" class="password1" name="password" required>

            <input type="submit" name="submit" value="Register">

            <p>Already have an account? <a href="login.php">Log in here</a></p>
        </form>
    </div>
</body>
</html>
