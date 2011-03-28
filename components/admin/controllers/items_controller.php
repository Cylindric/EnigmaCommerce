<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 * @subpackage controllers
 */
class ItemsController extends AdminAppController {

    var $name = 'Items';
    var $uses = array('Category', 'CategoryItem', 'Item');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('Items'));
    }

    /**
     * Shows a list of all items in the specified category.
     * TODO: Do something interesting for the "all" case (id=null)
     * @param mixed $category_id
     */
    function index($category_id = null) {
        if (!$category_id) {
            $category = array();
            $items = array();
        } else {
            $category = $this->Category->findById($category_id);
            $items = $this->Item->findInCategory($category['Category']['id']);
        }

        $this->set('category', $category);
        $this->set('items', $items);
    }

    function edit($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('item')), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Item->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('item')), 'flash/success');
                $this->redirect(array('action' => 'edit', $this->Item->id));
            } else {
                $this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('item')), 'flash/failure');
            }
        } else {
            $this->request->data = $this->Item->details($id);
        }

        $this->set('categories', $this->Category->find('list'));
        $this->set('data', $this->request->data);
    }

    function addCategory() {
        var_dump($this->request->data);
        if ($this->request->is('post') || $this->request->is('put')) {
            $ci = $this->CategoryItem->create();
            var_dump($ci);
            if ($this->CategoryItem->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('link')), 'flash/success');
            } else {
                $this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('link')), 'flash/failure');
            }
//            $this->redirect(array('action' => 'edit', $this->Item->id));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

}