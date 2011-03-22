<div class="content-1">
    <?php echo $this->Session->flash(); ?>

    <div class="panel-1">
        <?php echo $this->element('items/edit_main', $this->data); ?>
    </div>

    <div class="panel-2">
        <?php echo $this->element('items/category_list', array('categories' => $this->data)); ?>
    </div>

    <div class="panel-3">
        <?php echo $this->element('items/variation_list', array('variations' => $this->data)); ?>
    </div>

</div>
<div class="content-2">
    <?php echo $this->element('items/menu'); ?>
</div>