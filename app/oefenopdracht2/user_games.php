<?php 
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$db = new Database();
$conn = $db->getConnection();

// Voeg een nieuwe koppeling toe tussen gebruiker met ID 1 en game met ID 3
$insertQuery = "INSERT INTO user_games (user_id, game_id) VALUES (1, 3);";
$conn->exec($insertQuery);

// Haal alle games op die door gebruiker met ID 2 worden gespeeld
$selectGamesQuery = "
SELECT games.title
FROM games
INNER JOIN user_games ON games.id = user_games.game_id
WHERE user_games.user_id = 2;
";
$stmt = $conn->query($selectGamesQuery);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Games gespeeld door gebruiker met ID 2:<br>";
foreach ($games as $game) {
    echo $game['title'] . "<br>";
}

// Haal alle gebruikers op die de game met ID 4 spelen
$selectUsersQuery = "
SELECT users.username
FROM users
INNER JOIN user_games ON users.id = user_games.user_id
WHERE user_games.game_id = 4;
";
$stmt = $conn->query($selectUsersQuery);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Gebruikers die de game met ID 4 spelen:<br>";
foreach ($users as $user) {
    echo $user['username'] . "<br>";
}
?>
    