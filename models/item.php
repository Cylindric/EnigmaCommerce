<?php
class Item extends AppModel {

    var $name = 'Item';

    var $actsAs = array(
        'Sluggable' => array('label'=>'name', 'slug'=>'tag'),
    );

    var $hasMany = array('CategoryItem', 'Detail');

    var $validate = array(
        'tag' => array(
            'rule' => 'isUnique',
            'message' => 'Must be unique'
        ),
    );

}
