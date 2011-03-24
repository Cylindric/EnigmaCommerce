<div class="x-list categories">
    <?php foreach ($categories as $category): ?>
        <div class="x-listitem category">
            <div class="thumb">
                <?php echo $this->Html->image('icons/category.png', array('alt' => __('Edit'))); ?>
            </div>
            <div>
                <?php echo $this->Link->view('Category', $category); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
