<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class AdminComponent extends Object {

    var $components = array('Layout');
    
    public function register() {
        $this->Layout->addFooterMenuItem('admin');
    }
    
}