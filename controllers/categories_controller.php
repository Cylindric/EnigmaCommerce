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
 * The Category controller is used to drive interaction with Categories.
 */
class CategoriesController extends AppController {

    /**
     * Imports the category and item models for use in this controller.
     * @var array
     */
    var $uses = array('Category', 'Item');

    /**
     * Callback executed before any other actions are called.
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    /**
     * Displays the main category index.
     * Sets a view variable containing all categories directly below the main 
     * parent node.
     * 
     * @viewvar categories
     */
    public function index() {
        $parentid = $this->Category->field('id', array('Category.slug' => 'catrootnode'));

        $categories = $this->Category->find('all', array(
                    'fields' => array('Category.id', 'Category.name', 'Category.slug'),
                    'contain' => array(),
                    'conditions' => array('Category.parent_id' => $parentid)
                ));

        $this->set('categories', $categories);
    }

    /**
     * Displays a specific category.
     * Sets view vars for the selected category, all subcategories, and any items
     * in the specified category.
     * 
     * @param mixed $id The id or slug of the category to display.
     * @viewvar category
     * @viewvar subCategories
     * @viewvar relatedItems
     */
    public function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('category')), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        $category = $this->Category->findById($id);
        $subCategories = $this->Category->subCategories($category['Category']['id']);
        $relatedItems = $this->Item->findInCategory($category['Category']['id']);

        $this->set('category', $category);
        $this->set('subCategories', $subCategories);
        $this->set('relatedItems', $relatedItems);
    }

    /**
     * Returns an array of categories organised in a tree layout.
     * 
     * @return array
     */
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