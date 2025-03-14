<?php
    session_start(); // Start de session 

    spl_autoload_register(function ($class) {
        include 'classes/' . $class . '.php';
    });

    $db = new Database();
    $conn = $db->getConnection();

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: inloggen.php");
        exit;
    }

    if (isset($_GET['search'])) {
        $searchTerm = htmlspecialchars($_GET['search']);
        $games = $gameManager->searchGames($searchTerm);
    }
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
    <div class="gameGrid">
</body>
</html>

<?php
    $userManager = new UserManager($conn);
    $user = $userManager->getUser($_SESSION['username']);
    $user_id = $user['id'];

    if (isset($_GET['action']) && $_GET['action'] == 'add_to_wishlist' && isset($_GET['game_id'])) {

        echo "er is een action set";

        $game_id = intval($_GET['game_id']);
        $userManager->connection_user_games($user_id, $game_id);
    }

    $wishlistQuery = "
        SELECT games.id, games.title, games.image
        FROM games
        INNER JOIN user_games ON games.id = user_games.game_id
        WHERE user_games.user_id = :user_id;
    ";

    $stmt = $conn->prepare($wishlistQuery);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $wishlistGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h2>My Wishlist</h2>";
        echo "<div class='wishlistgrid'>";

    if (count($wishlistGames) > 0) {
            echo "<ul>";
        foreach ($wishlistGames as $game) {
            echo "<div class='wishlistColumn'>";
            echo '<a href="game_details.php?game_id=' . urlencode($game['id']) . '">';
            echo '<img id="imagetitle" src="uploads/' . htmlspecialchars($game['image']) . '" alt="' . htmlspecialchars($game['title']) . '"></a>'; 
            echo "<li>" . htmlspecialchars($game['title']) . "</li>";
            echo "</div>";
        }
            echo "</ul>";
            echo "</div>";
    } else {
        echo "<p>Your wishlist is empty.</p>";
    }



?>

<script src="background.js"></script>