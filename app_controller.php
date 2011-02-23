<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class AppController extends Controller {
    var $components = array(
        'Auth', 
        'RequestHandler', 
        'Session'
    );
    var $helpers = array('Form', 'Html', 'Js', 'Number', 'Session', 'Text', 'Tree');
    var $user = null;

    var $paginate = array(
        'limit' => 10
    );
    
    function beforeFilter() {
        $this->Auth->authenticate = array('Form');
        $this->Auth->autoRedirect = false;
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => false);
        $this->Auth->loginRedirect = array('controller' => 'categories', 'action' => 'index');
        
        if (isset($this->params['admin']) && $this->params['admin']) {
           $this->layout = 'admin'; 
        }

        $this->set('webRoot', $this->params->webroot);
        $this->set('user', $this->Auth->user());

    }

}