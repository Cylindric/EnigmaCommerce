<?php echo $this->element('categories/menu'); ?>
<div class="body-wide" id="body">

    <h2>Categories</h2>
    <?php echo $this->element('categories/list', array('categories' => $subCategories)); ?>
    
    <h2>Items</h2>
    <?php echo $this->element('items/list', array('items' => $relatedItems)); ?>

</div>