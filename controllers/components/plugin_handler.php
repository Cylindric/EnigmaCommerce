<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Manages the automatic loading and configuration of plugins.
 * @package core
 * @subpackage components
 */
class PluginHandlerComponent extends Component {

    private $controller;

    public function initialize($controller) {
        $this->controller = $controller;

        // Load all plugin configurations
        foreach (App::objects('plugin') as $plugin) {
            $name = Inflector::classify("{$plugin}_config");
            $file = Inflector::underscore($plugin) . DS . 'config' . DS . 'config.php';
            App::import('Plugin', $name, array('file' => $file));
        }

        $this->executeCallback('startup');
    }

    private function executeCallback($method) {
        foreach (App::objects('plugin') as $plugin) {
            $loaderFile = $plugin . '_auto_loader';
            $loaderClass = Inflector::classify($loaderFile);
            $loaderInstance = null;

            if (!ClassRegistry::isKeySet($loaderClass)) {
                $classFile = Inflector::underscore($plugin) . DS . $loaderFile . '.php';
                App::import('Plugin', $loaderClass, $classFile);
                if (class_exists($loaderClass)) {
                    ClassRegistry::addObject($loaderClass, new $loaderClass());
                }
            } else {
                $loaderInstance = & ClassRegistry::getObject($loaderClass);
            }
            if (!empty($loaderInstance) && in_array($method, get_class_methods($loaderClass))) {
                $loaderInstance->{$method}($this->controller);
            }
        }
    }

    public function startup($controller) {
        
    }

    public function shutdown() {
        
    }

    public function beforeRender($controller) {
        $this->controller = $controller;
        $this->executeCallback('beforeRender');
    }

}