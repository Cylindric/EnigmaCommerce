<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class ItemsController extends AdminAppController {

    var $name = 'Items';
    
    function beforeFilter() {
        parent::beforeFilter();
    }
    
    function index() {
        $this->set('items', $this->paginate());
    }

    function add() {
        if (!empty($this->data)) {
            $this->Item->create();
            if ($this->Item->save($this->data)) {
                $this->Session->setFlash(__('The item has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The item could not be saved. Please, try again.', true));
            }
        }
        $categories = $this->Item->Category->find('list');
        $this->set(compact('categories'));
    }

    function edit($id = null) {
        parent::admin_edit();
        if (!empty($this->data)) {
            $this->Item->save($this->data);
        }
        if (!empty($id)) {
            $data = $this->Item->findById($id);
        }
        $this->set('data', $data);
    }
    
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for item', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Item->delete($id)) {
            $this->Session->setFlash(__('Item deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Item was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

}