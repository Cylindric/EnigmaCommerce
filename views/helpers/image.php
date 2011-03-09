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

        $this->settings = array_merge(array(
            'alt' => $item['Item']['name'],
            'action' => 'view',
            'link' => true,
            'class' => 'thumb',
            'escape' => false,
            'blank' => true,
            'width' => 150,
        ), $settings);
        extract($this->settings);
        
        $src = '';
        if ($blank && empty($item['Picture']['filename'])) {
            $src = 'products' . DS . 'blank.png';
        } else {
            $src = 'products' . DS . $item['Picture']['filename'];
        }

        $out = '';
        if (!empty($src)) {
            $imgSettings = $this->settings;
            unset($imgSettings['action']);
            unset($imgSettings['link']);
            $out = $this->Html->image($src, $imgSettings);

            if ($link) {
                $linkSettings = $this->settings;
                $linkSettings['content'] = $out;
                $linkSettings['name'] = false;
                $out = $this->Link->link('Item', $item, $linkSettings);
            }
        }
        
        return $out;
    }
    
}
