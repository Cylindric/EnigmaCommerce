<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage helpers
 */
class LinkHelper extends AppHelper {

    var $name = 'Link';
    var $helpers = array('Html');
    private $settings = array(
        'sef' => false,
    );

    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);
        if (empty($settings['sef'])) {
            $this->settings['sef'] = Configure::read('sef');
        } else {
            $this->settings['sef'] = $settings['sef'];
        }
    }

    function view($model, $data, array $settings = array()) {
        $settings = array_merge(array('action' => 'view'), $settings);
        return $this->link($model, $data, $settings);
    }

    function edit($model, $data, array $settings = array()) {
        $settings = array_merge(array('action' => 'edit'), $settings);
        return $this->link($model, $data, $settings);
    }

    function link($model, $data, array $settings = array()) {
        $settings = array_merge(array(
            'sef' => $this->settings['sef'],
            'action' => null,
            'id' => 'id',
            'slug' => 'slug',
            'name' => 'name',
            'content' => null,
                ), $settings);
        extract($settings);

        if ($name === false) {
            $linkText = $content;
        } else {
            $linkText = $data[$model][$name];
        }


        if (is_array($action)) {
            $linkController = $action['controller'];
            $linkAction = $action['action'];
        } else {
            $linkController = Inflector::tableize($model);
            $linkAction = $action;
        }

        if (($sef) && (array_key_exists($slug, $data[$model]))) {
            $Linkid = $data[$model][$slug];
        } else {
            $Linkid = $data[$model][$id];
        }

        unset($settings['sef']);
        unset($settings['content']);
        unset($settings['link']);
        unset($settings['id']);
        unset($settings['name']);
        unset($settings['action']);
        $out = $this->Html->link($linkText, array('controller' => $linkController, 'action' => $linkAction, $Linkid), $settings);

        return $out;
    }

}