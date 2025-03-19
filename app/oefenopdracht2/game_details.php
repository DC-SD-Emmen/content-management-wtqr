<?php 
// start sessie voor toegang tot sessievariabelen
session_start(); 

// registreer autoload functie om klassen automatisch in te laden
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

// haal het game_id op uit de URL parameter
$game_id = $_GET["game_id"];

$db = new Database();
$GameManager = new GameManager($db);

// haal de specifieke game op via het game_id
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
            <!-- toon afbeelding van het spel -->
            <img src="uploads/<?php echo htmlspecialchars($singleGame->get_image()); ?>" alt="<?php echo htmlspecialchars($singleGame->get_title()); ?>" class="gameDetailImage">
            <div class="gameInfo">
                <h1><?php echo htmlspecialchars($singleGame->get_title()); ?></h1>
                <p class="developer"><strong>Developer:</strong> <?php echo htmlspecialchars($singleGame->get_developer()); ?></p>
                <p class="genre"><strong>Genre:</strong> <?php echo htmlspecialchars($singleGame->get_genre()); ?></p>
                <p class="platform"><strong>Platform:</strong> <?php echo htmlspecialchars($singleGame->get_platform()); ?></p>
                <p class="releaseYear"><strong>Release year:</strong> <?php echo date("d/m/Y", strtotime($singleGame->get_release_year())); ?></p>
                <p class="rating"><strong>Rating:</strong> <?php echo htmlspecialchars($singleGame->get_rating()); ?>/10</p>
                <a href="wishlist.php?action=add_to_wishlist&game_id=<?php echo $game_id; ?>" class="add_to_wishlist">Add to wishlist 📃</a> <br>
            </div>
        </div>

        <div class="description">
            <h2>About this game</h2>
            <!-- toon de beschrijving van het spel -->
            <p><?php echo nl2br(htmlspecialchars($singleGame->get_description())); ?></p>
        </div>

        <div class="libraryButton">
            <!-- knop om terug naar de bibliotheek te gaan -->
            <button id="libraryButton">Back to library</button>
        </div>
    </div>
</div>

<?php
// controleer of de gebruiker is ingelogd met de naam 'wtqr'
// admin functie: alleen wtqr kan deze verwijder knop zien en gebruiken
if (isset($_SESSION['username']) && $_SESSION['username'] === 'wtqr') {
?>
    <div id="deletebuttonDiv">
        <!-- formulier om het spel te verwijderen -->
        <form id="deletebuttonForm" method="POST">
            <input type="submit" id="yesDeletebutton" name="deleteButon" value="delete">
        </form> 
    </div>
<?php
}

// als het formulier wordt ingediend, verwijder het spel
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteButon'])) {
        $GameManager->delete_data($game_id); // verwijder het spel uit de database
    }
}
?>
</body>
</html>
