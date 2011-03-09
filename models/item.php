<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class Item extends AppModel {

    var $name = 'Item';

    var $actsAs = array(
        'Sluggable' => array('label'=>'name'),
    );

    var $belongsTo = array('Status');
    
    var $hasMany = array('CategoryItem', 'ItemPicture' , 'Variation');

    var $validate = array(
        'slug' => array(
            'rule' => 'isUnique',
            'message' => 'Must be unique'
        ),
    );

    public function details($id = null) {
        if (empty($id)) {
            $id = $this->id;
        }
        $item = $this->find('all', array(
            'contain' => array(
                'ItemPicture' => array(
                    'Picture'
                ),
                'Variation',
            ),
            'conditions' => array('Item.id' => $id),
        ));
        return $item[0];
    }
    
}
