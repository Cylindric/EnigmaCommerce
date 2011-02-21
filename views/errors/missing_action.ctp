<h2><?php echo(__('Missing Action')); ?></h2>
<p class="error">
	<strong><?php echo(__('Error')); ?>: </strong>
	<?php echo sprintf('Action %s could not be found.', '<em>' . $this->request->params['controller'] . '/' . $this->request->params['action'] . '</em>');?>
</p>
