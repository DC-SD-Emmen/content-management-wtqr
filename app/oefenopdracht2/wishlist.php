<?php
    session_start(); // Start de session 

    spl_autoload_register(function ($class) {
        include 'classes/' . $class . '.php';
    });

    $db = new Database();
    $conn = $db->getConnection();
    $gameManager = new GameManager($db);

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    if (isset($_GET['search'])) {
        $searchTerm = htmlspecialchars($_GET['search']);
        $games = $gameManager->searchGames($searchTerm);
    }

    $userManager = new UserManager($conn);
    $user = $userManager->getUser($_SESSION['username']);
    $user_id = $user['id'];

    if (isset($_GET['action']) && $_GET['action'] == 'add_to_wishlist' && isset($_GET['game_id'])) {

        $game_id = intval($_GET['game_id']);
        $userManager->connection_user_games($user_id, $game_id);
    }

    if (isset($_GET['action']) && $_GET['action'] == 'deleteSpecificGamesFromWishlist' && isset($_GET['game_id'])) {
         $game_id = intval($_GET['game_id']);
         $userManager->deleteSpecificGamesFromWishlist($user_id, $game_id);
         header("Location: wishlist.php"); 
         exit;
     }

   //hier games ophalen uit gamemanager, wishlist functionaliteit
    $wishlistGames = $gameManager->getGamesFromWishlist($user_id);

?>

<!DOCTYPE html>
<html lang="en">
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
                <li class="store2" ><a class="store1" href="https://store.steampowered.com/"
                target="_explorer.exe">STORE</a></li>

                <li class="library2"><a class="submit2" href="./index.php"
                target="_explorer.exe">LIBRARY</a></li>

                <li class="community2"><a class="community1" href="https://steamcommunity.com/"
                target="_explorer.exe">COMMUNITY</a></li>

                <li class="addgame"> <a class="submit" href="./add_game.php"
                target="_explorer.exe">ADD GAME</a></li>

                <li class="account2"><a class="account1" href="./user.php"
                target="_explorer.exe">ACCOUNT</a></li> 

                <li class="library">WISHLIST</a></li>
            </ul>
        </div>
        <div>
                <?php    
                    echo "<h2>My Wishlist</h2>";
                    echo "<div class='whishlistgrid'>";
                    if (count($wishlistGames) > 0) {
                            
                        foreach ($wishlistGames as $game) {
                            echo "<div class='gameGridItem'>";
                            echo '<a href="game_details.php?game_id=' . urlencode($game['id']) . '">';
                            echo '<img class="gameImage" id="imagetitle" src="uploads/' . htmlspecialchars($game['image']) . '" alt="' . htmlspecialchars($game['title']) . '"></a>'; 
                            echo '<span style="margin-right: 10px;">' . htmlspecialchars($game['title']) . '</span>';
                            echo '<a href="wishlist.php?action=deleteSpecificGamesFromWishlist&game_id=' . urlencode($game['id']) . '" class="add_to_wishlist">Remove</a>';
                            echo "</div>";
                        }
                            echo "</div>";
                    } else {
                        echo "<div class='wishlistempty'>Your wishlist is empty.</div>";
                    }

                ?>
            </div>
    </div>
</body>
</html>

