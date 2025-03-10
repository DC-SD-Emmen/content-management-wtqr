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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $errors = [];

    if (empty($username) || empty($password)) {
        $errors[] = "<div class='error-message' id='error-message'>Username and Password required</div>";
        echo "<script>
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                    }
                }, 5000); // Hide the error message after 5 seconds
              </script>";
    }

    if (empty($errors)) {
        register_user($username, $password);
    } else {
        echo '<div style="color: red;">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
}

function register_user($username, $password) {
    $database = new Database();
    $conn = $database->getConnection();

    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
        echo "<div class='error-message' id='error-message'>Username already exists. Please choose a different username.</div>";
        echo "<script>
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                    }
                }, 5000); // Hide the error message after 5 seconds
              </script>";
        return;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "<div id='redirect'>You have been registered. Redirecting in <span id='countdown'>3</span> seconds...</div>";
        echo "<script>
                let countdown = 3;
                const countdownElement = document.getElementById('countdown');
                const interval = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        window.location.href = 'login.php';
                    }
                }, 1000);
              </script>";
    } else {
        echo "<div class='error-message' id='error-message'>An error occurred during registration.</div>";
        echo "<script>
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                    }
                }, 5000); // Hide the error message after 5 seconds
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Registration</title>
</head>

<body>
    <h2>Registration</h2>
    <form action="" method="post">

        <label for="username">Username:</label>
        <input type="text" class="username1" name="username" required>

        <label for="password">Password:</label>
        <input type="password" class="password1" name="password" required>

        <input type="submit" name="submit" value="Register">

        <p>Already have an account? <a href="login.php">Log in here</a></p>

        <div id="redirect-container"></div>
    </form>
</body>

</html>
