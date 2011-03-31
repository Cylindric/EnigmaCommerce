<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * @package core
 * @subpackage helpers
 */
class FormatHelper extends AppHelper {

    var $name = 'Format';
    var $settings = array();
    var $helpers = array('Html', 'Number');

    function currency($number, $currency = 'GBP', $options = array()) {
        return $this->Number->currency($number, $currency, $options);
    }

}
