<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class UnitsController extends AppController {

    var $name = 'Units';

    function index() {
        $this->Unit->recursive = 0;
        $this->set('units', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid unit', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('unit', $this->Unit->read(null, $id));
    }

    function admin_index() {
        $this->Unit->recursive = 0;
        $this->set('units', $this->paginate());
    }

    function admin_add() {
        if (!empty($this->data)) {
            $this->Unit->create();
            if ($this->Unit->save($this->data)) {
                $this->Session->setFlash(__('The unit has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The unit could not be saved. Please, try again.', true));
            }
        }
        $parentunits = $this->Unit->ParentUnit->find('list');
        $this->set(compact('parentunits'));
    }

    function admin_edit($id = null) {
        parent::admin_edit();
        if(!empty($this->data)) {
            $this->Unit->save($this->data);
        }
        if (!empty($id)) {
            $data = $this->Unit->findById($id);
        }
        $this->set('data', $data);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for unit', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Unit->delete($id)) {
            $this->Session->setFlash(__('Unit deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Unit was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }
    
    function admin_menuitems() {
        // Find all Unit items for the menu.  Get full data, as there won't be many
        // so no point in loading them on demand.
        $nodes = $this->Unit->treeNodes(0, array(
            'name' => 'name'
        ));
        
        $this->set('data', $nodes);
        $this->viewPath = 'elements';
        $this->render('js_data');
    }
    
}
