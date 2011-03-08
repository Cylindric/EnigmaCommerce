<?php echo $this->element('categories/menu'); ?>
<div class="body-wide" id="body">

    <?php echo $this->element('categories/list', array('categories' => $categories)); ?>

</div>