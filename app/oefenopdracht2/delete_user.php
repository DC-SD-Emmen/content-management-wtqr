<?php

session_start();
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['action']) && $_GET['action'] == 'deleteUser' && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);  // Get the user_id from the URL, make sure it's an integer

    // Create an instance of the UserManager with your database connection
    $db = new Database();
    $userManager = new UserManager($db->getConnection());

    try {
        // Step 1: Delete all games associated with this user
        $userManager->deleteGamesFromWishlist($userId);  // First delete the games

        // Step 2: Delete the user from the users table
        $userManager->deleteUser($userId);  // Now delete the user

        // Optional: Destroy the session (if the user was logged in)
        session_unset();   // Remove all session variables
        session_destroy(); // Destroy the session

        // Redirect after successful deletion
        header("Location: index.php");  // Redirect to the homepage or login page
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
