<?php

class Game {
    // properties  
    private $id; 
    private $title; 
    private $genre; 
    private $platform; 
    private $release_year; 
    private $rating;
    private $developer; 
    private $image; 
    private $description; 

    // id instellen en ophalen  
    public function setID($id) {
        $this->id = $id;
    }

    public function getID() {
        return $this->id;
    }

    // titel instellen en ophalen  
    public function set_title($title) {
        $this->title = $title;
    }

    public function get_title() { 
        return $this->title;
    }

    // genre instellen en ophalen  
    public function set_genre($genre) {
        $this->genre = $genre;
    }

    public function get_genre() { 
        return $this->genre;
    }

    // platform instellen en ophalen  
    public function set_platform($platform) {
        $this->platform = $platform;
    }
    
    public function get_platform() { 
        return $this->platform;
    }

    // release year instellen en ophalen  
    public function set_release_year($release_year) {
        $this->release_year = $release_year;
    }
        
    public function get_release_year() { 
        return $this->release_year;
    }

    // rating instellen en ophalen  
    public function set_rating($rating) {
        $this->rating = $rating;
    }
    
    public function get_rating() { 
        return $this->rating;
    }
    
    // developer instellen en ophalen  
    public function set_developer($developer) {
        $this->developer = $developer;
    }
    
    public function get_developer() { 
        return $this->developer;
    }
    
    // afbeelding instellen en ophalen  
    public function set_image($image) {
        $this->image = $image;
    }
    
    public function get_image() { 
        return $this->image;
    }
    
    // beschrijving instellen en ophalen  
    public function set_description($description) {
        $this->description = $description;
    }
    
    public function get_description() { 
        return $this->description;
    }
}

?>
