<div id="left-navigation">
<?php
    $nodes = $this->requestAction('/categories/menu_nodes');
   
    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'element' => 'categorymenu_item',
            'action' => array('controller'=>'categories', 'action'=>'view')
        )
    );
?>
</div>