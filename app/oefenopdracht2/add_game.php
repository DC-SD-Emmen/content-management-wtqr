<?php
    // start sessie voor toegang tot sessievariabelen
    session_start();

    // registreer autoload functie om klassen automatisch in te laden
    spl_autoload_register(function ($class) {
        include 'classes/' . $class . '.php';
    });

    // admin functie, zodat alleen 'wtqr' hier in kan
    // controleer of gebruiker is ingelogd en of de gebruikersnaam 'wtqr' is
    if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'wtqr') {
        header("Location: index.php"); // als niet ingelogd, redirect naar index
        exit();
    }

    $db = new Database();
    $gameManager = new GameManager($db);

    // controleer of het formulier is ingevuld via post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // probeer bestand te uploaden
        $uploadSuccess = $gameManager->fileUpload($_FILES['image']);
        // controleer of console is geselecteerd
        $consoleSelected = isset($_POST['selectedConsole']);

        // als bestand uploaden en console geselecteerd zijn, voer data in 
        // && staat voor 'en' s
        if ($uploadSuccess && $consoleSelected) {
            $gameManager->insertData($_POST, $_FILES['image']['name']);
            header('Location: index.php'); // redirect naar index na succesvolle invoer
        }

        // geef foutmelding als uploaden niet is gelukt
        if (!$uploadSuccess) {
            echo "<div class='error'>File upload failed.</div>";
        }

        // geef foutmelding als geen console is geselecteerd
        if (!$consoleSelected) {
            echo "<div class='error'>Please select a console before submitting.</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add game</title>
    <link rel='stylesheet' href='eindopdracht.css'>
    <script src="eindopdracht.js" defer></script>
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

            <li class="addgame2">ADD GAME</li> 

            <li class="account2"><a class="account1" href="./user.php"
            target="_explorer.exe">ACCOUNT</a></li> 
         </ul>
   </div>

   <div id="form">
         <!-- check of er iets in de dropdown is gekozen ↓  -->
        <form id='addgameform' method="post" onsubmit="return validateSelection()" enctype="multipart/form-data">

            <label id="title1" for="title"> Title </label>
            <input type="text" id="title" name="title" required> <br><br>

            <label id="genre1" for="genre"> Genre </label>
            <input type="text" id="genre" name="genre" required> <br><br><br>

            <label id="platform1" for="platform" name="platform"> Platform </label>
            <div class="dropdown">
                <label class="dropbtn" id="dropdownLabel" onclick="toggleDropdown()">Select console</label>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="#" onclick="selectConsole('PC')">Pc</a>
                    <a href="#" onclick="selectConsole('Xbox')">Xbox</a>
                    <a href="#" onclick="selectConsole('Playstation')">Playstation</a>
                    <a href="#" onclick="selectConsole('Nintendo Switch')">Nintendo switch</a>
                    <a href="#" onclick="selectConsole('Mobile')">Mobile</a>
                </div>
            </div>

            <input type="hidden" name="selectedConsole" id="selectedConsole">
    
            <div id="errorMessage" class="error" style="display: none;">Please select a console.</div>
            <br><br><br>

            <label id="release_year1" for="release_year"> Release year </label> 
            <input type="date" id="release_year" name="release_year" required> <br><br>

            <label id="rating1" for="rating"> Rating </label>
            <input type="range" id="rating" name="rating" min="1.0" max="10.0" step="0.1" required 
                oninput="this.nextElementSibling.value = parseFloat(this.value).toFixed(1)">
                <!-- bij bewegen slider update het getal ernaast, afronden op 1 decimaal -->
            <output for="rating">5.0</output><br><br> <!-- laat standaard 5.0 zien -->  


            <label id="developer1" for="developer"> Developer </label>
            <input type="text" id="developer" name="developer" required> <br><br>

            <label id="image1" for="image"> Image </label>
            <input type="file" id="image" name="image" required> <br><br>

            <label id="description1" for="description"> Description </label>
            <textarea id="description" name="description" required></textarea> <br><br>

            <input type="submit" id="submit" name="submit">
       
        <script>  
            // validatie functie voor formulier
            function validateForm(event) {
            const title = document.getElementById('title').value;
            const genre = document.getElementById('genre').value;
            const platform = document.getElementById('platform').value;
            const releaseYear = document.getElementById('release_year').value;
            const rating = document.getElementById('rating').value;
            const developer = document.getElementById('developer').value;
            const image = document.getElementById('image').value;
            const description = document.getElementById('description').value;

                // controleer of alle velden ingevuld zijn
                if (!title || !genre || !platform || !releaseYear || !rating || !developer || !image || !description) {
                alert("Please fill in all fields.");
                return false;
            }

            return true;
        }
        </script>

        </form>
    </div>
    <div id='resultaten-container'>
    </div>
    <div class="library">
</div>

</div>
</body> 
</html>
