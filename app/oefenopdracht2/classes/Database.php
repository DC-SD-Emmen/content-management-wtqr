<?php 

class Database {  

   // properties  
   private $serverName = "mysql"; 
   private $title = "root"; 
   private $genre = "root"; 
   private $dbName = "user_login"; 
   private $conn; 

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
