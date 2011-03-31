<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating Baskets.
 * 
 * @package core
 * @subpackage models
 */
class Basket extends AppModel {

    var $name = 'Basket';
    var $belongsTo = array('User');
    var $hasMany = array('Detail');

    function addDetail($detail) {
        if (is_array($detail)) {
            if (!array_key_exists('Detail', $detail)) {
                throw new InvalidArgumentException('Detail parameter must be a detail record, or detail ID');
            }
        } else {
            if (is_numeric($detail)) {
                $detail = $this->Detail->findById($detail);
            }
            if ($detail === false) {
                throw new InvalidArgumentException('Detail parameter must be a valid detail ID');
            }
        }
    }

}
