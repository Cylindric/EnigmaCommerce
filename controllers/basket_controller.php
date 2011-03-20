<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage controllers
 */

/**
 * Basket Controller
 */
class BasketController extends AppController {

    var $name = 'Basket';

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('view', 'add');
    }

    function view() {
        
    }

    function add() {
        
    }

}
