<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage models
 */
class Item extends AppModel {

    var $name = 'Item';
    var $actsAs = array(
        'Sluggable' => array('label' => 'name'),
    );
    var $belongsTo = array('Status');
    var $hasMany = array('CategoryItem', 'ItemPicture', 'Variation');
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
                        'CategoryItem' => array(
                            'Category',
                        ),
                        'ItemPicture' => array(
                            'Picture',
                        ),
                        'Status',
                        'Variation',
                    ),
                    'conditions' => array('Item.id' => $id),
                ));
        return $item[0];
    }

    public function findInCategory($category_id, $options = array()) {
        $joins = array(
            array(
                'table' => 'category_items',
                'alias' => 'CategoryItem',
                'type' => 'inner',
                'conditions' => array(
                    'Item.id = CategoryItem.item_id',
                    'CategoryItem.category_id' => $category_id,
                ),
            ),
        );

        $contain = array(
            'CategoryItem',
            'ItemPicture' => 'Picture',
        );

        $order = array(
            'Item.name',
        );

        $options = array_merge(
                array('joins' => $joins, 'contain' => $contain, 'order' => $order), $options
        );

        $items = $this->find('all', $options);

        return $items;
    }

}
