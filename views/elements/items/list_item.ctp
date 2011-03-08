<div class="x-list item">
<?php echo $this->Image->itemThumb($item); ?>
<h2><?php echo $this->Link->view('Item', $item); ?></h2>
<?php echo $this->Text->truncate($item['Item']['description'], 250, array('html'=>true)); ?>
</div>