<?php

// start sessie voor toegang tot sessievariabelen
session_start();

// controleer of de gebruiker ingelogd is, zo niet doorverwijzen naar login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game library</title> 
    <link rel='stylesheet' href='eindopdracht.css'>
    <script src="eindopdracht.js" defer></script>
</head>
<body>
    <div id="topcontainer">    
            <ul>
                <li class="store" ><a class="store1" href="https://store.steampowered.com/"
                target="_explorer.exe">STORE</a></li>

                <li class="library">LIBRARY</a></li>

                <li class="community" ><a class="community1" href="https://steamcommunity.com/"
                target="_explorer.exe">COMMUNITY</a></li>

                <li class="addgame"> <a class="submit" href="./add_game.php"
                target="_explorer.exe">ADD GAME</a></li>

                <li class="account"><a class="account1" href="./user.php"
                target="_explorer.exe">ACCOUNT</a></li> 
            </ul>

    <?php
        // autoload functie voor het inladen van klassen
        spl_autoload_register(function ($class) {
            include 'classes/' . $class . '.php';
        });

        // maak verbinding met de database en haal GameManager op
        $db = new Database();
        $GameManager = new GameManager($db);
    ?>
                    
                    
<div id="loader">
    <div class="loading-text">Loading...</div>
</div>

<div id="content" class="hidden">
    <div id="page">
        <div class="navigation">
            <!-- navigatie link voor route selectie -->
            <a class="active" href="" onClick="onRouteClick('a'); return false;">
                <div class="loader"></div>
                <div class="container"></div>
            </a>
            <div class="libraryGame">
                <div class="sidebar animated">
                    <!-- sidebar titel voor alle games -->
                    <h1 id="AllToggle"> - All games </h1> 
                    <?php 
                    // haal alle games op uit de database
                    $games = $GameManager->fetch_all_games(); 
                    ?>
                    <?php foreach ($games as $game): ?>
                        <div class="gameSidebarItem animated-element">
                            <!-- toon een link naar de detailpagina van een spel -->
                            <a href="game_details.php?game_id=<?php echo $game->getID(); ?>">
                                <!-- toon afbeelding van het spel in de sidebar -->
                                <img src="uploads/<?php echo htmlspecialchars($game->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="sidebarGameImage">
                                <!-- toon de titel van het spel -->
                                <span class="gameTitle"><?php echo htmlspecialchars($game->get_title()); ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>


                <div class="gameGrid">
                    <?php foreach ($games as $game): ?>
                        <div class="animated-element">
                            <!-- toon een grid item voor elk spel met een afbeelding -->
                            <a href="game_details.php?game_id=<?php echo $game->getID(); ?>">
                                <img src="uploads/<?php echo htmlspecialchars($game->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="gameImage">
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <div id="sidebarbar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
