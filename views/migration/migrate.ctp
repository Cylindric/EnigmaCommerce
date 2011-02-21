<div class="install">
	<ul class="migrate">
		<?php foreach($msgs as $msg): ?>
		<li><?php echo $msg['text'];?></li>
		<?php endforeach; ?>
	</ul>
</div>
<a name="end" />
<?php
	if($section !== 'done') {
		echo $this->Html->meta(null, null, array('http-equiv' => 'refresh', 'content' => '1;'.$this->Html->url($next_page)), false);
	}
?>