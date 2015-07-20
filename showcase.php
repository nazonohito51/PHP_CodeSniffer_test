<?php

require_once('item.php');

class Showcase
{
    var $items;
    
    function validateItem($item) {
        if (is_a($item, Item) !== true) return false;
        if ($item->checkValid() !== true) return false;
        
        return true;
    }
    
    function Showcase() {
        $this->items = array();
    }
    
    function addItem($item) {
        $this->items[] = $item;
    }
    
    function searchItemById($id) {
        if (array_key_exists($id, $this->items) === false) return false;
        
        return $this->items[$id];
    }
    
    function searchIdByName($name) {
        foreach ($this->items as $id => $item) {
            if ($item->getName() === $name) return $id;
        }
        
        return null;
    }
}

?>
