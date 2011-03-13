<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class RendererComponent extends Component {

    private $controller;
    
    private $footerMenu = array();

    public function initialize($controller) {
    }
    
    public function startup($controller) {
        $this->controller = $controller;
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