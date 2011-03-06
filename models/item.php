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

    /**
     * Returns all items that are children of the specified category and all it's
     * subcategories.
     * 
     * The sort order can be specified by passing in a settings array with the 
     * field to sort by:
     * array('order' => 'Item.name')
     * 
     * @param int $category_id
     * @param array $settings
     * @return array
     */
    public function withinCategory($category_id, array $settings = array()) {
        $settings = array_merge(array(
            'order' => array('Item.name')
        ), $settings);
        
        $category = ClassRegistry::init('Category');
        $categoryItems = ClassRegistry::init('CategoryItems');
        
        $category->read(null, $category_id);
 
        // Using a manual join, as at time of writing there seems to be a problem 
        // with table prefixes and complex joins.
//        $items = $this->find(
//            'all', array(
//                'contain' => array(
//                    'CategoryItem' => array(
//                        'Category' => array(
//                            'conditions' => array(
//                                'Category.lft >=' => $category->data['Category']['lft'],
//                                'Category.rght <=' => $category->data['Category']['rght']                                
//                            )
//                        )
//                    )
//                ),
//            )
//        );
        
        $items = $this->find(
            'all', array(
                'joins'=>array(
                    array(
                        'table'=>$categoryItems->tablePrefix.$categoryItems->table,
                        'alias'=>'CategoryItems',
                        'type'=>'inner',
                        'foreignKey'=>false,
                        'conditions'=>array('CategoryItems.item_id = Item.id'),
                    ),
                    array(
                        'table'=>$category->tablePrefix.$category->table,
                        'alias'=>'Category',
                        'type'=>'inner',
                        'foreignKey'=>false,
                        'conditions'=>array(
                            'Category.id = CategoryItems.category_id',
                            'Category.lft >=' => $category->data['Category']['lft'],
                            'Category.rght <=' => $category->data['Category']['rght']
                        ),
                    )
                ),
                'order' => $settings['order']
            )
        );

        return $items;
    }
    
}
