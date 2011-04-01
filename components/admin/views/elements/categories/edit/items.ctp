<?php foreach ($items as $item): ?>
    <div class="compact_item">
        <span class="item name"><?php echo $this->Link->edit('Item', $item); ?></span>
    </div>
<?php endforeach; ?>
