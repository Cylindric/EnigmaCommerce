<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class AppController extends Controller {
    var $components = array(
        'Auth',
        'Layout',
        'RequestHandler',
        'Session',
    );
    var $helpers = array('Form', 'Html', 'Js', 'Image', 'Link', 'Format', 'Session', 'Text', 'Tree');
    var $user = null;

    var $paginate = array(
        'limit' => 10
    );

    function beforeFilter() {
        $this->Auth->authenticate = array('Form');
        $this->Auth->autoRedirect = false;
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'categories', 'action' => 'index');

        $user = $this->Auth->user();
        if ($user) {
            if ($user['group_id'] == 1) {
                $this->Layout->addFooterMenuItem('admin');
            }
        }
        
        $this->set('webRoot', $this->params->webroot);
        $this->set('user', $this->Auth->user());

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

    }

}