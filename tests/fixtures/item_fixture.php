<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class ItemFixture extends CakeTestFixture {

    var $name = 'Item';
    var $import = array('table' => 'items');
    var $records = array(
        array('id' => 1, 'name' => 'item-1'),
        array('id' => 2, 'name' => 'item-2'),
        array('id' => 3, 'name' => 'item-3'),
    );

}

