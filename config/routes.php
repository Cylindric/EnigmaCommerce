<?php
Router::connect('/', array('controller' => 'categories', 'action' => 'index'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

Router::connect('/admin', array('controller' => 'categories', 'action' => 'index', 'prefix' => 'admin'));
Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));

Router::connect(
    '/categories/view/:tag',
    array('controller' => 'categories', 'action' => 'view'),
    array('pass' => array('tag'), 'tag' => '[a-zA-Z]+[\w]*')
);

Router::parseExtensions('js');