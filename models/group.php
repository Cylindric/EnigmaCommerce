<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating Groups.
 * 
 * @package core
 * @subpackage models
 */
class Group extends AppModel {

    var $name = 'Group';
    var $hasMany = array('User');
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty')
            ),
        ),
    );

}