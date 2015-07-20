<?php

class Item
{
    var $name;
    var $price;
    var $release_date;
    var $stock;
    
    function validateName($name) {
        if (is_string($name) !== true) return false;

        $str_len = strlen($name);
        if ($str_len < 1 || 30 < $str_len) return false;
        
        return true;
    }
    
    function validatePrice($price) {
        if (is_int($price) !== true) return false;

        if ($price < 1 || 1000000 < $price) return false;
        
        return true;
    }
    
    function validateReleaseDate($release_date) {
        if (is_string($release_date) !== true) return false;

        $pattern = '/^\d{4}\/\d{2}$/';
        if (preg_match($pattern, $release_date) == false) return false;
        
        return true;
    }
    
    function validateStock($stock) {
        if (is_int($stock) !== true) return false;

        if ($stock < 1 || 1000000 < $stock) return false;
        
        return true;
    }
    
    function checkValid() {
        if ($this->validateName($this->name) !== true) return false;
        if ($this->validatePrice($this->price) !== true) return false;
        if ($this->validateReleaseDate($this->release_date) !== true) return false;
        if ($this->validateStock($this->stock) !== true) return false;
        
        return true;
    }
    
    function Item($name, $price, $release_date, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->release_date = $release_date;
        $this->stock = $stock;
    }
    
    function getName() {
        return $this->name;
    }
    
    function getStock() {
        return $this->stock;
    }
    
    function reservation($quantity) {
        if (is_int($quantity) !== true) return false;
        if ($this->getStock() < $quantity) return false;
        
        $this->stock -= $quantity;
        return true;
    }
    
    function calculateCharge($quantity) {
        if (is_int($quantity) !== true) return false;
        if ($quantity <= 0) return false;
        
        return $this->price * $quantity;
    }
}

?>
