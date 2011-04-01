<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 */

/**
 * Base object for all Admin Controllers
 * 
 * @package admin_component
 * @subpackage controllers
 */
class AdminAppController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
    }

    protected function action_id() {
        $sessionKey = $this->Request->params['controller'] . '.id';
        if (count($this->passedArgs) > 0) {
            $passedId = $this->passedArgs[0];
            $this->Session->write($sessionKey, $passedId);
            return $passedId;
        }

        $sessionId = $this->Session->read($sessionKey);
        if (!empty($sessionId)) {
            return $sessionId;
        }

        $this->Session->setFlash(__('Invalid %s', __($this->name)), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

}