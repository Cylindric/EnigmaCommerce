<div name="x-list item">
<?php
//var_dump($item);
    echo $this->Image->itemThumb($item);
    echo $this->Link->view('Item', $item['Item']);
    echo $this->Text->truncate($item['Item']['description'], 250, array('html'=>true));
?>
</div>