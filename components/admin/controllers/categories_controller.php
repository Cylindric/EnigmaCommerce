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
    }

    function menu_nodes() {
        return $this->Category->menuNodes();
    }
    
    function index() {
        $this->Category->recursive = 0;
        $this->set('categories', $this->paginate());
    }

    function add() {
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

    function edit($id = null) {
        $this->Category->id = $id;
        if (!$this->Category->exists()) {
            throw new NotFoundException(__('Invalid %s', 'Category'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Category->save($this->request->data)) {
                    $this->Session->setFlash(__('The %s has been saved', 'Category'));
//                    $this->redirect(array('action' => 'index'));
                    $this->redirect(array('action' => 'edit', $id));
            } else {
                    $this->Session->setFlash(__('The %s could not be saved. Please, try again.', 'Category'));
            }
        } else {
            $this->request->data = $this->Category->read(null, $id);
        }

        $this->set('parents', $this->Category->find('list'));
//        $this->set('items', $this->Category->Item->find('list'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for category'));
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