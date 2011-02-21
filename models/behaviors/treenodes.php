<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class TreenodesBehavior extends ModelBehavior {
    
    public function setup($model, $settings = array()) {
        $default = array(
            'name' => 'name',
            'id' => 'id',
            'parent_id' => 'parent_id'
        );
        
        if (!isset($this->settings[$model->alias])) {
            $configured = Configure::read('Treenodes');
            if (!empty($configured)) {
                foreach($default as $key => $value) {
                    if (isset($configured[$key])) {
                        $default[$key] = $configured[$key];
                    }
                }
            }
            $this->settings[$model->alias] = $default;
        }

        $this->settings[$model->alias] = array_merge($this->settings[$model->alias], $settings);
    }

    function treeNodes(&$model, $rootId = null, $settings = array()) {
        $alias = $model->alias;
        $settings = array_merge($this->settings[$alias], $settings);
        
        $parentField = $alias.'.'.$settings['parent_id'];
        
        if (is_null($rootId)) {
            $recursive = true;
            $rootId = 0;
        } else {
            $recursive = false;
        }

        $nodes = $model->find('all', array(
            'conditions' => array(
                $parentField => $rootId
            ),
            'recursive' => 0
        ));

        $jsNodes = array();
        foreach ($nodes as $node) {
            $children = $model->find('count', array(
                'conditions' => array(
                    $parentField => $node[$alias][$settings['id']]
                )
            ));
            
            $jsNode = array();
            $jsNode['id'] = $node[$alias][$settings['id']];
            $jsNode['text'] = $node[$alias][$settings['name']];
            //$jsNode['cls'] = $alias;
            $jsNode['iconCls'] = $alias;
            $jsNode['editAction'] = '';//$this->params->webroot.'admin/units/edit/'.$jsNode[$settings['id']];
            if ($children == 0) {
                $jsNode['leaf'] = true;
            } else {
                $jsNode['leaf'] = false;
                if ($recursive) {
                    $jsNode['children'] = $this->treeNodes($model, $jsNode['id']);                    
                }
            }
            $jsNodes[] = $jsNode;
        }

        return $jsNodes;
    }
    
}