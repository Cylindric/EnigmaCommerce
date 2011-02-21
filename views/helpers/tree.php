<?php
class TreeHelper extends AppHelper {

    var $name = 'Tree';
    var $settings = array();
    var $helpers = array ('Html');

    function generate ($data, array $settings = array()) {

        $this->settings = array_merge(array(
            'model' => null,
            'title' => 'name',
            'link' => true,
            'div' => 'tree',
            'action' => null
        ), $settings);
        extract($this->settings);
        
        $out = '<div id="'.$div.'">';
        $out .= $this->drawNodes($data);
        $out .= '</div>';

        return $out;
    }
    
    private function drawNodes($data) {
        extract($this->settings);
        
        $out = '<ul>';
        foreach($data as $node) {
            $out .= '<li>';
            
            $nodeaction = $action;
            $nodeaction[] = $node[$model]['id'];
            if ($link) {
                $out .= $this->Html->link($node[$model][$title], $nodeaction);
            } else {
                $out .= $node[$model][$title].'<br/>';
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
