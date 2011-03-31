<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating links between Items and Pictures.
 * 
 * @package core
 * @subpackage models
 */
class ItemPicture extends AppModel {

    var $name = 'ItemPicture';
    var $belongsTo = array('Item', 'Picture');

}