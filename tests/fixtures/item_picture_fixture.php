<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class ItemPictureFixture extends CakeTestFixture {

    var $name = 'ItemPicture';
    var $import = array('table' => 'item_pictures');
    var $records = array(
        array('id' => 1, 'item_id' => 1, 'picture_id' => 1, 'is_primary' => true),
        array('id' => 2, 'item_id' => 1, 'picture_id' => 2, 'is_primary' => false),
        array('id' => 2, 'item_id' => 2, 'picture_id' => 3, 'is_primary' => true),
        array('id' => 2, 'item_id' => 3, 'picture_id' => 4, 'is_primary' => true),
    );

}

