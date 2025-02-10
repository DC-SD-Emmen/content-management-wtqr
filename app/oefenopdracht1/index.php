<?php

$host = "mysql"; // Le host est le nom du service, présent dans le docker-compose.yml
$dbname = "my-wonderful-website";
$charset = "utf8";
$port = "3306";
?>

<?php
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "Registratie succesvol!";
    } else {
        echo "Er is een fout opgetreden bij de registratie.";
    }
}
?>
<html>

<head>

<title>Register</title>
</head>
<body>

    <h2>Register</h2>
    <form action="index.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>

<body>

</body>

</html>
