<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class LinkHelper extends AppHelper {

    var $name = 'Link';
    var $settings = array();
    var $helpers = array ('Html');

    function view($model, $data, array $settings = array()) {
        $settings = array_merge(array('action' => 'view'), $settings);
        return $this->link($model, $data, $settings);
    }
    
    function link($model, $data, array $settings = array()) {

        $this->settings = array_merge(array(
            'sef' => false,
            'action' => null,
            'id' => 'id',
            'slug' => 'slug',
            'name' => 'name'
        ), $settings);
        extract($this->settings);

        $linkText = $data[$name];
        
        $linkController = Inflector::tableize($model);
        $linkAction = $action['action'];
        if (($sef) && (array_key_exists($slug, $data))) {
            $Linkid = $data[$slug];        
        } else {
            $Linkid = $data[$id];
        }
        $out = $this->Html->link($linkText, array('controller' => $linkController, 'action' => $linkAction, $Linkid));

        return $out;
    }
    
}
