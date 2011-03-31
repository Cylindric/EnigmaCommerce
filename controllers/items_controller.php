<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Handles interactions with Items.
 * @package core
 * @subpackage controllers
 */
class ItemsController extends AppController {

    var $name = 'Items';

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    function index() {
        $this->Item->recursive = 0;
        $this->set('items', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid %s', __('item')), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        $item = $this->Item->details($id);
        $this->set('item', $item);
    }

}