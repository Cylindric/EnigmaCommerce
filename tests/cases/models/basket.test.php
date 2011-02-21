<?php
App::import('Model', 'Basket');

class BasketTestCase extends CakeTestCase {

    function  setUp() {
        parent::setUp();
        $this->Basket =& ClassRegistry::init('Basket');
        $this->Detail =& ClassRegistry::init('Detail');
    }

    function testAddWithInvalidDetailFails() {
        $this->expectException('InvalidArgumentException');
        $this->Basket->addDetail(array());
    }

    function testAddWithInvalidDetailIdFails() {
        $this->expectException('InvalidArgumentException');
        $this->Basket->addDetail('this_is_an_invalid_item_id');
    }

    function testAddWithMissingDetailIdFails() {
        $this->expectException('InvalidArgumentException');
        $this->Basket->addDetail(99);
    }

    function testAdd() {
        $detail = $this->Detail->create(array('id'=>1, 'name'=>'test', 'price'=>12.34));

        $this->Basket->create();
        $this->Basket->addDetail($detail);
    }
}