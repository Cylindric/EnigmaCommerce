<?php echo $this->element('categories/menu'); ?>
<div class="body-wide" id="body">

    <h2>Sub-Categories</h3>
    <?php echo $this->element('categories/list', array('categories' => $categories)); ?>

</div>