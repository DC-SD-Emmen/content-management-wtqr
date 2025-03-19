<?php
session_start();

spl_autoload_register(function ($class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$userManager = new UserManager($database->getConnection());

$errorMessage = '';
$successMessage = '';

$userId = $_SESSION['user_id'];
$currentUser = $userManager->getUser($_SESSION['username']);

if (!$currentUser) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'] ?? '';

    if (isset($_POST['update_username'])) {
        $newUsername = htmlspecialchars($_POST['new_username'] ?? '');
        
        if (!empty($newUsername)) {
            $result = $userManager->updateUserCredentials(
                $userId, 
                $newUsername,
                $currentUser['email'],
                $currentUser['password'],
                $currentUser['password']
            );
            if (strpos($result, 'successfully') !== false) {
                $_SESSION['username'] = $newUsername;
                $successMessage = "Username updated successfully!";
            } else {
                $errorMessage = $result;
            }
        } else {
            $errorMessage = "Username field cannot be empty!";
        }
    }

    if (isset($_POST['update_email'])) {
        $newEmail = htmlspecialchars($_POST['new_email'] ?? '');
        
        if (!empty($newEmail) && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $result = $userManager->updateUserCredentials(
                $userId, 
                $currentUser['username'], 
                $newEmail,
                $currentUser['password'],
                $currentUser['password'] 
            );
            if (strpos($result, 'successfully') !== false) {
                $successMessage = "Email updated successfully!";
            } else {
                $errorMessage = $result;
            }
        } else {
            $errorMessage = "Please enter a valid email address!";
        }
    }

   // Handle updating the password
   if (isset($_POST['update_password'])) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            // Check if the current password entered matches the stored hashed password
            if (!password_verify($currentPassword, $currentUser['password'])) {
                $errorMessage = "Current password is incorrect!";
            } else {
                // Proceed with updating the password
                $result = $userManager->updateUserCredentials(
                    $userId, 
                    $currentUser['username'],
                    $currentUser['email'], 
                    $newPassword, // New password entered by the user
                    $confirmPassword // Confirm password entered by the user
                );
                if (strpos($result, 'successfully') !== false) {
                    $successMessage = "Password updated successfully! Redirecting...";
                    echo "<script>setTimeout(() => window.location.href = 'user.php', 3000);</script>";
                } else {
                    $errorMessage = $result;
                }
            }
        } else {
            $errorMessage = "Passwords do not match!";
        }
    } else {
        $errorMessage = "All password fields are required!";
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
            <li class="store"><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>
            <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>
            <li class="community"><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>
            <li class="addgame"><a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>
            <li class="library">ACCOUNT</li> 
        </ul>

        <div class="containerupdate">
            <h2>Update Your Information</h2>

            <?php 
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
            </form>

            <p><a href="user.php">Back to Dashboard</a></p>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('redirect');
            if (errorMessage) errorMessage.style.display = 'none';
            if (successMessage) successMessage.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
