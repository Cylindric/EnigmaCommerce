<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/
class Category extends AppModel {

    var $name = 'Category';

    var $actsAs = array(
        'Containable',
        'Tree',
        'Sluggable' => array('label'=>'name', 'ignore'=>array()),
    );

    var $belongsTo = array(
        'ParentCategory' => array(
            'className' => 'Category',
            'foreignKey' => 'parent_id')
    );

    var $hasMany = array('CategoryItem');

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
            'order' => array('Category.name')
        ), $settings);

        $categories = $this->find('all', array(
            'fields' => array('Category.id', 'Category.name', 'Category.slug'),
            'contain' => array(),
            'conditions' => array('Category.parent_id' => $parent_id),
            'order' => $settings['order']
        ));
        return $categories;
    }

}
