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
$conn = $database->getConnection();
$userManager = new UserManager($conn);

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = htmlspecialchars($_POST['new_username'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userId = $_SESSION['user_id'];

    // Validate fields
    if (empty($newUsername) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "<div class='error-message' id='error-message'>All fields are required.</div>";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "<div class='error-message' id='error-message'>Passwords do not match.</div>";
    } else {
        $user = $userManager->getUser($_SESSION['username']);

        // Check if the current password matches the one in the database
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $errorMessage = "<div class='error-message' id='error-message'>Current password is incorrect.</div>";
        } else {
            // Update credentials
            $result = $userManager->updateUserCredentials($userId, $newUsername, $newPassword, $confirmPassword);
            
            // If the update was successful, update session username
            if (strpos($result, 'successfully') !== false) {
                $_SESSION['username'] = $newUsername;
                $successMessage = $result;
            } else {
                $errorMessage = $result;
            }
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
            if (!empty($errorMessage)) echo $errorMessage;
            if (!empty($successMessage)) echo $successMessage;
            ?>

            <form action="" method="post">
                <label for="new_username">New Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($newUsername ?? ''); ?>" required>

                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>

                <input type="submit" value="Update Information">
            </form>

            <p><a href="user.php">Back to Dashboard</a></p>
        </div>
    </div>

    <script>
    // Countdown and Redirect logic
    if (document.getElementById('redirect')) {
        let countdown = 3;  // Start countdown at 3 seconds
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            if (countdownElement) countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = 'user.php';  // Redirect to user.php after countdown finishes
            }
        }, 1000);
    }
</script>

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
