<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class CategoriesController extends AppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    function index() {
        $parentid = $this->Category->field('id', array('Category.slug'=>'catrootnode'));
        
        $categories = $this->Category->find('all', array(
            'fields'=>array('Category.id', 'Category.name', 'Category.slug'),
            'contain'=>array(),
            'conditions'=>array('Category.parent_id'=>$parentid)
        ));

        $this->set('categories', $categories);
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category'));
            $this->redirect(array('action' => 'index'));
        }

        $category = $this->Category->findById($id);
        $this->set('category', $category);
        $this->set('subCategories', $this->Category->subCategories($category['Category']['id']));        
        $this->set('relatedItems', $this->Item->withinCategory($category['Category']['id']));
    }

    function menu_nodes() {
        return $this->Category->menuNodes();
    }

}