<?php

    class GameManager {
  
         // houdt de connectie bij
        private $conn;

        // constructor om de verbinding te maekn
        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        public function insertData($data, $imageName) {
             // filter en check de inputs
            $title = htmlspecialchars($data['title'] ?? '');
            $genre = htmlspecialchars($data['genre'] ?? '');
            $platform = htmlspecialchars($data['selectedConsole'] ?? '');
            $release_year = htmlspecialchars($data['release_year'] ?? '');
            $rating = htmlspecialchars($data['rating'] ?? '');
            $developer = htmlspecialchars($data['developer'] ?? '');
            $image = htmlspecialchars($data['image'] ?? '');
            $description = htmlspecialchars($data['description'] ?? '');

             // regGEX
            $titleRegex = '/^[a-zA-Z0-9\s:,!()-]+$/'; 
            $genreRegex = '/^[a-zA-Z\s\-\']+$/';
            $platformRegex = '/^.*$/'; 
            $release_yearRegex = '/^.*$/'; 
            $ratingRegex = '/^([1-9](\.\d)?|10(\.0)?)$/';
            $developerRegex = '/^[a-zA-Z0-9]+$/';
            $imageRegex = '/^.*$/'; 
            $descriptionRegex = '/^[a-zA-Z0-9\s.,;:!?()\'\"-]+$/';

             // valideer elk veld 
            if (!preg_match($titleRegex, $title)) {
                echo "<div class='invalidTitle'> titel is ongeldig </div>";
            } else if(!preg_match($genreRegex, $genre)) {
                echo "<div class='invalidGenre'> genre is ongeldig </div>";
            } else if(!preg_match($platformRegex, $platform)) {
                echo "<div class='invalidPlatform'> platform is ongeldig </div>";
            } else if(!preg_match($release_yearRegex, $release_year)) {
                echo "<div class='invalidReleaseYear'> releasejaar is ongeldig </div>";
            } else if(!preg_match($ratingRegex, $rating)) { 
                echo "<div class='invalidRating'> beoordeling is ongeldig </div>";
            } else if(!preg_match($developerRegex, $developer)) { 
                echo "<div class='invalidDeveloper '> ontwikkelaar is ongeldig </div>";
            } else if(!preg_match($imageRegex, $image)) { 
                echo "<div class='invalidImage'> afbeelding is ongeldig </div>";
            } else if(!preg_match($descriptionRegex, $description)) { 
                echo "<div class='invalidDescription'> beschrijving is ongeldig </div>";    
            } else { 
                 // als alles goed is, voeg t toe aan de datatabase
                 // bind peramitor gebruiken we om sql injectie attack te voorkomen
                try {
                    $stmt = $this->conn->prepare("INSERT INTO games (title , genre,  platform, release_year, rating, developer, image, description) 
                                                        VALUES (:title , :genre,  :platform, :release_year, :rating, :developer, :image, :description)");
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':genre', $genre);
                    $stmt->bindParam(':platform', $platform);
                    $stmt->bindParam(':release_year', $release_year);
                    $stmt->bindParam(':rating', $rating);
                    $stmt->bindParam(':developer', $developer);
                    $stmt->bindParam(':image', $imageName);
                    $stmt->bindParam(':description', $description);
                    $stmt->execute(); 
                    
                    } catch(PDOException $e) {
                       echo "<div class='error'> Fout: </div>" . $e->getMessage();

                    // vind database fouten op en toon ze
                   echo "<script>window.location.href = 'http://localhost/eindopdracht/index.php';</script>";
                   return true;
       
               } catch (PDOException $e) {
                   echo "<div class='error'>Fout: " . $e->getMessage() . "</div>";
                   return false;   
            }
        }
    }

 public function fetch_all_games() {
        $stmt = $this->conn->prepare("SELECT * FROM games");
        $stmt->execute();
        // bekijkt de resultaten en zet elke rij in een object
        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $game = new Game();
            $game->setID($row['id']);
            $game->set_title($row['title']);
            $game->set_developer($row['developer']);
            $game->set_description($row['description']);
            $game->set_genre($row['genre']);
            $game->set_platform($row['platform']);
            $game->set_release_year($row['release_year']);
            $game->set_rating($row['rating']);
            $game->set_image($row['image']);
    
            $games[] = $game; // voegt het game object toe aan de lijst
        } 
         // error
        if (empty($games)) {
            echo "<div>Geen games gevonden in de database.</div>";
         }
    
        return $games;
    }


    public function fetch_game_by_title($id) {
        // perpared de query om een game op te halen 
        $stmt = $this->conn->prepare("SELECT * FROM games WHERE id = :id");
        
        // bind het id aan de query
        $stmt->bindParam(':id', $id);
        
        // voer de query uit 
        $stmt->execute();
        
        // haal de gegevens op van de game
        $gameData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // check of er data is
        if ($gameData) {
            $game = new Game();
            
            // stelt de specificaties van de game in
            $game->setID($gameData['id']);
            $game->set_title($gameData['title']);
            $game->set_description($gameData['description']);
            $game->set_developer($gameData['developer']);
            $game->set_genre($gameData['genre']);
            $game->set_platform($gameData['platform']);
            $game->set_release_year($gameData['release_year']);
            $game->set_rating($gameData['rating']);
            $game->set_image($gameData['image']);
            
            //  return game + data
            return $game;
        }
    }
    

    public function fetch_first_game_id() {
        // prepared query om eerste game van rij te vinden 
        $stmt = $this->conn->prepare("SELECT id FROM games ORDER BY id ASC LIMIT 1");
        $stmt->execute();

        // fetch resultaat
        $firstGame = $stmt->fetch(PDO::FETCH_ASSOC);

        // geeft eerste game terug als die er is, anders geef nul
        return $firstGame ? $firstGame['id'] : null; 
    }


    public function delete_data($game_id) {
        try {
            // check game id
            if (!is_numeric($game_id)) {
                echo "Invalid game ID.";
                return;
            }
    
            // begin transactie zodat beide deletes goed gaan
            $this->conn->beginTransaction();

    
            // haal eerst de game uit de user wishlist
            $stmt = $this->conn->prepare("DELETE FROM user_games WHERE game_id = :game_id");
            $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
    
            // haal de image path op
            $stmt = $this->conn->prepare("SELECT image FROM games WHERE id = :id");
            $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row) {
                $image_path = 'uploads/' . $row["image"];
    
                // kijk of ie bestaat en verwijderem dan
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
    
            // verwijder de game uit de games table
            $stmt = $this->conn->prepare("DELETE FROM games WHERE id = :id");
            $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
    
            // commit maakt het zo alles permanent is 
            $this->conn->commit();

    
            echo "<div id='redirect'>Successfully removed record.</div>";
            echo "<meta http-equiv='refresh' content='0;url=http://localhost/oefenopdracht2/index.php'>";
        } catch (PDOException $e) {
            // rollback de veranderingen als er iets fout gaat om consistentie te behouden
            $this->conn->rollBack();
            echo "<div class='message-error'>Error: </div>" . $e->getMessage();
        }
    }
    

    public function fileUpload($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // controleer of het bestand een afbeelding is of geen afbeelding

        $check = getimagesize($file["tmp_name"]); 
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<div class='error-message'>Bestand is geen afbeelding.</div>";
            $uploadOk = 0;
        }


        // controleer of het bestand al bestaat
        if (file_exists($target_file)) {
            echo "<div class='error-message'>Sorry, het bestand bestaat al.</div>";
            $uploadOk = 0;
        } 
        // controleer de bestandsgrootte
        if ($file["size"] > 5000000) {
            echo "<div class='error-message'>Sorry, je bestand is te groot.</div>";
            $uploadOk = 0;
        }

        // sta bepaalde bestandstypen toe
        if($imageFileType != "jpg" && $imageFileType != "webp" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "<div class='error-message'>Sorry, alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan.</div>";
            $uploadOk = 0;
        }

        // controleer of $uploadOk op 0 is gezet door een fout
        if ($uploadOk == 0) {
            echo "<div class='error-message'>Sorry, je bestand werd niet geüpload.</div>";
        // als alles goed is, probeer het bestand dan te uploaden
        } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            
            return True;
        } else {
            
            return False;
        }
        }
    }

    public function getGamesFromWishlist($user_id) {
        // query om de games uit de wishlist van een gebruiker op te halen
        $wishlistQuery = "
        SELECT games.id, games.title, games.image
        FROM games
        INNER JOIN user_games ON games.id = user_games.game_id
        WHERE user_games.user_id = :user_id;
        ";

        // bind de user_id parameter
        $stmt = $this->conn->prepare($wishlistQuery);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        // haal resultaten in elke rij van een array
        $wishlistGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // geef de lijst van games terug
        return $wishlistGames;
    }
    

}
?>
