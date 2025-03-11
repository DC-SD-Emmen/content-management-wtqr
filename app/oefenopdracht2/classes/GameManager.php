<?php

    class GameManager {
  
         // houdt de databaseverbinding bij   
        private $conn;

        // constructor om de databaseverbinding te initialiseren
        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        public function insertData($data, $imageName) {
             // saniteer en valideer de invoervelden
            $title = htmlspecialchars($data['title'] ?? '');
            $genre = htmlspecialchars($data['genre'] ?? '');
            $platform = htmlspecialchars($data['selectedConsole'] ?? '');
            $release_year = htmlspecialchars($data['release_year'] ?? '');
            $rating = htmlspecialchars($data['rating'] ?? '');
            $developer = htmlspecialchars($data['developer'] ?? '');
            $image = htmlspecialchars($data['image'] ?? '');
            $description = htmlspecialchars($data['description'] ?? '');

             // reguliere expressies om elk veld te valideren
            $titleRegex = '/^[a-zA-Z0-9\s:,!()-]+$/'; 
            $genreRegex = '/^[a-zA-Z\s\-\']+$/';
            $platformRegex = '/^.*$/'; 
            $release_yearRegex = '/^.*$/'; 
            $ratingRegex = '/^([1-9](\.\d)?|10(\.0)?)$/';
            $developerRegex = '/^[a-zA-Z0-9]+$/';
            $imageRegex = '/^.*$/'; 
            $descriptionRegex = '/^[a-zA-Z0-9\s.,;:!?()\'\"-]+$/';

             // valideer elk veld en toon foutmeldingen als het niet geldig is
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
                 // als alle validaties slagen, probeer de game in de database in te voegen
                 //Bind parameter gebruiken we om SQL injection attack te voorkomen.
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

                    // vang database-gerelateerde fouten op en toon ze
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
        // loop door de resultaten en zet elke rij om in een Game object
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
    
            $games[] = $game; // voeg het game-object toe aan de lijst
        } 
         // toon een bericht als er geen games zijn gevonden
        if (empty($games)) {
            echo "<div>Geen games gevonden in de database.</div>";
         }
    
        return $games;
    }


    public function fetch_game_by_title($id) {
    
        $stmt = $this->conn->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $gameData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($gameData) {
            $game = new Game();
            $game->setID($gameData['id']);
            $game->set_title($gameData['title']);
            $game->set_description($gameData['description']);
            $game->set_developer($gameData['developer']);
            $game->set_genre($gameData['genre']);
            $game->set_platform($gameData['platform']);
            $game->set_release_year($gameData['release_year']);
            $game->set_rating($gameData['rating']);
            $game->set_image($gameData['image']);
            return $game;
        }
    }

    public function fetch_first_game_id() {
        $stmt = $this->conn->prepare("SELECT id FROM games ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $firstGame = $stmt->fetch(PDO::FETCH_ASSOC);
        return $firstGame ? $firstGame['id'] : null; // retourneer null als er geen game is gevonden
    }

    public function delete_data($game_id) {
        try {
            // valideer game-id
            if (!is_numeric($game_id)) {
                echo "Invalid game ID.";
                return;
            }

            // haal het afbeeldingspad op voor het spel

            $stmt = $this->conn->prepare("SELECT image FROM games WHERE id = :id");
            $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $image_path = 'uploads/' . $row["image"]; 
    
                // kijk of de file een bestaat en verwijder hem. 
                if (file_exists($image_path)) {
                    if (unlink($image_path)) {
                        echo "Image successfully removed: " . $image_path;
                    } else {
                        echo "Error while removing the image: " . $image_path;
                    }
                } else {
                    echo "Image not found: " . $image_path;
                }
            } else {
                echo "No image found for the specified game ID.";
            }
    
            // Verwijder de game uit de database
            $stmt = $this->conn->prepare("DELETE FROM games WHERE id = :id");
            $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
    
            echo "Successfully removed record.";
            // redirect naar index.php
            echo "<meta http-equiv='refresh' content='0;url=http://localhost/oefenopdracht2/index.php'>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
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
            echo "Bestand is geen afbeelding.";
            $uploadOk = 0;
        }


        // controleer of het bestand al bestaat
        if (file_exists($target_file)) {
            echo "Sorry, het bestand bestaat al.";
            $uploadOk = 0;
        }

        // controleer de bestandsgrootte
        if ($file["size"] > 5000000) {
            echo "Sorry, je bestand is te groot.";
            $uploadOk = 0;
        }

        // sta bepaalde bestandstypen toe
        if($imageFileType != "jpg" && $imageFileType != "webp" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan.";
            $uploadOk = 0;
        }

        // controleer of $uploadOk op 0 is gezet door een fout
        if ($uploadOk == 0) {
            echo "Sorry, je bestand werd niet geüpload.";
        // als alles goed is, probeer het bestand dan te uploaden
        } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            
            return True;
        } else {
            
            return False;
        }
        }
    }

}
?>
