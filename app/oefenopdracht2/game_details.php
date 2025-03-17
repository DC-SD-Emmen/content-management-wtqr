<?php 
session_start(); 
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$game_id = $_GET["game_id"];
$db = new Database();
$GameManager = new GameManager($db);
$singleGame = $GameManager->fetch_game_by_title($game_id); 
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game-details</title> 
    <link rel='stylesheet' href='eindopdracht.css'>
    <script src="eindopdracht.js" defer></script>
</head>
<body id="detail-body">

<div id="top-container-detail">
    <div class="gameDetails">
        <div class="gameHeader">
            <img src="uploads/<?php echo htmlspecialchars($singleGame->get_image()); ?>" alt="<?php echo htmlspecialchars($singleGame->get_title()); ?>" class="gameDetailImage">
            <div class="gameInfo">
                <h1><?php echo htmlspecialchars($singleGame->get_title()); ?></h1>
                <p class="developer"><strong>Developer:</strong> <?php echo htmlspecialchars($singleGame->get_developer()); ?></p>
                <p class="genre"><strong>Genre:</strong> <?php echo htmlspecialchars($singleGame->get_genre()); ?></p>
                <p class="platform"><strong>Platform:</strong> <?php echo htmlspecialchars($singleGame->get_platform()); ?></p>
                <p class="releaseYear"><strong>Release Year:</strong> <?php echo date("d/m/Y", strtotime($singleGame->get_release_year())); ?></p>
                <p class="rating"><strong>Rating:</strong> <?php echo htmlspecialchars($singleGame->get_rating()); ?>/10</p>
                <a href="wishlist.php?action=add_to_wishlist&game_id=<?php echo $game_id; ?>" class="add_to_wishlist">Add to wishlist 📃</a> <br>
            </div>
        </div>

        <div class="description">
            <h2>About This Game</h2>
            <p><?php echo nl2br(htmlspecialchars($singleGame->get_description())); ?></p>
        </div>

        <div class="libraryButton">
            <button id="libraryButton">Back to library</button>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['username']) && $_SESSION['username'] === 'wtqr') {
?>
    <div id="deletebuttonDiv">
        <form id="deletebuttonForm" method="POST">
            <input type="submit" id="yesDeletebutton" name="deleteButon" value="Delete">
        </form> 
    </div>
<?php
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteButon'])) {
        $GameManager->delete_data($game_id);
    }
}
?>
</body>
</html>

