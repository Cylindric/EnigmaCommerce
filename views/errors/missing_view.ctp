<h2><?php echo(__('Missing View')); ?></h2>
<p class="error">
	<strong><?php echo(__('Error')); ?>: </strong>
	<?php echo sprintf('View for %s could not be found.', '<em>' . $this->request->params['action'] . '</em>');?>
</p>
