<div id="left_menu">
<?php
    $nodes = $this->requestAction('/categories/menu_nodes');
    echo $this->Tree->generate(
        $nodes, array(
            'model' => 'Category',
            'showRoot' => false,
            'action' => array('controller'=>'categories', 'action'=>'edit')
        )
    );
?>
</div>
<div id="body" class="view">
    <div id="bodycontent">

        <p>(A Categories dashboard should go here, or maybe just a list of categories)</p>

    </div>
</div>
