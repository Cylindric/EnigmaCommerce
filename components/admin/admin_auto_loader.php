<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package admin_component
 */

/**
 * Auto-loader for the Admin component
 * 
 * @package admin_component
 * @subpackage core
 */
class AdminAutoLoader extends object {

    private $Renderer = null;
    private $Auth = null;

    function startup(&$controller) {
        
    }

    function beforeRender($controller) {
        $this->Auth = $controller->Components->load('Auth');
        $this->Renderer = $controller->Components->load('Renderer');

        $user = $this->Auth->user();

        if (($user) && ($user['group_id'] == 1)) {
            $menu_item = array(
                'url' => '/admin/',
                'title' => __('Administrator'),
                'options' => array(),
            );
            $this->Renderer->addFooterMenuItem($menu_item);
        }
    }

}
