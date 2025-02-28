<?php
    $host = "mysql";
    $dbname = "my-wonderful-website";
    $charset = "utf8";
    $port = "3306";
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>

<h2>Register</h2>
<div id="form">
    <form action="index.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Register">

        <p>Already have an account? <a href="login.php">Login here</a></p>

    </form>

</div>

</body>
</html>

<?php
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo '<div id="text">Registration successful! <a href="login.php">Login here</a></div>';
    } else {
        echo '<div id="text">An error occurred during registration.</div>';
    }    
}
?>