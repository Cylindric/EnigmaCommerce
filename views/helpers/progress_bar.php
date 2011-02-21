<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class ProgressBarHelper extends AppHelper
{
    var $name = 'ProgressBar';
    var $helpers = array ('Html', 'Js');

    function generate($percentage) {
        $html  = '<div class="progress_bar">';
        $html .= '<div style="width: '.($percentage*100).'%"></div>';
        $html .= '</div>';
        return $html;
    }

}