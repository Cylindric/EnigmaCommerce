<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage config
 */
Router::connect('/', array('controller' => 'categories', 'action' => 'index'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));

Router::connect(
                '/categories/view/:tag', array('controller' => 'categories', 'action' => 'view'), array('pass' => array('tag'), 'tag' => '[a-zA-Z]+[\w]*')
);

Router::parseExtensions('js');