<?php
session_start();

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

// controleer of user_id in de url staat

if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);  // zet user_id om naar een geheel getal (dus wat intval doet)

    $db = new Database();
    $userManager = new UserManager($db->getConnection());

    // verwijder de gebruiker en alle bijbehorende games

    $userManager->deleteUserAndGames($userId);

    session_unset();   // haal alle sessievariabelen weg
    session_destroy(); // vernietig de sessie volledig

    // stuur de gebruiker na verwijdering door naar de index, wat dus automatisch naar login.php gaat (ik had natuurlijk ook gewoon naar login.php kunnen doen maar dit werkt ook)

    header("Location: index.php");  
    exit;
} else {
    echo "User ID is missing."; // geef een melding als er geen user_id is
}

?>
