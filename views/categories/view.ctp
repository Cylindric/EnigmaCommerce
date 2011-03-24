<div class="content-1"> 

    <h2>Categories</h2>
    <?php echo $this->element('categories/list', array('categories' => $subCategories)); ?>
    
    <h2>Items</h2>
    <?php echo $this->element('items/category_list', array('items' => $relatedItems)); ?>

</div> 
<div class="content-2"> 
    <?php echo $this->element('categories/menu'); ?>
</div> 
