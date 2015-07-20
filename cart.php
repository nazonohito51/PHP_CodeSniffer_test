<?php

require_once('item.php');
require_once('showcase.php');

class Cart
{
    var $items;
    
    var $shipping_method_enum = array(
        'normal' => 0,
        'express' => 1,
    );
    var $term_of_payment_enum = array(
        'creditcard' => 0,
        'cache_on_delivery' => 1,
        'convenience_store' => 2,
    );
    
    function Cart() {
        $this->items = array();
    }
    
    function getItemList() {
        $ret = array();
        
        // 将来的に$this->itemsの形式が変わっても、一定の形式のarrayを返せるようにする
        foreach($this->items as $item) {
            $tmp = array();
            $tmp['id'] = $item['id'];
            $tmp['quantity'] = $item['quantity'];
            
            $ret[] = $tmp;
        }
        
        return $ret;
    }
    
    function insertItem($id, $quantity) {
        // 既にカート内にある商品なら、数量を加算する
        foreach ($this->items as $key => $item) {
            if ($item['id'] === $id) {
                $this->items[$key]['quantity'] += $quantity;
                return true;
            }
        }
        
        // カート内にない商品なら、新しく追加する
        $this->items[] = array('id' => $id, 'quantity' => $quantity);
        return true;
    }
    
    function calculateTortalPrice($shipping_method, $term_of_payment, $showcase) {
        if (in_array($shipping_method, $this->shipping_method_enum) !== true) return false;
        if (in_array($term_of_payment, $this->term_of_payment_enum) !== true) return false;
        
        $tortal_price = 0;
        
        // 商品別価格
        foreach ($this->items as $item) {
            $item_object = $showcase->searchItemById($item['id']);
            $charge = $item_object->calculateCharge($item['quantity']);
            if (is_int($charge) !== true) return false;
            $tortal_price += $charge;
        }
        
        // 配送方法別価格
        if ($shipping_method === $this->shipping_method_enum['express']) $tortal_price += 200; 
        
        return $tortal_price;
    }
}

?>
