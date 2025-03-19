<?php
    session_start(); 

    spl_autoload_register(function ($class) {
        include 'classes/' . $class . '.php';
    });

    // databaseverbinding maken en GameManager aanmaken
    $db = new Database();
    $conn = $db->getConnection();
    $gameManager = new GameManager($db);

    // controleer of de gebruiker is ingelogd, zo niet redirect naar login pagina
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    // haal gebruikersinformatie op en sla het user-id op
    $userManager = new UserManager($conn);
    $user = $userManager->getUser($_SESSION['username']);
    $user_id = $user['id'];

    // voeg een game toe aan de wishlist
    if (isset($_GET['action']) && $_GET['action'] == 'add_to_wishlist' && isset($_GET['game_id'])) {
        $game_id = intval($_GET['game_id']); // zet game_id om naar een integer dus een heel getal
        $userManager->connection_user_games($user_id, $game_id); // voeg de game toe aan de wishlist
    }

    // verwijder een specifieke game van de wishlist
    if (isset($_GET['action']) && $_GET['action'] == 'deleteSpecificGamesFromWishlist' && isset($_GET['game_id'])) {
        $game_id = intval($_GET['game_id']); // zet game_id om naar een integer dus een heel getal
        $userManager->deleteSpecificGamesFromWishlist($user_id, $game_id); // verwijder de game uit de wishlist
        header("Location: wishlist.php"); // herlaad de pagina na het verwijderen
        exit;
    }

    // haal alle games uit de wishlist op
    $wishlistGames = $gameManager->getGamesFromWishlist($user_id);


?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="eindopdracht.css">
    <title>Wishlist</title>
</head>
<body>

    <div id="topcontainer">
        
        <div id="top">        
            <ul>
                <!-- navigatiebalk voor de website -->
                <li class="store2"><a class="store1" href="https://store.steampowered.com/" target="_explorer.exe">STORE</a></li>
                <li class="library2"><a class="submit2" href="./index.php" target="_explorer.exe">LIBRARY</a></li>
                <li class="community2"><a class="community1" href="https://steamcommunity.com/" target="_explorer.exe">COMMUNITY</a></li>
                <li class="addgame"><a class="submit" href="./add_game.php" target="_explorer.exe">ADD GAME</a></li>
                <li class="account2"><a class="account1" href="./user.php" target="_explorer.exe">ACCOUNT</a></li> 
                <li class="library">WISHLIST</a></li>
            </ul>
        </div>
        <div>
                <?php    
                    echo "<h2>My Wishlist</h2>"; 
                    echo "<div class='whishlistgrid'>"; // begin van de grid voor games
                    if (count($wishlistGames) > 0) { // controleer of er games in de wishlist staan
                            
                        foreach ($wishlistGames as $game) { // loop door de games in de wishlist
                            echo "<div class='gameGridItem'>"; 
                            echo '<a href="game_details.php?game_id=' . urlencode($game['id']) . '">'; // link naar de game details
                            echo '<img class="gameImage" id="imagetitle" src="uploads/' . htmlspecialchars($game['image']) . '" alt="' . htmlspecialchars($game['title']) . '"></a>'; // toon de afbeelding van de game
                            echo '<span style="margin-right: 10px;">' . htmlspecialchars($game['title']) . '</span>'; // toon de titel van de game
                            echo '<a href="wishlist.php?action=deleteSpecificGamesFromWishlist&game_id=' . urlencode($game['id']) . '" class="add_to_wishlist">Remove</a>'; // verwijder de game uit de verlanglijst
                            echo "</div>"; 
                        }
                            echo "</div>"; // grid einde
                    } else {
                        echo "<div class='wishlistempty'>Your wishilist is empty</div>"; // als er niks is laat dat zien
                    }

                ?>
            </div>
    </div>
</body>
</html>
