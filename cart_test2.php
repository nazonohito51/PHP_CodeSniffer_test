<?php

if (! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', '../simpletest/');
}
require_once SIMPLE_TEST . 'unit_tester.php';
require_once SIMPLE_TEST . 'reporter.php';

require_once 'cart.php';



class CartTest extends UnitTestCase
{
    var $cart;
    var $items = array();
    var $showcase;

    function CartTest() 
    {
        $this->UnitTestCase('Cart class test');
    }

    function setUp() 
    {
        $this->cart = new Cart();
        $this->items[] = new Item('Perfect PHP', 3600, '2010/11', 2);
        $this->items[] = new Item('TEST-DRIVEN DEVELOPMENT', 3000, '2006/12', 0);
        $this->items[] = new Item('RedBull', 240, '2011/7', 50);
        $this->showcase = new Showcase();
        $this->showcase->addItem($this->items[0]);
        $this->showcase->addItem($this->items[1]);
        $this->showcase->addItem($this->items[2]);
    }
    
    function testGetItemList() 
    {
        $this->cart->items = array(
            array('id' => 0, 'quantity' => 11, 'other' => 21),
            array('id' => 1, 'quantity' => 12, 'other' => 22),
            array('id' => 2, 'quantity' => 13, 'other' => 23),
        );
        
        $cart_items = $this->cart->getItemList();
        $this->assertIdentical(count($cart_items), 3);
        
        $this->assertIdentical(count($cart_items[0]), 2);
        $this->assertIdentical($cart_items[0]['id'], 0);
        $this->assertIdentical($cart_items[0]['quantity'], 11);
        
        $this->assertIdentical(count($cart_items[1]), 2);
        $this->assertIdentical($cart_items[1]['id'], 1);
        $this->assertIdentical($cart_items[1]['quantity'], 12);
        
        $this->assertIdentical(count($cart_items[2]), 2);
        $this->assertIdentical($cart_items[2]['id'], 2);
        $this->assertIdentical($cart_items[2]['quantity'], 13);
    }
    
    function testInsertItem() 
    {
        $this->assertTrue($this->cart->insertItem(0, 111));
        $this->assertIdentical(count($this->cart->items), 1);
        $this->assertIdentical($this->cart->items[0]['id'], 0);
        $this->assertIdentical($this->cart->items[0]['quantity'], 111);
        
        $this->assertTrue($this->cart->insertItem(1, 222));
        $this->assertIdentical(count($this->cart->items), 2);
        $this->assertIdentical($this->cart->items[1]['id'], 1);
        $this->assertIdentical($this->cart->items[1]['quantity'], 222);
        
        $this->assertTrue($this->cart->insertItem(2, 333));
        $this->assertIdentical(count($this->cart->items), 3);
        $this->assertIdentical($this->cart->items[2]['id'], 2);
        $this->assertIdentical($this->cart->items[2]['quantity'], 333);
        
        $this->assertTrue($this->cart->insertItem(0, 444));
        $this->assertIdentical(count($this->cart->items), 3);
        $this->assertIdentical($this->cart->items[0]['id'], 0);
        $this->assertIdentical($this->cart->items[0]['quantity'], 555);
    }
    
    function testCalculateTortalPrice() 
    {
        $this->assertIdentical($this->cart->calculateTortalPrice(0, 0, $this->showcase), 0);
        
        $this->cart->insertItem(0, 1);
        $this->assertIdentical($this->cart->calculateTortalPrice(0, 0, $this->showcase), 3600);
        
        $this->cart->insertItem(1, 2);
        $this->assertIdentical($this->cart->calculateTortalPrice(0, 0, $this->showcase), 9600);
        
        $this->assertIdentical($this->cart->calculateTortalPrice(1, 0, $this->showcase), 9800);
        $this->assertIdentical($this->cart->calculateTortalPrice(0, 1, $this->showcase), 9600);
        $this->assertIdentical($this->cart->calculateTortalPrice(0, 2, $this->showcase), 9600);
        
        $this->assertFalse($this->cart->calculateTortalPrice(-1, 0, $this->showcase));
        $this->assertFalse($this->cart->calculateTortalPrice(2, 0, $this->showcase));
        $this->assertFalse($this->cart->calculateTortalPrice(0, -1, $this->showcase));
        $this->assertFalse($this->cart->calculateTortalPrice(0, 3, $this->showcase));
        
        $this->cart->items[1]['quantity'] = null;
        $this->assertFalse($this->cart->calculateTortalPrice(0, 0, $this->showcase));
    }
}

$test = &new CartTest();
$test->run(new TextReporter());

?>
