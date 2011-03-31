<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 */

/**
 * Handles interactions with Categories, such as basic CRUD.
 * @package admin_component
 * @subpackage controllers
 */
class CategoriesController extends AdminAppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('Categories'));
    }

    public function index() {
        
    }

    public function view($id) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('category')), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        $category = $this->Category->findById($id);
        $this->set('category', $category);
        $this->set('items', $this->Item->findInCategory($category['Category']['id']));
    }

    public function add() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('category')), 'flash/success');
                $this->redirect(array('action' => 'edit', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The %s could not be created. Please, try again.', __('category')), 'flash/failure');
            }
        } else {
            $this->request->data = $this->Category->create();
        }

        $this->set('parents', $this->Category->find('list'));
        $this->set('data', $this->request->data);
    }

    public function edit($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('category')), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('category')), 'flash/success');
                $this->redirect(array('action' => 'edit', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('category')), 'flash/failure');
            }
        } else {
            $this->request->data = $this->Category->findById($id);
        }

        $this->set('parents', $this->Category->find('list'));
        $this->set('items', $this->Item->findInCategory($this->request->data['Category']['id']));
        $this->set('data', $this->request->data);
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