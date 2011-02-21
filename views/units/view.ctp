<div class="units view">
<h2><?php  __('Unit');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Pluralname'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['pluralname']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Unit'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['unit']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parentunit'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($unit['Parentunit']['name'], array('controller' => 'units', 'action' => 'view', $unit['Parentunit']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Scalefactor'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['scalefactor']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unit['Unit']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Unit', true), array('action' => 'edit', $unit['Unit']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Unit', true), array('action' => 'delete', $unit['Unit']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $unit['Unit']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Units', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Unit', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Units', true), array('controller' => 'units', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parentunit', true), array('controller' => 'units', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Units');?></h3>
	<?php if (!empty($unit['Childunit'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Pluralname'); ?></th>
		<th><?php __('Unit'); ?></th>
		<th><?php __('Parent Id'); ?></th>
		<th><?php __('Scalefactor'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($unit['Childunit'] as $childunit):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childunit['id'];?></td>
			<td><?php echo $childunit['name'];?></td>
			<td><?php echo $childunit['pluralname'];?></td>
			<td><?php echo $childunit['unit'];?></td>
			<td><?php echo $childunit['parent_id'];?></td>
			<td><?php echo $childunit['scalefactor'];?></td>
			<td><?php echo $childunit['created'];?></td>
			<td><?php echo $childunit['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'units', 'action' => 'view', $childunit['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'units', 'action' => 'edit', $childunit['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'units', 'action' => 'delete', $childunit['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childunit['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Childunit', true), array('controller' => 'units', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
