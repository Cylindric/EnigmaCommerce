<?php if (!$isAjax): ?>
	<div id="cat_menu">
	<?php
	echo $this->TreeMenu->generate(
		$this->requestAction('categories/menu'),
		array(
			'model'=>'Category',
			'controller'=>'categories',
			'action'=>'view',
			'ajaxtarget'=>'bodycontent'
		)
	);
	?>
	</div>
	<div id="body" class="view">
	<div id="bodycontent">
<?php endif; ?>


<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

<?php
	$this->Paginator->options(array(
		'update' => '#bodycontent',
		'before' => $this->Js->get('#bodycontent')->effect('fadeOut'),
		'complete' => $this->Js->get('#bodycontent')->effect('fadeIn')
	));
?>

	<div id="ajax-loading-message">Loading</div>

	<table class="list">
		<tr>
			<th><?php echo $this->Paginator->sort(__('Name', true), 'name'); ?></th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<?php	foreach ($categories as $category): ?>
		<tr>
			<td>
				<?php
					echo $this->Html->image('icons/category.png', array('alt' => __('Edit', true)));
					echo $this->Js->link(
					$category['Category']['name'],
					array('controller' => 'categories', 'action' => 'view', $category['Category']['tag']),
					array('update' => '#bodycontent')
				);
				?>
			</td>
			<td class="actions">
			<?php echo $this->Html->image('icons/delete.png', array('alt' => __('Delete', true), 'url' => array('controller' => 'categories', 'action' => 'delete', $category['Category']['id']))); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php
	if($this->Paginator->hasPage(2)) {
		echo $this->Paginator->first($this->Html->image('icons/resultset_first.png', array('alt' => __('First', true))), array('escape' => false));
		echo $this->Paginator->prev($this->Html->image('icons/resultset_previous.png', array('alt' => __('Previous', true))), array('escape' => false));
		echo $this->Paginator->numbers();
		echo $this->Paginator->next($this->Html->image('icons/resultset_next.png', array('alt' => __('Next', true))), array('escape' => false));
		echo $this->Paginator->last($this->Html->image('icons/resultset_last.png', array('alt' => __('Last', true))), array('escape' => false));
		echo $this->Paginator->counter();
	}
	?>

<?php if ($isAjax): ?>
	<?php echo $this->Js->writeBuffer(); ?>

<?php else: ?>
	</div>
	</div>
	<div class="clear"></div>
<?php endif; ?>
