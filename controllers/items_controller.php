<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class ItemsController extends AppController {

    var $name = 'Items';
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'view');
    }
    
    function index() {
        $this->Item->recursive = 0;
        $this->set('items', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid item', true));
            $this->redirect(array('action' => 'index'));
        }

        if (is_numeric($id)) {
            $item = $this->Item->findById($id);
        } else {
            $item = $this->Item->findBySlug($id);
        }

        $this->set('item', $item);
    }

    function admin_index() {
        $this->set('items', $this->paginate());
    }

    function admin_add() {
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

    function admin_edit($id = null) {
        parent::admin_edit();
        if(!empty($this->data)) {
            $this->Item->save($this->data);
        }
        if (!empty($id)) {
            $data = $this->Item->findById($id);
        }
        $this->set('data', $data);
    }
    
    function admin_delete($id = null) {
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