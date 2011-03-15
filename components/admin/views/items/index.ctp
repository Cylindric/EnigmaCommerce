<div class="content-1">
    <?php echo $this->Session->flash(); ?>                    

    <h1><?php echo __('Items'); ?></h1>
    <?php 
        foreach ($items as $item) {
            echo $this->element('items/compact_list', array('item' => $item));
        }
    ?>
</div>

<div class="content-2"> 
    <?php echo $this->element('items/menu'); ?>
</div>