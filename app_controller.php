<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/
class AppController extends Controller {
    var $components = array('Auth', 'RequestHandler', 'Session');
    var $helpers = array('Form', 'Html', 'Js', 'Session', 'Text', 'Number');
    var $view = 'Theme';
    var $user = null;

    function beforeFilter() {
        $this->Auth->autoRedirect = false;
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => false);
        $this->Auth->loginRedirect = array('controller' => 'categories', 'action' => 'index');

        if ($this->Auth->user()) {
           $this->Auth->getModel()->read(null, $this->Auth->user('id'));
        }

        if (isset($this->params['admin']) && $this->params['admin']) {
            if (!is_null($this->user) && $this->user->isAdmin()) {
                    $this->theme = 'cp';
            }
        }        

        // Determine if the page is being loaded via an Ajax request
        // Many views render differently if it is.
        $isAjax = $this->RequestHandler->isAjax();
        $this->set('isAjax', $isAjax);
        
        // Ajax requests come in different flavours.  Some update the entire
        // controller frame, some only the detail frame.
        // If this isn't an Ajax request, update everything.
        // If it is an Ajax request, assume the bodycontent should be updated,
        // unless specified otherwise by the GET value resetFrame.
        $this->set('ajaxFrame', 'body');
        if ($isAjax) {
            $this->set('ajaxFrame', 'bodycontent');
            if(isset($this->request->query['resetFrame'])) {
                $this->set('ajaxFrame', $this->request->query['resetFrame']);
            }
        }

        $this->set('webRoot', $this->params->webroot);
        if ($this->user == null) {
            $this->set('user', null);
        } else {
            $this->set('user', $this->user->data);
        }

    }

//    function admin_menu() {
//        $this->ext = '.js';
//    }
//    
    function admin_edit() {
        //$this->ext = '.js';
    }
    
}