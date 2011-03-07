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
            'sef' => true,
            'action' => null,
            'id' => 'id',
            'slug' => 'slug',
            'name' => 'name',
            'content' => null,
        ), $settings);
        extract($this->settings);

        if ($name === false) {
            $linkText = $content;
        } else {
            $linkText = $data[$name];
        }
        
        $linkController = Inflector::tableize($model);
        
        if (is_array($action)) {
            $linkAction = $action['action'];
        } else {
            $linkAction = $action;
        }

        if (($sef) && (array_key_exists($slug, $data))) {
            $Linkid = $data[$slug];        
        } else {
            $Linkid = $data[$id];
        }
        
        unset($settings['content']);
        unset($settings['link']);
        unset($settings['id']);
        unset($settings['name']);
        unset($settings['action']);
        $out = $this->Html->link($linkText, array('controller' => $linkController, 'action' => $linkAction, $Linkid), $settings);

        return $out;
    }
    
}