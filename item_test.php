<?php

if (! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', '../simpletest/');
}
require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');

require_once('item.php');



class ItemTest extends UnitTestCase {
    var $item;

    function ItemTest() {
        $this->UnitTestCase('Item class test');
    }

    function setUp() {
        $this->item = new Item('Perfect PHP', 3600, '2010/11', 2);
    }
    
    function testValidateName() {
        $this->assertTrue($this->item->validateName('string'));
        $this->assertFalse($this->item->validateName(1));
        $this->assertFalse($this->item->validateName(1.1));
        $this->assertFalse($this->item->validateName(array()));
        $this->assertFalse($this->item->validateName(null));
        
        $this->assertFalse($this->item->validateName(''));
        $this->assertTrue($this->item->validateName('1'));
        $this->assertTrue($this->item->validateName('123456789012345678901234567890'));
        $this->assertFalse($this->item->validateName('1234567890123456789012345678901'));
    }
    
    function testValidatePrice() {
        $this->assertTrue($this->item->validatePrice(1));
        $this->assertFalse($this->item->validatePrice('string'));
        $this->assertFalse($this->item->validatePrice(1.1));
        $this->assertFalse($this->item->validatePrice(array()));
        $this->assertFalse($this->item->validatePrice(null));
        
        $this->assertFalse($this->item->validatePrice(0));
        $this->assertTrue($this->item->validatePrice(1));
        $this->assertTrue($this->item->validatePrice(1000000));
        $this->assertFalse($this->item->validatePrice(1000001));
    }
    
    function testValidateReleaseDate() {
        $this->assertTrue($this->item->validateReleaseDate('2015/05'));
        $this->assertFalse($this->item->validateReleaseDate(1));
        $this->assertFalse($this->item->validateReleaseDate(1.1));
        $this->assertFalse($this->item->validateReleaseDate(array()));
        $this->assertFalse($this->item->validateReleaseDate(null));
        
        $this->assertFalse($this->item->validateReleaseDate('2015/05/01'));
    }
    
    function testValidateStock() {
        $this->assertTrue($this->item->validateStock(1));
        $this->assertFalse($this->item->validateStock('string'));
        $this->assertFalse($this->item->validateStock(1.1));
        $this->assertFalse($this->item->validateStock(array()));
        $this->assertFalse($this->item->validateStock(null));
        
        $this->assertFalse($this->item->validateStock(0));
        $this->assertTrue($this->item->validateStock(1));
        $this->assertTrue($this->item->validateStock(1000000));
        $this->assertFalse($this->item->validateStock(1000001));
    }
    
    function testGetStock() {
        $this->assertIdentical($this->item->getStock(), 2);
    }
    
    function testReservation() {
        $item = new Item('Perfect PHP', 3600, '2010/11', 2);
        
        $this->assertFalse($item->reservation('1'));
        $this->assertFalse($item->reservation(1.1));
        
        $this->assertTrue($item->reservation(1));
        $this->assertIdentical($item->getStock(), 1);
        
        $item = new Item('Perfect PHP', 3600, '2010/11', 2);
        $this->assertTrue($item->reservation(2));
        $this->assertIdentical($item->getStock(), 0);
        
        $item = new Item('Perfect PHP', 3600, '2010/11', 2);
        $this->assertFalse($item->reservation(3));
        $this->assertIdentical($item->getStock(), 2);
    }
    
    function testCalculateCharge() {
        $item = new Item('Perfect PHP', 3600, '2010/11', 2);
        $this->assertIdentical($item->calculateCharge(1), 3600);
        $this->assertIdentical($item->calculateCharge(2), 7200);
        
        $item = new Item('Perfect PHP', 500, '2010/11', 2);
        $this->assertIdentical($item->calculateCharge(1), 500);
        $this->assertIdentical($item->calculateCharge(2), 1000);
        
        $this->assertFalse($item->calculateCharge(0));
        
        $this->assertFalse($item->calculateCharge('string'));
        $this->assertFalse($item->calculateCharge(1.1));
        $this->assertFalse($item->calculateCharge(null));
    }
}

$test = &new ItemTest();
$test->run(new TextReporter());

?>
