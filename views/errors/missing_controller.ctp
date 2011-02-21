<h2><?php echo(__('Missing Controller')); ?></h2>
<p class="error">
	<strong><?php echo(__('Error')); ?>: </strong>
	<?php echo sprintf(__('%s could not be found.'), '<em>' . $this->request->params['controller'] . '</em>');?>
</p>
