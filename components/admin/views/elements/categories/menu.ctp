<div id="left-navigation">
<?php
    $nodes = $this->requestAction('/admin/categories/menu_nodes');

    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'element' => 'categorymenu_item',
            'action' => array('controller'=>'categories', 'action'=>'edit')
        )
    );
?>
</div>