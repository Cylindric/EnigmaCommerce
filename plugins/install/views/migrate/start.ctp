<div id="body" class="view">
    <div id="bodycontent">

    <ul class="migrate">
        <?php foreach($messages as $section => $message): ?>
        <li><?php echo $section.': '.$message;?></li>
        <?php endforeach; ?>
    </ul>

<?php
if($status !== 'finished') {
    echo $this->Html->meta(null, null, array('http-equiv' => 'refresh', 'content' => '0;'), false);
}
?>

    </div>
</div>
<div class="clear"></div>
