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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $errors = [];

    if (empty($username) || empty($password)) {
        $errors[] = "<div class='error-message' id='error-message'>Username and password are required   .</div>";
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
        verify_password($username, $password);
    } else {
        echo '<div style="color: red;">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
}

function verify_password($username, $password) {
    $database = new Database();
    $conn = $database->getConnection();

    $username = htmlspecialchars($username);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        ob_end_clean(); 

        header("Location: user.php?id=" . urlencode($user['username']) . $user['id']);
        exit; 
    } else {
        echo
        "<div class='error-message' id='error-message'>Invalid username or password.</div>";
        echo "<script>
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                    }
                }, 5000); // Hide the error message after 5 seconds
        </script>";  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <form action="" method="post">
        <label for="username">Username:</label>
        <input type="text" class="username2" name="username" required>

        <label for="password">Password:</label>
        <input type="password" class="password2" name="password" required>

        <input type="submit" name="submit" value="Login">

        <p>Don't have an account? <a href="index.php">Register here</a></p>
    </form>
</body>

</html>
