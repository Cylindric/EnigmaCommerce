<div class="units index">
	<h2><?php __('Units');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('pluralname');?></th>
			<th><?php echo $this->Paginator->sort('unit');?></th>
			<th><?php echo $this->Paginator->sort('parent_id');?></th>
			<th><?php echo $this->Paginator->sort('scalefactor');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($units as $unit):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $unit['Unit']['id']; ?>&nbsp;</td>
		<td><?php echo $unit['Unit']['name']; ?>&nbsp;</td>
		<td><?php echo $unit['Unit']['pluralname']; ?>&nbsp;</td>
		<td><?php echo $unit['Unit']['unit']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($unit['Parentunit']['name'], array('controller' => 'units', 'action' => 'view', $unit['Parentunit']['id'])); ?>
		</td>
		<td><?php echo $unit['Unit']['scalefactor']; ?>&nbsp;</td>
		<td><?php echo $unit['Unit']['created']; ?>&nbsp;</td>
		<td><?php echo $unit['Unit']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $unit['Unit']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $unit['Unit']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $unit['Unit']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $unit['Unit']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Unit', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Units', true), array('controller' => 'units', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parentunit', true), array('controller' => 'units', 'action' => 'add')); ?> </li>
	</ul>
</div>