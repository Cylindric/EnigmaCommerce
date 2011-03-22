<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class VariationFixture extends CakeTestFixture {

    var $name = 'Variation';
    var $import = array('table' => 'variations');
    var $records = array(
        array('id' => 1, 'name' => 'var-1a', 'item_id' => 1),
        array('id' => 2, 'name' => 'var-1b', 'item_id' => 1),
        array('id' => 3, 'name' => 'var-1c', 'item_id' => 1),
        array('id' => 4, 'name' => 'var-2a', 'item_id' => 2),
        array('id' => 5, 'name' => 'var-2b', 'item_id' => 2),
        array('id' => 6, 'name' => 'var-3a', 'item_id' => 3),
        array('id' => 7, 'name' => 'var-3b', 'item_id' => 3),
        array('id' => 8, 'name' => 'var-3c', 'item_id' => 3),
        array('id' => 9, 'name' => 'var-3d', 'item_id' => 3),
    );

}