<?php
class Detail extends AppModel {

    var $name = 'Detail';

    var $actsAs = array(
        'Sluggable' => array(
            'label'=>'name', 
            'slug'=>'tag', 
            'ignore'=>array(),
            'unique_conditions'=>array(array('foreignField'=>'Detail.item_id', 'localField'=>'item_id'))),
    );
    
    var $belongsTo = array('Item');
    
}