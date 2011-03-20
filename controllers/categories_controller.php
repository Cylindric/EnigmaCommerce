<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage controllers
 */

/**
 * Category Controller
 */
class CategoriesController extends AppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    public function index() {

        $parentid = $this->Category->field('id', array('Category.slug' => 'catrootnode'));

        $categories = $this->Category->find('all', array(
                    'fields' => array('Category.id', 'Category.name', 'Category.slug'),
                    'contain' => array(),
                    'conditions' => array('Category.parent_id' => $parentid)
                ));

        $this->set('categories', $categories);
    }

    public function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('category')));
            $this->redirect(array('action' => 'index'));
        }

        $category = $this->Category->findById($id);
        $subCategories = $this->Category->subCategories($category['Category']['id']);
        $relatedItems = $this->Item->findInCategory($category['Category']['id']);

        $this->set('category', $category);
        $this->set('subCategories', $subCategories);
        $this->set('relatedItems', $relatedItems);
    }

    public function menu_nodes() {
        $root = 0;
        if (isset($this->request->params['form']['node'])) {
            $root = (int) $this->request->params['form']['node'];
        }
        if ($root == 0) {
            $root = $this->Category->field('id', array('slug' => 'catrootnode'));
        }
        $data = $this->Category->menuNodes($root);
        return $data;
    }

}