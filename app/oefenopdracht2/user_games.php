<?php 
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$db = new Database();
$conn = $db->getConnection();

// Voeg een nieuwe koppeling toe tussen gebruiker met ID 1 en game met ID 3
$insertQuery = "INSERT INTO user_games (user_id, game_id) VALUES (1, 3);";
$conn->exec($insertQuery);

// haal alle games op die door gebruiker met ID 2 worden gespeeld
$selectGamesQuery = "
SELECT games.title
FROM games
INNER JOIN user_games ON games.id = user_games.game_id
WHERE user_games.user_id = 2;
";
$stmt = $conn->query($selectGamesQuery);  // voer de query uit om de games op te halen
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);  // verkrijg de resultaten van de query als een associatieve array
echo "Games played by user with ID 2:<br>";  // toon een bericht dat aangeeft welke games worden weergegeven
foreach ($games as $game) {
    echo $game['title'] . "<br>";  // toon de titel van elke game die door de gebruiker met ID 2 wordt gespeeld
}

// haal alle gebruikers op die de game met ID 4 spelen
$selectUsersQuery = "
SELECT users.username
FROM users
INNER JOIN user_games ON users.id = user_games.user_id
WHERE user_games.game_id = 4;
";
$stmt = $conn->query($selectUsersQuery);  // voer de query uit om de gebruikers op te halen
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);  // verkrijg de resultaten van de query als een associatieve array
echo "Users who play the game with ID 4:<br>";  // toon een bericht dat aangeeft welke gebruikers worden weergegeven
foreach ($users as $user) {
    echo $user['username'] . "<br>";  // toon de gebruikersnaam van elke gebruiker die de game met ID 4 speelt
}

?>
    