<?php

$host = "mysql";
$dbname = "my-wonderful-website";
$charset = "utf8";
$port = "3306";


spl_autoload_register(function ($class) {
    include __DIR__ . '/' . $class . '.php';
});

session_start(); 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="eindopdracht.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="topcontainer">
            <ul id="topbar"> 
                <li class="store" ><a class="store1" href="https://store.steampowered.com/"
                target="_explorer.exe">STORE</a></li>

                <li class="library2"><a class="submit2" href="./index.php"
                target="_explorer.exe">LIBRARY</a></li>

                <li class="community" ><a class="community1" href="https://steamcommunity.com/"
                target="_explorer.exe">COMMUNITY</a></li>

                <li class="addgame"> <a class="submit" href="./add_game.php"
                target="_explorer.exe">ADD GAME</a></li>

                <li class="library">ACCOUNT</li> 
            </ul>
            <div class="container">
    <br>
    <form id="user">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <div id="loggedIn">You are logged in.</div>

    <div style="display: flex; align-items: center; gap: 15px; text-align: center;">
        <p><a id="LoggedOut" href="user.php?action=logout">Logout</a></p>
        <p><a id="Wishlist" href="wishlist.php">Wishlist</a></p>
        <p><a id="UpdateInformation" href="update_information.php">Update Information</a></p>
        <a href="delete_user.php?user_id=<?php echo $_SESSION['user_id']; ?>" onclick="return confirm('Are you sure you want to delete your account and all your wishlist games? This action cannot be undone.');">Delete Account</a>
    </div> 


    </form>
</div>
</body>
</html>
