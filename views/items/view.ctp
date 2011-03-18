<div class="content-1">

    <?php echo $this->Image->itemThumb($item, array('link' => false, 'blank' => false)); ?>
    <h1><?php echo $item['Item']['name']; ?></h1>
    <p><?php echo $item['Item']['description']; ?></p>
    <?php if (count($item['Variation'])>0): ?>
        <div class="x-list">
            <?php foreach ($item['Variation'] as $variation): ?>
            <div class="x-item variation">
                <?php echo $variation['name']; ?><br />
                <?php echo $this->Format->currency($variation['price']); ?><br />
                <?php echo $this->Html->link(__('Buy'), array('controller' => 'basket', 'action' => 'add', $item['Item']['slug'], $variation['slug'])); ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<div class="content-2">
    <?php echo $this->element('categories/menu'); ?>
</div>
    