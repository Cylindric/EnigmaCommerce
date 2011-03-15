<?php
    $nodes = $this->requestAction('/admin/categories/menu_nodes');

    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'div' => 'menutree',
            'action' => array('controller'=>'items', 'action'=>'index'),
        )
    );
