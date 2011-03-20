<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package install_component
 * @subpackage core
 */
class InstallAutoLoader extends object {

    private $Renderer = null;

    function startup($controller) {
        
    }

    function beforeRender($controller) {
        $this->Renderer = $controller->Components->load('Renderer');

        $menu_item = array(
            'url' => '/install/',
            'title' => __('Install'),
            'options' => array(),
        );
        $this->Renderer->addFooterMenuItem($menu_item);
    }

}
