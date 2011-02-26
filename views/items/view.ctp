<?php echo $this->element('categorymenu'); ?>
<div id="body" class="view">
    <div id="bodycontent">

    <?php echo $this->Session->flash(); ?>

        <h1><?php echo $item['Item']['name']; ?></h1>
        <p><?php echo $item['Item']['description']; ?></p>
    <?php if (count($item['Variation'])>0): ?>
        <div class="x-list">
            <?php foreach ($item['Variation'] as $variation): ?>
            <div class="x-item variation">
                <?php echo $variation['name']; ?><br />
                <?php echo $variation['price']; ?><br />
                <?php echo $this->Html->link(__('Buy'), array('controller' => 'basket', 'action' => 'add', $item['Item']['slug'], $variation['slug'])); ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    </div>
</div>
<div class="clear"></div>
