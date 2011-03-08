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
                'Category.parent_id' => $parent_id,
                'Category.visible_on_web' => true,
            ),
        ));
        $nodes = array();
        foreach ($categories as $category) {
            $count = $this->find('count', array(
                'conditions' => array('Category.parent_id' => $category['Category']['id']),
            ));
            $node = array();
            $node['id'] = $category['Category']['id'];
            $node['text'] = $category['Category']['name'];
            $node['cls'] = 'category';
            $node['iconCls'] = 'category';
            $node['leaf'] = ($count == 0);
            $node['action'] = 'edit';
            $nodes[] = $node;
        }
        return $nodes;    
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

        $subItems = $this->find(
            'all', array(
                'contain' => array(),
                'fields' => array(
                    'Item.id', 'Item.name', 'Item.description',
                    'Picture.filename',
                ),
                'joins' => array(
                    array(
                        'table' => 'category_items',
                        'alias' => 'CategoryItem',
                        'type' => 'inner',
                        'foreignKey' => false,
                        'conditions' => array('CategoryItem.category_id = Category.id'),
                    ),
                    array(
                        'table' => 'items',
                        'alias' => 'Item',
                        'type' => 'left',
                        'foreignKey' => false,
                        'conditions' => array('CategoryItem.item_id = Item.id'),                        
                    ),
                    array(
                        'table' => 'item_pictures',
                        'alias' => 'ItemPicture',
                        'type' => 'left',
                        'foreignKey' => false,
                        'conditions' => array('ItemPicture.item_id = Item.id'),                        
                    ),
                    array(
                        'table' => 'pictures',
                        'alias' => 'Picture',
                        'type' => 'left',
                        'foreignKey' => false,
                        'conditions' => array('ItemPicture.picture_id = Picture.id'),                        
                    ),
                ),
                'conditions' => array('Category.id' => $subCategories),
                'order' => array('Item.name'),
            )
        );

        return $subItems;
    }

}
