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
        $this->Auth->allow('index', 'view');
        
        $menu_categories = $this->Category->find('threaded', array(
            'fields'=>array('Category.id', 'Category.name', 'Category.slug', 'Category.parent_id'),
            'contain'=>array()
        ));
        $this->set('menu_categories', $menu_categories);
    }

    function index() {
        $parentid = $this->Category->field('id', array('Category.slug'=>'catrootnode'));
        $this->Category->recursive = 0;
        
        $categories = $this->Category->find('all', array(
            'fields'=>array('Category.id', 'Category.name', 'Category.slug'),
            'contain'=>array(),
            'conditions'=>array('Category.parent_id'=>$parentid)
        ));

        $this->set('categories', $categories);
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }

        if (is_numeric($id)) {
            $category = $this->Category->findById($id);
        } else {
            $category = $this->Category->findBySlug($id);
        }

        $this->set('subCategories', $this->Category->subCategories($id));        
        $this->set('relatedItems', $this->Item->withinCategory($id));
        $this->set('category', $category);
    }

    function admin_view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('category', $this->Category->read(null, $id));
    }

    function admin_index() {
        $this->Category->recursive = 0;
        $this->set('categories', $this->paginate());
    }

    function admin_add() {
        if (!empty($this->data)) {
            $this->Category->create();
            if ($this->Category->save($this->data)) {
                $this->Session->setFlash(__('The category has been saved', true));
                $this->redirect(array('action' => 'view', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The category could not be saved. Please, try again.', true));
            }
        }
        $this->set('parents', $this->Category->find('list'));
        $this->set('items', $this->Category->Item->find('list'));
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Category->save($this->data)) {
                $this->Session->setFlash(__('The category has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The category could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Category->read(null, $id);
        }

        $this->set('parents', $this->Category->find('list'));
        //$this->set('items', $this->Category->Item->find('list'));
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for category', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Category->delete($id)) {
            $this->Session->setFlash(__('Category deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Category was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

}