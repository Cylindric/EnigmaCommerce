<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class ItemsController extends AdminAppController {

    var $name = 'Items';
    var $uses = array('Category', 'Item');

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
            $this->Session->setFlash(__('Invalid %s', __('item')));
            $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Item->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('item')), 'flash_success');
                $this->redirect(array('action' => 'edit', $this->Item->id));
            } else {
                $this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('item')), 'flash_failure');
            }
        } else {
            $this->request->data = $this->Item->findById($id);
        }

        $this->set('data', $this->request->data);
    }

}