<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Handles display of static Pages.
 * @package core
 * @subpackage controllers
 */
class PagesController extends AppController {

    var $name = 'Pages';
    var $helpers = array('Html');
    var $uses = array();

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('display');
    }

    function display() {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title_for_layout'));
        $this->render(implode('/', $path));
    }

    function admin_mainmenu() {
        $this->ext = '.js';
    }

    function admin_mainmenuitems() {
        $nodes = array();
        $nodes[] = array(
            'text' => 'test1',
            'id' => '1'
        );
        $this->set('data', $nodes);
        $this->render('/elements/js_data', 'data');
    }

}
