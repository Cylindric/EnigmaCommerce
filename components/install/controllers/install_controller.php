<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class InstallController extends InstallAppController {
    var $uses = array();

    function beforeFilter() {
        parent::beforeFilter();

        // Make sure we disable Auth for the installer, otherwise we get lots
        // of complaints about missing tables etc
        $this->Auth->enabled = false;
        $this->Auth->authorize = false;
        $this->Auth->allow('*');
    }

    function index() {
    }

}