<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class AdminAppController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
        var_dump("test");
    }
    
}