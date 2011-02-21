	<div id="cat_menu">
	<?php
	echo $this->TreeMenu->generate(
		$this->requestAction('categories/menu'),
		array(
			'model'=>'Category',
			'controller'=>'categories',
			'action'=>'view'
		)
	);
	?>
	</div>
	<div id="body" class="view">
	<div id="bodycontent">


<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

<h2><?php echo $category['Category']['name']; ?></h2>
<?php echo $category['Category']['description']; ?>

<div class="related">
	<h3><?php __('Related Items');?></h3>
	<?php if (!empty($category['RelatedItems'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Name'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($category['RelatedItems'] as $item):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Html->link($item['Item']['name'], array('controller' => 'items', 'action' => 'view', $item['Item']['tag'])); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>

	</div>
	</div>
	<div class="clear"></div>
