<?php
class Unit extends AppModel {

    var $name = 'Unit';

    var $actsAs = array('Containable');

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
