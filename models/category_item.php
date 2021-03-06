<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating the links between Categories and Items.
 * 
 * @package core
 * @subpackage models
 */
class CategoryItem extends AppModel {

    var $name = 'CategoryItem';
    var $belongsTo = array('Category', 'Item');

}