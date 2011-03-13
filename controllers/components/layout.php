<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class LayoutComponent extends Object {

    private $controller;
    
    private $footerMenu = array();

    public function startup($controller) {
        $this->controller = $controller;
    }
    
    public function initialize() {
    }
    
    public function shutdown() {
    }

    public function addFooterMenuItem($item) {
        $this->footerMenu[] = $item;
    }
    
    public function beforeRender($controller) {
        $this->controller = $controller;
        $this->controller->set('footerMenuItems', $this->footerMenu);
    }
    
}