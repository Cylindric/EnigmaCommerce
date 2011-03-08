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
        $item = $this->find('first', array(
            'contain' => array(),
            'fields' => array(
                'Item.id', 'Item.name', 'Item.description', 'Item.slug',
                'Picture.id', 'Picture.filename',
                'Variation.id',
            ),
            'joins' => array(
                array(
                    'table' => 'item_pictures', 'alias' => 'ItemPicture', 'type' => 'left', 'foreignKey' => false,
                    'conditions' => array('ItemPicture.item_id = Item.id'),                        
                ),
                array(
                    'table' => 'pictures', 'alias' => 'Picture', 'type' => 'left', 'foreignKey' => false,
                    'conditions' => array('ItemPicture.picture_id = Picture.id'),                        
                ),
                array(
                    'table' => 'variations', 'alias' => 'Variation', 'type' => 'left', 'foreignKey' => false,
                    'conditions' => array('Variation.item_id = Item.id'),                        
                ),
            ),
            'conditions' => array('Item.id' => $id)
        ));
        var_dump($item);
        return $item;
    }
    
}
