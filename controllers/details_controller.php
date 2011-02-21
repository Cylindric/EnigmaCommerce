<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/
class DetailsController extends AppController {

    var $name = 'Details';

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('index', 'view');
    }
    
    function index() {
        $this->Detail->recursive = 0;
        $this->set('details', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid detail', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('detail', $this->Detail->read(null, $id));
    }

    function add() {
        if (!empty($this->data)) {
            $this->Detail->create();
            if ($this->Detail->save($this->data)) {
                $this->Session->setFlash(__('The detail has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The detail could not be saved. Please, try again.', true));
            }
        }
    }

    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid detail', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Detail->save($this->data)) {
                $this->Session->setFlash(__('The detail has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The detail could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Detail->read(null, $id);
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for detail', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Detail->delete($id)) {
            $this->Session->setFlash(__('Detail deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Detail was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

}
