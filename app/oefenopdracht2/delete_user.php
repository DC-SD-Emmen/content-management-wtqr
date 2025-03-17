<?php
session_start();

// Autoload classes (ensure this works with your directory structure)
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

// Check if user_id is set in the URL
if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);  // Ensure user_id is an integer

    // Create an instance of the Database and UserManager classes
    $db = new Database();
    $userManager = new UserManager($db->getConnection());

    // Call the deleteUserAndGames method to delete both the user's games and the user
    $userManager->deleteUserAndGames($userId);

    // Optional: Destroy the session (if the user was logged in)
    session_unset();   // Remove all session variables
    session_destroy(); // Destroy the session

    // Redirect after successful deletion
    header("Location: index.php");  // Redirect to the homepage or login page
    exit;
} else {
    echo "User ID is missing.";
}
?>
