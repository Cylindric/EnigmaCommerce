<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating Units.
 * 
 * @package core
 * @subpackage models
 */
class Unit extends AppModel {

    var $name = 'Unit';
    var $belongsTo = array(
        'ParentUnit' => array(
            'className' => 'Unit',
            'foreignKey' => 'parent_id'
        )
    );
    var $hasMany = array(
        'ChildUnit' => array(
            'className' => 'Unit',
            'foreignKey' => 'parent_id',
            'dependent' => false
        )
    );
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'pluralname' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'parent_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
    );

}
