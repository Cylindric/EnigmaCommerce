<?php
    $nodes = $this->requestAction('/admin/categories/menu_nodes');

    $extraNodes = array(
        array('url' => '/admin/categories/add', 'title' => __('Add new %s', __('category'))),
    );
    
    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'element' => 'categorymenu_item',
            'div' => 'menutree',
            'action' => array('controller'=>'categories', 'action'=>'edit'),
            'extraNodes' => $extraNodes,
        )
    );
