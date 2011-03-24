<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
App::import('Model', 'Item');

class ItemTestCase extends CakeTestCase {

    var $fixtures = array(
        'app.category',
        'app.category_item',
        'app.item',
        'app.item_picture',
        'app.picture',
        'app.status',
        'app.variation',
    );

    public function setup() {
        parent::setup();
        $this->Item = ClassRegistry::init('Item');
    }

    public function testDetailsContainsRequiredData() {
        $itemUnderTest = $this->Item->find('first');        
        $data = $this->Item->details($itemUnderTest['Item']['id']);
        $this->assertArrayHasKey('CategoryItem', $data);
        $this->assertArrayHasKey('Category', $data['CategoryItem'][0]);
        $this->assertArrayHasKey('Item', $data);
        $this->assertArrayHasKey('ItemPicture', $data);
        $this->assertArrayHasKey('Status', $data);
        $this->assertArrayHasKey('Variation', $data);
    }

    public function testDetailsContainsRequiredDataRecords() {
        $itemUnderTest = $this->Item->find('first');
        $data = $this->Item->details($itemUnderTest['Item']['id']);
        $this->assertGreaterThan(0, count($data['CategoryItem']));
        $this->assertGreaterThan(0, count($data['CategoryItem'][0]['Category']));
        $this->assertGreaterThan(0, count($data['ItemPicture']));
        $this->assertGreaterThan(0, count($data['Variation']));
    }

}