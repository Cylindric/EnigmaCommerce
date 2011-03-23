<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class CategoryItemFixture extends CakeTestFixture {

    var $name = 'CategoryItem';
    var $import = array('table' => 'category_items');
    var $records = array(
        array('id' => 1, 'category_id' => 2, 'item_id' => 1, 'is_primary' => true), // cat-1 item-1
        array('id' => 2, 'category_id' => 10, 'item_id' => 1, 'is_primary' => false), // cat-2b1 item-1
        array('id' => 2, 'category_id' => 5, 'item_id' => 2, 'is_primary' => true), // cat-1a2 item-2
        array('id' => 2, 'category_id' => 11, 'item_id' => 3, 'is_primary' => true), // cat-2b2 item-3
    );

}