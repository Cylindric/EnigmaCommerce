<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class PictureFixture extends CakeTestFixture {

    var $name = 'Picture';
    var $import = array('table' => 'pictures');
    var $records = array(
        array('id' => 1, 'name' => 'Picture 1'),
        array('id' => 2, 'name' => 'Picture 2'),
        array('id' => 3, 'name' => 'Picture 3'),
        array('id' => 4, 'name' => 'Picture 4'),
    );

}

