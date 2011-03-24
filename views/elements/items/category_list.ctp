<div class="x-list items">
    <?php foreach ($items as $item): ?>
        <div class="x-listitem item">
            <div class="thumb">
                <?php echo $this->Image->itemThumb($item); ?>
            </div>
            <div>
                <h2><?php echo $this->Link->view('Item', $item); ?></h2>
                <?php echo $this->Text->truncate($item['Item']['description'], 250, array('html' => true)); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>