<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 */

/**
 * Handles interactions with Users, such as basic CRUD.
 * @package admin_component
 * @subpackage controllers
 */
class UsersController extends AdminAppController {

    var $name = 'Users';

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login');
        $this->set('title_for_layout', __('Users'));
    }

    function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->loginRedirect);
            } else {
                $this->Session->setFlash(__('Username or password is incorrect'), 'flash/error');
            }
        }
    }

    function logout() {
        $this->Session->setFlash(__('Good-Bye'));
        $this->redirect($this->Auth->logout());
    }

}