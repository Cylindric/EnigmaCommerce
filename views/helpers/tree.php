<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage helpers
 */
class TreeHelper extends AppHelper {

    var $name = 'Tree';
    var $settings = array();
    var $helpers = array('Html', 'Link');

    function generate($data, array $settings = array()) {

        $this->settings = array_merge(array(
            'model' => null,
            'title' => 'name',
            'link' => true,
            'div' => 'tree',
            'showRoot' => true,
            'action' => null,
            'extraNodes' => array(),
                ), $settings);
        extract($this->settings);

        if ($showRoot == false) {
            $data = $data[0]['children'];
        }

        $out = '<div class="' . $div . '">';
        $out .= $this->drawNodes($data);
        $out .= '</div>';

        return $out;
    }

    private function drawNodes($data) {
        extract($this->settings);

        $out = '<ul>';
        foreach ($this->settings['extraNodes'] as $extraNode) {
            $out .= '<li>';
            $out .= $this->Html->link($extraNode['title'], $extraNode['url']);
            $out .= '</li>';
        }
        $this->settings['extraNodes'] = array();

        foreach ($data as $node) {
            $out .= '<li>';

            $nodeaction = $action;
            $nodeaction[] = $node[$model]['id'];
            if ($link) {
                $out .= $this->Link->link($model, $node, array(
                            'action' => $nodeaction,
                            'name' => $title
                        ));
            } else {
                $out .= $node[$model][$title] . '<br/>';
            }

            if (count($node['children']) > 0) {
                $out .= $this->drawNodes($node['children']);
            }

            $out .= '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

}
