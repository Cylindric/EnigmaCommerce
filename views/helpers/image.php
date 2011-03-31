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
class ImageHelper extends AppHelper {

    var $name = 'Image';
    var $settings = array();
    var $helpers = array('Html', 'Link');

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
        // pull out the primary picture
        $picture = null;
        if (array_key_exists('ItemPicture', $item)) {
            $itemPics = $item['ItemPicture'];
        } else {
            $itemPics = $item['Item']['ItemPicture'];
        }

        foreach ($itemPics as $itemPic) {
            $picture = $itemPic['Picture'];
            if ($itemPic['is_primary'] == true) {
                break;
            }
        }

        if (!$blank && empty($picture)) {
            $src = 'products' . DS . 'blank.png';
        } else {
            $src = 'products' . DS . $picture['filename'];
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
