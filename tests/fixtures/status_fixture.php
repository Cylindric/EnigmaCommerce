<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class StatusFixture extends CakeTestFixture {

    public $name = 'Statuses';
    public $import = array('table' => 'statuses');
    var $records = array(
        array('id' => 1, 'name' => 'Status 1'),
        array('id' => 2, 'name' => 'Status 2'),
        array('id' => 3, 'name' => 'Status 3'),
    );

}

