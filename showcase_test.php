<?php

if (! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', '../simpletest/');
}
require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');

require_once('showcase.php');



class ShowcaseTest extends UnitTestCase {
    var $showcase;

    function ShowcaseTest() {
        $this->UnitTestCase('Showcase class test');
    }

    function setUp() {
        $this->showcase = new Showcase();
    }
    
    function testValidateItem() {
        $item = new Item('Perfect PHP', 3600, '2010/11', 2);
        $this->assertTrue($this->showcase->validateItem($item));
        
        $item = new Item('', 0, '', 0);
        $this->assertFalse($this->showcase->validateItem($item));
        
        $this->assertFalse($this->showcase->validateItem('string'));
        $this->assertFalse($this->showcase->validateItem(1));
        $this->assertFalse($this->showcase->validateItem(1.1));
        $this->assertFalse($this->showcase->validateItem(null));
        $this->assertFalse($this->showcase->validateItem(array()));
        $this->assertFalse($this->showcase->validateItem(new Showcase()));
    }
    
    function testAddItem() {
        $items = array();
        $items[] = new Item('Perfect PHP', 3600, '2010/11', 2);
        
        $this->showcase->addItem($items[0]);
        $this->assertIdentical(count($this->showcase->items), 1);
        $this->assertIdentical($this->showcase->items[0], $items[0]);
        
        $items[] = new Item('TEST-DRIVEN DEVELOPMENT', 3000, '2006/12', 0);
        $items[] = new Item('RedBull', 240, '2011/7', 50);
        $this->showcase->addItem($items[1]);
        $this->showcase->addItem($items[2]);
        
        $this->assertIdentical(count($this->showcase->items), 3);
        $this->assertIdentical($this->showcase->items[1], $items[1]);
        $this->assertIdentical($this->showcase->items[2], $items[2]);
    }
    
    function testSearchItemById() {
        $items = array();
        $items[] = new Item('Perfect PHP', 3600, '2010/11', 2);
        $items[] = new Item('TEST-DRIVEN DEVELOPMENT', 3000, '2006/12', 0);
        $items[] = new Item('RedBull', 240, '2011/7', 50);
        $this->showcase->addItem($items[0]);
        $this->showcase->addItem($items[1]);
        $this->showcase->addItem($items[2]);
        
        $this->assertIdentical($this->showcase->searchItemById(0), $items[0]);
        $this->assertIdentical($this->showcase->searchItemById(1), $items[1]);
        $this->assertIdentical($this->showcase->searchItemById(2), $items[2]);
        $this->assertFalse($this->showcase->searchItemById(3));
    }
    
    function testSearchIdByName() {
        $items = array();
        $items[] = new Item('Perfect PHP', 3600, '2010/11', 2);
        $items[] = new Item('TEST-DRIVEN DEVELOPMENT', 3000, '2006/12', 0);
        $items[] = new Item('RedBull', 240, '2011/7', 50);
        $this->showcase->addItem($items[0]);
        $this->showcase->addItem($items[1]);
        $this->showcase->addItem($items[2]);
        
        $this->assertIdentical($this->showcase->searchIdByName('Perfect PHP'), 0);
        $this->assertIdentical($this->showcase->searchIdByName('TEST-DRIVEN DEVELOPMENT'), 1);
        $this->assertIdentical($this->showcase->searchIdByName('RedBull'), 2);
        $this->assertNull($this->showcase->searchIdByName('NotExistItem'));
    }
}

$test = &new ShowcaseTest();
$test->run(new TextReporter());

?>
