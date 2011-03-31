<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 */

/**
 * Handles interactions with the connections between Categories and Items.
 * @package admin_component
 * @subpackage controllers
 */
class CategoryItemsController extends AdminAppController {

    var $name = 'CategoyItems';
    var $uses = array('CategoryItems');

    public function add() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->CategoryItems->save($this->request->data)) {
                $this->Session->setFlash(__('The %s has been saved', __('link')), 'flash/success');
            } else {
                $this->Session->setFlash(__('The %s could not be created. Please, try again.', __('link')), 'flash/failure');
            }
            $this->redirect($this->referer());
        } else {
            $this->redirect('/');
        }
    }

}