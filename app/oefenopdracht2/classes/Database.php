<?php 

class Database {  

   // properties  
   private $serverName = "mysql"; // naam van de server  
   private $title = "root"; // gebruikersnaam voor database  
   private $genre = "root"; // wachtwoord voor database  
   private $dbName = "user_login"; // naam van de database  
   private $conn; // connectie variabele  

   // constructor functie  
   public function __construct(){
      try {
         // maakt connectie met de database  
         $this->conn = new PDO("mysql:host={$this->serverName};dbname={$this->dbName}", $this->title, $this->genre);
         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // zet error mode aan  
      } catch(PDOException $e) {
         echo "<div class='failed'> Connection failed: </div>" . $e->getMessage(); // als het fout gaat laat error zien  
      }
   }

   public function getConnection() {
      return $this->conn; // geeft de database connectie terug  
   }
}
