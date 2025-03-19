<?php

session_start(); 

spl_autoload_register(function ($class) {
    include __DIR__ . '/' . $class . '.php';
});

// controleer of gebruiker is ingelogd, zo niet, doorverwijzen naar inlogpagina
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// uitlogfunctie
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="eindopdracht.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="topcontainer">
            <ul id="topbar"> 
                <li class="store" ><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>

                <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>

                <li class="community" ><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>

                <li class="addgame"> <a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>

                <li class="library">ACCOUNT</li> 
            </ul>
            <div class="container">
    <br>
    <form id="user">
    <h2 class="user">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <div id="loggedIn">You are logged in.</div>

    <div style="display: flex; align-items: center; gap: 10px; text-align: center;margin-left: 14.5%;>">
        <p><a id="LoggedOut" href="user.php?action=logout">Logout</a></p>
        <p><a id="Wishlist" href="wishlist.php">Wishlist</as></p>
        <p><a id="UpdateInformation" href="update_information.php">Change Information</a></p>
        <a href="delete_user.php?user_id=<?php echo $_SESSION['user_id']; ?>" onclick="return confirm('Weet je zeker dat je je account en alle wenslijst games wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.');">Delete account</a>
    </div> 

    <?php 
        if (isset($error_message)) echo "<div class='error-message'>$error_message</div>";
        if (isset($success_message)) echo "<div class='success-message'>$success_message</div>";
    ?>
</div>
</body>
</html>
