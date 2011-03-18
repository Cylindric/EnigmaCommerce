<?php
    $nodes = $this->requestAction('/categories/menu_nodes');

    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'div' => 'menutree',
            'action' => array('controller'=>'categories', 'action'=>'view'),
        )
    );
