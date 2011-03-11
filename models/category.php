<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/
class Category extends AppModel {

    var $name = 'Category';

    var $actsAs = array(
        'Tree',
        'Sluggable' => array('label'=>'name', 'ignore'=>array()),
    );

    var $belongsTo = array(
        'ParentCategory' => array(
            'className' => 'Category',
            'foreignKey' => 'parent_id'),
        'Status'
    );

    var $hasMany = array('CategoryItem');

    var $validate = array(
        'slug' => array(
            'uniqueSlug' => array(
                'rule' => 'isUnique',
                'message' => 'The slug must be unique'
            )
        )
    );
    
    public function menuNodes($parent_id) {
        $categories = $this->find('threaded', array(
            'fields' => array('Category.id', 'Category.name', 'Category.slug', 'Category.parent_id'),
            'conditions' => array(
                'Category.visible_on_web' => true,
            ),
        ));
        return $categories;
    }

    /**
     * Returns all categories that are children of the specified category.
     * 
     * The sort order can be specified by passing in a settings array with the 
     * field to sort by:
     * array('order' => 'Category.name')
     * 
     * @param int $parent_id
     * @param array $settings
     * @return array
     */
    public function subCategories($parent_id, array $settings = array()) {
        $settings = array_merge(array(
            'order' => array('Category.name'),
            'find' => 'all',
        ), $settings);
        
        $categories = $this->find($settings['find'], array(
            'fields' => array('Category.id', 'Category.name', 'Category.slug', 'Category.id AS innerid'),
            'conditions' => array(
                'Category.parent_id' => $parent_id,
            ),
            'order' => $settings['order']
        ));
        return $categories;
    }

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
    public function subItems($category_id, array $settings = array()) {
        $settings = array_merge(array(
            'order' => array('Item.name')
        ), $settings);
        
        $subCategories = $this->subCategories($category_id, array('find'=>'list'));
        $subCategories = array_keys($subCategories);
        $subCategories[] = (int)$category_id;

        $catItems = $this->find('all', array(
            'contain' => array(
                'CategoryItem' => array(
                    'Item' => array(
                        'ItemPicture' => array(
                            'Picture'
                        )
                    )
                ),
            ),
            'conditions' => array('Category.id' => $subCategories),
        ));

        $items = array();
        foreach ($catItems as $cat) {
            foreach ($cat['CategoryItem'] as $catItem) {
                $items[] = array('Item'=>$catItem['Item']);
            }
        }

        return $items;
    }

}
