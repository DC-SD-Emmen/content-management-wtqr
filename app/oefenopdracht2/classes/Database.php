<?php 


class Database {  

// Properties
   private $serverName = "mysql";
   private $title = "root";
   private $genre = "root";
   private $dbName = "gamelibrary";
   private $conn;

 //Functies
   public function __construct(){
   try {
      $this->conn = new PDO("mysql:host={$this->serverName};dbname={$this->dbName}", $this->title, $this->genre);
       $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       } catch(PDOException $e) {
       echo "<div class='failed'> Connection failed: </div>" . $e->getMessage();
      }
   }

   public function getConnection() {
      return $this->conn;
   }
}

