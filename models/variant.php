<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class Variant extends AppModel {

    var $name = 'Variant';

    var $actsAs = array(
        'Sluggable' => array(
            'label'=>'name', 
            'ignore'=>array(),
            'unique_conditions'=>array(array('foreignField'=>'Variant.item_id', 'localField'=>'item_id'))),
    );
    
    var $belongsTo = array('Item');
    
}