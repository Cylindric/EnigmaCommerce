<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class Variantion extends AppModel {

    var $name = 'Variantion';

    var $actsAs = array(
        'Containable',
        'Sluggable' => array(
            'label' => 'name', 
            'ignore' => array(),
            'unique_conditions'=>array(array('foreignField' => 'Variation.item_id', 'localField' => 'item_id'))),
    );
    
    var $belongsTo = array('Item');
    
}