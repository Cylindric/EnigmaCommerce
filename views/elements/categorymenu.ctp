<div id="left_menu">
<?php
    $nodes = $menu_categories[0]['children'];
    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'element' => 'categorymenu_item',
            'action' => array('controller'=>'categories', 'action'=>'view')
        )
    );
?>
</div>