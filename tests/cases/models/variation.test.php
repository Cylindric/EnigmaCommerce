<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('Model', 'Item');
App::import('Model', 'Variation');

class VariationTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.category_item', 
        'app.item',
        'app.item_picture',
        'app.status',
        'app.variation', 
    );
    
    public function setup() {
        parent::setup();
        $this->Variation = ClassRegistry::init('Variation');
        $this->Item = ClassRegistry::init('Item');
    }

    public function testFindForItem() {
        $item = $this->Item->findById(1);
        var_dump($item);
//        $nodes = $this->Variation->findForItem(0);
//        
//        $this->assertTrue(count($nodes)==1);
//        $this->assertEqual('catrootnode', $nodes[0]['Category']['name']);
    }
   
}