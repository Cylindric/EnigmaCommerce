<?php echo $this->element('categories/menu'); ?>
<div class="body-wide" id="body">

    <?php echo $this->element('categories/list', array('categories' => $subCategories)); ?>
    <?php echo $this->element('items/list', array('items' => $relatedItems)); ?>

</div>