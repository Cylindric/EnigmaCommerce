<div class="details index">
	<h2><?php __('Details');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('item_id');?></th>
			<th><?php echo $this->Paginator->sort('unit_id');?></th>
			<th><?php echo $this->Paginator->sort('size');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('tag');?></th>
			<th><?php echo $this->Paginator->sort('price');?></th>
			<th><?php echo $this->Paginator->sort('rrp');?></th>
			<th><?php echo $this->Paginator->sort('stockcode');?></th>
			<th><?php echo $this->Paginator->sort('status');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th><?php echo $this->Paginator->sort('legacy_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($details as $detail):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $detail['Detail']['id']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['item_id']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['unit_id']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['size']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['name']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['tag']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['price']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['rrp']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['stockcode']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['status']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['created']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['modified']; ?>&nbsp;</td>
		<td><?php echo $detail['Detail']['legacy_id']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $detail['Detail']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $detail['Detail']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $detail['Detail']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $detail['Detail']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Detail', true), array('action' => 'add')); ?></li>
	</ul>
</div>