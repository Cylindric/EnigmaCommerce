<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class ImageHelper extends AppHelper {

    var $name = 'Image';
    var $settings = array();
    var $helpers = array ('Html', 'Link');

    function itemThumb($item, array $settings = array()) {

        $settings = array_merge(array(
            'alt' => $item['Item']['name'],
            'action' => 'view',
            'link' => true,
            'class' => 'thumb',
            'escape' => false,
        ), $settings);
        
        if (empty($item['Picture']['filename'])) {
            $src = 'products' . DS . 'blank.png';
        } else {
            $src = 'products' . DS . $item['Picture']['filename'];
        }
        
        $imgSettings = $settings;
        unset($imgSettings['action']);
        unset($imgSettings['link']);
        $img = $this->Html->image($src, $imgSettings);

        $linkSettings = $settings;
        $linkSettings['content'] = $img;
        $linkSettings['name'] = false;
        $out = $this->Link->link('Item', $item['Item'], $linkSettings);
        
        return $out;
    }
    
}
