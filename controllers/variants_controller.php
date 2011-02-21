<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class VariantsController extends AppController {

    var $name = 'Variants';

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('index', 'view');
    }
    
    function index() {
        $this->Variant->recursive = 0;
        $this->set('Variants', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Variant', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('Variant', $this->Variant->read(null, $id));
    }

    function add() {
        if (!empty($this->data)) {
            $this->Variant->create();
            if ($this->Variant->save($this->data)) {
                $this->Session->setFlash(__('The Variant has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Variant could not be saved. Please, try again.', true));
            }
        }
    }

    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Variant', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Variant->save($this->data)) {
                $this->Session->setFlash(__('The Variant has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Variant could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Variant->read(null, $id);
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Variant', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Variant->delete($id)) {
            $this->Session->setFlash(__('Variant deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Variant was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

}
