<div class="units form">
<?php echo $this->Form->create('Unit');?>
	<fieldset>
 		<legend><?php __('Edit Unit'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('pluralname');
		echo $this->Form->input('unit');
		echo $this->Form->input('parent_id');
		echo $this->Form->input('scalefactor');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Unit.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Unit.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Units', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Units', true), array('controller' => 'units', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parentunit', true), array('controller' => 'units', 'action' => 'add')); ?> </li>
	</ul>
</div>