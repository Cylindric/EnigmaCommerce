<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class CategoriesController extends AdminAppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    public function index() {
    }

    public function add() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('category')));
                $this->redirect(array('action' => 'edit', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The %s could not be created. Please, try again.', __('category')));
            }
        } else {
            $this->request->data = $this->Category->create();
        }
        
        $this->set('parents', $this->Category->find('list'));
        $this->set('data', $this->request->data);
    }

    public function edit($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('category')));
            $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('category')), 'flash_success');
                $this->redirect(array('action' => 'edit', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Category')), 'flash_failure');
            }
        } else {
            $this->request->data = $this->Category->findById($id);
        }

        $this->set('parents', $this->Category->find('list'));
        $this->set('data', $this->request->data);
    }

    public function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for %s', __('category')));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Category->delete($id)) {
            $this->Session->setFlash(__('%s deleted', __('Category')));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('%s was not deleted', __('Category')));
        $this->redirect(array('action' => 'index'));
    }

    public function menu_nodes() {
        $root = 0;
        if (isset($this->request->params['form']['node'])) {
            $root = (int)$this->request->params['form']['node'];
        }
        if ($root == 0) {
            $root = $this->Category->field('id', array('slug' => 'catrootnode'));
        }
        $data = $this->Category->menuNodes($root);
        return $data;
    }

}