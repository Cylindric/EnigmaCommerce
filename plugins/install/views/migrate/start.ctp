<div class="install">
    <ul class="migrate">
        <?php foreach($messages as $section => $message): ?>
        <li><?php echo $section.': '.$message;?></li>
        <?php endforeach; ?>
    </ul>
</div>
<a name="end" />
<?php
if($status !== 'finished') {
    echo $this->Html->meta(null, null, array('http-equiv' => 'refresh', 'content' => '1;'), false);
}