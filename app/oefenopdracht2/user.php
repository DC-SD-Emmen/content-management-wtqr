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
    <br>   
    <form>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div id="loggedIn">You are logged in.</div>
        <p><a id="LoggedOut" href="user.php?action=logout">Logout</a></p>
    </form>
</div>
</body>
</html>
