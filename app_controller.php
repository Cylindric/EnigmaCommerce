<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class AppController extends Controller {
    var $components = array('Auth', 'RequestHandler', 'Session');
    var $helpers = array('Form', 'Html', 'Js', 'Session', 'Text', 'Number', 'Tree');
    var $user = null;

    var $paginate = array(
        'limit' => 10
    );
    
    function beforeFilter() {
        $this->Auth->autoRedirect = false;
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => false);
        $this->Auth->loginRedirect = array('controller' => 'categories', 'action' => 'index');

        if ($this->Auth->user()) {
           $this->user = $this->Auth->getModel();
           $this->user->read(null, $this->Auth->user('id'));
        }
        
        if (isset($this->params['admin']) && $this->params['admin']) {
           $this->layout = 'admin'; 
        }

        $this->set('webRoot', $this->params->webroot);
        if ($this->user == null) {
            $this->set('user', null);
        } else {
            $this->set('user', $this->user->data);
        }

    }

}